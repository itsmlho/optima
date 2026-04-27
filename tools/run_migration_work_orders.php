<?php
// Run via: php tools/run_migration_work_orders.php

$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$steps = [
    'Drop FK' => 'ALTER TABLE work_orders DROP FOREIGN KEY fk_wo_unit',
    'Make nullable' => 'ALTER TABLE work_orders MODIFY COLUMN unit_id INT UNSIGNED NULL DEFAULT NULL',
    'Add FK SET NULL' => 'ALTER TABLE work_orders ADD CONSTRAINT fk_wo_unit FOREIGN KEY (unit_id) REFERENCES inventory_unit (id_inventory_unit) ON DELETE SET NULL ON UPDATE NO ACTION',
];

foreach ($steps as $label => $sql) {
    try {
        $pdo->exec($sql);
        echo "[OK] {$label}\n";
    } catch (\PDOException $e) {
        echo "[ERR] {$label}: " . $e->getMessage() . "\n";
    }
}

// Verify
$col = $pdo->query("SHOW COLUMNS FROM work_orders LIKE 'unit_id'")->fetch(PDO::FETCH_ASSOC);
echo "\nResult: work_orders.unit_id | Type={$col['Type']} | Null={$col['Null']}\n";
