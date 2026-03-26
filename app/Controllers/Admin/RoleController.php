<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\RolePermissionModel;
use App\Models\UserRoleModel;

class RoleController extends BaseController
{
    protected $roleModel;
    protected $permissionModel;
    protected $rolePermissionModel;
    protected $userRoleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->rolePermissionModel = new RolePermissionModel();
        $this->userRoleModel = new UserRoleModel();
    }

    /**
     * Display simple role management page
     */
    public function index()
    {
        // Check permission: User harus punya akses ke admin module
        if (!$this->hasPermission('admin.role_management') && !$this->canAccess('admin')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }
        
        $data = [
            'title' => 'Simple Role Management',
            'roles' => $this->roleModel->findAll(),
            'permissions' => $this->getAllPermissions()
        ];

        return view('admin/advanced_user_management/role', $data);
    }

    /**
     * Get all roles
     */
    public function getRoles()
    {
        if (!$this->hasPermission('admin.role_management')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        try {
            $roles = $this->roleModel->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'roles' => $roles
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get role details with permissions
     */
    public function getRole($roleId)
    {
        if (!$this->hasPermission('admin.role_management')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        try {
            $role = $this->roleModel->find($roleId);
            if (!$role) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Role tidak ditemukan'
                ]);
            }

            // Get role permissions
            $permissions = $this->rolePermissionModel
                ->join('permissions p', 'p.id = role_permissions.permission_id')
                ->where('role_permissions.role_id', $roleId)
                ->where('role_permissions.granted', 1)
                ->select('p.id, p.display_name, p.key_name, p.module, p.page, p.action, p.description')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'role' => $role,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get all permissions grouped by module
     */
    public function getPermissions()
    {
        if (!$this->hasPermission('admin.role_management')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        try {
            $permissions = $this->getAllPermissions();
            
            return $this->response->setJSON([
                'success' => true,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Save role with permissions
     */
    public function saveRole()
    {
        if (!$this->hasPermission('admin.role_management')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        try {
            $input = $this->request->getJSON(true);
            
            // Debug logging
            log_message('info', 'RoleController::saveRole - Input: ' . json_encode($input));
            
            $roleData = [
                'name' => $input['name'],
                'description' => $input['description'] ?? '',
                'is_active' => $input['is_active'] ?? 1
            ];

            $db = \Config\Database::connect();
            $db->transStart();

            if (!empty($input['role_id'])) {
                // Update existing role
                $roleId = $input['role_id'];
                $this->roleModel->update($roleId, $roleData);
                
                // Remove existing permissions
                $this->rolePermissionModel->where('role_id', $roleId)->delete();
                log_message('info', "RoleController::saveRole - Removed existing permissions for role ID: {$roleId}");
            } else {
                // Create new role
                $roleId = $this->roleModel->insert($roleData);
                log_message('info', "RoleController::saveRole - Created new role with ID: {$roleId}");
            }

            // Add new permissions
            if (!empty($input['permissions'])) {
                log_message('info', 'RoleController::saveRole - Adding permissions: ' . json_encode($input['permissions']));
                foreach ($input['permissions'] as $permission) {
                    $this->rolePermissionModel->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permission['permission_id'],
                        'granted' => 1,
                        'assigned_by' => session()->get('user_id'),
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
                log_message('info', "RoleController::saveRole - Added " . count($input['permissions']) . " permissions for role ID: {$roleId}");
            } else {
                log_message('info', 'RoleController::saveRole - No permissions to add');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            // Send notification - role saved
            if (function_exists('notify_role_saved')) {
                $action = !empty($input['role_id']) ? 'updated' : 'created';
                $permissionsCount = !empty($input['permissions']) ? count($input['permissions']) : 0;
                
                notify_role_saved([
                    'id' => $roleId,
                    'role_name' => $roleData['name'],
                    'action' => $action,
                    'permissions_count' => $permissionsCount,
                    'saved_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/admin/roles')
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role saved successfully'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get all permissions grouped by module
     */
    private function getAllPermissions()
    {
        $permissions = $this->permissionModel
            ->orderBy('module', 'ASC')
            ->orderBy('page', 'ASC') 
            ->orderBy('action', 'ASC')
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
}
