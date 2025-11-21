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
        'area_id',
        'primary_address',
        'secondary_address',
        'city',
        'province',
        'postal_code',
        'pic_name',
        'pic_phone',
        'pic_email',
        'contract_type',
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
        'area_id' => 'required|integer|is_not_unique[areas.id]',
        'primary_address' => 'permit_empty|max_length[500]',
        'city' => 'permit_empty|max_length[100]',
        'province' => 'permit_empty|max_length[100]',
        'pic_name' => 'permit_empty|max_length[100]',
        'pic_phone' => 'permit_empty|max_length[20]',
        'pic_email' => 'permit_empty|valid_email|max_length[100]',
        'contract_type' => 'permit_empty|in_list[RENTAL_HARIAN,RENTAL_BULANAN,JUAL]',
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
        ],
        'area_id' => [
            'required' => 'Area harus dipilih',
            'integer' => 'Area harus berupa angka',
            'is_not_unique' => 'Area tidak valid'
        ],
        'primary_address' => [
            'required' => 'Alamat utama harus diisi'
        ],
        'city' => [
            'required' => 'Kota harus diisi',
            'max_length' => 'Nama kota maksimal 100 karakter'
        ],
        'province' => [
            'required' => 'Provinsi harus diisi',
            'max_length' => 'Nama provinsi maksimal 100 karakter'
        ],
        'pic_email' => [
            'valid_email' => 'Format email tidak valid'
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
     * Get customers with area information
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