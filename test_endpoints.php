<?php
// Test file untuk endpoint Work Order
header('Content-Type: application/json');

// Base URL
$base_url = 'http://localhost/optima1/public/work-orders/';

echo "<h2>Testing Work Order Backend Endpoints</h2>";
echo "<div style='font-family: monospace;'>";

// Test 1: Generate Work Order Number
echo "<h3>1. Testing Generate Number</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'generate-number');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: " . $http_code . "<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";

// Test 2: Search Units
echo "<h3>2. Testing Search Units</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'search-units');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['search' => 'TR']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: " . $http_code . "<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";

// Test 3: Search Staff
echo "<h3>3. Testing Search Staff (Mechanic)</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'search-staff');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['search' => 'mech', 'role' => 'mechanic']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: " . $http_code . "<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";

// Test 4: Get Priority
echo "<h3>4. Testing Get Priority</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'get-priority');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['priority_id' => '1']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: " . $http_code . "<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";

// Test 5: Get Subcategory Priority
echo "<h3>5. Testing Get Subcategory Priority</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'get-subcategory-priority');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['subcategory_id' => '1']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: " . $http_code . "<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";

echo "</div>";

// Test database connection
echo "<h3>6. Testing Database Connection</h3>";
try {
    $config = new \Config\Database();
    $db = \Config\Database::connect();
    
    // Test query for work orders
    $query = $db->query("SELECT COUNT(*) as total FROM work_orders");
    $result = $query->getRowArray();
    echo "Total Work Orders: " . $result['total'] . "<br>";
    
    // Test query for units
    $query = $db->query("SELECT COUNT(*) as total FROM units");
    $result = $query->getRowArray();
    echo "Total Units: " . $result['total'] . "<br>";
    
    // Test query for staff
    $query = $db->query("SELECT COUNT(*) as total FROM staff");
    $result = $query->getRowArray();
    echo "Total Staff: " . $result['total'] . "<br>";
    
    echo "Database connection: <span style='color: green;'>OK</span><br>";
    
} catch (Exception $e) {
    echo "Database connection: <span style='color: red;'>ERROR - " . $e->getMessage() . "</span><br>";
}
?>