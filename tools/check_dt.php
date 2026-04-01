<?php
$files = [
    'app/Views/admin/advanced_user_management/permissions.php',
    'app/Views/finance/invoices.php',
    'app/Views/marketing/unit_tersedia.php',
    'app/Views/perizinan/silo.php',
    'app/Views/warehouse/inventory/attachments/index.php',
    'app/Views/warehouse/inventory/invent_attachment.php',
    'app/Views/warehouse/po_verification.php',
];
foreach ($files as $f) {
    $c = file_get_contents($f);
    preg_match_all('/<script[^>]*datatables\.net[^>]*>/i', $c, $m);
    echo "=== " . basename(dirname($f)) . "/" . basename($f) . " ===\n";
    foreach ($m[0] as $tag) echo "  $tag\n";
    echo "\n";
}
