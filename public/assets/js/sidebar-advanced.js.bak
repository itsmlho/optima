/**
 * OPTIMA Sidebar Enhancement - MINIMAL & CLEAN VERSION
 * Only essential functionality to prevent conflicts
 */

// CRITICAL: Prevent script duplication at global level
if (window.OPTIMA_SIDEBAR_INITIALIZED) {
    console.log('⏭️ OptimaSidebar already initialized, skipping duplicate initialization');
    // Exit early to prevent duplicate initialization
    throw new Error('OptimaSidebar already initialized');
}

// Mark as initialized
window.OPTIMA_SIDEBAR_INITIALIZED = true;

class OptimaSidebar {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.scrollPosition = 0;
        
        // Only initialize if sidebar exists and not already initialized
        if (this.sidebar && !this.sidebar.dataset.optimaInitialized) {
            this.init();
        }
    }
    
    init() {
        // Mark as initialized to prevent re-initialization
        this.sidebar.dataset.optimaInitialized = 'true';
        
        this.setupScrollMemory();
        this.addVisualEffects();
        
        console.log('OPTIMA Advanced Sidebar initialized');
    }
    
    setupScrollMemory() {
        if (!this.sidebar) return;
        
        // Save scroll position on scroll (debounced)
        this.sidebar.addEventListener('scroll', this.debounce(() => {
            this.saveScrollPosition();
        }, 150));
        
        // Save before page unload
        window.addEventListener('beforeunload', () => {
            this.saveScrollPosition();
        });
        
        // Restore scroll position
        this.restoreScrollPosition();
    }
    
    saveScrollPosition() {
        if (this.sidebar) {
            const position = this.sidebar.scrollTop;
            localStorage.setItem('optima_sidebar_scroll', position);
            this.scrollPosition = position;
        }
    }
    
    restoreScrollPosition() {
        if (!this.sidebar) return;
        
        // Restore immediately if available
        const savedPosition = localStorage.getItem('optima_sidebar_scroll');
        if (savedPosition) {
            this.sidebar.scrollTop = parseInt(savedPosition);
        }
        
        // Also restore after a short delay to ensure DOM is ready
        setTimeout(() => {
            const savedPos = localStorage.getItem('optima_sidebar_scroll');
            if (savedPos && this.sidebar) {
                this.sidebar.scrollTop = parseInt(savedPos);
            }
        }, 250);
    }
    
    addVisualEffects() {
        if (!this.sidebar) return;
        
        // Add smooth scrolling
        this.sidebar.style.scrollBehavior = 'smooth';
        
        // Add CSS animations only if not already added
        this.injectKeyframes();
    }
    
    injectKeyframes() {
        if (document.getElementById('sidebar-keyframes')) return;
        
        const style = document.createElement('style');
        style.id = 'sidebar-keyframes';
        style.textContent = `
            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            .nav-item {
                animation: slideInLeft 0.3s ease-out forwards;
            }
            
            .nav-item:nth-child(1) { animation-delay: 0.1s; }
            .nav-item:nth-child(2) { animation-delay: 0.15s; }
            .nav-item:nth-child(3) { animation-delay: 0.2s; }
            .nav-item:nth-child(4) { animation-delay: 0.25s; }
            .nav-item:nth-child(5) { animation-delay: 0.3s; }
        `;
        
        document.head.appendChild(style);
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
    scrollToTop() {
        if (this.sidebar) {
            this.sidebar.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    }
    
    scrollToActive() {
        if (this.sidebar) {
            const activeLink = this.sidebar.querySelector('.nav-link.active');
            if (activeLink) {
                activeLink.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }
    }
}

// Auto-initialize when DOM is ready (only once)
document.addEventListener('DOMContentLoaded', function() {
    // Check if already initialized
    if (window.optimaSidebar) {
        console.log('⏭️ OptimaSidebar already exists, skipping initialization');
        return;
    }
    
    // Initialize after a brief delay to ensure all elements are ready
    setTimeout(() => {
        try {
            window.optimaSidebar = new OptimaSidebar();
        } catch (error) {
            console.warn('OptimaSidebar initialization failed:', error);
        }
    }, 100);
});

// Global functions for external access (with safety checks)
window.saveSidebarScrollPosition = function() {
    if (window.optimaSidebar && window.optimaSidebar.saveScrollPosition) {
        window.optimaSidebar.saveScrollPosition();
    }
};

window.restoreSidebarScrollPosition = function() {
    if (window.optimaSidebar && window.optimaSidebar.restoreScrollPosition) {
        window.optimaSidebar.restoreScrollPosition();
    }
};

window.scrollSidebarToTop = function() {
    if (window.optimaSidebar && window.optimaSidebar.scrollToTop) {
        window.optimaSidebar.scrollToTop();
    }
};

window.scrollSidebarToActive = function() {
    if (window.optimaSidebar && window.optimaSidebar.scrollToActive) {
        window.optimaSidebar.scrollToActive();
    }
};