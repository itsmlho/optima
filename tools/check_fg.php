<?php
$fg = [
    'app/Views/auth/forgot_password.php',
    'app/Views/auth/login.php',
    'app/Views/auth/register.php',
    'app/Views/marketing/customer_management.php',
    'app/Views/notifications/admin.php',
    'app/Views/perizinan/silo.php',
    'app/Views/service/work_orders.php',
    'app/Views/system/settings.php',
];
foreach ($fg as $f) {
    $c = file_get_contents($f);
    preg_match("/extend\('([^']+)'\)/", $c, $m);
    $layout = $m[1] ?? 'NO EXTEND';
    $count = substr_count($c, 'class="form-group"');
    echo str_pad(basename($f), 30) . " layout=$layout | form-group x$count\n";
}
