<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\CustomerLocationModel;
use App\Models\AreaModel;
use App\Models\CustomerContractModel;
use App\Traits\ActivityLoggingTrait;
use App\Traits\DateFilterTrait;

/**
 * Customer Management Module Controller
 * 
 * Handles customer management dashboard, CRUD operations, location management,
 * and UI interactions for the customer management module.
 * 
 * Route: /customer-management
 * View: app/Views/marketing/customer_management.php
 * 
 * For API endpoints used by other modules (quotations, marketing), see Customers controller.
 * 
 * Features:
 * - Customer listing with DataTables (server-side processing)
 * - Customer CRUD operations (create, read, update, delete)
 * - Location management per customer
 * - Contract overview and statistics
 * - Export functionality (Excel/PDF)
 * - Permission-based access control
 * 
 * @package App\Controllers
 */
class CustomerManagementController extends BaseController
{
    use ActivityLoggingTrait;
    use DateFilterTrait;
    protected $db;
    protected $customerModel;
    protected $locationModel;
    protected $areaModel;
    protected $contractModel;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->customerModel = new CustomerModel();
        $this->locationModel = new CustomerLocationModel();
        $this->areaModel = new AreaModel();
        $this->contractModel = new CustomerContractModel();
        // Initialize activity logger
        if (method_exists($this, 'initializeActivityLogging')) {
            $this->initializeActivityLogging();
        }
    }

    /**
     * Display customer management dashboard
     */
    public function index()
    {
        if (!$this->hasPermission('marketing.customer.view')) {
            return redirect()->to('/')->with('error', 'Unauthorized');
        }
        $data = [
            'title' => 'Customer Management - Marketing',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/marketing/customer_management' => 'Customer Management'
            ],
            'customers' => $this->customerModel->findAll(),
            'areas' => $this->areaModel->findAll(),
            'totalCustomers' => $this->customerModel->countAllResults(),
            'totalLocations' => $this->locationModel->countAllResults(),
            'customersByArea' => $this->getCustomersByAreaStats(),
            'loadDataTables' => true, // Enable DataTables loading
        ];
        
        return view('marketing/customer_management', $data);
    }

    /**
     * Get customers with pagination and search
     */
    public function getCustomers()
    {
        try {
            // Check permission - return empty data without 403 status to avoid DataTable error
            if (!$this->hasPermission('marketing.customer.view')) {
                return $this->response->setJSON([
                    'draw' => 1,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Anda tidak memiliki izin untuk melihat data customer'
                ]);
            }
            
            $request = $this->request;
            $draw = $request->getPost('draw') ?: 1;
            $start = $request->getPost('start') ?: 0;
            $length = $request->getPost('length') ?: 10;
            
            // Get status filter
            $statusFilter = $request->getPost('status_filter') ?: 'all';
        
            // Safe array access
            $search = $request->getPost('search') ?: [];
            $searchValue = isset($search['value']) ? $search['value'] : '';
            
            $order = $request->getPost('order') ?: [['column' => 0, 'dir' => 'asc']];
            $orderColumnIndex = isset($order[0]['column']) ? $order[0]['column'] : 0;
            $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
            
            // Define columns for ordering - updated to match new structure
            $columns = ['customers.customer_code', 'customers.customer_name', 'area_name', 'pic_name', 'pic_phone', 'customers.created_at'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'customers.customer_name';
            
            // Get total records
            $totalRecords = $this->customerModel->countAllResults();
        
            // Build query with search - updated to include customer_locations primary contact and marketing_name
            $builder = $this->customerModel->builder();
            $builder->select('customers.id, customers.customer_code, customers.customer_name, customers.marketing_name, customers.created_at, customers.updated_at, customers.is_active,
                              GROUP_CONCAT(DISTINCT areas.area_name ORDER BY areas.area_name SEPARATOR ", ") as area_name,
                              MAX(cl_primary.contact_person) as pic_name, 
                              MAX(cl_primary.phone) as pic_phone, 
                              MAX(cl_primary.email) as pic_email,
                              MAX(cl_primary.address) as primary_address')
                    ->join('customer_locations cl_primary', 'customers.id = cl_primary.customer_id AND cl_primary.is_primary = 1', 'left')
                    ->join('customer_locations cl_all', 'customers.id = cl_all.customer_id', 'left')
                    ->join('areas', 'cl_all.area_id = areas.id', 'left')
                    ->groupBy('customers.id, customers.customer_code, customers.customer_name, customers.marketing_name, customers.created_at, customers.updated_at, customers.is_active');
                    
            if (!empty($searchValue)) {
                $builder->groupStart()
                        ->like('customers.customer_code', $searchValue)
                        ->orLike('customers.customer_name', $searchValue)
                        ->orLike('areas.area_name', $searchValue)
                        ->orLike('cl_primary.contact_person', $searchValue)
                        ->orLike('cl_primary.phone', $searchValue)
                        ->orLike('cl_primary.email', $searchValue)
                        ->groupEnd();
            }
            
            // For filtered count, we need to get the distinct customer IDs first
            $countBuilder = $this->db->table('customers');
            if (!empty($searchValue)) {
                $countBuilder->distinct('customers.id')
                            ->join('customer_locations cl_primary', 'customers.id = cl_primary.customer_id AND cl_primary.is_primary = 1', 'left')
                            ->join('customer_locations cl_all', 'customers.id = cl_all.customer_id', 'left')
                            ->join('areas', 'cl_all.area_id = areas.id', 'left')
                            ->groupStart()
                            ->like('customers.customer_code', $searchValue)
                            ->orLike('customers.customer_name', $searchValue)
                            ->orLike('areas.area_name', $searchValue)
                            ->orLike('cl_primary.contact_person', $searchValue)
                            ->orLike('cl_primary.phone', $searchValue)
                            ->orLike('cl_primary.email', $searchValue)
                            ->groupEnd();
                // Apply date filter to count builder too
                $this->applyDateFilter($countBuilder, 'customers.created_at');
                
                // Apply status filter to count builder
                if ($statusFilter === 'active') {
                    $countBuilder->where('customers.is_active', 1);
                } elseif ($statusFilter === 'inactive') {
                    $countBuilder->where('customers.is_active', 0);
                }
                
                $filteredRecords = count($countBuilder->get()->getResultArray());
            } else {
                // No search filter - count based on status filter only using fresh builder
                $countBuilder = $this->db->table('customers');
                $this->applyDateFilter($countBuilder, 'customers.created_at');
                
                if ($statusFilter === 'active') {
                    $countBuilder->where('customers.is_active', 1);
                } elseif ($statusFilter === 'inactive') {
                    $countBuilder->where('customers.is_active', 0);
                }
                // For 'all' and 'no_contract', no additional filter needed at count stage
                
                $filteredRecords = $countBuilder->countAllResults();
            }
            
            // Apply date filter if provided
            $this->applyDateFilter($builder, 'customers.created_at');
            
            // Apply status filter
            if ($statusFilter === 'active') {
                $builder->where('customers.is_active', 1);
            } elseif ($statusFilter === 'inactive') {
                $builder->where('customers.is_active', 0);
            }
            // Note: 'no_contract' filter will be applied after data enrichment
            
            // Apply ordering
            $builder->orderBy($orderColumn, $orderDir);
            
            // For no_contract filter, we need ALL customers first, then filter by active_contracts_count
            // So pagination will be applied AFTER filtering
            if ($statusFilter !== 'no_contract') {
                $builder->limit($length, $start);
            }

            // Clone builder for getting IDs (without limit)
            $idsBuilder = clone $builder;
            $customerIds = array_column($idsBuilder->select('id')->get()->getResultArray(), 'id');

            // If no customers, return empty
            if (empty($customerIds)) {
                $customers = [];
            } else {
                // Batch query: Get all counts in ONE query using subqueries
                $customerIdsStr = implode(',', array_map('intval', $customerIds));

                // Get locations count per customer
                $locationsQuery = "SELECT customer_id, COUNT(*) as cnt FROM customer_locations WHERE customer_id IN ($customerIdsStr) GROUP BY customer_id";
                $locationsResult = $this->db->query($locationsQuery)->getResultArray();
                $locationsByCustomer = array_column($locationsResult, 'cnt', 'customer_id');

                // Get contracts count (not cancelled) per customer
                $contractsQuery = "SELECT customer_id, COUNT(*) as cnt FROM kontrak WHERE customer_id IN ($customerIdsStr) AND status != 'CANCELLED' GROUP BY customer_id";
                $contractsResult = $this->db->query($contractsQuery)->getResultArray();
                $contractsByCustomer = array_column($contractsResult, 'cnt', 'customer_id');

                // Get active contracts count per customer
                $activeContractsQuery = "SELECT customer_id, COUNT(*) as cnt FROM kontrak WHERE customer_id IN ($customerIdsStr) AND status = 'ACTIVE' GROUP BY customer_id";
                $activeContractsResult = $this->db->query($activeContractsQuery)->getResultArray();
                $activeContractsByCustomer = array_column($activeContractsResult, 'cnt', 'customer_id');

                // Get units count via kontrak_unit junction table per customer
                $unitsQuery = "SELECT k.customer_id, COUNT(*) as cnt
                    FROM kontrak_unit ku
                    JOIN kontrak k ON k.id = ku.kontrak_id
                    WHERE k.customer_id IN ($customerIdsStr)
                    AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
                    AND (ku.is_temporary IS NULL OR ku.is_temporary = 0)
                    GROUP BY k.customer_id";
                $unitsResult = $this->db->query($unitsQuery)->getResultArray();
                $unitsByCustomer = array_column($unitsResult, 'cnt', 'customer_id');

                // Get PO count per customer
                $poQuery = "SELECT customer_id, COUNT(*) as cnt FROM kontrak
                    WHERE customer_id IN ($customerIdsStr)
                    AND status != 'CANCELLED'
                    AND customer_po_number IS NOT NULL
                    AND customer_po_number != ''
                    GROUP BY customer_id";
                $poResult = $this->db->query($poQuery)->getResultArray();
                $poByCustomer = array_column($poResult, 'cnt', 'customer_id');

                // Apply pagination and get customers
                $builder->limit($length, $start);
                $customers = $builder->get()->getResultArray();

                // Map counts to customers
                foreach ($customers as &$customer) {
                    $cid = $customer['id'];
                    $customer['locations_count'] = $locationsByCustomer[$cid] ?? 0;
                    $customer['contracts_count'] = $contractsByCustomer[$cid] ?? 0;
                    $customer['active_contracts_count'] = $activeContractsByCustomer[$cid] ?? 0;
                    $customer['total_units'] = $unitsByCustomer[$cid] ?? 0;
                    $customer['po_count'] = $poByCustomer[$cid] ?? 0;

                    // Create locations summary with contract units
                    if ($customer['locations_count'] > 0) {
                        $unitText = $customer['total_units'] > 0 ? ', ' . $customer['total_units'] . ' units' : '';
                        $customer['locations_summary'] = $customer['locations_count'] . ' location' .
                            ($customer['locations_count'] > 1 ? 's' : '') . $unitText;
                    } else {
                        $customer['locations_summary'] = 'No locations';
                    }
                }
            }
            
            // Apply no_contract filter after data enrichment
            if ($statusFilter === 'no_contract') {
                $customers = array_filter($customers, function($customer) {
                    return $customer['active_contracts_count'] == 0;
                });
                $customers = array_values($customers); // Re-index array
                
                // Update filtered count to total matching customers
                $filteredRecords = count($customers);
                
                // Apply pagination manually in PHP (since we skipped SQL LIMIT)
                $customers = array_slice($customers, $start, $length);
            }
            
            $response = [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $customers
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', 'CustomerManagementController::getCustomers - Error: ' . $e->getMessage());
            log_message('error', 'CustomerManagementController::getCustomers - File: ' . $e->getFile() . ':' . $e->getLine());
            log_message('error', 'CustomerManagementController::getCustomers - Trace: ' . $e->getTraceAsString());
            
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
     * Show customer details
     */
    public function show($id)
    {
        if (!$this->hasPermission('marketing.customer.view')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer not found']);
        }
        
        // Get customer with area info
        $customerData = $this->customerModel->getCustomersWithArea($id);
        $locations = $this->locationModel->getLocationsByCustomer($id);
        $contracts = $this->contractModel->getContractsByCustomer($id);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'customer' => $customerData,
                'locations' => $locations,
                'contracts' => $contracts
            ]
        ]);
    }

    /**
     * Show customer details (alias for show method)
     */
    public function showCustomer($id)
    {
        return $this->getCustomerDetailedInfo($id);
    }
    
    /**
     * Get customer detail (alias for backward compatibility)
     */
    public function getCustomerDetail($id)
    {
        return $this->getCustomerDetailedInfo($id);
    }
    
    /**
     * Get detailed customer information for enhanced modal view
     */
    public function getCustomerDetailedInfo($id)
    {
        // Log request for debugging
        log_message('info', '[CustomerManagement] getCustomerDetailedInfo called for ID: ' . $id);
        
        if (!$this->hasPermission('marketing.customer.view')) {
            log_message('warning', '[CustomerManagement] Permission denied for user accessing customer ID: ' . $id);
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized - Permission denied'])->setStatusCode(403);
        }
        
        try {
            log_message('debug', '[CustomerManagement] Starting data fetch for customer ID: ' . $id);
            
            // Get customer basic info with area from primary location
            $customerBuilder = $this->customerModel->builder();
            $customer = $customerBuilder->select('customers.*, areas.area_name, areas.area_code')
                                      ->join('customer_locations cl', 'customers.id = cl.customer_id AND cl.is_primary = 1', 'left')
                                      ->join('areas', 'cl.area_id = areas.id', 'left')
                                      ->where('customers.id', $id)
                                      ->get()->getRowArray();
            
            if (!$customer) {
                log_message('warning', '[CustomerManagement] Customer not found: ID ' . $id);
                return $this->response->setJSON(['success' => false, 'message' => 'Customer not found']);
            }
            
            log_message('debug', '[CustomerManagement] Customer found: ' . ($customer['customer_name'] ?? 'N/A'));
            
            // Get customer locations
            $locations = $this->locationModel->where('customer_id', $id)
                                           ->orderBy('is_primary', 'DESC')
                                           ->orderBy('location_name', 'ASC')
                                           ->findAll();
            
            // Get customer contracts with units summary from kontrak_unit junction table
            $contractsBuilder = $this->db->table('kontrak k');
            $contracts = $contractsBuilder->select('k.*, 
                                                   (SELECT GROUP_CONCAT(DISTINCT cl2.location_name SEPARATOR ", ") 
                                                    FROM kontrak_unit ku2 
                                                    JOIN customer_locations cl2 ON ku2.customer_location_id = cl2.id 
                                                    WHERE ku2.kontrak_id = k.id) as location_names,
                                                   (SELECT COUNT(*) FROM kontrak_unit ku2 WHERE ku2.kontrak_id = k.id AND (ku2.is_temporary IS NULL OR ku2.is_temporary != 1)) as active_units')
                                        ->where('k.customer_id', $id)
                                        ->where('k.status !=', 'CANCELLED')
                                        ->orderBy('k.tanggal_mulai', 'DESC')
                                        ->get()->getResultArray();

            // Get customer units - via kontrak_unit junction table (source of truth)
            $contractUnits = $this->db->table('kontrak_unit ku')
                ->select('ku.unit_id as id_inventory_unit')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->where('k.customer_id', $id)
                ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->where('(ku.is_temporary IS NULL OR ku.is_temporary = 0)')
                ->get()->getResultArray();

            $unitIds = array_column($contractUnits, 'id_inventory_unit');
            $unitIds = array_unique(array_filter($unitIds)); // Filter out nulls and empty values

            if (empty($unitIds) || count($unitIds) === 0) {
                $units = [];
            } else {
                $unitsBuilder = $this->db->table('inventory_unit iu');
                $units = $unitsBuilder->select('iu.*')
                                    ->whereIn('iu.id_inventory_unit', $unitIds)
                                    ->orderBy('iu.no_unit', 'ASC')
                                    ->get()->getResultArray();

                // Add contract info to each unit
                if (!empty($units)) {
                    foreach ($units as &$unit) {
                        $contractInfo = $this->db->table('kontrak_unit ku')
                            ->select('k.no_kontrak, cl.location_name as contract_location, ku.is_temporary')
                            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                            ->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left')
                            ->where('ku.unit_id', $unit['id_inventory_unit'])
                            ->where('(ku.is_temporary IS NULL OR ku.is_temporary != 1)')
                            ->orderBy('ku.id', 'DESC')
                            ->limit(1)
                            ->get()->getRowArray();

                        $unit['no_kontrak'] = $contractInfo['no_kontrak'] ?? null;
                        $unit['contract_location'] = $contractInfo['contract_location'] ?? null;
                        $unit['is_temporary'] = $contractInfo['is_temporary'] ?? null;
                    }
                }
            }
            
            // Get activity history (work orders via kontrak.customer_id)
            $activityBuilder = $this->db->table('spk s');
            $activities = $activityBuilder->select('s.*, "Work Order" as activity_type')
                                        ->join('kontrak k', 's.kontrak_id = k.id', 'left')
                                        ->where('k.customer_id', $id)
                                        ->where('k.status !=', 'CANCELLED')
                                        ->orderBy('s.dibuat_pada', 'DESC')
                                        ->limit(20)
                                        ->get()->getResultArray();
            
            // Add delivery instructions to activities
            $deliveryBuilder = $this->db->table('delivery_instructions di');
            $deliveries = $deliveryBuilder->select('di.*, s.pelanggan, "Delivery" as activity_type')
                                        ->join('spk s', 'di.spk_id = s.id', 'left')
                                        ->join('kontrak k', 's.kontrak_id = k.id', 'left')
                                        ->where('k.customer_id', $id)
                                        ->where('k.status !=', 'CANCELLED')
                                        ->orderBy('di.dibuat_pada', 'DESC')
                                        ->limit(10)
                                        ->get()->getResultArray();
            
            // Merge and sort activities
            $allActivities = array_merge($activities, $deliveries);
            usort($allActivities, function($a, $b) {
                $dateA = $a['dibuat_pada'] ?? $a['created_at'] ?? '1970-01-01';
                $dateB = $b['dibuat_pada'] ?? $b['created_at'] ?? '1970-01-01';
                return strtotime($dateB) - strtotime($dateA);
            });
            $allActivities = array_slice($allActivities, 0, 15); // Limit to 15 recent activities
            
            // Calculate statistics
            $stats = [
                'total_locations' => count($locations),
                'primary_locations' => count(array_filter($locations, function($loc) { return ($loc['is_primary'] ?? 0) == 1; })),
                'total_contracts' => count($contracts),
                'active_contracts' => count(array_filter($contracts, function($c) { return ($c['status'] ?? '') == 'ACTIVE'; })),
                'total_po_only' => count(array_filter($contracts, function($c) { return isset($c['rental_type']) && $c['rental_type'] == 'PO_ONLY'; })),
                'total_units' => count($units),
                'active_units' => count(array_filter($units, function($u) { 
                    $status = $u['workflow_status'] ?? $u['status_unit'] ?? null;
                    return in_array($status, ['DISEWA', 'BEROPERASI', 'DALAM_PENGIRIMAN', 'STOCK_ASET']);
                })),
                'total_contract_value' => array_sum(array_column($contracts, 'nilai_total')),
                'total_activities' => count($allActivities)
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'customer' => $customer,
                    'locations' => $locations,
                    'contracts' => $contracts,
                    'units' => $units,
                    'activities' => $allActivities,
                    'stats' => $stats
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[CustomerManagement] Error in getCustomerDetailedInfo: ' . $e->getMessage());
            log_message('error', '[CustomerManagement] Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading customer details: ' . $e->getMessage(),
                'debug' => ENVIRONMENT === 'development' ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ]);
        }
    }

    /**
     * Create new customer
     */
    public function store()
    {
        return $this->storeCustomer();
    }
    
    /**
     * Create new customer (main method)
     */
    public function storeCustomer()
    {
        if (!$this->hasPermission('marketing.customer.create')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        
        $rules = [
            // Customer basic info
            'customer_code' => 'required|is_unique[customers.customer_code]|max_length[20]',
            'customer_name' => 'required|max_length[255]',
            'is_active' => 'permit_empty|in_list[0,1]',
            
            // Primary location info
            'area_id' => 'required|integer',
            'location_name' => 'required|max_length[100]',
            'location_type' => 'permit_empty|in_list[HEAD_OFFICE,BRANCH,WAREHOUSE,FACTORY]',
            'address' => 'required|max_length[500]',
            'city' => 'required|max_length[100]',
            'province' => 'required|max_length[100]',
            'postal_code' => 'permit_empty|max_length[10]',
            
            // Contact person info
            'contact_person' => 'required|max_length[255]',
            'pic_position' => 'permit_empty|max_length[64]',
            'phone' => 'required|max_length[20]',
            'email' => 'max_length[128]',
            'notes' => 'permit_empty|max_length[255]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        // Additional email validation (only if email is provided)
        $email = $this->request->getPost('email');
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'The email field must contain a valid email address.',
                'errors' => ['email' => 'The email field must contain a valid email address.']
            ]);
        }
        
        // Start transaction
        $this->db->transStart();
        
        try {
            // Create customer
            $customerData = [
            'customer_code' => $this->request->getPost('customer_code'),
            'customer_name' => $this->request->getPost('customer_name'),
                'is_active' => $this->request->getPost('is_active') ?: 1
            ];
            
            $customerId = $this->customerModel->insert($customerData);
            
            if (!$customerId) {
                log_message('error', 'Failed to insert customer: ' . json_encode($this->customerModel->errors()));
                throw new \Exception('Failed to create customer');
            }
            
            if ($customerId) {
                // Generate location code if not provided
                $locationCode = $this->request->getPost('primary_location_code');
                if (empty($locationCode)) {
                    $now = new \DateTime();
                    $year = $now->format('Y');
                    $month = $now->format('m');
                    $day = $now->format('d');
                    $random = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
                    $locationCode = "LOC-{$year}{$month}{$day}-{$random}";
                }
                
                // Create primary location
                    $locationData = [
                        'customer_id' => $customerId,
                        'area_id' => $this->request->getPost('area_id'),
                    'location_name' => $this->request->getPost('location_name'),
                    'location_code' => $locationCode,
                    'location_type' => $this->request->getPost('location_type') ?: 'HEAD_OFFICE',
                    'address' => $this->request->getPost('address'),
                    'city' => $this->request->getPost('city'),
                    'province' => $this->request->getPost('province'),
                    'postal_code' => $this->request->getPost('postal_code'),
                    'contact_person' => $this->request->getPost('contact_person'),
                    'pic_position' => $this->request->getPost('pic_position'),
                    'phone' => $this->request->getPost('phone'),
                    'email' => $this->request->getPost('email'),
                    'notes' => $this->request->getPost('notes'),
                        'is_primary' => 1,
                    'is_active' => 1
                ];
                
                $locationId = $this->locationModel->insert($locationData);
                
                if ($locationId) {
                    $this->db->transCommit();

                    // Activity Log: CREATE customer + location
                    $this->logCreate('customers', (int)$customerId, $customerData, [
                        'description' => 'Customer created from Customer Management',
                        'relations' => $this->buildRelations('customers', (int)$customerId, [
                            'customer_locations' => [(int)$locationId]
                        ]),
                        'module_name' => 'MARKETING',
                        'submenu_item' => 'Customer Management',
                        'business_impact' => 'MEDIUM'
                    ]);
                    $this->logCreate('customer_locations', (int)$locationId, $locationData, [
                        'description' => 'Primary location created for customer',
                        'relations' => $this->buildRelations('customers', (int)$customerId, [
                            'customer_locations' => [(int)$locationId]
                        ]),
                        'module_name' => 'MARKETING',
                        'submenu_item' => 'Customer Management',
                        'business_impact' => 'LOW'
                    ]);
                    
                    // Send notification: Customer Created
                    helper('notification');
                    if (function_exists('notify_customer_created')) {
                        notify_customer_created([
                            'id' => $customerId,
                            'customer_name' => $customerData['customer_name'],
                            'customer_code' => $customerData['customer_code'],
                            'customer_type' => $customerData['customer_type'] ?? 'Regular',
                            'phone' => $locationData['phone'] ?? '',
                            'email' => $locationData['email'] ?? '',
                            'created_by' => session()->get('user_name') ?? 'System',
                            'url' => base_url('/customers/view/' . $customerId)
                        ]);
                    }
                    
                    // Send notification: Customer Location Added
                    if (function_exists('notify_customer_location_added')) {
                        notify_customer_location_added([
                            'id' => $locationId,
                            'customer_name' => $customerData['customer_name'],
                            'location_name' => $locationData['location_name'],
                            'address' => $locationData['address']
                        ]);
                    }
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Customer and primary location created successfully',
                        'data' => [
                            'customer_id' => $customerId,
                            'location_id' => $locationId
                        ]
                    ]);
                } else {
                    log_message('error', 'Failed to insert location: ' . json_encode($this->locationModel->errors()));
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to create primary location'
                    ]);
                }
            } else {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create customer'
                ]);
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating customer: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update customer
     */
    public function update($id)
    {
        return $this->updateCustomer($id);
    }
    
    /**
     * Update customer (main method)
     */
    public function updateCustomer($id)
    {
        if (!$this->hasPermission('marketing.customer.edit')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer not found']);
        }
        
        // Validate input
        // Note: customer_code is readonly and cannot be changed, so we don't validate or update it
        $rules = [
            'customer_name' => 'required|max_length[255]',
            'is_active' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        // Prepare data for update
        // Note: customer_code is intentionally excluded as it's readonly and should never change
        $data = [
            'customer_name' => $this->request->getPost('customer_name'),
            'is_active' => $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1
        ];
        
        log_message('info', '[CustomerManagement] Updating customer ID: ' . $id);
        log_message('debug', '[CustomerManagement] Update data: ' . json_encode($data));
        
        try {
            $updated = $this->customerModel->update($id, $data);
            
            log_message('debug', '[CustomerManagement] Update result: ' . ($updated ? 'SUCCESS' : 'FAILED'));
            
            if ($updated) {
                // Activity Log: UPDATE customer (diff only)
                $this->logUpdate('customers', (int)$id, $customer, $data, [
                    'description' => 'Customer updated from Customer Management',
                    'relations' => $this->buildRelations('customers', (int)$id),
                    'module_name' => 'MARKETING',
                    'submenu_item' => 'Customer Management',
                    'business_impact' => 'MEDIUM'
                ]);

                // Detect changes for notification
                $changes = [];
                foreach ($data as $key => $value) {
                    if (isset($customer[$key]) && $customer[$key] != $value) {
                        $changes[] = ucfirst(str_replace('_', ' ', $key)) . ": {$customer[$key]} → {$value}";
                    }
                }
                
                // Send notification: Customer Updated
                helper('notification');
                if (function_exists('notify_customer_updated')) {
                    notify_customer_updated([
                        'id' => $id,
                        'customer_name' => $data['customer_name'],
                        'customer_code' => $customer['customer_code'], // Use existing code (readonly field)
                        'changes' => implode(', ', $changes),
                        'updated_by' => session()->get('user_name') ?? 'System',
                        'url' => base_url('/customers/view/' . $id)
                    ]);
                }
                
                // Check for status change notification
                if (isset($data['is_active']) && isset($customer['is_active']) && $data['is_active'] != $customer['is_active']) {
                    if (function_exists('notify_customer_status_changed')) {
                        notify_customer_status_changed([
                            'id' => $id,
                            'customer_code' => $customer['customer_code'], // Use existing code (readonly field)
                            'customer_name' => $data['customer_name'],
                            'old_status' => $customer['is_active'] == 1 ? 'Active' : 'Inactive',
                            'new_status' => $data['is_active'] == 1 ? 'Active' : 'Inactive',
                            'reason' => 'Status updated from Customer Management',
                            'changed_by' => session()->get('user_name') ?? 'System',
                            'url' => base_url('/customers/view/' . $id)
                        ]);
                    }
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer updated successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', '[CustomerManagement] Update exception: ' . $e->getMessage());
            log_message('error', '[CustomerManagement] Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating customer: ' . $e->getMessage(),
                'debug' => ENVIRONMENT === 'development' ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ]);
        }
        
        // Log model errors if update returned false
        $modelErrors = $this->customerModel->errors();
        log_message('warning', '[CustomerManagement] Customer update failed - Model errors: ' . json_encode($modelErrors));
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update customer',
            'errors' => $modelErrors
        ]);
    }

    /**
     * Delete customer
     */
    public function delete($id)
    {
        return $this->deleteCustomer($id);
    }
    
    /**
     * Check if customer can be deleted
     * Returns validation data about contracts and units
     */
    public function checkCustomerDeletion($id)
    {
        if (!$this->hasPermission('marketing.customer.delete')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        
        try {
            $customer = $this->customerModel->find($id);
            
            if (!$customer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }
            
            // Check for active contracts
            $activeContracts = $this->db->table('kontrak')
                ->where('customer_id', $id)
                ->where('status', 'ACTIVE')
                ->countAllResults();
            
            // Check for units at customer location (via kontrak_unit)
            $unitsAtLocation = $this->db->table('kontrak_unit ku')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->where('k.customer_id', $id)
                ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->where('(ku.is_temporary IS NULL OR ku.is_temporary = 0)')
                ->countAllResults();
            
            // Check total contracts (including completed/terminated)
            $totalContracts = $this->db->table('kontrak')
                ->where('customer_id', $id)
                ->where('status !=', 'CANCELLED')
                ->countAllResults();
            
            // Determine if deletion is allowed
            $canDelete = ($activeContracts == 0 && $unitsAtLocation == 0);
            
            $response = [
                'can_delete' => $canDelete,
                'customer_name' => $customer['customer_name'],
                'customer_code' => $customer['customer_code'],
                'active_contracts' => $activeContracts,
                'units_at_location' => $unitsAtLocation,
                'total_contracts' => $totalContracts
            ];
            
            if (!$canDelete) {
                $response['message'] = 'Customer cannot be deleted due to active contracts or units at location';
            }
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', '[CustomerManagement] Check deletion error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error checking customer deletion status: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Delete customer (main method)
     */
    public function deleteCustomer($id)
    {
        if (!$this->hasPermission('marketing.customer.delete')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        
        try {
            $customer = $this->customerModel->find($id);
            
            if (!$customer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }
            
            // Double-check validation before deletion
            $activeContracts = $this->db->table('kontrak')
                ->where('customer_id', $id)
                ->where('status', 'ACTIVE')
                ->countAllResults();
            
            $unitsAtLocation = $this->db->table('kontrak_unit ku')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->where('k.customer_id', $id)
                ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->where('(ku.is_temporary IS NULL OR ku.is_temporary = 0)')
                ->countAllResults();
            
            // Block deletion if there are active contracts or units
            if ($activeContracts > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Cannot delete customer with {$activeContracts} active contract(s). Please terminate contracts first."
                ]);
            }
            
            if ($unitsAtLocation > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Cannot delete customer with {$unitsAtLocation} unit(s) at location. Please return units to warehouse first."
                ]);
            }
            
            // Save customer data for logging/notification before deletion
            $customerData = [
                'customer_code' => $customer['customer_code'],
                'customer_name' => $customer['customer_name']
            ];
            
            // Delete customer locations first (cascade)
            $this->locationModel->where('customer_id', $id)->delete();
            
            // Delete the customer
            $deleted = $this->customerModel->delete($id);
            
            if ($deleted) {
                // Activity Log: DELETE customer (with snapshot)
                $this->logDelete('customers', (int)$id, $customer, [
                    'description' => 'Customer deleted from Customer Management',
                    'relations' => $this->buildRelations('customers', (int)$id),
                    'module_name' => 'MARKETING',
                    'submenu_item' => 'Customer Management',
                    'business_impact' => 'HIGH',
                    'is_critical' => 1
                ]);

                // Send notification: Customer Deleted
                helper('notification');
                if (function_exists('notify_customer_deleted')) {
                    notify_customer_deleted([
                        'id' => $id,
                        'customer_name' => $customerData['customer_name'],
                        'customer_code' => $customerData['customer_code'],
                        'deleted_by' => session()->get('user_name') ?? 'System'
                    ]);
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer deleted successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', '[CustomerManagement] Delete customer error: ' . $e->getMessage());
            log_message('error', '[CustomerManagement] Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting customer: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete customer'
        ]);
    }

    // Customer Location Management Methods

    /**
     * Get customer locations
     */
    public function getLocations($customerId)
    {
        if (!$this->hasPermission('marketing.customer.view')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        $locations = $this->locationModel->getLocationsByCustomer($customerId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Get single location data for editing
     */
    public function getLocation($locationId)
    {
        if (!$this->hasPermission('marketing.customer.view')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        $location = $this->locationModel->find($locationId);
        
        if (!$location) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Location not found'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $location
        ]);
    }

    /**
     * Store customer location
     */
    public function storeLocation()
    {
        if (!$this->hasPermission('marketing.location.create')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        
        $rules = [
            'customer_id' => 'required|integer',
            'location_name' => 'required|max_length[100]',
            'address' => 'required|max_length[500]',
            'city' => 'required|max_length[100]',
            'province' => 'required|max_length[100]',
            'contact_person' => 'permit_empty|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'is_primary' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'customer_id' => $this->request->getPost('customer_id'),
            'location_name' => $this->request->getPost('location_name'),
            'location_type' => $this->request->getPost('location_type') ?: 'BRANCH',
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'province' => $this->request->getPost('province'),
            'postal_code' => $this->request->getPost('postal_code'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'pic_position' => $this->request->getPost('pic_position'),
            'notes' => $this->request->getPost('notes'),
            'is_primary' => $this->request->getPost('is_primary') ? 1 : 0,
            'is_active' => 1
        ];
        
        try {
            // If this is primary, unset other primary locations
            if ($data['is_primary']) {
                $this->locationModel->where('customer_id', $data['customer_id'])
                                   ->set('is_primary', 0)
                                   ->update();
            }
            
            $locationId = $this->locationModel->insert($data);
            
            if ($locationId) {
                // Activity Log: CREATE location
                $this->logCreate('customer_locations', (int)$locationId, $data, [
                    'description' => 'Customer location added',
                    'relations' => $this->buildRelations('customers', (int)$data['customer_id'], [
                        'customer_locations' => [(int)$locationId]
                    ]),
                    'module_name' => 'MARKETING',
                    'submenu_item' => 'Customer Management',
                    'business_impact' => 'LOW'
                ]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Location added successfully',
                    'data' => ['id' => $locationId]
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error adding location: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to add location'
        ]);
    }

    /**
     * Update customer location
     */
    public function updateLocation($id)
    {
        if (!$this->hasPermission('marketing.location.edit')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        $location = $this->locationModel->find($id);
        if (!$location) {
            return $this->response->setJSON(['success' => false, 'message' => 'Location not found']);
        }
        
        $rules = [
            'location_name' => 'required|max_length[255]',
            'address' => 'required|max_length[500]',
            'contact_person' => 'permit_empty|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'is_primary' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'location_name' => $this->request->getPost('location_name'),
            'address' => $this->request->getPost('address'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'pic_position' => $this->request->getPost('pic_position'),
            'notes' => $this->request->getPost('notes'),
            'is_primary' => $this->request->getPost('is_primary') ? 1 : 0
        ];
        
        try {
            // If this is primary, unset other primary locations
            if ($data['is_primary']) {
                $this->locationModel->where('customer_id', $location['customer_id'])
                                   ->where('id !=', $id)
                                   ->set('is_primary', 0)
                                   ->update();
            }
            
            $updated = $this->locationModel->update($id, $data);
            
            if ($updated) {
                // Activity Log: UPDATE location
                $this->logUpdate('customer_locations', (int)$id, $location, $data, [
                    'description' => 'Customer location updated',
                    'relations' => $this->buildRelations('customers', (int)$location['customer_id'], [
                        'customer_locations' => [(int)$id]
                    ]),
                    'module_name' => 'MARKETING',
                    'submenu_item' => 'Customer Management',
                    'business_impact' => 'LOW'
                ]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Location updated successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating location: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update location'
        ]);
    }

    /**
     * Delete customer location
     */
    public function deleteLocation($id)
    {
        if (!$this->hasPermission('marketing.location.delete')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        try {
            $location = $this->locationModel->find($id);
            if (!$location) {
                return $this->response->setJSON(['success' => false, 'message' => 'Location not found']);
            }
            
            // Check if this is the only location
            $locationCount = $this->locationModel->where('customer_id', $location['customer_id'])->countAllResults();
            
            if ($locationCount <= 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete the only location. Customer must have at least one location.'
                ]);
            }
            
            // Check if location has active units via kontrak_unit
            $unitsAtLocation = $this->db->table('kontrak_unit ku')
                ->select('ku.id, iu.no_unit, mu.merk_unit, mu.model_unit, iu.serial_number, kp.kapasitas_unit, k.no_kontrak')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id', 'left')
                ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                ->join('kapasitas kp', 'kp.id_kapasitas = iu.kapasitas_unit_id', 'left')
                ->where('ku.customer_location_id', $id)
                ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->get()
                ->getResultArray();
            
            if (!empty($unitsAtLocation)) {
                return $this->response->setJSON([
                    'success' => false,
                    'has_units' => true,
                    'message' => 'Lokasi ini masih memiliki ' . count($unitsAtLocation) . ' unit aktif. Silakan hapus/pindahkan unit terlebih dahulu dari halaman kontrak.',
                    'units' => $unitsAtLocation
                ]);
            }
            
            $deleted = $this->locationModel->delete($id);
            
            if ($deleted) {
                // Activity Log: DELETE location
                $this->logDelete('customer_locations', (int)$id, $location, [
                    'description' => 'Customer location deleted',
                    'relations' => $this->buildRelations('customers', (int)$location['customer_id'], [
                        'customer_locations' => [(int)$id]
                    ]),
                    'module_name' => 'MARKETING',
                    'submenu_item' => 'Customer Management',
                    'business_impact' => 'MEDIUM'
                ]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Location deleted successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting location: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete location'
        ]);
    }

    /**
     * Get areas for dropdown
     */
    public function getAreas()
    {
        try {
            $areas = $this->areaModel->where('is_active', 1)->findAll();
            return $this->response->setJSON([
                'success' => true,
                'data' => $areas
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading areas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get departemen for dropdown
     */
    public function getDepartemen()
    {
        try {
            $departemen = $this->db->table('departemen')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $departemen
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading departemen: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get tipe unit for dropdown
     */
    public function getTipeUnit()
    {
        try {
            $tipeUnit = $this->db->table('tipe_unit t')
                ->select('t.id_tipe_unit, t.tipe, t.jenis, t.id_departemen, d.nama_departemen')
                ->join('departemen d', 't.id_departemen = d.id_departemen', 'left')
                ->orderBy('d.nama_departemen', 'ASC')
                ->orderBy('t.tipe', 'ASC')
                ->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $tipeUnit
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading tipe unit: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get kapasitas for dropdown
     */
    public function getKapasitas()
    {
        try {
            $kapasitas = $this->db->table('kapasitas')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $kapasitas
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading kapasitas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get merk unit for dropdown
     */
    public function getMerkUnit()
    {
        try {
            // Check if table exists
            if (!$this->db->tableExists('merk_unit')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $merkUnit = $this->db->table('merk_unit')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $merkUnit
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Get jenis baterai for dropdown
     */
    public function getJenisBaterai()
    {
        try {
            // Check if table exists
            if (!$this->db->tableExists('jenis_baterai')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $jenisBaterai = $this->db->table('jenis_baterai')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $jenisBaterai
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Get charger for dropdown
     */
    public function getCharger()
    {
        try {
            if (!$this->db->tableExists('charger')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $charger = $this->db->table('charger')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $charger
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Get attachment tipe for dropdown
     */
    public function getAttachmentTipe()
    {
        try {
            if (!$this->db->tableExists('attachment_tipe')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $attachmentTipe = $this->db->table('attachment_tipe')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $attachmentTipe
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Get valve for dropdown
     */
    public function getValve()
    {
        try {
            if (!$this->db->tableExists('valve')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $valve = $this->db->table('valve')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $valve
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Get mast for dropdown
     */
    public function getMast()
    {
        try {
            if (!$this->db->tableExists('mast')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $mast = $this->db->table('mast')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $mast
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Get ban for dropdown
     */
    public function getBan()
    {
        try {
            if (!$this->db->tableExists('ban')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $ban = $this->db->table('ban')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $ban
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Get roda for dropdown
     */
    public function getRoda()
    {
        try {
            if (!$this->db->tableExists('roda')) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $roda = $this->db->table('roda')->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $roda
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Get customer statistics by area
     */
    private function getCustomersByAreaStats()
    {
        $builder = $this->customerModel->builder();
        $builder->select('areas.area_name, COUNT(customers.id) as customer_count')
                ->join('customer_locations cl', 'customers.id = cl.customer_id AND cl.is_primary = 1', 'left')
                ->join('areas', 'cl.area_id = areas.id', 'left')
                ->groupBy('areas.id, areas.area_name')
                ->orderBy('customer_count', 'DESC');
                
        return $builder->get()->getResultArray();
    }

    /**
     * Get customer statistics for dashboard
     */
    public function getCustomerStats()
    {
        try {
            // Log date filter params for debugging
            $params = $this->getDateFilterParams();
            log_message('info', 'CustomerStats - Date filter params: ' . json_encode($params));
            
            // Get total customers with date filter
            $customerBuilder = $this->customerModel->builder();
            $this->applyDateFilter($customerBuilder, 'created_at');
            $totalCustomers = $customerBuilder->countAllResults();
            
            // Get active customers with date filter
            $activeBuilder = $this->customerModel->builder();
            $this->applyDateFilter($activeBuilder, 'created_at');
            $activeCustomers = $activeBuilder->where('is_active', 1)->countAllResults();
            
            // Get total contracts for filtered customers via customer_contracts
            // customer_contracts table doesn't exist; count directly from kontrak
            $contractBuilder = $this->db->table('kontrak k');
            $contractBuilder->select('COUNT(DISTINCT k.id) as total')
                ->join('customers c', 'c.id = k.customer_id', 'left')
                ->where('k.status !=', 'CANCELLED');
            $this->applyDateFilter($contractBuilder, 'c.created_at');
            $contractResultSet = $contractBuilder->get();
            $contractResult    = $contractResultSet ? $contractResultSet->getRow() : null;
            $totalContracts = $contractResult->total ?? 0;
            
            // Get total units for filtered customers via kontrak_unit junction table
            $unitBuilder = $this->db->table('kontrak_unit ku');
            $unitBuilder->select('COUNT(DISTINCT ku.unit_id) as total')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customers c', 'c.id = k.customer_id', 'left')
                ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
                ->where('(ku.is_temporary IS NULL OR ku.is_temporary = 0)');
            $this->applyDateFilter($unitBuilder, 'c.created_at');
            $unitResultSet = $unitBuilder->get();
            $unitResult    = $unitResultSet ? $unitResultSet->getRow() : null;
            $totalUnits = $unitResult->total ?? 0;
            
            // Get tab counts (for status filter tabs)
            $allCount = $this->customerModel->countAllResults();
            $activeCount = $this->customerModel->where('is_active', 1)->countAllResults();
            $inactiveCount = $this->customerModel->where('is_active', 0)->countAllResults();
            
            // Get customers with no active contracts
            $allCustomers = $this->customerModel->findAll();
            $noContractCount = 0;
            foreach ($allCustomers as $customer) {
                $activeContractsCount = $this->db->table('kontrak')
                    ->where('customer_id', $customer['id'])
                    ->where('status', 'ACTIVE')
                    ->countAllResults();
                if ($activeContractsCount == 0) {
                    $noContractCount++;
                }
            }
            
            $stats = [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'total_contracts' => $totalContracts,
                'total_units' => $totalUnits,
                'tab_counts' => [
                    'all' => $allCount,
                    'active' => $activeCount,
                    'inactive' => $inactiveCount,
                    'no_contract' => $noContractCount
                ]
            ];
            
            log_message('info', 'CustomerStats - Results: ' . json_encode($stats));
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'CustomerManagementController::getCustomerStats - Error: ' . $e->getMessage());
            log_message('error', 'CustomerManagementController::getCustomerStats - Trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load statistics: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get customer contracts (moved from CustomerManagementNew)
     */
    public function getCustomerContracts($customerId)
    {
        try {
            $customerId = (int)$customerId;
            
            if (!$customerId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid customer ID'
                ]);
            }

            // Get limit parameter from query string (default: no limit)
            $limit = $this->request->getGet('limit');
            $limit = $limit ? (int)$limit : null;

            // Get customer name first
            $customer = $this->db->table('customers')
                ->where('id', $customerId)
                ->get()
                ->getRowArray();

            if (!$customer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }

            // Get total count for all contracts/PO directly from kontrak
            $totalQuery = "
                SELECT COUNT(*) as total,
                       COUNT(CASE WHEN k.rental_type = 'CONTRACT' THEN 1 END) as total_contracts,
                       COUNT(CASE WHEN k.rental_type = 'PO_ONLY' THEN 1 END) as total_po_only,
                       COUNT(CASE WHEN k.rental_type = 'DAILY_SPOT' THEN 1 END) as total_daily_spot
                FROM kontrak k
                WHERE k.customer_id = ? AND k.status != 'CANCELLED'
            ";
            $totalResult = $this->db->query($totalQuery, [$customerId])->getRowArray();

            // Get contracts for this customer directly from kontrak
            $query = "
                SELECT 
                    k.id,
                    k.no_kontrak,
                    k.customer_po_number,
                    k.rental_type,
                    c.customer_name as pelanggan,
                    (SELECT GROUP_CONCAT(DISTINCT cl2.location_name SEPARATOR ', ') 
                     FROM kontrak_unit ku2 
                     JOIN customer_locations cl2 ON ku2.customer_location_id = cl2.id 
                     WHERE ku2.kontrak_id = k.id) as lokasi,
                    k.tanggal_mulai,
                    k.tanggal_berakhir as tanggal_selesai,
                    k.status,
                    k.total_units,
                    k.nilai_total,
                    k.dibuat_pada as created_at,
                    k.diperbarui_pada as updated_at
                FROM kontrak k
                LEFT JOIN customers c ON k.customer_id = c.id
                WHERE k.customer_id = ? AND k.status != 'CANCELLED'
                ORDER BY k.dibuat_pada DESC
            ";
            
            if ($limit) {
                $query .= " LIMIT " . $limit;
            }
            
            $result = $this->db->query($query, [$customerId]);
            $contracts = $result->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $contracts,
                'total' => $totalResult['total'],
                'stats' => [
                    'total_contracts' => $totalResult['total_contracts'],
                    'total_po_only' => $totalResult['total_po_only'],
                    'total_daily_spot' => $totalResult['total_daily_spot']
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'CustomerManagementController::getCustomerContracts - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load contracts: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get customer activity log for Activity tab
     */
    public function getCustomerActivity($customerId)
    {
        try {
            $customerId = (int)$customerId;
            
            if (!$customerId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid customer ID'
                ]);
            }

            // Get filter parameter (all, contract, quotation, delivery, location)
            $filter = $this->request->getGet('filter') ?? 'all';

            $activities = [];

            // Get contract activities
            if ($filter === 'all' || $filter === 'contract') {
                $contractQuery = "
                    SELECT 
                        k.id,
                        'contract' as type,
                        CONCAT('Contract ', k.rental_type, ' - ', k.no_kontrak) as title,
                        CONCAT('Created ', CASE WHEN k.rental_type='CONTRACT' THEN 'formal contract' 
                                                WHEN k.rental_type='PO_ONLY' THEN 'PO-only agreement' 
                                                ELSE 'daily/spot rental' END) as description,
                        u.username as user,
                        k.dibuat_pada as created_at
                    FROM kontrak k
                    LEFT JOIN customers c ON k.customer_id = c.id
                    LEFT JOIN users u ON k.dibuat_oleh = u.id
                    WHERE c.id = ?
                    ORDER BY k.dibuat_pada DESC
                    LIMIT 20
                ";
                $contractActivities = $this->db->query($contractQuery, [$customerId])->getResultArray();
                $activities = array_merge($activities, $contractActivities);
            }

            // Get quotation activities
            if ($filter === 'all' || $filter === 'quotation') {
                $quotationQuery = "
                    SELECT 
                        q.id_quotation as id,
                        'quotation' as type,
                        CONCAT('Quotation #', q.quotation_number, ' - ', q.stage) as title,
                        CONCAT('Quotation for ', q.prospect_name) as description,
                        u.username as user,
                        q.created_at
                    FROM quotations q
                    LEFT JOIN customers c ON q.created_customer_id = c.id
                    LEFT JOIN users u ON q.created_by = u.id
                    WHERE c.id = ?
                    ORDER BY q.created_at DESC
                    LIMIT 20
                ";
                $quotationActivities = $this->db->query($quotationQuery, [$customerId])->getResultArray();
                $activities = array_merge($activities, $quotationActivities);
            }

            // Get delivery activities
            if ($filter === 'all' || $filter === 'delivery') {
                $deliveryQuery = "
                    SELECT 
                        di.id,
                        'delivery' as type,
                        CONCAT('Delivery ', di.nomor_di, ' - ', di.status_di) as title,
                        CONCAT('Delivery on ', DATE_FORMAT(di.tanggal_kirim, '%d %b %Y')) as description,
                        u.username as user,
                        di.dibuat_pada as created_at
                    FROM delivery_instructions di
                    LEFT JOIN spk s ON di.spk_id = s.id
                    LEFT JOIN kontrak k ON s.kontrak_id = k.id
                    LEFT JOIN customers c ON k.customer_id = c.id
                    LEFT JOIN users u ON u.id = di.dibuat_oleh
                    WHERE c.id = ?
                    ORDER BY di.dibuat_pada DESC
                    LIMIT 20
                ";
                $deliveryActivities = $this->db->query($deliveryQuery, [$customerId])->getResultArray();
                $activities = array_merge($activities, $deliveryActivities);
            }

            // Get location activities
            if ($filter === 'all' || $filter === 'location') {
                $locationQuery = "
                    SELECT 
                        cl.id,
                        'location' as type,
                        CONCAT('Location ', CASE WHEN cl.is_primary=1 THEN '(Primary) ' ELSE '' END, '- ', cl.location_name) as title,
                        CONCAT('Added location in ', a.area_name) as description,
                        NULL as user,
                        cl.created_at
                    FROM customer_locations cl
                    LEFT JOIN areas a ON cl.area_id = a.id
                    WHERE cl.customer_id = ?
                    ORDER BY cl.created_at DESC
                    LIMIT 20
                ";
                $locationActivities = $this->db->query($locationQuery, [$customerId])->getResultArray();
                $activities = array_merge($activities, $locationActivities);
            }

            // Sort all activities by date
            usort($activities, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            // Limit to 30 most recent
            $activities = array_slice($activities, 0, 30);

            return $this->response->setJSON([
                'success' => true,
                'data' => $activities
            ]);

        } catch (\Exception $e) {
            log_message('error', 'CustomerManagementController::getCustomerActivity - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load activity log: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get customer locations (moved from CustomerManagementNew)
     */
    public function getCustomerLocations($customerId)
    {
        try {
            // First, try to add location_code column if it doesn't exist
            try {
                $this->db->query("ALTER TABLE customer_locations ADD COLUMN location_code VARCHAR(50) NULL AFTER location_name");
            } catch (\Exception $e) {
                // Column might already exist, ignore error
            }
            
            
            $locations = $this->db->table('customer_locations cl')
                ->select('cl.*, areas.area_name, areas.area_code')
                ->join('areas', 'cl.area_id = areas.id', 'left')
                ->where('cl.customer_id', $customerId)
                ->where('cl.is_active', 1)
                ->orderBy('cl.is_primary', 'DESC')
                ->orderBy('cl.location_name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $locations
            ]);

        } catch (\Exception $e) {
            log_message('error', 'CustomerManagementController::getCustomerLocations - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load locations'
            ]);
        }
    }

    /**
     * Store new customer location
     */
    public function storeCustomerLocation()
    {
        if (!$this->hasPermission('marketing.customer.update')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }

        $rules = [
            'customer_id' => 'required|integer',
            'area_id' => 'required|integer',
            'location_name' => 'required|max_length[100]',
            'address' => 'required|max_length[500]',
            'city' => 'required|max_length[100]',
            'province' => 'required|max_length[100]',
            'postal_code' => 'permit_empty|max_length[10]',
            'contact_person' => 'permit_empty|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|max_length[128]',
            'notes' => 'permit_empty|max_length[255]',
            'is_primary' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        // Generate location code if not provided
        $locationCode = $this->request->getPost('location_code');
        if (empty($locationCode)) {
            $now = new \DateTime();
            $year = $now->format('Y');
            $month = $now->format('m');
            $day = $now->format('d');
            $random = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
            $locationCode = "LOC-{$year}{$month}{$day}-{$random}";
        }

        $data = [
            'customer_id' => (int) $this->request->getPost('customer_id'),
            'area_id' => (int) $this->request->getPost('area_id'),
            'location_name' => $this->request->getPost('location_name'),
            'location_code' => $locationCode,
            'location_type' => $this->request->getPost('location_type') ?: 'BRANCH',
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'province' => $this->request->getPost('province'),
            'postal_code' => $this->request->getPost('postal_code'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'notes' => $this->request->getPost('notes'),
            'is_primary' => (int) ($this->request->getPost('is_primary') ?: 0),
            'is_active' => 1,
        ];

        try {
            $id = $this->locationModel->insert($data);
            if (!$id) {
                return $this->response->setJSON(['success'=>false,'message'=>'Failed to create location']);
            }
            
            // Send notification: Customer Location Added
            helper('notification');
            $customer = $this->customerModel->find($data['customer_id']);
            if ($customer) {
                notify_customer_location_added([
                    'id' => $id,
                    'customer_name' => $customer['customer_name'],
                    'location_name' => $data['location_name'],
                    'address' => $data['address']
                ]);
            }
            
            return $this->response->setJSON(['success'=>true,'message'=>'Location created','data'=>['id'=>$id]]);
        } catch (\Exception $e) {
            log_message('error', 'storeCustomerLocation error: '.$e->getMessage());
            return $this->response->setJSON(['success'=>false,'message'=>'Failed to create location']);
        }
    }

    /**
     * Show customer location for editing
     */
    public function showCustomerLocation($id)
    {
        try {
            $location = $this->locationModel->find($id);
            if (!$location) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Location not found'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $location
            ]);
        } catch (\Exception $e) {
            log_message('error', 'showCustomerLocation error: '.$e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load location'
            ]);
        }
    }

    /**
     * Update customer location
     */
    public function updateCustomerLocation($id)
    {
        if (!$this->hasPermission('marketing.customer.update')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }

        $rules = [
            'area_id' => 'required|integer',
            'location_name' => 'required|max_length[100]',
            'address' => 'required|max_length[500]',
            'city' => 'required|max_length[100]',
            'province' => 'required|max_length[100]',
            'postal_code' => 'permit_empty|max_length[10]',
            'contact_person' => 'permit_empty|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|max_length[128]',
            'notes' => 'permit_empty|max_length[255]',
            'is_primary' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'area_id' => (int) $this->request->getPost('area_id'),
            'location_name' => $this->request->getPost('location_name'),
            'location_code' => $this->request->getPost('location_code'),
            'location_type' => $this->request->getPost('location_type') ?: 'BRANCH',
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'province' => $this->request->getPost('province'),
            'postal_code' => $this->request->getPost('postal_code'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'notes' => $this->request->getPost('notes'),
            'is_primary' => (int) ($this->request->getPost('is_primary') ?: 0),
        ];

        try {
            // Get location data before update
            $location = $this->locationModel->find($id);
            
            // Use direct database update for reliable location_code updates
            $builder = $this->db->table('customer_locations');
            $builder->where('id', (int)$id);
            $ok = $builder->update($data);
            
            if (!$ok) {
                return $this->response->setJSON(['success'=>false,'message'=>'Failed to update location']);
            }
            
            // Send notification if location was changed
            helper('notification');
            if ($location) {
                $customer = $this->customerModel->find($location['customer_id']);
                notify_customer_location_added([
                    'id' => $id,
                    'customer_name' => $customer['customer_name'] ?? '',
                    'location_name' => $data['location_name'],
                    'address' => $data['address']
                ]);
            }
            
            return $this->response->setJSON(['success'=>true,'message'=>'Location updated']);
        } catch (\Exception $e) {
            log_message('error', 'updateCustomerLocation error: '.$e->getMessage());
            return $this->response->setJSON(['success'=>false,'message'=>'Failed to update location']);
        }
    }

    /**
     * Generate PDF report for customer
     */
    public function generateCustomerPDF($customerId = null)
    {
        try {
            if (!$customerId) {
                return $this->response->setJSON(['success' => false, 'message' => 'Customer ID is required']);
            }

            // Get customer data
            $customer = $this->customerModel->find($customerId);
            if (!$customer) {
                return $this->response->setJSON(['success' => false, 'message' => 'Customer not found']);
            }

            // Get customer locations
            $locations = $this->db->table('customer_locations cl')
                ->select('cl.*, a.area_name')
                ->join('areas a', 'a.id = cl.area_id', 'left')
                ->where('cl.customer_id', $customerId)
                ->get()
                ->getResultArray();

            // Get customer contracts
            $contracts = $this->db->table('kontrak k')
                ->select('k.*')
                ->where('k.customer_id', $customerId)
                ->get()
                ->getResultArray();

            // Get units for each contract with detailed specifications
            $units = [];
            foreach ($contracts as $contract) {
                $contractUnits = $this->db->table('inventory_unit iu')
                    ->select('
                        iu.*, 
                        k.no_kontrak, 
                        cl.location_name,
                        d.nama_departemen,
                        kap.kapasitas_unit,
                        tm.tipe_mast,
                        jr.tipe_roda,
                        tb.tipe_ban,
                        v.jumlah_valve,
                        tu.tipe as tipe_unit,
                        tu.jenis as jenis_unit,
                        mu.merk_unit,
                        mu.model_unit
                    ')
                    // Updated: JOIN via kontrak_unit junction table (source of truth)
                    ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit', 'left')
                    ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                    ->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left')
                    ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
                    ->join('kapasitas kap', 'kap.id_kapasitas = iu.kapasitas_unit_id', 'left')
                    ->join('tipe_mast tm', 'tm.id_mast = iu.model_mast_id', 'left')
                    ->join('jenis_roda jr', 'jr.id_roda = iu.roda_id', 'left')
                    ->join('tipe_ban tb', 'tb.id_ban = iu.ban_id', 'left')
                    ->join('valve v', 'v.id_valve = iu.valve_id', 'left')
                    ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                    ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                    ->where('ku.kontrak_id', $contract['id'])
                    ->whereIn('ku.status', ['ACTIVE', 'TEMP_ACTIVE'])
                    ->get()
                    ->getResultArray();

                // Add contract info to each unit
                foreach ($contractUnits as $unit) {
                    $unit['contract_info'] = [
                        'no_kontrak' => $contract['no_kontrak'],
                        'location_name' => $contract['location_name']
                    ];
                    $units[] = $unit;
                }
            }

            // Prepare data for PDF
            $data = [
                'customerData' => $customer,
                'contractsData' => $contracts,
                'locationsData' => $locations,
                'unitsData' => $units
            ];

            // Generate PDF directly in controller
            return $this->generatePDFDirectly($data);

        } catch (\Exception $e) {
            log_message('error', 'generateCustomerPDF error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Search customers for prospect linking
     */
    public function searchCustomers()
    {
        try {
            $searchTerm = $this->request->getPost('search');
            
            if (empty($searchTerm) || strlen($searchTerm) < 2) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Search term must be at least 2 characters',
                    'data' => []
                ]);
            }
            
            $builder = $this->customerModel->builder();
            $builder->select('customers.id, customers.customer_code, customers.customer_name, 
                             COUNT(cl.id) as location_count,
                             GROUP_CONCAT(DISTINCT CONCAT(cl.location_name, " (", cl.city, ")") 
                                         ORDER BY cl.is_primary DESC, cl.location_name ASC 
                                         SEPARATOR "; ") as locations_summary,
                             MAX(CASE WHEN cl.is_primary = 1 THEN cl.address END) as primary_address,
                             MAX(CASE WHEN cl.is_primary = 1 THEN CONCAT(cl.city, ", ", cl.province) END) as primary_location,
                             MAX(CASE WHEN cl.is_primary = 1 THEN cl.contact_person END) as primary_contact,
                             MAX(CASE WHEN cl.is_primary = 1 THEN cl.phone END) as primary_phone')
                    ->join('customer_locations cl', 'customers.id = cl.customer_id', 'left')
                    ->where('customers.is_active', 1)
                    ->groupStart()
                    ->like('customers.customer_name', $searchTerm)
                    ->orLike('customers.customer_code', $searchTerm)
                    ->orLike('cl.location_name', $searchTerm)
                    ->orLike('cl.city', $searchTerm)
                    ->groupEnd()
                    ->groupBy('customers.id, customers.customer_code, customers.customer_name')
                    ->orderBy('customers.customer_name', 'ASC')
                    ->limit(10);
            
            $results = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'CustomerManagementController::searchCustomers error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error searching customers: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Generate PDF directly without view
     */
    private function generatePDFDirectly($data)
    {
        try {
            $customer = $data['customerData'];
            $contracts = $data['contractsData'];
            $locations = $data['locationsData'];
            $units = $data['unitsData'];

            // Pass data to view
            $viewData = [
                'customerData' => $customer,
                'contractsData' => $contracts,
                'locationsData' => $locations,
                'unitsData' => $units
            ];

            // Use the proper view file
            return view('marketing/customer_pdf', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'generatePDFDirectly error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Get unlinked deliveries alert widget data
     * Shows "Hutang Dokumen" for DIs pending contract > 14 days
     * Used in dashboard for alerting marketing team
     */
    public function getUnlinkedDeliveriesWidget()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        try {
            $diModel = new \App\Models\DeliveryInstructionModel();
            $unlinked = $diModel->getUnlinkedDeliveries();
            
            // Filter: > 14 days (urgent)
            $urgent = array_filter($unlinked, function($di) {
                return isset($di['days_pending']) && $di['days_pending'] > 14;
            });
            
            // Get oldest pending days
            $oldestPending = 0;
            if (!empty($unlinked)) {
                $daysPendingArray = array_column($unlinked, 'days_pending');
                $oldestPending = !empty($daysPendingArray) ? max($daysPendingArray) : 0;
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'total_unlinked' => count($unlinked),
                    'urgent_count' => count($urgent),
                    'oldest_pending' => $oldestPending,
                    'list' => array_values($urgent)
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'getUnlinkedDeliveriesWidget error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading widget data: ' . $e->getMessage()
            ]);
        }
    }


}