<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$backupTables = [
    '_backup_invalid_dates_kontrak_unit',
    '_backup_kontrak_unit_before_sync', 
    '_final_backup_inventory_attachment_20260303',
];

echo "=== Dropping backup tables ===\n";
foreach ($backupTables as $t) {
    try {
        $pdo->exec("DROP TABLE IF EXISTS `$t`");
        echo "  DROPPED: $t\n";
    } catch (PDOException $e) {
        echo "  FAIL: $t => {$e->getMessage()}\n";
    }
}

// Verify
$r = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='optima_ci' AND table_name LIKE '%backup%'");
$remaining = $r->fetchAll(PDO::FETCH_COLUMN);
echo "\nRemaining backup tables: " . (count($remaining) ? implode(', ', $remaining) : 'NONE (CLEAN!)') . "\n";

$r = $pdo->query("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema='optima_ci' AND table_type='BASE TABLE'");
echo "Total tables: " . $r->fetch()['c'] . "\n";
