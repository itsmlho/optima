<?php
// Diagnostic script - DELETE THIS FILE AFTER USE
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'optima_ci';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

echo "<pre style='font-family:monospace;font-size:13px;'>";

// 1. Check kontrak table columns
echo "=== KONTRAK TABLE COLUMNS ===\n";
$r = $conn->query("SHOW COLUMNS FROM kontrak");
while($row = $r->fetch_assoc()) {
    echo sprintf("%-30s %-20s %s\n", $row['Field'], $row['Type'], $row['Null']);
}

// 2. Summary counts
echo "\n=== SUMMARY ===\n";
$r = $conn->query("SELECT COUNT(*) as total_kontrak, SUM(CASE WHEN total_units > 0 THEN 1 ELSE 0 END) as kontrak_with_units, SUM(CASE WHEN COALESCE(total_units,0) = 0 THEN 1 ELSE 0 END) as kontrak_zero_units FROM kontrak");
$row = $r->fetch_assoc();
echo "Total kontrak        : {$row['total_kontrak']}\n";
echo "Kontrak total_units>0: {$row['kontrak_with_units']}\n";
echo "Kontrak total_units=0: {$row['kontrak_zero_units']}\n";

// 3. kontrak_unit stats
echo "\n=== KONTRAK_UNIT TABLE ===\n";
$r = $conn->query("SELECT COUNT(*) as total, COUNT(DISTINCT kontrak_id) as distinct_kontrak, COUNT(DISTINCT unit_id) as distinct_units FROM kontrak_unit");
$row = $r->fetch_assoc();
echo "Total rows           : {$row['total']}\n";
echo "Distinct kontrak_id  : {$row['distinct_kontrak']}\n";
echo "Distinct unit_id     : {$row['distinct_units']}\n";

// 4. kontrak_unit status breakdown
echo "\n=== KONTRAK_UNIT STATUS ===\n";
$r = $conn->query("SELECT status, is_temporary, COUNT(*) as cnt FROM kontrak_unit GROUP BY status, is_temporary ORDER BY status");
while($row = $r->fetch_assoc()) {
    echo "status={$row['status']}, is_temporary={$row['is_temporary']}: {$row['cnt']} rows\n";
}

// 5. Check if kontrak has customer_id
echo "\n=== KONTRAK customer_id COLUMN ===\n";
$r = $conn->query("SHOW COLUMNS FROM kontrak LIKE 'customer_id'");
if ($r->num_rows > 0) {
    echo "FOUND: kontrak.customer_id EXISTS\n";
} else {
    echo "NOT FOUND: kontrak does not have customer_id column\n";
    echo "=> Join must go via customer_locations (customer_location_id -> cl.id -> cl.customer_id)\n";
}

// 6. Sample: kontrak vs actual unit count
echo "\n=== SAMPLE: STORED total_units vs ACTUAL COUNT from kontrak_unit ===\n";
$r = $conn->query("
    SELECT k.id, k.no_kontrak, k.total_units as stored_total_units,
           COUNT(ku.id) as actual_count_all,
           SUM(CASE WHEN ku.status IN ('ACTIVE','TEMP_ACTIVE') AND COALESCE(ku.is_temporary,0)=0 THEN 1 ELSE 0 END) as actual_active_non_temp
    FROM kontrak k
    LEFT JOIN kontrak_unit ku ON ku.kontrak_id = k.id
    GROUP BY k.id, k.no_kontrak, k.total_units
    HAVING actual_count_all > 0
    LIMIT 15
");
echo sprintf("%-5s %-25s %-15s %-15s %-20s\n", "ID", "no_kontrak", "stored_units", "all_in_ku", "active_non_temp");
echo str_repeat("-", 85) . "\n";
while($row = $r->fetch_assoc()) {
    echo sprintf("%-5s %-25s %-15s %-15s %-20s\n",
        $row['id'], $row['no_kontrak'], $row['stored_total_units'],
        $row['actual_count_all'], $row['actual_active_non_temp']);
}

// 7. getCustomers units query test (CustomerManagementController line 164-170)
echo "\n=== TEST getCustomers UNIT QUERY (first 5 customers) ===\n";
$r = $conn->query("SELECT c.id, c.customer_name FROM customers c LIMIT 5");
$customers = $r->fetch_all(MYSQLI_ASSOC);
foreach ($customers as $cust) {
    $cid = $cust['id'];
    $r2 = $conn->query("
        SELECT COUNT(*) as cnt
        FROM kontrak_unit ku
        JOIN kontrak k ON k.id = ku.kontrak_id
        JOIN customer_locations cl ON cl.id = k.customer_location_id
        WHERE cl.customer_id = $cid
        AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
        AND (ku.is_temporary IS NULL OR ku.is_temporary = 0)
    ");
    $row2 = $r2->fetch_assoc();
    echo "Customer {$cid} [{$cust['customer_name']}]: {$row2['cnt']} units\n";
}

// 8. getGrouped query test
echo "\n=== TEST getGrouped QUERY (uses k.total_units - sample 5) ===\n";
$r = $conn->query("
    SELECT c.customer_name, COUNT(k.id) as num_kontrak, SUM(k.total_units) as sum_stored_units
    FROM kontrak k
    LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
    LEFT JOIN customers c ON cl.customer_id = c.id
    GROUP BY c.id, c.customer_name
    HAVING num_kontrak > 0
    LIMIT 5
");
echo sprintf("%-30s %-12s %-15s\n", "customer_name", "num_kontrak", "sum_stored_units");
echo str_repeat("-", 60) . "\n";
while($row = $r->fetch_assoc()) {
    echo sprintf("%-30s %-12s %-15s\n", $row['customer_name'], $row['num_kontrak'], $row['sum_stored_units']);
}

echo "\n=== DONE ===\n";
echo "</pre>";
$conn->close();
