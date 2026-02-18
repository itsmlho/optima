<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Operator Model
 * 
 * Manages equipment operators, mechanics, and drivers
 * 
 * @package    App\Models
 * @category   Marketing
 * @author     Optima Development Team
 * @created    2026-02-15
 */
class OperatorModel extends Model
{
    protected $table = 'operators';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'operator_code',
        'operator_name',
        'nik',
        'certification_level',
        'certification_number',
        'certification_issued_date',
        'certification_expiry',
        'certification_issuer',
        'skills',
        'monthly_rate',
        'daily_rate',
        'hourly_rate',
        'phone',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'employment_type',
        'join_date',
        'employee_id',
        'status',
        'current_assignment_id',
        'notes',
        'photo',
        'documents',
        'created_by',
        'updated_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;
    
    protected $returnType = 'array';
    
    // Validation rules
    protected $validationRules = [
        'operator_code' => 'required|max_length[50]|is_unique[operators.operator_code,id,{id}]',
        'operator_name' => 'required|max_length[100]',
        'monthly_rate'  => 'required|decimal',
        'status'        => 'required|in_list[AVAILABLE,ASSIGNED,ON_LEAVE,INACTIVE,TERMINATED]'
    ];
    
    protected $validationMessages = [
        'operator_code' => [
            'required' => 'Operator code is required',
            'is_unique' => 'Operator code already exists'
        ],
        'operator_name' => [
            'required' => 'Operator name is required'
        ]
    ];
    
    // Cast fields to proper types
    protected $casts = [
        'monthly_rate' => 'decimal',
        'daily_rate' => 'decimal',
        'hourly_rate' => 'decimal',
        'certification_issued_date' => 'date',
        'certification_expiry' => 'date',
        'join_date' => 'date'
    ];
    
    /**
     * Get available operators (not assigned)
     * 
     * @return array
     */
    public function getAvailableOperators()
    {
        return $this->where('status', 'AVAILABLE')
                    ->where('deleted_at', null)
                    ->orderBy('certification_level', 'DESC')
                    ->orderBy('operator_name', 'ASC')
                    ->findAll();
    }
    
    /**
     * Check if operator is available for assignment
     * 
     * @param int $operatorId
     * @param string $startDate
     * @return bool
     */
    public function isAvailable($operatorId, $startDate = null)
    {
        $operator = $this->find($operatorId);
        
        if (!$operator || $operator['status'] !== 'AVAILABLE') {
            return false;
        }
        
        // Check for overlapping active assignments
        if ($startDate) {
            $db = \Config\Database::connect();
            $overlap = $db->table('contract_operator_assignments')
                ->where('operator_id', $operatorId)
                ->where('status', 'ACTIVE')
                ->groupStart()
                    ->where('assignment_end IS NULL')
                    ->orWhere('assignment_end >=', $startDate)
                ->groupEnd()
                ->countAllResults();
                
            return $overlap === 0;
        }
        
        return true;
    }
    
    /**
     * Generate next operator code
     * 
     * @return string
     */
    public function generateOperatorCode()
    {
        $lastCode = $this->selectMax('operator_code')
                         ->where('operator_code LIKE', 'OP-%')
                         ->first();
        
        if ($lastCode && isset($lastCode['operator_code'])) {
            $number = (int) substr($lastCode['operator_code'], 3);
            $nextNumber = $number + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'OP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get operators by certification level
     * 
     * @param string $level
     * @return array
     */
    public function getByCertificationLevel($level)
    {
        return $this->where('certification_level', $level)
                    ->where('status !=', 'TERMINATED')
                    ->findAll();
    }
    
    /**
     * Get operators with expiring certifications
     * 
     * @param int $daysThreshold
     * @return array
     */
    public function getExpiringCertifications($daysThreshold = 30)
    {
        $thresholdDate = date('Y-m-d', strtotime("+{$daysThreshold} days"));
        
        return $this->where('certification_expiry <=', $thresholdDate)
                    ->where('certification_expiry >=', date('Y-m-d'))
                    ->where('status', 'AVAILABLE')
                    ->orderBy('certification_expiry', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get operator with current assignment details
     * 
     * @param int $operatorId
     * @return array|null
     */
    public function getWithAssignment($operatorId)
    {
        return $this->select('operators.*, 
                             contract_operator_assignments.assignment_start,
                             contract_operator_assignments.monthly_billing_rate,
                             kontrak.nomor_kontrak,
                             customers.nama_perusahaan')
                    ->join('contract_operator_assignments', 
                          'contract_operator_assignments.id = operators.current_assignment_id', 
                          'left')
                    ->join('kontrak', 
                          'kontrak.id = contract_operator_assignments.contract_id', 
                          'left')
                    ->join('customers', 
                          'customers.id = kontrak.customer_id', 
                          'left')
                    ->where('operators.id', $operatorId)
                    ->first();
    }
    
    /**
     * Mark operator as assigned
     * 
     * @param int $operatorId
     * @param int $assignmentId
     * @return bool
     */
    public function markAsAssigned($operatorId, $assignmentId)
    {
        return $this->update($operatorId, [
            'status' => 'ASSIGNED',
            'current_assignment_id' => $assignmentId
        ]);
    }
    
    /**
     * Mark operator as available
     * 
     * @param int $operatorId
     * @return bool
     */
    public function markAsAvailable($operatorId)
    {
        return $this->update($operatorId, [
            'status' => 'AVAILABLE',
            'current_assignment_id' => null
        ]);
    }
    
    /**
     * Get operators statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        $db = \Config\Database::connect();
        
        return [
            'total' => $this->where('deleted_at', null)->countAllResults(false),
            'available' => $this->where('status', 'AVAILABLE')->countAllResults(false),
            'assigned' => $this->where('status', 'ASSIGNED')->countAllResults(false),
            'on_leave' => $this->where('status', 'ON_LEAVE')->countAllResults(false),
            'inactive' => $this->where('status', 'INACTIVE')->countAllResults(false),
            'expert' => $this->where('certification_level', 'EXPERT')
                            ->where('deleted_at', null)
                            ->countAllResults(false),
            'certifications_expiring' => $this->getExpiringCertifications(30)
        ];
    }
    
    /**
     * Search operators by name or code
     * 
     * @param string $keyword
     * @return array
     */
    public function search($keyword)
    {
        return $this->groupStart()
                        ->like('operator_name', $keyword)
                        ->orLike('operator_code', $keyword)
                        ->orLike('nik', $keyword)
                        ->orLike('phone', $keyword)
                    ->groupEnd()
                    ->where('deleted_at', null)
                    ->orderBy('operator_name', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get operators for DataTables serverside
     * 
     * @param array $request
     * @return array
     */
    public function getForDataTable($request)
    {
        $builder = $this->builder();
        
        // Total records
        $totalRecords = $builder->countAllResults(false);
        
        // Apply search filter
        if (!empty($request['search']['value'])) {
            $search = $request['search']['value'];
            $builder->groupStart()
                    ->like('operator_name', $search)
                    ->orLike('operator_code', $search)
                    ->orLike('phone', $search)
                    ->groupEnd();
        }
        
        // Filtered records
        $filteredRecords = $builder->countAllResults(false);
        
        // Apply ordering
        if (isset($request['order'])) {
            $columnIndex = $request['order'][0]['column'];
            $columnName = $request['columns'][$columnIndex]['data'] ?? 'id';
            $direction = $request['order'][0]['dir'];
            $builder->orderBy($columnName, $direction);
        }
        
        // Apply pagination
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $builder->limit($length, $start);
        
        $data = $builder->get()->getResultArray();
        
        return [
            'draw' => $request['draw'] ?? 1,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }
}
