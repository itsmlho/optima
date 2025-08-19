<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id'; // Fixed: Should be id, not role_id
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'description', 'is_preset', 'level', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[100]|is_unique[roles.name,id,{id}]', // Fixed: Use id as primary key
        'description' => 'permit_empty|max_length[500]',
        'is_preset' => 'permit_empty|in_list[0,1]',
        'level' => 'permit_empty|integer|greater_than[0]|less_than[6]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Nama peran harus diisi.',
            'max_length' => 'Nama peran maksimal 100 karakter.',
            'is_unique' => 'Nama peran sudah ada dalam sistem.'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // PERBAIKAN: Menggunakan Dependency Injection untuk mengelola model lain.
    protected $userModel;
    protected $permissionModel;

    public function __construct()
    {
        parent::__construct();
        // Inisialisasi model lain di constructor agar lebih rapi dan mudah di-test.
        $this->userModel = model('UserModel');
        $this->permissionModel = model('PermissionModel');
    }

    // --- Role Management Methods ---

    public function getRoles($filters = [])
    {
        $builder = $this->builder();
        
        if (!empty($filters['search'])) {
            $builder->like('name', $filters['search'])
                    ->orLike('description', 'search');
        }

        if (isset($filters['is_preset'])) {
            $builder->where('is_preset', $filters['is_preset']);
        }

        return $builder->orderBy('name', 'ASC')->get()->getResultArray();
    }

    public function getPresetRoles()
    {
        return $this->where('is_preset', 1)->findAll();
    }

    public function getRolesByUser($userId)
    {
        // Menggunakan properti $this->userModel yang sudah diinisialisasi.
        $user = $this->userModel->getUsersWithRole($userId);
        return $user ? [$user['role_name']] : [];
    }

    public function createRoleWithPermissions($roleData, $permissionIds)
    {
        $this->db->transStart();
        
        $roleId = $this->insert($roleData);
        
        if ($roleId && !empty($permissionIds)) {
            $this->assignPermissionsToRole($roleId, $permissionIds, false); // Memanggil fungsi yang sudah ada
        }
        
        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * PERBAIKAN: Mengoptimalkan query untuk menghindari N+1 problem.
     * Metode ini sekarang hanya menggunakan satu query, bukan satu query per peran.
     */
    public function getRoleUsageStats()
    {
        return $this->builder('r')
            ->select('r.name, COUNT(u.id) as user_count')
            ->join('users u', 'u.role_id = r.id', 'left') // LEFT JOIN untuk menyertakan peran dengan 0 pengguna
            ->groupBy('r.id, r.name')
            ->orderBy('r.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    // --- Permission Management Methods ---

    public function getPermissionsGroupedByModule()
    {
        return $this->permissionModel->getPermissionsGroupedByModule();
    }

    public function getPermissionsByRole($roleName)
    {
        return $this->permissionModel->getPermissionsByRole($roleName);
    }
    
    public function getRolePermissions($roleId)
    {
        return $this->db->table('role_permissions')
                        ->where('role_id', $roleId)
                        ->get()
                        ->getResultArray();
    }

    /**
     * @param int   $roleId
     * @param array $permissionIds
     * @param bool  $deleteFirst   Apakah hapus dulu permission lama atau tidak.
     */
    public function assignPermissionsToRole($roleId, $permissionIds, $deleteFirst = true)
    {
        if ($deleteFirst) {
            // Hapus izin yang ada untuk peran ini
            $this->db->table('role_permissions')->where('role_id', $roleId)->delete();
        }
        
        if (!empty($permissionIds)) {
            $rolePermissionData = [];
            foreach ($permissionIds as $permissionId) {
                $rolePermissionData[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            
            return $this->db->table('role_permissions')->insertBatch($rolePermissionData);
        }
        
        return true;
    }

    // --- System Methods ---

    public function getAvailableModules()
    {
        return $this->permissionModel->getAvailableModules();
    }

    public function getRoleTemplates()
    {
        return [
            'management' => [
                'name' => 'Management Level',
                'description' => 'Full access across divisions except permission management.',
                'permissions' => $this->getManagementPermissions()
            ],
            'division_head' => [
                'name' => 'Division Head',
                'description' => 'Limited access to own division with staff filtering.',
                'permissions' => $this->getDivisionHeadPermissions()
            ],
            'admin' => [
                'name' => 'Admin Level',
                'description' => 'Access limited to data identified with admin ID.',
                'permissions' => $this->getAdminPermissions()
            ]
        ];
    }

    // Private helper methods untuk templates
    private function getManagementPermissions()
    {
        return [
           'dashboard.view', 'dashboard.export', 'users.view', 'users.create', 'users.edit',
           'divisions.view', 'projects.view', 'projects.create', 'projects.edit',
           'rentals.view', 'rentals.create', 'rentals.edit', 'rentals.approve',
           'forklifts.view', 'forklifts.create', 'forklifts.edit', 'reports.view', 'reports.export', 'settings.view', 'logs.view'
        ];
    }

    private function getDivisionHeadPermissions()
    {
        return [
           'dashboard.view', 'divisions.view', 'projects.view', 'projects.create', 'projects.edit',
           'rentals.view', 'rentals.create', 'rentals.edit', 'rentals.approve',
           'forklifts.view', 'forklifts.create', 'forklifts.edit', 'reports.view', 'logs.view'
        ];
    }

    private function getAdminPermissions()
    {
        return [
           'dashboard.view', 'rentals.view', 'rentals.create', 'rentals.edit',
           'forklifts.view', 'forklifts.create', 'forklifts.edit', 'ports.view'
        ];
    }
}