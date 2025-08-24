<?php
// Simple script to check database structure

try {
    $pdo = new PDO('mysql:host=localhost;dbname=optima_db', 'root', '');
    
    // List all tables
    echo "<h2>Database Tables:</h2>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Check for spk table (not marketing_spk)
    if (in_array('spk', $tables)) {
        echo "<h3>spk Table Structure:</h3>";
        $structure = $pdo->query("DESCRIBE spk");
        echo "<pre>";
        print_r($structure->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";
        
        // Get sample data
        $rows = $pdo->query("SELECT * FROM spk LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Sample SPK Data:</h3>";
        echo "<pre>";
        print_r($rows);
        echo "</pre>";
    }
    
    // Check for kontrak_spesifikasi table
    if (in_array('kontrak_spesifikasi', $tables)) {
        echo "<h3>kontrak_spesifikasi Structure:</h3>";
        $structure = $pdo->query("DESCRIBE kontrak_spesifikasi");
        echo "<pre>";
        print_r($structure->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";
        
        // Get sample data
        $rows = $pdo->query("SELECT * FROM kontrak_spesifikasi LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Sample Data:</h3>";
        echo "<pre>";
        print_r($rows);
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
