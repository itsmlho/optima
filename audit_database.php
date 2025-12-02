<?php

try {
    // Direct MySQL connection
    $mysqli = new mysqli('localhost', 'root', '', 'optima_ci');
    
    if ($mysqli->connect_error) {
        die('Connection failed: ' . $mysqli->connect_error);
    }
    
    echo "=== AUDIT OPTIMA_CI DATABASE STRUCTURE ===" . PHP_EOL;
    
    // Check kontrak table
    echo PHP_EOL . "1. KONTRAK TABLE:" . PHP_EOL;
    $result = $mysqli->query('DESCRIBE kontrak');
    if ($result) {
        while($row = $result->fetch_assoc()) {
            echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . $row['Default'] . PHP_EOL;
        }
    } else {
        echo "  Error: " . $mysqli->error . PHP_EOL;
    }
    
    // Check kontrak_spesifikasi table
    echo PHP_EOL . "2. KONTRAK_SPESIFIKASI TABLE:" . PHP_EOL;
    $result = $mysqli->query('DESCRIBE kontrak_spesifikasi');
    if ($result) {
        while($row = $result->fetch_assoc()) {
            echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . $row['Default'] . PHP_EOL;
        }
    } else {
        echo "  Error: " . $mysqli->error . PHP_EOL;
    }
    
    // Check customer_management table
    echo PHP_EOL . "3. CUSTOMER_MANAGEMENT TABLE:" . PHP_EOL;
    $result = $mysqli->query('DESCRIBE customer_management');
    if ($result) {
        while($row = $result->fetch_assoc()) {
            echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . $row['Default'] . PHP_EOL;
        }
    } else {
        echo "  Error: " . $mysqli->error . PHP_EOL;
    }
    
    // Check quotations table if exists
    echo PHP_EOL . "4. QUOTATIONS TABLE:" . PHP_EOL;
    $result = $mysqli->query('DESCRIBE quotations');
    if ($result) {
        while($row = $result->fetch_assoc()) {
            echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . $row['Default'] . PHP_EOL;
        }
    } else {
        echo "  Table 'quotations' does not exist or error: " . $mysqli->error . PHP_EOL;
    }
    
    // Sample data analysis
    echo PHP_EOL . "=== SAMPLE DATA ANALYSIS ===" . PHP_EOL;
    
    // Count kontrak records
    $result = $mysqli->query('SELECT COUNT(*) as total FROM kontrak');
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Total kontrak records: " . $row['total'] . PHP_EOL;
    }
    
    // Count kontrak_spesifikasi records
    $result = $mysqli->query('SELECT COUNT(*) as total FROM kontrak_spesifikasi');
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Total kontrak_spesifikasi records: " . $row['total'] . PHP_EOL;
    }
    
    // Show sample kontrak_spesifikasi fields to understand structure
    echo PHP_EOL . "Sample kontrak_spesifikasi data (first 3 records):" . PHP_EOL;
    $result = $mysqli->query('SELECT * FROM kontrak_spesifikasi LIMIT 3');
    if ($result) {
        while($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'];
            if (isset($row['kontrak_id'])) echo " | Kontrak ID: " . $row['kontrak_id'];
            if (isset($row['tipe_unit'])) echo " | Tipe: " . $row['tipe_unit'];
            if (isset($row['kapasitas'])) echo " | Kapasitas: " . $row['kapasitas'];
            echo PHP_EOL;
        }
    }
    
    // Check business flow relationships
    echo PHP_EOL . "=== BUSINESS FLOW ANALYSIS ===" . PHP_EOL;
    
    // Check foreign key relationships in kontrak_spesifikasi
    $result = $mysqli->query("SELECT DISTINCT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                              FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                              WHERE REFERENCED_TABLE_SCHEMA = 'optima_ci' 
                              AND TABLE_NAME IN ('kontrak_spesifikasi', 'kontrak', 'customer_management')");
    if ($result) {
        echo "Foreign Key Relationships:" . PHP_EOL;
        while($row = $result->fetch_assoc()) {
            echo "  " . $row['TABLE_NAME'] . "." . $row['COLUMN_NAME'] . " -> " . $row['REFERENCED_TABLE_NAME'] . "." . $row['REFERENCED_COLUMN_NAME'] . PHP_EOL;
        }
    }
    
    $mysqli->close();
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>