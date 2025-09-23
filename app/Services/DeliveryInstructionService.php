<?php

namespace App\Services;

use App\Config\JenisPerintahKerja;
use App\Config\TujuanPerintahKerja;
use App\Config\UnitWorkflowStatus;

/**
 * Delivery Instruction Business Logic Service
 * Handles validation and business rules for DI creation with contract unit management
 */
class DeliveryInstructionService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Get units from active contract for TARIK/TUKAR operations
     */
    public function getContractUnits($kontrakId, $jenisPerintahKode, $tujuanPerintahKode)
    {
        // For TARIK and TUKAR, we need to show units that are currently in the contract
        if (!in_array($jenisPerintahKode, [JenisPerintahKerja::TARIK, JenisPerintahKerja::TUKAR])) {
            return [];
        }

        $query = $this->db->table('kontrak_unit ku')
            ->select('
                ku.*,
                iu.id_inventory_unit,
                iu.no_unit,
                iu.status as unit_status,
                mu.merk_unit,
                mu.model_unit,
                k.nomor_kontrak,
                k.pelanggan,
                k.lokasi,
                k.tanggal_mulai,
                k.tanggal_selesai,
                k.status as kontrak_status
            ')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('kontrak k', 'k.id = ku.kontrak_id')
            ->where('ku.kontrak_id', $kontrakId)
            ->where('ku.status', 'AKTIF');

        // For TARIK operations, only show units that are currently deployed
        if ($jenisPerintahKode === JenisPerintahKerja::TARIK) {
            $query->whereIn('iu.status', ['DISEWA', 'BEROPERASI']);
        }

        // For TUKAR operations, show units that can be replaced
        if ($jenisPerintahKode === JenisPerintahKerja::TUKAR) {
            $query->whereIn('iu.status', ['DISEWA', 'BEROPERASI']);
        }

        // Exclude units that are already in active DI
        $query->whereNotIn('iu.id_inventory_unit', function($subquery) {
            $subquery->select('unit_id')
                    ->from('delivery_items di')
                    ->join('delivery_instructions dins', 'dins.id = di.delivery_instruction_id')
                    ->where('unit_id IS NOT NULL')
                    ->whereIn('dins.status', ['DIAJUKAN', 'DISETUJUI', 'PERSIAPAN_UNIT', 'SIAP_KIRIM', 'DALAM_PERJALANAN']);
        });

        $units = $query->get()->getResultArray();

        // Add workflow information for each unit
        foreach ($units as &$unit) {
            $unit['current_workflow_status'] = $this->getCurrentWorkflowStatus($unit['id_inventory_unit']);
            $unit['can_be_processed'] = $this->canUnitBeProcessed($unit['id_inventory_unit'], $jenisPerintahKode);
            $unit['next_status'] = UnitWorkflowStatus::getNextStatus($unit['unit_status'], $jenisPerintahKode);
        }

        return $units;
    }

    /**
     * Get available SPK with contract information for dynamic unit selection
     */
    public function getAvailableSpkWithContractInfo($jenisPerintahKode, $tujuanPerintahKode = null)
    {
        // Validate jenis perintah first
        if (!in_array($jenisPerintahKode, array_keys(JenisPerintahKerja::getAll()))) {
            throw new \InvalidArgumentException("Invalid jenis perintah: {$jenisPerintahKode}");
        }

        $query = $this->db->table('spk')
            ->select('
                spk.*, 
                kontrak.nomor_kontrak, 
                kontrak.status as kontrak_status,
                kontrak.tanggal_mulai,
                kontrak.tanggal_selesai,
                kontrak.pelanggan,
                kontrak.lokasi,
                jpk.nama as jenis_perintah_nama,
                tpk.nama as tujuan_perintah_nama,
                (SELECT COUNT(*) FROM kontrak_unit WHERE kontrak_id = kontrak.id AND status = "AKTIF") as total_units_in_contract
            ')
            ->join('kontrak', 'kontrak.id = spk.kontrak_id', 'left')
            ->join('jenis_perintah_kerja jpk', 'jpk.id = spk.jenis_perintah_kerja_id', 'left')
            ->join('tujuan_perintah_kerja tpk', 'tpk.id = spk.tujuan_perintah_kerja_id', 'left')
            ->where('spk.status', 'READY');

        // Apply contract status filter based on tujuan perintah
        if ($tujuanPerintahKode) {
            $contractStatusFilter = TujuanPerintahKerja::getContractStatusFilter($tujuanPerintahKode);
            
            if ($contractStatusFilter === 'AKTIF') {
                $query->where('kontrak.status', 'AKTIF')
                      ->where('kontrak.tanggal_selesai >=', date('Y-m-d'));
            } elseif ($contractStatusFilter === 'NON_AKTIF') {
                $query->where('kontrak.status', 'NON_AKTIF')
                      ->orWhere('kontrak.tanggal_selesai <', date('Y-m-d'));
            } elseif ($contractStatusFilter === 'BARU') {
                $query->groupStart()
                      ->where('spk.kontrak_id IS NULL')
                      ->orWhere('kontrak.status', 'DRAFT')
                      ->groupEnd();
            }
        }

        // For TARIK and TUKAR, ensure SPK has active contract with units
        if (in_array($jenisPerintahKode, [JenisPerintahKerja::TARIK, JenisPerintahKerja::TUKAR])) {
            $query->where('spk.kontrak_id IS NOT NULL')
                  ->where('kontrak.status', 'AKTIF')
                  ->having('total_units_in_contract >', 0);
        }

        $spkList = $query->get()->getResultArray();

        // For each SPK with contract, get unit details
        foreach ($spkList as &$spk) {
            if ($spk['kontrak_id'] && in_array($jenisPerintahKode, [JenisPerintahKerja::TARIK, JenisPerintahKerja::TUKAR])) {
                $spk['contract_units'] = $this->getContractUnits($spk['kontrak_id'], $jenisPerintahKode, $tujuanPerintahKode);
                $spk['available_units_count'] = count($spk['contract_units']);
            }
        }

        return $spkList;
    }

    /**
     * Process unit status change for TARIK operation
     */
    public function processUnitTarik($unitIds, $diId, $stage)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $stageActions = UnitWorkflowStatus::getStageActions($stage, 'TARIK');

            foreach ($unitIds as $unitId) {
                // Update unit status based on stage
                if (isset($stageActions['update_unit_status'])) {
                    $this->updateUnitStatus($unitId, $stageActions['update_unit_status'], $diId);
                }

                // Disconnect from contract if needed
                if (isset($stageActions['disconnect_partial_contract']) || isset($stageActions['disconnect_contract_fully'])) {
                    $this->disconnectUnitFromContract($unitId, $stage);
                }

                // Log activity
                $this->logUnitWorkflowActivity($unitId, $diId, $stage, 'TARIK');
            }

            $db->transCommit();
            return ['success' => true, 'message' => 'Unit status updated successfully'];

        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Failed to update unit status: ' . $e->getMessage()];
        }
    }

    /**
     * Process unit status change for TUKAR operation
     */
    public function processUnitTukar($oldUnitIds, $newUnitIds, $diId, $stage)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $stageActions = UnitWorkflowStatus::getStageActions($stage, 'TUKAR');

            // Process old units
            foreach ($oldUnitIds as $unitId) {
                if (isset($stageActions['update_old_unit_status'])) {
                    $this->updateUnitStatus($unitId, $stageActions['update_old_unit_status'], $diId);
                }

                if (isset($stageActions['disconnect_old_unit_contract'])) {
                    $this->disconnectUnitFromContract($unitId, $stage);
                }
            }

            // Process new units
            foreach ($newUnitIds as $unitId) {
                if (isset($stageActions['update_new_unit_status'])) {
                    $this->updateUnitStatus($unitId, $stageActions['update_new_unit_status'], $diId);
                }

                if (isset($stageActions['transfer_contract_to_new_unit'])) {
                    $this->transferContractToNewUnit($oldUnitIds[0], $unitId);
                }
            }

            $db->transCommit();
            return ['success' => true, 'message' => 'Unit exchange processed successfully'];

        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Failed to process unit exchange: ' . $e->getMessage()];
        }
    }

    /**
     * Update unit status
     */
    protected function updateUnitStatus($unitId, $newStatus, $diId)
    {
        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $unitId)
            ->update([
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
                'di_workflow_id' => $diId
            ]);
    }

    /**
     * Disconnect unit from contract
     */
    protected function disconnectUnitFromContract($unitId, $stage)
    {
        // Get contract info before disconnecting
        $contractUnit = $this->db->table('kontrak_unit')
            ->where('unit_id', $unitId)
            ->where('status', 'AKTIF')
            ->get()
            ->getRowArray();

        if ($contractUnit) {
            // Mark contract_unit as TARIK/TUKAR
            $this->db->table('kontrak_unit')
                ->where('id', $contractUnit['id'])
                ->update([
                    'status' => 'DITARIK',
                    'tanggal_tarik' => date('Y-m-d H:i:s'),
                    'stage_tarik' => $stage,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Log the disconnection
            $this->logContractDisconnection($contractUnit['kontrak_id'], $unitId, $stage);
        }
    }

    /**
     * Transfer contract from old unit to new unit (for TUKAR)
     */
    protected function transferContractToNewUnit($oldUnitId, $newUnitId)
    {
        // Get old contract info
        $oldContractUnit = $this->db->table('kontrak_unit')
            ->where('unit_id', $oldUnitId)
            ->where('status', 'AKTIF')
            ->get()
            ->getRowArray();

        if ($oldContractUnit) {
            // Mark old unit as DITUKAR
            $this->db->table('kontrak_unit')
                ->where('id', $oldContractUnit['id'])
                ->update([
                    'status' => 'DITUKAR',
                    'tanggal_tukar' => date('Y-m-d H:i:s'),
                    'unit_pengganti_id' => $newUnitId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Create new contract_unit for new unit
            $this->db->table('kontrak_unit')->insert([
                'kontrak_id' => $oldContractUnit['kontrak_id'],
                'unit_id' => $newUnitId,
                'tanggal_mulai' => date('Y-m-d'),
                'status' => 'AKTIF',
                'unit_sebelumnya_id' => $oldUnitId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Get current workflow status for unit
     */
    protected function getCurrentWorkflowStatus($unitId)
    {
        $unit = $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $unitId)
            ->get()
            ->getRowArray();

        return $unit ? $unit['status'] : null;
    }

    /**
     * Check if unit can be processed
     */
    protected function canUnitBeProcessed($unitId, $jenisPerintah)
    {
        $unit = $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $unitId)
            ->get()
            ->getRowArray();

        if (!$unit) return false;

        // Check if unit is not already in another active DI
        $activeDI = $this->db->table('delivery_items di')
            ->join('delivery_instructions dins', 'dins.id = di.delivery_instruction_id')
            ->where('di.unit_id', $unitId)
            ->whereIn('dins.status', ['DIAJUKAN', 'DISETUJUI', 'PERSIAPAN_UNIT', 'DALAM_PERJALANAN'])
            ->get()
            ->getRowArray();

        if ($activeDI) return false;

        // Check workflow status compatibility
        $allowedStatuses = [
            'TARIK' => ['DISEWA', 'BEROPERASI'],
            'TUKAR' => ['DISEWA', 'BEROPERASI'],
            'ANTAR' => ['TERSEDIA', 'STOCK_ASET'],
            'RELOKASI' => ['DISEWA', 'BEROPERASI']
        ];

        return in_array($unit['status'], $allowedStatuses[$jenisPerintah] ?? []);
    }

    /**
     * Log unit workflow activity
     */
    protected function logUnitWorkflowActivity($unitId, $diId, $stage, $jenisPerintah)
    {
        $this->db->table('unit_workflow_log')->insert([
            'unit_id' => $unitId,
            'di_id' => $diId,
            'stage' => $stage,
            'jenis_perintah' => $jenisPerintah,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session('user_id') ?? null
        ]);
    }

    /**
     * Log contract disconnection
     */
    protected function logContractDisconnection($kontrakId, $unitId, $stage)
    {
        $this->db->table('contract_disconnection_log')->insert([
            'kontrak_id' => $kontrakId,
            'unit_id' => $unitId,
            'stage' => $stage,
            'disconnected_at' => date('Y-m-d H:i:s'),
            'disconnected_by' => session('user_id') ?? null
        ]);
    }

    /**
     * Get available units for SPK based on jenis and tujuan perintah
     */
    public function getAvailableUnits($spkId, $jenisPerintahKode, $tujuanPerintahKode)
    {
        $spk = $this->db->table('spk')
            ->select('spk.*, kontrak.status as kontrak_status')
            ->join('kontrak', 'kontrak.id = spk.kontrak_id', 'left')
            ->where('spk.id', $spkId)
            ->get()
            ->getRowArray();

        if (!$spk) {
            throw new \InvalidArgumentException("SPK not found: {$spkId}");
        }

        $unitRules = TujuanPerintahKerja::getUnitSelectionRules($tujuanPerintahKode);

        $query = $this->db->table('inventory_unit iu')
            ->select('
                iu.*, 
                mu.merk_unit, 
                mu.model_unit,
                ku.nomor_kontrak,
                ku.status as kontrak_status,
                ku.tanggal_mulai,
                ku.tanggal_selesai,
                ku.lokasi as kontrak_lokasi
            ')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit', 'left')
            ->where('iu.status', 'TERSEDIA');

        // Apply unit selection rules based on tujuan perintah
        if ($unitRules['requires_active_contract']) {
            $query->where('ku.status', 'AKTIF')
                  ->where('ku.tanggal_selesai >=', date('Y-m-d'));
                  
            // If SPK has specific contract, filter by that contract
            if ($spk['kontrak_id']) {
                $query->where('ku.kontrak_id', $spk['kontrak_id']);
            }
        }

        if ($unitRules['requires_inactive_contract']) {
            $query->groupStart()
                  ->where('ku.status', 'NON_AKTIF')
                  ->orWhere('ku.tanggal_selesai <', date('Y-m-d'))
                  ->groupEnd();
        }

        // For TARIK operations, filter units that are currently deployed
        if ($jenisPerintahKode === JenisPerintahKerja::TARIK) {
            $query->whereIn('iu.status', ['DISEWA', 'BEROPERASI']);
        }

        // For ANTAR operations, filter available units in warehouse
        if ($jenisPerintahKode === JenisPerintahKerja::ANTAR) {
            $query->where('iu.status', 'TERSEDIA');
        }

        // Exclude units that are already in active DI
        $query->whereNotIn('iu.id_inventory_unit', function($subquery) {
            $subquery->select('unit_id')
                    ->from('delivery_items di')
                    ->join('delivery_instructions dins', 'dins.id = di.delivery_instruction_id')
                    ->where('unit_id IS NOT NULL')
                    ->whereIn('dins.status', ['DIAJUKAN', 'DISETUJUI', 'PERSIAPAN_UNIT', 'SIAP_KIRIM', 'DALAM_PERJALANAN']);
        });

        return $query->get()->getResultArray();
    }

    /**
     * Validate DI creation based on business rules
     */
    public function validateDiCreation($data)
    {
        $errors = [];

        // Validate required fields
        if (empty($data['jenis_perintah_kerja_id'])) {
            $errors[] = 'Jenis Perintah Kerja harus dipilih';
        }

        if (empty($data['tujuan_perintah_kerja_id'])) {
            $errors[] = 'Tujuan Perintah harus dipilih';
        }

        if (empty($data['spk_id'])) {
            $errors[] = 'SPK harus dipilih';
        }

        // If we have jenis and tujuan, validate compatibility
        if (!empty($data['jenis_perintah_kerja_id']) && !empty($data['tujuan_perintah_kerja_id'])) {
            $jenisData = $this->db->table('jenis_perintah_kerja')
                ->where('id', $data['jenis_perintah_kerja_id'])
                ->get()
                ->getRowArray();

            $tujuanData = $this->db->table('tujuan_perintah_kerja')
                ->where('id', $data['tujuan_perintah_kerja_id'])
                ->get()
                ->getRowArray();

            if ($jenisData && $tujuanData) {
                // Check if tujuan belongs to the selected jenis
                if ($tujuanData['jenis_perintah_id'] != $jenisData['id']) {
                    $errors[] = 'Tujuan Perintah tidak sesuai dengan Jenis Perintah yang dipilih';
                }

                // Additional business rule validations
                $this->validateBusinessRules($jenisData['kode'], $tujuanData['kode'], $data, $errors);
            }
        }

        return $errors;
    }

    /**
     * Validate specific business rules
     */
    protected function validateBusinessRules($jenisKode, $tujuanKode, $data, &$errors)
    {
        // Rule 1: TARIK_HABIS_KONTRAK must select SPK with expired/inactive contract
        if ($tujuanKode === TujuanPerintahKerja::TARIK_HABIS_KONTRAK && !empty($data['spk_id'])) {
            $spk = $this->db->table('spk')
                ->select('spk.*, kontrak.status, kontrak.tanggal_selesai')
                ->join('kontrak', 'kontrak.id = spk.kontrak_id', 'left')
                ->where('spk.id', $data['spk_id'])
                ->get()
                ->getRowArray();

            if ($spk) {
                $isExpired = $spk['tanggal_selesai'] && $spk['tanggal_selesai'] < date('Y-m-d');
                $isInactive = $spk['status'] === 'NON_AKTIF';
                
                if (!$isExpired && !$isInactive) {
                    $errors[] = 'Untuk TARIK karena habis kontrak, harus memilih SPK dengan kontrak yang sudah berakhir atau non-aktif';
                }
            }
        }

        // Rule 2: ANTAR_BARU should allow SPK without contract or new contracts
        if ($tujuanKode === TujuanPerintahKerja::ANTAR_BARU && !empty($data['spk_id'])) {
            $spk = $this->db->table('spk')
                ->select('spk.*, kontrak.status')
                ->join('kontrak', 'kontrak.id = spk.kontrak_id', 'left')
                ->where('spk.id', $data['spk_id'])
                ->get()
                ->getRowArray();

            if ($spk && $spk['kontrak_id'] && $spk['status'] === 'AKTIF') {
                $errors[] = 'Untuk ANTAR kontrak baru, tidak boleh memilih SPK dengan kontrak aktif yang sudah ada';
            }
        }

        // Rule 3: Unit replacement operations need specific validation
        if (TujuanPerintahKerja::requiresUnitReplacement($tujuanKode)) {
            // Additional validation for unit replacement can be added here
            // For example, ensuring old unit is selected for return
        }

        // Rule 4: Same location operations validation
        if (TujuanPerintahKerja::allowsSameLocation($tujuanKode)) {
            // Validate that origin and destination can be the same
        }
    }

    /**
     * Get SPK selection constraints based on jenis and tujuan
     */
    public function getSpkSelectionConstraints($jenisKode, $tujuanKode)
    {
        return [
            'contract_status_required' => TujuanPerintahKerja::getContractStatusFilter($tujuanKode),
            'requires_active_contract' => TujuanPerintahKerja::requiresActiveContract($tujuanKode),
            'requires_inactive_contract' => TujuanPerintahKerja::requiresInactiveContract($tujuanKode),
            'allows_new_contract' => TujuanPerintahKerja::allowsNewContract($tujuanKode),
            'requires_unit_preparation' => JenisPerintahKerja::requiresUnitPreparation($jenisKode),
            'contract_validation_required' => JenisPerintahKerja::requiresContractValidation($jenisKode)
        ];
    }

    /**
     * Get recommended next steps after DI creation
     */
    public function getRecommendedNextSteps($jenisKode, $tujuanKode)
    {
        $steps = [];

        if (JenisPerintahKerja::requiresUnitPreparation($jenisKode)) {
            $steps[] = 'Persiapan unit di workshop/gudang';
            $steps[] = 'Quality check dan inspection';
        }

        if ($jenisKode === JenisPerintahKerja::TARIK) {
            $steps[] = 'Koordinasi dengan pelanggan untuk jadwal penarikan';
            $steps[] = 'Persiapan transportasi dan tenaga kerja';
        }

        if ($jenisKode === JenisPerintahKerja::TUKAR) {
            $steps[] = 'Koordinasi pengambilan unit lama';
            $steps[] = 'Persiapan unit pengganti';
            $steps[] = 'Sinkronisasi jadwal tukar';
        }

        if ($jenisKode === JenisPerintahKerja::RELOKASI) {
            $steps[] = 'Konfirmasi lokasi tujuan';
            $steps[] = 'Perhitungan rute dan biaya transport';
        }

        return $steps;
    }
}