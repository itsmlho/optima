<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$cols = $pdo->query("SHOW COLUMNS FROM delivery_instructions")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo $c['Field'] . ' | ' . $c['Type'] . ' | Null=' . $c['Null'] . PHP_EOL;
}
