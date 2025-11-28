<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Welcome extends Controller
{
    public function index()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please login first.');
        }

        // Get user data from session or database
        $userId = $session->get('user_id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->getUserWithRoles($userId);

        // Map database fields to expected array structure
        if ($user) {
            // Get primary role name - use first role or superadmin status
            $roleName = 'Guest';
            if (!empty($user['is_super_admin']) && $user['is_super_admin'] == 1) {
                $roleName = 'System Administrator';
            } elseif (!empty($user['roles']) && is_array($user['roles'])) {
                $roleName = $user['roles'][0]['role_name'] ?? ($user['roles'][0]['name'] ?? 'User');
            } elseif ($session->get('role_name')) {
                $roleName = $session->get('role_name');
            }

            $user = [
                'id' => $user['id'],
                'name' => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
                'username' => $user['username'] ?? 'unknown',
                'email' => $user['email'] ?? 'unknown@email.com',
                'role' => $roleName,
                'department' => $user['department'] ?? $session->get('department_name') ?? 'Unknown Department',
                'position' => $user['position'] ?? 'Unknown Position',
                'phone' => $user['phone'] ?? '-',
                'avatar' => $user['avatar'] ?? null,
                'last_login' => $user['last_login'] ?? null,
                'is_super_admin' => $user['is_super_admin'] ?? 0
            ];
        } else {
            // Fallback to session data if user not found in database
            $user = [
                'id' => $userId ?? 0,
                'name' => $session->get('user_name') ?? 'Unknown User',
                'username' => $session->get('username') ?? 'unknown',
                'email' => $session->get('email') ?? 'unknown@email.com',
                'role' => $session->get('role_name') ?? 'Guest',
                'department' => $session->get('department_name') ?? 'Unknown Department',
                'position' => 'Unknown Position',
                'phone' => '-',
                'avatar' => null,
                'last_login' => null,
                'is_super_admin' => 0
            ];
        }

        $data = [
            'title' => 'Welcome to Optima System',
            'user' => $user,
            'user_name' => $user['name'] ?? $session->get('user_name') ?? 'User',
            'role_name' => $user['role'] ?? $session->get('role_name') ?? 'Guest',
            'current_route' => 'welcome'
        ];

        return view('welcome', $data);
    }
}