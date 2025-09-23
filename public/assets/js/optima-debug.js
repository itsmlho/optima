/**
 * OPTIMA SPA Debug Helper
 * Simple debugging utilities untuk production
 */

// Debug function untuk console
window.debugOptimaSPA = function() {
    
    if (window.optimaSPA) {
        
        // Check active link
        const activeLink = document.querySelector('.nav-link.active');
        if (activeLink) {
        } else {
        }
        
        // Check all nav links
        const allLinks = document.querySelectorAll('.nav-link[href]');
    }
    
};

// Quick test function
window.testSPANavigation = function(url = '/dashboard') {
    
    if (window.optimaSPA) {
        window.optimaSPA.navigateTo(url);
    } else {
        console.error('❌ SPA not initialized');
    }
};

// Quick refresh function
window.refreshSPAPage = function() {
    
    if (window.optimaSPA) {
        window.optimaSPA.refreshCurrentPage();
    } else {
        console.error('❌ SPA not initialized');
    }
};

// Check if everything is loaded
window.checkSPAStatus = function() {
    const status = {
        spaInitialized: !!window.optimaSPA,
        spaUnifiedLoaded: window.OPTIMA_SPA_UNIFIED_INITIALIZED,
        dataRefreshLoaded: !!window.OptimaDataRefresh,
        sidebar: !!document.getElementById('sidebar'),
        mainContent: !!document.querySelector('.content-body')
    };
    
    
    const allGood = Object.values(status).every(v => v === true);
    
    return status;
};

