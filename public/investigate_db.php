<?php
/**
 * Deep Database Investigation untuk inventory_unit table
 * Cek triggers, views, generated columns yang mungkin inject 'status_unit'
 */

// Hardcode database config (adjust if needed)
$dbConfig = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'optima_ci',  // Sesuaikan dengan nama database Anda
];

// Connect to MySQL
$mysqli = new mysqli(
    $dbConfig['hostname'],
    $dbConfig['username'],
    $dbConfig['password'],
    $dbConfig['database']
);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "<pre>";
echo "=== DEEP DATABASE INVESTIGATION ===\n\n";

// 1. Check table structure
echo "1. TABLE STRUCTURE (inventory_unit):\n";
echo str_repeat("-", 80) . "\n";
$result = $mysqli->query("SHOW FULL COLUMNS FROM inventory_unit");
while ($field = $result->fetch_assoc()) {
    echo sprintf("%-30s %-20s %-10s\n", 
        $field['Field'], 
        $field['Type'], 
        $field['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
    );
}

// 2. Check for triggers
echo "\n\n2. TRIGGERS on inventory_unit:\n";
echo str_repeat("-", 80) . "\n";
$result = $mysqli->query("SHOW TRIGGERS WHERE `Table` = 'inventory_unit'");
if ($result->num_rows === 0) {
    echo "✅ No triggers found\n";
} else {
    while ($trigger = $result->fetch_assoc()) {
        echo "\nTrigger: {$trigger['Trigger']}\n";
        echo "Event: {$trigger['Event']} {$trigger['Timing']}\n";
        echo "Statement:\n{$trigger['Statement']}\n";
        echo str_repeat("-", 80) . "\n";
    }
}

// 3. Check for views that might reference this table
echo "\n\n3. VIEWS referencing inventory_unit:\n";
echo str_repeat("-", 80) . "\n";
$dbName = $dbConfig['database'];
$result = $mysqli->query("
    SELECT TABLE_NAME, VIEW_DEFINITION 
    FROM INFORMATION_SCHEMA.VIEWS 
    WHERE TABLE_SCHEMA = '$dbName' 
    AND VIEW_DEFINITION LIKE '%inventory_unit%'
");

if ($result->num_rows === 0) {
    echo "✅ No views found\n";
} else {
    while ($view = $result->fetch_assoc()) {
        echo "\nView: {$view['TABLE_NAME']}\n";
        echo "Definition:\n" . substr($view['VIEW_DEFINITION'], 0, 500) . "...\n";
        echo str_repeat("-", 80) . "\n";
    }
}

// 4. Check for generated/computed columns
echo "\n\n4. GENERATED/COMPUTED COLUMNS:\n";
echo str_repeat("-", 80) . "\n";
$result = $mysqli->query("
    SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, GENERATION_EXPRESSION
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = '$dbName' 
    AND TABLE_NAME = 'inventory_unit'
    AND EXTRA LIKE '%GENERATED%'
");

if ($result->num_rows === 0) {
    echo "✅ No generated columns found\n";
} else {
    while ($col = $result->fetch_assoc()) {
        echo "\nColumn: {$col['COLUMN_NAME']}\n";
        echo "Expression: {$col['GENERATION_EXPRESSION']}\n";
        echo str_repeat("-", 80) . "\n";
    }
}

// 5. Check for column 'status_unit' existence
echo "\n\n5. CHECKING IF 'status_unit' COLUMN EXISTS:\n";
echo str_repeat("-", 80) . "\n";
$result = $mysqli->query("SHOW COLUMNS FROM inventory_unit LIKE 'status_unit'");
if ($result->num_rows > 0) {
    $col = $result->fetch_assoc();
    echo "⚠️  FOUND 'status_unit' column!\n";
    echo "Type: {$col['Type']}\n";
    echo "Nullable: {$col['Null']}\n";
    echo "Default: {$col['Default']}\n";
} else {
    echo "✅ Column 'status_unit' does NOT exist (correct)\n";
}

// 6. Check actual table creation statement
echo "\n\n6. TABLE CREATION STATEMENT:\n";
echo str_repeat("-", 80) . "\n";
$result = $mysqli->query("SHOW CREATE TABLE inventory_unit");
$row = $result->fetch_assoc();
echo $row['Create Table'] . "\n";

$mysqli->close();
echo "\n\n=== END OF INVESTIGATION ===\n";
echo "</pre>";
