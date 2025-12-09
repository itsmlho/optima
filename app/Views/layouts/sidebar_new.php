<?php 
/**
 * Enhanced Sidebar Navigation Structure
 * Struktur baru berdasarkan rekomendasi peningkatan UX
 * PT Sarana Mitra Luas Tbk - OPTIMA System
 */
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
                    <span class="nav-link-text">Analytics</span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header">Dashboard & Analytics</div>
                    <a href="<?= base_url('/dashboard') ?>" class="nav-dropdown-item <?= (current_url() === base_url('/') || current_url() === base_url('/dashboard') || strpos(current_url(), 'dashboard') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="<?= base_url('/operational/tracking') ?>" class="nav-dropdown-item">
                        <i class="fas fa-truck"></i> Tracking Delivery
                    </a>
                </div>
            </li>

            <!-- Customer Relationship Management -->
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="crm">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-link-text">CRM</span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header">Customer Relationship Management</div>
                    <?php if (can_view('marketing')): ?>
                    <a href="<?= base_url('/marketing/customer-management') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'customer-management') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-users"></i> Customer Management
                    </a>
                    <a href="<?= base_url('/marketing/quotations') ?>" class="nav-dropdown-item">
                        <i class="fas fa-file-invoice-dollar"></i> Quotations
                    </a>
                    <a href="<?= base_url('/marketing/spk') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'marketing/spk') !== false ? 'active' : '' ?>">
                        <i class="fas fa-file-contract"></i> Work Orders (SPK)
                    </a>
                    <a href="<?= base_url('/marketing/di') ?>" class="nav-dropdown-item">
                        <i class="fas fa-shipping-fast"></i> Delivery Instructions (DI)
                    </a>
                    <?php endif; ?>
                </div>
            </li>

            <!-- Service & Maintenance -->
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="service">
                    <i class="fas fa-tools"></i>
                    <span class="nav-link-text">Service</span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header">Service & Maintenance</div>
                    <?php if (can_view('service')): ?>
                    <a href="<?= base_url('/service/spk_service') ?>" class="nav-dropdown-item">
                        <i class="fas fa-clipboard-list"></i> Work Orders SPK (Unit Preparation)
                    </a>
                    <a href="<?= base_url('/service/work-orders') ?>" class="nav-dropdown-item">
                        <i class="fas fa-wrench"></i> Work Orders (Complaint Management)
                    </a>
                    <a href="<?= base_url('/service/pmps') ?>" class="nav-dropdown-item">
                        <i class="fas fa-calendar-check"></i> Preventive Maintenance (PMPS)
                    </a>
                    <a href="<?= base_url('/service/area-management') ?>" class="nav-dropdown-item <?= (strpos(current_url(), 'service/area-management') !== false) ? 'active' : '' ?>">
                        <i class="fas fa-map-marked-alt"></i> Area & Employee Management
                    </a>
                    <?php endif; ?>
                </div>
            </li>

            <!-- Supply Chain Management -->
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="supply-chain">
                    <i class="fas fa-shipping-fast"></i>
                    <span class="nav-link-text">Supply Chain</span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header">Supply Chain Management</div>
                    <?php if (can_view('purchasing')): ?>
                    <a href="<?= base_url('/purchasing') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'purchasing') !== false && strpos(current_url(), 'sparepart') === false && strpos(current_url(), 'supplier') === false ? 'active' : '' ?>">
                        <i class="fas fa-truck"></i> PO Unit & Attachment
                    </a>
                    <a href="<?= base_url('/purchasing/po-sparepart-list') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'po-sparepart') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tools"></i> PO Sparepart
                    </a>
                    <a href="<?= base_url('/warehouse/purchase-orders/rejected-items') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'rejected-items') !== false ? 'active' : '' ?>">
                        <i class="fas fa-times"></i> PO Reject
                    </a>
                    <a href="<?= base_url('/purchasing/supplier-management-page') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'supplier-management') !== false ? 'active' : '' ?>">
                        <i class="fas fa-building"></i> Supplier Management
                    </a>
                    <?php endif; ?>
                    <?php if (can_view('operational')): ?>
                    <a href="<?= base_url('/operational/delivery') ?>" class="nav-dropdown-item">
                        <i class="fas fa-shipping-fast"></i> Delivery Process
                    </a>
                    <?php endif; ?>
                </div>
            </li>

            <!-- Inventory & Warehouse -->
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="warehouse">
                    <i class="fas fa-warehouse"></i>
                    <span class="nav-link-text">Warehouse</span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header">Inventory & Warehouse</div>
                    <?php if (can_view('warehouse')): ?>
                    <a href="<?= base_url('/warehouse/inventory/invent_unit') ?>" class="nav-dropdown-item">
                        <i class="fas fa-truck"></i> Unit Inventory
                    </a>
                    <a href="<?= base_url('/warehouse/inventory/invent_attachment') ?>" class="nav-dropdown-item">
                        <i class="fas fa-battery-half"></i> Attachment & Battery Inventory
                    </a>
                    <a href="<?= base_url('/warehouse/inventory/invent_sparepart') ?>" class="nav-dropdown-item">
                        <i class="fas fa-tools"></i> Sparepart Inventory
                    </a>
                    <a href="<?= base_url('/warehouse/sparepart-usage') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'warehouse/sparepart-usage') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tools"></i> Sparepart Usage & Returns
                    </a>
                    <a href="<?= base_url('/warehouse/purchase-orders/wh-verification') ?>" class="nav-dropdown-item <?= strpos(current_url(), 'warehouse/purchase-orders/wh-verification') !== false ? 'active' : '' ?>">
                        <i class="fas fa-clipboard-check"></i> PO Verification
                    </a>
                    <?php endif; ?>
                </div>
            </li>

            <!-- Finance & Accounting -->
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="finance">
                    <i class="fas fa-chart-pie"></i>
                    <span class="nav-link-text">Finance</span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header">Finance & Accounting</div>
                    <?php if (can_view('accounting')): ?>
                    <a href="<?= base_url('/finance/invoices') ?>" class="nav-dropdown-item">
                        <i class="fas fa-file-invoice"></i> Invoice Management
                    </a>
                    <a href="<?= base_url('/finance/invoices') ?>" class="nav-dropdown-item">
                        <i class="fas fa-check-circle"></i> Payment Validation
                    </a>
                    <?php endif; ?>
                </div>
            </li>

            <!-- Compliance & Permits -->
            <?php if (can_view('perizinan')): ?>
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="compliance">
                    <i class="fas fa-shield-alt"></i>
                    <span class="nav-link-text">Compliance</span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header">Compliance & Permits</div>
                    <a href="<?= base_url('/perizinan/silo') ?>" class="nav-dropdown-item">
                        <i class="fa-solid fa-shield-halved"></i> SILO (Surat Izin Layak Operasi)
                    </a>
                    <a href="<?= base_url('/perizinan/emisi') ?>" class="nav-dropdown-item">
                        <i class="fas fa-leaf"></i> EMISI (Surat Izin Emisi Gas Buang)
                    </a>
                </div>
            </li>
            <?php endif; ?>

            <!-- System Administration -->
            <li class="nav-item nav-group-item">
                <a class="nav-link nav-group-link" data-group="admin">
                    <i class="fas fa-cogs"></i>
                    <span class="nav-link-text">System</span>
                </a>
                <div class="nav-dropdown">
                    <div class="nav-dropdown-header">System Administration</div>
                    <?php if (can_view('admin')): ?>
                    <a href="<?= base_url('/admin') ?>" class="nav-dropdown-item <?= (strpos(current_url(), '/admin') !== false && strpos(current_url(), 'activity-log') === false && strpos(current_url(), 'advanced-users') === false && strpos(current_url(), 'roles') === false && strpos(current_url(), 'permissions') === false) ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Administration
                    </a>
                    <a href="<?= base_url('/settings') ?>" class="nav-dropdown-item">
                        <i class="fas fa-sliders-h"></i> Configuration
                    </a>
                    <?php else: ?>
                    <div class="nav-dropdown-item text-muted">
                        <i class="fas fa-lock"></i> Access Restricted
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
                   data-tooltip="Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <!-- Tracking Delivery -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/operational/tracking') ?>"
                   data-search-terms="tracking delivery pengiriman monitoring"
                   data-tooltip="Tracking Delivery">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text">Tracking Delivery</span>
                </a>
            </li>


            <!-- MARKETING DIVISION -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">MARKETING</div>
            </li>

            <!-- Quotations -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/quotations') ?>"
                   data-search-terms="marketing quotations proposals quotes"
                   data-tooltip="Quotations">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span class="nav-link-text">Quotations</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Customer Management -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'customer-management') !== false) ? 'active' : '' ?>" href="<?= base_url('/marketing/customer-management') ?>"
                   data-search-terms="customer management marketing"
                   data-tooltip="Customer Management">
                    <i class="fas fa-users"></i>
                    <span class="nav-link-text">Customer Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- SPK -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'marketing/spk') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/spk') ?>"
                   data-search-terms="spk work orders surat perintah kerja"
                   data-tooltip="Work Orders (SPK)">
                    <i class="fas fa-file-contract"></i>
                    <span class="nav-link-text">Work Orders (SPK)</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Delivery Instructions -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/di') ?>"
                   data-search-terms="delivery instructions di"
                   data-tooltip="Delivery Instructions (DI)">
                    <i class="fas fa-shipping-fast"></i>
                    <span class="nav-link-text">Delivery Instructions (DI)</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- SERVICE DIVISION -->
            <?php if (can_view('service')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">SERVICE</div>
            </li>

            <!-- SPK Service -->
            <?php if (can_view('service')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/spk_service') ?>"
                   data-search-terms="spk service unit preparation"
                   data-tooltip="Work Orders SPK (Unit Preparation)">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="nav-link-text">Work Orders SPK (Unit Preparation)</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PMPS -->
            <?php if (can_view('service')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/pmps') ?>"
                   data-search-terms="pmps preventive maintenance">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-link-text">Preventive Maintenance (PMPS)</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Workorders -->
            <?php if (can_view('service')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/work-orders') ?>"
                   data-search-terms="workorder complaint keluhan">
                    <i class="fas fa-wrench"></i>
                    <span class="nav-link-text">Work Orders</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Area & Employee Management -->
            <?php if (can_view('service')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'service/area-management') !== false) ? 'active' : '' ?>" href="<?= base_url('/service/area-management') ?>"
                   data-search-terms="area staff employee management service">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="nav-link-text">Area & Employee Management</span>
                </a>
            </li>
            <?php endif; ?>


            <?php endif; ?>

            <!-- OPERATIONAL DIVISION -->
            <?php if (can_view('operational')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">OPERATIONAL</div>
            </li>

            <!-- Delivery Process -->
            <?php if (can_view('operational')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/operational/delivery') ?>"
                   data-search-terms="delivery process pengiriman">
                    <i class="fas fa-shipping-fast"></i>
                    <span class="nav-link-text">Delivery Process</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- ACCOUNTING DIVISION -->
            <?php if (can_view('accounting')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">ACCOUNTING</div>
            </li>

            <!-- Invoice Management -->
            <?php if (can_view('accounting')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/finance/invoices') ?>"
                   data-search-terms="invoice management tagihan">
                    <i class="fas fa-file-invoice"></i>
                    <span class="nav-link-text">Invoice Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Payment Validation -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/finance/invoices') ?>"
                   data-search-terms="payment validation pembayaran">
                    <i class="fas fa-check-circle"></i>
                    <span class="nav-link-text">Payment Validation</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- PURCHASING DIVISION -->
            <?php if (can_view('purchasing')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">PURCHASING</div>
            </li>

            <!-- PO Unit & Attachment -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'purchasing') !== false && strpos(current_url(), 'sparepart') === false && strpos(current_url(), 'supplier') === false ? 'active' : '' ?>" 
                   href="<?= base_url('/purchasing') ?>"
                   data-search-terms="purchasing po unit attachment battery charger">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text">PO Unit & Attachment</span>
                </a>
            </li>

            <!-- PO Sparepart -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'po-sparepart') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/purchasing/po-sparepart-list') ?>"
                   data-search-terms="purchasing po sparepart parts">
                    <i class="fas fa-tools"></i>
                    <span class="nav-link-text">PO Sparepart</span>
                </a>
            </li>

            <!-- PO Reject -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'rejected-items') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/warehouse/purchase-orders/rejected-items') ?>"
                   data-search-terms="po reject rejection">
                    <i class="fas fa-times"></i>
                    <span class="nav-link-text">PO Reject</span>
                </a>
            </li>

            <!-- Supplier Management -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'supplier-management') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/purchasing/supplier-management-page') ?>"
                   data-search-terms="supplier vendor management">
                    <i class="fas fa-building"></i>
                    <span class="nav-link-text">Supplier Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- WAREHOUSE DIVISION -->
            <?php if (can_view('warehouse')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">WAREHOUSE & ASSETS</div>
            </li>

            <!-- Inventory -->
            <?php if (can_view('warehouse')): ?>
            <!-- Unit Inventory -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/warehouse/inventory/invent_unit') ?>"
                   data-search-terms="inventory unit assets warehouse">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text">Unit Inventory</span>
                </a>
            </li>
            
            <!-- Attachment & Battery Inventory -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/warehouse/inventory/invent_attachment') ?>"
                   data-search-terms="inventory attachment battery warehouse">
                    <i class="fas fa-battery-half"></i>
                    <span class="nav-link-text">Attachment & Battery Inventory</span>
                </a>
            </li>
            
            <!-- Sparepart Inventory -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/warehouse/inventory/invent_sparepart') ?>"
                   data-search-terms="inventory sparepart spare part warehouse">
                    <i class="fas fa-tools"></i>
                    <span class="nav-link-text">Sparepart Inventory</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Sparepart Usage & Returns -->
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
            <?php if (can_view('warehouse')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'warehouse/purchase-orders/wh-verification') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/warehouse/purchase-orders/wh-verification') ?>"
                   data-search-terms="po verification verify purchase order warehouse">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-link-text">PO Verification</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PERIZINAN DIVISION -->
            <?php if (can_view('perizinan')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">PERIZINAN</div>
            </li>

            <!-- SILO -->
            <?php if (can_view('perizinan')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/perizinan/silo') ?>"
                   data-search-terms="silo izin layak operasi">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span class="nav-link-text">SILO (Surat Izin Layak Operasi)</span>
                </a>
            </li>

            <!-- EMISI -->
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
            <?php if (can_view('admin')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">ADMINISTRATION</div>
            </li>

            <!-- Admin Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), '/admin') !== false && strpos(current_url(), 'activity-log') === false && strpos(current_url(), 'advanced-users') === false && strpos(current_url(), 'roles') === false && strpos(current_url(), 'permissions') === false) ? 'active' : '' ?>" 
                   href="<?= base_url('/admin') ?>"
                   data-search-terms="admin dashboard system administration">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-link-text">Administration</span>
                </a>
            </li>

            <!-- Configuration -->
            <?php if (can_view('admin')): ?>
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
    </div>
</nav>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
