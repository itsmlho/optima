<?php
$db = new mysqli('127.0.0.1', 'root', '', 'optima_ci');

// Step 1: Add REKONDISI to ENUM (keep FABRIKASI for now)
$r = $db->query("ALTER TABLE work_orders MODIFY order_type ENUM('COMPLAINT','PMPS','FABRIKASI','REKONDISI','PERSIAPAN') NOT NULL");
echo "Step 1 (add REKONDISI to ENUM): " . ($db->error ?: 'OK') . PHP_EOL;

// Step 2: Update existing data
$r = $db->query("UPDATE work_orders SET order_type='REKONDISI' WHERE order_type='FABRIKASI'");
echo "Step 2 (update data): " . ($db->error ?: $db->affected_rows . ' rows updated') . PHP_EOL;

// Step 3: Remove FABRIKASI from ENUM
$r = $db->query("ALTER TABLE work_orders MODIFY order_type ENUM('COMPLAINT','PMPS','REKONDISI','PERSIAPAN') NOT NULL");
echo "Step 3 (remove FABRIKASI from ENUM): " . ($db->error ?: 'OK') . PHP_EOL;

// Verify
$r = $db->query("SHOW COLUMNS FROM work_orders WHERE Field='order_type'");
echo "Final column type: " . $r->fetch_row()[1] . PHP_EOL;
$r = $db->query("SELECT order_type, COUNT(*) cnt FROM work_orders GROUP BY order_type");
echo "Data count by type:\n";
while ($row = $r->fetch_row()) echo "  {$row[0]}: {$row[1]}\n";
