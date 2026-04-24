<?php
$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) { die('Connect failed: ' . $db->connect_error . "\n"); }

// Check if 'units' table exists
$r = $db->query("SHOW TABLES LIKE 'units'");
echo 'units table exists: ' . ($r->num_rows > 0 ? 'YES' : 'NO') . PHP_EOL;

// Check status_unit table 
$r = $db->query('SELECT id_status, status_unit FROM status_unit LIMIT 20');
echo 'status_unit values:' . PHP_EOL;
while ($row = $r->fetch_assoc()) echo '  ' . json_encode($row) . PHP_EOL;

// Simulate exact controller query
$r = $db->query("
    SELECT 
        iu.id_inventory_unit AS id,
        iu.no_unit,
        iu.no_unit_na,
        iu.serial_number,
        mu.merk_unit,
        mu.model_unit,
        su.status_unit AS status_unit_name,
        iu.status_unit_id
    FROM inventory_unit iu
    LEFT JOIN model_unit mu  ON mu.id_model_unit = iu.model_unit_id
    LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
    WHERE iu.status_unit_id != 13
    ORDER BY iu.no_unit
    LIMIT 5
");
if (!$r) { echo 'Query error: ' . $db->error . PHP_EOL; }
else {
    echo PHP_EOL . 'Sample results (as controller would return):' . PHP_EOL;
    while ($row = $r->fetch_assoc()) {
        $noUnit = $row['no_unit'] ?: $row['no_unit_na'] ?: 'UNIT-' . $row['id'];
        $desc   = trim(($row['merk_unit'] ?? '') . ' ' . ($row['model_unit'] ?? ''));
        $sn     = $row['serial_number'] ? ' | SN: ' . $row['serial_number'] : '';
        echo json_encode([
            'id'   => $row['id'],
            'text' => $noUnit . ($desc ? ' — ' . $desc : '') . $sn,
        ]) . PHP_EOL;
    }
}
