<?php
// Test Work Orders System
require_once 'vendor/autoload.php';

// Basic database connection test
$host = 'localhost';
$dbname = 'optima_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Work Orders System Test</h2>";
    echo "<p>✅ Database connection successful</p>";
    
    // Check if work order tables exist
    $tables = [
        'work_orders',
        'work_order_categories', 
        'work_order_subcategories',
        'work_order_priorities',
        'work_order_statuses',
        'work_order_staff'
    ];
    
    echo "<h3>Table Status:</h3>";
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>✅ $table: {$result['count']} records</p>";
        } catch (Exception $e) {
            echo "<p>❌ $table: Error - {$e->getMessage()}</p>";
        }
    }
    
    // Test work orders view
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM work_orders_with_details LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>✅ work_orders_with_details view: Working</p>";
    } catch (Exception $e) {
        echo "<p>❌ work_orders_with_details view: Error - {$e->getMessage()}</p>";
    }
    
    // Check for sample data
    try {
        $stmt = $pdo->query("SELECT work_order_number, order_type, description FROM work_orders LIMIT 5");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($results) > 0) {
            echo "<h3>Sample Work Orders:</h3>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Work Order Number</th><th>Type</th><th>Description</th></tr>";
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>{$row['work_order_number']}</td>";
                echo "<td>{$row['order_type']}</td>";
                echo "<td>" . substr($row['description'], 0, 50) . "...</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>ℹ️ No work orders found in database</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Sample data query error: {$e->getMessage()}</p>";
    }
    
} catch (PDOException $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<br><hr>";
echo "<p><a href='service/work-orders'>🔗 Go to Work Orders Page</a></p>";
echo "<p><a href='http://localhost/phpmyadmin'>🔗 Open phpMyAdmin</a></p>";
?>