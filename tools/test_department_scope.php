<?php
/**
 * Test script to verify department scope detection logic for Service users
 * Simulates what get_user_area_department_scope() does
 * Run: php tools/test_department_scope.php
 */

$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);

$departmentMap = [
    'DIESEL' => [1],
    'ELECTRIC' => [2],
    'GASOLINE' => [3],
    'DIESEL_GASOLINE' => [1, 3],
    'DIESEL_ELECTRIC_GASOLINE' => [1, 2, 3],
    'ALL' => [1, 2, 3],
];

$testUsers = [
    ['username' => 'superadmin', 'expected' => 'Full access (super_admin bypass)'],
    ['username' => 'admin_diesel', 'expected' => 'DIESEL only [1]'],
    ['username' => 'admin_electric', 'expected' => 'ELECTRIC only [2]'],
    ['username' => 'admin_area_test', 'expected' => 'ALL departments [1,2,3]'],
    ['username' => 'admin_marketing', 'expected' => 'Full access (non-service)'],
];

foreach ($testUsers as $test) {
    echo "\n=== Testing: {$test['username']} ===\n";
    echo "Expected: {$test['expected']}\n";
    
    // Get user
    $stmt = $db->prepare("SELECT id, username, division_id, is_super_admin FROM users WHERE username = ?");
    $stmt->bind_param('s', $test['username']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) { echo "  ❌ User not found!\n"; continue; }
    
    // Get role slug
    $stmt = $db->prepare("SELECT r.slug, r.name FROM user_roles ur JOIN roles r ON r.id = ur.role_id WHERE ur.user_id = ? AND ur.is_active = 1 AND r.is_active = 1 ORDER BY r.is_system_role DESC, ur.created_at ASC LIMIT 1");
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $role = $stmt->get_result()->fetch_assoc();
    $roleSlug = $role['slug'] ?? 'none';
    $roleName = $role['name'] ?? 'none';
    
    echo "  User ID: {$user['id']}, Role: {$roleName} (slug: {$roleSlug})\n";
    
    // Simulate get_user_area_department_scope() logic:
    
    // Normalize role (the fix we applied)
    $normalizedRole = strtolower(str_replace(['_', '-'], ' ', $roleSlug));
    echo "  Normalized role: '$normalizedRole'\n";
    
    // 1. Super admin check
    if ($user['is_super_admin'] == 1 || in_array($normalizedRole, ['super administrator', 'administrator', 'super admin'])) {
        echo "  ✅ Result: null (FULL ACCESS - super admin)\n";
        continue;
    }
    
    // 2. Get division
    $stmt = $db->prepare("SELECT d.name FROM user_roles ur JOIN roles r ON ur.role_id = r.id JOIN divisions d ON r.division_id = d.id WHERE ur.user_id = ? AND ur.is_active = 1 LIMIT 1");
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $div = $stmt->get_result()->fetch_assoc();
    $divName = $div['name'] ?? 'unknown';
    
    if (strtolower($divName) !== 'service') {
        echo "  ✅ Result: null (FULL ACCESS - non-service division: $divName)\n";
        continue;
    }
    
    echo "  Division: $divName (Service - filtering applies)\n";
    
    // 3. Head Service check
    if ($normalizedRole === 'head service') {
        echo "  ✅ Result: null (FULL ACCESS - Head Service)\n";
        continue;
    }
    
    // 4-6. Service roles - get user_area_access
    $stmt = $db->prepare("SELECT department_scope, area_type, specific_areas FROM user_area_access WHERE user_id = ? AND is_active = 1");
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $access = $stmt->get_result()->fetch_assoc();
    
    if (!$access) {
        echo "  ❌ No user_area_access record found!\n";
        continue;
    }
    
    echo "  user_area_access: dept_scope={$access['department_scope']}, area_type={$access['area_type']}\n";
    
    // Map department scope to IDs
    $deptIds = $departmentMap[$access['department_scope']] ?? [];
    
    // Determine access type based on role
    $accessType = 'ALL'; // default
    if ($normalizedRole === 'admin service pusat') $accessType = 'ALL';
    elseif ($normalizedRole === 'admin service area') $accessType = 'BRANCH';
    elseif (in_array($normalizedRole, ['supervisor service', 'staff service'])) $accessType = 'ASSIGNED';
    
    echo "  Access type: $accessType\n";
    echo "  ✅ Result: departments=[" . implode(',', $deptIds) . "] | dept_scope={$access['department_scope']}\n";
    
    // Verify unit counts for these departments
    if (!empty($deptIds)) {
        $placeholders = implode(',', array_fill(0, count($deptIds), '?'));
        $types = str_repeat('i', count($deptIds));
        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM inventory_unit WHERE departemen_id IN ($placeholders)");
        $stmt->bind_param($types, ...$deptIds);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['cnt'];
        echo "  📊 Units matching scope: $count\n";
    }
}

echo "\n✅ All tests complete.\n";
$db->close();
