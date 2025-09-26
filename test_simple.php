<?php
// Simple test for auto staff assignment
define('APPPATH', __DIR__ . '/app/');
define('ROOTPATH', __DIR__ . '/');
define('WRITEPATH', __DIR__ . '/writable/');

require_once APPPATH . '../system/Test/bootstrap.php';

$config = new \Config\Database();
$db = \Config\Database::connect();

echo "Testing Auto Staff Assignment System\n";
echo "===================================\n\n";

// 1. Test Database Views
echo "1. Testing Database Views:\n";
echo "-------------------------\n";

// Test vw_area_staff_summary
echo "Area Staff Summary:\n";
try {
    $areaStaff = $db->query("SELECT * FROM vw_area_staff_summary ORDER BY area_code, role LIMIT 10")->getResultArray();
    if (empty($areaStaff)) {
        echo "No data found in vw_area_staff_summary\n";
    } else {
        foreach ($areaStaff as $staff) {
            echo sprintf("- Area %s (%s): %s - %s\n", 
                $staff['area_code'], $staff['area_name'], 
                $staff['staff_name'], $staff['role']
            );
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nUnit Complete Info (first 5):\n";
try {
    $units = $db->query("SELECT * FROM vw_unit_complete_info LIMIT 5")->getResultArray();
    if (empty($units)) {
        echo "No data found in vw_unit_complete_info\n";
    } else {
        foreach ($units as $unit) {
            echo sprintf("- Unit %s: %s in %s (%s)\n",
                $unit['no_unit'] ?? 'N/A', 
                $unit['customer_name'] ?? $unit['pelanggan'] ?? 'Unknown',
                $unit['area_name'] ?? 'No Area', 
                $unit['area_code'] ?? 'N/A'
            );
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// 2. Test GetAreaStaffByRole function
echo "\n2. Testing GetAreaStaffByRole Function:\n";
echo "--------------------------------------\n";

try {
    // Get a unit with area
    $testUnit = $db->query("SELECT * FROM vw_unit_complete_info WHERE area_id IS NOT NULL LIMIT 1")->getRowArray();
    if ($testUnit) {
        echo "Testing with Unit: " . $testUnit['no_unit'] . " in Area: " . $testUnit['area_name'] . "\n";
        
        $roles = ['ADMIN', 'FOREMAN', 'MECHANIC', 'HELPER'];
        foreach ($roles as $role) {
            $result = $db->query("SELECT GetAreaStaffByRole(?, ?) as staff_id", [$testUnit['area_id'], $role])->getRowArray();
            if ($result && $result['staff_id']) {
                $staff = $db->query("SELECT staff_name, role FROM staff WHERE id = ?", [$result['staff_id']])->getRowArray();
                if ($staff) {
                    echo "- {$role}: {$staff['staff_name']}\n";
                } else {
                    echo "- {$role}: Staff ID {$result['staff_id']} not found\n";
                }
            } else {
                echo "- {$role}: No staff available\n";
            }
        }
    } else {
        echo "No test unit found with area assigned\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// 3. Test Tables Structure
echo "\n3. Testing Tables Structure:\n";
echo "----------------------------\n";

$tables = ['areas', 'customers', 'customer_locations', 'staff', 'area_staff_assignments', 'customer_contracts'];

foreach ($tables as $table) {
    try {
        $count = $db->query("SELECT COUNT(*) as count FROM {$table}")->getRowArray();
        echo "- {$table}: {$count['count']} records\n";
    } catch (Exception $e) {
        echo "- {$table}: Error - " . $e->getMessage() . "\n";
    }
}

// 4. Test Area-Staff Relationships
echo "\n4. Testing Area-Staff Relationships:\n";
echo "------------------------------------\n";

try {
    $areaStaffQuery = "
        SELECT a.area_name, s.staff_name, s.role, asa.assignment_type
        FROM areas a
        JOIN area_staff_assignments asa ON a.id = asa.area_id
        JOIN staff s ON asa.staff_id = s.id
        WHERE asa.is_active = 1
        ORDER BY a.area_name, s.role
        LIMIT 10
    ";
    
    $areaStaff = $db->query($areaStaffQuery)->getResultArray();
    if (empty($areaStaff)) {
        echo "No active area staff assignments found\n";
    } else {
        foreach ($areaStaff as $assignment) {
            echo sprintf("- %s: %s (%s) - %s\n",
                $assignment['area_name'],
                $assignment['staff_name'],
                $assignment['role'],
                $assignment['assignment_type']
            );
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// 5. Test Customer-Unit-Area Relationships
echo "\n5. Testing Customer-Unit-Area Relationships:\n";
echo "--------------------------------------------\n";

try {
    $customerUnitQuery = "
        SELECT c.customer_name, iu.no_unit, a.area_name
        FROM customers c
        JOIN customer_contracts cc ON c.id = cc.customer_id
        JOIN kontrak k ON cc.kontrak_id = k.id
        JOIN inventory_unit iu ON k.id = iu.kontrak_id
        JOIN areas a ON c.area_id = a.id
        WHERE iu.no_unit IS NOT NULL
        LIMIT 10
    ";
    
    $customerUnits = $db->query($customerUnitQuery)->getResultArray();
    if (empty($customerUnits)) {
        echo "No customer-unit-area relationships found\n";
    } else {
        foreach ($customerUnits as $relation) {
            echo sprintf("- Customer: %s, Unit: %s, Area: %s\n",
                $relation['customer_name'],
                $relation['no_unit'],
                $relation['area_name']
            );
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nAuto Staff Assignment System Test Completed!\n";
?>