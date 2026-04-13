<?php

namespace App\Models;

use CodeIgniter\Model;

class SpkModel extends Model
{
    protected $table            = 'spk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_spk','jenis_spk','kontrak_id','kontrak_spesifikasi_id','quotation_specification_id','jumlah_unit','po_kontrak_nomor','pelanggan','pic','kontak','lokasi',
        'delivery_plan','spesifikasi','catatan','status',
        'dibuat_oleh','dibuat_pada','diperbarui_pada',
        'contract_linked_at','contract_linked_by','source_type','customer_id',
        'persiapan_unit_mekanik','persiapan_unit_estimasi_mulai','persiapan_unit_estimasi_selesai','persiapan_unit_tanggal_approve',
        'persiapan_unit_id','persiapan_aksesoris_tersedia',
        'fabrikasi_mekanik','fabrikasi_estimasi_mulai','fabrikasi_estimasi_selesai','fabrikasi_tanggal_approve',
        'fabrikasi_attachment_id',
        'painting_mekanik','painting_estimasi_mulai','painting_estimasi_selesai','painting_tanggal_approve',
        'pdi_mekanik','pdi_estimasi_mulai','pdi_estimasi_selesai','pdi_tanggal_approve','pdi_catatan',
        'jenis_perintah_kerja_id','tujuan_perintah_kerja_id','status_eksekusi_workflow_id'
    ];

    protected $useTimestamps = false;

    /** Generate next SPK number with prefix SPK/YYYYMM/NNN */
    public function generateNextNumber(): string
    {
        $prefix = 'SPK/' . date('Ym') . '/';

        // Get the latest SPK number for this month
        // NOTE: Do NOT start a nested transaction here - this method is called from within
        // an outer transaction in createSPKFromQuotation(). Using transException(true) inside
        // a nested call permanently pollutes the shared DB connection's transException state,
        // causing auto-rollback on any subsequent query failure.
        $row = $this->db->query(
            "SELECT nomor_spk FROM {$this->table} WHERE nomor_spk LIKE ? ORDER BY id DESC LIMIT 1",
            [$prefix . '%']
        )->getRowArray();

        $seq = 1;
        if ($row && isset($row['nomor_spk'])) {
            $parts = explode('/', $row['nomor_spk']);
            $seq = isset($parts[2]) ? ((int)$parts[2] + 1) : 1;
        }

        // Ensure the generated number is unique (handles concurrent inserts)
        $attempts = 0;
        do {
            $newNumber = $prefix . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
            $exists    = $this->db->table($this->table)->where('nomor_spk', $newNumber)->countAllResults();
            if ($exists > 0) {
                $seq++;
            }
            $attempts++;
        } while ($exists > 0 && $attempts < 100);

        return $newNumber;
    }

    /** Update status and record history (best-effort). Requires SpkStatusHistoryModel table available. */
    public function setStatusWithHistory(int $id, string $newStatus, ?int $userId = null, ?string $note = null): bool
    {
        $prev = $this->select('status')->find($id);
        $ok = $this->update($id, ['status'=>$newStatus, 'diperbarui_pada'=>date('Y-m-d H:i:s')]);
        if ($ok && $prev && isset($prev['status'])) {
            try {
                $hist = new \App\Models\SpkStatusHistoryModel();
                $hist->insert([
                    'spk_id' => $id,
                    'status_from' => $prev['status'],
                    'status_to' => $newStatus,
                    'changed_by' => $userId ?: 0,
                    'note' => $note,
                    'changed_at' => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) { /* ignore */ }
        }
        return (bool)$ok;
    }

    /** Persist selected unit/attachment into spesifikasi JSON and move to READY with history. */
    public function assignSelectionsAndReady(int $spkId, array $selection, ?int $userId = null): bool
    {
        $spk = $this->find($spkId);
        if (!$spk) return false;
        $spec = [];
        if (!empty($spk['spesifikasi'])) {
            $decoded = json_decode($spk['spesifikasi'], true);
            if (is_array($decoded)) $spec = $decoded;
        }
        $spec['selected'] = $selection;
        $ok = $this->update($spkId, [
            'spesifikasi' => json_encode($spec, JSON_UNESCAPED_UNICODE),
            'diperbarui_pada' => date('Y-m-d H:i:s'),
        ]);
        if (!$ok) return false;
        return $this->setStatusWithHistory($spkId, 'READY', $userId, 'Unit & attachment ditetapkan oleh Service');
    }

    /**
     * Create SPK from Quotation Specification (without Contract)
     * Enables express workflow for operational flexibility
     * 
     * @param int $quotationSpecId FK to quotation_specifications
     * @param array $customerData Customer and delivery information
     * @param int $userId User creating the SPK
     * @return int|false SPK ID or false on failure
     */
    public function createFromQuotation(int $quotationSpecId, array $customerData, int $userId)
    {
        // Fetch quotation specification
        $quotationSpecModel = new \App\Models\QuotationSpecificationModel();
        $spec = $quotationSpecModel->find($quotationSpecId);
        
        if (!$spec) {
            return false;
        }
        
        // Fetch parent quotation for customer info
        $quotationModel = new \App\Models\QuotationModel();
        $quotation = $quotationModel->find($spec['id_quotation']);
        
        if (!$quotation) {
            return false;
        }
        
        // Build specification JSON from quotation data
        $spesifikasiData = [
            'from_quotation' => true,
            'quotation_id' => $quotation['id_quotation'],
            'quotation_number' => $quotation['quotation_number'],
            'specification_name' => $spec['specification_name'] ?? '',
            'description' => $spec['specification_description'] ?? '',
            'quantity' => $spec['quantity'] ?? 1,
            'unit_price' => $spec['unit_price'] ?? 0,
            'rental_duration' => $spec['rental_duration'] ?? null,
            'rental_rate_type' => $spec['rental_rate_type'] ?? null,
            'equipment_type' => $spec['equipment_type'] ?? null,
            'brand' => $spec['brand'] ?? null,
            'model' => $spec['model'] ?? null,
            'specifications' => $spec['specifications'] ?? null,
        ];
        
        // Generate SPK number
        $spkNumber = $this->generateNextNumber();
        
        // Prepare SPK data
        $spkData = [
            'nomor_spk' => $spkNumber,
            'jenis_spk' => $customerData['jenis_spk'] ?? 'UNIT',
            'kontrak_id' => null, // No contract yet
            'kontrak_spesifikasi_id' => null,
            'quotation_specification_id' => $quotationSpecId,
            'source_type' => 'QUOTATION',
            'jumlah_unit' => $spec['quantity'] ?? 1,
            'po_kontrak_nomor' => $customerData['po_kontrak_nomor'] ?? '',
            'pelanggan' => $customerData['pelanggan'] ?? $quotation['prospect_name'],
            'pic' => $customerData['pic'] ?? $quotation['prospect_contact_person'],
            'kontak' => $customerData['kontak'] ?? $quotation['prospect_phone'],
            'lokasi' => $customerData['lokasi'] ?? $quotation['prospect_address'],
            'delivery_plan' => $customerData['delivery_plan'] ?? null,
            'spesifikasi' => json_encode($spesifikasiData, JSON_UNESCAPED_UNICODE),
            'catatan' => $customerData['catatan'] ?? 'Created from Quotation - Contract pending',
            'status' => 'SUBMITTED',
            'dibuat_oleh' => $userId,
            'dibuat_pada' => date('Y-m-d H:i:s'),
            'diperbarui_pada' => date('Y-m-d H:i:s'),
        ];
        
        // Insert SPK
        if ($this->insert($spkData)) {
            return $this->getInsertID();
        }
        
        return false;
    }

    /**
     * Link SPK to Contract (late-linking mechanism)
     * Updates SPK and auto-propagates to related DIs via database trigger
     * 
     * @param int $spkId SPK to link
     * @param int $contractId Contract to link to
     * @param int $userId User performing the link
     * @return array ['success' => bool, 'message' => string, 'di_count' => int]
     */
    public function linkToContract(int $spkId, int $contractId, int $userId): array
    {
        $spk = $this->find($spkId);
        
        if (!$spk) {
            return ['success' => false, 'message' => 'SPK not found', 'di_count' => 0];
        }
        
        if ($spk['kontrak_id'] !== null) {
            return ['success' => false, 'message' => 'SPK already linked to contract', 'di_count' => 0];
        }
        
        // Verify contract exists and get customer info
        $kontrakModel = new \App\Models\KontrakModel();
        $contract = $kontrakModel->find($contractId);
        
        if (!$contract) {
            return ['success' => false, 'message' => 'Contract not found', 'di_count' => 0];
        }
        
        // Verify customer match (optional but recommended)
        // Skip validation if customer names differ slightly
        
        // Update SPK with contract link
        $updateData = [
            'kontrak_id' => $contractId,
            'contract_linked_at' => date('Y-m-d H:i:s'),
            'contract_linked_by' => $userId,
            'source_type' => 'CONTRACT', // Change source type to CONTRACT after linking
            'diperbarui_pada' => date('Y-m-d H:i:s'),
        ];
        
        $updated = $this->update($spkId, $updateData);
        
        if (!$updated) {
            return ['success' => false, 'message' => 'Failed to update SPK', 'di_count' => 0];
        }
        
        // Propagate contract_id and pelanggan_id to all DIs under this SPK
        $diModel = new \App\Models\DeliveryInstructionModel();
        $diCount = $diModel->where('spk_id', $spkId)->countAllResults();

        if ($diCount > 0) {
            $diModel->where('spk_id', $spkId)
                    ->where('contract_id IS NULL')
                    ->set([
                        'contract_id'     => $contractId,
                        'pelanggan_id'    => $contract['customer_id'] ?? null,
                        'diperbarui_pada' => date('Y-m-d H:i:s'),
                    ])
                    ->update();
        }
        
        return [
            'success' => true, 
            'message' => "SPK linked to contract successfully. {$diCount} delivery instructions updated.",
            'di_count' => $diCount
        ];
    }

    /**
     * Get SPKs without contract (AWAITING_CONTRACT workflow)
     * 
     * @param int|null $customerId Filter by customer if provided
     * @return array List of unlinked SPKs
     */
    public function getUnlinkedSPKs(?int $customerId = null): array
    {
        $builder = $this->select('spk.*, quotations.quotation_number, quotations.prospect_name')
                        ->join('quotation_specifications qs', 'qs.id_specification = spk.quotation_specification_id', 'left')
                        ->join('quotations', 'quotations.id_quotation = qs.id_quotation', 'left')
                        ->where('spk.kontrak_id', null)
                        ->where('spk.source_type', 'QUOTATION')
                        ->where('spk.status !=', 'CANCELLED');
        
        if ($customerId !== null) {
            // Filter by customer - would need customer_id field or join
            // For now, just return all unlinked SPKs
        }
        
        return $builder->orderBy('spk.dibuat_pada', 'DESC')->findAll();
    }

    /**
     * Get SPKs eligible for linking to a specific contract
     * Filters by customer match
     * 
     * @param int $contractId Contract to link to
     * @return array List of eligible SPKs
     */
    public function getSPKsForLinking(int $contractId): array
    {
        $kontrakModel = new \App\Models\KontrakModel();
        $contract = $kontrakModel->find($contractId);
        
        if (!$contract) {
            return [];
        }
        
        // Get customer from contract
        $customerLocationModel = new \App\Models\CustomerLocationModel();
        $location = $customerLocationModel->find($contract['customer_location_id']);
        
        if (!$location) {
            return [];
        }
        
        $customerId = $location['customer_id'];
        
        // Get unlinked SPKs - would need better customer matching
        // For now, return all unlinked SPKs with customer name match (fuzzy)
        $allUnlinked = $this->getUnlinkedSPKs();
        
        // Could add customer name matching logic here
        // For now, return all to let user select
        
        return $allUnlinked;
    }

    /**
     * Check if SPK has contract
     * Quick boolean check for DI creation status determination
     * 
     * @param int $spkId SPK ID to check
     * @return bool True if has contract, false otherwise
     */
    public function hasContract(int $spkId): bool
    {
        $spk = $this->select('kontrak_id')->find($spkId);
        return $spk && $spk['kontrak_id'] !== null;
    }
}

