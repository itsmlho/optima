<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP is working!<br>";

// Test database connection
try {
    $conn = new mysqli('127.0.0.1', 'root', 'root', 'optima_db');
    if ($conn->connect_error) {
        echo "Connection failed: " . $conn->connect_error;
    } else {
        echo "Database connection successful!<br>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Test CodeIgniter loading
echo "Testing CodeIgniter...<br>";

try {
    require_once '../app/Config/Paths.php';
    echo "Paths.php loaded successfully!<br>";
} catch (Exception $e) {
    echo "Error loading Paths.php: " . $e->getMessage() . "<br>";
}

try {
    $paths = new Config\Paths();
    echo "Paths object created successfully!<br>";
    echo "System directory: " . $paths->systemDirectory . "<br>";
    
    if (file_exists($paths->systemDirectory . '/Boot.php')) {
        echo "Boot.php exists!<br>";
    } else {
        echo "Boot.php NOT found at: " . $paths->systemDirectory . '/Boot.php<br>';
    }
} catch (Exception $e) {
    echo "Error with Paths: " . $e->getMessage() . "<br>";
}
?>
