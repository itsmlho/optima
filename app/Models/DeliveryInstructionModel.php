<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryInstructionModel extends Model
{
    protected $table = 'delivery_instructions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'spk_id','jenis_spk','nomor_di','po_kontrak_nomor','pelanggan','lokasi','tanggal_kirim','status','status_di','catatan','dibuat_oleh','dibuat_pada','diperbarui_pada',
        'jenis_perintah_kerja_id','tujuan_perintah_kerja_id','status_eksekusi_workflow_id',
        'perencanaan_tanggal_approve','estimasi_sampai','nama_supir','no_hp_supir','no_sim_supir','kendaraan','no_polisi_kendaraan',
        'berangkat_tanggal_approve','catatan_berangkat',
        'sampai_tanggal_approve','catatan_sampai',
        'invoice_generated','invoice_generated_at',
        // Contract linking fields
        'contract_id','tarik_contract_id','pelanggan_id','customer_location_id','bast_date','billing_start_date',
        'contract_linked_at','contract_linked_by',
    ];
    
    // Explicitly disable automatic timestamps since our table uses custom field names
    protected $useTimestamps = false;
    protected $createdField = '';  // Disable automatic created_at
    protected $updatedField = '';  // Disable automatic updated_at

    /** Generate next DI number with prefix DI/YYYYMM/NNN */
    public function generateNextNumber(): string
    {
        $prefix = 'DI/'.date('Ym').'/';
        $row = $this->db->table($this->table)->like('nomor_di', $prefix)->orderBy('id','DESC')->get()->getRowArray();
        $seq = 1;
        if ($row && isset($row['nomor_di'])) {
            $parts = explode('/', $row['nomor_di']);
            $seq = isset($parts[2]) ? ((int)$parts[2] + 1) : 1;
        }
        return $prefix . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Determine initial status based on parent SPK's contract status
     * Called during DI creation to set appropriate status
     * 
     * @param int $spkId Parent SPK ID
     * @return string Status ('DIAJUKAN' or 'DISETUJUI')
     */
    public function determineInitialStatus(int $spkId): string
    {
        $spkModel = new \App\Models\SpkModel();
        $hasContract = $spkModel->hasContract($spkId);
        
        // Return valid ENUM values for status_di column
        return $hasContract ? 'DISETUJUI' : 'DIAJUKAN';
    }

    /**
     * Inherit contract information from parent SPK
     * Used by trigger or manual sync after SPK linking
     * Contract relationship is handled via spk_id → kontrak_id
     * 
     * @param int $diId Delivery Instruction ID
     * @return bool Success status
     */
    public function inheritContractFromSPK(int $diId): bool
    {
        $di = $this->find($diId);
        
        if (!$di || !$di['spk_id']) {
            return false;
        }
        
        $spkModel = new \App\Models\SpkModel();
        $spk = $spkModel->find($di['spk_id']);
        
        if (!$spk || !$spk['kontrak_id']) {
            return false;
        }
        
        // Update DI status when SPK is linked to contract
        // Contract info accessed via JOIN: spk.kontrak_id
        return (bool) $this->update($diId, [
            'status_di' => 'DISETUJUI',
            'diperbarui_pada' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get Delivery Instructions in DIAJUKAN status (waiting for contract)
     * Used for dashboard alerts and follow-up monitoring
     * 
     * @return array List of unlinked DIs with details
     */
    public function getUnlinkedDeliveries(): array
    {
        return $this->select('delivery_instructions.*, 
                             spk.nomor_spk, spk.pelanggan as spk_customer,
                             quotations.quotation_number,
                             DATEDIFF(NOW(), delivery_instructions.dibuat_pada) as days_pending')
                    ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
                    ->join('quotation_specifications qs', 'qs.id_specification = spk.quotation_specification_id', 'left')
                    ->join('quotations', 'quotations.id_quotation = qs.id_quotation', 'left')
                    ->where('delivery_instructions.status_di', 'DIAJUKAN')
                    ->orderBy('delivery_instructions.dibuat_pada', 'DESC')
                    ->findAll();
    }

    /**
     * Validate if DI is ready for invoicing (billing readiness check)
     * Implements invoice locking mechanism - THREE LAYER VALIDATION
     * 
     * @param int $diId Delivery Instruction ID
     * @return array Empty if valid, array of error messages if not ready
     */
    public function validateBillingReadiness(int $diId): array
    {
        $errors = [];
        
        $di = $this->select('delivery_instructions.*, spk.nomor_spk')
                   ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
                   ->find($diId);
        
        if (!$di) {
            $errors[] = 'Delivery Instruction not found';
            return $errors;
        }
        
        // Check 1: Contract must be linked
        if ($di['contract_id'] === null) {
            $errors[] = 'Contract not linked. Please link contract to SPK ' . ($di['nomor_spk'] ?? '') . ' first.';
        }
        
        // Check 2: Status must not be AWAITING_CONTRACT
        if ($di['status'] === 'AWAITING_CONTRACT') {
            $errors[] = 'Delivery in AWAITING_CONTRACT status. Contract linking required.';
        }
        
        // Check 3: BAST date required for billing calculation
        if (empty($di['bast_date'])) {
            $errors[] = 'BAST (Berita Acara Serah Terima) date required for billing calculation.';
        }
        
        // Check 4: Delivery must be completed
        if (!in_array($di['status'], ['DELIVERED', 'COMPLETED'])) {
            $errors[] = 'Delivery must be completed before invoicing. Current status: ' . $di['status'];
        }
        
        return $errors;
    }

    /**
     * Set BAST date and auto-calculate billing start date
     * 
     * @param int $diId Delivery Instruction ID
     * @param string $bastDate BAST date (Y-m-d format)
     * @return bool Success status
     */
    public function setBillingStartDate(int $diId, string $bastDate): bool
    {
        // Get contract to determine billing type
        $di = $this->select('delivery_instructions.*, kontrak.jenis_sewa')
                   ->join('kontrak', 'kontrak.id = delivery_instructions.contract_id', 'left')
                   ->find($diId);
        
        if (!$di) {
            return false;
        }
        
        // Calculate billing_start_date based on contract type
        $billingStartDate = $bastDate; // Default: use BAST date itself
        
        if (isset($di['jenis_sewa']) && $di['jenis_sewa'] === 'BULANAN') {
            // For monthly rental: Start of month containing BAST date
            $bastTimestamp = strtotime($bastDate);
            $billingStartDate = date('Y-m-01', $bastTimestamp);
        }
        // For HARIAN (daily): Use BAST date as-is
        
        return (bool) $this->update($diId, [
            'bast_date' => $bastDate,
            'billing_start_date' => $billingStartDate,
            'diperbarui_pada' => date('Y-m-d H:i:s'),
        ]);
    }
}

