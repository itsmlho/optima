<?php
/**
 * Script untuk menghasilkan file master_import_from_zero.sql
 * Termasuk TRUNCATE tabel kontrak dan kontrak_unit agar data tere-set (dari 0)
 */

$csvFile = 'C:/laragon/www/optima/databases/Input_Data/master_import_ready.csv';
$sqlFile = 'C:/laragon/www/optima/databases/Input_Data/master_import_from_zero.sql';

if(!file_exists($csvFile)) die("File $csvFile tidak ditemukan.\n");

$handleCsv = fopen($csvFile, 'r');
$handleSql = fopen($sqlFile, 'w');

fgetcsv($handleCsv, 0, ';'); // skip header

fwrite($handleSql, "/*\n");
fwrite($handleSql, " * OPTIMA Master Data Import SQL (FRESH START DARI NOL)\n");
fwrite($handleSql, " * Generated on: " . date('Y-m-d H:i:s') . "\n");
fwrite($handleSql, " */\n\n");
fwrite($handleSql, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

// RESET TABEL
fwrite($handleSql, "-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 0. TRUNCATE TABEL UNTUK RESET ID\n");
fwrite($handleSql, "-- --------------------------------------------------------\n");
fwrite($handleSql, "TRUNCATE TABLE kontrak_unit;\n");
fwrite($handleSql, "TRUNCATE TABLE kontrak;\n\n");
fwrite($handleSql, "-- Catatan: Tabel inventory_unit TIDAK di-truncate karena ini data master unit aset.\n\n");

$kontrakCache = []; 
$kontrakQueries = [];
$kontrakUnitQueries = [];
$inventoryUnitUpdates = [];

$isValidDate = function($date) {
    if(!$date) return false;
    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return false;
    $d = explode('-', $date);
    return checkdate((int)$d[1], (int)$d[2], (int)$d[0]);
};

while (($row = fgetcsv($handleCsv, 0, ';')) !== FALSE) {
    if(count($row) < 17) continue;

    $unit_id = (int)trim($row[0]);
    $nama_customer = trim($row[2]);
    $customer_id = (int)trim($row[3]);
    $tahun = (int)trim($row[10]);
    $nomor_po = addslashes(trim($row[11]));
    $no_kontrak = addslashes(trim($row[12]));
    $awal_kontrak = trim($row[13]);
    $akhir_kontrak = trim($row[14]);
    $harga = (float)trim($row[15]);

    if(!$unit_id) continue;
    
    $loc_id = $customer_id ? "(SELECT id FROM customer_locations WHERE customer_id = $customer_id LIMIT 1)" : "1";
    
    $is_po_only = false;
    if(empty($no_kontrak)) {
        $cleanName = preg_replace('/[^A-Za-z0-9]/', '', substr($nama_customer, 0, 10));
        $no_kontrak = "PO-ONLY-" . ($customer_id ?: $cleanName);
        $is_po_only = true;
    }

    if(empty($nomor_po) || $nomor_po == '-') {
        $is_po_only = true;
    }
    
    $rental_type = $is_po_only ? 'PO_ONLY' : 'CONTRACT';
    $v_awal = $isValidDate($awal_kontrak) ? "'$awal_kontrak'" : "'2000-01-01'";
    $v_akhir = $isValidDate($akhir_kontrak) ? "'$akhir_kontrak'" : "'2000-01-01'";

    // 1. KONTRAK (INSERT) - Karena tabel kosong, kita cukup INSERT untuk setiap kontrak unik
    if(!isset($kontrakCache[$no_kontrak])) {
        $kontrakCache[$no_kontrak] = true;
        // PENTING: Karena truncate, kita tidak butuh ON DUPLICATE KEY UPDATE untuk fresh start
        $q_kontrak = "INSERT INTO kontrak (no_kontrak, customer_po_number, rental_type, customer_location_id, tanggal_mulai, tanggal_berakhir, status, dibuat_pada) ";
        $q_kontrak .= "VALUES ('$no_kontrak', '$nomor_po', '$rental_type', IFNULL($loc_id, 1), $v_awal, $v_akhir, 'ACTIVE', NOW());\n";
        $kontrakQueries[] = $q_kontrak;
    }
    
    // 2. KONTRAK_UNIT
    // Kita langsung insert, tidak perlu DELETE sebelumnya karena tabel di-truncate
    $q_ku = "INSERT INTO kontrak_unit (kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status, created_at) ";
    $q_ku .= "SELECT id, $unit_id, $v_awal, $v_akhir, 'ACTIVE', NOW() FROM kontrak WHERE no_kontrak = '$no_kontrak' LIMIT 1;\n";
    $kontrakUnitQueries[] = $q_ku;
    
    // 3. INVENTORY UNIT (Update year/price)
    $updates = [];
    if($tahun > 1900) $updates[] = "tahun_unit = '$tahun'";
    if($harga > 0) $updates[] = "harga_sewa_bulanan = $harga";
    
    if(!empty($updates)) {
        $inventoryUnitUpdates[] = "UPDATE inventory_unit SET " . implode(", ", $updates) . " WHERE id_inventory_unit = $unit_id;\n";
    }
}

fwrite($handleSql, "-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 1. INSERT KONTRAK (DARI 0)\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");
foreach ($kontrakQueries as $q) fwrite($handleSql, $q);

fwrite($handleSql, "\n-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 2. INSERT KONTRAK UNIT MAPPING\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");
foreach ($kontrakUnitQueries as $q) fwrite($handleSql, $q);

fwrite($handleSql, "\n-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 3. INVENTORY UNIT UPDATES (TAHUN & HARGA)\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");
// Hilangkan duplikat query inventory updates jika ada unit_id ganda
$inventoryUnitUpdates = array_unique($inventoryUnitUpdates);
foreach ($inventoryUnitUpdates as $q) fwrite($handleSql, $q);

fwrite($handleSql, "\n-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 4. RECALCULATE TOTAL UNITS AND PRICES IN KONTRAK\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");

$recalc_q = "
UPDATE kontrak k
JOIN (
    SELECT 
        ku.kontrak_id, 
        COUNT(ku.id) as counted_units, 
        SUM(COALESCE(iu.harga_sewa_bulanan, 0)) as total_price
    FROM kontrak_unit ku
    JOIN inventory_unit iu ON ku.unit_id = iu.id_inventory_unit
    WHERE ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
    GROUP BY ku.kontrak_id
) agg ON k.id = agg.kontrak_id
SET 
    k.total_units = agg.counted_units,
    k.nilai_total = agg.total_price;
";
fwrite($handleSql, $recalc_q . "\n");
fwrite($handleSql, "SET FOREIGN_KEY_CHECKS = 1;\n");

fclose($handleCsv);
fclose($handleSql);

echo "BERHASIL: SQL Export 'From Zero' generated at $sqlFile\n";
?>
