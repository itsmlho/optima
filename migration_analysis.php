<?php

$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "=== MIGRATION ANALYSIS: KONTRAK_SPESIFIKASI -> QUOTATION_SPECIFICATIONS ===" . PHP_EOL;

// Analyze field mapping between kontrak_spesifikasi and quotation_specifications
echo PHP_EOL . "=== FIELD MAPPING ANALYSIS ===" . PHP_EOL;

// Get kontrak_spesifikasi structure
$ks_fields = [];
$result = $mysqli->query('DESCRIBE kontrak_spesifikasi');
while($row = $result->fetch_assoc()) {
    $ks_fields[] = $row['Field'];
}

// Get quotation_specifications structure  
$qs_fields = [];
$result = $mysqli->query('DESCRIBE quotation_specifications');
while($row = $result->fetch_assoc()) {
    $qs_fields[] = $row['Field'];
}

echo "KONTRAK_SPESIFIKASI fields (" . count($ks_fields) . "):" . PHP_EOL;
foreach($ks_fields as $field) {
    echo "  " . $field . PHP_EOL;
}

echo PHP_EOL . "QUOTATION_SPECIFICATIONS fields (" . count($qs_fields) . "):" . PHP_EOL;
foreach($qs_fields as $field) {
    echo "  " . $field . PHP_EOL;
}

// Analyze potential field mappings
echo PHP_EOL . "=== SUGGESTED FIELD MAPPINGS ===" . PHP_EOL;

$mappings = [
    // Direct mappings
    'kontrak_id' => 'id_quotation (via new quotation)',
    'spek_kode' => 'specification_name',
    'jumlah_dibutuhkan' => 'quantity', 
    'harga_per_unit_bulanan' => 'unit_price',
    'catatan_spek' => 'notes',
    'tipe_jenis' => 'equipment_type',
    'merk_unit' => 'brand',
    'model_unit' => 'model',
    
    // Complex mappings that need logic
    'departemen_id + tipe_unit_id + kapasitas_id' => 'category (derived)',
    'attachment_tipe + attachment_merk' => 'specifications (JSON)',
    'jenis_baterai + charger_id + mast_id + ban_id + roda_id + valve_id' => 'specifications (JSON)',
    'aksesoris' => 'specifications (append to JSON)',
    
    // New fields in quotation_specifications
    'rental_rate_type' => 'MONTHLY (default from jenis_sewa)',
    'service_duration' => 'calculated from tanggal_mulai - tanggal_berakhir',
    'delivery_required' => 'TRUE (default)',
    'installation_required' => 'TRUE (default)',
    'maintenance_included' => 'TRUE (default)',
    'warranty_period' => '12 (default months)'
];

foreach($mappings as $source => $target) {
    echo "  " . $source . " -> " . $target . PHP_EOL;
}

// Check lookup tables for proper mapping
echo PHP_EOL . "=== LOOKUP TABLES FOR MAPPING ===" . PHP_EOL;

$lookup_tables = ['departemen', 'tipe_unit', 'kapasitas', 'charger', 'tipe_mast', 'tipe_ban', 'valve'];

foreach($lookup_tables as $table) {
    $result = $mysqli->query("SELECT COUNT(*) as total FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "$table: " . $row['total'] . " records" . PHP_EOL;
        
        // Show sample data
        $result2 = $mysqli->query("SELECT * FROM $table LIMIT 3");
        if ($result2) {
            while($sample = $result2->fetch_assoc()) {
                $keys = array_keys($sample);
                echo "  Sample: " . $sample[$keys[1]] . " (ID: " . $sample[$keys[0]] . ")" . PHP_EOL;
                break; // Just show one sample
            }
        }
    }
}

// Check existing kontrak to understand the conversion process
echo PHP_EOL . "=== SAMPLE KONTRAK WITH SPECIFICATIONS ===" . PHP_EOL;

$result = $mysqli->query("
    SELECT 
        k.id as kontrak_id,
        k.no_kontrak,
        k.nilai_total,
        k.jenis_sewa,
        k.tanggal_mulai,
        k.tanggal_berakhir,
        cl.location_name,
        c.customer_name,
        COUNT(ks.id) as total_specs
    FROM kontrak k 
    LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
    LEFT JOIN customers c ON cl.customer_id = c.id  
    LEFT JOIN kontrak_spesifikasi ks ON k.id = ks.kontrak_id
    GROUP BY k.id
    LIMIT 5
");

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "Kontrak #" . $row['kontrak_id'] . ": " . $row['no_kontrak'] . PHP_EOL;
        echo "  Customer: " . $row['customer_name'] . " | Location: " . $row['location_name'] . PHP_EOL;
        echo "  Value: " . $row['nilai_total'] . " | Type: " . $row['jenis_sewa'] . PHP_EOL;
        echo "  Period: " . $row['tanggal_mulai'] . " to " . $row['tanggal_berakhir'] . PHP_EOL;
        echo "  Specifications: " . $row['total_specs'] . " items" . PHP_EOL;
        echo PHP_EOL;
    }
}

$mysqli->close();

?>