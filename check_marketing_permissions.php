#!/usr/bin/env php
<?php
/**
 * Diagnostic Script - Check Marketing Permissions
 * Run: php check_marketing_permissions.php
 */

// Simple database connection
$host = 'localhost';
$database = 'optima_db';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

echo "\n========================================\n";
echo "DIAGNOSTIC: Marketing Permissions\n";
echo "========================================\n\n";

// 1. Check Staff Marketing Role
echo "1. Checking Staff Marketing Role:\n";
echo "-----------------------------------\n";
$stmt = $db->query("SELECT * FROM roles WHERE name LIKE '%Marketing%' OR name LIKE '%marketing%'");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
$staffMarketingRoleId = null;
if (empty($roles)) {
    echo "❌ NO MARKETING ROLE FOUND!\n\n";
} else {
    foreach ($roles as $role) {
        echo "✅ Role ID: {$role['id']}, Name: {$role['name']}, Display: {$role['display_name']}\n";
        $staffMarketingRoleId = $role['id'];
    }
    echo "\n";
}

// 2. Check Marketing Permissions
echo "2. Checking Marketing Module Permissions:\n";
echo "-----------------------------------\n";
$stmt = $db->query("SELECT * FROM permissions WHERE module = 'marketing' ORDER BY page, action LIMIT 20");
$marketingPerms = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($marketingPerms)) {
    echo "❌ NO MARKETING PERMISSIONS FOUND IN DATABASE!\n\n";
} else {
    echo "Found " . count($marketingPerms) . " marketing permissions\n";
    foreach (array_slice($marketingPerms, 0, 10) as $perm) {
        echo "   - ID {$perm['id']}: {$perm['key_name']} ({$perm['display_name']})\n";
    }
    echo "\n";
}

// 3. Check Role-Permission Assignments
if (!empty($roles)) {
    echo "3. Checking Role-Permission Assignments:\n";
    echo "-----------------------------------\n";
    foreach ($roles as $role) {
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            WHERE rp.role_id = ? AND p.module = 'marketing' AND rp.granted = 1
        ");
        $stmt->execute([$role['id']]);
        $assigned = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Role '{$role['name']}' (ID {$role['id']}): {$assigned['count']} marketing permissions assigned\n";
        
        if ($assigned['count'] == 0) {
            echo "   ❌ NO MARKETING PERMISSIONS ASSIGNED!\n";
        } else {
            // Show sample permissions
            $stmt2 = $db->prepare("
                SELECT p.key_name
                FROM role_permissions rp
                INNER JOIN permissions p ON rp.permission_id = p.id
                WHERE rp.role_id = ? AND p.module = 'marketing' AND rp.granted = 1
                LIMIT 5
            ");
            $stmt2->execute([$role['id']]);
            $samples = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($samples as $s) {
                echo "   ✅ {$s['key_name']}\n";
            }
        }
        echo "\n";
    }
}

// 4. Check User (Firsty) Assignment
echo "4. Checking User 'Firsty' Assignment:\n";
echo "-----------------------------------\n";
$stmt = $db->query("SELECT * FROM user WHERE username = 'Firsty1a21' OR email LIKE '%marketing@sml%'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "❌ User Firsty not found!\n\n";
} else {
    echo "✅ User ID: {$user['user_id']}, Username: {$user['username']}, Name: {$user['first_name']} {$user['last_name']}\n";
    
    // Check user roles
    $stmt2 = $db->prepare("
        SELECT ur.*, r.name, r.display_name
        FROM user_roles ur
        INNER JOIN roles r ON ur.role_id = r.id
        WHERE ur.user_id = ?
    ");
    $stmt2->execute([$user['user_id']]);
    $userRoles = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($userRoles)) {
        echo "   ❌ NO ROLE ASSIGNED TO USER!\n";
    } else {
        foreach ($userRoles as $ur) {
            echo "   ✅ Assigned Role: {$ur['name']} (ID {$ur['role_id']}), Active: " . ($ur['is_active'] ? 'YES' : 'NO') . "\n";
        }
    }
    echo "\n";
    
    // Check effective permissions
    $stmt3 = $db->prepare("
        SELECT COUNT(*) as count
        FROM role_permissions rp
        INNER JOIN permissions p ON rp.permission_id = p.id
        INNER JOIN user_roles ur ON ur.role_id = rp.role_id
        WHERE ur.user_id = ? AND p.module = 'marketing' AND rp.granted = 1 AND ur.is_active = 1
    ");
    $stmt3->execute([$user['user_id']]);
    $effectivePerms = $stmt3->fetch(PDO::FETCH_ASSOC);
    
    echo "   Effective Marketing Permissions: {$effectivePerms['count']}\n";
    if ($effectivePerms['count'] == 0) {
        echo "   ❌ USER HAS NO MARKETING PERMISSIONS!\n";
    }
}

echo "\n========================================\n";
echo "RECOMMENDATION:\n";
echo "========================================\n";
if (!empty($roles) && !empty($marketingPerms)) {
    echo "Run this SQL to assign ALL marketing permissions to Staff Marketing role:\n\n";
    echo "INSERT INTO role_permissions (role_id, permission_id, granted)\n";
    echo "SELECT {$roles[0]['id']}, p.id, 1\n";
    echo "FROM permissions p\n";
    echo "WHERE p.module = 'marketing'\n";
    echo "AND NOT EXISTS (\n";
    echo "    SELECT 1 FROM role_permissions rp\n";
    echo "    WHERE rp.role_id = {$roles[0]['id']} AND rp.permission_id = p.id\n";
    echo ");\n\n";
}

echo "========================================\n\n";
