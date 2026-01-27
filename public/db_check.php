<?php

// Basic constants
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// 1. Manually parse .env file to be absolutely sure what is in it
$envFile = FCPATH . '../.env';
$envContent = file_get_contents($envFile);
preg_match('/database.default.password\s*=\s*(.*)/', $envContent, $matches);
$rawPassword = $matches[1] ?? 'NOT FOUND';

// 2. Load CI Classes to see what the framework sees
require FCPATH . '../app/Config/Paths.php';
$paths = new \Config\Paths();
require $paths->systemDirectory . '/Config/DotEnv.php';

$dotenv = new \CodeIgniter\Config\DotEnv(FCPATH . '../');
$dotenv->load();

$ciPassword = getenv('database.default.password');

// 3. Output results
echo "<h1>Debug .env</h1>";
echo "<h3>Raw .env Parser</h3>";
echo "Raw Password Value in file: [" . $rawPassword . "]<br>";
echo "Length: " . strlen($rawPassword) . "<br>";
echo "Hex dump: " . bin2hex($rawPassword) . "<br>";

echo "<h3>CI DotEnv Parser</h3>";
echo "CI Password Value: [" . $ciPassword . "]<br>";
echo "Length: " . strlen($ciPassword) . "<br>";
echo "Hex dump: " . bin2hex($ciPassword) . "<br>";

$host = getenv('database.default.hostname');
$user = getenv('database.default.username');
$db   = getenv('database.default.database');

echo "<h3>Connection Test</h3>";
echo "Connecting to $host as $user with password [" . $ciPassword . "]...<br>";

$mysqli = new mysqli($host, $user, $ciPassword, $db);

if ($mysqli->connect_error) {
    echo "<div style='color: red; font-weight: bold'>Connection Failed: " . $mysqli->connect_error . "</div>";
} else {
    echo "<div style='color: green; font-weight: bold'>Connection Successful!</div>";
}
