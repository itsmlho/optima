<?php
// Bootstrap CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require_once FCPATH . '../vendor/autoload.php';

// Load the framework
$app = require_once FCPATH . '../app/Config/Paths.php';
require_once rtrim($app->systemDirectory, '\\/ ') . '/bootstrap.php';

$app = \Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

// Get table structure
echo "=== QUOTATION_SPECIFICATIONS TABLE STRUCTURE ===\n";
$query = $db->query('DESCRIBE quotation_specifications');
$fields = $query->getResultArray();

foreach ($fields as $f) {
    echo $f['Field'] . " (" . $f['Type'] . ")\n";
}

echo "\n=== SAMPLE DATA ===\n";
$data = $db->query('SELECT * FROM quotation_specifications WHERE id_quotation = 5 LIMIT 1')->getRowArray();
if ($data) {
    print_r($data);
} else {
    echo "No data found for quotation ID 5\n";
}

echo "\n=== COUNT ===\n";
$count = $db->query('SELECT COUNT(*) as total FROM quotation_specifications WHERE id_quotation = 5')->getRow();
echo "Total specifications for quotation 5: " . $count->total . "\n";
