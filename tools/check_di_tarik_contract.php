<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=optima_ci;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$cols = array_column($pdo->query('SHOW COLUMNS FROM delivery_instructions')->fetchAll(PDO::FETCH_ASSOC), 'Field');
echo "tarik_contract_id: " . (in_array('tarik_contract_id', $cols) ? 'EXISTS' : 'NOT_EXISTS') . PHP_EOL;
echo "contract_id: " . (in_array('contract_id', $cols) ? 'EXISTS' : 'NOT_EXISTS') . PHP_EOL;
