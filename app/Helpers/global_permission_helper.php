<?php
// Global Permission Helper - Simple & Universal
if (!function_exists('get_global_permission')) {
    function get_global_permission($module) {
        // Use $_SESSION directly for compatibility
        $username = $_SESSION['username'] ?? null;
        $role = $_SESSION['role'] ?? null;
        
        // Super admin - Full access
        if ($role === 'super_admin' || $username === 'super_admin') {
            return [
                'view' => true,
                'create' => true,
                'edit' => true,
                'delete' => true,
                'export' => true
            ];
        }
        
        // View Only users - Based on username
        $view_only_users = ['Admin Diesel', 'service_diesel', 'view_only_user'];
        if (in_array($username, $view_only_users)) {
            return [
                'view' => true,
                'create' => false,
                'edit' => false,
                'delete' => false,
                'export' => false
            ];
        }
        
        // Default - Full access
        return [
            'view' => true,
            'create' => true,
            'edit' => true,
            'delete' => true,
            'export' => true
        ];
    }
}

// Global permission check function
if (!function_exists('can_global')) {
    function can_global($module, $action) {
        $permissions = get_global_permission($module);
        return $permissions[$action] ?? false;
    }
}
?>
