<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\CustomerLocationModel;
use App\Models\AreaModel;
use App\Models\CustomerContractModel;
use App\Traits\ActivityLoggingTrait;

class CustomerManagementController extends BaseController
{
    use ActivityLoggingTrait;
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
                '/marketing' => 'Marketing',
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
            if (!$this->hasPermission('marketing.customer.view')) {
                return $this->response->setJSON(['draw'=>1,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[]])->setStatusCode(403);
            }
            
            $request = $this->request;
            $draw = $request->getPost('draw') ?: 1;
            $start = $request->getPost('start') ?: 0;
            $length = $request->getPost('length') ?: 10;
        
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
        
            // Build query with search - updated to include customer_locations primary contact
            // Build query with search - updated to include customer_locations primary contact
            $builder = $this->customerModel->builder();
            $builder->select('customers.id, customers.customer_code, customers.customer_name, customers.created_at, customers.updated_at, customers.is_active,
                              GROUP_CONCAT(DISTINCT areas.area_name ORDER BY areas.area_name SEPARATOR ", ") as area_name,
                              MAX(cl_primary.contact_person) as pic_name, 
                              MAX(cl_primary.phone) as pic_phone, 
                              MAX(cl_primary.email) as pic_email,
                              MAX(cl_primary.address) as primary_address')
                    ->join('customer_locations cl_primary', 'customers.id = cl_primary.customer_id AND cl_primary.is_primary = 1', 'left')
                    ->join('customer_locations cl_all', 'customers.id = cl_all.customer_id', 'left')
                    ->join('areas', 'cl_all.area_id = areas.id', 'left')
                    ->groupBy('customers.id, customers.customer_code, customers.customer_name, customers.created_at, customers.updated_at, customers.is_active');
                    
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
                $filteredRecords = count($countBuilder->get()->getResultArray());
            } else {
                $filteredRecords = $totalRecords;
            }
            
            // Apply ordering and pagination
            $builder->orderBy($orderColumn, $orderDir)
                    ->limit($length, $start);
            
            $customers = $builder->get()->getResultArray();
            
            // Add additional data for enhanced table display
            foreach ($customers as &$customer) {
            $customer['locations_count'] = $this->locationModel->where('customer_id', $customer['id'])->countAllResults();
            
            // Get contracts through customer_locations (updated for optimized database structure)
            $kontrakBuilder = $this->db->table('kontrak k');
            $kontrakBuilder->select('COUNT(*) as contract_count, SUM(k.total_units) as total_units, COUNT(CASE WHEN k.no_po_marketing != "" AND k.no_po_marketing IS NOT NULL THEN 1 END) as po_count')
                          ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
                          ->where('cl.customer_id', $customer['id']);
            $contractData = $kontrakBuilder->get()->getRowArray();
            
            $customer['contracts_count'] = $contractData['contract_count'] ?? 0;
            $customer['total_units'] = $contractData['total_units'] ?? 0;
            $customer['po_count'] = $contractData['po_count'] ?? 0;
            
            // Create locations summary with contract units
            if ($customer['locations_count'] > 0) {
                $unitText = $customer['total_units'] > 0 ? ', ' . $customer['total_units'] . ' units' : '';
                $customer['locations_summary'] = $customer['locations_count'] . ' location' . 
                    ($customer['locations_count'] > 1 ? 's' : '') . $unitText;
            } else {
                $customer['locations_summary'] = 'No locations';
            }
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
        if (!$this->hasPermission('marketing.customer.view')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        try {
            // Get customer basic info with area from primary location
            $customerBuilder = $this->customerModel->builder();
            $customer = $customerBuilder->select('customers.*, areas.area_name, areas.area_code')
                                      ->join('customer_locations cl', 'customers.id = cl.customer_id AND cl.is_primary = 1', 'left')
                                      ->join('areas', 'cl.area_id = areas.id', 'left')
                                      ->where('customers.id', $id)
                                      ->get()->getRowArray();
            
            if (!$customer) {
                return $this->response->setJSON(['success' => false, 'message' => 'Customer not found']);
            }
            
            // Get customer locations
            $locations = $this->locationModel->where('customer_id', $id)
                                           ->orderBy('is_primary', 'DESC')
                                           ->orderBy('location_name', 'ASC')
                                           ->findAll();
            
            // Get customer contracts with units summary
            $contractsBuilder = $this->db->table('kontrak k');
            $contracts = $contractsBuilder->select('k.*, cl.location_name, 
                                                   COUNT(iu.id_inventory_unit) as active_units,
                                                   SUM(CASE WHEN iu.workflow_status IS NOT NULL THEN 1 ELSE 0 END) as workflow_units')
                                        ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
                                        ->join('inventory_unit iu', 'k.id = iu.kontrak_id', 'left')
                                        ->where('cl.customer_id', $id)
                                        ->groupBy('k.id')
                                        ->orderBy('k.tanggal_mulai', 'DESC')
                                        ->get()->getResultArray();
            
            // Get customer units with details
            $unitsBuilder = $this->db->table('inventory_unit iu');
            $units = $unitsBuilder->select('iu.*, k.no_kontrak, cl.location_name as contract_location,
                                          su.status_unit, tu.tipe as nama_tipe, mu.model_unit as nama_model,
                                          iu.workflow_status, iu.lokasi_unit')
                                ->join('kontrak k', 'iu.kontrak_id = k.id', 'left')
                                ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
                                ->join('status_unit su', 'iu.status_unit_id = su.id_status', 'left')
                                ->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left')
                                ->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left')
                                ->where('cl.customer_id', $id)
                                ->orderBy('k.tanggal_mulai', 'DESC')
                                ->orderBy('iu.no_unit', 'ASC')
                                ->get()->getResultArray();
            
            // Get activity history (work orders through kontrak relationship)
            $activityBuilder = $this->db->table('spk s');
            $activities = $activityBuilder->select('s.*, "Work Order" as activity_type')
                                        ->join('kontrak k', 's.kontrak_id = k.id', 'left')
                                        ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
                                        ->where('cl.customer_id', $id)
                                        ->orderBy('s.dibuat_pada', 'DESC')
                                        ->limit(20)
                                        ->get()->getResultArray();
            
            // Add delivery instructions to activities
            $deliveryBuilder = $this->db->table('delivery_instructions di');
            $deliveries = $deliveryBuilder->select('di.*, s.pelanggan, "Delivery" as activity_type')
                                        ->join('spk s', 'di.spk_id = s.id', 'left')
                                        ->join('kontrak k', 's.kontrak_id = k.id', 'left')
                                        ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
                                        ->where('cl.customer_id', $id)
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
                'primary_locations' => count(array_filter($locations, function($loc) { return $loc['is_primary'] == 1; })),
                'total_contracts' => count($contracts),
                'active_contracts' => count(array_filter($contracts, function($c) { return $c['status'] == 'Aktif'; })),
                'total_units' => count($units),
                'active_units' => count(array_filter($units, function($u) { return in_array($u['status_unit'], ['DISEWA', 'BEROPERASI']); })),
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
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading customer details: ' . $e->getMessage()
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
        
        $rules = [
            'customer_code' => "required|max_length[20]|is_unique[customers.customer_code,id,$id]",
            'customer_name' => 'required|max_length[255]',
            'area_id' => 'required|integer',
            'address' => 'permit_empty|max_length[500]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'max_length[100]',
            'contact_person' => 'permit_empty|max_length[255]'
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
        
        $data = [
            'customer_code' => $this->request->getPost('customer_code'),
            'customer_name' => $this->request->getPost('customer_name'),
            'area_id' => $this->request->getPost('area_id'),
            'address' => $this->request->getPost('address'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'contact_person' => $this->request->getPost('contact_person'),
            'description' => $this->request->getPost('description')
        ];
        
        try {
            $updated = $this->customerModel->update($id, $data);
            
            if ($updated) {
                // Activity Log: UPDATE customer (diff only)
                $this->logUpdate('customers', (int)$id, $customer, $data, [
                    'description' => 'Customer updated from Customer Management',
                    'relations' => $this->buildRelations('customers', (int)$id),
                    'module_name' => 'MARKETING',
                    'submenu_item' => 'Customer Management',
                    'business_impact' => 'MEDIUM'
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer updated successfully'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating customer: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update customer'
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
     * Delete customer (main method)
     */
    public function deleteCustomer($id)
    {
        if (!$this->hasPermission('marketing.customer.delete')) {
            return $this->response->setJSON(['success'=>false,'message'=>'Unauthorized'])->setStatusCode(403);
        }
        try {
            // Check if customer has contracts through customer_locations (updated for optimized structure)
            $contractsCount = $this->db->table('kontrak k')
                ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'inner')
                ->where('cl.customer_id', $id)
                ->countAllResults();
            
            if ($contractsCount > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete customer with active contracts. Please remove contracts first.'
                ]);
            }
            
            // Delete customer locations first
            $this->locationModel->where('customer_id', $id)->delete();
            
            // Delete customer
            $deleted = $this->customerModel->delete($id);
            
            if ($deleted) {
                // Activity Log: DELETE customer (with snapshot)
                $this->logDelete('customers', (int)$id, $this->request->getVar() ? $this->request->getVar() : ['id' => (int)$id], [
                    'description' => 'Customer deleted from Customer Management',
                    'relations' => $this->buildRelations('customers', (int)$id),
                    'module_name' => 'MARKETING',
                    'submenu_item' => 'Customer Management',
                    'business_impact' => 'HIGH',
                    'is_critical' => 1
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer deleted successfully'
                ]);
            }
            
        } catch (\Exception $e) {
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
            $tipeUnit = $this->db->table('tipe_unit')->get()->getResultArray();
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
            // Get total customers
            $totalCustomers = $this->customerModel->countAllResults();
            
            // Get active customers
            $activeCustomers = $this->customerModel->where('is_active', 1)->countAllResults();
            
            // Get total contracts
            $totalContracts = $this->db->table('kontrak')->countAllResults();
            
            // Get total units
            $totalUnits = $this->db->table('inventory_unit')->countAllResults();
            
            // Get multi-location customers
            $multiLocationCustomers = $this->db->table('customers c')
                ->select('c.id')
                ->join('customer_locations cl', 'c.id = cl.customer_id', 'left')
                ->where('cl.is_active', 1)
                ->groupBy('c.id')
                ->having('COUNT(cl.id) > 1')
                ->countAllResults();
            
            $stats = [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'total_contracts' => $totalContracts,
                'total_units' => $totalUnits,
                'multi_location_customers' => $multiLocationCustomers
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'CustomerManagementController::getCustomerStats - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load statistics'
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

            // Get contracts for this customer using same query as kontrak.php
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
                WHERE c.id = ?
                ORDER BY k.id DESC
            ";
            
            $result = $this->db->query($query, [$customerId]);
            $contracts = $result->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $contracts
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
            // Use direct database update for reliable location_code updates
            $builder = $this->db->table('customer_locations');
            $builder->where('id', (int)$id);
            $ok = $builder->update($data);
            
            if (!$ok) {
                return $this->response->setJSON(['success'=>false,'message'=>'Failed to update location']);
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

            // Get customer contracts with locations
            $contracts = $this->db->table('kontrak k')
                ->select('k.*, cl.location_name, cl.address as location_address')
                ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
                ->where('k.customer_location_id IN (SELECT id FROM customer_locations WHERE customer_id = ' . $customerId . ')')
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
                    ->join('kontrak k', 'k.id = iu.kontrak_id', 'left')
                    ->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left')
                    ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
                    ->join('kapasitas kap', 'kap.id_kapasitas = iu.kapasitas_unit_id', 'left')
                    ->join('tipe_mast tm', 'tm.id_mast = iu.model_mast_id', 'left')
                    ->join('jenis_roda jr', 'jr.id_roda = iu.roda_id', 'left')
                    ->join('tipe_ban tb', 'tb.id_ban = iu.ban_id', 'left')
                    ->join('valve v', 'v.id_valve = iu.valve_id', 'left')
                    ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                    ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                    ->where('iu.kontrak_id', $contract['id'])
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


}