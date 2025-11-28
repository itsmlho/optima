<?php

/**
 * RBAC Helper Functions
 * Centralized helper for Role-Based Access Control
 */

if (!function_exists('can_access')) {
    /**
     * Check if user can access a specific permission
     * Priority: Super Admin > Custom Permission (override) > Role Permission > Deny
     */
    function can_access($permission_key, $user_id = null, $division_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        // DEBUG: Log permission check
        log_message('info', "RBAC Debug - Permission: {$permission_key}, User ID: " . ($user_id ?? 'NULL'));
        
        if (!$user_id) {
            log_message('info', "RBAC Debug - No user ID found");
            return false;
        }

        // Tambahan: Super Administrator bypass (support variasi nama role)
        $userRole = session()->get('role');
        if ($userRole) {
            $roleKey = strtolower(str_replace(['_', ' '], '', trim($userRole)));
            if (in_array($roleKey, ['superadministrator', 'superadmin'])) {
                return true;
            }
        }

        $db = \Config\Database::connect();

        try {
            // 1. Check custom permission (override) first
            $customQuery = $db->table('user_permissions up')
                ->join('permissions p', 'p.id = up.permission_id')
                ->where('up.user_id', $user_id)
                ->where('p.key', $permission_key);
                
            if ($division_id) {
                $customQuery->where('up.division_id', $division_id);
            }
            
            $customPermission = $customQuery->orderBy('up.id', 'DESC')->get()->getRowArray();
            
            if ($customPermission) {
                return $customPermission['granted'] == 1;
            }

            // 2. Check role permission
            $rolePermission = $db->table('user_roles ur')
                ->join('role_permissions rp', 'rp.role_id = ur.role_id')
                ->join('permissions p', 'p.id = rp.permission_id')
                ->where('ur.user_id', $user_id)
                ->where('p.key', $permission_key)
                ->countAllResults();

            // DEBUG: Log role permission result
            log_message('info', "RBAC Debug - Role permission count for {$permission_key}: {$rolePermission}");
            
            return $rolePermission > 0;

        } catch (\Exception $e) {
            log_message('error', 'RBAC Helper Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_permission_level')) {
    /**
     * Get permission access level for user
     * Returns: 'none', 'view', 'edit', 'delete', 'manage'
     */
    function get_permission_level($permission_key, $user_id = null, $division_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        if (!$user_id) {
            return 'none';
        }

        // Super Administrator has full access
        $userRole = session()->get('role');
        if ($userRole) {
            $roleKey = strtolower(str_replace(['_', ' '], '', trim($userRole)));
            if (in_array($roleKey, ['superadministrator', 'superadmin'])) {
                return 'manage';
            }
        }

        $db = \Config\Database::connect();

        try {
            // Check for specific permission levels
            $permissionLevels = [
                $permission_key . '.manage' => 'manage',
                $permission_key . '.delete' => 'delete', 
                $permission_key . '.edit' => 'edit',
                $permission_key . '.view' => 'view',
                $permission_key => 'view' // Default to view if just permission key
            ];

            foreach ($permissionLevels as $permKey => $level) {
                if (can_access($permKey, $user_id, $division_id)) {
                    return $level;
                }
            }

            return 'none';

        } catch (\Exception $e) {
            log_message('error', 'Permission Level Error: ' . $e->getMessage());
            return 'none';
        }
    }
}

if (!function_exists('can_view')) {
    /**
     * Check if user can view (read-only access)
     */
    function can_view($permission_key, $user_id = null, $division_id = null)
    {
        $level = get_permission_level($permission_key, $user_id, $division_id);
        return in_array($level, ['view', 'edit', 'delete', 'manage']);
    }
}

if (!function_exists('can_edit')) {
    /**
     * Check if user can edit
     */
    function can_edit($permission_key, $user_id = null, $division_id = null)
    {
        $level = get_permission_level($permission_key, $user_id, $division_id);
        return in_array($level, ['edit', 'delete', 'manage']);
    }
}

if (!function_exists('can_delete')) {
    /**
     * Check if user can delete
     */
    function can_delete($permission_key, $user_id = null, $division_id = null)
    {
        $level = get_permission_level($permission_key, $user_id, $division_id);
        return in_array($level, ['delete', 'manage']);
    }
}

if (!function_exists('can_manage')) {
    /**
     * Check if user can manage (full access)
     */
    function can_manage($permission_key, $user_id = null, $division_id = null)
    {
        $level = get_permission_level($permission_key, $user_id, $division_id);
        return $level === 'manage';
    }
}

if (!function_exists('user_has_role')) {
    /**
     * Check if user has specific role
     */
    function user_has_role($role_name, $user_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        if (!$user_id) {
            return false;
        }

        $db = \Config\Database::connect();

        try {
            $hasRole = $db->table('user_roles ur')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('ur.user_id', $user_id)
                ->where('r.name', $role_name)
                ->countAllResults();

            return $hasRole > 0;

        } catch (\Exception $e) {
            log_message('error', 'RBAC Role Check Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('user_in_division')) {
    /**
     * Check if user is in specific division
     */
    function user_in_division($division_code, $user_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        if (!$user_id) {
            return false;
        }

        $db = \Config\Database::connect();

        try {
            $inDivision = $db->table('user_divisions ud')
                ->join('divisions d', 'd.id = ud.division_id')
                ->where('ud.user_id', $user_id)
                ->where('d.code', $division_code)
                ->countAllResults();

            return $inDivision > 0;

        } catch (\Exception $e) {
            log_message('error', 'RBAC Division Check Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('is_division_head')) {
    /**
     * Check if user is division head
     */
    function is_division_head($division_code = null, $user_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        if (!$user_id) {
            return false;
        }

        $db = \Config\Database::connect();

        try {
            $query = $db->table('user_divisions ud')
                ->join('divisions d', 'd.id = ud.division_id')
                ->where('ud.user_id', $user_id)
                ->where('ud.is_head', 1);
                
            if ($division_code) {
                $query->where('d.code', $division_code);
            }

            return $query->countAllResults() > 0;

        } catch (\Exception $e) {
            log_message('error', 'RBAC Division Head Check Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_user_permissions')) {
    /**
     * Get all effective permissions for user
     */
    function get_user_permissions($user_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        if (!$user_id) {
            return [];
        }

        $db = \Config\Database::connect();

        try {
            // Get role permissions
            $rolePermissions = $db->table('user_roles ur')
                ->join('role_permissions rp', 'rp.role_id = ur.role_id')
                ->join('permissions p', 'p.id = rp.permission_id')
                ->where('ur.user_id', $user_id)
                ->select('p.key, p.name, p.description, "role" as source')
                ->get()->getResultArray();

            // Get custom permissions
            $customPermissions = $db->table('user_permissions up')
                ->join('permissions p', 'p.id = up.permission_id')
                ->where('up.user_id', $user_id)
                ->select('p.key, p.name, p.description, up.granted, "custom" as source')
                ->get()->getResultArray();

            // Merge and resolve conflicts (custom overrides role)
            $effective = [];
            
            // Start with role permissions
            foreach ($rolePermissions as $perm) {
                $effective[$perm['key']] = [
                    'key' => $perm['key'],
                    'name' => $perm['name'],
                    'description' => $perm['description'],
                    'granted' => true,
                    'source' => $perm['source']
                ];
            }
            
            // Apply custom overrides
            foreach ($customPermissions as $perm) {
                $effective[$perm['key']] = [
                    'key' => $perm['key'],
                    'name' => $perm['name'],
                    'description' => $perm['description'],
                    'granted' => $perm['granted'] == 1,
                    'source' => $perm['source']
                ];
            }

            return array_values($effective);

        } catch (\Exception $e) {
            log_message('error', 'RBAC Get Permissions Error: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('get_user_menu_permissions')) {
    /**
     * Get menu permissions based on user divisions and permissions
     */
    function get_user_menu_permissions($user_id = null)
    {
        $user_id = $user_id ?? session()->get('user_id');
        
        if (!$user_id) {
            return [];
        }

        try {
            return [
                // Dashboard
                'dashboard' => can_access('dashboard.access', $user_id),
                
                // Administration
                'admin' => [
                    'access' => can_access('admin.access', $user_id),
                    'users' => can_access('admin.user_management', $user_id),
                    'roles' => can_access('admin.role_management', $user_id),
                    'permissions' => can_access('admin.permission_management', $user_id),
                ],
                
                // Service Division
                'service' => [
                    'access' => user_in_division('SVC', $user_id) && can_access('service.access', $user_id),
                    'work_orders' => can_access('service.work_orders.view', $user_id),
                    'maintenance' => can_access('service.maintenance.view', $user_id),
                ],
                
                // Marketing Division
                'marketing' => [
                    'access' => user_in_division('MKT', $user_id) && can_access('marketing.access', $user_id),
                    'customers' => can_access('marketing.customers.view', $user_id),
                    'rentals' => can_access('marketing.rentals.view', $user_id),
                ],
                
                // Warehouse Division
                'warehouse' => [
                    'access' => user_in_division('WHS', $user_id) && can_access('warehouse.access', $user_id),
                    'inventory' => can_access('warehouse.inventory.view', $user_id),
                    'units' => can_access('warehouse.units.view', $user_id),
                ],
                
                // Purchasing Division
                'purchasing' => [
                    'access' => user_in_division('PUR', $user_id) && can_access('purchasing.access', $user_id),
                    'orders' => can_access('purchasing.orders.view', $user_id),
                    'suppliers' => can_access('purchasing.suppliers.view', $user_id),
                ],
                
                // Finance Division
                'finance' => [
                    'access' => user_in_division('FIN', $user_id) && can_access('finance.access', $user_id),
                    'invoices' => can_access('finance.invoices.view', $user_id),
                    'payments' => can_access('finance.payments.view', $user_id),
                ],
                
                // Reports
                'reports' => [
                    'access' => can_access('reports.access', $user_id),
                    'export' => can_access('reports.export', $user_id),
                ],
            ];

        } catch (\Exception $e) {
            log_message('error', 'RBAC Menu Permissions Error: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('rbac_middleware')) {
    /**
     * RBAC Middleware function for controllers
     */
    function rbac_middleware($required_permission, $user_id = null)
    {
        if (!can_access($required_permission, $user_id)) {
            $response = service('response');
            
            if (service('request')->isAJAX()) {
                return $response->setJSON(['success' => false, 'message' => 'Access denied'])
                               ->setStatusCode(403);
            }
            
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to access this resource.');
        }
        
        return true;
    }
}

if (!function_exists('can_create')) {
    /**
     * Check if user can create (edit permission level and above)
     */
    function can_create($permission_key, $user_id = null, $division_id = null)
    {
        $level = get_permission_level($permission_key, $user_id, $division_id);
        return in_array($level, ['edit', 'delete', 'manage']);
    }
}

if (!function_exists('can_export')) {
    /**
     * Check if user can export (view permission level and above)
     */
    function can_export($permission_key, $user_id = null, $division_id = null)
    {
        $level = get_permission_level($permission_key, $user_id, $division_id);
        return in_array($level, ['view', 'edit', 'delete', 'manage']);
    }
}
