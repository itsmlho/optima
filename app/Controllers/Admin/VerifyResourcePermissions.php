<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class VerifyResourcePermissions extends BaseController
{
    public function index()
    {
        // Only for admin/super admin
        if (!$this->hasPermission('admin.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $db = \Config\Database::connect();
        
        // Get all permissions
        $totalPermissions = $db->table('permissions')->countAllResults();
        $resourcePermissions = $db->table('permissions')
            ->where('category', 'resource')
            ->countAllResults();
        
        $modulePermissions = $db->table('permissions')
            ->where('category', '!=', 'resource')
            ->orWhere('category IS NULL')
            ->countAllResults();
        
        // Get resource permissions list
        $resourcePermsList = $db->table('permissions')
            ->where('category', 'resource')
            ->orderBy('module', 'ASC')
            ->orderBy('key', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get role permissions count
        $rolePermissions = $db->table('roles r')
            ->select('r.name, COUNT(rp.permission_id) as permission_count')
            ->join('role_permissions rp', 'rp.role_id = r.id', 'left')
            ->groupBy('r.id', 'r.name')
            ->orderBy('permission_count', 'DESC')
            ->get()
            ->getResultArray();
        
        // Get resource permissions by role
        $resourceByRole = $db->table('roles r')
            ->select('r.name as role_name, p.key as permission_key, p.name as permission_name')
            ->join('role_permissions rp', 'rp.role_id = r.id')
            ->join('permissions p', 'rp.permission_id = p.id')
            ->where('p.category', 'resource')
            ->orderBy('r.name', 'ASC')
            ->orderBy('p.key', 'ASC')
            ->get()
            ->getResultArray();
        
        // Verification status
        $verification = [
            'total_permissions_ok' => $totalPermissions >= 39,
            'resource_permissions_ok' => $resourcePermissions == 7,
            'expected_total' => 39,
            'expected_resource' => 7,
            'actual_total' => $totalPermissions,
            'actual_resource' => $resourcePermissions
        ];
        
        $data = [
            'title' => 'Verify Resource Permissions',
            'page_title' => 'Resource Permissions Verification',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/admin' => 'Administration',
                '/admin/verify-resource-permissions' => 'Verify Resource Permissions'
            ],
            'stats' => [
                'total_permissions' => $totalPermissions,
                'module_permissions' => $modulePermissions,
                'resource_permissions' => $resourcePermissions,
                'expected_total' => 39,
                'expected_resource' => 7
            ],
            'verification' => $verification,
            'resource_permissions' => $resourcePermsList,
            'role_permissions' => $rolePermissions,
            'resource_by_role' => $resourceByRole
        ];
        
        return view('admin/verify_resource_permissions', $data);
    }
}
