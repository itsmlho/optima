<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResourcePermissions extends Migration
{
    public function up()
    {
        $this->db = \Config\Database::connect();
        
        // Resource permissions untuk cross-division access
        $resourcePermissions = [
            [
                'key' => 'warehouse.inventory.view',
                'name' => 'View Inventory (Cross-Division)',
                'description' => 'View inventory across divisions - untuk divisi lain yang perlu cek ketersediaan unit',
                'module' => 'warehouse',
                'category' => 'resource',
                'is_system_permission' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'warehouse.inventory.manage',
                'name' => 'Manage Inventory (Cross-Division)',
                'description' => 'Manage inventory across divisions - untuk Service yang perlu update status unit setelah maintenance',
                'module' => 'warehouse',
                'category' => 'resource',
                'is_system_permission' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'marketing.kontrak.view',
                'name' => 'View Kontrak (Cross-Division)',
                'description' => 'View kontrak across divisions - untuk Service, Operational, Warehouse, Accounting',
                'module' => 'marketing',
                'category' => 'resource',
                'is_system_permission' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'service.workorder.view',
                'name' => 'View Work Order (Cross-Division)',
                'description' => 'View work order across divisions - untuk Marketing, Warehouse, Accounting',
                'module' => 'service',
                'category' => 'resource',
                'is_system_permission' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'purchasing.po.view',
                'name' => 'View PO (Cross-Division)',
                'description' => 'View purchase order across divisions - untuk Marketing, Warehouse, Accounting',
                'module' => 'purchasing',
                'category' => 'resource',
                'is_system_permission' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'operational.delivery.view',
                'name' => 'View Delivery (Cross-Division)',
                'description' => 'View delivery across divisions - untuk Marketing, Warehouse',
                'module' => 'operational',
                'category' => 'resource',
                'is_system_permission' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'accounting.financial.view',
                'name' => 'View Financial (Cross-Division)',
                'description' => 'View financial data across divisions - untuk Marketing, Service, Purchasing',
                'module' => 'accounting',
                'category' => 'resource',
                'is_system_permission' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert resource permissions (skip if already exists)
        foreach ($resourcePermissions as $perm) {
            $existing = $this->db->table('permissions')
                ->where('key', $perm['key'])
                ->get()
                ->getRowArray();
            
            if (!$existing) {
                $this->db->table('permissions')->insert($perm);
            }
        }
    }

    public function down()
    {
        // Remove resource permissions
        $resourcePermissionKeys = [
            'warehouse.inventory.view',
            'warehouse.inventory.manage',
            'marketing.kontrak.view',
            'service.workorder.view',
            'purchasing.po.view',
            'operational.delivery.view',
            'accounting.financial.view'
        ];

        // Get permission IDs
        $permissionIds = $this->db->table('permissions')
            ->whereIn('key', $resourcePermissionKeys)
            ->select('id')
            ->get()
            ->getResultArray();
        
        $ids = array_column($permissionIds, 'id');

        if (!empty($ids)) {
            // Remove from role_permissions
            $this->db->table('role_permissions')
                ->whereIn('permission_id', $ids)
                ->delete();

            // Remove from user_permissions (if exists)
            if ($this->db->tableExists('user_permissions')) {
                $this->db->table('user_permissions')
                    ->whereIn('permission_id', $ids)
                    ->delete();
            }

            // Remove permissions
            $this->db->table('permissions')
                ->whereIn('id', $ids)
                ->delete();
        }
    }
}

