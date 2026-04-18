<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UnitAssetRequestModel;
use App\Models\InventoryUnitModel;
use CodeIgniter\API\ResponseTrait;

/**
 * AssetRequestController
 * Purchasing module: Review and approve/reject asset number requests from Warehouse.
 * Halaman approval permintaan nomor aset dari Warehouse untuk unit NON_ASSET_STOCK.
 */
class AssetRequestController extends BaseController
{
    use ResponseTrait;

    protected UnitAssetRequestModel $assetRequestModel;
    protected InventoryUnitModel    $inventoryUnitModel;

    public function __construct()
    {
        $this->assetRequestModel  = new UnitAssetRequestModel();
        $this->inventoryUnitModel = new InventoryUnitModel();
        helper(['form', 'url', 'global_permission', 'simple_rbac', 'auth']);
    }

    /**
     * GET purchasing/asset-requests
     * Main page listing all asset number requests.
     */
    public function index()
    {
        return view('purchasing/asset_requests/index', [
            'title' => 'Permintaan Nomor Aset',
        ]);
    }

    /**
     * GET purchasing/asset-requests/suggest-number
     * Returns the last used no_unit and the next suggested number.
     */
    public function suggestNumber()
    {
        $db = \Config\Database::connect();

        $lastNoUnit = $db->table('inventory_unit')
            ->selectMax('CAST(no_unit AS UNSIGNED)', 'max_no')
            ->where('no_unit IS NOT NULL')
            ->where('no_unit !=', '')
            ->get()
            ->getRowArray()['max_no'] ?? null;

        $last    = $lastNoUnit ? (int) $lastNoUnit : 0;
        $suggest = $last + 1;

        return $this->response->setJSON([
            'success' => true,
            'last_no_unit' => $last > 0 ? (string) $last : null,
            'suggested'    => (string) $suggest,
            'csrf_hash'    => csrf_hash(),
        ]);
    }

    /**
     * POST purchasing/asset-requests/datatable
     * DataTable server-side response.
     */
    public function datatable()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        }

        try {
            $statusFilter = $this->request->getPost('status_filter') ?: null;
            $filters = [];
            if ($statusFilter && in_array($statusFilter, ['PENDING', 'APPROVED', 'REJECTED'], true)) {
                $filters['status'] = $statusFilter;
            }

            $allRows = $this->assetRequestModel->getWithDetails($filters);

            // Server-side search
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            if ($searchValue !== '') {
                $s = mb_strtolower($searchValue);
                $allRows = array_values(array_filter($allRows, static function ($row) use ($s) {
                    return str_contains(mb_strtolower((string)($row['stock_number'] ?? '')), $s)
                        || str_contains(mb_strtolower((string)($row['serial_number'] ?? '')), $s)
                        || str_contains(mb_strtolower((string)($row['merk_unit'] ?? '')), $s)
                        || str_contains(mb_strtolower((string)($row['model_unit'] ?? '')), $s)
                        || str_contains(mb_strtolower((string)($row['requested_by_name'] ?? '')), $s);
                }));
            }

            $total    = count($allRows);
            $start    = (int)($this->request->getPost('start')  ?? 0);
            $length   = (int)($this->request->getPost('length') ?? 20);
            $pageData = array_slice($allRows, $start, $length > 0 ? $length : null);

            return $this->response->setJSON([
                'draw'            => intval($this->request->getPost('draw')),
                'recordsTotal'    => $total,
                'recordsFiltered' => $total,
                'data'            => $pageData,
                'csrf_hash'       => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[AssetRequestController::datatable] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'draw'            => intval($this->request->getPost('draw')),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Terjadi kesalahan pada server.',
                'csrf_hash'       => csrf_hash(),
            ]);
        }
    }

    /**
     * POST purchasing/asset-requests/{id}/approve
     * Approve request: assign official no_unit, change status to AVAILABLE_STOCK.
     */
    public function approve(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        }

        try {
            $request = $this->assetRequestModel->find($id);
            if (!$request) {
                return $this->response->setJSON(['success' => false, 'message' => 'Request tidak ditemukan.']);
            }
            if ($request['status'] !== 'PENDING') {
                return $this->response->setJSON(['success' => false, 'message' => 'Request sudah diproses sebelumnya.']);
            }

            $assignedNoUnit = trim($this->request->getPost('assigned_no_unit') ?? '');
            if ($assignedNoUnit === '') {
                return $this->response->setJSON(['success' => false, 'message' => 'Nomor aset tidak boleh kosong.']);
            }

            // Ensure asset number is unique
            $db = \Config\Database::connect();
            $exists = $db->table('inventory_unit')
                ->where('no_unit', $assignedNoUnit)
                ->countAllResults();
            if ($exists > 0) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nomor aset ' . $assignedNoUnit . ' sudah digunakan oleh unit lain.']);
            }

            $userId = session()->get('user_id') ?? null;

            // Update inventory_unit: set no_unit, clear no_unit_na, change status to AVAILABLE_STOCK (1)
            $this->inventoryUnitModel->update((int)$request['id_inventory_unit'], [
                'no_unit'        => $assignedNoUnit,
                'no_unit_na'     => null,
                'status_unit_id' => 1, // AVAILABLE_STOCK
            ]);

            // Update request record
            $this->assetRequestModel->update($id, [
                'status'          => 'APPROVED',
                'reviewed_by'     => $userId,
                'reviewed_at'     => date('Y-m-d H:i:s'),
                'assigned_no_unit' => $assignedNoUnit,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permintaan disetujui. Nomor aset ' . $assignedNoUnit . ' telah ditetapkan.',
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[AssetRequestController::approve] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan sistem.']);
        }
    }

    /**
     * POST purchasing/asset-requests/{id}/reject
     * Reject request: clear no_unit_na so unit can re-request.
     */
    public function reject(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        }

        try {
            $request = $this->assetRequestModel->find($id);
            if (!$request) {
                return $this->response->setJSON(['success' => false, 'message' => 'Request tidak ditemukan.']);
            }
            if ($request['status'] !== 'PENDING') {
                return $this->response->setJSON(['success' => false, 'message' => 'Request sudah diproses sebelumnya.']);
            }

            $rejectNotes = trim($this->request->getPost('reject_notes') ?? '');
            $userId      = session()->get('user_id') ?? null;

            // Clear STOCK number from unit so it can be re-requested
            $this->inventoryUnitModel->update((int)$request['id_inventory_unit'], [
                'no_unit_na' => null,
            ]);

            // Update request record
            $this->assetRequestModel->update($id, [
                'status'       => 'REJECTED',
                'reviewed_by'  => $userId,
                'reviewed_at'  => date('Y-m-d H:i:s'),
                'reject_notes' => $rejectNotes ?: null,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permintaan ditolak. Unit dapat mengajukan kembali.',
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[AssetRequestController::reject] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan sistem.']);
        }
    }
}
