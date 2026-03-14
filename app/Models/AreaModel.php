<?php

namespace App\Models;

use CodeIgniter\Model;

class AreaModel extends Model
{
    protected $table = 'areas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true; // Aktivkan softDeletes setelah kolom deleted_at ditambahkan
    protected $protectFields = true;
    
    protected $allowedFields = [
        'area_code', 
        'area_name', 
        'area_description',
        'area_type',  // NEW: CENTRAL or BRANCH
        'departemen_id', // DEPRECATED: Keep for reference only
        'area_coordinates', 
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'area_code' => 'required|max_length[10]|is_unique[areas.area_code,id,{id}]',
        'area_name' => 'required|max_length[100]',
        'area_description' => 'permit_empty|string',
        'area_type' => 'permit_empty|in_list[CENTRAL,BRANCH]',
        'departemen_id' => 'permit_empty|integer',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'area_code' => [
            'required' => 'Kode area harus diisi',
            'is_unique' => 'Kode area sudah digunakan',
            'max_length' => 'Kode area maksimal 10 karakter'
        ],
        'area_name' => [
            'required' => 'Nama area harus diisi',
            'max_length' => 'Nama area maksimal 100 karakter'
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
     * Get all active areas
     */
    public function getActiveAreas()
    {
        return $this->where('is_active', 1)->findAll();
    }
    
    /**
     * Get active areas by type
     */
    public function getAreasByType($type = null)
    {
        $builder = $this->where('is_active', 1);
        
        if ($type && in_array($type, ['CENTRAL', 'BRANCH'])) {
            $builder->where('area_type', $type);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get central HQ areas
     */
    public function getCentralAreas()
    {
        return $this->getAreasByType('CENTRAL');
    }
    
    /**
     * Get branch areas
     */
    public function getBranchAreas()
    {
        return $this->getAreasByType('BRANCH');
    }
    
    /**
     * Get area by code
     */
    public function getByCode($code)
    {
        return $this->where('area_code', $code)->first();
    }
    
    /**
     * Get areas with customer count
     */
    public function getAreasWithStats()
    {
        return $this->select('areas.*, COUNT(DISTINCT customers.id) as customer_count')
                   ->join('customer_locations cl', 'cl.area_id = areas.id', 'left')
                   ->join('customers', 'customers.id = cl.customer_id AND customers.is_active = 1', 'left')
                   ->where('areas.is_active', 1)
                   ->groupBy('areas.id')
                   ->findAll();
    }
    
    /**
     * Get area staff summary
     */
    public function getAreaStaffSummary($areaId = null)
    {
        $builder = $this->db->table('vw_area_staff_summary');
        
        if ($areaId) {
            $builder->where('area_id', $areaId);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get areas for dropdown/select options
     */
    public function getAreasForDropdown()
    {
        $areas = $this->getActiveAreas();
        $options = [];
        
        foreach ($areas as $area) {
            $options[$area['id']] = $area['area_name'] . ' (' . $area['area_code'] . ')';
        }
        
        return $options;
    }
}