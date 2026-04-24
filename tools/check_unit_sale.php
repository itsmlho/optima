<?php
$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) { echo 'Connect failed: ' . $db->connect_error; exit; }

$r = $db->query('SELECT COUNT(*) AS cnt FROM inventory_unit');
echo 'inventory_unit rows: ' . $r->fetch_assoc()['cnt'] . PHP_EOL;

$r = $db->query('SELECT COUNT(*) AS cnt FROM inventory_unit WHERE status_unit_id != 13');
echo 'non-sold units: ' . $r->fetch_assoc()['cnt'] . PHP_EOL;

$r = $db->query('DESCRIBE inventory_unit');
$cols = [];
while ($row = $r->fetch_assoc()) $cols[] = $row['Field'];
echo 'inventory_unit columns: ' . implode(', ', $cols) . PHP_EOL;

// Check model_unit join
$r = $db->query('DESCRIBE model_unit');
$cols = [];
while ($row = $r->fetch_assoc()) $cols[] = $row['Field'];
echo 'model_unit columns: ' . implode(', ', $cols) . PHP_EOL;

// Try the actual query
$r = $db->query("SELECT iu.id_inventory_unit AS id, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit, su.status_unit
    FROM inventory_unit iu
    LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
    LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
    WHERE iu.status_unit_id != 13
    LIMIT 5");
if (!$r) { echo 'Query error: ' . $db->error . PHP_EOL; }
else {
    while ($row = $r->fetch_assoc()) echo json_encode($row) . PHP_EOL;
}
