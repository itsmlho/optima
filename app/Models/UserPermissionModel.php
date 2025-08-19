<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPermissionModel extends Model
{
    protected $table = 'user_permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id', 'permission_id', 'division_id', 'granted', 'assigned_by', 'assigned_at', 'expires_at'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'permission_id' => 'required|is_natural_no_zero',
        'division_id' => 'permit_empty|is_natural_no_zero',
        'granted' => 'permit_empty|in_list[0,1]',
        'assigned_by' => 'permit_empty|is_natural_no_zero'
    ];

    /**
     * Get user permissions with permission details
     */
    public function getUserPermissions($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_permissions.*, permissions.name, permissions.key as permission_key, permissions.module, divisions.name as division_name, divisions.code as division_code');
        $builder->join('permissions', 'permissions.id = user_permissions.permission_id');
        $builder->join('divisions', 'divisions.id = user_permissions.division_id', 'left');
        $builder->where('user_permissions.user_id', $userId);
        $builder->where('(user_permissions.expires_at IS NULL OR user_permissions.expires_at > NOW())');
        $builder->orderBy('permissions.module', 'ASC');
        $builder->orderBy('permissions.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Update user permissions (replace all)
     */
    public function updateUserPermissions($userId, $permissionData, $assignedBy = null)
    {
        $this->db->transBegin();

        try {
            // Remove existing custom permissions
            $this->where('user_id', $userId)->delete();
            
            // Add new permissions
            $data = [];
            foreach ($permissionData as $permission) {
                $data[] = [
                    'user_id' => $userId,
                    'permission_id' => $permission['permission_id'],
                    'division_id' => $permission['division_id'] ?? null,
                    'granted' => $permission['granted'] ?? 1,
                    'assigned_by' => $assignedBy,
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'expires_at' => $permission['expires_at'] ?? null
                ];
            }
            
            if (!empty($data)) {
                $this->insertBatch($data);
            }
            
            $this->db->transCommit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return false;
        }
    }

    /**
     * Assign permission to user
     */
    public function assignPermission($userId, $permissionId, $divisionId = null, $granted = true, $assignedBy = null, $expiresAt = null)
    {
        // Remove existing permission if exists
        $builder = $this->where('user_id', $userId)->where('permission_id', $permissionId);
        if ($divisionId) {
            $builder->where('division_id', $divisionId);
        } else {
            $builder->where('division_id IS NULL');
        }
        $builder->delete();
        
        // Add new permission
        $data = [
            'user_id' => $userId,
            'permission_id' => $permissionId,
            'division_id' => $divisionId,
            'granted' => $granted ? 1 : 0,
            'assigned_by' => $assignedBy,
            'assigned_at' => date('Y-m-d H:i:s'),
            'expires_at' => $expiresAt
        ];
        
        return $this->insert($data);
    }

    /**
     * Remove permission from user
     */
    public function removePermission($userId, $permissionId, $divisionId = null)
    {
        $builder = $this->where('user_id', $userId)->where('permission_id', $permissionId);
        if ($divisionId) {
            $builder->where('division_id', $divisionId);
        }
        return $builder->delete();
    }

    /**
     * Check if user has specific permission
     */
    public function checkUserPermission($userId, $permissionKey, $divisionId = null)
    {
        // First check role-based permissions
        $rolePermission = $this->checkRolePermission($userId, $permissionKey);
        
        // Then check custom permissions (can override role permissions)
        $customPermission = $this->checkCustomPermission($userId, $permissionKey, $divisionId);
        
        // Custom permissions override role permissions
        if ($customPermission !== null) {
            return $customPermission;
        }
        
        return $rolePermission;
    }

    /**
     * Check role-based permission
     */
    protected function checkRolePermission($userId, $permissionKey)
    {
        $builder = $this->db->table('user_roles');
        $builder->join('role_permissions', 'role_permissions.role_id = user_roles.role_id');
        $builder->join('permissions', 'permissions.id = role_permissions.permission_id');
        $builder->where('user_roles.user_id', $userId);
        $builder->where('permissions.key', $permissionKey);
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Check custom permission
     */
    protected function checkCustomPermission($userId, $permissionKey, $divisionId = null)
    {
        $builder = $this->db->table($this->table);
        $builder->join('permissions', 'permissions.id = user_permissions.permission_id');
        $builder->where('user_permissions.user_id', $userId);
        $builder->where('permissions.key', $permissionKey);
        $builder->where('(user_permissions.expires_at IS NULL OR user_permissions.expires_at > NOW())');
        
        if ($divisionId) {
            $builder->where('user_permissions.division_id', $divisionId);
        }
        
        $result = $builder->get()->getRowArray();
        
        if ($result) {
            return $result['granted'] == 1;
        }
        
        return null; // No custom permission found
    }

    /**
     * Get users with custom permissions count
     */
    public function getUsersWithCustomPermissions()
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_id');
        $builder->groupBy('user_id');
        
        return $builder->countAllResults();
    }

    /**
     * Get user permissions count
     */
    public function getUserPermissionsCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('(expires_at IS NULL OR expires_at > NOW())')
                    ->countAllResults();
    }

    /**
     * Get effective permissions for user
     */
    public function getEffectivePermissions($userId)
    {
        $permissions = [];
        
        // Get role permissions
        $rolePermissions = $this->getRolePermissions($userId);
        foreach ($rolePermissions as $perm) {
            $permissions[$perm['permission_key']] = true;
        }
        
        // Apply custom permissions (can grant or deny)
        $customPermissions = $this->getUserPermissions($userId);
        foreach ($customPermissions as $perm) {
            $permissions[$perm['permission_key']] = $perm['granted'] == 1;
        }
        
        return $permissions;
    }

    /**
     * Get role permissions for user
     */
    protected function getRolePermissions($userId)
    {
        $builder = $this->db->table('user_roles');
        $builder->select('permissions.key as permission_key, permissions.name, permissions.module');
        $builder->join('role_permissions', 'role_permissions.role_id = user_roles.role_id');
        $builder->join('permissions', 'permissions.id = role_permissions.permission_id');
        $builder->where('user_roles.user_id', $userId);
        $builder->groupBy('permissions.id'); // Avoid duplicates
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get permission assignments by user
     */
    public function getPermissionAssignments($permissionId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_permissions.*, users.first_name, users.last_name, users.email, divisions.name as division_name');
        $builder->join('users', 'users.id = user_permissions.user_id');
        $builder->join('divisions', 'divisions.id = user_permissions.division_id', 'left');
        $builder->where('user_permissions.permission_id', $permissionId);
        $builder->where('(user_permissions.expires_at IS NULL OR user_permissions.expires_at > NOW())');
        $builder->orderBy('user_permissions.granted', 'DESC');
        $builder->orderBy('users.first_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Clean expired permissions
     */
    public function cleanExpiredPermissions()
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }

    /**
     * Get permissions expiring soon
     */
    public function getPermissionsExpiringSoon($days = 7)
    {
        $expiryDate = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        
        $builder = $this->db->table($this->table);
        $builder->select('user_permissions.*, users.first_name, users.last_name, permissions.name as permission_name, divisions.name as division_name');
        $builder->join('users', 'users.id = user_permissions.user_id');
        $builder->join('permissions', 'permissions.id = user_permissions.permission_id');
        $builder->join('divisions', 'divisions.id = user_permissions.division_id', 'left');
        $builder->where('user_permissions.expires_at IS NOT NULL');
        $builder->where('user_permissions.expires_at <=', $expiryDate);
        $builder->where('user_permissions.expires_at >', date('Y-m-d H:i:s'));
        $builder->orderBy('user_permissions.expires_at', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Bulk assign permissions
     */
    public function bulkAssignPermissions($userIds, $permissionIds, $divisionId = null, $granted = true, $assignedBy = null)
    {
        $this->db->transBegin();
        
        try {
            $data = [];
            foreach ($userIds as $userId) {
                foreach ($permissionIds as $permissionId) {
                    $data[] = [
                        'user_id' => $userId,
                        'permission_id' => $permissionId,
                        'division_id' => $divisionId,
                        'granted' => $granted ? 1 : 0,
                        'assigned_by' => $assignedBy,
                        'assigned_at' => date('Y-m-d H:i:s')
                    ];
                }
            }
            
            if (!empty($data)) {
                $this->insertBatch($data);
            }
            
            $this->db->transCommit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return false;
        }
    }
}
