<?php

namespace App\Models;

use CodeIgniter\Model;

class StaffModel extends Model
{
    protected $table = 'staff';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'staff_code',
        'staff_name',
        'staff_role',
        'phone',
        'email',
        'address',
        'hire_date',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'staff_code' => 'required|max_length[20]|is_unique[staff.staff_code,id,{id}]',
        'staff_name' => 'required|max_length[100]',
        'staff_role' => 'required|in_list[ADMIN,FOREMAN,MECHANIC,HELPER,SUPERVISOR]',
        'phone' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[100]',
        'address' => 'permit_empty',
        'hire_date' => 'permit_empty|valid_date',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'staff_code' => [
            'required' => 'Kode staff harus diisi',
            'is_unique' => 'Kode staff sudah digunakan',
            'max_length' => 'Kode staff maksimal 20 karakter'
        ],
        'staff_name' => [
            'required' => 'Nama staff harus diisi',
            'max_length' => 'Nama staff maksimal 100 karakter'
        ],
        'staff_role' => [
            'required' => 'Role staff harus dipilih',
            'in_list' => 'Role staff tidak valid'
        ],
        'phone' => [
            'max_length' => 'Nomor telepon maksimal 20 karakter'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid',
            'max_length' => 'Email maksimal 100 karakter'
        ],
        'hire_date' => [
            'valid_date' => 'Format tanggal tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get active staff
     */
    public function getActiveStaff()
    {
        return $this->where('is_active', 1)->orderBy('staff_name')->findAll();
    }
    
    /**
     * Get staff by role
     */
    public function getStaffByRole($role)
    {
        return $this->where('staff_role', $role)
                   ->where('is_active', 1)
                   ->orderBy('staff_name')
                   ->findAll();
    }
    
    /**
     * Get staff assigned to area
     */
    public function getStaffByArea($areaId, $role = null)
    {
        $builder = $this->select('staff.*, area_staff_assignments.assignment_type, area_staff_assignments.start_date, area_staff_assignments.end_date')
                       ->join('area_staff_assignments', 'area_staff_assignments.staff_id = staff.id')
                       ->where('area_staff_assignments.area_id', $areaId)
                       ->where('area_staff_assignments.is_active', 1)
                       ->where('staff.is_active', 1);
                       
        if ($role) {
            $builder->where('staff.staff_role', $role);
        }
        
        return $builder->orderBy('staff.staff_role')
                      ->orderBy('staff.staff_name')
                      ->findAll();
    }
    
    /**
     * Get staff for specific unit (based on unit's customer area)
     */
    public function getStaffByUnit($unitId, $role = null)
    {
        $builder = $this->db->table('staff s')
                           ->select('s.*, s.staff_role, asa.assignment_type, c.customer_name, a.area_name')
                           ->join('area_staff_assignments asa', 's.id = asa.staff_id')
                           ->join('areas a', 'asa.area_id = a.id')
                           ->join('customers c', 'a.id = c.area_id')
                           ->join('inventory_unit iu', 'c.id = iu.customer_id')
                           ->where('iu.id_inventory_unit', $unitId)
                           ->where('s.is_active', 1)
                           ->where('asa.is_active', 1);
                           
        if ($role) {
            $builder->where('s.staff_role', $role);
        }
        
        return $builder->orderBy('s.staff_role')
                      ->orderBy('asa.assignment_type')
                      ->get()->getResultArray();
    }
    
    /**
     * Get staff with area assignments
     */
    public function getStaffWithAreas($staffId = null)
    {
        $builder = $this->select('
                        staff.*,
                        GROUP_CONCAT(CONCAT(areas.area_name, " (", area_staff_assignments.assignment_type, ")") SEPARATOR ", ") as assigned_areas
                    ')
                       ->join('area_staff_assignments', 'area_staff_assignments.staff_id = staff.id AND area_staff_assignments.is_active = 1', 'left')
                       ->join('areas', 'areas.id = area_staff_assignments.area_id', 'left')
                       ->where('staff.is_active', 1);
                       
        if ($staffId) {
            $builder->where('staff.id', $staffId);
        }
        
        $builder->groupBy('staff.id')
               ->orderBy('staff.staff_role')
               ->orderBy('staff.staff_name');
               
        if ($staffId) {
            return $builder->first();
        }
        
        return $builder->findAll();
    }
    
    /**
     * Search staff
     */
    public function searchStaff($search = '', $role = null, $areaId = null)
    {
        $builder = $this->select('staff.*, GROUP_CONCAT(areas.area_name SEPARATOR ", ") as assigned_areas')
                       ->join('area_staff_assignments asa', 'asa.staff_id = staff.id AND asa.is_active = 1', 'left')
                       ->join('areas', 'areas.id = asa.area_id', 'left')
                       ->where('staff.is_active', 1);
                       
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('staff.staff_name', $search)
                   ->orLike('staff.staff_code', $search)
                   ->orLike('staff.phone', $search)
                   ->orLike('staff.email', $search)
                   ->groupEnd();
        }
        
        if ($role) {
            $builder->where('staff.staff_role', $role);
        }
        
        if ($areaId) {
            $builder->where('asa.area_id', $areaId);
        }
        
        return $builder->groupBy('staff.id')
                      ->orderBy('staff.staff_role')
                      ->orderBy('staff.staff_name')
                      ->findAll();
    }
    
    /**
     * Get staff for dropdown
     */
    public function getStaffForDropdown($role = null, $areaId = null)
    {
        $builder = $this->select('staff.id, staff.staff_name, staff.staff_code, staff.staff_role')
                       ->where('staff.is_active', 1);
                       
        if ($areaId) {
            $builder->join('area_staff_assignments asa', 'asa.staff_id = staff.id')
                   ->where('asa.area_id', $areaId)
                   ->where('asa.is_active', 1);
        }
        
        if ($role) {
            $builder->where('staff.staff_role', $role);
        }
        
        $staff = $builder->orderBy('staff.staff_name')->findAll();
        
        $options = [];
        foreach ($staff as $person) {
            $label = $person['staff_name'] . ' (' . $person['staff_role'] . ')';
            $options[$person['id']] = $label;
        }
        
        return $options;
    }
    
    /**
     * Generate next staff code
     */
    public function generateStaffCode($prefix = 'STF')
    {
        $lastStaff = $this->select('staff_code')
                         ->like('staff_code', $prefix)
                         ->orderBy('id', 'DESC')
                         ->first();
                         
        if ($lastStaff) {
            $lastNumber = (int)substr($lastStaff['staff_code'], strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get staff roles for dropdown
     */
    public function getStaffRoles()
    {
        return [
            'ADMIN' => 'Administrator',
            'FOREMAN' => 'Foreman',
            'MECHANIC' => 'Mechanic',
            'HELPER' => 'Helper',
            'SUPERVISOR' => 'Supervisor'
        ];
    }
}