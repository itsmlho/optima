<?php
/**
 * API Test Script for Employee Endpoints
 * Test the multi-mechanic selection API endpoints
 */

// Include CodeIgniter bootstrap
require_once 'app/Config/App.php';

// Test URL
$baseUrl = 'http://localhost/optima/public';
$testEndpoints = [
    '/service/employees/by-roles?roles=MECHANIC_UNIT_PREP,HELPER',
    '/service/employees/by-roles?roles=MECHANIC_FABRICATION,HELPER',
    '/service/employees/by-roles?roles=FOREMAN,SUPERVISOR,HELPER'
];

echo "<h2>API Endpoint Tests</h2>\n";

foreach ($testEndpoints as $endpoint) {
    $url = $baseUrl . $endpoint;
    echo "<h3>Testing: {$endpoint}</h3>\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: {$httpCode}<br>\n";
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data) {
            echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "<br>\n";
        } else {
            echo "Raw Response: " . htmlspecialchars(substr($response, 0, 500)) . "<br>\n";
        }
    } else {
        echo "No response received<br>\n";
    }
    
    echo "<hr>\n";
}
?>