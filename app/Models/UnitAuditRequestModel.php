<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitAuditRequestModel extends Model
{
    protected $table            = 'unit_audit_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'audit_number',
        'customer_id',
        'kontrak_id',
        'unit_id',
        'request_type',
        'current_data',
        'proposed_data',
        'notes',
        'evidence_photo',
        'status',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ── Helpers ──────────────────────────────────────────

    /**
     * Generate audit number AUD-YYYYMMDD-NNNN
     */
    public function generateAuditNumber(): string
    {
        $prefix = 'AUD-' . date('Ymd') . '-';

        $last = $this->select('audit_number')
                     ->like('audit_number', $prefix, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        $next = 1;
        if ($last) {
            $next = (int) substr($last['audit_number'], -4) + 1;
        }

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // ── Data Retrieval ──────────────────────────────────

    /**
     * Get audit requests with joined info (unit, customer, submitter, reviewer)
     */
    public function getWithDetails(array $filters = []): array
    {
        $builder = $this->db->table('unit_audit_requests uar');
        $builder->select('uar.*,
            c.customer_name,
            c.customer_code,
            iu.no_unit,
            iu.no_unit_na,
            iu.serial_number,
            iu.lokasi_unit,
            mu.merk_unit,
            mu.model_unit,
            CONCAT(submitter.first_name, " ", COALESCE(submitter.last_name, "")) as submitter_name,
            CONCAT(reviewer.first_name, " ", COALESCE(reviewer.last_name, "")) as reviewer_name,
            k.no_kontrak')
            ->join('customers c', 'c.id = uar.customer_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = uar.unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('kontrak k', 'k.id = uar.kontrak_id', 'left')
            ->join('users submitter', 'submitter.id = uar.submitted_by', 'left')
            ->join('users reviewer', 'reviewer.id = uar.reviewed_by', 'left');

        if (!empty($filters['status'])) {
            $builder->where('uar.status', $filters['status']);
        }
        if (!empty($filters['customer_id'])) {
            $builder->where('uar.customer_id', $filters['customer_id']);
        }
        if (!empty($filters['request_type'])) {
            $builder->where('uar.request_type', $filters['request_type']);
        }
        if (!empty($filters['submitted_by'])) {
            $builder->where('uar.submitted_by', $filters['submitted_by']);
        }

        $builder->orderBy('uar.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get units linked to a customer via kontrak_unit
     */
    public function getUnitsForCustomer(int $customerId): array
    {
        return $this->db->table('kontrak_unit ku')
            ->select('ku.id as kontrak_unit_id,
                ku.kontrak_id,
                ku.unit_id,
                ku.status as ku_status,
                ku.is_spare,
                ku.harga_sewa,
                k.no_kontrak,
                k.rental_type,
                k.jenis_sewa,
                k.status as contract_status,
                iu.id_inventory_unit,
                COALESCE(iu.no_unit, iu.no_unit_na) as no_unit,
                iu.serial_number,
                iu.lokasi_unit,
                iu.status_unit_id,
                su.status_unit as status_name,
                mu.merk_unit,
                mu.model_unit,
                tu.tipe as tipe_unit,
                (SELECT cl.location_name FROM customer_locations cl WHERE cl.id = ku.customer_location_id LIMIT 1) as lokasi_kontrak')
            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id', 'left')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->where('k.customer_id', $customerId)
            ->whereIn('ku.status', ['ACTIVE', 'PULLED', 'REPLACED'])
            ->where('ku.is_temporary', 0)
            ->orderBy('k.no_kontrak', 'ASC')
            ->orderBy('iu.no_unit', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get customers that have active contracts with units
     */
    public function getCustomersWithUnits(): array
    {
        return $this->db->table('customers c')
            ->select('c.id, c.customer_name, c.customer_code, c.is_active,
                COUNT(DISTINCT ku.unit_id) as total_units,
                COUNT(DISTINCT k.id) as total_contracts')
            ->join('kontrak k', 'k.customer_id = c.id')
            ->join('kontrak_unit ku', 'ku.kontrak_id = k.id AND ku.status IN ("ACTIVE","PULLED","REPLACED") AND ku.is_temporary = 0')
            ->where('c.is_active', 1)
            ->groupBy('c.id')
            ->having('total_units >', 0)
            ->orderBy('c.customer_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get statistics
     */
    public function getStats(): array
    {
        $total     = $this->countAllResults(false);
        $submitted = $this->where('status', 'SUBMITTED')->countAllResults(false);
        $approved  = $this->where('status', 'APPROVED')->countAllResults(false);
        $rejected  = $this->where('status', 'REJECTED')->countAllResults(false);

        return [
            'total'     => $total,
            'submitted' => $submitted,
            'approved'  => $approved,
            'rejected'  => $rejected,
        ];
    }

    /**
     * Get pending requests for marketing approval
     */
    public function getPendingRequests(): array
    {
        return $this->getWithDetails(['status' => 'SUBMITTED']);
    }

    // ── Approval Logic ──────────────────────────────────

    /**
     * Approve and apply the change
     */
    public function approveAndApply(int $id, int $reviewerId, ?string $notes = null): array
    {
        $request = $this->find($id);
        if (!$request) {
            return ['success' => false, 'message' => 'Request tidak ditemukan'];
        }
        if ($request['status'] !== 'SUBMITTED') {
            return ['success' => false, 'message' => 'Request sudah diproses'];
        }

        $db = $this->db;
        $db->transStart();

        try {
            // Mark as approved
            $this->update($id, [
                'status'       => 'APPROVED',
                'reviewed_by'  => $reviewerId,
                'reviewed_at'  => date('Y-m-d H:i:s'),
                'review_notes' => $notes,
            ]);

            // Apply the change based on request type
            $proposed = json_decode($request['proposed_data'], true) ?? [];
            $unitId   = $request['unit_id'];

            switch ($request['request_type']) {
                case 'LOCATION_MISMATCH':
                    // Update unit location
                    if ($unitId && !empty($proposed['new_location'])) {
                        $db->table('inventory_unit')
                           ->where('id_inventory_unit', $unitId)
                           ->update(['lokasi_unit' => $proposed['new_location']]);
                    }
                    break;

                case 'MARK_SPARE':
                    // Mark unit as spare in kontrak_unit
                    if ($unitId && $request['kontrak_id']) {
                        $db->table('kontrak_unit')
                           ->where('unit_id', $unitId)
                           ->where('kontrak_id', $request['kontrak_id'])
                           ->whereIn('status', ['ACTIVE', 'PULLED', 'REPLACED'])
                           ->update(['is_spare' => 1]);
                    }
                    break;

                case 'ADD_UNIT':
                    // Add unit to contract
                    if (!empty($proposed['unit_id']) && $request['kontrak_id']) {
                        $db->table('kontrak_unit')->insert([
                            'kontrak_id'  => $request['kontrak_id'],
                            'unit_id'     => $proposed['unit_id'],
                            'status'      => 'ACTIVE',
                            'is_spare'    => $proposed['is_spare'] ?? 0,
                            'harga_sewa'  => $proposed['harga_sewa'] ?? null,
                            'created_at'  => date('Y-m-d H:i:s'),
                        ]);
                    }
                    break;

                case 'UNIT_SWAP':
                    // Swap unit: deactivate old, activate new
                    if ($unitId && !empty($proposed['new_unit_id']) && $request['kontrak_id']) {
                        // Pull old unit
                        $db->table('kontrak_unit')
                           ->where('unit_id', $unitId)
                           ->where('kontrak_id', $request['kontrak_id'])
                           ->whereIn('status', ['ACTIVE'])
                           ->update(['status' => 'PULLED']);

                        // Add new unit
                        $db->table('kontrak_unit')->insert([
                            'kontrak_id' => $request['kontrak_id'],
                            'unit_id'    => $proposed['new_unit_id'],
                            'status'     => 'ACTIVE',
                            'is_spare'   => 0,
                            'harga_sewa' => $proposed['harga_sewa'] ?? null,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    break;

                case 'UNIT_MISSING':
                    // Flag unit — just log it, no automatic action needed
                    break;

                case 'OTHER':
                    // Custom — apply location change if provided
                    if ($unitId && !empty($proposed['new_location'])) {
                        $db->table('inventory_unit')
                           ->where('id_inventory_unit', $unitId)
                           ->update(['lokasi_unit' => $proposed['new_location']]);
                    }
                    break;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['success' => false, 'message' => 'Database transaction failed'];
            }

            return ['success' => true, 'message' => 'Request approved and changes applied'];
        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reject request
     */
    public function rejectRequest(int $id, int $reviewerId, ?string $notes = null): array
    {
        $request = $this->find($id);
        if (!$request) {
            return ['success' => false, 'message' => 'Request tidak ditemukan'];
        }
        if ($request['status'] !== 'SUBMITTED') {
            return ['success' => false, 'message' => 'Request sudah diproses'];
        }

        $this->update($id, [
            'status'       => 'REJECTED',
            'reviewed_by'  => $reviewerId,
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'review_notes' => $notes,
        ]);

        return ['success' => true, 'message' => 'Request rejected'];
    }
}
