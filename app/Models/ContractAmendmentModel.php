<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Contract Amendment Model
 * Manages price/term changes to existing contracts
 * Used for mid-term adjustments without creating new contract
 */
class ContractAmendmentModel extends Model
{
    protected $table = 'contract_amendments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'parent_contract_id',
        'amendment_number',
        'amendment_date',
        'reason',
        'price_change_percent',
        'new_monthly_rate',
        'effective_date',
        'created_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'parent_contract_id' => 'required|integer',
        'amendment_number' => 'required|string|max_length[50]|is_unique[contract_amendments.amendment_number]',
        'amendment_date' => 'required|valid_date',
        'reason' => 'required|string',
        'new_monthly_rate' => 'required|decimal',
        'effective_date' => 'required|valid_date',
        'created_by' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'amendment_number' => [
            'is_unique' => 'Amendment number already exists'
        ]
    ];
    
    /**
     * Create a new amendment for a contract
     * 
     * @param int $contractId Parent contract ID
     * @param array $data Amendment data (reason, new_rate, effective_date, etc)
     * @return int|false Amendment ID or false on failure
     */
    public function createAmendment(int $contractId, array $data)
    {
        // Validate contract exists
        $kontrakModel = new \App\Models\KontrakModel();
        $contract = $kontrakModel->find($contractId);
        
        if (!$contract) {
            return false;
        }
        
        // Generate amendment number if not provided
        if (empty($data['amendment_number'])) {
            $data['amendment_number'] = $this->generateAmendmentNumber($contractId);
        }
        
        // Calculate price change percentage if not provided
        if (empty($data['price_change_percent']) && !empty($data['new_monthly_rate'])) {
            $oldRate = floatval($contract['nilai_total'] ?? 0) / max(1, intval($contract['total_units'] ?? 1));
            $newRate = floatval($data['new_monthly_rate']);
            
            if ($oldRate > 0) {
                $data['price_change_percent'] = (($newRate - $oldRate) / $oldRate) * 100;
            }
        }
        
        // Set parent contract
        $data['parent_contract_id'] = $contractId;
        
        // Set amendment date if not provided
        if (empty($data['amendment_date'])) {
            $data['amendment_date'] = date('Y-m-d');
        }
        
        if ($this->insert($data)) {
            return $this->getInsertID();
        }
        
        return false;
    }
    
    /**
     * Get all amendments for a contract
     * 
     * @param int $contractId Contract ID
     * @return array List of amendments ordered by effective_date
     */
    public function getAmendmentsByContract(int $contractId): array
    {
        return $this->where('parent_contract_id', $contractId)
                    ->orderBy('effective_date', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get effective rate for a specific billing period
     * Finds applicable amendment based on effective_date
     * 
     * @param int $contractId Contract ID
     * @param string $billingDate Date in billing period (Y-m-d format)
     * @return float|null Effective rate or null if no amendment applicable
     */
    public function getEffectiveRate(int $contractId, string $billingDate): ?float
    {
        $amendment = $this->where('parent_contract_id', $contractId)
                          ->where('effective_date <=', $billingDate)
                          ->orderBy('effective_date', 'DESC')
                          ->first();
        
        if ($amendment) {
            return floatval($amendment['new_monthly_rate']);
        }
        
        return null;
    }
    
    /**
     * Generate amendment number
     * Format: AMD/CONTRACT_NO/NNN
     * 
     * @param int $contractId Parent contract ID
     * @return string Amendment number
     */
    private function generateAmendmentNumber(int $contractId): string
    {
        $kontrakModel = new \App\Models\KontrakModel();
        $contract = $kontrakModel->find($contractId);
        
        if (!$contract) {
            return 'AMD/' . date('YmdHis');
        }
        
        $contractNo = preg_replace('/[^A-Z0-9]/', '', $contract['no_kontrak']);
        
        $count = $this->where('parent_contract_id', $contractId)->countAllResults();
        $seq = $count + 1;
        
        return 'AMD/' . $contractNo . '/' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Validate amendment effective date is within contract period
     * 
     * @param int $contractId Contract ID
     * @param string $effectiveDate Effective date to validate
     * @return array Validation result ['valid' => bool, 'errors' => array]
     */
    public function validateEffectiveDate(int $contractId, string $effectiveDate): array
    {
        $result = ['valid' => true, 'errors' => [], 'warnings' => []];
        
        $kontrakModel = new \App\Models\KontrakModel();
        $contract = $kontrakModel->find($contractId);
        
        if (!$contract) {
            $result['valid'] = false;
            $result['errors'][] = 'Contract not found';
            return $result;
        }
        
        $effectiveTimestamp = strtotime($effectiveDate);
        $startTimestamp = strtotime($contract['tanggal_mulai']);
        $endTimestamp = strtotime($contract['tanggal_berakhir']);
        
        // Check if effective date is within contract period
        if ($effectiveTimestamp < $startTimestamp) {
            $result['valid'] = false;
            $result['errors'][] = 'Effective date cannot be before contract start date';
        }
        
        if ($effectiveTimestamp > $endTimestamp) {
            $result['valid'] = false;
            $result['errors'][] = 'Effective date cannot be after contract end date';
        }
        
        // Warning if effective date is mid-month (for monthly billing)
        if ($contract['jenis_sewa'] === 'BULANAN') {
            $dayOfMonth = date('d', $effectiveTimestamp);
            if ($dayOfMonth != '01') {
                $result['warnings'][] = 'Effective date is mid-month. May require prorated adjustment in billing.';
            }
        }
        
        return $result;
    }
}
