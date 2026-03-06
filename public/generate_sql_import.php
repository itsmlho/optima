<?php
/**
 * Script untuk menghasilkan file import.sql dari master_import_ready.csv
 * Output akan ditaruh di databases/Input_Data/master_import.sql
 */

$csvFile = 'C:/laragon/www/optima/databases/Input_Data/master_import_ready.csv';
$sqlFile = 'C:/laragon/www/optima/databases/Input_Data/master_import.sql';

if(!file_exists($csvFile)) die("File $csvFile tidak ditemukan.\n");

$handleCsv = fopen($csvFile, 'r');
$handleSql = fopen($sqlFile, 'w');

// Header CSV
fgetcsv($handleCsv, 0, ';');

fwrite($handleSql, "/*\n");
fwrite($handleSql, " * OPTIMA Master Data Import SQL\n");
fwrite($handleSql, " * Generated on: " . date('Y-m-d H:i:s') . "\n");
fwrite($handleSql, " */\n\n");
fwrite($handleSql, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

// Untuk mapping sementara agar tidak duplikat insert kontrak
$kontrakCache = []; 
$kontrakIdCounter = 10000; // Mulai dari ID besar agar tidak bentrok (tapi kita pakai query logic jika memungkinkan, atau UPSERT)

// Karena kita generate SQL mentah offline, cara terbaik adalah menggunakan INSERT IGNORE 
// atau ON DUPLICATE KEY UPDATE.
// Untuk relasional yg ID nya auto-increment, lebih aman kita generate procedural atau update records yg sudah di import.

fwrite($handleSql, "-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 1. KONTRAK DATA (UPSERT)\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");

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
    $no_unit = addslashes(trim($row[1]));
    $nama_customer = trim($row[2]);
    $customer_id = (int)trim($row[3]);
    // $lokasi_db = trim($row[4]);
    // $merk = trim($row[6]);
    // $model = trim($row[7]);
    // $tipe = trim($row[8]);
    // $kapasitas = trim($row[9]);
    $tahun = (int)trim($row[10]);
    $nomor_po = addslashes(trim($row[11]));
    $no_kontrak = addslashes(trim($row[12]));
    $awal_kontrak = trim($row[13]);
    $akhir_kontrak = trim($row[14]);
    $harga = (float)trim($row[15]);

    if(!$unit_id) continue;
    
    // Default location id (dummy 1 for now if no customer_id exists)
    $loc_id = $customer_id ? "(SELECT id FROM customer_locations WHERE customer_id = $customer_id LIMIT 1)" : "1";
    
    // Kontrak creation logic
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

    // 1. KONTRAK (INSERT IGNORE / UPSERT)
    if(!isset($kontrakCache[$no_kontrak])) {
        $kontrakCache[$no_kontrak] = true;
        $q_kontrak = "INSERT INTO kontrak (no_kontrak, customer_po_number, rental_type, customer_location_id, tanggal_mulai, tanggal_berakhir, status, dibuat_pada) ";
        $q_kontrak .= "VALUES ('$no_kontrak', '$nomor_po', '$rental_type', IFNULL($loc_id, 1), $v_awal, $v_akhir, 'ACTIVE', NOW()) ";
        $q_kontrak .= "ON DUPLICATE KEY UPDATE customer_po_number = VALUES(customer_po_number), rental_type = VALUES(rental_type), tanggal_mulai = VALUES(tanggal_mulai), tanggal_berakhir = VALUES(tanggal_berakhir);\n";
        $kontrakQueries[] = $q_kontrak;
    }
    
    // 2. KONTRAK_UNIT (UPSERT via Sub-Select) - Mapping the exact unit to contract
    // We update historical records first
    $q_hist = "UPDATE kontrak_unit SET status = 'INACTIVE' WHERE unit_id = $unit_id AND kontrak_id != (SELECT id FROM kontrak WHERE no_kontrak = '$no_kontrak' LIMIT 1);\n";
    $kontrakUnitQueries[] = $q_hist;
    
    // Insert new mapping
    $q_ku = "INSERT INTO kontrak_unit (kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status, created_at) ";
    $q_ku .= "SELECT id, $unit_id, $v_awal, $v_akhir, 'ACTIVE', NOW() FROM kontrak WHERE no_kontrak = '$no_kontrak' LIMIT 1 ";
    // MySQL Insert logic doesn't support ON DUPLICATE key like this without a unique constraint, so instead we do an UPDATE or INSERT check 
    // For pure SQL export without procedures, it's safer to delete exact mapping then insert, or rely on existing constraints.
    // Assuming (kontrak_id, unit_id) is NOT UNIQUE but we only want 1 ACTIVE.
    
    // Better logic to avoid dupes:
    $q_ku_delete = "DELETE FROM kontrak_unit WHERE unit_id = $unit_id AND kontrak_id = (SELECT id FROM kontrak WHERE no_kontrak = '$no_kontrak' LIMIT 1);\n";
    $kontrakUnitQueries[] = $q_ku_delete;
    $kontrakUnitQueries[] = $q_ku . ";\n";
    
    // 3. INVENTORY UNIT (Update year/price if valid)
    $updates = [];
    if($tahun > 1900) $updates[] = "tahun_unit = '$tahun'";
    if($harga > 0) $updates[] = "harga_sewa_bulanan = $harga";
    
    if(!empty($updates)) {
        $inventoryUnitUpdates[] = "UPDATE inventory_unit SET " . implode(", ", $updates) . " WHERE id_inventory_unit = $unit_id;\n";
    }
}

// Menulis ke file SQL
foreach ($kontrakQueries as $q) fwrite($handleSql, $q);

fwrite($handleSql, "\n-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 2. KONTRAK UNIT MAPPING\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");
foreach ($kontrakUnitQueries as $q) fwrite($handleSql, $q);

fwrite($handleSql, "\n-- --------------------------------------------------------\n");
fwrite($handleSql, "-- 3. INVENTORY UNIT UPDATES (TAHUN & HARGA)\n");
fwrite($handleSql, "-- --------------------------------------------------------\n\n");
foreach ($inventoryUnitUpdates as $q) fwrite($handleSql, $q);

// Recalculate totals script logic in SQL
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

echo "BERHASIL: SQL Export file generated at $sqlFile\n";
echo "Total Kontrak Insert/Update Queries: " . count($kontrakQueries) . "\n";
echo "Total Unit Mapping Queries: " . count($kontrakUnitQueries) . "\n";
echo "Total Unit Update (Year/Price) Queries: " . count($inventoryUnitUpdates) . "\n";
?>
