<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationRuleModel extends Model
{
    protected $table = 'notification_rules';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'description', 'trigger_event', 'is_active',
        'conditions', 'target_roles', 'target_divisions', 'target_departments', 
        'target_users', 'exclude_creator', 'title_template', 'message_template',
        'category', 'type', 'priority', 'url_template', 'delay_minutes', 
        'expire_days', 'created_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get active rules for specific trigger event
     */
    public function getActiveRulesByEvent($triggerEvent)
    {
        return $this->where([
            'trigger_event' => $triggerEvent,
            'is_active' => 1
        ])->findAll();
    }

    /**
     * Create default notification rules for system
     */
    public function createDefaultRules()
    {
        $defaultRules = [
            [
                'name' => 'SPK Created - Service Notification',
                'description' => 'Notify service division when new SPK is created',
                'trigger_event' => 'spk_created',
                'conditions' => '{}',
                'target_divisions' => 'service',
                'target_roles' => 'manager,supervisor,technician',
                'title_template' => 'SPK Baru: {{nomor_spk}} - {{departemen}}',
                'message_template' => 'SPK baru telah dibuat untuk {{pelanggan}} dengan departemen {{departemen}}. Silakan periksa dan proses sesuai prosedur.',
                'category' => 'spk',
                'type' => 'info',
                'priority' => 2,
                'url_template' => '/service/spk/detail/{{id}}',
                'exclude_creator' => 1
            ],
            [
                'name' => 'SPK DIESEL - Service DIESEL Team',
                'description' => 'Notify DIESEL service team for DIESEL SPK',
                'trigger_event' => 'spk_created',
                'conditions' => '{"departemen": "DIESEL"}',
                'target_divisions' => 'service',
                'target_departments' => 'DIESEL',
                'title_template' => 'SPK DIESEL: {{nomor_spk}} - {{pelanggan}}',
                'message_template' => 'SPK DIESEL baru memerlukan perhatian tim service DIESEL. Unit: {{unit_info}}, Lokasi: {{lokasi}}',
                'category' => 'spk',
                'type' => 'warning',
                'priority' => 3,
                'url_template' => '/service/spk/detail/{{id}}'
            ],
            [
                'name' => 'DI Ready - Operational Team',
                'description' => 'Notify operational when DI is ready for processing',
                'trigger_event' => 'di_submitted',
                'conditions' => '{}',
                'target_divisions' => 'operational',
                'title_template' => 'DI Siap Diproses: {{nomor_di}}',
                'message_template' => 'Delivery Instruction {{nomor_di}} untuk {{pelanggan}} siap diproses. Lokasi: {{lokasi}}',
                'category' => 'di',
                'type' => 'info',
                'priority' => 2,
                'url_template' => '/operational/delivery'
            ]
        ];

        foreach ($defaultRules as $rule) {
            $existing = $this->where('name', $rule['name'])->first();
            if (!$existing) {
                $this->insert($rule);
            }
        }
    }

    /**
     * Get all available trigger events
     */
    public function getAvailableTriggerEvents()
    {
        return [
            'spk_created' => 'SPK Dibuat',
            'spk_approved' => 'SPK Disetujui',
            'spk_completed' => 'SPK Selesai',
            'di_submitted' => 'DI Diajukan',
            'di_processed' => 'DI Diproses',
            'di_delivered' => 'DI Terkirim',
            'inventory_low_stock' => 'Stok Rendah',
            'maintenance_due' => 'Maintenance Jatuh Tempo',
            'contract_expiring' => 'Kontrak Akan Berakhir',
            'user_login' => 'User Login',
            'system_error' => 'System Error'
        ];
    }

    /**
     * Test notification rule
     */
    public function testRule($ruleId, $testData = [])
    {
        $rule = $this->find($ruleId);
        if (!$rule) {
            return ['success' => false, 'message' => 'Rule not found'];
        }

        $notificationModel = new NotificationModel();
        
        // Use default test data if none provided
        if (empty($testData)) {
            $testData = [
                'nomor_spk' => 'SPK/TEST/001',
                'pelanggan' => 'Test Customer',
                'departemen' => 'DIESEL',
                'id' => 999
            ];
        }

        try {
            $notification = [
                'title' => $this->processTemplate($rule['title_template'], $testData),
                'message' => $this->processTemplate($rule['message_template'], $testData),
                'type' => $rule['type'],
                'category' => 'test',
                'priority' => $rule['priority'],
                'url' => $this->processTemplate($rule['url_template'] ?? '', $testData)
            ];

            $recipients = $this->getTestRecipients($rule);

            return [
                'success' => true,
                'notification' => $notification,
                'recipients_count' => count($recipients),
                'recipients' => $recipients
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ];
        }
    }

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

    protected function getTestRecipients($rule)
    {
        $userModel = new \App\Models\UserModel();
        $recipients = [];

        if (!empty($rule['target_roles'])) {
            $roles = explode(',', $rule['target_roles']);
            $roleUsers = $userModel->getUsersByRoles($roles);
            $recipients = array_merge($recipients, $roleUsers);
        }

        if (!empty($rule['target_divisions'])) {
            $divisions = explode(',', $rule['target_divisions']);
            $divisionUsers = $userModel->getUsersByDivisions($divisions);
            $recipients = array_merge($recipients, $divisionUsers);
        }

        return array_unique($recipients, SORT_REGULAR);
    }

    /**
     * Get all notification rules with creator info
     */
    public function getAllRules($limit = null, $offset = null)
    {
        return $this->select('notification_rules.*, CONCAT(users.first_name, " ", users.last_name) as creator_name')
                    ->join('users', 'users.id = notification_rules.created_by', 'left')
                    ->orderBy('notification_rules.created_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }    /**
     * Get rule by ID with creator info
     */
    public function getRuleById($id)
    {
        return $this->select('notification_rules.*, CONCAT(users.first_name, " ", users.last_name) as creator_name')
                    ->join('users', 'users.id = notification_rules.created_by', 'left')
                    ->where('notification_rules.id', $id)
                    ->first();
    }    /**
     * Create a new notification rule
     */
    public function createRule($data)
    {
        return $this->insert($data);
    }

    /**
     * Update notification rule
     */
    public function updateRule($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Delete notification rule
     */
    public function deleteRule($id)
    {
        return $this->delete($id);
    }

    /**
     * Toggle rule active status
     */
    public function toggleRuleStatus($id)
    {
        $rule = $this->find($id);
        if ($rule) {
            return $this->update($id, ['is_active' => !$rule['is_active']]);
        }
        return false;
    }
}
