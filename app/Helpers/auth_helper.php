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
        try {
            $session = session();
            
            // Check if user is logged in
            if (!$session->get('isLoggedIn')) {
                return null;
            }
            
            $userId = $session->get('user_id');
            $userRole = $session->get('role');
            
            if (!$userId || !$userRole) {
                return null;
            }
            
            log_message('debug', "Processing scope for user ID: {$userId}, Role: {$userRole}");
            
            // Normalize role: convert underscores/hyphens to spaces for consistent matching
            // Session stores slug (e.g., 'admin_service_pusat'), comparisons use name format
            $normalizedRole = strtolower(str_replace(['_', '-'], ' ', $userRole));
            
            // 1. SUPER ADMINISTRATOR - Full Access
            if (in_array($normalizedRole, ['super administrator', 'administrator', 'super admin'])) {
                log_message('debug', 'Super Administrator detected - granting full access');
                return null; // Full access
            }
            
            // 2. CHECK IF USER IS IN SERVICE DIVISION - Only Service division uses area+department filtering
            $userDivision = getUserDivision($userId);
            if (!$userDivision || strtolower($userDivision) !== 'service') {
                log_message('debug', "Non-service division detected: {$userDivision} - no area/department filtering");
                return null; // No filtering for non-service divisions
            }
            
            // FROM HERE: Only Service Division users
            log_message('debug', 'Service division user detected - applying area/department filtering');
            
            // 3. HEAD SERVICE - Full Service Access
            if ($normalizedRole === 'head service') {
                log_message('debug', 'Head Service detected - granting full service access');
                return null; // Full access to all service operations
            }
            
            // 4. ADMIN SERVICE PUSAT - All Service Areas (can see all areas)
            if ($normalizedRole === 'admin service pusat') {
                return getUserServiceAccess($userId, 'ALL');
            }
            
            // 5. ADMIN SERVICE AREA - Mill Areas Only  
            if ($normalizedRole === 'admin service area') {
                return getUserServiceAccess($userId, 'MILL');
            }
            
            // 6. SUPERVISOR/STAFF SERVICE - Based on assignments
            if (in_array($normalizedRole, ['supervisor service', 'staff service'])) {
                return getUserServiceAccess($userId, 'ASSIGNED');
            }
            
            // 7. LEGACY SERVICE ROLES - Department specific
            if (strpos($normalizedRole, 'service') !== false) {
                return getLegacyServiceAccess($userRole);
            }
            
            // 8. MANAGER SERVICE AREA
            if ($normalizedRole === 'manager service area') {
                return getUserServiceAccess($userId, 'ALL');
            }
            
            // 8. DEFAULT FOR SERVICE DIVISION - Limited access
            log_message('debug', 'Unknown service role - applying limited access');
            return ['areas' => [], 'departments' => [], 'has_full_access' => false];
            
        } catch (\Exception $e) {
            log_message('error', 'Error in get_user_area_department_scope: ' . $e->getMessage());
            return null; // Return null on error for debugging
        }
    }
}

if (!function_exists('getServicePusatScope')) {
    /**
     * Get scope for Service Pusat roles (Central office with department restrictions)
     */
    function getServicePusatScope($role)
    {
        $db = \Config\Database::connect();
        
        // Get Central areas (CENTRAL type)
        try {
            $centralAreas = $db->table('areas')
                ->select('id')
                ->where('area_type', 'CENTRAL')
                ->where('is_active', 1)
                ->get()
                ->getResultArray();
            
            $areaIds = array_column($centralAreas, 'id');
            
            // Department filtering based on role
            $departments = [];
            if (strpos(strtolower($role), 'electric') !== false) {
                $departments = [2]; // Electric only
                log_message('debug', 'Service Pusat Electric - Central areas, Electric department only');
            } elseif (strpos(strtolower($role), 'diesel') !== false) {
                $departments = [1, 3]; // Diesel + Gasoline
                log_message('debug', 'Service Pusat Diesel - Central areas, Diesel+Gasoline departments');
            } else {
                $departments = [1, 2, 3]; // All departments for Manager Service Pusat
                log_message('debug', 'Service Pusat Manager - Central areas, all departments');
            }
            
            return [
                'areas' => $areaIds,
                'departments' => $departments,
                'has_full_access' => false,
                'scope_type' => 'service_pusat'
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting Service Pusat scope: ' . $e->getMessage());
            return ['areas' => [], 'departments' => [], 'has_full_access' => false];
        }
    }
}

if (!function_exists('getServiceAreaScope')) {
    /**
     * Get scope for Service Area roles (Geographic restrictions)
     */
    function getServiceAreaScope($userId, $role)
    {
        $db = \Config\Database::connect();
        
        try {
            // Get assigned areas from area_employee_assignments
            if ($db->tableExists('area_employee_assignments')) {
                $assignedAreas = $db->table('area_employee_assignments')
                    ->select('area_id')
                    ->where('employee_id', $userId)
                    ->where('is_active', 1)
                    ->get()
                    ->getResultArray();
                
                $areaIds = array_column($assignedAreas, 'area_id');
                
                // Service Area roles can access all departments in their assigned areas
                $departments = [1, 2, 3]; // All departments
                
                log_message('debug', "Service Area scope - User {$userId} assigned to areas: " . implode(',', $areaIds));
                
                return [
                    'areas' => $areaIds,
                    'departments' => $departments,
                    'has_full_access' => false,
                    'scope_type' => 'service_area'
                ];
            } else {
                // Fallback: Mill areas only
                $branchAreas = $db->table('areas')
                    ->select('id')
                    ->where('area_type', 'MILL')
                    ->where('is_active', 1)
                    ->get()
                    ->getResultArray();
                
                $areaIds = array_column($branchAreas, 'id');
                
                return [
                    'areas' => $areaIds,
                    'departments' => [1, 2, 3],
                    'has_full_access' => false,
                    'scope_type' => 'service_area_fallback'
                ];
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting Service Area scope: ' . $e->getMessage());
            return ['areas' => [], 'departments' => [], 'has_full_access' => false];
        }
    }
}

if (!function_exists('getFieldServiceScope')) {
    /**
     * Get scope for Field Service roles (Area + Department specific)
     */
    function getFieldServiceScope($userId, $role)
    {
        $db = \Config\Database::connect();
        
        try {
            if ($db->tableExists('area_employee_assignments')) {
                // Get specific assignments
                $assignments = $db->table('area_employee_assignments aea')
                    ->select('aea.area_id, aea.department_scope')
                    ->where('aea.employee_id', $userId)
                    ->where('aea.is_active', 1)
                    ->get()
                    ->getResultArray();
                
                $areaIds = [];
                $departments = [];
                
                foreach ($assignments as $assignment) {
                    $areaIds[] = $assignment['area_id'];
                    
                    // Parse department_scope
                    $scope = strtolower($assignment['department_scope'] ?? 'ALL');
                    if ($scope === 'all') {
                        $departments = array_merge($departments, [1, 2, 3]);
                    } elseif (strpos($scope, 'electric') !== false) {
                        $departments[] = 2;
                    } elseif (strpos($scope, 'diesel') !== false) {
                        $departments[] = 1;
                    } elseif (strpos($scope, 'gasoline') !== false) {
                        $departments[] = 3;
                    }
                }
                
                $areaIds = array_unique($areaIds);
                $departments = array_unique($departments);
                
                log_message('debug', "Field Service scope - User {$userId}: Areas[" . implode(',', $areaIds) . "], Departments[" . implode(',', $departments) . "]");
                
                return [
                    'areas' => $areaIds,
                    'departments' => $departments,
                    'has_full_access' => false,
                    'scope_type' => 'field_service'
                ];
            }
            
            return ['areas' => [], 'departments' => [], 'has_full_access' => false];
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting Field Service scope: ' . $e->getMessage());
            return ['areas' => [], 'departments' => [], 'has_full_access' => false];
        }
    }
}

if (!function_exists('getLegacyServiceScope')) {
    /**
     * Get scope for Legacy Service roles
     */
    function getLegacyServiceScope($role)
    {
        $db = \Config\Database::connect();
        
        try {
            if (strpos(strtolower($role), 'head') !== false) {
                // Head roles get wider access
                if (strpos(strtolower($role), 'electric') !== false) {
                    $departments = [2]; // Electric only
                } elseif (strpos(strtolower($role), 'diesel') !== false) {
                    $departments = [1, 3]; // Diesel + Gasoline
                } else {
                    $departments = [1, 2, 3]; // All
                }
                
                // All areas for head roles
                $areas = $db->table('areas')->select('id')->where('is_active', 1)->get()->getResultArray();
                $areaIds = array_column($areas, 'id');
                
            } else {
                // Staff roles get limited access
                if (strpos(strtolower($role), 'electric') !== false) {
                    $departments = [2];
                } elseif (strpos(strtolower($role), 'diesel') !== false) {
                    $departments = [1, 3];
                } else {
                    $departments = [1, 2, 3];
                }
                
                // Central areas only for staff
                $areas = $db->table('areas')->select('id')->where('area_type', 'CENTRAL')->where('is_active', 1)->get()->getResultArray();
                $areaIds = array_column($areas, 'id');
            }
            
            log_message('debug', "Legacy Service scope - Role: {$role}, Areas: " . implode(',', $areaIds) . ", Departments: " . implode(',', $departments));
            
            return [
                'areas' => $areaIds,
                'departments' => $departments,
                'has_full_access' => false,
                'scope_type' => 'legacy_service'
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting Legacy Service scope: ' . $e->getMessage());
            return ['areas' => [], 'departments' => [], 'has_full_access' => false];
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

if (!function_exists('getUserAccessFromTable')) {
    /**
     * Get user access from user_area_access table
     */
    function getUserAccessFromTable($userId, $accessType)
    {
        $db = \Config\Database::connect();
        
        try {
            // Check if user_area_access table exists
            if (!$db->tableExists('user_area_access')) {
                // Fallback to default behavior
                return getDefaultAccessByType($accessType);
            }
            
            $userAccess = $db->table('user_area_access')
                ->where('user_id', $userId)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            
            if (!$userAccess) {
                // No specific access defined, use default
                return getDefaultAccessByType($accessType);
            }
            
            // Get areas based on access type and user settings
            $areaIds = [];
            
            if ($userAccess['specific_areas']) {
                // Use specific areas from JSON
                $specificAreas = json_decode($userAccess['specific_areas'], true);
                $areaIds = is_array($specificAreas) ? $specificAreas : [];
            } else {
                // Use area_type filter
                $areaType = $userAccess['area_type'] ?? $accessType;
                
                if ($areaType === 'ALL') {
                    $areas = safe_get_result($db->table('areas')->select('id')->where('is_active', 1));
                } else {
                    $areas = safe_get_result($db->table('areas')->select('id')->where('area_type', $areaType)->where('is_active', 1));
                }
                $areaIds = array_column($areas, 'id');
            }
            
            // Get departments based on scope
            $departments = [];
            $deptScope = $userAccess['department_scope'] ?? 'ALL';
            
            switch ($deptScope) {
                case 'ELECTRIC':
                    $departments = [2];
                    break;
                case 'DIESEL':
                    $departments = [1];
                    break;
                case 'GASOLINE':
                    $departments = [3];
                    break;
                case 'DIESEL_GASOLINE':
                    $departments = [1, 3];
                    break;
                default:
                    $departments = [1, 2, 3];
            }
            
            log_message('debug', "User access from table - User: {$userId}, Areas: " . implode(',', $areaIds) . ", Departments: " . implode(',', $departments));
            
            return [
                'areas' => $areaIds,
                'departments' => $departments,
                'has_full_access' => false,
                'scope_type' => 'table_based'
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting user access from table: ' . $e->getMessage());
            return getDefaultAccessByType($accessType);
        }
    }
}

if (!function_exists('getUserDivision')) {
    /**
     * Get user division for filtering check
     */
    function getUserDivision($userId)
    {
        $db = \Config\Database::connect();
        
        try {
            $query = $db->table('users u')
                ->select('d.name as division_name')
                ->join('user_roles ur', 'u.id = ur.user_id', 'left')
                ->join('roles r', 'ur.role_id = r.id', 'left') 
                ->join('divisions d', 'r.division_id = d.id', 'left')
                ->where('u.id', $userId)
                ->where('ur.is_active', 1)
                ->get();
                
            $result = $query->getRowArray();
            return $result ? $result['division_name'] : null;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getUserDivision: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('getUserServiceAccess')) {
    /**
     * Service-specific access handler
     */
    function getUserServiceAccess($userId, $accessType)
    {
        $db = \Config\Database::connect();
        
        try {
            log_message('debug', "Getting service access for user {$userId}, type: {$accessType}");
            
            // Get user access data specifically for service division
            $userAccessQuery = $db->table('user_area_access')
                ->where('user_id', $userId)
                ->where('is_active', 1)
                ->get();
                
            $userAccess = $userAccessQuery->getRowArray();
            
            // Get branch access data for service division users
            $branchAccess = $db->table('user_branch_access')
                ->where('user_id', $userId)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            
            if ($userAccess) {
                log_message('debug', 'Found service access data: ' . json_encode($userAccess));
                
                $scope = [
                    'areas' => [],
                    'departments' => [],
                    'has_full_access' => false,
                    'area_type' => $userAccess['area_type'] ?? 'ALL',
                    'department_scope' => $userAccess['department_scope'] ?? 'ALL'
                ];
                
                // Handle area filtering for service users with branch consideration
                $areaTypeFilter = null;
                $specificAreaIds = [];
                
                if ($accessType === 'CENTRAL') {
                    $areaTypeFilter = 'CENTRAL';
                } elseif ($accessType === 'MILL') {
                    $areaTypeFilter = 'MILL';
                    
                    // Apply mill filtering if exists
                    if ($branchAccess) {
                        if ($branchAccess['access_type'] === 'SPECIFIC_BRANCHES' && $branchAccess['branch_ids']) {
                            $specificAreaIds = json_decode($branchAccess['branch_ids'], true) ?: [];
                        } elseif ($branchAccess['access_type'] === 'NO_BRANCHES') {
                            $specificAreaIds = []; // No mill access
                        }
                        // For 'ALL_BRANCHES', no additional filtering needed
                    }
                } elseif ($accessType === 'ALL') {
                    // Admin Service Pusat can see all areas, but still apply mill filtering for MILL areas
                    $areaTypeFilter = null;
                    
                    if ($branchAccess && $branchAccess['access_type'] === 'SPECIFIC_BRANCHES' && $branchAccess['branch_ids']) {
                        // For ALL access with mill restriction, get all CENTRAL + specific MILL areas
                        $centralQuery = $db->table('areas')
                            ->select('id as area_id')
                            ->where('area_type', 'CENTRAL');
                        $centralAreas = safe_get_result($centralQuery);
                        $centralAreaIds = array_column($centralAreas, 'area_id');
                        
                        $branchAreaIds = json_decode($branchAccess['branch_ids'], true) ?: [];
                        $scope['areas'] = array_merge($centralAreaIds, $branchAreaIds);
                        
                        log_message('debug', 'ALL access with branch filtering - Areas: ' . implode(', ', $scope['areas']));
                    }
                } elseif ($accessType === 'ASSIGNED' && $userAccess['area_type'] !== 'ALL') {
                    $areaTypeFilter = $userAccess['area_type'];
                }
                
                // Get areas for service operations if not already set
                if (empty($scope['areas'])) {
                    $areaQuery = $db->table('areas')
                        ->select('id as area_id, area_name, area_type');
                    
                    if ($areaTypeFilter) {
                        $areaQuery->where('area_type', $areaTypeFilter);
                    }
                    
                    // Apply specific area filtering if exists
                    if (!empty($specificAreaIds)) {
                        $areaQuery->whereIn('id', $specificAreaIds);
                    }
                    
                    $areas = safe_get_result($areaQuery);
                    $scope['areas'] = array_column($areas, 'area_id');
                }
                
                // Handle department filtering for service
                if ($userAccess['department_scope'] !== 'ALL') {
                    $departmentMap = [
                        'DIESEL' => [1],
                        'ELECTRIC' => [2], 
                        'GASOLINE' => [3],
                        'DIESEL_ELECTRIC_GASOLINE' => [1, 2, 3]
                    ];
                    
                    $scope['departments'] = $departmentMap[$userAccess['department_scope']] ?? [];
                }
                
                log_message('debug', 'Service scope - Areas: ' . implode(', ', $scope['areas']) . ', Departments: ' . implode(', ', $scope['departments']));
                return $scope;
            }
            
            // Default service access based on type
            return getDefaultServiceAccess($userId, $accessType);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getUserServiceAccess: ' . $e->getMessage());
            return getDefaultServiceAccess($userId, $accessType);
        }
    }
}

if (!function_exists('getDefaultServiceAccess')) {
    /**
     * Default service access when no specific access found
     */
    function getDefaultServiceAccess($userId, $accessType)
    {
        $db = \Config\Database::connect();
        
        $scope = [
            'areas' => [],
            'departments' => [],
            'has_full_access' => false
        ];
        
        try {
            // Check branch access even for default access
            $branchAccess = $db->table('user_branch_access')
                ->where('user_id', $userId)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            
            // Get areas based on access type - use 'id' as primary key
            $areaQuery = $db->table('areas')
                ->select('id as area_id');
                
            if ($accessType === 'CENTRAL') {
                $areaQuery->where('area_type', 'CENTRAL');
            } elseif ($accessType === 'MILL') {
                $areaQuery->where('area_type', 'MILL');
                
                // Apply mill filtering for MILL access
                if ($branchAccess && $branchAccess['access_type'] === 'SPECIFIC_BRANCHES' && $branchAccess['branch_ids']) {
                    $branchAreaIds = json_decode($branchAccess['branch_ids'], true) ?: [];
                    if (!empty($branchAreaIds)) {
                        $areaQuery->whereIn('id', $branchAreaIds);
                    }
                } elseif ($branchAccess && $branchAccess['access_type'] === 'NO_BRANCHES') {
                    // No mill access
                    $scope['areas'] = [];
                    return $scope;
                }
            }
            // For 'ALL', no filter needed - get all areas, but apply branch filtering
            
            $areas = $areaQuery->get()->getResultArray();
            $scope['areas'] = array_column($areas, 'area_id');
            
            log_message('debug', "Default service access for {$accessType}: " . implode(', ', $scope['areas']));
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getDefaultServiceAccess: ' . $e->getMessage());
        }
        
        return $scope;
    }
}

if (!function_exists('getDefaultAccessByType')) {
    /**
     * Get default access when no specific configuration exists
     */
    function getDefaultAccessByType($accessType)
    {
        $db = \Config\Database::connect();
        
        try {
            switch ($accessType) {
                case 'CENTRAL':
                    // Admin Service Pusat - Central areas only
                    $areas = $db->table('areas')->select('id')->where('area_type', 'CENTRAL')->where('is_active', 1)->get()->getResultArray();
                    $areaIds = array_column($areas, 'id');
                    return [
                        'areas' => $areaIds,
                        'departments' => [1, 2, 3], // All departments by default
                        'has_full_access' => false,
                        'scope_type' => 'default_central'
                    ];
                    
                case 'MILL':
                    // Admin Service Area - Mill areas only
                    $areas = safe_get_result($db->table('areas')->select('id')->where('area_type', 'MILL')->where('is_active', 1));
                    $areaIds = array_column($areas, 'id');
                    return [
                        'areas' => $areaIds,
                        'departments' => [1, 2, 3], // All departments by default
                        'has_full_access' => false,
                        'scope_type' => 'default_mill'
                    ];
                    
                case 'ASSIGNED':
                    // Based on area_employee_assignments
                    return ['areas' => [], 'departments' => [], 'has_full_access' => false];
                    
                default:
                    return null; // Full access
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting default access: ' . $e->getMessage());
            return ['areas' => [], 'departments' => [], 'has_full_access' => false];
        }
    }
}

if (!function_exists('getLegacyServiceAccess')) {
    /**
     * Handle legacy service role access
     */
    function getLegacyServiceAccess($role)
    {
        $db = \Config\Database::connect();
        
        try {
            // Determine departments based on legacy role
            $departments = [1, 2, 3]; // Default all departments
            
            if (strpos(strtolower($role), 'electric') !== false) {
                $departments = [2]; // Electric only
            } elseif (strpos(strtolower($role), 'diesel') !== false) {
                $departments = [1, 3]; // Diesel + Gasoline
            }
            
            // All areas for legacy roles
            $areas = $db->table('areas')->select('id')->where('is_active', 1)->get()->getResultArray();
            $areaIds = array_column($areas, 'id');
            
            log_message('debug', "Legacy service access - Role: {$role}, Departments: " . implode(',', $departments));
            
            return [
                'areas' => $areaIds,
                'departments' => $departments,
                'has_full_access' => false,
                'scope_type' => 'legacy'
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting legacy service access: ' . $e->getMessage());
            return ['areas' => [], 'departments' => [], 'has_full_access' => false];
        }
    }
}
?>
