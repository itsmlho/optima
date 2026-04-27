<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=optima_ci', 'root', '');
$cols = $pdo->query("SHOW COLUMNS FROM delivery_items")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo $c['Field'] . ' | ' . $c['Type'] . ' | Null=' . $c['Null'] . PHP_EOL;
}
