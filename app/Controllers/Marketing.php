<?php

namespace App\Controllers;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\SpkModel;
use App\Models\KontrakModel;
use App\Models\KontrakSpesifikasiModel;
use App\Models\QuotationModel;
use App\Models\QuotationSpecificationModel;
use App\Models\InventoryUnitModel;
use App\Models\InventoryAttachmentModel;
use App\Models\InventoryBatteryModel;
use App\Models\InventoryChargerModel;
use App\Models\InventoryComponentHelper;
use App\Models\DeliveryInstructionModel;
use App\Models\DeliveryItemModel;
use App\Models\NotificationModel;
use App\Services\ExportService;
use App\Traits\ActivityLoggingTrait;
use App\Traits\DateFilterTrait;
use Dompdf\Dompdf;
use Dompdf\Options;

class Marketing extends BaseDataTableController
{
    use ActivityLoggingTrait;
    use DateFilterTrait;
    
    protected $db;
    protected $spkModel;
    protected $kontrakModel;
    protected $kontrakSpesifikasiModel;
    protected $quotationModel;
    protected $quotationSpecificationModel;
    protected $unitModel;
    protected $attModel;
    protected $batteryModel;
    protected $chargerModel;
    protected $componentHelper;
    protected $diModel;
    protected $diItemModel;
    protected $notifModel;
    protected $performanceService;
    protected $customerModel;
    protected $exportService;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger); // Initialize BaseDataTableController
        $this->db = \Config\Database::connect();
        $this->spkModel = new SpkModel();
        $this->kontrakModel = new KontrakModel();
        $this->kontrakSpesifikasiModel = new KontrakSpesifikasiModel();
        $this->quotationModel = new QuotationModel();
        $this->quotationSpecificationModel = new QuotationSpecificationModel();
        $this->unitModel = new InventoryUnitModel();
        $this->attModel = new InventoryAttachmentModel();
        $this->batteryModel = new InventoryBatteryModel();
        $this->chargerModel = new InventoryChargerModel();
        $this->componentHelper = new InventoryComponentHelper();
        $this->diModel = new DeliveryInstructionModel();
        $this->diItemModel = new DeliveryItemModel();
        $this->notifModel = class_exists(\App\Models\NotificationModel::class) ? new NotificationModel() : null;
        $this->performanceService = new \App\Services\PerformanceService();
        $this->exportService = new ExportService();
    }

    /**
     * Marketing dashboard index page
     */
    public function index()
    {
        // Load simple_rbac helper
        helper('simple_rbac');
        
        // Extract contract ID from URL for auto-opening modal (from notification deep linking)
        $uri = service('uri');
        $autoOpenContractId = null;
        
        // Check if URL matches /marketing/contracts/view/{id}
        $segments = $uri->getSegments();
        if (count($segments) >= 3 && $segments[1] === 'contracts' && $segments[2] === 'view' && isset($segments[3]) && is_numeric($segments[3])) {
            $autoOpenContractId = (int)$segments[3];
        }
        
        // Get marketing statistics
        $marketing_stats = $this->getMarketingDashboardStats();
        
        // Get recent quotations (last 5)
        $recent_quotations = $this->getRecentQuotationsForDashboard();
        
        // Get active contracts (last 5)
        $active_contracts = $this->getActiveContractsForDashboard();
        
        // Get revenue data for chart (last 6 months)
        $revenue_data = $this->getRevenueDataForChart();
        
        $data = [
            'title' => 'Marketing Dashboard',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/marketing' => 'Marketing'
            ],
            'marketing_stats' => $marketing_stats,
            'recent_quotations' => $recent_quotations,
            'active_contracts' => $active_contracts,
            'revenue_data' => $revenue_data,
            'autoOpenContractId' => $autoOpenContractId
        ];
        
        return view('marketing/index', $data);
    }

    public function availableUnits()
    {
        // Check permission: Marketing perlu akses ke warehouse inventory (cross-division)
        // Bisa menggunakan module permission (warehouse.access) atau resource permission (warehouse.inventory.view)
        if (!$this->canAccess('warehouse') && !$this->canViewResource('warehouse', 'inventory')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda tidak memiliki izin'
                ])->setStatusCode(403);
            }
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki izin');
        }
        
        return view('marketing/unit_tersedia');
    }

    /**
     * Export Contract Data to Excel (REFACTORED)
     * 
     * Detailed export with contracts -> customers -> units
     * Uses ExportService for proper MVC separation
     */
    public function exportKontrak()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.kontrak')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.kontrak');
        }
        
        // Log export activity
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'kontrak', 0, 'Export Kontrak Data to Excel', [
                'module_name' => 'MARKETING',
                'submenu_item' => 'Kontrak',
                'business_impact' => 'LOW'
            ]);
        }
        
        // Get contract data from database
        $query = $this->db->query("
            SELECT 
                k.*, 
                c.customer_name, 
                (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as location_name,
                (SELECT cl.city FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as city,
                iu.no_unit,
                iu.serial_number,
                iu.tahun_unit,
                mu.model_unit,
                mu.merk_unit,
                su.status_unit
            FROM kontrak k
            LEFT JOIN customers c ON c.id = k.customer_id
            -- Updated: JOIN via kontrak_unit junction table (source of truth)
            LEFT JOIN kontrak_unit ku ON ku.kontrak_id = k.id AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
            LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
            ORDER BY k.dibuat_pada DESC, iu.no_unit ASC
        ");
        
        $data = $query->getResultArray();
        
        // Format data for export
        foreach ($data as &$row) {
            $row['periode_sewa'] = '-';
            if (!empty($row['tanggal_mulai']) && !empty($row['tanggal_berakhir'])) {
                $row['periode_sewa'] = date('d/m/y', strtotime($row['tanggal_mulai'])) . ' - ' . date('d/m/y', strtotime($row['tanggal_berakhir']));
            }
            $row['nilai_formatted'] = $row['nilai_total'] ? 'Rp ' . number_format($row['nilai_total'], 0, ',', '.') : '-';
            $row['unit_model'] = trim(($row['merk_unit'] ?? '') . ' ' . ($row['model_unit'] ?? ''));
        }
        
        // Define headers for Excel
        $headers = [
            'no_kontrak' => 'Nomor Kontrak',
            'customer_po_number' => 'No PO Marketing',
            'customer_name' => 'Customer',
            'location_name' => 'Lokasi',
            'city' => 'Kota',
            'jenis_sewa' => 'Jenis Sewa',
            'periode_sewa' => 'Periode Sewa',
            'nilai_formatted' => 'Nilai Kontrak',
            'status' => 'Status Kontrak',
            'no_unit' => 'No Unit',
            'unit_model' => 'Model Unit',
            'serial_number' => 'Serial Number',
            'tahun_unit' => 'Tahun',
            'status_unit' => 'Status Unit'
        ];
        
        // Export using ExportService
        return $this->exportService->exportToExcel($data, $headers, 'Contract Management', [
            'filename' => 'Kontrak_PO_Rental_' . date('Y-m-d_H-i-s') . '.xlsx',
            'author' => 'OPTIMA Marketing',
            'subject' => 'Contract Management Report (Detailed)'
        ]);
    }

    /**
     * Export Customer Management Data to Excel (REFACTORED)
     * 
     * Detailed export with customers -> locations -> contracts -> units
     * Uses ExportService for proper MVC separation
     */
    public function exportCustomer()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.customer')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.customer');
        }
        
        // Log export activity
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'customers', 0, 'Export Customer Data to Excel', [
                'module_name' => 'MARKETING',
                'submenu_item' => 'Customer Management',
                'business_impact' => 'LOW'
            ]);
        }
        
        // Get customer data from database
        $query = $this->db->query("
            SELECT 
                c.customer_code,
                c.customer_name,
                c.is_active as customer_status,
                c.created_at as customer_created,
                a.area_name,
                cl.location_name,
                cl.city,
                cl.address,
                cl.contact_person as pic_name,
                cl.phone as pic_phone,
                k.no_kontrak,
                k.customer_po_number,
                k.jenis_sewa,
                k.tanggal_mulai,
                k.tanggal_berakhir,
                k.nilai_total,
                k.status as kontrak_status,
                iu.no_unit,
                iu.serial_number,
                iu.tahun_unit,
                mu.model_unit,
                mu.merk_unit,
                su.status_unit
            FROM customers c
            LEFT JOIN customer_locations cl ON cl.customer_id = c.id
            LEFT JOIN areas a ON a.id = iu.area_id
            LEFT JOIN kontrak k ON k.customer_id = c.id
            -- Updated: JOIN via kontrak_unit junction table (source of truth)
            LEFT JOIN kontrak_unit ku ON ku.kontrak_id = k.id AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
            LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
            ORDER BY c.customer_name ASC, cl.location_name ASC, k.tanggal_mulai DESC, iu.no_unit ASC
        ");
        
        $data = $query->getResultArray();
        
        // Format data for export
        foreach ($data as &$row) {
            $row['periode_sewa'] = '-';
            if (!empty($row['tanggal_mulai']) && !empty($row['tanggal_berakhir'])) {
                $row['periode_sewa'] = date('d/m/y', strtotime($row['tanggal_mulai'])) . ' - ' .  date('d/m/y', strtotime($row['tanggal_berakhir']));
            }
            $row['pic_full'] = trim(($row['pic_name'] ?? '-') . ' ' .  ($row['pic_phone'] ?? ''));
            $row['unit_model'] = trim(($row['merk_unit'] ?? '') . ' ' . ($row['model_unit'] ?? ''));
            $row['nilai_formatted'] = $row['nilai_total'] ? 'Rp ' . number_format($row['nilai_total'], 0, ',', '.') : '-';
        }
        
        // Define headers for Excel
        $headers = [
            'customer_code' => 'Kode Customer',
            'customer_name' => 'Nama Customer',
            'area_name' => 'Area',
            'location_name' => 'Lokasi Cabang',
            'city' => 'Kota',
            'address' => 'Alamat',
            'pic_full' => 'PIC',
            'no_kontrak' => 'No Kontrak',
            'customer_po_number' => 'No PO',
            'jenis_sewa' => 'Jenis Sewa',
            'kontrak_status' => 'Status Kontrak',
            'nilai_formatted' => 'Nilai Kontrak',
            'periode_sewa' => 'Periode Sewa',
            'no_unit' => 'No Unit',
            'unit_model' => 'Model',
            'serial_number' => 'Serial Number',
            'status_unit' => 'Status Unit'
        ];
        
        // Export using ExportService
        return $this->exportService->exportToExcel($data, $headers, 'Customer Management', [
            'filename' => 'Customer_Management_' . date('Y-m-d_H-i-s') . '.xlsx',
            'author' => 'OPTIMA Marketing',
            'subject' => 'Customer Master Data Report (Detailed)'
        ]);
    }

    // Test method for debugging template system

    // Legacy route support (unit-tersedia) jika masih dipakai
    public function unitTersedia()
    {
        return $this->availableUnits();
    }

    // Proxy detail (optional) agar marketing bisa akses tanpa prefix inventory
    public function unitDetail($id)
    {
        // Check permission: Marketing perlu akses ke warehouse inventory (cross-division)
        if (!$this->canAccess('warehouse') && !$this->canViewResource('warehouse', 'inventory')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak: Anda tidak memiliki izin'
            ])->setStatusCode(403);
        }
        
        try {
            $id = (int)$id;
            if ($id <= 0) return $this->response->setJSON(['success'=>false,'message'=>'ID tidak valid']);
            
            $db = \Config\Database::connect();
            
            // Get main unit data
            $sql = 'SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    iu.serial_number as serial_number_po,
                    iu.status_unit_id as status_unit,
                    COALESCE(mu.merk_unit, "Unknown") as merk_unit,
                    iu.lokasi_unit,
                    iu.status_unit_id as status_unit_raw,
                    iu.keterangan,
                    iu.departemen_id as jenis_unit,
                    iu.model_unit_id,
                    iu.tipe_unit_id,
                    iu.tahun_unit as tahun_po,
                    iu.kapasitas_unit_id as kapasitas_id,
                    iu.model_mast_id as mast_id,
                    iu.sn_mast as sn_mast_po,
                    iu.model_mesin_id as mesin_id,
                    iu.sn_mesin as sn_mesin_po,
                    iu.ban_id,
                    iu.roda_id,
                    iu.valve_id,
                    ku.kontrak_id,
                    iu.aksesoris,
                    COALESCE(mu.model_unit, "Unknown") as model_unit,
                    COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "Unknown") as nama_tipe_unit,
                    COALESCE(su.status_unit, "Unknown") as status_unit_name,
                    COALESCE(d.nama_departemen, "Unknown") as nama_departemen,
                    COALESCE(k.kapasitas_unit, 0) as kapasitas_unit,
                    COALESCE(mu_mast.model_unit, "Unknown") as model_mast,
                    COALESCE(mu_mesin.model_unit, "Unknown") as model_mesin,
                    COALESCE(ban.tipe_ban, "Unknown") as jenis_ban,
                    COALESCE(roda.tipe_roda, "Unknown") as jenis_roda,
                    COALESCE(valve.jumlah_valve, "Unknown") as jenis_valve
                FROM inventory_unit iu
                -- Updated: Get kontrak_id from junction table (source of truth)
                LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0
                LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
                LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
                LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
                LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
                LEFT JOIN kapasitas k ON iu.kapasitas_unit_id = k.id_kapasitas
                LEFT JOIN model_unit mu_mast ON iu.model_mast_id = mu_mast.id_model_unit
                LEFT JOIN model_unit mu_mesin ON iu.model_mesin_id = mu_mesin.id_model_unit
                LEFT JOIN tipe_ban ban ON iu.ban_id = ban.id_ban
                LEFT JOIN jenis_roda roda ON iu.roda_id = roda.id_roda
                LEFT JOIN valve ON iu.valve_id = valve.id_valve
                WHERE iu.id_inventory_unit = ?';
            
            $result = $db->query($sql, [$id]);
            $unit = $result->getRowArray();
            
            if (!$unit) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan']);
            }
            
            // Get attachment data (attachment, battery, charger)
            $attachmentSql = 'SELECT 
                    "attachment" as tipe_item,
                    ia.attachment_type_id as attachment_id,
                    ia.serial_number as sn_attachment,
                    NULL as baterai_id,
                    NULL as sn_baterai,
                    NULL as charger_id,
                    NULL as sn_charger,
                    ia.physical_condition as kondisi_fisik,
                    ia.completeness as kelengkapan,
                    ia.notes as catatan_fisik,
                    ia.storage_location as lokasi_penyimpanan,
                    COALESCE(att.tipe, "") as attachment_name,
                    COALESCE(att.merk, "") as attachment_merk,
                    "" as baterai_name,
                    "" as baterai_merk,
                    "" as charger_name,
                    "" as charger_merk
                FROM inventory_attachments ia
                LEFT JOIN attachment att ON ia.attachment_type_id = att.id_attachment
                WHERE ia.inventory_unit_id = ?
                UNION ALL
                SELECT 
                    "battery" as tipe_item,
                    NULL as attachment_id,
                    NULL as sn_attachment,
                    ib.battery_type_id as baterai_id,
                    ib.serial_number as sn_baterai,
                    NULL as charger_id,
                    NULL as sn_charger,
                    ib.physical_condition as kondisi_fisik,
                    NULL as kelengkapan,
                    ib.notes as catatan_fisik,
                    ib.storage_location as lokasi_penyimpanan,
                    "" as attachment_name,
                    "" as attachment_merk,
                    COALESCE(bat.jenis_baterai, "") as baterai_name,
                    COALESCE(bat.merk_baterai, "") as baterai_merk,
                    "" as charger_name,
                    "" as charger_merk
                FROM inventory_batteries ib
                LEFT JOIN baterai bat ON ib.battery_type_id = bat.id
                WHERE ib.inventory_unit_id = ?
                UNION ALL
                SELECT 
                    "charger" as tipe_item,
                    NULL as attachment_id,
                    NULL as sn_attachment,
                    NULL as baterai_id,
                    NULL as sn_baterai,
                    ic.charger_type_id as charger_id,
                    ic.serial_number as sn_charger,
                    ic.physical_condition as kondisi_fisik,
                    NULL as kelengkapan,
                    ic.notes as catatan_fisik,
                    ic.storage_location as lokasi_penyimpanan,
                    "" as attachment_name,
                    "" as attachment_merk,
                    "" as baterai_name,
                    "" as baterai_merk,
                    COALESCE(chr.tipe_charger, "") as charger_name,
                    COALESCE(chr.merk_charger, "") as charger_merk
                FROM inventory_chargers ic
                LEFT JOIN charger chr ON ic.charger_type_id = chr.id_charger
                WHERE ic.inventory_unit_id = ?
                ORDER BY tipe_item';
                
            $attachmentResult = $db->query($attachmentSql, [$id, $id, $id]);
            $attachments = $attachmentResult->getResultArray();
            
            // Organize attachments by type
            $unit['attachments'] = [];
            $unit['batteries'] = [];
            $unit['chargers'] = [];
            
            foreach ($attachments as $att) {
                switch ($att['tipe_item']) {
                    case 'attachment':
                        $unit['attachments'][] = [
                            'name' => $att['attachment_name'],
                            'merk' => $att['attachment_merk'],
                            'serial_number' => $att['sn_attachment'],
                            'kondisi_fisik' => $att['kondisi_fisik'],
                            'kelengkapan' => $att['kelengkapan'],
                            'catatan_fisik' => $att['catatan_fisik'],
                            'lokasi_penyimpanan' => $att['lokasi_penyimpanan']
                        ];
                        break;
                    case 'battery':
                        $unit['batteries'][] = [
                            'name' => $att['baterai_name'],
                            'merk' => $att['baterai_merk'],
                            'serial_number' => $att['sn_baterai'],
                            'kondisi_fisik' => $att['kondisi_fisik'],
                            'kelengkapan' => $att['kelengkapan'],
                            'catatan_fisik' => $att['catatan_fisik'],
                            'lokasi_penyimpanan' => $att['lokasi_penyimpanan']
                        ];
                        break;
                    case 'charger':
                        $unit['chargers'][] = [
                            'name' => $att['charger_name'],
                            'merk' => $att['charger_merk'],
                            'serial_number' => $att['sn_charger'],
                            'kondisi_fisik' => $att['kondisi_fisik'],
                            'kelengkapan' => $att['kelengkapan'],
                            'catatan_fisik' => $att['catatan_fisik'],
                            'lokasi_penyimpanan' => $att['lokasi_penyimpanan']
                        ];
                        break;
                }
            }
            
            return $this->response->setJSON(['success' => true, 'data' => $unit]);
            
        } catch (\Exception $e) {
            log_message('error', 'Marketing::unitDetail Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.']);
        }
    }

    // Placeholder views for Penawaran (Quotations), Booking, and SPK as requested
    /**
     * Main quotations page (replaces old penawaran)
     */
    public function quotations()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        // Load simple_rbac helper
        helper('simple_rbac');
        
        // Check if this is a create request
        $mode = $this->request->getGet('mode');
        
        // Get quotation statistics
        $stats = $this->getQuotationStatsData();
        
        $data = [
            'title' => 'Quotations Management',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/marketing/quotations' => 'Quotations'
            ],
            'can_access_marketing' => $this->canAccess('marketing'),
            'can_create_marketing' => $this->canManage('marketing'),
            'can_export_marketing' => $this->canExport('marketing'),
            'quotation_stats' => $stats,
            'mode' => $mode ?? 'index'
        ];
        
        // If create mode, add additional data
        if ($mode === 'create') {
            $data['quotation_number'] = $this->generateQuotationNumber();
            $data['customers'] = $this->getCustomersForDropdown();
            $data['users'] = $this->getActiveUsersForDropdown();
        }
        
        return view('marketing/quotations', $data);
    }

    /**
     * Get quotation statistics for dashboard
     */
    private function getQuotationStatsData()
    {
        $total = $this->quotationModel->countAll();
        $accepted = $this->quotationModel->where('stage', 'ACCEPTED')->countAllResults(false);
        $pending = $this->quotationModel->where('stage', 'SENT')->countAllResults(false);
        $rejected = $this->quotationModel->where('stage', 'REJECTED')->countAllResults(false);
        $deals = $this->quotationModel->where('is_deal', 1)->countAllResults(false);
        
        return [
            'total' => $total,
            'accepted' => $accepted,
            'pending' => $pending,
            'rejected' => $rejected,
            'deals' => $deals,
            'conversion_rate' => $total > 0 ? round(($deals / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get quotations data for DataTable
     */
    public function getQuotationsData()
    {
        try {
            if (!$this->hasPermission('marketing.access')) {
                return $this->response->setJSON(['draw'=>1,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[]])->setStatusCode(403);
            }
            
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403);
            }

            $draw = $this->request->getPost('draw') ?: 1;
            $start = $this->request->getPost('start') ?: 0;
            $length = $this->request->getPost('length') ?: 10;
            
            // Safe array access
            $search = $this->request->getPost('search') ?: [];
            $searchValue = isset($search['value']) ? $search['value'] : '';
            
            // Get date range filter
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
            
            // Get total records
            $totalRecords = $this->quotationModel->countAll();
            
            $builder = $this->quotationModel->builder();
            
            // Apply date range filter using trait
            $this->applyDateFilter($builder, 'quotation_date');
            
            // Apply search filter
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('quotation_number', $searchValue)
                    ->orLike('prospect_name', $searchValue)
                    ->orLike('quotation_title', $searchValue)
                    ->orLike('workflow_stage', $searchValue)
                    ->groupEnd();
            }
            
            // Get filtered records count
            $filteredRecords = !empty($searchValue) ? $builder->countAllResults(false) : $totalRecords;
            
            // Get data with pagination
            $quotations = $builder->orderBy('created_at', 'DESC')
                ->limit($length, $start)
                ->get()->getResultArray();
            
            $data = [];
            $index = 0;
            foreach ($quotations as $quotation) {
                $stageBadge = $this->getWorkflowStageBadge($quotation['workflow_stage']);
                $actions = $this->getQuotationActions($quotation['id_quotation'], $quotation['workflow_stage']);
                $validityMeta = $this->getQuotationValidityMeta($quotation);
                
                $data[] = [
                    'DT_RowId' => 'row_' . $quotation['id_quotation'],
                    'DT_RowIndex' => $start + $index + 1,
                    'id_quotation' => $quotation['id_quotation'], // Add this for row click functionality
                    'quotation_number' => $quotation['quotation_number'],
                    'prospect_name' => $quotation['prospect_name'],
                    'quotation_title' => $quotation['quotation_title'] ?? '-',
                    'quotation_date' => date('d/m/Y', strtotime($quotation['quotation_date'])),
                    'total_amount' => 'Rp ' . number_format($quotation['total_amount'], 0, ',', '.'),
                    'workflow_stage' => $stageBadge,
                    'validity_badge' => $validityMeta['validity_badge'],
                    'validity_state' => $validityMeta['state'],
                    'is_valid_until_expired' => $validityMeta['is_expired'] ? 1 : 0,
                    'actions' => $actions
                ];
                $index++;
            }
            
            $response = [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', 'Marketing::getQuotationsData - Error: ' . $e->getMessage());
            log_message('error', 'Marketing::getQuotationsData - File: ' . $e->getFile() . ':' . $e->getLine());
            
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Database error occurred'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get workflow stage badge HTML
     */
    private function getWorkflowStageBadge($stage)
    {
        $badges = [
            'PROSPECT' => '<span class="badge bg-info">Prospect</span>',
            'QUOTATION' => '<span class="badge bg-warning">Quotation</span>',
            'SENT' => '<span class="badge bg-primary">Sent</span>',
            'DEAL' => '<span class="badge bg-success">Deal</span>',
            'NOT_DEAL' => '<span class="badge bg-danger">Not Deal</span>'
        ];
        
        return $badges[$stage] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Normalisasi tanggal valid_until (Y-m-d) untuk perbandingan.
     */
    private function normalizeQuotationValidUntilDate(?string $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        $s = trim((string) $raw);
        if ($s === '') {
            return null;
        }
        if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $s, $m)) {
            return $m[1];
        }
        $ts = strtotime($s);

        return $ts ? date('Y-m-d', $ts) : null;
    }

    /**
     * @return array{state:string,is_expired:bool,validity_badge:string}
     */
    private function getQuotationValidityMeta(array $quotation): array
    {
        $until = $this->normalizeQuotationValidUntilDate($quotation['valid_until'] ?? null);
        if ($until === null) {
            return [
                'state'          => 'no_date',
                'is_expired'     => false,
                'validity_badge' => '<span class="badge bg-secondary">' . lang('Marketing.quotation_validity_no_date') . '</span>',
            ];
        }
        $today = function_exists('date_jakarta') ? date_jakarta('Y-m-d') : date('Y-m-d');
        if ($until < $today) {
            return [
                'state'          => 'expired',
                'is_expired'     => true,
                'validity_badge' => '<span class="badge bg-danger">' . lang('Marketing.quotation_validity_expired') . '</span>',
            ];
        }
        if ($until > $today) {
            return [
                'state'          => 'valid',
                'is_expired'     => false,
                'validity_badge' => '<span class="badge bg-success">' . lang('Marketing.quotation_validity_valid') . '</span>',
            ];
        }

        return [
            'state'          => 'expires_today',
            'is_expired'     => false,
            'validity_badge' => '<span class="badge bg-warning text-dark">' . lang('Marketing.quotation_validity_expires_today') . '</span>',
        ];
    }

    /**
     * True jika valid_until sudah lewat (strictly before today).
     */
    private function isQuotationValidUntilExpired(?string $raw): bool
    {
        $until = $this->normalizeQuotationValidUntilDate($raw);
        if ($until === null) {
            return false;
        }
        $today = function_exists('date_jakarta') ? date_jakarta('Y-m-d') : date('Y-m-d');

        return $until < $today;
    }

    /**
     * Get action buttons for quotation based on workflow stage
     */
    private function getQuotationActions($quotationId, $workflowStage = 'PROSPECT')
    {
        $actions = '<div class="btn-group" role="group">';
        
        // Check if quotation has specifications (required for SEND action)
        $hasSpecs = $this->quotationSpecificationModel->where('id_quotation', $quotationId)->countAllResults() > 0;
        
        // Check if customer was created (for DEAL stage actions)
        $quotation = $this->quotationModel->find($quotationId);
        $hasCustomer = !empty($quotation['created_customer_id']);
        
        // Workflow actions based on current stage
        switch ($workflowStage) {
            case 'PROSPECT':
                $actions .= '<button class="btn btn-sm btn-success" onclick="convertToQuotation(' . $quotationId . ')" title="Convert to Quotation">';
                $actions .= '<i class="fas fa-arrow-right me-1"></i>Create Quotation';
                $actions .= '</button>';
                break;
                
            case 'QUOTATION':
                $actions .= '<button class="btn btn-sm btn-warning me-1" onclick="addSpecifications(' . $quotationId . ')" title="Add/Edit Specifications">';
                $actions .= '<i class="fas fa-list me-1"></i>Add Specs';
                $actions .= '</button>';
                
                if ($hasSpecs) {
                    $actions .= '<button class="btn btn-sm btn-secondary me-1" onclick="openPrintSpecModal(' . $quotationId . ')" title="Print with Spec Selection">';
                    $actions .= '<i class="fas fa-print me-1"></i>Print';
                    $actions .= '</button>';
                    
                    $actions .= '<button class="btn btn-sm btn-info" onclick="sendQuotation(' . $quotationId . ')" title="Send Quotation">';
                    $actions .= '<i class="fas fa-paper-plane me-1"></i>Send';
                    $actions .= '</button>';
                } else {
                    $actions .= '<button class="btn btn-sm btn-secondary" disabled title="Add specifications before sending">';
                    $actions .= '<i class="fas fa-paper-plane me-1"></i>Send (Add Specs First)';
                    $actions .= '</button>';
                }
                break;
                
            case 'SENT':
                $actions .= '<button class="btn btn-sm btn-secondary me-1" onclick="openPrintSpecModal(' . $quotationId . ')" title="Print with Spec Selection">';
                $actions .= '<i class="fas fa-print me-1"></i>Print';
                $actions .= '</button>';
                              
                $actions .= '<button class="btn btn-sm btn-success me-1" onclick="markAsDeal(' . $quotationId . ')" title="Mark as Deal">';
                $actions .= '<i class="fas fa-handshake me-1"></i>Deal';
                $actions .= '</button>';
                $actions .= '<button class="btn btn-sm btn-danger" onclick="markAsNotDeal(' . $quotationId . ')" title="Mark as Not Deal">';
                $actions .= '<i class="fas fa-times-circle me-1"></i>No Deal';
                $actions .= '</button>';
                break;
                
            case 'DEAL':
                // DEAL stage flow:
                // 1) No customer yet => show manual customer fallback
                // 2) Customer exists => Create Contract + Create SPK
                // 3) SPK created => show completion badge
                $spkCreated = !empty($quotation['spk_created']);

                if (!$hasCustomer) {
                    $actions .= '<button class="btn btn-sm btn-warning" onclick="createCustomerFromDeal(' . $quotationId . ')" title="Fallback: create customer manually if automatic creation on Deal did not succeed">';
                    $actions .= '<i class="fas fa-user-plus me-1"></i>Add Customer';
                    $actions .= '</button>';
                } elseif (!$spkCreated) {
                    $actions .= '<button class="btn btn-sm btn-info me-1" onclick="completeCustomerContract(' . $quotationId . ')" title="Create or link customer contract">';
                    $actions .= '<i class="fas fa-file-contract me-1"></i>Create Contract';
                    $actions .= '</button>';

                    $actions .= '<button class="btn btn-sm btn-success" onclick="createSPKFromQuotation(' . $quotationId . ')" title="Create SPK from quotation">';
                    $actions .= '<i class="fas fa-clipboard-check me-1"></i>Create SPK';
                    $actions .= '</button>';
                } else {
                    $actions .= '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>SPK Created</span>';
                }
                break;
                
            case 'NOT_DEAL':
                // Show read-only No Deal status
                $actions .= '<span class="badge bg-danger">No Deal</span>';
                break;
        }
        
        $actions .= '</div>';
        return $actions;
    }

    public function booking()
    {
        // Check permission for viewing booking
        if (!$this->hasPermission('marketing.booking.view')) {
            return redirect()->to('/')->with('error', 'Akses ditolak: Anda tidak memiliki izin');
        }
        
        return view('marketing/booking');
    }

    /**
     * Create new quotation
     */
    public function createQuotation()
    {
        $canCreate = $this->hasPermission('marketing.quotation.create')
            || $this->hasPermission('marketing.kontrak.create')
            || $this->hasPermission('marketing.contract.create')
            || $this->canManage('marketing');
        if (!$canCreate) {
            return redirect()->to('/marketing/quotations')->with('error', 'Akses ditolak');
        }
        
        if ($this->request->getMethod() === 'POST') {
            return $this->storeQuotation();
        }
        
        // Get dropdown data
        $data = [
            'title' => 'Create New Quotation',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/marketing/quotations' => 'Quotations',
                '/marketing/quotation/create' => 'Create Quotation'
            ]
        ];
        
        return view('marketing/quotation_create', $data);
    }

    /**
     * Store new quotation
     */
    public function storeQuotation()
    {
        $canCreate = $this->hasPermission('marketing.quotation.create')
            || $this->hasPermission('marketing.kontrak.create')
            || $this->hasPermission('marketing.contract.create')
            || $this->canManage('marketing');
        if (!$canCreate) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'prospect_name' => 'required|max_length[255]',
            'quotation_title' => 'required|max_length[255]',
            'quotation_date' => 'required|valid_date',
            'valid_until' => 'required|valid_date',
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa kembali data yang diisi.',
                    'errors' => $validation->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Validate session user_id — quotations.created_by is NOT NULL FK → users.id
            $creatorId = (int)session()->get('user_id');
            if ($creatorId <= 0 || !$db->table('users')->where('id', $creatorId)->countAllResults()) {
                log_message('error', "[Marketing] storeQuotation rejected: user_id={$creatorId} not found in users table");
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Session tidak valid. Silakan login ulang.']);
            }

            // Generate quotation number
            $quotationNumber = $this->generateQuotationNumber();
            
            // Prepare quotation data
            $quotationData = [
                'quotation_number' => $quotationNumber,
                'prospect_name' => $this->request->getPost('prospect_name'),
                'prospect_contact_person' => $this->request->getPost('prospect_contact_person'),
                'prospect_phone' => $this->request->getPost('prospect_phone'),
                'prospect_email' => $this->request->getPost('prospect_email'),
                'prospect_address' => $this->request->getPost('prospect_address'),
                'prospect_city' => $this->request->getPost('prospect_city'),
                'quotation_title' => $this->request->getPost('quotation_title'),
                'quotation_description' => $this->request->getPost('quotation_description'),
                'quotation_date' => $this->request->getPost('quotation_date'),
                'valid_until' => $this->request->getPost('valid_until'),
                'currency' => $this->request->getPost('currency') ?: 'IDR',
                'payment_terms' => $this->request->getPost('payment_terms'),
                'delivery_terms' => $this->request->getPost('delivery_terms'),
                'warranty_terms' => $this->request->getPost('warranty_terms'),
                'stage' => 'DRAFT',
                'probability_percent' => $this->request->getPost('probability_percent') ?: 50,
                'created_by' => $creatorId,
                'assigned_to' => $this->request->getPost('assigned_to') ?: $creatorId
            ];
            
            $quotationId = $this->quotationModel->insert($quotationData);
            
            if (!$quotationId) {
                throw new \Exception('Failed to create quotation');
            }
            
            // Handle specifications if provided
            $specifications = $this->request->getPost('specifications');
            if ($specifications && is_array($specifications)) {
                foreach ($specifications as $spec) {
                    $specData = [
                        'id_quotation' => $quotationId,
                        'item_name' => $spec['item_name'],
                        'description' => $spec['description'] ?? '',
                        'category' => $spec['category'] ?? 'General',
                        'quantity' => $spec['quantity'],
                        'unit' => $spec['unit'],
                        'unit_price' => $spec['unit_price'],
                        'total_price' => $spec['quantity'] * $spec['unit_price'],
                        'brand' => $spec['brand'] ?? '',
                        'model_number' => $spec['model_number'] ?? '',
                        'specifications' => $spec['specifications'] ?? '',
                        'notes' => $spec['notes'] ?? '',
                        'is_active' => 1
                    ];
                    
                    $this->quotationSpecificationModel->insert($specData);
                }
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            // Send notification - quotation created
            if (function_exists('notify_quotation_created')) {
                notify_quotation_created([
                    'id' => $quotationId,
                    'quotation_number' => $quotationNumber,
                    'customer_name' => $quotationData['prospect_name'],
                    'total_value' => 0, // Will be calculated from specifications
                    'stage' => 'DRAFT',
                    'created_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/marketing/quotations/view/' . $quotationId)
                ]);
            }
            
            // Log activity
            $this->logActivity('CREATE', 'quotation', $quotationId, 'Created quotation: ' . $quotationNumber);
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Quotation created successfully',
                    'id_quotation' => $quotationId,
                    'quotation_number' => $quotationNumber
                ]);
            }
            
            return redirect()->to('/marketing/quotation/view/' . $quotationId)
                ->with('success', 'Quotation created successfully');
                
        } catch (\Exception $e) {
            $db->transRollback();
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
                ]);
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memproses permintaan. Silakan coba lagi.');
        }
    }
    
    /**
     * Create new prospect (first stage of quotation workflow)
     */
    public function createProspect()
    {
        $canCreate = $this->hasPermission('marketing.quotation.create')
            || $this->hasPermission('marketing.kontrak.create')
            || $this->canManage('marketing');
        if (!$canCreate) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }
        
        // Check if linking to existing customer
        $existingCustomerId = $this->request->getPost('existing_customer_id');
        
        // Dynamic validation rules based on customer type
        $validationRules = [
            'quotation_title' => 'required|max_length[255]',
            'valid_until' => 'required|valid_date'
        ];
        
        // Only require prospect fields if NOT linking to existing customer
        if (empty($existingCustomerId)) {
            $validationRules['prospect_name'] = 'required|max_length[255]';
            $validationRules['prospect_contact_person'] = 'required|max_length[255]';
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules($validationRules);
        
        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang diisi.',
                'errors' => $validation->getErrors()
            ]);
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Generate quotation number for prospect
            $quotationNumber = $this->generateQuotationNumber();
            
            // Check if linking to existing customer
            $existingCustomerId = $this->request->getPost('existing_customer_id');
            $linkedCustomerData = null;
            
            if (!empty($existingCustomerId)) {
                // Get existing customer data
                $customerModel = new \App\Models\CustomerModel();
                $customerLocationModel = new \App\Models\CustomerLocationModel();
                
                $customer = $customerModel->find($existingCustomerId);
                if ($customer) {
                    // Get primary location
                    $primaryLocation = $customerLocationModel->where([
                        'customer_id' => $existingCustomerId,
                        'is_primary' => 1
                    ])->first();
                    
                    if ($primaryLocation) {
                        $linkedCustomerData = [
                            'customer_id' => $customer['id'],
                            'customer_name' => $customer['customer_name'],
                            'location' => $primaryLocation
                        ];
                    }
                }
            }
            
            // Create prospect record with smart data handling
            $prospectData = [
                'quotation_number' => $quotationNumber,
                'prospect_name' => $linkedCustomerData ? $linkedCustomerData['customer_name'] : $this->request->getPost('prospect_name'),
                'prospect_contact_person' => $linkedCustomerData ? ($linkedCustomerData['location']['contact_person'] ?? $this->request->getPost('prospect_contact_person')) : $this->request->getPost('prospect_contact_person'),
                'prospect_email' => $linkedCustomerData ? ($linkedCustomerData['location']['email'] ?? $this->request->getPost('prospect_email')) : $this->request->getPost('prospect_email'),
                'prospect_phone' => $linkedCustomerData ? ($linkedCustomerData['location']['phone'] ?? $this->request->getPost('prospect_phone')) : $this->request->getPost('prospect_phone'),
                'prospect_address' => $linkedCustomerData ? ($linkedCustomerData['location']['address'] ?? $this->request->getPost('prospect_address')) : $this->request->getPost('prospect_address'),
                'prospect_city' => $linkedCustomerData ? ($linkedCustomerData['location']['city'] ?? $this->request->getPost('prospect_city')) : $this->request->getPost('prospect_city'),
                'prospect_province' => $linkedCustomerData ? ($linkedCustomerData['location']['province'] ?? $this->request->getPost('prospect_province')) : $this->request->getPost('prospect_province'),
                'quotation_title' => $this->request->getPost('quotation_title'),
                'quotation_description' => $this->request->getPost('quotation_description'),
                'quotation_date' => $this->request->getPost('quotation_date') ?: date('Y-m-d'),
                'valid_until' => $this->request->getPost('valid_until'),
                'stage' => 'DRAFT',
                'workflow_stage' => 'PROSPECT',
                'currency' => 'IDR',
                'created_by' => session('user_id'),
                // Store reference to linked customer (if any)
                'created_customer_id' => $existingCustomerId ? $existingCustomerId : null
            ];
            
            $quotationId = $this->quotationModel->insert($prospectData);
            
            if (!$quotationId) {
                throw new \Exception('Failed to create prospect');
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            // Log activity
            $this->logActivity('PROSPECT_CREATED', 'quotation', $quotationId, 
                'Created new prospect: ' . $this->request->getPost('prospect_name'));
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Prospect created successfully',
                'data' => [
                    'id_quotation' => $quotationId,
                    'quotation_number' => $quotationNumber,
                    'workflow_stage' => 'PROSPECT'
                ]
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Gagal membuat data. Silakan coba lagi.');
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }
    
    /**
     * Convert prospect to quotation (allow adding specifications)
     */
    public function convertToQuotation($quotationId)
    {
        $canAct = $this->hasPermission('marketing.quotation.create')
            || $this->hasPermission('marketing.quotation.edit')
            || $this->canManage('marketing');
        if (!$canAct) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk mengkonversi prospek']);
        }
        
        try {
            $quotation = $this->quotationModel->find($quotationId);
            
            if (!$quotation) {
                return $this->response->setJSON(['success' => false, 'message' => 'Quotation tidak ditemukan']);
            }
            
            if ($quotation['workflow_stage'] !== 'PROSPECT') {
                return $this->response->setJSON(['success' => false, 'message' => 'Only prospects can be converted to quotations']);
            }
            
            $oldSalesStage = (string) ($quotation['stage'] ?? 'PROSPECT');

            // Update workflow stage to quotation
            $this->quotationModel->update($quotationId, [
                'workflow_stage' => 'QUOTATION',
                'stage' => 'DRAFT'
            ]);

            $this->insertQuotationStageHistoryIfExists(
                (int) $quotationId,
                $oldSalesStage,
                'DRAFT',
                'convert_to_quotation',
                'workflow_stage PROSPECT → QUOTATION'
            );

            $after = array_merge($quotation, [
                'workflow_stage' => 'QUOTATION',
                'stage'          => 'DRAFT',
            ]);
            $this->logQuotationWorkflowDocumentHistory(
                (int) $quotationId,
                'WORKFLOW',
                'Prospek diubah menjadi quotation (siap isi spesifikasi).',
                $quotation,
                $after
            );
            
            // Log activity
            $this->logActivity('PROSPECT_TO_QUOTATION', 'quotation', $quotationId, 
                'Converted prospect to quotation: ' . $quotation['quotation_number']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Prospect converted to quotation successfully. Please add specifications before sending.',
                'redirect_to_specs' => true
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * View quotation details
     */
    public function viewQuotation($quotationId)
    {
        if (!$this->hasPermission('marketing.quotation.view')) {
            return redirect()->to('/marketing/quotations')->with('error', 'Akses ditolak');
        }
        
        $quotation = $this->quotationModel->find($quotationId);
        
        if (!$quotation) {
            return redirect()->to('/marketing/quotations')->with('error', 'Quotation tidak ditemukan');
        }
        
        // Get specifications
        $specifications = $this->quotationSpecificationModel
            ->where('id_quotation', $quotationId)
            ->where('is_active', 1)
            ->orderBy('id_specification', 'ASC')
            ->findAll();
        
        $data = [
            'title' => 'Quotation Details - ' . $quotation['quotation_number'],
            'quotation' => $quotation,
            'specifications' => $specifications,
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/marketing/quotations' => 'Quotations',
                '/marketing/quotation/view/' . $quotationId => 'View Quotation'
            ]
        ];
        
        return view('marketing/quotation_view', $data);
    }

    /**
     * Convert quotation to contract
     */
    public function convertToContract($quotationId)
    {
        $canAct = $this->hasPermission('marketing.quotation.edit')
            || $this->hasPermission('marketing.contract.create')
            || $this->hasPermission('marketing.kontrak.create')
            || $this->canManage('marketing');
        if (!$canAct) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk membuat kontrak']);
        }
        
        $quotation = $this->quotationModel->find($quotationId);
        
        if (!$quotation) {
            return $this->response->setJSON(['success' => false, 'message' => 'Quotation tidak ditemukan']);
        }
        
        if ($quotation['stage'] !== 'ACCEPTED') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only accepted quotations can be converted to contracts']);
        }
        
        if ($quotation['created_contract_id']) {
            return $this->response->setJSON(['success' => false, 'message' => 'This quotation has already been converted to a contract']);
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get quotation specifications
            $specifications = $this->quotationSpecificationModel
                ->where('id_quotation', $quotationId)
                ->where('is_active', 1)
                ->findAll();
            
            // Generate contract number
            $contractNumber = $this->generateContractNumberInternal();
            
            // Create contract
            $contractData = [
                'no_kontrak' => $contractNumber,
                'customer_name' => $quotation['prospect_name'],
                'tanggal_mulai' => date('Y-m-d'),
                'tanggal_berakhir' => date('Y-m-d', strtotime('+1 month')),
                'nilai_total' => $quotation['total_amount'],
                'status' => 'ACTIVE',
                'jenis_sewa' => 'BULANAN', // Default, bisa disesuaikan
                'dibuat_oleh' => session('user_id'),
                'dibuat_pada' => date('Y-m-d H:i:s')
            ];
            
            $contractId = $this->kontrakModel->insert($contractData);
            
            if (!$contractId) {
                throw new \Exception('Failed to create contract');
            }
            
            // Create contract specifications
            foreach ($specifications as $spec) {
                $kontrakSpecData = [
                    'kontrak_id' => $contractId,
                    'spek_kode' => $spec['item_name'],
                    'nama_spec' => $spec['item_name'],
                    'qty_spec' => $spec['quantity'],
                    'unit_spec' => $spec['unit'],
                    'keterangan_spec' => $spec['description'],
                    'harga_unit_spec' => $spec['unit_price'],
                    'total_harga_spec' => $spec['total_price'],
                    'brand_spec' => $spec['brand'],
                    'tipe_spec' => $spec['category']
                ];
                
                $this->kontrakSpesifikasiModel->insert($kontrakSpecData);
            }
            
            // Update quotation with contract reference
            $this->quotationModel->update($quotationId, [
                'created_contract_id' => $contractId,
                'is_deal' => 1,
                'deal_date' => date('Y-m-d H:i:s')
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            // Log activity
            $this->logActivity('CONVERT', 'quotation', $quotationId, 'Converted quotation to contract: ' . $contractNumber);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Quotation successfully converted to contract',
                'contract_number' => $contractNumber,
                'contract_id' => $contractId
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Generate quotation number
     */
    private function generateQuotationNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastQuotation = $this->quotationModel
            ->like('quotation_number', 'QUO/' . $year . '/' . $month . '/', 'after')
            ->orderBy('quotation_number', 'DESC')
            ->first();
        
        $sequence = 1;
        if ($lastQuotation) {
            $parts = explode('/', $lastQuotation['quotation_number']);
            if (count($parts) === 4) {
                $sequence = intval($parts[3]) + 1;
            }
        }
        
        return 'QUO/' . $year . '/' . $month . '/' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate contract number (internal use)
     */
    private function generateContractNumberInternal()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastContract = $this->kontrakModel
            ->like('no_kontrak', 'KNTRK/' . $year . '/' . $month . '/', 'after')
            ->orderBy('no_kontrak', 'DESC')
            ->first();
        
        $sequence = 1;
        if ($lastContract) {
            $parts = explode('/', $lastContract['no_kontrak']);
            if (count($parts) === 4) {
                $sequence = intval($parts[3]) + 1;
            }
        }

        return 'KNTRK/' . $year . '/' . $month . '/' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }    /**
     * Get customers for dropdown
     */
    private function getCustomersForDropdown()
    {
        // Use customer table if available
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT id, customer_name as name 
            FROM customers 
            WHERE aktif = 1 
            ORDER BY customer_name ASC
        ");
        
        return $query->getResultArray();
    }

    /**
     * Get active users for dropdown
     */
    private function getActiveUsersForDropdown()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT id, firstname, lastname 
            FROM users 
            WHERE status = 'active' 
            ORDER BY firstname, lastname ASC
        ");
        
        return $query->getResultArray();
    }

    /**
     * Get quotation statistics for API
     */
    public function getQuotationStats()
    {
        // Disable debug bar for AJAX responses
        $this->response->setHeader('X-Requested-With', 'XMLHttpRequest');
        
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Authentication required'
            ])->setStatusCode(401);
        }
        
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        // Log date filter params for debugging
        $params = $this->getDateFilterParams();
        log_message('info', 'QuotationStats - Date filter params: ' . json_encode($params));

        $builder = $this->quotationModel->builder();

        // Apply date filters using trait (supports both GET and POST)
        $this->applyDateFilter($builder, 'quotations.quotation_date');

        // Count total
        $total = $builder->countAllResults(false);

        // Count by stage
        $pending = (clone $builder)->where('quotations.stage', 'SENT')->countAllResults(false);
        $approved = (clone $builder)->where('quotations.stage', 'ACCEPTED')->countAllResults(false);
        $rejected = (clone $builder)->where('quotations.stage', 'REJECTED')->countAllResults(false);
        
        // Calculate total value
        $totalValueBuilder = $this->quotationModel->builder();
        $this->applyDateFilter($totalValueBuilder, 'quotation_date');
        $totalValue = $totalValueBuilder
            ->selectSum('total_amount')
            ->where('stage !=', 'REJECTED')
            ->get()
            ->getRow()
            ->total_amount ?? 0;

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'total' => $total,
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
                'total_value' => $totalValue
            ]);
    }

    /**
     * Get active users for filter
     */
    public function getActiveUsers()
    {
        try {
            if (!$this->hasPermission('marketing.access')) {
                return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
            }
            
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403);
            }

            $users = $this->getActiveUsersForDropdown();

            return $this->response->setJSON([
                'success' => true,
                'data' => $users
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Marketing::getActiveUsers - Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error occurred'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get single quotation data
     */
    public function getQuotation($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        // Get quotation with customer location and contract details
        // First, try to get location from contract, if no contract then get primary location
        $quotation = $this->db->table('quotations q')
            ->select('q.*, 
                c.customer_name, 
                COALESCE((SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1), cl_primary.location_name) as location_name,
                COALESCE((SELECT cl.address FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1), cl_primary.address) as location_address,
                COALESCE((SELECT cl.contact_person FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1), cl_primary.contact_person) as pic_name, 
                COALESCE((SELECT cl.phone FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1), cl_primary.phone) as pic_phone,
                COALESCE((SELECT cl.id FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1), cl_primary.id) as customer_location_id,
                k.no_kontrak as contract_number,
                (SELECT COUNT(*) FROM quotation_specifications WHERE id_quotation = q.id_quotation) as spec_count')
            ->join('customers c', 'c.id = q.created_customer_id', 'left')
            ->join('kontrak k', 'k.id = q.created_contract_id', 'left')
            ->join('customer_locations cl_primary', 'cl_primary.customer_id = q.created_customer_id AND cl_primary.is_primary = 1 AND cl_primary.is_active = 1', 'left')
            ->where('q.id_quotation', $quotationId)
            ->get()
            ->getRowArray();

        if (!$quotation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Quotation tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $quotation
        ]);
    }

    /**
     * Update quotation data
     */
    public function updateQuotation($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        if (!$this->hasPermission('marketing.access')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You do not have permission to update quotations'
            ])->setStatusCode(403);
        }

        try {
            // Get existing quotation to check if it can be edited
            $quotation = $this->quotationModel->find($quotationId);
            if (!$quotation) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Quotation tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Prevent editing if already linked to contract
            if (!empty($quotation['created_contract_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cannot edit quotation that is already linked to a contract'
                ])->setStatusCode(400);
            }

            // Prevent editing for expired or lost deals
            if ($quotation['stage'] === 'EXPIRED') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cannot edit expired quotation. Please create a new one.'
                ])->setStatusCode(400);
            }

            if ($quotation['workflow_stage'] === 'NOT_DEAL') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cannot edit rejected/lost quotation. Please create a new one.'
                ])->setStatusCode(400);
            }

            // Store old values for audit trail
            $oldValues = [
                'total_amount' => $quotation['total_amount'],
                'valid_until' => $quotation['valid_until'],
                'quotation_description' => $quotation['quotation_description'],
                'notes' => $quotation['notes'] ?? null
            ];

            // Get data from request
            $data = [
                'total_amount' => $this->request->getPost('total_amount'),
                'valid_until' => $this->request->getPost('valid_until'),
                'quotation_description' => $this->request->getPost('quotation_description'),
                'notes' => $this->request->getPost('notes'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Validate required fields
            if (empty($data['total_amount']) || empty($data['valid_until']) || empty($data['quotation_description'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please fill all required fields'
                ])->setStatusCode(400);
            }

            // Determine if edit should trigger revision (version increment)
            $currentVersion = $quotation['version'] ?? 1;
            $isRevision = false;
            
            // Stages that trigger REVISION when edited (customer already involved)
            $revisionStages = ['SENT', 'FOLLOW_UP', 'NEGOTIATION', 'ACCEPTED'];
            $revisionWorkflowStages = ['SENT', 'DEAL'];
            
            // Check if quotation is in stage that requires revision tracking
            $stageTriggersRevision = in_array($quotation['stage'], $revisionStages);
            $workflowTriggersRevision = in_array($quotation['workflow_stage'], $revisionWorkflowStages);
            
            // Debug logging
            log_message('info', "Update Quotation #{$quotationId} - Stage: {$quotation['stage']}, Workflow: {$quotation['workflow_stage']}, Version: {$currentVersion}");
            
            // If in any revision-triggering stage, increment version and mark as REVISED
            if ($stageTriggersRevision || $workflowTriggersRevision) {
                $data['revision_status'] = 'REVISED';
                $data['revised_at'] = date('Y-m-d H:i:s');
                $data['revised_by'] = session()->get('user_id');
                $data['version'] = $currentVersion + 1;
                $isRevision = true;
                
                log_message('info', "Quotation #{$quotationId} marked as REVISED - Stage: {$quotation['stage']}, New version: " . ($currentVersion + 1));
            } else {
                log_message('info', "Quotation #{$quotationId} normal update (no revision) - Stage: {$quotation['stage']}, Version stays: {$currentVersion}");
            }

            // Update quotation
            $this->quotationModel->update($quotationId, $data);

            // Log the change to history
            $changesSummary = $this->buildChangesSummary($oldValues, $data);
            $this->logQuotationChange(
                $quotationId,
                $data['version'] ?? $currentVersion,
                $isRevision ? 'REVISED' : 'UPDATED',
                $changesSummary,
                $oldValues,
                $data
            );

            $message = $isRevision 
                ? 'Quotation updated and marked as REVISED (version ' . ($currentVersion + 1) . ')'
                : 'Quotation updated successfully';

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message,
                'is_revision' => $isRevision,
                'version' => $data['version'] ?? $currentVersion
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::updateQuotation - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Helper method to build human-readable changes summary
     */
    private function buildChangesSummary($oldValues, $newValues)
    {
        $changes = [];
        
        if ($oldValues['total_amount'] != $newValues['total_amount']) {
            $changes[] = sprintf(
                'Total amount changed from Rp %s to Rp %s',
                number_format($oldValues['total_amount'], 0, ',', '.'),
                number_format($newValues['total_amount'], 0, ',', '.')
            );
        }
        
        if ($oldValues['valid_until'] != $newValues['valid_until']) {
            $changes[] = sprintf(
                'Valid until changed from %s to %s',
                $oldValues['valid_until'],
                $newValues['valid_until']
            );
        }
        
        if ($oldValues['quotation_description'] != $newValues['quotation_description']) {
            $changes[] = 'Description updated';
        }
        
        return empty($changes) ? 'Minor updates' : implode('; ', $changes);
    }

    /**
     * Log quotation changes to history table
     */
    private function logQuotationChange($quotationId, $version, $actionType, $changesSummary, $oldValues, $newValues)
    {
        try {
            $db = \Config\Database::connect();

            if (! $db->tableExists('quotation_history')) {
                log_message('debug', 'logQuotationChange skipped: quotation_history table missing');

                return false;
            }
            
            $userId = session()->get('user_id');
            
            // Force write to log file regardless of CI4 settings
            $logMessage = date('Y-m-d H:i:s') . " - logQuotationChange called - QuotationID: $quotationId, Version: $version, Action: $actionType, UserID: $userId\n";
            file_put_contents(WRITEPATH . 'logs/quotation_debug.log', $logMessage, FILE_APPEND | LOCK_EX);
            
            // Debug logging through CI4
            log_message('info', 'logQuotationChange called - QuotationID: ' . $quotationId . ', Version: ' . $version . ', Action: ' . $actionType . ', UserID: ' . $userId);
            
            $data = [
                'quotation_id' => $quotationId,
                'version' => $version,
                'action_type' => $actionType,
                'changed_by' => $userId ?: 1, // Fallback to user ID 1 if session empty
                'changed_at' => date('Y-m-d H:i:s'),
                'changes_summary' => $changesSummary,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($newValues),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString()
            ];
            
            $result = $db->table('quotation_history')->insert($data);
            
            if ($result) {
                $insertId = $db->insertID();
                log_message('info', 'History logged successfully - Insert ID: ' . $insertId);
                file_put_contents(WRITEPATH . 'logs/quotation_debug.log', "SUCCESS: History logged - Insert ID: $insertId\n", FILE_APPEND | LOCK_EX);
            } else {
                log_message('error', 'History insert returned false');
                file_put_contents(WRITEPATH . 'logs/quotation_debug.log', "ERROR: History insert returned false\n", FILE_APPEND | LOCK_EX);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            log_message('error', 'Gagal memproses permintaan. Silakan coba lagi.');
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Force write error to custom log
            $errorMessage = date('Y-m-d H:i:s') . " - ERROR in logQuotationChange: " . $e->getMessage() . "\nStack: " . $e->getTraceAsString() . "\n\n";
            file_put_contents(WRITEPATH . 'logs/quotation_debug.log', $errorMessage, FILE_APPEND | LOCK_EX);
            
            return false;
        }
    }

    /**
     * Delete quotation
     */
    public function deleteQuotation($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        if (!$this->hasPermission('marketing.access')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You do not have permission to delete quotations'
            ])->setStatusCode(403);
        }

        try {
            // Get existing quotation to check if it can be deleted
            $quotation = $this->quotationModel->find($quotationId);
            if (!$quotation) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Quotation tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Prevent deletion if already linked to contract
            if (!empty($quotation['created_contract_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cannot delete quotation that is already linked to a contract'
                ])->setStatusCode(400);
            }

            // Delete specifications first
            $this->db->table('quotation_specifications')
                ->where('id_quotation', $quotationId)
                ->delete();

            // Log deletion before actually deleting
            $this->logQuotationChange(
                $quotationId,
                $quotation['version'] ?? 1,
                'DELETED',
                'Quotation deleted by user',
                [
                    'quotation_number' => $quotation['quotation_number'],
                    'total_amount' => $quotation['total_amount'],
                    'workflow_stage' => $quotation['workflow_stage']
                ],
                null
            );

            // Delete quotation
            $this->quotationModel->delete($quotationId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Quotation deleted successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::deleteQuotation - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get quotation history/audit trail
     */
    public function getQuotationHistory($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $history = $this->fetchQuotationDocumentHistoryRows((int) $quotationId);

            return $this->response->setJSON([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Marketing::getQuotationHistory - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Riwayat perubahan stage penjualan (DRAFT → SENT → NEGOTIATION, …) dari quotation_stage_history.
     */
    public function getQuotationStageHistory($quotationId)
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $db = \Config\Database::connect();
            if (! $db->tableExists('quotation_stage_history')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data'    => [],
                    'meta'    => ['table_ready' => false],
                ]);
            }

            $quotationIdCol = $db->fieldExists('id_quotation', 'quotation_stage_history') ? 'id_quotation' : 'quotation_id';
            $changedByCol = null;
            foreach (['changed_by', 'updated_by', 'created_by', 'user_id'] as $c) {
                if ($db->fieldExists($c, 'quotation_stage_history')) {
                    $changedByCol = $c;
                    break;
                }
            }
            $changedAtCol = null;
            foreach (['changed_at', 'created_at', 'updated_at'] as $c) {
                if ($db->fieldExists($c, 'quotation_stage_history')) {
                    $changedAtCol = $c;
                    break;
                }
            }

            $builder = $db->table('quotation_stage_history s');
            $builder->select('s.*');
            if ($changedByCol !== null) {
                $builder->select('u.first_name, u.last_name, u.username');
                $builder->join('users u', 'u.id = s.' . $changedByCol, 'left');
            }
            $builder->where('s.' . $quotationIdCol, (int) $quotationId);
            $builder->orderBy('s.' . ($changedAtCol ?? 'id'), 'DESC');
            $rows = $builder->get()->getResultArray();

            foreach ($rows as &$r) {
                $fn                        = trim(((string) ($r['first_name'] ?? '')) . ' ' . ((string) ($r['last_name'] ?? '')));
                $r['changed_by_display'] = $fn !== '' ? $fn : ($r['username'] ?? '-');
                if ($changedAtCol !== null && isset($r[$changedAtCol]) && ! isset($r['changed_at'])) {
                    $r['changed_at'] = $r[$changedAtCol];
                }
                // UI expects "stage" as destination; some DBs use new_stage / stage_to.
                if (empty($r['stage'])) {
                    foreach (['new_stage', 'to_stage', 'stage_to'] as $alt) {
                        if (! empty($r[$alt])) {
                            $r['stage'] = $r[$alt];
                            break;
                        }
                    }
                }
            }
            unset($r);

            return $this->response->setJSON([
                'success' => true,
                'data'    => $rows,
                'meta'    => ['table_ready' => true],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Marketing::getQuotationStageHistory - ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ])->setStatusCode(500);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchQuotationDocumentHistoryRows(int $quotationId): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('quotation_history')) {
            return [];
        }

        $rows = [];
        try {
            if ($db->tableExists('vw_quotation_history_detail')) {
                $rows = $db->table('vw_quotation_history_detail')
                    ->where('quotation_id', $quotationId)
                    ->orderBy('changed_at', 'DESC')
                    ->get()
                    ->getResultArray();
            }
        } catch (\Throwable $e) {
            log_message('debug', 'vw_quotation_history_detail query failed, using base table: ' . $e->getMessage());
            $rows = [];
        }

        if ($rows !== []) {
            return $this->normalizeQuotationDocumentHistoryRows($rows);
        }

        $builder = $db->table('quotation_history h');
        $builder->select('h.*, u.first_name, u.last_name, u.username');
        $builder->join('users u', 'u.id = h.changed_by', 'left');
        $builder->where('h.quotation_id', $quotationId);
        $builder->orderBy('h.changed_at', 'DESC');
        $rows = $builder->get()->getResultArray();

        return $this->normalizeQuotationDocumentHistoryRows($rows);
    }

    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return list<array<string, mixed>>
     */
    private function normalizeQuotationDocumentHistoryRows(array $rows): array
    {
        foreach ($rows as &$row) {
            if (empty($row['changed_by_name'])) {
                $fn = trim(((string) ($row['first_name'] ?? '')) . ' ' . ((string) ($row['last_name'] ?? '')));
                $row['changed_by_name'] = $fn !== '' ? $fn : ($row['username'] ?? '-');
            }
            if (empty($row['changed_by_username']) && ! empty($row['username'])) {
                $row['changed_by_username'] = $row['username'];
            }
        }
        unset($row);

        return $rows;
    }

    /**
     * Catat satu baris ke quotation_stage_history (jika tabel & user valid).
     * Menggunakan fallback user id seperti logQuotationChange agar audit tidak hilang diam-diam.
     */
    private function insertQuotationStageHistoryIfExists(
        int $quotationId,
        string $oldStage,
        string $newStage,
        ?string $changeReason = null,
        ?string $changeNotes = null
    ): void {
        $db = \Config\Database::connect();
        if (! $db->tableExists('quotation_stage_history')) {
            return;
        }
        $quotationIdCol = $db->fieldExists('id_quotation', 'quotation_stage_history') ? 'id_quotation' : 'quotation_id';
        $changedByCol = null;
        foreach (['changed_by', 'updated_by', 'created_by', 'user_id'] as $c) {
            if ($db->fieldExists($c, 'quotation_stage_history')) {
                $changedByCol = $c;
                break;
            }
        }
        $changedAtCol = null;
        foreach (['changed_at', 'created_at', 'updated_at'] as $c) {
            if ($db->fieldExists($c, 'quotation_stage_history')) {
                $changedAtCol = $c;
                break;
            }
        }

        $stageUserId = (int) (session()->get('user_id') ?? 0);
        if ($stageUserId <= 0 || ! $db->table('users')->where('id', $stageUserId)->countAllResults()) {
            log_message('warning', 'insertQuotationStageHistoryIfExists: invalid or missing session user_id for quotation_id=' . $quotationId . '; using fallback id=1 (same policy as quotation_history)');
            $stageUserId = 1;
            if (! $db->table('users')->where('id', $stageUserId)->countAllResults()) {
                log_message('error', 'insertQuotationStageHistoryIfExists: skip (no users.id=1 fallback)');

                return;
            }
        }
        if ($changedByCol === null || $changedAtCol === null) {
            log_message('error', 'insertQuotationStageHistoryIfExists: skip (missing changed_by/changed_at columns)');

            return;
        }

        $newStageCol = null;
        foreach (['stage', 'new_stage', 'to_stage', 'stage_to'] as $c) {
            if ($db->fieldExists($c, 'quotation_stage_history')) {
                $newStageCol = $c;
                break;
            }
        }
        if ($newStageCol === null) {
            log_message('error', 'insertQuotationStageHistoryIfExists: no target stage column (stage/new_stage/…) on quotation_stage_history');

            return;
        }

        $insert = [
            $quotationIdCol => $quotationId,
            $newStageCol    => $newStage,
            $changedByCol   => $stageUserId,
            $changedAtCol   => date('Y-m-d H:i:s'),
        ];
        if ($db->fieldExists('stage_from', 'quotation_stage_history')) {
            $insert['stage_from'] = $oldStage;
        }
        if ($db->fieldExists('change_reason', 'quotation_stage_history')) {
            $insert['change_reason'] = $changeReason;
        }
        if ($db->fieldExists('change_notes', 'quotation_stage_history')) {
            $insert['change_notes'] = $changeNotes;
        }
        try {
            $db->table('quotation_stage_history')->insert($insert);
        } catch (\Throwable $e) {
            log_message('error', 'insertQuotationStageHistoryIfExists: ' . $e->getMessage());
        }
    }

    /**
     * Ringkasan header quotation untuk quotation_history (workflow / kirim / deal).
     *
     * @param array<string, mixed>|null $extraBefore
     * @param array<string, mixed>|null $extraAfter
     */
    private function logQuotationWorkflowDocumentHistory(
        int $quotationId,
        string $actionType,
        string $changesSummary,
        array $before,
        array $after,
        ?array $extraBefore = null,
        ?array $extraAfter = null
    ): void {
        $row = $this->quotationModel->find($quotationId);
        if (! $row) {
            return;
        }
        $version = (int) ($row['version'] ?? 1);
        $pick = static function (array $q): array {
            return [
                'quotation_number' => $q['quotation_number'] ?? null,
                'workflow_stage'   => $q['workflow_stage'] ?? null,
                'stage'            => $q['stage'] ?? null,
                'is_deal'          => $q['is_deal'] ?? null,
                'total_amount'     => $q['total_amount'] ?? null,
                'valid_until'      => $q['valid_until'] ?? null,
            ];
        };
        $oldVals = array_merge($pick($before), $extraBefore ?? []);
        $newVals = array_merge($pick($after), $extraAfter ?? []);
        $this->logQuotationChange($quotationId, $version, $actionType, $changesSummary, $oldVals, $newVals);
    }

    /**
     * Riwayat perubahan quotation specification (harga, qty, teknis) untuk SPV/Manager.
     */
    public function getQuotationSpecificationHistory($quotationId)
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $db = \Config\Database::connect();
            if (! $db->tableExists('quotation_specification_history')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data'    => [],
                    'meta'    => ['table_ready' => false],
                ]);
            }

            $historyModel = new \App\Models\QuotationSpecificationHistoryModel();
            $rows         = $historyModel->getHistoryForQuotation((int) $quotationId);

            foreach ($rows as &$r) {
                $fn                    = trim(((string) ($r['first_name'] ?? '')) . ' ' . ((string) ($r['last_name'] ?? '')));
                $r['changed_by_display'] = $fn !== '' ? $fn : ($r['username'] ?? '-');
            }
            unset($r);

            return $this->response->setJSON([
                'success' => true,
                'data'    => $rows,
                'meta'    => ['table_ready' => true],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Marketing::getQuotationSpecificationHistory - ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ])->setStatusCode(500);
        }
    }

    /**
     * Get quotation specifications for SPK creation
     */
    public function getSpecifications($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $db = \Config\Database::connect();
            
            log_message('info', "getSpecifications called for quotation ID: {$quotationId}");
            
            // Get specifications with all related data
            $specifications = $db->table('quotation_specifications qs')
                ->select('qs.id_specification,
                          qs.id_quotation,
                          qs.specification_name,
                          qs.specification_type,
                          qs.quantity,
                          qs.is_spare_unit,
                          qs.spare_quantity,
                          qs.include_operator,
                          qs.operator_quantity,
                          qs.operator_monthly_rate,
                          qs.operator_daily_rate,
                          qs.operator_description,
                          qs.operator_certification_required,
                          qs.monthly_price,
                          qs.monthly_price as unit_price,
                          qs.monthly_price as harga_per_unit,
                          qs.daily_price,
                          qs.daily_price as harga_per_unit_harian,
                          qs.total_price,
                          qs.notes,
                          qs.fork_id,
                          qs.departemen_text,
                          qs.tipe_unit_text,
                          qs.kapasitas_text,
                          qs.merk_unit_text,
                          COALESCE(qs.unit_accessories, "") as unit_accessories,
                          COALESCE(qs.unit_accessories, "") as aksesoris,
                          qs.brand_id,
                          mu.merk_unit,
                          mu.merk_unit as brand,
                          mu.model_unit,
                          qs.departemen_id,
                          qs.tipe_unit_id,
                          qs.kapasitas_id,
                          qs.attachment_id,
                          a.tipe as attachment_tipe,
                          a.merk as attachment_merk,
                          qs.battery_id,
                          b.jenis_baterai,
                          qs.charger_id,
                          c.merk_charger,
                          c.tipe_charger,
                          qs.mast_id,
                          m.tipe_mast as mast_name,
                          qs.ban_id,
                          tb.tipe_ban as tire_name,
                          qs.roda_id,
                          jr.tipe_roda as wheel_name,
                          qs.valve_id,
                          v.jumlah_valve as valve_name,
                          d.nama_departemen, 
                          tu.tipe as nama_tipe_unit,
                          tu.jenis as jenis_tipe_unit,
                          k.kapasitas_unit as nama_kapasitas')
                ->join('departemen d', 'd.id_departemen = qs.departemen_id', 'left')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = qs.tipe_unit_id', 'left')
                ->join('kapasitas k', 'k.id_kapasitas = qs.kapasitas_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = qs.brand_id', 'left')
                ->join('baterai b', 'b.id = qs.battery_id', 'left')
                ->join('attachment a', 'a.id_attachment = qs.attachment_id', 'left')
                ->join('charger c', 'c.id_charger = qs.charger_id', 'left')
                ->join('tipe_mast m', 'm.id_mast = qs.mast_id', 'left')
                ->join('tipe_ban tb', 'tb.id_ban = qs.ban_id', 'left')
                ->join('jenis_roda jr', 'jr.id_roda = qs.roda_id', 'left')
                ->join('valve v', 'v.id_valve = qs.valve_id', 'left')
                ->where('qs.id_quotation', $quotationId)
                ->get()
                ->getResultArray();

            log_message('info', "getSpecifications found " . count($specifications) . " specifications");
            
            // Add existing SPK count for each specification
            foreach ($specifications as &$spec) {
                $existingSPK = $db->table('spk')
                    ->selectSum('jumlah_unit', 'total_units')
                    ->where('quotation_specification_id', $spec['id_specification'])
                    ->where('status !=', 'CANCELLED')
                    ->get()
                    ->getRowArray();
                
                $spec['existing_spk_units'] = (int)($existingSPK['total_units'] ?? 0);
                $spec['available_units'] = (int)$spec['quantity'] - $spec['existing_spk_units'];
            }
            unset($spec); // Break reference

            if (empty($specifications)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No specifications found for this quotation'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $specifications
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getSpecifications error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Update quotation contract complete flag
     */
    public function updateContractComplete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $quotationId = $this->request->getPost('quotation_id');
        
        if (!$quotationId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Quotation ID is required'
            ]);
        }

        // Use Query Builder to avoid CI4 Model update exceptions
        $db = \Config\Database::connect();
        
        $result = $db->table('quotations')
            ->where('id_quotation', $quotationId)
            ->update(['customer_contract_complete' => 1]);

        if ($result !== false) {
            // Get quotation details for notification
            $quotation = $this->quotationModel->find($quotationId);
            
            // Send notification - contract completed
            if (function_exists('notify_contract_completed') && $quotation) {
                notify_contract_completed([
                    'id' => $quotationId,
                    'contract_number' => $quotation['quotation_number'] ?? '',
                    'customer_name' => $quotation['prospect_name'] ?? '',
                    'total_value' => $quotation['total_amount'] ?? 0,
                    'completion_date' => date('Y-m-d'),
                    'completed_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/marketing/quotations/view/' . $quotationId)
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Contract stage completed'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update quotation'
        ]);
    }

    /**
     * Link existing contract to quotation (Option 1: Pure Selection - No Update)
     * This method only creates a link between quotation and existing contract
     * without modifying the contract data itself
     */
    public function linkContract()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $quotationId = $this->request->getPost('quotation_id');
        $contractId = $this->request->getPost('contract_id');
        
        if (!$quotationId || !$contractId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Quotation ID and Contract ID are required'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Check if customer_contract_complete column exists, if not use workflow flags
            $fields = $db->getFieldNames('quotations');
            $hasContractCompleteField = in_array('customer_contract_complete', $fields);
            
            // Update quotation to mark contract as complete and link to contract
            $updateData = [
                'created_contract_id' => $contractId,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Add contract complete flag if column exists
            if ($hasContractCompleteField) {
                $updateData['customer_contract_complete'] = 1;
            }
            
            $result = $db->table('quotations')
                ->where('id_quotation', $quotationId)
                ->update($updateData);

            if ($result !== false) {
                log_message('info', "Contract {$contractId} linked to quotation {$quotationId}");
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Contract linked to quotation successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to link contract to quotation'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::linkContract - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Export quotations to Excel
     */
    public function exportQuotations()
    {
        if (!$this->hasPermission('marketing.quotation.export')) {
            return redirect()->to('/marketing/quotations')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $filters = [
            'stage' => $this->request->getGet('stage'),
            'assigned_to' => $this->request->getGet('assigned_to'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        $builder = $this->quotationModel;

        // Apply filters
        if (!empty($filters['stage'])) {
            $builder->where('stage', $filters['stage']);
        }
        if (!empty($filters['assigned_to'])) {
            $builder->where('assigned_to', $filters['assigned_to']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('quotation_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('quotation_date <=', $filters['date_to']);
        }
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('quotation_number', $filters['search'])
                ->orLike('prospect_name', $filters['search'])
                ->orLike('quotation_title', $filters['search'])
                ->groupEnd();
        }

        // Add User Join for Assigned To Name
        $builder->select('quotations.*, u.username as assigned_to_name');
        $builder->join('users u', 'u.id = quotations.assigned_to', 'left');

        $quotations = $builder->orderBy('quotation_date', 'DESC')->findAll();
        
        // Pass to View for Excel Export
        $data = ['quotations' => $quotations];
        return view('marketing/export_quotations', $data);
    }

    /**
     * Convert quotation to deal
     */
    public function convertToDeal($quotationId)
    {
        $canAct = $this->hasPermission('marketing.quotation.edit')
            || $this->canManage('marketing');
        if (!$canAct) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin']);
        }

        $quotation = $this->quotationModel->find($quotationId);

        if (!$quotation) {
            return $this->response->setJSON(['success' => false, 'message' => 'Quotation tidak ditemukan']);
        }

        if ($quotation['stage'] !== 'ACCEPTED') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only accepted quotations can be converted to deals']);
        }

        // Update quotation to mark as deal
        $this->quotationModel->update($quotationId, [
            'is_deal' => 1,
            'deal_date' => date('Y-m-d H:i:s')
        ]);

        // Log activity
        $this->logActivity('CONVERT', 'quotation', $quotationId, 'Converted quotation to deal: ' . $quotation['quotation_number']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Quotation successfully converted to deal'
        ]);
    }

    /**
     * Update quotation stage
     */
    public function updateQuotationStage($quotationId)
    {
        $canAct = $this->hasPermission('marketing.quotation.edit')
            || $this->canManage('marketing');
        if (!$canAct) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'new_stage' => 'required|in_list[DRAFT,SENT,FOLLOW_UP,NEGOTIATION,ACCEPTED,REJECTED,EXPIRED]',
            'probability_percent' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[100]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang diisi.',
                'errors' => $validation->getErrors()
            ]);
        }

        $updateData = [
            'stage' => $this->request->getPost('new_stage'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->request->getPost('probability_percent') !== '') {
            $updateData['probability_percent'] = $this->request->getPost('probability_percent');
        }

        $quotationBefore = $this->quotationModel->find($quotationId);
        if (! $quotationBefore) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Quotation tidak ditemukan',
            ]);
        }
        $oldStage = $quotationBefore['stage'] ?? 'UNKNOWN';

        if (! $this->quotationModel->update($quotationId, $updateData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui stage. Silakan coba lagi.',
            ]);
        }

        // Get quotation details for notification (after update)
        $quotation = $this->quotationModel->find($quotationId);

        // Send notification - quotation stage changed
        if (function_exists('notify_quotation_stage_changed')) {
            notify_quotation_stage_changed([
                'id' => $quotationId,
                'quotation_number' => $quotation['quotation_number'] ?? '',
                'customer_name' => $quotation['prospect_name'] ?? '',
                'old_stage' => $oldStage,
                'new_stage' => $updateData['stage'],
                'updated_by' => session('username') ?? session('user_id'),
                'url' => base_url('/marketing/quotations/view/' . $quotationId)
            ]);
        }

        $this->insertQuotationStageHistoryIfExists(
            (int) $quotationId,
            $oldStage,
            (string) $updateData['stage'],
            $this->request->getPost('change_reason'),
            $this->request->getPost('change_notes')
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Stage updated successfully'
        ]);
    }

    /**
     * Get marketing dashboard statistics
     */
    private function getMarketingDashboardStats()
    {
        // Get quotation stats
        $quotation_stats = $this->getQuotationStatsData();
        
        // Get contract stats
        $total_contracts = $this->kontrakModel->countAll();
        $active_contracts = $this->kontrakModel->where('status', 'ACTIVE')->countAllResults(false);
        
        // Calculate monthly revenue (from active contracts)
        $monthly_revenue = $this->kontrakModel
            ->selectSum('nilai_total')
            ->where('status', 'ACTIVE')
            ->where('MONTH(tanggal_mulai)', date('n'))
            ->where('YEAR(tanggal_mulai)', date('Y'))
            ->get()
            ->getRow()
            ->nilai_total ?? 0;
        
        // Calculate conversion rate (deals / total quotations)
        $conversion_rate = $quotation_stats['total'] > 0 ? 
            round(($quotation_stats['deals'] / $quotation_stats['total']) * 100, 1) : 0;
        
        // Mock customer satisfaction (can be replaced with real data later)
        $customer_satisfaction = 87; // This should come from customer feedback system
        
        return [
            'total_quotations' => $quotation_stats['total'],
            'pending_quotations' => $quotation_stats['pending'],
            'active_contracts' => $active_contracts,
            'monthly_revenue' => $monthly_revenue,
            'conversion_rate' => $conversion_rate,
            'customer_satisfaction' => $customer_satisfaction
        ];
    }

    /**
     * Get recent quotations for dashboard display
     */
    private function getRecentQuotationsForDashboard()
    {
        $quotations = $this->quotationModel
            ->select('id_quotation as id, quotation_number, prospect_name as client, quotation_title as project, total_amount as value, stage as status, created_at')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();
        
        // Format data for dashboard display
        foreach ($quotations as &$quotation) {
            $quotation['id'] = $quotation['quotation_number'];
            $quotation['status'] = $this->formatQuotationStatusForDashboard($quotation['status']);
        }
        
        return $quotations;
    }

    /**
     * Get active contracts for dashboard display
     */
    private function getActiveContractsForDashboard()
    {
        $contracts = $this->kontrakModel
            ->select('no_kontrak as contract_number, customer_name as client, nilai_total as value, tanggal_mulai as start_date, tanggal_berakhir as end_date, status')
            ->where('status', 'ACTIVE')
            ->orderBy('tanggal_mulai', 'DESC')
            ->limit(5)
            ->findAll();
        
        // Add project field (can be derived from contract details)
        foreach ($contracts as &$contract) {
            $contract['project'] = 'Equipment Rental'; // This should be derived from kontrak_spesifikasi
        }
        
        return $contracts;
    }

    /**
     * Get revenue data for chart (last 6 months)
     */
    private function getRevenueDataForChart()
    {
        $revenue_data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $month = date('M Y', strtotime("-$i months"));
            
            $revenue = $this->kontrakModel
                ->selectSum('nilai_total')
                ->where('status', 'ACTIVE')
                ->where("DATE_FORMAT(tanggal_mulai, '%Y-%m')", $date)
                ->get()
                ->getRow()
                ->nilai_total ?? 0;
            
            $revenue_data[$month] = (float)$revenue;
        }
        
        return $revenue_data;
    }

    /**
     * Format quotation status for dashboard display
     */
    private function formatQuotationStatusForDashboard($stage)
    {
        $statusMap = [
            'DRAFT' => 'Draft',
            'SENT' => 'Pending',
            'FOLLOW_UP' => 'Follow Up',
            'NEGOTIATION' => 'Negotiation',
            'ACCEPTED' => 'Approved',
            'REJECTED' => 'Rejected',
            'EXPIRED' => 'Expired'
        ];
        
        return $statusMap[$stage] ?? $stage;
    }

    public function spk()
    {
        // Load simple_rbac helper
        helper('simple_rbac');
        
        // Extract SPK ID from URL for auto-opening modal (from notification deep linking)
        $uri = service('uri');
        $autoOpenSpkId = null;
        
        // Check if URL matches /marketing/spk/detail/{id}
        $segments = $uri->getSegments();
        if (count($segments) >= 3 && $segments[1] === 'spk' && $segments[2] === 'detail' && isset($segments[3]) && is_numeric($segments[3])) {
            $autoOpenSpkId = (int)$segments[3];
        }
        
        return view('marketing/spk', [
            'title' => 'Work Orders (SPK)',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/marketing/spk' => 'Work Orders (SPK)'
            ],
            'can_view_marketing' => can_view('marketing'),
            'can_create_marketing' => $this->canManage('marketing'),
            'can_export_marketing' => $this->canExport('marketing'),
            'autoOpenSpkId' => $autoOpenSpkId
        ]);
    }
    public function di()
    {
        return view('marketing/di', [
            'title' => 'Delivery Instructions (DI)',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/marketing/di' => 'Delivery Instructions (DI)'
            ]
        ]);
    }

    // Generate/download SPK PDF (server-rendered HTML -> Dompdf)
    public function spkPdf($id)
    {
        $id = (int)$id;
        $row = $this->spkModel->find($id);
        if (!$row) {
            return $this->response->setStatusCode(404)->setBody('SPK tidak ditemukan');
        }
        // Enrich spesifikasi similar to spkDetail
        $spec = [];
        if (!empty($row['spesifikasi'])) {
            $dec = json_decode($row['spesifikasi'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($dec)) $spec = $dec;
        }
        $enriched = $spec;
        $mapQueries = [
            'departemen_id' => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen'],
            'kapasitas_id'  => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit'],
            'mast_id'       => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast'],
            'ban_id'        => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban'],
            'valve_id'      => ['table'=>'valve','id'=>'id_valve','name'=>'jumlah_valve'],
            'roda_id'       => ['table'=>'jenis_roda','id'=>'id_roda','name'=>'tipe_roda'],
        ];
        foreach ($mapQueries as $key => $cfg) {
            if (!empty($spec[$key])) {
                $rec = $this->db->table($cfg['table'])->select($cfg['name'].' as name', false)->where($cfg['id'], $spec[$key])->get()->getRowArray();
                if ($rec && isset($rec['name'])) $enriched[$key.'_name'] = $rec['name'];
            }
        }
        if (!empty($spec['selected']) && is_array($spec['selected'])) {
            $sel = $spec['selected'];
            $enriched['selected'] = $sel;
            if (!empty($sel['unit_id'])) {
                $u = $this->unitModel
                    ->select('inventory_unit.no_unit, inventory_unit.serial_number, inventory_unit.tahun_unit, inventory_unit.lokasi_unit, inventory_unit.sn_mast, inventory_unit.sn_mesin, inventory_unit.sn_baterai, inventory_unit.sn_charger, mu.merk_unit, mu.model_unit')
                    ->join('model_unit mu','mu.id_model_unit = inventory_unit.model_unit_id','left')
                    ->where('inventory_unit.id_inventory_unit', (int)$sel['unit_id'])
                    ->first();
                if ($u) {
                    $enriched['selected']['unit'] = [
                        'id' => (int)$sel['unit_id'],
                        'no_unit' => $u['no_unit'] ?? null,
                        'serial_number' => $u['serial_number'] ?? null,
                        'tahun_unit' => $u['tahun_unit'] ?? null,
                        'merk_unit' => $u['merk_unit'] ?? null,
                        'model_unit' => $u['model_unit'] ?? null,
                        'lokasi_unit' => $u['lokasi_unit'] ?? null,
                        'sn_mast' => $u['sn_mast'] ?? null,
                        'sn_mesin' => $u['sn_mesin'] ?? null,
                        'sn_baterai' => $u['sn_baterai'] ?? null,
                        'sn_charger' => $u['sn_charger'] ?? null,
                    ];
                }
            }
            if (!empty($sel['inventory_attachment_id'])) {
                $a = $this->attModel
                    ->select('a.tipe, a.merk, a.model, inventory_attachment.sn_attachment, inventory_attachment.lokasi_penyimpanan')
                    ->join('attachment a','a.id_attachment = inventory_attachment.attachment_id','left')
                    ->where('inventory_attachment.id_inventory_attachment', (int)$sel['inventory_attachment_id'])
                    ->first();
                if ($a) {
                    $enriched['selected']['attachment'] = [
                        'tipe' => $a['tipe'] ?? null,
                        'merk' => $a['merk'] ?? null,
                        'model' => $a['model'] ?? null,
                        'sn_attachment' => $a['sn_attachment'] ?? null,
                        'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    ];
                }
            }
        }
        $html = view('marketing/spk_pdf', ['spk'=>$row, 'spesifikasi'=>$enriched]);
        try {
            if (!class_exists('\\Dompdf\\Dompdf')) {
                return redirect()->to(base_url('marketing/spk/print/'.$id));
            }
            $optClass = '\\Dompdf\\Options';
            $domClass = '\\Dompdf\\Dompdf';
            $options = new $optClass();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $dompdf = new $domClass($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $filenameCore = $row['po_kontrak_nomor'] ?? $row['nomor_spk'] ?? ('SPK_'.$id);
            $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filenameCore).'.pdf';
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"')
                ->setBody($dompdf->output());
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setBody('Gagal membuat PDF: '.$e->getMessage());
        }
    }

    /**
     * Get unit components from inventory tables (single source of truth)
     */
    private function getUnitComponents($unitId)
    {
        return $this->componentHelper->getUnitComponents($unitId);
    }

    /** Render HTML print view for browser printing (no PDF lib required) */
    public function spkPrint($id)
    {
        $id = (int)$id;
        $row = $this->db->table('spk')
            ->select('spk.*, qs_lookup.id_quotation, q_lookup.quotation_number')
            ->join('quotation_specifications qs_lookup', 'qs_lookup.id_specification = spk.quotation_specification_id', 'left')
            ->join('quotations q_lookup', 'q_lookup.id_quotation = qs_lookup.id_quotation', 'left')
            ->where('spk.id', $id)
            ->get()
            ->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setBody('SPK tidak ditemukan');
        }
        // reuse enrichment from spkPdf/spkDetail
        $spec = [];
        if (!empty($row['spesifikasi'])) {
            $dec = json_decode($row['spesifikasi'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($dec)) $spec = $dec;
        }
        $enriched = $spec;
        
        // Load quotation_specifications data (for Equipment section - data permintaan marketing)
        $kontrak_spec = null;
        // Resolve spec ID: directly from spk.quotation_specification_id, or fallback via kontrak
        $specId = (int)($row['quotation_specification_id'] ?? 0);
        if (!$specId && !empty($row['kontrak_id'])) {
            $specIdRow = $this->db->table('quotation_specifications qs')
                ->select('qs.id_specification')
                ->join('quotations q', 'q.id_quotation = qs.id_quotation', 'inner')
                ->where('qs.kontrak_id', $row['kontrak_id'])
                ->where('qs.is_active', 1)
                ->orderBy('qs.id_specification', 'DESC')
                ->get()->getRowArray();
            $specId = (int)($specIdRow['id_specification'] ?? 0);
        }
        if ($specId) {
            // Check which optional tables exist in this DB (production may differ from local schema)
            $hasBaterai = $this->db->tableExists('baterai');
            $hasFork    = $this->db->tableExists('fork');
            // Also check if the FK columns exist in quotation_specifications
            $qsCols = $this->db->getFieldNames('quotation_specifications');
            $hasBatteryCol = in_array('battery_id', $qsCols);
            $hasForkCol    = in_array('fork_id', $qsCols);

            $builder = $this->db->table('quotation_specifications qs')
                ->select('qs.*')
                ->select('COALESCE(qs.tipe_unit_text, tu.jenis) as kontrak_jenis_unit, tu.tipe as kontrak_tipe_unit')
                ->select('COALESCE(qs.kapasitas_text, k.kapasitas_unit) as kontrak_kapasitas_name')
                ->select('COALESCE(qs.departemen_text, d.nama_departemen) as kontrak_departemen_name')
                ->select('tm.tipe_mast as kontrak_mast_name')
                ->select('jr.tipe_roda as kontrak_roda_name')
                ->select('tb.tipe_ban as kontrak_ban_name')
                ->select('v.jumlah_valve as kontrak_valve_name')
                ->select('chr.merk_charger as kontrak_merk_charger, chr.tipe_charger as kontrak_tipe_charger')
                ->select('COALESCE(qs.merk_unit_text, mu.merk_unit) as merk_unit, mu.model_unit')
                ->select('att.tipe as attachment_tipe, att.merk as attachment_merk, att.model as attachment_model')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = qs.tipe_unit_id', 'left')
                ->join('kapasitas k', 'k.id_kapasitas = qs.kapasitas_id', 'left')
                ->join('departemen d', 'd.id_departemen = qs.departemen_id', 'left')
                ->join('tipe_mast tm', 'tm.id_mast = qs.mast_id', 'left')
                ->join('jenis_roda jr', 'jr.id_roda = qs.roda_id', 'left')
                ->join('tipe_ban tb', 'tb.id_ban = qs.ban_id', 'left')
                ->join('valve v', 'v.id_valve = qs.valve_id', 'left')
                ->join('charger chr', 'chr.id_charger = qs.charger_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = qs.brand_id', 'left')
                ->join('attachment att', 'att.id_attachment = qs.attachment_id', 'left');

            if ($hasBaterai && $hasBatteryCol) {
                $builder->select('bat.merk_baterai, bat.tipe_baterai, bat.jenis_baterai')
                        ->join('baterai bat', 'bat.id = qs.battery_id', 'left');
            }
            if ($hasFork && $hasForkCol) {
                $builder->select('fk.name as fork_name, fk.fork_class as fork_class')
                        ->join('fork fk', 'fk.id = qs.fork_id', 'left');
            }

            $result      = $builder->where('qs.id_specification', $specId)->get();
            $kontrak_spec = ($result !== false) ? $result->getRowArray() : null;

            // Map quotation_specifications fields to expected kontrak_spesifikasi format
            if ($kontrak_spec) {
                // Map new field names to legacy names for compatibility
                $kontrak_spec['jumlah_dibutuhkan'] = $kontrak_spec['quantity'] ?? 1;
                
                // Map battery information
                if (!empty($kontrak_spec['merk_baterai']) || !empty($kontrak_spec['tipe_baterai']) || !empty($kontrak_spec['jenis_baterai'])) {
                    $batteryParts = array_filter([
                        $kontrak_spec['merk_baterai'] ?? '',
                        $kontrak_spec['tipe_baterai'] ?? '',
                        $kontrak_spec['jenis_baterai'] ?? ''
                    ]);
                    $kontrak_spec['jenis_baterai'] = implode(' ', $batteryParts);
                }
                
                // Map attachment information
                if (!empty($kontrak_spec['attachment_tipe'])) {
                    $kontrak_spec['attachment_name'] = $kontrak_spec['attachment_tipe'];
                    if (!empty($kontrak_spec['attachment_merk'])) {
                        $kontrak_spec['attachment_name'] .= ' ' . $kontrak_spec['attachment_merk'];
                    }
                    if (!empty($kontrak_spec['attachment_model'])) {
                        $kontrak_spec['attachment_name'] .= ' ' . $kontrak_spec['attachment_model'];
                    }
                }
                
                // Decode unit_accessories JSON if stored as string
                if (isset($kontrak_spec['unit_accessories']) && is_string($kontrak_spec['unit_accessories'])) {
                    try {
                        $decoded_aks = json_decode($kontrak_spec['unit_accessories'], true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $kontrak_spec['aksesoris'] = $decoded_aks;
                        } else {
                            $kontrak_spec['aksesoris'] = $kontrak_spec['unit_accessories'];
                        }
                    } catch (\Exception $e) {
                        $kontrak_spec['aksesoris'] = $kontrak_spec['unit_accessories'];
                    }
                } else {
                    $kontrak_spec['aksesoris'] = $kontrak_spec['unit_accessories'] ?? '';
                }
            }
            
            // Format charger info if available
            if (!empty($kontrak_spec['kontrak_merk_charger']) || !empty($kontrak_spec['kontrak_tipe_charger'])) {
                $kontrak_spec['kontrak_charger_model'] = trim(($kontrak_spec['kontrak_merk_charger'] ?? '') . ' ' . ($kontrak_spec['kontrak_tipe_charger'] ?? ''));
            }
        }
        
        $mapQueries = [
            'departemen_id' => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen'],
            'kapasitas_id'  => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit'],
            'mast_id'       => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast'],
            'ban_id'        => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban'],
            'valve_id'      => ['table'=>'valve','id'=>'id_valve','name'=>'jumlah_valve'],
            'roda_id'       => ['table'=>'jenis_roda','id'=>'id_roda','name'=>'tipe_roda'],
        ];
        foreach ($mapQueries as $key => $cfg) {
            if (!empty($spec[$key])) {
                $rec = $this->db->table($cfg['table'])->select($cfg['name'].' as name', false)->where($cfg['id'], $spec[$key])->get()->getRowArray();
                if ($rec && isset($rec['name'])) $enriched[$key.'_name'] = $rec['name'];
            }
        }

        // ── Direct SPK: build synthetic kontrak_spec so print_spk.php Equipment section renders correctly ──
        if (!$kontrak_spec && ($row['source_type'] ?? '') === 'DIRECT') {
            $kontrak_spec = [
                'merk_unit'               => $enriched['merk_unit']              ?? '',
                'model_unit'              => $enriched['model_unit']             ?? '',
                'kontrak_jenis_unit'      => '',
                'kontrak_tipe_unit'       => '',
                'kontrak_kapasitas_name'  => $enriched['kapasitas_id_name']      ?? '',
                'kontrak_departemen_name' => $enriched['departemen_id_name']     ?? '',
                'kontrak_mast_name'       => $enriched['mast_id_name']           ?? '',
                'kontrak_ban_name'        => $enriched['ban_id_name']            ?? '',
                'kontrak_valve_name'      => $enriched['valve_id_name']          ?? '',
                'attachment_tipe'         => $enriched['attachment_tipe']        ?? '',
                'attachment_merk'         => $enriched['attachment_merk']        ?? '',
                'attachment_model'        => '',
                'fork_name'               => '',
                'fork_class'              => '',
                'notes'                   => $spec['notes']                      ?? ($row['catatan'] ?? ''),
                'aksesoris'               => $spec['aksesoris']                  ?? [],
                'jumlah_dibutuhkan'       => $row['jumlah_unit']                 ?? 1,
            ];

            // Resolve tipe_unit_id → jenis / tipe
            if (!empty($spec['tipe_unit_id'])) {
                $tuRow = $this->db->table('tipe_unit')
                    ->where('id_tipe_unit', (int) $spec['tipe_unit_id'])
                    ->get()->getRowArray();
                if ($tuRow) {
                    $kontrak_spec['kontrak_jenis_unit'] = $tuRow['jenis'] ?? '';
                    $kontrak_spec['kontrak_tipe_unit']  = $tuRow['tipe']  ?? '';
                }
            }

            // Resolve fork_id → fork name (fork table may not exist on all environments)
            if (!empty($spec['fork_id']) && $this->db->tableExists('fork')) {
                $forkRow = $this->db->table('fork')
                    ->where('id', (int) $spec['fork_id'])
                    ->get()->getRowArray();
                if ($forkRow) {
                    $kontrak_spec['fork_name']  = $forkRow['name']       ?? '';
                    $kontrak_spec['fork_class'] = $forkRow['fork_class'] ?? '';
                }
            }

            // Build attachment display name
            if (!empty($kontrak_spec['attachment_tipe'])) {
                $attName = $kontrak_spec['attachment_tipe'];
                if (!empty($kontrak_spec['attachment_merk'])) $attName .= ' ' . $kontrak_spec['attachment_merk'];
                $kontrak_spec['attachment_name'] = trim($attName);
            }
        }

        // Build prepared_units_detail (match Service controller behavior)
        $preparedUnits = [];
        if (!empty($enriched['prepared_units']) && is_array($enriched['prepared_units'])) {
            $preparedUnits = $enriched['prepared_units'];
        } elseif (!empty($spec['prepared_units']) && is_array($spec['prepared_units'])) {
            $preparedUnits = $spec['prepared_units'];
        }
        if (!empty($preparedUnits)) {
            $preparedDetails = [];
            foreach ($preparedUnits as $pu) {
                $uInfo = null; $aInfo = null; $bInfo = null; $cInfo = null;
                $unitLabel=''; $attLabel=''; $batLabel=''; $chrLabel='';
                
                // Load unit info
                if (!empty($pu['unit_id'])) {
                    $uInfo = $this->db->table('inventory_unit iu')
                        ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.lokasi_unit, mu.merk_unit, mu.model_unit, tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                        ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name')
                        ->select('tm.tipe_mast as mast_name, jr.tipe_roda as roda_name, tb.tipe_ban as ban_name, v.jumlah_valve as valve_name')
                        ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                        ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                        ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                        ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                        ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                        ->join('jenis_roda jr','jr.id_roda = iu.roda_id','left')
                        ->join('tipe_ban tb','tb.id_ban = iu.ban_id','left')
                        ->join('valve v','v.id_valve = iu.valve_id','left')
                        ->where('iu.id_inventory_unit', $pu['unit_id'])
                        ->get()->getRowArray();
                    if ($uInfo) {
                        $unitLabel = trim(($uInfo['no_unit'] ?: '-') . ' - ' . ($uInfo['merk_unit'] ?: '-') . ' ' . ($uInfo['model_unit'] ?: '') . ' @ ' . ($uInfo['lokasi_unit'] ?: '-'));
                    }
                }
                
                // Load attachment info
                if (!empty($pu['attachment_inventory_id'])) {
                    $aInfo = $this->componentHelper->getAttachmentByInventoryId($pu['attachment_inventory_id']);
                    if ($aInfo) {
                        $attLabel = trim(($aInfo['tipe'] ?: '-') . ' ' . ($aInfo['merk'] ?: '') . ' ' . ($aInfo['model'] ?: ''));
                        $suf = [];
                        if (!empty($aInfo['sn_attachment'])) $suf[] = 'SN: '.$aInfo['sn_attachment'];
                        if (!empty($aInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$aInfo['lokasi_penyimpanan'];
                        if ($suf) $attLabel .= ' ['.implode(', ', $suf).']';
                    }
                }
                
                // Load battery info
                if (!empty($pu['battery_inventory_id'])) {
                    $bInfo = $this->componentHelper->getBatteryByInventoryId($pu['battery_inventory_id']);
                    if ($bInfo) {
                        $batLabel = trim(($bInfo['merk_baterai'] ?: '-') . ' ' . ($bInfo['tipe_baterai'] ?: '') . ' ' . ($bInfo['jenis_baterai'] ?: ''));
                        $suf = [];
                        if (!empty($bInfo['sn_baterai'])) $suf[] = 'SN: '.$bInfo['sn_baterai'];
                        if (!empty($bInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$bInfo['lokasi_penyimpanan'];
                        if ($suf) $batLabel .= ' ['.implode(', ', $suf).']';
                    }
                }
                
                // Load charger info  
                if (!empty($pu['charger_inventory_id'])) {
                    $cInfo = $this->componentHelper->getChargerByInventoryId($pu['charger_inventory_id']);
                    if ($cInfo) {
                        $chrLabel = trim(($cInfo['merk_charger'] ?: '-') . ' ' . ($cInfo['tipe_charger'] ?: ''));
                        $suf = [];
                        if (!empty($cInfo['sn_charger'])) $suf[] = 'SN: '.$cInfo['sn_charger'];
                        if (!empty($cInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$cInfo['lokasi_penyimpanan'];
                        if ($suf) $chrLabel .= ' ['.implode(', ', $suf).']';
                    }
                }
                $preparedDetails[] = [
                    'unit_id' => $pu['unit_id'] ?? null,
                    'unit_label' => $unitLabel,
                    'no_unit' => $uInfo['no_unit'] ?? '',
                    'serial_number' => $uInfo['serial_number'] ?? '',
                    'merk_unit' => $uInfo['merk_unit'] ?? '',
                    'model_unit' => $uInfo['model_unit'] ?? '',
                    'tipe_jenis' => $uInfo['tipe_jenis'] ?? '',
                    'jenis_unit' => $uInfo['jenis_unit'] ?? '',
                    'kapasitas_name' => $uInfo['kapasitas_name'] ?? '',
                    'departemen_name' => $uInfo['departemen_name'] ?? '',
                    'mast_id_name' => $uInfo['mast_name'] ?? '',
                    'roda_id_name' => $uInfo['roda_name'] ?? '',
                    'ban_id_name' => $uInfo['ban_name'] ?? '',
                    'valve_id_name' => $uInfo['valve_name'] ?? '',
                    'attachment_inventory_id' => $pu['attachment_inventory_id'] ?? null,
                    'attachment_label' => $attLabel,
                    'sn_attachment_formatted' => !empty($aInfo['sn_attachment']) ? 
                        trim(($aInfo['tipe'] ?? '') . ' ' . ($aInfo['merk'] ?? '') . ' ' . ($aInfo['model'] ?? '')) . ' (SN: ' . $aInfo['sn_attachment'] . ')' : 
                        trim(($aInfo['tipe'] ?? '') . ' ' . ($aInfo['merk'] ?? '') . ' ' . ($aInfo['model'] ?? '')),
                    'battery_inventory_id' => $pu['battery_inventory_id'] ?? null,
                    'battery_label' => $batLabel,
                    'sn_baterai_formatted' => !empty($bInfo['sn_baterai']) ? 
                        trim(($bInfo['merk_baterai'] ?? '') . ' ' . ($bInfo['tipe_baterai'] ?? '') . ' ' . ($bInfo['jenis_baterai'] ?? '')) . ' (SN: ' . $bInfo['sn_baterai'] . ')' : 
                        trim(($bInfo['merk_baterai'] ?? '') . ' ' . ($bInfo['tipe_baterai'] ?? '') . ' ' . ($bInfo['jenis_baterai'] ?? '')),
                    'charger_inventory_id' => $pu['charger_inventory_id'] ?? null,
                    'charger_label' => $chrLabel,
                    'sn_charger_formatted' => !empty($cInfo['sn_charger']) ? 
                        trim(($cInfo['merk_charger'] ?? '') . ' ' . ($cInfo['tipe_charger'] ?? '')) . ' (SN: ' . $cInfo['sn_charger'] . ')' : 
                        trim(($cInfo['merk_charger'] ?? '') . ' ' . ($cInfo['tipe_charger'] ?? '')),
                    'mekanik' => $pu['mekanik'] ?? '',
                    'aksesoris' => $pu['aksesoris_tersedia'] ?? $pu['aksesoris'] ?? '',
                    'catatan' => $pu['catatan'] ?? '',
                    'timestamp' => $pu['timestamp'] ?? ''
                ];
            }
            $enriched['prepared_units_detail'] = $preparedDetails;
        }

        // Load unit data from approval workflow if available
        // Handle both Aset (stored as no_unit) and Non Aset (stored as id_inventory_unit)
        if (!empty($row['persiapan_unit_id']) && $row['persiapan_unit_id'] != '0') {
            // First try to find by no_unit (for Aset units)
            $u = $this->db->table('inventory_unit iu')
                    ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id')
                    ->select('iu.sn_mast, iu.sn_mesin')
                    ->select('mu.merk_unit, mu.model_unit')
                    ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                    ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model')
                    ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name, su.status_unit')
                    ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                    ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                    ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                    ->join('mesin m','m.id = iu.model_mesin_id','left')
                    ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                    ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                    ->join('status_unit su','su.id_status = iu.status_unit_id','left')
                    ->where('iu.no_unit', $row['persiapan_unit_id'])
                    ->get()->getRowArray();
                
            // If not found by no_unit, try by id_inventory_unit (for Non Aset units)
            if (!$u) {
                $u = $this->db->table('inventory_unit iu')
                        ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id')
                        ->select('iu.sn_mast, iu.sn_mesin')
                        ->select('mu.merk_unit, mu.model_unit')
                        ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                        ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model')
                        ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name, su.status_unit')
                        ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                        ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                        ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                        ->join('mesin m','m.id = iu.model_mesin_id','left')
                        ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                        ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                        ->join('status_unit su','su.id_status = iu.status_unit_id','left')
                        ->where('iu.id_inventory_unit', $row['persiapan_unit_id'])
                        ->get()->getRowArray();
            }
                
            if ($u) {
                // Get unit components from inventory_attachment (single source of truth for serial numbers)
                $unitComponents = $this->getUnitComponents($u['id_inventory_unit']);
                $unitComponents = is_array($unitComponents) ? $unitComponents : [];

                // Prepare battery data safely
                $batterySN = '';
                $batteryModel = '';
                $batteryDisplay = '';
                $batteryData = isset($unitComponents['battery']) && is_array($unitComponents['battery']) ? $unitComponents['battery'] : [];
                if (!empty($batteryData)) {
                    if (!empty($batteryData['sn_baterai'])) {
                        $batterySN = $batteryData['sn_baterai'];
                    }
                    if (!empty($batteryData['tipe_baterai'])) {
                        $batteryModel = $batteryData['tipe_baterai'];
                        $batteryDisplay = $batteryModel;
                        if (!empty($batterySN)) {
                            $batteryDisplay = $batteryModel . ' (' . $batterySN . ')';
                        }
                    }
                }

                // Prepare charger data safely
                $chargerSN = '';
                $chargerModel = '';
                $chargerDisplay = '';
                $chargerData = isset($unitComponents['charger']) && is_array($unitComponents['charger']) ? $unitComponents['charger'] : [];
                if (!empty($chargerData)) {
                    if (!empty($chargerData['sn_charger'])) {
                        $chargerSN = $chargerData['sn_charger'];
                    }
                    if (!empty($chargerData['tipe_charger'])) {
                        $chargerModel = $chargerData['tipe_charger'];
                        $chargerDisplay = $chargerModel;
                        if (!empty($chargerSN)) {
                            $chargerDisplay = $chargerModel . ' (' . $chargerSN . ')';
                        }
                    }
                }

                $enriched['selected']['unit'] = [
                    'id' => (int)$u['id_inventory_unit'],
                    'no_unit' => $u['no_unit'] ?? null,
                    'serial_number' => $u['serial_number'] ?? null,
                    'merk_unit' => $u['merk_unit'] ?? null,
                    'model_unit' => $u['model_unit'] ?? null,
                    'tipe_jenis' => $u['tipe_jenis'] ?? null,
                    'jenis_unit' => $u['jenis_unit'] ?? null,
                    'tahun_unit' => $u['tahun_unit'] ?? null,
                    'lokasi_unit' => $u['lokasi_unit'] ?? null,
                    'kapasitas_name' => $u['kapasitas_name'] ?? null,
                    'departemen_name' => $u['departemen_name'] ?? null,
                    'status_unit' => $u['status_unit'] ?? null,
                    'status_unit_id' => $u['status_unit_id'] ?? null,
                    // Format: Model (SN) atau hanya Model jika SN kosong
                    'sn_mast_formatted' => !empty($u['sn_mast']) ? ($u['mast_model'] ?? 'Mast') . ' (' . $u['sn_mast'] . ')' : ($u['mast_model'] ?? ''),
                    'sn_mesin_formatted' => !empty($u['sn_mesin']) ? ($u['mesin_model'] ?? 'Mesin') . ' (' . $u['sn_mesin'] . ')' : ($u['mesin_model'] ?? ''),
                    'sn_baterai_formatted' => $batteryDisplay,
                    'sn_charger_formatted' => $chargerDisplay,
                ];
            
                // Override spesifikasi with unit data  
                $enriched['tipe_jenis'] = $u['tipe_jenis'] ?? $enriched['tipe_jenis'] ?? '';
                $enriched['jenis_unit'] = $u['jenis_unit'] ?? $enriched['jenis_unit'] ?? '';
                $enriched['merk_unit'] = $u['merk_unit'] ?? $enriched['merk_unit'] ?? '';
                $enriched['model_unit'] = $u['model_unit'] ?? $enriched['model_unit'] ?? '';
                $enriched['kapasitas_id_name'] = $u['kapasitas_name'] ?? $enriched['kapasitas_id_name'] ?? '';
                $enriched['departemen_id_name'] = $u['departemen_name'] ?? $enriched['departemen_id_name'] ?? '';
                $enriched['baterai_model'] = $batteryModel;
                $enriched['charger_model'] = $chargerModel;
            }
        }
        
        // Load attachment data from approval workflow if available  
        if (!empty($row['fabrikasi_attachment_id'])) {
            $a = $this->componentHelper->getAttachmentByInventoryId($row['fabrikasi_attachment_id']);
                
            if ($a) {
                $enriched['selected']['attachment'] = [
                    'id' => (int)$a['id_inventory_attachment'],
                    'tipe' => $a['tipe'] ?? null,
                    'merk' => $a['merk'] ?? null,
                    'model' => $a['model'] ?? null,
                    'sn_attachment' => $a['sn_attachment'] ?? null,
                    'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    // Format: Model (SN)
                    'sn_attachment_formatted' => !empty($a['sn_attachment']) ? ($a['model'] ?? 'Attachment') . ' (' . $a['sn_attachment'] . ')' : ($a['model'] ?? ''),
                ];
                
                // Override spesifikasi with attachment data
                $enriched['attachment_tipe'] = $a['tipe'] ?? $enriched['attachment_tipe'] ?? '';
            }
        }
        
        // Load battery and charger data from JSON spesifikasi (Electric department)
        if (!empty($spec['persiapan_battery_id'])) {
            $b = $this->componentHelper->getBatteryByInventoryId($spec['persiapan_battery_id']);
                
            if ($b) {
                $enriched['selected']['battery'] = [
                    'id' => (int)$b['id_inventory_attachment'],
                    'merk_baterai' => $b['merk_baterai'] ?? null,
                    'tipe_baterai' => $b['tipe_baterai'] ?? null,
                    'jenis_baterai' => $b['jenis_baterai'] ?? null,
                    'sn_baterai' => $b['sn_baterai'] ?? null,
                    'lokasi_penyimpanan' => $b['lokasi_penyimpanan'] ?? null,
                    // Format: Merk Tipe Jenis (SN)
                    'sn_baterai_formatted' => !empty($b['sn_baterai']) ? 
                        trim(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? '')) . ' (SN: ' . $b['sn_baterai'] . ')' : 
                        trim(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? '')),
                ];
                
                // Override spesifikasi with battery data
                $enriched['jenis_baterai'] = trim(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? ''));
            }
        }
        
        if (!empty($spec['persiapan_charger_id'])) {
            $c = $this->componentHelper->getChargerByInventoryId($spec['persiapan_charger_id']);
                
            if ($c) {
                $enriched['selected']['charger'] = [
                    'id' => (int)$c['id_inventory_attachment'],
                    'merk_charger' => $c['merk_charger'] ?? null,
                    'tipe_charger' => $c['tipe_charger'] ?? null,
                    'sn_charger' => $c['sn_charger'] ?? null,
                    'lokasi_penyimpanan' => $c['lokasi_penyimpanan'] ?? null,
                    // Format: Merk Tipe (SN)
                    'sn_charger_formatted' => !empty($c['sn_charger']) ? 
                        trim(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? '')) . ' (SN: ' . $c['sn_charger'] . ')' : 
                        trim(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? '')),
                ];
                
                // Override spesifikasi with charger data
                $enriched['charger_model'] = trim(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? ''));
            }
        }
        
        // Load attachment data from JSON spesifikasi if not loaded from fabrikasi_attachment_id
        if (empty($enriched['selected']['attachment']) && !empty($spec['fabrikasi_attachment_id'])) {
            $a = $this->componentHelper->getAttachmentByInventoryId($spec['fabrikasi_attachment_id']);
                
            if ($a) {
                $enriched['selected']['attachment'] = [
                    'id' => (int)$a['id_inventory_attachment'],
                    'tipe' => $a['tipe'] ?? null,
                    'merk' => $a['merk'] ?? null,
                    'model' => $a['model'] ?? null,
                    'sn_attachment' => $a['sn_attachment'] ?? null,
                    'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    // Format: Tipe Merk Model (SN)
                    'sn_attachment_formatted' => !empty($a['sn_attachment']) ? 
                        trim(($a['tipe'] ?? '') . ' ' . ($a['merk'] ?? '') . ' ' . ($a['model'] ?? '')) . ' (SN: ' . $a['sn_attachment'] . ')' : 
                        trim(($a['tipe'] ?? '') . ' ' . ($a['merk'] ?? '') . ' ' . ($a['model'] ?? '')),
                ];
                
                // Override spesifikasi with attachment data
                $enriched['attachment_tipe'] = trim(($a['tipe'] ?? '') . ' ' . ($a['merk'] ?? '') . ' ' . ($a['model'] ?? ''));
            }
        }
        
        // Legacy: enrich selected items from spesifikasi (fallback if no approval workflow data)
        if (!empty($spec['selected']) && is_array($spec['selected'])) {
            $sel = $spec['selected'];
            if (empty($enriched['selected'])) $enriched['selected'] = [];
            
            // Only load legacy unit data if no approval workflow data exists
            if (empty($enriched['selected']['unit']) && !empty($sel['unit_id'])) {
                $u = $this->unitModel
                    ->select('inventory_unit.no_unit, inventory_unit.serial_number, inventory_unit.tahun_unit, inventory_unit.lokasi_unit, inventory_unit.sn_mast, inventory_unit.sn_mesin, inventory_unit.sn_baterai, inventory_unit.sn_charger, mu.merk_unit, mu.model_unit')
                    ->join('model_unit mu','mu.id_model_unit = inventory_unit.model_unit_id','left')
                    ->where('inventory_unit.id_inventory_unit', (int)$sel['unit_id'])
                    ->first();
                if ($u) {
                    $enriched['selected']['unit'] = [
                        'id' => (int)$sel['unit_id'],
                        'no_unit' => $u['no_unit'] ?? null,
                        'serial_number' => $u['serial_number'] ?? null,
                        'tahun_unit' => $u['tahun_unit'] ?? null,
                        'merk_unit' => $u['merk_unit'] ?? null,
                        'model_unit' => $u['model_unit'] ?? null,
                        'lokasi_unit' => $u['lokasi_unit'] ?? null,
                        'sn_mast' => $u['sn_mast'] ?? null,
                        'sn_mesin' => $u['sn_mesin'] ?? null,
                        'sn_baterai' => $u['sn_baterai'] ?? null,
                        'sn_charger' => $u['sn_charger'] ?? null,
                    ];
                }
            }
            
            // Only load legacy attachment data if no approval workflow data exists
            if (empty($enriched['selected']['attachment']) && !empty($sel['inventory_attachment_id'])) {
                $a = $this->attModel
                    ->select('a.tipe, a.merk, a.model, inventory_attachments.serial_number as sn_attachment, inventory_attachments.storage_location as lokasi_penyimpanan')
                    ->join('attachment a','a.id_attachment = inventory_attachments.attachment_type_id','left')
                    ->where('inventory_attachments.id', (int)$sel['inventory_attachment_id'])
                    ->first();
                if ($a) {
                    $enriched['selected']['attachment'] = [
                        'tipe' => $a['tipe'] ?? null,
                        'merk' => $a['merk'] ?? null,
                        'model' => $a['model'] ?? null,
                        'sn_attachment' => $a['sn_attachment'] ?? null,
                        'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    ];
                }
            }
        }
        
        // Add stage_status data for print view
        $stageStatus = $this->getSpkStageStatusData($id);
        $row['stage_status'] = $stageStatus;

        // Resolve SPK creator name from users table (for Marketing signature section)
        $creatorUserId = $row['dibuat_oleh'] ?? $row['created_by'] ?? null;
        if (!empty($creatorUserId)) {
            $creator = $this->db->table('users')
                ->select("id, username, TRIM(CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,''))) AS full_name")
                ->where('id', (int)$creatorUserId)
                ->get()->getRowArray();
            if ($creator) {
                $creatorName = trim($creator['full_name'] ?? '');
                if ($creatorName === '') {
                    $creatorName = $creator['username'] ?? '';
                }
                $row['created_by_name'] = $creatorName;
            }
        }

        // Process prepared units data for print view
        $preparedUnitsDetail = $this->getPreparedUnitsDetail($id, $stageStatus);
        $row['prepared_units_detail'] = $preparedUnitsDetail;
        
        return view('marketing/print_spk', ['spk'=>$row, 'spesifikasi'=>$enriched, 'kontrak_spesifikasi'=>$kontrak_spec]);
    }

    /**
     * Get prepared units detail for print view
     */
    private function getPreparedUnitsDetail($spkId, $stageStatus)
    {
        $preparedList = [];
        
        if (isset($stageStatus['unit_stages'])) {
            foreach ($stageStatus['unit_stages'] as $unitIndex => $unitStages) {
                if (isset($unitStages['persiapan_unit']) && $unitStages['persiapan_unit']['completed']) {
                    // Get unit details from persiapan_unit stage
                    $unitData = $unitStages['persiapan_unit'] ?? [];
                    $unitId = $unitData['unit_id'] ?? null;
                    
                    // Get unit details from inventory_unit with joins
                    $unitDetails = null;
                    $isInActiveDI = false;
                    $activeDIInfo = null;
                    
                    if ($unitId) {
                        $unitDetails = $this->db->table('inventory_unit iu')
                            ->select('iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit, tu.tipe as jenis_unit, tu.jenis as jenis_unit_type, k.kapasitas_unit as kapasitas_name, tm.tipe_mast as mast_name, jr.tipe_roda as roda_name, tb.tipe_ban as ban_name, v.jumlah_valve as valve_name, d.nama_departemen as departemen_name')
                            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                            ->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left')
                            ->join('tipe_mast tm', 'tm.id_mast = iu.model_mast_id', 'left')
                            ->join('jenis_roda jr', 'jr.id_roda = iu.roda_id', 'left')
                            ->join('tipe_ban tb', 'tb.id_ban = iu.ban_id', 'left')
                            ->join('valve v', 'v.id_valve = iu.valve_id', 'left')
                            ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
                            ->where('iu.id_inventory_unit', $unitId)
                            ->get()
                            ->getRowArray();
                        
                        // Check if unit is already in active DI (not SAMPAI_LOKASI or SELESAI)
                        $activeDI = $this->db->query("
                            SELECT di.nomor_di, di.status_di, di.pelanggan
                            FROM delivery_items di_items
                            INNER JOIN delivery_instructions di ON di.id = di_items.di_id
                            WHERE di_items.unit_id = ?
                            AND di.status_di NOT IN ('SAMPAI_LOKASI', 'SELESAI', 'DIBATALKAN')
                            AND di_items.item_type = 'UNIT'
                            LIMIT 1
                        ", [$unitId])->getRowArray();
                        
                        if (!$activeDI) {
                            // No fallback needed - delivery_items table already checked above
                            log_message('debug', "No active DI found for unit {$unitId}");
                        }
                        
                        if ($activeDI) {
                            $isInActiveDI = true;
                            $activeDIInfo = $activeDI;
                        }
                    }
                    
                    // Get attachment details from inventory_attachment (OBSOLETE - overwritten below)
                    // This code is replaced by individual component queries below
                    $attachmentDetails = null;
                    
                    // Get battery and charger details from spk_unit_stages with full names
                    $batteryDetails = null;
                    $chargerDetails = null;
                    $attachmentDetails = null;
                    
                    // Get battery and charger from persiapan_unit stage
                    if (isset($unitStages['persiapan_unit'])) {
                        $persiapanData = $unitStages['persiapan_unit'];
                        $batteryId = $persiapanData['battery_inventory_attachment_id'] ?? null;
                        $chargerId = $persiapanData['charger_inventory_attachment_id'] ?? null;
                        
                        if ($batteryId) {
                            $batteryDetails = $this->componentHelper->getBatteryByInventoryId($batteryId);
                        }
                        
                        if ($chargerId) {
                            $chargerDetails = $this->componentHelper->getChargerByInventoryId($chargerId);
                        }
                    }
                    
                    // Get attachment from fabrikasi stage
                    if (isset($unitStages['fabrikasi'])) {
                        $fabrikasiData = $unitStages['fabrikasi'];
                        $attachmentId = $fabrikasiData['attachment_inventory_attachment_id'] ?? null;
                        
                        if ($attachmentId) {
                            $attachmentDetails = $this->componentHelper->getAttachmentByInventoryId($attachmentId);
                        }
                    }
                    
                    // Combine notes from all stages
                    $combinedNotes = [];
                    $stageNames = [
                        'persiapan_unit' => 'Persiapan Unit',
                        'fabrikasi' => 'Fabrikasi', 
                        'painting' => 'Painting',
                        'pdi' => 'PDI'
                    ];
                    
                    foreach ($stageNames as $stageKey => $stageName) {
                        if (isset($unitStages[$stageKey]) && !empty($unitStages[$stageKey]['catatan'])) {
                            $combinedNotes[] = $stageName . ': ' . $unitStages[$stageKey]['catatan'];
                        }
                    }
                    
                    // Format No Unit: [no_unit] (SN: [serial_number])
                    $noUnitFormatted = '';
                    if ($unitDetails['no_unit']) {
                        $noUnitFormatted = $unitDetails['no_unit'];
                        if ($unitDetails['serial_number']) {
                            $noUnitFormatted .= ' (SN: ' . $unitDetails['serial_number'] . ')';
                        }
                    } else {
                        $noUnitFormatted = 'Unit-' . $unitId;
                    }
                    
                    // Format Jenis Unit: [jenis] - [merk] ([model])
                    $jenisUnitFormatted = '';
                    if ($unitDetails['jenis_unit_type']) {
                        $jenisUnitFormatted = $unitDetails['jenis_unit_type'];
                        if ($unitDetails['merk_unit'] && $unitDetails['model_unit']) {
                            $jenisUnitFormatted .= ' - ' . $unitDetails['merk_unit'] . ' (' . $unitDetails['model_unit'] . ')';
                        } elseif ($unitDetails['merk_unit']) {
                            $jenisUnitFormatted .= ' - ' . $unitDetails['merk_unit'];
                        }
                    } else {
                        $jenisUnitFormatted = '-';
                    }
                    
                    // Format Charger: [merk] [tipe] (SN: [sn])
                    $chargerFormatted = '';
                    if ($chargerDetails && $chargerDetails['merk_charger'] && $chargerDetails['tipe_charger']) {
                        $chargerFormatted = $chargerDetails['merk_charger'] . ' ' . $chargerDetails['tipe_charger'];
                        if ($chargerDetails['sn_charger']) {
                            $chargerFormatted .= ' (SN: ' . $chargerDetails['sn_charger'] . ')';
                        }
                    } else {
                        $chargerFormatted = '-';
                    }
                    
                    // Format Baterai: [merk] [tipe] [jenis] (SN: [sn])
                    $bateraiFormatted = '';
                    if ($batteryDetails && $batteryDetails['merk_baterai'] && $batteryDetails['tipe_baterai']) {
                        $bateraiFormatted = $batteryDetails['merk_baterai'] . ' ' . $batteryDetails['tipe_baterai'];
                        if ($batteryDetails['jenis_baterai']) {
                            $bateraiFormatted .= ' ' . $batteryDetails['jenis_baterai'];
                        }
                        if ($batteryDetails['sn_baterai']) {
                            $bateraiFormatted .= ' (SN: ' . $batteryDetails['sn_baterai'] . ')';
                        }
                    } else {
                        $bateraiFormatted = '-';
                    }
                    
                    // Format Attachment: [merk] - [model] [tipe] (SN: [sn])
                    $attachmentFormatted = '';
                    if ($attachmentDetails && $attachmentDetails['merk'] && $attachmentDetails['model']) {
                        $attachmentFormatted = $attachmentDetails['merk'] . ' - ' . $attachmentDetails['model'];
                        if ($attachmentDetails['tipe']) {
                            $attachmentFormatted .= ' ' . $attachmentDetails['tipe'];
                        }
                        if ($attachmentDetails['sn_attachment']) {
                            $attachmentFormatted .= ' (SN: ' . $attachmentDetails['sn_attachment'] . ')';
                        }
                    } else {
                        $attachmentFormatted = '-';
                    }
                    
                    $preparedList[] = [
                        'unit_index' => $unitIndex,
                        'unit_id' => $unitId,
                        'no_unit' => $noUnitFormatted,
                        'serial_number' => $unitDetails['serial_number'] ?? '',
                        'jenis_unit' => $jenisUnitFormatted,
                        'departemen_name' => $unitDetails['departemen_name'] ?? '',
                        'kapasitas_name' => $unitDetails['kapasitas_name'] ?? '',
                        'mast_name' => $unitDetails['mast_name'] ?? '',
                        'roda_name' => $unitDetails['roda_name'] ?? '',
                        'ban_name' => $unitDetails['ban_name'] ?? '',
                        'valve_name' => $unitDetails['valve_name'] ?? '',
                        'baterai_sn' => $bateraiFormatted,
                        'charger_sn' => $chargerFormatted,
                        'attachment_sn' => $attachmentFormatted,
                        'aksesoris' => $this->formatAksesoris($unitData['aksesoris_tersedia'] ?? ''),
                        'combined_notes' => implode(' | ', $combinedNotes),
                        'is_in_active_di' => $isInActiveDI,
                        'active_di_info' => $activeDIInfo
                    ];
                }
            }
        }
        
        return $preparedList;
    }

    /**
     * Format aksesoris to remove quotes and brackets
     */
    private function formatAksesoris($aksesoris)
    {
        if (is_string($aksesoris)) {
            // If it's a JSON string, decode it first
            $decoded = json_decode($aksesoris, true);
            if (is_array($decoded)) {
                return implode(', ', $decoded);
            }
            return $aksesoris;
        } elseif (is_array($aksesoris)) {
            return implode(', ', $aksesoris);
        }
        return $aksesoris;
    }

    /**
     * Get SPK stage status data for internal use (returns array, not Response)
     */
    private function getSpkStageStatusData($spkId)
    {
        try {
            $spk = $this->db->table('spk')->where('id', $spkId)->get()->getRowArray();
            if (!$spk) {
                return [];
            }

            $totalUnits = (int) $spk['jumlah_unit'];
            $unitStages = [];

            // Get stage data for each unit
            for ($unitIndex = 1; $unitIndex <= $totalUnits; $unitIndex++) {
                $stages = $this->db->table('spk_unit_stages sus')
                    ->select('sus.stage_name, sus.tanggal_approve, sus.mekanik, sus.catatan, sus.unit_id, sus.area_id, sus.aksesoris_tersedia, sus.battery_inventory_attachment_id, sus.charger_inventory_attachment_id, sus.attachment_inventory_attachment_id')
                    ->where('sus.spk_id', $spkId)
                    ->where('sus.unit_index', $unitIndex)
                    ->orderBy('sus.stage_name')
                    ->get()
                    ->getResultArray();

                $stageStatus = [];
                foreach ($stages as $stage) {
                    $stageStatus[$stage['stage_name']] = [
                        'completed' => !empty($stage['tanggal_approve']),
                        'mekanik' => $stage['mekanik'] ?? null,
                        'catatan' => $stage['catatan'] ?? null,
                        'tanggal_approve' => $stage['tanggal_approve'] ?? null,
                        'unit_id' => $stage['unit_id'] ?? null,
                        'area_id' => $stage['area_id'] ?? null,
                        'aksesoris_tersedia' => $stage['aksesoris_tersedia'] ?? null,
                        'battery_inventory_attachment_id' => $stage['battery_inventory_attachment_id'] ?? null,
                        'charger_inventory_attachment_id' => $stage['charger_inventory_attachment_id'] ?? null,
                        'attachment_inventory_attachment_id' => $stage['attachment_inventory_attachment_id'] ?? null
                    ];
                }

                $unitStages[$unitIndex] = $stageStatus;
            }

            return [
                'unit_stages' => $unitStages
            ];
        } catch (\Exception $e) {
            log_message('error', 'SPK Stage Status Error: ' . $e->getMessage());
            return [];
        }
    }

    // --- SPK Minimal APIs for integrated workflow ---
    public function spkList()
    {
        $builder = $this->spkModel->builder();
        
        // Join with quotation_specifications and quotations to get quotation_number
        $builder->select('spk.*, qs.id_quotation, q.quotation_number')
            ->join('quotation_specifications qs', 'qs.id_specification = spk.quotation_specification_id', 'left')
            ->join('quotations q', 'q.id_quotation = qs.id_quotation', 'left');
        
        // Apply date filter if provided (supports both GET and POST)
        $hasFilter = $this->hasDateFilter();
        log_message('info', 'SPK List - Date Filter: ' . ($hasFilter ? 'YES (' . $this->getDateFilterParams()['start'] . ' to ' . $this->getDateFilterParams()['end'] . ')' : 'NO'));
        
        if ($hasFilter) {
            $this->applyDateFilter($builder, 'spk.created_at');
        }
        
        $data = $builder->orderBy('spk.id','DESC')->get()->getResultArray();
        
        log_message('info', 'SPK List - Returned ' . count($data) . ' records');
        
        return $this->response->setJSON(['data'=>$data,'csrf_hash'=>csrf_hash()]);
    }

    /**
     * DataTables endpoint for Marketing SPK (server-side processing)
     */
    public function spkData()
    {
        $request = $this->request;
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 25;
        $search = $request->getPost('search')['value'] ?? '';
        $statusFilter = $request->getPost('status_filter') ?? 'all';
        
        $builder = $this->spkModel->builder();
        
        // Join with quotations for quotation_number
        $builder->select('spk.*, qs.id_quotation, q.quotation_number')
            ->join('quotation_specifications qs', 'qs.id_specification = spk.quotation_specification_id', 'left')
            ->join('quotations q', 'q.id_quotation = qs.id_quotation', 'left');
        
        // Apply status filter (tab filtering)
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'COMPLETED') {
                $builder->whereIn('spk.status', ['COMPLETED', 'DELIVERED']);
            } else {
                $builder->where('spk.status', $statusFilter);
            }
        }
        
        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('spk.nomor_spk', $search)
                ->orLike('spk.pelanggan', $search)
                ->orLike('spk.po_kontrak_nomor', $search)
                ->orLike('spk.pic', $search)
                ->orLike('spk.kontak', $search)
                ->orLike('spk.jenis_spk', $search)
                ->groupEnd();
        }
        
        // Get total filtered count before pagination
        $totalFiltered = $builder->countAllResults(false);
        
        // Apply sorting (default: latest first)
        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';
        $columns = ['spk.nomor_spk', 'spk.jenis_spk', 'spk.po_kontrak_nomor', 'spk.kontrak_id', 'spk.pelanggan', 'spk.pic', 'spk.kontak', 'spk.status', 'spk.jumlah_unit'];
        
        if (isset($columns[$orderColumnIndex])) {
            $builder->orderBy($columns[$orderColumnIndex], $orderDir);
        } else {
            $builder->orderBy('spk.id', 'DESC');
        }
        
        // Apply pagination
        $data = $builder->limit($length, $start)->get()->getResultArray();
        
        // Get total count (before any filters)
        $totalRecords = $this->spkModel->countAll();
        
        log_message('info', 'SPK DataTables - Draw: ' . $draw . ', Total: ' . $totalRecords . ', Filtered: ' . $totalFiltered . ', Returned: ' . count($data));
        
        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    /**
     * Statistics endpoint for Marketing SPK
     */
    public function spkStats()
    {
        $statusFilter = $this->request->getPost('status_filter') ?? 'all';

        // Single optimized query to get all counts at once
        $result = $this->db->query("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'READY' THEN 1 ELSE 0 END) as ready,
                SUM(CASE WHEN status IN ('COMPLETED', 'DELIVERED') THEN 1 ELSE 0 END) as completed
            FROM spk
        ")->getRowArray();

        $total = (int) $result['total'];

        // Apply filter to get filtered total
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'COMPLETED') {
                $total = $this->spkModel->whereIn('status', ['COMPLETED', 'DELIVERED'])->countAllResults();
            } else {
                $total = $this->spkModel->where('status', $statusFilter)->countAllResults();
            }
        }

        return $this->response->setJSON([
            'total' => $total,
            'in_progress' => (int) ($result['in_progress'] ?? 0),
            'ready' => (int) ($result['ready'] ?? 0),
            'completed' => (int) ($result['completed'] ?? 0)
        ]);
    }

    public function spkDetail($id)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized: Please login first'
                ]);
            }
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $row = $this->spkModel->find((int)$id);
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']);
        }
        $spec = [];
        if (!empty($row['spesifikasi'])) {
            $decoded = json_decode($row['spesifikasi'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $spec = $decoded;
            }
        }
        // Enrich human-readable names for common IDs
        $enriched = $spec;
        $mapQueries = [
            'departemen_id' => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen'],
            'kapasitas_id'  => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit'],
            'mast_id'       => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast'],
            'ban_id'        => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban'],
            'valve_id'      => ['table'=>'valve','id'=>'id_valve','name'=>'jumlah_valve'],
            'roda_id'       => ['table'=>'jenis_roda','id'=>'id_roda','name'=>'tipe_roda'],
            'charger_id'    => ['table'=>'charger','id'=>'id_charger','name'=>'tipe_charger'],
        ];
        foreach ($mapQueries as $key => $cfg) {
            if (!empty($spec[$key])) {
                $val = $spec[$key];
                $rec = $this->db->table($cfg['table'])->select($cfg['name'].' as name', false)->where($cfg['id'], $val)->get()->getRowArray();
                if ($rec && isset($rec['name'])) {
                    $enriched[$key.'_name'] = $rec['name'];
                }
            }
        }
        // Enrich selected items (unit & attachment) labels and details if present
        if (!empty($spec['selected']) && is_array($spec['selected'])) {
            $sel = $spec['selected'];
            $enriched['selected'] = $sel;
            // Unit label
            if (!empty($sel['unit_id'])) {
                $u = $this->unitModel
                    ->select('inventory_unit.no_unit, inventory_unit.serial_number, inventory_unit.tahun_unit, inventory_unit.lokasi_unit, inventory_unit.sn_mast, inventory_unit.sn_mesin, inventory_unit.sn_baterai, inventory_unit.sn_charger, mu.merk_unit, mu.model_unit')
                    ->join('model_unit mu','mu.id_model_unit = inventory_unit.model_unit_id','left')
                    ->where('inventory_unit.id_inventory_unit', (int)$sel['unit_id'])
                    ->first();
                if ($u) {
                    $label = trim(($u['no_unit'] ?: '-') . ' - ' . ($u['merk_unit'] ?: '-') . ' ' . ($u['model_unit'] ?: '') . ' @ ' . ($u['lokasi_unit'] ?: '-'));
                    $enriched['selected']['unit'] = [
                        'id' => (int)$sel['unit_id'],
                        'label' => $label,
                        'no_unit' => $u['no_unit'] ?? null,
                        'serial_number' => $u['serial_number'] ?? null,
                        'tahun_unit' => $u['tahun_unit'] ?? null,
                        'merk_unit' => $u['merk_unit'] ?? null,
                        'model_unit' => $u['model_unit'] ?? null,
                        'lokasi_unit' => $u['lokasi_unit'] ?? null,
                        'sn_mast' => $u['sn_mast'] ?? null,
                        'sn_mesin' => $u['sn_mesin'] ?? null,
                        'sn_baterai' => $u['sn_baterai'] ?? null,
                        'sn_charger' => $u['sn_charger'] ?? null,
                    ];
                }
            }
            // Attachment label from inventory_attachments
            if (!empty($sel['inventory_attachment_id'])) {
                $a = $this->attModel
                    ->select('a.tipe, a.merk, a.model, inventory_attachments.serial_number as sn_attachment, inventory_attachments.storage_location as lokasi_penyimpanan')
                    ->join('attachment a','a.id_attachment = inventory_attachments.attachment_type_id','left')
                    ->where('inventory_attachments.id', (int)$sel['inventory_attachment_id'])
                    ->first();
                if ($a) {
                    $label = trim(($a['tipe'] ?: '-') . ' ' . ($a['merk'] ?: '') . ' ' . ($a['model'] ?: ''));
                    $suffix = [];
                    if (!empty($a['sn_attachment'])) $suffix[] = 'SN: '.$a['sn_attachment'];
                    if (!empty($a['lokasi_penyimpanan'])) $suffix[] = '@ '.$a['lokasi_penyimpanan'];
                    if ($suffix) $label .= ' ['.implode(', ', $suffix).']';
                    $enriched['selected']['attachment'] = [
                        'id' => (int)$sel['inventory_attachment_id'],
                        'label' => $label,
                        'tipe' => $a['tipe'] ?? null,
                        'merk' => $a['merk'] ?? null,
                        'model' => $a['model'] ?? null,
                        'sn_attachment' => $a['sn_attachment'] ?? null,
                        'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    ];
                }
            }
        }

        // Also expose prepared_units and enrich them as prepared_units_detail for multi-unit READY SPK
        $preparedUnits = [];
        if (!empty($spec['prepared_units']) && is_array($spec['prepared_units'])) {
            $preparedUnits = $spec['prepared_units'];
            $preparedDetails = [];
            foreach ($preparedUnits as $pu) {
                $uInfo = null; $aInfo = null; $unitLabel=''; $attLabel='';
                if (!empty($pu['unit_id'])) {
                    $uInfo = $this->db->table('inventory_unit iu')
                        ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.lokasi_unit, mu.merk_unit, mu.model_unit, tu.tipe as tipe_jenis')
                        ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                        ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
                        ->where('iu.id_inventory_unit', $pu['unit_id'])
                        ->get()->getRowArray();
                    if ($uInfo) {
                        $unitLabel = trim(($uInfo['no_unit'] ?: '-') . ' - ' . ($uInfo['merk_unit'] ?: '-') . ' ' . ($uInfo['model_unit'] ?: '') . ' @ ' . ($uInfo['lokasi_unit'] ?: '-'));
                    }
                }
                if (!empty($pu['attachment_id'])) {
                    $aInfo = $this->componentHelper->getAttachmentByInventoryId($pu['attachment_id']);
                    if ($aInfo) {
                        $attLabel = trim(($aInfo['tipe'] ?: '-') . ' ' . ($aInfo['merk'] ?: '') . ' ' . ($aInfo['model'] ?: ''));
                        $suf = [];
                        if (!empty($aInfo['sn_attachment'])) $suf[] = 'SN: '.$aInfo['sn_attachment'];
                        if (!empty($aInfo['lokasi_penyimpanan'])) $suf[] = '@ '.$aInfo['lokasi_penyimpanan'];
                        if ($suf) $attLabel .= ' ['.implode(', ', $suf).']';
                }
                }
                $preparedDetails[] = [
                    'unit_id' => $pu['unit_id'] ?? null,
                    'unit_label' => $unitLabel,
                    'no_unit' => $uInfo['no_unit'] ?? '',
                    'serial_number' => $uInfo['serial_number'] ?? '',
                    'merk_unit' => $uInfo['merk_unit'] ?? '',
                    'model_unit' => $uInfo['model_unit'] ?? '',
                    'tipe_jenis' => $uInfo['tipe_jenis'] ?? '',
                    'attachment_id' => $pu['attachment_id'] ?? null,
                    'attachment_label' => $attLabel,
                    'mekanik' => $pu['mekanik'] ?? '',
                    'aksesoris_tersedia' => $pu['aksesoris_tersedia'] ?? '',
                    'catatan' => $pu['catatan'] ?? '',
                    'timestamp' => $pu['timestamp'] ?? ''
                ];
            }
            $enriched['prepared_units_detail'] = $preparedDetails;
        }
        // Get actual prepared units from spk_unit_stages (new workflow)
        $stageStatus = $this->getSpkStageStatusData($id);
        $preparedUnitsFromStages = $this->getPreparedUnitsDetail($id, $stageStatus);
        
        // If we have prepared units from stages, map to expected format
        if (!empty($preparedUnitsFromStages)) {
            $mappedPreparedUnits = [];
            foreach ($preparedUnitsFromStages as $unit) {
                $mappedPreparedUnits[] = [
                    'unit_id' => $unit['unit_id'],
                    'unit_label' => $unit['no_unit'] ?? '-',
                    'no_unit' => $unit['no_unit'] ?? '-',
                    'serial_number' => $unit['serial_number'] ?? '-',
                    'merk_unit' => '',  // Will be extracted from jenis_unit
                    'model_unit' => '', // Will be extracted from jenis_unit
                    'tipe_jenis' => $unit['jenis_unit'] ?? '-',
                    'attachment_label' => $unit['attachment_sn'] ?? '-',
                    'mekanik' => $unit['departemen_name'] ?? '-', // Using available data
                    'catatan' => $unit['combined_notes'] ?? '',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'aksesoris_tersedia' => $unit['aksesoris'] ?? '',
                    // Additional fields from new structure
                    'kapasitas_name' => $unit['kapasitas_name'] ?? '-',
                    'mast_name' => $unit['mast_name'] ?? '-',
                    'roda_name' => $unit['roda_name'] ?? '-',
                    'ban_name' => $unit['ban_name'] ?? '-',
                    'valve_name' => $unit['valve_name'] ?? '-',
                    'baterai_sn' => $unit['baterai_sn'] ?? '-',
                    'charger_sn' => $unit['charger_sn'] ?? '-'
                ];
            }
            $enriched['prepared_units_detail'] = $mappedPreparedUnits;
        }
        
        // Get quotation_specifications data if available (new system)
        $kontrak_spec = null;
        if (!empty($row['quotation_specification_id'])) {
            $kontrak_spec = $this->db->table('quotation_specifications')
                ->where('id_specification', $row['quotation_specification_id'])
                ->get()
                ->getRowArray();
                
            // Process unit_accessories if it's stored as JSON or CSV
            if ($kontrak_spec && isset($kontrak_spec['unit_accessories']) && !empty($kontrak_spec['unit_accessories'])) {
                $accessories_raw = trim($kontrak_spec['unit_accessories']);
                
                // Try JSON first
                try {
                    $decoded_aksesoris = json_decode($accessories_raw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_aksesoris)) {
                        $kontrak_spec['aksesoris'] = $decoded_aksesoris;
                    } else {
                        // Not JSON, treat as CSV string
                        $kontrak_spec['aksesoris'] = array_map('trim', explode(',', $accessories_raw));
                    }
                } catch (\Exception $e) {
                    // Treat as CSV string
                    $kontrak_spec['aksesoris'] = array_map('trim', explode(',', $accessories_raw));
                }
            }
            
            // Enrich kontrak_spec with human-readable names
            if ($kontrak_spec) {
                $kontrakEnrichMap = [
                    'departemen_id' => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen'],
                    'tipe_unit_id'  => ['table'=>'tipe_unit','id'=>'id_tipe_unit','name'=>'jenis'],
                    'brand_id'      => ['table'=>'model_unit','id'=>'id_model_unit','name'=>'merk_unit'],
                    'kapasitas_id'  => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit'],
                    'mast_id'       => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast'],
                    'ban_id'        => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban'],
                    'valve_id'      => ['table'=>'valve','id'=>'id_valve','name'=>'jumlah_valve'],
                    'roda_id'       => ['table'=>'jenis_roda','id'=>'id_roda','name'=>'tipe_roda'],
                    'charger_id'    => ['table'=>'charger','id'=>'id_charger','name'=>'tipe_charger'],
                    'battery_id'    => ['table'=>'baterai','id'=>'id','name'=>'jenis_baterai'],
                    'attachment_id' => ['table'=>'attachment','id'=>'id_attachment','name'=>'tipe'],
                ];
                
                foreach ($kontrakEnrichMap as $key => $cfg) {
                    if (!empty($kontrak_spec[$key])) {
                        $val = $kontrak_spec[$key];
                        $rec = $this->db->table($cfg['table'])
                            ->select($cfg['name'].' as name', false)
                            ->where($cfg['id'], $val)
                            ->get()
                            ->getRowArray();
                        if ($rec && isset($rec['name'])) {
                            $kontrak_spec[$key.'_name'] = $rec['name'];
                        }
                    }
                }
            }
        }
        
        // Fetch customer_id from associated contract for DI creation
        $customerId = null;
        // 1. Try via kontrak_id (SPK linked to contract)
        if (!empty($row['kontrak_id'])) {
            $kontrakRow = $this->db->table('kontrak')->select('customer_id')->where('id', (int)$row['kontrak_id'])->get()->getRowArray();
            if ($kontrakRow && !empty($kontrakRow['customer_id'])) {
                $customerId = (int)$kontrakRow['customer_id'];
            }
        }
        // 2. Fallback: try via po_kontrak_nomor
        if (!$customerId && !empty($row['po_kontrak_nomor'])) {
            $kontrakByNo = $this->db->table('kontrak')->select('customer_id')
                ->where('no_kontrak', $row['po_kontrak_nomor'])
                ->orWhere('customer_po_number', $row['po_kontrak_nomor'])
                ->limit(1)->get()->getRowArray();
            if ($kontrakByNo && !empty($kontrakByNo['customer_id'])) {
                $customerId = (int)$kontrakByNo['customer_id'];
            }
        }
        // 3. Fallback: try via pelanggan name
        if (!$customerId && !empty($row['pelanggan'])) {
            $customerByName = $this->db->table('customers')->select('id')
                ->where('customer_name', $row['pelanggan'])
                ->where('deleted_at IS NULL', null, false)
                ->limit(1)->get()->getRowArray();
            if ($customerByName) {
                $customerId = (int)$customerByName['id'];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $row,
            'customer_id' => $customerId, // For loading customer locations in DI modal
            'jenis_spk' => $row['jenis_spk'] ?? 'UNIT', // Explicitly include SPK type for frontend
            'spesifikasi' => $enriched,
            'prepared_units' => $preparedUnits,
            'prepared_units_detail' => $enriched['prepared_units_detail'] ?? [],
            'kontrak_spec' => $kontrak_spec,
            'stage_status' => $stageStatus, // Include stage status data
            'csrf_hash' => csrf_hash(),
            'debug' => ENVIRONMENT === 'development' ? [
                'stageStatus_count' => count($stageStatus['unit_stages'] ?? []),
                'preparedUnitsFromStages_count' => count($preparedUnitsFromStages),
                'enriched_prepared_units_detail_count' => count($enriched['prepared_units_detail'] ?? [])
            ] : null
        ]);
    }

    // Get units registered in a contract for ATTACHMENT SPK
    public function kontrakUnits($kontrakId)
    {
        try {
            log_message('debug', '=== kontrakUnits START === Kontrak ID: ' . $kontrakId);
            
            // Get all units associated with this contract
            $units = $this->db->table('inventory_unit iu')
                ->select('
                    iu.id_inventory_unit, 
                    iu.serial_number, 
                    iu.no_unit,
                    iu.tipe_unit_id,
                    iu.kapasitas_unit_id,
                    CONCAT(tu.tipe, " ", tu.jenis) as tipe_jenis,
                    tu.tipe,
                    tu.jenis,
                    k.kapasitas_unit as kapasitas,
                    mu.merk_unit, 
                    mu.model_unit, 
                    iu.status_unit_id,
                    su.id_status,
                    su.status_unit,
                    iu.lokasi_unit,
                    COALESCE(cl.location_name, iu.lokasi_unit, "Lokasi Belum Ditentukan") as lokasi,
                    cl.address as alamat,
                    cl.id as customer_location_id,
                    iu.harga_sewa_bulanan,
                    iu.tahun_pembuatan,
                    iu.catatan
                ')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
                ->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left')
                // Updated: JOIN via kontrak_unit junction table, location via ku.customer_location_id
                ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit', 'left')
                ->join('kontrak kt', 'kt.id = ku.kontrak_id', 'left')
                ->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left')
                ->where('ku.kontrak_id', $kontrakId)
                ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->orderBy('cl.location_name', 'ASC')
                ->orderBy('iu.serial_number', 'ASC')
                ->get()
                ->getResultArray();
            
            log_message('debug', 'Units Found: ' . count($units));
            
            if (count($units) > 0) {
                log_message('debug', 'First unit sample: ' . json_encode($units[0]));
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $units,
                'count' => count($units),
                'kontrak_id' => $kontrakId,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    // Get customer locations for dropdown
    public function customerLocations($customerId)
    {
        try {
            $locations = $this->db->table('customer_locations')
                ->where('customer_id', $customerId)
                ->orderBy('location_name', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $locations,
                'count' => count($locations),
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    // Provide kontrak options (ACTIVE/PENDING) for searchable dropdown
    public function kontrakOptions()
    {
        $q = trim($this->request->getGet('q') ?? '');
        $status = trim($this->request->getGet('status') ?? 'PENDING');
        
        try {
            // Use database query builder with proper JOINs
            $builder = $this->db->table('kontrak k');
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            // Use LEFT JOIN because not all kontrak have quotation_specifications with kontrak_id
            $builder->join('quotation_specifications qs', 'qs.kontrak_id = k.id', 'left');

            // Fixed: Remove cl.location_name from GROUP BY (it's from subquery, not a JOIN)
            $builder->select('k.id, k.no_kontrak, k.customer_po_number, k.rental_type, c.customer_name as pelanggan, (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as lokasi');
            $builder->whereIn('k.status', ['ACTIVE', 'PENDING']);
            $builder->groupBy('k.id, k.no_kontrak, k.customer_po_number, k.rental_type, c.customer_name');
            
            if ($q !== '') {
                $builder->groupStart()
                    ->like('k.no_kontrak', $q)
                    ->orLike('k.customer_po_number', $q)
                    ->orLike('c.customer_name', $q)
                ->groupEnd();
            }
            
            $rows = $builder->orderBy('k.dibuat_pada', 'DESC')->limit(20)->get()->getResultArray();
            
            // map to simple text for display if needed
            $options = array_map(function($r){
                $label = trim(($r['no_kontrak'] ?: '') . ' ' . ($r['customer_po_number'] ? '(' . $r['customer_po_number'] . ')' : '') . ' - ' . ($r['pelanggan'] ?: '-'));
                return [
                    'id' => (int)$r['id'],
                    'no_kontrak' => $r['no_kontrak'],
                    'customer_po_number' => $r['customer_po_number'],
                    'rental_type' => $r['rental_type'],
                    'pelanggan' => $r['pelanggan'],
                    'lokasi' => $r['lokasi'] ?? '-',
                    'label' => $label
                ];
            }, $rows);
            
            return $this->response->setJSON(['success' => true, 'data'=>$options,'csrf_hash'=>csrf_hash()]);
            
        } catch (\Exception $e) {
            log_message('error', 'kontrakOptions Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Get active contracts for SPK creation
     */
    public function getActiveContracts()
    {
        try {
            // Get contracts with specifications from quotation_specifications
            $builder = $this->db->table('kontrak k');
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            $builder->join('quotation_specifications qs', 'qs.kontrak_id = k.id', 'inner');
            
            // Use safe column selection - only select columns that exist
            $builder->select('k.id, k.no_kontrak, k.customer_po_number, k.rental_type, c.customer_name as pelanggan');
            
            $builder->whereIn('k.status', ['ACTIVE', 'PENDING']);

            // If spk_id is provided, filter contracts to the SPK's customer
            $spkId = (int) $this->request->getGet('spk_id');
            if ($spkId > 0) {
                $spkRow = $this->db->table('spk')
                    ->select('pelanggan_id')
                    ->where('id', $spkId)
                    ->get()->getRowArray();
                if ($spkRow && !empty($spkRow['pelanggan_id'])) {
                    $builder->where('k.customer_id', (int) $spkRow['pelanggan_id']);
                }
            }
            $builder->groupBy('k.id, k.no_kontrak, k.customer_po_number, k.rental_type, c.customer_name');
            $rows = $builder->orderBy('k.dibuat_pada', 'DESC')->get()->getResultArray();
            
            log_message('info', 'getActiveContracts found ' . count($rows) . ' contracts');
            
            $contracts = array_map(function($r){
                $label = ($r['no_kontrak'] ?? 'No Contract') . ' - ' . ($r['pelanggan'] ?? 'Unknown Customer');
                return [
                    'id' => (int)$r['id'],
                    'no_kontrak' => $r['no_kontrak'] ?? '',
                    'customer_po_number' => $r['customer_po_number'] ?? '',
                    'rental_type' => $r['rental_type'] ?? 'CONTRACT',
                    'pelanggan' => $r['pelanggan'] ?? '',
                    'label' => $label,
                    'pic' => '', // Will be filled separately if needed
                    'kontak' => '' // Will be filled separately if needed
                ];
            }, $rows);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $contracts,
                'count' => count($contracts)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getActiveContracts Error. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get contracts for TARIK workflow (includes ACTIVE + EXPIRED with units)
     */
    public function getContractsForTarik()
    {
        try {
            $builder = $this->db->table('kontrak k');
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            $builder->join('kontrak_unit ku', 'ku.kontrak_id = k.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE","TEMPORARILY_REPLACED") AND ku.is_temporary = 0', 'inner');
            $builder->select('
                k.id, k.no_kontrak, k.customer_po_number, k.rental_type, k.status,
                k.tanggal_berakhir,
                c.customer_name as pelanggan,
                COUNT(DISTINCT ku.id) as unit_count,
                (SELECT cl.location_name FROM kontrak_unit ku2 JOIN customer_locations cl ON cl.id = ku2.customer_location_id WHERE ku2.kontrak_id = k.id LIMIT 1) as lokasi
            ');
            $builder->whereIn('k.status', ['ACTIVE', 'EXPIRED']);
            $builder->groupBy('k.id, k.no_kontrak, k.customer_po_number, k.rental_type, k.status, k.tanggal_berakhir, c.customer_name');
            $builder->orderBy('k.tanggal_berakhir', 'ASC');
            $rows = $builder->get()->getResultArray();

            $contracts = array_map(function ($r) {
                $statusTag = $r['status'] === 'EXPIRED' ? ' [EXPIRED]' : '';
                $daysInfo = '';
                if (!empty($r['tanggal_berakhir'])) {
                    $diff = (int)((strtotime($r['tanggal_berakhir']) - time()) / 86400);
                    if ($diff < 0) {
                        $daysInfo = ' (expired ' . abs($diff) . 'd ago)';
                    } elseif ($diff <= 30) {
                        $daysInfo = ' (' . $diff . 'd left)';
                    }
                }
                return [
                    'id' => (int)$r['id'],
                    'no_kontrak' => $r['no_kontrak'] ?? '',
                    'customer_po_number' => $r['customer_po_number'] ?? '',
                    'rental_type' => $r['rental_type'] ?? 'CONTRACT',
                    'status' => $r['status'],
                    'pelanggan' => $r['pelanggan'] ?? '',
                    'lokasi' => $r['lokasi'] ?? '',
                    'unit_count' => (int)$r['unit_count'],
                    'tanggal_berakhir' => $r['tanggal_berakhir'] ?? '',
                    'label' => ($r['no_kontrak'] ?? '') . ' - ' . ($r['pelanggan'] ?? '') . $statusTag . $daysInfo
                ];
            }, $rows);

            return $this->response->setJSON([
                'success' => true,
                'data' => $contracts,
                'count' => count($contracts)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getContractsForTarik error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get specific contract by ID for SPK creation
     */
    public function getKontrak($id)
    {
        try {
            $builder = $this->db->table('kontrak k');
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            
            // Include customer_id for loading customer locations
            $builder->select('k.id, k.no_kontrak, k.customer_po_number, k.rental_type, c.id as customer_id, c.customer_name as pelanggan, (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as lokasi');
            $builder->where('k.id', $id);
            $row = $builder->get()->getRowArray();
            
            if (!$row) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => (int)$row['id'],
                    'no_kontrak' => $row['no_kontrak'],
                    'customer_po_number' => $row['customer_po_number'],
                    'rental_type' => $row['rental_type'],
                    'customer_id' => (int)$row['customer_id'],
                    'pelanggan' => $row['pelanggan'],
                    'lokasi' => $row['lokasi']
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    // Monitoring: Kontrak → SPK status (simple aggregation)
    public function spkMonitoring()
    {
        try {
            // Updated query to handle SPKs from both contracts and quotations
            $sql = "SELECT k.id, k.no_kontrak, k.customer_po_number, 
                           c.customer_name as pelanggan, 
                           (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as lokasi,
                       COUNT(s.id) AS total_spk,
                       SUM(CASE WHEN s.status = 'SUBMITTED' THEN 1 ELSE 0 END) AS submitted,
                       SUM(CASE WHEN s.status = 'IN_PROGRESS' THEN 1 ELSE 0 END) AS in_progress,
                       SUM(CASE WHEN s.status = 'READY' THEN 1 ELSE 0 END) AS ready,
                       SUM(CASE WHEN s.status = 'COMPLETED' THEN 1 ELSE 0 END) AS completed,
                       SUM(CASE WHEN s.status = 'DELIVERED' THEN 1 ELSE 0 END) AS delivered,
                       SUM(CASE WHEN s.status = 'CANCELLED' THEN 1 ELSE 0 END) AS cancelled,
                       MAX(s.diperbarui_pada) AS last_update
                FROM kontrak k
                LEFT JOIN customers c ON c.id = k.customer_id
                LEFT JOIN spk s ON (
                    s.kontrak_id = k.id 
                    OR s.po_kontrak_nomor = k.no_kontrak 
                    OR s.po_kontrak_nomor = k.customer_po_number
                )
                GROUP BY k.id
                ORDER BY k.id DESC
                LIMIT 100";
            $rows = $this->db->query($sql)->getResultArray();
            
            return $this->response->setJSON(['success' => true, 'data'=>$rows, 'csrf_hash'=>csrf_hash()]);
            
        } catch (\Exception $e) {
            log_message('error', 'spkMonitoring error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'data' => [], 'message' => 'Failed to load monitoring data', 'csrf_hash'=>csrf_hash()]);
        }
    }

    // List DIs for marketing page
    public function diList()
    {
        $rows = $this->diModel
            ->select('
                delivery_instructions.*, 
                spk.pic as spk_pic, 
                spk.kontak as spk_kontak,
                spk.nomor_spk,
                jpk.nama as jenis_perintah,
                tpk.nama as tujuan_perintah
            ')
            ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
            ->join('jenis_perintah_kerja jpk', 'jpk.id = delivery_instructions.jenis_perintah_kerja_id', 'left')
            ->join('tujuan_perintah_kerja tpk', 'tpk.id = delivery_instructions.tujuan_perintah_kerja_id', 'left')
            ->orderBy('delivery_instructions.id','DESC')
            ->findAll();
            
        // Add items information for each DI
        foreach ($rows as &$row) {
            // Get items for this DI
            $items = $this->diItemModel
                ->select('
                    delivery_items.*, 
                    iu.no_unit, 
                    mu.merk_unit, 
                    mu.model_unit,
                    a.tipe as att_tipe, 
                    a.merk as att_merk, 
                    a.model as att_model
                ')
                ->join('inventory_unit iu','iu.id_inventory_unit = delivery_items.unit_id','left')
                ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                ->join('attachment a', 'a.id_attachment = delivery_items.attachment_id', 'left')
                ->where('delivery_items.di_id', $row['id'])
                ->findAll();
                
            // Format item labels and check for temporary units
            $itemLabels = [];
            $unitCount = 0;
            $attachmentCount = 0;
            $hasTemporaryUnits = false;
            
            foreach ($items as $item) {
                if ($item['item_type'] === 'UNIT') {
                    $label = trim(($item['no_unit'] ?: 'Unit') . ' - ' . ($item['merk_unit'] ?: '') . ' ' . ($item['model_unit'] ?: ''));
                    $itemLabels[] = ['unit_label' => $label, 'type' => 'unit'];
                    $unitCount++;
                    
                    // Check if this unit is temporary (active temporary assignment)
                    if ($item['unit_id']) {
                        $tempCheck = $this->db->table('kontrak_unit')
                            ->where('unit_id', $item['unit_id'])
                            ->where('is_temporary', 1)
                            ->countAllResults();
                        if ($tempCheck > 0) {
                            $hasTemporaryUnits = true;
                        }
                    }
                } elseif ($item['item_type'] === 'ATTACHMENT') {
                    $label = trim(($item['att_tipe'] ?: 'Attachment') . ' ' . ($item['att_merk'] ?: '') . ' ' . ($item['att_model'] ?: ''));
                    $itemLabels[] = ['attachment_label' => $label, 'type' => 'attachment'];
                    $attachmentCount++;
                }
            }
            
            $row['items'] = $itemLabels;
            $row['total_units'] = $unitCount;
            $row['total_attachments'] = $attachmentCount;
            $row['has_temporary_units'] = $hasTemporaryUnits;
        }
        
        return $this->response->setJSON(['data'=>$rows,'csrf_hash'=>csrf_hash()]);
    }

    /**
     * DataTables endpoint for Marketing DI (server-side processing)
     */
    public function diData()
    {
        $request = $this->request;
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 25;
        $search = $request->getPost('search')['value'] ?? '';
        $statusFilter = $request->getPost('status_filter') ?? 'all';
        
        $builder = $this->diModel->builder();
        
        // Join with related tables
        $builder->select('
            delivery_instructions.*, 
            spk.pic as spk_pic, 
            spk.kontak as spk_kontak,
            spk.nomor_spk,
            spk.po_kontrak_nomor,
            spk.kontrak_id as spk_kontrak_id,
            jpk.nama as jenis_perintah,
            jpk.kode as jenis_perintah_kode,
            tpk.nama as tujuan_perintah,
            tpk.kode as tujuan_perintah_kode,
            k.no_kontrak as linked_contract_number,
            k.customer_id as spk_customer_id
        ')
        ->join('spk', 'spk.id = delivery_instructions.spk_id', 'left')
        ->join('kontrak k', 'k.id = delivery_instructions.contract_id', 'left')
        ->join('jenis_perintah_kerja jpk', 'jpk.id = delivery_instructions.jenis_perintah_kerja_id', 'left')
        ->join('tujuan_perintah_kerja tpk', 'tpk.id = delivery_instructions.tujuan_perintah_kerja_id', 'left');
        
        // Apply status filter
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'SUBMITTED') {
                $builder->whereIn('delivery_instructions.status_di', ['DIAJUKAN', '']);
            } else if ($statusFilter === 'INPROGRESS') {
                $builder->whereIn('delivery_instructions.status_di', ['DISETUJUI', 'PERSIAPAN_UNIT', 'SIAP_KIRIM', 'DALAM_PERJALANAN']);
            } else if ($statusFilter === 'DELIVERED') {
                $builder->whereIn('delivery_instructions.status_di', ['SAMPAI_LOKASI', 'SELESAI']);
            } else if ($statusFilter === 'CANCELLED') {
                $builder->where('delivery_instructions.status_di', 'DIBATALKAN');
            } else if ($statusFilter === 'AWAITING_CONTRACT') {
                $builder->where('delivery_instructions.status_di', 'AWAITING_CONTRACT');
            } else {
                $builder->where('delivery_instructions.status_di', $statusFilter);
            }
        }
        
        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('delivery_instructions.nomor_di', $search)
                ->orLike('spk.nomor_spk', $search)
                ->orLike('spk.po_kontrak_nomor', $search)
                ->orLike('delivery_instructions.pelanggan', $search)
                ->orLike('delivery_instructions.lokasi', $search)
                ->groupEnd();
        }
        
        // Get total filtered count
        $totalFiltered = $builder->countAllResults(false);
        
        // Apply sorting
        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';
        $columns = ['delivery_instructions.nomor_di', 'spk.nomor_spk', 'spk.po_kontrak_nomor', 'delivery_instructions.pelanggan', 'delivery_instructions.lokasi', 'delivery_instructions.total_items', 'jpk.nama', 'tpk.nama', 'delivery_instructions.requested_delivery_date', 'delivery_instructions.status_di'];
        
        if (isset($columns[$orderColumnIndex])) {
            $builder->orderBy($columns[$orderColumnIndex], $orderDir);
        } else {
            $builder->orderBy('delivery_instructions.id', 'DESC');
        }
        
        // Apply pagination
        $query = $builder->limit($length, $start)->get();
        if ($query === false) {
            // Jangan lempar fatal error di production, log saja lalu kembalikan data kosong
            $dbError = $this->db->error();
            log_message(
                'error',
                'Marketing::diData - gagal mengeksekusi query DataTable DI. Error: {code} {message}',
                [
                    'code'    => $dbError['code'] ?? 'N/A',
                    'message' => $dbError['message'] ?? 'Unknown database error',
                ]
            );

            $data = [];
        } else {
            $data = $query->getResultArray();
        }
        
        // Batch-load items for all DI rows in one query (instead of 1 query per DI)
        $batchItems = [];
        $diIds = array_column($data, 'id');
        if (!empty($diIds)) {
            $allItems = $this->diItemModel
                ->select('
                    delivery_items.*,
                    iu.no_unit,
                    mu.merk_unit,
                    mu.model_unit,
                    a.tipe as att_tipe,
                    a.merk as att_merk,
                    a.model as att_model
                ')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = delivery_items.unit_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('attachment a', 'a.id_attachment = delivery_items.attachment_id', 'left')
                ->whereIn('delivery_items.di_id', $diIds)
                ->findAll();
            foreach ($allItems as $item) {
                $batchItems[(int)$item['di_id']][] = $item;
            }
        }

        // Build item labels from batched data
        foreach ($data as &$row) {
            $items = $batchItems[(int)$row['id']] ?? [];
            $itemLabels = [];
            $unitCount = 0;
            $attachmentCount = 0;

            foreach ($items as $item) {
                if ($item['item_type'] === 'UNIT') {
                    $label = trim(($item['no_unit'] ?: 'Unit') . ' - ' . ($item['merk_unit'] ?: '') . ' ' . ($item['model_unit'] ?: ''));
                    $itemLabels[] = ['unit_label' => $label, 'type' => 'unit'];
                    $unitCount++;
                } elseif ($item['item_type'] === 'ATTACHMENT') {
                    $label = trim(($item['att_tipe'] ?: 'Attachment') . ' ' . ($item['att_merk'] ?: '') . ' ' . ($item['att_model'] ?: ''));
                    $itemLabels[] = ['attachment_label' => $label, 'type' => 'attachment'];
                    $attachmentCount++;
                }
            }

            $row['items'] = $itemLabels;
            $row['total_units'] = $unitCount;
            $row['total_attachments'] = $attachmentCount;
        }
        
        // Get total count
        $totalRecords = $this->diModel->countAll();
        
        log_message('info', 'DI DataTables - Draw: ' . $draw . ', Total: ' . $totalRecords . ', Filtered: ' . $totalFiltered . ', Returned: ' . count($data));
        
        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    /**
     * Statistics endpoint for Marketing DI
     */
    public function diStats()
    {
        $statusFilter = $this->request->getPost('status_filter') ?? 'all';
        
        $builder = $this->diModel->builder();
        
        // Total count with current filter
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'SUBMITTED') {
                $builder->whereIn('status_di', ['DIAJUKAN', '']);
            } else if ($statusFilter === 'INPROGRESS') {
                $builder->whereIn('status_di', ['DISETUJUI', 'PERSIAPAN_UNIT', 'SIAP_KIRIM', 'DALAM_PERJALANAN']);
            } else if ($statusFilter === 'DELIVERED') {
                $builder->whereIn('status_di', ['SAMPAI_LOKASI', 'SELESAI']);
            } else {
                $builder->where('status_di', $statusFilter);
            }
            $total = $builder->countAllResults();
        } else {
            $total = $this->diModel->countAll();
        }
        
        // Individual status counts (always from full dataset)
        $submitted = $this->diModel->where('status_di', 'DIAJUKAN')->countAllResults(false);
        $inProgress = $this->diModel->whereIn('status_di', ['DISETUJUI', 'PERSIAPAN_UNIT', 'SIAP_KIRIM', 'DALAM_PERJALANAN'])->countAllResults(false);
        $delivered = $this->diModel->whereIn('status_di', ['SAMPAI_LOKASI', 'SELESAI'])->countAllResults(false);
        $awaitingContract = $this->diModel->where('status_di', 'AWAITING_CONTRACT')->countAllResults();
        
        return $this->response->setJSON([
            'total' => $total,
            'submitted' => $submitted,
            'inprogress' => $inProgress,
            'delivered' => $delivered,
            'awaiting_contract' => $awaitingContract
        ]);
    }

    // Detailed DI info (for Marketing view)
    public function diDetail($id)
    {
        $di = $this->diModel->find((int)$id);
        if (!$di) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'DI tidak ditemukan']);
        }
        // Related SPK (optional)
        $spk = null;
        if (!empty($di['spk_id'])) {
            $spk = $this->spkModel->find((int)$di['spk_id']);
        }

        // Resolve pelanggan_id: DI may have it directly, or inherit from SPK's contract
        if (empty($di['pelanggan_id']) && !empty($spk) && !empty($spk['kontrak_id'])) {
            $kontrak = $this->db->table('kontrak')
                ->select('id, customer_id, no_kontrak')
                ->where('id', $spk['kontrak_id'])
                ->get()->getRowArray();
            if ($kontrak && !empty($kontrak['customer_id'])) {
                $di['pelanggan_id'] = $kontrak['customer_id'];
                // Also backfill contract_id on DI if missing
                if (empty($di['contract_id'])) {
                    $di['contract_id'] = $kontrak['id'];
                }
            }
        }

        // Items
        $items = $this->diItemModel
            ->select('delivery_items.*, iu.no_unit, mu.merk_unit, mu.model_unit, a2.tipe as att_tipe, a2.merk as att_merk, a2.model as att_model')
            ->join('inventory_unit iu','iu.id_inventory_unit = delivery_items.unit_id','left')
            ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
            ->join('attachment a2', 'a2.id_attachment = delivery_items.attachment_id', 'left')
            ->where('delivery_items.di_id',(int)$id)
            ->findAll();
        // Format labels
        foreach ($items as &$it) {
            if ($it['item_type'] === 'UNIT') {
                $it['label'] = trim(($it['no_unit'] ?: '-') . ' - ' . ($it['merk_unit'] ?: '') . ' ' . ($it['model_unit'] ?: ''));
            } elseif ($it['item_type'] === 'ATTACHMENT') {
                $it['label'] = trim(($it['att_tipe'] ?: 'Attachment') . ' ' . ($it['att_merk'] ?: '') . ' ' . ($it['att_model'] ?: ''));
            } else {
                $it['label'] = 'Item';
            }
        }
        unset($it);
        return $this->response->setJSON([
            'success'=>true,
            'data'=>$di,
            'spk'=>$spk,
            'items'=>$items,
            'csrf_hash'=>csrf_hash()
        ]);
    }

    /**
     * Print Surat Perintah Penarikan Unit (SPPU)
     * For withdrawal workflows: TARIK & TUKAR
     */
    public function printWithdrawalLetter($id)
    {
        $id = (int)$id;
        
        // Get DI with jenis_perintah_kerja kode
        $di = $this->db->table('delivery_instructions di')
            ->select('di.*, jpk.kode as jenis_perintah_kode, jpk.nama as jenis_perintah_nama')
            ->join('jenis_perintah_kerja jpk', 'jpk.id = di.jenis_perintah_kerja_id', 'left')
            ->where('di.id', $id)
            ->get()
            ->getRowArray();
        
        if (!$di) {
            return $this->response->setStatusCode(404)->setBody('DI tidak ditemukan');
        }

        // Check if DI is withdrawal type (TARIK or TUKAR) - use kode instead of nama
        $jenisKode = strtoupper($di['jenis_perintah_kode'] ?? '');
        if (!in_array($jenisKode, ['TARIK', 'TUKAR'])) {
            return $this->response->setStatusCode(400)->setBody('Surat penarikan hanya untuk jenis TARIK atau TUKAR');
        }

        // Get SPK data
        $spk = null;
        if (!empty($di['spk_id'])) {
            $spk = $this->spkModel->find((int)$di['spk_id']);
        }

        // Get all items with full details - added departemen, kapasitas, jenis, battery, charger
        $items = $this->diItemModel
            ->select('delivery_items.*, 
                     iu.no_unit, iu.serial_number, iu.tahun_unit,
                     mu.merk_unit, mu.model_unit,
                     tu.tipe as unit_tipe, tu.jenis as unit_jenis,
                     d.nama_departemen as departemen_nama,
                     k.kapasitas_unit as kapasitas_unit_nama,
                     ku.is_temporary, ku.original_unit_id,
                     orig.no_unit as original_no_unit,
                     a2.tipe as att_tipe, a2.merk as att_merk, a2.model as att_model,
                     bat.merk_baterai, bat.tipe_baterai, bat.jenis_baterai,
                     ib.serial_number as sn_baterai,
                     chr.merk_charger, chr.tipe_charger,
                     ic.serial_number as sn_charger')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = delivery_items.unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
            ->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left')
            ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit', 'left')
            ->join('inventory_unit orig', 'orig.id_inventory_unit = ku.original_unit_id', 'left')
            ->join('attachment a2', 'a2.id_attachment = delivery_items.attachment_id', 'left')
            ->join('inventory_batteries ib', 'ib.inventory_unit_id = iu.id_inventory_unit', 'left')
            ->join('baterai bat', 'bat.id = ib.battery_type_id', 'left')
            ->join('inventory_chargers ic', 'ic.inventory_unit_id = iu.id_inventory_unit', 'left')
            ->join('charger chr', 'chr.id_charger = ic.charger_type_id', 'left')
            ->where('delivery_items.di_id', $id)
            ->findAll();

        // Separate units and attachments - organize battery/charger data
        $units = [];
        $attachments = [];
        
        foreach ($items as $item) {
            if ($item['item_type'] === 'UNIT') {
                // Build jenis + tipe display (e.g., "Forklift COUNTER BALANCE")
                if (!empty($item['unit_jenis'])) {
                    $item['unit_tipe'] = trim($item['unit_tipe'] . ' ' . $item['unit_jenis']);
                }
                
                // Organize battery data
                if (!empty($item['merk_baterai'])) {
                    $item['battery'] = [
                        'merk_baterai' => $item['merk_baterai'],
                        'tipe_baterai' => $item['tipe_baterai'] ?? '',
                        'jenis_baterai' => $item['jenis_baterai'] ?? '',
                        'sn_baterai' => $item['sn_baterai'] ?? ''
                    ];
                }
                
                // Organize charger data
                if (!empty($item['merk_charger'])) {
                    $item['charger'] = [
                        'merk_charger' => $item['merk_charger'],
                        'tipe_charger' => $item['tipe_charger'] ?? '',
                        'sn_charger' => $item['sn_charger'] ?? ''
                    ];
                }
                
                $units[] = $item;
            } elseif ($item['item_type'] === 'ATTACHMENT') {
                $attachments[] = $item;
            }
        }

        // Get customer/contract info from SPK
        $customerName = $di['pelanggan'] ?? ($spk['pelanggan'] ?? '-');
        $customerLocation = $di['lokasi'] ?? ($spk['lokasi'] ?? '-');
        $contractNo = $di['po_kontrak_nomor'] ?? ($spk['po_kontrak_nomor'] ?? '-');

        // Get tujuan_perintah_kerja kode for reason text
        $tujuanData = $this->db->table('tujuan_perintah_kerja')
            ->select('kode, nama')
            ->where('id', $di['tujuan_perintah_kerja_id'])
            ->get()
            ->getRowArray();
        
        $tujuanKode = strtoupper($tujuanData['kode'] ?? '');
        $tujuanNama = $tujuanData['nama'] ?? '';
        
        // Dynamic withdrawal purpose - use getWithdrawalReason for customizable text
        // Note: tujuanKode from database already includes jenis prefix (e.g., 'TUKAR_DOWNGRADE')
        $tujuanDisplay = $this->getWithdrawalReason($tujuanKode);
        
        // Context-aware important notes
        $catatanPenting = $this->getCatatanPenting($tujuanKode);

        return view('marketing/print_withdrawal_letter', [
            'di' => $di,
            'spk' => $spk,
            'units' => $units,
            'attachments' => $attachments,
            'customerName' => $customerName,
            'customerLocation' => $customerLocation,
            'contractNo' => $contractNo,
            'tujuanDisplay' => $tujuanDisplay,
            'catatanPenting' => $catatanPenting,
            'jenis' => $jenisKode,
            'tujuan' => $tujuanKode
        ]);
    }

    /**
     * Helper: Get withdrawal reason text based on tujuan code
     * Note: tujuanKode from database already includes jenis prefix (e.g., 'TUKAR_DOWNGRADE')
     */
    private function getWithdrawalReason($tujuanKode)
    {
        $reasons = [
            // TARIK workflows
            'TARIK_MAINTENANCE' => 'Penarikan unit untuk keperluan maintenance dan perbaikan',
            'TARIK_RUSAK' => 'Penarikan unit karena kerusakan yang memerlukan perbaikan',
            'TARIK_HABIS_KONTRAK' => 'Penarikan unit karena masa kontrak telah berakhir',
            'TARIK_PINDAH_LOKASI' => 'Penarikan unit untuk relokasi ke lokasi baru',
            
            // TUKAR workflows
            'TUKAR_MAINTENANCE' => 'Penarikan unit untuk maintenance dan penggantian dengan unit temporary',
            'TUKAR_RUSAK' => 'Penarikan unit yang rusak dan penggantian dengan unit pengganti',
            'TUKAR_UPGRADE' => 'Penarikan unit untuk upgrade ke spesifikasi yang lebih tinggi',
            'TUKAR_DOWNGRADE' => 'Penarikan unit untuk downgrade ke spesifikasi yang sesuai kebutuhan'
        ];

        return $reasons[$tujuanKode] ?? 'Penarikan unit sesuai instruksi delivery';
    }
    
    /**
     * Helper: Get context-aware important notes based on tujuan code
     * Note: tujuanKode from database already includes jenis prefix (e.g., 'TUKAR_DOWNGRADE')
     */
    private function getCatatanPenting($tujuanKode)
    {
        $notes = [
            // TARIK workflows
            'TARIK_HABIS_KONTRAK' => 'Unit akan dikembalikan ke gudang PT. Sarana Mitra Luas setelah masa kontrak berakhir. Verifikasi kelengkapan dan kondisi unit wajib dilakukan sebelum penarikan.',
            'TARIK_RUSAK' => 'Unit mengalami kerusakan dan akan ditarik untuk perbaikan. Dokumentasi kondisi kerusakan wajib dilakukan sebelum penarikan.',
            'TARIK_MAINTENANCE' => 'Unit akan ditarik untuk maintenance terjadwal. Jadwal pengembalian akan diinformasikan setelah pekerjaan selesai.',
            'TARIK_PINDAH_LOKASI' => 'Unit akan dipindahkan ke lokasi baru sesuai instruksi pelanggan. Koordinasi dengan penerima di lokasi tujuan diperlukan.',
            
            // TUKAR workflows
            'TUKAR_MAINTENANCE' => 'Unit akan ditarik untuk maintenance dan digantikan dengan unit temporary. Pengembalian unit original akan dilakukan setelah maintenance selesai.',
            'TUKAR_RUSAK' => 'Unit yang rusak akan digantikan dengan unit pengganti. Verifikasi kondisi unit pengganti wajib dilakukan saat pengiriman.',
            'TUKAR_UPGRADE' => 'Unit akan digantikan dengan unit baru dengan spesifikasi yang lebih tinggi sesuai permintaan. Addendum kontrak akan mengikuti.',
            'TUKAR_DOWNGRADE' => 'Unit akan digantikan dengan unit yang sesuai kebutuhan operasional pelanggan. Penyesuaian kontrak akan diproses.'
        ];

        return $notes[$tujuanKode] ?? 'Penarikan unit dilakukan sesuai instruksi operasional. Koordinasi dengan tim operasional diperlukan untuk kelancaran proses.';
    }

    // Options: SPK that are READY for DI creation
    public function spkReadyOptions()
    {
        $q = trim($this->request->getGet('q') ?? '');
        $b = $this->spkModel
            ->select('spk.id, spk.nomor_spk, spk.po_kontrak_nomor, spk.pelanggan, spk.lokasi, COALESCE(spk.customer_id, k.customer_id, c.id) as customer_id')
            ->join('kontrak k', 'k.id = spk.kontrak_id', 'left')
            ->join('customers c', 'c.customer_name = spk.pelanggan', 'left')
            ->where('spk.status', 'READY');
        if ($q !== '') {
            $b->groupStart()
                ->like('nomor_spk',$q)
                ->orLike('po_kontrak_nomor',$q)
                ->orLike('pelanggan',$q)
            ->groupEnd();
        }
        $rows = $b->orderBy('id','DESC')->limit(50)->get()->getResultArray();
        $opts = array_map(function($r){
            return [
                'id' => (int)$r['id'],
                'label' => trim(($r['nomor_spk'] ?: '-') . ' - ' . ($r['po_kontrak_nomor'] ?: '-') . ' - ' . ($r['pelanggan'] ?: '-')),
                'pelanggan' => $r['pelanggan'] ?: '',
                'lokasi' => $r['lokasi'] ?: '',
                'po' => $r['po_kontrak_nomor'] ?: '',
                'nomor_spk' => $r['nomor_spk'] ?: '',
                'customer_id' => isset($r['customer_id']) ? (int)$r['customer_id'] : null,
            ];
        }, $rows);
        return $this->response->setJSON(['data'=>$opts,'csrf_hash'=>csrf_hash()]);
    }

    public function spkCreate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $this->db->transBegin();

        try {
            // Log all received POST data for debugging
            log_message('info', 'Marketing::spkCreate - Received POST data: ' . json_encode($this->request->getPost()));
            
            // Debug jenis_spk specifically
            $jenisSpkRaw = $this->request->getPost('jenis_spk');
            log_message('info', 'Marketing::spkCreate - Raw jenis_spk from form: "' . $jenisSpkRaw . '"');
            log_message('info', 'Marketing::spkCreate - jenis_spk type: ' . gettype($jenisSpkRaw));
            
            // Check if this is new workflow with kontrak_spesifikasi_id or kontrak_id
            $kontrakSpesifikasiId = $this->request->getPost('kontrak_spesifikasi_id');
            $kontrakId = $this->request->getPost('kontrak_id');
            $jumlahUnit = (int)($this->request->getPost('jumlah_unit') ?: 1);

            log_message('info', 'Marketing::spkCreate - kontrak_spesifikasi_id: ' . $kontrakSpesifikasiId);
            log_message('info', 'Marketing::spkCreate - kontrak_id: ' . $kontrakId);
            log_message('info', 'Marketing::spkCreate - jumlah_unit: ' . $jumlahUnit);

            if ($kontrakSpesifikasiId && $kontrakSpesifikasiId > 0) {
                // New workflow: Create SPK based on contract specification from quotation_specifications
                log_message('info', 'Marketing::spkCreate - Using specification workflow for id_specification: ' . $kontrakSpesifikasiId);
                
                // Query quotation_specifications directly
                $spesifikasi = $this->db->table('quotation_specifications')
                    ->where('id_specification', $kontrakSpesifikasiId)
                    ->where('is_active', 1)
                    ->get()
                    ->getRowArray();
                    
                log_message('info', 'Marketing::spkCreate - Found spesifikasi: ' . json_encode($spesifikasi));
                if (!$spesifikasi) {
                    throw new \Exception('Spesifikasi kontrak tidak ditemukan.');
                }

                // For quotation_specifications, use quantity field
                $available = $spesifikasi['quantity'];
                if ($jumlahUnit > $available) {
                    throw new \Exception("Jumlah unit melebihi yang tersedia. Maksimal: {$available} unit");
                }

                // Contract link is optional in quotation -> SPK workflow
                $linkedKontrakId = !empty($spesifikasi['kontrak_id']) ? (int)$spesifikasi['kontrak_id'] : null;
                $kontrak = null;
                if ($linkedKontrakId) {
                    $kontrak = $this->db->table('kontrak k')
                        ->select('k.id, k.no_kontrak, c.customer_name as pelanggan,
                            (SELECT cl.contact_person FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as pic,
                            (SELECT cl.phone FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as kontak,
                            (SELECT cl.address FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as lokasi')
                        ->join('customers c', 'c.id = k.customer_id', 'left')
                        ->where('k.id', $linkedKontrakId)
                        ->get()
                        ->getRowArray();

                    if (!$kontrak) {
                        log_message('warning', 'Marketing::spkCreate - Linked contract not found for specification ID ' . $kontrakSpesifikasiId . ': kontrak_id=' . $linkedKontrakId);
                        $linkedKontrakId = null;
                    }
                }

                // Fallback data when contract is not linked yet
                $quotation = null;
                if (!empty($spesifikasi['id_quotation'])) {
                    $quotation = $this->db->table('quotations')
                        ->select('prospect_name, prospect_contact_person, prospect_phone, prospect_address')
                        ->where('id_quotation', $spesifikasi['id_quotation'])
                        ->get()
                        ->getRowArray();
                }

                // Get jenis_spk from form input, default to 'UNIT' if not provided
                $jenis = strtoupper(trim((string)$this->request->getPost('jenis_spk') ?: 'UNIT'));
                $allowedJenis = ['UNIT','ATTACHMENT'];
                if (!in_array($jenis, $allowedJenis, true)) { $jenis = 'UNIT'; }

                // Build specification array from quotation_specifications
                $spec = [
                    'departemen_id' => $spesifikasi['departemen_id'],
                    'tipe_unit_id' => $spesifikasi['tipe_unit_id'],
                    'tipe_jenis' => null, // Not in quotation_specifications
                    'merk_unit' => null, // Not in quotation_specifications
                    'model_unit' => null, // Not in quotation_specifications
                    'kapasitas_id' => $spesifikasi['kapasitas_id'],
                    'attachment_tipe' => null, // Will be loaded from attachment table
                    'attachment_merk' => null, // Will be loaded from attachment table
                    'jenis_baterai' => null, // Will be loaded from baterai table
                    'charger_id' => $spesifikasi['charger_id'],
                    'mast_id' => $spesifikasi['mast_id'],
                    'ban_id' => $spesifikasi['ban_id'],
                    'roda_id' => $spesifikasi['roda_id'],
                    'valve_id' => $spesifikasi['valve_id'],
                    'aksesoris' => []
                ];
                
                // For ATTACHMENT SPK, add target unit info to spec
                if ($jenis === 'ATTACHMENT') {
                    $targetUnitId = $this->request->getPost('target_unit_id');
                    
                    // DEBUG: Log all POST data to see what was sent
                    log_message('info', '🔍 ATTACHMENT SPK - All POST data: ' . json_encode($this->request->getPost()));
                    log_message('info', '📋 ATTACHMENT SPK - target_unit_id from POST: ' . ($targetUnitId ?: 'NULL/EMPTY'));
                    
                    if (!$targetUnitId) {
                        throw new \Exception('Unit tujuan wajib dipilih untuk SPK ATTACHMENT');
                    }
                    
                    // Get target unit details - try different approaches
                    try {
                        // First try: direct query to inventory_unit
                        $targetUnitQuery = $this->db->table('inventory_unit')
                            ->where('id_inventory_unit', $targetUnitId)
                            ->get();
                        
                        if (!$targetUnitQuery) {
                            throw new \Exception('Gagal mengambil data unit tujuan - query failed');
                        }
                        
                        $targetUnit = $targetUnitQuery->getRowArray();
                        
                        if (!$targetUnit) {
                            // Try alternative: check if unit exists in different table
                            $altQuery = $this->db->table('units')
                                ->where('id', $targetUnitId)
                                ->get();
                            
                            if ($altQuery) {
                                $altUnit = $altQuery->getRowArray();
                                if ($altUnit) {
                                    // Map alternative unit data
                                    $targetUnit = [
                                        'id_inventory_unit' => $altUnit['id'],
                                        'sn_unit' => $altUnit['serial_number'] ?? $altUnit['sn_unit'] ?? 'N/A',
                                        'tipe_jenis' => $altUnit['jenis_unit'] ?? $altUnit['tipe_jenis'] ?? 'N/A',
                                        'merk_unit' => $altUnit['merk'] ?? $altUnit['merk_unit'] ?? 'N/A',
                                        'model_unit' => $altUnit['model'] ?? $altUnit['model_unit'] ?? 'N/A'
                                    ];
                                } else {
                                    throw new \Exception('Unit tujuan tidak ditemukan di database');
                                }
                            } else {
                                throw new \Exception('Unit tujuan tidak ditemukan');
                            }
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.');
                    }
                    
                    $spec['target_unit_id'] = $targetUnitId;
                    $spec['target_unit_sn'] = $targetUnit['sn_unit'] ?? $targetUnit['serial_number'] ?? 'N/A';
                    $spec['target_unit_info'] = [
                        'tipe' => $targetUnit['tipe_jenis'] ?? $targetUnit['jenis_unit'] ?? 'N/A',
                        'merk' => $targetUnit['merk_unit'] ?? $targetUnit['merk'] ?? 'N/A',
                        'model' => $targetUnit['model_unit'] ?? $targetUnit['model'] ?? 'N/A'
                    ];
                    $spec['replacement_reason'] = $this->request->getPost('replacement_reason') ?: 'Penggantian attachment';
                    
                    log_message('info', 'Marketing::spkCreate - ATTACHMENT SPK target unit: ' . json_encode($spec));
                    
                    // Force jumlah_unit to 1 for ATTACHMENT
                    $jumlahUnit = 1;
                }

                $payload = [
                    'nomor_spk' => method_exists($this->spkModel,'generateNextNumber') ? $this->spkModel->generateNextNumber() : $this->generateSpkNumber(),
                    'jenis_spk' => $jenis,
                    'kontrak_id' => $linkedKontrakId,
                    'quotation_specification_id' => $kontrakSpesifikasiId,
                    'jumlah_unit' => $jumlahUnit,
                    'po_kontrak_nomor' => $kontrak['no_kontrak'] ?? null,
                    'pelanggan' => $this->request->getPost('pelanggan') ?: ($kontrak['pelanggan'] ?? ($quotation['prospect_name'] ?? '')),
                    'pic' => $this->request->getPost('pic') ?: ($kontrak['pic'] ?? ($quotation['prospect_contact_person'] ?? null)),
                    'kontak' => $this->request->getPost('kontak') ?: ($kontrak['kontak'] ?? ($quotation['prospect_phone'] ?? null)),
                    'lokasi' => $this->request->getPost('lokasi') ?: ($kontrak['lokasi'] ?? ($quotation['prospect_address'] ?? null)),
                    'delivery_plan' => $this->request->getPost('delivery_plan') ?: null,
                    'spesifikasi' => json_encode($spec),
                    'catatan' => $this->request->getPost('catatan') ?: null,
                    'status' => 'SUBMITTED',
                    'dibuat_oleh' => session('user_id') ?: 1,
                    'dibuat_pada' => date('Y-m-d H:i:s')
                ];

            } elseif ($kontrakId && $kontrakId > 0) {
                // Fallback: Create SPK based on contract ID only (when no specification is selected)
                log_message('info', 'Marketing::spkCreate - Using kontrak_id fallback workflow for kontrak: ' . $kontrakId);
                $kontrak = $this->kontrakModel->find($kontrakId);
                log_message('info', 'Marketing::spkCreate - Found kontrak: ' . json_encode($kontrak));
                if (!$kontrak) {
                    throw new \Exception('Kontrak tidak ditemukan.');
                }

                // Get first available specification for this contract from quotation_specifications
                $spesifikasiList = $this->db->table('quotation_specifications')
                    ->where('kontrak_id', $kontrakId)
                    ->where('is_active', 1)
                    ->get()
                    ->getResultArray();
                    
                log_message('info', 'Marketing::spkCreate - Found ' . count($spesifikasiList) . ' specifications for kontrak ' . $kontrakId);
                
                $firstSpecId = null;
                if (!empty($spesifikasiList)) {
                    log_message('info', 'Marketing::spkCreate - First spec sample: ' . json_encode($spesifikasiList[0]));
                }
                $spec = [];
                
                if (!empty($spesifikasiList)) {
                    // Use the first specification as template
                    $firstSpec = $spesifikasiList[0];
                    log_message('info', 'Marketing::spkCreate - Using first spec as template: ' . json_encode($firstSpec));
                    $firstSpecId = $firstSpec['id_specification']; // Use id_specification field
                    $spec = [
                        'departemen_id' => $firstSpec['departemen_id'] ?? null,
                        'tipe_unit_id' => $firstSpec['tipe_unit_id'] ?? null,
                        'tipe_jenis' => null, // Not in quotation_specifications
                        'merk_unit' => null, // Not in quotation_specifications
                        'model_unit' => null, // Not in quotation_specifications
                        'kapasitas_id' => $firstSpec['kapasitas_id'] ?? null,
                        'attachment_tipe' => null, // Not in quotation_specifications
                        'attachment_merk' => null, // Not in quotation_specifications
                        'jenis_baterai' => null, // Not in quotation_specifications
                        'charger_id' => $firstSpec['charger_id'] ?? null,
                        'mast_id' => $firstSpec['mast_id'] ?? null,
                        'ban_id' => $firstSpec['ban_id'] ?? null,
                        'roda_id' => $firstSpec['roda_id'] ?? null,
                        'valve_id' => $firstSpec['valve_id'] ?? null,
                        'aksesoris' => []
                    ];
                } else {
                    log_message('info', 'Marketing::spkCreate - No specifications found for kontrak ' . $kontrakId . ', using empty spec');
                }

                // Get jenis_spk from form input, default to 'UNIT' if not provided
                $jenis = strtoupper(trim((string)$this->request->getPost('jenis_spk') ?: 'UNIT'));
                $allowedJenis = ['UNIT','ATTACHMENT'];
                if (!in_array($jenis, $allowedJenis, true)) { $jenis = 'UNIT'; }

                $payload = [
                    'nomor_spk' => method_exists($this->spkModel,'generateNextNumber') ? $this->spkModel->generateNextNumber() : $this->generateSpkNumber(),
                    'jenis_spk' => $jenis,
                    'kontrak_id' => $kontrak['id'],
                    'quotation_specification_id' => $firstSpecId, // Use the first spec ID if available
                    'jumlah_unit' => $jumlahUnit,
                    'po_kontrak_nomor' => $kontrak['no_kontrak'],
                    'pelanggan' => $this->request->getPost('pelanggan') ?: $kontrak['pelanggan'],
                    'pic' => $this->request->getPost('pic') ?: $kontrak['pic'],
                    'kontak' => $this->request->getPost('kontak') ?: $kontrak['kontak'],
                    'lokasi' => $this->request->getPost('lokasi') ?: $kontrak['lokasi'],
                    'delivery_plan' => $this->request->getPost('delivery_plan') ?: null,
                    'spesifikasi' => json_encode($spec),
                    'catatan' => $this->request->getPost('catatan') ?: null,
                    'status' => 'SUBMITTED',
                    'dibuat_oleh' => session('user_id') ?: 1,
                    'dibuat_pada' => date('Y-m-d H:i:s')
                ];

            } else {
                // Legacy workflow: manual specification input
                $spec = $this->request->getPost('spesifikasi') ?? [];
                if (isset($spec['aksesoris']) && is_string($spec['aksesoris'])) {
                    $decoded = json_decode($spec['aksesoris'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $spec['aksesoris'] = $decoded;
                    }
                }

                $jenis = strtoupper(trim((string)$this->request->getPost('jenis_spk') ?: 'UNIT'));
                $allowedJenis = ['UNIT','ATTACHMENT'];
                if (!in_array($jenis, $allowedJenis, true)) { $jenis = 'UNIT'; }

                $payload = [
                    'nomor_spk' => method_exists($this->spkModel,'generateNextNumber') ? $this->spkModel->generateNextNumber() : $this->generateSpkNumber(),
                    'jenis_spk' => $jenis,
                    'po_kontrak_nomor' => $this->request->getPost('po_kontrak_nomor') ?: null,
                    'pelanggan' => $this->request->getPost('pelanggan') ?: '',
                    'pic' => $this->request->getPost('pic') ?: null,
                    'kontak' => $this->request->getPost('kontak') ?: null,
                    'lokasi' => $this->request->getPost('lokasi') ?: null,
                    'delivery_plan' => $this->request->getPost('delivery_plan') ?: null,
                    'spesifikasi' => json_encode($spec),
                    'catatan' => $this->request->getPost('catatan') ?: null,
                    'status' => 'SUBMITTED',
                    'dibuat_oleh' => session('user_id') ?: 1,
                    'dibuat_pada' => date('Y-m-d H:i:s')
                ];
            }

            // Insert SPK with robust success detection
            log_message('info', 'Marketing::spkCreate - About to insert SPK');
            log_message('info', 'Marketing::spkCreate - Payload: ' . json_encode($payload));
            
            // Check if SPK number already exists before insert
            $existingSpkQuery = $this->db->table('spk')->where('nomor_spk', $payload['nomor_spk'])->get();
            if (!$existingSpkQuery) {
                throw new \Exception('Gagal mengecek nomor SPK yang sudah ada');
            }
            
            $existingSpk = $existingSpkQuery->getRow();
            if ($existingSpk) {
                log_message('error', 'Marketing::spkCreate - SPK number already exists: ' . $payload['nomor_spk'] . ' with ID: ' . $existingSpk->id);
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nomor SPK sudah digunakan. Silakan refresh halaman dan coba lagi.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            
            $insertResult = $this->spkModel->insert($payload);
            log_message('info', 'Marketing::spkCreate - Insert result: ' . json_encode($insertResult));
            
            // Commit transaction immediately after insert
            $this->db->transCommit();
            log_message('info', 'Marketing::spkCreate - Transaction committed');
            
            // Get the insert ID AFTER committing the transaction
            $insertedId = $this->spkModel->getInsertID();
            log_message('info', 'Marketing::spkCreate - getInsertID() result: ' . $insertedId);
            log_message('info', 'Marketing::spkCreate - getInsertID() type: ' . gettype($insertedId));
            
            // Also try getting the last insert ID from the database connection
            $dbInsertId = $this->db->insertID();
            log_message('info', 'Marketing::spkCreate - db->insertID() result: ' . $dbInsertId);
            
            // Test database connection after commit
            $testQuery = $this->db->table('spk')->countAll();
            log_message('info', 'Marketing::spkCreate - Database connection test after commit: ' . $testQuery . ' records found');
            
            // Verify the inserted record using the insert ID
            if ($insertedId && $insertedId > 0) {
                $spkId = $insertedId;
                log_message('info', 'Marketing::spkCreate - Using getInsertID() result: ' . $spkId);
                
                // Double-check by querying the database
                $verifyQuery = $this->db->table('spk')->where('id', $spkId)->get();
                if ($verifyQuery->getNumRows() > 0) {
                    $verified = $verifyQuery->getRow();
                    log_message('info', 'Marketing::spkCreate - Verification successful: ID=' . $verified->id . ', nomor_spk=' . $verified->nomor_spk);
                } else {
                    log_message('error', 'Marketing::spkCreate - Verification failed: Could not find record with ID=' . $spkId);
                }
            } else {
                // Fallback: try to find by nomor_spk
                log_message('info', 'Marketing::spkCreate - getInsertID() failed, trying fallback search by nomor_spk=' . $payload['nomor_spk']);
                $query = $this->db->table('spk')
                    ->where('nomor_spk', $payload['nomor_spk'])
                    ->get();
                
                log_message('info', 'Marketing::spkCreate - Fallback query executed, num_rows: ' . $query->getNumRows());
                
                if ($query->getNumRows() > 0) {
                    $inserted = $query->getRow();
                    $spkId = $inserted->id;
                    log_message('info', 'Marketing::spkCreate - Fallback successful, found ID: ' . $spkId);
                } else {
                    log_message('error', 'Marketing::spkCreate - Fallback failed: Could not find inserted SPK record');
                    $spkId = null;
                }
            }

            if ($spkId) {
                log_message('info', 'Marketing::spkCreate - SPK berhasil dibuat dengan ID: ' . $spkId);
                
                // Verify the data was actually inserted
                $insertedSpk = $this->spkModel->find($spkId);
                log_message('info', 'Marketing::spkCreate - Verification - Inserted SPK data: ' . json_encode($insertedSpk));

                // Build notification-friendly departemen + unit string from stored `spesifikasi`
                $insertedSpkArray = is_object($insertedSpk) ? get_object_vars($insertedSpk) : (array)($insertedSpk ?? []);
                $specJson = $insertedSpkArray['spesifikasi'] ?? null;
                $specDecoded = [];
                if (is_string($specJson) && $specJson !== '') {
                    $decoded = json_decode($specJson, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $specDecoded = $decoded;
                    }
                } elseif (is_array($specJson)) {
                    $specDecoded = $specJson;
                }

                $departemenName = 'N/A';
                if (!empty($specDecoded['departemen_id'])) {
                    $departemenRow = $this->db->table('departemen')
                        ->select('nama_departemen')
                        ->where('id_departemen', $specDecoded['departemen_id'])
                        ->get()
                        ->getRowArray();
                    $departemenName = $departemenRow['nama_departemen'] ?? $departemenName;
                }

                $unitName = 'N/A';
                if (!empty($specDecoded['tipe_unit_id'])) {
                    $tipeUnitRow = $this->db->table('tipe_unit')
                        ->select('tipe, jenis')
                        ->where('id_tipe_unit', $specDecoded['tipe_unit_id'])
                        ->get()
                        ->getRowArray();
                    $unitName = $tipeUnitRow['tipe'] ?? $tipeUnitRow['jenis'] ?? $unitName;
                }
                
                // Log SPK creation using trait
                $this->logCreate('spk', $spkId, [
                    'spk_id' => $spkId,
                    'nomor_spk' => $payload['nomor_spk'],
                    'jenis_spk' => $payload['jenis_spk'],
                    'kontrak_id' => $payload['kontrak_id'] ?? null,
                    'kontrak_spesifikasi_id' => $payload['kontrak_spesifikasi_id'] ?? null,
                    'jumlah_unit' => $payload['jumlah_unit']
                ]);
                
                // Notify Service team with SPK data
                $this->sendSpkNotification($payload['nomor_spk'], [
                    'id' => $spkId,
                    'pelanggan' => $payload['pelanggan'],
                    'departemen' => $departemenName,
                    'unit_no' => $unitName,
                    'lokasi' => $payload['lokasi'] ?? 'N/A'
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'SPK berhasil dibuat',
                    'nomor' => $payload['nomor_spk'],
                    'spk_id' => $spkId,
                    'inserted_data' => $insertedSpk,
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                // Get detailed error information
                $errors = $this->spkModel->errors();
                $dbError = $this->db->error();
                
                // Try to get more detailed error info
                $lastQuery = $this->db->getLastQuery();
                $errorCode = $dbError['code'] ?? 0;
                $errorMessage = $dbError['message'] ?? '';
                
                log_message('error', 'Marketing::spkCreate - Insert result: ' . json_encode($insertResult));
                log_message('error', 'Marketing::spkCreate - Could not find inserted SPK record');
                log_message('error', 'Marketing::spkCreate - Model validation errors: ' . json_encode($errors));
                log_message('error', 'Marketing::spkCreate - Database error: ' . json_encode($dbError));
                log_message('error', 'Marketing::spkCreate - Last query: ' . $lastQuery);
                log_message('error', 'Marketing::spkCreate - Payload data: ' . json_encode($payload));
                
                // Check if there are any SPK records at all
                $totalSpk = $this->db->table('spk')->countAll();
                log_message('error', 'Marketing::spkCreate - Total SPK records in database: ' . $totalSpk);
                
                // Check if the SPK number already exists
                $existingSpk = $this->db->table('spk')->where('nomor_spk', $payload['nomor_spk'])->get()->getRow();
                if ($existingSpk) {
                    log_message('error', 'Marketing::spkCreate - SPK number already exists: ' . json_encode($existingSpk));
                }
                
                // Construct a more informative error message
                $errorMsg = 'Gagal membuat SPK.';
                
                if (!empty($errors)) {
                    $errorMsg .= ' Validation errors: ' . implode(', ', $errors);
                } elseif (!empty($errorMessage)) {
                    $errorMsg .= ' Database error: ' . $errorMessage;
                } elseif ($errorCode > 0) {
                    $errorMsg .= ' Database error code: ' . $errorCode;
                } elseif ($insertResult === false) {
                    $errorMsg .= ' Insert failed.';
                } else {
                    $errorMsg .= ' Could not verify SPK was saved.';
                }
                
                // Rollback transaction since verification failed
                $this->db->transRollback();
                log_message('error', 'Marketing::spkCreate - Transaction rolled back due to verification failure');
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg,
                    'validation_errors' => $errors,
                    'db_error' => $dbError,
                    'debug_info' => [
                        'last_query' => $lastQuery,
                        'db_error_code' => $errorCode,
                        'db_error_message' => $errorMessage,
                        'insert_result' => $insertResult,
                        'searched_nomor_spk' => $payload['nomor_spk'],
                        'total_spk_records' => $totalSpk,
                        'existing_spk' => $existingSpk ? 'YES' : 'NO'
                    ],
                    'csrf_hash' => csrf_hash()
                ]);
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Create multiple SPKs from quotation specifications
     * User selects which specifications and quantities to create SPK for
     */
    public function createSPKFromQuotation()
    {
        $canCreateSpk = $this->hasPermission('marketing.spk.create')
            || $this->hasPermission('marketing.kontrak.create')
            || $this->canManage('marketing');
        if (!$canCreateSpk) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        // Initialize database connection
        $this->db = \Config\Database::connect();
        
        // EXTREME DEBUG - This MUST show up
        error_log("============ CREATE SPK FROM QUOTATION CALLED ============");
        log_message('error', 'createSPKFromQuotation - Function called');
        log_message('error', 'REQUEST METHOD: ' . $this->request->getMethod());
        log_message('error', 'IS AJAX: ' . ($this->request->isAJAX() ? 'YES' : 'NO'));
        
        if (!$this->request->isAJAX()) {
            log_message('error', 'createSPKFromQuotation - Not AJAX request');
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        log_message('info', 'createSPKFromQuotation - Starting transaction');
        $this->db->transStart();

        try {
            log_message('info', 'createSPKFromQuotation - Getting JSON input');
            // Get JSON input
            $jsonData = $this->request->getJSON(true); // true = return as array
            
            log_message('info', 'createSPKFromQuotation - JSON data: ' . ($jsonData ? 'exists' : 'null'));
            
            // Fallback to POST if not JSON
            if (!$jsonData) {
                log_message('info', 'createSPKFromQuotation - Fallback to POST');
                $jsonData = [
                    'quotation_id' => $this->request->getPost('quotation_id'),
                    'customer_id' => $this->request->getPost('customer_id'),
                    'contract_id' => $this->request->getPost('contract_id'),
                    'delivery_date' => $this->request->getPost('delivery_date'),
                    'specifications' => $this->request->getPost('specifications')
                 ];
            }
            
            $quotationId = $jsonData['quotation_id'] ?? null;
            $customerId = $jsonData['customer_id'] ?? null;
            $contractId = $jsonData['contract_id'] ?? null;
            $deliveryDate = $jsonData['delivery_date'] ?? null;
            $specifications = $jsonData['specifications'] ?? [];

            log_message('error', 'createSPKFromQuotation - Input data: ' . json_encode($jsonData));
            log_message('error', 'createSPKFromQuotation - Specifications count: ' . count($specifications));
            log_message('error', 'createSPKFromQuotation - Specifications type: ' . gettype($specifications));

            // Validation - CONTRACT NOW OPTIONAL (can be linked later)
            if (!$quotationId || !$customerId) {
                log_message('error', 'createSPKFromQuotation - Missing data: quotation=' . $quotationId . ', customer=' . $customerId);
                throw new \Exception('Missing required data: quotation or customer');
            }

            if (!$deliveryDate) {
                throw new \Exception('Delivery date is required');
            }

            if (empty($specifications) || !is_array($specifications)) {
                throw new \Exception('Please select at least one specification');
            }

            // Get quotation with customer location data
            $quotation = $this->quotationModel->find($quotationId);
            if (!$quotation) {
                throw new \Exception('Quotation tidak ditemukan');
            }

            // Get customer and location data for fallback
            $customerData = $this->db->table('customers c')
                ->select('c.*, cl.contact_person as pic, cl.phone as kontak, cl.address as lokasi, cl.location_name as nama_lokasi')
                ->join('customer_locations cl', 'cl.customer_id = c.id', 'left')
                ->where('c.id', $customerId)
                ->get()
                ->getRowArray();

            // Get contract data if provided (OPTIONAL) - VALIDATE contract exists
            $contract = null;
            $validContractId = null;
            if ($contractId) {
                $contract = $this->db->table('kontrak k')
                    ->select('k.*, c.customer_name as pelanggan, 
                        (SELECT cl.contact_person FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as pic,
                        (SELECT cl.phone FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as kontak,
                        (SELECT cl.address FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as lokasi,
                        (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as nama_lokasi')
                    ->join('customers c', 'c.id = k.customer_id', 'left')
                    ->where('k.id', $contractId)
                    ->get()
                    ->getRowArray();
                    
                if (!$contract) {
                    log_message('warning', 'Contract ID provided but not found in database: ' . $contractId . ' - Setting to NULL');
                    $validContractId = null; // Contract doesn't exist, set to NULL
                } else {
                    $validContractId = $contractId; // Contract exists, use it
                }
            }
            
            // Use contract data if available, otherwise fallback to customer/quotation data
            $customerInfo = [
                'pelanggan' => $contract['pelanggan'] ?? $customerData['customer_name'] ?? $quotation['prospect_name'],
                'pic' => $contract['pic'] ?? $customerData['pic'] ?? $quotation['prospect_contact_person'],
                'kontak' => $contract['kontak'] ?? $customerData['kontak'] ?? $quotation['prospect_phone'],
                'lokasi' => $contract ? (($contract['nama_lokasi'] ?? '') . ($contract['lokasi'] ? ' - ' . $contract['lokasi'] : '')) 
                          : (($customerData['nama_lokasi'] ?? '') . ($customerData['lokasi'] ? ' - ' . $customerData['lokasi'] : '') ?: $quotation['prospect_address']),
                'po_kontrak_nomor' => $contract['no_kontrak'] ?? null
            ];

            $createdSPKs = [];
            $spkNumbers = [];
            $errorMessages = []; // Track errors
            $notificationQueue = []; // Collected after transaction commits

            // Create SPK for each selected specification
            foreach ($specifications as $specData) {
                log_message('error', "Loop iteration - specData: " . json_encode($specData));
                
                $specId = $specData['specification_id'];
                $quantity = (int)$specData['quantity'];

                log_message('error', "Processing spec {$specId} with quantity {$quantity}");

                if ($quantity <= 0) {
                    $errorMessages[] = "Specification #{$specId}: Invalid quantity ({$quantity})";
                    log_message('warning', "Spec {$specId} skipped: invalid quantity {$quantity}");
                    continue; // Skip invalid quantities
                }

                // Get specification details
                $spec = $this->db->table('quotation_specifications qs')
                    ->select('qs.*, 
                        d.nama_departemen, 
                        tu.tipe as nama_tipe_unit, 
                        tu.jenis as jenis_tipe_unit, 
                        k.kapasitas_unit as nama_kapasitas')
                    ->join('departemen d', 'd.id_departemen = qs.departemen_id', 'left')
                    ->join('tipe_unit tu', 'tu.id_tipe_unit = qs.tipe_unit_id', 'left')
                    ->join('kapasitas k', 'k.id_kapasitas = qs.kapasitas_id', 'left')
                    ->where('qs.id_specification', $specId)
                    ->get()
                    ->getRowArray();

                if (!$spec) {
                    $errorMessages[] = "Specification #{$specId}: Not found in database";
                    log_message('error', "Specification not found: ID {$specId}");
                    continue;
                }
                
                // Build brand and model manually from available data
                $spec['brand'] = $spec['brand_id'] ?? 'N/A';
                $spec['model'] = ($spec['nama_tipe_unit'] ?? 'N/A') . ' - ' . ($spec['jenis_tipe_unit'] ?? '');

                log_message('error', "Spec {$specId} found: " . json_encode(['brand_id' => $spec['brand_id'] ?? 'N/A', 'model' => $spec['model'] ?? 'N/A', 'qty' => $spec['quantity'], 'quotation_id' => $spec['id_quotation'] ?? 'null']));

                // Check existing SPKs for this specification
                $existingSPKs = $this->db->table('spk')
                    ->selectSum('jumlah_unit', 'total_units')
                    ->where('quotation_specification_id', $specId)
                    ->where('status !=', 'CANCELLED')
                    ->get()
                    ->getRowArray();
                
                $existingUnits = (int)($existingSPKs['total_units'] ?? 0);
                $specTotalQty = (int)($spec['quantity'] ?? 0);
                $availableQty = $specTotalQty - $existingUnits;
                
                log_message('error', "Availability check for spec {$specId}: Total={$specTotalQty}, Existing={$existingUnits}, Available={$availableQty}");
                
                // Validate: requested quantity vs available
                if ($availableQty <= 0) {
                    $errorMessages[] = "Specification #{$specId}: All units already have SPK (Total: {$specTotalQty}, Existing: {$existingUnits})";
                    log_message('error', "SKIPPED: Specification {$specId} has no available units. Total: {$specTotalQty}, Already created: {$existingUnits}");
                    continue; // Skip - all units already have SPKs
                }
                
                if ($quantity > $availableQty) {
                    log_message('warning', "Specification {$specId} requested {$quantity} units but only {$availableQty} available (Total: {$specTotalQty}, Existing: {$existingUnits})");
                    $quantity = $availableQty; // Adjust to available quantity
                }

                log_message('error', "CHECKPOINT 1: About to build specification JSON for spec {$specId}");

                // Build specification JSON
                $spesifikasiData = [
                    'departemen_id' => $spec['departemen_id'] ?? null,
                    'tipe_unit_id' => $spec['tipe_unit_id'] ?? null,
                    'tipe_jenis' => $spec['jenis_tipe_unit'] ?? null,
                    'merk_unit' => $spec['brand'] ?? null,
                    'model_unit' => $spec['model'] ?? null,
                    'kapasitas_id' => $spec['kapasitas_id'] ?? null,
                    'attachment_tipe' => $spec['attachment_id'] ?? null,
                    'attachment_merk' => null,
                    'jenis_baterai' => $spec['battery_id'] ?? null,
                    'charger_id' => $spec['charger_id'] ?? null,
                    'mast_id' => $spec['mast_id'] ?? null,
                    'ban_id' => $spec['ban_id'] ?? null,
                    'roda_id' => $spec['roda_id'] ?? null,
                    'valve_id' => $spec['valve_id'] ?? null,
                    'aksesoris' => []
                ];

                log_message('error', "CHECKPOINT 2: About to generate SPK number");

                // Generate SPK number
                $nomorSPK = method_exists($this->spkModel, 'generateNextNumber') 
                    ? $this->spkModel->generateNextNumber() 
                    : $this->generateSpkNumber();

                log_message('error', "CHECKPOINT 3: SPK number generated: {$nomorSPK}");

                // Prepare SPK payload - Contract is OPTIONAL (use validContractId)
                $spkPayload = [
                    'nomor_spk' => $nomorSPK,
                    'jenis_spk' => 'UNIT',
                    'kontrak_id' => $validContractId, // NULL if contract doesn't exist or not provided
                    'quotation_specification_id' => $specId,
                    // 'source_type' => $validContractId ? 'CONTRACT' : 'QUOTATION', // Disabled - column doesn't exist yet
                    'jumlah_unit' => $quantity,
                    'po_kontrak_nomor' => $customerInfo['po_kontrak_nomor'],
                    'pelanggan' => $customerInfo['pelanggan'],
                    'pic' => $customerInfo['pic'],
                    'kontak' => $customerInfo['kontak'],
                    'lokasi' => $customerInfo['lokasi'],
                    'delivery_plan' => $deliveryDate,
                    'spesifikasi' => json_encode($spesifikasiData),
                    'catatan' => "Created from Quotation {$quotation['quotation_number']}" . ($validContractId ? "" : " (Contract pending)"),
                    'status' => 'SUBMITTED',
                    'dibuat_oleh' => session('user_id') ?? 1,
                    'dibuat_pada' => date('Y-m-d H:i:s')
                ];

                log_message('error', "CHECKPOINT 4: About to insert SPK with payload: " . json_encode(['nomor' => $spkPayload['nomor_spk'], 'kontrak_id' => $spkPayload['kontrak_id'], 'spec_id' => $spkPayload['quotation_specification_id'], 'pelanggan' => $spkPayload['pelanggan']]));

                // Insert SPK
                try {
                    $insertResult = $this->spkModel->insert($spkPayload);
                    
                    log_message('error', "CHECKPOINT 5: Insert result: " . ($insertResult ? 'SUCCESS' : 'FAILED'));
                    
                    if ($insertResult) {
                        $spkId = $this->spkModel->getInsertID();
                        
                        if ($spkId) {
                            $createdSPKs[] = $spkId;
                            $spkNumbers[] = $nomorSPK;
                            // Queue notification data - will be sent AFTER transaction commits
                            $notificationQueue[] = [
                                'spk_id'           => $spkId,
                                'nomor_spk'        => $nomorSPK,
                                'quotation_id'     => $quotationId,
                                'specification_id' => $specId,
                                'jumlah_unit'      => $quantity,
                                'pelanggan'        => $customerInfo['pelanggan'],
                                'departemen'       => $spec['nama_departemen'] ?? 'N/A',
                                'unit_type'        => $spec['nama_tipe_unit'] ?? 'N/A',
                                'lokasi'           => $customerInfo['lokasi'],
                                'created_by'       => session('username') ?? 'System',
                            ];
                            log_message('info', "SPK created from quotation: {$nomorSPK} (ID: {$spkId})");
                        } else {
                            $errorMessages[] = "Specification #{$specId}: Insert succeeded but no ID returned";
                            log_message('error', "Insert succeeded but getInsertID returned null for spec {$specId}");
                        }
                    } else {
                        $errors = $this->spkModel->errors();
                        $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Unknown insert failure';
                        $errorMessages[] = "Specification #{$specId}: {$errorMsg}";
                        log_message('error', "Insert failed for spec {$specId}: " . json_encode($errors));
                    }
                } catch (\Exception $insertEx) {
                    $errorMessages[] = "Specification #{$specId}: " . $insertEx->getMessage();
                    log_message('error', "Exception during insert for spec {$specId}: " . $insertEx->getMessage());
                }
            }

            if (empty($createdSPKs)) {
                $errorDetail = !empty($errorMessages) ? "\n\nDetails:\n" . implode("\n", $errorMessages) : '';
                throw new \Exception('Failed to create any SPK. Please check your selections.' . $errorDetail);
            }

            // Check if ALL specifications are now fully allocated
            $allSpecsAllocated = $this->checkAllSpecificationsAllocated($quotationId);
            $statusUpdated = false;
            
            if ($allSpecsAllocated) {
                // Mark quotation as SPK created using query builder to avoid "no data to update" error
                $this->db->table('quotations')
                    ->where('id_quotation', $quotationId)
                    ->update([
                        'spk_created' => 1,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                $statusUpdated = true;
                log_message('info', "Quotation {$quotationId} marked with spk_created=1 - all specifications have SPKs");
            }
            
            $commitOk = $this->db->transComplete();

            if (!$commitOk) {
                throw new \Exception('Transaction failed to commit. Please try again.');
            }

            // --- POST-TRANSACTION: notifications & activity logs ---
            // These run OUTSIDE the transaction so any failure here never rolls back the SPK insert.
            foreach ($notificationQueue as $n) {
                $this->sendSpkNotification($n['nomor_spk'], $n);
                $this->logCreate('spk', $n['spk_id'], [
                    'spk_id'           => $n['spk_id'],
                    'nomor_spk'        => $n['nomor_spk'],
                    'quotation_id'     => $n['quotation_id'],
                    'specification_id' => $n['specification_id'],
                    'jumlah_unit'      => $n['jumlah_unit'],
                ]);
            }

            $message = count($createdSPKs) . ' SPK(s) created successfully! Numbers: ' . implode(', ', $spkNumbers);
            if ($statusUpdated) {
                $message .= ' | ✅ Quotation marked as CLOSED (all specifications completed)';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'spk_count' => count($createdSPKs),
                'spk_numbers' => $spkNumbers,
                'spk_ids' => $createdSPKs,
                'all_specs_allocated' => $allSpecsAllocated,
                'status_updated' => $statusUpdated,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            if ($this->db->transDepth > 0) {
                $this->db->transRollback();
            }
            log_message('error', 'createSPKFromQuotation failed: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Create SPK directly without going through the quotation flow.
     * Accepts spec fields directly in the request body and sets source_type = 'DIRECT'.
     */
    public function createDirectSPK()
    {
        $this->db = \Config\Database::connect();

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        $this->db->transStart();

        try {
            $data = $this->request->getJSON(true) ?: $this->request->getPost();

            $customerId         = $data['customer_id'] ?? null;
            $customerLocationId = $data['customer_location_id'] ?? null;
            $contractId         = $data['contract_id'] ?? null;
            $deliveryDate       = $data['delivery_date'] ?? null;
            $jumlahUnit         = max(1, (int)($data['jumlah_unit'] ?? 1));
            $jenisSpk           = in_array($data['jenis_spk'] ?? '', ['UNIT','ATTACHMENT']) ? $data['jenis_spk'] : 'UNIT';
            $catatan            = $data['catatan'] ?? '';
            $notes              = $data['notes'] ?? ''; // merged userNotes + [OPTIMA_SPEC_TECH] block from JS

            // ── Resolve quotation-style brand_id → merk_unit / model_unit ──────
            $merkUnit  = $data['merk_unit']  ?? null;
            $modelUnit = $data['model_unit'] ?? null;
            if (!empty($data['brand_id'])) {
                $brandRow = $this->db->table('model_unit')
                    ->where('id_model_unit', (int) $data['brand_id'])
                    ->get()->getRowArray();
                if ($brandRow) {
                    $merkUnit  = $brandRow['merk_unit']  ?? $merkUnit;
                    $modelUnit = $brandRow['model_unit'] ?? $modelUnit;
                }
            }

            // ── Resolve attachment_id → attachment_tipe / attachment_merk ────
            $forkAttachType  = $data['fork_attach_type'] ?? 'none';
            $attachmentTipe  = $data['attachment_tipe']  ?? null;
            $attachmentMerk  = $data['attachment_merk']  ?? null;
            $forkId          = null;
            if ($forkAttachType === 'attachment' && !empty($data['attachment_id'])) {
                $attRow = $this->db->table('attachment')
                    ->where('id_attachment', (int) $data['attachment_id'])
                    ->get()->getRowArray();
                if ($attRow) {
                    $attachmentTipe = $attRow['tipe'] ?? $attRow['nama'] ?? null;
                    $attachmentMerk = $attRow['merk'] ?? null;
                }
            } elseif ($forkAttachType === 'fork' && !empty($data['fork_id'])) {
                $forkId = (int) $data['fork_id'];
            } else {
                $attachmentTipe = null;
                $attachmentMerk = null;
            }

            // ── Spec fields ──────────────────────────────────────────────────
            $spesifikasiData = [
                'departemen_id'  => $data['departemen_id']  ?? null,
                'tipe_unit_id'   => $data['tipe_unit_id']   ?? null,
                'tipe_jenis'     => $data['tipe_jenis']     ?? null,
                'merk_unit'      => $merkUnit,
                'model_unit'     => $modelUnit,
                'kapasitas_id'   => $data['kapasitas_id']   ?? null,
                'attachment_tipe'=> $attachmentTipe,
                'attachment_merk'=> $attachmentMerk,
                'jenis_baterai'  => $data['jenis_baterai']  ?? null,
                'charger_id'     => $data['charger_id']     ?? null,
                'mast_id'        => $data['mast_id']        ?? null,
                'ban_id'         => $data['ban_id']         ?? null,
                'roda_id'        => $data['roda_id']        ?? null,
                'valve_id'       => $data['valve_id']       ?? null,
                'fork_id'        => $forkId,
                'notes'          => $notes,
                'aksesoris'      => [],
            ];

            // Validate
            if (!$customerId) {
                throw new \Exception('Customer wajib dipilih');
            }
            if (!$deliveryDate) {
                throw new \Exception('Delivery date wajib diisi');
            }
            if (!$spesifikasiData['departemen_id']) {
                throw new \Exception('Departemen wajib dipilih');
            }
            if (!$spesifikasiData['tipe_unit_id']) {
                throw new \Exception('Tipe unit wajib dipilih');
            }

            // Resolve customer info
            $locationRow = null;
            if ($customerLocationId) {
                $locationRow = $this->db->table('customer_locations')
                    ->where('id', $customerLocationId)
                    ->where('customer_id', $customerId)
                    ->get()->getRowArray();
            }

            $customerRow = $this->db->table('customers')
                ->where('id', $customerId)
                ->get()->getRowArray();

            if (!$customerRow) {
                throw new \Exception('Customer tidak ditemukan');
            }

            // Contract info (optional)
            $contractRow   = null;
            $poKontrakNomor = null;
            if ($contractId) {
                $contractRow = $this->db->table('kontrak')
                    ->where('id', $contractId)
                    ->where('customer_id', $customerId)
                    ->get()->getRowArray();
                if ($contractRow) {
                    $poKontrakNomor = $contractRow['no_kontrak'] ?? null;
                }
            }

            $pelanggan = $customerRow['customer_name'];
            $pic       = $locationRow['contact_person'] ?? ($contractRow['pic'] ?? null);
            $kontak    = $locationRow['phone']          ?? ($contractRow['kontak'] ?? null);
            $lokasi    = $locationRow
                ? (($locationRow['location_name'] ?? '') . ($locationRow['address'] ? ' - ' . $locationRow['address'] : ''))
                : ($contractRow['lokasi'] ?? null);

            $nomorSPK = $this->spkModel->generateNextNumber();

            $spkPayload = [
                'nomor_spk'                  => $nomorSPK,
                'jenis_spk'                  => $jenisSpk,
                'kontrak_id'                 => $contractRow ? $contractId : null,
                'quotation_specification_id' => null,
                'source_type'                => 'DIRECT',
                'customer_id'                => (int)$customerId,
                'jumlah_unit'                => $jumlahUnit,
                'po_kontrak_nomor'           => $poKontrakNomor,
                'pelanggan'                  => $pelanggan,
                'pic'                        => $pic,
                'kontak'                     => $kontak,
                'lokasi'                     => $lokasi,
                'delivery_plan'              => $deliveryDate,
                'spesifikasi'                => json_encode($spesifikasiData),
                'catatan'                    => $catatan ?: 'Dibuat langsung (Direct SPK)',
                'status'                     => 'SUBMITTED',
                'dibuat_oleh'                => session('user_id') ?? 1,
                'dibuat_pada'                => date('Y-m-d H:i:s'),
            ];

            $insertResult = $this->spkModel->insert($spkPayload);
            if (!$insertResult) {
                $errors = $this->spkModel->errors();
                throw new \Exception(!empty($errors) ? implode(', ', $errors) : 'Gagal menyimpan SPK');
            }

            $spkId = $this->spkModel->getInsertID();
            $this->db->transComplete();

            if (!$this->db->transStatus()) {
                throw new \Exception('Transaksi gagal. Silakan coba lagi.');
            }

            // Activity log (outside transaction)
            $this->logCreate('spk', $spkId, [
                'spk_id'     => $spkId,
                'nomor_spk'  => $nomorSPK,
                'source_type'=> 'DIRECT',
                'customer_id'=> $customerId,
                'jumlah_unit'=> $jumlahUnit,
            ]);

            return $this->response->setJSON([
                'success'    => true,
                'message'    => "SPK berhasil dibuat: {$nomorSPK}",
                'spk_id'     => $spkId,
                'spk_number' => $nomorSPK,
                'csrf_hash'  => csrf_hash(),
            ]);

        } catch (\Exception $e) {
            if ($this->db->transDepth > 0) {
                $this->db->transRollback();
            }
            log_message('error', 'createDirectSPK failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'csrf_hash' => csrf_hash(),
            ]);
        }
    }

    /**
     * Check if all specifications in a quotation are fully allocated with SPKs
     * 
     * @param int $quotationId
     * @return bool True if all specs are allocated, false otherwise
     */
    private function checkAllSpecificationsAllocated($quotationId)
    {
        try {
            // Get all specifications for this quotation
            $specifications = $this->db->table('quotation_specifications')
                ->select('id_specification, quantity')
                ->where('id_quotation', $quotationId)
                ->get()
                ->getResultArray();
            
            if (empty($specifications)) {
                return false; // No specifications = not allocated
            }
            
            // Check each specification
            foreach ($specifications as $spec) {
                // Count existing SPK units for this specification
                $existingSPKs = $this->db->table('spk')
                    ->selectSum('jumlah_unit', 'total_units')
                    ->where('quotation_specification_id', $spec['id_specification'])
                    ->where('status !=', 'CANCELLED')
                    ->get()
                    ->getRowArray();
                
                $existingUnits = (int)($existingSPKs['total_units'] ?? 0);
                $totalQty = (int)($spec['quantity'] ?? 0);
                
                // If any specification still has available units, return false
                if ($existingUnits < $totalQty) {
                    log_message('info', "Spec {$spec['id_specification']} not fully allocated: {$existingUnits}/{$totalQty}");
                    return false;
                }
            }
            
            // All specifications are fully allocated
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'checkAllSpecificationsAllocated failed: ' . $e->getMessage());
            return false;
        }
    }

    private function sendSpkNotification($nomorSpk, $spkData = [])
    {
        try {
            // Load notification helper
            helper('notification');
            
            $spkId = $spkData['spk_id'] ?? $spkData['id'] ?? null;
            $createdBy = $spkData['created_by']
                ?? session()->get('username')
                ?? session()->get('first_name')
                ?? 'System';

            // Prepare event data for notification
            $eventData = [
                'nomor_spk'  => $nomorSpk,
                'id'         => $spkId,
                'pelanggan'  => $spkData['pelanggan'] ?? 'N/A',
                'departemen' => $spkData['departemen'] ?? $spkData['department'] ?? 'N/A',
                'unit_no'    => $spkData['unit_no'] ?? $spkData['unit_type'] ?? $spkData['no_unit'] ?? 'N/A',
                'no_unit'    => $spkData['no_unit'] ?? $spkData['unit_no'] ?? $spkData['unit_type'] ?? 'N/A',
                'lokasi'     => $spkData['lokasi'] ?? 'N/A',
                'created_by' => $createdBy,
                'url'        => $spkData['url'] ?? base_url('service/spk/detail/' . ($spkId ?? '')),
            ];
            
            // Send notification using helper function
            $result = notify_spk_created($eventData);
            
            if ($result && isset($result['notifications_sent'])) {
                log_message('info', "SPK Notification sent: {$result['notifications_sent']} notifications for SPK {$nomorSpk}");
            }
            
        } catch (\Throwable $e) {
            // Silent fail; notifications are optional
            log_message('error', 'SPK Notification failed: ' . $e->getMessage());
        }
    }

    // Generic options endpoint for SPK specifications
    public function specOptions()
    {
        $type = trim($this->request->getGet('type') ?? '');
        // Predefined simple maps
        $map = [
            'departemen'      => ['table'=>'departemen','id'=>'id_departemen','name'=>'nama_departemen','order'=>'nama_departemen'],
            'tipe_unit'       => null, // Special handling for DISTINCT tipe
            'jenis_unit'      => null, // DISTINCT jenis from tipe_unit filtered by tipe_unit_id
            'kapasitas'       => ['table'=>'kapasitas','id'=>'id_kapasitas','name'=>'kapasitas_unit','order'=>'kapasitas_unit'],
            'mast'            => ['table'=>'tipe_mast','id'=>'id_mast','name'=>'tipe_mast','order'=>'tipe_mast'],
            'ban'             => ['table'=>'tipe_ban','id'=>'id_ban','name'=>'tipe_ban','order'=>'tipe_ban'],
            'charger'         => ['table'=>'charger','id'=>'id_charger','name'=>"CONCAT(merk_charger, ' - ', tipe_charger)",'order'=>'merk_charger'],
            'baterai'         => ['table'=>'baterai','id'=>'id','name'=>"CONCAT(merk_baterai, ' ', tipe_baterai)",'order'=>'merk_baterai'],
            'attachment'      => ['table'=>'attachment','id'=>'id_attachment','name'=>"CONCAT(merk, ' ', model)",'order'=>'merk'],
            'attachment_merk' => null, // DISTINCT merk from attachment
            // New simplified request types
            'tipe_jenis'      => null, // DISTINCT jenis from tipe_unit
            'merk_unit'       => null, // DISTINCT merk_unit from model_unit
            'valve'           => null, // valve.id_valve, valve.jumlah_valve
            'jenis_baterai'   => null, // DISTINCT jenis_baterai from baterai
            'attachment_tipe' => null, // DISTINCT tipe from attachment
            'roda'            => null, // jenis_roda.id_roda, jenis_roda.tipe_roda
            'mast_model'      => null, // DISTINCT tipe_mast
            'mast_height'     => null, // Rows by selected model mast
        ];

        // Handle special DISTINCT cases and departmental filtering
        if ($type === 'tipe_unit') {
            // Get DISTINCT tipe names with MIN(id) to avoid duplicates in UI
            $rows = $this->db->table('tipe_unit')
                ->select('MIN(id_tipe_unit) as id, TRIM(tipe) as name', false)
                ->where('tipe IS NOT NULL', null, false)
                ->where("TRIM(tipe) <> ''", null, false)
                ->groupBy('TRIM(tipe)')
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }

        if ($type === 'jenis_unit') {
            $tipeUnit = trim($this->request->getGet('parent_tipe') ?? '');
            $builder = $this->db->table('tipe_unit')
                ->select('DISTINCT TRIM(jenis) as name', false)
                ->where('jenis IS NOT NULL', null, false)
                ->where("TRIM(jenis) <> ''", null, false);
            
            if ($tipeUnit !== '') {
                $builder->where('tipe', $tipeUnit);
            }
            
            $rows = $builder->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            
            // map id = name for simple string options
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }

        // Check departemen for baterai/charger locking
        if ($type === 'baterai' || $type === 'charger') {
            $departemenId = trim($this->request->getGet('departemen_id') ?? '');
            
            // Check if departemen is Electric
            $isElectric = false;
            if ($departemenId !== '') {
                $dept = $this->db->table('departemen')
                    ->select('nama_departemen')
                    ->where('id_departemen', $departemenId)
                    ->get()->getRowArray();
                
                if ($dept && (stripos($dept['nama_departemen'], 'electric') !== false || 
                             stripos($dept['nama_departemen'], 'listrik') !== false)) {
                    $isElectric = true;
                }
            }
            
            // If not electric, return empty array
            if (!$isElectric) {
                return $this->response->setJSON(['success'=>true,'data'=>[],'csrf_hash'=>csrf_hash()]);
            }
        }

        if ($type === 'tipe_jenis') {
            $rows = $this->db->table('tipe_unit')
                ->select('DISTINCT TRIM(jenis) as name', false)
        ->where('jenis IS NOT NULL', null, false)
                ->where("TRIM(jenis) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            // map id = name for simple string options
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'merk_unit') {
            $departemenId = trim($this->request->getGet('departemen_id') ?? '');
            // Return FK ID for brand
            $builder = $this->db->table('model_unit')
                ->select('id_model_unit as id, CONCAT(merk_unit, " - ", model_unit) as name')
                ->where('merk_unit IS NOT NULL', null, false)
                ->where("TRIM(merk_unit) <> ''", null, false)
                ->orderBy('merk_unit, model_unit','ASC')
                ->limit(200);
            if ($departemenId !== '') {
                $builder->where('departemen_id', (int)$departemenId);
            }
            $rows = $builder->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'mast_model') {
            $rows = $this->db->table('tipe_mast')
                ->select('MIN(id_mast) as id, TRIM(tipe_mast) as name', false)
                ->where('tipe_mast IS NOT NULL', null, false)
                ->where("TRIM(tipe_mast) <> ''", null, false)
                ->groupBy('TRIM(tipe_mast)')
                ->orderBy('name', 'ASC')
                ->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'mast_height') {
            $mastModel = trim($this->request->getGet('mast_model') ?? '');
            $builder = $this->db->table('tipe_mast')
                ->select('id_mast as id, CONCAT(TRIM(tipe_mast), " - ", COALESCE(NULLIF(TRIM(tinggi_mast), ""), "-")) as name', false)
                ->where('tipe_mast IS NOT NULL', null, false)
                ->where("TRIM(tipe_mast) <> ''", null, false);
            if ($mastModel !== '') {
                $builder->where('TRIM(tipe_mast)', $mastModel);
            }
            $rows = $builder
                ->orderBy('tinggi_mast', 'ASC')
                ->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'battery' || $type === 'jenis_baterai') {
            // Return FK ID for battery
            $rows = $this->db->table('baterai')
                ->select('id, CONCAT(merk_baterai, " - ", tipe_baterai, " (", jenis_baterai, ")") as name')
                ->where('jenis_baterai IS NOT NULL', null, false)
                ->orderBy('merk_baterai, tipe_baterai','ASC')
                ->limit(200)
                ->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'attachment' || $type === 'attachment_tipe') {
            // Return FK ID for attachment
            $rows = $this->db->table('attachment')
                ->select('id_attachment as id, CONCAT(tipe, " - ", merk, " ", model) as name')
                ->where('tipe IS NOT NULL', null, false)
                ->orderBy('tipe, merk, model','ASC')
                ->limit(200)
                ->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'valve') {
            $rows = $this->db->table('valve')
                ->select('id_valve as id, jumlah_valve as name')
                ->orderBy('jumlah_valve','ASC')
                ->limit(200)
                ->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'roda') {
            $rows = $this->db->table('jenis_roda')
                ->select('id_roda as id, tipe_roda as name')
                ->orderBy('tipe_roda','ASC')
                ->limit(200)
                ->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'mast') {
            $rows = $this->db->table('tipe_mast')
                ->select('id_mast as id, CONCAT(TRIM(tipe_mast), " - ", COALESCE(NULLIF(TRIM(tinggi_mast), ""), "-")) as name', false)
                ->where('tipe_mast IS NOT NULL', null, false)
                ->where("TRIM(tipe_mast) <> ''", null, false)
                ->orderBy('tipe_mast', 'ASC')
                ->orderBy('tinggi_mast', 'ASC')
                ->get()->getResultArray();
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }

        // Fallback to table/column map for legacy/spec detail options
        if (!isset($map[$type])) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Unknown type','csrf_hash'=>csrf_hash()]);
        }
        $cfg = $map[$type];
        
        // Special handling for charger based on departemen filtering
        if ($type === 'charger') {
            $departemenId = trim($this->request->getGet('departemen_id') ?? '');
            
            // Check if departemen is Electric first
            $isElectric = false;
            if ($departemenId !== '') {
                $dept = $this->db->table('departemen')
                    ->select('nama_departemen')
                    ->where('id_departemen', $departemenId)
                    ->get()->getRowArray();
                
                if ($dept && (stripos($dept['nama_departemen'], 'electric') !== false || 
                             stripos($dept['nama_departemen'], 'listrik') !== false)) {
                    $isElectric = true;
                }
            }
            
            // If not electric, return empty array
            if (!$isElectric) {
                return $this->response->setJSON(['success'=>true,'data'=>[],'csrf_hash'=>csrf_hash()]);
            }
        }
        
        $builder = $this->db->table($cfg['table'])
            ->select($cfg['id'].' as id')
            ->select($cfg['name'].' as name', false)
            ->orderBy($cfg['order'],'ASC')
            ->limit(200);
        $rows = $builder->get()->getResultArray();
        return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
    }

    /**
     * Get fork list for quotation spec modal
     * Route: GET marketing/forks
     */
    public function getForks()
    {
        try {
            $rows = $this->db->table('fork')
                ->select('id, name, length_mm, fork_class, capacity_kg')
                ->orderBy('name', 'ASC')
                ->get()->getResultArray();

            $data = array_map(function ($r) {
                $label = $r['name'];
                if (!empty($r['capacity_kg'])) {
                    $label .= ' (' . $r['capacity_kg'] . 'kg)';
                }
                return ['id' => $r['id'], 'name' => $label];
            }, $rows);

            return $this->response->setJSON(['success' => true, 'data' => $data, 'csrf_hash' => csrf_hash()]);
        } catch (\Exception $e) {
            log_message('error', 'getForks error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'data' => [], 'csrf_hash' => csrf_hash()]);
        }
    }

    /**
     * Create SPK from Quotation Specifications.
     * Requires structured specification rows (departemen_id, tipe_unit_id, etc.); free-text-only specs would break SPK/unit rules.
     */
    public function createFromQuotation()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $this->db->transBegin();

        try {
            $requestData = $this->request->getJSON(true);
            $quotationId = $requestData['quotation_id'] ?? null;

            if (!$quotationId) {
                throw new \Exception('ID Quotation tidak tersedia');
            }

            // Get quotation data with contract info
            $quotation = $this->quotationModel->getQuotationWithContract($quotationId);
            if (!$quotation) {
                throw new \Exception('Quotation tidak ditemukan');
            }

            if ($quotation['workflow_stage'] !== 'DEAL') {
                throw new \Exception('Hanya quotation dengan status DEAL yang dapat dibuat SPK');
            }

            // CONTRACT NOW OPTIONAL - Can be NULL, will be linked later
            // No need to enforce contract requirement here
            $contractId = $quotation['created_contract_id'] ?? null;

            // Get all specifications from quotation
            $specifications = $this->quotationSpecificationModel->where('id_quotation', $quotationId)
                                                               ->where('is_active', 1)
                                                               ->findAll();

            if (empty($specifications)) {
                throw new \Exception('Tidak ada spesifikasi aktif dalam quotation');
            }

            $spkCount = 0;
            $spkNumbers = [];

            // Create SPK for each specification
            foreach ($specifications as $spec) {
                $jenis_spk = !empty($spec['attachment_tipe']) ? 'ATTACHMENT' : 'UNIT';
                
                // Build specification data
                $specData = [
                    'departemen_id' => $spec['departemen_id'],
                    'tipe_unit_id' => $spec['tipe_unit_id'],
                    'equipment_type' => $spec['equipment_type'],
                    'brand' => $spec['brand'],
                    'model' => $spec['model'],
                    'kapasitas_id' => $spec['kapasitas_id'],
                    'attachment_tipe' => $spec['attachment_tipe'],
                    'attachment_merk' => $spec['attachment_merk'],
                    'jenis_baterai' => $spec['jenis_baterai'],
                    'charger_id' => $spec['charger_id'],
                    'mast_id' => $spec['mast_id'],
                    'ban_id' => $spec['ban_id'],
                    'roda_id' => $spec['roda_id'],
                    'valve_id' => $spec['valve_id'],
                    'specifications' => $spec['specifications'],
                    'service_scope' => $spec['service_scope'],
                    'notes' => $spec['notes']
                ];

                $spkData = [
                    'nomor_spk' => method_exists($this->spkModel, 'generateNextNumber') ? $this->spkModel->generateNextNumber() : $this->generateSpkNumber(),
                    'jenis_spk' => $jenis_spk,
                    'kontrak_id' => $contractId, // Can be NULL - contract is optional
                    'quotation_specification_id' => $spec['id_specification'], // New field to link to quotation_specifications
                    'jumlah_unit' => $spec['quantity'],
                    'po_kontrak_nomor' => $quotation['quotation_number'],
                    'pelanggan' => $quotation['customer_name'],
                    'pic' => $quotation['contact_person'] ?? '',
                    'kontak' => $quotation['phone'] ?? '',
                    'lokasi' => $quotation['location_name'] ?? '',
                    'delivery_plan' => null,
                    'spesifikasi' => json_encode($specData),
                    'catatan' => $spec['notes'],
                    'status' => 'SUBMITTED',
                    'dibuat_oleh' => session('user_id') ?: 1
                ];

                $spkId = $this->spkModel->insert($spkData);
                if (!$spkId) {
                    throw new \Exception('Gagal membuat SPK untuk spesifikasi: ' . $spec['specification_name']);
                }

                $spkNumbers[] = $spkData['nomor_spk'];
                $spkCount++;

                // Log activity
                $this->logActivity('spk_created_from_quotation', 'spk', $spkId, 
                    'SPK created from quotation: ' . $spkData['nomor_spk'] . ' for quotation: ' . $quotation['quotation_number'], [
                    'spk_number' => $spkData['nomor_spk'],
                    'quotation_id' => $quotationId,
                    'quotation_number' => $quotation['quotation_number'],
                    'specification_name' => $spec['specification_name']
                ]);
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil membuat {$spkCount} SPK dari quotation",
                'spk_count' => $spkCount,
                'spk_numbers' => $spkNumbers,
                'spk_number' => $spkNumbers[0] ?? null
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'SPK Creation from Quotation failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Update SPK data (full update)
     */
    public function spkUpdate($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $id = (int) $id;
        
        // Debug: Log all received data
        log_message('debug', 'SPK Update - Raw input: ' . json_encode($this->request->getRawInput()));
        log_message('debug', 'SPK Update - POST data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'SPK Update - PUT data: ' . json_encode($this->request->getVar()));
        
        // Try multiple ways to get data (PUT method compatibility)
        $data = [
            'jenis_spk' => $this->request->getPost('jenis_spk') ?: $this->request->getVar('jenis_spk'),
            'po_kontrak_nomor' => $this->request->getPost('po_kontrak_nomor') ?: $this->request->getVar('po_kontrak_nomor'),
            'pelanggan' => $this->request->getPost('pelanggan') ?: $this->request->getVar('pelanggan'),
            'pic' => $this->request->getPost('pic') ?: $this->request->getVar('pic'),
            'kontak' => $this->request->getPost('kontak') ?: $this->request->getVar('kontak'),
            'lokasi' => $this->request->getPost('lokasi') ?: $this->request->getVar('lokasi'),
            'delivery_plan' => $this->request->getPost('delivery_plan') ?: $this->request->getVar('delivery_plan'),
            'status' => $this->request->getPost('status') ?: $this->request->getVar('status'),
            'catatan' => $this->request->getPost('catatan') ?: $this->request->getVar('catatan'),
            'diperbarui_pada' => date('Y-m-d H:i:s')
        ];

        // Debug: Log processed data
        log_message('debug', 'SPK Update - Processed data: ' . json_encode($data));

        // Validate required fields
        if (empty($data['jenis_spk']) || empty($data['pelanggan'])) {
            log_message('error', 'SPK Update - Validation failed. Data: ' . json_encode($data));
            return $this->response->setJSON(['success'=>false,'message'=>'Jenis SPK dan Pelanggan wajib diisi.']);
        }

        // Validate status
        $allowedStatuses = ['DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED'];
        if (!in_array($data['status'], $allowedStatuses, true)) {
            return $this->response->setJSON(['success'=>false,'message'=>'Status tidak valid']);
        }

        // Get current SPK data for rollback validation
        $currentSpk = $this->db->table('spk')->where('id', $id)->get()->getRowArray();
        if (!$currentSpk) {
            return $this->response->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']);
        }

        $oldStatus = $currentSpk['status'];
        $newStatus = $data['status'];

        // Business logic for status changes
        if ($oldStatus !== $newStatus) {
            // Log status change
            $this->logStatusChange($id, $oldStatus, $newStatus, 'SPK updated via Marketing');
            
            // Handle rollback from READY to IN_PROGRESS
            if ($oldStatus === 'READY' && $newStatus === 'IN_PROGRESS') {
                // This is a rollback - reset approval stages
                $this->handleSpkRollback($id);
            }
        }

        // Update SPK
        $result = $this->db->table('spk')->where('id', $id)->update($data);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'SPK berhasil diperbarui',
                'data' => ['id' => $id, 'status' => $newStatus]
            ]);
        } else {
            return $this->response->setJSON(['success'=>false,'message'=>'Gagal memperbarui SPK']);
        }
    }

    /**
     * Handle SPK rollback from READY to IN_PROGRESS
     * IMPORTANT: Marketing rollback should NOT reset approval stages
     * Service should handle granular rollback of specific stages/units
     */
    private function handleSpkRollback($spkId)
    {
        try {
            // Marketing rollback should ONLY change status, NOT reset approval stages
            // This allows Service to do granular rollback of specific stages/units
            
            // Log rollback action
            $this->db->table('spk_rollback_log')->insert([
                'spk_id' => $spkId,
                'stage' => 'status_rollback',
                'action' => 'MARKETING_ROLLBACK',
                'old_data' => json_encode(['status' => 'READY']),
                'new_data' => json_encode(['status' => 'IN_PROGRESS']),
                'reason' => 'Marketing rollback from READY to IN_PROGRESS - Status only, approval stages preserved',
                'rolled_back_by' => session('user_id') ?: 1
            ]);

            // IMPORTANT: Clear prepared_units from spesifikasi JSON to avoid confusion
            // This prevents "Unit 2 dari 1" issue when Service tries to prepare units again
            $spk = $this->db->table('spk')->where('id', $spkId)->get()->getRowArray();
            if ($spk && !empty($spk['spesifikasi'])) {
                $spec = json_decode($spk['spesifikasi'], true);
                if (is_array($spec)) {
                    // Clear prepared_units to start fresh
                    unset($spec['prepared_units']);
                    unset($spec['fabrikasi_last']);
                    
                    // Update spesifikasi
                    $this->db->table('spk')->where('id', $spkId)->update([
                        'spesifikasi' => json_encode($spec),
                        'diperbarui_pada' => date('Y-m-d H:i:s')
                    ]);
                    
                    log_message('info', "SPK $spkId: Cleared prepared_units and fabrikasi_last from spesifikasi after Marketing rollback");
                }
            }

            log_message('info', "Marketing rollback for SPK $spkId: Status changed to IN_PROGRESS, approval stages preserved, spesifikasi cleared");

        } catch (\Exception $e) {
            log_message('error', 'SPK rollback failed: ' . $e->getMessage());
        }
    }

    /**
     * Log status change
     */
    private function logStatusChange($spkId, $fromStatus, $toStatus, $note = null)
    {
        try {
            $this->db->table('spk_status_history')->insert([
                'spk_id' => (int)$spkId,
                'status_from' => $fromStatus,
                'status_to' => $toStatus,
                'changed_by' => session('user_id') ?: 1,
                'note' => $note,
                'changed_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Status change log failed: ' . $e->getMessage());
        }
    }

    public function spkUpdateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        $status = $this->request->getPost('status');
        $allowed = ['DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED'];
        if (!in_array($status,$allowed,true)) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Status tidak valid']);
        }
        
        // Get current SPK data
        $currentSpk = $this->db->table('spk')->select('*')->where('id',$id)->get()->getRowArray();
        if (!$currentSpk) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']);
        }
        
        $oldStatus = $currentSpk['status'];
        
        // Update status
        $this->db->table('spk')->where('id',$id)->update(['status'=>$status,'diperbarui_pada'=>date('Y-m-d H:i:s')]);
        
        // Log status history (best-effort)
        if ($oldStatus) {
            $this->db->table('spk_status_history')->insert([
                'spk_id' => (int)$id,
                'status_from' => $oldStatus,
                'status_to' => $status,
                'changed_by' => session('user_id') ?: 1,
                'note' => null,
                'changed_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        // Log activity using trait
        try {
            $this->logUpdate('spk', $id, ['status' => $oldStatus], ['status' => $status], [
                'description' => "Mengubah status SPK {$currentSpk['nomor_spk']} dari {$oldStatus} ke {$status}",
                'workflow_stage' => 'STATUS_CHANGED',
                'business_impact' => 'HIGH',
                'relations' => [
                    'spk' => [$id],
                    'kontrak' => [$currentSpk['kontrak_id']]
                ]
            ]);
        } catch (\Exception $logError) {
            log_message('error', 'Failed to log SPK status update: ' . $logError->getMessage());
        }
        
        return $this->response->setJSON(['success'=>true,'message'=>'Status diperbarui','csrf_hash'=>csrf_hash()]);
    }

    private function generateSpkNumber(): string
    {
        $prefix = 'SPK/'.date('Ym').'/';
        $last = $this->db->table('spk')->like('nomor_spk',$prefix)->orderBy('id','DESC')->get()->getRowArray();
        $seq = 1;
        if ($last && isset($last['nomor_spk'])) {
            $parts = explode('/', $last['nomor_spk']);
            $seq = isset($parts[2]) ? (int)$parts[2] + 1 : 1;
        }
        return $prefix . str_pad((string)$seq,3,'0',STR_PAD_LEFT);
    }

    private function generateDiNumber(): string
    {
        $prefix = 'DI/'.date('Ym').'/';
        $last = $this->db->table('delivery_instructions')->like('nomor_di',$prefix)->orderBy('id','DESC')->get()->getRowArray();
        $seq = 1;
        if ($last && isset($last['nomor_di'])) {
            $parts = explode('/', $last['nomor_di']);
            $seq = isset($parts[2]) ? (int)$parts[2] + 1 : 1;
        }
        return $prefix . str_pad((string)$seq,3,'0',STR_PAD_LEFT);
    }

    public function diCreate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        
        // Check what database we're actually connected to
        try {
            $dbConfig = $this->db->getDatabase();
            error_log('DI Create - Connected database: ' . $dbConfig);
            
            // Also check if we can actually talk to the DB by testing a simple query
            $testQuery = $this->db->query('SELECT VERSION() as version');
            $dbVersion = $testQuery->getRowArray();
            if ($dbVersion) {
                error_log('MySQL Version: ' . ($dbVersion['version'] ?? 'unknown'));
            }
        } catch (\Exception $e) {
            error_log('Database check error: ' . $e->getMessage());
        }
        
        // Debug logging
        error_log('DI Create Request - POST data: ' . print_r($this->request->getPost(), true));
        
        $spkId = (int)($this->request->getPost('spk_id') ?? 0);
        $poNo = trim((string)($this->request->getPost('po_kontrak_nomor') ?? ''));
        $tanggalKirim = $this->request->getPost('tanggal_kirim') ?: null;
        $catatan = $this->request->getPost('catatan') ?: null;

        // Workflow fields
        $jenisPerintahKerjaId = (int)($this->request->getPost('jenis_perintah_kerja_id') ?? 0);
        $tujuanPerintahKerjaId = (int)($this->request->getPost('tujuan_perintah_kerja_id') ?? 0);

        $pelanggan = $this->request->getPost('pelanggan') ?: '';
        $lokasi = $this->request->getPost('lokasi') ?: null;
        $customerLocationId = (int)($this->request->getPost('customer_location_id') ?? 0) ?: null;

    // units selected for this DI (allow multiple)
    $unitIds = $this->request->getPost('unit_ids');
    if (is_string($unitIds)) { $unitIds = [$unitIds]; }
    if (!is_array($unitIds)) { $unitIds = []; }
    $unitIds = array_values(array_unique(array_filter(array_map('intval', $unitIds))));

    // Fallback: TARIK workflow uses tarik_units[] instead of unit_ids[]
    if (empty($unitIds)) {
        $tarikUnits = $this->request->getPost('tarik_units');
        if (is_string($tarikUnits)) { $tarikUnits = [$tarikUnits]; }
        if (is_array($tarikUnits)) {
            $unitIds = array_values(array_unique(array_filter(array_map('intval', $tarikUnits))));
        }
    }

    error_log('DI Create Parsed Inputs: spk_id=' . $spkId . ', po=' . $poNo . ', tanggal_kirim=' . ($tanggalKirim ?: '-') . ', unit_ids=' . json_encode($unitIds));

    $selected = ['unit_id'=>null,'inventory_attachment_id'=>null];
        if ($spkId > 0) {
            // Ensure SPK is READY (Service has assigned items)
            $spk = $this->db->table('spk')->where('id',$spkId)->get()->getRowArray();
            if (!$spk) {
                error_log("DI Create Error: SPK not found with ID: $spkId");
                return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']);
            }
            if ($spk['status'] !== 'READY') {
                error_log("DI Create Error: SPK status is '{$spk['status']}', not READY");
                return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'SPK belum READY']);
            }
            $poNo = $spk['po_kontrak_nomor'];
            $pelanggan = $spk['pelanggan'];
            $lokasi = $spk['lokasi'];
            
            // ENHANCEMENT: Detect SPK type for optimized DI handling
            $isAttachmentSpk = (isset($spk['jenis_spk']) && strtoupper($spk['jenis_spk']) === 'ATTACHMENT');
            error_log('DI Create - SPK Type: ' . ($spk['jenis_spk'] ?? 'UNKNOWN') . ', isAttachmentSpk: ' . ($isAttachmentSpk ? 'YES' : 'NO'));
            
            // Extract prepared units from SPK spesifikasi if no units provided
            if (empty($unitIds) && !empty($spk['spesifikasi'])) {
                $spec = json_decode($spk['spesifikasi'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($spec)) {
                    // Get units from prepared_units array
                    if (isset($spec['prepared_units']) && is_array($spec['prepared_units'])) {
                        foreach ($spec['prepared_units'] as $preparedUnit) {
                            if (isset($preparedUnit['unit_id']) && is_numeric($preparedUnit['unit_id'])) {
                                $unitIds[] = (int)$preparedUnit['unit_id'];
                            }
                        }
                        error_log('DI Create - Extracted unit IDs from SPK prepared_units: ' . json_encode($unitIds));
                        
                        // ENHANCEMENT: For ATTACHMENT SPK, extract attachment_inventory_id from prepared_units
                        if ($isAttachmentSpk && empty($unitIds)) {
                            foreach ($spec['prepared_units'] as $preparedUnit) {
                                if (isset($preparedUnit['attachment_inventory_id']) && is_numeric($preparedUnit['attachment_inventory_id'])) {
                                    $selected['inventory_attachment_id'] = (int)$preparedUnit['attachment_inventory_id'];
                                    error_log('DI Create - ATTACHMENT SPK: Extracted attachment_inventory_id from prepared_units: ' . $selected['inventory_attachment_id']);
                                    break; // Take first attachment
                                }
                            }
                        }
                    }
                    
                    // Also check for legacy 'selected' format as fallback
                    if (empty($unitIds) && isset($spec['selected'])) {
                        $selected['unit_id'] = (int)($spec['selected']['unit_id'] ?? 0) ?: null;
                        $selected['inventory_attachment_id'] = (int)($spec['selected']['inventory_attachment_id'] ?? 0) ?: null;
                    }
                    
                    // ENHANCEMENT: For ATTACHMENT SPK, prioritize inventory_attachment_id if no unit found
                    if ($isAttachmentSpk && empty($unitIds) && !empty($selected['inventory_attachment_id'])) {
                        error_log('DI Create - ATTACHMENT SPK detected with attachment ID: ' . $selected['inventory_attachment_id']);
                        // We'll handle this in the delivery items section
                    }
                }
            }
            
            // ENHANCEMENT: For ATTACHMENT SPK, allow DI creation even without main unit
            if ($isAttachmentSpk && empty($unitIds) && empty($selected['unit_id']) && empty($selected['inventory_attachment_id'])) {
                error_log('DI Create - ATTACHMENT SPK without prepared items detected');
                // This might be a pure attachment delivery - continue with validation below
            }
        }

        if ($poNo === '') {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'PO/Kontrak wajib diisi']);
        }

        if (empty($pelanggan)) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Nama pelanggan wajib diisi']);
        }

        // Validate workflow fields
        if ($jenisPerintahKerjaId <= 0) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Jenis Perintah Kerja harus dipilih']);
        }

        if ($tujuanPerintahKerjaId <= 0) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Tujuan Perintah Kerja harus dipilih']);
        }

        // Resolve pelanggan_id for customer tracking
        $pelangganId = (int)($this->request->getPost('pelanggan_id') ?? 0) ?: null;
        if (!$pelangganId && $spkId > 0 && !empty($spk['kontrak_id'])) {
            $kontrakRow = $this->db->table('kontrak')->select('customer_id')->where('id', (int)$spk['kontrak_id'])->get()->getRowArray();
            if ($kontrakRow && !empty($kontrakRow['customer_id'])) {
                $pelangganId = (int)$kontrakRow['customer_id'];
            }
        }
        if (!$pelangganId && !empty($pelanggan)) {
            $customerRow = $this->db->table('customers')->select('id')->where('customer_name', $pelanggan)->where('deleted_at IS NULL', null, false)->limit(1)->get()->getRowArray();
            if ($customerRow) {
                $pelangganId = (int)$customerRow['id'];
            }
        }
        if (!$pelangganId && !empty($poNo)) {
            $kontrakByNo = $this->db->table('kontrak')->select('customer_id')->where('no_kontrak', $poNo)->limit(1)->get()->getRowArray();
            if ($kontrakByNo && !empty($kontrakByNo['customer_id'])) {
                $pelangganId = (int)$kontrakByNo['customer_id'];
            }
        }

        if ($customerLocationId === null) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Customer Location wajib dipilih']);
        }

        $locationRow = $this->db->table('customer_locations')
            ->select('id, customer_id, location_name, operator_monthly_rate, operator_daily_rate')
            ->where('id', $customerLocationId)
            ->limit(1)
            ->get()->getRowArray();
        if (!$locationRow) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Customer Location tidak ditemukan']);
        }
        if (!empty($pelangganId) && !empty($locationRow['customer_id']) && (int)$locationRow['customer_id'] !== (int)$pelangganId) {
            return $this->response->setStatusCode(422)->setJSON(['success'=>false,'message'=>'Customer Location tidak sesuai dengan customer DI']);
        }

        $lokasi = $locationRow['location_name'] ?? $lokasi;

        // Determine initial status based on SPK contract linkage
        // DIAJUKAN if SPK has no contract, DISETUJUI if SPK has contract
        $initialStatus = 'DIAJUKAN'; // Default for backward compatibility
        
        if ($spkId > 0) {
            $diModelInstance = new \App\Models\DeliveryInstructionModel();
            $initialStatus = $diModelInstance->determineInitialStatus($spkId);
            error_log('DI Create - Determined initial status: ' . $initialStatus);
        }

        $payload = [
            'nomor_di' => method_exists($this->diModel,'generateNextNumber') ? $this->diModel->generateNextNumber() : $this->generateDiNumber(),
            'spk_id' => $spkId ?: null,
            'jenis_spk' => isset($spk['jenis_spk']) ? $spk['jenis_spk'] : 'UNIT', // Copy jenis_spk from SPK
            'po_kontrak_nomor' => $poNo,
            'pelanggan' => $pelanggan,
            'pelanggan_id' => $pelangganId,
            'customer_location_id' => $customerLocationId,
            'lokasi' => $lokasi,
            'status_di' => $initialStatus,  // Auto-determined based on SPK contract status
            'jenis_perintah_kerja_id' => $jenisPerintahKerjaId,
            'tujuan_perintah_kerja_id' => $tujuanPerintahKerjaId,
            'status_eksekusi_workflow_id' => 1, // Default status eksekusi (PENDING atau sesuai workflow)
            'dibuat_oleh' => session('user_id') ?: 1,
            'dibuat_pada' => date('Y-m-d H:i:s'),
        ];
        
        // Only add optional fields if they have values
        if ($tanggalKirim) {
            $payload['tanggal_kirim'] = $tanggalKirim;
        }
        if ($catatan) {
            $payload['catatan'] = $catatan;
        }
        // Start manual transaction
        $this->db->transBegin();
        
        // Try the insert and catch any errors
        try {
            error_log('DI Create - About to insert payload: ' . json_encode($payload));
            
            // Add debugging before model insert
            error_log('DI Create - diModel class: ' . get_class($this->diModel));
            error_log('DI Create - diModel table: ' . $this->diModel->getTable());
            
            $insertResult = $this->diModel->insert($payload);
            error_log('DI Create - Insert result: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));
            
            if (!$insertResult) {
                $errors = $this->diModel->errors();
                error_log('DI Model Insert Errors: ' . print_r($errors, true));
                // Fallback to DB error if model errors are empty
                $dbError = $this->db->error();
                error_log('DI Insert DB Error after model failure: ' . print_r($dbError, true));
                $msg = '';
                if (!empty($errors)) {
                    $msg = 'Model validation failed: ' . implode(', ', $errors);
                } elseif (!empty($dbError['message'])) {
                    $msg = 'Database error: ' . $dbError['message'];
                    error_log('DI Insert DB Error: ' . print_r($dbError, true));
                } else {
                    // Try to get more detailed error information
                    $lastQuery = $this->db->getLastQuery();
                    error_log('DI Insert - Last Query: ' . ($lastQuery ? $lastQuery : 'No query available'));
                    $msg = 'Insert failed with no specific error message';
                }
                throw new \Exception($msg);
            }
            
            // Get the inserted DI ID - use the returned value from insert() if it's the ID, otherwise use getInsertID()
            $diId = $insertResult;
            if (!is_numeric($diId) || $diId <= 0) {
                $diId = (int)$this->diModel->getInsertID();
            }
            
            // Double-check that we have a valid DI ID
            if (!$diId || $diId <= 0) {
                error_log('DI Create - Failed to get valid DI ID after insert');
                throw new \Exception('Failed to retrieve DI ID after insertion');
            }
            
            error_log("DI Insert successful with ID: $diId");
        } catch (\Exception $e) {
            $dbError = $this->db->error();
            $lastQuery = method_exists($this->db, 'getLastQuery') ? (string)$this->db->getLastQuery() : '';
            error_log('DI Insert Exception: ' . ($e->getMessage() ?: '[empty message]'));
            if (!empty($dbError)) error_log('DB Error: ' . print_r($dbError, true));
            if ($lastQuery) error_log('Last Query: ' . $lastQuery);
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'success'=>false,
                'message'=>'Gagal membuat DI: ' . ($e->getMessage() ?: (!empty($dbError['message']) ? $dbError['message'] : 'Unknown error')),
                'debug' => ENVIRONMENT === 'development' ? [
                    'db_error' => $dbError,
                    'last_query' => $lastQuery,
                    'payload' => $payload,
                ] : null,
                'csrf_hash'=>csrf_hash()
            ]);
        }
        // Insert delivery items: prefer explicit unit_ids from form (multiple),
        // fallback to selected items from SPK if none provided.
        // ENHANCEMENT: Special handling for ATTACHMENT SPK
        try {
            error_log('DI Create - Starting delivery items processing...');
            error_log('DI Create - diItemModel class: ' . get_class($this->diItemModel));
            error_log('DI Create - About to insert delivery items for unit_ids: ' . json_encode($unitIds));
            error_log('DI Create - SPK Type: ' . ($spk['jenis_spk'] ?? 'UNKNOWN') . ', isAttachmentSpk: ' . ($isAttachmentSpk ?? 'UNDEFINED'));

            $operatorRequired = 0;
            $operatorQuantity = 0;
            if (!empty($spk['quotation_specification_id'])) {
                $specRow = $this->db->table('quotation_specifications')
                    ->select('include_operator, operator_quantity')
                    ->where('id_specification', (int)$spk['quotation_specification_id'])
                    ->get()->getRowArray();
                if ($specRow) {
                    $operatorRequired = (int)($specRow['include_operator'] ?? 0);
                    $operatorQuantity = (int)($specRow['operator_quantity'] ?? 0);
                }
            }
            if ($operatorQuantity <= 0 && !empty($spk['spesifikasi'])) {
                $specJson = json_decode($spk['spesifikasi'], true);
                if (is_array($specJson)) {
                    $operatorRequired = (int)($specJson['include_operator'] ?? $operatorRequired);
                    $operatorQuantity = (int)($specJson['operator_quantity'] ?? $operatorQuantity);
                }
            }
            if ($operatorRequired !== 1) {
                $operatorQuantity = 0;
            }
            $operatorMonthlySnapshot = ($locationRow['operator_monthly_rate'] ?? null);
            $operatorDailySnapshot = ($locationRow['operator_daily_rate'] ?? null);
            // Operator rates are optional - DI can be created without them
            $operatorSnapshotFields = [
                'operator_required' => $operatorRequired,
                'operator_quantity' => $operatorQuantity,
                'operator_monthly_rate_snapshot' => $operatorMonthlySnapshot,
                'operator_daily_rate_snapshot' => $operatorDailySnapshot,
                'operator_rate_source' => 'customer_location_master',
            ];
            
            if (!empty($unitIds)) {
                foreach ($unitIds as $uid) {
                    error_log("DI Create - Processing unit ID: $uid");
                    
                    // Verify if unit exists before insertion
                    $unitExists = $this->db->table('inventory_unit')
                        ->where('id_inventory_unit', (int)$uid)
                        ->countAllResults();
                    
                    if (!$unitExists) {
                        throw new \Exception("Unit dengan ID {$uid} tidak ditemukan di inventory");
                    }
                    
                    $unitPayload = [
                        'di_id' => $diId,
                        'item_type' => 'UNIT',
                        'unit_id' => (int)$uid,
                    ];
                    $unitPayload = array_merge($unitPayload, $operatorSnapshotFields);
                    
                    // Only add optional fields if they have values
                    // attachment_id and keterangan are nullable, so we can skip them if null
                    
                    error_log('DI Create - About to insert unit payload: ' . json_encode($unitPayload));
                    
                    // Try direct DB insert first to see if model is the issue
                    try {
                        error_log('DI Create - Attempting direct DB table access...');
                        
                        // Test if we can access the table structure first
                        try {
                            $fields = $this->db->getFieldData('delivery_items');
                            error_log('DI Create - Table structure check passed. Fields: ' . count($fields));
                        } catch (\Exception $structureEx) {
                            error_log('DI Create - Table structure check failed: ' . $structureEx->getMessage());
                            throw new \Exception('Cannot access delivery_items table structure: ' . $structureEx->getMessage());
                        }
                        
                        $directResult = $this->db->table('delivery_items')->insert($unitPayload);
                        error_log('DI Create - Direct DB insert result: ' . ($directResult ? 'SUCCESS' : 'FAILED'));
                        
                        if (!$directResult) {
                            $dbError = $this->db->error();
                            error_log('DI Create - Direct DB insert error: ' . print_r($dbError, true));
                            throw new \Exception('Direct DB insert failed: ' . ($dbError['message'] ?? 'Unknown DB error'));
                        }
                        
                    } catch (\Exception $directEx) {
                        error_log('DI Create - Direct insert exception: ' . $directEx->getMessage());
                        
                        // Fallback to model insert
                        error_log('DI Create - Trying model insert as fallback...');
                        $itemResult = $this->diItemModel->insert($unitPayload);
                        error_log('DI Create - Model insert result: ' . ($itemResult ? 'SUCCESS ID='.$itemResult : 'FAILED'));
                        
                        if (!$itemResult) {
                            $errors = $this->diItemModel->errors();
                            $dbError = $this->db->error();
                            $lastQuery = method_exists($this->db, 'getLastQuery') ? (string)$this->db->getLastQuery() : '';
                            error_log('DI Create - Model insert failed. Model errors: ' . print_r($errors, true));
                            error_log('DI Create - DB error: ' . print_r($dbError, true));
                            error_log('DI Create - Last query: ' . $lastQuery);
                            throw new \Exception('Failed to insert unit item: ' . implode(', ', $errors) . ' | DB: ' . ($dbError['message'] ?? 'No DB error'));
                        }
                    }
                }
                
                // Add attachments (battery, charger, attachment) from SPK approved data
                // USE spk_unit_stages as single source of truth (approved by Service team)
                if (!empty($unitIds) && !empty($spkId)) {
                    foreach ($unitIds as $unitId) {
                        // First, get unit_index from persiapan_unit stage
                        $persiapanStage = $this->db->table('spk_unit_stages')
                            ->select('unit_index, battery_inventory_attachment_id, charger_inventory_attachment_id')
                            ->where('spk_id', $spkId)
                            ->where('unit_id', $unitId)
                            ->where('stage_name', 'persiapan_unit')
                            ->where('tanggal_approve IS NOT NULL')
                            ->get()->getRowArray();
                        
                        if (!$persiapanStage) continue; // Skip if unit not found in SPK
                        
                        $unitIndex = $persiapanStage['unit_index'];
                        
                        // Get attachment from fabrikasi stage using unit_index (because unit_id might be NULL)
                        $fabrikasiStage = $this->db->table('spk_unit_stages')
                            ->select('attachment_inventory_attachment_id')
                            ->where('spk_id', $spkId)
                            ->where('unit_index', $unitIndex)
                            ->where('stage_name', 'fabrikasi')
                            ->where('tanggal_approve IS NOT NULL')
                            ->get()->getRowArray();
                        
                        // Insert battery if approved
                        if (!empty($persiapanStage['battery_inventory_attachment_id'])) {
                            $batteryId = $persiapanStage['battery_inventory_attachment_id'];
                            // Get actual battery_id from inventory_batteries
                            $invBattery = $this->db->table('inventory_batteries')
                                ->select('battery_type_id as baterai_id')
                                ->where('id', $batteryId)
                                ->get()->getRowArray();
                            
                            if ($invBattery && $invBattery['baterai_id']) {
                                $itemResult = $this->db->table('delivery_items')->insert([
                                    'di_id' => $diId,
                                    'item_type' => 'ATTACHMENT',
                                    'attachment_id' => $invBattery['baterai_id'],
                                    'parent_unit_id' => $unitId,
                                    'keterangan' => 'Battery (Approved in SPK Persiapan Unit)'
                                ] + $operatorSnapshotFields);
                                error_log("DI Create - Added approved battery (ID: {$invBattery['baterai_id']}) for unit $unitId");
                            }
                        }
                        
                        if (!empty($persiapanStage['charger_inventory_attachment_id'])) {
                            $chargerId = $persiapanStage['charger_inventory_attachment_id'];
                            // Get actual charger_id from inventory_chargers
                            $invCharger = $this->db->table('inventory_chargers')
                                ->select('charger_type_id as charger_id')
                                ->where('id', $chargerId)
                                ->get()->getRowArray();
                            
                            if ($invCharger && $invCharger['charger_id']) {
                                $itemResult = $this->db->table('delivery_items')->insert([
                                    'di_id' => $diId,
                                    'item_type' => 'ATTACHMENT',
                                    'attachment_id' => $invCharger['charger_id'],
                                    'parent_unit_id' => $unitId,
                                    'keterangan' => 'Charger (Approved in SPK Persiapan Unit)'
                                ] + $operatorSnapshotFields);
                                error_log("DI Create - Added approved charger (ID: {$invCharger['charger_id']}) for unit $unitId");
                            }
                        }
                        
                        if (!empty($fabrikasiStage['attachment_inventory_attachment_id'])) {
                            $attachmentId = $fabrikasiStage['attachment_inventory_attachment_id'];
                            // Get actual attachment_id from inventory_attachments
                            $invAttachment = $this->db->table('inventory_attachments')
                                ->select('attachment_type_id as attachment_id')
                                ->where('id', $attachmentId)
                                ->get()->getRowArray();
                            
                            if ($invAttachment && $invAttachment['attachment_id']) {
                                $itemResult = $this->db->table('delivery_items')->insert([
                                    'di_id' => $diId,
                                    'item_type' => 'ATTACHMENT',
                                    'attachment_id' => $invAttachment['attachment_id'],
                                    'parent_unit_id' => $unitId,
                                    'keterangan' => 'Attachment (Approved in SPK Fabrikasi)'
                                ] + $operatorSnapshotFields);
                                error_log("DI Create - Added approved attachment (ID: {$invAttachment['attachment_id']}) for unit $unitId");
                            }
                        }
                    }
                }
            } else {
                // ENHANCEMENT: For ATTACHMENT SPK without units, handle attachment-only delivery
                if ($isAttachmentSpk && empty($selected['unit_id']) && !empty($selected['inventory_attachment_id'])) {
                    error_log('DI Create - Processing ATTACHMENT-only SPK delivery');
                    // Map inventory_attachment to attachment_id if needed - check all 3 tables
                    $inv = $this->componentHelper->findComponentByIdAny($selected['inventory_attachment_id']);
                    $attId = $inv['attachment_id'] ?? $inv['baterai_id'] ?? $inv['charger_id'] ?? null;
                    $componentType = $inv['tipe_item'] ?? 'attachment';
                    if ($attId) {
                        $itemResult = $this->db->table('delivery_items')->insert([
                            'di_id' => $diId,
                            'item_type' => 'ATTACHMENT',
                            'attachment_id' => $attId,
                            'keterangan' => 'Pure Attachment Delivery - ' . ($inv['tipe_item'] ?? 'attachment'),
                        ] + $operatorSnapshotFields);
                        if (!$itemResult) {
                            $errors = $this->diItemModel->errors();
                            throw new \Exception('Failed to insert pure attachment: ' . implode(', ', $errors));
                        }
                        error_log('DI Create - Added pure attachment delivery (ID: ' . $attId . ' Type: ' . $componentType . ')');
                    }
                } else {
                    // Standard workflow for UNIT SPK
                    if (!empty($selected['unit_id'])) {
                        $itemResult = $this->db->table('delivery_items')->insert([
                            'di_id' => $diId,
                            'item_type' => 'UNIT',
                            'unit_id' => (int)$selected['unit_id'],
                        ] + $operatorSnapshotFields);
                        if (!$itemResult) {
                            $errors = $this->diItemModel->errors();
                            throw new \Exception('Failed to insert selected unit: ' . implode(', ', $errors));
                        }
                    }
                    if (!empty($selected['inventory_attachment_id'])) {
                        // Map inventory_attachment to attachment_id if needed - check all 3 tables
                        $inv = $this->componentHelper->findComponentByIdAny($selected['inventory_attachment_id']);
                        $attId = $inv['attachment_id'] ?? $inv['baterai_id'] ?? $inv['charger_id'] ?? null;
                        if ($attId) {
                            $itemResult = $this->db->table('delivery_items')->insert([
                                'di_id' => $diId,
                                'item_type' => 'ATTACHMENT',
                                'attachment_id' => $attId,
                            ] + $operatorSnapshotFields);
                            if (!$itemResult) {
                                $errors = $this->diItemModel->errors();
                                throw new \Exception('Failed to insert attachment: ' . implode(', ', $errors));
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $dbError = $this->db->error();
            $lastQuery = method_exists($this->db, 'getLastQuery') ? (string)$this->db->getLastQuery() : '';
            error_log('DI Items Insert Exception: ' . ($e->getMessage() ?: '[empty message]'));
            if (!empty($dbError)) error_log('DB Error: ' . print_r($dbError, true));
            if ($lastQuery) error_log('Last Query: ' . $lastQuery);
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'success'=>false,
                'message'=>'Gagal membuat items DI: ' . ($e->getMessage() ?: (!empty($dbError['message']) ? $dbError['message'] : 'Unknown error')),
                'debug' => ENVIRONMENT === 'development' ? [
                    'db_error' => $dbError,
                    'last_query' => $lastQuery,
                ] : null,
                'csrf_hash'=>csrf_hash()
            ]);
        }
        
        // Update SPK status to IN_PROGRESS when DI is created
        if ($spkId > 0) {
            try {
                error_log("DI Create - About to update SPK status for spkId: $spkId");
                $updateResult = $this->db->table('spk')->where('id', $spkId)->update([
                    'status' => 'IN_PROGRESS',
                    'diperbarui_pada' => date('Y-m-d H:i:s')
                ]);
                
                if (!$updateResult) {
                    error_log("DI Create - SPK status update failed");
                    throw new \Exception('Failed to update SPK status');
                } else {
                    error_log("DI Create - SPK status updated successfully");
                }
                
                // Log status history - temporarily disabled due to consistent failures
                // try {
                //     error_log("DI Create - About to insert SPK status history");
                //     // Use table builder instead of raw query for better error handling
                //     $statusHistory = [
                //         'spk_id' => $spkId,
                //         'status_from' => 'READY',
                //         'status_to' => 'IN_PROGRESS',
                //         'changed_by' => session('user_id') ?: 1,
                //         'note' => 'DI created: ' . $payload['nomor_di']
                //     ];
                //     
                //     $historyResult = $this->db->table('spk_status_history')->insert($statusHistory);
                //     if ($historyResult) {
                //         error_log("DI Create - SPK status history inserted successfully");
                //     } else {
                //         $historyError = $this->db->error();
                //         error_log("DI Create - SPK status history insert failed: " . print_r($historyError, true));
                //         // Don't throw exception for history logging, it's not critical
                //     }
                // } catch (\Exception $e) {
                //     // Continue if history logging fails (best effort)
                //     error_log('SPK Status History Exception: ' . $e->getMessage());
                // }
                error_log("DI Create - SPK status history temporarily disabled due to failures");
            } catch (\Exception $e) {
                error_log('SPK Status Update Exception: ' . $e->getMessage());
                // SPK status update failure should not fail the entire DI creation
                // Log the error but continue with the transaction
            }
        }
        
        // Debug what query is about to be executed in the transaction
        error_log('DI Creation - Before transComplete. Payload: ' . json_encode($payload));
        error_log('Unit IDs: ' . json_encode($unitIds));
        
        // Check what tables and columns are involved
        try {
            $diTable = $this->diModel->table;
            $diColumns = $this->db->getFieldNames($diTable);
            error_log('DI Table: ' . $diTable . ', Columns: ' . implode(', ', $diColumns));
            
            $diItemsTable = $this->diItemModel->table;
            $diItemColumns = $this->db->getFieldNames($diItemsTable);
            error_log('DI Items Table: ' . $diItemsTable . ', Columns: ' . implode(', ', $diItemColumns));
        } catch (\Exception $e) {
            error_log('Terjadi kesalahan. Silakan coba lagi.');
        }
        
        // Check transaction status and commit or rollback
        if ($this->db->transStatus() === false) {
            // Transaction failed, rollback and return error
            $this->db->transRollback();
            $dbError = $this->db->error();
            error_log('DI Creation DB Error: ' . print_r($dbError, true));
            error_log('DI Creation Payload: ' . print_r($payload, true));
            
            // Check if we can get the last query to debug
            $lastQuery = '';
            if (method_exists($this->db, 'getLastQuery')) {
                $lastQuery = (string)$this->db->getLastQuery();
                error_log('Last Query: ' . $lastQuery);
            }
            
            return $this->response->setStatusCode(500)->setJSON([
                'success'=>false,
                'message'=>'Gagal membuat DI: Transaction failed',
                'debug' => ENVIRONMENT === 'development' ? [
                    'db_error' => $dbError,
                    'last_query' => $lastQuery,
                    'payload' => $payload,
                ] : null,
                'csrf_hash'=>csrf_hash()
            ]);
        } else {
            // Transaction successful, commit it
            $this->db->transCommit();
            
            // Update SPK status to COMPLETED when DI is created
            if ($spkId > 0) {
                $this->db->table('spk')
                    ->where('id', $spkId)
                    ->update(['status' => 'COMPLETED', 'diperbarui_pada' => date('Y-m-d H:i:s')]);
                error_log("SPK {$spkId}: Status updated to COMPLETED after DI creation");
            }
            
            // Log DI creation using trait
            $this->logCreate('delivery_instruction', $diId, [
                'di_id' => $diId,
                'nomor_di' => $payload['nomor_di'],
                'spk_id' => $spkId ?: null,
                'po_kontrak_nomor' => $poNo,
                'pelanggan' => $pelanggan,
                'customer_location_id' => $customerLocationId,
                'jenis_perintah_kerja_id' => $jenisPerintahKerjaId,
                'tujuan_perintah_kerja_id' => $tujuanPerintahKerjaId,
                'unit_ids' => $unitIds,
                'operator_required' => $operatorRequired,
                'operator_quantity' => $operatorQuantity,
                'operator_monthly_rate_snapshot' => $operatorMonthlySnapshot,
                'operator_daily_rate_snapshot' => $operatorDailySnapshot
            ]);
            
            // Send notification: DI Created
            helper('notification');
            
            // Get jenis perintah name
            $jenisPerintahName = '';
            if ($jenisPerintahKerjaId > 0) {
                $jenisPerintah = $this->db->table('jenis_perintah_kerja')
                    ->where('id', $jenisPerintahKerjaId)
                    ->get()
                    ->getRowArray();
                $jenisPerintahName = $jenisPerintah['nama'] ?? '';
            }
            
            notify_di_created([
                'id' => $diId,
                'nomor_di' => $payload['nomor_di'],
                'unit_code' => '',
                'customer' => $pelanggan,
                'jenis_perintah' => $jenisPerintahName
            ]);
            
            return $this->response->setJSON([
                'success'=>true,
                'message'=>'DI dibuat',
                'nomor'=>$payload['nomor_di'],
                'csrf_hash'=>csrf_hash()
            ]);
        }
    }

    // ===== KONTRAK METHODS =====
    public function kontrak()
    {
        $data = [
            'title' => 'Manajemen Kontrak',
            'breadcrumbs' => [
                'marketing' => 'Marketing',
                'marketing/kontrak' => 'Kontrak'
            ],
            'loadDataTables' => true, // Enable DataTables loading
        ];
        
        return view('marketing/kontrak', $data);
    }


    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request','csrf_hash'=>csrf_hash()]);
        }

        try {
            $draw   = (int)($this->request->getPost('draw') ?? 0);
            $start  = (int)($this->request->getPost('start') ?? 0);
            $length = (int)($this->request->getPost('length') ?? 10);
            $searchValue = trim($this->request->getPost('search')['value'] ?? '');

            // Base query with JOIN to customers
            $builder = $this->db->table('kontrak k');
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            
            $countBuilder = $this->db->table('kontrak k');
            $countBuilder->join('customers c', 'c.id = k.customer_id', 'left');

            // Status filter functionality
            $statusFilter = trim($this->request->getPost('statusFilter') ?? 'all');
            if ($statusFilter !== 'all') {
                if ($statusFilter === 'expiring') {
                    // Expiring contracts (ACTIVE status and expiring within 30 days)
                    $expiringDate = date('Y-m-d', strtotime('+30 days'));
                    $builder->where('k.status', 'ACTIVE')
                           ->where('k.tanggal_berakhir <=', $expiringDate)
                           ->where('k.tanggal_berakhir >=', date('Y-m-d'));
                    $countBuilder->where('k.status', 'ACTIVE')
                                ->where('k.tanggal_berakhir <=', $expiringDate)
                                ->where('k.tanggal_berakhir >=', date('Y-m-d'));
                } else {
                    // Standard status filter
                    $builder->where('k.status', $statusFilter);
                    $countBuilder->where('k.status', $statusFilter);
                }
            }

            // Search functionality with new database structure
            if ($searchValue !== '') {
                $builder->groupStart()
                    ->like('k.no_kontrak', $searchValue)
                    ->orLike('c.customer_name', $searchValue) // Search customer name
                    ->orLike('cl.location_name', $searchValue) // Search location name
                    ->orLike('cl.address', $searchValue) // Search address
                    ->orLike('k.no_po_marketing', $searchValue)
                ->groupEnd();

                $countBuilder->groupStart()
                    ->like('k.no_kontrak', $searchValue)
                    ->orLike('c.customer_name', $searchValue)
                    ->orLike('cl.location_name', $searchValue)
                    ->orLike('cl.address', $searchValue)
                    ->orLike('k.no_po_marketing', $searchValue)
                ->groupEnd();
            }

            // Count records
            $recordsTotal = $this->db->table('kontrak')->countAllResults();
            $recordsFiltered = $countBuilder->countAllResults();

            // Select with proper field mapping from new database structure
            $builder->select('k.id, 
                            k.no_kontrak, 
                            k.no_po_marketing, 
                            k.jenis_sewa,
                            k.tanggal_mulai, 
                            k.tanggal_berakhir, 
                            k.status,
                            k.total_units,
                            k.nilai_total,
                            k.dibuat_pada,
                            k.diperbarui_pada,
                            c.customer_name as pelanggan,
                            cl.location_name as lokasi,
                            cl.contact_person as pic,
                            cl.phone as kontak,
                            cl.address as alamat,
                            (SELECT COUNT(*) FROM kontrak_unit ku WHERE ku.kontrak_id = k.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0) as calculated_total_units,
                            k.nilai_total as calculated_value');

            $kontrakData = $builder
                ->orderBy('k.id', 'DESC')
                ->limit($length, $start)
                ->get()
                ->getResultArray();

            // Fallback simple query if result unexpectedly empty but table has records
            if (empty($kontrakData)) {
                $totalCheck = $this->db->table('kontrak')->countAllResults();
                if ($totalCheck > 0) {
                    log_message('debug', 'Marketing::getDataTable primary query returned empty, running fallback simple select');
                    $kontrakData = $this->db->table('kontrak k')
                        ->join('customers c', 'c.id = k.customer_id', 'left')
                        ->select('k.id, 
                                k.no_kontrak, 
                                k.no_po_marketing, 
                                k.jenis_sewa,
                                k.tanggal_mulai, 
                                k.tanggal_berakhir, 
                                k.status,
                                k.total_units,
                                k.nilai_total,
                                c.customer_name as pelanggan,
                                cl.location_name as lokasi,
                                cl.contact_person as pic,
                                cl.phone as kontak,
                                (SELECT COUNT(*) FROM kontrak_unit ku WHERE ku.kontrak_id = k.id AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0) as calculated_total_units,
                                k.nilai_total as calculated_value')
                        ->orderBy('k.id','DESC')
                        ->limit($length, $start)
                        ->get()
                        ->getResultArray();
                }
            }

            // Format data
            $data = [];
            foreach ($kontrakData as $row) {
                $statusClass = $this->getStatusClass($row['status']);
                $startDate = date('d/m/Y', strtotime($row['tanggal_mulai']));
                $endDate = date('d/m/Y', strtotime($row['tanggal_berakhir']));
                $period = $startDate . ' - ' . $endDate;

                $totalUnits = $row['calculated_total_units'] ?? 0;

                $data[] = [
                    'id' => $row['id'],
                    'contract_number' => esc($row['no_kontrak']),
                    'po' => esc($row['no_po_marketing'] ?? ''),
                    'client_name' => esc($row['pelanggan']),
                    'jenis_sewa' => ucfirst($row['jenis_sewa'] ?? 'Belum Ditentukan'),
                    'period' => $period,
                    'value' => 'Rp ' . number_format($row['calculated_value'] ?? 0, 0, ',', '.'),
                    'total_units' => intval($row['calculated_total_units'] ?? 0),
                    'status' => '<span class="badge bg-' . $statusClass . '">' . esc($row['status']) . '</span>',
                    'actions' => $this->buildKontrakActions($row['id'])
                ];
            }

            // Calculate statistics
            $stats = $this->getKontrakStats();
            
            // Debug logging
            log_message('debug', 'Marketing::getDataTable - Data count: ' . count($data));
            log_message('debug', 'Marketing::getDataTable - Record counts: total=' . $recordsTotal . ', filtered=' . $recordsFiltered);

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'stats' => $stats,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'draw' => $draw ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan pada server. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Generate unique contract number (private)
     */
    private function generateContractNumberPrivate()
    {
        $year = date('Y');
        $month = date('m');

        // Find the highest contract number for current year/month
        $prefix = "KTR/{$year}/{$month}/";
        $existing = $this->db->table('kontrak')
            ->select('no_kontrak')
            ->like('no_kontrak', $prefix, 'after')
            ->orderBy('no_kontrak', 'DESC')
            ->get()
            ->getRowArray();

        $nextNumber = 1;
        if ($existing) {
            // Extract number from existing contract (e.g., "KTR/2025/08/005" -> 5)
            $parts = explode('/', $existing['no_kontrak']);
            if (count($parts) >= 4) {
                $lastPart = end($parts);
                if (is_numeric($lastPart)) {
                    $nextNumber = (int)$lastPart + 1;
                }
            }
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Check if contract number already exists
     */
    public function checkContractNumberDuplicate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $contractNumber = trim((string)$this->request->getPost('contract_number'));
            
            if (!$contractNumber) {
                return $this->response->setJSON([
                    'success' => false,
                    'duplicate' => false,
                    'message' => 'Contract number is required',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            $existing = $this->kontrakModel->where('no_kontrak', $contractNumber)->first();
            
            return $this->response->setJSON([
                'success' => true,
                'duplicate' => $existing ? true : false,
                'existing_id' => $existing ? ($existing['id'] ?? $existing->id) : null,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Generate next available contract number
     */
    public function generateContractNumber()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            $contractNumber = $this->generateContractNumberPrivate();

            return $this->response->setJSON([
                'success' => true,
                'contract_number' => $contractNumber,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal generate nomor kontrak. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function getData()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get all kontrak data with joined information from new database structure
            $query = "
                SELECT 
                    k.id,
                    k.no_kontrak,
                    k.no_po_marketing,
                    c.customer_name as pelanggan,
                    (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as lokasi,
                    k.tanggal_mulai,
                    k.tanggal_berakhir as tanggal_selesai,
                    k.status,
                    k.total_units,
                    k.nilai_total,
                    k.dibuat_pada as created_at,
                    k.diperbarui_pada as updated_at
                FROM kontrak k
                LEFT JOIN customers c ON c.id = k.customer_id
                ORDER BY k.id DESC
            ";
            
            $result = $db->query($query);
            $data = $result->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data kontrak. Silakan coba lagi.'
            ]);
        }
    }

    public function getDIData()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get all DI data with joined information
            $query = "
                SELECT 
                    di.id,
                    di.no_di,
                    di.spk_id,
                    di.status,
                    di.tanggal_dibuat,
                    di.tanggal_dikirim,
                    di.pic,
                    di.catatan,
                    spk.no_spk,
                    spk.pelanggan,
                    spk.departemen
                FROM delivery_instructions di
                LEFT JOIN spk ON di.spk_id = spk.id
                ORDER BY di.id DESC
            ";
            
            $result = $db->query($query);
            $data = $result->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'draw' => 1,
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data DI. Silakan coba lagi.'
            ]);
        }
    }

    // Method storeKontrak removed - using Kontrak::store instead for consistency

    // Method detailKontrak removed - unused due to route priority (Kontrak::detail is called instead)
    public function detailKontrakRemoved($id)
    {
        try {
            // Get contract with customer and location data using JOIN
            $kontrak = $this->db->query("SELECT k.*, 
                                               c.customer_name,
                                               (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as location_name,
                                               (SELECT cl.contact_person FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as contact_person,
                                               (SELECT cl.phone FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as phone,
                                               (SELECT cl.address FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as address,
                                               CONCAT(u.first_name, ' ', u.last_name) as dibuat_oleh_nama
                                        FROM kontrak k 
                                        LEFT JOIN customers c ON c.id = k.customer_id 
                                        LEFT JOIN users u ON k.dibuat_oleh = u.id 
                                        WHERE k.id = ?", [$id])->getRowArray();

            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }

            // Add backward compatibility aliases for SPK modal
            $kontrak['pelanggan'] = $kontrak['customer_name'];
            $kontrak['pic'] = $kontrak['contact_person'];
            $kontrak['kontak'] = $kontrak['phone'];
            $kontrak['lokasi'] = $kontrak['location_name'];
            $kontrak['alamat'] = $kontrak['address'];

            return $this->response->setJSON([
                'success' => true,
                'data' => $kontrak,
                'source' => 'Marketing::detailKontrak',
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data kontrak. Silakan coba lagi.'
            ]);
        }
    }

    // Method updateKontrak moved to Kontrak controller

    // Method deleteKontrak moved to Kontrak controller

    private function getKontrakStats()
    {
        $stats = [
            'total' => $this->db->table('kontrak')->countAllResults(),
            'active' => $this->db->table('kontrak')->where('status', 'ACTIVE')->countAllResults(),
            'expiring' => 0, // Will be calculated based on date
            'expired' => $this->db->table('kontrak')->where('status', 'EXPIRED')->countAllResults()
        ];

        // Calculate expiring contracts (within 30 days)
        $expiringDate = date('Y-m-d', strtotime('+30 days'));
        $stats['expiring'] = $this->db->table('kontrak')
            ->where('status', 'ACTIVE')
            ->where('tanggal_berakhir <=', $expiringDate)
            ->where('tanggal_berakhir >=', date('Y-m-d'))
            ->countAllResults();

        return $stats;
    }

    private function getStatusClass($status)
    {
        switch ($status) {
            case 'ACTIVE': return 'success';
            case 'PENDING': return 'warning';
            case 'EXPIRED': return 'secondary';
            case 'CANCELLED': return 'danger';
            default: return 'secondary';
        }
    }

    private function buildKontrakActions($id)
    {
        return '<div class="dropdown">'
            .'<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">'
            .'<i class="fas fa-ellipsis-h"></i></button>'
            .'<ul class="dropdown-menu">'
            .'<li><a class="dropdown-item" href="javascript:viewContractUnits('.$id.')"><i class="fas fa-list me-2 text-info"></i>Lihat Unit</a></li>'
            .'<li><a class="dropdown-item" href="javascript:editContract('.$id.')"><i class="fas fa-edit me-2 text-primary"></i>Edit</a></li>'
            .'<li><a class="dropdown-item" href="javascript:deleteContract('.$id.')"><i class="fas fa-trash me-2 text-danger"></i>Hapus</a></li>'
            .'</ul></div>';
    }

    public function availableUnitsData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request','csrf_hash'=>csrf_hash()]);
        }

                $draw   = (int)($this->request->getPost('draw') ?? 0);
                $start  = (int)($this->request->getPost('start') ?? 0);
                $length = (int)($this->request->getPost('length') ?? 10);
                if ($length <= 0) { $length = 10; }
                if ($length === -1) { $length = null; }

            $statusTab   = $this->request->getPost('status_tab'); // 'all', '7', '8'
            $tipeFilter  = trim($this->request->getPost('tipe') ?? '');
                $lokasiFilter= trim($this->request->getPost('lokasi') ?? '');
                $searchValue = trim($this->request->getPost('search')['value'] ?? '');

    try {
                    // Base queries
                    $base = $this->baseQuery(); // already limited to status 7 & 8
                    $count = $this->baseQuery();

                    // Status tab filter (optional within 7 & 8)
                    if ($statusTab === '7' || $statusTab === '8') {
                        $base->where('iu.status_unit_id', (int)$statusTab);
                        $count->where('iu.status_unit_id', (int)$statusTab);
                    }

                    if ($tipeFilter !== '') {
                        // Cari pada tabel tipe_unit (kolom tipe atau jenis)
                        $base->groupStart()->like('tu.tipe', $tipeFilter)->orLike('tu.jenis', $tipeFilter)->groupEnd();
                        $count->groupStart()->like('tu.tipe', $tipeFilter)->orLike('tu.jenis', $tipeFilter)->groupEnd();
                    }
                    if ($lokasiFilter !== '') {
                        $base->like('iu.lokasi_unit', $lokasiFilter);
                        $count->like('iu.lokasi_unit', $lokasiFilter);
                    }

                    if ($searchValue !== '') {
                        $base->groupStart()
                            ->like('iu.no_unit', $searchValue)
                            ->orLike('iu.no_unit_na', $searchValue)
                            ->orLike('iu.serial_number', $searchValue)
                            ->orLike('mu.merk_unit', $searchValue)
                            ->orLike('mu.model_unit', $searchValue)
                            ->orLike('tu.tipe', $searchValue)
                            ->orLike('tu.jenis', $searchValue)
                            ->orLike('iu.lokasi_unit', $searchValue)
                        ->groupEnd();

                        $count->groupStart()
                            ->like('iu.no_unit', $searchValue)
                            ->orLike('iu.no_unit_na', $searchValue)
                            ->orLike('iu.serial_number', $searchValue)
                            ->orLike('mu.merk_unit', $searchValue)
                            ->orLike('mu.model_unit', $searchValue)
                            ->orLike('tu.tipe', $searchValue)
                            ->orLike('tu.jenis', $searchValue)
                            ->orLike('iu.lokasi_unit', $searchValue)
                        ->groupEnd();
                    }

                    // Counts
                    $recordsTotal    = $this->baseQuery()->countAllResults(); // total status 7 & 8
                    $recordsFiltered = $count->countAllResults();

                    // Ordering (simple & safe). If no_unit missing, fallback by id.
                    $base->orderBy('iu.no_unit','ASC')->orderBy('iu.id_inventory_unit','ASC');
                    if ($length !== null) { $base->limit($length, $start); }

                    $rows = $base->get()->getResultArray();
                    $data = [];
                    foreach ($rows as $r) {
                        $realId = isset($r['id_inventory_unit']) ? (int)$r['id_inventory_unit'] : 0;
                        $data[] = [
                            'id'              => $realId,
                            'no_unit'         => $r['no_unit'] ?: ($r['no_unit_na'] ?? null),
                            'serial_number'   => $r['serial_number'],
                            'brand'           => $r['merk_unit'],
                            'model'           => $r['model_unit'],
                            'type_full'       => $r['tipe_full'],
                            'capacity'        => $r['kapasitas_unit'],
                            'lokasi_unit'     => $r['lokasi_unit'],
                            'nama_departemen' => $r['nama_departemen'] ?? '-',
                            'status_id'       => (int)$r['status_unit_id'],
                            'status_name'     => strtoupper($r['status_unit_name']),
                            'actions'         => $this->buildActions($realId)
                        ];
                    }

            return $this->response->setJSON([
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
                'csrf_hash'       => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error: '.$e->getMessage(),
                'csrf_hash' => csrf_hash(),
            ]);
        }
    }

    private function baseQuery(): BaseBuilder
    {
        $qb = $this->db->table('inventory_unit iu')
            ->select('iu.id_inventory_unit, iu.no_unit, iu.no_unit_na, iu.serial_number, iu.status_unit_id, iu.lokasi_unit, iu.created_at')
            ->select('COALESCE(mu.merk_unit, "-") AS merk_unit, COALESCE(mu.model_unit, "") AS model_unit')
            ->select('COALESCE(CONCAT(tu.tipe, " ", tu.jenis), "-") AS tipe_full')
            ->select('COALESCE(kap.kapasitas_unit, "-") AS kapasitas_unit')
            ->select('COALESCE(d.nama_departemen, "-") AS nama_departemen')
            ->select('su.status_unit AS status_unit_name')
            ->join('status_unit su','su.id_status = iu.status_unit_id','left')
            ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
            ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left')
            ->join('kapasitas kap','kap.id_kapasitas = iu.kapasitas_unit_id','left')
            ->join('departemen d','d.id_departemen = iu.departemen_id','left')
            ->whereIn('iu.status_unit_id',[7,8]);
        return $qb;
    }

    private function buildActions(int $id): string
    {
        return '<div class="dropdown">'
            .'<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>'
            .'<ul class="dropdown-menu">'
            .'<li><a class="dropdown-item" href="#" onclick="viewDetail('.$id.')"><i class="fas fa-eye me-2 text-info"></i>Lihat</a></li>'
            .'<li><a class="dropdown-item" href="'.base_url('marketing/quotations').'?unit='.$id.'"><i class="fas fa-file-invoice me-2 text-primary"></i>Quotations</a></li>'
            .'<li><a class="dropdown-item" href="'.base_url('marketing/booking').'?unit='.$id.'"><i class="fas fa-calendar-plus me-2 text-success"></i>Booking</a></li>'
            .'</ul></div>';
    }

    /**
     * Get contract details by ID with customer and location info
     */
    // Method getKontrak moved to Kontrak controller
    public function getKontrakRemoved($id)
    {        
        try {
            // Test 1: Simple kontrak query first
            $kontrak = $this->db->table('kontrak')->where('id', (int)$id)->get()->getRowArray();
            if (!$kontrak) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            
            // Get customer location if exists from kontrak_unit (new schema)
            $customer_location = null;
            if (!empty($kontrak['customer_id'])) {
                // Query first location from kontrak_unit table
                $customer_location = $this->db->query("SELECT cl.*, c.customer_name 
                                                     FROM kontrak_unit ku
                                                     JOIN customer_locations cl ON cl.id = ku.customer_location_id
                                                     JOIN customers c ON cl.customer_id = c.id
                                                     WHERE ku.kontrak_id = ?
                                                     LIMIT 1", 
                                                     [$id])->getRowArray();
            }

            // Get user info
            $user = null;
            if ($kontrak['dibuat_oleh']) {
                $user = $this->db->table('users')->where('id', $kontrak['dibuat_oleh'])->get()->getRowArray();
            }

            // Merge data
            $contract = $kontrak;
            if ($customer_location) {
                $contract['customer_name'] = $customer_location['customer_name'];
                $contract['pelanggan'] = $customer_location['customer_name'];
                $contract['location_name'] = $customer_location['location_name'];
                $contract['lokasi'] = $customer_location['location_name'];
                $contract['contact_person'] = $customer_location['contact_person'];
                $contract['pic'] = $customer_location['contact_person'];
                $contract['phone'] = $customer_location['phone'];
                $contract['kontak'] = $customer_location['phone'];
                $contract['address'] = $customer_location['address'];
                $contract['alamat'] = $customer_location['address'];
            }
            if ($user) {
                $contract['dibuat_oleh_nama'] = ($user['first_name'] . ' ' . $user['last_name']);
            }
            
            if (!$contract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $contract,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Marketing::getKontrak] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat detail kontrak',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Find contract by specification ID
     */
    public function findBySpesifikasi($spekId)
    {
        try {
            $spek = $this->kontrakSpesifikasiModel->find((int)$spekId);
            
            if (!$spek) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Spesifikasi tidak ditemukan',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'kontrak_id' => $spek['kontrak_id'],
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Marketing::findBySpesifikasi] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mencari kontrak',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Cleanup SPK records with ID = 0
     */
    public function cleanupSpkZero()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            // Check for SPK records with ID = 0
            $spkZeroRecords = $this->db->table('spk')->where('id', 0)->get()->getResultArray();

            $result = [
                'success' => true,
                'message' => 'Cleanup completed',
                'found_records' => count($spkZeroRecords),
                'deleted_records' => 0,
                'deleted_history' => 0,
                'records' => []
            ];

            if (count($spkZeroRecords) > 0) {
                // Store record details for response
                foreach ($spkZeroRecords as $record) {
                    $result['records'][] = [
                        'id' => $record['id'],
                        'nomor_spk' => $record['nomor_spk'],
                        'status' => $record['status'],
                        'dibuat_pada' => $record['dibuat_pada']
                    ];
                }

                // Delete SPK records with ID = 0
                $deleted = $this->db->table('spk')->where('id', 0)->delete();
                $result['deleted_records'] = $deleted;

                // Delete related status history records
                $statusHistoryRecords = $this->db->table('spk_status_history')->where('spk_id', 0)->get()->getResultArray();
                if (count($statusHistoryRecords) > 0) {
                    $deletedHistory = $this->db->table('spk_status_history')->where('spk_id', 0)->delete();
                    $result['deleted_history'] = $deletedHistory;
                }

                log_message('info', 'Marketing::cleanupSpkZero - Deleted ' . $deleted . ' SPK records with ID = 0 and ' . $result['deleted_history'] . ' status history records');
            } else {
                $result['message'] = 'No SPK records with ID = 0 found';
            }

            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::cleanupSpkZero - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ]);
        }
    }
    
    /**
     * API untuk mendapatkan data jenis perintah kerja
     */
    public function getJenisPerintahKerja()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }
        
        // Check context - if called from SPK, exclude TARIK workflows
        $context = $this->request->getGet('context');
        
        try {
            $builder = $this->db->table('jenis_perintah_kerja')
                ->where('aktif', 1);
                
            // For SPK context, exclude TARIK workflows since TARIK doesn't need SPK
            if ($context === 'spk') {
                $builder->where('nama !=', 'TARIK')
                       ->where('kode !=', 'TARIK');
            }
                
            $data = $builder->orderBy('nama', 'ASC')
                ->get()
                ->getResultArray();
                
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }
    
    /**
     * API untuk mendapatkan tujuan perintah kerja berdasarkan jenis
     */
    public function getTujuanPerintahKerja()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }
        
        $jenisId = (int) $this->request->getGet('jenis_id');
        if (!$jenisId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jenis ID is required']);
        }
        
        try {
            $data = $this->db->table('tujuan_perintah_kerja')
                ->where('jenis_perintah_id', $jenisId)
                ->where('aktif', 1)
                ->orderBy('nama', 'ASC')
                ->get()
                ->getResultArray();
                
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Delete SPK
     */
    public function spkDelete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }
        $canDeleteSpk = $this->hasPermission('marketing.spk.delete')
            || $this->hasPermission('marketing.spk.edit')
            || $this->hasPermission('marketing.kontrak.delete')
            || $this->canDelete('marketing')
            || $this->canManage('marketing');
        if (!$canDeleteSpk) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        try {
            // Validate SPK ID
            if (!$id || $id <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID SPK tidak valid.'
                ]);
            }

            // Check if SPK exists
            $spk = $this->spkModel->find($id);
            if (!$spk) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SPK tidak ditemukan.'
                ]);
            }

            // Check if SPK can be deleted (only if status is SUBMITTED or DRAFT)
            if (!in_array($spk['status'], ['SUBMITTED', 'DRAFT'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SPK tidak dapat dihapus karena status sudah diproses.'
                ]);
            }

            // Check for related DI records
            $diCount = $this->db->table('delivery_instruction')
                ->where('spk_id', $id)
                ->countAllResults();

            if ($diCount > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SPK tidak dapat dihapus karena sudah memiliki Delivery Instruction.'
                ]);
            }

            // Start transaction
            $this->db->transBegin();

            // Delete SPK
            $deleteResult = $this->spkModel->delete($id);

            if ($deleteResult) {
                // Log SPK deletion using trait
                $this->logDelete('spk', $id, $spk, [
                    'spk_id' => $id,
                    'nomor_spk' => $spk['nomor_spk'] ?? null,
                    'status' => $spk['status'] ?? null
                ]);
                
                $this->db->transComplete();
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'SPK berhasil dihapus.'
                ]);
            } else {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus SPK.'
                ]);
            }

        } catch (\Exception $e) {
            if ($this->db->transStatus()) {
                $this->db->transRollback();
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Delete DI
     */
    public function diDelete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }
        $canDeleteDi = $this->hasPermission('marketing.delivery.delete')
            || $this->hasPermission('marketing.delivery.edit')
            || $this->canDelete('marketing')
            || $this->canManage('marketing');
        if (!$canDeleteDi) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        try {
            // Validate DI ID
            if (!$id || $id <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID DI tidak valid.'
                ]);
            }

            // Check if DI exists
            $di = $this->diModel->find($id);
            if (!$di) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'DI tidak ditemukan.'
                ]);
            }

            // Check if DI can be deleted (only if status is SUBMITTED)
            if ($di['status'] !== 'SUBMITTED') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'DI tidak dapat dihapus karena status sudah diproses.'
                ]);
            }

            // Check for related delivery items
            $itemCount = $this->db->table('delivery_item')
                ->where('delivery_instruction_id', $id)
                ->countAllResults();

            if ($itemCount > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'DI tidak dapat dihapus karena sudah memiliki item yang diproses.'
                ]);
            }

            // Start transaction
            $this->db->transBegin();

            // Delete DI
            $deleteResult = $this->diModel->delete($id);

            if ($deleteResult) {
                // Log DI deletion using trait
                $this->logDelete('delivery_instruction', $id, $di, [
                    'di_id' => $id,
                    'nomor_di' => $di['nomor_di'] ?? null,
                    'status' => $di['status'] ?? null
                ]);
                
                $this->db->transComplete();
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'DI berhasil dihapus.'
                ]);
            } else {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus DI.'
                ]);
            }

        } catch (\Exception $e) {
            if ($this->db->transStatus()) {
                $this->db->transRollback();
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }
    
    /**
     * Get contract detail for modal view (alias for getKontrak)
     */
    // Method kontrakDetail moved to Kontrak controller
    
    /**
     * Get customer locations for dropdown in contract forms
     */
    public function getCustomerLocations()
    {
        
        try {
            $builder = $this->db->table('customer_locations cl');
            $builder->join('customers c', 'cl.customer_id = c.id', 'left');
            $builder->select('cl.id, 
                            cl.location_name, 
                            cl.address,
                            cl.contact_person,
                            cl.phone,
                            c.customer_name,
                            c.customer_code');
            $builder->where('cl.is_active', 1);
            $builder->orderBy('c.customer_name', 'ASC');
            $builder->orderBy('cl.is_primary', 'DESC'); // Primary locations first
            
            $locations = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $locations,
                'csrf_hash' => csrf_hash()
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }
    
    /**
     * Get customers list for dropdown
     */
    // Method getCustomers moved to Kontrak controller for better structure
    
    /**
     * Get customer locations by customer ID
     */
    // Method getLocationsByCustomer moved to Kontrak controller for better structure

    /**
     * Show customer detail (sesuai dengan alur yang sudah ada)
     */
    public function showCustomer($customerId)
    {
        try {
            $customerId = (int)$customerId;
            log_message('info', 'Marketing::showCustomer - Requested customer ID: ' . $customerId);
            
            if (!$customerId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid customer ID'
                ]);
            }
            
            // Get customer data with area information
            $customer = $this->db->table('customers c')
                ->select('c.*, a.area_name')
                ->join('areas a', 'a.id = c.area_id', 'left')
                ->where('c.id', $customerId)
                ->get()
                ->getRowArray();
                
            if (!$customer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan'
                ]);
            }
            
            // Get customer locations
            $locations = $this->db->table('customer_locations')
                ->where('customer_id', $customerId)
                ->where('is_active', 1)
                ->get()
                ->getResultArray();
                
            // Get customer contracts
            $contracts = $this->db->table('kontrak')
                ->where('pelanggan', $customer['customer_name'])
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'customer' => $customer,
                    'locations' => $locations,
                    'contracts' => $contracts
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Marketing::showCustomer - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load customer details'
            ]);
        }
    }

    /**
     * Get customer detail by ID (untuk kompatibilitas)
     */
    public function getCustomerDetail($customerId)
    {
        try {
            $customerId = (int)$customerId;
            log_message('info', 'Marketing::getCustomerDetail - Requested customer ID: ' . $customerId);
            
            if (!$customerId) {
                log_message('error', 'Marketing::getCustomerDetail - Invalid customer ID: ' . $customerId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid customer ID'
                ]);
            }
            
            // Check if customers table exists and has data
            $customersCount = $this->db->table('customers')->countAllResults();
            log_message('info', 'Marketing::getCustomerDetail - Total customers in database: ' . $customersCount);
            
            // Get customer data with area information
            $customer = $this->db->table('customers c')
                ->select('c.*, a.area_name')
                ->join('areas a', 'a.id = c.area_id', 'left')
                ->where('c.id', $customerId)
                ->get()
                ->getRowArray();
                
            log_message('info', 'Marketing::getCustomerDetail - Customer query result: ' . json_encode($customer));
                
            if (!$customer) {
                log_message('error', 'Marketing::getCustomerDetail - Customer not found for ID: ' . $customerId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan'
                ]);
            }
            
            // Get customer locations count
            $locationsCount = $this->db->table('customer_locations')
                ->where('customer_id', $customerId)
                ->where('is_active', 1)
                ->countAllResults();
                
            // Get contracts count - simplified approach
            try {
                $contractsCount = $this->db->table('kontrak')
                    ->where('pelanggan', $customer['customer_name'])
                    ->countAllResults();
                log_message('info', 'Marketing::getCustomerDetail - Contracts count: ' . $contractsCount);
            } catch (\Exception $e) {
                log_message('error', 'Marketing::getCustomerDetail - Error getting contracts count: ' . $e->getMessage());
                $contractsCount = 0;
            }
                
            // Get PO count - simplified approach
            try {
                $poCount = $this->db->table('kontrak')
                    ->where('pelanggan', $customer['customer_name'])
                    ->where('no_po_marketing IS NOT NULL')
                    ->where('no_po_marketing !=', '')
                    ->countAllResults();
                log_message('info', 'Marketing::getCustomerDetail - PO count: ' . $poCount);
            } catch (\Exception $e) {
                log_message('error', 'Marketing::getCustomerDetail - Error getting PO count: ' . $e->getMessage());
                $poCount = 0;
            }
            
            // Add additional data
            $customer['locations_count'] = $locationsCount;
            $customer['contracts_count'] = $contractsCount;
            $customer['po_count'] = $poCount;
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $customer
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Marketing::getCustomerDetail - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load customer details'
            ]);
        }
    }

    // ===== WORKFLOW STAGE TRANSITION METHODS =====

    public function sendQuotation($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/marketing/quotations');
        }

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->getQuotationDetail($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            if ($quotation['workflow_stage'] !== 'QUOTATION') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation must be in QUOTATION stage to send'
                ]);
            }

            // Check if specifications exist
            $specsCount = $this->quotationSpecificationModel->where('id_quotation', $quotationId)->countAllResults();
            if ($specsCount == 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot send quotation without specifications. Please add specifications first.',
                    'require_specs' => true
                ]);
            }

            $oldSalesStage = $quotation['stage'] ?? 'DRAFT';
            $updated        = $quotationModel->update($quotationId, [
                'workflow_stage' => 'SENT',
                'stage' => 'SENT',
                'sent_at' => date('Y-m-d H:i:s')
            ]);

            if ($updated) {
                $this->insertQuotationStageHistoryIfExists(
                    (int) $quotationId,
                    (string) $oldSalesStage,
                    'SENT',
                    'send_quotation',
                    'workflow_stage → SENT'
                );
                $afterSent = array_merge($quotation, [
                    'workflow_stage' => 'SENT',
                    'stage'          => 'SENT',
                ]);
                $this->logQuotationWorkflowDocumentHistory(
                    (int) $quotationId,
                    'SENT',
                    'Quotation dikirim ke pelanggan (workflow SENT).',
                    $quotation,
                    $afterSent
                );
                // Log activity
                $this->logActivity('send_quotation', 'quotations', $quotationId, 'Quotation ' . $quotation['quotation_number'] . ' sent to customer');

                // Send notification: Quotation Sent to Customer
                helper('notification');
                if (function_exists('notify_quotation_sent_to_customer')) {
                    notify_quotation_sent_to_customer([
                        'id' => $quotationId,
                        'quote_number' => $quotation['quotation_number'],
                        'customer_name' => $quotation['customer_name'] ?? '',
                        'customer_email' => $quotation['customer_email'] ?? '',
                        'sent_method' => 'email',
                        'sent_by' => session()->get('username') ?? session()->get('first_name') ?? 'System',
                        'sent_at' => date('Y-m-d H:i:s'),
                        'url' => base_url('/marketing/quotations/view/' . $quotationId)
                    ]);
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Quotation sent successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to send quotation'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Marketing::sendQuotation - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    // REMOVED: Old markDeal() function - Use markAsDeal() instead (line ~6062)
    // The newer version has better validation, transaction handling, and auto-creates customers

    // REMOVED: Old markNotDeal() function - Use markAsNotDeal() instead (line ~6244)
    // The newer version has better validation and proper status updates

    public function createCustomer($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/marketing/quotations');
        }

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            if ($quotation['workflow_stage'] !== 'DEAL') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation must be marked as deal to create customer'
                ]);
            }

            // Check if customer already exists
            $customerModel = new \App\Models\CustomerModel();
            if (!empty($quotation['created_customer_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer already created for this quotation'
                ]);
            }

            // Create customer record using the stored procedure if available, or manual insert
            try {
                // Use the stored procedure for quotation to customer conversion
                $db = \Config\Database::connect();
                $query = $db->query("CALL sp_convert_quotation_to_deal(?, ?)", [$quotationId, session()->get('user_id')]);
                
                // Get the updated quotation to check customer creation
                $updatedQuotation = $quotationModel->find($quotationId);
                
                if (!empty($updatedQuotation['created_customer_id'])) {
                    // Log activity
                    $this->logActivity('create_customer_from_deal', 'customers', $updatedQuotation['created_customer_id'], 
                        'Customer created from deal quotation: ' . $quotation['quotation_number']);

                    // Send notification: Customer Created
                    helper('notification');
                    notify_customer_created([
                        'id' => $updatedQuotation['created_customer_id'],
                        'customer_name' => $quotation['prospect_name'] ?? '',
                        'customer_code' => '',
                        'contact_person' => $quotation['prospect_contact_person'] ?? '',
                        'phone' => $quotation['prospect_phone'] ?? ''
                    ]);

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Customer created successfully from deal. You can now create contracts and purchase orders.',
                        'customer_id' => $updatedQuotation['created_customer_id']
                    ]);
                }
                
            } catch (\Exception $spError) {
                // If stored procedure fails, fall back to manual creation
                log_message('info', 'Stored procedure failed, using manual customer creation: ' . $spError->getMessage());
                
                // Manual customer creation
                $customerData = [
                    'customer_name' => $quotation['prospect_name'],
                    'contact_person' => $quotation['prospect_contact_person'] ?? '',
                    'phone' => $quotation['prospect_phone'] ?? '',
                    'email' => $quotation['prospect_email'] ?? '',
                    'address' => $quotation['prospect_address'] ?? '',
                    'city' => $quotation['prospect_city'] ?? '',
                    'customer_type' => 'CORPORATE',
                    'status' => 'ACTIVE',
                    'created_by' => session()->get('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $customerId = $customerModel->insert($customerData);

                if ($customerId) {
                    // Update quotation with customer_id
                    $quotationModel->update($quotationId, [
                        'created_customer_id' => $customerId,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    // Log activity
                    $this->logActivity('create_customer_from_deal', 'customers', $customerId, 
                        'Customer created from deal quotation: ' . $quotation['quotation_number']);

                    // Send notification: Customer Created
                    helper('notification');
                    notify_customer_created([
                        'id' => $customerId,
                        'customer_name' => $customerData['customer_name'],
                        'customer_code' => '',
                        'contact_person' => $customerData['contact_person'],
                        'phone' => $customerData['phone']
                    ]);

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Customer created successfully from deal. You can now create contracts and purchase orders.',
                        'customer_id' => $customerId
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to create customer manually'
                    ]);
                }
            }

        } catch (\Exception $e) {
            log_message('error', 'Marketing::createCustomer - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Mark quotation as deal and auto-create customer with notification
     */
    public function markAsDeal($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/marketing/quotations');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            if ($quotation['workflow_stage'] !== 'SENT') {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only sent quotations can be marked as deal'
                ]);
            }

            // Check if specifications exist
            $hasSpecs = $this->quotationSpecificationModel->where('id_quotation', $quotationId)->countAllResults() > 0;
            if (!$hasSpecs) {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please add specifications before marking as deal'
                ]);
            }

            $allowExpiredValidUntil = (int) ($this->request->getPost('allow_expired_valid_until') ?? 0) === 1;
            if ($this->isQuotationValidUntilExpired($quotation['valid_until'] ?? null) && ! $allowExpiredValidUntil) {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Marketing.quotation_deal_expired_blocked'),
                    'code' => 'valid_until_expired',
                ])->setStatusCode(422);
            }

            if ($allowExpiredValidUntil && $this->isQuotationValidUntilExpired($quotation['valid_until'] ?? null)) {
                log_message('info', sprintf(
                    'markAsDeal: expired valid_until override — quotation_id=%s user=%s valid_until=%s',
                    (string) $quotationId,
                    (string) (session()->get('username') ?? session()->get('email') ?? 'unknown'),
                    (string) ($quotation['valid_until'] ?? '')
                ));
            }

            // Update quotation to DEAL status
            $oldSalesStage = $quotation['stage'] ?? 'SENT';
            $updated       = $quotationModel->update($quotationId, [
                'workflow_stage' => 'DEAL',
                'stage' => 'ACCEPTED',
                'is_deal' => 1,
                'deal_date' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if (!$updated) {
                throw new \Exception('Failed to update quotation status');
            }

            // Pre-compute $afterDeal for audit log (pure PHP, no DB ops)
            $afterDeal = array_merge($quotation, [
                'workflow_stage' => 'DEAL',
                'stage'          => 'ACCEPTED',
                'is_deal'        => 1,
            ]);

            // Auto-create customer from prospect data. If this fails, fallback remains available in DEAL stage.
            $customerMessage = '';
            $customerExists = false;
            $customerNeedsManualRecovery = false;
            $customerId = !empty($quotation['created_customer_id']) ? (int) $quotation['created_customer_id'] : null;
            $customerModel = new \App\Models\CustomerModel();
            
            try {
                // Simple check by name only (using correct field name)
                $existingCustomer = $customerModel->where('customer_name', $quotation['prospect_name'])->first();
                
                if ($existingCustomer) {
                    // Customer already exists
                    $customerId = $existingCustomer['id'];
                    $customerExists = true;
                    $customerMessage = 'Customer existing berhasil terhubung otomatis: ' . $existingCustomer['customer_name'];
                    
                    // Update quotation with existing customer ID
                    $quotationModel->update($quotationId, [
                        'created_customer_id' => $customerId
                    ]);
                } else {
                    // Generate unique customer code
                    $customerCode = 'CUST-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    
                    // Check if code exists and regenerate if needed
                    while ($customerModel->where('customer_code', $customerCode)->first()) {
                        $customerCode = 'CUST-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    }
                    
                    // Create basic customer record only (using correct field names)
                    $customerData = [
                        'customer_code' => $customerCode,
                        'customer_name' => $quotation['prospect_name'] ?: 'Unknown Customer',
                        'is_active' => 1
                    ];

                    log_message('info', 'Creating basic customer with data: ' . json_encode($customerData));

                    $customerId = $customerModel->insert($customerData);
                    
                    if ($customerId && $customerId > 0) {
                        $customerMessage = 'Customer baru berhasil dibuat otomatis: ' . $quotation['prospect_name'] . ' (detail dapat dilengkapi nanti)';
                        
                        // Update quotation with new customer ID
                        $quotationModel->update($quotationId, [
                            'created_customer_id' => $customerId
                        ]);
                        
                        log_message('info', 'Customer created successfully with ID: ' . $customerId);
                    } else {
                        $insertErrors = $customerModel->errors();
                        $errorMsg = 'Insert failed';
                        if (!empty($insertErrors)) {
                            $errorMsg .= ': ' . implode(', ', $insertErrors);
                        }
                        
                        log_message('error', 'Customer creation failed: ' . $errorMsg);
                        $customerNeedsManualRecovery = true;
                        $customerMessage = 'Quotation berhasil diubah ke DEAL, tetapi pembuatan customer otomatis gagal (' . $errorMsg . '). Gunakan tombol Add Customer sebagai fallback manual.';
                    }
                }
            } catch (\Exception $customerError) {
                log_message('error', 'Auto customer creation failed: ' . $customerError->getMessage());
                $customerNeedsManualRecovery = true;
                $customerMessage = 'Quotation berhasil diubah ke DEAL, tetapi pembuatan customer otomatis gagal. Gunakan tombol Add Customer sebagai fallback manual.';
            }

            // Auto-link to existing contract if available
            $contractMessage = '';
            if (isset($customerId) && $customerId) {
                try {
                    // Check if quotation already has a contract linked
                    $currentQuotation = $quotationModel->find($quotationId);
                    
                    if (!$currentQuotation['created_contract_id']) {
                        // Find existing contracts for this customer
                        $existingContracts = $this->db->query("
                            SELECT k.id, k.no_kontrak, k.status, 
                                (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as location_name
                            FROM kontrak k
                            WHERE k.customer_id = ?
                            AND k.status IN ('ACTIVE', 'PENDING')
                            ORDER BY 
                                CASE k.status 
                                    WHEN 'ACTIVE' THEN 1
                                    WHEN 'PENDING' THEN 2
                                    ELSE 3
                                END ASC,
                                k.id DESC
                            LIMIT 1
                        ", [$customerId])->getRowArray();
                        
                        if ($existingContracts) {
                            // Auto-link to best available contract
                            $quotationModel->update($quotationId, [
                                'created_contract_id' => $existingContracts['id'],
                                'customer_contract_complete' => 1
                            ]);
                            
                            $contractMessage = ' Terhubung dengan kontrak: ' . $existingContracts['no_kontrak'] . ' (' . $existingContracts['status'] . ')';
                            
                            log_message('info', "Auto-linked quotation {$quotationId} to existing contract {$existingContracts['id']} ({$existingContracts['no_kontrak']})");
                        } else {
                            $contractMessage = ' (Kontrak belum ada, perlu dibuat manual)';
                        }
                    }
                } catch (\Exception $contractError) {
                    log_message('error', 'Auto-link contract failed: ' . $contractError->getMessage());
                    $contractMessage = ' (Gagal menghubungkan kontrak otomatis)';
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'Marketing::markAsDeal - Transaction failed');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to mark as deal: Transaction failed'
                ]);
            }

            // Audit logs — run OUTSIDE transaction so insert failures cannot rollback
            // the quotation status change (quotation_history.action_type ENUM may need migration)
            $this->insertQuotationStageHistoryIfExists(
                (int) $quotationId,
                (string) $oldSalesStage,
                'ACCEPTED',
                'mark_as_deal',
                'workflow_stage → DEAL'
            );
            $this->logQuotationWorkflowDocumentHistory(
                (int) $quotationId,
                'DEAL',
                'Quotation ditandai Deal (ACCEPTED).',
                $quotation,
                $afterDeal
            );

            // Log activity
            $this->logActivity('mark_as_deal', 'quotations', $quotationId, 
                'Quotation marked as deal: ' . $quotation['quotation_number']);

            // Check customer profile completion status only if customer is already linked.
            $profileStatus = null;
            $needsProfileCompletion = false;
            if (!empty($customerId)) {
                $profileStatus = $customerModel->getCustomerProfileStatus($customerId);
                $needsProfileCompletion = !$profileStatus['complete'];
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Quotation marked as DEAL! ' . $customerMessage . $contractMessage,
                'customer_exists' => $customerExists,
                'customer_message' => $customerMessage,
                'contract_message' => $contractMessage,
                'customer_id' => $customerId,
                'needs_manual_customer_creation' => $customerNeedsManualRecovery,
                'quotation_id' => $quotationId,
                'needs_profile_completion' => $needsProfileCompletion,
                'profile_status' => $profileStatus
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Marketing::markAsDeal - Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get customer profile completion status
     */
    public function getCustomerProfileStatus($customerId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/marketing/quotations');
        }

        try {
            $customerModel = new \App\Models\CustomerModel();
            $profileStatus = $customerModel->getCustomerProfileStatus($customerId);
            
            return $this->response->setJSON([
                'success' => true,
                'profile_status' => $profileStatus
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::getCustomerProfileStatus - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Mark quotation as not deal
     */
    public function markAsNotDeal($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/marketing/quotations');
        }

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            if ($quotation['workflow_stage'] !== 'SENT') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only sent quotations can be marked as not deal'
                ]);
            }

            $oldSalesStage = $quotation['stage'] ?? 'SENT';
            $updated       = $quotationModel->update($quotationId, [
                'workflow_stage' => 'NOT_DEAL',
                'stage' => 'REJECTED',
                'is_deal' => 0,
                'rejected_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($updated) {
                $this->insertQuotationStageHistoryIfExists(
                    (int) $quotationId,
                    (string) $oldSalesStage,
                    'REJECTED',
                    'mark_as_not_deal',
                    'workflow_stage → NOT_DEAL'
                );
                $afterNotDeal = array_merge($quotation, [
                    'workflow_stage' => 'NOT_DEAL',
                    'stage'          => 'REJECTED',
                    'is_deal'        => 0,
                ]);
                $this->logQuotationWorkflowDocumentHistory(
                    (int) $quotationId,
                    'REJECTED',
                    'Quotation ditandai tidak deal (ditutup).',
                    $quotation,
                    $afterNotDeal
                );
                // Log activity
                $this->logActivity('mark_as_not_deal', 'quotations', $quotationId, 
                    'Quotation marked as not deal: ' . $quotation['quotation_number']);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Quotation marked as NOT DEAL and closed.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to mark quotation as not deal'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Marketing::markAsNotDeal - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Check if customer profile is complete
     */
    private function isCustomerProfileComplete($customerId)
    {
        if (!$customerId) {
            return false;
        }

        $customerModel = new \App\Models\CustomerModel();
        $customerLocationModel = new \App\Models\CustomerLocationModel();

        // Check basic customer data
        $customer = $customerModel->find($customerId);
        if (!$customer) {
            return false;
        }

        // Check if customer has at least one complete location
        $locations = $customerLocationModel->where('customer_id', $customerId)
                                          ->where('is_active', 1)
                                          ->findAll();

        $hasCompleteLocation = false;
        foreach ($locations as $location) {
            // Consider location complete if it has: address, city, province, and contact info
            if (!empty($location['address']) && 
                !empty($location['city']) && 
                !empty($location['province']) &&
                !empty($location['contact_person']) &&
                $location['address'] !== 'Alamat belum ditentukan' &&
                $location['city'] !== 'Kota belum ditentukan' &&
                $location['province'] !== 'Provinsi belum ditentukan') {
                $hasCompleteLocation = true;
                break;
            }
        }

        return $hasCompleteLocation;
    }

    /**
     * Create customer from deal quotation
     */
    public function createCustomerFromDeal($quotationId)
    {
        // Alias for createCustomer method for better naming
        return $this->createCustomer($quotationId);
    }

    /**
     * Create contract from deal quotation
     */
    public function createContract($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/marketing/quotations');
        }
        $canCreateContract = $this->hasPermission('marketing.kontrak.create')
            || $this->hasPermission('marketing.contract.create')
            || $this->hasPermission('marketing.quotation.edit')
            || $this->canManage('marketing');
        if (!$canCreateContract) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            if ($quotation['workflow_stage'] !== 'DEAL' || empty($quotation['created_customer_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation must be a deal with customer created first'
                ]);
            }

            // Validate customer profile completion
            $customerModel = new \App\Models\CustomerModel();
            if (!$customerModel->isCustomerProfileComplete($quotation['created_customer_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer profile must be completed before creating contract',
                    'require_profile_completion' => true,
                    'customer_id' => $quotation['created_customer_id']
                ]);
            }

            if (!empty($quotation['created_contract_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contract already created for this quotation'
                ]);
            }

            // Get customer primary location or first location
            $customerLocation = $this->db->table('customer_locations')
                ->where('customer_id', $quotation['created_customer_id'])
                ->where('is_active', 1)
                ->orderBy('is_primary', 'DESC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getRowArray();

            if (!$customerLocation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No active location found for this customer. Please add a location first.'
                ]);
            }

            // Generate contract number
            $contractNumber = $this->generateContractNumberInternal();

            // Create contract record with correct schema (customer_id, not customer_location_id)
            // Note: customer_location_id moved to kontrak_unit table per March 5, 2026 schema change
            $contractData = [
                'no_kontrak' => $contractNumber,
                'customer_id' => $quotation['created_customer_id'],  // Use customer_id instead
                'nilai_total' => $quotation['total_amount'],
                'tanggal_mulai' => date('Y-m-d'),
                'tanggal_berakhir' => date('Y-m-d', strtotime('+12 months')),
                'status' => 'PENDING',
                'dibuat_oleh' => session()->get('user_id')
            ];

            log_message('info', 'Creating contract with customer: ' . json_encode([
                'customer_id' => $quotation['created_customer_id'],
                'primary_location_id' => $customerLocation['id'],
                'location_name' => $customerLocation['location_name'],
                'is_primary' => $customerLocation['is_primary']
            ]));

            $kontrakModel = new \App\Models\KontrakModel();
            $contractId = $kontrakModel->insert($contractData);

            if ($contractId) {
                // Update quotation with contract_id and mark contract as complete
                $quotationModel->update($quotationId, [
                    'created_contract_id' => $contractId,
                    'customer_contract_complete' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Log activity
                $this->logActivity('create_contract', 'kontrak', $contractId, 
                    'Contract created from quotation: ' . $quotation['quotation_number']);

                // Send notification: Contract Created
                helper('notification');
                if (!isset($this->customerModel)) {
                    $this->customerModel = new \App\Models\CustomerModel();
                }
                $customer = $this->customerModel->find($quotation['created_customer_id']);
                if ($customer) {
                    notify_customer_contract_created([
                        'id' => $contractId,
                        'contract_number' => $contractNumber,
                        'customer_name' => $customer['customer_name'],
                        'nilai_total' => $contractData['nilai_total'],
                        'tanggal_mulai' => $contractData['tanggal_mulai'],
                        'tanggal_selesai' => $contractData['tanggal_berakhir']
                    ]);
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Contract created successfully: ' . $contractNumber,
                    'contract_id' => $contractId,
                    'contract_number' => $contractNumber
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create contract'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Marketing::createContract - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Create purchase order from deal quotation
     */
    public function createPurchaseOrder($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/marketing/quotations');
        }

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            if ($quotation['workflow_stage'] !== 'DEAL' || empty($quotation['created_customer_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation must be a deal with customer created first'
                ]);
            }

            // For now, just return success with a message to manually create PO
            // This can be enhanced later with actual PO creation logic
            
            // Send notification - PO created from quotation (placeholder notification)
            if (function_exists('notify_po_created_from_quotation')) {
                notify_po_created_from_quotation([
                    'id' => null, // PO not actually created yet
                    'po_number' => 'PENDING',
                    'quotation_number' => $quotation['quotation_number'] ?? '',
                    'customer_name' => $quotation['prospect_name'] ?? '',
                    'created_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/purchasing/po')
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Please create Purchase Order manually in the Purchase Order module for customer: ' . $quotation['prospect_name'],
                'redirect_to_po' => true,
                'customer_id' => $quotation['created_customer_id']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::createPurchaseOrder - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Create SPK from completed deal
     */
    public function createSPK($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/marketing/quotations');
        }
        $canCreateSpk = $this->hasPermission('marketing.spk.create')
            || $this->hasPermission('marketing.kontrak.create')
            || $this->canManage('marketing');
        if (!$canCreateSpk) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            if ($quotation['workflow_stage'] !== 'DEAL' || 
                empty($quotation['created_customer_id']) || 
                empty($quotation['created_contract_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contract must be created first before creating SPK'
                ]);
            }

            // Validate customer profile completion
            if (!$this->isCustomerProfileComplete($quotation['created_customer_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer profile must be completed before creating SPK',
                    'require_profile_completion' => true,
                    'customer_id' => $quotation['created_customer_id']
                ]);
            }

            // Redirect to SPK creation with quotation data
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Redirecting to SPK creation...',
                'redirect_to_spk' => true,
                'quotation_id' => $quotationId,
                'contract_id' => $quotation['created_contract_id'],
                'customer_id' => $quotation['created_customer_id'],
                'redirect_url' => base_url('marketing/spk/create?quotation_id=' . $quotationId . '&contract_id=' . $quotation['created_contract_id'])
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::createSPK - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Print Quotation PDF - Professional format for customer
     */
    public function quotationPrint($id)
    {
        $id = (int)$id;
        
        // Get quotation data
        $quotation = $this->quotationModel->find($id);
        if (!$quotation) {
            return $this->response->setStatusCode(404)->setBody('Quotation tidak ditemukan');
        }

        // Get selected specs from URL parameter
        $selectedSpecs = $this->request->getGet('specs');
        $specIds = [];
        if (!empty($selectedSpecs)) {
            $specIds = array_map('intval', explode(',', $selectedSpecs));
        }

        // Get quotation specifications with related data
        $builder = $this->db->table('quotation_specifications qs')
            ->select('qs.*')
            ->select('tu.jenis as unit_type, tu.tipe as unit_subtype')
            ->select('k.kapasitas_unit as capacity_name')
            ->select('d.nama_departemen as department_name')
            ->select('mu.merk_unit as brand_name, mu.model_unit as model_name')
            // Text columns override JOIN values for new free-text records
            ->select('COALESCE(qs.departemen_text, d.nama_departemen) as display_department')
            ->select('COALESCE(qs.tipe_unit_text, tu.jenis) as display_unit_type')
            ->select('COALESCE(qs.kapasitas_text, k.kapasitas_unit) as display_capacity')
            ->select('COALESCE(qs.merk_unit_text, mu.merk_unit) as display_brand')
            ->select('tm.tipe_mast as mast_name')
            ->select('jr.tipe_roda as wheel_name')
            ->select('tb.tipe_ban as tire_name')
            ->select('v.jumlah_valve as valve_name')
            ->select('chr.merk_charger as charger_brand, chr.tipe_charger as charger_type')
            ->select('att.tipe as attachment_type, att.merk as attachment_brand, att.model as attachment_model')
            ->select('fk.name as quotation_fork_name, fk.fork_class as quotation_fork_class')
            ->select('bat.merk_baterai as battery_brand, bat.tipe_baterai as battery_type, bat.jenis_baterai as jenis_baterai')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = qs.tipe_unit_id', 'left')
            ->join('kapasitas k', 'k.id_kapasitas = qs.kapasitas_id', 'left')
            ->join('departemen d', 'd.id_departemen = qs.departemen_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = qs.brand_id', 'left')
            ->join('tipe_mast tm', 'tm.id_mast = qs.mast_id', 'left')
            ->join('jenis_roda jr', 'jr.id_roda = qs.roda_id', 'left')
            ->join('tipe_ban tb', 'tb.id_ban = qs.ban_id', 'left')
            ->join('valve v', 'v.id_valve = qs.valve_id', 'left')
            ->join('charger chr', 'chr.id_charger = qs.charger_id', 'left')
            ->join('attachment att', 'att.id_attachment = qs.attachment_id', 'left')
            ->join('fork fk', 'fk.id = qs.fork_id', 'left')
            ->join('baterai bat', 'bat.id = qs.battery_id', 'left')
            ->where('qs.id_quotation', $id)
            ->where('qs.is_active', 1);
        
        // Filter by selected specs if provided
        if (!empty($specIds)) {
            $builder->whereIn('qs.id_specification', $specIds);
        }
        
        $specifications = $builder
            ->orderBy('qs.specification_type', 'ASC')
            ->orderBy('qs.id_specification', 'ASC')
            ->get()
            ->getResultArray();

        // Get created by user info (prefer full name over username)
        $createdBy = $this->db->table('users')->where('id', $quotation['created_by'])->get()->getRowArray();
        $createdByFullName = trim(((string)($createdBy['first_name'] ?? '')) . ' ' . ((string)($createdBy['last_name'] ?? '')));
        $quotation['created_by_name'] = $createdByFullName !== ''
            ? $createdByFullName
            : ($createdBy['nama'] ?? 'Marketing Manager');
        $quotation['created_by_phone'] = $createdBy['no_hp'] ?? $createdBy['phone'] ?? '';

        // Get assigned to user info
        if (!empty($quotation['assigned_to'])) {
            $assignedTo = $this->db->table('users')->where('id', $quotation['assigned_to'])->get()->getRowArray();
            $assignedToFullName = trim(((string)($assignedTo['first_name'] ?? '')) . ' ' . ((string)($assignedTo['last_name'] ?? '')));
            $quotation['assigned_to_name'] = $assignedToFullName !== ''
                ? $assignedToFullName
                : ($assignedTo['nama'] ?? 'Assigned User');
        }

        return view('marketing/print_quotation', [
            'quotation' => $quotation,
            'specifications' => $specifications
        ]);
    }

    /**
     * Add specifications to quotation
     */
    public function addSpecifications($quotationId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/marketing/quotations');
        }

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Opening specifications modal...',
                'open_specifications' => true,
                'quotation_id' => $quotationId
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::addSpecifications - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    // ============================================================================
    // QUOTATION-BASED WORKFLOW - NEW METHODS
    // ============================================================================

    /**
     * Link SPK to Contract (late-linking mechanism)
     * Called when contract documentation arrives after operational start
     */
    public function linkSPKToContract()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $spkId = $this->request->getPost('spk_id');
            $contractId = $this->request->getPost('contract_id');
            $bastDate = $this->request->getPost('bast_date'); // Optional

            if (empty($spkId) || empty($contractId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SPK ID and Contract ID required'
                ]);
            }

            $spkModel = new \App\Models\SpkModel();
            $userId = session()->get('user_id') ?? 1;

            // Link SPK to contract (triggers auto-propagation to DIs)
            $result = $spkModel->linkToContract($spkId, $contractId, $userId);

            if ($result['success']) {
                // If BAST date provided, update related DIs
                if (!empty($bastDate)) {
                    $diModel = new \App\Models\DeliveryInstructionModel();
                    $dis = $diModel->where('spk_id', $spkId)->findAll();
                    
                    foreach ($dis as $di) {
                        $diModel->setBillingStartDate($di['id'], $bastDate);
                    }
                }

                // Check for late-linking scenario and trigger instant invoice generation
                $lateInvoicesGenerated = 0;
                $db = \Config\Database::connect();
                $builder = $db->table('delivery_instructions');
                $dis = $builder->where('spk_id', $spkId)
                    ->where('status_di', 'SELESAI')
                    ->where('sampai_tanggal_approve IS NOT NULL', null, false)
                    ->get()
                    ->getResultArray();
                
                if (!empty($dis)) {
                    $invoiceJob = new \App\Jobs\InvoiceAutomationJob();
                    
                    foreach ($dis as $di) {
                        // Check if DI completed more than 30 days ago
                        if (!empty($di['sampai_tanggal_approve'])) {
                            $completedDate = strtotime($di['sampai_tanggal_approve']);
                            $daysPassed = floor((time() - $completedDate) / (60 * 60 * 24));
                            
                            if ($daysPassed >= 30 && empty($di['invoice_generated'])) {
                                try {
                                    $invoiceGenerated = $invoiceJob->handleLateLinkedDI($di['id']);
                                    if ($invoiceGenerated) {
                                        $lateInvoicesGenerated++;
                                    }
                                } catch (\Exception $e) {
                                    log_message('error', 'Marketing::linkSPKToContract - Late invoice generation failed for DI #' . $di['id'] . ': ' . $e->getMessage());
                                }
                            }
                        }
                    }
                }

                // Send success notification to Marketing and Finance teams
                $this->sendLinkingSuccessNotification($spkId, $contractId, $result['di_count']);
                
                // Prepare success message with invoice info if any were generated
                $message = $result['message'];
                if ($lateInvoicesGenerated > 0) {
                    $message .= " Note: {$lateInvoicesGenerated} invoice(s) were automatically generated due to late-linking (>30 days after delivery completion).";
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message,
                    'di_count' => $result['di_count'],
                    'late_invoices_generated' => $lateInvoicesGenerated
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::linkSPKToContract - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Link Delivery Instruction to Contract (Late Binding)
     * Allows individual DI to be linked to contract independent of SPK
     * Useful for cases where contract finalized after DI creation
     */
    public function linkDIToContract()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $diId = $this->request->getPost('di_id');
            $contractId = $this->request->getPost('contract_id');
            $bastDate = $this->request->getPost('bast_date'); // Optional

            if (empty($diId) || empty($contractId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'DI ID and Contract ID required'
                ]);
            }

            $diModel = new \App\Models\DeliveryInstructionModel();
            $kontrakModel = new \App\Models\KontrakModel();
            $userId = session()->get('user_id') ?? 1;

            // Validate DI exists and not already linked
            $di = $diModel->find($diId);
            if (!$di) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Delivery Instruction not found'
                ]);
            }

            if ($di['contract_id'] !== null) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'DI already linked to contract'
                ]);
            }

            // Validate contract exists
            $contract = $kontrakModel->find($contractId);
            if (!$contract) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }

            // Update DI with contract linkage
            $poNumber = trim($this->request->getPost('po_number') ?? '');
            $updateData = [
                'contract_id'        => $contractId,
                'pelanggan_id'       => $contract['customer_id'] ?? null,
                'contract_linked_at' => date('Y-m-d H:i:s'),
                'contract_linked_by' => $userId,
                'status_di'          => 'SUBMITTED',
                'diperbarui_pada'    => date('Y-m-d H:i:s'),
            ];

            // If a specific PO Bulanan number is provided, save it
            if (!empty($poNumber)) {
                $updateData['po_kontrak_nomor'] = $poNumber;
            }

            // If BAST date provided, set billing start date
            if (!empty($bastDate)) {
                $updateData['bast_date']          = $bastDate;
                $updateData['billing_start_date'] = $bastDate;
            }

            $updated = $diModel->update($diId, $updateData);

            if (!$updated) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update DI'
                ]);
            }

            // Send notification to Finance team
            $this->sendDILinkingSuccessNotification($diId, $contractId);

            return $this->response->setJSON([
                'success' => true,
                'message' => "DI {$di['nomor_di']} linked to contract {$contract['no_kontrak']} successfully. Ready for invoice generation.",
                'di_number' => $di['nomor_di'],
                'contract_number' => $contract['no_kontrak']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::linkDIToContract - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Send notification when DI linked to contract
     */
    private function sendDILinkingSuccessNotification($diId, $contractId)
    {
        try {
            $notificationModel = new \App\Models\NotificationModel();
            $diModel = new \App\Models\DeliveryInstructionModel();
            $kontrakModel = new \App\Models\KontrakModel();

            $di = $diModel->find($diId);
            $contract = $kontrakModel->find($contractId);

            if ($di && $contract) {
                // Notify Finance team (division_id = 4)
                $notificationModel->createCrossDivisionNotification(
                    4, // Finance division
                    "DI {$di['nomor_di']} linked to contract {$contract['no_kontrak']}. Invoice generation now available.",
                    "/finance/invoices?di_id={$diId}",
                    'FINANCE',
                    'high',
                    session()->get('user_id')
                );
            }
        } catch (\Exception $e) {
            log_message('error', 'Gagal memproses permintaan. Silakan coba lagi.');
        }
    }

    /**
     * Get contracts by customer ID for DI linking
     * Returns all DEAL contracts for the specified customer
     */
    public function getContractsByCustomer($customerId = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Customer ID is required'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Query contracts for this customer (table is 'kontrak')
            $query = $db->table('kontrak k')
                ->select('k.id, k.no_kontrak, k.no_kontrak as nomor_kontrak, c.customer_name, k.rental_type, k.tanggal_mulai, k.tanggal_mulai as tanggal_kontrak, k.tanggal_berakhir as tanggal_selesai, k.status, k.status as status_kontrak')
                ->join('customers c', 'c.id = k.customer_id', 'left')
                ->where('k.customer_id', $customerId)
                ->orderBy('k.tanggal_mulai', 'DESC')
                ->get();
            
            $contracts = $query->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $contracts
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::getContractsByCustomer - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get linkable contracts and PO Bulanan for a DI.
     * Resolves customer from pelanggan_id → spk.kontrak → customer name lookup.
     * Returns contracts (all rental_type) and active PO Bulanan (contract_po_history).
     */
    public function getLinkableContractsForDI($diId = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        if (!$diId) {
            return $this->response->setJSON(['success' => false, 'message' => 'DI ID diperlukan']);
        }

        try {
            $db = \Config\Database::connect();

            $di = $db->table('delivery_instructions')
                ->select('id, nomor_di, pelanggan, pelanggan_id, spk_id, contract_id')
                ->where('id', (int)$diId)
                ->get()->getRowArray();

            if (!$di) {
                return $this->response->setJSON(['success' => false, 'message' => 'DI tidak ditemukan']);
            }

            $customerId = $di['pelanggan_id'] ? (int)$di['pelanggan_id'] : null;

            // Try via SPK → kontrak
            if (!$customerId && !empty($di['spk_id'])) {
                $spk = $db->table('spk')->select('kontrak_id')->where('id', (int)$di['spk_id'])->get()->getRowArray();
                if ($spk && !empty($spk['kontrak_id'])) {
                    $k = $db->table('kontrak')->select('customer_id')->where('id', (int)$spk['kontrak_id'])->get()->getRowArray();
                    if ($k && !empty($k['customer_id'])) {
                        $customerId = (int)$k['customer_id'];
                    }
                }
            }

            // Try by pelanggan name
            if (!$customerId && !empty($di['pelanggan'])) {
                $cust = $db->table('customers')->select('id')->where('customer_name', $di['pelanggan'])->where('deleted_at IS NULL', null, false)->limit(1)->get()->getRowArray();
                if ($cust) $customerId = (int)$cust['id'];
            }

            $customerName = $di['pelanggan'];
            if ($customerId) {
                $c = $db->table('customers')->select('customer_name')->where('id', $customerId)->get()->getRowArray();
                if ($c) $customerName = $c['customer_name'];
            }

            // Get contracts for this customer
            $contracts = [];
            if ($customerId) {
                $contracts = $db->table('kontrak k')
                    ->select('k.id, k.no_kontrak, k.rental_type, k.status, k.tanggal_mulai, k.tanggal_berakhir, c.customer_name')
                    ->join('customers c', 'c.id = k.customer_id', 'left')
                    ->where('k.customer_id', $customerId)
                    ->orderBy('k.status = "ACTIVE"', 'DESC', false)
                    ->orderBy('k.tanggal_mulai', 'DESC')
                    ->get()->getResultArray();
            }

            // Get active PO Bulanan entries
            $poBulanan = [];
            if (!empty($contracts)) {
                $contractIds = array_column($contracts, 'id');
                $poBulanan = $db->table('contract_po_history cph')
                    ->select('cph.id, cph.po_number, cph.contract_id, cph.effective_from, cph.effective_to, cph.status, k.no_kontrak as contract_no')
                    ->join('kontrak k', 'k.id = cph.contract_id', 'left')
                    ->whereIn('cph.contract_id', $contractIds)
                    ->where('cph.status', 'ACTIVE')
                    ->orderBy('cph.effective_from', 'DESC')
                    ->get()->getResultArray();
            }

            return $this->response->setJSON([
                'success'        => true,
                'customer_id'    => $customerId,
                'customer_name'  => $customerName,
                'contracts'      => $contracts,
                'po_bulanan'     => $poBulanan,
                'already_linked' => !empty($di['contract_id']),
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::getLinkableContractsForDI - Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get unlinked SPKs for contract linking
     * Used in contract creation/detail view
     */
    public function getSPKsForContractLinking($customerId = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $spkModel = new \App\Models\SpkModel();
            $unlinkedSPKs = $spkModel->getUnlinkedSPKs($customerId);

            return $this->response->setJSON([
                'success' => true,
                'data' => $unlinkedSPKs
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::getSPKsForContractLinking - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Check if SPK has contract (for DI status determination)
     */
    public function checkSPKHasContract($spkId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $spkModel = new \App\Models\SpkModel();
            $hasContract = $spkModel->hasContract($spkId);

            return $this->response->setJSON([
                'success' => true,
                'has_contract' => $hasContract
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::checkSPKHasContract - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Renew contract without re-delivery
     * Creates new contract linked to original via contract_renewals table
     */
    public function renewContract($contractId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $kontrakModel = new \App\Models\KontrakModel();
            $renewalModel = new \App\Models\ContractRenewalModel();

            // Validate eligibility
            $eligibility = $renewalModel->checkRenewalEligibility($contractId);
            
            if (!$eligibility['eligible']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contract cannot be renewed: ' . implode(', ', $eligibility['reasons'])
                ]);
            }

            // Get original contract
            $originalContract = $kontrakModel->find($contractId);
            
            if (!$originalContract) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Original contract not found'
                ]);
            }

            // Get renewal data from request
            $newStartDate = $this->request->getPost('start_date');
            $newEndDate = $this->request->getPost('end_date');
            $newRates = $this->request->getPost('rates'); // Optional rate changes
            $sameLocation = $this->request->getPost('same_location') ?? true;
            $notes = $this->request->getPost('notes') ?? '';

            // Generate new contract number
            // Simple approach: append -R1, -R2, etc.
            $renewalCount = $renewalModel->where('original_contract_id', $contractId)->countAllResults() + 1;
            $newContractNumber = $originalContract['no_kontrak'] . '-R' . $renewalCount;

            // Create new contract
            // Note: customer_location_id REMOVED from kontrak table (March 5, 2026)
            // Using customer_id instead; location tracking is in kontrak_unit table
            $newContractData = [
                'no_kontrak' => $newContractNumber,
                'customer_id' => $originalContract['customer_id'],  // Use customer_id instead
                'no_po_marketing' => $this->request->getPost('po_number') ?? $originalContract['no_po_marketing'],
                'nilai_total' => $newRates ?? $originalContract['nilai_total'],
                'total_units' => $originalContract['total_units'],
                'jenis_sewa' => $originalContract['jenis_sewa'],
                'tanggal_mulai' => $newStartDate,
                'tanggal_berakhir' => $newEndDate,
                'status' => 'ACTIVE',
                'dibuat_oleh' => session()->get('user_id') ?? 1,
                'dibuat_pada' => date('Y-m-d H:i:s'),
                'diperbarui_pada' => date('Y-m-d H:i:s')
            ];

            if ($kontrakModel->insert($newContractData)) {
                $newContractId = $kontrakModel->getInsertID();

                // Copy specifications from original contract
                $kontrakSpesifikasiModel = new \App\Models\KontrakSpesifikasiModel();
                $originalSpecs = $kontrakSpesifikasiModel->where('kontrak_id', $contractId)->findAll();

                foreach ($originalSpecs as $spec) {
                    $newSpec = $spec;
                    unset($newSpec['id']);
                    $newSpec['kontrak_id'] = $newContractId;
                    
                    // Apply new rates if provided
                    if (!empty($newRates)) {
                        $newSpec['harga_per_unit_bulanan'] = $newRates;
                    }
                    
                    $kontrakSpesifikasiModel->insert($newSpec);
                }

                // Create renewal record
                $renewalData = [
                    'original_contract_id' => $contractId,
                    'renewed_contract_id' => $newContractId,
                    'renewal_date' => date('Y-m-d'),
                    'same_location' => $sameLocation,
                    'notes' => $notes,
                    'created_by' => session()->get('user_id') ?? 1
                ];

                $renewalModel->createRenewal($contractId, $newContractId, $renewalData);

                // Update billing schedule
                $scheduleModel = new \App\Models\RecurringBillingScheduleModel();
                $scheduleModel->createSchedule($newContractId);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Contract renewed successfully',
                    'new_contract_id' => $newContractId,
                    'contract_number' => $newContractNumber
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create renewed contract'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::renewContract - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Create contract amendment for price/term changes
     */
    public function createAmendment($contractId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $amendmentModel = new \App\Models\ContractAmendmentModel();

            $amendmentData = [
                'parent_contract_id' => $contractId,
                'reason' => $this->request->getPost('reason'),
                'new_monthly_rate' => $this->request->getPost('new_rate'),
                'effective_date' => $this->request->getPost('effective_date'),
                'created_by' => session()->get('user_id') ?? 1
            ];

            // Validate effective date
            $validation = $amendmentModel->validateEffectiveDate($contractId, $amendmentData['effective_date']);
            
            if (!$validation['valid']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid effective date',
                    'errors' => $validation['errors']
                ]);
            }

            $amendmentId = $amendmentModel->createAmendment($contractId, $amendmentData);

            if ($amendmentId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Amendment created successfully',
                    'amendment_id' => $amendmentId,
                    'warnings' => $validation['warnings'] ?? []
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create amendment'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::createAmendment - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    // ============================================================================
    // NOTIFICATION HELPERS
    // ============================================================================

    /**
     * Send success notification when SPK is linked to contract
     */
    protected function sendLinkingSuccessNotification($spkId, $contractId, $diCount)
    {
        try {
            $notificationModel = new \App\Models\NotificationModel();
            $spkModel = new \App\Models\SpkModel();
            $kontrakModel = new \App\Models\KontrakModel();
            
            $spk = $spkModel->find($spkId);
            $contract = $kontrakModel->find($contractId);
            
            if (!$spk || !$contract) {
                return;
            }

            // Get Finance team users
            $financeUsers = $this->getFinanceTeamUsers();
            
            // Get the user who performed the linking
            $linkingUser = session()->get('user_id') ?? 1;

            $title = "✓ SPK Berhasil Link ke Kontrak";
            $message = "SPK {$spk['nomor_spk']} telah berhasil di-link ke kontrak {$contract['no_kontrak']}. "
                     . "{$diCount} DI telah di-update dan siap untuk invoicing. "
                     . "Customer: {$spk['pelanggan']}.";

            $options = [
                'type' => 'success',
                'icon' => 'check-circle',
                'module' => 'spk',
                'id' => $spkId,
                'url' => "/marketing/spk/detail/{$spkId}"
            ];

            // Notify Finance team
            if (!empty($financeUsers)) {
                $notificationModel->sendToMultiple($financeUsers, $title, $message, $options);
            }

            // Also notify the linking user
            $notificationModel->send($linkingUser, $title, $message, $options);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::sendLinkingSuccessNotification - Error: ' . $e->getMessage());
            // Don't throw, just log - notification failure shouldn't break the main operation
        }
    }

    /**
     * Get Finance team user IDs
     */
    protected function getFinanceTeamUsers()
    {
        try {
            $db = \Config\Database::connect();

            $query = $db->table('users u')
                ->select('u.id')
                ->join('divisions d', 'd.id = u.division_id', 'left')
                ->where('u.is_active', 1)
                ->groupStart()
                    ->like('d.name', 'Finance', 'both')
                    ->orLike('d.name', 'Accounting', 'both')
                    ->orLike('d.name', 'Keuangan', 'both')
                ->groupEnd()
                ->get();

            $users = $query->getResultArray();
            
            return array_column($users, 'id');

        } catch (\Exception $e) {
            log_message('error', 'Marketing::getFinanceTeamUsers - Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Convert prospect to permanent customer
     * Called when DEAL quotation needs customer record creation
     * 
     * @param int $quotationId
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function convertProspectToCustomer($quotationId = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        try {
            if (empty($quotationId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation ID required'
                ]);
            }
            
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);
            
            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation tidak ditemukan'
                ]);
            }
            
            // Validate quotation is DEAL stage and not already converted
            if ($quotation['workflow_stage'] !== 'DEAL') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only DEAL quotations can be converted to customers'
                ]);
            }
            
            if (!empty($quotation['created_customer_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This quotation has already been converted to a customer'
                ]);
            }
            
            // Generate customer code
            $customerModel = new \App\Models\CustomerModel();
            $customerCode = $customerModel->generateCustomerCode();
            
            // Prepare customer data from quotation
            $customerData = [
                'customer_code' => $customerCode,
                'customer_name' => $quotation['prospect_name'],
                'customer_type' => 'RENTAL', // Default type, can be changed later
                'industry' => null,
                'company_npwp' => null,
                'address' => $quotation['prospect_address'] ?? null,
                'city' => null,
                'postal_code' => null,
                'phone' => $quotation['phone'] ?? null,
                'email' => $quotation['email'] ?? null,
                'contact_person' => $quotation['contact_person'] ?? null,
                'status' => 'ACTIVE',
                'notes' => "Converted from quotation #{$quotation['quotation_number']}",
                'created_by' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Insert customer record
            $customerId = $customerModel->insert($customerData);
            
            if (!$customerId) {
                throw new \Exception('Failed to create customer record');
            }
            
            // Create primary location if address available
            if (!empty($quotation['prospect_address'])) {
                $locationModel = new \App\Models\CustomerLocationModel();
                $locationData = [
                    'customer_id' => $customerId,
                    'location_name' => 'Primary Location',
                    'address' => $quotation['prospect_address'],
                    'city' => null,
                    'postal_code' => null,
                    'pic_name' => $quotation['contact_person'] ?? null,
                    'pic_phone' => $quotation['phone'] ?? null,
                    'pic_email' => $quotation['email'] ?? null,
                    'is_primary' => 1,
                    'is_active' => 1,
                    'created_by' => session()->get('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $locationModel->insert($locationData);
            }
            
            // Update quotation with customer reference
            $quotationModel->update($quotationId, [
                'created_customer_id' => $customerId,
                'customer_converted_at' => date('Y-m-d H:i:s')
            ]);
            
            // Log activity
            log_message('info', "Marketing::convertProspectToCustomer - Converted quotation #{$quotationId} to customer #{$customerId} ({$customerCode})");
            
            // Send notification to user
            $notificationModel = new \App\Models\NotificationModel();
            $userId = session()->get('user_id');
            
            $notificationModel->createNotification([
                'user_id' => $userId,
                'title' => 'Customer Created from Quotation',
                'message' => "Prospect '{$quotation['prospect_name']}' has been successfully converted to customer {$customerCode}.",
                'type' => 'success',
                'category' => 'customer',
                'url' => "/marketing/customer-management?id={$customerId}",
                'is_system_generated' => 0
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Prospect successfully converted to customer!",
                'customer_id' => $customerId,
                'customer_code' => $customerCode
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Marketing::convertProspectToCustomer - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get contracts available for SPK linking (by customer from SPK's quotation)
     */
    public function getContractsForSPKLinking($spkId = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $spkId = $spkId ?? $this->request->getGet('spk_id');
            if (!$spkId) {
                return $this->response->setJSON(['success' => false, 'message' => 'SPK ID required']);
            }

            $spk = $this->spkModel->find((int) $spkId);
            if (!$spk) {
                return $this->response->setJSON(['success' => false, 'message' => 'SPK tidak ditemukan']);
            }

            // Get customer from quotation chain
            $customerId = null;
            if (!empty($spk['quotation_specification_id'])) {
                $row = $this->db->table('quotation_specifications qs')
                    ->select('q.customer_id')
                    ->join('quotations q', 'q.id_quotation = qs.id_quotation', 'left')
                    ->where('qs.id_specification', $spk['quotation_specification_id'])
                    ->get()->getRowArray();
                $customerId = $row['customer_id'] ?? null;
            }

            $builder = $this->db->table('kontrak')
                ->select('kontrak.id, kontrak.no_kontrak, kontrak.pelanggan as customer_name, kontrak.tanggal_mulai, kontrak.tanggal_selesai, kontrak.status');

            if ($customerId) {
                $builder->where('kontrak.customer_id', $customerId);
            }

            $contracts = $builder->orderBy('kontrak.tanggal_mulai', 'DESC')->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data'    => $contracts,
                'spk'     => ['id' => $spk['id'], 'nomor_spk' => $spk['nomor_spk']],
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::getContractsForSPKLinking - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat kontrak. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Update DI fields (partial update via AJAX)
     */
    public function diUpdate($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $di = $this->diModel->find((int) $id);
        if (!$di) {
            return $this->response->setJSON(['success' => false, 'message' => 'DI tidak ditemukan']);
        }

        try {
            $data = $this->request->getPost();
            // Only allow safe fields to be updated
            $allowed = ['catatan', 'status', 'status_di', 'tanggal_kirim', 'lokasi', 'pelanggan',
                        'nama_supir', 'no_hp_supir', 'no_sim_supir', 'kendaraan', 'no_polisi_kendaraan',
                        'estimasi_sampai', 'diperbarui_pada'];
            $updateData = array_intersect_key($data, array_flip($allowed));
            $updateData['diperbarui_pada'] = date('Y-m-d H:i:s');

            $this->diModel->update((int) $id, $updateData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'DI berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Marketing::diUpdate - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.'
            ]);
        }
    }
}
