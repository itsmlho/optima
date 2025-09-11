<?php

/**
 * Notification Integration Example for SPK Workflow
 * 
 * This file demonstrates how to integrate our sophisticated notification system
 * into existing workflow controllers like SPK management.
 * 
 * This shows integration patterns that can be applied to:
 * - SPK creation, status updates, approvals
 * - DI processing workflow
 * - Inventory changes and alerts
 * - Any workflow that needs targeted notifications
 */

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

/**
 * Example: Enhanced Service Controller with Notification Integration
 * 
 * This demonstrates how to add notification hooks to existing SPK workflow methods.
 * The pattern can be applied to any controller that manages workflow events.
 */
class ServiceWithNotifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Enhanced SPK Status Update with Smart Notification Integration
     * 
     * This shows how to add notification hooks to existing methods
     * without breaking current functionality.
     */
    public function spkUpdateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $status = $this->request->getPost('status');
        if (!$status) {
            return $this->response->setJSON(['success'=>false,'message'=>'Status tidak boleh kosong']);
        }

        // Get SPK data before update for notification context
        $oldSpk = $this->db->table('spk')
            ->select('spk.*, c.nama_perusahaan, u.username as created_by_name')
            ->join('customers c', 'spk.customer_id = c.id', 'left')
            ->join('users u', 'spk.created_by = u.id', 'left')
            ->where('spk.id', $id)
            ->get()
            ->getRowArray();

        if (!$oldSpk) {
            return $this->response->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']);
        }

        // Update SPK status
        $this->db->table('spk')->where('id', $id)->update([
            'status' => $status,
            'diperbarui_pada' => date('Y-m-d H:i:s')
        ]);

        // Log the update using existing trait
        $this->logUpdate('spk', $id, $oldSpk, ['status' => $status], [
            'spk_id' => $id,
            'old_status' => $oldSpk['status'] ?? null,
            'new_status' => $status
        ]);

        // ===== NOTIFICATION INTEGRATION EXAMPLE =====
        // Send targeted notifications based on SPK status change
        $this->sendSPKStatusNotification($id, $oldSpk, $status);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Status SPK berhasil diperbarui',
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * Smart SPK Status Change Notification System
     * 
     * This method demonstrates sophisticated notification targeting based on:
     * - SPK department (diesel, forklift, etc.)
     * - Target division (service, marketing, finance)
     * - Status transition type
     * - Customer importance level
     */
    private function sendSPKStatusNotification($spkId, $spkData, $newStatus)
    {
        try {
            // Prepare notification context with rich data
            $context = [
                'spk_id' => $spkId,
                'nomor_spk' => $spkData['nomor_spk'] ?? "SPK#{$spkId}",
                'old_status' => $spkData['status'] ?? 'UNKNOWN',
                'new_status' => $newStatus,
                'customer_name' => $spkData['nama_perusahaan'] ?? 'Unknown Customer',
                'departemen' => $spkData['departemen'] ?? 'general',
                'created_by' => $spkData['created_by_name'] ?? 'System',
                'priority' => $this->determineSPKPriority($spkData),
                'timestamp' => date('Y-m-d H:i:s'),
                'status_change' => $spkData['status'] . ' → ' . $newStatus
            ];

            // Send notifications based on status transition
            switch ($newStatus) {
                case 'SUBMITTED':
                    // Notify service division when SPK is submitted
                    $this->notificationModel->sendByRule('spk_submitted', $context);
                    break;

                case 'IN_PROGRESS':
                    // Notify relevant departments and management
                    $this->notificationModel->sendByRule('spk_started', $context);
                    
                    // Special notification for high-priority customers
                    if ($context['priority'] === 'high') {
                        $this->notificationModel->sendByRule('spk_priority_started', $context);
                    }
                    break;

                case 'READY':
                    // Notify marketing for delivery coordination
                    // Notify customer service for customer communication
                    $this->notificationModel->sendByRule('spk_ready', $context);
                    break;

                case 'DELIVERED':
                    // Notify finance for billing
                    // Notify management for completion tracking
                    $this->notificationModel->sendByRule('spk_delivered', $context);
                    break;

                case 'CANCELLED':
                    // Notify all stakeholders about cancellation
                    $this->notificationModel->sendByRule('spk_cancelled', $context);
                    break;
            }

            // Department-specific notifications
            if ($spkData['departemen'] === 'diesel') {
                // Send to Service DIESEL team specifically
                $context['target_department'] = 'diesel';
                $context['target_division'] = 'service';
                $this->notificationModel->sendByRule('spk_diesel_update', $context);
            }

            log_message('info', "SPK notification sent for SPK#{$spkId} status change to {$newStatus}");

        } catch (\Exception $e) {
            // Don't break the workflow if notifications fail
            log_message('error', "Failed to send SPK notification: " . $e->getMessage());
        }
    }

    /**
     * Example: DI Creation with Notification Integration
     * 
     * This shows how to integrate notifications when creating Delivery Instructions
     */
    public function createDIFromSPK($spkId)
    {
        // ... existing DI creation logic ...

        // Get SPK and related data
        $spkData = $this->db->table('spk')
            ->select('spk.*, c.nama_perusahaan')
            ->join('customers c', 'spk.customer_id = c.id', 'left')
            ->where('spk.id', $spkId)
            ->get()
            ->getRowArray();

        try {
            // Create DI record
            $diData = [
                'spk_id' => $spkId,
                'nomor_di' => $this->generateDINumber(),
                'status' => 'CREATED',
                'created_by' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $diId = $this->db->table('delivery_instructions')->insert($diData);

            // ===== NOTIFICATION INTEGRATION =====
            // Send targeted notifications about new DI
            $notificationContext = [
                'di_id' => $diId,
                'spk_id' => $spkId,
                'nomor_spk' => $spkData['nomor_spk'],
                'nomor_di' => $diData['nomor_di'],
                'customer_name' => $spkData['nama_perusahaan'],
                'departemen' => $spkData['departemen'],
                'created_by' => session()->get('username'),
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // Notify delivery team, logistics, and management
            $this->notificationModel->sendByRule('di_created', $notificationContext);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'DI berhasil dibuat',
                'di_id' => $diId
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal membuat DI: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Example: Inventory Alert Integration
     * 
     * This shows how to integrate inventory alerts when stock levels change
     */
    public function updateInventoryStock($itemId, $newStock)
    {
        try {
            // Get current item data
            $itemData = $this->db->table('inventory_items')
                ->where('id', $itemId)
                ->get()
                ->getRowArray();

            // Update stock
            $this->db->table('inventory_items')
                ->where('id', $itemId)
                ->update([
                    'current_stock' => $newStock,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // ===== SMART INVENTORY NOTIFICATIONS =====
            $this->checkAndSendInventoryAlerts($itemId, $itemData, $newStock);

            return ['success' => true, 'message' => 'Stock updated successfully'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function checkAndSendInventoryAlerts($itemId, $itemData, $newStock)
    {
        $context = [
            'item_id' => $itemId,
            'item_name' => $itemData['name'],
            'old_stock' => $itemData['current_stock'],
            'current_stock' => $newStock,
            'min_stock' => $itemData['min_stock'] ?? 10,
            'category' => $itemData['category'] ?? 'general',
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Low stock alert
        if ($newStock <= ($itemData['min_stock'] ?? 10)) {
            $this->notificationModel->sendByRule('inventory_low_stock', $context);
        }

        // Critical stock alert (zero or negative)
        if ($newStock <= 0) {
            $context['alert_level'] = 'critical';
            $this->notificationModel->sendByRule('inventory_critical_stock', $context);
        }

        // Reorder point alert
        if ($newStock <= ($itemData['reorder_point'] ?? 5)) {
            $this->notificationModel->sendByRule('inventory_reorder_needed', $context);
        }
    }

    /**
     * Helper method to determine SPK priority based on various factors
     */
    private function determineSPKPriority($spkData)
    {
        // Priority logic based on customer type, department, urgency, etc.
        $factors = [
            'customer_vip' => strpos(strtolower($spkData['nama_perusahaan'] ?? ''), 'priority') !== false,
            'urgent_department' => in_array($spkData['departemen'] ?? '', ['diesel', 'emergency']),
            'large_value' => ($spkData['total_value'] ?? 0) > 100000000, // 100M+
            'express_delivery' => ($spkData['delivery_type'] ?? '') === 'express'
        ];

        $priorityScore = array_sum($factors);

        if ($priorityScore >= 3) return 'urgent';
        if ($priorityScore >= 2) return 'high';
        if ($priorityScore >= 1) return 'medium';
        return 'low';
    }

    /**
     * Example method showing how to test notification targeting
     */
    public function testNotificationTargeting()
    {
        if (session()->get('role') !== 'superadmin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        try {
            // Test SPK DIESEL notification targeting
            $testContext = [
                'spk_id' => 999,
                'nomor_spk' => 'SPK-TEST-001',
                'departemen' => 'diesel',
                'customer_name' => 'Test Customer',
                'new_status' => 'SUBMITTED',
                'created_by' => 'Test User',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $result = $this->notificationModel->sendByRule('spk_created', $testContext);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Test notification sent successfully',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }
}

/**
 * NOTIFICATION RULE EXAMPLES
 * 
 * These are examples of notification rules that would be configured
 * in the notification_rules table to support the above integrations:
 * 
 * 1. SPK DIESEL to Service DIESEL:
 *    - trigger_event: 'spk_created'
 *    - target_divisions: 'service'
 *    - target_departments: 'diesel'
 *    - conditions: '{"source_department": "diesel"}'
 *    - title_template: 'SPK Baru - {departemen} #{nomor_spk}'
 *    - message_template: 'SPK baru {nomor_spk} untuk departemen {departemen} telah dibuat...'
 * 
 * 2. DI Processing Alert:
 *    - trigger_event: 'di_created'
 *    - target_divisions: 'service,logistics'
 *    - conditions: '{}'
 *    - title_template: 'DI Baru Perlu Diproses - #{nomor_di}'
 * 
 * 3. High Priority SPK Alert:
 *    - trigger_event: 'spk_priority_started'
 *    - target_roles: 'manager,admin'
 *    - conditions: '{"priority": "high"}'
 *    - title_template: 'SPK Prioritas Tinggi Dimulai - {nomor_spk}'
 * 
 * 4. Low Stock Alert:
 *    - trigger_event: 'inventory_low_stock'
 *    - target_divisions: 'warehouse,purchasing'
 *    - conditions: '{}'
 *    - title_template: 'Stok Rendah - {item_name}'
 */
