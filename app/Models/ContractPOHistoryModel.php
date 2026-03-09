<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Contract PO History Model
 * 
 * Manages Purchase Order history for contracts (monthly PO rotation)
 * 
 * @package    App\Models
 * @category   Marketing
 * @author     Optima Development Team
 * @created    2026-02-15
 */
class ContractPOHistoryModel extends Model
{
    protected $table = 'contract_po_history';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'contract_id',
        'po_number',
        'po_date',
        'po_value',
        'po_description',
        'effective_from',
        'effective_to',
        'po_document',
        'document_upload_date',
        'status',
        'superseded_by_po_id',
        'invoice_count',
        'total_invoiced',
        'customer_contact_person',
        'customer_email',
        'customer_phone',
        'notes',
        'internal_notes',
        'tags',
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
        'contract_id'    => 'required|integer',
        'po_number'      => 'required|max_length[100]',
        'po_date'        => 'required|valid_date',
        'effective_from' => 'required|valid_date',
        'status'         => 'required|in_list[ACTIVE,EXPIRED,SUPERSEDED,CANCELLED]'
    ];
    
    protected $validationMessages = [
        'po_number' => [
            'required' => 'PO number is required'
        ],
        'contract_id' => [
            'required' => 'Contract reference is required'
        ]
    ];
    
    // Cast fields
    protected $casts = [
        'po_date' => 'date',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'po_value' => 'decimal',
        'total_invoiced' => 'decimal',
        'document_upload_date' => 'datetime'
    ];
    
    /**
     * Get PO history by contract
     * 
     * @param int $contractId
     * @param bool $activeOnly
     * @return array
     */
    public function getByContract($contractId, $activeOnly = false)
    {
        $builder = $this->where('contract_id', $contractId);
        
        if ($activeOnly) {
            $builder->where('status', 'ACTIVE');
        }
        
        return $builder->orderBy('effective_from', 'DESC')->findAll();
    }
    
    /**
     * Get current active PO for contract
     * 
     * @param int $contractId
     * @return array|null
     */
    public function getCurrentPO($contractId)
    {
        return $this->where('contract_id', $contractId)
                    ->where('status', 'ACTIVE')
                    ->where('effective_to IS NULL')
                    ->orderBy('effective_from', 'DESC')
                    ->first();
    }
    
    /**
     * Get PO applicable for specific date or date range
     * 
     * @param int $contractId
     * @param string $date (or start date of range)
     * @param string|null $endDate (for range queries)
     * @return array|null
     */
    public function getPOForDate($contractId, $date, $endDate = null)
    {
        $builder = $this->where('contract_id', $contractId)
                        ->where('effective_from <=', $endDate ?? $date);
        
        $builder->groupStart()
                    ->where('effective_to >=', $date)
                    ->orWhere('effective_to IS NULL')
                ->groupEnd();
        
        return $builder->orderBy('effective_from', 'DESC')->first();
    }
    
    /**
     * Get PO applicable for billing period
     * 
     * @param int $contractId
     * @param string $periodStart
     * @param string $periodEnd
     * @return array|null
     */
    public function getPOForBillingPeriod($contractId, $periodStart, $periodEnd)
    {
        // Get PO that overlaps with the billing period
        return $this->where('contract_id', $contractId)
                    ->where('effective_from <=', $periodEnd)
                    ->groupStart()
                        ->where('effective_to >=', $periodStart)
                        ->orWhere('effective_to IS NULL')
                    ->groupEnd()
                    ->orderBy('effective_from', 'DESC')
                    ->first();
    }
    
    /**
     * Add new PO and expire previous one
     * 
     * @param array $data
     * @return int|bool New PO ID or false on failure
     */
    public function addNewPO($data)
    {
        $contractId = $data['contract_id'];
        $effectiveFrom = $data['effective_from'];
        
        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Expire previous active PO
            $this->expirePreviousPO($contractId, $effectiveFrom);
            
            // Insert new PO
            $data['status'] = 'ACTIVE';
            $poId = $this->insert($data);
            
            // Update main contract with latest PO
            $kontrakModel = new \App\Models\KontrakModel();
            $kontrakModel->update($contractId, [
                'customer_po_number' => $data['po_number']
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return false;
            }
            
            return $poId;
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Failed to add new PO: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Expire previous active PO when new one is added
     * 
     * @param int $contractId
     * @param string $newEffectiveFrom
     * @return bool
     */
    public function expirePreviousPO($contractId, $newEffectiveFrom)
    {
        // Calculate effective_to as day before new PO starts
        $effectiveTo = date('Y-m-d', strtotime($newEffectiveFrom . ' -1 day'));
        
        return $this->where('contract_id', $contractId)
                    ->where('status', 'ACTIVE')
                    ->set([
                        'effective_to' => $effectiveTo,
                        'status' => 'EXPIRED'
                    ])
                    ->update();
    }
    
    /**
     * Get full PO history timeline for contract
     * 
     * @param int $contractId
     * @return array
     */
    public function getHistoryTimeline($contractId)
    {
        return $this->select('contract_po_history.*,
                             users.nama as created_by_name')
                    ->join('users', 'users.id = contract_po_history.created_by', 'left')
                    ->where('contract_id', $contractId)
                    ->orderBy('effective_from', 'DESC')
                    ->findAll();
    }
    
    /**
     * Check for PO gaps in contract period
     * 
     * @param int $contractId
     * @return array Dates without PO coverage
     */
    public function checkForGaps($contractId)
    {
        // Get contract dates
        $db = \Config\Database::connect();
        $contract = $db->table('kontrak')
                       ->select('tanggal_mulai, tanggal_selesai')
                       ->where('id', $contractId)
                       ->get()
                       ->getRowArray();
        
        if (!$contract) {
            return [];
        }
        
        $gaps = [];
        $poHistory = $this->getByContract($contractId);
        
        if (empty($poHistory)) {
            // No PO at all
            return [[
                'gap_start' => $contract['tanggal_mulai'],
                'gap_end' => $contract['tanggal_selesai'] ?? date('Y-m-d'),
                'message' => 'No PO coverage for entire contract period'
            ]];
        }
        
        // Sort by effective_from
        usort($poHistory, function($a, $b) {
            return strtotime($a['effective_from']) - strtotime($b['effective_from']);
        });
        
        // Check for gaps between POs
        $previousEnd = $contract['tanggal_mulai'];
        
        foreach ($poHistory as $po) {
            $currentStart = $po['effective_from'];
            
            if (strtotime($currentStart) > strtotime($previousEnd)) {
                $gaps[] = [
                    'gap_start' => date('Y-m-d', strtotime($previousEnd . ' +1 day')),
                    'gap_end' => date('Y-m-d', strtotime($currentStart . ' -1 day')),
                    'message' => 'Gap between PO periods'
                ];
            }
            
            $previousEnd = $po['effective_to'] ?? date('Y-m-d');
        }
        
        return $gaps;
    }
    
    /**
     * Increment invoice count when invoice is created
     * 
     * @param int $poId
     * @param float $invoiceAmount
     * @return bool
     */
    public function recordInvoice($poId, $invoiceAmount)
    {
        $po = $this->find($poId);
        if (!$po) {
            return false;
        }
        
        return $this->update($poId, [
            'invoice_count' => $po['invoice_count'] + 1,
            'total_invoiced' => $po['total_invoiced'] + $invoiceAmount
        ]);
    }
    
    /**
     * Get POs expiring soon
     * 
     * @param int $daysThreshold
     * @return array
     */
    public function getExpiringSoon($daysThreshold = 7)
    {
        $thresholdDate = date('Y-m-d', strtotime("+{$daysThreshold} days"));
        
        return $this->select('contract_po_history.*,
                             kontrak.nomor_kontrak,
                             customers.nama_perusahaan')
                    ->join('kontrak', 'kontrak.id = contract_po_history.contract_id')
                    ->join('customers', 'customers.id = kontrak.customer_id')
                    ->where('contract_po_history.status', 'ACTIVE')
                    ->where('contract_po_history.effective_to <=', $thresholdDate)
                    ->where('contract_po_history.effective_to >=', date('Y-m-d'))
                    ->orderBy('contract_po_history.effective_to', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get PO statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        return [
            'total' => $this->countAllResults(false),
            'active' => $this->where('status', 'ACTIVE')->countAllResults(false),
            'expired' => $this->where('status', 'EXPIRED')->countAllResults(false),
            'expiring_soon' => count($this->getExpiringSoon(7)),
            'this_month' => $this->where('MONTH(effective_from)', date('m'))
                                 ->where('YEAR(effective_from)', date('Y'))
                                 ->countAllResults(false)
        ];
    }
    
    /**
     * Cancel PO
     * 
     * @param int $poId
     * @param string $reason
     * @return bool
     */
    public function cancelPO($poId, $reason)
    {
        return $this->update($poId, [
            'status' => 'CANCELLED',
            'internal_notes' => $reason,
            'effective_to' => date('Y-m-d')
        ]);
    }
    
    /**
     * Search PO by number across all contracts
     * 
     * @param string $poNumber
     * @return array
     */
    public function searchByPONumber($poNumber)
    {
        return $this->select('contract_po_history.*,
                             kontrak.nomor_kontrak,
                             customers.nama_perusahaan')
                    ->join('kontrak', 'kontrak.id = contract_po_history.contract_id')
                    ->join('customers', 'customers.id = kontrak.customer_id')
                    ->like('contract_po_history.po_number', $poNumber)
                    ->orderBy('contract_po_history.effective_from', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get POs by status with contract details
     * 
     * @param string $status
     * @return array
     */
    public function getByStatus($status)
    {
        return $this->select('contract_po_history.*,
                             kontrak.nomor_kontrak,
                             customers.nama_perusahaan,
                             (SELECT cl.nama_lokasi FROM kontrak_unit ku JOIN customer_location cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = kontrak.id LIMIT 1) as nama_lokasi')
                    ->join('kontrak', 'kontrak.id = contract_po_history.contract_id')
                    ->join('customers', 'customers.id = kontrak.customer_id')
                    ->where('contract_po_history.status', $status)
                    ->orderBy('contract_po_history.effective_from', 'DESC')
                    ->findAll();
    }
}
