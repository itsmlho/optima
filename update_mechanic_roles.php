<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'optima_ci');

echo "🔧 Updating existing mechanics to new role structure...\n\n";

// Get all mechanics
$result = $mysqli->query("SELECT * FROM employees WHERE staff_role = 'MECHANIC'");
$mechanics = $result->fetch_all(MYSQLI_ASSOC);

echo "Found " . count($mechanics) . " mechanics to update:\n";

$newRoles = ['MECHANIC_SERVICE_AREA', 'MECHANIC_UNIT_PREP', 'MECHANIC_FABRICATION'];
$workLocations = ['AREA', 'PUSAT', 'PUSAT']; // corresponding work locations
$descriptions = [
    'MECHANIC_SERVICE_AREA' => 'Mekanik Service Area - Bertanggung jawab untuk service, maintenance, dan perbaikan unit forklift di area yang ditugaskan',
    'MECHANIC_UNIT_PREP' => 'Mekanik Persiapan Unit - Bertanggung jawab untuk persiapan, setup, dan konfigurasi unit forklift baru sesuai spesifikasi SPK',
    'MECHANIC_FABRICATION' => 'Mekanik Fabrikasi - Bertanggung jawab untuk fabrikasi, modifikasi, dan persiapan attachment/aksesori forklift sesuai spesifikasi SPK'
];

foreach ($mechanics as $index => $mechanic) {
    // Randomly assign new role
    $roleIndex = $index % 3; // distribute evenly
    $newRole = $newRoles[$roleIndex];
    $workLocation = $workLocations[$roleIndex];
    $jobDescription = $descriptions[$newRole];
    
    $updateQuery = "UPDATE employees SET 
                    staff_role = '$newRole', 
                    work_location = '$workLocation',
                    job_description = '$jobDescription'
                    WHERE id = {$mechanic['id']}";
    
    if ($mysqli->query($updateQuery)) {
        echo "✅ Updated {$mechanic['staff_name']} ({$mechanic['staff_code']}) -> $newRole ($workLocation)\n";
    } else {
        echo "❌ Failed to update {$mechanic['staff_name']}: " . $mysqli->error . "\n";
    }
}

echo "\n📊 Summary after update:\n";
$summary = $mysqli->query("
    SELECT staff_role, work_location, COUNT(*) as count 
    FROM employees 
    WHERE staff_role LIKE 'MECHANIC%' 
    GROUP BY staff_role, work_location 
    ORDER BY staff_role
");

while ($row = $summary->fetch_assoc()) {
    echo "- {$row['staff_role']} ({$row['work_location']}): {$row['count']} employees\n";
}

echo "\n🎉 Mechanic role update completed!\n";
?>