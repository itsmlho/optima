<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UnitAuditRequestModel;
use App\Models\InventoryUnitModel;
use App\Models\AuditLocationModel;
use App\Models\InventoryComponentHelper;

class UnitAudit extends BaseController
{
    protected $auditModel;
    protected $unitModel;
    protected $auditLocationModel;

    public function __construct()
    {
        $this->auditModel = new UnitAuditRequestModel();
        $this->unitModel  = new InventoryUnitModel();
        $this->auditLocationModel = new AuditLocationModel();
    }

    // ── Pages ────────────────────────────────────────────

    /**
     * Admin audit page (Service)
     */
    public function index()
    {
        $data['title'] = 'Unit Audit';
        $data['stats'] = $this->auditModel->getStats();
        return view('service/unit_audit', $data);
    }

    // ── AJAX Endpoints ──────────────────────────────────

    /**
     * Get customers that have active contracts with units
     */
    public function getCustomersWithUnits()
    {
        try {
            $customers = $this->auditModel->getCustomersWithUnits();
            return $this->response->setJSON(['success' => true, 'data' => $customers]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get customers for Unit Audit page with location counts and audit badge (Select2 + badges).
     */
    public function getCustomersForUnitAudit()
    {
        try {
            $customers = $this->auditLocationModel->getCustomersWithLocationAuditSummary();
            return $this->response->setJSON(['success' => true, 'data' => $customers]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get units linked to a customer via kontrak_unit
     */
    public function getCustomerUnits($customerId)
    {
        try {
            $units = $this->auditModel->getUnitsForCustomer((int) $customerId);
            return $this->response->setJSON(['success' => true, 'data' => $units]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Create a new audit request (change proposal)
     */
    public function createAuditRequest()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $requestType = $this->request->getPost('request_type');
        $customerId  = $this->request->getPost('customer_id');
        $kontrakId   = $this->request->getPost('kontrak_id');
        $unitId      = $this->request->getPost('unit_id');
        $notes       = $this->request->getPost('notes');

        // Build current_data snapshot
        $currentData = [];
        if ($unitId) {
            $unit = $this->unitModel->find($unitId);
            if ($unit) {
                $currentData = [
                    'no_unit'    => $unit['no_unit'] ?? $unit['no_unit_na'] ?? null,
                    'serial'     => $unit['serial_number'] ?? null,
                    'lokasi'     => $unit['lokasi_unit'] ?? null,
                    'status_id'  => $unit['status_unit_id'] ?? null,
                ];
            }
        }

        // Build proposed_data from form
        $proposedData = [];
        switch ($requestType) {
            case 'LOCATION_MISMATCH':
                $proposedData['new_location'] = $this->request->getPost('proposed_location');
                break;
            case 'UNIT_SWAP':
                $proposedData['new_unit_id']  = $this->request->getPost('proposed_unit_id');
                $proposedData['harga_sewa']   = $this->request->getPost('proposed_harga_sewa');
                break;
            case 'ADD_UNIT':
                $proposedData['unit_id']            = $this->request->getPost('proposed_unit_id');
                $proposedData['is_spare']           = $this->request->getPost('proposed_is_spare') ? 1 : 0;
                $proposedData['harga_sewa']         = $this->request->getPost('proposed_harga_sewa');
                $proposedData['customer_location_id'] = $this->request->getPost('customer_location_id') ?: null;
                break;
            case 'MARK_SPARE':
                $proposedData['is_spare'] = 1;
                break;
            case 'UNIT_MISSING':
                $proposedData['last_known_location'] = $this->request->getPost('proposed_location');
                break;
            case 'OTHER':
                $proposedData['new_location'] = $this->request->getPost('proposed_location');
                $proposedData['description']  = $this->request->getPost('proposed_description');
                break;
        }

        try {
            $data = [
                'audit_number'  => $this->auditModel->generateAuditNumber(),
                'customer_id'   => $customerId,
                'kontrak_id'    => $kontrakId ?: null,
                'unit_id'       => $unitId ?: null,
                'request_type'  => $requestType,
                'current_data'  => json_encode($currentData),
                'proposed_data' => json_encode($proposedData),
                'notes'         => $notes,
                'status'        => 'SUBMITTED',
                'submitted_by'  => $userId,
            ];

            $result = $this->auditModel->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Audit request berhasil disubmit!',
                'data'    => ['id' => $result, 'audit_number' => $data['audit_number']],
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get audit requests list
     */
    public function getAuditRequests()
    {
        $filters = [];
        if ($status = $this->request->getGet('status')) {
            $filters['status'] = $status;
        }
        if ($customerId = $this->request->getGet('customer_id')) {
            $filters['customer_id'] = $customerId;
        }
        if ($requestType = $this->request->getGet('request_type')) {
            $filters['request_type'] = $requestType;
        }

        try {
            $data = $this->auditModel->getWithDetails($filters);
            return $this->response->setJSON(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get single request detail
     */
    public function getAuditDetail($id)
    {
        try {
            $data = $this->auditModel->getWithDetails();
            $item = null;
            foreach ($data as $row) {
                if ((int) $row['id'] === (int) $id) {
                    $item = $row;
                    break;
                }
            }
            if (!$item) {
                return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
            }
            return $this->response->setJSON(['success' => true, 'data' => $item]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get available units for dropdown (not yet in any contract)
     */
    public function getAvailableUnits()
    {
        try {
            $units = $this->unitModel->getUnitsForDropdown();
            return $this->response->setJSON(['success' => true, 'data' => $units]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Select2 AJAX: cari unit tanpa memuat seluruh fleet (Unit Audit — Tambah Unit / Verifikasi Unit Berbeda).
     * GET: q (min 1 char, kecuali id), purpose = add_location | unit_swap, id = preload satu baris.
     */
    public function searchInventoryUnits()
    {
        try {
            helper('auth');
            $scope   = get_user_area_department_scope();
            $deptIds = null;
            if (is_array($scope) && ! empty($scope['departments'])) {
                $deptIds = array_map('intval', $scope['departments']);
            }

            $q       = trim((string) $this->request->getGet('q'));
            $purpose = (string) $this->request->getGet('purpose');
            if (! in_array($purpose, ['add_location', 'unit_swap'], true)) {
                $purpose = 'unit_swap';
            }

            $onlyId = $this->request->getGet('id');
            $onlyId = ($onlyId !== null && $onlyId !== '') ? (int) $onlyId : null;

            $units = $this->unitModel->searchForUnitAuditPicker($q, $purpose, $onlyId, $deptIds, 35);

            return $this->response->setJSON(['success' => true, 'data' => $units]);
        } catch (\Throwable $e) {
            log_message('error', 'UnitAudit searchInventoryUnits: {msg}', ['msg' => $e->getMessage()]);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mencari unit',
                'data'    => [],
            ]);
        }
    }

    /**
     * Get active customers
     */
    public function getCustomers()
    {
        try {
            $customers = (new \App\Models\CustomerModel())->getActiveCustomers();
            return $this->response->setJSON(['success' => true, 'data' => $customers]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    // ── Marketing Approval (Pengajuan Unit) ──────────────────────────────

    /**
     * Approve request (Marketing)
     */
    public function approveRequest($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $notes  = $this->request->getPost('notes');
        $result = $this->auditModel->approveAndApply((int) $id, $userId, $notes);

        return $this->response->setJSON($result);
    }

    /**
     * Reject request (Marketing)
     */
    public function rejectRequest($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $notes  = $this->request->getPost('notes');
        $result = $this->auditModel->rejectRequest((int) $id, $userId, $notes);

        return $this->response->setJSON($result);
    }

    // ═══════════════════════════════════════════════════════
    // LOCATION-BASED AUDIT (SERVICE SIDE)
    // ═══════════════════════════════════════════════════════

    /**
     * Unit Verification index page (Service) - satu halaman untuk verifikasi unit di lokasi customer
     */
    public function verificationIndex()
    {
        $data['title'] = 'Unit Verification';
        $data['stats'] = $this->auditLocationModel->getStats();
        return view('service/unit_verification', $data);
    }

    /**
     * Redirect: Audit per Lokasi digabung ke Unit Verification (bookmark/link lama tetap jalan)
     */
    public function redirectToVerification()
    {
        return redirect()->to('/service/unit-verification');
    }

    /**
     * Get customers with active locations for audit
     */
    public function getCustomersWithLocations()
    {
        try {
            $customers = $this->auditLocationModel->getCustomersWithLocations();
            return $this->response->setJSON(['success' => true, 'data' => $customers]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get locations for a customer
     */
    public function getLocationsForCustomer($customerId)
    {
        try {
            $locations = $this->auditLocationModel->getLocationsForCustomer((int) $customerId);
            return $this->response->setJSON(['success' => true, 'data' => $locations]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get all areas for dropdown (with departemen for DIESEL/ELECTRIC badge)
     */
    public function getAreas()
    {
        try {
            $db = \Config\Database::connect();
            $areas = $db->table('areas a')
                ->select('a.id, a.area_name, a.area_code, a.departemen_id, d.nama_departemen')
                ->join('departemen d', 'd.id_departemen = a.departemen_id', 'left')
                ->where('a.is_active', 1)
                ->orderBy('d.nama_departemen', 'ASC')
                ->orderBy('a.area_name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON(['success' => true, 'data' => $areas]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Request to add a new customer location (Service submits, Marketing approves)
     */
    public function requestAddLocation()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $customerId = $this->request->getPost('customer_id');
        $locationName = trim($this->request->getPost('location_name') ?? '');
        $areaId = $this->request->getPost('area_id');
        $address = trim($this->request->getPost('address') ?? '');
        $city = trim($this->request->getPost('city') ?? '');
        $province = trim($this->request->getPost('province') ?? '');
        $postalCode = trim($this->request->getPost('postal_code') ?? '');
        $contactPerson = trim($this->request->getPost('contact_person') ?? '');
        $phone = trim($this->request->getPost('phone') ?? '');
        $notes = trim($this->request->getPost('notes') ?? '');

        // Validation
        if (empty($customerId) || empty($locationName) || empty($areaId) || empty($address) || empty($city) || empty($province)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data lokasi tidak lengkap']);
        }

        try {
            $db = \Config\Database::connect();

            // Check for duplicate location name for this customer
            $existing = $db->table('customer_locations')
                ->where('customer_id', $customerId)
                ->where('location_name', $locationName)
                ->where('is_active', 1)
                ->countAllResults();

            if ($existing > 0) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lokasi dengan nama ini sudah ada']);
            }

            // Generate location code
            $now = new \DateTime();
            $locationCode = 'LOC-' . $now->format('Ymd') . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

            // Insert with approval_status = PENDING
            $data = [
                'customer_id'      => $customerId,
                'area_id'          => $areaId,
                'location_name'    => $locationName,
                'location_code'    => $locationCode,
                'location_type'    => 'MILL',
                'address'          => $address,
                'city'             => $city,
                'province'         => $province,
                'postal_code'      => $postalCode,
                'contact_person'   => $contactPerson,
                'phone'            => $phone,
                'notes'            => $notes,
                'is_primary'       => 0,
                'is_active'        => 1,
                'approval_status'  => 'PENDING',
                'requested_by'     => $userId,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            $db->table('customer_locations')->insert($data);
            $locationId = $db->insertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Request lokasi baru dikirim ke Marketing untuk approval',
                'data'    => ['id' => $locationId, 'location_code' => $locationCode],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'UnitAudit::requestAddLocation - ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan request lokasi']);
        }
    }

    /**
     * Get units at a specific location
     */
    public function getLocationUnits($locationId)
    {
        try {
            $units = $this->auditLocationModel->getUnitsForLocation((int) $locationId);
            return $this->response->setJSON(['success' => true, 'data' => $units]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get location details
     */
    public function getLocationDetails($locationId)
    {
        try {
            $location = $this->auditLocationModel->getLocationDetails((int) $locationId);
            if (!$location) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lokasi tidak ditemukan']);
            }
            return $this->response->setJSON(['success' => true, 'data' => $location]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Create new location audit
     */
    public function createLocationAudit()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $customerId = $this->request->getPost('customer_id');
        $locationId = $this->request->getPost('customer_location_id');
        $auditDate = $this->request->getPost('audit_date') ?: date('Y-m-d');

        if (!$customerId || !$locationId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer dan Location harus dipilih']);
        }

        try {
            $auditId = $this->auditLocationModel->createAuditLocation([
                'customer_id'          => $customerId,
                'customer_location_id' => $locationId,
                'audit_date'           => $auditDate,
                'submitted_by'         => $userId,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Audit lokasi berhasil dibuat',
                'data'    => ['id' => $auditId],
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get list of location audits
     */
    public function getLocationAudits()
    {
        $filters = [];
        if ($status = $this->request->getGet('status')) {
            $filters['status'] = $status;
        }
        if ($customerId = $this->request->getGet('customer_id')) {
            $filters['customer_id'] = $customerId;
        }

        try {
            $audits = $this->auditLocationModel->getAllAudits($filters);
            return $this->response->setJSON(['success' => true, 'data' => $audits]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get data grouped by customer then location (same as Contract > By Customer) for Unit Verification page
     * Uses all locations from contracts (not filtered by audit existence)
     */
    public function getVerificationGrouped()
    {
        try {
            $customers = $this->auditLocationModel->getVerificationGrouped();
            return $this->response->setJSON(['success' => true, 'data' => $customers]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get grouped data for Unit Verification: only from existing unit_audit_locations records
     */
    public function getVerificationGroupedFromAudits()
    {
        try {
            $customers = $this->auditLocationModel->getVerificationGroupedFromAudits();
            return $this->response->setJSON(['success' => true, 'data' => $customers]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get locations with latest audit status for a customer (Unit Audit page badge display)
     */
    public function getLocationsWithAuditStatus($customerId)
    {
        try {
            $locations = $this->auditLocationModel->getLocationsForCustomer((int) $customerId);

            // Be defensive for mixed deploy states (controller updated, model may lag behind in prod).
            // If status method is unavailable or fails, keep returning locations instead of HTTP 500.
            $statusMap = [];
            if (method_exists($this->auditLocationModel, 'getLocationAuditStatusForCustomer')) {
                try {
                    $statusMap = $this->auditLocationModel->getLocationAuditStatusForCustomer((int) $customerId);
                } catch (\Throwable $e) {
                    log_message('error', 'UnitAudit getLocationAuditStatusForCustomer failed: {msg}', ['msg' => $e->getMessage()]);
                    $statusMap = [];
                }
            } else {
                log_message('warning', 'UnitAudit model method getLocationAuditStatusForCustomer not found; returning locations without audit badges');
            }

            foreach ($locations as &$loc) {
                $locId = (int) $loc['id'];
                $loc['audit_status'] = $statusMap[$locId] ?? null;
            }
            return $this->response->setJSON(['success' => true, 'data' => $locations]);
        } catch (\Throwable $e) {
            log_message('error', 'UnitAudit getLocationsWithAuditStatus failed: {msg}', ['msg' => $e->getMessage()]);

            // Fallback for production schema drift: return basic active locations only.
            try {
                $fallbackLocations = \Config\Database::connect()
                    ->table('customer_locations')
                    ->select('id, location_name, address')
                    ->where('customer_id', (int) $customerId)
                    ->where('is_active', 1)
                    ->orderBy('location_name', 'ASC')
                    ->get()
                    ->getResultArray();

                if (is_array($fallbackLocations)) {
                    $locIds = array_values(array_filter(array_column($fallbackLocations, 'id')));
                    $countMap = [];
                    if ($locIds !== []) {
                        $countRows = \Config\Database::connect()
                            ->table('kontrak_unit')
                            ->select('customer_location_id, COUNT(*) AS cnt')
                            ->whereIn('customer_location_id', $locIds)
                            ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE', 'Aktif'])
                            ->where('(is_temporary IS NULL OR is_temporary = 0)', null, false)
                            ->groupBy('customer_location_id')
                            ->get()
                            ->getResultArray();
                        foreach ($countRows as $cr) {
                            $countMap[(int) $cr['customer_location_id']] = (int) $cr['cnt'];
                        }
                    }
                    foreach ($fallbackLocations as &$loc) {
                        $loc['audit_status'] = null;
                        $lid = isset($loc['id']) ? (int) $loc['id'] : 0;
                        $loc['total_units'] = $countMap[$lid] ?? 0;
                        $loc['spare_units'] = 0;
                        $loc['no_kontrak_masked'] = '-';
                        $loc['no_po_masked'] = '-';
                        $loc['periode_text'] = '-';
                        $loc['periode_badge_text'] = '-';
                        $loc['periode_status_text'] = '—';
                        $loc['last_audit'] = null;
                        $loc['due_for_reaudit'] = true;
                        $loc['is_pending_approval'] = false;
                    }
                    unset($loc);
                    return $this->response->setJSON([
                        'success' => true,
                        'data' => $fallbackLocations,
                        'fallback' => true
                    ]);
                }
            } catch (\Throwable $fallbackError) {
                log_message('error', 'UnitAudit fallback locations also failed: {msg}', ['msg' => $fallbackError->getMessage()]);
            }

            return $this->response
                ->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Create audit verification from Unit Audit page Verifikasi modal.
     * Captures field_status (sesuai/tidak_sesuai), reasons, keterangan; sets status PENDING_APPROVAL.
     */
    public function createAuditVerification()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $customerId   = $this->request->getPost('customer_id');
        $locationId   = $this->request->getPost('customer_location_id');
        $auditDate    = $this->request->getPost('audit_date') ?: date('Y-m-d');
        $mechanicName = $this->request->getPost('mechanic_name') ? trim($this->request->getPost('mechanic_name')) : '';
        $fieldStatus  = $this->request->getPost('field_status') ?: 'sesuai';
        $auditSummary = $this->request->getPost('audit_summary') ?: '';
        $items        = $this->request->getPost('items') ?: [];

        if (!$customerId || !$locationId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer dan Lokasi wajib diisi']);
        }
        if ($mechanicName === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Mekanik yang audit wajib diisi']);
        }

        $hasDiscrepancy = ($fieldStatus === 'tidak_sesuai') ? 1 : 0;
        $mechanicNotes  = json_encode([
            'field_status' => $fieldStatus,
            'items_count'  => is_array($items) ? count($items) : 0,
        ], JSON_UNESCAPED_UNICODE);

        try {
            $auditId = $this->auditLocationModel->createAuditLocation([
                'customer_id'          => $customerId,
                'customer_location_id' => $locationId,
                'audit_date'           => $auditDate,
                'submitted_by'         => $userId,
                'mechanic_name'        => $mechanicName,
                'status'               => 'PENDING_APPROVAL',
                'has_discrepancy'      => $hasDiscrepancy,
                'mechanic_notes'       => $mechanicNotes,
                'service_notes'        => $auditSummary,
            ]);

            if (!empty($items) && is_array($items)) {
                $this->auditLocationModel->updateAuditLocationItemResults((int) $auditId, $items);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Verifikasi berhasil dibuat dan dikirim ke Marketing untuk approval',
                'data'    => ['id' => $auditId],
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Print location form for mechanic (no existing audit record needed).
     * Shows: customer, location, unit list with fill-in fields (kontrak saja).
     */
    public function printLocationForm($customerId, $locationId)
    {
        try {
            $location = $this->auditLocationModel->getLocationDetails((int) $locationId);
            if (!$location) {
                return redirect()->to('/service/unit-audit')->with('error', 'Lokasi tidak ditemukan');
            }
            $units = $this->auditLocationModel->getUnitsForLocation((int) $locationId);

            $kontrakInfo = \Config\Database::connect()->table('kontrak_unit ku')
                ->select('k.no_kontrak, k.customer_po_number, k.tanggal_mulai, k.tanggal_berakhir')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->where('ku.customer_location_id', $locationId)
                ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->get()
                ->getRowArray();

            $periodeStatus = $this->auditLocationModel->getPeriodeStatusText($kontrakInfo['tanggal_berakhir'] ?? null);
            $noPoMasked    = $this->auditLocationModel->maskPoNumberForView($kontrakInfo['customer_po_number'] ?? null);
            $noKontrakMasked = $this->auditLocationModel->maskContractNumberForView($kontrakInfo['no_kontrak'] ?? null);

            return view('service/print_location_form', [
                'location'         => $location,
                'units'            => $units,
                'kontrak_info'     => $kontrakInfo,
                'no_kontrak_masked'=> $noKontrakMasked,
                'no_po_masked'     => $noPoMasked,
                'periode_status'   => $periodeStatus,
                'print_date'       => date('d-m-Y'),
            ]);
        } catch (\Exception $e) {
            return redirect()->to('/service/unit-audit')->with('error', $e->getMessage());
        }
    }

    /**
     * Get location audit detail
     */
    public function getLocationAuditDetail($id)
    {
        try {
            $audit = $this->auditLocationModel->getWithDetails((int) $id);
            if (!$audit) {
                return $this->response->setJSON(['success' => false, 'message' => 'Audit tidak ditemukan']);
            }
            return $this->response->setJSON(['success' => true, 'data' => $audit]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Print audit form (legacy format)
     */
    public function printLocationAudit($id)
    {
        try {
            $audit = $this->auditLocationModel->getWithDetails((int) $id);
            if (!$audit) {
                return redirect()->to('/service/unit-verification')->with('error', 'Audit tidak ditemukan');
            }
            return view('service/print_audit_form', ['audit' => $audit]);
        } catch (\Exception $e) {
            return redirect()->to('/service/unit-verification')->with('error', $e->getMessage());
        }
    }

    /**
     * Print verification form (Item / Database / Real Lapangan / Sesuai format)
     */
    public function printVerificationLocation($id)
    {
        try {
            $audit = $this->auditLocationModel->getWithDetails((int) $id);
            if (!$audit) {
                return redirect()->to('/service/unit-verification')->with('error', 'Audit tidak ditemukan');
            }
            $audit['no_kontrak_masked'] = $this->auditLocationModel->maskContractNumberForView($audit['no_kontrak'] ?? null);
            return view('service/print_verification_location', ['audit' => $audit]);
        } catch (\Exception $e) {
            return redirect()->to('/service/unit-verification')->with('error', $e->getMessage());
        }
    }

    /**
     * Mark audit as printed
     */
    public function markAuditPrinted($id)
    {
        try {
            $this->auditLocationModel->markAsPrinted((int) $id);
            return $this->response->setJSON(['success' => true, 'message' => 'Status diubah menjadi Printed']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Mark audit as in progress (mechanic started)
     */
    public function markAuditInProgress($id)
    {
        try {
            $this->auditLocationModel->markAsInProgress((int) $id);
            return $this->response->setJSON(['success' => true, 'message' => 'Status diubah menjadi In Progress']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Submit audit results (input from mechanic)
     */
    public function submitAuditResults()
    {
        $auditId = $this->request->getPost('audit_id');
        $items = $this->request->getPost('items') ?: [];
        $summary = $this->request->getPost('summary') ?: [];

        if (!$auditId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Audit ID diperlukan']);
        }

        try {
            $this->auditLocationModel->submitAuditResults((int) $auditId, $items, $summary);
            return $this->response->setJSON(['success' => true, 'message' => 'Hasil audit berhasil disimpan']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Submit to marketing for approval (only when has_discrepancy)
     */
    public function submitToMarketing($id)
    {
        try {
            $result = $this->auditLocationModel->submitForApproval((int) $id);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Input audit results page
     */
    public function inputAuditResults($id)
    {
        try {
            $audit = $this->auditLocationModel->getWithDetails((int) $id);
            if (!$audit) {
                return redirect()->to('/service/unit-verification')->with('error', 'Audit tidak ditemukan');
            }
            $audit['no_kontrak_masked'] = $this->auditLocationModel->maskContractNumberForView($audit['no_kontrak'] ?? null);
            $data['title'] = 'Input Hasil Audit';
            $data['audit'] = $audit;
            return view('service/unit_audit_result_input', $data);
        } catch (\Exception $e) {
            return redirect()->to('/service/unit-verification')->with('error', $e->getMessage());
        }
    }

    /**
     * Per-unit verification view for admins (detail verifikasi 1 unit).
     * URL: /service/unit-verification/unit/{auditId}/{index}
     */
    public function verifyUnit(int $auditId, int $index)
    {
        try {
            $audit = $this->auditLocationModel->getWithDetails($auditId);
            if (!$audit) {
                return redirect()->to('/service/unit-verification')->with('error', 'Audit tidak ditemukan');
            }

            $items = $audit['items'] ?? [];
            if (empty($items)) {
                return redirect()->to('/service/unit-audit/inputResults/' . $auditId)
                    ->with('error', 'Belum ada detail unit untuk audit ini');
            }

            $totalUnits = count($items);
            if ($index < 1) $index = 1;
            if ($index > $totalUnits) $index = $totalUnits;

            $currentItem = $items[$index - 1];

            // Load unit components (attachment, battery, charger) for this unit if available
            $components = [
                'battery'    => null,
                'charger'    => null,
                'attachment' => null,
            ];
            $unitDetail = null;
            if (!empty($currentItem['unit_id'])) {
                $helper = new InventoryComponentHelper();
                $components = $helper->getUnitComponents((int) $currentItem['unit_id']);

                // Fetch full unit details (tahun, departemen, tipe, kapasitas, mesin, mast, aksesoris, etc.)
                $db = \Config\Database::connect();
                $unitDetail = $db->query("
                    SELECT
                        iu.id_inventory_unit, iu.no_unit, iu.serial_number,
                        iu.tahun_unit, iu.keterangan, iu.tinggi_mast, iu.sn_mast,
                        iu.sn_mesin, iu.hour_meter, iu.aksesoris,
                        tu.tipe AS tipe_unit_name,
                        mu.merk_unit, mu.model_unit,
                        k.kapasitas_unit AS kapasitas_name,
                        tm.tipe_mast AS model_mast_name,
                        me.model_mesin AS model_mesin_name, me.merk_mesin,
                        d.nama_departemen AS departemen_name
                    FROM inventory_unit iu
                    LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
                    LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
                    LEFT JOIN kapasitas k ON k.id_kapasitas = iu.kapasitas_unit_id
                    LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
                    LEFT JOIN mesin me ON me.id = iu.model_mesin_id
                    LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
                    WHERE iu.id_inventory_unit = ?
                ", [(int) $currentItem['unit_id']])->getRowArray();

                // Parse accessories JSON
                if ($unitDetail && !empty($unitDetail['aksesoris'])) {
                    $raw = $unitDetail['aksesoris'];
                    $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
                    if (is_array($decoded)) {
                        $unitDetail['aksesoris_list'] = array_map(function ($a) {
                            if (is_string($a)) return strtoupper($a);
                            return strtoupper($a['name'] ?? $a['value'] ?? (string)$a);
                        }, $decoded);
                    } else {
                        $unitDetail['aksesoris_list'] = [];
                    }
                } elseif ($unitDetail) {
                    $unitDetail['aksesoris_list'] = [];
                }
            }

            $data = [
                'title'       => 'Verifikasi Unit Audit',
                'audit'       => $audit,
                'item'        => $currentItem,
                'index'       => $index,
                'totalUnits'  => $totalUnits,
                'hasPrev'     => $index > 1,
                'hasNext'     => $index < $totalUnits,
                'components'  => $components,
                'unitDetail'  => $unitDetail,
            ];

            return view('service/unit_verification_unit', $data);
        } catch (\Exception $e) {
            return redirect()->to('/service/unit-verification')->with('error', $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════
    // MARKETING APPROVAL FOR LOCATION AUDIT
    // ═══════════════════════════════════════════════════════

    /**
     * Approval page (Marketing) - Halaman tunggal: Pengajuan Unit + Request Lokasi + Approve Audit Lokasi
     */
    public function approvalLocation()
    {
        $data['title'] = 'Audit Approval';
        $data['stats'] = $this->auditLocationModel->getStats();
        return view('marketing/audit_approval_location', $data);
    }

    /**
     * Get pending location audit approvals
     */
    public function getPendingLocationApprovals()
    {
        try {
            $audits = $this->auditLocationModel->getPendingApprovals();
            return $this->response->setJSON(['success' => true, 'data' => $audits]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get pending customer location requests (new locations awaiting approval)
     * Includes ADD_UNIT requests linked to each location (for combined approval)
     */
    public function getPendingLocationRequests()
    {
        try {
            $db = \Config\Database::connect();
            $requests = $db->table('customer_locations cl')
                ->select("cl.*, c.customer_name, c.customer_code, a.area_name, TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as requested_by_name")
                ->join('customers c', 'c.id = cl.customer_id', 'left')
                ->join('areas a', 'a.id = cl.area_id', 'left')
                ->join('users u', 'u.id = cl.requested_by', 'left')
                ->where('cl.approval_status', 'PENDING')
                ->where('cl.is_active', 1)
                ->orderBy('cl.created_at', 'DESC')
                ->get()
                ->getResultArray();

            $locationIds = array_column($requests, 'id');
            $addUnitByLoc = array_fill_keys($locationIds, []);
            $pendingByKey = [];
            foreach ($requests as $r) {
                $key = (int) $r['customer_id'] . '|' . ($r['location_name'] ?? '');
                $pendingByKey[$key] = (int) $r['id'];
            }
            if (!empty($locationIds)) {
                $addUnits = $db->table('unit_audit_requests uar')
                    ->select('uar.*')
                    ->where('uar.request_type', 'ADD_UNIT')
                    ->where('uar.status', 'SUBMITTED')
                    ->get()
                    ->getResultArray();
                foreach ($addUnits as $au) {
                    $proposed = json_decode($au['proposed_data'] ?? '{}', true);
                    $locId = isset($proposed['customer_location_id']) ? (int) $proposed['customer_location_id'] : null;
                    $targetLocId = null;
                    if ($locId && in_array($locId, $locationIds, true)) {
                        $targetLocId = $locId;
                    } else {
                        $refLoc = $locId ? $db->table('customer_locations')->where('id', $locId)->get()->getRowArray() : null;
                        if ($refLoc) {
                            $key = (int) $refLoc['customer_id'] . '|' . ($refLoc['location_name'] ?? '');
                            $targetLocId = $pendingByKey[$key] ?? null;
                        }
                    }
                    if ($targetLocId) {
                        $unitId = $proposed['unit_id'] ?? null;
                        if ($unitId) {
                            $unit = $db->table('inventory_unit iu')
                                ->select('iu.*, mu.merk_unit, mu.model_unit, k.kapasitas_unit')
                                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                                ->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left')
                                ->where('iu.id_inventory_unit', $unitId)
                                ->get()->getRowArray();
                            $au['no_unit'] = $unit ? ($unit['no_unit'] ?? $unit['no_unit_na'] ?? 'UNIT-' . $unitId) : 'UNIT-' . $unitId;
                            $au['serial_number'] = $unit ? ($unit['serial_number'] ?? null) : null;
                            $au['merk_model'] = $unit ? trim(($unit['merk_unit'] ?? '') . ' ' . ($unit['model_unit'] ?? '')) : '-';
                            $au['kapasitas'] = $unit ? ($unit['kapasitas_unit'] ?? '-') : '-';
                        }
                        $addUnitByLoc[$targetLocId][] = $au;
                    }
                }
            }
            foreach ($requests as &$r) {
                $r['add_unit_requests'] = $addUnitByLoc[(int) $r['id']] ?? [];
            }

            return $this->response->setJSON(['success' => true, 'data' => $requests]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get unified approval history: Lokasi Baru (LOC) + Audit Lokasi (AUDLOC) approved
     * Untuk Riwayat Approval - satu tabel gabungan
     */
    public function getApprovalHistory()
    {
        try {
            $db = \Config\Database::connect();
            $merged = [];

            // 1. Approved Location Requests (Request Lokasi Baru)
            $locRequests = $db->table('customer_locations cl')
                ->select("cl.id, cl.location_code, cl.location_name, cl.approved_at,
                    c.customer_name, c.customer_code, a.area_name,
                    TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as approved_by_name")
                ->join('customers c', 'c.id = cl.customer_id', 'left')
                ->join('areas a', 'a.id = cl.area_id', 'left')
                ->join('users u', 'u.id = cl.approved_by', 'left')
                ->where('cl.approval_status', 'APPROVED')
                ->where('cl.is_active', 1)
                ->orderBy('cl.approved_at', 'DESC')
                ->limit(50)
                ->get()
                ->getResultArray();

            foreach ($locRequests as $r) {
                $merged[] = [
                    'type'            => 'LOCATION_REQUEST',
                    'id'              => $r['id'],
                    'code'            => $r['location_code'] ?? 'LOC-' . $r['id'],
                    'customer_name'   => $r['customer_name'] ?? '-',
                    'customer_code'   => $r['customer_code'] ?? '',
                    'location_name'   => $r['location_name'] ?? '-',
                    'area_name'       => $r['area_name'] ?? '-',
                    'tanggal'         => $r['approved_at'] ?? null,
                    'approved_by_name'=> $r['approved_by_name'] ?? '-',
                    'status'          => 'APPROVED',
                ];
            }

            // 2. Approved Location Audits (Approve Audit Lokasi)
            $auditRows = $db->table('unit_audit_locations ual')
                ->select('ual.id, ual.audit_number, ual.reviewed_at, ual.kontrak_total_units, ual.actual_total_units, ual.total_price_adjustment,
                    c.customer_name, cl.location_name,
                    TRIM(CONCAT(COALESCE(rev.first_name, ""), " ", COALESCE(rev.last_name, ""))) as reviewed_by_name')
                ->join('customers c', 'c.id = ual.customer_id', 'left')
                ->join('customer_locations cl', 'cl.id = ual.customer_location_id', 'left')
                ->join('users rev', 'rev.id = ual.reviewed_by', 'left')
                ->where('ual.status', 'APPROVED')
                ->orderBy('ual.reviewed_at', 'DESC')
                ->limit(50)
                ->get()
                ->getResultArray();

            foreach ($auditRows as $a) {
                $merged[] = [
                    'type'            => 'AUDIT_LOCATION',
                    'id'              => $a['id'],
                    'code'            => $a['audit_number'] ?? 'AUDLOC-' . $a['id'],
                    'customer_name'   => $a['customer_name'] ?? '-',
                    'customer_code'   => '',
                    'location_name'   => $a['location_name'] ?? '-',
                    'area_name'       => '-',
                    'tanggal'         => $a['reviewed_at'] ?? null,
                    'approved_by_name'=> trim($a['reviewed_by_name'] ?? '') ?: '-',
                    'status'          => 'APPROVED',
                    'audit_number'    => $a['audit_number'] ?? null,
                    'kontrak_total_units' => $a['kontrak_total_units'] ?? null,
                    'actual_total_units'  => $a['actual_total_units'] ?? null,
                    'total_price_adjustment' => $a['total_price_adjustment'] ?? null,
                ];
            }

            // Sort by tanggal DESC
            usort($merged, function ($a, $b) {
                $ta = strtotime($a['tanggal'] ?? '0');
                $tb = strtotime($b['tanggal'] ?? '0');
                return $tb - $ta;
            });

            return $this->response->setJSON(['success' => true, 'data' => array_slice($merged, 0, 50)]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get detail of a single approved location request (untuk modal detail)
     * Includes: requested_by, approved_by, all units (approved + rejected) dengan Merk/Model, Kapasitas, Harga Sewa
     */
    public function getLocationRequestDetail($id)
    {
        try {
            $db = \Config\Database::connect();
            $req = $db->table('customer_locations cl')
                ->select("cl.*, c.customer_name, c.customer_code, a.area_name,
                    TRIM(CONCAT(COALESCE(u_approved.first_name, ''), ' ', COALESCE(u_approved.last_name, ''))) as approved_by_name,
                    TRIM(CONCAT(COALESCE(u_req.first_name, ''), ' ', COALESCE(u_req.last_name, ''))) as requested_by_name")
                ->join('customers c', 'c.id = cl.customer_id', 'left')
                ->join('areas a', 'a.id = cl.area_id', 'left')
                ->join('users u_approved', 'u_approved.id = cl.approved_by', 'left')
                ->join('users u_req', 'u_req.id = cl.requested_by', 'left')
                ->where('cl.id', $id)
                ->where('cl.approval_status', 'APPROVED')
                ->get()
                ->getRowArray();

            if (!$req) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lokasi tidak ditemukan']);
            }

            $locKey = (int) $req['customer_id'] . '|' . ($req['location_name'] ?? '');
            $addUnits = $db->table('unit_audit_requests uar')
                ->select('uar.*')
                ->where('uar.request_type', 'ADD_UNIT')
                ->whereIn('uar.status', ['APPROVED', 'REJECTED'])
                ->get()
                ->getResultArray();

            $addUnitForLoc = [];
            foreach ($addUnits as $au) {
                $proposed = json_decode($au['proposed_data'] ?? '{}', true);
                $locId = isset($proposed['customer_location_id']) ? (int) $proposed['customer_location_id'] : null;
                $match = ($locId === (int) $id);
                if (!$match && $locId) {
                    $refLoc = $db->table('customer_locations')->where('id', $locId)->get()->getRowArray();
                    if ($refLoc) {
                        $refKey = (int) $refLoc['customer_id'] . '|' . ($refLoc['location_name'] ?? '');
                        $match = ($refKey === $locKey);
                    }
                }
                if ($match) {
                    $unitId = $proposed['unit_id'] ?? null;
                    if ($unitId) {
                        $unit = $db->table('inventory_unit iu')
                            ->select('iu.*, mu.merk_unit, mu.model_unit, k.kapasitas_unit')
                            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                            ->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left')
                            ->where('iu.id_inventory_unit', $unitId)
                            ->get()->getRowArray();
                        $au['no_unit'] = $unit ? ($unit['no_unit'] ?? $unit['no_unit_na'] ?? 'UNIT-' . $unitId) : 'UNIT-' . $unitId;
                        $au['serial_number'] = $unit ? ($unit['serial_number'] ?? null) : null;
                        $au['merk_model'] = $unit ? trim(($unit['merk_unit'] ?? '') . ' ' . ($unit['model_unit'] ?? '')) : '-';
                        $au['kapasitas'] = $unit ? ($unit['kapasitas_unit'] ?? '-') : '-';
                    }
                    $au['is_spare'] = !empty($proposed['is_spare']);
                    $au['harga_sewa_proposed'] = $proposed['harga_sewa'] ?? null;
                    if ($au['status'] === 'APPROVED' && !empty($au['kontrak_id']) && $unitId) {
                        $ku = $db->table('kontrak_unit')
                            ->where('kontrak_id', $au['kontrak_id'])
                            ->where('unit_id', $unitId)
                            ->where('customer_location_id', $id)
                            ->get()->getRowArray();
                        $au['harga_sewa'] = $ku ? ($ku['harga_sewa'] ?? $au['harga_sewa_proposed']) : $au['harga_sewa_proposed'];
                    } else {
                        $au['harga_sewa'] = $au['status'] === 'REJECTED' ? null : $au['harga_sewa_proposed'];
                    }
                    $addUnitForLoc[] = $au;
                }
            }
            $req['add_unit_requests'] = $addUnitForLoc;

            return $this->response->setJSON(['success' => true, 'data' => $req]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get recently approved location requests (untuk rollback) - deprecated, use getApprovalHistory
     */
    public function getApprovedLocationRequests()
    {
        try {
            $db = \Config\Database::connect();
            $requests = $db->table('customer_locations cl')
                ->select("cl.*, c.customer_name, c.customer_code, a.area_name, TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as approved_by_name")
                ->join('customers c', 'c.id = cl.customer_id', 'left')
                ->join('areas a', 'a.id = cl.area_id', 'left')
                ->join('users u', 'u.id = cl.approved_by', 'left')
                ->where('cl.approval_status', 'APPROVED')
                ->where('cl.is_active', 1)
                ->orderBy('cl.approved_at', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();

            return $this->response->setJSON(['success' => true, 'data' => $requests]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Approve a customer location request (combined with ADD_UNIT requests if any)
     */
    public function approveLocationRequest($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        try {
            $db = \Config\Database::connect();

            // Check if location exists and is pending
            $location = $db->table('customer_locations')->where('id', $id)->get()->getRowArray();
            if (!$location) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lokasi tidak ditemukan']);
            }
            if ($location['approval_status'] !== 'PENDING') {
                return $this->response->setJSON(['success' => false, 'message' => 'Lokasi sudah diproses']);
            }

            $notes = $this->request->getPost('notes') ?? '';

            // Get ADD_UNIT requests for this location (match by id or by customer_id+location_name)
            $addUnits = $db->table('unit_audit_requests')
                ->where('request_type', 'ADD_UNIT')
                ->where('status', 'SUBMITTED')
                ->get()
                ->getResultArray();
            $addUnitsForLoc = [];
            $locKey = $location ? ((int) $location['customer_id'] . '|' . ($location['location_name'] ?? '')) : null;
            foreach ($addUnits as $au) {
                $proposed = json_decode($au['proposed_data'] ?? '{}', true);
                $locId = isset($proposed['customer_location_id']) ? (int) $proposed['customer_location_id'] : null;
                $match = ($locId === (int) $id);
                if (!$match && $locId && $locKey) {
                    $refLoc = $db->table('customer_locations')->where('id', $locId)->get()->getRowArray();
                    if ($refLoc) {
                        $refKey = (int) $refLoc['customer_id'] . '|' . ($refLoc['location_name'] ?? '');
                        $match = ($refKey === $locKey);
                    }
                }
                if ($match) {
                    $addUnitsForLoc[] = $au;
                }
            }

            $kontrakId = $this->request->getPost('kontrak_id');
            $unitPrices = $this->request->getPost('unit_prices'); // JSON: { request_id: price } - hanya yang di-include

            $kontrakInfo = null;
            if ($kontrakId) {
                $kontrakInfo = $db->table('kontrak')->where('id', $kontrakId)->get()->getRowArray();
            }

            $db->transStart();

            // Approve location
            $db->table('customer_locations')->where('id', $id)->update([
                'approval_status' => 'APPROVED',
                'approved_by'     => $userId,
                'approved_at'     => date('Y-m-d H:i:s'),
                'approval_notes'  => $notes,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            $prices = is_string($unitPrices) ? json_decode($unitPrices, true) : (is_array($unitPrices) ? $unitPrices : []);
            $unitsAdded = 0;
            $unitsRejected = 0;
            $unitIdsAlreadyAdded = [];
            foreach ($addUnitsForLoc as $req) {
                $reqId = $req['id'];
                $isIncluded = isset($prices[$reqId]) || array_key_exists($reqId, $prices);
                if ($kontrakId && $isIncluded && $kontrakInfo) {
                    $proposed = json_decode($req['proposed_data'] ?? '{}', true);
                    $unitId = isset($proposed['unit_id']) ? (int) $proposed['unit_id'] : null;
                    if ($unitId) {
                        $hargaSewa = isset($prices[$reqId]) ? (float) $prices[$reqId] : (isset($prices[(string)$reqId]) ? (float) $prices[(string)$reqId] : 0);
                        $isSpare = !empty($proposed['is_spare']);
                        $tanggalMulai = $kontrakInfo['tanggal_mulai'] ?? date('Y-m-d');
                        $tanggalSelesai = $kontrakInfo['tanggal_berakhir'] ?? null;

                        $existing = $db->table('kontrak_unit')
                            ->where('kontrak_id', $kontrakId)
                            ->where('unit_id', $unitId)
                            ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE', 'AKTIF'])
                            ->get()->getRowArray();
                        if ($existing || in_array($unitId, $unitIdsAlreadyAdded, true)) {
                            $db->table('unit_audit_requests')->where('id', $reqId)->update([
                                'status'       => 'REJECTED',
                                'reviewed_by'  => $userId,
                                'reviewed_at'  => date('Y-m-d H:i:s'),
                                'review_notes' => $existing ? 'Unit sudah ada di kontrak' : 'Unit duplikat dalam batch',
                                'updated_at'   => date('Y-m-d H:i:s'),
                            ]);
                            $unitsRejected++;
                            continue;
                        }
                        $insertData = [
                                'kontrak_id'           => $kontrakId,
                                'unit_id'              => $unitId,
                                'customer_location_id' => $id,
                                'tanggal_mulai'        => $kontrakInfo['tanggal_mulai'] ?? date('Y-m-d'),
                                'tanggal_selesai'      => $kontrakInfo['tanggal_berakhir'] ?? null,
                                'status'               => 'ACTIVE',
                                'is_spare'             => $isSpare ? 1 : 0,
                                'harga_sewa'           => $hargaSewa,
                                'created_at'           => date('Y-m-d H:i:s'),
                            ];
                            $db->table('kontrak_unit')->insert($insertData);
                            $unitIdsAlreadyAdded[] = $unitId;

                            $db->table('unit_audit_requests')->where('id', $reqId)->update([
                                'status'       => 'APPROVED',
                                'reviewed_by'  => $userId,
                                'reviewed_at'  => date('Y-m-d H:i:s'),
                                'review_notes' => 'Approved bersama lokasi',
                                'kontrak_id'   => $kontrakId,
                                'updated_at'   => date('Y-m-d H:i:s'),
                            ]);
                            $unitsAdded++;
                        }
                } else {
                    $db->table('unit_audit_requests')->where('id', $req['id'])->update([
                        'status'       => 'REJECTED',
                        'reviewed_by'  => $userId,
                        'reviewed_at'  => date('Y-m-d H:i:s'),
                        'review_notes' => $kontrakId ? 'Unit tidak di-include dalam approval' : 'Lokasi diapprove tanpa unit',
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ]);
                    $unitsRejected++;
                }
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                $err = $db->error();
                $msg = 'Gagal memproses approval';
                if (!empty($err['message'])) {
                    $msg .= ': ' . $err['message'];
                    log_message('error', 'UnitAudit::approveLocationRequest DB error: ' . json_encode($err));
                }
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }

            $msg = 'Lokasi berhasil diapprove';
            if ($unitsAdded > 0) {
                $msg .= ' dan ' . $unitsAdded . ' unit ditambahkan ke kontrak';
            }
            if ($unitsRejected > 0) {
                $msg .= ' (' . $unitsRejected . ' unit ditolak)';
            }
            $msg .= '.';

            return $this->response->setJSON(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Reject a customer location request
     */
    public function rejectLocationRequest($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        try {
            $db = \Config\Database::connect();
            
            // Check if location exists and is pending
            $location = $db->table('customer_locations')->where('id', $id)->get()->getRowArray();
            if (!$location) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lokasi tidak ditemukan']);
            }
            if ($location['approval_status'] !== 'PENDING') {
                return $this->response->setJSON(['success' => false, 'message' => 'Lokasi sudah diproses']);
            }

            $notes = trim($this->request->getPost('notes') ?? '') ?: null;

            $db->transStart();
            $db->table('customer_locations')->where('id', $id)->update([
                'approval_status' => 'REJECTED',
                'approved_by'     => $userId,
                'approved_at'     => date('Y-m-d H:i:s'),
                'approval_notes'  => $notes ?? '',
                'is_active'       => 0,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            // Reject linked ADD_UNIT requests
            $addUnits = $db->table('unit_audit_requests')
                ->where('request_type', 'ADD_UNIT')
                ->where('status', 'SUBMITTED')
                ->get()
                ->getResultArray();
            foreach ($addUnits as $au) {
                $proposed = json_decode($au['proposed_data'] ?? '{}', true);
                $locId = isset($proposed['customer_location_id']) ? (int) $proposed['customer_location_id'] : null;
                if ($locId === (int) $id) {
                    $db->table('unit_audit_requests')->where('id', $au['id'])->update([
                        'status'       => 'REJECTED',
                        'reviewed_by'  => $userId,
                        'reviewed_at'  => date('Y-m-d H:i:s'),
                        'review_notes' => 'Lokasi ditolak',
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            $db->transComplete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lokasi berhasil ditolak',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Rollback approved location request (kembalikan ke PENDING)
     * - Revert customer_locations ke PENDING
     * - Hapus unit dari kontrak_unit jika ada
     * - Revert unit_audit_requests ke SUBMITTED
     */
    public function rollbackLocationRequest($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        try {
            $db = \Config\Database::connect();
            $location = $db->table('customer_locations')->where('id', $id)->get()->getRowArray();
            if (!$location) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lokasi tidak ditemukan']);
            }
            if ($location['approval_status'] !== 'APPROVED') {
                return $this->response->setJSON(['success' => false, 'message' => 'Hanya lokasi yang sudah diapprove yang dapat di-rollback']);
            }

            $db->transStart();

            // Get ADD_UNIT requests that were approved with this location (have kontrak_id)
            $approvedUnits = $db->table('unit_audit_requests')
                ->where('request_type', 'ADD_UNIT')
                ->where('status', 'APPROVED')
                ->where('kontrak_id IS NOT NULL')
                ->get()
                ->getResultArray();

            foreach ($approvedUnits as $au) {
                $proposed = json_decode($au['proposed_data'] ?? '{}', true);
                $locId = isset($proposed['customer_location_id']) ? (int) $proposed['customer_location_id'] : null;
                if ($locId !== (int) $id) {
                    continue;
                }
                $unitId = $proposed['unit_id'] ?? null;
                $kontrakId = $au['kontrak_id'] ?? null;
                if (!$unitId || !$kontrakId) {
                    continue;
                }
                // Hapus dari kontrak_unit
                $db->table('kontrak_unit')
                    ->where('kontrak_id', $kontrakId)
                    ->where('unit_id', $unitId)
                    ->where('customer_location_id', $id)
                    ->delete();
                // Revert unit_audit_requests ke SUBMITTED
                $db->table('unit_audit_requests')->where('id', $au['id'])->update([
                    'status'       => 'SUBMITTED',
                    'reviewed_by'  => null,
                    'reviewed_at'  => null,
                    'review_notes' => null,
                    'kontrak_id'   => null,
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }

            // Revert location ke PENDING
            $db->table('customer_locations')->where('id', $id)->update([
                'approval_status' => 'PENDING',
                'approved_by'     => null,
                'approved_at'     => null,
                'approval_notes'  => null,
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal rollback']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Lokasi berhasil di-rollback ke status PENDING']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get contracts for a customer (for Marketing approval dropdown)
     */
    public function getContractsForCustomer($customerId)
    {
        try {
            $db = \Config\Database::connect();
            $contracts = $db->table('kontrak k')
                ->select('k.id, k.no_kontrak, k.customer_po_number, k.status, k.tanggal_mulai, k.tanggal_berakhir')
                ->where('k.customer_id', $customerId)
                ->whereIn('k.status', ['ACTIVE', 'EXPIRED'])
                ->orderBy('k.status', 'ASC')
                ->orderBy('k.tanggal_berakhir', 'DESC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON(['success' => true, 'data' => $contracts]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Approve location audit
     */
    public function approveLocationAudit($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $pricing = [
            'price_per_unit'      => $this->request->getPost('price_per_unit'),
            'marketing_notes'     => $this->request->getPost('marketing_notes'),
            'deactivate_location' => $this->request->getPost('deactivate_location') === '1',
        ];

        try {
            $result = $this->auditLocationModel->approveAudit((int) $id, $pricing, $userId);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Reject location audit
     */
    public function rejectLocationAudit($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $notes = $this->request->getPost('notes');

        try {
            $result = $this->auditLocationModel->rejectAudit((int) $id, $notes, $userId);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    // ═══════════════════════════════════════════════════════
    // UNIT VERIFICATION MASTER DATA (per-unit admin view)
    // ═══════════════════════════════════════════════════════

    /**
     * GET  service/unit-audit/unit-master-data/{unitId}
     * Returns current unit data + master options for all dropdowns,
     * mirroring WorkOrderController::getUnitVerificationData().
     */
    public function getUnitVerificationMasterData(int $unitId)
    {
        try {
            $db = \Config\Database::connect();

            // Full unit data (inventory_unit has no attachment/battery/charger FK columns; components link via inventory_unit_id)
            $unit = $db->query("
                SELECT
                    iu.id_inventory_unit, iu.no_unit, iu.serial_number,
                    iu.tahun_unit, iu.keterangan, iu.tinggi_mast, iu.sn_mast,
                    iu.sn_mesin, iu.hour_meter, iu.aksesoris,
                    iu.tipe_unit_id, iu.model_unit_id, iu.kapasitas_unit_id,
                    iu.model_mast_id, iu.model_mesin_id, iu.departemen_id,
                    tu.tipe AS tipe_unit_name,
                    mu.merk_unit, mu.model_unit,
                    CONCAT(mu.merk_unit, ' - ', mu.model_unit) AS model_unit_name,
                    k.kapasitas_unit AS kapasitas_name,
                    tm.tipe_mast AS model_mast_name,
                    me.model_mesin AS model_mesin_name, me.merk_mesin,
                    CONCAT(me.merk_mesin, ' - ', me.model_mesin) AS model_mesin_full,
                    d.nama_departemen AS departemen_name
                FROM inventory_unit iu
                LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
                LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
                LEFT JOIN kapasitas k ON k.id_kapasitas = iu.kapasitas_unit_id
                LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
                LEFT JOIN mesin me ON me.id = iu.model_mesin_id
                LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
                WHERE iu.id_inventory_unit = ?
            ", [$unitId])->getRowArray();

            if (!$unit) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan']);
            }

            // Component data: link is component.inventory_unit_id = unitId (not columns on inventory_unit)
            $helper = new InventoryComponentHelper();
            $components = $helper->getUnitComponents($unitId);
            $unit['attachment_inventory_attachment_id'] = $components['attachment']['id_inventory_attachment'] ?? null;
            $unit['baterai_inventory_attachment_id']   = $components['battery']['id_inventory_attachment'] ?? null;
            $unit['charger_inventory_attachment_id']   = $components['charger']['id_inventory_attachment'] ?? null;

            // Parse aksesoris
            $aksesorisRaw = $unit['aksesoris'] ?? null;
            $aksesoris = [];
            if ($aksesorisRaw) {
                $decoded = is_string($aksesorisRaw) ? json_decode($aksesorisRaw, true) : $aksesorisRaw;
                if (is_array($decoded)) {
                    foreach ($decoded as $a) {
                        $aksesoris[] = strtoupper(is_string($a) ? $a : ($a['name'] ?? $a['value'] ?? (string)$a));
                    }
                }
            }
            $unit['aksesoris_list'] = $aksesoris;

            // Master options (mirrors WorkOrderController::getUnitVerificationData)
            $departemenOptions = $db->table('departemen')
                ->select('id_departemen as id, nama_departemen as name')
                ->orderBy('nama_departemen')->get()->getResultArray();

            $tipeUnitOptions = $db->table('tipe_unit')
                ->select('id_tipe_unit as id, tipe as name, id_departemen')
                ->orderBy('tipe')->get()->getResultArray();

            $modelUnitOptions = $db->table('model_unit')
                ->select('id_model_unit as id, CONCAT(merk_unit, " - ", model_unit) as name')
                ->orderBy('merk_unit')->get()->getResultArray();

            $kapasitasOptions = $db->table('kapasitas')
                ->select('id_kapasitas as id, kapasitas_unit as name')
                ->orderBy('kapasitas_unit')->get()->getResultArray();

            $modelMastOptions = $db->table('tipe_mast')
                ->select('id_mast as id, tipe_mast as name')
                ->orderBy('tipe_mast')->get()->getResultArray();

            $modelMesinOptions = $db->table('mesin')
                ->select('id as id, CONCAT(merk_mesin, " - ", model_mesin) as name, departemen_id')
                ->orderBy('merk_mesin')->get()->getResultArray();

            // Available attachments (include current; $components already loaded above)
            $attachmentOptions = $db->query("
                SELECT ia.id, CONCAT(a.tipe, ' ', a.merk, ' ', a.model, ' [SN: ', COALESCE(ia.serial_number, '-'), ']') AS name,
                    a.tipe, a.merk, a.model, ia.serial_number AS sn_attachment
                FROM inventory_attachments ia
                JOIN attachment a ON ia.attachment_type_id = a.id_attachment
                WHERE ia.status IN ('AVAILABLE') OR ia.inventory_unit_id = ?
                ORDER BY a.tipe, a.merk
            ", [$unitId])->getResultArray();

            $bateraiOptions = $db->query("
                SELECT ib.id, CONCAT(b.merk_baterai, ' ', b.tipe_baterai, ' [SN: ', COALESCE(ib.serial_number, '-'), ']') AS name,
                    b.merk_baterai, b.tipe_baterai, ib.serial_number AS sn_baterai
                FROM inventory_batteries ib
                JOIN baterai b ON ib.battery_type_id = b.id
                WHERE ib.status IN ('AVAILABLE') OR ib.inventory_unit_id = ?
                ORDER BY b.merk_baterai
            ", [$unitId])->getResultArray();

            $chargerOptions = $db->query("
                SELECT ic.id, CONCAT(c.merk_charger, ' ', c.tipe_charger, ' [SN: ', COALESCE(ic.serial_number, '-'), ']') AS name,
                    c.merk_charger, c.tipe_charger, ic.serial_number AS sn_charger
                FROM inventory_chargers ic
                JOIN charger c ON ic.charger_type_id = c.id_charger
                WHERE ic.status IN ('AVAILABLE') OR ic.inventory_unit_id = ?
                ORDER BY c.merk_charger
            ", [$unitId])->getResultArray();

            return $this->response->setJSON([
                'success'    => true,
                'unit'       => $unit,
                'components' => $components,
                'options'    => [
                    'departemen'  => $departemenOptions,
                    'tipe_unit'   => $tipeUnitOptions,
                    'model_unit'  => $modelUnitOptions,
                    'kapasitas'   => $kapasitasOptions,
                    'model_mast'  => $modelMastOptions,
                    'model_mesin' => $modelMesinOptions,
                    'attachment'  => $attachmentOptions,
                    'baterai'     => $bateraiOptions,
                    'charger'     => $chargerOptions,
                ],
                'csrf_hash'  => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * POST  service/unit-audit/save-unit-verification
     * Saves both audit result (submitAuditResults flow) AND updates
     * the master inventory_unit data + components.
     */
    public function saveUnitVerificationFromAudit()
    {
        $auditId = (int) $this->request->getPost('audit_id');
        $unitId  = (int) $this->request->getPost('unit_id');
        $items   = $this->request->getPost('items') ?: [];
        $summary = $this->request->getPost('summary') ?: [];
        $master  = $this->request->getPost('master') ?: [];

        if (!$auditId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Audit ID diperlukan']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Save audit results (existing flow)
            if (!empty($items)) {
                $this->auditLocationModel->submitAuditResults($auditId, $items, $summary);
            }

            // 2. Update inventory_unit master data (if unit_id provided)
            // Component link is on component tables (inventory_unit_id), not on inventory_unit
            if ($unitId && !empty($master)) {
                $helper = new InventoryComponentHelper();
                $oldComponents = $helper->getUnitComponents($unitId);
                $oldAtt = $oldComponents['attachment']['id_inventory_attachment'] ?? null;
                $oldBat = $oldComponents['battery']['id_inventory_attachment'] ?? null;
                $oldChr = $oldComponents['charger']['id_inventory_attachment'] ?? null;
                $postVerificationStatus = isset($master['post_verification_status']) && $master['post_verification_status'] !== ''
                    ? (int) $master['post_verification_status']
                    : null;
                $allowedPostStatuses = [1, 7, 8, 10]; // AVAILABLE_STOCK, RENTAL_ACTIVE, RENTAL_DAILY, BREAKDOWN
                if ($postVerificationStatus !== null && !in_array($postVerificationStatus, $allowedPostStatuses, true)) {
                    throw new \RuntimeException('Hasil verifikasi status unit tidak valid');
                }

                $unitData = array_filter([
                    'serial_number'     => $master['serial_number'] ?? null,
                    'tahun_unit'        => $master['tahun_unit'] ?? null,
                    'departemen_id'     => $master['departemen_id'] ?: null,
                    'tipe_unit_id'      => $master['tipe_unit_id'] ?: null,
                    'model_unit_id'     => $master['model_unit_id'] ?: null,
                    'kapasitas_unit_id' => $master['kapasitas_unit_id'] ?: null,
                    'model_mesin_id'    => $master['model_mesin_id'] ?: null,
                    'sn_mesin'          => $master['sn_mesin'] ?? null,
                    'model_mast_id'     => $master['model_mast_id'] ?: null,
                    'sn_mast'           => $master['sn_mast'] ?? null,
                    'tinggi_mast'       => $master['tinggi_mast'] ?? null,
                    'keterangan'        => $master['keterangan'] ?? null,
                    'hour_meter'        => $master['hour_meter'] ?? null,
                    'status_unit_id'    => $postVerificationStatus,
                    'workflow_status'   => $postVerificationStatus !== null
                        ? match ($postVerificationStatus) {
                            1 => 'TERSEDIA',
                            7 => 'DISEWA',
                            8 => 'DISEWA',
                            10 => 'UNDER_REPAIR',
                            default => null,
                        }
                        : null,
                ], fn($v) => $v !== null && $v !== '');

                $newAttachmentId = $master['attachment_id'] ?? null;
                if ($newAttachmentId) {
                    if ($oldAtt && $oldAtt != $newAttachmentId) {
                        $this->releaseComponent($db, 'inventory_attachments', $oldAtt);
                    }
                    $this->assignComponent($db, 'inventory_attachments', $newAttachmentId, $unitId);
                }

                $newBateraiId = $master['baterai_id'] ?? null;
                if ($newBateraiId) {
                    if ($oldBat && $oldBat != $newBateraiId) {
                        $this->releaseComponent($db, 'inventory_batteries', $oldBat);
                    }
                    $this->assignComponent($db, 'inventory_batteries', $newBateraiId, $unitId);
                }

                $newChargerId = $master['charger_id'] ?? null;
                if ($newChargerId) {
                    if ($oldChr && $oldChr != $newChargerId) {
                        $this->releaseComponent($db, 'inventory_chargers', $oldChr);
                    }
                    $this->assignComponent($db, 'inventory_chargers', $newChargerId, $unitId);
                }

                if (!empty($unitData)) {
                    $db->table('inventory_unit')
                        ->where('id_inventory_unit', $unitId)
                        ->update($unitData);
                }
            }

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new \Exception('Transaksi gagal');
            }

            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Verifikasi berhasil disimpan',
                'csrf_hash' => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    private function releaseComponent(\CodeIgniter\Database\BaseConnection $db, string $table, int $id): void
    {
        // Get old unit_id before releasing
        $oldRecord = $db->table($table)->select('inventory_unit_id')->where('id', $id)->get()->getRowArray();
        $oldUnitId = $oldRecord['inventory_unit_id'] ?? null;
        
        $db->table($table)->where('id', $id)->update([
            'inventory_unit_id' => null,
            'status'            => 'AVAILABLE',
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
        
        // Log to component_audit_log
        if ($oldUnitId) {
            $componentType = match($table) {
                'inventory_batteries' => 'BATTERY',
                'inventory_chargers' => 'CHARGER',
                'inventory_attachments' => 'ATTACHMENT',
                default => 'ATTACHMENT'
            };
            
            $auditService = new \App\Services\ComponentAuditService($db);
            $auditService->logRemoval($componentType, $id, $oldUnitId, [
                'triggered_by' => 'UNIT_AUDIT_VERIFICATION',
                'reference_type' => 'unit_audit',
                'notes' => $componentType . ' released during unit audit verification',
            ]);
        }
    }

    private function assignComponent(\CodeIgniter\Database\BaseConnection $db, string $table, int $id, int $unitId): void
    {
        $db->table($table)->where('id', $id)->update([
            'inventory_unit_id' => $unitId,
            'status'            => 'IN_USE',
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
        
        // Log to component_audit_log
        $componentType = match($table) {
            'inventory_batteries' => 'BATTERY',
            'inventory_chargers' => 'CHARGER',
            'inventory_attachments' => 'ATTACHMENT',
            default => 'ATTACHMENT'
        };
        
        $auditService = new \App\Services\ComponentAuditService($db);
        $auditService->logAssignment($componentType, $id, $unitId, [
            'triggered_by' => 'UNIT_AUDIT_VERIFICATION',
            'reference_type' => 'unit_audit',
            'notes' => $componentType . ' assigned during unit audit verification',
        ]);
    }
}
