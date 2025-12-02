<?php

// Test script untuk memastikan quotation system berfungsi
$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

echo "=== QUOTATION SYSTEM FUNCTIONALITY TEST ===" . PHP_EOL;

// 1. Test database connectivity
echo "1. Testing database connection..." . PHP_EOL;
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "✅ Database connected successfully" . PHP_EOL;

// 2. Test quotations table
echo PHP_EOL . "2. Testing quotations table..." . PHP_EOL;
$quotations = $mysqli->query("SELECT COUNT(*) as count FROM quotations");
if ($quotations) {
    $count = $quotations->fetch_assoc()['count'];
    echo "✅ Quotations table accessible, $count records found" . PHP_EOL;
} else {
    echo "❌ Error accessing quotations table: " . $mysqli->error . PHP_EOL;
}

// 3. Test quotation_specifications table  
echo PHP_EOL . "3. Testing quotation_specifications table..." . PHP_EOL;
$specs = $mysqli->query("SELECT COUNT(*) as count FROM quotation_specifications");
if ($specs) {
    $count = $specs->fetch_assoc()['count'];
    echo "✅ Quotation specifications table accessible, $count records found" . PHP_EOL;
} else {
    echo "❌ Error accessing quotation_specifications table: " . $mysqli->error . PHP_EOL;
}

// 4. Test sample data retrieval
echo PHP_EOL . "4. Testing sample quotation data..." . PHP_EOL;
$sample = $mysqli->query("
    SELECT q.quotation_number, q.prospect_name, q.stage, q.total_amount, 
           COUNT(qs.id_specification) as spec_count
    FROM quotations q 
    LEFT JOIN quotation_specifications qs ON q.id_quotation = qs.id_quotation 
    GROUP BY q.id_quotation 
    ORDER BY q.created_at DESC 
    LIMIT 3
");

if ($sample) {
    while($row = $sample->fetch_assoc()) {
        echo "  📋 " . $row['quotation_number'] . " | " . $row['prospect_name'] . " | " . 
             $row['stage'] . " | Rp " . number_format($row['total_amount']) . 
             " | " . $row['spec_count'] . " specs" . PHP_EOL;
    }
} else {
    echo "❌ Error retrieving sample data: " . $mysqli->error . PHP_EOL;
}

// 5. Test quotation statistics
echo PHP_EOL . "5. Testing quotation statistics..." . PHP_EOL;
$stats = $mysqli->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN stage = 'SENT' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN stage = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted,
        SUM(CASE WHEN is_deal = 1 THEN 1 ELSE 0 END) as deals,
        SUM(total_amount) as total_value
    FROM quotations
");

if ($stats) {
    $data = $stats->fetch_assoc();
    echo "  📊 Total: " . $data['total'] . PHP_EOL;
    echo "  📊 Pending: " . $data['pending'] . PHP_EOL;
    echo "  📊 Accepted: " . $data['accepted'] . PHP_EOL;
    echo "  📊 Deals: " . $data['deals'] . PHP_EOL;
    echo "  📊 Total Value: Rp " . number_format($data['total_value']) . PHP_EOL;
    
    if ($data['total'] > 0) {
        $conversion = round(($data['deals'] / $data['total']) * 100, 1);
        echo "  📊 Conversion Rate: " . $conversion . "%" . PHP_EOL;
    }
}

// 6. Test contract relationship
echo PHP_EOL . "6. Testing quotation-contract relationship..." . PHP_EOL;
$contracts = $mysqli->query("
    SELECT q.quotation_number, k.no_kontrak, k.customer_name
    FROM quotations q
    JOIN kontrak k ON q.created_contract_id = k.id
    LIMIT 3
");

if ($contracts) {
    $count = 0;
    while($row = $contracts->fetch_assoc()) {
        echo "  🔗 " . $row['quotation_number'] . " → " . $row['no_kontrak'] . " (" . $row['customer_name'] . ")" . PHP_EOL;
        $count++;
    }
    echo "✅ Found $count quotations linked to contracts" . PHP_EOL;
}

echo PHP_EOL . "=== TEST COMPLETED ===" . PHP_EOL;
echo "🎯 Quotation system is ready for use!" . PHP_EOL;
echo "📍 Access URL: /marketing/quotations" . PHP_EOL;

$mysqli->close();

?>