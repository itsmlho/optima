<?php
/**
 * Verify TUKAR workflow DB migration
 * Usage: php tools/verify_tukar_migration.php
 * Or open via browser: http://localhost/optima/tools/verify_tukar_migration.php
 */

$host   = '127.0.0.1';
$dbname = 'optima_ci';
$user   = 'root';
$pass   = '';

// Production: uncomment and fill in
// $host   = '127.0.0.1';
// $dbname = 'u138256737_optima_db';
// $user   = 'u138256737_root_optima';
// $pass   = 'YOUR_PRODUCTION_PASSWORD';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . PHP_EOL);
}

$checks = [];

// Helper: get columns of a table
function getColumns(PDO $pdo, string $table): array {
    return array_column(
        $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC),
        null, 'Field'
    );
}

// Helper: get indexes of a table
function getIndexes(PDO $pdo, string $table): array {
    return array_column(
        $pdo->query("SHOW INDEX FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC),
        null, 'Key_name'
    );
}

// ─────────────────────────────────────────────
// CHECK 1: delivery_items.item_role
// ─────────────────────────────────────────────
$cols = getColumns($pdo, 'delivery_items');
if (isset($cols['item_role'])) {
    $col = $cols['item_role'];
    $typeOk = stripos($col['Type'], "enum('KIRIM','TARIK')") !== false;
    $defaultOk = $col['Default'] === 'KIRIM';
    $nullOk = $col['Null'] === 'NO';
    $checks[] = [
        'table'   => 'delivery_items',
        'column'  => 'item_role',
        'status'  => ($typeOk && $defaultOk && $nullOk) ? 'OK' : 'WRONG',
        'detail'  => "Type={$col['Type']} | Default={$col['Default']} | Null={$col['Null']}",
        'expect'  => "Type=enum('KIRIM','TARIK') | Default=KIRIM | Null=NO",
    ];
} else {
    $checks[] = [
        'table'   => 'delivery_items',
        'column'  => 'item_role',
        'status'  => 'MISSING',
        'detail'  => 'Column does not exist',
        'expect'  => "ENUM('KIRIM','TARIK') NOT NULL DEFAULT 'KIRIM'",
    ];
}

// ─────────────────────────────────────────────
// CHECK 2: delivery_items index idx_di_item_role
// ─────────────────────────────────────────────
$indexes = getIndexes($pdo, 'delivery_items');
$checks[] = [
    'table'   => 'delivery_items',
    'column'  => 'INDEX idx_di_item_role',
    'status'  => isset($indexes['idx_di_item_role']) ? 'OK' : 'MISSING',
    'detail'  => isset($indexes['idx_di_item_role']) ? "Index exists on (di_id, item_role)" : 'Index not found',
    'expect'  => 'INDEX on (di_id, item_role)',
];

// ─────────────────────────────────────────────
// CHECK 3: delivery_instructions.tarik_contract_id
// ─────────────────────────────────────────────
$cols = getColumns($pdo, 'delivery_instructions');
if (isset($cols['tarik_contract_id'])) {
    $col = $cols['tarik_contract_id'];
    $typeOk = stripos($col['Type'], 'int') !== false;
    $nullOk = $col['Null'] === 'YES';
    $checks[] = [
        'table'   => 'delivery_instructions',
        'column'  => 'tarik_contract_id',
        'status'  => ($typeOk && $nullOk) ? 'OK' : 'WRONG',
        'detail'  => "Type={$col['Type']} | Null={$col['Null']} | Default=" . ($col['Default'] ?? 'NULL'),
        'expect'  => 'Type=int | Null=YES | Default=NULL',
    ];
} else {
    $checks[] = [
        'table'   => 'delivery_instructions',
        'column'  => 'tarik_contract_id',
        'status'  => 'MISSING',
        'detail'  => 'Column does not exist',
        'expect'  => 'INT NULL',
    ];
}

// ─────────────────────────────────────────────
// CHECK 4: Existing data — no TARIK items with wrong role
// ─────────────────────────────────────────────
$totalItems = (int)$pdo->query("SELECT COUNT(*) FROM delivery_items")->fetchColumn();
$kirimCount = (int)$pdo->query("SELECT COUNT(*) FROM delivery_items WHERE item_role = 'KIRIM'")->fetchColumn();
$tarikCount = (int)$pdo->query("SELECT COUNT(*) FROM delivery_items WHERE item_role = 'TARIK'")->fetchColumn();
$checks[] = [
    'table'   => 'delivery_items',
    'column'  => 'DATA: item_role distribution',
    'status'  => ($kirimCount + $tarikCount === $totalItems) ? 'OK' : 'WARN',
    'detail'  => "Total={$totalItems} | KIRIM={$kirimCount} | TARIK={$tarikCount}",
    'expect'  => 'All rows have valid item_role',
];

// ─────────────────────────────────────────────
// OUTPUT
// ─────────────────────────────────────────────
$isCli = PHP_SAPI === 'cli';

if ($isCli) {
    echo "\n=== TUKAR Migration Verification ===\n";
    echo "DB: {$dbname} @ {$host}\n\n";
    $allOk = true;
    foreach ($checks as $c) {
        $icon = $c['status'] === 'OK' ? '✅' : ($c['status'] === 'WARN' ? '⚠️ ' : '❌');
        printf("%s %-45s [%s]\n", $icon, "{$c['table']}.{$c['column']}", $c['status']);
        if ($c['status'] !== 'OK') {
            printf("   Got    : %s\n", $c['detail']);
            printf("   Expect : %s\n", $c['expect']);
            $allOk = false;
        }
    }
    echo "\n" . ($allOk ? "✅ All checks passed. Migration complete." : "❌ Some checks FAILED — run the migration SQL.") . "\n\n";
} else {
    // Browser output
    header('Content-Type: text/html; charset=utf-8');
    $allOk = array_reduce($checks, fn($carry, $c) => $carry && $c['status'] === 'OK', true);
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Migration Verify</title>
    <style>body{font-family:monospace;padding:20px;background:#f8f9fa}
    table{border-collapse:collapse;width:100%}th,td{border:1px solid #dee2e6;padding:8px 12px;text-align:left}
    th{background:#343a40;color:#fff}.ok{background:#d1e7dd}.fail{background:#f8d7da}.warn{background:#fff3cd}
    h2{margin-bottom:4px}</style></head><body>";
    echo "<h2>TUKAR Migration Verification</h2>";
    echo "<p>DB: <strong>{$dbname}</strong> @ {$host}</p>";
    echo "<table><tr><th>Table</th><th>Column/Check</th><th>Status</th><th>Detail</th><th>Expected</th></tr>";
    foreach ($checks as $c) {
        $cls = $c['status'] === 'OK' ? 'ok' : ($c['status'] === 'WARN' ? 'warn' : 'fail');
        echo "<tr class='{$cls}'><td>{$c['table']}</td><td>{$c['column']}</td><td><strong>{$c['status']}</strong></td><td>{$c['detail']}</td><td>{$c['expect']}</td></tr>";
    }
    echo "</table>";
    echo "<p style='font-size:1.1em;margin-top:16px'>" . ($allOk
        ? "✅ <strong>All checks passed.</strong> Migration complete."
        : "❌ <strong>Some checks FAILED.</strong> Run the migration SQL.") . "</p>";
    echo "</body></html>";
}
