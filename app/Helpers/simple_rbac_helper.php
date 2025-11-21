<?php

/**
 * Simple RBAC Helper Functions
 * Simple and powerful Role-Based Access Control
 */

if (!function_exists('can_access')) {
    /**
     * Simple permission check - Check if user can access a module with specific level
     * 
     * @param string $module Module name (admin, marketing, service, etc.)
     * @param string $level Permission level (view, edit, full)
     * @param int|null $user_id User ID (optional, defaults to current user)
     * @return bool
     */
    function can_access($module, $level = 'view', $user_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        if (!$user_id) {
            return false;
        }

        // Super admin bypass
        $userRole = session()->get('role');
        if ($userRole && in_array(strtolower($userRole), ['super_admin', 'superadministrator'])) {
            return true;
        }

        $db = \Config\Database::connect();

        try {
            // Get permission key
            $permission_key = $module . '.' . $level;
            
            // Check if user has this permission through role
            $hasPermission = $db->table('user_roles ur')
                ->join('role_permissions rp', 'rp.role_id = ur.role_id')
                ->join('permissions p', 'p.id = rp.permission_id')
                ->where('ur.user_id', $user_id)
                ->where('p.key', $permission_key)
                ->where('rp.granted', 1)
                ->countAllResults();

            return $hasPermission > 0;

        } catch (\Exception $e) {
            log_message('error', 'Simple RBAC Helper Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('can_view')) {
    /**
     * Check if user can view a module
     */
    function can_view($module, $user_id = null)
    {
        return can_access($module, 'view', $user_id);
    }
}

if (!function_exists('can_edit')) {
    /**
     * Check if user can edit a module
     */
    function can_edit($module, $user_id = null)
    {
        return can_access($module, 'edit', $user_id);
    }
}

if (!function_exists('can_manage')) {
    /**
     * Check if user has full access to a module
     */
    function can_manage($module, $user_id = null)
    {
        return can_access($module, 'full', $user_id);
    }
}

if (!function_exists('can_create')) {
    /**
     * Check if user can create data in a module
     */
    function can_create($module, $user_id = null)
    {
        return get_user_permission_level($module, $user_id) >= 2;
    }
}

if (!function_exists('can_export')) {
    /**
     * Check if user can export data from a module
     */
    function can_export($module, $user_id = null)
    {
        return get_user_permission_level($module, $user_id) >= 3;
    }
}

if (!function_exists('get_user_permissions')) {
    /**
     * Get all permissions for current user
     */
    function get_user_permissions($user_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        if (!$user_id) {
            return [];
        }

        $db = \Config\Database::connect();

        try {
            $permissions = $db->table('user_roles ur')
                ->join('role_permissions rp', 'rp.role_id = ur.role_id')
                ->join('permissions p', 'p.id = rp.permission_id')
                ->where('ur.user_id', $user_id)
                ->where('rp.granted', 1)
                ->select('p.key, p.name, p.module, p.level')
                ->get()
                ->getResultArray();

            return $permissions;

        } catch (\Exception $e) {
            log_message('error', 'Get User Permissions Error: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('get_user_permission_level')) {
    /**
     * Get user's permission level for a specific module
     * Returns: 0 (no access), 1 (view), 2 (edit), 3 (full)
     */
    function get_user_permission_level($module, $user_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        // DEBUG: Log function call
        log_message('debug', "get_user_permission_level called: module=$module, user_id=$user_id");
        
        if (!$user_id) {
            log_message('debug', "get_user_permission_level: No user_id, returning 0");
            return 0;
        }

        // Super admin bypass
        $userRole = session()->get('role');
        if ($userRole && in_array(strtolower($userRole), ['super_admin', 'superadministrator'])) {
            log_message('debug', "get_user_permission_level: Super admin bypass, returning 3");
            return 3;
        }

        $db = \Config\Database::connect();

        try {
            $permission = $db->table('user_roles ur')
                ->join('role_permissions rp', 'rp.role_id = ur.role_id')
                ->join('permissions p', 'p.id = rp.permission_id')
                ->where('ur.user_id', $user_id)
                ->where('p.module', $module)
                ->where('rp.granted', 1)
                ->select('p.level')
                ->orderBy('p.level', 'DESC')
                ->get()
                ->getRowArray();

            $level = $permission ? (int)$permission['level'] : 0;
            log_message('debug', "get_user_permission_level: Found permission level $level for module $module");
            return $level;

        } catch (\Exception $e) {
            log_message('error', 'Get User Permission Level Error: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('get_module_permissions')) {
    /**
     * Get all permissions for a specific module
     */
    function get_module_permissions($module)
    {
        $db = \Config\Database::connect();

        try {
            $permissions = $db->table('permissions')
                ->where('module', $module)
                ->orderBy('level', 'ASC')
                ->get()
                ->getResultArray();

            return $permissions;

        } catch (\Exception $e) {
            log_message('error', 'Get Module Permissions Error: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('get_all_modules')) {
    /**
     * Get all available modules
     */
    function get_all_modules()
    {
        $db = \Config\Database::connect();

        try {
            $modules = $db->table('permissions')
                ->select('module')
                ->distinct()
                ->orderBy('module', 'ASC')
                ->get()
                ->getResultArray();

            return array_column($modules, 'module');

        } catch (\Exception $e) {
            log_message('error', 'Get All Modules Error: ' . $e->getMessage());
            return [];
        }
    }
}