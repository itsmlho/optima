<?php
/**
 * Create test Service users for department filtering testing
 * Run: php tools/create_test_service_users.php
 */

$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);

// Get service division id
$r = $db->query("SELECT id FROM divisions WHERE LOWER(name) LIKE '%service%' LIMIT 1");
$service_div = $r->fetch_assoc()['id'];
echo "Service division_id: $service_div\n";

// Get roles for service
$r = $db->query("SELECT id, name, slug FROM roles WHERE division_id = $service_div ORDER BY name");
echo "\nAvailable Service Roles:\n";
$roles = [];
while ($row = $r->fetch_assoc()) {
    echo "  Role ID: {$row['id']} | {$row['name']} | slug={$row['slug']}\n";
    $roles[$row['slug']] = $row['id'];
}

// We need: admin_service_pusat (department-scoped) and admin_service_area (area-scoped)
$admin_pusat_role = $roles['admin_service_pusat'] ?? null;
$admin_area_role = $roles['admin_service_area'] ?? null;

echo "\nadmin_service_pusat role_id: $admin_pusat_role\n";
echo "admin_service_area role_id: $admin_area_role\n";

// Create test users
$password = password_hash('password123', PASSWORD_BCRYPT);

$testUsers = [
    [
        'username' => 'admin_diesel',
        'email' => 'admin_diesel@test.com',
        'role_id' => $admin_pusat_role,
        'dept_scope' => 'DIESEL',
        'description' => 'Admin Service Pusat - DIESEL only'
    ],
    [
        'username' => 'admin_electric',
        'email' => 'admin_electric@test.com',
        'role_id' => $admin_pusat_role,
        'dept_scope' => 'ELECTRIC',
        'description' => 'Admin Service Pusat - ELECTRIC only'
    ],
    [
        'username' => 'admin_area_test',
        'email' => 'admin_area@test.com',
        'role_id' => $admin_area_role,
        'dept_scope' => 'DIESEL_ELECTRIC_GASOLINE',
        'description' => 'Admin Service Area - ALL departments in area'
    ]
];

// Check user_area_access table structure
$r = $db->query("DESCRIBE user_area_access");
echo "\nuser_area_access columns:\n";
while ($row = $r->fetch_assoc()) {
    echo "  {$row['Field']} - {$row['Type']}\n";
}

// Get some areas for the area user
$r = $db->query("SELECT id, area_code, area_name FROM areas WHERE is_active = 1 LIMIT 5");
echo "\nSample areas:\n";
while ($row = $r->fetch_assoc()) {
    echo "  {$row['id']} | {$row['area_code']} | {$row['area_name']}\n";
}

echo "\n--- Creating test users ---\n";

foreach ($testUsers as $user) {
    // Check if user already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param('s', $user['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $existingUser = $result->fetch_assoc();
        echo "User '{$user['username']}' already exists (id={$existingUser['id']}), skipping creation.\n";
        $userId = $existingUser['id'];
    } else {
        // Create user
        $firstName = ucfirst(str_replace('_', ' ', $user['username']));
        $lastName = 'Test';
        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, division_id, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->bind_param('sssssi', $user['username'], $user['email'], $password, $firstName, $lastName, $service_div);
        $stmt->execute();
        $userId = $db->insert_id;
        echo "Created user '{$user['username']}' (id=$userId)\n";
    }
    
    // Check if user_role already exists
    $stmt = $db->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role_id = ? AND is_active = 1");
    $stmt->bind_param('ii', $userId, $user['role_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Create user_role
        $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id, division_id, assigned_by, assigned_at, is_active, created_at) VALUES (?, ?, ?, 1, NOW(), 1, NOW())");
        $stmt->bind_param('iii', $userId, $user['role_id'], $service_div);
        $stmt->execute();
        echo "  Assigned role_id={$user['role_id']} to user\n";
    } else {
        echo "  Role already assigned to user\n";
    }
    
    // Check if user_area_access already exists
    $stmt = $db->prepare("SELECT id FROM user_area_access WHERE user_id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Create user_area_access
        $areaType = 'CENTRAL';
        $stmt = $db->prepare("INSERT INTO user_area_access (user_id, department_scope, area_type, is_active, created_at) VALUES (?, ?, ?, 1, NOW())");
        $stmt->bind_param('iss', $userId, $user['dept_scope'], $areaType);
        $stmt->execute();
        echo "  Created area_access: dept_scope={$user['dept_scope']}\n";
    } else {
        echo "  Area access already exists\n";
    }
    
    echo "  -> {$user['description']}\n\n";
}

// For the area user, assign them to a specific area
echo "\n--- Assigning area access for admin_area_test ---\n";

// Check if area_user_assignments or similar exists
$r = $db->query("SHOW TABLES LIKE '%area%'");
echo "Area-related tables:\n";
while ($row = $r->fetch_row()) {
    echo "  $row[0]\n";
}

// Check user_area_access for area assignments
$r = $db->query("SELECT * FROM user_area_access ORDER BY user_id");
echo "\nAll user_area_access records:\n";
while ($row = $r->fetch_assoc()) {
    echo "  user_id={$row['user_id']} | dept_scope={$row['department_scope']} | area_type={$row['area_type']} | specific_areas={$row['specific_areas']} | active={$row['is_active']}\n";
}

echo "\n✅ Done! Test users created.\n";
echo "Login credentials:\n";
echo "  admin_diesel / password123 (DIESEL only)\n";
echo "  admin_electric / password123 (ELECTRIC only)\n";
echo "  admin_area_test / password123 (ALL departments, area-scoped)\n";

$db->close();
