<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use App\Traits\ActivityLoggingTrait;

class RoleController extends BaseController
{
    use ResponseTrait;
    use ActivityLoggingTrait;

    protected $db;
    protected $roleModel;
    protected $permissionModel;
    protected $userModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->userModel = new UserModel();
    }

    /**
     * Role Management Dashboard
     */
    public function index()
    {
        if (!$this->hasPermission('admin.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $roles = $this->getRolesWithStats();
            $permissions = $this->getPermissionsGroupedByModule();
            $stats = $this->getRoleStats();

            $data = [
                'title' => 'Role Management',
                'breadcrumbs' => [
                    '/' => 'Dashboard',
                    '/admin' => 'Administration',
                    '/admin/role' => 'Role Management'
                ],
                'roles' => $roles,
                'permissions' => $permissions,
                'stats' => $stats
            ];

            return view('admin/advanced_user_management/role', $data);
        } catch (\Exception $e) {
            log_message('error', 'Role Management Error: ' . $e->getMessage());

            $data = [
                'title' => 'Role Management - Error',
                'error_message' => 'Error loading roles: ' . $e->getMessage(),
                'roles' => [],
                'permissions' => [],
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'permissions' => 0,
                    'role_permissions' => 0
                ]
            ];
            return view('admin/advanced_user_management/role', $data);
        }
    }

    /**
     * Get DataTable data for roles
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
            $orderColumn = intval($this->request->getPost('order')[0]['column'] ?? 1);
            $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'asc';
            $filter = $this->request->getPost('filter') ?? 'all';

            // Column mapping untuk DataTable
            $columns = ['checkbox', 'name', 'description', 'permissions', 'users', 'status', 'actions'];
            $orderByColumn = $columns[$orderColumn] ?? 'name';

            // Build query
            $builder = $this->db->table('roles r');
            $builder->select('r.id, r.name, r.description, r.is_active, r.is_system_role');

            // Filter berdasarkan tipe
            if ($filter === 'active') {
                $builder->where('r.is_active', 1);
            } elseif ($filter === 'inactive') {
                $builder->where('r.is_active', 0);
            } elseif ($filter === 'system') {
                $builder->where('r.is_system_role', 1);
            } elseif ($filter === 'custom') {
                $builder->where('r.is_system_role', 0);
            }

            // Search
            if (!empty($searchValue)) {
                $builder->groupStart();
                $builder->like('r.name', $searchValue);
                $builder->orLike('r.description', $searchValue);
                $builder->groupEnd();
            }

            // Get total count
            $totalRecords = $this->db->table('roles')->countAllResults();
            
            // Clone untuk filtered count
            $tempBuilder = clone $builder;
            $filteredRecords = $tempBuilder->countAllResults(false);

            // Apply ordering
            if ($orderByColumn !== 'checkbox' && $orderByColumn !== 'actions' && 
                in_array($orderByColumn, ['name', 'description', 'status'])) {
                if ($orderByColumn === 'status') {
                    $builder->orderBy('r.is_active', $orderDir);
                } else {
                    $builder->orderBy('r.' . $orderByColumn, $orderDir);
                }
            } else {
                $builder->orderBy('r.name', 'asc');
            }
            
            // Apply pagination
            if ($length > 0) {
                $builder->limit($length, $start);
            }
            
            $roles = $builder->get()->getResultArray();

            // Format data untuk DataTable
            $data = [];
            
            foreach ($roles as $role) {
                // Get permission count
                $permissionCount = $this->db->table('role_permissions')
                    ->where('role_id', $role['id'])
                    ->countAllResults();

                // Get user count
                $userCount = $this->db->table('user_roles')
                    ->where('role_id', $role['id'])
                    ->countAllResults();

                $statusBadge = $role['is_active'] ?
                    '<span class="badge bg-success">Active</span>' :
                    '<span class="badge bg-secondary">Inactive</span>';

                $typeBadge = $role['is_system_role'] ?
                    '<span class="badge bg-warning">System</span>' :
                    '<span class="badge bg-info">Custom</span>';

                $checkbox = '<input type="checkbox" name="role_ids[]" value="' . $role['id'] . '" class="form-check-input">';

                $actions = '
                    <div class="btn-group btn-group-sm role-actions">
                        <button type="button" class="btn btn-info btn-sm" onclick="viewRole(' . $role['id'] . ')" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="editRole(' . $role['id'] . ')" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRole(' . $role['id'] . ', \'' . esc($role['name']) . '\')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';

                $roleDisplay = '
                    <div>
                        <strong>' . esc($role['name']) . '</strong>
                        <br>' . $typeBadge . '
                    </div>';

                // Return sebagai indexed array
                $data[] = [
                    $checkbox, // column 0
                    $roleDisplay, // column 1
                    esc($role['description'] ?? '-'), // column 2
                    '<span class="badge bg-primary">' . $permissionCount . '</span>', // column 3
                    '<span class="badge bg-success">' . $userCount . '</span>', // column 4
                    $statusBadge, // column 5
                    $actions // column 6
                ];
            }

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
                'stats' => $this->getRoleStats()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'DataTable Role Error: ' . $e->getMessage());
            
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
     * Create New Role
     */
    public function store()
    {
        if (!$this->hasPermission('admin.manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $validation = $this->validate([
            'name' => 'required|min_length[3]|max_length[100]|is_unique[roles.name]',
            'description' => 'permit_empty|max_length[255]',
            'is_system_role' => 'permit_empty|in_list[0,1]'
        ]);

        if (!$validation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        $this->db->transStart();

        try {
            // Create role
            $roleData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_system_role' => $this->request->getPost('is_system_role') ?? 0,
                'is_active' => 1
            ];

            $roleId = $this->roleModel->insert($roleData);

            if (!$roleId) {
                throw new \Exception('Failed to create role');
            }

            // Assign permissions
            $permissions = $this->request->getPost('permissions') ?? [];
            if (!empty($permissions)) {
                foreach ($permissions as $permissionId) {
                    $this->db->table('role_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                        'granted' => 1,
                        'assigned_by' => session()->get('user_id') ?? 1,
                        'assigned_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Log role creation using trait
            $this->logCreate('roles', $roleId, [
                'role_id' => $roleId,
                'name' => $roleData['name'],
                'description' => $roleData['description'],
                'is_system_role' => $roleData['is_system_role'],
                'permissions' => $permissions,
                'created_by' => session()->get('user_id') ?? 1
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role created successfully',
                'role_id' => $roleId
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Create Role Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update Role
     */
    public function update($roleId)
    {
        if (!$this->hasPermission('admin.manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role not found'])->setStatusCode(404);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => "required|min_length[3]|max_length[100]|is_unique[roles.name,id,{$roleId}]",
            'description' => 'permit_empty|max_length[255]',
            'is_active' => 'permit_empty|in_list[0,1]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $this->db->transStart();

        try {
            // Update role
            $roleData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ?? 1
            ];

            log_message('info', 'Role Update - Role Data: ' . json_encode($roleData));

            // Temporarily disable validation for debugging
            $this->roleModel->skipValidation(true);
            $result = $this->roleModel->update($roleId, $roleData);
            $this->roleModel->skipValidation(false);
            log_message('info', 'Role Update - Role update result: ' . ($result ? 'SUCCESS' : 'FAILED'));

            if ($result === false) {
                log_message('error', 'Role Update - Role update failed for role ID: ' . $roleId);
                throw new \Exception('Failed to update role');
            }

            // Update permissions
            $permissions = $this->request->getPost('permissions') ?? [];
            log_message('info', 'Role Update - Permissions: ' . json_encode($permissions));
            
            // Delete existing permissions
            $this->db->table('role_permissions')->where('role_id', $roleId)->delete();
            log_message('info', 'Role Update - Deleted existing permissions for role: ' . $roleId);
            
            // Insert new permissions
            if (!empty($permissions)) {
                foreach ($permissions as $permissionId) {
                    $insertData = [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                        'granted' => 1,
                        'assigned_by' => session()->get('user_id') ?? 1,
                        'assigned_at' => date('Y-m-d H:i:s')
                    ];
                    log_message('info', 'Role Update - Inserting permission: ' . json_encode($insertData));
                    
                    try {
                        $insertResult = $this->db->table('role_permissions')->insert($insertData);
                        log_message('info', 'Role Update - Permission insert result: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));
                    } catch (\Exception $insertError) {
                        log_message('error', 'Role Update - Permission insert error: ' . $insertError->getMessage());
                        throw $insertError;
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Log role update using trait
            try {
                $this->logUpdate('roles', $roleId, $role, $roleData, [
                    'role_id' => $roleId,
                    'permissions' => $permissions,
                    'updated_by' => session()->get('user_id') ?? 1
                ]);
                log_message('info', 'Role Update - Activity log created successfully');
            } catch (\Exception $logError) {
                log_message('error', 'Role Update - Activity log error: ' . $logError->getMessage());
                // Don't fail the transaction for logging errors
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role updated successfully'
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Update Role Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete Role
     */
    public function delete($roleId)
    {
        if (!$this->hasPermission('admin.delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role not found'])->setStatusCode(404);
        }

        // Check if role is assigned to users
        $userCount = $this->db->table('user_roles')->where('role_id', $roleId)->countAllResults();
        
        if ($userCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Cannot delete role. It is assigned to {$userCount} user(s)."
            ])->setStatusCode(400);
        }

        $this->db->transStart();

        try {
            // Delete role permissions
            $this->db->table('role_permissions')->where('role_id', $roleId)->delete();
            
            // Delete role
            $result = $this->roleModel->delete($roleId);

            if (!$result) {
                throw new \Exception('Failed to delete role');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Log role deletion using trait
            $this->logDelete('roles', $roleId, [
                'role_id' => $roleId,
                'name' => $role['name'],
                'description' => $role['description'],
                'deleted_by' => session()->get('user_id') ?? 1
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Delete Role Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get Role Details
     */
    public function getRoleDetail($roleId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $role = $this->roleModel->find($roleId);
            if (!$role) {
                return $this->response->setJSON(['success' => false, 'message' => 'Role not found']);
            }

            // Get role permissions
            $rolePermissions = $this->db->table('role_permissions')
                ->where('role_id', $roleId)
                ->select('permission_id')
                ->get()
                ->getResultArray();

            $permissionIds = array_column($rolePermissions, 'permission_id');
            
            // Debug logging
            log_message('info', "RoleController::getRoleDetail - Role ID: {$roleId}");
            log_message('info', "RoleController::getRoleDetail - Permission IDs: " . json_encode($permissionIds));

            return $this->response->setJSON([
                'success' => true,
                'role' => $role,
                'rolePermissions' => $permissionIds
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show Role Details
     */
    public function show($roleId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $role = $this->roleModel->find($roleId);
            if (!$role) {
                return $this->response->setJSON(['success' => false, 'message' => 'Role not found']);
            }

            // Get permissions
            $permissions = $this->db->table('role_permissions rp')
                ->join('permissions p', 'p.id = rp.permission_id')
                ->where('rp.role_id', $roleId)
                ->select('p.id, p.key, p.name, p.description, p.module')
                ->get()->getResultArray();

            // Get users
            $users = $this->db->table('user_roles ur')
                ->join('users u', 'u.id = ur.user_id')
                ->where('ur.role_id', $roleId)
                ->select('u.id, u.first_name, u.last_name, u.email, u.is_active')
                ->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'role' => $role,
                    'permissions' => $permissions,
                    'users' => $users
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get role counts for stats
     */
    public function getCounts()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $stats = $this->getRoleStats();
            return $this->response->setJSON($stats);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'total' => 0,
                'active' => 0,
                'permissions' => 0,
                'role_permissions' => 0
            ]);
        }
    }

    /**
     * Bulk Actions
     */
    public function bulkAction()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $action = $this->request->getPost('action');
        $roleIds = $this->request->getPost('role_ids');

        if (empty($roleIds) || !is_array($roleIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No roles selected']);
        }

        try {
            $affectedRows = 0;

            switch ($action) {
                case 'activate':
                    $affectedRows = $this->db->table('roles')
                        ->whereIn('id', $roleIds)
                        ->update(['is_active' => 1]);
                    break;
                    
                case 'deactivate':
                    $affectedRows = $this->db->table('roles')
                        ->whereIn('id', $roleIds)
                        ->update(['is_active' => 0]);
                    break;
                    
                case 'delete':
                    // Check if any roles are assigned to users
                    $userCount = $this->db->table('user_roles')
                        ->whereIn('role_id', $roleIds)
                        ->countAllResults();
                    
                    if ($userCount > 0) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Cannot delete roles that are assigned to users.'
                        ]);
                    }
                    
                    // Delete role permissions first
                    $this->db->table('role_permissions')->whereIn('role_id', $roleIds)->delete();
                    
                    // Delete roles
                    $affectedRows = $this->db->table('roles')->whereIn('id', $roleIds)->delete();
                    break;
                    
                default:
                    return $this->response->setJSON(['success' => false, 'message' => 'Invalid action']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => ucfirst($action) . " completed successfully. {$affectedRows} role(s) affected."
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Bulk Action Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Helper Methods
     */
    private function getRolesWithStats()
    {
        $roles = $this->roleModel->findAll();

        foreach ($roles as &$role) {
            // Get permission count
            try {
                $permissionCount = $this->db->table('role_permissions')
                    ->where('role_id', $role['id'])
                    ->countAllResults();
                $role['permission_count'] = $permissionCount;
            } catch (\Exception $e) {
                $role['permission_count'] = 0;
            }

            // Get user count
            try {
                $userCount = $this->db->table('user_roles')
                    ->where('role_id', $role['id'])
                    ->countAllResults();
                $role['user_count'] = $userCount;
            } catch (\Exception $e) {
                $role['user_count'] = 0;
            }

            // Ensure required fields
            $role['name'] = $role['name'] ?? '';
            $role['description'] = $role['description'] ?? '';
            $role['is_active'] = $role['is_active'] ?? 1;
            $role['is_system_role'] = $role['is_system_role'] ?? 0;
        }

        return $roles;
    }

    private function getPermissionsGroupedByModule()
    {
        try {
            $permissions = $this->permissionModel->findAll();
            $grouped = [];

            foreach ($permissions as $permission) {
                $module = $permission['module'] ?? 'general';
                $grouped[$module][] = $permission;
            }

            return $grouped;
        } catch (\Exception $e) {
            log_message('error', 'Get Permissions Error: ' . $e->getMessage());
            return [];
        }
    }

    private function getRoleStats()
    {
        try {
            $total = $this->db->table('roles')->countAllResults();
            
            $active = $this->db->table('roles')
                ->where('is_active', 1)
                ->countAllResults();
            
            $permissions = $this->db->table('permissions')->countAllResults();
            
            $rolePermissions = $this->db->table('role_permissions')->countAllResults();

            return [
                'total' => $total,
                'active' => $active,
                'permissions' => $permissions,
                'role_permissions' => $rolePermissions
            ];
        } catch (\Exception $e) {
            log_message('error', 'Role Stats Error: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'permissions' => 0,
                'role_permissions' => 0
            ];
        }
    }

    // hasPermission method removed - using BaseController's protected method instead
}