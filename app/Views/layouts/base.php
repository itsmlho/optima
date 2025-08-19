<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="OPTIMA - Sistem Manajemen Penyewaan Forklift PT Sarana Mitra Luas Tbk">
    <meta name="keywords" content="forklift, rental, manajemen, optima, sarana mitra luas">
    <meta name="author" content="PT Sarana Mitra Luas Tbk">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0061f2">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    
    <!-- Title -->
    <title>OPTIMA | PT Sarana Mitra Luas Tbk</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/logo-optima.ico') ?>">
    <link rel="icon" type="image/x-icon" href="<?= base_url('logo-optima.ico') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/images/logo-optima.ico') ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metropolis:wght@400;500;600;700;800&family=Roboto+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    
    <!-- Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    
    <!-- Pickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.2/dist/themes/nano.min.css" rel="stylesheet">
    
    <!-- noUiSlider CSS -->
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/optima-pro.css') ?>" rel="stylesheet">
    
    <!-- Page Specific CSS -->
    <?= $this->renderSection('css') ?>
    
    <!-- Custom Styles -->
    <style>
        /* Loading animation */
        .page-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .page-loading.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        
        .loading-content {
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .loading-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 97, 242, 0.25);
        }
        
        .loading-text {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0061f2;
            margin-bottom: 0.5rem;
        }
        
        .loading-subtitle {
            font-size: 0.875rem;
            color: #69707a;
            margin-bottom: 1.5rem;
        }
        
        .loading-spinner {
            width: 2rem;
            height: 2rem;
            border: 3px solid #e9ecef;
            border-top: 3px solid #0061f2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(1rem);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
    /* (Removed legacy #flash-toast-container styles; using unified #optima-toast-container system) */

    /* Ensure PO verification list items remain clickable even within high z-index layout */
    .po-group-header, .item-child-item, .unit-child-item, .unit-list-item {pointer-events:auto; position:relative;}
    /* legacy toast progress removed */
        
        /* Sidebar overlay for mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        
        @media (max-width: 991.98px) {
            .sidebar.show + .sidebar-overlay {
                display: block;
            }
        }
        
        /* Sidebar Collapsed State */
        .sidebar.collapsed {
            transform: translateX(-280px);
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        /* Smooth transition for sidebar toggle */
        .sidebar,
        .main-content {
            transition: all 0.3s ease;
        }

        /* Dark Mode Styles */
        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1a1a;
            --bs-body-color: #ffffff;
            --bs-secondary-bg: #2d2d2d;
            --bs-tertiary-bg: #3d3d3d;
            --bs-border-color: #404040;
        }

        [data-bs-theme="dark"] .sidebar {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border-right: 1px solid #404040;
        }

        [data-bs-theme="dark"] .main-content {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .navbar-brand {
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .card {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        [data-bs-theme="dark"] .card-header {
            background-color: #3d3d3d;
            border-bottom-color: #404040;
        }

        [data-bs-theme="dark"] .form-control {
            background-color: #2d2d2d;
            border-color: #404040;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .form-control:focus {
            background-color: #2d2d2d;
            border-color: #0061f2;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .btn-outline-secondary {
            border-color: #404040;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .btn-outline-secondary:hover {
            background-color: #404040;
            border-color: #404040;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #ffffff;
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #404040;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #adb5bd !important;
        }

        [data-bs-theme="dark"] .border-left-primary {
            border-left-color: #0061f2 !important;
        }

        [data-bs-theme="dark"] .border-left-success {
            border-left-color: #1cc88a !important;
        }

        [data-bs-theme="dark"] .border-left-warning {
            border-left-color: #f6c23e !important;
        }

        [data-bs-theme="dark"] .border-left-info {
            border-left-color: #36b9cc !important;
        }
        
        /* Sidebar Scrollable */
        .sidebar {
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            z-index: 1000;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - 80px);
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        /* Custom scrollbar */
        .sidebar::-webkit-scrollbar,
        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track,
        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb,
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover,
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* Dark mode scrollbar */
        [data-bs-theme="light"] .sidebar::-webkit-scrollbar-track,
        [data-bs-theme="light"] .sidebar-nav::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        [data-bs-theme="light"] .sidebar::-webkit-scrollbar-thumb,
        [data-bs-theme="light"] .sidebar-nav::-webkit-scrollbar-thumb {
            background: #c1c1c1;
        }
        
        [data-bs-theme="light"] .sidebar::-webkit-scrollbar-thumb:hover,
        [data-bs-theme="light"] .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: #0061f2;
        }
        
        /* Submenu Styling */
        .nav-submenu {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            margin: 0.5rem 0;
            padding: 0.5rem 0;
        }
        
        /* Light mode submenu background */
        [data-bs-theme="light"] .nav-submenu {
            background: rgba(0, 97, 242, 0.05);
        }
        
        [data-bs-theme="light"] .nav-submenu-nested {
            background: rgba(0, 97, 242, 0.03);
        }
        
        .nav-submenu-item {
            padding: 0.5rem 1rem !important;
            margin: 0.25rem 0;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8) !important;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .nav-submenu-item:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            transform: translateX(5px);
        }
        
        .nav-submenu-item.active {
            background: rgba(0, 97, 242, 0.2) !important;
            color: #0061f2 !important;
            border-left: 3px solid #0061f2;
        }
        
        /* Light mode submenu styling */
        [data-bs-theme="light"] .nav-submenu-item {
            color: #3a3b45 !important;
        }
        
        [data-bs-theme="light"] .nav-submenu-item:hover {
            background: rgba(0, 97, 242, 0.1) !important;
            color: #0061f2 !important;
        }
        
        [data-bs-theme="light"] .nav-submenu-nested-item {
            color: #5a5c69 !important;
        }
        
        [data-bs-theme="light"] .nav-submenu-nested-item:hover {
            background: rgba(0, 97, 242, 0.08) !important;
            color: #0061f2 !important;
        }
        
        .nav-submenu-nested {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 6px;
            margin: 0.25rem 0;
            padding: 0.25rem 0;
        }
        
        .nav-submenu-nested-item {
            padding: 0.4rem 1rem !important;
            margin: 0.15rem 0;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7) !important;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .nav-submenu-nested-item:hover {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #fff !important;
            transform: translateX(3px);
        }
        
        .nav-submenu-nested-item i.fas {
            font-size: 0.6rem;
            opacity: 0.6;
        }
        
        .sidebar .nav-link[data-bs-toggle="collapse"][aria-expanded="true"] {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        .sidebar .nav-link[data-bs-toggle="collapse"][aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
        }
        
        .sidebar .nav-link .fa-chevron-down {
            transition: transform 0.3s ease;
            font-size: 0.7rem;
        }
        
        .sidebar .nav-link.collapsed .fa-chevron-down {
            transform: rotate(0deg);
        }
        
        /* Divider styling */
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 1rem 0 !important;
        }
        
        /* Print styles */
        @media print {
            .sidebar,
            .content-header,
            .notification-container,
            .sidebar-overlay,
            .page-loading {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .content-body {
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Toast Global (pojok kanan atas) -->
    <div id="optima-toast-container" aria-live="polite" aria-atomic="true"></div>
    <style>
        #optima-toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1085;
            display: flex;
            flex-direction: column;
            gap: .5rem;
            max-width: 340px;
        }
        .optima-toast {
            border-radius: .65rem;
            box-shadow: 0 6px 18px -4px rgba(0,0,0,.25);
            background: #fff;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            animation: fadeIn .25s ease;
        }
        .optima-toast.success { border-left: 6px solid #198754; }
        .optima-toast.error { border-left: 6px solid #dc3545; }
        .optima-toast.warning { border-left: 6px solid #ffc107; }
        .optima-toast.info { border-left: 6px solid #0d6efd; }
        .optima-toast .ot-head {
            font-weight: 600;
            font-size: .85rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: .25rem;
        }
        .optima-toast .ot-body { font-size: .82rem; line-height: 1.2rem; }
        .optima-toast .ot-close {
            background: transparent;
            border: none;
            font-size: .9rem;
            cursor: pointer;
            color: #6c757d;
        }
        .optima-progress {
            height: 3px;
            width: 100%;
            background: linear-gradient(90deg,#0d6efd,#3b82f6);
            animation: ot-progress linear forwards;
        }
        @keyframes ot-progress { from {width:100%} to {width:0} }
        @keyframes fadeIn { from {opacity:0; transform: translateY(-6px);} to {opacity:1; transform:translateY(0);} }
    </style>
    <script>
        // Global toast creator (Bootstrap toast style like notifications/index.php)
        window.createOptimaToast = function({type='info', title='Info', message='', duration=5000} = {}) {
            const color = (type==='success') ? 'success' : (type==='warning') ? 'warning' : (type==='error' || type==='danger') ? 'danger' : 'info';
            const icon = (type==='success') ? 'fas fa-check-circle' : (type==='warning') ? 'fas fa-exclamation-triangle' : (type==='error' || type==='danger') ? 'fas fa-times-circle' : 'fas fa-info-circle';
            // Stack toasts: compute offset
            const existing = document.querySelectorAll('.optima-bs-toast').length;
            const topOffset = 20 + (existing * 84); // 84px per toast approx
            const el = document.createElement('div');
            el.className = `toast optima-bs-toast align-items-center text-white bg-${color} border-0`;
            el.setAttribute('role','alert');
            el.style.cssText = `position: fixed; top: ${topOffset}px; right: 20px; z-index: 1060;`;
            el.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${icon} me-2"></i>
                        <strong>${escapeHtml(title)}</strong><br>
                        ${escapeHtml(message)}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(el);
            try {
                if (window.bootstrap && bootstrap.Toast) {
                    const t = new bootstrap.Toast(el, { delay: duration });
                    el.addEventListener('hidden.bs.toast', () => el.remove());
                    t.show();
                } else if (window.$ && typeof $('.toast').toast === 'function') {
                    $(el).toast({ delay: duration }).toast('show');
                    $(el).on('hidden.bs.toast', function(){ $(this).remove(); });
                } else {
                    // Fallback: auto-remove without animation
                    setTimeout(() => { if (el && el.remove) el.remove(); }, duration);
                }
            } catch (e) {
                setTimeout(() => { if (el && el.remove) el.remove(); }, duration);
            }
        };
        function escapeHtml(str){ return String(str??'').replace(/[&<>"']/g,s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }
        // Backward compatibility
        window.OptimaPro = window.OptimaPro || {};
        window.OptimaPro.showNotification = (msg,type='info') => createOptimaToast({type:type==='error'?'error':type, title:type.toUpperCase(), message:msg});
    </script>
    <!-- Page Loading -->
    <div class="page-loading" id="pageLoading">
        <div class="loading-content">
            <div class="loading-logo">
                <i class="fas fa-truck"></i>
            </div>
            <div class="loading-text">OPTIMA</div>
            <div class="loading-subtitle">PT Sarana Mitra Luas Tbk</div>
            <div class="loading-spinner"></div>
        </div>
    </div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a href="<?= base_url('/') ?>" class="sidebar-brand" style="text-decoration: none; color: inherit;">
            <div class="sidebar-brand-icon">
                <img src="<?= base_url('assets/images/logo-optima.ico') ?>" alt="OPTIMA" class="optima-logo">
            </div>
            <div class="sidebar-brand-text">OPTIMA</div>
        </a>
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#dashboardSubmenu">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-link-text">Dashboard</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="dashboardSubmenu" aria-labelledby="dashboardHeading">
                        <div class="nav-submenu ms-3">
                                <a class="nav-link nav-submenu-item" href="<?= base_url('/dashboard/service') ?>">
                                    <i class="fas fa-tools"></i>
                                    Service
                                </a>
                                <a class="nav-link nav-submenu-item" href="<?= base_url('/dashboard/rolling') ?>">
                                    <i class="fas fa-truck-moving"></i>
                                    Operational
                                </a>
                                <a class="nav-link nav-submenu-item" href="<?= base_url('/dashboard/marketing') ?>">
                                    <i class="fas fa-chart-line"></i>
                                    Marketing
                                </a>
                                <a class="nav-link nav-submenu-item" href="<?= base_url('/dashboard/warehouse') ?>">
                                    <i class="fas fa-warehouse"></i>
                                    Warehouse & Assets
                                </a>
                                <a class="nav-link nav-submenu-item" href="<?= base_url('/purchasing') ?>">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Purchasing
                                </a>
                                <a class="nav-link nav-submenu-item" href="<?= base_url('/dashboard/accounting') ?>">
                                    <i class="fas fa-calculator"></i>
                                    Accounting
                                </a>
                                <a class="nav-link nav-submenu-item" href="<?= base_url('/dashboard/perizinan') ?>">
                                    <i class="fa-solid fa-file-contract"></i>
                                    Perizinan
                                </a>
                        </div>
                    </div>
                </li>

                <!-- Marketing Division -->
                <?php if (can_access('marketing.access')): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#marketingSubmenu">
                        <i class="fas fa-chart-line"></i>
                        <span class="nav-link-text">Marketing</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="marketingSubmenu">
                        <div class="nav-submenu ms-3">
                            <?php if (can_access('marketing.penawaran.create')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/marketing/penawaran') ?>">
                                <i class="fas fa-file-invoice"></i>
                                Buat Penawaran
                            </a>
                            <?php endif; ?>
                            <?php if (can_access('marketing.kontrak.manage')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/marketing/kontrak') ?>">
                                <i class="fas fa-handshake"></i>
                                Kontrak & PO
                            </a>
                            <?php if (can_access('marketing.spk.manage')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/marketing/spk') ?>">
                                <i class="fas fa-handshake"></i>
                                Surat Perintah Kerja (SPK)
                            </a>
                            <?php endif; ?>
                            <?php if (can_access('marketing.di.manage')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/marketing/di') ?>">
                                <i class="fas fa-handshake"></i>
                                Delivery Instructions (DI)
                            </a>
                            <?php endif; ?>
                            <?php if (can_access('marketing.list_unit.view')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/marketing/list-unit') ?>">
                                <i class="fas fa-list"></i>
                                List Unit
                            </a>
                            <?php endif; ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/marketing/available-units') ?>">
                                <i class="fas fa-check-circle"></i>
                                Unit Tersedia
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endif; ?>
                
                <!-- Service Division -->
                <?php if (can_access('service.access')): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#serviceSubmenu">
                        <i class="fas fa-tools"></i>
                        <span class="nav-link-text">Service</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="serviceSubmenu">
                        <div class="nav-submenu ms-3">
                            <!-- Work Orders -->
                            <?php if (can_access('service.work_orders.view')): ?>
                            <a class="nav-link collapsed nav-submenu-item" href="#" data-bs-toggle="collapse" data-bs-target="#workOrdersSubmenu">
                                <i class="fas fa-clipboard-list"></i>
                                Work Orders
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="workOrdersSubmenu">
                                <div class="nav-submenu-nested ms-3">
                                    <?php if (can_access('service.work_orders.view')): ?>
                                    <a class="nav-link nav-submenu-nested-item" href="<?= base_url('/service/work-orders') ?>">
                                        <i class="fas fa-circle"></i>
                                        Work Order
                                    </a>
                                    <?php endif; ?>
                                    <a class="nav-link nav-submenu-nested-item" href="<?= base_url('/service/work-orders/history') ?>">
                                        <i class="fas fa-circle"></i>
                                        History
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- PMPS -->
                            <?php if (can_access('service.pmps.view')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/service/pmps') ?>">
                                <i class="fas fa-calendar-check"></i>
                                PMPS
                            </a>
                            <?php endif; ?>

                            <!--Inventory -->
                            <?php if (can_access('service.inventory.view')): ?>
                            <a class="nav-link collapsed nav-submenu-item" href="#" data-bs-toggle="collapse" data-bs-target="#serviceInventorySubmenu">
                                <i class="fas fa-clipboard-list"></i>
                                Inventory
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="serviceInventorySubmenu">
                                <div class="nav-submenu-nested ms-3">
                                    <?php if (can_access('service.unit_inventory.view')): ?>
                                    <a class="nav-link nav-submenu-nested-item" href="<?= base_url('/service/unit-inventory') ?>">
                                        <i class="fas fa-circle"></i>
                                        Unit Inventory
                                    </a>
                                    <?php endif; ?>
                                    <a class="nav-link nav-submenu-nested-item" href="<?= base_url('/service/attachment-inventory') ?>">
                                        <i class="fas fa-circle"></i>
                                        Attachment Inventory
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!--SPK -->
                            <?php if (can_access('service.spk.view')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/service/spk_service') ?>">
                                <i class="fas fa-clipboard-list"></i>
                                SPK
                            </a>
                            <?php endif; ?>
                            
                            <!-- PDI -->
                            <?php if (can_access('service.pdi.view')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/service/pdi') ?>">
                                <i class="fas fa-truck"></i>
                                Pre-Delivery Inspection
                            </a>
                            <?php endif; ?>

                            <!-- Data Unit -->
                            <?php if (can_access('service.data_unit.view')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/service/data-unit') ?>">
                                <i class="fas fa-truck"></i>
                                Data Unit
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endif; ?>
                

                <!-- Operational Division -->
                <?php if (can_access('operational.access')): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#unitRollingSubmenu">
                        <i class="fas fa-truck-moving"></i>
                        <span class="nav-link-text">Operational</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="unitRollingSubmenu">
                        <div class="nav-submenu ms-3">
                            <?php if (can_access('operational.delivery_instructions.view')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/operational/delivery') ?>">
                                <i class="fas fa-database"></i>
                                Delivery Instructions
                            </a>
                            <?php endif; ?>
                            <?php if (can_access('operational.tracking.view')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/operational/tracking') ?>">
                                <i class="fas fa-route"></i>
                                Tracking
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endif; ?>


                <!-- Warehouse & Assets Division -->
                <?php if (can_access('warehouse.access')): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#warehouseSubmenu">
                        <i class="fas fa-warehouse"></i>
                        <span class="nav-link-text">Warehouse & Assets</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="warehouseSubmenu">
                        <div class="nav-submenu ms-3">
                            <?php if (can_access('warehouse.assets.manage')): ?>
                            <!-- <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/unit-assets') ?>">
                                <i class="fas fa-truck"></i>
                                Unit Assets
                            </a> -->
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/inventory/invent_unit') ?>">
                                <i class="fas fa-truck"></i>
                                Unit
                            </a>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/inventory/invent_attachment') ?>">
                                <i class="fas fa-paperclip"></i>
                                Attachment & Battery
                            </a>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/inventory/invent_sparepart') ?>">
                                <i class="fas fa-tools"></i>
                                Sparepart
                            </a>
                            <?php endif; ?>
                            <!-- <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/sparepart') ?>">
                                <i class="fas fa-cog"></i>
                                Data Sparepart
                            </a> -->

                            
                            <!-- Purchase Order Verification Submenu -->
                            <a class="nav-link nav-submenu-item collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#poVerificationSubmenu">
                                <i class="fas fa-clipboard-check"></i>
                                <span class="nav-link-text">PO Verification</span>
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>   
                            <div class="collapse" id="poVerificationSubmenu">
                                <div class="nav-submenu ms-4"> <!-- Indentasi lebih dalam -->
                                    <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/purchase-orders/po-unit') ?>">
                                        <i class="fas fa-truck-loading"></i>
                                        PO Unit
                                    </a>
                                    <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/purchase-orders/po-attachment') ?>">
                                        <i class="fas fa-battery-full"></i>
                                        PO Attachment & Battery
                                    </a>
                                    <a class="nav-link nav-submenu-item" href="<?= base_url('/warehouse/purchase-orders/po-sparepart') ?>">
                                        <i class="fas fa-tools"></i>
                                        PO Sparepart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endif; ?>

                <!-- Purchasing Division -->
                <?php if (can_access('purchasing.access')): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#purchasingSubmenu">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="nav-link-text">Purchasing Division</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="purchasingSubmenu">
                        <div class="nav-submenu ms-3">
                        
                            <div class="nav-submenu-divider my-2"></div>
                            <!-- <h6 class="nav-submenu-heading">Purchase Orders</h6> -->
                            <?php if (can_access('purchasing.manage')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/purchasing/form-po') ?>">
                                <i class="fas fa-truck"></i>
                                Buat PO
                            </a>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/purchasing/po-unit') ?>">
                                <i class="fas fa-truck"></i>
                                PO Unit
                            </a>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/purchasing/po-attachment') ?>">
                                <i class="fas fa-battery-full"></i>
                                PO Attachment & Battery
                            </a>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/purchasing/po-sparepart') ?>">
                                <i class="fas fa-cogs"></i>
                                PO Sparepart
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endif; ?>
                
                <!-- Perizinan -->
                <?php if (can_access('perizinan.access')): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#perizinanSubmenu">
                        <i class="fa-solid fa-file-contract"></i>
                        <span class="nav-link-text">Perizinan</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="perizinanSubmenu">
                        <div class="nav-submenu ms-3">
                        
                            <div class="nav-submenu-divider my-2"></div>
                            <?php if (can_access('perizinan.manage')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/perizinan/form-silo') ?>">
                                <i class="fa-solid fa-shield-halved"></i>
                                SILO (Surat Izin Layak Operasi)
                            </a>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/perizinan/form-emisi') ?>">
                                <i class="fa-solid fa-shield-halved"></i>
                                EMISI (Surat Izin Emisi Gas Buang)
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endif; ?>
                
                <!-- Accounting -->
                <?php if (can_access('accounting.access')): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#accountingSubmenu">
                        <i class="fas fa-calculator"></i>
                        <span class="nav-link-text">Accounting</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="accountingSubmenu">
                        <div class="nav-submenu ms-3">
                            <!-- <?php if (can_access('finance.view')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/finance') ?>">
                                <i class="fas fa-chart-line"></i>
                                Keuangan
                            </a>
                            <?php endif; ?> -->

                            <?php if (can_access('invoices.view')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/finance/invoices') ?>">
                                <i class="fas fa-file-invoice"></i>
                                Invoice Management
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endif; ?>

                <!-- Divider -->
                <li class="nav-item">
                    <hr class="sidebar-divider my-3">
                </li>
                
                <!-- Administration -->
                <?php if (can_access('admin.access')): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#adminSubmenu">
                        <i class="fas fa-user-shield"></i>
                        <span class="nav-link-text">Administration</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="adminSubmenu">
                        <div class="nav-submenu ms-3">
                            <?php if (can_access('admin.user_management')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/admin/advanced-users') ?>">
                                <i class="fas fa-users-cog"></i>
                                User Management
                            </a>
                            <?php endif; ?>                        
                            
                            <?php if (can_access('admin.role_management')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/admin/roles') ?>">
                                <i class="fas fa-user-tag"></i>
                                Role Management
                            </a>
                            <?php endif; ?>
                            
                            <?php if (can_access('admin.permission_management')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/admin/permissions') ?>">
                                <i class="fas fa-key"></i>
                                Permission Management
                            </a>
                            <?php endif; ?>
                            
                            <?php if (can_access('admin.system_settings')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/admin') ?>">
                                <i class="fas fa-cog"></i>
                                System Settings
                            </a>
                            <?php endif; ?>
                            
                            <?php if (can_access('admin.configuration')): ?>
                            <a class="nav-link nav-submenu-item" href="<?= base_url('/settings') ?>">
                                <i class="fas fa-sliders-h"></i>
                                Configuration
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endif; ?>
                <!-- End Administration -->
                
                <!-- Tracking Delivery -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/operational/tracking') ?>">
                        <i class="fas fa-truck"></i>
                        <span class="nav-link-text">Tracking Delivery</span>
                    </a>
                </li>

                <!-- Tracking Work Orders -->
                <li class="nav-item">
                    <a class="nav-link <?= strpos(service('router')->getMatchedRoute()[0], 'tracking') !== false ? 'active' : '' ?>" href="<?= base_url('/tracking-wo') ?>">
                        <i class="fas fa-truck"></i>
                        <span class="nav-link-text">Tracking Work Orders</span>
                    </a>
                </li>

            

                <!-- Rental Management -->
                <!-- <li class="nav-item">
                    <a class="nav-link <?= strpos(service('router')->getMatchedRoute()[0], 'rentals') !== false ? 'active' : '' ?>" href="<?= base_url('/rentals') ?>">
                        <i class="fas fa-handshake"></i>
                        <span class="nav-link-text">Rental Management</span>
                    </a>
                </li> -->
                
                <!-- Pelanggan -->
                <!-- <li class="nav-item">
                    <a class="nav-link <?= strpos(service('router')->getMatchedRoute()[0], 'customers') !== false ? 'active' : '' ?>" href="<?= base_url('/customers') ?>">
                        <i class="fas fa-users"></i>
                        <span class="nav-link-text">Pelanggan</span>
                    </a>
                </li> -->

                
                <!-- Laporan -->
                <!-- <li class="nav-item">
                    <a class="nav-link <?= strpos(service('router')->getMatchedRoute()[0], 'reports') !== false ? 'active' : '' ?>" href="<?= base_url('/reports') ?>">
                        <i class="fas fa-file-alt"></i>
                        <span class="nav-link-text">Laporan</span>
                    </a>
                </li> -->
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Content Header -->
        <header class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-md-none p-0 me-3 sidebar-toggle" type="button">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <div>
                        <h1 class="h3 mb-0"><?= $title ?? 'Dashboard' ?></h1>
                        <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                        <nav aria-label="breadcrumb" class="d-none d-lg-block">
                            <ol class="breadcrumb">
                                <?php foreach ($breadcrumbs as $key => $breadcrumb): ?>
                                    <?php if ($key === array_key_last($breadcrumbs)): ?>
                                        <li class="breadcrumb-item active" aria-current="page"><?= $breadcrumb ?></li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item"><a href="<?= base_url($key) ?>"><?= $breadcrumb ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <!-- Search Box -->
                    <div class="me-3 d-none d-md-block">
                        <div class="input-group">
                            <input type="text" class="form-control search-box" placeholder="Cari..." data-target=".searchable">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary position-relative" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <span class="notification-count" data-realtime="notification_count">0</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px;">
                            <li><h6 class="dropdown-header">Notifikasi</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="notification-item">
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle text-info"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="fw-semibold">Pemeliharaan Terjadwal</div>
                                            <div class="text-muted small">Unit FL-001 memerlukan pemeliharaan</div>
                                            <div class="text-muted small">2 jam yang lalu</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="<?= base_url('/notifications') ?>">Lihat Semua Notifikasi</a></li>
                        </ul>
                    </div>
                    
                    <!-- Theme Toggle -->
                    <button class="btn btn-outline-secondary me-3 theme-toggle" type="button" title="Toggle Dark Mode">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <!-- Sidebar Toggle -->
                    <button class="btn btn-outline-secondary me-3 d-none d-md-block" type="button" onclick="toggleSidebar()" title="Toggle Sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (session()->get('avatar')): ?>
                                <img src="<?= session()->get('avatar') ?>" alt="Avatar" class="rounded-circle me-2" width="24" height="24" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                    <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                </div>
                            <?php endif; ?>
                            <?= session()->get('first_name') ? session()->get('first_name') . ' ' . session()->get('last_name') : 'Admin User' ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><h6 class="dropdown-header">Halo, <?= session()->get('first_name') ?>!</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('/profile') ?>">
                                <i class="fas fa-user me-2"></i>Profil Saya
                            </a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/settings') ?>">
                                <i class="fas fa-cog me-2"></i>Pengaturan
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('/auth/logout') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>Keluar
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="content-body">
            <?php
                $flashPayload = [];
                foreach (['success','error','warning','info'] as $type) {
                    $val = session()->getFlashdata($type);
                    if ($val) { $flashPayload[$type] = $val; }
                }
            ?>
            <script>
                // Bridge PHP flashdata to unified toast system (waits until createOptimaToast is defined)
                (function(flashData){
                    if(!flashData || Object.keys(flashData).length===0) return;
                    function pump(){
                        if(typeof window.createOptimaToast==='function') {
                            Object.entries(flashData).forEach(([type,msg])=> createOptimaToast({type:type==='error'?'error':type, title:type.charAt(0).toUpperCase()+type.slice(1), message:msg}));
                            return true;
                        }
                        return false;
                    }
                    if(!pump()) {
                        let attempts=0; const iv=setInterval(()=>{ if(pump()||++attempts>40) clearInterval(iv); },120);
                    }
                })(<?= json_encode($flashPayload) ?>);
            </script>

            <script>
            // Fix: ensure verification list items clickable (some users report dropdown headers not responding)
            document.addEventListener('DOMContentLoaded', function(){
                const restorableSelectors = '.po-group-header, .item-child-item, .unit-child-item, .unit-list-item';
                function restoreClicks(){
                    document.querySelectorAll(restorableSelectors).forEach(el=>{
                        if(getComputedStyle(el).pointerEvents==='none'){
                            el.style.pointerEvents='auto';
                        }
                    });
                }
                restoreClicks();
                const mo = new MutationObserver(restoreClicks);
                mo.observe(document.body,{childList:true,subtree:true});
            });
            </script>

            <!-- Page Content -->
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Company Credit Footer -->
    <footer class="company-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    
                    <div class="d-flex flex-column align-items-end">
                        <div class="d-flex align-items-center">
                            <span class="text-muted small me-1">Powered by:</span>
                            <img src="<?= base_url('assets/images/company-logo.svg') ?>" 
                                alt="SML Rental" 
                                class="company-credit-logo"
                                onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                            <span class="text-muted small d-none">SML Rental</span>
                        </div>

                        <p class="text-muted small mb-0 text-end">
                            © <?= date('Y') ?> OPTIMA - PT Sarana Mitra Luas Tbk. All rights reserved.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </footer>

    <!-- Legacy notification container removed: unified toast system in use -->

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay"></div>

    <!-- Scripts -->
    <!-- jQuery (core) -->
    <!-- jQuery: integrity removed to avoid mismatch blocking; consider self-hosting for SRI -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script>
    // Fallback if CDN jQuery blocked (e.g., CSP/SRI mismatch cached):
    if (typeof window.jQuery === 'undefined') {
        document.write('<script src="<?= base_url('assets/js/vendor/jquery-3.7.1.min.js') ?>"><\/script>');
    }
    </script>
    <!-- Bootstrap Bundle -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vendor: DataTables (loaded deferred; initiate only where needed) -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- Flatpickr -->
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
    <!-- Pickr -->
    <script defer src="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.2/dist/pickr.min.js"></script>
    <!-- noUiSlider -->
    <script defer src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
    
    <!-- OPTIMA Pro JavaScript -->
    <script src="<?= base_url('assets/js/optima-pro.js') ?>"></script>

    
    <!-- Global JavaScript Variables -->
    <script>
        // Set global variables
        window.baseUrl = '<?= base_url() ?>';
        window.csrfToken = '<?= csrf_hash() ?>';
        window.csrfTokenName = '<?= csrf_token() ?>';
        window.currentUser = {
            id: '<?= session()->get('user_id') ?>',
            name: '<?= session()->get('first_name') . ' ' . session()->get('last_name') ?>',
            email: '<?= session()->get('email') ?>',
            role: '<?= session()->get('role') ?>',
            avatar: '<?= session()->get('avatar') ?: base_url('assets/images/default-avatar.svg') ?>'
        };
        
        // Hide loading screen when page is fully loaded
        window.addEventListener('load', function() {
            const loading = document.getElementById('pageLoading');
            if (loading) {
                loading.classList.add('fade-out');
                setTimeout(() => {
                    loading.style.display = 'none';
                }, 300);
            }
        });
        
        // Global error handler
        window.addEventListener('error', function(event) {
            // Only log meaningful errors, ignore null or minor errors
            if (event.error && event.error.message && !event.error.message.includes('Script error')) {
                console.error('Global error:', event.error);
                if (typeof OptimaPro !== 'undefined') {
                    OptimaPro.showNotification('Terjadi kesalahan pada sistem', 'danger');
                }
            }
        });
        
        // Enhanced OptimaPro notification system (unified)
        window.OptimaPro = window.OptimaPro || {};
        window.OptimaPro.showNotification = function(message, type = 'info', duration = 5000) {
            if (typeof window.createOptimaToast !== 'function') { console.warn('Toast system not ready'); return; }
            const t = (type === 'danger') ? 'error' : (type || 'info');
            const title = (t === 'error') ? 'Error' : (t === 'success') ? 'Success' : (t === 'warning') ? 'Warning' : 'Info';
            return window.createOptimaToast({ type: t, title, message, duration });
        };
        
        // Global AJAX setup
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        }

        // Theme initialization and toggle functionality
        (function() {
            // Apply saved theme immediately to prevent flash
            const savedTheme = localStorage.getItem('optima-theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const themeToApply = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            
            document.documentElement.setAttribute('data-bs-theme', themeToApply);
        })();
        
        document.addEventListener('DOMContentLoaded', function() {
            // Direct theme toggle implementation without dependency on OptimaPro
            const themeToggle = document.querySelector('.theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    console.log('Theme toggle button clicked!');
                    const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    
                    // Set theme
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('optima-theme', newTheme);
                    
                    // Update icon
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                    }
                    
                    // Update OptimaPro config if available
                    if (typeof OptimaPro !== 'undefined' && OptimaPro.config) {
                        OptimaPro.config.theme = newTheme;
                    }
                    
                    // Show notification for theme change
                    OptimaPro.showNotification(`Switched to ${newTheme} mode`, 'info', 2000);
                    
                    console.log('Theme switched to:', newTheme);
                });
                
                // Initialize correct icon on load
                const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                const icon = themeToggle.querySelector('i');
                if (icon) {
                    icon.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                }
            }
        });

        // Sidebar toggle functionality
        window.toggleSidebar = function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (sidebar && mainContent) {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Save state to localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('optima-sidebar-collapsed', isCollapsed);
                
                // Show notification
                OptimaPro.showNotification(
                    isCollapsed ? 'Sidebar collapsed' : 'Sidebar expanded', 
                    'info', 
                    1500
                );
                
                // Trigger resize event for charts
                window.dispatchEvent(new Event('resize'));
            }
        };

        // Initialize sidebar state from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedState = localStorage.getItem('optima-sidebar-collapsed');
            if (savedState === 'true') {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');
                if (sidebar && mainContent) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
            }
        });
    </script>
    
    <!-- Page Specific JavaScript -->
    <?= $this->renderSection('javascript') ?>
    
    <!-- Initialize OPTIMA Pro -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap dropdowns first
            if (typeof bootstrap !== 'undefined') {
                // Make sure all dropdowns are properly initialized
                const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
                dropdownElements.forEach(function(element) {
                    new bootstrap.Dropdown(element);
                });
            }
            
            // Initialize application
            if (typeof OptimaPro !== 'undefined') {
                OptimaPro.init();
            }
            
            // Page specific initialization
        });
    </script>
    <?= $this->renderSection('script') ?>
    <script>
    // Unified notification wrapper
    window.OptimaNotify = window.OptimaNotify || {
        success: (m,t='Berhasil') => window.createOptimaToast && createOptimaToast({type:'success', title:t, message:m}),
        error: (m,t='Gagal') => window.createOptimaToast && createOptimaToast({type:'error', title:t, message:m}),
        warning: (m,t='Perhatian') => window.createOptimaToast && createOptimaToast({type:'warning', title:t, message:m}),
        info: (m,t='Info') => window.createOptimaToast && createOptimaToast({type:'info', title:t, message:m})
    };
    // SweetAlert2 monkeypatch to reroute simple toast / trivial result dialogs
    (function(){
        const tryPatch = () => {
            if(!window.Swal || !window.createOptimaToast || window._swalToastPatched) return;
            window._swalToastPatched = true;
            const orig = Swal.fire;
            Swal.fire = function(a,b,c){
                try {
                    if (typeof a==='object' && a && a.toast) {
                        const icon=(a.icon||'info').toLowerCase();
                        if(['success','error','info','warning'].includes(icon)) {
                            createOptimaToast({type:icon,title:a.title||icon.toUpperCase(),message:a.text||a.html||'',duration:a.timer||3000});
                            return Promise.resolve({isConfirmed:true,isToast:true});
                        }
                    }
                    if (typeof a==='string' && typeof b==='string' && typeof c==='string') {
                        const icon=c.toLowerCase();
                        if(['success','error','info','warning'].includes(icon)) {
                            createOptimaToast({type:icon,title:a,message:b});
                            return Promise.resolve({isConfirmed:true,isToast:true});
                        }
                    }
                    // Object signature without toast:true but simple notification (no cancel/input)
                    if (typeof a==='object' && a && !a.toast) {
                        const icon=(a.icon||'').toLowerCase();
                        const isSimple = ['success','error','info','warning'].includes(icon) && !a.showCancelButton && !a.showDenyButton && !a.input;
                        if (isSimple) {
                            // Avoid converting confirmation dialogs with custom confirm text other than default OK/Ok/Tutup
                            const cText = (a.confirmButtonText||'').toLowerCase();
                            const isConfirmLike = cText && !['','ok','oke','tutup','close','ya','yes'].includes(cText); // treat custom action verbs as confirm dialogs
                            if(!isConfirmLike) {
                                createOptimaToast({type:icon||'info', title:a.title||icon.toUpperCase()||'Info', message:a.text||a.html||'', duration:a.timer||4000});
                                return Promise.resolve({isConfirmed:true,isToast:true});
                            }
                        }
                    }
                } catch(e) {}
                return orig.apply(this, arguments);
            };
        };
        if(!tryPatch()) { let tries=0; const iv=setInterval(()=>{ tryPatch(); if(window._swalToastPatched||++tries>50) clearInterval(iv); },200);}    
    })();
    </script>

    <!-- Notification system disabled -->
    <script>
// Notification functions disabled for now
function fetchNotifications() {
    return;
}

function markNotificationRead(id) {
    return;
}

document.addEventListener('DOMContentLoaded', function() {
    // Notification polling disabled
});
    </script>
</body>
</html>
