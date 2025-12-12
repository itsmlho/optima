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
                'module' => 'warehouse',
                'page' => 'inventory',
                'action' => 'view_cross_division',
                'key_name' => 'warehouse.inventory.view_cross_division',
                'display_name' => 'View Inventory (Cross-Division)',
                'description' => 'View inventory across divisions - untuk divisi lain yang perlu cek ketersediaan unit',
                'category' => 'resource',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'module' => 'warehouse',
                'page' => 'inventory',
                'action' => 'manage_cross_division',
                'key_name' => 'warehouse.inventory.manage_cross_division',
                'display_name' => 'Manage Inventory (Cross-Division)',
                'description' => 'Manage inventory across divisions - untuk Service yang perlu update status unit setelah maintenance',
                'category' => 'resource',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'module' => 'marketing',
                'page' => 'kontrak',
                'action' => 'view_cross_division',
                'key_name' => 'marketing.kontrak.view_cross_division',
                'display_name' => 'View Kontrak (Cross-Division)',
                'description' => 'View kontrak across divisions - untuk Service, Operational, Warehouse, Accounting',
                'category' => 'resource',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'module' => 'service',
                'page' => 'workorder',
                'action' => 'view_cross_division',
                'key_name' => 'service.workorder.view_cross_division',
                'display_name' => 'View Work Order (Cross-Division)',
                'description' => 'View work order across divisions - untuk Marketing, Warehouse, Accounting',
                'category' => 'resource',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'module' => 'purchasing',
                'page' => 'po',
                'action' => 'view_cross_division',
                'key_name' => 'purchasing.po.view_cross_division',
                'display_name' => 'View PO (Cross-Division)',
                'description' => 'View purchase order across divisions - untuk Marketing, Warehouse, Accounting',
                'category' => 'resource',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'module' => 'operational',
                'page' => 'delivery',
                'action' => 'view_cross_division',
                'key_name' => 'operational.delivery.view_cross_division',
                'display_name' => 'View Delivery (Cross-Division)',
                'description' => 'View delivery across divisions - untuk Marketing, Warehouse',
                'category' => 'resource',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'module' => 'accounting',
                'page' => 'financial',
                'action' => 'view_cross_division',
                'key_name' => 'accounting.financial.view_cross_division',
                'display_name' => 'View Financial (Cross-Division)',
                'description' => 'View financial data across divisions - untuk Marketing, Service, Purchasing',
                'category' => 'resource',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert resource permissions (skip if already exists)
        foreach ($resourcePermissions as $perm) {
            $existing = $this->db->table('permissions')
                ->where('key_name', $perm['key_name'])
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
            'warehouse.inventory.view_cross_division',
            'warehouse.inventory.manage_cross_division',
            'marketing.kontrak.view_cross_division',
            'service.workorder.view_cross_division',
            'purchasing.po.view_cross_division',
            'operational.delivery.view_cross_division',
            'accounting.financial.view_cross_division'
        ];

        // Get permission IDs
        $permissionIds = $this->db->table('permissions')
            ->whereIn('key_name', $resourcePermissionKeys)
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

