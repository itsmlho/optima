<?php

$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "=== COMPREHENSIVE BUSINESS FLOW AUDIT ===" . PHP_EOL;

// 1. CUSTOMERS TABLE
echo PHP_EOL . "1. CUSTOMERS TABLE:" . PHP_EOL;
$result = $mysqli->query('DESCRIBE customers');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . PHP_EOL;
    }
}

// 2. CUSTOMER_LOCATIONS TABLE  
echo PHP_EOL . "2. CUSTOMER_LOCATIONS TABLE:" . PHP_EOL;
$result = $mysqli->query('DESCRIBE customer_locations');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . PHP_EOL;
    }
}

// 3. QUOTATIONS TABLE (already exists!)
echo PHP_EOL . "3. QUOTATIONS TABLE:" . PHP_EOL;
$result = $mysqli->query('DESCRIBE quotations');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . PHP_EOL;
    }
}

// 4. QUOTATION_SPECIFICATIONS TABLE (already exists!)
echo PHP_EOL . "4. QUOTATION_SPECIFICATIONS TABLE:" . PHP_EOL;
$result = $mysqli->query('DESCRIBE quotation_specifications');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . PHP_EOL;
    }
}

// 5. KONTRAK TABLE (current system)
echo PHP_EOL . "5. KONTRAK TABLE:" . PHP_EOL;
$result = $mysqli->query('DESCRIBE kontrak');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . PHP_EOL;
    }
}

// 6. KONTRAK_SPESIFIKASI TABLE (to be migrated)
echo PHP_EOL . "6. KONTRAK_SPESIFIKASI TABLE:" . PHP_EOL;
$result = $mysqli->query('DESCRIBE kontrak_spesifikasi');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . PHP_EOL;
    }
}

// 7. SPK TABLE 
echo PHP_EOL . "7. SPK TABLE:" . PHP_EOL;
$result = $mysqli->query('DESCRIBE spk');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . PHP_EOL;
    }
}

// BUSINESS FLOW ANALYSIS
echo PHP_EOL . "=== CURRENT BUSINESS FLOW ANALYSIS ===" . PHP_EOL;

// Count records in each table
$tables = ['customers', 'customer_locations', 'quotations', 'quotation_specifications', 'kontrak', 'kontrak_spesifikasi', 'spk'];

foreach($tables as $table) {
    $result = $mysqli->query("SELECT COUNT(*) as total FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Total $table records: " . $row['total'] . PHP_EOL;
    }
}

// Check if quotation_specifications is already populated
echo PHP_EOL . "=== QUOTATION_SPECIFICATIONS SAMPLE DATA ===" . PHP_EOL;
$result = $mysqli->query('SELECT * FROM quotation_specifications LIMIT 3');
if ($result && $result->num_rows > 0) {
    echo "Quotation specifications already have data:" . PHP_EOL;
    while($row = $result->fetch_assoc()) {
        echo "  ID: " . $row['id'] . " | Quotation ID: " . (isset($row['quotation_id']) ? $row['quotation_id'] : 'N/A') . PHP_EOL;
    }
} else {
    echo "Quotation specifications table is empty - ready for migration" . PHP_EOL;
}

// Check current kontrak_spesifikasi data structure
echo PHP_EOL . "=== KONTRAK_SPESIFIKASI SAMPLE DATA ===" . PHP_EOL;
$result = $mysqli->query('SELECT * FROM kontrak_spesifikasi LIMIT 3');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  ID: " . $row['id'] . " | Kontrak ID: " . $row['kontrak_id'];
        if (isset($row['spek_kode'])) echo " | Spek Kode: " . $row['spek_kode'];
        if (isset($row['jumlah_dibutuhkan'])) echo " | Jumlah: " . $row['jumlah_dibutuhkan'];
        echo PHP_EOL;
    }
}

echo PHP_EOL . "=== FOREIGN KEY RELATIONSHIPS ===" . PHP_EOL;
$result = $mysqli->query("
    SELECT DISTINCT 
        TABLE_NAME, 
        COLUMN_NAME, 
        REFERENCED_TABLE_NAME, 
        REFERENCED_COLUMN_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE REFERENCED_TABLE_SCHEMA = 'optima_ci' 
    AND TABLE_NAME IN ('kontrak_spesifikasi', 'kontrak', 'customers', 'customer_locations', 'quotations', 'quotation_specifications', 'spk')
    ORDER BY TABLE_NAME
");

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['TABLE_NAME'] . "." . $row['COLUMN_NAME'] . " -> " . $row['REFERENCED_TABLE_NAME'] . "." . $row['REFERENCED_COLUMN_NAME'] . PHP_EOL;
    }
}

$mysqli->close();

?>