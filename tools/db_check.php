<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) { echo 'Connection failed: ' . $db->connect_error; exit(1); }

echo "=== 1. VIEW vw_unit_with_contracts ===\n";
$r = $db->query("SHOW CREATE VIEW vw_unit_with_contracts");
echo ($r && $r->fetch_assoc()) ? "  EXISTS - OK\n" : "  MISSING!\n";

echo "\n=== 2. kontrak_unit table ===\n";
$r = $db->query("SELECT COUNT(*) as cnt FROM kontrak_unit");
$row = $r->fetch_assoc();
echo "  Rows: {$row['cnt']}\n";

echo "\n=== 3. kontrak_unit status distribution ===\n";
$r = $db->query("SELECT status, COUNT(*) as cnt FROM kontrak_unit GROUP BY status ORDER BY cnt DESC");
while ($row = $r->fetch_assoc()) echo "  {$row['status']}: {$row['cnt']}\n";

echo "\n=== 4. FK constraints on kontrak_unit ===\n";
$r = $db->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA='optima_ci' AND TABLE_NAME='kontrak_unit' AND REFERENCED_TABLE_NAME IS NOT NULL");
while ($row = $r->fetch_assoc()) echo "  {$row['CONSTRAINT_NAME']}: {$row['COLUMN_NAME']} -> {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}\n";

echo "\n=== 5. Redundant columns on inventory_unit ===\n";
$r = $db->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='optima_ci' AND TABLE_NAME='inventory_unit' AND COLUMN_NAME IN ('kontrak_id','customer_id','customer_location_id')");
while ($row = $r->fetch_assoc()) echo "  {$row['COLUMN_NAME']} (still exists - expected, Step 4 deferred)\n";

echo "\n=== 6. Data consistency: kontrak_unit vs inventory_unit.kontrak_id ===\n";
$r = $db->query("
SELECT 
  (SELECT COUNT(*) FROM inventory_unit WHERE kontrak_id IS NOT NULL AND kontrak_id > 0) as iu_with_kontrak,
  (SELECT COUNT(*) FROM kontrak_unit WHERE status = 'ACTIVE') as ku_active,
  (SELECT COUNT(*) FROM inventory_unit iu 
   WHERE iu.kontrak_id IS NOT NULL AND iu.kontrak_id > 0 
   AND NOT EXISTS (SELECT 1 FROM kontrak_unit ku WHERE ku.unit_id = iu.id_inventory_unit AND ku.kontrak_id = iu.kontrak_id)
  ) as mismatches
");
$row = $r->fetch_assoc();
echo "  inventory_unit with kontrak_id: {$row['iu_with_kontrak']}\n";
echo "  kontrak_unit ACTIVE rows: {$row['ku_active']}\n";
echo "  Mismatches (iu has kontrak but no ku row): {$row['mismatches']}\n";

echo "\n=== 7. kontrak.status enum values in use ===\n";
$r = $db->query("SELECT status, COUNT(*) as cnt FROM kontrak GROUP BY status ORDER BY cnt DESC");
while ($row = $r->fetch_assoc()) echo "  {$row['status']}: {$row['cnt']}\n";

echo "\n=== 8. kontrak_unit.status enum values in use ===\n";
$r = $db->query("SELECT status, COUNT(*) as cnt FROM kontrak_unit GROUP BY status ORDER BY cnt DESC");
while ($row = $r->fetch_assoc()) echo "  {$row['status']}: {$row['cnt']}\n";

echo "\n=== 9. Duplicate FK check ===\n";
$r = $db->query("
SELECT TABLE_NAME, COLUMN_NAME, COUNT(*) as fk_count 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA='optima_ci' AND REFERENCED_TABLE_NAME IS NOT NULL 
GROUP BY TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
HAVING COUNT(*) > 1
");
$found = false;
while ($row = $r->fetch_assoc()) { echo "  DUPLICATE: {$row['TABLE_NAME']}.{$row['COLUMN_NAME']} ({$row['fk_count']} FKs)\n"; $found = true; }
if (!$found) echo "  None - all clean\n";

echo "\n=== 10. Index idx_kontrak_unit_active ===\n";
$r = $db->query("SHOW INDEX FROM kontrak_unit WHERE Key_name = 'idx_kontrak_unit_active'");
echo ($r && $r->num_rows > 0) ? "  EXISTS - OK\n" : "  MISSING!\n";

$db->close();
echo "\n=== DATABASE CHECK COMPLETE ===\n";
