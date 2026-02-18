<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Contract Operator Assignment Model
 * 
 * Manages operator assignments to rental contracts
 * 
 * @package    App\Models
 * @category   Marketing
 * @author     Optima Development Team
 * @created    2026-02-15
 */
class OperatorAssignmentModel extends Model
{
    protected $table = 'contract_operator_assignments';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'contract_id',
        'operator_id',
        'assignment_start',
        'assignment_end',
        'actual_end_date',
        'billing_type',
        'monthly_billing_rate',
        'daily_billing_rate',
        'hourly_billing_rate',
        'work_hours_per_day',
        'work_days_per_week',
        'overtime_allowed',
        'overtime_rate_multiplier',
        'equipment_assigned',
        'location_id',
        'shift_schedule',
        'status',
        'performance_rating',
        'performance_notes',
        'billing_notes',
        'contract_notes',
        'replacement_for_assignment_id',
        'approved_by',
        'approved_at',
        'rejection_reason',
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
        'contract_id'          => 'required|integer',
        'operator_id'          => 'required|integer',
        'assignment_start'     => 'required|valid_date',
        'monthly_billing_rate' => 'required|decimal',
        'status'               => 'required|in_list[PENDING,ACTIVE,COMPLETED,CANCELLED,TERMINATED]'
    ];
    
    protected $validationMessages = [
        'contract_id' => [
            'required' => 'Contract is required'
        ],
        'operator_id' => [
            'required' => 'Operator is required'
        ]
    ];
    
    // Cast fields
    protected $casts = [
        'assignment_start' => 'date',
        'assignment_end' => 'date',
        'actual_end_date' => 'date',
        'monthly_billing_rate' => 'decimal',
        'daily_billing_rate' => 'decimal',
        'hourly_billing_rate' => 'decimal',
        'performance_rating' => 'decimal',
        'approved_at' => 'datetime'
    ];
    
    /**
     * Get assignments by contract
     * 
     * @param int $contractId
     * @param string|null $status
     * @return array
     */
    public function getByContract($contractId, $status = null)
    {
        $builder = $this->select('contract_operator_assignments.*, 
                                  operators.operator_name, 
                                  operators.operator_code,
                                  operators.certification_level,
                                  operators.phone')
                        ->join('operators', 'operators.id = contract_operator_assignments.operator_id')
                        ->where('contract_operator_assignments.contract_id', $contractId);
        
        if ($status) {
            $builder->where('contract_operator_assignments.status', $status);
        }
        
        return $builder->orderBy('assignment_start', 'DESC')->findAll();
    }
    
    /**
     * Get active assignments by contract
     * 
     * @param int $contractId
     * @return array
     */
    public function getActiveByContract($contractId)
    {
        return $this->getByContract($contractId, 'ACTIVE');
    }
    
    /**
     * Get assignments by operator
     * 
     * @param int $operatorId
     * @param string|null $status
     * @return array
     */
    public function getByOperator($operatorId, $status = null)
    {
        $builder = $this->select('contract_operator_assignments.*, 
                                  kontrak.nomor_kontrak,
                                  customers.nama_perusahaan,
                                  customer_location.alamat as location_address')
                        ->join('kontrak', 'kontrak.id = contract_operator_assignments.contract_id')
                        ->join('customers', 'customers.id = kontrak.customer_id')
                        ->join('customer_location', 
                              'customer_location.id = contract_operator_assignments.location_id', 
                              'left')
                        ->where('contract_operator_assignments.operator_id', $operatorId);
        
        if ($status) {
            $builder->where('contract_operator_assignments.status', $status);
        }
        
        return $builder->orderBy('assignment_start', 'DESC')->findAll();
    }
    
    /**
     * Get current active assignment for operator
     * 
     * @param int $operatorId
     * @return array|null
     */
    public function getCurrentAssignment($operatorId)
    {
        return $this->select('contract_operator_assignments.*, 
                             kontrak.nomor_kontrak,
                             customers.nama_perusahaan')
                    ->join('kontrak', 'kontrak.id = contract_operator_assignments.contract_id')
                    ->join('customers', 'customers.id = kontrak.customer_id')
                    ->where('contract_operator_assignments.operator_id', $operatorId)
                    ->where('contract_operator_assignments.status', 'ACTIVE')
                    ->orderBy('assignment_start', 'DESC')
                    ->first();
    }
    
    /**
     * Check if operator has overlapping assignment
     * 
     * @param int $operatorId
     * @param string $startDate
     * @param string|null $endDate
     * @param int|null $excludeAssignmentId
     * @return bool
     */
    public function hasOverlappingAssignment($operatorId, $startDate, $endDate = null, $excludeAssignmentId = null)
    {
        $builder = $this->where('operator_id', $operatorId)
                        ->where('status', 'ACTIVE');
        
        if ($excludeAssignmentId) {
            $builder->where('id !=', $excludeAssignmentId);
        }
        
        // Check for overlaps
        $builder->groupStart()
                    ->where('assignment_start <=', $endDate ?? $startDate)
                    ->groupStart()
                        ->where('assignment_end >=', $startDate)
                        ->orWhere('assignment_end IS NULL')
                    ->groupEnd()
                ->groupEnd();
        
        return $builder->countAllResults() > 0;
    }
    
    /**
     * Complete assignment
     * 
     * @param int $assignmentId
     * @param string $endDate
     * @param float|null $rating
     * @param string|null $notes
     * @return bool
     */
    public function completeAssignment($assignmentId, $endDate, $rating = null, $notes = null)
    {
        $data = [
            'status' => 'COMPLETED',
            'actual_end_date' => $endDate
        ];
        
        if ($rating !== null) {
            $data['performance_rating'] = $rating;
        }
        
        if ($notes !== null) {
            $data['performance_notes'] = $notes;
        }
        
        $result = $this->update($assignmentId, $data);
        
        // Update operator status back to available
        if ($result) {
            $assignment = $this->find($assignmentId);
            if ($assignment) {
                $operatorModel = new OperatorModel();
                $operatorModel->markAsAvailable($assignment['operator_id']);
            }
        }
        
        return $result;
    }
    
    /**
     * Cancel assignment
     * 
     * @param int $assignmentId
     * @param string $reason
     * @return bool
     */
    public function cancelAssignment($assignmentId, $reason)
    {
        $assignment = $this->find($assignmentId);
        if (!$assignment) {
            return false;
        }
        
        $result = $this->update($assignmentId, [
            'status' => 'CANCELLED',
            'rejection_reason' => $reason,
            'actual_end_date' => date('Y-m-d')
        ]);
        
        // Update operator status
        if ($result && $assignment['status'] === 'ACTIVE') {
            $operatorModel = new OperatorModel();
            $operatorModel->markAsAvailable($assignment['operator_id']);
        }
        
        return $result;
    }
    
    /**
     * Get assignments for invoice generation (active in billing period)
     * 
     * @param int $contractId
     * @param string $periodStart
     * @param string $periodEnd
     * @return array
     */
    public function getForBillingPeriod($contractId, $periodStart, $periodEnd)
    {
        return $this->select('contract_operator_assignments.*, 
                             operators.operator_name,
                             operators.operator_code')
                    ->join('operators', 'operators.id = contract_operator_assignments.operator_id')
                    ->where('contract_operator_assignments.contract_id', $contractId)
                    ->where('contract_operator_assignments.status', 'ACTIVE')
                    ->where('contract_operator_assignments.assignment_start <=', $periodEnd)
                    ->groupStart()
                        ->where('contract_operator_assignments.assignment_end >=', $periodStart)
                        ->orWhere('contract_operator_assignments.assignment_end IS NULL')
                    ->groupEnd()
                    ->findAll();
    }
    
    /**
     * Calculate billing amount for assignment in period
     * 
     * @param array $assignment
     * @param string $periodStart
     * @param string $periodEnd
     * @return float
     */
    public function calculateBillingAmount($assignment, $periodStart, $periodEnd)
    {
        $billingType = $assignment['billing_type'];
        
        // Determine actual billing period (intersection of assignment period and billing period)
        $actualStart = max($assignment['assignment_start'], $periodStart);
        $actualEnd = min($assignment['assignment_end'] ?? $periodEnd, $periodEnd);
        
        $startDate = new \DateTime($actualStart);
        $endDate = new \DateTime($actualEnd);
        $billingDays = $startDate->diff($endDate)->days + 1;
        
        switch ($billingType) {
            case 'MONTHLY_PACKAGE':
                // Pro-rate if partial month
                $daysInMonth = date('t', strtotime($periodStart));
                $monthlyRate = $assignment['monthly_billing_rate'];
                return ($billingDays >= $daysInMonth) ? $monthlyRate : ($monthlyRate / $daysInMonth * $billingDays);
                
            case 'DAILY_RATE':
                return $assignment['daily_billing_rate'] * $billingDays;
                
            case 'HOURLY_RATE':
                // Would need actual hours tracked
                $hoursPerDay = $assignment['work_hours_per_day'] ?? 8;
                return $assignment['hourly_billing_rate'] * $hoursPerDay * $billingDays;
                
            default:
                return $assignment['monthly_billing_rate'];
        }
    }
    
    /**
     * Get assignments statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        return [
            'total' => $this->countAllResults(false),
            'active' => $this->where('status', 'ACTIVE')->countAllResults(false),
            'pending' => $this->where('status', 'PENDING')->countAllResults(false),
            'completed' => $this->where('status', 'COMPLETED')->countAllResults(false),
            'this_month' => $this->where('MONTH(assignment_start)', date('m'))
                                 ->where('YEAR(assignment_start)', date('Y'))
                                 ->countAllResults(false)
        ];
    }
    
    /**
     * Get assignment with full details
     * 
     * @param int $assignmentId
     * @return array|null
     */
    public function getWithDetails($assignmentId)
    {
        return $this->select('contract_operator_assignments.*, 
                             operators.operator_name,
                             operators.operator_code,
                             operators.certification_level,
                             operators.phone,
                             operators.email,
                             kontrak.nomor_kontrak,
                             customers.nama_perusahaan,
                             customer_location.nama_lokasi,
                             customer_location.alamat')
                    ->join('operators', 'operators.id = contract_operator_assignments.operator_id')
                    ->join('kontrak', 'kontrak.id = contract_operator_assignments.contract_id')
                    ->join('customers', 'customers.id = kontrak.customer_id')
                    ->join('customer_location', 
                          'customer_location.id = contract_operator_assignments.location_id', 
                          'left')
                    ->where('contract_operator_assignments.id', $assignmentId)
                    ->first();
    }
}
