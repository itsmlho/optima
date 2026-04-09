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

        $stats['active_contract_units'] = $this->db->query("
            SELECT COUNT(DISTINCT ku.unit_id) as cnt
            FROM kontrak_unit ku
            JOIN kontrak k ON k.id = ku.kontrak_id
            WHERE k.status = 'ACTIVE' AND ku.status = 'ACTIVE'
        ")->getRowArray()['cnt'] ?? 0;

        // All active areas for dropdowns
        $areas = $this->db->table('areas')
            ->select('id, area_code, area_name, area_type, departemen_id')
            ->where('is_active', 1)
            ->orderBy('area_type')->orderBy('area_name')
            ->get()->getResultArray();

        // User dept scope for area dropdown filtering in frontend
        $userDeptScope = get_user_area_department_scope();

        $data = [
            'pageTitle' => 'Unit Area Mapping',
            'stats'     => $stats,
            'areas'     => $areas,
            'userDeptScope' => $userDeptScope,
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
            LEFT JOIN customer_locations cl ON cl.id IN (
                SELECT DISTINCT ku2.customer_location_id FROM kontrak_unit ku2
                JOIN inventory_unit iu2 ON iu2.id_inventory_unit = ku2.unit_id
                WHERE iu2.area_id = a.id AND ku2.status = 'ACTIVE'
            ) AND cl.is_active = 1
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
    // AJAX: Customer Locations list (Tab 2 - input area per unit dalam lokasi)
    // Returns each active location with its active-contract units and their
    // individual area_id.  Replaces the old single-area-per-location design.
    // -------------------------------------------------------------------------

    public function getCustomerLocations()
    {
        // Optional filters
        $filterDept   = $this->request->getPost('dept_filter');   // departemen_id int or ''
        $filterAssign = $this->request->getPost('area_filter');   // 'assigned','unassigned','all'

        $sql = "
            SELECT
                cl.id                       AS location_id,
                cl.location_name,
                cl.location_code,
                cl.contact_person,
                cl.phone,
                cl.city,
                c.customer_name,
                iu.id_inventory_unit,
                iu.no_unit,
                iu.fuel_type,
                iu.area_id,
                d.nama_departemen,
                a.area_code,
                a.area_name,
                CONCAT(mu.merk_unit, ' ', mu.model_unit) AS model,
                k.no_kontrak
            FROM customer_locations cl
            JOIN customers c ON c.id = cl.customer_id
            JOIN kontrak_unit ku
                ON ku.customer_location_id = cl.id AND ku.status = 'ACTIVE'
            JOIN kontrak k
                ON k.id = ku.kontrak_id AND k.status = 'ACTIVE'
            JOIN inventory_unit iu
                ON iu.id_inventory_unit = ku.unit_id
            LEFT JOIN model_unit mu
                ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN departemen d
                ON d.id_departemen = iu.departemen_id
            LEFT JOIN areas a
                ON a.id = iu.area_id
            WHERE cl.is_active = 1
        ";

        $params = [];

        if (!empty($filterDept) && is_numeric($filterDept)) {
            $sql   .= ' AND iu.departemen_id = ?';
            $params[] = (int) $filterDept;
        }

        if ($filterAssign === 'assigned') {
            $sql .= ' AND iu.area_id IS NOT NULL';
        } elseif ($filterAssign === 'unassigned') {
            $sql .= ' AND iu.area_id IS NULL';
        }

        $sql .= ' ORDER BY c.customer_name, cl.location_name, iu.no_unit';

        $rows = $this->db->query($sql, $params)->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    // -------------------------------------------------------------------------
    // POST: Assign area directly to a single unit (replaces assignAreaToLocation)
    // -------------------------------------------------------------------------

    public function assignUnitArea()
    {
        $unitId = (int) $this->request->getPost('unit_id');
        $areaId = $this->request->getPost('area_id'); // can be '' to clear

        if (!$unitId) {
            return $this->response->setJSON(['success' => false, 'message' => 'unit_id required']);
        }

        $areaIdValue = ($areaId !== '' && $areaId !== null) ? (int) $areaId : null;

        try {
            $this->db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update(['area_id' => $areaIdValue, 'updated_at' => date('Y-m-d H:i:s')]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Area unit berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'assignUnitArea error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
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
                k.no_kontrak,
                iu.fuel_type,
                d.nama_departemen
            FROM inventory_unit iu
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
            LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status = 'ACTIVE'
            LEFT JOIN kontrak k ON k.id = ku.kontrak_id AND k.status = 'ACTIVE'
            LEFT JOIN customers c ON c.id = k.customer_id
            LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id
            LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
            WHERE iu.area_id IS NULL
              AND iu.status_unit_id != 13
            ORDER BY k.no_kontrak IS NULL, c.customer_name, iu.no_unit
            LIMIT 500
        ")->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    // -------------------------------------------------------------------------
    // AJAX: Get units at a specific customer location (for preview modal)
    // -------------------------------------------------------------------------

    public function getUnitsAtLocation($locationId)
    {
        $locationId = (int) $locationId;
        if (!$locationId) {
            return $this->response->setJSON(['success' => false, 'message' => 'location_id invalid']);
        }

        $rows = $this->db->query("
            SELECT
                iu.id_inventory_unit,
                iu.no_unit,
                CONCAT(mu.merk_unit, ' ', mu.model_unit) AS model,
                su.status_unit                           AS status,
                iu.fuel_type,
                d.nama_departemen,
                a.area_code,
                a.area_name,
                k.no_kontrak,
                k.tanggal_berakhir
            FROM inventory_unit iu
            JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status = 'ACTIVE'
            JOIN kontrak k ON k.id = ku.kontrak_id AND k.status = 'ACTIVE'
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
            LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
            LEFT JOIN areas a ON a.id = iu.area_id
            WHERE ku.customer_location_id = ?
            ORDER BY iu.no_unit
        ", [$locationId])->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    // -------------------------------------------------------------------------
    // POST: Update PIC info for a customer location (service-side)
    // -------------------------------------------------------------------------

    public function updateLocationPic($id)
    {
        $id = (int) $id;
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID tidak valid']);
        }

        $rules = [
            'contact_person' => 'permit_empty|max_length[255]',
            'phone'          => 'permit_empty|max_length[20]',
            'email'          => 'permit_empty|valid_email|max_length[128]',
            'pic_position'   => 'permit_empty|max_length[100]',
            'address'        => 'permit_empty|max_length[500]',
            'city'           => 'permit_empty|max_length[100]',
            'province'       => 'permit_empty|max_length[100]',
            'postal_code'    => 'permit_empty|max_length[10]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        try {
            $this->db->table('customer_locations')
                ->where('id', $id)
                ->update([
                    'contact_person' => $this->request->getPost('contact_person') ?? '',
                    'phone'          => $this->request->getPost('phone') ?? '',
                    'email'          => $this->request->getPost('email') ?? '',
                    'pic_position'   => $this->request->getPost('pic_position') ?? '',
                    'address'        => $this->request->getPost('address') ?? '',
                    'city'           => $this->request->getPost('city') ?? '',
                    'province'       => $this->request->getPost('province') ?? '',
                    'postal_code'    => $this->request->getPost('postal_code') ?? '',
                ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Data lokasi berhasil diperbarui']);
        } catch (\Exception $e) {
            log_message('error', 'updateLocationPic error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data PIC']);
        }
    }
}
