<?php 
/**
 * Enhanced Sidebar Navigation Structure
 * Struktur baru berdasarkan rekomendasi peningkatan UX
 * PT Sarana Mitra Luas Tbk - OPTIMA System
 */
?>

<!-- Enhanced Sidebar with Advanced Features -->
<nav class="sidebar sidebar-enhanced" id="sidebar">
    <!-- Navigation Menu -->
    <div class="sidebar-nav">
        <ul class="nav flex-column">
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= (current_url() === base_url('/') || current_url() === base_url('/dashboard') || strpos(current_url(), 'dashboard') !== false) ? 'active' : '' ?>" 
                   href="<?= base_url('/dashboard') ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <!-- Tracking Delivery -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/operational/tracking') ?>"
                   data-search-terms="tracking delivery pengiriman monitoring">
                    <i class="fas fa-truck"></i>
                    <span class="nav-link-text">Tracking Delivery</span>
                </a>
            </li>


            <!-- MARKETING DIVISION -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">MARKETING</div>
            </li>

            <!-- Buat Penawaran -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/penawaran') ?>"
                   data-search-terms="marketing penawaran buat proposal">
                    <i class="fas fa-file-invoice"></i>
                    <span class="nav-link-text">Buat Penawaran</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Customer Management -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'customer-management') !== false) ? 'active' : '' ?>" href="<?= base_url('/marketing/customer-management') ?>"
                   data-search-terms="customer management marketing">
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
                   data-search-terms="spk surat perintah kerja">
                    <i class="fas fa-file-contract"></i>
                    <span class="nav-link-text">SPK (Surat Perintah Kerja)</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Delivery Instructions -->
            <?php if (can_view('marketing')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/di') ?>"
                   data-search-terms="delivery instructions di">
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
                   data-search-terms="spk service penyiapan unit">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="nav-link-text">SPK Service (Penyiapan Unit)</span>
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
