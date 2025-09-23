<?php 
/**
 * Enhanced Sidebar Navigation Structure
 * Struktur baru berdasarkan rekomendasi peningkatan UX
 * PT Sarana Mitra Luas Tbk - OPTIMA System
 */
?>

<!-- Enhanced Sidebar with Advanced Features -->
<nav class="sidebar sidebar-enhanced" id="sidebar">
    <!-- Sidebar Brand -->
    <a href="<?= base_url('/') ?>" class="sidebar-brand" style="text-decoration: none; color: inherit;">
        <div class="sidebar-brand-icon">
            <img src="<?= base_url('assets/images/logo-optima.ico') ?>" alt="OPTIMA" class="optima-logo">
        </div>
        <div class="sidebar-brand-text">OPTIMA</div>
    </a>

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

            <!-- Tracking Work Orders -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(service('router')->getMatchedRoute()[0], 'tracking') !== false ? 'active' : '' ?>" href="<?= base_url('/tracking-wo') ?>"
                   data-search-terms="tracking work orders wo">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="nav-link-text">Tracking Work Orders</span>
                </a>
            </li>

            <!-- MARKETING DIVISION -->
            <?php if (can_access('marketing.access')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">MARKETING</div>
            </li>

            <!-- Buat Penawaran -->
            <?php if (can_access('marketing.penawaran.create')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/penawaran') ?>"
                   data-search-terms="marketing penawaran buat proposal">
                    <i class="fas fa-file-invoice"></i>
                    <span class="nav-link-text">Buat Penawaran</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Kontrak & PO -->
            <?php if (can_access('marketing.kontrak.manage')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'marketing/kontrak') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/marketing/kontrak') ?>"
                   data-search-terms="kontrak po rental">
                    <i class="fas fa-handshake"></i>
                    <span class="nav-link-text">Kontrak/PO Rental</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- SPK -->
            <?php if (can_access('marketing.spk.manage')): ?>
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
            <?php if (can_access('marketing.di.manage')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/di') ?>"
                   data-search-terms="delivery instructions di">
                    <i class="fas fa-shipping-fast"></i>
                    <span class="nav-link-text">Delivery Instructions (DI)</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- List Unit -->
            <!-- <?php if (can_access('marketing.list_unit.view')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/list-unit') ?>"
                   data-search-terms="list unit available">
                    <i class="fas fa-list"></i>
                    <span class="nav-link-text">List Unit</span>
                </a>
            </li> -->
            <?php endif; ?>

            <!-- Unit Tersedia -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/marketing/available-units') ?>"
                   data-search-terms="unit tersedia available">
                    <i class="fas fa-check-circle"></i>
                    <span class="nav-link-text">Unit Tersedia</span>
                </a>
            </li> -->
            <?php endif; ?>

            <!-- SERVICE DIVISION -->
            <?php if (can_access('service.access')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">SERVICE</div>
            </li>

            <!-- SPK Service -->
            <?php if (can_access('service.spk.view')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/spk_service') ?>"
                   data-search-terms="spk service penyiapan unit">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="nav-link-text">SPK Service (Penyiapan Unit)</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PMPS -->
            <?php if (can_access('service.pmps.view')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/pmps') ?>"
                   data-search-terms="pmps preventive maintenance">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-link-text">Preventive Maintenance (PMPS)</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Workorders -->
            <?php if (can_access('service.work_orders.view')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/work-orders') ?>"
                   data-search-terms="workorder complaint keluhan">
                    <i class="fas fa-wrench"></i>
                    <span class="nav-link-text">Work Orders</span>
                </a>
            </li>
            <?php endif; ?>


            

            <!-- Data Unit -->
            <?php if (can_access('service.data_unit.view')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/service/data-unit') ?>"
                   data-search-terms="data unit service">
                    <i class="fas fa-database"></i>
                    <span class="nav-link-text">Data Unit</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- OPERATIONAL DIVISION -->
            <?php if (can_access('operational.access')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">OPERATIONAL</div>
            </li>

            <!-- Delivery Process -->
            <?php if (can_access('operational.delivery_instructions.view')): ?>
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
            <?php if (can_access('accounting.access')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">ACCOUNTING</div>
            </li>

            <!-- Invoice Management -->
            <?php if (can_access('invoices.view')): ?>
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
                <a class="nav-link" href="<?= base_url('/finance/payment-validation') ?>"
                   data-search-terms="payment validation pembayaran">
                    <i class="fas fa-check-circle"></i>
                    <span class="nav-link-text">Payment Validation</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- PURCHASING DIVISION -->
            <?php if (can_access('purchasing.access')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">PURCHASING</div>
            </li>

            <!-- Form PO -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/purchasing/form-po') ?>" 
                   data-search-terms="purchase order buat form po">
                    <i class="fas fa-plus-circle"></i>
                    <span class="nav-link-text">Buat PO</span>
                </a>
            </li>

            <!-- Purchase Orders - Dropdown -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#purchaseOrdersSubmenu" 
                   data-search-terms="purchasing purchase order po unit attachment sparepart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="nav-link-text">Purchase Orders</span>
                    <i class="fas fa-chevron-down ms-auto collapse-icon"></i>
                </a>
                <div class="collapse" id="purchaseOrdersSubmenu">
                    <div class="nav-submenu">
                        <a class="nav-link nav-submenu-item" href="<?= base_url('/purchasing/po-unit') ?>"
                           data-search-terms="po unit purchase order unit">
                            <i class="fas fa-truck"></i>
                            PO Unit
                        </a>
                        <a class="nav-link nav-submenu-item" href="<?= base_url('/purchasing/po-attachment') ?>"
                           data-search-terms="po attachment battery purchase order">
                            <i class="fas fa-battery-full"></i>
                            PO Attachment & Battery
                        </a>
                        <a class="nav-link nav-submenu-item" href="<?= base_url('/purchasing/po-sparepart') ?>"
                           data-search-terms="po sparepart purchase order spare part">
                            <i class="fas fa-cogs"></i>
                            PO Sparepart
                        </a>
                    </div>
                </div>
            </li>
            <?php endif; ?>

            <!-- WAREHOUSE DIVISION -->
            <?php if (can_access('warehouse.access')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">WAREHOUSE & ASSETS</div>
            </li>

            <!-- Inventory -->
            <?php if (can_access('warehouse.access')): ?>
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

            <!-- PO Verification - Dropdown -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#poVerificationSubmenu"
                   data-search-terms="po verification verify purchase order">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-link-text">PO Verification</span>
                    <i class="fas fa-chevron-down ms-auto collapse-icon"></i>
                </a>
                <div class="collapse" id="poVerificationSubmenu">
                    <div class="nav-submenu">
                        <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/purchase-orders/po-unit') ?>"
                           data-search-terms="po verification unit">
                            <i class="fas fa-truck-loading"></i>
                            PO Unit
                        </a>
                        <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/purchase-orders/po-attachment') ?>"
                           data-search-terms="po verification attachment battery">
                            <i class="fas fa-battery-full"></i>
                            PO Attachment & Battery
                        </a>
                        <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/purchase-orders/po-sparepart') ?>"
                           data-search-terms="po verification sparepart">
                            <i class="fas fa-tools"></i>
                            PO Sparepart
                        </a>
                    </div>
                </div>
            </li>
            <?php endif; ?>

            <!-- PERIZINAN DIVISION -->
            <?php if (can_access('perizinan.access')): ?>
            <li class="nav-divider">
                <div class="sidebar-heading">PERIZINAN</div>
            </li>

            <!-- SILO -->
            <?php if (can_access('perizinan.manage')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/perizinan/form-silo') ?>"
                   data-search-terms="silo izin layak operasi">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span class="nav-link-text">SILO (Surat Izin Layak Operasi)</span>
                </a>
            </li>

            <!-- EMISI -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/perizinan/form-emisi') ?>"
                   data-search-terms="emisi gas buang izin">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span class="nav-link-text">EMISI (Surat Izin Emisi Gas Buang)</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Divider -->
            <li class="nav-divider">
                <div class="sidebar-heading">ADMINISTRATION</div>
            </li>

            <!-- Administration -->
            <?php if (can_access('admin.access')): ?>
            <!-- User Management -->
            <?php if (can_access('admin.user_management')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/admin/advanced-users') ?>"
                   data-search-terms="user management pengguna">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-link-text">User Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Role Management -->
            <?php if (can_access('admin.role_management')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/admin/roles') ?>"
                   data-search-terms="role management peran">
                    <i class="fas fa-user-tag"></i>
                    <span class="nav-link-text">Role Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Permission Management -->
            <?php if (can_access('admin.permission_management')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/admin/permissions') ?>"
                   data-search-terms="permission management izin akses">
                    <i class="fas fa-key"></i>
                    <span class="nav-link-text">Permission Management</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- System Settings -->
            <?php if (can_access('admin.system_settings')): ?>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), '/admin') !== false && strpos(current_url(), 'activity-log') === false && strpos(current_url(), 'advanced-users') === false && strpos(current_url(), 'roles') === false && strpos(current_url(), 'permissions') === false) ? 'active' : '' ?>" 
                   href="<?= base_url('/admin') ?>"
                   data-search-terms="system settings pengaturan">
                    <i class="fas fa-cog"></i>
                    <span class="nav-link-text">System Settings</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Activity Log -->
            <?php if (can_access('admin.activity_log')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'activity-log') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/admin/activity-log') ?>"
                   data-search-terms="activity log aktivitas user audit trail">
                    <i class="fas fa-history"></i>
                    <span class="nav-link-text">Activity Log</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Notification Center -->
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'notifications') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('/notifications') ?>"
                   data-search-terms="notifications notifikasi alert pemberitahuan">
                    <i class="fas fa-bell"></i>
                    <span class="nav-link-text">Notification Center</span>
                    <span class="badge bg-warning ms-2 notification-badge" id="sidebarNotificationCount" style="display: none;">0</span>
                </a>
            </li>

            <!-- Notification Rules (Super Admin Only) -->
            <?php if (session()->get('role') === 'super_admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos(current_url(), 'notifications/admin') !== false ? 'active' : '' ?>" 
                   href="<?= base_url('notifications/admin') ?>"
                   data-search-terms="notification rules aturan notifikasi admin">
                    <i class="fas fa-cogs"></i>
                    <span class="nav-link-text">Notification Rules</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Configuration -->
            <?php if (can_access('admin.configuration')): ?>
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
