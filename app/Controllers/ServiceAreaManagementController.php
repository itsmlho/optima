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
    protected $db;
    
    public function __construct()
    {
        $this->areaModel = new AreaModel();
        $this->employeeModel = new EmployeeModel();
        $this->assignmentModel = new AreaEmployeeAssignmentModel();
        $this->customerModel = new CustomerModel();
        $this->db = \Config\Database::connect();
        
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
            return redirect()->to('/')->with('error', 'Akses ditolak: Anda tidak memiliki izin');
        }
        
        // Data for dashboard stats
        $db = \Config\Database::connect();
        
        // NEW: Apply area-department scope filter
        $scope = get_user_area_department_scope();
        
        // Count total areas with scope filter
        $areaBuilder = $db->table('areas');
        if ($scope !== null && !empty($scope['areas'])) {
            $areaBuilder->whereIn('id', $scope['areas']);
        }
        $totalAreas = $areaBuilder->where('is_active', 1)->countAllResults();
        
        // Count employees with scope filter
        $employeeBuilder = $db->table('employees');
        if ($scope !== null && !empty($scope['departments'])) {
            $employeeBuilder->whereIn('departemen_id', $scope['departments']);
        }
        $totalEmployees = $employeeBuilder->where('is_active', 1)->countAllResults();
        
        $dashboardData = [
            'totalAreas' => $totalAreas,
            'totalEmployees' => $totalEmployees,
            'totalAssignments' => $db->table('area_employee_assignments')->where('is_active', 1)->countAllResults(),
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/service/area_employee_management' => 'Area & Employee Management'
            ]
        ];
        
        // Get active areas with scope filter
        $areas = [];
        try {
            $areaBuilder = $db->table('areas');
            $areaBuilder->select('areas.id, areas.area_code, areas.area_name, areas.area_type, areas.departemen_id');
            $areaBuilder->where('areas.is_active', 1);

            // Apply area scope filter
            if ($scope !== null && !empty($scope['areas'])) {
                $areaBuilder->whereIn('areas.id', $scope['areas']);
            }

            // Apply department scope to areas (e.g. admin service electric only sees electric areas)
            if ($scope !== null && !empty($scope['departments'])) {
                $areaBuilder->whereIn('areas.departemen_id', $scope['departments']);
            }

            $areaBuilder->orderBy('areas.area_type', 'ASC');
            $areaBuilder->orderBy('areas.area_name', 'ASC');
            $areas = $areaBuilder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
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
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
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
            
            // Department filter removed - not applicable for areas table
            
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
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
        
        // Unit Mapping stats (merged from UnitAreaMappingController)
        $unitStats = [
            'units_with_area'               => $db->table('inventory_unit')->where('area_id IS NOT NULL')->countAllResults(),
            'units_without_area'            => $db->table('inventory_unit')->where('area_id IS NULL')->where('status_unit_id !=', 13)->countAllResults(),
            'active_contract_units'         => $db->query("
                SELECT COUNT(DISTINCT ku.unit_id) as cnt
                FROM kontrak_unit ku
                JOIN kontrak k ON k.id = ku.kontrak_id
                WHERE k.status = 'ACTIVE' AND ku.status = 'ACTIVE'
            ")->getRowArray()['cnt'] ?? 0,
            'contract_units_without_area'   => $db->query("
                SELECT COUNT(DISTINCT ku.unit_id) as cnt
                FROM kontrak_unit ku
                JOIN kontrak k ON k.id = ku.kontrak_id
                JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
                WHERE k.status = 'ACTIVE' AND ku.status = 'ACTIVE'
                  AND iu.area_id IS NULL
            ")->getRowArray()['cnt'] ?? 0,
        ];

        $data = [
            'title' => 'Area Management',
            'dashboardData' => $dashboardData,
            'totalAreas' => $dashboardData['totalAreas'],
            'totalEmployees' => $dashboardData['totalEmployees'],
            'totalAssignments' => $dashboardData['totalAssignments'],
            'unitStats' => $unitStats,
            'areas' => $areas,
            'userDeptScope' => $scope,
            'roleStats' => $roleStats,
            'employeesByRole' => $employeesByRole,
            'assignmentsByArea' => $assignmentsByArea,
            'loadCharts' => true,
            'loadDataTables' => true,
            'departemen' => $db->table('departemen')->select('id_departemen, nama_departemen')->orderBy('id_departemen', 'ASC')->get()->getResultArray(),
        ];
        
        return view('service/area_employee_management', $data);
    }

    /**
     * Get areas for DataTable
     */
    /**
     * Get areas data for DataTables with server-side processing
     * Supports search and pagination
     */
    public function getAreas()
    {
        try {
            // Get request data
            $post = $this->request->getPost();
            $get = $this->request->getGet();
            $request = array_merge($get, $post);
            
            // Extract search value
            $searchValue = isset($request['search']['value']) && !empty($request['search']['value'])
                ? trim($request['search']['value'])
                : null;
                
            // Get pagination
            $start = isset($request['start']) ? (int)$request['start'] : 0;
            $length = isset($request['length']) ? (int)$request['length'] : 10;
            
            // Get scope - now properly implemented for role-based filtering
            $scope = get_user_area_department_scope();
            
            // Debug logging
            log_message('debug', 'User scope: ' . json_encode($scope));
            log_message('debug', 'User role from session: ' . session()->get('role'));
            log_message('debug', 'User ID from session: ' . session()->get('user_id'));
            
            // Build base scope clause (used for totalRecords count)
            $scopeClause = " WHERE areas.is_active = 1";
            $scopeParams = [];

            // Apply role-based area scope filtering
            if ($scope !== null && !empty($scope['areas'])) {
                $scopeClause .= " AND areas.id IN (" . implode(',', array_fill(0, count($scope['areas']), '?')) . ")";
                $scopeParams = array_merge($scopeParams, $scope['areas']);
                log_message('debug', 'Applied area filtering: ' . implode(',', $scope['areas']));
            }

            // Apply department scope to areas (e.g. admin service electric only sees electric areas)
            if ($scope !== null && !empty($scope['departments'])) {
                $scopeClause .= " AND areas.departemen_id IN (" . implode(',', array_fill(0, count($scope['departments']), '?')) . ")";
                $scopeParams = array_merge($scopeParams, $scope['departments']);
                log_message('debug', 'Applied department filtering: ' . implode(',', $scope['departments']));
            }

            // Start where clause from scope
            $whereClause = $scopeClause;
            $params = $scopeParams;

            // Add search condition
            if ($searchValue) {
                $whereClause .= " AND (areas.area_name LIKE ? OR areas.area_code LIKE ? OR areas.area_description LIKE ?)";
                $params = array_merge($params, ["%$searchValue%", "%$searchValue%", "%$searchValue%"]);
            }

            // Department filter (manual filter from UI dropdown)
            $departemenId = $request['departemen_id'] ?? null;
            if ($departemenId) {
                $whereClause .= " AND areas.departemen_id = ?";
                $params[] = (int) $departemenId;
            }

            // Count total records (scope only, no search)
            $totalSql = "SELECT COUNT(*) as total FROM areas" . $scopeClause;
            $totalResult = $this->db->query($totalSql, $scopeParams);
            $totalRecords = $totalResult->getRow()->total;
            
            // Count filtered records (with scope and search)
            $filteredSql = "SELECT COUNT(*) as total FROM areas" . $whereClause;
            $filteredResult = $this->db->query($filteredSql, $params);
            $filteredRecords = $filteredResult->getRow()->total;
            
            // Get actual data (join departemen for department name)
            $dataSql = "SELECT areas.*, d.nama_departemen as departemen_name
                        FROM areas
                        LEFT JOIN departemen d ON d.id_departemen = areas.departemen_id"
                        . $whereClause . " ORDER BY areas.area_name ASC LIMIT ? OFFSET ?";
            $dataParams = array_merge($params, [$length, $start]);
            $dataResult = $this->db->query($dataSql, $dataParams);
            $areas = $dataResult->getResultArray();
            
            // ── Batch queries (4 queries total, not 4 per row) ──────────────────
            $areaIds = array_column($areas, 'id');

            // 1. Customer counts per area (batch)
            $batchCustomerCounts = [];
            if (!empty($areaIds)) {
                $ph = implode(',', array_fill(0, count($areaIds), '?'));
                $rows = $this->db->query(
                    "SELECT iu.area_id, COUNT(DISTINCT k.customer_id) as cnt
                     FROM inventory_unit iu
                     JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status = 'ACTIVE'
                     JOIN kontrak k ON k.id = ku.kontrak_id AND k.status = 'ACTIVE'
                     WHERE iu.area_id IN ($ph) GROUP BY iu.area_id",
                    $areaIds
                )->getResultArray();
                foreach ($rows as $r) {
                    $batchCustomerCounts[(int)$r['area_id']] = (int)$r['cnt'];
                }
            }

            // 2. Unit counts per area (batch)
            $batchUnitCounts = [];
            if (!empty($areaIds)) {
                $ph = implode(',', array_fill(0, count($areaIds), '?'));
                $rows = $this->db->query(
                    "SELECT area_id, COUNT(*) as cnt FROM inventory_unit WHERE area_id IN ($ph) GROUP BY area_id",
                    $areaIds
                )->getResultArray();
                foreach ($rows as $r) {
                    $batchUnitCounts[(int)$r['area_id']] = (int)$r['cnt'];
                }
            }

            // 3. Employee breakdown per area (batch, single tableExists check)
            $batchEmployeeData = [];
            $hasAssignmentsTable = $this->db->tableExists('area_employee_assignments');
            if ($hasAssignmentsTable && !empty($areaIds)) {
                try {
                    $ph = implode(',', array_fill(0, count($areaIds), '?'));
                    $rows = $this->db->query(
                        "SELECT aea.area_id,
                            SUM(CASE WHEN e.staff_role LIKE '%FOREMAN%' THEN 1 ELSE 0 END) as foreman,
                            SUM(CASE WHEN e.staff_role LIKE '%MECHANIC%' THEN 1 ELSE 0 END) as mechanic,
                            SUM(CASE WHEN e.staff_role LIKE '%HELPER%' THEN 1 ELSE 0 END) as helper,
                            GROUP_CONCAT(DISTINCT CASE WHEN e.staff_role LIKE '%FOREMAN%'
                                THEN e.staff_name END ORDER BY e.staff_name SEPARATOR ', ') as foremans,
                            GROUP_CONCAT(DISTINCT CASE WHEN e.staff_role LIKE '%MECHANIC%'
                                THEN e.staff_name END ORDER BY e.staff_name SEPARATOR ', ') as mechanics
                         FROM area_employee_assignments aea
                         JOIN employees e ON e.id = aea.employee_id
                         WHERE aea.area_id IN ($ph) AND aea.is_active = 1 AND e.is_active = 1
                         GROUP BY aea.area_id",
                        $areaIds
                    )->getResultArray();
                    foreach ($rows as $r) {
                        $batchEmployeeData[(int)$r['area_id']] = $r;
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Batch employee count error: ' . $e->getMessage());
                }
            }

            // 4. Location counts per area (batch)
            $batchLocationCounts = [];
            if (!empty($areaIds)) {
                $ph = implode(',', array_fill(0, count($areaIds), '?'));
                $rows = $this->db->query(
                    "SELECT iu.area_id, COUNT(DISTINCT ku.customer_location_id) as cnt
                     FROM inventory_unit iu
                     JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status = 'ACTIVE'
                     JOIN customer_locations cl ON cl.id = ku.customer_location_id AND cl.is_active = 1
                     WHERE iu.area_id IN ($ph) GROUP BY iu.area_id",
                    $areaIds
                )->getResultArray();
                foreach ($rows as $r) {
                    $batchLocationCounts[(int)$r['area_id']] = (int)$r['cnt'];
                }
            }
            // ── End batch queries ────────────────────────────────────────────────

            // Format data
            $data = [];
            foreach ($areas as $area) {
                $aId = (int)$area['id'];

                $emp = $batchEmployeeData[$aId] ?? null;
                $employeeBreakdown = [
                    'foreman'  => (int)($emp['foreman']  ?? 0),
                    'mechanic' => (int)($emp['mechanic'] ?? 0),
                    'helper'   => (int)($emp['helper']   ?? 0),
                ];
                $foremans  = $emp['foremans']  ?? '';
                $mechanics = $emp['mechanics'] ?? '';
                $totalEmployees = $employeeBreakdown['foreman'] + $employeeBreakdown['mechanic'] + $employeeBreakdown['helper'];

                $data[] = [
                    'id' => $area['id'],
                    'area_code' => $area['area_code'],
                    'area_name' => $area['area_name'],
                    'area_type' => $area['area_type'] ?? 'MILL',
                    'departemen_id' => $area['departemen_id'] ?? null,
                    'departemen_name' => $area['departemen_name'] ?? null,
                    'description' => $area['area_description'] ?? '',
                    'customers_count' => $batchCustomerCounts[$aId] ?? 0,
                    'location_count' => $batchLocationCounts[$aId] ?? 0,
                    'unit_count' => $batchUnitCounts[$aId] ?? 0,
                    'employees_count' => $totalEmployees,
                    'employees_breakdown' => $employeeBreakdown,
                    'foreman_count' => $employeeBreakdown['foreman'],
                    'mechanic_count' => $employeeBreakdown['mechanic'],
                    'foremans' => $foremans,
                    'mechanics' => $mechanics,
                    'created_at' => $area['created_at'],
                    'updated_at' => $area['updated_at'],
                    'is_active' => $area['is_active']
                ];
            }
            
            return $this->response->setJSON([
                'draw' => isset($request['draw']) ? intval($request['draw']) : 1,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Areas DataTable error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Return error response with debug info
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => true,
                'message' => 'Gagal memuat data. Silakan coba lagi.',
                'debug_info' => [
                    'error_message' => $e->getMessage(),
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile()
                ]
            ]);
        }
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
        $builder->join('inventory_unit iu_c', 'iu_c.area_id = areas.id', 'left');
        $builder->join('kontrak_unit ku_c', 'ku_c.unit_id = iu_c.id_inventory_unit AND ku_c.status = \'ACTIVE\'', 'left');
        $builder->join('kontrak k_c', 'k_c.id = ku_c.kontrak_id AND k_c.status = \'ACTIVE\'', 'left');
        $builder->join('customers c', 'c.id = k_c.customer_id', 'left');
        $builder->where('areas.id', $id);
        $builder->where('areas.deleted_at IS NULL', null, false);
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
     * Show single area data (for edit modal)
     */
    public function showArea($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID area tidak valid'
            ]);
        }

        $db = \Config\Database::connect();
        $area = $db->table('areas')
            ->select('id, area_code, area_name, area_type, area_description as description, departemen_id, is_active')
            ->where('id', $id)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if (!$area) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Area tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => ['area' => $area]
        ]);
    }

    /**
     * Show single employee data (for edit modal)
     */
    public function showEmployee($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID karyawan tidak valid'
            ]);
        }

        $db = \Config\Database::connect();
        $employee = $db->table('employees')
            ->select('id, staff_code, staff_name, staff_role, staff_role as role, job_description as description, departemen_id, work_location, phone, email, address, is_active')
            ->where('id', $id)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if (!$employee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => ['employee' => $employee]
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
        
        $validationMessages = [
            'area_code' => [
                'required' => 'Kode area harus diisi.',
                'max_length' => 'Kode area maksimal 20 karakter.',
                'is_unique' => 'Kode area sudah digunakan. Silakan gunakan kode yang berbeda.'
            ],
            'area_name' => [
                'required' => 'Nama area harus diisi.',
                'max_length' => 'Nama area maksimal 100 karakter.'
            ]
        ];
        
        if (!$this->validateData($input, $validationRules, $validationMessages)) {
            $errors = $this->validator->getErrors();
            $firstError = !empty($errors) ? reset($errors) : 'Validasi gagal';
            return $this->response->setJSON([
                'success' => false,
                'message' => $firstError,
                'errors' => $errors
            ]);
        }
        
        $areaType = !empty($input['area_type']) ? $input['area_type'] : 'MILL';
        $data = [
            'area_code' => $input['area_code'],
            'area_name' => $input['area_name'],
            'area_description' => !empty($input['area_description']) ? $input['area_description'] : null,
            'area_type' => $areaType,
            'departemen_id' => ($areaType === 'CENTRAL' && !empty($input['departemen_id'])) ? (int)$input['departemen_id'] : null,
            'is_active' => 1,
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
                    'message' => 'Gagal menyimpan area. Periksa kembali data yang diisi.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Area save exception. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan area. Silakan coba lagi.'
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
        
        $validationMessages = [
            'area_code' => [
                'required' => 'Kode area harus diisi.',
                'max_length' => 'Kode area maksimal 20 karakter.',
                'is_unique' => 'Kode area sudah digunakan. Silakan gunakan kode yang berbeda.'
            ],
            'area_name' => [
                'required' => 'Nama area harus diisi.',
                'max_length' => 'Nama area maksimal 100 karakter.'
            ]
        ];
        
        if (!$this->validateData($input, $validationRules, $validationMessages)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                'success' => false,
                'message' => !empty($errors) ? reset($errors) : 'Validasi gagal',
                'errors' => $errors
            ]);
        }
        
        $areaType = !empty($input['area_type']) ? $input['area_type'] : 'MILL';
        $data = [
            'area_code' => $input['area_code'],
            'area_name' => $input['area_name'],
            'area_description' => !empty($input['area_description']) ? $input['area_description'] : null,
            'area_type' => $areaType,
            'departemen_id' => ($areaType === 'CENTRAL' && !empty($input['departemen_id'])) ? (int)$input['departemen_id'] : null,
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
                    'message' => 'Area tidak ditemukan'
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
                    'message' => 'Gagal mengupdate area. Data tidak berubah atau area tidak ditemukan.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Area update exception. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate area. Silakan coba lagi.'
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
            log_message('error', 'Area delete exception. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus area. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get employees for DataTable
     */
    public function getEmployees()
    {
        try {
            // Handle both GET and POST requests (DataTables uses POST)
            $request = $this->request->getMethod() === 'post' 
                ? $this->request->getPost() 
                : $this->request->getGet();
            
            // Debug: Log the request
            log_message('debug', 'Employees DataTable request: ' . json_encode($request));
            log_message('debug', 'Employees Search value: ' . ($request['search']['value'] ?? 'empty'));
            
            // Apply division-based department filter first
            $allowedDepartments = get_user_division_departments();
            
            // Total records without filtering (apply department filter)
            $totalBuilder = $this->db->table('employees');
            $totalBuilder->where('is_active', 1);
            if ($allowedDepartments !== null && is_array($allowedDepartments)) {
                $totalBuilder->whereIn('departemen_id', $allowedDepartments);
            }
            $totalRecords = $totalBuilder->countAllResults();
            
            // Build query
            $builder = $this->db->table('employees');
            $builder->select('employees.*, d.nama_departemen');
            $builder->join('departemen d', 'employees.departemen_id = d.id_departemen', 'left');
            $builder->where('employees.is_active', 1);
            
            // Apply department filter to main builder
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
                    ->orLike('employees.job_description', $searchValue)
                    ->orLike('employees.work_location', $searchValue)
                    ->orLike('employees.phone', $searchValue)
                    ->orLike('employees.email', $searchValue)
                    ->orLike('d.nama_departemen', $searchValue)
                ->groupEnd();
            }
            
            // Count filtered records (clone builder for count to avoid side effects)
            $countBuilder = clone $builder;
            $recordsFiltered = $countBuilder->countAllResults();
            
            // Debug logging
            log_message('debug', 'Employees - Total records: ' . $totalRecords);
            log_message('debug', 'Employees - Filtered records: ' . $recordsFiltered);
            log_message('debug', 'Employees - Search applied: ' . (isset($request['search']['value']) && !empty($request['search']['value']) ? 'YES' : 'NO'));
            
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
                        if (in_array($column, ['staff_code', 'staff_name', 'staff_role', 'phone', 'email'])) {
                            $builder->orderBy('employees.' . $column, $dir);
                        }
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
            
            // Get all employee IDs for batch assignment query
            $employeeIds = array_column($results, 'id');
            
            // Batch fetch all assignments to avoid N+1 queries
            $assignmentsMap = [];
            if (!empty($employeeIds)) {
                $assignmentsQuery = $this->db->table('area_employee_assignments aea')
                    ->select('aea.employee_id, a.area_name, a.area_code, a.area_type, aea.assignment_type')
                    ->join('areas a', 'a.id = aea.area_id')
                    ->whereIn('aea.employee_id', $employeeIds)
                    ->where('aea.is_active', 1)
                    ->where('aea.deleted_at IS NULL', null, false)
                    ->get()
                    ->getResultArray();
                
                // Group by employee_id
                foreach ($assignmentsQuery as $assignment) {
                    $empId = $assignment['employee_id'];
                    unset($assignment['employee_id']);
                    if (!isset($assignmentsMap[$empId])) {
                        $assignmentsMap[$empId] = [];
                    }
                    $assignmentsMap[$empId][] = $assignment;
                }
            }
            
            // Format data for DataTable
            $data = [];
            foreach ($results as $row) {
                // Get assignments from map (already fetched)
                $assignments = $assignmentsMap[$row['id']] ?? [];
                
                $formattedRow = [
                    'id' => $row['id'],
                    'staff_code' => $row['staff_code'],
                    'staff_name' => $row['staff_name'],
                    'staff_role' => $row['staff_role'],
                    'job_description' => $row['job_description'] ?? '-',
                    'work_location' => $row['work_location'] ?? '-',
                    'departemen' => $row['nama_departemen'] ?? '-',
                    'area_assignments' => $assignments,
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'address' => $row['address'] ?? null,
                    'hire_date' => $row['hire_date'] ?? null,
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
        } catch (\Exception $e) {
             log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => true,
                'message' => 'Gagal memuat data. Silakan coba lagi.',
                'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ]);
        }
    }

    /**
     * Get employee detail with assignments
     */
    public function getEmployeeDetail($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID karyawan harus diisi'
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
                    'message' => 'Karyawan tidak ditemukan'
                ]);
            }

            // Get employee assignments
            $assignmentBuilder = $db->table('area_employee_assignments aea');
            $assignmentBuilder->select('aea.*, a.area_name, a.area_code');
            $assignmentBuilder->join('areas a', 'aea.area_id = a.id');
            $assignmentBuilder->where('aea.employee_id', $id);
            $assignmentBuilder->where('aea.deleted_at IS NULL', null, false);
            $assignmentBuilder->orderBy('aea.assignment_type', 'ASC');
            
            $assignments = $assignmentBuilder->get()->getResultArray();
            
            $employee['assignments'] = $assignments;

            return $this->response->setJSON([
                'success' => true,
                'data' => $employee
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat detail karyawan. Silakan coba lagi.'
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
            'staff_role' => 'required|in_list[ADMIN,SUPERVISOR,FOREMAN,MECHANIC,MECHANIC_SERVICE_AREA,MECHANIC_UNIT_PREP,MECHANIC_FABRICATION,HELPER]',
            'work_location' => 'required|in_list[CENTRAL,MILL,BOTH]',
            'job_description' => 'required',
            'departemen_id' => 'permit_empty|integer',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[100]',
            'address' => 'permit_empty',
            'hire_date' => 'permit_empty|valid_date'
        ];
        
        $validationMessages = [
            'staff_code' => [
                'required' => 'Kode karyawan harus diisi.',
                'max_length' => 'Kode karyawan maksimal 20 karakter.',
                'is_unique' => 'Kode karyawan sudah digunakan. Silakan gunakan kode yang berbeda.'
            ],
            'staff_name' => [
                'required' => 'Nama karyawan harus diisi.',
                'max_length' => 'Nama karyawan maksimal 100 karakter.'
            ],
            'staff_role' => [
                'required' => 'Role karyawan harus dipilih.',
                'in_list' => 'Role karyawan tidak valid.'
            ],
            'work_location' => [
                'required' => 'Lokasi kerja harus dipilih.',
                'in_list' => 'Lokasi kerja tidak valid.'
            ],
            'job_description' => [
                'required' => 'Deskripsi pekerjaan harus diisi.'
            ],
            'email' => [
                'valid_email' => 'Format email tidak valid.'
            ]
        ];
        
        if (!$this->validateData($input, $validationRules, $validationMessages)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                'success' => false,
                'message' => !empty($errors) ? reset($errors) : 'Validasi gagal',
                'errors' => $errors
            ]);
        }
        
        $data = [
            'staff_code' => $input['staff_code'],
            'staff_name' => $input['staff_name'],
            'staff_role' => $input['staff_role'],
            'work_location' => $input['work_location'],
            'job_description' => $input['job_description'],
            'departemen_id' => !empty($input['departemen_id']) ? $input['departemen_id'] : null,
            'phone' => $input['phone'] ?? null,
            'email' => $input['email'] ?? null,
            'address' => $input['address'] ?? null,
            'hire_date' => !empty($input['hire_date']) ? $input['hire_date'] : null,
            'is_active' => 1
        ];

        try {
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
                    'message' => 'Gagal menyimpan data karyawan. Periksa kembali data yang diisi.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Employee save exception. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan karyawan. Silakan coba lagi.'
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
            'staff_role' => 'required|in_list[ADMIN,SUPERVISOR,FOREMAN,MECHANIC,MECHANIC_SERVICE_AREA,MECHANIC_UNIT_PREP,MECHANIC_FABRICATION,HELPER]',
            'work_location' => 'permit_empty|in_list[CENTRAL,MILL,BOTH]',
            'departemen_id' => 'permit_empty|integer',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[100]',
            'address' => 'permit_empty',
            'hire_date' => 'permit_empty|valid_date'
        ];
        
        $validationMessages = [
            'staff_code' => [
                'required' => 'Kode karyawan harus diisi.',
                'max_length' => 'Kode karyawan maksimal 20 karakter.',
                'is_unique' => 'Kode karyawan sudah digunakan. Silakan gunakan kode yang berbeda.'
            ],
            'staff_name' => [
                'required' => 'Nama karyawan harus diisi.',
                'max_length' => 'Nama karyawan maksimal 100 karakter.'
            ],
            'staff_role' => [
                'required' => 'Role karyawan harus dipilih.',
                'in_list' => 'Role karyawan tidak valid.'
            ],
            'email' => [
                'valid_email' => 'Format email tidak valid.'
            ]
        ];
        
        if (!$this->validateData($input, $validationRules, $validationMessages)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                'success' => false,
                'message' => !empty($errors) ? reset($errors) : 'Validasi gagal',
                'errors' => $errors
            ]);
        }
        
        $data = [
            'staff_code' => $input['staff_code'],
            'staff_name' => $input['staff_name'],
            'staff_role' => $input['staff_role'],
            'departemen_id' => !empty($input['departemen_id']) ? $input['departemen_id'] : null,
            'work_location' => !empty($input['work_location']) ? $input['work_location'] : null,
            'job_description' => $input['job_description'] ?? null,
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
                    'message' => 'Karyawan tidak ditemukan'
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
                    'message' => 'Gagal mengupdate karyawan. Data tidak berubah atau karyawan tidak ditemukan.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Employee update exception. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate karyawan. Silakan coba lagi.'
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
            log_message('error', 'Employee delete exception. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus karyawan. Silakan coba lagi.'
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
        $builder->where('area_employee_assignments.deleted_at IS NULL', null, false);
        
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
        $builder->where('area_employee_assignments.deleted_at IS NULL', null, false);
        
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
            'department_scope' => 'permit_empty|string|max_length[100]',
            'start_date' => 'required|valid_date',
            'end_date' => 'permit_empty|valid_date',
            'notes' => 'permit_empty'
        ];
        
        if (!$this->validateData($input, $validationRules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                'success' => false,
                'message' => !empty($errors) ? reset($errors) : 'Validasi gagal',
                'errors' => $errors
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
            'department_scope' => !empty($input['department_scope']) ? $input['department_scope'] : 'ALL',
            'start_date' => $input['start_date'],
            'end_date' => !empty($input['end_date']) ? $input['end_date'] : null,
            'notes' => $input['notes'] ?? null,
            'is_active' => 1
        ];
        
        try {
            // Insert data
            $assignmentId = $this->assignmentModel->insert($data);
            
            if ($assignmentId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Penugasan berhasil disimpan',
                    'id' => $assignmentId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan penugasan. Periksa kembali data yang diisi.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Assignment save exception. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan penugasan. Silakan coba lagi.'
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
        $builder->where('employees.deleted_at IS NULL', null, false);
        
        if ($role) {
            // Support role subtypes (MECHANIC_*, HELPER_*)
            if ($role === 'MECHANIC' || $role === 'HELPER') {
                $builder->like('employees.staff_role', $role, 'both');
            } else {
                $builder->where('employees.staff_role', $role);
            }
        }
        
        if ($areaId) {
            // Exclude employees already assigned as PRIMARY to this area
            $subquery = $this->assignmentModel->builder()
                ->select('employee_id')
                ->where('area_id', $areaId)
                ->where('assignment_type', 'PRIMARY')
                ->where('is_active', 1)
                ->where('deleted_at IS NULL', null, false);
            
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
        $builder->where('area_employee_assignments.deleted_at IS NULL', null, false);
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
        $builder->where('area_employee_assignments.deleted_at IS NULL', null, false);
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
        $builder->where('area_employee_assignments.deleted_at IS NULL', null, false);
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
            'department_scope' => 'permit_empty|string|max_length[100]',
            'start_date' => 'required|valid_date',
            'end_date' => 'permit_empty|valid_date',
            'notes' => 'permit_empty'
        ];
        
        if (!$this->validateData($input, $validationRules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                'success' => false,
                'message' => !empty($errors) ? reset($errors) : 'Validasi gagal',
                'errors' => $errors
            ]);
        }
        
        $data = [
            'area_id' => $input['area_id'],
            'employee_id' => $input['staff_id'], // Map staff_id to employee_id
            'assignment_type' => $input['assignment_type'],
            'department_scope' => !empty($input['department_scope']) ? $input['department_scope'] : 'ALL',
            'start_date' => $input['start_date'],
            'end_date' => !empty($input['end_date']) ? $input['end_date'] : null,
            'notes' => $input['notes'] ?? null,
            'is_active' => 1
        ];
        
        try {
            // Insert assignment
            $assignmentId = $this->assignmentModel->insert($data);
            
            if ($assignmentId) {
                // Get details for notification
                $db = \Config\Database::connect();
                $assignment = $db->table('area_employee_assignments saa')
                    ->select('saa.*, a.area_name, e.staff_name as employee_name')
                    ->join('areas a', 'a.id = saa.area_id', 'left')
                    ->join('employees e', 'e.id = saa.employee_id', 'left')
                    ->where('saa.id', $assignmentId)
                    ->get()
                    ->getRowArray();
                
                // Send notification - service assignment created
                if (function_exists('notify_service_assignment_created') && $assignment) {
                    notify_service_assignment_created([
                        'id' => $assignmentId,
                        'employee_name' => $assignment['employee_name'] ?? '',
                        'area_name' => $assignment['area_name'] ?? '',
                        'role' => $assignment['assignment_type'] ?? '',
                        'start_date' => $assignment['start_date'] ?? '',
                        'created_by' => session('username') ?? session('user_id'),
                        'url' => base_url('/service/area-management')
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Penugasan berhasil dibuat',
                    'id' => $assignmentId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membuat penugasan. Periksa kembali data yang diisi.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Gagal membuat data. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat penugasan. Silakan coba lagi.'
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
                'message' => 'ID penugasan tidak valid'
            ]);
        }
        
        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Penugasan tidak ditemukan'
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
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                'success' => false,
                'message' => !empty($errors) ? reset($errors) : 'Validasi gagal',
                'errors' => $errors
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
                // Get details for notification
                $assignment = $this->assignmentModel->find($id);
                $db = \Config\Database::connect();
                $details = $db->table('area_employee_assignments saa')
                    ->select('a.area_name, e.staff_name as employee_name')
                    ->join('areas a', 'a.id = saa.area_id', 'left')
                    ->join('employees e', 'e.id = saa.employee_id', 'left')
                    ->where('saa.id', $id)
                    ->get()
                    ->getRowArray();
                
                // Send notification - service assignment updated
                if (function_exists('notify_service_assignment_updated') && $details) {
                    notify_service_assignment_updated([
                        'id' => $id,
                        'employee_name' => $details['employee_name'] ?? '',
                        'area_name' => $details['area_name'] ?? '',
                        'changes' => 'Assignment details updated',
                        'updated_by' => session('username') ?? session('user_id'),
                        'url' => base_url('/service/area-management')
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Penugasan berhasil diperbarui'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui penugasan. Data tidak berubah.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating assignment. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui penugasan. Silakan coba lagi.'
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
                'message' => 'ID penugasan tidak valid'
            ]);
        }
        
        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            log_message('error', 'Delete assignment: Assignment not found for ID: ' . $id);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Penugasan tidak ditemukan'
            ]);
        }
        
        log_message('info', 'Assignment found, proceeding with deletion: ' . json_encode($assignment));
        
        try {
            // Get details before deletion for notification
            $db = \Config\Database::connect();
            $details = $db->table('area_employee_assignments saa')
                ->select('a.area_name, e.staff_name as employee_name')
                ->join('areas a', 'a.id = saa.area_id', 'left')
                ->join('employees e', 'e.id = saa.employee_id', 'left')
                ->where('saa.id', $id)
                ->get()
                ->getRowArray();
            
            // Hard delete - actually remove the record
            $deleted = $this->assignmentModel->delete($id);
            
            if ($deleted) {
                log_message('info', 'Assignment deleted successfully by user: ' . session()->get('user_id'));
                
                // Send notification - service assignment deleted
                if (function_exists('notify_service_assignment_deleted') && $details) {
                    notify_service_assignment_deleted([
                        'id' => $id,
                        'employee_name' => $details['employee_name'] ?? '',
                        'area_name' => $details['area_name'] ?? '',
                        'deleted_by' => session('username') ?? session('user_id'),
                        'url' => base_url('/service/area-management')
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Penugasan berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus penugasan. Silakan coba lagi.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting assignment. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus penugasan. Silakan coba lagi.'
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
                'message' => 'ID penugasan tidak valid'
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
                    'message' => 'Penugasan tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $assignment
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat detail penugasan. Silakan coba lagi.'
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
                'message' => 'ID penugasan tidak valid'
            ]);
        }

        $isActive = $this->request->getPost('is_active');
        if ($isActive === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status harus diisi'
            ]);
        }

        try {
            $assignment = $this->assignmentModel->find($id);
            if (!$assignment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Penugasan tidak ditemukan'
                ]);
            }

            $updated = $this->assignmentModel->update($id, [
                'is_active' => $isActive
            ]);

            if ($updated) {
                $statusText = $isActive == 1 ? 'diaktifkan' : 'dinonaktifkan';
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Penugasan berhasil {$statusText}"
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengubah status penugasan'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error toggling assignment status. Silakan coba lagi.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status penugasan. Silakan coba lagi.'
            ]);
        }
    }
}