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
use App\Models\DeliveryInstructionModel;
use App\Models\DeliveryItemModel;
use App\Models\NotificationModel;
use App\Traits\ActivityLoggingTrait;
use Dompdf\Dompdf;
use Dompdf\Options;

class Marketing extends BaseDataTableController
{
    use ActivityLoggingTrait;
    
    protected $db;
    protected $spkModel;
    protected $kontrakModel;
    protected $kontrakSpesifikasiModel;
    protected $quotationModel;
    protected $quotationSpecificationModel;
    protected $unitModel;
    protected $attModel;
    protected $diModel;
    protected $diItemModel;
    protected $notifModel;
    protected $performanceService;

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
        $this->diModel = new DeliveryInstructionModel();
        $this->diItemModel = new DeliveryItemModel();
        $this->notifModel = class_exists(\App\Models\NotificationModel::class) ? new NotificationModel() : null;
        $this->performanceService = new \App\Services\PerformanceService();
    }

    /**
     * Marketing dashboard index page
     */
    public function index()
    {
        // Load simple_rbac helper
        helper('simple_rbac');
        
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
            'revenue_data' => $revenue_data
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
                    'message' => 'Access denied: You do not have permission to view inventory'
                ])->setStatusCode(403);
            }
            return redirect()->to('/dashboard')->with('error', 'Access denied: You do not have permission to view inventory');
        }
        
        return view('marketing/unit_tersedia');
    }

    public function exportKontrak()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.kontrak')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.kontrak');
        }
        // Log EXPORT action
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'kontrak', 0, 'Export Kontrak CSV', [
                'module_name' => 'MARKETING',
                'submenu_item' => 'Kontrak',
                'business_impact' => 'LOW'
            ]);
        }
        return view('marketing/export_kontrak');
    }

    public function exportCustomer()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.customer')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.customer');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'customers', 0, 'Export Customer CSV', [
                'module_name' => 'MARKETING',
                'submenu_item' => 'Customer Management',
                'business_impact' => 'LOW'
            ]);
        }
        return view('marketing/export_customer');
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
                'message' => 'Access denied: You do not have permission to view unit details'
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
                    iu.kontrak_id,
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
                    ia.tipe_item,
                    ia.attachment_id,
                    ia.sn_attachment,
                    ia.baterai_id,
                    ia.sn_baterai,
                    ia.charger_id,
                    ia.sn_charger,
                    ia.kondisi_fisik,
                    ia.kelengkapan,
                    ia.catatan_fisik,
                    ia.lokasi_penyimpanan,
                    COALESCE(att.tipe, "") as attachment_name,
                    COALESCE(att.merk, "") as attachment_merk,
                    COALESCE(bat.jenis_baterai, "") as baterai_name,
                    COALESCE(bat.merk_baterai, "") as baterai_merk,
                    COALESCE(chr.tipe_charger, "") as charger_name,
                    COALESCE(chr.merk_charger, "") as charger_merk
                FROM inventory_attachment ia
                LEFT JOIN attachment att ON ia.attachment_id = att.id_attachment
                LEFT JOIN baterai bat ON ia.baterai_id = bat.id
                LEFT JOIN charger chr ON ia.charger_id = chr.id_charger
                WHERE ia.id_inventory_unit = ?
                ORDER BY ia.tipe_item';
                
            $attachmentResult = $db->query($attachmentSql, [$id]);
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
            return $this->response->setJSON(['success' => false, 'message' => 'Error retrieving unit detail: ' . $e->getMessage()]);
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
            
            // Get total records
            $totalRecords = $this->quotationModel->countAll();
            
            $builder = $this->quotationModel->builder();
            
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
                
                $data[] = [
                    'DT_RowId' => 'row_' . $quotation['id_quotation'],
                    'DT_RowIndex' => $start + $index,
                    'id_quotation' => $quotation['id_quotation'], // Add this for row click functionality
                    'quotation_number' => $quotation['quotation_number'],
                    'prospect_name' => $quotation['prospect_name'],
                    'quotation_title' => $quotation['quotation_title'] ?? '-',
                    'quotation_date' => date('d/m/Y', strtotime($quotation['quotation_date'])),
                    'total_amount' => 'Rp ' . number_format($quotation['total_amount'], 0, ',', '.'),
                    'workflow_stage' => $stageBadge,
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
     * Get action buttons for quotation based on workflow stage
     */
    private function getQuotationActions($quotationId, $workflowStage = 'PROSPECT')
    {
        $actions = '<div class="btn-group" role="group">';
        
        // Check if quotation has specifications (required for SEND action)
        $hasSpecs = $this->quotationSpecificationModel->where('id_quotation', $quotationId)->countAllResults() > 0;
        
        // Check if customer was created (for contract/PO buttons)
        $quotation = $this->quotationModel->find($quotationId);
        $hasCustomer = !empty($quotation['created_customer_id']);
        $hasContract = !empty($quotation['created_contract_id']);
        
        // Check if customer has completed profile and location (for DEAL workflow)
        $customerLocationComplete = false;
        $customerContractComplete = false;
        if ($hasCustomer) {
            $customerModel = new \App\Models\CustomerModel();
            $profileStatus = $customerModel->getCustomerProfileStatus($quotation['created_customer_id']);
            $customerLocationComplete = $profileStatus['complete'] && $profileStatus['has_location'];
            
            // Check if customer has contracts
            $db = \Config\Database::connect();
            $contractCount = $db->table('kontrak k')
                ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
                ->where('cl.customer_id', $quotation['created_customer_id'])
                ->where('k.status', 'Aktif')
                ->countAllResults();
            $customerContractComplete = $contractCount > 0;
        }
        
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
                $actions .= '<button class="btn btn-sm btn-success me-1" onclick="markAsDeal(' . $quotationId . ')" title="Mark as Deal">';
                $actions .= '<i class="fas fa-handshake me-1"></i>Deal';
                $actions .= '</button>';
                $actions .= '<button class="btn btn-sm btn-danger" onclick="markAsNotDeal(' . $quotationId . ')" title="Mark as Not Deal">';
                $actions .= '<i class="fas fa-times-circle me-1"></i>No Deal';
                $actions .= '</button>';
                break;
                
            case 'DEAL':
                // STRICT SEQUENTIAL WORKFLOW - Use database flags for reliable state
                $customerLocationComplete = !empty($quotation['customer_location_complete']);
                $customerContractComplete = !empty($quotation['customer_contract_complete']);
                $spkCreated = !empty($quotation['spk_created']);
                
                // Step 1: Location must be completed first (MANDATORY)
                if (!$customerLocationComplete) {
                    $actions .= '<button class="btn btn-sm btn-warning" onclick="completeCustomerProfile(' . $quotationId . ')" title="Complete customer profile and location first">';
                    $actions .= '<i class="fas fa-user-edit me-1"></i>Complete Customer Profile';
                    $actions .= '</button>';
                } 
                // Step 2: Contract must be completed after location (MANDATORY)
                else if (!$customerContractComplete) {
                    $actions .= '<button class="btn btn-sm btn-info" onclick="completeCustomerContract(' . $quotationId . ')" title="Complete customer contract (required before SPK)">';
                    $actions .= '<i class="fas fa-file-contract me-1"></i>Complete Contract';
                    $actions .= '</button>';
                } 
                // Step 3: Only show SPK if BOTH location AND contract are complete
                else if (!$spkCreated) {
                    $actions .= '<button class="btn btn-sm btn-success" onclick="createSPK(' . $quotationId . ')" title="Create SPK">';
                    $actions .= '<i class="fas fa-clipboard-list me-1"></i>Create SPK';
                    $actions .= '</button>';
                }
                // All steps completed
                else {
                    $actions .= '<span class="badge bg-success">SPK Created</span>';
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
            return redirect()->to('/')->with('error', 'Access denied: You do not have permission to view booking');
        }
        
        return view('marketing/booking');
    }

    /**
     * Create new quotation
     */
    public function createQuotation()
    {
        if (!$this->canManage('marketing')) {
            return redirect()->to('/marketing/quotations')->with('error', 'Access denied');
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
        if (!$this->canManage('marketing')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
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
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
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
                'created_by' => session('user_id'),
                'assigned_to' => $this->request->getPost('assigned_to') ?: session('user_id')
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
                        'sort_order' => $spec['sort_order'] ?? 0,
                        'is_active' => 1
                    ];
                    
                    $this->quotationSpecificationModel->insert($specData);
                }
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
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
                    'message' => 'Failed to create quotation: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'Failed to create quotation: ' . $e->getMessage());
        }
    }
    
    /**
     * Create new prospect (first stage of quotation workflow)
     */
    public function createProspect()
    {
        if (!$this->canManage('marketing')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'prospect_name' => 'required|max_length[255]',
            'prospect_contact_person' => 'required|max_length[255]',
            'quotation_title' => 'required|max_length[255]',
            'valid_until' => 'required|valid_date'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
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
            log_message('error', 'Error creating prospect: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create prospect: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Convert prospect to quotation (allow adding specifications)
     */
    public function convertToQuotation($quotationId)
    {
        if (!$this->canManage('marketing')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }
        
        try {
            $quotation = $this->quotationModel->find($quotationId);
            
            if (!$quotation) {
                return $this->response->setJSON(['success' => false, 'message' => 'Quotation not found']);
            }
            
            if ($quotation['workflow_stage'] !== 'PROSPECT') {
                return $this->response->setJSON(['success' => false, 'message' => 'Only prospects can be converted to quotations']);
            }
            
            // Update workflow stage to quotation
            $this->quotationModel->update($quotationId, [
                'workflow_stage' => 'QUOTATION',
                'stage' => 'DRAFT'
            ]);
            
            // Log activity
            $this->logActivity('PROSPECT_TO_QUOTATION', 'quotation', $quotationId, 
                'Converted prospect to quotation: ' . $quotation['quotation_number']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Prospect converted to quotation successfully. Please add specifications before sending.',
                'redirect_to_specs' => true
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error converting prospect: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to convert prospect: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * View quotation details
     */
    public function viewQuotation($quotationId)
    {
        if (!$this->canAccess('marketing')) {
            return redirect()->to('/marketing/quotations')->with('error', 'Access denied');
        }
        
        $quotation = $this->quotationModel->find($quotationId);
        
        if (!$quotation) {
            return redirect()->to('/marketing/quotations')->with('error', 'Quotation not found');
        }
        
        // Get specifications
        $specifications = $this->quotationSpecificationModel
            ->where('id_quotation', $quotationId)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
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
        if (!$this->canManage('marketing')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }
        
        $quotation = $this->quotationModel->find($quotationId);
        
        if (!$quotation) {
            return $this->response->setJSON(['success' => false, 'message' => 'Quotation not found']);
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
                'status' => 'Aktif',
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
                'message' => 'Failed to convert quotation: ' . $e->getMessage()
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

        $stats = $this->getQuotationStatsData();
        
        // Calculate total value
        $totalValue = $this->quotationModel
            ->selectSum('total_amount')
            ->where('stage !=', 'REJECTED')
            ->get()
            ->getRow()
            ->total_amount ?? 0;

        $stats['total_value'] = $totalValue;

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'success' => true,
                'data' => $stats
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

        $quotation = $this->quotationModel->find($quotationId);

        if (!$quotation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Quotation not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $quotation
        ]);
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
                          qs.quantity,
                          qs.brand as merk_unit,
                          qs.model as model_unit,
                          qs.equipment_type as tipe_jenis,
                          qs.departemen_id,
                          qs.tipe_unit_id,
                          qs.kapasitas_id,
                          qs.attachment_tipe,
                          qs.attachment_merk,
                          qs.jenis_baterai,
                          qs.charger_id,
                          qs.mast_id,
                          qs.ban_id,
                          qs.roda_id,
                          qs.valve_id,
                          d.nama_departemen, 
                          tu.tipe as nama_tipe_unit,
                          tu.jenis as jenis_tipe_unit,
                          k.kapasitas_unit as nama_kapasitas')
                ->join('departemen d', 'd.id_departemen = qs.departemen_id', 'left')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = qs.tipe_unit_id', 'left')
                ->join('kapasitas k', 'k.id_kapasitas = qs.kapasitas_id', 'left')
                ->where('qs.id_quotation', $quotationId)
                ->get()
                ->getResultArray();

            log_message('info', "getSpecifications found " . count($specifications) . " specifications");

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
                'message' => 'Failed to load specifications: ' . $e->getMessage()
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
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export quotations to Excel
     */
    public function exportQuotations()
    {
        if (!$this->canExport('marketing')) {
            return redirect()->to('/marketing/quotations')->with('error', 'Access denied');
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

        $quotations = $builder->orderBy('quotation_date', 'DESC')->findAll();

        // Generate CSV
        $filename = 'quotations_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Quotation Number',
            'Prospect Name',
            'Title',
            'Date',
            'Valid Until',
            'Stage',
            'Total Amount',
            'Currency'
        ]);
        
        // CSV data
        foreach ($quotations as $quotation) {
            fputcsv($output, [
                $quotation['quotation_number'],
                $quotation['prospect_name'],
                $quotation['quotation_title'],
                $quotation['quotation_date'],
                $quotation['valid_until'],
                $quotation['stage'],
                $quotation['total_amount'],
                $quotation['currency']
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Convert quotation to deal
     */
    public function convertToDeal($quotationId)
    {
        if (!$this->canManage('marketing')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $quotation = $this->quotationModel->find($quotationId);

        if (!$quotation) {
            return $this->response->setJSON(['success' => false, 'message' => 'Quotation not found']);
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
        if (!$this->canManage('marketing')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'new_stage' => 'required|in_list[DRAFT,SENT,FOLLOW_UP,NEGOTIATION,ACCEPTED,REJECTED,EXPIRED]',
            'probability_percent' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[100]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
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

        $this->quotationModel->update($quotationId, $updateData);

        // Log stage history if table exists
        $db = \Config\Database::connect();
        if ($db->tableExists('quotation_stage_history')) {
            $db->table('quotation_stage_history')->insert([
                'id_quotation' => $quotationId,
                'stage' => $updateData['stage'],
                'change_reason' => $this->request->getPost('change_reason'),
                'change_notes' => $this->request->getPost('change_notes'),
                'changed_by' => session('user_id'),
                'changed_at' => date('Y-m-d H:i:s')
            ]);
        }

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
        $active_contracts = $this->kontrakModel->where('status', 'Aktif')->countAllResults(false);
        
        // Calculate monthly revenue (from active contracts)
        $monthly_revenue = $this->kontrakModel
            ->selectSum('nilai_total')
            ->where('status', 'Aktif')
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
            ->where('status', 'Aktif')
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
                ->where('status', 'Aktif')
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
        
        return view('marketing/spk', [
            'title' => 'Surat Perintah Kerja (SPK)',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/marketing/spk' => 'Surat Perintah Kerja (SPK)'
            ],
            'can_view_marketing' => can_view('marketing'),
            'can_create_marketing' => $this->canManage('marketing'),
            'can_export_marketing' => $this->canExport('marketing'),
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
     * Get unit components from inventory_attachment (single source of truth)
     */
    private function getUnitComponents($unitId)
    {
        $components = [
            'battery' => null,
            'charger' => null,
            'attachment' => null
        ];

        // Get battery info - include both available (7) and in use (8) for the unit
        $battery = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment, ia.baterai_id, ia.sn_baterai, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->join('baterai b', 'ia.baterai_id = b.id', 'left')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'battery')
            ->whereIn('ia.status_unit', [7, 8]) // Available or In use for this unit
            ->get()->getRowArray();

        if ($battery) {
            $components['battery'] = $battery;
        }

        // Get charger info - include both available (7) and in use (8) for the unit
        $charger = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment, ia.charger_id, ia.sn_charger, c.merk_charger, c.tipe_charger')
            ->join('charger c', 'ia.charger_id = c.id_charger', 'left')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'charger')
            ->whereIn('ia.status_unit', [7, 8]) // Available or In use for this unit
            ->get()->getRowArray();

        if ($charger) {
            $components['charger'] = $charger;
        }

        // Get attachment info
        $attachment = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment, ia.attachment_id, ia.sn_attachment, a.tipe, a.merk, a.model')
            ->join('attachment a', 'ia.attachment_id = a.id_attachment', 'left')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'attachment')
            ->where('ia.status_unit', 8) // In use
            ->get()->getRowArray();

        if ($attachment) {
            $components['attachment'] = $attachment;
        }

        return $components;
    }

    /** Render HTML print view for browser printing (no PDF lib required) */
    public function spkPrint($id)
    {
        $id = (int)$id;
        $row = $this->db->table('spk')->where('id', $id)->get()->getRowArray();
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
        
        // Load kontrak_spesifikasi data (for Equipment section - data permintaan marketing)
        $kontrak_spec = null;
        if (!empty($row['kontrak_spesifikasi_id'])) {
            $kontrak_spec = $this->db->table('kontrak_spesifikasi ks')
                ->select('ks.*')
                ->select('tu.jenis as kontrak_jenis_unit, tu.tipe as kontrak_tipe_unit')
                ->select('k.kapasitas_unit as kontrak_kapasitas_name')
                ->select('d.nama_departemen as kontrak_departemen_name')
                ->select('tm.tipe_mast as kontrak_mast_name')
                ->select('jr.tipe_roda as kontrak_roda_name')
                ->select('tb.tipe_ban as kontrak_ban_name')
                ->select('v.jumlah_valve as kontrak_valve_name')
                ->select('chr.merk_charger as kontrak_merk_charger, chr.tipe_charger as kontrak_tipe_charger')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = ks.tipe_unit_id', 'left')
                ->join('kapasitas k', 'k.id_kapasitas = ks.kapasitas_id', 'left')
                ->join('departemen d', 'd.id_departemen = ks.departemen_id', 'left')
                ->join('tipe_mast tm', 'tm.id_mast = ks.mast_id', 'left')
                ->join('jenis_roda jr', 'jr.id_roda = ks.roda_id', 'left')
                ->join('tipe_ban tb', 'tb.id_ban = ks.ban_id', 'left')
                ->join('valve v', 'v.id_valve = ks.valve_id', 'left')
                ->join('charger chr', 'chr.id_charger = ks.charger_id', 'left')
                ->where('ks.id', $row['kontrak_spesifikasi_id'])
                ->get()
                ->getRowArray();

            // Decode aksesoris JSON if stored as string
            if ($kontrak_spec && isset($kontrak_spec['aksesoris']) && is_string($kontrak_spec['aksesoris'])) {
                try {
                    $decoded_aks = json_decode($kontrak_spec['aksesoris'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $kontrak_spec['aksesoris'] = $decoded_aks;
                    }
                } catch (\Exception $e) {
                    // keep original string on failure
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
                    $aInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan, att.tipe, att.merk, att.model')
                        ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                        ->where('ia.id_inventory_attachment', $pu['attachment_inventory_id'])
                        ->where('ia.tipe_item', 'attachment')
                        ->get()->getRowArray();
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
                    $bInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_baterai, ia.lokasi_penyimpanan, bat.merk_baterai, bat.tipe_baterai, bat.jenis_baterai')
                        ->join('baterai bat','bat.id = ia.baterai_id','left')
                        ->where('ia.id_inventory_attachment', $pu['battery_inventory_id'])
                        ->where('ia.tipe_item', 'battery')
                        ->get()->getRowArray();
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
                    $cInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_charger, ia.lokasi_penyimpanan, chr.merk_charger, chr.tipe_charger')
                        ->join('charger chr','chr.id_charger = ia.charger_id','left')
                        ->where('ia.id_inventory_attachment', $pu['charger_inventory_id'])
                        ->where('ia.tipe_item', 'charger')
                        ->get()->getRowArray();
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
            $a = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan')
                ->select('att.tipe, att.merk, att.model')
                ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                ->where('ia.id_inventory_attachment', $row['fabrikasi_attachment_id'])
                ->get()->getRowArray();
                
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
            $b = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_baterai, ia.lokasi_penyimpanan')
                ->select('bat.merk_baterai, bat.tipe_baterai, bat.jenis_baterai')
                ->join('baterai bat','bat.id = ia.baterai_id','left')
                ->where('ia.id_inventory_attachment', $spec['persiapan_battery_id'])
                ->where('ia.tipe_item', 'battery')
                ->get()->getRowArray();
                
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
            $c = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_charger, ia.lokasi_penyimpanan')
                ->select('chr.merk_charger, chr.tipe_charger')
                ->join('charger chr','chr.id_charger = ia.charger_id','left')
                ->where('ia.id_inventory_attachment', $spec['persiapan_charger_id'])
                ->where('ia.tipe_item', 'charger')
                ->get()->getRowArray();
                
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
            $a = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan')
                ->select('att.tipe, att.merk, att.model')
                ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                ->where('ia.id_inventory_attachment', $spec['fabrikasi_attachment_id'])
                ->where('ia.tipe_item', 'attachment')
                ->get()->getRowArray();
                
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
        
        // Add stage_status data for print view
        $stageStatus = $this->getSpkStageStatusData($id);
        $row['stage_status'] = $stageStatus;
        
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
                        // Also check delivery_instruction_items as fallback
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
                            // Fallback check to delivery_instruction_items
                            $activeDI = $this->db->query("
                                SELECT di.nomor_di, di.status_di, di.pelanggan
                                FROM delivery_instruction_items dii
                                INNER JOIN delivery_instructions di ON di.id = dii.delivery_instruction_id
                                WHERE dii.unit_id = ?
                                AND di.status_di NOT IN ('SAMPAI_LOKASI', 'SELESAI', 'DIBATALKAN')
                                AND dii.item_type = 'UNIT'
                                LIMIT 1
                            ", [$unitId])->getRowArray();
                        }
                        
                        if ($activeDI) {
                            $isInActiveDI = true;
                            $activeDIInfo = $activeDI;
                        }
                    }
                    
                    // Get attachment details from inventory_attachment
                    $attachmentDetails = null;
                    if ($unitId) {
                        $attachmentDetails = $this->db->table('inventory_attachment ia')
                            ->select('ia.sn_attachment, ia.sn_baterai, ia.sn_charger, a.tipe as attachment_type')
                            ->join('attachment a', 'a.id_attachment = ia.attachment_id', 'left')
                            ->where('ia.id_inventory_unit', $unitId)
                            ->get()
                            ->getRowArray();
                    }
                    
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
                            $batteryDetails = $this->db->table('inventory_attachment ia')
                                ->select('ia.sn_baterai, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
                                ->join('baterai b', 'b.id = ia.baterai_id', 'left')
                                ->where('ia.id_inventory_attachment', $batteryId)
                                ->get()
                                ->getRowArray();
                        }
                        
                        if ($chargerId) {
                            $chargerDetails = $this->db->table('inventory_attachment ia')
                                ->select('ia.sn_charger, c.merk_charger, c.tipe_charger')
                                ->join('charger c', 'c.id_charger = ia.charger_id', 'left')
                                ->where('ia.id_inventory_attachment', $chargerId)
                                ->get()
                                ->getRowArray();
                        }
                    }
                    
                    // Get attachment from fabrikasi stage
                    if (isset($unitStages['fabrikasi'])) {
                        $fabrikasiData = $unitStages['fabrikasi'];
                        $attachmentId = $fabrikasiData['attachment_inventory_attachment_id'] ?? null;
                        
                        // Debug logging
                        error_log("DEBUG: Fabrikasi data for unit $unitIndex: " . json_encode($fabrikasiData));
                        error_log("DEBUG: Attachment ID: " . ($attachmentId ?? 'NULL'));
                        
                        if ($attachmentId) {
                            $attachmentDetails = $this->db->table('inventory_attachment ia')
                                ->select('ia.sn_attachment, a.merk, a.model, a.tipe')
                                ->join('attachment a', 'a.id_attachment = ia.attachment_id', 'left')
                                ->where('ia.id_inventory_attachment', $attachmentId)
                                ->get()
                                ->getRowArray();
                            
                            error_log("DEBUG: Attachment details: " . json_encode($attachmentDetails));
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
                        $jenisUnitFormatted = 'REACH TRUCK';
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
                        $attachmentFormatted = 'ATT-' . $unitId;
                    }
                    
                    $preparedList[] = [
                        'unit_index' => $unitIndex,
                        'unit_id' => $unitId,
                        'no_unit' => $noUnitFormatted,
                        'serial_number' => $unitDetails['serial_number'] ?? 'SN-' . $unitId,
                        'jenis_unit' => $jenisUnitFormatted,
                        'departemen_name' => $unitDetails['departemen_name'] ?? 'ELECTRIC',
                        'kapasitas_name' => $unitDetails['kapasitas_name'] ?? '15 Ton',
                        'mast_name' => $unitDetails['mast_name'] ?? 'Triplex (3-stage FFL) - ZSM450',
                        'roda_name' => $unitDetails['roda_name'] ?? '3-Wheel',
                        'ban_name' => $unitDetails['ban_name'] ?? 'Cushion (Ban Bantal)',
                        'valve_name' => $unitDetails['valve_name'] ?? '3 Valve',
                        'baterai_sn' => $bateraiFormatted,
                        'charger_sn' => $chargerFormatted,
                        'attachment_sn' => $attachmentFormatted,
                        'aksesoris' => $this->formatAksesoris($unitData['aksesoris_tersedia'] ?? 'LAMPU UTAMA, ROTARY LAMP, SENSOR PARKING, HORN SPEAKER, APAR 1 KG, BEACON'),
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
    $data = $this->spkModel->orderBy('id','DESC')->findAll();
        return $this->response->setJSON(['data'=>$data,'csrf_hash'=>csrf_hash()]);
    }

    public function spkDetail($id)
    {
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
            // Attachment label from inventory_attachment
            if (!empty($sel['inventory_attachment_id'])) {
                $a = $this->attModel
                    ->select('a.tipe, a.merk, a.model, inventory_attachment.sn_attachment, inventory_attachment.lokasi_penyimpanan')
                    ->join('attachment a','a.id_attachment = inventory_attachment.attachment_id','left')
                    ->where('inventory_attachment.id_inventory_attachment', (int)$sel['inventory_attachment_id'])
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
                    $aInfo = $this->db->table('inventory_attachment ia')
                        ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan, att.tipe, att.merk, att.model')
                        ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                        ->where('ia.id_inventory_attachment', $pu['attachment_id'])
                        ->get()->getRowArray();
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
        
        // If we have prepared units from stages, use those instead
        if (!empty($preparedUnitsFromStages)) {
            $enriched['prepared_units_detail'] = $preparedUnitsFromStages;
        }
        
        // Get kontrak_spesifikasi data if available
        $kontrak_spec = null;
        if (!empty($row['kontrak_spesifikasi_id'])) {
            $kontrak_spec = $this->db->table('kontrak_spesifikasi')
                ->where('id', $row['kontrak_spesifikasi_id'])
                ->get()
                ->getRowArray();
                
            // Process aksesoris if it's stored as JSON
            if ($kontrak_spec && isset($kontrak_spec['aksesoris']) && is_string($kontrak_spec['aksesoris'])) {
                try {
                    $decoded_aksesoris = json_decode($kontrak_spec['aksesoris'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $kontrak_spec['aksesoris'] = $decoded_aksesoris;
                    }
                } catch (\Exception $e) {
                    // Keep as string if parsing fails
                }
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $row,
            'jenis_spk' => $row['jenis_spk'] ?? 'UNIT', // Explicitly include SPK type for frontend
            'spesifikasi' => $enriched,
            'prepared_units' => $preparedUnits,
            'prepared_units_detail' => $enriched['prepared_units_detail'] ?? [],
            'kontrak_spec' => $kontrak_spec,
            'csrf_hash' => csrf_hash()
        ]);
    }

    // Get units registered in a contract for ATTACHMENT SPK
    public function kontrakUnits($kontrakId)
    {
        try {
            // Get all units that have been delivered under this contract
            $units = $this->db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit, iu.sn_unit, iu.tipe_unit_id, tu.tipe_jenis, mu.merk_unit, mu.model_unit, iu.status_unit_id, su.status_unit')
                ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('status_unit su', 'su.id_status_unit = iu.status_unit_id', 'left')
                ->where('iu.kontrak_id', $kontrakId)
                ->whereIn('iu.status_unit_id', [6, 7, 8]) // DELIVERED, IN_USE, or MAINTENANCE
                ->orderBy('iu.sn_unit', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'units' => $units,
                'count' => count($units),
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
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
                'message' => $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    // Provide kontrak options (Pending) for searchable dropdown
    public function kontrakOptions()
    {
        $q = trim($this->request->getGet('q') ?? '');
        $status = trim($this->request->getGet('status') ?? 'Pending');
        
        // Use database query builder with proper JOINs
        $builder = $this->db->table('kontrak k');
        $builder->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left');
        $builder->join('customers c', 'cl.customer_id = c.id', 'left');
        $builder->join('kontrak_spesifikasi ks', 'ks.kontrak_id = k.id', 'inner');
        $builder->select('k.id, k.no_kontrak, k.no_po_marketing, c.customer_name as pelanggan, cl.location_name as lokasi');
        $builder->whereIn('k.status', ['Aktif', 'Pending']);
        $builder->groupBy('k.id, k.no_kontrak, k.no_po_marketing, c.customer_name, cl.location_name');
        
        if ($q !== '') {
            $builder->groupStart()
                ->like('k.no_kontrak', $q)
                ->orLike('k.no_po_marketing', $q)
                ->orLike('c.customer_name', $q)
            ->groupEnd();
        }
        $rows = $builder->orderBy('k.dibuat_pada', 'DESC')->limit(20)->get()->getResultArray();
        
        // map to simple text for display if needed
        $options = array_map(function($r){
            $label = trim(($r['no_kontrak'] ?: '') . ' ' . ($r['no_po_marketing'] ? '(' . $r['no_po_marketing'] . ')' : '') . ' - ' . ($r['pelanggan'] ?: '-'));
            return [
                'id' => (int)$r['id'],
                'no_kontrak' => $r['no_kontrak'],
                'no_po_marketing' => $r['no_po_marketing'],
                'pelanggan' => $r['pelanggan'],
                'lokasi' => $r['lokasi'],
                'label' => $label
            ];
        }, $rows);
        return $this->response->setJSON(['data'=>$options,'csrf_hash'=>csrf_hash()]);
    }

    /**
     * Get active contracts for SPK creation
     */
    public function getActiveContracts()
    {
        try {
            // Get contracts with specifications - using safe column names
            $builder = $this->db->table('kontrak k');
            $builder->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left');
            $builder->join('customers c', 'cl.customer_id = c.id', 'left');
            $builder->join('kontrak_spesifikasi ks', 'ks.kontrak_id = k.id', 'inner');
            
            // Use safe column selection - only select columns that exist
            $builder->select('k.id, k.no_kontrak, k.no_po_marketing, c.customer_name as pelanggan, cl.location_name as lokasi');
            
            $builder->whereIn('k.status', ['Aktif', 'Pending']);
            $builder->groupBy('k.id, k.no_kontrak, k.no_po_marketing, c.customer_name, cl.location_name');
            $rows = $builder->orderBy('k.dibuat_pada', 'DESC')->get()->getResultArray();
            
            $contracts = array_map(function($r){
                return [
                    'id' => (int)$r['id'],
                    'no_kontrak' => $r['no_kontrak'],
                    'no_po_marketing' => $r['no_po_marketing'],
                    'pelanggan' => $r['pelanggan'],
                    'lokasi' => $r['lokasi'],
                    'pic' => '', // Will be filled separately if needed
                    'kontak' => '' // Will be filled separately if needed
                ];
            }, $rows);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $contracts
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading contracts: ' . $e->getMessage()
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
            $builder->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left');
            $builder->join('customers c', 'cl.customer_id = c.id', 'left');
            
            // Use safe column selection - only select columns that exist
            $builder->select('k.id, k.no_kontrak, k.no_po_marketing, c.customer_name as pelanggan, cl.location_name as lokasi');
            $builder->where('k.id', $id);
            $row = $builder->get()->getRowArray();
            
            if (!$row) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contract not found'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => (int)$row['id'],
                    'no_kontrak' => $row['no_kontrak'],
                    'no_po_marketing' => $row['no_po_marketing'],
                    'pelanggan' => $row['pelanggan'],
                    'lokasi' => $row['lokasi'],
                    'pic' => '', // Will be filled separately if needed
                    'kontak' => '' // Will be filled separately if needed
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading contract: ' . $e->getMessage()
            ]);
        }
    }

    // Monitoring: Kontrak → SPK status (simple aggregation)
    public function spkMonitoring()
    {
        $sql = "SELECT k.id, k.no_kontrak, k.no_po_marketing, 
                       c.customer_name as pelanggan, 
                       cl.location_name as lokasi,
                   COUNT(s.id) AS total_spk,
                   SUM(CASE WHEN s.status = 'SUBMITTED' THEN 1 ELSE 0 END) AS submitted,
                   SUM(CASE WHEN s.status = 'IN_PROGRESS' THEN 1 ELSE 0 END) AS in_progress,
                   SUM(CASE WHEN s.status = 'READY' THEN 1 ELSE 0 END) AS ready,
                   SUM(CASE WHEN s.status = 'COMPLETED' THEN 1 ELSE 0 END) AS completed,
                   SUM(CASE WHEN s.status = 'DELIVERED' THEN 1 ELSE 0 END) AS delivered,
                   SUM(CASE WHEN s.status = 'CANCELLED' THEN 1 ELSE 0 END) AS cancelled,
                   MAX(s.diperbarui_pada) AS last_update
            FROM kontrak k
            LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
            LEFT JOIN customers c ON cl.customer_id = c.id
            LEFT JOIN spk s ON (s.po_kontrak_nomor = k.no_kontrak OR s.po_kontrak_nomor = k.no_po_marketing)
            GROUP BY k.id, k.no_kontrak, k.no_po_marketing, c.customer_name, cl.location_name
            ORDER BY k.id DESC
            LIMIT 100";
        $rows = $this->db->query($sql)->getResultArray();
        return $this->response->setJSON(['data'=>$rows, 'csrf_hash'=>csrf_hash()]);
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
                
            // Format item labels
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
        
        return $this->response->setJSON(['data'=>$rows,'csrf_hash'=>csrf_hash()]);
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

    // Options: SPK that are READY for DI creation
    public function spkReadyOptions()
    {
        $q = trim($this->request->getGet('q') ?? '');
    $b = $this->spkModel->select('id, nomor_spk, po_kontrak_nomor, pelanggan, lokasi')->where('status','READY');
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
                // New workflow: Create SPK based on contract specification
                log_message('info', 'Marketing::spkCreate - Using specification workflow for kontrak_spesifikasi_id: ' . $kontrakSpesifikasiId);
                $spesifikasi = $this->kontrakSpesifikasiModel->find($kontrakSpesifikasiId);
                log_message('info', 'Marketing::spkCreate - Found spesifikasi: ' . json_encode($spesifikasi));
                if (!$spesifikasi) {
                    throw new \Exception('Spesifikasi kontrak tidak ditemukan.');
                }

                // Check if enough units are needed
                $available = $spesifikasi['jumlah_dibutuhkan'] - $spesifikasi['jumlah_tersedia'];
                if ($jumlahUnit > $available) {
                    throw new \Exception("Jumlah unit melebihi yang dibutuhkan. Maksimal: {$available} unit");
                }

                // Get contract info
                $kontrak = $this->kontrakModel->find($spesifikasi['kontrak_id']);
                if (!$kontrak) {
                    throw new \Exception('Kontrak tidak ditemukan.');
                }

                // Get jenis_spk from form input, default to 'UNIT' if not provided
                $jenis = strtoupper(trim((string)$this->request->getPost('jenis_spk') ?: 'UNIT'));
                $allowedJenis = ['UNIT','ATTACHMENT'];
                if (!in_array($jenis, $allowedJenis, true)) { $jenis = 'UNIT'; }

                // Build specification array from kontrak_spesifikasi
                $spec = [
                    'departemen_id' => $spesifikasi['departemen_id'],
                    'tipe_unit_id' => $spesifikasi['tipe_unit_id'],
                    'tipe_jenis' => $spesifikasi['tipe_jenis'],
                    'merk_unit' => $spesifikasi['merk_unit'],
                    'model_unit' => $spesifikasi['model_unit'],
                    'kapasitas_id' => $spesifikasi['kapasitas_id'],
                    'attachment_tipe' => $spesifikasi['attachment_tipe'],
                    'attachment_merk' => $spesifikasi['attachment_merk'],
                    'jenis_baterai' => $spesifikasi['jenis_baterai'],
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
                        throw new \Exception('Error: ' . $e->getMessage());
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
                    'kontrak_id' => $kontrak['id'],
                    'kontrak_spesifikasi_id' => $kontrakSpesifikasiId,
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

            } elseif ($kontrakId && $kontrakId > 0) {
                // Fallback: Create SPK based on contract ID only (when no specification is selected or kontrak_spesifikasi_id is 0)
                log_message('info', 'Marketing::spkCreate - Using kontrak_id fallback workflow for kontrak: ' . $kontrakId);
                $kontrak = $this->kontrakModel->find($kontrakId);
                log_message('info', 'Marketing::spkCreate - Found kontrak: ' . json_encode($kontrak));
                if (!$kontrak) {
                    throw new \Exception('Kontrak tidak ditemukan.');
                }

                // Get first available specification for this contract, or create basic spec
                $spesifikasiList = $this->kontrakSpesifikasiModel->getByKontrakId($kontrakId);
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
                    $firstSpecId = $firstSpec['id']; // Store the ID for kontrak_spesifikasi_id
                    $spec = [
                        'departemen_id' => $firstSpec['departemen_id'] ?? null,
                        'tipe_unit_id' => $firstSpec['tipe_unit_id'] ?? null,
                        'tipe_jenis' => $firstSpec['tipe_jenis'] ?? null,
                        'merk_unit' => $firstSpec['merk_unit'] ?? null,
                        'model_unit' => $firstSpec['model_unit'] ?? null,
                        'kapasitas_id' => $firstSpec['kapasitas_id'] ?? null,
                        'attachment_tipe' => $firstSpec['attachment_tipe'] ?? null,
                        'attachment_merk' => $firstSpec['attachment_merk'] ?? null,
                        'jenis_baterai' => $firstSpec['jenis_baterai'] ?? null,
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
                    'kontrak_spesifikasi_id' => $firstSpecId, // Use the first spec ID if available
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
                    'departemen' => $payload['departemen'] ?? 'N/A',
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
                'message' => $e->getMessage(),
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
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        $this->db->transBegin();

        try {
            $quotationId = $this->request->getPost('quotation_id');
            $customerId = $this->request->getPost('customer_id');
            $contractId = $this->request->getPost('contract_id');
            $deliveryDate = $this->request->getPost('delivery_date');
            $specifications = $this->request->getPost('specifications'); // Array of {specification_id, quantity}

            // Validation
            if (!$quotationId || !$customerId || !$contractId) {
                throw new \Exception('Missing required data: quotation, customer, or contract');
            }

            if (!$deliveryDate) {
                throw new \Exception('Delivery date is required');
            }

            if (empty($specifications) || !is_array($specifications)) {
                throw new \Exception('Please select at least one specification');
            }

            // Get quotation
            $quotation = $this->quotationModel->find($quotationId);
            if (!$quotation) {
                throw new \Exception('Quotation not found');
            }

            // Get contract
            $contract = $this->kontrakModel->find($contractId);
            if (!$contract) {
                throw new \Exception('Contract not found');
            }

            $createdSPKs = [];
            $spkNumbers = [];

            // Create SPK for each selected specification
            foreach ($specifications as $specData) {
                $specId = $specData['specification_id'];
                $quantity = (int)$specData['quantity'];

                if ($quantity <= 0) {
                    continue; // Skip invalid quantities
                }

                // Get specification details
                $spec = $this->db->table('quotation_specifications qs')
                    ->select('qs.*, d.nama_departemen, tu.tipe as nama_tipe_unit, tu.jenis as jenis_tipe_unit, k.kapasitas_unit as nama_kapasitas')
                    ->join('departemen d', 'd.id_departemen = qs.departemen_id', 'left')
                    ->join('tipe_unit tu', 'tu.id_tipe_unit = qs.tipe_unit_id', 'left')
                    ->join('kapasitas k', 'k.id_kapasitas = qs.kapasitas_id', 'left')
                    ->where('qs.id_specification', $specId)
                    ->get()
                    ->getRowArray();

                if (!$spec) {
                    log_message('error', "Specification not found: ID {$specId}");
                    continue;
                }

                // Build specification JSON
                $spesifikasiData = [
                    'departemen_id' => $spec['departemen_id'] ?? null,
                    'tipe_unit_id' => $spec['tipe_unit_id'] ?? null,
                    'tipe_jenis' => $spec['equipment_type'] ?? null,
                    'merk_unit' => $spec['brand'] ?? null,
                    'model_unit' => $spec['model'] ?? null,
                    'kapasitas_id' => $spec['kapasitas_id'] ?? null,
                    'attachment_tipe' => $spec['attachment_tipe'] ?? null,
                    'attachment_merk' => $spec['attachment_merk'] ?? null,
                    'jenis_baterai' => $spec['jenis_baterai'] ?? null,
                    'charger_id' => $spec['charger_id'] ?? null,
                    'mast_id' => $spec['mast_id'] ?? null,
                    'ban_id' => $spec['ban_id'] ?? null,
                    'roda_id' => $spec['roda_id'] ?? null,
                    'valve_id' => $spec['valve_id'] ?? null,
                    'aksesoris' => []
                ];

                // Generate SPK number
                $nomorSPK = method_exists($this->spkModel, 'generateNextNumber') 
                    ? $this->spkModel->generateNextNumber() 
                    : $this->generateSpkNumber();

                // Prepare SPK payload
                $spkPayload = [
                    'nomor_spk' => $nomorSPK,
                    'jenis_spk' => 'UNIT',
                    'kontrak_id' => $contractId,
                    'kontrak_spesifikasi_id' => null, // Link to quotation spec instead
                    'quotation_specification_id' => $specId,
                    'jumlah_unit' => $quantity,
                    'po_kontrak_nomor' => $contract['no_kontrak'] ?? null,
                    'pelanggan' => $contract['pelanggan'] ?? $quotation['customer_name'] ?? '',
                    'pic' => $contract['pic'] ?? null,
                    'kontak' => $contract['kontak'] ?? null,
                    'lokasi' => $contract['lokasi'] ?? null,
                    'delivery_plan' => $deliveryDate,
                    'spesifikasi' => json_encode($spesifikasiData),
                    'catatan' => "Created from Quotation {$quotation['quotation_number']}",
                    'status' => 'SUBMITTED',
                    'dibuat_oleh' => session('user_id') ?? 1,
                    'dibuat_pada' => date('Y-m-d H:i:s')
                ];

                // Insert SPK
                $insertResult = $this->spkModel->insert($spkPayload);
                
                if ($insertResult) {
                    $spkId = $this->spkModel->getInsertID();
                    
                    if ($spkId) {
                        $createdSPKs[] = $spkId;
                        $spkNumbers[] = $nomorSPK;
                        
                        // Log SPK creation
                        $this->logCreate('spk', $spkId, [
                            'spk_id' => $spkId,
                            'nomor_spk' => $nomorSPK,
                            'quotation_id' => $quotationId,
                            'specification_id' => $specId,
                            'jumlah_unit' => $quantity
                        ]);
                        
                        log_message('info', "SPK created from quotation: {$nomorSPK} (ID: {$spkId})");
                    }
                }
            }

            if (empty($createdSPKs)) {
                throw new \Exception('Failed to create any SPK. Please check your selections.');
            }

            // Update quotation status if needed
            // You might want to mark quotation as having SPKs created
            
            $this->db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'SPK created successfully',
                'spk_count' => count($createdSPKs),
                'spk_numbers' => $spkNumbers,
                'spk_ids' => $createdSPKs,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'createSPKFromQuotation failed: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    private function sendSpkNotification($nomorSpk, $spkData = [])
    {
        try {
            // Load notification helper
            helper('notification');
            
            // Prepare event data for notification
            $eventData = [
                'nomor_spk' => $nomorSpk,
                'pelanggan' => $spkData['pelanggan'] ?? 'N/A',
                'departemen' => $spkData['departemen'] ?? 'N/A',
                'lokasi' => $spkData['lokasi'] ?? 'N/A',
                'id' => $spkData['id'] ?? null
            ];
            
            // Debug logging
            log_message('info', "SPK Notification Debug - nomorSpk: {$nomorSpk}");
            log_message('info', "SPK Notification Debug - spkData: " . json_encode($spkData));
            log_message('info', "SPK Notification Debug - eventData: " . json_encode($eventData));
            
            // Send notification using helper function
            $result = notify_spk_created($eventData);
            
            // Log the result
            if ($result && isset($result['notifications_sent'])) {
                log_message('info', "SPK Notification sent: {$result['notifications_sent']} notifications for SPK {$nomorSpk}");
            } else {
                log_message('warning', "SPK Notification failed or returned no result for SPK {$nomorSpk}");
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
            $rows = $this->db->table('model_unit')
                ->select('DISTINCT TRIM(merk_unit) as name', false)
        ->where('merk_unit IS NOT NULL', null, false)
                ->where("TRIM(merk_unit) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
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
    if ($type === 'jenis_baterai') {
            $rows = $this->db->table('baterai')
                ->select('DISTINCT TRIM(jenis_baterai) as name', false)
        ->where('jenis_baterai IS NOT NULL', null, false)
                ->where("TRIM(jenis_baterai) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'attachment_tipe') {
            $rows = $this->db->table('attachment')
                ->select('DISTINCT TRIM(tipe) as name', false)
        ->where('tipe IS NOT NULL', null, false)
                ->where("TRIM(tipe) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
            return $this->response->setJSON(['success'=>true,'data'=>$rows,'csrf_hash'=>csrf_hash()]);
        }
        if ($type === 'attachment_merk') {
            $rows = $this->db->table('attachment')
                ->select('DISTINCT TRIM(merk) as name', false)
                ->where('merk IS NOT NULL', null, false)
                ->where("TRIM(merk) <> ''", null, false)
                ->orderBy('name','ASC')
                ->limit(200)
                ->get()->getResultArray();
            $rows = array_map(fn($r)=>['id'=>$r['name'],'name'=>$r['name']], $rows);
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
     * Create SPK from Quotation Specifications
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

            if (!$quotation['created_contract_id']) {
                throw new \Exception('Quotation belum memiliki kontrak yang dibuat');
            }

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
                    'kontrak_id' => $quotation['created_contract_id'],
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
                'message' => $e->getMessage()
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
        if (empty($data['jenis_spk']) || empty($data['po_kontrak_nomor']) || empty($data['pelanggan'])) {
            log_message('error', 'SPK Update - Validation failed. Data: ' . json_encode($data));
            return $this->response->setJSON(['success'=>false,'message'=>'Jenis SPK, PO Kontrak, dan Pelanggan wajib diisi. Data received: ' . json_encode($data)]);
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

    // units selected for this DI (allow multiple)
    $unitIds = $this->request->getPost('unit_ids');
    if (is_string($unitIds)) { $unitIds = [$unitIds]; }
    if (!is_array($unitIds)) { $unitIds = []; }
    $unitIds = array_values(array_unique(array_filter(array_map('intval', $unitIds))));
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

        $payload = [
            'nomor_di' => method_exists($this->diModel,'generateNextNumber') ? $this->diModel->generateNextNumber() : $this->generateDiNumber(),
            'spk_id' => $spkId ?: null,
            'jenis_spk' => isset($spk['jenis_spk']) ? $spk['jenis_spk'] : 'UNIT', // Copy jenis_spk from SPK
            'po_kontrak_nomor' => $poNo,
            'pelanggan' => $pelanggan,
            'lokasi' => $lokasi,
            'status_di' => 'DIAJUKAN',  // Use status_di field to match database column
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
                'debug' => [
                    'db_error' => $dbError,
                    'last_query' => $lastQuery,
                    'payload' => $payload,
                ],
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
                            // Get actual battery_id from inventory_attachment
                            $invBattery = $this->db->table('inventory_attachment')
                                ->select('baterai_id')
                                ->where('id_inventory_attachment', $batteryId)
                                ->get()->getRowArray();
                            
                            if ($invBattery && $invBattery['baterai_id']) {
                                $itemResult = $this->diItemModel->insert([
                                    'di_id' => $diId,
                                    'item_type' => 'ATTACHMENT',
                                    'attachment_id' => $invBattery['baterai_id'],
                                    'parent_unit_id' => $unitId,
                                    'keterangan' => 'Battery (Approved in SPK Persiapan Unit)'
                                ]);
                                error_log("DI Create - Added approved battery (ID: {$invBattery['baterai_id']}) for unit $unitId");
                            }
                        }
                        
                        // Insert charger if approved
                        if (!empty($persiapanStage['charger_inventory_attachment_id'])) {
                            $chargerId = $persiapanStage['charger_inventory_attachment_id'];
                            // Get actual charger_id from inventory_attachment
                            $invCharger = $this->db->table('inventory_attachment')
                                ->select('charger_id')
                                ->where('id_inventory_attachment', $chargerId)
                                ->get()->getRowArray();
                            
                            if ($invCharger && $invCharger['charger_id']) {
                                $itemResult = $this->diItemModel->insert([
                                    'di_id' => $diId,
                                    'item_type' => 'ATTACHMENT',
                                    'attachment_id' => $invCharger['charger_id'],
                                    'parent_unit_id' => $unitId,
                                    'keterangan' => 'Charger (Approved in SPK Persiapan Unit)'
                                ]);
                                error_log("DI Create - Added approved charger (ID: {$invCharger['charger_id']}) for unit $unitId");
                            }
                        }
                        
                        // Insert attachment if approved
                        if (!empty($fabrikasiStage['attachment_inventory_attachment_id'])) {
                            $attachmentId = $fabrikasiStage['attachment_inventory_attachment_id'];
                            // Get actual attachment_id from inventory_attachment
                            $invAttachment = $this->db->table('inventory_attachment')
                                ->select('attachment_id')
                                ->where('id_inventory_attachment', $attachmentId)
                                ->get()->getRowArray();
                            
                            if ($invAttachment && $invAttachment['attachment_id']) {
                                $itemResult = $this->diItemModel->insert([
                                    'di_id' => $diId,
                                    'item_type' => 'ATTACHMENT',
                                    'attachment_id' => $invAttachment['attachment_id'],
                                    'parent_unit_id' => $unitId,
                                    'keterangan' => 'Attachment (Approved in SPK Fabrikasi)'
                                ]);
                                error_log("DI Create - Added approved attachment (ID: {$invAttachment['attachment_id']}) for unit $unitId");
                            }
                        }
                    }
                }
            } else {
                // ENHANCEMENT: For ATTACHMENT SPK without units, handle attachment-only delivery
                if ($isAttachmentSpk && empty($selected['unit_id']) && !empty($selected['inventory_attachment_id'])) {
                    error_log('DI Create - Processing ATTACHMENT-only SPK delivery');
                    // Map inventory_attachment to attachment_id if needed
                    $inv = $this->db->table('inventory_attachment')->select('attachment_id, tipe_item')->where('id_inventory_attachment', (int)$selected['inventory_attachment_id'])->get()->getRowArray();
                    $attId = $inv['attachment_id'] ?? null;
                    if ($attId) {
                        $itemResult = $this->diItemModel->insert([
                            'di_id' => $diId,
                            'item_type' => 'ATTACHMENT',
                            'attachment_id' => $attId,
                            'keterangan' => 'Pure Attachment Delivery - ' . ($inv['tipe_item'] ?? 'attachment'),
                        ]);
                        if (!$itemResult) {
                            $errors = $this->diItemModel->errors();
                            throw new \Exception('Failed to insert pure attachment: ' . implode(', ', $errors));
                        }
                        error_log('DI Create - Added pure attachment delivery (ID: ' . $attId . ')');
                    }
                } else {
                    // Standard workflow for UNIT SPK
                    if (!empty($selected['unit_id'])) {
                        $itemResult = $this->diItemModel->insert([
                            'di_id' => $diId,
                            'item_type' => 'UNIT',
                            'unit_id' => (int)$selected['unit_id'],
                        ]);
                        if (!$itemResult) {
                            $errors = $this->diItemModel->errors();
                            throw new \Exception('Failed to insert selected unit: ' . implode(', ', $errors));
                        }
                    }
                    if (!empty($selected['inventory_attachment_id'])) {
                        // Map inventory_attachment to attachment_id if needed
                        $inv = $this->db->table('inventory_attachment')->select('attachment_id')->where('id_inventory_attachment', (int)$selected['inventory_attachment_id'])->get()->getRowArray();
                        $attId = $inv['attachment_id'] ?? null;
                        if ($attId) {
                            $itemResult = $this->diItemModel->insert([
                                'di_id' => $diId,
                                'item_type' => 'ATTACHMENT',
                                'attachment_id' => $attId,
                            ]);
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
                'debug' => [
                    'db_error' => $dbError,
                    'last_query' => $lastQuery,
                ],
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
            error_log('Error getting table info: ' . $e->getMessage());
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
                'debug' => [
                    'db_error' => $dbError,
                    'last_query' => $lastQuery,
                    'payload' => $payload,
                ],
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
                'jenis_perintah_kerja_id' => $jenisPerintahKerjaId,
                'tujuan_perintah_kerja_id' => $tujuanPerintahKerjaId,
                'unit_ids' => $unitIds
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

            // Base query with JOIN to customer_locations and customers
            $builder = $this->db->table('kontrak k');
            $builder->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left');
            $builder->join('customers c', 'cl.customer_id = c.id', 'left');
            
            $countBuilder = $this->db->table('kontrak k');
            $countBuilder->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left');
            $countBuilder->join('customers c', 'cl.customer_id = c.id', 'left');

            // Status filter functionality
            $statusFilter = trim($this->request->getPost('statusFilter') ?? 'all');
            if ($statusFilter !== 'all') {
                if ($statusFilter === 'expiring') {
                    // Expiring contracts (Aktif status and expiring within 30 days)
                    $expiringDate = date('Y-m-d', strtotime('+30 days'));
                    $builder->where('k.status', 'Aktif')
                           ->where('k.tanggal_berakhir <=', $expiringDate)
                           ->where('k.tanggal_berakhir >=', date('Y-m-d'));
                    $countBuilder->where('k.status', 'Aktif')
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
                            (SELECT COUNT(*) FROM inventory_unit iu WHERE iu.kontrak_id = k.id) as calculated_total_units,
                            COALESCE(k.nilai_total, 
                                    (SELECT SUM(jumlah_dibutuhkan * harga_per_unit_bulanan) FROM kontrak_spesifikasi ks WHERE ks.kontrak_id = k.id), 
                                    0) as calculated_value');

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
                        ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
                        ->join('customers c', 'cl.customer_id = c.id', 'left')
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
                                (SELECT COUNT(*) FROM inventory_unit iu WHERE iu.kontrak_id = k.id) as calculated_total_units,
                                COALESCE(k.nilai_total, 
                                        (SELECT SUM(jumlah_dibutuhkan * harga_per_unit_bulanan) FROM kontrak_spesifikasi ks WHERE ks.kontrak_id = k.id), 
                                        0) as calculated_value')
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
                'error' => 'Server error: ' . $e->getMessage(),
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
                'message' => 'Error checking contract number: ' . $e->getMessage(),
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
                'message' => 'Gagal generate nomor kontrak: ' . $e->getMessage(),
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
                    cl.location_name as lokasi,
                    k.tanggal_mulai,
                    k.tanggal_berakhir as tanggal_selesai,
                    k.status,
                    k.total_units,
                    k.nilai_total,
                    k.dibuat_pada as created_at,
                    k.diperbarui_pada as updated_at
                FROM kontrak k
                LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
                LEFT JOIN customers c ON cl.customer_id = c.id
                ORDER BY k.id DESC
            ";
            
            $result = $db->query($query);
            $data = $result->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Marketing::getData(): ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data kontrak: ' . $e->getMessage()
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
            log_message('error', 'Error in Marketing::getDIData(): ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data DI: ' . $e->getMessage()
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
                                               cl.location_name,
                                               cl.contact_person,
                                               cl.phone,
                                               cl.address,
                                               CONCAT(u.first_name, ' ', u.last_name) as dibuat_oleh_nama
                                        FROM kontrak k 
                                        LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id 
                                        LEFT JOIN customers c ON cl.customer_id = c.id 
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
                'message' => 'Gagal memuat data kontrak: ' . $e->getMessage()
            ]);
        }
    }

    // Method updateKontrak moved to Kontrak controller

    // Method deleteKontrak moved to Kontrak controller

    private function getKontrakStats()
    {
        $stats = [
            'total' => $this->db->table('kontrak')->countAllResults(),
            'active' => $this->db->table('kontrak')->where('status', 'Aktif')->countAllResults(),
            'expiring' => 0, // Will be calculated based on date
            'expired' => $this->db->table('kontrak')->where('status', 'Berakhir')->countAllResults()
        ];

        // Calculate expiring contracts (within 30 days)
        $expiringDate = date('Y-m-d', strtotime('+30 days'));
        $stats['expiring'] = $this->db->table('kontrak')
            ->where('status', 'Aktif')
            ->where('tanggal_berakhir <=', $expiringDate)
            ->where('tanggal_berakhir >=', date('Y-m-d'))
            ->countAllResults();

        return $stats;
    }

    private function getStatusClass($status)
    {
        switch ($status) {
            case 'Aktif': return 'success';
            case 'Pending': return 'warning';
            case 'Berakhir': return 'secondary';
            case 'Dibatalkan': return 'danger';
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
                            ->orLike('iu.serial_number', $searchValue)
                            ->orLike('mu.merk_unit', $searchValue)
                            ->orLike('mu.model_unit', $searchValue)
                            ->orLike('tu.tipe', $searchValue)
                            ->orLike('tu.jenis', $searchValue)
                            ->orLike('iu.lokasi_unit', $searchValue)
                        ->groupEnd();

                        $count->groupStart()
                            ->like('iu.no_unit', $searchValue)
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
                            'no_unit'         => $r['no_unit'],
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
            ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.status_unit_id, iu.lokasi_unit, iu.created_at')
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
            
            // Get customer location if exists
            $customer_location = null;
            if (!empty($kontrak['customer_location_id'])) {
                $locationId = (int)$kontrak['customer_location_id'];
                $customer_location = $this->db->query("SELECT cl.*, c.customer_name 
                                                     FROM customer_locations cl 
                                                     LEFT JOIN customers c ON cl.customer_id = c.id 
                                                     WHERE cl.id = ?", 
                                                     [$locationId])->getRowArray();
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
                'message' => 'Error during cleanup: ' . $e->getMessage()
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
                'message' => 'Error: ' . $e->getMessage()
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
                'message' => 'Error: ' . $e->getMessage()
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
                'message' => 'Error: ' . $e->getMessage()
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
                'message' => 'Error: ' . $e->getMessage()
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
                'message' => 'Error loading customer locations: ' . $e->getMessage(),
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
                    'message' => 'Customer not found'
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
                    'message' => 'Customer not found'
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
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation not found'
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

            $updated = $quotationModel->update($quotationId, [
                'workflow_stage' => 'SENT',
                'stage' => 'SENT',
                'sent_at' => date('Y-m-d H:i:s')
            ]);

            if ($updated) {
                // Log activity
                $this->logActivity('send_quotation', 'quotations', $quotationId, 'Quotation ' . $quotation['quotation_number'] . ' sent to customer');

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
                'message' => 'Failed to send quotation: ' . $e->getMessage()
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
                    'message' => 'Quotation not found'
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
                'message' => 'Failed to create customer: ' . $e->getMessage()
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
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation not found'
                ]);
            }

            if ($quotation['workflow_stage'] !== 'SENT') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only sent quotations can be marked as deal'
                ]);
            }

            // Check if specifications exist
            $hasSpecs = $this->quotationSpecificationModel->where('id_quotation', $quotationId)->countAllResults() > 0;
            if (!$hasSpecs) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please add specifications before marking as deal'
                ]);
            }

            // Update quotation to DEAL status
            $updated = $quotationModel->update($quotationId, [
                'workflow_stage' => 'DEAL',
                'stage' => 'ACCEPTED',
                'is_deal' => 1,
                'deal_date' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if (!$updated) {
                throw new \Exception('Failed to update quotation status');
            }

            // Auto-create customer from prospect data (Simplified approach)
            $customerMessage = '';
            $customerExists = false;
            
            try {
                $customerModel = new \App\Models\CustomerModel();
                
                // Simple check by name only (using correct field name)
                $existingCustomer = $customerModel->where('customer_name', $quotation['prospect_name'])->first();
                
                if ($existingCustomer) {
                    // Customer already exists
                    $customerId = $existingCustomer['id'];
                    $customerExists = true;
                    $customerMessage = 'Customer sudah terdaftar: ' . $existingCustomer['customer_name'];
                    
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
                        $customerMessage = 'Customer baru berhasil ditambahkan: ' . $quotation['prospect_name'] . ' (Detail dapat dilengkapi nanti)';
                        
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
                        $customerMessage = 'Customer gagal ditambahkan (' . $errorMsg . '), silakan buat manual';
                    }
                }
            } catch (\Exception $customerError) {
                log_message('error', 'Auto customer creation failed: ' . $customerError->getMessage());
                $customerMessage = 'Customer gagal ditambahkan otomatis, silakan buat manual';
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'Marketing::markAsDeal - Transaction failed');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to mark as deal: Transaction failed'
                ]);
            }

            // Log activity
            $this->logActivity('mark_as_deal', 'quotations', $quotationId, 
                'Quotation marked as deal: ' . $quotation['quotation_number']);

            // Check customer profile completion status
            $profileStatus = $customerModel->getCustomerProfileStatus($customerId);
            $needsProfileCompletion = !$profileStatus['complete'];

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Quotation marked as DEAL! ' . $customerMessage,
                'customer_exists' => $customerExists,
                'customer_message' => $customerMessage,
                'customer_id' => $customerId,
                'quotation_id' => $quotationId,
                'needs_profile_completion' => $needsProfileCompletion,
                'profile_status' => $profileStatus
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Marketing::markAsDeal - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark as deal: ' . $e->getMessage()
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
                'message' => 'Failed to get customer profile status: ' . $e->getMessage()
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
                    'message' => 'Quotation not found'
                ]);
            }

            if ($quotation['workflow_stage'] !== 'SENT') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only sent quotations can be marked as not deal'
                ]);
            }

            $updated = $quotationModel->update($quotationId, [
                'workflow_stage' => 'NOT_DEAL',
                'stage' => 'REJECTED',
                'is_deal' => 0,
                'rejected_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($updated) {
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
                'message' => 'Failed to mark as not deal: ' . $e->getMessage()
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

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation not found'
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

            // Generate contract number
            $contractNumber = $this->generateContractNumberInternal();

            // Create contract record
            $contractData = [
                'no_kontrak' => $contractNumber,
                'customer_id' => $quotation['created_customer_id'],
                'quotation_id' => $quotationId,
                'nilai_total' => $quotation['total_amount'],
                'tanggal_mulai' => date('Y-m-d'),
                'tanggal_berakhir' => date('Y-m-d', strtotime('+12 months')),
                'status' => 'Draft',
                'created_by' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $kontrakModel = new \App\Models\KontrakModel();
            $contractId = $kontrakModel->insert($contractData);

            if ($contractId) {
                // Update quotation with contract_id
                $quotationModel->update($quotationId, [
                    'created_contract_id' => $contractId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Log activity
                $this->logActivity('create_contract', 'kontrak', $contractId, 
                    'Contract created from quotation: ' . $quotation['quotation_number']);

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
                'message' => 'Failed to create contract: ' . $e->getMessage()
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
                    'message' => 'Quotation not found'
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
                'message' => 'Failed to create purchase order: ' . $e->getMessage()
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

        try {
            $quotationModel = new \App\Models\QuotationModel();
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quotation not found'
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
                'message' => 'Failed to create SPK: ' . $e->getMessage()
            ]);
        }
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
                    'message' => 'Quotation not found'
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
                'message' => 'Failed to open specifications: ' . $e->getMessage()
            ]);
        }
    }
}
