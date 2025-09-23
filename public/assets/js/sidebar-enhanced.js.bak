/*!
 * OPTIMA Pro Enhanced Sidebar JavaScript
 * Enhanced functionality for better user experience
 * PT Sarana Mitra Luas Tbk - OPTIMA System v2.0
 */

(function() {
    'use strict';

    // Enhanced Sidebar Toggle with Smooth Animations
    const EnhancedSidebar = {
        initialized: false,
        sidebar: null,
        mainContent: null,
        toggleButtons: [],
        
        init: function() {
            if (this.initialized) return;
            
            this.sidebar = document.querySelector('.sidebar');
            this.mainContent = document.querySelector('.main-content');
            this.toggleButtons = document.querySelectorAll('[onclick="toggleSidebar()"], .sidebar-toggle');
            
            if (!this.sidebar || !this.mainContent) return;
            
            this.bindEvents();
            this.initializeTooltips();
            this.initializeState();
            this.initialized = true;
            
            // console.log('Enhanced Sidebar initialized');
        },
        
        bindEvents: function() {
            // Mobile overlay click
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.addEventListener('click', this.hideMobileSidebar.bind(this));
            }
            
            // Handle responsive changes
            window.addEventListener('resize', this.handleResize.bind(this));
            
            // Enhanced toggle for mobile
            document.addEventListener('click', (e) => {
                if (e.target.closest('.sidebar-toggle')) {
                    e.preventDefault();
                    this.handleMobileToggle();
                }
            });
        },
        
        initializeTooltips: function() {
            // Initialize tooltips for collapsed sidebar
            const navLinks = this.sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                const text = link.querySelector('.nav-link-text')?.textContent.trim();
                if (text && !link.hasAttribute('data-bs-toggle')) {
                    link.setAttribute('data-bs-toggle', 'tooltip');
                    link.setAttribute('data-bs-placement', 'right');
                    link.setAttribute('title', text);
                }
            });
        },
        
        initializeState: function() {
            // Load saved state
            const savedState = localStorage.getItem('optima-sidebar-collapsed');
            if (savedState === 'true') {
                this.sidebar.classList.add('collapsed');
                this.mainContent.classList.add('expanded');
            }
            
            // Initialize collapse states
            this.initializeCollapseStates();
        },
        
        initializeCollapseStates: function() {
            // Save and restore submenu collapse states
            const collapseElements = this.sidebar.querySelectorAll('.collapse');
            collapseElements.forEach(collapse => {
                const id = collapse.id;
                const savedState = localStorage.getItem(`optima-submenu-${id}`);
                
                if (savedState === 'true') {
                    collapse.classList.add('show');
                    const toggle = document.querySelector(`[data-bs-target="#${id}"]`);
                    if (toggle) {
                        toggle.classList.remove('collapsed');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                }
                
                // Save state on toggle
                collapse.addEventListener('shown.bs.collapse', () => {
                    localStorage.setItem(`optima-submenu-${id}`, 'true');
                });
                
                collapse.addEventListener('hidden.bs.collapse', () => {
                    localStorage.setItem(`optima-submenu-${id}`, 'false');
                });
            });
        },
        
        handleMobileToggle: function() {
            if (window.innerWidth <= 991.98) {
                this.sidebar.classList.toggle('show');
                document.body.classList.toggle('sidebar-open');
                
                // Show/hide overlay
                const overlay = document.getElementById('sidebarOverlay');
                if (overlay) {
                    overlay.style.display = this.sidebar.classList.contains('show') ? 'block' : 'none';
                }
            }
        },
        
        hideMobileSidebar: function() {
            this.sidebar.classList.remove('show');
            document.body.classList.remove('sidebar-open');
            
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        },
        
        handleResize: function() {
            if (window.innerWidth > 991.98) {
                this.sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
                
                const overlay = document.getElementById('sidebarOverlay');
                if (overlay) {
                    overlay.style.display = 'none';
                }
            }
        }
    };

    // Status Indicators for Menu Items
    const StatusIndicators = {
        init: function() {
            this.loadStatusData();
            this.updateIndicators();
            
            // Update every 30 seconds
            setInterval(() => {
                this.loadStatusData();
                this.updateIndicators();
            }, 30000);
        },
        
        loadStatusData: function() {
            // Simulate loading status data
            // In real implementation, this would fetch from API
            this.statusData = {
                'purchasing': { count: 3, type: 'warning' },
                'warehouse': { count: 1, type: 'info' },
                'service': { count: 5, type: 'danger' }
            };
        },
        
        updateIndicators: function() {
            Object.keys(this.statusData).forEach(key => {
                const navLink = document.querySelector(`[data-bs-target="#${key}Submenu"]`);
                if (navLink && this.statusData[key].count > 0) {
                    this.addIndicator(navLink, this.statusData[key]);
                }
            });
        },
        
        addIndicator: function(element, data) {
            // Remove existing indicator
            const existing = element.querySelector('.status-indicator');
            if (existing) {
                existing.remove();
            }
            
            // Add new indicator
            const indicator = document.createElement('span');
            indicator.className = `status-indicator ${data.type}`;
            indicator.textContent = data.count;
            indicator.title = `${data.count} item(s) require attention`;
            
            element.appendChild(indicator);
        }
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // console.log('DOM Content Loaded - Starting initialization');
        
        // Add small delay to ensure all elements are rendered
        setTimeout(function() {
            EnhancedSidebar.init();
            StatusIndicators.init();
        }, 100);
    });

})();
