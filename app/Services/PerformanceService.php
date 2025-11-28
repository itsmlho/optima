<?php

namespace App\Services;

/**
 * Performance Service untuk monitoring dan optimization
 */
class PerformanceService
{
    protected $cache;
    protected $db;
    protected $slowQueryThreshold = 0.1; // 100ms
    protected $cacheStats = [
        'hits' => 0,
        'misses' => 0
    ];

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
        $this->db = \Config\Database::connect();
        
        // Load cache stats from persistent storage if available
        $this->loadCacheStats();
    }

    /**
     * Increment cache hits counter
     */
    public function incrementCacheHits()
    {
        $this->cacheStats['hits']++;
        $this->saveCacheStats();
    }

    /**
     * Increment cache misses counter
     */
    public function incrementCacheMisses()
    {
        $this->cacheStats['misses']++;
        $this->saveCacheStats();
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats()
    {
        return $this->cacheStats;
    }

    /**
     * Load cache stats from file
     */
    private function loadCacheStats()
    {
        $statsFile = WRITEPATH . 'cache/stats.json';
        if (file_exists($statsFile)) {
            $stats = json_decode(file_get_contents($statsFile), true);
            if ($stats && is_array($stats)) {
                $this->cacheStats = array_merge($this->cacheStats, $stats);
            }
        }
    }

    /**
     * Save cache stats to file
     */
    private function saveCacheStats()
    {
        $statsFile = WRITEPATH . 'cache/stats.json';
        $dir = dirname($statsFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($statsFile, json_encode($this->cacheStats));
    }

    /**
     * Generate comprehensive performance report
     */
    public function generatePerformanceReport()
    {
        return [
            'timestamp' => time(),
            'cache' => $this->getCacheStats(),
            'database' => $this->getDatabaseStats(),
            'memory' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true)
            ],
            'php' => [
                'version' => PHP_VERSION,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time')
            ]
        ];
    }

    /**
     * Get database performance statistics
     */
    private function getDatabaseStats()
    {
        try {
            $stats = [];
            
            // Get slow query count from today
            $logFile = WRITEPATH . 'logs/log-' . date('Y-m-d') . '.log';
            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                $slowQueryCount = substr_count($logContent, 'Slow queries detected');
                $stats['slow_queries_today'] = $slowQueryCount;
            } else {
                $stats['slow_queries_today'] = 0;
            }

            // Get database connection info
            $stats['connection_status'] = $this->db->connID ? 'connected' : 'disconnected';
            
            return $stats;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Monitor slow queries (simplified for CI4 compatibility)
     */
    public function logSlowQueries()
    {
        // For CI4, we'll track queries differently
        // This is a placeholder that logs when called
        $slowQueries = [];
        
        log_message('info', 'Query monitoring checkpoint', [
            'timestamp' => time(),
            'memory_usage' => memory_get_usage(true),
            'url' => current_url(),
            'user_id' => session('user_id')
        ]);

        return $slowQueries;
    }

    /**
     * Cache query dengan key yang smart
     */
    public function cacheQuery($key, $callback, $ttl = 300, $tags = [])
    {
        $cacheKey = $this->generateCacheKey($key, $tags);
        $data = $this->cache->get($cacheKey);

        if ($data === null) {
            $startTime = microtime(true);
            $data = $callback();
            $endTime = microtime(true);

            $this->cache->save($cacheKey, $data, $ttl);

            // Log cache miss untuk analysis
            log_message('debug', 'Cache miss', [
                'key' => $cacheKey,
                'execution_time' => round(($endTime - $startTime) * 1000, 2) . 'ms'
            ]);
        }

        return $data;
    }

    /**
     * Generate smart cache key
     */
    protected function generateCacheKey($base, $tags = [])
    {
        $context = [
            'user_id' => session('user_id'),
            'user_role' => session('user_role'),
            'timestamp' => date('Y-m-d-H') // Hourly cache refresh
        ];

        return 'perf_' . md5($base . serialize(array_merge($context, $tags)));
    }

    /**
     * Monitor memory usage
     */
    public function checkMemoryUsage()
    {
        $current = memory_get_usage(true);
        $peak = memory_get_peak_usage(true);
        
        $currentMB = round($current / 1024 / 1024, 2);
        $peakMB = round($peak / 1024 / 1024, 2);
        $limit = ini_get('memory_limit');

        if ($currentMB > 256) { // Warning threshold
            log_message('warning', 'High memory usage detected', [
                'current' => $currentMB . 'MB',
                'peak' => $peakMB . 'MB',
                'limit' => $limit,
                'url' => current_url()
            ]);
        }

        return [
            'current_mb' => $currentMB,
            'peak_mb' => $peakMB,
            'limit' => $limit
        ];
    }

    /**
     * Health check untuk sistem
     */
    public function healthCheck()
    {
        $health = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'disk_space' => $this->checkDiskSpace(),
            'memory' => $this->checkMemoryUsage()
        ];

        $health['overall'] = $this->determineOverallHealth($health);
        
        return $health;
    }

    protected function checkDatabase()
    {
        try {
            $startTime = microtime(true);
            $result = $this->db->query('SELECT 1 as test')->getRow();
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'status' => $result ? 'healthy' : 'error',
                'response_time_ms' => $responseTime,
                'connection' => $this->db->connID ? 'connected' : 'disconnected'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'response_time_ms' => null
            ];
        }
    }

    protected function checkCache()
    {
        try {
            $testKey = 'health_check_' . time();
            $testValue = 'ok';
            
            $this->cache->save($testKey, $testValue, 10);
            $retrieved = $this->cache->get($testKey);
            $this->cache->delete($testKey);

            return [
                'status' => ($retrieved === $testValue) ? 'healthy' : 'error',
                'handler' => 'file' // Default cache handler for CI4
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    protected function checkDiskSpace()
    {
        try {
            $path = WRITEPATH;
            $bytes = disk_free_space($path);
            $gb = round($bytes / 1024 / 1024 / 1024, 2);

            return [
                'status' => $gb > 1 ? 'healthy' : 'warning',
                'free_space_gb' => $gb,
                'path' => $path
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    protected function determineOverallHealth($checks)
    {
        $statuses = array_column($checks, 'status');
        
        if (in_array('error', $statuses)) {
            return 'unhealthy';
        } elseif (in_array('warning', $statuses)) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }

    /**
     * Optimize query berdasarkan pattern yang terdeteksi
     */
    public function optimizeQuery($originalQuery)
    {
        // Basic optimization suggestions
        $suggestions = [];

        if (stripos($originalQuery, 'SELECT *') !== false) {
            $suggestions[] = 'Consider selecting only needed columns instead of SELECT *';
        }

        if (stripos($originalQuery, 'ORDER BY') !== false && stripos($originalQuery, 'LIMIT') === false) {
            $suggestions[] = 'Consider adding LIMIT when using ORDER BY';
        }

        if (preg_match_all('/JOIN\s+(\w+)/i', $originalQuery, $matches)) {
            if (count($matches[1]) > 3) {
                $suggestions[] = 'Consider reducing number of JOINs or using subqueries';
            }
        }

        return $suggestions;
    }
}