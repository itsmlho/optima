<?php
/**
 * Demo layout: Sidebar OPTIMA dengan styling improved (transisi halus, icon rapi).
 * Struktur sidebar = sama dengan production, isi menu statis untuk preview.
 */
$base = base_url('');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title', true) ?: 'Sidebar Improved Demo' ?> - OPTIMA</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= $base ?>assets/css/desktop/optima-sidebar-codepen-enhance.css" rel="stylesheet">
    <link href="<?= $base ?>assets/css/desktop/optima-sidebar-collapsed-demo.css" rel="stylesheet">
    <link href="<?= $base ?>assets/css/desktop/optima-sidebar-improved-demo.css" rel="stylesheet">
    <?= $this->renderSection('css') ?>
</head>
<body class="demo-sidebar-improved">
    <nav class="sidebar sidebar-enhanced" id="sidebar">
        <div class="sidebar-brand">
            <i class="sidebar-brand-icon fas fa-box"></i>
            <span class="sidebar-brand-text">OPTIMA</span>
        </div>

        <div class="sidebar-collapsed-menu" id="sidebarCollapsedMenu">
            <div class="collapsed-nav-container">
                <ul class="nav flex-column">
                    <li class="nav-item nav-group-item">
                        <a class="nav-link nav-group-link" href="<?= $base ?>dashboard">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-link-text">Analytics</span>
                        </a>
                        <div class="nav-dropdown">
                            <div class="nav-dropdown-header">Dashboard &amp; Analytics</div>
                            <a href="<?= $base ?>dashboard" class="nav-dropdown-item"><i class="fas fa-tachometer-alt"></i> <span class="nav-link-text">Dashboard</span></a>
                            <a href="<?= $base ?>operational/tracking" class="nav-dropdown-item"><i class="fas fa-truck"></i> <span class="nav-link-text">Tracking</span></a>
                        </div>
                    </li>
                    <li class="nav-item nav-group-item">
                        <a class="nav-link nav-group-link" href="#">
                            <i class="fas fa-users-cog"></i>
                            <span class="nav-link-text">CRM</span>
                        </a>
                        <div class="nav-dropdown">
                            <div class="nav-dropdown-header">Marketing</div>
                            <a href="<?= $base ?>marketing/quotations" class="nav-dropdown-item"><i class="fas fa-file-invoice-dollar"></i> <span class="nav-link-text">Quotations</span></a>
                            <a href="<?= $base ?>marketing/customer-management" class="nav-dropdown-item"><i class="fas fa-users"></i> <span class="nav-link-text">Customer</span></a>
                            <a href="<?= $base ?>marketing/spk" class="nav-dropdown-item"><i class="fas fa-file-contract"></i> <span class="nav-link-text">SPK</span></a>
                        </div>
                    </li>
                    <li class="nav-item nav-group-item">
                        <a class="nav-link nav-group-link" href="#">
                            <i class="fas fa-tools"></i>
                            <span class="nav-link-text">Service</span>
                        </a>
                        <div class="nav-dropdown">
                            <div class="nav-dropdown-header">Service &amp; Maintenance</div>
                            <a href="<?= $base ?>service/spk_service" class="nav-dropdown-item"><i class="fas fa-clipboard-list"></i> <span class="nav-link-text">SPK Service</span></a>
                            <a href="<?= $base ?>service/pmps" class="nav-dropdown-item"><i class="fas fa-calendar-check"></i> <span class="nav-link-text">PMPS</span></a>
                            <a href="<?= $base ?>service/work-orders" class="nav-dropdown-item"><i class="fas fa-wrench"></i> <span class="nav-link-text">Work Orders</span></a>
                        </div>
                    </li>
                    <li class="nav-item nav-group-item">
                        <a class="nav-link nav-group-link" href="#">
                            <i class="fas fa-warehouse"></i>
                            <span class="nav-link-text">Warehouse</span>
                        </a>
                        <div class="nav-dropdown">
                            <div class="nav-dropdown-header">Inventory &amp; Warehouse</div>
                            <a href="<?= $base ?>warehouse/inventory/unit" class="nav-dropdown-item"><i class="fas fa-truck"></i> <span class="nav-link-text">Unit Inventory</span></a>
                            <a href="<?= $base ?>warehouse/inventory/invent_sparepart" class="nav-dropdown-item"><i class="fas fa-tools"></i> <span class="nav-link-text">Sparepart</span></a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= $base ?>dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-link-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>operational/tracking">
                        <i class="fas fa-truck"></i>
                        <span class="nav-link-text">Tracking Delivery</span>
                    </a>
                </li>
                <li class="nav-divider">
                    <div class="sidebar-heading">MARKETING</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>marketing/quotations">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span class="nav-link-text">Quotations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>marketing/customer-management">
                        <i class="fas fa-users"></i>
                        <span class="nav-link-text">Customer Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>marketing/spk">
                        <i class="fas fa-file-contract"></i>
                        <span class="nav-link-text">SPK</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>marketing/di">
                        <i class="fas fa-shipping-fast"></i>
                        <span class="nav-link-text">Delivery Instructions</span>
                    </a>
                </li>
                <li class="nav-divider">
                    <div class="sidebar-heading">SERVICE</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>service/spk_service">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="nav-link-text">SPK Service</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>service/pmps">
                        <i class="fas fa-calendar-check"></i>
                        <span class="nav-link-text">PMPS</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>service/work-orders">
                        <i class="fas fa-wrench"></i>
                        <span class="nav-link-text">Work Orders</span>
                    </a>
                </li>
                <li class="nav-divider">
                    <div class="sidebar-heading">WAREHOUSE</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>warehouse/inventory/unit">
                        <i class="fas fa-truck"></i>
                        <span class="nav-link-text">Unit Inventory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>warehouse/inventory/invent_sparepart">
                        <i class="fas fa-tools"></i>
                        <span class="nav-link-text">Sparepart Inventory</span>
                    </a>
                </li>
                <li class="nav-divider">
                    <div class="sidebar-heading">ADMINISTRATION</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>admin">
                        <i class="fas fa-cogs"></i>
                        <span class="nav-link-text">Administration</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="main-content" id="mainContent">
        <div class="content-body p-4">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="<?= $base ?>assets/js/sidebar-codepen-enhance.js"></script>
    <?= $this->renderSection('javascript') ?>
</body>
</html>
