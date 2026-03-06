<?php
/**
 * Script untuk merge dan merapikan data_mrktg, data_acc, dan data_service
 * menjadi file master CSV yang siap diinput.
 */

$dir = 'C:/laragon/www/optima/databases/Input_Data/';
$files = [
    'mrktg' => $dir . 'data_marketing.csv',
    'acc'   => $dir . 'data_acc.csv',
    'srv'   => $dir . 'data_service.csv'
];

$outputFile = $dir . 'master_import_ready.csv';

// Struktur target master import
$headerTarget = [
    'unit_id', 
    'no_unit', 
    'nama_customer', 
    'customer_id', 
    'lokasi_db', 
    'lokasi_mentah', 
    'merk', 
    'model', 
    'tipe', 
    'kapasitas', 
    'tahun', 
    'nomor_po', 
    'no_kontrak', 
    'awal_kontrak', 
    'akhir_kontrak', 
    'harga', 
    'sumber_data'
];

$handleOut = fopen($outputFile, 'w');
fputcsv($handleOut, $headerTarget, ';');

// Mapping unit_id as key to detect duplicates
$mergedData = [];

// 1. Parse Data Marketing
if (file_exists($files['mrktg'])) {
    $h = fopen($files['mrktg'], 'r');
    $head = fgetcsv($h, 0, ';');
    while (($row = fgetcsv($h, 0, ';')) !== FALSE) {
        if(count($row) < 15) continue;
        // MARKETING;Customer;Lokasi;No Unit;Year;Merk;Model;DEPARTEMEN;Seri Number;Kapasitas;ATTACHEMANT;No PO;KONTRAK;AWAL KONTRAK;AKHIR KONTRAK;Harga/Unit
        $no_unit = trim($row[3]);
        $harga_str = preg_replace('/[^0-9]/', '', $row[15] ?? '');
        
        if(!$no_unit) continue;
        
        $mergedData[$no_unit] = [
            'unit_id' => '', // akan diisi dari DB/auto
            'no_unit' => $no_unit,
            'nama_customer' => trim($row[1]),
            'customer_id' => '',
            'lokasi_db' => trim($row[2]),
            'lokasi_mentah' => trim($row[2]),
            'merk' => trim($row[5]),
            'model' => trim($row[6]),
            'tipe' => trim($row[7]),
            'kapasitas' => trim($row[9]),
            'tahun' => trim($row[4]),
            'nomor_po' => trim($row[11]),
            'no_kontrak' => trim($row[12]),
            'awal_kontrak' => trim($row[13]),
            'akhir_kontrak' => trim($row[14]),
            'harga' => $harga_str,
            'sumber_data' => 'Marketing'
        ];
    }
    fclose($h);
}

// 2. Parse Data Service (Lebih Akurat terkait unit_id)
if (file_exists($files['srv'])) {
    $h = fopen($files['srv'], 'r');
    $head = fgetcsv($h, 0, ';');
    if(!$head) $head = fgetcsv($h, 0, ','); // fallback
    
    // Asumsi: Marketing;Departemen;unit_id;No Unit;nama customer;customers_id;area_id;AREA_mentah;customer_location_id;lokasi_db;lokasi_mentah;kontrak_id;Kontrak;No PO;Awal Kontrak;Kontrak Habis;Harga
    while (($row = fgetcsv($h, 0, ';')) !== FALSE) {
        if(count($row) < 15) continue;
        $no_unit = trim($row[3]);
        if(!$no_unit) continue;

        $harga_str = preg_replace('/[^0-9]/', '', $row[16] ?? '');

        if(isset($mergedData[$no_unit])) {
            // Update/Merge
            $mergedData[$no_unit]['unit_id'] = trim($row[2]);
            $mergedData[$no_unit]['customer_id'] = trim($row[5]);
            $mergedData[$no_unit]['lokasi_db'] = trim($row[9]);
            $mergedData[$no_unit]['lokasi_mentah'] = trim($row[10]);
            if(trim($row[12])) $mergedData[$no_unit]['no_kontrak'] = trim($row[12]);
            if(trim($row[13])) $mergedData[$no_unit]['nomor_po'] = trim($row[13]);
            if(trim($row[14])) $mergedData[$no_unit]['awal_kontrak'] = trim($row[14]);
            if(trim($row[15])) $mergedData[$no_unit]['akhir_kontrak'] = trim($row[15]);
            if($harga_str) $mergedData[$no_unit]['harga'] = $harga_str;
            $mergedData[$no_unit]['sumber_data'] .= ', Service';
        } else {
            // New Entry from Service
            $mergedData[$no_unit] = [
                'unit_id' => trim($row[2]),
                'no_unit' => $no_unit,
                'nama_customer' => trim($row[4]),
                'customer_id' => trim($row[5]),
                'lokasi_db' => trim($row[9]),
                'lokasi_mentah' => trim($row[10]),
                'merk' => '',
                'model' => '',
                'tipe' => trim($row[1]),
                'kapasitas' => '',
                'tahun' => '',
                'nomor_po' => trim($row[13]),
                'no_kontrak' => trim($row[12]),
                'awal_kontrak' => trim($row[14]),
                'akhir_kontrak' => trim($row[15]),
                'harga' => $harga_str,
                'sumber_data' => 'Service'
            ];
        }
    }
    fclose($h);
}

// 3. Parse Data Accounting (Seringkali harga paling valid)
if (file_exists($files['acc'])) {
    $h = fopen($files['acc'], 'r');
    $head = fgetcsv($h, 0, ';');
    // Asumsi: NOUNIT;MARKETING;CUSTOMER;LOKASI;UNIT ANTAR;PO;Kontrak;KONTRAK AWAL;KONTRAK AKHIR;HARGA
    while (($row = fgetcsv($h, 0, ';')) !== FALSE) {
        if(count($row) < 8) continue;
        $no_unit = trim($row[0]);
        if(!$no_unit) continue;
        
        $harga_str = preg_replace('/[^0-9]/', '', $row[9] ?? '');

        if(isset($mergedData[$no_unit])) {
            if(trim($row[5])) $mergedData[$no_unit]['nomor_po'] = trim($row[5]);
            if(trim($row[6])) $mergedData[$no_unit]['no_kontrak'] = trim($row[6]);
            if(trim($row[7])) $mergedData[$no_unit]['awal_kontrak'] = trim($row[7]);
            if(trim($row[8])) $mergedData[$no_unit]['akhir_kontrak'] = trim($row[8]);
            if($harga_str) $mergedData[$no_unit]['harga'] = $harga_str; // Force acc price
            $mergedData[$no_unit]['sumber_data'] .= ', Acc';
        } else {
             $mergedData[$no_unit] = [
                'unit_id' => '',
                'no_unit' => $no_unit,
                'nama_customer' => trim($row[2]),
                'customer_id' => '',
                'lokasi_db' => trim($row[3]),
                'lokasi_mentah' => trim($row[3]),
                'merk' => '',
                'model' => '',
                'tipe' => '',
                'kapasitas' => '',
                'tahun' => '',
                'nomor_po' => trim($row[5]),
                'no_kontrak' => trim($row[6]),
                'awal_kontrak' => trim($row[7]),
                'akhir_kontrak' => trim($row[8]),
                'harga' => $harga_str,
                'sumber_data' => 'Acc'
            ];
        }
    }
    fclose($h);
}

// 4. Fill Missing Unit IDs / Customer IDs from DB & Clean dates
$mysqli = new mysqli("localhost", "root", "", "optima_ci");

function formatDate($dateStr) {
    if(!$dateStr || $dateStr == '-' || $dateStr == '#N/A') return '';
    $dateStr = str_replace(['/', '.'], '-', trim($dateStr));
    $parts = explode('-', $dateStr);
    if(count($parts) == 3) {
        if(strlen($parts[2]) == 4) { // DD-MM-YYYY
            return sprintf("%04d-%02d-%02d", $parts[2], $parts[1], $parts[0]);
        } else if(strlen($parts[0]) == 4) { // YYYY-MM-DD
             return sprintf("%04d-%02d-%02d", $parts[0], $parts[1], $parts[2]);
        }
    }
    return $dateStr;
}

$kosongPO = 0;

foreach ($mergedData as $no_unit => &$data) {
    // Lookup unit db
    if(empty($data['unit_id'])) {
        $stmt = $mysqli->prepare("SELECT id_inventory_unit FROM inventory_unit WHERE no_unit = ? LIMIT 1");
        $stmt->bind_param('s', $no_unit);
        $stmt->execute();
        $res = $stmt->get_result();
        if($row = $res->fetch_assoc()){
            $data['unit_id'] = $row['id_inventory_unit'];
        }
        $stmt->close();
    }
    
    // Lookup customer db
    if(empty($data['customer_id']) && !empty($data['nama_customer'])) {
        $stmt = $mysqli->prepare("SELECT id FROM customers WHERE customer_name LIKE ? LIMIT 1");
        $like = '%' . $data['nama_customer'] . '%';
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $res = $stmt->get_result();
        if($row = $res->fetch_assoc()){
            $data['customer_id'] = $row['id'];
        }
        $stmt->close();
    }
    
    // Standardize dates (to YYYY-MM-DD for DB insert readiness)
    $data['awal_kontrak'] = formatDate($data['awal_kontrak']);
    $data['akhir_kontrak'] = formatDate($data['akhir_kontrak']);
    
    // Data PO cleaning
    if($data['nomor_po'] == '-' || strtolower(trim($data['nomor_po'])) == 'po perbulan') {
         $data['nomor_po'] = '';
    }
    
    if(empty($data['nomor_po'])) {
        $kosongPO++;
    }

    fputcsv($handleOut, [
        $data['unit_id'], 
        $data['no_unit'], 
        $data['nama_customer'], 
        $data['customer_id'], 
        $data['lokasi_db'], 
        $data['lokasi_mentah'], 
        $data['merk'], 
        $data['model'], 
        $data['tipe'], 
        $data['kapasitas'], 
        $data['tahun'], 
        $data['nomor_po'], 
        $data['no_kontrak'], 
        $data['awal_kontrak'], 
        $data['akhir_kontrak'], 
        $data['harga'], 
        $data['sumber_data']
    ], ';');
}

fclose($handleOut);
$mysqli->close();

echo "BERHASIL MERGE FILE!\n";
echo "Total Unit Tersimpan: " . count($mergedData) . "\n";
echo "Total PO Kosong (PO_ONLY cases): $kosongPO\n";
echo "File tersimpan di: $outputFile\n";
?>
