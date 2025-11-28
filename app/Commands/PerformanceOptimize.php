<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\PerformanceService;
use App\Services\CacheService;

/**
 * Performance Optimization CLI Command
 * Menjalankan maintenance, cache warming, dan performance monitoring
 */
class PerformanceOptimize extends BaseCommand
{
    protected $group       = 'Optimization';
    protected $name        = 'optimize:performance';
    protected $description = 'Run performance optimizations, cache warming, and maintenance tasks';
    
    protected $usage = 'optimize:performance [options]';
    protected $arguments = [];
    protected $options = [
        '--warm-cache'     => 'Warm critical caches',
        '--clear-cache'    => 'Clear all caches before warming',
        '--run-maintenance' => 'Run database maintenance tasks',
        '--optimize-tables' => 'Optimize database tables',
        '--generate-reports' => 'Generate performance reports',
        '--all'            => 'Run all optimization tasks'
    ];
    
    protected $performanceService;
    protected $cacheService;
    
    public function run(array $params)
    {
        $this->performanceService = new PerformanceService();
        $this->cacheService = new CacheService();
        
        CLI::write('🚀 OPTIMA Performance Optimization Starting...', 'green');
        CLI::newLine();
        
        $startTime = microtime(true);
        $tasksRun = 0;
        
        // Parse options
        $warmCache = CLI::getOption('warm-cache') || CLI::getOption('all');
        $clearCache = CLI::getOption('clear-cache') || CLI::getOption('all');
        $runMaintenance = CLI::getOption('run-maintenance') || CLI::getOption('all');
        $optimizeTables = CLI::getOption('optimize-tables') || CLI::getOption('all');
        $generateReports = CLI::getOption('generate-reports') || CLI::getOption('all');
        
        // 1. Clear cache if requested
        if ($clearCache) {
            CLI::write('🗑️  Clearing existing caches...', 'yellow');
            $this->clearAllCaches();
            $tasksRun++;
        }
        
        // 2. Run database maintenance
        if ($runMaintenance) {
            CLI::write('🔧 Running database maintenance...', 'yellow');
            $this->runDatabaseMaintenance();
            $tasksRun++;
        }
        
        // 3. Optimize database tables
        if ($optimizeTables) {
            CLI::write('📊 Optimizing database tables...', 'yellow');
            $this->optimizeDatabase();
            $tasksRun++;
        }
        
        // 4. Warm critical caches
        if ($warmCache) {
            CLI::write('🔥 Warming critical caches...', 'yellow');
            $this->warmCriticalCaches();
            $tasksRun++;
        }
        
        // 5. Generate performance reports
        if ($generateReports) {
            CLI::write('📈 Generating performance reports...', 'yellow');
            $this->generatePerformanceReports();
            $tasksRun++;
        }
        
        // 6. Process queued cache refreshes
        CLI::write('🔄 Processing queued cache refreshes...', 'yellow');
        $refreshed = $this->cacheService->processScheduledRefreshes();
        CLI::write("   ✓ Processed {$refreshed} cache refresh jobs", 'green');
        $tasksRun++;
        
        $totalTime = round(microtime(true) - $startTime, 2);
        
        CLI::newLine();
        CLI::write("✅ Performance optimization completed!", 'green');
        CLI::write("   📊 Tasks run: {$tasksRun}", 'white');
        CLI::write("   ⏱️  Total time: {$totalTime}s", 'white');
        CLI::write("   💾 Memory peak: " . formatBytes(memory_get_peak_usage(true)), 'white');
        
        // Show performance summary
        $this->showPerformanceSummary();
    }
    
    private function clearAllCaches()
    {
        $cache = \Config\Services::cache();
        
        // Clear CodeIgniter cache
        $cache->clean();
        CLI::write('   ✓ CodeIgniter cache cleared', 'green');
        
        // Clear custom cache groups
        $groups = ['contracts', 'customers', 'inventory', 'reports', 'users'];
        foreach ($groups as $group) {
            $cleared = $this->cacheService->invalidateGroup($group);
            CLI::write("   ✓ {$group} cache cleared ({$cleared} keys)", 'green');
        }
        
        // Clear file-based caches
        $this->clearFileCaches();
    }
    
    private function clearFileCaches()
    {
        $cacheDir = WRITEPATH . 'cache/';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '*');
            $cleared = 0;
            foreach ($files as $file) {
                if (is_file($file) && unlink($file)) {
                    $cleared++;
                }
            }
            CLI::write("   ✓ File caches cleared ({$cleared} files)", 'green');
        }
    }
    
    private function runDatabaseMaintenance()
    {
        $db = \Config\Database::connect();
        
        // Clean up old cache invalidation records
        $deleted = $db->table('cache_invalidation_queue')
            ->where('processed_at IS NOT NULL')
            ->where('triggered_at <', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->delete();
        CLI::write("   ✓ Cleaned cache invalidation queue ({$deleted} records)", 'green');
        
        // Clean up old activity logs (keep last 30 days)
        if ($db->tableExists('activity_logs')) {
            $deleted = $db->table('activity_logs')
                ->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 days')))
                ->delete();
            CLI::write("   ✓ Cleaned activity logs ({$deleted} records)", 'green');
        }
        
        // Clean up temporary files
        $this->cleanupTempFiles();
        
        // Update table statistics
        $tables = ['kontrak', 'inventory_unit', 'customers', 'customer_locations'];
        foreach ($tables as $table) {
            $db->query("ANALYZE TABLE {$table}");
        }
        CLI::write('   ✓ Updated table statistics', 'green');
    }
    
    private function cleanupTempFiles()
    {
        $tempDirs = [
            WRITEPATH . 'uploads/temp/',
            WRITEPATH . 'cache/exports/',
            WRITEPATH . 'queue/'
        ];
        
        $totalCleaned = 0;
        foreach ($tempDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '*');
                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < strtotime('-1 hour')) {
                        if (unlink($file)) {
                            $totalCleaned++;
                        }
                    }
                }
            }
        }
        
        CLI::write("   ✓ Cleaned temporary files ({$totalCleaned} files)", 'green');
    }
    
    private function optimizeDatabase()
    {
        $db = \Config\Database::connect();
        
        // Get tables to optimize
        $tables = [
            'kontrak', 'inventory_unit', 'customer_locations', 'customers',
            'kontrak_spesifikasi', 'delivery_instruction', 'kendaraan_tracking'
        ];
        
        foreach ($tables as $table) {
            try {
                $result = $db->query("OPTIMIZE TABLE {$table}");
                CLI::write("   ✓ Optimized table: {$table}", 'green');
            } catch (\Exception $e) {
                CLI::write("   ⚠️  Failed to optimize {$table}: " . $e->getMessage(), 'yellow');
            }
        }
        
        // Check and repair if needed
        $this->checkAndRepairTables($tables);
    }
    
    private function checkAndRepairTables($tables)
    {
        $db = \Config\Database::connect();
        
        foreach ($tables as $table) {
            try {
                $result = $db->query("CHECK TABLE {$table}")->getResult();
                foreach ($result as $row) {
                    if (property_exists($row, 'Msg_text') && 
                        stripos($row->Msg_text, 'corrupt') !== false) {
                        
                        CLI::write("   🔧 Repairing corrupted table: {$table}", 'yellow');
                        $db->query("REPAIR TABLE {$table}");
                        CLI::write("   ✓ Repaired table: {$table}", 'green');
                    }
                }
            } catch (\Exception $e) {
                CLI::write("   ⚠️  Could not check table {$table}: " . $e->getMessage(), 'yellow');
            }
        }
    }
    
    private function warmCriticalCaches()
    {
        // Cache critical lookup data
        $this->cacheService->warmCache([
            'dashboard_stats',
            'lookup_data',
            'user_permissions'
        ]);
        CLI::write('   ✓ Warmed lookup data caches', 'green');
        
        // Pre-cache common dashboard queries
        $this->warmDashboardCaches();
        
        // Pre-cache user permissions for active users
        $this->warmUserCaches();
        
        // Pre-cache frequently accessed contract data
        $this->warmContractCaches();
    }
    
    private function warmDashboardCaches()
    {
        $db = \Config\Database::connect();
        
        // Dashboard stats for different periods
        $periods = [7, 30, 90];
        foreach ($periods as $days) {
            $this->cacheService->remember("dashboard_stats_{$days}d", function() use ($db, $days) {
                return $db->query("
                    SELECT 
                        DATE(dibuat_pada) as date,
                        COUNT(*) as contract_count,
                        SUM(nilai_total) as total_value
                    FROM kontrak 
                    WHERE dibuat_pada >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                    GROUP BY DATE(dibuat_pada)
                    ORDER BY date DESC
                ", [$days])->getResultArray();
            }, 3600, 'dashboard');
        }
        
        CLI::write('   ✓ Warmed dashboard caches', 'green');
    }
    
    private function warmUserCaches()
    {
        $db = \Config\Database::connect();
        
        $activeUsers = $db->table('users')
            ->where('is_active', 1)
            ->where('last_login >', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->select('id, username')
            ->limit(50)
            ->get()
            ->getResultArray();
        
        foreach ($activeUsers as $user) {
            $this->cacheService->remember("user_permissions_{$user['id']}", function() use ($user) {
                return $this->loadUserPermissions($user['id']);
            }, 7200, 'users');
        }
        
        CLI::write("   ✓ Warmed user permission caches (" . count($activeUsers) . " users)", 'green');
    }
    
    private function warmContractCaches()
    {
        $db = \Config\Database::connect();
        
        // Pre-cache recent contracts summary
        $this->cacheService->remember('recent_contracts_summary', function() use ($db) {
            return $db->query("
                SELECT 
                    status,
                    COUNT(*) as count,
                    SUM(nilai_total) as total_value
                FROM kontrak 
                WHERE dibuat_pada >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY status
            ")->getResultArray();
        }, 1800, 'contracts');
        
        // Pre-cache unit availability stats
        $this->cacheService->remember('unit_availability_stats', function() use ($db) {
            return $db->query("
                SELECT 
                    CASE 
                        WHEN kontrak_id IS NULL THEN 'available'
                        ELSE 'contracted'
                    END as status,
                    COUNT(*) as count
                FROM inventory_unit
                GROUP BY CASE WHEN kontrak_id IS NULL THEN 'available' ELSE 'contracted' END
            ")->getResultArray();
        }, 900, 'inventory');
        
        CLI::write('   ✓ Warmed contract and inventory caches', 'green');
    }
    
    private function generatePerformanceReports()
    {
        $reportData = $this->performanceService->generatePerformanceReport();
        
        // Save report to file
        $reportFile = WRITEPATH . 'reports/performance_' . date('Y-m-d_H-i-s') . '.json';
        if (!is_dir(dirname($reportFile))) {
            mkdir(dirname($reportFile), 0755, true);
        }
        
        file_put_contents($reportFile, json_encode($reportData, JSON_PRETTY_PRINT));
        CLI::write("   ✓ Performance report saved: " . basename($reportFile), 'green');
        
        // Generate summary
        $this->generateReportSummary($reportData);
    }
    
    private function generateReportSummary($reportData)
    {
        CLI::newLine();
        CLI::write('📊 PERFORMANCE SUMMARY', 'cyan');
        CLI::write('========================', 'cyan');
        
        if (isset($reportData['database'])) {
            CLI::write("Database Queries: " . ($reportData['database']['slow_queries'] ?? 'N/A'), 'white');
        }
        
        if (isset($reportData['cache'])) {
            $hitRatio = $reportData['cache']['hit_ratio'] ?? 0;
            CLI::write("Cache Hit Ratio: " . number_format($hitRatio * 100, 1) . "%", 'white');
        }
        
        if (isset($reportData['memory'])) {
            CLI::write("Memory Usage: " . formatBytes($reportData['memory']['current'] ?? 0), 'white');
        }
    }
    
    private function showPerformanceSummary()
    {
        $stats = $this->cacheService->getStats();
        
        CLI::newLine();
        CLI::write('💾 CACHE STATISTICS', 'cyan');
        CLI::write('===================', 'cyan');
        CLI::write("Hit Ratio: " . number_format($stats['hit_ratio'] * 100, 1) . "%", 'white');
        CLI::write("Total Requests: " . number_format($stats['total_requests']), 'white');
        CLI::write("Cache Keys: " . number_format($stats['key_count']), 'white');
        
        if (!empty($stats['groups'])) {
            CLI::newLine();
            CLI::write('Cache Groups:', 'yellow');
            foreach ($stats['groups'] as $group => $count) {
                CLI::write("  {$group}: {$count} keys", 'white');
            }
        }
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
}

// Helper function for formatting bytes
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}