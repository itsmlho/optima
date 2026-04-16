<?php

/**
 * ENHANCED PERMISSION HELPER FUNCTIONS
 * Comprehensive permission management system dengan struktur granular
 * Supports module.page.action.subaction.component permission structure
 */

if (!function_exists('hasPermission')) {
    /**
     * Resolve legacy permission keys to canonical keys.
     *
     * @param string $permissionKey
     * @return array<string>
     */
    function resolvePermissionAliases(string $permissionKey): array
    {
        $aliases = [
            'view_marketing' => [
                'marketing.audit_approval.view',
                'marketing.quotation.view',
                'marketing.customer.view'
            ],
            'approve_marketing' => [
                'marketing.audit_approval.approve',
                'marketing.quotation.approve'
            ],
            'view_service' => [
                'service.unit_audit.view',
                'service.unit_audit_location.view',
                'service.workorder.navigation'
            ],
            'create_service' => [
                'service.unit_audit.create',
                'service.unit_audit_location.create',
                'service.workorder.create'
            ],
            'divisions.view' => [
                'settings.division.view'
            ],
            'positions.view' => [
                'settings.role.view',
                'settings.user.view'
            ],
            'logs.view' => [
                'settings.system.view',
                'admin.access'
            ],
            'service.area_management.view' => [
                'service.area_management.view',
                'service.area_management.navigation'
            ],
            'marketing.customer.update' => [
                'marketing.customer.edit'
            ]
        ];

        $keys = [$permissionKey];
        if (isset($aliases[$permissionKey])) {
            $keys = array_merge($keys, $aliases[$permissionKey]);
        }

        return array_values(array_unique($keys));
    }

    /**
     * Check if user has specific permission
     * 
     * @param string $permissionKey Permission in format module.page.action[.subaction][.component]
     * @param int|null $userId Optional user ID, defaults to current session user
     * @return bool
     */
    function hasPermission(string $permissionKey, ?int $userId = null): bool
    {
        $permissionKeys = resolvePermissionAliases($permissionKey);

        if (!$userId) {
            $userId = session()->get('user_id');
        }

        if (!$userId) {
            return false;
        }

        // ✅ BYPASS: Allow admin and superadmin full access
        $userRole = session()->get('role');
        if (!empty($userRole) && in_array(strtolower($userRole), ['admin', 'superadmin', 'super_admin', 'administrator', 'super administrator'])) {
            return true;
        }

        $db = \Config\Database::connect();

        $placeholders = implode(',', array_fill(0, count($permissionKeys), '?'));
        $queryKeys = array_values($permissionKeys);
        
        // ═══════════════════════════════════════════════════════════════
        // PRIORITY 1: Check User-Specific Permissions (HIGHEST PRIORITY)
        // ═══════════════════════════════════════════════════════════════
        $userPermissionQuery = $db->query("
            SELECT up.granted
            FROM user_permissions up
            INNER JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ? 
            AND p.key_name IN ({$placeholders})
            AND (up.expires_at IS NULL OR up.expires_at > NOW())
            ORDER BY up.created_at DESC
            LIMIT 1
        ", array_merge([$userId], $queryKeys));
        
        $userPermission = safe_get_row($userPermissionQuery);
        
        if ($userPermission !== null) {
            // User-specific permission found
            // granted = 1 → ALLOW (override role)
            // granted = 0 → DENY (revoke, even if role has it)
            return isset($userPermission['granted']) ? (bool) $userPermission['granted'] : false;
        }

        // Broad module gate fallback (non-admin modules):
        // jika diminta `{module}.access` untuk modul operasional, selaraskan dengan hasModuleAccess().
        // Sengaja TIDAK mencakup `admin.access` agar gate admin tetap ketat.
        if (preg_match('/^([a-z0-9_]+)\.access$/', $permissionKey, $m)) {
            $moduleFromKey = $m[1];
            static $accessKeysAlignedWithModule = ['marketing', 'warehouse', 'purchasing', 'service'];
            if (in_array($moduleFromKey, $accessKeysAlignedWithModule, true) && hasModuleAccess($moduleFromKey, $userId)) {
                return true;
            }
        }
        
        // ═══════════════════════════════════════════════════════════════
        // PRIORITY 2: Check Role Permissions (DEFAULT BEHAVIOR)
        // ═══════════════════════════════════════════════════════════════
        $rolePermissionQuery = $db->query("
            SELECT COUNT(*) as count 
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND p.key_name IN ({$placeholders})
            AND rp.granted = 1
            AND ur.is_active = 1
        ", array_merge([$userId], $queryKeys));
        
        $result = safe_get_row($rolePermissionQuery);

        return $result && isset($result['count']) && $result['count'] > 0;
    }
}

if (!function_exists('hasModuleAccess')) {
    /**
     * Check if user has any access to a module
     * 
     * @param string $module Module name (e.g., 'marketing', 'service')
     * @param int|null $userId Optional user ID
     * @return bool
     */
    function hasModuleAccess(string $module, ?int $userId = null): bool
    {
        if (!$userId) {
            $userId = session()->get('user_id');
        }

        if (!$userId) {
            return false;
        }

        // ✅ BYPASS: Allow admin and superadmin full access
        $userRole = session()->get('role');
        if (!empty($userRole) && in_array(strtolower($userRole), ['admin', 'superadmin', 'super_admin', 'administrator', 'super administrator'])) {
            return true;
        }

        // ✅ BYPASS: Module-specific admin roles
        $moduleAdminRoles = [
            'service'    => ['admin_service_pusat', 'admin_service_area', 'head_service', 'supervisor_service', 'manager-service-area'],
            'marketing'  => ['head_marketing', 'staff_marketing'],
            'purchasing' => ['head_purchasing', 'staff_purchasing'],
            'warehouse'  => ['head_warehouse', 'staff_warehouse'],
        ];
        if (isset($moduleAdminRoles[$module]) && !empty($userRole) && in_array(strtolower($userRole), $moduleAdminRoles[$module], true)) {
            return true;
        }

        $db = \Config\Database::connect();

        // Direct role-slug bypass: does NOT depend on session role, works with stale sessions too
        $moduleAdminSlugs = [
            'service'    => "'admin_service_pusat','admin_service_area','head_service','supervisor_service','manager-service-area','staff_service'",
            'marketing'  => "'head_marketing','staff_marketing'",
            'purchasing' => "'head_purchasing','staff_purchasing'",
            'warehouse'  => "'head_warehouse','staff_warehouse'",
        ];
        if (isset($moduleAdminSlugs[$module])) {
            $slugRow = $db->query("
                SELECT r.slug FROM user_roles ur
                JOIN roles r ON r.id = ur.role_id
                WHERE ur.user_id = ? AND ur.is_active = 1
                AND r.slug IN ({$moduleAdminSlugs[$module]})
                LIMIT 1
            ", [$userId])->getRowArray();
            if ($slugRow) return true;
        }

        $moduleAccessQuery = $db->query("
            SELECT COUNT(*) as count 
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND p.module = ?
            AND rp.granted = 1
            AND ur.is_active = 1
        ", [$userId, $module]);
        
        $result = safe_get_row($moduleAccessQuery);
        if ($result && isset($result['count']) && $result['count'] > 0) {
            return true;
        }

        // Also check user-specific overrides (custom grants via user_permissions)
        $userModuleQuery = $db->query("
            SELECT COUNT(*) as count
            FROM user_permissions up
            INNER JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ?
            AND p.module = ?
            AND up.granted = 1
            AND (up.expires_at IS NULL OR up.expires_at > NOW())
        ", [$userId, $module]);

        $userResult = safe_get_row($userModuleQuery);
        return $userResult && isset($userResult['count']) && $userResult['count'] > 0;
    }
}

if (!function_exists('hasPageAccess')) {
    /**
     * Check if user has any access to a specific page
     * 
     * @param string $module Module name
     * @param string $page Page name
     * @param int|null $userId Optional user ID
     * @return bool
     */
    function hasPageAccess(string $module, string $page, ?int $userId = null): bool
    {
        if (!$userId) {
            $userId = session()->get('user_id');
        }

        if (!$userId) {
            return false;
        }

        $db = \Config\Database::connect();
        
        $pageAccessQuery = $db->query("
            SELECT COUNT(*) as count 
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND p.module = ?
            AND p.page = ?
            AND rp.granted = 1
            AND ur.is_active = 1
        ", [$userId, $module, $page]);
        
        $result = safe_get_row($pageAccessQuery);
        if ($result && isset($result['count']) && $result['count'] > 0) {
            return true;
        }

        // Also check user-specific overrides (custom grants via user_permissions)
        $userPageQuery = $db->query("
            SELECT COUNT(*) as count
            FROM user_permissions up
            INNER JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ?
            AND p.module = ?
            AND p.page = ?
            AND up.granted = 1
            AND (up.expires_at IS NULL OR up.expires_at > NOW())
        ", [$userId, $module, $page]);

        $userResult = safe_get_row($userPageQuery);
        return $userResult && isset($userResult['count']) && $userResult['count'] > 0;
    }
}

if (!function_exists('canPerformAction')) {
    /**
     * Check if user can perform specific action on a page
     * 
     * @param string $module Module name
     * @param string $page Page name
     * @param string $action Action name (create, edit, delete, etc.)
     * @param int|null $userId Optional user ID
     * @return bool
     */
    function canPerformAction(string $module, string $page, string $action, ?int $userId = null): bool
    {
        $permissionKey = "{$module}.{$page}.{$action}";
        return hasPermission($permissionKey, $userId);
    }
}

if (!function_exists('canNavigateTo')) {
    /**
     * Check if user can see navigation menu for a page
     * 
     * @param string $module Module name
     * @param string $page Page name
     * @param int|null $userId Optional user ID
     * @return bool
     */
    function canNavigateTo(string $module, string $page, ?int $userId = null): bool
    {
        $permissionKey = "{$module}.{$page}.navigation";
        return hasPermission($permissionKey, $userId);
    }
}

if (!function_exists('getUserPermissions')) {
    /**
     * Get all permissions for a user
     * 
     * @param int|null $userId Optional user ID
     * @return array
     */
    function getUserPermissions(?int $userId = null): array
    {
        if (!$userId) {
            $userId = session()->get('user_id');
        }

        if (!$userId) {
            return [];
        }

        $db = \Config\Database::connect();
        
        $userPermsQuery = $db->query("
            SELECT p.key_name, p.display_name, p.module, p.page, p.action, p.category
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND rp.granted = 1
            ORDER BY p.module, p.page, p.action
        ", [$userId]);
        
        $result = safe_get_result($userPermsQuery);

        return $result;
    }
}

if (!function_exists('getUserModulePermissions')) {
    /**
     * Get permissions for specific module
     * 
     * @param string $module Module name
     * @param int|null $userId Optional user ID
     * @return array
     */
    function getUserModulePermissions(string $module, ?int $userId = null): array
    {
        if (!$userId) {
            $userId = session()->get('user_id');
        }

        if (!$userId) {
            return [];
        }

        $db = \Config\Database::connect();
        
        $modulePermsQuery = $db->query("
            SELECT p.key_name, p.display_name, p.page, p.action, p.category
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND p.module = ?
            AND rp.granted = 1
            ORDER BY p.page, p.action
        ", [$userId, $module]);
        
        $result = safe_get_result($modulePermsQuery);

        return $result;
    }
}

if (!function_exists('isSystemAdmin')) {
    /**
     * Check if user is system administrator (has full access)
     * 
     * @param int|null $userId Optional user ID
     * @return bool
     */
    function isSystemAdmin(?int $userId = null): bool
    {
        if (!$userId) {
            $userId = session()->get('user_id');
        }

        if (!$userId) {
            return false;
        }

        $db = \Config\Database::connect();
        
        $adminCheckQuery = $db->query("
            SELECT COUNT(*) as count 
            FROM user_roles ur
            INNER JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ? 
            AND r.name IN ('Super Administrator', 'Administrator')
        ", [$userId]);
        
        $result = safe_get_row($adminCheckQuery);

        return $result && isset($result['count']) && $result['count'] > 0;
    }
}

// Legacy compatibility functions
if (!function_exists('can_view')) {
    /**
     * Legacy compatibility function for old permission system
     * 
     * @param string $moduleOrPermission Module name or permission key
     * @param int|null $userId Optional user ID
     * @return bool
     */
    function can_view(string $moduleOrPermission, ?int $userId = null): bool
    {
        // If it's a module name, check module access
        if (!str_contains($moduleOrPermission, '.')) {
            return hasModuleAccess($moduleOrPermission, $userId);
        }
        
        // If it's a full permission key, check specific permission
        return hasPermission($moduleOrPermission, $userId);
    }
}

if (!function_exists('can_edit')) {
    /**
     * Legacy compatibility - check edit permission
     */
    function can_edit(string $moduleOrPage, ?int $userId = null): bool
    {
        if (str_contains($moduleOrPage, '.')) {
            return hasPermission($moduleOrPage, $userId);
        }

        // Delegate to centralized module-action evaluator when available
        if (!function_exists('can_module_action')) {
            helper('rbac');
        }
        if (function_exists('can_module_action')) {
            return can_module_action($moduleOrPage, ['edit', 'delete', 'manage'], $userId);
        }

        // Fallback legacy behavior
        return hasPermission("{$moduleOrPage}.{$moduleOrPage}.edit", $userId);
    }
}

if (!function_exists('can_create')) {
    /**
     * Legacy compatibility - check create permission
     */
    function can_create(string $moduleOrPage, ?int $userId = null): bool
    {
        if (str_contains($moduleOrPage, '.')) {
            return hasPermission($moduleOrPage, $userId);
        }

        // Delegate to centralized module-action evaluator when available
        if (!function_exists('can_module_action')) {
            helper('rbac');
        }
        if (function_exists('can_module_action')) {
            return can_module_action($moduleOrPage, ['create', 'edit', 'delete', 'manage'], $userId);
        }

        // Fallback legacy behavior
        return hasPermission("{$moduleOrPage}.{$moduleOrPage}.create", $userId);
    }
}

if (!function_exists('can_delete')) {
    /**
     * Legacy compatibility - check delete permission
     */
    function can_delete(string $moduleOrPage, ?int $userId = null): bool
    {
        if (str_contains($moduleOrPage, '.')) {
            return hasPermission($moduleOrPage, $userId);
        }

        // Delegate to centralized module-action evaluator when available
        if (!function_exists('can_module_action')) {
            helper('rbac');
        }
        if (function_exists('can_module_action')) {
            return can_module_action($moduleOrPage, ['delete', 'manage'], $userId);
        }

        // Fallback legacy behavior
        return hasPermission("{$moduleOrPage}.{$moduleOrPage}.delete", $userId);
    }
}

if (!function_exists('can_export')) {
    /**
     * Legacy compatibility - check export permission.
     */
    function can_export(string $moduleOrPage, ?int $userId = null): bool
    {
        if (str_contains($moduleOrPage, '.')) {
            return hasPermission($moduleOrPage, $userId);
        }

        if (!function_exists('can_module_action')) {
            helper('rbac');
        }
        if (function_exists('can_module_action')) {
            return can_module_action($moduleOrPage, ['export', 'view', 'edit', 'delete', 'manage'], $userId);
        }

        return hasPermission("{$moduleOrPage}.{$moduleOrPage}.export", $userId);
    }
}

// Enhanced permission checking functions
if (!function_exists('checkPermissionOr403')) {
    /**
     * Check permission or throw 403 error
     * 
     * @param string $permissionKey Permission key
     * @param int|null $userId Optional user ID
     * @throws \CodeIgniter\Exceptions\PageNotFoundException
     */
    function checkPermissionOr403(string $permissionKey, ?int $userId = null): void
    {
        if (!hasPermission($permissionKey, $userId)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Access denied. Permission required: {$permissionKey}");
        }
    }
}

if (!function_exists('getModuleDisplayName')) {
    /**
     * Get human readable module name
     * 
     * @param string $module Module name
     * @return string
     */
    function getModuleDisplayName(string $module): string
    {
        $moduleNames = [
            'marketing' => 'Marketing',
            'service' => 'Service',
            'purchasing' => 'Purchasing', 
            'warehouse' => 'Warehouse & Assets',
            'accounting' => 'Accounting',
            'operational' => 'Operational',
            'perizinan' => 'Perizinan',
            'admin' => 'Administration'
        ];
        
        return $moduleNames[$module] ?? ucfirst($module);
    }
}

if (!function_exists('getCategoryDisplayName')) {
    /**
     * Get human readable category name
     * 
     * @param string $category Category name
     * @return string
     */
    function getCategoryDisplayName(string $category): string
    {
        $categoryNames = [
            'navigation' => 'Navigation',
            'read' => 'View/Read',
            'write' => 'Create/Edit',
            'delete' => 'Delete',
            'export' => 'Export',
            'action' => 'Actions'
        ];
        
        return $categoryNames[$category] ?? ucfirst($category);
    }
}