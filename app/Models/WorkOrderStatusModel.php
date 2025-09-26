<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderStatusModel extends Model
{
    protected $table            = 'work_order_statuses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status_name',
        'status_code', 
        'status_color',
        'description',
        'is_final_status',
        'sort_order',
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
     * Get all active statuses
     */
    public function getActiveStatuses()
    {
        return $this->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->findAll();
    }

    /**
     * Get status by code
     */
    public function getByCode($code)
    {
        return $this->where('status_code', $code)
                   ->where('is_active', 1)
                   ->first();
    }

    /**
     * Get status options for dropdown
     */
    public function getStatusOptions()
    {
        $statuses = $this->getActiveStatuses();
        $options = [];
        
        foreach ($statuses as $status) {
            $options[$status['id']] = $status['status_name'];
        }
        
        return $options;
    }
}