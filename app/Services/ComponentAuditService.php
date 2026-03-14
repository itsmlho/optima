<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

/**
 * ComponentAuditService
 * 
 * Unified service untuk logging semua perubahan komponen (battery, charger, attachment)
 * ke tabel component_audit_log.
 * 
 * Usage:
 *   $auditService = new ComponentAuditService();
 *   $auditService->logAssignment('BATTERY', $batteryId, $unitId, [...]);
 *   $auditService->logRemoval('CHARGER', $chargerId, $fromUnitId, [...]);
 *   $auditService->logTransfer('ATTACHMENT', $attachmentId, $fromUnitId, $toUnitId, [...]);
 */
class ComponentAuditService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    /**
     * Log component assignment to a unit
     */
    public function logAssignment(
        string $componentType,
        int $componentId,
        int $toUnitId,
        array $options = []
    ): bool {
        return $this->log([
            'component_type' => strtoupper($componentType),
            'component_id' => $componentId,
            'event_type' => 'ASSIGNED',
            'event_category' => 'ASSIGNMENT',
            'to_unit_id' => $toUnitId,
            'from_unit_id' => $options['from_unit_id'] ?? null,
            'event_title' => $options['event_title'] ?? "Component assigned to unit #{$toUnitId}",
            'reference_type' => $options['reference_type'] ?? null,
            'reference_id' => $options['reference_id'] ?? null,
            'spk_id' => $options['spk_id'] ?? null,
            'di_id' => $options['di_id'] ?? null,
            'work_order_id' => $options['work_order_id'] ?? null,
            'stage_name' => $options['stage_name'] ?? null,
            'notes' => $options['notes'] ?? null,
            'triggered_by' => $options['triggered_by'] ?? 'MANUAL',
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    /**
     * Log component removal/detachment from a unit
     */
    public function logRemoval(
        string $componentType,
        int $componentId,
        int $fromUnitId,
        array $options = []
    ): bool {
        return $this->log([
            'component_type' => strtoupper($componentType),
            'component_id' => $componentId,
            'event_type' => $options['event_type'] ?? 'REMOVED',
            'event_category' => 'ASSIGNMENT',
            'from_unit_id' => $fromUnitId,
            'to_unit_id' => null,
            'event_title' => $options['event_title'] ?? "Component removed from unit #{$fromUnitId}",
            'reference_type' => $options['reference_type'] ?? null,
            'reference_id' => $options['reference_id'] ?? null,
            'spk_id' => $options['spk_id'] ?? null,
            'di_id' => $options['di_id'] ?? null,
            'work_order_id' => $options['work_order_id'] ?? null,
            'stage_name' => $options['stage_name'] ?? null,
            'notes' => $options['notes'] ?? null,
            'triggered_by' => $options['triggered_by'] ?? 'MANUAL',
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    /**
     * Log component transfer between units
     */
    public function logTransfer(
        string $componentType,
        int $componentId,
        ?int $fromUnitId,
        int $toUnitId,
        array $options = []
    ): bool {
        return $this->log([
            'component_type' => strtoupper($componentType),
            'component_id' => $componentId,
            'event_type' => 'TRANSFERRED',
            'event_category' => 'TRANSFER',
            'from_unit_id' => $fromUnitId,
            'to_unit_id' => $toUnitId,
            'event_title' => $options['event_title'] ?? ($fromUnitId 
                ? "Component transferred from unit #{$fromUnitId} to unit #{$toUnitId}"
                : "Component assigned to unit #{$toUnitId}"),
            'reference_type' => $options['reference_type'] ?? null,
            'reference_id' => $options['reference_id'] ?? null,
            'spk_id' => $options['spk_id'] ?? null,
            'di_id' => $options['di_id'] ?? null,
            'work_order_id' => $options['work_order_id'] ?? null,
            'stage_name' => $options['stage_name'] ?? null,
            'notes' => $options['notes'] ?? null,
            'triggered_by' => $options['triggered_by'] ?? 'TRANSFER',
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    /**
     * Log bulk release of components (e.g., when contract ends)
     */
    public function logBulkRelease(
        string $componentType,
        array $componentIds,
        ?int $fromUnitId,
        array $options = []
    ): int {
        $count = 0;
        foreach ($componentIds as $componentId) {
            if ($this->logRemoval($componentType, $componentId, $fromUnitId ?? 0, array_merge($options, [
                'event_type' => 'BULK_RELEASED',
                'event_title' => $options['event_title'] ?? 'Component bulk released (contract ended)',
            ]))) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Log component replacement (remove old, assign new)
     */
    public function logReplacement(
        string $componentType,
        int $oldComponentId,
        int $newComponentId,
        int $unitId,
        array $options = []
    ): bool {
        $this->logRemoval($componentType, $oldComponentId, $unitId, array_merge($options, [
            'event_type' => 'REPLACED',
            'event_title' => "Component replaced on unit #{$unitId}",
            'notes' => ($options['notes'] ?? '') . " Replaced by component #{$newComponentId}",
        ]));

        return $this->logAssignment($componentType, $newComponentId, $unitId, array_merge($options, [
            'event_title' => "Component assigned (replacement) to unit #{$unitId}",
            'notes' => ($options['notes'] ?? '') . " Replacing component #{$oldComponentId}",
        ]));
    }

    /**
     * Log verification event
     */
    public function logVerification(
        string $componentType,
        int $componentId,
        int $unitId,
        array $options = []
    ): bool {
        return $this->log([
            'component_type' => strtoupper($componentType),
            'component_id' => $componentId,
            'event_type' => 'VERIFIED',
            'event_category' => 'STATUS',
            'to_unit_id' => $unitId,
            'event_title' => $options['event_title'] ?? "Component verified on unit #{$unitId}",
            'reference_type' => $options['reference_type'] ?? 'work_order',
            'reference_id' => $options['reference_id'] ?? null,
            'work_order_id' => $options['work_order_id'] ?? null,
            'notes' => $options['notes'] ?? null,
            'triggered_by' => $options['triggered_by'] ?? 'UNIT_VERIFICATION',
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    /**
     * Core logging method
     */
    protected function log(array $data): bool
    {
        $userId = session('user_id') ?? null;

        $insertData = [
            'component_type' => $data['component_type'],
            'component_id' => $data['component_id'],
            'event_type' => $data['event_type'],
            'event_category' => $data['event_category'] ?? 'ASSIGNMENT',
            'event_title' => $data['event_title'] ?? null,
            'event_description' => $data['event_description'] ?? null,
            'from_unit_id' => $data['from_unit_id'] ?? null,
            'to_unit_id' => $data['to_unit_id'] ?? null,
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'spk_id' => $data['spk_id'] ?? null,
            'di_id' => $data['di_id'] ?? null,
            'work_order_id' => $data['work_order_id'] ?? null,
            'stage_name' => $data['stage_name'] ?? null,
            'metadata' => isset($data['metadata']) ? (is_string($data['metadata']) ? $data['metadata'] : json_encode($data['metadata'])) : null,
            'notes' => $data['notes'] ?? null,
            'triggered_by' => $data['triggered_by'] ?? null,
            'performed_by' => $data['performed_by'] ?? $userId,
            'performed_at' => $data['performed_at'] ?? date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        try {
            return $this->db->table('component_audit_log')->insert($insertData);
        } catch (\Exception $e) {
            log_message('error', '[ComponentAuditService] Failed to log: ' . $e->getMessage());
            log_message('error', '[ComponentAuditService] Data: ' . json_encode($insertData));
            return false;
        }
    }

    /**
     * Get component history
     */
    public function getComponentHistory(string $componentType, int $componentId, int $limit = 50): array
    {
        return $this->db->table('component_audit_log')
            ->where('component_type', strtoupper($componentType))
            ->where('component_id', $componentId)
            ->orderBy('performed_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get unit component history (all components ever assigned to a unit)
     */
    public function getUnitComponentHistory(int $unitId, int $limit = 100): array
    {
        return $this->db->table('component_audit_log')
            ->groupStart()
                ->where('from_unit_id', $unitId)
                ->orWhere('to_unit_id', $unitId)
            ->groupEnd()
            ->orderBy('performed_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get recent activity for dashboard/monitoring
     */
    public function getRecentActivity(int $limit = 50, ?string $componentType = null): array
    {
        $builder = $this->db->table('component_audit_log cal')
            ->select('cal.*, u.nama_lengkap as performed_by_name')
            ->join('users u', 'u.id = cal.performed_by', 'left')
            ->orderBy('cal.performed_at', 'DESC')
            ->limit($limit);

        if ($componentType) {
            $builder->where('cal.component_type', strtoupper($componentType));
        }

        return $builder->get()->getResultArray();
    }
}
