<?php

require_once 'app/Config/Autoload.php';
require_once 'vendor/autoload.php';

use CodeIgniter\Config\Services;
use App\Models\AreaModel;
use App\Models\StaffModel;
use App\Models\AreaStaffAssignmentModel;
use App\Models\CustomerModel;
use App\Controllers\WorkOrderController;

// Initialize CodeIgniter
$autoloader = new \Config\Autoload();
$autoloader->initialize();

echo "<h1>Area-based Staff Assignment Integration Test</h1>";
echo "<p>Testing workflow: Create area → staff → assignment → work order → verify auto assignment</p>";

// Initialize models
$areaModel = new AreaModel();
$staffModel = new StaffModel();
$assignmentModel = new AreaStaffAssignmentModel();
$customerModel = new CustomerModel();

echo "<h2>Step 1: Create Test Area</h2>";

// Create test area
$testAreaData = [
    'area_code' => 'TEST-AREA-' . date('His'),
    'area_name' => 'Test Area for Integration',
    'description' => 'Test area for auto staff assignment integration'
];

$areaId = $areaModel->insert($testAreaData);
if ($areaId) {
    echo "✅ Test area created with ID: {$areaId}<br>";
    echo "Area Code: {$testAreaData['area_code']}<br>";
} else {
    echo "❌ Failed to create test area<br>";
    exit;
}

echo "<h2>Step 2: Create Test Staff (All Roles)</h2>";

// Create staff for all roles
$roles = ['ADMIN', 'FOREMAN', 'MECHANIC', 'HELPER'];
$staffIds = [];

foreach ($roles as $role) {
    $staffData = [
        'staff_code' => 'TST-' . $role . '-' . date('His'),
        'staff_name' => 'Test ' . ucfirst(strtolower($role)),
        'role' => $role,
        'phone' => '081234567' . substr(microtime(), -3),
        'email' => strtolower($role) . '_test@example.com',
        'is_active' => 1
    ];
    
    $staffId = $staffModel->insert($staffData);
    if ($staffId) {
        $staffIds[$role] = $staffId;
        echo "✅ {$role} staff created - ID: {$staffId}, Code: {$staffData['staff_code']}<br>";
    } else {
        echo "❌ Failed to create {$role} staff<br>";
    }
}

echo "<h2>Step 3: Create PRIMARY Assignments</h2>";

// Create PRIMARY assignments for all roles
foreach ($roles as $role) {
    if (isset($staffIds[$role])) {
        $assignmentData = [
            'area_id' => $areaId,
            'staff_id' => $staffIds[$role],
            'assignment_type' => 'PRIMARY',
            'start_date' => date('Y-m-d'),
            'end_date' => null,
            'is_active' => 1,
            'notes' => 'Test PRIMARY assignment for ' . $role
        ];
        
        $assignmentId = $assignmentModel->insert($assignmentData);
        if ($assignmentId) {
            echo "✅ PRIMARY assignment created for {$role} - Assignment ID: {$assignmentId}<br>";
        } else {
            echo "❌ Failed to create assignment for {$role}<br>";
        }
    }
}

echo "<h2>Step 4: Create Test Customer in Area</h2>";

// Create test customer in the area
$customerData = [
    'customer_code' => 'TESTCUST-' . date('His'),
    'customer_name' => 'Test Customer for Integration',
    'area_id' => $areaId,
    'address' => 'Test Address',
    'phone' => '081234567890',
    'email' => 'testcustomer@example.com',
    'is_active' => 1
];

$customerId = $customerModel->insert($customerData);
if ($customerId) {
    echo "✅ Test customer created - ID: {$customerId}, Code: {$customerData['customer_code']}<br>";
} else {
    echo "❌ Failed to create test customer<br>";
    exit;
}

echo "<h2>Step 5: Get Available Unit for Work Order</h2>";

// Get any available unit from the customer
$db = \Config\Database::connect();
$unit = $db->table('inventory_unit')
           ->select('id_inventory_unit, no_unit, customer_id')
           ->where('customer_id', $customerId)
           ->limit(1)
           ->get()
           ->getRowArray();

if (!$unit) {
    // Create a test unit if none exists
    echo "No existing unit found, creating test unit...<br>";
    $unitData = [
        'no_unit' => 'TEST-UNIT-' . date('His'),
        'customer_id' => $customerId,
        'tipe_unit' => 'ELECTRIC',
        'status' => 'ACTIVE',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $unitId = $db->table('inventory_unit')->insert($unitData);
    if ($unitId) {
        $unit = [
            'id_inventory_unit' => $db->insertID(),
            'no_unit' => $unitData['no_unit'],
            'customer_id' => $customerId
        ];
        echo "✅ Test unit created - ID: {$unit['id_inventory_unit']}, No Unit: {$unit['no_unit']}<br>";
    } else {
        echo "❌ Failed to create test unit<br>";
        exit;
    }
} else {
    echo "✅ Found existing unit - ID: {$unit['id_inventory_unit']}, No Unit: {$unit['no_unit']}<br>";
}

echo "<h2>Step 6: Test Auto Staff Assignment</h2>";

// Test the autoAssignStaff method
$workOrderController = new WorkOrderController();

// Use reflection to access private method for testing
$reflector = new ReflectionClass($workOrderController);
$method = $reflector->getMethod('autoAssignStaff');
$method->setAccessible(true);

try {
    $assignedStaff = $method->invoke($workOrderController, $unit['id_inventory_unit']);
    
    echo "<h3>Auto Assignment Results:</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>Role</th><th>Assigned Staff ID</th><th>Staff Name</th></tr>";
    
    $rolesLower = ['admin', 'foreman', 'mechanic', 'helper'];
    foreach ($rolesLower as $role) {
        $staffIdField = $role . '_staff_id';
        $assignedId = $assignedStaff[$staffIdField] ?? null;
        $assignedName = $assignedStaff['assigned_staff_names'][$role] ?? 'None';
        
        $status = $assignedId ? "✅" : "❌";
        echo "<tr>";
        echo "<td>{$status} " . strtoupper($role) . "</td>";
        echo "<td>{$assignedId}</td>";
        echo "<td>{$assignedName}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verify assignments match our created staff
    $allAssigned = true;
    foreach ($rolesLower as $role) {
        $staffIdField = $role . '_staff_id';
        $expectedId = $staffIds[strtoupper($role)] ?? null;
        $actualId = $assignedStaff[$staffIdField] ?? null;
        
        if ($expectedId && $actualId == $expectedId) {
            echo "✅ {$role} assignment correct: Expected {$expectedId}, Got {$actualId}<br>";
        } else {
            echo "❌ {$role} assignment mismatch: Expected {$expectedId}, Got {$actualId}<br>";
            $allAssigned = false;
        }
    }
    
    if ($allAssigned) {
        echo "<h2>🎉 INTEGRATION TEST PASSED!</h2>";
        echo "<p style='color: green; font-weight: bold;'>All staff were correctly auto-assigned based on area assignments.</p>";
    } else {
        echo "<h2>⚠️ INTEGRATION TEST ISSUES</h2>";
        echo "<p style='color: orange; font-weight: bold;'>Some staff assignments didn't match expectations.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ INTEGRATION TEST FAILED</h2>";
    echo "<p style='color: red;'>Error during auto assignment: " . $e->getMessage() . "</p>";
}

echo "<h2>Step 7: Cleanup (Optional)</h2>";
echo "<p>Test data created:</p>";
echo "<ul>";
echo "<li>Area ID: {$areaId}</li>";
echo "<li>Customer ID: {$customerId}</li>";
echo "<li>Unit ID: {$unit['id_inventory_unit']}</li>";
foreach ($staffIds as $role => $id) {
    echo "<li>{$role} Staff ID: {$id}</li>";
}
echo "</ul>";
echo "<p><em>You can manually delete these test records from the database if needed.</em></p>";

echo "<hr>";
echo "<p>Integration test completed at: " . date('Y-m-d H:i:s') . "</p>";
?>