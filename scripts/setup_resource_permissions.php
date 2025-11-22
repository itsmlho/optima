<?php

/**
 * Script untuk setup Resource Permissions
 * Run: php scripts/setup_resource_permissions.php
 */

// Direct database connection using PDO
$host = '127.0.0.1';
$username = 'root';
$password = 'root';
$database = 'optima_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}

echo "========================================\n";
echo "Setup Resource Permissions\n";
echo "========================================\n\n";

// 1. Insert Resource Permissions
echo "1. Inserting Resource Permissions...\n";

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

$inserted = 0;
$skipped = 0;

foreach ($resourcePermissions as $perm) {
    // Check if already exists
    $stmt = $pdo->prepare("SELECT id FROM permissions WHERE `key` = ?");
    $stmt->execute([$perm['key']]);
    
    if ($stmt->rowCount() > 0) {
        echo "  ⚠ Permission '{$perm['key']}' already exists, skipping...\n";
        $skipped++;
        continue;
    }

    // Insert permission
    $stmt = $pdo->prepare("INSERT INTO permissions (`key`, name, description, module, category, is_system_permission, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $perm['key'],
        $perm['name'],
        $perm['description'],
        $perm['module'],
        $perm['category'],
        $perm['is_system_permission'],
        $perm['is_active'],
        $perm['created_at'],
        $perm['updated_at']
    ]);
    
    echo "  ✓ Inserted '{$perm['key']}'\n";
    $inserted++;
}

echo "\n  Summary: {$inserted} inserted, {$skipped} skipped\n\n";

// 2. Get all permissions and roles
echo "2. Getting permissions and roles...\n";

$permissions = $pdo->query("SELECT id, `key` FROM permissions")->fetchAll(PDO::FETCH_ASSOC);
$permissionMap = [];
foreach ($permissions as $perm) {
    $permissionMap[$perm['key']] = $perm['id'];
}

$roles = $pdo->query("SELECT id, name FROM roles")->fetchAll(PDO::FETCH_ASSOC);
$roleMap = [];
foreach ($roles as $role) {
    $roleMap[strtolower($role['name'])] = $role['id'];
}

echo "  Found " . count($permissions) . " permissions\n";
echo "  Found " . count($roles) . " roles\n\n";

// 3. Assign Resource Permissions to Roles
echo "3. Assigning Resource Permissions to Roles...\n";

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
$totalErrors = 0;

foreach ($assignments as $roleName => $permissionKeys) {
    $roleId = $roleMap[strtolower($roleName)] ?? null;
    
    if (!$roleId) {
        echo "  ⚠ Role '{$roleName}' not found, skipping...\n";
        $totalErrors++;
        continue;
    }

    echo "  Processing role: {$roleName}...\n";

    foreach ($permissionKeys as $permissionKey) {
        $permissionId = $permissionMap[$permissionKey] ?? null;
        
        if (!$permissionId) {
            echo "    ⚠ Permission '{$permissionKey}' not found, skipping...\n";
            $totalSkipped++;
            continue;
        }

        // Check if already assigned
        $stmt = $pdo->prepare("SELECT id FROM role_permissions WHERE role_id = ? AND permission_id = ?");
        $stmt->execute([$roleId, $permissionId]);
        
        if ($stmt->rowCount() > 0) {
            echo "    ✓ Permission '{$permissionKey}' already assigned\n";
            continue;
        }

        // Assign permission
        try {
            $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id, granted) VALUES (?, ?, 1)");
            $stmt->execute([$roleId, $permissionId]);
            
            echo "    ✓ Assigned '{$permissionKey}'\n";
            $totalAssigned++;
        } catch (PDOException $e) {
            echo "    ✗ Error assigning '{$permissionKey}': " . $e->getMessage() . "\n";
            $totalErrors++;
        }
    }
}

echo "\n========================================\n";
echo "Summary:\n";
echo "  Permissions inserted: {$inserted}\n";
echo "  Permissions skipped: {$skipped}\n";
echo "  Role permissions assigned: {$totalAssigned}\n";
echo "  Role permissions skipped: {$totalSkipped}\n";
echo "  Errors: {$totalErrors}\n";
echo "========================================\n\n";

// 4. Verify
echo "4. Verifying setup...\n";

$result = $pdo->query("SELECT COUNT(*) as total FROM permissions")->fetch(PDO::FETCH_ASSOC);
$totalPermissions = $result['total'];

$result = $pdo->query("SELECT COUNT(*) as total FROM permissions WHERE category = 'resource'")->fetch(PDO::FETCH_ASSOC);
$resourcePermissions = $result['total'];

echo "  Total permissions: {$totalPermissions}\n";
echo "  Resource permissions: {$resourcePermissions}\n";

$result = $pdo->query("SELECT COUNT(*) as total FROM role_permissions")->fetch(PDO::FETCH_ASSOC);
$totalRolePermissions = $result['total'];
echo "  Total role-permission assignments: {$totalRolePermissions}\n";

echo "\n✅ Setup completed!\n";

