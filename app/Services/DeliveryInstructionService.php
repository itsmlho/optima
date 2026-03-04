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
            ->where('ku.status', 'ACTIVE');

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
                (SELECT COUNT(*) FROM kontrak_unit WHERE kontrak_id = kontrak.id AND status = "ACTIVE") as total_units_in_contract
            ')
            ->join('kontrak', 'kontrak.id = spk.kontrak_id', 'left')
            ->join('jenis_perintah_kerja jpk', 'jpk.id = spk.jenis_perintah_kerja_id', 'left')
            ->join('tujuan_perintah_kerja tpk', 'tpk.id = spk.tujuan_perintah_kerja_id', 'left')
            ->where('spk.status', 'READY');

        // Apply contract status filter based on tujuan perintah
        if ($tujuanPerintahKode) {
            $contractStatusFilter = TujuanPerintahKerja::getContractStatusFilter($tujuanPerintahKode);
            
            if ($contractStatusFilter === 'ACTIVE') {
                $query->where('kontrak.status', 'ACTIVE')
                      ->where('kontrak.tanggal_selesai >=', date('Y-m-d'));
            } elseif ($contractStatusFilter === 'EXPIRED') {
                $query->where('kontrak.status', 'EXPIRED')
                      ->orWhere('kontrak.tanggal_selesai <', date('Y-m-d'));
            } elseif ($contractStatusFilter === 'BARU') {
                $query->groupStart()
                      ->where('spk.kontrak_id IS NULL')
                      ->orWhere('kontrak.status', 'PENDING')
                      ->groupEnd();
            }
        }

        // For TARIK and TUKAR, ensure SPK has active contract with units
        if (in_array($jenisPerintahKode, [JenisPerintahKerja::TARIK, JenisPerintahKerja::TUKAR])) {
            $query->where('spk.kontrak_id IS NOT NULL')
                  ->where('kontrak.status', 'ACTIVE')
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
            
            // Get tujuan_perintah_kerja_id from DI for conditional logic
            $tujuanId = $this->getTujuanPerintahKerjaId($diId);

            foreach ($unitIds as $unitId) {
                // Update unit status based on stage
                if (isset($stageActions['update_unit_status'])) {
                    $this->updateUnitStatus($unitId, $stageActions['update_unit_status'], $diId);
                }

                // Disconnect from contract if needed (with tujuan-based logic)
                if (isset($stageActions['disconnect_partial_contract']) || isset($stageActions['disconnect_contract_fully'])) {
                    $this->disconnectUnitFromContract($unitId, $stage, $tujuanId);
                }

                // Log activity
                $this->logUnitWorkflowActivity($unitId, $diId, $stage, 'TARIK', $tujuanId);
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
            
            // Get tujuan_perintah_kerja_id for temp vs permanent logic
            $tujuanId = $this->getTujuanPerintahKerjaId($diId);

            // Process old units
            foreach ($oldUnitIds as $unitId) {
                if (isset($stageActions['update_old_unit_status'])) {
                    $this->updateUnitStatus($unitId, $stageActions['update_old_unit_status'], $diId);
                }

                if (isset($stageActions['disconnect_old_unit_contract'])) {
                    // For TUKAR, we handle this in transferContractToNewUnit
                    // This will disconnect for permanent, keep for temporary
                }
            }

            // Process new units and transfer contracts
            foreach ($newUnitIds as $index => $newUnitId) {
                $oldUnitId = $oldUnitIds[$index] ?? $oldUnitIds[0];
                
                if (isset($stageActions['update_new_unit_status'])) {
                    $this->updateUnitStatus($newUnitId, $stageActions['update_new_unit_status'], $diId);
                }

                if (isset($stageActions['transfer_contract_to_new_unit'])) {
                    // Pass tujuanId for temporary vs permanent logic
                    $this->transferContractToNewUnit($oldUnitId, $newUnitId, $tujuanId);
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
                'workflow_status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
                'di_workflow_id' => $diId
            ]);
    }

    /**
     * Get tujuan_perintah_kerja_id from delivery instruction
     */
    protected function getTujuanPerintahKerjaId($diId)
    {
        $di = $this->db->table('delivery_instructions')
            ->select('tujuan_perintah_kerja_id')
            ->where('id', $diId)
            ->get()
            ->getRowArray();
        
        return $di ? $di['tujuan_perintah_kerja_id'] : null;
    }

    /**
     * Disconnect unit from contract with tujuan-based logic
     * Different tujuan types have different FK disconnection behaviors
     */
    protected function disconnectUnitFromContract($unitId, $stage, $tujuanId = null)
    {
        // Get contract info before disconnecting
        $contractUnit = $this->db->table('kontrak_unit')
            ->where('unit_id', $unitId)
            ->where('status', 'ACTIVE')
            ->get()
            ->getRowArray();

        if ($contractUnit) {
            // Determine disconnect behavior based on tujuan
            // ID 4: TARIK_HABIS_KONTRAK - Full disconnect
            // ID 6: TARIK_MAINTENANCE - Keep FKs (temporary)
            // ID 5: TARIK_PINDAH_LOKASI - Keep FKs (relocation)
            // ID 7: TARIK_RUSAK - Keep FKs (will return after repair)
            
            $shouldDisconnectFKs = in_array($tujuanId, [4]); // Only HABIS_KONTRAK disconnects
            $isTemporary = in_array($tujuanId, [6, 7]); // MAINTENANCE, RUSAK are temporary
            $isRelocation = ($tujuanId == 5); // PINDAH_LOKASI
            
            // Get customer/location info from kontrak_unit junction (Phase 1A refactored)
            $unitInfo = $this->db->table('kontrak_unit ku')
                ->select('ku.kontrak_id, k.customer_id, k.customer_location_id')
                ->join('kontrak k', 'k.id = ku.kontrak_id')
                ->where('ku.unit_id', $unitId)
                ->where('ku.status', 'ACTIVE')
                ->get()
                ->getRowArray();
            
            if ($isTemporary) {
                // TEMPORARY: Mark kontrak_unit as MAINTENANCE/UNDER_REPAIR but keep FKs
                $newStatus = ($tujuanId == 6) ? 'MAINTENANCE' : 'UNDER_REPAIR';
                $this->db->table('kontrak_unit')
                    ->where('id', $contractUnit['id'])
                    ->update([
                        'status' => $newStatus,
                        'maintenance_start' => date('Y-m-d H:i:s'),
                        'maintenance_reason' => ($tujuanId == 6) ? 'Scheduled maintenance' : 'Unit damaged - repair needed',
                        'stage_tarik' => $stage,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => session('user_id')
                    ]);
                
                // Keep FKs but update workflow status
                $this->db->table('inventory_unit')
                    ->where('id_inventory_unit', $unitId)
                    ->update([
                        'workflow_status' => ($tujuanId == 6) ? 'MAINTENANCE_IN_PROGRESS' : 'UNDER_REPAIR',
                        'maintenance_location' => 'WORKSHOP',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } elseif ($isRelocation) {
                // RELOCATION: Keep kontrak_unit AKTIF, just update location
                // Location update will be handled separately in TUKAR_PINDAH_LOKASI logic
                $this->db->table('kontrak_unit')
                    ->where('id', $contractUnit['id'])
                    ->update([
                        'stage_tarik' => $stage,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => session('user_id')
                    ]);
                
                $this->db->table('inventory_unit')
                    ->where('id_inventory_unit', $unitId)
                    ->update([
                        'workflow_status' => 'RELOCATING',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // PERMANENT DISCONNECT: HABIS_KONTRAK
                $this->db->table('kontrak_unit')
                    ->where('id', $contractUnit['id'])
                    ->update([
                        'status' => 'DITARIK',
                        'tanggal_tarik' => date('Y-m-d H:i:s'),
                        'stage_tarik' => $stage,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => session('user_id')
                    ]);
                
                if ($shouldDisconnectFKs) {
                    // Fully disconnect: update workflow status
                    // TODO Step 4: Remove kontrak_id/customer_id/customer_location_id after column drop
                    $this->db->table('inventory_unit')
                        ->where('id_inventory_unit', $unitId)
                        ->update([
                            'kontrak_id' => null,
                            'customer_id' => null,
                            'customer_location_id' => null,
                            'workflow_status' => 'STOCK_ASET',
                            'contract_disconnect_date' => date('Y-m-d H:i:s'),
                            'contract_disconnect_stage' => 'HABIS_KONTRAK',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                }
            }

            // Log the disconnection/status change
            $this->logContractDisconnection($contractUnit['kontrak_id'], $unitId, $stage, $tujuanId);
        }
    }

    /**
     * Transfer contract from old unit to new unit (for TUKAR)
     * Handles both permanent and temporary replacements
     */
    protected function transferContractToNewUnit($oldUnitId, $newUnitId, $tujuanId = null)
    {
        // Get old contract info
        $oldContractUnit = $this->db->table('kontrak_unit')
            ->where('unit_id', $oldUnitId)
            ->where('status', 'ACTIVE')
            ->get()
            ->getRowArray();

        if ($oldContractUnit) {
            // Get customer/location info from kontrak_unit junction (Phase 1A refactored)
            $oldUnitInfo = $this->db->table('kontrak_unit ku')
                ->select('ku.kontrak_id, k.customer_id, k.customer_location_id')
                ->join('kontrak k', 'k.id = ku.kontrak_id')
                ->where('ku.unit_id', $oldUnitId)
                ->where('ku.status', 'ACTIVE')
                ->get()
                ->getRowArray();
            
            // ID 11: TUKAR_MAINTENANCE - Temporary replacement
            // ID 8, 9, 10: TUKAR_UPGRADE, DOWNGRADE, RUSAK - Permanent replacement
            $isTemporaryReplacement = ($tujuanId == 11); // TUKAR_MAINTENANCE
            
            if ($isTemporaryReplacement) {
                // TEMPORARY REPLACEMENT: Keep old unit linked, mark as temporarily replaced
                $this->handleTemporaryReplacement($oldUnitId, $newUnitId, $oldContractUnit, $oldUnitInfo);
            } else {
                // PERMANENT REPLACEMENT: Full transfer
                $this->handlePermanentReplacement($oldUnitId, $newUnitId, $oldContractUnit, $oldUnitInfo);
            }
        }
    }
    
    /**
     * Handle permanent unit replacement (UPGRADE/DOWNGRADE/RUSAK)
     */
    protected function handlePermanentReplacement($oldUnitId, $newUnitId, $oldContractUnit, $oldUnitInfo)
    {
        // Mark old unit as DITUKAR in kontrak_unit
        $this->db->table('kontrak_unit')
            ->where('id', $oldContractUnit['id'])
            ->update([
                'status' => 'DITUKAR',
                'tanggal_tukar' => date('Y-m-d H:i:s'),
                'unit_pengganti_id' => $newUnitId,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session('user_id')
            ]);

        // Disconnect old unit: update workflow + dual-write legacy FKs
        // TODO Step 4: Remove kontrak_id/customer_id/customer_location_id after column drop
        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $oldUnitId)
            ->update([
                'kontrak_id' => null,
                'customer_id' => null,
                'customer_location_id' => null,
                'workflow_status' => 'STOCK_ASET',
                'contract_disconnect_date' => date('Y-m-d H:i:s'),
                'contract_disconnect_stage' => 'DITUKAR',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // Create new contract_unit for new unit
        $this->db->table('kontrak_unit')->insert([
            'kontrak_id' => $oldContractUnit['kontrak_id'],
            'unit_id' => $newUnitId,
            'tanggal_mulai' => date('Y-m-d'),
            'status' => 'ACTIVE',
            'unit_sebelumnya_id' => $oldUnitId,
            'is_temporary' => false,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session('user_id')
        ]);

        // Dual-write: transfer legacy FKs to new unit (kontrak_unit INSERT above is the primary)
        // TODO Step 4: Remove kontrak_id/customer_id/customer_location_id after column drop
        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $newUnitId)
            ->update([
                'kontrak_id' => $oldUnitInfo['kontrak_id'],
                'customer_id' => $oldUnitInfo['customer_id'],
                'customer_location_id' => $oldUnitInfo['customer_location_id'],
                'workflow_status' => 'DISEWA',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // Transfer attachments from old unit to new unit
        $this->transferAttachments($oldUnitId, $newUnitId);
    }
    
    /**
     * Handle temporary unit replacement (TUKAR_MAINTENANCE)
     * Original unit will return after maintenance
     */
    protected function handleTemporaryReplacement($oldUnitId, $newUnitId, $oldContractUnit, $oldUnitInfo)
    {
        // Mark old unit as TEMPORARILY_REPLACED (not DITUKAR!)
        $this->db->table('kontrak_unit')
            ->where('id', $oldContractUnit['id'])
            ->update([
                'status' => 'TEMPORARILY_REPLACED',
                'temporary_replacement_date' => date('Y-m-d H:i:s'),
                'temporary_replacement_unit_id' => $newUnitId,
                'maintenance_start' => date('Y-m-d H:i:s'),
                'maintenance_reason' => 'Temporary replacement during maintenance',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session('user_id')
            ]);
        
        // Keep FKs on old unit but mark as in maintenance
        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $oldUnitId)
            ->update([
                'workflow_status' => 'MAINTENANCE_WITH_REPLACEMENT',
                'maintenance_location' => 'WORKSHOP',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
        // Create TEMPORARY kontrak_unit for replacement unit
        $this->db->table('kontrak_unit')->insert([
            'kontrak_id' => $oldContractUnit['kontrak_id'],
            'unit_id' => $newUnitId,
            'tanggal_mulai' => date('Y-m-d'),
            'status' => 'TEMPORARY_ACTIVE',
            'is_temporary' => true,
            'original_unit_id' => $oldUnitId,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session('user_id')
        ]);
        
        // Dual-write: set legacy FKs on replacement unit (kontrak_unit INSERT above is the primary)
        // TODO Step 4: Remove kontrak_id/customer_id/customer_location_id after column drop
        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $newUnitId)
            ->update([
                'kontrak_id' => $oldUnitInfo['kontrak_id'],
                'customer_id' => $oldUnitInfo['customer_id'],
                'customer_location_id' => $oldUnitInfo['customer_location_id'],
                'workflow_status' => 'TEMPORARY_RENTAL',
                'is_temporary_assignment' => true,
                'temporary_for_contract_id' => $oldUnitInfo['kontrak_id'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
        // Transfer attachments temporarily
        $this->transferAttachments($oldUnitId, $newUnitId);
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

        // Check workflow_status if set, otherwise check via kontrak_unit junction
        $currentStatus = $unit['workflow_status'] ?? null;
        if (!$currentStatus) {
            // Fallback: infer from kontrak_unit junction (Phase 1A refactored)
            $activeKu = $this->db->table('kontrak_unit')
                ->where('unit_id', $unitId)
                ->where('status', 'ACTIVE')
                ->countAllResults();
            $currentStatus = $activeKu > 0 ? 'DISEWA' : 'TERSEDIA';
        }

        return in_array($currentStatus, $allowedStatuses[$jenisPerintah] ?? []);
    }

    /**
     * Log unit workflow activity
     */
    protected function logUnitWorkflowActivity($unitId, $diId, $stage, $jenisPerintah, $tujuanId = null)
    {
        $this->db->table('unit_workflow_log')->insert([
            'unit_id' => $unitId,
            'di_id' => $diId,
            'stage' => $stage,
            'jenis_perintah' => $jenisPerintah,
            'tujuan_perintah_id' => $tujuanId,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session('user_id') ?? null
        ]);
    }

    /**
     * Log contract disconnection
     */
    protected function logContractDisconnection($kontrakId, $unitId, $stage, $tujuanId = null)
    {
        $this->db->table('contract_disconnection_log')->insert([
            'kontrak_id' => $kontrakId,
            'unit_id' => $unitId,
            'stage' => $stage,
            'tujuan_perintah_id' => $tujuanId,
            'disconnected_at' => date('Y-m-d H:i:s'),
            'disconnected_by' => session('user_id') ?? null
        ]);
    }

    /**
     * Transfer attachments from old unit to new unit (for TUKAR)
     * Uses 2-step detach→attach process like KANIBAL system
     */
    protected function transferAttachments($oldUnitId, $newUnitId)
    {
        // Get all attachments/batteries/chargers from old unit
        $batteries = $this->db->table('inventory_batteries')
            ->where('inventory_unit_id', $oldUnitId)
            ->get()
            ->getResultArray();
            
        $chargers = $this->db->table('inventory_chargers')
            ->where('inventory_unit_id', $oldUnitId)
            ->get()
            ->getResultArray();
            
        $attachments = $this->db->table('inventory_attachments')
            ->where('inventory_unit_id', $oldUnitId)
            ->get()
            ->getResultArray();

        // Transfer batteries
        foreach ($batteries as $battery) {
            // Step 1: Detach from old unit
            $this->db->table('inventory_batteries')
                ->where('id', $battery['id'])
                ->update([
                    'inventory_unit_id' => null,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Step 2: Attach to new unit
            $this->db->table('inventory_batteries')
                ->where('id', $battery['id'])
                ->update([
                    'inventory_unit_id' => $newUnitId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Log the transfer
            $this->db->table('attachment_transfer_log')->insert([
                'attachment_id' => $battery['id'],
                'from_unit_id' => $oldUnitId,
                'to_unit_id' => $newUnitId,
                'transfer_type' => 'TUKAR',
                'triggered_by' => 'DI_WORKFLOW',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Transfer chargers
        foreach ($chargers as $charger) {
            // Step 1: Detach from old unit
            $this->db->table('inventory_chargers')
                ->where('id', $charger['id'])
                ->update([
                    'inventory_unit_id' => null,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Step 2: Attach to new unit
            $this->db->table('inventory_chargers')
                ->where('id', $charger['id'])
                ->update([
                    'inventory_unit_id' => $newUnitId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Log the transfer
            $this->db->table('attachment_transfer_log')->insert([
                'attachment_id' => $charger['id'],
                'from_unit_id' => $oldUnitId,
                'to_unit_id' => $newUnitId,
                'transfer_type' => 'TUKAR',
                'triggered_by' => 'DI_WORKFLOW',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Transfer attachments
        foreach ($attachments as $attachment) {
            // Step 1: Detach from old unit
            $this->db->table('inventory_attachments')
                ->where('id', $attachment['id'])
                ->update([
                    'inventory_unit_id' => null,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Step 2: Attach to new unit
            $this->db->table('inventory_attachments')
                ->where('id', $attachment['id'])
                ->update([
                    'inventory_unit_id' => $newUnitId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Log the transfer in attachment_transfer_log
            $this->db->table('attachment_transfer_log')->insert([
                'attachment_id' => $attachment['id'],
                'from_unit_id' => $oldUnitId,
                'to_unit_id' => $newUnitId,
                'transfer_type' => 'TUKAR',
                'triggered_by' => 'DI_WORKFLOW',
                'spk_id' => null,
                'notes' => 'Automatic transfer during TUKAR operation',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => session('user_id')
            ]);

            log_message('info', "Transferred attachment {$attachment['id_inventory_attachment']} from unit {$oldUnitId} to unit {$newUnitId} via TUKAR");
        }
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
            $query->where('ku.status', 'ACTIVE')
                  ->where('ku.tanggal_selesai >=', date('Y-m-d'));
                  
            // If SPK has specific contract, filter by that contract
            if ($spk['kontrak_id']) {
                $query->where('ku.kontrak_id', $spk['kontrak_id']);
            }
        }

        if ($unitRules['requires_inactive_contract']) {
            $query->groupStart()
                  ->where('ku.status', 'INACTIVE')
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
                    ->join('delivery_instructions dins', 'dins.id = di.di_id')
                    ->where('unit_id IS NOT NULL')
                    ->whereIn('dins.status_di', ['DIAJUKAN', 'DISETUJUI', 'PERSIAPAN_UNIT', 'SIAP_KIRIM', 'DALAM_PERJALANAN', 'SAMPAI_LOKASI']);
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
                $isInactive = $spk['status'] === 'EXPIRED';
                
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

            if ($spk && $spk['kontrak_id'] && $spk['status'] === 'ACTIVE') {
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