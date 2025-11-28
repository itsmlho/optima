<?php

/**
 * ============================================================================
 * SMART CACHING HELPER - 100% GRATIS
 * ============================================================================
 * Intelligent caching functions untuk shared hosting optimization
 * ============================================================================
 */

if (!function_exists('smart_cache')) {
    /**
     * Smart cache dengan auto TTL based on data type
     */
    function smart_cache(string $key, callable $callback, string $type = 'default')
    {
        $cache = \Config\Services::cache();
        $config = config('Cache');
        
        // Get TTL based on type
        $ttl = $config->customTTL[$type] ?? ($config->ttl ?? 300);
        
        // Check if data exists in cache
        $data = $cache->get($key);
        if ($data !== null) {
            return $data;
        }
        
        // Execute callback and cache result
        $result = $callback();
        $cache->save($key, $result, $ttl);
        
        return $result;
    }
}

if (!function_exists('cache_dashboard_data')) {
    /**
     * Cache dashboard data dengan tag untuk easy invalidation
     */
    function cache_dashboard_data(int $userId, callable $callback)
    {
        return smart_cache("dashboard_data_{$userId}", $callback, 'dashboard');
    }
}

if (!function_exists('cache_user_notifications')) {
    /**
     * Cache user notifications
     */
    function cache_user_notifications(int $userId, callable $callback)
    {
        return smart_cache("user_notifications_{$userId}", $callback, 'notifications');
    }
}

if (!function_exists('cache_heavy_query')) {
    /**
     * Cache heavy database queries
     */
    function cache_heavy_query(string $queryKey, callable $callback)
    {
        return smart_cache("heavy_query_{$queryKey}", $callback, 'heavy_queries');
    }
}

if (!function_exists('cache_statistics')) {
    /**
     * Cache statistical data
     */
    function cache_statistics(string $statKey, callable $callback)
    {
        return smart_cache("statistics_{$statKey}", $callback, 'statistics');
    }
}

if (!function_exists('invalidate_user_cache')) {
    /**
     * Invalidate all cache untuk specific user
     */
    function invalidate_user_cache(int $userId): bool
    {
        $cache = \Config\Services::cache();
        
        $keys = [
            "dashboard_data_{$userId}",
            "user_notifications_{$userId}",
            "notification_count_{$userId}",
            "user_data_{$userId}",
            "user_permissions_{$userId}"
        ];
        
        $success = true;
        foreach ($keys as $key) {
            if (!$cache->delete($key)) {
                $success = false;
            }
        }
        
        return $success;
    }
}

if (!function_exists('queue_email')) {
    /**
     * Queue email untuk background processing
     */
    function queue_email(string $to, string $subject, string $message, array $options = []): string
    {
        $queueService = new \App\Services\SimpleQueueService();
        
        $emailData = array_merge([
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
        ], $options);
        
        return $queueService->push('\App\Jobs\SendEmailJob', $emailData);
    }
}

if (!function_exists('queue_notification')) {
    /**
     * Queue notification untuk background processing
     */
    function queue_notification(int $userId, string $title, string $message, array $options = []): string
    {
        $queueService = new \App\Services\SimpleQueueService();
        
        $notificationData = array_merge([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
        ], $options);
        
        return $queueService->push('\App\Jobs\ProcessNotificationJob', $notificationData);
    }
}

if (!function_exists('process_queue_jobs')) {
    /**
     * Helper untuk process queue jobs (untuk cron atau manual trigger)
     */
    function process_queue_jobs(): array
    {
        $queueService = new \App\Services\SimpleQueueService();
        return $queueService->work();
    }
}

if (!function_exists('get_cache_stats')) {
    /**
     * Get cache statistics
     */
    function get_cache_stats(): array
    {
        $cacheDir = WRITEPATH . 'cache/';
        $queueService = new \App\Services\SimpleQueueService();
        
        if (!is_dir($cacheDir)) {
            return [
                'cache_files' => 0,
                'cache_size' => '0 bytes',
                'queue_stats' => $queueService->getStats()
            ];
        }
        
        $files = glob($cacheDir . '*');
        $totalSize = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
            }
        }
        
        return [
            'cache_files' => count($files),
            'cache_size' => format_bytes($totalSize),
            'queue_stats' => $queueService->getStats()
        ];
    }
}

if (!function_exists('format_bytes')) {
    /**
     * Format bytes untuk human readable
     */
    function format_bytes(int $bytes, int $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

if (!function_exists('clear_expired_cache')) {
    /**
     * Clear expired cache files (untuk maintenance)
     */
    function clear_expired_cache(): array
    {
        $cacheDir = WRITEPATH . 'cache/';
        $files = glob($cacheDir . '*');
        $cleared = 0;
        $errors = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                // Check if file is older than 24 hours
                if (filemtime($file) < (time() - 86400)) {
                    if (unlink($file)) {
                        $cleared++;
                    } else {
                        $errors++;
                    }
                }
            }
        }
        
        return [
            'cleared' => $cleared,
            'errors' => $errors,
            'message' => "Cleared {$cleared} expired cache files"
        ];
    }
}