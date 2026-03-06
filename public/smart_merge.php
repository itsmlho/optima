<?php
/**
 * SMART MERGE (4-Way Data Match)
 * --------------------------------------------------------------------------
 * Hierarki Source of Truth:
 * 1. INVENTORY (Data Master) -> Baseline seluruh unit (4989), saring yang relevan.
 * 2. ACCOUNTING -> Acuan kepastian bahwa unit tersebut disewa (Active Billing), Tanggal & Harga.
 * 3. MARKETING -> Acuan legalitas (No Kontrak, Nama Perusahaan, No PO).
 * 4. SERVICE -> Acuan kehadiran fisik di lapangan (Bukti kuat).
 */

$start_time = microtime(true);

// --------------------------------------------------------------------------
// 1. FILE CONFIGURATION
// --------------------------------------------------------------------------
$src_inv = 'C:/laragon/www/optima/databases/Input_Data/inventory_unit.csv'; // 4989 Unit Master
$src_acc = 'C:/laragon/www/optima/databases/Input_Data/data_acc.csv';       // Penagihan Bulanan
$src_mkt = 'C:/laragon/www/optima/databases/Input_Data/data_marketing.csv'; // Legal Kontrak & PIC
$src_srv = 'C:/laragon/www/optima/databases/Input_Data/data_service.csv';   // Aktivitas Fisik

$out_valid = 'C:/laragon/www/optima/databases/Input_Data/master_reconciled_v2.csv';
$out_anomali = 'C:/laragon/www/optima/databases/Input_Data/laporan_anomali_v2.csv';

// Helpers
function readCsvAsAssoc($filepath, $delimiter = ';') {
    if (!file_exists($filepath)) die("File $filepath tidak ditemukan!\n");
    $data = [];
    $handle = fopen($filepath, 'r');
    $header = fgetcsv($handle, 0, $delimiter);
    
    // Safely remove UTF-8 BOM from the very first column header ONLY
    if(isset($header[0])) {
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
    }
    // Trim all headers
    $header = array_map('trim', $header);
    
    while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
        // Skip empty rows
        if(count($row) <= 1 && empty($row[0])) continue;
        
        if(count($row) == count($header)) {
            $data[] = array_combine($header, $row);
        } else {
            // Jika row lebih pendek, lengkapi dengan string kosong
            $temp = [];
            foreach($header as $i => $col) {
                $temp[$col] = isset($row[$i]) ? $row[$i] : '';
            }
            $data[] = $temp;
        }
    }
    fclose($handle);
    return $data;
}

function getSafeVal($row, $possible_keys) {
    foreach($possible_keys as $k) {
        if(isset($row[$k]) && trim($row[$k]) !== '') return trim($row[$k]);
    }
    return '';
}

// --------------------------------------------------------------------------
// 2. MEMBUAT DICTIONARIES BERDASARKAN SOURCE OF TRUTH
// --------------------------------------------------------------------------
echo "Membaca semua file data...\n";

// A. INVENTORY (Data Tulang Punggung)
$inv_raw = readCsvAsAssoc($src_inv, ';');
$master_Inventory = []; // Index by id_inventory_unit
$map_no2id = [];        // Map no_unit -> id_inventory_unit

foreach($inv_raw as $row) {
    $uid = getSafeVal($row, ['id_inventory_unit', 'id']);
    $no_unit = getSafeVal($row, ['no_unit']);
    if($uid) {
        $master_Inventory[$uid] = [
            'no_unit' => $no_unit,
            'sn' => getSafeVal($row, ['serial_number']),
            'tahun' => getSafeVal($row, ['tahun_unit']),
            'lokasi_gudang' => strtoupper(getSafeVal($row, ['lokasi_unit']))
        ];
        if($no_unit) {
            $map_no2id[$no_unit] = $uid; // Build lookup table
        }
    }
}
echo "=> Loaded Inventory: " . count($master_Inventory) . " unit.\n";


// Helper to resolve string unit ID to actual DB ID
function resolveUnitId($uid_or_no, $map) {
    if(isset($map[$uid_or_no])) return $map[$uid_or_no]; // if it is a no_unit, map it
    return $uid_or_no; // otherwise hope it's an id_inventory_unit or let it fall through
}

// B. ACCOUNTING (Kebenaran Harga & Status Penagihan)
$acc_raw = readCsvAsAssoc($src_acc, ';');
$data_Accounting = [];
foreach($acc_raw as $row) {
    $raw_uid = getSafeVal($row, ['NOUNIT', 'No Unit', 'No. Unit', 'id_inventory_unit', 'unit_id']);
    if(!$raw_uid) continue;
    
    $uid = resolveUnitId($raw_uid, $map_no2id);
    
    $hrg_str = preg_replace('/[^0-9]/', '', getSafeVal($row, ['HARGA', 'Harga ', 'Harga', 'Harga Sewa']));
    
    $data_Accounting[$uid] = [
        'harga' => (float)$hrg_str,
        'awal_sewa' => getSafeVal($row, ['KONTRAK AWAL', 'Mulai ', 'Mulai', 'Awal Sewa']),
        'akhir_sewa' => getSafeVal($row, ['KONTRAK AKHIR', 'Selesai ', 'Selesai', 'Akhir Sewa'])
    ];
}
echo "=> Loaded Accounting: " . count($data_Accounting) . " baris tagihan.\n";


// C. MARKETING (Kebenaran Kontrak & Customer)
$mkt_raw = readCsvAsAssoc($src_mkt, ';');
$data_Marketing = [];
foreach($mkt_raw as $row) {
    $raw_uid = getSafeVal($row, ['No Unit', 'No .Unit', 'No. Unit', 'unit_id']);
    if(!$raw_uid) continue;

    $uid = resolveUnitId($raw_uid, $map_no2id);

    $data_Marketing[$uid] = [
        'nama_customer' => getSafeVal($row, ['Customer', 'Nama Perusahaan']),
        'no_kontrak' => getSafeVal($row, ['KONTRAK', 'Kontrak', 'No Kontrak']),
        'no_po' => getSafeVal($row, ['No PO', 'PO', 'No. PO']),
        'lokasi_marketing' => getSafeVal($row, ['Lokasi', 'Lokasi / Cabang'])
    ];
}
echo "=> Loaded Marketing: " . count($data_Marketing) . " baris unit.\n";


// D. SERVICE (Kebenaran Fisik Aktual di Lapangan)
$srv_raw = readCsvAsAssoc($src_srv, ';');
$data_Service = [];
foreach($srv_raw as $row) {
    $raw_uid = getSafeVal($row, ['unit_id', 'No Unit', 'No. Unit']);
    if(!$raw_uid) continue;

    // Service file usually has BOTH unit_id and No Unit. If it has unit_id, it might match directly
    if(isset($map_no2id[$raw_uid])) {
        $uid = $map_no2id[$raw_uid]; // It was a no_unit
    } else {
        $uid = $raw_uid; // It probably is already the unit_id
    }

    $data_Service[$uid] = [
        'nama_customer_srv' => getSafeVal($row, ['nama customer', 'Nama Perusahaan', 'Customer']),
        'lokasi_service' => getSafeVal($row, ['lokasi_mentah', 'Lokasi / Cabang', 'Lokasi'])
    ];
}
echo "=> Loaded Service: " . count($data_Service) . " baris aktivitas service.\n";


// --------------------------------------------------------------------------
// 3. RECONCILIATION ALGORITHM
// --------------------------------------------------------------------------
echo "\nMemulai Rekonsiliasi Silang 4 Departemen...\n";

$reconciled_data = [];
$anomalies = [];

// Header untuk file anomali
$anomali_header = ["ID_Unit", "SN_Unit", "Status_Tagihan_ACC", "Status_Kontrak_MKT", "Aktif_Service_SRV", "Lokasi_Gudang_INV", "Masalah", "Rekomendasi_Validasi"];

// Header untuk file valid
$valid_header = [
    "ID_Unit", "Serial_Number", "Tahun_Unit", "Nama_Customer", "No_PO", "No_Kontrak", 
    "Awal_Sewa", "Akhir_Sewa", "Harga_Bulan", "Sumber_Data_Customer"
];


// Kita iterasi SEMUA ID (Gabungan dari INV, ACC, MKT, SRV)
$all_ids = array_unique(array_merge(
    array_keys($master_Inventory), 
    array_keys($data_Accounting), 
    array_keys($data_Marketing), 
    array_keys($data_Service)
));

foreach($all_ids as $uid) {
    // Skip jika unit benar kosong
    if(empty($uid) || !is_numeric($uid)) continue;

    $in_inv = isset($master_Inventory[$uid]);
    $in_acc = isset($data_Accounting[$uid]);
    $in_mkt = isset($data_Marketing[$uid]);
    $in_srv = isset($data_Service[$uid]);

    $inv_loc = $in_inv ? $master_Inventory[$uid]['lokasi_gudang'] : 'TIDAK ADA DI MASTER';
    $sn = $in_inv ? $master_Inventory[$uid]['sn'] : 'UNKNOWN';
    $tahun = $in_inv ? $master_Inventory[$uid]['tahun'] : '';

    $is_jual_or_scrap = (strpos($inv_loc, 'JUAL') !== false) || (strpos($inv_loc, 'RONGSOK') !== false) || (strpos($inv_loc, 'SCRAP') !== false);
    $is_bengkel = (strpos($inv_loc, 'WORKSHOP') !== false) || (strpos($inv_loc, 'POS') !== false);

    $is_anomaly = false;
    $anomaly_reasons = [];
    $recommended_action = "";

    // -- ATURAN ANOMALI (BUSINESS RULES) --

    // Aturan 1: Ada ditagihan, TAPI master inventory bilang sudah JUAL/RONGSOK
    if ($in_acc && $is_jual_or_scrap) {
        $is_anomaly = true;
        $anomaly_reasons[] = "Di-Tagih (Active Billing) tapi master status JUAL/RONGSOK.";
        $recommended_action = "Cek Accounting apakah ID unit tagihan salah, atau Warehouse salah tulis status.";
    }

    // Aturan 2: Di data operasional (MKT/SRV/ACC) ada, tapi sama sekali TIDAK DITEMUKAN di Master Inventory 4989.
    if (($in_acc || $in_mkt || $in_srv) && !$in_inv) {
        $is_anomaly = true;
        $anomaly_reasons[] = "Unit diproses operasional tapi Tidak Ditemukan di Master Database.";
        $recommended_action = "Tambahkan unit ke database gudang terlebih dahulu.";
    }

    // Aturan 3: Accounting MENAGIH, TAPI Marketing tidak punya kontrak DAN Service tidak pernah ada data servisnya.
    if ($in_acc && !$in_mkt && !$in_srv) {
        // Toleransi: Mungkin unit baru deploy. Kita masukkan ke reconciled tapi dicatat sebagai Warning
        $anomaly_reasons[] = "[WARNING] Ditagihkan tapi tidak ada kontrak dari Marketing maupun catatan Service.";
    }

    // Aturan 4: Marketing bilang ADA KONTRAK AKTIF, TAPI Accounting tidak pernah / belum menagih.
    if ($in_mkt && !$in_acc && !$is_jual_or_scrap && !$is_bengkel) {
        // Bisa jadi PO Only atau belum jatuh tempo tagihan.
        $anomaly_reasons[] = "[WARNING] Kontrak ada di Marketing, tapi belum ada penagihan di Accounting.";
    }

    // -- FINAL DECISION (Lolos masuk Reconciled Data) --
    // Kita hanya mendata ke SQL jika minimal ada di Marketing ATAU ada di Accounting, 
    // DAN BUKAN anomali tingkat Parah (seperti Aturan 1 & 2).
    
    // Jika itu anomali parah, kita stop dan lempar ke laporan_anomali saja.
    if ($is_anomaly) {
        $anomalies[] = [
            $uid, $sn, 
            $in_acc ? "DITAGIH" : "KOSONG", 
            $in_mkt ? "ADA_KONTRAK" : "KOSONG", 
            $in_srv ? "SERVIS_JALAN" : "KOSONG", 
            $inv_loc, 
            implode(" | ", $anomaly_reasons),
            $recommended_action
        ];
        continue; // Skip dari tabel final karena datanya bermasalah
    }

    // Jika bukan anomali parah, dan unit ini dikerjakan secara operasional (MKT atau ACC atau SRV)
    if (($in_acc || $in_mkt || $in_srv) && !$is_jual_or_scrap && !$is_bengkel) {
        // Tentukan Nama Customer Prioritas:
        // Prioritas kebenaran: Service (Fisik) -> Marketing (PO) -> Accounting
        $final_customer = '';
        $sumber_cust = '';
        if ($in_srv && !empty($data_Service[$uid]['nama_customer_srv'])) {
            $final_customer = $data_Service[$uid]['nama_customer_srv'];
            $sumber_cust = 'SERVICE';
        } else if ($in_mkt && !empty($data_Marketing[$uid]['nama_customer'])) {
            $final_customer = $data_Marketing[$uid]['nama_customer'];
            $sumber_cust = 'MARKETING';
        } else if ($in_inv && strpos($inv_loc, 'WORKSHOP') === false) {
             $final_customer = $inv_loc;
             $sumber_cust = 'WAREHOUSE';
        }

        // Jangan insert jika customer kosong sama sekali
        if(empty(trim($final_customer))) {
             if(count($anomaly_reasons) == 0) {
                 $anomalies[] = [
                    $uid, $sn, 
                    $in_acc ? "DITAGIH" : "KOSONG", $in_mkt ? "ADA_KONTRAK" : "KOSONG", $in_srv ? "SERVIS_JALAN" : "KOSONG", 
                    $inv_loc, "Tidak ada institusi/perusahaan yang disebutkan (Customer Kosong).", "Lengkapi di MKT/SRV"
                 ];
             }
             continue; 
        }

        // Tentukan Data Tagihan (Dari Acc)
        $harga = $in_acc ? $data_Accounting[$uid]['harga'] : 0;
        $awal = $in_acc ? $data_Accounting[$uid]['awal_sewa'] : '';
        $akhir = $in_acc ? $data_Accounting[$uid]['akhir_sewa'] : '';

        // Tentukan Legalitas (Dari Mkt)
        $kontrak = $in_mkt ? $data_Marketing[$uid]['no_kontrak'] : '';
        $po = $in_mkt ? $data_Marketing[$uid]['no_po'] : '';

        $reconciled_data[] = [
            $uid, $sn, $tahun, $final_customer, $po, $kontrak,
            $awal, $akhir, $harga, $sumber_cust
        ];
    }
}


// --------------------------------------------------------------------------
// 4. WRITE OUTPUTS
// --------------------------------------------------------------------------
$fh_valid = fopen($out_valid, 'w');
fputcsv($fh_valid, $valid_header, ';');
foreach($reconciled_data as $row) fputcsv($fh_valid, $row, ';');
fclose($fh_valid);

$fh_anomali = fopen($out_anomali, 'w');
fputcsv($fh_anomali, $anomali_header, ';');
foreach($anomalies as $row) fputcsv($fh_anomali, $row, ';');
fclose($fh_anomali);

$time = round((microtime(true) - $start_time), 2);
echo "Selesai dalam $time detik.\n";
echo "Total Output Valid Lolos Filter: " . count($reconciled_data) . " unit siap import.\n";
echo "Total Anomali Ditemukan: " . count($anomalies) . " unit bermasalah yang terisolir ke CSV.\n";
echo "Laporan dapat dicek di databases/Input_Data/laporan_anomali.csv\n";
?>
