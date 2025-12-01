<?php

namespace App\Controllers;

use App\Models\AreaModel;
use App\Models\EmployeeModel;
use App\Models\AreaEmployeeAssignmentModel;
use App\Models\CustomerModel;

class ServiceAreaManagementController extends BaseController
{
    protected $areaModel;
    protected $employeeModel;
    protected $assignmentModel;
    protected $customerModel;
    
    public function __construct()
    {
        $this->areaModel = new AreaModel();
        $this->employeeModel = new EmployeeModel();
        $this->assignmentModel = new AreaEmployeeAssignmentModel();
        $this->customerModel = new CustomerModel();
        
        // Load auth helper for division filtering
        helper('auth');
    }

    /**
     * Display service area management dashboard
     */
    public function index()
    {
        // Check permission for viewing area management
        if (!$this->hasPermission('service.area_management.view')) {
            return redirect()->to('/')->with('error', 'Access denied: You do not have permission to view area management');
        }
        
        // Data for dashboard stats
        $db = \Config\Database::connect();
        
        // Apply department filter for dashboard stats
        $allowedDepartments = get_user_division_departments();
        
        // Check if departemen_id column exists
        $fields = $db->getFieldData('areas');
        $hasDepartemenId = false;
        foreach ($fields as $field) {
            if ($field->name === 'departemen_id') {
                $hasDepartemenId = true;
                break;
            }
        }
        
        $areaBuilder = $db->table('areas');
        if ($hasDepartemenId && $allowedDepartments !== null && is_array($allowedDepartments)) {
            $areaBuilder->whereIn('departemen_id', $allowedDepartments);
        }
        $totalAreas = $areaBuilder->where('is_active', 1)->countAllResults();
        
        $dashboardData = [
            'totalAreas' => $totalAreas,
            'totalEmployees' => $db->table('employees')->where('is_active', 1)->countAllResults(),
            'totalAssignments' => $db->table('area_employee_assignments')->where('is_active', 1)->countAllResults(),
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service' => 'Service',
                '/service/area_employee_management' => 'Area & Employee Management'
            ]
        ];
        
        // Get active areas with department filter
        $areas = [];
        try {
            $areaBuilder = $db->table('areas');
            $areaBuilder->select('areas.id, areas.area_code, areas.area_name');
            $areaBuilder->where('areas.is_active', 1);
            
            // Apply department filter if column exists
            if ($hasDepartemenId && $allowedDepartments !== null && is_array($allowedDepartments)) {
                $areaBuilder->whereIn('areas.departemen_id', $allowedDepartments);
            }
            
            $areaBuilder->orderBy('areas.area_name', 'ASC');
            $areas = $areaBuilder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting areas: ' . $e->getMessage());
        }
        
        // Get employees data by role for stats
        $roleStats = [];
        try {
            $employeeBuilder = $db->table('employees');
            $employeeBuilder->select('staff_role, COUNT(*) as total');
            $employeeBuilder->where('is_active', 1);
            $employeeBuilder->groupBy('staff_role');
            $employeeStats = $employeeBuilder->get()->getResultArray();
            
            foreach ($employeeStats as $stat) {
                $roleStats[$stat['staff_role']] = $stat['total'];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error getting employee stats: ' . $e->getMessage());
        }
        
        // Prepare data for charts
        $employeesByRole = [];
        foreach ($roleStats as $role => $count) {
            $employeesByRole[] = [
                'role' => $role,
                'employee_count' => $count
            ];
        }
        
        // Get assignments by area for chart with department filter
        $assignmentsByArea = [];
        try {
            $sql = "
                SELECT a.area_name, a.area_code, COUNT(aea.id) as assignment_count
                FROM areas a
                LEFT JOIN area_employee_assignments aea ON a.id = aea.area_id AND aea.is_active = 1
                WHERE a.is_active = 1
            ";
            
            // Add department filter if column exists
            if ($hasDepartemenId && $allowedDepartments !== null && is_array($allowedDepartments)) {
                $deptIds = implode(',', array_map('intval', $allowedDepartments));
                $sql .= " AND a.departemen_id IN ($deptIds)";
            }
            
            $sql .= "
                GROUP BY a.id, a.area_name, a.area_code
                ORDER BY assignment_count DESC
                LIMIT 10
            ";
            
            $assignmentsQuery = $db->query($sql);
            
            if ($assignmentsQuery) {
                $assignmentsByArea = $assignmentsQuery->getResultArray();
            }
        } catch (\Exception $e) {
            log_message('error', 'Error getting assignments by area: ' . $e->getMessage());
        }
        
        $data = [
            'title' => 'Area Management',
            'dashboardData' => $dashboardData,
            'totalAreas' => $dashboardData['totalAreas'],
            'totalEmployees' => $dashboardData['totalEmployees'],
            'totalAssignments' => $dashboardData['totalAssignments'],
            'areas' => $areas,
            'roleStats' => $roleStats,
            'employeesByRole' => $employeesByRole,
            'assignmentsByArea' => $assignmentsByArea,
            'loadCharts' => true, // Enable Chart.js loading
            'loadDataTables' => true, // Enable DataTables loading
        ];
        
        return view('service/area_employee_management', $data);
    }

    /**
     * Get areas for DataTable
     */
    public function getAreas()
    {
        // Handle both GET and POST requests (DataTables can use either)
        $request = $this->request->getMethod() === 'post' 
            ? $this->request->getPost() 
            : $this->request->getGet();
        
        // Debug: Log the request
        log_message('debug', 'DataTable request: ' . json_encode($request));
        log_message('debug', 'Draw parameter: ' . ($request['draw'] ?? 'not set'));
        
        // Apply division-based department filter for areas using global helper
        $allowedDepartments = get_user_division_departments();
        
        // Check if departemen_id column exists in areas table
        $db = \Config\Database::connect();
        $fields = $db->getFieldData('areas');
        $hasDepartemenId = false;
        foreach ($fields as $field) {
            if ($field->name === 'departemen_id') {
                $hasDepartemenId = true;
                break;
            }
        }
        
        // Total records - apply department filter if needed
        $totalBuilder = $this->areaModel->builder();
        if ($hasDepartemenId && $allowedDepartments !== null && is_array($allowedDepartments)) {
            $totalBuilder->whereIn('departemen_id', $allowedDepartments);
        }
        $totalRecords = $totalBuilder->countAllResults();
        
        // Build query dengan join yang benar setelah migration area_id ke customer_locations
        $builder = $this->areaModel->builder();
        $builder->select('areas.*, COUNT(DISTINCT c.id) as customers_count');
        $builder->join('customer_locations cl', 'cl.area_id = areas.id', 'left');
        $builder->join('customers c', 'c.id = cl.customer_id', 'left');
        
        // Apply department filter directly on areas.departemen_id if column exists
        if ($hasDepartemenId && $allowedDepartments !== null && is_array($allowedDepartments)) {
            // Simple filter: areas.departemen_id IN allowed departments
            $builder->whereIn('areas.departemen_id', $allowedDepartments);
        } elseif ($allowedDepartments !== null && is_array($allowedDepartments)) {
            // Fallback: if departemen_id column doesn't exist, use complex join
            // Get area IDs that have units in allowed departments
            $areaQuery = $db->table('areas a')
                ->select('DISTINCT a.id')
                ->join('customer_locations cl', 'cl.area_id = a.id', 'inner')
                ->join('kontrak k', 'k.customer_location_id = cl.id', 'inner')
                ->join('inventory_unit iu', 'iu.kontrak_id = k.id', 'inner')
                ->whereIn('iu.departemen_id', $allowedDepartments)
                ->get();
            $allowedAreaIds = array_column($areaQuery->getResultArray(), 'id');
            
            // If no areas found, return empty result
            if (empty($allowedAreaIds)) {
                return $this->response->setJSON([
                    'draw' => isset($request['draw']) ? intval($request['draw']) : 1,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []
                ]);
            }
            
            $builder->whereIn('areas.id', $allowedAreaIds);
        }
        
        $builder->groupBy('areas.id');
        
        // Apply search filter
        if (isset($request['search']['value']) && !empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart()
                ->like('areas.area_name', $searchValue)
                ->orLike('areas.area_code', $searchValue)
                ->orLike('areas.area_description', $searchValue)
            ->groupEnd();
        }
        
        // Count filtered records
        $recordsFiltered = $builder->countAllResults(false);
        
        // Apply order
        if (isset($request['order']) && !empty($request['order'])) {
            $order = $request['order'][0];
            $column = $request['columns'][$order['column']]['data'];
            $dir = $order['dir'];
            
            // Pastikan kolom yang valid untuk pengurutan
            if (in_array($column, ['area_code', 'area_name', 'description', 'customers_count', 'employees_count', 'created_at'])) {
                // Jika column adalah customers_count atau employees_count, gunakan cast untuk pengurutan numerik
                if (in_array($column, ['customers_count', 'employees_count'])) {
                    $builder->orderBy("CAST($column AS UNSIGNED)", $dir);
                } else {
                    // Untuk kolom lain, tambahkan prefiks areas. jika diperlukan
                    $orderColumn = in_array($column, ['area_code', 'area_name', 'created_at']) ? "areas.$column" : $column;
                    $builder->orderBy($orderColumn, $dir);
                }
            } else {
                $builder->orderBy('areas.area_name', 'ASC');
            }
        } else {
            $builder->orderBy('areas.area_name', 'ASC');
        }
        
        // Apply limit and offset
        if (isset($request['length']) && $request['length'] != -1) {
            $builder->limit($request['length'], isset($request['start']) ? $request['start'] : 0);
        }
        
        // Get results
        $query = $builder->get();
        $results = $query->getResultArray();
        
        // Format data for DataTable
        $data = [];
        foreach ($results as $row) {
            $foreman = $this->getAreaForeman($row['id']);
            $mechanic = $this->getAreaMechanics($row['id']);
            $helper = $this->getAreaHelpers($row['id']);
            
            // Count employees (Foreman + Mechanics + Helpers)
            $employeesCount = ($foreman ? 1 : 0) + count($mechanic) + count($helper);
            
            // Create assignment summary for display
            $assignmentSummary = [
                'foreman' => $foreman ? $foreman['staff_name'] : null,
                'mechanics' => $mechanic ? array_column($mechanic, 'staff_name') : [],
                'helpers' => $helper ? array_column($helper, 'staff_name') : []
            ];
            
            $formattedRow = [
                'id' => $row['id'],
                'area_code' => $row['area_code'],
                'area_name' => $row['area_name'],
                'description' => $row['area_description'] ?? '',
                'customers_count' => isset($row['customers_count']) ? (int)$row['customers_count'] : 0,
                'employees_count' => $employeesCount,
                'assignment_summary' => $assignmentSummary,
                'is_active' => $row['is_active'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'actions' => '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-info view-area" data-id="'.$row['id'].'"><i class="fas fa-eye"></i></button>
                    <button type="button" class="btn btn-sm btn-primary edit-area" data-id="'.$row['id'].'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="btn btn-sm btn-danger delete-area" data-id="'.$row['id'].'"><i class="fas fa-trash"></i></button>
                </div>'
            ];
            
            $data[] = $formattedRow;
        }
        
        // Return data in DataTable format
        return $this->response->setJSON([
            'draw' => isset($request['draw']) ? intval($request['draw']) : 1,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    /**
     * Get area by ID
     */
    public function getArea($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID area tidak valid'
            ]);
        }
        
        $builder = $this->areaModel->builder();
        $builder->select('areas.*, COUNT(DISTINCT c.id) as customers_count');
        $builder->join('customer_locations cl', 'cl.area_id = areas.id', 'left');
        $builder->join('customers c', 'c.id = cl.customer_id', 'left');
        $builder->where('areas.id', $id);
        $builder->groupBy('areas.id');
        
        $query = $builder->get();
        $area = $query->getRowArray();
        
        if (!$area) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Area tidak ditemukan'
            ]);
        }
        
        // Get assigned employees
        $area['foreman'] = $this->getAreaForeman($id);
        $area['mechanics'] = $this->getAreaMechanics($id);
        $area['helpers'] = $this->getAreaHelpers($id);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $area
        ]);
    }

    /**
     * Save new area
     */
    public function saveArea()
    {
        $input = $this->request->getPost();
        
        // Validate input
        $validationRules = [
            'area_code' => 'required|max_length[20]|is_unique[areas.area_code]',
            'area_name' => 'required|max_length[100]',
            'area_description' => 'permit_empty'
        ];
        
        if (!$this->validateData($input, $validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'area_code' => $input['area_code'],
            'area_name' => $input['area_name'],
            'area_description' => !empty($input['area_description']) ? $input['area_description'] : null,
            'is_active' => 1,
            'created_by' => session()->get('user_id')
        ];
        
        try {
            // Insert data
            $areaId = $this->areaModel->insert($data);
            
            if ($areaId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Area berhasil disimpan',
                    'id' => $areaId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan area'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update area
     */
    public function updateArea($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID area tidak valid'
            ]);
        }
        
        $area = $this->areaModel->find($id);
        if (!$area) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Area tidak ditemukan'
            ]);
        }
        
        $input = $this->request->getPost();
        
        // Validate input
        $validationRules = [
            'area_code' => "required|max_length[20]|is_unique[areas.area_code,id,{$id}]",
            'area_name' => 'required|max_length[100]',
            'area_description' => 'permit_empty'
        ];
        
        if (!$this->validateData($input, $validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'area_code' => $input['area_code'],
            'area_name' => $input['area_name'],
            'area_description' => !empty($input['area_description']) ? $input['area_description'] : null,
            'departemen_id' => !empty($input['departemen_id']) ? (int)$input['departemen_id'] : null,
            'updated_at' => date('Y-m-d H:i:s') // Force update
        ];
        
        try {
            // Log input data for debugging
            log_message('debug', 'Update area input: ' . json_encode($input));
            log_message('debug', 'Update area data: ' . json_encode($data));
            log_message('debug', 'Update area ID: ' . $id);
            
            // Check if area exists first
            $existingArea = $this->areaModel->find($id);
            log_message('debug', 'Existing area: ' . json_encode($existingArea));
            
            if (!$existingArea) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Area not found with ID: ' . $id
                ]);
            }
            
            // Update data using manual query to get better control
            $db = \Config\Database::connect();
            $builder = $db->table('areas');
            $builder->where('id', $id);
            $builder->where('deleted_at IS NULL'); // Ensure not soft deleted
            $builder->update($data);
            $updated = $db->affectedRows() > 0;
            
            log_message('debug', 'Manual update result: ' . ($updated ? 'true' : 'false'));
            log_message('debug', 'Affected rows: ' . $db->affectedRows());
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Area berhasil diupdate'
                ]);
            } else {
                // Log more details about the failure
                log_message('error', 'Area update failed for ID: ' . $id);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate area - No rows affected'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Area update exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete area
     */
    public function deleteArea($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID area tidak valid'
            ]);
        }
        
        $area = $this->areaModel->find($id);
        if (!$area) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Area tidak ditemukan'
            ]);
        }
        
        try {
            // Soft delete
            $updated = $this->areaModel->update($id, ['is_active' => 0]);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Area berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus area'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get employees for DataTable
     */
    public function getEmployees()
    {
        $request = $this->request->getGet();
        
        // Total records without filtering
        $totalRecords = $this->employeeModel->where('is_active', 1)->countAllResults();
        
        // Build query
        $builder = $this->employeeModel->builder();
        $builder->select('employees.*, d.nama_departemen');
        $builder->join('departemen d', 'employees.departemen_id = d.id_departemen', 'left');
        $builder->where('employees.is_active', 1);
        
        // Apply division-based department filter for employees using global helper
        $allowedDepartments = get_user_division_departments();
        
        if ($allowedDepartments !== null && is_array($allowedDepartments)) {
            $builder->whereIn('employees.departemen_id', $allowedDepartments);
        }
        
        // Apply search filter
        if (isset($request['search']['value']) && !empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart()
                ->like('employees.staff_code', $searchValue)
                ->orLike('employees.staff_name', $searchValue)
                ->orLike('employees.staff_role', $searchValue)
                ->orLike('employees.phone', $searchValue)
                ->orLike('employees.email', $searchValue)
                ->orLike('d.nama_departemen', $searchValue)
            ->groupEnd();
        }
        
        // Count filtered records
        $recordsFiltered = $builder->countAllResults(false);
        
        // Apply order
        if (isset($request['order']) && !empty($request['order'])) {
            $order = $request['order'][0];
            $column = $request['columns'][$order['column']]['data'];
            $dir = $order['dir'];
            
            // Handle custom ordering
            switch ($column) {
                case 'departemen':
                    $builder->orderBy('d.nama_departemen', $dir);
                    break;
                default:
                    $builder->orderBy($column, $dir);
                    break;
            }
        } else {
            $builder->orderBy('employees.staff_name', 'ASC');
        }
        
        // Apply limit and offset
        if (isset($request['length']) && $request['length'] != -1) {
            $builder->limit($request['length'], isset($request['start']) ? $request['start'] : 0);
        }
        
        // Get results
        $query = $builder->get();
        $results = $query->getResultArray();
        
        // Format data for DataTable
        $data = [];
        foreach ($results as $row) {
            $formattedRow = [
                'id' => $row['id'],
                'staff_code' => $row['staff_code'],
                'staff_name' => $row['staff_name'],
                'staff_role' => $row['staff_role'],
                'departemen' => $row['nama_departemen'] ?? '-',
                'phone' => $row['phone'],
                'email' => $row['email'],
                'address' => $row['address'],
                'hire_date' => $row['hire_date'],
                'is_active' => $row['is_active'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ];
            
            $data[] = $formattedRow;
        }
        
        // Return data in DataTable format
        return $this->response->setJSON([
            'draw' => isset($request['draw']) ? intval($request['draw']) : 1,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    /**
     * Get employee detail with assignments
     */
    public function getEmployeeDetail($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Employee ID is required'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Get employee data
            $builder = $db->table('employees e');
            $builder->select('e.*, d.nama_departemen as departemen');
            $builder->join('departemen d', 'e.departemen_id = d.id_departemen', 'left');
            $builder->where('e.id', $id);
            $builder->where('e.is_active', 1);
            
            $employee = $builder->get()->getRowArray();
            
            if (!$employee) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Employee not found'
                ]);
            }

            // Get employee assignments
            $assignmentBuilder = $db->table('area_employee_assignments aea');
            $assignmentBuilder->select('aea.*, a.area_name, a.area_code');
            $assignmentBuilder->join('areas a', 'aea.area_id = a.id');
            $assignmentBuilder->where('aea.employee_id', $id);
            $assignmentBuilder->orderBy('aea.assignment_type', 'ASC');
            
            $assignments = $assignmentBuilder->get()->getResultArray();
            
            $employee['assignments'] = $assignments;

            return $this->response->setJSON([
                'success' => true,
                'data' => $employee
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting employee detail: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving employee details'
            ]);
        }
    }

    /**
     * Get employee by ID
     */
    public function getEmployee($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID karyawan tidak valid'
            ]);
        }
        
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $employee
        ]);
    }

    /**
     * Save new employee
     */
    public function saveEmployee()
    {
        $input = $this->request->getPost();
        
        // Validate input
        $validationRules = [
            'staff_code' => 'required|max_length[20]|is_unique[employees.staff_code]',
            'staff_name' => 'required|max_length[100]',
            'staff_role' => 'required|in_list[ADMIN,FOREMAN,MECHANIC,HELPER,SUPERVISOR]',
            'departemen_id' => 'permit_empty|integer',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[100]',
            'address' => 'permit_empty',
            'hire_date' => 'permit_empty|valid_date'
        ];
        
        if (!$this->validateData($input, $validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'staff_code' => $input['staff_code'],
            'staff_name' => $input['staff_name'],
            'staff_role' => $input['staff_role'],
            'departemen_id' => !empty($input['departemen_id']) ? $input['departemen_id'] : null,
            'phone' => $input['phone'] ?? null,
            'email' => $input['email'] ?? null,
            'address' => $input['address'] ?? null,
            'hire_date' => !empty($input['hire_date']) ? $input['hire_date'] : null,
            'is_active' => 1,
            'created_by' => session()->get('user_id')
        ];
        
        try {
            // Insert data
            $employeeId = $this->employeeModel->insert($data);
            
            if ($employeeId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Karyawan berhasil disimpan',
                    'id' => $employeeId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan karyawan'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update employee
     */
    public function updateEmployee($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID karyawan tidak valid'
            ]);
        }
        
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }
        
        $input = $this->request->getPost();
        
        // Validate input
        $validationRules = [
            'staff_code' => "required|max_length[20]|is_unique[employees.staff_code,id,{$id}]",
            'staff_name' => 'required|max_length[100]',
            'staff_role' => 'required|in_list[ADMIN,FOREMAN,MECHANIC,HELPER,SUPERVISOR]',
            'departemen_id' => 'permit_empty|integer',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[100]',
            'address' => 'permit_empty',
            'hire_date' => 'permit_empty|valid_date'
        ];
        
        if (!$this->validateData($input, $validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'staff_code' => $input['staff_code'],
            'staff_name' => $input['staff_name'],
            'staff_role' => $input['staff_role'],
            'departemen_id' => !empty($input['departemen_id']) ? $input['departemen_id'] : null,
            'phone' => $input['phone'] ?? null,
            'email' => $input['email'] ?? null,
            'address' => $input['address'] ?? null,
            'hire_date' => !empty($input['hire_date']) ? $input['hire_date'] : null,
            'updated_at' => date('Y-m-d H:i:s') // Force update
        ];
        
        try {
            // Log input data for debugging
            log_message('debug', 'Update employee input: ' . json_encode($input));
            log_message('debug', 'Update employee data: ' . json_encode($data));
            log_message('debug', 'Update employee ID: ' . $id);
            
            // Check if employee exists first
            $existingEmployee = $this->employeeModel->find($id);
            log_message('debug', 'Existing employee: ' . json_encode($existingEmployee));
            
            if (!$existingEmployee) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Employee not found with ID: ' . $id
                ]);
            }
            
            // Update data using manual query to get better control
            $db = \Config\Database::connect();
            $builder = $db->table('employees');
            $builder->where('id', $id);
            $builder->where('deleted_at IS NULL'); // Ensure not soft deleted
            $builder->update($data);
            $updated = $db->affectedRows() > 0;
            
            log_message('debug', 'Manual update result: ' . ($updated ? 'true' : 'false'));
            log_message('debug', 'Affected rows: ' . $db->affectedRows());
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Karyawan berhasil diupdate'
                ]);
            } else {
                // Log more details about the failure
                log_message('error', 'Employee update failed for ID: ' . $id);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate karyawan - No rows affected'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Employee update exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete employee
     */
    public function deleteEmployee($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID karyawan tidak valid'
            ]);
        }
        
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }
        
        try {
            // Soft delete
            $updated = $this->employeeModel->update($id, ['is_active' => 0]);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Karyawan berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus karyawan'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get area assignments for DataTable
     */
    public function getAreaAssignments($areaId)
    {
        $request = $this->request->getGet();
        
        // Total records without filtering
        $totalRecords = $this->assignmentModel->where('area_id', $areaId)->countAllResults();
        
        // Build query
        $builder = $this->assignmentModel->builder();
        $builder->select('area_employee_assignments.*, a.area_name, a.area_code, e.staff_name, e.staff_code, e.staff_role');
        $builder->join('areas a', 'area_employee_assignments.area_id = a.id', 'inner');
        $builder->join('employees e', 'area_employee_assignments.employee_id = e.id', 'inner');
        $builder->where('area_employee_assignments.area_id', $areaId);
        
        // Apply search filter
        if (isset($request['search']['value']) && !empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart()
                ->like('a.area_name', $searchValue)
                ->orLike('a.area_code', $searchValue)
                ->orLike('e.staff_name', $searchValue)
                ->orLike('e.staff_code', $searchValue)
                ->orLike('e.staff_role', $searchValue)
                ->orLike('area_employee_assignments.assignment_type', $searchValue)
            ->groupEnd();
        }
        
        // Count filtered records
        $recordsFiltered = $builder->countAllResults(false);
        
        // Apply order
        if (isset($request['order']) && !empty($request['order'])) {
            $order = $request['order'][0];
            $column = $request['columns'][$order['column']]['data'];
            $dir = $order['dir'];
            
            // Handle custom ordering
            switch ($column) {
                case 'area_name':
                    $builder->orderBy('a.area_name', $dir);
                    break;
                case 'staff_name':
                    $builder->orderBy('e.staff_name', $dir);
                    break;
                case 'staff_role':
                    $builder->orderBy('e.staff_role', $dir);
                    break;
                default:
                    $builder->orderBy($column, $dir);
                    break;
            }
        } else {
            $builder->orderBy('e.staff_role', 'ASC');
            $builder->orderBy('e.staff_name', 'ASC');
        }
        
        // Apply limit and offset
        if (isset($request['length']) && $request['length'] != -1) {
            $builder->limit($request['length'], isset($request['start']) ? $request['start'] : 0);
        }
        
        // Get results
        $query = $builder->get();
        $results = $query->getResultArray();
        
        // Format data for DataTable
        $data = [];
        foreach ($results as $row) {
            $formattedRow = [
                'id' => $row['id'],
                'area_id' => $row['area_id'],
                'area_name' => $row['area_name'],
                'area_code' => $row['area_code'],
                'employee_id' => $row['employee_id'],
                'staff_name' => $row['staff_name'],
                'staff_code' => $row['staff_code'],
                'staff_role' => $row['staff_role'],
                'assignment_type' => $row['assignment_type'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'notes' => $row['notes'],
                'is_active' => $row['is_active'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ];
            
            $data[] = $formattedRow;
        }
        
        // Return data in simple format for frontend
        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    /**
     * Get assignment by ID
     */
    public function getAssignment($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID assignment tidak valid'
            ]);
        }
        
        $builder = $this->assignmentModel->builder();
        $builder->select('area_employee_assignments.*, a.area_name, a.area_code, e.staff_name, e.staff_code, e.staff_role');
        $builder->join('areas a', 'area_employee_assignments.area_id = a.id', 'inner');
        $builder->join('employees e', 'area_employee_assignments.employee_id = e.id', 'inner');
        $builder->where('area_employee_assignments.id', $id);
        
        $query = $builder->get();
        $assignment = $query->getRowArray();
        
        if (!$assignment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assignment tidak ditemukan'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $assignment
        ]);
    }

    /**
     * Save new assignment
     */
    public function saveAssignment()
    {
        $input = $this->request->getPost();
        
        // Validate input
        $validationRules = [
            'area_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'assignment_type' => 'required|in_list[PRIMARY,BACKUP,TEMPORARY]',
            'start_date' => 'required|valid_date',
            'end_date' => 'permit_empty|valid_date',
            'notes' => 'permit_empty'
        ];
        
        if (!$this->validateData($input, $validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        // Check if employee exists
        $employee = $this->employeeModel->find($input['employee_id']);
        if (!$employee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }
        
        // Check if area exists
        $area = $this->areaModel->find($input['area_id']);
        if (!$area) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Area tidak ditemukan'
            ]);
        }
        
        // Check for existing assignment with PRIMARY type
        if ($input['assignment_type'] === 'PRIMARY' && $employee['staff_role'] === 'FOREMAN') {
            $existingAssignment = $this->assignmentModel
                ->where('area_id', $input['area_id'])
                ->where('assignment_type', 'PRIMARY')
                ->where('is_active', 1)
                ->join('employees', 'area_employee_assignments.employee_id = employees.id')
                ->where('employees.staff_role', 'FOREMAN')
                ->first();
            
            if ($existingAssignment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Area ini sudah memiliki Foreman utama (PRIMARY)'
                ]);
            }
        }
        
        $data = [
            'area_id' => $input['area_id'],
            'employee_id' => $input['employee_id'],
            'assignment_type' => $input['assignment_type'],
            'start_date' => $input['start_date'],
            'end_date' => !empty($input['end_date']) ? $input['end_date'] : null,
            'notes' => $input['notes'] ?? null,
            'is_active' => 1,
            'created_by' => session()->get('user_id')
        ];
        
        try {
            // Insert data
            $assignmentId = $this->assignmentModel->insert($data);
            
            if ($assignmentId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Assignment berhasil disimpan',
                    'id' => $assignmentId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan assignment'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }



    /**
     * Get employees by role statistics
     */
    public function getEmployeeStatsByRole()
    {
        $builder = $this->employeeModel->builder();
        $builder->select('staff_role, COUNT(*) as total');
        $builder->where('is_active', 1);
        $builder->groupBy('staff_role');
        
        $query = $builder->get();
        $results = $query->getResultArray();
        
        $stats = [];
        foreach ($results as $row) {
            $stats[$row['staff_role']] = (int)$row['total'];
        }
        
        // Ensure all roles have a value
        $roles = ['ADMIN', 'SUPERVISOR', 'FOREMAN', 'MECHANIC', 'HELPER'];
        foreach ($roles as $role) {
            if (!isset($stats[$role])) {
                $stats[$role] = 0;
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get available employees for assignment
     */
    public function getAvailableEmployees($areaId = null, $role = null)
    {
        $builder = $this->employeeModel->builder();
        $builder->select('employees.*');
        $builder->where('employees.is_active', 1);
        
        if ($role) {
            $builder->where('employees.staff_role', $role);
        }
        
        if ($areaId) {
            // Exclude employees already assigned as PRIMARY to this area
            $subquery = $this->assignmentModel->builder()
                ->select('employee_id')
                ->where('area_id', $areaId)
                ->where('assignment_type', 'PRIMARY')
                ->where('is_active', 1);
            
            $builder->whereNotIn('employees.id', $subquery, false);
        }
        
        $builder->orderBy('employees.staff_name', 'ASC');
        
        $query = $builder->get();
        $employees = $query->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $employees
        ]);
    }

    /**
     * Get customers for dropdown
     */
    public function getCustomers()
    {
        $customers = $this->customerModel->where('is_active', 1)->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $customers
        ]);
    }

    /**
     * Get locations by customer ID
     */
    public function getLocationsByCustomer($customerId)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('customer_locations');
        $builder->where('customer_id', $customerId);
        $builder->where('is_active', 1);
        
        $locations = $builder->get()->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Helper function to get area foreman
     */
    private function getAreaForeman($areaId)
    {
        $builder = $this->assignmentModel->builder();
        $builder->select('e.id, e.staff_code, e.staff_name, e.staff_role, e.phone, area_employee_assignments.assignment_type');
        $builder->join('employees e', 'area_employee_assignments.employee_id = e.id', 'inner');
        $builder->where('area_employee_assignments.area_id', $areaId);
        $builder->where('area_employee_assignments.is_active', 1);
        $builder->where('e.staff_role', 'FOREMAN');
        $builder->orderBy('area_employee_assignments.assignment_type', 'ASC');
        $builder->limit(1);
        
        $query = $builder->get();
        $foreman = $query->getRowArray();
        
        return $foreman;
    }

    /**
     * Helper function to get area mechanics
     */
    private function getAreaMechanics($areaId)
    {
        $builder = $this->assignmentModel->builder();
        $builder->select('e.id, e.staff_code, e.staff_name, e.staff_role, e.phone, area_employee_assignments.assignment_type');
        $builder->join('employees e', 'area_employee_assignments.employee_id = e.id', 'inner');
        $builder->where('area_employee_assignments.area_id', $areaId);
        $builder->where('area_employee_assignments.is_active', 1);
        $builder->where('e.staff_role', 'MECHANIC');
        $builder->orderBy('area_employee_assignments.assignment_type', 'ASC');
        
        $query = $builder->get();
        $mechanics = $query->getResultArray();
        
        return $mechanics;
    }

    /**
     * Helper function to get area helpers
     */
    private function getAreaHelpers($areaId)
    {
        $builder = $this->assignmentModel->builder();
        $builder->select('e.id, e.staff_code, e.staff_name, e.staff_role, e.phone, area_employee_assignments.assignment_type');
        $builder->join('employees e', 'area_employee_assignments.employee_id = e.id', 'inner');
        $builder->where('area_employee_assignments.area_id', $areaId);
        $builder->where('area_employee_assignments.is_active', 1);
        $builder->where('e.staff_role', 'HELPER');
        $builder->orderBy('area_employee_assignments.assignment_type', 'ASC');
        
        $query = $builder->get();
        $helpers = $query->getResultArray();
        
        return $helpers;
    }

    /**
     * Store new assignment
     */
    public function storeAssignment()
    {
        $input = $this->request->getPost();
        
        // Validate input
        $validationRules = [
            'area_id' => 'required|integer',
            'staff_id' => 'required|integer',
            'assignment_type' => 'required|in_list[PRIMARY,BACKUP,TEMPORARY]',
            'start_date' => 'required|valid_date',
            'end_date' => 'permit_empty|valid_date',
            'notes' => 'permit_empty'
        ];
        
        if (!$this->validateData($input, $validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'area_id' => $input['area_id'],
            'employee_id' => $input['staff_id'], // Map staff_id to employee_id
            'assignment_type' => $input['assignment_type'],
            'start_date' => $input['start_date'],
            'end_date' => !empty($input['end_date']) ? $input['end_date'] : null,
            'notes' => $input['notes'] ?? null,
            'is_active' => 1,
            'created_by' => session()->get('user_id')
        ];
        
        try {
            // Insert assignment
            $assignmentId = $this->assignmentModel->insert($data);
            
            if ($assignmentId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Assignment created successfully',
                    'id' => $assignmentId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create assignment'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error creating assignment: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update assignment
     */
    public function updateAssignment($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid assignment ID'
            ]);
        }
        
        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assignment not found'
            ]);
        }
        
        $input = $this->request->getPost();
        
        // Validate input
        $validationRules = [
            'assignment_type' => 'required|in_list[PRIMARY,BACKUP,TEMPORARY]',
            'start_date' => 'required|valid_date',
            'end_date' => 'permit_empty|valid_date',
            'notes' => 'permit_empty',
            'is_active' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validateData($input, $validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'assignment_type' => $input['assignment_type'],
            'start_date' => $input['start_date'],
            'end_date' => !empty($input['end_date']) ? $input['end_date'] : null,
            'notes' => $input['notes'] ?? null,
            'is_active' => isset($input['is_active']) ? (int)$input['is_active'] : 1,
            'updated_by' => session()->get('user_id')
        ];
        
        try {
            $updated = $this->assignmentModel->update($id, $data);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Assignment updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update assignment'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating assignment: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete assignment
     */
    public function deleteAssignment($id = null)
    {
        log_message('info', 'Delete assignment request received for ID: ' . $id);
        
        if (!$id) {
            log_message('error', 'Delete assignment: Invalid ID provided');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid assignment ID'
            ]);
        }
        
        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            log_message('error', 'Delete assignment: Assignment not found for ID: ' . $id);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assignment not found'
            ]);
        }
        
        log_message('info', 'Assignment found, proceeding with deletion: ' . json_encode($assignment));
        
        try {
            // Hard delete - actually remove the record
            $deleted = $this->assignmentModel->delete($id);
            
            if ($deleted) {
                log_message('info', 'Assignment deleted successfully by user: ' . session()->get('user_id'));
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Assignment removed successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to remove assignment'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting assignment: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show assignment details
     */
    public function showAssignment($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid assignment ID'
            ]);
        }
        
        try {
            log_message('debug', 'Showing assignment ID: ' . $id);
            
            $db = \Config\Database::connect();
            $builder = $db->table('area_employee_assignments aea');
            $builder->select('aea.*, a.area_code, a.area_name, e.staff_code, e.staff_name, e.staff_role');
            $builder->join('areas a', 'a.id = aea.area_id');
            $builder->join('employees e', 'e.id = aea.employee_id');
            $builder->where('aea.id', $id);
            
            $assignment = $builder->get()->getRowArray();
            
            log_message('debug', 'Assignment query result: ' . json_encode($assignment));
            
            if (!$assignment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Assignment not found'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $assignment
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting assignment: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving assignment'
            ]);
        }
    }

    /**
     * Toggle assignment status (active/inactive)
     */
    public function toggleAssignmentStatus($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid assignment ID'
            ]);
        }

        $isActive = $this->request->getPost('is_active');
        if ($isActive === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status not provided'
            ]);
        }

        try {
            $assignment = $this->assignmentModel->find($id);
            if (!$assignment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Assignment not found'
                ]);
            }

            $updated = $this->assignmentModel->update($id, [
                'is_active' => $isActive
            ]);

            if ($updated) {
                $statusText = $isActive == 1 ? 'activated' : 'deactivated';
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Assignment {$statusText} successfully"
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update assignment status'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error toggling assignment status: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating assignment status'
            ]);
        }
    }
}