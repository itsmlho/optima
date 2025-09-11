/**
 * OPTIMA SPA Navigation System
 * Handles page loading without full page reload - keeps sidebar state
 */

// CRITICAL: Prevent script duplication at global level
if (window.OPTIMA_SPA_INITIALIZED) {
    console.log('⏭️ SPA Navigation already initialized, skipping duplicate initialization');
    // Exit early to prevent duplicate initialization
    throw new Error('SPA Navigation already initialized');
}

// Mark as initialized
window.OPTIMA_SPA_INITIALIZED = true;

// Initialize global script tracker (IMMEDIATELY)
if (!window.OPTIMA_SCRIPT_TRACKER) {
    window.OPTIMA_SCRIPT_TRACKER = {
        loadedExternal: new Set(),
        executedInline: new Set(),
        scriptElements: new Map(),
        blockedScripts: new Set()
    };
}

// NUCLEAR OPTION: Pre-block problematic scripts IMMEDIATELY
const PROBLEMATIC_SCRIPTS = [
    'sidebar-advanced.js',
    'spa-navigation.js',
    'optima-pro.js',
    'sidebar-enhanced.js'
];

PROBLEMATIC_SCRIPTS.forEach(pattern => {
    // Find and pre-block any existing scripts
    const existingScripts = document.querySelectorAll(`script[src*="${pattern}"]`);
    existingScripts.forEach(script => {
        window.OPTIMA_SCRIPT_TRACKER.blockedScripts.add(script.src);
        console.log('🚫 PRE-BLOCKING existing problematic script:', script.src);
    });
});

class OptimaSPANavigation {
    constructor() {
        this.isLoading = false;
        this.currentPath = window.location.pathname;
        this.loadingTimeout = null;
        
        this.init();
    }
    
    init() {
        this.bindNavigationEvents();
        this.setupHistoryAPI();
        this.showLoadingIndicator = this.debounce(this.showLoadingIndicator.bind(this), 100);
        
        console.log('OPTIMA SPA Navigation initialized');
    }
    
    bindNavigationEvents() {
        console.log('🚀 Binding SPA navigation events...');
        
        // AGGRESSIVE: Handle ALL link clicks first in capture phase
        document.addEventListener('click', (e) => {
            // Find the actual link element
            const link = e.target.closest('a[href]');
            if (!link) return;
            
            // Check if it's a navigation link we should handle
            const isNavLink = link.classList.contains('nav-link') || 
                             link.closest('.nav-item') || 
                             link.closest('.breadcrumb-item');
            
            if (isNavLink && this.isInternalLink(link.href)) {
                // Hide all tooltips immediately before navigation
                this.hideAllTooltips();
                
                // Only log important navigation events
                // console.log('🔄 SPA Navigation:', link.href);
                
                // AGGRESSIVE EVENT BLOCKING
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Visual feedback
                link.style.pointerEvents = 'none';
                link.style.opacity = '0.7';
                
                // Start navigation
                this.navigateTo(link.href).then(() => {
                    // Re-enable link after navigation
                    link.style.pointerEvents = '';
                    link.style.opacity = '';
                }).catch(() => {
                    // Re-enable link on error
                    link.style.pointerEvents = '';
                    link.style.opacity = '';
                });
                
                return false;
            }
        }, true); // Capture phase - runs before any other handlers
        
        // Handle breadcrumb navigation
        document.addEventListener('click', (e) => {
            const breadcrumbLink = e.target.closest('.breadcrumb-item a[href]');
            
            if (breadcrumbLink && this.isInternalLink(breadcrumbLink.href)) {
                e.preventDefault();
                this.navigateTo(breadcrumbLink.href);
            }
        });
        
        // Handle form submissions that should use AJAX
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.classList.contains('spa-form') || form.hasAttribute('data-spa')) {
                e.preventDefault();
                this.handleFormSubmission(form);
            }
        });
    }
    
    setupHistoryAPI() {
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.path) {
                this.loadContent(e.state.path, false);
            } else {
                this.loadContent(window.location.pathname, false);
            }
        });
    }
    
    isInternalLink(href) {
        try {
            // Skip invalid hrefs
            if (!href || href === '#' || href === 'javascript:void(0)' || href.startsWith('javascript:')) {
                if (window.SPA_DEBUG) console.log('❌ Invalid href:', href);
                return false;
            }
            
            // Handle relative links
            if (href.startsWith('/') && !href.startsWith('//')) {
                // Exclude specific paths
                if (href.includes('logout') || href.includes('download') || href.includes('export')) {
                    if (window.SPA_DEBUG) console.log('❌ Excluded path:', href);
                    return false;
                }
                if (window.SPA_DEBUG) console.log('✅ Internal relative link:', href);
                return true;
            }
            
            const url = new URL(href, window.location.origin);
            const isInternal = url.origin === window.location.origin && 
                   !href.includes('#') && 
                   !href.includes('logout') &&
                   !href.includes('download') &&
                   !href.includes('export');
            
            if (window.SPA_DEBUG) console.log('🔍 Link check:', href, 'isInternal:', isInternal);
            return isInternal;
        } catch (e) {
            console.warn('❌ Link check failed:', href, e);
            return false;
        }
    }
    
    async navigateTo(url, pushState = true) {
        if (this.isLoading) {
            console.log('⏳ Navigation already in progress, skipping:', url);
            return;
        }
        
        // Add navigation class to body for CSS rules
        document.body.classList.add('spa-navigating');
        
        // console.log('🎯 Navigating to:', url);
        
        try {
            const urlObj = new URL(url, window.location.origin);
            const path = urlObj.pathname + urlObj.search;
            
            // Don't reload if it's the same page
            if (path === this.currentPath) {
                console.log('📍 Same page, skipping navigation:', path);
                document.body.classList.remove('spa-navigating');
                return;
            }
            
            // console.log('🚀 Starting SPA navigation from', this.currentPath, 'to', path);
            this.showLoadingIndicator();
            
            await this.loadContent(path, pushState);
            
        } catch (error) {
            console.error('❌ Navigation error:', error);
            this.hideLoadingIndicator();
            
            // Show error notification
            if (window.createOptimaToast) {
                createOptimaToast({
                    type: 'error',
                    title: 'Navigation Error',
                    message: 'SPA navigation failed, using fallback'
                });
            }
            
            // Fallback to normal navigation
            console.log('🔄 Falling back to normal navigation');
            window.location.href = url;
        } finally {
            // Remove navigation class
            document.body.classList.remove('spa-navigating');
        }
    }
    
    async loadContent(path, pushState = true) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        const startTime = performance.now();
        
        // Only show loading for slow requests
        this.showLoadingIndicator();
        
        try {
            const response = await fetch(path, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html, application/xhtml+xml',
                    'X-SPA-Request': 'true'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const html = await response.text();
            
            // Parse the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update content with smooth transition
            this.updatePageContent(doc);
            
            // Update browser history
            if (pushState) {
                const title = doc.title || 'OPTIMA';
                history.pushState({ path }, title, path);
                document.title = title;
            }
            
            this.currentPath = path;
            
            // Update active states immediately
            this.updateActiveStates(path);
            
            // CRITICAL: Force reload content area to trigger data loading
            this.forceContentReload(path);
            
            const loadTime = performance.now() - startTime;
            if (loadTime > 1000) {
                console.log(`⚡ SPA navigation completed (${Math.round(loadTime)}ms)`);
            }
            
        } catch (error) {
            console.error('❌ SPA navigation failed:', error);
            
            // Fallback to normal navigation immediately
            window.location.href = path;
        } finally {
            // Always complete loading quickly
            this.isLoading = false;
            this.hideLoadingIndicator();
        }
    }
    
    updatePageContent(doc) {
        // Update main content with instant transition
        const newContentBody = doc.querySelector('.content-body');
        const currentContentBody = document.querySelector('.content-body');
        
        if (newContentBody && currentContentBody) {
            // Instant content update for better performance
            currentContentBody.innerHTML = newContentBody.innerHTML;
            currentContentBody.style.opacity = '1';
        }
        
        // Update page title in header
        const newTitle = doc.querySelector('.content-header h1');
        const currentTitle = document.querySelector('.content-header h1');
        
        if (newTitle && currentTitle) {
            currentTitle.textContent = newTitle.textContent;
        }
        
        // Update breadcrumbs
        const newBreadcrumb = doc.querySelector('.content-header nav[aria-label="breadcrumb"]');
        const currentBreadcrumb = document.querySelector('.content-header nav[aria-label="breadcrumb"]');
        
        if (newBreadcrumb && currentBreadcrumb) {
            currentBreadcrumb.innerHTML = newBreadcrumb.innerHTML;
        } else if (newBreadcrumb) {
            // Add breadcrumb if it doesn't exist
            const headerDiv = document.querySelector('.content-header > div > div');
            if (headerDiv) {
                headerDiv.appendChild(newBreadcrumb);
            }
        } else if (currentBreadcrumb) {
            // Remove breadcrumb if new page doesn't have one
            currentBreadcrumb.remove();
        }
        
        // Execute any inline scripts in the new content
        this.executeInlineScripts(newContentBody);
    }
    
    executeInlineScripts(container) {
        if (!container) return;
        
        // Use the same robust script execution system
        this.extractAndExecuteScripts(container);
    }
    
    // NEW: Content-only reload (most efficient for data updates)
    async reloadContentOnly(url) {
        try {
            console.log('🔄 Reloading content only from:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    'Cache-Control': 'no-cache'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract only the data content (tables, cards, etc.)
            const newContentBody = doc.querySelector('.content-body');
            const currentContentBody = document.querySelector('.content-body');
            
            if (newContentBody && currentContentBody) {
                // Only update data containers, preserve scripts and event handlers
                const dataContainers = newContentBody.querySelectorAll('.data-container, .table-responsive, .card-body, .table');
                const currentDataContainers = currentContentBody.querySelectorAll('.data-container, .table-responsive, .card-body, .table');
                
                dataContainers.forEach((newContainer, index) => {
                    if (currentDataContainers[index]) {
                        currentDataContainers[index].innerHTML = newContainer.innerHTML;
                    }
                });
                
                console.log('✅ Content-only reload completed');
            }
            
        } catch (error) {
            console.error('❌ Error in content-only reload:', error);
        }
    }
    
    // NEW: Global function to trigger efficient data reload based on current page
    triggerEfficientDataReload() {
        const currentPath = window.location.pathname;
        console.log('🔄 Triggering efficient data reload for:', currentPath);
        
        if (currentPath.includes('/marketing/kontrak')) {
            if (typeof reloadKontrakDataOnly === 'function') {
                reloadKontrakDataOnly();
            } else if (typeof loadKontrakData === 'function') {
                loadKontrakData();
            }
        } else if (currentPath.includes('/marketing/di')) {
            if (typeof reloadDIDataOnly === 'function') {
                reloadDIDataOnly();
            } else if (typeof loadDIData === 'function') {
                loadDIData();
            }
        } else if (currentPath.includes('/operational/delivery')) {
            if (typeof reloadDeliveryDataOnly === 'function') {
                reloadDeliveryDataOnly();
            } else if (typeof loadDeliveryData === 'function') {
                loadDeliveryData();
            }
        }
        
        // Generic trigger for any page
        if (typeof window.initializePage === 'function') {
            window.initializePage();
        }
    }
    
    // NEW: Clean up script tracking (for debugging)
    clearScriptTracking() {
        if (window.OPTIMA_SCRIPT_TRACKER) {
            window.OPTIMA_SCRIPT_TRACKER.loadedExternal.clear();
            window.OPTIMA_SCRIPT_TRACKER.executedInline.clear();
            window.OPTIMA_SCRIPT_TRACKER.scriptElements.clear();
            window.OPTIMA_SCRIPT_TRACKER.blockedScripts.clear();
            console.log('🧹 Script tracking cleared');
        }
    }
    
    // NEW: Reset all script tracking (nuclear option)
    resetAllScriptTracking() {
        if (window.OPTIMA_SCRIPT_TRACKER) {
            window.OPTIMA_SCRIPT_TRACKER.loadedExternal.clear();
            window.OPTIMA_SCRIPT_TRACKER.executedInline.clear();
            window.OPTIMA_SCRIPT_TRACKER.scriptElements.clear();
            window.OPTIMA_SCRIPT_TRACKER.blockedScripts.clear();
        }
        
        // Reset initialization flags
        window.OPTIMA_SPA_INITIALIZED = false;
        window.OPTIMA_SIDEBAR_INITIALIZED = false;
        window.OPTIMA_PRO_INITIALIZED = false;
        
        console.log('🧹 All script tracking and initialization flags reset');
    }
    
    // NEW: Get script tracking status (for debugging)
    getScriptTrackingStatus() {
        if (window.OPTIMA_SCRIPT_TRACKER) {
            return {
                loadedExternal: window.OPTIMA_SCRIPT_TRACKER.loadedExternal.size,
                executedInline: window.OPTIMA_SCRIPT_TRACKER.executedInline.size,
                scriptElements: window.OPTIMA_SCRIPT_TRACKER.scriptElements.size
            };
        }
        return null;
    }
    
    updateActiveStates(path) {
        // console.log('🔄 Updating active states for path:', path);
        
        // Remove ALL active states and indicators
        document.querySelectorAll('.nav-link.active').forEach(link => {
            // console.log('🗑️ Removing active from:', link.href);
            link.classList.remove('active');
        });
        
        // Remove ALL nav-indicator elements
        document.querySelectorAll('.nav-indicator').forEach(indicator => {
            // console.log('🗑️ Removing indicator');
            indicator.remove();
        });
        
        // Find current link with multiple selector strategies
        let currentLink = null;
        
        // Strategy 1: Exact path match
        currentLink = document.querySelector(`a.nav-link[href="${path}"]`);
        
        // Strategy 2: Base URL + path match  
        if (!currentLink) {
            const baseUrl = window.location.origin;
            currentLink = document.querySelector(`a.nav-link[href="${baseUrl}${path}"]`);
        }
        
        // Strategy 3: Contains path (for complex URLs)
        if (!currentLink) {
            const links = document.querySelectorAll('a.nav-link[href]');
            for (const link of links) {
                try {
                    const linkUrl = new URL(link.href);
                    if (linkUrl.pathname === path) {
                        currentLink = link;
                        break;
                    }
                } catch (e) {}
            }
        }
        
        if (currentLink) {
            console.log('✅ Setting active state for:', currentLink.href);
            currentLink.classList.add('active');
            
            // Add single indicator
            if (!currentLink.querySelector('.nav-indicator')) {
                const indicator = document.createElement('span');
                indicator.className = 'nav-indicator';
                currentLink.appendChild(indicator);
                // console.log('✨ Added nav-indicator');
            }
            
            // Scroll to active item if needed
            setTimeout(() => {
                this.scrollToActiveItem(currentLink);
            }, 100);
        } else {
            console.warn('⚠️ No matching nav-link found for path:', path);
        }
    }
    
    scrollToActiveItem(activeLink) {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar || !activeLink) return;
        
        const linkRect = activeLink.getBoundingClientRect();
        const sidebarRect = sidebar.getBoundingClientRect();
        
        // Check if link is outside visible area
        if (linkRect.top < sidebarRect.top || linkRect.bottom > sidebarRect.bottom) {
            activeLink.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }
    
    initializePageComponents() {
        // Immediate cleanup and initialization for better performance
        this.cleanupPageComponents();
        
        try {
            // ULTRA AGGRESSIVE: DataTables cleanup and initialization
            if (window.jQuery && jQuery.fn.DataTable) {
                // Step 1: Destroy ALL existing DataTables
                jQuery('table.dataTable').each(function() {
                    try {
                        const table = jQuery(this).DataTable();
                        if (table) {
                            table.destroy();
                        }
                    } catch (e) {}
                });
                
                // Step 2: Remove ALL DataTables classes and wrappers
                jQuery('table').removeClass('dataTable');
                jQuery('.dataTables_wrapper').remove();
                jQuery('.dataTables_length').remove();
                jQuery('.dataTables_filter').remove();
                jQuery('.dataTables_info').remove();
                jQuery('.dataTables_paginate').remove();
                jQuery('.dataTables_processing').remove();
                
                // Step 3: Wait a bit for DOM cleanup
                setTimeout(() => {
                    // Step 4: Initialize DataTables ONLY for tables with data
                    jQuery('table[id$="Table"]:not(.dataTable)').each(function() {
                        try {
                            const $table = jQuery(this);
                            const $thead = $table.find('thead');
                            const $tbody = $table.find('tbody');
                            
                            // Check if table has proper structure
                            if ($thead.length === 0 || $thead.find('th').length === 0) {
                                return;
                            }
                            
                            // Check if table has data rows
                            const dataRows = $tbody.find('tr').length;
                            if (dataRows === 0) {
                                console.log('Skipping empty table:', this.id);
                                return;
                            }
                            
                            // Check column count consistency
                            const headerCols = $thead.find('th').length;
                            const bodyRows = $tbody.find('tr');
                            let columnMismatch = false;
                            
                            bodyRows.each(function() {
                                const rowCols = jQuery(this).find('td, th').length;
                                if (rowCols !== headerCols && rowCols > 0) {
                                    columnMismatch = true;
                                    return false;
                                }
                            });
                            
                            if (columnMismatch) {
                                console.warn('Column count mismatch in table:', this.id);
                                return;
                            }
                            
                            // Initialize DataTable
                            if (typeof window[this.id + 'Init'] === 'function') {
                                console.log('Using custom init for:', this.id);
                                window[this.id + 'Init']();
                            } else {
                                console.log('Using fallback init for:', this.id);
                                $table.DataTable({
                                    responsive: true,
                                    pageLength: 10,
                                    autoWidth: false,
                                    destroy: true,
                                    language: {
                                        "decimal": "",
                                        "emptyTable": "Tidak ada data tersedia",
                                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                                        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                                        "infoFiltered": "(disaring dari _MAX_ total entri)",
                                        "infoPostFix": "",
                                        "thousands": ",",
                                        "lengthMenu": "Tampilkan _MENU_ entri",
                                        "loadingRecords": "Memuat...",
                                        "processing": "Memproses...",
                                        "search": "Cari:",
                                        "zeroRecords": "Tidak ditemukan data yang cocok",
                                        "paginate": {
                                            "first": "Pertama",
                                            "last": "Terakhir",
                                            "next": "Selanjutnya",
                                            "previous": "Sebelumnya"
                                        },
                                        "aria": {
                                            "sortAscending": ": aktifkan untuk mengurutkan kolom naik",
                                            "sortDescending": ": aktifkan untuk mengurutkan kolom turun"
                                        }
                                    }
                                });
                            }
                            
                            // CRITICAL: Trigger data loading functions for specific tables
                            if (this.id === 'contractsTable' && typeof loadKontrakData === 'function') {
                                console.log('🚀 Triggering loadKontrakData for contractsTable');
                                setTimeout(() => {
                                    loadKontrakData();
                                }, 100);
                            }
                            
                            if (this.id === 'diTable' && typeof loadDIData === 'function') {
                                console.log('🚀 Triggering loadDIData for diTable');
                                setTimeout(() => {
                                    loadDIData();
                                }, 100);
                            }
                            
                            if (this.id === 'quotationsTable' && typeof loadQuotationsData === 'function') {
                                console.log('🚀 Triggering loadQuotationsData for quotationsTable');
                                setTimeout(() => {
                                    loadQuotationsData();
                                }, 100);
                            }
                        } catch (e) {
                            console.warn('DataTable initialization failed for:', this.id, e);
                        }
                    });
                }, 200);
            }
            
            // Re-initialize Bootstrap components with better cleanup
            if (window.bootstrap) {
                // Initialize dropdowns safely (tooltips disabled to prevent errors)
                if (bootstrap.Dropdown) {
                    const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]:not(.dropdown-initialized)');
                    dropdowns.forEach(el => {
                        try {
                            if (el && typeof el === 'object') {
                                new bootstrap.Dropdown(el);
                                if (el.classList) {
                                    el.classList.add('dropdown-initialized');
                                }
                            }
                        } catch (e) {
                            // Silently ignore dropdown initialization errors
                        }
                    });
                }
                
                // DISABLED: Tooltips to prevent errors during SPA navigation
                // Tooltips will be re-enabled only when needed
            }
            
            // ULTRA AGGRESSIVE: Destroy existing charts to prevent Canvas conflicts
            if (window.Chart) {
                // Method 1: Destroy all chart instances
                if (window.Chart.instances) {
                    Object.values(window.Chart.instances).forEach(chart => {
                        try {
                            chart.destroy();
                        } catch (e) {}
                    });
                    // Clear instances object
                    window.Chart.instances = {};
                }
                
                // Method 2: Destroy charts by canvas elements
                document.querySelectorAll('canvas').forEach(canvas => {
                    try {
                        const chart = Chart.getChart(canvas);
                        if (chart) {
                            chart.destroy();
                        }
                    } catch (e) {}
                });
                
                // Method 3: Force clear canvas context
                document.querySelectorAll('canvas').forEach(canvas => {
                    try {
                        const ctx = canvas.getContext('2d');
                        if (ctx) {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                        }
                    } catch (e) {}
                });
                
                // Method 4: Remove and recreate canvas elements
                document.querySelectorAll('canvas[id*="Chart"]').forEach(canvas => {
                    try {
                        const parent = canvas.parentNode;
                        const newCanvas = canvas.cloneNode(true);
                        parent.replaceChild(newCanvas, canvas);
                    } catch (e) {}
                });
            }
            
            // Immediate page-specific initialization
            if (typeof initializeDashboard === 'function') {
                try { initializeDashboard(); } catch(e) {}
            }
            if (typeof initializeKontrakPage === 'function') {
                try { initializeKontrakPage(); } catch(e) {}
            }
            
            // CRITICAL: Trigger data loading for pages that need it
            setTimeout(() => {
                // Check current path and trigger appropriate data loading
                const currentPath = this.currentPath || window.location.pathname;
                
                if (currentPath.includes('/marketing/kontrak') && typeof loadKontrakData === 'function') {
                    console.log('🚀 Auto-triggering loadKontrakData for kontrak page');
                    loadKontrakData();
                }
                
                if (currentPath.includes('/marketing/di') && typeof loadDIData === 'function') {
                    console.log('🚀 Auto-triggering loadDIData for DI page');
                    loadDIData();
                }
                
                if (currentPath.includes('/marketing/penawaran') && typeof loadQuotationsData === 'function') {
                    console.log('🚀 Auto-triggering loadQuotationsData for penawaran page');
                    loadQuotationsData();
                }
                
                if (currentPath.includes('/operational/delivery') && typeof loadDeliveryData === 'function') {
                    console.log('🚀 Auto-triggering loadDeliveryData for delivery page');
                    loadDeliveryData();
                }
            }, 300);
            
            // DISABLED: Force trigger DataTables to prevent double initialization
            // DataTables are now initialized only once in the main loop above
            
        } catch (error) {
            console.warn('⚠️ Error initializing page components:', error);
        }
        
        // Trigger custom initialization event immediately
        document.dispatchEvent(new CustomEvent('spa:pageLoaded', {
            detail: { path: this.currentPath }
        }));
    }
    
    cleanupPageComponents() {
        // Hide and cleanup all tooltips/popovers
        this.hideAllTooltips();
        
        // ULTRA AGGRESSIVE: Cleanup Chart.js instances
        if (window.Chart) {
            // Method 1: Destroy all chart instances
            if (window.Chart.instances) {
                Object.values(window.Chart.instances).forEach(chart => {
                    try {
                        chart.destroy();
                    } catch (e) {}
                });
                // Clear instances object
                window.Chart.instances = {};
            }
            
            // Method 2: Destroy charts by canvas elements
            document.querySelectorAll('canvas').forEach(canvas => {
                try {
                    const chart = Chart.getChart(canvas);
                    if (chart) {
                        chart.destroy();
                    }
                } catch (e) {}
            });
            
            // Method 3: Force clear canvas context
            document.querySelectorAll('canvas').forEach(canvas => {
                try {
                    const ctx = canvas.getContext('2d');
                    if (ctx) {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                } catch (e) {}
            });
        }
        
        // Cleanup intervals and timeouts
        if (window.dashboardInterval) {
            clearInterval(window.dashboardInterval);
            window.dashboardInterval = null;
        }
        
        // Remove Bootstrap component classes
        const oldElements = document.querySelectorAll('.tooltip-initialized, .dropdown-initialized');
        oldElements.forEach(el => {
            try {
                if (el && el.classList) {
                    el.classList.remove('tooltip-initialized', 'dropdown-initialized');
                }
                
                // Destroy existing Bootstrap tooltips/popovers safely
                if (window.bootstrap) {
                    try {
                        if (bootstrap.Tooltip) {
                            const tooltip = bootstrap.Tooltip.getInstance(el);
                            if (tooltip && typeof tooltip.dispose === 'function') {
                                tooltip.dispose();
                            }
                        }
                        
                        if (bootstrap.Popover) {
                            const popover = bootstrap.Popover.getInstance(el);
                            if (popover && typeof popover.dispose === 'function') {
                                popover.dispose();
                            }
                        }
                    } catch (e) {
                        // Silently ignore disposal errors
                    }
                }
            } catch (e) {
                // Silently ignore element processing errors
            }
        });
        
        // Remove any orphaned tooltip/popover elements safely
        const orphanedElements = document.querySelectorAll('.tooltip, .popover');
        orphanedElements.forEach(el => {
            try {
                if (el && el.parentNode) {
                    el.remove();
                }
            } catch (e) {
                // Silently ignore removal errors
            }
        });
    }
    
    hideAllTooltips() {
        try {
            // AGGRESSIVE: Force hide all tooltips by destroying them completely
            if (window.bootstrap) {
                // Destroy ALL tooltip instances globally
                try {
                    const allTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                    allTooltips.forEach(el => {
                        try {
                            // Force dispose instead of hide
                            const tooltip = bootstrap.Tooltip.getInstance(el);
                            if (tooltip) {
                                tooltip.dispose();
                            }
                        } catch (e) {
                            // Force remove data attributes
                            try {
                                el.removeAttribute('data-bs-toggle');
                                el.removeAttribute('data-bs-original-title');
                                el.removeAttribute('title');
                            } catch (e2) {}
                        }
                    });
                } catch (e) {}
                
                // Destroy ALL popover instances globally
                try {
                    const allPopovers = document.querySelectorAll('[data-bs-toggle="popover"]');
                    allPopovers.forEach(el => {
                        try {
                            const popover = bootstrap.Popover.getInstance(el);
                            if (popover) {
                                popover.dispose();
                            }
                        } catch (e) {
                            // Force remove data attributes
                            try {
                                el.removeAttribute('data-bs-toggle');
                                el.removeAttribute('data-bs-content');
                            } catch (e2) {}
                        }
                    });
                } catch (e) {}
            }
            
            // AGGRESSIVE: Remove ALL tooltip/popover elements from DOM
            const allTooltipElements = document.querySelectorAll('.tooltip, .popover, [class*="tooltip"], [class*="popover"]');
            allTooltipElements.forEach(el => {
                try {
                    if (el && el.parentNode) {
                        el.remove();
                    }
                } catch (e) {}
            });
            
            // AGGRESSIVE: Clear any pending tooltip timeouts
            try {
                // Clear all timeouts that might be related to tooltips
                for (let i = 1; i < 10000; i++) {
                    clearTimeout(i);
                }
            } catch (e) {}
            
        } catch (error) {
            // Silently ignore all tooltip cleanup errors
        }
    }
    
    async handleFormSubmission(form) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoadingIndicator();
        
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action || window.location.pathname, {
                method: form.method || 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-SPA-Request': 'true'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                
                if (result.redirect) {
                    this.navigateTo(result.redirect);
                } else if (result.reload) {
                    this.loadContent(window.location.pathname, false);
                }
                
                if (result.message) {
                    createOptimaToast({
                        type: result.success ? 'success' : 'error',
                        title: result.success ? 'Success' : 'Error',
                        message: result.message
                    });
                }
            }
            
        } catch (error) {
            console.error('Form submission error:', error);
            createOptimaToast({
                type: 'error',
                title: 'Error',
                message: 'Form submission failed'
            });
        } finally {
            this.isLoading = false;
            this.hideLoadingIndicator();
        }
    }
    
    // NEW: Force content reload to trigger data loading functions
    forceContentReload(path) {
        console.log('🔄 Force reloading content area for:', path);
        
        // Get the main content area
        const contentArea = document.querySelector('.content-body') || document.querySelector('main') || document.querySelector('#main-content');
        
        if (!contentArea) {
            console.warn('Content area not found, falling back to normal initialization');
            setTimeout(() => {
                this.initializePageComponents();
            }, 100);
            return;
        }
        
        // Add a temporary class to indicate reloading
        contentArea.classList.add('content-reloading');
        
        // Force reload the content area by re-fetching
        setTimeout(async () => {
            try {
                const response = await fetch(path, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html, application/xhtml+xml',
                        'X-Content-Reload': 'true'
                    }
                });
                
                if (response.ok) {
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Update only the content area
                    const newContent = doc.querySelector('.content-body') || doc.querySelector('main') || doc.querySelector('#main-content');
                    if (newContent) {
                        contentArea.innerHTML = newContent.innerHTML;
                        console.log('✅ Content area reloaded successfully');
                        
                        // CRITICAL: Extract and execute JavaScript from the new content
                        this.extractAndExecuteScripts(doc);
                        
                        // Now initialize components and trigger data loading
                        setTimeout(() => {
                            this.initializePageComponents();
                            this.triggerDataLoading(path);
                        }, 200);
                    }
                }
            } catch (error) {
                console.warn('Content reload failed, using fallback:', error);
                // Fallback to normal initialization
                setTimeout(() => {
                    this.initializePageComponents();
                    this.triggerDataLoading(path);
                }, 200);
            } finally {
                contentArea.classList.remove('content-reloading');
            }
        }, 100);
    }
    
    // OPTIMIZED: Trigger data loading functions based on path (avoid duplicates)
    triggerDataLoading(path) {
        console.log('🚀 Triggering data loading for path:', path);
        
        // Track data loading to avoid duplicates
        if (!window.dataLoadingTracker) {
            window.dataLoadingTracker = new Set();
        }
        
        // Create unique key for this path and timestamp
        const loadingKey = `${path}_${Date.now()}`;
        
        // Wait a bit for DOM to be ready
        setTimeout(() => {
            // Check if data is already being loaded for this path
            const currentPathKey = path.split('/').pop(); // Get last part of path
            if (window.dataLoadingTracker.has(currentPathKey)) {
                console.log('⏭️ Data already loading for path:', currentPathKey);
                return;
            }
            
            // Mark as loading
            window.dataLoadingTracker.add(currentPathKey);
            
            if (path.includes('/marketing/kontrak')) {
                // OPTIMIZED: Try efficient reload first, then fallback to full load
                if (typeof reloadKontrakDataOnly === 'function') {
                    console.log('🚀 Triggering reloadKontrakDataOnly (efficient)');
                    reloadKontrakDataOnly();
                } else if (typeof loadKontrakData === 'function') {
                    console.log('🚀 Triggering loadKontrakData (full)');
                    loadKontrakData();
                } else {
                    console.warn('loadKontrakData function not found, trying direct AJAX call');
                    this.loadKontrakDataDirect();
                }
            }
            
            if (path.includes('/marketing/di')) {
                if (typeof loadDIData === 'function') {
                    console.log('🚀 Triggering loadDIData');
                    loadDIData();
                } else {
                    console.warn('loadDIData function not found, trying direct AJAX call');
                    this.loadDIDataDirect();
                }
            }
            
            if (path.includes('/marketing/penawaran')) {
                if (typeof loadQuotationsData === 'function') {
                    console.log('🚀 Triggering loadQuotationsData');
                    loadQuotationsData();
                } else {
                    console.warn('loadQuotationsData function not found');
                }
            }
            
            if (path.includes('/operational/delivery')) {
                if (typeof loadDeliveryData === 'function') {
                    console.log('🚀 Triggering loadDeliveryData');
                    loadDeliveryData();
                } else {
                    console.warn('loadDeliveryData function not found');
                }
            }
            
            // Generic trigger for any page
            if (typeof window.initializePage === 'function') {
                console.log('🚀 Triggering initializePage');
                window.initializePage();
            }
            
            // Clear loading flag after a delay
            setTimeout(() => {
                window.dataLoadingTracker.delete(currentPathKey);
            }, 2000);
            
        }, 500); // Increased delay to allow script execution
    }
    
    // NEW: Direct AJAX call for kontrak data
    loadKontrakDataDirect() {
        console.log('🚀 Making direct AJAX call for kontrak data');
        
        const tbody = document.querySelector('#contractsTable tbody');
        if (!tbody) {
            console.warn('contractsTable tbody not found');
            return;
        }
        
        tbody.innerHTML = '<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
        
        fetch('/optima1/public/marketing/kontrak/getDataTable', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'draw=1&start=0&length=100'
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ Kontrak data loaded:', data);
            
            if (data && data.data && data.data.length > 0) {
                let tableHTML = '';
                data.data.forEach(item => {
                    tableHTML += `
                        <tr>
                            <td><a href="#" class="text-decoration-none fw-bold">${item.contract_number || ''}</a></td>
                            <td>${item.po || '-'}</td>
                            <td>${item.client_name || '-'}</td>
                            <td>${item.period || '-'}</td>
                            <td><span class="fw-bold text-primary">${item.total_unit || 0}</span></td>
                            <td><span class="badge bg-success">${item.status || 'Unknown'}</span></td>
                        </tr>
                    `;
                });
                tbody.innerHTML = tableHTML;
                console.log('✅ Kontrak table populated with', data.data.length, 'records');
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data kontrak</td></tr>';
                console.log('⚠️ No kontrak data found');
            }
        })
        .catch(error => {
            console.error('❌ Error loading kontrak data:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>';
        });
    }
    
    // NEW: Direct AJAX call for DI data
    loadDIDataDirect() {
        console.log('🚀 Making direct AJAX call for DI data');
        
        const tbody = document.querySelector('#diTable tbody');
        if (!tbody) {
            console.warn('diTable tbody not found');
            return;
        }
        
        tbody.innerHTML = '<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
        
        // You'll need to implement the actual DI data endpoint
        fetch('/optima1/public/marketing/di/getData', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ DI data loaded:', data);
            
            if (data && data.length > 0) {
                let tableHTML = '';
                data.forEach(item => {
                    tableHTML += `
                        <tr>
                            <td>${item.di_number || ''}</td>
                            <td>${item.contract_number || '-'}</td>
                            <td>${item.client_name || '-'}</td>
                            <td>${item.status || '-'}</td>
                            <td>${item.created_date || '-'}</td>
                            <td><span class="badge bg-info">${item.status || 'Unknown'}</span></td>
                        </tr>
                    `;
                });
                tbody.innerHTML = tableHTML;
                console.log('✅ DI table populated with', data.length, 'records');
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data DI</td></tr>';
                console.log('⚠️ No DI data found');
            }
        })
        .catch(error => {
            console.error('❌ Error loading DI data:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>';
        });
    }
    
    // ULTIMATE SOLUTION: COMPLETELY DISABLE problematic script loading
    extractAndExecuteScripts(doc) {
        console.log('🔧 Extracting and executing scripts from loaded content');
        
        // Initialize persistent script tracking (survives navigation)
        if (!window.OPTIMA_SCRIPT_TRACKER) {
            window.OPTIMA_SCRIPT_TRACKER = {
                loadedExternal: new Set(),
                executedInline: new Set(),
                scriptElements: new Map(),
                blockedScripts: new Set()
            };
        }
        
        const tracker = window.OPTIMA_SCRIPT_TRACKER;
        const scripts = doc.querySelectorAll('script');
        
        scripts.forEach((script, index) => {
            try {
                if (script.src) {
                    // ULTIMATE SOLUTION: COMPLETELY DISABLE problematic scripts - NO LOADING AT ALL
                    if (this.isProblematicScript(script.src)) {
                        console.log('🚫 ULTIMATE BLOCKING - COMPLETELY DISABLED:', script.src);
                        tracker.blockedScripts.add(script.src);
                        // DO NOT LOAD - JUST RETURN
                        return;
                    }
                    
                    // Check if script already exists in DOM
                    const existingScript = document.querySelector(`script[src="${script.src}"]`);
                    if (existingScript) {
                        console.log('⏭️ SKIPPING external script (already exists in DOM):', script.src);
                        tracker.loadedExternal.add(script.src);
                        return;
                    }
                    
                    // Check tracking states
                    const isAlreadyLoaded = tracker.loadedExternal.has(script.src);
                    const isBeingLoaded = tracker.scriptElements.has(script.src);
                    const isBlocked = tracker.blockedScripts.has(script.src);
                    
                    if (isAlreadyLoaded || isBeingLoaded || isBlocked) {
                        console.log('⏭️ SKIPPING external script (tracked state):', script.src);
                        return;
                    }
                    
                    // Only load NON-problematic scripts
                    const newScript = document.createElement('script');
                    newScript.src = script.src;
                    newScript.async = script.async;
                    newScript.defer = script.defer;
                    
                    // Add load event listener to track completion
                    newScript.onload = () => {
                        tracker.loadedExternal.add(script.src);
                        tracker.scriptElements.delete(script.src);
                        console.log('✅ External script loaded successfully:', script.src);
                    };
                    
                    newScript.onerror = () => {
                        tracker.scriptElements.delete(script.src);
                        tracker.blockedScripts.add(script.src);
                        console.error('❌ Failed to load external script:', script.src);
                    };
                    
                    // Track loading state
                    tracker.scriptElements.set(script.src, newScript);
                    
                    // Append to DOM
                    document.head.appendChild(newScript);
                    console.log('📜 Loading external script:', script.src);
                    
                } else if (script.textContent.trim()) {
                    // Inline script - use content hash for tracking
                    const scriptContent = script.textContent.trim();
                    const scriptHash = this.createContentHash(scriptContent);
                    
                    // ULTIMATE SOLUTION: COMPLETELY DISABLE problematic inline scripts
                    if (this.shouldSkipInlineScript(scriptContent)) {
                        console.log('🚫 ULTIMATE BLOCKING - COMPLETELY DISABLED inline script');
                        tracker.blockedScripts.add(scriptHash);
                        // DO NOT EXECUTE - JUST RETURN
                        return;
                    }
                    
                    // Check tracking states
                    const isAlreadyExecuted = tracker.executedInline.has(scriptHash);
                    const isBlocked = tracker.blockedScripts.has(scriptHash);
                    
                    if (isAlreadyExecuted || isBlocked) {
                        console.log('⏭️ SKIPPING inline script (already executed/blocked)');
                        return;
                    }
                    
                    console.log('📜 Executing inline script (hash:', scriptHash.substring(0, 8) + '...)');
                    
                    // Execute in isolated scope to prevent conflicts
                    try {
                        // Use Function constructor instead of eval for better isolation
                        const executeScript = new Function(scriptContent);
                        executeScript();
                        
                        // Mark as executed
                        tracker.executedInline.add(scriptHash);
                        console.log('✅ Inline script executed successfully');
                        
                    } catch (execError) {
                        console.warn('⚠️ Error executing inline script:', execError);
                        // Block failed scripts to prevent infinite retries
                        tracker.blockedScripts.add(scriptHash);
                        tracker.executedInline.add(scriptHash);
                    }
                }
            } catch (error) {
                console.warn('⚠️ Error processing script:', error);
            }
        });
    }
    
    // Helper function to identify problematic external scripts
    isProblematicScript(src) {
        const problematicPatterns = [
            'sidebar-advanced.js',
            'spa-navigation.js',
            'optima-pro.js',
            'sidebar-enhanced.js'
        ];
        
        return problematicPatterns.some(pattern => src.includes(pattern));
    }
    
    // Helper function to create content hash
    createContentHash(content) {
        let hash = 0;
        for (let i = 0; i < content.length; i++) {
            const char = content.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash.toString(36); // Base36 for shorter hash
    }
    
    // Helper function to identify problematic inline scripts
    shouldSkipInlineScript(content) {
        const skipPatterns = [
            'OptimaSPANavigation',
            'OptimaSidebar', 
            'OptimaHelpers',
            'class Optima',
            'function Optima',
            'var Optima',
            'let Optima',
            'const Optima'
        ];
        
        return skipPatterns.some(pattern => content.includes(pattern));
    }
    
    
    showLoadingIndicator() {
        // DISABLE loading indicator for fast local navigation
        // Only show for slow requests (>800ms)
        if (this.loadingTimeout) return;
        
        this.loadingTimeout = setTimeout(() => {
            // Only show loading if request is still pending after 800ms
            if (!this.isLoading) return;
            
            let indicator = document.getElementById('spa-loading-indicator');
            
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'spa-loading-indicator';
                indicator.innerHTML = `
                    <div class="spa-loading-content">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;
                indicator.style.cssText = `
                    position: fixed;
                    top: 70px;
                    right: 20px;
                    background: rgba(255, 255, 255, 0.95);
                    padding: 15px 25px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 9999;
                    border: 1px solid #e9ecef;
                `;
                
                const style = document.createElement('style');
                style.textContent = `
                    .spa-loading-content {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        color: #0061f2;
                        font-size: 14px;
                        font-weight: 500;
                    }
                `;
                document.head.appendChild(style);
                
                document.body.appendChild(indicator);
            }
            
            indicator.style.display = 'block';
            
            // Auto-cleanup after 5 seconds (reduced from 10s)
            setTimeout(() => {
                this.hideLoadingIndicator();
            }, 5000);
            
        }, 800); // Only show loading after 800ms for slow requests
    }
    
    hideLoadingIndicator() {
        // Clear ALL timeouts
        if (this.loadingTimeout) {
            clearTimeout(this.loadingTimeout);
            this.loadingTimeout = null;
        }
        
        // Force remove the indicator completely
        const indicators = document.querySelectorAll('#spa-loading-indicator');
        indicators.forEach(indicator => {
            indicator.remove();
        });
        
        // Clear any content loading states
        const contentBody = document.querySelector('.content-body');
        if (contentBody) {
            contentBody.classList.remove('loading');
            contentBody.style.opacity = '1';
            contentBody.style.pointerEvents = 'auto';
        }
    }
    
    // Utility function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Public methods
    getCurrentPath() {
        return this.currentPath;
    }
    
    isNavigationLoading() {
        return this.isLoading;
    }
    
    // Disable SPA for specific links
    disableSPAFor(selector) {
        document.querySelectorAll(selector).forEach(link => {
            link.setAttribute('data-spa-disabled', 'true');
        });
    }
    
    // Enable SPA for specific forms
    enableSPAFor(selector) {
        document.querySelectorAll(selector).forEach(form => {
            form.classList.add('spa-form');
        });
    }
}

// IMMEDIATE INITIALIZATION - Before other scripts can interfere
console.log('🚀 SPA Navigation loading...');

// Initialize as soon as this script loads
(function() {
    'use strict';
    
    function initSPA() {
        try {
            console.log('🎯 Initializing SPA navigation system...');
            window.optimaSPA = new OptimaSPANavigation();
            console.log('✅ SPA Navigation initialized successfully');
            
            // Global functions for external access
            window.navigateTo = function(url) {
                if (window.optimaSPA) {
                    return window.optimaSPA.navigateTo(url);
                } else {
                    console.warn('⚠️ SPA not available, using fallback');
                    window.location.href = url;
                }
            };
            
            window.reloadCurrentPage = function() {
                if (window.optimaSPA) {
                    return window.optimaSPA.loadContent(window.location.pathname, false);
                } else {
                    window.location.reload();
                }
            };
            
            // Test function for debugging
            window.testSPA = function() {
                console.log('🔧 SPA Debug Info:');
                console.log('- SPA instance:', window.optimaSPA);
                console.log('- Current path:', window.optimaSPA?.getCurrentPath());
                console.log('- Is loading:', window.optimaSPA?.isNavigationLoading());
                console.log('- Debug mode:', window.SPA_DEBUG);
                
                // Test navigation
                console.log('🧪 Testing SPA navigation to /dashboard...');
                if (window.optimaSPA) {
                    window.optimaSPA.navigateTo('/dashboard');
                }
            };
            
            // Mark SPA as ready
            window.SPA_READY = true;
            console.log('🎉 SPA system ready for navigation');
            
        } catch (error) {
            console.error('❌ Critical error initializing SPA:', error);
            window.SPA_READY = false;
        }
    }
    
    // Initialize immediately if DOM is ready, otherwise wait
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSPA);
    } else {
        initSPA();
    }
})();

// Listen for custom events to reload specific components
document.addEventListener('spa:pageLoaded', function(e) {
    // console.log('SPA page loaded:', e.detail.path);
    
    // Refresh notification count
    if (typeof updateNotificationCount === 'function') {
        updateNotificationCount();
    }
    
    // Re-initialize any specific components
    if (typeof initPageSpecificComponents === 'function') {
        initPageSpecificComponents();
    }
});

// NEW: Global function to trigger efficient data reload
window.reloadPageData = function() {
    if (window.optimaSPA && typeof window.optimaSPA.triggerEfficientDataReload === 'function') {
        window.optimaSPA.triggerEfficientDataReload();
    } else {
        console.warn('SPA navigation not available for data reload');
    }
};

// NEW: Global function to reload content only (most efficient)
window.reloadContentOnly = function(url) {
    if (window.optimaSPA && typeof window.optimaSPA.reloadContentOnly === 'function') {
        return window.optimaSPA.reloadContentOnly(url || window.location.href);
    } else {
        console.warn('SPA navigation not available for content reload');
    }
};

// NEW: Global debugging functions
window.debugSPA = function() {
    console.log('🔍 SPA Debug Information:');
    console.log('- SPA Initialized:', window.OPTIMA_SPA_INITIALIZED);
    console.log('- SPA Ready:', window.SPA_READY);
    console.log('- SPA Instance:', window.optimaSPA);
    
    if (window.optimaSPA) {
        console.log('- Current Path:', window.optimaSPA.currentPath);
        console.log('- Is Loading:', window.optimaSPA.isLoading);
        console.log('- Script Tracking:', window.optimaSPA.getScriptTrackingStatus());
    }
    
    if (window.OPTIMA_SCRIPT_TRACKER) {
        console.log('- Loaded External Scripts:', Array.from(window.OPTIMA_SCRIPT_TRACKER.loadedExternal));
        console.log('- Executed Inline Scripts:', window.OPTIMA_SCRIPT_TRACKER.executedInline.size);
        console.log('- Script Elements in Loading:', Array.from(window.OPTIMA_SCRIPT_TRACKER.scriptElements.keys()));
    }
};

// NEW: Global function to clear script tracking (for testing)
window.clearSPAScriptTracking = function() {
    if (window.optimaSPA && typeof window.optimaSPA.clearScriptTracking === 'function') {
        window.optimaSPA.clearScriptTracking();
    } else {
        console.warn('SPA navigation not available for script tracking clear');
    }
};

// NEW: Global function to reset all script tracking (nuclear option)
window.resetAllSPAScriptTracking = function() {
    if (window.optimaSPA && typeof window.optimaSPA.resetAllScriptTracking === 'function') {
        window.optimaSPA.resetAllScriptTracking();
    } else {
        console.warn('SPA navigation not available for script tracking reset');
    }
};

// NEW: Global function to check script status
window.checkScriptStatus = function() {
    console.log('🔍 Script Status Check:');
    console.log('- SPA Initialized:', window.OPTIMA_SPA_INITIALIZED);
    console.log('- Sidebar Initialized:', window.OPTIMA_SIDEBAR_INITIALIZED);
    console.log('- Pro Initialized:', window.OPTIMA_PRO_INITIALIZED);
    
    if (window.OPTIMA_SCRIPT_TRACKER) {
        console.log('- Loaded External Scripts:', Array.from(window.OPTIMA_SCRIPT_TRACKER.loadedExternal));
        console.log('- Executed Inline Scripts:', window.OPTIMA_SCRIPT_TRACKER.executedInline.size);
        console.log('- Script Elements in Loading:', Array.from(window.OPTIMA_SCRIPT_TRACKER.scriptElements.keys()));
        console.log('- Blocked Scripts:', Array.from(window.OPTIMA_SCRIPT_TRACKER.blockedScripts));
    }
    
    // Check for existing problematic scripts in DOM
    const existingProblematic = document.querySelectorAll('script[src*="sidebar-advanced.js"], script[src*="spa-navigation.js"], script[src*="optima-pro.js"], script[src*="sidebar-enhanced.js"]');
    console.log('- Existing Problematic Scripts in DOM:', existingProblematic.length);
    existingProblematic.forEach(script => console.log('  -', script.src));
};

// NEW: Force block all problematic scripts immediately
window.forceBlockProblematicScripts = function() {
    console.log('🚫 FORCE BLOCKING all problematic scripts');
    
    if (!window.OPTIMA_SCRIPT_TRACKER) {
        window.OPTIMA_SCRIPT_TRACKER = {
            loadedExternal: new Set(),
            executedInline: new Set(),
            scriptElements: new Map(),
            blockedScripts: new Set()
        };
    }
    
    const problematicPatterns = ['sidebar-advanced.js', 'spa-navigation.js', 'optima-pro.js', 'sidebar-enhanced.js'];
    
    problematicPatterns.forEach(pattern => {
        // Block pattern
        window.OPTIMA_SCRIPT_TRACKER.blockedScripts.add(pattern);
        
        // Find and mark existing scripts
        const existingScripts = document.querySelectorAll(`script[src*="${pattern}"]`);
        existingScripts.forEach(script => {
            window.OPTIMA_SCRIPT_TRACKER.blockedScripts.add(script.src);
            script.dataset.blocked = 'true';
            console.log('🚫 BLOCKED:', script.src);
        });
    });
    
    console.log('✅ Force blocking completed');
};
