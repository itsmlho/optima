<?php
// Migration: add tarik_contract_id to delivery_instructions
// For TUKAR workflow: records which contract the old (pulled) unit came from
$pdo = new PDO('mysql:host=127.0.0.1;dbname=optima_ci;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$cols = array_column($pdo->query('SHOW COLUMNS FROM delivery_instructions')->fetchAll(PDO::FETCH_ASSOC), 'Field');

if (in_array('tarik_contract_id', $cols)) {
    echo "tarik_contract_id already exists — skipping.\n";
} else {
    // Add after contract_id column
    $pdo->exec("ALTER TABLE `delivery_instructions`
        ADD COLUMN `tarik_contract_id` INT NULL COMMENT 'TUKAR workflow: contract_id of the old unit being pulled (may differ from contract_id of new unit)'
        AFTER `contract_id`");
    echo "Added tarik_contract_id column.\n";
}

// Verify
$result = $pdo->query("SHOW COLUMNS FROM delivery_instructions LIKE 'tarik_contract_id'")->fetch(PDO::FETCH_ASSOC);
echo "Column: " . ($result ? "{$result['Field']} | {$result['Type']} | Null={$result['Null']}" : 'NOT FOUND') . "\n";
echo "Migration complete.\n";
