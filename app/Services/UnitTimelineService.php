<?php

namespace App\Services;

/**
 * UnitTimelineService
 *
 * Records deployment/retrieval events for inventory units.
 * Gracefully no-ops if the unit_timeline table does not exist.
 */
class UnitTimelineService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Record a unit deployment event (unit sent to customer).
     */
    public function recordDeployment(int $unitId, string $customer, string $location, ?string $contractNumber = null, array $meta = []): bool
    {
        return $this->record($unitId, 'DEPLOYMENT', 'Unit Dikirim ke Customer', array_merge([
            'customer'        => $customer,
            'location'        => $location,
            'contract_number' => $contractNumber,
        ], $meta));
    }

    /**
     * Record a unit status change event.
     */
    public function recordStatusChange(int $unitId, string $oldStatus, string $newStatus, ?string $note = null, ?int $performedBy = null): bool
    {
        return $this->record($unitId, 'STATUS_CHANGE', "Status: {$oldStatus} → {$newStatus}", [
            'old_status'   => $oldStatus,
            'new_status'   => $newStatus,
            'note'         => $note,
            'performed_by' => $performedBy,
        ]);
    }

    /**
     * Record a unit retrieval event (unit pulled back from customer).
     */
    public function recordRetrieval(int $unitId, string $customer, string $location, string $reason = 'DI_COMPLETED', array $meta = []): bool
    {
        return $this->record($unitId, 'RETRIEVAL', 'Unit Ditarik dari Customer', array_merge([
            'customer' => $customer,
            'location' => $location,
            'reason'   => $reason,
        ], $meta));
    }

    /**
     * Generic timeline record writer.
     */
    protected function record(int $unitId, string $eventType, string $title, array $meta = []): bool
    {
        try {
            if (!$this->db->tableExists('unit_timeline')) {
                return true; // Table not yet created — skip silently
            }

            $this->db->table('unit_timeline')->insert([
                'unit_id'      => $unitId,
                'event_type'   => $eventType,
                'title'        => $title,
                'metadata'     => json_encode($meta, JSON_UNESCAPED_UNICODE),
                'performed_by' => $meta['performed_by'] ?? null,
                'performed_at' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            return true;
        } catch (\Throwable $e) {
            log_message('error', '[UnitTimelineService] ' . $e->getMessage());
            return false;
        }
    }
}
