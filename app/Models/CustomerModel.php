<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'customer_code',
        'customer_name',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'customer_code' => 'required|max_length[20]|is_unique[customers.customer_code,id,{id}]',
        'customer_name' => 'required|max_length[255]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'customer_code' => [
            'required' => 'Kode customer harus diisi',
            'is_unique' => 'Kode customer sudah digunakan',
            'max_length' => 'Kode customer maksimal 20 karakter'
        ],
        'customer_name' => [
            'required' => 'Nama customer harus diisi',
            'max_length' => 'Nama customer maksimal 255 karakter'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get active customers
     */
    public function getActiveCustomers()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Check if customer profile is complete
     */
    public function isCustomerProfileComplete($customerId)
    {
        // Get customer basic data
        $customer = $this->find($customerId);
        if (!$customer) {
            return false;
        }

        // Check if customer has basic required info
        if (empty($customer['customer_name']) || $customer['customer_name'] == 'Unknown Customer') {
            return false;
        }

        // Check if customer has at least one complete location
        $locationModel = new \App\Models\CustomerLocationModel();
        $locations = $locationModel->where('customer_id', $customerId)
                                  ->where('is_active', 1)
                                  ->findAll();

        if (empty($locations)) {
            return false;
        }

        // Check if at least one location has complete data
        foreach ($locations as $location) {
            $isComplete = true;
            
            // Required fields check
            $requiredFields = ['address', 'city', 'province', 'contact_person'];
            foreach ($requiredFields as $field) {
                if (empty($location[$field]) || 
                    $location[$field] == 'Alamat belum ditentukan' ||
                    $location[$field] == 'Kota belum ditentukan' ||
                    $location[$field] == 'Provinsi belum ditentukan' ||
                    $location[$field] == 'Contact belum ditentukan') {
                    $isComplete = false;
                    break;
                }
            }
            
            if ($isComplete) {
                return true; // At least one complete location found
            }
        }

        return false; // No complete location found
    }

    /**
     * Get customer profile completion status
     */
    public function getCustomerProfileStatus($customerId)
    {
        $customer = $this->find($customerId);
        if (!$customer) {
            return [
                'exists' => false,
                'complete' => false,
                'has_location' => false,
                'message' => 'Customer not found'
            ];
        }

        $isComplete = $this->isCustomerProfileComplete($customerId);
        
        // Check if customer has any locations
        $locationModel = new \App\Models\CustomerLocationModel();
        $hasLocation = $locationModel->where('customer_id', $customerId)
                                   ->where('is_active', 1)
                                   ->countAllResults() > 0;
        
        return [
            'exists' => true,
            'complete' => $isComplete,
            'has_location' => $hasLocation,
            'customer_name' => $customer['customer_name'],
            'message' => $isComplete ? 'Customer profile is complete' : 'Customer profile needs completion'
        ];
    }

    /**
     * Get customers with area information (legacy method)
     */
    public function getCustomersWithArea($customerId = null)
    {
        $builder = $this->select('customers.*, areas.area_name, areas.area_code')
                       ->join('customer_locations cl', 'customers.id = cl.customer_id AND cl.is_primary = 1', 'left')
                       ->join('areas', 'areas.id = cl.area_id', 'left')
                       ->where('customers.is_active', 1);
                       
        if ($customerId) {
            $builder->where('customers.id', $customerId);
            return $builder->first();
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get customers by area
     */
    public function getCustomersByArea($areaId)
    {
        return $this->select('customers.*, areas.area_name')
                   ->join('customer_locations cl', 'customers.id = cl.customer_id AND cl.is_primary = 1', 'left')
                   ->join('areas', 'areas.id = cl.area_id', 'left')
                   ->where('cl.area_id', $areaId)
                   ->where('customers.is_active', 1)
                   ->findAll();
    }
    
    /**
     * Get customer with complete info (locations, contracts, etc)
     */
    public function getCustomerComplete($customerId)
    {
        // Get customer with area
        $customer = $this->getCustomersWithArea($customerId);
        
        if (!$customer) {
            return null;
        }
        
        // Get customer locations
        $locationModel = new CustomerLocationModel();
        $customer['locations'] = $locationModel->getLocationsByCustomer($customerId);
        
        // Get customer contracts
        $contractModel = new CustomerContractModel();
        $customer['contracts'] = $contractModel->getContractsByCustomer($customerId);
        
        // Get inventory units
        $inventoryModel = new \App\Models\InventoryUnitModel();
        $customer['units'] = $inventoryModel->where('customer_id', $customerId)->findAll();
        
        return $customer;
    }
    
    /**
     * Search customers
     */
    public function searchCustomers($search = '', $areaId = null, $contractType = null)
    {
        $builder = $this->select('customers.*, areas.area_name, areas.area_code')
                       ->join('customer_locations cl', 'customers.id = cl.customer_id AND cl.is_primary = 1', 'left')
                       ->join('areas', 'areas.id = cl.area_id', 'left')
                       ->where('customers.is_active', 1);
                       
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('customers.customer_name', $search)
                   ->orLike('customers.customer_code', $search)
                   ->orLike('customers.primary_address', $search)
                   ->orLike('customers.pic_name', $search)
                   ->groupEnd();
        }
        
        if ($areaId) {
            $builder->where('cl.area_id', $areaId);
        }
        
        if ($contractType) {
            $builder->where('customers.contract_type', $contractType);
        }
        
        return $builder->orderBy('customers.customer_name')->findAll();
    }
    
    /**
     * Get customers for dropdown
     */
    public function getCustomersForDropdown($areaId = null)
    {
        $builder = $this->select('customers.id, customers.customer_name, customers.customer_code')
                       ->where('customers.is_active', 1);
                       
        if ($areaId) {
            $builder->where('cl.area_id', $areaId);
        }
        
        $customers = $builder->orderBy('customers.customer_name')->findAll();
        
        $options = [];
        foreach ($customers as $customer) {
            $options[$customer['id']] = $customer['customer_name'] . ' (' . $customer['customer_code'] . ')';
        }
        
        return $options;
    }
    
    /**
     * Generate next customer code
     */
    public function generateCustomerCode($prefix = 'CUST')
    {
        $lastCustomer = $this->select('customer_code')
                           ->like('customer_code', $prefix)
                           ->orderBy('id', 'DESC')
                           ->first();
                           
        if ($lastCustomer) {
            $lastNumber = (int)substr($lastCustomer['customer_code'], strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}