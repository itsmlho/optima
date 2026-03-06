<?php
/**
 * Add Unit Audit and Surat Jalan menus to sidebar
 */

$file = 'c:/laragon/www/optima/app/Views/layouts/sidebar_new.php';
$content = file_get_contents($file);

// 1. Add Unit Audit menu to Service section (after Area & Employee Management)
$unitAuditService = '<!-- Area & Employee Management -->
            <?php if (can_view(\'service\')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), \'service/area-management\') !== false) ? \'active\' : \'\' ?>" href="<?= base_url(\'/service/area-management\') ?>"
                   data-search-terms="area staff employee management service">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="nav-link-text"><?= lang(\'App.area_employee_management\') ?></span>
                </a>
            </li>
            <?php endif; ?>


            <?php endif; ?>

            <!-- OPERATIONAL DIVISION -->';

$unitAuditServiceReplace = '<!-- Area & Employee Management -->
            <?php if (can_view(\'service\')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), \'service/area-management\') !== false) ? \'active\' : \'\' ?>" href="<?= base_url(\'/service/area-management\') ?>"
                   data-search-terms="area staff employee management service">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="nav-link-text"><?= lang(\'App.area_employee_management\') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Unit Audit -->
            <?php if (can_view(\'service\')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), \'service/unit_audit\') !== false && strpos(current_url(), \'movements\') === false) ? \'active\' : \'\' ?>" href="<?= base_url(\'/service/unit_audit\') ?>"
                   data-search-terms="unit audit location mismatch">
                    <i class="fas fa-search"></i>
                    <span class="nav-link-text">Unit Audit</span>
                </a>
            </li>
            <?php endif; ?>


            <?php endif; ?>

            <!-- OPERATIONAL DIVISION -->';

$content = str_replace($unitAuditService, $unitAuditServiceReplace, $content);

// 2. Add Surat Jalan menu to Warehouse section (after PO Verification)
$suratJalanWarehouse = '            <!-- PO Verification -->
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

$suratJalanWarehouseReplace = '            <!-- PO Verification -->
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

            <!-- Surat Jalan (Movements) -->
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

$content = str_replace($suratJalanWarehouse, $suratJalanWarehouseReplace, $content);

// Save the file
file_put_contents($file, $content);

echo "Done!\n";
echo "Unit Audit added to Service section\n";
echo "Surat Jalan added to Warehouse section\n";
