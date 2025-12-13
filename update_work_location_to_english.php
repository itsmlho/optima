<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'optima_ci');

echo "🔧 Updating work_location from Indonesian to English...\n\n";

try {
    // Update database values from PUSAT to CENTRAL
    $updateQuery = "UPDATE employees SET work_location = 'CENTRAL' WHERE work_location = 'PUSAT'";
    
    if ($mysqli->query($updateQuery)) {
        $affectedRows = $mysqli->affected_rows;
        echo "✅ Updated $affectedRows records from 'PUSAT' to 'CENTRAL'\n\n";
        
        // Update the ENUM column definition
        $alterQuery = "ALTER TABLE employees 
                       MODIFY COLUMN work_location ENUM('CENTRAL','AREA','BOTH') DEFAULT NULL";
        
        if ($mysqli->query($alterQuery)) {
            echo "✅ Updated work_location ENUM definition\n\n";
        } else {
            echo "❌ Failed to update ENUM: " . $mysqli->error . "\n";
        }
        
        // Show summary
        echo "📊 Current work_location distribution:\n";
        $summary = $mysqli->query("
            SELECT work_location, COUNT(*) as count 
            FROM employees 
            WHERE work_location IS NOT NULL
            GROUP BY work_location 
            ORDER BY work_location
        ");
        
        while ($row = $summary->fetch_assoc()) {
            echo "- {$row['work_location']}: {$row['count']} employees\n";
        }
        
    } else {
        echo "❌ Failed to update records: " . $mysqli->error . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Work location update completed!\n";
?>