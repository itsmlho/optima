<?php
/**
 * List all indexes on inventory_unit table
 */
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$stmt = $pdo->query("SHOW INDEX FROM inventory_unit");
$indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$grouped = [];
foreach ($indexes as $idx) {
    $name = $idx['Key_name'];
    $grouped[$name][] = $idx['Column_name'];
}
echo "=== INDEXES ON inventory_unit ===\n";
foreach ($grouped as $name => $cols) {
    echo str_pad($name, 45) . ' => (' . implode(', ', $cols) . ")\n";
}
echo "\nTotal: " . count($grouped) . " indexes\n";

// Check FK constraints
$stmt = $pdo->query("
    SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = 'optima_ci' 
    AND TABLE_NAME = 'inventory_unit'
    AND REFERENCED_TABLE_NAME IS NOT NULL
");
$fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\n=== FK CONSTRAINTS on inventory_unit ===\n";
foreach ($fks as $fk) {
    echo str_pad($fk['CONSTRAINT_NAME'], 45) . " => {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}\n";
}
echo "\nTotal FKs: " . count($fks) . "\n";
