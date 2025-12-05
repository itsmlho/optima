<!DOCTYPE html>
<html>
<head>
    <title>Test Database Queries</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
        h3 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
    </style>
</head>
<body>
    <h1>Database Query Test</h1>

<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Get database configuration
$config = new \Config\Database();
$db = \Config\Database::connect();

echo "<h3>1. Check Quotation ID 5</h3>";
try {
    $quotation = $db->table('quotations')
        ->where('id_quotation', 5)
        ->get()
        ->getRowArray();
    
    if ($quotation) {
        echo "<pre>";
        echo "Quotation found:\n";
        echo "ID: " . $quotation['id_quotation'] . "\n";
        echo "Number: " . ($quotation['quotation_number'] ?? 'N/A') . "\n";
        echo "Customer ID: " . ($quotation['created_customer_id'] ?? 'N/A') . "\n";
        echo "Contract ID: " . ($quotation['created_contract_id'] ?? 'N/A') . "\n";
        echo "</pre>";
    } else {
        echo "<pre>Quotation ID 5 not found</pre>";
    }
} catch (\Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>";
}

echo "<h3>2. Check Quotation Specifications</h3>";
try {
    $specs = $db->table('quotation_specifications')
        ->where('id_quotation', 5)
        ->get()
        ->getResultArray();
    
    echo "<pre>";
    echo "Found " . count($specs) . " specifications\n";
    if (!empty($specs)) {
        echo "\nFirst specification:\n";
        print_r($specs[0]);
    }
    echo "</pre>";
} catch (\Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>";
}

echo "<h3>3. Test Join Query (same as getSpecifications)</h3>";
try {
    $specifications = $db->table('quotation_specifications qs')
        ->select('qs.id,
                  qs.id_quotation,
                  qs.quantity,
                  qs.merk_unit,
                  qs.model_unit,
                  d.nama_departemen, 
                  tu.nama_tipe_unit, 
                  k.nama_kapasitas')
        ->join('departemen d', 'd.id_departemen = qs.id_departemen', 'left')
        ->join('tipe_unit tu', 'tu.id_tipe_unit = qs.id_tipe_unit', 'left')
        ->join('kapasitas k', 'k.id_kapasitas = qs.id_kapasitas', 'left')
        ->where('qs.id_quotation', 5)
        ->get()
        ->getResultArray();
    
    echo "<pre>";
    echo "Found " . count($specifications) . " specifications with joins\n";
    if (!empty($specifications)) {
        echo "\nFirst specification:\n";
        print_r($specifications[0]);
    }
    echo "</pre>";
} catch (\Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>";
}

echo "<h3>4. Check Table Structure</h3>";
try {
    $fields = $db->query('SHOW COLUMNS FROM quotation_specifications')->getResultArray();
    echo "<pre>";
    echo "Columns in quotation_specifications:\n";
    foreach ($fields as $field) {
        echo "- " . $field['Field'] . " (" . $field['Type'] . ")\n";
    }
    echo "</pre>";
} catch (\Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>";
}
?>

</body>
</html>
