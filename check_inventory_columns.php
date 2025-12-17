<?php
// Bootstrap CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require_once __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';

$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();
$result = $db->query('SHOW COLUMNS FROM inventory_unit');
$columns = $result->getResultArray();

echo "Columns in inventory_unit table:\n";
echo str_repeat("=", 80) . "\n";

foreach ($columns as $col) {
    echo sprintf("%-30s %-20s %s\n", 
        $col['Field'], 
        $col['Type'], 
        $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
    );
}

echo "\n";
echo "Looking for 'kontrak_spesifikasi_id':\n";
$hasColumn = false;
foreach ($columns as $col) {
    if ($col['Field'] === 'kontrak_spesifikasi_id') {
        $hasColumn = true;
        echo "✓ FOUND: kontrak_spesifikasi_id\n";
        print_r($col);
        break;
    }
}

if (!$hasColumn) {
    echo "✗ NOT FOUND: kontrak_spesifikasi_id column does not exist!\n";
    echo "\nThis is causing the error: 'Unknown column kontrak_spesifikasi_id in NEW'\n";
    echo "The trigger tr_inventory_unit_bi references this column but it doesn't exist.\n";
}
