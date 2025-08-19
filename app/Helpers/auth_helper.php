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
            'status' => 'active'
        ]);
        
        return true;
    }
}
?>
