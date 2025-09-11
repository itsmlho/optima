<?php
// Include the CodeIgniter bootstrap
require_once dirname(__DIR__) . '/system/bootstrap.php';

// Initialize the framework
$app = new \CodeIgniter\CodeIgniter(dirname(__DIR__));
$app->initialize();

// Test the SystemActivityLogModel
$systemActivityModel = new \App\Models\SystemActivityLogModel();

$testData = [
    'table_name' => 'kontrak',
    'record_id' => 99,
    'action_type' => 'DELETE', 
    'action_description' => 'Test delete logging',
    'old_values' => '{"no_kontrak":"TEST"}',
    'affected_fields' => '["no_kontrak"]',
    'user_id' => 1
];

echo "Testing SystemActivityLogModel insert...\n";

try {
    $result = $systemActivityModel->insert($testData);
    echo "Insert result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    if (!$result) {
        echo "Model errors: " . json_encode($systemActivityModel->errors()) . "\n";
    } else {
        echo "Insert ID: " . $systemActivityModel->getInsertID() . "\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
