<?php
// Debug script untuk melihat query yang error
// Akses: http://localhost/optima/public/debug_di_query.php?id=148

$id = $_GET['id'] ?? 148;

require_once '../vendor/autoload.php';

$app = \Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

// Enable query logging
$db->enableQueryLog();

try {
    // Coba query yang sama seperti di diPrint
    echo "<h3>Testing Query for DI #{$id}</h3>";
    
    $query = $db->table('delivery_items di')
        ->select('di.*, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit')
        ->select('ia.sn_attachment, att.tipe as att_tipe, att.merk as att_merk, att.model as att_model')
        ->join('inventory_unit iu','iu.id_inventory_unit = di.unit_id','left')
        ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
        ->join('inventory_attachment ia', 'ia.id_inventory_attachment = di.attachment_id', 'left')
        ->join('attachment att', 'att.id_attachment = ia.attachment_id', 'left')
        ->where('di.di_id', $id)
        ->getCompiledSelect();
        
    echo "<h4>Generated SQL:</h4>";
    echo "<pre>" . htmlspecialchars($query) . "</pre>";
    
    // Now actually execute
    $db->table('delivery_items di')
        ->select('di.*, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit')
        ->select('ia.sn_attachment, att.tipe as att_tipe, att.merk as att_merk, att.model as att_model')
        ->join('inventory_unit iu','iu.id_inventory_unit = di.unit_id','left')
        ->join('model_unit mu','mu.id_model_unit = iu.model_unit_id','left')
        ->join('inventory_attachment ia', 'ia.id_inventory_attachment = di.attachment_id', 'left')
        ->join('attachment att', 'att.id_attachment = ia.attachment_id', 'left')
        ->where('di.di_id', $id)
        ->get()->getResultArray();
        
    echo "<h4 style='color: green;'>✅ Query executed successfully!</h4>";
    
} catch (Exception $e) {
    echo "<h4 style='color: red;'>❌ Error:</h4>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h3>Query Log:</h3>";
echo "<pre>";
print_r($db->getQueryLog());
echo "</pre>";
?>
