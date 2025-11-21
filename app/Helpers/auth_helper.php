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
?>
