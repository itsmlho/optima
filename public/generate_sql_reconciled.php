<?php
/**
 * GENERATE SQL RECONCILED (FRESH START DARI NOL)
 * -------------------------------------------------------------
 * Script ini mengambil data suci dari master_reconciled_v2.csv
 * dan mengonversinya menjadi master_import_reconciled.sql yang
 * menghapus bersih relasi kontrak lama lalu memasukkan 2200+
 * data relasi yang 100% tervalidasi antar 4 departemen.
 */

$csvFile = 'C:/laragon/www/optima/databases/Input_Data/master_reconciled_v2.csv';
$sqlFile = 'C:/laragon/www/optima/databases/Input_Data/master_import_reconciled.sql';

if(!file_exists($csvFile)) die("File $csvFile tidak ditemukan.\n");

$handleCsv = fopen($csvFile, 'r');
$handleSql = fopen($sqlFile, 'w');

fgetcsv($handleCsv, 0, ';'); // skip header

fwrite($handleSql, "/*\n");
fwrite($handleSql, " * OPTIMA Master Data Import SQL (RECONCILED 4-WAY MATCH)\n");
fwrite($handleSql, " * Generated on: " . date('Y-m-d H:i:s') . "\n");
fwrite($handleSql, " */\n\n");
fwrite($handleSql, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

// RESET TABEL MAPPING LAMA
fwrite($handleSql, "-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 0. TRUNCATE TABEL UNTUK RESET KONTRAK & KONTRAK UNIT\n");
fwrite($handleSql, "-- --------------------------------------------------------\n");
fwrite($handleSql, "TRUNCATE TABLE kontrak_unit;\n");
fwrite($handleSql, "TRUNCATE TABLE kontrak;\n\n");

$kontrakCache = []; 
$kontrakQueries = [];
$kontrakUnitQueries = [];
$inventoryUnitUpdates = [];

function normalizeDate($d) {
    if(empty($d) || trim($d) == '-' || trim($d) == '') return '2000-01-01';
    
    // Y-m-d format
    if(preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($d))) return trim($d);
    
    // d/m/Y or d/m/y format from Excel exports
    if(preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/', trim($d), $m)) {
        $year = strlen($m[3]) == 2 ? '20' . $m[3] : $m[3];
        return sprintf('%04d-%02d-%02d', $year, $m[2], $m[1]);
    }
    
    // Strtotime fallback
    $time = strtotime(str_replace('/', '-', $d));
    if($time) return date('Y-m-d', $time);
    
    return '2000-01-01';
}

while (($row = fgetcsv($handleCsv, 0, ';')) !== FALSE) {
    if(count($row) < 9) continue;

    $unit_id = (int)trim($row[0]);
    if(!$unit_id) continue;

    $tahun = (int)trim($row[2]);
    $nama_customer = addslashes(trim($row[3]));
    $nomor_po = addslashes(trim($row[4]));
    $no_kontrak = addslashes(trim($row[5]));
    $awal_kontrak = normalizeDate($row[6]);
    $akhir_kontrak = normalizeDate($row[7]);
    $harga = (float)trim($row[8]);

    
    $is_po_only = false;
    if(empty($no_kontrak) || $no_kontrak == '-') {
        $cleanName = preg_replace('/[^A-Za-z0-9]/', '', substr($nama_customer, 0, 10));
        $no_kontrak = "PO-ONLY-" . ($cleanName ?: 'UNKNOWN');
        $is_po_only = true;
    }

    if(empty($nomor_po) || $nomor_po == '-') {
        $is_po_only = true;
    }
    
    $rental_type = $is_po_only ? 'PO_ONLY' : 'CONTRACT';
    
    // SQL Subquery Cerdas untuk mencocokkan ID Pelanggan
    $get_customer_id = "(SELECT id FROM customers WHERE customer_name = '$nama_customer' LIMIT 1)";
    
    // Gunakan klausa dinamis IF agar tidak menyebabkan SQL error saat parameter 0 masuk ke subquery
    $get_location_id = "IFNULL((SELECT id FROM customer_locations WHERE customer_id = $get_customer_id LIMIT 1), NULL)";

    // 1. KONTRAK (INSERT) - Karena truncate, kita inject unique contracts
    if(!isset($kontrakCache[$no_kontrak])) {
        $kontrakCache[$no_kontrak] = true;
        
        $q_kontrak = "INSERT INTO kontrak (no_kontrak, customer_po_number, rental_type, customer_location_id, tanggal_mulai, tanggal_berakhir, status, dibuat_pada) ";
        $q_kontrak .= "VALUES ('$no_kontrak', '$nomor_po', '$rental_type', $get_location_id, '$awal_kontrak', '$akhir_kontrak', 'ACTIVE', NOW());\n";
        $kontrakQueries[] = $q_kontrak;
    }
    
    // 2. KONTRAK_UNIT
    $q_ku = "INSERT INTO kontrak_unit (kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status, created_at) ";
    $q_ku .= "SELECT id, $unit_id, '$awal_kontrak', '$akhir_kontrak', 'ACTIVE', NOW() FROM kontrak WHERE no_kontrak = '$no_kontrak' LIMIT 1;\n";
    $kontrakUnitQueries[] = $q_ku;
    
    // 3. INVENTORY UNIT (Update year/price via DB Master ID)
    $updates = [];
    if($tahun > 1900) $updates[] = "tahun_unit = '$tahun'";
    if($harga > 0) $updates[] = "harga_sewa_bulanan = $harga";
    
    if(!empty($updates)) {
        $inventoryUnitUpdates[] = "UPDATE inventory_unit SET " . implode(", ", $updates) . " WHERE id_inventory_unit = $unit_id;\n";
    }
}

fwrite($handleSql, "-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 1. INSERT KONTRAK REKONSILIASI (DARI 0)\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");
foreach ($kontrakQueries as $q) fwrite($handleSql, $q);

fwrite($handleSql, "\n-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 2. INSERT KONTRAK UNIT MAPPING\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");
foreach ($kontrakUnitQueries as $q) fwrite($handleSql, $q);

fwrite($handleSql, "\n-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 3. INVENTORY UNIT UPDATES (HARGA TAGIHAN)\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");
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

echo "BERHASIL: SQL Export Reconciled generated at $sqlFile\n";
?>
