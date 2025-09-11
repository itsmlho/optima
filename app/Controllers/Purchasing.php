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
            $where["purchase_orders.tanggal_po >="] = empty($start_date) ? date("Y-m-01") : $start_date;

            $end_date = $this->request->getPost("end_date");
            $where["purchase_orders.tanggal_po <="] = empty($end_date) ? date("Y-m-t") : $end_date;

            $tipewhere = $tipe == "unit" ? "Unit" : ($tipe == "sparepart" ? "Sparepart" : "Attachment & Battery");
            $where["purchase_orders.tipe_po"] = $tipewhere;

            // Hitung total semua PO tanpa filter (buat recordsTotal)
            $recordsTotal = $this->purchasingModel
                ->where($where)
                ->countAllResults(false);

            // 1. Hitung total data setelah difilter
            $filterModel = $this->purchasingModel;
            $recordsFiltered = $filterModel
                ->where($where)
                ->countAllResults(false);

            // $this->purchasingModel->resetQuery(); // <<<<< ini WAJIB buat ngehapus join sebelumnya

            // 2. Ambil data PO
            $totalItemsSubquery = "(SELECT COUNT(id_po_unit) FROM po_units WHERE po_id = purchase_orders.id_po)";
            $sesuaiItemsSubquery = "(SELECT COUNT(id_po_unit) FROM po_units WHERE po_id = purchase_orders.id_po AND status_verifikasi = 'Sesuai')";
            $processedItemsSubquery = "(SELECT COUNT(id_po_unit) FROM po_units WHERE po_id = purchase_orders.id_po AND status_verifikasi != 'Belum Dicek')";
            $rejectedItemsSubquery = "(SELECT COUNT(id_po_unit) FROM po_units WHERE po_id = purchase_orders.id_po AND status_verifikasi = 'Tidak Sesuai')";

            $data = $filterModel
                ->select('purchase_orders.id_po, purchase_orders.no_po, DATE_FORMAT(purchase_orders.tanggal_po, "%d-%M-%Y") as tanggal_po, suppliers.nama_supplier, purchase_orders.status, '
                    . $totalItemsSubquery . ' as total_items, '
                    . $sesuaiItemsSubquery . ' as sesuai_items, '
                    . $processedItemsSubquery . ' as processed_items, '
                    . $rejectedItemsSubquery . ' as rejected_items')
                ->join('suppliers', 'purchase_orders.supplier_id = suppliers.id_supplier')
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
        $data = [
            'title'                 => 'Purchasing Division | OPTIMA',
            'page_title'            => 'Purchasing Division Dashboard',
            'po_unit_stats'         => $this->purchasingModel->where('tipe_po','Unit')->get()->getResultArray(),
            'po_attachment_stats'   => $this->purchasingModel->where('tipe_po','Attachment & Battery')->get()->getResultArray(),
            'po_sparepart_stats'    => $this->purchasingModel->where('tipe_po','Sparepart')->get()->getResultArray(),
            'notification_stats'    => $this->notificationModel->getNotificationStats('purchasing'),
            'recent_notifications'  => $this->notificationModel->limit(5)->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('purchasing/dashboard', $data);
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
            
            for ($i=1; $i <= $qty_duplicates; $i++) { 
                $poUnitData[] = [
                    'po_id'             => $newPoId,
                    'jenis_unit'        => $this->request->getPost('jenis_unit'),
                    'merk_unit'         => $this->request->getPost('merk_unit'), // This is now the ID from the dropdown
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
                
                // Tambah notifikasi ke warehouse
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
            'title' => 'Purchase Order Sparepart',
            'stats' => $this->purchasingModel->getPOStats('tipe_po','Sparepart'),
            'suppliers' => $this->purchasingModel->getSuppliers(),
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
                    // JIKA BERHASIL, tampilkan pesan sukses
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
     return redirect()->to('/purchasing/po-sparepart')->with('success', 'PO Sparepart berhasil diupdate.');
    }

    public function deletePoSparepart($id)
    {
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


}