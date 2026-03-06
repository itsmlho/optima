<?php
// audit_csv.php
$mysqli = new mysqli("localhost", "root", "", "optima_ci");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 1. Audit Table Kontrak & Kontrak_Unit
$res_kontrak = $mysqli->query("SELECT COUNT(*) as c FROM kontrak");
$row = $res_kontrak->fetch_assoc();
$total_k = $row['c'];

$res_ku = $mysqli->query("SELECT COUNT(*) as c FROM kontrak_unit");
$row = $res_ku->fetch_assoc();
$total_ku = $row['c'];

echo "--- DATA DATABASE SAAT INI ---\n";
echo "Total Kontrak di DB: $total_k\n";
echo "Total Relasi Unit di DB (kontrak_unit): $total_ku\n\n";

// 2. Audit CSV data_mrktg.csv
$file = 'databases/Input_Data/data_mrktg.csv';
if (!file_exists($file)) {
	$file = '../databases/Input_Data/data_mrktg.csv';
}
if (!file_exists($file)) {
	$file = 'C:/laragon/www/optima/databases/Input_Data/data_mrktg.csv';
}

if (!file_exists($file)) {
    die("Cannot find data_mrktg.csv\n");
}

$handle = fopen($file, "r");
$header = fgetcsv($handle, 10000, ";"); // Assuming semicolon, might be comma

// detect delimiter
rewind($handle);
$line = fgets($handle);
$delimiter = strpos($line, ';') !== false ? ';' : ',';
rewind($handle);

$header = fgetcsv($handle, 10000, $delimiter);

$csv_rows = 0;
$distinct_kontrak_id = [];
$distinct_unit_id = [];
$empty_po = 0;

while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
    if (count($data) < 10) continue; // skip empty/invalid lines
    $csv_rows++;
    
    // Find column index
    $idx_kontrak_id = array_search('kontrak_id', $header);
    if($idx_kontrak_id === false && in_array('Kontrak', $header)) {
        $idx_kontrak_id = array_search('Kontrak', $header);
    }
    
    $idx_unit_id = array_search('unit_id', $header);
    if($idx_unit_id === false && in_array('No Unit', $header)) {
        $idx_unit_id = array_search('No Unit', $header);
    }
    
    $idx_po = array_search('No PO', $header);
    if($idx_po === false && in_array('PO', $header)) {
        $idx_po = array_search('PO', $header);
    }

    if ($idx_kontrak_id !== false && isset($data[$idx_kontrak_id])) {
        $k_id = trim($data[$idx_kontrak_id]);
        if($k_id) $distinct_kontrak_id[$k_id] = true;
    }
    
    if ($idx_unit_id !== false && isset($data[$idx_unit_id])) {
        $u_id = trim($data[$idx_unit_id]);
        if($u_id) $distinct_unit_id[$u_id] = true;
    }
    
    if ($idx_po !== false && isset($data[$idx_po])) {
        if(trim($data[$idx_po]) === '' || trim($data[$idx_po]) === '-') {
            $empty_po++;
        }
    }
}
fclose($handle);

echo "--- DATA DARI CSV (data_mrktg.csv) ---\n";
echo "Total Baris Data Valid: $csv_rows\n";
echo "Total Unik Kontrak ID: " . count($distinct_kontrak_id) . "\n";
echo "Total Unik Unit ID: " . count($distinct_unit_id) . "\n";
echo "Jumlah Baris Kolom PO Kosong: $empty_po\n";

$mysqli->close();
?>
