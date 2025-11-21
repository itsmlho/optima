<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'key',
        'name', 
        'description',
        'module',
        'category',
        'is_system_permission',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    // Timestamps
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation - disable karena kita validasi di controller
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Seed default core permissions if desired.
     * Idempotent: will not duplicate existing keys.
     */
    public function createDefaultPermissions(): void
    {
        $defaults = [
            ['key' => 'admin.access', 'name' => 'ADMIN ACCESS', 'module' => 'admin', 'category' => 'access'],
            ['key' => 'admin.user_management', 'name' => 'USER MANAGEMENT', 'module' => 'admin', 'category' => 'management'],
            ['key' => 'admin.role_management', 'name' => 'ROLE MANAGEMENT', 'module' => 'admin', 'category' => 'management'],
            ['key' => 'admin.permission_management', 'name' => 'PERMISSION MANAGEMENT', 'module' => 'admin', 'category' => 'management'],
        ];

        foreach ($defaults as $perm) {
            $exists = $this->where('key', $perm['key'])->first();
            if ($exists) continue;
            $this->insert([
                'key' => $perm['key'],
                'name' => $perm['name'],
                'description' => 'Default system permission',
                'module' => $perm['module'],
                'category' => $perm['category'],
                'is_system_permission' => 1,
                'is_active' => 1
            ]);
        }
    }
}