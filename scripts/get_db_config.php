<?php
/**
 * Helper script to extract database config
 * Used by shell scripts to get database credentials
 */

// Read database config directly from file (avoid CodeIgniter dependencies)
$configFile = __DIR__ . '/../app/Config/Database.php';
$configContent = file_get_contents($configFile);

// Extract database config using regex
$config = [
    'hostname' => '127.0.0.1',
    'username' => 'root',
    'password' => 'root',
    'database' => 'optima_db',
    'port' => 3306,
];

// Parse config from PHP file
if (preg_match("/'hostname'\s*=>\s*['\"]([^'\"]+)['\"]/", $configContent, $matches)) {
    $config['hostname'] = $matches[1];
}
if (preg_match("/'username'\s*=>\s*['\"]([^'\"]+)['\"]/", $configContent, $matches)) {
    $config['username'] = $matches[1];
}
if (preg_match("/'password'\s*=>\s*['\"]([^'\"]+)['\"]/", $configContent, $matches)) {
    $config['password'] = $matches[1];
}
if (preg_match("/'database'\s*=>\s*['\"]([^'\"]+)['\"]/", $configContent, $matches)) {
    $config['database'] = $matches[1];
}
if (preg_match("/'port'\s*=>\s*(\d+)/", $configContent, $matches)) {
    $config['port'] = (int)$matches[1];
}

// Output as shell variables
echo "DB_HOST={$config['hostname']}\n";
echo "DB_PORT={$config['port']}\n";
echo "DB_USER={$config['username']}\n";
echo "DB_PASS={$config['password']}\n";
echo "DB_NAME={$config['database']}\n";

