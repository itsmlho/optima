/**
 * ============================================================================
 * OPTIMA NOTIFICATION SYSTEM - LIGHTWEIGHT CLIENT (CORRECTED)
 * ============================================================================
 * Simple, efficient notification system using polling
 * No SSE complexity, no infinite loops, battery-friendly
 * ============================================================================
 */

class OptimaNotificationLightweight {
    constructor() {
        // Get base URL properly - extract everything before the route
        const path = window.location.pathname;
        
        // Check if URL has index.php
        if (path.includes('/index.php/')) {
            // Split by /index.php/ and take the first part + /index.php
            const indexPhpPosition = path.indexOf('/index.php/');
            const basePath = path.substring(0, indexPhpPosition);
            this.baseUrl = window.location.origin + basePath + '/index.php';
        } else if (path.includes('/public/')) {
            // If no index.php but has /public/, use up to /public
            const publicPosition = path.indexOf('/public/');
            const basePath = path.substring(0, publicPosition);
            this.baseUrl = window.location.origin + basePath + '/public/index.php';
        } else {
            // Fallback: use origin
            this.baseUrl = window.location.origin + '/index.php';
        }
        
        // BaseURL configured: this.baseUrl
        
        this.pollingInterval = 60000; // 60 seconds (reduced frequency for performance)
        this.pollingTimer = null;
        this.isPolling = false;
        this.lastNotificationId = 0;
        
        // DOM elements
        this.badge = null;
        this.dropdownMenu = null;
        
        // Disable sound by default for performance
        this.enableSound = false;
        
        // Reduced queue size
        this.notificationQueue = [];
        this.isShowingNotification = false;
        
        // Simplified popup tracking (in-memory only for performance)
        this.shownPopupIds = new Set();
        this.storageAvailable = this.checkStorageAvailability();
        
        this.init();
    }

    checkStorageAvailability() {
        try {
            const testKey = '__optima_notification_storage_test__';
            window.localStorage.setItem(testKey, '1');
            window.localStorage.removeItem(testKey);
            return true;
        } catch (error) {
            return false;
        }
    }
    
    /**
     * Load popup IDs that have already been shown to user
     * Uses localStorage to persist across page reloads/logins
     */
    loadShownPopups() {
        if (!this.storageAvailable) {
            this.shownPopupIds = new Set();
            return;
        }

        try {
            const stored = localStorage.getItem('optima_shown_notification_popups');
            if (stored) {
                const ids = JSON.parse(stored);
                this.shownPopupIds = new Set(ids);
                console.log(`✅ Loaded ${this.shownPopupIds.size} shown notification IDs from storage`);
            } else {
                this.shownPopupIds = new Set();
            }
        } catch (error) {
            console.warn('⚠️ Failed to load shown popups:', error);
            this.shownPopupIds = new Set();
        }
    }
    
    /**
     * Save popup IDs to localStorage
     * Prevents re-showing alerts on reload/login
     */
    saveShownPopups() {
        if (!this.storageAvailable) {
            return;
        }

        try {
            const ids = Array.from(this.shownPopupIds);
            // Keep only last 100 notification IDs to prevent localStorage bloat
            const recentIds = ids.slice(-100);
            localStorage.setItem('optima_shown_notification_popups', JSON.stringify(recentIds));
            this.shownPopupIds = new Set(recentIds);
        } catch (error) {
            console.warn('⚠️ Failed to save shown popups:', error);
        }
    }
    
    init() {
        // Load shown popup IDs from localStorage
        this.loadShownPopups();
        
        // Cache DOM elements
        this.badge = document.getElementById('notificationBadge');
        this.dropdownMenu = document.getElementById('notificationDropdownMenu');
        
        // DOM elements cached
        
        // Update count immediately
        this.updateCount();
        
        // Check for new notifications immediately (on page load)
        setTimeout(() => {
            this.pollForNotifications();
        }, 500); // Faster initialization - 0.5 seconds
        
        // Start regular polling
        this.startPolling();
        
        // Update when notification dropdown is opened (more specific selector)
        // Find the parent div of the notification dropdown
        const notificationDropdownParent = this.dropdownMenu?.closest('.dropdown');
        if (notificationDropdownParent) {
            const notificationButton = notificationDropdownParent.querySelector('[data-bs-toggle="dropdown"]');
            if (notificationButton) {
                notificationButton.addEventListener('click', () => {
                    this.updateCount();
                    this.fetchRecent();
                });
                
                // Also listen to Bootstrap's show event
                this.dropdownMenu.addEventListener('show.bs.dropdown', () => {
                    this.fetchRecent();
                });
            } else {
                console.warn('⚠️ Notification dropdown button not found');
            }
        } else {
            console.warn('⚠️ Notification dropdown parent not found');
        }
    }
    
    /**
     * Sound functionality removed for performance
     */
    initSound() {
        // Sound disabled for performance optimization
    }
    
    /**
     * Sound functionality removed for performance
     */
    playSound(notification) {
        // Sound disabled for performance optimization
    }
    
    /**
     * Sound functionality removed for performance
     */
    toggleSound() {
        // Sound disabled for performance optimization
    }
    
    /**
     * Process notification queue (show SweetAlert one by one)
     */
    async processNotificationQueue() {
        // If already showing a notification, return
        if (this.isShowingNotification || this.notificationQueue.length === 0) {
            return;
        }
        
        // Get next notification from queue
        const notification = this.notificationQueue.shift();
        this.isShowingNotification = true;
        
        // Mark this notification as shown (prevent duplicate popup)
        this.shownPopupIds.add(notification.id);
        
        // Save to localStorage to persist across page reloads
        this.saveShownPopups();
        
        // Show notification popup
        await this.showNotificationPopup(notification);
        
        // After popup is dismissed, decrement badge locally (avoid extra HTTP request per popup)
        this._decrementBadge();
        this.animateBellIcon();
        
        // Mark as showing in queue
        this.isShowingNotification = false;
        
        // Process next notification if any (with delay)
        if (this.notificationQueue.length > 0) {
            setTimeout(() => {
                this.processNotificationQueue();
            }, 500); // 500ms delay between notifications
        }
    }
    
    /**
     * Animate bell icon when new notification arrives
     */
    animateBellIcon() {
        const bellIcon = document.querySelector('.nav-link i.fa-bell');
        const badge = this.badge;
        
        if (bellIcon) {
            // Ring animation
            bellIcon.classList.add('notification-bell-ring');
            setTimeout(() => {
                bellIcon.classList.remove('notification-bell-ring');
            }, 500);
        }
        
        if (badge) {
            // Pulse animation
            badge.classList.add('notification-badge-pulse');
            setTimeout(() => {
                badge.classList.remove('notification-badge-pulse');
            }, 1800); // 3 pulses (0.6s * 3)
        }
    }
    
    /**
     * Show notification popup with Bootstrap 5 Toast (READ-ONLY)
     * No action buttons, no redirect — just informational toast.
     */
    showNotificationPopup(notification) {
        return new Promise((resolve) => {
            // Determine type and icon
            let type = 'info';
            
            switch(notification.type) {
                case 'success':
                    type = 'success';
                    break;
                case 'warning':
                    type = 'warning';
                    break;
                case 'error':
                case 'critical':
                    type = 'danger';
                    break;
                default:
                    type = 'info';
            }
            
            // Clean up title and message - remove HTML tags
            const titleText = (notification.title || 'Notification')
                .replace(/<[^>]*>/g, '')
                .replace(/\s+/g, ' ')
                .trim();
            
            const messageText = (notification.message || '')
                .replace(/<[^>]*>/g, '')
                .replace(/\s+/g, ' ')
                .trim();
            
            // Use Bootstrap 5 Toast in read-only mode (no action buttons, no redirect)
            if (window.createOptimaToast) {
                window.createOptimaToast({
                    type: type,
                    title: titleText,
                    message: messageText,
                    duration: 6000,
                    timestamp: notification.created_at || notification.timestamp || null
                });
                
                // Resolve after a short delay
                setTimeout(() => resolve(), 500);
            } else {
                // Fallback to SweetAlert2 if Bootstrap toast not available
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                        
                        if (notification.url) {
                            toast.style.cursor = 'pointer';
                            toast.addEventListener('click', () => {
                                window.location.href = notification.url;
                            });
                        }
                    },
                    willClose: () => {
                        resolve();
                    }
                });
                
                toast.fire({
                    icon: type === 'danger' ? 'error' : type,
                    title: titleText,
                    text: messageText
                });
            }
        });
    }
    
    startPolling() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        
        this.pollingTimer = setInterval(() => {
            this.pollForNotifications();
        }, this.pollingInterval);
    }
    
    stopPolling() {
        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
            this.pollingTimer = null;
        }
        this.isPolling = false;
    }
    
    /**
     * Trigger immediate check (call this after CRUD operations)
     */
    triggerImmediateCheck() {
        this.pollForNotifications();
    }
    
    async pollForNotifications() {
        try {
            const response = await fetch(`${this.baseUrl}/notifications/poll?lastId=${this.lastNotificationId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                console.warn('⚠️ Polling failed:', response.status);
                return;
            }
            
            const data = await response.json();
            
            if (data.success && data.notifications) {
                // Update last notification ID
                if (data.lastId > this.lastNotificationId) {
                    this.lastNotificationId = data.lastId;
                }
                
                // Process new notifications
                if (data.notifications.length > 0) {
                    // Update bell icon count and animate (always, regardless of popup display)
                    this.updateCount();
                    this.animateBellIcon();
                    
                    // Filter out notifications that have already been shown as popup
                    const newNotifications = data.notifications.filter(n => {
                        return !this.shownPopupIds.has(n.id);
                    });
                    
                    // Add to queue only new notifications (not shown as popup yet)
                    newNotifications.forEach(notification => {
                        this.notificationQueue.push(notification);
                    });
                    
                    // Process queue (show popups for new notifications only)
                    if (newNotifications.length > 0) {
                        this.processNotificationQueue();
                    }
                    
                    // Update dropdown if open
                    if (this.dropdownMenu && this.dropdownMenu.classList.contains('show')) {
                        this.fetchRecent();
                    }
                }
            }
            
        } catch (error) {
            console.warn('⚠️ Polling error:', error.message);
        }
    }
    
    /**
     * Decrement the badge count locally by 1 (no HTTP request).
     * Used after a notification popup is dismissed.
     */
    _decrementBadge() {
        const current = parseInt(this.badge?.textContent || '0', 10);
        const next = Math.max(0, current - 1);
        if (this.badge) {
            this.badge.textContent = next;
            this.badge.style.display = next > 0 ? 'inline-block' : 'none';
        }
        const sidebarBadge = document.getElementById('sidebarNotificationCount');
        if (sidebarBadge) {
            sidebarBadge.textContent = next;
            sidebarBadge.style.display = next > 0 ? 'inline' : 'none';
        }
    }

    async updateCount() {
        try {
            const response = await fetch(`${this.baseUrl}/notifications/count`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                console.warn('⚠️ Count update failed:', response.status);
                return;
            }
            
            const data = await response.json();
            const count = data.count || 0;
            
            // Update badge
            if (this.badge) {
                this.badge.textContent = count;
                this.badge.style.display = count > 0 ? 'inline-block' : 'none';
            }
            
            // Update sidebar badge
            const sidebarBadge = document.getElementById('sidebarNotificationCount');
            if (sidebarBadge) {
                sidebarBadge.textContent = count;
                sidebarBadge.style.display = count > 0 ? 'inline' : 'none';
            }
            
        } catch (error) {
            console.warn('⚠️ Count update error:', error.message);
        }
    }
    
    async fetchRecent() {
        try {
            // Fetching notifications
            
            const response = await fetch(`${this.baseUrl}/notifications/get?limit=5`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            // Response received
            
            if (!response.ok) {
                console.warn('⚠️ Fetch recent failed:', response.status, response.statusText);
                this.showErrorInDropdown('Gagal memuat notifikasi');
                return;
            }
            
            const data = await response.json();
            // Data received
            
            if (data.success && data.notifications) {
                // Updating dropdown
                // Update dropdown content
                this.updateDropdownContent(data.notifications); 
            } else {
                console.warn('⚠️ Invalid response format:', data);
                this.showErrorInDropdown('Format response tidak valid');
            }
            
        } catch (error) {
            console.error('❌ Fetch recent error:', error);
            this.showErrorInDropdown('Terjadi kesalahan: ' + error.message);
        }
    }
    
    showErrorInDropdown(message) {
        if (!this.dropdownMenu) return;
        
        const existingItems = this.dropdownMenu.querySelectorAll('.notification-item');
        existingItems.forEach(item => item.remove());
        
        const errorHtml = `
            <li class="notification-item">
                <div class="text-center py-3">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 small mt-2">${message}</p>
                </div>
            </li>
        `;
        
        const divider = this.dropdownMenu.querySelector('hr.dropdown-divider:last-of-type');
        if (divider) {
            divider.insertAdjacentHTML('beforebegin', errorHtml);
        }
    }
    
    updateDropdownContent(notifications) {
        if (!this.dropdownMenu) return;
        
        // Remove ALL existing notification items
        const existingItems = this.dropdownMenu.querySelectorAll('.notification-item');
        existingItems.forEach(item => item.remove());
        
        // Create notification items
        let html = '';
        if (notifications.length === 0) {
            html = `
                <li class="notification-item">
                    <div class="text-center py-3">
                        <i class="fas fa-bell-slash text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0 small mt-2">Tidak ada notifikasi</p>
                    </div>
                </li>
            `;
        } else {
            notifications.forEach(notification => {
                const isRead = notification.is_read == '1';
                const readClass = isRead ? '' : 'unread';
                const unreadDot = isRead ? '' : '<span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="font-size: 0.5rem;"></span>';

                // Check notification style - info_only means no click/redirect
                const isInfoOnly = notification.notification_style === 'info_only';

                // For info_only, use a div instead of button, no onclick handler
                const clickElement = isInfoOnly
                    ? `<div class="dropdown-item text-start" style="cursor: default; pointer-events: none;">`
                    : `<button type="button" class="dropdown-item text-start" onclick="handleNotificationClick(${notification.id})">`;

                const closeElement = isInfoOnly ? '</div>' : '</button>';

                html += `
                    <li class="notification-item ${readClass} ${isInfoOnly ? 'info-only' : ''}" data-id="${notification.id}" data-read="${notification.is_read}" data-style="${notification.notification_style || 'link'}">
                        ${clickElement}
                            <div class="d-flex align-items-start position-relative">
                                <i class="fas fa-${this.getNotificationIcon(notification.type)} text-${this.getNotificationColor(notification.type)} me-2 mt-1"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">${notification.title}</div>
                                    <div class="text-muted small">${notification.message}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">${this.formatDate(notification.created_at)}</div>
                                </div>
                                ${unreadDot}
                            </div>
                        ${closeElement}
                    </li>
                `;
            });
        }
        
        // Insert before the divider and "Lihat Semua" link
        const divider = this.dropdownMenu.querySelector('.dropdown-divider');
        if (divider) {
            divider.insertAdjacentHTML('beforebegin', html);
        }
        
        // FORCE TEXT WRAPPING - Apply styles after DOM insertion
        setTimeout(() => {
            const notificationItems = this.dropdownMenu.querySelectorAll('.notification-item button.dropdown-item');
            notificationItems.forEach(item => {
                const allDivs = item.querySelectorAll('div');
                allDivs.forEach(div => {
                    div.style.cssText = 'display: block !important; white-space: normal !important; word-wrap: break-word !important; word-break: break-word !important; max-width: 100% !important; overflow: visible !important; text-overflow: clip !important;';
                });
            });
        }, 10);
    }
    
    // Deprecated: URL-based navigation removed for read-only notifications
    getNotificationUrl(notification) {
        return `${this.baseUrl}/notifications`;
    }
    
    getNotificationIcon(type) {
        const icons = {
            'info': 'info-circle',
            'success': 'check-circle',
            'warning': 'exclamation-triangle',
            'error': 'times-circle',
            'critical': 'exclamation-circle'
        };
        return icons[type] || 'bell';
    }
    
    getNotificationColor(type) {
        const colors = {
            'info': 'primary',
            'success': 'success',
            'warning': 'warning',
            'error': 'danger',
            'critical': 'danger'
        };
        return colors[type] || 'primary';
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'Baru saja';
        if (diff < 3600000) return `${Math.floor(diff / 60000)} menit lalu`;
        if (diff < 86400000) return `${Math.floor(diff / 3600000)} jam lalu`;
        if (diff < 604800000) return `${Math.floor(diff / 86400000)} hari lalu`;
        
        return date.toLocaleDateString('id-ID');
    }
    
    showToast(notification) {
        // Simple toast notification
        const iconType = this.getIconType(notification.type);
        const message = notification.message || '';
        const title = notification.title || 'Notifikasi';

        if (window.OptimaNotify && typeof window.OptimaNotify[iconType] === 'function') {
            window.OptimaNotify[iconType](message, title);
            return;
        }

        // Fallback to browser notification
        if (Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '/optima/public/assets/img/logo.png'
            });
        }
    }
    
    /**
     * Show simple toast for system messages
     */
    showSimpleToast(message, type = 'info') {
        if (window.OptimaNotify && typeof window.OptimaNotify[type] === 'function') {
            window.OptimaNotify[type](message);
        }
    }
    
    getIconType(type) {
        const iconMap = {
            'success': 'success',
            'warning': 'warning',
            'error': 'error',
            'info': 'info'
        };
        return iconMap[type] || 'info';
    }
    
    markAsRead(notificationId) {
        fetch(`${this.baseUrl}/notifications/mark-as-read/${notificationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                // Update local state
                const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('unread');
                    notificationItem.setAttribute('data-read', '1');
                    // Remove unread dot
                    const unreadDot = notificationItem.querySelector('.position-absolute');
                    if (unreadDot) {
                        unreadDot.remove();
                    }
                }
                
                // Update count
                this.updateCount();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    markAllAsRead() {
        fetch(`${this.baseUrl}/notifications/mark-all-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                // Update all notification items
                const notificationItems = document.querySelectorAll('.notification-item');
                notificationItems.forEach(item => {
                    item.classList.remove('unread');
                    item.setAttribute('data-read', '1');
                    const unreadDot = item.querySelector('.position-absolute');
                    if (unreadDot) {
                        unreadDot.remove();
                    }
                });
                
                // Update count
                this.updateCount();
                
                // All notifications marked as read
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }
    
    destroy() {
        this.stopPolling();
        // Notification client destroyed
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize lightweight notification client
    window.optimaNotification = new OptimaNotificationLightweight();
    
    // Expose for compatibility with existing code
    window.updateNotificationCount = function() {
        if (window.optimaNotification) {
            window.optimaNotification.updateCount();
        }
    };
    
    window.fetchRecentNotifications = function() {
        if (window.optimaNotification) {
            window.optimaNotification.fetchRecent();
        }
    };
    
    // Global functions for notification actions
    window.markAsRead = function(notificationId) {
        if (window.optimaNotification) {
            window.optimaNotification.markAsRead(notificationId);
        }
    };
    
    window.markAllAsRead = function() {
        // Global markAllAsRead called
        
        if (window.optimaNotification && typeof window.optimaNotification.markAllAsRead === 'function') {
            window.optimaNotification.markAllAsRead();
        } else {
            console.error('❌ optimaNotification.markAllAsRead is not a function');
        }
    };
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.optimaNotification) {
        window.optimaNotification.destroy();
    }
});

/**
 * Global function to trigger immediate notification check
 * Call this after CRUD operations to get instant notifications
 */
window.checkNotificationsNow = function() {
    if (window.optimaNotification && typeof window.optimaNotification.triggerImmediateCheck === 'function') {
        window.optimaNotification.triggerImmediateCheck();
    } else {
        console.warn('Notification client not ready yet');
    }
};
