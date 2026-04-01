<?php
/**
 * Batch fix script:
 * 1. form-group → mb-3 (Bootstrap 4 → 5)
 * 2. Remove duplicate DataTables CDN script tags
 */

$formGroupFiles = [
    'app/Views/auth/forgot_password.php',
    'app/Views/auth/login.php',
    'app/Views/auth/register.php',
    'app/Views/marketing/customer_management.php',
    'app/Views/notifications/admin.php',
    'app/Views/perizinan/silo.php',
    'app/Views/service/work_orders.php',
    'app/Views/system/settings.php',
];

$dtDuplicateFiles = [
    'app/Views/admin/advanced_user_management/permissions.php',
    'app/Views/finance/invoices.php',
    'app/Views/marketing/unit_tersedia.php',
    'app/Views/perizinan/silo.php',
    'app/Views/warehouse/inventory/attachments/index.php',
    'app/Views/warehouse/inventory/invent_attachment.php',
    'app/Views/warehouse/po_verification.php',
];

echo "=== Fix 1: form-group → mb-3 ===\n";
foreach ($formGroupFiles as $f) {
    $c = file_get_contents($f);
    $before = substr_count($c, 'class="form-group"');
    $c = str_replace('class="form-group"', 'class="mb-3"', $c);
    file_put_contents($f, $c);
    echo "  ✓ $f ($before replacements)\n";
}

echo "\n=== Fix 2: Remove duplicate DataTables CDN script tags ===\n";
// Pattern: <script src="https://cdn.datatables.net/.../...min.js..."> including variations with ?v=...
$dtPattern = '/<script[^>]+cdn\.datatables\.net\/[^>]+>\s*<\/script>/i';
foreach ($dtDuplicateFiles as $f) {
    $c = file_get_contents($f);
    $count = preg_match_all($dtPattern, $c);
    $c = preg_replace($dtPattern, '', $c);
    // Clean up extra blank lines
    $c = preg_replace('/\n{3,}/', "\n\n", $c);
    file_put_contents($f, $c);
    echo "  ✓ $f (removed $count tags)\n";
}

echo "\nDone.\n";
