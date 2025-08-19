<?php

namespace App\Models;

use CodeIgniter\Model;

class DivisionModel extends Model
{
    protected $table = 'divisions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'name', 'code', 'description', 'parent_id', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]|is_unique[divisions.name,id,{id}]',
        'code' => 'required|min_length[2]|max_length[10]|is_unique[divisions.code,id,{id}]',
        'description' => 'permit_empty|max_length[500]',
        'parent_id' => 'permit_empty|is_natural_no_zero',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama divisi harus diisi',
            'is_unique' => 'Nama divisi sudah ada'
        ],
        'code' => [
            'required' => 'Kode divisi harus diisi',
            'is_unique' => 'Kode divisi sudah ada'
        ]
    ];

    /**
     * Get all active divisions
     */
    public function getActiveDivisions()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get divisions with hierarchy
     */
    public function getDivisionsHierarchy()
    {
        $divisions = $this->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->findAll();
        return $this->buildHierarchy($divisions);
    }

    /**
     * Get child divisions
     */
    public function getChildDivisions($parentId)
    {
        return $this->where('parent_id', $parentId)->where('is_active', 1)->findAll();
    }

    /**
     * Get division with users count
     */
    public function getDivisionsWithUserCount()
    {
        $builder = $this->db->table($this->table);
        $builder->select('divisions.*, COUNT(user_divisions.user_id) as user_count');
        $builder->join('user_divisions', 'user_divisions.division_id = divisions.id', 'left');
        $builder->where('divisions.is_active', 1);
        $builder->groupBy('divisions.id');
        $builder->orderBy('divisions.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Check if division has users
     */
    public function hasUsers($divisionId)
    {
        $userDivisionModel = new \App\Models\UserDivisionModel();
        return $userDivisionModel->where('division_id', $divisionId)->countAllResults() > 0;
    }

    /**
     * Get division by code
     */
    public function getDivisionByCode($code)
    {
        return $this->where('code', $code)->first();
    }

    /**
     * Build hierarchy array from flat array
     */
    protected function buildHierarchy($divisions, $parentId = null, $level = 0)
    {
        $result = [];
        
        foreach ($divisions as $division) {
            if ($division['parent_id'] == $parentId) {
                $division['level'] = $level;
                $division['children'] = $this->buildHierarchy($divisions, $division['id'], $level + 1);
                $result[] = $division;
            }
        }
        
        return $result;
    }

    /**
     * Get all parent divisions for a given division
     */
    public function getParentDivisions($divisionId)
    {
        $parents = [];
        $division = $this->find($divisionId);
        
        while ($division && $division['parent_id']) {
            $parent = $this->find($division['parent_id']);
            if ($parent) {
                $parents[] = $parent;
                $division = $parent;
            } else {
                break;
            }
        }
        
        return array_reverse($parents);
    }
}
