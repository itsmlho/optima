<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true; // Re-enabled after deleted_at column was added
    protected $protectFields = true;
    
    protected $allowedFields = [
        'staff_code',
        'staff_name',
        'staff_role',
        'work_location',
        'job_description',
        'departemen_id',
        'phone',
        'email',
        'address',
        'hire_date',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'staff_code' => 'required|max_length[20]|is_unique[employees.staff_code,id,{id}]',
        'staff_name' => 'required|max_length[100]',
        'staff_role' => 'required|in_list[ADMIN,SUPERVISOR,FOREMAN,MECHANIC,MECHANIC_SERVICE_AREA,MECHANIC_UNIT_PREP,MECHANIC_FABRICATION,HELPER]',
        'work_location' => 'permit_empty|in_list[CENTRAL,BRANCH,BOTH]',
        'job_description' => 'permit_empty',
        'departemen_id' => 'permit_empty|integer',
        'phone' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[100]',
        'address' => 'permit_empty',
        'hire_date' => 'permit_empty|valid_date',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'staff_code' => [
            'required' => 'Kode staff harus diisi',
            'max_length' => 'Kode staff maksimal 20 karakter',
            'is_unique' => 'Kode staff sudah digunakan'
        ],
        'staff_name' => [
            'required' => 'Nama staff harus diisi',
            'max_length' => 'Nama staff maksimal 100 karakter'
        ],
        'staff_role' => [
            'required' => 'Role staff harus dipilih',
            'in_list' => 'Role staff tidak valid'
        ],
        'departemen_id' => [
            'integer' => 'Departemen tidak valid'
        ],
        'phone' => [
            'max_length' => 'Nomor telepon maksimal 20 karakter'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid',
            'max_length' => 'Email maksimal 100 karakter'
        ],
        'hire_date' => [
            'valid_date' => 'Tanggal hire tidak valid'
        ],
        'is_active' => [
            'in_list' => 'Status aktif tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get active employees by role
     */
    public function getEmployeesByRole($role = null)
    {
        $builder = $this->db->table($this->table);
        
        if ($role) {
            $builder->where('staff_role', $role);
        }
        
        return $builder->where('is_active', 1)
                      ->orderBy('staff_name', 'ASC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Get staff by role (alias for compatibility)
     */
    public function getStaffByRole($role = null)
    {
        return $this->getEmployeesByRole($role);
    }

    /**
     * Get employees by department
     */
    public function getEmployeesByDepartment($departmentId)
    {
        return $this->db->table($this->table)
                       ->where('departemen_id', $departmentId)
                       ->where('is_active', 1)
                       ->orderBy('staff_role', 'ASC')
                       ->orderBy('staff_name', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get employee with department info
     */
    public function getEmployeeWithDepartment($id)
    {
        return $this->db->table($this->table . ' e')
                       ->select('e.*, d.nama_departemen')
                       ->join('departemen d', 'e.departemen_id = d.id_departemen', 'left')
                       ->where('e.id', $id)
                       ->get()
                       ->getRowArray();
    }

    /**
     * Get employees assigned to area
     */
    public function getEmployeesByArea($areaId)
    {
        return $this->db->table($this->table . ' e')
                       ->select('e.*, d.nama_departemen, aea.assignment_type, aea.start_date, aea.end_date')
                       ->join('departemen d', 'e.departemen_id = d.id_departemen', 'left')
                       ->join('area_employee_assignments aea', 'e.id = aea.employee_id', 'inner')
                       ->where('aea.area_id', $areaId)
                       ->where('e.is_active', 1)
                       ->where('aea.is_active', 1)
                       ->orderBy('e.staff_role', 'ASC')
                       ->orderBy('e.staff_name', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Search employees
     */
    public function searchEmployees($search = '', $role = null, $departmentId = null)
    {
        $builder = $this->db->table($this->table . ' e');
        $builder->select('e.*, d.nama_departemen');
        $builder->join('departemen d', 'e.departemen_id = d.id_departemen', 'left');
        $builder->where('e.is_active', 1);

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('e.staff_name', $search)
                    ->orLike('e.staff_code', $search)
                    ->orLike('d.nama_departemen', $search)
                    ->groupEnd();
        }

        if ($role) {
            $builder->where('e.staff_role', $role);
        }

        if ($departmentId) {
            $builder->where('e.departemen_id', $departmentId);
        }

        return $builder->orderBy('e.staff_role', 'ASC')
                      ->orderBy('e.staff_name', 'ASC')
                      ->get()
                      ->getResultArray();
    }
}
