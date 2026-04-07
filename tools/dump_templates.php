<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci;charset=utf8mb4', 'root', '');
$r = $pdo->query("SELECT trigger_event, title_template, message_template FROM notification_rules WHERE is_active=1 ORDER BY trigger_event")->fetchAll(PDO::FETCH_ASSOC);
foreach ($r as $row) {
    echo $row['trigger_event'] . '|' . $row['title_template'] . '|' . $row['message_template'] . PHP_EOL;
}
