<?php
// Create a quick debug script to test the SPK detail API response

// Disable error reporting for cleaner output
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to fetch API data
function fetchUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $status,
        'body' => $response
    ];
}

// Get SPK ID from query parameter or use default
$spkId = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Test endpoint
$url = "http://localhost/optima1/marketing/spk/detail/$spkId";
echo "<h2>Testing SPK Detail API</h2>";
echo "<p>URL: $url</p>";

$result = fetchUrl($url);
echo "<p>Status Code: {$result['status']}</p>";

$data = json_decode($result['body'], true);

// Check if we got valid data
if ($result['status'] == 200) {
    if (isset($data['success']) && $data['success']) {
        echo "<h3>Success! Data structure:</h3>";
        
        echo "<h4>Main Data (j.data):</h4>";
        echo "<pre>";
        print_r($data['data']);
        echo "</pre>";
        
        echo "<h4>Specification Data (j.spesifikasi):</h4>";
        echo "<pre>";
        print_r($data['spesifikasi']);
        echo "</pre>";
        
    } else {
        echo "<h3>API returned error:</h3>";
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
} else {
    echo "<h3>Error fetching data!</h3>";
    echo "<pre>{$result['body']}</pre>";
}
