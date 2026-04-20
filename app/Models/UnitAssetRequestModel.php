<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitAssetRequestModel extends Model
{
    protected $table            = 'unit_asset_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_inventory_unit',
        'stock_number',        'request_type',
        'requested_no_unit',        'status',
        'requested_by',
        'requested_at',
        'reviewed_by',
        'reviewed_at',
        'assigned_no_unit',
        'reject_notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Check if a unit already has a PENDING request.
     */
    public function hasPendingRequest(int $unitId): bool
    {
        return $this->where('id_inventory_unit', $unitId)
                    ->where('status', 'PENDING')
                    ->countAllResults() > 0;
    }

    /**
     * Check if a unit already has a PENDING CHANGE request.
     */
    public function hasPendingChangeRequest(int $unitId): bool
    {
        return $this->where('id_inventory_unit', $unitId)
                    ->where('status', 'PENDING')
                    ->where('request_type', 'CHANGE')
                    ->countAllResults() > 0;
    }

    /**
     * Get pending request for a specific unit (for display on unit detail).
     */
    public function getPendingForUnit(int $unitId): ?array
    {
        return $this->where('id_inventory_unit', $unitId)
                    ->where('status', 'PENDING')
                    ->where('request_type', 'NEW')
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    /**
     * Get pending CHANGE request for a specific unit.
     */
    public function getPendingChangeForUnit(int $unitId): ?array
    {
        return $this->where('id_inventory_unit', $unitId)
                    ->where('status', 'PENDING')
                    ->where('request_type', 'CHANGE')
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    /**
     * Get all requests with unit and user info joined (for Purchasing DataTable).
     */
    public function getWithDetails(array $filters = []): array
    {
        $builder = $this->db->table('unit_asset_requests r')
            ->select([
                'r.*',
                'iu.serial_number',
                'iu.no_unit AS current_no_unit',
                'mu.merk_unit',
                'mu.model_unit',
                'tu.jenis AS unit_jenis',
                'tu.tipe AS unit_tipe',
                'u_req.username AS requested_by_name',
                'u_rev.username AS reviewed_by_name',
            ])
            ->join('inventory_unit iu', 'iu.id_inventory_unit = r.id_inventory_unit', 'left')
            ->join('model_unit mu',     'mu.id_model_unit = iu.model_unit_id',        'left')
            ->join('tipe_unit tu',      'tu.id_tipe_unit  = iu.tipe_unit_id',         'left')
            ->join('users u_req',       'u_req.id = r.requested_by',                  'left')
            ->join('users u_rev',       'u_rev.id = r.reviewed_by',                   'left');

        if (!empty($filters['status'])) {
            $builder->where('r.status', $filters['status']);
        }

        return $builder->orderBy('r.created_at', 'DESC')->get()->getResultArray();
    }
}
