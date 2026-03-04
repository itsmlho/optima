<?php
/**
 * Cleanup ALL duplicate FK constraints in optima_ci
 * Each table+column+referenced_table should have exactly 1 FK
 * When duplicates exist, keep the FIRST one (alphabetically) and drop the rest
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) { echo "Connection failed: {$db->connect_error}\n"; exit(1); }

echo "=== Finding ALL duplicate FK constraints ===\n\n";

// Find all groups of duplicate FKs
$r = $db->query("
SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME,
       GROUP_CONCAT(CONSTRAINT_NAME ORDER BY CONSTRAINT_NAME) as constraint_names,
       COUNT(*) as fk_count
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'optima_ci' 
  AND REFERENCED_TABLE_NAME IS NOT NULL
GROUP BY TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
HAVING COUNT(*) > 1
ORDER BY TABLE_NAME, COLUMN_NAME
");

$dropStatements = [];
$totalDuplicates = 0;

while ($row = $r->fetch_assoc()) {
    $constraints = explode(',', $row['constraint_names']);
    $keep = $constraints[0]; // Keep the first one alphabetically
    $drop = array_slice($constraints, 1); // Drop the rest
    
    echo "Table: {$row['TABLE_NAME']}.{$row['COLUMN_NAME']} -> {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}\n";
    echo "  KEEP: {$keep}\n";
    foreach ($drop as $fk) {
        echo "  DROP: {$fk}\n";
        $dropStatements[] = [
            'table' => $row['TABLE_NAME'],
            'fk' => $fk,
            'desc' => "{$row['TABLE_NAME']}.{$row['COLUMN_NAME']} -> {$row['REFERENCED_TABLE_NAME']}"
        ];
        $totalDuplicates++;
    }
    echo "\n";
}

echo "Total duplicate FKs to drop: {$totalDuplicates}\n\n";

if ($totalDuplicates === 0) {
    echo "Nothing to do!\n";
    $db->close();
    exit(0);
}

// Execute drops
echo "=== Executing drops ===\n\n";
$success = 0;
$failed = 0;

foreach ($dropStatements as $stmt) {
    $sql = "ALTER TABLE `{$stmt['table']}` DROP FOREIGN KEY `{$stmt['fk']}`";
    try {
        $db->query($sql);
        echo "  OK: Dropped {$stmt['fk']} from {$stmt['table']}\n";
        $success++;
    } catch (Exception $e) {
        echo "  FAIL: {$stmt['fk']} - {$e->getMessage()}\n";
        $failed++;
    }
}

echo "\n=== Results ===\n";
echo "  Success: {$success}\n";
echo "  Failed: {$failed}\n";

// Verify
echo "\n=== Verification: remaining duplicates ===\n";
$r = $db->query("
SELECT TABLE_NAME, COLUMN_NAME, COUNT(*) as fk_count
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'optima_ci' AND REFERENCED_TABLE_NAME IS NOT NULL 
GROUP BY TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
HAVING COUNT(*) > 1
");
$remaining = 0;
while ($row = $r->fetch_assoc()) { 
    echo "  STILL DUPLICATE: {$row['TABLE_NAME']}.{$row['COLUMN_NAME']} ({$row['fk_count']})\n"; 
    $remaining++; 
}
if ($remaining === 0) echo "  ALL CLEAN - zero duplicates remaining\n";

$db->close();
echo "\n=== DONE ===\n";
