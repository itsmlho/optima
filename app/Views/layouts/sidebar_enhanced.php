<?php
/**
 * ENHANCED SIDEBAR WITH GRANULAR PERMISSION CHECKS
 * Updated to use the new comprehensive permission system
 */
?>
<nav class="sidebar sidebar-expanded" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-cogs"></i>
            <span class="logo-text">OPTIMA</span>
        </div>
        <div class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-chevron-left"></i>
        </div>
    </div>

    <div class="sidebar-content">
        <ul class="sidebar-nav">
            
            <!-- Dashboard - Always visible -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), '/admin') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/admin') ?>"
                   data-search-terms="dashboard admin panel">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <!-- MARKETING DIVISION -->
            <?php if (hasModuleAccess('marketing')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">MARKETING</div>
            </li>

            <!-- Customer Management -->
            <?php if (canNavigateTo('marketing', 'customer')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'customer-management') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/customer-management') ?>"
                   data-search-terms="customer management pelanggan">
                    <i class="fas fa-users"></i>
                    <span class="nav-link-text">Customer Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Customer Database -->
            <?php if (canNavigateTo('marketing', 'customer_db')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'customer-database') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/customer-database') ?>"
                   data-search-terms="customer database data pelanggan">
                    <i class="fas fa-database"></i>
                    <span class="nav-link-text">Customer Database</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Quotation System -->
            <?php if (canNavigateTo('marketing', 'quotation')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'quotation-system') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/quotation-system') ?>"
                   data-search-terms="quotation penawaran sistem">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span class="nav-link-text">Quotation System</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- SPK Management -->
            <?php if (canNavigateTo('marketing', 'spk')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'spk-system') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/spk-system') ?>"
                   data-search-terms="spk surat perintah kerja">
                    <i class="fas fa-file-contract"></i>
                    <span class="nav-link-text">SPK Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Delivery Instructions -->
            <?php if (canNavigateTo('marketing', 'delivery')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'delivery-instructions') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/delivery-instructions') ?>"
                   data-search-terms="delivery instructions pengiriman">
                    <i class="fas fa-shipping-fast"></i>
                    <span class="nav-link-text">Delivery Instructions</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- SERVICE DIVISION -->
            <?php if (hasModuleAccess('service')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">SERVICE</div>
            </li>

            <!-- Work Orders -->
            <?php if (canNavigateTo('service', 'workorder')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'work-orders') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/service/work-orders') ?>"
                   data-search-terms="work orders perintah kerja service">
                    <i class="fas fa-wrench"></i>
                    <span class="nav-link-text">Work Orders</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PMPS Management -->
            <?php if (canNavigateTo('service', 'pmps')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'pmps') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/service/pmps') ?>"
                   data-search-terms="pmps preventive maintenance">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-link-text">PMPS Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Area Management -->
            <?php if (canNavigateTo('service', 'area')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'area-management') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/service/area-management') ?>"
                   data-search-terms="area management wilayah service">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="nav-link-text">Area Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Service User Management -->
            <?php if (canNavigateTo('service', 'user')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'user-management') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/service/user-management') ?>"
                   data-search-terms="user management service users">
                    <i class="fas fa-user-cog"></i>
                    <span class="nav-link-text">Service User Management</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- PURCHASING DIVISION -->
            <?php if (hasModuleAccess('purchasing')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">PURCHASING</div>
            </li>

            <!-- PO Management -->
            <?php if (canNavigateTo('purchasing', 'po')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'po-page') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/purchasing/po-page') ?>"
                   data-search-terms="po purchase order pembelian">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="nav-link-text">PO Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PO Sparepart -->
            <?php if (canNavigateTo('purchasing', 'po_sparepart')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'po-sparepart') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/purchasing/po-sparepart-list') ?>"
                   data-search-terms="purchasing po sparepart parts">
                    <i class="fas fa-tools"></i>
                    <span class="nav-link-text">PO Sparepart</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Supplier Management -->
            <?php if (canNavigateTo('purchasing', 'supplier')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'supplier-management') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/purchasing/supplier-management-page') ?>"
                   data-search-terms="supplier vendor management">
                    <i class="fas fa-building"></i>
                    <span class="nav-link-text">Supplier Management</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- WAREHOUSE DIVISION -->
            <?php if (hasModuleAccess('warehouse')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">WAREHOUSE & ASSETS</div>
            </li>

            <!-- Unit Inventory -->
            <?php if (canNavigateTo('warehouse', 'unit_inventory')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/warehouse/inventory/invent_unit') ?>"
                   data-search-terms="inventory unit assets warehouse">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text">Unit Inventory</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Attachment & Battery Inventory -->
            <?php if (canNavigateTo('warehouse', 'attachment_inventory')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/warehouse/inventory/invent_attachment') ?>"
                   data-search-terms="inventory attachment battery warehouse">
                    <i class="fas fa-battery-half"></i>
                    <span class="nav-link-text">Attachment & Battery Inventory</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Sparepart Inventory -->
            <?php if (canNavigateTo('warehouse', 'sparepart_inventory')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/warehouse/inventory/invent_sparepart') ?>"
                   data-search-terms="inventory sparepart spare part warehouse">
                    <i class="fas fa-tools"></i>
                    <span class="nav-link-text">Sparepart Inventory</span>
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
                    <span class="nav-link-text">Sparepart Usage & Returns</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PO Verification -->
            <?php if (canNavigateTo('warehouse', 'po_verification')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'warehouse/purchase-orders/wh-verification') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/warehouse/purchase-orders/wh-verification') ?>"
                   data-search-terms="po verification verify purchase order warehouse">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-link-text">PO Verification</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- ACCOUNTING DIVISION -->
            <?php if (hasModuleAccess('accounting')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">ACCOUNTING</div>
            </li>

            <!-- Invoice Management -->
            <?php if (canNavigateTo('accounting', 'invoice')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'invoice-page') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/accounting/invoice-page') ?>"
                   data-search-terms="invoice management faktur">
                    <i class="fas fa-file-invoice"></i>
                    <span class="nav-link-text">Invoice Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Payment Validation -->
            <?php if (canNavigateTo('accounting', 'payment')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'payment-validation') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/accounting/payment-validation') ?>"
                   data-search-terms="payment validation pembayaran">
                    <i class="fas fa-credit-card"></i>
                    <span class="nav-link-text">Payment Validation</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- OPERATIONAL DIVISION -->
            <?php if (hasModuleAccess('operational')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">OPERATIONAL</div>
            </li>

            <!-- Delivery Process -->
            <?php if (canNavigateTo('operational', 'delivery')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'operational/delivery') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/operational/delivery') ?>"
                   data-search-terms="delivery process operational pengiriman">
                    <i class="fas fa-truck-moving"></i>
                    <span class="nav-link-text">Delivery Process</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- PERIZINAN DIVISION -->
            <?php if (hasModuleAccess('perizinan')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">PERIZINAN</div>
            </li>

            <!-- SILO -->
            <?php if (canNavigateTo('perizinan', 'silo')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/perizinan/silo') ?>"
                   data-search-terms="silo izin layak operasi">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span class="nav-link-text">SILO (Surat Izin Layak Operasi)</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- EMISI -->
            <?php if (canNavigateTo('perizinan', 'emisi')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/perizinan/emisi') ?>"
                   data-search-terms="emisi gas buang izin">
                    <i class="fas fa-leaf"></i>
                    <span class="nav-link-text">EMISI (Surat Izin Emisi Gas Buang)</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Administration -->
            <?php if (hasModuleAccess('admin')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">ADMINISTRATION</div>
            </li>

            <!-- Admin Dashboard -->
            <?php if (canNavigateTo('admin', 'dashboard')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), '/admin') !== false && strpos(current_url(), 'activity-log') === false && strpos(current_url(), 'advanced-users') === false && strpos(current_url(), 'roles') === false && strpos(current_url(), 'permissions') === false) ? 'active' : '' ?>" 
                   href="<?= base_url('/admin') ?>"
                   data-search-terms="admin dashboard system administration">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-link-text">Administration</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Configuration -->
            <?php if (canNavigateTo('admin', 'config')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/settings') ?>"
                   data-search-terms="configuration konfigurasi">
                    <i class="fas fa-sliders-h"></i>
                    <span class="nav-link-text">Configuration</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

        </ul>
    </div>

    <!-- Simple User Status -->
    <div class="sidebar-user-status">
        <div class="logged-in-text">Logged in as:</div>
        <div class="user-name"><?= session()->get('first_name') ? session()->get('first_name') . ' ' . session()->get('last_name') : 'Admin User' ?></div>
        <?php if (isSystemAdmin()): ?>
        <div class="user-role">
            <span class="badge bg-danger">System Administrator</span>
        </div>
        <?php endif; ?>
    </div>
</nav>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>