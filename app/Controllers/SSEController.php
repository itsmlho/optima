<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

/**
 * ============================================================================
 * SERVER-SENT EVENTS (SSE) CONTROLLER
 * ============================================================================
 * Real-time notification streaming using SSE (no external dependencies)
 * ============================================================================
 */
class SSEController extends BaseController
{
    protected $notificationModel;
    protected $db;
    
    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->db = \Config\Database::connect();
    }
    
    /**
     * SSE Stream endpoint for real-time notifications
     */
    public function stream()
    {
        $userId = $this->request->getGet('user_id') ?? session()->get('user_id');
        
        if (!$userId) {
            return $this->response->setJSON(['error' => 'User belum terautentikasi. Silakan login kembali.'], 401);
        }
        
        // Set SSE headers
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Cache-Control');
        
        // Start output buffering
        ob_start();
        
        // Send initial connection event
        $this->sendSSEEvent('connected', [
            'message' => 'SSE connection established',
            'user_id' => $userId,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        // Get the last notification ID from query parameter or start from latest
        $lastNotificationId = (int)($this->request->getGet('last_id') ?? 0);
        
        // If no last_id provided, get the latest notification ID to start from
        if ($lastNotificationId === 0) {
            $latestNotification = $this->notificationModel
                ->where('user_id', $userId)
                ->orderBy('id', 'DESC')
                ->first();
            $lastNotificationId = $latestNotification['id'] ?? 0;
        }
        
        $startTime = time();
        $maxExecutionTime = 300; // 5 minutes max
        $lastHeartbeat = 0;
        
        while (time() - $startTime < $maxExecutionTime) {
            // Check for new notifications only
            $newNotifications = $this->getNewNotifications($userId, $lastNotificationId);
            
            if (!empty($newNotifications)) {
                foreach ($newNotifications as $notification) {
                    $this->sendSSEEvent('new-notification', $notification);
                    $lastNotificationId = max($lastNotificationId, (int)$notification['id']);
                }
            }
            
            // Send heartbeat every 30 seconds
            $currentTime = time();
            if ($currentTime - $lastHeartbeat >= 30) {
                $this->sendSSEEvent('heartbeat', [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'user_id' => $userId,
                    'last_id' => $lastNotificationId
                ]);
                $lastHeartbeat = $currentTime;
            }
            
            // Sleep for 2 seconds before next check (reduce CPU usage)
            sleep(2);
            
            // Flush output to client
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
            
            // Check if connection is still alive
            if (connection_aborted()) {
                break;
            }
        }
        
        // Send disconnect event
        $this->sendSSEEvent('disconnected', [
            'message' => 'SSE connection closed',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        return $this->response;
    }
    
    /**
     * Get new notifications since last ID
     */
    private function getNewNotifications($userId, $lastId)
    {
        try {
            $notifications = $this->notificationModel
                ->where('user_id', $userId)
                ->where('id >', $lastId)
                ->where('is_read', 0)
                ->orderBy('created_at', 'ASC')
                ->limit(10)
                ->findAll();
            
            return $notifications;
        } catch (\Exception $e) {
            log_message('error', 'SSE getNewNotifications failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Send SSE event to client
     */
    private function sendSSEEvent($event, $data)
    {
        $jsonData = json_encode($data);
        echo "event: {$event}\n";
        echo "data: {$jsonData}\n\n";
    }
    
    /**
     * Broadcast notification to specific user via SSE
     */
    public function broadcastToUser($userId, $notification)
    {
        // This would be called by other controllers when creating notifications
        // For now, we'll use the existing notification system
        return $this->notificationModel->insert($notification);
    }
    
    /**
     * Broadcast notification to division
     */
    public function broadcastToDivision($divisionId, $notification)
    {
        // Get all users in the division
        $users = $this->db->table('user_roles ur')
            ->join('users u', 'u.id = ur.user_id')
            ->where('ur.division_id', $divisionId)
            ->where('ur.is_active', 1)
            ->select('u.id')
            ->get()
            ->getResultArray();
        
        $userIds = array_column($users, 'id');
        $successCount = 0;
        
        foreach ($userIds as $userId) {
            $notification['user_id'] = $userId;
            if ($this->notificationModel->insert($notification)) {
                $successCount++;
            }
        }
        
        return $successCount;
    }
    
    /**
     * Broadcast notification to role
     */
    public function broadcastToRole($roleId, $notification)
    {
        // Get all users with the role
        $users = $this->db->table('user_roles ur')
            ->join('users u', 'u.id = ur.user_id')
            ->where('ur.role_id', $roleId)
            ->where('ur.is_active', 1)
            ->select('u.id')
            ->get()
            ->getResultArray();
        
        $userIds = array_column($users, 'id');
        $successCount = 0;
        
        foreach ($userIds as $userId) {
            $notification['user_id'] = $userId;
            if ($this->notificationModel->insert($notification)) {
                $successCount++;
            }
        }
        
        return $successCount;
    }
    
    /**
     * Test SSE connection
     */
    public function test()
    {
        $userId = session()->get('user_id') ?? 1;
        
        // Create test notification
        $testNotification = [
            'user_id' => $userId,
            'title' => 'SSE Test Notification',
            'message' => 'This is a test notification for SSE connection',
            'type' => 'info',
            'icon' => 'bell',
            'related_module' => 'test',
            'related_id' => 0,
            'url' => null,
            'is_read' => 0
        ];
        
        try {
            $result = $this->notificationModel->insert($testNotification);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'SSE test notification created',
                    'notification_id' => $result
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create test notification',
                    'errors' => $this->notificationModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal membuat data. Silakan coba lagi.'
            ]);
        }
    }
}
