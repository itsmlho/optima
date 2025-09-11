<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DebugSession extends BaseController
{
    public function index()
    {
        echo "<h2>🔍 Debug Session Information</h2>";
        
        // First set mock session for testing
        session()->set([
            'isLoggedIn' => true,
            'user_id' => 1,
            'username' => 'superadmin',
            'role' => 'super_admin',
            'name' => 'Super Administrator'
        ]);
        
        // Now display current session data
        echo "<h3>Current Session Data:</h3>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
        
        $role = session()->get('role');
        echo "<h3>Specific Role Check:</h3>";
        echo "<p><strong>session()->get('role'):</strong> '" . ($role ?? '') . "'</p>";
        echo "<p><strong>Type:</strong> " . gettype($role ?? '') . "</p>";
        echo "<p><strong>Is 'super_admin'?:</strong> " . ($role === 'super_admin' ? 'YES' : 'NO') . "</p>";
        echo "<p><strong>Is 'superadmin'?:</strong> " . ($role === 'superadmin' ? 'YES' : 'NO') . "</p>";
        
        // Check other possible role fields
        echo "<h3>Checking All Possible Role Fields:</h3>";
        $roleFields = ['role', 'user_role', 'role_name', 'user_type', 'level'];
        foreach ($roleFields as $field) {
            $value = session()->get($field);
            echo "<p><strong>{$field}:</strong> '" . ($value ?? '') . "' (type: " . gettype($value ?? '') . ")</p>";
        }
        
        // Test navigation after setting session
        echo "<h3>Now you can test the navigation:</h3>";
        echo "<p><a href='" . base_url() . "' target='_blank'>Go to Dashboard (should show menus now)</a></p>";
        
        echo "<hr>";
        echo "<a href='" . base_url() . "'>Back to Dashboard</a>";
    }
}
