<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/**
 * Customers API Controller
 * 
 * Provides API endpoints for customer data access used by other modules
 * (Marketing, Quotations, Contracts). These endpoints are called via AJAX
 * for customer/location/contract lookups and management.
 * 
 * For customer management UI and CRUD operations, see CustomerManagementController.
 * 
 * Routes:
 * - GET  customers/get/:id - Get customer by ID
 * - GET  customers/getLocations/:id - Get customer locations
 * - GET  customers/getContracts/:id - Get customer contracts
 * - POST customers/saveLocation - Save new location
 * - POST customers/setPrimaryLocation - Set primary location
 * - POST customers/searchContract - Search contract by number/PO
 * 
 * @package App\Controllers
 */
class Customers extends BaseController
{
    protected $customerModel;
    protected $customerLocationModel;

    public function __construct()
    {
        $this->customerModel = new \App\Models\CustomerModel();
        $this->customerLocationModel = new \App\Models\CustomerLocationModel();
    }

    /**
     * Get customer by ID
     */
    public function get($customerId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/');
        }

        try {
            $customer = $this->customerModel->find($customerId);

            if (!$customer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $customer
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Customers::get - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get customer: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get customer locations
     */
    public function getLocations($customerId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/');
        }

        try {
            $locations = $this->customerLocationModel
                ->where('customer_id', $customerId)
                ->where('is_active', 1)
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $locations
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Customers::getLocations - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get customer locations: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save new customer location
     */
    public function saveLocation()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        
        try {
            $data = $this->request->getJSON(true);
            
            // Validate required fields
            if (empty($data['customer_id']) || empty($data['location_name']) || 
                empty($data['address']) || empty($data['city']) || 
                empty($data['province']) || empty($data['contact_person'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'All required fields must be filled'
                ]);
            }

            // If this is marked as primary, unset other primary locations
            if (!empty($data['is_primary']) && $data['is_primary'] == 1) {
                $db->table('customer_locations')
                    ->where('customer_id', $data['customer_id'])
                    ->update(['is_primary' => 0]);
            }

            // Add timestamps
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            // Insert new location
            $locationId = $this->customerLocationModel->insert($data);

            if ($locationId) {
                // If this was triggered from quotation workflow, update quotation status
                if (!empty($data['quotation_id'])) {
                    log_message('debug', 'Quotation ID provided: ' . $data['quotation_id']);
                    
                    // Only mark as complete if workflow_completed flag is true
                    if (!empty($data['workflow_completed'])) {
                        $quotationModel = new \App\Models\QuotationModel();
                        $quotation = $quotationModel->find($data['quotation_id']);
                        
                        log_message('debug', 'Quotation found: ' . json_encode($quotation));
                        
                        if ($quotation && $quotation['workflow_stage'] === 'DEAL') {
                            log_message('debug', 'Updating quotation workflow status...');
                            
                            // Mark that customer location is now complete using Query Builder
                            $result = $db->table('quotations')
                                ->where('id_quotation', $data['quotation_id'])
                                ->update([
                                    'customer_location_complete' => 1,
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            
                            log_message('info', 'Quotation #' . $data['quotation_id'] . ' - Customer location marked as complete. Rows affected: ' . $result);
                        } else {
                            log_message('debug', 'Quotation not in DEAL stage or not found. Stage: ' . ($quotation['workflow_stage'] ?? 'N/A'));
                        }
                    } else {
                        log_message('debug', 'Workflow not completed - flag not set');
                    }
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer location saved successfully',
                    'data' => [
                        'location_id' => $locationId
                    ]
                ]);
            } else {
                $errors = $this->customerLocationModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to save location: ' . implode(', ', $errors)
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Customers::saveLocation - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save customer location: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Set primary location for customer
     */
    public function setPrimaryLocation()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        
        try {
            $data = $this->request->getJSON(true);
            
            if (empty($data['customer_id']) || empty($data['location_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer ID and Location ID are required'
                ]);
            }

            // Verify location exists and belongs to customer
            $location = $this->customerLocationModel->find($data['location_id']);
            if (!$location) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Location not found'
                ]);
            }
            
            if ($location['customer_id'] != $data['customer_id']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Location does not belong to this customer'
                ]);
            }

            // First, unset all primary flags for this customer using Query Builder
            $db->table('customer_locations')
                ->where('customer_id', $data['customer_id'])
                ->update(['is_primary' => 0]);

            // Set the selected location as primary using Query Builder
            $db->table('customer_locations')
                ->where('id', $data['location_id'])
                ->update(['is_primary' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

            // Success - operation is idempotent
            
            // If this was triggered from quotation workflow, update quotation status
            if (!empty($data['quotation_id'])) {
                // Only mark as complete if workflow_completed flag is true
                if (!empty($data['workflow_completed'])) {
                    $quotationModel = new \App\Models\QuotationModel();
                    $quotation = $quotationModel->find($data['quotation_id']);
                    
                    if ($quotation && $quotation['workflow_stage'] === 'DEAL') {
                        // Mark that customer location is now complete using Query Builder
                        $db->table('quotations')
                            ->where('id_quotation', $data['quotation_id'])
                            ->update([
                                'customer_location_complete' => 1,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        
                        log_message('info', 'Quotation #' . $data['quotation_id'] . ' - Customer location marked as complete via primary location');
                    }
                } else {
                    log_message('debug', 'Workflow not completed - flag not set');
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Primary location updated successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Customers::setPrimaryLocation - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to set primary location: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show customer detail page with contracts list
     */
    public function detail($customerId)
    {
        if (!$customerId || $customerId == '0') {
            return redirect()->to('marketing/kontrak')->with('error', 'ID customer tidak valid.');
        }

        $customer = $this->customerModel->find($customerId);

        if (!$customer) {
            return redirect()->to('marketing/kontrak')->with('error', 'Customer tidak ditemukan.');
        }

        // Get customer locations
        $locations = $this->customerLocationModel->where('customer_id', $customerId)
            ->where('is_active', 1)
            ->findAll();

        $data = [
            'title' => 'Customer Detail — ' . ($customer['customer_name'] ?? 'Customer'),
            'customer' => $customer,
            'locations' => $locations,
        ];

        return view('marketing/customer_detail', $data);
    }

    /**
     * Get customer contracts
     */
    public function getContracts($customerId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/');
        }

        try {
            $db = \Config\Database::connect();

            // Get contracts with location information and unit counts
            // Join through customers table since kontrak has direct customer_id
            $contracts = $db->table('kontrak k')
                ->select('k.*, (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as location_name, (SELECT cl.address FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as address, (SELECT cl.city FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as city, (SELECT cl.province FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as province, c.id as customer_id, c.customer_name, (SELECT ku.customer_location_id FROM kontrak_unit ku WHERE ku.kontrak_id = k.id LIMIT 1) as location_id')
                ->join('customers c', 'k.customer_id = c.id', 'left')
                ->where('k.customer_id', $customerId)
                ->orderBy('k.dibuat_pada', 'DESC')
                ->get()
                ->getResultArray();

            // Add unit count and nilai_total for each contract
            foreach ($contracts as &$kontrak) {
                $kontrakId = $kontrak['id'];

                // Get unit count from kontrak_unit
                $unitCount = $db->table('kontrak_unit')
                    ->where('kontrak_id', $kontrakId)
                    ->where('status', 'AKTIF')
                    ->countAllResults();

                // Get nilai total from inventory_unit through kontrak_unit
                $nilaiQuery = "
                    SELECT COALESCE(SUM(iu.harga_sewa_bulanan), 0) as total
                    FROM kontrak_unit ku
                    JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
                    WHERE ku.kontrak_id = ? AND ku.status = 'AKTIF'
                ";
                $nilaiResult = $db->query($nilaiQuery, [$kontrakId])->getRow();

                $kontrak['total_units'] = $unitCount;
                $kontrak['nilai_total'] = $nilaiResult->total ?? 0;
            }

            return $this->response->setJSON([
                'success' => true,
                'contracts' => $contracts
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Customers::getContracts - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get customer contracts: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Search contract by contract number or PO number
     */
    public function searchContract()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/');
        }

        try {
            $customerId = $this->request->getPost('customer_id');
            $field = $this->request->getPost('field'); // 'no_kontrak' or 'customer_po_number'
            $value = $this->request->getPost('value');

            if (!$customerId || !$field || !$value) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ]);
            }

            $db = \Config\Database::connect();
            
            // Search for contract by specified field
            $contract = $db->table('kontrak k')
                ->select('k.*, (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as location_name, (SELECT cl.address FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as address, (SELECT cl.city FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as city, (SELECT cl.province FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as province, c.id as customer_id, c.customer_name, (SELECT ku.customer_location_id FROM kontrak_unit ku WHERE ku.kontrak_id = k.id LIMIT 1) as location_id')
                ->join('customers c', 'k.customer_id = c.id', 'left')
                ->where('k.customer_id', $customerId)
                ->where("k.$field", $value)
                ->where('cl.is_active', 1)
                ->orderBy('k.dibuat_pada', 'DESC')
                ->get()
                ->getRowArray();

            if ($contract) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $contract
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contract not found'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Customers::searchContract - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to search contract: ' . $e->getMessage()
            ]);
        }
    }
}
