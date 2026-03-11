<?php
/**
 * Layout for CodingNepal-style sidebar demo
 * No session - static menu with OPTIMA routes
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->renderSection('title', true) ?: 'Sidebar Demo' ?> - OPTIMA</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/desktop/optima-sidebar-codingnepal.css') ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=block" />
    <?= $this->renderSection('css') ?>
</head>
<body class="cn-sidebar-layout">
    <!-- Header: bagian sendiri (nanti diisi) -->
    <header class="cn-layout-header" role="banner">
        <button class="sidebar-menu-button" type="button" aria-label="Toggle menu">
            <span class="material-symbols-rounded">menu</span>
        </button>
        <span class="cn-header-title">OPTIMA</span>
    </header>

    <!-- Sidebar: default collapsed (icon-only). Klik ikon menu di header untuk expand; state disimpan di localStorage. -->
    <aside class="sidebar collapsed">
        <nav class="sidebar-nav">
            <ul class="nav-list primary-nav">
                <li class="nav-item">
                    <a href="<?= base_url('/dashboard') ?>" class="nav-link">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item"><a class="nav-link dropdown-title">Dashboard</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">store</span>
                        <span class="nav-label">Marketing</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item"><a class="nav-link dropdown-title">Marketing</a></li>
                        <li class="nav-item"><a href="<?= base_url('/marketing/quotations') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">description</span><span class="drop-label">Quotations</span></a></li>
                        <li class="nav-item"><a href="<?= base_url('/marketing/customer-management') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">group</span><span class="drop-label">Customer Management</span></a></li>
                        <li class="nav-item"><a href="<?= base_url('/marketing/spk') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">assignment</span><span class="drop-label">SPK</span></a></li>
                        <li class="nav-item"><a href="<?= base_url('/marketing/di') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">local_shipping</span><span class="drop-label">Delivery Instructions</span></a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">build</span>
                        <span class="nav-label">Service</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item"><a class="nav-link dropdown-title">Service</a></li>
                        <li class="nav-item"><a href="<?= base_url('/service/spk_service') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">build_circle</span><span class="drop-label">SPK Service</span></a></li>
                        <li class="nav-item"><a href="<?= base_url('/service/pmps') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">checklist</span><span class="drop-label">PMPS</span></a></li>
                        <li class="nav-item"><a href="<?= base_url('/service/work-orders') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">work</span><span class="drop-label">Work Orders</span></a></li>
                        <li class="nav-item"><a href="<?= base_url('/service/unit-audit') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">fact_check</span><span class="drop-label">Unit Audit</span></a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">warehouse</span>
                        <span class="nav-label">Warehouse</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item"><a class="nav-link dropdown-title">Warehouse</a></li>
                        <li class="nav-item"><a href="<?= base_url('/warehouse/inventory/unit') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">inventory_2</span><span class="drop-label">Unit Inventory</span></a></li>
                        <li class="nav-item"><a href="<?= base_url('/warehouse/inventory/attachments') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">attach_file</span><span class="drop-label">Attachment Inventory</span></a></li>
                        <li class="nav-item"><a href="<?= base_url('/warehouse/inventory/invent_sparepart') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">category</span><span class="drop-label">Sparepart Inventory</span></a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        <span class="nav-label">Purchasing</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item"><a class="nav-link dropdown-title">Purchasing</a></li>
                        <li class="nav-item"><a href="<?= base_url('/purchasing') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">shopping_cart</span><span class="drop-label">PO Unit & Attachment</span></a></li>
                        <li class="nav-item"><a href="<?= base_url('/purchasing/supplier-management-page') ?>" class="nav-link dropdown-link"><span class="material-symbols-rounded drop-icon">business</span><span class="drop-label">Supplier Management</span></a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/operational/tracking') ?>" class="nav-link">
                        <span class="material-symbols-rounded">local_shipping</span>
                        <span class="nav-label">Tracking</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item"><a class="nav-link dropdown-title">Tracking</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/admin') ?>" class="nav-link">
                        <span class="material-symbols-rounded">settings</span>
                        <span class="nav-label">Administration</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item"><a class="nav-link dropdown-title">Administration</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="cn-main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer: bagian sendiri (nanti diisi) -->
    <footer class="cn-layout-footer" role="contentinfo">
        <small>&copy; OPTIMA</small>
    </footer>

    <script src="<?= base_url('assets/js/sidebar-codingnepal.js') ?>"></script>
    <?= $this->renderSection('javascript') ?>
</body>
</html>
