<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'module',
        'page', 
        'action',
        'subaction',
        'component',
        'key_name',
        'display_name',
        'description',
        'category',
        'is_active',
        'created_at',
        'updated_at'
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'module' => 'required|max_length[50]',
        'page' => 'required|max_length[50]',
        'action' => 'required|max_length[50]',
        'key_name' => 'required|max_length[255]|is_unique[permissions.key_name,id,{id}]',
        'display_name' => 'required|max_length[255]',
        'category' => 'max_length[50]'
    ];
    
    protected $validationMessages = [
        'key_name' => [
            'is_unique' => 'Permission key already exists.'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
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
     * Get permissions grouped by module and page
     */
    public function getPermissionsGroupedByModuleAndPage(): array
    {
        $permissions = $this->where('is_active', 1)
                           ->orderBy('module, page, action')
                           ->findAll();
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $module = $permission['module'] ?? 'general';
            $page = $permission['page'] ?? 'general';
            
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            if (!isset($grouped[$module][$page])) {
                $grouped[$module][$page] = [];
            }
            $grouped[$module][$page][] = $permission;
        }
        
        return $grouped;
    }

    /**
     * Get permissions grouped by module
     */
    public function getPermissionsGroupedByModule(): array
    {
        $permissions = $this->where('is_active', 1)
                           ->orderBy('module, page, action')
                           ->findAll();
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $module = $permission['module'] ?? 'general';
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $permission;
        }
        
        return $grouped;
    }
    
    /**
     * Get permissions grouped by category  
     */
    public function getPermissionsGroupedByCategory(): array
    {
        $permissions = $this->where('is_active', 1)
                           ->orderBy('category, module, page')
                           ->findAll();
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $category = $permission['category'] ?? 'general';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $permission;
        }
        
        return $grouped;
    }
    
    /**
     * Get permissions for specific module
     */
    public function getModulePermissions(string $module): array
    {
        return $this->where('module', $module)
                   ->where('is_active', 1)
                   ->orderBy('page, action')
                   ->findAll();
    }
    
    /**
     * Get permissions for specific page
     */
    public function getPagePermissions(string $module, string $page): array
    {
        return $this->where('module', $module)
                   ->where('page', $page)
                   ->where('is_active', 1)
                   ->orderBy('action')
                   ->findAll();
    }
    
    /**
     * Search permissions by keyword
     */
    public function searchPermissions(string $keyword): array
    {
        return $this->groupStart()
                   ->like('display_name', $keyword)
                   ->orLike('description', $keyword)
                   ->orLike('key_name', $keyword)
                   ->groupEnd()
                   ->where('is_active', 1)
                   ->orderBy('module, page, action')
                   ->findAll();
    }
    
    /**
     * Get permission tree structure for UI
     */
    public function getPermissionTree(): array
    {
        $permissions = $this->where('is_active', 1)
                           ->orderBy('module, page, action')
                           ->findAll();
        
        $tree = [];
        
        foreach ($permissions as $permission) {
            $module = $permission['module'] ?? 'general';
            $page = $permission['page'] ?? 'general';
            
            if (!isset($tree[$module])) {
                $tree[$module] = [
                    'name' => ucfirst($module),
                    'pages' => []
                ];
            }
            
            if (!isset($tree[$module]['pages'][$page])) {
                $tree[$module]['pages'][$page] = [
                    'name' => ucfirst(str_replace('_', ' ', $page)),
                    'permissions' => []
                ];
            }
            
            $tree[$module]['pages'][$page]['permissions'][] = $permission;
        }
        
        return $tree;
    }
    
    /**
     * Create permission with auto-generated key
     */
    public function createPermission(array $data): int|bool
    {
        // Auto-generate key_name if not provided
        if (empty($data['key_name'])) {
            $keyParts = [$data['module'], $data['page'], $data['action']];
            if (!empty($data['subaction'])) {
                $keyParts[] = $data['subaction'];
            }
            if (!empty($data['component'])) {
                $keyParts[] = $data['component'];
            }
            $data['key_name'] = implode('.', $keyParts);
        }
        
        return $this->insert($data);
    }
    
    /**
     * Get permission statistics
     */
    public function getPermissionStats(): array
    {
        $db = $this->db;
        
        $stats = [];
        
        // Total permissions
        $stats['total'] = $this->where('is_active', 1)->countAllResults();
        
        // Permissions by module
        $moduleStats = $db->table($this->table)
                         ->select('module, COUNT(*) as count')
                         ->where('is_active', 1)
                         ->groupBy('module')
                         ->get()
                         ->getResultArray();
        
        $stats['by_module'] = [];
        foreach ($moduleStats as $stat) {
            $stats['by_module'][$stat['module']] = $stat['count'];
        }
        
        // Permissions by category
        $categoryStats = $db->table($this->table)
                           ->select('category, COUNT(*) as count')
                           ->where('is_active', 1)
                           ->groupBy('category')
                           ->get()
                           ->getResultArray();
        
        $stats['by_category'] = [];
        foreach ($categoryStats as $stat) {
            $stats['by_category'][$stat['category']] = $stat['count'];
        }
        
        return $stats;
    }

    /**
     * Get all unique modules for filtering
     */
    public function getModules(): array
    {
        return $this->select('module')
                   ->where('is_active', 1)
                   ->groupBy('module')
                   ->orderBy('module')
                   ->findAll();
    }

    /**
     * Get all unique pages for filtering
     */
    public function getPages(): array
    {
        return $this->select('page, module')
                   ->where('is_active', 1)
                   ->groupBy('module, page')
                   ->orderBy('module, page')
                   ->findAll();
    }
}