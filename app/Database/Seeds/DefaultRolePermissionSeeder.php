<?php

/**
 * DEFAULT ROLE PERMISSION MAPPINGS SEEDER
 * Membuat mapping default permission untuk roles yang sudah ada
 */

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DefaultRolePermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        echo "Creating default role-permission mappings...\n";
        
        // Get all permissions
        $permissions = $db->table('permissions')->get()->getResultArray();
        $permissionIds = array_column($permissions, 'id', 'key_name');
        
        // Get all roles
        $roles = $db->table('roles')->get()->getResultArray();
        
        if (empty($roles)) {
            echo "No roles found. Creating default roles...\n";
            
            // Create default roles if none exist
            $defaultRoles = [
                [
                    'name' => 'Super Admin',
                    'description' => 'Full system access',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Marketing Manager',
                    'description' => 'Full marketing module access',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Service Manager',
                    'description' => 'Full service module access',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Purchasing Manager',
                    'description' => 'Full purchasing module access',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Warehouse Manager',
                    'description' => 'Full warehouse module access',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Accounting Manager',
                    'description' => 'Full accounting module access',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Service User',
                    'description' => 'Limited service access for field technicians',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Marketing Staff',
                    'description' => 'Limited marketing access for staff',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            foreach ($defaultRoles as $role) {
                $db->table('roles')->insert($role);
            }
            
            // Refresh roles list
            $roles = $db->table('roles')->get()->getResultArray();
        }
        
        // Define role permission mappings based on existing roles
        $rolePermissionMappings = [
            
            // SUPER ADMINISTRATOR - All permissions
            'Super Administrator' => array_keys($permissionIds), // All permission keys
            'Administrator' => array_keys($permissionIds), // All permission keys
            
            // HEAD ROLES - Full module access + admin
            'Head Marketing' => [
                // Marketing permissions
                'marketing.customer.navigation', 'marketing.customer.index', 'marketing.customer.create', 
                'marketing.customer.edit', 'marketing.customer.delete', 'marketing.customer.export',
                'marketing.customer_db.navigation', 'marketing.customer_db.index', 'marketing.customer_db.search', 'marketing.customer_db.export',
                'marketing.quotation.navigation', 'marketing.quotation.index', 'marketing.quotation.create', 
                'marketing.quotation.edit', 'marketing.quotation.delete', 'marketing.quotation.approve', 'marketing.quotation.print',
                'marketing.spk.navigation', 'marketing.spk.index', 'marketing.spk.create', 
                'marketing.spk.edit', 'marketing.spk.delete', 'marketing.spk.close',
                'marketing.delivery.navigation', 'marketing.delivery.index', 'marketing.delivery.create', 
                'marketing.delivery.edit', 'marketing.delivery.print',
                // Admin access
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats', 'admin.dashboard.reports'
            ],
            
            'Head Service' => [
                'service.workorder.navigation', 'service.workorder.index', 'service.workorder.create', 
                'service.workorder.edit', 'service.workorder.assign', 'service.workorder.complete',
                'service.pmps.navigation', 'service.pmps.index', 'service.pmps.create', 
                'service.pmps.edit', 'service.pmps.execute',
                'service.area.navigation', 'service.area.index', 'service.area.create', 
                'service.area.edit', 'service.area.assign_user',
                'service.user.navigation', 'service.user.index', 'service.user.create', 
                'service.user.edit', 'service.user.assign_area', 'service.user.assign_branch',
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats', 'admin.dashboard.reports'
            ],
            
            'Head Purchasing' => [
                'purchasing.po.navigation', 'purchasing.po.index', 'purchasing.po.create', 
                'purchasing.po.edit', 'purchasing.po.approve',
                'purchasing.po_sparepart.navigation', 'purchasing.po_sparepart.index', 'purchasing.po_sparepart.create',
                'purchasing.supplier.navigation', 'purchasing.supplier.index', 'purchasing.supplier.create', 'purchasing.supplier.edit',
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats', 'admin.dashboard.reports'
            ],
            
            'Head Warehouse' => [
                'warehouse.unit_inventory.navigation', 'warehouse.unit_inventory.index', 'warehouse.unit_inventory.create', 'warehouse.unit_inventory.edit',
                'warehouse.attachment_inventory.navigation', 'warehouse.attachment_inventory.index', 'warehouse.attachment_inventory.create',
                'warehouse.sparepart_inventory.navigation', 'warehouse.sparepart_inventory.index', 'warehouse.sparepart_inventory.create',
                'warehouse.sparepart_usage.navigation', 'warehouse.sparepart_usage.index', 'warehouse.sparepart_usage.create', 'warehouse.sparepart_usage.return',
                'warehouse.po_verification.navigation', 'warehouse.po_verification.index', 'warehouse.po_verification.verify', 'warehouse.po_verification.approve',
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats', 'admin.dashboard.reports'
            ],
            
            'Head Accounting' => [
                'accounting.invoice.navigation', 'accounting.invoice.index', 'accounting.invoice.create', 
                'accounting.invoice.edit', 'accounting.invoice.approve', 'accounting.invoice.print',
                'accounting.payment.navigation', 'accounting.payment.index', 'accounting.payment.validate', 
                'accounting.payment.approve', 'accounting.payment.reject',
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats', 'admin.dashboard.reports'
            ],
            
            'Head Operational' => [
                'operational.delivery.navigation', 'operational.delivery.index', 'operational.delivery.create',
                'operational.delivery.dispatch', 'operational.delivery.track',
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats', 'admin.dashboard.reports'
            ],
            
            'Head IT' => [
                // Full admin access for IT
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats', 'admin.dashboard.reports',
                'admin.config.navigation', 'admin.config.index', 'admin.config.edit', 'admin.config.backup', 'admin.config.restore'
            ],
            
            'Head HRD' => [
                // Service user management for HRD
                'service.user.navigation', 'service.user.index', 'service.user.create', 
                'service.user.edit', 'service.user.assign_area', 'service.user.assign_branch',
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats'
            ],
            
            // STAFF ROLES - Limited access within their modules
            'Staff Marketing' => [
                'marketing.customer.navigation', 'marketing.customer.index', 'marketing.customer.create', 'marketing.customer.edit',
                'marketing.customer_db.navigation', 'marketing.customer_db.index', 'marketing.customer_db.search',
                'marketing.quotation.navigation', 'marketing.quotation.index', 'marketing.quotation.create', 'marketing.quotation.edit',
                'marketing.spk.navigation', 'marketing.spk.index', 'marketing.spk.create', 'marketing.spk.edit',
                'marketing.delivery.navigation', 'marketing.delivery.index', 'marketing.delivery.create', 'marketing.delivery.edit',
                'admin.dashboard.navigation', 'admin.dashboard.index'
            ],
            
            'Staff Service' => [
                'service.workorder.navigation', 'service.workorder.index', 'service.workorder.edit', 'service.workorder.complete',
                'service.pmps.navigation', 'service.pmps.index', 'service.pmps.execute',
                'admin.dashboard.navigation', 'admin.dashboard.index'
            ],
            
            'Staff Purchasing' => [
                'purchasing.po.navigation', 'purchasing.po.index', 'purchasing.po.create', 'purchasing.po.edit',
                'purchasing.po_sparepart.navigation', 'purchasing.po_sparepart.index', 'purchasing.po_sparepart.create',
                'purchasing.supplier.navigation', 'purchasing.supplier.index',
                'admin.dashboard.navigation', 'admin.dashboard.index'
            ],
            
            'Staff Warehouse' => [
                'warehouse.unit_inventory.navigation', 'warehouse.unit_inventory.index', 'warehouse.unit_inventory.create', 'warehouse.unit_inventory.edit',
                'warehouse.attachment_inventory.navigation', 'warehouse.attachment_inventory.index', 'warehouse.attachment_inventory.create',
                'warehouse.sparepart_inventory.navigation', 'warehouse.sparepart_inventory.index', 'warehouse.sparepart_inventory.create',
                'warehouse.sparepart_usage.navigation', 'warehouse.sparepart_usage.index', 'warehouse.sparepart_usage.create',
                'warehouse.po_verification.navigation', 'warehouse.po_verification.index', 'warehouse.po_verification.verify',
                'admin.dashboard.navigation', 'admin.dashboard.index'
            ],
            
            'Staff Accounting' => [
                'accounting.invoice.navigation', 'accounting.invoice.index', 'accounting.invoice.create', 'accounting.invoice.edit',
                'accounting.payment.navigation', 'accounting.payment.index', 'accounting.payment.validate',
                'admin.dashboard.navigation', 'admin.dashboard.index'
            ],
            
            'Staff Operational' => [
                'operational.delivery.navigation', 'operational.delivery.index', 'operational.delivery.create',
                'admin.dashboard.navigation', 'admin.dashboard.index'
            ],
            
            'Staff IT' => [
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats',
                'admin.config.navigation', 'admin.config.index'
            ],
            
            'Staff HRD' => [
                'service.user.navigation', 'service.user.index', 'service.user.edit',
                'admin.dashboard.navigation', 'admin.dashboard.index'
            ],
            
            // SERVICE SPECIFIC ROLES
            'Admin Service Pusat' => [
                'service.workorder.navigation', 'service.workorder.index', 'service.workorder.create', 
                'service.workorder.edit', 'service.workorder.assign', 'service.workorder.complete',
                'service.pmps.navigation', 'service.pmps.index', 'service.pmps.create', 'service.pmps.edit', 'service.pmps.execute',
                'service.area.navigation', 'service.area.index', 'service.area.create', 'service.area.edit', 'service.area.assign_user',
                'service.user.navigation', 'service.user.index', 'service.user.create', 'service.user.edit', 
                'service.user.assign_area', 'service.user.assign_branch',
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats'
            ],
            
            'Admin Service Area' => [
                'service.workorder.navigation', 'service.workorder.index', 'service.workorder.create', 
                'service.workorder.edit', 'service.workorder.assign', 'service.workorder.complete',
                'service.pmps.navigation', 'service.pmps.index', 'service.pmps.create', 'service.pmps.edit', 'service.pmps.execute',
                'service.area.navigation', 'service.area.index',
                'admin.dashboard.navigation', 'admin.dashboard.index'
            ],
            
            'Manager Service Area' => [
                'service.workorder.navigation', 'service.workorder.index', 'service.workorder.create', 
                'service.workorder.edit', 'service.workorder.assign', 'service.workorder.complete',
                'service.pmps.navigation', 'service.pmps.index', 'service.pmps.create', 'service.pmps.edit', 'service.pmps.execute',
                'service.area.navigation', 'service.area.index', 'service.area.edit', 'service.area.assign_user',
                'service.user.navigation', 'service.user.index', 'service.user.edit', 'service.user.assign_area', 'service.user.assign_branch',
                'admin.dashboard.navigation', 'admin.dashboard.index', 'admin.dashboard.stats'
            ],
            
            'Supervisor Service' => [
                'service.workorder.navigation', 'service.workorder.index', 'service.workorder.edit', 
                'service.workorder.assign', 'service.workorder.complete',
                'service.pmps.navigation', 'service.pmps.index', 'service.pmps.execute',
                'admin.dashboard.navigation', 'admin.dashboard.index'
            ]
        ];
        
        // Clear existing role permissions
        $db->table('role_permissions')->truncate();
        
        // Insert role permissions
        $totalInserted = 0;
        foreach ($roles as $role) {
            $roleName = $role['name'];
            
            if (!isset($rolePermissionMappings[$roleName])) {
                echo "Warning: No permission mapping defined for role '{$roleName}'\n";
                continue;
            }
            
            $permissionKeys = $rolePermissionMappings[$roleName];
            
            foreach ($permissionKeys as $permissionKey) {
                if (!isset($permissionIds[$permissionKey])) {
                    echo "Warning: Permission '{$permissionKey}' not found for role '{$roleName}'\n";
                    continue;
                }
                
                $rolePermissionData = [
                    'role_id' => $role['id'],
                    'permission_id' => $permissionIds[$permissionKey],
                    'granted' => 1,
                    'assigned_at' => date('Y-m-d H:i:s')
                ];
                
                try {
                    $db->table('role_permissions')->insert($rolePermissionData);
                    $totalInserted++;
                } catch (\Exception $e) {
                    echo "Error inserting permission '{$permissionKey}' for role '{$roleName}': " . $e->getMessage() . "\n";
                }
            }
            
            $permissionCount = count($permissionKeys);
            echo "✓ Created {$permissionCount} permissions for role '{$roleName}'\n";
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "DEFAULT ROLE PERMISSION MAPPING COMPLETE\n";
        echo str_repeat("=", 60) . "\n";
        echo "Total Role-Permission Mappings Created: {$totalInserted}\n";
        echo "Total Roles: " . count($roles) . "\n";
        echo "Total Permissions Available: " . count($permissions) . "\n";
        echo str_repeat("=", 60) . "\n";
        
        // Show role summary
        echo "ROLE SUMMARY:\n";
        foreach ($roles as $role) {
            $permissionCount = $db->table('role_permissions')
                ->where('role_id', $role['id'])
                ->where('granted', 1)
                ->countAllResults();
            
            echo "- {$role['name']}: {$permissionCount} permissions\n";
        }
        
        echo "\nNext Steps:\n";
        echo "1. Update PermissionHelper to use new permission structure\n";
        echo "2. Update controllers with granular permission checks\n";
        echo "3. Update sidebar navigation with permission checks\n";
        echo "4. Test role-based access across all modules\n";
        echo "5. Assign roles to existing users\n";
    }
}