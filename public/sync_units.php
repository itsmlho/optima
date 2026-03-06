<?php
// One-time sync script - DELETE AFTER USE
$conn = new mysqli('127.0.0.1', 'root', '', 'optima_ci');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

echo "<pre>";

// 1. Sync total_units ke semua kontrak berdasarkan data aktual dari kontrak_unit
$sql = "
    UPDATE kontrak k
    SET k.total_units = (
        SELECT COUNT(*) FROM kontrak_unit ku
        WHERE ku.kontrak_id = k.id
        AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
        AND COALESCE(ku.is_temporary, 0) = 0
    )
";
$conn->query($sql);
$rowsAffected = $conn->affected_rows;
echo "✅ Updated total_units for {$rowsAffected} kontrak rows.\n\n";

// 2. Sync nilai_total ke semua kontrak berdasarkan data aktual dari kontrak_unit
$sql2 = "
    UPDATE kontrak k
    SET k.nilai_total = (
        SELECT COALESCE(SUM(iu.harga_sewa_bulanan), 0)
        FROM kontrak_unit ku
        JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
        WHERE ku.kontrak_id = k.id
        AND ku.status IN ('ACTIVE','TEMP_ACTIVE')
        AND COALESCE(ku.is_temporary, 0) = 0
    )
    WHERE k.nilai_total = 0 OR k.nilai_total IS NULL
";
$conn->query($sql2);
$rowsAffected2 = $conn->affected_rows;
echo "✅ Updated nilai_total for {$rowsAffected2} kontrak rows (where it was 0 or NULL).\n\n";

// 3. Verifikasi hasil
$r = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN total_units > 0 THEN 1 ELSE 0 END) as with_units FROM kontrak");
$row = $r->fetch_assoc();
echo "=== HASIL SETELAH SYNC ===\n";
echo "Total kontrak              : {$row['total']}\n";
echo "Kontrak dengan total_units > 0: {$row['with_units']}\n";
echo "Kontrak masih 0 (no units) : " . ($row['total'] - $row['with_units']) . "\n\n";

// 4. Sample data setelah sync
$r2 = $conn->query("SELECT k.no_kontrak, k.total_units, COUNT(ku.id) as actual FROM kontrak k LEFT JOIN kontrak_unit ku ON ku.kontrak_id = k.id AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND COALESCE(ku.is_temporary,0)=0 GROUP BY k.id HAVING actual > 0 LIMIT 10");
echo "=== SAMPLE KONTRAK DENGAN UNIT ===\n";
echo sprintf("%-40s %-15s %-10s\n", "no_kontrak", "total_units", "actual");
echo str_repeat("-", 70) . "\n";
while($row2 = $r2->fetch_assoc()) {
    echo sprintf("%-40s %-15s %-10s\n", substr($row2['no_kontrak'],0,38), $row2['total_units'], $row2['actual']);
}

echo "\n✅ SELESAI. Hapus file ini setelah selesai!\n";
echo "</pre>";
$conn->close();
