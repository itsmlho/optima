<?php
/**
 * Sprint 1-3 Endpoints Test Script
 * Tests all new billing enhancement endpoints
 * Run from browser: http://localhost/optima/tests/sprint_1_3_endpoints_test.php
 */

require_once __DIR__ . '/../system/Config/Paths.php';
require_once SYSTEMPATH . 'bootstrap.php';

// Initialize CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

echo "<!DOCTYPE html><html><head><title>Sprint 1-3 Endpoints Test</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#f5f5f5;} ";
echo ".test{background:white;margin:10px 0;padding:15px;border-left:4px solid #ddd;} ";
echo ".pass{border-left-color:#4caf50;} .fail{border-left-color:#f44336;} ";
echo "h3{margin:0 0 10px 0;} pre{background:#f9f9f9;padding:10px;overflow-x:auto;}</style></head><body>";

echo "<h1>🧪 Sprint 1-3 Endpoints Test</h1>";
echo "<p>Testing all billing enhancement endpoints...</p><hr>";

// Helper function to test endpoint
function testEndpoint($name, $url, $method = 'GET', $data = null) {
    echo "<div class='test'>";
    echo "<h3>Testing: $name</h3>";
    echo "<p><strong>Endpoint:</strong> $method $url</p>";
    
    try {
        $ch = curl_init();
        $fullUrl = 'http://localhost/optima/' . ltrim($url, '/');
        
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "<p class='fail'>❌ <strong>Error:</strong> $error</p>";
            return false;
        }
        
        $json = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            echo "<p class='pass'>✅ <strong>Status:</strong> HTTP $httpCode</p>";
            
            if ($json) {
                if (isset($json['success']) && $json['success']) {
                    echo "<p class='pass'>✅ <strong>Success:</strong> " . 
                         ($json['message'] ?? 'Request successful') . "</p>";
                    
                    if (isset($json['data'])) {
                        $count = is_array($json['data']) ? count($json['data']) : 1;
                        echo "<p>📊 <strong>Data count:</strong> $count records</p>";
                        echo "<pre>" . json_encode($json['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
                    }
                } else {
                    echo "<p class='fail'>⚠️ <strong>Warning:</strong> " . 
                         ($json['message'] ?? 'Unknown response') . "</p>";
                }
            } else {
                echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
            }
            
            echo "</div>";
            return true;
        } else {
            echo "<p class='fail'>❌ <strong>Failed:</strong> HTTP $httpCode</p>";
            echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
            echo "</div>";
            return false;
        }
    } catch (Exception $e) {
        echo "<p class='fail'>❌ <strong>Exception:</strong> " . $e->getMessage() . "</p>";
        echo "</div>";
        return false;
    }
}

echo "<h2>📋 Sprint 1: Renewal Workflow</h2>";

// Test 1: Get Expiring Contracts
testEndpoint(
    'Get Expiring Contracts',
    'kontrak/getExpiringContracts',
    'GET'
);

// Test 2: Get Contract Units
testEndpoint(
    'Get Contract Units (for renewal)',
    'marketing/kontrak/units/70',
    'GET'
);

echo "<h2>📋 Sprint 3: Contract Amendments</h2>";

// Test 3: Get Active Contracts
testEndpoint(
    'Get Active Contracts',
    'kontrak/getActiveContracts',
    'GET'
);

// Test 4: Get Contract History
testEndpoint(
    'Get Contract History',
    'kontrak/getContractHistory/70',
    'GET'
);

// Test 5: Get Rate History
testEndpoint(
    'Get Rate History',
    'kontrak/getRateHistory/70',
    'GET'
);

echo "<h2>📋 Additional APIs</h2>";

// Test 6: Get All Contracts
testEndpoint(
    'Get All Contracts',
    'kontrak/getAllContracts',
    'GET'
);

// Test 7: Get All Units
testEndpoint(
    'Get All Units',
    'kontrak/getAllUnits',
    'GET'
);

// Test 8: Get Stats
testEndpoint(
    'Get Contract Stats',
    'kontrak/getStats',
    'GET'
);

echo "<hr>";
echo "<h2>✅ Test Summary</h2>";
echo "<p>All endpoint tests completed. Check results above for details.</p>";
echo "<p><a href='/optima/marketing/kontrak'>← Back to Contracts</a></p>";
echo "</body></html>";
