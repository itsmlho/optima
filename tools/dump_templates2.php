<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci;charset=utf8mb4', 'root', '');
$events = ['contract_created','quotation_created','sparepart_low_stock','sparepart_out_of_stock','sparepart_added','sparepart_used','spk_completed','work_order_unit_verified'];
foreach ($events as $e) {
    $r = $pdo->query("SELECT title_template, message_template FROM notification_rules WHERE trigger_event='$e' AND is_active=1")->fetch();
    if ($r) {
        echo "[$e]\n  title:   " . $r['title_template'] . "\n  message: " . $r['message_template'] . "\n\n";
    } else {
        echo "[$e] NO ACTIVE TEMPLATE\n\n";
    }
}
