<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'optima_ci');

echo "🔧 Updating job descriptions to use English terminology...\n\n";

try {
    $jobDescriptions = [
        'ADMIN' => 'Administrator - Mengelola operasional administrasi, dokumentasi, dan koordinasi dengan berbagai departemen. Dapat bekerja di central office maupun area branch.',
        'SUPERVISOR' => 'Supervisor - Mengawasi dan mengkoordinir aktivitas operasional serta memastikan target kinerja tercapai. Bertanggung jawab terhadap manajemen tim di area yang ditugaskan.',
        'FOREMAN' => 'Foreman - Memimpin tim teknis, mengawasi pekerjaan lapangan, dan bertanggung jawab terhadap kualitas hasil kerja. Mengkoordinir aktivitas harian tim mekanik.',
        'MECHANIC_SERVICE_AREA' => 'Mechanic Service Area - Bertanggung jawab untuk service, maintenance, dan perbaikan unit forklift di area/branch yang ditugaskan. Melakukan perjalanan ke lokasi customer untuk service on-site.',
        'MECHANIC_UNIT_PREP' => 'Mechanic Unit Preparation - Bertanggung jawab untuk persiapan, setup, dan konfigurasi unit forklift baru sesuai spesifikasi SPK. Bekerja di central workshop untuk memastikan unit siap kirim.',
        'MECHANIC_FABRICATION' => 'Mechanic Fabrication - Bertanggung jawab untuk fabrikasi, modifikasi, dan persiapan attachment/aksesori forklift sesuai spesifikasi SPK. Menguasai teknik pengelasan dan machining di central workshop.',
        'HELPER' => 'Helper - Membantu aktivitas teknis dan operasional, mendukung mechanic dalam pekerjaan service dan maintenance. Dapat ditempatkan di central office atau area branch sesuai kebutuhan.'
    ];
    
    foreach ($jobDescriptions as $role => $description) {
        $stmt = $mysqli->prepare("UPDATE employees SET job_description = ? WHERE staff_role = ?");
        $stmt->bind_param("ss", $description, $role);
        
        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            echo "✅ Updated job description for $role ($affected employees)\n";
        } else {
            echo "❌ Failed to update $role: " . $stmt->error . "\n";
        }
        $stmt->close();
    }
    
    echo "\n📊 Summary of updated job descriptions:\n";
    $result = $mysqli->query("
        SELECT staff_role, COUNT(*) as count 
        FROM employees 
        WHERE job_description IS NOT NULL AND job_description != ''
        GROUP BY staff_role 
        ORDER BY staff_role
    ");
    
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['staff_role']}: {$row['count']} employees with job description\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Job descriptions updated to use English terminology!\n";
?>