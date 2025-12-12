<?php

namespace App\Helpers;

/**
 * Granular Permission Helper
 * 
 * Provides granular permission checking functionality for the application
 */
class PermissionHelper
{
    private static $userPermissions = null;
    private static $rolePermissions = null;
    
    /**
     * Initialize user permissions from session/cache
     */
    public static function init()
    {
        if (self::$userPermissions === null) {
            self::loadUserPermissions();
        }
    }
    
    /**
     * Load user permissions from database
     */
    private static function loadUserPermissions()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            self::$userPermissions = [];
            self::$rolePermissions = [];
            return;
        }
        
        $db = \Config\Database::connect();
        
        // Load custom permissions
        $userPerms = $db->table('user_permissions up')
            ->join('permissions p', 'p.id = up.permission_id')
            ->where('up.user_id', $userId)
            ->where('up.granted', 1)
            ->where('p.is_active', 1)
            ->select('p.key_name, p.display_name, p.module, p.description')
            ->get()
            ->getResultArray();
            
        // Load role-based permissions
        $rolePerms = $db->table('user_roles ur')
            ->join('role_permissions rp', 'rp.role_id = ur.role_id')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('ur.user_id', $userId)
            ->where('rp.granted', 1)
            ->where('p.is_active', 1)
            ->select('p.key_name, p.display_name, p.module, p.description')
            ->get()
            ->getResultArray();
        
        // Combine and index permissions
        self::$userPermissions = array_column($userPerms, 'key_name');
        self::$rolePermissions = array_column($rolePerms, 'key_name');
    }
    
    /**
     * Check if user has specific permission
     * 
     * @param string $permission Permission key (e.g., 'service.workorders.create')
     * @return bool
     */
    public static function hasPermission(string $permission): bool
    {
        self::init();
        
        return in_array($permission, self::$userPermissions) || 
               in_array($permission, self::$rolePermissions);
    }
    
    /**
     * Check if user has any of the specified permissions
     * 
     * @param array $permissions Array of permission keys
     * @return bool
     */
    public static function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if user has all specified permissions
     * 
     * @param array $permissions Array of permission keys
     * @return bool
     */
    public static function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!self::hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Check if user has page access
     * 
     * @param string $module Module name
     * @param string $page Page name
     * @return bool
     */
    public static function hasPageAccess(string $module, string $page): bool
    {
        return self::hasPermission("{$module}.{$page}.index") || 
               self::hasPermission("{$module}.{$page}.view");
    }
    
    /**
     * Check if user can perform action on module/page
     * 
     * @param string $module Module name
     * @param string $page Page name  
     * @param string $action Action name
     * @return bool
     */
    public static function canPerformAction(string $module, string $page, string $action): bool
    {
        return self::hasPermission("{$module}.{$page}.{$action}");
    }
    
    /**
     * Get user's permissions for specific module
     * 
     * @param string $module Module name
     * @return array
     */
    public static function getModulePermissions(string $module): array
    {
        self::init();
        
        $allPermissions = array_merge(self::$userPermissions, self::$rolePermissions);
        $modulePermissions = [];
        
        foreach ($allPermissions as $permission) {
            if (strpos($permission, $module . '.') === 0) {
                $modulePermissions[] = $permission;
            }
        }
        
        return array_unique($modulePermissions);
    }
    
    /**
     * Get all user permissions
     * 
     * @return array
     */
    public static function getAllPermissions(): array
    {
        self::init();
        return array_unique(array_merge(self::$userPermissions, self::$rolePermissions));
    }
    
    /**
     * Check if user has component access
     * 
     * @param string $component Component key
     * @return bool
     */
    public static function hasComponentAccess(string $component): bool
    {
        return self::hasPermission($component);
    }
    
    /**
     * Clear cached permissions (useful after role/permission changes)
     */
    public static function clearCache(): void
    {
        self::$userPermissions = null;
        self::$rolePermissions = null;
    }
}