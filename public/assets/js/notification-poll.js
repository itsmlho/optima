// Notification polling for Optima
// Call this in your main layout after login
// Requires: showNotification() already loaded

function pollNotifications() {
    fetch('/api/notifications')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(n => {
                    showNotification(n.message, 'warning');
                    // Optionally, update notification bell UI here
                });
            }
        });
}

// Poll every 30 seconds
document.addEventListener('DOMContentLoaded', function() {
    setInterval(pollNotifications, 30000);
    pollNotifications(); // initial call
});
