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
        
        console.log('🔗 Notification System BaseURL:', this.baseUrl);
        console.log('📍 Current pathname:', path);
        
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
        
        console.log('🚀 Optima Notification Lightweight Client initialized');
        this.init();
    }
    
    /**
     * Simplified popup tracking (removed localStorage for performance)
     */
    loadShownPopups() {
        // No localStorage operations for better performance
        // Reset every session
        this.shownPopupIds = new Set();
    }
    
    /**
     * Removed localStorage for performance optimization
     */
    saveShownPopups() {
        // No localStorage operations for better performance
    }
    
    init() {
        // Cache DOM elements
        this.badge = document.getElementById('notificationBadge');
        this.dropdownMenu = document.getElementById('notificationDropdownMenu');
        
        console.log('🔧 Init - Badge element:', this.badge);
        console.log('🔧 Init - Dropdown menu:', this.dropdownMenu);
        
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
                console.log('✅ Notification dropdown button found, attaching event listener');
                notificationButton.addEventListener('click', () => {
                    console.log('🔔 Notification dropdown clicked!');
                    this.updateCount();
                    this.fetchRecent();
                });
                
                // Also listen to Bootstrap's show event
                this.dropdownMenu.addEventListener('show.bs.dropdown', () => {
                    console.log('🔔 Dropdown showing (Bootstrap event)');
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
        
        console.log(`📢 Showing popup for notification ID: ${notification.id}`);
        
        // Show notification popup
        await this.showNotificationPopup(notification);
        
        // After popup is dismissed, update count
        this.updateCount();
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
     * Show notification popup with Bootstrap 5 Toast (with action button)
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
            
            // Use Bootstrap 5 Toast with action button if URL exists
            if (window.createOptimaToast) {
                window.createOptimaToast({
                    type: type,
                    title: titleText,
                    message: messageText,
                    duration: 8000, // 8 seconds for notifications with action
                    url: notification.url || null,
                    actionText: 'Lihat Detail',
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
        console.log('🔄 Starting notification polling...');
        
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
        console.log('⏹️ Stopped notification polling');
    }
    
    /**
     * Trigger immediate check (call this after CRUD operations)
     */
    triggerImmediateCheck() {
        console.log('🚀 Triggering immediate notification check...');
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
                    console.log(`🔔 Received ${data.notifications.length} new notifications`);
                    
                    // Filter out notifications that have already been shown as popup
                    const newNotifications = data.notifications.filter(n => {
                        return !this.shownPopupIds.has(n.id);
                    });
                    
                    console.log(`📢 ${newNotifications.length} notifications ready for popup (${data.notifications.length - newNotifications.length} already shown)`);
                    
                    // Add to queue only new notifications
                    newNotifications.forEach(notification => {
                        this.notificationQueue.push(notification);
                    });
                    
                    // Process queue
                    this.processNotificationQueue();
                    
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
            console.log('📥 Fetching recent notifications from:', `${this.baseUrl}/notifications/get`);
            
            const response = await fetch(`${this.baseUrl}/notifications/get?limit=5`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                console.warn('⚠️ Fetch recent failed:', response.status, response.statusText);
                this.showErrorInDropdown('Gagal memuat notifikasi');
                return;
            }
            
            const data = await response.json();
            console.log('📦 Received data:', data);
            
            if (data.success && data.notifications) {
                console.log('✅ Updating dropdown with', data.notifications.length, 'notifications');
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
                
                html += `
                    <li class="notification-item ${readClass}" data-id="${notification.id}" data-read="${notification.is_read}">
                        <a class="dropdown-item" href="${this.getNotificationUrl(notification)}" onclick="markAsRead(${notification.id})">
                            <div class="d-flex align-items-start position-relative">
                                <i class="fas fa-${this.getNotificationIcon(notification.type)} text-${this.getNotificationColor(notification.type)} me-2 mt-1"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">${notification.title}</div>
                                    <div class="text-muted small">${notification.message}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">${this.formatDate(notification.created_at)}</div>
                                </div>
                                ${unreadDot}
                            </div>
                        </a>
                    </li>
                `;
            });
        }
        
        // Insert before the divider and "Lihat Semua" link
        const divider = this.dropdownMenu.querySelector('.dropdown-divider');
        if (divider) {
            divider.insertAdjacentHTML('beforebegin', html);
        }
    }
    
    getNotificationUrl(notification) {
        if (notification.related_id && notification.related_id > 0) {
            if (notification.related_module === 'spk') {
                return `${this.baseUrl}/marketing/spk/detail/${notification.related_id}`;
            } else if (notification.related_module === 'work_order') {
                return `${this.baseUrl}/service/work-orders/detail/${notification.related_id}`;
            } else if (notification.related_module === 'purchase_order') {
                return `${this.baseUrl}/purchasing/po/detail/${notification.related_id}`;
            }
        }
        
        return notification.url || `${this.baseUrl}/notifications`;
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
        // Simple toast notification (NOT USED anymore - using showNotificationPopup instead)
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: notification.title || 'Notifikasi',
                text: notification.message || '',
                icon: this.getIconType(notification.type),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        } else {
            // Fallback to browser notification
            if (Notification.permission === 'granted') {
                new Notification(notification.title || 'Notifikasi', {
                    body: notification.message || '',
                    icon: '/optima/public/assets/img/logo.png'
                });
            }
        }
    }
    
    /**
     * Show simple toast for system messages
     */
    showSimpleToast(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
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
                
                console.log('✅ All notifications marked as read');
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }
    
    destroy() {
        this.stopPolling();
        console.log('🗑️ Notification client destroyed');
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize lightweight notification client
    window.optimaNotification = new OptimaNotificationLightweight();
    
    // Debug: Check if class is properly initialized
    console.log('🔍 Notification client initialized:', window.optimaNotification);
    console.log('🔍 markAsRead method exists:', typeof window.optimaNotification.markAsRead);
    console.log('🔍 markAllAsRead method exists:', typeof window.optimaNotification.markAllAsRead);
    
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
        console.log('🔍 Global markAllAsRead called');
        console.log('🔍 optimaNotification exists:', !!window.optimaNotification);
        console.log('🔍 markAllAsRead method exists:', typeof window.optimaNotification?.markAllAsRead);
        
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
