<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
echo "=== DATABASE STATUS ===\n";

$r = $pdo->query("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema='optima_ci' AND table_type='BASE TABLE'");
echo "Tables: " . $r->fetch()['c'] . "\n";

$r = $pdo->query("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema='optima_ci' AND table_type='VIEW'");
echo "Views: " . $r->fetch()['c'] . "\n";

$r = $pdo->query("SELECT COUNT(*) c FROM information_schema.triggers WHERE trigger_schema='optima_ci'");
echo "Triggers: " . $r->fetch()['c'] . "\n";

$r = $pdo->query("SHOW COLUMNS FROM inventory_unit WHERE Field IN ('kontrak_id','customer_id','customer_location_id')");
echo "Redundant cols in inventory_unit: " . count($r->fetchAll()) . "\n";

$r = $pdo->query("SELECT COUNT(*) c FROM inventory_unit");
echo "Units: " . $r->fetch()['c'] . "\n";

$r = $pdo->query("SELECT COUNT(*) c FROM kontrak");
echo "Contracts: " . $r->fetch()['c'] . "\n";

$r = $pdo->query("SELECT COUNT(*) c FROM kontrak_unit WHERE status='ACTIVE'");
echo "Active kontrak_unit: " . $r->fetch()['c'] . "\n";

$r = $pdo->query("SELECT COUNT(*) c FROM vw_unit_with_contracts");
echo "VIEW rows: " . $r->fetch()['c'] . "\n";

// Check backup tables
$r = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='optima_ci' AND table_name LIKE '%backup%'");
$backups = $r->fetchAll(PDO::FETCH_COLUMN);
echo "Backup tables: " . (count($backups) ? implode(', ', $backups) : 'NONE (clean)') . "\n";

// Check FK constraints on kontrak
echo "\n=== FK CONSTRAINTS CHECK ===\n";
$r = $pdo->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA='optima_ci' AND TABLE_NAME='kontrak' AND REFERENCED_TABLE_NAME IS NOT NULL");
foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $fk) {
    echo "  kontrak." . str_pad($fk['COLUMN_NAME'], 30) . " -> " . $fk['REFERENCED_TABLE_NAME'] . " ({$fk['CONSTRAINT_NAME']})\n";
}

$r = $pdo->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA='optima_ci' AND TABLE_NAME='spk' AND REFERENCED_TABLE_NAME IS NOT NULL");
foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $fk) {
    echo "  spk." . str_pad($fk['COLUMN_NAME'], 30) . " -> " . $fk['REFERENCED_TABLE_NAME'] . " ({$fk['CONSTRAINT_NAME']})\n";
}

// Check triggers
echo "\n=== TRIGGERS ===\n";
$r = $pdo->query("SHOW TRIGGERS");
$triggers = $r->fetchAll(PDO::FETCH_ASSOC);
foreach ($triggers as $t) {
    echo "  " . str_pad($t['Trigger'], 40) . " on " . $t['Table'] . " (" . $t['Timing'] . " " . $t['Event'] . ")\n";
}
if (empty($triggers)) echo "  NONE\n";

// Check for duplicate FK constraints across all tables
echo "\n=== DUPLICATE FK CHECK ===\n";
$r = $pdo->query("
    SELECT TABLE_NAME, COLUMN_NAME, COUNT(*) as cnt, GROUP_CONCAT(CONSTRAINT_NAME) as fks
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA='optima_ci' AND REFERENCED_TABLE_NAME IS NOT NULL
    GROUP BY TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
    HAVING COUNT(*) > 1
");
$dupes = $r->fetchAll(PDO::FETCH_ASSOC);
echo "Duplicate FKs: " . count($dupes) . (count($dupes) === 0 ? " (CLEAN!)" : "") . "\n";
foreach ($dupes as $d) {
    echo "  {$d['TABLE_NAME']}.{$d['COLUMN_NAME']} has {$d['cnt']} FKs: {$d['fks']}\n";
}

echo "\n=== STATUS ENUM CHECK ===\n";
$r = $pdo->query("SELECT status, COUNT(*) c FROM kontrak GROUP BY status ORDER BY status");
foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "  kontrak.status = '{$row['status']}' ({$row['c']} rows)\n";
}
$r = $pdo->query("SELECT status, COUNT(*) c FROM kontrak_unit GROUP BY status ORDER BY status");
foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "  kontrak_unit.status = '{$row['status']}' ({$row['c']} rows)\n";
}
