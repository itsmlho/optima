<?php
// Simple test untuk cek superadmin access
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'optima_ci';

try {
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "=== SUPERADMIN ACCESS ANALYSIS ===\n\n";
    
    // 1. Check superadmin user
    echo "1. SUPERADMIN USER INFO:\n";
    $user = $mysqli->query("SELECT id, username, email, is_active, created_at FROM users WHERE username = 'superadmin'")->fetch_assoc();
    if ($user) {
        echo "- ID: {$user['id']}\n";
        echo "- Username: {$user['username']}\n";
        echo "- Email: {$user['email']}\n";
        echo "- Is Active: {$user['is_active']}\n";
        echo "- Created: {$user['created_at']}\n";
    } else {
        echo "- SUPERADMIN USER NOT FOUND!\n";
    }
    
    // 2. Check user roles
    echo "\n2. SUPERADMIN ROLES:\n";
    $roles = $mysqli->query("
        SELECT r.id, r.name, r.description, d.name as division_name
        FROM user_roles ur 
        JOIN roles r ON r.id = ur.role_id 
        LEFT JOIN divisions d ON d.id = ur.division_id
        WHERE ur.user_id = {$user['id']}
    ");
    
    if ($roles->num_rows > 0) {
        while ($role = $roles->fetch_assoc()) {
            echo "- Role: {$role['name']} (Division: {$role['division_name']})\n";
        }
    } else {
        echo "- NO ROLES ASSIGNED!\n";
    }
    
    // 3. Check user permissions
    echo "\n3. SUPERADMIN PERMISSIONS:\n";
    $perms = $mysqli->query("
        SELECT p.key_name, p.display_name, p.description, up.granted
        FROM user_permissions up 
        JOIN permissions p ON p.id = up.permission_id 
        WHERE up.user_id = {$user['id']}
        ORDER BY p.key_name
    ");
    
    if ($perms && $perms->num_rows > 0) {
        $grantedCount = 0;
        while ($perm = $perms->fetch_assoc()) {
            $status = $perm['granted'] ? 'GRANTED' : 'DENIED';
            echo "- {$perm['key_name']}: {$status}\n";
            if ($perm['granted']) $grantedCount++;
        }
        echo "\nTotal permissions granted: $grantedCount\n";
    } else {
        echo "- NO DIRECT PERMISSIONS SET!\n";
    }
    
    // 4. Check role-based permissions
    echo "\n4. ROLE-BASED PERMISSIONS:\n";
    $rolePerms = $mysqli->query("
        SELECT DISTINCT p.key_name, p.display_name, rp.granted
        FROM user_roles ur
        JOIN role_permissions rp ON rp.role_id = ur.role_id
        JOIN permissions p ON p.id = rp.permission_id
        WHERE ur.user_id = {$user['id']} AND rp.granted = 1
        ORDER BY p.key_name
    ");
    
    if ($rolePerms && $rolePerms->num_rows > 0) {
        $count = 0;
        while ($perm = $rolePerms->fetch_assoc()) {
            echo "- {$perm['key_name']}: {$perm['display_name']}\n";
            $count++;
        }
        echo "\nTotal role permissions: $count\n";
    } else {
        echo "- NO ROLE PERMISSIONS FOUND!\n";
    }
    
    // 5. Check if there's a super admin role
    echo "\n5. SUPER ADMIN ROLES AVAILABLE:\n";
    $superRoles = $mysqli->query("
        SELECT id, name, description 
        FROM roles 
        WHERE name LIKE '%admin%' OR name LIKE '%super%'
        ORDER BY name
    ");
    
    while ($role = $superRoles->fetch_assoc()) {
        echo "- {$role['name']}: {$role['description']}\n";
    }
    
    // 6. Check total permissions in system
    echo "\n6. SYSTEM PERMISSIONS OVERVIEW:\n";
    $totalPerms = $mysqli->query("SELECT COUNT(*) as total FROM permissions")->fetch_assoc();
    echo "- Total permissions in system: {$totalPerms['total']}\n";
    
    $modules = $mysqli->query("SELECT DISTINCT module FROM permissions WHERE module IS NOT NULL ORDER BY module");
    echo "- Modules: ";
    $moduleList = [];
    while ($module = $modules->fetch_assoc()) {
        $moduleList[] = $module['module'];
    }
    echo implode(', ', $moduleList) . "\n";
    
    // 7. Check if superadmin should have all permissions
    echo "\n7. SOLUTION - CREATE SUPERADMIN ROLE:\n";
    echo "Creating full access role and assigning to superadmin...\n";
    
    // Check if super admin role exists
    $superRole = $mysqli->query("SELECT id FROM roles WHERE name = 'Super Administrator'")->fetch_assoc();
    if (!$superRole) {
        echo "- Creating Super Administrator role...\n";
        $mysqli->query("INSERT INTO roles (name, description, created_at, updated_at) VALUES ('Super Administrator', 'Full system access for superadmin user', NOW(), NOW())");
        $superRoleId = $mysqli->insert_id;
        echo "- Super Administrator role created with ID: $superRoleId\n";
    } else {
        $superRoleId = $superRole['id'];
        echo "- Super Administrator role already exists with ID: $superRoleId\n";
    }
    
    // Assign all permissions to super admin role
    echo "- Assigning all permissions to Super Administrator role...\n";
    $allPermissions = $mysqli->query("SELECT id FROM permissions WHERE is_active = 1");
    $assignedCount = 0;
    
    while ($permission = $allPermissions->fetch_assoc()) {
        // Check if already assigned
        $existing = $mysqli->query("SELECT id FROM role_permissions WHERE role_id = $superRoleId AND permission_id = {$permission['id']}")->fetch_assoc();
        if (!$existing) {
            $mysqli->query("INSERT INTO role_permissions (role_id, permission_id, granted, created_at, updated_at) VALUES ($superRoleId, {$permission['id']}, 1, NOW(), NOW())");
            $assignedCount++;
        }
    }
    echo "- Assigned $assignedCount new permissions to Super Administrator role\n";
    
    // Assign super admin role to superadmin user
    echo "- Assigning Super Administrator role to superadmin user...\n";
    $existingUserRole = $mysqli->query("SELECT id FROM user_roles WHERE user_id = {$user['id']} AND role_id = $superRoleId")->fetch_assoc();
    if (!$existingUserRole) {
        $mysqli->query("INSERT INTO user_roles (user_id, role_id, created_at, updated_at) VALUES ({$user['id']}, $superRoleId, NOW(), NOW())");
        echo "- Super Administrator role assigned to superadmin\n";
    } else {
        echo "- Super Administrator role already assigned to superadmin\n";
    }
    
    echo "\n✅ SUPERADMIN SETUP COMPLETE!\n";
    echo "Superadmin now has full access to all system features.\n";
    
    // Final verification
    echo "\n8. FINAL VERIFICATION:\n";
    $finalRoles = $mysqli->query("
        SELECT r.name, COUNT(rp.id) as permission_count
        FROM user_roles ur 
        JOIN roles r ON r.id = ur.role_id 
        LEFT JOIN role_permissions rp ON rp.role_id = ur.role_id AND rp.granted = 1
        WHERE ur.user_id = {$user['id']}
        GROUP BY r.id, r.name
    ");
    
    while ($role = $finalRoles->fetch_assoc()) {
        echo "- Role: {$role['name']} - {$role['permission_count']} permissions\n";
    }
    
    $totalUserPerms = $mysqli->query("
        SELECT COUNT(DISTINCT rp.permission_id) as total
        FROM user_roles ur
        JOIN role_permissions rp ON rp.role_id = ur.role_id
        WHERE ur.user_id = {$user['id']} AND rp.granted = 1
    ")->fetch_assoc();
    
    echo "\nTotal unique permissions for superadmin: {$totalUserPerms['total']}/115\n";
    
    if ($totalUserPerms['total'] >= 100) {
        echo "🎉 SUCCESS: Superadmin has comprehensive access!\n";
    } else {
        echo "⚠️  WARNING: Superadmin may still have limited access\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>