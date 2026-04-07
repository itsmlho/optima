<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci;charset=utf8mb4', 'root', '');
$r = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
sort($r);
foreach($r as $t) echo $t . PHP_EOL;
