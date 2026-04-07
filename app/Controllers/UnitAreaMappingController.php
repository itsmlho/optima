<?php

namespace App\Controllers;

class UnitAreaMappingController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['auth', 'url']);
    }

    // -------------------------------------------------------------------------
    // Main Page
    // -------------------------------------------------------------------------

    public function index()
    {
        if (!$this->hasPermission('service.area_management.view')) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Summary stats
        $stats = [];

        $stats['total_areas'] = $this->db->table('areas')
            ->where('is_active', 1)->countAllResults();

        $stats['units_with_area'] = $this->db->table('inventory_unit')
            ->where('area_id IS NOT NULL')->countAllResults();

        $stats['units_without_area'] = $this->db->table('inventory_unit')
            ->where('area_id IS NULL')
            ->where('status_unit_id !=', 13) // exclude SOLD
            ->countAllResults();

        $stats['locations_without_area'] = $this->db->table('customer_locations')
            ->where('area_id IS NULL')
            ->where('is_active', 1)
            ->countAllResults();

        $stats['active_contract_units'] = $this->db->query("
            SELECT COUNT(DISTINCT ku.unit_id) as cnt
            FROM kontrak_unit ku
            JOIN kontrak k ON k.id = ku.kontrak_id
            WHERE k.status = 'ACTIVE' AND ku.status = 'ACTIVE'
        ")->getRowArray()['cnt'] ?? 0;

        // All active areas for dropdowns
        $areas = $this->db->table('areas')
            ->select('id, area_code, area_name, area_type')
            ->where('is_active', 1)
            ->orderBy('area_type')->orderBy('area_name')
            ->get()->getResultArray();

        $data = [
            'pageTitle' => 'Unit Area Mapping',
            'stats'     => $stats,
            'areas'     => $areas,
            'breadcrumbs' => [
                '/'                         => 'Dashboard',
                '/service/area-management'  => 'Area Management',
                ''                          => 'Unit Mapping',
            ],
        ];

        return view('service/unit_area_mapping', $data);
    }

    // -------------------------------------------------------------------------
    // AJAX: Area Summary (Tab 1)
    // -------------------------------------------------------------------------

    public function getAreaSummary()
    {
        $rows = $this->db->query("
            SELECT
                a.id,
                a.area_code,
                a.area_name,
                a.area_type,
                COUNT(DISTINCT iu.id_inventory_unit)                                        AS unit_count,
                COUNT(DISTINCT CASE WHEN e.staff_role LIKE '%FOREMAN%' THEN e.id END)       AS foreman_count,
                COUNT(DISTINCT CASE WHEN e.staff_role LIKE '%MECHANIC%' THEN e.id END)      AS mechanic_count,
                GROUP_CONCAT(DISTINCT CASE WHEN e.staff_role LIKE '%FOREMAN%'
                    THEN e.staff_name END SEPARATOR ', ')                                   AS foremans,
                GROUP_CONCAT(DISTINCT CASE WHEN e.staff_role LIKE '%MECHANIC%'
                    THEN e.staff_name END SEPARATOR ', ')                                   AS mechanics,
                COUNT(DISTINCT cl.id)                                                       AS location_count
            FROM areas a
            LEFT JOIN inventory_unit iu ON iu.area_id = a.id
            LEFT JOIN area_employee_assignments aea ON aea.area_id = a.id AND aea.is_active = 1
            LEFT JOIN employees e ON e.id = aea.employee_id AND e.is_active = 1
            LEFT JOIN customer_locations cl ON cl.area_id = a.id AND cl.is_active = 1
            WHERE a.is_active = 1
            GROUP BY a.id, a.area_code, a.area_name, a.area_type
            ORDER BY a.area_type, a.area_name
        ")->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    // -------------------------------------------------------------------------
    // AJAX: Units for a single area (Tab 1 drill-down)
    // -------------------------------------------------------------------------

    public function getAreaUnits()
    {
        $areaId = (int) $this->request->getPost('area_id');
        if (!$areaId) {
            return $this->response->setJSON(['success' => false, 'message' => 'area_id required']);
        }

        $rows = $this->db->query("
            SELECT
                iu.id_inventory_unit,
                iu.no_unit,
                CONCAT(mu.merk_unit, ' ', mu.model_unit) AS model,
                su.status_unit                           AS status,
                c.customer_name,
                cl.location_name,
                k.no_kontrak,
                k.tanggal_berakhir
            FROM inventory_unit iu
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
            LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status = 'ACTIVE'
            LEFT JOIN kontrak k ON k.id = ku.kontrak_id AND k.status = 'ACTIVE'
            LEFT JOIN customers c ON c.id = k.customer_id
            LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id
            WHERE iu.area_id = ?
            ORDER BY c.customer_name, iu.no_unit
        ", [$areaId])->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    // -------------------------------------------------------------------------
    // AJAX: Customer Locations list (Tab 2 - input area per lokasi)
    // -------------------------------------------------------------------------

    public function getCustomerLocations()
    {
        $filterArea = $this->request->getPost('area_filter'); // 'assigned','unassigned','all'

        $sql = "
            SELECT
                cl.id,
                cl.location_name,
                cl.location_code,
                cl.area_id,
                a.area_code,
                a.area_name,
                c.customer_name,
                COUNT(DISTINCT CASE WHEN ku.status = 'ACTIVE' THEN ku.unit_id END) AS active_units
            FROM customer_locations cl
            JOIN customers c ON c.id = cl.customer_id
            LEFT JOIN areas a ON a.id = cl.area_id
            LEFT JOIN kontrak_unit ku ON ku.customer_location_id = cl.id
            WHERE cl.is_active = 1
        ";

        if ($filterArea === 'assigned') {
            $sql .= ' AND cl.area_id IS NOT NULL';
        } elseif ($filterArea === 'unassigned') {
            $sql .= ' AND cl.area_id IS NULL';
        }

        $sql .= ' GROUP BY cl.id, cl.location_name, cl.location_code, cl.area_id, 
                            a.area_code, a.area_name, c.customer_name
                  ORDER BY c.customer_name, cl.location_name';

        $rows = $this->db->query($sql)->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    // -------------------------------------------------------------------------
    // POST: Assign area to a customer location + sync affected units
    // -------------------------------------------------------------------------

    public function assignAreaToLocation()
    {
        $locationId = (int) $this->request->getPost('location_id');
        $areaId     = $this->request->getPost('area_id'); // can be '' to remove

        if (!$locationId) {
            return $this->response->setJSON(['success' => false, 'message' => 'location_id required']);
        }

        $areaIdValue = $areaId !== '' ? (int) $areaId : null;

        try {
            $this->db->transStart();

            // 1. Update customer_locations
            $this->db->table('customer_locations')
                ->where('id', $locationId)
                ->update(['area_id' => $areaIdValue]);

            // 2. Sync inventory_unit.area_id for all ACTIVE units at this location
            if ($areaIdValue !== null) {
                $this->db->query("
                    UPDATE inventory_unit iu
                    JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status = 'ACTIVE'
                    SET iu.area_id = ?, iu.updated_at = NOW()
                    WHERE ku.customer_location_id = ?
                ", [$areaIdValue, $locationId]);
            }

            $this->db->transComplete();

            if (!$this->db->transStatus()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Transaksi gagal']);
            }

            // Count synced units
            $synced = $this->db->query("
                SELECT COUNT(DISTINCT ku.unit_id) as cnt
                FROM kontrak_unit ku
                WHERE ku.customer_location_id = ? AND ku.status = 'ACTIVE'
            ", [$locationId])->getRowArray()['cnt'] ?? 0;

            return $this->response->setJSON([
                'success'      => true,
                'message'      => "Area berhasil di-assign. {$synced} unit aktif telah ter-sync.",
                'synced_units' => (int) $synced,
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'assignAreaToLocation error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // POST: Bulk sync all units from their contract customer_location.area_id
    // -------------------------------------------------------------------------

    public function syncFromContracts()
    {
        try {
            $result = $this->db->query("
                UPDATE inventory_unit iu
                JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status = 'ACTIVE'
                JOIN customer_locations cl ON cl.id = ku.customer_location_id AND cl.area_id IS NOT NULL
                SET iu.area_id = cl.area_id, iu.updated_at = NOW()
                WHERE iu.area_id IS NULL OR iu.area_id != cl.area_id
            ");

            $affected = $this->db->affectedRows();

            return $this->response->setJSON([
                'success'  => true,
                'message'  => "{$affected} unit berhasil di-sync dari data kontrak.",
                'affected' => $affected,
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'syncFromContracts error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal sync: ' . $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // POST: Manually assign a single unit to an area
    // -------------------------------------------------------------------------

    public function manualAssignUnit()
    {
        $unitId = (int) $this->request->getPost('unit_id');
        $areaId = $this->request->getPost('area_id');

        if (!$unitId) {
            return $this->response->setJSON(['success' => false, 'message' => 'unit_id required']);
        }

        $areaIdValue = ($areaId !== '' && $areaId !== null) ? (int) $areaId : null;

        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $unitId)
            ->update(['area_id' => $areaIdValue, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Area unit berhasil diperbarui.',
        ]);
    }

    // -------------------------------------------------------------------------
    // AJAX: Unassigned units (Tab 3)
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // POST: Batch assign many locations to one area
    // -------------------------------------------------------------------------

    public function batchAssignLocations()
    {
        $locationIds = $this->request->getPost('location_ids');
        $areaId      = $this->request->getPost('area_id');

        if (!$locationIds || !$areaId) {
            return $this->response->setJSON(['success' => false, 'message' => 'location_ids dan area_id wajib diisi']);
        }

        if (is_string($locationIds)) {
            $locationIds = json_decode($locationIds, true);
        }

        $locationIds = array_values(array_filter(array_map('intval', (array) $locationIds)));
        $areaId      = (int) $areaId;

        if (empty($locationIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada lokasi yang dipilih']);
        }

        try {
            $this->db->transStart();

            $this->db->table('customer_locations')
                ->whereIn('id', $locationIds)
                ->update(['area_id' => $areaId]);

            $placeholders = implode(',', array_fill(0, count($locationIds), '?'));
            $this->db->query("
                UPDATE inventory_unit iu
                JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status = 'ACTIVE'
                SET iu.area_id = ?, iu.updated_at = NOW()
                WHERE ku.customer_location_id IN ({$placeholders})
            ", array_merge([$areaId], $locationIds));

            $this->db->transComplete();

            if (!$this->db->transStatus()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Transaksi gagal']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => count($locationIds) . ' lokasi berhasil diassign ke area.',
                'count'   => count($locationIds),
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'batchAssignLocations error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // POST: Batch assign many units to one area
    // -------------------------------------------------------------------------

    public function batchAssignUnits()
    {
        $unitIds = $this->request->getPost('unit_ids');
        $areaId  = $this->request->getPost('area_id');

        if (!$unitIds || !$areaId) {
            return $this->response->setJSON(['success' => false, 'message' => 'unit_ids dan area_id wajib diisi']);
        }

        if (is_string($unitIds)) {
            $unitIds = json_decode($unitIds, true);
        }

        $unitIds = array_values(array_filter(array_map('intval', (array) $unitIds)));
        $areaId  = (int) $areaId;

        if (empty($unitIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada unit yang dipilih']);
        }

        try {
            $this->db->table('inventory_unit')
                ->whereIn('id_inventory_unit', $unitIds)
                ->update(['area_id' => $areaId, 'updated_at' => date('Y-m-d H:i:s')]);

            $affected = $this->db->affectedRows();

            return $this->response->setJSON([
                'success'  => true,
                'message'  => $affected . ' unit berhasil di-assign ke area.',
                'affected' => $affected,
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'batchAssignUnits error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // AJAX: Unassigned units (Tab 3)
    // -------------------------------------------------------------------------

    public function getUnassignedUnits()
    {
        $rows = $this->db->query("
            SELECT
                iu.id_inventory_unit,
                iu.no_unit,
                CONCAT(mu.merk_unit, ' ', mu.model_unit) AS model,
                su.status_unit                           AS status,
                c.customer_name,
                cl.location_name,
                k.no_kontrak
            FROM inventory_unit iu
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
            LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status = 'ACTIVE'
            LEFT JOIN kontrak k ON k.id = ku.kontrak_id AND k.status = 'ACTIVE'
            LEFT JOIN customers c ON c.id = k.customer_id
            LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id
            WHERE iu.area_id IS NULL
              AND iu.status_unit_id != 13
            ORDER BY k.no_kontrak IS NULL, c.customer_name, iu.no_unit
            LIMIT 500
        ")->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }
}
