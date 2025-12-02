<?php

$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "=== RUNNING MIGRATION: KONTRAK_SPESIFIKASI -> QUOTATION_SPECIFICATIONS ===" . PHP_EOL;

// Read and execute migration SQL
$migrationSQL = file_get_contents('migration_quotation_system.sql');

// Split SQL into individual statements
$statements = preg_split('/;\s*$/m', $migrationSQL);

$successCount = 0;
$errorCount = 0;

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (empty($statement) || strpos($statement, '--') === 0 || strpos($statement, 'USE') === 0) {
        continue;
    }
    
    echo "\nExecuting: " . substr($statement, 0, 100) . "..." . PHP_EOL;
    
    if ($mysqli->query($statement)) {
        echo "✅ SUCCESS" . PHP_EOL;
        $successCount++;
    } else {
        echo "❌ ERROR: " . $mysqli->error . PHP_EOL;
        $errorCount++;
        
        // Continue with non-critical errors
        if (strpos($mysqli->error, 'already exists') !== false || 
            strpos($mysqli->error, 'Duplicate column') !== false) {
            echo "   (Non-critical error, continuing...)" . PHP_EOL;
        }
    }
}

echo PHP_EOL . "=== MIGRATION SUMMARY ===" . PHP_EOL;
echo "Successful statements: $successCount" . PHP_EOL;
echo "Errors encountered: $errorCount" . PHP_EOL;

// Check migration results
echo PHP_EOL . "=== POST-MIGRATION VERIFICATION ===" . PHP_EOL;

// Check quotations created
$result = $mysqli->query("SELECT COUNT(*) as count FROM quotations WHERE quotation_number LIKE 'QUO-MIG-%'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Quotations created from kontrak: " . $row['count'] . PHP_EOL;
}

// Check specifications migrated  
$result = $mysqli->query("SELECT COUNT(*) as count FROM quotation_specifications WHERE original_kontrak_spek_id IS NOT NULL");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Specifications migrated: " . $row['count'] . PHP_EOL;
}

// Show sample migrated data
echo PHP_EOL . "Sample migrated quotations:" . PHP_EOL;
$result = $mysqli->query("
    SELECT 
        q.quotation_number,
        q.prospect_name,
        q.stage,
        q.total_amount,
        COUNT(qs.id_specification) as spec_count
    FROM quotations q
    LEFT JOIN quotation_specifications qs ON q.id_quotation = qs.id_quotation  
    WHERE q.quotation_number LIKE 'QUO-MIG-%'
    GROUP BY q.id_quotation
    LIMIT 5
");

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['quotation_number'] . " | " . $row['prospect_name'] . " | " . $row['stage'] . " | Rp " . number_format($row['total_amount']) . " | " . $row['spec_count'] . " specs" . PHP_EOL;
    }
}

$mysqli->close();

?>