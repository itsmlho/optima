<?php

namespace App\Controllers;

use App\Services\PerformanceService;

class HealthController extends BaseController
{
    protected $performanceService;

    public function __construct()
    {
        $this->performanceService = new PerformanceService();
    }

    /**
     * Health check endpoint
     */
    public function check()
    {
        try {
            $health = $this->performanceService->healthCheck();
            
            $httpStatus = 200;
            if ($health['overall'] === 'unhealthy') {
                $httpStatus = 503; // Service Unavailable
            } elseif ($health['overall'] === 'warning') {
                $httpStatus = 200; // OK but with warnings
            }

            return $this->response
                ->setStatusCode($httpStatus)
                ->setJSON([
                    'status' => $health['overall'],
                    'timestamp' => date('c'),
                    'checks' => $health
                ]);

        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(503)
                ->setJSON([
                    'status' => 'unhealthy',
                    'timestamp' => date('c'),
                    'error' => 'Health check failed: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Simple ping endpoint
     */
    public function ping()
    {
        return $this->response->setJSON([
            'status' => 'ok',
            'timestamp' => date('c'),
        ]);
    }

    /**
     * System information (hanya untuk admin)
     */
    public function info()
    {
        if (!$this->hasPermission('admin.access')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'Access denied']);
        }

        $memory = $this->performanceService->checkMemoryUsage();
        
        $info = [
            'php_version' => PHP_VERSION,
            'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'environment' => ENVIRONMENT,
            'memory' => $memory,
            'server_time' => date('c'),
            'timezone' => date_default_timezone_get(),
            'extensions' => get_loaded_extensions()
        ];

        return $this->response->setJSON($info);
    }

    /**
     * Performance statistics
     */
    public function performance()
    {
        if (!$this->hasPermission('admin.access')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'Access denied']);
        }

        // Ambil statistik performa dari log files atau database
        $stats = [
            'average_response_time' => $this->getAverageResponseTime(),
            'slow_queries_count' => $this->getSlowQueriesCount(),
            'cache_hit_ratio' => $this->getCacheHitRatio(),
            'error_rate' => $this->getErrorRate()
        ];

        return $this->response->setJSON($stats);
    }

    private function getAverageResponseTime()
    {
        // Implementasi untuk mendapatkan rata-rata response time
        // Bisa dari log files atau monitoring system
        return 0.5; // placeholder
    }

    private function getSlowQueriesCount()
    {
        // Count slow queries dari log
        return 0; // placeholder
    }

    private function getCacheHitRatio()
    {
        // Cache hit ratio calculation
        return 0.85; // placeholder
    }

    private function getErrorRate()
    {
        // Error rate calculation
        return 0.01; // placeholder
    }
}