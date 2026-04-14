<?php
/**
 * OPTIMA Sidebar — CodingNepal Style
 * Floating, expand/collapse, dropdown click, state di localStorage.
 * Struktur: aside.sidebar > nav.sidebar-nav > ul.nav-list.primary-nav
 *           icon = Material Symbols Rounded
 * Permission: can_view(), canNavigateTo() dari permission_helper
 * PT Sarana Mitra Luas Tbk — OPTIMA System
 */
helper('permission_helper');
?>

<aside id="sidebar" class="sidebar collapsed" aria-label="Navigasi utama">
    <a href="<?= base_url('/welcome') ?>" class="sidebar-brand" aria-label="OPTIMA - Beranda">
        <img src="<?= base_url('assets/images/logo-optima.ico') ?>" alt="" class="sidebar-brand-logo">
        <span class="sidebar-brand-text">OPTIMA</span>
    </a>
    <nav class="sidebar-nav">
        <ul class="nav-list primary-nav">

            <!-- ── Dashboard ─────────────────────────────────────────── -->
            <li class="nav-item">
                <a href="<?= base_url('/dashboard') ?>" class="nav-link">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span class="nav-label">Dashboard</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Dashboard</span></li>
                    <li class="nav-item"><a href="<?= base_url('/dashboard') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">dashboard</span><span class="drop-label">Dashboard</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/operational/tracking') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">local_shipping</span><span class="drop-label">Tracking</span></a></li>
                </ul>
            </li>

            <!-- ── Marketing ─────────────────────────────────────────── -->
            <?php if (hasModuleAccess('marketing')): ?>
            <?php
                $cur = current_url();
                $isMarketingSection = (strpos($cur, 'marketing/') !== false);
                $isMarketingQuotations = (strpos($cur, 'marketing/quotations') !== false);
                $isMarketingCustomer = (strpos($cur, 'marketing/customer-management') !== false);
                $isMarketingKontrak = (strpos($cur, 'marketing/kontrak') !== false || strpos($cur, 'marketing/rental') !== false);
                $isMarketingSpk = (strpos($cur, 'marketing/spk') !== false);
                $isMarketingDi = (strpos($cur, 'marketing/di') !== false);
                $isMarketingAudit = (strpos($cur, 'marketing/audit-approval') !== false);
            ?>
            <li class="nav-item dropdown-container<?= $isMarketingSection ? ' open' : '' ?>">
                <a href="#" class="nav-link dropdown-toggle<?= $isMarketingSection ? ' active' : '' ?>">
                    <span class="material-symbols-rounded">store</span>
                    <span class="nav-label">Marketing</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Marketing</span></li>
                    <?php if (canNavigateTo('marketing', 'quotation')): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('/marketing/quotations') ?>" class="nav-link dropdown-link<?= $isMarketingQuotations ? ' active' : '' ?>">
                            <span class="material-symbols-rounded drop-icon">description</span>
                            <span class="drop-label">Quotations</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'customer')): ?>
                    <li class="nav-item"><a href="<?= base_url('/marketing/customer-management') ?>" class="nav-link dropdown-link<?= $isMarketingCustomer ? ' active' : '' ?>"><span class="material-symbols-rounded drop-icon">group</span><span class="drop-label">Customer Management</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'contract')): ?>
                    <li class="nav-item"><a href="<?= base_url('/marketing/rental') ?>" class="nav-link dropdown-link<?= $isMarketingKontrak ? ' active' : '' ?>"><span class="material-symbols-rounded drop-icon">contract</span><span class="drop-label">Rental</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'spk')): ?>
                    <li class="nav-item"><a href="<?= base_url('/marketing/spk') ?>" class="nav-link dropdown-link<?= $isMarketingSpk ? ' active' : '' ?>"><span class="material-symbols-rounded drop-icon">assignment</span><span class="drop-label">SPK Marketing</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'delivery_instructions')): ?>
                    <li class="nav-item"><a href="<?= base_url('/marketing/di') ?>" class="nav-link dropdown-link<?= $isMarketingDi ? ' active' : '' ?>"><span class="material-symbols-rounded drop-icon">local_shipping</span><span class="drop-label">Delivery Instructions</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'audit_approval')): ?>
                    <li class="nav-item"><a href="<?= base_url('/marketing/audit-approval') ?>" class="nav-link dropdown-link<?= $isMarketingAudit ? ' active' : '' ?>"><span class="material-symbols-rounded drop-icon">check_circle</span><span class="drop-label">Audit Approval</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ── Service ────────────────────────────────────────────── -->
            <?php if (hasModuleAccess('service')): ?>
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">build</span>
                    <span class="nav-label">Service</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Service</span></li>
                    <?php if (canNavigateTo('service', 'work_order') || canNavigateTo('service', 'spk_service')): ?>
                    <li class="nav-item"><a href="<?= base_url('/service/spk_service') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">build_circle</span><span class="drop-label">SPK Service</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/service/work-orders') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">work</span><span class="drop-label">Work Orders</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('service', 'pmps')): ?>
                    <li class="nav-item"><a href="<?= base_url('/service/pmps') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">checklist</span><span class="drop-label">PMPS</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('service', 'unit_audit')): ?>
                    <li class="nav-item"><a href="<?= base_url('/service/unit-audit') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">fact_check</span><span class="drop-label">Unit Audit</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/service/unit-verification') ?>" class="nav-link dropdown-link"><i class="fas fa-clipboard-check drop-icon" aria-hidden="true"></i><span class="drop-label">Unit Verification</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('service', 'area_management')): ?>
                    <li class="nav-item"><a href="<?= base_url('/service/area-management') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">map</span><span class="drop-label">Area Management</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('service', 'customer_location')): ?>
                    <li class="nav-item"><a href="<?= base_url('/service/customer-locations') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">location_on</span><span class="drop-label">Customer Locations</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ── Operational ────────────────────────────────────────── -->
            <?php if (hasModuleAccess('operational')): ?>
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">local_shipping</span>
                    <span class="nav-label">Operational</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Operational</span></li>
                    <?php if (canNavigateTo('operational', 'delivery_process')): ?>
                    <li class="nav-item"><a href="<?= base_url('/operational/delivery') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">delivery_truck_speed</span><span class="drop-label">Delivery Process</span></a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a href="<?= base_url('/operational/tracking') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">pin_drop</span><span class="drop-label">Tracking</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/operational/temporary-units-report') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">pending_actions</span><span class="drop-label">Unit Sementara</span></a></li>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ── Finance ────────────────────────────────────────────── -->
            <?php if (hasModuleAccess('accounting')): ?>
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">account_balance</span>
                    <span class="nav-label">Finance</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Finance</span></li>
                    <?php if (canNavigateTo('accounting', 'invoice')): ?>
                    <li class="nav-item"><a href="<?= base_url('/finance/invoices') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">receipt_long</span><span class="drop-label">Invoices</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('accounting', 'payment_validation')): ?>
                    <li class="nav-item"><a href="<?= base_url('/finance/payments') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">payments</span><span class="drop-label">Payments</span></a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a href="<?= base_url('/finance/expenses') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">money_off</span><span class="drop-label">Expenses</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/finance/reports') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">summarize</span><span class="drop-label">Finance Reports</span></a></li>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ── Purchasing ─────────────────────────────────────────── -->
            <?php if (hasModuleAccess('purchasing')): ?>
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">shopping_cart</span>
                    <span class="nav-label">Purchasing</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Purchasing</span></li>
                    <?php if (canNavigateTo('purchasing', 'purchasing')): ?>
                    <li class="nav-item"><a href="<?= base_url('/purchasing') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">shopping_cart</span><span class="drop-label">PO Unit & Attachment</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/warehouse/purchase-orders/rejected-items') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">block</span><span class="drop-label">PO Reject</span></a></li>
                    <?php endif; ?>
                    <?php if (hasModuleAccess('purchasing')): ?>
                    <li class="nav-item"><a href="<?= base_url('/purchasing/supplier-management-page') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">business</span><span class="drop-label">Supplier Management</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ── Warehouse ──────────────────────────────────────────── -->
            <?php if (hasModuleAccess('warehouse')): ?>
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">warehouse</span>
                    <span class="nav-label">Warehouse</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Warehouse</span></li>
                    <?php if (canNavigateTo('warehouse', 'inventory_unit')): ?>
                    <li class="nav-item"><a href="<?= base_url('/warehouse/inventory/unit') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">inventory_2</span><span class="drop-label">Unit Inventory</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'attachment_inventory')): ?>
                    <li class="nav-item"><a href="<?= base_url('/warehouse/inventory/attachments') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">attach_file</span><span class="drop-label">Attachment Inventory</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'sparepart_inventory')): ?>
                    <li class="nav-item"><a href="<?= base_url('/warehouse/inventory/invent_sparepart') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">category</span><span class="drop-label">Sparepart Inventory</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'sparepart_usage')): ?>
                    <li class="nav-item"><a href="<?= base_url('/warehouse/sparepart-usage') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">construction</span><span class="drop-label">Sparepart Usage</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'po_verification')): ?>
                    <li class="nav-item"><a href="<?= base_url('/warehouse/purchase-orders/wh-verification') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">fact_check</span><span class="drop-label">PO Verification</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'inventory_unit')): ?>
                    <li class="nav-item"><a href="<?= base_url('/warehouse/returned-verifications') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">assignment_turned_in</span><span class="drop-label">Returned Verifications</span></a></li>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'movements')): ?>
                    <li class="nav-item"><a href="<?= base_url('/warehouse/movements') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">swap_horiz</span><span class="drop-label">Surat Jalan</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ── Perizinan ──────────────────────────────────────────── -->
            <?php if (hasModuleAccess('perizinan')): ?>
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">shield</span>
                    <span class="nav-label">Perizinan</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Perizinan</span></li>
                    <?php if (canNavigateTo('perizinan', 'silo')): ?>
                    <li class="nav-item"><a href="<?= base_url('/perizinan/silo') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">security</span><span class="drop-label">Izin SILO</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/perizinan/emisi') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">eco</span><span class="drop-label">Izin Emisi</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ── Reports ────────────────────────────────────────────── -->
            <?php if (hasModuleAccess('reports')): ?>
            <li class="nav-item">
                <a href="<?= base_url('/reports') ?>" class="nav-link dropdown-link">
                    <span class="material-symbols-rounded drop-icon">bar_chart</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- ── Master Data ────────────────────────────────────────── -->
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">database</span>
                    <span class="nav-label">Master Data</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Master Data</span></li>
                    <?php if (hasPermission('view_master_data') || hasPermission('master_data.index.navigation')): ?>
                    <li class="nav-item"><a href="<?= base_url('/master-data') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">table_view</span><span class="drop-label">Master Data Center</span></a></li>
                    <?php endif; ?>
                    <li class="nav-item"><span class="nav-link dropdown-link text-muted"><span class="material-symbols-rounded drop-icon">info</span><span class="drop-label">Entity CRUD terpusat</span></span></li>
                </ul>
            </li>

            <!-- ── Administration ─────────────────────────────────────── -->
            <?php if (hasModuleAccess('admin')): ?>
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">settings</span>
                    <span class="nav-label">Administration</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item nav-item-flyout-title"><span class="nav-link dropdown-title" aria-hidden="true">Administration</span></li>
                    <li class="nav-item"><a href="<?= base_url('/admin') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">admin_panel_settings</span><span class="drop-label">System Admin</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/admin/advanced-users') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">manage_accounts</span><span class="drop-label">User Management</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/admin/activity-log') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">history</span><span class="drop-label">Activity Log</span></a></li>
                    <?php if (canNavigateTo('settings', 'role') || canNavigateTo('settings', 'permission') || canNavigateTo('settings', 'user')): ?>
                    <li class="nav-item"><a href="<?= base_url('/permission-management/role-permissions') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">shield</span><span class="drop-label">Role Permissions</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/permission-management/user-permissions') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">person_check</span><span class="drop-label">User Permissions</span></a></li>
                    <li class="nav-item"><a href="<?= base_url('/permission-management/audit-trail') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">gavel</span><span class="drop-label">Audit Trail</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

        </ul>
    </nav>
</aside>
