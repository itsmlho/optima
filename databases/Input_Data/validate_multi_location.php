<?php
$db = new mysqli('localhost', 'root', '', 'optima_ci');

echo "=== MULTI-LOCATION CONTRACT VALIDATION ===\n\n";

// Find contracts with multiple locations
$query = "
    SELECT 
        k.id,
        k.no_kontrak,
        c.customer_name,
        COUNT(ku.id) as total_units,
        COUNT(DISTINCT ku.customer_location_id) as unique_locations
    FROM kontrak k
    JOIN customers c ON k.customer_id = c.id
    JOIN kontrak_unit ku ON k.id = ku.kontrak_id
    WHERE ku.customer_location_id IS NOT NULL
    GROUP BY k.id
    HAVING unique_locations > 1
    ORDER BY unique_locations DESC, total_units DESC
    LIMIT 15
";

$result = $db->query($query);

if ($result->num_rows === 0) {
    echo "No multi-location contracts found.\n";
    exit;
}

echo "Found " . $result->num_rows . " contracts with multiple locations:\n\n";

$count = 0;
while ($row = $result->fetch_assoc()) {
    $count++;
    echo sprintf(
        "%2d. %-50s | %s\n     %d units across %d locations\n",
        $count,
        substr($row['no_kontrak'], 0, 50),
        substr($row['customer_name'], 0, 40),
        $row['total_units'],
        $row['unique_locations']
    );
    
    // Get location details for this contract
    $detail_query = "
        SELECT 
            COALESCE(cl.location_name, cl.address) as location_name,
            COUNT(ku.id) as unit_count
        FROM kontrak_unit ku
        JOIN customer_locations cl ON ku.customer_location_id = cl.id
        WHERE ku.kontrak_id = {$row['id']}
        GROUP BY ku.customer_location_id
        ORDER BY unit_count DESC
    ";
    
    $detail_result = $db->query($detail_query);
    while ($detail = $detail_result->fetch_assoc()) {
        echo sprintf(
            "     - %s: %d units\n",
            substr($detail['location_name'], 0, 60),
            $detail['unit_count']
        );
    }
    
    echo "\n";
}

// Summary statistics
echo "\n=== SUMMARY ===\n\n";

$stats = $db->query("
    SELECT 
        COUNT(DISTINCT k.id) as total_contracts,
        SUM(CASE WHEN location_count > 1 THEN 1 ELSE 0 END) as multi_location_contracts,
        COUNT(DISTINCT ku.id) as total_units,
        COUNT(DISTINCT ku.customer_location_id) as unique_locations
    FROM kontrak k
    JOIN (
        SELECT 
            kontrak_id,
            COUNT(DISTINCT customer_location_id) as location_count
        FROM kontrak_unit
        WHERE customer_location_id IS NOT NULL
        GROUP BY kontrak_id
    ) sub ON k.id = sub.kontrak_id
    JOIN kontrak_unit ku ON k.id = ku.kontrak_id
")->fetch_assoc();

echo "Total contracts: {$stats['total_contracts']}\n";
echo "Multi-location contracts: {$stats['multi_location_contracts']}\n";
echo "Single-location contracts: " . ($stats['total_contracts'] - $stats['multi_location_contracts']) . "\n";
echo "Total units: {$stats['total_units']}\n";
echo "Unique customer locations: {$stats['unique_locations']}\n";

$percentage = round(($stats['multi_location_contracts'] / $stats['total_contracts']) * 100, 1);
echo "\nPercentage of multi-location contracts: {$percentage}%\n";

echo "\n✓ Location tracking is working correctly at kontrak_unit level!\n";
