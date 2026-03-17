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
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/logo-optima.ico') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
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
    
    <!-- Chart.js (deferred for performance) -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    
    <!-- Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    <!-- Pickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.2/dist/themes/nano.min.css" rel="stylesheet">
    
    <!-- noUiSlider CSS -->
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css" rel="stylesheet">
    
    <!-- OPTIMA Pro CSS (Enhanced with Centralized Components) -->
    <link href="<?= base_url('assets/css/desktop/optima-pro.css') ?>?v=<?= filemtime(FCPATH.'assets/css/desktop/optima-pro.css') ?>" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="<?= base_url('assets/css/plugins/select2.min.css') ?>" rel="stylesheet">
    <!-- Select2 Custom CSS -->
    <link href="<?= base_url('assets/css/plugins/select2-custom.css') ?>?v=<?= filemtime(FCPATH.'assets/css/plugins/select2-custom.css') ?>" rel="stylesheet">
    <!-- Dashboard Modern CSS -->
    <link href="<?= base_url('assets/css/desktop/dashboard-modern.css') ?>?v=<?= filemtime(FCPATH.'assets/css/desktop/dashboard-modern.css') ?>" rel="stylesheet">
    <!-- Global Permission CSS -->
    <link href="<?= base_url('assets/css/plugins/global-permission.css') ?>?v=<?= filemtime(FCPATH.'assets/css/plugins/global-permission.css') ?>" rel="stylesheet">
    <!-- Notification Popup CSS -->
    <link href="<?= base_url('assets/css/plugins/notification-popup.css') ?>?v=<?= filemtime(FCPATH.'assets/css/plugins/notification-popup.css') ?>" rel="stylesheet">

    <!-- OPTIMA DataTable CSS - Centralized Table Styling System -->
    <link href="<?= base_url('assets/css/desktop/optima-datatable.css') ?>?v=<?= filemtime(FCPATH.'assets/css/desktop/optima-datatable.css') ?>" rel="stylesheet">

    <!-- OPTIMA Sidebar CodingNepal Style - Floating, expand/collapse -->
    <link href="<?= base_url('assets/css/desktop/optima-sidebar-codingnepal.css') ?>?v=<?= filemtime(FCPATH.'assets/css/desktop/optima-sidebar-codingnepal.css') ?>" rel="stylesheet">

    <!-- Material Symbols Rounded (sidebar icons) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=block" />

    <!-- Sidebar Scroll Management (deferred) -->
    <script defer src="<?= base_url('assets/js/sidebar-scroll.js') ?>?v=<?= filemtime(FCPATH.'assets/js/sidebar-scroll.js') ?>"></script>
   
    <!-- OPTIMA Command Palette Search Button — Header Styles -->
    <style>
    #header-search-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 10px;
        padding: 6px 14px;
        color: #495057;
        font-size: 0.82rem;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s, color 0.2s, box-shadow 0.2s;
        white-space: nowrap;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    #header-search-btn:hover {
        background: #ffffff;
        border-color: rgba(0, 97, 242, 0.4);
        color: #0061f2;
        box-shadow: 0 2px 6px rgba(0, 97, 242, 0.12);
    }
    #header-search-btn .header-search-kbd {
        display: inline-flex;
        gap: 2px;
        opacity: 0.7;
    }
    #header-search-btn .header-search-kbd kbd {
        background: rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.12);
        border-radius: 4px;
        padding: 1px 5px;
        font-size: 0.68rem;
        font-family: inherit;
        line-height: 1.4;
        color: #6c757d;
    }
    #header-search-btn .header-search-label {
        opacity: 0.85;
        color: #6c757d;
    }
    /* Hide label + kbd on very small screens, keep only icon */
    @media (max-width: 480px) {
        #header-search-btn .header-search-label,
        #header-search-btn .header-search-kbd { display: none; }
        #header-search-btn { padding: 6px 10px; }
    }
    @media (max-width: 768px) {
        #header-search-btn .header-search-kbd { display: none; }
    }
    /* Dark mode */
    html[data-bs-theme="dark"] #header-search-btn {
        background: rgba(255,255,255,0.07);
        border-color: rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.85);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }
    html[data-bs-theme="dark"] #header-search-btn:hover {
        background: rgba(255,255,255,0.14);
        border-color: rgba(255,255,255,0.25);
        color: #fff;
    }
    html[data-bs-theme="dark"] #header-search-btn .header-search-kbd kbd {
        background: rgba(255,255,255,0.15);
        border-color: rgba(255,255,255,0.25);
        color: rgba(255,255,255,0.85);
    }
    html[data-bs-theme="dark"] #header-search-btn .header-search-label {
        color: rgba(255,255,255,0.85);
    }
    </style>

    <!-- Page Specific CSS -->
    <?= $this->renderSection('css') ?>
</head>
<body class="bg-light cn-sidebar-layout">
    <!-- Toast Container Bootstrap 5 (pojok kanan atas) -->
    <div class="toast-container position-fixed top-0 end-0 p-3" id="optima-toast-container" style="z-index: 1090;"></div>
    <script>
        // ============================================================
        // GLOBAL CSRF TOKEN HELPER - Available to ALL views/pages
        // Reads from cookie dynamically to prevent stale token issues
        // UPDATED: Returns object with {name, value, hash} for dynamic CSRF
        // ============================================================
        window.getCsrfTokenData = function() {
            // Priority 1: Try reading from cookies
            try {
                const cookies = document.cookie.split(';');
                
                // CI4 CSRF cookie starts with 'csrf_cookie_name' prefix (default)
                // But actual name is dynamic like: csrf_cookie_name_abc123
                for (let cookie of cookies) {
                    const [name, value] = cookie.trim().split('=');
                    // Match cookies that start with csrf
                    if (name && name.startsWith('csrf')) {
                        const tokenData = {
                            cookieName: name,
                            tokenName: <?= json_encode(csrf_token()) ?>,
                            tokenValue: decodeURIComponent(value),
                            hash: decodeURIComponent(value) // alias
                        };
                        return tokenData;
                    }
                }
            } catch (e) {
                // Cookie access blocked by browser
            }
            
            // Priority 2: Fallback to meta tag (always works, not blocked by tracking prevention)
            const metaCsrf = document.querySelector('meta[name="csrf-token"]');
            if (metaCsrf && metaCsrf.content) {
                const fallbackData = {
                    cookieName: <?= json_encode(config('Security')->cookieName ?? 'csrf_cookie_name') ?>,
                    tokenName: <?= json_encode(csrf_token()) ?>,
                    tokenValue: metaCsrf.content,
                    hash: metaCsrf.content
                };
                return fallbackData;
            }

            // Priority 3: Last resort - empty token (will fail, but at least won't crash)
            return {
                cookieName: 'csrf_cookie_name',
                tokenName: <?= json_encode(csrf_token()) ?>,
                tokenValue: '',
                hash: ''
            };
        };
        
        // Legacy function - returns only value for backward compatibility
        window.getCsrfToken = function() {
            return window.getCsrfTokenData().tokenValue;
        };
        
        // Global token variables (updated dynamically)
        window.csrfTokenName = <?= json_encode(csrf_token()) ?>;
        window.csrfTokenValue = window.getCsrfToken();
        window.csrfToken = window.csrfTokenValue; // Alias for backward compatibility
        // Refresh token alias on each AJAX call via jQuery global setup (set below after jQuery loads)

        // ============================================================
        // GLOBAL AJAX ERROR HANDLER - Handle session expiration
        // ============================================================
        (function() {
            const baseUrl = <?= json_encode(base_url()) ?>;
            const loginUrl = baseUrl + '/auth/login';

            // Override fetch API
            const originalFetch = window.fetch;
            window.fetch = function(input, init) {
                return originalFetch.apply(this, arguments).then(function(response) {
                    // Check for 401 (Unauthorized) or 403 (Forbidden)
                    if (response.status === 401 || response.status === 403) {
                        // Try to check if it's a JSON response
                        response.clone().json().catch(function() {
                            // Not JSON - it's a full page redirect
                            if (window.OptimaNotify) OptimaNotify.error('Session Anda telah habis. Silakan login kembali.');
                            else alert('Session Anda telah habis. Silakan login kembali.');
                            window.location.href = loginUrl;
                        });
                    }
                    return response;
                });
            };

            // Override jQuery AJAX (if jQuery is loaded)
            if (typeof $ !== 'undefined') {
                $(document).ajaxError(function(event, xhr, settings, error) {
                    if (xhr.status === 401 || xhr.status === 403) {
                        if (window.OptimaNotify) OptimaNotify.error('Session Anda telah habis. Silakan login kembali.');
                        else alert('Session Anda telah habis. Silakan login kembali.');
                        window.location.href = loginUrl;
                    }
                });
            }
        })();

        // Global function for mark all as read
        window.markAllAsRead = function() {
            if (window.optimaSSENotifications) {
                window.optimaSSENotifications.markAllAsRead();
            }
        };
        
        // Global function for handling notification clicks (READ-ONLY)
        // Behavior: mark as read + close dropdown. No redirect, no modal navigation.
        window.handleNotificationClick = function(notificationId, url) { // url kept for backward-compat, ignored
            if (window.optimaSSENotifications) {
                window.optimaSSENotifications.markAsRead(notificationId);
            }

            const dropdown = document.querySelector('[data-bs-toggle="dropdown"]');
            if (dropdown) {
                const bsDropdown = bootstrap.Dropdown.getInstance(dropdown);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
            }
        };
        
        /**
         * Universal Modal Router
         * Attempts to open detail modal based on URL pattern
         * Returns true if modal was opened, false if navigation needed
         */
        window.tryOpenModalFromUrl = function(url) {
            if (!url || url === '#') return false;
            
 
            
            // Parse URL to extract module and ID
            const urlObj = new URL(url, window.location.origin);
            const pathname = urlObj.pathname;
            
            // ==================== PURCHASING ====================
            // Pattern: /purchasing/detail/{id} or /purchasing/po/detail/{id}
            let match = pathname.match(/\/purchasing\/(?:po\/)?detail\/(\d+)/);
            if (match) {
                const poId = match[1];

                if (typeof viewPODetail === 'function') {
                    viewPODetail(poId);
                    return true;
                } else {
                    window.location.href = '/optima/public/purchasing#view-po-' + poId;
                    return true;
                }
            }
            
            // ==================== SERVICE ====================
            // Pattern: /service/work-orders/detail/{id}
            match = pathname.match(/\/service\/work-orders\/detail\/(\d+)/);
            if (match) {
                const woId = match[1];

                if (typeof viewWorkOrderDetail === 'function') {
                    viewWorkOrderDetail(woId);
                    return true;
                } else {
                    window.location.href = '/optima/public/service#view-wo-' + woId;
                    return true;
                }
            }
            
            // ==================== MARKETING ====================
            // Pattern: /marketing/spk/detail/{id}
            match = pathname.match(/\/marketing\/spk\/detail\/(\d+)/);
            if (match) {
                const spkId = match[1];

                if (typeof viewSPKDetail === 'function') {
                    viewSPKDetail(spkId);
                    return true;
                } else {
                    window.location.href = '/optima/public/marketing#view-spk-' + spkId;
                    return true;
                }
            }
            
            // ==================== OPERATIONAL ====================
            // Pattern: /operational/delivery/detail/{id}
            match = pathname.match(/\/operational\/delivery\/detail\/(\d+)/);
            if (match) {
                const diId = match[1];
                
                if (typeof viewDIDetail === 'function') {
                    viewDIDetail(diId);
                    return true;
                } else {
                    window.location.href = '/optima/public/operational/delivery#view-di-' + diId;
                    return true;
                }
            }
            
            // No modal handler found for URL
            return false;
        };
        
        /**
         * Handle URL hash on page load (for deep linking)
         * Example: /purchasing#view-po-123
         */
        window.handleUrlHashModal = function() {
            const hash = window.location.hash;
            if (!hash) return;
            
            // Handling URL hash
            
            // Pattern: #view-po-{id}
            let match = hash.match(/#view-po-(\d+)/);
            if (match && typeof viewPODetail === 'function') {
                const poId = match[1];
                setTimeout(() => viewPODetail(poId), 500);
                return;
            }
            
            // Pattern: #view-wo-{id}
            match = hash.match(/#view-wo-(\d+)/);
            if (match && typeof viewWorkOrderDetail === 'function') {
                const woId = match[1];
                setTimeout(() => viewWorkOrderDetail(woId), 500);
                return;
            }
            
            // Pattern: #view-spk-{id}
            match = hash.match(/#view-spk-(\d+)/);
            if (match && typeof viewSPKDetail === 'function') {
                const spkId = match[1];
                setTimeout(() => viewSPKDetail(spkId), 500);
                return;
            }
            
            // Pattern: #view-di-{id}
            match = hash.match(/#view-di-(\d+)/);
            if (match && typeof viewDIDetail === 'function') {
                const diId = match[1];
                setTimeout(() => viewDIDetail(diId), 500);
                return;
            }
        };
        
        // Call hash handler on page load
        document.addEventListener('DOMContentLoaded', function() {
            window.handleUrlHashModal();
        });
        
        // Helper function untuk format waktu relatif (16 jam lalu, dll)
        window.formatRelativeTime = function(timestamp) {
            const lang = window.lang || ((key) => key);
            if (!timestamp) return lang('just_now');

            try {
                const now = new Date();
                const notifTime = new Date(timestamp);
                const diffMs = now - notifTime;
                const diffSec = Math.floor(diffMs / 1000);
                const diffMin = Math.floor(diffSec / 60);
                const diffHour = Math.floor(diffMin / 60);
                const diffDay = Math.floor(diffHour / 24);

                if (diffSec < 60) return lang('just_now');
                if (diffMin < 60) return lang('minutes_ago').replace('{count}', diffMin);
                if (diffHour < 24) return lang('hours_ago').replace('{count}', diffHour);
                if (diffDay < 7) return lang('days_ago').replace('{count}', diffDay);

                // Lebih dari 7 hari, tampilkan tanggal
                const day = String(notifTime.getDate()).padStart(2, '0');
                const month = String(notifTime.getMonth() + 1).padStart(2, '0');
                const year = notifTime.getFullYear();
                const hours = String(notifTime.getHours()).padStart(2, '0');
                const minutes = String(notifTime.getMinutes()).padStart(2, '0');

                return `${day}/${month}/${year} ${hours}:${minutes}`;
            } catch (e) {
                return lang('just_now');
            }
        };
        
        // Global toast creator (Bootstrap 5 Toast with optional action button)
        // ES5-compatible version (no object destructuring for older browsers)
        window.createOptimaToast = function(options) {
            // Default values without object destructuring (ES5-compatible)
            options = options || {};
            var type = options.type !== undefined ? options.type : 'info';
            var title = options.title;
            var message = options.message !== undefined ? options.message : '';
            var duration = options.duration !== undefined ? options.duration : 5000;
            var url = options.url;
            var actionText = options.actionText;
            var timestamp = options.timestamp;
            
            // Safe evaluation of window.lang (executed when function is CALLED, not defined)
            if (!title) {
                title = (typeof window.lang === 'function') ? window.lang('info') : 'Info';
            }
            if (!actionText) {
                actionText = (typeof window.lang === 'function') ? window.lang('view_detail') : 'Lihat Detail';
            }
            
            var color = (type==='success') ? 'success' : (type==='warning') ? 'warning' : (type==='error' || type==='danger') ? 'danger' : 'info';
            var icon = (type==='success') ? 'fas fa-check-circle' : (type==='warning') ? 'fas fa-exclamation-triangle' : (type==='error' || type==='danger') ? 'fas fa-times-circle' : 'fas fa-info-circle';
            var iconBg = (type==='success') ? 'text-success' : (type==='warning') ? 'text-warning' : (type==='error' || type==='danger') ? 'text-danger' : 'text-info';
            
            // Format waktu relatif
            var timeText = window.formatRelativeTime(timestamp);
            
            var el = document.createElement('div');
            el.className = 'toast';
            el.setAttribute('role','alert');
            el.setAttribute('aria-live','assertive');
            el.setAttribute('aria-atomic','true');
            
            // Build toast body with optional action button
            var bodyContent = '<div class="toast-body">' + escapeHtml(message);
            if (url && url !== '#' && url !== '') {
                bodyContent += '<div class="mt-2 pt-2 border-top">' +
                    '<button type="button" class="btn btn-primary btn-sm me-2" onclick="window.location.href=\'' + escapeHtml(url) + '\'">' +
                    '<i class="fas fa-external-link-alt me-1"></i>' + escapeHtml(actionText) +
                    '</button>' +
                    '<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="toast">Tutup</button>' +
                    '</div>';
            }
            bodyContent += '</div>';
            
            el.innerHTML = 
                '<div class="toast-header">' +
                    '<i class="' + icon + ' ' + iconBg + ' me-2"></i>' +
                    '<strong class="me-auto">' + escapeHtml(title) + '</strong>' +
                    '<small class="text-muted">' + escapeHtml(timeText) + '</small>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>' +
                '</div>' +
                bodyContent;
                
            var container = document.getElementById('optima-toast-container');
            if (container) {
                container.appendChild(el);
            } else {
                document.body.appendChild(el);
            }
            try {
                if (window.bootstrap && bootstrap.Toast) {
                    var t = new bootstrap.Toast(el, { delay: duration });
                    el.addEventListener('hidden.bs.toast', function() { el.remove(); });
                    t.show();
                } else if (window.$ && typeof $('.toast').toast === 'function') {
                    $(el).toast({ delay: duration }).toast('show');
                    $(el).on('hidden.bs.toast', function(){ $(this).remove(); });
                } else {
                    // Fallback: auto-remove without animation
                    setTimeout(function() { if (el && el.remove) el.remove(); }, duration);
                }
            } catch (e) {
                setTimeout(function() { if (el && el.remove) el.remove(); }, duration);
            }
        };
        
        function escapeHtml(str){ 
            return String(str !== null && str !== undefined ? str : '').replace(/[&<>"']/g, function(s) {
                return ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]);
            }); 
        }
        
        // ============================================================
        // GLOBAL SWEETALERT2 HELPERS — OPTIMA Standard
        // Menggantikan confirm() browser native di semua modul
        // ============================================================
        
        /**
         * Konfirmasi hapus / aksi berbahaya dengan SweetAlert2
         * @param {Object} opts - { title, text, confirmText, type }
         * @returns {Promise<boolean>} true jika dikonfirmasi
         */
        window.confirmSwal = function(opts) {
            opts = opts || {};
            var lang = window.lang || function(key) { return key; };
            if (typeof Swal === 'undefined') {
                // Fallback ke browser confirm jika SweetAlert belum load
                return Promise.resolve(confirm(opts.text || lang('are_you_sure')));
            }
            return Swal.fire({
                title: opts.title || lang('confirm'),
                text: opts.text || lang('are_you_sure'),
                icon: opts.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: opts.type === 'delete' ? '#dc3545' : (opts.type === 'success' ? '#198754' : '#0d6efd'),
                cancelButtonColor: '#6c757d',
                confirmButtonText: opts.confirmText || (opts.type === 'delete' ? '<i class="fas fa-trash me-1"></i>' + lang('confirm_delete_btn') : '<i class="fas fa-check me-1"></i>' + lang('confirm_continue_btn')),
                cancelButtonText: '<i class="fas fa-times me-1"></i>' + lang('cancel'),
                reverseButtons: true,
            }).then(function(result) {
                return result.isConfirmed;
            });
        };
        
        /**
         * Alert informatif dengan SweetAlert2
         * @param {string} type - 'success', 'error', 'warning', 'info'
         * @param {string} message
         * @param {string} title
         */
        window.alertSwal = function(type, message, title) {
            title = title || '';
            if (typeof Swal === 'undefined') return;
            var lang = window.lang || function(key) { return key; };
            Swal.fire({
                icon: type === 'danger' ? 'error' : type,
                title: title || (type === 'success' ? lang('success') : type === 'error' || type === 'danger' ? lang('error') : lang('warning')),
                text: message,
                confirmButtonText: lang('ok'),
                confirmButtonColor: '#0d6efd',
            });
        };
        
        // Backward compatibility & Helper functions
        window.showToast = window.createOptimaToast; // Alias untuk kompatibilitas
        window.OptimaPro = window.OptimaPro || {};
        window.OptimaPro.showNotification = function(msg, type) {
            type = type || 'info';
            var toastType = type === 'error' ? 'error' : type;
            return createOptimaToast({type: toastType, title: type.toUpperCase(), message: msg});
        };
        
        // Track page start time BEFORE load event
        window.pageStartTime = performance.now();
        
        // Define BASE_URL globally for all JavaScript files
        var BASE_URL = <?= json_encode(rtrim(base_url(), '/') . '/') ?>;
        window.BASE_URL = BASE_URL;

        // Global auth/session error handler module
        window.OptimaAuth = {
            _sessionExpiredShown: false,
            handleSessionExpired: function(response) {
                if (this._sessionExpiredShown) return;
                this._sessionExpiredShown = true;
                var loginUrl = <?= json_encode(base_url('auth/login')) ?>;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sesi Berakhir',
                        text: (response && response.message) ? response.message : 'Sesi Anda telah berakhir. Silakan login kembali.',
                        confirmButtonText: 'Login Kembali',
                        allowOutsideClick: false,
                    }).then(function() {
                        window.location.href = loginUrl;
                    });
                } else {
                    alert('Sesi Anda telah berakhir. Silakan login kembali.');
                    window.location.href = loginUrl;
                }
            },
            handleAccessDenied: function(response) {
                var msg = (response && response.message) ? response.message : 'Anda tidak memiliki akses ke fitur ini.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: msg });
                } else {
                    alert(msg);
                }
            },
            handleHttpError: function(xhr) {
                if (xhr.status === 401) {
                    this.handleSessionExpired(xhr.responseJSON || {});
                    return true;
                }
                if (xhr.status === 403) {
                    var r = xhr.responseJSON || {};
                    if (r.code === 'ACCESS_DENIED') {
                        this.handleAccessDenied(r);
                        return true;
                    }
                }
                return false;
            }
        };
    </script>
    <!-- Page Loading -->
    <div class="loading-overlay" id="pageLoading">
        <div class="loading-content">
            <div class="loading-logo">
                <img src="<?= base_url('assets/images/logo-optima.png') ?>" alt="OPTIMA">
            </div>
            <div class="loading-text">OPTIMA</div>
            <div class="loading-subtitle">PT Sarana Mitra Luas Tbk</div>
        </div>
    </div>

    <!-- Global Header (Full Width) -->
    <header class="global-header" id="globalHeader">
        <div class="header-container">
            <!-- Left Section: Logo + Sidebar Toggle -->
            <div class="header-left">
                <button class="btn btn-link sidebar-toggle sidebar-menu-button" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?= base_url('/welcome') ?>" class="header-brand d-flex align-items-center text-decoration-none gap-2">
                    <!-- Cukup logo & nama perusahaan (logo OPTIMA dipindah ke sidebar) -->
                    <img src="<?= base_url('assets/images/company-logo.png') ?>" alt="SML" style="height: 24px; width: auto;">
                    <span class="fw-bold text-dark" style="font-size: 0.8rem; letter-spacing: 0.5px; white-space: nowrap;">
                        PT SARANA MITRA LUAS Tbk
                    </span>
                </a>
            </div>
            
            <!-- Right Section: Controls -->
            <div class="header-right">

                <!-- Ctrl+K Search Trigger (Command Palette) -->
                <button type="button" id="header-search-btn"
                    onclick="window.openCommandPalette ? window.openCommandPalette() : null"
                    aria-label="Buka pencarian cepat (Ctrl+K)"
                    title="Pencarian Cepat — Ctrl+K">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <span class="header-search-label">Cari...</span>
                    <span class="header-search-kbd"><kbd>Ctrl</kbd><kbd>K</kbd></span>
                </button>

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
                        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                    </button>
                    <ul id="notificationDropdownMenu" class="dropdown-menu dropdown-menu-end notification-dropdown" style="min-width: 380px !important; max-width: 380px !important; width: 380px !important; overflow-x: hidden !important;">
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
    <?= $this->include('layouts/sidebar_optima') ?>
    
    <!-- Main Content -->
    <main class="main-content cn-main-content" id="mainContent">
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
    <footer class="admin-footer cn-layout-footer">
        
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

    <!-- OptimaConfirm Modal (Bootstrap 5) -->
    <div class="modal fade" id="optimaConfirmModal" tabindex="-1" aria-labelledby="optimaConfirmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center" id="optimaConfirmModalLabel">
                        <span class="optima-confirm-icon me-2"></span>
                        <span class="optima-confirm-title">Konfirmasi</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 optima-confirm-text">Apakah Anda yakin?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn optima-confirm-btn" id="optimaConfirmBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>

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
    
    <!-- OPTIMA Language Helper for Multilingual Support (deferred) -->
    <script defer src="<?= base_url('assets/js/language-helper.js') ?>?v=<?= time() ?>"></script>
    <script>
    // Initialize current language from session
    if (typeof LanguageHelper !== 'undefined') {
        LanguageHelper.setLanguage(<?= json_encode(service('request')->getLocale()) ?>);
    }
    </script>
    <!-- Vendor: DataTables (loaded deferred for better performance) -->
    <script defer src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script defer src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script defer src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script defer src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- Flatpickr -->
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
    
    <!-- Moment.js (required for daterangepicker) - deferred for performance -->
    <script defer src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <!-- Date Range Picker JS - deferred -->
    <script defer src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Global Date Range Picker Script - deferred -->
    <script defer src="<?= base_url('assets/js/global-daterange.js') ?>?v=<?= time() ?>"></script>
    
    <!-- DataTable Date Filter Mixin (deferred) -->
    <script defer src="<?= base_url('assets/js/datatable-datefilter-mixin.js') ?>?v=<?= time() ?>"></script>

    <!-- Page Date Filter Helper (deferred) -->
    <script defer src="<?= base_url('assets/js/page-date-filter-helper.js') ?>?v=<?= time() ?>"></script>
    
    <!-- Pickr -->
    <script defer src="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.2/dist/pickr.min.js"></script>
    <!-- noUiSlider -->
    <script defer src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
    
    <!-- SweetAlert2 (deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>

    <!-- Select2 (deferred) -->
    <script defer src="<?= base_url('assets/js/select2.min.js') ?>"></script>
    
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
    
    <!-- Notification Sound Generator (deferred) -->
    <script defer src="<?= base_url('assets/js/notification-sound-generator.js') ?>?v=<?= time() ?>"></script>

    <!-- OPTIMA SPA Main System (deferred) -->
    <script defer src="<?= base_url('assets/js/optima-spa-main.js') ?>?v=<?= time() ?>"></script>

    <!-- Global Permission System (deferred) -->
    <script defer src="<?= base_url('assets/js/global-permission.js') ?>?v=<?= time() ?>"></script>

    <!-- UI Helpers (deferred) -->
    <script defer src="<?= base_url('assets/js/ui_helpers.js') ?>?v=<?= time() ?>"></script>

    <!-- OPTIMA Sidebar CodingNepal (deferred) -->
    <script defer src="<?= base_url('assets/js/sidebar-codingnepal.js') ?>?v=<?= time() ?>"></script>
    
    <!-- Global JavaScript Variables -->
    <script>
        // Set global variables
        window.baseUrl = <?= json_encode(base_url()) ?>;
        window.csrfToken = <?= json_encode(csrf_hash()) ?>;
        window.csrfTokenName = <?= json_encode(csrf_token()) ?>;
        window.currentUser = {
            id: <?= json_encode((string)(session()->get('user_id') ?? '')) ?>,
            name: <?= json_encode(trim((session()->get('first_name') ?? '') . ' ' . (session()->get('last_name') ?? ''))) ?>,
            email: <?= json_encode((string)(session()->get('email') ?? '')) ?>,
            role: <?= json_encode((string)(session()->get('role') ?? '')) ?>,
            avatar: <?= json_encode((string)(session()->get('avatar') ?: base_url('assets/images/default-avatar.svg'))) ?>
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
            if (!loading) return;
            
            // Minimum delay to ensure animation is visible and professional (1.5 seconds)
            const minLoadTime = 300; // Reduced from 1500ms for better UX
            const elapsed = performance.now() - (window.pageStartTime || 0);
            const remainingTime = Math.max(0, minLoadTime - elapsed);
            
            setTimeout(() => {
                loading.classList.add('fade-out');
                setTimeout(() => {
                    loading.style.display = 'none';
                    loading.remove(); // Clean up DOM
                }, 400); // Smooth fade-out transition
            }, remainingTime);
        });
        
        // Global DataTables Processing Safety Monitor
        // Prevents stuck loading indicators across all DataTables
        (function() {
            let processingStartTimes = new Map();
            const MAX_PROCESSING_TIME = 35000; // 35 seconds max
            
            // Monitor visible processing indicators every 5 seconds
            setInterval(function() {
                $('.dataTables_processing:visible').each(function() {
                    const $processing = $(this);
                    const id = $processing.closest('.dataTables_wrapper').attr('id') || 'unknown';
                    
                    // Track when processing started
                    if (!processingStartTimes.has(id)) {
                        processingStartTimes.set(id, Date.now());
                    } else {
                        const elapsed = Date.now() - processingStartTimes.get(id);
                        
                        // Force hide if stuck for too long
                        if (elapsed > MAX_PROCESSING_TIME) {
                            console.warn('⚠️ Force hiding stuck DataTables processing:', id, 'elapsed:', elapsed + 'ms');
                            $processing.hide();
                            processingStartTimes.delete(id);
                            
                            if (typeof showNotification === 'function') {
                                showNotification('Loading terlalu lama dan dihentikan. Silakan refresh halaman.', 'warning');
                            }
                        }
                    }
                });
                
                // Clean up hidden processing indicators from tracking
                $('.dataTables_processing:hidden').each(function() {
                    const id = $(this).closest('.dataTables_wrapper').attr('id');
                    if (id && processingStartTimes.has(id)) {
                        processingStartTimes.delete(id);
                    }
                });
            }, 5000); // Check every 5 seconds
        })();
        
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
        
        // Enhanced OptimaPro notification system (unified) - ES5-compatible
        window.OptimaPro = window.OptimaPro || {};
        window.OptimaPro.showNotification = function(message, type, duration) {
            type = type || 'info';
            duration = duration || 5000;
            if (typeof window.createOptimaToast !== 'function') { console.warn('Toast system not ready'); return; }
            var t = (type === 'danger') ? 'error' : (type || 'info');
            var lang = window.lang || function(key) { return key; };
            var title = (t === 'error') ? lang('error') : (t === 'success') ? lang('success') : (t === 'warning') ? lang('warning') : lang('info');
            return window.createOptimaToast({ type: t, title: title, message: message, duration: duration });
        };
        
        // Global AJAX setup — dynamic CSRF token refreshed per request
        // Using $(function(){}) ensures this runs AFTER jQuery is fully ready
        // and handles the case where CDN jQuery is replaced by local fallback
        (function applyAjaxCsrfSetup() {
            function setup() {
                $.ajaxSetup({
                    beforeSend: function(xhr, settings) {
                        // Always read the latest CSRF token before sending
                        // getCsrfToken() reads from session (never stale)
                        const token = (typeof window.getCsrfToken === 'function')
                            ? (window.getCsrfToken() || window.csrfToken || '')
                            : (window.csrfToken || (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || '');
                        if (token) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    },
                    error: function(xhr, status, error) {
                        // Global AJAX error handler
                        // Only auto-handle 401/403 auth errors (don't interfere with module-specific handlers)
                        
                        // 401 Unauthorized - Session Expired
                        if (xhr.status === 401) {
                            const response = xhr.responseJSON || {};
                            console.warn('🔐 Session Expired (code:', response.code, ')');
                            if (window.OptimaAuth) {
                                window.OptimaAuth.handleSessionExpired(response);
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sesi Berakhir',
                                    text: 'Sesi Anda telah berakhir. Silakan login kembali.',
                                    confirmButtonText: 'Login',
                                    allowOutsideClick: false,
                                }).then(() => {
                                    window.location.href = <?= json_encode(base_url('auth/login')) ?>;
                                });
                            } else {
                                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                                window.location.href = <?= json_encode(base_url('auth/login')) ?>;
                            }
                            return false;
                        }
                        
                        // 403 Forbidden - Access Denied or CSRF Token Expired
                        if (xhr.status === 403) {
                            const response = xhr.responseJSON || {};
                            const message = response.message || '';
                            
                            // ACCESS_DENIED from PermissionFilter
                            if (response.code === 'ACCESS_DENIED') {
                                console.warn('🔐 Access Denied');
                                if (window.OptimaAuth) {
                                    window.OptimaAuth.handleAccessDenied(response);
                                } else if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Akses Ditolak',
                                        text: message || 'Anda tidak memiliki akses ke fitur ini.',
                                    });
                                } else {
                                    alert(message || 'Anda tidak memiliki akses ke fitur ini.');
                                }
                                return false;
                            }
                            
                            // Detect CSRF token mismatch/expiry
                            if (message.includes('not allowed') || 
                                message.includes('CSRF') || 
                                response.type === 'CodeIgniter\\Security\\Exceptions\\SecurityException') {
                                
                                console.warn('🔐 CSRF Token Expired - Session timeout detected');
                                
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Sesi Berakhir',
                                        text: 'Token keamanan telah kedaluwarsa. Halaman perlu di-refresh.',
                                        confirmButtonText: 'Refresh Sekarang',
                                        allowOutsideClick: false,
                                    }).then(() => { window.location.reload(); });
                                } else {
                                    if (confirm('Sesi Anda telah berakhir. Refresh halaman sekarang?')) {
                                        window.location.reload();
                                    }
                                }
                                
                                return false;
                            }
                        }
                        
                        // For other errors, continue to module-specific handlers
                        // (Don't prevent default behavior)
                    }
                });
            }
            // Try immediately (if jQuery already loaded)
            if (typeof $ !== 'undefined') {
                setup();
                $(function() { setup(); }); // Re-apply on DOM ready as safety net
            } else {
                // Fallback: wait for jQuery via polling (CDN delay edge case)
                const jqWait = setInterval(function() {
                    if (typeof $ !== 'undefined') {
                        clearInterval(jqWait);
                        setup();
                    }
                }, 50);
            }
        })();

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
    // Unified confirmation helper (OptimaConfirm) - Bootstrap 5 Modal
    (function() {
        const lang = function(k) { return (typeof window.lang === 'function' ? window.lang(k) : k); };
        const iconMap = { warning: 'bi-exclamation-triangle-fill text-danger', question: 'bi-question-circle-fill text-primary', info: 'bi-info-circle-fill text-info', success: 'bi-check-circle-fill text-success' };

        function showBootstrapConfirm(opts, onConfirm) {
            const el = document.getElementById('optimaConfirmModal');
            if (!el || typeof bootstrap === 'undefined') {
                const plain = (opts.messageHtml || opts.html || opts.text || opts.title || '').toString().replace(/<[^>]+>/g, '') || lang('are_you_sure');
                if (window.confirm(plain)) { try { if (typeof onConfirm === 'function') onConfirm(); } catch (e) { console.error('OptimaConfirm callback error', e); } }
                return;
            }
            const titleEl = el.querySelector('.optima-confirm-title');
            const iconEl = el.querySelector('.optima-confirm-icon');
            const textEl = el.querySelector('.optima-confirm-text');
            const btnEl = document.getElementById('optimaConfirmBtn');

            const title = opts.title || lang('confirm');
            const html = (opts.messageHtml || opts.html || opts.text || '').toString();
            const confirmText = opts.confirmButtonText || opts.confirmText || lang('yes');
            const cancelText = opts.cancelButtonText || opts.cancelText || lang('cancel');
            const iconClass = iconMap[opts.icon || 'question'] || iconMap.question;
            const btnVariant = opts.confirmButtonColor || opts.confirmColor || 'primary';
            const btnClassMap = {
                '#dc3545': 'btn-danger', '#d33': 'btn-danger', '#c82333': 'btn-danger', 'danger': 'btn-danger',
                '#198754': 'btn-success', '#28a745': 'btn-success', '#3a9b68': 'btn-success', 'success': 'btn-success',
                '#0d6efd': 'btn-primary', '#3085d6': 'btn-primary', '#007bff': 'btn-primary', 'primary': 'btn-primary',
                '#ffc107': 'btn-warning', 'warning': 'btn-warning',
                '#6c757d': 'btn-secondary', 'secondary': 'btn-secondary',
                '#0dcaf0': 'btn-info', 'info': 'btn-info'
            };
            const btnClass = btnClassMap[btnVariant] || (btnVariant.startsWith('#') ? 'btn-primary' : 'btn-' + btnVariant) || 'btn-primary';

            titleEl.textContent = title;
            iconEl.className = 'optima-confirm-icon me-2';
            iconEl.innerHTML = '<i class="bi ' + iconClass + '"></i>';
            textEl.innerHTML = html || '';
            btnEl.innerHTML = confirmText;
            btnEl.className = 'btn ' + btnClass;
            el.querySelector('.modal-footer .btn-secondary').textContent = cancelText;

            let modalInstance = bootstrap.Modal.getInstance(el);
            if (!modalInstance) modalInstance = new bootstrap.Modal(el);

            function cleanup() {
                btnEl.removeEventListener('click', onBtnClick);
                el.removeEventListener('hidden.bs.modal', onHidden);
            }
            function onBtnClick() {
                modalInstance.hide();
                try { if (typeof onConfirm === 'function') onConfirm(); } catch (e) { console.error('OptimaConfirm callback error', e); }
                cleanup();
            }
            function onHidden() { cleanup(); }

            btnEl.addEventListener('click', onBtnClick);
            el.addEventListener('hidden.bs.modal', onHidden);
            modalInstance.show();
        }

        window.OptimaConfirm = window.OptimaConfirm || {
            danger: function(opts) {
                const o = opts || {};
                showBootstrapConfirm({
                    title: o.title || lang('delete') + '?',
                    messageHtml: o.messageHtml || o.html || o.text,
                    icon: o.icon || 'warning',
                    confirmText: o.confirmText || lang('yes') + ', ' + lang('delete') + '!',
                    cancelText: o.cancelText || lang('cancel'),
                    confirmButtonColor: '#dc3545'
                }, o.onConfirm);
            },
            approve: function(opts) {
                const o = opts || {};
                showBootstrapConfirm({
                    title: o.title || lang('approve') + '?',
                    messageHtml: o.messageHtml || o.html || o.text,
                    icon: o.icon || 'question',
                    confirmText: o.confirmText || lang('yes') + ', ' + lang('approve') + '!',
                    cancelText: o.cancelText || lang('cancel'),
                    confirmButtonColor: '#198754'
                }, o.onConfirm);
            },
            submit: function(opts) {
                const o = opts || {};
                showBootstrapConfirm({
                    title: o.title || lang('send') + '?',
                    messageHtml: o.messageHtml || o.html || o.text,
                    icon: o.icon || 'question',
                    confirmText: o.confirmText || lang('yes') + ', ' + lang('send') + '!',
                    cancelText: o.cancelText || lang('cancel'),
                    confirmButtonColor: '#0d6efd'
                }, o.onConfirm);
            },
            generic: function(opts) {
                const o = opts || {};
                showBootstrapConfirm({
                    title: o.title || lang('confirm'),
                    messageHtml: o.messageHtml || o.html || o.text,
                    icon: o.icon || 'question',
                    confirmText: o.confirmText || o.confirmButtonText || lang('yes'),
                    cancelText: o.cancelText || o.cancelButtonText || lang('cancel'),
                    confirmButtonColor: o.confirmButtonColor || o.confirmColor || '#0d6efd'
                }, o.onConfirm);
            },
            show: function(opts) {
                return window.OptimaConfirm.generic(opts);
            }
        };
    })();

    // Unified notification wrapper (ES5-compatible)
    window.OptimaNotify = window.OptimaNotify || {
        success: function(m, t) {
            var title = t || (typeof window.lang === 'function' ? window.lang('success') : 'Sukses');
            return window.createOptimaToast && createOptimaToast({type:'success', title: title, message: m});
        },
        error: function(m, t) {
            var title = t || (typeof window.lang === 'function' ? window.lang('error') : 'Error');
            return window.createOptimaToast && createOptimaToast({type:'error', title: title, message: m});
        },
        warning: function(m, t) {
            var title = t || (typeof window.lang === 'function' ? window.lang('warning') : 'Peringatan');
            return window.createOptimaToast && createOptimaToast({type:'warning', title: title, message: m});
        },
        info: function(m, t) {
            var title = t || (typeof window.lang === 'function' ? window.lang('info') : 'Info');
            return window.createOptimaToast && createOptimaToast({type:'info', title: title, message: m});
        }
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
                                const lang = window.lang || ((key) => key);
                                createOptimaToast({type:icon||'info', title:a.title||icon.toUpperCase()||lang('info'), message:a.text||a.html||'', duration:a.timer||4000});
                                return Promise.resolve({isConfirmed:true,isToast:true});
                            }
                        }
                    }
                    // Confirmation dialog (showCancelButton:true) → OptimaConfirm Bootstrap modal
                    if (typeof a === 'object' && a && a.showCancelButton && window.OptimaConfirm) {
                        return new Promise(function(resolve) {
                            var resolved = false;
                            function done(isConfirmed) {
                                if (resolved) return;
                                resolved = true;
                                resolve({ isConfirmed: isConfirmed, isDismissed: !isConfirmed, dismiss: isConfirmed ? undefined : 'cancel', value: isConfirmed ? true : undefined });
                            }
                            var icon = (a.icon || 'question').toLowerCase();
                            if (!['warning','question','info','success','error'].includes(icon)) icon = 'question';
                            var rawColor = (a.confirmButtonColor || '').toLowerCase();
                            var confirmColor = 'primary';
                            if (['#dc3545','#d33','#c82333','danger'].includes(rawColor)) confirmColor = 'danger';
                            else if (['#198754','#28a745','#3a9b68','success'].includes(rawColor)) confirmColor = 'success';
                            else if (['#ffc107','warning'].includes(rawColor)) confirmColor = 'warning';
                            else if (['#6c757d','secondary'].includes(rawColor)) confirmColor = 'secondary';
                            var el = document.getElementById('optimaConfirmModal');
                            function onHide() {
                                if (el) el.removeEventListener('hidden.bs.modal', onHide);
                                done(false);
                            }
                            window.OptimaConfirm.generic({
                                title: a.title || 'Konfirmasi',
                                html: a.html || a.text || '',
                                icon: icon,
                                confirmText: a.confirmButtonText || 'Ya',
                                cancelText: a.cancelButtonText || 'Batal',
                                confirmButtonColor: confirmColor,
                                onConfirm: function() {
                                    if (el) el.removeEventListener('hidden.bs.modal', onHide);
                                    done(true);
                                }
                            });
                            if (el) el.addEventListener('hidden.bs.modal', onHide);
                        });
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
    <script defer src="<?= base_url('assets/js/notification-lightweight.js') ?>"></script>

    <!-- Metis Dashboard Style Sidebar Toggle (disabled for cn-sidebar-layout / CodingNepal sidebar) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Untuk layout baru (cn-sidebar-layout) kita pakai sidebar-codingnepal.js, 
            // jadi script Metis ini tidak dijalankan agar tidak bentrok.
            if (document.body.classList.contains('cn-sidebar-layout')) {
                return;
            }

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
                        // ALIGNMENT FIX: Use itemRect.top directly so it aligns with the icon 
                        // and stays below the 60px header.
                        // Clamp to minimum 60px to ensuring "menampilkannya kebawah" (below header)
                        let topPosition = Math.max(itemRect.top, 60);
                        
                        // Adjust if dropdown would overflow bottom
                        if (topPosition + dropdownHeight > viewportHeight - 10) {
                            topPosition = viewportHeight - dropdownHeight - 10;
                        }
                        
                        // Ensure minimum top position (redundant but safe)
                        if (topPosition < 60) {
                            topPosition = 60;
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
    
    <!-- OPTIMA DataTable Configuration - Centralized Table Behavior (deferred) -->
    <script defer src="<?= base_url('assets/js/optima-datatable-config.js') ?>?v=<?= time() ?>"></script>

    <!-- ============================================================
         OPTIMA COMMAND PALETTE (Ctrl+K / ⌘K)
         Global search overlay yang tersedia di semua halaman.
         Mengindex semua link navigasi sidebar secara otomatis.
    ============================================================ -->
    <!-- Command Palette HTML -->
    <div id="optima-command-palette" role="dialog" aria-modal="true" aria-label="Command Palette" style="display:none;">
        <div id="optima-cp-backdrop"></div>
        <div id="optima-cp-dialog">
            <div id="optima-cp-header">
                <i class="fas fa-search" id="optima-cp-icon" aria-hidden="true"></i>
                <input type="text" id="optima-cp-input" placeholder="Cari menu, halaman, fitur..." autocomplete="off" spellcheck="false" autofocus>
                <kbd id="optima-cp-esc-hint" title="Tekan Esc untuk menutup">Esc</kbd>
            </div>
            <div id="optima-cp-results" role="listbox"></div>
            <div id="optima-cp-footer">
                <span><kbd>↑↓</kbd> Navigasi</span>
                <span><kbd>Enter</kbd> Buka</span>
                <span><kbd>Esc</kbd> Tutup</span>
                <span class="ms-auto opacity-50 small">OPTIMA Search</span>
            </div>
        </div>
    </div>

    <style>
    /* === Command Palette === */
    #optima-command-palette {
        position: fixed; inset: 0; z-index: 9999;
    }
    #optima-cp-backdrop {
        position: absolute; inset: 0;
        background: rgba(0,0,0,.5);
        backdrop-filter: blur(4px);
        animation: cpFadeIn .15s ease;
    }
    #optima-cp-dialog {
        position: absolute;
        top: 12%; left: 50%;
        transform: translateX(-50%);
        width: min(640px, 94vw);
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 24px 64px rgba(0,0,0,.22), 0 0 0 1px rgba(0,0,0,.08);
        overflow: hidden;
        animation: cpSlideIn .18s cubic-bezier(.22,1,.36,1);
    }
    html[data-bs-theme="dark"] #optima-cp-dialog {
        background: #1e2130;
        box-shadow: 0 24px 64px rgba(0,0,0,.5), 0 0 0 1px rgba(255,255,255,.07);
    }
    @keyframes cpFadeIn  { from { opacity:0 } to { opacity:1 } }
    @keyframes cpSlideIn { from { opacity:0; transform:translateX(-50%) translateY(-12px) } to { opacity:1; transform:translateX(-50%) translateY(0) } }

    #optima-cp-header {
        display: flex; align-items: center; gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(0,0,0,.08);
    }
    html[data-bs-theme="dark"] #optima-cp-header { border-color: rgba(255,255,255,.08); }
    #optima-cp-icon { color: #6c757d; font-size: 1rem; flex-shrink: 0; }
    #optima-cp-input {
        flex: 1; border: none; outline: none; font-size: 1rem;
        background: transparent; color: inherit;
    }
    #optima-cp-esc-hint {
        background: #f1f3f5; color: #6c757d;
        border: 1px solid #dee2e6; border-radius: 6px;
        padding: 2px 8px; font-size: .75rem; font-family: inherit;
        cursor: pointer;
    }
    html[data-bs-theme="dark"] #optima-cp-esc-hint { background:#2a2d3e; border-color:#3a3d50; color:#9ba1b0; }

    #optima-cp-results {
        max-height: 380px;
        overflow-y: auto;
        padding: 8px 0;
    }
    #optima-cp-results:empty::before {
        content: 'Ketik untuk mencari...';
        display: block; text-align: center;
        padding: 32px; color: #9ba1b0; font-size: .9rem;
    }
    .cp-group-label {
        padding: 8px 20px 4px;
        font-size: .7rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: #9ba1b0;
    }
    .cp-item {
        display: flex; align-items: center; gap: 14px;
        padding: 10px 20px;
        cursor: pointer;
        transition: background .1s;
        border-radius: 0;
        text-decoration: none;
        color: inherit;
    }
    .cp-item:hover, .cp-item.active {
        background: rgba(0,97,242,.08);
        color: #0061f2;
    }
    html[data-bs-theme="dark"] .cp-item:hover, html[data-bs-theme="dark"] .cp-item.active {
        background: rgba(86,141,255,.12);
        color: #568dff;
    }
    .cp-item-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        background: rgba(0,97,242,.1);
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; color: #0061f2; flex-shrink: 0;
    }
    html[data-bs-theme="dark"] .cp-item-icon { background: rgba(86,141,255,.15); color:#568dff; }
    .cp-item-label { font-size: .9rem; font-weight: 500; line-height: 1.2; }
    .cp-item-group { font-size: .75rem; color: #9ba1b0; }
    .cp-no-results {
        text-align: center; padding: 32px;
        color: #9ba1b0; font-size: .9rem;
    }
    .cp-no-results i { font-size: 2rem; display: block; margin-bottom: 8px; opacity: .4; }

    #optima-cp-footer {
        display: flex; gap: 16px; align-items: center;
        padding: 10px 20px;
        border-top: 1px solid rgba(0,0,0,.08);
        font-size: .75rem; color: #9ba1b0;
    }
    html[data-bs-theme="dark"] #optima-cp-footer { border-color: rgba(255,255,255,.08); }
    #optima-cp-footer kbd {
        background: #f1f3f5; border: 1px solid #dee2e6;
        border-radius: 4px; padding: 1px 5px;
        font-size: .7rem; color: #6c757d;
    }
    html[data-bs-theme="dark"] #optima-cp-footer kbd { background:#2a2d3e; border-color:#3a3d50; color:#9ba1b0; }

    /* Ctrl+K button in topbar */
    #optima-cp-trigger {
        display: flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 8px;
        padding: 5px 14px; margin: 0 8px;
        cursor: pointer;
        color: rgba(255,255,255,.8);
        font-size: .82rem;
        transition: all .2s;
    }
    #optima-cp-trigger:hover { background: rgba(255,255,255,.2); color:#fff; }
    #optima-cp-trigger kbd {
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 4px; padding: 1px 5px;
        font-size: .7rem; font-family: inherit;
    }
    @media(max-width:576px) { #optima-cp-trigger span.cp-trigger-text { display:none; } }
    </style>

    <script>
    // =============================================================
    // OPTIMA COMMAND PALETTE — Global Search
    // =============================================================
    (function() {
        'use strict';

        const SHORTCUT_KEY = 'k';
        let isOpen = false;
        let activeIdx = -1;
        let filteredItems = [];
        let allItems = [];

        // --- Build index from sidebar nav links ---
        function buildIndex() {
            const items = [];
            // Collect from all rendered sidebar nav links
            document.querySelectorAll('.nav-dropdown-item, .sidebar-nav-link, .nav-link[href]').forEach(el => {
                const href = el.getAttribute('href') || '';
                if (!href || href === '#' || href.startsWith('javascript')) return;

                const icon = el.querySelector('i') ? el.querySelector('i').className : 'fas fa-circle';
                let text = el.textContent.trim().replace(/\s+/g, ' ');
                if (!text || text.length < 2) return;

                // Try to get group label from closest parent header
                let group = '';
                const header = el.closest('.nav-group-item, .sidebar-section');
                if (header) {
                    const lbl = header.querySelector('.nav-group-label, .nav-link-text, .sidebar-section-header');
                    if (lbl) group = lbl.textContent.trim();
                }

                items.push({ label: text, href, icon, group });
            });

            // Hardcoded quick actions (always available)
            const quickActions = [
                { label: 'Dashboard', href: '<?= base_url('/dashboard') ?>', icon: 'fas fa-tachometer-alt', group: 'Navigation' },
                { label: 'Work Orders', href: '<?= base_url('service/work-orders') ?>', icon: 'fas fa-clipboard-check', group: 'Service' },
                { label: 'Contracts — Kontrak', href: '<?= base_url('marketing/kontrak') ?>', icon: 'fas fa-file-contract', group: 'Marketing' },
                { label: 'Invoices', href: '<?= base_url('finance/invoices') ?>', icon: 'fas fa-file-invoice-dollar', group: 'Finance' },
                { label: 'Purchasing', href: '<?= base_url('/purchasing') ?>', icon: 'fas fa-shopping-cart', group: 'Purchasing' },
                { label: 'Inventory Unit', href: '<?= base_url('warehouse/inventory/units') ?>', icon: 'fas fa-boxes', group: 'Warehouse' },
                { label: 'Customer Management', href: '<?= base_url('marketing/customer-management') ?>', icon: 'fas fa-users', group: 'Marketing' },
                { label: 'Reports', href: '<?= base_url('/reports') ?>', icon: 'fas fa-chart-bar', group: 'Laporan' },
                { label: 'Settings — Pengaturan', href: '<?= base_url('admin/settings') ?>', icon: 'fas fa-cog', group: 'Admin' },
                { label: 'Quotations', href: '<?= base_url('marketing/quotations') ?>', icon: 'fas fa-file-invoice', group: 'Marketing' },
                { label: 'Sparepart Validation', href: '<?= base_url('service/sparepart-validation') ?>', icon: 'fas fa-toolbox', group: 'Service' },
                { label: 'Area & Employee Management', href: '<?= base_url('service/area-employee-management') ?>', icon: 'fas fa-users-cog', group: 'Service' },
                { label: 'Delivery Instructions', href: '<?= base_url('marketing/di') ?>', icon: 'fas fa-shipping-fast', group: 'Marketing' },
            ];

            // Merge: quickActions first, then sidebar-discovered (dedup by href)
            const seenHrefs = new Set();
            const merged = [];
            [...quickActions, ...items].forEach(item => {
                if (!seenHrefs.has(item.href)) {
                    seenHrefs.add(item.href);
                    merged.push(item);
                }
            });
            return merged;
        }

        // --- Render results ---
        function render(query) {
            const container = document.getElementById('optima-cp-results');
            if (!query.trim()) {
                // Show all quick actions as default
                filteredItems = allItems.slice(0, 8);
            } else {
                const q = query.toLowerCase();
                filteredItems = allItems.filter(item =>
                    item.label.toLowerCase().includes(q) ||
                    (item.group && item.group.toLowerCase().includes(q))
                ).slice(0, 12);
            }
            activeIdx = filteredItems.length > 0 ? 0 : -1;

            if (filteredItems.length === 0) {
                container.innerHTML = `<div class="cp-no-results"><i class="fas fa-search-minus"></i>Tidak ditemukan: "<strong>${escStr(query)}</strong>"</div>`;
                return;
            }

            // Group by category
            const groups = {};
            filteredItems.forEach((item, i) => {
                const g = item.group || 'Lainnya';
                if (!groups[g]) groups[g] = [];
                groups[g].push({ ...item, _idx: i });
            });

            let html = '';
            Object.entries(groups).forEach(([groupName, groupItems]) => {
                html += `<div class="cp-group-label">${escStr(groupName)}</div>`;
                groupItems.forEach(item => {
                    const isActive = item._idx === activeIdx ? ' active' : '';
                    html += `<a href="${item.href}" class="cp-item${isActive}" data-idx="${item._idx}" role="option" aria-selected="${item._idx === activeIdx}">
                        <div class="cp-item-icon"><i class="${item.icon}" aria-hidden="true"></i></div>
                        <div>
                            <div class="cp-item-label">${escStr(item.label)}</div>
                        </div>
                    </a>`;
                });
            });
            container.innerHTML = html;

            // Add click handlers
            container.querySelectorAll('.cp-item').forEach(el => {
                el.addEventListener('mouseenter', () => {
                    activeIdx = parseInt(el.dataset.idx);
                    updateActive();
                });
            });
        }

        function escStr(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function updateActive() {
            document.querySelectorAll('#optima-cp-results .cp-item').forEach(el => {
                const idx = parseInt(el.dataset.idx);
                el.classList.toggle('active', idx === activeIdx);
                el.setAttribute('aria-selected', idx === activeIdx);
            });
            // Scroll active into view
            const activeEl = document.querySelector('#optima-cp-results .cp-item.active');
            if (activeEl) activeEl.scrollIntoView({ block: 'nearest' });
        }

        // --- Open / Close ---
        function open() {
            if (isOpen) return;
            isOpen = true;
            allItems = buildIndex();
            const palette = document.getElementById('optima-command-palette');
            palette.style.display = 'block';
            const input = document.getElementById('optima-cp-input');
            input.value = '';
            render('');
            setTimeout(() => input.focus(), 50);
            document.body.style.overflow = 'hidden';
        }

        function close() {
            if (!isOpen) return;
            isOpen = false;
            document.getElementById('optima-command-palette').style.display = 'none';
            document.body.style.overflow = '';
        }

        function navigate(dir) {
            if (filteredItems.length === 0) return;
            activeIdx = (activeIdx + dir + filteredItems.length) % filteredItems.length;
            updateActive();
        }

        function activate() {
            if (activeIdx < 0 || !filteredItems[activeIdx]) return;
            window.location.href = filteredItems[activeIdx].href;
        }

        // --- Event listeners ---
        document.addEventListener('DOMContentLoaded', function() {
            const palette = document.getElementById('optima-command-palette');
            const backdrop = document.getElementById('optima-cp-backdrop');
            const input = document.getElementById('optima-cp-input');

            // Close on backdrop click
            backdrop.addEventListener('click', close);

            // Input
            input.addEventListener('input', () => render(input.value));
            input.addEventListener('keydown', e => {
                if (e.key === 'ArrowDown') { e.preventDefault(); navigate(1); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); navigate(-1); }
                else if (e.key === 'Enter') { e.preventDefault(); activate(); close(); }
                else if (e.key === 'Escape') { close(); }
            });

            // ESC hint click
            document.getElementById('optima-cp-esc-hint').addEventListener('click', close);

            // Add Ctrl+K trigger button to navbar if it exists
            const navbar = document.querySelector('.navbar .navbar-nav, .topbar, .navbar-collapse, .top-navigation');
            if (navbar) {
                const triggerBtn = document.createElement('button');
                triggerBtn.id = 'optima-cp-trigger';
                triggerBtn.type = 'button';
                triggerBtn.setAttribute('aria-label', 'Buka pencarian (Ctrl+K)');
                triggerBtn.innerHTML = '<i class="fas fa-search" aria-hidden="true"></i><span class="cp-trigger-text">Cari...</span><kbd>Ctrl K</kbd>';
                triggerBtn.addEventListener('click', open);
                navbar.prepend(triggerBtn);
            }
        });

        // Global keyboard shortcut Ctrl+K / Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === SHORTCUT_KEY) {
                e.preventDefault();
                isOpen ? close() : open();
            }
        });

        // Expose globally
        window.openCommandPalette = open;
        window.closeCommandPalette = close;
    })();
    </script>

    <!-- Phase 6.4: Export with Loading Feedback — Global helper -->
    <script>
    /**
     * exportWithLoading(url, label)
     * Shows SweetAlert2 loading state while exporting a file.
     * Usage: exportWithLoading('<?= base_url('reports/export') ?>', 'Export Data')
     */
    window.exportWithLoading = async function(url, label = 'Mengekspor data') {
        Swal.fire({
            title: label + '...',
            html: '<div class="my-2 text-muted small">Sedang memproses, harap tunggu.</div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Server error: ' + response.status);

            const blob = await response.blob();
            const disposition = response.headers.get('Content-Disposition') || '';
            let filename = label.replace(/\s+/g, '_') + '.xlsx';
            const match = disposition.match(/filename[^;=\n]*=(['"]?)([^'";\n]+)\1/);
            if (match) filename = match[2];

            // Trigger download
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            a.click();
            URL.revokeObjectURL(a.href);

            Swal.fire({ icon: 'success', title: 'Berhasil!', text: `${filename} berhasil diunduh.`, timer: 2500, showConfirmButton: false });
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Gagal Export', text: err.message || 'Terjadi kesalahan saat mengekspor data.' });
        }
    };
    </script>

</body>
</html>
