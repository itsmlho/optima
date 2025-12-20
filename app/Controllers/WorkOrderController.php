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
use CodeIgniter\Controller;

class WorkOrderController extends Controller
{
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
        $userRole = $session->get('role_name');
        if (empty($userRole)) {
            return redirect()->to('/')->with('error', 'Access denied: You do not have permission to view work orders');
        }
        
        $data = [
            'title' => 'Work Orders Management',
            'workOrders' => $this->workOrderModel->getAllWorkOrders(),
            'statuses' => $this->workOrderModel->getStatuses(),
            'priorities' => $this->workOrderModel->getPriorities(),
            'categories' => $this->workOrderModel->getCategories(),
            'units' => $this->getUnits(),
            'areas' => $this->areaModel->getActiveAreas(),
            'spareparts' => $this->getSparepartsForDropdown(),
            'staff' => [
                'ADMIN' => $this->workOrderModel->getStaff('ADMIN'),
                'FOREMAN' => $this->workOrderModel->getStaff('FOREMAN'),
                'MECHANIC' => $this->workOrderModel->getStaff('MECHANIC'),
                'HELPER' => $this->workOrderModel->getStaff('HELPER')
            ]
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
        } catch (\Exception $e) {
            log_message('error', 'Error getting spareparts dropdown: ' . $e->getMessage());
            return [];
        }
    }

    // Mendapatkan daftar unit dengan customer dan area info
    private function getUnits()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('inventory_unit iu');
        $builder->select('iu.id_inventory_unit, iu.no_unit, 
                         COALESCE(c.customer_name, "Belum Ada Kontrak") as pelanggan,
                         a.id as area_id, a.area_name, a.area_code,
                         mu.merk_unit, tu.tipe');
        $builder->join('kontrak k', 'iu.kontrak_id = k.id', 'left');
        $builder->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left');
        $builder->join('customers c', 'c.id = cl.customer_id', 'left');
        $builder->join('areas a', 'c.area_id = a.id', 'left');
        $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
        $builder->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left');
        $builder->where('iu.no_unit IS NOT NULL');
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
                    'message' => 'Unit not found'
                ]);
            }
            
            // Direct approach: inventory_unit already has area_id column!
            $builder = $db->table('inventory_unit iu');
            $builder->select('a.id as area_id, a.area_name, a.area_code,
                             s.staff_name as foreman_name, s.id as foreman_id');
            $builder->join('areas a', 'iu.area_id = a.id', 'left');
            $builder->join('area_employee_assignments aea', 'a.id = aea.area_id AND aea.is_active = 1', 'left');
            $builder->join('work_order_staff_backup_final s', 'aea.employee_id = s.id AND s.staff_role = "FOREMAN" AND s.is_active = 1', 'left');
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
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving area information: ' . $e->getMessage()
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
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving spareparts: ' . $e->getMessage()
            ]);
        }
    }


    // Get staff dropdown data
    public function staffDropdown()
    {
        $staffRole = $this->request->getPost('staff_role');
        $areaId = $this->request->getPost('area_id');
        
        if (!$staffRole) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Staff role required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            
            if ($areaId) {
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
                $builder->where('aea.area_id', $areaId);
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
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving staff: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get area staff (admin and foreman) for auto-fill
     */
    public function getAreaStaff()
    {
        $areaId = $this->request->getPost('area_id');
        
        if (!$areaId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Area ID required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            
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
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'admins' => $admins,
                    'foremans' => $foremans
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving area staff: ' . $e->getMessage()
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
            $optimizedModel = new \App\Models\Optimized\OptimizedWorkOrderModel();
            
            // Build parameters for optimized model
            $params = [
                'search' => $search,
                'start' => $start,
                'length' => max(1, $length),
                'orderColumn' => 'wo.report_date',
                'orderDir' => 'DESC',
                'conditions' => []
            ];

            // Add tab-specific conditions
            if ($tab === 'closed') {
                $params['conditions']['wo.status_id'] = 6; // Closed status ID
            } elseif ($tab === 'progress') {
                $params['conditions']['wo.status_id !='] = 6; // Not closed
            }

            // Add additional filters
            $status = $request->getPost('status') ?? $request->getGet('status');
            $priority = $request->getPost('priority') ?? $request->getGet('priority');
            $startDate = $request->getPost('start_date') ?? $request->getGet('start_date');
            $endDate = $request->getPost('end_date') ?? $request->getGet('end_date');
            
            if (!empty($status)) {
                $params['conditions']['wo.status_id'] = $status;
            }
            if (!empty($priority)) {
                $params['conditions']['wo.priority_id'] = $priority;
            }
            if (!empty($startDate)) {
                $params['conditions']['DATE(wo.report_date)'] = $startDate;
            }

            // Get data from optimized model
            $result = $optimizedModel->getDataTableOptimized($params);
            
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

        } catch (\Exception $e) {
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
        
        // Apply division-based department filter using global helper
        $allowedDepartments = get_user_division_departments();
        
        if ($allowedDepartments !== null && is_array($allowedDepartments)) {
            // Filter work orders by unit's department
            $db = \Config\Database::connect();
            $filteredWorkOrders = [];
            foreach ($workOrders as $wo) {
                // Get unit's department
                $unit = $db->table('inventory_unit')
                    ->select('departemen_id')
                    ->where('id_inventory_unit', $wo['unit_id'])
                    ->get()
                    ->getRowArray();
                
                $unitDeptId = $unit['departemen_id'] ?? null;
                if ($unitDeptId && in_array($unitDeptId, $allowedDepartments)) {
                    $filteredWorkOrders[] = $wo;
                }
            }
            $workOrders = $filteredWorkOrders;
        }
        
        // Total records untuk pagination
        $totalRecords = count($workOrders);
        
        // Pagination manual
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
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');
        
        if (!$id || !$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
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
                    break;
                case 'WAITING_PARTS':
                    // Waiting for spare parts - add hold date if needed
                    $updateData['hold_date'] = date('Y-m-d H:i:s');
                    break;
                case 'ON_HOLD':
                    // On hold/pending - add hold date if needed
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
                        SELECT wo.wo_number, iu.no_unit, wos.status_name as old_status
                        FROM work_orders wo
                        LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_unit
                        LEFT JOIN work_order_statuses wos ON wos.id = ?
                        WHERE wo.id = ?
                    ', [$fromStatusId, $id]);
                    $woInfo = $woQuery->getRow();
                    
                    notify_workorder_status_changed([
                        'id' => $id,
                        'wo_number' => $woInfo ? $woInfo->wo_number : 'Unknown WO',
                        'unit_code' => $woInfo ? $woInfo->no_unit : 'Unknown Unit',
                        'old_status' => $woInfo ? $woInfo->old_status : 'Unknown',
                        'new_status' => $statusData['status_name'],
                        'updated_by' => session()->get('user_name') ?? 'System',
                        'url' => base_url('/service/work-order-detail/' . $id)
                    ]);
                    
                    log_message('info', "WorkOrder status updated: {$id} → {$status} - Notification sent");
                } catch (\Exception $notifError) {
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
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
            } catch (\Exception $jsonError) {
                log_message('debug', 'JSON parsing failed (expected for form data): ' . $jsonError->getMessage());
            }
            
            // If no JSON, get POST data
            if (empty($input)) {
                $input = $this->request->getPost();
                log_message('debug', 'WO Store - Using POST Input');
            }
            
            log_message('debug', 'WO Store Final Input: ' . print_r($input, true));
            
            // Debug individual required fields
            log_message('debug', 'WO Store Required Fields Check:');
            log_message('debug', 'unit_id: ' . ($input['unit_id'] ?? 'NULL') . ' (empty: ' . (empty($input['unit_id']) ? 'YES' : 'NO') . ')');
            log_message('debug', 'order_type: ' . ($input['order_type'] ?? 'NULL') . ' (empty: ' . (empty($input['order_type']) ? 'YES' : 'NO') . ')');
            log_message('debug', 'priority_id: ' . ($input['priority_id'] ?? 'NULL') . ' (empty: ' . (empty($input['priority_id']) ? 'YES' : 'NO') . ')');
            log_message('debug', 'category_id: ' . ($input['category_id'] ?? 'NULL') . ' (empty: ' . (empty($input['category_id']) ? 'YES' : 'NO') . ')');
            log_message('debug', 'complaint_description: ' . ($input['complaint_description'] ?? 'NULL') . ' (empty: ' . (empty($input['complaint_description']) ? 'YES' : 'NO') . ')');
            
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
                        log_message('debug', 'Auto-assigned priority_id: ' . $input['priority_id']);
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
                    'message' => 'Validation error',
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
                
                // Check what's still missing and build error message
                if (!$adminId) {
                    $missingRoles[] = 'Admin';
                }
                if (!$foremanId) {
                    $missingRoles[] = 'Foreman';
                }
                if (empty($mechanicIds)) {
                    $missingRoles[] = 'Mechanic (minimal 1)';
                }
                if (empty($helperIds)) {
                    $missingRoles[] = 'Helper (minimal 1)';
                }
                
                if (!empty($missingRoles)) {
                    $areaName = $unitAreaInfo['area_name'] ?? 'Unknown';
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Staff tidak lengkap untuk Area "' . $areaName . '". Staff yang belum di-assign: ' . implode(', ', $missingRoles) . '. Silakan assign staff terlebih dahulu di Area Management.',
                        'missing_roles' => $missingRoles,
                        'area_name' => $areaName,
                        'redirect_hint' => 'Area Management'
                    ]);
                }
            }
            
            log_message('debug', 'Final admin_id: ' . ($adminId ?? 'NULL') . ', foreman_id: ' . ($foremanId ?? 'NULL'));
            
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
                'created_by' => session()->get('user_id') ?? 1
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
                    throw new \Exception('Gagal menyimpan work order: ' . implode(', ', $errors));
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
                    
                    for ($i = 0; $i < count($sparepartNames); $i++) {
                        if (!empty($sparepartNames[$i])) {
                            // Parse sparepart dari format "KODE - NAMA"
                            $parts = explode(' - ', $sparepartNames[$i], 2);
                            if (count($parts) >= 2) {
                                $sparepartCode = trim($parts[0]);
                                $sparepartName = trim($parts[1]);
                                
                                $spareparts[] = [
                                    'sparepart_code' => $sparepartCode,
                                    'sparepart_name' => $sparepartName,
                                    'quantity_brought' => $sparepartQuantities[$i] ?? 1,
                                    'satuan' => $sparepartUnits[$i] ?? 'pcs'
                                ];
                            }
                        }
                    }
                    
                    if (!empty($spareparts)) {
                        $sparepartsAdded = $sparepartModel->addSpareparts($result, $spareparts);
                        if (!$sparepartsAdded) {
                            throw new \Exception('Gagal menyimpan data sparepart');
                        }
                        log_message('debug', 'Spareparts added successfully');
                    }
                }
                
                $db->transComplete();
                
                if ($db->transStatus() === false) {
                    throw new \Exception('Transaksi database gagal');
                }
                
                // Send notification
                try {
                    helper('notification');
                    
                    // Get created WO details
                    $woQuery = $db->query('
                        SELECT wo.wo_number, iu.no_unit, wo.order_type, 
                               p.priority_name, c.category_name, wo.complaint_description
                        FROM work_orders wo
                        LEFT JOIN inventory_unit iu ON wo.unit_id = iu.id_unit
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
                } catch (\Exception $notifError) {
                    log_message('error', 'Failed to send workorder creation notification: ' . $notifError->getMessage());
                }
                
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
                
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error in WorkOrderController::store - ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
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
                'message' => 'Validation error',
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
        
        // Jika status berubah menjadi completed, set completion_date
        if ($oldStatusId != $newStatusId && $newStatusId == 6) {
            $data['completion_date'] = date('Y-m-d H:i:s');
        }
        
        $result = $this->workOrderModel->update($id, $data);
        
        // Jika status berubah, tambahkan riwayat perubahan status
        if ($oldStatusId != $newStatusId) {
            $this->workOrderModel->addStatusHistory($id, $newStatusId, 'Status updated');
        }
        
        if ($result) {
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
                // Log activity
                try {
                    // Log deletion activity
                    log_message('info', 'Work Order deleted: ID=' . $id . ' by user_id=' . (session()->get('user_id') ?? 1) . ' from IP=' . $this->request->getIPAddress());
                } catch (\Exception $logError) {
                    log_message('error', 'Failed to log work order deletion: ' . $logError->getMessage());
                }
                
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
            
        } catch (\Exception $e) {
            log_message('error', 'Error in WorkOrderController::delete - ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
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
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting subcategories: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => false,
                'message' => 'Error getting subcategories'
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
            
        } catch (\Exception $e) {
            log_message('error', 'Error in WorkOrderController::edit - ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
                LEFT JOIN kontrak k ON iu.kontrak_id = k.id
                LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
                LEFT JOIN customers c ON cl.customer_id = c.id
                LEFT JOIN departemen d ON iu.departemen_id = d.id_departemen
                WHERE iu.id_inventory_unit = ?
            ", [$unitId]);
            
            return $query->getRowArray();
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting unit area info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get area staff information for work order assignment
     */
    private function getAreaStaffInfo($unitId)
    {
        try {
            $db = \Config\Database::connect();
            
            // First get the area_id from the unit through customer relationship
            $unitQuery = $db->query("
                SELECT c.area_id 
                FROM inventory_unit iu
                JOIN kontrak k ON iu.kontrak_id = k.id
                JOIN customer_locations cl ON cl.id = k.customer_location_id
                JOIN customers c ON c.id = cl.customer_id
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
                JOIN work_order_staff_backup_final s ON aea.employee_id = s.id
                WHERE aea.area_id = ? AND aea.is_active = 1 AND s.is_active = 1
                ORDER BY s.staff_role, s.staff_name
            ", [$areaId]);
            
            return $query->getResultArray();
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting area staff info: ' . $e->getMessage());
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
            
        } catch (\Exception $e) {
            log_message('error', 'Error generating work order number: ' . $e->getMessage());
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
            
        } catch (\Exception $e) {
            log_message('error', 'Error generating work order number: ' . $e->getMessage());
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
                            c.customer_name as pelanggan, cl.location_name as lokasi, tu.tipe as unit_type, mu.model_unit, mu.merk_unit');
            $builder->join('kontrak k', 'iu.kontrak_id = k.id', 'left');
            $builder->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left');
            $builder->join('customers c', 'c.id = cl.customer_id', 'left');
            $builder->join('tipe_unit tu', 'iu.jenis_unit_id = tu.id_tipe_unit', 'left');
            $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
            $builder->groupStart()
                ->like('iu.no_unit', $query)
                ->orLike('c.customer_name', $query)
                ->orLike('iu.serial_number', $query)
                ->orLike('tu.jenis', $query)
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
            
        } catch (\Exception $e) {
            log_message('error', 'Error searching units: ' . $e->getMessage());
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
                    'message' => 'Priority not found'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting priority: ' . $e->getMessage());
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
                    'message' => 'Subcategory not found'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting subcategory priority: ' . $e->getMessage());
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
            
        } catch (\Exception $e) {
            log_message('error', 'Error calculating TTR: ' . $e->getMessage());
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
            
        } catch (\Exception $e) {
            log_message('error', 'Error updating work order with TTR: ' . $e->getMessage());
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
            
            // Use area_id from inventory_unit (iu.area_id) - the actual unit area
            $sql = "SELECT 
                        iu.id_inventory_unit as id, 
                        iu.no_unit,
                        iu.serial_number,
                        COALESCE(c.customer_name, 'Belum Ada Kontrak') as pelanggan,
                        cl.location_name as lokasi,
                        tu.jenis as jenis,
                        kp.kapasitas_unit as kapasitas,
                        mu.merk_unit as merk,
                        mu.model_unit,
                        a.id as area_id, 
                        a.area_name
                    FROM inventory_unit iu
                    LEFT JOIN kontrak k ON iu.kontrak_id = k.id
                    LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
                    LEFT JOIN customers c ON c.id = cl.customer_id
                    LEFT JOIN areas a ON a.id = iu.area_id
                    LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
                    LEFT JOIN kapasitas kp ON iu.kapasitas_unit_id = kp.id_kapasitas
                    LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
                    WHERE iu.no_unit IS NOT NULL
                    ORDER BY iu.no_unit ASC
                    LIMIT 100";
            
            $result = $db->query($sql);
            $units = $result->getResultArray();
            
            // Handle null values
            foreach ($units as &$unit) {
                $unit['pelanggan'] = $unit['pelanggan'] ?? 'Belum Ada Kontrak';
                $unit['lokasi'] = $unit['lokasi'] ?? 'N/A';
                $unit['jenis'] = $unit['jenis'] ?? 'N/A';
                $unit['kapasitas'] = $unit['kapasitas'] ?? 'N/A';
                $unit['area_id'] = $unit['area_id'] ?? 0;
                $unit['area_name'] = $unit['area_name'] ?? 'N/A';
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $units,
                'count' => count($units)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting units dropdown: ' . $e->getMessage());
            $errorMsg = $this->getMySQLError($db ?? null);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting units data: ' . ($errorMsg ?: $e->getMessage())
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
                } catch (\Exception $e) {
                    log_message('warning', 'Failed to create assignment records: ' . $e->getMessage());
                    // Continue anyway as main work order update succeeded
                }
                
                // Add status history with correct parameters
                $statusHistoryAdded = $this->workOrderModel->addStatusHistory($workOrderId, 2, 'Work order assigned to mechanic and helper', $fromStatusId);
                
                if (!$statusHistoryAdded) {
                    log_message('warning', 'Failed to add status history, but continuing...');
                }
                
                $db->transCommit();
                
                log_message('info', 'Assignment completed successfully');
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Berhasil memilih ' . count($mechanicIds) . ' mekanik dan ' . count($helperIds) . ' helper'
                ]);
                
            } catch (\Exception $e) {
                $db->transRollback();
                log_message('error', 'Transaction failed: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', 'Error assigning employees: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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

        } catch (\Exception $e) {
            log_message('error', 'Error getting work order spareparts: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Close work order with sparepart usage tracking
     */
    public function closeWorkOrder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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

            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', 'Error closing work order: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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

        } catch (\Exception $e) {
            log_message('error', 'Error getting complete data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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

        } catch (\Exception $e) {
            log_message('error', 'Error saving complete data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get unit verification data for complete modal
     */
    public function getUnitVerificationData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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

            // Get unit details with all related data
            $db = \Config\Database::connect();
            $unit = $db->query("
                SELECT 
                    iu.id_inventory_unit,
                    iu.no_unit,
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
                    tu.tipe as tipe_unit_name,
                    mu.model_unit as model_unit_name,
                    mu.merk_unit,
                    k.kapasitas_unit as kapasitas_name,
                    tm.tipe_mast as model_mast_name,
                    m.model_mesin as model_mesin_name,
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
                return $this->response->setJSON(['success' => false, 'message' => 'Unit not found']);
            }

            // Get customer data from unit's contract
            $customerData = $db->query("
                SELECT 
                    c.customer_name as pelanggan,
                    cl.location_name as lokasi
                FROM inventory_unit iu
                LEFT JOIN kontrak k ON iu.kontrak_id = k.id
                LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
                LEFT JOIN customers c ON cl.customer_id = c.id
                WHERE iu.id_inventory_unit = ?
            ", [$workOrder['unit_id']])->getRowArray();
            
            // Add customer data to unit
            $unit['pelanggan'] = $customerData['pelanggan'] ?? 'N/A';
            $unit['lokasi'] = $customerData['lokasi'] ?? 'N/A';

            // Get attachment data from inventory_attachment table
            $attachment = $db->query("
                SELECT 
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
                    a.tipe as attachment_name,
                    a.merk as attachment_merk,
                    a.model as attachment_model,
                    b.tipe_baterai as baterai_name,
                    b.merk_baterai as baterai_merk,
                    c.tipe_charger as charger_name,
                    c.merk_charger as charger_merk
                FROM inventory_attachment ia
                LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
                LEFT JOIN baterai b ON ia.baterai_id = b.id
                LEFT JOIN charger c ON ia.charger_id = c.id_charger
                WHERE ia.id_inventory_unit = ?
            ", [$workOrder['unit_id']])->getRowArray();

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

            $attachmentOptions = $db->table('attachment')
                ->select('id_attachment as id, CONCAT(tipe, " - ", merk) as name')
                ->get()
                ->getResultArray();

            $bateraiOptions = $db->table('baterai')
                ->select('id as id, CONCAT(tipe_baterai, " - ", merk_baterai) as name')
                ->get()
                ->getResultArray();

            $chargerOptions = $db->table('charger')
                ->select('id_charger as id, CONCAT(tipe_charger, " - ", merk_charger) as name')
                ->get()
                ->getResultArray();

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
            $assignedStaff = [];
            if (!empty($workOrder['mechanic_id'])) {
                $mechanic = $db->table('employees')->select('staff_name')->where('id', $workOrder['mechanic_id'])->get()->getRowArray();
                if ($mechanic) $assignedStaff[] = $mechanic['staff_name'];
            }
            if (!empty($workOrder['helper_id'])) {
                $helper = $db->table('employees')->select('staff_name')->where('id', $workOrder['helper_id'])->get()->getRowArray();
                if ($helper) $assignedStaff[] = $helper['staff_name'];
            }   
            $verifiedBy = implode(' & ', $assignedStaff);
            
            // If no staff assigned, use current user or default
            if (empty($verifiedBy)) {
                $verifiedBy = 'Mekanik Belum Ditentukan';
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'work_order' => $workOrder,
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

        } catch (\Exception $e) {
            log_message('error', 'Error getting unit verification data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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
        } catch (\Exception $e) {
            log_message('error', 'Error getting MySQL error: ' . $e->getMessage());
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
            } catch (\Exception $e) {
                // Ignore
            }
        }
        
        return 'Unknown database error';
    }

    /**
     * Save unit verification and complete work order
     */
    public function saveUnitVerification()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $workOrderId = $this->request->getPost('work_order_id');
        $unitId = $this->request->getPost('unit_id');

        if (!$workOrderId || !$unitId) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Data tidak lengkap - Work Order ID atau Unit ID tidak ditemukan'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Validate work order and unit exist
            $woExists = $db->table('work_orders')->where('id', $workOrderId)->countAllResults() > 0;
            if (!$woExists) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work order tidak ditemukan'
                ]);
            }
            
            // VALIDASI: Pastikan repair_description sudah diisi
            $workOrder = $db->table('work_orders')
                ->select('repair_description, notes')
                ->where('id', $workOrderId)
                ->get()
                ->getRowArray();
            
            if (empty($workOrder['repair_description'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Analysis & Repair harus diisi terlebih dahulu. Silakan klik tombol Complete untuk melengkapi data.'
                ]);
            }
            
            $unitExists = $db->table('inventory_unit')->where('id_inventory_unit', $unitId)->countAllResults() > 0;
            if (!$unitExists) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan'
                ]);
            }
            
            $db->transStart();

            // Update inventory_unit table with unit verification data
            $unitUpdateData = [
                'no_unit' => $this->request->getPost('no_unit'),
                'serial_number' => $this->request->getPost('serial_number'),
                'tahun_unit' => $this->request->getPost('tahun_unit'),
                'departemen_id' => $this->request->getPost('departemen_id') ?: null,
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

            // Check transaction status before update
            if ($db->transStatus() === false) {
                $errorMsg = $this->getMySQLError($db);
                throw new \Exception('Transaksi gagal sebelum update unit: ' . $errorMsg);
            }

            $updated = $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update($unitUpdateData);

            // Check transaction status immediately after update
            if ($db->transStatus() === false) {
                $errorMsg = $this->getMySQLError($db);
                log_message('error', 'Transaction failed after inventory_unit update. Unit ID: ' . $unitId . ', Error: ' . $errorMsg);
                log_message('error', 'Update data: ' . json_encode($unitUpdateData));
                throw new \Exception('Error update unit: ' . $errorMsg);
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

            // Handle inventory_attachment table - UPDATE existing records to preserve po_id and catatan_inventory
            $attachmentId = $this->request->getPost('attachment_id');
            $chargerId = $this->request->getPost('charger_id');
            $bateraiId = $this->request->getPost('baterai_id');
            
            // Get existing attachment records to preserve po_id and catatan_inventory
            $existingAttachments = $db->table('inventory_attachment')
                ->where('id_inventory_unit', $unitId)
                ->get()->getResultArray();
            
            // Create map of existing records by type
            $existingMap = [];
            foreach ($existingAttachments as $existing) {
                $existingMap[$existing['tipe_item']] = $existing;
            }
            
            // Check transaction status before delete
            if ($db->transStatus() === false) {
                $errorMsg = $this->getMySQLError($db);
                log_message('error', 'Transaction failed before deleting inventory_attachment. Error: ' . $errorMsg);
                throw new \Exception('Transaksi gagal sebelum delete attachment: ' . $errorMsg);
            }

            // Delete all existing records first
            $db->table('inventory_attachment')->where('id_inventory_unit', $unitId)->delete();
            
            // Check transaction status after delete
            if ($db->transStatus() === false) {
                $errorMsg = $this->getMySQLError($db);
                log_message('error', 'Transaction failed after deleting inventory_attachment. Error: ' . $errorMsg);
                throw new \Exception('Transaksi gagal setelah delete attachment: ' . $errorMsg);
            }
            
            // Insert/Update attachment record if selected
            if (!empty($attachmentId)) {
                // Validate attachment_id exists in attachment table
                $attachmentExists = $db->table('attachment')
                    ->where('id_attachment', $attachmentId)
                    ->countAllResults() > 0;
                
                if (!$attachmentExists) {
                    // Skip if attachment doesn't exist
                } else {
                    $attachmentData = [
                        'id_inventory_unit' => $unitId,
                        'tipe_item' => 'attachment',
                        'attachment_id' => $attachmentId,
                        'sn_attachment' => $this->request->getPost('sn_attachment'),
                        'kondisi_fisik' => $this->request->getPost('kondisi_fisik') ?: 'Baik',
                        'kelengkapan' => $this->request->getPost('kelengkapan') ?: 'Lengkap',
                        'catatan_fisik' => $this->request->getPost('catatan_fisik'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                
                // Preserve po_id and catatan_inventory if they existed
                if (isset($existingMap['attachment'])) {
                    if (!empty($existingMap['attachment']['po_id'])) {
                        $attachmentData['po_id'] = $existingMap['attachment']['po_id'];
                    }
                    if (!empty($existingMap['attachment']['catatan_inventory'])) {
                        $attachmentData['catatan_inventory'] = $existingMap['attachment']['catatan_inventory'];
                    }
                    // Preserve created_at if updating existing record
                    if (!empty($existingMap['attachment']['created_at'])) {
                        $attachmentData['created_at'] = $existingMap['attachment']['created_at'];
                        $attachmentData['updated_at'] = date('Y-m-d H:i:s');
                    }
                }
                
                    // Check transaction status before insert
                    if ($db->transStatus() === false) {
                        $errorMsg = $this->getMySQLError($db);
                        log_message('error', 'Transaction failed before inserting attachment. Error: ' . $errorMsg);
                        throw new \Exception('Transaksi gagal sebelum insert attachment: ' . $errorMsg);
                    }

                    $insertResult = $db->table('inventory_attachment')->insert($attachmentData);
                    
                    // Check transaction status immediately after insert
                    if ($db->transStatus() === false) {
                        $errorMsg = $this->getMySQLError($db);
                        log_message('error', 'Transaction failed after inserting attachment. Data: ' . json_encode($attachmentData) . ', Error: ' . $errorMsg);
                        throw new \Exception('Error menyimpan attachment: ' . $errorMsg);
                    }
                    
                    $errorMsg = $this->getMySQLError($db);
                    if ((!empty($errorMsg) && strpos($errorMsg, 'Unknown database error') === false) || !$insertResult) {
                        log_message('error', 'Failed to insert attachment. Result: ' . ($insertResult ? 'true' : 'false') . ', Error: ' . $errorMsg);
                        throw new \Exception('Error menyimpan attachment: ' . $errorMsg);
                    }
                }
            }
            
            // Insert/Update charger record if selected
            if (!empty($chargerId)) {
                // Validate charger_id exists in charger table
                $chargerExists = $db->table('charger')
                    ->where('id_charger', $chargerId)
                    ->countAllResults() > 0;
                
                if (!$chargerExists) {
                    // Skip if charger doesn't exist
                } else {
                    $chargerData = [
                        'id_inventory_unit' => $unitId,
                        'tipe_item' => 'charger',
                        'charger_id' => $chargerId,
                        'sn_charger' => $this->request->getPost('sn_charger'),
                        'kondisi_fisik' => $this->request->getPost('kondisi_fisik') ?: 'Baik',
                        'kelengkapan' => $this->request->getPost('kelengkapan') ?: 'Lengkap',
                        'catatan_fisik' => $this->request->getPost('catatan_fisik'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                
                // Preserve po_id and catatan_inventory if they existed
                if (isset($existingMap['charger'])) {
                    if (!empty($existingMap['charger']['po_id'])) {
                        $chargerData['po_id'] = $existingMap['charger']['po_id'];
                    }
                    if (!empty($existingMap['charger']['catatan_inventory'])) {
                        $chargerData['catatan_inventory'] = $existingMap['charger']['catatan_inventory'];
                    }
                    // Preserve created_at if updating existing record
                    if (!empty($existingMap['charger']['created_at'])) {
                        $chargerData['created_at'] = $existingMap['charger']['created_at'];
                        $chargerData['updated_at'] = date('Y-m-d H:i:s');
                    }
                }
                
                    // Check transaction status before insert
                    if ($db->transStatus() === false) {
                        $errorMsg = $this->getMySQLError($db);
                        log_message('error', 'Transaction failed before inserting charger. Error: ' . $errorMsg);
                        throw new \Exception('Transaksi gagal sebelum insert charger: ' . $errorMsg);
                    }

                    $insertResult = $db->table('inventory_attachment')->insert($chargerData);
                    
                    // Check transaction status after insert
                    if ($db->transStatus() === false) {
                        $errorMsg = $this->getMySQLError($db);
                        log_message('error', 'Transaction failed after inserting charger. Error: ' . $errorMsg);
                        throw new \Exception('Error menyimpan charger: ' . $errorMsg);
                    }
                    
                    $errorMsg = $this->getMySQLError($db);
                    if ((!empty($errorMsg) && strpos($errorMsg, 'Unknown database error') === false) || !$insertResult) {
                        throw new \Exception('Error menyimpan charger: ' . $errorMsg);
                    }
                }
            }
            
            // Insert/Update baterai record if selected
            if (!empty($bateraiId)) {
                // Validate baterai_id exists in baterai table (primary key is 'id', not 'id_baterai')
                $bateraiExists = $db->table('baterai')
                    ->where('id', $bateraiId)
                    ->countAllResults() > 0;
                
                if (!$bateraiExists) {
                    // Skip if baterai doesn't exist
                } else {
                    $bateraiData = [
                        'id_inventory_unit' => $unitId,
                        'tipe_item' => 'baterai',
                        'baterai_id' => $bateraiId,
                        'sn_baterai' => $this->request->getPost('sn_baterai'),
                        'kondisi_fisik' => $this->request->getPost('kondisi_fisik') ?: 'Baik',
                        'kelengkapan' => $this->request->getPost('kelengkapan') ?: 'Lengkap',
                        'catatan_fisik' => $this->request->getPost('catatan_fisik'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                
                // Preserve po_id and catatan_inventory if they existed
                if (isset($existingMap['baterai'])) {
                    if (!empty($existingMap['baterai']['po_id'])) {
                        $bateraiData['po_id'] = $existingMap['baterai']['po_id'];
                    }
                    if (!empty($existingMap['baterai']['catatan_inventory'])) {
                        $bateraiData['catatan_inventory'] = $existingMap['baterai']['catatan_inventory'];
                    }
                    // Preserve created_at if updating existing record
                    if (!empty($existingMap['baterai']['created_at'])) {
                        $bateraiData['created_at'] = $existingMap['baterai']['created_at'];
                        $bateraiData['updated_at'] = date('Y-m-d H:i:s');
                    }
                }
                
                    // Check transaction status before insert
                    if ($db->transStatus() === false) {
                        $errorMsg = $this->getMySQLError($db);
                        log_message('error', 'Transaction failed before inserting baterai. Error: ' . $errorMsg);
                        throw new \Exception('Transaksi gagal sebelum insert baterai: ' . $errorMsg);
                    }

                    $insertResult = $db->table('inventory_attachment')->insert($bateraiData);
                    
                    // Check transaction status after insert
                    if ($db->transStatus() === false) {
                        $errorMsg = $this->getMySQLError($db);
                        log_message('error', 'Transaction failed after inserting baterai. Error: ' . $errorMsg);
                        throw new \Exception('Error menyimpan baterai: ' . $errorMsg);
                    }
                    
                    $errorMsg = $this->getMySQLError($db);
                    if ((!empty($errorMsg) && strpos($errorMsg, 'Unknown database error') === false) || !$insertResult) {
                        throw new \Exception('Error menyimpan baterai: ' . $errorMsg);
                    }
                }
            }

            // Check transaction status before accessories update
            if ($db->transStatus() === false) {
                $errorMsg = $this->getMySQLError($db);
                log_message('error', 'Transaction failed before accessories update. Error: ' . $errorMsg);
                throw new \Exception('Transaksi gagal sebelum update accessories: ' . $errorMsg);
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
                $db->table('inventory_unit')
                    ->where('id_inventory_unit', $unitId)
                    ->update($inventoryUpdateData);
            }
            
            // Check transaction status after accessories update
            if ($db->transStatus() === false) {
                $errorMsg = $this->getMySQLError($db);
                log_message('error', 'Transaction failed after accessories update. Error: ' . $errorMsg);
                throw new \Exception('Transaksi gagal setelah update accessories: ' . $errorMsg);
            }
            
            // Get current status before update for history
            $currentWorkOrder = $db->table('work_orders')
                ->where('id', $workOrderId)
                ->get()
                ->getRowArray();
            
            $fromStatusId = $currentWorkOrder['status_id'] ?? null;

            // Get COMPLETED status
            $statusData = $db->table('work_order_statuses')
                ->where('status_code', 'COMPLETED')
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
                
            if (!$statusData) {
                throw new \Exception('Status COMPLETED tidak ditemukan');
            }

            $woUpdateData = [
                'status_id' => $statusData['id'],
                'completion_date' => date('Y-m-d H:i:s'),
                'unit_verified' => 1,
                'unit_verified_at' => date('Y-m-d H:i:s'),
                'notes' => $this->request->getPost('catatan_fisik'),
                'hm' => $this->request->getPost('hm') ?: null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update work order
            $woUpdated = $db->table('work_orders')
                ->where('id', $workOrderId)
                ->update($woUpdateData);

            $errorMsg = $this->getMySQLError($db);
            if ((!empty($errorMsg) && strpos($errorMsg, 'Unknown database error') === false) || !$woUpdated) {
                throw new \Exception('Error update work order: ' . $errorMsg);
            }
            
            if ($db->transStatus() === false) {
                $errorMsg = $this->getMySQLError($db);
                throw new \Exception('Transaksi gagal setelah update work order: ' . $errorMsg);
            }

            // Insert status history
            $historyData = [
                'work_order_id' => $workOrderId,
                'from_status_id' => $fromStatusId,
                'to_status_id' => $statusData['id'],
                'changed_by' => session()->get('user_id') ?: 1,
                'change_reason' => 'Work order completed with unit verification',
                'changed_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('work_order_status_history')->insert($historyData);
            
            $db->transComplete();

            if ($db->transStatus() === false) {
                $errorMsg = $this->getMySQLError($db);
                throw new \Exception('Transaksi gagal: ' . $errorMsg);
            }
            
            // Get work order details for notification
            $workOrder = $db->table('work_orders')
                ->where('id', $workOrderId)
                ->get()
                ->getRowArray();
            
            // Send notification - unit verification saved
            if (function_exists('notify_unit_verification_saved') && $workOrder) {
                notify_unit_verification_saved([
                    'id' => $workOrderId,
                    'wo_number' => $workOrder['work_order_number'] ?? '',
                    'unit_code' => $workOrder['unit_code'] ?? '',
                    'verification_status' => 'COMPLETED',
                    'verified_by' => session('username') ?? session('user_id'),
                    'verification_date' => date('Y-m-d H:i:s'),
                    'url' => base_url('/service/work-orders/view/' . $workOrderId)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Unit berhasil diverifikasi dan work order diselesaikan'
            ]);

        } catch (\Exception $e) {
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
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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

        } catch (\Exception $e) {
            log_message('error', 'Error getting sparepart validation data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get sparepart master data for dropdown
     */
    public function getSparepartMaster()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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

        } catch (\Exception $e) {
            log_message('error', 'Error getting sparepart master data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save sparepart validation and close work order
     */
    public function saveSparepartValidation()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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

            // Load return model for auto-creating return records
            $returnModel = new \App\Models\WorkOrderSparepartReturnModel();

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

                        // Auto-create return record if there's quantity to return
                        if ($quantityReturn > 0) {
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

                            $returnModel->insert($returnData);
                            log_message('info', "Auto-created return record for WO {$workOrderId}, Sparepart: {$originalSparepart['sparepart_name']}, Return Qty: {$quantityReturn}");
                        }
                    }
                }
            }

            // Save additional spareparts
            $additionalSpareparts = $this->request->getPost('additional_spareparts') ?: [];
            foreach ($additionalSpareparts as $sparepart) {
                if (!empty($sparepart['sparepart_id']) && !empty($sparepart['quantity'])) {
                    // Get sparepart details from master
                    $sparepartData = $db->table('sparepart')
                        ->where('id_sparepart', $sparepart['sparepart_id'])
                        ->get()
                        ->getRowArray();
                    
                    $additionalData = [
                        'work_order_id' => $workOrderId,
                        'sparepart_code' => $sparepartData['kode'] ?? '',
                        'sparepart_name' => $sparepartData['desc_sparepart'] ?? '',
                        'quantity_brought' => $sparepart['quantity'],
                        'quantity_used' => $sparepart['quantity'], // For additional, used = planned
                        'satuan' => $sparepart['satuan'] ?? 'PCS',
                        'notes' => $sparepart['notes'] ?? '',
                        'is_additional' => 1, // Mark as additional sparepart
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $db->table('work_order_spareparts')->insert($additionalData);
                }
            }

            // Update work order status to CLOSED after sparepart validation
            $statusData = $this->workOrderModel->getStatusByCode('CLOSED');
            if (!$statusData) {
                throw new \Exception('CLOSED status not found');
            }

            $woUpdateData = [
                'status_id' => $statusData['id'],
                'closed_date' => date('Y-m-d H:i:s'),
                'sparepart_validated' => 1,
                'sparepart_validated_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->workOrderModel->skipValidation(true);
            $woUpdated = $this->workOrderModel->update($workOrderId, $woUpdateData);
            $this->workOrderModel->skipValidation(false);

            if (!$woUpdated) {
                throw new \Exception('Failed to update work order status');
            }

            // Add status history
            $this->workOrderModel->addStatusHistory($workOrderId, $statusData['id'], 'Work order closed with sparepart validation completed', $fromStatusId);

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

        } catch (\Exception $e) {
            log_message('error', 'Error saving sparepart validation: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get sparepart usage data for close modal
     */
    public function getSparepartUsageData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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

        } catch (\Exception $e) {
            log_message('error', 'Error getting sparepart usage data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save sparepart usage and close work order
     */
    public function saveSparepartUsage()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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

        } catch (\Exception $e) {
            log_message('error', 'Error saving sparepart usage: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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

        } catch (\Exception $e) {
            log_message('error', 'Error generating gap alert: ' . $e->getMessage());
        }
    }
}