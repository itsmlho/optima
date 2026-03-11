<?php
/**
 * Clear OPcache and restart
 * Access via: http://localhost/optima/clear_cache.php
 */

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared successfully!<br>";
} else {
    echo "⚠️ OPcache not enabled<br>";
}

if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✅ APCu cache cleared!<br>";
}

echo "<br><strong>Next steps:</strong><br>";
echo "1. Restart Laragon (Stop All → Start All)<br>";
echo "2. Refresh your application<br>";
echo "3. Delete this file after use<br>";
