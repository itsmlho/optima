<?php
// Fix Unit Audit menu in sidebar

$file = 'c:/laragon/www/optima/app/Views/layouts/sidebar_new.php';
$content = file_get_contents($file);

// Fix the broken Unit Audit link - add class="nav-link"
$search = '<a <?= (strpos(current_url(), \'service/unit_audit\') !== false && strpos(current_url(), \'movements\') === false) ? \'active\' : \'\' )" href="<?= base_url(\'/service/unit_audit\') ?>"';

$replace = '<a class="nav-link <?= (strpos(current_url(), \'service/unit_audit\') !== false && strpos(current_url(), \'movements\') === false) ? \'active\' : \'\' )" href="<?= base_url(\'/service/unit_audit\') ?>"';

$newContent = str_replace($search, $replace, $content);

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "Fixed Unit Audit link!\n";
} else {
    echo "Pattern not found or already fixed\n";
}
