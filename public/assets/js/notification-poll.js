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

// Poll every 5 minutes (300 seconds) to reduce server load
document.addEventListener('DOMContentLoaded', function() {
    setInterval(pollNotifications, 300000);
    pollNotifications(); // initial call
});
