<?php
/**
 * Debug script to check API endpoint responses
 * 
 * Executes requests against API endpoints and displays the raw response
 */

// Initialize
$id = isset($_GET['id']) ? (int)$_GET['id'] : 17; // Default to ID 17 as we've seen it in the tests
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : 'marketing/spk/detail';

// HTTP Client function
function httpRequest($url, $method = 'GET', $data = null) {
    $options = [
        'http' => [
            'method' => $method,
            'header' => 'Content-Type: application/json',
            'ignore_errors' => true
        ]
    ];
    
    if ($data && $method !== 'GET') {
        $options['http']['content'] = json_encode($data);
    }
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    return [
        'status' => $http_response_header[0],
        'headers' => $http_response_header,
        'body' => $result
    ];
}

// Get the base URL (assumes this script is in the same directory as index.php)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . $host . dirname($_SERVER['PHP_SELF']);

// Build the URL
$url = rtrim($baseUrl, '/') . '/' . trim($endpoint, '/') . '/' . $id;

// Make the request
$response = httpRequest($url);

// Display output
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Debug Response</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .status { padding: 5px 10px; border-radius: 3px; display: inline-block; margin-bottom: 10px; }
        .success { background-color: #dff0d8; color: #3c763d; }
        .error { background-color: #f2dede; color: #a94442; }
    </style>
</head>
<body>
    <h1>API Debug Response</h1>
    
    <form method="GET">
        <div style="margin-bottom: 15px;">
            <label for="endpoint">Endpoint:</label>
            <input type="text" id="endpoint" name="endpoint" value="<?= htmlspecialchars($endpoint) ?>" style="width: 300px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="id">ID:</label>
            <input type="number" id="id" name="id" value="<?= $id ?>">
        </div>
        <button type="submit">Send Request</button>
    </form>
    
    <h2>Request</h2>
    <div><strong>URL:</strong> <?= htmlspecialchars($url) ?></div>
    <div><strong>Method:</strong> GET</div>
    
    <h2>Response</h2>
    <div>
        <strong>Status:</strong> 
        <span class="status <?= strpos($response['status'], '200') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($response['status']) ?>
        </span>
    </div>
    
    <h3>Headers</h3>
    <pre><?= htmlspecialchars(implode("\n", $response['headers'])) ?></pre>
    
    <h3>Body</h3>
    <?php
    // Try to parse as JSON for better display
    $json = json_decode($response['body'], true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo '<pre>' . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . '</pre>';
    } else {
        echo '<pre>' . htmlspecialchars($response['body']) . '</pre>';
    }
    ?>
</body>
</html>
