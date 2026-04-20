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
            COALESCE(iu.no_unit, iu.no_unit_na, (SELECT iu2.no_unit FROM inventory_unit iu2 WHERE iu2.id_inventory_unit = CAST(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(uar.proposed_data, \'{}\'), "$.unit_id")) AS UNSIGNED) LIMIT 1)) as no_unit,
            iu.no_unit_na,
            COALESCE(iu.serial_number, (SELECT iu2.serial_number FROM inventory_unit iu2 WHERE iu2.id_inventory_unit = CAST(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(uar.proposed_data, \'{}\'), "$.unit_id")) AS UNSIGNED) LIMIT 1)) as serial_number,
            iu.lokasi_unit,
            c.customer_name,
            c.customer_code,
            mu.merk_unit,
            mu.model_unit,
            CONCAT(submitter.first_name, " ", COALESCE(submitter.last_name, "")) as submitter_name,
            CONCAT(reviewer.first_name, " ", COALESCE(reviewer.last_name, "")) as reviewer_name,
            k.no_kontrak,
            COALESCE(
                (SELECT cl_loc.location_name FROM customer_locations cl_loc WHERE cl_loc.id = CAST(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(uar.proposed_data, \'{}\'), "$.customer_location_id")) AS UNSIGNED) LIMIT 1),
                (SELECT cl_ku.location_name FROM kontrak_unit ku_loc JOIN customer_locations cl_ku ON cl_ku.id = ku_loc.customer_location_id WHERE ku_loc.kontrak_id = uar.kontrak_id LIMIT 1)
            ) as lokasi_kontrak')
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
        if (!empty($filters['id'])) {
            $builder->where('uar.id', (int) $filters['id']);
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
            ->where('ku.status', 'ACTIVE')
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
            ->join('kontrak_unit ku', 'ku.kontrak_id = k.id AND ku.status = "ACTIVE" AND ku.is_temporary = 0')
            ->where('c.is_active', 1)
            ->groupBy('c.id')
            ->having('total_units >', 0)
            ->orderBy('c.customer_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get statistics (single query)
     */
    public function getStats(): array
    {
        $row = $this->db->table('unit_audit_requests')
            ->select("COUNT(*) as total,
                SUM(status='SUBMITTED') as submitted,
                SUM(status='APPROVED')  as approved,
                SUM(status='REJECTED')  as rejected")
            ->get()->getRowArray();

        return [
            'total'     => (int) ($row['total']     ?? 0),
            'submitted' => (int) ($row['submitted'] ?? 0),
            'approved'  => (int) ($row['approved']  ?? 0),
            'rejected'  => (int) ($row['rejected']  ?? 0),
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
    public function approveAndApply(int $id, int $reviewerId, ?string $notes = null, array $overrides = []): array
    {
        $request = $this->find($id);
        if (!$request) {
            return ['success' => false, 'message' => 'Request tidak ditemukan'];
        }
        if ($request['status'] !== 'SUBMITTED') {
            return ['success' => false, 'message' => 'Request sudah diproses'];
        }

        $proposed = json_decode($request['proposed_data'], true) ?? [];

        // Resolve effective values (overrides from Marketing > proposed_data)
        $kontrakId     = $overrides['kontrak_id']     ?? $request['kontrak_id'] ?? null;
        $hargaSewa     = $overrides['harga_sewa']     ?? $proposed['harga_sewa'] ?? null;
        $isSpare       = isset($overrides['is_spare']) ? (int) $overrides['is_spare'] : (int) ($proposed['is_spare'] ?? 0);
        $missingAction = $overrides['missing_action'] ?? 'record';
        // For transfer releases, always pull regardless of UI selection —
        // the purpose of this request IS to release the unit from its source contract.
        if (!empty($proposed['is_transfer'])) {
            $missingAction = 'pull';
        }

        // ADD_UNIT requires a contract
        if ($request['request_type'] === 'ADD_UNIT' && empty($kontrakId)) {
            return ['success' => false, 'message' => 'Pilih kontrak terlebih dahulu sebelum approve.'];
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
            $unitId = $request['unit_id'];

            switch ($request['request_type']) {
                case 'LOCATION_MISMATCH':
                    if ($unitId && !empty($proposed['new_location'])) {
                        $db->table('inventory_unit')
                           ->where('id_inventory_unit', $unitId)
                           ->update(['lokasi_unit' => $proposed['new_location']]);
                    }
                    break;

                case 'MARK_SPARE':
                    if ($unitId && $kontrakId) {
                        $db->table('kontrak_unit')
                           ->where('unit_id', $unitId)
                           ->where('kontrak_id', $kontrakId)
                           ->where('status', 'ACTIVE')
                           ->update(['is_spare' => 1]);

                        // Option 2: sync inventory_unit status to SPARE (id=15)
                        // so the inventory list reflects the unit is a backup/spare unit.
                        $db->table('inventory_unit')
                           ->where('id_inventory_unit', $unitId)
                           ->update(['status_unit_id' => 15]);
                    }
                    break;

                case 'ADD_UNIT':
                    // $kontrakId already validated above (not empty)
                    if (!empty($proposed['unit_id'])) {
                        // If this ADD_UNIT is part of a transfer, the paired release request
                        // (UNIT_MISSING/pull) must be APPROVED first to avoid double-contract.
                        if (!empty($proposed['linked_release_id'])) {
                            $releaseReq = $this->find((int) $proposed['linked_release_id']);
                            if ($releaseReq && $releaseReq['status'] !== 'APPROVED') {
                                $db->transRollback();
                                $releaseAudit = $releaseReq['audit_number'] ?? '#' . $proposed['linked_release_id'];
                                return [
                                    'success' => false,
                                    'message' => "Harap approve dulu request pelepasan unit dari kontrak asal (No. Audit: {$releaseAudit}) sebelum meng-approve request tambah ini.",
                                ];
                            }
                        }

                        $db->table('kontrak_unit')->insert([
                            'kontrak_id'           => $kontrakId,
                            'unit_id'              => $proposed['unit_id'],
                            'status'               => 'ACTIVE',
                            'is_spare'             => $isSpare,
                            'harga_sewa'           => $hargaSewa ?: null,
                            'tanggal_mulai'        => date('Y-m-d'),
                            'customer_location_id' => $proposed['customer_location_id'] ?? null,
                            'created_by'           => $reviewerId,
                            'created_at'           => date('Y-m-d H:i:s'),
                        ]);

                        // Option 2: sync inventory_unit status when unit enters a contract as spare
                        $db->table('inventory_unit')
                           ->where('id_inventory_unit', $proposed['unit_id'])
                           ->update(['status_unit_id' => $isSpare ? 15 : 7]); // 15=SPARE, 7=RENTAL_ACTIVE
                    }
                    break;

                case 'UNIT_SWAP':
                    if ($unitId && !empty($proposed['new_unit_id']) && $kontrakId) {
                        // Fetch old kontrak_unit to inherit is_spare
                        $oldKu = $db->table('kontrak_unit')
                            ->where('unit_id', $unitId)
                            ->where('kontrak_id', $kontrakId)
                            ->where('status', 'ACTIVE')
                            ->get()->getRowArray();
                        $inheritSpare = $oldKu ? (int) $oldKu['is_spare'] : 0;

                        // Pull old unit
                        $db->table('kontrak_unit')
                           ->where('unit_id', $unitId)
                           ->where('kontrak_id', $kontrakId)
                           ->where('status', 'ACTIVE')
                           ->update(['status' => 'PULLED']);

                        // Insert new unit (inherit spare status and location from old unit)
                        $db->table('kontrak_unit')->insert([
                            'kontrak_id'           => $kontrakId,
                            'unit_id'              => $proposed['new_unit_id'],
                            'status'               => 'ACTIVE',
                            'is_spare'             => $inheritSpare,
                            'harga_sewa'           => $hargaSewa ?: null,
                            'tanggal_mulai'        => date('Y-m-d'),
                            'customer_location_id' => $oldKu['customer_location_id'] ?? null,
                            'created_by'           => $reviewerId,
                            'created_at'           => date('Y-m-d H:i:s'),
                        ]);

                        // Option 2: sync inventory status
                        $db->table('inventory_unit')
                           ->where('id_inventory_unit', $unitId)
                           ->update(['status_unit_id' => 12]); // 12=RETURNED
                        $db->table('inventory_unit')
                           ->where('id_inventory_unit', $proposed['new_unit_id'])
                           ->update(['status_unit_id' => 7]); // 7=RENTAL_ACTIVE
                    }
                    break;

                case 'UNIT_MISSING':
                    if ($missingAction === 'pull' && $unitId && $kontrakId) {
                        // If a specific kontrak_unit row is specified (e.g. transfer flow), pull by ID.
                        // Otherwise fall back to unit_id + kontrak_id lookup.
                        if (!empty($proposed['kontrak_unit_id'])) {
                            $db->table('kontrak_unit')
                               ->where('id', (int) $proposed['kontrak_unit_id'])
                               ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE', 'Aktif'])
                               ->update(['status' => 'PULLED', 'tanggal_selesai' => date('Y-m-d')]);
                        } else {
                            $db->table('kontrak_unit')
                               ->where('unit_id', $unitId)
                               ->where('kontrak_id', $kontrakId)
                               ->where('status', 'ACTIVE')
                               ->update(['status' => 'PULLED']);
                        }

                        // Option 2: after pulling, check if unit is still spare in another active contract.
                        // If not, revert inventory status to RETURNED (12).
                        $stillSpare = $db->table('kontrak_unit')
                            ->where('unit_id', $unitId)
                            ->where('is_spare', 1)
                            ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE'])
                            ->countAllResults();
                        if (!$stillSpare) {
                            $stillActive = $db->table('kontrak_unit')
                                ->where('unit_id', $unitId)
                                ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE'])
                                ->countAllResults();
                            $revertStatus = $stillActive ? 7 : 12; // 7=RENTAL_ACTIVE, 12=RETURNED
                            $db->table('inventory_unit')
                               ->where('id_inventory_unit', $unitId)
                               ->update(['status_unit_id' => $revertStatus]);
                        }
                    }
                    // 'record' → no DB change, already marked APPROVED above
                    break;

                case 'OTHER':
                    if ($unitId && !empty($proposed['new_location'])) {
                        $db->table('inventory_unit')
                           ->where('id_inventory_unit', $unitId)
                           ->update(['lokasi_unit' => $proposed['new_location']]);
                    }
                    break;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $dbErr = $db->error();
                $errMsg = $dbErr['message'] ?? 'Database transaction failed';
                log_message('error', 'approveAndApply[' . $request['request_type'] . '] failed: ' . json_encode($dbErr));
                return ['success' => false, 'message' => 'Database error: ' . $errMsg];
            }

            return ['success' => true, 'message' => 'Request approved dan perubahan telah diterapkan'];
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'approveAndApply exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reject request — cascades to paired transfer request if applicable
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

        $proposed = json_decode($request['proposed_data'] ?? '{}', true);
        $now      = date('Y-m-d H:i:s');

        $this->db->transStart();

        $this->update($id, [
            'status'       => 'REJECTED',
            'reviewed_by'  => $reviewerId,
            'reviewed_at'  => $now,
            'review_notes' => $notes,
        ]);

        // Cascade: reject the paired transfer request
        $pairedId = null;
        if ($request['request_type'] === 'ADD_UNIT' && !empty($proposed['linked_release_id'])) {
            // ADD_UNIT rejected → also reject the paired UNIT_MISSING release
            $pairedId = (int) $proposed['linked_release_id'];
        } elseif ($request['request_type'] === 'UNIT_MISSING' && !empty($proposed['is_transfer'])) {
            // UNIT_MISSING release rejected → find the paired ADD_UNIT that references this ID
            // Use JSON_EXTRACT for reliable matching (LIKE can miss spaces in stored JSON)
            $paired = $this->db->table('unit_audit_requests')
                ->where('request_type', 'ADD_UNIT')
                ->where('status', 'SUBMITTED')
                ->where('CAST(JSON_UNQUOTE(JSON_EXTRACT(proposed_data, "$.linked_release_id")) AS UNSIGNED)', $id)
                ->get()->getRowArray();
            $pairedId = $paired ? (int) $paired['id'] : null;
        }

        if ($pairedId) {
            $this->db->table('unit_audit_requests')
                ->where('id', $pairedId)
                ->where('status', 'SUBMITTED')
                ->update([
                    'status'       => 'REJECTED',
                    'reviewed_by'  => $reviewerId,
                    'reviewed_at'  => $now,
                    'review_notes' => 'Dibatalkan karena request pasangan ditolak',
                    'updated_at'   => $now,
                ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menolak request'];
        }

        return ['success' => true, 'message' => 'Request rejected'];
    }
}
