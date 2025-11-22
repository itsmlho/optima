<?php
/**
 * Script untuk verifikasi kelengkapan backup database
 * Memastikan semua komponen ter-backup: Tables, Views, Procedures, Functions, Triggers, Events
 */

$backupFile = $argv[1] ?? __DIR__ . '/backups/optima_db_*.sql.gz';
$backupFiles = glob($backupFile);

if (empty($backupFiles)) {
    die("Error: Backup file not found: $backupFile\n");
}

$backupFile = $backupFiles[0]; // Get latest
echo "Verifying backup: $backupFile\n\n";

// Read database config
$configFile = __DIR__ . '/../app/Config/Database.php';
$configContent = file_get_contents($configFile);

$config = [
    'hostname' => '127.0.0.1',
    'username' => 'root',
    'password' => 'root',
    'database' => 'optima_db',
    'port' => 3306,
];

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

// Function to execute MySQL query
function mysql_query($host, $port, $user, $pass, $query, $database = '') {
    $tmpConfig = sys_get_temp_dir() . '/mysql_config_' . uniqid() . '.cnf';
    $configContent = "[client]\n";
    $configContent .= "host=" . $host . "\n";
    $configContent .= "port=" . $port . "\n";
    $configContent .= "user=" . $user . "\n";
    $configContent .= "password=" . $pass . "\n";
    file_put_contents($tmpConfig, $configContent);
    chmod($tmpConfig, 0600);
    
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
    
    @unlink($tmpConfig);
    
    if ($returnVar !== 0) {
        return false;
    }
    
    return $output;
}

// Get actual counts from database
echo "Getting actual database components...\n";
$actual = [
    'tables' => 0,
    'views' => 0,
    'procedures' => 0,
    'functions' => 0,
    'triggers' => 0,
    'events' => 0,
];

$result = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], 
    "SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = '{$config['database']}' AND table_type = 'BASE TABLE'");
if ($result !== false) {
    foreach ($result as $line) {
        if (preg_match('/(\d+)/', $line, $matches)) {
            $actual['tables'] = (int)$matches[1];
            break;
        }
    }
}

$result = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], 
    "SELECT COUNT(*) as cnt FROM information_schema.views WHERE table_schema = '{$config['database']}'");
if ($result !== false) {
    foreach ($result as $line) {
        if (preg_match('/(\d+)/', $line, $matches)) {
            $actual['views'] = (int)$matches[1];
            break;
        }
    }
}

$result = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], 
    "SELECT COUNT(*) as cnt FROM information_schema.routines WHERE routine_schema = '{$config['database']}' AND routine_type = 'PROCEDURE'");
if ($result !== false) {
    foreach ($result as $line) {
        if (preg_match('/(\d+)/', $line, $matches)) {
            $actual['procedures'] = (int)$matches[1];
            break;
        }
    }
}

$result = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], 
    "SELECT COUNT(*) as cnt FROM information_schema.routines WHERE routine_schema = '{$config['database']}' AND routine_type = 'FUNCTION'");
if ($result !== false) {
    foreach ($result as $line) {
        if (preg_match('/(\d+)/', $line, $matches)) {
            $actual['functions'] = (int)$matches[1];
            break;
        }
    }
}

$result = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], 
    "SELECT COUNT(*) as cnt FROM information_schema.triggers WHERE trigger_schema = '{$config['database']}'");
if ($result !== false) {
    foreach ($result as $line) {
        if (preg_match('/(\d+)/', $line, $matches)) {
            $actual['triggers'] = (int)$matches[1];
            break;
        }
    }
}

$result = mysql_query($config['hostname'], $config['port'], $config['username'], $config['password'], 
    "SELECT COUNT(*) as cnt FROM information_schema.events WHERE event_schema = '{$config['database']}'");
if ($result !== false) {
    foreach ($result as $line) {
        if (preg_match('/(\d+)/', $line, $matches)) {
            $actual['events'] = (int)$matches[1];
            break;
        }
    }
}

// Check backup file
echo "Checking backup file...\n";
$backupContent = shell_exec("zcat " . escapeshellarg($backupFile) . " 2>/dev/null");

if (empty($backupContent)) {
    die("Error: Cannot read backup file or file is empty\n");
}

$backup = [
    'tables' => preg_match_all('/CREATE TABLE/i', $backupContent),
    'views' => preg_match_all('/CREATE.*VIEW/i', $backupContent),
    'procedures' => preg_match_all('/CREATE.*PROCEDURE/i', $backupContent),
    'functions' => preg_match_all('/CREATE.*FUNCTION/i', $backupContent),
    'triggers' => preg_match_all('/CREATE.*TRIGGER/i', $backupContent),
    'events' => preg_match_all('/CREATE.*EVENT/i', $backupContent),
];

// Compare
echo "\n========================================\n";
echo "VERIFICATION RESULTS\n";
echo "========================================\n\n";

$allMatch = true;
foreach ($actual as $component => $count) {
    $backupCount = $backup[$component];
    $match = $count == $backupCount;
    $status = $match ? "✓" : "✗";
    
    if (!$match) {
        $allMatch = false;
    }
    
    printf("%-15s: Actual: %3d | Backup: %3d | %s\n", 
        ucfirst($component), 
        $count, 
        $backupCount,
        $status
    );
}

echo "\n========================================\n";
if ($allMatch) {
    echo "✓ BACKUP IS COMPLETE - All components match!\n";
} else {
    echo "✗ BACKUP IS INCOMPLETE - Some components missing!\n";
    echo "\nRecommendation: Create new backup with complete options.\n";
}
echo "========================================\n";

