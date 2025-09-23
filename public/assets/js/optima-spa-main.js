/**
 * =======================================================================================
 * OPTIMA SPA - RELOAD MODE WITH SIDEBAR ENHANCEMENTS (v.2025)
 * =======================================================================================
 * Simple SPA with full page reload + active states + scroll preservation
 * =======================================================================================
 */

class OptimaSPAMain {
    constructor() {
        this.contentContainer = document.querySelector('#content-area, .content-wrapper, main');
        this.currentPath = window.location.pathname;
        this.isLoading = false;
        this.enableSPA = true;

        if (!this.contentContainer) {
            console.warn('❌ SPA: Content container not found. Using full reload mode.');
        }

        console.log('🚀 OPTIMA SPA Main System Initialized (Enhanced Reload Mode)');
        this.init();
    }

    init() {
        this.setupMenuClickListener();
        this.setupPopStateListener();
        this.setupActiveStates();
        this.preserveScrollPosition();
    }

    setupMenuClickListener() {
        document.body.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');

            if (link && this.isMenuLink(link)) {
                e.preventDefault();
                this.saveScrollPosition();
                this.navigateWithReload(link.href);
            }
        });
    }
    
    isMenuLink(link) {
        const href = link.getAttribute('href');
        
        // Skip external links, downloads, etc.
        if (!href || 
            link.hostname !== location.hostname ||
            link.target ||
            href.startsWith('#') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:') ||
            link.classList.contains('no-spa') ||
            /\.(pdf|zip|jpg|png)$/i.test(href)) {
            return false;
        }

        // Check if it's a menu/sidebar link
        const isMenuLink = link.closest('.sidebar, .nav, .navbar, .menu') || 
                          link.classList.contains('nav-link') ||
                          link.classList.contains('menu-link');

        return isMenuLink;
    }

    setupPopStateListener() {
        window.addEventListener('popstate', () => {
            // For browser back/forward, just reload to ensure data consistency
            console.log('SPA: Browser navigation detected, reloading for data consistency');
            this.saveScrollPosition();
            window.location.reload();
        });
    }

    setupActiveStates() {
        // Set active state for current page on load
        this.updateActiveStates();
        
        // Update active states after navigation
        window.addEventListener('load', () => {
            this.updateActiveStates();
            this.restoreScrollPosition();
        });
    }

    updateActiveStates() {
        const currentPath = window.location.pathname;
        
        // Remove all existing active states
        document.querySelectorAll('.nav-link.active').forEach(link => {
            link.classList.remove('active');
        });

        // Find and set active link
        const activeLink = this.findActiveLink(currentPath);
        if (activeLink) {
            activeLink.classList.add('active');
            
            // Expand parent dropdown if exists
            this.expandParentDropdown(activeLink);
            
            console.log('✅ Active state set for:', activeLink.textContent.trim());
        }
    }

    findActiveLink(currentPath) {
        // Strategy 1: Exact match
        let activeLink = document.querySelector(`a.nav-link[href="${currentPath}"]`);
        if (activeLink) return activeLink;

        // Strategy 2: Full URL match
        const fullUrl = window.location.origin + currentPath;
        activeLink = document.querySelector(`a.nav-link[href="${fullUrl}"]`);
        if (activeLink) return activeLink;

        // Strategy 3: Partial path match (for dynamic routes)
        const navLinks = document.querySelectorAll('a.nav-link[href]');
        for (const link of navLinks) {
            try {
                const linkUrl = new URL(link.href);
                if (linkUrl.pathname === currentPath) {
                    return link;
                }
            } catch (e) {
                continue;
            }
        }

        // Strategy 4: Best match for sub-paths
        let bestMatch = null;
        let bestMatchLength = 0;
        
        for (const link of navLinks) {
            try {
                const linkUrl = new URL(link.href);
                const linkPath = linkUrl.pathname;
                
                if (currentPath.startsWith(linkPath) && linkPath.length > bestMatchLength) {
                    bestMatch = link;
                    bestMatchLength = linkPath.length;
                }
            } catch (e) {
                continue;
            }
        }

        return bestMatch;
    }

    expandParentDropdown(activeLink) {
        // Check if the active link is inside a dropdown/collapse
        const parentCollapse = activeLink.closest('.collapse');
        if (parentCollapse) {
            // Show the collapse
            parentCollapse.classList.add('show');
            
            // Find the toggle button and mark it as expanded
            const toggleButton = document.querySelector(`[data-bs-target="#${parentCollapse.id}"], [href="#${parentCollapse.id}"]`);
            if (toggleButton) {
                toggleButton.setAttribute('aria-expanded', 'true');
                toggleButton.classList.remove('collapsed');
            }
        }

        // Handle other dropdown types if needed
        const parentDropdown = activeLink.closest('.dropdown-menu');
        if (parentDropdown) {
            parentDropdown.classList.add('show');
            const toggleButton = parentDropdown.previousElementSibling;
            if (toggleButton) {
                toggleButton.classList.add('show');
            }
        }
    }

    saveScrollPosition() {
        const sidebar = document.querySelector('.sidebar-nav, .sidebar, #sidebar');
        if (sidebar) {
            const scrollPos = sidebar.scrollTop;
            sessionStorage.setItem('sidebar-scroll-position', scrollPos);
            console.log('💾 Saved sidebar scroll position:', scrollPos);
        }
    }

    restoreScrollPosition() {
        const sidebar = document.querySelector('.sidebar-nav, .sidebar, #sidebar');
        if (sidebar) {
            const savedPos = sessionStorage.getItem('sidebar-scroll-position');
            if (savedPos !== null) {
                const scrollPos = parseInt(savedPos);
                
                // Use requestAnimationFrame to ensure DOM is ready
                requestAnimationFrame(() => {
                    sidebar.scrollTop = scrollPos;
                    console.log('📜 Restored sidebar scroll position:', scrollPos);
                });
            }
        }
    }

    preserveScrollPosition() {
        // Auto-scroll to active item if it's out of view
        setTimeout(() => {
            this.scrollToActiveItem();
        }, 100);
    }

    scrollToActiveItem() {
        const activeLink = document.querySelector('.nav-link.active');
        const sidebar = document.querySelector('.sidebar-nav, .sidebar, #sidebar');
        
        if (activeLink && sidebar) {
            const linkRect = activeLink.getBoundingClientRect();
            const sidebarRect = sidebar.getBoundingClientRect();
            
            // Check if link is outside visible area
            if (linkRect.top < sidebarRect.top || linkRect.bottom > sidebarRect.bottom) {
                activeLink.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                console.log('📍 Scrolled to active menu item');
            }
        }
    }

    navigateWithReload(url) {
        if (this.isLoading) {
            return;
        }

        this.isLoading = true;
        this.showLoadingIndicator();
        
        console.log(`SPA: Navigating to ${url} with reload mode`);
        
        // Small delay to show loading indicator, then reload
        setTimeout(() => {
            window.location.href = url;
        }, 200);
    }

    showLoadingIndicator() {
        let indicator = document.getElementById('spa-loading-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'spa-loading-indicator';
            indicator.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: linear-gradient(90deg, #0d6efd 0%, #20c997 50%, #0d6efd 100%);
                background-size: 200% 100%;
                animation: loading-wave 1s ease-in-out infinite;
                z-index: 9999;
            `;
            
            // Add animation keyframes
            if (!document.getElementById('spa-loading-style')) {
                const style = document.createElement('style');
                style.id = 'spa-loading-style';
                style.textContent = `
                    @keyframes loading-wave {
                        0% { background-position: 200% 0; }
                        100% { background-position: -200% 0; }
                    }
                    
                    /* Enhanced active state styles */
                    .nav-link.active {
                        background: linear-gradient(135deg, rgba(0, 97, 242, 0.1) 0%, rgba(77, 140, 255, 0.1) 100%) !important;
                        border-left: 3px solid #0061f2 !important;
                        color: #0061f2 !important;
                        font-weight: 600 !important;
                        position: relative;
                    }
                    
                    .nav-link.active::after {
                        content: '';
                        position: absolute;
                        right: 1rem;
                        top: 50%;
                        transform: translateY(-50%);
                        width: 8px;
                        height: 8px;
                        background: #0061f2;
                        border-radius: 50%;
                        opacity: 0.8;
                    }
                    
                    .nav-link.active .nav-link-text {
                        color: #0061f2 !important;
                    }
                    
                    .nav-link.active i {
                        color: #0061f2 !important;
                    }
                `;
                document.head.appendChild(style);
            }
            
            document.body.appendChild(indicator);
        }
        indicator.style.display = 'block';
    }

    hideLoadingIndicator() {
        const indicator = document.getElementById('spa-loading-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
}

// Global instantiation
if (typeof window.optimaSPA === 'undefined') {
    window.optimaSPA = new OptimaSPAMain();
} else {
    console.log('📋 OptimaSPAMain already running.');
}
