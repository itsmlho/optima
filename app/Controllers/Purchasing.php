<?php

namespace App\Controllers;

// Gunakan model yang sudah digabung

use App\Models\AttachmentModel;
use App\Models\BateraiModel;
use App\Models\ChargerModel;
use App\Models\DepartemenModel;
use App\Models\JenisRodaModel;
use App\Models\KapasitasModel;
use App\Models\MesinModel;
use App\Models\ModelUnitModel;
use App\Models\POUnitsModel;
use App\Models\POAttachmentModel;
use App\Models\PurchasingManagementModel;
use App\Models\PurchasingModel;
use App\Models\StatusUnitModel;
use App\Traits\ActivityLoggingTrait;
use CodeIgniter\API\ResponseTrait;
use App\Models\SupplierModel;
use App\Models\TipeBanModel;
use App\Models\TipeMastModel;
use App\Models\TipeUnitModel;
use App\Models\ValveModel;
use App\Models\SparepartModel; // <-- adit
use App\Models\POSparepartItemModel; // <-- adit
use App\Models\InventorySparepartModel;
use App\Models\InventoryUnitModel; // <-- untuk inventory unit
use App\Models\InventoryAttachmentModel; // <-- untuk inventory attachment
use App\Models\PODeliveryModel; // <-- untuk delivery tracking
use App\Models\DeliveryItemModel; // <-- untuk delivery items


class Purchasing extends BaseController
{
    use ResponseTrait, ActivityLoggingTrait;
 
    // Standardisasi nama properti model
    protected $purchasingModel;
    protected $poUnitsModel;
    protected $poItemsModel;
    protected $notificationModel;
    protected $supplierModel;
    protected $purchaseModel;
    protected $statusUnitModel;
    protected $departemenModel;
    protected $tipeUnitModel;
    protected $modelUnitModel;
    protected $kapasitasModel;
    protected $tipeMastModel;
    protected $mesinModel;
    protected $attachmentModel;
    protected $bateraiModel;
    protected $chargerModel;
    protected $jenisRodaModel;
    protected $tipeBanModel;
    protected $valveModel;
    protected $sparepartModel;
    protected $poSparepartItemModel;
    protected $inventorySparepartModel;
    protected $purchasingManagementModel;
    protected $inventoryUnitModel; // <-- untuk inventory unit
    protected $inventoryAttachmentModel; // <-- untuk inventory attachment
    protected $poAttachmentModel; // <-- untuk po attachment items
    protected $poDeliveryModel; // <-- untuk delivery tracking
    protected $deliveryItemModel; // <-- untuk delivery items

    public function __construct()
    {
        // Inisialisasi model dengan nama yang sudah distandarisasi
        $this->purchasingModel = new PurchasingManagementModel();
        $this->poUnitsModel = new POUnitsModel();
        $this->poItemsModel = new POAttachmentModel();
        $this->notificationModel = new PurchasingManagementModel();
        $this->supplierModel = new SupplierModel();
        $this->purchaseModel = new PurchasingModel();
        $this->statusUnitModel = new StatusUnitModel();
        $this->departemenModel = new DepartemenModel();
        $this->tipeUnitModel = new TipeUnitModel();
        $this->modelUnitModel = new ModelUnitModel();
        $this->kapasitasModel = new KapasitasModel();
        $this->tipeMastModel = new TipeMastModel();
        $this->mesinModel = new MesinModel();
        $this->attachmentModel = new AttachmentModel();
        $this->bateraiModel = new BateraiModel();
        $this->chargerModel = new ChargerModel();
        $this->jenisRodaModel = new JenisRodaModel();
        $this->tipeBanModel = new TipeBanModel();
        $this->valveModel = new ValveModel();
        $this->sparepartModel = new SparepartModel();
        $this->poSparepartItemModel = new POSparepartItemModel();
        $this->inventorySparepartModel = new InventorySparepartModel();
        $this->purchasingManagementModel = new PurchasingManagementModel();
        $this->inventoryUnitModel = new InventoryUnitModel(); // <-- untuk inventory unit
        $this->inventoryAttachmentModel = new InventoryAttachmentModel(); // <-- untuk inventory attachment
        $this->poAttachmentModel = new POAttachmentModel(); // <-- untuk po attachment items
        $this->poDeliveryModel = new PODeliveryModel(); // <-- untuk delivery tracking
        $this->deliveryItemModel = new DeliveryItemModel(); // <-- untuk delivery items
        
        helper(['form', 'url']);
    }
    

    //FUNCTION API
        public function getModelUnitMerk()
        {
            // Ambil parameter 'merk' dari URL
            $merk = $this->request->getGet("merk");
        
            // Validasi: pastikan parameter 'merk' ada
            if (!$merk) {
                // Kirim error jika parameter tidak ada
                return $this->fail('Parameter merk tidak ditemukan.', 400);
            }
        
            try {
                // Gunakan metode 'where' dan 'findAll' yang standar dari Model
                // $this->modelunitmodel adalah properti yang sudah diinisialisasi di __construct
                $data = $this->modelUnitModel
                            ->select('id_model_unit, model_unit')
                            ->where('merk_unit', $merk)
                            ->findAll();
                
                // Kirim data sebagai JSON dengan status 200 OK
                return $this->respond(['data' => $data]);
        
            } catch (\Exception $e) {
                // Jika terjadi error database, catat di log dan kirim error 500
                log_message('error', '[getModelUnitMerk] ' . $e->getMessage());
                return $this->failServerError('Terjadi kesalahan di server saat mengambil data model.');
            }
        }

        public function getDataPOAPI($tipe)
        {
            $draw = $this->request->getPost('draw') ?? 1; // Wajib buat DataTables
            $start = (int)$this->request->getPost('start') ?? 0;
            $length = (int)$this->request->getPost('length') ?? 10;

            // Ambil parameter filter
            $status = $this->request->getPost("status");
            if (!empty($status) && $status != "all") {
                $where["purchase_orders.status"] = $status;
            }

            $supplier = $this->request->getPost("supplier");
            if (!empty($supplier) && $supplier != "all") {
                $where["purchase_orders.supplier_id"] = $supplier;
            }

            $start_date = $this->request->getPost("start_date");
            $where["purchase_orders.tanggal_po >="] = empty($start_date) ? date('Y', strtotime('-1 year')) . "-01-01" : $start_date;

            $end_date = $this->request->getPost("end_date");
            $where["purchase_orders.tanggal_po <="] = empty($end_date) ? date('Y') . "-12-31" : $end_date;

            $tipewhere = $tipe == "unit" ? "Unit" : ($tipe == "sparepart" ? "Sparepart" : "Attachment & Battery");
            $where["purchase_orders.tipe_po"] = $tipewhere;

            // Filter khusus untuk PO Verification: hanya tampilkan PO yang memiliki unit dengan status_verifikasi = 'belum_dicek'
            $useJoinForVerification = ($tipe == "unit");

            // Hitung total semua PO tanpa filter (buat recordsTotal)
            $this->purchasingModel->resetQuery();
            $baseQuery = $this->purchasingModel;
            if ($useJoinForVerification) {
                $baseQuery = $baseQuery
                    ->join('po_units', 'po_units.po_id = purchase_orders.id_po AND po_units.status_verifikasi = "Belum Dicek"', 'inner')
                    ->groupBy('purchase_orders.id_po');
            }
            $recordsTotal = $baseQuery
                ->where($where)
                ->countAllResults(false);

            // 1. Hitung total data setelah difilter
            $this->purchasingModel->resetQuery();
            $filterQuery = $this->purchasingModel;
            if ($useJoinForVerification) {
                $filterQuery = $filterQuery
                    ->join('po_units', 'po_units.po_id = purchase_orders.id_po AND po_units.status_verifikasi = "Belum Dicek"', 'inner')
                    ->groupBy('purchase_orders.id_po');
            }
            $recordsFiltered = $filterQuery
                ->where($where)
                ->countAllResults(false);

            // 2. Ambil data PO
            $totalItemsSubquery = "(SELECT COUNT(id_po_unit) FROM po_units WHERE po_id = purchase_orders.id_po)";
            $sesuaiItemsSubquery = "(SELECT COUNT(id_po_unit) FROM po_units WHERE po_id = purchase_orders.id_po AND status_verifikasi = 'Sesuai')";
            $processedItemsSubquery = "(SELECT COUNT(id_po_unit) FROM po_units WHERE po_id = purchase_orders.id_po AND status_verifikasi != 'Belum Dicek')";
            $rejectedItemsSubquery = "(SELECT COUNT(id_po_unit) FROM po_units WHERE po_id = purchase_orders.id_po AND status_verifikasi = 'Tidak Sesuai')";

            $this->purchasingModel->resetQuery();
            $dataQuery = $this->purchasingModel
                ->select('purchase_orders.id_po, purchase_orders.no_po, DATE_FORMAT(purchase_orders.tanggal_po, "%d-%M-%Y") as tanggal_po, suppliers.nama_supplier, purchase_orders.status, '
                    . $totalItemsSubquery . ' as total_items, '
                    . $sesuaiItemsSubquery . ' as sesuai_items, '
                    . $processedItemsSubquery . ' as processed_items, '
                    . $rejectedItemsSubquery . ' as rejected_items')
                ->join('suppliers', 'purchase_orders.supplier_id = suppliers.id_supplier');
            
            // Tambahkan join untuk filter verification jika diperlukan
            if ($useJoinForVerification) {
                $dataQuery = $dataQuery
                    ->join('po_units', 'po_units.po_id = purchase_orders.id_po AND po_units.status_verifikasi = "Belum Dicek"', 'inner')
                    ->groupBy('purchase_orders.id_po');
            }
            
            $data = $dataQuery
                ->where($where)
                ->limit($length, $start)
                ->findAll();
            
            // echo $this->purchasingModel->getLastQuery();

            // (opsional) hitung stats buat card UI
            $stats = [
                'total' => $recordsTotal,
                'pending' => count(array_filter($data, fn($d) => $d['status'] === 'pending')),
                'selesai_catatan' => count(array_filter($data, fn($d) => $d['status'] === 'Selesai dengan Catatan')),
                'completed' => count(array_filter($data, fn($d) => $d['status'] === 'completed')),
            ];

            return $this->respond([
                'draw' => intval($draw),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'stats' => $stats
            ], 200);
        }
        
        public function getDetailPOAPI($id_po, $api = true)
        {
            // Ambil data PO utama
            $poData = $this->purchaseModel
                ->select('
                    purchase_orders.no_po,
                    DATE_FORMAT(purchase_orders.tanggal_po, "%d-%M-%Y") as tanggal_po,
                    purchase_orders.invoice_no,
                    DATE_FORMAT(purchase_orders.invoice_date, "%d-%M-%Y") as invoice_date,
                    DATE_FORMAT(purchase_orders.bl_date, "%d-%M-%Y") as bl_date,
                    purchase_orders.keterangan_po,
                    purchase_orders.tipe_po,
                    DATE_FORMAT(purchase_orders.created_at, "%d-%M-%Y") as created_at,
                    DATE_FORMAT(purchase_orders.updated_at, "%d-%M-%Y") as updated_at,
                    purchase_orders.status,
                    suppliers.nama_supplier
                ')
                ->join('suppliers', 'suppliers.id_supplier = purchase_orders.supplier_id')
                ->where('purchase_orders.id_po', $id_po)
                ->get()
                ->getRowArray();

            // Ambil detail unit dari PO
            $dataDetail = $this->poUnitsModel
                ->select('
                    po_units.status_penjualan,
                    po_units.status_verifikasi,
                    po_units.serial_number_po,
                    po_units.tahun_po,
                    po_units.sn_mast_po,
                    po_units.sn_mesin_po,
                    po_units.sn_charger_po,
                    po_units.sn_attachment_po,
                    po_units.sn_baterai_po,
                    po_units.keterangan,
                    model_unit.merk_unit,
                    model_unit.model_unit,
                    CONCAT(tipe_unit.tipe, " ", tipe_unit.jenis) AS nama_tipe_unit,
                    kapasitas.kapasitas_unit,
                    tipe_mast.tipe_mast,
                    mesin.merk_mesin,
                    mesin.model_mesin,
                    mesin.bahan_bakar,
                    CONCAT(attachment.tipe, " ", attachment.merk, " ", attachment.model) AS attachment_name,
                    baterai.merk_baterai,
                    baterai.tipe_baterai,
                    baterai.jenis_baterai,
                    charger.merk_charger,
                    charger.tipe_charger,
                    tipe_ban.tipe_ban,
                    jenis_roda.tipe_roda,
                    valve.jumlah_valve,
                    departemen.nama_departemen
                ')
                ->join('departemen', 'departemen.id_departemen = po_units.jenis_unit', 'left')
                ->join('model_unit', 'model_unit.id_model_unit = po_units.model_unit_id', 'left')
                ->join('tipe_unit', 'tipe_unit.id_tipe_unit = po_units.tipe_unit_id', 'left')
                ->join('kapasitas', 'kapasitas.id_kapasitas = po_units.kapasitas_id', 'left')
                ->join('tipe_mast', 'tipe_mast.id_mast = po_units.mast_id', 'left')
                ->join('mesin', 'mesin.id = po_units.mesin_id', 'left')
                ->join('attachment', 'attachment.id_attachment = po_units.attachment_id', 'left')
                ->join('baterai', 'baterai.id = po_units.baterai_id', 'left')
                ->join('charger', 'charger.id_charger = po_units.charger_id', 'left')
                ->join('tipe_ban', 'tipe_ban.id_ban = po_units.ban_id', 'left')
                ->join('jenis_roda', 'jenis_roda.id_roda = po_units.roda_id', 'left')
                ->join('valve', 'valve.id_valve = po_units.valve_id', 'left')
                ->where('po_units.po_id', $id_po)
                ->get()
                ->getResultArray();

            // Gabungin data
            $combinedData = [
                'po' => $poData,
                'details' => $dataDetail
            ];
            if($api){
                return $this->respond($combinedData,200);
            }else{
                return $combinedData;
            }
        }
    //END FUNCTION API

    public function newPoUnit()
    {
        $tipeUnitsAll = $this->tipeUnitModel->get()->getResultArray();
        $tipeList = array_values(array_unique(array_map(fn($r) => $r['tipe'] ?? '', $tipeUnitsAll)));
        $data = [
            'title'         => 'Tambah PO Unit Baru',
            'card_title'    => 'New Purchase Order Unit',
            'mode'          => 'new',
            'id_po'         => null,
            'detail'        => [],
            'suppliers'     => $this->supplierModel->get()->getResultArray(),
            'status_units'  => $this->statusUnitModel->get()->getResultArray(),
            'departemens'   => $this->departemenModel->get()->getResultArray(),
            'tipe_units'    => $tipeUnitsAll,
            'tipe_list'     => $tipeList,
            'merks'         => $this->modelUnitModel->select('merk_unit, MIN(id_model_unit) as id_model_unit')->groupBy('merk_unit')->get()->getResultArray(),
            'kapasitas'     => $this->kapasitasModel->get()->getResultArray(),
            'masts'         => $this->tipeMastModel->get()->getResultArray(),
            'mesins'        => $this->mesinModel->get()->getResultArray(),
            'attachments'   => $this->attachmentModel->get()->getResultArray(),
            'baterais'      => $this->bateraiModel->get()->getResultArray(),
            'chargers'      => $this->chargerModel->get()->getResultArray(),
            'rodas'         => $this->jenisRodaModel->get()->getResultArray(),
            'bans'          => $this->tipeBanModel->get()->getResultArray(),
            'valves'        => $this->valveModel->get()->getResultArray(),
            'validation'    => \Config\Services::validation()
        ];

        return view('purchasing/po_unitForm', $data);
    }

    public function editPoUnit($id_po)
    {
        $poData = $this->purchaseModel->find($id_po);
        $poDetail = $this->poUnitsModel->where('po_id', $id_po)->first();
        $modelUnit = $this->modelUnitModel->where('id_model_unit', $poDetail["merk_unit"])->get()->getRowArray();

        if (!$poData) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $tipeUnitsAll = $this->tipeUnitModel->get()->getResultArray();
        $tipeList = array_values(array_unique(array_map(fn($r) => $r['tipe'] ?? '', $tipeUnitsAll)));
        $data = [
            'title'         => 'Edit PO ' . $poData['no_po'],
            'card_title'    => 'Edit PO ' . $poData['no_po'],
            'po'            => $poData,
            'detail'        => $poDetail,
            'mode'          => 'update',
            'id_po'         => $id_po,
            'qty_unit'      => $this->poUnitsModel->where('po_id', $id_po)->countAllResults(),
            'suppliers'     => $this->supplierModel->get()->getResultArray(),
            'status_units'  => $this->statusUnitModel->get()->getResultArray(),
            'departemens'   => $this->departemenModel->get()->getResultArray(),
            'tipe_units'    => $tipeUnitsAll,
            'tipe_list'     => $tipeList,
            'merks'         => $this->modelUnitModel->select('merk_unit, MIN(id_model_unit) as id_model_unit')->groupBy('merk_unit')->get()->getResultArray(),
            // 'modelsunit'    => $this->modelunitmodel->where('merk_unit',$modelUnit["merk_unit"])->get()->getResultArray(),
            'kapasitas'     => $this->kapasitasModel->get()->getResultArray(),
            'masts'         => $this->tipeMastModel->get()->getResultArray(),
            'mesins'        => $this->mesinModel->get()->getResultArray(),
            'attachments'   => $this->attachmentModel->get()->getResultArray(),
            'baterais'      => $this->bateraiModel->get()->getResultArray(),
            'chargers'      => $this->chargerModel->get()->getResultArray(),
            'rodas'         => $this->jenisRodaModel->get()->getResultArray(),
            'bans'          => $this->tipeBanModel->get()->getResultArray(),
            'valves'        => $this->valveModel->get()->getResultArray(),
            'validation'    => \Config\Services::validation()
        ];

        return view('purchasing/po_unitForm', $data);
    }

    public function index()
    {
        // Check permission
        if (!$this->canAccess('purchasing')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }
        
        // Redirect to purchasing hub
        return $this->purchasingHub();
    }

    /**
     * Main Purchasing Hub - Unified Dashboard
     */
    public function purchasingHub()
    {
        // Check permission
        if (!$this->canAccess('purchasing')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }
        
        // Extract PO ID from URL for auto-opening modal (from notification deep linking)
        $uri = service('uri');
        $autoOpenPoId = null;
        
        // Check if URL matches /purchasing/detail/{id} or /purchasing/po/detail/{id}
        $segments = $uri->getSegments();
        if (count($segments) >= 3 && $segments[1] === 'detail' && is_numeric($segments[2])) {
            $autoOpenPoId = (int)$segments[2];
        } elseif (count($segments) >= 4 && $segments[1] === 'po' && $segments[2] === 'detail' && is_numeric($segments[3])) {
            $autoOpenPoId = (int)$segments[3];
        }
        
        // Get suppliers
        $suppliers = $this->supplierModel->findAll();
        
        // Get stats for each PO type
        $statsUnit = $this->getPOStats('unit');
        $statsAttachment = $this->getPOStats('attachment');
        $statsSparepart = $this->getPOStats('sparepart');
        
        // Get supplier stats
        $supplierStats = $this->getSupplierStats();
        
        // Get data for dropdowns
        $departemens = $this->departemenModel->findAll();
        $merks = $this->modelUnitModel->select('merk_unit, MIN(id_model_unit) as id_model_unit')->groupBy('merk_unit')->findAll();
        $attachments = $this->attachmentModel->findAll();
        $baterais = $this->bateraiModel->findAll();
        $chargers = $this->chargerModel->findAll();
        $spareparts = $this->sparepartModel->findAll();
        
        $data = [
            'title' => 'Purchasing Management | OPTIMA',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/purchasing' => 'PO Unit & Attachment'
            ],
            'suppliers' => $suppliers,
            'statsUnit' => $statsUnit,
            'statsAttachment' => $statsAttachment,
            'statsSparepart' => $statsSparepart,
            'supplierStats' => $supplierStats,
            'dataUnit' => ['suppliers' => $suppliers],
            'dataAttachment' => ['suppliers' => $suppliers],
            'dataSparepart' => ['suppliers' => $suppliers, 'spareparts' => $spareparts],
            'dataSupplier' => [],
            // For modal form
            'departemens' => $departemens,
            'merks' => $merks,
            'attachments' => $attachments,
            'baterais' => $baterais,
            'chargers' => $chargers,
            'spareparts' => $spareparts,
            // For auto-opening modal from notification
            'autoOpenPoId' => $autoOpenPoId
        ];
        
        return view('purchasing/purchasing', $data);
    }

    public function exportPO()
    {
        // Activity Log: EXPORT purchase orders
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'purchase_orders', 0, 'Export Purchase Orders CSV', [
                'module_name' => 'PURCHASING',
                'submenu_item' => 'Purchase Orders',
                'business_impact' => 'LOW'
            ]);
        }
        return view('purchasing/export_po');
    }

    public function exportSupplier()
    {
        // Activity Log: EXPORT suppliers
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'suppliers', 0, 'Export Suppliers CSV', [
                'module_name' => 'PURCHASING',
                'submenu_item' => 'Supplier Management',
                'business_impact' => 'LOW'
            ]);
        }
        return view('purchasing/export_supplier');
    }

    public function exportPOProgres()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.purchasing_progres')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.purchasing_progres');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'purchase_orders', 0, 'Export PO Progres CSV', [
                'module_name' => 'PURCHASING',
                'submenu_item' => 'PO Unit & Attachment - Progres',
                'business_impact' => 'LOW'
            ]);
        }
        return view('purchasing/export_po_progres');
    }

    public function exportPODelivery()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.purchasing_delivery')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.purchasing_delivery');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'purchase_orders', 0, 'Export PO Delivery CSV', [
                'module_name' => 'PURCHASING',
                'submenu_item' => 'PO Unit & Attachment - Delivery',
                'business_impact' => 'LOW'
            ]);
        }
        return view('purchasing/export_po_delivery');
    }

    public function exportPOCompleted()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.purchasing_completed')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.purchasing_completed');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'purchase_orders', 0, 'Export PO Completed CSV', [
                'module_name' => 'PURCHASING',
                'submenu_item' => 'PO Unit & Attachment - Completed',
                'business_impact' => 'LOW'
            ]);
        }
        return view('purchasing/export_po_completed');
    }

    /**
     * Print Purchase Order
     */
    public function printPackingList()
    {
        try {
            $deliveryId = $this->request->getGet('delivery_id');
            if (!$deliveryId) {
                throw new \Exception('Delivery ID tidak ditemukan');
            }

            // Get delivery data
            $db = \Config\Database::connect();
            // Use purchase_orders and suppliers tables
            $delivery = $db->table('po_deliveries pd')
                ->select('
                    pd.*,
                    po.no_po,
                    s.nama_supplier
                ')
                ->join('purchase_orders po', 'po.id_po = pd.po_id', 'left')
                ->join('suppliers s', 's.id_supplier = po.supplier_id', 'left')
                ->where('pd.id_delivery', $deliveryId)
                ->get()
                ->getRowArray();

            if (!$delivery) {
                throw new \Exception('Delivery tidak ditemukan');
            }

            // Get packing list data
            $packingList = [
                'packing_list_no' => $delivery['packing_list_no'] ?? '-',
                'delivery_date' => $delivery['delivery_date'] ?? $delivery['expected_date'] ?? date('Y-m-d'),
                'driver_name' => $delivery['driver_name'] ?? $delivery['driver'] ?? '-',
                'status' => $delivery['status'] ?? 'PENDING'
            ];

            // Get delivery items with full specifications
            // Each item from po_delivery_items represents one individual item with assigned SN
            $items = [];
            
            // Try to get from po_delivery_items first (if exists) - each row is one item with SN
            // Use same query structure as getPODetail to ensure consistency
            if ($db->tableExists('po_delivery_items')) {
                try {
                    // Use EXACT same query as getPODetail to ensure consistency
                    $deliveryItems = $db->table('po_delivery_items pdi')
                        ->select('
                            pdi.*,
                            pdi.item_name,
                            pdi.item_type,
                            pdi.serial_number,
                            pdi.qty,
                            "Belum Dicek" as status_verifikasi
                        ')
                        ->where('pdi.delivery_id', $deliveryId)
                        ->orderBy('pdi.id_delivery_item', 'ASC')
                        ->get()
                        ->getResultArray();
                    
                    log_message('debug', 'Print Packing List - po_delivery_items found: ' . count($deliveryItems));
                    
                    if (!empty($deliveryItems)) {
                        // For each delivery item (individual item with SN), get full specifications
                        foreach ($deliveryItems as $deliveryItem) {
                            // Debug: Log raw data first
                            log_message('debug', 'Print Packing List - Raw Delivery Item: ' . json_encode($deliveryItem));
                            
                            // Normalize item_type (handle case sensitivity - database might use lowercase)
                            $itemTypeRaw = $deliveryItem['item_type'] ?? 'unit';
                            $itemType = ucfirst(strtolower($itemTypeRaw)); // Normalize to 'Unit', 'Attachment', etc.
                            $itemName = $deliveryItem['item_name'] ?? '';
                            
                            // Get SN - use same method as getPODetail (direct access)
                            $serialNumber = isset($deliveryItem['serial_number']) ? trim((string)$deliveryItem['serial_number']) : '';
                            
                            // Debug logging for SN - log all delivery item data
                            log_message('debug', 'Print Packing List - Item: ' . $itemName . ', Type (raw): ' . $itemTypeRaw . ', Type (normalized): ' . $itemType . ', SN from po_delivery_items: [' . $serialNumber . ']');
                            log_message('debug', 'Print Packing List - All columns: ' . implode(', ', array_keys($deliveryItem)));
                            log_message('debug', 'Print Packing List - serial_number value: ' . var_export($deliveryItem['serial_number'] ?? 'NOT SET', true));
                            
                            // Get full specifications based on item type
                            if ($itemType === 'Unit' && !empty($delivery['po_id'])) {
                                // Get unit specs from po_units with complete details
                                // SAME QUERY STRUCTURE AS whVerification
                                $unitSpec = $db->table('po_units pu')
                                    ->select('
                                        pu.*,
                                        pu.merk_unit as brand_name_po,
                                        mu.model_unit,
                                        mu.merk_unit as brand_from_model_table,
                                        tu.tipe as jenis_unit,
                                        tu.tipe as tipe_unit,
                                        pu.tahun_po as tahun_unit,
                                        d.nama_departemen,
                                        k.kapasitas_unit,
                                        tm.tipe_mast,
                                        m.merk_mesin,
                                        m.model_mesin,
                                        tb.tipe_ban,
                                        jr.tipe_roda,
                                        v.jumlah_valve,
                                        pu.keterangan
                                    ')
                                    ->join('model_unit mu', 'mu.id_model_unit = pu.model_unit_id', 'left')
                                    ->join('tipe_unit tu', 'tu.id_tipe_unit = pu.tipe_unit_id', 'left')
                                    ->join('departemen d', 'd.id_departemen = tu.id_departemen', 'left')
                                    ->join('kapasitas k', 'k.id_kapasitas = pu.kapasitas_id', 'left')
                                    ->join('tipe_mast tm', 'tm.id_mast = pu.mast_id', 'left')
                                    ->join('mesin m', 'm.id = pu.mesin_id', 'left')
                                    ->join('tipe_ban tb', 'tb.id_ban = pu.ban_id', 'left')
                                    ->join('jenis_roda jr', 'jr.id_roda = pu.roda_id', 'left')
                                    ->join('valve v', 'v.id_valve = pu.valve_id', 'left')
                                    ->where('pu.po_id', $delivery['po_id']);
                                
                                // Get first unit from PO (matching will be done by item_name from po_delivery_items)
                                $unitSpec = $unitSpec->limit(1)->get()->getRowArray();
                                
                                // If no match found, get first unit from PO
                                if (!$unitSpec) {
                                    $unitSpec = $db->table('po_units pu')
                                        ->select('
                                            pu.*,
                                            pu.merk_unit as brand_name_po,
                                            mu.model_unit,
                                            mu.merk_unit as brand_from_model_table,
                                            tu.tipe as jenis_unit,
                                            tu.tipe as tipe_unit,
                                            pu.tahun_po as tahun_unit,
                                            d.nama_departemen,
                                            k.kapasitas_unit,
                                            tm.tipe_mast,
                                            m.merk_mesin,
                                            m.model_mesin,
                                            tb.tipe_ban,
                                            jr.tipe_roda,
                                            v.jumlah_valve,
                                            pu.keterangan
                                        ')
                                        ->join('model_unit mu', 'mu.id_model_unit = pu.model_unit_id', 'left')
                                        ->join('tipe_unit tu', 'tu.id_tipe_unit = pu.tipe_unit_id', 'left')
                                        ->join('departemen d', 'd.id_departemen = tu.id_departemen', 'left')
                                        ->join('kapasitas k', 'k.id_kapasitas = pu.kapasitas_id', 'left')
                                        ->join('tipe_mast tm', 'tm.id_mast = pu.mast_id', 'left')
                                        ->join('mesin m', 'm.id = pu.mesin_id', 'left')
                                        ->join('tipe_ban tb', 'tb.id_ban = pu.ban_id', 'left')
                                        ->join('jenis_roda jr', 'jr.id_roda = pu.roda_id', 'left')
                                        ->join('valve v', 'v.id_valve = pu.valve_id', 'left')
                                        ->where('pu.po_id', $delivery['po_id'])
                                        ->limit(1)
                                        ->get()
                                        ->getRowArray();
                                }
                                
                                if ($unitSpec) {
                                    // Use brand_name_po from po_units (VARCHAR), fallback to brand_from_model_table
                                    $brandValue = $unitSpec['brand_name_po'] ?? $unitSpec['brand_from_model_table'] ?? '-';
                                    
                                    $items[] = [
                                        'item_type' => 'Unit',
                                        'item_name' => $itemName ?: trim($brandValue . ' ' . ($unitSpec['model_unit'] ?? '')),
                                        'serial_number' => $serialNumber, // Use SN from delivery (even if empty, don't fallback to PO SN)
                                        'merk_unit' => $brandValue,
                                        'model_unit' => $unitSpec['model_unit'] ?? '-',
                                        'jenis_unit' => $unitSpec['jenis_unit'] ?? '-',
                                        'tipe_unit' => $unitSpec['tipe_unit'] ?? '-',
                                        'tahun_unit' => $unitSpec['tahun_unit'] ?? '-',
                                        'nama_departemen' => $unitSpec['nama_departemen'] ?? '-',
                                        'kapasitas_unit' => $unitSpec['kapasitas_unit'] ?? '-',
                                        'tipe_mast' => $unitSpec['tipe_mast'] ?? '-',
                                        'merk_mesin' => $unitSpec['merk_mesin'] ?? '-',
                                        'model_mesin' => $unitSpec['model_mesin'] ?? '-',
                                        'tipe_ban' => $unitSpec['tipe_ban'] ?? '-',
                                        'tipe_roda' => $unitSpec['tipe_roda'] ?? '-',
                                        'jumlah_valve' => $unitSpec['jumlah_valve'] ?? '-',
                                        'keterangan' => $unitSpec['keterangan'] ?? '-',
                                        'qty' => 1 // Each delivery item is one unit
                                    ];
                                }
                            } elseif (($itemType === 'Attachment' || $itemTypeRaw === 'attachment') && !empty($delivery['po_id'])) {
                                // Get attachment specs from po_attachment with complete details
                                $attachmentSpec = $db->table('po_attachment pa')
                                    ->select('
                                        pa.*,
                                        a.merk as merk_attachment,
                                        a.model as model_attachment,
                                        a.tipe as tipe_attachment
                                    ')
                                    ->join('attachment a', 'a.id_attachment = pa.attachment_id', 'left')
                                    ->where('pa.po_id', $delivery['po_id'])
                                    ->where('pa.item_type', 'Attachment');
                                
                                // Get first attachment from PO (matching will be done by item_name from po_delivery_items)
                                $attachmentSpec = $attachmentSpec->limit(1)->get()->getRowArray();
                                
                                // If no match found, get first attachment from PO
                                if (!$attachmentSpec) {
                                    $attachmentSpec = $db->table('po_attachment pa')
                                        ->select('
                                            pa.*,
                                            a.merk as merk_attachment,
                                            a.model as model_attachment,
                                            a.tipe as tipe_attachment
                                        ')
                                        ->join('attachment a', 'a.id_attachment = pa.attachment_id', 'left')
                                        ->where('pa.po_id', $delivery['po_id'])
                                        ->where('pa.item_type', 'Attachment')
                                        ->limit(1)
                                        ->get()
                                        ->getRowArray();
                                }
                                
                                if ($attachmentSpec) {
                                    $items[] = [
                                        'item_type' => 'Attachment',
                                        'item_name' => $itemName ?: trim(($attachmentSpec['tipe_attachment'] ?? '') . ' ' . ($attachmentSpec['merk_attachment'] ?? '') . ' ' . ($attachmentSpec['model_attachment'] ?? '')),
                                        'serial_number' => $serialNumber, // Use SN from delivery (even if empty, don't fallback to PO SN)
                                        'merk_attachment' => $attachmentSpec['merk_attachment'] ?? '-',
                                        'model_attachment' => $attachmentSpec['model_attachment'] ?? '-',
                                        'tipe_attachment' => $attachmentSpec['tipe_attachment'] ?? '-',
                                        'keterangan' => $attachmentSpec['keterangan'] ?? '-',
                                        'qty' => 1 // Each delivery item is one unit
                                    ];
                                }
                            } elseif (($itemType === 'Battery' || $itemTypeRaw === 'battery') && !empty($delivery['po_id'])) {
                                // Get battery specs from po_attachment with complete details
                                $batterySpec = $db->table('po_attachment pa')
                                    ->select('
                                        pa.*,
                                        b.merk_baterai,
                                        b.tipe_baterai,
                                        b.jenis_baterai
                                    ')
                                    ->join('baterai b', 'b.id = pa.baterai_id', 'left')
                                    ->where('pa.po_id', $delivery['po_id'])
                                    ->where('pa.item_type', 'Battery');
                                
                                // Get first battery from PO (matching will be done by item_name from po_delivery_items)
                                $batterySpec = $batterySpec->limit(1)->get()->getRowArray();
                                
                                // If no match found, get first battery from PO
                                if (!$batterySpec) {
                                    $batterySpec = $db->table('po_attachment pa')
                                        ->select('
                                            pa.*,
                                            b.merk_baterai,
                                            b.tipe_baterai,
                                            b.jenis_baterai
                                        ')
                                        ->join('baterai b', 'b.id = pa.baterai_id', 'left')
                                        ->where('pa.po_id', $delivery['po_id'])
                                        ->where('pa.item_type', 'Battery')
                                        ->limit(1)
                                        ->get()
                                        ->getRowArray();
                                }
                                
                                if ($batterySpec) {
                                    $items[] = [
                                        'item_type' => 'Battery',
                                        'item_name' => $itemName ?: trim(($batterySpec['merk_baterai'] ?? '') . ' ' . ($batterySpec['tipe_baterai'] ?? '') . ' ' . ($batterySpec['jenis_baterai'] ?? '')),
                                        'serial_number' => $serialNumber, // Use SN from delivery (even if empty, don't fallback to PO SN)
                                        'merk_baterai' => $batterySpec['merk_baterai'] ?? '-',
                                        'tipe_baterai' => $batterySpec['tipe_baterai'] ?? '-',
                                        'jenis_baterai' => $batterySpec['jenis_baterai'] ?? '-',
                                        'keterangan' => $batterySpec['keterangan'] ?? '-',
                                        'qty' => 1 // Each delivery item is one unit
                                    ];
                                }
                            } elseif (($itemType === 'Charger' || $itemTypeRaw === 'charger') && !empty($delivery['po_id'])) {
                                // Get charger specs from po_attachment with complete details
                                $chargerSpec = $db->table('po_attachment pa')
                                    ->select('
                                        pa.*,
                                        c.merk_charger,
                                        c.tipe_charger
                                    ')
                                    ->join('charger c', 'c.id_charger = pa.charger_id', 'left')
                                    ->where('pa.po_id', $delivery['po_id'])
                                    ->where('pa.item_type', 'Charger');
                                
                                // Get first charger from PO (matching will be done by item_name from po_delivery_items)
                                $chargerSpec = $chargerSpec->limit(1)->get()->getRowArray();
                                
                                // If no match found, get first charger from PO
                                if (!$chargerSpec) {
                                    $chargerSpec = $db->table('po_attachment pa')
                                        ->select('
                                            pa.*,
                                            c.merk_charger,
                                            c.tipe_charger
                                        ')
                                        ->join('charger c', 'c.id_charger = pa.charger_id', 'left')
                                        ->where('pa.po_id', $delivery['po_id'])
                                        ->where('pa.item_type', 'Charger')
                                        ->limit(1)
                                        ->get()
                                        ->getRowArray();
                                }
                                
                                if ($chargerSpec) {
                                    $items[] = [
                                        'item_type' => 'Charger',
                                        'item_name' => $itemName ?: trim(($chargerSpec['merk_charger'] ?? '') . ' ' . ($chargerSpec['tipe_charger'] ?? '')),
                                        'serial_number' => $serialNumber, // Use SN from delivery (even if empty, don't fallback to PO SN)
                                        'merk_charger' => $chargerSpec['merk_charger'] ?? '-',
                                        'tipe_charger' => $chargerSpec['tipe_charger'] ?? '-',
                                        'keterangan' => $chargerSpec['keterangan'] ?? '-',
                                        'qty' => 1 // Each delivery item is one unit
                                    ];
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    log_message('debug', 'po_delivery_items query failed: ' . $e->getMessage());
                }
            }
            
            // Fallback: If items exist but SN is empty, try to get from serial_numbers JSON
            // Also check if po_delivery_items exists but SN is empty
            if (!empty($items)) {
                // Check if any items have empty SN, try to get from serial_numbers JSON
                foreach ($items as $index => $item) {
                    if (empty($item['serial_number']) && !empty($delivery['serial_numbers'])) {
                        try {
                            $serialData = json_decode($delivery['serial_numbers'], true);
                            if (is_array($serialData)) {
                                // Try to find matching item in serial_numbers JSON
                                foreach ($serialData as $serialItem) {
                                    $serialItemType = ucfirst(strtolower($serialItem['type'] ?? 'Unit'));
                                    $serialItemName = trim($serialItem['item_name'] ?? '');
                                    $itemNameMatch = trim($item['item_name'] ?? '');
                                    
                                    if ($serialItemType === $item['item_type'] && 
                                        ($serialItemName === $itemNameMatch || empty($itemNameMatch))) {
                                        $items[$index]['serial_number'] = trim($serialItem['serial_number'] ?? '');
                                        log_message('debug', 'Print Packing List - Found SN from serial_numbers JSON for: ' . $itemNameMatch . ' = [' . $items[$index]['serial_number'] . ']');
                                        break;
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            log_message('debug', 'serial_numbers JSON parsing failed for item: ' . $e->getMessage());
                        }
                    }
                }
            }
            
            // Fallback: If no items from po_delivery_items, parse from serial_numbers JSON
            if (empty($items) && !empty($delivery['serial_numbers'])) {
                try {
                    $serialData = json_decode($delivery['serial_numbers'], true);
                    if (is_array($serialData)) {
                        log_message('debug', 'Print Packing List - Parsing serial_numbers JSON: ' . count($serialData) . ' items');
                        
                        foreach ($serialData as $serialItem) {
                            $itemType = ucfirst(strtolower($serialItem['type'] ?? 'Unit'));
                            $itemName = $serialItem['item_name'] ?? '';
                            $serialNumber = trim($serialItem['serial_number'] ?? '');
                            
                            // Get full specifications based on item type (same as above)
                            if ($itemType === 'Unit' && !empty($delivery['po_id'])) {
                                // SAME QUERY STRUCTURE AS whVerification
                                $unitSpec = $db->table('po_units pu')
                                    ->select('
                                        pu.*,
                                        pu.merk_unit as brand_name_po,
                                        mu.model_unit,
                                        mu.merk_unit as brand_from_model_table,
                                        tu.tipe as jenis_unit,
                                        tu.tipe as tipe_unit,
                                        pu.tahun_po as tahun_unit,
                                        d.nama_departemen,
                                        k.kapasitas_unit,
                                        tm.tipe_mast,
                                        m.merk_mesin,
                                        m.model_mesin,
                                        tb.tipe_ban,
                                        jr.tipe_roda,
                                        v.jumlah_valve,
                                        pu.keterangan
                                    ')
                                    ->join('model_unit mu', 'mu.id_model_unit = pu.model_unit_id', 'left')
                                    ->join('tipe_unit tu', 'tu.id_tipe_unit = pu.tipe_unit_id', 'left')
                                    ->join('departemen d', 'd.id_departemen = tu.id_departemen', 'left')
                                    ->join('kapasitas k', 'k.id_kapasitas = pu.kapasitas_id', 'left')
                                    ->join('tipe_mast tm', 'tm.id_mast = pu.mast_id', 'left')
                                    ->join('mesin m', 'm.id = pu.mesin_id', 'left')
                                    ->join('tipe_ban tb', 'tb.id_ban = pu.ban_id', 'left')
                                    ->join('jenis_roda jr', 'jr.id_roda = pu.roda_id', 'left')
                                    ->join('valve v', 'v.id_valve = pu.valve_id', 'left')
                                    ->where('pu.po_id', $delivery['po_id'])
                                    ->limit(1)
                                    ->get()
                                    ->getRowArray();
                                
                                if ($unitSpec) {
                                    // Use brand_name_po from po_units (VARCHAR), fallback to brand_from_model_table
                                    $brandValue = $unitSpec['brand_name_po'] ?? $unitSpec['brand_from_model_table'] ?? '-';
                                    
                                    $items[] = [
                                        'item_type' => 'Unit',
                                        'item_name' => $itemName ?: trim($brandValue . ' ' . ($unitSpec['model_unit'] ?? '')),
                                        'serial_number' => $serialNumber,
                                        'merk_unit' => $brandValue,
                                        'model_unit' => $unitSpec['model_unit'] ?? '-',
                                        'jenis_unit' => $unitSpec['jenis_unit'] ?? '-',
                                        'tipe_unit' => $unitSpec['tipe_unit'] ?? '-',
                                        'tahun_unit' => $unitSpec['tahun_unit'] ?? '-',
                                        'nama_departemen' => $unitSpec['nama_departemen'] ?? '-',
                                        'kapasitas_unit' => $unitSpec['kapasitas_unit'] ?? '-',
                                        'tipe_mast' => $unitSpec['tipe_mast'] ?? '-',
                                        'merk_mesin' => $unitSpec['merk_mesin'] ?? '-',
                                        'model_mesin' => $unitSpec['model_mesin'] ?? '-',
                                        'tipe_ban' => $unitSpec['tipe_ban'] ?? '-',
                                        'tipe_roda' => $unitSpec['tipe_roda'] ?? '-',
                                        'jumlah_valve' => $unitSpec['jumlah_valve'] ?? '-',
                                        'keterangan' => $unitSpec['keterangan'] ?? '-',
                                        'qty' => 1
                                    ];
                                }
                            } elseif ($itemType === 'Attachment' && !empty($delivery['po_id'])) {
                                $attachmentSpec = $db->table('po_attachment pa')
                                    ->select('
                                        pa.*,
                                        a.merk as merk_attachment,
                                        a.model as model_attachment,
                                        a.tipe as tipe_attachment
                                    ')
                                    ->join('attachment a', 'a.id_attachment = pa.attachment_id', 'left')
                                    ->where('pa.po_id', $delivery['po_id'])
                                    ->where('pa.item_type', 'Attachment')
                                    ->limit(1)
                                    ->get()
                                    ->getRowArray();
                                
                                if ($attachmentSpec) {
                                    $items[] = [
                                        'item_type' => 'Attachment',
                                        'item_name' => $itemName ?: trim(($attachmentSpec['tipe_attachment'] ?? '') . ' ' . ($attachmentSpec['merk_attachment'] ?? '') . ' ' . ($attachmentSpec['model_attachment'] ?? '')),
                                        'serial_number' => $serialNumber,
                                        'merk_attachment' => $attachmentSpec['merk_attachment'] ?? '-',
                                        'model_attachment' => $attachmentSpec['model_attachment'] ?? '-',
                                        'tipe_attachment' => $attachmentSpec['tipe_attachment'] ?? '-',
                                        'keterangan' => $attachmentSpec['keterangan'] ?? '-',
                                        'qty' => 1
                                    ];
                                }
                            } elseif ($itemType === 'Battery' && !empty($delivery['po_id'])) {
                                $batterySpec = $db->table('po_attachment pa')
                                    ->select('
                                        pa.*,
                                        b.merk_baterai,
                                        b.tipe_baterai,
                                        b.jenis_baterai
                                    ')
                                    ->join('baterai b', 'b.id = pa.baterai_id', 'left')
                                    ->where('pa.po_id', $delivery['po_id'])
                                    ->where('pa.item_type', 'Battery')
                                    ->limit(1)
                                    ->get()
                                    ->getRowArray();
                                
                                if ($batterySpec) {
                                    $items[] = [
                                        'item_type' => 'Battery',
                                        'item_name' => $itemName ?: trim(($batterySpec['merk_baterai'] ?? '') . ' ' . ($batterySpec['tipe_baterai'] ?? '') . ' ' . ($batterySpec['jenis_baterai'] ?? '')),
                                        'serial_number' => $serialNumber,
                                        'merk_baterai' => $batterySpec['merk_baterai'] ?? '-',
                                        'tipe_baterai' => $batterySpec['tipe_baterai'] ?? '-',
                                        'jenis_baterai' => $batterySpec['jenis_baterai'] ?? '-',
                                        'keterangan' => $batterySpec['keterangan'] ?? '-',
                                        'qty' => 1
                                    ];
                                }
                            } elseif ($itemType === 'Charger' && !empty($delivery['po_id'])) {
                                $chargerSpec = $db->table('po_attachment pa')
                                    ->select('
                                        pa.*,
                                        c.merk_charger,
                                        c.tipe_charger
                                    ')
                                    ->join('charger c', 'c.id_charger = pa.charger_id', 'left')
                                    ->where('pa.po_id', $delivery['po_id'])
                                    ->where('pa.item_type', 'Charger')
                                    ->limit(1)
                                    ->get()
                                    ->getRowArray();
                                
                                if ($chargerSpec) {
                                    $items[] = [
                                        'item_type' => 'Charger',
                                        'item_name' => $itemName ?: trim(($chargerSpec['merk_charger'] ?? '') . ' ' . ($chargerSpec['tipe_charger'] ?? '')),
                                        'serial_number' => $serialNumber,
                                        'merk_charger' => $chargerSpec['merk_charger'] ?? '-',
                                        'tipe_charger' => $chargerSpec['tipe_charger'] ?? '-',
                                        'keterangan' => $chargerSpec['keterangan'] ?? '-',
                                        'qty' => 1
                                    ];
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    log_message('debug', 'serial_numbers JSON parsing failed: ' . $e->getMessage());
                }
            }

            // Debug logging
            log_message('debug', 'Print Packing List - Delivery ID: ' . $deliveryId);
            log_message('debug', 'Print Packing List - PO ID: ' . ($delivery['po_id'] ?? 'N/A'));
            log_message('debug', 'Print Packing List - Items count: ' . count($items));
            
            // Log SN for each item
            foreach ($items as $idx => $item) {
                log_message('debug', 'Print Packing List - Item #' . ($idx + 1) . ': ' . ($item['item_name'] ?? 'N/A') . ' (Type: ' . ($item['item_type'] ?? 'N/A') . '), SN: [' . ($item['serial_number'] ?? 'EMPTY') . ']');
            }
            
            if (empty($items)) {
                log_message('debug', 'Print Packing List - No items found, checking po_delivery_items table exists: ' . ($db->tableExists('po_delivery_items') ? 'YES' : 'NO'));
                if (!empty($delivery['serial_numbers'])) {
                    log_message('debug', 'Print Packing List - serial_numbers JSON exists: ' . substr($delivery['serial_numbers'], 0, 200));
                }
            }

            $data = [
                'delivery' => $delivery,
                'packingList' => $packingList,
                'items' => $items
            ];

            return view('purchasing/print_packing_list', $data);

        } catch (\Exception $e) {
            log_message('error', 'Print Packing List Error: ' . $e->getMessage());
            log_message('error', 'Print Packing List Stack Trace: ' . $e->getTraceAsString());
            
            // Return error page instead of JSON for better debugging
            $errorMessage = 'Gagal mencetak Packing List: ' . $e->getMessage();
            if (ENVIRONMENT === 'development') {
                $errorMessage .= '<br><br>Stack Trace:<br><pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
            
            return $this->failServerError($errorMessage);
        }
    }

    public function printPO($poId)
    {
        try {
            // Get PO data
            $po = $this->purchasingModel->find($poId);
            if (!$po) {
                throw new \Exception('Purchase Order tidak ditemukan');
            }

            // Get supplier data
            $supplier = $this->supplierModel->find($po['supplier_id']);

            // Get PO items with complete specifications (same as getPODetail)
            $db = \Config\Database::connect();
            $items = [];
            
            // Get Unit items with complete specifications
            $unitItems = $db->table('po_units pu')
                ->select('
                    pu.*,
                    "Unit" as item_type,
                    1 as qty_ordered,
                    1 as qty_received,
                    pu.status_verifikasi,
                    pu.keterangan as catatan_verifikasi,
                    CONCAT(mu.merk_unit, " ", mu.model_unit) as item_name,
                    pu.serial_number_po as serial_number,
                    pu.keterangan,
                    mu.merk_unit,
                    mu.model_unit,
                    tu.tipe as jenis_unit,
                    d.nama_departemen,
                    k.kapasitas_unit,
                    tm.tipe_mast,
                    m.merk_mesin,
                    tb.tipe_ban,
                    jr.tipe_roda,
                    v.jumlah_valve,
                    pu.tahun_po
                ')
                ->join('model_unit mu', 'mu.id_model_unit = pu.model_unit_id', 'left')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = pu.tipe_unit_id', 'left')
                ->join('departemen d', 'd.id_departemen = tu.id_departemen', 'left')
                ->join('kapasitas k', 'k.id_kapasitas = pu.kapasitas_id', 'left')
                ->join('tipe_mast tm', 'tm.id_mast = pu.mast_id', 'left')
                ->join('mesin m', 'm.id = pu.mesin_id', 'left')
                ->join('tipe_ban tb', 'tb.id_ban = pu.ban_id', 'left')
                ->join('jenis_roda jr', 'jr.id_roda = pu.roda_id', 'left')
                ->join('valve v', 'v.id_valve = pu.valve_id', 'left')
                ->where('pu.po_id', $poId)
                ->get()
                ->getResultArray();
            
            // Get Attachment items with complete specifications
            $attachmentItems = $db->table('po_attachment pa')
                ->select('
                    pa.*,
                    pa.item_type,
                    pa.qty_ordered,
                    pa.qty_received,
                    pa.status_verifikasi,
                    pa.catatan_verifikasi,
                    pa.serial_number,
                    pa.keterangan,
                    CONCAT(a.tipe, " ", a.merk, " ", a.model) as item_name,
                    a.merk as merk_attachment,
                    a.model as model_attachment,
                    a.tipe as tipe_attachment
                ')
                ->join('attachment a', 'a.id_attachment = pa.attachment_id', 'left')
                ->where('pa.po_id', $poId)
                ->where('pa.item_type', 'Attachment')
                ->get()
                ->getResultArray();
            
            // Get Battery items
            $batteryItems = $db->table('po_attachment pa')
                ->select('
                    pa.*,
                    pa.item_type,
                    pa.qty_ordered,
                    pa.qty_received,
                    pa.status_verifikasi,
                    pa.catatan_verifikasi,
                    pa.serial_number,
                    pa.keterangan,
                    CONCAT(b.merk_baterai, " ", b.tipe_baterai, " ", b.jenis_baterai) as item_name,
                    b.merk_baterai,
                    b.tipe_baterai,
                    b.jenis_baterai
                ')
                ->join('baterai b', 'b.id = pa.baterai_id', 'left')
                ->where('pa.po_id', $poId)
                ->where('pa.item_type', 'Battery')
                ->get()
                ->getResultArray();
            
            // Get Charger items
            $chargerItems = $db->table('po_attachment pa')
                ->select('
                    pa.*,
                    pa.item_type,
                    pa.qty_ordered,
                    pa.qty_received,
                    pa.status_verifikasi,
                    pa.catatan_verifikasi,
                    pa.serial_number,
                    pa.keterangan,
                    CONCAT(c.merk_charger, " ", c.tipe_charger) as item_name,
                    c.merk_charger,
                    c.tipe_charger
                ')
                ->join('charger c', 'c.id_charger = pa.charger_id', 'left')
                ->where('pa.po_id', $poId)
                ->where('pa.item_type', 'Charger')
                ->get()
                ->getResultArray();
            
            // Combine all items
            $items = array_merge($unitItems, $attachmentItems, $batteryItems, $chargerItems);

            // Get delivery data
            $deliveries = [];
            if (isset($this->poDeliveryModel)) {
                $deliveries = $this->poDeliveryModel->where('po_id', $poId)->findAll();
            }

            // Add packing list data to each item
            foreach ($items as &$item) {
                $item['packing_lists'] = [];
                
                // Get packing lists for this specific item
                if (!empty($deliveries)) {
                    foreach ($deliveries as $delivery) {
                        // Get delivery items from po_delivery_items table
                        $db = \Config\Database::connect();
                        $deliveryItems = $db->table('po_delivery_items pdi')
                            ->select('pdi.*, pdi.item_name, pdi.item_type, pdi.serial_number, pdi.qty')
                            ->where('pdi.delivery_id', $delivery['id_delivery'])
                            ->get()
                            ->getResultArray();
                        
                        foreach ($deliveryItems as $deliveryItem) {
                            // Check if this delivery item matches our current item
                            $itemNameMatch = ($deliveryItem['item_name'] ?? '') === ($item['item_name'] ?? '');
                            $itemTypeMatch = ($deliveryItem['item_type'] ?? '') === ($item['item_type'] ?? '');
                            
                            if ($itemTypeMatch && $itemNameMatch) {
                                $item['packing_lists'][] = [
                                    'packing_list_no' => $delivery['packing_list_no'] ?? '-',
                                    'delivery_date' => $delivery['expected_date'] ?? $delivery['actual_date'] ?? null,
                                    'driver_name' => $delivery['driver_name'] ?? '-',
                                    'qty' => $deliveryItem['qty'] ?? 1,
                                    'serial_numbers' => $deliveryItem['serial_number'] ?? '-'
                                ];
                            }
                        }
                    }
                }
            }

            $data = [
                'po' => $po,
                'supplier' => $supplier,
                'items' => $items,
                'deliveries' => $deliveries
            ];

            return view('purchasing/print_po', $data);

        } catch (\Exception $e) {
            log_message('error', 'Print PO Error: ' . $e->getMessage());
            return $this->failServerError('Gagal mencetak Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Get unified PO data for DataTable
     */
    public function getUnifiedPOData()
    {
        $draw = $this->request->getPost('draw') ?? 1;
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $search = $this->request->getPost('search');
        $searchValue = is_array($search) && isset($search['value']) ? $search['value'] : '';
        
        // Global filters
        $po_type = $this->request->getPost('po_type'); // NEW: filter by PO type
        $tab_type = $this->request->getPost('tab_type'); // NEW: filter by tab (progres/completed)
        $status = $this->request->getPost('status');
        $supplier = $this->request->getPost('supplier');
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        
        // Get database connection
        $db = \Config\Database::connect();
        
        // Build query
        $builder = $db->table('purchase_orders po')
            ->select('
                po.id_po,
                po.no_po,
                po.tanggal_po,
                po.supplier_id,
                s.nama_supplier,
                po.tipe_po,
                po.status,
                po.total_unit,
                po.total_attachment,
                po.total_battery,
                po.total_charger,
                COUNT(DISTINCT pd.id_delivery) as total_deliveries,
                COUNT(DISTINCT CASE WHEN pd.status = "Received" THEN pd.id_delivery END) as completed_deliveries,
                (po.total_unit + po.total_attachment + po.total_battery + po.total_charger) as total_qty_ordered,
                (SELECT COALESCE(SUM(pdi.qty), 0) FROM po_delivery_items pdi 
                 LEFT JOIN po_deliveries pd2 ON pdi.delivery_id = pd2.id_delivery 
                 WHERE pd2.po_id = po.id_po AND pd2.status = "Received") as total_qty_received,
                (SELECT COALESCE(SUM(pdi.qty), 0) FROM po_delivery_items pdi 
                 LEFT JOIN po_deliveries pd2 ON pdi.delivery_id = pd2.id_delivery 
                 WHERE pd2.po_id = po.id_po) as total_qty_scheduled,
                (SELECT COALESCE(COUNT(*), 0) FROM po_units pu 
                 WHERE pu.po_id = po.id_po AND pu.status_verifikasi = "Sesuai") + 
                (SELECT COALESCE(COUNT(*), 0) FROM po_attachment pa 
                 WHERE pa.po_id = po.id_po AND pa.status_verifikasi = "Sesuai") as total_qty_verified,
                CASE 
                    WHEN (SELECT COALESCE(SUM(pdi.qty), 0) FROM po_delivery_items pdi 
                          LEFT JOIN po_deliveries pd2 ON pdi.delivery_id = pd2.id_delivery 
                          WHERE pd2.po_id = po.id_po AND pd2.status = "Received") = 0 THEN "Not Started"
                    WHEN (SELECT COALESCE(SUM(pdi.qty), 0) FROM po_delivery_items pdi 
                          LEFT JOIN po_deliveries pd2 ON pdi.delivery_id = pd2.id_delivery 
                          WHERE pd2.po_id = po.id_po AND pd2.status = "Received") < (po.total_unit + po.total_attachment + po.total_battery + po.total_charger) THEN "Partial"
                    WHEN (SELECT COALESCE(SUM(pdi.qty), 0) FROM po_delivery_items pdi 
                          LEFT JOIN po_deliveries pd2 ON pdi.delivery_id = pd2.id_delivery 
                          WHERE pd2.po_id = po.id_po AND pd2.status = "Received") = (po.total_unit + po.total_attachment + po.total_battery + po.total_charger) THEN "Complete"
                    ELSE "Over Delivered"
                END as delivery_status
            ')
            ->join('suppliers s', 's.id_supplier = po.supplier_id', 'left')
            ->join('po_deliveries pd', 'pd.po_id = po.id_po', 'left')
            ->groupBy('po.id_po, po.no_po, po.tanggal_po, po.supplier_id, s.nama_supplier, po.tipe_po, po.status, po.total_unit, po.total_attachment, po.total_battery, po.total_charger');
        
        // Apply po_type filter (exclude Sparepart from Unit & Attachment view)
        if ($po_type === 'unit_attachment') {
            $builder->whereIn('po.tipe_po', ['Unit', 'Attachment & Battery', 'Dinamis']);
        } else if ($po_type === 'sparepart') {
            $builder->where('po.tipe_po', 'Sparepart');
        }
        
        // Apply tab filter (progres/completed)
        if ($tab_type === 'completed') {
            $builder->where('po.status', 'completed');
        } else if ($tab_type === 'progres') {
            $builder->where('po.status !=', 'completed');
        }
        
        // Apply filters
        if (!empty($status) && $status !== 'all') {
            $builder->where('po.status', $status);
        }
        
        if (!empty($supplier) && $supplier !== 'all') {
            $builder->where('po.supplier_id', $supplier);
        }
        
        if (!empty($start_date)) {
            $builder->where('po.tanggal_po >=', $start_date);
        } else {
            // Default range: Previous year to Current year
            $builder->where('po.tanggal_po >=', date('Y', strtotime('-1 year')) . '-01-01');
        }
        
        if (!empty($end_date)) {
            $builder->where('po.tanggal_po <=', $end_date);
        } else {
            $builder->where('po.tanggal_po <=', date('Y') . '-12-31');
        }
        
        // Search
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('po.no_po', $searchValue)
                ->orLike('s.nama_supplier', $searchValue)
                ->orLike('po.status', $searchValue)
                ->groupEnd();
        }
        
        // Get total records
        $totalRecords = $builder->countAllResults(false);
        
        // Get filtered records
        $filteredRecords = $builder->countAllResults(false);
        
        // Get data
        $data = $builder->orderBy('po.tanggal_po', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();
        
        return $this->respond([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Store unified PO with items and delivery schedule
     */
    public function storeUnifiedPO()
    {
        // Check permission for creating PO
        if (!$this->hasPermission('purchasing.po_unit_attachment.create')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: You do not have permission to create Purchase Orders'
            ])->setStatusCode(403);
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // First, get items to determine tipe_po
            $itemsJson = $this->request->getPost('items_json');
            $items = json_decode($itemsJson, true);
            
            if (!$items || !is_array($items)) {
                throw new \Exception('Items data is invalid or empty');
            }
            
            // Auto-detect tipe_po based on item types
            $tipePo = $this->detectPOTipe($items);
            
            // Format dates properly
            $tanggalPo = $this->request->getPost('tanggal_po');
            $invoiceDate = $this->request->getPost('invoice_date');
            $blDate = $this->request->getPost('bl_date');
            
            // Convert date format if needed
            if (!empty($tanggalPo)) {
                $tanggalPo = date('Y-m-d', strtotime($tanggalPo));
            }
            if (!empty($invoiceDate)) {
                $invoiceDate = date('Y-m-d', strtotime($invoiceDate));
            }
            if (!empty($blDate)) {
                $blDate = date('Y-m-d', strtotime($blDate));
            }
            
            $poData = [
                'no_po' => $this->request->getPost('no_po'),
                'tanggal_po' => $tanggalPo,
                'supplier_id' => $this->request->getPost('id_supplier'),
                'tipe_po' => $tipePo, // Auto-detected based on items
                'status' => 'pending', // Fixed: lowercase as required by validation
                'invoice_no' => $this->request->getPost('invoice_no') ?: null,
                'invoice_date' => $invoiceDate ?: null,
                'bl_date' => $blDate ?: null,
                'keterangan_po' => $this->request->getPost('keterangan_po') ?: null
            ];
            
            // Validate required fields
            if (empty($poData['no_po'])) {
                throw new \Exception('Nomor PO harus diisi');
            }
            if (empty($poData['tanggal_po'])) {
                throw new \Exception('Tanggal PO harus diisi');
            }
            if (empty($poData['supplier_id'])) {
                throw new \Exception('Supplier harus dipilih');
            }
            
            // Debug logging for PO data
            log_message('info', '[storeUnifiedPO] PO Data to insert: ' . json_encode($poData));
            log_message('info', '[storeUnifiedPO] All POST data: ' . json_encode($this->request->getPost()));
            
            // Test with minimal data first - ensure no id_po field
            $testData = [
                'no_po' => $poData['no_po'],
                'tanggal_po' => $poData['tanggal_po'],
                'supplier_id' => $poData['supplier_id'],
                'tipe_po' => $poData['tipe_po'],
                'status' => $poData['status']
            ];
            
            // Remove any potential id_po field that might cause auto-increment issues
            unset($testData['id_po']);
            unset($poData['id_po']);
            
            log_message('info', '[storeUnifiedPO] Test data (minimal): ' . json_encode($testData));
            
            try {
                // Use direct database insert to avoid model issues
                log_message('info', '[storeUnifiedPO] Attempting direct database insert');
                $db = \Config\Database::connect();
                $builder = $db->table('purchase_orders');
                
                // Ensure we don't include id_po in the insert
                $insertData = $testData;
                unset($insertData['id_po']);
                
                log_message('info', '[storeUnifiedPO] Insert data (final): ' . json_encode($insertData));
                
                $result = $builder->insert($insertData);
                
                if ($result) {
                    $poId = $db->insertID();
                    log_message('info', '[storeUnifiedPO] Direct insert successful with ID: ' . $poId);
                } else {
                    $error = $db->error();
                    log_message('error', '[storeUnifiedPO] Direct insert failed: ' . json_encode($error));
                    throw new \Exception('Database insert failed: ' . $error['message']);
                }
                
            } catch (\Exception $e) {
                log_message('error', '[storeUnifiedPO] Exception during insert: ' . $e->getMessage());
                log_message('error', '[storeUnifiedPO] Exception trace: ' . $e->getTraceAsString());
                throw $e;
            }
            
            if (!$poId) {
                throw new \Exception('Failed to create PO record');
            }
            
            // Debug logging
            log_message('info', '[storeUnifiedPO] Items JSON: ' . $itemsJson);
            log_message('info', '[storeUnifiedPO] Decoded items: ' . json_encode($items));
            log_message('info', '[storeUnifiedPO] Detected tipe_po: ' . $tipePo);
            
            // Process items - totals will be auto-calculated by database triggers
            foreach ($items as $item) {
                // Debug logging for each item
                log_message('info', '[storeUnifiedPO] Processing item: ' . json_encode($item));
                
                // Extract item details based on type
                $itemType = $item['item_type'];
                $qty = intval($item['qty'] ?? 1);
                
                // Build item name from display data
                $itemName = $this->buildItemName($item);
                
                // Get item ID based on type
                $itemId = $this->getItemId($item);
                
                $itemData = [
                    'po_id' => $poId,
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'item_name' => $itemName,
                    'qty_ordered' => $qty,
                    'qty_received' => 0,
                    'harga_satuan' => 0, // Will be updated later
                    'total_harga' => 0, // Will be updated later
                    'keterangan' => $item['keterangan'] ?? null,
                    'status_verifikasi' => 'Belum Dicek'
                ];
                
                // Insert based on item type (database triggers will auto-update totals)
                $insertResult = false;
                if ($itemType === 'unit') {
                    $insertResult = $this->insertUnitItem($poId, $item, $itemData);
                    log_message('info', '[storeUnifiedPO] Unit insert result: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));
                } elseif ($itemType === 'attachment') {
                    $insertResult = $this->insertAttachmentItem($poId, $item, $itemData);
                    log_message('info', '[storeUnifiedPO] Attachment insert result: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));
                } elseif ($itemType === 'battery') {
                    $insertResult = $this->insertBatteryItem($poId, $item, $itemData);
                    log_message('info', '[storeUnifiedPO] Battery insert result: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));
                } elseif ($itemType === 'charger') {
                    $insertResult = $this->insertChargerItem($poId, $item, $itemData);
                    log_message('info', '[storeUnifiedPO] Charger insert result: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));
                }
                
                if (!$insertResult) {
                    throw new \Exception("Failed to insert {$itemType} item");
                }
            }
            
            // Delivery schedule will be created later when needed
            
            // Note: Totals are automatically calculated by database triggers
            // After all items are inserted, triggers have already updated:
            // - total_unit, total_attachment, total_battery, total_charger in purchase_orders
            log_message('info', '[storeUnifiedPO] All items inserted. Totals auto-calculated by database triggers.');
            
            $db->transComplete();
            
            if ($db->transStatus()) {
                // Send notification
                try {
                    helper('notification');
                    
                    // Get supplier name
                    $supplierQuery = $db->query('SELECT nama_supplier FROM suppliers WHERE id_supplier = ?', [$poData['supplier_id']]);
                    $supplier = $supplierQuery->getRow();
                    $supplierName = $supplier ? $supplier->nama_supplier : 'Unknown Supplier';
                    
                    // Calculate total amount (rough estimate from items)
                    $totalAmount = 0; // TODO: Calculate from actual items if needed
                    
                    notify_po_created([
                        'id' => $poId,
                        'po_number' => $poData['no_po'],
                        'supplier_name' => $supplierName,
                        'po_type' => $poData['tipe_po'],
                        'total_amount' => $totalAmount,
                        'delivery_date' => $poData['tanggal_po'],
                        'created_by' => session()->get('user_name') ?? 'System',
                        'url' => base_url('/purchasing/po-detail/' . $poId)
                    ]);
                    
                    log_message('info', "PO created: {$poData['no_po']} - Notification sent");
                } catch (\Exception $notifError) {
                    log_message('error', 'Failed to send PO notification: ' . $notifError->getMessage());
                }
                
                // Return JSON response instead of redirect
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Purchase Order berhasil dibuat dengan nomor: ' . $poData['no_po'],
                    'po_number' => $poData['no_po']
                ]);
            } else {
                throw new \Exception('Transaction failed');
            }
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[storeUnifiedPO] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan Purchase Order: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Build item name from display data
     */
    private function buildItemName($item)
    {
        if (!isset($item['_display'])) {
            return $item['item_type'] . ' Item';
        }
        
        $display = $item['_display'];
        
        switch ($item['item_type']) {
            case 'unit':
                return sprintf('%s %s | %s - %s | %s | Tahun %s (%s)',
                    $display['merk_text'] ?? '',
                    $display['model_text'] ?? '',
                    $display['departemen_text'] ?? '',
                    $display['jenis_text'] ?? '',
                    $display['kapasitas_text'] ?? '',
                    $item['tahun_unit'] ?? '',
                    $display['kondisi_text'] ?? ''
                );
                
            case 'attachment':
                return sprintf('%s | %s - %s',
                    $display['tipe_text'] ?? '',
                    $display['merk_text'] ?? '',
                    $display['model_text'] ?? ''
                );
                
            case 'battery':
                return sprintf('%s | %s - %s',
                    $display['jenis_text'] ?? '',
                    $display['merk_text'] ?? '',
                    $display['tipe_text'] ?? ''
                );
                
            case 'charger':
                return sprintf('%s - %s',
                    $display['merk_text'] ?? '',
                    $display['model_text'] ?? ''
                );
                
            default:
                return $item['item_type'] . ' Item';
        }
    }

    /**
     * Get item ID based on type
     */
    private function getItemId($item)
    {
        switch ($item['item_type']) {
            case 'unit':
                return $item['model_unit_id'] ?? 0;
            case 'attachment':
                return $item['attachment_id'] ?? 0;
            case 'battery':
                return $item['baterai_id'] ?? 0;
            case 'charger':
                return $item['charger_id'] ?? 0;
            default:
                return 0;
        }
    }

    /**
     * Insert unit item to po_units table
     * Creates multiple rows based on qty
     */
    private function insertUnitItem($poId, $item, $itemData)
    {
        $qty = intval($item['qty'] ?? 1);
        $successCount = 0;
        
        log_message('info', '[storeUnifiedPO] Inserting ' . $qty . ' unit items');
        
        // Create base data structure
        $unitDataTemplate = [
                        'po_id' => $poId,
            'jenis_unit' => $item['tipe_unit_id'] ?? null,
            'tipe_unit_id' => $item['tipe_unit_id'] ?? null,
            'merk_unit' => $item['merk_unit'] ?? null,
            'model_unit_id' => $item['model_unit_id'] ?? null,
            'tahun_po' => $item['tahun_unit'] ?? null,
            'kapasitas_id' => $item['kapasitas_id'] ?? null,
            'status_penjualan' => $item['kondisi_penjualan'] ?? null,
            'mast_id' => $item['mast_id'] ?? null,
            'sn_mast_po' => $item['sn_mast'] ?? null,
            'mesin_id' => $item['mesin_id'] ?? null,
            'sn_mesin_po' => $item['sn_mesin'] ?? null,
            'ban_id' => $item['ban_id'] ?? null,
            'roda_id' => $item['roda_id'] ?? null,
            'valve_id' => $item['valve_id'] ?? null,
            'status_verifikasi' => $itemData['status_verifikasi'],
            'keterangan' => $itemData['keterangan']
        ];
        
        // Insert multiple rows based on qty
        for ($i = 0; $i < $qty; $i++) {
            $unitData = $unitDataTemplate;
            unset($unitData['id_po_unit']);
            
            try {
                $result = $this->poUnitsModel->insert($unitData);
                if ($result) {
                    $successCount++;
                    log_message('info', "[storeUnifiedPO] Unit #{$i} inserted with ID: {$result}");
                } else {
                    $errors = $this->poUnitsModel->errors();
                    log_message('error', "[storeUnifiedPO] Unit #{$i} model insert failed: " . json_encode($errors));
                    
                    // Try direct insert as fallback
                    $db = \Config\Database::connect();
                    $builder = $db->table('po_units');
                    if ($builder->insert($unitData)) {
                        $successCount++;
                        log_message('info', "[storeUnifiedPO] Unit #{$i} direct insert successful");
                    }
                }
            } catch (\Exception $e) {
                log_message('error', "[storeUnifiedPO] Unit #{$i} exception: " . $e->getMessage());
            }
        }
        
        log_message('info', "[storeUnifiedPO] Successfully inserted {$successCount}/{$qty} unit items");
        return $successCount > 0 ? $successCount : false;
    }

    /**
     * Insert attachment item to po_attachment table
     * Creates multiple rows based on qty
     */
    private function insertAttachmentItem($poId, $item, $itemData)
    {
        $qty = intval($item['qty'] ?? 1);
        $successCount = 0;
        
        log_message('info', '[storeUnifiedPO] Inserting ' . $qty . ' attachment items');
        
        $attachmentDataTemplate = [
            'po_id' => $poId,
            'item_type' => 'attachment',
            'attachment_id' => $item['attachment_id'] ?? null,
            'serial_number' => $item['serial_number'] ?? null,
            'status_verifikasi' => $itemData['status_verifikasi'],
            'keterangan' => $itemData['keterangan']
        ];
        
        // Insert multiple rows based on qty
        for ($i = 0; $i < $qty; $i++) {
            $attachmentData = $attachmentDataTemplate;
            unset($attachmentData['id_po_item']);
            
            try {
                $result = $this->poItemsModel->insert($attachmentData);
                if ($result) {
                    $successCount++;
                    log_message('info', "[storeUnifiedPO] Attachment #{$i} inserted with ID: {$result}");
                } else {
                    $errors = $this->poItemsModel->errors();
                    log_message('error', "[storeUnifiedPO] Attachment #{$i} insert failed: " . json_encode($errors));
                }
            } catch (\Exception $e) {
                log_message('error', "[storeUnifiedPO] Attachment #{$i} exception: " . $e->getMessage());
            }
        }
        
        log_message('info', "[storeUnifiedPO] Successfully inserted {$successCount}/{$qty} attachment items");
        return $successCount > 0 ? $successCount : false;
    }

    /**
     * Insert battery item to po_attachment table
     * Creates multiple rows based on qty
     */
    private function insertBatteryItem($poId, $item, $itemData)
    {
        $qty = intval($item['qty'] ?? 1);
        $successCount = 0;
        
        log_message('info', '[storeUnifiedPO] Inserting ' . $qty . ' battery items');
        
        $batteryDataTemplate = [
            'po_id' => $poId,
            'item_type' => 'battery',
            'baterai_id' => $item['baterai_id'] ?? null,
            'serial_number' => $item['serial_number'] ?? null,
            'status_verifikasi' => $itemData['status_verifikasi'],
            'keterangan' => $itemData['keterangan']
        ];
        
        // Insert multiple rows based on qty
        for ($i = 0; $i < $qty; $i++) {
            $batteryData = $batteryDataTemplate;
            unset($batteryData['id_po_item']);
            
            try {
                $result = $this->poItemsModel->insert($batteryData);
                if ($result) {
                    $successCount++;
                    log_message('info', "[storeUnifiedPO] Battery #{$i} inserted with ID: {$result}");
            } else {
                    $errors = $this->poItemsModel->errors();
                    log_message('error', "[storeUnifiedPO] Battery #{$i} insert failed: " . json_encode($errors));
                }
            } catch (\Exception $e) {
                log_message('error', "[storeUnifiedPO] Battery #{$i} exception: " . $e->getMessage());
            }
        }
        
        log_message('info', "[storeUnifiedPO] Successfully inserted {$successCount}/{$qty} battery items");
        return $successCount > 0 ? $successCount : false;
    }

    /**
     * Insert charger item to po_attachment table
     * Creates multiple rows based on qty
     */
    private function insertChargerItem($poId, $item, $itemData)
    {
        $qty = intval($item['qty'] ?? 1);
        $successCount = 0;
        
        log_message('info', '[storeUnifiedPO] Inserting ' . $qty . ' charger items');
        
        $chargerDataTemplate = [
            'po_id' => $poId,
            'item_type' => 'charger',
            'charger_id' => $item['charger_id'] ?? null,
            'serial_number' => $item['serial_number'] ?? null,
            'status_verifikasi' => $itemData['status_verifikasi'],
            'keterangan' => $itemData['keterangan']
        ];
        
        // Insert multiple rows based on qty
        for ($i = 0; $i < $qty; $i++) {
            $chargerData = $chargerDataTemplate;
            unset($chargerData['id_po_item']);
            
            try {
                $result = $this->poItemsModel->insert($chargerData);
                if ($result) {
                    $successCount++;
                    log_message('info', "[storeUnifiedPO] Charger #{$i} inserted with ID: {$result}");
                } else {
                    $errors = $this->poItemsModel->errors();
                    log_message('error', "[storeUnifiedPO] Charger #{$i} insert failed: " . json_encode($errors));
                }
        } catch (\Exception $e) {
                log_message('error', "[storeUnifiedPO] Charger #{$i} exception: " . $e->getMessage());
            }
        }
        
        log_message('info', "[storeUnifiedPO] Successfully inserted {$successCount}/{$qty} charger items");
        return $successCount > 0 ? $successCount : false;
    }

    /**
     * Auto-detect PO type based on item types
     */
    private function detectPOTipe($items)
    {
        $itemTypes = [];
        foreach ($items as $item) {
            $itemTypes[] = $item['item_type'];
        }
        
        $uniqueTypes = array_unique($itemTypes);
        $typeCount = count($uniqueTypes);
        
        log_message('info', '[detectPOTipe] Item types found: ' . json_encode($uniqueTypes));
        
        // Logic based on database analysis:
        if ($typeCount == 1) {
            // Single type
            switch ($uniqueTypes[0]) {
                case 'unit':
                    return 'Unit';
                case 'attachment':
                case 'battery':
                case 'charger':
                    return 'Attachment & Battery';
                case 'sparepart':
                    return 'Sparepart';
                default:
                    return 'Unit';
            }
        } else {
            // Multiple types - use 'Dinamis' for mixed types
            return 'Dinamis';
        }
    }

    /**
     * View PO details with delivery tracking
     */
    public function viewPO($poId)
    {
        $po = $this->purchaseModel->find($poId);
        if (!$po) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('PO not found');
        }
        
        $supplier = $this->supplierModel->find($po['supplier_id']);
        $items = $this->poItemsModel->where('po_id', $poId)->findAll();
        $deliveries = $this->poDeliveryModel->getDeliveriesByPO($poId);
        
        // Get delivery items for each delivery
        foreach ($deliveries as &$delivery) {
            $delivery['items'] = $this->deliveryItemModel->getDeliveryItemsWithDetails($delivery['id_delivery']);
        }
        
        $data = [
            'title' => 'PO Details | OPTIMA',
            'po' => $po,
            'supplier' => $supplier,
            'items' => $items,
            'deliveries' => $deliveries
        ];
        
        return view('purchasing/po_details', $data);
    }

    /**
     * Update delivery status (Old version - removed to prevent duplication)
     * See the new comprehensive version in the Delivery Workflow API section
     */

    /**
     * Verify delivery items
     */
    public function verifyDeliveryItems()
    {
        $deliveryId = $this->request->getPost('delivery_id');
        $items = $this->request->getPost('items');
        $verifiedBy = session()->get('username') ?? 'Unknown';
        
        $result = $this->deliveryItemModel->verifyDeliveryItems($deliveryId, $items, $verifiedBy);
        
        if ($result) {
            return $this->respond([
                'success' => true,
                'message' => 'Delivery items verified successfully'
            ]);
        } else {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to verify delivery items'
            ], 500);
        }
    }

    /**
     * Generate PO number
     */
    private function generatePONumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastPO = $this->purchaseModel->select('no_po')
            ->like('no_po', "PO-{$year}{$month}")
            ->orderBy('no_po', 'DESC')
            ->first();
        
        if ($lastPO) {
            preg_match('/PO-' . $year . $month . '-(\d+)/', $lastPO['no_po'], $matches);
            $sequence = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        } else {
            $sequence = 1;
        }
        
        return "PO-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get item form based on type
     */
    /**
     * Show create unified PO form (Unit & Attachment)
     */
    public function createUnifiedPO()
    {
        $data = [
            'suppliers' => $this->supplierModel->findAll(),
            'departemens' => $this->departemenModel->findAll(),
            'tipeUnits' => $this->tipeUnitModel->findAll(),
            'kapasitas' => $this->kapasitasModel->findAll(),
            'merks' => $this->modelUnitModel->select('merk_unit, MIN(id_model_unit) as id_model_unit')->groupBy('merk_unit')->findAll(),
            'attachments' => $this->attachmentModel->findAll(),
            'baterais' => $this->bateraiModel->findAll(),
            'chargers' => $this->chargerModel->findAll(),
            'masts' => $this->tipeMastModel->findAll(),
            'mesins' => $this->mesinModel->findAll(),
            'bans' => $this->tipeBanModel->findAll(),
            'rodas' => $this->jenisRodaModel->findAll(),
            'valves' => $this->valveModel->findAll(),
        ];
        
        return view('purchasing/create_po_unified', $data);
    }


    /**
     * Show PO Sparepart list page
     */
    public function poSparepartList()
    {
        // Redirect to unified purchasing page
        return redirect()->to('/purchasing');
    }

    /**
     * Show create PO Sparepart form
     */
    public function createPOSparepart()
    {
        $data = [
            'suppliers' => $this->supplierModel->findAll(),
            'spareparts' => $this->sparepartModel->findAll(),
        ];
        
        return view('purchasing/create_po_sparepart', $data);
    }

    /**
     * Show Supplier Management page
     */
    public function supplierManagementPage()
    {
        $data = [
            'title' => 'Supplier Management',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/purchasing/supplier_management' => 'Supplier Management'
            ],
            'suppliers' => $this->supplierModel->findAll(),
            'supplierStats' => $this->getSupplierStatsForPage(),
        ];
        
        return view('purchasing/supplier_management', $data);
    }

    /**
     * Get PO Sparepart statistics
     */
    private function getPOSparepartStats()
    {
        $db = \Config\Database::connect();
        
        $total = $db->table('purchase_orders')
            ->where('tipe_po', 'Sparepart')
            ->countAllResults();
            
        $pending = $db->table('purchase_orders')
            ->where('tipe_po', 'Sparepart')
            ->where('status', 'Pending')
            ->countAllResults();
            
        $inProgress = $db->table('purchase_orders')
            ->where('tipe_po', 'Sparepart')
            ->where('status', 'In Progress')
            ->countAllResults();
            
        $completed = $db->table('purchase_orders')
            ->where('tipe_po', 'Sparepart')
            ->where('status', 'Completed')
            ->countAllResults();
        
        return [
            'total' => $total,
            'pending' => $pending,
            'in_progress' => $inProgress,
            'completed' => $completed
        ];
    }

    /**
     * Get Supplier statistics for page
     */
    private function getSupplierStatsForPage()
    {
        $db = \Config\Database::connect();
        
        $total = $this->supplierModel->countAll();
        
        $active = $db->table('suppliers')
            ->where('status', 'Active')
            ->countAllResults();
            
        $verified = $db->table('suppliers')
            ->where('is_verified', 1)
            ->countAllResults();
            
        $totalPO = $db->table('purchase_orders')->countAllResults();
        
        return [
            'total' => $total,
            'active' => $active,
            'verified' => $verified,
            'total_po' => $totalPO
        ];
    }

    public function getItemForm($type)
    {
        $data = [];
        
        switch ($type) {
            case 'unit':
                $data['departemens'] = $this->departemenModel->findAll();
                $data['tipeUnits'] = $this->tipeUnitModel->findAll();
                $data['merks'] = $this->modelUnitModel->select('merk_unit, MIN(id_model_unit) as id_model_unit')->groupBy('merk_unit')->findAll();
                $data['kapasitas'] = $this->kapasitasModel->findAll();
                $data['masts'] = $this->tipeMastModel->findAll();
                $data['mesins'] = $this->mesinModel->findAll();
                $data['bans'] = $this->tipeBanModel->findAll();
                $data['rodas'] = $this->jenisRodaModel->findAll();
                $data['valves'] = $this->valveModel->findAll();
                $data['baterais'] = $this->bateraiModel->findAll();
                return view('purchasing/forms/unit_form_fragment', $data);
                
            case 'attachment':
                $data['attachments'] = $this->attachmentModel->findAll();
                return view('purchasing/forms/attachment_form_fragment', $data);
                
            case 'battery':
                $data['baterais'] = $this->bateraiModel->findAll();
                return view('purchasing/forms/battery_form_fragment', $data);
                
            case 'charger':
                $data['chargers'] = $this->chargerModel->findAll();
                return view('purchasing/forms/charger_form_fragment', $data);
                
            default:
                return '<div class="alert alert-danger">Invalid item type</div>';
        }
    }

    /**
     * Get model units by merk
     */
    public function getModelUnits()
    {
        $merk = $this->request->getGet('merk');
        
        if (empty($merk)) {
            return $this->respond(['success' => false, 'data' => []]);
        }
        
        $models = $this->modelUnitModel->where('merk_unit', $merk)->findAll();
        
        return $this->respond(['success' => true, 'data' => $models]);
    }

    /**
     * Get tipe units based on departemen
     */
    public function getTipeUnits($departemenId)
    {
        try {
            $tipeUnits = $this->tipeUnitModel->where('id_departemen', $departemenId)->findAll();
            return $this->respond(['success' => true, 'data' => $tipeUnits]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get jenis units based on tipe
     */
    public function getJenisUnits($tipeId)
    {
        try {
            $tipeUnit = $this->tipeUnitModel->find($tipeId);
            if (!$tipeUnit) {
                return $this->respond(['success' => false, 'message' => 'Tipe unit not found'], 404);
            }
            
            // Return the jenis from the same tipe unit record
            return $this->respond(['success' => true, 'data' => [
                ['id_tipe_unit' => $tipeUnit['id_tipe_unit'], 'jenis' => $tipeUnit['jenis']]
            ]]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get attachment merks based on tipe
     */
    public function getAttachmentMerks()
    {
        $tipe = $this->request->getGet('tipe');
        
        if (!$tipe) {
            return $this->respond(['success' => false, 'message' => 'Tipe is required'], 400);
        }
        
        try {
            $attachments = $this->attachmentModel->where('tipe', $tipe)->findAll();
            $merks = [];
            $seenMerks = [];
            
            foreach ($attachments as $attachment) {
                if (!in_array($attachment['merk'], $seenMerks)) {
                    $merks[] = [
                        'id' => $attachment['id_attachment'],
                        'merk_attachment' => $attachment['merk']
                    ];
                    $seenMerks[] = $attachment['merk'];
                }
            }
            
            return $this->respond(['success' => true, 'data' => $merks]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get attachment models based on merk
     */
    public function getAttachmentModels()
    {
        $merkId = $this->request->getGet('merk_id');
        $tipe = $this->request->getGet('tipe');
        $merk = $this->request->getGet('merk');
        
        try {
            // Support both ID-based and name-based queries
            if ($merkId) {
                // Old method: using merk_id
            $attachment = $this->attachmentModel->find($merkId);
            if (!$attachment) {
                return $this->respond(['success' => false, 'message' => 'Attachment not found'], 404);
            }
            $models = $this->attachmentModel->where('merk', $attachment['merk'])->findAll();
            } else if ($tipe && $merk) {
                // New method: using tipe and merk names
                $models = $this->attachmentModel
                    ->where('tipe', $tipe)
                    ->where('merk', $merk)
                    ->findAll();
            } else {
                return $this->respond(['success' => false, 'message' => 'Either merk_id or (tipe + merk) is required'], 400);
            }
            
            return $this->respond(['success' => true, 'data' => $models]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get battery merks based on tipe
     */
    public function getBatteryMerks()
    {
        // Support both 'tipe' and 'jenis' parameters
        $jenis = $this->request->getGet('jenis') ?: $this->request->getGet('tipe');
        
        if (!$jenis) {
            return $this->respond(['success' => false, 'message' => 'Jenis/Tipe is required'], 400);
        }
        
        try {
            $baterais = $this->bateraiModel->where('jenis_baterai', $jenis)->findAll();
            $merks = [];
            $seenMerks = [];
            
            foreach ($baterais as $baterai) {
                if (!in_array($baterai['merk_baterai'], $seenMerks)) {
                    $merks[] = [
                        'id' => $baterai['id'],
                        'merk_baterai' => $baterai['merk_baterai']
                    ];
                    $seenMerks[] = $baterai['merk_baterai'];
                }
            }
            
            return $this->respond(['success' => true, 'data' => $merks]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get battery jenis based on merk
     */
    public function getBatteryJenis()
    {
        $merkId = $this->request->getGet('merk_id');
        
        if (!$merkId) {
            return $this->respond(['success' => false, 'message' => 'Merk ID is required'], 400);
        }
        
        try {
            $baterai = $this->bateraiModel->find($merkId);
            if (!$baterai) {
                return $this->respond(['success' => false, 'message' => 'Battery not found'], 404);
            }
            
            // Get all jenis for this merk
            $baterais = $this->bateraiModel->where('merk_baterai', $baterai['merk_baterai'])->findAll();
            
            return $this->respond(['success' => true, 'data' => $baterais]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get charger merks based on tipe
     */
    public function getChargerMerks()
    {
        $tipe = $this->request->getGet('tipe');
        
        if (!$tipe) {
            return $this->respond(['success' => false, 'message' => 'Tipe is required'], 400);
        }
        
        try {
            $chargers = $this->chargerModel->where('tipe_charger', $tipe)->findAll();
            $merks = [];
            $seenMerks = [];
            
            foreach ($chargers as $charger) {
                if (!in_array($charger['merk_charger'], $seenMerks)) {
                    $merks[] = [
                        'id' => $charger['id_charger'],
                        'merk_charger' => $charger['merk_charger']
                    ];
                    $seenMerks[] = $charger['merk_charger'];
                }
            }
            
            return $this->respond(['success' => true, 'data' => $merks]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get charger models based on merk
     */
    public function getChargerModels()
    {
        $merkId = $this->request->getGet('merk_id');
        $merk = $this->request->getGet('merk');
        
        try {
            // Support both ID-based and name-based queries
            if ($merkId) {
                // Old method: using merk_id
            $charger = $this->chargerModel->find($merkId);
            if (!$charger) {
                return $this->respond(['success' => false, 'message' => 'Charger not found'], 404);
            }
            $chargers = $this->chargerModel->where('merk_charger', $charger['merk_charger'])->findAll();
            } else if ($merk) {
                // New method: using merk name
                $chargers = $this->chargerModel->where('merk_charger', $merk)->findAll();
            } else {
                return $this->respond(['success' => false, 'message' => 'Either merk_id or merk is required'], 400);
            }
            
            // Format data to include model_charger as tipe_charger
            $models = [];
            foreach ($chargers as $chr) {
                $models[] = [
                    'id_charger' => $chr['id_charger'],
                    'model_charger' => $chr['tipe_charger'] ?: $chr['model_charger'] ?: 'Standard'
                ];
            }
            
            return $this->respond(['success' => true, 'data' => $models]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get battery tipes based on jenis and merk (NEW API for simplified cascading)
     */
    public function getBatteryTipes()
    {
        $jenis = $this->request->getGet('jenis');
        $merk = $this->request->getGet('merk');
        
        if (!$jenis || !$merk) {
            return $this->respond(['success' => false, 'message' => 'Jenis and Merk are required'], 400);
        }
        
        try {
            $batteries = $this->bateraiModel
                ->where('jenis_baterai', $jenis)
                ->where('merk_baterai', $merk)
                ->findAll();
            
            return $this->respond(['success' => true, 'data' => $batteries]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get PO detail for modal - COMPREHENSIVE VERSION
     */
    public function getPODetail($poId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Get PO with supplier info
            $po = $db->table('purchase_orders po')
                ->select('po.*, suppliers.nama_supplier')
                ->join('suppliers', 'suppliers.id_supplier = po.supplier_id', 'left')
                ->where('po.id_po', $poId)
                ->get()
                ->getRowArray();
                
            if (!$po) {
                return $this->respond(['success' => false, 'message' => 'PO not found'], 404);
            }
            
            // Get items from all tables (units, attachments, batteries, chargers)
            $items = [];
            
            // Get Unit items with complete specifications
            $unitItems = $db->table('po_units pu')
                ->select('
                    pu.*,
                    "Unit" as item_type,
                    1 as qty_ordered,
                    1 as qty_received,
                    pu.status_verifikasi,
                    pu.keterangan as catatan_verifikasi,
                    CONCAT(mu.merk_unit, " ", mu.model_unit) as item_name,
                    pu.serial_number_po as serial_number,
                    pu.keterangan,
                    mu.merk_unit,
                    mu.model_unit,
                    tu.tipe as jenis_unit,
                    d.nama_departemen,
                    k.kapasitas_unit,
                    tm.tipe_mast,
                    m.merk_mesin,
                    tb.tipe_ban,
                    jr.tipe_roda,
                    v.jumlah_valve
                ')
                ->join('model_unit mu', 'mu.id_model_unit = pu.model_unit_id', 'left')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = pu.tipe_unit_id', 'left')
                ->join('departemen d', 'd.id_departemen = tu.id_departemen', 'left')
                ->join('kapasitas k', 'k.id_kapasitas = pu.kapasitas_id', 'left')
                ->join('tipe_mast tm', 'tm.id_mast = pu.mast_id', 'left')
                ->join('mesin m', 'm.id = pu.mesin_id', 'left')
                ->join('tipe_ban tb', 'tb.id_ban = pu.ban_id', 'left')
                ->join('jenis_roda jr', 'jr.id_roda = pu.roda_id', 'left')
                ->join('valve v', 'v.id_valve = pu.valve_id', 'left')
                ->where('pu.po_id', $poId)
                ->get()
                ->getResultArray();
            
            // Get Attachment items with complete specifications
            $attachmentItems = $db->table('po_attachment pa')
                ->select('
                    pa.*,
                    pa.item_type,
                    pa.qty_ordered,
                    pa.qty_received,
                    pa.status_verifikasi,
                    pa.catatan_verifikasi,
                    pa.serial_number,
                    pa.keterangan,
                    CONCAT(a.tipe, " ", a.merk, " ", a.model) as item_name,
                    a.merk as merk_attachment,
                    a.model as model_attachment,
                    a.tipe as tipe_attachment
                ')
                ->join('attachment a', 'a.id_attachment = pa.attachment_id', 'left')
                ->where('pa.po_id', $poId)
                ->where('pa.item_type', 'Attachment')
                ->get()
                ->getResultArray();
            
            // Get Battery items
            $batteryItems = $db->table('po_attachment pa')
                ->select('
                    pa.*,
                    pa.item_type,
                    pa.qty_ordered,
                    pa.qty_received,
                    pa.status_verifikasi,
                    pa.catatan_verifikasi,
                    pa.serial_number,
                    pa.keterangan,
                    CONCAT(b.merk_baterai, " ", b.tipe_baterai, " ", b.jenis_baterai) as item_name,
                    b.merk_baterai,
                    b.tipe_baterai,
                    b.jenis_baterai
                ')
                ->join('baterai b', 'b.id = pa.baterai_id', 'left')
                ->where('pa.po_id', $poId)
                ->where('pa.item_type', 'Battery')
                ->get()
                ->getResultArray();
            
            // Get Charger items
            $chargerItems = $db->table('po_attachment pa')
                ->select('
                    pa.*,
                    pa.item_type,
                    pa.qty_ordered,
                    pa.qty_received,
                    pa.status_verifikasi,
                    pa.catatan_verifikasi,
                    pa.serial_number,
                    pa.keterangan,
                    CONCAT(c.merk_charger, " ", c.tipe_charger) as item_name,
                    c.merk_charger,
                    c.tipe_charger
                ')
                ->join('charger c', 'c.id_charger = pa.charger_id', 'left')
                ->where('pa.po_id', $poId)
                ->where('pa.item_type', 'Charger')
                ->get()
                ->getResultArray();
            
            // Combine all items
            $items = array_merge($unitItems, $attachmentItems, $batteryItems, $chargerItems);
            
            // Get delivery data with parsed serial numbers
            $deliveries = $db->table('po_deliveries pd')
                ->select('pd.*')
                ->where('pd.po_id', $poId)
                ->orderBy('pd.delivery_sequence', 'ASC')
                ->get()
                ->getResultArray();
            
            // Parse delivery items from serial_numbers JSON
            $deliveryItems = [];
            $totalDeliveredByType = [
                'unit' => 0,
                'attachment' => 0,
                'battery' => 0,
                'charger' => 0
            ];
            
            foreach ($deliveries as $delivery) {
                $deliveryItems[$delivery['id_delivery']] = [];
                
                // Get actual delivery items from po_delivery_items table
                $actualDeliveryItems = $db->table('po_delivery_items pdi')
                    ->select('
                        pdi.*,
                        pdi.item_name,
                        pdi.item_type,
                        pdi.serial_number,
                        pdi.qty,
                        "Belum Dicek" as status_verifikasi
                    ')
                    ->where('pdi.delivery_id', $delivery['id_delivery'])
                    ->get()
                    ->getResultArray();
                
                // Also parse serial_numbers JSON for additional data
                if ($delivery['serial_numbers']) {
                    $serialData = json_decode($delivery['serial_numbers'], true);
                    if (is_array($serialData)) {
                        foreach ($serialData as $item) {
                            if (isset($item['type']) && isset($item['qty'])) {
                                // Count delivered items by type
                                if ($delivery['status'] === 'Received') {
                                    $type = strtolower($item['type']);
                                    if (isset($totalDeliveredByType[$type])) {
                                        $totalDeliveredByType[$type] += (int)$item['qty'];
                                    }
                                }
                            }
                        }
                    }
                }
                
                $deliveryItems[$delivery['id_delivery']] = $actualDeliveryItems;
            }
            
            // Calculate summary statistics based on delivery status
            $totalItemsOrdered = count($items);
            
            // Calculate items received based on delivery status (using new logic)
            $totalItemsReceived = array_sum($totalDeliveredByType);
            
            $totalDeliveries = count($deliveries);
            $completedDeliveries = count(array_filter($deliveries, fn($d) => $d['status'] === 'Received'));
            $verifiedItems = count(array_filter($items, fn($i) => $i['status_verifikasi'] === 'Sesuai'));
            
            // Calculate breakdown by item type
            $itemTypeBreakdown = [];
            foreach ($items as $item) {
                $type = $item['item_type'] ?? 'Other';
                if (!isset($itemTypeBreakdown[$type])) {
                    $itemTypeBreakdown[$type] = 0;
                }
                $itemTypeBreakdown[$type]++;
            }
            
            $summary = [
                'total_items_ordered' => $totalItemsOrdered,
                'total_items_received' => $totalItemsReceived,
                'total_deliveries' => $totalDeliveries,
                'completed_deliveries' => $completedDeliveries,
                'in_transit_deliveries' => count(array_filter($deliveries, fn($d) => $d['status'] === 'In Transit')),
                'scheduled_deliveries' => count(array_filter($deliveries, fn($d) => $d['status'] === 'Scheduled')),
                'verified_items' => $verifiedItems,
                'rejected_items' => count(array_filter($items, fn($i) => $i['status_verifikasi'] === 'Tidak Sesuai')),
                'pending_verification' => count(array_filter($items, fn($i) => $i['status_verifikasi'] === 'Belum Dicek')),
                'item_type_breakdown' => $itemTypeBreakdown,
                'delivered_by_type' => $totalDeliveredByType
            ];
            
            return $this->respond([
                'success' => true,
                'data' => [
                    'po' => $po,
                    'items' => $items,
                    'deliveries' => $deliveries,
                    'delivery_items' => $deliveryItems,
                    'summary' => $summary
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', '[getPODetail] Error: ' . $e->getMessage());
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reverify PO - Reset "Tidak Sesuai" items back to "Belum Dicek"
     */
    public function reverifyPO($poId)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Reset verification status of items
            $this->poItemsModel
                ->where('po_id', $poId)
                ->where('status_verifikasi', 'Tidak Sesuai')
                ->set(['status_verifikasi' => 'Belum Dicek', 'catatan_verifikasi' => null])
                ->update();
            
            // Reset PO status back to pending
            $this->purchaseModel->update($poId, ['status' => 'pending']);
            
            $db->transComplete();
            
            if ($db->transStatus()) {
                return $this->respond(['success' => true, 'message' => 'PO berhasil direset untuk verifikasi ulang']);
            } else {
                throw new \Exception('Transaction failed');
            }
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel PO permanently
     */
    public function cancelPO($poId)
    {
        try {
            $result = $this->purchaseModel->update($poId, ['status' => 'Cancelled']);
            
            if ($result) {
                // Send notification: PO Rejected/Cancelled
                helper('notification');
                $po = $this->purchaseModel->find($poId);
                if ($po) {
                    $db = \Config\Database::connect();
                    $supplier = $db->table('suppliers')->where('id_supplier', $po['supplier_id'])->get()->getRowArray();
                    notify_po_rejected([
                        'id' => $poId,
                        'nomor_po' => $po['no_po'],
                        'supplier_name' => $supplier['nama_supplier'] ?? '',
                        'alasan' => 'PO Cancelled',
                        'rejected_by' => session('username') ?? session('user_id'),
                        'url' => base_url('/purchasing/po-list')
                    ]);
                }
                
                return $this->respond(['success' => true, 'message' => 'PO berhasil dibatalkan']);
            } else {
                return $this->respond(['success' => false, 'message' => 'Gagal membatalkan PO'], 500);
            }
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete PO
     */
    public function deletePO($poId)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Delete items first
            $this->poItemsModel->where('po_id', $poId)->delete();
            
            // Delete deliveries (cascade will handle delivery_items)
            $this->poDeliveryModel->where('po_id', $poId)->delete();
            
            // Delete PO
            $this->purchaseModel->delete($poId);
            
            $db->transComplete();
            
            if ($db->transStatus()) {
                return $this->respond(['success' => true, 'message' => 'PO berhasil dihapus']);
            } else {
                throw new \Exception('Transaction failed');
            }
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Complete PO
     */
    public function completePO($poId)
    {
        try {
            $result = $this->purchaseModel->update($poId, ['status' => 'Completed']);
            
            if ($result) {
                return $this->respond(['success' => true, 'message' => 'PO berhasil ditandai sebagai completed']);
            } else {
                return $this->respond(['success' => false, 'message' => 'Gagal complete PO'], 500);
            }
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function poVerification()
    {
        // Get data untuk attachment verification
        $poAttachmentItems = $this->poItemsModel
            ->select('po_items.*, purchase_orders.no_po, purchase_orders.id_po as po_id')
            ->join('purchase_orders', 'purchase_orders.id_po = po_items.po_id')
            ->where('po_items.status_verifikasi', 'Belum Dicek')
            ->orderBy('purchase_orders.no_po', 'ASC')
            ->get()->getResultArray();

        // Group by PO
        $detailGroup = [];
        foreach ($poAttachmentItems as $item) {
            $poKey = $item['po_id'];
            if (!isset($detailGroup[$poKey])) {
                $detailGroup[$poKey] = [
                    'no_po' => $item['no_po'],
                    'data' => []
                ];
            }
            $detailGroup[$poKey]['data'][] = $item;
        }

        $data = [
            'title' => 'PO Verification',
            'suppliers' => $this->supplierModel->get()->getResultArray(),
            'detailGroup' => $detailGroup
        ];

        return view('warehouse/purchase_orders/po_verification', $data);
    }

    public function printPOUnit($id_po)
    {
        $data["data"] = $this->getDetailPOAPI($id_po, false);
        return view('purchasing/print_po',$data);
    }

    public function poUnit()
    {
        if ($this->request->isAJAX()) {
            $start = $this->request->getPost('start') ?? 0;
            $length = $this->request->getPost('length') ?? 10;
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            $orderColumn = $this->request->getPost('columns')[$this->request->getPost('order')[0]['column']]['data'] ?? 'tanggal_po';
            $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';

            // Gunakan method getDataTable yang sudah dinamis dari PurchasingManagementModel
            $data = $this->purchasingManagementModel->getDataTable('Unit', $start, $length, $orderColumn, $orderDir, $searchValue);
            
            return $this->response->setJSON([
                "draw" => $this->request->getPost('draw'),
                "recordsTotal" => $this->purchasingManagementModel->countAllTipe('Unit'),
                "recordsFiltered" => $this->purchasingManagementModel->countFiltered('Unit', $searchValue),
                "data" => $data,
            ]);
        }
        
        // Mengambil data statistik menggunakan PurchasingManagementModel
        $stats = $this->purchasingManagementModel->getPOStats('tipe_po', 'Unit');
        $data = [
            'title' => 'Purchase Order Unit',
            'suppliers' => $this->purchasingManagementModel->getSuppliers(),
        ];
        // Gabungkan stats agar bisa diakses sebagai variabel individual di view
        $data = array_merge($data, $stats);

        return view('purchasing/po_unit', $data);
    }

    public function storePoUnit()
    {
        try {
            // Log incoming data for debugging
            log_message('debug', '[Purchasing] POST data: ' . json_encode($this->request->getPost()));
            
            // Use manual PO number input
            $poNumber = $this->request->getPost('no_po');
            
            // 1. Simpan data ke tabel induk `purchase_orders`
            $poData = [
                'no_po'         => $poNumber,
                'tanggal_po'    => $this->request->getPost('tanggal_po'),
                'supplier_id'   => $this->request->getPost('id_supplier'),
                'tipe_po'       => 'Unit',
                'invoice_no'    => $this->request->getPost('invoice_no') ?: null,
                'invoice_date'  => $this->request->getPost('invoice_date') ?: null,
                'bl_date'       => $this->request->getPost('bl_date') ?: null,
                'keterangan_po' => $this->request->getPost('keterangan_po') ?: null,
                'status'        => 'pending', // Default status
            ];
            
            log_message('debug', '[Purchasing] PO data to insert: ' . json_encode($poData));
        
        if($this->purchaseModel->insert($poData)){
            $newPoId = $this->purchaseModel->getInsertID();

            // 2. Simpan semua data spesifikasi ke tabel `po_units`
            $qty_duplicates = $this->request->getPost('qty_duplicates') ?: 1;
            $poUnitData = []; // Initialize array
            
            // Get brand name from model_unit table based on selected ID
            $db = \Config\Database::connect();
            $merkId = $this->request->getPost('merk_unit'); // This is ID from dropdown
            $merkName = null;
            
            if ($merkId) {
                $merkData = $db->table('model_unit')
                    ->select('merk_unit')
                    ->where('id_model_unit', $merkId)
                    ->get()
                    ->getRowArray();
                $merkName = $merkData['merk_unit'] ?? null;
            }
            
            for ($i=1; $i <= $qty_duplicates; $i++) { 
                $poUnitData[] = [
                    'po_id'             => $newPoId,
                    'jenis_unit'        => $this->request->getPost('jenis_unit'),
                    'merk_unit'         => $merkName, // Save brand name (VARCHAR), not ID
                    'model_unit_id'     => $this->request->getPost('model_unit_id'),
                    'tipe_unit_id'      => $this->request->getPost('tipe_unit_id'),
                    'tahun_po'          => $this->request->getPost('tahun_unit'),
                    'kapasitas_id'      => $this->request->getPost('kapasitas_id'),
                    'mast_id'           => $this->request->getPost('mast_id') ?: null,
                    'mesin_id'          => $this->request->getPost('mesin_id') ?: null,
                    'attachment_id'     => $this->request->getPost('attachment_id') ?: null,
                    'baterai_id'        => $this->request->getPost('baterai_id') ?: null,
                    'charger_id'        => $this->request->getPost('charger_id') ?: null,
                    'ban_id'            => $this->request->getPost('ban_id') ?: null,
                    'roda_id'           => $this->request->getPost('roda_id') ?: null,
                    'valve_id'          => $this->request->getPost('valve_id') ?: null,
                    'keterangan'        => $this->request->getPost('keterangan') ? htmlspecialchars($this->request->getPost('keterangan')) : null,
                    'status_penjualan'  => $this->request->getPost('kondisi_penjualan'),
                    'status_verifikasi' => 'Belum Dicek',
                ];
            }
            
            log_message('debug', '[Purchasing] PO Unit data to insert: ' . json_encode($poUnitData));
            
            if($this->poUnitsModel->insertBatch($poUnitData)){
                log_message('info', '[Purchasing] Successfully created PO Unit with ID: ' . $newPoId . ' and PO Number: ' . $poNumber);
                
                // Log PO Unit creation using trait
                $this->logCreate('purchase_orders', $newPoId, [
                    'po_id' => $newPoId,
                    'no_po' => $poNumber,
                    'tipe_po' => 'Unit',
                    'supplier_id' => $this->request->getPost('id_supplier'),
                    'qty_duplicates' => $qty_duplicates
                ]);
                
                // Send notification: PO Unit Created
                helper('notification');
                $db = \Config\Database::connect();
                $supplier = $db->table('suppliers')->where('id_supplier', $this->request->getPost('id_supplier'))->get()->getRowArray();
                notify_po_unit_created([
                    'id' => $newPoId,
                    'nomor_po' => $poNumber,
                    'supplier_name' => $supplier['nama_supplier'] ?? '',
                    'unit_type' => $this->request->getPost('jenis_unit'),
                    'quantity' => $qty_duplicates,
                    'created_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/warehouse/purchase-orders')
                ]);
                
                // Tambah notifikasi ke warehouse (legacy)
                $notifModel = new \App\Models\NotificationModel();
                $notifModel->insert([
                    'role' => 'warehouse',
                    'division' => 'warehouse',
                    'message' => 'Ada ' . $qty_duplicates . ' unit PO baru (No: ' . $poNumber . ') yang harus diverifikasi.',
                    'link' => '/warehouse/purchase-orders',
                ]);
                return redirect()->to('/purchasing/po-unit')->with('success', 'PO Unit berhasil dibuat dengan nomor: ' . $poNumber);
            }else{
                
                $this->purchaseModel->delete($newPoId); // Hapus PO jika gagal simpan detail

                $errors = $this->poUnitsModel->errors(); // ambil semua pesan error validasi
                log_message('error', '[Purchasing] POUnits insert failed: ' . json_encode($errors));
                
                $error = '';
                foreach ($errors as $key => $value) {
                    $error .= $value . '<br />';
                }
                return redirect()
                    ->to('/purchasing/po-unit')
                    ->withInput()
                    ->with('errors', $errors)
                    ->with('error', 'PO Unit gagal dibuat. Cek form dan perbaiki kesalahan.<br />'.$error);
            }
        }else{
            $errors = $this->purchaseModel->errors(); // ambil semua pesan error validasi
            log_message('error', '[Purchasing] Purchase order insert failed: ' . json_encode($errors));
            
            $error = '';
            foreach ($errors as $key => $value) {
                $error .= $value . '<br />';
            }
            return redirect()
                ->to('/purchasing/po-unit')
                ->withInput()
                ->with('errors', $errors)
                ->with('error', 'PO Unit gagal dibuat. Cek form dan perbaiki kesalahan.<br />'.$error);

        }
        } catch (\Exception $e) {
            log_message('error', '[Purchasing] Exception in storePoUnit: ' . $e->getMessage());
            log_message('error', '[Purchasing] Stack trace: ' . $e->getTraceAsString());
            
            return redirect()
                ->to('/purchasing/po-unit')
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function saveUpdatePoUnit($id_po)
    {
        // 1. Simpan data ke tabel induk `purchase_orders`
        $poData = [
            'no_po'         => $this->request->getPost('no_po'),
            'tanggal_po'    => $this->request->getPost('tanggal_po'),
            'supplier_id'   => $this->request->getPost('id_supplier'),
            'tipe_po'       => 'Unit',
            'invoice_no'    => $this->request->getPost('invoice_no') ?? NULL,
            'invoice_date'  => $this->request->getPost('invoice_date') ?? NULL,
            'bl_date'       => $this->request->getPost('bl_date') ?? NULL,
            'keterangan_po' => $this->request->getPost('keterangan_po') ?? NULL
        ];
        
        if($this->purchaseModel->update($id_po, $poData)){
            // 2. Simpan semua data spesifikasi ke tabel `po_units`
            $poUnitData = [
                'po_id'             => $id_po,
                'jenis_unit'        => $this->request->getPost('jenis_unit'),
                'merk_unit'         => $this->request->getPost('merk_unit'),
                'model_unit_id'     => $this->request->getPost('model_unit_id'),
                'tipe_unit_id'      => $this->request->getPost('tipe_unit_id'),
                'tahun_po'          => $this->request->getPost('tahun_unit'),
                'kapasitas_id'      => $this->request->getPost('kapasitas_id'),
                'mast_id'           => $this->request->getPost('mast_id'),
                'mesin_id'          => $this->request->getPost('mesin_id'),
                'attachment_id'     => $this->request->getPost('attachment_id'),
                'baterai_id'        => $this->request->getPost('baterai_id'),
                'charger_id'        => $this->request->getPost('charger_id'),
                'ban_id'            => $this->request->getPost('ban_id'),
                'roda_id'           => $this->request->getPost('roda_id'),
                'valve_id'          => $this->request->getPost('valve_id'),
                'keterangan'        => $this->request->getPost('keterangan'),
                'status_penjualan'  => $this->request->getPost('kondisi_penjualan'),
            ];
            if($this->poUnitsModel->where('po_id', $id_po)->set($poUnitData)->update()){
                return redirect()->to('/purchasing/po-unit')->with('success', 'PO Unit berhasil dirubah.');
            }else{
                $errors = $this->poUnitsModel->errors(); // ambil semua pesan error validasi
                return redirect()
                    ->to('/purchasing/po-unit')
                    ->withInput()
                    ->with('errors', $errors)
                    ->with('error', 'PO Unit gagal dirubah. Cek form dan perbaiki kesalahan.');
            }
        }else{
            $errors = $this->purchaseModel->errors(); // ambil semua pesan error validasi
            return redirect()
                ->to('/purchasing/po-unit')
                ->withInput()
                ->with('errors', $errors)
                ->with('error', 'PO Unit gagal dirubah. Cek form dan perbaiki kesalahan.');
        }
    }
    
    public function deletePoUnit($id_po)
    {
        $poData = $this->purchaseModel->find($id_po);
        
        $this->purchaseModel->delete($id_po);
        
        // Log PO Unit deletion using trait
        $this->logDelete('purchase_orders', $id_po, $poData, [
            'po_id' => $id_po,
            'no_po' => $poData['no_po'] ?? null,
            'tipe_po' => $poData['tipe_po'] ?? null
        ]);
        
        return $this->response->setJSON(['success' => true]);
    }

    // ===================================================================
    // PURCHASE ORDER ATTACHMENT & BATTERY METHODS
    // ===================================================================

    public function poAttachment()
    {
        // Cek jika ini adalah permintaan AJAX dari DataTable
        if ($this->request->isAJAX()) {
            $start = $this->request->getPost('start') ?? 0;
            $length = $this->request->getPost('length') ?? 10;
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            $orderColumn = $this->request->getPost('columns')[$this->request->getPost('order')[0]['column']]['data'] ?? 'tanggal_po';
            $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';

            // Gunakan model yang benar (PurchasingManagementModel) untuk data tabel
            $data = $this->purchasingManagementModel->getDataTable('Attachment & Battery', $start, $length, $orderColumn, $orderDir, $searchValue);
            
            return $this->response->setJSON([
                "draw" => $this->request->getPost('draw'),
                "recordsTotal" => $this->purchasingManagementModel->countAllTipe('Attachment & Battery'),
                "recordsFiltered" => $this->purchasingManagementModel->countFiltered('Attachment & Battery', $searchValue),
                "data" => $data,
            ]);
        }
        
        // Jika bukan AJAX, siapkan data untuk memuat halaman
        $data = [
            'title' => 'Purchase Order Attachment & Battery',
            'stats' => $this->purchasingManagementModel->getPOStats('tipe_po','Attachment & Battery'),
            'suppliers' => $this->purchasingManagementModel->getSuppliers(),
        ];

        return view('purchasing/po_attachment', $data);
    }

    public function newPoAttachment()
    {
        $data = [
            'title'         => 'New PO Attachment & Battery',
            'card_title'    => 'New PO Attachment & Battery',
            'po'            => [],
            'detail'        => [],
            'mode'          => 'create',
            'suppliers'     => $this->supplierModel->get()->getResultArray(),
            'attachments'   => $this->attachmentModel->get()->getResultArray(),
            'baterais'      => $this->bateraiModel->get()->getResultArray(),
            'chargers'      => $this->chargerModel->get()->getResultArray(),
            'validation'    => \Config\Services::validation()
        ];

        return view('purchasing/po_attachmentForm', $data);
    }

    public function editPoAttachment($id_po)
    {
        $poData = $this->purchaseModel->where('id_po', $id_po)->first();
        $poDetail = $this->poItemsModel->where('po_id', $id_po)->first();

        if (!$poData) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'title'         => 'Edit PO ' . $poData['no_po'],
            'card_title'    => 'Edit PO ' . $poData['no_po'],
            'po'            => $poData,
            'detail'        => $poDetail,
            'mode'          => 'update',
            'id_po'         => $id_po,
            'suppliers'     => $this->supplierModel->get()->getResultArray(),
            'attachments'   => $this->attachmentModel->get()->getResultArray(),
            'baterais'      => $this->bateraiModel->get()->getResultArray(),
            'chargers'      => $this->chargerModel->get()->getResultArray(),
            'validation'    => \Config\Services::validation()
        ];

        return view('purchasing/po_attachmentForm', $data);
    }

    public function storePoAttachment()
    {
        $item_type = $this->request->getPost('tipe_po'); // Ini adalah 'Attachment' atau 'Battery'
        
        // 1. Validasi input
        $validationRules = [
            'no_po'       => 'required|max_length[50]',
            'tanggal_po'  => 'required|valid_date',
            'id_supplier' => 'required|integer',
            'tipe_po'     => 'required|in_list[Attachment,Battery]',
        ];

        if ($item_type === 'Attachment') {
            $validationRules['specification'] = 'required|integer'; 
        } elseif ($item_type === 'Battery') {
            $validationRules['baterai_id'] = 'required|integer';
            $validationRules['charger_id'] = 'required|integer';
        }

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Simpan data PO ke tabel `purchase_orders`
        $poData = [
            'no_po'         => $this->request->getPost('no_po'),
            'tanggal_po'    => $this->request->getPost('tanggal_po'),
            'supplier_id'   => $this->request->getPost('id_supplier'),
            'invoice_no'    => $this->request->getPost('invoice_no') ?? NULL,
            'invoice_date'  => $this->request->getPost('invoice_date') ?? NULL,
            'bl_date'       => $this->request->getPost('bl_date') ?? NULL,
            'tipe_po'       => 'Attachment & Battery', 
            'status'        => 'pending',
            'keterangan_po' => $this->request->getPost('keterangan_po') ?? NULL,
        ];

        if ($this->purchaseModel->insert($poData)) {
            $newPoId = $this->purchaseModel->getInsertID();

            // 3. Siapkan data untuk insert batch
            $itemsToInsert = [];
            $quantity = (int)$this->request->getPost('qty');

            for ($i = 0; $i < $quantity; $i++) {
                $poItemData = [
                    'po_id'             => $newPoId,
                    'item_type'         => $item_type,
                    'keterangan'        => htmlspecialchars($this->request->getPost('keterangan')),
                    'status_verifikasi' => 'Belum Dicek',
                ];

                if ($item_type === 'Attachment') {
                    $poItemData['attachment_id'] = $this->request->getPost('specification');
                    // SN bisa diisi di loop jika ada format khusus, atau dibiarkan sama
                    $poItemData['serial_number'] = $this->request->getPost('serial_number') ?? NULL;
                } elseif ($item_type === 'Battery') {
                    $poItemData['baterai_id'] = $this->request->getPost('baterai_id');
                    $poItemData['charger_id'] = $this->request->getPost('charger_id');
                    $poItemData['serial_number'] = $this->request->getPost('serial_number') ?? NULL; 
                    $poItemData['serial_number_charger'] = $this->request->getPost('serial_number_charger');
                }
                $itemsToInsert[] = $poItemData;
            }

            // 4. Gunakan insertBatch untuk efisiensi
            if ($this->poItemsModel->insertBatch($itemsToInsert)) {
                // Log PO Attachment creation using trait
                $this->logCreate('purchase_orders', $newPoId, [
                    'po_id' => $newPoId,
                    'no_po' => $this->request->getPost('no_po'),
                    'tipe_po' => 'Attachment & Battery',
                    'item_type' => $item_type,
                    'quantity' => $quantity,
                    'supplier_id' => $this->request->getPost('id_supplier')
                ]);
                
                // Send notification: PO Attachment Created
                helper('notification');
                $db = \Config\Database::connect();
                $supplier = $db->table('suppliers')->where('id_supplier', $this->request->getPost('id_supplier'))->get()->getRowArray();
                notify_po_attachment_created([
                    'id' => $newPoId,
                    'nomor_po' => $this->request->getPost('no_po'),
                    'supplier_name' => $supplier['nama_supplier'] ?? '',
                    'item_type' => $item_type,
                    'quantity' => $quantity,
                    'created_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/warehouse/purchase-orders')
                ]);
                
                return redirect()->to('/purchasing/po-attachment')->with('success', 'PO ' . $item_type . ' berhasil ditambahkan sebanyak ' . $quantity . ' unit.');
            } else {
                $this->purchaseModel->delete($newPoId);
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan detail PO.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data PO utama.');
        }
    }

    public function saveUpdatePoAttachment($id_po)
    {
        $item_type = $this->request->getPost('tipe_po');
        
        // 1. Validasi input
        $validationRules = [
            'no_po'       => 'required|max_length[50]',
            'tanggal_po'  => 'required|valid_date',
            'id_supplier' => 'required|integer',
            'tipe_po'     => 'required|in_list[Attachment,Battery]',
            'qty'         => 'required|integer|greater_than[0]',
        ];

        if ($item_type === 'Attachment') {
            $validationRules['specification'] = 'required|integer';
        } elseif ($item_type === 'Battery') {
            $validationRules['baterai_id'] = 'required|integer';
            $validationRules['charger_id'] = 'required|integer';
        }

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Update data PO di tabel `purchase_orders`
        $poData = [
            'no_po'         => $this->request->getPost('no_po'),
            'tanggal_po'    => $this->request->getPost('tanggal_po'),
            'supplier_id'   => $this->request->getPost('id_supplier'),
            'invoice_no'    => $this->request->getPost('invoice_no'),
            'invoice_date'  => $this->request->getPost('invoice_date'),
            'bl_date'       => $this->request->getPost('bl_date'),
            'tipe_po'       => 'Attachment & Battery',
            'keterangan_po' => $this->request->getPost('keterangan_po'),
        ];

        if ($this->purchaseModel->update($id_po, $poData)) {
            // 3. Hapus item lama untuk digantikan dengan yang baru
            $this->poItemsModel->where('po_id', $id_po)->delete();

            // 4. Siapkan data baru untuk insert batch
            $itemsToInsert = [];
            $quantity = (int)$this->request->getPost('qty');

            for ($i = 0; $i < $quantity; $i++) {
                $poItemData = [
                    'po_id'             => $id_po, // Gunakan id_po yang ada
                    'item_type'         => $item_type,
                    'qty'               => 1, // Setiap baris mewakili 1 item
                    'keterangan'        => htmlspecialchars($this->request->getPost('keterangan')),
                    'status_verifikasi' => 'Belum Dicek', // Atau ambil status lama jika perlu
                ];

                if ($item_type === 'Attachment') {
                    $poItemData['attachment_id'] = $this->request->getPost('specification');
                    $poItemData['serial_number'] = $this->request->getPost('serial_number');
                } elseif ($item_type === 'Battery') {
                    $poItemData['baterai_id'] = $this->request->getPost('baterai_id');
                    $poItemData['charger_id'] = $this->request->getPost('charger_id');
                    $poItemData['serial_number'] = $this->request->getPost('serial_number');
                    $poItemData['serial_number_charger'] = $this->request->getPost('serial_number_charger');
                }
                $itemsToInsert[] = $poItemData;
            }

            // 5. Gunakan insertBatch untuk memasukkan data baru
            if ($this->poItemsModel->insertBatch($itemsToInsert)) {
                return redirect()->to('/purchasing/po-attachment')->with('success', 'PO ' . $item_type . ' berhasil dirubah.');
            } else {
                 return redirect()->back()->withInput()->with('error', 'Gagal mengubah detail PO.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah data PO utama.');
        }
    }

    public function deletePoAttachment($id)
    {
        if ($this->request->isAJAX()) {
            $poData = $this->purchaseModel->find($id);
            
            $db = \Config\Database::connect();
            $db->transStart();
            $this->poItemsModel->where('po_id', $id)->delete();
            $this->purchaseModel->delete($id);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data.']);
            }
            
            // Log PO Attachment deletion using trait
            $this->logDelete('purchase_orders', $id, $poData, [
                'po_id' => $id,
                'no_po' => $poData['no_po'] ?? null,
                'tipe_po' => $poData['tipe_po'] ?? null
            ]);
            
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->to('/purchasing/po-attachment');
    }

    public function resolvePoAttachment($id)
    {
        if ($this->request->isAJAX()) {
            $po = $this->purchaseModel->find($id);

            if ($po && $po['status'] === 'Selesai dengan Catatan') {
                if ($this->purchaseModel->update($id, ['status' => 'completed'])) {
                    return $this->response->setJSON(['success' => true]);
                }
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal update database.']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Status PO tidak valid untuk diselesaikan.']);
        }
        return redirect()->to('/purchasing/po-attachment');
    }

    public function printPOAttachment($id_po)
    {
        $data["data"] = $this->getDetailPOAttachmentAPI($id_po, false);
        return view('purchasing/print_po_attachment', $data);
    }

    public function getDetailPOAttachmentAPI($id_po, $api = true)
    {
        $poData = $this->purchaseModel
            ->select('purchase_orders.*, suppliers.nama_supplier')
            ->join('suppliers', 'suppliers.id_supplier = purchase_orders.supplier_id')
            ->where('purchase_orders.id_po', $id_po)
            ->first();

        $dataDetail = $this->poItemsModel
            ->select('
                po_items.*, 
                CONCAT(attachment.tipe, " ", attachment.merk, " ", attachment.model) AS attachment_name, 
                baterai.merk_baterai, baterai.tipe_baterai,
                charger.merk_charger, charger.tipe_charger
            ')
            ->join('attachment', 'attachment.id_attachment = po_items.attachment_id', 'left')
            ->join('baterai', 'baterai.id = po_items.baterai_id', 'left')
            ->join('charger', 'charger.id_charger = po_items.charger_id', 'left')
            ->where('po_items.po_id', $id_po)
            ->findAll();

        $response = [
            'po' => $poData,
            'details' => $dataDetail // Menggunakan key 'details'
        ];

        if ($api) {
            return $this->respond($response, 200);
        } else {
            return $response;
        }
    }

    // ===================================================================
    // PURCHASE ORDER SPAREPART METHODS
    // ===================================================================

    public function poSparepart()
    {
        if ($this->request->isAJAX()) {
            // Logika untuk DataTable server-side
            $start = $this->request->getPost('start') ?? 0;
            $length = $this->request->getPost('length') ?? 10;
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            $orderColumn = $this->request->getPost('columns')[$this->request->getPost('order')[0]['column']]['data'] ?? 'tanggal_po';
            $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';

            $data = $this->purchasingModel->getDataTable('Sparepart', $start, $length, $orderColumn, $orderDir, $searchValue);
            
            return $this->response->setJSON([
                "draw" => $this->request->getPost('draw'),
                "recordsTotal" => $this->purchasingModel->countAllTipe('Sparepart'),
                "recordsFiltered" => $this->purchasingModel->countFiltered('Sparepart', $searchValue),
                "data" => $data,
            ]);
        }
        
        $data = [
            'title' => 'PO Sparepart - Coming Soon',
        ];
        return view('purchasing/po_sparepart', $data);
    }

    public function poSparepartForm()
    {
        $data = [
            'title'      => 'Tambah PO Sparepart',
            'card_title' => 'PO Sparepart', // Judul untuk mode create
            'mode'       => 'create',
            'po'         => [],
            'items'      => [],
            'suppliers'  => $this->supplierModel->findAll(),
            'spareparts' => $this->sparepartModel->findAll(),
        ];
        return view('purchasing/po_sparepartForm', $data);
    }

    public function storePoSparepart()
    {
        // 1. Validasi data dasar
        $validationRules = [
            'no_po'       => 'required|max_length[50]',
            'tanggal_po'  => 'required|valid_date',
            'id_supplier' => 'required|integer',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Simpan data PO utama
        $poData = [
            'no_po'         => $this->request->getPost('no_po'),
            'tanggal_po'    => $this->request->getPost('tanggal_po'),
            'supplier_id'   => $this->request->getPost('id_supplier'),
            'tipe_po'       => 'Sparepart', 
            'status'        => 'pending',
            'keterangan_po' => $this->request->getPost('keterangan_po'),
        ];

        if ($this->purchaseModel->insert($poData)) {
            $newPoId = $this->purchaseModel->getInsertID();

            // 3. Siapkan data item sparepart
            $sparepartIds = $this->request->getPost('sparepart_id');
            $quantities = $this->request->getPost('qty');
            $satuans = $this->request->getPost('satuan'); 
            $keteranganItems = $this->request->getPost('keterangan_item');
            
            $itemsToInsert = [];
            if (is_array($sparepartIds) && !empty($sparepartIds)) {
                for ($i = 0; $i < count($sparepartIds); $i++) {
                    // Pastikan baris tidak kosong sebelum ditambahkan
                    if (!empty($sparepartIds[$i])) {
                        $itemsToInsert[] = [
                            'po_id'        => $newPoId,
                            'sparepart_id' => $sparepartIds[$i],
                            'qty'          => $quantities[$i],
                            'satuan'       => $satuans[$i],
                            'keterangan'   => $keteranganItems[$i],
                        ];
                    }
                }
            }

            // 4. Lakukan insert batch HANYA jika ada item yang akan dimasukkan
            if (!empty($itemsToInsert)) {
                // PERIKSA HASIL INSERT BATCH
                if ($this->poSparepartItemModel->insertBatch($itemsToInsert)) {
                    // JIKA BERHASIL, send notification and tampilkan pesan sukses
                    helper('notification');
                    $db = \Config\Database::connect();
                    $supplier = $db->table('suppliers')->where('id_supplier', $this->request->getPost('id_supplier'))->get()->getRowArray();
                    notify_po_sparepart_created([
                        'id' => $newPoId,
                        'nomor_po' => $this->request->getPost('no_po'),
                        'supplier_name' => $supplier['nama_supplier'] ?? '',
                        'total_items' => count($itemsToInsert),
                        'created_by' => session('username') ?? session('user_id'),
                        'url' => base_url('/warehouse/purchase-orders')
                    ]);
                    
                    return redirect()->to('/purchasing/po-sparepart')->with('success', 'PO Sparepart berhasil ditambahkan.');
                } else {
                    // JIKA GAGAL, hapus PO utama (rollback) dan tampilkan error
                    $this->purchaseModel->delete($newPoId);
                    return redirect()->back()->withInput()->with('error', 'Gagal menyimpan detail item sparepart. PO dibatalkan.');
                }
            } else {
                // Jika tidak ada item sama sekali, batalkan PO dan beri pesan error
                $this->purchaseModel->delete($newPoId);
                return redirect()->back()->withInput()->with('error', 'Tidak ada item sparepart yang ditambahkan. PO dibatalkan.');
            }
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data PO utama.');
    }

    public function viewPoSparepart($id)
    {
        $poData = $this->purchaseModel
            ->select('purchase_orders.*, suppliers.nama_supplier')
            ->join('suppliers', 'suppliers.id_supplier = purchase_orders.supplier_id')
            ->where('purchase_orders.id_po', $id)
            ->first();

        $itemsData = $this->poSparepartItemModel
            ->select('po_sparepart_items.*, s.kode, s.desc_sparepart')
            ->join('sparepart s', 's.id_sparepart = po_sparepart_items.sparepart_id')
            ->where('po_id', $id)
            ->findAll();

        if ($poData) {
            return $this->response->setJSON(['po' => $poData, 'items' => $itemsData]);
        }
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Data not found']);
    }

    // METHOD BARU UNTUK MENAMPILKAN FORM EDIT
    public function editPoSparepart($id)
    {
        $poData = $this->purchaseModel->find($id);
        if (!$poData) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title'      => 'Edit PO Sparepart ' . $poData['no_po'],
            'card_title' => 'Edit Purchase Order ' . $poData['no_po'], // Judul untuk mode edit
            'mode'       => 'update',
            'po'         => $poData,
            'items'      => $this->poSparepartItemModel->where('po_id', $id)->findAll(),
            'suppliers'  => $this->supplierModel->findAll(),
            'spareparts' => $this->sparepartModel->findAll(),
        ];

        return view('purchasing/po_sparepartForm', $data); 
    }

    public function updatePoSparepart($id)
    {
        // Check permission for updating PO
        if (!$this->hasPermission('purchasing.po_sparepart.edit')) {
            return redirect()->to('/purchasing/po-sparepart')->with('error', 'Access denied: You do not have permission to update Purchase Orders');
        }
        
        return redirect()->to('/purchasing/po-sparepart')->with('success', 'PO Sparepart berhasil diupdate.');
    }

    public function deletePoSparepart($id)
    {
        // Check permission for deleting PO
        if (!$this->hasPermission('purchasing.po_sparepart.delete')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied: You do not have permission to delete Purchase Orders'
                ])->setStatusCode(403);
            }
            return redirect()->to('/purchasing/po-sparepart')->with('error', 'Access denied: You do not have permission to delete Purchase Orders');
        }
        
        if ($this->request->isAJAX()) {
            $db = \Config\Database::connect();
            $db->transStart();
            $this->poSparepartItemModel->where('po_id', $id)->delete();
            $this->purchaseModel->delete($id);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data.']);
            }
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->to('/purchasing/po-sparepart');
    }

    public function resolvePoSparepart($id)
    {
        if ($this->request->isAJAX()) {
            $po = $this->purchaseModel->find($id);

            if ($po && $po['status'] === 'Selesai dengan Catatan') {
                if ($this->purchaseModel->update($id, ['status' => 'completed'])) {
                    return $this->response->setJSON(['success' => true]);
                }
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal update database.']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Status PO tidak valid untuk diselesaikan.']);
        }
        return redirect()->to('/purchasing/po-sparepart');
    }
    // ===================================================================
    // API METHODS FOR AJAX CALLS
    // ===================================================================

    /**
     * Get notifications
     */
    public function getNotifications()
    {
        $notifications = $this->notificationModel->where('division', 'purchasing')
                                                 ->orderBy('created_at', 'DESC')
                                                 ->findAll();
        return $this->response->setJSON($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $this->notificationModel->update($id, ['is_read' => 1]);
        return $this->response->setJSON(['success' => true]);
    }

    // ===================================================================
    // FORM GABUNGAN DINAMIS
    // ===================================================================

    /**
     * Menampilkan form PO dinamis
     */
    public function formPo()
    {
        $data = [
            'title' => 'Form Purchase Order',
            'suppliers' => $this->supplierModel->findAll(),
        ];

        return view('purchasing/formPo', $data);
    }

    /**
     * API untuk load form unit di dalam modal
     */
    public function getUnitFormAPI()
    {
        // Load data untuk dropdown
        $data = [
            'merks' => $this->modelUnitModel->select('DISTINCT(merk_unit) as merk_unit, MIN(id_model_unit) as id_model_unit')->groupBy('merk_unit')->findAll(),
            'departemens' => $this->departemenModel->findAll(),
            'tipe_units' => $this->tipeUnitModel->findAll(),
            'kapasitas' => $this->kapasitasModel->findAll(),
            'masts' => $this->tipeMastModel->findAll(),
            'mesins' => $this->mesinModel->findAll(),
            'bans' => $this->tipeBanModel->findAll(),
            'rodas' => $this->jenisRodaModel->findAll(),
            'valves' => $this->valveModel->findAll(),
            'baterais' => $this->bateraiModel->findAll(),
        ];
        
        // Render form unit sebagai fragment
        return view('purchasing/ajax/unit_form_fragment', $data);
    }

    /**
     * API untuk load form attachment di dalam modal
     */
    public function getAttachmentFormAPI()
    {
        // Load data untuk dropdown
        $data = [
            'attachments' => $this->attachmentModel->findAll(),
            'baterais' => $this->bateraiModel->findAll(),
            'chargers' => $this->chargerModel->findAll(),
        ];
        
        // Render form attachment sebagai fragment
        return view('purchasing/ajax/attachment_form_fragment', $data);
    }

    /**
     * API untuk load form sparepart di dalam modal
     */
    public function getSparepartFormAPI()
    {
        // Load data untuk dropdown
        $data = [
            'spareparts' => $this->sparepartModel->findAll(),
        ];
        
        // Render form sparepart sebagai fragment
        return view('purchasing/ajax/sparepart_form_fragment', $data);
    }

    /**
     * Handle form PO dinamis yang bisa input multiple items
     */
    public function storePoDinamis()
    {
        // 1. Validasi Input Form Utama
        $rules = [
            'no_po'       => 'required|max_length[50]',
            'tanggal_po'  => 'required|valid_date',
            'id_supplier' => 'required|integer',
            'items_json'  => 'required'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Mulai Transaksi Database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $items = json_decode($this->request->getPost('items_json'), true);
    
            // 3. Kelompokkan item berdasarkan tipenya
            $groupedItems = [];
            foreach ($items as $item) {
                $groupedItems[$item['item_type']][] = $item;
            }

            // 4. Loop untuk setiap JENIS item, buat PO terpisah
            foreach ($groupedItems as $itemType => $itemList) {
                
                // Tentukan tipe_po berdasarkan grup
                $tipePo = '';
                if ($itemType === 'unit') $tipePo = 'Unit';
                elseif ($itemType === 'attachment') $tipePo = 'Attachment & Battery';
                elseif ($itemType === 'sparepart') $tipePo = 'Sparepart';

                // Siapkan data header PO untuk jenis item ini
                $poHeaderData = [
                    'no_po'         => $this->request->getPost('no_po'), // Nomor PO sama
                    'tanggal_po'    => $this->request->getPost('tanggal_po'),
                    'supplier_id'   => $this->request->getPost('id_supplier'),
                    'tipe_po'       => $tipePo, // tipe_po spesifik per grup
                    'status'        => 'pending',
                ];
                
                // 5. Simpan Header PO untuk grup ini
                $this->purchaseModel->insert($poHeaderData);
                $newPoId = $this->purchaseModel->getInsertID();

                // 6. Simpan semua item dalam grup ini ke tabel detailnya masing-masing
                foreach ($itemList as $item) {
                    switch ($itemType) {
                        case 'unit':
                            $this->insertPoUnit($newPoId, $item);
                            break;
                        case 'attachment':
                            $this->insertPoAttachment($newPoId, $item);
                            break;
                        case 'sparepart':
                            $this->insertPoSparepart($newPoId, $item);
                            break;
                    }
                }
            } // Akhir dari loop per jenis item
            
            $db->transComplete();

            if ($db->transStatus() === false) {
                // Tambahkan log untuk debug transaksi database
                log_message('error', '[Purchasing] Transaksi database gagal: ' . json_encode($db->error()));
                throw new \Exception('Transaksi database gagal.');
            }

            return redirect()->to('/purchasing/form-po')->with('success', 'PO berhasil dibuat untuk ' . count($groupedItems) . ' tipe item.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


    /**
     * Insert data ke po_units menggunakan Model
     * SEKALIGUS INSERT KE INVENTORY_UNIT untuk verifikasi
     */
    private function insertPoUnit($poId, $item)
    {
        // Siapkan data dasar unit (tanpa po_id)
        $unitDataTemplate = [
            'jenis_unit'        => $item['jenis'],
            'merk_unit'         => $item['merk'],
            'model_unit_id'     => $item['model'],
            'tipe_unit_id'      => $item['jenis_unit'],
            'tahun_po'          => $item['tahun'],
            'kapasitas_id'      => $item['kapasitas'],
            'mast_id'           => $item['mast'] ?: null,
            'mesin_id'          => $item['engine'] ?: null,
            'ban_id'            => $item['tire'] ?: null,
            'roda_id'           => $item['wheel'] ?: null,
            'valve_id'          => $item['valve'] ?: null,
            'baterai_id'        => $item['battery'] ?: null,
            'status_penjualan'  => $item['kondisi'],
            'keterangan'        => $item['keterangan'],
            'status_verifikasi' => 'Belum Dicek'
        ];

        // Tentukan jumlah duplikasi dari 'qty'
        $quantity = (int)($item['qty'] ?? 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        // Siapkan array batch untuk diduplikasi
    $unitItemsBatch = [];
        
        for ($i = 0; $i < $quantity; $i++) {
            // Setiap item dalam batch memiliki po_id yang sama
            $newItem = ['po_id' => $poId] + $unitDataTemplate;
            $unitItemsBatch[] = $newItem;
            
        }

        // Gunakan insertBatch untuk efisiensi PO Units
        if (!$this->poUnitsModel->insertBatch($unitItemsBatch)) {
            $errors = $this->poUnitsModel->errors();
            throw new \Exception('Gagal menyimpan detail Unit (batch): ' . implode(', ', $errors));
        }
        
    // INVENTORY UNIT DITUNDA: Insert ke inventory_unit akan dilakukan saat proses verifikasi di Warehouse, bukan saat pembuatan PO.
    }

    /**
     * Insert data ke po_items (attachment/battery) menggunakan Model.
     * SEKALIGUS INSERT KE INVENTORY_ATTACHMENT untuk verifikasi
     */
    private function insertPoAttachment($poId, $item)
    {
        // Siapkan data dasar
        $attachmentDataTemplate = [
            'item_type'         => $item['po_type'], // "Attachment" atau "Battery"
            'attachment_id'     => $item['attachment_id'] ?: null,
            'baterai_id'        => $item['baterai_id'] ?: null,
            'charger_id'        => $item['charger_id'] ?: null,
            'keterangan'        => $item['keterangan'],
            'status_verifikasi' => 'Belum Dicek'
        ];
        
        // Tentukan jumlah duplikasi
        $quantity = (int)($item['qty'] ?? 1);
        if ($quantity < 1) $quantity = 1;

        // Siapkan array batch
    $attachmentItemsBatch = [];
        
        for ($i = 0; $i < $quantity; $i++) {
            $newItem = ['po_id' => $poId] + $attachmentDataTemplate;
            $attachmentItemsBatch[] = $newItem;
            
            // INVENTORY ATTACHMENT DITUNDA: Akan dibuat saat verifikasi (Warehouse) jika status 'Sesuai'.
        }

        // Gunakan insertBatch untuk PO Items
        if (!$this->poItemsModel->insertBatch($attachmentItemsBatch)) {
            $errors = $this->poItemsModel->errors();
            throw new \Exception('Gagal menyimpan detail Attachment: ' . implode(', ', $errors));
        }
        
    // INVENTORY ATTACHMENT DITUNDA: Insert ke inventory_attachment dilakukan saat verifikasi.
    }

    /**
     * Insert data ke po_sparepart_items menggunakan Model.
     */
    private function insertPoSparepart($poId, $item)
    {
        log_message('debug', '[insertPoSparepart] Incoming item: ' . json_encode($item));
        // Guard minimal fields
        if (!isset($item['sparepart_id']) || !isset($item['qty']) || !isset($item['satuan'])) {
            throw new \Exception('Data sparepart tidak lengkap: ' . json_encode($item));
        }
        $sparepartData = [
            'po_id'             => $poId,
            'sparepart_id'      => $item['sparepart_id'],
            'qty'               => $item['qty'],
            'satuan'            => $item['satuan'],
            'keterangan'        => $item['keterangan'] ?? null,
            'status_verifikasi' => 'Belum Dicek'
        ];

        if (!$this->poSparepartItemModel->insert($sparepartData)) {
            $errors = $this->poSparepartItemModel->errors();
            log_message('error', '[insertPoSparepart] Insert po_sparepart_items failed: ' . json_encode($errors) . ' data=' . json_encode($sparepartData));
            throw new \Exception('Gagal menyimpan detail Sparepart: ' . implode(', ', $errors));
        }
    // NOTE: Tidak langsung update inventory di tahap pembuatan PO.
    // Stok sparepart akan bertambah saat verifikasi (lihat WarehousePO::verifyPoSparepart) untuk menjaga akurasi penerimaan barang.
    log_message('debug', '[insertPoSparepart] Stored PO sparepart item (inventory update deferred until verification)');
    }

    /**
     * API untuk mendukung cascading dropdown attachment 
     */
    public function getAttachmentMerkAPI()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $tipe = $this->request->getGet('tipe');
        if (!$tipe) {
            return $this->fail('Parameter tipe tidak ditemukan.', 400);
        }

        try {
            $merks = $this->attachmentModel
                        ->select('DISTINCT(merk) as merk')
                        ->where('tipe', $tipe)
                        ->where('merk !=', '')
                        ->orderBy('merk', 'ASC')
                        ->findAll();
            
            $merkList = array_column($merks, 'merk');
            return $this->respond(['data' => $merkList]);
        } catch (\Exception $e) {
            return $this->fail('Error: ' . $e->getMessage(), 500);
        }
    }

    public function getAttachmentModelAPI()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $tipe = $this->request->getGet('tipe');
        $merk = $this->request->getGet('merk');
        
        if (!$tipe || !$merk) {
            return $this->fail('Parameter tipe dan merk harus ada.', 400);
        }

        try {
            $models = $this->attachmentModel
                        ->select('id_attachment, model')
                        ->where('tipe', $tipe)
                        ->where('merk', $merk)
                        ->orderBy('model', 'ASC')
                        ->findAll();
            
            return $this->respond(['data' => $models]);
        } catch (\Exception $e) {
            return $this->fail('Error: ' . $e->getMessage(), 500);
        }
    }

    public function apiGetTipeUnits()
    {
        if(!$this->request->isAJAX()) return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Invalid request']);
        $dept = $this->request->getGet('departemen');
        $tipe = $this->request->getGet('tipe');
        $builder = $this->tipeUnitModel->asArray();
        if($dept){ $builder->where('id_departemen', $dept); }
        if($tipe){ $builder->where('tipe', $tipe); }
        $rows = $builder->findAll();
        return $this->response->setJSON(['success'=>true,'data'=>$rows]);
    }

    /**
     * Get PO Statistics by type
     */
    private function getPOStats($type)
    {
        $db = \Config\Database::connect();
        
        switch($type) {
            case 'unit':
                $table = 'purchase_orders';
                $itemsTable = 'po_units';
                break;
            case 'attachment':
                $table = 'purchase_orders';
                $itemsTable = 'po_items';
                break;
            case 'sparepart':
                $table = 'purchase_orders';
                $itemsTable = 'po_sparepart_items';
                break;
            default:
                return [];
        }

        // Get total PO count
        $totalQuery = $db->query("SELECT COUNT(*) as total FROM {$table} WHERE tipe_po = ?", [$type]);
        $total = $totalQuery->getRow()->total;

        // Get pending count
        $pendingQuery = $db->query("SELECT COUNT(*) as pending FROM {$table} WHERE tipe_po = ? AND status = 'pending'", [$type]);
        $pending = $pendingQuery->getRow()->pending;

        // Get completed count
        $completedQuery = $db->query("SELECT COUNT(*) as completed FROM {$table} WHERE tipe_po = ? AND status = 'completed'", [$type]);
        $completed = $completedQuery->getRow()->completed;

        // Get "Selesai dengan Catatan" count
        $catatanQuery = $db->query("SELECT COUNT(*) as catatan FROM {$table} WHERE tipe_po = ? AND status = 'Selesai dengan Catatan'", [$type]);
        $catatan = $catatanQuery->getRow()->catatan;

        return [
            'total' => $total,
            'pending' => $pending,
            'completed' => $completed,
            'Selesai dengan Catatan' => $catatan
        ];
    }

    /**
     * Get Supplier Statistics
     */
    private function getSupplierStats()
    {
        $totalSuppliers = $this->supplierModel->countAllResults();
        $activeSuppliers = $this->supplierModel->where('status', 'Active')->countAllResults();
        $verifiedSuppliers = $this->supplierModel->where('is_verified', 1)->countAllResults();
        
        // Get top performing suppliers
        $db = \Config\Database::connect();
        $topSuppliers = $db->query("
            SELECT nama_supplier, rating, total_orders, on_time_delivery_rate
            FROM suppliers 
            WHERE status = 'Active' 
            ORDER BY rating DESC, total_orders DESC 
            LIMIT 5
        ")->getResultArray();

        return [
            'total' => $totalSuppliers,
            'active' => $activeSuppliers,
            'verified' => $verifiedSuppliers,
            'top_performers' => $topSuppliers
        ];
    }

    // ======================================================================
    // DELIVERY WORKFLOW API ENDPOINTS
    // ======================================================================

    /**
     * Get Delivery Data for DataTable
     */
    public function getDeliveryData()
    {
        try {
            $db = \Config\Database::connect();
            
            // Check if po_deliveries table exists
            if (!$db->tableExists('po_deliveries')) {
                log_message('warning', 'Table po_deliveries does not exist');
                return $this->respond([
                    'draw' => intval($this->request->getPost('draw') ?? 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Table po_deliveries belum dibuat. Silakan jalankan database migration terlebih dahulu.'
                ]);
            }
            
            // Get request parameters
            $draw = $this->request->getPost('draw') ?? 1;
            $start = $this->request->getPost('start') ?? 0;
            $length = $this->request->getPost('length') ?? 10;
            
            // Debug logging
            log_message('info', 'getDeliveryData - draw: ' . $draw . ', start: ' . $start . ', length: ' . $length);
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            $orderColumn = $this->request->getPost('order')[0]['column'] ?? 0;
            $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';
            
            // Column mapping
            $columns = [
                'packing_list_no',
                'no_po', 
                'nama_supplier',
                'delivery_date',
                'driver_name',
                'total_items',
                'status'
            ];
            
            $orderBy = $columns[$orderColumn] ?? 'delivery_date';
            
            // Build query
            $query = "
                SELECT 
                    pd.id_delivery,
                    pd.po_id,
                    pd.packing_list_no,
                    po.no_po,
                    s.nama_supplier,
                    pd.delivery_date,
                    pd.driver_name,
                    pd.status,
                    pd.serial_numbers,
                    pd.total_items,
                    pd.total_value
                FROM po_deliveries pd
                LEFT JOIN purchase_orders po ON pd.po_id = po.id_po
                LEFT JOIN suppliers s ON po.supplier_id = s.id_supplier
                WHERE 1=1
            ";
            
            $params = [];
            
            // Add search filter
            if (!empty($searchValue)) {
                $query .= " AND (
                    pd.packing_list_no LIKE ? OR 
                    po.no_po LIKE ? OR 
                    s.nama_supplier LIKE ? OR 
                    pd.driver_name LIKE ?
                )";
                $searchTerm = "%{$searchValue}%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            
            // Add ordering
            $query .= " ORDER BY {$orderBy} {$orderDir}";
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM po_deliveries pd 
                          LEFT JOIN purchase_orders po ON pd.po_id = po.id_po 
                          LEFT JOIN suppliers s ON po.supplier_id = s.id_supplier";
            $totalRecords = $db->query($countQuery)->getRow()->total ?? 0;
            
            // Get filtered count
            if (!empty($params)) {
                $countFilteredQuery = "
                    SELECT COUNT(*) as total 
                    FROM po_deliveries pd
                    LEFT JOIN purchase_orders po ON pd.po_id = po.id_po
                    LEFT JOIN suppliers s ON po.supplier_id = s.id_supplier
                    WHERE 1=1 AND (
                        pd.packing_list_no LIKE ? OR 
                        po.no_po LIKE ? OR 
                        s.nama_supplier LIKE ? OR 
                        pd.driver_name LIKE ?
                    )
                ";
                $filteredRecords = $db->query($countFilteredQuery, $params)->getRow()->total ?? 0;
            } else {
                $filteredRecords = $totalRecords;
            }
            
            // Add pagination
            $query .= " LIMIT {$start}, {$length}";
            
            // Execute query
            $results = $db->query($query, $params)->getResultArray();
            
            // Debug logging
            log_message('info', 'getDeliveryData - Found ' . count($results) . ' deliveries');
            log_message('info', 'getDeliveryData - Results: ' . json_encode($results));
            
            return $this->respond([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getDeliveryData: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->respond([
                'draw' => intval($this->request->getPost('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load delivery data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create New Delivery
     */
    public function createDelivery()
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();
            
            $poId = $this->request->getPost('po_id');
            $deliveryDate = $this->request->getPost('delivery_date');
            $packingListNo = $this->request->getPost('packing_list_no');
            $driverName = $this->request->getPost('driver_name');
            $driverPhone = $this->request->getPost('driver_phone');
            $vehicleInfo = $this->request->getPost('vehicle_info');
            $vehiclePlate = $this->request->getPost('vehicle_plate');
            $notes = $this->request->getPost('notes');
            $items = $this->request->getPost('items'); // Array of selected items for delivery
            
            // Debug logging
            log_message('info', 'Create Delivery Debug - PO ID: ' . $poId);
            log_message('info', 'Create Delivery Debug - Delivery Date: ' . $deliveryDate);
            log_message('info', 'Create Delivery Debug - Packing List No: ' . $packingListNo);
            log_message('info', 'Create Delivery Debug - Items (raw): ' . $items);
            log_message('info', 'Create Delivery Debug - Items type: ' . gettype($items));
            
            // Validate required fields
            if (empty($poId) || empty($deliveryDate) || empty($packingListNo)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'PO ID, tanggal delivery, dan packing list number harus diisi'
                ]);
            }
            
            // Check if packing list number already exists
            $existingPackingList = $db->query("SELECT id_delivery FROM po_deliveries WHERE packing_list_no = ?", [$packingListNo])->getRow();
            if ($existingPackingList) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Nomor packing list "' . $packingListNo . '" sudah terdaftar. Silakan gunakan nomor yang berbeda.'
                ]);
            }
            
            // Parse items from JSON string
            if (is_string($items)) {
                $items = json_decode($items, true);
                log_message('info', 'Create Delivery Debug - Items after JSON decode: ' . json_encode($items));
            }
            
            // Validate items selection
            if (empty($items) || !is_array($items) || count($items) === 0) {
                log_message('error', 'Create Delivery Debug - Items validation failed. Items: ' . json_encode($items));
                log_message('error', 'Create Delivery Debug - Items count: ' . (is_array($items) ? count($items) : 'not array'));
                return $this->respond([
                    'success' => false,
                    'message' => 'Pilih minimal satu item untuk dikirim'
                ]);
            }
            
            // Get delivery sequence for this PO
            $sequenceQuery = $db->query("SELECT COALESCE(MAX(delivery_sequence), 0) + 1 as next_seq FROM po_deliveries WHERE po_id = ?", [$poId]);
            $nextSequence = $sequenceQuery->getRow()->next_seq;
            
            // Calculate total items for delivery
            $totalItems = 0;
            foreach ($items as $item) {
                $totalItems += intval($item['qty']);
            }
            
            // Insert delivery record
            $deliveryData = [
                'po_id' => $poId,
                'delivery_sequence' => $nextSequence,
                'packing_list_no' => $packingListNo,
                'delivery_date' => $deliveryDate,
                'expected_date' => $deliveryDate,
                'status' => 'Scheduled',
                'total_items' => $totalItems,
                'total_value' => 0, // Will be calculated later if needed
                'driver_name' => $driverName,
                'driver_phone' => $driverPhone,
                'vehicle_info' => $vehicleInfo,
                'vehicle_plate' => $vehiclePlate,
                'notes' => $notes,
                'serial_numbers' => json_encode($items), // Store selected items as JSON
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('po_deliveries')->insert($deliveryData);
            $deliveryId = $db->insertID();
            
            log_message('info', 'Create Delivery Debug - Insert ID: ' . $deliveryId);
            
            // Insert selected items to po_delivery_items with proper foreign keys
            if (!empty($items) && is_array($items)) {
                log_message('info', 'Create Delivery Debug - Processing ' . count($items) . ' selected items');
                
                foreach ($items as $item) {
                    $itemType = $item['type'] ?? 'unit';
                    $itemId = intval($item['id'] ?? 0);
                    
                    if ($itemId <= 0) {
                        log_message('warning', 'Create Delivery Debug - Skipping item with invalid ID: ' . $itemId);
                        continue;
                    }
                    
                    // Prepare item data based on type
                    $itemData = [
                        'delivery_id' => $deliveryId,
                        'po_id' => $poId,
                        'item_type' => $itemType,
                        'qty' => 1, // Always 1 for checklist items
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // Set foreign key based on item type
                    if ($itemType === 'unit') {
                        $itemData['id_po_unit'] = $itemId;
                        $itemData['id_po_attachment'] = null;
                        
                        // Get unit details with complete format
                        $unitQuery = $db->query("
                            SELECT 
                                pu.*,
                                pu.kapasitas_id,
                                COALESCE(mu.model_unit, 'Unknown Model') as model_name,
                                COALESCE(mu.merk_unit, 'Unknown Brand') as brand_name,
                                COALESCE(tu.jenis, 'Unknown Jenis') as jenis,
                                COALESCE(d.nama_departemen, 'Unknown Departemen') as departemen,
                                COALESCE(k.kapasitas_unit, 'Unknown Kapasitas') as kapasitas
                            FROM po_units pu
                            LEFT JOIN model_unit mu ON pu.model_unit_id = mu.id_model_unit
                            LEFT JOIN tipe_unit tu ON pu.tipe_unit_id = tu.id_tipe_unit
                            LEFT JOIN departemen d ON tu.id_departemen = d.id_departemen
                            LEFT JOIN kapasitas k ON pu.kapasitas_id = k.id_kapasitas
                            WHERE pu.id_po_unit = ?
                        ", [$itemId]);
                        
                        if ($unitQuery) {
                            $unit = $unitQuery->getRow();
                        } else {
                            $unit = false;
                        }
                        if ($unit) {
                            $itemData['item_name'] = $unit->brand_name . ' | ' . $unit->model_name . ' | ' . $unit->jenis . ' | ' . $unit->departemen . ' | ' . $unit->kapasitas;
                            $itemData['item_description'] = '';
                        } else {
                            $itemData['item_name'] = 'Unit Item';
                            $itemData['item_description'] = 'Unit Item';
                        }
                    } else {
                        $itemData['id_po_unit'] = null;
                        $itemData['id_po_attachment'] = $itemId;
                        
                        // Get attachment/battery/charger details based on item type
                        if ($itemType === 'attachment') {
                            $attachmentQuery = $db->query("
                                SELECT 
                                    pa.*,
                                    COALESCE(a.merk, 'Unknown Merk') as merk_attachment,
                                    COALESCE(a.model, 'Unknown Model') as model_attachment,
                                    COALESCE(a.tipe, 'Unknown Tipe') as tipe_attachment
                                FROM po_attachment pa
                                LEFT JOIN attachment a ON pa.attachment_id = a.id_attachment
                                WHERE pa.id_po_attachment = ?
                            ", [$itemId]);
                            
                            if ($attachmentQuery) {
                                $attachment = $attachmentQuery->getRow();
                            } else {
                                $attachment = false;
                            }
                            if ($attachment) {
                                $itemData['item_name'] = $attachment->merk_attachment . ' ' . $attachment->model_attachment . ' - ' . $attachment->tipe_attachment;
                                $itemData['item_description'] = '';
                            } else {
                                $itemData['item_name'] = 'Attachment Item';
                                $itemData['item_description'] = 'Attachment Item';
                            }
                        } elseif ($itemType === 'battery') {
                            $batteryQuery = $db->query("
                                SELECT 
                                    pa.*,
                                    COALESCE(b.merk_baterai, 'Unknown Merk') as merk_battery,
                                    COALESCE(b.tipe_baterai, 'Unknown Tipe') as tipe_battery,
                                    COALESCE(b.jenis_baterai, 'Unknown Jenis') as jenis_battery
                                FROM po_attachment pa
                                LEFT JOIN baterai b ON pa.baterai_id = b.id
                                WHERE pa.id_po_attachment = ?
                            ", [$itemId]);
                            
                            if ($batteryQuery) {
                                $battery = $batteryQuery->getRow();
                            } else {
                                $battery = false;
                            }
                            if ($battery) {
                                $itemData['item_name'] = $battery->merk_battery . ' | ' . $battery->tipe_battery . ' | ' . $battery->jenis_battery;
                                $itemData['item_description'] = '';
                            } else {
                                $itemData['item_name'] = 'Battery Item';
                                $itemData['item_description'] = 'Battery Item';
                            }
                        } elseif ($itemType === 'charger') {
                            $chargerQuery = $db->query("
                                SELECT 
                                    pa.*,
                                    COALESCE(c.merk_charger, 'Unknown Merk') as merk_charger,
                                    COALESCE(c.tipe_charger, 'Unknown Tipe') as tipe_charger
                                FROM po_attachment pa
                                LEFT JOIN charger c ON pa.charger_id = c.id_charger
                                WHERE pa.id_po_attachment = ?
                            ", [$itemId]);
                            
                            if ($chargerQuery) {
                                $charger = $chargerQuery->getRow();
                            } else {
                                $charger = false;
                            }
                            if ($charger) {
                                $itemData['item_name'] = $charger->merk_charger . ' | ' . $charger->tipe_charger;
                                $itemData['item_description'] = '';
                            } else {
                                $itemData['item_name'] = 'Charger Item';
                                $itemData['item_description'] = 'Charger Item';
                            }
                        } else {
                            $itemData['item_name'] = ucfirst($itemType) . ' Item';
                            $itemData['item_description'] = ucfirst($itemType) . ' Item';
                        }
                    }
                    
                    try {
                        $db->table('po_delivery_items')->insert($itemData);
                        log_message('info', 'Create Delivery Debug - Inserted item: ' . $itemData['item_name']);
                    } catch (\Exception $e) {
                        log_message('error', 'Create Delivery Debug - Failed to insert item: ' . $e->getMessage());
                        $db->transRollback();
                        return $this->respond([
                            'success' => false,
                            'message' => 'Gagal menyimpan item "' . $itemData['item_name'] . '": ' . $e->getMessage()
                        ]);
                    }
                }
                
                log_message('info', 'Create Delivery Debug - Completed inserting items to po_delivery_items');
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Create Delivery Debug - Transaction failed: ' . json_encode($error));
                
                // Provide specific error messages based on the error
                if (isset($error['code'])) {
                    switch ($error['code']) {
                        case 1062: // Duplicate entry
                            return $this->respond([
                                'success' => false,
                                'message' => 'Data duplikat ditemukan. Silakan coba lagi dengan data yang berbeda.'
                            ]);
                        case 1452: // Foreign key constraint
                            return $this->respond([
                                'success' => false,
                                'message' => 'Data referensi tidak valid. Silakan periksa kembali data yang dipilih.'
                            ]);
                        default:
                            return $this->respond([
                                'success' => false,
                                'message' => 'Terjadi kesalahan database: ' . ($error['message'] ?? 'Unknown error')
                            ]);
                    }
                }
                
                return $this->respond([
                    'success' => false,
                    'message' => 'Gagal menyimpan data delivery. Silakan coba lagi.'
                ]);
            }
            
            // Send notification
            try {
                helper('notification');
                
                // Get PO and supplier info
                $poQuery = $db->query('
                    SELECT po.no_po, s.nama_supplier
                    FROM purchase_orders po
                    LEFT JOIN suppliers s ON po.supplier_id = s.id_supplier
                    WHERE po.id_po = ?
                ', [$poId]);
                $poInfo = $poQuery->getRow();
                
                // Send general delivery notification (for DI workflow)
                notify_delivery_created([
                    'id' => $deliveryId,
                    'delivery_number' => $packingListNo,
                    'nomor_delivery' => $packingListNo,  // Alias for template
                    'po_number' => $poInfo ? $poInfo->no_po : 'Unknown PO',
                    'supplier_name' => $poInfo ? $poInfo->nama_supplier : 'Unknown Supplier',
                    'customer' => $poInfo ? $poInfo->nama_supplier : 'Unknown Supplier',  // Alias
                    'customer_name' => $poInfo ? $poInfo->nama_supplier : 'Unknown Supplier',  // Standard
                    'delivery_date' => $deliveryDate,
                    'items_count' => $totalItems,
                    'created_by' => session()->get('user_name') ?? 'System',
                    'url' => base_url('/purchasing/delivery-detail/' . $deliveryId),
                    'module' => 'purchasing'
                ]);
                
                // Send PO-specific delivery notification (for Warehouse & Purchasing cross-division)
                if (function_exists('notify_po_delivery_created')) {
                    notify_po_delivery_created([
                        'delivery_id' => $deliveryId,
                        'po_id' => $poId,
                        'po_number' => $poInfo ? $poInfo->no_po : 'Unknown PO',
                        'supplier_name' => $poInfo ? $poInfo->nama_supplier : 'Unknown Supplier',
                        'delivery_date' => $deliveryDate,
                        'delivery_type' => 'Purchase Order Delivery',
                        'item_count' => $totalItems,
                        'total_quantity' => $totalItems,
                        'created_by' => session()->get('user_name') ?? 'System',
                        'created_at' => date('Y-m-d H:i:s'),
                        'notes' => $notes ?? '',
                        'url' => base_url('/purchasing/delivery-detail/' . $deliveryId)
                    ]);
                }
                
                log_message('info', "Delivery created: {$packingListNo} - Notification sent");
            } catch (\Exception $notifError) {
                log_message('error', 'Failed to send delivery notification: ' . $notifError->getMessage());
            }
            
            return $this->respond([
                'success' => true,
                'message' => 'Delivery schedule berhasil dibuat',
                'delivery_id' => $deliveryId,
                'packing_list_no' => $packingListNo
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in createDelivery: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->respond([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat delivery: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get PO Items for Delivery Selection
     */
    public function getDeliveryItems($poId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Get delivery ID from request
            $deliveryId = $this->request->getGet('delivery_id');
            
            if ($deliveryId) {
                // Get items from specific delivery (for SN assignment)
                return $this->getDeliveryItemsForSN($deliveryId);
            }
            
            // Get PO details with totals
            $poQuery = $db->query("
                SELECT 
                    po.*,
                    s.nama_supplier,
                    po.no_po as nomor_po,
                    COALESCE(po.total_unit, 0) as total_unit,
                    COALESCE(po.total_attachment, 0) as total_attachment,
                    COALESCE(po.total_battery, 0) as total_battery,
                    COALESCE(po.total_charger, 0) as total_charger
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id_supplier
                WHERE po.id_po = ?
            ", [$poId]);
            
            $po = $poQuery->getRow();
            if (!$po) {
                return $this->respond([
                    'success' => false,
                    'message' => 'PO tidak ditemukan'
                ]);
            }
            
            // Get delivered items to check which items are already in any delivery
            // Any item that exists in po_delivery_items should be disabled
            $deliveredItemsQuery = $db->query("
                SELECT 
                    pdi.item_type,
                    pdi.id_po_unit,
                    pdi.id_po_attachment
                FROM po_delivery_items pdi
                LEFT JOIN po_deliveries pd ON pdi.delivery_id = pd.id_delivery
                WHERE pd.po_id = ?
            ", [$poId]);
            
            $deliveredItems = $deliveredItemsQuery->getResult();
            $deliveredUnits = [];
            $deliveredAttachments = [];
            
            // Log for debugging
            log_message('info', 'Found ' . count($deliveredItems) . ' delivered items for PO: ' . $poId);
            
            foreach ($deliveredItems as $item) {
                log_message('info', 'Delivered item - Type: ' . $item->item_type . ', Unit ID: ' . $item->id_po_unit . ', Attachment ID: ' . $item->id_po_attachment);
                
                if ($item->item_type === 'unit' && $item->id_po_unit) {
                    $deliveredUnits[] = $item->id_po_unit;
                } elseif (in_array($item->item_type, ['attachment', 'battery', 'charger']) && $item->id_po_attachment) {
                    $deliveredAttachments[] = $item->id_po_attachment;
                }
            }
            
            log_message('info', 'Delivered units: ' . json_encode($deliveredUnits));
            log_message('info', 'Delivered attachments: ' . json_encode($deliveredAttachments));
            
            // Build items array with detailed information
            $items = [
                'units' => [],
                'attachments' => [],
                'batteries' => [],
                'chargers' => [],
                'delivered_units' => $deliveredUnits,
                'delivered_attachments' => $deliveredAttachments
            ];
            
            // Get detailed unit items - ALL units from PO (not grouped)
            if ($po->total_unit > 0) {
                $unitsQuery = $db->query("
                    SELECT 
                        pu.id_po_unit,
                        pu.model_unit_id,
                        pu.tipe_unit_id,
                        pu.kapasitas_id,
                        COALESCE(mu.model_unit, 'Unknown Model') as model_name,
                        COALESCE(mu.merk_unit, 'Unknown Brand') as brand_name,
                        COALESCE(tu.jenis, 'Unknown Jenis') as jenis,
                        COALESCE(d.nama_departemen, 'Unknown Departemen') as departemen,
                        COALESCE(k.kapasitas_unit, 'Unknown Kapasitas') as kapasitas
                    FROM po_units pu
                    LEFT JOIN model_unit mu ON pu.model_unit_id = mu.id_model_unit
                    LEFT JOIN tipe_unit tu ON pu.tipe_unit_id = tu.id_tipe_unit
                    LEFT JOIN departemen d ON tu.id_departemen = d.id_departemen
                    LEFT JOIN kapasitas k ON pu.kapasitas_id = k.id_kapasitas
                    WHERE pu.po_id = ?
                    ORDER BY pu.id_po_unit
                ", [$poId]);
                
                $units = $unitsQuery->getResult();
                foreach ($units as $unit) {
                    $isDelivered = in_array($unit->id_po_unit, $deliveredUnits);
                    log_message('info', 'Unit ' . $unit->id_po_unit . ' is_delivered: ' . ($isDelivered ? 'true' : 'false'));
                    
                    $items['units'][] = [
                        'id_po_unit' => $unit->id_po_unit,
                        'item_name' => $unit->brand_name . ' | ' . $unit->model_name . ' | ' . $unit->jenis . ' | ' . $unit->departemen . ' | ' . $unit->kapasitas,
                        'item_description' => '',
                        'qty' => 1,
                        'is_delivered' => $isDelivered
                    ];
                }
            }
            
            // Get detailed attachment items - ALL attachments from PO (not grouped)
            if ($po->total_attachment > 0) {
                $attachmentsQuery = $db->query("
                    SELECT 
                        pa.id_po_attachment,
                        pa.item_type,
                        pa.qty_ordered,
                        pa.serial_number,
                        pa.attachment_id,
                        COALESCE(a.merk, 'Unknown') as merk_attachment,
                        COALESCE(a.model, 'Unknown') as model_attachment,
                        COALESCE(a.tipe, 'Unknown') as tipe_attachment
                    FROM po_attachment pa
                    LEFT JOIN attachment a ON pa.attachment_id = a.id_attachment
                    WHERE pa.po_id = ? AND pa.item_type = 'Attachment'
                    ORDER BY pa.id_po_attachment
                ", [$poId]);
                
                $attachments = $attachmentsQuery->getResult();
                foreach ($attachments as $attachment) {
                    $isDelivered = in_array($attachment->id_po_attachment, $deliveredAttachments);
                    log_message('info', 'Attachment ' . $attachment->id_po_attachment . ' is_delivered: ' . ($isDelivered ? 'true' : 'false'));
                    
                    $itemName = $attachment->merk_attachment . ' ' . $attachment->model_attachment;
                    $itemDescription = $attachment->merk_attachment . ' ' . $attachment->model_attachment . ' - ' . $attachment->tipe_attachment;
                    
                $items['attachments'][] = [
                    'id_po_attachment' => $attachment->id_po_attachment,
                    'item_name' => $attachment->merk_attachment . ' ' . $attachment->model_attachment . ' - ' . $attachment->tipe_attachment,
                    'item_description' => '',
                    'qty' => 1,
                    'is_delivered' => $isDelivered
                ];
                }
            }
            
            // Get detailed battery items - ALL batteries from PO (not grouped)
            if ($po->total_battery > 0) {
                $batteriesQuery = $db->query("
                    SELECT 
                        pa.id_po_attachment,
                        pa.item_type,
                        pa.qty_ordered,
                        pa.serial_number,
                        pa.baterai_id,
                        COALESCE(b.merk_baterai, 'Unknown') as merk_baterai,
                        COALESCE(b.tipe_baterai, 'Unknown') as tipe_baterai,
                        COALESCE(b.jenis_baterai, 'Unknown') as jenis_baterai
                    FROM po_attachment pa
                    LEFT JOIN baterai b ON pa.baterai_id = b.id
                    WHERE pa.po_id = ? AND pa.item_type = 'Battery'
                    ORDER BY pa.id_po_attachment
                ", [$poId]);
                
                $batteries = $batteriesQuery->getResult();
                foreach ($batteries as $battery) {
                    $isDelivered = in_array($battery->id_po_attachment, $deliveredAttachments);
                    $itemName = $battery->merk_baterai;
                    $itemDescription = $battery->merk_baterai . ' ' . $battery->tipe_baterai . ' ' . $battery->jenis_baterai;
                    
                $items['batteries'][] = [
                    'id_po_attachment' => $battery->id_po_attachment,
                    'item_name' => $battery->merk_baterai . ' | ' . $battery->tipe_baterai . ' | ' . $battery->jenis_baterai,
                    'item_description' => '',
                    'qty' => 1,
                    'is_delivered' => $isDelivered
                ];
                }
            }
            
            // Get detailed charger items - ALL chargers from PO (not grouped)
            if ($po->total_charger > 0) {
                $chargersQuery = $db->query("
                    SELECT 
                        pa.id_po_attachment,
                        pa.item_type,
                        pa.qty_ordered,
                        pa.serial_number,
                        pa.charger_id,
                        COALESCE(c.merk_charger, 'Unknown') as merk_charger,
                        COALESCE(c.tipe_charger, 'Unknown') as tipe_charger
                    FROM po_attachment pa
                    LEFT JOIN charger c ON pa.charger_id = c.id_charger
                    WHERE pa.po_id = ? AND pa.item_type = 'Charger'
                    ORDER BY pa.id_po_attachment
                ", [$poId]);
                
                $chargers = $chargersQuery->getResult();
                foreach ($chargers as $charger) {
                    $isDelivered = in_array($charger->id_po_attachment, $deliveredAttachments);
                    $itemName = $charger->merk_charger;
                    $itemDescription = $charger->merk_charger . ' ' . $charger->tipe_charger;
                    
                $items['chargers'][] = [
                    'id_po_attachment' => $charger->id_po_attachment,
                    'item_name' => $charger->merk_charger . ' ' . $charger->tipe_charger,
                    'item_description' => '',
                    'qty' => 1,
                    'is_delivered' => $isDelivered
                ];
                }
            }
            
            return $this->respond([
                'success' => true,
                'po' => $po,
                'items' => $items
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getDeliveryItems: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->respond([
                'success' => false,
                'message' => 'Gagal memuat data items: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get Delivery Items for SN Assignment (based on serial_numbers from delivery)
     */
    public function getDeliveryItemsForSN($deliveryId)
    {
        try {
            $db = \Config\Database::connect();
            
            log_message('info', 'getDeliveryItemsForSN called with deliveryId: ' . $deliveryId);
            
            // Get delivery with serial_numbers
            $deliveryQuery = $db->query("
                SELECT 
                    pd.*,
                    po.no_po,
                    s.nama_supplier
                FROM po_deliveries pd
                LEFT JOIN purchase_orders po ON pd.po_id = po.id_po
                LEFT JOIN suppliers s ON po.supplier_id = s.id_supplier
                WHERE pd.id_delivery = ?
            ", [$deliveryId]);
            
            $delivery = $deliveryQuery->getRow();
            log_message('info', 'Delivery query result: ' . json_encode($delivery));
            
            if (!$delivery) {
                log_message('error', 'Delivery not found for ID: ' . $deliveryId);
                return $this->respond([
                    'success' => false,
                    'message' => 'Delivery tidak ditemukan'
                ]);
            }
            
            // Get delivery ID for querying po_delivery_items
            $deliveryId = $delivery->id_delivery;
            log_message('info', 'Getting delivery items for delivery ID: ' . $deliveryId);
            
            // Get items from po_delivery_items table (only items for this delivery)
            $deliveryItemsQuery = $db->query("
                SELECT 
                    pdi.*,
                    mu.model_unit,
                    mu.merk_unit,
                    tu.tipe as jenis,
                    d.nama_departemen,
                    k.kapasitas_unit,
                    a.merk as merk_attachment,
                    a.model as model_attachment,
                    a.tipe as tipe_attachment,
                    b.merk_baterai as merk_battery,
                    b.tipe_baterai as tipe_battery,
                    b.jenis_baterai as jenis_battery,
                    c.merk_charger as merk_charger,
                    c.tipe_charger as tipe_charger
                FROM po_delivery_items pdi
                LEFT JOIN po_units pu ON pdi.id_po_unit = pu.id_po_unit
                LEFT JOIN model_unit mu ON pu.model_unit_id = mu.id_model_unit
                LEFT JOIN tipe_unit tu ON pu.tipe_unit_id = tu.id_tipe_unit
                LEFT JOIN departemen d ON tu.id_departemen = d.id_departemen
                LEFT JOIN kapasitas k ON pu.kapasitas_id = k.id_kapasitas
                LEFT JOIN po_attachment pa ON pdi.id_po_attachment = pa.id_po_attachment
                LEFT JOIN attachment a ON pa.attachment_id = a.id_attachment
                LEFT JOIN baterai b ON pa.baterai_id = b.id
                LEFT JOIN charger c ON pa.charger_id = c.id_charger
                WHERE pdi.delivery_id = ?
                ORDER BY pdi.item_type, pdi.id_delivery_item
            ", [$deliveryId]);
            
            $deliveryItems = $deliveryItemsQuery->getResult();
            
            log_message('info', 'Found ' . count($deliveryItems) . ' items in po_delivery_items for delivery: ' . $deliveryId);
            
            // Build items array from po_delivery_items
            $items = [
                'units' => [],
                'attachments' => [],
                'batteries' => [],
                'chargers' => []
            ];
            
            foreach ($deliveryItems as $item) {
                $itemData = [
                    'id_delivery_item' => $item->id_delivery_item,
                    'item_type' => $item->item_type,
                    'qty' => $item->qty,
                    'item_name' => $item->item_name,
                    'item_description' => $item->item_description,
                    'sn_mast_po' => $item->sn_mast_po,
                    'sn_mesin_po' => $item->sn_mesin_po,
                    'serial_number' => $item->serial_number
                ];
                
                // Add to appropriate category
                if ($item->item_type === 'unit') {
                    $items['units'][] = $itemData;
                } elseif ($item->item_type === 'attachment') {
                    $items['attachments'][] = $itemData;
                } elseif ($item->item_type === 'battery') {
                    $items['batteries'][] = $itemData;
                } elseif ($item->item_type === 'charger') {
                    $items['chargers'][] = $itemData;
                }
            }
            
            
            log_message('info', 'Final items array: ' . json_encode($items));
            
            return $this->respond([
                'success' => true,
                'items' => $items
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getDeliveryItemsForSN: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->respond([
                'success' => false,
                'message' => 'Gagal memuat items untuk SN assignment: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Assign Serial Numbers to Delivery Items
     */
    public function assignSerialNumbers()
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();
            
            $deliveryId = $this->request->getPost('delivery_id');
            $serialNumbers = $this->request->getPost('serial_numbers'); // JSON array
            
            log_message('info', 'Assign SN Debug - Delivery ID: ' . $deliveryId);
            log_message('info', 'Assign SN Debug - Serial Numbers: ' . $serialNumbers);
            
            if (empty($deliveryId) || empty($serialNumbers)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Delivery ID dan serial numbers harus diisi'
                ]);
            }
            
            // Parse serial numbers JSON
            $snData = json_decode($serialNumbers, true);
            if (!$snData || !is_array($snData)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Format serial numbers tidak valid'
                ]);
            }
            
            log_message('info', 'Assign SN Debug - Parsed SN Data: ' . json_encode($snData));
            
            // Get delivery items to match with SN data
            $deliveryItems = $db->query("
                SELECT id_delivery_item, item_type, id_po_unit, id_po_attachment
                FROM po_delivery_items 
                WHERE delivery_id = ?
                ORDER BY item_type, id_delivery_item
            ", [$deliveryId])->getResult();
            
            log_message('info', 'Assign SN Debug - Found ' . count($deliveryItems) . ' delivery items');
            
            // Group items by type and create type-specific indexes
            $itemCounts = ['unit' => 0, 'attachment' => 0, 'battery' => 0, 'charger' => 0];
            
            // Update each item in po_delivery_items with serial numbers
            foreach ($deliveryItems as $item) {
                // Get the correct index for this item type
                $typeIndex = $itemCounts[$item->item_type];
                
                // Find matching SN data by type and type-specific index
                $matchingSnData = null;
                foreach ($snData as $snItem) {
                    if ($snItem['type'] === $item->item_type && $snItem['index'] == $typeIndex) {
                        $matchingSnData = $snItem;
                        break;
                    }
                }
                
                log_message('info', 'Assign SN Debug - Processing item ' . $item->id_delivery_item . 
                    ' type: ' . $item->item_type . ' typeIndex: ' . $typeIndex . 
                    ' matchingData: ' . ($matchingSnData ? 'found' : 'not found'));
                
                if ($matchingSnData) {
                    $updateData = [];
                    
                    // Update based on item type
                    if (isset($matchingSnData['sn_mast'])) {
                        $updateData['sn_mast_po'] = $matchingSnData['sn_mast'];
                    }
                    if (isset($matchingSnData['sn_engine'])) {
                        $updateData['sn_mesin_po'] = $matchingSnData['sn_engine'];
                    }
                    if (isset($matchingSnData['serial_number'])) {
                        $updateData['serial_number'] = $matchingSnData['serial_number'];
                    }
                    
                    if (!empty($updateData)) {
                        $updateData['updated_at'] = date('Y-m-d H:i:s');
                        
                        // Update the specific item by id_delivery_item
                        $db->table('po_delivery_items')
                           ->where('id_delivery_item', $item->id_delivery_item)
                           ->update($updateData);
                           
                        log_message('info', 'Assign SN Debug - Updated item ' . $item->id_delivery_item . 
                            ' (' . $item->item_type . '): ' . json_encode($updateData));
                    }
                }
                
                // Increment the counter for this item type
                $itemCounts[$item->item_type]++;
            }
            
            // Sync SN to master tables (po_units and po_attachment)
            $this->syncSerialNumbersToMasterTables($deliveryId, $db);
            
            // Update delivery status to "In Transit" after SN assignment
            $db->table('po_deliveries')
               ->where('id_delivery', $deliveryId)
               ->update([
                   'status' => 'In Transit',
                   'updated_at' => date('Y-m-d H:i:s')
               ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Assign SN Debug - Transaction failed: ' . json_encode($error));
                return $this->respond([
                    'success' => false,
                    'message' => 'Gagal menyimpan serial numbers: ' . ($error['message'] ?? 'Unknown error')
                ]);
            }
            
            log_message('info', 'Assign SN Debug - Successfully saved SN for delivery: ' . $deliveryId);
            
            return $this->respond([
                'success' => true,
                'message' => 'Serial numbers berhasil disimpan dan status delivery diupdate ke "In Transit"'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in assignSerialNumbers: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->respond([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan serial numbers: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update Delivery Status
     */
    public function updateDeliveryStatus()
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();
            
            $deliveryId = $this->request->getPost('delivery_id');
            $status = $this->request->getPost('status');
            
            if (empty($deliveryId) || empty($status)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Delivery ID dan status harus diisi'
                ]);
            }
            
            // Validate status
            $validStatuses = ['Scheduled', 'In Transit', 'Received', 'Completed', 'Cancelled'];
            if (!in_array($status, $validStatuses)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ]);
            }
            
            // Prepare update data
            $updateData = [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // If status is 'Received', set actual_date to current date
            if ($status === 'Received') {
                $updateData['actual_date'] = date('Y-m-d H:i:s');
            }
            
            // Update delivery status
            $db->table('po_deliveries')
               ->where('id_delivery', $deliveryId)
               ->update($updateData);
            
            // If status is 'Received', trigger warehouse verification
            if ($status === 'Received') {
                $this->triggerWarehouseVerification($deliveryId);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Gagal mengupdate status delivery'
                ]);
            }
            
            // Send notification
            try {
                helper('notification');
                
                // Get delivery and PO info BEFORE update
                $deliveryQuery = $db->query('
                    SELECT d.packing_list_no, d.status as old_status, po.no_po, s.nama_supplier
                    FROM po_deliveries d
                    LEFT JOIN purchase_orders po ON d.po_id = po.id_po
                    LEFT JOIN suppliers s ON po.supplier_id = s.id_supplier
                    WHERE d.id_delivery = ?
                ', [$deliveryId]);
                $deliveryInfo = $deliveryQuery->getRow();
                
                notify_delivery_status_changed([
                    'id' => $deliveryId,
                    'delivery_number' => $deliveryInfo ? $deliveryInfo->packing_list_no : 'Unknown',
                    'po_number' => $deliveryInfo ? $deliveryInfo->no_po : 'Unknown PO',
                    'old_status' => $deliveryInfo ? $deliveryInfo->old_status : 'Unknown',
                    'new_status' => $status,
                    'supplier_name' => $deliveryInfo ? $deliveryInfo->nama_supplier : 'Unknown Supplier',
                    'updated_by' => session()->get('user_name') ?? 'System',
                    'url' => base_url('/purchasing/delivery-detail/' . $deliveryId)
                ]);
                
                log_message('info', "Delivery status updated: {$deliveryId} → {$status} - Notification sent");
            } catch (\Exception $notifError) {
                log_message('error', 'Failed to send delivery status notification: ' . $notifError->getMessage());
            }
            
            return $this->respond([
                'success' => true,
                'message' => 'Status delivery berhasil diupdate'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in updateDeliveryStatus: ' . $e->getMessage());
            return $this->respond([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate Packing List Number
     */
    private function generatePackingListNumber()
    {
        $prefix = 'PL/' . date('Ym') . '/';
        
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COALESCE(MAX(CAST(SUBSTRING(packing_list_no, LENGTH(?) + 1) AS UNSIGNED)), 0) + 1 as next_seq
            FROM po_deliveries 
            WHERE packing_list_no LIKE CONCAT(?, '%')
        ", [$prefix, $prefix]);
        
        $nextSeq = $query->getRow()->next_seq;
        return $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Trigger Warehouse Verification
     */
    private function triggerWarehouseVerification($deliveryId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Get delivery details
            $deliveryQuery = $db->query("
                SELECT pd.*, po.no_po 
                FROM po_deliveries pd
                LEFT JOIN purchase_orders po ON pd.po_id = po.id_po
                WHERE pd.id_delivery = ?
            ", [$deliveryId]);
            
            $delivery = $deliveryQuery->getRow();
            if (!$delivery) {
                return false;
            }
            
            // Create warehouse verification record
            $verificationData = [
                'delivery_id' => $deliveryId,
                'po_id' => $delivery->po_id,
                'packing_list_no' => $delivery->packing_list_no,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Insert into warehouse verification table (if exists)
            // This would integrate with existing warehouse system
            log_message('info', "Warehouse verification triggered for delivery ID: {$deliveryId}");
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in triggerWarehouseVerification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync serial numbers from po_delivery_items to master tables (po_units and po_attachment)
     */
    private function syncSerialNumbersToMasterTables($deliveryId, $db)
    {
        try {
            log_message('info', 'Starting SN sync to master tables for delivery: ' . $deliveryId);
            
            // Get all delivery items with their SN data
            $deliveryItems = $db->query("
                SELECT 
                    pdi.item_type,
                    pdi.id_po_unit,
                    pdi.id_po_attachment,
                    pdi.sn_mast_po,
                    pdi.sn_mesin_po,
                    pdi.serial_number
                FROM po_delivery_items pdi
                WHERE pdi.delivery_id = ?
                AND (pdi.sn_mast_po IS NOT NULL OR pdi.sn_mesin_po IS NOT NULL OR pdi.serial_number IS NOT NULL)
            ", [$deliveryId])->getResult();
            
            foreach ($deliveryItems as $item) {
                if ($item->item_type === 'unit' && $item->id_po_unit) {
                    // Sync to po_units table
                    $updateData = [];
                    if (!empty($item->sn_mast_po)) {
                        $updateData['sn_mast_po'] = $item->sn_mast_po;
                    }
                    if (!empty($item->sn_mesin_po)) {
                        $updateData['sn_mesin_po'] = $item->sn_mesin_po;
                    }
                    if (!empty($item->serial_number)) {
                        $updateData['serial_number_po'] = $item->serial_number;
                    }
                    
                    if (!empty($updateData)) {
                        $updateData['updated_at'] = date('Y-m-d H:i:s');
                        
                        $db->table('po_units')
                           ->where('id_po_unit', $item->id_po_unit)
                           ->update($updateData);
                           
                        log_message('info', 'Synced unit SN to po_units: ' . $item->id_po_unit . ' - ' . json_encode($updateData));
                    }
                    
                } elseif (in_array($item->item_type, ['attachment', 'battery', 'charger']) && $item->id_po_attachment) {
                    // Sync to po_attachment table
                    $updateData = [];
                    if (!empty($item->serial_number)) {
                        $updateData['serial_number'] = $item->serial_number;
                    }
                    
                    if (!empty($updateData)) {
                        $updateData['updated_at'] = date('Y-m-d H:i:s');
                        
                        $db->table('po_attachment')
                           ->where('id_po_attachment', $item->id_po_attachment)
                           ->update($updateData);
                           
                        log_message('info', 'Synced attachment SN to po_attachment: ' . $item->id_po_attachment . ' - ' . json_encode($updateData));
                    }
                }
            }
            
            log_message('info', 'Completed SN sync to master tables for delivery: ' . $deliveryId);
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in syncSerialNumbersToMasterTables: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================
    // SUPPLIER MANAGEMENT CRUD METHODS
    // ========================================

    /**
     * Generate supplier code
     */
    public function generateSupplierCode()
    {
        try {
            $newCode = $this->generateUniqueSupplierCode();
            
            return $this->response->setJSON([
                'success' => true,
                'code' => $newCode
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal generate kode supplier: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store new supplier
     */
    public function storeSupplier()
    {
        try {
            // Always generate kode supplier to ensure uniqueness
            $kodeSupplier = $this->generateUniqueSupplierCode();
            
            // Debug logging
            log_message('info', 'Generated kode supplier: ' . $kodeSupplier);
            
            if (empty($kodeSupplier)) {
                log_message('error', 'Kode supplier kosong! Method generateUniqueSupplierCode() return empty');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal generate kode supplier - kode kosong'
                ]);
            }
            
            // Double check kode supplier tidak kosong
            if (empty($kodeSupplier)) {
                $kodeSupplier = $this->generateSequentialFallbackCode();
                log_message('warning', 'Kode supplier masih kosong, menggunakan fallback: ' . $kodeSupplier);
            }
            
            $data = [
                'kode_supplier' => $kodeSupplier,
                'nama_supplier' => $this->request->getPost('nama_supplier'),
                'business_type' => $this->request->getPost('business_type'),
                'status' => 'Active', // Default status untuk supplier baru
                'contact_person' => $this->request->getPost('contact_person'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'website' => $this->request->getPost('website'),
                'address' => $this->request->getPost('address'),
                'notes' => $this->request->getPost('notes'),
            ];

            // Debug: Log data before insert
            log_message('info', 'Kode supplier in data: ' . $data['kode_supplier']);

            if ($this->supplierModel->insert($data)) {
                $supplierId = $this->supplierModel->getInsertID();
                
                // Send notification: Supplier Created
                helper('notification');
                notify_supplier_created([
                    'id' => $supplierId,
                    'kode_supplier' => $kodeSupplier,
                    'nama_supplier' => $data['nama_supplier'],
                    'business_type' => $data['business_type'],
                    'created_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/purchasing/supplier-management')
                ]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Supplier berhasil ditambahkan dengan kode: ' . $kodeSupplier
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menambahkan supplier: ' . implode(', ', $this->supplierModel->errors())
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate unique supplier code
     */
    private function generateUniqueSupplierCode()
    {
        try {
            $db = \Config\Database::connect();
            $year = date('Y');
            
            log_message('info', 'Starting generateUniqueSupplierCode for year: ' . $year);
            
            // Get the highest number for current year
            $lastSupplier = $db->table('suppliers')
                ->select('kode_supplier')
                ->like('kode_supplier', 'SUP-' . $year . '-', 'after')
                ->orderBy('kode_supplier', 'DESC')
                ->limit(1)
                ->get()
                ->getRow();
            
            if ($lastSupplier && !empty($lastSupplier->kode_supplier)) {
                // Extract number from last code
                preg_match('/SUP-' . $year . '-(\d{3})/', $lastSupplier->kode_supplier, $matches);
                if ($matches) {
                    $number = intval($matches[1]) + 1;
                } else {
                    $number = 1;
                }
            } else {
                $number = 1;
            }
            
            // Format: SUP-YYYY-XXX
            $newCode = 'SUP-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            
            // Double check if code already exists (safety check)
            $existingSupplier = $db->table('suppliers')
                ->where('kode_supplier', $newCode)
                ->countAllResults();
            
            if ($existingSupplier > 0) {
                // If exists, increment by 1 and try again
                $number++;
                $newCode = 'SUP-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
                
                // Final check
                $existingSupplier2 = $db->table('suppliers')
                    ->where('kode_supplier', $newCode)
                    ->countAllResults();
                
                if ($existingSupplier2 > 0) {
                    // If still exists, find the next available number
                    $maxNumber = $db->table('suppliers')
                        ->select('kode_supplier')
                        ->like('kode_supplier', 'SUP-' . $year . '-', 'after')
                        ->get()
                        ->getResult();
                    
                    $maxNum = 0;
                    foreach ($maxNumber as $supplier) {
                        preg_match('/SUP-' . $year . '-(\d{3})/', $supplier->kode_supplier, $matches);
                        if ($matches && intval($matches[1]) > $maxNum) {
                            $maxNum = intval($matches[1]);
                        }
                    }
                    $number = $maxNum + 1;
                    $newCode = 'SUP-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
                }
            }
            
            log_message('info', 'Generated sequential code: ' . $newCode);
            return $newCode;
            
        } catch (\Exception $e) {
            log_message('error', 'Error generating supplier code: ' . $e->getMessage());
            // Simple sequential fallback without timestamp
            $year = date('Y');
            $fallbackCode = 'SUP-' . $year . '-001';
            log_message('info', 'Using simple fallback code: ' . $fallbackCode);
            return $fallbackCode;
        }
    }

    /**
     * Generate sequential fallback code
     */
    private function generateSequentialFallbackCode()
    {
        try {
            $db = \Config\Database::connect();
            $year = date('Y');
            
            // Get the highest number for current year
            $lastSupplier = $db->table('suppliers')
                ->select('kode_supplier')
                ->like('kode_supplier', 'SUP-' . $year . '-', 'after')
                ->orderBy('kode_supplier', 'DESC')
                ->limit(1)
                ->get()
                ->getRow();
            
            if ($lastSupplier && !empty($lastSupplier->kode_supplier)) {
                preg_match('/SUP-' . $year . '-(\d{3})/', $lastSupplier->kode_supplier, $matches);
                $number = $matches ? intval($matches[1]) + 1 : 1;
            } else {
                $number = 1;
            }
            
            return 'SUP-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            
        } catch (\Exception $e) {
            // Ultimate fallback
            return 'SUP-' . date('Y') . '-001';
        }
    }

    /**
     * Get suppliers list for AJAX refresh
     */
    public function suppliersList()
    {
        try {
            // Get suppliers data
            $suppliers = $this->supplierModel->findAll();
            
            // Get statistics
            $total = $this->supplierModel->countAllResults();
            $active = $this->supplierModel->where('status', 'Active')->countAllResults();
            $verified = $this->supplierModel->where('status', 'Verified')->countAllResults();
            
            // Get total PO (you might need to adjust this based on your PO table)
            $totalPO = 0; // Placeholder - adjust based on your PO table structure
            
            $stats = [
                'total' => $total,
                'active' => $active,
                'verified' => $verified,
                'total_po' => $totalPO
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'suppliers' => $suppliers,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data supplier: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get single supplier by ID
     */
    public function getSupplier($id)
    {
        try {
            $supplier = $this->supplierModel->find($id);
            if ($supplier) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $supplier
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Supplier tidak ditemukan'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update supplier
     */
    public function updateSupplier($id)
    {
        // Check permission for updating supplier
        if (!$this->hasPermission('purchasing.supplier_management.edit')) {
            return redirect()->to('/purchasing/supplier-management')->with('error', 'Access denied: You do not have permission to update suppliers');
        }
        
        try {
            $data = [
                'kode_supplier' => $this->request->getPost('kode_supplier'),
                'nama_supplier' => $this->request->getPost('nama_supplier'),
                'business_type' => $this->request->getPost('business_type'),
                'status' => $this->request->getPost('status'),
                'contact_person' => $this->request->getPost('contact_person'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'website' => $this->request->getPost('website'),
                'address' => $this->request->getPost('address'),
                'notes' => $this->request->getPost('notes'),
            ];

            if ($this->supplierModel->update($id, $data)) {
                // Send notification: Supplier Updated
                helper('notification');
                notify_supplier_updated([
                    'id' => $id,
                    'kode_supplier' => $data['kode_supplier'],
                    'nama_supplier' => $data['nama_supplier'],
                    'business_type' => $data['business_type'],
                    'updated_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/purchasing/supplier-management')
                ]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Supplier berhasil diperbarui'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui supplier: ' . implode(', ', $this->supplierModel->errors())
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update supplier status
     */
    public function updateSupplierStatus($id)
    {
        try {
            $status = $this->request->getPost('status');
            $reason = $this->request->getPost('reason');
            
            if (empty($status)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status tidak boleh kosong'
                ]);
            }
            
            $data = [
                'status' => $status,
                'notes' => $this->request->getPost('notes') // Keep existing notes
            ];
            
            // Add reason to notes if provided
            if (!empty($reason)) {
                $existingSupplier = $this->supplierModel->find($id);
                $existingNotes = $existingSupplier['notes'] ?? '';
                $newNote = '[Status Change: ' . date('Y-m-d H:i:s') . '] ' . $reason;
                $data['notes'] = $existingNotes . ($existingNotes ? "\n" : '') . $newNote;
            }
            
            if ($this->supplierModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status supplier berhasil diubah ke: ' . $status
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengubah status supplier: ' . implode(', ', $this->supplierModel->errors())
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete supplier
     */
    public function deleteSupplier($id)
    {
        try {
            // Get supplier data before delete for notification
            $supplier = $this->supplierModel->find($id);
            
            if ($this->supplierModel->delete($id)) {
                // Send notification: Supplier Deleted
                if ($supplier) {
                    helper('notification');
                    notify_supplier_deleted([
                        'id' => $id,
                        'kode_supplier' => $supplier['kode_supplier'] ?? '',
                        'nama_supplier' => $supplier['nama_supplier'] ?? '',
                        'deleted_by' => session('username') ?? session('user_id'),
                        'url' => base_url('/purchasing/supplier-management')
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Supplier berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus supplier'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export suppliers to Excel
     */
    public function exportSuppliers()
    {
        try {
            $suppliers = $this->supplierModel->findAll();
            
            // Simple CSV export for now
            $filename = 'suppliers_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($output, [
                'Kode Supplier',
                'Nama Supplier', 
                'Business Type',
                'Status',
                'Contact Person',
                'Phone',
                'Email',
                'Website',
                'Address',
                'Notes'
            ]);
            
            // CSV Data
            foreach ($suppliers as $supplier) {
                fputcsv($output, [
                    $supplier['kode_supplier'],
                    $supplier['nama_supplier'],
                    $supplier['business_type'],
                    $supplier['status'],
                    $supplier['contact_person'],
                    $supplier['phone'],
                    $supplier['email'],
                    $supplier['website'],
                    $supplier['address'],
                    $supplier['notes']
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get master data configuration
     */
    private function getMasterDataConfig()
    {
        return [
            'brand' => [
                'title' => 'Brand & Model Unit',
                'model' => 'modelUnitModel',
                'fields' => [
                    ['name' => 'merk_unit', 'label' => 'Brand', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: AVANT, BT, CAT, TOYOTA'],
                    ['name' => 'model_unit', 'label' => 'Model', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: M420MSDTT, RRE160MC, EP15TCA']
                ],
                'return_field' => 'id_model_unit',
                'refresh_related' => ['unit_merk', 'unit_model'],  // Refresh both Brand and Model dropdowns
                'note' => 'Brand dan Model harus diisi bersamaan karena berasal dari tabel yang sama (model_unit)'
            ],
            'model' => [
                'title' => 'Model Unit',
                'model' => 'modelUnitModel',
                'fields' => [
                    ['name' => 'merk_unit', 'label' => 'Brand', 'type' => 'hidden', 'required' => true],
                    ['name' => 'model_unit', 'label' => 'Model', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: M420MSDTT, RRE160MC, EP15TCA']
                ],
                'return_field' => 'id_model_unit'
            ],
            'kapasitas' => [
                'title' => 'Kapasitas',
                'model' => 'kapasitasModel',
                'fields' => [
                    ['name' => 'kapasitas_unit', 'label' => 'Kapasitas', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: 200 kg, 1.5 Ton, 2.5 Ton']
                ],
                'return_field' => 'id_kapasitas'
            ],
            'mast' => [
                'title' => 'Tipe Mast',
                'model' => 'tipeMastModel',
                'fields' => [
                    ['name' => 'tipe_mast', 'label' => 'Tipe Mast', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Duplex (2-stage FFL) - ZM300, Simplex (2-stage mast) - V (3000)'],
                    ['name' => 'tinggi_mast', 'label' => 'Tinggi Mast (Optional)', 'type' => 'text', 'required' => false, 'placeholder' => 'Contoh: 3000mm, 5000mm (opsional)']
                ],
                'return_field' => 'id_mast'
            ],
            'engine' => [
                'title' => 'Tipe Mesin',
                'model' => 'mesinModel',
                'fields' => [
                    ['name' => 'merk_mesin', 'label' => 'Merk Mesin', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: TOYOTA, ISUZU, MITSUBISHI'],
                    ['name' => 'model_mesin', 'label' => 'Model Mesin', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: 1DZ-0196006, 4JG2, 6D34'],
                    ['name' => 'bahan_bakar', 'label' => 'Bahan Bakar', 'type' => 'select', 'required' => true, 'options' => ['Diesel', 'Bensin', 'Electric', 'LPG', 'Hybrid']]
                ],
                'return_field' => 'id'
            ],
            'tire' => [
                'title' => 'Tipe Ban',
                'model' => 'tipeBanModel',
                'fields' => [
                    ['name' => 'tipe_ban', 'label' => 'Tipe Ban', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Solid (Ban Mati), Pneumatic (Ban Angin), Cushion (Ban Bantal)']
                ],
                'return_field' => 'id_ban'
            ],
            'wheel' => [
                'title' => 'Jenis Roda',
                'model' => 'jenisRodaModel',
                'fields' => [
                    ['name' => 'tipe_roda', 'label' => 'Jenis Roda', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: 3-Wheel, 4-Wheel, 4-Way Multi-Directional (FFL)']
                ],
                'return_field' => 'id_roda'
            ],
            'valve' => [
                'title' => 'Jumlah Valve',
                'model' => 'valveModel',
                'fields' => [
                    ['name' => 'jumlah_valve', 'label' => 'Jumlah Valve', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: 2 Valve, 3 Valve, 4 Valve']
                ],
                'return_field' => 'id_valve'
            ],
            'battery' => [
                'title' => 'Battery',
                'model' => 'bateraiModel',
                'fields' => [
                    ['name' => 'merk_baterai', 'label' => 'Merk Battery', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: JUNGHEINRICH (JHR), GS ASTRA'],
                    ['name' => 'tipe_baterai', 'label' => 'Tipe Battery', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: 48V / 775AH AQUAMATIC (5PZS775)'],
                    ['name' => 'jenis_baterai', 'label' => 'Jenis Battery', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Lead Acid, Lithium Ion']
                ],
                'return_field' => 'id',
                'note' => 'Struktur DB: id, merk_baterai, tipe_baterai, jenis_baterai'
            ],
            'attachment_type' => [
                'title' => 'Attachment',
                'model' => 'attachmentModel',
                'fields' => [
                    ['name' => 'tipe', 'label' => 'Tipe Attachment', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: FORK POSITIONER, PAPER ROLL CLAMP, SIDE SHIFTER'],
                    ['name' => 'merk', 'label' => 'Merk', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: CASCADE, KAUP, BOLZONI'],
                    ['name' => 'model', 'label' => 'Model', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: 120K-FPS-CO82, 77F-RCP-01C']
                ],
                'return_field' => 'id_attachment'
            ],
            'charger' => [
                'title' => 'Charger',
                'model' => 'chargerModel',
                'fields' => [
                    ['name' => 'merk_charger', 'label' => 'Merk Charger', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: JUNGHEINRICH, STILL, HAWKER'],
                    ['name' => 'tipe_charger', 'label' => 'Tipe Charger', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: SLT010nDe48/80P(48V / 80A), ECOTRON XM(80V / 125A)']
                ],
                'return_field' => 'id_charger'
            ],
            'departemen' => [
                'title' => 'Departemen',
                'model' => 'departemenModel',
                'fields' => [
                    ['name' => 'nama_departemen', 'label' => 'Nama Departemen', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: DIESEL, ELECTRIC, GASOLINE']
                ],
                'return_field' => 'id_departemen'
            ],
            'jenis_unit' => [
                'title' => 'Jenis Unit',
                'model' => 'tipeUnitModel',
                'fields' => [
                    ['name' => 'id_departemen', 'label' => 'Departemen', 'type' => 'select', 'required' => true, 'data_source' => 'departemenModel'],
                    ['name' => 'tipe', 'label' => 'Tipe', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Forklift, Alat Berat, Alat Kebersihan'],
                    ['name' => 'jenis', 'label' => 'Jenis', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: COUNTER BALANCE, REACH TRUCK, PALLET STACKER']
                ],
                'return_field' => 'id_tipe_unit'
            ]
        ];
    }

    /**
     * Get form configuration for quick add
     */
    public function getQuickAddForm()
    {
        try {
            $type = $this->request->getGet('type');
            $config = $this->getMasterDataConfig();
            
            if (!isset($config[$type])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipe master data tidak ditemukan'
                ]);
            }
            
            $formConfig = $config[$type];
            
            // Process fields to populate data_source for select fields
            foreach ($formConfig['fields'] as &$field) {
                if ($field['type'] === 'select' && isset($field['data_source'])) {
                    // Load data from model
                    $modelName = $field['data_source'];
                    if (property_exists($this, $modelName)) {
                        $model = $this->$modelName;
                        $data = $model->findAll();
                        
                        // Format options based on model
                        $options = [];
                        foreach ($data as $row) {
                            if ($modelName === 'departemenModel') {
                                $options[] = [
                                    'value' => $row['id_departemen'],
                                    'label' => $row['nama_departemen']
                                ];
                            }
                        }
                        $field['options'] = $options;
                    }
                }
            }
            
            // Get additional data if needed
            $additionalData = [];
            if ($type === 'model') {
                // Get current selected brand
                $additionalData['current_brand'] = $this->request->getGet('brand');
            } elseif ($type === 'jenis_unit') {
                // Get current selected departemen
                $additionalData['current_departemen'] = $this->request->getGet('departemen');
            }
            
            return $this->response->setJSON([
                'success' => true,
                'config' => $formConfig,
                'additionalData' => $additionalData
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Quick add master data via AJAX
     */
    public function quickAddMasterData()
    {
        try {
            $type = $this->request->getPost('type');
            $data = $this->request->getPost('data');
            
            $config = $this->getMasterDataConfig();
            
            if (!isset($config[$type])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipe master data tidak valid'
                ]);
            }
            
            $formConfig = $config[$type];
            $modelName = $formConfig['model'];
            $model = $this->$modelName;
            
            // Validate required fields
            foreach ($formConfig['fields'] as $field) {
                if ($field['required'] && empty($data[$field['name']])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $field['label'] . ' harus diisi'
                    ]);
                }
            }
            
            // Insert data
            $insertData = [];
            foreach ($formConfig['fields'] as $field) {
                if (isset($data[$field['name']])) {
                    $insertData[$field['name']] = trim($data[$field['name']]);
                }
            }
            
            $insertId = $model->insert($insertData);
            
            if (!$insertId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data: ' . implode(', ', $model->errors())
                ]);
            }
            
            // Get inserted data
            $insertedData = $model->find($insertId);
            
            // Log activity
            $this->logActivity(
                'create',
                'master_data',
                $insertId,
                'Menambahkan ' . $formConfig['title'] . ': ' . json_encode($insertData)
            );
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $formConfig['title'] . ' berhasil ditambahkan',
                'data' => $insertedData,
                'id' => $insertId
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Refresh dropdown data
     */
    public function refreshDropdownData()
    {
        try {
            $type = $this->request->getPost('type');
            $config = $this->getMasterDataConfig();
            
            if (!isset($config[$type])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipe tidak valid'
                ]);
            }
            
            $formConfig = $config[$type];
            $modelName = $formConfig['model'];
            $model = $this->$modelName;
            
            // Get data based on type
            $data = [];
            switch ($type) {
                case 'brand':
                    $data = $model->select('merk_unit, MIN(id_model_unit) as id_model_unit')->groupBy('merk_unit')->findAll();
                    break;
                case 'model':
                    $brand = $this->request->getPost('brand');
                    if ($brand) {
                        $data = $model->where('merk_unit', $brand)->findAll();
                    }
                    break;
                default:
                    $data = $model->findAll();
                    break;
            }
            
            $response = [
                'success' => true,
                'data' => $data
            ];
            
            // Add refresh_related if defined in config
            if (isset($formConfig['refresh_related'])) {
                $response['refresh_related'] = $formConfig['refresh_related'];
            }
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

}