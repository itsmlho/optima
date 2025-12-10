<?php

if (!function_exists('auth_user')) {
    /**
     * Get current authenticated user info for development
     */
    function auth_user()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return null;
        }
        
        return (object) [
            'id' => $session->get('user_id'),
            'username' => $session->get('username'),
            'email' => $session->get('email'),
            'role' => $session->get('role'),
            'department' => $session->get('department'),
            'position' => $session->get('position'),
            'avatar' => $session->get('avatar'),
            'status' => $session->get('status', 'active')
        ];
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in()
    {
        return session()->get('isLoggedIn') === true;
    }
}

if (!function_exists('login_user')) {
    /**
     * Login user and set session data
     */
    function login_user($userData)
    {
        $session = session();
        
        $session->set([
            'isLoggedIn' => true,
            'user_id' => $userData['id'],
            'username' => $userData['username'],
            'email' => $userData['email'],
            'role' => $userData['role'] ?? 'user',
            'department' => $userData['department'] ?? '',
            'position' => $userData['position'] ?? '',
            'avatar' => $userData['avatar'] ?? null,
            'status' => $userData['status'] ?? 'active'
        ]);
        
        return true;
    }
}

if (!function_exists('logout_user')) {
    /**
     * Logout user and clear session
     */
    function logout_user()
    {
        $session = session();
        $session->destroy();
        return true;
    }
}

if (!function_exists('quick_login_as_superadmin')) {
    /**
     * Quick login as superadmin for development
     */
    function quick_login_as_superadmin()
    {
        $session = session();
        
        $session->set([
            'isLoggedIn' => true,
            'user_id' => 1,
            'username' => 'superadmin',
            'email' => 'admin@optima.com',
            'role' => 'super_admin',
            'department' => 'IT Support',
            'position' => 'Administrator',
            'avatar' => null,
            'status' => 'active'
        ]);
        
        return true;
    }
}

if (!function_exists('get_user_division_departments')) {
    /**
     * DEPRECATED: Use get_user_area_department_scope() instead
     * Get allowed department IDs based on user's division
     * Service Diesel -> Departments: DIESEL (1) and GASOLINE (3)
     * Service Electric -> Department: ELECTRIC (2)
     * Returns null if user is not in Service Diesel or Service Electric, meaning no filter
     * 
     * @return array|null Array of department IDs to filter, or null if no filter should be applied
     */
    function get_user_division_departments()
    {
        try {
            $session = session();
            
            // Check if user is logged in
            if (!$session->get('isLoggedIn')) {
                return null;
            }
            
            $userId = $session->get('user_id');
            if (!$userId) {
                return null;
            }
            
            // Get user divisions from user_roles table (bukan user_divisions!)
            $db = \Config\Database::connect();
            
            // Check if tables exist before querying
            if (!$db->tableExists('user_roles') || !$db->tableExists('divisions')) {
                log_message('debug', 'Division tables not found, skipping division filter');
                return null;
            }
            
            $userDivisions = $db->table('user_roles ur')
                ->select('ur.division_id, d.name as division_name, d.code as division_code')
                ->join('divisions d', 'd.id = ur.division_id', 'left')
                ->where('ur.user_id', $userId)
                ->where('ur.division_id IS NOT NULL')
                ->where('ur.is_active', 1)
                ->get()
                ->getResultArray();
            
            if (empty($userDivisions)) {
                return null; // No divisions assigned, no filter
            }
            
            // Check if user is in Service Diesel or Service Electric
            $isServiceDiesel = false;
            $isServiceElectric = false;
            
            foreach ($userDivisions as $division) {
                $divName = strtolower($division['division_name'] ?? '');
                $divCode = strtolower($division['division_code'] ?? '');
                
                if (stripos($divName, 'service diesel') !== false || 
                    stripos($divCode, 'service_diesel') !== false) {
                    $isServiceDiesel = true;
                }
                
                if (stripos($divName, 'service electric') !== false || 
                    stripos($divCode, 'service_electric') !== false) {
                    $isServiceElectric = true;
                }
            }
            
            // If user is in both divisions, return null (no filter - show all)
            if ($isServiceDiesel && $isServiceElectric) {
                return null;
            }
            
            // Service Diesel -> Departments: DIESEL (1) and GASOLINE (3)
            if ($isServiceDiesel) {
                return [1, 3]; // DIESEL and GASOLINE
            }
            
            // Service Electric -> Department: ELECTRIC (2)
            if ($isServiceElectric) {
                return [2]; // ELECTRIC
            }
            
            // Not in Service Diesel or Service Electric, no filter
            return null;
        } catch (\Exception $e) {
            // Log error but don't break the application
            log_message('error', 'Error in get_user_division_departments: ' . $e->getMessage());
            return null; // Return null on error to show all data
        }
    }
}

if (!function_exists('get_user_area_department_scope')) {
    /**
     * NEW: Get user's area and department access scope based on area_employee_assignments
     * 
     * Returns:
     * - null: Full access (superadmin or no filter needed)
     * - array: ['areas' => [...], 'departments' => [...], 'has_full_access' => bool]
     * 
     * Logic:
     * - Central HQ admin: Limited to specific departments (ELECTRIC or DIESEL+GASOLINE)
     * - Branch admin: Full access to all departments (department_scope = 'ALL')
     * - Superadmin/No assignment: Full access (null)
     * 
     * @return array|null
     */
    function get_user_area_department_scope()
    {
        // TEMPORARY: Disable all scope filtering to show all data for administrators
        log_message('debug', 'Scope filtering temporarily disabled - returning null for full access');
        return null;
        
        try {
            $session = session();
            
            // Check if user is logged in
            if (!$session->get('isLoggedIn')) {
                return null;
            }
            
            $userId = $session->get('user_id');
            if (!$userId) {
                return null;
            }
            
            // Check if user is administrator - give full access
            $userRole = $session->get('role');
            log_message('debug', "User role check: " . ($userRole ?? 'NULL'));
            log_message('debug', "All session data: " . json_encode($session->get()));
            
            if (in_array($userRole, ['administrator', 'admin', 'superadmin', 'super_admin'])) {
                log_message('debug', 'Administrator detected, granting full access to all areas');
                return null; // Full access for administrators
            }
            
            $db = \Config\Database::connect();
            
            // Check if tables exist
            if (!$db->tableExists('area_employee_assignments') || !$db->tableExists('employees')) {
                log_message('debug', 'Area assignment tables not found, skipping filter');
                return null;
            }
            
            // Get user's area assignments with department scope
            $assignments = $db->table('area_employee_assignments aea')
                ->select('aea.area_id, aea.department_scope, a.area_name, a.area_type, a.area_code')
                ->join('areas a', 'a.id = aea.area_id', 'left')
                ->where('aea.employee_id', $userId)
                ->where('aea.is_active', 1)
                ->get()
                ->getResultArray();
            
            // No assignments = superadmin or full access
            if (empty($assignments)) {
                return null;
            }
            
            $result = [
                'areas' => [],
                'departments' => [],
                'has_full_access' => false
            ];
            
            foreach ($assignments as $assign) {
                $result['areas'][] = $assign['area_id'];
                
                $scope = $assign['department_scope'] ?? 'ALL';
                
                // If ANY assignment has ALL scope, user has full access
                if ($scope === 'ALL') {
                    $result['has_full_access'] = true;
                    return null; // Branch admin - no filter needed
                }
                
                // Parse scope: 'ELECTRIC', 'DIESEL', 'DIESEL,GASOLINE'
                $depts = array_map('trim', explode(',', $scope));
                foreach ($depts as $dept) {
                    if ($dept === 'ELECTRIC') $result['departments'][] = 2;
                    if ($dept === 'DIESEL') $result['departments'][] = 1;
                    if ($dept === 'GASOLINE') $result['departments'][] = 3;
                }
            }
            
            $result['areas'] = array_unique($result['areas']);
            $result['departments'] = array_unique($result['departments']);
            
            // If no departments specified, return null (full access)
            if (empty($result['departments'])) {
                return null;
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in get_user_area_department_scope: ' . $e->getMessage());
            return null; // Return null on error to show all data
        }
    }
}

if (!function_exists('apply_area_department_filter')) {
    /**
     * Helper to apply area/department filter to query builder
     * 
     * Usage:
     * $builder = $db->table('inventory_units');
     * apply_area_department_filter($builder, 'inventory_units');
     * 
     * @param object $builder CodeIgniter Query Builder instance
     * @param string $table Table name or alias
     * @param string $areaColumn Column name for area_id (default: 'area_id')
     * @param string $deptColumn Column name for departemen_id (default: 'departemen_id')
     * @return void
     */
    function apply_area_department_filter($builder, $table = null, $areaColumn = 'area_id', $deptColumn = 'departemen_id')
    {
        $scope = get_user_area_department_scope();
        
        // No filter needed
        if ($scope === null) {
            return;
        }
        
        $prefix = $table ? "$table." : '';
        
        // Apply area filter if areas are specified
        if (!empty($scope['areas'])) {
            $builder->whereIn($prefix . $areaColumn, $scope['areas']);
        }
        
        // Apply department filter if departments are specified
        if (!empty($scope['departments'])) {
            $builder->whereIn($prefix . $deptColumn, $scope['departments']);
        }
    }
}
?>
