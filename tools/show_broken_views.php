<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$brokenViews = [
    'contract_unit_summary',
    'inventory_unit_components', 
    'v_customer_activity',
    'v_unit_availability',
    'vw_attachment_installed',
    'vw_attachment_status',
    'vw_work_order_sparepart_summary',
    'vw_work_orders_detail',
];

foreach ($brokenViews as $view) {
    echo "=== $view ===\n";
    $r = $pdo->query("SHOW CREATE VIEW `$view`");
    $def = $r->fetch(PDO::FETCH_ASSOC);
    echo $def['Create View'] . "\n\n";
}
