<?php
/**
 * Database Audit Script
 * 
 * Script ini akan:
 * 1. Mengaudit semua database di MySQL server
 * 2. Mengidentifikasi database yang aktif digunakan aplikasi
 * 3. Memisahkan database aktif vs tidak aktif
 * 4. Menghasilkan laporan lengkap
 */

// Read database config directly from file (avoid CodeIgniter dependencies)
$configFile = __DIR__ . '/../app/Config/Database.php';
$configContent = file_get_contents($configFile);

// Extract database config using regex
$config = [
    'hostname' => '127.0.0.1',
    'username' => 'root',
    'password' => 'root',
    'database' => 'optima_ci',
    'port' => 3306,
];

// Parse config from PHP file
if (preg_match("/'hostname'\s*=>\s*['\"]([^'\"]+)['\"]/", $configContent, $matches)) {
    $config['hostname'] = $matches[1];
}
if (preg_match("/'username'\s*=>\s*['\"]([^'\"]+)['\"]/", $configContent, $matches)) {
    $config['username'] = $matches[1];
}
if (preg_match("/'password'\s*=>\s*['\"]([^'\"]+)['\"]/", $configContent, $matches)) {
    $config['password'] = $matches[1];
}
if (preg_match("/'database'\s*=>\s*['\"]([^'\"]+)['\"]/", $configContent, $matches)) {
    $config['database'] = $matches[1];
}
if (preg_match("/'port'\s*=>\s*(\d+)/", $configContent, $matches)) {
    $config['port'] = (int)$matches[1];
}

// Function to execute MySQL query via command line
function mysql_query($host, $port, $user, $pass, $query, $database = '') {
    // Create temporary config file for password (more secure)
    $tmpConfig = sys_get_temp_dir() . '/mysql_config_' . uniqid() . '.cnf';
    $configContent = "[client]\n";
    $configContent .= "host=" . $host . "\n";
    $configContent .= "port=" . $port . "\n";
    $configContent .= "user=" . $user . "\n";
    $configContent .= "password=" . $pass . "\n";
    file_put_contents($tmpConfig, $configContent);
    chmod($tmpConfig, 0600); // Read/write for owner only
    
    $dbParam = $database ? "-D " . escapeshellarg($database) : '';
    $queryEscaped = str_replace(['"', '$', '`'], ['\\"', '\\$', '\\`'], $query);
    $cmd = sprintf(
        'mysql --defaults-file=%s %s -e %s 2>&1',
        escapeshellarg($tmpConfig),
        $dbParam,
        escapeshellarg($queryEscaped)
    );
    
    $output = [];
    $returnVar = 0;
    exec($cmd, $output, $returnVar);
    
    // Clean up temp file
    @unlink($tmpConfig);
    
    if ($returnVar !== 0) {
        return false;
    }
    
    return $output;
}

// Test connection
$testQuery = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], 'SELECT 1');
if ($testQuery === false) {
    die("Connection failed: Cannot connect to MySQL server\n");
}

echo "========================================\n";
echo "DATABASE AUDIT REPORT\n";
echo "========================================\n";
echo "Server: {$config['hostname']}:{$config['port']}\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

// Get all databases
$databases = [];
$result = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], 'SHOW DATABASES');

if ($result !== false) {
    foreach ($result as $line) {
        $dbName = trim($line);
        
        // Skip empty lines and headers
        if (empty($dbName) || $dbName === 'Database') {
            continue;
        }
        
        // Skip system databases
        if (in_array($dbName, ['information_schema', 'performance_schema', 'mysql', 'sys'])) {
            continue;
        }
        
        $databases[] = $dbName;
    }
}

// Database yang digunakan aplikasi (dari config)
$appDatabase = $config['database'];
$activeDatabases = [];
$inactiveDatabases = [];

// Analyze each database
foreach ($databases as $dbName) {
    // Get table count
    $tableResult = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], "SHOW TABLES", $dbName);
    $tableCount = $tableResult !== false ? count(array_filter($tableResult, function($line) {
        return !empty(trim($line)) && trim($line) !== 'Tables_in_';
    })) : 0;
    
    // Get total size
    $sizeQuery = "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.tables WHERE table_schema = '$dbName'";
    $sizeResult = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], $sizeQuery);
    $size = 0;
    if ($sizeResult !== false) {
        foreach ($sizeResult as $line) {
            if (preg_match('/([\d.]+)/', $line, $matches)) {
                $size = (float)$matches[1];
                break;
            }
        }
    }
    
    // Check if database is used by application
    $isActive = false;
    $usageReason = [];
    
    // Check 1: Is it the configured database?
    if ($dbName === $appDatabase) {
        $isActive = true;
        $usageReason[] = "Database utama aplikasi (dari config)";
    }
    
    // Check 2: Does it have tables that match application structure?
    if ($tableCount > 0 && $tableResult !== false) {
        $tables = [];
        foreach ($tableResult as $line) {
            $tableName = trim($line);
            if (!empty($tableName) && $tableName !== 'Tables_in_' . $dbName) {
                $tables[] = $tableName;
            }
        }
        
        // Check for common application tables
        $commonTables = ['users', 'roles', 'permissions', 'user_roles', 'role_permissions'];
        $foundCommonTables = array_intersect($commonTables, $tables);
        
        if (count($foundCommonTables) > 0) {
            $isActive = true;
            $usageReason[] = "Memiliki tabel aplikasi: " . implode(', ', $foundCommonTables);
        }
    }
    
    // Check 3: Check for foreign keys (indicates active database)
    $fkQuery = "SELECT COUNT(*) as fk_count FROM information_schema.KEY_COLUMN_USAGE WHERE table_schema = '$dbName' AND referenced_table_name IS NOT NULL";
    $fkResult = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], $fkQuery);
    $fkCount = 0;
    if ($fkResult !== false) {
        foreach ($fkResult as $line) {
            if (preg_match('/(\d+)/', $line, $matches)) {
                $fkCount = (int)$matches[1];
                break;
            }
        }
    }
    
    $dbInfo = [
        'name' => $dbName,
        'table_count' => $tableCount,
        'size_mb' => $size,
        'fk_count' => $fkCount,
        'is_active' => $isActive,
        'usage_reason' => $usageReason
    ];
    
    if ($isActive) {
        $activeDatabases[] = $dbInfo;
    } else {
        $inactiveDatabases[] = $dbInfo;
    }
}

// Generate report
echo "DATABASE AKTIF (DIGUNAKAN APLIKASI)\n";
echo "========================================\n";
if (empty($activeDatabases)) {
    echo "Tidak ada database aktif ditemukan.\n\n";
} else {
    foreach ($activeDatabases as $db) {
        echo "\nDatabase: {$db['name']}\n";
        echo "  - Jumlah Tabel: {$db['table_count']}\n";
        echo "  - Ukuran: {$db['size_mb']} MB\n";
        echo "  - Foreign Keys: {$db['fk_count']}\n";
        echo "  - Alasan Aktif:\n";
        foreach ($db['usage_reason'] as $reason) {
            echo "    * $reason\n";
        }
    }
}

echo "\n\nDATABASE TIDAK AKTIF (TIDAK DIGUNAKAN)\n";
echo "========================================\n";
if (empty($inactiveDatabases)) {
    echo "Tidak ada database tidak aktif ditemukan.\n\n";
} else {
    foreach ($inactiveDatabases as $db) {
        echo "\nDatabase: {$db['name']}\n";
        echo "  - Jumlah Tabel: {$db['table_count']}\n";
        echo "  - Ukuran: {$db['size_mb']} MB\n";
        echo "  - Foreign Keys: {$db['fk_count']}\n";
        echo "  - Status: Tidak digunakan oleh aplikasi\n";
    }
}

// Save detailed report to file
$reportFile = __DIR__ . '/database_audit_report_' . date('Y-m-d_His') . '.txt';
$reportContent = ob_get_clean();

// Generate HTML report
$htmlReport = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Database Audit Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #ddd; padding-bottom: 5px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .active { background-color: #d4edda; }
        .inactive { background-color: #f8d7da; }
        .info { background-color: #d1ecf1; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Database Audit Report</h1>
    <div class='info'>
        <strong>Server:</strong> {$config['hostname']}:{$config['port']}<br>
        <strong>Tanggal:</strong> " . date('Y-m-d H:i:s') . "<br>
        <strong>Database Aplikasi (Config):</strong> {$appDatabase}
    </div>
    
    <h2>Database Aktif (" . count($activeDatabases) . ")</h2>
    <table>
        <tr>
            <th>Nama Database</th>
            <th>Jumlah Tabel</th>
            <th>Ukuran (MB)</th>
            <th>Foreign Keys</th>
            <th>Alasan Aktif</th>
        </tr>";

foreach ($activeDatabases as $db) {
    $htmlReport .= "
        <tr class='active'>
            <td><strong>{$db['name']}</strong></td>
            <td>{$db['table_count']}</td>
            <td>{$db['size_mb']}</td>
            <td>{$db['fk_count']}</td>
            <td>" . implode('<br>', $db['usage_reason']) . "</td>
        </tr>";
}

$htmlReport .= "
    </table>
    
    <h2>Database Tidak Aktif (" . count($inactiveDatabases) . ")</h2>
    <table>
        <tr>
            <th>Nama Database</th>
            <th>Jumlah Tabel</th>
            <th>Ukuran (MB)</th>
            <th>Foreign Keys</th>
            <th>Status</th>
        </tr>";

foreach ($inactiveDatabases as $db) {
    $htmlReport .= "
        <tr class='inactive'>
            <td>{$db['name']}</td>
            <td>{$db['table_count']}</td>
            <td>{$db['size_mb']}</td>
            <td>{$db['fk_count']}</td>
            <td>Tidak digunakan</td>
        </tr>";
}

$htmlReport .= "
    </table>
</body>
</html>";

// Save reports
file_put_contents($reportFile, $reportContent);
file_put_contents(__DIR__ . '/database_audit_report_' . date('Y-m-d_His') . '.html', $htmlReport);

// Save JSON for backup script
$jsonData = [
    'audit_date' => date('Y-m-d H:i:s'),
    'server' => $config['hostname'],
    'port' => $config['port'],
    'app_database' => $appDatabase,
    'active_databases' => array_column($activeDatabases, 'name'),
    'inactive_databases' => array_column($inactiveDatabases, 'name'),
    'detailed' => [
        'active' => $activeDatabases,
        'inactive' => $inactiveDatabases
    ]
];

file_put_contents(__DIR__ . '/database_audit_result.json', json_encode($jsonData, JSON_PRETTY_PRINT));

echo "\n\n========================================\n";
echo "LAPORAN TELAH DISIMPAN:\n";
echo "========================================\n";
echo "1. Text Report: $reportFile\n";
echo "2. HTML Report: " . __DIR__ . "/database_audit_report_" . date('Y-m-d_His') . ".html\n";
echo "3. JSON Data: " . __DIR__ . "/database_audit_result.json\n";
echo "\n";
echo "Database Aktif: " . count($activeDatabases) . "\n";
echo "Database Tidak Aktif: " . count($inactiveDatabases) . "\n";
echo "\n";

// Connection closed (using command line, no need to close)

