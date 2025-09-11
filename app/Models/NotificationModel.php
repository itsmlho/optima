<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'message', 'type', 'category', 'icon', 
        'related_table', 'related_id', 'url', 'priority', 
        'expires_at', 'created_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Create notification and automatically send to targeted users
     * 
     * @param array $data Notification data
     * @param array $options Targeting options
     * @return int|false Notification ID or false on failure
     */
    public function createAndSend($data, $options = [])
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create notification
            $notificationId = $this->insert($data);
            if (!$notificationId) {
                throw new \Exception('Failed to create notification');
            }

            // Determine recipients
            $recipients = $this->determineRecipients($data, $options);
            
            // Send to recipients
            $this->sendToRecipients($notificationId, $recipients);

            // Log the notification
            $this->logNotification($notificationId, $data, $options, $recipients);

            $db->transComplete();
            return $notificationId;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Notification creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification based on predefined rules
     * 
     * @param string $triggerEvent Event that triggered the notification
     * @param array $eventData Data related to the event
     * @return array Results of notifications sent
     */
    public function sendByRule($triggerEvent, $eventData = [])
    {
        $rulesModel = new NotificationRuleModel();
        $rules = $rulesModel->getActiveRulesByEvent($triggerEvent);
        
        $results = [];
        
        foreach ($rules as $rule) {
            // Check if conditions match
            if ($this->matchesConditions($rule, $eventData)) {
                $notification = $this->buildNotificationFromRule($rule, $eventData);
                $recipients = $this->getRecipientsFromRule($rule, $eventData);
                
                $notificationId = $this->createAndSend($notification, [
                    'specific_users' => $recipients,
                    'rule_id' => $rule['id']
                ]);
                
                $results[] = [
                    'rule_id' => $rule['id'],
                    'notification_id' => $notificationId,
                    'recipients_count' => count($recipients),
                    'success' => $notificationId !== false
                ];
            }
        }
        
        return $results;
    }

    /**
     * Determine who should receive the notification
     */
    protected function determineRecipients($data, $options)
    {
        $recipients = [];
        $userModel = new \App\Models\UserModel();

        // Specific users
        if (!empty($options['specific_users'])) {
            $recipients = array_merge($recipients, $options['specific_users']);
        }

        // By roles
        if (!empty($options['roles'])) {
            $roleUsers = $userModel->getUsersByRoles($options['roles']);
            $recipients = array_merge($recipients, array_column($roleUsers, 'id'));
        }

        // By divisions
        if (!empty($options['divisions'])) {
            $divisionUsers = $userModel->getUsersByDivisions($options['divisions']);
            $recipients = array_merge($recipients, array_column($divisionUsers, 'id'));
        }

        // By departments
        if (!empty($options['departments'])) {
            $departmentUsers = $userModel->getUsersByDepartments($options['departments']);
            $recipients = array_merge($recipients, array_column($departmentUsers, 'id'));
        }

        // By division AND department combination
        if (!empty($options['division_department_match'])) {
            foreach ($options['division_department_match'] as $match) {
                $matchUsers = $userModel->getUsersByDivisionAndDepartment(
                    $match['division'], 
                    $match['department']
                );
                $recipients = array_merge($recipients, array_column($matchUsers, 'id'));
            }
        }

        // Remove duplicates and exclude creator if specified
        $recipients = array_unique($recipients);
        
        if (!empty($options['exclude_creator']) && !empty($data['created_by'])) {
            $recipients = array_diff($recipients, [$data['created_by']]);
        }

        return array_values($recipients);
    }

    /**
     * Send notification to specific recipients
     */
    protected function sendToRecipients($notificationId, $recipients)
    {
        if (empty($recipients)) {
            return;
        }

        $recipientModel = new NotificationRecipientModel();
        $recipientData = [];

        foreach ($recipients as $userId) {
            $recipientData[] = [
                'notification_id' => $notificationId,
                'user_id' => $userId,
                'delivery_method' => 'web'
            ];
        }

        return $recipientModel->insertBatch($recipientData);
    }

    /**
     * Check if event data matches rule conditions
     */
    protected function matchesConditions($rule, $eventData)
    {
        if (empty($rule['conditions'])) {
            return true;
        }

        $conditions = json_decode($rule['conditions'], true);
        if (!$conditions) {
            return true;
        }

        foreach ($conditions as $field => $expectedValue) {
            if (!isset($eventData[$field]) || $eventData[$field] != $expectedValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * Build notification from rule template
     */
    protected function buildNotificationFromRule($rule, $eventData)
    {
        return [
            'title' => $this->processTemplate($rule['title_template'], $eventData),
            'message' => $this->processTemplate($rule['message_template'], $eventData),
            'type' => $rule['type'],
            'category' => $rule['category'],
            'priority' => $rule['priority'],
            'url' => $this->processTemplate($rule['url_template'] ?? '', $eventData),
            'expires_at' => $rule['expire_days'] > 0 ? 
                date('Y-m-d H:i:s', strtotime("+{$rule['expire_days']} days")) : null
        ];
    }

    /**
     * Get recipients based on rule targeting (Enhanced with multi-targeting and auto-superadmin)
     */
    protected function getRecipientsFromRule($rule, $eventData)
    {
        $options = [];
        $recipients = [];

        // Handle new mixed targeting (JSON format) - highest priority
        if (!empty($rule['target_mixed'])) {
            $mixedTargets = json_decode($rule['target_mixed'], true);
            
            if (is_array($mixedTargets)) {
                if (!empty($mixedTargets['roles'])) {
                    $options['roles'] = $mixedTargets['roles'];
                }
                if (!empty($mixedTargets['divisions'])) {
                    $options['divisions'] = $mixedTargets['divisions'];
                }
                if (!empty($mixedTargets['departments'])) {
                    $options['departments'] = $mixedTargets['departments'];
                }
                if (!empty($mixedTargets['users'])) {
                    $options['specific_users'] = $mixedTargets['users'];
                }
            }
        } else {
            // Fallback to legacy single-target format
            if (!empty($rule['target_roles'])) {
                $options['roles'] = explode(',', $rule['target_roles']);
            }

            if (!empty($rule['target_divisions'])) {
                $options['divisions'] = explode(',', $rule['target_divisions']);
            }

            if (!empty($rule['target_departments'])) {
                $options['departments'] = explode(',', $rule['target_departments']);
            }

            if (!empty($rule['target_users'])) {
                $options['specific_users'] = explode(',', $rule['target_users']);
            }
        }

        $options['exclude_creator'] = $rule['exclude_creator'] ?? 0;

        // Get recipients based on targeting rules
        $recipients = $this->determineRecipients([], $options);

        // Auto-include Super Administrator if enabled (default: true)
        $autoIncludeSuperadmin = $rule['auto_include_superadmin'] ?? 1;
        if ($autoIncludeSuperadmin) {
            $superadminUsers = $this->getSuperAdminUsers();
            $recipients = array_merge($recipients, $superadminUsers);
            
            // Remove duplicates
            $recipients = array_unique($recipients);
        }

        return $recipients;
    }

    /**
     * Get Super Administrator user IDs
     */
    protected function getSuperAdminUsers()
    {
        $userModel = new \App\Models\UserModel();
        $superAdmins = $userModel->getUsersByRoles(['Super Administrator']);
        return array_column($superAdmins, 'id');
    }

    /**
     * Process template variables
     */
    protected function processTemplate($template, $data)
    {
        if (empty($template)) {
            return '';
        }

        foreach ($data as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }

        return $template;
    }

    /**
     * Log notification for audit trail
     */
    protected function logNotification($notificationId, $data, $options, $recipients)
    {
        $logModel = new NotificationLogModel();
        
        $logData = [
            'notification_id' => $notificationId,
            'rule_id' => $options['rule_id'] ?? null,
            'total_recipients' => count($recipients),
            'successful_deliveries' => count($recipients), // All web notifications are immediate
            'failed_deliveries' => 0,
            'trigger_data' => json_encode($options)
        ];

        return $logModel->insert($logData);
    }

    /**
     * Get notifications for specific user
     */
    public function getUserNotifications($userId, $limit = 50, $unreadOnly = false)
    {
        $builder = $this->db->table('v_user_notifications');
        $builder->where('user_id', $userId);
        
        if ($unreadOnly) {
            $builder->where('is_read', 0);
        }
        
        $builder->orderBy('priority', 'DESC');
        $builder->orderBy('created_at', 'DESC');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get notification statistics for user
     */
    public function getUserStats($userId)
    {
        $builder = $this->db->table('v_notification_stats');
        $builder->where('user_id', $userId);
        
        $result = $builder->get()->getRowArray();
        
        return $result ?: [
            'total_notifications' => 0,
            'unread_count' => 0,
            'high_priority_count' => 0,
            'latest_notification' => null
        ];
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        $recipientModel = new NotificationRecipientModel();
        
        return $recipientModel->where([
            'notification_id' => $notificationId,
            'user_id' => $userId
        ])->set([
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ])->update();
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        $recipientModel = new NotificationRecipientModel();
        
        return $recipientModel->where([
            'user_id' => $userId,
            'is_read' => 0
        ])->set([
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ])->update();
    }

    /**
     * Dismiss notification
     */
    public function dismissNotification($notificationId, $userId)
    {
        $recipientModel = new NotificationRecipientModel();
        
        return $recipientModel->where([
            'notification_id' => $notificationId,
            'user_id' => $userId
        ])->set([
            'is_dismissed' => 1,
            'dismissed_at' => date('Y-m-d H:i:s')
        ])->update();
    }

    /**
     * Clean up expired notifications
     */
    public function cleanupExpired()
    {
        return $this->where('expires_at <=', date('Y-m-d H:i:s'))->delete();
    }
}
