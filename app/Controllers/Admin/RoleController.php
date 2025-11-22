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
        try {
            $roles = $this->roleModel->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'roles' => $roles
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading roles: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get role details with permissions
     */
    public function getRole($roleId)
    {
        try {
            $role = $this->roleModel->find($roleId);
            if (!$role) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Role not found'
                ]);
            }

            // Get role permissions
            $permissions = $this->rolePermissionModel
                ->join('permissions p', 'p.id = role_permissions.permission_id')
                ->where('role_permissions.role_id', $roleId)
                ->where('role_permissions.granted', 1)
                ->select('p.*')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'role' => $role,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading role: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all permissions grouped by module
     */
    public function getPermissions()
    {
        try {
            $permissions = $this->getAllPermissions();
            
            return $this->response->setJSON([
                'success' => true,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading permissions: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save role with permissions
     */
    public function saveRole()
    {
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

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role saved successfully'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error saving role: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all permissions grouped by module
     */
    private function getAllPermissions()
    {
        $permissions = $this->permissionModel->orderBy('module', 'ASC')->orderBy('level', 'ASC')->findAll();
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission['module']][] = $permission;
        }
        
        return $grouped;
    }
}
