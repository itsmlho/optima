<?php

namespace App\Services;

/**
 * Contract Timeline Service
 * Provides a clean API for writing events to the contract_timeline table.
 * Tracks all significant changes to contracts (status changes, amendments, etc.)
 */
class ContractTimelineService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Record a contract event.
     *
     * @param int    $contractId
     * @param string $category  e.g. LIFECYCLE, AMENDMENT, BILLING
     * @param string $type      e.g. STATUS_CHANGE, UNIT_ADDED, UNIT_REMOVED
     * @param string $title     Short human-readable title
     * @param array  $options   Optional: description, reference_type, reference_id, metadata, performed_by, performed_at
     */
    public function recordEvent(
        int    $contractId,
        string $category,
        string $type,
        string $title,
        array  $options = []
    ): bool {
        try {
            $this->db->table('contract_timeline')->insert([
                'contract_id'      => $contractId,
                'event_category'   => $category,
                'event_type'       => $type,
                'event_title'      => $title,
                'event_description'=> $options['description'] ?? null,
                'reference_type'   => $options['reference_type'] ?? null,
                'reference_id'     => $options['reference_id'] ?? null,
                'metadata'         => isset($options['metadata']) ? json_encode($options['metadata']) : null,
                'performed_by'     => $options['performed_by'] ?? null,
                'performed_at'     => $options['performed_at'] ?? date('Y-m-d H:i:s'),
                'created_at'       => date('Y-m-d H:i:s'),
            ]);
            return true;
        } catch (\Throwable $e) {
            log_message('error', '[ContractTimelineService] ' . $e->getMessage());
            return false;
        }
    }

    public function recordStatusChange(int $contractId, string $oldStatus, string $newStatus, ?string $reason = null, ?int $performedBy = null): bool
    {
        return $this->recordEvent($contractId, 'LIFECYCLE', 'STATUS_CHANGED', "Status: {$oldStatus} → {$newStatus}", [
            'description'  => $reason,
            'metadata'     => ['old_status' => $oldStatus, 'new_status' => $newStatus],
            'performed_by' => $performedBy,
        ]);
    }

    public function recordUnitAdded(int $contractId, int $unitId, int $kontrakUnitId, $performedBy = null): bool
    {
        return $this->recordEvent($contractId, 'RENTAL', 'UNIT_ADDED', 'Unit Ditambahkan ke Kontrak', [
            'description'    => "Unit #{$unitId} ditambahkan ke kontrak",
            'reference_type' => 'kontrak_unit',
            'reference_id'   => $kontrakUnitId,
            'metadata'       => ['unit_id' => $unitId, 'kontrak_unit_id' => $kontrakUnitId],
            'performed_by'   => $performedBy,
        ]);
    }

    public function recordUnitRemoved(int $contractId, int $unitId, int $kontrakUnitId, ?string $reason = null, ?int $performedBy = null): bool
    {
        return $this->recordEvent($contractId, 'RENTAL', 'UNIT_REMOVED', 'Unit Dilepas dari Kontrak', [
            'description'    => $reason ?? "Unit #{$unitId} dilepas dari kontrak",
            'reference_type' => 'kontrak_unit',
            'reference_id'   => $kontrakUnitId,
            'metadata'       => ['unit_id' => $unitId, 'kontrak_unit_id' => $kontrakUnitId],
            'performed_by'   => $performedBy,
        ]);
    }

    public function recordUnitDisconnection(int $contractId, int $unitId, string $stage, $performedBy = null): bool
    {
        return $this->recordEvent($contractId, 'RENTAL', 'UNIT_DISCONNECTED', 'Unit Disconnect', [
            'description'    => "Unit #{$unitId} disconnect di stage: {$stage}",
            'metadata'       => ['unit_id' => $unitId, 'disconnect_stage' => $stage],
            'performed_by'   => $performedBy,
        ]);
    }

    public function recordAmendment(int $contractId, int $amendmentId, string $amendmentType, $performedBy = null): bool
    {
        return $this->recordEvent($contractId, 'AMENDMENT', 'AMENDMENT_CREATED', "Amandemen: {$amendmentType}", [
            'reference_type' => 'contract_amendments',
            'reference_id'   => $amendmentId,
            'metadata'       => ['amendment_id' => $amendmentId, 'amendment_type' => $amendmentType],
            'performed_by'   => $performedBy,
        ]);
    }

    public function recordRenewal(int $contractId, int $renewalContractId, $performedBy = null): bool
    {
        return $this->recordEvent($contractId, 'LIFECYCLE', 'CONTRACT_RENEWED', 'Kontrak Diperpanjang', [
            'description'    => "Kontrak diperpanjang ke kontrak #{$renewalContractId}",
            'reference_type' => 'kontrak',
            'reference_id'   => $renewalContractId,
            'metadata'       => ['renewal_contract_id' => $renewalContractId],
            'performed_by'   => $performedBy,
        ]);
    }

    public function recordTermination(int $contractId, string $reason, $performedBy = null): bool
    {
        return $this->recordEvent($contractId, 'LIFECYCLE', 'CONTRACT_TERMINATED', 'Kontrak Dihentikan', [
            'description'  => $reason,
            'performed_by' => $performedBy,
        ]);
    }
}
