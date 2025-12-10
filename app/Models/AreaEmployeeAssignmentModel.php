<?php

namespace App\Models;

use CodeIgniter\Model;

class AreaEmployeeAssignmentModel extends Model
{
    protected $table = 'area_employee_assignments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true; // Re-enabled after deleted_at column was added
    protected $protectFields = true;
    
    protected $allowedFields = [
        'area_id',
        'employee_id',
        'assignment_type',
        'start_date',
        'end_date',
        'is_active',
        'notes',
        'department_scope'  // NEW: ALL, ELECTRIC, DIESEL, DIESEL,GASOLINE, etc.
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'area_id' => 'required|integer',
        'employee_id' => 'required|integer',
        'assignment_type' => 'required|in_list[PRIMARY,BACKUP,TEMPORARY]',
        'start_date' => 'required|valid_date',
        'end_date' => 'permit_empty|valid_date',
        'is_active' => 'permit_empty|in_list[0,1]',
        'notes' => 'permit_empty',
        'department_scope' => 'permit_empty|string|max_length[100]'
    ];

    protected $validationMessages = [
        'area_id' => [
            'required' => 'Area harus dipilih',
            'integer' => 'Area tidak valid'
        ],
        'employee_id' => [
            'required' => 'Employee harus dipilih',
            'integer' => 'Employee tidak valid'
        ],
        'assignment_type' => [
            'required' => 'Tipe assignment harus dipilih',
            'in_list' => 'Tipe assignment tidak valid'
        ],
        'start_date' => [
            'required' => 'Tanggal mulai harus diisi',
            'valid_date' => 'Tanggal mulai tidak valid'
        ],
        'end_date' => [
            'valid_date' => 'Tanggal akhir tidak valid'
        ],
        'is_active' => [
            'in_list' => 'Status aktif tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get active assignments by area
     */
    public function getAssignmentsByArea($areaId)
    {
        return $this->db->table($this->table . ' aea')
                       ->select('aea.*, e.staff_name, e.staff_role, d.nama_departemen, a.area_name, a.area_type')
                       ->join('employees e', 'aea.employee_id = e.id', 'inner')
                       ->join('departemen d', 'e.departemen_id = d.id_departemen', 'left')
                       ->join('areas a', 'aea.area_id = a.id', 'left')
                       ->where('aea.area_id', $areaId)
                       ->where('aea.is_active', 1)
                       ->where('e.is_active', 1)
                       ->orderBy('e.staff_role', 'ASC')
                       ->orderBy('e.staff_name', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get active assignments by employee
     */
    public function getAssignmentsByEmployee($employeeId)
    {
        return $this->db->table($this->table . ' aea')
                       ->select('aea.*, e.staff_name, e.staff_role, d.nama_departemen, a.area_name, a.area_code, a.area_type')
                       ->join('employees e', 'aea.employee_id = e.id', 'inner')
                       ->join('departemen d', 'e.departemen_id = d.id_departemen', 'left')
                       ->join('areas a', 'aea.area_id = a.id', 'left')
                       ->where('aea.employee_id', $employeeId)
                       ->where('aea.is_active', 1)
                       ->where('e.is_active', 1)
                       ->orderBy('aea.start_date', 'DESC')
                       ->get()
                       ->getResultArray();
    }
    
    /**
     * Get assignments by employee with department scope filter
     */
    public function getEmployeeAssignmentsWithScope($employeeId)
    {
        $assignments = $this->getAssignmentsByEmployee($employeeId);
        
        $result = [
            'areas' => [],
            'departments' => [],
            'has_full_access' => false
        ];
        
        foreach ($assignments as $assign) {
            $result['areas'][] = $assign['area_id'];
            
            $scope = $assign['department_scope'] ?? 'ALL';
            
            if ($scope === 'ALL') {
                $result['has_full_access'] = true;
                return $result; // Return immediately for full access
            }
            
            // Parse scope: 'ELECTRIC', 'DIESEL', 'DIESEL,GASOLINE'
            $depts = array_map('trim', explode(',', $scope));
            foreach ($depts as $dept) {
                if ($dept === 'ELECTRIC') $result['departments'][] = 2;
                if ($dept === 'DIESEL') $result['departments'][] = 1;
                if ($dept === 'GASOLINE') $result['departments'][] = 3;
            }
        }
        
        $result['areas'] = array_unique($result['areas']);
        $result['departments'] = array_unique($result['departments']);
        
        return $result;
    }

    /**
     * Get primary assignment for employee in area
     */
    public function getPrimaryAssignment($employeeId, $areaId)
    {
        return $this->db->table($this->table)
                       ->where('employee_id', $employeeId)
                       ->where('area_id', $areaId)
                       ->where('assignment_type', 'PRIMARY')
                       ->where('is_active', 1)
                       ->get()
                       ->getRowArray();
    }

    /**
     * Get employees by area and role
     */
    public function getEmployeesByAreaAndRole($areaId, $role)
    {
        return $this->db->table($this->table . ' aea')
                       ->select('e.*, d.nama_departemen')
                       ->join('employees e', 'aea.employee_id = e.id', 'inner')
                       ->join('departemen d', 'e.departemen_id = d.id_departemen', 'left')
                       ->where('aea.area_id', $areaId)
                       ->where('e.staff_role', $role)
                       ->where('aea.is_active', 1)
                       ->where('e.is_active', 1)
                       ->orderBy('aea.assignment_type', 'ASC')
                       ->orderBy('e.staff_name', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Check if employee is assigned to area
     */
    public function isEmployeeAssignedToArea($employeeId, $areaId)
    {
        $result = $this->db->table($this->table)
                          ->where('employee_id', $employeeId)
                          ->where('area_id', $areaId)
                          ->where('is_active', 1)
                          ->get()
                          ->getRowArray();
        
        return !empty($result);
    }

    /**
     * Get area summary with employee counts
     */
    public function getAreaSummary()
    {
        return $this->db->table($this->table . ' aea')
                       ->select('a.id as area_id, a.area_name, a.area_code, d.nama_departemen,
                                COUNT(DISTINCT e.id) as total_employees,
                                COUNT(DISTINCT CASE WHEN e.staff_role = "ADMIN" THEN e.id END) as admin_count,
                                COUNT(DISTINCT CASE WHEN e.staff_role = "FOREMAN" THEN e.id END) as foreman_count,
                                COUNT(DISTINCT CASE WHEN e.staff_role = "MECHANIC" THEN e.id END) as mechanic_count,
                                COUNT(DISTINCT CASE WHEN e.staff_role = "HELPER" THEN e.id END) as helper_count')
                       ->join('areas a', 'aea.area_id = a.id', 'inner')
                       ->join('employees e', 'aea.employee_id = e.id', 'inner')
                       ->join('departemen d', 'e.departemen_id = d.id_departemen', 'left')
                       ->where('aea.is_active', 1)
                       ->where('e.is_active', 1)
                       ->where('a.is_active', 1)
                       ->groupBy('a.id, a.area_name, a.area_code, d.nama_departemen')
                       ->orderBy('d.nama_departemen', 'ASC')
                       ->orderBy('a.area_name', 'ASC')
                       ->get()
                       ->getResultArray();
    }
}
