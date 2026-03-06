<?php
/**
 * Add Unit Audit and Surat Jalan menus to collapsed sidebar sections
 */

$file = 'c:/laragon/www/optima/app/Views/layouts/sidebar_new.php';
$content = file_get_contents($file);

// 1. Add Unit Audit to collapsed Service section (after area_management)
$collapsedService = '                    <?php if (canNavigateTo(\'service\', \'area\')): ?>
                    <a href="<?= base_url(\'/service/area-management\') ?>" class="nav-dropdown-item <?= (strpos(current_url(), \'service/area-management\') !== false) ? \'active\' : \'\' ?>">
                        <i class="fas fa-map-marked-alt"></i> <?= lang(\'App.area_management\') ?>
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Supply Chain Management -->';

$collapsedServiceReplace = '                    <?php if (canNavigateTo(\'service\', \'area\')): ?>
                    <a href="<?= base_url(\'/service/area-management\') ?>" class="nav-dropdown-item <?= (strpos(current_url(), \'service/area-management\') !== false) ? \'active\' : \'\' ?>">
                        <i class="fas fa-map-marked-alt"></i> <?= lang(\'App.area_management\') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (can_view(\'service\')): ?>
                    <a href="<?= base_url(\'/service/unit_audit\') ?>" class="nav-dropdown-item <?= (strpos(current_url(), \'service/unit_audit\') !== false && strpos(current_url(), \'movements\') === false) ? \'active\' : \'\' ?>">
                        <i class="fas fa-search"></i> Unit Audit
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Supply Chain Management -->';

$content = str_replace($collapsedService, $collapsedServiceReplace, $content);

// 2. Add Surat Jalan to collapsed Warehouse section (after PO Verification)
$collapsedWarehouse = '                    <?php if (canNavigateTo(\'warehouse\', \'po_verification\')): ?>
                    <a href="<?= base_url(\'/warehouse/purchase-orders/wh-verification\') ?>" class="nav-dropdown-item <?= strpos(current_url(), \'warehouse/purchase-orders/wh-verification\') !== false ? \'active\' : \'\' ?>">
                        <i class="fas fa-clipboard-check"></i> <?= lang(\'App.po_verification\') ?>
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Finance & Accounting -->';

$collapsedWarehouseReplace = '                    <?php if (canNavigateTo(\'warehouse\', \'po_verification\')): ?>
                    <a href="<?= base_url(\'/warehouse/purchase-orders/wh-verification\') ?>" class="nav-dropdown-item <?= strpos(current_url(), \'warehouse/purchase-orders/wh-verification\') !== false ? \'active\' : \'\' ?>">
                        <i class="fas fa-clipboard-check"></i> <?= lang(\'App.po_verification\') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (can_view(\'warehouse\')): ?>
                    <a href="<?= base_url(\'/service/unit_audit/movements\') ?>" class="nav-dropdown-item <?= (strpos(current_url(), \'service/unit_audit/movements\') !== false) ? \'active\' : \'\' ?>">
                        <i class="fas fa-truck"></i> Surat Jalan
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Finance & Accounting -->';

$content = str_replace($collapsedWarehouse, $collapsedWarehouseReplace, $content);

// Save the file
file_put_contents($file, $content);

echo "Done! Added menus to collapsed sidebar sections.\n";
