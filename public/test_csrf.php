<!DOCTYPE html>
<html>
<head>
    <title>CSRF Config Test - Optima</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        pre { background: #f8f8f8; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #0061f2; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 CSRF Configuration Test</h1>
        <p>Testing if .env config is loaded correctly...</p>
        
        <?php
        // Bootstrap CodeIgniter 4 properly
        chdir(__DIR__ . '/..');
        
        // Define path constants
        define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
        
        // Load CodeIgniter bootstrap
        require_once __DIR__ . '/../app/Config/Paths.php';
        $paths = new Config\Paths();
        
        require_once $paths->systemDirectory . '/bootstrap.php';
        
        // Get security config
        $security = config('Security');
        
        echo '<table>';
        echo '<tr><th>Configuration</th><th>Value</th><th>Status</th></tr>';
        
        // Check Token Name
        $expectedTokenName = 'csrf_test_name';
        $actualTokenName = $security->tokenName;
        $tokenStatus = ($actualTokenName === $expectedTokenName) ? '<span class="success">✅ CORRECT</span>' : '<span class="error">❌ WRONG</span>';
        echo '<tr><td><strong>Token Name</strong></td><td>' . htmlspecialchars($actualTokenName) . '</td><td>' . $tokenStatus . '</td></tr>';
        
        // Check Token Randomize
        $expectedRandomize = false;
        $actualRandomize = $security->tokenRandomize;
        $randomizeStatus = ($actualRandomize === $expectedRandomize) ? '<span class="success">✅ CORRECT</span>' : '<span class="error">❌ WRONG (will break AJAX)</span>';
        echo '<tr><td><strong>Token Randomize</strong></td><td>' . ($actualRandomize ? 'true' : 'false') . '</td><td>' . $randomizeStatus . '</td></tr>';
        
        // Check Regenerate
        $expectedRegenerate = false;
        $actualRegenerate = $security->regenerate;
        $regenerateStatus = ($actualRegenerate === $expectedRegenerate) ? '<span class="success">✅ CORRECT</span>' : '<span class="error">❌ WRONG (will break AJAX)</span>';
        echo '<tr><td><strong>Regenerate</strong></td><td>' . ($actualRegenerate ? 'true' : 'false') . '</td><td>' . $regenerateStatus . '</td></tr>';
        
        // Check Cookie Name
        echo '<tr><td><strong>Cookie Name</strong></td><td>' . htmlspecialchars($security->cookieName) . '</td><td><span class="success">✅</span></td></tr>';
        
        // Check Header Name
        echo '<tr><td><strong>Header Name</strong></td><td>' . htmlspecialchars($security->headerName) . '</td><td><span class="success">✅</span></td></tr>';
        
        // Check Protection Method
        echo '<tr><td><strong>Protection Method</strong></td><td>' . htmlspecialchars($security->csrfProtection) . '</td><td><span class="success">✅</span></td></tr>';
        
        echo '</table>';
        
        // Test Helper Functions
        echo '<h2>🔧 Helper Functions Test</h2>';
        echo '<table>';
        echo '<tr><th>Helper Function</th><th>Returns</th></tr>';
        echo '<tr><td><code>csrf_token()</code></td><td><code>' . htmlspecialchars(csrf_token()) . '</code></td></tr>';
        echo '<tr><td><code>csrf_header()</code></td><td><code>' . htmlspecialchars(csrf_header()) . '</code></td></tr>';
        echo '<tr><td><code>csrf_hash()</code></td><td><code>' . htmlspecialchars(substr(csrf_hash(), 0, 40)) . '...</code></td></tr>';
        echo '</table>';
        
        // Overall Status
        echo '<h2>📊 Overall Status</h2>';
        if ($actualTokenName === $expectedTokenName && $actualRandomize === $expectedRandomize && $actualRegenerate === $expectedRegenerate) {
            echo '<div style="background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; border: 2px solid #c3e6cb;">';
            echo '<h3 style="margin-top: 0;">✅ ALL TESTS PASSED!</h3>';
            echo '<p><strong>Configuration is correct.</strong> CSRF should now work properly.</p>';
            echo '<p><strong>Next steps:</strong></p>';
            echo '<ol>';
            echo '<li><strong>Restart Laragon Apache</strong> (Stop All → Start All)</li>';
            echo '<li><strong>Clear browser cache</strong> (Ctrl+Shift+R)</li>';
            echo '<li><strong>Test Customer Management</strong> page</li>';
            echo '</ol>';
            echo '</div>';
        } else {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; border: 2px solid #f5c6cb;">';
            echo '<h3 style="margin-top: 0;">❌ CONFIGURATION MISMATCH</h3>';
            echo '<p>.env file has been updated, but CodeIgniter is still using old config.</p>';
            echo '<p><strong>Required actions:</strong></p>';
            echo '<ol>';
            echo '<li><strong>Restart Apache</strong> through Laragon (Stop All → Start All)</li>';
            echo '<li><strong>Clear PHP OpCache</strong> if enabled</li>';
            echo '<li><strong>Refresh this page</strong> to verify</li>';
            echo '</ol>';
            echo '</div>';
        }
        ?>
        
        <hr>
        <p style="text-align: center; color: #666;">
            <em>After fixing, refresh browser and test at: 
            <a href="/optima/public/marketing/customer-management" target="_blank">Customer Management</a></em>
        </p>
    </div>
</body>
</html>
