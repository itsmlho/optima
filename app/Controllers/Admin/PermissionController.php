<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PermissionModel;
use CodeIgniter\API\ResponseTrait;
use App\Traits\ActivityLoggingTrait;

class PermissionController extends BaseController
{
    use ResponseTrait;
    use ActivityLoggingTrait;

    protected $db;
    protected $permissionModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->permissionModel = new PermissionModel();
    }

    /**
     * Permission Management Dashboard
     */
    public function index()
    {
        if (!$this->hasPermission('admin.permission_management')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $permissions = $this->getPermissionsWithStats();
            $stats = $this->getPermissionStats();

            $data = [
                'title' => 'Permission Management',
                'breadcrumbs' => [
                    '/' => 'Dashboard',
                    '/admin' => 'Administration',
                    '/admin/permissions' => 'Permission Management'
                ],
                'permissions' => $permissions,
                'stats' => $stats
            ];

            return view('admin/advanced_user_management/permissions', $data);
        } catch (\Exception $e) {
            log_message('error', 'Permission Management Error: ' . $e->getMessage());

            $data = [
                'title' => 'Permission Management - Error',
                'error_message' => 'Error loading permissions: ' . $e->getMessage(),
                'permissions' => [],
                'stats' => [
                    'total' => 0,
                    'modules' => 0,
                    'system' => 0,
                    'custom' => 0
                ]
            ];
            return view('admin/advanced_user_management/permissions', $data);
        }
    }

    /**
     * Create New Permission
     */
    public function store()
    {
        if (!$this->hasPermission('admin.permission_create')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $validation = $this->validate([
            'key' => "required|min_length[3]|max_length[100]|is_unique[permissions.key]",
            'name' => 'permit_empty|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
            'module' => 'permit_empty|max_length[50]'
        ]);

        if (!$validation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        try {
            // Hanya sertakan kolom yang ada di tabel permissions
            $permissionData = [
                'key' => $this->request->getPost('key'),
                'name' => $this->request->getPost('name') ?: $this->request->getPost('key'),
                'description' => $this->request->getPost('description'),
                'module' => $this->request->getPost('module'),
                'is_active' => 1 // Default active
            ];

            $permissionId = $this->permissionModel->insert($permissionData);

            if (!$permissionId) {
                throw new \Exception('Failed to create permission');
            }

            // Log permission creation using trait
            $this->logCreate('permissions', $permissionId, [
                'permission_id' => $permissionId,
                'key' => $permissionData['key'],
                'name' => $permissionData['name'],
                'description' => $permissionData['description'],
                'module' => $permissionData['module'],
                'created_by' => session()->get('user_id') ?? 1
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission created successfully',
                'permission_id' => $permissionId
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Create Permission Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update Permission
     */
    public function update($permissionId)
    {
        if (!$this->hasPermission('admin.permission_edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $permission = $this->permissionModel->find($permissionId);
        if (!$permission) {
            return $this->response->setJSON(['success' => false, 'message' => 'Permission not found'])->setStatusCode(404);
        }

        // Manual validation untuk update
        $validation = \Config\Services::validation();
        $validation->setRules([
            'key' => "required|min_length[3]|max_length[150]|is_unique[permissions.key,id,{$permissionId}]",
            'name' => 'permit_empty|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
            'module' => 'permit_empty|max_length[50]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            // Hanya sertakan kolom yang ada di tabel permissions
            $permissionData = [
                'key' => $this->request->getPost('key'),
                'name' => $this->request->getPost('name') ?: $this->request->getPost('key'),
                'description' => $this->request->getPost('description'),
                'module' => $this->request->getPost('module')
            ];

            $result = $this->permissionModel->update($permissionId, $permissionData);

            if ($result === false) {
                throw new \Exception('Failed to update permission in database');
            }

            // Log permission update using trait
            $this->logUpdate('permissions', $permissionId, [
                'permission_id' => $permissionId,
                'key' => $permissionData['key'],
                'name' => $permissionData['name'],
                'description' => $permissionData['description'],
                'module' => $permissionData['module'],
                'updated_by' => session()->get('user_id') ?? 1,
                'previous_key' => $permission['key'],
                'previous_name' => $permission['name']
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission updated successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Update Permission Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete Permission
     */
    public function delete($permissionId)
    {
        if (!$this->hasPermission('admin.permission_delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $permission = $this->permissionModel->find($permissionId);
        if (!$permission) {
            return $this->response->setJSON(['success' => false, 'message' => 'Permission not found'])->setStatusCode(404);
        }

        // Check if permission is assigned to roles
        $roleCount = $this->db->table('role_permissions')->where('permission_id', $permissionId)->countAllResults();
        
        // Check if permission is assigned to users directly
        $userCount = $this->db->table('user_permissions')->where('permission_id', $permissionId)->countAllResults();

        if ($roleCount > 0 || $userCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Cannot delete permission. It is assigned to {$roleCount} role(s) and {$userCount} user(s)."
            ])->setStatusCode(400);
        }

        try {
            $result = $this->permissionModel->delete($permissionId);

            if (!$result) {
                log_message('error', 'PermissionModel delete failed for ID: ' . $permissionId);
                throw new \Exception('Failed to delete permission from database');
            }

            // Log permission deletion using trait
            $this->logDelete('permissions', $permissionId, [
                'permission_id' => $permissionId,
                'key' => $permission['key'],
                'name' => $permission['name'],
                'description' => $permission['description'],
                'module' => $permission['module'],
                'deleted_by' => session()->get('user_id') ?? 1
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Delete Permission Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get Permission Details (AJAX)
     */
    public function getDetail($permissionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $permission = $this->permissionModel->find($permissionId);
            if (!$permission) {
                return $this->response->setJSON(['success' => false, 'message' => 'Permission not found']);
            }

            return $this->response->setJSON([
                'success' => true,
                'permission' => $permission
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get DataTable data for permissions
     */
    public function getDataTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Not an AJAX request'
            ])->setStatusCode(400);
        }

        try {
            $draw = intval($this->request->getPost('draw') ?? 1);
            $start = intval($this->request->getPost('start') ?? 0);
            $length = intval($this->request->getPost('length') ?? 10);
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            $orderColumn = intval($this->request->getPost('order')[0]['column'] ?? 0);
            $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'asc';
            $filter = $this->request->getPost('filter') ?? 'all';

            // Column mapping untuk DataTable
            $columns = ['key', 'name', 'description', 'module', 'actions'];
            $orderByColumn = $columns[$orderColumn] ?? 'key';

            // Build query - hanya select kolom yang ada
            $builder = $this->db->table('permissions');
            $builder->select('id, `key`, name, description, module, is_active');

            // Filter berdasarkan tipe
            if ($filter === 'system') {
                $builder->where('module', 'system');
            } elseif ($filter === 'custom') {
                $builder->where('module IS NOT NULL');
                $builder->where('module !=', 'system');
                $builder->where('module !=', '');
            } elseif ($filter === 'module') {
                // Ambil daftar module unik
                $modules = $this->db->table('permissions')
                    ->select('module')
                    ->groupBy('module')
                    ->orderBy('module', 'asc')
                    ->get()->getResultArray();

                $data = [];
                foreach ($modules as $mod) {
                    $data[] = [
                        'module' => strtoupper($mod['module']),
                        'actions' => '<button class="btn btn-sm btn-primary" onclick="toggleModulePermissions(\'' . $mod['module'] . '\', this)"><i class="fas fa-plus"></i></button>'
                    ];
                }

                return $this->response->setJSON([
                    'draw' => $draw,
                    'recordsTotal' => count($data),
                    'recordsFiltered' => count($data),
                    'data' => $data
                ]);
            }
            // jika 'all' tidak ada kondisi tambahan

            // Search
            if (!empty($searchValue)) {
                $builder->groupStart();
                $builder->like('`key`', $searchValue);
                $builder->orLike('name', $searchValue);
                $builder->orLike('description', $searchValue);
                $builder->orLike('module', $searchValue);
                $builder->groupEnd();
            }

            // Get total count
            $totalRecords = $this->db->table('permissions')->countAllResults();
            
            // Clone untuk filtered count
            $tempBuilder = clone $builder;
            $filteredRecords = $tempBuilder->countAllResults(false);

            // Apply ordering - hindari order by actions
            if ($orderByColumn !== 'actions' && in_array($orderByColumn, ['key', 'name', 'description', 'module'])) {
                if ($orderByColumn === 'key') {
                    $builder->orderBy('`key`', $orderDir);
                } else {
                    $builder->orderBy($orderByColumn, $orderDir);
                }
            } else {
                // Default ordering untuk filter module
                if ($filter === 'module') {
                    $builder->orderBy('module', 'asc');
                    $builder->orderBy('`key`', 'asc');
                } else {
                    $builder->orderBy('`key`', 'asc');
                }
            }
            
            // Apply pagination
            if ($length > 0) {
                $builder->limit($length, $start);
            }
            
            $permissions = $builder->get()->getResultArray();

            // Format data untuk DataTable - return sebagai indexed array
            $data = [];
            $currentModule = '';
            
            foreach ($permissions as $permission) {
                $statusBadge = ($permission['is_active'] ?? 1) ?
                    '<span class="badge bg-success">Active</span>' :
                    '<span class="badge bg-secondary">Inactive</span>';

                $actions = '
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-info btn-sm" onclick="editPermission(' . $permission['id'] . ')" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deletePermission(' . $permission['id'] . ')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';

                // Untuk filter module, tambahkan header module
                $keyDisplay = esc($permission['key']);
                $moduleDisplay = $permission['module'] ?? 'General';
                
                if ($filter === 'module') {
                    // Jika module berubah, tampilkan sebagai header
                    if ($currentModule !== $moduleDisplay) {
                        $currentModule = $moduleDisplay;
                        $keyDisplay = '<strong class="text-primary">' . ucfirst($moduleDisplay) . '</strong><br><span class="ms-3">' . esc($permission['key']) . '</span>';
                    } else {
                        $keyDisplay = '<span class="ms-4">' . esc($permission['key']) . '</span>';
                    }
                }

                // Return sebagai indexed array, bukan associative
                $data[] = [
                    '<div>' . $keyDisplay . '<br><small class="text-muted">' . $statusBadge . '</small></div>', // column 0
                    esc($permission['name'] ?? $permission['key']), // column 1
                    esc($permission['description'] ?? '-'), // column 2
                    '<span class="badge bg-info">' . esc($moduleDisplay) . '</span>', // column 3
                    $actions // column 4
                ];
            }

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'DataTable Permission Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'draw' => intval($this->request->getPost('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get permission counts for stats/cards/tabs
     */
    public function getCounts()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $stats = $this->getPermissionStats();
            return $this->response->setJSON($stats);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'total' => 0,
                'modules' => 0,
                'system' => 0,
                'custom' => 0
            ]);
        }
    }

    public function byModule($module)
    {
        $permissions = $this->db->table('permissions')
            ->where('module', $module)
            ->orderBy('key', 'asc')
            ->get()->getResultArray();

        return $this->response->setJSON(['permissions' => $permissions]);
    }

    /**
     * Get permission usage details
     */
    public function usage($permissionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $permission = $this->permissionModel->find($permissionId);
            if (!$permission) {
                return $this->response->setJSON(['success' => false, 'message' => 'Permission not found']);
            }

            // Get roles using this permission
            $roles = $this->db->table('role_permissions rp')
                ->join('roles r', 'r.id = rp.role_id')
                ->where('rp.permission_id', $permissionId)
                ->select('r.id, r.name, r.description')
                ->get()->getResultArray();

            // Get users with direct permission assignments
            $users = $this->db->table('user_permissions up')
                ->join('users u', 'u.id = up.user_id')
                ->where('up.permission_id', $permissionId)
                ->select('u.id, u.first_name, u.last_name, u.email, up.granted')
                ->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'permission' => $permission,
                'roles' => $roles,
                'users' => $users
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Helper Methods
     */
    protected function getPermissionsWithStats()
    {
        $permissions = $this->permissionModel->findAll();

        foreach ($permissions as &$permission) {
            // Get role count
            try {
                $roleCount = $this->db->table('role_permissions')
                    ->where('permission_id', $permission['id'])
                    ->countAllResults();
                $permission['role_count'] = $roleCount;
            } catch (\Exception $e) {
                $permission['role_count'] = 0;
            }

            // Get user override count
            try {
                $userCount = $this->db->table('user_permissions')
                    ->where('permission_id', $permission['id'])
                    ->countAllResults();
                $permission['user_override_count'] = $userCount;
            } catch (\Exception $e) {
                $permission['user_override_count'] = 0;
            }

            // Ensure required fields
            $permission['key'] = $permission['key'] ?? '';
            $permission['name'] = $permission['name'] ?? $permission['key'];
            $permission['description'] = $permission['description'] ?? '';
            $permission['module'] = $permission['module'] ?? '';
            $permission['is_active'] = $permission['is_active'] ?? 1;
        }

        return $permissions;
    }

    private function getPermissionStats()
    {
        try {
            $total = $this->db->table('permissions')->countAllResults();
            
            $system = $this->db->table('permissions')
                ->where('module', 'system')
                ->countAllResults();
            
            $custom = $this->db->table('permissions')
                ->where('module IS NOT NULL')
                ->where('module !=', 'system')
                ->where('module !=', '')
                ->countAllResults();
            
            $modules = $this->db->table('permissions')
                ->select('module')
                ->where('module IS NOT NULL')
                ->where('module !=', '')
                ->groupBy('module')
                ->countAllResults();

            return [
                'total' => $total,
                'system' => $system,
                'custom' => $custom,
                'modules' => $modules
            ];
        } catch (\Exception $e) {
            log_message('error', 'Permission Stats Error: ' . $e->getMessage());
            return [
                'total' => 0,
                'system' => 0,
                'custom' => 0,
                'modules' => 0
            ];
        }
    }

    // hasPermission method removed - using BaseController's protected method instead
}