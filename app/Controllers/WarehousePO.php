<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;
use App\Models\POUnitsModel;
use App\Models\PurchasingModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\POAttachmentModel;//ADIT
use App\Models\POSparepartItemModel; // adit
use App\Models\InventorySparepartModel; 
use App\Models\InventoryUnitModel;
use App\Models\InventoryAttachmentModel;
use App\Models\VerificationAuditLogModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


class WarehousePO extends BaseController
{
    use ActivityLoggingTrait;
    use ResponseTrait;
    protected $pounitsmodel;
    protected $purchasemodel;
    protected $poAttachmentModel;//ADIT
    protected $poSparepartItemModel; // adit
    protected $inventorySparepartModel;
    protected $inventoryUnitModel;
    protected $inventoryAttachmentModel;
    protected $verificationAuditLogModel;

    // Replace the constructor with initController
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Ensure timezone is set to Jakarta
        date_default_timezone_set('Asia/Jakarta');
        
        // Load date helper
        helper('date');
        
        // Set MySQL timezone to Jakarta for this connection
        $db = \Config\Database::connect();
        try {
            $db->query("SET time_zone = '+07:00'");
        } catch (\Exception $e) {
            log_message('warning', '[WarehousePO] Failed to set MySQL timezone: ' . $e->getMessage());
        }

        $this->pounitsmodel = new POUnitsModel(); //MALIK
        $this->purchasemodel = new PurchasingModel(); //MALIK
        $this->poAttachmentModel = new POAttachmentModel();//ADIT
        $this->poSparepartItemModel = new POSparepartItemModel(); // adit
        $this->inventorySparepartModel = new InventorySparepartModel(); 
        $this->inventoryUnitModel = new InventoryUnitModel();
        $this->inventoryAttachmentModel = new InventoryAttachmentModel(); // pastikan ada
        $this->verificationAuditLogModel = new VerificationAuditLogModel();
    }

    /**
     * Main Purchase Order Verification Dashboard for Warehouse
     */
    public function index()
    {
        $data = [
            'title' => 'Purchase Order Verification | OPTIMA',
            'page_title' => 'Purchase Order Verification Dashboard',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse/purchase-orders' => 'PO Verification'
            ],
            // Fixed column name: status_verifikasi
            'po_unit_stats' => $this->pounitsmodel->where("status_verifikasi", "Belum Dicek")->get()->getResultArray(),
            // 'po_attachment_stats' => $this->poAttachmentModel->getPOStats(),
            // 'po_sparepart_stats' => $this->poSparepartModel->getPOStats(),
            // 'verification_summary' => $this->getVerificationSummary()
        ];

        return view('warehouse/purchase_orders/index', $data);
    }

    /**
     * Unified PO Verification Page (3 tabs in 1 page)
     */
    public function whVerification()
    {
        // Get data untuk semua tipe PO dengan detail lengkap dan packing list
        // Only show items with Received delivery status
        // Get unit data with delivery info - similar to attachment approach
        // Don't use GROUP BY, let each unit have its own tanggal_datang like attachment
        $dataUnit = $this->pounitsmodel
            ->select('
                po_units.*, 
                purchase_orders.no_po, 
                purchase_orders.id_po as po_id,
                mu.model_unit, mu.merk_unit,
                tu.jenis as jenis, 
                d.nama_departemen,
                k.kapasitas_unit,
                tm.tipe_mast, tm.tinggi_mast,
                m.merk_mesin, m.model_mesin,
                tb.tipe_ban,
                jr.tipe_roda,
                v.jumlah_valve,
                pd.packing_list_no, 
                pd.actual_date as tanggal_datang,
                pd.updated_at,
                pd.status as delivery_status,
                pd.delivery_sequence
            ')
            ->join('purchase_orders', 'purchase_orders.id_po = po_units.po_id')
            ->join('model_unit mu', 'mu.id_model_unit = po_units.model_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = po_units.tipe_unit_id', 'left')
            ->join('departemen d', 'd.id_departemen = tu.id_departemen', 'left')
            ->join('kapasitas k', 'k.id_kapasitas = po_units.kapasitas_id', 'left')
            ->join('tipe_mast tm', 'tm.id_mast = po_units.mast_id', 'left')
            ->join('mesin m', 'm.id = po_units.mesin_id', 'left')
            ->join('tipe_ban tb', 'tb.id_ban = po_units.ban_id', 'left')
            ->join('jenis_roda jr', 'jr.id_roda = po_units.roda_id', 'left')
            ->join('valve v', 'v.id_valve = po_units.valve_id', 'left')
            ->join('po_delivery_items pdi', 'pdi.id_po_unit = po_units.id_po_unit', 'left')
            ->join('po_deliveries pd', 'pd.id_delivery = pdi.delivery_id AND pd.status = "Received"', 'left')
            ->where('po_units.status_verifikasi', 'Belum Dicek')
            ->orderBy('pd.actual_date', 'DESC')
            ->orderBy('po_units.id_po_unit', 'DESC') // Fallback ordering jika tidak ada delivery date
            ->get()->getResultArray();

        // Only show attachments with Received delivery status
        $dataAttachment = $this->poAttachmentModel
            ->select('
                po_attachment.*, 
                purchase_orders.no_po, 
                purchase_orders.id_po as po_id,
                a.merk as merk_attachment, a.model as model_attachment, a.tipe as tipe_attachment,
                b.merk_baterai as merk_battery, b.tipe_baterai as tipe_battery, b.jenis_baterai as jenis_battery,
                c.merk_charger as merk_charger, c.tipe_charger as tipe_charger,
                pd.packing_list_no, pd.actual_date as tanggal_datang,
                pd.updated_at,
                pd.status as delivery_status,
                pd.delivery_sequence
            ')
            ->join('purchase_orders', 'purchase_orders.id_po = po_attachment.po_id')
            ->join('attachment a', 'a.id_attachment = po_attachment.attachment_id', 'left')
            ->join('baterai b', 'b.id = po_attachment.baterai_id', 'left')
            ->join('charger c', 'c.id_charger = po_attachment.charger_id', 'left')
            // INNER JOIN to only show items with delivery records
            ->join('po_delivery_items pdi', 'pdi.id_po_attachment = po_attachment.id_po_attachment', 'inner')
            // INNER JOIN to only show items with "Received" status
            ->join('po_deliveries pd', 'pd.id_delivery = pdi.delivery_id', 'inner')
            ->where('po_attachment.status_verifikasi', 'Belum Dicek')
            ->where('pd.status', 'Received') // Only Received deliveries
            ->orderBy('pd.actual_date', 'DESC')
            ->get()->getResultArray();

        $dataSparepart = $this->poSparepartItemModel
            ->select('po_sparepart_items.*, purchase_orders.no_po, purchase_orders.id_po')
            ->join('purchase_orders', 'purchase_orders.id_po = po_sparepart_items.po_id')
            ->where('po_sparepart_items.status_verifikasi', 'Belum Dicek')
            ->orderBy('purchase_orders.no_po', 'ASC')
            ->get()->getResultArray();

        // Group data by PO
        $detailGroupUnit = $this->groupByPO($dataUnit, 'id_po_unit');
        $detailGroupAttachment = $this->groupByPO($dataAttachment, 'id_po_item');
        $detailGroupSparepart = $this->groupByPO($dataSparepart, 'id');

        $data = [
            'title' => 'PO Verification | Warehouse',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse/wh_verification' => 'PO Verification'
            ],
            'detailGroupUnit' => $detailGroupUnit,
            'detailGroupAttachment' => $detailGroupAttachment,
            'detailGroupSparepart' => $detailGroupSparepart
        ];

        return view('warehouse/purchase_orders/wh_verification', $data);
    }

    /**
     * Helper function to group data by PO
     */
    private function groupByPO($items, $itemIdField)
    {
        $detailGroup = [];
        foreach ($items as $item) {
            $poKey = $item['id_po'] ?? $item['po_id'];
            if (!isset($detailGroup[$poKey])) {
                $detailGroup[$poKey] = [
                    'no_po' => $item['no_po'],
                    'packing_list_no' => null,
                    'tanggal_datang' => null,
                    'data' => []
                ];
            }
            $item['item_id_field'] = $itemIdField;
            $item['po_id'] = $poKey; // Normalize po_id
            $detailGroup[$poKey]['data'][] = $item;
            
            // Update packing_list_no and tanggal_datang if available (take the latest/most recent)
            // For packing_list_no, take the first non-empty value
            if (!empty($item['packing_list_no']) && empty($detailGroup[$poKey]['packing_list_no'])) {
                $detailGroup[$poKey]['packing_list_no'] = $item['packing_list_no'];
            }
            // For tanggal_datang, take the latest non-null value
            if (!empty($item['tanggal_datang']) && $item['tanggal_datang'] !== null) {
                $itemDate = is_string($item['tanggal_datang']) ? strtotime($item['tanggal_datang']) : $item['tanggal_datang'];
                
                // Safely handle current date with explicit null checking
                $currentDateValue = $detailGroup[$poKey]['tanggal_datang'] ?? null;
                $currentDate = (!empty($currentDateValue) && $currentDateValue !== null)
                    ? (is_string($currentDateValue) ? strtotime($currentDateValue) : $currentDateValue)
                    : 0;
                
                if ($itemDate > $currentDate) {
                    $detailGroup[$poKey]['tanggal_datang'] = $item['tanggal_datang'];
                }
            }
        }
        return $detailGroup;
    }

    /**
     * Get dropdown options for unit verification fields
     */
    public function getUnitVerificationOptions()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request method']);
        }

        $fieldType = $this->request->getGet('field');
        
        if (!$fieldType) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Field parameter is required'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $options = [];

            switch ($fieldType) {
                case 'departemen':
                    $options = $db->table('departemen')
                        ->select('id_departemen as id, nama_departemen as text')
                        ->orderBy('nama_departemen', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'tipe_unit':
                    $options = $db->table('tipe_unit')
                        ->select('id_tipe_unit as id, jenis as text')
                        ->orderBy('jenis', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'merk_unit':
                    // Get unique merk_unit values for Brand dropdown
                    $options = $db->table('model_unit')
                        ->select('merk_unit as id, merk_unit as text')
                        ->distinct()
                        ->orderBy('merk_unit', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'model_unit':
                    // Get model_unit filtered by merk_unit if provided
                    $merkUnit = $this->request->getGet('merk_unit');
                    $query = $db->table('model_unit')
                        ->select('id_model_unit as id, model_unit as text, merk_unit')
                        ->orderBy('model_unit', 'ASC');
                    if ($merkUnit) {
                        $query->where('merk_unit', $merkUnit);
                    }
                    $options = $query->get()->getResultArray();
                    break;
                case 'kapasitas':
                    $options = $db->table('kapasitas')
                        ->select('id_kapasitas as id, kapasitas_unit as text')
                        ->orderBy('kapasitas_unit', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'tipe_mast':
                    $options = $db->table('tipe_mast')
                        ->select('id_mast as id, CONCAT(tipe_mast, IF(tinggi_mast IS NOT NULL AND tinggi_mast != "", CONCAT(" (", tinggi_mast, ")"), "")) as text')
                        ->orderBy('tipe_mast', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'merk_mesin':
                    // Get unique merk_mesin values for Engine Type dropdown
                    $options = $db->table('mesin')
                        ->select('merk_mesin as id, merk_mesin as text')
                        ->distinct()
                        ->orderBy('merk_mesin', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'model_mesin':
                    // Get model_mesin filtered by merk_mesin if provided
                    $merkMesin = $this->request->getGet('merk_mesin');
                    $query = $db->table('mesin')
                        ->select('id as id, model_mesin as text, merk_mesin')
                        ->orderBy('model_mesin', 'ASC');
                    if ($merkMesin) {
                        $query->where('merk_mesin', $merkMesin);
                    }
                    $options = $query->get()->getResultArray();
                    break;
                case 'mesin':
                    $options = $db->table('mesin')
                        ->select('id, CONCAT(merk_mesin, " - ", model_mesin) as text')
                        ->orderBy('merk_mesin', 'ASC')
                        ->orderBy('model_mesin', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'tipe_ban':
                    $options = $db->table('tipe_ban')
                        ->select('id_ban as id, tipe_ban as text')
                        ->orderBy('tipe_ban', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'jenis_roda':
                    $options = $db->table('jenis_roda')
                        ->select('id_roda as id, COALESCE(tipe_roda, jenis_roda, nama_jenis_roda) as text')
                        ->orderBy('text', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'valve':
                    $options = $db->table('valve')
                        ->select('id_valve as id, CONCAT(jumlah_valve, " Valve") as text')
                        ->orderBy('jumlah_valve', 'ASC')
                        ->get()->getResultArray();
                    break;
                // Attachment fields
                case 'tipe_attachment':
                    $options = $db->table('attachment')
                        ->select('tipe as id, tipe as text')
                        ->distinct()
                        ->orderBy('tipe', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'merk_attachment':
                    $tipe = $this->request->getGet('tipe');
                    $query = $db->table('attachment')
                        ->select('merk as id, merk as text')
                        ->distinct()
                        ->orderBy('merk', 'ASC');
                    if ($tipe) {
                        $query->where('tipe', $tipe);
                    }
                    $options = $query->get()->getResultArray();
                    break;
                case 'model_attachment':
                    $tipe = $this->request->getGet('tipe');
                    $merk = $this->request->getGet('merk');
                    $query = $db->table('attachment')
                        ->select('id_attachment as id, model as text, tipe, merk')
                        ->orderBy('model', 'ASC');
                    if ($tipe) {
                        $query->where('tipe', $tipe);
                    }
                    if ($merk) {
                        $query->where('merk', $merk);
                    }
                    $options = $query->get()->getResultArray();
                    break;
                // Battery fields
                case 'merk_battery':
                    $options = $db->table('baterai')
                        ->select('merk_baterai as id, merk_baterai as text')
                        ->distinct()
                        ->orderBy('merk_baterai', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'tipe_battery':
                    $merk = $this->request->getGet('merk');
                    $query = $db->table('baterai')
                        ->select('tipe_baterai as id, tipe_baterai as text')
                        ->distinct()
                        ->orderBy('tipe_baterai', 'ASC');
                    if ($merk) {
                        $query->where('merk_baterai', $merk);
                    }
                    $options = $query->get()->getResultArray();
                    break;
                case 'jenis_battery':
                    $merk = $this->request->getGet('merk');
                    $tipe = $this->request->getGet('tipe');
                    $query = $db->table('baterai')
                        ->select('jenis_baterai as id, jenis_baterai as text')
                        ->distinct()
                        ->orderBy('jenis_baterai', 'ASC');
                    if ($merk) {
                        $query->where('merk_baterai', $merk);
                    }
                    if ($tipe) {
                        $query->where('tipe_baterai', $tipe);
                    }
                    $options = $query->get()->getResultArray();
                    break;
                // Charger fields
                case 'merk_charger':
                    $options = $db->table('charger')
                        ->select('merk_charger as id, merk_charger as text')
                        ->distinct()
                        ->orderBy('merk_charger', 'ASC')
                        ->get()->getResultArray();
                    break;
                case 'tipe_charger':
                    $merk = $this->request->getGet('merk');
                    $query = $db->table('charger')
                        ->select('id_charger as id, tipe_charger as text, merk_charger')
                        ->orderBy('tipe_charger', 'ASC');
                    if ($merk) {
                        $query->where('merk_charger', $merk);
                    }
                    $options = $query->get()->getResultArray();
                    break;
                default:
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Unknown field type: ' . $fieldType
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $options
            ]);

        } catch (\Exception $e) {
            log_message('error', '[WarehousePO] Error getting dropdown options: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * PO Unit Verification Interface
     */
    public function poUnit()
    {
        $dataDetail = $this->pounitsmodel
            ->select('
                po_units.id_po_unit,
                po_units.po_id,
                po_units.status_penjualan,
                po_units.status_verifikasi,
                po_units.serial_number_po,
                po_units.tahun_po,
                po_units.sn_mast_po,
                po_units.sn_mesin_po,
                po_units.sn_attachment_po,
                po_units.sn_baterai_po,
                po_units.keterangan,
                po_units.sn_charger_po,
                purchase_orders.no_po,
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
                departemen.nama_departemen as jenis_unit
            ')
            ->join('purchase_orders', 'purchase_orders.id_po = po_units.po_id', 'left')
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
            ->where('po_units.status_verifikasi', 'Belum Dicek')
            ->get()
            ->getResultArray();
            
        // Grouping data by PO ID
        $detailGroup = [];
        foreach ($dataDetail as $item) {
            $detailGroup[$item["po_id"]]["no_po"] = $item["no_po"];
            $detailGroup[$item["po_id"]]["data"][] = $item;
        }

        $data = [
            'title' => 'PO Unit Verification',
            'page_title' => 'PO Unit Verification',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse/purchase-orders/po-unit' => 'PO Unit'
            ],
            'detailGroup' => $detailGroup,
            // 'data' => $dataDetail,
            // 'verification_options' => $this->poUnitModel->getVerificationStatusOptions()
        ];

        return view('warehouse/purchase_orders/po_unit', $data);
    }
    
    public function printPOUnits()
    {
        $dataDetail = $this->pounitsmodel
            ->select('
                po_units.id_po_unit,
                po_units.po_id,
                po_units.status_penjualan,
                po_units.status_verifikasi,
                po_units.serial_number_po,
                po_units.tahun_po,
                po_units.sn_mast_po,
                po_units.sn_mesin_po,
                po_units.sn_attachment_po,
                po_units.sn_baterai_po,
                po_units.keterangan,
                po_units.sn_charger_po,
                model_unit.merk_unit,
                model_unit.model_unit,
                tipe_unit.nama_tipe_unit,
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
                departemen.nama_departemen as jenis_unit
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
            ->where('po_units.status_verifikasi', 'Belum Dicek')
            ->get()
            ->getResultArray();
        $data = [
            'title' => 'Print PO Unit Verification',
            'data' => $dataDetail,
        ];

        return view('warehouse/purchase_orders/print_list_units', $data);
    }

    /**
     * Helper: Get user info by ID
     */
    private function getUserInfo($userId)
    {
        if (empty($userId)) {
            return ['first_name' => null, 'last_name' => null, 'username' => null];
        }
        
        $db = \Config\Database::connect();
        $user = $db->table('users')
            ->select('first_name, last_name, username')
            ->where('id', $userId)
            ->get()
            ->getRowArray();
        
        return $user ?: ['first_name' => null, 'last_name' => null, 'username' => null];
    }
    
    /**
     * Helper: Get verification info from audit log
     */
    /**
     * Helper: Get verification info from audit log with detailed logging
     */
    private function getVerificationInfo($db, $poType, $sourceId, $fallbackDate = null)
    {
        if (!$db->tableExists('verification_audit_log')) {
            return [
                'tanggal_verifikasi' => $fallbackDate,
                'verified_by_name' => null,
                'verified_by_lastname' => null
            ];
        }
        
        // Query dengan logging detail - CRITICAL: Get latest audit log for this item
        // Get the most recent verification record (regardless of status) for this item
        $auditLog = $db->table('verification_audit_log val')
            ->select('val.created_at, val.user_id, val.status_after, u.first_name, u.last_name, u.username')
            ->join('users u', 'u.id = val.user_id', 'left')
            ->where('val.po_type', $poType)
            ->where('val.source_id', $sourceId)
            ->orderBy('val.created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
        
        log_message('info', '[WarehousePO] getVerificationInfo for ' . $poType . ' #' . $sourceId . ': ' . ($auditLog ? 'FOUND' : 'NOT FOUND'));
        
        if ($auditLog) {
            log_message('info', '[WarehousePO] Audit log data: ' . json_encode($auditLog));
            
            // Get verifier name - prioritize first_name, fallback to username
            $verifiedByName = null;
            $verifiedByLastname = null;
            
            if (!empty($auditLog['user_id'])) {
                // Check if user data exists
                if (!empty($auditLog['first_name']) || !empty($auditLog['username'])) {
                    $verifiedByName = $auditLog['first_name'] ?? $auditLog['username'];
                    $verifiedByLastname = $auditLog['last_name'] ?? null;
                    log_message('info', '[WarehousePO] ✓ Verifier found: ' . $verifiedByName . ' ' . ($verifiedByLastname ?? ''));
                } else {
                    // User ID exists but user data not found - try to get from users table directly
                    log_message('warning', '[WarehousePO] User ID ' . $auditLog['user_id'] . ' found but user data is empty. Querying users table...');
                    $user = $db->table('users')
                        ->select('first_name, last_name, username')
                        ->where('id', $auditLog['user_id'])
                        ->get()
                        ->getRowArray();
                    
                    if ($user) {
                        $verifiedByName = $user['first_name'] ?? $user['username'];
                        $verifiedByLastname = $user['last_name'] ?? null;
                        log_message('info', '[WarehousePO] ✓ User data retrieved directly: ' . $verifiedByName . ' ' . ($verifiedByLastname ?? ''));
                    } else {
                        log_message('error', '[WarehousePO] ✗ User ID ' . $auditLog['user_id'] . ' not found in users table!');
                    }
                }
            } else {
                log_message('error', '[WarehousePO] ✗ Audit log found but user_id is NULL for ' . $poType . ' #' . $sourceId);
            }
            
            return [
                'tanggal_verifikasi' => $auditLog['created_at'] ?? $fallbackDate,
                'verified_by_name' => $verifiedByName,
                'verified_by_lastname' => $verifiedByLastname
            ];
        }
        
        log_message('debug', '[WarehousePO] No audit log found for ' . $poType . ' #' . $sourceId);
        return [
            'tanggal_verifikasi' => $fallbackDate,
            'verified_by_name' => null,
            'verified_by_lastname' => null
        ];
    }
    
    /**
     * Helper: Get delivery info
     */
    private function getDeliveryInfo($db, $poId, $itemType = null, $itemId = null)
    {
        $builder = $db->table('po_deliveries pd')
            ->select('pd.packing_list_no, pd.actual_date, pd.delivery_date')
            ->where('pd.po_id', $poId)
            ->where('pd.status', 'Received');
        
        if ($itemType && $itemId) {
            $builder->join('po_delivery_items pdi', 'pdi.delivery_id = pd.id_delivery', 'left')
                ->where('pdi.item_type', $itemType);
            
            if ($itemType === 'Unit') {
                $builder->where('pdi.id_po_unit', $itemId);
            } elseif ($itemType === 'Attachment') {
                $builder->where('pdi.id_po_attachment', $itemId);
            }
        }
        
        $delivery = $builder->orderBy('pd.actual_date', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
        
        return $delivery ?: null;
    }
    
    /**
     * Halaman untuk menampilkan PO items yang tidak sesuai/rejected
     */
    public function rejectedItems()
    {
        $db = \Config\Database::connect();
        
        // Get rejected units
        $rejectedUnits = $db->table('po_units pu')
            ->select('pu.*, po.no_po, po.id_po as po_id, mu.merk_unit, mu.model_unit')
            ->join('purchase_orders po', 'po.id_po = pu.po_id')
            ->join('model_unit mu', 'mu.id_model_unit = pu.model_unit_id', 'left')
            ->where('pu.status_verifikasi', 'Tidak Sesuai')
            ->orderBy('pu.updated_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Enrich units data
        foreach ($rejectedUnits as &$unit) {
            // Delivery info
            $delivery = $this->getDeliveryInfo($db, $unit['po_id'], 'Unit', $unit['id_po_unit']);
            $unit['packing_list_no'] = $delivery['packing_list_no'] ?? null;
            $unit['tanggal_sampai'] = $delivery['actual_date'] ?? $delivery['delivery_date'] ?? null;
            
            // Serial numbers
            $unit['sn_unit'] = $unit['serial_number_po'] ?? null;
            $unit['sn_mesin'] = $unit['sn_mesin_po'] ?? null;
            $unit['sn_mast'] = $unit['sn_mast_po'] ?? null;
            $unit['sn_baterai'] = $unit['sn_baterai_po'] ?? null;
            
            // Verification info
            $verifInfo = $this->getVerificationInfo($db, 'unit', $unit['id_po_unit'], $unit['updated_at'] ?? $unit['created_at'] ?? null);
            $unit['tanggal_verifikasi'] = $verifInfo['tanggal_verifikasi'];
            $unit['verified_by_name'] = $verifInfo['verified_by_name'];
            $unit['verified_by_lastname'] = $verifInfo['verified_by_lastname'];
        }
        unset($unit);
        
        // Get rejected attachments
        $rejectedAttachments = $db->table('po_attachment pa')
            ->select('pa.*, po.no_po, po.id_po as po_id')
            ->join('purchase_orders po', 'po.id_po = pa.po_id')
            ->where('pa.status_verifikasi', 'Tidak Sesuai')
            ->orderBy('pa.updated_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Enrich attachments data
        foreach ($rejectedAttachments as &$attachment) {
            // Delivery info
            $delivery = $this->getDeliveryInfo($db, $attachment['po_id'], 'Attachment', $attachment['id_po_attachment']);
            $attachment['packing_list_no'] = $delivery['packing_list_no'] ?? null;
            $attachment['tanggal_sampai'] = $delivery['actual_date'] ?? $delivery['delivery_date'] ?? null;
            $attachment['sn'] = $attachment['serial_number'] ?? null;
            
            // Verification info
            $verifInfo = $this->getVerificationInfo($db, 'attachment', $attachment['id_po_attachment'], $attachment['updated_at'] ?? $attachment['created_at'] ?? null);
            $attachment['tanggal_verifikasi'] = $verifInfo['tanggal_verifikasi'];
            $attachment['verified_by_name'] = $verifInfo['verified_by_name'];
            $attachment['verified_by_lastname'] = $verifInfo['verified_by_lastname'];
        }
        unset($attachment);
        
        // Get rejected spareparts
        $rejectedSpareparts = $db->table('po_sparepart_items psi')
            ->select('psi.*, po.no_po, po.id_po as po_id, s.kode, s.desc_sparepart')
            ->join('purchase_orders po', 'po.id_po = psi.po_id')
            ->join('sparepart s', 's.id_sparepart = psi.sparepart_id', 'left')
            ->where('psi.status_verifikasi', 'Tidak Sesuai')
            ->orderBy('psi.id', 'DESC')
            ->get()
            ->getResultArray();
        
        // Enrich spareparts data
        foreach ($rejectedSpareparts as &$sparepart) {
            // Delivery info (sparepart doesn't have direct link in po_delivery_items)
            $delivery = $this->getDeliveryInfo($db, $sparepart['po_id']);
            $sparepart['packing_list_no'] = $delivery['packing_list_no'] ?? null;
            $sparepart['tanggal_sampai'] = $delivery['actual_date'] ?? $delivery['delivery_date'] ?? null;
            
            // Verification info
            $verifInfo = $this->getVerificationInfo($db, 'sparepart', $sparepart['id'], $sparepart['updated_at'] ?? $sparepart['created_at'] ?? null);
            $sparepart['tanggal_verifikasi'] = $verifInfo['tanggal_verifikasi'];
            $sparepart['verified_by_name'] = $verifInfo['verified_by_name'];
            $sparepart['verified_by_lastname'] = $verifInfo['verified_by_lastname'];
        }
        unset($sparepart);
        
        // Get discrepancy details from po_verification (if table exists)
        $discrepancies = [];
        try {
            if (!empty($rejectedUnits) || !empty($rejectedAttachments) || !empty($rejectedSpareparts)) {
                $sourceIds = [];
                foreach ($rejectedUnits as $unit) {
                    $sourceIds[] = ['type' => 'unit', 'id' => $unit['id_po_unit']];
                }
                foreach ($rejectedAttachments as $att) {
                    $sourceIds[] = ['type' => 'attachment', 'id' => $att['id_po_attachment']];
                }
                foreach ($rejectedSpareparts as $sp) {
                    $sourceIds[] = ['type' => 'sparepart', 'id' => $sp['id']];
                }
                
                // Get discrepancies for all items
                foreach ($sourceIds as $source) {
                    $discs = $db->table('po_verification')
                        ->where('po_type', $source['type'])
                        ->where('source_id', $source['id'])
                        ->where('status_verifikasi', 'Tidak Sesuai')
                        ->get()
                        ->getResultArray();
                    
                    if (!empty($discs)) {
                        $discrepancies[$source['type'] . '_' . $source['id']] = $discs;
                    }
                }
            }
        } catch (\Exception $e) {
            // Table po_verification belum ada, skip
            log_message('info', '[WarehousePO] po_verification table not found, skipping discrepancy details');
        }
        
        $data = [
            'title' => 'Rejected Items - PO Verification | OPTIMA',
            'page_title' => 'PO Items - Tidak Sesuai / Rejected',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse/purchase-orders/rejected-items' => 'Rejected Items'
            ],
            'rejected_units' => $rejectedUnits,
            'rejected_attachments' => $rejectedAttachments,
            'rejected_spareparts' => $rejectedSpareparts,
            'discrepancies' => $discrepancies,
            'total_rejected' => count($rejectedUnits) + count($rejectedAttachments) + count($rejectedSpareparts)
        ];
        
        return view('warehouse/purchase_orders/rejected_items', $data);
    }

    public function verifyPoUnit()
    {
        if ($this->request->isAJAX()) {
            $id_unit = $this->request->getPost('id_unit');
            $po_id = $this->request->getPost('po_id');
            $status = $this->request->getPost('status');
            $catatan = $this->request->getPost('catatan_verifikasi');
            $lokasi_unit = $this->request->getPost('lokasi_unit') ?: 'POS 1';
            
            // Mengambil data Serial Number dari form (jika ada)
            $snData = [
                'serial_number_po' => $this->request->getPost('sn_unit'),
                'sn_mesin_po'      => $this->request->getPost('sn_mesin'),
                'sn_baterai_po'    => $this->request->getPost('sn_baterai'),
                'sn_mast_po'       => $this->request->getPost('sn_mast'),
            ];

            // Menyiapkan data lengkap untuk diupdate ke tabel po_units
            $dataToUpdate = [
                'status_verifikasi'  => $status,
            ];
            
            // Hanya tambahkan catatan_verifikasi jika ada (untuk "Tidak Sesuai")
            // Potong jika terlalu panjang (max 500 karakter sesuai validation rule)
            if (!empty($catatan)) {
                $catatanTrimmed = mb_substr($catatan, 0, 500);
                if (strlen($catatan) > 500) {
                    log_message('warning', '[WarehousePO] Catatan verifikasi dipotong dari ' . strlen($catatan) . ' menjadi 500 karakter');
                }
                $dataToUpdate['catatan_verifikasi'] = $catatanTrimmed;
            }
            
            // Tambahkan SN data yang tidak kosong
            foreach ($snData as $key => $value) {
                if ($value !== null && $value !== '') {
                    $dataToUpdate[$key] = $value;
                }
            }

            // Serial number mandatory validation when status 'Sesuai'
            if ($status === 'Sesuai' && (empty($snData['serial_number_po']) || empty($snData['sn_mesin_po']))) {
                return $this->response->setJSON([
                    'statusCode' => 422,
                    'message' => 'Serial number unit dan mesin wajib diisi untuk status Sesuai.'
                ]);
            }

            $db = \Config\Database::connect();
            $db->transBegin();
            $original = $this->pounitsmodel->find($id_unit);
            
            if (!$original) {
                $db->transRollback();
                return $this->response->setJSON([
                    'statusCode' => 404,
                    'message' => 'Unit tidak ditemukan.'
                ]);
            }
            
            try {
                // Log data yang akan diupdate untuk debugging
                log_message('debug', '[WarehousePO] Updating unit ' . $id_unit . ' with data: ' . json_encode($dataToUpdate));
                
                // Skip validation untuk update verifikasi (karena hanya update status dan catatan)
                $this->pounitsmodel->skipValidation(true);
                
                if ($this->pounitsmodel->update($id_unit, $dataToUpdate)) {
                    // Re-enable validation
                    $this->pounitsmodel->skipValidation(false);
                    // Jika verifikasi "Sesuai", masukkan ke tabel inventory
                    if ($status === 'Sesuai') {
                        // Ambil semua data detail dari unit yang baru diverifikasi dengan JOIN untuk mendapatkan merk_unit
                        $verifiedUnit = $this->pounitsmodel
                            ->select('po_units.*, model_unit.merk_unit')
                            ->join('model_unit', 'model_unit.id_model_unit = po_units.model_unit_id', 'left')
                            ->find($id_unit);

                        if ($verifiedUnit) {
                            // Siapkan data LENGKAP untuk dimasukkan ke tabel inventory_unit
                            // Map fields from PO Unit to inventory_unit schema (hanya field yang ada di allowedFields)
                            $inventoryData = [
                                'serial_number'      => $snData['serial_number_po'] ?: ($verifiedUnit['serial_number_po'] ?? null),
                                'id_po'              => $verifiedUnit['po_id'] ?? null,
                                'tahun_unit'         => $verifiedUnit['tahun_po'] ?? null,
                                'status_unit_id'     => 2, // 2 = STOCK NON-ASET
                                'lokasi_unit'        => $lokasi_unit,
                                'departemen_id'      => $verifiedUnit['jenis_unit'] ?? null, // jenis_unit holds departemen id
                                'keterangan'         => $verifiedUnit['keterangan'] ?? null,
                                'tipe_unit_id'       => $verifiedUnit['tipe_unit_id'] ?? null,
                                'model_unit_id'      => $verifiedUnit['model_unit_id'] ?? null,
                                'kapasitas_unit_id'  => $verifiedUnit['kapasitas_id'] ?? null,
                                'model_mast_id'      => $verifiedUnit['mast_id'] ?? null,
                                'sn_mast'            => $snData['sn_mast_po'] ?: ($verifiedUnit['sn_mast_po'] ?? null),
                                'model_mesin_id'     => $verifiedUnit['mesin_id'] ?? null,
                                'sn_mesin'           => $snData['sn_mesin_po'] ?: ($verifiedUnit['sn_mesin_po'] ?? null),
                                'roda_id'            => $verifiedUnit['roda_id'] ?? null,
                                'ban_id'             => $verifiedUnit['ban_id'] ?? null,
                                'valve_id'           => $verifiedUnit['valve_id'] ?? null,
                            ];
                            
                            // Hapus nilai null untuk menghindari error database
                            $inventoryData = array_filter($inventoryData, function($value) {
                                return !($value === null || $value === '');
                            });
                            
                            // CATATAN: Attachment, Battery, Charger disimpan di tabel inventory_attachments terpisah
                            // Tidak disimpan langsung di inventory_unit
                            
                            // Log untuk debugging
                            log_message('debug', '[WarehousePO] Attempting to insert to inventory_unit: ' . json_encode($inventoryData));
                            
                            // Gunakan InventoryUnitModel untuk menyimpan data ke tabel inventory
                            if (!$this->inventoryUnitModel->insert($inventoryData)) {
                                // Log error jika insert gagal
                                $errors = $this->inventoryUnitModel->errors();
                                log_message('error', '[WarehousePO] Failed to insert to inventory_unit: ' . json_encode($errors));
                                log_message('error', '[WarehousePO] Attempted data: ' . json_encode($inventoryData));
                                $db->transRollback();
                                return $this->response->setJSON(['statusCode' => 500, 'message' => 'Gagal memasukkan data ke inventory: ' . implode(', ', $errors)]);
                            } else {
                                log_message('info', '[WarehousePO] Successfully inserted unit ' . $id_unit . ' to inventory_unit');
                                
                                // CATATAN: Data di po_units TIDAK dihapus, tetap ada untuk tracking dan history
                                // Status sudah diupdate menjadi 'Sesuai' di atas
                            }
                        }
                    }

                    // Jika status "Tidak Sesuai", simpan discrepancy dan kirim notifikasi
                    if ($status === 'Tidak Sesuai') {
                        // Ambil discrepancy data dari frontend (jika ada)
                        $discrepanciesJson = $this->request->getPost('discrepancies');
                        $discrepancies = [];
                        
                        log_message('debug', '[WarehousePO] Received discrepancies JSON: ' . ($discrepanciesJson ?? 'NULL'));
                        
                        if (!empty($discrepanciesJson)) {
                            try {
                                $decoded = json_decode($discrepanciesJson, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $discrepancies = $decoded;
                                    log_message('debug', '[WarehousePO] Parsed ' . count($discrepancies) . ' discrepancies from frontend');
                                } else {
                                    log_message('warning', '[WarehousePO] Invalid JSON or not array. JSON error: ' . json_last_error_msg());
                                }
                            } catch (\Exception $e) {
                                log_message('error', '[WarehousePO] Failed to parse discrepancies JSON: ' . $e->getMessage());
                                $discrepancies = [];
                            }
                        } else {
                            log_message('debug', '[WarehousePO] No discrepancies JSON received from frontend');
                        }
                        
                        // Juga collect discrepancies dari SN fields (jika ada perbedaan)
                        if (!empty($snData['serial_number_po']) && isset($original['serial_number_po']) && 
                            $snData['serial_number_po'] !== $original['serial_number_po']) {
                            // Cek apakah sudah ada di discrepancies dari frontend
                            $exists = false;
                            foreach ($discrepancies as $disc) {
                                if (isset($disc['field_name']) && $disc['field_name'] === 'sn_unit') {
                                    $exists = true;
                                    break;
                                }
                            }
                            if (!$exists) {
                                $discrepancies[] = [
                                    'field_name' => 'sn_unit',
                                    'database_value' => $original['serial_number_po'] ?? '',
                                    'real_value' => $snData['serial_number_po']
                                ];
                            }
                        }
                        if (!empty($snData['sn_mesin_po']) && isset($original['sn_mesin_po']) && 
                            $snData['sn_mesin_po'] !== $original['sn_mesin_po']) {
                            $exists = false;
                            foreach ($discrepancies as $disc) {
                                if (isset($disc['field_name']) && $disc['field_name'] === 'sn_mesin') {
                                    $exists = true;
                                    break;
                                }
                            }
                            if (!$exists) {
                                $discrepancies[] = [
                                    'field_name' => 'sn_mesin',
                                    'database_value' => $original['sn_mesin_po'] ?? '',
                                    'real_value' => $snData['sn_mesin_po']
                                ];
                            }
                        }
                        if (!empty($snData['sn_mast_po']) && isset($original['sn_mast_po']) && 
                            $snData['sn_mast_po'] !== $original['sn_mast_po']) {
                            $exists = false;
                            foreach ($discrepancies as $disc) {
                                if (isset($disc['field_name']) && $disc['field_name'] === 'sn_mast') {
                                    $exists = true;
                                    break;
                                }
                            }
                            if (!$exists) {
                                $discrepancies[] = [
                                    'field_name' => 'sn_mast',
                                    'database_value' => $original['sn_mast_po'] ?? '',
                                    'real_value' => $snData['sn_mast_po']
                                ];
                            }
                        }
                        if (!empty($snData['sn_baterai_po']) && isset($original['sn_baterai_po']) && 
                            $snData['sn_baterai_po'] !== $original['sn_baterai_po']) {
                            $exists = false;
                            foreach ($discrepancies as $disc) {
                                if (isset($disc['field_name']) && $disc['field_name'] === 'sn_baterai') {
                                    $exists = true;
                                    break;
                                }
                            }
                            if (!$exists) {
                                $discrepancies[] = [
                                    'field_name' => 'sn_baterai',
                                    'database_value' => $original['sn_baterai_po'] ?? '',
                                    'real_value' => $snData['sn_baterai_po']
                                ];
                            }
                        }
                        
                        // Log discrepancies untuk debugging
                        log_message('info', '[WarehousePO] Total discrepancies to save for unit ' . $id_unit . ': ' . count($discrepancies));
                        log_message('debug', '[WarehousePO] Discrepancies data: ' . json_encode($discrepancies));
                        
                        // Simpan discrepancy ke po_verification table (diluar transaction untuk memastikan tersimpan)
                        // Tapi tetap dalam transaction untuk konsistensi
                        try {
                            $saveResult = $this->saveVerificationDiscrepancies('unit', $id_unit, $po_id, $status, $discrepancies, $catatan);
                            if ($saveResult) {
                                log_message('info', '[WarehousePO] Successfully saved ' . count($discrepancies) . ' discrepancies to po_verification table');
                            } else {
                                log_message('error', '[WarehousePO] Failed to save discrepancies to po_verification table');
                            }
                        } catch (\Exception $e) {
                            log_message('error', '[WarehousePO] Exception while saving discrepancies: ' . $e->getMessage());
                            // Jangan rollback transaction karena update po_units sudah berhasil
                        }
                        
                        // Kirim notifikasi ke Purchasing
                        try {
                            $itemName = ($original['merk_unit'] ?? '') . ' ' . ($original['model_unit'] ?? '');
                            $this->notifyPurchasingForDiscrepancy('unit', $po_id, $itemName, $catatan, $discrepancies);
                        } catch (\Exception $e) {
                            log_message('warning', '[WarehousePO] Failed to notify Purchasing: ' . $e->getMessage());
                            // Jangan rollback transaction karena update po_units sudah berhasil
                        }
                    }
                    
                    // Panggil fungsi untuk update status PO utama
                    $this->updateOverallPOStatusForUnit($po_id);
                    
                    // Audit log (dengan pengecekan tabel)
                    if ($db->tableExists('verification_audit_log')) {
                        try {
                            // CRITICAL: Get user_id from session - try multiple methods
                            $userId = session()->get('user_id');
                            if (empty($userId)) {
                                $userId = session()->get('id');
                            }
                            if (empty($userId) && function_exists('auth_user')) {
                                $authUser = auth_user();
                                $userId = $authUser ? $authUser->id : null;
                            }
                            
                            // Log session data for debugging
                            $sessionData = session()->get();
                            log_message('info', '[WarehousePO] Session user_id: ' . ($userId ?? 'NULL') . ' | Session keys: ' . implode(', ', array_keys($sessionData)));
                            
                            if (empty($userId)) {
                                log_message('error', '[WarehousePO] ✗ CRITICAL: user_id is empty in session! Cannot log audit. Session data: ' . json_encode($sessionData));
                            } else {
                                // Ensure user_id is integer
                                $userId = (int)$userId;
                                
                                // Verify user exists in database
                                $userExists = $db->table('users')
                                    ->where('id', $userId)
                                    ->countAllResults() > 0;
                                
                                if (!$userExists) {
                                    log_message('error', '[WarehousePO] ✗ User ID ' . $userId . ' does not exist in users table!');
                                } else {
                                    log_message('info', '[WarehousePO] ✓ User ID ' . $userId . ' verified in users table');
                                }
                                
                                // Prepare audit log data
                                $logData = [
                                    'po_type' => 'unit',
                                    'source_id' => $id_unit,
                                    'po_id' => $po_id,
                                    'action' => 'verify',
                                    'status_before' => $original['status_verifikasi'] ?? null,
                                    'status_after' => $status,
                                    'user_id' => $userId,
                                    'notes' => mb_substr($catatan, 0, 500),
                                    'payload' => json_encode($snData),
                                    'created_at' => date_jakarta()
                                ];
                                
                                log_message('info', '[WarehousePO] Attempting to log audit: ' . json_encode($logData));
                                
                                // Insert directly using Query Builder for better control
                                $insertResult = $db->table('verification_audit_log')->insert($logData);
                                
                                if ($insertResult) {
                                    $insertId = $db->insertID();
                                    log_message('info', '[WarehousePO] ✓ Audit log saved successfully! ID: ' . $insertId . ' | User: ' . $userId . ' | Time: ' . $logData['created_at']);
                                } else {
                                    $dbError = $db->error();
                                    log_message('error', '[WarehousePO] ✗ Failed to insert audit log! Error: ' . json_encode($dbError) . ' | Data: ' . json_encode($logData));
                                }
                            }
                        } catch (\Exception $e) {
                            log_message('error', '[WarehousePO] Exception in audit log: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                        }
                    } else {
                        log_message('debug', '[WarehousePO] verification_audit_log table does not exist');
                    }
                    
                    $db->transCommit();
                    return $this->response->setJSON(['statusCode' => 200, 'message' => 'Verifikasi berhasil.']);
                } else {
                    // Re-enable validation
                    $this->pounitsmodel->skipValidation(false);
                    
                    $db->transRollback();
                    $errors = $this->pounitsmodel->errors();
                    $dbError = $db->error();
                    
                    // Log detail error
                    log_message('error', '[WarehousePO] Failed to update unit ' . $id_unit);
                    log_message('error', '[WarehousePO] Model errors: ' . json_encode($errors));
                    log_message('error', '[WarehousePO] DB error: ' . json_encode($dbError));
                    log_message('error', '[WarehousePO] Attempted data: ' . json_encode($dataToUpdate));
                    
                    // Cek apakah catatan terlalu panjang
                    if (!empty($dataToUpdate['catatan_verifikasi']) && strlen($dataToUpdate['catatan_verifikasi']) > 500) {
                        $errorMessage = 'Catatan verifikasi terlalu panjang (maksimal 500 karakter). Panjang saat ini: ' . strlen($dataToUpdate['catatan_verifikasi']);
                    } else {
                        $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Gagal update database.';
                        if (!empty($dbError['message'])) {
                            $errorMessage .= ' DB Error: ' . $dbError['message'];
                        }
                    }
                    
                    return $this->response->setJSON([
                        'statusCode' => 500, 
                        'message' => $errorMessage
                    ]);
                }
            } catch (\Exception $e) {
                // Re-enable validation
                $this->pounitsmodel->skipValidation(false);
                
                // Log error untuk debugging
                log_message('error', '[WarehousePO] Exception in verifyPoUnit: ' . $e->getMessage());
                log_message('error', '[WarehousePO] Stack trace: ' . $e->getTraceAsString());
                if ($db->transStatus()) { $db->transRollback(); }
                return $this->response->setJSON([
                    'statusCode' => 500, 
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        }
        return redirect()->to('/');
    }

    private function updateOverallPOStatusForUnit($po_id)
    {
        // 1. Hitung total item unit untuk PO ini
        $totalItems = $this->pounitsmodel->where('po_id', $po_id)->countAllResults();

        // 2. Hitung item unit yang sudah diverifikasi (Sesuai atau Tidak Sesuai)
        $verifiedItems = $this->pounitsmodel
            ->where('po_id', $po_id)
            ->whereIn('status_verifikasi', ['Sesuai', 'Tidak Sesuai'])
            ->countAllResults();

        // 3. Hanya lanjutkan jika semua item sudah diverifikasi
        if ($totalItems > 0 && $totalItems == $verifiedItems) {
            
            // 4. Periksa apakah ada item yang "Tidak Sesuai"
            $mismatchedItems = $this->pounitsmodel
                ->where('po_id', $po_id)
                ->where('status_verifikasi', 'Tidak Sesuai')
                ->countAllResults();

            $newStatus = '';
            if ($mismatchedItems > 0) {
                // Jika ada minimal satu item ditolak, status PO menjadi 'Selesai dengan Catatan'
                $newStatus = 'Selesai dengan Catatan';
            } else {
                // Jika semua item 'Sesuai', status PO menjadi 'completed'
                $newStatus = 'completed';
            }

            // 5. Update status di tabel purchase_orders
            if ($newStatus) {
                $this->purchasemodel->update($po_id, ['status' => $newStatus]);
            }
        }
        // Jika belum semua item diverifikasi, tidak ada yang diubah. Status PO tetap 'pending'.
    }

    public function verifyPoAttachment()
    {
        if ($this->request->isAJAX()) {
            $id_item = $this->request->getPost('id_item');
            $po_id   = $this->request->getPost('po_id');
            $status  = trim((string)$this->request->getPost('status'));
            $catatan = $this->request->getPost('catatan_verifikasi');

            // Ambil nilai dari form
            $snAttachment = trim((string)$this->request->getPost('serial_number'));            // SN Attachment atau SN Baterai
            $snCharger    = trim((string)$this->request->getPost('serial_number_charger'));
            $lokasi       = trim((string)$this->request->getPost('lokasi')) ?: 'POS 1';

            $db = \Config\Database::connect();
            $db->transBegin();

            $original = $this->poAttachmentModel->find($id_item);
            if (!$original) {
                return $this->response->setJSON(['success' => false, 'message' => 'PO Item tidak ditemukan.']);
            }

            $itemType = trim((string)($original['item_type'] ?? '')); // 'Attachment' | 'Battery'
            $statusNorm = strtolower($status);

            // Validasi SN saat status = "Sesuai"
            if ($statusNorm === 'sesuai') {
                if ($itemType === 'Attachment') {
                    if (empty($original['attachment_id'])) {
                        return $this->response->setJSON(['success'=>false,'message'=>'Model Attachment belum terdaftar di master.']);
                    }
                    if (!$snAttachment) {
                        return $this->response->setJSON(['success'=>false,'message'=>'Serial number Attachment wajib diisi.']);
                    }
                } elseif ($itemType === 'Battery') {
                    if (empty($original['baterai_id']) && empty($original['charger_id'])) {
                        return $this->response->setJSON(['success'=>false,'message'=>'PO item Battery tidak memiliki baterai/charger model.']);
                    }
                    if (!empty($original['baterai_id']) && !$snAttachment) {
                        return $this->response->setJSON(['success'=>false,'message'=>'Serial number Baterai wajib diisi.']);
                    }
                    if (!empty($original['charger_id']) && !$snCharger) {
                        return $this->response->setJSON(['success'=>false,'message'=>'Serial number Charger wajib diisi.']);
                    }
                }
            }

            // Update po_item
            $dataToUpdate = [
                'status_verifikasi'      => $status,
                'catatan_verifikasi'     => $catatan,
                'serial_number'          => $snAttachment ?: ($original['serial_number'] ?? null),
                'serial_number_charger'  => $snCharger   ?: ($original['serial_number_charger'] ?? null),
            ];

            try {
                if (!$this->poAttachmentModel->update($id_item, array_filter($dataToUpdate, fn($v)=>$v!==null && $v!==''))) {
                    $db->transRollback();
                    return $this->response->setJSON(['success'=>false,'message'=>'Gagal update PO item.']);
                }

                // Insert stok fisik hanya jika 'Sesuai'
                if ($statusNorm === 'sesuai') {
                    $verified = $this->poAttachmentModel->find($id_item);

                    $rows = [];
                    if ($itemType === 'Attachment') {
                        $rows[] = [
                            'po_id'               => (int)$verified['po_id'],
                            'attachment_id'       => (int)$verified['attachment_id'],
                            'sn_attachment'       => $snAttachment ?: $verified['serial_number'],
                            'id_inventory_unit'   => null,
                            'status_unit'         => 7,
                            'lokasi_penyimpanan'  => $lokasi,
                            'kondisi_fisik'       => 'Baik',
                            'kelengkapan'         => 'Lengkap',
                            'tanggal_masuk'       => date_jakarta(),
                            'catatan_inventory'   => 'Dari verifikasi PO: '.($catatan ?: 'Sesuai'),
                        ];
                    } elseif ($itemType === 'Battery') {
                        if (!empty($verified['baterai_id'])) {
                            $rows[] = [
                                'po_id'               => (int)$verified['po_id'],
                                'baterai_id'          => (int)$verified['baterai_id'],
                                'sn_baterai'          => $snAttachment ?: $verified['serial_number'],
                                'id_inventory_unit'   => null,
                                'status_unit'         => 7,
                                'lokasi_penyimpanan'  => $lokasi,
                                'kondisi_fisik'       => 'Baik',
                                'kelengkapan'         => 'Lengkap',
                                'tanggal_masuk'       => date_jakarta(),
                                'catatan_inventory'   => 'Dari verifikasi PO (Battery): '.($catatan ?: 'Sesuai'),
                            ];
                        }
                        if (!empty($verified['charger_id'])) {
                            $rows[] = [
                                'po_id'               => (int)$verified['po_id'],
                                'charger_id'          => (int)$verified['charger_id'],
                                'sn_charger'          => $snCharger ?: $verified['serial_number_charger'],
                                'id_inventory_unit'   => null,
                                'status_unit'         => 7,
                                'lokasi_penyimpanan'  => $lokasi,
                                'kondisi_fisik'       => 'Baik',
                                'kelengkapan'         => 'Lengkap',
                                'tanggal_masuk'       => date_jakarta(),
                                'catatan_inventory'   => 'Dari verifikasi PO (Charger): '.($catatan ?: 'Sesuai'),
                            ];
                        }
                    }

                    if (!$rows) {
                        $db->transRollback();
                        return $this->response->setJSON(['success'=>false,'message'=>'Tidak ada item fisik yang bisa dibuat.']);
                    }

                    // Simpan, dan tampilkan error validasi/DB jika gagal
                    // Hindari insertBatch karena kolom baris bisa berbeda (baterai vs charger)
                    // Normalisasi kunci agar konsisten, lalu insert satu per satu
                    $baseDefaults = [
                        'attachment_id'      => null,
                        'sn_attachment'      => null,
                        'baterai_id'         => null,
                        'sn_baterai'         => null,
                        'charger_id'         => null,
                        'sn_charger'         => null,
                        'id_inventory_unit'  => null,
                        'status_unit'        => 7,
                        'lokasi_penyimpanan' => $lokasi,
                        'kondisi_fisik'      => 'Baik',
                        'kelengkapan'        => 'Lengkap',
                    ];

                    foreach ($rows as $idx => $r) {
                        $payload = array_merge($baseDefaults, $r);
                        if (!$this->inventoryAttachmentModel->insert($payload)) {
                            $errors = $this->inventoryAttachmentModel->errors();
                            $dberr  = $db->error();
                            $db->transRollback();
                            return $this->response->setJSON([
                                'success'      => false,
                                'message'      => 'Gagal insert inventory pada baris '.($idx+1),
                                'model_errors' => $errors,
                                'db_error'     => $dberr,
                                'payload'      => $payload,
                            ]);
                        }
                    }
                }
                
                // Audit log untuk attachment
                if ($db->tableExists('verification_audit_log')) {
                    try {
                        $userId = session()->get('user_id') ?? session()->get('id');
                        if (empty($userId) && function_exists('auth_user')) {
                            $authUser = auth_user();
                            $userId = $authUser ? $authUser->id : null;
                        }
                        
                        if (!empty($userId)) {
                            $this->verificationAuditLogModel->log([
                                'po_type' => 'attachment',
                                'source_id' => $id_item,
                                'po_id' => $po_id,
                                'action' => 'verify',
                                'status_before' => $original['status_verifikasi'] ?? null,
                                'status_after' => $status,
                                'user_id' => (int)$userId,
                                'notes' => mb_substr($catatan, 0, 500),
                                'payload' => json_encode(['item_type' => $itemType, 'sn_attachment' => $snAttachment, 'sn_charger' => $snCharger])
                            ]);
                        }
                    } catch (\Exception $e) {
                        log_message('error', '[WarehousePO] Failed to log attachment audit: ' . $e->getMessage());
                    }
                }

                $db->transCommit();
                return $this->response->setJSON(['success'=>true,'message'=>'Verifikasi berhasil, stok dibuat.']);

            } catch (\Throwable $e) {
                if ($db->transStatus()) { $db->transRollback(); }
                log_message('error', '[WarehousePO] verifyPoAttachment error: '.$e->getMessage());
                return $this->response->setJSON(['success'=>false,'message'=>'Error: '.$e->getMessage()]);
            }
        }
        return redirect()->to('/');
    }

    /**
     * PO Attachment Verification Interface
     */
    public function poAttachment()
    {
        $itemsToVerify = $this->poAttachmentModel
            ->select('
                po_items.*, 
                po.id_po, po.no_po,
                CONCAT(attachment.tipe, " ", attachment.merk, " ", attachment.model) AS attachment_name, 
                baterai.merk_baterai, baterai.tipe_baterai,
                charger.merk_charger, charger.tipe_charger
            ')
            ->join('purchase_orders po', 'po.id_po = po_items.po_id')
            ->join('attachment', 'attachment.id_attachment = po_items.attachment_id', 'left')
            ->join('baterai', 'baterai.id = po_items.baterai_id', 'left')
            ->join('charger', 'charger.id_charger = po_items.charger_id', 'left')
            ->whereIn('po.status', ['pending', 'approved'])
            ->where('po_items.status_verifikasi', 'Belum Dicek')
            ->findAll();

        // Kelompokkan item berdasarkan po_id
        $detailGroup = [];
        foreach ($itemsToVerify as $item) {
            $po_id = $item['id_po'];
            if (!isset($detailGroup[$po_id])) {
                $detailGroup[$po_id] = [
                    'no_po' => $item['no_po'],
                    'data'  => []
                ];
            }
            $detailGroup[$po_id]['data'][] = $item;
        }

        $data = [
            'title'       => 'PO Attachment & Battery Verification',
            'detailGroup' => $detailGroup, // Kirim data yang sudah dikelompokkan
        ];

        return view('warehouse/purchase_orders/po_attachment', $data);
    }



    // METHOD BARU UNTUK MENAMPILKAN HALAMAN VERIFIKASI SPAREPART
    public function poSparepart()
    {
        $itemsToVerify = $this->poSparepartItemModel
            ->select('
                po_sparepart_items.id, po_sparepart_items.qty, po_sparepart_items.keterangan,
                po.id_po, po.no_po,
                s.kode, s.desc_sparepart
            ')
            ->join('purchase_orders po', 'po.id_po = po_sparepart_items.po_id')
            ->join('sparepart s', 's.id_sparepart = po_sparepart_items.sparepart_id')
            ->whereIn('po.status', ['pending', 'approved'])
            ->where('po_sparepart_items.status_verifikasi', 'Belum Dicek')
            ->findAll();

        // Kelompokkan item berdasarkan po_id
        $detailGroup = [];
        foreach ($itemsToVerify as $item) {
            $po_id = $item['id_po'];
            if (!isset($detailGroup[$po_id])) {
                $detailGroup[$po_id] = [
                    'no_po' => $item['no_po'],
                    'data'  => []
                ];
            }
            $detailGroup[$po_id]['data'][] = $item;
        }

        $data = [
            'title'       => 'PO Sparepart Verification',
            'detailGroup' => $detailGroup, // Kirim data yang sudah dikelompokkan
        ];

        return view('warehouse/purchase_orders/po_sparepart', $data);
    }

    /**
     * Update verification status for any PO type
     */

    /**
     * Mengembalikan item PO yang 'Tidak Sesuai' ke status 'Belum Dicek'
     * dan mengembalikan status PO utama ke 'pending'.
     */
    public function reverifyPO($po_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Akses ditolak.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Ubah status semua item unit yang "Tidak Sesuai" menjadi "Belum Dicek"
        $this->pounitsmodel
            ->where('po_id', $po_id)
            ->where('status_verifikasi', 'Tidak Sesuai')
            ->set(['status_verifikasi' => 'Belum Dicek'])
            ->update();
        
        // (Opsional) Lakukan hal yang sama untuk po_attachment atau po_sparepart jika perlu
        // $this->poAttachmentModel->where('po_id', $po_id)->...

        // 2. Ubah status PO utama kembali ke 'pending' agar masuk antrian lagi
        $this->purchasemodel->update($po_id, ['status' => 'pending']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui database.']);
        }

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Reset status unit untuk re-verification setelah barang baru datang
     */
    public function reverifyUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Akses ditolak.');
        }

        $idUnit = $this->request->getPost('id_unit');
        $poId = $this->request->getPost('po_id');

        if (empty($idUnit) || empty($poId)) {
            return $this->response->setJSON([
                'statusCode' => 400,
                'message' => 'ID unit dan PO ID wajib diisi.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Reset status verifikasi ke "Belum Dicek"
        $updated = $this->pounitsmodel->update($idUnit, [
            'status_verifikasi' => 'Belum Dicek',
            'catatan_verifikasi' => null // Clear catatan reject
        ]);

        // Hapus discrepancy records dari po_verification untuk item ini
        if ($db->tableExists('po_verification')) {
            $db->table('po_verification')
                ->where('po_type', 'unit')
                ->where('source_id', $idUnit)
                ->where('status_verifikasi', 'Tidak Sesuai')
                ->delete();
        }

        // Update PO status jika semua item sudah tidak ada yang "Tidak Sesuai"
        $remainingRejected = $this->pounitsmodel
            ->where('po_id', $poId)
            ->where('status_verifikasi', 'Tidak Sesuai')
            ->countAllResults();

        if ($remainingRejected == 0) {
            // Cek apakah ada item lain yang masih "Tidak Sesuai" (attachment/sparepart)
            $otherRejected = 0;
            if ($db->tableExists('po_attachment')) {
                $otherRejected += $db->table('po_attachment')
                    ->where('po_id', $poId)
                    ->where('status_verifikasi', 'Tidak Sesuai')
                    ->countAllResults();
            }
            if ($db->tableExists('po_sparepart_items')) {
                $otherRejected += $db->table('po_sparepart_items')
                    ->where('po_id', $poId)
                    ->where('status_verifikasi', 'Tidak Sesuai')
                    ->countAllResults();
            }

            // Jika tidak ada item rejected lagi, update PO status
            if ($otherRejected == 0) {
                $this->purchasemodel->update($poId, ['status' => 'pending']);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false || !$updated) {
            return $this->response->setJSON([
                'statusCode' => 500,
                'message' => 'Gagal reset status unit untuk re-verification.'
            ]);
        }

        log_message('info', "[WarehousePO] Unit {$idUnit} reset untuk re-verification oleh user " . session()->get('user_id'));

        return $this->response->setJSON([
            'statusCode' => 200,
            'message' => 'Status unit telah direset. Item sekarang siap untuk verifikasi ulang.'
        ]);
    }

    /**
     * Reset status attachment untuk re-verification setelah barang baru datang
     */
    public function reverifyAttachment()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Akses ditolak.');
        }

        $idAttachment = $this->request->getPost('id_attachment');
        $poId = $this->request->getPost('po_id');

        if (empty($idAttachment) || empty($poId)) {
            return $this->response->setJSON([
                'statusCode' => 400,
                'message' => 'ID attachment dan PO ID wajib diisi.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $updated = $this->poAttachmentModel->update($idAttachment, [
            'status_verifikasi' => 'Belum Dicek',
            'catatan_verifikasi' => null
        ]);

        // Hapus discrepancy records
        if ($db->tableExists('po_verification')) {
            $db->table('po_verification')
                ->where('po_type', 'attachment')
                ->where('source_id', $idAttachment)
                ->where('status_verifikasi', 'Tidak Sesuai')
                ->delete();
        }

        $db->transComplete();

        if ($db->transStatus() === false || !$updated) {
            return $this->response->setJSON([
                'statusCode' => 500,
                'message' => 'Gagal reset status attachment untuk re-verification.'
            ]);
        }

        return $this->response->setJSON([
            'statusCode' => 200,
            'message' => 'Status attachment telah direset. Item sekarang siap untuk verifikasi ulang.'
        ]);
    }

    /**
     * Reset status sparepart untuk re-verification setelah barang baru datang
     */
    public function reverifySparepart()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Akses ditolak.');
        }

        $idSparepart = $this->request->getPost('id_sparepart');
        $poId = $this->request->getPost('po_id');

        if (empty($idSparepart) || empty($poId)) {
            return $this->response->setJSON([
                'statusCode' => 400,
                'message' => 'ID sparepart dan PO ID wajib diisi.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $updated = $this->poSparepartItemModel->update($idSparepart, [
            'status_verifikasi' => 'Belum Dicek',
            'catatan_verifikasi' => null
        ]);

        // Hapus discrepancy records
        if ($db->tableExists('po_verification')) {
            $db->table('po_verification')
                ->where('po_type', 'sparepart')
                ->where('source_id', $idSparepart)
                ->where('status_verifikasi', 'Tidak Sesuai')
                ->delete();
        }

        $db->transComplete();

        if ($db->transStatus() === false || !$updated) {
            return $this->response->setJSON([
                'statusCode' => 500,
                'message' => 'Gagal reset status sparepart untuk re-verification.'
            ]);
        }

        return $this->response->setJSON([
            'statusCode' => 200,
            'message' => 'Status sparepart telah direset. Item sekarang siap untuk verifikasi ulang.'
        ]);
    }

    /**
     * Mengubah status PO menjadi 'cancelled'.
     */
    public function cancelPO($po_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Akses ditolak.');
        }

        $result = $this->purchasemodel->update($po_id, ['status' => 'cancelled']);

        if ($result) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengubah status PO.']);
        }
    }
    
    public function updateVerification()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        try {
            $poType = $this->request->getPost('po_type');
            $poId = $this->request->getPost('po_id');
            $status = $this->request->getPost('status');
            $comments = $this->request->getPost('comments');
            $verifiedBy = session('user_id');

            switch ($poType) {
                case 'unit':
                    $result = $this->pounitsmodel->updateVerificationStatus($poId, $status, $comments, $verifiedBy);
                    break;
                case 'attachment':
                    $result = $this->poAttachmentModel->updateVerificationStatus($poId, $status, $comments, $verifiedBy);
                    break;
                case 'sparepart':
                    $result = $this->poSparepartItemModel->updateVerificationStatus($poId, $status, $comments, $verifiedBy);
                    break;
                default:
                    throw new \Exception('Invalid PO type');
            }

            // Kirim notifikasi ke warehouse jika status diverifikasi
            if ($result && in_array($status, ['Sesuai', 'Tidak Sesuai'])) {
                $this->sendWarehouseNotification($poType, $poId, $status, $verifiedBy);
            }

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Verification status updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update verification status'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Verification update failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'System error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Kirim notifikasi ke warehouse saat PO diverifikasi
     */
    private function sendWarehouseNotification($poType, $poId, $status, $verifiedBy)
    {
        // Ambil nomor PO
        $noPO = '-';
        switch ($poType) {
            case 'unit':
                $po = $this->pounitsmodel->find($poId);
                $noPO = $po['no_po'] ?? $poId;
                break;
            case 'attachment':
                $po = $this->poAttachmentModel->find($poId);
                $noPO = $po['no_po'] ?? $poId;
                break;
            case 'sparepart':
                $po = $this->poSparepartItemModel->find($poId);
                $noPO = $po['no_po'] ?? $poId;
                break;
        }

        $message = "PO $noPO ($poType) telah diverifikasi dengan status: $status.";

        // Insert ke tabel notifications (gunakan NotificationModel)
        $notificationModel = new \App\Models\NotificationModel();
        $notificationModel->insert([
            'title' => 'PO Verification',
            'message' => $message,
            'target_role' => 'warehouse',
            'data_id' => $poId,
            'data_type' => $poType,
            'created_by' => $verifiedBy,
            'created_at' => date_jakarta(),
        ]);
    }

    /**
     * Get DataTable data for PO Unit Verification
     */
    private function getPoUnitVerificationDataTable()
    {
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $searchValue = $this->request->getPost('search')['value'] ?? '';

        $data = $this->pounitsmodel->getPurchaseOrdersWithDetails();
        
        // Filter data if search value exists
        if (!empty($searchValue)) {
            $data = array_filter($data, function($item) use ($searchValue) {
                return stripos($item['no_po'], $searchValue) !== false ||
                       stripos($item['nama_supplier'], $searchValue) !== false ||
                       stripos($item['merek_unit'], $searchValue) !== false ||
                       stripos($item['serial_number'], $searchValue) !== false;
            });
        }

        $recordsTotal = count($data);
        $recordsFiltered = $recordsTotal;

        // Paginate
        $data = array_slice($data, $start, $length);

        // Format data for DataTable with verification focus
        $formattedData = [];
        foreach ($data as $item) {
            $statusIcon = $this->pounitsmodel->getStatusIcon($item['status_verification']);
            $statusBadge = $this->pounitsmodel->getStatusBadgeClass($item['status_verification']);
            
            $formattedData[] = [
                'no_po' => $item['no_po'],
                'nama_supplier' => $item['nama_supplier'] ?? '-',
                'merek_unit' => $item['merek_unit'],
                'model_unit' => $item['model_unit'],
                'serial_number' => $item['serial_number'],
                'status' => '<i class="' . $statusIcon . '"></i> <span class="badge badge-' . $statusBadge . '">' . 
                           $this->pounitsmodel->getVerificationStatusOptions()[$item['status_verification']] . '</span>',
                'verified_by' => $item['verified_by'] ? 'User #' . $item['verified_by'] : '-',
                'verification_date' => $item['verification_date'] ? date('d/m/Y H:i', strtotime($item['verification_date'])) : '-',
                'actions' => $this->generateVerificationActions('unit', $item['id_po_unit'], $item['status_verification'])
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData
        ]);
    }

    /**
     * Get DataTable data for PO Attachment Verification
     */
    private function getPoAttachmentVerificationDataTable()
    {
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $searchValue = $this->request->getPost('search')['value'] ?? '';

        $data = $this->poAttachmentModel->getPurchaseOrdersWithDetails();
        
        // Filter data if search value exists
        if (!empty($searchValue)) {
            $data = array_filter($data, function($item) use ($searchValue) {
                return stripos($item['no_po'], $searchValue) !== false ||
                       stripos($item['nama_supplier'], $searchValue) !== false ||
                       stripos($item['nama_barang'], $searchValue) !== false;
            });
        }

        $recordsTotal = count($data);
        $recordsFiltered = $recordsTotal;

        // Paginate
        $data = array_slice($data, $start, $length);

        // Format data for DataTable with verification focus
        $formattedData = [];
        foreach ($data as $item) {
            $statusIcon = $this->poAttachmentModel->getStatusIcon($item['status_verifikasi']);
            $statusBadge = $this->poAttachmentModel->getStatusBadgeClass($item['status_verifikasi']);
            
            $formattedData[] = [
                'no_po' => $item['no_po'],
                'nama_supplier' => $item['nama_supplier'] ?? '-',
                'nama_barang' => $item['nama_barang'],
                'model_barang' => $item['model_barang'] ?? '-',
                'jumlah' => $item['jumlah'],
                'status' => '<i class="' . $statusIcon . '"></i> <span class="badge badge-' . $statusBadge . '">' . 
                           $this->poAttachmentModel->getVerificationStatusOptions()[$item['status_verifikasi']] . '</span>',
                'verified_by' => $item['verified_by'] ? 'User #' . $item['verified_by'] : '-',
                'verification_date' => $item['verification_date'] ? date('d/m/Y H:i', strtotime($item['verification_date'])) : '-',
                'actions' => $this->generateVerificationActions('attachment', $item['id_po_attachment'], $item['status_verifikasi'])
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData
        ]);
    }

    // METHOD BARU UNTUK PROSES VERIFIKASI
    public function verifyPoSparepart()
    {
        if ($this->request->isAJAX()) {
            $id_item = $this->request->getPost('id_item');
            $po_id = $this->request->getPost('po_id');
            $status = $this->request->getPost('status');
            $catatan = $this->request->getPost('catatan_verifikasi');

            $db = \Config\Database::connect();
            $db->transBegin();
            $original = $this->poSparepartItemModel->find($id_item);

            // 1. Update status verifikasi di tabel po_sparepart_items
            $this->poSparepartItemModel->update($id_item, ['status_verifikasi' => $status, 'catatan_verifikasi' => $catatan]);

            // 2. Jika status "Sesuai", tambahkan ke inventory
            if ($status === 'Sesuai') {
                $verifiedItem = $this->poSparepartItemModel->find($id_item);
                
                if ($verifiedItem) {
                    $sparepartId = $verifiedItem['sparepart_id'];
                    $quantity = (int)$verifiedItem['qty'];
                    $lokasi = 'POS 1'; // Lokasi default

                    // PERBAIKAN: Menggunakan query "INSERT ... ON DUPLICATE KEY UPDATE"
                    // Ini adalah cara paling andal untuk menambah atau memperbarui stok dalam satu langkah.
                    $sql = "INSERT INTO inventory_spareparts (sparepart_id, stok, lokasi_rak) VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE stok = stok + ?";
                    
                    $db->query($sql, [$sparepartId, $quantity, $lokasi, $quantity]);
                }
            }
            
            if ($db->transStatus() === false) {
                // Jika transaksi gagal, kirim pesan error
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui database. Transaksi dibatalkan.']);
            } else {
                // Jika transaksi berhasil, perbarui status PO utama
                try {
                    $this->updateOverallPOStatus($po_id);
                } catch (\Exception $e) {
                    log_message('error', '[WarehousePO] Gagal update status PO: ' . $e->getMessage());
                }
                // Audit log untuk sparepart - ensure user_id is set correctly
                $userId = session()->get('user_id') ?? session()->get('id');
                if (empty($userId) && function_exists('auth_user')) {
                    $authUser = auth_user();
                    $userId = $authUser ? $authUser->id : null;
                }
                
                if (!empty($userId)) {
                    $this->verificationAuditLogModel->log([
                        'po_type' => 'sparepart',
                        'source_id' => $id_item,
                        'po_id' => $po_id,
                        'action' => 'verify',
                        'status_before' => $original['status_verifikasi'] ?? null,
                        'status_after' => $status,
                        'user_id' => (int)$userId,
                        'notes' => mb_substr($catatan, 0, 500),
                        'payload' => json_encode(['qty' => $this->request->getPost('qty')])
                    ]);
                } else {
                    log_message('warning', '[WarehousePO] Cannot log sparepart audit: user_id is empty');
                }
                $db->transCommit();
                return $this->response->setJSON(['success' => true]);
            }
        }
        return redirect()->to('/');
    }

    private function updateOverallPOStatus($po_id)
    {
        $totalItems = $this->poSparepartItemModel->where('po_id', $po_id)->countAllResults();
        $verifiedItems = $this->poSparepartItemModel
            ->where('po_id', $po_id)
            ->whereIn('status_verifikasi', ['Sesuai', 'Tidak Sesuai'])
            ->countAllResults();

        // Jika semua item sudah diverifikasi, ubah status PO menjadi 'completed'
        if ($totalItems > 0 && $totalItems == $verifiedItems) {
            // Cek apakah ada item yang "Tidak Sesuai"
            $mismatchedItems = $this->poSparepartItemModel
                ->where('po_id', $po_id)
                ->where('status_verifikasi', 'Tidak Sesuai')
                ->countAllResults();

            if ($mismatchedItems > 0) {
                // Jika ada item yang tidak sesuai, set status ke "Selesai dengan Catatan"
                $newStatus = 'Selesai dengan Catatan';
            } else {
                // Jika semua sesuai, set status ke "completed"
                $newStatus = 'completed';
            }

            $this->purchasemodel->update($po_id, ['status' => $newStatus]);
        }
    }
    
    /**
     * Mengambil data untuk DataTable verifikasi PO Sparepart.
     * Dibuat private karena hanya dipanggil dari dalam controller ini.
     */
    private function getPoSparepartVerificationDataTable()
    {
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $searchValue = $this->request->getPost('search')['value'] ?? '';
        $orderColumn = $this->request->getPost('columns')[$this->request->getPost('order')[0]['column']]['data'] ?? 'id';
        $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';

        // Query Builder untuk mengambil data
        $db = \Config\Database::connect();
        $builder = $db->table('po_sparepart_items as psi')
            ->select('
                psi.id, psi.qty, psi.status_verifikasi,
                po.no_po,
                s.kode, s.desc_sparepart,
                sup.nama_supplier
            ')
            ->join('purchase_orders as po', 'po.id_po = psi.po_id')
            ->join('sparepart as s', 's.id_sparepart = psi.sparepart_id')
            ->join('suppliers as sup', 'sup.id_supplier = po.supplier_id');

        // Menghitung total record sebelum filter
        $recordsTotal = $builder->countAllResults(false); // false agar tidak me-reset query

        // Menerapkan filter pencarian
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('po.no_po', $searchValue)
                ->orLike('sup.nama_supplier', $searchValue)
                ->orLike('s.kode', $searchValue)
                ->orLike('s.desc_sparepart', $searchValue)
                ->groupEnd();
        }

        // Menghitung total record setelah filter
        $recordsFiltered = $builder->countAllResults(false);

        // Menerapkan sorting dan pagination
        $builder->orderBy($orderColumn, $orderDir)->limit($length, $start);
        
        $data = $builder->get()->getResultArray();

        // Format data untuk response DataTable
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'no_po'         => esc($item['no_po']),
                'nama_supplier' => esc($item['nama_supplier']),
                'deskripsi'     => esc($item['kode'] . ' - ' . $item['desc_sparepart']),
                'jumlah'        => $item['qty'],
                'status'        => $this->formatStatusBadge($item['status_verifikasi']),
                'actions'       => $this->generateVerificationActions('sparepart', $item['id'], $item['status_verifikasi'])
            ];
        }

        return $this->response->setJSON([
            'draw'            => intval($draw),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $formattedData
        ]);
    }

    /**
     * Helper function untuk membuat badge status.
     */
    private function formatStatusBadge($status)
    {
        $badgeClass = 'bg-secondary';
        if ($status === 'Belum Dicek') $badgeClass = 'bg-warning text-dark';
        if ($status === 'Sesuai') $badgeClass = 'bg-success';
        if ($status === 'Tidak Sesuai') $badgeClass = 'bg-danger';
        return "<span class='badge {$badgeClass}'>{$status}</span>";
    }

    /**
     * Save verification discrepancies to po_verification table
     */
    private function saveVerificationDiscrepancies($poType, $sourceId, $poId, $status, $discrepancies = [], $catatan = '')
    {
        try {
            $db = \Config\Database::connect();
            
            // Check if po_verification table exists
            if (!$db->tableExists('po_verification')) {
                log_message('warning', '[WarehousePO] po_verification table does not exist, skipping discrepancy save');
                return false;
            }
            
            $userId = session()->get('user_id');
            
            if ($status === 'Sesuai' && empty($discrepancies)) {
                log_message('debug', '[WarehousePO] Status is Sesuai and no discrepancies, skipping save');
                return true;
            }
            
            if (empty($discrepancies)) {
                log_message('warning', '[WarehousePO] No discrepancies to save for ' . $poType . ' ID ' . $sourceId);
                return false;
            }
            
            $insertedCount = 0;
            foreach ($discrepancies as $index => $disc) {
                // Validasi data discrepancy
                if (empty($disc['field_name'])) {
                    log_message('warning', '[WarehousePO] Skipping discrepancy at index ' . $index . ' - missing field_name');
                    continue;
                }
                
                $discrepancyType = 'Minor';
                if (empty($disc['database_value']) && !empty($disc['real_value'])) {
                    $discrepancyType = 'Missing';
                } elseif (!empty($disc['database_value']) && !empty($disc['real_value']) && 
                          $disc['database_value'] !== $disc['real_value']) {
                    $majorFields = ['sn_unit', 'sn_mesin', 'serial_number', 'merk', 'model', 'model_unit'];
                    $discrepancyType = in_array($disc['field_name'], $majorFields) ? 'Major' : 'Minor';
                }
                
                $data = [
                    'po_type' => $poType,
                    'source_id' => $sourceId,
                    'po_id' => $poId,
                    'field_name' => $disc['field_name'],
                    'database_value' => $disc['database_value'] ?? null,
                    'real_value' => $disc['real_value'] ?? null,
                    'discrepancy_type' => $discrepancyType,
                    'status_verifikasi' => $status,
                    'catatan' => $catatan,
                    'verified_by' => $userId,
                    'created_at' => date_jakarta()
                ];
                
                log_message('debug', '[WarehousePO] Inserting discrepancy: ' . json_encode($data));
                
                if ($db->table('po_verification')->insert($data)) {
                    $insertedCount++;
                } else {
                    $error = $db->error();
                    log_message('error', '[WarehousePO] Failed to insert discrepancy: ' . json_encode($error));
                }
            }
            
            log_message('info', '[WarehousePO] Inserted ' . $insertedCount . ' out of ' . count($discrepancies) . ' discrepancies');
            
            return $insertedCount > 0;
        } catch (\Exception $e) {
            log_message('error', '[WarehousePO] Exception in saveVerificationDiscrepancies: ' . $e->getMessage());
            log_message('error', '[WarehousePO] Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Notify Purchasing team for discrepancy
     */
    private function notifyPurchasingForDiscrepancy($poType, $poId, $itemName, $catatan = '', $discrepancies = [])
    {
        try {
            $db = \Config\Database::connect();
            
            // Check if notifications table exists
            if (!$db->tableExists('notifications')) {
                log_message('warning', '[WarehousePO] notifications table does not exist, skipping notification');
                return false;
            }
            
            $po = $db->table('purchase_orders')->where('id_po', $poId)->get()->getRowArray();
            if (!$po) {
                log_message('error', '[WarehousePO] PO not found: ' . $poId);
                return false;
            }
            
            $purchasingUsers = $db->table('users u')
                ->select('u.id')
                ->join('user_roles ur', 'ur.user_id = u.id', 'left')
                ->join('divisions d', 'd.id = ur.division_id', 'left')
                ->where('u.is_active', 1)
                ->groupStart()
                    ->where('LOWER(d.name)', 'purchasing')
                    ->orWhere('d.id', 6)
                ->groupEnd()
                ->groupBy('u.id')
                ->get()
                ->getResultArray();
            
            if (empty($purchasingUsers)) {
                log_message('warning', '[WarehousePO] No active users found in Purchasing division');
                return false;
            }
            
            $discrepancySummary = '';
            if (!empty($discrepancies)) {
                $discrepancySummary = "\n\nDetail Ketidaksesuaian:\n";
                foreach ($discrepancies as $disc) {
                    $discrepancySummary .= "- {$disc['field_name']}: Database = '{$disc['database_value']}', Real = '{$disc['real_value']}'\n";
                }
            }
            
            $title = "Verifikasi Tidak Sesuai - PO: {$po['no_po']}";
            $message = "Item verifikasi tidak sesuai dengan database:\n\n";
            $message .= "PO Number: {$po['no_po']}\n";
            $message .= "Tipe Item: " . ucfirst($poType) . "\n";
            $message .= "Item: {$itemName}\n";
            if ($catatan) {
                $message .= "Alasan Reject: {$catatan}\n";
            }
            $message .= $discrepancySummary;
            $message .= "\nSilakan review dan tindak lanjuti.";
            
            $notificationModel = new \App\Models\NotificationModel();
            $sentCount = 0;
            
            foreach ($purchasingUsers as $user) {
                $notificationData = [
                    'user_id' => $user['id'],
                    'title' => $title,
                    'message' => $message,
                    'type' => 'warning',
                    'icon' => 'exclamation-triangle',
                    'related_module' => 'purchasing',
                    'related_id' => $poId,
                    'url' => base_url('purchasing/purchase-orders/detail/' . $poId),
                    'is_read' => 0, // Explicitly set to unread
                    'created_at' => date_jakarta()
                ];
                
                log_message('debug', '[WarehousePO] Creating notification for user ' . $user['id'] . ': ' . json_encode($notificationData));
                
                if ($notificationModel->insert($notificationData)) {
                    $sentCount++;
                    log_message('info', '[WarehousePO] Notification created successfully for user ' . $user['id']);
                } else {
                    $errors = $notificationModel->errors();
                    log_message('error', '[WarehousePO] Failed to create notification for user ' . $user['id'] . ': ' . json_encode($errors));
                }
            }
            
            log_message('info', "[WarehousePO] Sent discrepancy notification to {$sentCount} Purchasing users for PO {$poId}");
            return $sentCount > 0;
            
        } catch (\Exception $e) {
            log_message('error', '[WarehousePO] Failed to notify Purchasing: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper function untuk membuat tombol aksi.
     */
    private function generateVerificationActions($type, $id, $status)
    {
        if ($status !== 'Belum Dicek') {
            return '<span class="text-success"><i class="fas fa-check-circle"></i> Terverifikasi</span>';
        }

        // Ganti URL dan fungsi onclick sesuai kebutuhan Anda
        return '
            <div class="btn-group btn-group-sm">
                <button class="btn btn-success" onclick="verifyItem('.$id.', \'Sesuai\')">Sesuai</button>
                <button class="btn btn-danger" onclick="verifyItem('.$id.', \'Tidak Sesuai\')">Tidak Sesuai</button>
            </div>
        ';
    }

    /**
     * Get verification summary for dashboard
     */
    private function getVerificationSummary()
    {
        $unitStats = $this->pounitsmodel->getPOStats();
        $attachmentStats = $this->poAttachmentModel->getPOStats();
        $sparepartStats = $this->poSparepartItemModel->getPOStats();
        
        return [
            'total_pos' => $unitStats['total'] + $attachmentStats['total'] + $sparepartStats['total'],
            'pending_verification' => $unitStats['belum_dicek'] + $attachmentStats['belum_dicek'] + $sparepartStats['belum_dicek'],
            'verified_sesuai' => $unitStats['sesuai'] + $attachmentStats['sesuai'] + $sparepartStats['sesuai'],
            'verified_tidak_sesuai' => $unitStats['tidak_sesuai'] + $attachmentStats['tidak_sesuai'] + $sparepartStats['tidak_sesuai'],
            'completion_rate' => $this->calculateOverallCompletionRate($unitStats, $attachmentStats, $sparepartStats)
        ];
    }

    /**
     * Calculate overall completion rate
     */
    private function calculateOverallCompletionRate($unitStats, $attachmentStats, $sparepartStats)
    {
        $totalPOs = $unitStats['total'] + $attachmentStats['total'] + $sparepartStats['total'];
        $completedPOs = ($unitStats['sesuai'] + $unitStats['tidak_sesuai']) +
                       ($attachmentStats['sesuai'] + $attachmentStats['tidak_sesuai']) +
                       ($sparepartStats['sesuai'] + $sparepartStats['tidak_sesuai']);
        
        return $totalPOs > 0 ? round(($completedPOs / $totalPOs) * 100, 2) : 0;
    }
}