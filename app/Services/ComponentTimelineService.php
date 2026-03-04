<?php

namespace App\Services;

/**
 * Component Timeline Service
 * Tracks attachment/detachment of components (batteries, chargers, attachments)
 * to/from units.
 */
class ComponentTimelineService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Record a component event.
     *
     * @param int    $unitId
     * @param string $componentType  e.g. BATTERY, CHARGER, ATTACHMENT
     * @param string $eventType      e.g. ATTACHED, DETACHED, REPLACED
     * @param string $title          Short human-readable title
     * @param array  $options        Optional: description, component_id, metadata, performed_by, performed_at
     */
    public function recordEvent(
        int    $unitId,
        string $componentType,
        string $eventType,
        string $title,
        array  $options = []
    ): bool {
        try {
            $this->db->table('component_timeline')->insert([
                'unit_id'          => $unitId,
                'component_type'   => $componentType,
                'event_type'       => $eventType,
                'event_title'      => $title,
                'event_description'=> $options['description'] ?? null,
                'component_id'     => $options['component_id'] ?? null,
                'metadata'         => isset($options['metadata']) ? json_encode($options['metadata']) : null,
                'performed_by'     => $options['performed_by'] ?? null,
                'performed_at'     => $options['performed_at'] ?? date('Y-m-d H:i:s'),
                'created_at'       => date('Y-m-d H:i:s'),
            ]);
            return true;
        } catch (\Throwable $e) {
            log_message('error', '[ComponentTimelineService] ' . $e->getMessage());
            return false;
        }
    }

    public function recordAttachment(int $unitId, string $componentType, int $componentId, string $componentName, $performedBy = null): bool
    {
        return $this->recordEvent($unitId, $componentType, 'ATTACHED', ucfirst(strtolower($componentType)) . ' Dipasang', [
            'description'   => "{$componentName} dipasang pada unit",
            'component_id'  => $componentId,
            'metadata'      => ['component_name' => $componentName],
            'performed_by'  => $performedBy,
        ]);
    }

    public function recordDetachment(int $unitId, string $componentType, int $componentId, string $componentName, string $reason = null, $performedBy = null): bool
    {
        return $this->recordEvent($unitId, $componentType, 'DETACHED', ucfirst(strtolower($componentType)) . ' Dilepas', [
            'description'   => $reason ?? "{$componentName} dilepas dari unit",
            'component_id'  => $componentId,
            'metadata'      => ['component_name' => $componentName],
            'performed_by'  => $performedBy,
        ]);
    }

    public function recordReplacement(int $unitId, string $componentType, int $oldComponentId, int $newComponentId, string $oldName, string $newName, string $reason = null, $performedBy = null): bool
    {
        return $this->recordEvent($unitId, $componentType, 'REPLACED', ucfirst(strtolower($componentType)) . ' Diganti', [
            'description'   => $reason ?? "{$oldName} diganti dengan {$newName}",
            'component_id'  => $newComponentId,
            'metadata'      => [
                'old_component_id'   => $oldComponentId,
                'new_component_id'   => $newComponentId,
                'old_component_name' => $oldName,
                'new_component_name' => $newName,
            ],
            'performed_by'  => $performedBy,
        ]);
    }
}
