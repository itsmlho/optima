<?php
// Add kontrak_spesifikasi_id column to inventory_unit table

$host = 'localhost';
$db   = 'optima_ci';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding kontrak_spesifikasi_id column to inventory_unit table...\n";
    echo str_repeat("=", 80) . "\n\n";
    
    // Check if column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM inventory_unit LIKE 'kontrak_spesifikasi_id'");
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "Column 'kontrak_spesifikasi_id' already exists!\n";
        exit(0);
    }
    
    // Add the column after kontrak_id
    $sql = "ALTER TABLE inventory_unit 
            ADD COLUMN kontrak_spesifikasi_id INT(10) UNSIGNED NULL 
            COMMENT 'FK ke kontrak_spesifikasi' 
            AFTER kontrak_id";
    
    $pdo->exec($sql);
    
    echo "✓ Successfully added column 'kontrak_spesifikasi_id'\n";
    echo "\nVerifying...\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM inventory_unit LIKE 'kontrak_spesifikasi_id'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($column) {
        echo "✓ Verified! Column structure:\n";
        print_r($column);
    }
    
    echo "\n✓ Done! You can now retry the warehouse verification.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
