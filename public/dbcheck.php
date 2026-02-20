<?php
// Debug: Cek data kontrak_unit untuk unit dengan no_unit tertentu
// HAPUS file ini setelah selesai debug!
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=optima_ci', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Cari id_inventory_unit berdasarkan no_unit
$no_unit = $_GET['no_unit'] ?? '5122';
$stmt = $pdo->prepare('SELECT id_inventory_unit, no_unit, serial_number, status FROM inventory_unit WHERE no_unit = ? LIMIT 3');
$stmt->execute([$no_unit]);
$unit = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Unit: $no_unit</h2><pre>";
print_r($unit);
echo "</pre>";

if (!$unit) { die("Unit tidak ditemukan"); }

$uid = $unit[0]['id_inventory_unit'];
echo "<h2>id_inventory_unit = $uid</h2>";

// 2. Cek kontrak_unit untuk unit ini
$stmt2 = $pdo->prepare('
    SELECT ku.id, ku.unit_id, ku.kontrak_id, ku.status, ku.tanggal_mulai, ku.tanggal_selesai,
           k.no_kontrak, k.tanggal_mulai as k_tgl_mulai
    FROM kontrak_unit ku
    JOIN kontrak k ON k.id = ku.kontrak_id
    WHERE ku.unit_id = ?
    ORDER BY ku.tanggal_mulai ASC
');
$stmt2->execute([$uid]);
$rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>kontrak_unit records (" . count($rows) . " rows)</h2><pre>";
print_r($rows);
echo "</pre>";

// 3. Cek system_activity_log untuk unit ini
$stmt3 = $pdo->prepare('
    SELECT id, action_type, action_description, created_at, username
    FROM system_activity_log
    WHERE entity_type = ? AND entity_id = ?
    ORDER BY created_at ASC
    LIMIT 20
');
$stmt3->execute(['inventory_unit', $uid]);
$logs = $stmt3->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>system_activity_log (" . count($logs) . " rows)</h2><pre>";
print_r($logs);
echo "</pre>";
