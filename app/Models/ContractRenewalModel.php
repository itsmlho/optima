<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Contract Renewal Model
 * Manages contract renewals, linking old and new contracts
 * Used when contract period extends without unit re-delivery
 */
class ContractRenewalModel extends Model
{
    protected $table = 'contract_renewals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'original_contract_id',
        'renewed_contract_id',
        'renewal_date',
        'same_location',
        'notes',
        'created_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null; // No updated_at field
    
    protected $validationRules = [
        'original_contract_id' => 'required|integer',
        'renewed_contract_id' => 'required|integer',
        'renewal_date' => 'required|valid_date',
        'created_by' => 'required|integer'
    ];
    
    /**
     * Create renewal record linking old and new contracts
     * 
     * @param int $originalContractId Original contract ID
     * @param int $renewedContractId New contract ID
     * @param array $data Additional data (notes, same_location, etc)
     * @return int|false Renewal ID or false on failure
     */
    public function createRenewal(int $originalContractId, int $renewedContractId, array $data)
    {
        // Validate contracts exist
        $kontrakModel = new \App\Models\KontrakModel();
        
        $originalContract = $kontrakModel->find($originalContractId);
        $renewedContract = $kontrakModel->find($renewedContractId);
        
        if (!$originalContract || !$renewedContract) {
            return false;
        }
        
        // Check if renewed contract already has a renewal record
        $existingRenewal = $this->where('renewed_contract_id', $renewedContractId)->first();
        if ($existingRenewal) {
            // Already linked
            return false;
        }
        
        // Prepare data
        $renewalData = [
            'original_contract_id' => $originalContractId,
            'renewed_contract_id' => $renewedContractId,
            'renewal_date' => $data['renewal_date'] ?? date('Y-m-d'),
            'same_location' => $data['same_location'] ?? true,
            'notes' => $data['notes'] ?? '',
            'created_by' => $data['created_by']
        ];
        
        if ($this->insert($renewalData)) {
            // If same location, copy billing schedule to new contract
            if ($renewalData['same_location']) {
                $this->migrateBillingSchedule($originalContractId, $renewedContractId);
            }
            
            return $this->getInsertID();
        }
        
        return false;
    }
    
    /**
     * Get full renewal chain for a contract
     * Returns chronological list from original to latest renewal
     * 
     * @param int $contractId Any contract ID in the chain
     * @return array Array of contracts with renewal information
     */
    public function getRenewalChain(int $contractId): array
    {
        $chain = [];
        $kontrakModel = new \App\Models\KontrakModel();
        
        // Find the original contract (root of chain)
        $originalId = $this->findOriginalContract($contractId);
        
        // Get original contract
        $original = $kontrakModel->find($originalId);
        if ($original) {
            $chain[] = array_merge($original, ['is_original' => true, 'renewal_info' => null]);
        }
        
        // Get all renewals in chronological order
        $renewals = $this->where('original_contract_id', $originalId)
                         ->orderBy('renewal_date', 'ASC')
                         ->findAll();
        
        foreach ($renewals as $renewal) {
            $contract = $kontrakModel->find($renewal['renewed_contract_id']);
            if ($contract) {
                $chain[] = array_merge($contract, [
                    'is_original' => false,
                    'renewal_info' => $renewal
                ]);
            }
        }
        
        return $chain;
    }
    
    /**
     * Find the original (root) contract in renewal chain
     * 
     * @param int $contractId Any contract ID in the chain
     * @return int Original contract ID
     */
    private function findOriginalContract(int $contractId): int
    {
        // Check if this contract is a renewal
        $renewal = $this->where('renewed_contract_id', $contractId)->first();
        
        if ($renewal) {
            // This is a renewed contract, trace back to original
            return $this->findOriginalContract($renewal['original_contract_id']);
        }
        
        // This is the original contract
        return $contractId;
    }
    
    /**
     * Check if contract can be renewed
     * 
     * @param int $contractId Contract ID to check
     * @return array Eligibility result ['eligible' => bool, 'reasons' => array]
     */
    public function checkRenewalEligibility(int $contractId): array
    {
        $result = ['eligible' => true, 'reasons' => []];
        
        $kontrakModel = new \App\Models\KontrakModel();
        $contract = $kontrakModel->find($contractId);
        
        if (!$contract) {
            $result['eligible'] = false;
            $result['reasons'][] = 'Contract not found';
            return $result;
        }
        
        // Check if already renewed
        $existingRenewal = $this->where('original_contract_id', $contractId)
                                ->orWhere('renewed_contract_id', $contractId)
                                ->first();
        
        if ($existingRenewal) {
            // Contract is part of renewal chain - can still renew
            // Just inform user
            $result['reasons'][] = 'Contract is part of renewal chain';
        }
        
        // Check contract status
        if ($contract['status'] === 'CANCELLED') {
            $result['eligible'] = false;
            $result['reasons'][] = 'Cannot renew cancelled contract';
        }
        
        // Check if contract is near end date or expired
        $endDate = strtotime($contract['tanggal_berakhir']);
        $today = strtotime(date('Y-m-d'));
        $daysUntilEnd = ($endDate - $today) / (60 * 60 * 24);
        
        if ($daysUntilEnd > 60) {
            $result['reasons'][] = 'Contract expires in ' . round($daysUntilEnd) . ' days. Consider renewing closer to end date.';
        }
        
        return $result;
    }
    
    /**
     * Migrate billing schedule from old contract to new contract
     * Used for seamless billing continuation
     * 
     * @param int $originalContractId Original contract ID
     * @param int $renewedContractId New contract ID
     * @return bool Success status
     */
    private function migrateBillingSchedule(int $originalContractId, int $renewedContractId): bool
    {
        $scheduleModel = new \App\Models\RecurringBillingScheduleModel();
        
        // Get original schedule
        $originalSchedule = $scheduleModel->where('contract_id', $originalContractId)->first();
        
        if (!$originalSchedule) {
            return false;
        }
        
        // Mark original schedule as completed
        $scheduleModel->update($originalSchedule['id'], ['status' => 'COMPLETED']);
        
        // Create new schedule for renewed contract
        $kontrakModel = new \App\Models\KontrakModel();
        $renewedContract = $kontrakModel->find($renewedContractId);
        
        if ($renewedContract) {
            $newScheduleData = [
                'contract_id' => $renewedContractId,
                'frequency' => $originalSchedule['frequency'],
                'next_billing_date' => $renewedContract['tanggal_mulai'], // Start billing from new contract start
                'auto_generate' => $originalSchedule['auto_generate'],
                'status' => 'ACTIVE'
            ];
            
            return $scheduleModel->createSchedule($renewedContractId, $originalSchedule['frequency']);
        }
        
        return false;
    }
}
