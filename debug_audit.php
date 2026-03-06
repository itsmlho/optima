<?php
// Quick test script to debug the query issue
require_once 'vendor/autoload.php';

// Bootstrap CodeIgniter
$paths = new Config\Paths();
chdir(__DIR__);

$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

// Check if tables exist
echo "=== Checking tables ===\n";
$result = $db->query("SHOW TABLES LIKE 'unit_audit_requests'");
echo "unit_audit_requests exists: " . ($result->getNumRows() > 0 ? "YES" : "NO") . "\n";

$result = $db->query("SHOW TABLES LIKE 'unit_movements'");
echo "unit_movements exists: " . ($result->getNumRows() > 0 ? "YES" : "NO") . "\n";

// Check users table columns
echo "\n=== Users table columns ===\n";
$result = $db->query("SHOW COLUMNS FROM users");
foreach ($result->getResultArray() as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}

// Check model_unit table columns
echo "\n=== Model_unit table columns ===\n";
$result = $db->query("SHOW COLUMNS FROM model_unit");
foreach ($result->getResultArray() as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}

// Try the actual query to see the SQL error
echo "\n=== Testing query ===\n";
$builder = $db->table('unit_audit_requests uar');
$builder->select('uar.*,
    iu.no_unit,
    iu.no_unit_na,
    iu.serial_number,
    iu.lokasi_unit as unit_current_location,
    iu.status_unit_id,
    su.status_unit as status_unit_name,
    mu.merk_unit,
    mu.model_unit,
    tu.tipe as tipe_unit,
    reporter.name as reporter_name,
    approver.name as approver_name');
$builder->join('inventory_unit iu', 'uar.unit_id = iu.id_inventory_unit', 'left');
$builder->join('status_unit su', 'iu.status_unit_id = su.id_status', 'left');
$builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
$builder->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left');
$builder->join('users reporter', 'uar.reported_by_user_id = reporter.id', 'left');
$builder->join('users approver', 'uar.approved_by_user_id = approver.id', 'left');
$builder->orderBy('uar.created_at', 'DESC');

$result = $builder->get();
if ($result === false) {
    echo "Query FAILED!\n";
    echo "Last query: " . $db->getLastQuery() . "\n";
    echo "Error: " . print_r($db->error(), true) . "\n";
} else {
    echo "Query OK! Rows: " . $result->getNumRows() . "\n";
}
