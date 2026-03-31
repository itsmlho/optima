<?php

namespace App\Controllers;

use App\Models\WorkOrderModel;
use App\Models\WorkOrderAssignmentModel;
use App\Models\AreaEmployeeAssignmentModel;
use App\Models\EmployeeModel;
use App\Models\CustomerModel;
use App\Models\AreaModel;
use App\Models\WorkOrderSparepartModel;
use App\Models\WorkOrderSparepartUsageModel;
use App\Models\InventoryAttachmentModel;
use App\Models\InventoryBatteryModel;
use App\Models\InventoryChargerModel;
use App\Models\InventoryComponentHelper;
use App\Models\AuditLocationModel;
use App\Traits\ActivityLoggingTrait;
use CodeIgniter\Controller;

class WorkOrderController extends Controller
{
    use ActivityLoggingTrait;
    protected $workOrderModel;
    protected $workOrderAssignmentModel;
    protected $areaEmployeeModel;
    protected $employeeModel;
    protected $customerModel;
    protected $areaModel;
    protected $sparepartModel;
    protected $sparepartUsageModel;
    
    public function __construct()
    {
        $this->workOrderModel = new WorkOrderModel();
        $this->workOrderAssignmentModel = new WorkOrderAssignmentModel();
        $this->areaEmployeeModel = new AreaEmployeeAssignmentModel();
        $this->employeeModel = new EmployeeModel();
        $this->customerModel = new CustomerModel();
        $this->areaModel = new AreaModel();
        $this->sparepartModel = new WorkOrderSparepartModel();
        $this->sparepartUsageModel = new WorkOrderSparepartUsageModel();
        
        // Load auth helper for division filtering
        helper('auth');
    }
    
    // Menampilkan halaman daftar work order
    public function index()
    {
        // Check user permissions via session
        $session = session();
        $userRole = $session->get('role');
        if (empty($userRole)) {
            return redirect()->to('/')->with('error', 'Akses ditolak: Anda tidak memiliki izin');
        }
        
        // Get user's department scope using proper RBAC infrastructure
        $scope = get_user_area_department_scope();
        $userDepartemenIds = []; // empty = no filter (full access)
        $userAreaIds = [];
        $scopeType = null;
        
        if ($scope !== null) {
            $userDepartemenIds = !empty($scope['departments']) ? $scope['departments'] : [];
            $userAreaIds = !empty($scope['areas']) ? $scope['areas'] : [];
            $scopeType = $scope['scope_type'] ?? null;
        }
        
        // Get department names for display
        $userDepartemenName = null;
        if (!empty($userDepartemenIds)) {
            $db = \Config\Database::connect();
            $depts = $db->table('departemen')
                ->select('nama_departemen')
                ->whereIn('id_departemen', $userDepartemenIds)
                ->get()
                ->getResultArray();
            $userDepartemenName = implode(', ', array_column($depts, 'nama_departemen'));
        }
        
        $data = [
            'title' => 'Work Orders Management',
            'workOrders' => $this->workOrderModel->getAllWorkOrders(),
            'statuses' => $this->workOrderModel->getStatuses(),
            'priorities' => $this->workOrderModel->getPriorities(),
            'categories' => $this->workOrderModel->getCategories(),
            'units' => $this->getUnits(),
            'areas' => $this->areaModel->getActiveAreas(),
            'spareparts' => [], // REMOVED: Pre-loading 14k+ items - now using AJAX search
            'staff' => [
                'ADMIN' => $this->workOrderModel->getStaff('ADMIN'),
                'FOREMAN' => $this->workOrderModel->getStaff('FOREMAN'),
                'MECHANIC' => $this->workOrderModel->getStaff('MECHANIC'),
                'HELPER' => $this->workOrderModel->getStaff('HELPER')
            ],
            // User department scope for filtering
            'user_departemen_ids' => $userDepartemenIds,
            'user_departemen_name' => $userDepartemenName,
            'user_area_ids' => $userAreaIds,
            'scope_type' => $scopeType,
            // Permission flags for view
            'can_create' => can_create('service'),
            'can_edit' => can_edit('service'),
            'can_delete' => can_delete('service'),
            'can_export' => can_export('service')
        ];
        
        return view('service/work_orders', $data);
    }
    
    // Get spareparts data for dropdown
    private function getSparepartsForDropdown()
    {
        try {
            $db = \Config\Database::connect();
            $spareparts = $db->table('sparepart')
                ->select('id_sparepart, kode, desc_sparepart')
                ->where('is_active', 1)
                ->orderBy('kode', 'ASC')
                ->get()
                ->getResultArray();
            
            // Format data untuk dropdown dengan text yang berisi kode dan nama
            $formatted = [];
            foreach ($spareparts as $sparepart) {
                $formatted[] = [
                    'id_sparepart' => $sparepart['id_sparepart'],
                    'kode' => $sparepart['kode'],
                    'desc_sparepart' => $sparepart['desc_sparepart'],
                    'text' => $sparepart['kode'] . ' - ' . $sparepart['desc_sparepart']
                ];
            }
            
            return $formatted;
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return [];
        }
    }

    /**
     * AJAX endpoint for sparepart search (Select2 AJAX)
     * Optimized for large datasets (14k+ items)
     */
    public function searchSpareparts()
    {
        try {
            $searchTerm = $this->request->getGet('q') ?? '';
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = 30; // Limit results per page
            
            $db = \Config\Database::connect();
            $builder = $db->table('sparepart');
            
            // Select fields
            $builder->select('id_sparepart, kode, desc_sparepart');
            
            // Search filter
            if (!empty($searchTerm)) {
                $builder->groupStart()
                    ->like('kode', $searchTerm)
                    ->orLike('desc_sparepart', $searchTerm)
                    ->groupEnd();
            }
            
            // Count total for pagination
            $totalCount = $builder->countAllResults(false);
            
            // Pagination
            $builder->orderBy('kode', 'ASC');
            $builder->limit($perPage, ($page - 1) * $perPage);
            
            $spareparts = $builder->get()->getResultArray();
            
            // Format for Select2
            $results = [];
            foreach ($spareparts as $sparepart) {
                $results[] = [
                    'id' => $sparepart['kode'] . ' - ' . $sparepart['desc_sparepart'],
                    'text' => $sparepart['kode'] . ' - ' . $sparepart['desc_sparepart'],
                    'kode' => $sparepart['kode'],
                    'desc' => $sparepart['desc_sparepart']
                ];
            }
            
            // Add manual input option at the end of first page
            if ($page === 1) {
                $results[] = [
                    'id' => 'INPUT_MANUAL',
                    'text' => '--- Input Manual ---'
                ];
            }
            
            return $this->response->setJSON([
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $totalCount
                ]
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'results' => [],
                'pagination' => ['more' => false]
            ]);
        }
    }

    // Mendapatkan daftar unit dengan customer dan area info
    private function getUnits()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('inventory_unit iu');
        $builder->select('iu.id_inventory_unit, iu.no_unit, iu.departemen_id,
                         COALESCE(c.customer_name, "Belum Ada Kontrak") as pelanggan,
                         a.id as area_id, a.area_name, a.area_code,
                         d.nama_departemen as departemen_name,
                         mu.merk_unit, tu.tipe');
        // Updated: JOIN via kontrak_unit junction table (source of truth)
        $builder->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left');
        $builder->join('kontrak k', 'k.id = ku.kontrak_id', 'left');
        $builder->join('customers c', 'c.id = k.customer_id', 'left');
        $builder->join('areas a', 'c.area_id = a.id', 'left');
        $builder->join('departemen d', 'iu.departemen_id = d.id_departemen', 'left');
        $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
        $builder->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left');
        $builder->where('iu.no_unit IS NOT NULL');
        // Exclude SOLD units (status_unit_id = 13)
        $builder->where('iu.status_unit_id !=', 13);
        
        // Apply department scope filtering
        $scope = get_user_area_department_scope();
        if ($scope !== null && !empty($scope['departments'])) {
            $builder->whereIn('iu.departemen_id', array_map('intval', $scope['departments']));
        }
        
        $builder->orderBy('iu.no_unit', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    // Get unit area information for work order form
    public function getUnitArea()
    {
        $unitId = $this->request->getPost('unit_id');
        
        if (!$unitId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unit ID required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            
            // Debug: Check if unit exists
            $unit = $db->table('inventory_unit')->where('id_inventory_unit', $unitId)->get()->getRowArray();
            if (!$unit) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan'
                ]);
            }
            
            // Direct approach: inventory_unit already has area_id column!
            $builder = $db->table('inventory_unit iu');
            $builder->select('a.id as area_id, a.area_name, a.area_code, a.departemen_id,
                             d.nama_departemen as departemen_name,
                             s.staff_name as foreman_name, s.id as foreman_id');
            $builder->join('areas a', 'iu.area_id = a.id', 'left');
            $builder->join('departemen d', 'a.departemen_id = d.id_departemen', 'left');
            $builder->join('area_employee_assignments aea', 'a.id = aea.area_id AND aea.is_active = 1', 'left');
            $builder->join('employees s', 'aea.employee_id = s.id AND s.staff_role = "FOREMAN" AND s.is_active = 1', 'left');
            $builder->where('iu.id_inventory_unit', $unitId);
            
            $result = $builder->get()->getRowArray();
            
            // If still no area found, get any active area as fallback
            if (!$result || !$result['area_id']) {
                $fallbackArea = $db->table('areas')
                    ->select('id as area_id, area_name, area_code')
                    ->where('is_active', 1)
                    ->get()
                    ->getRowArray();
                
                if ($fallbackArea) {
                    // Try to get foreman for this area
                    $foreman = $db->table('area_staff_assignments asa')
                        ->select('s.staff_name, s.id as foreman_id')
                        ->join('staff s', 'asa.staff_id = s.id')
                        ->where('asa.area_id', $fallbackArea['area_id'])
                        ->where('asa.is_active', 1)
                        ->where('s.staff_role', 'FOREMAN')
                        ->where('s.is_active', 1)
                        ->get()
                        ->getRowArray();
                    
                    $result = $fallbackArea;
                    if ($foreman) {
                        $result['foreman_name'] = $foreman['staff_name'];
                        $result['foreman_id'] = $foreman['foreman_id'];
                    } else {
                        $result['foreman_name'] = 'No Foreman Assigned';
                        $result['foreman_id'] = null;
                    }
                }
            }
            
            if ($result && $result['area_id']) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Area information not found for this unit'
                ]);
            }
            
        } catch (\Throwable $e) {
            log_message('error', 'WorkOrderController::getUnitArea - [' . get_class($e) . '] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat informasi area'
            ]);
        }
    }

    // Get spareparts dropdown data
    public function sparepartsDropdown()
    {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('sparepart');
            $builder->select('id_sparepart, kode, desc_sparepart');
            $builder->orderBy('kode', 'ASC');
            
            $spareparts = $builder->get()->getResultArray();
            
            // If no spareparts found, return empty array
            if (empty($spareparts)) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [],
                    'message' => 'No spareparts found. Please add sparepart data to database.'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $spareparts
            ]);
            
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data sparepart'
            ]);
        }
    }


    // Get staff dropdown data
    public function staffDropdown()
    {
        $staffRole = $this->request->getPost('staff_role');
        $areaId = $this->request->getPost('area_id');
        $departemenId = $this->request->getPost('departemen_id');
        // Support multiple department IDs (e.g. from scope)
        $departemenIds = $this->request->getPost('departemen_ids');
        
        // Auto-apply user's scope if no explicit filter sent
        $scopeAreaIds = null; // for MILL users (area-based scope)
        if (empty($departemenId) && empty($departemenIds) && empty($areaId)) {
            $scope = get_user_area_department_scope();
            if ($scope !== null) {
                if (!empty($scope['departments'])) {
                    $departemenIds = $scope['departments'];
                } elseif (!empty($scope['areas'])) {
                    // MILL user: scope by area assignments
                    $scopeAreaIds = $scope['areas'];
                }
            }
        }
        
        if (!$staffRole) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Staff role required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            
            if ($departemenId || !empty($departemenIds)) {
                // Filter by department(s) (DIESEL/ELECTRIC/multi)
                $builder = $db->table('employees e');
                $builder->select('e.id, e.staff_name, e.staff_role, e.staff_code, e.employee_code');
                
                // Handle MECHANIC and HELPER role matching
                if ($staffRole === 'MECHANIC') {
                    $builder->like('e.staff_role', 'MECHANIC', 'both');
                } elseif ($staffRole === 'HELPER') {
                    $builder->like('e.staff_role', 'HELPER', 'both');
                } else {
                    $builder->where('e.staff_role', $staffRole);
                }
                
                $builder->where('e.is_active', 1);
                
                if (!empty($departemenIds) && is_array($departemenIds)) {
                    $builder->whereIn('e.departemen_id', array_map('intval', $departemenIds));
                } else {
                    $builder->where('e.departemen_id', (int) $departemenId);
                }
                $builder->orderBy('e.staff_name', 'ASC');
                
            } elseif ($areaId || !empty($scopeAreaIds)) {
                // Use area_employee_assignments table with employees for area-based filtering
                $builder = $db->table('employees e');
                $builder->select('e.id, e.staff_name, e.staff_role');
                $builder->join('area_employee_assignments aea', 'e.id = aea.employee_id');
                
                // Handle MECHANIC and HELPER role matching (support subtypes)
                if ($staffRole === 'MECHANIC') {
                    $builder->like('e.staff_role', 'MECHANIC', 'both');
                } elseif ($staffRole === 'HELPER') {
                    $builder->like('e.staff_role', 'HELPER', 'both');
                } else {
                    $builder->where('e.staff_role', $staffRole);
                }
                
                $builder->where('e.is_active', 1);
                if (!empty($scopeAreaIds)) {
                    $builder->whereIn('aea.area_id', array_map('intval', $scopeAreaIds));
                } else {
                    $builder->where('aea.area_id', $areaId);
                }
                $builder->where('aea.is_active', 1);
            } else {
                // Use employees table for general staff (fallback - not area-specific)
                $builder = $db->table('employees');
                $builder->select('id, staff_name, staff_role');
                
                // Handle MECHANIC and HELPER role matching (support subtypes)
                if ($staffRole === 'MECHANIC') {
                    $builder->like('staff_role', 'MECHANIC', 'both');
                } elseif ($staffRole === 'HELPER') {
                    $builder->like('staff_role', 'HELPER', 'both');
                } else {
                    $builder->where('staff_role', $staffRole);
                }
                
                $builder->where('is_active', 1);
            }
            
            $builder->orderBy('staff_name', 'ASC');
            
            $staff = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $staff
            ]);
            
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data staff'
            ]);
        }
    }

    /**
     * Get area staff (admin and foreman) for auto-fill
     */
    public function getAreaStaff()
    {
        $areaId = $this->request->getPost('area_id');
        
        // When no explicit area_id, fall back to the user's scope areas
        $scopeAreaIds = null;
        $loadAll = false;
        if (!$areaId) {
            $scope = get_user_area_department_scope();
            if ($scope !== null && !empty($scope['areas'])) {
                // MILL user: restrict to their assigned areas
                $scopeAreaIds = array_map('intval', $scope['areas']);
            } else {
                // Department-based or full-access user: load all admins/foremans
                $loadAll = true;
            }
        }
        
        try {
            $db = \Config\Database::connect();
            
            if ($loadAll) {
                // Full-access or department-based user: load all admins/foremans
                // Query employees directly (no area_employee_assignments join needed)
                $admins = $db->table('employees')
                    ->select('id, staff_name, staff_role')
                    ->like('staff_role', 'ADMIN', 'both')
                    ->where('is_active', 1)
                    ->orderBy('staff_name', 'ASC')
                    ->get()->getResultArray();
                
                $foremans = $db->table('employees')
                    ->select('id, staff_name, staff_role')
                    ->where('staff_role', 'FOREMAN')
                    ->where('is_active', 1)
                    ->orderBy('staff_name', 'ASC')
                    ->get()->getResultArray();
            } elseif ($scopeAreaIds !== null) {
                $areaPlaceholders = implode(',', $scopeAreaIds);
                
                $admins = $db->query("
                    SELECT e.id, e.staff_name, aea.assignment_type, aea.start_date
                    FROM area_employee_assignments aea
                    JOIN employees e ON aea.employee_id = e.id
                    WHERE aea.area_id IN ($areaPlaceholders) AND aea.is_active = 1
                    AND e.staff_role LIKE '%ADMIN%' AND e.is_active = 1
                    ORDER BY CASE aea.assignment_type WHEN 'PRIMARY' THEN 0 ELSE 1 END,
                        aea.start_date ASC, e.id ASC
                ")->getResultArray();
                
                $foremans = $db->query("
                    SELECT e.id, e.staff_name, aea.assignment_type, aea.start_date
                    FROM area_employee_assignments aea
                    JOIN employees e ON aea.employee_id = e.id
                    WHERE aea.area_id IN ($areaPlaceholders) AND aea.is_active = 1
                    AND e.staff_role = 'FOREMAN' AND e.is_active = 1
                    ORDER BY CASE aea.assignment_type WHEN 'PRIMARY' THEN 0 ELSE 1 END,
                        aea.start_date ASC, e.id ASC
                ")->getResultArray();
            } else {
            // Get ALL admins for this area with priority sorting
            $admins = $db->query("
                SELECT e.id, e.staff_name, aea.assignment_type, aea.start_date
                FROM area_employee_assignments aea
                JOIN employees e ON aea.employee_id = e.id
                WHERE aea.area_id = ? AND aea.is_active = 1 
                AND e.staff_role LIKE '%ADMIN%' AND e.is_active = 1
                ORDER BY 
                    CASE aea.assignment_type WHEN 'PRIMARY' THEN 0 ELSE 1 END,
                    aea.start_date ASC,
                    e.id ASC
            ", [$areaId])->getResultArray();
            
            // Get ALL foremans for this area with priority sorting
            $foremans = $db->query("
                SELECT e.id, e.staff_name, aea.assignment_type, aea.start_date
                FROM area_employee_assignments aea
                JOIN employees e ON aea.employee_id = e.id
                WHERE aea.area_id = ? AND aea.is_active = 1 
                AND e.staff_role = 'FOREMAN' AND e.is_active = 1
                ORDER BY 
                    CASE aea.assignment_type WHEN 'PRIMARY' THEN 0 ELSE 1 END,
                    aea.start_date ASC,
                    e.id ASC
            ", [$areaId])->getResultArray();
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'admins' => $admins,
                    'foremans' => $foremans
                ]
            ]);
            
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data staff area'
            ]);
        }
    }

    // Populate sparepart data
    
    // Mendapatkan data work order untuk DataTable
    public function getWorkOrders()
    {
        $request = $this->request;
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchData = $request->getPost('search');
        $search = isset($searchData['value']) ? $searchData['value'] : '';
        
        // Tab filter parameter
        $tab = $request->getPost('tab') ?? $request->getGet('tab') ?? 'progress';
        
        // Phase 3: Check if optimized model should be used
        $useOptimized = $request->getPost('useOptimized') ?? $request->getGet('useOptimized') ?? false;
        
        // If optimized is requested, use optimized model
        if ($useOptimized) {
            return $this->getOptimizedWorkOrders($request, $draw, $start, $length, $search, $tab);
        }
        
        // Fallback to standard implementation
        return $this->getStandardWorkOrders($request, $draw, $start, $length, $search, $tab);
    }

    /**
     * Phase 3: Optimized work orders data method
     */
    protected function getOptimizedWorkOrders($request, $draw, $start, $length, $search, $tab)
    {
        try {
            // Load optimized model
            $optimizedModel = new \App\Models\OptimizedWorkOrderModel();
            
            // Pagination parameters - prevent division by zero
            $length = max(1, $length);
            $page = ($start / $length) + 1;
            
            // Get filters from request
            $status = $request->getPost('status') ?? $request->getGet('status');
            $priority = $request->getPost('priority') ?? $request->getGet('priority');
            $startDate = $request->getPost('start_date') ?? $request->getGet('start_date');
            $endDate = $request->getPost('end_date') ?? $request->getGet('end_date');
            $month = $request->getPost('month') ?? $request->getGet('month');
            
            // Apply tab-specific filtering
            if ($tab === 'closed') {
                $status = 'CLOSED'; // Status code for closed tab
            } elseif ($tab === 'progress') {
                // For progress tab, exclude closed status
                if (empty($status)) {
                    $status = 'exclude_closed';
                }
            }
            
            // Get user's scope: departments (CENTRAL) or areas (MILL)
            $woScope = get_user_area_department_scope();
            $allowedDepartments = null;
            $allowedAreas = null;
            if ($woScope !== null) {
                if (!empty($woScope['departments'])) {
                    $allowedDepartments = $woScope['departments'];
                } elseif (!empty($woScope['areas'])) {
                    $allowedAreas = $woScope['areas'];
                }
            }
            
            // Build filters array for optimized model
            $filters = [
                'search' => $search,
                'status' => $status,
                'priority' => $priority,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'month' => $month,
                'department_ids' => $allowedDepartments,
                'area_ids' => $allowedAreas
            ];

            // Get data from optimized model - FIXED: Use correct method name
            $result = $optimizedModel->getOptimizedWorkOrders($filters, $page, $length);
            
            // Format data for DataTable
            $formattedData = [];
            $no = $start + 1;
            
            foreach ($result['data'] as $wo) {
                $formattedData[] = $this->formatWorkOrderRow($wo, $no++, $tab);
            }

            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => $result['total'],
                'recordsFiltered' => $result['filtered'],
                'data' => $formattedData
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Optimized WorkOrders DataTable Error: ' . $e->getMessage());
            
            // Fallback to standard method on error
            return $this->getStandardWorkOrders($request, $draw, $start, $length, $search, $tab);
        }
    }
        
    /**
     * Standard work orders data method (existing implementation)
     */
    protected function getStandardWorkOrders($request, $draw, $start, $length, $search, $tab)
    {
        // Pagination parameters - prevent division by zero
        $length = max(1, $length); // Ensure length is at least 1
        $page = ($start / $length) + 1;
        $perPage = $length;
        
        // Status filter jika ada
        $status = $request->getPost('status') ?? $request->getGet('status');
        $priority = $request->getPost('priority') ?? $request->getGet('priority');
        $startDate = $request->getPost('start_date') ?? $request->getGet('start_date');
        $endDate = $request->getPost('end_date') ?? $request->getGet('end_date');
        $month = $request->getPost('month') ?? $request->getGet('month');
        
        // Apply tab-specific filtering
        if ($tab === 'closed') {
            $status = 'Closed'; // Force status to Closed for closed tab
        } elseif ($tab === 'progress') {
            // For progress tab, exclude closed status
            if (empty($status)) {
                $excludeStatus = 'Closed';
            }
        }
        
        // Mendapatkan data work order dengan filter
        $workOrders = $this->workOrderModel->searchWorkOrders(
            $search,
            $status,
            $priority,
            $startDate,
            $endDate,
            $month,
            isset($excludeStatus) ? $excludeStatus : null
        );
        // Apply unified scope filter (replaces deprecated get_user_division_departments)
        $woScope = get_user_area_department_scope();
        $allowedDepartments = null;
        $allowedAreaIds = null;
        if ($woScope !== null) {
            if (!empty($woScope['departments'])) {
                $allowedDepartments = $woScope['departments'];
            } elseif (!empty($woScope['areas'])) {
                $allowedAreaIds = $woScope['areas'];
            }
        }

        if ($allowedDepartments !== null) {
            // CENTRAL/ELECTRIC users: filter by unit's department
            $db = \Config\Database::connect();
            $unitIds = array_filter(array_column($workOrders, 'unit_id'));

            $unitsById = [];
            if (!empty($unitIds)) {
                $unitIdsStr = implode(',', array_map('intval', $unitIds));
                $unitsQuery = "SELECT id_inventory_unit, departemen_id FROM inventory_unit WHERE id_inventory_unit IN ($unitIdsStr)";
                $unitsResult = $db->query($unitsQuery)->getResultArray();
                $unitsById = array_column($unitsResult, 'departemen_id', 'id_inventory_unit');
            }

            $workOrders = array_filter($workOrders, function($wo) use ($allowedDepartments, $unitsById) {
                $unitDeptId = $unitsById[$wo['unit_id']] ?? null;
                return $unitDeptId && in_array($unitDeptId, $allowedDepartments);
            });
            $workOrders = array_values($workOrders);
        } elseif ($allowedAreaIds !== null) {
            // MILL users: filter by admin/foreman employee assigned to user's areas
            $db = \Config\Database::connect();
            $areaEmpResult = $db->table('area_employee_assignments')
                ->select('employee_id')
                ->whereIn('area_id', $allowedAreaIds)
                ->where('is_active', 1)
                ->get()->getResultArray();
            $areaEmpIds = array_column($areaEmpResult, 'employee_id');

            if (!empty($areaEmpIds)) {
                $workOrders = array_filter($workOrders, function($wo) use ($areaEmpIds) {
                    return in_array($wo['admin_id'], $areaEmpIds)
                        || in_array($wo['foreman_id'], $areaEmpIds)
                        || in_array($wo['mechanic_id'], $areaEmpIds);
                });
                $workOrders = array_values($workOrders);
            } else {
                $workOrders = [];
            }
        }

        // Total records untuk pagination
        $totalRecords = count($workOrders);

        // Use array_slice for pagination (acceptable since data is already filtered in memory)
        $filteredWorkOrders = array_slice($workOrders, $start, $length);
        
        $data = [];
        $no = $start + 1;
        
        foreach ($filteredWorkOrders as $row) {
            $data[] = $this->formatWorkOrderRow($row, $no++, $tab);
        }
        
        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, 
            'data' => $data
        ]);
    }

    /**
     * Format work order row for DataTable display
     */
    protected function formatWorkOrderRow($wo, $no, $tab)
    {
        // Dynamic action buttons based on status
        $action = $this->getStatusActionButton($wo['status_code'], $wo['id'], $wo['work_order_number']);
        
        $statusBadge = '<span class="badge bg-'.$wo['status_color'].'">'.$wo['status'].'</span>';
        $priorityBadge = '<span class="badge bg-'.$wo['priority_color'].'">'.$wo['priority'].'</span>';
        
        // Format unit info
        $unitInfo = $wo['no_unit'] . ' - ' . $wo['pelanggan'] . ' (' . $wo['merk_unit'] . ' ' . $wo['model_unit'] . ')';
        
        $row = [];
        $row[] = $no;
        $row[] = $wo['work_order_number'];
        $row[] = date('d/m/Y H:i', strtotime($wo['report_date']));
        $row[] = $unitInfo;
        $row[] = $wo['order_type'];
        $row[] = $priorityBadge;
        $row[] = $wo['category'];
        $row[] = $statusBadge;
        $row[] = $action;
        
        // For closed tab, add closed date as 9th column
        if ($tab === 'closed') {
            $closedDate = !empty($wo['closed_date']) ? date('d/m/Y H:i', strtotime($wo['closed_date'])) : '-';
            $row[] = $closedDate;
        }
        
        // Add data attributes for onclick functionality
        $row['DT_RowAttr'] = [
            'data-wo-id' => $wo['id'],
            'data-wo-number' => $wo['work_order_number'],
            'data-status-code' => $wo['status_code']
        ];
        
        return $row;
    }

    /**
     * Generate dynamic action buttons based on status
     */
    private function getStatusActionButton($statusCode, $woId, $woNumber = null)
    {
        $buttons = [];
        $woNumberAttr = $woNumber ? ' data-wo-number="'.$woNumber.'"' : '';
        
        switch ($statusCode) {
            case 'OPEN':
                $buttons[] = '<button type="button" class="btn btn-sm btn-primary btn-assign" data-id="'.$woId.'"'.$woNumberAttr.'>Start Work</button>';
                break;
                
            case 'PENDING':
                $buttons[] = '<button type="button" class="btn btn-sm btn-primary btn-assign" data-id="'.$woId.'"'.$woNumberAttr.'>Continue Work</button>';
                break;
                
            case 'ASSIGNED':
                $buttons[] = '<button type="button" class="btn btn-sm btn-success btn-start" data-id="'.$woId.'"'.$woNumberAttr.'>Continue Work</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-secondary btn-reassign" data-id="'.$woId.'"'.$woNumberAttr.'>Reassign</button>';
                break;
                
            case 'IN_PROGRESS':
                $buttons[] = '<button type="button" class="btn btn-sm btn-warning btn-pause" data-id="'.$woId.'"'.$woNumberAttr.'>Pending</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-success btn-complete" data-id="'.$woId.'"'.$woNumberAttr.'>Complete</button>';
                break;
                
            case 'WAITING_PARTS':
                $buttons[] = '<button type="button" class="btn btn-sm btn-primary btn-resume" data-id="'.$woId.'"'.$woNumberAttr.'>Resume</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-warning btn-pause" data-id="'.$woId.'"'.$woNumberAttr.'>Pending</button>';
                break;
                
            case 'TESTING':
                $buttons[] = '<button type="button" class="btn btn-sm btn-success btn-complete" data-id="'.$woId.'"'.$woNumberAttr.'>Complete</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-warning btn-back-to-progress" data-id="'.$woId.'"'.$woNumberAttr.'>Back to Progress</button>';
                break;
                
            case 'ON_HOLD':
            case 'WAITING_SCHEDULE':
            case 'WAITING_PERMIT':
            case 'WAITING_TOOLS':
            case 'OTHER_HOLD':
                $buttons[] = '<button type="button" class="btn btn-sm btn-primary btn-resume" data-id="'.$woId.'"'.$woNumberAttr.'>Resume</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-danger btn-cancel" data-id="'.$woId.'"'.$woNumberAttr.'>Cancel</button>';
                break;
                
            case 'COMPLETED':
                $buttons[] = '<button type="button" class="btn btn-sm btn-success btn-close-wo" data-id="'.$woId.'"'.$woNumberAttr.'>Close</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-warning btn-complete" data-id="'.$woId.'"'.$woNumberAttr.'>Re-Verif Unit</button>';
                break;
                
            case 'CLOSED':
                // No actions available for closed work orders
                $buttons[] = '<button type="button" class="btn btn-sm btn-secondary" disabled>Closed</button>';
                break;
                
            case 'CANCELLED':
                $buttons[] = '<button type="button" class="btn btn-sm btn-secondary" disabled>Cancelled</button>';
                break;
                
            default:
                $buttons[] = '<button type="button" class="btn btn-sm btn-secondary" disabled>No Action</button>';
                break;
        }
        
        return '<div class="btn-group-vertical btn-group-sm" role="group">' . implode(' ', $buttons) . '</div>';
    }
    
    // Update work order status
    public function updateStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }
        
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');
        
        if (!$id || !$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap. Harap isi semua field yang wajib.']);
        }
        
        try {
            // Get current status before update for history
            $currentWorkOrder = $this->workOrderModel->find($id);
            $fromStatusId = null;
            if ($currentWorkOrder && is_array($currentWorkOrder)) {
                $fromStatusId = $currentWorkOrder['status_id'];
            } elseif ($currentWorkOrder && is_object($currentWorkOrder)) {
                $fromStatusId = $currentWorkOrder->status_id ?? null;
            }
            
            // Get status ID based on status code
            $statusData = $this->workOrderModel->getStatusByCode($status);
            if (!$statusData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Status tidak valid']);
            }

            // Block COMPLETED/CLOSED if spareparts exist but not validated
            if (in_array($status, ['COMPLETED', 'CLOSED'])) {
                $dbCheck = \Config\Database::connect();
                $sparepartCount = $dbCheck->table('work_order_spareparts')
                    ->where('work_order_id', $id)
                    ->countAllResults();
                if ($sparepartCount > 0) {
                    $woData = $dbCheck->table('work_orders')
                        ->select('sparepart_validated')
                        ->where('id', $id)
                        ->get()
                        ->getRowArray();
                    if (!($woData['sparepart_validated'] ?? 0)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Work order ini memiliki sparepart yang belum divalidasi. Harap selesaikan validasi sparepart terlebih dahulu.'
                        ]);
                    }
                }
            }

            // Update work order status
            $updateData = [
                'status_id' => $statusData['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Add specific fields based on status
            switch ($status) {
                case 'ASSIGNED':
                    $updateData['assigned_date'] = date('Y-m-d H:i:s');
                    break;
                case 'IN_PROGRESS':
                    $updateData['start_date'] = date('Y-m-d H:i:s');
                    break;
                case 'COMPLETED':
                    $updateData['completed_date'] = date('Y-m-d H:i:s');
                    break;
                case 'CLOSED':
                    $updateData['closed_date'] = date('Y-m-d H:i:s');
                    // Revert unit workflow_status when WO is fully closed
                    if ($currentWorkOrder) {
                        $unitId = is_array($currentWorkOrder) ? ($currentWorkOrder['unit_id'] ?? null) : ($currentWorkOrder->unit_id ?? null);
                        if ($unitId) {
                            $this->revertUnitWorkflowStatus((int)$unitId, (int)$id);
                        }
                    }
                    break;
                case 'CANCELLED':
                    $updateData['cancelled_date'] = date('Y-m-d H:i:s');
                    // Revert unit workflow_status when WO is cancelled
                    if ($currentWorkOrder) {
                        $unitId = is_array($currentWorkOrder) ? ($currentWorkOrder['unit_id'] ?? null) : ($currentWorkOrder->unit_id ?? null);
                        if ($unitId) {
                            $this->revertUnitWorkflowStatus((int)$unitId, (int)$id);
                        }
                    }
                    break;
                case 'WAITING_PARTS':
                case 'WAITING_SCHEDULE':
                case 'WAITING_PERMIT':
                case 'WAITING_TOOLS':
                case 'OTHER_HOLD':
                    $updateData['hold_date'] = date('Y-m-d H:i:s');
                    break;
                case 'ON_HOLD':
                    $updateData['hold_date'] = date('Y-m-d H:i:s');
                    break;
            }
            
            $updated = $this->workOrderModel->update($id, $updateData);
            
            if ($updated) {
                // Add status history record with proper from_status_id
                log_message('debug', "Adding status history: WO ID=$id, From Status=$fromStatusId, To Status={$statusData['id']}, Notes=$notes");
                $historyResult = $this->workOrderModel->addStatusHistory($id, $statusData['id'], $notes, $fromStatusId);
                log_message('debug', "Status history result: " . ($historyResult ? 'SUCCESS' : 'FAILED'));
                
                if (!$historyResult) {
                    log_message('error', 'Failed to add status history for work order ' . $id);
                }
                
                // Send notification
                try {
                    helper('notification');
                    
                    // Get WO details
                    $db = \Config\Database::connect();
                    $woQuery = $db->query('
                        SELECT wo.work_order_number, iu.no_unit, wos.status_name as old_status
                        FROM work_orders wo
                        LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_inventory_unit
                        LEFT JOIN work_order_statuses wos ON wos.id = ?
                        WHERE wo.id = ?
                    ', [$fromStatusId, $id]);
                    $woInfo = $woQuery ? $woQuery->getRow() : null;
                    
                    notify_workorder_status_changed([
                        'id' => $id,
                        'wo_number' => $woInfo ? $woInfo->work_order_number : 'Unknown WO',
                        'unit_code' => $woInfo ? $woInfo->no_unit : 'Unknown Unit',
                        'old_status' => $woInfo ? $woInfo->old_status : 'Unknown',
                        'new_status' => $statusData['status_name'],
                        'updated_by' => session()->get('user_name') ?? 'System',
                        'url' => base_url('/service/work-order-detail/' . $id)
                    ]);
                    
                    log_message('info', "WorkOrder status updated: {$id} → {$status} - Notification sent");
                } catch (\Throwable $notifError) {
                    log_message('error', 'Failed to send workorder status notification: ' . $notifError->getMessage());
                }
                
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Status work order berhasil diubah'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Gagal mengubah status work order'
                ]);
            }
            
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }
    
    // Menampilkan form tambah work order
    public function create()
    {
        $data = [
            'title' => 'Create New Work Order',
            'statuses' => $this->workOrderModel->getStatuses(),
            'priorities' => $this->workOrderModel->getPriorities(),
            'categories' => $this->workOrderModel->getCategories(),
            'adminStaff' => $this->workOrderModel->getStaff('ADMIN'),
            'foremanStaff' => $this->workOrderModel->getStaff('FOREMAN'),
            'mechanicStaff' => $this->workOrderModel->getStaff('MECHANIC'),
            'helperStaff' => $this->workOrderModel->getStaff('HELPER')
        ];
        
        return view('service/work_order_form', $data);
    }
    
    /**
     * Dedicated Work Order Detail Page
     * Displays a full-page, rich detail view for a specific work order.
     */
    public function detail($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        try {
            $db = \Config\Database::connect();

            // Main WO data with joins
            $wo = $db->query("
                SELECT 
                    wo.*,
                    ws.status_name, ws.status_code, ws.color as status_color,
                    wp.priority_name, wp.color as priority_color,
                    wc.category_name,
                    wcs.subcategory_name,
                    iu.no_unit, iu.hm as unit_hm,
                    mu.merk_unit, tu.tipe as model_unit,
                    COALESCE(c.customer_name, 'Belum Ada Kontrak') as pelanggan,
                    cl.location_name,
                    a.area_name, a.area_code,
                    -- Staff
                    adm.staff_name as admin_name,
                    frm.staff_name as foreman_name,
                    mec.staff_name as mechanic_name,
                    hlp.staff_name as helper_name,
                    -- Dates
                    TIMESTAMPDIFF(HOUR, wo.report_date, IFNULL(wo.closed_date, NOW())) as ttr_hours
                FROM work_orders wo
                LEFT JOIN work_order_statuses ws  ON wo.status_id = ws.id
                LEFT JOIN work_order_priorities wp ON wo.priority_id = wp.id
                LEFT JOIN work_order_categories wc ON wo.category_id = wc.id
                LEFT JOIN work_order_subcategories wcs ON wo.subcategory_id = wcs.id
                LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_inventory_unit
                LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
                LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
                -- Updated: JOIN via kontrak_unit junction table (source of truth)
                LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
                LEFT JOIN kontrak k ON k.id = ku.kontrak_id
                LEFT JOIN customers c ON c.id = k.customer_id
                LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id
                LEFT JOIN areas a ON c.area_id = a.id
                LEFT JOIN employees adm ON wo.admin_id = adm.id
                LEFT JOIN employees frm ON wo.foreman_id = frm.id
                LEFT JOIN employees mec ON wo.mechanic_id = mec.id
                LEFT JOIN employees hlp ON wo.helper_id = hlp.id
                WHERE wo.id = ?
            ", [$id])->getRowArray();

            if (!$wo) {
                return redirect()->to('/service/work-orders')->with('error', 'Work Order tidak ditemukan.');
            }

            // Status history
            $statusHistory = $db->query("
                SELECT 
                    wsh.*,
                    ws_from.status_name as from_status,
                    ws_to.status_name as to_status,
                    ws_to.color as to_color
                FROM work_order_status_history wsh
                LEFT JOIN work_order_statuses ws_from ON wsh.from_status_id = ws_from.id
                LEFT JOIN work_order_statuses ws_to ON wsh.to_status_id = ws_to.id
                WHERE wsh.work_order_id = ?
                ORDER BY wsh.created_at ASC
            ", [$id])->getResultArray();

            // Spareparts used (with enhanced source tracking)
            $spareparts = $db->query("
                SELECT 
                    wos.*, 
                    COALESCE(wos.sparepart_code, s.kode) as sparepart_code,
                    COALESCE(wos.sparepart_name, s.desc_sparepart) as sparepart_name,
                    wos.source_type,
                    wos.source_unit_id,
                    wos.source_notes,
                    iu.no_unit as source_unit_number,
                    iu.no_unit_na as source_unit_number_alt
                FROM work_order_spareparts wos
                LEFT JOIN sparepart s ON wos.sparepart_id = s.id_sparepart
                LEFT JOIN inventory_unit iu ON wos.source_unit_id = iu.id_inventory_unit
                WHERE wos.work_order_id = ?
                ORDER BY wos.id ASC
            ", [$id])->getResultArray();

            // Additional mechanics/helpers (from assignments table if exists)
            $assignments = $db->query("
                SELECT woa.*, e.staff_name, e.staff_role
                FROM work_order_assignments woa
                JOIN employees e ON woa.employee_id = e.id
                WHERE woa.work_order_id = ?
                ORDER BY e.staff_role, e.staff_name
            ", [$id])->getResultArray();

            $data = [
                'title'         => 'WO Detail: ' . $wo['work_order_number'],
                'wo'            => $wo,
                'statusHistory' => $statusHistory,
                'spareparts'    => $spareparts,
                'assignments'   => $assignments,
            ];

            return view('service/work_order_detail', $data);

        } catch (\Throwable $e) {
            log_message('error', 'WorkOrder detail error: ' . $e->getMessage());
            return redirect()->to('/service/work-orders')
                ->with('error', 'Terjadi kesalahan saat memuat detail work order.');
        }
    }

    // Menyimpan data work order baru
    public function store()
    {
        try {
            // Handle both JSON and FormData input safely
            $input = [];
            
            // Try to get JSON first
            try {
                $jsonInput = $this->request->getJSON(true);
                if ($jsonInput) {
                    $input = $jsonInput;
                    log_message('debug', 'WO Store - Using JSON Input');
                }
            } catch (\Throwable $jsonError) {
                log_message('debug', 'JSON parsing failed (expected for form data): ' . $jsonError->getMessage());
            }
            
            // If no JSON, get POST data
            if (empty($input)) {
                $input = $this->request->getPost();
                log_message('debug', 'WO Store - Using POST Input');
            }
            
            log_message('debug', 'WO Store Final Input: ' . print_r($input, true));
            
            // Debug individual required fields (safe array handling)
            log_message('debug', 'WO Store Required Fields Check:');
            $unitIdLog = isset($input['unit_id']) ? (is_array($input['unit_id']) ? json_encode($input['unit_id']) : $input['unit_id']) : 'NULL';
            $orderTypeLog = isset($input['order_type']) ? (is_array($input['order_type']) ? json_encode($input['order_type']) : $input['order_type']) : 'NULL';
            $priorityIdLog = isset($input['priority_id']) ? (is_array($input['priority_id']) ? json_encode($input['priority_id']) : $input['priority_id']) : 'NULL';
            $categoryIdLog = isset($input['category_id']) ? (is_array($input['category_id']) ? json_encode($input['category_id']) : $input['category_id']) : 'NULL';
            $complaintLog = isset($input['complaint_description']) ? (is_array($input['complaint_description']) ? json_encode($input['complaint_description']) : substr($input['complaint_description'], 0, 100)) : 'NULL';
            
            log_message('debug', 'unit_id: ' . $unitIdLog . ' (empty: ' . (empty($input['unit_id']) ? 'YES' : 'NO') . ')');
            log_message('debug', 'order_type: ' . $orderTypeLog . ' (empty: ' . (empty($input['order_type']) ? 'YES' : 'NO') . ')');
            log_message('debug', 'priority_id: ' . $priorityIdLog . ' (empty: ' . (empty($input['priority_id']) ? 'YES' : 'NO') . ')');
            log_message('debug', 'category_id: ' . $categoryIdLog . ' (empty: ' . (empty($input['category_id']) ? 'YES' : 'NO') . ')');
            log_message('debug', 'complaint_description: ' . $complaintLog . ' (empty: ' . (empty($input['complaint_description']) ? 'YES' : 'NO') . ')');
            
            // Validation rules (removed status_id from required fields)
            $rules = [
                'unit_id' => 'required|integer',
                'order_type' => 'required',
                'priority_id' => 'required|integer',
                'category_id' => 'required|integer',
                'complaint_description' => 'required|min_length[3]' // Merubah validasi menjadi minimal 3 karakter
            ];
            
            $messages = [
                'unit_id' => [
                    'required' => 'Unit harus dipilih',
                    'integer' => 'Unit ID harus berupa angka'
                ],
                'order_type' => [
                    'required' => 'Tipe order harus dipilih'
                ],
                'priority_id' => [
                    'required' => 'Priority harus dipilih',
                    'integer' => 'Priority ID harus berupa angka'
                ],
                'category_id' => [
                    'required' => 'Kategori harus dipilih',
                    'integer' => 'Category ID harus berupa angka'
                ],
                'complaint_description' => [
                    'required' => 'Deskripsi keluhan harus diisi',
                    'min_length' => 'Deskripsi keluhan minimal 3 karakter' // Merubah pesan error sesuai validasi
                ]
            ];
            
            // Manual validation with improved logic
            $errors = [];
            
            // Required field validation
            if (empty($input['unit_id']) || !is_numeric($input['unit_id'])) {
                $errors['unit_id'] = 'Unit harus dipilih';
            }
            
            if (empty($input['order_type'])) {
                $errors['order_type'] = 'Tipe order harus dipilih';
            }
            
            if (empty($input['category_id']) || !is_numeric($input['category_id'])) {
                $errors['category_id'] = 'Kategori harus dipilih';
            }
            
            if (empty($input['complaint_description'])) {
                $errors['complaint_description'] = 'Deskripsi keluhan harus diisi';
            } elseif (strlen(trim($input['complaint_description'])) < 3) { // Merubah validasi menjadi minimal 3 karakter
                $errors['complaint_description'] = 'Deskripsi keluhan minimal 3 karakter';
            }
            
            // Priority validation - allow auto-generation if not provided
            if (empty($input['priority_id']) || !is_numeric($input['priority_id'])) {
                // Try to get default priority from category
                if (!empty($input['category_id'])) {
                    $db = \Config\Database::connect();
                    $category = $db->table('work_order_categories')
                                  ->where('id', $input['category_id'])
                                  ->get()
                                  ->getRow();
                    
                    if ($category && !empty($category->default_priority_id)) {
                        $input['priority_id'] = $category->default_priority_id;
                        $priorityLog = is_array($input['priority_id']) ? json_encode($input['priority_id']) : $input['priority_id'];
                        log_message('debug', 'Auto-assigned priority_id: ' . $priorityLog);
                    } else {
                        // Default to priority 1 (LOW) if no default priority set
                        $input['priority_id'] = 1;
                        log_message('debug', 'Auto-assigned default priority_id: 1');
                    }
                }
            }
            
            if (!empty($errors)) {
                log_message('debug', 'Manual validation failed with errors: ' . print_r($errors, true));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa kembali data yang diisi.',
                    'errors' => $errors
                ]);
            }
            
            // Check if unit already has open work order (not CLOSED)
            $db = \Config\Database::connect();
            $existingWO = $db->table('work_orders wo')
                ->select('wo.work_order_number, wo.id, s.status_name')
                ->join('work_order_statuses s', 'wo.status_id = s.id', 'left')
                ->where('wo.unit_id', $input['unit_id'])
                ->where('wo.deleted_at', null)
                ->whereNotIn('s.status_code', ['CLOSED', 'COMPLETED']) // Exclude completed statuses
                ->orderBy('wo.id', 'DESC')
                ->limit(1)
                ->get()
                ->getRow();
            
            if ($existingWO) {
                log_message('debug', 'Unit already has open WO: ' . print_r($existingWO, true));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit ini sudah memiliki Work Order yang masih aktif (' . $existingWO->work_order_number . ' - Status: ' . $existingWO->status_name . '). Harap selesaikan Work Order tersebut hingga CLOSED sebelum membuat Work Order baru.',
                    'existing_wo' => [
                        'id' => $existingWO->id,
                        'number' => $existingWO->work_order_number,
                        'status' => $existingWO->status_name
                    ]
                ]);
            }
            
            // Get default status (try multiple options)
            $db = \Config\Database::connect();
            $defaultStatus = $db->table('work_order_statuses')
                               ->where('is_active', 1)
                               ->whereIn('status_code', ['PENDING', 'OPEN', 'NEW'])
                               ->orderBy('sort_order')
                               ->limit(1)
                               ->get()
                               ->getRow();
            
            // If still no status, get the first active status
            if (!$defaultStatus) {
                $defaultStatus = $db->table('work_order_statuses')
                                   ->where('is_active', 1)
                                   ->orderBy('sort_order')
                                   ->limit(1)
                                   ->get()
                                   ->getRow();
            }
            
            if (!$defaultStatus) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No active work order status found. Please contact administrator to setup work order statuses.'
                ]);
            }

            // Validate session user_id before INSERT — work_orders.created_by is NOT NULL FK → users.id
            $creatorId = (int)session()->get('user_id');
            if ($creatorId <= 0 || !$db->table('users')->where('id', $creatorId)->countAllResults()) {
                log_message('error', "[WorkOrder] createWorkOrder rejected: user_id={$creatorId} missing or not in users table");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session tidak valid. Silakan login ulang sebelum membuat Work Order.'
                ]);
            }
            
            // Generate work order number
            $woNumber = $this->generateWorkOrderNumber();
            
            // Get unit area info for area-based assignment
            $unitAreaInfo = $this->getUnitAreaInfo($input['unit_id']);
            log_message('debug', 'Unit Area Info: ' . print_r($unitAreaInfo, true));
            
            // Use admin_id and foreman_id from user selection (from dropdown)
            // Fallback to auto-assignment only if not provided
            // Ensure empty strings are converted to NULL for foreign key constraints
            $adminId = !empty($input['admin_id']) ? $input['admin_id'] : null;
            $foremanId = !empty($input['foreman_id']) ? $input['foreman_id'] : null;
            
            // Get mechanic and helper IDs
            $mechanicIds = $input['mechanic_id'] ?? [];
            $helperIds = $input['helper_id'] ?? [];
            
            // Filter out empty values
            $mechanicIds = array_filter($mechanicIds, function($id) { return !empty($id); });
            $helperIds = array_filter($helperIds, function($id) { return !empty($id); });
            
            // Validation: Check for duplicate mechanics
            if (count($mechanicIds) !== count(array_unique($mechanicIds))) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak dapat memilih mekanik yang sama lebih dari sekali dalam satu Work Order.'
                ]);
            }
            
            // Validation: Check for duplicate helpers
            if (count($helperIds) !== count(array_unique($helperIds))) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak dapat memilih helper yang sama lebih dari sekali dalam satu Work Order.'
                ]);
            }
            
            // If not provided, try auto-assignment from area
            if (!$adminId || !$foremanId || empty($mechanicIds) || empty($helperIds)) {
                $areaStaffInfo = $this->getAreaStaffInfo($input['unit_id']);
                log_message('debug', 'Area Staff Info for auto-assignment: ' . print_r($areaStaffInfo, true));
                
                $missingRoles = [];
                
                if ($areaStaffInfo && is_array($areaStaffInfo)) {
                    foreach ($areaStaffInfo as $staff) {
                        if (!$adminId && $staff['staff_role'] === 'ADMIN') {
                            $adminId = $staff['id'];
                        }
                        if (!$foremanId && $staff['staff_role'] === 'FOREMAN') {
                            $foremanId = $staff['id'];
                        }
                    }
                }
                
                // HYBRID VALIDATION: Only fail if REQUIRED staff (Mechanic/Helper) are missing
                // Admin and Foreman can be empty if user doesn't manually select them
                
                // Check required staff only (Mechanic & Helper)
                if (empty($mechanicIds)) {
                    $missingRoles[] = 'Mechanic (minimal 1)';
                }
                if (empty($helperIds)) {
                    $missingRoles[] = 'Helper (minimal 1)';
                }
                
                // Only fail if mechanic or helper is missing
                if (!empty($missingRoles)) {
                    $areaName = $unitAreaInfo['area_name'] ?? 'Unknown';
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Staff tidak lengkap. Staff yang harus diisi: ' . implode(', ', $missingRoles) . '. Harap pilih minimal 1 mechanic dan 1 helper.',
                        'missing_roles' => $missingRoles,
                        'area_name' => $areaName
                    ]);
                }
                
                // WARN: If admin/foreman still empty after auto-assignment, log warning but allow creation
                if (!$adminId) {
                    $unitIdLog = is_array($input['unit_id']) ? json_encode($input['unit_id']) : $input['unit_id'];
                    log_message('warning', 'Work Order created without Admin for unit_id: ' . $unitIdLog . ' (Area: ' . ($unitAreaInfo['area_name'] ?? 'Unknown') . ')');
                }
                if (!$foremanId) {
                    $unitIdLog = is_array($input['unit_id']) ? json_encode($input['unit_id']) : $input['unit_id'];
                    log_message('warning', 'Work Order created without Foreman for unit_id: ' . $unitIdLog . ' (Area: ' . ($unitAreaInfo['area_name'] ?? 'Unknown') . ')');
                }
            }
            
            // Safe logging: Handle if admin/foreman are arrays (defensive coding)
            $adminIdLog = is_array($adminId) ? json_encode($adminId) : ($adminId ?? 'NULL');
            $foremanIdLog = is_array($foremanId) ? json_encode($foremanId) : ($foremanId ?? 'NULL');
            log_message('debug', 'Final admin_id: ' . $adminIdLog . ', foreman_id: ' . $foremanIdLog);
            
            // Ensure admin_id and foreman_id are scalar values (fix potential Select2 array issue)
            if (is_array($adminId)) {
                log_message('warning', 'admin_id received as array, taking first value: ' . json_encode($adminId));
                $adminId = !empty($adminId) ? reset($adminId) : null;
            }
            if (is_array($foremanId)) {
                log_message('warning', 'foreman_id received as array, taking first value: ' . json_encode($foremanId));
                $foremanId = !empty($foremanId) ? reset($foremanId) : null;
            }
            
            // Sanitize ALL scalar input fields - ensure they're not arrays
            $scalarFields = ['unit_id', 'order_type', 'priority_id', 'requested_repair_time', 
                            'category_id', 'subcategory_id', 'complaint_description', 'pic', 'hm'];
            
            foreach ($scalarFields as $field) {
                if (isset($input[$field]) && is_array($input[$field])) {
                    log_message('warning', $field . ' received as array, taking first value: ' . json_encode($input[$field]));
                    $input[$field] = !empty($input[$field]) ? reset($input[$field]) : null;
                }
            }
            
            $data = [
                'work_order_number' => $woNumber,
                'report_date' => date('Y-m-d H:i:s'),
                'unit_id' => $input['unit_id'] ?? null,
                'order_type' => $input['order_type'] ?? null,
                'priority_id' => $input['priority_id'] ?? null,
                'requested_repair_time' => $input['requested_repair_time'] ?? null,
                'category_id' => $input['category_id'] ?? null,
                'subcategory_id' => $input['subcategory_id'] ?? null,
                'complaint_description' => $input['complaint_description'] ?? null,
                'status_id' => $defaultStatus->id, // Set default status (OPEN)
                // Use admin and foreman from user selection or auto-assignment
                'admin_id' => $adminId,
                'foreman_id' => $foremanId,
                'mechanic_id' => null, // Will be assigned during assignment process
                'helper_id' => null,   // Will be assigned during assignment process
                'area' => $unitAreaInfo['area_name'] ?? null,
                'pic' => $input['pic'] ?? null,
                'hm' => $input['hm'] ?? null,
                'created_by' => $creatorId
            ];
            
            // Start transaction for work order and spareparts
            $db = \Config\Database::connect();
            $db->transStart();
            
            try {
                log_message('debug', 'Inserting work order data: ' . print_r($data, true));
                $result = $this->workOrderModel->insert($data);
                log_message('debug', 'Insert result: ' . $result);
                
                if (!$result) {
                    $errors = $this->workOrderModel->errors();
                    log_message('error', 'Model errors: ' . print_r($errors, true));
                    // Safely format errors (handle nested arrays)
                    $errorMessages = [];
                    foreach ($errors as $field => $message) {
                        if (is_array($message)) {
                            $errorMessages[] = $field . ': ' . implode('; ', array_map('strval', $message));
                        } else {
                            $errorMessages[] = is_string($message) ? $message : $field . ': ' . json_encode($message);
                        }
                    }
                    throw new \Exception('Gagal menyimpan work order: ' . implode(', ', $errorMessages));
                }

                // Add initial status history for new work order
                $this->workOrderModel->addStatusHistory(
                    $result, 
                    $defaultStatus->id, 
                    'Work order created with initial status',
                    null // from_status_id is null for new work orders
                );
                log_message('debug', 'Initial status history added for work order: ' . $result);

                // Handle staff assignment if provided - using direct columns
                $mechanicIds = $input['mechanic_id'] ?? [];
                $helperIds = $input['helper_id'] ?? [];
                
                if (!empty($mechanicIds) || !empty($helperIds)) {
                    log_message('debug', 'Assigning staff - Mechanics: ' . print_r($mechanicIds, true) . ', Helpers: ' . print_r($helperIds, true));
                    
                    // Get first mechanic and helper (for backward compatibility)
                    $firstMechanic = null;
                    $firstHelper = null;
                    
                    foreach ($mechanicIds as $mechanicId) {
                        if (!empty($mechanicId)) {
                            $firstMechanic = $mechanicId;
                            break;
                        }
                    }
                    
                    foreach ($helperIds as $helperId) {
                        if (!empty($helperId)) {
                            $firstHelper = $helperId;
                            break;
                        }
                    }
                    
                    // Update work order with first mechanic and helper
                    if ($firstMechanic || $firstHelper) {
                        $updateData = [];
                        if ($firstMechanic) $updateData['mechanic_id'] = $firstMechanic;
                        if ($firstHelper) $updateData['helper_id'] = $firstHelper;
                        
                        $this->workOrderModel->update($result, $updateData);
                        log_message('debug', 'Staff assigned during work order creation');
                    }
                }
                
                // Handle spareparts if provided
                if (!empty($input['sparepart_name']) && is_array($input['sparepart_name'])) {
                    log_message('debug', 'Processing spareparts: ' . print_r($input['sparepart_name'], true));
                    
                    // Load work order sparepart model
                    $sparepartModel = new \App\Models\WorkOrderSparepartModel();
                    
                    // Prepare spareparts array
                    $spareparts = [];
                    $sparepartNames = $input['sparepart_name'] ?? [];
                    $sparepartQuantities = $input['sparepart_quantity'] ?? [];
                    $sparepartUnits = $input['sparepart_unit'] ?? [];
                    $sparepartNotes = $input['sparepart_notes'] ?? [];
                    
                    // Enhanced source tracking
                    $sourceTypes = $input['source_type'] ?? []; // WAREHOUSE, BEKAS, KANIBAL
                    $sourceUnitIds = $input['source_unit_id'] ?? [];
                    $sourceNotes = $input['source_notes'] ?? [];
                    $itemTypes = $input['item_type'] ?? [];
                    
                    for ($i = 0; $i < count($sparepartNames); $i++) {
                        // Safety: skip if empty or if value is an array (shouldn't happen but defensive)
                        $currentName = $sparepartNames[$i];
                        if (is_array($currentName)) {
                            log_message('warning', "sparepart_name[{$i}] received as array, skipping: " . json_encode($currentName));
                            continue;
                        }
                        if (!empty($currentName)) {
                            $currentName = trim((string)$currentName);
                            // Check if format is "KODE - NAMA" (from dropdown) or manual entry (no separator)
                            if (strpos($currentName, ' - ') !== false) {
                                // Dropdown selection: Parse format "KODE - NAMA"
                                $parts = explode(' - ', $currentName, 2);
                                $sparepartCode = trim($parts[0]);
                                $sparepartName = trim($parts[1]);
                            } else {
                                // Manual entry: No code, just name
                                $sparepartCode = null; // Will be NULL in database
                                $sparepartName = $currentName;
                                log_message('info', "[WO Created] Manual sparepart entry: {$sparepartName}");
                            }
                            
                            // Get source type (default: WAREHOUSE) - ensure scalar
                            $sourceType = $sourceTypes[$i] ?? 'WAREHOUSE';
                            if (is_array($sourceType)) $sourceType = reset($sourceType) ?: 'WAREHOUSE';
                            
                            // Validation: KANIBAL must have source_unit_id - ensure scalar
                            $sourceUnitId = !empty($sourceUnitIds[$i]) ? $sourceUnitIds[$i] : null;
                            if (is_array($sourceUnitId)) $sourceUnitId = reset($sourceUnitId) ?: null;
                            if ($sourceType === 'KANIBAL' && !$sourceUnitId) {
                                throw new \Exception("Sparepart KANIBAL '{$sparepartName}' harus memiliki unit sumber. Pilih unit asal copotan.");
                            }
                            
                            // Validation: WAREHOUSE source should not allow manual entry (optional - can be removed if user wants flexibility)
                            // if ($sourceType === 'WAREHOUSE' && $sparepartCode === null) {
                            //     throw new \Exception("Sparepart WAREHOUSE harus dipilih dari dropdown. Manual entry hanya untuk BEKAS/KANIBAL.");
                            // }
                            
                            // Set is_from_warehouse for backward compatibility
                            $isFromWarehouse = ($sourceType === 'WAREHOUSE') ? 1 : 0;
                            
                            // Ensure all values are scalar before inserting
                            $qty = $sparepartQuantities[$i] ?? 1;
                            $satuan = $sparepartUnits[$i] ?? 'pcs';
                            $notes = $sparepartNotes[$i] ?? null;
                            $srcNote = $sourceNotes[$i] ?? null;
                            $itemType = $itemTypes[$i] ?? 'sparepart';
                            if (is_array($qty)) $qty = reset($qty) ?: 1;
                            if (is_array($satuan)) $satuan = reset($satuan) ?: 'pcs';
                            if (is_array($notes)) $notes = reset($notes) ?: null;
                            if (is_array($srcNote)) $srcNote = reset($srcNote) ?: null;
                            if (is_array($itemType)) $itemType = reset($itemType) ?: 'sparepart';
                            
                            $spareparts[] = [
                                'sparepart_code' => $sparepartCode, // NULL for manual entries
                                'sparepart_name' => $sparepartName,
                                'item_type' => $itemType,
                                'quantity_brought' => $qty,
                                'satuan' => $satuan,
                                'notes' => $notes,
                                // Enhanced source tracking
                                'source_type' => $sourceType,
                                'source_unit_id' => $sourceUnitId,
                                'source_notes' => $srcNote,
                                // Backward compatibility
                                'is_from_warehouse' => $isFromWarehouse
                            ];
                        }
                    }
                    
                    if (!empty($spareparts)) {
                        $sparepartsAdded = $sparepartModel->addSpareparts($result, $spareparts);
                        if (!$sparepartsAdded) {
                            throw new \Exception('Gagal menyimpan data sparepart');
                        }
                        log_message('info', '[WO Created] Added ' . count($spareparts) . ' spareparts with enhanced source tracking');
                    }
                }
                
                // Snapshot current unit status before setting MAINTENANCE_IN_PROGRESS
                $preWoUnit = $db->table('inventory_unit')
                    ->select('workflow_status, status_unit_id')
                    ->where('id_inventory_unit', (int)$input['unit_id'])
                    ->get()->getRowArray();
                $preWoWorkflowStatus  = $preWoUnit['workflow_status']  ?? null;
                $preWoStatusUnitId    = $preWoUnit['status_unit_id']   ?? null;

                // Update unit to in-progress maintenance status for WO lifecycle lock
                $db->table('inventory_unit')
                    ->where('id_inventory_unit', (int)$input['unit_id'])
                    ->update([
                        'workflow_status' => 'MAINTENANCE_IN_PROGRESS',
                        'status_unit_id'  => 11, // MAINTENANCE (in progress)
                    ]);
                log_message('info', 'Unit ' . $input['unit_id'] . ' set to MAINTENANCE_IN_PROGRESS with status_unit_id=11');

                // Persist snapshot on the WO row so every terminal path can restore it
                $db->table('work_orders')
                    ->where('id', $result)
                    ->update([
                        'pre_wo_workflow_status' => $preWoWorkflowStatus,
                        'pre_wo_status_unit_id'  => $preWoStatusUnitId,
                    ]);

                $db->transComplete();
                
                if ($db->transStatus() === false) {
                    throw new \Exception('Transaksi database gagal');
                }
                
                // Send notification
                try {
                    helper('notification');
                    
                    // Get created WO details
                    $woQuery = $db->query('
                        SELECT wo.work_order_number, iu.no_unit, wo.order_type, 
                               p.priority_name, c.category_name, wo.complaint_description
                        FROM work_orders wo
                        LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_inventory_unit
                        LEFT JOIN work_order_priorities p ON wo.priority_id = p.id
                        LEFT JOIN work_order_categories c ON wo.category_id = c.id
                        WHERE wo.id = ?
                    ', [$result]);
                    $woInfo = $woQuery->getRow();
                    
                    notify_workorder_created([
                        'id' => $result,
                        'wo_number' => $woNumber,
                        'unit_code' => $woInfo ? $woInfo->no_unit : 'Unknown Unit',
                        'order_type' => $woInfo ? $woInfo->order_type : 'Unknown',
                        'priority' => $woInfo ? $woInfo->priority_name : 'Unknown',
                        'category' => $woInfo ? $woInfo->category_name : 'Unknown',
                        'complaint' => $woInfo ? $woInfo->complaint_description : 'N/A',
                        'created_by' => session()->get('user_name') ?? 'System',
                        'url' => base_url('/service/work-order-detail/' . $result)
                    ]);
                    
                    log_message('info', "WorkOrder created: {$woNumber} - Notification sent");
                } catch (\Throwable $notifError) {
                    log_message('error', 'Failed to send workorder creation notification: ' . $notifError->getMessage());
                }
                
                // Log to system_activity_log
                $this->logCreate('work_orders', $result, $data, [
                    'module_name' => 'service',
                    'description' => "Work Order {$woNumber} created",
                    'business_impact' => 'MEDIUM',
                ]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Work Order berhasil dibuat dengan nomor: ' . $woNumber,
                    'data' => [
                        'id' => $result,
                        'work_order_number' => $woNumber,
                        'unit_area' => $unitAreaInfo['area_name'] ?? 'Unknown',
                        'spareparts_count' => count($input['sparepart_name'] ?? []),
                        'assigned_staff' => [
                            'admin_id' => $adminId ?? null,
                            'foreman_id' => $foremanId ?? null,
                            'mechanic_id' => $firstMechanic ?? null,
                            'helper_id' => $firstHelper ?? null
                        ]
                    ]
                ]);
                
            } catch (\Throwable $e) {
                $db->transRollback();
                throw $e;
            }
            
        } catch (\Throwable $e) {
            log_message('error', 'Error in WorkOrderController::store - [' . get_class($e) . '] ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.'
            ]);
        }
    }
    
    
    // Menyimpan perubahan data work order
    public function update($id)
    {
        $rules = [
            'unit_id' => 'required|integer',
            'order_type' => 'required',
            'priority_id' => 'required|integer',
            'category_id' => 'required|integer',
            'complaint_description' => 'required|min_length[5]',
            'status_id' => 'required|integer'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang diisi.',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        // Dapatkan status sebelumnya
        $prevData = $this->workOrderModel->find($id);
        $oldStatusId = $prevData['status_id'];
        $newStatusId = $this->request->getPost('status_id');
        
        $data = [
            'unit_id' => $this->request->getPost('unit_id'),
            'order_type' => $this->request->getPost('order_type'),
            'priority_id' => $this->request->getPost('priority_id'),
            'requested_repair_time' => $this->request->getPost('requested_repair_time'),
            'category_id' => $this->request->getPost('category_id'),
            'subcategory_id' => $this->request->getPost('subcategory_id'),
            'complaint_description' => $this->request->getPost('complaint_description'),
            'status_id' => $newStatusId,
            'admin_id' => $this->request->getPost('admin_id'),
            'foreman_id' => $this->request->getPost('foreman_id'),
            'mechanic_id' => $this->request->getPost('mechanic_id'),
            'helper_id' => $this->request->getPost('helper_id'),
            'repair_description' => $this->request->getPost('repair_description'),
            'notes' => $this->request->getPost('notes'),
            'sparepart_used' => $this->request->getPost('sparepart_used'),
            'time_to_repair' => $this->request->getPost('time_to_repair'),
            'area' => $this->request->getPost('area')
        ];
        
        // Jika status berubah menjadi completed, validasi sparepart dulu
        if ($oldStatusId != $newStatusId && $newStatusId == 6) {
            $db = \Config\Database::connect();
            $sparepartCount = $db->table('work_order_spareparts')
                ->where('work_order_id', $id)
                ->countAllResults();
            if ($sparepartCount > 0) {
                $woData = $db->table('work_orders')
                    ->select('sparepart_validated')
                    ->where('id', $id)
                    ->get()
                    ->getRowArray();
                if (!($woData['sparepart_validated'] ?? 0)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Work order ini memiliki sparepart yang belum divalidasi. Harap selesaikan validasi sparepart terlebih dahulu.'
                    ]);
                }
            }
            $data['completion_date'] = date('Y-m-d H:i:s');
        }
        
        $result = $this->workOrderModel->update($id, $data);
        
        // Jika status berubah, tambahkan riwayat perubahan status
        if ($oldStatusId != $newStatusId) {
            $this->workOrderModel->addStatusHistory($id, $newStatusId, 'Status updated');
        }
        
        if ($result) {
            // Log to system_activity_log
            $this->logUpdate('work_orders', $id, $prevData, $data, [
                'module_name' => 'service',
                'description' => "Work Order #{$id} updated",
                'business_impact' => 'MEDIUM',
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Work Order berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate Work Order',
                'errors' => $this->workOrderModel->errors()
            ]);
        }
    }
    
    // Menghapus data work order
    public function delete($id)
    {
        try {
            // Validate ID
            if (empty($id) || !is_numeric($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID Work Order tidak valid'
                ]);
            }
            
            // Check if work order exists
            $workOrder = $this->workOrderModel->find($id);
            if (!$workOrder) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work Order tidak ditemukan'
                ]);
            }
            
            // Get status information
            $statusModel = new \App\Models\WorkOrderStatusModel();
            $status = $statusModel->find($workOrder['status_id']);
            
            // Check if work order can be deleted (not completed or closed)
            if ($status && in_array($status['status_code'], ['COMPLETED', 'CLOSED'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work Order yang sudah selesai atau ditutup tidak dapat dihapus'
                ]);
            }
            
            // Perform soft delete
            $result = $this->workOrderModel->delete($id);
            
            if ($result) {
                // Log to system_activity_log
                $this->logDelete('work_orders', $id, $workOrder, [
                    'module_name' => 'service',
                    'description' => "Work Order {$workOrder['work_order_number']} deleted",
                    'business_impact' => 'HIGH',
                    'is_critical' => true,
                ]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Work Order "' . ($workOrder['work_order_number'] ?? 'WO-' . $id) . '" berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus Work Order',
                    'errors' => $this->workOrderModel->errors()
                ]);
            }
            
        } catch (\Throwable $e) {
            log_message('error', 'Error in WorkOrderController::delete - ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.'
            ]);
        }
    }
    
    // Mendapatkan subcategories berdasarkan category_id (untuk dropdown dinamis)
    public function getSubcategories()
    {
        // Handle both GET and POST requests
        $categoryId = $this->request->getGet('category_id') ?: $this->request->getPost('category_id');
        
        if (empty($categoryId)) {
            return $this->response->setJSON([
                'success' => false,
                'status' => false,
                'message' => 'Category ID is required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            $subcategories = $db->table('work_order_subcategories')
                ->select('id, subcategory_name, subcategory_code')
                ->where('category_id', $categoryId)
                ->where('is_active', 1)
                ->orderBy('subcategory_name', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'status' => true,
                'data' => $subcategories
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'status' => false,
                'message' => 'Gagal memuat subkategori'
            ]);
        }
    }
    
    // Mendapatkan detail work order (untuk modal)
    public function view($id)
    {
        $workOrder = $this->workOrderModel->getDetailWorkOrder($id);
        
        if (!$workOrder) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Work Order tidak ditemukan'
            ]);
        }
        
        log_message('debug', "=== SPAREPART USAGE MAPPING DEBUG ===");
        log_message('debug', "Work Order ID: {$id}");
        
        // Add usage status to spareparts if they exist
        if (!empty($workOrder['spareparts'])) {
            log_message('debug', "Spareparts count: " . count($workOrder['spareparts']));
            
            // Add usage info to each sparepart based on quantity_used field
            foreach ($workOrder['spareparts'] as &$sparepart) {
                log_message('debug', "Checking sparepart ID: {$sparepart['id']} Name: {$sparepart['name']}");
                
                $qtyBrought = (int)($sparepart['qty'] ?? 0);
                $qtyUsed = (int)($sparepart['quantity_used'] ?? 0);
                
                log_message('debug', "  Qty Brought: $qtyBrought, Qty Used: $qtyUsed");
                
                // Determine status based on quantity_used
                if ($qtyUsed > 0) {
                    $sparepart['is_used'] = 1; // Used
                    $sparepart['used_quantity'] = $qtyUsed;
                    
                    // Check if there's a return (used < brought)
                    if ($qtyUsed < $qtyBrought) {
                        $qtyReturned = $qtyBrought - $qtyUsed;
                        $sparepart['is_used'] = 0; // Has return
                        $sparepart['returned_quantity'] = $qtyReturned;
                        log_message('debug', "  ✓ Status: RETURNED (used: {$qtyUsed}, returned: {$qtyReturned})");
                    } else {
                        log_message('debug', "  ✓ Status: USED (all used: {$qtyUsed})");
                    }
                } else {
                    log_message('debug', "  ✗ Status: PENDING (not validated)");
                    // Leave is_used undefined for pending status
                }
            }
            unset($sparepart); // Break reference
            
            log_message('debug', "Final spareparts data: " . json_encode($workOrder['spareparts']));
        }
        
        log_message('debug', "=== END DEBUG ===");
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $workOrder
        ]);
    }
    
    // Mendapatkan detail work order (untuk modal) - alias untuk backward compatibility
    public function getDetail($id)
    {
        return $this->view($id);
    }
    
    // Print work order
    public function print($id)
    {
        $workOrder = $this->workOrderModel->getDetailWorkOrder($id);
        
        if (!$workOrder) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Work Order tidak ditemukan'
            ]);
        }
        
        return view('service/print_work_order', [
            'workOrder' => $workOrder
        ]);
    }
    
    // Edit work order
    public function edit($id)
    {
        try {
            // Get work order details
            $workOrder = $this->workOrderModel->getDetailWorkOrder($id);
            
            if (!$workOrder) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work Order tidak ditemukan'
                ]);
            }

            // Get spareparts for this work order
            $db = \Config\Database::connect();
            $spareparts = $db->table('work_order_spareparts wos')
                ->select('wos.sparepart_name, wos.quantity_brought as quantity, wos.satuan as unit, wos.sparepart_code')
                ->where('wos.work_order_id', $id)
                ->get()
                ->getResultArray();
            
            // Get form data for editing
            $data = [
                'workOrder' => $workOrder,
                'spareparts' => $spareparts, // Work order specific spareparts
                'statuses' => $this->workOrderModel->getStatuses(),
                'priorities' => $this->workOrderModel->getPriorities(),
                'categories' => $this->workOrderModel->getCategories(),
                'units' => $this->getUnits(),
                'areas' => $this->areaModel->getActiveAreas(),
                'sparepartOptions' => $this->getSparepartsForDropdown(), // All available spareparts for dropdown
                'staff' => [
                    'ADMIN' => $this->workOrderModel->getStaff('ADMIN'),
                    'FOREMAN' => $this->workOrderModel->getStaff('FOREMAN'),
                    'MECHANIC' => $this->workOrderModel->getStaff('MECHANIC'),
                    'HELPER' => $this->workOrderModel->getStaff('HELPER')
                ]
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Error in WorkOrderController::edit - ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }
    
    // Mendapatkan statistik work order
    public function getStats()
    {
        $stats = $this->workOrderModel->getWorkOrderStats();
        
        return $this->response->setJSON([
            'status' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Auto assign employees based on unit's area
     */

    
    /**
     * Get unit area information for debugging
     */
    private function getUnitAreaInfo($unitId)
    {
        try {
            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    iu.area_id,
                    c.customer_name,
                    a.area_name,
                    a.area_code,
                    d.nama_departemen
                FROM inventory_unit iu
                LEFT JOIN areas a ON iu.area_id = a.id
                -- Updated: JOIN via kontrak_unit junction table (source of truth)
                LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
                LEFT JOIN kontrak k ON k.id = ku.kontrak_id
                LEFT JOIN customers c ON c.id = k.customer_id
                LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
                WHERE iu.id_inventory_unit = ?
            ", [$unitId]);
            
            return $query->getRowArray();
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return null;
        }
    }

    /**
     * Get area staff information for work order assignment
     */
    /**
     * Revert unit workflow/status after Work Order reaches a terminal state.
     *
     * Priority:
     *   1. Restore from pre_wo_workflow_status + pre_wo_status_unit_id snapshot stored on the WO row.
     *   2. Fallback: derive from active contract presence (DISEWA / TERSEDIA).
     *
     * @param int      $unitId   inventory_unit.id_inventory_unit
     * @param int|null $woId     work_orders.id (used to read snapshot)
     */
    private function revertUnitWorkflowStatus(int $unitId, ?int $woId = null): void
    {
        $db = \Config\Database::connect();

        // Attempt to restore from snapshot
        $revertStatus = null;
        $revertStatusUnitId = null;
        if ($woId) {
            $snap = $db->table('work_orders')
                ->select('pre_wo_workflow_status, pre_wo_status_unit_id')
                ->where('id', $woId)
                ->get()->getRowArray();
            if (!empty($snap['pre_wo_workflow_status'])) {
                $revertStatus = $snap['pre_wo_workflow_status'];
            }
            if (!empty($snap['pre_wo_status_unit_id'])) {
                $revertStatusUnitId = (int) $snap['pre_wo_status_unit_id'];
            }
        }

        // Fallback: derive from active contract if no snapshot available
        if ($revertStatus === null) {
            $hasActiveContract = $db->table('kontrak_unit')
                ->where('unit_id', $unitId)
                ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->where('is_temporary', 0)
                ->countAllResults() > 0;
            $revertStatus = $hasActiveContract ? 'DISEWA' : 'TERSEDIA';
            log_message('info', "Unit {$unitId}: no pre_wo snapshot found, deriving workflow_status as {$revertStatus}");
        }

        $updateData = ['workflow_status' => $revertStatus];
        if ($revertStatusUnitId !== null) {
            $updateData['status_unit_id'] = $revertStatusUnitId;
        }

        $db->table('inventory_unit')
            ->where('id_inventory_unit', $unitId)
            ->update($updateData);
        log_message(
            'info',
            "Unit {$unitId} reverted after WO terminal state: workflow_status={$revertStatus}, status_unit_id=" . ($updateData['status_unit_id'] ?? 'unchanged')
        );
    }

    private function getAreaStaffInfo($unitId)
    {
        try {
            $db = \Config\Database::connect();
            
            // First get the area_id from the unit through customer relationship
            $unitQuery = $db->query("
                SELECT c.area_id 
                FROM inventory_unit iu
                -- Updated: JOIN via kontrak_unit junction table (source of truth)
                LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
                JOIN kontrak k ON k.id = ku.kontrak_id
                JOIN customers c ON c.id = k.customer_id
                WHERE iu.id_inventory_unit = ?
            ", [$unitId]);
            $unit = $unitQuery->getRowArray();
            
            if (!$unit || !$unit['area_id']) {
                return null;
            }
            
            $areaId = $unit['area_id'];
            
            // Get staff assigned to this area
            $query = $db->query("
                SELECT 
                    s.id,
                    s.staff_name,
                    s.staff_role,
                    aea.assignment_type,
                    aea.is_active
                FROM area_employee_assignments aea
                JOIN employees s ON aea.employee_id = s.id
                WHERE aea.area_id = ? AND aea.is_active = 1 AND s.is_active = 1
                ORDER BY s.staff_role, s.staff_name
            ", [$areaId]);
            
            return $query->getResultArray();
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return null;
        }
    }

    // Internal method to generate work order number
    private function generateWorkOrderNumber()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get the highest work_order_number (treating as integer)
            $query = $db->query("SELECT MAX(CAST(work_order_number AS UNSIGNED)) as max_number FROM work_orders");
            $result = $query->getRowArray();
            
            $nextNumber = 1;
            if ($result && !empty($result['max_number'])) {
                $nextNumber = intval($result['max_number']) + 1;
            }
            
            return (string)$nextNumber;
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return (string)time(); // Fallback to timestamp
        }
    }
    
    // Generate Work Order Number (hanya angka, auto increment) - API endpoint
    public function generateNumber()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get the highest work_order_number (treating as integer)
            $query = $db->query("SELECT MAX(CAST(work_order_number AS UNSIGNED)) as max_number FROM work_orders");
            $result = $query->getRowArray();
            
            $nextNumber = 1;
            if ($result && !empty($result['max_number'])) {
                $nextNumber = intval($result['max_number']) + 1;
            }
            
            return $this->response->setJSON([
                'success' => true,
                'work_order_number' => (string)$nextNumber
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error generating work order number'
            ]);
        }
    }
    
    // Search Units
    public function searchUnits()
    {
        $query = $this->request->getPost('query');
        
        if (empty($query)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Query is required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('inventory_unit iu');
            $builder->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number, 
                            c.customer_name as pelanggan, cl.location_name as lokasi, tu.tipe as unit_type, tu.jenis as jenis,
                            mu.model_unit, mu.merk_unit, kp.kapasitas_unit as kapasitas, su.status_unit as status');
            // Updated: JOIN via kontrak_unit junction table (source of truth)
            $builder->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left');
            $builder->join('kontrak k', 'k.id = ku.kontrak_id', 'left');
            $builder->join('customers c', 'c.id = k.customer_id', 'left');
            $builder->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left');
            $builder->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left');
            $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
            $builder->join('kapasitas kp', 'iu.kapasitas_unit_id = kp.id_kapasitas', 'left');
            $builder->join('status_unit su', 'iu.status_unit_id = su.id_status', 'left');
            $builder->groupStart()
                ->like('iu.no_unit', $query)
                ->orLike('c.customer_name', $query)
                ->orLike('iu.serial_number', $query)
                ->orLike('tu.tipe', $query) // Unit type (tipe_unit.tipe)
                ->orLike('mu.model_unit', $query)
                ->orLike('mu.merk_unit', $query)
            ->groupEnd();
            $builder->where('iu.no_unit IS NOT NULL');
            $builder->limit(10);
            
            $units = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $units
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error searching units'
            ]);
        }
    }
    
    
    // Get Priority by ID
    public function getPriority()
    {
        $priorityId = $this->request->getPost('priority_id');
        
        if (empty($priorityId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Priority ID is required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            $priority = $db->table('work_order_priorities')
                ->where('id', $priorityId)
                ->get()
                ->getRowArray();
            
            if ($priority) {
                return $this->response->setJSON([
                    'success' => true,
                    'priority_name' => $priority['priority_name']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Prioritas tidak ditemukan'
                ]);
            }
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting priority'
            ]);
        }
    }
    
    // Get Subcategory Priority
    public function getSubcategoryPriority()
    {
        $subcategoryId = $this->request->getPost('subcategory_id');
        
        if (empty($subcategoryId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Subcategory ID is required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            $subcategory = $db->table('work_order_subcategories')
                ->where('id', $subcategoryId)
                ->get()
                ->getRowArray();
            
            if ($subcategory) {
                // For now, return default priority (can be enhanced later)
                return $this->response->setJSON([
                    'success' => true,
                    'priority_id' => 2, // Default to normal priority
                    'subcategory_name' => $subcategory['subcategory_name']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Subkategori tidak ditemukan'
                ]);
            }
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting subcategory priority'
            ]);
        }
    }
    
    // Calculate Time to Repair (TTR) automatically
    public function calculateTTR($workOrderId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Get work order data
            $workOrder = $db->table('work_orders')
                ->select('created_at, completion_date, status_id')
                ->where('id', $workOrderId)
                ->get()
                ->getRowArray();
                
            if (!$workOrder) {
                return null;
            }
            
            // If not completed yet, calculate current duration
            $endTime = $workOrder['completion_date'] ?? date('Y-m-d H:i:s');
            $startTime = $workOrder['created_at'];
            
            // Calculate difference in hours
            $start = new \DateTime($startTime);
            $end = new \DateTime($endTime);
            $interval = $start->diff($end);
            
            // Convert to hours (including fractional hours)
            $hours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
            
            return round($hours, 2);
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return null;
        }
    }
    
    // Update work order with auto TTR calculation
    public function updateWithTTR($workOrderId, $data = [])
    {
        try {
            // Calculate current TTR
            $calculatedTTR = $this->calculateTTR($workOrderId);
            
            if ($calculatedTTR !== null) {
                $data['time_to_repair'] = $calculatedTTR;
            }
            
            // If status is being changed to completed, set completion_date
            if (isset($data['status_id'])) {
                $db = \Config\Database::connect();
                $status = $db->table('statuses')
                    ->where('id', $data['status_id'])
                    ->get()
                    ->getRowArray();
                    
                if ($status && in_array(strtoupper($status['status_name']), ['COMPLETED', 'CLOSED'])) {
                    $data['completion_date'] = date('Y-m-d H:i:s');
                    // Recalculate TTR with actual completion time
                    $data['time_to_repair'] = $this->calculateTTR($workOrderId);
                }
            }
            
            return $this->workOrderModel->update($workOrderId, $data);
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return false;
        }
    }

    /**
     * Public wrapper for updateWithTTR with notification support
     */
    public function updateWorkOrderWithTTR($workOrderId, $data = [])
    {
        $result = $this->updateWithTTR($workOrderId, $data);
        
        if ($result) {
            // Get work order details for notification
            $workOrder = $this->workOrderModel->find($workOrderId);
            
            // Send notification - TTR updated
            if (function_exists('notify_workorder_ttr_updated') && $workOrder) {
                notify_workorder_ttr_updated([
                    'id' => $workOrderId,
                    'wo_number' => $workOrder['work_order_number'] ?? '',
                    'unit_code' => $workOrder['unit_code'] ?? '',
                    'ttr_hours' => $data['time_to_repair'] ?? $workOrder['time_to_repair'] ?? 0,
                    'updated_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/service/work-orders/view/' . $workOrderId)
                ]);
            }
        }
        
        return $result;
    }

    // Get Units for Dropdown
    public function getUnitsDropdown()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get user's department/area scope for filtering
            $scope = get_user_area_department_scope();
            
            // Use area_id from inventory_unit (iu.area_id) - the actual unit area
            // Show ALL units with ALL statuses
            $sql = "SELECT 
                        iu.id_inventory_unit as id, 
                        iu.no_unit,
                        iu.serial_number,
                        COALESCE(c.customer_name, 'Belum Ada Kontrak') as pelanggan,
                        COALESCE(cl.location_name, 'N/A') as lokasi,
                        tu.jenis as jenis,
                        kp.kapasitas_unit as kapasitas,
                        mu.merk_unit as merk,
                        mu.model_unit,
                        su.status_unit as status,
                        iu.workflow_status,
                        a.id as area_id, 
                        a.area_name,
                        iu.departemen_id,
                        dep.nama_departemen as departemen_name
                    FROM inventory_unit iu
                    -- Updated: JOIN via kontrak_unit junction table (source of truth)
                    LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
                    LEFT JOIN kontrak k ON k.id = ku.kontrak_id
                    LEFT JOIN customers c ON c.id = k.customer_id
                    LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id
                    LEFT JOIN areas a ON a.id = iu.area_id
                    LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
                    LEFT JOIN kapasitas kp ON iu.kapasitas_unit_id = kp.id_kapasitas
                    LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
                    LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
                    LEFT JOIN departemen dep ON iu.departemen_id = dep.id_departemen
                    WHERE iu.no_unit IS NOT NULL";
            
            $bindings = [];
            
            // Apply department/area scope filtering
            if ($scope !== null) {
                if (!empty($scope['departments'])) {
                    $deptIds = array_map('intval', $scope['departments']);
                    $placeholders = implode(',', array_fill(0, count($deptIds), '?'));
                    $sql .= " AND iu.departemen_id IN ({$placeholders})";
                    $bindings = array_merge($bindings, $deptIds);
                }
            }
            
            // Exclude SOLD (13), UNDER_REPAIR (10), MAINTENANCE (11) units
            $sql .= " AND iu.status_unit_id NOT IN (10, 11, 13)";
            
            // Exclude units that already have an active (non-CLOSED/COMPLETED) work order
            $sql .= " AND NOT EXISTS (
                SELECT 1 FROM work_orders wo_check
                JOIN work_order_statuses wos_check ON wo_check.status_id = wos_check.id
                WHERE wo_check.unit_id = iu.id_inventory_unit
                AND wo_check.deleted_at IS NULL
                AND wos_check.status_code NOT IN ('CLOSED','COMPLETED')
            )";
            
            $sql .= " ORDER BY iu.no_unit ASC";
            
            $result = $db->query($sql, $bindings);
            $units = $result->getResultArray();
            
            // Handle null values
            foreach ($units as &$unit) {
                $unit['pelanggan'] = $unit['pelanggan'] ?? 'Belum Ada Kontrak';
                $unit['lokasi'] = $unit['lokasi'] ?? 'N/A';
                $unit['jenis'] = $unit['jenis'] ?? 'N/A';
                $unit['kapasitas'] = $unit['kapasitas'] ?? 'N/A';
                $unit['status'] = $unit['status'] ?? 'N/A';
                $unit['area_id'] = $unit['area_id'] ?? 0;
                $unit['area_name'] = $unit['area_name'] ?? 'N/A';
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $units,
                'count' => count($units)
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            $errorMsg = $this->getMySQLError($db ?? null);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data unit'
            ]);
        }
    }

    
    /**
     * Assign employees to a work order
     */
    public function assignEmployees()
    {
        log_message('info', 'assignEmployees function called');
        
        $workOrderId = $this->request->getPost('work_order_id');
        $mechanicIds = $this->request->getPost('mechanic_ids');
        $helperIds = $this->request->getPost('helper_ids');
        $notes = $this->request->getPost('notes');
        
        log_message('info', 'Assignment data: ' . json_encode([
            'work_order_id' => $workOrderId,
            'mechanic_ids' => $mechanicIds,
            'helper_ids' => $helperIds,
            'notes' => $notes
        ]));
        
        if (!$workOrderId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Work Order ID required'
            ]);
        }
        
        // Validate mechanic selection
        if (!$mechanicIds || !is_array($mechanicIds) || count($mechanicIds) == 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Minimal satu mekanik harus dipilih'
            ]);
        }
        
        if (count($mechanicIds) > 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Maksimal 2 mekanik dapat dipilih'
            ]);
        }
        
        // Validate helper selection
        if ($helperIds && is_array($helperIds) && count($helperIds) > 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Maksimal 2 helper dapat dipilih'
            ]);
        }

        try {
            // Get work order info
            $workOrder = $this->workOrderModel->find($workOrderId);
            if (!$workOrder || !is_array($workOrder)) {
                log_message('error', 'Work Order not found: ' . $workOrderId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work Order tidak ditemukan'
                ]);
            }
            
            log_message('info', 'Current work order status: ' . $workOrder['status_id']);
            
            // Get primary mechanic (first selected)
            $primaryMechanicId = $mechanicIds[0];
            
            // Start database transaction
            $db = \Config\Database::connect();
            $db->transBegin();
            
            try {
                // Prepare assignments array
                $assignments = [];
                
                // Add mechanics to assignments
                foreach ($mechanicIds as $index => $mechanicId) {
                    $assignments[] = [
                        'employee_id' => $mechanicId,
                        'role' => 'MECHANIC',
                        'assignment_type' => $index === 0 ? 'PRIMARY' : 'SECONDARY',
                        'notes' => $notes
                    ];
                }
                
                // Add helpers to assignments
                foreach ($helperIds as $index => $helperId) {
                    $assignments[] = [
                        'employee_id' => $helperId,
                        'role' => 'HELPER',
                        'assignment_type' => $index === 0 ? 'PRIMARY' : 'SECONDARY',
                        'notes' => $notes
                    ];
                }
                
                log_message('info', 'Assignments to be created: ' . json_encode($assignments));
                
                // Get current status before update for history
                $currentWorkOrder = $this->workOrderModel->find($workOrderId);
                $fromStatusId = null;
                if ($currentWorkOrder && is_array($currentWorkOrder)) {
                    $fromStatusId = $currentWorkOrder['status_id'];
                } elseif ($currentWorkOrder && is_object($currentWorkOrder)) {
                    $fromStatusId = $currentWorkOrder->status_id ?? null;
                }

                // Update work order status FIRST to ensure it exists and is valid
                $updateData = [
                    'status_id' => 2, // ASSIGNED status
                    'mechanic_id' => $mechanicIds[0], // Primary mechanic for backward compatibility
                    'helper_id' => isset($helperIds[0]) ? $helperIds[0] : null, // Primary helper for backward compatibility
                    'notes' => $notes
                ];
                
                log_message('info', 'Updating work order with data: ' . json_encode($updateData));
                
                // Temporarily disable validation for assignment update
                $this->workOrderModel->skipValidation(true);
                $updated = $this->workOrderModel->update($workOrderId, $updateData);
                $this->workOrderModel->skipValidation(false);
                
                if (!$updated) {
                    // Get validation errors for debugging
                    $validationErrors = $this->workOrderModel->errors();
                    log_message('error', 'Work order update failed. Validation errors: ' . json_encode($validationErrors));
                    log_message('error', 'Work order ID: ' . $workOrderId);
                    log_message('error', 'Update data: ' . json_encode($updateData));
                    
                    // Check if work order exists
                    $existingWo = $this->workOrderModel->find($workOrderId);
                    log_message('error', 'Existing work order: ' . json_encode($existingWo));
                    
                    throw new \Exception('Failed to update work order. Errors: ' . json_encode($validationErrors));
                }
                
                log_message('info', 'Work order updated successfully');
                
                // Now assign multiple employees using assignment model
                $assignedBy = session()->get('user_id') ?: 1; // Fallback to user 1
                try {
                    $this->workOrderAssignmentModel->assignEmployees($workOrderId, $assignments, $assignedBy);
                    log_message('info', 'Multiple assignments created successfully');
                } catch (\Throwable $e) {
                    log_message('warning', 'Gagal memproses permintaan. Silakan coba lagi.');
                    // Continue anyway as main work order update succeeded
                }
                
                // Add status history with correct parameters
                $statusHistoryAdded = $this->workOrderModel->addStatusHistory($workOrderId, 2, 'Work order assigned to mechanic and helper', $fromStatusId);
                
                if (!$statusHistoryAdded) {
                    log_message('warning', 'Gagal memproses permintaan. Silakan coba lagi.');
                }

                $db->transCommit();

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Karyawan berhasil ditugaskan ke Work Order'
                ]);

            } catch (\Throwable $e) {
                $db->transRollback();
                throw $e;
            }

        } catch (\Throwable $e) {
            log_message('error', 'Error assigning employees. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Get spareparts for work order
     */
    public function getWorkOrderSpareparts($workOrderId)
    {
        try {
            $spareparts = $this->sparepartModel->getSparePartsWithUsage($workOrderId);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $spareparts
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Close work order with sparepart usage tracking
     */
    public function closeWorkOrder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        try {
            $woId = $this->request->getPost('work_order_id');
            $sparepartUsage = $this->request->getPost('sparepart_usage'); // Array of usage data
            $completionNotes = $this->request->getPost('completion_notes');

            if (!$woId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work Order ID required'
                ]);
            }

            $db = \Config\Database::connect();

            // Block if spareparts exist but not validated
            $sparepartCount = $db->table('work_order_spareparts')
                ->where('work_order_id', $woId)
                ->countAllResults();
            if ($sparepartCount > 0) {
                $woData = $db->table('work_orders')
                    ->select('sparepart_validated')
                    ->where('id', $woId)
                    ->get()
                    ->getRowArray();
                if (!($woData['sparepart_validated'] ?? 0)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Work order ini memiliki sparepart yang belum divalidasi. Harap selesaikan validasi sparepart terlebih dahulu.'
                    ]);
                }
            }

            $db->transStart();

            try {
                // Get current status before update for history
                $currentWorkOrder = $this->workOrderModel->find($woId);
                $fromStatusId = null;
                if ($currentWorkOrder && is_array($currentWorkOrder)) {
                    $fromStatusId = $currentWorkOrder['status_id'];
                } elseif ($currentWorkOrder && is_object($currentWorkOrder)) {
                    $fromStatusId = $currentWorkOrder->status_id ?? null;
                }

                // Update work order status to COMPLETED
                $completedStatus = $this->workOrderModel->getStatusByCode('COMPLETED');
                $updateData = [
                    'completed_date' => date('Y-m-d H:i:s'),
                    'completion_notes' => $completionNotes
                ];
                
                if ($completedStatus) {
                    $updateData['status_id'] = $completedStatus['id'];
                }

                $this->workOrderModel->update($woId, $updateData);

                // Add status history for completion
                if ($completedStatus) {
                    $this->workOrderModel->addStatusHistory($woId, $completedStatus['id'], 'Work order completed', $fromStatusId);
                }

                // Record sparepart usage if provided
                if (!empty($sparepartUsage) && is_array($sparepartUsage)) {
                    $this->sparepartUsageModel->recordMultipleUsage($woId, $sparepartUsage);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Transaksi database gagal');
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Work Order berhasil diselesaikan'
                ]);

            } catch (\Throwable $e) {
                $db->transRollback();
                throw $e;
            }

        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Test endpoint to verify routing is working
     */
    public function testRouting()
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Unit verification routing is working!',
            'timestamp' => date('Y-m-d H:i:s'),
            'methods_available' => [
                'getUnitVerificationData',
                'saveUnitVerification', 
                'getSparepartUsageData',
                'saveSparepartUsage',
                'getCompleteData',
                'saveCompleteWorkOrder'
            ]
        ]);
    }

    /**
     * Get complete work order data (repair_description, notes)
     */
    public function getCompleteData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        $workOrderId = $this->request->getPost('work_order_id');
        
        if (!$workOrderId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Work Order ID required'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Get work order data
            $workOrder = $db->table('work_orders')
                ->select('id, work_order_number, repair_description, notes, status_id')
                ->where('id', $workOrderId)
                ->get()
                ->getRowArray();
            
            if (!$workOrder) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work Order not found'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'work_order_id' => $workOrder['id'],
                    'work_order_number' => $workOrder['work_order_number'],
                    'repair_description' => $workOrder['repair_description'],
                    'notes' => $workOrder['notes'],
                    'status_id' => $workOrder['status_id']
                ]
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Save complete work order data (repair_description, notes)
     * Status remains IN_PROGRESS - will be changed to COMPLETED after verification
     */
    public function saveCompleteWorkOrder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        $workOrderId = $this->request->getPost('work_order_id');
        $repairDescription = $this->request->getPost('repair_description');
        $notes = $this->request->getPost('notes');
        
        if (!$workOrderId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Work Order ID required'
            ]);
        }

        if (empty($repairDescription)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Analysis & Repair is required'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Check if work order exists
            $workOrder = $db->table('work_orders')
                ->where('id', $workOrderId)
                ->get()
                ->getRowArray();
            
            if (!$workOrder) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work Order not found'
                ]);
            }

            // Update work order with repair data
            // Status REMAINS IN_PROGRESS - will be changed to COMPLETED after unit verification
            $updateData = [
                'repair_description' => $repairDescription,
                'notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $db->table('work_orders')
                ->where('id', $workOrderId)
                ->update($updateData);

            if (!$updated) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update work order'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Complete data saved successfully. Please continue with unit verification.',
                'data' => [
                    'work_order_id' => $workOrderId,
                    'status' => 'IN_PROGRESS' // Status tetap IN_PROGRESS
                ]
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Gagal menyimpan data. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Get unit verification data for complete modal
     */
    public function getUnitVerificationData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        $workOrderId = $this->request->getPost('work_order_id');
        $auditId     = (int) ($this->request->getPost('audit_id') ?? 0);
        $unitIdPost  = (int) ($this->request->getPost('unit_id') ?? 0);
        $isAuditContext = $auditId > 0;
        $auditHeader    = null;

        if ($isAuditContext) {
            if ($unitIdPost <= 0) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit ID wajib untuk verifikasi audit']);
            }
            $auditLocModel = new AuditLocationModel();
            $auditHeader   = $auditLocModel->getWithDetails($auditId);
            if (!$auditHeader) {
                return $this->response->setJSON(['success' => false, 'message' => 'Audit tidak ditemukan']);
            }
            $inAudit = false;
            foreach ($auditHeader['items'] ?? [] as $row) {
                if ((int) ($row['unit_id'] ?? 0) === $unitIdPost) {
                    $inAudit = true;
                    break;
                }
            }
            if (!$inAudit) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak termasuk dalam audit ini']);
            }
            $workOrder = [
                'id'                  => null,
                'unit_id'             => $unitIdPost,
                'work_order_number'   => $auditHeader['audit_number'] ?? 'AUDIT',
                'wo_number'           => $auditHeader['audit_number'] ?? 'AUDIT',
                'unit_code'           => '',
                'mechanic_id'         => null,
                'helper_id'           => null,
                '_audit_mechanic_name'=> $auditHeader['mechanic_name'] ?? null,
            ];
        } elseif (!$workOrderId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Work Order ID required']);
        }

        try {
            if (!$isAuditContext) {
                $workOrder = $this->workOrderModel->find($workOrderId);
                if (!$workOrder) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Work Order not found']);
                }
                if (is_object($workOrder)) {
                    $workOrder = (array) $workOrder;
                }
            }

            // Get unit details with all related data
            $db = \Config\Database::connect();
            $unit = $db->query("
                SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
                    iu.status_unit_id,
                    iu.serial_number,
                    iu.tahun_unit,
                    iu.departemen_id,
                    iu.keterangan,
                    iu.tipe_unit_id,
                    iu.model_unit_id,
                    iu.kapasitas_unit_id,
                    iu.model_mast_id,
                    iu.tinggi_mast,
                    iu.sn_mast,
                    iu.model_mesin_id,
                    iu.sn_mesin,
                    iu.roda_id,
                    iu.ban_id,
                    iu.valve_id,
                    iu.aksesoris,
                    iu.hour_meter,
                    CASE WHEN tu.jenis IS NOT NULL AND tu.jenis != '' THEN CONCAT(tu.tipe, ' - ', tu.jenis) ELSE tu.tipe END as tipe_unit_name,
                    CONCAT(mu.merk_unit, ' - ', mu.model_unit) as model_unit_name,
                    mu.merk_unit,
                    k.kapasitas_unit as kapasitas_name,
                    tm.tipe_mast as model_mast_name,
                    CONCAT(m.merk_mesin, ' - ', m.model_mesin) as model_mesin_name,
                    d.nama_departemen as departemen_name,
                    jr.tipe_roda as roda_name,
                    tb.tipe_ban as ban_name,
                    v.jumlah_valve as valve_name
                FROM inventory_unit iu
                LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
                LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
                LEFT JOIN kapasitas k ON iu.kapasitas_unit_id = k.id_kapasitas
                LEFT JOIN tipe_mast tm ON iu.model_mast_id = tm.id_mast
                LEFT JOIN mesin m ON iu.model_mesin_id = m.id
                LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
                LEFT JOIN jenis_roda jr ON iu.roda_id = jr.id_roda
                LEFT JOIN tipe_ban tb ON iu.ban_id = tb.id_ban
                LEFT JOIN valve v ON iu.valve_id = v.id_valve
                WHERE iu.id_inventory_unit = ?
            ", [$workOrder['unit_id']])->getRowArray();

            if (!$unit) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan']);
            }

            // Get customer data from unit's contract
            $customerData = $db->query("
                SELECT 
                    c.customer_name as pelanggan,
                    cl.location_name as lokasi
                FROM inventory_unit iu
                -- Updated: JOIN via kontrak_unit junction table (source of truth)
                LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0
                LEFT JOIN kontrak k ON k.id = ku.kontrak_id
                LEFT JOIN customers c ON c.id = k.customer_id
                LEFT JOIN customer_locations cl ON cl.id = ku.customer_location_id
                WHERE iu.id_inventory_unit = ?
            ", [$workOrder['unit_id']])->getRowArray();
            
            // Add customer data to unit
            $unit['pelanggan'] = $customerData['pelanggan'] ?? 'N/A';
            $unit['lokasi'] = $customerData['lokasi'] ?? 'N/A';
            if ($isAuditContext && $auditHeader) {
                $unit['pelanggan'] = $auditHeader['customer_name'] ?? $unit['pelanggan'];
                $unit['lokasi']     = $auditHeader['location_name'] ?? $unit['lokasi'];
            }

            // Get attachment data from NEW separate tables (UNION ALL approach)
            $attachmentRows = $db->query("
                SELECT 
                    ia.id as id_inventory_attachment,
                    'attachment' as tipe_item,
                    ia.attachment_type_id as attachment_id,
                    ia.serial_number as sn_attachment,
                    NULL as baterai_id,
                    NULL as sn_baterai,
                    NULL as charger_id,
                    NULL as sn_charger,
                    ia.physical_condition as kondisi_fisik,
                    ia.completeness as kelengkapan,
                    ia.notes as catatan_fisik,
                    a.tipe as attachment_tipe,
                    a.merk as attachment_merk,
                    a.model as attachment_model,
                    NULL as tipe_baterai,
                    NULL as merk_baterai,
                    NULL as jenis_baterai,
                    NULL as tipe_charger,
                    NULL as merk_charger
                FROM inventory_attachments ia
                LEFT JOIN attachment a ON ia.attachment_type_id = a.id_attachment
 WHERE ia.inventory_unit_id = ?
                UNION ALL
                SELECT 
                    ib.id as id_inventory_attachment,
                    'battery' as tipe_item,
                    NULL as attachment_id,
                    NULL as sn_attachment,
                    ib.battery_type_id as baterai_id,
                    ib.serial_number as sn_baterai,
                    NULL as charger_id,
                    NULL as sn_charger,
                    ib.physical_condition as kondisi_fisik,
                    NULL as kelengkapan,
                    ib.notes as catatan_fisik,
                    NULL as attachment_tipe,
                    NULL as attachment_merk,
                    NULL as attachment_model,
                    b.tipe_baterai,
                    b.merk_baterai,
                    b.jenis_baterai,
                    NULL as tipe_charger,
                    NULL as merk_charger
                FROM inventory_batteries ib
                LEFT JOIN baterai b ON ib.battery_type_id = b.id
                WHERE ib.inventory_unit_id = ?
                UNION ALL
                SELECT 
                    ic.id as id_inventory_attachment,
                    'charger' as tipe_item,
                    NULL as attachment_id,
                    NULL as sn_attachment,
                    NULL as baterai_id,
                    NULL as sn_baterai,
                    ic.charger_type_id as charger_id,
                    ic.serial_number as sn_charger,
                    ic.physical_condition as kondisi_fisik,
                    NULL as kelengkapan,
                    ic.notes as catatan_fisik,
                    NULL as attachment_tipe,
                    NULL as attachment_merk,
                    NULL as attachment_model,
                    NULL as tipe_baterai,
                    NULL as merk_baterai,
                    NULL as jenis_baterai,
                    c.tipe_charger,
                    c.merk_charger
                FROM inventory_chargers ic
                LEFT JOIN charger c ON ic.charger_type_id = c.id_charger
                WHERE ic.inventory_unit_id = ?
            ", [$workOrder['unit_id'], $workOrder['unit_id'], $workOrder['unit_id']])->getResultArray();
            
            // Parse attachment data by type
            $attachment = [
                'attachment_id' => null,
                'attachment_name' => '',
                'sn_attachment' => '',
                'baterai_id' => null,
                'baterai_name' => '',
                'sn_baterai' => '',
                'charger_id' => null,
                'charger_name' => '',
                'sn_charger' => '',
                'kondisi_fisik' => '',
                'kelengkapan' => ''
            ];
            
            foreach ($attachmentRows as $row) {
                if ($row['tipe_item'] === 'attachment' && !empty($row['attachment_id'])) {
                    $attachment['attachment_id'] = $row['id_inventory_attachment'];
                    $attachment['attachment_name'] = trim(($row['attachment_tipe'] ?? '') . ' ' . ($row['attachment_merk'] ?? '') . ' ' . ($row['attachment_model'] ?? ''));
                    $attachment['sn_attachment'] = $row['sn_attachment'];
                    $attachment['kondisi_fisik'] = $row['kondisi_fisik'] ?? 'Baik';
                    $attachment['kelengkapan'] = $row['kelengkapan'] ?? 'Lengkap';
                }
                if ($row['tipe_item'] === 'battery' && !empty($row['baterai_id'])) {
                    $attachment['baterai_id'] = $row['id_inventory_attachment'];
                    $attachment['baterai_name'] = trim(($row['merk_baterai'] ?? '') . ' ' . ($row['tipe_baterai'] ?? ''));
                    if (!empty($row['jenis_baterai'])) {
                        $attachment['baterai_name'] .= ' (' . $row['jenis_baterai'] . ')';
                    }
                    $attachment['sn_baterai'] = $row['sn_baterai'];
                }
                if ($row['tipe_item'] === 'charger' && !empty($row['charger_id'])) {
                    $attachment['charger_id'] = $row['id_inventory_attachment'];
                    $attachment['charger_name'] = trim(($row['merk_charger'] ?? '') . ' ' . ($row['tipe_charger'] ?? ''));
                    $attachment['sn_charger'] = $row['sn_charger'];
                }
            }

            // Get dropdown options
            $departemenOptions = $db->table('departemen')
                ->select('id_departemen as id, nama_departemen as name')
                ->get()
                ->getResultArray();
                
            $tipeUnitOptions = $db->table('tipe_unit')
                ->select('id_tipe_unit as id, tipe as name, jenis, id_departemen')
                ->get()
                ->getResultArray();

            $modelUnitOptions = $db->table('model_unit')
                ->select('id_model_unit as id, CONCAT(merk_unit, " - ", model_unit) as name')
                ->get()
                ->getResultArray();

            $kapasitasOptions = $db->table('kapasitas')
                ->select('id_kapasitas as id, kapasitas_unit as name')
                ->get()
                ->getResultArray();

            $modelMastOptions = $db->table('tipe_mast')
                ->select('id_mast as id, tipe_mast as name')
                ->get()
                ->getResultArray();

            $modelMesinOptions = $db->table('mesin')
                ->select('id as id, CONCAT(merk_mesin, " - ", model_mesin) as name, departemen_id')
                ->get()
                ->getResultArray();

            $rodaOptions = $db->table('jenis_roda')
                ->select('id_roda as id, tipe_roda as name')
                ->get()
                ->getResultArray();

            $banOptions = $db->table('tipe_ban')
                ->select('id_ban as id, tipe_ban as name')
                ->get()
                ->getResultArray();

            $valveOptions = $db->table('valve')
                ->select('id_valve as id, jumlah_valve as name')
                ->get()
                ->getResultArray();

            $attachmentOptions = $db->query("
                SELECT 
                    ia.id as id,
                    CONCAT(a.tipe, ' - ', a.merk, ' - ', a.model, ' [SN: ', COALESCE(ia.serial_number, 'No SN'), ']') as name,
                    a.tipe,
                    a.merk,
                    a.model,
                    ia.serial_number as sn_attachment,
                    ia.attachment_type_id as attachment_id,
                    ia.status as attachment_status
                FROM inventory_attachments ia
                JOIN attachment a ON ia.attachment_type_id = a.id_attachment
                WHERE ia.status = 'AVAILABLE' 
                AND ia.attachment_type_id IS NOT NULL
                ORDER BY a.tipe, a.merk, a.model
            ")->getResultArray();

            $bateraiOptions = $db->query("
                SELECT 
                    ib.id as id,
                    CONCAT(b.tipe_baterai, ' - ', b.merk_baterai, ' [SN: ', COALESCE(ib.serial_number, 'No SN'), ']') as name,
                    b.tipe_baterai,
                    b.merk_baterai,
                    ib.serial_number as sn_baterai,
                    ib.battery_type_id as baterai_id,
                    ib.status as attachment_status
                FROM inventory_batteries ib
                JOIN baterai b ON ib.battery_type_id = b.id
                WHERE ib.status = 'AVAILABLE' 
                AND ib.battery_type_id IS NOT NULL
                ORDER BY b.tipe_baterai, b.merk_baterai
            ")->getResultArray();

            $chargerOptions = $db->query("
                SELECT 
                    ic.id as id,
                    CONCAT(c.tipe_charger, ' - ', c.merk_charger, ' [SN: ', COALESCE(ic.serial_number, 'No SN'), ']') as name,
                    c.tipe_charger,
                    c.merk_charger,
                    ic.serial_number as sn_charger,
                    ic.charger_type_id as charger_id,
                    ic.status as attachment_status
                FROM inventory_chargers ic
                JOIN charger c ON ic.charger_type_id = c.id_charger
                WHERE ic.status = 'AVAILABLE' 
                AND ic.charger_type_id IS NOT NULL
                ORDER BY c.tipe_charger, c.merk_charger
            ")->getResultArray();

            // Add currently assigned attachments to the options (if any)
            $currentAttachments = $db->query("
                SELECT 
                    ia.id as id,
                    CONCAT(a.tipe, ' - ', a.merk, ' - ', a.model, ' [SN: ', COALESCE(ia.serial_number, 'No SN'), '] (Current)') as name,
                    a.tipe,
                    a.merk,
                    a.model,
                    ia.serial_number as sn_attachment,
                    ia.attachment_type_id as attachment_id,
                    ia.status as attachment_status
                FROM inventory_attachments ia
                JOIN attachment a ON ia.attachment_type_id = a.id_attachment
                WHERE ia.inventory_unit_id = ? 
                AND ia.attachment_type_id IS NOT NULL
                ORDER BY a.tipe, a.merk, a.model
            ", [$workOrder['unit_id']])->getResultArray();

            $currentBaterais = $db->query("
                SELECT 
                    ib.id as id,
                    CONCAT(b.tipe_baterai, ' - ', b.merk_baterai, ' [SN: ', COALESCE(ib.serial_number, 'No SN'), '] (Current)') as name,
                    b.tipe_baterai,
                    b.merk_baterai,
                    ib.serial_number as sn_baterai,
                    ib.battery_type_id as baterai_id,
                    ib.status as attachment_status
                FROM inventory_batteries ib
                JOIN baterai b ON ib.battery_type_id = b.id
                WHERE ib.inventory_unit_id = ? 
                AND ib.battery_type_id IS NOT NULL
                ORDER BY b.tipe_baterai, b.merk_baterai
            ", [$workOrder['unit_id']])->getResultArray();

            $currentChargers = $db->query("
                SELECT 
                    ic.id as id,
                    CONCAT(c.tipe_charger, ' - ', c.merk_charger, ' [SN: ', COALESCE(ic.serial_number, 'No SN'), '] (Current)') as name,
                    c.tipe_charger,
                    c.merk_charger,
                    ic.serial_number as sn_charger,
                    ic.charger_type_id as charger_id,
                    ic.status as attachment_status
                FROM inventory_chargers ic
                JOIN charger c ON ic.charger_type_id = c.id_charger
                WHERE ic.inventory_unit_id = ? 
                AND ic.charger_type_id IS NOT NULL
                ORDER BY c.tipe_charger, c.merk_charger
            ", [$workOrder['unit_id']])->getResultArray();

            // Merge current and available options
            $attachmentOptions = array_merge($currentAttachments, $attachmentOptions);
            $bateraiOptions = array_merge($currentBaterais, $bateraiOptions);
            $chargerOptions = array_merge($currentChargers, $chargerOptions);

            // Get unit accessories from inventory_unit.aksesoris field (JSON format)
            $accessories = [];
            if (!empty($unit['aksesoris'])) {
                $accessoriesJson = $unit['aksesoris'];
                log_message('info', 'Raw accessories data: ' . $accessoriesJson);
                
                if (is_string($accessoriesJson)) {
                    $decodedAccessories = json_decode($accessoriesJson, true);
                    if ($decodedAccessories && is_array($decodedAccessories)) {
                        $accessories = $decodedAccessories;
                    }
                } elseif (is_array($accessoriesJson)) {
                    $accessories = $accessoriesJson;
                }
                
                // Convert to expected format for frontend
                if (!empty($accessories)) {
                    $accessories = array_map(function($item) {
                        if (is_string($item)) {
                            return ['name' => $item];
                        } elseif (is_array($item)) {
                            if (isset($item['name'])) {
                                return ['name' => $item['name']];
                            } elseif (isset($item['value'])) {
                                return ['name' => $item['value']];
                            } elseif (isset($item[0])) {
                                return ['name' => $item[0]];
                            }
                        }
                        return ['name' => (string)$item];
                    }, $accessories);
                }
                
                log_message('info', 'Processed accessories for frontend: ' . json_encode($accessories));
            }

            // Get assigned staff names for verified by
            if ($isAuditContext && $auditHeader) {
                $verifiedBy = trim((string) ($auditHeader['mechanic_name'] ?? ''));
                if ($verifiedBy === '') {
                    $verifiedBy = trim((string) ($auditHeader['submitter_name'] ?? ''));
                }
                if ($verifiedBy === '') {
                    $verifiedBy = 'Audit lokasi';
                }
            } else {
                $assignedStaff = [];
                if (!empty($workOrder['mechanic_id'])) {
                    $mechanic = $db->table('employees')->select('staff_name')->where('id', $workOrder['mechanic_id'])->get()->getRowArray();
                    if ($mechanic) {
                        $assignedStaff[] = $mechanic['staff_name'];
                    }
                }
                if (!empty($workOrder['helper_id'])) {
                    $helper = $db->table('employees')->select('staff_name')->where('id', $workOrder['helper_id'])->get()->getRowArray();
                    if ($helper) {
                        $assignedStaff[] = $helper['staff_name'];
                    }
                }
                $verifiedBy = implode(' & ', $assignedStaff);
                if (empty($verifiedBy)) {
                    $verifiedBy = 'Mekanik Belum Ditentukan';
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'work_order' => $workOrder,
                    'verification_context' => $isAuditContext ? 'audit' : 'work_order',
                    'audit_id' => $isAuditContext ? $auditId : null,
                    'unit' => $unit,
                    'attachment' => $attachment,
                    'options' => [
                        'departemen' => $departemenOptions,
                        'tipe_unit' => $tipeUnitOptions,
                        'model_unit' => $modelUnitOptions,
                        'kapasitas' => $kapasitasOptions,
                        'model_mast' => $modelMastOptions,
                        'model_mesin' => $modelMesinOptions,
                        'roda' => $rodaOptions,
                        'ban' => $banOptions,
                        'valve' => $valveOptions,
                        'attachment' => $attachmentOptions,
                        'baterai' => $bateraiOptions,
                        'charger' => $chargerOptions
                    ],
                    'accessories' => $accessories,
                    'verified_by' => $verifiedBy
                ]
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Helper method to get MySQL error directly from connection
     */
    private function getMySQLError($db)
    {
        $mysqlError = '';
        $mysqlErrno = 0;
        
        try {
            $connId = $db->connID;
            if ($connId && is_object($connId)) {
                // Try mysqli methods first
                if (method_exists($connId, 'error')) {
                    $mysqlError = $connId->error;
                    $mysqlErrno = $connId->errno ?? 0;
                } 
                // Try properties
                elseif (property_exists($connId, 'error')) {
                    $mysqlError = $connId->error;
                    $mysqlErrno = $connId->errno ?? 0;
                }
                // Try mysqli_error function
                elseif (function_exists('mysqli_error') && is_resource($connId)) {
                    $mysqlError = mysqli_error($connId);
                    $mysqlErrno = mysqli_errno($connId);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
        
        // If we got MySQL error, return it
        if (!empty($mysqlError)) {
            return 'MySQL Error (' . $mysqlErrno . '): ' . $mysqlError;
        }
        
        // Fallback to CodeIgniter error
        $dbError = $db->error();
        if (is_array($dbError)) {
            if (!empty($dbError['message'])) {
                return $dbError['message'];
            }
            if (!empty($dbError['code'])) {
                return 'Database error code: ' . $dbError['code'];
            }
        } elseif (!empty($dbError)) {
            return (string)$dbError;
        }
        
        // If transaction status is false but no error message, check for warnings
        if ($db->transStatus() === false) {
            try {
                $connId = $db->connID;
                if ($connId && is_object($connId) && method_exists($connId, 'sqlstate')) {
                    $sqlState = $connId->sqlstate;
                    if ($sqlState && $sqlState !== '00000') {
                        return 'SQL State: ' . $sqlState;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }
        
        return 'Unknown database error';
    }

    /**
     * Get available tinggi mast options for a selected model mast
     * Returns distinct tinggi_mast values for the given model name
     */
    public function getMastHeights()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        try {
            $modelName = $this->request->getPost('model_name');

            if (empty($modelName)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Model name is required'
                ]);
            }

            $db = \Config\Database::connect();

            // Get distinct tinggi_mast values for this model
            $query = "
                SELECT DISTINCT tinggi_mast as tinggi
                FROM tipe_mast
                WHERE tipe_mast = ?
                  AND tinggi_mast IS NOT NULL
                  AND tinggi_mast != ''
                ORDER BY tinggi_mast ASC
            ";

            $heights = $db->query($query, [$modelName])->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $heights
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data tinggi mast. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get unit verification history
     * Retrieves the most recent verification record for a unit (excluding current work order)
     */
    public function getUnitVerificationHistory()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        try {
            $unitId = $this->request->getPost('unit_id');
            $currentWorkOrderId = $this->request->getPost('current_work_order_id');

            if (empty($unitId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit ID is required'
                ]);
            }

            $db = \Config\Database::connect();

            // Query the most recent verification history for this unit (WO or standalone/audit)
            $query = "
                SELECT 
                    uvh.verified_at,
                    wo.work_order_number as wo_number,
                    e.staff_name as mechanic_name,
                    uvh.work_order_id
                FROM unit_verification_history uvh
                LEFT JOIN work_orders wo ON wo.id = uvh.work_order_id
                LEFT JOIN employees e ON e.id = uvh.verified_by
                WHERE uvh.unit_id = ?
            ";

            $params = [$unitId];

            // Exclude current work order if provided
            if (!empty($currentWorkOrderId)) {
                $query .= " AND (uvh.work_order_id IS NULL OR uvh.work_order_id != ?)";
                $params[] = $currentWorkOrderId;
            }

            $query .= " ORDER BY uvh.verified_at DESC LIMIT 1";

            $history = $db->query($query, $params)->getRowArray();

            if ($history) {
                $refLabel = !empty($history['wo_number'])
                    ? ('WO ' . $history['wo_number'])
                    : 'Verifikasi audit/mandiri';
                $mechName = $history['mechanic_name'] ?: '—';

                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'has_history' => true,
                        'verified_at' => date('d M Y H:i', strtotime($history['verified_at'])),
                        'mechanic_name' => $mechName,
                        'wo_number' => $history['wo_number'],
                        'reference_label' => $refLabel,
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => ['has_history' => false]
                ]);
            }

        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil riwayat verifikasi. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Save unit verification and complete work order
     */
    public function saveUnitVerification()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        $workOrderIdRaw = $this->request->getPost('work_order_id');
        $unitId         = (int) $this->request->getPost('unit_id');
        $auditIdSave    = (int) ($this->request->getPost('audit_id') ?? 0);
        $isAuditVerification = $auditIdSave > 0;
        $auditHeaderSave     = null;

        if ($isAuditVerification) {
            if ($unitId <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap - Unit ID tidak ditemukan',
                ]);
            }
            $auditLocModel = new AuditLocationModel();
            $auditHeaderSave = $auditLocModel->getWithDetails($auditIdSave);
            if (!$auditHeaderSave) {
                return $this->response->setJSON(['success' => false, 'message' => 'Audit tidak ditemukan']);
            }
            $inAudit = false;
            foreach ($auditHeaderSave['items'] ?? [] as $row) {
                if ((int) ($row['unit_id'] ?? 0) === $unitId) {
                    $inAudit = true;
                    break;
                }
            }
            if (!$inAudit) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak termasuk dalam audit ini']);
            }
            $workOrderId = null;
        } else {
            $workOrderId = $workOrderIdRaw;
            if (!$workOrderId || !$unitId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap - Work Order ID atau Unit ID tidak ditemukan',
                ]);
            }
        }

        try {
            $db = \Config\Database::connect();
            
            // Debug form data
            $formData = $this->request->getPost();
            log_message('debug', 'Unit Verification Form Data: ' . json_encode($formData));
            
            if (!$isAuditVerification) {
                $woExists = $db->table('work_orders')->where('id', $workOrderId)->countAllResults() > 0;
                if (!$woExists) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Work order tidak ditemukan',
                    ]);
                }

                $workOrder = $db->table('work_orders')
                    ->select('repair_description, notes')
                    ->where('id', $workOrderId)
                    ->get()
                    ->getRowArray();

                if (empty($workOrder['repair_description'])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Analysis & Repair harus diisi terlebih dahulu. Silakan klik tombol Complete untuk melengkapi data.',
                    ]);
                }
            }
            
            $unitExists = $db->table('inventory_unit')->where('id_inventory_unit', $unitId)->countAllResults() > 0;
            if (!$unitExists) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan'
                ]);
            }

            // Validate session user_id BEFORE starting transaction
            // changed_by/verified_by are NOT NULL FK → users.id — must be valid
            $currentUserId = (int)session()->get('user_id');
            if ($currentUserId <= 0) {
                log_message('error', "[WorkOrder] saveUnitVerification rejected: user_id not in session for WO {$workOrderId}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session tidak valid. Silakan login ulang sebelum menyimpan verifikasi.'
                ]);
            }
            $userExists = $db->table('users')->where('id', $currentUserId)->countAllResults() > 0;
            if (!$userExists) {
                log_message('error', "[WorkOrder] saveUnitVerification rejected: user_id={$currentUserId} not found in users table for WO {$workOrderId}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "User ID {$currentUserId} tidak ditemukan. Hubungi administrator."
                ]);
            }

            $db->transStart();

            $componentLogWoId    = $isAuditVerification ? null : (int) $workOrderId;
            $componentLogRefType = $isAuditVerification ? 'location_audit' : 'work_order';
            $componentLogRefId   = $isAuditVerification ? $auditIdSave : (int) $workOrderId;

            // Get existing unit data for comparison (before update) - Simple query first
            try {
                $oldUnitQuery = $db->table('inventory_unit')
                    ->select('*')
                    ->where('id_inventory_unit', $unitId)
                    ->get();

                if (!$oldUnitQuery) {
                    throw new \Exception('Failed to execute unit query');
                }

                $oldUnitData = $oldUnitQuery->getRowArray();
                if (!$oldUnitData) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Unit data tidak ditemukan untuk perbandingan'
                    ]);
                }
                
                // Get related names separately for comparison
                if (!empty($oldUnitData['departemen_id'])) {
                    $deptQuery = $db->table('departemen')->select('nama_departemen')->where('id_departemen', $oldUnitData['departemen_id'])->get();
                    $dept = $deptQuery ? $deptQuery->getRowArray() : null;
                    $oldUnitData['nama_departemen'] = $dept ? ($dept['nama_departemen'] ?? '') : '';
                }
                
                if (!empty($oldUnitData['tipe_unit_id'])) {
                    $tipeQuery = $db->table('tipe_unit')->select('tipe')->where('id_tipe_unit', $oldUnitData['tipe_unit_id'])->get();
                    $tipe = $tipeQuery ? $tipeQuery->getRowArray() : null;
                    $oldUnitData['tipe_unit_name'] = $tipe ? ($tipe['tipe'] ?? '') : '';
                }
                
                if (!empty($oldUnitData['model_unit_id'])) {
                    $modelQuery = $db->table('model_unit')->select('model_unit')->where('id_model_unit', $oldUnitData['model_unit_id'])->get();
                    $model = $modelQuery ? $modelQuery->getRowArray() : null;
                    $oldUnitData['model_unit_name'] = $model ? ($model['model_unit'] ?? '') : '';
                }
                
            } catch (\Throwable $e) {
                log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memuat data unit dari database'
                ]);
            }

            // Handle departemen - form sends name, need to get ID
            $departemenId = null;
            $departemenName = $this->request->getPost('departemen');
            if ($departemenName) {
                $dept = $db->table('departemen')
                    ->select('id_departemen')
                    ->where('nama_departemen', $departemenName)
                    ->get();
                if ($dept && $dept->getNumRows() > 0) {
                    $departemenId = $dept->getRowArray()['id_departemen'];
                }
            }

            // Validate and map post verification status (business decision output)
            $postVerificationStatus = $this->request->getPost('post_verification_status');
            $postVerificationStatus = ($postVerificationStatus !== null && $postVerificationStatus !== '')
                ? (int) $postVerificationStatus
                : null;
            $allowedPostStatuses = [1, 7, 8, 10]; // AVAILABLE_STOCK, RENTAL_ACTIVE, RENTAL_DAILY, BREAKDOWN
            if ($postVerificationStatus !== null && !in_array($postVerificationStatus, $allowedPostStatuses, true)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hasil verifikasi status unit tidak valid'
                ]);
            }

            // Update inventory_unit table with unit verification data
            $unitUpdateData = [
                'no_unit' => $this->request->getPost('no_unit'),
                'serial_number' => $this->request->getPost('serial_number'),
                'tahun_unit' => $this->request->getPost('tahun_unit'),
                'departemen_id' => $departemenId,
                'keterangan' => $this->request->getPost('keterangan'),
                'tipe_unit_id' => $this->request->getPost('tipe_unit_id') ?: null,
                'model_unit_id' => $this->request->getPost('model_unit_id') ?: null,
                'kapasitas_unit_id' => $this->request->getPost('kapasitas_unit_id') ?: null,
                'model_mast_id' => $this->request->getPost('model_mast_id') ?: null,
                'tinggi_mast' => $this->request->getPost('tinggi_mast'),
                'sn_mast' => $this->request->getPost('sn_mast'),
                'model_mesin_id' => $this->request->getPost('model_mesin_id') ?: null,
                'sn_mesin' => $this->request->getPost('sn_mesin'),
                'roda_id' => $this->request->getPost('roda_id') ?: null,
                'ban_id' => $this->request->getPost('ban_id') ?: null,
                'valve_id' => $this->request->getPost('valve_id') ?: null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Apply status outcome from verification.
            // If unit came from RETURNED queue and operator does not choose explicitly,
            // default to AVAILABLE_STOCK to prevent units getting stuck at status 12.
            if ($postVerificationStatus !== null) {
                $unitUpdateData['status_unit_id'] = $postVerificationStatus;
                $unitUpdateData['workflow_status'] = match ($postVerificationStatus) {
                    1 => 'TERSEDIA',
                    7 => 'DISEWA',
                    8 => 'DISEWA',
                    10 => 'UNDER_REPAIR',
                    default => $oldUnitData['workflow_status'] ?? null,
                };
            } elseif ((int)($oldUnitData['status_unit_id'] ?? 0) === 12) {
                $unitUpdateData['status_unit_id'] = 1;
                $unitUpdateData['workflow_status'] = 'TERSEDIA';
            }

            // Validate foreign keys before update
            if (!empty($unitUpdateData['departemen_id'])) {
                $deptExists = $db->table('departemen')->where('id_departemen', $unitUpdateData['departemen_id'])->countAllResults() > 0;
                if (!$deptExists) {
                    throw new \Exception('Departemen ID tidak valid: ' . $unitUpdateData['departemen_id']);
                }
            }
            
            if (!empty($unitUpdateData['tipe_unit_id'])) {
                $tipeExists = $db->table('tipe_unit')->where('id_tipe_unit', $unitUpdateData['tipe_unit_id'])->countAllResults() > 0;
                if (!$tipeExists) {
                    throw new \Exception('Tipe Unit ID tidak valid: ' . $unitUpdateData['tipe_unit_id']);
                }
            }
            
            if (!empty($unitUpdateData['model_unit_id'])) {
                $modelExists = $db->table('model_unit')->where('id_model_unit', $unitUpdateData['model_unit_id'])->countAllResults() > 0;
                if (!$modelExists) {
                    throw new \Exception('Model Unit ID tidak valid: ' . $unitUpdateData['model_unit_id']);
                }
            }
            
            if (!empty($unitUpdateData['kapasitas_unit_id'])) {
                $kapExists = $db->table('kapasitas')->where('id_kapasitas', $unitUpdateData['kapasitas_unit_id'])->countAllResults() > 0;
                if (!$kapExists) {
                    throw new \Exception('Kapasitas Unit ID tidak valid: ' . $unitUpdateData['kapasitas_unit_id']);
                }
            }

            // Remove empty values
            $unitUpdateData = array_filter($unitUpdateData, function($value) {
                return $value !== '' && $value !== null;
            });

            $updated = $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update($unitUpdateData);

            if ($updated === false) {
                $errorMsg = $this->getMySQLError($db);
                log_message('error', 'Failed to update inventory_unit. Unit ID: ' . $unitId . ', Error: ' . $errorMsg);
                log_message('error', 'Update data: ' . json_encode($unitUpdateData));
                throw new \Exception('Gagal update data unit: ' . $errorMsg);
            }

            // Check for MySQL errors
            $errorMsg = $this->getMySQLError($db);
            if (!empty($errorMsg) && strpos($errorMsg, 'Unknown database error') === false) {
                log_message('error', 'MySQL error after inventory_unit update: ' . $errorMsg);
                throw new \Exception('Error updating unit: ' . $errorMsg);
            }

            if (!$updated) {
                $errorMsg = $this->getMySQLError($db);
                if (!empty($errorMsg) && strpos($errorMsg, 'Unknown database error') === false) {
                    throw new \Exception('Gagal update data unit: ' . $errorMsg);
                }
                throw new \Exception('Gagal update data unit - tidak ada baris yang terupdate');
            }

            // Handle inventory_attachment table with SWAP logic
$attachmentInventoryId = $this->request->getPost('attachment_id'); // This is actually id_inventory_attachment
            $chargerInventoryId = $this->request->getPost('charger_id'); // This is actually id_inventory_attachment  
            $bateraiInventoryId = $this->request->getPost('baterai_id'); // This is actually id_inventory_attachment
            
            $componentHelper = new InventoryComponentHelper();
            
            // Get existing attachment records to preserve po_id and catatan_inventory
            $existingAttachments = [];
            
            // Query all 3 tables for existing components
            $existingBatteries = $db->table('inventory_batteries')
                ->where('inventory_unit_id', $unitId)
                ->get()->getResultArray();
            foreach ($existingBatteries as $bat) {
                $existingAttachments[] = array_merge($bat, ['tipe_item' => 'battery']);
            }
            
            $existingChargers = $db->table('inventory_chargers')
                ->where('inventory_unit_id', $unitId)
                ->get()->getResultArray();
            foreach ($existingChargers as $chr) {
                $existingAttachments[] = array_merge($chr, ['tipe_item' => 'charger']);
            }
            
            $existingAttachmentsTable = $db->table('inventory_attachments')
                ->where('inventory_unit_id', $unitId)
                ->get()->getResultArray();
            foreach ($existingAttachmentsTable as $att) {
                $existingAttachments[] = array_merge($att, ['tipe_item' => 'attachment']);
            }
            
            // Create map of existing records by type
            $existingMap = [];
            foreach ($existingAttachments as $existing) {
                $existingMap[$existing['tipe_item']] = $existing;
            }

            // Load InventoryAttachmentModel for swap functionality
            $attachmentModel = new \App\Models\InventoryAttachmentModel();
            
            // Track ALL changes for comprehensive notification
            $allChanges = [];
            
            // Compare unit data changes
            if ($oldUnitData['no_unit'] != $unitUpdateData['no_unit']) {
                $allChanges[] = "No Unit: {$oldUnitData['no_unit']} → {$unitUpdateData['no_unit']}";
            }
            if ($oldUnitData['serial_number'] != $unitUpdateData['serial_number']) {
                $allChanges[] = "Serial Number: " . ($oldUnitData['serial_number'] ?: '-') . " → " . ($unitUpdateData['serial_number'] ?: '-');
            }
            if ($oldUnitData['tahun_unit'] != $unitUpdateData['tahun_unit']) {
                $allChanges[] = "Tahun: " . ($oldUnitData['tahun_unit'] ?: '-') . " → " . ($unitUpdateData['tahun_unit'] ?: '-');
            }
            
            // Compare departemen
            $oldDeptId = $oldUnitData['departemen_id'] ?? null;
            $newDeptId = $unitUpdateData['departemen_id'] ?? null;
            if ($oldDeptId != $newDeptId) {
                $newDeptName = '';
                if (!empty($newDeptId)) {
                    $deptQuery = $db->table('departemen')->select('nama_departemen')->where('id_departemen', $newDeptId)->get();
                    $dept = $deptQuery ? $deptQuery->getRowArray() : null;
                    $newDeptName = $dept ? ($dept['nama_departemen'] ?? '') : '';
                }
                $allChanges[] = "Departemen: " . ($oldUnitData['nama_departemen'] ?? '-') . " → " . ($newDeptName ?: '-');
            }
            
            // Compare tipe unit
            $oldTipeId = $oldUnitData['tipe_unit_id'] ?? null;
            $newTipeId = $unitUpdateData['tipe_unit_id'] ?? null;
            if ($oldTipeId != $newTipeId) {
                $newTipeName = '';
                if (!empty($newTipeId)) {
                    $tipeQuery = $db->table('tipe_unit')->select('tipe')->where('id_tipe_unit', $newTipeId)->get();
                    $tipe = $tipeQuery ? $tipeQuery->getRowArray() : null;
                    $newTipeName = $tipe ? ($tipe['tipe'] ?? '') : '';
                }
                $allChanges[] = "Tipe Unit: " . ($oldUnitData['tipe_unit_name'] ?? '-') . " → " . ($newTipeName ?: '-');
            }
            
            // Compare model unit
            $oldModelId = $oldUnitData['model_unit_id'] ?? null;
            $newModelId = $unitUpdateData['model_unit_id'] ?? null;
            if ($oldModelId != $newModelId) {
                $newModelName = '';
                if (!empty($newModelId)) {
                    $modelQuery = $db->table('model_unit')->select('model_unit')->where('id_model_unit', $newModelId)->get();
                    $model = $modelQuery ? $modelQuery->getRowArray() : null;
                    $newModelName = $model ? ($model['model_unit'] ?? '') : '';
                }
                $allChanges[] = "Model Unit: " . ($oldUnitData['model_unit_name'] ?? '-') . " → " . ($newModelName ?: '-');
            }
            
            // Compare SN Mast
            $oldSnMast = $oldUnitData['sn_mast'] ?? null;
            $newSnMast = $unitUpdateData['sn_mast'] ?? null;
            if ($oldSnMast != $newSnMast) {
                $allChanges[] = "SN Mast: " . ($oldSnMast ?: '-') . " → " . ($newSnMast ?: '-');
            }
            
            // Compare SN Mesin
            $oldSnMesin = $oldUnitData['sn_mesin'] ?? null;
            $newSnMesin = $unitUpdateData['sn_mesin'] ?? null;
            if ($oldSnMesin != $newSnMesin) {
                $allChanges[] = "SN Mesin: " . ($oldSnMesin ?: '-') . " → " . ($newSnMesin ?: '-');
            }
            
            // Check attachment changes
            $oldAttachmentId = $existingMap['attachment']['attachment_type_id'] ?? null;
            $currentAttachmentId = null;
            if ($attachmentInventoryId) {
                // Get attachment_type_id from inventory_attachments table
                $currentAttQuery = $db->table('inventory_attachments')
                    ->select('attachment_type_id')
                    ->where('id', $attachmentInventoryId)
                    ->get();
                $currentAtt = $currentAttQuery ? $currentAttQuery->getRowArray() : null;
                $currentAttachmentId = $currentAtt['attachment_type_id'] ?? null;
            }
            if ($oldAttachmentId != $currentAttachmentId) {
                $oldAttInfo = '';
                $newAttInfo = '';
                if ($oldAttachmentId) {
                    $attQuery = $db->table('attachment')->select('tipe, merk, model')->where('id_attachment', $oldAttachmentId)->get();
                    $att = $attQuery ? $attQuery->getRowArray() : null;
                    $oldAttInfo = $att ? (($att['tipe'] ?? '') . ' ' . ($att['merk'] ?? '') . ' ' . ($att['model'] ?? '')) : '';
                }
                if ($currentAttachmentId) {
                    $attQuery = $db->table('attachment')->select('tipe, merk, model')->where('id_attachment', $currentAttachmentId)->get();
                    $att = $attQuery ? $attQuery->getRowArray() : null;
                    $newAttInfo = $att ? (($att['tipe'] ?? '') . ' ' . ($att['merk'] ?? '') . ' ' . ($att['model'] ?? '')) : '';
                }
                $allChanges[] = "Attachment: " . ($oldAttInfo ?: '-') . " → " . ($newAttInfo ?: '-');
            }
            
            // Check charger changes
            $oldChargerId = $existingMap['charger']['charger_type_id'] ?? null;
            $currentChargerId = null;
            if ($chargerInventoryId) {
                // Get charger_type_id from inventory_chargers table
                $currentChrQuery = $db->table('inventory_chargers')
                    ->select('charger_type_id')
                    ->where('id', $chargerInventoryId)
                    ->get();
                $currentChr = $currentChrQuery ? $currentChrQuery->getRowArray() : null;
                $currentChargerId = $currentChr['charger_type_id'] ?? null;
            }
            if ($oldChargerId != $currentChargerId) {
                $oldChrInfo = '';
                $newChrInfo = '';
                if ($oldChargerId) {
                    $chrQuery = $db->table('charger')->select('merk_charger, tipe_charger')->where('id_charger', $oldChargerId)->get();
                    $chr = $chrQuery ? $chrQuery->getRowArray() : null;
                    $oldChrInfo = $chr ? (($chr['merk_charger'] ?? '') . ' ' . ($chr['tipe_charger'] ?? '')) : '';
                }
                if ($currentChargerId) {
                    $chrQuery = $db->table('charger')->select('merk_charger, tipe_charger')->where('id_charger', $currentChargerId)->get();
                    $chr = $chrQuery ? $chrQuery->getRowArray() : null;
                    $newChrInfo = $chr ? (($chr['merk_charger'] ?? '') . ' ' . ($chr['tipe_charger'] ?? '')) : '';
                }
                $allChanges[] = "Charger: " . ($oldChrInfo ?: '-') . " → " . ($newChrInfo ?: '-');
            }
            
            // Check baterai changes
            $oldBateraiId = $existingMap['battery']['battery_type_id'] ?? null;
            $currentBateraiId = null;
            if ($bateraiInventoryId) {
                // Get battery_type_id from inventory_batteries table
                $currentBatQuery = $db->table('inventory_batteries')
                    ->select('battery_type_id')
                    ->where('id', $bateraiInventoryId)
                    ->get();
                $currentBat = $currentBatQuery ? $currentBatQuery->getRowArray() : null;
                $currentBateraiId = $currentBat['battery_type_id'] ?? null;
            }
            if ($oldBateraiId != $currentBateraiId) {
                $oldBatInfo = '';
                $newBatInfo = '';
                if ($oldBateraiId) {
                    $batQuery = $db->table('baterai')->select('merk_baterai, tipe_baterai')->where('id', $oldBateraiId)->get();
                    $bat = $batQuery ? $batQuery->getRowArray() : null;
                    $oldBatInfo = $bat ? (($bat['merk_baterai'] ?? '') . ' ' . ($bat['tipe_baterai'] ?? '')) : '';
                }
                if ($currentBateraiId) {
                    $batQuery = $db->table('baterai')->select('merk_baterai, tipe_baterai')->where('id', $currentBateraiId)->get();
                    $bat = $batQuery ? $batQuery->getRowArray() : null;
                    $newBatInfo = $bat ? (($bat['merk_baterai'] ?? '') . ' ' . ($bat['tipe_baterai'] ?? '')) : '';
                }
                $allChanges[] = "Baterai: " . ($oldBatInfo ?: '-') . " → " . ($newBatInfo ?: '-');
            }
            
            // STEP 1: Release ALL old components from this unit (set to AVAILABLE and detach)
            $auditService = new \App\Services\ComponentAuditService($db);
            
            // Release batteries
            $oldBatteries = $db->table('inventory_batteries')
                ->where('inventory_unit_id', $unitId)
                ->get()
                ->getResultArray();
            
            foreach ($oldBatteries as $oldBat) {
                $db->table('inventory_batteries')
                    ->where('id', $oldBat['id'])
                    ->update([
                        'inventory_unit_id' => null,
                        'status' => 'AVAILABLE',
                        'storage_location' => 'Workshop',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                
                // Log to component_audit_log
                $auditService->logRemoval('BATTERY', $oldBat['id'], $unitId, [
                    'work_order_id' => $componentLogWoId,
                    'triggered_by' => 'UNIT_VERIFICATION',
                    'reference_type' => $componentLogRefType,
                    'reference_id' => $componentLogRefId,
                    'notes' => 'Battery released during unit verification',
                ]);
                
                log_message('info', "[WorkOrder] Released battery {$oldBat['id']} from unit {$unitId}");
            }
            
            // Release chargers
            $oldChargers = $db->table('inventory_chargers')
                ->where('inventory_unit_id', $unitId)
                ->get()
                ->getResultArray();
            
            foreach ($oldChargers as $oldChr) {
                $db->table('inventory_chargers')
                    ->where('id', $oldChr['id'])
                    ->update([
                        'inventory_unit_id' => null,
                        'status' => 'AVAILABLE',
                        'storage_location' => 'Workshop',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                
                // Log to component_audit_log
                $auditService->logRemoval('CHARGER', $oldChr['id'], $unitId, [
                    'work_order_id' => $componentLogWoId,
                    'triggered_by' => 'UNIT_VERIFICATION',
                    'reference_type' => $componentLogRefType,
                    'reference_id' => $componentLogRefId,
                    'notes' => 'Charger released during unit verification',
                ]);
                
                log_message('info', "[WorkOrder] Released charger {$oldChr['id']} from unit {$unitId}");
            }
            
            // Release attachments
            $oldAttachmentsRelease = $db->table('inventory_attachments')
                ->where('inventory_unit_id', $unitId)
                ->get()
                ->getResultArray();
            
            foreach ($oldAttachmentsRelease as $oldAtt) {
                $db->table('inventory_attachments')
                    ->where('id', $oldAtt['id'])
                    ->update([
                        'inventory_unit_id' => null,
                        'status' => 'AVAILABLE',
                        'storage_location' => 'Workshop',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                
                // Log to component_audit_log
                $auditService->logRemoval('ATTACHMENT', $oldAtt['id'], $unitId, [
                    'work_order_id' => $componentLogWoId,
                    'triggered_by' => 'UNIT_VERIFICATION',
                    'reference_type' => $componentLogRefType,
                    'reference_id' => $componentLogRefId,
                    'notes' => 'Attachment released during unit verification',
                ]);
                
                log_message('info', "[WorkOrder] Released attachment {$oldAtt['id']} from unit {$unitId}");
            }
            
            // STEP 2: Attach NEW components to this unit
            // Handle attachment record if selected
            if (!empty($attachmentInventoryId)) {
                // Determine which table this component is in
                $componentType = $componentHelper->detectComponentType($attachmentInventoryId);
                $tableName = match($componentType) {
                    'battery' => 'inventory_batteries',
                    'charger' => 'inventory_chargers',
                    'attachment' => 'inventory_attachments',
                    default => null
                };
                
                if ($tableName) {
                    // Update the selected component record to attach to this unit
                    $updateData = [
                        'inventory_unit_id' => $unitId,
                        'status' => 'IN_USE',
                        'storage_location' => 'Terpasang di Unit',
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $updateResult = $db->table($tableName)
                        ->where('id', $attachmentInventoryId)
                        ->update($updateData);
                    
                    if ($updateResult === false) {
                        $errorMsg = $this->getMySQLError($db);
                        throw new \Exception('Gagal update data attachment: ' . $errorMsg);
                    }
                    
                    // Log to component_audit_log
                    $auditService->logAssignment(strtoupper($componentType), $attachmentInventoryId, $unitId, [
                        'work_order_id' => $componentLogWoId,
                        'triggered_by' => 'UNIT_VERIFICATION',
                        'reference_type' => $componentLogRefType,
                        'reference_id' => $componentLogRefId,
                        'notes' => ucfirst($componentType) . ' attached during unit verification',
                    ]);
                    
                    log_message('info', "[WorkOrder] Attached {$componentType} {$attachmentInventoryId} to unit {$unitId}");
                }
            }
            
            // Handle charger record if selected
            if (!empty($chargerInventoryId)) {
                // Determine which table this component is in
                $componentType = $componentHelper->detectComponentType($chargerInventoryId);
                $tableName = match($componentType) {
                    'battery' => 'inventory_batteries',
                    'charger' => 'inventory_chargers',
                    'attachment' => 'inventory_attachments',
                    default => null
                };
                
                if ($tableName) {
                    // Update the selected component record to attach to this unit
                    $updateData = [
                        'inventory_unit_id' => $unitId,
                        'status' => 'IN_USE',
                        'storage_location' => 'Terpasang di Unit',
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $updateResult = $db->table($tableName)
                        ->where('id', $chargerInventoryId)
                        ->update($updateData);
                    
                    if ($updateResult === false) {
                        $errorMsg = $this->getMySQLError($db);
                        throw new \Exception('Gagal update data charger: ' . $errorMsg);
                    }
                    
                    // Log to component_audit_log
                    $auditService->logAssignment(strtoupper($componentType), $chargerInventoryId, $unitId, [
                        'work_order_id' => $componentLogWoId,
                        'triggered_by' => 'UNIT_VERIFICATION',
                        'reference_type' => $componentLogRefType,
                        'reference_id' => $componentLogRefId,
                        'notes' => ucfirst($componentType) . ' attached during unit verification',
                    ]);
                    
                    log_message('info', "[WorkOrder] Attached {$componentType} {$chargerInventoryId} to unit {$unitId}");
                }
            }
            
            // Handle baterai record if selected
            if (!empty($bateraiInventoryId)) {
                // Determine which table this component is in
                $componentType = $componentHelper->detectComponentType($bateraiInventoryId);
                $tableName = match($componentType) {
                    'battery' => 'inventory_batteries',
                    'charger' => 'inventory_chargers',
                    'attachment' => 'inventory_attachments',
                    default => null
                };
                
                if ($tableName) {
                    // Update the selected component record to attach to this unit
                    $updateData = [
                        'inventory_unit_id' => $unitId,
                        'status' => 'IN_USE',
                        'storage_location' => 'Terpasang di Unit',
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $updateResult = $db->table($tableName)
                        ->where('id', $bateraiInventoryId)
                        ->update($updateData);
                    
                    if ($updateResult === false) {
                        $errorMsg = $this->getMySQLError($db);
                        throw new \Exception('Gagal update data baterai: ' . $errorMsg);
                    }
                    
                    // Log to component_audit_log
                    $auditService->logAssignment(strtoupper($componentType), $bateraiInventoryId, $unitId, [
                        'work_order_id' => $componentLogWoId,
                        'triggered_by' => 'UNIT_VERIFICATION',
                        'reference_type' => $componentLogRefType,
                        'reference_id' => $componentLogRefId,
                        'notes' => ucfirst($componentType) . ' attached during unit verification',
                    ]);
                    
                    log_message('info', "[WorkOrder] Attached {$componentType} {$bateraiInventoryId} to unit {$unitId}");
                }
            }

            // Handle unit accessories and hour meter update
            $accessories = $this->request->getPost('accessories');
            
            // Handle charger record if selected with SWAP logic
            if (!empty($chargerInventoryId)) {
                // Get charger_type_id from the inventory_chargers record
                $chargerRecord = $db->table('inventory_chargers')
                    ->select('charger_type_id')
                    ->where('id', $chargerInventoryId)
                    ->get()
                    ->getRowArray();
                
                $chargerId = $chargerRecord ? $chargerRecord['charger_type_id'] : null;
                
                if (!empty($chargerId)) {
                    // Check if this charger is currently attached to another unit (SWAP scenario)
                    $existingChargerUnit = $db->table('inventory_chargers')
                        ->select('id, inventory_unit_id, status')
                        ->where('charger_type_id', $chargerId)
                        ->where('inventory_unit_id !=', $unitId)
                        ->where('status', 'IN_USE')
                        ->get()
                        ->getRowArray();
                
                if ($existingChargerUnit) {
                    // SWAP: Charger is currently attached to another unit
                    $fromUnitId = $existingChargerUnit['inventory_unit_id'];
                    $recordId = $existingChargerUnit['id'];
                    
                    log_message('info', "[WorkOrder] Swapping charger {$chargerId} from unit {$fromUnitId} to unit {$unitId}");
                    
                    // Manual swap: detach from old unit, attach to new unit
                    $db->table('inventory_chargers')
                        ->where('id', $recordId)
                        ->update([
                            'inventory_unit_id' => $unitId,
                            'status' => 'IN_USE',
                            'storage_location' => 'Terpasang di Unit',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    
                    // Update SN if provided
                    $snCharger = $this->request->getPost('sn_charger');
                    if (!empty($snCharger)) {
                        $db->table('inventory_chargers')
                            ->where('id', $recordId)
                            ->update(['serial_number' => $snCharger]);
                    }
                    
                    // Send swap notification if function exists
                    if (function_exists('notify_attachment_swapped')) {
                        $fromUnitQuery = $db->table('inventory_unit')->select('COALESCE(no_unit, no_unit_na) as no_unit')->where('id_inventory_unit', $fromUnitId)->get();
                        $toUnitQuery = $db->table('inventory_unit')->select('COALESCE(no_unit, no_unit_na) as no_unit')->where('id_inventory_unit', $unitId)->get();
                        $chargerInfoQuery = $db->table('charger')->select('merk_charger, tipe_charger')->where('id_charger', $chargerId)->get();
                        
                        $fromUnit = $fromUnitQuery ? $fromUnitQuery->getRowArray() : ['no_unit' => 'Unknown'];
                        $toUnit = $toUnitQuery ? $toUnitQuery->getRowArray() : ['no_unit' => 'Unknown'];
                        $chargerInfo = $chargerInfoQuery ? $chargerInfoQuery->getRowArray() : ['merk_charger' => 'Unknown', 'tipe_charger' => ''];
                        
                        notify_attachment_swapped([
                            'module' => 'work_order_verification',
                            'attachment_id' => $recordId,
                            'tipe_item' => 'Charger',
                            'attachment_info' => ($chargerInfo['merk_charger'] ?? '') . ' - ' . ($chargerInfo['tipe_charger'] ?? ''),
                            'from_unit_id' => $fromUnitId,
                            'from_unit_number' => $fromUnit['no_unit'] ?? "ID {$fromUnitId}",
                            'to_unit_id' => $unitId,
                            'to_unit_number' => $toUnit['no_unit'] ?? "ID {$unitId}",
                            'reason' => 'Work Order Verification',
                            'performed_by' => session('username') ?? session('user_id'),
                            'performed_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } else {
                    // NOT SWAP: Charger is available - no insert needed, already attached above
                    log_message('info', "[WorkOrder] Charger {$chargerInventoryId} attached to unit {$unitId} (no swap)");
                }
            }
            }
            
            // Handle baterai record if selected with SWAP logic
            if (!empty($bateraiInventoryId)) {
                // Get battery_type_id from the inventory_batteries record
                $bateraiRecord = $db->table('inventory_batteries')
                    ->select('battery_type_id')
                    ->where('id', $bateraiInventoryId)
                    ->get()
                    ->getRowArray();
                
                $bateraiId = $bateraiRecord ? $bateraiRecord['battery_type_id'] : null;
                
                if (!empty($bateraiId)) {
                // Check if this baterai is currently attached to another unit (SWAP scenario)
                $existingBateraiUnit = $db->table('inventory_batteries')
                    ->select('id, inventory_unit_id, status')
                    ->where('battery_type_id', $bateraiId)
                    ->where('inventory_unit_id !=', $unitId)
                    ->where('status', 'IN_USE')
                    ->get()
                    ->getRowArray();
                
                if ($existingBateraiUnit) {
                    // SWAP: Baterai is currently attached to another unit
                    $fromUnitId = $existingBateraiUnit['inventory_unit_id'];
                    $recordId = $existingBateraiUnit['id'];
                    
                    log_message('info', "[WorkOrder] Swapping baterai {$bateraiId} from unit {$fromUnitId} to unit {$unitId}");
                    
                    // Manual swap: detach from old unit, attach to new unit
                    $db->table('inventory_batteries')
                        ->where('id', $recordId)
                        ->update([
                            'inventory_unit_id' => $unitId,
                            'status' => 'IN_USE',
                            'storage_location' => 'Terpasang di Unit',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    
                    // Update SN if provided
                    $snBaterai = $this->request->getPost('sn_baterai');
                    if (!empty($snBaterai)) {
                        $db->table('inventory_batteries')
                            ->where('id', $recordId)
                            ->update(['serial_number' => $snBaterai]);
                    }
                    
                    // Send swap notification if function exists
                    if (function_exists('notify_attachment_swapped')) {
                        $fromUnitQuery = $db->table('inventory_unit')->select('COALESCE(no_unit, no_unit_na) as no_unit')->where('id_inventory_unit', $fromUnitId)->get();
                        $toUnitQuery = $db->table('inventory_unit')->select('COALESCE(no_unit, no_unit_na) as no_unit')->where('id_inventory_unit', $unitId)->get();
                        $bateraiInfoQuery = $db->table('baterai')->select('merk_baterai, tipe_baterai')->where('id', $bateraiId)->get();
                        
                        $fromUnit = $fromUnitQuery ? $fromUnitQuery->getRowArray() : ['no_unit' => 'Unknown'];
                        $toUnit = $toUnitQuery ? $toUnitQuery->getRowArray() : ['no_unit' => 'Unknown'];
                        $bateraiInfo = $bateraiInfoQuery ? $bateraiInfoQuery->getRowArray() : ['merk_baterai' => 'Unknown', 'tipe_baterai' => ''];
                        
                        notify_attachment_swapped([
                            'module' => 'work_order_verification',
                            'attachment_id' => $recordId,
                            'tipe_item' => 'Baterai',
                            'attachment_info' => ($bateraiInfo['merk_baterai'] ?? '') . ' - ' . ($bateraiInfo['tipe_baterai'] ?? ''),
                            'from_unit_id' => $fromUnitId,
                            'from_unit_number' => $fromUnit['no_unit'] ?? "ID {$fromUnitId}",
                            'to_unit_id' => $unitId,
                            'to_unit_number' => $toUnit['no_unit'] ?? "ID {$unitId}",
                            'reason' => 'Work Order Verification',
                            'performed_by' => session('username') ?? session('user_id'),
                            'performed_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } else {
                    // NOT SWAP: Baterai is available - no insert needed, already attached above
                    log_message('info', "[WorkOrder] Baterai {$bateraiInventoryId} attached to unit {$unitId} (no swap)");
                }
            }
            }

            // Handle unit accessories and hour meter update
            $accessories = $this->request->getPost('accessories');
            $hourMeter = $this->request->getPost('hm');
            
            $inventoryUpdateData = [];
            
            if (!empty($accessories) && is_array($accessories)) {
                $inventoryUpdateData['aksesoris'] = json_encode($accessories);
            } else {
                $inventoryUpdateData['aksesoris'] = null;
            }
            
            // Update hour meter on unit if provided (support decimal)
            if (!empty($hourMeter) && is_numeric($hourMeter)) {
                $inventoryUpdateData['hour_meter'] = (float)$hourMeter;
            }
            
            // Apply updates to inventory_unit
            if (!empty($inventoryUpdateData)) {
                $updateResult = $db->table('inventory_unit')
                    ->where('id_inventory_unit', $unitId)
                    ->update($inventoryUpdateData);
                    
                if ($updateResult === false) {
                    $errorMsg = $this->getMySQLError($db);
                    log_message('error', 'Failed to update inventory_unit. Error: ' . $errorMsg);
                    throw new \Exception('Gagal update data unit: ' . $errorMsg);
                }
            }
            
            $fromStatusId = null;
            $statusData   = null;

            if (!$isAuditVerification) {
                $currentWorkOrder = $db->table('work_orders')
                    ->where('id', $workOrderId)
                    ->get()
                    ->getRowArray();

                $fromStatusId = $currentWorkOrder['status_id'] ?? null;

                $statusData = $db->table('work_order_statuses')
                    ->where('status_code', 'COMPLETED')
                    ->where('is_active', 1)
                    ->get()
                    ->getRowArray();

                if (!$statusData) {
                    log_message('error', 'Status COMPLETED tidak ditemukan di database');
                    throw new \Exception('Status COMPLETED tidak ditemukan');
                }

                log_message('info', "[WorkOrder] Found COMPLETED status with ID: {$statusData['id']}");
                log_message('info', "[WorkOrder] Updating WO {$workOrderId} to COMPLETED status (ID: {$statusData['id']})");

                $woUpdateData = [
                    'status_id'         => $statusData['id'],
                    'completion_date'   => date('Y-m-d H:i:s'),
                    'unit_verified'     => 1,
                    'unit_verified_at'  => date('Y-m-d H:i:s'),
                    'notes'             => $this->request->getPost('catatan_fisik'),
                    'hm'                => $this->request->getPost('hm') ?: null,
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];

                $woUpdated = $db->table('work_orders')
                    ->where('id', $workOrderId)
                    ->update($woUpdateData);

                if ($woUpdated === false) {
                    $errorMsg = $this->getMySQLError($db);
                    log_message('error', 'Failed to update work order. Error: ' . $errorMsg);
                    throw new \Exception('Gagal update work order: ' . $errorMsg);
                }

                log_message('info', "[WorkOrder] WO {$workOrderId} successfully updated to COMPLETED. Update result: " . json_encode($woUpdated));
            }

            $verificationHistoryData = [
                'unit_id'             => $unitId,
                'work_order_id'       => $isAuditVerification ? null : $workOrderId,
                'verified_by'         => $currentUserId,
                'verified_at'         => date('Y-m-d H:i:s'),
                'verification_data'   => json_encode([
                    'unit_changes' => $allChanges,
                    'audit_id'     => $isAuditVerification ? $auditIdSave : null,
                    'components'   => [
                        'attachment_id' => $attachmentInventoryId ?? null,
                        'charger_id'    => $chargerInventoryId ?? null,
                        'battery_id'    => $bateraiInventoryId ?? null,
                    ],
                    'old_data'     => $oldUnitData,
                    'new_data'     => $unitUpdateData,
                ], JSON_UNESCAPED_UNICODE),
                'created_at'          => date('Y-m-d H:i:s'),
            ];

            if ($db->tableExists('unit_verification_history')) {
                try {
                    $uvhFields = $db->getFieldNames('unit_verification_history');
                    if (is_array($uvhFields) && in_array('verification_type', $uvhFields, true)) {
                        $verificationHistoryData['verification_type'] = $isAuditVerification ? 'STANDALONE' : 'WO';
                    }
                    if (is_array($uvhFields) && in_array('notes', $uvhFields, true) && $isAuditVerification && $auditHeaderSave) {
                        $verificationHistoryData['notes'] = 'Audit lokasi: ' . ($auditHeaderSave['audit_number'] ?? (string) $auditIdSave);
                    }
                } catch (\Throwable $e) {
                    // ignore optional columns
                }

                log_message('info', "[WorkOrder] Inserting unit_verification_history: unit={$unitId}, WO=" . ($workOrderId ?? 'null') . ", audit=" . ($isAuditVerification ? $auditIdSave : '-') . ", verified_by={$currentUserId}");
                $db->table('unit_verification_history')->insert($verificationHistoryData);
            }

            if (!$isAuditVerification && $statusData !== null) {
                $historyData = [
                    'work_order_id'  => $workOrderId,
                    'from_status_id' => $fromStatusId,
                    'to_status_id'   => $statusData['id'],
                    'changed_by'     => $currentUserId,
                    'change_reason'  => 'Work order completed with unit verification',
                    'changed_at'     => date('Y-m-d H:i:s'),
                ];
                $db->table('work_order_status_history')->insert($historyData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $dbError = $this->getMySQLError($db);
                $ctx = $isAuditVerification ? "audit {$auditIdSave}" : "WO {$workOrderId}";
                log_message('error', "[WorkOrder] Transaction failed for {$ctx}. DB error: " . ($dbError ?: '(empty)'));
                throw new \Exception('Gagal menyimpan verifikasi: ' . ($dbError ?: 'Periksa server log untuk detail error'));
            }

            log_message('info', $isAuditVerification
                ? "[WorkOrder] Audit unit verification transaction OK unit={$unitId} audit={$auditIdSave}"
                : "[WorkOrder] Transaction completed successfully for WO {$workOrderId}");

            if (!$isAuditVerification) {
                $workOrder = $db->table('work_orders')
                    ->where('id', $workOrderId)
                    ->get()
                    ->getRowArray();

                log_message('info', "[WorkOrder] Current WO status after update: status_id = " . ($workOrder['status_id'] ?? 'NULL'));

                if (function_exists('notify_unit_verification_saved') && $workOrder) {
                    notify_unit_verification_saved([
                        'id'                  => $workOrderId,
                        'wo_number'           => $workOrder['work_order_number'] ?? '',
                        'unit_code'           => $workOrder['unit_code'] ?? '',
                        'verification_status' => 'COMPLETED',
                        'verified_by'         => session('username') ?? session('user_id'),
                        'verification_date'   => date('Y-m-d H:i:s'),
                        'url'                 => base_url('/service/work-orders/view/' . $workOrderId),
                    ]);
                }

                if (!empty($allChanges)) {
                    $unitNo = $oldUnitData['no_unit'] ?? "ID {$unitId}";
                    $changesList = implode("\n- ", $allChanges);
                    log_message('info', "[WorkOrder Verification] Unit {$unitNo} (WO: {$workOrder['work_order_number']}): " . count($allChanges) . " perubahan data");
                    if (function_exists('notify_work_order_unit_verified')) {
                        notify_work_order_unit_verified([
                            'work_order_id' => $workOrderId,
                            'wo_number'     => $workOrder['work_order_number'] ?? '',
                            'unit_code'     => $unitNo,
                            'changes_count' => count($allChanges),
                            'changes_list'  => $changesList,
                            'created_by'    => session('username') ?? session('user_id'),
                            'verified_at'   => date('Y-m-d H:i:s'),
                            'url'           => base_url('/service/work-orders/view/' . $workOrderId),
                        ]);
                    }
                }
            } elseif (function_exists('notify_unit_verification_saved')) {
                $unitNo = $oldUnitData['no_unit'] ?? "ID {$unitId}";
                notify_unit_verification_saved([
                    'id'                  => 0,
                    'wo_number'           => $auditHeaderSave['audit_number'] ?? 'AUDIT',
                    'unit_code'           => $unitNo,
                    'verification_status' => 'AUDIT_SAVED',
                    'verified_by'         => session('username') ?? session('user_id'),
                    'verification_date'   => date('Y-m-d H:i:s'),
                    'url'                 => base_url('/service/unit-verification'),
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $isAuditVerification
                    ? 'Unit berhasil diverifikasi (audit lokasi)'
                    : 'Unit berhasil diverifikasi dan work order diselesaikan',
            ]);

        } catch (\Throwable $e) {
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }
            
            $errorMsg = $e->getMessage();
            if (isset($db)) {
                $dbError = $this->getMySQLError($db);
                if (!empty($dbError) && strpos($dbError, 'Unknown database error') === false) {
                    $errorMsg = $dbError;
                }
            }
            
            log_message('error', 'Error saving unit verification: ' . $errorMsg);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $errorMsg
            ]);
        }
    }

    /**
     * Get sparepart validation data
     */
    public function getSparepartValidationData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        $workOrderId = $this->request->getGet('work_order_id');
        
        if (!$workOrderId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Work Order ID required']);
        }

        try {
            $db = \Config\Database::connect();
            
            // Get planned spareparts from work_order_spareparts
            $usedSpareparts = $db->table('work_order_spareparts wos')
                ->select('wos.*, wos.sparepart_name, wos.sparepart_code, 
                         wos.quantity_brought as planned_quantity, 
                         COALESCE(wos.quantity_used, wos.quantity_brought) as used_quantity,
                         wos.notes')
                ->where('wos.work_order_id', $workOrderId)
                ->where('COALESCE(wos.is_additional, 0) = 0') // Only planned spareparts
                ->get()
                ->getResultArray();

            // Get additional spareparts
            $additionalSpareparts = $db->table('work_order_spareparts wos')
                ->select('wos.*, wos.sparepart_name, wos.sparepart_code')
                ->where('wos.work_order_id', $workOrderId)
                ->where('wos.is_additional = 1') // Only additional spareparts
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'used_spareparts' => $usedSpareparts,
                    'additional_spareparts' => $additionalSpareparts
                ]
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Get sparepart master data for dropdown
     */
    public function getSparepartMaster()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        try {
            $db = \Config\Database::connect();
            
            $spareparts = $db->table('sparepart')
                ->select('id_sparepart as id, desc_sparepart as name, kode as code')
                ->orderBy('desc_sparepart', 'ASC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $spareparts
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Save sparepart validation and close work order
     */
    public function saveSparepartValidation()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        $workOrderId = $this->request->getPost('work_order_id');
        
        if (!$workOrderId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Work Order ID required']);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Get current work order status for history
            $currentWorkOrder = $this->workOrderModel->find($workOrderId);
            $fromStatusId = null;
            if ($currentWorkOrder && is_array($currentWorkOrder)) {
                $fromStatusId = $currentWorkOrder['status_id'];
            } elseif ($currentWorkOrder && is_object($currentWorkOrder)) {
                $fromStatusId = $currentWorkOrder->status_id ?? null;
            }

            // Load DB builder for auto-creating return records
            $returnDb = \Config\Database::connect();

            // Update used spareparts quantities and create return records if needed
            $usedSpareparts = $this->request->getPost('used_spareparts') ?: [];
            foreach ($usedSpareparts as $sparepart) {
                if (!empty($sparepart['id'])) {
                    // Get original sparepart data
                    $originalSparepart = $db->table('work_order_spareparts')
                        ->where('id', $sparepart['id'])
                        ->where('work_order_id', $workOrderId)
                        ->get()
                        ->getRowArray();

                    if ($originalSparepart) {
                        $isFromWarehouse = (int)($originalSparepart['is_from_warehouse'] ?? 1); // ← NEW: Get warehouse flag
                        $quantityBrought = (int)($originalSparepart['quantity_brought'] ?? 0);
                        $quantityUsed = (int)($sparepart['used_quantity'] ?? 0);
                        $quantityReturn = $quantityBrought - $quantityUsed;

                        // Update used quantity
                        $updateData = [
                            'quantity_used' => $quantityUsed,
                            'notes' => $sparepart['notes'] ?? $originalSparepart['notes'],
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        
                        $db->table('work_order_spareparts')
                            ->where('id', $sparepart['id'])
                            ->where('work_order_id', $workOrderId)
                            ->update($updateData);

                        // ✅ Insert into work_order_sparepart_usage table for tracking
                        if ($quantityUsed > 0) {
                            $usageData = [
                                'work_order_sparepart_id' => $sparepart['id'],
                                'work_order_id' => $workOrderId,
                                'quantity_used' => $quantityUsed,
                                'quantity_returned' => $quantityReturn > 0 ? $quantityReturn : 0,
                                'usage_notes' => $sparepart['notes'] ?? null,
                                'used_at' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ];
                            
                            $db->table('work_order_sparepart_usage')->insert($usageData);
                            log_message('info', "Created usage record for WO {$workOrderId}, Sparepart: {$originalSparepart['sparepart_name']}, Used Qty: {$quantityUsed}");
                        }

                        // ✅ ONLY create return record if FROM WAREHOUSE and has quantity to return
                        if ($quantityReturn > 0 && $isFromWarehouse == 1) {
                            $returnData = [
                                'work_order_id' => $workOrderId,
                                'work_order_sparepart_id' => $sparepart['id'],
                                'sparepart_code' => $originalSparepart['sparepart_code'] ?? '',
                                'sparepart_name' => $originalSparepart['sparepart_name'] ?? '',
                                'quantity_brought' => $quantityBrought,
                                'quantity_used' => $quantityUsed,
                                'quantity_return' => $quantityReturn,
                                'satuan' => $originalSparepart['satuan'] ?? 'PCS',
                                'status' => 'PENDING',
                                'return_notes' => 'Auto-generated from sparepart validation'
                            ];

                            $returnDb->table('work_order_sparepart_returns')->insert($returnData);
                            log_message('info', "Auto-created return record for WO {$workOrderId}, Sparepart: {$originalSparepart['sparepart_name']}, Return Qty: {$quantityReturn}");
                        } else if ($quantityReturn > 0 && $isFromWarehouse == 0) {
                            // ← NEW: Log skip for non-warehouse sparepart
                            log_message('info', "Skipped return record for NON-WAREHOUSE sparepart - WO {$workOrderId}, Sparepart: {$originalSparepart['sparepart_name']} (Bekas/Reuse)");
                        }
                    }
                }
            }

            // Save additional spareparts
            $additionalSpareparts = $this->request->getPost('additional_spareparts') ?: [];
            foreach ($additionalSpareparts as $sparepart) {
                $qty = (int)($sparepart['quantity'] ?? 0);
                if ($qty <= 0) continue;

                $itemType = $sparepart['item_type'] ?? 'sparepart';

                // Resolve sparepart name and code
                // Select2 AJAX returns id = "KODE - DESC"; manual entry returns plain text; tool uses item_name_manual
                $sparepartCode = '';
                $sparepartName = '';
                if ($itemType === 'tool') {
                    $sparepartName = trim($sparepart['item_name_manual'] ?? '');
                } else {
                    $sparepartId = trim($sparepart['sparepart_id'] ?? '');
                    if (!empty($sparepartId)) {
                        if (str_contains($sparepartId, ' - ')) {
                            // "KODE - NAME" format from Select2
                            [$sparepartCode, $sparepartName] = array_map('trim', explode(' - ', $sparepartId, 2));
                        } else {
                            // Manual plain-text entry
                            $sparepartName = $sparepartId;
                        }
                    }
                }

                if (empty($sparepartName)) {
                    log_message('info', "Skipped additional sparepart with empty name for WO {$workOrderId}");
                    continue;
                }

                $sourceType  = $sparepart['source'] ?? 'WAREHOUSE';
                $sourceUnitId = !empty($sparepart['source_unit_id']) ? (int)$sparepart['source_unit_id'] : null;
                $sourceNotes  = !empty($sparepart['source_notes']) ? $sparepart['source_notes'] : null;

                $additionalData = [
                    'work_order_id'   => $workOrderId,
                    'sparepart_code'  => $sparepartCode,
                    'sparepart_name'  => $sparepartName,
                    'item_type'       => $itemType,
                    'quantity_brought' => $qty,
                    'quantity_used'   => $qty, // For additional, used = brought
                    'satuan'          => $sparepart['satuan'] ?? 'PCS',
                    'source_type'     => $sourceType,
                    'source_unit_id'  => $sourceUnitId,
                    'source_notes'    => $sourceNotes,
                    'notes'           => $sparepart['notes'] ?? '',
                    'is_additional'   => 1,
                    'is_from_warehouse' => ($sourceType === 'WAREHOUSE') ? 1 : 0,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];

                $db->table('work_order_spareparts')->insert($additionalData);
                $additionalSparepartId = $db->insertID();

                if ($additionalSparepartId && $qty > 0) {
                    $usageData = [
                        'work_order_sparepart_id' => $additionalSparepartId,
                        'work_order_id'           => $workOrderId,
                        'quantity_used'           => $qty,
                        'quantity_returned'       => 0,
                        'usage_notes'             => $sparepart['notes'] ?? null,
                        'used_at'                 => date('Y-m-d H:i:s'),
                        'created_at'              => date('Y-m-d H:i:s'),
                        'updated_at'              => date('Y-m-d H:i:s'),
                    ];
                    $db->table('work_order_sparepart_usage')->insert($usageData);
                    log_message('info', "Created usage record for additional sparepart - WO {$workOrderId}, Item: {$sparepartName}");
                }
            }

            // Update work order status to CLOSED after sparepart validation
            $statusData = $this->workOrderModel->getStatusByCode('CLOSED');
            if (!$statusData) {
                throw new \Exception('CLOSED status not found');
            }

            // Use DB builder directly to bypass model $allowedFields (completion_date, sparepart_validated, etc.)
            $woUpdated = $db->table('work_orders')
                ->where('id', $workOrderId)
                ->update([
                    'status_id'              => $statusData['id'],
                    'completion_date'        => date('Y-m-d H:i:s'),
                    'sparepart_validated'    => 1,
                    'sparepart_validated_at' => date('Y-m-d H:i:s'),
                    'updated_at'             => date('Y-m-d H:i:s'),
                ]);

            if (!$woUpdated) {
                throw new \Exception('Failed to update work order status to CLOSED');
            }

            // Add status history
            $this->workOrderModel->addStatusHistory($workOrderId, $statusData['id'], 'Work order closed with sparepart validation completed', $fromStatusId);

            // Revert unit workflow_status now that WO is closed (sparepart validation path)
            $woUnitRow = is_array($currentWorkOrder) ? $currentWorkOrder : (array)($currentWorkOrder ?? []);
            $woUnitId  = $woUnitRow['unit_id'] ?? null;
            if ($woUnitId) {
                $this->revertUnitWorkflowStatus((int)$woUnitId, (int)$workOrderId);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            // Count spareparts used for notification
            $sparepartCount = count($usedSpareparts) + count($additionalSpareparts);
            
            // Get work order details for notification
            $workOrder = $this->workOrderModel->find($workOrderId);
            
            // Send notification - sparepart validation saved
            if (function_exists('notify_sparepart_validation_saved') && $workOrder) {
                notify_sparepart_validation_saved([
                    'id' => $workOrderId,
                    'wo_number' => $workOrder['work_order_number'] ?? '',
                    'sparepart_count' => $sparepartCount,
                    'validated_by' => session('username') ?? session('user_id'),
                    'validation_date' => date('Y-m-d H:i:s'),
                    'url' => base_url('/service/work-orders/view/' . $workOrderId)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sparepart berhasil divalidasi dan work order ditutup'
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'saveSparepartValidation error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Get sparepart usage data for close modal
     */
    public function getSparepartUsageData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        $workOrderId = $this->request->getPost('work_order_id');
        
        if (!$workOrderId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Work Order ID required']);
        }

        try {
            // Get work order info
            $workOrder = $this->workOrderModel->find($workOrderId);
            if (!$workOrder) {
                return $this->response->setJSON(['success' => false, 'message' => 'Work Order not found']);
            }

            // Convert to array if it's an object
            if (is_object($workOrder)) {
                $workOrder = (array) $workOrder;
            }

            // Get brought spareparts for this work order
            $broughtSpareparts = $this->sparepartModel->getSparePartsWithUsage($workOrderId);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'work_order' => $workOrder,
                    'brought_spareparts' => $broughtSpareparts
                ]
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Error getting sparepart usage data. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Save sparepart usage and close work order
     */
    public function saveSparepartUsage()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.']);
        }

        $workOrderId = $this->request->getPost('work_order_id');
        
        if (!$workOrderId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Work Order ID required']);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Process brought spareparts usage
            $broughtSpareparts = $this->request->getPost('brought_spareparts');
            if ($broughtSpareparts && is_array($broughtSpareparts)) {
                $usageData = [];
                foreach ($broughtSpareparts as $item) {
                    if (isset($item['quantity_used']) && $item['quantity_used'] > 0) {
                        $quantityBrought = intval($item['quantity_brought']);
                        $quantityUsed = intval($item['quantity_used']);
                        $quantityReturned = $quantityBrought - $quantityUsed;
                        
                        $usageData[] = [
                            'sparepart_id' => $item['sparepart_id'],
                            'quantity_used' => $quantityUsed,
                            'quantity_returned' => $quantityReturned,
                            'usage_notes' => $item['usage_notes'] ?? '',
                            'return_notes' => $quantityReturned > 0 ? 'Sisa dari perbaikan' : ''
                        ];
                    }
                }
                
                if (!empty($usageData)) {
                    $this->sparepartUsageModel->recordMultipleUsage($workOrderId, $usageData);
                }
            }

            // Process additional spareparts usage
            $additionalSpareparts = $this->request->getPost('additional_spareparts');
            if ($additionalSpareparts && is_array($additionalSpareparts)) {
                $additionalUsageData = [];
                foreach ($additionalSpareparts as $item) {
                    if (!empty($item['sparepart_id']) && $item['quantity_used'] > 0) {
                        $additionalUsageData[] = [
                            'sparepart_id' => $item['sparepart_id'],
                            'quantity_used' => $item['quantity_used'],
                            'quantity_returned' => 0,
                            'usage_notes' => ($item['notes'] ?? '') . ' (Sumber: ' . ($item['source'] ?? 'Unknown') . ')'
                        ];
                    }
                }
                
                if (!empty($additionalUsageData)) {
                    $this->sparepartUsageModel->recordMultipleUsage($workOrderId, $additionalUsageData);
                }
            }

            // Get current status before update for history
            $currentWorkOrder = $this->workOrderModel->find($workOrderId);
            $fromStatusId = null;
            if ($currentWorkOrder && is_array($currentWorkOrder)) {
                $fromStatusId = $currentWorkOrder['status_id'];
            } elseif ($currentWorkOrder && is_object($currentWorkOrder)) {
                $fromStatusId = $currentWorkOrder->status_id ?? null;
            }

            // Update work order status to CLOSED
            $statusData = $this->workOrderModel->getStatusByCode('CLOSED');
            if (!$statusData) {
                throw new \Exception('CLOSED status not found');
            }

            $woUpdateData = [
                'status_id' => $statusData['id'],
                'closed_date' => date('Y-m-d H:i:s'),
                'repair_result' => $this->request->getPost('repair_result'),
                'unit_status_after_repair' => $this->request->getPost('unit_status'),
                'completion_time' => $this->request->getPost('completion_time'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->workOrderModel->skipValidation(true);
            $woUpdated = $this->workOrderModel->update($workOrderId, $woUpdateData);
            $this->workOrderModel->skipValidation(false);

            if (!$woUpdated) {
                throw new \Exception('Failed to update work order status');
            }

            // Add status history with improved tracking
            $this->workOrderModel->addStatusHistory($workOrderId, $statusData['id'], 'Work order closed with sparepart usage tracking', $fromStatusId);

            // Revert unit workflow_status now that WO is closed (sparepart usage path)
            $woUnitRow = is_array($currentWorkOrder) ? $currentWorkOrder : (array)($currentWorkOrder ?? []);
            $woUnitId  = $woUnitRow['unit_id'] ?? null;
            if ($woUnitId) {
                $this->revertUnitWorkflowStatus((int)$woUnitId, (int)$workOrderId);
            }

            // Generate gap alert for admin if there are remaining spareparts
            $this->generateGapAlertBasedOnUsage($workOrderId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            // Count total spareparts used for notification
            $totalSparepartsUsed = 0;
            $sparepartNames = [];
            
            if ($broughtSpareparts && is_array($broughtSpareparts)) {
                foreach ($broughtSpareparts as $item) {
                    if (isset($item['quantity_used']) && $item['quantity_used'] > 0) {
                        $totalSparepartsUsed += intval($item['quantity_used']);
                        if (!empty($item['sparepart_name'])) {
                            $sparepartNames[] = $item['sparepart_name'];
                        }
                    }
                }
            }
            
            if ($additionalSpareparts && is_array($additionalSpareparts)) {
                foreach ($additionalSpareparts as $item) {
                    if (!empty($item['sparepart_id']) && $item['quantity_used'] > 0) {
                        $totalSparepartsUsed += intval($item['quantity_used']);
                        if (!empty($item['sparepart_name'])) {
                            $sparepartNames[] = $item['sparepart_name'];
                        }
                    }
                }
            }
            
            // Get work order details for notification
            $workOrder = $this->workOrderModel->find($workOrderId);
            
            // Send notification - sparepart used
            if (function_exists('notify_sparepart_used') && $workOrder && !empty($sparepartNames)) {
                notify_sparepart_used([
                    'id' => $workOrderId,
                    'wo_number' => $workOrder['work_order_number'] ?? '',
                    'sparepart_name' => implode(', ', array_slice($sparepartNames, 0, 3)), // Max 3 names
                    'quantity' => $totalSparepartsUsed,
                    'unit_code' => $workOrder['unit_code'] ?? '',
                    'used_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/service/work-orders/view/' . $workOrderId)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sparepart usage berhasil disimpan dan work order ditutup'
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Gagal menyimpan data. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.'
            ]);
        }
    }

    /**
     * Generate gap alert for admin about remaining spareparts (updated version)
     */
    private function generateGapAlertBasedOnUsage($workOrderId)
    {
        try {
            // Get pending returns (items that were brought but not fully used)
            $pendingReturns = $this->sparepartUsageModel->getPendingReturns($workOrderId);

            if (!empty($pendingReturns)) {
                $db = \Config\Database::connect();
                
                // Create alert/notification for admin
                $alertData = [
                    'work_order_id' => $workOrderId,
                    'alert_type' => 'SPAREPART_GAP',
                    'title' => 'Sisa Sparepart Perlu Dikembalikan',
                    'message' => 'Work Order memiliki sisa sparepart yang perlu dikembalikan ke warehouse',
                    'remaining_items' => json_encode($pendingReturns),
                    'created_at' => date('Y-m-d H:i:s'),
                    'is_read' => 0,
                    'priority' => 'MEDIUM'
                ];

                // Check if system_alerts table exists, if not create simple log
                if ($db->tableExists('system_alerts')) {
                    $db->table('system_alerts')->insert($alertData);
                } else {
                    log_message('info', 'Gap alert for WO ' . $workOrderId . ': ' . json_encode($pendingReturns));
                }
                
                log_message('info', 'Gap alert generated for work order: ' . $workOrderId);
            }

        } catch (\Throwable $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}