/*!
 * OPTIMA Sidebar Scroll Management
 * Maintains sidebar scroll position during navigation
 * PT Sarana Mitra Luas Tbk - OPTIMA System
 */

(function() {
    'use strict';
    
    const STORAGE_KEY = 'optima_sidebar_scroll_position';
    
    // Save sidebar scroll position
    function saveSidebarScrollPosition() {
        const sidebar = document.querySelector('.sidebar-nav');
        if (sidebar) {
            sessionStorage.setItem(STORAGE_KEY, sidebar.scrollTop.toString());
            console.log('Sidebar scroll position saved:', sidebar.scrollTop);
        }
    }
    
    // Restore sidebar scroll position
    function restoreSidebarScrollPosition() {
        const sidebar = document.querySelector('.sidebar-nav');
        const savedPosition = sessionStorage.getItem(STORAGE_KEY);
        
        if (sidebar && savedPosition !== null) {
            const position = parseInt(savedPosition);
            sidebar.scrollTop = position;
            console.log('Sidebar scroll position restored:', position);
        }
    }
    
    // Save position when clicking navigation links
    function attachScrollSaveListeners() {
        document.addEventListener('click', function(e) {
            // Check if clicked element is a navigation link that will cause page navigation
            const navLink = e.target.closest('.sidebar-nav .nav-link');
            
            if (navLink) {
                const href = navLink.getAttribute('href');
                
                // Only save position for actual page navigation (not dropdown toggles)
                if (href && 
                    !href.startsWith('#') && 
                    !navLink.hasAttribute('data-bs-toggle') &&
                    !e.defaultPrevented) {
                    
                    saveSidebarScrollPosition();
                    console.log('Navigation detected, saving scroll position for:', href);
                }
            }
        });
    }
    
    // Initialize when DOM is ready
    function initializeSidebarScroll() {
        // Restore position immediately
        restoreSidebarScrollPosition();
        
        // Attach event listeners
        attachScrollSaveListeners();
        
        // Also save position before page unload as a backup
        window.addEventListener('beforeunload', saveSidebarScrollPosition);
        
        console.log('Sidebar scroll management initialized');
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeSidebarScroll);
    } else {
        // DOM already loaded
        initializeSidebarScroll();
    }
    
    // Expose functions globally for debugging
    window.OptimaSidebar = {
        save: saveSidebarScrollPosition,
        restore: restoreSidebarScrollPosition,
        getPosition: function() {
            const sidebar = document.querySelector('.sidebar-nav');
            return sidebar ? sidebar.scrollTop : null;
        },
        setPosition: function(position) {
            const sidebar = document.querySelector('.sidebar-nav');
            if (sidebar) {
                sidebar.scrollTop = position;
            }
        }
    };
    
})();