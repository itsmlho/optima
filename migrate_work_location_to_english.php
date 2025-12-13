<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'optima_ci');

echo "🔧 Updating work_location ENUM and data from Indonesian to English...\n\n";

try {
    // First, update the ENUM column definition to include both old and new values
    echo "Step 1: Updating ENUM to include both old and new values...\n";
    $alterQuery1 = "ALTER TABLE employees 
                    MODIFY COLUMN work_location ENUM('PUSAT','CENTRAL','AREA','BOTH') DEFAULT NULL";
    
    if ($mysqli->query($alterQuery1)) {
        echo "✅ Updated ENUM to include both values\n\n";
        
        // Step 2: Update data from PUSAT to CENTRAL
        echo "Step 2: Updating data from 'PUSAT' to 'CENTRAL'...\n";
        $updateQuery = "UPDATE employees SET work_location = 'CENTRAL' WHERE work_location = 'PUSAT'";
        
        if ($mysqli->query($updateQuery)) {
            $affectedRows = $mysqli->affected_rows;
            echo "✅ Updated $affectedRows records from 'PUSAT' to 'CENTRAL'\n\n";
            
            // Step 3: Remove PUSAT from ENUM
            echo "Step 3: Removing 'PUSAT' from ENUM...\n";
            $alterQuery2 = "ALTER TABLE employees 
                           MODIFY COLUMN work_location ENUM('CENTRAL','AREA','BOTH') DEFAULT NULL";
            
            if ($mysqli->query($alterQuery2)) {
                echo "✅ Removed 'PUSAT' from ENUM definition\n\n";
            } else {
                echo "❌ Failed to remove PUSAT from ENUM: " . $mysqli->error . "\n";
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
        
    } else {
        echo "❌ Failed to update ENUM: " . $mysqli->error . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Work location migration to English completed!\n";
?>