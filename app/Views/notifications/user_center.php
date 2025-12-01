<?php $this->extend('layouts/base'); ?>

<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-bell me-2"></i>Notification Center</h2>
            <p class="text-muted mb-0">View and manage your notifications</p>
        </div>
        <button class="btn btn-outline-primary" onclick="markAllAsRead()">
            <i class="fas fa-check-double me-2"></i>Mark All as Read
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary bg-soft text-primary">
                                <i class="fas fa-bell fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Total</h6>
                            <h3 class="mb-0 fw-bold"><?= $stats['total'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning bg-soft text-warning">
                                <i class="fas fa-envelope fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Unread</h6>
                            <h3 class="mb-0 fw-bold"><?= $stats['unread'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-soft text-success">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Read</h6>
                            <h3 class="mb-0 fw-bold"><?= $stats['read'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info bg-soft text-info">
                                <i class="fas fa-calendar-day fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Today</h6>
                            <h3 class="mb-0 fw-bold"><?= $stats['today'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Notifications</h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" onclick="filterNotifications('all')">All</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterNotifications('unread')">Unread</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterNotifications('read')">Read</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="notificationsList">
                <?php 
                // Debug: Show notification count
                echo "<!-- DEBUG: Notification count = " . count($notifications ?? []) . " -->";
                ?>
                <?php if (empty($notifications)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No notifications yet</p>
                    <small class="text-muted">Debug: Count = <?= count($notifications ?? []) ?></small>
                </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?= $notification['is_read'] ? '' : 'unread' ?>" data-id="<?= $notification['id'] ?>" data-read="<?= $notification['is_read'] ?>">
                        <div class="d-flex align-items-start p-3 border-bottom">
                            <div class="flex-shrink-0 me-3">
                                <?php
                                $typeColors = [
                                    'info' => 'primary',
                                    'success' => 'success',
                                    'warning' => 'warning',
                                    'error' => 'danger'
                                ];
                                $color = $typeColors[$notification['type']] ?? 'secondary';
                                ?>
                                <div class="avatar-sm rounded-circle bg-<?= $color ?> bg-soft text-<?= $color ?>">
                                    <i class="fas fa-<?= $notification['icon'] ?> fa-lg"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= esc($notification['title']) ?></h6>
                                        <p class="text-muted mb-2 small"><?= esc($notification['message']) ?></p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= timeAgo($notification['created_at']) ?>
                                        </small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <?php if (!$notification['is_read']): ?>
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="markAsRead(<?= $notification['id'] ?>)">
                                                <i class="fas fa-check me-2"></i>Mark as Read
                                            </a></li>
                                            <?php endif; ?>
                                            <?php if ($notification['url']): ?>
                                            <li><a class="dropdown-item" href="<?= $notification['url'] ?>">
                                                <i class="fas fa-external-link-alt me-2"></i>View Details
                                            </a></li>
                                            <?php endif; ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteNotification(<?= $notification['id'] ?>)">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item.unread {
    background-color: #f8f9fa;
}

.notification-item.unread .flex-grow-1 h6 {
    font-weight: 600;
}

.notification-item:hover {
    background-color: #f1f3f5;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-soft {
    opacity: 0.2;
}
</style>

<script>
const csrfToken = '<?= csrf_hash() ?>';
const baseUrl = '<?= base_url() ?>';

// Mark notification as read
async function markAsRead(notificationId) {
    try {
        const response = await fetch(`${baseUrl}/notifications/mark-as-read/${notificationId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            const item = document.querySelector(`[data-id="${notificationId}"]`);
            if (item) {
                item.classList.remove('unread');
                item.setAttribute('data-read', '1');
            }
            updateNotificationCount();
            showNotification('Notification marked as read', 'success');
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

// Mark all as read
async function markAllAsRead() {
    try {
        const response = await fetch(`${baseUrl}/notifications/mark-all-as-read`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                item.setAttribute('data-read', '1');
            });
            updateNotificationCount();
            showNotification('All notifications marked as read', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
}

// Delete notification
async function deleteNotification(notificationId) {
    const result = await Swal.fire({
        title: 'Delete notification?',
        text: "This action cannot be undone",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`${baseUrl}/notifications/delete/${notificationId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                const item = document.querySelector(`[data-id="${notificationId}"]`);
                if (item) {
                    item.remove();
                }
                updateNotificationCount();
                showNotification('Notification deleted', 'success');
            }
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    }
}

// Filter notifications
function filterNotifications(filter) {
    const items = document.querySelectorAll('.notification-item');
    
    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Filter items
    items.forEach(item => {
        const isRead = item.getAttribute('data-read') === '1';
        
        if (filter === 'all') {
            item.style.display = '';
        } else if (filter === 'unread' && isRead) {
            item.style.display = 'none';
        } else if (filter === 'read' && !isRead) {
            item.style.display = 'none';
        } else {
            item.style.display = '';
        }
    });
}

// Update notification count in header
function updateNotificationCount() {
    if (typeof window.updateNotificationCount === 'function') {
        window.updateNotificationCount();
    }
}
</script>

<?php $this->endSection(); ?>

<?php
// Helper function for time ago
function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    
    return date('d M Y', $time);
}
?>

