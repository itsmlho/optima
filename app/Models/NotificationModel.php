<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'related_module',
        'related_id',
        'url',
        'notification_style',
        'is_read',
        'read_at',
        'expires_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'user_id' => 'required|integer',
        'title' => 'required|string|max_length[255]',
        'message' => 'permit_empty|string',
        'type' => 'permit_empty|in_list[info,success,warning,error]',
        'icon' => 'permit_empty|string|max_length[50]'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be an integer'
        ],
        'title' => [
            'required' => 'Notification title is required',
            'max_length' => 'Title cannot exceed 255 characters'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    /**
     * Create and send notification to a user
     */
    public function send($userId, $title, $message, $options = [])
    {
        $data = [
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $options['type'] ?? 'info',
            'icon' => $options['icon'] ?? 'bell',
            'related_module' => $options['module'] ?? null,
            'related_id' => $options['id'] ?? null,
            'url' => $options['url'] ?? null,
            'notification_style' => $options['notification_style'] ?? 'info_only',
            'expires_at' => $options['expires_at'] ?? null
        ];

        return $this->insert($data);
    }
    
    /**
     * Create and send notification to multiple users
     */
    public function sendToMultiple($userIds, $title, $message, $options = [])
    {
        $notifications = [];

        foreach ($userIds as $userId) {
            $data = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $options['type'] ?? 'info',
                'icon' => $options['icon'] ?? 'bell',
                'related_module' => $options['module'] ?? null,
                'related_id' => $options['id'] ?? null,
                'url' => $options['url'] ?? null,
                'notification_style' => $options['notification_style'] ?? 'info_only',
                'expires_at' => $options['expires_at'] ?? null
            ];

            $notifications[] = $data;
        }

        return $this->insertBatch($notifications);
    }
    
    /**
     * Get unread notifications for a user
     */
    public function getUnreadForUser($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get unread count for a user
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null)
    {
        $builder = $this->where('id', $notificationId);
        
        if ($userId) {
            $builder->where('user_id', $userId);
        }
        
        return $builder->set([
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ])->update();
    }
    
    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsReadForUser($userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->set([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ])->update();
    }
    
    /**
     * Delete old read notifications (cleanup)
     */
    public function cleanupOldNotifications($days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('is_read', 1)
            ->where('created_at <', $date)
            ->delete();
    }
    
    /**
     * Delete expired notifications
     */
    public function cleanupExpiredNotifications()
    {
        return $this->where('expires_at IS NOT NULL')
            ->where('expires_at <', date('Y-m-d H:i:s'))
            ->delete();
    }
}
