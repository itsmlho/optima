<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ============================================================================
 * PUSHER CONTROLLER - REAL-TIME NOTIFICATION BROADCAST
 * ============================================================================
 * Handle Pusher authentication and broadcasting
 * Based on: https://www.youtube.com/watch?v=IpMByy8wU8A
 * ============================================================================
 */
class PusherController extends BaseController
{
    protected $pusher;
    
    public function __construct()
    {
        // Initialize Pusher (you'll need to install pusher/pusher-php-server)
        // composer require pusher/pusher-php-server
        $this->pusher = new \Pusher\Pusher(
            getenv('PUSHER_APP_KEY') ?: 'your-app-key',
            getenv('PUSHER_APP_SECRET') ?: 'your-app-secret',
            getenv('PUSHER_APP_ID') ?: 'your-app-id',
            [
                'cluster' => getenv('PUSHER_APP_CLUSTER') ?: 'ap1',
                'useTLS' => true
            ]
        );
    }
    
    /**
     * Pusher authentication endpoint
     */
    public function auth()
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return $this->response->setJSON(['error' => 'Unauthorized'], 401);
        }
        
        $socketId = $this->request->getPost('socket_id');
        $channelName = $this->request->getPost('channel_name');
        
        // Validate channel name (should be private-user-{user_id})
        if (!preg_match('/^private-user-(\d+)$/', $channelName, $matches)) {
            return $this->response->setJSON(['error' => 'Invalid channel'], 403);
        }
        
        $channelUserId = $matches[1];
        if ($channelUserId != $userId) {
            return $this->response->setJSON(['error' => 'Access denied'], 403);
        }
        
        // Generate auth response
        $auth = $this->pusher->authorizeChannel($channelName, $socketId);
        
        return $this->response->setJSON($auth);
    }
    
    /**
     * Broadcast notification to specific user
     */
    public function broadcastToUser($userId, $notification)
    {
        try {
            $channel = "private-user-{$userId}";
            
            $this->pusher->trigger($channel, 'new-notification', [
                'id' => $notification['id'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'type' => $notification['type'],
                'icon' => $notification['icon'],
                'related_module' => $notification['related_module'],
                'related_id' => $notification['related_id'],
                'url' => $notification['url'],
                'created_at' => $notification['created_at']
            ]);
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Pusher broadcast failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Broadcast notification to multiple users
     */
    public function broadcastToUsers($userIds, $notification)
    {
        $successCount = 0;
        
        foreach ($userIds as $userId) {
            if ($this->broadcastToUser($userId, $notification)) {
                $successCount++;
            }
        }
        
        return $successCount;
    }
    
    /**
     * Broadcast notification to division
     */
    public function broadcastToDivision($divisionId, $notification)
    {
        // Get all users in the division
        $db = \Config\Database::connect();
        $users = $db->table('user_roles ur')
            ->join('users u', 'u.id = ur.user_id')
            ->where('ur.division_id', $divisionId)
            ->where('ur.is_active', 1)
            ->select('u.id')
            ->get()
            ->getResultArray();
        
        $userIds = array_column($users, 'id');
        return $this->broadcastToUsers($userIds, $notification);
    }
    
    /**
     * Broadcast notification to role
     */
    public function broadcastToRole($roleId, $notification)
    {
        // Get all users with the role
        $db = \Config\Database::connect();
        $users = $db->table('user_roles ur')
            ->join('users u', 'u.id = ur.user_id')
            ->where('ur.role_id', $roleId)
            ->where('ur.is_active', 1)
            ->select('u.id')
            ->get()
            ->getResultArray();
        
        $userIds = array_column($users, 'id');
        return $this->broadcastToUsers($userIds, $notification);
    }
    
    /**
     * Broadcast count update
     */
    public function broadcastCountUpdate($userId, $count)
    {
        try {
            $channel = "private-user-{$userId}";
            
            $this->pusher->trigger($channel, 'count-update', [
                'count' => $count,
                'timestamp' => time()
            ]);
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Pusher count update failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Broadcast notification read event
     */
    public function broadcastNotificationRead($userId, $notificationId)
    {
        try {
            $channel = "private-user-{$userId}";
            
            $this->pusher->trigger($channel, 'notification-read', [
                'notification_id' => $notificationId,
                'timestamp' => time()
            ]);
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Pusher read broadcast failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test Pusher connection
     */
    public function test()
    {
        try {
            $this->pusher->trigger('test-channel', 'test-event', [
                'message' => 'Pusher connection test successful!',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pusher test successful'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pusher test failed: ' . $e->getMessage()
            ]);
        }
    }
}
