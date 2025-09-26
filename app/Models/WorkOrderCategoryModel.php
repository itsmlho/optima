<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderCategoryModel extends Model
{
    protected $table            = 'work_order_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_name',
        'category_code',
        'description',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all active categories
     */
    public function getActiveCategories()
    {
        return $this->where('is_active', 1)
                   ->orderBy('category_name', 'ASC')
                   ->findAll();
    }

    /**
     * Get category by code
     */
    public function getByCode($code)
    {
        return $this->where('category_code', $code)
                   ->where('is_active', 1)
                   ->first();
    }

    /**
     * Get category options for dropdown
     */
    public function getCategoryOptions()
    {
        $categories = $this->getActiveCategories();
        $options = [];
        
        foreach ($categories as $category) {
            $options[$category['id']] = $category['category_name'];
        }
        
        return $options;
    }
}