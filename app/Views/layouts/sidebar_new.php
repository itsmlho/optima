<?php 
/**
 * Enhanced Sidebar Navigation Structure
 * Struktur baru berdasarkan rekomendasi peningkatan UX
 * PT Sarana Mitra Luas Tbk - OPTIMA System
 */

// Load permission helper functions
helper('permission_helper');
?>

<!-- Enhanced Sidebar with Advanced Features -->
<nav class="sidebar sidebar-enhanced" id="sidebar">
    
    <!-- Collapsed Menu Groups (Only visible when collapsed) -->
    <div class="sidebar-collapsed-menu" id="sidebarCollapsedMenu">
        <div class="collapsed-nav-container">
            <ul class="nav flex-column">
            
            <!-- Dashboard & Analytics -->
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="dashboard">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-link-text"><?= lang('App.analytics') ?></span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header"><?= lang('App.dashboard_analytics') ?></div>
                    <a href="<?= base_url('/dashboard') ?>" class="nav-dropdown-item <?= (current_url() === base_url('/') || current_url() === base_url('/dashboard') || strpos(current_url(), 'dashboard') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> <?= lang('App.dashboard') ?>
                    </a>
                    <a href="<?= base_url('/operational/tracking') ?>" class="nav-dropdown-item">
                        <i class="fas fa-truck"></i> <?= lang('App.tracking_delivery') ?>
                    </a>
                </div>
            </li>

            <!-- Customer Relationship Management -->
            <?php if (canNavigateTo('marketing', 'customer') || canNavigateTo('marketing', 'quotation') || canNavigateTo('marketing', 'spk') || canNavigateTo('marketing', 'delivery')): ?>
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="crm">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-link-text"><?= lang('App.crm') ?></span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header"><?= lang('App.customer_relationship_management') ?></div>
                    <?php if (canNavigateTo('marketing', 'quotation')): ?>
                    <a href="<?= base_url('/marketing/quotations') ?>" class="nav-dropdown-item">
                        <i class="fas fa-file-invoice-dollar"></i> <?= lang('Marketing.quotations') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'customer')): ?>
                    <a href="<?= base_url('/marketing/customer-management') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'customer-management') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-users"></i> <?= lang('Marketing.customer_management') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'quotation')): ?>
                    <a href="<?= base_url('/marketing/kontrak') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'marketing/kontrak') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-file-invoice"></i> Contracts & PO
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'spk')): ?>
                    <a href="<?= base_url('/marketing/spk') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'marketing/spk') !== false ? 'active' : '' ?>">
                        <i class="fas fa-file-contract"></i> <?= lang('App.work_orders_spk') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'delivery')): ?>
                    <a href="<?= base_url('/marketing/di') ?>" class="nav-dropdown-item">
                        <i class="fas fa-shipping-fast"></i> <?= lang('App.delivery_instructions_di') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('marketing', 'audit_approval')): ?>
                    <a href="<?= base_url('/marketing/audit-approval') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'marketing/audit-approval') !== false && strpos(current_url(), 'location') === false) ? 'active' : '' ?>">
                        <i class="fas fa-check-circle"></i> Audit Approval
                    </a>
                    <a href="<?= base_url('/marketing/audit-approval-location') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'marketing/audit-approval-location') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-map-marker-alt"></i> Approve Audit Unit
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Service & Maintenance -->
            <!-- Service Section -->
            <?php if (canNavigateTo('service', 'workorder') || canNavigateTo('service', 'pmps') || canNavigateTo('service', 'area') || canNavigateTo('service', 'user')): ?>
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="service">
                    <i class="fas fa-tools"></i>
                    <span class="nav-link-text"><?= lang('App.service') ?></span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header"><?= lang('App.service_maintenance') ?></div>
                    <?php if (canNavigateTo('service', 'workorder')): ?>
                    <a href="<?= base_url('/service/spk_service') ?>" class="nav-dropdown-item">
                        <i class="fas fa-clipboard-list"></i> <?= lang('App.work_orders_unit_prep') ?>
                    </a>
                    <a href="<?= base_url('/service/work-orders') ?>" class="nav-dropdown-item">
                        <i class="fas fa-wrench"></i> <?= lang('App.work_orders_complaint') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('service', 'pmps')): ?>
                    <a href="<?= base_url('/service/pmps') ?>" class="nav-dropdown-item">
                        <i class="fas fa-calendar-check"></i> <?= lang('App.preventive_maintenance_pmps') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('service', 'workorder')): ?>
                    <a href="<?= base_url('/service/unit-audit') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/unit-audit') !== false && strpos(current_url(), 'location') === false) ? 'active' : '' ?>">
                        <i class="fas fa-search"></i> Unit Audit
                    </a>
                    <a href="<?= base_url('/service/unit-audit/location') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/unit-audit/location') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-map-marker-alt"></i> Audit Unit per Lokasi
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('service', 'area')): ?>
                    <a href="<?= base_url('/service/area-management') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/area-management') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-map-marked-alt"></i> <?= lang('App.area_management') ?>
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Supply Chain Management -->
            <?php if (canNavigateTo('purchasing', 'po') || canNavigateTo('purchasing', 'po_sparepart') || canNavigateTo('purchasing', 'supplier') || canNavigateTo('operational', 'delivery')): ?>
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="supply-chain">
                    <i class="fas fa-shipping-fast"></i>
                    <span class="nav-link-text"><?= lang('App.supply_chain') ?></span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header"><?= lang('App.supply_chain_management') ?></div>
                    <?php if (canNavigateTo('purchasing', 'po')): ?>
                    <a href="<?= base_url('/purchasing') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'purchasing') !== false && strpos(current_url(), 'sparepart') === false && strpos(current_url(), 'supplier') === false ? 'active' : '' ?>">
                        <i class="fas fa-truck"></i> <?= lang('App.po_unit_attachment') ?>
                    </a>
                    <!-- <?php endif; ?>
                    <?php if (canNavigateTo('purchasing', 'po_sparepart')): ?>
                    <a href="<?= base_url('/purchasing/po-sparepart-list') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'po-sparepart') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tools"></i> <?= lang('App.po_sparepart') ?>
                    </a> -->
                    <?php endif; ?>
                    <?php if (canNavigateTo('purchasing', 'po')): ?>
                    <a href="<?= base_url('/warehouse/purchase-orders/rejected-items') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'rejected-items') !== false ? 'active' : '' ?>">
                        <i class="fas fa-times"></i> <?= lang('App.po_reject') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('purchasing', 'supplier')): ?>
                    <a href="<?= base_url('/purchasing/supplier-management-page') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'supplier-management') !== false ? 'active' : '' ?>">
                        <i class="fas fa-building"></i> <?= lang('App.supplier_management') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('operational', 'delivery')): ?>
                    <a href="<?= base_url('/operational/delivery') ?>" class="nav-dropdown-item">
                        <i class="fas fa-shipping-fast"></i> <?= lang('App.delivery_process') ?>
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Inventory & Warehouse -->
            <?php if (canNavigateTo('warehouse', 'unit_inventory') || canNavigateTo('warehouse', 'attachment_inventory') || canNavigateTo('warehouse', 'sparepart_inventory') || canNavigateTo('warehouse', 'sparepart_usage') || canNavigateTo('warehouse', 'po_verification')): ?>
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="warehouse">
                    <i class="fas fa-warehouse"></i>
                    <span class="nav-link-text"><?= lang('App.warehouse') ?></span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header"><?= lang('App.inventory_warehouse') ?></div>
                    <?php if (canNavigateTo('warehouse', 'unit_inventory')): ?>
                    <a href="<?= base_url('/warehouse/inventory/unit') ?>" class="nav-dropdown-item">
                        <i class="fas fa-truck"></i> <?= lang('App.unit_inventory') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'attachment_inventory')): ?>
                    <a href="<?= base_url('/warehouse/inventory/attachments') ?>" class="nav-dropdown-item">
                        <i class="fas fa-battery-half"></i> <?= lang('App.attachment_battery_inventory') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'sparepart_inventory')): ?>
                    <a href="<?= base_url('/warehouse/inventory/invent_sparepart') ?>" class="nav-dropdown-item">
                        <i class="fas fa-tools"></i> <?= lang('App.sparepart_inventory') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'sparepart_usage')): ?>
                    <a href="<?= base_url('/warehouse/sparepart-usage') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'warehouse/sparepart-usage') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tools"></i> <?= lang('App.sparepart_usage_returns') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'po_verification')): ?>
                    <a href="<?= base_url('/warehouse/purchase-orders/wh-verification') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'warehouse/purchase-orders/wh-verification') !== false ? 'active' : '' ?>">
                        <i class="fas fa-clipboard-check"></i> <?= lang('App.po_verification') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('warehouse', 'unit_inventory')): ?>
                    <a href="<?= base_url('/warehouse/movements') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'warehouse/movements') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-truck-moving"></i> Surat Jalan
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Finance & Accounting -->
            <?php if (canNavigateTo('accounting', 'invoice') || canNavigateTo('accounting', 'payment')): ?>
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="finance">
                    <i class="fas fa-chart-pie"></i>
                    <span class="nav-link-text"><?= lang('App.finance') ?></span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header"><?= lang('App.finance_accounting') ?></div>
                    <?php if (canNavigateTo('accounting', 'invoice')): ?>
                    <a href="<?= base_url('/finance/invoices') ?>" class="nav-dropdown-item">
                        <i class="fas fa-file-invoice"></i> <?= lang('App.invoice_management') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (canNavigateTo('accounting', 'payment')): ?>
                    <a href="<?= base_url('/finance/invoices') ?>" class="nav-dropdown-item">
                        <i class="fas fa-check-circle"></i> <?= lang('App.payment_validation') ?>
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Compliance & Permits -->
            <?php if (can_view('perizinan')): ?>
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="compliance">
                    <i class="fas fa-shield-alt"></i>
                    <span class="nav-link-text"><?= lang('App.compliance') ?></span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header"><?= lang('App.compliance_permits') ?></div>
                    <a href="<?= base_url('/perizinan/silo') ?>" class="nav-dropdown-item">
                        <i class="fa-solid fa-shield-halved"></i> <?= lang('App.silo_permit') ?>
                    </a>
                    <a href="<?= base_url('/perizinan/emisi') ?>" class="nav-dropdown-item">
                        <i class="fas fa-leaf"></i> <?= lang('App.emission_permit') ?>
                    </a>
                </div>
            </li>
            <?php endif; ?>

            <!-- System Administration -->
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="admin">
                    <i class="fas fa-cogs"></i>
                    <span class="nav-link-text"><?= lang('App.system') ?></span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header"><?= lang('App.system_administration') ?></div>
                    <?php if (can_view('admin')): ?>
                    <a href="<?= base_url('/admin') ?>" class="nav-dropdown-item <?= (strpos(current_url(), '/admin') !== false && strpos(current_url(), 'activity-log') === false && strpos(current_url(), 'advanced-users') === false && strpos(current_url(), 'roles') === false && strpos(current_url(), 'permissions') === false) ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> <?= lang('App.administration') ?>
                    </a>
                    <?php else: ?>
                    <div class="nav-dropdown-item text-muted">
                        <i class="fas fa-lock"></i> <?= lang('App.access_restricted') ?>
                    </div>
                    <?php endif; ?>
                </div>
            </li>

        </ul>
        </div>
    </div>

    <!-- Regular Menu (Visible when expanded) -->
    <div class="sidebar-nav">
        <ul class="nav flex-column">
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= (current_url() === base_url('/') || current_url() === base_url('/dashboard') || strpos(current_url(), 'dashboard') !== false) ? 'active' : '' ?>" 
                   href="<?= base_url('/dashboard') ?>"
                   data-tooltip="<?= lang('App.dashboard') ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-link-text"><?= lang('App.dashboard') ?></span>
                </a>
            </li>

            <!-- Tracking Delivery -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/operational/tracking') ?>"
                   data-search-terms="tracking delivery pengiriman monitoring"
                   data-tooltip="<?= lang('App.tracking_delivery') ?>">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text"><?= lang('App.tracking_delivery') ?></span>
                </a>
            </li>


            <!-- MARKETING DIVISION -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading"><?= strtoupper(lang('App.marketing')) ?></div>
            </li>

            <!-- Quotations -->
            <?php if (canNavigateTo('marketing', 'quotation')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/quotations') ?>"
                   data-search-terms="marketing quotations proposals quotes"
                   data-tooltip="<?= lang('App.quotations') ?>">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span class="nav-link-text"><?= lang('App.quotations') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Audit Approval -->
            <?php if (canNavigateTo('marketing', 'audit_approval')): ?>
            <li class="nav-item">
                <a class="nav-link text-warning <?= (strpos(current_url(), 'marketing/audit-approval') !== false && strpos(current_url(), 'location') === false) ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/audit-approval') ?>"
                   data-search-terms="audit approval verifikasi unit customer"
                   data-tooltip="Audit Approval">
                    <i class="fas fa-check-double"></i>
                    <span class="nav-link-text">Audit Approval</span>
                </a>
            </li>
            
            <!-- Approve Audit Unit (per Lokasi) -->
            <li class="nav-item">
                <a class="nav-link text-warning <?= (strpos(current_url(), 'marketing/audit-approval-location') !== false) ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/audit-approval-location') ?>"
                   data-search-terms="approve audit unit per lokasi location marketing"
                   data-tooltip="Approve Audit Unit">
                    <i class="fas fa-map-marker-alt"></i>
                    <span class="nav-link-text">Approve Audit Unit</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Customer Management -->
            <?php if (canNavigateTo('marketing', 'customer')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'customer-management') !== false) ? 'active' : '' ?>" href="<?= base_url('/marketing/customer-management') ?>"
                   data-search-terms="customer management marketing"
                   data-tooltip="<?= lang('Marketing.customer_management') ?>">
                    <i class="fas fa-users"></i>
                    <span class="nav-link-text"><?= lang('Marketing.customer_management') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- SPK -->
            <?php if (canNavigateTo('marketing', 'spk')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'marketing/spk') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/spk') ?>"
                   data-search-terms="spk work orders surat perintah kerja"
                   data-tooltip="<?= lang('App.work_orders_spk') ?>">
                    <i class="fas fa-file-contract"></i>
                    <span class="nav-link-text"><?= lang('App.work_orders_spk') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Delivery Instructions -->
            <?php if (canNavigateTo('marketing', 'delivery')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/di') ?>"
                   data-search-terms="delivery instructions di"
                   data-tooltip="<?= lang('App.delivery_instructions_di') ?>">
                    <i class="fas fa-shipping-fast"></i>
                    <span class="nav-link-text"><?= lang('App.delivery_instructions_di') ?></span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- SERVICE DIVISION -->
            <?php if (can_view('service')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading"><?= strtoupper(lang('App.service')) ?></div>
            </li>

            <!-- SPK Service -->
            <?php if (canNavigateTo('service', 'workorder')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/spk_service') ?>"
                   data-search-terms="spk service unit preparation"
                   data-tooltip="<?= lang('App.work_orders_unit_prep') ?>">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="nav-link-text"><?= lang('App.work_orders_unit_prep') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PMPS -->
            <?php if (canNavigateTo('service', 'pmps')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/pmps') ?>"
                   data-search-terms="pmps preventive maintenance">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-link-text"><?= lang('App.preventive_maintenance_pmps') ?></span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Workorders -->
            <?php if (canNavigateTo('service', 'workorder')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/work-orders') ?>"
                   data-search-terms="workorder complaint keluhan">
                    <i class="fas fa-wrench"></i>
                    <span class="nav-link-text"><?= lang('App.work_orders') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Area & Employee Management -->
            <?php if (canNavigateTo('service', 'area')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'service/area-management') !== false) ? 'active' : '' ?>" href="<?= base_url('/service/area-management') ?>"
                   data-search-terms="area staff employee management service">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="nav-link-text"><?= lang('App.area_employee_management') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Unit Audit -->
            <?php if (canNavigateTo('service', 'workorder')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'service/unit-audit') !== false && strpos(current_url(), 'location') === false) ? 'active' : '' ?>" 
                   href="<?= base_url('/service/unit-audit') ?>"
                   data-search-terms="unit audit pemeriksaan service">
                    <i class="fas fa-search"></i>
                    <span class="nav-link-text">Unit Audit</span>
                </a>
            </li>
            
            <!-- Audit Unit per Lokasi -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'service/unit-audit/location') !== false) ? 'active' : '' ?>" 
                   href="<?= base_url('/service/unit-audit/location') ?>"
                   data-search-terms="audit unit per lokasi location service">
                    <i class="fas fa-map-marker-alt"></i>
                    <span class="nav-link-text">Audit Unit per Lokasi</span>
                </a>
            </li>
            <?php endif; ?>


            <?php endif; ?>

            <!-- OPERATIONAL DIVISION -->
            <?php if (can_view('operational')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading"><?= strtoupper(lang('App.operational')) ?></div>
            </li>

            <!-- Delivery Process -->
            <?php if (canNavigateTo('operational', 'delivery')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/operational/delivery') ?>"
                   data-search-terms="delivery process pengiriman">
                    <i class="fas fa-shipping-fast"></i>
                    <span class="nav-link-text"><?= lang('App.delivery_process') ?></span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- ACCOUNTING DIVISION -->
            <?php if (can_view('accounting')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading"><?= strtoupper(lang('App.accounting')) ?></div>
            </li>

            <!-- Invoice Management -->
            <?php if (canNavigateTo('accounting', 'invoice')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/finance/invoices') ?>"
                   data-search-terms="invoice management tagihan">
                    <i class="fas fa-file-invoice"></i>
                    <span class="nav-link-text"><?= lang('App.invoice_management') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Payment Validation -->
            <?php if (canNavigateTo('accounting', 'payment')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/finance/invoices') ?>"
                   data-search-terms="payment validation pembayaran">
                    <i class="fas fa-check-circle"></i>
                    <span class="nav-link-text"><?= lang('App.payment_validation') ?></span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
            
            <!-- PURCHASING DIVISION -->
            <?php if (can_view('purchasing')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading"><?= strtoupper(lang('App.purchasing')) ?></div>
            </li>

            <!-- PO Unit & Attachment -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'purchasing') !== false && strpos(current_url(), 'sparepart') === false && strpos(current_url(), 'supplier') === false ? 'active' : '' ?>" 
                   href="<?= base_url('/purchasing') ?>"
                   data-search-terms="purchasing po unit attachment battery charger">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text"><?= lang('App.po_unit_attachment') ?></span>
                </a>
            </li>

            <!-- PO Sparepart -->
            <!-- <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'po-sparepart') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/purchasing/po-sparepart-list') ?>"
                   data-search-terms="purchasing po sparepart parts">
                    <i class="fas fa-tools"></i>
                    <span class="nav-link-text"><?= lang('App.po_sparepart') ?></span>
                </a>
            </li> -->

            <!-- PO Reject -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'rejected-items') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/warehouse/purchase-orders/rejected-items') ?>"
                   data-search-terms="po reject rejection">
                    <i class="fas fa-times"></i>
                    <span class="nav-link-text"><?= lang('App.po_reject') ?></span>
                </a>
            </li>

            <!-- Supplier Management -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'supplier-management') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/purchasing/supplier-management-page') ?>"
                   data-search-terms="supplier vendor management">
                    <i class="fas fa-building"></i>
                    <span class="nav-link-text"><?= lang('App.supplier_management') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- WAREHOUSE DIVISION -->
            <?php if (can_view('warehouse')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading"><?= strtoupper(lang('App.warehouse')) ?> & <?= strtoupper(lang('App.assets')) ?></div>
            </li>

            <!-- Inventory -->
            <!-- Unit Inventory -->
            <?php if (canNavigateTo('warehouse', 'unit_inventory')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/warehouse/inventory/unit') ?>"
                   data-search-terms="inventory unit assets warehouse">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text"><?= lang('App.unit_inventory') ?></span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Attachment & Battery Inventory -->
            <?php if (canNavigateTo('warehouse', 'attachment_inventory')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/warehouse/inventory/attachments') ?>"
                   data-search-terms="inventory attachment battery warehouse">
                    <i class="fas fa-battery-half"></i>
                    <span class="nav-link-text"><?= lang('App.attachment_battery_inventory') ?></span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Sparepart Inventory -->
            <?php if (canNavigateTo('warehouse', 'sparepart_inventory')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/warehouse/inventory/invent_sparepart') ?>"
                   data-search-terms="inventory sparepart spare part warehouse">
                    <i class="fas fa-tools"></i>
                    <span class="nav-link-text"><?= lang('App.sparepart_inventory') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Sparepart Usage & Returns -->
            <?php if (canNavigateTo('warehouse', 'sparepart_usage')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'warehouse/sparepart-usage') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/warehouse/sparepart-usage') ?>"
                   data-search-terms="sparepart usage pemakaian pengembalian warehouse">
                    <i class="fas fa-tools me-2"></i>
                    <span class="nav-link-text"><?= lang('App.sparepart_usage_returns') ?></span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- PO Verification -->
            <?php if (canNavigateTo('warehouse', 'po_verification')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'warehouse/purchase-orders/wh-verification') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/warehouse/purchase-orders/wh-verification') ?>"
                   data-search-terms="po verification verify purchase order warehouse">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-link-text"><?= lang('App.po_verification') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Surat Jalan / Movement -->
            <?php if (canNavigateTo('warehouse', 'unit_inventory')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'warehouse/movements') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/warehouse/movements') ?>"
                   data-search-terms="surat jalan movement perpindahan unit warehouse">
                    <i class="fas fa-truck-moving"></i>
                    <span class="nav-link-text">Surat Jalan</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PERIZINAN DIVISION -->
            <?php if (can_view('perizinan')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading"><?= strtoupper(lang('App.licensing_permits')) ?></div>
            </li>

            <!-- SILO -->
            <?php if (canNavigateTo('perizinan', 'silo')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/perizinan/silo') ?>"
                   data-search-terms="silo izin layak operasi">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span class="nav-link-text"><?= lang('App.silo_permit') ?></span>
                </a>
            </li>

            <!-- EMISI -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/perizinan/emisi') ?>"
                   data-search-terms="emisi gas buang izin">
                    <i class="fas fa-leaf"></i>
                    <span class="nav-link-text"><?= lang('App.emission_permit') ?></span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Administration -->
            <?php if (can_view('admin')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading"><?= strtoupper(lang('App.administration')) ?></div>
            </li>

            <!-- Admin Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), '/admin') !== false && strpos(current_url(), 'activity-log') === false && strpos(current_url(), 'advanced-users') === false && strpos(current_url(), 'roles') === false && strpos(current_url(), 'permissions') === false && strpos(current_url(), 'permission-management') === false) ? 'active' : '' ?>" 
                   href="<?= base_url('/admin') ?>"
                   data-search-terms="admin dashboard system administration">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-link-text"><?= lang('App.administration') ?></span>
                </a>
            </li>

            <!-- Permission Management -->
            <?php if (canNavigateTo('settings', 'role') || canNavigateTo('settings', 'permission') || canNavigateTo('settings', 'user')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'permission-management') !== false) ? 'active' : '' ?>" 
                   href="#"
                   data-bs-toggle="collapse"
                   data-bs-target="#permissionSubmenu"
                   aria-expanded="false">
                    <i class="fas fa-shield-alt"></i>
                    <span class="nav-link-text">Permission Management</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <div class="collapse" id="permissionSubmenu">
                    <ul class="nav flex-column ms-3">
                        <?php if (canNavigateTo('settings', 'role')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'role-permissions') !== false) ? 'active' : '' ?>" 
                               href="<?= base_url('/permission-management/role-permissions') ?>">
                                <i class="fas fa-users-cog"></i>
                                <span class="nav-link-text">Role Permissions</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (canNavigateTo('settings', 'user')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'user-permissions') !== false) ? 'active' : '' ?>" 
                               href="<?= base_url('/permission-management/user-permissions') ?>">
                                <i class="fas fa-user-shield"></i>
                                <span class="nav-link-text">User Permissions</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (canNavigateTo('settings', 'permission')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'audit-trail') !== false) ? 'active' : '' ?>" 
                               href="<?= base_url('/permission-management/audit-trail') ?>">
                                <i class="fas fa-history"></i>
                                <span class="nav-link-text">Audit Trail</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
            <?php endif; ?>
            <?php endif; ?>

        </ul>
    </div>

    <!-- Simple User Status -->
    <div class="sidebar-user-status">
        <div class="d-flex align-items-center">
            <div class="user-avatar">
                <?= strtoupper(substr(session()->get('first_name') ?: 'A', 0, 1)) ?>
            </div>
            <div class="user-info ms-2">
                <div class="user-name"><?= session()->get('first_name') ? session()->get('first_name') . ' ' . session()->get('last_name') : 'Admin User' ?></div>
                <div class="user-role"><?= session()->get('role') ? ucwords(str_replace('_', ' ', session()->get('role'))) : 'Staff' ?></div>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
