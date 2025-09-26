<?php
// Simple test untuk backend Work Order tanpa cURL
echo "=== Work Order Backend Test ===\n\n";

// 1. Check if CodeIgniter files exist
echo "1. Checking Core Files:\n";
$files_to_check = [
    '/opt/lampp/htdocs/optima1/app/Controllers/WorkOrderController.php',
    '/opt/lampp/htdocs/optima1/app/Config/Routes.php',
    '/opt/lampp/htdocs/optima1/app/Views/service/work_orders.php'
];

foreach($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✓ " . basename($file) . " exists\n";
    } else {
        echo "✗ " . basename($file) . " missing\n";
    }
}

// 2. Check database connection (using PDO)
echo "\n2. Testing Database Connection:\n";
try {
    $host = 'localhost';
    $dbname = 'optima';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connection successful\n";
    
    // Check required tables
    $tables = ['work_orders', 'units', 'staff', 'work_order_status', 'priorities', 'categories'];
    foreach($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "✓ Table '$table': $count records\n";
    }
    
    // Test work order number generation logic
    echo "\n3. Testing Work Order Number Generation:\n";
    $stmt = $pdo->query("SELECT MAX(CAST(work_order_number AS UNSIGNED)) as max_number FROM work_orders");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $nextNumber = 1;
    if ($result && !empty($result['max_number'])) {
        $nextNumber = intval($result['max_number']) + 1;
    }
    
    echo "✓ Current max WO number: " . ($result['max_number'] ?? 0) . "\n";
    echo "✓ Next WO number would be: $nextNumber\n";
    
    // Test default status
    echo "\n4. Testing Default Status:\n";
    $stmt = $pdo->query("SELECT * FROM work_order_status WHERE status_code = 'PENDING'");
    $defaultStatus = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($defaultStatus) {
        echo "✓ Default status found: ID " . $defaultStatus['id'] . " (" . $defaultStatus['status_name'] . ")\n";
    } else {
        echo "✗ Default status 'PENDING' not found\n";
    }
    
    // Test unit search simulation
    echo "\n5. Testing Unit Search Logic:\n";
    $stmt = $pdo->prepare("SELECT unit_code, unit_name FROM units WHERE unit_code LIKE ? OR unit_name LIKE ? LIMIT 5");
    $searchTerm = '%TR%';
    $stmt->execute([$searchTerm, $searchTerm]);
    $units = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($units) {
        echo "✓ Found " . count($units) . " units matching 'TR':\n";
        foreach($units as $unit) {
            echo "  - " . $unit['unit_code'] . ": " . $unit['unit_name'] . "\n";
        }
    } else {
        echo "✗ No units found with 'TR' pattern\n";
    }
    
    // Test staff search simulation
    echo "\n6. Testing Staff Search Logic:\n";
    $stmt = $pdo->prepare("SELECT staff_name, position FROM staff WHERE position LIKE ? AND staff_name LIKE ? LIMIT 5");
    $stmt->execute(['%mechanic%', '%mech%']);
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($staff) {
        echo "✓ Found " . count($staff) . " mechanics matching 'mech':\n";
        foreach($staff as $s) {
            echo "  - " . $s['staff_name'] . " (" . $s['position'] . ")\n";
        }
    } else {
        echo "✗ No mechanics found with 'mech' pattern\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// 3. Check controller methods exist
echo "\n7. Checking Controller Methods:\n";
$controller_file = '/opt/lampp/htdocs/optima1/app/Controllers/WorkOrderController.php';
if (file_exists($controller_file)) {
    $content = file_get_contents($controller_file);
    
    $methods = ['generateNumber', 'generateWorkOrderNumber', 'searchUnits', 'searchStaff', 'getPriority', 'getSubcategoryPriority', 'store'];
    
    foreach($methods as $method) {
        if (strpos($content, "function $method") !== false) {
            echo "✓ Method $method() found\n";
        } else {
            echo "✗ Method $method() missing\n";
        }
    }
}

echo "\n=== Test Complete ===\n";
?>