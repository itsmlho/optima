<?php

// Initialize CodeIgniter
require_once __DIR__ . '/vendor/codeigniter4/framework/system/bootstrap.php';

$app = \Config\Services::codeigniter();
$app->initialize();

try {
    $db = \Config\Database::connect();
    $query = $db->query('SHOW TABLES LIKE "users"');
    if ($query->getNumRows() > 0) {
        echo 'Users table exists' . PHP_EOL;
        $userQuery = $db->query('SELECT COUNT(*) as count FROM users WHERE status = "active"');
        $result = $userQuery->getRow();
        echo 'Active users count: ' . $result->count . PHP_EOL;
        
        // Also check table structure
        $structure = $db->query('DESCRIBE users');
        echo 'Users table columns:' . PHP_EOL;
        foreach ($structure->getResultArray() as $column) {
            echo ' - ' . $column['Field'] . ' (' . $column['Type'] . ')' . PHP_EOL;
        }
    } else {
        echo 'Users table does not exist' . PHP_EOL;
        $tablesQuery = $db->query('SHOW TABLES');
        echo 'Available tables:' . PHP_EOL;
        foreach ($tablesQuery->getResultArray() as $table) {
            echo ' - ' . array_values($table)[0] . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo 'Database error: ' . $e->getMessage() . PHP_EOL;
}