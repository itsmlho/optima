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
        'mechanic_name',
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

    /**
     * Older production DBs may omit marketing approval columns on customer_locations.
     *
     * @return list<string>
     */
    private function getCustomerLocationColumnNames(): array
    {
        static $names = null;
        if ($names !== null) {
            return $names;
        }
        $names = [];
        try {
            foreach ($this->db->getFieldData('customer_locations') as $f) {
                if (is_object($f) && isset($f->name)) {
                    $names[] = (string) $f->name;
                } elseif (is_array($f) && isset($f['name'])) {
                    $names[] = (string) $f['name'];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'AuditLocationModel getCustomerLocationColumnNames: {msg}', ['msg' => $e->getMessage()]);
        }

        return $names;
    }

    private function customerLocationHasColumn(string $column): bool
    {
        return in_array($column, $this->getCustomerLocationColumnNames(), true);
    }

    // ── Data Retrieval ──────────────────────────────────

    /**
     * Get locations for a customer that have active contracts OR are pending approval
     * Includes periode (contract dates) and last audit info for Service view
     */
    public function getLocationsForCustomer(int $customerId): array
    {
        $hasApprovalStatus = $this->customerLocationHasColumn('approval_status');
        $hasRequestedBy    = $this->customerLocationHasColumn('requested_by');

        $select = 'cl.id, cl.location_name, cl.address, cl.contact_person, cl.phone';
        if ($hasApprovalStatus) {
            $select .= ', cl.approval_status';
        }
        if ($hasRequestedBy) {
            $select .= ', cl.requested_by';
        }
        $select .= ', cl.area_id, a.area_name, a.area_code,
                COUNT(ku.id) as total_units,
                SUM(CASE WHEN ku.is_spare = 1 THEN 1 ELSE 0 END) as spare_units,
                MIN(k.tanggal_mulai) as periode_start,
                MAX(k.tanggal_berakhir) as periode_end,
                (SELECT k2.rental_type FROM kontrak_unit ku2
                 JOIN kontrak k2 ON k2.id = ku2.kontrak_id
                 WHERE ku2.customer_location_id = cl.id AND ku2.status IN ("ACTIVE","TEMP_ACTIVE","Aktif")
                 AND (ku2.is_temporary IS NULL OR ku2.is_temporary = 0)
                 LIMIT 1) as rental_type,
                (SELECT k2.no_kontrak FROM kontrak_unit ku2
                 JOIN kontrak k2 ON k2.id = ku2.kontrak_id
                 WHERE ku2.customer_location_id = cl.id AND ku2.status IN ("ACTIVE","TEMP_ACTIVE","Aktif")
                 AND (ku2.is_temporary IS NULL OR ku2.is_temporary = 0)
                 LIMIT 1) as no_kontrak,
                (SELECT k2.customer_po_number FROM kontrak_unit ku2
                 JOIN kontrak k2 ON k2.id = ku2.kontrak_id
                 WHERE ku2.customer_location_id = cl.id AND ku2.status IN ("ACTIVE","TEMP_ACTIVE","Aktif")
                 AND (ku2.is_temporary IS NULL OR ku2.is_temporary = 0)
                 LIMIT 1) as customer_po_number';

        $builder = $this->db->table('customer_locations cl')
            ->select($select)
            ->join('areas a', 'a.id = cl.area_id', 'left')
            ->join('kontrak_unit ku', 'ku.customer_location_id = cl.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE","Aktif") AND (ku.is_temporary IS NULL OR ku.is_temporary = 0)', 'left')
            ->join('kontrak k', 'k.id = ku.kontrak_id AND k.status IN ("ACTIVE","TEMP_ACTIVE","Aktif")', 'left')
            ->where('cl.customer_id', $customerId)
            ->where('cl.is_active', 1);

        if ($hasApprovalStatus) {
            $builder->groupStart()
                ->where('cl.approval_status IS NULL')
                ->orWhere('cl.approval_status', 'APPROVED')
                ->orWhere('cl.approval_status', 'PENDING')
            ->groupEnd();
        }

        $builder->groupBy('cl.id');
        if ($hasApprovalStatus) {
            $builder->orderBy('cl.approval_status', 'DESC');
        }
        $rows = $builder->orderBy('cl.location_name', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            if (! $hasApprovalStatus) {
                $row['approval_status'] = null;
            }
            if (! $hasRequestedBy) {
                $row['requested_by'] = null;
            }
            $row['no_kontrak_masked']   = $this->maskContractNumber($row['no_kontrak'] ?? null);
            $row['no_po_masked']        = $this->maskContractNumber($row['customer_po_number'] ?? null);
            $row['periode_text']        = $this->formatPeriode($row['periode_start'] ?? null, $row['periode_end'] ?? null);
            $row['periode_status_text'] = $this->getPeriodeStatusText($row['periode_end'] ?? null);
            $rentalType = $row['rental_type'] ?? null;
            $periodeEnd = $row['periode_end'] ?? null;
            $row['periode_badge_text']  = (strtoupper((string) $rentalType) === 'PO_ONLY')
                ? 'Bulanan'
                : ($periodeEnd ? ('Valid s/d ' . date('d/m/Y', strtotime($periodeEnd))) : '—');
            try {
                $lastAudit = $this->getLastAuditByLocation((int) $row['id']);
            } catch (\Throwable $e) {
                log_message('error', 'getLastAuditByLocation failed for location {id}: {msg}', [
                    'id' => $row['id'] ?? 0,
                    'msg' => $e->getMessage(),
                ]);
                $lastAudit = null;
            }
            $row['last_audit'] = $lastAudit;
            $row['due_for_reaudit'] = $lastAudit ? $this->isDueForReaudit($lastAudit) : true;

            $row['is_pending_approval'] = (($row['approval_status'] ?? null) === 'PENDING');
        }
        return $rows;
    }

    /**
     * Mask contract number for Service view (public for controller use)
     */
    public function maskContractNumberForView(?string $noKontrak): string
    {
        return $this->maskContractNumber($noKontrak);
    }

    /**
     * Mask PO number for Service view (no_po / customer_po_number)
     */
    public function maskPoNumberForView(?string $noPo): string
    {
        return $this->maskContractNumber($noPo);
    }

    /**
     * Mask contract number for Service view
     */
    protected function maskContractNumber(?string $noKontrak): string
    {
        if (empty($noKontrak)) {
            return '-';
        }
        $parts = preg_split('/[\/\-]/', trim($noKontrak));
        $masked = [];
        foreach ($parts as $p) {
            $p = trim($p);
            $pLen = strlen($p);
            if ($pLen <= 4) {
                $masked[] = str_repeat('*', $pLen);
            } else {
                $masked[] = substr($p, 0, 2) . str_repeat('*', $pLen - 4) . substr($p, -2);
            }
        }
        return implode('/', $masked);
    }

    /**
     * Format periode from start/end dates
     */
    protected function formatPeriode(?string $start, ?string $end): string
    {
        if (!$start && !$end) {
            return '-';
        }
        $s = $start ? date('d/m/Y', strtotime($start)) : '?';
        $e = $end ? date('d/m/Y', strtotime($end)) : '?';
        return $s . ' - ' . $e;
    }

    /**
     * Status periode untuk mekanik: Aktif / Tinggal X hari / Sudah lewat X hari (ajuan unit pulang)
     */
    public function getPeriodeStatusText(?string $periodeEnd): string
    {
        if (empty($periodeEnd)) {
            return '—';
        }
        $today = date('Y-m-d');
        $end = date('Y-m-d', strtotime($periodeEnd));
        if ($end < $today) {
            $days = (int) floor((strtotime($today) - strtotime($end)) / 86400);
            return 'Sudah lewat ' . $days . ' hari — ajukan unit pulang';
        }
        $days = (int) floor((strtotime($end) - strtotime($today)) / 86400);
        if ($days <= 0) {
            return 'Berakhir hari ini';
        }
        if ($days <= 30) {
            return 'Tinggal ' . $days . ' hari — perhatikan pengembalian';
        }
        return 'Aktif (tinggal ' . $days . ' hari)';
    }

    /**
     * Audit statuses where admin may run inventory unit verification (modal) for units on that audit.
     */
    private const VERIFY_ELIGIBLE_AUDIT_STATUSES = ['PRINTED', 'IN_PROGRESS', 'RESULTS_ENTERED', 'PENDING_APPROVAL', 'APPROVED'];

    /**
     * Latest location audit eligible for per-unit verification (newest by id).
     */
    public function getLatestVerifiableAuditForLocation(int $customerLocationId): ?array
    {
        $row = $this->db->table('unit_audit_locations ual')
            ->select('ual.id, ual.audit_number, ual.status, ual.audit_date, ual.mechanic_name, ual.customer_id, ual.customer_location_id')
            ->where('ual.customer_location_id', $customerLocationId)
            ->whereIn('ual.status', self::VERIFY_ELIGIBLE_AUDIT_STATUSES)
            ->orderBy('ual.id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Locations for customer with verification_audit + verification_units (per latest eligible audit per location).
     */
    public function getVerificationLocationsBundleForCustomer(int $customerId): array
    {
        $locations = $this->getLocationsForCustomer($customerId);
        $itemsModel = new AuditLocationItemModel();
        $hasUvhTable = $this->db->tableExists('unit_verification_history');
        $uvhHasAuditId = false;
        if ($hasUvhTable) {
            try {
                $uvhFields = $this->db->getFieldNames('unit_verification_history');
                $uvhHasAuditId = is_array($uvhFields) && in_array('audit_id', $uvhFields, true);
            } catch (\Throwable $e) {
                $uvhHasAuditId = false;
            }
        }

        foreach ($locations as &$loc) {
            $locId = (int) $loc['id'];
            $loc['verification_audit']   = null;
            $loc['verification_units']   = [];
            $audit = $this->getLatestVerifiableAuditForLocation($locId);
            if (!$audit) {
                continue;
            }
            $auditId = (int) $audit['id'];
            $items   = $itemsModel->getByAuditLocation($auditId);
            $verifiedRows = [];
            if ($hasUvhTable && $uvhHasAuditId) {
                $verifiedRows = $this->db->table('unit_verification_history')
                    ->select('unit_id, MAX(verified_at) as verified_at')
                    ->where('audit_id', $auditId)
                    ->where('unit_id IS NOT NULL', null, false)
                    ->groupBy('unit_id')
                    ->get()
                    ->getResultArray();
            }
            $verifiedMap = [];
            foreach ($verifiedRows as $vr) {
                $vuid = (int) ($vr['unit_id'] ?? 0);
                if ($vuid > 0) {
                    $verifiedMap[$vuid] = $vr['verified_at'] ?? null;
                }
            }
            $units   = [];
            foreach ($items as $it) {
                $uid = (int) ($it['unit_id'] ?? 0);
                if ($uid <= 0) {
                    continue;
                }
                $noUnit = $it['actual_no_unit'] ?: $it['expected_no_unit'] ?: null;
                if ($noUnit === null || $noUnit === '') {
                    $noUnit = '-';
                }
                $serial = $it['actual_serial'] ?: $it['expected_serial'] ?: ($it['serial_number'] ?? '');
                $merk   = $it['actual_merk'] ?: $it['expected_merk'] ?: ($it['merk_unit'] ?? '');
                $model  = $it['actual_model'] ?: $it['expected_model'] ?: ($it['model_unit'] ?? '');
                $units[] = [
                    'audit_id'       => $auditId,
                    'audit_item_id'  => (int) $it['id'],
                    'unit_id'        => $uid,
                    'no_unit'        => $noUnit,
                    'serial_number'  => $serial,
                    'merk_model'     => trim($merk . ' ' . $model),
                    'audit_status'   => $audit['status'],
                    'audit_number'   => $audit['audit_number'],
                    'is_verified'    => array_key_exists($uid, $verifiedMap),
                    'verified_at'    => $verifiedMap[$uid] ?? null,
                ];
            }
            $verifiedCount = 0;
            foreach ($units as $u) {
                if (!empty($u['is_verified'])) {
                    $verifiedCount++;
                }
            }
            $totalUnits = count($units);
            $effectiveStatus = $audit['status'];
            if ($totalUnits > 0 && $verifiedCount < $totalUnits && in_array($audit['status'], ['PENDING_APPROVAL', 'APPROVED'], true)) {
                // Guard rail: jangan tampil "waiting approval/approved" sebelum semua unit selesai diverifikasi.
                $effectiveStatus = 'IN_PROGRESS';
            }
            $loc['verification_audit'] = [
                'id'           => $auditId,
                'audit_number' => $audit['audit_number'],
                'status'       => $audit['status'],
                'effective_status' => $effectiveStatus,
                'audit_date'   => $audit['audit_date'] ?? null,
                'verified_units' => $verifiedCount,
                'total_units'    => $totalUnits,
            ];
            $loc['verification_units'] = $units;
        }
        unset($loc);

        return $locations;
    }

    /**
     * All customers that have at least one unit_audit_locations row in a verification-eligible status,
     * with locations filtered to those that have a latest eligible audit (for Unit Verification index).
     *
     * @return list<array{id:int,customer_name:string,customer_code:string,locations:array}>
     */
    public function getVerificationOverviewForService(): array
    {
        $rows = $this->db->table('unit_audit_locations ual')
            ->select('ual.customer_id')
            ->whereIn('ual.status', self::VERIFY_ELIGIBLE_AUDIT_STATUSES)
            ->groupBy('ual.customer_id')
            ->get()
            ->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $cid = (int) ($row['customer_id'] ?? 0);
            if ($cid <= 0) {
                continue;
            }
            $bundle = $this->getVerificationLocationsBundleForCustomer($cid);
            $withAudit = array_values(array_filter($bundle, static function (array $loc): bool {
                return !empty($loc['verification_audit']);
            }));
            if ($withAudit === []) {
                continue;
            }
            $customerRow = $this->db->table('customers')
                ->select('id, customer_name, customer_code')
                ->where('id', $cid)
                ->get()
                ->getRowArray();
            $out[] = [
                'id'             => $cid,
                'customer_name'  => $customerRow['customer_name'] ?? ('Customer #' . $cid),
                'customer_code'  => $customerRow['customer_code'] ?? '',
                'locations'      => $withAudit,
            ];
        }

        usort($out, static function (array $a, array $b): int {
            return strcasecmp((string) ($a['customer_name'] ?? ''), (string) ($b['customer_name'] ?? ''));
        });

        return $out;
    }

    /**
     * Get last completed audit for a location (APPROVED or RESULTS_ENTERED with no discrepancy)
     */
    public function getLastAuditByLocation(int $customerLocationId): ?array
    {
        $row = $this->db->table('unit_audit_locations ual')
            ->select('ual.id, ual.audit_number, ual.audit_completed_date, ual.reviewed_at, ual.status,
                ual.submitted_by, ual.audited_by, ual.mechanic_name,
                CONCAT(u.first_name, " ", COALESCE(u.last_name, "")) as submitter_name,
                CONCAT(mech.first_name, " ", COALESCE(mech.last_name, "")) as audited_by_name')
            ->join('users u', 'u.id = ual.submitted_by', 'left')
            ->join('users mech', 'mech.id = ual.audited_by', 'left')
            ->where('ual.customer_location_id', $customerLocationId)
            ->whereIn('ual.status', ['APPROVED', 'RESULTS_ENTERED'])
            ->orderBy('ual.audit_completed_date', 'DESC')
            ->orderBy('ual.reviewed_at', 'DESC')
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }
        $row['checked_by_name'] = $row['audited_by_name'] ?: $row['mechanic_name'] ?: '-';
        $row['completed_at'] = $row['reviewed_at'] ?: $row['audit_completed_date'];
        return $row;
    }

    /**
     * Check if location is due for re-audit (more than 1 year since last audit)
     */
    public function isDueForReaudit(array $lastAudit): bool
    {
        $completedAt = $lastAudit['reviewed_at'] ?? $lastAudit['audit_completed_date'] ?? null;
        if (!$completedAt) {
            return true;
        }
        $oneYearAgo = date('Y-m-d', strtotime('-1 year'));
        $compareDate = is_string($completedAt) ? substr($completedAt, 0, 10) : $completedAt;
        return $compareDate <= $oneYearAgo;
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
                tc.kapasitas_unit as kapasitas')
            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id', 'left')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->join('kapasitas tc', 'tc.id_kapasitas = iu.kapasitas_unit_id', 'left')
            ->where('ku.customer_location_id', $locationId)
            ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE', 'Aktif'])
            ->where('(ku.is_temporary IS NULL OR ku.is_temporary = 0)', null, false)
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
                (SELECT COUNT(*) FROM kontrak_unit ku WHERE ku.customer_location_id = cl.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE","Aktif") AND (ku.is_temporary IS NULL OR ku.is_temporary = 0)) as total_units,
                (SELECT COUNT(*) FROM kontrak_unit ku WHERE ku.customer_location_id = cl.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE","Aktif") AND ku.is_spare = 1 AND (ku.is_temporary IS NULL OR ku.is_temporary = 0)) as spare_units')
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
            ->join('kontrak_unit ku', 'ku.customer_location_id = cl.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE","Aktif") AND (ku.is_temporary IS NULL OR ku.is_temporary = 0)', 'left')
            ->join('kontrak k', 'k.id = ku.kontrak_id AND k.status IN ("ACTIVE","TEMP_ACTIVE","Aktif")', 'left')
            ->where('c.is_active', 1)
            ->groupBy('c.id')
            ->having('total_units >', 0)
            ->orderBy('c.customer_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Customers with units + location audit summary for Unit Audit page (Select2 + badges).
     * Returns: total_locations, locations_approved, locations_belum_audit, audit_badge (belum_audit|sebagian|sudah_audit).
     */
    public function getCustomersWithLocationAuditSummary(): array
    {
        $customers = $this->getCustomersWithLocations();
        if (empty($customers)) {
            return [];
        }
        $ids = array_column($customers, 'id');
        $approvedCounts = $this->db->table('unit_audit_locations ual')
            ->select('ual.customer_id, COUNT(DISTINCT ual.customer_location_id) as approved_locations')
            ->whereIn('ual.customer_id', $ids)
            ->where('ual.status', 'APPROVED')
            ->groupBy('ual.customer_id')
            ->get()
            ->getResultArray();
        $approvedMap = [];
        foreach ($approvedCounts as $r) {
            $approvedMap[(int) $r['customer_id']] = (int) $r['approved_locations'];
        }
        foreach ($customers as &$c) {
            $totalLoc = (int) ($c['total_locations'] ?? 0);
            $approved = $approvedMap[(int) $c['id']] ?? 0;
            $c['locations_approved']   = $approved;
            $c['locations_belum_audit'] = $totalLoc - $approved;
            $c['audit_badge'] = $totalLoc <= 0 ? 'belum_audit' : ($approved >= $totalLoc ? 'sudah_audit' : ($approved > 0 ? 'sebagian' : 'belum_audit'));
        }
        return $customers;
    }

    /**
     * Get data for Unit Verification page: customers with their locations (same structure as Contract > By Customer).
     * Each location has no_kontrak_masked, periode_text, last_audit, due_for_reaudit, total_units, spare_units.
     */
    public function getVerificationGrouped(): array
    {
        $customers = $this->getCustomersWithLocations();
        $out = [];
        foreach ($customers as $c) {
            $locations = $this->getLocationsForCustomer((int) $c['id']);
            $out[] = [
                'id'             => (int) $c['id'],
                'customer_name'  => $c['customer_name'],
                'customer_code'  => $c['customer_code'] ?? null,
                'total_locations'=> (int) ($c['total_locations'] ?? 0),
                'total_units'    => (int) ($c['total_units'] ?? 0),
                'locations'      => $locations,
            ];
        }
        return $out;
    }

    /**
     * Get latest audit status per location for a customer (badge display in Unit Audit).
     * Returns array keyed by location_id.
     */
    public function getLocationAuditStatusForCustomer(int $customerId): array
    {
        $rows = $this->db->table('unit_audit_locations ual')
            ->select('ual.id, ual.customer_location_id, ual.status, ual.audit_number, ual.audit_date, ual.has_discrepancy')
            ->where('ual.customer_id', $customerId)
            ->orderBy('ual.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $locId = (int) $row['customer_location_id'];
            if (!isset($map[$locId])) {
                $map[$locId] = [
                    'audit_id'        => (int) $row['id'],
                    'status'          => $row['status'],
                    'audit_number'    => $row['audit_number'],
                    'audit_date'      => $row['audit_date'],
                    'has_discrepancy' => (bool) $row['has_discrepancy'],
                ];
            }
        }
        return $map;
    }

    /**
     * Get grouped data for Unit Verification page: only from existing unit_audit_locations.
     * Returns: Customer → Location → audits (with status, date, actions).
     */
    public function getVerificationGroupedFromAudits(): array
    {
        $rows = $this->db->table('unit_audit_locations ual')
            ->select('ual.id as audit_id, ual.audit_number, ual.status, ual.audit_date,
                ual.has_discrepancy, ual.kontrak_total_units, ual.actual_total_units,
                ual.mechanic_notes,
                ual.customer_id, ual.customer_location_id,
                c.customer_name, c.customer_code,
                cl.location_name, cl.address,
                k.no_kontrak, k.customer_po_number, k.tanggal_mulai as periode_start, k.tanggal_berakhir as periode_end')
            ->join('customers c', 'c.id = ual.customer_id', 'left')
            ->join('customer_locations cl', 'cl.id = ual.customer_location_id', 'left')
            ->join('kontrak k', 'k.id = ual.kontrak_id', 'left')
            ->orderBy('c.customer_name', 'ASC')
            ->orderBy('cl.location_name', 'ASC')
            ->orderBy('ual.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $customers = [];
        foreach ($rows as $row) {
            $custId = (int) $row['customer_id'];
            $locId  = (int) $row['customer_location_id'];

            if (!isset($customers[$custId])) {
                $customers[$custId] = [
                    'id'            => $custId,
                    'customer_name' => $row['customer_name'],
                    'customer_code' => $row['customer_code'],
                    'locations'     => [],
                ];
            }

            if (!isset($customers[$custId]['locations'][$locId])) {
                $customers[$custId]['locations'][$locId] = [
                    'id'                  => $locId,
                    'location_name'       => $row['location_name'],
                    'address'             => $row['address'],
                    'no_kontrak_masked'   => $this->maskContractNumber($row['no_kontrak'] ?? null),
                    'no_po_masked'        => $this->maskContractNumber($row['customer_po_number'] ?? null),
                    'periode_text'        => $this->formatPeriode($row['periode_start'] ?? null, $row['periode_end'] ?? null),
                    'periode_status_text' => $this->getPeriodeStatusText($row['periode_end'] ?? null),
                    'audits'              => [],
                ];
            }

            $customers[$custId]['locations'][$locId]['audits'][] = [
                'audit_id'           => (int) $row['audit_id'],
                'audit_number'       => $row['audit_number'],
                'status'             => $row['status'],
                'audit_date'         => $row['audit_date'],
                'has_discrepancy'    => (bool) $row['has_discrepancy'],
                'kontrak_total_units'=> (int) ($row['kontrak_total_units'] ?? 0),
                'actual_total_units' => $row['actual_total_units'] !== null ? (int) $row['actual_total_units'] : null,
            ];
        }

        $out = [];
        foreach ($customers as $cust) {
            $cust['locations'] = array_values($cust['locations']);
            $out[] = $cust;
        }
        return $out;
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
                ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE', 'Aktif'])
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
                'status'               => $data['status'] ?? 'DRAFT',
                'has_discrepancy'      => isset($data['has_discrepancy']) ? (int) $data['has_discrepancy'] : 0,
                'mechanic_notes'       => $data['mechanic_notes'] ?? null,
                'mechanic_name'        => $data['mechanic_name'] ?? null,
                'kontrak_total_units'  => $totalUnits,
                'kontrak_spare_units'  => $spareCount,
                'kontrak_has_operator' => 0,
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
     * Update audit location item results (per-unit Sesuai/Tidak from Verifikasi modal).
     * $items = [ ['unit_id' => id, 'result' => 'sesuai'|'tidak_sesuai', 'reasons' => [], 'keterangan' => ''], ... ]
     */
    public function updateAuditLocationItemResults(int $auditLocationId, array $items): void
    {
        $itemsModel = new AuditLocationItemModel();
        foreach ($items as $it) {
            $unitId = isset($it['unit_id']) ? (int) $it['unit_id'] : 0;
            $result = isset($it['result']) ? $it['result'] : 'sesuai';
            $dbResult = (strtolower((string) $result) === 'tidak_sesuai') ? 'MISMATCH' : 'MATCH';
            $reasons = isset($it['reasons']) && is_array($it['reasons']) ? $it['reasons'] : [];
            $keterangan = isset($it['keterangan']) ? trim((string) $it['keterangan']) : '';
            $extra = isset($it['extra']) && is_array($it['extra']) ? $it['extra'] : [];
            $notes = null;
            if ($dbResult === 'MISMATCH' && (count($reasons) > 0 || $keterangan !== '' || !empty($extra))) {
                $notes = json_encode(['reasons' => $reasons, 'keterangan' => $keterangan, 'extra' => $extra], JSON_UNESCAPED_UNICODE);
            }
            if ($unitId <= 0) {
                continue;
            }
            $rows = $this->db->table('unit_audit_location_items')
                ->where('audit_location_id', $auditLocationId)
                ->where('unit_id', $unitId)
                ->get()
                ->getResultArray();
            foreach ($rows as $row) {
                $update = ['result' => $dbResult];
                if ($notes !== null) {
                    $update['notes'] = $notes;
                }
                $itemsModel->update((int) $row['id'], $update);
            }
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
                k.no_kontrak, k.customer_po_number,
                k.tanggal_mulai as periode_start, k.tanggal_berakhir as periode_end,
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

        $header['no_kontrak_masked']   = $this->maskContractNumber($header['no_kontrak'] ?? null);
        $header['no_po_masked']        = $this->maskContractNumber($header['customer_po_number'] ?? null);
        $header['periode_text']        = $this->formatPeriode($header['periode_start'] ?? null, $header['periode_end'] ?? null);
        $header['periode_status_text'] = $this->getPeriodeStatusText($header['periode_end'] ?? null);

        $itemsModel = new AuditLocationItemModel();
        $header['items'] = $itemsModel->getByAuditLocation($id);

        return $header;
    }

    /**
     * Get all audits for listing
     * For Service: includes masked no_kontrak, periode, last_audit info (no harga)
     */
    public function getAllAudits(array $filters = []): array
    {
        $builder = $this->db->table('unit_audit_locations ual')
            ->select('ual.id, ual.audit_number, ual.customer_id, ual.customer_location_id,
                ual.audit_date, ual.status, ual.has_discrepancy,
                ual.kontrak_id, ual.submitted_by, ual.audited_by, ual.mechanic_name,
                c.customer_name,
                cl.location_name,
                k.no_kontrak, k.customer_po_number, k.tanggal_mulai as periode_start, k.tanggal_berakhir as periode_end,
                ual.kontrak_total_units, ual.actual_total_units,
                ual.kontrak_spare_units, ual.actual_spare_units,
                CONCAT(u.first_name, " ", COALESCE(u.last_name, "")) as submitter_name,
                CONCAT(mech.first_name, " ", COALESCE(mech.last_name, "")) as audited_by_name')
            ->join('customers c', 'c.id = ual.customer_id', 'left')
            ->join('customer_locations cl', 'cl.id = ual.customer_location_id', 'left')
            ->join('kontrak k', 'k.id = ual.kontrak_id', 'left')
            ->join('users u', 'u.id = ual.submitted_by', 'left')
            ->join('users mech', 'mech.id = ual.audited_by', 'left');

        if (!empty($filters['status'])) {
            $builder->where('ual.status', $filters['status']);
        }
        if (!empty($filters['customer_id'])) {
            $builder->where('ual.customer_id', $filters['customer_id']);
        }

        $builder->orderBy('ual.created_at', 'DESC');
        $rows = $builder->get()->getResultArray();

        foreach ($rows as &$row) {
            $row['no_kontrak_masked']   = $this->maskContractNumber($row['no_kontrak'] ?? null);
            $row['no_po_masked']        = $this->maskContractNumber($row['customer_po_number'] ?? null);
            $row['periode_text']        = $this->formatPeriode($row['periode_start'] ?? null, $row['periode_end'] ?? null);
            $row['periode_status_text'] = $this->getPeriodeStatusText($row['periode_end'] ?? null);
            $row['checked_by_name']     = $row['audited_by_name'] ?: $row['mechanic_name'] ?: '-';
        }
        return $rows;
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
     * Supports: update existing items, insert new items (ADD_UNIT)
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
            $itemsModel = new AuditLocationItemModel();
            $hasItemDiscrepancy = false;

            foreach ($items as $key => $item) {
                if (is_numeric($key)) {
                    $item['id'] = (int) $key;
                }
                $result = $item['result'] ?? 'MATCH';
                if ($result !== 'MATCH') {
                    $hasItemDiscrepancy = true;
                }

                if (!empty($item['id'])) {
                    // Update existing item
                    $updateData = [
                        'actual_no_unit'         => $item['actual_no_unit'] ?? null,
                        'actual_serial'         => $item['actual_serial'] ?? null,
                        'actual_merk'           => $item['actual_merk'] ?? null,
                        'actual_model'          => $item['actual_model'] ?? null,
                        'actual_is_spare'       => $item['actual_is_spare'] ?? 0,
                        'actual_operator_present' => $item['actual_operator_present'] ?? 0,
                        'result'                => $result,
                        'notes'                 => $item['notes'] ?? null,
                    ];
                    if (isset($item['unit_id'])) {
                        $updateData['unit_id'] = $item['unit_id'] ?: null;
                    }
                    $itemsModel->update($item['id'], $updateData);
                } else {
                    // Insert new item (ADD_UNIT - unit kurang)
                    if ($result === 'ADD_UNIT' && !empty($item['unit_id'])) {
                        $itemsModel->insert([
                            'audit_location_id'   => $id,
                            'kontrak_unit_id'     => null,
                            'unit_id'             => $item['unit_id'],
                            'expected_no_unit'    => null,
                            'expected_serial'     => null,
                            'expected_merk'       => null,
                            'expected_model'      => null,
                            'expected_is_spare'   => 0,
                            'actual_no_unit'      => $item['actual_no_unit'] ?? null,
                            'actual_serial'       => $item['actual_serial'] ?? null,
                            'actual_merk'         => $item['actual_merk'] ?? null,
                            'actual_model'        => $item['actual_model'] ?? null,
                            'actual_is_spare'     => $item['actual_is_spare'] ?? 0,
                            'actual_operator_present' => 0,
                            'result'              => 'ADD_UNIT',
                            'notes'               => $item['notes'] ?? null,
                        ]);
                    }
                }
            }

            $hasDiscrepancy = $hasItemDiscrepancy ||
                ($summary['actual_total_units'] != $audit['kontrak_total_units']) ||
                ($summary['actual_spare_units'] != $audit['kontrak_spare_units']);

            $headerUpdate = [
                'status'                  => 'RESULTS_ENTERED',
                'audit_completed_date'    => date('Y-m-d'),
                'actual_total_units'      => $summary['actual_total_units'],
                'actual_spare_units'      => $summary['actual_spare_units'],
                'actual_has_operator'     => $summary['actual_has_operator'] ?? 0,
                'has_discrepancy'         => $hasDiscrepancy ? 1 : 0,
                'mechanic_notes'          => $summary['mechanic_notes'] ?? null,
                'service_notes'           => $summary['service_notes'] ?? null,
            ];
            if (isset($summary['audited_by'])) {
                $headerUpdate['audited_by'] = $summary['audited_by'] ?: null;
            }
            if (isset($summary['mechanic_name'])) {
                $headerUpdate['mechanic_name'] = $summary['mechanic_name'] ?: null;
            }
            $this->update($id, $headerUpdate);

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
            // Use kontrak_id from existing audit record
            $kontrakId = $audit['kontrak_id'];

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
                'kontrak_id'             => $kontrakId,
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
                        // Add new unit to kontrak_unit (unit lebih - add to contract)
                        if (!empty($item['unit_id'])) {
                            $db->table('kontrak_unit')->insert([
                                'kontrak_id'           => $kontrakId,
                                'unit_id'              => $item['unit_id'],
                                'customer_location_id' => $audit['customer_location_id'],
                                'status'               => 'ACTIVE',
                                'is_spare'             => $item['actual_is_spare'] ?? 0,
                                'harga_sewa'           => $pricePerUnit,
                            ]);
                        }
                        break;

                    case 'ADD_UNIT':
                        // Unit kurang - add unit to kontrak at this location
                        if (!empty($item['unit_id'])) {
                            $db->table('kontrak_unit')->insert([
                                'kontrak_id'           => $kontrakId,
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

            // Nonaktifkan lokasi jika diminta
            if (!empty($pricing['deactivate_location'])) {
                $db->table('customer_locations')
                    ->where('id', $audit['customer_location_id'])
                    ->update(['is_active' => 0]);
            }

            $db->transComplete();

            return [
                'success' => true,
                'message' => 'Audit berhasil diapprove' . (!empty($pricing['deactivate_location']) ? ' & lokasi dinonaktifkan' : ''),
                'data'    => [
                    'unit_difference'        => $unitDifference,
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
     * Submit to marketing for approval (only when has_discrepancy)
     * If no discrepancy, mark as verified/completed without sending to Marketing
     */
    public function submitForApproval(int $id): array
    {
        $audit = $this->find($id);
        if (!$audit) {
            return ['success' => false, 'message' => 'Audit tidak ditemukan'];
        }
        if (($audit['has_discrepancy'] ?? 0) == 0) {
            $this->update($id, ['status' => 'APPROVED']);
            return ['success' => true, 'message' => 'Tidak ada selisih. Audit ditandai sebagai verifikasi selesai tanpa perlu approval Marketing.', 'no_approval' => true];
        }
        $this->update($id, ['status' => 'PENDING_APPROVAL']);
        return ['success' => true, 'message' => 'Audit dikirim ke Marketing untuk approval', 'no_approval' => false];
    }
}
