<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table = 'user_roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id', 'role_id', 'assigned_by', 'assigned_at'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'role_id' => 'required|is_natural_no_zero',
        'assigned_by' => 'permit_empty|is_natural_no_zero'
    ];

    /**
     * Get user roles with role details
     */
    public function getUserRoles($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_roles.*, roles.name as role_name, roles.description');
        $builder->join('roles', 'roles.id = user_roles.role_id');
        $builder->where('user_roles.user_id', $userId);
        $builder->orderBy('roles.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($roleId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_roles.*, users.first_name, users.last_name, users.email');
        $builder->join('users', 'users.id = user_roles.user_id');
        $builder->where('user_roles.role_id', $roleId);
        $builder->orderBy('users.first_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Update user roles (replace all)
     */
    public function updateUserRoles($userId, $roleIds, $assignedBy = null)
    {
        $this->db->transBegin();

        try {
            // Remove existing roles
            $this->where('user_id', $userId)->delete();
            
            // Add new roles
            $data = [];
            foreach ($roleIds as $roleId) {
                $data[] = [
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'assigned_by' => $assignedBy,
                    'assigned_at' => date('Y-m-d H:i:s')
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
     * Assign role to user
     */
    public function assignRole($userId, $roleId, $assignedBy = null)
    {
        // Check if already assigned
        if ($this->where('user_id', $userId)->where('role_id', $roleId)->first()) {
            return true; // Already assigned
        }
        
        $data = [
            'user_id' => $userId,
            'role_id' => $roleId,
            'assigned_by' => $assignedBy,
            'assigned_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert($data);
    }

    /**
     * Remove role from user
     */
    public function removeRole($userId, $roleId)
    {
        return $this->where('user_id', $userId)->where('role_id', $roleId)->delete();
    }

    /**
     * Check if user has role
     */
    public function userHasRole($userId, $roleId)
    {
        return $this->where('user_id', $userId)->where('role_id', $roleId)->first() !== null;
    }

    /**
     * Get user role permissions
     */
    public function getUserRolePermissions($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('permissions.id as permission_id, permissions.name, permissions.key as permission_key, permissions.module');
        $builder->join('roles', 'roles.id = user_roles.role_id');
        $builder->join('role_permissions', 'role_permissions.role_id = roles.id');
        $builder->join('permissions', 'permissions.id = role_permissions.permission_id');
        $builder->where('user_roles.user_id', $userId);
        $builder->groupBy('permissions.id'); // Avoid duplicates if user has multiple roles with same permission
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get user count by role
     */
    public function getUserCountByRole($roleId)
    {
        return $this->where('role_id', $roleId)->countAllResults();
    }

    /**
     * Get roles usage statistics
     */
    public function getRoleUsageStats()
    {
        $builder = $this->db->table($this->table);
        $builder->select('roles.name, roles.id as role_id, COUNT(user_roles.user_id) as user_count');
        $builder->join('roles', 'roles.id = user_roles.role_id');
        $builder->groupBy('roles.id');
        $builder->orderBy('user_count', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Check if user has any management role
     */
    public function userHasManagementRole($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->join('roles', 'roles.id = user_roles.role_id');
        $builder->where('user_roles.user_id', $userId);
        // Check for specific management role names since 'level' column doesn't exist
        $builder->whereIn('roles.name', ['Admin', 'Manager', 'Supervisor', 'System Administrator']);
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get user's roles (simplified without level)
     */
    public function getUserHighestRoleLevel($userId)
    {
        // Since 'level' column doesn't exist, return role priority based on name
        $roles = $this->getUserRoles($userId);
        
        if (empty($roles)) return null;
        
        // Define role hierarchy manually
        $rolePriority = [
            'System Administrator' => 1,
            'Admin' => 2,
            'Manager' => 3,
            'Supervisor' => 4,
            'User' => 5,
            'Guest' => 6
        ];
        
        $highestPriority = 999;
        foreach ($roles as $role) {
            $roleName = $role['role_name'] ?? $role['name'] ?? 'Guest';
            $priority = $rolePriority[$roleName] ?? 999;
            if ($priority < $highestPriority) {
                $highestPriority = $priority;
            }
        }
        
        return $highestPriority < 999 ? $highestPriority : null;
    }
}
