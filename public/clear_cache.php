<?php
/**
 * Clear PHP OPcache to reload Model changes
 * Access via: http://localhost/optima/public/clear_cache.php
 */

// Clear OPcache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared successfully!\n";
} else {
    echo "⚠️ OPcache not enabled\n";
}

// Clear APCu cache if available
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✅ APCu cache cleared!\n";
} else {
    echo "⚠️ APCu not enabled\n";
}

// Clear realpath cache
clearstatcache(true);
echo "✅ Realpath cache cleared!\n";

echo "\n📋 Please refresh your browser (Ctrl+Shift+R) and test again.\n";
echo "\n🔗 Return to: <a href='/optima/public/marketing/quotations'>Quotations</a>\n";
