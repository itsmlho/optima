<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationRecipientModel extends Model
{
    protected $table = 'notification_recipients';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'notification_id', 'user_id', 'is_read', 'read_at',
        'is_dismissed', 'dismissed_at', 'delivery_method'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false; // No updated_at field

    /**
     * Get unread count for user
     */
    public function getUnreadCount($userId)
    {
        return $this->where([
            'user_id' => $userId,
            'is_read' => 0,
            'is_dismissed' => 0
        ])->countAllResults();
    }

    /**
     * Get recent notifications for user
     */
    public function getRecentForUser($userId, $limit = 10)
    {
        $builder = $this->db->table('notification_recipients nr');
        $builder->join('notifications n', 'n.id = nr.notification_id');
        $builder->where('nr.user_id', $userId);
        $builder->where('nr.is_dismissed', 0);
        $builder->where('(n.expires_at IS NULL OR n.expires_at > NOW())');
        $builder->orderBy('n.priority', 'DESC');
        $builder->orderBy('n.created_at', 'DESC');
        $builder->limit($limit);
        
        $builder->select([
            'n.id', 'n.title', 'n.message', 'n.type', 'n.category',
            'n.icon', 'n.url', 'n.priority', 'n.created_at',
            'nr.is_read', 'nr.read_at'
        ]);

        return $builder->get()->getResultArray();
    }

    /**
     * Mark as read
     */
    public function markAsRead($notificationId, $userId)
    {
        return $this->where([
            'notification_id' => $notificationId,
            'user_id' => $userId
        ])->set([
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ])->update();
    }

    /**
     * Mark all as read for user
     */
    public function markAllAsRead($userId)
    {
        return $this->where([
            'user_id' => $userId,
            'is_read' => 0
        ])->set([
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ])->update();
    }
}
