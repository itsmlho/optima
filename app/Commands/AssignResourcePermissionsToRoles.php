<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\RolePermissionModel;

class AssignResourcePermissionsToRoles extends BaseCommand
{
    protected $group       = 'rbac';
    protected $name        = 'rbac:assign-resource-permissions';
    protected $description = 'Assign resource permissions to roles based on recommendation plan';

    public function run(array $params)
    {
        CLI::write('Assigning Resource Permissions to Roles...', 'yellow');
        CLI::newLine();

        $roleModel = new RoleModel();
        $permissionModel = new PermissionModel();
        $rolePermissionModel = new RolePermissionModel();
        $db = \Config\Database::connect();

        // Get all permissions
        $permissions = $permissionModel->findAll();
        $permissionMap = [];
        foreach ($permissions as $perm) {
            $permissionMap[$perm['key']] = $perm['id'];
        }

        // Get all roles
        $roles = $roleModel->findAll();
        $roleMap = [];
        foreach ($roles as $role) {
            $roleMap[strtolower($role['name'])] = $role['id'];
        }

        // Permission assignment matrix
        $assignments = [
            // Super Administrator - All permissions
            'super administrator' => [
                'warehouse.inventory.view',
                'warehouse.inventory.manage',
                'marketing.kontrak.view',
                'service.workorder.view',
                'purchasing.po.view',
                'operational.delivery.view',
                'accounting.financial.view'
            ],

            // Marketing Head
            'marketing head' => [
                'warehouse.inventory.view',
                'service.workorder.view',
                'purchasing.po.view',
                'operational.delivery.view',
                'accounting.financial.view'
            ],

            // Marketing Staff
            'marketing staff' => [
                'warehouse.inventory.view',
                'service.workorder.view',
                'purchasing.po.view',
                'operational.delivery.view',
                'accounting.financial.view'
            ],

            // Service Head
            'service head' => [
                'warehouse.inventory.view',
                'warehouse.inventory.manage',
                'marketing.kontrak.view',
                'purchasing.po.view',
                'accounting.financial.view'
            ],

            // Service Staff
            'service staff' => [
                'warehouse.inventory.view',
                'warehouse.inventory.manage',
                'marketing.kontrak.view',
                'purchasing.po.view'
            ],

            // Warehouse Head
            'warehouse head' => [
                'marketing.kontrak.view',
                'service.workorder.view',
                'purchasing.po.view',
                'operational.delivery.view',
                'accounting.financial.view'
            ],

            // Warehouse Staff
            'warehouse staff' => [
                'marketing.kontrak.view',
                'service.workorder.view',
                'purchasing.po.view',
                'operational.delivery.view'
            ],

            // Purchasing Head
            'purchasing head' => [
                'warehouse.inventory.view',
                'marketing.kontrak.view',
                'service.workorder.view',
                'accounting.financial.view'
            ],

            // Purchasing Staff
            'purchasing staff' => [
                'warehouse.inventory.view',
                'marketing.kontrak.view',
                'service.workorder.view'
            ],

            // Operational Head
            'operational head' => [
                'marketing.kontrak.view',
                'warehouse.inventory.view',
                'service.workorder.view',
                'purchasing.po.view'
            ],

            // Operational Staff
            'operational staff' => [
                'marketing.kontrak.view',
                'warehouse.inventory.view',
                'service.workorder.view'
            ],

            // Accounting Head
            'accounting head' => [
                'marketing.kontrak.view',
                'purchasing.po.view',
                'service.workorder.view',
                'warehouse.inventory.view'
            ],

            // Accounting Staff
            'accounting staff' => [
                'marketing.kontrak.view',
                'purchasing.po.view',
                'service.workorder.view'
            ],

            // Perizinan Head
            'perizinan head' => [
                'warehouse.inventory.view',
                'marketing.kontrak.view'
            ],

            // Perizinan Staff
            'perizinan staff' => [
                'warehouse.inventory.view',
                'marketing.kontrak.view'
            ]
        ];

        $totalAssigned = 0;
        $totalSkipped = 0;

        foreach ($assignments as $roleName => $permissionKeys) {
            $roleId = $roleMap[strtolower($roleName)] ?? null;
            
            if (!$roleId) {
                CLI::write("  ⚠ Role '{$roleName}' not found, skipping...", 'yellow');
                continue;
            }

            CLI::write("  Processing role: {$roleName}...", 'cyan');

            foreach ($permissionKeys as $permissionKey) {
                $permissionId = $permissionMap[$permissionKey] ?? null;
                
                if (!$permissionId) {
                    CLI::write("    ⚠ Permission '{$permissionKey}' not found, skipping...", 'yellow');
                    $totalSkipped++;
                    continue;
                }

                // Check if already assigned
                $existing = $db->table('role_permissions')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    CLI::write("    ✓ Permission '{$permissionKey}' already assigned", 'green');
                    continue;
                }

                // Assign permission
                $db->table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'granted' => 1
                ]);

                CLI::write("    ✓ Assigned '{$permissionKey}'", 'green');
                $totalAssigned++;
            }
        }

        CLI::newLine();
        CLI::write("Summary:", 'yellow');
        CLI::write("  Total assigned: {$totalAssigned}", 'green');
        CLI::write("  Total skipped: {$totalSkipped}", 'yellow');
        CLI::newLine();
        CLI::write('Resource permissions assignment completed!', 'green');
    }
}

