<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if referenced tables exist
$tables = ['status_attachment', 'work_order_sparepart_usage', 'inventory_attachment', 'baterai', 'charger', 'attachment'];
echo "=== TABLE EXISTENCE ===\n";
foreach ($tables as $t) {
    $r = $pdo->query("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema='optima_ci' AND table_name='$t'");
    echo "  $t: " . ($r->fetch()['c'] ? 'EXISTS' : 'MISSING') . "\n";
}

// Check inventory_attachment columns
echo "\n=== inventory_attachment COLUMNS ===\n";
$r = $pdo->query("SHOW COLUMNS FROM inventory_attachment");
foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "  " . str_pad($col['Field'], 30) . $col['Type'] . "\n";
}

// Check charger table PK
echo "\n=== charger PK ===\n";
$r = $pdo->query("SHOW COLUMNS FROM charger WHERE `Key` = 'PRI'");
foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "  PK: " . $col['Field'] . "\n";
}

// Check status_unit PK
echo "\n=== status_unit PK ===\n"; 
$r = $pdo->query("SHOW COLUMNS FROM status_unit WHERE `Key` = 'PRI'");
foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "  PK: " . $col['Field'] . "\n";
}
