<?php
require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = new \CodeIgniter\CLI\CLI();
$app = \Config\Services::codeigniter();
$app->initialize();

echo "Testing Auto Staff Assignment System\n";
echo "===================================\n\n";

// 1. Test Database Views
echo "1. Testing Database Views:\n";
echo "-------------------------\n";

$db = \Config\Database::connect();

// Test vw_area_staff_summary
echo "Area Staff Summary:\n";
$areaStaff = $db->query("SELECT * FROM vw_area_staff_summary ORDER BY area_code, role")->getResultArray();
foreach ($areaStaff as $staff) {
    echo sprintf("- Area %s (%s): %s %s - %s staff\n", 
        $staff['area_code'], $staff['area_name'], 
        $staff['staff_name'], $staff['role'], 
        $staff['staff_count']
    );
}

echo "\nUnit Complete Info (first 5):\n";
$units = $db->query("SELECT * FROM vw_unit_complete_info LIMIT 5")->getResultArray();
foreach ($units as $unit) {
    echo sprintf("- Unit %s: %s in %s (%s)\n",
        $unit['no_unit'], $unit['customer_name'] ?? $unit['pelanggan'],
        $unit['area_name'] ?? 'No Area', $unit['area_code'] ?? 'N/A'
    );
}

// 2. Test GetAreaStaffByRole function
echo "\n2. Testing GetAreaStaffByRole Function:\n";
echo "--------------------------------------\n";

// Get a unit with area
$testUnit = $db->query("SELECT * FROM vw_unit_complete_info WHERE area_id IS NOT NULL LIMIT 1")->getRowArray();
if ($testUnit) {
    echo "Testing with Unit: " . $testUnit['no_unit'] . " in Area: " . $testUnit['area_name'] . "\n";
    
    $roles = ['ADMIN', 'FOREMAN', 'MECHANIC', 'HELPER'];
    foreach ($roles as $role) {
        $result = $db->query("SELECT GetAreaStaffByRole(?, ?) as staff_id", [$testUnit['area_id'], $role])->getRowArray();
        if ($result['staff_id']) {
            $staff = $db->query("SELECT staff_name, role FROM staff WHERE id = ?", [$result['staff_id']])->getRowArray();
            echo "- {$role}: {$staff['staff_name']}\n";
        } else {
            echo "- {$role}: No staff available\n";
        }
    }
} else {
    echo "No test unit found with area assigned\n";
}

// 3. Test Auto Staff Assignment Logic
echo "\n3. Testing Auto Staff Assignment Logic:\n";
echo "---------------------------------------\n";

if ($testUnit) {
    // Load necessary models
    $workOrderModel = new \App\Models\WorkOrderModel();
    $workOrderStatusModel = new \App\Models\WorkOrderStatusModel();
    $workOrderPriorityModel = new \App\Models\WorkOrderPriorityModel();
    $workOrderCategoryModel = new \App\Models\WorkOrderCategoryModel();
    
    // Get required data for work order creation
    $status = $workOrderStatusModel->where('status_code', 'OPEN')->first();
    $priority = $workOrderPriorityModel->where('priority_code', 'MEDIUM')->first();
    $category = $workOrderCategoryModel->first();
    
    if ($status && $priority && $category) {
        // Create test work order data
        $workOrderData = [
            'work_order_number' => 'TEST-' . date('Ymd-His'),
            'report_date' => date('Y-m-d H:i:s'),
            'unit_id' => $testUnit['unit_id'],
            'order_type' => 'COMPLAINT',
            'priority_id' => $priority['id'],
            'category_id' => $category['id'],
            'complaint_description' => 'Test auto staff assignment system',
            'status_id' => $status['id'],
            'created_by' => 1
        ];
        
        echo "Creating test work order with data:\n";
        echo "- Unit: " . $testUnit['no_unit'] . "\n";
        echo "- Area: " . $testUnit['area_name'] . "\n";
        echo "- Customer: " . ($testUnit['customer_name'] ?? $testUnit['pelanggan']) . "\n";
        
        // Simulate auto staff assignment
        $areaId = $testUnit['area_id'];
        $assignedStaff = [];
        
        $roles = [
            'admin_staff_id' => 'ADMIN',
            'foreman_staff_id' => 'FOREMAN', 
            'mechanic_staff_id' => 'MECHANIC',
            'helper_staff_id' => 'HELPER'
        ];
        
        foreach ($roles as $field => $role) {
            // Try PRIMARY staff first
            $result = $db->query("SELECT GetAreaStaffByRole(?, ?) as staff_id", [$areaId, $role])->getRowArray();
            if ($result['staff_id']) {
                $workOrderData[$field] = $result['staff_id'];
                $staff = $db->query("SELECT staff_name FROM staff WHERE id = ?", [$result['staff_id']])->getRowArray();
                $assignedStaff[$role] = $staff['staff_name'];
            }
        }
        
        echo "\nAuto-assigned staff:\n";
        foreach ($assignedStaff as $role => $name) {
            echo "- {$role}: {$name}\n";
        }
        
        // Actually create the work order (commented out to prevent actual creation)
        echo "\n[NOTE: Work order creation is simulated - not actually inserted to prevent test data]\n";
        
        /*
        try {
            $workOrderId = $workOrderModel->insert($workOrderData);
            if ($workOrderId) {
                echo "\nWork Order created successfully with ID: {$workOrderId}\n";
                
                // Verify the created work order
                $createdWO = $workOrderModel->find($workOrderId);
                echo "Created Work Order Number: " . $createdWO['work_order_number'] . "\n";
                
                // Clean up test data
                $workOrderModel->delete($workOrderId);
                echo "Test work order cleaned up\n";
            }
        } catch (Exception $e) {
            echo "Error creating work order: " . $e->getMessage() . "\n";
        }
        */
        
    } else {
        echo "Required master data not found (status, priority, or category)\n";
    }
} else {
    echo "No test unit available for testing\n";
}

echo "\n4. Testing Area Integration:\n";
echo "----------------------------\n";

// Test areas data
$areaModel = new \App\Models\AreaModel();
$areas = $areaModel->findAll();
echo "Available Areas: " . count($areas) . "\n";
foreach ($areas as $area) {
    echo "- {$area['area_code']}: {$area['area_name']}\n";
}

// Test staff assignments by area
$staffModel = new \App\Models\StaffModel();
echo "\nStaff Distribution by Area:\n";
foreach ($areas as $area) {
    $areaStaff = $staffModel->getStaffByArea($area['id']);
    echo "- {$area['area_name']}: " . count($areaStaff) . " staff\n";
    foreach ($areaStaff as $staff) {
        echo "  * {$staff['staff_name']} ({$staff['role']})\n";
    }
}

echo "\nAuto Staff Assignment System Test Completed!\n";
?>