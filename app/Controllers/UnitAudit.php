<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UnitAuditRequestModel;
use App\Models\InventoryUnitModel;
use App\Models\AuditLocationModel;

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
     * Location audit index page (Service)
     */
    public function locationIndex()
    {
        $data['title'] = 'Audit Unit per Lokasi';
        $data['stats'] = $this->auditLocationModel->getStats();
        return view('service/unit_audit_location', $data);
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
     * Print audit form
     */
    public function printLocationAudit($id)
    {
        try {
            $audit = $this->auditLocationModel->getWithDetails((int) $id);
            if (!$audit) {
                return redirect()->to('/service/unit-audit/location')->with('error', 'Audit tidak ditemukan');
            }
            return view('service/print_audit_form', ['audit' => $audit]);
        } catch (\Exception $e) {
            return redirect()->to('/service/unit-audit/location')->with('error', $e->getMessage());
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
     * Submit to marketing for approval
     */
    public function submitToMarketing($id)
    {
        try {
            $this->auditLocationModel->submitForApproval((int) $id);
            return $this->response->setJSON(['success' => true, 'message' => 'Audit dikirim ke Marketing untuk approval']);
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
                return redirect()->to('/service/unit-audit/location')->with('error', 'Audit tidak ditemukan');
            }
            $data['title'] = 'Input Hasil Audit';
            $data['audit'] = $audit;
            return view('service/unit_audit_result_input', $data);
        } catch (\Exception $e) {
            return redirect()->to('/service/unit-audit/location')->with('error', $e->getMessage());
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
}
