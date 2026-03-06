<?php
// Helper script untuk list customers dengan locations
$db = new mysqli('localhost', 'root', '', 'optima_ci');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$filter = $argv[1] ?? '';

echo "=== CUSTOMER & LOCATION LIST ===\n";
if (!empty($filter)) {
    echo "Filter: '$filter'\n";
}
echo "\n";

$sql = "SELECT 
    c.id as customer_id,
    c.customer_name,
    c.customer_code,
    COUNT(DISTINCT k.id) as contract_count,
    COUNT(DISTINCT cl.id) as location_count
FROM customers c
LEFT JOIN kontrak k ON c.id = k.customer_id
LEFT JOIN customer_locations cl ON c.id = cl.customer_id
WHERE 1=1";

if (!empty($filter)) {
    $filter = $db->real_escape_string($filter);
    $sql .= " AND (
        c.customer_name LIKE '%$filter%' 
        OR c.customer_code LIKE '%$filter%'
    )";
}

$sql .= " GROUP BY c.id ORDER BY c.customer_name";

$result = $db->query($sql);

if ($result->num_rows === 0) {
    echo "No customers found.\n";
    exit;
}

$count = 0;
while ($row = $result->fetch_object()) {
    $count++;
    
    echo sprintf(
        "\n[%d] %s (code: %s)\n",
        $row->customer_id,
        $row->customer_name,
        $row->customer_code ?? 'N/A'
    );
    echo "  Contracts: {$row->contract_count}  |  Locations: {$row->location_count}\n";
    
    // Get locations for this customer
    $loc_result = $db->query("
        SELECT id, location_name, location_code, location_type, city, is_primary
        FROM customer_locations
        WHERE customer_id = {$row->customer_id}
        ORDER BY is_primary DESC, location_name
    ");
    
    if ($loc_result->num_rows > 0) {
        echo "  Locations:\n";
        while ($loc = $loc_result->fetch_object()) {
            $primary = $loc->is_primary ? " ★PRIMARY" : "";
            echo sprintf(
                "    [id=%d] %s (%s) - %s, %s%s\n",
                $loc->id,
                $loc->location_name,
                $loc->location_code ?? 'N/A',
                $loc->location_type ?? 'N/A',
                $loc->city ?? 'N/A',
                $primary
            );
        }
    }
}

echo "\n";
echo "Total: $count customers\n";

$db->close();
