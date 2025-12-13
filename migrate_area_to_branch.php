<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'optima_ci');

echo "🔧 Updating work_location from AREA to BRANCH for English consistency...\n\n";

try {
    // Step 1: Update the ENUM column definition to include BRANCH
    echo "Step 1: Updating ENUM to include BRANCH...\n";
    $alterQuery1 = "ALTER TABLE employees 
                    MODIFY COLUMN work_location ENUM('CENTRAL','AREA','BRANCH','BOTH') DEFAULT NULL";
    
    if ($mysqli->query($alterQuery1)) {
        echo "✅ Updated ENUM to include BRANCH\n\n";
        
        // Step 2: Update data from AREA to BRANCH
        echo "Step 2: Updating data from 'AREA' to 'BRANCH'...\n";
        $updateQuery = "UPDATE employees SET work_location = 'BRANCH' WHERE work_location = 'AREA'";
        
        if ($mysqli->query($updateQuery)) {
            $affectedRows = $mysqli->affected_rows;
            echo "✅ Updated $affectedRows records from 'AREA' to 'BRANCH'\n\n";
            
            // Step 3: Remove AREA from ENUM
            echo "Step 3: Removing 'AREA' from ENUM...\n";
            $alterQuery2 = "ALTER TABLE employees 
                           MODIFY COLUMN work_location ENUM('CENTRAL','BRANCH','BOTH') DEFAULT NULL";
            
            if ($mysqli->query($alterQuery2)) {
                echo "✅ Removed 'AREA' from ENUM definition\n\n";
            } else {
                echo "❌ Failed to remove AREA from ENUM: " . $mysqli->error . "\n";
            }
            
            // Step 4: Update job descriptions to use BRANCH terminology
            echo "Step 4: Updating job descriptions to use BRANCH terminology...\n";
            $jobUpdates = [
                'ADMIN' => 'Administrator - Mengelola operasional administrasi, dokumentasi, dan koordinasi dengan berbagai departemen. Dapat bekerja di central office maupun branch office.',
                'SUPERVISOR' => 'Supervisor - Mengawasi dan mengkoordinir aktivitas operasional serta memastikan target kinerja tercapai. Bertanggung jawab terhadap manajemen tim di branch yang ditugaskan.',
                'MECHANIC_SERVICE_AREA' => 'Mechanic Service Branch - Bertanggung jawab untuk service, maintenance, dan perbaikan unit forklift di branch yang ditugaskan. Melakukan perjalanan ke lokasi customer untuk service on-site.',
                'HELPER' => 'Helper - Membantu aktivitas teknis dan operasional, mendukung mechanic dalam pekerjaan service dan maintenance. Dapat ditempatkan di central office atau branch office sesuai kebutuhan.'
            ];
            
            foreach ($jobUpdates as $role => $description) {
                $stmt = $mysqli->prepare("UPDATE employees SET job_description = ? WHERE staff_role = ?");
                $stmt->bind_param("ss", $description, $role);
                if ($stmt->execute()) {
                    echo "✅ Updated job description for $role\n";
                }
                $stmt->close();
            }
            
            // Show summary
            echo "\n📊 Current work_location distribution:\n";
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

echo "\n🎉 Work location migration to BRANCH completed!\n";
?>