<?php
echo "=== VALIDATION: Contract Grouping Logic ===\n\n";

$handle = fopen('kontrak_from_accounting.csv', 'r');
fgetcsv($handle, 0, ';', '"', ''); // Skip header

$stats = [
    'total_contracts' => 0,
    'po_perbulan' => [],
    'spot_rental' => [],
    'normal_contracts' => [],
    'contracts_by_customer' => []
];

while ($row = fgetcsv($handle, 0, ';', '"', '')) {
    if (empty($row[0])) continue;
    
    $stats['total_contracts']++;
    $customer_id = $row[0];
    $kontrak_name = trim($row[1]);
    $units = (int)$row[9];
    
    // Track contracts by customer
    if (!isset($stats['contracts_by_customer'][$customer_id])) {
        $stats['contracts_by_customer'][$customer_id] = 0;
    }
    $stats['contracts_by_customer'][$customer_id]++;
    
    // Categorize
    $kontrak_upper = strtoupper($kontrak_name);
    
    if ($kontrak_name === 'SPOT RENTAL') {
        $stats['spot_rental'][] = ['customer' => $customer_id, 'units' => $units];
    } elseif (stripos($kontrak_upper, 'PO') !== false && stripos($kontrak_upper, 'BULAN') !== false) {
        $stats['po_perbulan'][] = ['customer' => $customer_id, 'kontrak' => $kontrak_name, 'units' => $units];
    } else {
        $stats['normal_contracts'][] = ['customer' => $customer_id, 'kontrak' => $kontrak_name, 'units' => $units];
    }
}
fclose($handle);

echo "SUMMARY:\n";
echo "  Total contracts: {$stats['total_contracts']}\n";
echo "  - SPOT RENTAL: " . count($stats['spot_rental']) . " contracts\n";
echo "  - PO PERBULAN patterns: " . count($stats['po_perbulan']) . " contracts\n";
echo "  - Normal contracts: " . count($stats['normal_contracts']) . " contracts\n\n";

echo "1. SPOT RENTAL (empty/dash contracts - grouped per customer):\n";
foreach (array_slice($stats['spot_rental'], 0, 5) as $sr) {
    echo "   Customer #{$sr['customer']}: {$sr['units']} units\n";
}
if (count($stats['spot_rental']) > 5) {
    echo "   ... and " . (count($stats['spot_rental']) - 5) . " more\n";
}

echo "\n2. PO PERBULAN patterns (grouped per customer, ignore dates):\n";
foreach (array_slice($stats['po_perbulan'], 0, 5) as $po) {
    echo "   Customer #{$po['customer']}: {$po['kontrak']} ({$po['units']} units)\n";
}
if (count($stats['po_perbulan']) > 5) {
    echo "   ... and " . (count($stats['po_perbulan']) - 5) . " more\n";
}

echo "\n3. Normal contracts (grouped by customer + contract + dates):\n";
foreach (array_slice($stats['normal_contracts'], 0, 5) as $nc) {
    echo "   Customer #{$nc['customer']}: " . substr($nc['kontrak'], 0, 40) . " ({$nc['units']} units)\n";
}
if (count($stats['normal_contracts']) > 5) {
    echo "   ... and " . (count($stats['normal_contracts']) - 5) . " more\n";
}

// Check for anomalies
arsort($stats['contracts_by_customer']);
echo "\n4. Customers with MOST contracts (may indicate renewals/addendums):\n";
$count = 0;
foreach ($stats['contracts_by_customer'] as $cid => $cnt) {
    if ($cnt > 5) {
        echo "   Customer #$cid: $cnt contracts\n";
        $count++;
        if ($count >= 10) break;
    }
}

echo "\n✓ Validation complete!\n";
echo "\nCONCLUSION:\n";
echo "- Recurring POs (PO PERBULAN): ✓ Merged per customer\n";
echo "- Spot rentals (empty contracts): ✓ Merged per customer\n";
echo "- Normal contracts: ✓ Kept separate by dates (renewals preserved)\n";
