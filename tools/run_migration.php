<?php
/**
 * Migration Script: Add customer_location_id to kontrak_unit
 * Run: php tools/run_migration.php
 */

// Simple database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'optima_ci';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database: $dbname\n\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

echo "=== Migration: Add customer_location_id to kontrak_unit ===\n\n";

// Check if column exists
$stmt = $pdo->query("
    SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = '$dbname'
    AND TABLE_NAME = 'kontrak_unit'
    AND COLUMN_NAME = 'customer_location_id'
");
$columnExists = $stmt->fetch(PDO::FETCH_ASSOC);

if ($columnExists) {
    echo "✓ Column 'customer_location_id' already exists!\n";
} else {
    echo "Adding column 'customer_location_id'...\n";
    try {
        $pdo->exec('ALTER TABLE kontrak_unit ADD COLUMN customer_location_id INT UNSIGNED NULL COMMENT \'Lokasi/titik penempatan unit dalam kontrak\'');
        echo "✓ Column added!\n";
    } catch (Exception $e) {
        echo "✗ Error adding column: " . $e->getMessage() . "\n";
    }
}

// Check if FK exists
$stmt = $pdo->query("
    SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = '$dbname'
    AND TABLE_NAME = 'kontrak_unit'
    AND CONSTRAINT_NAME = 'fk_kontrak_unit_location'
");
$fkExists = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fkExists) {
    echo "Adding foreign key constraint...\n";
    try {
        $pdo->exec('ALTER TABLE kontrak_unit ADD CONSTRAINT fk_kontrak_unit_location FOREIGN KEY (customer_location_id) REFERENCES customer_locations(id) ON DELETE SET NULL ON UPDATE CASCADE');
        echo "✓ FK added!\n";
    } catch (Exception $e) {
        echo "✗ Error adding FK: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ FK already exists!\n";
}

// Check if index exists
$stmt = $pdo->query("
    SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = '$dbname'
    AND TABLE_NAME = 'kontrak_unit'
    AND INDEX_NAME = 'idx_kontrak_unit_location'
");
$indexExists = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$indexExists) {
    echo "Adding index...\n";
    try {
        $pdo->exec('CREATE INDEX idx_kontrak_unit_location ON kontrak_unit(customer_location_id)');
        echo "✓ Index added!\n";
    } catch (Exception $e) {
        echo "✗ Error adding index: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ Index already exists!\n";
}

// Verify
echo "\n=== Verification ===\n";
$stmt = $pdo->query("DESCRIBE kontrak_unit");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
$hasColumn = false;
foreach ($columns as $col) {
    if ($col['Field'] === 'customer_location_id') {
        $hasColumn = true;
        echo "✓ Column 'customer_location_id' found in kontrak_unit!\n";
        echo "  Type: {$col['Type']}\n";
        echo "  Null: {$col['Null']}\n";
        echo "  Comment: {$col['Comment']}\n";
    }
}

if (!$hasColumn) {
    echo "✗ FAILED: Column not found!\n";
    exit(1);
}

echo "\n=== Migration Complete ===\n";
