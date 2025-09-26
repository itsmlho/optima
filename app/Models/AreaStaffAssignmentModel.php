<?php

namespace App\Models;

use CodeIgniter\Model;

class AreaStaffAssignmentModel extends Model
{
    protected $table = 'area_staff_assignments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'area_id',
        'staff_id',
        'assignment_type',
        'start_date',
        'end_date',
        'notes',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'area_id' => 'required|integer|is_not_unique[areas.id]',
        'staff_id' => 'required|integer|is_not_unique[staff.id]',
        'assignment_type' => 'required|in_list[PRIMARY,BACKUP,TEMPORARY]',
        'start_date' => 'required|valid_date',
        'end_date' => 'permit_empty|valid_date',
        'notes' => 'permit_empty',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'area_id' => [
            'required' => 'Area harus dipilih',
            'integer' => 'Area harus berupa angka',
            'is_not_unique' => 'Area tidak valid'
        ],
        'staff_id' => [
            'required' => 'Staff harus dipilih',
            'integer' => 'Staff harus berupa angka',
            'is_not_unique' => 'Staff tidak valid'
        ],
        'assignment_type' => [
            'required' => 'Tipe assignment harus dipilih',
            'in_list' => 'Tipe assignment tidak valid'
        ],
        'start_date' => [
            'required' => 'Tanggal mulai harus diisi',
            'valid_date' => 'Format tanggal mulai tidak valid'
        ],
        'end_date' => [
            'valid_date' => 'Format tanggal berakhir tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    protected $allowCallbacks = true;
    protected $beforeInsert = ['validateDateRange'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['validateDateRange'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get assignments by area
     */
    public function getAssignmentsByArea($areaId)
    {
        return $this->select('area_staff_assignments.*, staff.staff_name, staff.staff_code, staff.staff_role')
                   ->join('staff', 'staff.id = area_staff_assignments.staff_id')
                   ->where('area_staff_assignments.area_id', $areaId)
                   ->where('area_staff_assignments.is_active', 1)
                   ->orderBy('staff.staff_role')
                   ->orderBy('area_staff_assignments.assignment_type')
                   ->findAll();
    }
    
    /**
     * Get assignments by staff
     */
    public function getAssignmentsByStaff($staffId)
    {
        return $this->select('area_staff_assignments.*, areas.area_name, areas.area_code')
                   ->join('areas', 'areas.id = area_staff_assignments.area_id')
                   ->where('area_staff_assignments.staff_id', $staffId)
                   ->where('area_staff_assignments.is_active', 1)
                   ->orderBy('area_staff_assignments.assignment_type')
                   ->orderBy('area_staff_assignments.start_date', 'DESC')
                   ->findAll();
    }
    
    /**
     * Get active assignments
     */
    public function getActiveAssignments($areaId = null, $staffId = null)
    {
        $builder = $this->select('
                        area_staff_assignments.*,
                        areas.area_name, areas.area_code,
                        staff.staff_name, staff.staff_code, staff.staff_role
                    ')
                       ->join('areas', 'areas.id = area_staff_assignments.area_id')
                       ->join('staff', 'staff.id = area_staff_assignments.staff_id')
                       ->where('area_staff_assignments.is_active', 1)
                       ->where('staff.is_active', 1)
                       ->where('areas.is_active', 1);
                       
        // Check if assignment is currently active (within date range)
        $today = date('Y-m-d');
        $builder->where('area_staff_assignments.start_date <=', $today)
               ->groupStart()
               ->where('area_staff_assignments.end_date >=', $today)
               ->orWhere('area_staff_assignments.end_date IS NULL')
               ->groupEnd();
        
        if ($areaId) {
            $builder->where('area_staff_assignments.area_id', $areaId);
        }
        
        if ($staffId) {
            $builder->where('area_staff_assignments.staff_id', $staffId);
        }
        
        return $builder->orderBy('areas.area_name')
                      ->orderBy('staff.staff_role')
                      ->orderBy('area_staff_assignments.assignment_type')
                      ->findAll();
    }
    
    /**
     * Assign staff to area
     */
    public function assignStaffToArea($areaId, $staffId, $assignmentType = 'PRIMARY', $startDate = null, $endDate = null, $notes = null)
    {
        $data = [
            'area_id' => $areaId,
            'staff_id' => $staffId,
            'assignment_type' => $assignmentType,
            'start_date' => $startDate ?: date('Y-m-d'),
            'end_date' => $endDate,
            'notes' => $notes,
            'is_active' => 1
        ];
        
        return $this->save($data);
    }
    
    /**
     * Remove staff assignment
     */
    public function removeAssignment($assignmentId)
    {
        return $this->update($assignmentId, ['is_active' => 0]);
    }
    
    /**
     * Get staff by unit for work order assignment
     */
    public function getStaffForUnit($unitId, $role = null)
    {
        $builder = $this->db->table('area_staff_assignments asa')
                           ->select('s.id as staff_id, s.staff_name, s.staff_role, asa.assignment_type')
                           ->join('staff s', 's.id = asa.staff_id')
                           ->join('areas a', 'a.id = asa.area_id')
                           ->join('customers c', 'c.area_id = a.id')
                           ->join('inventory_unit iu', 'iu.customer_id = c.id')
                           ->where('iu.id_inventory_unit', $unitId)
                           ->where('s.is_active', 1)
                           ->where('asa.is_active', 1);
                           
        // Filter by current date
        $today = date('Y-m-d');
        $builder->where('asa.start_date <=', $today)
               ->groupStart()
               ->where('asa.end_date >=', $today)
               ->orWhere('asa.end_date IS NULL')
               ->groupEnd();
        
        if ($role) {
            $builder->where('s.staff_role', $role);
        }
        
        return $builder->orderBy('s.staff_role')
                      ->orderBy('asa.assignment_type', 'ASC') // PRIMARY first
                      ->get()->getResultArray();
    }
    
    /**
     * Get assignment types for dropdown
     */
    public function getAssignmentTypes()
    {
        return [
            'PRIMARY' => 'Primary',
            'BACKUP' => 'Backup',
            'TEMPORARY' => 'Temporary'
        ];
    }
    
    /**
     * Validate date range before insert/update
     */
    protected function validateDateRange(array $data)
    {
        if (isset($data['data']['start_date']) && isset($data['data']['end_date']) && 
            !empty($data['data']['end_date'])) {
            
            $startDate = $data['data']['start_date'];
            $endDate = $data['data']['end_date'];
            
            if (strtotime($endDate) < strtotime($startDate)) {
                throw new \InvalidArgumentException('Tanggal berakhir tidak boleh lebih awal dari tanggal mulai');
            }
        }
        
        return $data;
    }
    
    /**
     * Check if staff is already assigned to area
     */
    public function isStaffAssignedToArea($staffId, $areaId, $assignmentType = null)
    {
        $builder = $this->where('staff_id', $staffId)
                       ->where('area_id', $areaId)
                       ->where('is_active', 1);
                       
        if ($assignmentType) {
            $builder->where('assignment_type', $assignmentType);
        }
        
        // Check current date range
        $today = date('Y-m-d');
        $builder->where('start_date <=', $today)
               ->groupStart()
               ->where('end_date >=', $today)
               ->orWhere('end_date IS NULL')
               ->groupEnd();
        
        return $builder->first() !== null;
    }
}