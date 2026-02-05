<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface; 
use App\Models\InventoryUnitModel;
use App\Models\InventoryAttachmentModel;
use App\Models\SpkModel;
use App\Helpers\UnitComponentFormatter;
use App\Traits\ActivityLoggingTrait;


class Service extends BaseController
{
    use ActivityLoggingTrait;
    
    protected $db;
    protected $unitModel;
    protected $attModel;
    protected $spkModel;
    protected $componentFormatter;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->unitModel = new InventoryUnitModel();
        $this->attModel = new InventoryAttachmentModel();
        $this->spkModel = new SpkModel();
        $this->componentFormatter = new UnitComponentFormatter();
        
        // Load auth helper for division filtering
        helper('auth');
    }

    /**
     * Print verification page
     */
    public function printVerification($workOrderId = null)
    {
        // Check permission for viewing service verification
        if (!$this->hasPermission('service.work_orders.view')) {
            return redirect()->to('/')->with('error', 'Access denied: You do not have permission to view service verification');
        }
        
        $data = [];
        
        // Get work order ID from parameter or GET request
        if (!$workOrderId) {
            $workOrderId = $this->request->getGet('wo_id');
        }
        
        if ($workOrderId) {
            // Load WorkOrderModel untuk mendapatkan data work order
            $workOrderModel = new \App\Models\WorkOrderModel();
            $workOrder = $workOrderModel->getDetailWorkOrder($workOrderId);
            
            if ($workOrder) {
                $data['workOrder'] = $workOrder;
                // Also pass work order ID for JavaScript
                $data['workOrderId'] = $workOrderId;
            }
        }
        
        return view('service/print_verification', $data);
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

        // Get battery info - include both available and in use for the unit
        $battery = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment, ia.baterai_id, ia.sn_baterai, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->join('baterai b', 'ia.baterai_id = b.id', 'left')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'battery')
            ->whereIn('ia.attachment_status', ['AVAILABLE', 'IN_USE']) // Available or In use for this unit
            ->get()->getRowArray();

        if ($battery) {
            $components['battery'] = $battery;
        }

        // Get charger info - include both available and in use for the unit
        $charger = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment, ia.charger_id, ia.sn_charger, c.merk_charger, c.tipe_charger')
            ->join('charger c', 'ia.charger_id = c.id_charger', 'left')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'charger')
            ->whereIn('ia.attachment_status', ['AVAILABLE', 'IN_USE']) // Available or In use for this unit
            ->get()->getRowArray();

        if ($charger) {
            $components['charger'] = $charger;
        }

        // Get attachment info - include both available and used for the unit
        $attachment = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment, ia.attachment_id, ia.sn_attachment, a.tipe, a.merk, a.model')
            ->join('attachment a', 'ia.attachment_id = a.id_attachment', 'left')
            ->where('ia.id_inventory_unit', $unitId)
            ->where('ia.tipe_item', 'attachment')
            ->whereIn('ia.attachment_status', ['AVAILABLE', 'USED']) // Available or In use for this unit
            ->get()->getRowArray();

        if ($attachment) {
            $components['attachment'] = $attachment;
        }

        return $components;
    }

    /**
     * Redirect print SPK to Marketing controller for consistency
     */
    public function spkPrint($id)
    {
        // Check permission for printing SPK
        if (!$this->hasPermission('service.spk_service.view')) {
            return redirect()->to('/')->with('error', 'Access denied: You do not have permission to print SPK');
        }
        
        return redirect()->to(base_url('marketing/spk/print/' . $id));
    }

    /**
     * Update component assignment in inventory_attachment
     */
    private function updateComponentAssignment($unitId, $componentType, $inventoryAttachmentId, $action = 'assign')
    {
        if (!$inventoryAttachmentId) return false;

        // First, unassign any existing component of this type from the unit
        $this->db->table('inventory_attachment')
            ->where('id_inventory_unit', $unitId)
            ->where('tipe_item', $componentType)
            ->update([
                'id_inventory_unit' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        // Note: status_unit akan otomatis sinkronisasi dengan trigger (menjadi 7 = Available)

        // Then assign the new component
        $this->db->table('inventory_attachment')
            ->where('id_inventory_attachment', $inventoryAttachmentId)
            ->update([
                'id_inventory_unit' => $unitId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        // Note: status_unit akan otomatis sinkronisasi dengan trigger berdasarkan unit status

        return $this->db->affectedRows() > 0;
    }
    public function index()
    {
        $data = [
            'title' => 'Service Division | OPTIMA',
            'page_title' => 'Service Division Dashboard',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service' => 'Service Division'
            ]
        ];

        return view('service/index', $data);
    }

    public function workOrders()
    {
        // Load models
        $statusModel = new \App\Models\WorkOrderStatusModel();
        $priorityModel = new \App\Models\WorkOrderPriorityModel();
        $categoryModel = new \App\Models\WorkOrderCategoryModel();
        $staffModel = new \App\Models\EmployeeModel();
        $inventoryModel = new \App\Models\InventoryUnitModel();
        $areaModel = new \App\Models\AreaModel();
        $sparepartModel = new \App\Models\SparepartModel();

        $data = [
            'title' => 'Work Orders | OPTIMA',
            'page_title' => 'Work Orders',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service/work-orders' => 'Work Orders'
            ],
            'mode' => 'active',
            'active_statuses' => ['OPEN', 'KENDALA', 'PENDING'],
            // Required data for view
            'statuses' => $statusModel->getActiveStatuses(),
            'priorities' => $priorityModel->getActivePriorities(),
            'categories' => $categoryModel->getActiveCategories(),
            'staff' => $staffModel->getStaffByRole(),
            'units' => $inventoryModel->getUnitsForDropdown(),
            'areas' => $areaModel->getActiveAreas(),
            'spareparts' => $sparepartModel->getActiveSpareparts()
        ];

        return view('service/work_orders', $data);
    }

    public function workOrderHistory()
    {
        $data = [
            'title' => 'Work Order History | OPTIMA',
            'page_title' => 'Work Order History',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service/work-orders/history' => 'History'
            ],
            'mode' => 'history',
            'history_statuses' => ['CLOSED'],
        ];

        return view('service/work_order_history', $data);
    }

    // REMOVED: getWorkOrdersData() - using WorkOrderController::getWorkOrders() instead

    public function pmps()
    {
        $data = [
            'title' => 'PMPS | OPTIMA',
            'page_title' => 'Preventive Maintenance Planned Service',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service/pmps' => 'PMPS'
            ]
        ];

        return view('service/pmps', $data);
    }

    public function exportWorkorder()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.workorder')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.workorder');
        }
        // Activity Log: EXPORT work orders
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'work_orders', 0, 'Export Work Order CSV', [
                'module_name' => 'SERVICE',
                'submenu_item' => 'Work Orders',
                'business_impact' => 'LOW'
            ]);
        }
        return view('service/export_workorder');
    }

    public function exportEmployee()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.service_employee')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.service_employee');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'employees', 0, 'Export Employee CSV', [
                'module_name' => 'SERVICE',
                'submenu_item' => 'Area & Employee Management',
                'business_impact' => 'LOW'
            ]);
        }
        return view('service/export_employee');
    }

    public function exportArea()
    {
        if (method_exists($this, 'hasPermission') && !$this->hasPermission('export.service_area')) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: Missing permission export.service_area');
        }
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('EXPORT', 'areas', 0, 'Export Area CSV', [
                'module_name' => 'SERVICE',
                'submenu_item' => 'Area & Employee Management',
                'business_impact' => 'LOW'
            ]);
        }
        return view('service/export_area');
    }

    // --- SPK Service Handlers ---
    public function spkService()
    {
        // Extract SPK ID from URL for auto-opening modal (from notification deep linking)
        $uri = service('uri');
        $autoOpenSpkId = null;
        
        // Check if URL matches /service/spk/detail/{id}
        $segments = $uri->getSegments();
        if (count($segments) >= 3 && $segments[1] === 'spk' && $segments[2] === 'detail' && isset($segments[3]) && is_numeric($segments[3])) {
            $autoOpenSpkId = (int)$segments[3];
        }
        
        $data = [
            'title' => 'Work Orders (SPK) | OPTIMA',
            'page_title' => 'Work Orders (SPK) from Marketing',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service/spk_service' => 'Work Orders (SPK)'
            ],
            'autoOpenSpkId' => $autoOpenSpkId
        ];
    // Use single view (modular content merged into spk_service.php)
    return view('service/spk_service', $data);
    }

    // Get areas for SPK Service dropdown
    public function areas()
    {
        try {
            $db = \Config\Database::connect();
            
            if (!$db) {
                log_message('error', 'Areas endpoint: Database connection failed');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Database connection failed'
                ]);
            }
            
            // Get user's area and department scope for filtering
            $scope = get_user_area_department_scope();
            
            $builder = $db->table('areas a');
            $builder->select('a.id, a.area_code, a.area_name, a.area_type')
                    ->where('a.is_active', 1);
            
            // Apply area filtering if user has limited scope
            if ($scope !== null && !empty($scope['areas'])) {
                $builder->whereIn('a.id', $scope['areas']);
                log_message('info', 'Areas filtering applied: User limited to areas: ' . implode(',', $scope['areas']));
            }
            
            $areas = $builder->orderBy('a.area_type', 'ASC')
                           ->orderBy('a.area_name', 'ASC')
                           ->get()
                           ->getResultArray();
            
            log_message('info', 'Areas loaded successfully: ' . count($areas) . ' areas found');
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $areas
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error loading areas: ' . $e->getMessage());
            log_message('error', 'Areas error trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading areas: ' . $e->getMessage()
            ]);
        }
    }
    
    // Debug endpoint to check current user scope
    public function userScope()
    {
        try {
            $scope = get_user_area_department_scope();
            $session = session();
            
            $debugInfo = [
                'user_id' => $session->get('user_id'),
                'username' => $session->get('username'),
                'role' => $session->get('role'),
                'division' => $session->get('division_id'),
                'scope' => $scope,
                'allowed_departments' => $this->getAllowedServiceDepartemenIds()
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $debugInfo
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    // Get SPK department for filtering units
    public function getSpkDepartment($spkId)
    {
        try {
            // Validate SPK ID
            if (empty($spkId) || !is_numeric($spkId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid SPK ID provided'
                ]);
            }
            
            log_message('info', "Getting department for SPK ID: {$spkId}");
            
            // Updated query to use quotation_specifications instead of deprecated kontrak_spec
            $spk = $this->db->table('spk s')
                ->select('d.nama_departemen')
                ->join('quotation_specifications qs', 's.quotation_specification_id = qs.id_specification', 'left')
                ->join('departemen d', 'qs.departemen_id = d.id_departemen', 'left')
                ->where('s.id', $spkId)
                ->get()
                ->getRowArray();
                
            if (empty($spk)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SPK not found'
                ]);
            }
                
            log_message('info', "SPK department found: " . ($spk['nama_departemen'] ?? 'null'));
                
            return $this->response->setJSON([
                'success' => true,
                'department' => $spk['nama_departemen'] ?? null
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting SPK department: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting SPK department'
            ]);
        }
    }
    
    /**
     * Generate unique unit number for STOCK_NON_ASET units
     */
    private function generateUnitNumber()
    {
        // Get the highest unit number
        $maxUnit = $this->db->table('inventory_unit')
                           ->selectMax('no_unit')
                           ->where('no_unit IS NOT NULL')
                           ->where('no_unit !=', '')
                           ->get()
                           ->getRowArray();
        
        $nextNumber = 1;
        if ($maxUnit && $maxUnit['no_unit']) {
            $nextNumber = (int)$maxUnit['no_unit'] + 1;
        }
        
        // Ensure the number doesn't exist
        while ($this->db->table('inventory_unit')->where('no_unit', $nextNumber)->countAllResults() > 0) {
            $nextNumber++;
        }
        
        return $nextNumber;
    }

    public function spkList()
    {
        try {
            $userId = session()->get('user_id');
            
            // SIMPLE FILTER: Cek divisi user
            // Service Diesel (division_id = 1) -> hanya lihat DIESEL (1) & GASOLINE (3)
            // Service Electric (division_id = 2) -> hanya lihat ELECTRIC (2)
            $allowedDeptIds = null; // null = tampilkan semua
            
            // Use global helper function for division-based filtering
            $allowedDeptIds = get_user_division_departments();
            
            // Get all SPK
            $list = $this->db->table('spk')->orderBy('id','DESC')->get()->getResultArray();
            log_message('debug', 'Total SPK found: ' . count($list) . ', Filter: ' . ($allowedDeptIds ? json_encode($allowedDeptIds) : 'none'));
            
            // Filter berdasarkan departemen_id di JSON spesifikasi
            $filteredList = [];
            foreach ($list as $spk) {
                try {
                    // Add stage status (with error handling)
                    try {
                        $stageStatus = $this->getSpkStageStatusData($spk['id']);
                        if ($stageStatus) {
                            $spk['stage_status'] = $stageStatus;
                        }
                    } catch (\Exception $e) {
                        // Skip stage status if error, continue with SPK data
                        log_message('debug', 'Error getting stage status for SPK ' . $spk['id'] . ': ' . $e->getMessage());
                    }
                    
                    // Apply filter jika ada
                    if ($allowedDeptIds !== null && is_array($allowedDeptIds)) {
                        // Decode JSON spesifikasi
                        $spesifikasi = [];
                        if (!empty($spk['spesifikasi'])) {
                            $decoded = json_decode($spk['spesifikasi'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $spesifikasi = $decoded;
                            }
                        }
                        
                        // Cek departemen_id
                        $spkDeptId = null;
                        if (isset($spesifikasi['departemen_id'])) {
                            // Handle both string and integer
                            $deptId = $spesifikasi['departemen_id'];
                            $spkDeptId = is_numeric($deptId) ? (int)$deptId : null;
                        }
                        
                        // Jika departemen_id sesuai dengan yang diizinkan, tambahkan ke list
                        if ($spkDeptId && in_array($spkDeptId, $allowedDeptIds)) {
                            $filteredList[] = $spk;
                        }
                        // SPK tanpa departemen_id atau tidak sesuai -> EXCLUDE (jangan tampilkan)
                        // SPK tanpa departemen_id atau tidak sesuai -> skip
                    } else {
                        // Tidak ada filter, tambahkan semua
                        $filteredList[] = $spk;
                    }
                } catch (\Exception $e) {
                    // Log error but continue processing other SPKs
                    log_message('error', 'Error processing SPK ' . ($spk['id'] ?? 'unknown') . ': ' . $e->getMessage());
                    continue;
                }
            }
            
            log_message('debug', 'Filtered SPK count: ' . count($filteredList));
            return $this->response->setJSON(['success'=>true,'data'=>$filteredList,'csrf_hash'=>csrf_hash()]);
        } catch (\Exception $e) {
            log_message('error', 'SPK List Error: ' . $e->getMessage());
            log_message('error', 'SPK List Trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error loading SPK list: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function spkDetail($id)
    {
        // Check permission: Service punya service.access (module permission)
        if (!$this->canAccess('service')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied: You do not have permission to view SPK details'
                ])->setStatusCode(403);
            }
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }
        
        $row = $this->db->table('spk')->where('id', (int)$id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'SPK tidak ditemukan']);
        }
        
        // Note: Service mengakses kontrak_spesifikasi di bawah (line 733-749)
        // Ini adalah cross-division access yang sudah di-handle oleh permission check di atas
        // Service Head/Staff punya: marketing.kontrak.view (resource permission)
        
        // Get stage status from new spk_unit_stages table
        $stageStatus = $this->getSpkStageStatusData($id);
        if ($stageStatus) {
            $row['stage_status'] = $stageStatus;
        }
        $spec = [];
        if (!empty($row['spesifikasi'])) {
            $decoded = json_decode($row['spesifikasi'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $spec = $decoded;
            }
        }
        // Enrich names for ID-based fields (best-effort)
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
        // Enrich selected items (unit & attachment) with full details
        // First, check if data comes from approval workflow
        if (!empty($row['persiapan_unit_id'])) {
            // Prioritaskan cari dengan id_inventory_unit
            $u = $this->db->table('inventory_unit iu')
                ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id, iu.model_mast_id, iu.roda_id, iu.ban_id, iu.valve_id')
                ->select('iu.sn_mast, iu.sn_mesin')
                ->select('mu.merk_unit, mu.model_unit')
                ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model')
                ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name')
                ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left') 
                ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                ->join('mesin m','m.id = iu.model_mesin_id','left')
                ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                ->where('iu.id_inventory_unit', $row['persiapan_unit_id'])
                ->get()->getRowArray();
            // Fallback ke no_unit jika tidak ditemukan
            if (!$u) {
                $u = $this->db->table('inventory_unit iu')
                    ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_unit_id, iu.model_mast_id, iu.roda_id, iu.ban_id, iu.valve_id')
                    ->select('iu.sn_mast, iu.sn_mesin')
                    ->select('mu.merk_unit, mu.model_unit')
                    ->select('tu.tipe as tipe_jenis, tu.jenis as jenis_unit')
                    ->select('tm.tipe_mast as mast_model, m.model_mesin as mesin_model')
                    ->select('k.kapasitas_unit as kapasitas_name, d.nama_departemen as departemen_name')
                    ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                    ->join('tipe_unit tu','tu.id_tipe_unit = iu.tipe_unit_id','left') 
                    ->join('tipe_mast tm','tm.id_mast = iu.model_mast_id','left')
                    ->join('mesin m','m.id = iu.model_mesin_id','left')
                    ->join('kapasitas k','k.id_kapasitas = iu.kapasitas_unit_id','left')
                    ->join('departemen d','d.id_departemen = iu.departemen_id','left')
                    ->where('iu.no_unit', $row['persiapan_unit_id'])
                    ->get()->getRowArray();
            }
            if ($u) {
                // Get unit components from inventory_attachment (single source of truth for serial numbers)
                $unitComponents = $this->getUnitComponents($u['id_inventory_unit']);
                
                // Extract battery data
                $batteryData = array();
                if (isset($unitComponents['battery'])) {
                    $batteryData = $unitComponents['battery'];
                }
                
                $batteryModel = null;
                if (isset($batteryData['tipe_baterai'])) {
                    $batteryModel = $batteryData['tipe_baterai'];
                }
                
                $batteryMerk = null;
                if (isset($batteryData['merk_baterai'])) {
                    $batteryMerk = $batteryData['merk_baterai'];
                }
                
                $batterySN = null;
                if (isset($batteryData['sn_baterai'])) {
                    $batterySN = $batteryData['sn_baterai'];
                }
                
                $batteryDisplay = '';
                if ($batteryModel) {
                    $batteryDisplay = $batteryModel;
                    if ($batteryMerk && $batteryMerk != $batteryModel) {
                        $batteryDisplay = $batteryMerk . ' ' . $batteryModel;
                    }
                    if ($batterySN) {
                        $batteryDisplay .= ' (' . $batterySN . ')';
                    }
                }
                
                // Extract charger data
                $chargerData = array();
                if (isset($unitComponents['charger'])) {
                    $chargerData = $unitComponents['charger'];
                }
                
                $chargerModel = null;
                if (isset($chargerData['tipe_charger'])) {
                    $chargerModel = $chargerData['tipe_charger'];
                }
                
                $chargerMerk = null;
                if (isset($chargerData['merk_charger'])) {
                    $chargerMerk = $chargerData['merk_charger'];
                }
                
                $chargerSN = null;
                if (isset($chargerData['sn_charger'])) {
                    $chargerSN = $chargerData['sn_charger'];
                }
                
                $chargerDisplay = '';
                if ($chargerModel) {
                    $chargerDisplay = $chargerModel;
                    if ($chargerMerk && $chargerMerk != $chargerModel) {
                        $chargerDisplay = $chargerMerk . ' ' . $chargerModel;
                    }
                    if ($chargerSN) {
                        $chargerDisplay .= ' (' . $chargerSN . ')';
                    }
                }
                
                // Extract attachment data
                $attachmentData = array();
                if (isset($unitComponents['attachment'])) {
                    $attachmentData = $unitComponents['attachment'];
                }
                
                $attachmentModel = null;
                if (isset($attachmentData['model'])) {
                    $attachmentModel = $attachmentData['model'];
                }
                
                $attachmentMerk = null;
                if (isset($attachmentData['merk'])) {
                    $attachmentMerk = $attachmentData['merk'];
                }
                
                $attachmentTipe = null;
                if (isset($attachmentData['tipe'])) {
                    $attachmentTipe = $attachmentData['tipe'];
                }
                
                $attachmentSN = null;
                if (isset($attachmentData['sn_attachment'])) {
                    $attachmentSN = $attachmentData['sn_attachment'];
                }
                
                $attachmentDisplay = '';
                if ($attachmentTipe) {
                    $attachmentDisplay = $attachmentTipe;
                    if ($attachmentMerk) {
                        $attachmentDisplay .= ' ' . $attachmentMerk;
                    }
                    if ($attachmentModel) {
                        $attachmentDisplay .= ' ' . $attachmentModel;
                    }
                    if ($attachmentSN) {
                        $attachmentDisplay .= ' (' . $attachmentSN . ')';
                    }
                }
                
                $label = trim(($u['no_unit'] ?: '-') . ' - ' . ($u['merk_unit'] ?: '-') . ' ' . ($u['model_unit'] ?: '') . ' @ ' . ($u['lokasi_unit'] ?: '-'));
                // Ambil data valve, mast, roda, ban
                $valve = $this->db->table('valve')->select('jumlah_valve')->where('id_valve', $u['valve_id'] ?? null)->get()->getRowArray();
                $mast = $this->db->table('tipe_mast')->select('tipe_mast')->where('id_mast', $u['model_mast_id'] ?? null)->get()->getRowArray();
                $roda = $this->db->table('jenis_roda')->select('tipe_roda')->where('id_roda', $u['roda_id'] ?? null)->get()->getRowArray();
                $ban = $this->db->table('tipe_ban')->select('tipe_ban')->where('id_ban', $u['ban_id'] ?? null)->get()->getRowArray();
                $enriched['selected']['unit'] = [
                    'id' => (int)$u['id_inventory_unit'],
                    'label' => $label,
                    'no_unit' => $u['no_unit'] ?? null,
                    'serial_number' => $u['serial_number'] ?? null,
                    'tahun_unit' => $u['tahun_unit'] ?? null,
                    'merk_unit' => $u['merk_unit'] ?? null,
                    'model_unit' => $u['model_unit'] ?? null,
                    'tipe_jenis' => $u['tipe_jenis'] ?? null,
                    'jenis_unit' => $u['jenis_unit'] ?? null,
                    'lokasi_unit' => $u['lokasi_unit'] ?? null,
                    'kapasitas_name' => $u['kapasitas_name'] ?? null,
                    'departemen_name' => $u['departemen_name'] ?? null,
                    'valve' => $valve['jumlah_valve'] ?? '',
                    'mast' => $mast['tipe_mast'] ?? '',
                    'roda' => $roda['tipe_roda'] ?? '',
                    'ban' => $ban['tipe_ban'] ?? '',
                    // Format: Model (SN) atau hanya Model jika SN kosong
                    'sn_mast' => $u['sn_mast'] ?? null,
                    'sn_mesin' => $u['sn_mesin'] ?? null, 
                    'sn_baterai' => $batterySN ?? null,
                    'sn_charger' => $chargerSN ?? null,
                    'sn_mast_formatted' => !empty($u['sn_mast']) ? ($u['mast_model'] ?? 'Mast') . ' (' . $u['sn_mast'] . ')' : ($u['mast_model'] ?? ''),
                    'sn_mesin_formatted' => !empty($u['sn_mesin']) ? ($u['mesin_model'] ?? 'Mesin') . ' (' . $u['sn_mesin'] . ')' : ($u['mesin_model'] ?? ''),
                    'sn_baterai_formatted' => $batteryDisplay ?: '',
                    'sn_charger_formatted' => $chargerDisplay ?: '',
                    'attachment_display' => $attachmentDisplay ?: '',
                ];
            }
        }
        
        if (!empty($row['fabrikasi_attachment_id'])) {
            $a = $this->db->table('inventory_attachment ia')
                ->select('ia.id_inventory_attachment, ia.sn_attachment, ia.lokasi_penyimpanan')
                ->select('att.tipe, att.merk, att.model')
                ->join('attachment att','att.id_attachment = ia.attachment_id','left')
                ->where('ia.id_inventory_attachment', $row['fabrikasi_attachment_id'])
                ->get()->getRowArray();
                
            if ($a) {
                $label = trim(($a['tipe'] ?: '-') . ' ' . ($a['merk'] ?: '') . ' ' . ($a['model'] ?: ''));
                $suffix = [];
                if (!empty($a['sn_attachment'])) $suffix[] = 'SN: '.$a['sn_attachment'];
                if (!empty($a['lokasi_penyimpanan'])) $suffix[] = '@ '.$a['lokasi_penyimpanan'];
                if ($suffix) $label .= ' ['.implode(', ', $suffix).']';
                $enriched['selected']['attachment'] = [
                    'id' => (int)$a['id_inventory_attachment'],
                    'label' => $label,
                    'tipe' => $a['tipe'] ?? null,
                    'merk' => $a['merk'] ?? null,
                    'model' => $a['model'] ?? null,
                    'sn_attachment' => $a['sn_attachment'] ?? null,
                    'lokasi_penyimpanan' => $a['lokasi_penyimpanan'] ?? null,
                    'sn_attachment_formatted' => !empty($a['sn_attachment']) ? ($a['model'] ?? 'Attachment') . ' (' . $a['sn_attachment'] . ')' : ($a['model'] ?? ''),
                ];
            }
        }
        
    // Legacy: Load from spesifikasi selected if no approval workflow data
        if (!empty($spec['selected']) && is_array($spec['selected'])) {
            $sel = $spec['selected'];
            if (empty($enriched['selected']['unit']) && !empty($sel['unit_id'])) {
                $u = $this->db->table('inventory_unit iu')
                    ->select('iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.sn_mast, iu.sn_mesin, iu.sn_baterai, iu.sn_charger, mu.merk_unit, mu.model_unit')
                    ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
                    ->where('iu.id_inventory_unit', (int)$sel['unit_id'])
                    ->get()->getRowArray();
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
            if (empty($enriched['selected']['attachment']) && !empty($sel['inventory_attachment_id'])) {
                $a = $this->db->table('inventory_attachment ia')
                    ->select('a.tipe, a.merk, a.model, ia.sn_attachment, ia.lokasi_penyimpanan')
                    ->join('attachment a','a.id_attachment = ia.attachment_id','left')
                    ->where('ia.id_inventory_attachment', (int)$sel['inventory_attachment_id'])
                    ->get()->getRowArray();
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

        // Calculate prepared_units from new spk_unit_stages structure
        $preparedUnits = [];
        $spkId = (int) $id;
        $totalUnits = (int) $row['jumlah_unit'];
        
        // Get units that have completed persiapan_unit stage
        for ($unitIndex = 1; $unitIndex <= $totalUnits; $unitIndex++) {
            $persiapanStage = $this->db->table('spk_unit_stages')
                ->where('spk_id', $spkId)
                ->where('unit_index', $unitIndex)
                ->where('stage_name', 'persiapan_unit')
                ->where('tanggal_approve IS NOT NULL')
                ->get()
                ->getRowArray();
                
            if ($persiapanStage) {
                $preparedUnits[] = [
                    'unit_index' => $unitIndex,
                    'unit_id' => $persiapanStage['unit_id'],
                    'area_id' => $persiapanStage['area_id'],
                    'aksesoris_tersedia' => $persiapanStage['aksesoris_tersedia'],
                    'timestamp' => $persiapanStage['tanggal_approve']
                ];
            }
        }
        
        // Debug log
        log_message('info', "SPK {$spkId}: Found " . count($preparedUnits) . " prepared units from spk_unit_stages");
        
        // IMPORTANT: If SPK status is IN_PROGRESS and no approval stages are completed,
        // clear prepared_units to avoid confusion after Marketing 
        if ($row['status'] === 'IN_PROGRESS') {
            // Check if any stages are completed using new spk_unit_stages structure
            $anyStageCompleted = $this->db->table('spk_unit_stages')
                ->where('spk_id', $spkId)
                ->where('tanggal_approve IS NOT NULL')
                ->countAllResults() > 0;
            
            // If no stages are completed, clear prepared_units
            if (!$anyStageCompleted) {
                $preparedUnits = [];
                log_message('info', "SPK {$row['id']}: Cleared prepared_units due to Marketing rollback - no approval stages completed");
            }
        }

        // Enrich prepared_units into prepared_units_detail for distinct display in Service detail
        if (!empty($preparedUnits)) {
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

        return $this->response->setJSON([
            'success'=>true,
            'data'=>$row,
            'spesifikasi'=>$enriched,
            'kontrak_spec'=>$kontrak_spec,
            'prepared_units'=>$preparedUnits,
            'csrf_hash'=>csrf_hash()
        ]);
    }

    public function spkConfirmReady($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }
        $this->db->table('spk')->where('id',$id)->update(['status'=>'READY','diperbarui_pada'=>date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success'=>true,'message'=>'Unit siap','csrf_hash'=>csrf_hash()]);
    }

    public function spkUpdateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Bad request']);
        }

        $status = $this->request->getPost('status');
        if (!$status) {
            return $this->response->setJSON(['success'=>false,'message'=>'Status tidak boleh kosong']);
        }

        // Validate allowed status transitions
        $allowedStatus = ['SUBMITTED', 'IN_PROGRESS', 'READY', 'DELIVERED', 'CANCELLED'];
        if (!in_array($status, $allowedStatus)) {
            return $this->response->setJSON(['success'=>false,'message'=>'Status tidak valid']);
        }

        $oldSpk = $this->db->table('spk')->where('id', $id)->get()->getRowArray();
        
        $this->db->table('spk')->where('id', $id)->update([
            'status' => $status,
            'diperbarui_pada' => date('Y-m-d H:i:s')
        ]);

        // Log status update using trait
        $this->logUpdate('spk', $id, $oldSpk, ['status' => $status], [
            'spk_id' => $id,
            'old_status' => $oldSpk['status'] ?? null,
            'new_status' => $status
        ]);

        return $this->response->setJSON(['success'=>true,'message'=>'Status SPK berhasil diperbarui','csrf_hash'=>csrf_hash()]);
    }

    public function spkApproveStageOld($id)
    {
        // Log request details for debugging
        $this->logSpkApprovalRequest($id);
        
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        try {
            // This method is deprecated - use the refactored version below
            throw new \Exception('Method deprecated - use refactored implementation');
            
        } catch (\Exception $e) {
            log_message('error', 'SPK Approval Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log SPK approval request details for debugging
     */
    private function logSpkApprovalRequest($id)
    {
        log_message('info', "=== SPK APPROVE STAGE METHOD CALLED ===");
        log_message('info', "ID: $id, Method: " . $this->request->getMethod());
        log_message('info', "Is AJAX: " . ($this->request->isAJAX() ? 'true' : 'false'));
        log_message('info', "POST Data: " . json_encode($this->request->getPost()));
    }

    public function spkApproveStage($id)
    {
        try {
            // Extract and validate request data
            $approvalData = $this->validateAndExtractApprovalData();
            
            // Prepare base stage data
            $stageData = $this->prepareBaseStageData($id, $approvalData);
            
            // Handle stage-specific data
            $this->handleStageSpecificData($approvalData['stage'], $stageData, $approvalData);
            
            // Save the approval
            $this->saveStageApproval($stageData, $approvalData);
            
            // Send cross-division notifications for stage completion
            helper('notification');
            if (in_array($approvalData['stage'], ['persiapan_unit', 'fabrikasi', 'pdi'])) {
                $spk = $this->spkModel->find($id);
                if ($spk) {
                    $notifData = [
                        'spk_id' => $id,
                        'spk_number' => $spk['nomor_spk'] ?? 'N/A',
                        'stage' => $approvalData['stage'],
                        'pelanggan' => $spk['pelanggan'] ?? '',
                        'lokasi' => $spk['lokasi'] ?? '',
                        'approved_by' => session('username') ?? 'System',
                        'approved_at' => date('Y-m-d H:i:s'),
                        'url' => base_url('/service/spk/view/' . $id)
                    ];
                    
                    // Add stage-specific information
                    if ($approvalData['stage'] === 'persiapan_unit') {
                        $notifData['unit_info'] = isset($spk['spesifikasi']) ? 'Unit prepared from specifications' : '';
                        $notifData['items_prepared'] = 'Battery, Charger, and other components';
                        if (function_exists('notify_spk_unit_prep_completed')) {
                            notify_spk_unit_prep_completed($notifData);
                        }
                    } elseif ($approvalData['stage'] === 'fabrikasi') {
                        $notifData['attachment_info'] = isset($approvalData['attachment_inventory_attachment_id']) ? 'Attachment configured' : '';
                        $notifData['fabrication_notes'] = $approvalData['catatan_fabrikasi'] ?? '';
                        if (function_exists('notify_spk_fabrication_completed')) {
                            notify_spk_fabrication_completed($notifData);
                        }
                    } elseif ($approvalData['stage'] === 'pdi') {
                        $notifData['spk_status'] = 'READY';
                        $notifData['ready_for_delivery'] = true;
                        $notifData['pdi_results'] = $approvalData['catatan_pdi'] ?? 'PDI completed successfully';
                        if (function_exists('notify_spk_pdi_completed')) {
                            notify_spk_pdi_completed($notifData);
                        }
                    }
                }
            }
            
            // Check if all stages are completed and update SPK status if needed
            $this->checkAndUpdateSpkStatus($id);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Stage ' . $approvalData['stage'] . ' berhasil di-approve'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'SPK Approval Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Approve Fabrikasi stage (legacy method for compatibility)
     */
    public function approveFabrikasi()
    {
        try {
            $spkId = $this->request->getPost('spk_id');
            $mekanik = $this->request->getPost('mekanik');
            $estimasi_mulai = $this->request->getPost('estimasi_mulai');
            $estimasi_selesai = $this->request->getPost('estimasi_selesai');
            $attachment_id = $this->request->getPost('attachment_inventory_attachment_id');
            $transfer_attachment = $this->request->getPost('transfer_attachment') === 'true';
            $unitIndex = (int) $this->request->getPost('unit_index') ?: 1;

            if (!$spkId || !$mekanik) {
                throw new \Exception('SPK ID dan Mekanik harus diisi');
            }

            // Prepare approval data
            $approvalData = [
                'stage' => 'fabrikasi',
                'unitIndex' => $unitIndex,
                'mekanik' => $mekanik,
                'estimasi_mulai' => $estimasi_mulai,
                'estimasi_selesai' => $estimasi_selesai,
                'attachment_id' => $attachment_id,
                'transfer_attachment' => $transfer_attachment
            ];

            // Prepare base stage data
            $stageData = $this->prepareBaseStageData($spkId, $approvalData);
            
            // Handle stage-specific data
            $this->handleStageSpecificData('fabrikasi', $stageData, $approvalData);
            
            // Save the approval
            $this->saveStageApproval($stageData, $approvalData);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Fabrikasi berhasil di-approve'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Fabrikasi Approval Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Assign items to SPK (legacy method for compatibility)
     */
    public function assignItems()
    {
        try {
            $spkId = $this->request->getPost('spk_id');
            $unitId = $this->request->getPost('unit_id');
            $attachmentId = $this->request->getPost('inventory_attachment_id');

            if (!$spkId || !$unitId) {
                throw new \Exception('SPK ID dan Unit ID harus diisi');
            }

            // Update SPK with assigned unit (using correct field names)
            $updateData = [
                'status' => 'READY'
            ];
            
            // Store unit and attachment info in spesifikasi field as JSON
            $spesifikasi = [];
            if ($unitId) {
                $spesifikasi['unit_id'] = $unitId;
            }
            if ($attachmentId) {
                $spesifikasi['attachment_id'] = $attachmentId;
            }
            
            if (!empty($spesifikasi)) {
                $updateData['spesifikasi'] = json_encode($spesifikasi);
            }
            
            $this->db->table('spk')->where('id', $spkId)->update($updateData);

            // Send notification: SPK Assigned
            helper('notification');
            $spk = $this->db->table('spk')->where('id', $spkId)->get()->getRowArray();
            if ($spk) {
                notify_spk_assigned([
                    'id' => $spkId,
                    'nomor_spk' => $spk['nomor_spk'] ?? '',
                    'unit_id' => $unitId,
                    'attachment_id' => $attachmentId,
                    'assigned_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/service/spk/detail/' . $spkId)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item berhasil di-assign ke SPK'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Assign Items Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Change SPK unit (for rollback system)
     */
    public function changeSpkUnit($spkId)
    {
        try {
            $newUnitId = $this->request->getPost('unit_id');
            $unitIndex = (int) $this->request->getPost('unit_index') ?: 1;
            $reason = $this->request->getPost('reason');

            if (!$newUnitId || !$reason) {
                throw new \Exception('Unit ID dan alasan harus diisi');
            }

            // Update SPK unit (store in spesifikasi field)
            $spesifikasi = json_decode($this->db->table('spk')->where('id', $spkId)->get()->getRowArray()['spesifikasi'] ?? '{}', true);
            $spesifikasi['unit_id'] = $newUnitId;
            
            $this->db->table('spk')->where('id', $spkId)->update([
                'spesifikasi' => json_encode($spesifikasi)
            ]);

            // Log the change
            log_message('info', "SPK {$spkId} unit changed to {$newUnitId} for unit index {$unitIndex}. Reason: {$reason}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Unit berhasil diubah'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Change SPK Unit Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate and extract approval request data
     */
    private function validateAndExtractApprovalData()
    {
        $stage = $this->request->getPost('stage');
        $unitIndex = (int) $this->request->getPost('unit_index') ?: 1;
        $mekanik = trim($this->request->getPost('mekanik'));
        $estimasi_mulai = $this->request->getPost('estimasi_mulai');
        $estimasi_selesai = $this->request->getPost('estimasi_selesai');
        $attachment_id = $this->request->getPost('attachment_inventory_attachment_id');
        $transfer_attachment = $this->request->getPost('transfer_attachment') === 'true';

        // Extract multi-mechanic data
        $mechanicsDataJson = $this->request->getPost('mechanics_data');
        $mechanicsData = [];
        if ($mechanicsDataJson) {
            $mechanicsData = json_decode($mechanicsDataJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid mechanics data format');
            }
        }
        
        // Primary mechanic ID from multi-select
        $primaryMechanicId = $this->request->getPost('primary_mechanic_id');

        // Basic validation - check if we have mechanics data
        if (empty($mechanicsData) && !$mekanik) {
            throw new \Exception('At least one mechanic must be selected');
        }

        // If no legacy mechanic name but have mechanics data, use primary mechanic name
        if (!$mekanik && !empty($mechanicsData)) {
            $primaryMechanic = array_filter($mechanicsData, function($m) { return $m['isPrimary']; });
            $primaryMechanic = reset($primaryMechanic);
            if ($primaryMechanic) {
                $mekanik = $primaryMechanic['name'];
            }
        }

        // Stage-specific validation
        if (in_array($stage, ['fabrikasi', 'painting']) && (!$estimasi_mulai || !$estimasi_selesai)) {
            throw new \Exception('Estimasi mulai dan estimasi selesai harus diisi untuk stage ' . $stage);
        }

        // Validate allowed stages
        $allowedStages = ['persiapan_unit', 'fabrikasi', 'painting', 'pdi'];
        if (!in_array($stage, $allowedStages)) {
            throw new \Exception('Stage tidak valid');
        }

        return [
            'stage' => $stage,
            'unitIndex' => $unitIndex,
            'mekanik' => $mekanik, // Keep for legacy compatibility
            'mechanics_data' => $mechanicsData,
            'primary_mechanic_id' => $primaryMechanicId,
            'estimasi_mulai' => $estimasi_mulai,
            'estimasi_selesai' => $estimasi_selesai,
            'attachment_id' => $attachment_id,
            'transfer_attachment' => $transfer_attachment
        ];
    }

    /**
     * Prepare base stage data for approval
     */
    private function prepareBaseStageData($id, $approvalData)
    {
        $baseData = [
            'spk_id' => $id,
            'unit_index' => $approvalData['unitIndex'],
            'stage_name' => $approvalData['stage'],
            'mekanik' => $approvalData['mekanik'], // Keep for backwards compatibility
            'estimasi_mulai' => $approvalData['estimasi_mulai'],
            'estimasi_selesai' => $approvalData['estimasi_selesai'],
            'tanggal_approve' => date('Y-m-d H:i:s')
        ];
        
        // Add multi-mechanic data if available
        if (!empty($approvalData['mechanics_data'])) {
            $baseData['mechanics_json'] = json_encode($approvalData['mechanics_data']);
            $baseData['primary_mechanic_id'] = $approvalData['primary_mechanic_id'];
            $baseData['mechanics_count'] = count($approvalData['mechanics_data']);
        }
        
        return $baseData;
    }

    /**
     * Handle stage-specific data processing
     */
    private function handleStageSpecificData($stage, &$stageData, $approvalData)
    {
        switch ($stage) {
            case 'persiapan_unit':
                $this->handlePersiapanUnitStage($stageData, $approvalData);
                break;
            case 'pdi':
                $this->handlePdiStage($stageData, $approvalData);
                break;
            default:
                // For fabrikasi and painting stages, no additional data needed
                break;
        }
    }

    /**
     * Handle persiapan unit stage specific data
     */
    private function handlePersiapanUnitStage(&$stageData, $approvalData)
    {
        $unit_id = $this->request->getPost('unit_id');
        $area_id = $this->request->getPost('area_id');
        $aksesoris_tersedia = $this->request->getPost('aksesoris_tersedia');
        $battery_id = $this->request->getPost('battery_inventory_attachment_id');
        $charger_id = $this->request->getPost('charger_inventory_attachment_id');
        $no_unit_action = $this->request->getPost('no_unit_action');
        $update_no_unit = $this->request->getPost('update_no_unit');
        
        // Debug logging
        log_message('info', "SPK Approval Debug - Unit: $unit_id, Battery: $battery_id, Charger: $charger_id");
        log_message('info', "SPK Approval Debug - All POST data: " . json_encode($this->request->getPost()));
        
        // Validation
        if (!$unit_id || !$area_id) {
            throw new \Exception('Unit dan Area harus dipilih');
        }
        
        // Update stage data
        $stageData['unit_id'] = $unit_id;
        $stageData['area_id'] = $area_id;
        $stageData['aksesoris_tersedia'] = is_array($aksesoris_tersedia) ? json_encode($aksesoris_tersedia) : $aksesoris_tersedia;
        $stageData['battery_inventory_attachment_id'] = $battery_id ?: null;
        $stageData['charger_inventory_attachment_id'] = $charger_id ?: null;
        $stageData['no_unit_action'] = $no_unit_action;
        $stageData['update_no_unit'] = $update_no_unit;
        
        // Update inventory_unit
        $this->updateInventoryUnit($unit_id, $area_id, $no_unit_action, $update_no_unit);
        
        // Handle component attachments with SPK context for audit logging
        $this->handleComponentAttachments($unit_id, $battery_id, $charger_id, $stageData['spk_id'], 'persiapan_unit');
    }

    /**
     * Handle PDI stage specific data
     */
    private function handlePdiStage(&$stageData, $approvalData)
    {
        $catatan = $this->request->getPost('catatan');
        if (!$catatan) {
            throw new \Exception('Catatan PDI harus diisi');
        }
        
        $stageData['catatan'] = $catatan;
        
        // Check if this is ATTACHMENT SPK (skip persiapan unit)
        $spkData = $this->db->table('spk')
            ->where('id', $stageData['spk_id'])
            ->get()
            ->getRowArray();
            
        if ($spkData && $spkData['jenis_spk'] === 'ATTACHMENT') {
            // For ATTACHMENT SPK, get target_unit_id from spesifikasi field
            if (isset($spkData['spesifikasi'])) {
                $spesifikasi = json_decode($spkData['spesifikasi'], true);
                if (isset($spesifikasi['target_unit_id'])) {
                    $stageData['unit_id'] = $spesifikasi['target_unit_id'];
                    log_message('info', "ATTACHMENT SPK #{$stageData['spk_id']} PDI - Target unit: {$stageData['unit_id']}");
                } elseif (isset($spesifikasi['unit_id'])) {
                    // Fallback for old data
                    $stageData['unit_id'] = $spesifikasi['unit_id'];
                    log_message('info', "ATTACHMENT SPK #{$stageData['spk_id']} PDI - Using fallback unit_id: {$stageData['unit_id']}");
                } else {
                    throw new \Exception('Target Unit ID tidak ditemukan untuk SPK ATTACHMENT');
                }
            } else {
                throw new \Exception('Data spesifikasi tidak ditemukan untuk SPK ATTACHMENT');
            }
        } else {
            // For UNIT SPK, get unit_id from persiapan stage
            $persiapanStage = $this->getPersiapanStage($stageData['spk_id'], $stageData['unit_index']);
            if (!$persiapanStage || !$persiapanStage['unit_id']) {
                throw new \Exception('Data persiapan unit tidak ditemukan');
            }
            
            $stageData['unit_id'] = $persiapanStage['unit_id'];
        }
        
        // Update unit status to READY_TO_DELIVER
        // Check permission: Service perlu manage inventory (cross-division)
        // Service Head/Staff punya: warehouse.inventory.manage (resource permission)
        if (!$this->canManage('warehouse') && !$this->canManageResource('warehouse', 'inventory')) {
            throw new \Exception('Access denied: You do not have permission to update inventory');
        }
        
        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $stageData['unit_id'])
            ->update(['status_unit_id' => 5, 'updated_at' => date('Y-m-d H:i:s')]);
        
        // Check if all units are completed
        $this->checkAndUpdateSpkStatus($stageData['spk_id']);
    }

    /**
     * Update inventory unit data
     * Service perlu manage inventory (cross-division) - warehouse.inventory.manage
     */
    private function updateInventoryUnit($unit_id, $area_id, $no_unit_action, $update_no_unit)
    {
        // Check permission: Service perlu manage inventory (cross-division)
        // Service Head/Staff punya: warehouse.inventory.manage (resource permission)
        if (!$this->canManage('warehouse') && !$this->canManageResource('warehouse', 'inventory')) {
            log_message('error', 'Service::updateInventoryUnit - Access denied for user: ' . session()->get('user_id'));
            throw new \Exception('Access denied: You do not have permission to update inventory');
        }
        $updateData = [
            'area_id' => $area_id, 
            'status_unit_id' => 4, // IN_PREPARATION
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Handle no_unit update if requested
        if ($update_no_unit === 'true' && $no_unit_action) {
            if ($no_unit_action === 'AUTO_GENERATE') {
                // Auto-generate no_unit: ambil max no_unit + 1
                $maxNoUnit = $this->db->table('inventory_unit')
                    ->selectMax('no_unit')
                    ->get()
                    ->getRowArray();
                
                $newNoUnit = ($maxNoUnit['no_unit'] ?? 0) + 1;
                $updateData['no_unit'] = $newNoUnit;
                
                log_message('info', "Auto-generated no_unit: $newNoUnit for unit: $unit_id");
            } else {
                // Manual no_unit
                $updateData['no_unit'] = (int)$no_unit_action;
                log_message('info', "Manual no_unit: {$no_unit_action} for unit: $unit_id");
            }
        }
        
        $this->db->table('inventory_unit')
            ->where('id_inventory_unit', $unit_id)
            ->update($updateData);
    }

    /**
     * Handle component attachments (battery & charger)
     */
    private function handleComponentAttachments($unit_id, $battery_id, $charger_id, $spk_id = null, $stage_name = 'persiapan_unit')
    {
        // Handle enhanced component data (battery & charger replacement)
        $enhancedComponentData = $this->request->getPost('enhanced_component_data');
        if ($enhancedComponentData) {
            $this->processEnhancedComponentData($enhancedComponentData, $unit_id, $spk_id, $stage_name);
        } else {
            // Fallback: Legacy single field approach
            $this->processLegacyComponentData($unit_id, $battery_id, $charger_id, $spk_id, $stage_name);
        }
    }

    /**
     * Process enhanced component data for replacements
     */
    private function processEnhancedComponentData($enhancedComponentData, $unit_id, $spk_id = null, $stage_name = 'persiapan_unit')
    {
        $componentData = json_decode($enhancedComponentData, true);
        
        // Handle array of units or single unit object
        $units = is_array($componentData) && isset($componentData[0]) ? $componentData : [$componentData];
        
        foreach ($units as $unitComponentData) {
            if (isset($unitComponentData['components'])) {
                $this->processUnitComponents($unitComponentData['components'], $unit_id, $spk_id, $stage_name);
            }
        }
    }

    /**
     * Process unit components (battery/charger)
     */
    private function processUnitComponents($components, $unit_id, $spk_id = null, $stage_name = 'persiapan_unit')
    {
        // Handle Battery
        if (isset($components['battery'])) {
            $batteryComp = $components['battery'];
            $this->handleComponentReplacement($batteryComp, $unit_id, 'battery', $spk_id, $stage_name);
        }
        
        // Handle Charger
        if (isset($components['charger'])) {
            $chargerComp = $components['charger'];
            $this->handleComponentReplacement($chargerComp, $unit_id, 'charger', $spk_id, $stage_name);
        }
    }

    /**
     * Handle component replacement (battery or charger)
     */
    private function handleComponentReplacement($componentData, $unit_id, $type, $spk_id = null, $stage_name = 'persiapan_unit')
    {
        $old_unit_id = null;
        
        // If action is 'replace', detach old component first
        if ($componentData['action'] === 'replace' && !empty($componentData['existing_model_id'])) {
            // Get old unit_id before detaching for audit log
            $oldAttachment = $this->db->table('inventory_attachment')
                ->where('id_inventory_attachment', $componentData['existing_model_id'])
                ->get()->getRowArray();
            
            $old_unit_id = $oldAttachment['id_inventory_unit'] ?? null;
            
            $this->db->table('inventory_attachment')
                ->where('id_inventory_attachment', $componentData['existing_model_id'])
                ->update([
                    'id_inventory_unit' => null,
                    'attachment_status' => 'AVAILABLE',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            log_message('info', "Component {$type} ID {$componentData['existing_model_id']} detached from unit {$old_unit_id}");
        }
        
        // Attach new component
        if (!empty($componentData['new_inventory_attachment_id'])) {
            $this->db->table('inventory_attachment')
                ->where('id_inventory_attachment', $componentData['new_inventory_attachment_id'])
                ->update([
                    'id_inventory_unit' => $unit_id,
                    'attachment_status' => 'USED',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            // Log to audit table
            $transferType = ($componentData['action'] === 'replace' && $old_unit_id) ? 'TRANSFER' : 'NEW_ASSIGNMENT';
            $triggeredBy = $transferType === 'TRANSFER' ? 'KANIBAL_PERSIAPAN_UNIT' : 'PERSIAPAN_UNIT';
            
            $this->db->table('attachment_transfer_log')->insert([
                'attachment_id' => $componentData['new_inventory_attachment_id'],
                'from_unit_id' => $old_unit_id,
                'to_unit_id' => $unit_id,
                'transfer_type' => $transferType,
                'triggered_by' => $triggeredBy,
                'spk_id' => $spk_id,
                'stage_name' => $stage_name,
                'notes' => ucfirst($type) . ' ' . ($transferType === 'TRANSFER' ? 'transferred' : 'assigned'),
                'created_by' => session('user_id') ?? 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            log_message('info', "Component {$type} ID {$componentData['new_inventory_attachment_id']} {$transferType} to unit {$unit_id}");
        }
    }

    /**
     * Process legacy component data (single fields)
     */
    private function processLegacyComponentData($unit_id, $battery_id, $charger_id, $spk_id = null, $stage_name = 'persiapan_unit')
    {
        // Update battery attachment
        if ($battery_id) {
            $this->db->table('inventory_attachment')
                ->where('id_inventory_attachment', $battery_id)
                ->update([
                    'id_inventory_unit' => $unit_id, 
                    'attachment_status' => 'USED', 
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            // Log to audit table
            $this->db->table('attachment_transfer_log')->insert([
                'attachment_id' => $battery_id,
                'from_unit_id' => null,
                'to_unit_id' => $unit_id,
                'transfer_type' => 'NEW_ASSIGNMENT',
                'triggered_by' => 'PERSIAPAN_UNIT',
                'spk_id' => $spk_id,
                'stage_name' => $stage_name,
                'notes' => 'Battery assigned (legacy method)',
                'created_by' => session('user_id') ?? 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Update charger attachment
        if ($charger_id) {
            $this->db->table('inventory_attachment')
                ->where('id_inventory_attachment', $charger_id)
                ->update([
                    'id_inventory_unit' => $unit_id, 
                    'attachment_status' => 'USED', 
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            // Log to audit table
            $this->db->table('attachment_transfer_log')->insert([
                'attachment_id' => $charger_id,
                'from_unit_id' => null,
                'to_unit_id' => $unit_id,
                'transfer_type' => 'NEW_ASSIGNMENT',
                'triggered_by' => 'PERSIAPAN_UNIT',
                'spk_id' => $spk_id,
                'stage_name' => $stage_name,
                'notes' => 'Charger assigned (legacy method)',
                'created_by' => session('user_id') ?? 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Get persiapan unit stage data
     */
    private function getPersiapanStage($spk_id, $unit_index)
    {
        // First try to get from spk_unit_stages table
        $persiapanStage = $this->db->table('spk_unit_stages')
            ->where('spk_id', $spk_id)
            ->where('unit_index', $unit_index)
            ->where('stage_name', 'persiapan_unit')
            ->get()
            ->getRowArray();
            
        if ($persiapanStage) {
            return $persiapanStage;
        }
        
        // If not found, try to get from SPK table directly (fallback)
        $spkData = $this->db->table('spk')
            ->where('id', $spk_id)
            ->get()
            ->getRowArray();
            
        if ($spkData && isset($spkData['spesifikasi'])) {
            $spesifikasi = json_decode($spkData['spesifikasi'], true);
            if (isset($spesifikasi['unit_id'])) {
                return [
                    'unit_id' => $spesifikasi['unit_id'],
                    'spk_id' => $spk_id,
                    'unit_index' => $unit_index,
                    'stage_name' => 'persiapan_unit'
                ];
            }
        }
        
        return null;
    }

    /**
     * Save stage approval data
     */
    private function saveStageApproval($stageData, $approvalData)
    {
        $this->db->transStart();
        
        try {
            // Handle stage-specific logic for fabrikasi, painting stages
            if (in_array($approvalData['stage'], ['fabrikasi', 'painting'])) {
                $this->handleProductionStage($stageData, $approvalData);
            }
            
            // Insert or update stage data
            $existingStage = $this->db->table('spk_unit_stages')
                ->where('spk_id', $stageData['spk_id'])
                ->where('unit_index', $stageData['unit_index'])
                ->where('stage_name', $stageData['stage_name'])
                ->get()
                ->getRowArray();
            
            log_message('info', 'Stage data to save: ' . json_encode($stageData));
            log_message('info', 'Existing stage: ' . json_encode($existingStage));
            
            if ($existingStage) {
                $this->db->table('spk_unit_stages')
                    ->where('id', $existingStage['id'])
                    ->update($stageData);
                log_message('info', 'Updated existing stage with ID: ' . $existingStage['id']);
            } else {
                $this->db->table('spk_unit_stages')->insert($stageData);
                log_message('info', 'Inserted new stage data');
            }
            
            // Save individual mechanic assignments if we have multi-mechanic data
            if (!empty($approvalData['mechanics_data'])) {
                $this->saveMechanicAssignments($stageData, $approvalData);
            }
            
            $this->db->transComplete();
            
            // Handle attachment updates for fabrikasi stage
            if ($approvalData['stage'] === 'fabrikasi' && $approvalData['attachment_id']) {
                $this->handleFabrikasiAttachment($stageData, $approvalData);
            }
            
            // Send notification: Attachment Uploaded on Stage
            if (!empty($approvalData['attachment_id']) && in_array($approvalData['stage'], ['fabrikasi', 'painting', 'persiapan_unit', 'pdi'])) {
                helper('notification');
                
                // Get SPK data
                $spk = $this->db->table('spk')->where('id', $stageData['spk_id'])->get()->getRowArray();
                
                notify_attachment_uploaded([
                    'id' => $approvalData['attachment_id'],
                    'stage_name' => $approvalData['stage'],
                    'spk_number' => $spk['nomor_spk'] ?? '',
                    'unit_code' => '',
                    'file_name' => 'Attachment for ' . $approvalData['stage'],
                    'uploaded_by' => session()->get('username') ?? 'System',
                    'url' => base_url('/service/spk_service')
                ]);
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
    
    /**
     * Save individual mechanic assignments to spk_stage_mechanics table
     */
    private function saveMechanicAssignments($stageData, $approvalData)
    {
        $spkId = $stageData['spk_id'];
        $unitIndex = $stageData['unit_index'];
        $stageName = $stageData['stage_name'];
        
        // Delete existing assignments for this stage
        $this->db->table('spk_stage_mechanics')
            ->where('spk_id', $spkId)
            ->where('unit_index', $unitIndex)
            ->where('stage_name', $stageName)
            ->delete();
        
        // Insert new assignments
        foreach ($approvalData['mechanics_data'] as $mechanic) {
            $assignmentData = [
                'spk_id' => $spkId,
                'unit_index' => $unitIndex,
                'stage_name' => $stageName,
                'employee_id' => $mechanic['id'],
                'employee_role' => $mechanic['role'],
                'is_primary' => $mechanic['isPrimary'] ? 1 : 0,
                'assigned_at' => date('Y-m-d H:i:s'),
                'assigned_by' => session()->get('user_id') ?? 1
            ];
            
            $this->db->table('spk_stage_mechanics')->insert($assignmentData);
        }
        
        log_message('info', 'Saved ' . count($approvalData['mechanics_data']) . ' mechanic assignments for SPK ' . $spkId);
    }

    /**
     * Handle production stages (fabrikasi, painting)
     */
    private function handleProductionStage(&$stageData, $approvalData)
    {
        // Check if this is ATTACHMENT SPK (skip persiapan unit)
        $spkData = $this->db->table('spk')
            ->where('id', $stageData['spk_id'])
            ->get()
            ->getRowArray();
            
        if ($spkData && $spkData['jenis_spk'] === 'ATTACHMENT') {
            // For ATTACHMENT SPK, get target_unit_id from spesifikasi field
            if (isset($spkData['spesifikasi'])) {
                $spesifikasi = json_decode($spkData['spesifikasi'], true);
                if (isset($spesifikasi['target_unit_id'])) {
                    $stageData['unit_id'] = $spesifikasi['target_unit_id'];
                    log_message('info', "ATTACHMENT SPK #{$stageData['spk_id']} - Target unit: {$stageData['unit_id']} ({$spesifikasi['target_unit_sn']})");
                } elseif (isset($spesifikasi['unit_id'])) {
                    // Fallback for old data
                    $stageData['unit_id'] = $spesifikasi['unit_id'];
                    log_message('info', "ATTACHMENT SPK #{$stageData['spk_id']} - Using fallback unit_id: {$stageData['unit_id']}");
                } else {
                    throw new \Exception('Target Unit ID tidak ditemukan untuk SPK ATTACHMENT. Pastikan unit tujuan sudah dipilih saat pembuatan SPK.');
                }
            } else {
                throw new \Exception('Data spesifikasi tidak ditemukan untuk SPK ATTACHMENT');
            }
        } else {
            // For UNIT SPK, get unit_id from persiapan stage
            $persiapanStage = $this->getPersiapanStage($stageData['spk_id'], $stageData['unit_index']);
            
            if (!$persiapanStage || !$persiapanStage['unit_id']) {
                throw new \Exception('Data persiapan unit tidak ditemukan');
            }
            
            $stageData['unit_id'] = $persiapanStage['unit_id'];
        }
        
        // Handle fabrikasi specific logic
        if ($approvalData['stage'] === 'fabrikasi') {
            $this->validateFabrikasiAttachment($stageData, $approvalData);
        }
    }

    /**
     * Validate fabrikasi attachment
     */
    private function validateFabrikasiAttachment($stageData, $approvalData)
    {
        log_message('info', 'Fabrikasi stage data: attachment_id=' . $approvalData['attachment_id'] . ', transfer_attachment=' . ($approvalData['transfer_attachment'] ? 'true' : 'false'));
        
        // Debug: Check if attachment exists and is valid
        if ($approvalData['attachment_id']) {
            $attachmentCheck = $this->db->table('inventory_attachment')
                ->where('id_inventory_attachment', $approvalData['attachment_id'])
                ->get()
                ->getRowArray();
            
            if (!$attachmentCheck) {
                log_message('error', 'Attachment not found: ' . $approvalData['attachment_id']);
                throw new \Exception('Attachment tidak ditemukan');
            }
            
            log_message('info', 'Attachment found: ' . json_encode($attachmentCheck));
        }
        
        // Check if attachment is required (only if not editing existing stage)
        $existingStage = $this->db->table('spk_unit_stages')
            ->where('spk_id', $stageData['spk_id'])
            ->where('unit_index', $stageData['unit_index'])
            ->where('stage_name', 'fabrikasi')
            ->get()
            ->getRowArray();
        
        if (!$existingStage && !$approvalData['attachment_id']) {
            throw new \Exception('Attachment harus dipilih');
        }
        
        if ($approvalData['attachment_id']) {
            $stageData['attachment_inventory_attachment_id'] = $approvalData['attachment_id'];
        }
    }

    /**
     * Handle fabrikasi attachment update
     */
    private function handleFabrikasiAttachment($stageData, $approvalData)
    {
        // Get unit_id from persiapan stage for immediate attachment update
        $persiapanStage = $this->getPersiapanStage($stageData['spk_id'], $stageData['unit_index']);
        
        if ($persiapanStage && $persiapanStage['unit_id']) {
            try {
                log_message('info', "=== BACKGROUND ATTACHMENT UPDATE ===");
                log_message('info', "Attachment ID: {$approvalData['attachment_id']}");
                log_message('info', "Target Unit ID: {$persiapanStage['unit_id']}");
                log_message('info', "Transfer Mode: " . ($approvalData['transfer_attachment'] ? 'KANIBAL' : 'NORMAL'));
                log_message('info', "SPK ID: {$stageData['spk_id']}");
                log_message('info', "Stage Name: {$approvalData['stage']}");
                
                // Create and execute background attachment update with audit info
                $this->executeBackgroundAttachmentUpdate(
                    $approvalData['attachment_id'], 
                    $persiapanStage['unit_id'], 
                    $approvalData['transfer_attachment'],
                    $stageData['spk_id'],
                    $approvalData['stage']
                );
                
            } catch (\Exception $e) {
                log_message('error', 'Background attachment update failed: ' . $e->getMessage());
                // Don't throw exception as main approval already succeeded
            }
        }
    }

    /**
     * Execute background attachment update
     */
    private function executeBackgroundAttachmentUpdate($attachment_id, $unit_id, $transfer_attachment, $spk_id = null, $stage_name = 'fabrikasi')
    {
        // Get database config for script generation
        $db = \Config\Database::connect();
        $dbConfig = $db->getDatabase();
        
        // Create background update script
        $updateScript = WRITEPATH . 'update_attachment_' . $attachment_id . '_' . time() . '.php';
        
        // Build script content with proper escaping
        $hostname = $db->hostname;
        $username = $db->username;
        $password = $db->password;
        $database = $dbConfig;
        $transferModeStr = $transfer_attachment ? 'true' : 'false';
        
        $scriptContent = <<<'EOF'
<?php
// Background attachment update script
$attachment_id = %ATTACHMENT_ID%;
$unit_id = %UNIT_ID%;
$transfer_mode = %TRANSFER_MODE%;

// Wait 5 seconds to ensure main transaction is complete
sleep(5);

// Database connection
$mysqli = new mysqli('%HOSTNAME%', '%USERNAME%', '%PASSWORD%', '%DATABASE%');

if ($mysqli->connect_error) {
    error_log('Background update connection failed: ' . $mysqli->connect_error);
    exit(1);
}

$mysqli->begin_transaction();

try {
    if ($transfer_mode) {
        // KANIBAL MODE: Two-step update for proper detach → attach workflow
        
        // Get old unit_id before detaching (for audit log)
        $getOldUnit = $mysqli->query("SELECT id_inventory_unit FROM inventory_attachment WHERE id_inventory_attachment = $attachment_id");
        $oldUnitData = $getOldUnit->fetch_assoc();
        $old_unit_id = $oldUnitData['id_inventory_unit'] ?? null;
        
        // STEP 1: Detach from old unit
        $sql1 = "UPDATE inventory_attachment 
                 SET id_inventory_unit = NULL, 
                     updated_at = '" . date('Y-m-d H:i:s') . "' 
                 WHERE id_inventory_attachment = $attachment_id";
        
        $result1 = $mysqli->query($sql1);
        $affected1 = $mysqli->affected_rows;
        
        if (!$result1) {
            throw new Exception('KANIBAL STEP 1 FAILED: Detach failed - ' . $mysqli->error);
        }
        
        error_log('✅ KANIBAL STEP 1 SUCCESS: Attachment ' . $attachment_id . ' detached from unit ' . ($old_unit_id ?? 'NULL') . ' (affected: ' . $affected1 . ')');
        
        // Wait 1 second to ensure trigger completes
        sleep(1);
        
        // STEP 2: Attach to new unit
        $sql2 = "UPDATE inventory_attachment 
                 SET id_inventory_unit = $unit_id, 
                     updated_at = '" . date('Y-m-d H:i:s') . "' 
                 WHERE id_inventory_attachment = $attachment_id";
        
        $result2 = $mysqli->query($sql2);
        $affected2 = $mysqli->affected_rows;
        
        if (!$result2) {
            throw new Exception('KANIBAL STEP 2 FAILED: Attach failed - ' . $mysqli->error);
        }
        
        error_log('✅ KANIBAL STEP 2 SUCCESS: Attachment ' . $attachment_id . ' attached to unit ' . $unit_id . ' (affected: ' . $affected2 . ')');
        
        // Insert audit log
        $spk_id = %SPK_ID%;
        $stage_name = '%STAGE_NAME%';
        $created_by = %CREATED_BY%;
        
        $auditSql = "INSERT INTO attachment_transfer_log 
                     (attachment_id, from_unit_id, to_unit_id, transfer_type, triggered_by, spk_id, stage_name, created_by, created_at) 
                     VALUES 
                     ($attachment_id, " . ($old_unit_id ? $old_unit_id : 'NULL') . ", $unit_id, 'TRANSFER', 'KANIBAL_FABRIKASI', $spk_id, '$stage_name', $created_by, '" . date('Y-m-d H:i:s') . "')";
        
        $mysqli->query($auditSql);
        error_log('📝 AUDIT LOG: Transfer logged from unit ' . ($old_unit_id ?? 'NULL') . ' to unit ' . $unit_id);
        
    } else {
        // NORMAL MODE: Direct assignment (new attachment from warehouse)
        $sql = "UPDATE inventory_attachment 
                SET id_inventory_unit = $unit_id, 
                    updated_at = '" . date('Y-m-d H:i:s') . "' 
                WHERE id_inventory_attachment = $attachment_id";
        
        $result = $mysqli->query($sql);
        $affected_rows = $mysqli->affected_rows;
        
        if (!$result) {
            throw new Exception('NORMAL MODE FAILED: Assignment failed - ' . $mysqli->error);
        }
        
        error_log('✅ NORMAL MODE SUCCESS: Attachment ' . $attachment_id . ' assigned to unit ' . $unit_id . ' (affected rows: ' . $affected_rows . ')');
        
        // Insert audit log
        $spk_id = %SPK_ID%;
        $stage_name = '%STAGE_NAME%';
        $created_by = %CREATED_BY%;
        
        $auditSql = "INSERT INTO attachment_transfer_log 
                     (attachment_id, from_unit_id, to_unit_id, transfer_type, triggered_by, spk_id, stage_name, created_by, created_at) 
                     VALUES 
                     ($attachment_id, NULL, $unit_id, 'NEW_ASSIGNMENT', 'FABRIKASI', $spk_id, '$stage_name', $created_by, '" . date('Y-m-d H:i:s') . "')";
        
        $mysqli->query($auditSql);
        error_log('📝 AUDIT LOG: New assignment logged to unit ' . $unit_id);
    }
    
    $mysqli->commit();
    error_log('✅ TRANSACTION COMMITTED: All updates successful');
    
} catch (Exception $e) {
    $mysqli->rollback();
    error_log('❌ TRANSACTION ROLLBACK: ' . $e->getMessage());
    exit(1);
}

$mysqli->close();

// Clean up script
unlink(__FILE__);
?>
EOF;
        
        // Replace placeholders
        $scriptContent = str_replace([
            '%ATTACHMENT_ID%',
            '%UNIT_ID%',
            '%TRANSFER_MODE%',
            '%SPK_ID%',
            '%STAGE_NAME%',
            '%CREATED_BY%',
            '%HOSTNAME%',
            '%USERNAME%',
            '%PASSWORD%',
            '%DATABASE%'
        ], [
            $attachment_id,
            $unit_id,
            $transferModeStr,
            $spk_id ?? 0,
            $stage_name,
            session('user_id') ?? 1,
            $hostname,
            $username,
            $password,
            $database
        ], $scriptContent);
        
        file_put_contents($updateScript, $scriptContent);
        
        // Execute background script
        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen('start /B php ' . $updateScript, 'r'));
        } else {
            exec('php ' . $updateScript . ' > /dev/null 2>&1 &');
        }
        
        log_message('info', "🚀 BACKGROUND UPDATE STARTED: Script created and executed");
    }
    
    // Method untuk mendapatkan stage status data (untuk internal use)
    private function getSpkStageStatusData($spkId) {
        $spkId = (int) $spkId;

        // Get SPK basic info
        $spk = $this->db->table('spk')
            ->where('id', $spkId)
            ->get()
            ->getRowArray();

        if (!$spk) {
            return null;
        }

        // Get stage status for each unit
        $stages = $this->db->table('spk_unit_stages sus')
            ->select('sus.unit_index, sus.stage_name, sus.tanggal_approve, sus.mekanik')
            ->where('sus.spk_id', $spkId)
            ->orderBy('sus.unit_index, sus.stage_name')
            ->get()
            ->getResultArray();

        // Organize by unit
        $unitStages = [];
        foreach ($stages as $stage) {
            $unitIndex = $stage['unit_index'];
            if (!isset($unitStages[$unitIndex])) {
                $unitStages[$unitIndex] = [];
            }
            $unitStages[$unitIndex][$stage['stage_name']] = [
                'completed' => !empty($stage['tanggal_approve']),
                'tanggal_approve' => $stage['tanggal_approve'],
                'mekanik' => $stage['mekanik']
            ];
        }

        return [
            'spk' => $spk,
            'unit_stages' => $unitStages
        ];
    }
    
    /**
     * Get SPK edit options for multi-unit SPK editing
     */
    public function getSpkEditOptions($spkId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        $spkId = (int) $spkId;
        
        // Get SPK basic info
        $spk = $this->db->table('spk')
            ->where('id', $spkId)
            ->get()
            ->getRowArray();
        
        if (!$spk) {
            return $this->response->setJSON(['success' => false, 'message' => 'SPK tidak ditemukan']);
        }

        // Get stage status data
        $stageStatus = $this->getSpkStageStatusData($spkId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'spk' => $spk,
                'stage_status' => $stageStatus
            ]
        ]);
    }
    
    private function checkAndUpdateSpkStatus($spkId) {
        try {
            // Check if all units have completed PDI
            $spk = $this->db->table('spk')->where('id', $spkId)->get()->getRowArray();
            if (!$spk) return;
            
            $totalUnits = (int) $spk['jumlah_unit'];
            $completedUnits = $this->db->table('spk_unit_stages')
                ->where('spk_id', $spkId)
                ->where('stage_name', 'pdi')
                ->where('tanggal_approve IS NOT NULL')
                ->countAllResults();
            
            log_message('info', "SPK {$spkId}: Total units: {$totalUnits}, Completed PDI: {$completedUnits}");
            
            if ($completedUnits >= $totalUnits) {
                $this->db->table('spk')
                    ->where('id', $spkId)
                    ->update(['status' => 'READY', 'diperbarui_pada' => date('Y-m-d H:i:s')]);
                log_message('info', "SPK {$spkId}: Status updated to READY");
                
                // Send notification to Operational division
                helper('notification');
                if (function_exists('notify_spk_ready')) {
                    notify_spk_ready([
                        'id' => $spkId,
                        'nomor_spk' => $spk['nomor_spk'] ?? '',
                        'pelanggan' => $spk['nama_customer'] ?? $spk['pelanggan'] ?? '',
                        'jumlah_unit' => $totalUnits,
                        'no_unit' => $spk['no_unit'] ?? '',
                        'departemen' => 'Service',
                        'url' => base_url('/operational/spk/detail/' . $spkId)
                    ]);
                }
            }
        } catch (\Exception $e) {
            log_message('error', "SPK Status Update Error: " . $e->getMessage());
        }
    }

    // =====================================================
    // METHODS BARU UNTUK SPK_UNIT_STAGES
    // =====================================================

    // Method untuk mendapatkan SPK units dengan data edit
    public function getSpkUnitsWithEdit($spkId) {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        $spkId = (int) $spkId;

        // Get SPK basic info
        $spk = $this->db->table('spk')
            ->select('id, nomor_spk, status, jumlah_unit')
            ->where('id', $spkId)
            ->get()
            ->getRowArray();

        if (!$spk) {
            return $this->response->setJSON(['success' => false, 'message' => 'SPK tidak ditemukan']);
        }

        // Get units with their stages
        $units = [];
        for ($unitIndex = 1; $unitIndex <= $spk['jumlah_unit']; $unitIndex++) {
            // Get unit info from persiapan stage
            $persiapanStage = $this->db->table('spk_unit_stages sus')
                ->select('sus.unit_id, sus.area_id, sus.aksesoris_tersedia, sus.battery_inventory_attachment_id, sus.charger_inventory_attachment_id')
                ->where('sus.spk_id', $spkId)
                ->where('sus.unit_index', $unitIndex)
                ->where('sus.stage_name', 'persiapan_unit')
                ->get()
                ->getRowArray();

            $unitInfo = null;
            if ($persiapanStage && $persiapanStage['unit_id']) {
                $unitInfo = $this->db->table('inventory_unit iu')
                    ->select('iu.*, mu.merk_unit, mu.model_unit, a.area_name')
                    ->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left')
                    ->join('areas a', 'iu.area_id = a.id', 'left')
                    ->where('iu.id_inventory_unit', $persiapanStage['unit_id'])
                    ->get()
                    ->getRowArray();
            }

            // Get all stages for this unit
            $stages = $this->db->table('spk_unit_stages sus')
                ->select('sus.stage_name, sus.tanggal_approve, sus.mekanik, sus.catatan, sus.attachment_inventory_attachment_id')
                ->where('sus.spk_id', $spkId)
                ->where('sus.unit_index', $unitIndex)
                ->orderBy('sus.stage_name')
                ->get()
                ->getResultArray();

            // Organize stage status
            $stageStatus = [];
            $stageOrder = ['persiapan_unit', 'fabrikasi', 'painting', 'pdi'];
            foreach ($stageOrder as $stageName) {
                $stageData = array_filter($stages, function($s) use ($stageName) {
                    return $s['stage_name'] === $stageName;
                });
                $stageData = reset($stageData);
                $stageStatus[$stageName] = [
                    'completed' => !empty($stageData['tanggal_approve']),
                    'mekanik' => $stageData['mekanik'] ?? null,
                    'catatan' => $stageData['catatan'] ?? null,
                    'tanggal_approve' => $stageData['tanggal_approve'] ?? null,
                    'attachment_inventory_attachment_id' => $stageData['attachment_inventory_attachment_id'] ?? null
                ];
            }

            $units[] = [
                'unit_index' => $unitIndex,
                'unit_info' => $unitInfo,
                'persiapan_data' => $persiapanStage,
                'stages' => $stageStatus
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'spk' => $spk,
                'units' => $units
            ]
        ]);
    }

    // Method untuk mendapatkan stage status
    public function getSpkStageStatus($spkId) {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Bad request']);
        }

        $spkId = (int) $spkId;

        // Get SPK basic info
        $spk = $this->db->table('spk')
            ->where('id', $spkId)
            ->get()
            ->getRowArray();

        if (!$spk) {
            return $this->response->setJSON(['success' => false, 'message' => 'SPK tidak ditemukan']);
        }

        // Get stage status for each unit
        $stages = $this->db->table('spk_unit_stages sus')
            ->select('sus.unit_index, sus.stage_name, sus.tanggal_approve, sus.mekanik')
            ->where('sus.spk_id', $spkId)
            ->orderBy('sus.unit_index, sus.stage_name')
            ->get()
            ->getResultArray();

        // Organize by unit
        $unitStages = [];
        foreach ($stages as $stage) {
            $unitIndex = $stage['unit_index'];
            if (!isset($unitStages[$unitIndex])) {
                $unitStages[$unitIndex] = [];
            }
            $unitStages[$unitIndex][$stage['stage_name']] = [
                'completed' => !empty($stage['tanggal_approve']),
                'tanggal_approve' => $stage['tanggal_approve'],
                'mekanik' => $stage['mekanik']
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'spk' => $spk,
                'unit_stages' => $unitStages
            ]
        ]);
    }

    /** Simple list for unit picking (AVAILABLE_STOCK and STOCK_NON_ASET units: status 1 & 2) */
    public function dataUnitSimple()
    {
        $q = trim((string)($this->request->getGet('q') ?? ''));
        $excludeSpkId = trim((string)($this->request->getGet('exclude_spk_id') ?? ''));
        $spkDepartment = trim((string)($this->request->getGet('spk_department') ?? ''));
        
        $allowed = $this->getAllowedServiceDepartemenIds();
        if (!$allowed) { return $this->response->setJSON(['success'=>true,'data'=>[],'csrf_hash'=>csrf_hash()]); }
        
        // If SPK has specific department, filter units to match that department only
        if (!empty($spkDepartment)) {
            $departmentMap = [
                'ELECTRIC' => 2,
                'DIESEL' => 1, 
                'GASOLINE' => 3
            ];
            
            $spkDeptId = $departmentMap[strtoupper($spkDepartment)] ?? null;
            if ($spkDeptId && in_array($spkDeptId, $allowed)) {
                // Only show units that match SPK department
                $allowed = [$spkDeptId];
                log_message('info', "SPK Unit filtering: SPK requires {$spkDepartment} department, filtering to department ID: {$spkDeptId}");
            }
        }
        
        $qb = $this->serviceBaseQuery($allowed)
            ->select('iu.id_inventory_unit as id, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit, iu.status_unit_id')
            ->select('d.nama_departemen, iu.departemen_id, tu.tipe')
            ->whereIn('iu.status_unit_id', [1, 2]) // AVAILABLE_STOCK (1) and STOCK_NON_ASET (2)
            ->orderBy('iu.no_unit','ASC')
            ->limit(50);

        if ($q !== '') {
            $qb->groupStart()
                ->like('iu.no_unit', $q)
                ->orLike('iu.serial_number', $q)
                ->orLike('mu.merk_unit', $q)
                ->orLike('mu.model_unit', $q)
                ->orLike('iu.lokasi_unit', $q)
            ->groupEnd();
        }
        
        $rows = $qb->get()->getResultArray();
        
        // Get assigned units for the specified SPK if exclude_spk_id is provided
        $assignedUnits = [];
        if (!empty($excludeSpkId) && is_numeric($excludeSpkId)) {
            $assignedRows = $this->db->table('spk_unit_stages sus')
                ->select('sus.unit_id')
                ->where('sus.spk_id', $excludeSpkId)
                ->where('sus.unit_id IS NOT NULL')
                ->get()->getResultArray();
            $assignedUnits = array_column($assignedRows, 'unit_id');
        }
        
        $data = array_map(function($r) use ($assignedUnits) {
            // Check if unit needs no_unit (only for STOCK_NON_ASET - status 2)
            $needsNoUnit = ($r['status_unit_id'] == 2 && (empty($r['no_unit']) || $r['no_unit'] == 0));
            $noUnitDisplay = $r['no_unit'] ?: ($r['status_unit_id'] == 2 ? '[Non Aset]' : '[Akan di-generate]');
            
            // Check if this unit is already assigned to the current SPK
            $isAssignedInSpk = in_array($r['id'], $assignedUnits);
            
            // Format department display for the label
            $deptDisplay = '';
            if (!empty($r['nama_departemen'])) {
                $deptDisplay = ' - (' . $r['nama_departemen'] . ')';
            }
            
            // Create informative label with serial number and type
            $labelBase = $noUnitDisplay . $deptDisplay;
            $mainLabel = trim($labelBase." - ".($r['merk_unit']?:'-')." ".($r['model_unit']?:''));
            
            // Add serial number and type as additional info
            $serialParts = [];
            if (!empty($r['tipe'])) {
                $serialParts[] = $r['tipe'];
            }
            if (!empty($r['serial_number'])) {
                $serialParts[] = 'SN: ' . $r['serial_number'];
            } else {
                $serialParts[] = 'SN: -';
            }
            $serialInfo = implode(' | ', $serialParts);
            
            return [
                'id' => (int)$r['id'],
                'label' => $mainLabel,
                'serial_info' => $serialInfo,
                'no_unit' => $r['no_unit'],
                'serial_number' => $r['serial_number'],
                'merk_unit' => $r['merk_unit'],
                'model_unit' => $r['model_unit'],
                'tipe_unit' => $r['tipe'],
                'status_unit_id' => $r['status_unit_id'],
                'departemen_id' => $r['departemen_id'],
                'departemen_name' => $r['nama_departemen'],
                'needs_no_unit' => $needsNoUnit,
                'is_assigned_in_spk' => $isAssignedInSpk
            ];
        }, $rows);
        return $this->response->setJSON(['success'=>true,'data'=>$data,'csrf_hash'=>csrf_hash()]);
    }

    /** Simple list for attachment picking from inventory (statuses: AVAILABLE) - supports all types */
    public function dataAttachmentSimple()
    {
        $q = trim((string)($this->request->getGet('q') ?? ''));
        $type = trim((string)($this->request->getGet('type') ?? 'attachment'));
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        
        // Validate type
        if (!in_array($type, ['attachment', 'battery', 'charger'])) {
            $type = 'attachment';
        }
        
        $qb = $this->db->table('inventory_attachment ia')
            ->select('ia.id_inventory_attachment as id, ia.tipe_item, ia.sn_attachment, ia.sn_baterai, ia.sn_charger, ia.lokasi_penyimpanan, ia.kondisi_fisik, ia.kelengkapan, ia.id_inventory_unit, ia.attachment_status, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit')
            ->select('a.tipe as attachment_tipe, a.merk as attachment_merk, a.model as attachment_model')
            ->select('b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->select('c.merk_charger, c.tipe_charger')
            ->join('attachment a', 'a.id_attachment = ia.attachment_id', 'left')
            ->join('baterai b', 'b.id = ia.baterai_id', 'left')
            ->join('charger c', 'c.id_charger = ia.charger_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ia.id_inventory_unit', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->where('ia.tipe_item', $type);  // Filter by requested type
        
        // If no search query, prioritize AVAILABLE items first
        if (empty($q)) {
            $qb->whereIn('ia.attachment_status', ['AVAILABLE', 'USED'])
               ->orderBy("FIELD(ia.attachment_status, 'AVAILABLE', 'USED')", '', false) // AVAILABLE first
               ->orderBy('ia.id_inventory_attachment', 'DESC');
        } else {
            // With search query, show all matching items regardless of status
            $qb->whereIn('ia.attachment_status', ['AVAILABLE', 'USED', 'MAINTENANCE'])
               ->groupStart();
            
            if ($type === 'attachment') {
                $qb->like('ia.sn_attachment', $q)
                   ->orLike('a.tipe', $q)
                   ->orLike('a.merk', $q)
                   ->orLike('a.model', $q);
            } elseif ($type === 'battery') {
                $qb->like('ia.sn_baterai', $q)
                   ->orLike('b.merk_baterai', $q)
                   ->orLike('b.tipe_baterai', $q)
                   ->orLike('b.jenis_baterai', $q);
            } elseif ($type === 'charger') {
                $qb->like('ia.sn_charger', $q)
                   ->orLike('c.merk_charger', $q)
                   ->orLike('c.tipe_charger', $q);
            }
            
            $qb->orLike('ia.lokasi_penyimpanan', $q)
               ->groupEnd()
               ->orderBy("FIELD(ia.attachment_status, 'AVAILABLE', 'USED', 'MAINTENANCE')", '', false)
               ->orderBy('ia.id_inventory_attachment', 'DESC');
        }
        
        // Add pagination
        $qb->limit($perPage, $offset);
        
        $rows = $qb->get()->getResultArray();
        $data = array_map(function($r) use ($type){
            $isUsed = !empty($r['id_inventory_unit']);
            $label = '';
            $serialNumber = '';
            
            // Build label and serial number based on type
            if ($type === 'attachment') {
                $label = trim(($r['attachment_tipe'] ?: '-') . ' ' . ($r['attachment_merk'] ?: '') . ' ' . ($r['attachment_model'] ?: ''));
                $serialNumber = $r['sn_attachment'];
            } elseif ($type === 'battery') {
                $label = trim(($r['merk_baterai'] ?: '-') . ' ' . ($r['tipe_baterai'] ?: ''));
                if (!empty($r['jenis_baterai'])) {
                    $label .= ' (' . $r['jenis_baterai'] . ')';
                }
                $serialNumber = $r['sn_baterai'];
            } elseif ($type === 'charger') {
                $label = trim(($r['merk_charger'] ?: '-') . ' ' . ($r['tipe_charger'] ?: ''));
                $serialNumber = $r['sn_charger'];
            }
            
            return [
                'id'=>(int)$r['id'],
                'label'=>$label,
                'sn_attachment' => $r['sn_attachment'],
                'sn_baterai' => $r['sn_baterai'], 
                'sn_charger' => $r['sn_charger'],
                'tipe_item' => $r['tipe_item'],
                'kondisi_fisik' => $r['kondisi_fisik'],
                'kelengkapan' => $r['kelengkapan'],
                'lokasi_penyimpanan' => $r['lokasi_penyimpanan'],
                'attachment_status' => $r['attachment_status'],
                'is_used' => $isUsed,
                'used_by_unit' => $isUsed ? $r['no_unit'] : null,
                'installed_unit' => $isUsed ? [
                    'unit_id' => $r['id_inventory_unit'],
                    'no_unit' => $r['no_unit'],
                    'serial_number' => $r['serial_number'],
                    'merk_unit' => $r['merk_unit'],
                    'model_unit' => $r['model_unit']
                ] : null
            ];
        }, $rows);
        return $this->response->setJSON(['success'=>true,'data'=>$data,'csrf_hash'=>csrf_hash()]);
    }
    
    /**
     * Get employees by roles for multi-select mechanic dropdown
     */
    public function employeesByRoles()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $rolesParam = $this->request->getGet('roles');
        if (empty($rolesParam)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Roles parameter required']);
        }
        
        $roles = explode(',', $rolesParam);
        
        // Validate roles
        $validRoles = ['MECHANIC_UNIT_PREP', 'MECHANIC_FABRICATION', 'MECHANIC_SERVICE_AREA', 'FOREMAN', 'SUPERVISOR', 'HELPER'];
        $roles = array_intersect($roles, $validRoles);
        
        if (empty($roles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No valid roles provided']);
        }
        
        try {
            $employees = $this->db->table('employees')
                ->select('id, staff_name, staff_role, job_description, departemen_id')
                ->whereIn('staff_role', $roles)
                ->where('is_active', 1)
                ->orderBy('staff_role ASC, staff_name ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $employees,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching employees by roles: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error fetching employees'
            ]);
        }
    }

    private function serviceBaseQuery(array $allowed = [1,2,3]): \CodeIgniter\Database\BaseBuilder
    {
        // Limit to departemen ELECTRIC (2) and DIESEL & GASOLINE (1,3)
        $qb = $this->db->table('inventory_unit iu')
            ->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, iu.status_unit_id, iu.lokasi_unit')
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
            ->whereIn('iu.departemen_id',$allowed);
        return $qb;
    }

    /** RBAC logic: obtain allowed departemen IDs for service scope based on user department scope */
    private function getAllowedServiceDepartemenIds(): array
    {
        // Get user's area and department scope
        $scope = get_user_area_department_scope();
        
        // If user has full access (null scope) or no department restrictions, allow all
        if ($scope === null || empty($scope['departments'])) {
            return [1, 2, 3]; // All departments: DIESEL, ELECTRIC, GASOLINE
        }
        
        // Return user's allowed departments based on their scope
        log_message('info', 'Service filtering: User allowed departments: ' . implode(',', $scope['departments']));
        return $scope['departments'];
    }
    
    /** Add new inventory attachment via unit verification modal */
    public function addInventoryAttachment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $type = $this->request->getPost('tipe_item');
        $kondisiFisik = $this->request->getPost('kondisi_fisik');
        $kelengkapan = $this->request->getPost('kelengkapan');
        
        // Get serial number based on type
        $serialNumber = '';
        switch ($type) {
            case 'attachment':
                $serialNumber = trim($this->request->getPost('sn_attachment'));
                break;
            case 'battery':
                $serialNumber = trim($this->request->getPost('sn_baterai'));
                break;
            case 'charger':
                $serialNumber = trim($this->request->getPost('sn_charger'));
                break;
        }
        
        // Debug logging
        log_message('info', 'Add Inventory Attachment Debug:');
        log_message('info', 'Type: ' . $type);
        log_message('info', 'Serial Number: ' . $serialNumber);
        log_message('info', 'All POST data: ' . json_encode($this->request->getPost()));

        // Validation
        if (empty($type) || !in_array($type, ['attachment', 'battery', 'charger'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid type: ' . $type]);
        }

        if (empty($serialNumber)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Serial number is required']);
        }

        try {
            $data = [
                'tipe_item' => $type,
                'kondisi_fisik' => $kondisiFisik ?: 'Baik',
                'kelengkapan' => $kelengkapan ?: 'Lengkap',
                'status_unit' => 7, // AVAILABLE_STOCK
                'attachment_status' => 'AVAILABLE',
                'po_id' => 1, // default PO, you might want to make this dynamic
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Set specific fields based on type
            switch ($type) {
                case 'attachment':
                    $data['sn_attachment'] = $serialNumber;
                    $attachmentId = $this->request->getPost('attachment_id');
                    if (!empty($attachmentId)) {
                        $data['attachment_id'] = $attachmentId;
                    }
                    break;
                case 'battery':
                    $data['sn_baterai'] = $serialNumber;
                    $bateraiId = $this->request->getPost('baterai_id');
                    if (!empty($bateraiId)) {
                        $data['baterai_id'] = $bateraiId;
                    }
                    break;
                case 'charger':
                    $data['sn_charger'] = $serialNumber;
                    $chargerId = $this->request->getPost('charger_id');
                    if (!empty($chargerId)) {
                        $data['charger_id'] = $chargerId;
                    }
                    break;
            }

            // Check if serial number already exists for this type
            $existingCheck = $this->attModel->where('tipe_item', $type);
            switch ($type) {
                case 'attachment':
                    $existingCheck->where('sn_attachment', $serialNumber);
                    break;
                case 'battery':
                    $existingCheck->where('sn_baterai', $serialNumber);
                    break;
                case 'charger':
                    $existingCheck->where('sn_charger', $serialNumber);
                    break;
            }
            
            $existing = $existingCheck->first();
            if ($existing) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Serial number already exists for this type'
                ]);
            }

            // Insert new record
            $insertId = $this->attModel->insert($data);
            
            if ($insertId) {
                // Get the newly created record for dropdown
                $newRecord = $this->attModel->find($insertId);
                
                // Send cross-division notification to Warehouse
                helper('notification');
                if (function_exists('notify_attachment_added') && $insertId > 0) {
                    notify_attachment_added([
                        'attachment_id' => $insertId,
                        'tipe_item' => $type,
                        'merk' => $data['merk'] ?? '',
                        'model' => $data['model'] ?? '',
                        'serial_number' => $serialNumber,
                        'kondisi' => $data['kondisi_fisik'] ?? 'Baik',
                        'lokasi' => $data['lokasi_penyimpanan'] ?? 'Workshop',
                        'added_by' => session('username') ?? 'System',
                        'added_at' => date('Y-m-d H:i:s'),
                        'url' => base_url('/warehouse/attachment/view/' . $insertId)
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Attachment added successfully',
                    'data' => $newRecord,
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to add attachment'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error adding inventory attachment: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while adding attachment'
            ]);
        }
    }

    /** Get master data for attachment dropdown in modal */
    public function getMasterAttachment()
    {
        $q = trim((string)($this->request->getGet('q') ?? ''));
        
        $qb = $this->db->table('attachment')
            ->select('id_attachment as id, tipe, merk, model')
            ->orderBy('tipe, merk, model')
            ->limit(50);
            
        if ($q !== '') {
            $qb->groupStart()
                ->like('tipe', $q)
                ->orLike('merk', $q)
                ->orLike('model', $q)
                ->groupEnd();
        }
        
        $rows = $qb->get()->getResultArray();
        $data = array_map(function($r){
            $label = trim(($r['tipe'] ?: '') . ' ' . ($r['merk'] ?: '') . ' ' . ($r['model'] ?: ''));
            return [
                'id' => (int)$r['id'],
                'text' => $label,
                'tipe' => $r['tipe'],
                'merk' => $r['merk'],
                'model' => $r['model']
            ];
        }, $rows);
        
        return $this->response->setJSON(['success' => true, 'data' => $data, 'csrf_hash' => csrf_hash()]);
    }

    /** Get master data for baterai dropdown in modal */
    public function getMasterBaterai()
    {
        $q = trim((string)($this->request->getGet('q') ?? ''));
        
        $qb = $this->db->table('baterai')
            ->select('id, merk_baterai, tipe_baterai, jenis_baterai')
            ->orderBy('merk_baterai, tipe_baterai')
            ->limit(50);
            
        if ($q !== '') {
            $qb->groupStart()
                ->like('merk_baterai', $q)
                ->orLike('tipe_baterai', $q)
                ->orLike('jenis_baterai', $q)
                ->groupEnd();
        }
        
        $rows = $qb->get()->getResultArray();
        $data = array_map(function($r){
            $label = trim(($r['merk_baterai'] ?: '') . ' ' . ($r['tipe_baterai'] ?: ''));
            if (!empty($r['jenis_baterai'])) {
                $label .= ' (' . $r['jenis_baterai'] . ')';
            }
            return [
                'id' => (int)$r['id'],
                'text' => $label,
                'merk_baterai' => $r['merk_baterai'],
                'tipe_baterai' => $r['tipe_baterai'],
                'jenis_baterai' => $r['jenis_baterai']
            ];
        }, $rows);
        
        return $this->response->setJSON(['success' => true, 'data' => $data, 'csrf_hash' => csrf_hash()]);
    }

    /** Get master data for charger dropdown in modal */
    public function getMasterCharger()
    {
        $q = trim((string)($this->request->getGet('q') ?? ''));
        
        $qb = $this->db->table('charger')
            ->select('id_charger as id, merk_charger, tipe_charger')
            ->orderBy('merk_charger, tipe_charger')
            ->limit(50);
            
        if ($q !== '') {
            $qb->groupStart()
                ->like('merk_charger', $q)
                ->orLike('tipe_charger', $q)
                ->groupEnd();
        }
        
        $rows = $qb->get()->getResultArray();
        $data = array_map(function($r){
            $label = trim(($r['merk_charger'] ?: '') . ' ' . ($r['tipe_charger'] ?: ''));
            return [
                'id' => (int)$r['id'],
                'text' => $label,
                'merk_charger' => $r['merk_charger'],
                'tipe_charger' => $r['tipe_charger']
            ];
        }, $rows);
        
        return $this->response->setJSON(['success' => true, 'data' => $data, 'csrf_hash' => csrf_hash()]);
    }
    
    /**
     * Save unit verification data from service work order
     */
    public function saveUnitVerification()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Debug logging
            log_message('info', 'Save Unit Verification Debug:');
            log_message('info', 'All POST data: ' . json_encode($this->request->getPost()));
            // Get form data
            $workOrderId = $this->request->getPost('work_order_id');
            $unitId = $this->request->getPost('unit_id');
            
            // Validation
            if (empty($workOrderId) || empty($unitId)) {
                throw new \Exception('Work Order ID dan Unit ID wajib diisi');
            }

            // Get old attachment data before update
            $oldUnit = $db->table('inventory_unit')
                ->select('attachment_inventory_attachment_id, baterai_inventory_attachment_id, charger_inventory_attachment_id')
                ->where('id_inventory_unit', $unitId)
                ->get()
                ->getRowArray();

            // Prepare unit update data
            $unitData = [
                'no_unit' => $this->request->getPost('no_unit'),
                'pelanggan' => $this->request->getPost('pelanggan'),
                'lokasi' => $this->request->getPost('lokasi'),
                'serial_number' => $this->request->getPost('serial_number'),
                'tahun_unit' => $this->request->getPost('tahun_unit'),
                'departemen_id' => $this->request->getPost('departemen_id'),
                'tipe_unit_id' => $this->request->getPost('tipe_unit_id'),
                'model_unit_id' => $this->request->getPost('model_unit_id'),
                'kapasitas_unit_id' => $this->request->getPost('kapasitas_unit_id'),
                'model_mesin_id' => $this->request->getPost('model_mesin_id'),
                'sn_mesin' => $this->request->getPost('sn_mesin'),
                'model_mast_id' => $this->request->getPost('model_mast_id'),
                'sn_mast' => $this->request->getPost('sn_mast'),
                'tinggi_mast' => $this->request->getPost('tinggi_mast'),
                'keterangan' => $this->request->getPost('keterangan'),
            ];

            // Handle attachment data
            $newAttachmentId = $this->request->getPost('attachment_id');
            $newBateraiId = $this->request->getPost('baterai_id');
            $newChargerId = $this->request->getPost('charger_id');

            // Update attachment relationships
            if (!empty($newAttachmentId)) {
                // VALIDASI: Cek apakah attachment available atau sudah dipakai unit lain
                $attachmentCheck = $db->table('inventory_attachment')
                    ->select('id_inventory_attachment, attachment_status, id_inventory_unit')
                    ->where('id_inventory_attachment', $newAttachmentId)
                    ->get()->getRowArray();
                
                if (!$attachmentCheck) {
                    throw new \Exception('Attachment tidak ditemukan di database');
                }
                
                // Jika attachment USED dan bukan milik unit ini, reject
                if ($attachmentCheck['attachment_status'] === 'USED' && 
                    !empty($attachmentCheck['id_inventory_unit']) &&
                    $attachmentCheck['id_inventory_unit'] != $unitId) {
                    
                    // Get unit number yang pakai attachment ini
                    $usedByUnit = $db->table('inventory_unit')
                        ->select('no_unit')
                        ->where('id_inventory_unit', $attachmentCheck['id_inventory_unit'])
                        ->get()->getRowArray();
                    
                    $usedBy = $usedByUnit ? $usedByUnit['no_unit'] : 'Unit Lain';
                    throw new \Exception('Attachment sudah digunakan oleh ' . $usedBy . '. Silakan pilih attachment lain.');
                }
                
                $unitData['attachment_inventory_attachment_id'] = $newAttachmentId;
                $unitData['sn_attachment'] = $this->request->getPost('sn_attachment');
                
                // Release old attachment if changed
                if (!empty($oldUnit['attachment_inventory_attachment_id']) && 
                    $oldUnit['attachment_inventory_attachment_id'] != $newAttachmentId) {
                    $this->releaseAttachment($oldUnit['attachment_inventory_attachment_id']);
                }
                
                // Attach new attachment to unit
                $this->attachToUnit($newAttachmentId, $unitId);
            }

            if (!empty($newBateraiId)) {
                // VALIDASI: Cek apakah baterai available
                $bateraiCheck = $db->table('inventory_attachment')
                    ->select('id_inventory_attachment, attachment_status, id_inventory_unit')
                    ->where('id_inventory_attachment', $newBateraiId)
                    ->get()->getRowArray();
                
                if ($bateraiCheck && $bateraiCheck['attachment_status'] === 'USED' && 
                    !empty($bateraiCheck['id_inventory_unit']) &&
                    $bateraiCheck['id_inventory_unit'] != $unitId) {
                    
                    $usedByUnit = $db->table('inventory_unit')
                        ->select('no_unit')
                        ->where('id_inventory_unit', $bateraiCheck['id_inventory_unit'])
                        ->get()->getRowArray();
                    
                    $usedBy = $usedByUnit ? $usedByUnit['no_unit'] : 'Unit Lain';
                    throw new \Exception('Baterai sudah digunakan oleh ' . $usedBy);
                }
                
                $unitData['baterai_inventory_attachment_id'] = $newBateraiId;
                $unitData['sn_baterai'] = $this->request->getPost('sn_baterai');
                
                // Release old baterai if changed
                if (!empty($oldUnit['baterai_inventory_attachment_id']) && 
                    $oldUnit['baterai_inventory_attachment_id'] != $newBateraiId) {
                    $this->releaseAttachment($oldUnit['baterai_inventory_attachment_id']);
                }
                
                // Attach new baterai to unit
                $this->attachToUnit($newBateraiId, $unitId);
            }

            if (!empty($newChargerId)) {
                // VALIDASI: Cek apakah charger available
                $chargerCheck = $db->table('inventory_attachment')
                    ->select('id_inventory_attachment, attachment_status, id_inventory_unit')
                    ->where('id_inventory_attachment', $newChargerId)
                    ->get()->getRowArray();
                
                if ($chargerCheck && $chargerCheck['attachment_status'] === 'USED' && 
                    !empty($chargerCheck['id_inventory_unit']) &&
                    $chargerCheck['id_inventory_unit'] != $unitId) {
                    
                    $usedByUnit = $db->table('inventory_unit')
                        ->select('no_unit')
                        ->where('id_inventory_unit', $chargerCheck['id_inventory_unit'])
                        ->get()->getRowArray();
                    
                    $usedBy = $usedByUnit ? $usedByUnit['no_unit'] : 'Unit Lain';
                    throw new \Exception('Charger sudah digunakan oleh ' . $usedBy);
                }
                
                $unitData['charger_inventory_attachment_id'] = $newChargerId;
                $unitData['sn_charger'] = $this->request->getPost('sn_charger');
                
                // Release old charger if changed
                if (!empty($oldUnit['charger_inventory_attachment_id']) && 
                    $oldUnit['charger_inventory_attachment_id'] != $newChargerId) {
                    $this->releaseAttachment($oldUnit['charger_inventory_attachment_id']);
                }
                
                // Attach new charger to unit
                $this->attachToUnit($newChargerId, $unitId);
            }

            // Update unit data
            log_message('info', 'Updating unit data: ' . json_encode($unitData));
            $updateResult = $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update($unitData);
            
            if (!$updateResult) {
                throw new \Exception('Failed to update inventory_unit');
            }
            log_message('info', 'Unit data updated successfully');

            // Handle accessories - check if table exists first
            $accessories = $this->request->getPost('accessories');
            if (is_array($accessories) && count($accessories) > 0) {
                try {
                    // Check if unit_aksesoris table exists
                    $db->query("SELECT 1 FROM unit_aksesoris LIMIT 1");
                    
                    // Delete old accessories
                    $db->table('unit_aksesoris')
                        ->where('id_inventory_unit', $unitId)
                        ->delete();

                    // Insert new accessories
                    foreach ($accessories as $accessory) {
                        $db->table('unit_aksesoris')->insert([
                            'id_inventory_unit' => $unitId,
                            'accessory_name' => $accessory,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } catch (\Exception $e) {
                    // If table doesn't exist, just log and continue
                    log_message('info', 'unit_aksesoris table not found, skipping accessories: ' . $e->getMessage());
                }
            }

            // Update work order status to COMPLETED
            $verificationData = [
                'verified_by' => $this->request->getPost('verified_by'),
                'verification_date' => $this->request->getPost('verification_date'),
                'status' => 'COMPLETED',
                'completed_at' => date('Y-m-d H:i:s')
            ];

            log_message('info', 'Updating work order ID: ' . $workOrderId);
            log_message('info', 'Verification data: ' . json_encode($verificationData));
            
            $workOrderUpdate = $db->table('service_work_orders')
                ->where('id_service_work_orders', $workOrderId)
                ->update($verificationData);
            
            if (!$workOrderUpdate) {
                throw new \Exception('Failed to update service_work_orders');
            }
            log_message('info', 'Work order updated successfully');

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data verifikasi');
            }

            // Send notification: Unit Prep Completed
            helper('notification');
            $unit = $db->table('inventory_unit')->where('id_inventory_unit', $unitId)->get()->getRowArray();
            if ($unit) {
                notify_unit_prep_completed([
                    'id' => $workOrderId,
                    'no_unit' => $unit['no_unit'] ?? '',
                    'nomor_spk' => $unit['nomor_spk'] ?? '',
                    'verified_by' => $verificationData['verified_by'],
                    'verification_date' => $verificationData['verification_date'],
                    'url' => base_url('/service/work-order/detail/' . $workOrderId)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Verifikasi berhasil disimpan',
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error saving unit verification: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Release attachment from unit (set id_inventory_unit to NULL and status to AVAILABLE)
     */
    private function releaseAttachment($attachmentId)
    {
        $db = \Config\Database::connect();
        $db->table('inventory_attachment')
            ->where('id_inventory_attachment', $attachmentId)
            ->update([
                'id_inventory_unit' => null,
                'attachment_status' => 'AVAILABLE',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
        log_message('info', "Released attachment ID: {$attachmentId}");
    }

    /**
     * Attach attachment to unit (set id_inventory_unit and status to USED)
     */
    private function attachToUnit($attachmentId, $unitId)
    {
        $db = \Config\Database::connect();
        $db->table('inventory_attachment')
            ->where('id_inventory_attachment', $attachmentId)
            ->update([
                'id_inventory_unit' => $unitId,
                'attachment_status' => 'USED',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
        log_message('info', "Attached attachment ID: {$attachmentId} to unit ID: {$unitId}");
    }
    
}
