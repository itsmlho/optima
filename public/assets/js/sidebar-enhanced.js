/*!
 * OPTIMA Pro Enhanced Sidebar JavaScript
 * Enhanced functionality for better user experience
 * PT Sarana Mitra Luas Tbk - OPTIMA System v2.0
 */

(function() {
    'use strict';

    // Enhanced Sidebar Search Functionality
    const SidebarSearch = {
        initialized: false,
        searchInput: null,
        searchResults: null,
        menuItems: [],
        
        init: function() {
            if (this.initialized) return;
            
            this.searchInput = document.getElementById('sidebarSearch');
            this.searchResults = document.getElementById('searchResults');
            
            console.log('Search elements found:', {
                searchInput: !!this.searchInput,
                searchResults: !!this.searchResults
            });
            
            if (!this.searchInput || !this.searchResults) {
                console.error('Search elements not found!');
                return;
            }
            
            this.cacheMenuItems();
            this.bindEvents();
            this.initialized = true;
            
            console.log('Sidebar Search initialized successfully');
        },
        
        cacheMenuItems: function() {
            const menuLinks = document.querySelectorAll('.sidebar .nav-link');
            this.menuItems = [];
            
            menuLinks.forEach(link => {
                const text = link.textContent.trim();
                const href = link.getAttribute('href');
                const searchTerms = link.getAttribute('data-search-terms') || '';
                const icon = link.querySelector('i');
                const iconClass = icon ? icon.className : '';
                
                // Skip collapse triggers without href
                if (!href || href === '#') return;
                
                // Build breadcrumb path
                let breadcrumb = '';
                const parentSubmenu = link.closest('.collapse');
                if (parentSubmenu) {
                    const parentToggle = document.querySelector(`[data-bs-target="#${parentSubmenu.id}"]`);
                    if (parentToggle) {
                        const parentText = parentToggle.querySelector('.nav-link-text');
                        if (parentText) {
                            breadcrumb = parentText.textContent.trim() + ' > ';
                        }
                    }
                }
                
                this.menuItems.push({
                    text: text,
                    href: href,
                    searchTerms: searchTerms.toLowerCase(),
                    iconClass: iconClass,
                    breadcrumb: breadcrumb,
                    element: link
                });
            });
        },
        
        bindEvents: function() {
            // Search input events
            this.searchInput.addEventListener('input', this.handleSearch.bind(this));
            this.searchInput.addEventListener('focus', this.handleFocus.bind(this));
            this.searchInput.addEventListener('blur', this.handleBlur.bind(this));
            
            // Keyboard navigation
            this.searchInput.addEventListener('keydown', this.handleKeydown.bind(this));
            
            // Click outside to close
            document.addEventListener('click', this.handleOutsideClick.bind(this));
        },
        
        handleSearch: function(e) {
            const query = e.target.value.toLowerCase().trim();
            console.log('Search input detected:', query);
            
            if (query.length === 0) {
                this.hideResults();
                return;
            }
            
            if (query.length < 2) {
                return; // Minimum 2 characters
            }
            
            const results = this.searchMenuItems(query);
            console.log('Search results:', results.length);
            this.displayResults(results);
        },
        
        searchMenuItems: function(query) {
            return this.menuItems.filter(item => {
                const textMatch = item.text.toLowerCase().includes(query);
                const termMatch = item.searchTerms.includes(query);
                const breadcrumbMatch = item.breadcrumb.toLowerCase().includes(query);
                
                return textMatch || termMatch || breadcrumbMatch;
            }).slice(0, 8); // Limit to 8 results
        },
        
        displayResults: function(results) {
            if (results.length === 0) {
                this.searchResults.innerHTML = '<div class="search-result-item">Tidak ada hasil ditemukan</div>';
                this.showResults();
                return;
            }
            
            const html = results.map((item, index) => `
                <a href="${item.href}" class="search-result-item" data-index="${index}">
                    <i class="${item.iconClass}" style="margin-right: 0.5rem; width: 1rem;"></i>
                    ${item.text}
                    ${item.breadcrumb ? `<div class="search-result-path">${item.breadcrumb}${item.text}</div>` : ''}
                </a>
            `).join('');
            
            this.searchResults.innerHTML = html;
            this.showResults();
        },
        
        showResults: function() {
            this.searchResults.classList.remove('d-none');
            // Add class to sidebar for overflow management
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.add('search-active');
            }
        },
        
        hideResults: function() {
            this.searchResults.classList.add('d-none');
            // Remove class from sidebar
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.remove('search-active');
            }
        },
        
        handleFocus: function() {
            if (this.searchInput.value.trim().length >= 2) {
                this.showResults();
            }
        },
        
        handleBlur: function() {
            // Delay hiding to allow clicks on results
            setTimeout(() => {
                this.hideResults();
            }, 150);
        },
        
        handleKeydown: function(e) {
            const results = this.searchResults.querySelectorAll('.search-result-item');
            
            if (results.length === 0) return;
            
            const currentIndex = parseInt(document.querySelector('.search-result-item.highlighted')?.getAttribute('data-index') || -1);
            let newIndex = currentIndex;
            
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    newIndex = Math.min(currentIndex + 1, results.length - 1);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    newIndex = Math.max(currentIndex - 1, 0);
                    break;
                case 'Enter':
                    e.preventDefault();
                    const highlighted = document.querySelector('.search-result-item.highlighted');
                    if (highlighted) {
                        window.location.href = highlighted.href;
                    } else if (results.length > 0) {
                        window.location.href = results[0].href;
                    }
                    return;
                case 'Escape':
                    this.hideResults();
                    this.searchInput.blur();
                    return;
            }
            
            // Update highlighting
            results.forEach((item, index) => {
                item.classList.toggle('highlighted', index === newIndex);
            });
        },
        
        handleOutsideClick: function(e) {
            if (!this.searchInput.contains(e.target) && !this.searchResults.contains(e.target)) {
                this.hideResults();
            }
        }
    };

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
            
            console.log('Enhanced Sidebar initialized');
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
        console.log('DOM Content Loaded - Starting initialization');
        
        // Add small delay to ensure all elements are rendered
        setTimeout(function() {
            SidebarSearch.init();
            EnhancedSidebar.init();
            StatusIndicators.init();
            
            // Additional debugging
            const searchInput = document.getElementById('sidebarSearch');
            if (searchInput) {
                console.log('Search input found and ready for testing');
                
                // Test basic event binding manually
                searchInput.addEventListener('input', function(e) {
                    console.log('Direct event listener - Input detected:', e.target.value);
                });
                
            } else {
                console.error('Search input not found after DOM ready!');
            }
        }, 100);
    });

    // Global functions for backward compatibility
    window.toggleSidebarSearch = function() {
        const searchInput = document.getElementById('sidebarSearch');
        if (searchInput) {
            searchInput.focus();
        }
    };

})();
