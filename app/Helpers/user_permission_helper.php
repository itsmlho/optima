<?php

if (!function_exists('grantUserPermission')) {
    /**
     * Grant or revoke specific permission to user
     * 
     * @param int $userId User ID
     * @param string $permissionKey Permission key (e.g., 'warehouse.inventory_unit.view')
     * @param bool $granted TRUE to grant, FALSE to explicitly revoke
     * @param array $options Additional options:
     *   - 'reason' => 'Why this permission is granted'
     *   - 'assigned_by' => User ID who assigned this
     *   - 'expires_at' => '2026-12-31 23:59:59' (temporary permission)
     *   - 'is_temporary' => true/false
     *   - 'division_id' => null (for division-scoped permission)
     * @return bool Success status
     */
    function grantUserPermission(int $userId, string $permissionKey, bool $granted = true, array $options = []): bool
    {
        $db = \Config\Database::connect();
        
        // Get permission ID
        $permission = $db->table('permissions')
            ->where('key_name', $permissionKey)
            ->get()
            ->getRow();
        
        if (!$permission) {
            log_message('error', "Permission key not found: {$permissionKey}");
            return false;
        }
        
        // Check if user permission already exists
        $existing = $db->table('user_permissions')
            ->where('user_id', $userId)
            ->where('permission_id', $permission->id)
            ->get()
            ->getRow();
        
        $data = [
            'user_id' => $userId,
            'permission_id' => $permission->id,
            'granted' => $granted ? 1 : 0,
            'reason' => $options['reason'] ?? null,
            'assigned_by' => $options['assigned_by'] ?? session()->get('user_id'),
            'assigned_at' => date('Y-m-d H:i:s'),
            'expires_at' => $options['expires_at'] ?? null,
            'is_temporary' => $options['is_temporary'] ?? 0,
            'division_id' => $options['division_id'] ?? null,
        ];
        
        try {
            if ($existing) {
                // Update existing permission
                $db->table('user_permissions')
                    ->where('id', $existing->id)
                    ->update($data);
            } else {
                // Insert new permission
                $db->table('user_permissions')->insert($data);
            }
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to grant user permission: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('revokeUserPermission')) {
    /**
     * Explicitly revoke permission from user (even if role has it)
     * 
     * @param int $userId User ID
     * @param string $permissionKey Permission key
     * @param string $reason Why permission is revoked
     * @return bool Success status
     */
    function revokeUserPermission(int $userId, string $permissionKey, string $reason = ''): bool
    {
        return grantUserPermission($userId, $permissionKey, false, [
            'reason' => $reason ?: 'Permission explicitly revoked'
        ]);
    }
}

if (!function_exists('clearUserPermission')) {
    /**
     * Remove user-specific permission override (fallback to role permissions)
     * 
     * @param int $userId User ID
     * @param string $permissionKey Permission key
     * @return bool Success status
     */
    function clearUserPermission(int $userId, string $permissionKey): bool
    {
        $db = \Config\Database::connect();
        
        $permission = $db->table('permissions')
            ->where('key_name', $permissionKey)
            ->get()
            ->getRow();
        
        if (!$permission) {
            return false;
        }
        
        try {
            $db->table('user_permissions')
                ->where('user_id', $userId)
                ->where('permission_id', $permission->id)
                ->delete();
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to clear user permission: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('getUserPermissionOverrides')) {
    /**
     * Get all user-specific permission overrides
     * 
     * @param int $userId User ID
     * @param bool $includeExpired Include expired permissions
     * @return array List of user permissions with details
     */
    function getUserPermissionOverrides(int $userId, bool $includeExpired = false): array
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('user_permissions up')
            ->select('p.key_name, p.display_name, p.module, p.page, p.action, up.granted, up.reason, up.expires_at, up.is_temporary, up.assigned_at, u.username as assigned_by_username')
            ->join('permissions p', 'p.id = up.permission_id')
            ->join('user u', 'u.id = up.assigned_by', 'left')
            ->where('up.user_id', $userId);
        
        if (!$includeExpired) {
            $builder->groupStart()
                ->where('up.expires_at IS NULL')
                ->orWhere('up.expires_at >', date('Y-m-d H:i:s'))
                ->groupEnd();
        }
        
        return $builder->orderBy('up.assigned_at', 'DESC')->get()->getResultArray();
    }
}

if (!function_exists('grantBulkUserPermissions')) {
    /**
     * Grant multiple permissions to user at once
     * 
     * @param int $userId User ID
     * @param array $permissionKeys Array of permission keys
     * @param array $options Common options for all permissions
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    function grantBulkUserPermissions(int $userId, array $permissionKeys, array $options = []): array
    {
        $success = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($permissionKeys as $key) {
            if (grantUserPermission($userId, $key, true, $options)) {
                $success++;
            } else {
                $failed++;
                $errors[] = "Failed to grant: {$key}";
            }
        }
        
        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors
        ];
    }
}

if (!function_exists('cleanupExpiredPermissions')) {
    /**
     * Remove expired temporary permissions (for cron job)
     * 
     * @return int Number of permissions cleaned up
     */
    function cleanupExpiredPermissions(): int
    {
        $db = \Config\Database::connect();
        
        try {
            $builder = $db->table('user_permissions')
                ->where('expires_at IS NOT NULL')
                ->where('expires_at <', date('Y-m-d H:i:s'));
            
            $count = $builder->countAllResults(false);
            $builder->delete();
            
            return $count;
        } catch (\Exception $e) {
            log_message('error', 'Failed to cleanup expired permissions: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('getPermissionSource')) {
    /**
     * Get where permission comes from (for debugging/audit)
     * 
     * @param int $userId User ID
     * @param string $permissionKey Permission key
     * @return array ['has_permission' => bool, 'source' => string, 'details' => array]
     */
    function getPermissionSource(int $userId, string $permissionKey): array
    {
        // Check admin bypass
        $userRole = session()->get('role');
        if (!empty($userRole) && in_array(strtolower($userRole), ['admin', 'superadmin', 'super_admin', 'administrator', 'super administrator'])) {
            return [
                'has_permission' => true,
                'source' => 'admin_bypass',
                'details' => ['role' => $userRole]
            ];
        }
        
        $db = \Config\Database::connect();
        
        // Check user-specific permission
        $userPerm = $db->query("
            SELECT up.granted, up.reason, up.expires_at, up.is_temporary
            FROM user_permissions up
            INNER JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ? 
            AND p.key_name = ?
            AND (up.expires_at IS NULL OR up.expires_at > NOW())
            ORDER BY up.created_at DESC
            LIMIT 1
        ", [$userId, $permissionKey])->getRowArray();
        
        if ($userPerm !== null) {
            return [
                'has_permission' => (bool) $userPerm['granted'],
                'source' => $userPerm['granted'] ? 'user_grant' : 'user_revoke',
                'details' => $userPerm
            ];
        }
        
        // Check role permission
        $rolePerm = $db->query("
            SELECT r.name as role_name, rp.granted
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            INNER JOIN roles r ON r.id = ur.role_id
            WHERE ur.user_id = ? 
            AND p.key_name = ?
            AND rp.granted = 1
            AND ur.is_active = 1
            LIMIT 1
        ", [$userId, $permissionKey])->getRowArray();
        
        if ($rolePerm) {
            return [
                'has_permission' => true,
                'source' => 'role_grant',
                'details' => $rolePerm
            ];
        }
        
        return [
            'has_permission' => false,
            'source' => 'no_permission',
            'details' => []
        ];
    }
}
