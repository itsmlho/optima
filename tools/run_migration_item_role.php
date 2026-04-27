<?php
// Run migration: add item_role column to delivery_items
$pdo = new PDO('mysql:host=127.0.0.1;dbname=optima_ci;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if column already exists
$cols = array_column($pdo->query('SHOW COLUMNS FROM delivery_items')->fetchAll(PDO::FETCH_ASSOC), 'Field');
if (in_array('item_role', $cols)) {
    echo "item_role column already exists — skipping ALTER.\n";
} else {
    $pdo->exec("ALTER TABLE `delivery_items` ADD COLUMN `item_role` ENUM('KIRIM','TARIK') NOT NULL DEFAULT 'KIRIM' AFTER `item_type`");
    echo "Added item_role column.\n";
}

// Check if index already exists
$indexes = $pdo->query("SHOW INDEX FROM delivery_items WHERE Key_name = 'idx_di_item_role'")->fetchAll();
if ($indexes) {
    echo "idx_di_item_role index already exists — skipping.\n";
} else {
    $pdo->exec("ALTER TABLE `delivery_items` ADD INDEX `idx_di_item_role` (`di_id`, `item_role`)");
    echo "Added idx_di_item_role index.\n";
}

echo "Migration complete.\n";
// Verify
$result = $pdo->query("SHOW COLUMNS FROM delivery_items LIKE 'item_role'")->fetch(PDO::FETCH_ASSOC);
echo "Column: " . print_r($result, true) . "\n";
