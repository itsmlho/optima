<?php
// Quick test to check if Security config is loaded correctly
require_once __DIR__ . '/../vendor/autoload.php';

$pathsConfig = SYSTEMPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require_once SYSTEMPATH . 'bootstrap.php';

// Load Security config
$security = config('Security');

echo "<h2>CSRF Configuration Test</h2>";
echo "<pre>";
echo "Token Name (from config): " . $security->tokenName . "\n";
echo "Cookie Name (from config): " . $security->cookieName . "\n";
echo "Header Name (from config): " . $security->headerName . "\n";
echo "Token Randomize: " . ($security ->tokenRandomize ? 'true' : 'false') . "\n";
echo "Regenerate: " . ($security->regenerate ? 'true' : 'false') . "\n";
echo "\n";

// Test csrf_token() helper
echo "csrf_token() helper returns: " . csrf_token() . "\n";
echo "csrf_header() helper returns: " . csrf_header() . "\n";
echo "</pre>";

echo "<hr><p style='color: green;'>If Token Name shows 'csrf_test_name', config is loaded correctly.</p>";
echo "<p style='color: red;'>If Token Name shows 'csrf_token_name', config is NOT loaded (using system default).</p>";
