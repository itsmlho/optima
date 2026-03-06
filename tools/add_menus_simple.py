#!/usr/bin/env python3
"""
Add Unit Audit and Surat Jalan menus to sidebar - Simple string replace
"""

file_path = 'c:/laragon/www/optima/app/Views/layouts/sidebar_new.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add Unit Audit to regular Service section (after Area & Employee Management)
old1 = '''            <!-- Area & Employee Management -->
            <?php if (can_view('service')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'service/area-management') !== false) ? 'active' : '' ?>" href="<?= base_url('/service/area-management') ?>"
                   data-search-terms="area staff employee management service">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="nav-link-text"><?= lang('App.area_employee_management') ?></span>
                </a>
            </li>
            <?php endif; ?>


            <?php endif; ?>

            <!-- OPERATIONAL DIVISION -->'''

new1 = '''            <!-- Area & Employee Management -->
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

if old1 in content:
    content = content.replace(old1, new1)
    print("Unit Audit added to Service section")
else:
    print("Unit Audit pattern not found")

# 2. Add Surat Jalan to regular Warehouse section
old2 = '''            <!-- PO Verification -->
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

            <!-- PERIZINAN DIVISION -->'''

new2 = '''            <!-- PO Verification -->
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

if old2 in content:
    content = content.replace(old2, new2)
    print("Surat Jalan added to Warehouse section")
else:
    print("Surat Jalan pattern not found")

# 3. Add Unit Audit to collapsed Service section
old3 = '''                    <?php if (canNavigateTo('service', 'area')): ?>
                    <a href="<?= base_url('/service/area-management') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/area-management') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-map-marked-alt"></i> <?= lang('App.area_management') ?>
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Supply Chain Management -->'''

new3 = '''                    <?php if (canNavigateTo('service', 'area')): ?>
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

if old3 in content:
    content = content.replace(old3, new3)
    print("Unit Audit added to collapsed Service section")
else:
    print("Collapsed Service pattern not found")

# 4. Add Surat Jalan to collapsed Warehouse section
old4 = '''                    <?php if (canNavigateTo('warehouse', 'po_verification')): ?>
                    <a href="<?= base_url('/warehouse/purchase-orders/wh-verification') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'warehouse/purchase-orders/wh-verification') !== false ? 'active' : '' ?>">
                        <i class="fas fa-clipboard-check"></i> <?= lang('App.po_verification') ?>
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Finance & Accounting -->'''

new4 = '''                    <?php if (canNavigateTo('warehouse', 'po_verification')): ?>
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

if old4 in content:
    content = content.replace(old4, new4)
    print("Surat Jalan added to collapsed Warehouse section")
else:
    print("Collapsed Warehouse pattern not found")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("\nDone!")
