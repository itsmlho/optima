<?php
// Test script to verify foreign key validation
require_once __DIR__ . '/system/bootstrap.php';

use Config\Database;

$db = Database::connect();

echo "Testing foreign key validation...\n\n";

// Test kapasitas_id = 12 (should be invalid)
$kapasitasQuery = $db->table('kapasitas')->where('id_kapasitas', 12)->get();
$kapasitasExists = $kapasitasQuery->getNumRows() > 0;
echo "kapasitas_id=12 exists: " . ($kapasitasExists ? 'YES' : 'NO') . "\n";

// Test mast_id = 15 (should be invalid)
$mastQuery = $db->table('tipe_mast')->where('id_mast', 15)->get();
$mastExists = $mastQuery->getNumRows() > 0;
echo "mast_id=15 exists: " . ($mastExists ? 'YES' : 'NO') . "\n";

// Test valid IDs
$kapasitasQuery2 = $db->table('kapasitas')->where('id_kapasitas', 1)->get();
$kapasitasExists2 = $kapasitasQuery2->getNumRows() > 0;
echo "kapasitas_id=1 exists: " . ($kapasitasExists2 ? 'YES' : 'NO') . "\n";

$mastQuery2 = $db->table('tipe_mast')->where('id_mast', 1)->get();
$mastExists2 = $mastQuery2->getNumRows() > 0;
echo "mast_id=1 exists: " . ($mastExists2 ? 'YES' : 'NO') . "\n";

echo "\nTest completed.\n";
?>
