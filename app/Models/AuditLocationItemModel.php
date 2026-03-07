<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLocationItemModel extends Model
{
    protected $table            = 'unit_audit_location_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'audit_location_id',
        'kontrak_unit_id',
        'unit_id',
        'expected_no_unit',
        'expected_serial',
        'expected_merk',
        'expected_model',
        'expected_is_spare',
        'expected_status',
        'actual_no_unit',
        'actual_serial',
        'actual_merk',
        'actual_model',
        'actual_is_spare',
        'actual_operator_present',
        'result',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all items for an audit location
     */
    public function getByAuditLocation(int $auditLocationId): array
    {
        return $this->db->table('unit_audit_location_items uali')
            ->select('uali.*,
                iu.serial_number,
                mu.merk_unit,
                mu.model_unit,
                tu.tipe as tipe_unit')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = uali.unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->where('uali.audit_location_id', $auditLocationId)
            ->orderBy('uali.expected_no_unit', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Calculate result based on expected vs actual
     */
    public function calculateResult(array $item): string
    {
        // If no expected data but has actual -> EXTRA_UNIT
        if (empty($item['expected_no_unit']) && !empty($item['actual_no_unit'])) {
            return 'EXTRA_UNIT';
        }

        // If has expected data but no actual -> NO_UNIT_IN_KONTRAK
        if (!empty($item['expected_no_unit']) && empty($item['actual_no_unit'])) {
            return 'NO_UNIT_IN_KONTRAK';
        }

        // Compare unit number
        if ($item['expected_no_unit'] !== $item['actual_no_unit']) {
            return 'MISMATCH_NO_UNIT';
        }

        // Compare serial number
        if ($item['expected_serial'] !== $item['actual_serial']) {
            return 'MISMATCH_SERIAL';
        }

        // Compare merk/model
        if ($item['expected_merk'] !== $item['actual_merk'] || $item['expected_model'] !== $item['actual_model']) {
            return 'MISMATCH_SPEC';
        }

        // Compare spare status
        if ($item['expected_is_spare'] != $item['actual_is_spare']) {
            return 'MISMATCH_SPARE';
        }

        return 'MATCH';
    }
}
