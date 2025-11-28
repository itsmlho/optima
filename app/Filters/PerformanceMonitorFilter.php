<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\PerformanceService;

class PerformanceMonitorFilter implements FilterInterface
{
    protected $performanceService;
    protected $startTime;
    protected $startMemory;

    public function before(RequestInterface $request, $arguments = null)
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
        $this->performanceService = new PerformanceService();
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Hitung execution time
        $executionTime = microtime(true) - $this->startTime;
        $memoryUsed = memory_get_usage(true) - $this->startMemory;
        
        // Monitor slow requests (lebih dari 2 detik)
        if ($executionTime > 2.0) {
            log_message('warning', 'Slow request detected', [
                'url' => current_url(),
                'method' => $request->getMethod(),
                'execution_time' => round($executionTime, 3) . 's',
                'memory_used' => round($memoryUsed / 1024 / 1024, 2) . 'MB',
                'user_id' => session('user_id')
            ]);
        }

        // Monitor slow queries
        $this->performanceService->logSlowQueries();
        
        // Check memory usage
        $this->performanceService->checkMemoryUsage();

        // Add performance headers untuk debugging (hanya development)
        if (ENVIRONMENT !== 'production') {
            $response->setHeader('X-Execution-Time', round($executionTime * 1000, 2) . 'ms');
            $response->setHeader('X-Memory-Used', round($memoryUsed / 1024 / 1024, 2) . 'MB');
            $response->setHeader('X-Peak-Memory', round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB');
        }

        return $response;
    }
}