<?php
/**
 * Enable MySQL General Query Log untuk debugging
 * Jalankan file ini sekali, lalu coba approval lagi
 * Setelah itu check mysql.general_log table
 */

require_once '../system/bootstrap.php';

$app = \Config\Services::createRequest(config('App'), false);
$app->setPath('/');

$db = \Config\Database::connect();

try {
    // Enable general log
    $db->query("SET GLOBAL general_log = 'ON'");
    $db->query("SET GLOBAL log_output = 'TABLE'");
    
    echo "✅ MySQL Query Logging ENABLED\n";
    echo "Queries will be logged to: mysql.general_log table\n\n";
    echo "Steps:\n";
    echo "1. Try your approval action\n";
    echo "2. Check the log with:\n";
    echo "   SELECT * FROM mysql.general_log WHERE argument LIKE '%status_unit%' ORDER BY event_time DESC LIMIT 20;\n\n";
    echo "3. Disable logging after done:\n";
    echo "   SET GLOBAL general_log = 'OFF';\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
