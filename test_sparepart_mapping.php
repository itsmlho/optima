<?php
// Simple test script to check sparepart mapping logic
$workOrderId = 46;

// Simulate database connection
$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== TESTING SPAREPART MAPPING FOR WO #$workOrderId ===\n\n";

// Get spareparts
$sparepartsQuery = "SELECT id, sparepart_code as code, sparepart_name as name, 
                    quantity_brought as qty, satuan, notes 
                    FROM work_order_spareparts 
                    WHERE work_order_id = $workOrderId 
                    ORDER BY id ASC";
$sparepartsResult = $mysqli->query($sparepartsQuery);
$spareparts = [];
while ($row = $sparepartsResult->fetch_assoc()) {
    $spareparts[] = $row;
}

echo "1. SPAREPARTS FROM work_order_spareparts:\n";
foreach ($spareparts as $sp) {
    echo "   ID: {$sp['id']} | Name: {$sp['name']}\n";
}

// Get usage data
$usageQuery = "SELECT id, work_order_sparepart_id, work_order_id, 
               quantity_used, quantity_returned, usage_notes 
               FROM work_order_sparepart_usage 
               WHERE work_order_id = $workOrderId";
$usageResult = $mysqli->query($usageQuery);
$usageData = [];
while ($row = $usageResult->fetch_assoc()) {
    $usageData[] = $row;
}

echo "\n2. USAGE DATA FROM work_order_sparepart_usage:\n";
foreach ($usageData as $usage) {
    echo "   ID: {$usage['id']} | WO_Sparepart_ID: {$usage['work_order_sparepart_id']} | Used: {$usage['quantity_used']} | Returned: {$usage['quantity_returned']}\n";
}

// Map usage to spareparts
$usageMap = [];
foreach ($usageData as $usage) {
    $usageMap[$usage['work_order_sparepart_id']] = $usage;
}

echo "\n3. MAPPING RESULT:\n";
foreach ($spareparts as &$sparepart) {
    echo "   Checking Sparepart ID: {$sparepart['id']} Name: {$sparepart['name']}\n";
    
    if (isset($usageMap[$sparepart['id']])) {
        $sparepart['is_used'] = 1;
        $sparepart['used_quantity'] = $usageMap[$sparepart['id']]['quantity_used'];
        $sparepart['usage_notes'] = $usageMap[$sparepart['id']]['usage_notes'];
        
        $qtyUsed = (int)$usageMap[$sparepart['id']]['quantity_used'];
        $qtyReturned = (int)$usageMap[$sparepart['id']]['quantity_returned'];
        
        if ($qtyUsed > 0 && $qtyReturned > 0) {
            $sparepart['is_used'] = 0; // Returned
            echo "      ✓ MATCH! Status: RETURNED (used: $qtyUsed, returned: $qtyReturned)\n";
        } else if ($qtyUsed > 0) {
            $sparepart['is_used'] = 1; // Used
            echo "      ✓ MATCH! Status: USED (used: $qtyUsed)\n";
        }
    } else {
        echo "      ✗ NO MATCH - Status: PENDING\n";
    }
}

echo "\n4. FINAL SPAREPARTS DATA:\n";
echo json_encode($spareparts, JSON_PRETTY_PRINT);

$mysqli->close();
?>
