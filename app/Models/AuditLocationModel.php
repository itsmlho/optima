<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLocationModel extends Model
{
    protected $table            = 'unit_audit_locations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'audit_number',
        'customer_id',
        'customer_location_id',
        'kontrak_id',
        'audit_date',
        'audit_completed_date',
        'audited_by',
        'status',
        'kontrak_total_units',
        'kontrak_spare_units',
        'kontrak_has_operator',
        'actual_total_units',
        'actual_spare_units',
        'actual_has_operator',
        'has_discrepancy',
        'unit_difference',
        'price_per_unit',
        'total_price_adjustment',
        'mechanic_notes',
        'service_notes',
        'marketing_notes',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ── Helpers ──────────────────────────────────────────

    /**
     * Generate audit number AUDLOC-YYYYMMDD-NNNN
     */
    public function generateAuditNumber(): string
    {
        $prefix = 'AUDLOC-' . date('Ymd') . '-';

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
     * Get locations for a customer that have active contracts
     */
    public function getLocationsForCustomer(int $customerId): array
    {
        return $this->db->table('customer_locations cl')
            ->select('cl.id, cl.location_name, cl.address, cl.contact_person, cl.phone,
                COUNT(ku.id) as total_units,
                SUM(CASE WHEN ku.is_spare = 1 THEN 1 ELSE 0 END) as spare_units')
            ->join('kontrak_unit ku', 'ku.customer_location_id = cl.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
            ->join('kontrak k', 'k.id = ku.kontrak_id AND k.status = "ACTIVE"', 'left')
            ->where('cl.customer_id', $customerId)
            ->where('cl.is_active', 1)
            ->groupBy('cl.id')
            ->orderBy('cl.location_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get units at a specific location from kontrak_unit
     */
    public function getUnitsForLocation(int $locationId): array
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
                k.status as kontrak_status,
                iu.id_inventory_unit,
                COALESCE(iu.no_unit, iu.no_unit_na) as no_unit,
                iu.serial_number,
                iu.lokasi_unit,
                iu.status_unit_id,
                su.status_unit as status_name,
                mu.merk_unit,
                mu.model_unit,
                tu.tipe as tipe_unit,
                tc.kapasitas as kapasitas')
            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id', 'left')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->join('kapasitas tc', 'tc.id_kapasitas = iu.kapasitas_unit_id', 'left')
            ->where('ku.customer_location_id', $locationId)
            ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
            ->where('ku.is_temporary', 0)
            ->orderBy('iu.no_unit', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get location info with customer and contract details
     */
    public function getLocationDetails(int $locationId): ?array
    {
        return $this->db->table('customer_locations cl')
            ->select('cl.*,
                c.customer_name,
                c.customer_code,
                (SELECT COUNT(*) FROM kontrak_unit ku WHERE ku.customer_location_id = cl.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0) as total_units,
                (SELECT COUNT(*) FROM kontrak_unit ku WHERE ku.customer_location_id = cl.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_spare = 1 AND ku.is_temporary = 0) as spare_units')
            ->join('customers c', 'c.id = cl.customer_id', 'left')
            ->where('cl.id', $locationId)
            ->get()
            ->getRowArray();
    }

    /**
     * Get customers that have active contracts with units at locations
     */
    public function getCustomersWithLocations(): array
    {
        return $this->db->table('customers c')
            ->select('c.id, c.customer_name, c.customer_code, c.is_active,
                COUNT(DISTINCT cl.id) as total_locations,
                COUNT(DISTINCT ku.unit_id) as total_units')
            ->join('customer_locations cl', 'cl.customer_id = c.id AND cl.is_active = 1', 'left')
            ->join('kontrak_unit ku', 'ku.customer_location_id = cl.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
            ->join('kontrak k', 'k.id = ku.kontrak_id AND k.status = "ACTIVE"', 'left')
            ->where('c.is_active', 1)
            ->groupBy('c.id')
            ->having('total_units >', 0)
            ->orderBy('c.customer_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    // ── CRUD Operations ─────────────────────────────────

    /**
     * Create new audit location with expected units
     */
    public function createAuditLocation(array $data): int
    {
        // Get expected units from kontrak_unit
        $units = $this->getUnitsForLocation($data['customer_location_id']);

        $db = $this->db;
        $db->transStart();

        try {
            // Insert audit location header
            $auditNumber = $this->generateAuditNumber();

            // Get contract info
            $kontrakInfo = $this->db->table('kontrak_unit ku')
                ->select('ku.kontrak_id, k.no_kontrak')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->where('ku.customer_location_id', $data['customer_location_id'])
                ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->get()
                ->getRowArray();

            // Count spare units
            $spareCount = 0;
            $totalUnits = count($units);
            foreach ($units as $u) {
                if ($u['is_spare'] == 1) {
                    $spareCount++;
                }
            }

            $headerData = [
                'audit_number'         => $auditNumber,
                'customer_id'          => $data['customer_id'],
                'customer_location_id' => $data['customer_location_id'],
                'kontrak_id'           => $kontrakInfo['kontrak_id'] ?? null,
                'audit_date'           => $data['audit_date'] ?? date('Y-m-d'),
                'status'               => 'DRAFT',
                'kontrak_total_units'  => $totalUnits,
                'kontrak_spare_units'  => $spareCount,
                'kontrak_has_operator' => 0, // TODO: Check if operator is in contract
                'submitted_by'         => $data['submitted_by'],
            ];

            $this->insert($headerData);
            $auditId = $this->insertID;

            // Insert expected units as items
            $itemsModel = new AuditLocationItemModel();
            foreach ($units as $unit) {
                $itemsModel->insert([
                    'audit_location_id' => $auditId,
                    'kontrak_unit_id'   => $unit['kontrak_unit_id'],
                    'unit_id'           => $unit['unit_id'],
                    'expected_no_unit'  => $unit['no_unit'],
                    'expected_serial'   => $unit['serial_number'],
                    'expected_merk'    => $unit['merk_unit'],
                    'expected_model'    => $unit['model_unit'],
                    'expected_is_spare' => $unit['is_spare'] ?? 0,
                    'expected_status'   => $unit['ku_status'],
                ]);
            }

            $db->transComplete();

            return $auditId;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Get audit location with details
     */
    public function getWithDetails(int $id): ?array
    {
        $header = $this->db->table('unit_audit_locations ual')
            ->select('ual.*,
                c.customer_name,
                c.customer_code,
                cl.location_name,
                cl.address as location_address,
                cl.contact_person,
                cl.phone as location_phone,
                k.no_kontrak,
                CONCAT(u.first_name, " ", COALESCE(u.last_name, "")) as submitter_name,
                CONCAT(reviewer.first_name, " ", COALESCE(reviewer.last_name, "")) as reviewer_name')
            ->join('customers c', 'c.id = ual.customer_id', 'left')
            ->join('customer_locations cl', 'cl.id = ual.customer_location_id', 'left')
            ->join('kontrak k', 'k.id = ual.kontrak_id', 'left')
            ->join('users u', 'u.id = ual.submitted_by', 'left')
            ->join('users reviewer', 'reviewer.id = ual.reviewed_by', 'left')
            ->where('ual.id', $id)
            ->get()
            ->getRowArray();

        if (!$header) {
            return null;
        }

        // Get items
        $itemsModel = new AuditLocationItemModel();
        $header['items'] = $itemsModel->getByAuditLocation($id);

        return $header;
    }

    /**
     * Get all audits for listing
     */
    public function getAllAudits(array $filters = []): array
    {
        $builder = $this->db->table('unit_audit_locations ual')
            ->select('ual.id, ual.audit_number, ual.customer_id, ual.customer_location_id,
                ual.audit_date, ual.status, ual.has_discrepancy,
                c.customer_name,
                cl.location_name,
                ual.kontrak_total_units, ual.actual_total_units,
                ual.kontrak_spare_units, ual.actual_spare_units')
            ->join('customers c', 'c.id = ual.customer_id', 'left')
            ->join('customer_locations cl', 'cl.id = ual.customer_location_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('ual.status', $filters['status']);
        }
        if (!empty($filters['customer_id'])) {
            $builder->where('ual.customer_id', $filters['customer_id']);
        }

        $builder->orderBy('ual.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get pending approvals for Marketing
     */
    public function getPendingApprovals(): array
    {
        return $this->getAllAudits(['status' => 'PENDING_APPROVAL']);
    }

    /**
     * Get statistics
     */
    public function getStats(): array
    {
        $total = $this->countAllResults(false);

        return [
            'total'              => $total,
            'draft'              => $this->where('status', 'DRAFT')->countAllResults(false),
            'printed'            => $this->where('status', 'PRINTED')->countAllResults(false),
            'in_progress'        => $this->where('status', 'IN_PROGRESS')->countAllResults(false),
            'results_entered'   => $this->where('status', 'RESULTS_ENTERED')->countAllResults(false),
            'pending_approval'   => $this->where('status', 'PENDING_APPROVAL')->countAllResults(false),
            'approved'           => $this->where('status', 'APPROVED')->countAllResults(false),
            'rejected'           => $this->where('status', 'REJECTED')->countAllResults(false),
        ];
    }

    // ── Approval Workflow ────────────────────────────────

    /**
     * Submit audit results and calculate discrepancies
     */
    public function submitAuditResults(int $id, array $items, array $summary): bool
    {
        $audit = $this->find($id);
        if (!$audit) {
            throw new \Exception('Audit tidak ditemukan');
        }

        $db = $this->db;
        $db->transStart();

        try {
            // Update items
            $itemsModel = new AuditLocationItemModel();
            foreach ($items as $item) {
                $itemsModel->update($item['id'], [
                    'actual_no_unit'         => $item['actual_no_unit'] ?? null,
                    'actual_serial'         => $item['actual_serial'] ?? null,
                    'actual_merk'           => $item['actual_merk'] ?? null,
                    'actual_model'          => $item['actual_model'] ?? null,
                    'actual_is_spare'       => $item['actual_is_spare'] ?? 0,
                    'actual_operator_present' => $item['actual_operator_present'] ?? 0,
                    'result'                => $item['result'] ?? 'MATCH',
                    'notes'                 => $item['notes'] ?? null,
                ]);
            }

            // Update header with summary
            $hasDiscrepancy = ($summary['actual_total_units'] != $audit['kontrak_total_units']) ||
                             ($summary['actual_spare_units'] != $audit['kontrak_spare_units']);

            $this->update($id, [
                'status'                  => 'RESULTS_ENTERED',
                'audit_completed_date'    => date('Y-m-d'),
                'actual_total_units'      => $summary['actual_total_units'],
                'actual_spare_units'      => $summary['actual_spare_units'],
                'actual_has_operator'     => $summary['actual_has_operator'] ?? 0,
                'has_discrepancy'        => $hasDiscrepancy ? 1 : 0,
                'mechanic_notes'          => $summary['mechanic_notes'] ?? null,
                'service_notes'          => $summary['service_notes'] ?? null,
            ]);

            $db->transComplete();

            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Approve audit and update kontrak/inventory
     */
    public function approveAudit(int $id, array $pricing, int $reviewerId): array
    {
        $audit = $this->getWithDetails($id);
        if (!$audit) {
            return ['success' => false, 'message' => 'Audit tidak ditemukan'];
        }
        if ($audit['status'] !== 'PENDING_APPROVAL') {
            return ['success' => false, 'message' => 'Audit sudah diproses'];
        }

        $db = $this->db;
        $db->transStart();

        try {
            // Calculate price adjustment
            $unitDifference = $audit['actual_total_units'] - $audit['kontrak_total_units'];
            $pricePerUnit = $pricing['price_per_unit'] ?? null;
            $totalAdjustment = null;

            if ($unitDifference != 0 && $pricePerUnit) {
                $totalAdjustment = $unitDifference * $pricePerUnit;
            }

            // Update audit header
            $this->update($id, [
                'status'                 => 'APPROVED',
                'reviewed_by'            => $reviewerId,
                'reviewed_at'            => date('Y-m-d H:i:s'),
                'unit_difference'        => $unitDifference,
                'price_per_unit'         => $pricePerUnit,
                'total_price_adjustment' => $totalAdjustment,
                'marketing_notes'        => $pricing['marketing_notes'] ?? null,
            ]);

            // Process each item - apply changes to kontrak_unit and inventory_unit
            $itemsModel = new AuditLocationItemModel();
            foreach ($audit['items'] as $item) {
                switch ($item['result']) {
                    case 'EXTRA_UNIT':
                        // Add new unit to kontrak_unit
                        if ($item['unit_id']) {
                            $db->table('kontrak_unit')->insert([
                                'kontrak_id'           => $audit['kontrak_id'],
                                'unit_id'              => $item['unit_id'],
                                'customer_location_id' => $audit['customer_location_id'],
                                'status'               => 'ACTIVE',
                                'is_spare'             => $item['actual_is_spare'] ?? 0,
                                'harga_sewa'           => $pricePerUnit,
                            ]);
                        }
                        break;

                    case 'NO_UNIT_IN_KONTRAK':
                        // Mark unit as pulled in kontrak_unit
                        if ($item['kontrak_unit_id']) {
                            $db->table('kontrak_unit')
                                ->where('id', $item['kontrak_unit_id'])
                                ->update([
                                    'status'        => 'PULLED',
                                    'tanggal_tarik' => date('Y-m-d'),
                                ]);
                        }
                        break;

                    case 'MISMATCH_SPARE':
                        // Update spare status
                        if ($item['kontrak_unit_id']) {
                            $db->table('kontrak_unit')
                                ->where('id', $item['kontrak_unit_id'])
                                ->update(['is_spare' => $item['actual_is_spare']]);
                        }
                        break;

                    case 'MISMATCH_NO_UNIT':
                        // Update unit number in inventory
                        if ($item['unit_id'] && !empty($item['actual_no_unit'])) {
                            // Check if it's NA or regular
                            $unit = $db->table('inventory_unit')->find($item['unit_id']);
                            if ($unit && !empty($unit['no_unit_na'])) {
                                $db->table('inventory_unit')
                                    ->where('id_inventory_unit', $item['unit_id'])
                                    ->update(['no_unit_na' => $item['actual_no_unit']]);
                            } else {
                                $db->table('inventory_unit')
                                    ->where('id_inventory_unit', $item['unit_id'])
                                    ->update(['no_unit' => $item['actual_no_unit']]);
                            }
                        }
                        break;
                }
            }

            $db->transComplete();

            return [
                'success' => true,
                'message' => 'Audit berhasil diapprove',
                'data'    => [
                    'unit_difference'       => $unitDifference,
                    'total_price_adjustment' => $totalAdjustment,
                ],
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reject audit
     */
    public function rejectAudit(int $id, string $notes, int $reviewerId): array
    {
        $audit = $this->find($id);
        if (!$audit) {
            return ['success' => false, 'message' => 'Audit tidak ditemukan'];
        }
        if ($audit['status'] !== 'PENDING_APPROVAL') {
            return ['success' => false, 'message' => 'Audit sudah diproses'];
        }

        try {
            $this->update($id, [
                'status'        => 'REJECTED',
                'reviewed_by'   => $reviewerId,
                'reviewed_at'   => date('Y-m-d H:i:s'),
                'marketing_notes' => $notes,
            ]);

            return ['success' => true, 'message' => 'Audit ditolak'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ── Status Updates ─────────────────────────────────

    /**
     * Mark as printed (ready for mechanic)
     */
    public function markAsPrinted(int $id): bool
    {
        return (bool) $this->update($id, ['status' => 'PRINTED']);
    }

    /**
     * Mark as in progress (mechanic started)
     */
    public function markAsInProgress(int $id): bool
    {
        return (bool) $this->update($id, ['status' => 'IN_PROGRESS']);
    }

    /**
     * Submit to marketing for approval
     */
    public function submitForApproval(int $id): bool
    {
        return (bool) $this->update($id, ['status' => 'PENDING_APPROVAL']);
    }
}
