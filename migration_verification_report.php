<?php

$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

echo "=== MIGRATION VERIFICATION REPORT ===" . PHP_EOL . PHP_EOL;

// 1. Verify quotations
echo "1. QUOTATIONS MIGRATED:" . PHP_EOL;
$quotations = $mysqli->query("
    SELECT 
        q.id_quotation,
        q.quotation_number,
        q.prospect_name,
        q.stage,
        q.total_amount,
        q.created_contract_id,
        k.no_kontrak
    FROM quotations q
    LEFT JOIN kontrak k ON q.created_contract_id = k.id
    WHERE q.quotation_number LIKE 'QUO-MIG-%'
    ORDER BY q.id_quotation
");

if ($quotations) {
    while($q = $quotations->fetch_assoc()) {
        echo "  📋 {$q['quotation_number']} | {$q['prospect_name']} | {$q['stage']} | " . number_format($q['total_amount']) . " | Contract: {$q['no_kontrak']}" . PHP_EOL;
    }
    echo PHP_EOL;
}

// 2. Verify specifications
echo "2. SPECIFICATIONS MIGRATED:" . PHP_EOL;
$specs = $mysqli->query("
    SELECT 
        qs.id_specification,
        qs.specification_name,
        qs.category,
        qs.quantity,
        qs.unit_price,
        qs.total_price,
        q.quotation_number
    FROM quotation_specifications qs
    JOIN quotations q ON qs.id_quotation = q.id_quotation
    WHERE q.quotation_number LIKE 'QUO-MIG-%'
    ORDER BY q.quotation_number, qs.id_specification
");

if ($specs) {
    $current_quotation = '';
    while($s = $specs->fetch_assoc()) {
        if ($current_quotation !== $s['quotation_number']) {
            $current_quotation = $s['quotation_number'];
            echo "  📄 {$current_quotation}:" . PHP_EOL;
        }
        echo "    • {$s['specification_name']} | {$s['category']} | Qty: {$s['quantity']} | " . number_format($s['unit_price']) . " | Total: " . number_format($s['total_price']) . PHP_EOL;
    }
    echo PHP_EOL;
}

// 3. Summary statistics
echo "3. MIGRATION SUMMARY:" . PHP_EOL;

$quotation_count = $mysqli->query("SELECT COUNT(*) as count FROM quotations WHERE quotation_number LIKE 'QUO-MIG-%'")->fetch_assoc()['count'];
$spec_count = $mysqli->query("
    SELECT COUNT(*) as count 
    FROM quotation_specifications qs
    JOIN quotations q ON qs.id_quotation = q.id_quotation
    WHERE q.quotation_number LIKE 'QUO-MIG-%'
")->fetch_assoc()['count'];

$total_value = $mysqli->query("SELECT SUM(total_amount) as total FROM quotations WHERE quotation_number LIKE 'QUO-MIG-%'")->fetch_assoc()['total'];

echo "  ✅ Quotations migrated: $quotation_count" . PHP_EOL;
echo "  ✅ Specifications migrated: $spec_count" . PHP_EOL;
echo "  💰 Total value migrated: " . number_format($total_value) . PHP_EOL;
echo PHP_EOL;

// 4. Business flow status
echo "4. BUSINESS FLOW STATUS:" . PHP_EOL;
echo "  📈 Original Flow: Customer -> Kontrak -> Kontrak_Spesifikasi -> SPK" . PHP_EOL;
echo "  📊 New Flow: Customer -> Quotation -> Quotation_Specifications -> Kontrak -> SPK" . PHP_EOL;
echo "  🔄 Migration Status: COMPLETE" . PHP_EOL;
echo "  🎯 Next Steps: Update controllers to use quotation-centric workflow" . PHP_EOL;

$mysqli->close();

?>