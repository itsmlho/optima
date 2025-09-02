<?php

try {
    // Simple PDO connection
    $pdo = new PDO('mysql:host=localhost;dbname=optima_db;unix_socket=/opt/lampp/var/mysql/mysql.sock', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== SPK Table Diagnostics ===\n\n";
    
    // Check current records
    $stmt = $pdo->query("SELECT id, nomor_spk, status, dibuat_pada FROM spk ORDER BY id");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current SPK records:\n";
    foreach ($records as $record) {
        echo "ID: {$record['id']}, Nomor: {$record['nomor_spk']}, Status: {$record['status']}, Created: {$record['dibuat_pada']}\n";
    }
    
    // Check table status
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'spk'");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nTable Status:\n";
    echo "Auto_increment: " . ($status['Auto_increment'] ?? 'N/A') . "\n";
    echo "Engine: " . ($status['Engine'] ?? 'N/A') . "\n";
    
    // Get max ID
    $stmt = $pdo->query("SELECT MAX(id) as max_id FROM spk");
    $maxResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $maxId = $maxResult['max_id'] ?? 0;
    
    echo "Max ID in table: $maxId\n";
    
    // Check if auto_increment is correct
    $nextAutoIncrement = $maxId + 1;
    echo "Expected next auto_increment: $nextAutoIncrement\n";
    echo "Current auto_increment setting: " . ($status['Auto_increment'] ?? 'N/A') . "\n";
    
    // Fix auto_increment if needed
    if ($status['Auto_increment'] != $nextAutoIncrement) {
        echo "\nAuto_increment mismatch detected! Fixing...\n";
        $pdo->exec("ALTER TABLE spk AUTO_INCREMENT = $nextAutoIncrement");
        echo "Auto_increment fixed to $nextAutoIncrement\n";
        
        // Verify fix
        $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'spk'");
        $newStatus = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "New auto_increment value: " . ($newStatus['Auto_increment'] ?? 'N/A') . "\n";
    } else {
        echo "\nAuto_increment is correct.\n";
    }
    
    // Check for any records with ID = 0
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM spk WHERE id = 0");
    $zeroIdCount = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nRecords with ID = 0: " . ($zeroIdCount['count'] ?? 0) . "\n";
    
    if ($zeroIdCount['count'] > 0) {
        echo "Found records with ID = 0. These should be cleaned up.\n";
        
        // Show these records
        $stmt = $pdo->query("SELECT id, nomor_spk, status, dibuat_pada FROM spk WHERE id = 0");
        $zeroRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Records with ID = 0:\n";
        foreach ($zeroRecords as $record) {
            echo "ID: {$record['id']}, Nomor: {$record['nomor_spk']}, Status: {$record['status']}, Created: {$record['dibuat_pada']}\n";
        }
    }
    
    echo "\n=== Diagnostics Complete ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
