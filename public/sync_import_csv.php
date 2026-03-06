<?php
/**
 * Script untuk sinkronisasi master_import_ready.csv ke database
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli("localhost", "root", "", "optima_ci");
} catch(Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

$file = 'C:/laragon/www/optima/databases/Input_Data/master_import_ready.csv';
if(!file_exists($file)) die("File master_import_ready.csv tidak ditemukan.\n");

$handle = fopen($file, 'r');
$header = fgetcsv($handle, 0, ';'); // skip header

$stat_kontrak_new = 0;
$stat_ku_new = 0;
$stat_ku_updated = 0;
$stat_missing_unit = 0;
$stat_missing_cust = 0;

$mysqli->begin_transaction();

try {
    while (($row = fgetcsv($handle, 0, ';')) !== FALSE) {
        if(count($row) < 17) continue;

        $unit_id = trim($row[0]);
        $no_unit = trim($row[1]);
        $nama_customer = trim($row[2]);
        $customer_id = trim($row[3]);
        $lokasi_mentah = trim($row[5]);
        $nomor_po = trim($row[11]);
        $no_kontrak = trim($row[12]);
        $awal_kontrak = trim($row[13]);
        $akhir_kontrak = trim($row[14]);
        $harga = trim($row[15]);

        if(!$unit_id) {
            $stat_missing_unit++;
            continue;
        }

        if(!$customer_id && $nama_customer) {
            // Coba cari lagi atau insert customer baru?
            // Untuk sementara kita skip jika tidak ada customer_id karena relasi butuh itu.
            $stat_missing_cust++;
            //continue;
        }

        // 1. Tentukan No Kontrak
        $is_po_only = false;
        if(empty($no_kontrak)) {
            // Jika kosong, kita buat dummy per customer
            $no_kontrak = "PO-ONLY-" . ($customer_id ?: preg_replace('/[^A-Za-z0-9]/', '', substr($nama_customer, 0, 10)));
            $is_po_only = true;
        }

        if(empty($nomor_po) || $nomor_po == '-') {
            $is_po_only = true;
        }

        // 2. Cari Kontrak
        $kontrak_id = null;
        $stmt = $mysqli->prepare("SELECT id FROM kontrak WHERE no_kontrak = ? LIMIT 1");
        $stmt->bind_param('s', $no_kontrak);
        $stmt->execute();
        $res = $stmt->get_result();
        if($k_row = $res->fetch_assoc()) {
            $kontrak_id = $k_row['id'];
        }
        $stmt->close();

        $isValidDate = function($date) {
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return false;
            $d = explode('-', $date);
            return checkdate((int)$d[1], (int)$d[2], (int)$d[0]);
        };

        $v_awal = $isValidDate($awal_kontrak) ? $awal_kontrak : '2000-01-01';
        $v_akhir = $isValidDate($akhir_kontrak) ? $akhir_kontrak : '2000-01-01';

        // 3. Jika belum ada, buat Kontrak
        if(!$kontrak_id) {
            // Cari Customer Location
            $loc_id = null;
            if($customer_id) {
                $stmt = $mysqli->prepare("SELECT id FROM customer_locations WHERE customer_id = ? LIMIT 1");
                $stmt->bind_param('i', $customer_id);
                $stmt->execute();
                $resloc = $stmt->get_result();
                if($lrow = $resloc->fetch_assoc()){
                    $loc_id = $lrow['id'];
                }
                $stmt->close();
            }

            if(!$loc_id) {
                $stmt = $mysqli->prepare("SELECT id FROM customer_locations LIMIT 1");
                $stmt->execute();
                $resloc = $stmt->get_result();
                if($lrow = $resloc->fetch_assoc()){
                    $loc_id = $lrow['id'];
                }
                $stmt->close();
            }

            $rental_type = $is_po_only ? 'PO_ONLY' : 'CONTRACT';
            $customer_po_number = $nomor_po;
            $status = 'ACTIVE';

            $stmt = $mysqli->prepare("INSERT INTO kontrak (no_kontrak, customer_po_number, rental_type, customer_location_id, tanggal_mulai, tanggal_berakhir, status, dibuat_pada) VALUES (?,?,?,?,?,?,?, NOW())");
            $stmt->bind_param('sssisss', $no_kontrak, $customer_po_number, $rental_type, $loc_id, $v_awal, $v_akhir, $status);
            $stmt->execute();
            $kontrak_id = $stmt->insert_id;
            $stmt->close();
            $stat_kontrak_new++;
        }

        // 4. Update / Insert Kontrak_Unit
        // Deprecated unit old connections
        $stmt = $mysqli->prepare("UPDATE kontrak_unit SET status = 'INACTIVE' WHERE unit_id = ? AND kontrak_id != ?");
        $stmt->bind_param('ii', $unit_id, $kontrak_id);
        $stmt->execute();
        $stmt->close();

        // Cek apakah relasi kontrak ini sudah ada
        $stmt = $mysqli->prepare("SELECT id FROM kontrak_unit WHERE unit_id = ? AND kontrak_id = ? LIMIT 1");
        $stmt->bind_param('ii', $unit_id, $kontrak_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $ku_id = null;
        if($ku_row = $res->fetch_assoc()) {
            $ku_id = $ku_row['id'];
        }
        $stmt->close();

        if($ku_id) {
            // Update
            $stmt = $mysqli->prepare("UPDATE kontrak_unit SET tanggal_mulai = ?, tanggal_selesai = ?, status = 'ACTIVE' WHERE id = ?");
            $stmt->bind_param('ssi', $v_awal, $v_akhir, $ku_id);
            $stmt->execute();
            $stmt->close();
            $stat_ku_updated++;
        } else {
            // Insert
            $stmt = $mysqli->prepare("INSERT INTO kontrak_unit (kontrak_id, unit_id, tanggal_mulai, tanggal_selesai, status, created_at) VALUES (?,?,?,?,'ACTIVE',NOW())");
            $stmt->bind_param('iiss', $kontrak_id, $unit_id, $v_awal, $v_akhir);
            $stmt->execute();
            $stmt->close();
            $stat_ku_new++;
        }
        
        // 5. Update harga pada kontrak_spesifikasi jika diperlukan, atau skip karena beda module
    }

    $mysqli->commit();
    echo "PROSES SINKRONISASI BERHASIL!\n";
    echo "Kontrak Baru Dibuat: $stat_kontrak_new\n";
    echo "Relasi Unit (kontrak_unit) Baru Dibuat: $stat_ku_new\n";
    echo "Relasi Unit (kontrak_unit) Diperbarui ke ACTIVE: $stat_ku_updated\n";
    echo "Unit ID Kosong (di skip): $stat_missing_unit\n";
    echo "Customer Kosong: $stat_missing_cust\n";

} catch (Exception $e) {
    $mysqli->rollback();
    echo "ERROR SINKRONISASI: " . $e->getMessage() . "\n";
}

$mysqli->close();
fclose($handle);
?>
