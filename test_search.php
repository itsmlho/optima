<?php
// Simple test for area search functionality
require_once 'vendor/autoload.php';

// Simulate a POST request with search
$_POST = [
    'search' => ['value' => 'Jakarta'],
    'start' => 0,
    'length' => 10,
    'draw' => 1
];

$_SERVER['REQUEST_METHOD'] = 'POST';

// Initialize CodeIgniter
$paths = new \Config\Paths();
$bootstrap = \CodeIgniter\Boot::bootWeb($paths);

echo "Test completed. Check logs for debug output.\n";
?>