<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\SimpleQueueService;

/**
 * ============================================================================
 * QUEUE MANAGEMENT CONTROLLER - 100% GRATIS
 * ============================================================================
 * Manage background jobs dan queue system
 * ============================================================================
 */
class QueueController extends BaseController
{
    protected $queueService;
    
    public function __construct()
    {
        $this->queueService = new SimpleQueueService();
    }
    
    /**
     * Queue dashboard
     */
    public function index()
    {
        $stats = $this->queueService->getStats();
        $cacheStats = get_cache_stats();
        
        $data = [
            'title' => 'Queue Management',
            'page_title' => 'Background Jobs & Queue',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/queue' => 'Queue Management'
            ],
            'queue_stats' => $stats,
            'cache_stats' => $cacheStats
        ];
        
        return view('admin/queue_management', $data);
    }
    
    /**
     * Process queue jobs (manual trigger)
     */
    public function process()
    {
        try {
            $results = $this->queueService->work();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Queue processed successfully',
                'results' => $results,
                'processed_count' => count($results)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to process queue: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get queue statistics
     */
    public function stats()
    {
        $stats = $this->queueService->getStats();
        $cacheStats = get_cache_stats();
        
        return $this->response->setJSON([
            'success' => true,
            'queue_stats' => $stats,
            'cache_stats' => $cacheStats,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Clean failed jobs
     */
    public function cleanFailed()
    {
        try {
            $cleaned = $this->queueService->cleanFailedJobs();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Cleaned {$cleaned} failed jobs",
                'cleaned_count' => $cleaned
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to clean jobs: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            $cache = \Config\Services::cache();
            $result = $cache->clean();
            
            // Also clear expired cache files
            $expiredResult = clear_expired_cache();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cache cleared successfully',
                'cache_cleared' => $result,
                'expired_files_cleared' => $expiredResult['cleared']
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Test email queue
     */
    public function testEmail()
    {
        try {
            $testEmail = $this->request->getPost('email') ?: session()->get('email');
            
            if (!$testEmail) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email address required'
                ]);
            }
            
            // Get email config
            $emailConfig = config('Email');
            
            // Queue test email
            $jobId = queue_email(
                $testEmail,
                'Test Queue Email - OPTIMA System',
                $this->getTestEmailContent(),
                [
                    'from' => $emailConfig->fromEmail ?? 'itsupport@sml.co.id',
                    'from_name' => $emailConfig->fromName ?? 'OPTIMA System'
                ]
            );
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Test email queued successfully',
                'job_id' => $jobId,
                'note' => 'Email will be sent when queue is processed'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to queue email: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Test notification queue
     */
    public function testNotification()
    {
        try {
            $userId = session()->get('user_id');
            
            if (!$userId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]);
            }
            
            // Queue test notification
            $jobId = queue_notification(
                $userId,
                'Test Queue Notification',
                'This is a test notification from the queue system. Time: ' . date('H:i:s'),
                [
                    'type' => 'success',
                    'icon' => 'check-circle',
                    'url' => '/queue'
                ]
            );
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Test notification queued successfully',
                'job_id' => $jobId,
                'note' => 'Notification will appear when queue is processed'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to queue notification: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Auto-process endpoint (untuk cron job)
     * Usage: wget -q -O - http://yoursite.com/queue/auto-process
     */
    public function autoProcess()
    {
        // Simple security check
        $allowedIPs = ['127.0.0.1', '::1']; // localhost only
        $clientIP = $this->request->getIPAddress();
        
        if (!in_array($clientIP, $allowedIPs)) {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => 'Access denied'
            ]);
        }
        
        try {
            $results = $this->queueService->work();
            
            // Log for monitoring
            log_message('info', 'Auto-process queue: ' . count($results) . ' jobs processed');
            
            return $this->response->setJSON([
                'success' => true,
                'processed' => count($results),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Auto-process queue failed: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }
    
    private function getTestEmailContent(): string
    {
        return view('emails/test_queue_email', [
            'title' => 'Test Queue Email',
            'message' => 'This email was sent through the background queue system.',
            'timestamp' => date('Y-m-d H:i:s'),
            'app_name' => 'OPTIMA System'
        ]);
    }
}