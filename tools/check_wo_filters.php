<?php
$db = new mysqli('127.0.0.1', 'root', '', 'optima_ci');
echo "=== DEPARTEMEN ===\n";
$r = $db->query('SELECT id_departemen, nama_departemen FROM departemen ORDER BY id_departemen');
while ($row = $r->fetch_row()) echo $row[0] . ': ' . $row[1] . "\n";

echo "\n=== AREAS ===\n";
$r = $db->query('SELECT id, area_code, area_name FROM areas ORDER BY id LIMIT 20');
while ($row = $r->fetch_row()) echo $row[0] . ': ' . $row[1] . ' - ' . $row[2] . "\n";

echo "\n=== work_orders columns with departemen ===\n";
$r = $db->query("SELECT iu.departemen_id, d.nama_departemen, COUNT(*) cnt
    FROM work_orders wo 
    LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = wo.unit_id
    LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
    GROUP BY iu.departemen_id, d.nama_departemen ORDER BY cnt DESC");
while ($row = $r->fetch_assoc()) echo ($row['departemen_id'] ?? 'NULL') . ': ' . ($row['nama_departemen'] ?? 'NULL') . ' => ' . $row['cnt'] . "\n";
