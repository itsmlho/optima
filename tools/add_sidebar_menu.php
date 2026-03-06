<?php
// Add Unit Audit menu to sidebar

$file = 'c:/laragon/www/optima/app/Views/layouts/sidebar_new.php';
$content = file_get_contents($file);

$search = "                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Supply Chain Management -->";

$replace = "                    <?php if (canNavigateTo('service', 'unitaudit')): ?>
                    <a href=\"<?= base_url('/service/unit_audit') ?>\" class=\"nav-dropdown-item <?= (strpos(current_url(), 'service/unit_audit') !== false && strpos(current_url(), 'movements') === false) ? 'active' : '' ?>\">
                        <i class=\"fas fa-search\"></i> Unit Audit
                    </a>
                    <a href=\"<?= base_url('/service/unit_audit/movements') ?>\" class=\"nav-dropdown-item <?= (strpos(current_url(), 'service/unit_audit/movements') !== false) ? 'active' : '' ?>\">
                        <i class=\"fas fa-truck\"></i> Surat Jalan
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Supply Chain Management -->";

$newContent = str_replace($search, $replace, $content);

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "Menu added successfully!\n";
} else {
    echo "Pattern not found - menu may already exist or file changed\n";
}
