<?php
/**
 * Add Surat Jalan menu to regular Warehouse section
 */

$file = 'c:/laragon/www/optima/app/Views/layouts/sidebar_new.php';
$content = file_get_contents($file);

// Find the exact pattern
$search = '            <!-- PO Verification -->
            <?php if (can_view(\'warehouse\')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), \'warehouse/purchase-orders/wh-verification\') !== false ? \'active\' : \'\' ?>"
                   href="<?= base_url(\'/warehouse/purchase-orders/wh-verification\') ?>"
                   data-search-terms="po verification verify purchase order warehouse">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-link-text"><?= lang(\'App.po_verification\') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PERIZINAN DIVISION -->';

$replace = '            <!-- PO Verification -->
            <?php if (can_view(\'warehouse\')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), \'warehouse/purchase-orders/wh-verification\') !== false ? \'active\' : \'\' ?>"
                   href="<?= base_url(\'/warehouse/purchase-orders/wh-verification\') ?>"
                   data-search-terms="po verification verify purchase order warehouse">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-link-text"><?= lang(\'App.po_verification\') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Surat Jalan -->
            <?php if (can_view(\'warehouse\')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), \'service/unit_audit/movements\') !== false) ? \'active\' : \'\' ?>" href="<?= base_url(\'/service/unit_audit/movements\') ?>"
                   data-search-terms="surat jalan movement transfer">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text">Surat Jalan</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PERIZINAN DIVISION -->';

$newContent = str_replace($search, $replace, $content);

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "Surat Jalan added to regular Warehouse section!\n";
} else {
    echo "Pattern not found - trying alternative...\n";

    // Try alternative pattern with different whitespace
    $search2 = '            <!-- PO Verification -->
            <?php if (can_view(\'warehouse\')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), \'warehouse/purchase-orders/wh-verification\') !== false ? \'active\' : \'\' ?>"
                   href="<?= base_url(\'/warehouse/purchase-orders/wh-verification\') ?>"
                   data-search-terms="po verification verify purchase order warehouse">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-link-text"><?= lang(\'App.po_verification\') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PERIZINAN DIVISION -->';

    $replace2 = '            <!-- PO Verification -->
            <?php if (can_view(\'warehouse\')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), \'warehouse/purchase-orders/wh-verification\') !== false ? \'active\' : \'\' ?>"
                   href="<?= base_url(\'/warehouse/purchase-orders/wh-verification\') ?>"
                   data-search-terms="po verification verify purchase order warehouse">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-link-text"><?= lang(\'App.po_verification\') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Surat Jalan -->
            <?php if (can_view(\'warehouse\')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), \'service/unit_audit/movements\') !== false) ? \'active\' : \'\' ?>" href="<?= base_url(\'/service/unit_audit/movements\') ?>"
                   data-search-terms="surat jalan movement transfer">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text">Surat Jalan</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PERIZINAN DIVISION -->';

    $newContent2 = str_replace($search2, $replace2, $content);

    if ($newContent2 !== $content) {
        file_put_contents($file, $newContent2);
        echo "Surat Jalan added with alternative pattern!\n";
    } else {
        echo "Still not found. Let me check the actual content...\n";
    }
}
