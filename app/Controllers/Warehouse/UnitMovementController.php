<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\InventoryUnitModel;
use App\Models\UnitMovementModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class UnitMovementController extends BaseController
{
    protected $movementModel;
    protected $unitModel;

    public function __construct()
    {
        $this->movementModel = new UnitMovementModel();
        $this->unitModel = new InventoryUnitModel();
    }

    /**
     * Access checker
     */
    private function checkAccess($permission)
    {
        $user = session()->get('user_id');
        if (!$user) {
            return false;
        }

        $perm = strtolower((string) $permission);
        if ($perm === 'view') {
            return $this->hasPermission('warehouse.unit_inventory.view')
                || $this->hasPermission('warehouse.unit_inventory.index')
                || $this->hasPermission('warehouse.unit_movement.view')
                || $this->hasPermission('warehouse.movements.view')
                || $this->canAccess('warehouse');
        }
        if ($perm === 'create') {
            return $this->hasPermission('warehouse.unit_movement.create')
                || $this->hasPermission('warehouse.movements.create')
                || $this->hasPermission('warehouse.unit_inventory.edit')
                || $this->canManage('warehouse');
        }
        if ($perm === 'edit') {
            return $this->hasPermission('warehouse.unit_movement.edit')
                || $this->hasPermission('warehouse.movements.edit')
                || $this->hasPermission('warehouse.unit_inventory.edit')
                || $this->canManage('warehouse');
        }
        if ($perm === 'delete') {
            return $this->hasPermission('warehouse.unit_movement.delete')
                || $this->hasPermission('warehouse.movements.delete')
                || $this->hasPermission('warehouse.unit_inventory.delete')
                || $this->canDelete('warehouse');
        }

        return false;
    }

    /**
     * Unit Movement / Surat Jalan List
     */
    public function index()
    {
        if (!$this->checkAccess('view')) {
            return view('errors/html/error_403');
        }

        $data['title'] = 'Surat Jalan / Movement';
        $data['stats'] = $this->movementModel->getStats();
        $data['location_types'] = UnitMovementModel::getLocationTypes();
        $data['component_types'] = UnitMovementModel::getComponentTypes();
        $data['movement_purposes'] = UnitMovementModel::getMovementPurposes();

        return view('warehouse/unit_movement', $data);
    }

    /**
     * Get movements - DataTable
     */
    public function getMovements()
    {
        if (!$this->checkAccess('view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $status = $this->request->getGet('status');
        $originType = $this->request->getGet('origin_type');
        $destinationType = $this->request->getGet('destination_type');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($originType) $filters['origin_type'] = $originType;
        if ($destinationType) $filters['destination_type'] = $destinationType;
        if ($dateFrom) $filters['date_from'] = $dateFrom;
        if ($dateTo) $filters['date_to'] = $dateTo;

        $movements = $this->movementModel->getWithUnitInfo($filters);
        $movements = $this->movementModel->appendListPreview($movements);

        return $this->response->setJSON([
            'success' => true,
            'data' => $movements,
        ]);
    }

    /**
     * Create new movement
     */
    public function createMovement()
    {
        if (!$this->checkAccess('create')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        try {
            $payload = $this->request->getJSON(true);
            if (!is_array($payload) || $payload === []) {
                $payload = $this->request->getPost();
            }

            $items = $this->extractItemsPayload($payload);
            $stops = $this->extractStopsPayload($payload);
            $errors = $this->validateMovementPayload($payload, $items, $stops);
            if ($errors !== []) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa kembali data yang diisi.',
                    'errors'  => $errors,
                ]);
            }

            $header = [
                'unit_id'               => !empty($payload['unit_id']) ? (int)$payload['unit_id'] : null,
                'component_id'          => !empty($payload['component_id']) ? (int)$payload['component_id'] : null,
                'component_type'        => strtoupper((string)($payload['component_type'] ?? 'FORKLIFT')),
                'origin_location'       => trim((string)$payload['origin_location']),
                'destination_location'  => trim((string)$payload['destination_location']),
                'destination_recipient_name' => trim((string)($payload['destination_recipient_name'] ?? '')),
                'origin_type'           => strtoupper((string)$payload['origin_type']),
                'destination_type'      => strtoupper((string)$payload['destination_type']),
                'movement_date'         => $payload['movement_date'],
                'driver_name'           => trim((string)($payload['driver_name'] ?? '')),
                'vehicle_number'        => trim((string)($payload['vehicle_number'] ?? '')),
                'vehicle_type'          => trim((string)($payload['vehicle_type'] ?? '')),
                'notes'                 => trim((string)($payload['notes'] ?? '')),
                'movement_number'       => $this->movementModel->generateMovementNumber(),
                'surat_jalan_number'    => $this->movementModel->generateSuratJalanNumber(),
                'verification_code'     => $this->movementModel->generateVerificationCode(),
                'created_by_user_id'    => (int)session()->get('user_id'),
                'status'                => 'DRAFT',
            ];

            $db = \Config\Database::connect();
            if ($db->fieldExists('movement_purpose', 'unit_movements')) {
                $mp = strtoupper(trim((string)($payload['movement_purpose'] ?? UnitMovementModel::PURPOSE_INTERNAL_TRANSFER)));
                $allowedMp = array_keys(UnitMovementModel::getMovementPurposes());
                $header['movement_purpose'] = in_array($mp, $allowedMp, true) ? $mp : UnitMovementModel::PURPOSE_INTERNAL_TRANSFER;
            }
            if (!$db->fieldExists('vehicle_type', 'unit_movements')) {
                unset($header['vehicle_type']);
            }
            if (! $db->fieldExists('destination_recipient_name', 'unit_movements')) {
                unset($header['destination_recipient_name']);
            }

            $result = $this->movementModel->createWithDetails($header, $items, $stops);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Surat jalan berhasil dibuat',
                'data' => [
                    'id' => $result,
                    'movement_number' => $header['movement_number'],
                    'surat_jalan_number' => $header['surat_jalan_number'] ?? null,
                    'verification_code' => $header['verification_code'] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            log_message('error', '[UnitMovementController::createMovement] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Update movement to IN_TRANSIT
     */
    public function startMovement($id)
    {
        if (!$this->checkAccess('edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $movement = $this->movementModel->find($id);
        if (!$movement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data perpindahan tidak ditemukan']);
        }

        if ($movement['status'] !== 'DRAFT') {
            return $this->response->setJSON(['success' => false, 'message' => 'Perpindahan sudah dimulai atau selesai']);
        }

        try {
            $driverName    = trim((string) $this->request->getPost('driver_name'));
            $vehicleNumber = trim((string) $this->request->getPost('vehicle_number'));
            $vehicleType   = trim((string) $this->request->getPost('vehicle_type'));
            $notes         = trim((string) $this->request->getPost('notes'));

            $updateData = [
                'driver_name'    => $driverName,
                'vehicle_number' => $vehicleNumber,
                'notes'          => $notes,
            ];

            $db = \Config\Database::connect();
            if ($db->fieldExists('vehicle_type', 'unit_movements')) {
                $updateData['vehicle_type'] = $vehicleType;
            }

            $this->movementModel->update($id, $updateData);

            try {
                $checkpointOk = $this->movementModel->recordDepartureFromWarehouse($id, $driverName, $vehicleNumber, $notes);
                if (! $checkpointOk) {
                    $this->movementModel->update($id, ['status' => 'IN_TRANSIT']);
                }
            } catch (\Throwable $e) {
                log_message('error', '[UnitMovementController::startMovement] checkpoint: ' . $e->getMessage());
                $this->movementModel->update($id, ['status' => 'IN_TRANSIT']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Movement started',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Confirm arrival
     */
    public function confirmArrival($id)
    {
        if (!$this->checkAccess('edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $movement = $this->movementModel->find($id);
        if (!$movement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data perpindahan tidak ditemukan']);
        }

        if ($movement['status'] !== 'IN_TRANSIT') {
            return $this->response->setJSON(['success' => false, 'message' => 'Perpindahan tidak dalam status transit']);
        }

        try {
            $userId = session()->get('user_id');
            $this->movementModel->confirmArrival($id, $userId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Movement completed and unit location updated',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Cancel movement
     */
    public function cancelMovement($id)
    {
        if (!$this->checkAccess('delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $movement = $this->movementModel->find($id);
        if (!$movement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data perpindahan tidak ditemukan']);
        }

        if ($movement['status'] === 'ARRIVED') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot cancel completed movement']);
        }

        try {
            $this->movementModel->cancelMovement($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Movement cancelled',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Get movement details
     */
    public function getMovementDetail($id)
    {
        if (!$this->checkAccess('view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $bundle = $this->movementModel->getMovementDetailBundle((int)$id);
        if (!$bundle) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data perpindahan tidak ditemukan']);
        }

        $bundle['items'] = $this->movementModel->enrichItemsForPrint($bundle['items']);

        // Get unit info if exists (header unit_id, atau baris pertama Forklift di multi-item)
        $unit         = null;
        $movement     = $bundle['movement'];
        $unitIdDetail = (int) ($movement['unit_id'] ?? 0);
        if ($unitIdDetail <= 0 && ! empty($bundle['items'])) {
            foreach ($bundle['items'] as $it) {
                if (strtoupper((string) ($it['component_type'] ?? '')) === 'FORKLIFT' && ! empty($it['unit_id'])) {
                    $unitIdDetail = (int) $it['unit_id'];
                    break;
                }
            }
        }
        if ($unitIdDetail > 0) {
            try {
                $unit = $this->unitModel->getUnitDetailForWorkOrder($unitIdDetail);
            } catch (\Throwable $e) {
                log_message('error', '[UnitMovementController::getMovementDetail] getUnitDetailForWorkOrder: ' . $e->getMessage());
                $unit = null;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'movement' => $movement,
                'unit' => $unit,
                'items' => $bundle['items'] ?? [],
                'stops' => $bundle['stops'] ?? [],
                'checkpoints' => $bundle['checkpoints'] ?? [],
            ],
        ]);
    }

    /**
     * Cetak surat jalan (user login gudang).
     */
    public function printMovement($id)
    {
        if (! $this->checkAccess('view')) {
            return view('errors/html/error_403');
        }

        $bundle = $this->movementModel->getMovementPrintBundle((int) $id);
        if ($bundle === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $autoPrint = in_array(strtolower((string) $this->request->getGet('autoprint')), ['1', 'true', 'yes'], true);

        return view('public/surat_jalan_print', [
            'movement'        => $bundle['movement'],
            'items'           => $bundle['items'],
            'stops'           => $bundle['stops'],
            'checkpoints'     => $bundle['checkpoints'],
            'companyName'     => 'PT Sarana Mitra Luas Tbk',
            'isPublicContext' => false,
            'autoPrint'       => $autoPrint,
        ]);
    }

    /**
     * Get units for dropdown (available units)
     */
    public function getAvailableUnits()
    {
        if (!$this->checkAccess('view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $query = trim((string)$this->request->getGet('q'));
        if ($query !== '') {
            $units = $this->movementModel->searchUnitsForMovement($query, 25);
        } else {
            $units = $this->unitModel->getUnitsForDropdown([InventoryUnitModel::STATUS_UNIT_SOLD_ID]);
            $units = array_slice($units, 0, 25);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $units,
        ]);
    }

    /**
     * Get components by type for dropdown (Attachment, Charger, Battery, Sparepart)
     */
    public function getComponentsByType()
    {
        if (!$this->checkAccess('view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $type = strtoupper($this->request->getGet('type') ?? '');
        $db   = \Config\Database::connect();
        $data = [];

        try {
            switch ($type) {
                case 'ATTACHMENT':
                    if ($db->tableExists('inventory_attachments')) {
                        $rows = $db->table('inventory_attachments ia')
                            ->select('ia.id, CONCAT(IFNULL(a.tipe,""), " ", IFNULL(a.merk,""), " ", IFNULL(a.model,""), " [", IFNULL(ia.item_number,""), "]") as label, IFNULL(ia.storage_location,"") as location, IFNULL(ia.status,"") as status')
                            ->join('attachment a', 'a.id_attachment = ia.attachment_type_id', 'left')
                            ->get()->getResultArray();
                        $data = $rows;
                    }
                    break;
                case 'CHARGER':
                    if ($db->tableExists('inventory_chargers')) {
                        $rows = $db->table('inventory_chargers ic')
                            ->select('ic.id, CONCAT(IFNULL(ic.item_number,""), " SN:", IFNULL(ic.serial_number,""), " [", IFNULL(ic.input_voltage,""), "V/", IFNULL(ic.output_voltage,""), "V]") as label, IFNULL(ic.storage_location,"") as location, IFNULL(ic.status,"") as status')
                            ->get()->getResultArray();
                        $data = $rows;
                    }
                    break;
                case 'BATTERY':
                    if ($db->tableExists('inventory_batteries')) {
                        $rows = $db->table('inventory_batteries ib')
                            ->select('ib.id, CONCAT(IFNULL(ib.item_number,""), " SN:", IFNULL(ib.serial_number,""), " [", IFNULL(ib.voltage,""), "V ", IFNULL(ib.ampere_hour,""), "Ah]") as label, IFNULL(ib.storage_location,"") as location, IFNULL(ib.status,"") as status')
                            ->get()->getResultArray();
                        $data = $rows;
                    }
                    break;
                case 'FORK':
                    if ($db->tableExists('inventory_forks')) {
                        $rows = $db->table('inventory_forks ifork')
                            ->select('ifork.id, CONCAT(IFNULL(f.name,""), " [", IFNULL(ifork.item_number,""), "] (", IFNULL(ifork.qty_pairs,1), " pasang)") as label, IFNULL(ifork.storage_location,"") as location, IFNULL(ifork.status,"") as status')
                            ->join('fork f', 'f.id = ifork.fork_id', 'left')
                            ->get()->getResultArray();
                        $data = $rows;
                    }
                    break;
                case 'SPAREPART':
                    if ($db->tableExists('inventory_spareparts') && $db->tableExists('sparepart')) {
                        $rows = $db->table('inventory_spareparts isp')
                            ->select('isp.id, CONCAT(IFNULL(sp.kode,""), " - ", LEFT(IFNULL(sp.desc_sparepart,""), 60)) as label, IFNULL(isp.lokasi_rak,"") as location, "" as status')
                            ->join('sparepart sp', 'sp.id_sparepart = isp.sparepart_id', 'left')
                            ->where('isp.stok >', 0)
                            ->get()->getResultArray();
                        $data = $rows;
                    }
                    break;
                case 'FORKLIFT':
                    $query = trim((string)$this->request->getGet('q'));
                    $units = $query !== ''
                        ? $this->movementModel->searchUnitsForMovement($query, 25)
                        : array_slice($this->unitModel->getUnitsForDropdown([InventoryUnitModel::STATUS_UNIT_SOLD_ID]), 0, 25);
                    $data  = $units;
                    break;
                case 'OTHERS':
                    $data = [];
                    break;
                default:
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid component type: ' . $type,
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
            ]);
        }
    }

    private function extractItemsPayload(array $payload): array
    {
        if (!empty($payload['items']) && is_array($payload['items'])) {
            return array_values(array_filter($payload['items'], static fn ($item) => is_array($item)));
        }

        return [[
            'component_type' => $payload['component_type'] ?? 'FORKLIFT',
            'unit_id'        => $payload['unit_id'] ?? null,
            'component_id'   => $payload['component_id'] ?? null,
            'qty'            => 1,
            'item_notes'     => null,
        ]];
    }

    private function extractStopsPayload(array $payload): array
    {
        if (!empty($payload['stops']) && is_array($payload['stops'])) {
            $out = [];
            foreach ($payload['stops'] as $index => $stop) {
                if (!is_array($stop)) {
                    continue;
                }
                $stopType = strtoupper((string)($stop['stop_type'] ?? 'TRANSIT'));
                $out[] = [
                    'stop_type'      => $stopType,
                    'location_name'  => trim((string)($stop['location_name'] ?? '')),
                    'location_type'  => strtoupper((string)($stop['location_type'] ?? 'OTHER')),
                    'eta_at'         => $stop['eta_at'] ?? null,
                    'sequence_no'    => $index + 1,
                ];
            }
            if ($out !== []) {
                return $out;
            }
        }

        return [
            [
                'stop_type'      => 'ORIGIN',
                'location_name'  => trim((string)($payload['origin_location'] ?? '')),
                'location_type'  => strtoupper((string)($payload['origin_type'] ?? 'OTHER')),
            ],
            [
                'stop_type'      => 'DESTINATION',
                'location_name'  => trim((string)($payload['destination_location'] ?? '')),
                'location_type'  => strtoupper((string)($payload['destination_type'] ?? 'OTHER')),
            ],
        ];
    }

    private function validateMovementPayload(array $payload, array $items, array $stops): array
    {
        $errors = [];
        $componentTypes = array_keys(UnitMovementModel::getComponentTypes());
        $locationTypes = array_keys(UnitMovementModel::getLocationTypes());

        if (empty($payload['origin_location'])) {
            $errors['origin_location'] = 'Lokasi asal wajib diisi.';
        }
        if (empty($payload['destination_location'])) {
            $errors['destination_location'] = 'Lokasi tujuan wajib diisi.';
        }
        $dbVal = \Config\Database::connect();
        if ($dbVal->fieldExists('destination_recipient_name', 'unit_movements')) {
            $recName = trim((string) ($payload['destination_recipient_name'] ?? ''));
            if ($recName === '') {
                $errors['destination_recipient_name'] = 'Nama penerima di tujuan wajib diisi.';
            } elseif (strlen($recName) > 120) {
                $errors['destination_recipient_name'] = 'Nama penerima maksimal 120 karakter.';
            }
        }
        if (empty($payload['origin_type']) || !in_array(strtoupper((string)$payload['origin_type']), $locationTypes, true)) {
            $errors['origin_type'] = 'Tipe asal tidak valid.';
        }
        if (empty($payload['destination_type']) || !in_array(strtoupper((string)$payload['destination_type']), $locationTypes, true)) {
            $errors['destination_type'] = 'Tipe tujuan tidak valid.';
        }
        if (empty($payload['movement_date'])) {
            $errors['movement_date'] = 'Tanggal perpindahan wajib diisi.';
        }
        if ($dbVal->fieldExists('vehicle_type', 'unit_movements')) {
            $vehicleType = trim((string) ($payload['vehicle_type'] ?? ''));
            if ($vehicleType === '') {
                $errors['vehicle_type'] = 'Jenis kendaraan wajib diisi.';
            } elseif (strlen($vehicleType) > 120) {
                $errors['vehicle_type'] = 'Jenis kendaraan maksimal 120 karakter.';
            }
        }
        if (trim((string)($payload['notes'] ?? '')) === '') {
            $errors['notes'] = 'Alasan wajib diisi.';
        }

        $purposes = array_keys(UnitMovementModel::getMovementPurposes());
        $mp = strtoupper(trim((string)($payload['movement_purpose'] ?? UnitMovementModel::PURPOSE_INTERNAL_TRANSFER)));
        if (!in_array($mp, $purposes, true)) {
            $errors['movement_purpose'] = 'Tipe surat jalan tidak valid.';
        } elseif ($mp === UnitMovementModel::PURPOSE_SCRAP_SALE) {
            $hasForklift = false;
            foreach ($items as $item) {
                if (strtoupper((string)($item['component_type'] ?? '')) === 'FORKLIFT' && !empty($item['unit_id'])) {
                    $hasForklift = true;
                    break;
                }
            }
            if (!$hasForklift) {
                $errors['movement_purpose'] = 'SJ jual scrab wajib memuat minimal 1 baris Forklift/Unit dengan unit terpilih.';
            }
        }

        if (count($items) < 1) {
            $errors['items'] = 'Minimal 1 barang harus dipilih.';
        }
        foreach ($items as $idx => $item) {
            $type = strtoupper((string)($item['component_type'] ?? ''));
            if (!in_array($type, $componentTypes, true)) {
                $errors["items_{$idx}_component_type"] = 'Tipe barang tidak valid.';
            }
            if ($type === 'FORKLIFT' && empty($item['unit_id'])) {
                $errors["items_{$idx}_unit_id"] = 'Unit wajib dipilih untuk tipe Forklift.';
            }
            if ($type !== 'FORKLIFT' && $type !== 'OTHERS' && empty($item['component_id'])) {
                $errors["items_{$idx}_component_id"] = 'Komponen wajib dipilih.';
            }
            if ($type === 'OTHERS' && trim((string) ($item['item_notes'] ?? '')) === '') {
                $errors["items_{$idx}_item_notes"] = 'Keterangan barang wajib diisi untuk tipe Others.';
            }
        }

        if (count($stops) < 2) {
            $errors['stops'] = 'Minimal 2 lokasi (asal dan tujuan) wajib diisi.';
        } else {
            foreach ($stops as $idx => $stop) {
                if (empty($stop['location_name'])) {
                    $errors["stops_{$idx}_location_name"] = 'Lokasi stop tidak boleh kosong.';
                }
            }
        }

        return $errors;
    }
}
