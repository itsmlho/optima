<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class NotificationController extends ResourceController
{
    use ResponseTrait;

    public function __construct()
    {
        // Load required helpers
        helper(['auth']);
    }

    /**
     * Get user notifications
     */
    public function index()
    {
        try {
            // For now, return empty array since we don't have notifications table yet
            // This prevents the 404 error in the frontend
            $notifications = [];
            
            return $this->respond($notifications);
        } catch (\Exception $e) {
            log_message('error', 'Notification API Error: ' . $e->getMessage());
            return $this->respond([]);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id = null)
    {
        try {
            // For now, just return success
            // Later we can implement actual notification marking
            return $this->respond(['success' => true, 'message' => 'Notification marked as read']);
        } catch (\Exception $e) {
            log_message('error', 'Mark notification read error: ' . $e->getMessage());
            return $this->failServerError('Failed to mark notification as read');
        }
    }

    /**
     * Get notification count
     */
    public function getCount()
    {
        try {
            // For now, return 0 count
            return $this->respond(['count' => 0]);
        } catch (\Exception $e) {
            log_message('error', 'Get notification count error: ' . $e->getMessage());
            return $this->respond(['count' => 0]);
        }
    }
}
