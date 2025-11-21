<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Models\DivisionModel;

/**
 * ============================================================================
 * OPTIMA NOTIFICATION SERVICE
 * ============================================================================
 * Facebook-style cross-division notification service
 * ============================================================================
 */
class NotificationService extends BaseController
{
    protected $notificationModel;
    protected $userModel;
    protected $divisionModel;
    protected $db;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        $this->divisionModel = new DivisionModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Send notification to specific user
     */
    public function sendToUser($userId, $title, $message, $options = [])
    {
        $data = [
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $options['type'] ?? 'info',
            'icon' => $options['icon'] ?? 'bell',
            'related_module' => $options['module'] ?? null,
            'related_id' => $options['related_id'] ?? null,
            'url' => $options['url'] ?? null,
            'is_read' => 0
        ];

        return $this->notificationModel->insert($data);
    }

    /**
     * Send notification to all users in a division
     */
    public function sendToDivision($divisionId, $title, $message, $options = [])
    {
        // Get all users in the division
        $users = $this->db->table('user_roles ur')
            ->join('users u', 'u.id = ur.user_id')
            ->where('ur.division_id', $divisionId)
            ->where('ur.is_active', 1)
            ->select('u.id')
            ->get()
            ->getResultArray();

        $sentCount = 0;
        foreach ($users as $user) {
            $this->sendToUser($user['id'], $title, $message, $options);
            $sentCount++;
        }

        return $sentCount;
    }

    /**
     * Send notification to all users with specific role
     */
    public function sendToRole($roleId, $title, $message, $options = [])
    {
        // Get all users with the role
        $users = $this->db->table('user_roles ur')
            ->join('users u', 'u.id = ur.user_id')
            ->where('ur.role_id', $roleId)
            ->where('ur.is_active', 1)
            ->select('u.id')
            ->get()
            ->getResultArray();

        $sentCount = 0;
        foreach ($users as $user) {
            $this->sendToUser($user['id'], $title, $message, $options);
            $sentCount++;
        }

        return $sentCount;
    }

    /**
     * Send SPK notification (Marketing → Service)
     */
    public function sendSpkNotification($spkData)
    {
        $title = "SPK Baru: {$spkData['nomor_spk']}";
        $message = "SPK baru telah dibuat untuk {$spkData['pelanggan']} di departemen {$spkData['departemen']}";
        
        $options = [
            'type' => 'info',
            'icon' => 'file-contract',
            'module' => 'spk',
            'related_id' => $spkData['id'],
            'url' => base_url("marketing/spk/detail/{$spkData['id']}")
        ];

        // Send to Service division based on department
        $divisionId = $this->getServiceDivisionByDepartment($spkData['departemen']);
        if ($divisionId) {
            return $this->sendToDivision($divisionId, $title, $message, $options);
        }

        return 0;
    }

    /**
     * Send Work Order notification (Service → Warehouse)
     */
    public function sendWorkOrderNotification($woData)
    {
        $title = "Work Order Baru: {$woData['nomor_wo']}";
        $message = "Work Order baru telah dibuat untuk unit {$woData['unit']}";
        
        $options = [
            'type' => 'info',
            'icon' => 'tools',
            'module' => 'work_order',
            'related_id' => $woData['id'],
            'url' => base_url("service/work-orders/detail/{$woData['id']}")
        ];

        // Send to Warehouse division
        $warehouseDivisionId = $this->getDivisionIdByName('Warehouse');
        if ($warehouseDivisionId) {
            return $this->sendToDivision($warehouseDivisionId, $title, $message, $options);
        }

        return 0;
    }

    /**
     * Send PO notification (Purchasing → Warehouse)
     */
    public function sendPONotification($poData)
    {
        $title = "PO Baru: {$poData['nomor_po']}";
        $message = "Purchase Order baru telah dibuat untuk supplier {$poData['supplier']}";
        
        $options = [
            'type' => 'info',
            'icon' => 'shopping-cart',
            'module' => 'purchase_order',
            'related_id' => $poData['id'],
            'url' => base_url("purchasing/po/detail/{$poData['id']}")
        ];

        // Send to Warehouse division
        $warehouseDivisionId = $this->getDivisionIdByName('Warehouse');
        if ($warehouseDivisionId) {
            return $this->sendToDivision($warehouseDivisionId, $title, $message, $options);
        }

        return 0;
    }

    /**
     * Send inventory alert notification
     */
    public function sendInventoryAlert($alertData)
    {
        $title = "Low Stock Alert";
        $message = "Stok unit {$alertData['unit']} tersisa {$alertData['stock']} unit";
        
        $options = [
            'type' => 'warning',
            'icon' => 'exclamation-triangle',
            'module' => 'inventory',
            'related_id' => $alertData['unit_id'],
            'url' => base_url("warehouse/inventory-unit")
        ];

        // Send to Warehouse and Purchasing divisions
        $warehouseDivisionId = $this->getDivisionIdByName('Warehouse');
        $purchasingDivisionId = $this->getDivisionIdByName('Purchasing');
        
        $sentCount = 0;
        if ($warehouseDivisionId) {
            $sentCount += $this->sendToDivision($warehouseDivisionId, $title, $message, $options);
        }
        if ($purchasingDivisionId) {
            $sentCount += $this->sendToDivision($purchasingDivisionId, $title, $message, $options);
        }

        return $sentCount;
    }

    /**
     * Send system notification to all users
     */
    public function sendSystemNotification($title, $message, $options = [])
    {
        $users = $this->userModel->findAll();
        $sentCount = 0;

        foreach ($users as $user) {
            $this->sendToUser($user['id'], $title, $message, $options);
            $sentCount++;
        }

        return $sentCount;
    }

    /**
     * Get service division by department
     */
    private function getServiceDivisionByDepartment($department)
    {
        $divisionMap = [
            'DIESEL' => 'Service Diesel',
            'ELECTRIC' => 'Service Electric',
            'RENTAL' => 'Service Diesel' // Default to Service Diesel
        ];

        $divisionName = $divisionMap[$department] ?? 'Service Diesel';
        return $this->getDivisionIdByName($divisionName);
    }

    /**
     * Get division ID by name
     */
    private function getDivisionIdByName($name)
    {
        $division = $this->divisionModel->where('name', $name)->first();
        return $division ? $division['id'] : null;
    }

    /**
     * Send notification with template
     */
    public function sendWithTemplate($template, $data, $recipients)
    {
        $templates = [
            'spk_created' => [
                'title' => 'SPK Baru: {nomor_spk}',
                'message' => 'SPK baru telah dibuat untuk {pelanggan} di departemen {departemen}',
                'type' => 'info',
                'icon' => 'file-contract'
            ],
            'wo_created' => [
                'title' => 'Work Order Baru: {nomor_wo}',
                'message' => 'Work Order baru telah dibuat untuk unit {unit}',
                'type' => 'info',
                'icon' => 'tools'
            ],
            'po_created' => [
                'title' => 'PO Baru: {nomor_po}',
                'message' => 'Purchase Order baru telah dibuat untuk supplier {supplier}',
                'type' => 'info',
                'icon' => 'shopping-cart'
            ],
            'low_stock' => [
                'title' => 'Low Stock Alert',
                'message' => 'Stok unit {unit} tersisa {stock} unit',
                'type' => 'warning',
                'icon' => 'exclamation-triangle'
            ]
        ];

        if (!isset($templates[$template])) {
            return false;
        }

        $templateData = $templates[$template];
        $title = $this->replaceTemplateVariables($templateData['title'], $data);
        $message = $this->replaceTemplateVariables($templateData['message'], $data);

        $options = [
            'type' => $templateData['type'],
            'icon' => $templateData['icon'],
            'module' => $data['module'] ?? null,
            'related_id' => $data['related_id'] ?? null,
            'url' => $data['url'] ?? null
        ];

        $sentCount = 0;
        foreach ($recipients as $recipient) {
            if (is_array($recipient)) {
                // Division or role recipient
                if (isset($recipient['division_id'])) {
                    $sentCount += $this->sendToDivision($recipient['division_id'], $title, $message, $options);
                } elseif (isset($recipient['role_id'])) {
                    $sentCount += $this->sendToRole($recipient['role_id'], $title, $message, $options);
                }
            } else {
                // User ID
                $this->sendToUser($recipient, $title, $message, $options);
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Replace template variables
     */
    private function replaceTemplateVariables($template, $data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
}
