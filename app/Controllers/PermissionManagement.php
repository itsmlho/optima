<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class PermissionManagement extends BaseController
{
    protected $permissionModel;
    protected $roleModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['permission_helper', 'user_permission_helper']);
    }

    /**
     * Permission List - View all system permissions
     */
    public function index()
    {
        // Check permission
        if (!hasPermission('settings.permission.view')) {
            return redirect()->to('/')->with('error', 'Akses ditolak: Anda tidak memiliki permission untuk melihat permission list');
        }

        $data = [
            'title' => 'Permission Management',
            'modules' => $this->getModuleList()
        ];

        return view('settings/permissions/index', $data);
    }

    /**
     * Get permission list (AJAX DataTable)
     */
    public function getPermissions()
    {
        if (!hasPermission('settings.permission.view')) {
            return $this->response->setJSON(['error' => 'Akses ditolak']);
        }

        $request = $this->request;
        $draw = $request->getVar('draw');
        $start = $request->getVar('start') ?? 0;
        $length = $request->getVar('length') ?? 50;
        $searchValue = $request->getVar('search')['value'] ?? '';
        $moduleFilter = $request->getVar('module') ?? '';

        $builder = $this->db->table('permissions');

        // Apply filters
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('key_name', $searchValue)
                ->orLike('display_name', $searchValue)
                ->orLike('description', $searchValue)
                ->groupEnd();
        }

        if (!empty($moduleFilter)) {
            $builder->where('module', $moduleFilter);
        }

        // Total records
        $totalRecords = $builder->countAllResults(false);

        // Get data
        $permissions = $builder->orderBy('module, page, action')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        // Get usage count for each permission
        foreach ($permissions as &$perm) {
            // Count role assignments
            $roleCount = $this->db->table('role_permissions')
                ->where('permission_id', $perm['id'])
                ->where('granted', 1)
                ->countAllResults();

            // Count user assignments
            $userCount = $this->db->table('user_permissions')
                ->where('permission_id', $perm['id'])
                ->where('granted', 1)
                ->where('expires_at IS NULL OR expires_at >', date('Y-m-d H:i:s'))
                ->countAllResults();

            $perm['role_count'] = $roleCount;
            $perm['user_count'] = $userCount;
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $this->db->table('permissions')->countAllResults(),
            'recordsFiltered' => $totalRecords,
            'data' => $permissions
        ]);
    }

    /**
     * Role Permission Assignment - View
     */
    public function rolePermissions($roleId = null)
    {
        if (!hasPermission('settings.role.assign_permission')) {
            return redirect()->to('/')->with('error', 'Akses ditolak: Anda tidak memiliki permission untuk assign role permissions');
        }

        $roles = $this->db->table('roles')
            ->orderBy('name')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Role Permission Assignment',
            'roles' => $roles,
            'selected_role_id' => $roleId,
            'modules' => $this->getModuleList()
        ];

        return view('settings/permissions/role_permissions', $data);
    }

    /**
     * Get role permissions (AJAX)
     */
    public function getRolePermissions($roleId)
    {
        if (!hasPermission('settings.role.assign_permission')) {
            return $this->response->setJSON(['error' => 'Akses ditolak']);
        }

        // Get all permissions grouped by module
        $permissions = $this->db->table('permissions')
            ->orderBy('module, page, action')
            ->get()
            ->getResultArray();

        // Get granted permissions for this role
        $grantedPerms = $this->db->table('role_permissions')
            ->select('permission_id')
            ->where('role_id', $roleId)
            ->where('granted', 1)
            ->get()
            ->getResultArray();

        $grantedIds = array_column($grantedPerms, 'permission_id');

        // Group by module and page
        $grouped = [];
        foreach ($permissions as $perm) {
            $module = $perm['module'];
            $page = $perm['page'];

            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            if (!isset($grouped[$module][$page])) {
                $grouped[$module][$page] = [];
            }

            $perm['granted'] = in_array($perm['id'], $grantedIds);
            $grouped[$module][$page][] = $perm;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $grouped
        ]);
    }

    /**
     * Save role permissions (bulk update)
     */
    public function saveRolePermissions()
    {
        if (!hasPermission('settings.role.assign_permission')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $roleId = $this->request->getPost('role_id');
        $permissionIds = $this->request->getPost('permission_ids') ?? [];
        $userId = session()->get('user_id');

        if (!$roleId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role ID required']);
        }

        try {
            $this->db->transStart();

            // Delete existing permissions
            $this->db->table('role_permissions')
                ->where('role_id', $roleId)
                ->delete();

            // Insert new permissions
            foreach ($permissionIds as $permId) {
                $this->db->table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permId,
                    'granted' => 1,
                    'assigned_by' => $userId,
                    'assigned_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Database error']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => count($permissionIds) . ' permissions assigned to role'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Gagal memproses permintaan. Silakan coba lagi.');
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * User Custom Permission - View
     */
    public function userPermissions($userId = null)
    {
        if (!hasPermission('settings.user.assign_permission')) {
            return redirect()->to('/')->with('error', 'Akses ditolak: Anda tidak memiliki permission untuk assign user permissions');
        }

        // Get users
        $users = $this->db->table('users u')
            ->select('u.id, u.username, u.email, r.name as role_name')
            ->join('user_roles ur', 'ur.user_id = u.id', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->where('u.is_active', 1)
            ->orderBy('u.username')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'User Custom Permissions',
            'users' => $users,
            'selected_user_id' => $userId,
            'modules' => $this->getModuleList()
        ];

        return view('settings/permissions/user_permissions', $data);
    }

    /**
     * Get user custom permissions (AJAX)
     */
    public function getUserPermissions($userId)
    {
        if (!hasPermission('settings.user.assign_permission')) {
            return $this->response->setJSON(['error' => 'Akses ditolak']);
        }

        // Get all permissions
        $allPermissions = $this->db->table('permissions')
            ->orderBy('module, page, action')
            ->get()
            ->getResultArray();

        // Get user-specific permissions
        $userPerms = $this->db->table('user_permissions')
            ->where('user_id', $userId)
            ->where('expires_at IS NULL OR expires_at >', date('Y-m-d H:i:s'))
            ->get()
            ->getResultArray();

        $userPermsMap = [];
        foreach ($userPerms as $up) {
            $userPermsMap[$up['permission_id']] = $up;
        }

        // Get user role permissions for comparison
        $rolePerms = $this->db->query("
            SELECT DISTINCT p.id as permission_id
            FROM user_roles ur
            INNER JOIN role_permissions rp ON rp.role_id = ur.role_id
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE ur.user_id = ? AND rp.granted = 1 AND ur.is_active = 1
        ", [$userId])->getResultArray();

        $rolePermIds = array_column($rolePerms, 'permission_id');

        // Group permissions
        $grouped = [];
        foreach ($allPermissions as $perm) {
            $module = $perm['module'];
            $page = $perm['page'];

            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            if (!isset($grouped[$module][$page])) {
                $grouped[$module][$page] = [];
            }

            // Check permission source
            $hasRolePermission = in_array($perm['id'], $rolePermIds);
            $userOverride = isset($userPermsMap[$perm['id']]) ? $userPermsMap[$perm['id']] : null;

            $perm['has_role_permission'] = $hasRolePermission;
            $perm['user_override'] = $userOverride;
            $perm['effective_permission'] = $userOverride ? (bool)$userOverride['granted'] : $hasRolePermission;

            $grouped[$module][$page][] = $perm;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $grouped
        ]);
    }

    /**
     * Grant user custom permission
     */
    public function grantUserPermission()
    {
        if (!hasPermission('settings.user.assign_permission')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $userId = $this->request->getPost('user_id');
        $permissionId = $this->request->getPost('permission_id');
        $granted = $this->request->getPost('granted') == 1 ? 1 : 0;
        $reason = $this->request->getPost('reason') ?? '';
        $expiresAt = $this->request->getPost('expires_at') ?? null;
        $isTemporary = !empty($expiresAt) ? 1 : 0;

        if (!$userId || !$permissionId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID and Permission ID required']);
        }

        try {
            // Check if exists
            $existing = $this->db->table('user_permissions')
                ->where('user_id', $userId)
                ->where('permission_id', $permissionId)
                ->get()
                ->getRow();

            $data = [
                'user_id' => $userId,
                'permission_id' => $permissionId,
                'granted' => $granted,
                'reason' => $reason,
                'assigned_by' => session()->get('user_id'),
                'assigned_at' => date('Y-m-d H:i:s'),
                'expires_at' => $expiresAt,
                'is_temporary' => $isTemporary
            ];

            if ($existing) {
                $this->db->table('user_permissions')
                    ->where('id', $existing->id)
                    ->update($data);
                $message = 'Permission updated';
            } else {
                $this->db->table('user_permissions')->insert($data);
                $message = 'Permission granted';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Gagal memproses permintaan. Silakan coba lagi.');
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Revoke user custom permission
     */
    public function revokeUserPermission()
    {
        if (!hasPermission('settings.user.assign_permission')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $userId = $this->request->getPost('user_id');
        $permissionId = $this->request->getPost('permission_id');

        if (!$userId || !$permissionId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID and Permission ID required']);
        }

        try {
            $this->db->table('user_permissions')
                ->where('user_id', $userId)
                ->where('permission_id', $permissionId)
                ->delete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission override removed (fallback to role permissions)'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Gagal memproses permintaan. Silakan coba lagi.');
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Bulk update user permissions
     */
    public function bulkUpdateUserPermissions()
    {
        if (!hasPermission('settings.user.assign_permission')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $userId = $this->request->getPost('user_id');
        $permissions = $this->request->getPost('permissions') ?? []; // [{permission_id, granted, reason}]

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID required']);
        }

        try {
            $this->db->transStart();

            $assignedBy = session()->get('user_id');
            $now = date('Y-m-d H:i:s');
            $updated = 0;

            foreach ($permissions as $perm) {
                $permId = $perm['permission_id'];
                $granted = $perm['granted'];
                $reason = $perm['reason'] ?? '';

                // Check if exists
                $existing = $this->db->table('user_permissions')
                    ->where('user_id', $userId)
                    ->where('permission_id', $permId)
                    ->get()
                    ->getRow();

                $data = [
                    'user_id' => $userId,
                    'permission_id' => $permId,
                    'granted' => $granted,
                    'reason' => $reason,
                    'assigned_by' => $assignedBy,
                    'assigned_at' => $now
                ];

                if ($existing) {
                    $this->db->table('user_permissions')
                        ->where('id', $existing->id)
                        ->update($data);
                } else {
                    $this->db->table('user_permissions')->insert($data);
                }

                $updated++;
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Database error']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "{$updated} permissions updated"
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Gagal memproses permintaan. Silakan coba lagi.');
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    /**
     * Get module list
     */
    private function getModuleList()
    {
        $modules = $this->db->table('permissions')
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->get()
            ->getResultArray();

        return array_column($modules, 'module');
    }

    /**
     * Permission audit trail
     */
    public function auditTrail()
    {
        if (!hasPermission('settings.permission.view')) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'Permission Audit Trail'
        ];

        return view('settings/permissions/audit_trail', $data);
    }

    /**
     * Get permission changes (audit log)
     */
    public function getAuditLog()
    {
        if (!hasPermission('settings.permission.view')) {
            return $this->response->setJSON(['error' => 'Akses ditolak']);
        }

        // Query role permission changes
        $roleChanges = $this->db->query("
            SELECT 
                'role' as type,
                r.name as target_name,
                p.display_name as permission_name,
                rp.granted,
                u.username as assigned_by_name,
                rp.assigned_at
            FROM role_permissions rp
            INNER JOIN roles r ON r.id = rp.role_id
            INNER JOIN permissions p ON p.id = rp.permission_id
            LEFT JOIN user u ON u.id = rp.assigned_by
            ORDER BY rp.assigned_at DESC
            LIMIT 100
        ")->getResultArray();

        // Query user permission changes  
        $userChanges = $this->db->query("
            SELECT 
                'user' as type,
                usr.username as target_name,
                p.display_name as permission_name,
                up.granted,
                up.reason,
                up.expires_at,
                up.is_temporary,
                u.username as assigned_by_name,
                up.assigned_at
            FROM user_permissions up
            INNER JOIN user usr ON usr.id = up.user_id
            INNER JOIN permissions p ON p.id = up.permission_id
            LEFT JOIN user u ON u.id = up.assigned_by
            ORDER BY up.assigned_at DESC
            LIMIT 100
        ")->getResultArray();

        $combined = array_merge($roleChanges, $userChanges);

        // Sort by assigned_at descending
        usort($combined, function ($a, $b) {
            return strtotime($b['assigned_at']) - strtotime($a['assigned_at']);
        });

        return $this->response->setJSON([
            'success' => true,
            'data' => array_slice($combined, 0, 100)
        ]);
    }
}
