<?php

/**
 * ENHANCED PERMISSION HELPER FUNCTIONS
 * Comprehensive permission management system dengan struktur granular
 * Supports module.page.action.subaction.component permission structure
 */

if (!function_exists('hasPermission')) {
    /**
     * Check if user has specific permission
     * 
     * @param string $permissionKey Permission in format module.page.action[.subaction][.component]
     * @param int|null $userId Optional user ID, defaults to current session user
     * @return bool
     */
    function hasPermission(string $permissionKey, ?int $userId = null): bool
    {
        if (!$userId) {
            $userId = session()->get('user_id');
        }

        if (!$userId) {
            return false;
        }

        $db = \Config\Database::connect();
        
        // Check if user has permission through role
        $result = $db->query("
            SELECT COUNT(*) as count 
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND p.key_name = ?
            AND rp.granted = 1
        ", [$userId, $permissionKey])->getRowArray();

        return $result && $result['count'] > 0;
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

        $db = \Config\Database::connect();
        
        $result = $db->query("
            SELECT COUNT(*) as count 
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND p.module = ?
            AND rp.granted = 1
        ", [$userId, $module])->getRowArray();

        return $result && $result['count'] > 0;
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
        
        $result = $db->query("
            SELECT COUNT(*) as count 
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND p.module = ?
            AND p.page = ?
            AND rp.granted = 1
        ", [$userId, $module, $page])->getRowArray();

        return $result && $result['count'] > 0;
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
        
        $result = $db->query("
            SELECT p.key_name, p.display_name, p.module, p.page, p.action, p.category
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND rp.granted = 1
            ORDER BY p.module, p.page, p.action
        ", [$userId])->getResultArray();

        return $result ?: [];
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
        
        $result = $db->query("
            SELECT p.key_name, p.display_name, p.page, p.action, p.category
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ? 
            AND p.module = ?
            AND rp.granted = 1
            ORDER BY p.page, p.action
        ", [$userId, $module])->getResultArray();

        return $result ?: [];
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
        
        $result = $db->query("
            SELECT COUNT(*) as count 
            FROM user_roles ur
            INNER JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ? 
            AND r.name IN ('Super Administrator', 'Administrator')
        ", [$userId])->getRowArray();

        return $result && $result['count'] > 0;
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
        
        // Try to guess page from module for legacy support
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
        
        // Try to guess page from module for legacy support
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
        
        // Try to guess page from module for legacy support
        return hasPermission("{$moduleOrPage}.{$moduleOrPage}.delete", $userId);
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