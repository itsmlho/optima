<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table      = 'role_permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['role_id', 'permission_id'];
    protected $useTimestamps = false;

    // Get all role-permission relations
    public function getAll()
    {
        return $this->findAll();
    }

    // Get permissions by role
    public function getPermissionsByRole($roleId)
    {
        return $this->where('role_id', $roleId)->findAll();
    }

    // Get roles by permission
    public function getRolesByPermission($permissionId)
    {
        return $this->select('roles.*')
            ->join('roles', 'roles.id = role_permissions.role_id')
            ->where('role_permissions.permission_id', $permissionId)
            ->findAll();
    }

    // Add a permission to a role
    public function add($roleId, $permissionId)
    {
        return $this->insert([
            'role_id' => $roleId,
            'permission_id' => $permissionId
        ]);
    }

    // Remove a permission from a role
    public function remove($roleId, $permissionId)
    {
        return $this->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->delete();
    }

    // Update a role-permission relation (rarely used, but for completeness)
    public function updateRelation($id, $data)
    {
        return $this->update($id, $data);
    }

    // Delete all permissions for a role
    public function deleteByRole($roleId)
    {
        return $this->where('role_id', $roleId)->delete();
    }

    // Delete all roles for a permission
    public function deleteByPermission($permissionId)
    {
        return $this->where('permission_id', $permissionId)->delete();
    }
}