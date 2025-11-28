<?php

namespace App\Services;

use CodeIgniter\Database\BaseBuilder;
use Config\Services;

class CacheService
{
    private $cache;
    private $performanceService;
    
    // Cache groups for organized invalidation
    private $cacheGroups = [
        'contracts' => ['kontrak_*', 'contract_*'],
        'customers' => ['customer_*', 'location_*'],
        'inventory' => ['inventory_*', 'unit_*'],
        'reports' => ['report_*', 'dashboard_*'],
        'users' => ['user_*', 'permission_*']
    ];
    
    public function __construct()
    {
        $this->cache = Services::cache();
        $this->performanceService = new PerformanceService();
    }
    
    /**
     * Enhanced caching with automatic key generation and grouping
     */
    public function remember($key, $callback, $ttl = 3600, $group = null)
    {
        $fullKey = $this->generateCacheKey($key, $group);
        
        $cached = $this->cache->get($fullKey);
        if ($cached !== null) {
            $this->performanceService->incrementCacheHits();
            return $cached;
        }
        
        $this->performanceService->incrementCacheMisses();
        
        // Execute callback and cache result
        $startTime = microtime(true);
        $data = $callback();
        $executionTime = microtime(true) - $startTime;
        
        // Log slow operations
        if ($executionTime > 1.0) {
            log_message('info', "Slow cache operation: {$fullKey} took {$executionTime}s");
        }
        
        // Store with metadata
        $cacheData = [
            'data' => $data,
            'cached_at' => time(),
            'execution_time' => $executionTime,
            'group' => $group
        ];
        
        $this->cache->save($fullKey, $cacheData, $ttl);
        $this->addToGroup($group, $fullKey);
        
        return $data;
    }
    
    /**
     * Query-specific caching with parameter awareness
     */
    public function rememberQuery($queryKey, BaseBuilder $builder, $ttl = 3600, $group = 'queries')
    {
        $sql = $builder->getCompiledSelect(false);
        $bindings = $builder->getBinds();
        
        // Create unique key based on SQL and parameters
        $fullKey = $this->generateQueryCacheKey($queryKey, $sql, $bindings);
        
        return $this->remember($fullKey, function() use ($builder) {
            return $builder->get()->getResultArray();
        }, $ttl, $group);
    }
    
    /**
     * Paginated data caching with cursor support
     */
    public function rememberPaginated($baseKey, $page, $limit, $cursor, $callback, $ttl = 1800)
    {
        $paginationKey = sprintf('%s_page_%d_limit_%d_cursor_%s', 
            $baseKey, $page, $limit, md5($cursor ?? ''));
        
        return $this->remember($paginationKey, $callback, $ttl, 'pagination');
    }
    
    /**
     * Smart cache warming for frequently accessed data
     */
    public function warmCache($patterns = [])
    {
        foreach ($patterns as $pattern) {
            $this->warmCachePattern($pattern);
        }
    }
    
    private function warmCachePattern($pattern)
    {
        switch ($pattern) {
            case 'dashboard_stats':
                $this->warmDashboardStats();
                break;
            case 'user_permissions':
                $this->warmUserPermissions();
                break;
            case 'lookup_data':
                $this->warmLookupData();
                break;
        }
    }
    
    private function warmDashboardStats()
    {
        $db = \Config\Database::connect();
        
        // Pre-cache common dashboard queries
        $commonPeriods = [7, 30, 90];
        foreach ($commonPeriods as $days) {
            $this->remember("dashboard_contracts_{$days}d", function() use ($db, $days) {
                return $db->query("
                    SELECT 
                        DATE(dibuat_pada) as date,
                        COUNT(*) as count,
                        SUM(nilai_total) as total_value
                    FROM kontrak 
                    WHERE dibuat_pada >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                    GROUP BY DATE(dibuat_pada)
                    ORDER BY date DESC
                ", [$days])->getResultArray();
            }, 3600, 'dashboard');
        }
    }
    
    private function warmUserPermissions()
    {
        $db = \Config\Database::connect();
        
        $users = $db->table('users')
            ->where('is_active', 1)
            ->where('last_login >', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->select('id, username')
            ->limit(50) // Limit to prevent too many operations
            ->get()
            ->getResultArray();
        
        foreach ($users as $user) {
            $this->remember("user_permissions_{$user['id']}", function() use ($user) {
                return $this->loadUserPermissions($user['id']);
            }, 7200, 'users');
        }
    }
    
    private function warmLookupData()
    {
        $lookupTables = [
            'customers' => 'customers',
            'customer_locations' => 'customer_locations'
        ];
        
        $db = \Config\Database::connect();
        
        foreach ($lookupTables as $key => $table) {
            $this->remember("lookup_{$key}", function() use ($db, $table, $key) {
                $builder = $db->table($table);
                
                // Use correct column names based on table structure
                if ($key === 'customers') {
                    $builder->where('is_active', 1);
                } else if ($key === 'customer_locations') {
                    // Check if customer_locations has active column
                    $fields = $db->getFieldNames($table);
                    if (in_array('active', $fields)) {
                        $builder->where('active', 1);
                    }
                }
                
                return $builder->orderBy('id', 'ASC')->get()->getResultArray();
            }, 7200, 'lookups');
        }
    }
    
    /**
     * Intelligent cache invalidation
     */
    public function invalidateGroup($group)
    {
        if (!isset($this->cacheGroups[$group])) {
            return false;
        }
        
        $patterns = $this->cacheGroups[$group];
        $invalidated = 0;
        
        foreach ($patterns as $pattern) {
            $keys = $this->getKesByPattern($pattern);
            foreach ($keys as $key) {
                if ($this->cache->delete($key)) {
                    $invalidated++;
                }
            }
        }
        
        log_message('info', "Cache invalidated: {$group} ({$invalidated} keys)");
        return $invalidated;
    }
    
    /**
     * Cache statistics and monitoring
     */
    public function getStats()
    {
        $stats = $this->performanceService->getCacheStats();
        
        return [
            'hit_ratio' => $stats['hits'] / max(($stats['hits'] + $stats['misses']), 1),
            'total_requests' => $stats['hits'] + $stats['misses'],
            'memory_usage' => $this->getCacheMemoryUsage(),
            'key_count' => $this->getCacheKeyCount(),
            'groups' => $this->getGroupStats()
        ];
    }
    
    /**
     * Background cache refresh
     */
    public function refreshInBackground($key, $callback, $ttl = 3600, $group = null)
    {
        $fullKey = $this->generateCacheKey($key, $group);
        $current = $this->cache->get($fullKey);
        
        // If cache exists and not expired, return immediately
        if ($current !== null && $current['cached_at'] > (time() - $ttl + 300)) { // 5min buffer
            
            // Schedule background refresh if cache is getting old
            if ($current['cached_at'] < (time() - ($ttl * 0.8))) {
                $this->scheduleRefresh($fullKey, $callback, $ttl, $group);
            }
            
            return $current['data'];
        }
        
        // Cache expired or doesn't exist, refresh now
        return $this->remember($key, $callback, $ttl, $group);
    }
    
    private function scheduleRefresh($key, $callback, $ttl, $group)
    {
        // Simple file-based job queue
        $job = [
            'type' => 'cache_refresh',
            'key' => $key,
            'ttl' => $ttl,
            'group' => $group,
            'scheduled_at' => time()
        ];
        
        $jobFile = WRITEPATH . 'cache/refresh_' . md5($key) . '.json';
        file_put_contents($jobFile, json_encode($job));
    }
    
    /**
     * Process scheduled cache refreshes
     */
    public function processScheduledRefreshes()
    {
        $jobFiles = glob(WRITEPATH . 'cache/refresh_*.json');
        $processed = 0;
        
        foreach (array_slice($jobFiles, 0, 5) as $jobFile) { // Process max 5 at once
            $job = json_decode(file_get_contents($jobFile), true);
            
            if ($job && $job['scheduled_at'] < (time() - 60)) { // Process jobs older than 1min
                try {
                    // Execute refresh based on job data
                    $this->executeRefreshJob($job);
                    unlink($jobFile);
                    $processed++;
                } catch (\Exception $e) {
                    log_message('error', "Cache refresh failed: " . $e->getMessage());
                }
            }
        }
        
        return $processed;
    }
    
    // Helper methods
    private function generateCacheKey($key, $group = null)
    {
        $prefix = 'optima_v2_';
        if ($group) {
            $prefix .= $group . '_';
        }
        return $prefix . md5($key);
    }
    
    private function generateQueryCacheKey($baseKey, $sql, $bindings)
    {
        $queryHash = md5($sql . serialize($bindings));
        return $this->generateCacheKey($baseKey . '_' . $queryHash, 'queries');
    }
    
    private function addToGroup($group, $key)
    {
        if (!$group) return;
        
        $groupKey = "cache_group_{$group}";
        $groupKeys = $this->cache->get($groupKey) ?? [];
        $groupKeys[] = $key;
        $this->cache->save($groupKey, array_unique($groupKeys), 86400);
    }
    
    private function getKesByPattern($pattern)
    {
        // This would need Redis or Memcached for pattern matching
        // For file cache, we'll use a simpler group-based approach
        return [];
    }
    
    private function getCacheMemoryUsage()
    {
        // Implementation depends on cache driver
        return 0;
    }
    
    private function getCacheKeyCount()
    {
        // Implementation depends on cache driver
        return 0;
    }
    
    private function getGroupStats()
    {
        $stats = [];
        foreach (array_keys($this->cacheGroups) as $group) {
            $groupKey = "cache_group_{$group}";
            $keys = $this->cache->get($groupKey) ?? [];
            $stats[$group] = count($keys);
        }
        return $stats;
    }
    
    private function loadUserPermissions($userId)
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT p.permission_name 
            FROM user_permissions up
            JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ? AND up.active = 1
        ", [$userId])->getResultArray();
    }
    
    private function executeRefreshJob($job)
    {
        // This would need to reconstruct the callback based on job data
        // For now, just invalidate the cache to force refresh on next access
        $this->cache->delete($job['key']);
    }
}