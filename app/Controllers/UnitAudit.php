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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
                $proposedData['unit_id']     = $this->request->getPost('proposed_unit_id');
                $proposedData['is_spare']    = $this->request->getPost('proposed_is_spare') ? 1 : 0;
                $proposedData['harga_sewa']  = $this->request->getPost('proposed_harga_sewa');
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Marketing Approval ──────────────────────────────

    /**
     * Approval page (Marketing)
     */
    public function approval()
    {
        $data['title']   = 'Audit Approval';
        $data['stats']   = $this->auditModel->getStats();
        return view('marketing/audit_approval', $data);
    }

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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
                return $this->response->setJSON(['success' => false, 'message' => 'Location not found']);
            }
            return $this->response->setJSON(['success' => true, 'data' => $location]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get locations with latest audit status for a customer (Unit Audit page badge display)
     */
    public function getLocationsWithAuditStatus($customerId)
    {
        try {
            $locations = $this->auditLocationModel->getLocationsForCustomer((int) $customerId);
            $statusMap = $this->auditLocationModel->getLocationAuditStatusForCustomer((int) $customerId);
            foreach ($locations as &$loc) {
                $locId = (int) $loc['id'];
                $loc['audit_status'] = $statusMap[$locId] ?? null;
            }
            return $this->response->setJSON(['success' => true, 'data' => $locations]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
     * Approval page for location audits (Marketing)
     */
    public function approvalLocation()
    {
        $data['title'] = 'Approve Audit Unit';
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            'price_per_unit'   => $this->request->getPost('price_per_unit'),
            'marketing_notes'  => $this->request->getPost('marketing_notes'),
        ];

        try {
            $result = $this->auditLocationModel->approveAudit((int) $id, $pricing, $userId);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
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
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function releaseComponent(\CodeIgniter\Database\BaseConnection $db, string $table, int $id): void
    {
        $db->table($table)->where('id', $id)->update([
            'inventory_unit_id' => null,
            'status'            => 'AVAILABLE',
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
    }

    private function assignComponent(\CodeIgniter\Database\BaseConnection $db, string $table, int $id, int $unitId): void
    {
        $db->table($table)->where('id', $id)->update([
            'inventory_unit_id' => $unitId,
            'status'            => 'IN_USE',
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
    }
}
