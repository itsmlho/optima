<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationRuleModel extends Model
{
    protected $table = 'notification_rules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'name',
        'description',
        'trigger_event',
        'target_divisions',
        'target_roles',
        'target_departments',
        'target_users',
        'title_template',
        'message_template',
        'category',
        'type',
        'priority',
        'icon',
        'url_template',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'name' => 'required|string|max_length[100]',
        'trigger_event' => 'required|string|max_length[100]',
        'title_template' => 'required|string|max_length[255]',
        'type' => 'permit_empty|in_list[info,success,warning,error]'
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Rule name is required',
            'max_length' => 'Rule name cannot exceed 100 characters'
        ],
        'trigger_event' => [
            'required' => 'Event type is required'
        ],
        'title_template' => [
            'required' => 'Title template is required'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    /**
     * Get active rules by event type
     */
    public function getActiveRulesByEvent($eventType)
    {
        return $this->where('trigger_event', $eventType)
            ->where('is_active', 1)
            ->findAll();
    }
    
    /**
     * Get all event types
     */
    public function getAllEventTypes()
    {
        return $this->select('trigger_event')
            ->distinct()
            ->where('trigger_event IS NOT NULL')
            ->where('trigger_event !=', '')
            ->orderBy('trigger_event', 'ASC')
            ->findAll();
    }
    
    /**
     * Get rules by division
     */
    public function getRulesByDivision($division)
    {
        return $this->where('is_active', 1)
            ->groupStart()
                ->like('target_divisions', $division)
                ->orWhere('target_divisions', null)
                ->orWhere('target_divisions', '')
            ->groupEnd()
            ->findAll();
    }
    
    /**
     * Toggle rule status
     */
    public function toggleStatus($ruleId)
    {
        $rule = $this->find($ruleId);
        if (!$rule) {
            return false;
        }
        
        $newStatus = $rule['is_active'] ? 0 : 1;
        return $this->update($ruleId, ['is_active' => $newStatus]);
    }
}
