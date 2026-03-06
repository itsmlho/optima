#!/usr/bin/env python3
"""
Add Unit Audit and Surat Jalan menus to sidebar - Version 2
"""

import re

file_path = 'c:/laragon/www/optima/app/Views/layouts/sidebar_new.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add Unit Audit to regular Service section
# Pattern: after Area & Employee Management closing, before OPERATIONAL DIVISION
unit_audit_pattern = r'''(            <!-- Area & Employee Management -->
            <?php if \(can_view\('service'\)\): ?>
            <li class="nav-item">
                <a class="nav-link[^>]*>[^<]*</a>
            </li>
            <?php endif; ?>

            <?php endif; ?>

            <!-- OPERATIONAL DIVISION -->)'''

unit_audit_replacement = r'''            <!-- Area & Employee Management -->
            <?php if (can_view('service')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'service/area-management') !== false) ? 'active' : '' ?>" href="<?= base_url('/service/area-management') ?>"
                   data-search-terms="area staff employee management service">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="nav-link-text"><?= lang('App.area_employee_management') ?></span>
                </a>
            </li>
            <?php endif; ?>

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


            <?php endif; ?>

            <!-- OPERATIONAL DIVISION -->'''

result = re.sub(unit_audit_pattern, unit_audit_replacement, content, flags=re.DOTALL)

if result != content:
    content = result
    print("Unit Audit added to Service section")
else:
    print("Unit Audit pattern not matched")

# 2. Add Surat Jalan to regular Warehouse section
surat_jalan_pattern = r'''(            <!-- PO Verification -->
            <?php if \(can_view\('warehouse'\)\): ?>
            <li class="nav-item">
                <a class="nav-link[^>]*>[^<]*</a>
            </li>
            <?php endif; ?>

            <!-- PERIZINAN DIVISION -->)'''

surat_jalan_replacement = r'''            <!-- PO Verification -->
            <?php if (can_view('warehouse')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'warehouse/purchase-orders/wh-verification') !== false ? 'active' : '' ?>"
                   href="<?= base_url('/warehouse/purchase-orders/wh-verification') ?>"
                   data-search-terms="po verification verify purchase order warehouse">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-link-text"><?= lang('App.po_verification') ?></span>
                </a>
            </li>
            <?php endif; ?>

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

            <!-- PERIZINAN DIVISION -->'''

result = re.sub(surat_jalan_pattern, surat_jalan_replacement, content, flags=re.DOTALL)

if result != content:
    content = result
    print("Surat Jalan added to Warehouse section")
else:
    print("Surat Jalan pattern not matched")

# 3. Add Unit Audit to collapsed Service section
collapsed_service_pattern = r'''(                    <?php if \(canNavigateTo\('service', 'area'\)\): ?>
                    <a href="[^"]*" class="nav-dropdown-item[^>]*>[^<]*</a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Supply Chain Management -->)'''

collapsed_service_replacement = r'''                    <?php if (canNavigateTo('service', 'area')): ?>
                    <a href="<?= base_url('/service/area-management') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/area-management') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-map-marked-alt"></i> <?= lang('App.area_management') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (can_view('service')): ?>
                    <a href="<?= base_url('/service/unit_audit') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/unit_audit') !== false && strpos(current_url(), 'movements') === false) ? 'active' : '' ?>">
                        <i class="fas fa-search"></i> Unit Audit
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Supply Chain Management -->'''

result = re.sub(collapsed_service_pattern, collapsed_service_replacement, content, flags=re.DOTALL)

if result != content:
    content = result
    print("Unit Audit added to collapsed Service section")
else:
    print("Collapsed Service pattern not matched")

# 4. Add Surat Jalan to collapsed Warehouse section
collapsed_warehouse_pattern = r'''(                    <?php if \(canNavigateTo\('warehouse', 'po_verification'\)\): ?>
                    <a href="[^"]*" class="nav-dropdown-item[^>]*>[^<]*</a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Finance & Accounting -->)'''

collapsed_warehouse_replacement = r'''                    <?php if (canNavigateTo('warehouse', 'po_verification')): ?>
                    <a href="<?= base_url('/warehouse/purchase-orders/wh-verification') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'warehouse/purchase-orders/wh-verification') !== false ? 'active' : '' ?>">
                        <i class="fas fa-clipboard-check"></i> <?= lang('App.po_verification') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (can_view('warehouse')): ?>
                    <a href="<?= base_url('/service/unit_audit/movements') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/unit_audit/movements') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-truck"></i> Surat Jalan
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Finance & Accounting -->'''

result = re.sub(collapsed_warehouse_pattern, collapsed_warehouse_replacement, content, flags=re.DOTALL)

if result != content:
    content = result
    print("Surat Jalan added to collapsed Warehouse section")
else:
    print("Collapsed Warehouse pattern not matched")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("\nDone!")
