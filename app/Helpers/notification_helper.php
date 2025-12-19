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
            'phone' => $customerData['phone'] ?? '',
            'url' => base_url('/marketing/customer-management')
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
            'customer_code' => $customerData['customer_code'] ?? '',
            'url' => base_url('/marketing/customer-management')
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
            'customer_code' => $customerData['customer_code'] ?? '',
            'url' => base_url('/marketing/customer-management')
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
            'address' => $locationData['address'] ?? '',
            'url' => base_url('/marketing/customer-management')
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
            'tanggal_selesai' => $contractData['tanggal_selesai'] ?? '',
            'url' => base_url('/marketing/customer-management')
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
            'uploaded_by' => $attachmentData['uploaded_by'] ?? '',
            'url' => $attachmentData['url'] ?? base_url('/service/spk_service')
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
            'customer_name' => $invoiceData['customer_name'] ?? '',
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
            'module' => 'purchasing',
            'id' => $deliveryData['id'] ?? null,
            'delivery_number' => $deliveryData['delivery_number'] ?? $deliveryData['nomor_surat_jalan'] ?? '',
            'po_number' => $deliveryData['po_number'] ?? $deliveryData['nomor_po'] ?? '',
            'supplier_name' => $deliveryData['supplier_name'] ?? '',
            'delivery_date' => $deliveryData['delivery_date'] ?? $deliveryData['tanggal_kirim'] ?? '',
            'items_count' => $deliveryData['items_count'] ?? 0,
            'created_by' => $deliveryData['created_by'] ?? '',
            'url' => $deliveryData['url'] ?? base_url('/purchasing/deliveries')
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
            'url' => $deliveryData['url'] ?? base_url('/purchasing/deliveries')
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
            'completed_by' => $contractData['completed_by'] ?? '',
            'url' => $contractData['url'] ?? base_url('/marketing/contracts')
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
            'updated_by' => $unitData['updated_by'] ?? '',
            'url' => $unitData['url'] ?? base_url('/operational/unit-rolling')
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
            'updated_by' => $unitData['updated_by'] ?? '',
            'url' => $unitData['url'] ?? base_url('/warehouse/units')
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
            'contract_type' => $contractData['contract_type'] ?? $contractData['tipe_kontrak'] ?? '',
            'start_date' => $contractData['start_date'] ?? $contractData['tanggal_mulai'] ?? '',
            'end_date' => $contractData['end_date'] ?? $contractData['tanggal_selesai'] ?? '',
            'total_value' => $contractData['total_value'] ?? $contractData['nilai_total'] ?? 0,
            'created_by' => $contractData['created_by'] ?? '',
            'url' => $contractData['url'] ?? base_url('/marketing/contracts')
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
            'updated_by' => $contractData['updated_by'] ?? '',
            'url' => $contractData['url'] ?? base_url('/marketing/contracts')
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
            'deletion_reason' => $contractData['deletion_reason'] ?? 'N/A',
            'url' => $contractData['url'] ?? base_url('/marketing/contracts')
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
            'removed_by' => $userData['removed_by'] ?? '',
            'url' => $userData['url'] ?? base_url('/admin/user-management')
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
            'updated_by' => $userData['updated_by'] ?? '',
            'url' => $userData['url'] ?? base_url('/admin/user-management')
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
            'created_by' => $permissionData['created_by'] ?? '',
            'url' => $permissionData['url'] ?? base_url('/admin/permissions')
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
            'created_by' => $customerData['created_by'] ?? '',
            'url' => $customerData['url'] ?? base_url('/customers/view/' . ($customerData['id'] ?? ''))
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
            'updated_by' => $customerData['updated_by'] ?? '',
            'url' => $customerData['url'] ?? base_url('/customers/view/' . ($customerData['id'] ?? ''))
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
            'changed_by' => $customerData['changed_by'] ?? '',
            'url' => $customerData['url'] ?? base_url('/customers/view/' . ($customerData['id'] ?? ''))
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
            'completed_at' => $transferData['completed_at'] ?? date('Y-m-d H:i:s'),
            'url' => $transferData['url'] ?? base_url('/warehouse/transfers')
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
            'completed_at' => $stocktakeData['completed_at'] ?? date('Y-m-d H:i:s'),
            'url' => $stocktakeData['url'] ?? base_url('/warehouse/stocktakes')
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
            'completed_at' => $inspectionData['completed_at'] ?? date('Y-m-d H:i:s'),
            'url' => $inspectionData['url'] ?? base_url('/operations/inspections/view/' . ($inspectionData['inspection_id'] ?? ''))
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
            'received_at' => $paymentData['received_at'] ?? date('Y-m-d H:i:s'),
            'url' => $paymentData['url'] ?? base_url('/finance/payments')
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
            'threshold' => $budgetData['threshold'] ?? 90,
            'url' => $budgetData['url'] ?? base_url('/finance/budgets')
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
            'spk_number' => $spkData['spk_number'] ?? '',
            'unit_code' => $spkData['unit_code'] ?? '',
            'work_type' => $spkData['work_type'] ?? '',
            'actual_duration' => $spkData['actual_duration'] ?? 0,
            'result' => $spkData['result'] ?? '',
            'completed_by' => $spkData['completed_by'] ?? '',
            'completed_at' => $spkData['completed_at'] ?? date('Y-m-d H:i:s'),
            'url' => $spkData['url'] ?? base_url('/spk/view/' . ($spkData['spk_id'] ?? ''))
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
            'sent_at' => $quotationData['sent_at'] ?? date('Y-m-d H:i:s'),
            'url' => $quotationData['url'] ?? base_url('/marketing/quotations/view/' . ($quotationData['id'] ?? ''))
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
