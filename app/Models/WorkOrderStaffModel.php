<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderStaffModel extends Model
{
    protected $table            = 'work_order_staff';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'staff_name',
        'staff_role',
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
     * Get all active staff
     */
    public function getActiveStaff()
    {
        return $this->where('is_active', 1)
                   ->orderBy('staff_role', 'ASC')
                   ->orderBy('staff_name', 'ASC')
                   ->findAll();
    }

    /**
     * Get staff by role
     */
    public function getByRole($role)
    {
        return $this->where('staff_role', $role)
                   ->where('is_active', 1)
                   ->orderBy('staff_name', 'ASC')
                   ->findAll();
    }

    /**
     * Get staff grouped by role
     */
    public function getStaffByRole()
    {
        $allStaff = $this->getActiveStaff();
        $groupedStaff = [
            'ADMIN' => [],
            'FOREMAN' => [],
            'MECHANIC' => [],
            'HELPER' => []
        ];
        
        foreach ($allStaff as $staff) {
            $groupedStaff[$staff['staff_role']][] = $staff;
        }
        
        return $groupedStaff;
    }

    /**
     * Get staff options for dropdown by role
     */
    public function getStaffOptionsByRole($role)
    {
        $staff = $this->getByRole($role);
        $options = [];
        
        foreach ($staff as $member) {
            $options[$member['id']] = $member['staff_name'];
        }
        
        return $options;
    }
}