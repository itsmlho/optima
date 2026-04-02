<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\UnitMovementModel;
use App\Models\InventoryUnitModel;

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
        return true;
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

        $validation = \Config\Services::validation();
        $validation->setRules([
            'unit_id'               => 'permit_empty|integer',
            'origin_location'       => 'required|max_length[100]',
            'destination_location'  => 'required|max_length[100]',
            'origin_type'           => 'required|in_list[POS_1,POS_2,POS_3,POS_4,POS_5,CUSTOMER_SITE,WAREHOUSE,OTHER]',
            'destination_type'      => 'required|in_list[POS_1,POS_2,POS_3,POS_4,POS_5,CUSTOMER_SITE,WAREHOUSE,OTHER]',
            'movement_date'         => 'required|valid_date',
            'component_type'        => 'permit_empty|in_list[FORKLIFT,ATTACHMENT,CHARGER,BATTERY,FORK,SPAREPART]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang diisi.',
                'errors' => $validation->getErrors(),
            ]);
        }

        try {
            $data = $this->request->getPost();
            $data['movement_number'] = $this->movementModel->generateMovementNumber();
            $data['created_by_user_id'] = session()->get('user_id');
            $data['status'] = 'DRAFT';

            // Generate SJ number if customer site
            if (in_array($data['destination_type'], ['CUSTOMER_SITE', 'POS_1', 'POS_2', 'POS_3', 'POS_4', 'POS_5'])) {
                $data['surat_jalan_number'] = $this->movementModel->generateSuratJalanNumber();
            }

            $result = $this->movementModel->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Movement created successfully',
                'data' => [
                    'id' => $result,
                    'movement_number' => $data['movement_number'],
                    'surat_jalan_number' => $data['surat_jalan_number'] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
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
            $this->movementModel->update($id, [
                'status' => 'IN_TRANSIT',
                'driver_name' => $this->request->getPost('driver_name'),
                'vehicle_number' => $this->request->getPost('vehicle_number'),
                'notes' => $this->request->getPost('notes'),
            ]);

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

        $movement = $this->movementModel->find($id);
        if (!$movement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data perpindahan tidak ditemukan']);
        }

        // Get unit info if exists
        $unit = null;
        if ($movement['unit_id']) {
            $unit = $this->unitModel->getUnitDetailForWorkOrder($movement['unit_id']);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'movement' => $movement,
                'unit' => $unit,
            ],
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

        $units = $this->unitModel->getUnitsForDropdown();

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
                    $units = $this->unitModel->getUnitsForDropdown();
                    $data  = $units;
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
}
