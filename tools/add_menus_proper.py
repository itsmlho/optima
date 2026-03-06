#!/usr/bin/env python3
"""
Add Unit Audit and Surat Jalan menus to sidebar
"""

import re

file_path = 'c:/laragon/www/optima/app/Views/layouts/sidebar_new.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add Unit Audit to regular Service section (after Area & Employee Management)
# Pattern: find the area management section closing, then add Unit Audit before OPERATIONAL DIVISION
unit_audit_pattern = r'''(            <!-- Area & Employee Management -->
            <?php if \(can_view\('service'\)\): ?>
            <li class="nav-item">
                <a class="nav-link.*?</a>
            </li>
            <?php endif; ?>
)(\n\n            <?php endif; ?>\n\n            <!-- OPERATIONAL DIVISION -->)'''

unit_audit_replacement = r'''\1
            <!-- Unit Audit -->
            <?php if (can_view('service')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'service/unit_audit') !== false && strpos(current_url(), 'movements') === false) ? 'active' : '' ?>" href="<?= base_url('/service/unit_audit') ?>"
                   data-search-terms="unit audit location mismatch">
                    <i class="fas fa-search"></i>
                    <span class="nav-link-text">Unit Audit</span>
                </a>
            </li>
            <?php endif; ?>
\2'''

content = re.sub(unit_audit_pattern, unit_audit_replacement, content, flags=re.DOTALL)

# 2. Add Surat Jalan to regular Warehouse section (after PO Verification)
surat_jalan_pattern = r'''(            <!-- PO Verification -->
            <?php if \(can_view\('warehouse'\)\): ?>
            <li class="nav-item">
                <a class="nav-link.*?</a>
            </li>
            <?php endif; ?>
)(\n\n            <!-- PERIZINAN DIVISION -->)'''

surat_jalan_replacement = r'''\1
            <!-- Surat Jalan -->
            <?php if (can_view('warehouse')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'service/unit_audit/movements') !== false) ? 'active' : '' ?>" href="<?= base_url('/service/unit_audit/movements') ?>"
                   data-search-terms="surat jalan movement transfer">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text">Surat Jalan</span>
                </a>
            </li>
            <?php endif; ?>
\2'''

content = re.sub(surat_jalan_pattern, surat_jalan_replacement, content, flags=re.DOTALL)

# 3. Add Unit Audit to collapsed Service section
collapsed_service_pattern = r'''(                    <?php if \(canNavigateTo\('service', 'area'\)\): ?>
                    <a href="[^"]*service/area-management[^"]*" class="nav-dropdown-item[^"]*">.*?</a>
                    <?php endif; ?>
)(\n                </div>\n            </li>\n            <?php endif; ?>\n\n            <!-- Supply Chain Management -->)'''

collapsed_service_replacement = r'''\1
                    <?php if (can_view('service')): ?>
                    <a href="<?= base_url('/service/unit_audit') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/unit_audit') !== false && strpos(current_url(), 'movements') === false) ? 'active' : '' ?>">
                        <i class="fas fa-search"></i> Unit Audit
                    </a>
                    <?php endif; ?>
\2'''

content = re.sub(collapsed_service_pattern, collapsed_service_replacement, content, flags=re.DOTALL)

# 4. Add Surat Jalan to collapsed Warehouse section
collapsed_warehouse_pattern = r'''(                    <?php if \(canNavigateTo\('warehouse', 'po_verification'\)\): ?>
                    <a href="[^"]*warehouse/purchase-orders/wh-verification[^"]*" class="nav-dropdown-item[^"]*">.*?</a>
                    <?php endif; ?>
)(\n                </div>\n            </li>\n            <?php endif; ?>\n\n            <!-- Finance & Accounting -->)'''

collapsed_warehouse_replacement = r'''\1
                    <?php if (can_view('warehouse')): ?>
                    <a href="<?= base_url('/service/unit_audit/movements') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/unit_audit/movements') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-truck"></i> Surat Jalan
                    </a>
                    <?php endif; ?>
\2'''

content = re.sub(collapsed_warehouse_pattern, collapsed_warehouse_replacement, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Done! Menus added to sidebar.")
print("- Unit Audit: Service section (regular & collapsed)")
print("- Surat Jalan: Warehouse section (regular & collapsed)")
