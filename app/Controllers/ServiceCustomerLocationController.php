<?php

namespace App\Controllers;

use App\Models\CustomerLocationModel;
use App\Models\CustomerModel;
use App\Models\AreaModel;
use App\Traits\ActivityLoggingTrait;

/**
 * Service Customer Location Management Controller
 *
 * Allows service admin to:
 *  - View all customer locations with area, unit, and technician info
 *  - Request new locations (creates PENDING entry → marketing approves)
 *  - Edit existing locations directly (no approval required)
 */
class ServiceCustomerLocationController extends BaseController
{
    use ActivityLoggingTrait;

    protected $locationModel;
    protected $customerModel;
    protected $areaModel;
    protected $db;

    public function __construct()
    {
        $this->locationModel = new CustomerLocationModel();
        $this->customerModel = new CustomerModel();
        $this->areaModel     = new AreaModel();
        $this->db            = \Config\Database::connect();
        helper(['auth', 'simple_rbac']);
    }

    // ─────────────────────────────────────────────────────────────
    // PAGE ENTRY
    // ─────────────────────────────────────────────────────────────

    /**
     * Main page — Customer Location Management
     */
    public function index()
    {
        if (!$this->hasPermission('service.customer_location.view')) {
            return redirect()->to('/service')->with('error', 'Akses ditolak.');
        }

        // Stats for header cards
        $db = $this->db;

        // Total locations
        $totalLocations = $db->table('customer_locations')
            ->where('is_active', 1)
            ->countAllResults();

        // Pending approvals count
        $pendingCount = $db->table('customer_locations')
            ->where('approval_status', 'PENDING')
            ->where('is_active', 1)
            ->countAllResults();

        // Customers list for filter dropdown
        $customers = $db->table('customers')
            ->select('id, customer_name, customer_code')
            ->where('is_active', 1)
            ->orderBy('customer_name')
            ->get()->getResultArray();

        return view('service/customer_locations', [
            'title'          => 'Customer Location Management',
            'totalLocations' => $totalLocations,
            'pendingCount'   => $pendingCount,
            'customers'      => $customers,
            'departemen'     => $db->table('departemen')->select('id_departemen, nama_departemen')->orderBy('id_departemen', 'ASC')->get()->getResultArray(),
            'can_create'     => $this->hasPermission('service.customer_location.create'),
            'can_edit'       => $this->hasPermission('service.customer_location.edit'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // DATA ENDPOINT (DataTables server-side)
    // ─────────────────────────────────────────────────────────────

    /**
     * POST - DataTables AJAX data source
     */
    public function getData()
    {
        if (!$this->hasPermission('service.customer_location.view')) {
            return $this->response->setJSON(['error' => 'Akses ditolak.']);
        }

        $db = $this->db;

        // DataTables params
        $draw   = (int) ($this->request->getPost('draw') ?? 1);
        $start  = (int) ($this->request->getPost('start') ?? 0);
        $length = (int) ($this->request->getPost('length') ?? 25);
        $search = $this->request->getPost('search')['value'] ?? '';

        // Filters
        $filterCustomer  = $this->request->getPost('filter_customer');
        $filterStatus    = $this->request->getPost('filter_status');
        $filterLocType   = $this->request->getPost('filter_location_type');
        $filterDepartemen = $this->request->getPost('filter_departemen');

        // Base query
        $builder = $db->table('customer_locations cl')
            ->select('cl.id, cl.location_name, cl.location_code, cl.location_type,
                      cl.city, cl.province, cl.address, cl.contact_person, cl.phone,
                      cl.email, cl.pic_position, cl.notes,
                      cl.approval_status, cl.is_primary, cl.is_active,
                      cl.created_at,
                      c.customer_name, c.customer_code,
                      (SELECT COUNT(*) FROM kontrak_unit ku
                       JOIN kontrak k ON k.id = ku.kontrak_id
                       WHERE ku.customer_location_id = cl.id AND k.status = \'ACTIVE\'
                      ) as active_unit_count,
                      (SELECT GROUP_CONCAT(DISTINCT a2.area_name ORDER BY a2.area_name SEPARATOR \', \')
                       FROM kontrak_unit ku2
                       JOIN kontrak k2  ON k2.id  = ku2.kontrak_id
                       JOIN inventory_unit iu2 ON iu2.id_inventory_unit = ku2.unit_id
                       JOIN areas a2    ON a2.id  = iu2.area_id
                       WHERE ku2.customer_location_id = cl.id AND k2.status = \'ACTIVE\'
                         AND iu2.area_id IS NOT NULL
                      ) as areas_list')
            ->join('customers c', 'c.id = cl.customer_id', 'left')
            ->where('cl.is_active', 1);

        // Apply filters
        if ($filterCustomer) {
            $builder->where('cl.customer_id', (int)$filterCustomer);
        }
        if ($filterStatus) {
            if ($filterStatus === 'APPROVED_NULL') {
                // Locations without approval_status (old data — treated as approved)
                $builder->where('cl.approval_status IS NULL OR cl.approval_status = \'APPROVED\'', null, false);
            } else {
                $builder->where('cl.approval_status', $filterStatus);
            }
        }
        if ($filterLocType) {
            $builder->where('cl.location_type', $filterLocType);
        }
        if ($filterDepartemen) {
            $builder->where(
                "EXISTS (SELECT 1 FROM kontrak_unit ku3 JOIN inventory_unit iu3 ON iu3.id_inventory_unit = ku3.unit_id WHERE ku3.customer_location_id = cl.id AND iu3.departemen_id = " . (int) $filterDepartemen . ")",
                null, false
            );
        }

        // Global search
        if ($search !== '') {
            $builder->groupStart()
                ->like('cl.location_name', $search)
                ->orLike('cl.location_code', $search)
                ->orLike('c.customer_name', $search)
                ->orLike('c.customer_code', $search)
                ->orLike('cl.city', $search)
                ->orLike('cl.contact_person', $search)
            ->groupEnd();
        }

        // Total records (before pagination)
        $totalFiltered = $builder->countAllResults(false);

        // Paginate
        $builder->orderBy('c.customer_name', 'ASC')
                ->orderBy('cl.is_primary', 'DESC')
                ->orderBy('cl.location_name', 'ASC')
                ->limit($length, $start);

        $rows = $builder->get()->getResultArray();

        // Count total (unfiltered)
        $totalRecords = $db->table('customer_locations')
            ->where('is_active', 1)
            ->countAllResults();

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data'            => $rows,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // DETAIL (JSON)
    // ─────────────────────────────────────────────────────────────

    /**
     * GET - Return full location data as JSON (for detail/edit modal)
     */
    public function detail($id)
    {
        if (!$this->hasPermission('service.customer_location.view')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $location = $this->db->table('customer_locations cl')
            ->select('cl.*, c.customer_name, c.customer_code')
            ->join('customers c', 'c.id = cl.customer_id', 'left')
            ->where('cl.id', (int)$id)
            ->get()->getRowArray();

        if (!$location) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Lokasi tidak ditemukan.']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $location]);
    }

    // ─────────────────────────────────────────────────────────────
    // STORE (request new location → PENDING)
    // ─────────────────────────────────────────────────────────────

    /**
     * POST - Request a new customer location (creates PENDING entry)
     * No approval needed on service side; marketing approves via /marketing/audit-approval
     */
    public function store()
    {
        if (!$this->hasPermission('service.customer_location.create')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak.',
                csrf_token() => csrf_hash(),
            ]);
        }

        $rules = [
            'customer_id'   => 'required|integer',
            'location_name' => 'required|max_length[100]',
            'address'       => 'required',
            'city'          => 'required|max_length[100]',
            'province'      => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $this->validator->getErrors(),
                csrf_token() => csrf_hash(),
            ]);
        }

        $customerId   = (int)$this->request->getPost('customer_id');
        $locationName = trim($this->request->getPost('location_name'));
        $locationType = $this->request->getPost('location_type') ?: 'BRANCH';
        $address      = trim($this->request->getPost('address'));
        $city         = trim($this->request->getPost('city'));
        $province     = trim($this->request->getPost('province'));
        $userId       = session()->get('user_id') ?? null;

        // Auto-generate location code
        $locationCode = 'LOC-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        $data = [
            'customer_id'     => $customerId,
            'location_name'   => $locationName,
            'location_code'   => $locationCode,
            'location_type'   => $locationType,
            'address'         => $address,
            'city'            => $city,
            'province'        => $province,
            'postal_code'     => $this->request->getPost('postal_code') ?: null,
            'contact_person'  => $this->request->getPost('contact_person') ?: null,
            'phone'           => $this->request->getPost('phone') ?: null,
            'email'           => $this->request->getPost('email') ?: null,
            'pic_position'    => $this->request->getPost('pic_position') ?: null,
            'notes'           => $this->request->getPost('notes') ?: null,
            'is_primary'      => 0,
            'is_active'       => 1,
            'approval_status' => 'PENDING',
            'requested_by'    => $userId,
        ];

        try {
            $newId = $this->locationModel->insert($data);

            if (!$newId) {
                throw new \RuntimeException('Insert gagal.');
            }

            $this->logActivity('SERVICE_LOCATION_REQUEST', 'service', $newId,
                "Permintaan tambah lokasi: {$locationName} (Customer #{$customerId})");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permintaan lokasi berhasil dikirim. Menunggu persetujuan tim Marketing.',
                'id'      => $newId,
                csrf_token() => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            log_message('error', '[ServiceCustomerLocation::store] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                csrf_token() => csrf_hash(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE (direct, no approval)
    // ─────────────────────────────────────────────────────────────

    /**
     * POST - Update existing location directly (no approval required)
     */
    public function update($id)
    {
        if (!$this->hasPermission('service.customer_location.edit')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak.',
                csrf_token() => csrf_hash(),
            ]);
        }

        $location = $this->locationModel->find((int)$id);
        if (!$location) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lokasi tidak ditemukan.',
                csrf_token() => csrf_hash(),
            ]);
        }

        $rules = [
            'location_name' => 'required|max_length[100]',
            'address'       => 'required',
            'city'          => 'required|max_length[100]',
            'province'      => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $this->validator->getErrors(),
                csrf_token() => csrf_hash(),
            ]);
        }

        $data = [
            'location_name'  => trim($this->request->getPost('location_name')),
            'location_type'  => $this->request->getPost('location_type') ?: $location['location_type'],
            'address'        => trim($this->request->getPost('address')),
            'city'           => trim($this->request->getPost('city')),
            'province'       => trim($this->request->getPost('province')),
            'postal_code'    => $this->request->getPost('postal_code') ?: null,
            'contact_person' => $this->request->getPost('contact_person') ?: null,
            'phone'          => $this->request->getPost('phone') ?: null,
            'email'          => $this->request->getPost('email') ?: null,
            'pic_position'   => $this->request->getPost('pic_position') ?: null,
            'notes'          => $this->request->getPost('notes') ?: null,
        ];

        try {
            $this->locationModel->update((int)$id, $data);

            $this->logActivity('SERVICE_LOCATION_UPDATED', 'service', (int)$id,
                "Lokasi diperbarui: {$data['location_name']} (ID #{$id})");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data lokasi berhasil diperbarui.',
                csrf_token() => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            log_message('error', '[ServiceCustomerLocation::update] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                csrf_token() => csrf_hash(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // LOCATION UNITS (active units at this location)
    // ─────────────────────────────────────────────────────────────

    /**
     * GET - Active units currently deployed at the given location
     */
    public function getLocationUnits($id)
    {
        if (!$this->hasPermission('service.customer_location.view')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false]);
        }

        $units = $this->db->query("
            SELECT
                iu.id_inventory_unit,
                iu.no_unit_na        AS no_pol,
                iu.serial_number,
                mu.merk_unit         AS merk,
                mu.model_unit        AS model,
                tu.jenis             AS jenis,
                su.status_unit       AS status_name,
                a.area_name,
                a.area_code,
                d.nama_departemen    AS departemen
            FROM kontrak_unit ku
            JOIN kontrak k                ON k.id  = ku.kontrak_id
            JOIN inventory_unit iu        ON iu.id_inventory_unit = ku.unit_id
            LEFT JOIN model_unit mu       ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN tipe_unit tu        ON tu.id_tipe_unit = iu.tipe_unit_id
            LEFT JOIN status_unit su      ON su.id_status = iu.status_unit_id
            LEFT JOIN areas a             ON a.id = iu.area_id
            LEFT JOIN departemen d        ON d.id_departemen = iu.departemen_id
            WHERE ku.customer_location_id = ?
              AND k.status = 'ACTIVE'
            ORDER BY iu.no_unit_na
        ", [(int)$id])->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $units]);
    }

    // ─────────────────────────────────────────────────────────────
    // LOCATION EMPLOYEES (technicians assigned to the location's area)
    // ─────────────────────────────────────────────────────────────

    /**
     * GET - Employees assigned to the area of this location
     */
    public function getLocationEmployees($id)
    {
        if (!$this->hasPermission('service.customer_location.view')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false]);
        }

        // Get areas from units currently deployed at this location
        // (area is per-unit on inventory_unit.area_id, not on customer_location)
        $areaIds = $this->db->query("
            SELECT DISTINCT iu.area_id
            FROM kontrak_unit ku
            JOIN kontrak k         ON k.id  = ku.kontrak_id AND k.status = 'ACTIVE'
            JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
            WHERE ku.customer_location_id = ?
              AND iu.area_id IS NOT NULL
        ", [(int)$id])->getResultArray();

        if (empty($areaIds)) {
            return $this->response->setJSON([
                'success' => true,
                'data'    => [],
                'message' => 'Belum ada unit aktif dengan area yang terdaftar di lokasi ini.',
            ]);
        }

        $areaIdList = array_column($areaIds, 'area_id');

        $employees = $this->db->query("
            SELECT
                e.id,
                e.staff_name         AS nama,
                e.staff_code,
                e.staff_role         AS role,
                e.phone              AS telepon,
                aea.assignment_type,
                aea.department_scope,
                aea.start_date,
                a.area_name,
                a.area_code
            FROM area_employee_assignments aea
            JOIN employees e  ON e.id  = aea.employee_id
            JOIN areas a      ON a.id  = aea.area_id
            WHERE aea.area_id IN (" . implode(',', array_map('intval', $areaIdList)) . ")
              AND aea.is_active = 1
              AND e.is_active   = 1
            ORDER BY a.area_name ASC, aea.assignment_type ASC, e.staff_name ASC
        ")->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $employees]);
    }
}
