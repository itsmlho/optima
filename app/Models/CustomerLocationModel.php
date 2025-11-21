<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerLocationModel extends Model
{
    protected $table = 'customer_locations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'customer_id',
        'location_name',
        'address',
        'city',
        'province', 
        'postal_code',
        'gps_latitude',
        'gps_longitude',
        'contact_person',
        'phone',
        'email',
        'pic_position',
        'notes',
        'is_primary',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'customer_id' => 'required|integer|is_not_unique[customers.id]',
        'location_name' => 'required|max_length[100]',
        'address' => 'required',
        'city' => 'required|max_length[100]',
        'province' => 'required|max_length[100]',
        'postal_code' => 'permit_empty|max_length[10]',
        'email' => 'max_length[128]',
        'gps_latitude' => 'permit_empty|decimal',
        'gps_longitude' => 'permit_empty|decimal',
        'is_primary' => 'permit_empty|in_list[0,1]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'customer_id' => [
            'required' => 'Customer harus dipilih',
            'integer' => 'Customer harus berupa angka',
            'is_not_unique' => 'Customer tidak valid'
        ],
        'location_name' => [
            'required' => 'Nama lokasi harus diisi',
            'max_length' => 'Nama lokasi maksimal 100 karakter'
        ],
        'address' => [
            'required' => 'Alamat harus diisi'
        ],
        'city' => [
            'required' => 'Kota harus diisi',
            'max_length' => 'Nama kota maksimal 100 karakter'
        ],
        'province' => [
            'required' => 'Provinsi harus diisi',
            'max_length' => 'Nama provinsi maksimal 100 karakter'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    protected $allowCallbacks = true;
    protected $beforeInsert = ['ensureSinglePrimary'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['ensureSinglePrimary'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get locations by customer
     */
    public function getLocationsByCustomer($customerId)
    {
        return $this->where('customer_id', $customerId)
                   ->where('is_active', 1)
                   ->orderBy('is_primary', 'DESC')
                   ->orderBy('location_name')
                   ->findAll();
    }
    
    /**
     * Get primary location by customer
     */
    public function getPrimaryLocation($customerId)
    {
        return $this->where('customer_id', $customerId)
                   ->where('is_primary', 1)
                   ->where('is_active', 1)
                   ->first();
    }
    
    /**
     * Get locations for dropdown
     */
    public function getLocationsForDropdown($customerId)
    {
        $locations = $this->getLocationsByCustomer($customerId);
        
        $options = [];
        foreach ($locations as $location) {
            $label = $location['location_name'];
            if ($location['is_primary']) {
                $label .= ' (Utama)';
            }
            $options[$location['id']] = $label;
        }
        
        return $options;
    }
    
    /**
     * Ensure only one primary location per customer
     */
    protected function ensureSinglePrimary(array $data)
    {
        if (isset($data['data']['is_primary']) && $data['data']['is_primary'] == 1) {
            $customerId = $data['data']['customer_id'] ?? null;
            
            if ($customerId) {
                // Remove primary flag from other locations of same customer
                $builder = $this->builder();
                $builder->where('customer_id', $customerId)
                       ->where('is_primary', 1);
                       
                // If updating, exclude current record
                if (isset($data['id']) && is_array($data['id']) && !empty($data['id'])) {
                    $builder->whereNotIn('id', $data['id']);
                }
                
                $builder->set('is_primary', 0)->update();
            }
        }
        
        return $data;
    }
    
    /**
     * Get locations with customer info
     */
    public function getLocationsWithCustomer($locationId = null)
    {
        $builder = $this->select('customer_locations.*, customers.customer_name, customers.customer_code, areas.area_name')
                       ->join('customers', 'customers.id = customer_locations.customer_id')
                       ->join('areas', 'areas.id = customer_locations.area_id')
                       ->where('customer_locations.is_active', 1);
                       
        if ($locationId) {
            $builder->where('customer_locations.id', $locationId);
            return $builder->first();
        }
        
        return $builder->orderBy('customers.customer_name')
                      ->orderBy('customer_locations.is_primary', 'DESC')
                      ->findAll();
    }
    
    /**
     * Search locations
     */
    public function searchLocations($search = '', $customerId = null, $areaId = null)
    {
        $builder = $this->select('customer_locations.*, customers.customer_name, customers.customer_code, areas.area_name')
                       ->join('customers', 'customers.id = customer_locations.customer_id')
                       ->join('areas', 'areas.id = customer_locations.area_id')
                       ->where('customer_locations.is_active', 1);
                       
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('customer_locations.location_name', $search)
                   ->orLike('customer_locations.address', $search)
                   ->orLike('customer_locations.city', $search)
                   ->orLike('customers.customer_name', $search)
                   ->groupEnd();
        }
        
        if ($customerId) {
            $builder->where('customer_locations.customer_id', $customerId);
        }
        
        if ($areaId) {
            $builder->where('customer_locations.area_id', $areaId);
        }
        
        return $builder->orderBy('customers.customer_name')
                      ->orderBy('customer_locations.is_primary', 'DESC')
                      ->findAll();
    }
}