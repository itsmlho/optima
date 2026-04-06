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
                    // Get notification style from rule, default to info_only
                    $notificationStyle = $rule['notification_style'] ?? 'info_only';

                    $notificationData = [
                        'user_id' => $user['id'],
                        'title' => $title,
                        'message' => $message,
                        'type' => $rule['type'],
                        'icon' => 'bell',
                        'related_module' => $eventData['module'] ?? null,
                        'related_id' => $eventData['id'] ?? null,
                        // For info_only style, set URL to null (no click/redirect)
                        'url' => ($notificationStyle === 'info_only') ? null : ($eventData['url'] ?? null),
                        'notification_style' => $notificationStyle,
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
        $departemen = $spkData['departemen'] ?? $spkData['department'] ?? 'Marketing';
        $unitNo = $spkData['unit_no'] ?? $spkData['no_unit'] ?? '';
        $noUnit = $spkData['no_unit'] ?? $spkData['unit_no'] ?? '';
        $createdBy = $spkData['created_by']
            ?? session()->get('username')
            ?? session()->get('first_name')
            ?? 'System';

        return send_notification('spk_created', [
            'module'     => 'spk',
            'id'         => $spkData['id'] ?? $spkData['spk_id'] ?? null,
            'nomor_spk'  => $spkData['nomor_spk'] ?? $spkData['spk_number'] ?? '',
            'pelanggan'  => $spkData['pelanggan'] ?? $spkData['nama_customer'] ?? '',
            // Template DB saat ini memakai `{{departemen}}`, tapi kita juga sediakan alias `{{department}}`
            'departemen' => $departemen,
            'department' => $departemen,
            'unit_no'    => $unitNo,
            'no_unit'    => $noUnit,
            'created_by' => $createdBy,
            'url'        => $spkData['url'] ?? base_url('service/spk/detail/' . ($spkData['id'] ?? $spkData['spk_id'] ?? '')),
        ]);
    }
}

if (!function_exists('notify_spk_ready')) {
    /**
     * Send notification when SPK is READY for operational execution
     * 
     * @param array $spkData SPK data
     * @return bool|array
     */
    function notify_spk_ready($spkData)
    {
        return send_notification('spk_ready', [
            'module' => 'spk',
            'id' => $spkData['id'] ?? null,
            'nomor_spk' => $spkData['nomor_spk'] ?? '',
            'pelanggan' => $spkData['pelanggan'] ?? $spkData['nama_customer'] ?? '',
            'jumlah_unit' => $spkData['jumlah_unit'] ?? 0,
            'no_unit' => $spkData['no_unit'] ?? '',
            'departemen' => 'Service',
            'url' => $spkData['url'] ?? base_url('/operational/spk/detail/' . ($spkData['id'] ?? ''))
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
            'id' => $poData['id'] ?? $poData['id_po'] ?? null,
            // Support berbagai key variasi dari call-site
            'nomor_po' => $poData['nomor_po'] ?? $poData['po_number'] ?? '',
            'supplier' => $poData['supplier'] ?? $poData['supplier_name'] ?? '',
            'total_items' => $poData['total_items'] ?? $poData['total_amount'] ?? $poData['nilai_total'] ?? 0,
            'url' => $poData['url'] ?? base_url('/purchasing/po-detail/' . ($poData['id'] ?? $poData['id_po'] ?? ''))
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
            'departemen' => $woData['departemen'] ?? session('division') ?? 'Service',
            'nomor_wo' => $woData['nomor_wo'] ?? '',
            'unit_code' => $woData['unit_code'] ?? $woData['no_unit'] ?? '',
            'no_unit' => $woData['no_unit'] ?? $woData['unit_code'] ?? '',
            'unit_no' => $woData['unit_no'] ?? $woData['no_unit'] ?? $woData['unit_code'] ?? '',
            'priority' => $woData['priority'] ?? 'Normal',
            'url' => $woData['url'] ?? base_url('/service/work-orders/view/' . ($woData['id'] ?? ''))
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
            'jenis_perintah' => $diData['jenis_perintah'] ?? '',
            'url' => base_url('/operational/delivery/detail/' . ($diData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_customer_created')) {
    /**
     * Send notification when Customer is created
     * 
     * @param array $customerData Customer data
     * @return bool|array
     */
    function notify_customer_created($customerData)
    {
        return send_notification('customer_created', [
            'module' => 'customer',
            'id' => $customerData['id'] ?? null,
            'customer_name' => $customerData['customer_name'] ?? '',
            'customer_code' => $customerData['customer_code'] ?? '',
            'contact_person' => $customerData['contact_person'] ?? '',
            'phone' => $customerData['phone'] ?? ''
            // No URL - customer created, FYI to other teams
        ]);
    }
}

if (!function_exists('notify_customer_updated')) {
    /**
     * Send notification when Customer is updated
     * 
     * @param array $customerData Customer data
     * @return bool|array
     */
    function notify_customer_updated($customerData)
    {
        return send_notification('customer_updated', [
            'module' => 'customer',
            'id' => $customerData['id'] ?? null,
            'customer_name' => $customerData['customer_name'] ?? '',
            'customer_code' => $customerData['customer_code'] ?? ''
            // No URL - customer updated, informational only
        ]);
    }
}

if (!function_exists('notify_customer_deleted')) {
    /**
     * Send notification when Customer is deleted
     * 
     * @param array $customerData Customer data
     * @return bool|array
     */
    function notify_customer_deleted($customerData)
    {
        return send_notification('customer_deleted', [
            'module' => 'customer',
            'id' => $customerData['id'] ?? null,
            'customer_name' => $customerData['customer_name'] ?? '',
            'customer_code' => $customerData['customer_code'] ?? ''
            // No URL - customer deleted, no detail page exists
        ]);
    }
}

if (!function_exists('notify_customer_location_added')) {
    /**
     * Send notification when Customer Location is added
     * 
     * @param array $locationData Location data
     * @return bool|array
     */
    function notify_customer_location_added($locationData)
    {
        return send_notification('customer_location_added', [
            'module' => 'customer',
            'id' => $locationData['id'] ?? null,
            'customer_name' => $locationData['customer_name'] ?? '',
            'location_name' => $locationData['location_name'] ?? '',
            'address' => $locationData['address'] ?? ''
            // No URL - location added, informational only
        ]);
    }
}

if (!function_exists('notify_customer_contract_created')) {
    /**
     * Send notification when Contract is created
     * 
     * @param array $contractData Contract data
     * @return bool|array
     */
    function notify_customer_contract_created($contractData)
    {
        return send_notification('customer_contract_created', [
            'module' => 'contract',
            'id' => $contractData['id'] ?? null,
            'contract_number' => $contractData['no_kontrak'] ?? $contractData['contract_number'] ?? '',
            'customer_name' => $contractData['customer_name'] ?? '',
            'nilai_total' => $contractData['nilai_total'] ?? '',
            'tanggal_mulai' => $contractData['tanggal_mulai'] ?? '',
            'tanggal_selesai' => $contractData['tanggal_selesai'] ?? ''
            // No URL - contract created, informational only
        ]);
    }
}

if (!function_exists('notify_attachment_uploaded')) {
    /**
     * Send notification when Attachment is uploaded (for workorder stages)
     * 
     * @param array $attachmentData Attachment data
     * @return bool|array
     */
    function notify_attachment_uploaded($attachmentData)
    {
        return send_notification('attachment_uploaded', [
            'module' => $attachmentData['module'] ?? 'workorder',
            'id' => $attachmentData['id'] ?? null,
            'stage_name' => $attachmentData['stage_name'] ?? '',
            'spk_number' => $attachmentData['spk_number'] ?? '',
            'unit_code' => $attachmentData['unit_code'] ?? '',
            'file_name' => $attachmentData['file_name'] ?? '',
            'uploaded_by' => $attachmentData['uploaded_by'] ?? ''
            // No URL - file already uploaded, informational only
        ]);
    }
}

// ============================================================================
// CRITICAL PRIORITY NOTIFICATIONS (Phase 1 - Finance, Purchasing, WorkOrder)
// ============================================================================

if (!function_exists('notify_invoice_created')) {
    /**
     * Send notification when Invoice is created
     * 
     * @param array $invoiceData Invoice data
     * @return bool|array
     */
    function notify_invoice_created($invoiceData)
    {
        return send_notification('invoice_created', [
            'module' => 'finance',
            'id' => $invoiceData['id'] ?? null,
            'invoice_number' => $invoiceData['invoice_number'] ?? $invoiceData['nomor_invoice'] ?? '',
            'customer' => $invoiceData['customer'] ?? $invoiceData['customer_name'] ?? '',
            'customer_name' => $invoiceData['customer_name'] ?? $invoiceData['customer'] ?? '',
            'amount' => $invoiceData['amount'] ?? $invoiceData['total_amount'] ?? 0,
            'due_date' => $invoiceData['due_date'] ?? $invoiceData['tanggal_jatuh_tempo'] ?? '',
            'created_by' => $invoiceData['created_by'] ?? '',
            'url' => $invoiceData['url'] ?? base_url('/finance/invoices')
        ]);
    }
}

if (!function_exists('notify_payment_status_updated')) {
    /**
     * Send notification when Payment Status is updated
     * 
     * @param array $paymentData Payment data
     * @return bool|array
     */
    function notify_payment_status_updated($paymentData)
    {
        return send_notification('payment_status_updated', [
            'module' => 'finance',
            'id' => $paymentData['id'] ?? null,
            'invoice_number' => $paymentData['invoice_number'] ?? $paymentData['nomor_invoice'] ?? '',
            'customer_name' => $paymentData['customer_name'] ?? '',
            'old_status' => $paymentData['old_status'] ?? '',
            'new_status' => $paymentData['new_status'] ?? $paymentData['status'] ?? '',
            'amount' => $paymentData['amount'] ?? $paymentData['paid_amount'] ?? 0,
            'payment_date' => $paymentData['payment_date'] ?? $paymentData['tanggal_bayar'] ?? '',
            'updated_by' => $paymentData['updated_by'] ?? '',
            'url' => $paymentData['url'] ?? base_url('/finance/invoices')
        ]);
    }
}

if (!function_exists('notify_po_created')) {
    /**
     * Send notification when Purchase Order is created
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_created($poData)
    {
        return send_notification('po_created', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? $poData['id_po'] ?? null,
            'po_number' => $poData['po_number'] ?? $poData['nomor_po'] ?? '',
            'supplier_name' => $poData['supplier_name'] ?? '',
            'po_type' => $poData['po_type'] ?? $poData['tipe_po'] ?? '',
            'total_amount' => $poData['total_amount'] ?? $poData['nilai_total'] ?? 0,
            'delivery_date' => $poData['delivery_date'] ?? $poData['tanggal_pengiriman'] ?? '',
            'created_by' => $poData['created_by'] ?? '',
            'url' => $poData['url'] ?? base_url('/purchasing/po')
        ]);
    }
}

if (!function_exists('notify_delivery_created')) {
    /**
     * Send notification when Delivery is created
     * 
     * @param array $deliveryData Delivery data
     * @return bool|array
     */
    function notify_delivery_created($deliveryData)
    {
        return send_notification('delivery_created', [
            'module' => $deliveryData['module'] ?? 'purchasing',
            'id' => $deliveryData['id'] ?? null,
            'delivery_number' => $deliveryData['delivery_number'] ?? $deliveryData['nomor_surat_jalan'] ?? '',
            'nomor_delivery' => $deliveryData['nomor_delivery'] ?? $deliveryData['delivery_number'] ?? $deliveryData['nomor_surat_jalan'] ?? '',
            'po_number' => $deliveryData['po_number'] ?? $deliveryData['nomor_po'] ?? '',
            'supplier_name' => $deliveryData['supplier_name'] ?? '',
            'customer' => $deliveryData['customer'] ?? $deliveryData['customer_name'] ?? $deliveryData['supplier_name'] ?? '',
            'customer_name' => $deliveryData['customer_name'] ?? $deliveryData['supplier_name'] ?? '',
            'delivery_date' => $deliveryData['delivery_date'] ?? $deliveryData['tanggal_kirim'] ?? '',
            'items_count' => $deliveryData['items_count'] ?? 0,
            'created_by' => $deliveryData['created_by'] ?? '',
            'url' => $deliveryData['url'] ?? base_url('/operational/delivery/detail/' . ($deliveryData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_delivery_status_changed')) {
    /**
     * Send notification when Delivery Status is changed
     * 
     * @param array $deliveryData Delivery data
     * @return bool|array
     */
    function notify_delivery_status_changed($deliveryData)
    {
        return send_notification('delivery_status_changed', [
            'module' => 'purchasing',
            'id' => $deliveryData['id'] ?? null,
            'delivery_number' => $deliveryData['delivery_number'] ?? $deliveryData['nomor_surat_jalan'] ?? '',
            'po_number' => $deliveryData['po_number'] ?? $deliveryData['nomor_po'] ?? '',
            'old_status' => $deliveryData['old_status'] ?? '',
            'new_status' => $deliveryData['new_status'] ?? $deliveryData['status'] ?? '',
            'supplier_name' => $deliveryData['supplier_name'] ?? '',
            'updated_by' => $deliveryData['updated_by'] ?? '',
            'url' => $deliveryData['url'] ?? base_url('/operational/delivery/detail/' . ($deliveryData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_delivery_assigned')) {
    /**
     * Send notification when Delivery is assigned to driver
     * 
     * @param array $deliveryData Delivery data
     * @return bool|array
     */
    function notify_delivery_assigned($deliveryData)
    {
        // Template uses {{delivery_number}} and {{assigned_to}} — provide both canonical and alias keys
        $deliveryNumber = $deliveryData['delivery_number'] ?? $deliveryData['nomor_delivery'] ?? '';
        $assignedTo     = $deliveryData['assigned_to'] ?? $deliveryData['driver_name'] ?? '';

        return send_notification('delivery_assigned', [
            'module'          => 'operational',
            'id'              => $deliveryData['id'] ?? null,
            'delivery_number' => $deliveryNumber,   // matches {{delivery_number}} in template
            'nomor_delivery'  => $deliveryNumber,   // alias
            'assigned_to'     => $assignedTo,       // matches {{assigned_to}} in template
            'driver_name'     => $assignedTo,       // alias
            'vehicle'         => $deliveryData['vehicle'] ?? $deliveryData['no_unit'] ?? '',
            'customer_name'   => $deliveryData['customer_name'] ?? '',
            'destination'     => $deliveryData['destination'] ?? '',
            'assigned_by'     => $deliveryData['assigned_by'] ?? '',
            'url'             => $deliveryData['url'] ?? base_url('/operational/delivery/detail/' . ($deliveryData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_delivery_in_transit')) {
    /**
     * Send notification when Delivery is in transit
     * 
     * @param array $deliveryData Delivery data
     * @return bool|array
     */
    function notify_delivery_in_transit($deliveryData)
    {
        return send_notification('delivery_in_transit', [
            'module' => 'operational',
            'id' => $deliveryData['id'] ?? null,
            'nomor_delivery' => $deliveryData['nomor_delivery'] ?? $deliveryData['delivery_number'] ?? '',
            'delivery_number' => $deliveryData['delivery_number'] ?? $deliveryData['nomor_delivery'] ?? '',
            'customer' => $deliveryData['customer'] ?? $deliveryData['customer_name'] ?? '',
            'customer_name' => $deliveryData['customer_name'] ?? $deliveryData['customer'] ?? '',
            'driver_name' => $deliveryData['driver_name'] ?? '',
            'current_location' => $deliveryData['current_location'] ?? '',
            'destination' => $deliveryData['destination'] ?? '',
            'eta' => $deliveryData['eta'] ?? '',
            'url' => $deliveryData['url'] ?? base_url('/operational/delivery/detail/' . ($deliveryData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_delivery_arrived')) {
    /**
     * Send notification when Delivery arrives at destination
     * 
     * @param array $deliveryData Delivery data
     * @return bool|array
     */
    function notify_delivery_arrived($deliveryData)
    {
        return send_notification('delivery_arrived', [
            'module' => 'operational',
            'id' => $deliveryData['id'] ?? null,
            'nomor_delivery' => $deliveryData['nomor_delivery'] ?? $deliveryData['delivery_number'] ?? '',
            'delivery_number' => $deliveryData['delivery_number'] ?? $deliveryData['nomor_delivery'] ?? '',
            'customer' => $deliveryData['customer'] ?? $deliveryData['customer_name'] ?? '',
            'customer_name' => $deliveryData['customer_name'] ?? $deliveryData['customer'] ?? '',
            'arrival_time' => $deliveryData['arrival_time'] ?? date('Y-m-d H:i:s'),
            'driver_name' => $deliveryData['driver_name'] ?? '',
            'location' => $deliveryData['location'] ?? '',
            'url' => $deliveryData['url'] ?? base_url('/operational/delivery/detail/' . ($deliveryData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_delivery_completed')) {
    /**
     * Send notification when Delivery is completed
     * 
     * @param array $deliveryData Delivery data
     * @return bool|array
     */
    function notify_delivery_completed($deliveryData)
    {
        return send_notification('delivery_completed', [
            'module' => 'operational',
            'id' => $deliveryData['id'] ?? null,
            'nomor_delivery' => $deliveryData['nomor_delivery'] ?? $deliveryData['delivery_number'] ?? '',
            'delivery_number' => $deliveryData['delivery_number'] ?? $deliveryData['nomor_delivery'] ?? '',
            'customer' => $deliveryData['customer'] ?? $deliveryData['customer_name'] ?? '',
            'customer_name' => $deliveryData['customer_name'] ?? $deliveryData['customer'] ?? '',
            'completed_time' => $deliveryData['completed_time'] ?? date('Y-m-d H:i:s'),
            'signature' => $deliveryData['signature'] ?? 'Yes',
            'notes' => $deliveryData['notes'] ?? '',
            'completed_by' => $deliveryData['completed_by'] ?? ''
            // No URL - delivery already completed, informational only
        ]);
    }
}

if (!function_exists('notify_delivery_delayed')) {
    /**
     * Send notification when Delivery is delayed (CRITICAL ALERT)
     * 
     * @param array $deliveryData Delivery data
     * @return bool|array
     */
    function notify_delivery_delayed($deliveryData)
    {
        return send_notification('delivery_delayed', [
            'module' => 'operational',
            'id' => $deliveryData['id'] ?? null,
            'nomor_delivery' => $deliveryData['nomor_delivery'] ?? $deliveryData['delivery_number'] ?? '',
            'customer_name' => $deliveryData['customer_name'] ?? '',
            'scheduled_time' => $deliveryData['scheduled_time'] ?? '',
            'current_time' => date('Y-m-d H:i:s'),
            'delay_reason' => $deliveryData['delay_reason'] ?? 'Unknown',
            'estimated_arrival' => $deliveryData['estimated_arrival'] ?? '',
            'driver_name' => $deliveryData['driver_name'] ?? '',
            'url' => $deliveryData['url'] ?? base_url('/operational/delivery/detail/' . ($deliveryData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_workorder_created')) {
    /**
     * Send notification when Work Order is created
     * 
     * @param array $workorderData Work Order data
     * @return bool|array
     */
    function notify_workorder_created($workorderData)
    {
        return send_notification('workorder_created', [
            'module' => 'workorder',
            'id' => $workorderData['id'] ?? null,
            'wo_number' => $workorderData['wo_number'] ?? $workorderData['nomor_wo'] ?? '',
            'unit_code' => $workorderData['unit_code'] ?? $workorderData['no_unit'] ?? '',
            'order_type' => $workorderData['order_type'] ?? '',
            'priority' => $workorderData['priority'] ?? '',
            'category' => $workorderData['category'] ?? '',
            'complaint' => $workorderData['complaint'] ?? $workorderData['complaint_description'] ?? '',
            'created_by' => $workorderData['created_by'] ?? '',
            'url' => $workorderData['url'] ?? base_url('/service/work-orders')
        ]);
    }
}

if (!function_exists('notify_workorder_status_changed')) {
    /**
     * Send notification when Work Order Status is changed
     * 
     * @param array $workorderData Work Order data
     * @return bool|array
     */
    function notify_workorder_status_changed($workorderData)
    {
        return send_notification('workorder_status_changed', [
            'module' => 'workorder',
            'id' => $workorderData['id'] ?? null,
            'wo_number' => $workorderData['wo_number'] ?? $workorderData['nomor_wo'] ?? '',
            'unit_code' => $workorderData['unit_code'] ?? $workorderData['no_unit'] ?? '',
            'old_status' => $workorderData['old_status'] ?? '',
            'new_status' => $workorderData['new_status'] ?? $workorderData['status'] ?? '',
            'updated_by' => $workorderData['updated_by'] ?? '',
            'url' => $workorderData['url'] ?? base_url('/service/work-orders')
        ]);
    }
}

if (!function_exists('notify_po_verification_updated')) {
    /**
     * Send notification when PO Verification is updated
     * 
     * @param array $verificationData Verification data
     * @return bool|array
     */
    function notify_po_verification_updated($verificationData)
    {
        return send_notification('po_verification_updated', [
            'module' => 'purchasing',
            'id' => $verificationData['id'] ?? null,
            'po_number' => $verificationData['po_number'] ?? $verificationData['nomor_po'] ?? '',
            'verification_status' => $verificationData['verification_status'] ?? $verificationData['status_verifikasi'] ?? '',
            'verified_by' => $verificationData['verified_by'] ?? '',
            'verification_date' => $verificationData['verification_date'] ?? date('Y-m-d H:i:s'),
            'notes' => $verificationData['notes'] ?? $verificationData['catatan'] ?? '',
            'url' => $verificationData['url'] ?? base_url('/purchasing/po-verification')
        ]);
    }
}

// ============================================================================
// PHASE 1 CRITICAL PRIORITY - MISSING IMPLEMENTATIONS
// ============================================================================

if (!function_exists('notify_invoice_overdue')) {
    /**
     * Send notification when Invoice is OVERDUE (CRITICAL ALERT)
     * 
     * @param array $invoiceData Invoice data
     * @return bool|array
     */
    function notify_invoice_overdue($invoiceData)
    {
        return send_notification('invoice_overdue', [
            'module' => 'finance',
            'id' => $invoiceData['id'] ?? null,
            'invoice_number' => $invoiceData['invoice_number'] ?? $invoiceData['nomor_invoice'] ?? '',
            'customer_name' => $invoiceData['customer_name'] ?? '',
            'amount' => $invoiceData['amount'] ?? $invoiceData['total_amount'] ?? 0,
            'due_date' => $invoiceData['due_date'] ?? $invoiceData['tanggal_jatuh_tempo'] ?? '',
            'days_overdue' => $invoiceData['days_overdue'] ?? 0,
            'url' => $invoiceData['url'] ?? base_url('/finance/invoices')
        ]);
    }
}

if (!function_exists('notify_invoice_paid')) {
    /**
     * Send notification when Invoice is paid
     * 
     * @param array $invoiceData Invoice data
     * @return bool|array
     */
    function notify_invoice_paid($invoiceData)
    {
        return send_notification('invoice_paid', [
            'module' => 'finance',
            'id' => $invoiceData['id'] ?? null,
            'invoice_number' => $invoiceData['invoice_number'] ?? $invoiceData['nomor_invoice'] ?? '',
            'customer_name' => $invoiceData['customer_name'] ?? '',
            'amount' => $invoiceData['amount'] ?? $invoiceData['total_amount'] ?? 0,
            'payment_date' => $invoiceData['payment_date'] ?? $invoiceData['tanggal_bayar'] ?? '',
            'payment_method' => $invoiceData['payment_method'] ?? ''
            // No URL - invoice already paid, informational only
        ]);
    }
}

if (!function_exists('notify_invoice_sent')) {
    /**
     * Send notification when Invoice is sent to customer
     * 
     * @param array $invoiceData Invoice data
     * @return bool|array
     */
    function notify_invoice_sent($invoiceData)
    {
        return send_notification('invoice_sent', [
            'module' => 'finance',
            'id' => $invoiceData['id'] ?? null,
            'invoice_number' => $invoiceData['invoice_number'] ?? $invoiceData['nomor_invoice'] ?? '',
            'customer' => $invoiceData['customer'] ?? $invoiceData['customer_name'] ?? '',
            'customer_name' => $invoiceData['customer_name'] ?? $invoiceData['customer'] ?? '',
            'amount' => $invoiceData['amount'] ?? $invoiceData['total_amount'] ?? 0,
            'sent_date' => $invoiceData['sent_date'] ?? date('Y-m-d H:i:s'),
            'sent_by' => $invoiceData['sent_by'] ?? ''
            // No URL - invoice already sent, informational only
        ]);
    }
}

if (!function_exists('notify_sparepart_low_stock')) {
    /**
     * Send notification when Sparepart stock is LOW (CRITICAL ALERT)
     * 
     * @param array $sparepartData Sparepart data
     * @return bool|array
     */
    function notify_sparepart_low_stock($sparepartData)
    {
        return send_notification('sparepart_low_stock', [
            'module' => 'inventory',
            'id' => $sparepartData['id'] ?? null,
            'nama_sparepart' => $sparepartData['nama_sparepart'] ?? $sparepartData['name'] ?? '',
            'kode_sparepart' => $sparepartData['kode_sparepart'] ?? $sparepartData['code'] ?? '',
            'part_number' => $sparepartData['kode_sparepart'] ?? $sparepartData['code'] ?? '',
            'qty' => $sparepartData['qty'] ?? $sparepartData['stock'] ?? 0,
            'minimum_stock' => $sparepartData['minimum_stock'] ?? $sparepartData['min_stock'] ?? 0,
            'unit' => $sparepartData['unit'] ?? $sparepartData['satuan'] ?? '',
            'location' => $sparepartData['location'] ?? $sparepartData['lokasi'] ?? '',
            'url' => $sparepartData['url'] ?? base_url('/warehouse/inventory/invent_sparepart')
        ]);
    }
}

if (!function_exists('notify_sparepart_out_of_stock')) {
    /**
     * Send notification when Sparepart is OUT OF STOCK (CRITICAL ALERT)
     * 
     * @param array $sparepartData Sparepart data
     * @return bool|array
     */
    function notify_sparepart_out_of_stock($sparepartData)
    {
        return send_notification('sparepart_out_of_stock', [
            'module' => 'inventory',
            'id' => $sparepartData['id'] ?? null,
            'nama_sparepart' => $sparepartData['nama_sparepart'] ?? $sparepartData['name'] ?? '',
            'kode_sparepart' => $sparepartData['kode_sparepart'] ?? $sparepartData['code'] ?? '',
            'part_number' => $sparepartData['kode_sparepart'] ?? $sparepartData['code'] ?? '',
            'last_used_date' => $sparepartData['last_used_date'] ?? '',
            'location' => $sparepartData['location'] ?? $sparepartData['lokasi'] ?? '',
            'url' => $sparepartData['url'] ?? base_url('/warehouse/inventory/invent_sparepart')
        ]);
    }
}

if (!function_exists('notify_sparepart_added')) {
    /**
     * Send notification when Sparepart is added
     * 
     * @param array $sparepartData Sparepart data
     * @return bool|array
     */
    function notify_sparepart_added($sparepartData)
    {
        return send_notification('sparepart_added', [
            'module' => 'inventory',
            'id' => $sparepartData['id'] ?? null,
            'nama_sparepart' => $sparepartData['nama_sparepart'] ?? $sparepartData['name'] ?? '',
            'kode_sparepart' => $sparepartData['kode_sparepart'] ?? $sparepartData['code'] ?? '',
            'part_number' => $sparepartData['kode_sparepart'] ?? $sparepartData['code'] ?? '',
            'part_name' => $sparepartData['nama_sparepart'] ?? $sparepartData['name'] ?? '',
            'qty' => $sparepartData['qty'] ?? $sparepartData['stock'] ?? 0,
            'unit' => $sparepartData['unit'] ?? $sparepartData['satuan'] ?? '',
            'supplier' => $sparepartData['supplier'] ?? '',
            'added_by' => $sparepartData['added_by'] ?? ''
            // No URL - sparepart added, FYI only
        ]);
    }
}

if (!function_exists('notify_pmps_due_soon')) {
    /**
     * Send notification when PMPS is due soon (CRITICAL ALERT)
     * 
     * @param array $pmpsData PMPS data
     * @return bool|array
     */
    function notify_pmps_due_soon($pmpsData)
    {
        return send_notification('pmps_due_soon', [
            'module' => 'maintenance',
            'id' => $pmpsData['id'] ?? null,
            'departemen' => $pmpsData['departemen'] ?? session('division') ?? 'Service',
            'unit_no' => $pmpsData['unit_no'] ?? $pmpsData['no_unit'] ?? '',
            'no_unit' => $pmpsData['no_unit'] ?? $pmpsData['unit_no'] ?? '',
            'unit_model' => $pmpsData['unit_model'] ?? $pmpsData['model'] ?? '',
            'due_date' => $pmpsData['due_date'] ?? $pmpsData['tanggal_jatuh_tempo'] ?? '',
            'days' => $pmpsData['days'] ?? $pmpsData['days_until_due'] ?? 0,
            'current_hours' => $pmpsData['current_hours'] ?? $pmpsData['jam_operasional'] ?? 0,
            'service_type' => $pmpsData['service_type'] ?? $pmpsData['tipe_service'] ?? '',
            'url' => $pmpsData['url'] ?? base_url('/service/pmps')
        ]);
    }
}

if (!function_exists('notify_pmps_overdue')) {
    /**
     * Send notification when PMPS is OVERDUE (CRITICAL ALERT)
     * 
     * @param array $pmpsData PMPS data
     * @return bool|array
     */
    function notify_pmps_overdue($pmpsData)
    {
        return send_notification('pmps_overdue', [
            'module' => 'maintenance',
            'id' => $pmpsData['id'] ?? null,
            'departemen' => $pmpsData['departemen'] ?? session('division') ?? 'Service',
            'unit_no' => $pmpsData['unit_no'] ?? $pmpsData['no_unit'] ?? '',
            'no_unit' => $pmpsData['no_unit'] ?? $pmpsData['unit_no'] ?? '',
            'unit_model' => $pmpsData['unit_model'] ?? $pmpsData['model'] ?? '',
            'due_date' => $pmpsData['due_date'] ?? $pmpsData['tanggal_jatuh_tempo'] ?? '',
            'days' => $pmpsData['days'] ?? $pmpsData['days_overdue'] ?? 0,
            'current_hours' => $pmpsData['current_hours'] ?? $pmpsData['jam_operasional'] ?? 0,
            'service_type' => $pmpsData['service_type'] ?? $pmpsData['tipe_service'] ?? '',
            'url' => $pmpsData['url'] ?? base_url('/service/pmps')
        ]);
    }
}

if (!function_exists('notify_pmps_completed')) {
    /**
     * Send notification when PMPS is completed
     * 
     * @param array $pmpsData PMPS data
     * @return bool|array
     */
    function notify_pmps_completed($pmpsData)
    {
        return send_notification('pmps_completed', [
            'module' => 'maintenance',
            'id' => $pmpsData['id'] ?? null,
            'departemen' => $pmpsData['departemen'] ?? session('division') ?? 'Service',
            'unit_no' => $pmpsData['unit_no'] ?? $pmpsData['no_unit'] ?? '',
            'no_unit' => $pmpsData['no_unit'] ?? $pmpsData['unit_no'] ?? '',
            'unit_model' => $pmpsData['unit_model'] ?? $pmpsData['model'] ?? '',
            'completion_date' => $pmpsData['completion_date'] ?? date('Y-m-d H:i:s'),
            'service_type' => $pmpsData['service_type'] ?? $pmpsData['tipe_service'] ?? '',
            'mechanic' => $pmpsData['mechanic'] ?? $pmpsData['mekanik'] ?? '',
            'next_service_date' => $pmpsData['next_service_date'] ?? ''
            // No URL - PMPS already completed, informational only
        ]);
    }
}

if (!function_exists('notify_customer_contract_expired')) {
    /**
     * Send notification when Customer Contract is expired/expiring (WARNING)
     * 
     * @param array $contractData Contract data
     * @return bool|array
     */
    function notify_customer_contract_expired($contractData)
    {
        return send_notification('customer_contract_expired', [
            'module' => 'contract',
            'id' => $contractData['id'] ?? null,
            'contract_number' => $contractData['contract_number'] ?? $contractData['no_kontrak'] ?? '',
            'customer_name' => $contractData['customer_name'] ?? '',
            'end_date' => $contractData['end_date'] ?? $contractData['tanggal_selesai'] ?? '',
            'days' => $contractData['days'] ?? $contractData['days_until_expired'] ?? 0,
            'contract_value' => $contractData['contract_value'] ?? $contractData['nilai_total'] ?? 0,
            'url' => $contractData['url'] ?? base_url('/marketing/customer-management')
        ]);
    }
}

if (!function_exists('notify_inventory_unit_low_stock')) {
    /**
     * Send notification when Inventory Unit stock is low (CRITICAL ALERT)
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_inventory_unit_low_stock($unitData)
    {
        return send_notification('inventory_unit_low_stock', [
            'module' => 'inventory',
            'id' => $unitData['id'] ?? null,
            'tipe' => $unitData['tipe'] ?? $unitData['type'] ?? $unitData['model'] ?? '',
            'count' => $unitData['count'] ?? $unitData['available_count'] ?? 0,
            'minimum_stock' => $unitData['minimum_stock'] ?? $unitData['min_stock'] ?? 0,
            'status' => $unitData['status'] ?? 'Available',
            'category' => $unitData['category'] ?? $unitData['kategori'] ?? '',
            'url' => $unitData['url'] ?? base_url('/warehouse/inventory/invent_unit')
        ]);
    }
}

if (!function_exists('notify_attachment_broken')) {
    /**
     * Send notification when Attachment/Battery/Charger is BROKEN (CRITICAL ALERT)
     * 
     * @param array $attachmentData Attachment data
     * @return bool|array
     */
    function notify_attachment_broken($attachmentData)
    {
        return send_notification('attachment_broken', [
            'module' => 'inventory',
            'id' => $attachmentData['id'] ?? null,
            'tipe_item' => $attachmentData['tipe_item'] ?? $attachmentData['type'] ?? '',
            'serial_number' => $attachmentData['serial_number'] ?? $attachmentData['sn'] ?? '',
            'no_unit' => $attachmentData['no_unit'] ?? $attachmentData['unit_code'] ?? '',
            'damage_description' => $attachmentData['damage_description'] ?? $attachmentData['keterangan'] ?? '',
            'reported_by' => $attachmentData['reported_by'] ?? '',
            'report_date' => $attachmentData['report_date'] ?? date('Y-m-d H:i:s'),
            'url' => $attachmentData['url'] ?? base_url('/warehouse/inventory/invent_attachment')
        ]);
    }
}

// ============================================================================
// PHASE 2 HIGH PRIORITY - PURCHASE ORDER WORKFLOW (8 events)
// ============================================================================

if (!function_exists('notify_po_approved')) {
    /**
     * Send notification when Purchase Order is approved
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_approved($poData)
    {
        return send_notification('po_approved', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? null,
            'nomor_po' => $poData['nomor_po'] ?? $poData['po_number'] ?? '',
            'supplier_name' => $poData['supplier_name'] ?? '',
            'total_amount' => $poData['total_amount'] ?? $poData['nilai_total'] ?? 0,
            'approved_by' => $poData['approved_by'] ?? '',
            'approval_date' => $poData['approval_date'] ?? date('Y-m-d H:i:s')
            // No URL - PO already approved, informational only
        ]);
    }
}

if (!function_exists('notify_po_rejected')) {
    /**
     * Send notification when Purchase Order is rejected
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_rejected($poData)
    {
        return send_notification('po_rejected', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? null,
            'nomor_po' => $poData['nomor_po'] ?? $poData['po_number'] ?? '',
            'supplier_name' => $poData['supplier_name'] ?? '',
            'alasan' => $poData['alasan'] ?? $poData['rejection_reason'] ?? '',
            'rejected_by' => $poData['rejected_by'] ?? '',
            'rejection_date' => $poData['rejection_date'] ?? date('Y-m-d H:i:s'),
            'url' => $poData['url'] ?? base_url('/purchasing/po-unit')
        ]);
    }
}

if (!function_exists('notify_po_received')) {
    /**
     * Send notification when Purchase Order goods are received
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_received($poData)
    {
        return send_notification('po_received', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? null,
            'nomor_po' => $poData['nomor_po'] ?? $poData['po_number'] ?? '',
            'supplier_name' => $poData['supplier_name'] ?? '',
            'received_date' => $poData['received_date'] ?? date('Y-m-d H:i:s'),
            'received_by' => $poData['received_by'] ?? '',
            'items_received' => $poData['items_received'] ?? 0
            // No URL - PO already received, informational only
        ]);
    }
}

if (!function_exists('notify_po_verified')) {
    /**
     * Send notification when Purchase Order is verified
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_verified($poData)
    {
        return send_notification('po_verified', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? null,
            'nomor_po' => $poData['nomor_po'] ?? $poData['po_number'] ?? '',
            'supplier_name' => $poData['supplier_name'] ?? '',
            'verified_by' => $poData['verified_by'] ?? '',
            'verification_date' => $poData['verification_date'] ?? date('Y-m-d H:i:s')
            // No URL - informational only, no action required
        ]);
    }
}

if (!function_exists('notify_po_unit_created')) {
    /**
     * Send notification when PO Unit is created
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_unit_created($poData)
    {
        return send_notification('po_unit_created', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? null,
            'nomor_po' => $poData['nomor_po'] ?? $poData['po_number'] ?? '',
            'supplier_name' => $poData['supplier_name'] ?? '',
            'unit_type' => $poData['unit_type'] ?? $poData['tipe_unit'] ?? '',
            'quantity' => $poData['quantity'] ?? $poData['qty'] ?? 0,
            'total_amount' => $poData['total_amount'] ?? $poData['nilai_total'] ?? 0,
            'created_by' => $poData['created_by'] ?? '',
            'url' => $poData['url'] ?? base_url('/purchasing/po-unit')
        ]);
    }
}

if (!function_exists('notify_po_attachment_created')) {
    /**
     * Send notification when PO Attachment is created
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_attachment_created($poData)
    {
        return send_notification('po_attachment_created', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? null,
            'nomor_po' => $poData['nomor_po'] ?? $poData['po_number'] ?? '',
            'supplier_name' => $poData['supplier_name'] ?? '',
            'attachment_type' => $poData['attachment_type'] ?? $poData['tipe_attachment'] ?? '',
            'quantity' => $poData['quantity'] ?? $poData['qty'] ?? 0,
            'total_amount' => $poData['total_amount'] ?? $poData['nilai_total'] ?? 0,
            'created_by' => $poData['created_by'] ?? '',
            'url' => $poData['url'] ?? base_url('/purchasing/po-attachment')
        ]);
    }
}

if (!function_exists('notify_po_sparepart_created')) {
    /**
     * Send notification when PO Sparepart is created
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_sparepart_created($poData)
    {
        return send_notification('po_sparepart_created', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? null,
            'nomor_po' => $poData['nomor_po'] ?? $poData['po_number'] ?? '',
            'supplier_name' => $poData['supplier_name'] ?? '',
            'items_count' => $poData['items_count'] ?? 0,
            'total_amount' => $poData['total_amount'] ?? $poData['nilai_total'] ?? 0,
            'created_by' => $poData['created_by'] ?? '',
            'url' => $poData['url'] ?? base_url('/purchasing/po-sparepart')
        ]);
    }
}

if (!function_exists('notify_purchase_order_created')) {
    /**
     * Send notification when generic Purchase Order is created
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_purchase_order_created($poData)
    {
        return send_notification('purchase_order_created', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? null,
            'po_number' => $poData['po_number'] ?? $poData['nomor_po'] ?? '',
            'vendor' => $poData['vendor'] ?? $poData['supplier_name'] ?? '',
            'amount' => $poData['amount'] ?? $poData['total_amount'] ?? 0,
            'created_by' => $poData['created_by'] ?? '',
            'url' => $poData['url'] ?? base_url('/purchasing/po')
        ]);
    }
}

// ============================================================================
// PHASE 2 HIGH PRIORITY - DI WORKFLOW (5 events)
// ============================================================================

if (!function_exists('notify_di_submitted')) {
    /**
     * Send notification when DI is submitted
     * 
     * @param array $diData DI data
     * @return bool|array
     */
    function notify_di_submitted($diData)
    {
        return send_notification('di_submitted', [
            'module' => 'operational',
            'id' => $diData['id'] ?? null,
            'nomor_di' => $diData['nomor_di'] ?? '',
            'pelanggan' => $diData['pelanggan'] ?? $diData['customer'] ?? '',
            'lokasi' => $diData['lokasi'] ?? $diData['location'] ?? '',
            'jenis_perintah' => $diData['jenis_perintah'] ?? '',
            'submitted_by' => $diData['submitted_by'] ?? '',
            'url' => $diData['url'] ?? base_url('/operational/delivery')
        ]);
    }
}

if (!function_exists('notify_di_approved')) {
    /**
     * Send notification when DI is approved
     * 
     * @param array $diData DI data
     * @return bool|array
     */
    function notify_di_approved($diData)
    {
        return send_notification('di_approved', [
            'module' => 'operational',
            'id' => $diData['id'] ?? null,
            'nomor_di' => $diData['nomor_di'] ?? '',
            'customer' => $diData['customer'] ?? '',
            'approved_by' => $diData['approved_by'] ?? '',
            'approval_date' => $diData['approval_date'] ?? date('Y-m-d H:i:s')
            // No URL - DI already approved, informational only
        ]);
    }
}

if (!function_exists('notify_di_in_progress')) {
    /**
     * Send notification when DI is in progress
     * 
     * @param array $diData DI data
     * @return bool|array
     */
    function notify_di_in_progress($diData)
    {
        return send_notification('di_in_progress', [
            'module' => 'operational',
            'id' => $diData['id'] ?? null,
            'nomor_di' => $diData['nomor_di'] ?? '',
            'customer' => $diData['customer'] ?? '',
            'driver_name' => $diData['driver_name'] ?? '',
            'current_status' => $diData['current_status'] ?? 'In Progress',
            'url' => $diData['url'] ?? base_url('/operational/delivery')
        ]);
    }
}

if (!function_exists('notify_di_delivered')) {
    /**
     * Send notification when DI is delivered/completed
     * 
     * @param array $diData DI data
     * @return bool|array
     */
    function notify_di_delivered($diData)
    {
        return send_notification('di_delivered', [
            'module' => 'operational',
            'id' => $diData['id'] ?? null,
            'nomor_di' => $diData['nomor_di'] ?? '',
            'customer' => $diData['customer'] ?? '',
            'delivery_date' => $diData['delivery_date'] ?? date('Y-m-d H:i:s'),
            'driver_name' => $diData['driver_name'] ?? ''
            // No URL - DI already delivered, informational only
        ]);
    }
}

if (!function_exists('notify_di_cancelled')) {
    /**
     * Send notification when DI is cancelled
     * 
     * @param array $diData DI data
     * @return bool|array
     */
    function notify_di_cancelled($diData)
    {
        return send_notification('di_cancelled', [
            'module' => 'operational',
            'id' => $diData['id'] ?? null,
            'nomor_di' => $diData['nomor_di'] ?? '',
            'customer' => $diData['customer'] ?? '',
            'alasan' => $diData['alasan'] ?? $diData['cancellation_reason'] ?? '',
            'cancelled_by' => $diData['cancelled_by'] ?? ''
            // No URL - DI cancelled, no action needed
        ]);
    }
}

// ============================================================================
// PHASE 2 HIGH PRIORITY - WORK ORDER EXTENDED (4 events)
// ============================================================================

if (!function_exists('notify_work_order_assigned')) {
    /**
     * Send notification when Work Order is assigned to mechanic
     * 
     * @param array $woData Work Order data
     * @return bool|array
     */
    function notify_work_order_assigned($woData)
    {
        return send_notification('work_order_assigned', [
            'module' => 'work_order',
            'id' => $woData['id'] ?? null,
            'departemen' => $woData['departemen'] ?? session('division') ?? 'Service',
            'nomor_wo' => $woData['nomor_wo'] ?? $woData['wo_number'] ?? '',
            'wo_number' => $woData['wo_number'] ?? $woData['nomor_wo'] ?? '',
            'unit_code' => $woData['unit_code'] ?? $woData['no_unit'] ?? '',
            'no_unit' => $woData['no_unit'] ?? $woData['unit_code'] ?? '',
            'mechanic_name' => $woData['mechanic_name'] ?? $woData['mekanik'] ?? '',
            'priority' => $woData['priority'] ?? '',
            'assigned_by' => $woData['assigned_by'] ?? '',
            'url' => $woData['url'] ?? base_url('/service/work-orders')
        ]);
    }
}

if (!function_exists('notify_work_order_in_progress')) {
    /**
     * Send notification when Work Order is in progress
     * 
     * @param array $woData Work Order data
     * @return bool|array
     */
    function notify_work_order_in_progress($woData)
    {
        return send_notification('work_order_in_progress', [
            'module' => 'work_order',
            'id' => $woData['id'] ?? null,
            'departemen' => $woData['departemen'] ?? session('division') ?? 'Service',
            'nomor_wo' => $woData['nomor_wo'] ?? $woData['wo_number'] ?? '',
            'wo_number' => $woData['wo_number'] ?? $woData['nomor_wo'] ?? '',
            'unit_code' => $woData['unit_code'] ?? $woData['no_unit'] ?? '',
            'no_unit' => $woData['no_unit'] ?? $woData['unit_code'] ?? '',
            'mechanic' => $woData['mechanic'] ?? $woData['mekanik'] ?? '',
            'progress' => $woData['progress'] ?? 0,
            'url' => $woData['url'] ?? base_url('/service/work-orders')
        ]);
    }
}

if (!function_exists('notify_work_order_completed')) {
    /**
     * Send notification when Work Order is completed
     * 
     * @param array $woData Work Order data
     * @return bool|array
     */
    function notify_work_order_completed($woData)
    {
        return send_notification('work_order_completed', [
            'module' => 'work_order',
            'id' => $woData['id'] ?? null,
            'departemen' => $woData['departemen'] ?? session('division') ?? 'Service',
            'nomor_wo' => $woData['nomor_wo'] ?? $woData['wo_number'] ?? '',
            'wo_number' => $woData['wo_number'] ?? $woData['nomor_wo'] ?? '',
            'unit_code' => $woData['unit_code'] ?? $woData['no_unit'] ?? '',
            'no_unit' => $woData['no_unit'] ?? $woData['unit_code'] ?? '',
            'completion_date' => $woData['completion_date'] ?? date('Y-m-d H:i:s'),
            'mechanic' => $woData['mechanic'] ?? $woData['mekanik'] ?? ''
            // No URL - work order already completed, informational only
        ]);
    }
}

if (!function_exists('notify_work_order_cancelled')) {
    /**
     * Send notification when Work Order is cancelled
     * 
     * @param array $woData Work Order data
     * @return bool|array
     */
    function notify_work_order_cancelled($woData)
    {
        return send_notification('work_order_cancelled', [
            'module' => 'work_order',
            'id' => $woData['id'] ?? null,
            'departemen' => $woData['departemen'] ?? session('division') ?? 'Service',
            'nomor_wo' => $woData['nomor_wo'] ?? $woData['wo_number'] ?? '',
            'wo_number' => $woData['wo_number'] ?? $woData['nomor_wo'] ?? '',
            'unit_code' => $woData['unit_code'] ?? $woData['no_unit'] ?? '',
            'no_unit' => $woData['no_unit'] ?? $woData['unit_code'] ?? '',
            'cancellation_reason' => $woData['cancellation_reason'] ?? $woData['alasan'] ?? '',
            'cancelled_by' => $woData['cancelled_by'] ?? ''
            // No URL - work order cancelled, no action needed
        ]);
    }
}

// ============================================================================
// PHASE 3 MEDIUM PRIORITY - INVENTORY UNIT (6 events)
// ============================================================================

if (!function_exists('notify_inventory_unit_added')) {
    /**
     * Send notification when Inventory Unit is added
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_inventory_unit_added($unitData)
    {
        return send_notification('inventory_unit_added', [
            'module' => 'inventory',
            'id' => $unitData['id'] ?? null,
            'no_unit' => $unitData['no_unit'] ?? $unitData['unit_code'] ?? '',
            'model' => $unitData['model'] ?? $unitData['tipe'] ?? '',
            'serial_number' => $unitData['serial_number'] ?? '',
            'status' => $unitData['status'] ?? 'Available',
            'added_by' => $unitData['added_by'] ?? ''
            // No URL - unit added, FYI only
        ]);
    }
}

if (!function_exists('notify_inventory_unit_status_changed')) {
    /**
     * Send notification when Inventory Unit status changes
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_inventory_unit_status_changed($unitData)
    {
        return send_notification('inventory_unit_status_changed', [
            'module' => 'inventory',
            'id' => $unitData['id'] ?? null,
            'no_unit' => $unitData['no_unit'] ?? $unitData['unit_code'] ?? '',
            'old_status' => $unitData['old_status'] ?? '',
            'new_status' => $unitData['new_status'] ?? $unitData['status'] ?? '',
            'changed_by' => $unitData['changed_by'] ?? '',
            'url' => $unitData['url'] ?? base_url('/warehouse/inventory/get-unit-detail/' . ($unitData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_inventory_unit_rental_active')) {
    /**
     * Send notification when Inventory Unit rental becomes active
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_inventory_unit_rental_active($unitData)
    {
        return send_notification('inventory_unit_rental_active', [
            'module' => 'inventory',
            'id' => $unitData['id'] ?? null,
            'no_unit' => $unitData['no_unit'] ?? $unitData['unit_code'] ?? '',
            'customer' => $unitData['customer'] ?? $unitData['customer_name'] ?? '',
            'rental_start_date' => $unitData['rental_start_date'] ?? date('Y-m-d'),
            'rental_duration' => $unitData['rental_duration'] ?? '',
            'url' => $unitData['url'] ?? base_url('/warehouse/inventory/get-unit-detail/' . ($unitData['id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_inventory_unit_returned')) {
    /**
     * Send notification when Inventory Unit is returned from rental
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_inventory_unit_returned($unitData)
    {
        return send_notification('inventory_unit_returned', [
            'module' => 'inventory',
            'id' => $unitData['id'] ?? null,
            'no_unit' => $unitData['no_unit'] ?? $unitData['unit_code'] ?? '',
            'customer' => $unitData['customer'] ?? $unitData['customer_name'] ?? '',
            'return_date' => $unitData['return_date'] ?? date('Y-m-d H:i:s'),
            'condition' => $unitData['condition'] ?? 'Good'
            // No URL - unit returned, informational only
        ]);
    }
}

if (!function_exists('notify_inventory_unit_maintenance')) {
    /**
     * Send notification when Inventory Unit enters maintenance
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_inventory_unit_maintenance($unitData)
    {
        return send_notification('inventory_unit_maintenance', [
            'module' => 'inventory',
            'id' => $unitData['id'] ?? null,
            'no_unit' => $unitData['no_unit'] ?? $unitData['unit_code'] ?? '',
            'alasan' => $unitData['alasan'] ?? $unitData['maintenance_reason'] ?? '',
            'scheduled_date' => $unitData['scheduled_date'] ?? date('Y-m-d'),
            'estimated_completion' => $unitData['estimated_completion'] ?? '',
            'url' => $unitData['url'] ?? base_url('/warehouse/inventory/get-unit-detail/' . ($unitData['id'] ?? ''))
        ]);
    }
}

// ============================================================================
// PHASE 3 MEDIUM PRIORITY - ATTACHMENT MANAGEMENT (5 events)
// ============================================================================

if (!function_exists('notify_attachment_added')) {
    /**
     * Send notification when Attachment/Battery/Charger is added
     * 
     * @param array $attachmentData Attachment data
     * @return bool|array
     */
    function notify_attachment_added($attachmentData)
    {
        return send_notification('attachment_added', [
            'module' => 'inventory',
            'id' => $attachmentData['id'] ?? null,
            'tipe_item' => $attachmentData['tipe_item'] ?? $attachmentData['type'] ?? '',
            'serial_number' => $attachmentData['serial_number'] ?? $attachmentData['sn'] ?? '',
            'brand' => $attachmentData['brand'] ?? $attachmentData['merk'] ?? '',
            'status' => $attachmentData['status'] ?? 'Available',
            'added_by' => $attachmentData['added_by'] ?? ''
            // No URL - attachment added, FYI only
        ]);
    }
}

if (!function_exists('notify_attachment_attached')) {
    /**
     * Send notification when Attachment is attached to unit
     * 
     * @param array $attachmentData Attachment data
     * @return bool|array
     */
    function notify_attachment_attached($attachmentData)
    {
        return send_notification('attachment_attached', [
            'module' => $attachmentData['module'] ?? 'inventory',
            'attachment_id' => $attachmentData['attachment_id'] ?? $attachmentData['id'] ?? null,
            'tipe_item' => $attachmentData['tipe_item'] ?? $attachmentData['type'] ?? '',
            'attachment_info' => $attachmentData['attachment_info'] ?? '',
            'serial_number' => $attachmentData['serial_number'] ?? $attachmentData['sn'] ?? '',
            'no_unit' => $attachmentData['no_unit'] ?? $attachmentData['unit_code'] ?? '',
            'unit_id' => $attachmentData['unit_id'] ?? null,
            'performed_by' => $attachmentData['performed_by'] ?? $attachmentData['attached_by'] ?? '',
            'performed_at' => $attachmentData['performed_at'] ?? $attachmentData['attachment_date'] ?? date('Y-m-d H:i:s'),
            'notes' => $attachmentData['notes'] ?? '',
            'url' => $attachmentData['url'] ?? base_url('/warehouse/inventory/invent_attachment')
        ]);
    }
}

if (!function_exists('notify_attachment_detached')) {
    /**
     * Send notification when Attachment is detached from unit
     * 
     * @param array $attachmentData Attachment data
     * @return bool|array
     */
    function notify_attachment_detached($attachmentData)
    {
        return send_notification('attachment_detached', [
            'module' => $attachmentData['module'] ?? 'inventory',
            'attachment_id' => $attachmentData['attachment_id'] ?? $attachmentData['id'] ?? null,
            'tipe_item' => $attachmentData['tipe_item'] ?? $attachmentData['type'] ?? '',
            'attachment_info' => $attachmentData['attachment_info'] ?? '',
            'serial_number' => $attachmentData['serial_number'] ?? $attachmentData['sn'] ?? '',
            'no_unit' => $attachmentData['no_unit'] ?? $attachmentData['unit_code'] ?? '',
            'unit_id' => $attachmentData['unit_id'] ?? null,
            'performed_by' => $attachmentData['performed_by'] ?? $attachmentData['detached_by'] ?? '',
            'performed_at' => $attachmentData['performed_at'] ?? date('Y-m-d H:i:s'),
            'reason' => $attachmentData['reason'] ?? '',
            'new_location' => $attachmentData['new_location'] ?? '',
            'url' => $attachmentData['url'] ?? base_url('/warehouse/inventory/invent_attachment')
        ]);
    }
}

if (!function_exists('notify_attachment_maintenance')) {
    /**
     * Send notification when Attachment enters maintenance
     * 
     * @param array $attachmentData Attachment data
     * @return bool|array
     */
    function notify_attachment_maintenance($attachmentData)
    {
        return send_notification('attachment_maintenance', [
            'module' => 'inventory',
            'id' => $attachmentData['id'] ?? null,
            'tipe_item' => $attachmentData['tipe_item'] ?? $attachmentData['type'] ?? '',
            'serial_number' => $attachmentData['serial_number'] ?? $attachmentData['sn'] ?? '',
            'maintenance_reason' => $attachmentData['maintenance_reason'] ?? $attachmentData['alasan'] ?? '',
            'scheduled_date' => $attachmentData['scheduled_date'] ?? date('Y-m-d'),
            'url' => $attachmentData['url'] ?? base_url('/warehouse/inventory/invent_attachment')
        ]);
    }
}

// ============================================================================
// PHASE 3 MEDIUM PRIORITY - USER MANAGEMENT (9 events)
// ============================================================================

if (!function_exists('notify_user_created')) {
    /**
     * Send notification when User is created
     * 
     * @param array $userData User data
     * @return bool|array
     */
    function notify_user_created($userData)
    {
        return send_notification('user_created', [
            'module' => 'user',
            'id' => $userData['id'] ?? null,
            'username' => $userData['username'] ?? '',
            'email' => $userData['email'] ?? '',
            'role' => $userData['role'] ?? $userData['role_name'] ?? '',
            'division' => $userData['division'] ?? $userData['division_name'] ?? '',
            'created_by' => $userData['created_by'] ?? ''
            // No URL - user created, FYI to admins
        ]);
    }
}

if (!function_exists('notify_user_updated')) {
    /**
     * Send notification when User is updated
     * 
     * @param array $userData User data
     * @return bool|array
     */
    function notify_user_updated($userData)
    {
        return send_notification('user_updated', [
            'module' => 'user',
            'id' => $userData['id'] ?? null,
            'username' => $userData['username'] ?? '',
            'email' => $userData['email'] ?? '',
            'updated_by' => $userData['updated_by'] ?? ''
            // No URL - user updated, FYI to admins
        ]);
    }
}

if (!function_exists('notify_user_deleted')) {
    /**
     * Send notification when User is deleted
     * 
     * @param array $userData User data
     * @return bool|array
     */
    function notify_user_deleted($userData)
    {
        return send_notification('user_deleted', [
            'module' => 'user',
            'id' => $userData['id'] ?? null,
            'username' => $userData['username'] ?? '',
            'email' => $userData['email'] ?? '',
            'deleted_by' => $userData['deleted_by'] ?? ''
            // No URL - user deleted, no detail page exists
        ]);
    }
}

if (!function_exists('notify_user_activated')) {
    /**
     * Send notification when User is activated
     * 
     * @param array $userData User data
     * @return bool|array
     */
    function notify_user_activated($userData)
    {
        return send_notification('user_activated', [
            'module' => 'user',
            'id' => $userData['id'] ?? null,
            'username' => $userData['username'] ?? '',
            'email' => $userData['email'] ?? '',
            'activated_by' => $userData['activated_by'] ?? ''
            // No URL - user activated, informational only
        ]);
    }
}

if (!function_exists('notify_user_deactivated')) {
    /**
     * Send notification when User is deactivated
     * 
     * @param array $userData User data
     * @return bool|array
     */
    function notify_user_deactivated($userData)
    {
        return send_notification('user_deactivated', [
            'module' => 'user',
            'id' => $userData['id'] ?? null,
            'username' => $userData['username'] ?? '',
            'email' => $userData['email'] ?? '',
            'reason' => $userData['reason'] ?? '',
            'deactivated_by' => $userData['deactivated_by'] ?? ''
            // No URL - user deactivated, informational only
        ]);
    }
}

if (!function_exists('notify_password_reset')) {
    /**
     * Send notification when Password is reset
     * 
     * @param array $userData User data
     * @return bool|array
     */
    function notify_password_reset($userData)
    {
        return send_notification('password_reset', [
            'module' => 'user',
            'id' => $userData['id'] ?? null,
            'username' => $userData['username'] ?? '',
            'email' => $userData['email'] ?? '',
            'reset_by' => $userData['reset_by'] ?? 'System',
            'url' => $userData['url'] ?? base_url('/login')
        ]);
    }
}

if (!function_exists('notify_role_created')) {
    /**
     * Send notification when Role is created
     * 
     * @param array $roleData Role data
     * @return bool|array
     */
    function notify_role_created($roleData)
    {
        return send_notification('role_created', [
            'module' => 'role',
            'id' => $roleData['id'] ?? null,
            'role_name' => $roleData['role_name'] ?? $roleData['name'] ?? '',
            'permissions_count' => $roleData['permissions_count'] ?? 0,
            'created_by' => $roleData['created_by'] ?? ''
            // No URL - role created, FYI to admins
        ]);
    }
}

if (!function_exists('notify_role_updated')) {
    /**
     * Send notification when Role is updated
     * 
     * @param array $roleData Role data
     * @return bool|array
     */
    function notify_role_updated($roleData)
    {
        return send_notification('role_updated', [
            'module' => 'role',
            'id' => $roleData['id'] ?? null,
            'role_name' => $roleData['role_name'] ?? $roleData['name'] ?? '',
            'updated_by' => $roleData['updated_by'] ?? '',
            'changes' => $roleData['changes'] ?? ''
            // No URL - role updated, informational only
        ]);
    }
}

if (!function_exists('notify_permission_changed')) {
    /**
     * Send notification when User permission is changed
     * 
     * @param array $userData User data
     * @return bool|array
     */
    function notify_permission_changed($userData)
    {
        return send_notification('permission_changed', [
            'module' => 'permission',
            'id' => $userData['id'] ?? null,
            'username' => $userData['username'] ?? '',
            'changed_permissions' => $userData['changed_permissions'] ?? '',
            'changed_by' => $userData['changed_by'] ?? '',
            'url' => $userData['url'] ?? base_url('/dashboard')
        ]);
    }
}

// ============================================================================
// PHASE 4 LOW PRIORITY - SUPPLIER MANAGEMENT (3 events)
// ============================================================================

if (!function_exists('notify_supplier_created')) {
    /**
     * Send notification when Supplier is created
     * 
     * @param array $supplierData Supplier data
     * @return bool|array
     */
    function notify_supplier_created($supplierData)
    {
        return send_notification('supplier_created', [
            'module' => 'supplier',
            'id' => $supplierData['id'] ?? null,
            'supplier_name' => $supplierData['supplier_name'] ?? $supplierData['name'] ?? '',
            'supplier_code' => $supplierData['supplier_code'] ?? $supplierData['code'] ?? '',
            'contact_person' => $supplierData['contact_person'] ?? '',
            'phone' => $supplierData['phone'] ?? '',
            'created_by' => $supplierData['created_by'] ?? ''
            // No URL - supplier created, FYI only
        ]);
    }
}

if (!function_exists('notify_supplier_updated')) {
    /**
     * Send notification when Supplier is updated
     * 
     * @param array $supplierData Supplier data
     * @return bool|array
     */
    function notify_supplier_updated($supplierData)
    {
        return send_notification('supplier_updated', [
            'module' => 'supplier',
            'id' => $supplierData['id'] ?? null,
            'supplier_name' => $supplierData['supplier_name'] ?? $supplierData['name'] ?? '',
            'updated_by' => $supplierData['updated_by'] ?? ''
            // No URL - supplier updated, informational only
        ]);
    }
}

if (!function_exists('notify_supplier_deleted')) {
    /**
     * Send notification when Supplier is deleted
     * 
     * @param array $supplierData Supplier data
     * @return bool|array
     */
    function notify_supplier_deleted($supplierData)
    {
        return send_notification('supplier_deleted', [
            'module' => 'supplier',
            'id' => $supplierData['id'] ?? null,
            'supplier_name' => $supplierData['supplier_name'] ?? $supplierData['name'] ?? '',
            'deleted_by' => $supplierData['deleted_by'] ?? ''
            // No URL - supplier deleted, no detail page exists
        ]);
    }
}

// ============================================================================
// PHASE 4 LOW PRIORITY - EMPLOYEE MANAGEMENT (2 events)
// ============================================================================

if (!function_exists('notify_employee_assigned')) {
    /**
     * Send notification when Employee is assigned to area
     * 
     * @param array $employeeData Employee data
     * @return bool|array
     */
    function notify_employee_assigned($employeeData)
    {
        return send_notification('employee_assigned', [
            'module' => 'employee',
            'id' => $employeeData['id'] ?? null,
            'employee_name' => $employeeData['employee_name'] ?? $employeeData['name'] ?? '',
            'area_name' => $employeeData['area_name'] ?? '',
            'position' => $employeeData['position'] ?? $employeeData['jabatan'] ?? '',
            'assigned_by' => $employeeData['assigned_by'] ?? '',
            'url' => $employeeData['url'] ?? base_url('/service/area-management')
        ]);
    }
}

if (!function_exists('notify_employee_unassigned')) {
    /**
     * Send notification when Employee is unassigned from area
     * 
     * @param array $employeeData Employee data
     * @return bool|array
     */
    function notify_employee_unassigned($employeeData)
    {
        return send_notification('employee_unassigned', [
            'module' => 'employee',
            'id' => $employeeData['id'] ?? null,
            'employee_name' => $employeeData['employee_name'] ?? $employeeData['name'] ?? '',
            'area_name' => $employeeData['area_name'] ?? '',
            'reason' => $employeeData['reason'] ?? '',
            'unassigned_by' => $employeeData['unassigned_by'] ?? '',
            'url' => $employeeData['url'] ?? base_url('/service/area-management')
        ]);
    }
}

// ============================================================================
// PHASE 4 LOW PRIORITY - SPK & UNIT PREPARATION (4 events)
// ============================================================================

if (!function_exists('notify_spk_assigned')) {
    /**
     * Send notification when SPK is assigned to mechanic
     * 
     * @param array $spkData SPK data
     * @return bool|array
     */
    function notify_spk_assigned($spkData)
    {
        return send_notification('spk_assigned', [
            'module' => 'spk',
            'id' => $spkData['id'] ?? null,
            'departemen' => $spkData['departemen'] ?? session('division') ?? 'Service',
            'nomor_spk' => $spkData['nomor_spk'] ?? '',
            'no_unit' => $spkData['no_unit'] ?? $spkData['unit_code'] ?? '',
            'mechanic_name' => $spkData['mechanic_name'] ?? $spkData['mekanik'] ?? '',
            'assigned_by' => $spkData['assigned_by'] ?? '',
            'url' => $spkData['url'] ?? base_url('/service/spk_service')
        ]);
    }
}

if (!function_exists('notify_spk_cancelled')) {
    /**
     * Send notification when SPK is cancelled
     * 
     * @param array $spkData SPK data
     * @return bool|array
     */
    function notify_spk_cancelled($spkData)
    {
        return send_notification('spk_cancelled', [
            'module' => 'spk',
            'id' => $spkData['id'] ?? null,
            'departemen' => $spkData['departemen'] ?? session('division') ?? 'Marketing',
            'nomor_spk' => $spkData['nomor_spk'] ?? '',
            'pelanggan' => $spkData['pelanggan'] ?? $spkData['customer'] ?? '',
            'alasan' => $spkData['alasan'] ?? $spkData['cancellation_reason'] ?? '',
            'cancelled_by' => $spkData['cancelled_by'] ?? ''
            // No URL - SPK cancelled, no action needed
        ]);
    }
}

if (!function_exists('notify_unit_prep_started')) {
    /**
     * Send notification when Unit Preparation is started
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_unit_prep_started($unitData)
    {
        return send_notification('unit_prep_started', [
            'module' => 'spk',
            'id' => $unitData['id'] ?? null,
            'no_unit' => $unitData['no_unit'] ?? $unitData['unit_code'] ?? '',
            'nomor_spk' => $unitData['nomor_spk'] ?? '',
            'mechanic' => $unitData['mechanic'] ?? $unitData['mekanik'] ?? '',
            'start_date' => $unitData['start_date'] ?? date('Y-m-d H:i:s'),
            'url' => $unitData['url'] ?? base_url('/service/spk_service')
        ]);
    }
}

if (!function_exists('notify_unit_prep_completed')) {
    /**
     * Send notification when Unit Preparation is completed
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_unit_prep_completed($unitData)
    {
        return send_notification('unit_prep_completed', [
            'module' => 'spk',
            'id' => $unitData['id'] ?? null,
            'no_unit' => $unitData['no_unit'] ?? $unitData['unit_code'] ?? '',
            'nomor_spk' => $unitData['nomor_spk'] ?? '',
            'mechanic' => $unitData['mechanic'] ?? $unitData['mekanik'] ?? '',
            'completion_date' => $unitData['completion_date'] ?? date('Y-m-d H:i:s')
            // No URL - unit prep already completed, informational only
        ]);
    }
}

// ============================================================================
// PHASE 4 LOW PRIORITY - PAYMENT (1 event)
// ============================================================================

if (!function_exists('notify_payment_received')) {
    /**
     * Send notification when Payment is received
     * 
     * @param array $paymentData Payment data
     * @return bool|array
     */
    function notify_payment_received($paymentData)
    {
        return send_notification('payment_received', [
            'module' => 'payment',
            'id' => $paymentData['id'] ?? null,
            'amount' => $paymentData['amount'] ?? $paymentData['jumlah'] ?? 0,
            'customer' => $paymentData['customer'] ?? $paymentData['customer_name'] ?? '',
            'customer_name' => $paymentData['customer_name'] ?? $paymentData['customer'] ?? '',
            'payment_method' => $paymentData['payment_method'] ?? $paymentData['metode_pembayaran'] ?? '',
            'invoice_number' => $paymentData['invoice_number'] ?? $paymentData['nomor_invoice'] ?? '',
            'received_by' => $paymentData['received_by'] ?? ''
            // No URL - payment already received, informational only
        ]);
    }
}

// ============================================================================
// HIGH PRIORITY NOTIFICATIONS (Phase 2 - Marketing, WorkOrder Extended, etc.)
// ============================================================================

// --- MARKETING / QUOTATION NOTIFICATIONS ---

if (!function_exists('notify_quotation_created')) {
    /**
     * Send notification when Quotation is created
     * 
     * @param array $quotationData Quotation data
     * @return bool|array
     */
    function notify_quotation_created($quotationData)
    {
        return send_notification('quotation_created', [
            'module' => 'marketing',
            'id' => $quotationData['id'] ?? null,
            'quotation_number' => $quotationData['quotation_number'] ?? $quotationData['nomor_quotation'] ?? '',
            'customer_name' => $quotationData['customer_name'] ?? '',
            'customer' => $quotationData['customer_name'] ?? '',
            'total_value' => $quotationData['total_value'] ?? $quotationData['nilai_total'] ?? 0,
            'stage' => $quotationData['stage'] ?? 'Initial',
            'created_by' => $quotationData['created_by'] ?? '',
            'url' => $quotationData['url'] ?? base_url('/marketing/quotation-detail')
        ]);
    }
}

if (!function_exists('notify_quotation_stage_changed')) {
    /**
     * Send notification when Quotation stage changes
     * 
     * @param array $quotationData Quotation data
     * @return bool|array
     */
    function notify_quotation_stage_changed($quotationData)
    {
        return send_notification('quotation_stage_changed', [
            'module' => 'marketing',
            'id' => $quotationData['id'] ?? null,
            'quotation_number' => $quotationData['quotation_number'] ?? '',
            'customer_name' => $quotationData['customer_name'] ?? '',
            'old_stage' => $quotationData['old_stage'] ?? '',
            'new_stage' => $quotationData['new_stage'] ?? $quotationData['stage'] ?? '',
            'updated_by' => $quotationData['updated_by'] ?? '',
            'url' => $quotationData['url'] ?? base_url('/marketing/quotation-detail')
        ]);
    }
}

if (!function_exists('notify_contract_completed')) {
    /**
     * Send notification when Contract is completed/finalized
     * 
     * @param array $contractData Contract data
     * @return bool|array
     */
    function notify_contract_completed($contractData)
    {
        return send_notification('contract_completed', [
            'module' => 'contract',
            'id' => $contractData['id'] ?? null,
            'contract_number' => $contractData['contract_number'] ?? $contractData['no_kontrak'] ?? '',
            'customer_name' => $contractData['customer_name'] ?? '',
            'total_value' => $contractData['total_value'] ?? $contractData['nilai_total'] ?? 0,
            'completion_date' => $contractData['completion_date'] ?? date('Y-m-d'),
            'completed_by' => $contractData['completed_by'] ?? ''
            // No URL - contract already completed, informational only
        ]);
    }
}

if (!function_exists('notify_po_created_from_quotation')) {
    /**
     * Send notification when PO is created from Quotation
     * 
     * @param array $poData PO data
     * @return bool|array
     */
    function notify_po_created_from_quotation($poData)
    {
        return send_notification('po_created_from_quotation', [
            'module' => 'purchasing',
            'id' => $poData['id'] ?? null,
            'po_number' => $poData['po_number'] ?? '',
            'quotation_number' => $poData['quotation_number'] ?? '',
            'customer_name' => $poData['customer_name'] ?? '',
            'created_by' => $poData['created_by'] ?? '',
            'url' => $poData['url'] ?? base_url('/purchasing/po')
        ]);
    }
}

// --- WORKORDER EXTENDED NOTIFICATIONS ---

if (!function_exists('notify_workorder_ttr_updated')) {
    /**
     * Send notification when WorkOrder TTR (Time To Repair) is updated
     * 
     * @param array $ttrData TTR data
     * @return bool|array
     */
    function notify_workorder_ttr_updated($ttrData)
    {
        return send_notification('workorder_ttr_updated', [
            'module' => 'workorder',
            'id' => $ttrData['id'] ?? null,
            'wo_number' => $ttrData['wo_number'] ?? '',
            'unit_code' => $ttrData['unit_code'] ?? '',
            'ttr_hours' => $ttrData['ttr_hours'] ?? 0,
            'updated_by' => $ttrData['updated_by'] ?? '',
            'url' => $ttrData['url'] ?? base_url('/service/work-orders')
        ]);
    }
}

if (!function_exists('notify_unit_verification_saved')) {
    /**
     * Send notification when Unit Verification is saved
     * 
     * @param array $verificationData Verification data
     * @return bool|array
     */
    function notify_unit_verification_saved($verificationData)
    {
        return send_notification('unit_verification_saved', [
            'module' => 'service',
            'id' => $verificationData['id'] ?? null,
            'wo_number' => $verificationData['wo_number'] ?? '',
            'unit_code' => $verificationData['unit_code'] ?? '',
            'verification_status' => $verificationData['verification_status'] ?? '',
            'verified_by' => $verificationData['verified_by'] ?? '',
            'verification_date' => $verificationData['verification_date'] ?? date('Y-m-d H:i:s'),
            'url' => $verificationData['url'] ?? base_url('/service/unit-verification')
        ]);
    }
}

if (!function_exists('notify_sparepart_validation_saved')) {
    /**
     * Send notification when Sparepart Validation is saved
     * 
     * @param array $validationData Validation data
     * @return bool|array
     */
    function notify_sparepart_validation_saved($validationData)
    {
        return send_notification('sparepart_validation_saved', [
            'module' => 'workorder',
            'id' => $validationData['id'] ?? null,
            'wo_number' => $validationData['wo_number'] ?? '',
            'sparepart_count' => $validationData['sparepart_count'] ?? 0,
            'validated_by' => $validationData['validated_by'] ?? '',
            'validation_date' => $validationData['validation_date'] ?? date('Y-m-d H:i:s'),
            'url' => $validationData['url'] ?? base_url('/service/work-orders')
        ]);
    }
}

if (!function_exists('notify_sparepart_used')) {
    /**
     * Send notification when Sparepart is used/consumed
     * 
     * @param array $sparepartData Sparepart usage data
     * @return bool|array
     */
    function notify_sparepart_used($sparepartData)
    {
        return send_notification('sparepart_used', [
            'module' => 'workorder',
            'id' => $sparepartData['id'] ?? null,
            'wo_number' => $sparepartData['wo_number'] ?? '',
            'sparepart_name' => $sparepartData['sparepart_name'] ?? '',
            'part_number' => $sparepartData['sparepart_code'] ?? $sparepartData['kode_sparepart'] ?? $sparepartData['sparepart_name'] ?? '',
            'reference' => $sparepartData['wo_number'] ?? $sparepartData['reference'] ?? '',
            'quantity' => $sparepartData['quantity'] ?? 0,
            'unit_code' => $sparepartData['unit_code'] ?? '',
            'used_by' => $sparepartData['used_by'] ?? '',
            'url' => $sparepartData['url'] ?? base_url('/service/work-orders')
        ]);
    }
}

// --- SERVICE ASSIGNMENT NOTIFICATIONS ---

if (!function_exists('notify_service_assignment_created')) {
    /**
     * Send notification when Service Assignment is created
     * 
     * @param array $assignmentData Assignment data
     * @return bool|array
     */
    function notify_service_assignment_created($assignmentData)
    {
        return send_notification('service_assignment_created', [
            'module' => 'service_area',
            'id' => $assignmentData['id'] ?? null,
            'employee_name' => $assignmentData['employee_name'] ?? '',
            'area_name' => $assignmentData['area_name'] ?? '',
            'role' => $assignmentData['role'] ?? '',
            'start_date' => $assignmentData['start_date'] ?? date('Y-m-d'),
            'created_by' => $assignmentData['created_by'] ?? '',
            'url' => $assignmentData['url'] ?? base_url('/service/area-management')
        ]);
    }
}

if (!function_exists('notify_service_assignment_updated')) {
    /**
     * Send notification when Service Assignment is updated
     * 
     * @param array $assignmentData Assignment data
     * @return bool|array
     */
    function notify_service_assignment_updated($assignmentData)
    {
        return send_notification('service_assignment_updated', [
            'module' => 'service_area',
            'id' => $assignmentData['id'] ?? null,
            'employee_name' => $assignmentData['employee_name'] ?? '',
            'area_name' => $assignmentData['area_name'] ?? '',
            'changes' => $assignmentData['changes'] ?? 'Assignment details updated',
            'updated_by' => $assignmentData['updated_by'] ?? '',
            'url' => $assignmentData['url'] ?? base_url('/service/area-management')
        ]);
    }
}

if (!function_exists('notify_service_assignment_deleted')) {
    /**
     * Send notification when Service Assignment is deleted
     * 
     * @param array $assignmentData Assignment data
     * @return bool|array
     */
    function notify_service_assignment_deleted($assignmentData)
    {
        return send_notification('service_assignment_deleted', [
            'module' => 'service_area',
            'id' => $assignmentData['id'] ?? null,
            'employee_name' => $assignmentData['employee_name'] ?? '',
            'area_name' => $assignmentData['area_name'] ?? '',
            'deleted_by' => $assignmentData['deleted_by'] ?? '',
            'url' => $assignmentData['url'] ?? base_url('/service/area-management')
        ]);
    }
}

// --- UNIT MANAGEMENT NOTIFICATIONS ---

if (!function_exists('notify_unit_location_updated')) {
    /**
     * Send notification when Unit Location is updated
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_unit_location_updated($unitData)
    {
        return send_notification('unit_location_updated', [
            'module' => 'unit_rolling',
            'id' => $unitData['id'] ?? null,
            'unit_code' => $unitData['unit_code'] ?? $unitData['no_unit'] ?? '',
            'old_location' => $unitData['old_location'] ?? '',
            'new_location' => $unitData['new_location'] ?? $unitData['location'] ?? '',
            'updated_by' => $unitData['updated_by'] ?? ''
            // No URL - location updated, informational only
        ]);
    }
}

if (!function_exists('notify_warehouse_unit_updated')) {
    /**
     * Send notification when Warehouse Unit is updated
     * 
     * @param array $unitData Unit data
     * @return bool|array
     */
    function notify_warehouse_unit_updated($unitData)
    {
        return send_notification('warehouse_unit_updated', [
            'module' => 'warehouse',
            'id' => $unitData['id'] ?? null,
            'unit_code' => $unitData['unit_code'] ?? $unitData['no_unit'] ?? '',
            'changes' => $unitData['changes'] ?? 'Unit information updated',
            'updated_by' => $unitData['updated_by'] ?? ''
            // No URL - warehouse unit updated, informational only
        ]);
    }
}

// --- KONTRAK MANAGEMENT NOTIFICATIONS ---

if (!function_exists('notify_contract_created')) {
    /**
     * Send notification when Contract is created
     * 
     * @param array $contractData Contract data
     * @return bool|array
     */
    function notify_contract_created($contractData)
    {
        return send_notification('contract_created', [
            'module' => 'contract',
            'id' => $contractData['id'] ?? null,
            'contract_number' => $contractData['contract_number'] ?? $contractData['no_kontrak'] ?? '',
            'customer_name' => $contractData['customer_name'] ?? '',
            'customer' => $contractData['customer_name'] ?? '',
            'contract_type' => $contractData['contract_type'] ?? $contractData['tipe_kontrak'] ?? '',
            'start_date' => $contractData['start_date'] ?? $contractData['tanggal_mulai'] ?? '',
            'end_date' => $contractData['end_date'] ?? $contractData['tanggal_selesai'] ?? '',
            'total_value' => $contractData['total_value'] ?? $contractData['nilai_total'] ?? 0,
            'created_by' => $contractData['created_by'] ?? ''
            // No URL - contract created, informational only
        ]);
    }
}

if (!function_exists('notify_contract_updated')) {
    /**
     * Send notification when Contract is updated
     * 
     * @param array $contractData Contract data
     * @return bool|array
     */
    function notify_contract_updated($contractData)
    {
        return send_notification('contract_updated', [
            'module' => 'contract',
            'id' => $contractData['id'] ?? null,
            'contract_number' => $contractData['contract_number'] ?? $contractData['no_kontrak'] ?? '',
            'customer_name' => $contractData['customer_name'] ?? '',
            'changes' => $contractData['changes'] ?? 'Contract details updated',
            'updated_by' => $contractData['updated_by'] ?? ''
            // No URL - contract updated, informational only
        ]);
    }
}

if (!function_exists('notify_contract_deleted')) {
    /**
     * Send notification when Contract is deleted
     * 
     * @param array $contractData Contract data
     * @return bool|array
     */
    function notify_contract_deleted($contractData)
    {
        return send_notification('contract_deleted', [
            'module' => 'contract',
            'id' => $contractData['id'] ?? null,
            'contract_number' => $contractData['contract_number'] ?? $contractData['no_kontrak'] ?? '',
            'customer_name' => $contractData['customer_name'] ?? '',
            'deleted_by' => $contractData['deleted_by'] ?? '',
            'deletion_reason' => $contractData['deletion_reason'] ?? 'N/A'
            // No URL - contract deleted, informational only
        ]);
    }
}

// --- USER / PERMISSION MANAGEMENT NOTIFICATIONS ---

if (!function_exists('notify_user_removed_from_division')) {
    /**
     * Send notification when User is removed from Division
     * 
     * @param array $userData User data
     * @return bool|array
     */
    function notify_user_removed_from_division($userData)
    {
        return send_notification('user_removed_from_division', [
            'module' => 'admin',
            'id' => $userData['id'] ?? null,
            'user_name' => $userData['user_name'] ?? $userData['username'] ?? '',
            'division_name' => $userData['division_name'] ?? '',
            'removed_by' => $userData['removed_by'] ?? ''
            // No URL - user removed, informational only
        ]);
    }
}

if (!function_exists('notify_user_permissions_updated')) {
    /**
     * Send notification when User Permissions are updated
     * 
     * @param array $userData User data
     * @return bool|array
     */
    function notify_user_permissions_updated($userData)
    {
        return send_notification('user_permissions_updated', [
            'module' => 'admin',
            'id' => $userData['id'] ?? null,
            'user_name' => $userData['user_name'] ?? $userData['username'] ?? '',
            'permissions_changed' => $userData['permissions_changed'] ?? 'Custom permissions updated',
            'updated_by' => $userData['updated_by'] ?? ''
            // No URL - permissions updated, informational only
        ]);
    }
}

if (!function_exists('notify_permission_created')) {
    /**
     * Send notification when Permission is created
     * 
     * @param array $permissionData Permission data
     * @return bool|array
     */
    function notify_permission_created($permissionData)
    {
        return send_notification('permission_created', [
            'module' => 'admin',
            'id' => $permissionData['id'] ?? null,
            'permission_name' => $permissionData['permission_name'] ?? '',
            'permission_code' => $permissionData['permission_code'] ?? '',
            'module_name' => $permissionData['module_name'] ?? '',
            'created_by' => $permissionData['created_by'] ?? ''
            // No URL - permission created, FYI to admins
        ]);
    }
}

if (!function_exists('notify_role_saved')) {
    /**
     * Send notification when Role is created or updated
     * 
     * @param array $roleData Role data
     * @return bool|array
     */
    function notify_role_saved($roleData)
    {
        return send_notification('role_saved', [
            'module' => 'admin',
            'id' => $roleData['id'] ?? null,
            'role_name' => $roleData['role_name'] ?? '',
            'action' => $roleData['action'] ?? 'saved', // 'created' or 'updated'
            'permissions_count' => $roleData['permissions_count'] ?? 0,
            'saved_by' => $roleData['saved_by'] ?? '',
            'url' => $roleData['url'] ?? base_url('/admin/roles')
        ]);
    }
}

// ==================== PHASE 3: MEDIUM PRIORITY NOTIFICATIONS ====================
// Coverage: Customer (3), Warehouse (3), Operations (4), Finance (3), SPK (2), Marketing (2)
// Total: 17 functions

// --- CATEGORY 1: Customer Management (3 functions) ---

if (!function_exists('notify_customer_created')) {
    /**
     * Send notification when Customer is created
     * 
     * @param array $customerData Customer data
     * @return bool|array
     */
    function notify_customer_created($customerData)
    {
        return send_notification('customer_created', [
            'module' => 'customer',
            'id' => $customerData['id'] ?? null,
            'customer_code' => $customerData['customer_code'] ?? '',
            'customer_name' => $customerData['customer_name'] ?? $customerData['name'] ?? '',
            'customer_type' => $customerData['customer_type'] ?? $customerData['type'] ?? '',
            'phone' => $customerData['phone'] ?? '',
            'email' => $customerData['email'] ?? '',
            'created_by' => $customerData['created_by'] ?? ''
            // No URL - customer created, informational only
        ]);
    }
}

if (!function_exists('notify_customer_updated')) {
    /**
     * Send notification when Customer is updated
     * 
     * @param array $customerData Customer data
     * @return bool|array
     */
    function notify_customer_updated($customerData)
    {
        return send_notification('customer_updated', [
            'module' => 'customer',
            'id' => $customerData['id'] ?? null,
            'customer_code' => $customerData['customer_code'] ?? '',
            'customer_name' => $customerData['customer_name'] ?? $customerData['name'] ?? '',
            'changes' => $customerData['changes'] ?? '',
            'updated_by' => $customerData['updated_by'] ?? ''
            // No URL - customer updated, informational only
        ]);
    }
}

if (!function_exists('notify_customer_status_changed')) {
    /**
     * Send notification when Customer status changes
     * 
     * @param array $customerData Customer data
     * @return bool|array
     */
    function notify_customer_status_changed($customerData)
    {
        return send_notification('customer_status_changed', [
            'module' => 'customer',
            'id' => $customerData['id'] ?? null,
            'customer_code' => $customerData['customer_code'] ?? '',
            'customer_name' => $customerData['customer_name'] ?? $customerData['name'] ?? '',
            'old_status' => $customerData['old_status'] ?? '',
            'new_status' => $customerData['new_status'] ?? '',
            'reason' => $customerData['reason'] ?? '',
            'changed_by' => $customerData['changed_by'] ?? ''
            // No URL - status changed, informational only
        ]);
    }
}

// --- CATEGORY 2: Warehouse Extended (3 functions) ---

if (!function_exists('notify_warehouse_stock_alert')) {
    /**
     * Send notification when Warehouse stock reaches minimum threshold
     * 
     * @param array $stockData Stock data
     * @return bool|array
     */
    function notify_warehouse_stock_alert($stockData)
    {
        return send_notification('warehouse_stock_alert', [
            'module' => 'warehouse',
            'item_id' => $stockData['item_id'] ?? null,
            'item_name' => $stockData['item_name'] ?? '',
            'current_stock' => $stockData['current_stock'] ?? 0,
            'minimum_stock' => $stockData['minimum_stock'] ?? 0,
            'warehouse_name' => $stockData['warehouse_name'] ?? '',
            'unit' => $stockData['unit'] ?? '',
            'url' => $stockData['url'] ?? base_url('/warehouse/stock-report')
        ]);
    }
}

if (!function_exists('notify_warehouse_transfer_completed')) {
    /**
     * Send notification when Warehouse transfer is completed
     * 
     * @param array $transferData Transfer data
     * @return bool|array
     */
    function notify_warehouse_transfer_completed($transferData)
    {
        return send_notification('warehouse_transfer_completed', [
            'module' => 'warehouse',
            'transfer_id' => $transferData['transfer_id'] ?? null,
            'transfer_code' => $transferData['transfer_code'] ?? '',
            'from_warehouse' => $transferData['from_warehouse'] ?? '',
            'to_warehouse' => $transferData['to_warehouse'] ?? '',
            'item_count' => $transferData['item_count'] ?? 0,
            'completed_by' => $transferData['completed_by'] ?? '',
            'completed_at' => $transferData['completed_at'] ?? date('Y-m-d H:i:s')
            // No URL - transfer completed, informational only
        ]);
    }
}

if (!function_exists('notify_warehouse_stocktake_completed')) {
    /**
     * Send notification when Warehouse stocktake is completed
     * 
     * @param array $stocktakeData Stocktake data
     * @return bool|array
     */
    function notify_warehouse_stocktake_completed($stocktakeData)
    {
        return send_notification('warehouse_stocktake_completed', [
            'module' => 'warehouse',
            'stocktake_id' => $stocktakeData['stocktake_id'] ?? null,
            'stocktake_code' => $stocktakeData['stocktake_code'] ?? '',
            'warehouse_name' => $stocktakeData['warehouse_name'] ?? '',
            'items_counted' => $stocktakeData['items_counted'] ?? 0,
            'discrepancies' => $stocktakeData['discrepancies'] ?? 0,
            'completed_by' => $stocktakeData['completed_by'] ?? '',
            'completed_at' => $stocktakeData['completed_at'] ?? date('Y-m-d H:i:s')
            // No URL - stocktake completed, informational only
        ]);
    }
}

// --- CATEGORY 3: Operational Workflows (4 functions) ---

if (!function_exists('notify_inspection_scheduled')) {
    /**
     * Send notification when Unit inspection is scheduled
     * 
     * @param array $inspectionData Inspection data
     * @return bool|array
     */
    function notify_inspection_scheduled($inspectionData)
    {
        return send_notification('inspection_scheduled', [
            'module' => 'operations',
            'inspection_id' => $inspectionData['inspection_id'] ?? null,
            'unit_code' => $inspectionData['unit_code'] ?? '',
            'inspection_type' => $inspectionData['inspection_type'] ?? '',
            'scheduled_date' => $inspectionData['scheduled_date'] ?? '',
            'assigned_to' => $inspectionData['assigned_to'] ?? '',
            'priority' => $inspectionData['priority'] ?? 'normal',
            'url' => $inspectionData['url'] ?? base_url('/operations/inspections')
        ]);
    }
}

if (!function_exists('notify_inspection_completed')) {
    /**
     * Send notification when Unit inspection is completed
     * 
     * @param array $inspectionData Inspection data
     * @return bool|array
     */
    function notify_inspection_completed($inspectionData)
    {
        return send_notification('inspection_completed', [
            'module' => 'operations',
            'inspection_id' => $inspectionData['inspection_id'] ?? null,
            'unit_code' => $inspectionData['unit_code'] ?? '',
            'inspection_type' => $inspectionData['inspection_type'] ?? '',
            'result' => $inspectionData['result'] ?? '',
            'findings_count' => $inspectionData['findings_count'] ?? 0,
            'completed_by' => $inspectionData['completed_by'] ?? '',
            'completed_at' => $inspectionData['completed_at'] ?? date('Y-m-d H:i:s')
            // No URL - inspection completed, informational only
        ]);
    }
}

if (!function_exists('notify_maintenance_scheduled')) {
    /**
     * Send notification when Unit maintenance is scheduled
     * 
     * @param array $maintenanceData Maintenance data
     * @return bool|array
     */
    function notify_maintenance_scheduled($maintenanceData)
    {
        return send_notification('maintenance_scheduled', [
            'module' => 'operations',
            'maintenance_id' => $maintenanceData['maintenance_id'] ?? null,
            'unit_code' => $maintenanceData['unit_code'] ?? '',
            'maintenance_type' => $maintenanceData['maintenance_type'] ?? '',
            'scheduled_date' => $maintenanceData['scheduled_date'] ?? '',
            'estimated_hours' => $maintenanceData['estimated_hours'] ?? 0,
            'assigned_mechanic' => $maintenanceData['assigned_mechanic'] ?? '',
            'priority' => $maintenanceData['priority'] ?? 'normal',
            'url' => $maintenanceData['url'] ?? base_url('/operations/maintenance')
        ]);
    }
}

if (!function_exists('notify_maintenance_completed')) {
    /**
     * Send notification when Unit maintenance is completed
     * 
     * @param array $maintenanceData Maintenance data
     * @return bool|array
     */
    function notify_maintenance_completed($maintenanceData)
    {
        return send_notification('maintenance_completed', [
            'module' => 'operations',
            'maintenance_id' => $maintenanceData['maintenance_id'] ?? null,
            'unit_code' => $maintenanceData['unit_code'] ?? '',
            'maintenance_type' => $maintenanceData['maintenance_type'] ?? '',
            'actual_hours' => $maintenanceData['actual_hours'] ?? 0,
            'parts_replaced' => $maintenanceData['parts_replaced'] ?? 0,
            'total_cost' => $maintenanceData['total_cost'] ?? 0,
            'completed_by' => $maintenanceData['completed_by'] ?? '',
            'completed_at' => $maintenanceData['completed_at'] ?? date('Y-m-d H:i:s'),
            'url' => $maintenanceData['url'] ?? base_url('/operations/maintenance/view/' . ($maintenanceData['maintenance_id'] ?? ''))
        ]);
    }
}

// --- CATEGORY 4: Finance Extended (3 functions) ---

if (!function_exists('notify_payment_received')) {
    /**
     * Send notification when Payment is received
     * 
     * @param array $paymentData Payment data
     * @return bool|array
     */
    function notify_payment_received($paymentData)
    {
        return send_notification('payment_received', [
            'module' => 'finance',
            'payment_id' => $paymentData['payment_id'] ?? null,
            'invoice_number' => $paymentData['invoice_number'] ?? '',
            'customer_name' => $paymentData['customer_name'] ?? '',
            'amount' => $paymentData['amount'] ?? 0,
            'payment_method' => $paymentData['payment_method'] ?? '',
            'received_by' => $paymentData['received_by'] ?? '',
            'received_at' => $paymentData['received_at'] ?? date('Y-m-d H:i:s')
            // No URL - payment received, informational only
        ]);
    }
}

if (!function_exists('notify_payment_overdue')) {
    /**
     * Send notification when Payment becomes overdue
     * 
     * @param array $invoiceData Invoice data
     * @return bool|array
     */
    function notify_payment_overdue($invoiceData)
    {
        return send_notification('payment_overdue', [
            'module' => 'finance',
            'invoice_id' => $invoiceData['invoice_id'] ?? null,
            'invoice_number' => $invoiceData['invoice_number'] ?? '',
            'customer_name' => $invoiceData['customer_name'] ?? '',
            'amount' => $invoiceData['amount'] ?? 0,
            'due_date' => $invoiceData['due_date'] ?? '',
            'days_overdue' => $invoiceData['days_overdue'] ?? 0,
            'outstanding_balance' => $invoiceData['outstanding_balance'] ?? 0,
            'url' => $invoiceData['url'] ?? base_url('/finance/invoices/view/' . ($invoiceData['invoice_id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_budget_threshold_exceeded')) {
    /**
     * Send notification when Budget threshold is exceeded
     * 
     * @param array $budgetData Budget data
     * @return bool|array
     */
    function notify_budget_threshold_exceeded($budgetData)
    {
        return send_notification('budget_threshold_exceeded', [
            'module' => 'finance',
            'budget_id' => $budgetData['budget_id'] ?? null,
            'budget_name' => $budgetData['budget_name'] ?? '',
            'department' => $budgetData['department'] ?? '',
            'allocated_amount' => $budgetData['allocated_amount'] ?? 0,
            'spent_amount' => $budgetData['spent_amount'] ?? 0,
            'percentage_used' => $budgetData['percentage_used'] ?? 0,
            'threshold' => $budgetData['threshold'] ?? 90
            // No URL - budget threshold alert, informational only
        ]);
    }
}

// --- CATEGORY 5: SPK Management (2 functions) ---

if (!function_exists('notify_spk_created')) {
    /**
     * Send notification when SPK (Surat Perintah Kerja) is created
     * 
     * @param array $spkData SPK data
     * @return bool|array
     */
    function notify_spk_created($spkData)
    {
        return send_notification('spk_created', [
            'module' => 'spk',
            'spk_id' => $spkData['spk_id'] ?? null,
            'spk_number' => $spkData['spk_number'] ?? '',
            'unit_code' => $spkData['unit_code'] ?? '',
            'work_type' => $spkData['work_type'] ?? '',
            'assigned_to' => $spkData['assigned_to'] ?? '',
            'target_date' => $spkData['target_date'] ?? '',
            'priority' => $spkData['priority'] ?? 'normal',
            'created_by' => $spkData['created_by'] ?? '',
            'url' => $spkData['url'] ?? base_url('/spk/view/' . ($spkData['spk_id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_spk_completed')) {
    /**
     * Send notification when SPK is completed
     * 
     * @param array $spkData SPK data
     * @return bool|array
     */
    function notify_spk_completed($spkData)
    {
        return send_notification('spk_completed', [
            'module' => 'spk',
            'spk_id' => $spkData['spk_id'] ?? null,
            'departemen' => $spkData['departemen'] ?? session('division') ?? 'Marketing',
            'spk_number' => $spkData['spk_number'] ?? '',
            'nomor_spk' => $spkData['nomor_spk'] ?? $spkData['spk_number'] ?? '',
            'pelanggan' => $spkData['pelanggan'] ?? $spkData['customer_name'] ?? '',
            'unit_code' => $spkData['unit_code'] ?? '',
            'no_unit' => $spkData['no_unit'] ?? $spkData['unit_code'] ?? '',
            'work_type' => $spkData['work_type'] ?? '',
            'actual_duration' => $spkData['actual_duration'] ?? 0,
            'result' => $spkData['result'] ?? '',
            'completed_by' => $spkData['completed_by'] ?? '',
            'completed_at' => $spkData['completed_at'] ?? date('Y-m-d H:i:s')
            // No URL - SPK completed, informational only
        ]);
    }
}

// --- CATEGORY 6: Additional Marketing (2 functions) ---

if (!function_exists('notify_quotation_sent_to_customer')) {
    /**
     * Send notification when Quotation is sent to customer
     * 
     * @param array $quotationData Quotation data
     * @return bool|array
     */
    function notify_quotation_sent_to_customer($quotationData)
    {
        return send_notification('quotation_sent_to_customer', [
            'module' => 'marketing',
            'id' => $quotationData['id'] ?? null,
            'quote_number' => $quotationData['quote_number'] ?? '',
            'customer_name' => $quotationData['customer_name'] ?? '',
            'customer_email' => $quotationData['customer_email'] ?? '',
            'sent_method' => $quotationData['sent_method'] ?? 'email',
            'sent_by' => $quotationData['sent_by'] ?? '',
            'sent_at' => $quotationData['sent_at'] ?? date('Y-m-d H:i:s')
            // No URL - quotation sent, informational only
        ]);
    }
}

if (!function_exists('notify_quotation_follow_up_required')) {
    /**
     * Send notification when Quotation requires follow-up
     * 
     * @param array $quotationData Quotation data
     * @return bool|array
     */
    function notify_quotation_follow_up_required($quotationData)
    {
        return send_notification('quotation_follow_up_required', [
            'module' => 'marketing',
            'id' => $quotationData['id'] ?? null,
            'quote_number' => $quotationData['quote_number'] ?? '',
            'customer_name' => $quotationData['customer_name'] ?? '',
            'days_since_sent' => $quotationData['days_since_sent'] ?? 0,
            'last_contact' => $quotationData['last_contact'] ?? '',
            'assigned_to' => $quotationData['assigned_to'] ?? '',
            'follow_up_priority' => $quotationData['follow_up_priority'] ?? 'normal',
            'url' => $quotationData['url'] ?? base_url('/marketing/quotations/view/' . ($quotationData['id'] ?? ''))
        ]);
    }
}

// ============================================================================
// SPK STAGE NOTIFICATIONS - Cross-Division Workflow
// ============================================================================

if (!function_exists('notify_spk_unit_prep_completed')) {
    /**
     * Send notification when SPK Unit Preparation stage is completed
     * Targets: Marketing (success notice), Warehouse (items report)
     * 
     * @param array $spkData SPK stage completion data
     * @return bool|array
     */
    function notify_spk_unit_prep_completed($spkData)
    {
        return send_notification('spk_unit_prep_completed', [
            'module' => 'service',
            'spk_id' => $spkData['spk_id'] ?? null,
            'spk_number' => $spkData['spk_number'] ?? '',
            // Some templates use {{nomor_spk}}, so provide alias too.
            'nomor_spk' => $spkData['nomor_spk'] ?? $spkData['spk_number'] ?? '',
            'stage' => 'persiapan_unit',
            'pelanggan' => $spkData['pelanggan'] ?? '',
            'lokasi' => $spkData['lokasi'] ?? '',
            'approved_by' => $spkData['approved_by'] ?? '',
            'approved_at' => $spkData['approved_at'] ?? date('Y-m-d H:i:s'),
            'unit_info' => $spkData['unit_info'] ?? '',
            'items_prepared' => $spkData['items_prepared'] ?? ''
            // No URL - stage completed, informational only
        ]);
    }
}

if (!function_exists('notify_spk_fabrication_completed')) {
    /**
     * Send notification when SPK Fabrication stage is completed
     * Targets: Marketing (success notice), Warehouse (attachment report)
     * 
     * @param array $spkData SPK stage completion data
     * @return bool|array
     */
    function notify_spk_fabrication_completed($spkData)
    {
        return send_notification('spk_fabrication_completed', [
            'module' => 'service',
            'spk_id' => $spkData['spk_id'] ?? null,
            'spk_number' => $spkData['spk_number'] ?? '',
            // Some templates use {{nomor_spk}}, so provide alias too.
            'nomor_spk' => $spkData['nomor_spk'] ?? $spkData['spk_number'] ?? '',
            'stage' => 'fabrikasi',
            'pelanggan' => $spkData['pelanggan'] ?? '',
            'lokasi' => $spkData['lokasi'] ?? '',
            'approved_by' => $spkData['approved_by'] ?? '',
            'approved_at' => $spkData['approved_at'] ?? date('Y-m-d H:i:s'),
            'attachment_info' => $spkData['attachment_info'] ?? '',
            'fabrication_notes' => $spkData['fabrication_notes'] ?? ''
            // No URL - fabrication completed, informational only
        ]);
    }
}

if (!function_exists('notify_spk_pdi_completed')) {
    /**
     * Send notification when SPK PDI stage is completed - Unit becomes READY
     * Targets: Marketing (SPK ready notice), Operational (ready for DI creation)
     * 
     * @param array $spkData SPK stage completion data
     * @return bool|array
     */
    function notify_spk_pdi_completed($spkData)
    {
        return send_notification('spk_pdi_completed', [
            'module' => 'service',
            'spk_id' => $spkData['spk_id'] ?? null,
            'spk_number' => $spkData['spk_number'] ?? '',
            // Some templates use {{nomor_spk}}, so provide alias too.
            'nomor_spk' => $spkData['nomor_spk'] ?? $spkData['spk_number'] ?? '',
            'stage' => 'pdi',
            'pelanggan' => $spkData['pelanggan'] ?? '',
            'lokasi' => $spkData['lokasi'] ?? '',
            'approved_by' => $spkData['approved_by'] ?? '',
            'approved_at' => $spkData['approved_at'] ?? date('Y-m-d H:i:s'),
            'spk_status' => 'READY',
            'ready_for_delivery' => true,
            'pdi_results' => $spkData['pdi_results'] ?? ''
            // No URL - PDI completed, informational only
        ]);
    }
}

// ============================================================================
// ATTACHMENT NOTIFICATIONS - Warehouse-Service Cross-Division
// ============================================================================

if (!function_exists('notify_attachment_added')) {
    /**
     * Send notification when new attachment is added to inventory
     * Targets: Warehouse (new inventory item)
     * 
     * @param array $attachmentData Attachment data
     * @return bool|array
     */
    function notify_attachment_added($attachmentData)
    {
        return send_notification('attachment_added', [
            'module' => 'inventory',
            'attachment_id' => $attachmentData['attachment_id'] ?? null,
            'tipe_item' => $attachmentData['tipe_item'] ?? '',
            'merk' => $attachmentData['merk'] ?? '',
            'model' => $attachmentData['model'] ?? '',
            'serial_number' => $attachmentData['serial_number'] ?? '',
            'kondisi' => $attachmentData['kondisi'] ?? 'Baik',
            'lokasi' => $attachmentData['lokasi'] ?? 'Workshop',
            'added_by' => $attachmentData['added_by'] ?? '',
            'added_at' => $attachmentData['added_at'] ?? date('Y-m-d H:i:s'),
            'url' => $attachmentData['url'] ?? base_url('/warehouse/inventory/get-attachment-detail/' . ($attachmentData['attachment_id'] ?? ''))
        ]);
    }
}

if (!function_exists('notify_attachment_attached')) {
    /**
     * Send notification when attachment is attached to a unit
     * Targets: Service (unit configuration changed)
     * 
     * @param array $attachmentData Attachment operation data
     * @return bool|array
     */
    function notify_attachment_attached($attachmentData)
    {
        return send_notification('attachment_attached', [
            'module' => 'inventory',
            'attachment_id' => $attachmentData['attachment_id'] ?? null,
            'unit_id' => $attachmentData['unit_id'] ?? null,
            'no_unit' => $attachmentData['unit_number'] ?? $attachmentData['no_unit'] ?? '',
            'tipe_item' => $attachmentData['tipe_item'] ?? '',
            'attachment_info' => $attachmentData['attachment_info'] ?? '',
            'performed_by' => $attachmentData['performed_by'] ?? '',
            'performed_at' => $attachmentData['performed_at'] ?? date('Y-m-d H:i:s'),
            'notes' => $attachmentData['notes'] ?? ''
            // No URL - attachment attached, informational only
        ]);
    }
}

if (!function_exists('notify_attachment_detached')) {
    /**
     * Send notification when attachment is detached from a unit
     * Targets: Service (unit configuration changed)
     * 
     * @param array $attachmentData Attachment operation data
     * @return bool|array
     */
    function notify_attachment_detached($attachmentData)
    {
        return send_notification('attachment_detached', [
            'module' => 'inventory',
            'attachment_id' => $attachmentData['attachment_id'] ?? null,
            'unit_id' => $attachmentData['unit_id'] ?? null,
            'no_unit' => $attachmentData['unit_number'] ?? $attachmentData['no_unit'] ?? '',
            'tipe_item' => $attachmentData['tipe_item'] ?? '',
            'attachment_info' => $attachmentData['attachment_info'] ?? '',
            'reason' => $attachmentData['reason'] ?? '',
            'new_location' => $attachmentData['new_location'] ?? 'Workshop',
            'performed_by' => $attachmentData['performed_by'] ?? '',
            'performed_at' => $attachmentData['performed_at'] ?? date('Y-m-d H:i:s')
            // No URL - attachment detached, informational only
        ]);
    }
}

if (!function_exists('notify_attachment_swapped')) {
    /**
     * Send notification when attachment is swapped between units
     * Targets: Service (unit configurations changed)
     * 
     * @param array $attachmentData Attachment operation data
     * @return bool|array
     */
    function notify_attachment_swapped($attachmentData)
    {
        return send_notification('attachment_swapped', [
            'module' => 'inventory',
            'attachment_id' => $attachmentData['attachment_id'] ?? null,
            'from_unit_id' => $attachmentData['from_unit_id'] ?? null,
            'from_unit_number' => $attachmentData['from_unit_number'] ?? '',
            'to_unit_id' => $attachmentData['to_unit_id'] ?? null,
            'to_unit_number' => $attachmentData['to_unit_number'] ?? '',
            'tipe_item' => $attachmentData['tipe_item'] ?? '',
            'attachment_info' => $attachmentData['attachment_info'] ?? '',
            'reason' => $attachmentData['reason'] ?? '',
            'performed_by' => $attachmentData['performed_by'] ?? '',
            'performed_at' => $attachmentData['performed_at'] ?? date('Y-m-d H:i:s')
            // No URL - attachment swapped, informational only
        ]);
    }
}

// ============================================================================
// SPAREPART & PO DELIVERY NOTIFICATIONS
// ============================================================================

if (!function_exists('notify_sparepart_returned')) {
    /**
     * Send notification when sparepart return is confirmed by warehouse
     * Targets: Service (sparepart availability updated)
     * 
     * @param array $returnData Sparepart return data
     * @return bool|array
     */
    function notify_sparepart_returned($returnData)
    {
        return send_notification('sparepart_returned', [
            'module' => 'warehouse',
            'return_id' => $returnData['return_id'] ?? null,
            'sparepart_id' => $returnData['sparepart_id'] ?? null,
            'sparepart_name' => $returnData['sparepart_name'] ?? '',
            'quantity' => $returnData['quantity'] ?? 0,
            'condition' => $returnData['condition'] ?? 'Baik',
            'returned_by' => $returnData['returned_by'] ?? '',
            'returned_from' => $returnData['returned_from'] ?? '',
            'confirmed_by' => $returnData['confirmed_by'] ?? '',
            'confirmed_at' => $returnData['confirmed_at'] ?? date('Y-m-d H:i:s'),
            'notes' => $returnData['notes'] ?? ''
            // No URL - sparepart returned, informational only
        ]);
    }
}

if (!function_exists('notify_po_delivery_created')) {
    /**
     * Send notification when PO delivery schedule is created
     * Targets: Warehouse (prepare for receiving), Purchasing (tracking)
     * 
     * @param array $deliveryData PO delivery data
     * @return bool|array
     */
    function notify_po_delivery_created($deliveryData)
    {
        return send_notification('po_delivery_created', [
            'module' => 'purchasing',
            'delivery_id' => $deliveryData['delivery_id'] ?? null,
            'po_id' => $deliveryData['po_id'] ?? null,
            'po_number' => $deliveryData['po_number'] ?? '',
            'supplier_name' => $deliveryData['supplier_name'] ?? '',
            'delivery_date' => $deliveryData['delivery_date'] ?? '',
            'delivery_type' => $deliveryData['delivery_type'] ?? '',
            'item_count' => $deliveryData['item_count'] ?? 0,
            'total_quantity' => $deliveryData['total_quantity'] ?? 0,
            'created_by' => $deliveryData['created_by'] ?? '',
            'created_at' => $deliveryData['created_at'] ?? date('Y-m-d H:i:s'),
            'notes' => $deliveryData['notes'] ?? ''
            // No URL - delivery schedule created, informational only
        ]);
    }
}

// ============================================================================
// SCHEDULED NOTIFICATION CHECKS (Background Tasks)
// ============================================================================

if (!function_exists('check_contract_expiry_scheduled')) {
    /**
     * Check for contracts expiring soon (30 days warning)
     * Runs every 24 hours via pseudo-CRON
     * 
     * @return array Result with counts
     */
    function check_contract_expiry_scheduled()
    {
        try {
            $db = \Config\Database::connect();
            
            // Check last run time from cache
            $cache = \Config\Services::cache();
            $lastRun = $cache->get('contract_expiry_last_check');
            
            // Only run once per 24 hours
            if ($lastRun && (time() - $lastRun) < 86400) {
                log_message('info', '[Scheduler] Contract expiry check skipped - last run: ' . date('Y-m-d H:i:s', $lastRun));
                return ['status' => 'skipped', 'reason' => 'checked_recently'];
            }
            
            // Find contracts expiring in next 30 days
            $expiringContracts = $db->table('kontrak k')
                ->select('k.*, c.customer_name, (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as location_name')
                ->join('customers c', 'c.id = k.customer_id', 'left')
                ->where('k.status', 'ACTIVE')
                ->where('k.tanggal_berakhir >=', date('Y-m-d'))
                ->where('k.tanggal_berakhir <=', date('Y-m-d', strtotime('+30 days')))
                ->get()
                ->getResultArray();
            
            $notificationsSent = 0;
            
            foreach ($expiringContracts as $contract) {
                // Calculate days until expiry
                $expiryDate = new \DateTime($contract['tanggal_berakhir']);
                $today = new \DateTime();
                $daysUntilExpiry = $today->diff($expiryDate)->days;
                
                // Send notification
                notify_customer_contract_expired([
                    'id' => $contract['id'],
                    'contract_number' => $contract['no_kontrak'],
                    'customer_name' => $contract['customer_name'] ?? 'Unknown',
                    'location_name' => $contract['location_name'] ?? '',
                    'expiry_date' => $contract['tanggal_berakhir'],
                    'days_until_expiry' => $daysUntilExpiry,
                    'contract_value' => $contract['nilai_total'] ?? 0,
                    'url' => base_url('/marketing/contracts/view/' . $contract['id'])
                ]);
                
                $notificationsSent++;
            }
            
            // Update last run time
            $cache->save('contract_expiry_last_check', time(), 86400);
            
            log_message('info', '[Scheduler] Contract expiry check completed - ' . $notificationsSent . ' notifications sent for ' . count($expiringContracts) . ' expiring contracts');
            
            return [
                'status' => 'success',
                'contracts_checked' => count($expiringContracts),
                'notifications_sent' => $notificationsSent,
                'next_run' => date('Y-m-d H:i:s', time() + 86400)
            ];
            
        } catch (\Exception $e) {
            log_message('error', '[Scheduler] Contract expiry check failed: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('notify_work_order_unit_verified')) {
    /**
     * Send notification when Work Order Unit Verification has data changes
     * 
     * @param array $data Verification data
     * @return bool|array
     */
    function notify_work_order_unit_verified($data)
    {
        return send_notification('work_order_unit_verified', [
            'module' => 'work_order',
            'id' => $data['work_order_id'] ?? null,
            'wo_number' => $data['wo_number'] ?? '',
            'unit_code' => $data['unit_code'] ?? '',
            'unit_no' => $data['unit_code'] ?? $data['unit_no'] ?? '',
            'changes_count' => $data['changes_count'] ?? 0,
            'changes_list' => $data['changes_list'] ?? '',
            'created_by' => $data['created_by'] ?? 'System',
            'verified_at' => $data['verified_at'] ?? date('Y-m-d H:i:s'),
            'url' => $data['url'] ?? base_url('/service/work-orders/view/' . ($data['work_order_id'] ?? ''))
        ]);
    }
}
