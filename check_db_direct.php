<?php
// Simple direct MySQL connection
$host = 'localhost';
$db   = 'optima_ci';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking inventory_unit table structure...\n";
    echo str_repeat("=", 80) . "\n\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM inventory_unit");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total columns: " . count($columns) . "\n\n";
    
    $hasKontrakSpesifikasiId = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'kontrak_spesifikasi_id') {
            $hasKontrakSpesifikasiId = true;
            echo "✓ FOUND kontrak_spesifikasi_id:\n";
            print_r($col);
        }
    }
    
    if (!$hasKontrakSpesifikasiId) {
        echo "✗ kontrak_spesifikasi_id DOES NOT EXIST!\n\n";
        echo "This is the problem! The trigger tr_inventory_unit_bi tries to access\n";
        echo "NEW.kontrak_spesifikasi_id but the column doesn't exist in the table.\n\n";
        
        echo "Solution: Either add the column or modify the trigger.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
