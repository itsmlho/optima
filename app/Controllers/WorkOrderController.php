<?php

namespace App\Controllers;

use App\Models\WorkOrderModel;
use App\Models\AreaStaffAssignmentModel;
use App\Models\StaffModel;
use App\Models\CustomerModel;
use App\Models\AreaModel;
use CodeIgniter\Controller;

class WorkOrderController extends Controller
{
    protected $workOrderModel;
    protected $areaStaffModel;
    protected $staffModel;
    protected $customerModel;
    protected $areaModel;
    
    public function __construct()
    {
        $this->workOrderModel = new WorkOrderModel();
        $this->areaStaffModel = new AreaStaffAssignmentModel();
        $this->staffModel = new StaffModel();
        $this->customerModel = new CustomerModel();
        $this->areaModel = new AreaModel();
    }
    
    // Menampilkan halaman daftar work order
    public function index()
    {
        $data = [
            'title' => 'Work Orders Management',
            'workOrders' => $this->workOrderModel->getAllWorkOrders(),
            'statuses' => $this->workOrderModel->getStatuses(),
            'priorities' => $this->workOrderModel->getPriorities(),
            'categories' => $this->workOrderModel->getCategories(),
            'units' => $this->getUnits(),
            'areas' => $this->areaModel->getActiveAreas(),
            'staff' => [
                'ADMIN' => $this->workOrderModel->getStaff('ADMIN'),
                'FOREMAN' => $this->workOrderModel->getStaff('FOREMAN'),
                'MECHANIC' => $this->workOrderModel->getStaff('MECHANIC'),
                'HELPER' => $this->workOrderModel->getStaff('HELPER')
            ]
        ];
        
        return view('service/work_orders', $data);
    }
    
    // Mendapatkan daftar unit dengan customer dan area info
    private function getUnits()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('inventory_unit iu');
        $builder->select('iu.id_inventory_unit, iu.no_unit, 
                         COALESCE(c.customer_name, k.pelanggan) as pelanggan,
                         a.area_name, a.area_code,
                         mu.merk_unit, tu.tipe');
        $builder->join('kontrak k', 'iu.kontrak_id = k.id', 'left');
        $builder->join('customers c', 'iu.customer_id = c.id', 'left');
        $builder->join('areas a', 'c.area_id = a.id', 'left');
        $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
        $builder->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left');
        $builder->where('iu.no_unit IS NOT NULL');
        $builder->orderBy('iu.no_unit', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    // Mendapatkan data work order untuk DataTable
    public function getWorkOrders()
    {
        $request = $this->request;
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchData = $request->getPost('search');
        $search = isset($searchData['value']) ? $searchData['value'] : '';
        
        // Pagination parameters - prevent division by zero
        $length = max(1, $length); // Ensure length is at least 1
        $page = ($start / $length) + 1;
        $perPage = $length;
        
        // Status filter jika ada
        $status = $request->getPost('status');
        $priority = $request->getPost('priority');
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
        
        // Mendapatkan data work order dengan filter
        $workOrders = $this->workOrderModel->searchWorkOrders(
            $search,
            $status,
            $priority,
            $startDate,
            $endDate
        );
        
        // Hitung total data
        $totalRecords = $this->workOrderModel->countAllWorkOrders();
        $totalFilteredRecords = count($workOrders);
        
        // Pagination manual
        $workOrders = array_slice($workOrders, $start, $length);
        
        $data = [];
        $no = $start + 1;
        
        foreach ($workOrders as $wo) {
            // Dynamic action buttons based on status
            $action = $this->getStatusActionButton($wo['status_code'], $wo['id']);
            
            $statusBadge = '<span class="badge bg-'.$wo['status_color'].'">'.$wo['status'].'</span>';
            $priorityBadge = '<span class="badge bg-'.$wo['priority_color'].'">'.$wo['priority'].'</span>';
            
            // Format unit info
            $unitInfo = $wo['no_unit'] . ' - ' . $wo['pelanggan'] . ' (' . $wo['merk_unit'] . ' ' . $wo['model_unit'] . ')';
            
            $row = [];
            $row[] = $no++;
            $row[] = $wo['work_order_number'];
            $row[] = date('d/m/Y H:i', strtotime($wo['report_date']));
            $row[] = $unitInfo;
            $row[] = $wo['order_type'];
            $row[] = $priorityBadge;
            $row[] = $wo['category'];
            $row[] = $statusBadge;
            $row[] = $action;
            
            // Add data attributes for onclick functionality
            $row['DT_RowAttr'] = [
                'data-wo-id' => $wo['id'],
                'data-wo-number' => $wo['work_order_number'],
                'data-status-code' => $wo['status_code']
            ];
            
            $data[] = $row;
        }
        
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFilteredRecords,
            'data' => $data
        ];
        
        return $this->response->setJSON($response);
    }
    
    // Generate dynamic action buttons based on status
    private function getStatusActionButton($statusCode, $woId)
    {
        $buttons = [];
        
        switch ($statusCode) {
            case 'PENDING':
                $buttons[] = '<button type="button" class="btn btn-sm btn-primary btn-assign" data-id="'.$woId.'">Assign</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-danger btn-delete-wo" data-id="'.$woId.'" title="Hapus WO">
                                <i class="fas fa-trash"></i></button>';
                break;
                
            case 'ASSIGNED':
                $buttons[] = '<button type="button" class="btn btn-sm btn-success btn-start" data-id="'.$woId.'">Start Work</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-secondary btn-reassign" data-id="'.$woId.'">Reassign</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-danger btn-delete-wo" data-id="'.$woId.'" title="Hapus WO">
                                <i class="fas fa-trash"></i></button>';
                break;
                
            case 'IN_PROGRESS':
                $buttons[] = '<button type="button" class="btn btn-sm btn-warning btn-pause" data-id="'.$woId.'">Pause</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-success btn-complete" data-id="'.$woId.'">Complete</button>';
                break;
                
            case 'ON_HOLD':
                $buttons[] = '<button type="button" class="btn btn-sm btn-primary btn-resume" data-id="'.$woId.'">Resume</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-danger btn-cancel" data-id="'.$woId.'">Cancel</button>';
                break;
                
            case 'COMPLETED':
                $buttons[] = '<button type="button" class="btn btn-sm btn-success btn-close-wo" data-id="'.$woId.'">Close</button>';
                $buttons[] = '<button type="button" class="btn btn-sm btn-warning btn-reopen" data-id="'.$woId.'">Reopen</button>';
                break;
                
            case 'CLOSED':
                $buttons[] = '<button type="button" class="btn btn-sm btn-info btn-reopen" data-id="'.$woId.'">Reopen</button>';
                break;
                
            case 'CANCELLED':
                $buttons[] = '<button type="button" class="btn btn-sm btn-info btn-reopen" data-id="'.$woId.'">Reopen</button>';
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
            }
            
            $updated = $this->workOrderModel->update($id, $updateData);
            
            if ($updated) {
                // Log the status change
                if ($notes) {
                    // Add activity log if notes provided
                    $this->workOrderModel->addActivityLog($id, $status, $notes);
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
                'complaint_description' => 'required|min_length[5]'
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
                    'min_length' => 'Deskripsi keluhan minimal 5 karakter'
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
            } elseif (strlen(trim($input['complaint_description'])) < 5) {
                $errors['complaint_description'] = 'Deskripsi keluhan minimal 5 karakter';
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
                        $errors['priority_id'] = 'Priority harus dipilih atau kategori harus memiliki priority default';
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
            
            // Auto assign staff based on unit area (if no manual staff selection)
            $autoStaffAssignment = $this->autoAssignStaff($input['unit_id']);
            
            // Get unit area info for logging
            $unitAreaInfo = $this->getUnitAreaInfo($input['unit_id']);
            log_message('debug', 'Unit Area Info: ' . print_r($unitAreaInfo, true));
            log_message('debug', 'Auto Staff Assignment: ' . print_r($autoStaffAssignment, true));
            
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
                'status_id' => $defaultStatus->id, // Set default status
                // Use manual assignment if provided, otherwise use auto assignment
                'admin_staff_id' => $input['admin_staff_id'] ?? $autoStaffAssignment['admin_staff_id'],
                'foreman_staff_id' => $input['foreman_staff_id'] ?? $autoStaffAssignment['foreman_staff_id'],
                'mechanic_staff_id' => $input['mechanic_staff_id'] ?? $autoStaffAssignment['mechanic_staff_id'],
                'helper_staff_id' => $input['helper_staff_id'] ?? $autoStaffAssignment['helper_staff_id'],
                'area' => $input['area'] ?? ($unitAreaInfo['area_name'] ?? null),
                'created_by' => session()->get('user_id') ?? 1 // Use session user ID or default to 1
            ];
            
            $result = $this->workOrderModel->insert($data);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Work Order berhasil dibuat dengan nomor: ' . $woNumber,
                    'data' => [
                        'id' => $result,
                        'work_order_number' => $woNumber,
                        'auto_assigned_staff' => $autoStaffAssignment['assigned_staff_names'],
                        'unit_area' => $unitAreaInfo['area_name'] ?? 'Unknown'
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membuat Work Order',
                    'errors' => $this->workOrderModel->errors()
                ]);
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
    
    // Mendapatkan detail work order
    public function edit($id)
    {
        $workOrder = $this->workOrderModel->find($id);
        
        if (!$workOrder) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Work Order tidak ditemukan'
            ]);
        }
        
        // Mendapatkan subkategori berdasarkan kategori
        $subcategories = $this->workOrderModel->getSubcategories($workOrder['category_id']);
        
        $data = [
            'title' => 'Edit Work Order',
            'workOrder' => $workOrder,
            'statuses' => $this->workOrderModel->getStatuses(),
            'priorities' => $this->workOrderModel->getPriorities(),
            'categories' => $this->workOrderModel->getCategories(),
            'subcategories' => $subcategories,
            'adminStaff' => $this->workOrderModel->getStaff('ADMIN'),
            'foremanStaff' => $this->workOrderModel->getStaff('FOREMAN'),
            'mechanicStaff' => $this->workOrderModel->getStaff('MECHANIC'),
            'helperStaff' => $this->workOrderModel->getStaff('HELPER')
        ];
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
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
            'admin_staff_id' => $this->request->getPost('admin_staff_id'),
            'foreman_staff_id' => $this->request->getPost('foreman_staff_id'),
            'mechanic_staff_id' => $this->request->getPost('mechanic_staff_id'),
            'helper_staff_id' => $this->request->getPost('helper_staff_id'),
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
            $this->workOrderModel->addStatusHistory($id, $oldStatusId, $newStatusId, 1, 'Status updated');
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
            
            // Check if work order can be deleted (not completed or in critical status)
            if (in_array($workOrder['status_id'], [3, 4])) { // Assuming 3=Completed, 4=Closed
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
                    helper('activity_log');
                    log_delete('work_orders', $id, $workOrder, [
                        'user_id' => session()->get('user_id') ?? 1,
                        'is_critical' => true,
                        'module_name' => 'SERVICE'
                    ]);
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
     * Auto assign staff based on unit's area
     */
    private function autoAssignStaff($unitId)
    {
        try {
            $assignedStaff = [
                'admin_staff_id' => null,
                'foreman_staff_id' => null,
                'mechanic_staff_id' => null,
                'helper_staff_id' => null,
                'assigned_staff_names' => []
            ];
            
            // Get staff assigned to the unit's area for each role
            $roles = ['ADMIN', 'FOREMAN', 'MECHANIC', 'HELPER'];
            
            foreach ($roles as $role) {
                $staff = $this->areaStaffModel->getStaffForUnit($unitId, $role);
                
                if (!empty($staff)) {
                    // Get first PRIMARY assignment, or any if no PRIMARY found
                    $selectedStaff = null;
                    foreach ($staff as $person) {
                        if ($person['assignment_type'] === 'PRIMARY') {
                            $selectedStaff = $person;
                            break;
                        }
                    }
                    
                    // If no PRIMARY found, use first available
                    if (!$selectedStaff && !empty($staff)) {
                        $selectedStaff = $staff[0];
                    }
                    
                    if ($selectedStaff) {
                        $staffIdField = strtolower($role) . '_staff_id';
                        $assignedStaff[$staffIdField] = $selectedStaff['staff_id'];
                        $assignedStaff['assigned_staff_names'][strtolower($role)] = $selectedStaff['staff_name'];
                    }
                }
            }
            
            return $assignedStaff;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in autoAssignStaff: ' . $e->getMessage());
            return [
                'admin_staff_id' => null,
                'foreman_staff_id' => null,
                'mechanic_staff_id' => null,
                'helper_staff_id' => null,
                'assigned_staff_names' => []
            ];
        }
    }
    
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
                    c.customer_name,
                    a.area_name,
                    a.area_code
                FROM inventory_unit iu
                LEFT JOIN customers c ON iu.customer_id = c.id
                LEFT JOIN areas a ON c.area_id = a.id
                WHERE iu.id_inventory_unit = ?
            ", [$unitId]);
            
            return $query->getRowArray();
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting unit area info: ' . $e->getMessage());
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
                            k.pelanggan, k.lokasi, tu.tipe as unit_type, mu.model_unit, mu.merk_unit');
            $builder->join('kontrak k', 'iu.kontrak_id = k.id', 'left');
            $builder->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left');
            $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
            $builder->groupStart()
                ->like('iu.no_unit', $query)
                ->orLike('k.pelanggan', $query)
                ->orLike('iu.serial_number', $query)
                ->orLike('tu.tipe', $query)
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
    
    // Search Staff
    public function searchStaff()
    {
        $query = $this->request->getPost('query');
        $staffType = $this->request->getPost('staff_type');
        
        if (empty($query) || empty($staffType)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Query and staff type are required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('work_order_staff');
            $builder->select('id, staff_name, staff_role as position');
            $builder->where('staff_role', strtoupper($staffType));
            $builder->where('is_active', 1);
            $builder->like('staff_name', $query);
            $builder->limit(10);
            
            $staff = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $staff
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error searching staff: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error searching staff'
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

    // Get Units for Dropdown
    public function getUnitsDropdown()
    {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('inventory_unit iu');
            $builder->select('iu.id_inventory_unit as id, iu.no_unit, iu.serial_number, 
                            k.pelanggan, k.lokasi, tu.tipe as unit_type, mu.model_unit, mu.merk_unit');
            $builder->join('kontrak k', 'iu.kontrak_id = k.id', 'left');
            $builder->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left');
            $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
            $builder->where('iu.no_unit IS NOT NULL');
            $builder->orderBy('iu.no_unit', 'ASC');
            $builder->limit(100); // Limit untuk performance
            
            $units = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $units
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting units dropdown: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting units data'
            ]);
        }
    }

    // Get Staff for Dropdown by Role
    public function getStaffDropdown()
    {
        $staffRole = $this->request->getPost('staff_role');
        
        if (empty($staffRole)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Staff role is required'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            $staff = $db->table('work_order_staff')
                ->select('id, staff_name, staff_role')
                ->where('staff_role', strtoupper($staffRole))
                ->where('is_active', 1)
                ->orderBy('staff_name', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $staff
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting staff dropdown: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error getting staff data'
            ]);
        }
    }
    
    // API endpoints untuk testing
    public function api($action = null, $id = null)
    {
        $response = ['success' => false, 'message' => '', 'data' => null];
        
        try {
            switch ($action) {
                case 'units':
                    $response['data'] = $this->getUnits();
                    $response['success'] = true;
                    break;
                    
                case 'priorities':
                    $priorityModel = new \App\Models\WorkOrderPriorityModel();
                    $response['data'] = $priorityModel->findAll();
                    $response['success'] = true;
                    break;
                    
                case 'categories':
                    $categoryModel = new \App\Models\WorkOrderCategoryModel();
                    $response['data'] = $categoryModel->findAll();
                    $response['success'] = true;
                    break;
                    
                case 'unit-area-info':
                    if ($id) {
                        $unitInfo = $this->getUnitAreaInfo($id);
                        if ($unitInfo) {
                            $response['data'] = [
                                'area_info' => $unitInfo,
                                'available_staff' => $this->getAreaStaff($unitInfo['area_id'] ?? null)
                            ];
                            $response['success'] = true;
                        } else {
                            $response['message'] = 'Unit not found or no area assigned';
                        }
                    } else {
                        $response['message'] = 'Unit ID required';
                    }
                    break;
                    
                case 'simulate-assignment':
                    $response = $this->simulateStaffAssignment();
                    break;
                    
                default:
                    $response['message'] = 'Invalid API action';
            }
            
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        return $this->response->setJSON($response);
    }
    
    // Helper method untuk mendapatkan staff di area
    private function getAreaStaff($areaId)
    {
        if (!$areaId) return [];
        
        $staffModel = new \App\Models\StaffModel();
        return $staffModel->getStaffByArea($areaId);
    }
    
    // Simulasi staff assignment
    private function simulateStaffAssignment()
    {
        $unitId = $this->request->getPost('unit_id');
        $orderType = $this->request->getPost('order_type');
        $priorityId = $this->request->getPost('priority_id');
        $categoryId = $this->request->getPost('category_id');
        $complaint = $this->request->getPost('complaint_description');
        
        if (!$unitId || !$orderType || !$priorityId || !$categoryId || !$complaint) {
            return [
                'success' => false,
                'message' => 'All fields are required for simulation'
            ];
        }
        
        // Get unit area info
        $unitInfo = $this->getUnitAreaInfo($unitId);
        if (!$unitInfo) {
            return [
                'success' => false,
                'message' => 'Unit not found or no area assigned'
            ];
        }
        
        // Simulate auto staff assignment
        $assignedStaff = $this->autoAssignStaff($unitId);
        
        return [
            'success' => true,
            'message' => 'Staff assignment simulation completed',
            'unit_info' => $unitInfo,
            'assigned_staff' => $assignedStaff
        ];
    }
}