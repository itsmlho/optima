<?php

/**
 * ============================================================================
 * NOTIFICATION HELPER FUNCTIONS
 * ============================================================================
 * Simple helper functions to send notifications from anywhere in the application
 * ============================================================================
 */

if (!function_exists('send_notification')) {
    /**
     * Send notification by event type
     * 
     * @param string $eventType Event type (e.g., 'spk_created', 'po_created')
     * @param array $eventData Data to replace template variables
     * @return bool|array Result of notification sending
     */
    function send_notification($eventType, $eventData = [])
    {
        try {
            $db = \Config\Database::connect();
            
            // Get active rules for this event
            $rules = $db->table('notification_rules')
                ->where('trigger_event', $eventType)
                ->where('is_active', 1)
                ->get()
                ->getResultArray();
            
            if (empty($rules)) {
                log_message('info', "No active rules found for event: {$eventType}");
                return false;
            }
            
            $notificationsSent = 0;
            
            foreach ($rules as $rule) {
                // Get target users
                $targetUsers = get_target_users_for_rule($rule);
                
                log_message('info', "Notification rule {$rule['id']} - Found " . count($targetUsers) . " target users");
                
                if (empty($targetUsers)) {
                    continue;
                }
                
                // Replace template variables
                $title = replace_template_vars($rule['title_template'], $eventData);
                $message = replace_template_vars($rule['message_template'], $eventData);
                
                log_message('info', "Notification title: {$title}");
                log_message('info', "Notification message: {$message}");
                
                // Create notification for each target user
                foreach ($targetUsers as $user) {
                    $notificationData = [
                        'user_id' => $user['id'],
                        'title' => $title,
                        'message' => $message,
                        'type' => $rule['type'],
                        'icon' => 'bell', // Default icon since column doesn't exist
                        'related_module' => $eventData['module'] ?? null,
                        'related_id' => $eventData['id'] ?? null,
                        'url' => $eventData['url'] ?? null,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    log_message('info', "Creating notification for user {$user['id']}: " . json_encode($notificationData));
                    
                    $inserted = $db->table('notifications')->insert($notificationData);
                    
                    if ($inserted) {
                        $notificationsSent++;
                        log_message('info', "Notification created successfully for user {$user['id']}");
                    } else {
                        log_message('error', "Failed to create notification for user {$user['id']}");
                    }
                }
            }
            
            log_message('info', "Sent {$notificationsSent} notifications for event: {$eventType}");
            
            return [
                'success' => $notificationsSent > 0,
                'notifications_sent' => $notificationsSent
            ];
            
        } catch (\Exception $e) {
            log_message('error', "Failed to send notification: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('send_direct_notification')) {
    /**
     * Send direct notification to specific user(s)
     * 
     * @param int|array $userId User ID or array of user IDs
     * @param string $title Notification title
     * @param string $message Notification message
     * @param array $options Additional options (type, icon, url, etc.)
     * @return bool Success status
     */
    function send_direct_notification($userId, $title, $message, $options = [])
    {
        try {
            $db = \Config\Database::connect();
            
            $userIds = is_array($userId) ? $userId : [$userId];
            
            foreach ($userIds as $id) {
                $data = [
                    'user_id' => $id,
                    'title' => $title,
                    'message' => $message,
                    'type' => $options['type'] ?? 'info',
                    'icon' => $options['icon'] ?? 'bell',
                    'related_module' => $options['module'] ?? null,
                    'related_id' => $options['id'] ?? null,
                    'url' => $options['url'] ?? null,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $db->table('notifications')->insert($data);
            }
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', "Failed to send direct notification: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_target_users_for_rule')) {
    /**
     * Get target users based on notification rule
     * 
     * @param array $rule Notification rule
     * @return array Array of users
     */
    function get_target_users_for_rule($rule)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('users u');
        $builder->select('u.id, u.username, u.email, u.division_id');
        $builder->where('u.is_active', 1);
        
        // Filter by division
        if (!empty($rule['target_divisions'])) {
            $divisions = explode(',', $rule['target_divisions']);
            $builder->join('divisions d', 'd.id = u.division_id', 'left');
            $builder->groupStart();
            foreach ($divisions as $division) {
                $division = trim($division);
                $builder->orWhere('LOWER(d.name)', strtolower($division));
                $builder->orWhere('LOWER(d.name) LIKE', '%' . strtolower($division) . '%');
            }
            $builder->groupEnd();
        }
        
        // Filter by department (if target_departments exists)
        if (!empty($rule['target_departments'])) {
            $departments = explode(',', $rule['target_departments']);
            $builder->join('user_roles ur', 'ur.user_id = u.id', 'left');
            $builder->join('departemen dep', 'dep.id_departemen = ur.department_id', 'left');
            $builder->groupStart();
            foreach ($departments as $dept) {
                $dept = trim($dept);
                $builder->orWhere('LOWER(dep.nama_departemen)', strtolower($dept));
                $builder->orWhere('LOWER(dep.nama_departemen) LIKE', '%' . strtolower($dept) . '%');
            }
            $builder->groupEnd();
        }
        
        // Filter by role (if target_roles exists)
        if (!empty($rule['target_roles'])) {
            $roles = explode(',', $rule['target_roles']);
            $builder->join('user_roles ur', 'ur.user_id = u.id', 'left');
            $builder->join('roles r', 'r.id = ur.role_id', 'left');
            $builder->groupStart();
            foreach ($roles as $role) {
                $role = trim($role);
                $builder->orWhere('LOWER(r.name) LIKE', '%' . strtolower($role) . '%');
            }
            $builder->groupEnd();
        }
        
        // Get target users
        $targetUsers = $builder->get()->getResultArray();
        
        // Auto-include superadmin if enabled
        if (!empty($rule['auto_include_superadmin']) && $rule['auto_include_superadmin'] == 1) {
            $superAdminUsers = $db->table('users u')
                ->select('u.id, u.username, u.email, u.division_id')
                ->where('u.is_active', 1)
                ->where('u.is_super_admin', 1)
                ->get()
                ->getResultArray();
            
            // Merge superadmin users with target users (avoid duplicates)
            $existingIds = array_column($targetUsers, 'id');
            foreach ($superAdminUsers as $superAdmin) {
                if (!in_array($superAdmin['id'], $existingIds)) {
                    $targetUsers[] = $superAdmin;
                }
            }
        }
        
        return $targetUsers;
    }
}

if (!function_exists('replace_template_vars')) {
    /**
     * Replace template variables with actual data
     * 
     * @param string $template Template string with {{variables}}
     * @param array $data Data array
     * @return string Processed string
     */
    function replace_template_vars($template, $data)
    {
        if (empty($template)) {
            return '';
        }
        
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        
        // Remove any remaining unreplaced variables
        $template = preg_replace('/\{\{[^}]+\}\}/', '', $template);
        
        return $template;
    }
}

if (!function_exists('notify_spk_created')) {
    /**
     * Send notification when SPK is created
     * 
     * @param array $spkData SPK data
     * @return bool|array
     */
    function notify_spk_created($spkData)
    {
        return send_notification('spk_created', [
            'module' => 'spk',
            'id' => $spkData['id'] ?? null,
            'nomor_spk' => $spkData['nomor_spk'] ?? '',
            'pelanggan' => $spkData['pelanggan'] ?? $spkData['nama_customer'] ?? '',
            'departemen' => $spkData['departemen'] ?? '',
            'url' => base_url('/service/spk/detail/' . ($spkData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_po_created')) {
    /**
     * Send notification when PO is created
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_created($poData)
    {
        return send_notification('po_created', [
            'module' => 'po',
            'id' => $poData['id'] ?? null,
            'nomor_po' => $poData['nomor_po'] ?? '',
            'supplier' => $poData['supplier'] ?? '',
            'total_items' => $poData['total_items'] ?? 0,
            'url' => base_url('/purchasing/detail/' . ($poData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_work_order_created')) {
    /**
     * Send notification when Work Order is created
     * 
     * @param array $woData Work Order data
     * @return bool|array
     */
    function notify_work_order_created($woData)
    {
        return send_notification('work_order_created', [
            'module' => 'work_order',
            'id' => $woData['id'] ?? null,
            'nomor_wo' => $woData['nomor_wo'] ?? '',
            'unit_code' => $woData['unit_code'] ?? '',
            'priority' => $woData['priority'] ?? 'Normal',
            'url' => base_url('/service/work-orders/detail/' . ($woData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_di_created')) {
    /**
     * Send notification when DI (Delivery Instruction) is created
     * 
     * @param array $diData DI data
     * @return bool|array
     */
    function notify_di_created($diData)
    {
        return send_notification('di_created', [
            'module' => 'delivery',
            'id' => $diData['id'] ?? null,
            'nomor_di' => $diData['nomor_di'] ?? '',
            'unit_code' => $diData['unit_code'] ?? '',
            'customer' => $diData['customer'] ?? '',
            'url' => base_url('/operational/delivery/detail/' . ($diData['id'] ?? ''))
        ]);
    }
}

