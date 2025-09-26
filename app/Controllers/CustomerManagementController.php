<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CustomerModel;
use App\Models\CustomerLocationModel;
use App\Models\AreaModel;
use App\Models\CustomerContractModel;

class CustomerManagementController extends Controller
{
    protected $customerModel;
    protected $locationModel;
    protected $areaModel;
    protected $contractModel;
    
    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->locationModel = new CustomerLocationModel();
        $this->areaModel = new AreaModel();
        $this->contractModel = new CustomerContractModel();
    }

    /**
     * Display customer management dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Customer Management - Marketing',
            'customers' => $this->customerModel->findAll(),
            'areas' => $this->areaModel->findAll(),
            'totalCustomers' => $this->customerModel->countAllResults(),
            'totalLocations' => $this->locationModel->countAllResults(),
            'customersByArea' => $this->getCustomersByAreaStats()
        ];
        
        return view('marketing/customer_management', $data);
    }

    /**
     * Get customers with pagination and search
     */
    public function getCustomers()
    {
        $request = $this->request;
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        $orderColumnIndex = $request->getPost('order')[0]['column'];
        $orderDir = $request->getPost('order')[0]['dir'];
        
        // Define columns for ordering
        $columns = ['customer_code', 'customer_name', 'area_name', 'pic_phone', 'pic_email', 'created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'customer_name';
        
        // Get total records
        $totalRecords = $this->customerModel->countAllResults();
        
        // Build query with search
        $builder = $this->customerModel->builder();
        $builder->select('customers.*, areas.area_name, areas.area_code')
                ->join('areas', 'customers.area_id = areas.id', 'left');
                
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('customer_code', $searchValue)
                    ->orLike('customer_name', $searchValue)
                    ->orLike('areas.area_name', $searchValue)
                    ->orLike('pic_phone', $searchValue)
                    ->orLike('pic_email', $searchValue)
                    ->groupEnd();
        }
        
        $filteredRecords = $builder->countAllResults(false);
        
        // Apply ordering and pagination
        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);
        
        $customers = $builder->get()->getResultArray();
        
        // Add additional data for enhanced table display
        foreach ($customers as &$customer) {
            $customer['locations_count'] = $this->locationModel->where('customer_id', $customer['id'])->countAllResults();
            $customer['contracts_count'] = $this->contractModel->where('customer_id', $customer['id'])->countAllResults();
            
            // Get total units from kontrak table through customer_contracts relationship
            $contractsBuilder = $this->contractModel->builder();
            $contractsBuilder->select('SUM(kontrak.total_units) as total_units')
                           ->join('kontrak', 'kontrak.id = customer_contracts.kontrak_id', 'inner')
                           ->where('customer_contracts.customer_id', $customer['id']);
            $contractUnits = $contractsBuilder->get()->getRowArray();
            $customer['total_units'] = $contractUnits['total_units'] ?? 0;
            
            // Get PO count for this customer (contracts that have PO numbers)
            $poBuilder = $this->contractModel->builder();
            $customer['po_count'] = $poBuilder->join('kontrak', 'kontrak.id = customer_contracts.kontrak_id', 'inner')
                                             ->where('customer_contracts.customer_id', $customer['id'])
                                             ->where('kontrak.no_po_marketing !=', '')
                                             ->where('kontrak.no_po_marketing IS NOT NULL', null, false)
                                             ->countAllResults();
            
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
    }

    /**
     * Show customer details
     */
    public function show($id)
    {
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
        return $this->show($id);
    }

    /**
     * Create new customer
     */
    public function store()
    {
        $rules = [
            'customer_code' => 'required|is_unique[customers.customer_code]|max_length[20]',
            'customer_name' => 'required|max_length[255]',
            'area_id' => 'required|integer',
            'address' => 'permit_empty|max_length[500]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[100]',
            'contact_person' => 'permit_empty|max_length[255]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
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
            $customerId = $this->customerModel->insert($data);
            
            if ($customerId) {
                // Create primary location if address provided
                if (!empty($data['address'])) {
                    $locationData = [
                        'customer_id' => $customerId,
                        'location_name' => 'Primary Office',
                        'address' => $data['address'],
                        'is_primary' => 1,
                        'contact_person' => $data['contact_person'],
                        'phone' => $data['phone']
                    ];
                    $this->locationModel->insert($locationData);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer created successfully',
                    'data' => ['id' => $customerId]
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating customer: ' . $e->getMessage()
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create customer'
        ]);
    }

    /**
     * Update customer
     */
    public function update($id)
    {
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
            'email' => 'permit_empty|valid_email|max_length[100]',
            'contact_person' => 'permit_empty|max_length[255]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
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
        try {
            // Check if customer has contracts
            $contractsCount = $this->contractModel->where('customer_id', $id)->countAllResults();
            
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
        $rules = [
            'customer_id' => 'required|integer',
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
            'customer_id' => $this->request->getPost('customer_id'),
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
                $this->locationModel->where('customer_id', $data['customer_id'])
                                   ->set('is_primary', 0)
                                   ->update();
            }
            
            $locationId = $this->locationModel->insert($data);
            
            if ($locationId) {
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
     * Get customer statistics by area
     */
    private function getCustomersByAreaStats()
    {
        $builder = $this->customerModel->builder();
        $builder->select('areas.area_name, COUNT(customers.id) as customer_count')
                ->join('areas', 'customers.area_id = areas.id', 'left')
                ->groupBy('areas.id, areas.area_name')
                ->orderBy('customer_count', 'DESC');
                
        return $builder->get()->getResultArray();
    }
}