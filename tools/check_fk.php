<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
echo "=== FK constraints referencing inventory_unit ===\n";
$rows = $pdo->query("
    SELECT kcu.TABLE_NAME, kcu.COLUMN_NAME, kcu.CONSTRAINT_NAME, rc.DELETE_RULE
    FROM information_schema.KEY_COLUMN_USAGE kcu
    JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
        ON rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
        AND rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
    WHERE kcu.REFERENCED_TABLE_NAME = 'inventory_unit'
      AND kcu.TABLE_SCHEMA = 'optima_ci'
    ORDER BY kcu.TABLE_NAME
")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['TABLE_NAME'] . '.' . $r['COLUMN_NAME'] . ' | DELETE=' . $r['DELETE_RULE'] . ' | ' . $r['CONSTRAINT_NAME'] . PHP_EOL;
}

echo "\n=== Tables with unit_id but NO FK to inventory_unit ===\n";
$cols = $pdo->query("
    SELECT c.TABLE_NAME, c.COLUMN_NAME
    FROM information_schema.COLUMNS c
    LEFT JOIN information_schema.KEY_COLUMN_USAGE kcu
        ON kcu.TABLE_NAME = c.TABLE_NAME
        AND kcu.COLUMN_NAME = c.COLUMN_NAME
        AND kcu.REFERENCED_TABLE_NAME = 'inventory_unit'
        AND kcu.TABLE_SCHEMA = 'optima_ci'
    WHERE c.TABLE_SCHEMA = 'optima_ci'
      AND c.COLUMN_NAME LIKE '%unit_id%'
      AND kcu.CONSTRAINT_NAME IS NULL
    ORDER BY c.TABLE_NAME
")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $r) {
    echo $r['TABLE_NAME'] . '.' . $r['COLUMN_NAME'] . ' (no FK)' . PHP_EOL;
}

echo "\n=== work_orders.unit_id column info ===\n";
$col = $pdo->query("SHOW COLUMNS FROM work_orders LIKE 'unit_id'")->fetch(PDO::FETCH_ASSOC);
if ($col) echo 'Type: '.$col['Type'].' | Null: '.$col['Null'].' | Default: '.($col['Default'] ?? 'NULL').PHP_EOL;
else echo '(column not found)'.PHP_EOL;

echo "\n=== Tables matching *unit_log* or *log_unit* ===\n";
foreach ($pdo->query("SHOW TABLES LIKE '%unit_log%'")->fetchAll(PDO::FETCH_COLUMN) as $t) echo $t.PHP_EOL;
foreach ($pdo->query("SHOW TABLES LIKE '%log_unit%'")->fetchAll(PDO::FETCH_COLUMN) as $t) echo $t.PHP_EOL;
