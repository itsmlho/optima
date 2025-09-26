<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderPriorityModel extends Model
{
    protected $table            = 'work_order_priorities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'priority_name',
        'priority_code',
        'priority_level',
        'priority_color',
        'description',
        'sla_hours',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all active priorities
     */
    public function getActivePriorities()
    {
        return $this->where('is_active', 1)
                   ->orderBy('priority_level', 'DESC')
                   ->findAll();
    }

    /**
     * Get priority by code
     */
    public function getByCode($code)
    {
        return $this->where('priority_code', $code)
                   ->where('is_active', 1)
                   ->first();
    }

    /**
     * Get priority options for dropdown
     */
    public function getPriorityOptions()
    {
        $priorities = $this->getActivePriorities();
        $options = [];
        
        foreach ($priorities as $priority) {
            $options[$priority['id']] = $priority['priority_name'];
        }
        
        return $options;
    }
}