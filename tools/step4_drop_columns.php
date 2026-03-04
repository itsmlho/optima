<?php
/**
 * Step 4: Drop redundant columns and clean up indexes on inventory_unit
 * 
 * Drops: kontrak_id, customer_id, customer_location_id
 * These are now handled by kontrak_unit junction table + vw_unit_with_contracts VIEW
 * 
 * Also cleans up redundant/duplicate indexes
 */

$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$success = 0;
$fail = 0;

function run($pdo, $sql, &$success, &$fail) {
    try {
        $pdo->exec($sql);
        echo "  OK: $sql\n";
        $success++;
    } catch (PDOException $e) {
        // Index/FK doesn't exist - that's fine
        if (strpos($e->getMessage(), '1091') !== false || strpos($e->getMessage(), '1553') !== false) {
            echo "  SKIP (not found): $sql\n";
        } else {
            echo "  FAIL: $sql => {$e->getMessage()}\n";
            $fail++;
        }
    }
}

// ============================================
// PHASE 1: Drop FK constraints for redundant columns
// ============================================
echo "=== PHASE 1: Drop FK constraints ===\n";
$fksToDropForColumns = [
    'fk_inventory_unit_kontrak',           // kontrak_id -> kontrak.id
    'fk_inventory_unit_customer',          // customer_id -> customers.id
    'fk_inventory_unit_customer_location', // customer_location_id -> customer_locations.id
];
foreach ($fksToDropForColumns as $fk) {
    run($pdo, "ALTER TABLE inventory_unit DROP FOREIGN KEY $fk", $success, $fail);
}

// ============================================
// PHASE 2: Drop indexes that reference redundant columns
// ============================================
echo "\n=== PHASE 2: Drop indexes on redundant columns ===\n";
$indexesToDrop = [
    // Single-column indexes on redundant columns
    'idx_customer_id',                    // (customer_id)
    'idx_customer_location_id',           // (customer_location_id)
    'idx_inventory_unit_kontrak',         // (kontrak_id) - was FK index
    
    // Composite indexes that include redundant columns
    'idx_inventory_customer_location',    // (customer_id, customer_location_id, area_id)
    'idx_inventory_kontrak_detail',       // (kontrak_id, status_unit_id)
    'idx_inventory_unit_customer',        // (customer_id, status_unit_id)
];
foreach ($indexesToDrop as $idx) {
    run($pdo, "ALTER TABLE inventory_unit DROP INDEX $idx", $success, $fail);
}

// ============================================
// PHASE 3: Drop the 3 redundant columns
// ============================================
echo "\n=== PHASE 3: Drop redundant columns ===\n";
$columnsToDrop = ['kontrak_id', 'customer_id', 'customer_location_id'];
foreach ($columnsToDrop as $col) {
    run($pdo, "ALTER TABLE inventory_unit DROP COLUMN $col", $success, $fail);
}

// ============================================
// PHASE 4: Clean up other redundant/duplicate indexes
// ============================================
echo "\n=== PHASE 4: Clean redundant/duplicate indexes ===\n";

// idx_unit_workflow_status (workflow_status) - duplicate of idx_inventory_unit_workflow
// idx_inventory_unit_workflow (workflow_status) - covered by idx_inventory_workflow (workflow_status, di_workflow_id)
// Keep: idx_inventory_workflow (composite, most useful)
run($pdo, "ALTER TABLE inventory_unit DROP INDEX idx_unit_workflow_status", $success, $fail);
run($pdo, "ALTER TABLE inventory_unit DROP INDEX idx_inventory_unit_workflow", $success, $fail);

// idx_inventory_unit_created (created_at) - covered by idx_inventory_dates (created_at, updated_at)
run($pdo, "ALTER TABLE inventory_unit DROP INDEX idx_inventory_unit_created", $success, $fail);

// idx_no_unit_na_pattern (no_unit_na) - duplicate of idx_no_unit_na (no_unit_na)
run($pdo, "ALTER TABLE inventory_unit DROP INDEX idx_no_unit_na_pattern", $success, $fail);

// ============================================
// VERIFY
// ============================================
echo "\n=== VERIFICATION ===\n";

// Check columns
$stmt = $pdo->query("SHOW COLUMNS FROM inventory_unit WHERE Field IN ('kontrak_id','customer_id','customer_location_id')");
$remaining = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Remaining redundant columns: " . count($remaining) . (count($remaining) === 0 ? " (CLEAN!)" : " (PROBLEM!)") . "\n";

// Count total indexes
$stmt = $pdo->query("SHOW INDEX FROM inventory_unit");
$indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$grouped = [];
foreach ($indexes as $idx) {
    $grouped[$idx['Key_name']][] = $idx['Column_name'];
}
echo "Total indexes remaining: " . count($grouped) . "\n";
foreach ($grouped as $name => $cols) {
    echo "  " . str_pad($name, 42) . " => (" . implode(', ', $cols) . ")\n";
}

// Count total columns
$stmt = $pdo->query("SHOW COLUMNS FROM inventory_unit");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nTotal columns remaining: " . count($cols) . "\n";

// Check FKs
$stmt = $pdo->query("
    SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'inventory_unit' AND REFERENCED_TABLE_NAME IS NOT NULL
");
$fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total FK constraints remaining: " . count($fks) . "\n";
foreach ($fks as $fk) {
    echo "  " . str_pad($fk['CONSTRAINT_NAME'], 42) . " => {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}\n";
}

echo "\n=== SUMMARY ===\n";
echo "Success: $success | Failures: $fail\n";
echo $fail === 0 ? "ALL CLEAN!\n" : "SOME FAILURES - check above!\n";
