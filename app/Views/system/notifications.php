<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Action Buttons -->
<div class="d-flex justify-content-end gap-2 mb-4">
    <button class="btn btn-outline-secondary" onclick="markAllAsRead()">
        <i class="fas fa-check-double me-2"></i>Mark All as Read
    </button>
    <button class="btn btn-outline-danger" onclick="clearAllNotifications()">
        <i class="fas fa-trash me-2"></i>Clear All
    </button>
</div>

<!-- Notification Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="d-flex align-items-center">
                    <div class="pro-stats-icon bg-primary me-3">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <div class="pro-stats-label">Total</div>
                        <div class="pro-stats-value"><?= count($notifications) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="d-flex align-items-center">
                    <div class="pro-stats-icon bg-warning me-3">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <div class="pro-stats-label">Unread</div>
                        <div class="pro-stats-value"><?= count(array_filter($notifications, fn($n) => !$n['read'])) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="d-flex align-items-center">
                    <div class="pro-stats-icon bg-danger me-3">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="pro-stats-label">Critical</div>
                        <div class="pro-stats-value"><?= count(array_filter($notifications, fn($n) => $n['type'] === 'warning')) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="d-flex align-items-center">
                    <div class="pro-stats-icon bg-success me-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="pro-stats-label">Completed</div>
                        <div class="pro-stats-value"><?= count(array_filter($notifications, fn($n) => $n['type'] === 'success')) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="card-title">Recent Notifications</h5>
        <p class="card-subtitle">All system notifications and alerts</p>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <?php foreach ($notifications as $index => $notification): ?>
            <div class="list-group-item list-group-item-action notification-item <?= !$notification['read'] ? 'unread' : '' ?>" data-index="<?= $index ?>">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="<?= $notification['icon'] ?> fa-lg text-<?= $notification['type'] === 'warning' ? 'warning' : ($notification['type'] === 'success' ? 'success' : 'info') ?>"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="mb-1 fw-semibold"><?= esc($notification['title']) ?></h6>
                            <div class="d-flex gap-2">
                                <?php if (!$notification['read']): ?>
                                <span class="badge bg-primary rounded-pill">New</span>
                                <?php endif; ?>
                                <small class="text-muted"><?= esc($notification['time']) ?></small>
                            </div>
                        </div>
                        <p class="mb-1 text-muted"><?= esc($notification['message']) ?></p>
                        <div class="d-flex gap-2 mt-2">
                            <?php if (!$notification['read']): ?>
                            <button class="btn btn-sm btn-outline-primary" onclick="markAsRead(<?= $index ?>)">
                                <i class="fas fa-check me-1"></i>Mark as Read
                            </button>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification(<?= $index ?>)">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.notification-item.unread {
    background-color: var(--bs-light);
    border-left: 4px solid var(--bs-primary);
}

.notification-item:hover {
    background-color: var(--bs-light);
}
</style>

<script>
function markAsRead(index) {
    const item = document.querySelector(`[data-index="${index}"]`);
    if (item) {
        item.classList.remove('unread');
        const badge = item.querySelector('.badge');
        if (badge) badge.remove();
        const button = item.querySelector('.btn-outline-primary');
        if (button) button.remove();
        showNotification('Notification marked as read', 'success');
    }
}

function markAllAsRead() {
    const unreadItems = document.querySelectorAll('.notification-item.unread');
    unreadItems.forEach(item => {
        item.classList.remove('unread');
        const badge = item.querySelector('.badge');
        if (badge) badge.remove();
        const button = item.querySelector('.btn-outline-primary');
        if (button) button.remove();
    });
    showNotification('All notifications marked as read', 'success');
}

function deleteNotification(index) {
    const item = document.querySelector(`[data-index="${index}"]`);
    if (item && confirm('Are you sure you want to delete this notification?')) {
        item.remove();
        showNotification('Notification deleted', 'info');
    }
}

function clearAllNotifications() {
    if (confirm('Are you sure you want to clear all notifications?')) {
        const container = document.querySelector('.list-group');
        container.innerHTML = '<div class="text-center p-4 text-muted">No notifications</div>';
        showNotification('All notifications cleared', 'info');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 1060; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}
</script>

<?= $this->endSection() ?>
