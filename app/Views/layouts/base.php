<?php 
// All helpers disabled to avoid errors
// helper('rbac');
// helper('simple_rbac');
// helper('global_permission');

// Get current language for HTML lang attribute
$currentLang = service('request')->getLocale();
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" data-bs-theme="light" id="html-root">
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
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    
    <!-- Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    <!-- Pickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.2/dist/themes/nano.min.css" rel="stylesheet">
    
    <!-- noUiSlider CSS -->
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css" rel="stylesheet">
    
    <!-- OPTIMA Pro CSS (Enhanced with Centralized Components) -->
    <link href="<?= base_url('assets/css/optima-pro.css') ?>?v=<?= time() ?>" rel="stylesheet">
    <!-- Select2 Custom CSS -->
    <link href="<?= base_url('assets/css/select2-custom.css') ?>?v=<?= time() ?>" rel="stylesheet">
    <!-- Dashboard Modern CSS -->
    <link href="<?= base_url('assets/css/dashboard-modern.css') ?>?v=<?= time() ?>" rel="stylesheet">
    <!-- Global Permission CSS -->
    <link href="<?= base_url('assets/css/global-permission.css') ?>?v=<?= time() ?>" rel="stylesheet">
    <!-- Notification Popup CSS -->
    <link href="<?= base_url('assets/css/notification-popup.css') ?>?v=<?= time() ?>" rel="stylesheet">
    
    <!-- Global Consistent Table Sorting Headers -->
    <style>
    /* Consistent DataTables-like sorting headers for all manual tables */
    .table-manual-sort thead th {
        position: relative;
        cursor: pointer;
        user-select: none;
        padding-right: 20px !important;
    }

    .table-manual-sort thead th:hover {
        background-color: #f8f9fa;
    }

    .table-manual-sort thead th::after {
        content: "\2195"; /* Up-down arrow */
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.3;
        font-size: 10px;
        color: #6c757d;
    }

    .table-manual-sort thead th.sortable-asc::after {
        content: "\2191"; /* Up arrow */
        opacity: 1;
        color: #0d6efd;
        font-weight: bold;
    }

    .table-manual-sort thead th.sortable-desc::after {
        content: "\2193"; /* Down arrow */
        opacity: 1;
        color: #0d6efd;
        font-weight: bold;
    }

    .table-manual-sort thead th:last-child::after,
    .table-manual-sort thead th[data-no-sort]::after {
        display: none; /* Hide sort arrow on actions column or no-sort columns */
    }
    </style>
    
    <!-- Sidebar Scroll Management -->
    <script src="<?= base_url('assets/js/sidebar-scroll.js') ?>?v=<?= time() ?>"></script>
   
    <!-- Page Specific CSS -->
    <?= $this->renderSection('css') ?>
</head>
<body class="bg-light">
    <!-- Toast Container Bootstrap 5 (pojok kanan atas) -->
    <div class="toast-container position-fixed top-0 end-0 p-3" id="optima-toast-container" style="z-index: 1090;"></div>
    <script>
        // Global function for mark all as read
        window.markAllAsRead = function() {
            if (window.optimaSSENotifications) {
                window.optimaSSENotifications.markAllAsRead();
            }
        };
        
        // Global function for handling notification clicks
        window.handleNotificationClick = function(notificationId, url) {
            console.log('🔔 SSE notification clicked:', notificationId, url);
            
            // Mark as read first
            if (window.optimaSSENotifications) {
                window.optimaSSENotifications.markAsRead(notificationId);
            }
            
            // Navigate to URL if not '#'
            if (url && url !== '#') {
                // Close dropdown first
                const dropdown = document.querySelector('[data-bs-toggle="dropdown"]');
                if (dropdown) {
                    const bsDropdown = bootstrap.Dropdown.getInstance(dropdown);
                    if (bsDropdown) {
                        bsDropdown.hide();
                    }
                }
                
                // Navigate after a short delay
                setTimeout(() => {
                    window.location.href = url;
                }, 100);
            }
        };
        
        // Helper function untuk format waktu relatif (16 jam lalu, dll)
        window.formatRelativeTime = function(timestamp) {
            if (!timestamp) return 'Baru saja';
            
            try {
                const now = new Date();
                const notifTime = new Date(timestamp);
                const diffMs = now - notifTime;
                const diffSec = Math.floor(diffMs / 1000);
                const diffMin = Math.floor(diffSec / 60);
                const diffHour = Math.floor(diffMin / 60);
                const diffDay = Math.floor(diffHour / 24);
                
                if (diffSec < 60) return 'Baru saja';
                if (diffMin < 60) return `${diffMin} menit lalu`;
                if (diffHour < 24) return `${diffHour} jam lalu`;
                if (diffDay < 7) return `${diffDay} hari lalu`;
                
                // Lebih dari 7 hari, tampilkan tanggal
                const day = String(notifTime.getDate()).padStart(2, '0');
                const month = String(notifTime.getMonth() + 1).padStart(2, '0');
                const year = notifTime.getFullYear();
                const hours = String(notifTime.getHours()).padStart(2, '0');
                const minutes = String(notifTime.getMinutes()).padStart(2, '0');
                
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            } catch (e) {
                return 'Baru saja';
            }
        };
        
        // Global toast creator (Bootstrap 5 Toast with optional action button)
        window.createOptimaToast = function({type='info', title='Info', message='', duration=5000, url=null, actionText='Lihat Detail', timestamp=null} = {}) {
            const color = (type==='success') ? 'success' : (type==='warning') ? 'warning' : (type==='error' || type==='danger') ? 'danger' : 'info';
            const icon = (type==='success') ? 'fas fa-check-circle' : (type==='warning') ? 'fas fa-exclamation-triangle' : (type==='error' || type==='danger') ? 'fas fa-times-circle' : 'fas fa-info-circle';
            const iconBg = (type==='success') ? 'text-success' : (type==='warning') ? 'text-warning' : (type==='error' || type==='danger') ? 'text-danger' : 'text-info';
            
            // Format waktu relatif
            const timeText = window.formatRelativeTime(timestamp);
            
            const el = document.createElement('div');
            el.className = `toast`;
            el.setAttribute('role','alert');
            el.setAttribute('aria-live','assertive');
            el.setAttribute('aria-atomic','true');
            
            // Build toast body with optional action button
            let bodyContent = `<div class="toast-body">${escapeHtml(message)}`;
            if (url && url !== '#' && url !== '') {
                bodyContent += `
                    <div class="mt-2 pt-2 border-top">
                        <button type="button" class="btn btn-primary btn-sm me-2" onclick="window.location.href='${escapeHtml(url)}'">
                            <i class="fas fa-external-link-alt me-1"></i>${escapeHtml(actionText)}
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="toast">Tutup</button>
                    </div>`;
            }
            bodyContent += `</div>`;
            
            el.innerHTML = `
                <div class="toast-header">
                    <i class="${icon} ${iconBg} me-2"></i>
                    <strong class="me-auto">${escapeHtml(title)}</strong>
                    <small class="text-muted">${escapeHtml(timeText)}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                ${bodyContent}
            `;
            const container = document.getElementById('optima-toast-container');
            if (container) {
                container.appendChild(el);
            } else {
                document.body.appendChild(el);
            }
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
        
        // Backward compatibility & Helper functions
        window.showToast = window.createOptimaToast; // Alias untuk kompatibilitas
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

    <!-- Global Header (Full Width) -->
    <header class="global-header" id="globalHeader">
        <div class="header-container">
            <!-- Left Section: Logo + Sidebar Toggle -->
            <div class="header-left">
                <button class="btn btn-link sidebar-toggle" type="button" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?= base_url('/welcome') ?>" class="header-brand">
                    <img src="<?= base_url('assets/images/logo-optima.ico') ?>" alt="OPTIMA" class="header-logo">
                    <span class="header-brand-text">OPTIMA</span>
                </a>
            </div>
            
            <!-- Center Section: Title + Breadcrumb (Small) -->
            <div class="header-center">
                <h1 class="header-title"><?= $title ?? 'Dashboard' ?></h1>
                <?php if (isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs) > 0): ?>
                <nav class="header-breadcrumb">
                    <ol class="breadcrumb-small">
                        <?php foreach ($breadcrumbs as $key => $breadcrumb): ?>
                            <?php if ($key === array_key_last($breadcrumbs)): ?>
                                <li class="breadcrumb-item-small active"><?= $breadcrumb ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item-small">
                                    <a href="<?= base_url($key) ?>"><?= $breadcrumb ?></a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                <?php endif; ?>
            </div>
            
            <!-- Right Section: Controls -->
            <div class="header-right">
                <!-- Language Switcher -->
                <div class="dropdown">
                    <button class="header-control-btn" type="button" data-bs-toggle="dropdown" title="<?= lang('App.select_language') ?>">
                        <?php 
                        $currentLang = service('request')->getLocale();
                        $langCode = strtoupper($currentLang);
                        ?>
                        <span class="fw-semibold"><?= $langCode ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item <?= $currentLang === 'id' ? 'active' : '' ?>" 
                               href="<?= base_url('language/switch/id') ?>">
                                <span class="me-2">🇮🇩</span>
                                Bahasa Indonesia
                                <?php if ($currentLang === 'id'): ?>
                                    <i class="fas fa-check float-end text-success mt-1"></i>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= $currentLang === 'en' ? 'active' : '' ?>" 
                               href="<?= base_url('language/switch/en') ?>">
                                <span class="me-2">🇬🇧</span>
                                English
                                <?php if ($currentLang === 'en'): ?>
                                    <i class="fas fa-check float-end text-success mt-1"></i>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="header-control-btn" type="button" data-bs-toggle="dropdown" title="Notifikasi">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge" style="display: none;">
                            <span class="notification-count" data-realtime="notification_count">0</span>
                        </span>
                    </button>
                    <ul id="notificationDropdownMenu" class="dropdown-menu dropdown-menu-end notification-dropdown">
                        <li><h6 class="dropdown-header d-flex justify-content-between align-items-center">
                            <span>Notifikasi</span>
                            <button class="btn btn-sm btn-primary" onclick="markAllAsRead()" style="font-size: 0.7rem;">
                                <i class="fas fa-check-double me-1"></i>Mark All
                            </button>
                        </h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="notification-item">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mb-0 small mt-2">Memuat notifikasi...</p>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="<?= base_url('/notifications') ?>">
                            <i class="fas fa-bell me-2"></i>Lihat Semua Notifikasi
                        </a></li>
                    </ul>
                </div>
                
                <!-- Theme Toggle -->
                <button class="header-control-btn theme-toggle" type="button" title="Toggle Dark Mode">
                    <i class="fas fa-moon"></i>
                </button>
                
                <!-- User Profile -->
                <div class="dropdown">
                    <button class="header-control-btn header-profile-btn" type="button" data-bs-toggle="dropdown" title="Profil User">
                        <?php 
                        $userAvatar = session()->get('avatar');
                        if ($userAvatar && !filter_var($userAvatar, FILTER_VALIDATE_URL)) {
                            $userAvatar = base_url($userAvatar);
                        }
                        ?>
                        <?php if ($userAvatar): ?>
                            <img src="<?= $userAvatar ?>" alt="Avatar" class="user-avatar">
                        <?php else: ?>
                            <div class="user-avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <span class="user-name"><?= session()->get('first_name') ?? 'User' ?></span>
                        <i class="fas fa-chevron-down user-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end user-dropdown">
                        <li><h6 class="dropdown-header">Halo, <?= session()->get('first_name') ?? 'User' ?>!</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('/profile') ?>">
                            <i class="fas fa-user me-2"></i>Profil Saya
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

    <!-- Enhanced Sidebar -->
    <?= $this->include('layouts/sidebar_new') ?>
    
    <!-- Main Content -->
    <main class="main-content" id="mainContent">
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
            <div class="content-body">
                <?= $this->renderSection('content') ?>
            </div>
        </main>

    <!-- Admin Footer -->
    <footer class="admin-footer">
        
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0 text-muted">© <?= date('Y') ?> OPTIMA - PT Sarana Mitra Luas Tbk. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex align-items-center justify-content-md-end">
                    <span class="text-muted me-2">Powered by:</span>
                    <img src="<?= base_url('assets/images/company-logo.svg') ?>" 
                            alt="SML Rental" 
                            style="height: 24px; opacity: 0.8;"
                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                    <span class="text-muted d-none">SML Rental</span>
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
    
    <!-- OPTIMA Language Helper for Multilingual Support -->
    <script src="<?= base_url('assets/js/language-helper.js') ?>?v=<?= time() ?>"></script>
    <script>
    // Initialize current language from session
    if (typeof LanguageHelper !== 'undefined') {
        LanguageHelper.setLanguage('<?= service('request')->getLocale() ?>');
    }
    </script>
    <!-- Vendor: DataTables (loaded deferred; initiate only where needed) -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- Flatpickr -->
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
    
    <!-- Moment.js (required for daterangepicker) -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    
    <!-- Date Range Picker JS -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    <!-- Global Date Range Picker Script (loaded after daterangepicker) -->
    <script src="<?= base_url('assets/js/global-daterange.js') ?>?v=<?= time() ?>"></script>
    
    <!-- DataTable Date Filter Mixin -->
    <script src="<?= base_url('assets/js/datatable-datefilter-mixin.js') ?>?v=<?= time() ?>"></script>
    
    <!-- Page Date Filter Helper (Universal API for all pages) -->
    <script src="<?= base_url('assets/js/page-date-filter-helper.js') ?>?v=<?= time() ?>"></script>
    
    <!-- Pickr -->
    <script defer src="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.2/dist/pickr.min.js"></script>
    <!-- noUiSlider -->
    <script defer src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    
    <!-- Global Consistent Sorting Headers JavaScript -->
    <script>
    // Global function for consistent manual table sorting headers with actual sorting
    function initializeManualTableSorting(tableSelector) {
        const $table = $(tableSelector);
        const $tbody = $table.find('tbody');
        
        $(tableSelector + ' thead th:not([data-no-sort]):not(:last-child)').on('click', function() {
            var $this = $(this);
            var columnIndex = $this.index();
            var currentDirection = 'none';
            
            // Determine current and new direction
            if ($this.hasClass('sortable-asc')) {
                currentDirection = 'desc';
                $this.removeClass('sortable-asc').addClass('sortable-desc');
            } else if ($this.hasClass('sortable-desc')) {
                currentDirection = 'asc';
                $this.removeClass('sortable-desc').addClass('sortable-asc');
            } else {
                currentDirection = 'asc';
                $this.addClass('sortable-asc');
            }
            
            // Remove sorting classes from other columns
            $table.find('thead th').not($this).removeClass('sortable-asc sortable-desc');
            
            // Perform actual sorting
            const rows = $tbody.find('tr').toArray();
            rows.sort(function(a, b) {
                const aText = $(a).find('td').eq(columnIndex).text().trim();
                const bText = $(b).find('td').eq(columnIndex).text().trim();
                
                // Try to parse as numbers first
                const aNum = parseFloat(aText.replace(/[^\d.-]/g, ''));
                const bNum = parseFloat(bText.replace(/[^\d.-]/g, ''));
                
                let comparison = 0;
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    // Numeric comparison
                    comparison = aNum - bNum;
                } else {
                    // Text comparison
                    comparison = aText.localeCompare(bText, 'id', {numeric: true});
                }
                
                return currentDirection === 'asc' ? comparison : -comparison;
            });
            
            // Reorder rows in DOM
            $tbody.empty().append(rows);
            
            // Trigger custom sorting event for additional handling if needed
            $this.trigger('manual-sort', {
                column: columnIndex,
                direction: currentDirection,
                sortedRows: rows
            });
        });
        
        // Store original row order for potential reset
        $table.data('originalOrder', $tbody.find('tr').toArray());
    }

    // Auto-initialize tables with class 'table-manual-sort' on document ready
    $(document).ready(function() {
        if ($('.table-manual-sort').length > 0) {
            $('.table-manual-sort').each(function() {
                var tableId = $(this).attr('id') || 'table-' + Math.random().toString(36).substr(2, 9);
                if (!$(this).attr('id')) {
                    $(this).attr('id', tableId);
                }
                initializeManualTableSorting('#' + tableId);
            });
        }
    });
    </script>
    
    <!-- Notification Sound Generator -->
    <script src="<?= base_url('assets/js/notification-sound-generator.js') ?>?v=<?= time() ?>"></script>
    
    <!-- OPTIMA SPA Main System (Single File) -->
    <script src="<?= base_url('assets/js/optima-spa-main.js') ?>?v=<?= time() ?>"></script>
    
    <!-- Global Permission System -->
    <script src="<?= base_url('assets/js/global-permission.js') ?>?v=<?= time() ?>"></script>
    
    
    
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
        
        // Global permissions for JavaScript
        window.globalPermissions = {
            view: true,
            create: true,
            edit: true,
            delete: true,
            export: true
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
        
        // Global fetch wrapper to auto-attach CSRF token and X-Requested-With for same-origin non-GET requests
        (function(){
            try {
                const _fetch = window.fetch.bind(window);
                window.fetch = function(input, init){
                    init = init || {};
                    try {
                        const url = (typeof input === 'string') ? input : (input && input.url) || '';
                        const isSameOrigin = url && (url.startsWith(window.location.origin) || url.startsWith('/') || url.startsWith(window.baseUrl));
                        const method = (init.method || (typeof input !== 'string' && input.method) || 'GET').toString().toUpperCase();
                        if (isSameOrigin) {
                            init.headers = init.headers || {};
                            // normalize Headers instance
                            if (init.headers instanceof Headers) {
                                // leave as-is; set via append if not present
                                if (method !== 'GET') {
                                    if (!init.headers.has('X-CSRF-TOKEN')) init.headers.set('X-CSRF-TOKEN', window.csrfToken);
                                    if (!init.headers.has('X-Requested-With')) init.headers.set('X-Requested-With', 'XMLHttpRequest');
                                }
                            } else {
                                if (method !== 'GET') {
                                    if (!('X-CSRF-TOKEN' in init.headers) && !('x-csrf-token' in init.headers)) init.headers['X-CSRF-TOKEN'] = window.csrfToken;
                                    if (!('X-Requested-With' in init.headers) && !('x-requested-with' in init.headers)) init.headers['X-Requested-With'] = 'XMLHttpRequest';
                                }
                            }
                        }
                    } catch(e){}
                    return _fetch(input, init);
                };
            } catch(e) {
                console.warn('Failed to install fetch wrapper for CSRF:', e);
            }
        })();
        
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

        // Theme initialization with tracking prevention fallback
        (function() {
            let savedTheme = 'light';
            
            // Safe localStorage access with tracking prevention fallback
            try {
                savedTheme = localStorage.getItem('optima-theme');
            } catch (e) {
                console.warn('LocalStorage blocked by tracking prevention, using sessionStorage fallback');
                try {
                    savedTheme = sessionStorage.getItem('optima-theme');
                } catch (e2) {
                    console.warn('SessionStorage also blocked, using cookie fallback');
                    const themeMatch = document.cookie.match(/optima-theme=([^;]+)/);
                    savedTheme = themeMatch ? themeMatch[1] : null;
                }
            }
            
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const themeToApply = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            
            document.documentElement.setAttribute('data-bs-theme', themeToApply);
        })();
        
        // Safe storage function
        function safeSaveTheme(theme) {
            try {
                localStorage.setItem('optima-theme', theme);
            } catch (e) {
                try {
                    sessionStorage.setItem('optima-theme', theme);
                } catch (e2) {
                    // Cookie fallback with 30 days expiry
                    const date = new Date();
                    date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
                    document.cookie = `optima-theme=${theme}; expires=${date.toUTCString()}; path=/; SameSite=Lax`;
                }
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced Theme toggle system
            const themeToggle = document.querySelector('.theme-toggle');
            
            // Initialize icon based on current theme
            function updateThemeIcon() {
                if (!themeToggle) return;
                
                const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                const icon = themeToggle.querySelector('i');
                if (icon) {
                    icon.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                }
                themeToggle.title = `Switch to ${currentTheme === 'dark' ? 'Light' : 'Dark'} Mode`;
            }
            
            if (themeToggle) {
                // Set initial icon
                updateThemeIcon();
                
                themeToggle.addEventListener('click', function() {
                    console.log('Theme toggle button clicked!');
                    const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    
                    // Set theme with smooth transition
                    document.documentElement.style.transition = 'all 0.3s ease';
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    
                    // Safe theme saving
                    safeSaveTheme(newTheme);
                    
                    // Update icon
                    updateThemeIcon();
                    
                    // Trigger custom event for other components
                    window.dispatchEvent(new CustomEvent('themeChanged', { 
                        detail: { theme: newTheme } 
                    }));
                    
                    // Update OPTIMA state if available
                    if (window.OPTIMA && window.OPTIMA.initialized) {
                        window.OPTIMA.state.theme = newTheme;
                        window.OPTIMA.log(`Theme switched to ${newTheme} mode`);
                    }
                    
                    // Remove transition after theme change
                    setTimeout(() => {
                        document.documentElement.style.transition = '';
                    }, 300);
                    
                    console.log(`Theme switched to: ${newTheme}`);
                });
                
                // Safe theme retrieval function
                function safeGetTheme() {
                    try {
                        return localStorage.getItem('optima-theme');
                    } catch (e) {
                        try {
                            return sessionStorage.getItem('optima-theme');
                        } catch (e2) {
                            // Check cookies as final fallback
                            const cookies = document.cookie.split(';');
                            const themeCookie = cookies.find(cookie => cookie.trim().startsWith('optima-theme='));
                            return themeCookie ? themeCookie.split('=')[1] : null;
                        }
                    }
                }
                
                // Listen for system theme changes
                if (window.matchMedia) {
                    try {
                        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                            if (!safeGetTheme()) {
                                const newTheme = e.matches ? 'dark' : 'light';
                                document.documentElement.setAttribute('data-bs-theme', newTheme);
                                updateThemeIcon();
                                console.log(`System theme changed to: ${newTheme}`);
                            }
                        });
                    } catch (e) {
                        console.warn('System theme detection not available:', e.message);
                    }
                }
                
                // Final initialization complete
            }
        });

        // Sidebar toggle functionality - Now handled by OPTIMA unified system
        // window.toggleSidebar function is provided by optima-unified.js
    </script>
    
    <!-- Page Specific JavaScript -->
    <?= $this->renderSection('javascript') ?>
    
    <!-- Initialize OPTIMA Unified System -->
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
            
            // OPTIMA unified system will auto-initialize
            // No need for manual initialization
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

    <!-- ==================================================================== -->
    <!-- LIGHTWEIGHT POLLING NOTIFICATION SYSTEM -->
    <!-- ==================================================================== -->
    <script src="<?= base_url('assets/js/notification-lightweight.js') ?>"></script>

    <!-- Metis Dashboard Style Sidebar Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            // Check if elements exist
            if (!sidebarToggle || !sidebar || !mainContent) {
                console.warn('Sidebar elements not found');
                return;
            }
            
            // Safe localStorage access for sidebar state
            function getSidebarState() {
                try {
                    return localStorage.getItem('sidebarCollapsed') === 'true';
                } catch (e) {
                    console.warn('localStorage unavailable for sidebar state');
                    return false;
                }
            }
            
            function setSidebarState(collapsed) {
                try {
                    localStorage.setItem('sidebarCollapsed', collapsed.toString());
                } catch (e) {
                    console.warn('localStorage unavailable for sidebar state');
                }
            }
            
            // Initialize sidebar state
            const isCollapsed = getSidebarState();
            if (isCollapsed) {
                document.body.classList.add('sidebar-collapsed');
            }
            
            // Toggle sidebar function
            function toggleSidebar() {
                const isCurrentlyCollapsed = document.body.classList.contains('sidebar-collapsed');
                
                if (isCurrentlyCollapsed) {
                    // Expand sidebar
                    document.body.classList.remove('sidebar-collapsed');
                    setSidebarState(false);
                } else {
                    // Collapse sidebar
                    document.body.classList.add('sidebar-collapsed');
                    setSidebarState(true);
                }
            }
            
            // Attach toggle event
            sidebarToggle.addEventListener('click', toggleSidebar);
            
            // Handle mobile/tablet view
            function handleResize() {
                const isMobile = window.innerWidth <= 768;
                
                if (isMobile) {
                    // Mobile behavior - hide sidebar by default
                    document.body.classList.remove('sidebar-collapsed');
                } else {
                    // Desktop behavior - restore saved state
                    const isCollapsed = getSidebarState();
                    if (isCollapsed) {
                        document.body.classList.add('sidebar-collapsed');
                    }
                    sidebar.classList.remove('show');
                }
            }
            
            // Initial resize check
            handleResize();
            
            // Listen to window resize
            window.addEventListener('resize', handleResize);
            
            // Enterprise Dropdown Positioning
            function initializeDropdownPositioning() {
                const groupItems = document.querySelectorAll('.nav-group-item');
                
                groupItems.forEach((item, index) => {
                    const dropdown = item.querySelector('.nav-dropdown');
                    if (!dropdown) return;
                    
                    item.addEventListener('mouseenter', () => {
                        if (!document.body.classList.contains('sidebar-collapsed')) return;
                        
                        // Calculate optimal position
                        const itemRect = item.getBoundingClientRect();
                        const dropdownHeight = dropdown.offsetHeight;
                        const viewportHeight = window.innerHeight;
                        
                        // Position dropdown smartly to avoid viewport overflow
                        let topPosition = itemRect.top - 60; // Subtract header height
                        
                        // Adjust if dropdown would overflow bottom
                        if (topPosition + dropdownHeight > viewportHeight - 20) {
                            topPosition = viewportHeight - dropdownHeight - 20;
                        }
                        
                        // Ensure minimum top position
                        if (topPosition < 20) {
                            topPosition = 20;
                        }
                        
                        dropdown.style.top = topPosition + 'px';
                    });
                });
            }
            
            // Initialize dropdown positioning after DOM is ready
            setTimeout(initializeDropdownPositioning, 100);
            
            // Reinitialize on window resize
            window.addEventListener('resize', initializeDropdownPositioning);
            
            // Close mobile sidebar when clicking outside
            document.addEventListener('click', function(e) {
                const isMobile = window.innerWidth <= 768;
                if (isMobile && sidebar.classList.contains('show')) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        });
        
        // Legacy support for existing toggle function calls
        function toggleSidebar() {
            const toggleButton = document.getElementById('sidebarToggle');
            if (toggleButton) {
                toggleButton.click();
            }
        }
    </script>
</body>
</html>
