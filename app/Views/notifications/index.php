<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-bell me-2"></i>Notification Center
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshNotifications()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="markAllAsRead()">
                    <i class="fas fa-check-double me-1"></i>Mark All Read
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="toggleRealTime()">
                    <i class="fas fa-broadcast-tower me-1"></i><span id="realTimeStatus">Enable</span> Real-time
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Notifications</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalNotifications">
                                <?= $notification_stats['total'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Unread</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="unreadNotifications">
                                <?= $notification_stats['unread'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayNotifications">
                                <?= $notification_stats['today'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                This Week</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="weekNotifications">
                                <?= $notification_stats['this_week'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info" id="realTimeAlert" style="display: none;">
                <i class="fas fa-broadcast-tower me-2"></i>
                <strong>Real-time notifications enabled!</strong> You will receive live updates automatically.
                <span class="float-end">
                    <small id="connectionStatus">Connecting...</small>
                </span>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Recent Notifications</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow">
                    <a class="dropdown-item" href="#" onclick="filterNotifications('all')">
                        <i class="fas fa-list me-2"></i>Show All
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterNotifications('unread')">
                        <i class="fas fa-envelope me-2"></i>Unread Only
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterNotifications('today')">
                        <i class="fas fa-calendar-day me-2"></i>Today Only
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="clearAllNotifications()">
                        <i class="fas fa-trash me-2"></i>Clear All
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="notificationsList">
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>" 
                             data-id="<?= $notification['id'] ?>" 
                             data-type="<?= $notification['type'] ?>">
                            <div class="d-flex align-items-start p-3 border-bottom">
                                <div class="notification-icon me-3">
                                    <i class="<?= getNotificationIcon($notification['type']) ?> fa-lg text-<?= getNotificationColor($notification['type']) ?>"></i>
                                </div>
                                <div class="notification-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="notification-title mb-1 <?= $notification['is_read'] ? 'text-muted' : 'fw-bold' ?>">
                                            <?= esc($notification['title']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?= timeAgo($notification['created_at']) ?>
                                        </small>
                                    </div>
                                    <p class="notification-message mb-2 <?= $notification['is_read'] ? 'text-muted' : '' ?>">
                                        <?= esc($notification['message']) ?>
                                    </p>
                                    <div class="notification-actions">
                                        <?php if (!$notification['is_read']): ?>
                                            <button class="btn btn-sm btn-outline-primary" onclick="markAsRead(<?= $notification['id'] ?>)">
                                                <i class="fas fa-check me-1"></i>Mark Read
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($notification['url']): ?>
                                            <a href="<?= esc($notification['url']) ?>" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-external-link-alt me-1"></i>View
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification(<?= $notification['id'] ?>)">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No notifications found</h5>
                        <p class="text-muted">You're all caught up! New notifications will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Test Notification Modal -->
<div class="modal fade" id="testNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-vial me-2"></i>Test Notification
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testNotificationForm">
                    <div class="mb-3">
                        <label for="testTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="testTitle" value="Test Notification" required>
                    </div>
                    <div class="mb-3">
                        <label for="testMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="testMessage" rows="3" required>This is a test notification to verify the real-time system is working correctly.</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="testType" class="form-label">Type</label>
                        <select class="form-select" id="testType">
                            <option value="info">Info</option>
                            <option value="success">Success</option>
                            <option value="warning">Warning</option>
                            <option value="error">Error</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendTestNotification()">
                    <i class="fas fa-paper-plane me-2"></i>Send Test
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let eventSource = null;
let realTimeEnabled = false;

$(document).ready(function() {
    // Initialize notification system
    initializeNotifications();
    
    // Add test button for development
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        $('.btn-group').append(`
            <button class="btn btn-outline-warning btn-sm" onclick="showTestModal()">
                <i class="fas fa-vial me-1"></i>Test
            </button>
        `);
    }
});

function initializeNotifications() {
    // Auto-enable real-time if supported
    if (typeof(EventSource) !== "undefined") {
        // Real-time is supported
        console.log('Server-Sent Events supported');
    } else {
        // Fallback to polling
        console.log('Server-Sent Events not supported, using polling');
        setInterval(refreshNotifications, 30000); // Poll every 30 seconds
    }
}

function toggleRealTime() {
    if (realTimeEnabled) {
        disableRealTime();
    } else {
        enableRealTime();
    }
}

function enableRealTime() {
    if (typeof(EventSource) === "undefined") {
        showNotification('Real-time notifications not supported by your browser', 'error');
        return;
    }

    if (eventSource) {
        eventSource.close();
    }

    eventSource = new EventSource('<?= base_url('notifications/stream') ?>');
    
    eventSource.onopen = function(event) {
        realTimeEnabled = true;
        $('#realTimeStatus').text('Disable');
        $('#realTimeAlert').show();
        $('#connectionStatus').text('Connected').removeClass('text-danger').addClass('text-success');
        showNotification('Real-time notifications enabled', 'success');
    };

    eventSource.onmessage = function(event) {
        const data = JSON.parse(event.data);
        addNotificationToList(data);
        updateNotificationStats();
        showToast(data);
    };

    eventSource.addEventListener('notification', function(event) {
        const data = JSON.parse(event.data);
        addNotificationToList(data);
        updateNotificationStats();
        showToast(data);
    });

    eventSource.addEventListener('heartbeat', function(event) {
        $('#connectionStatus').text('Connected').removeClass('text-danger').addClass('text-success');
    });

    eventSource.onerror = function(event) {
        $('#connectionStatus').text('Connection Error').removeClass('text-success').addClass('text-danger');
        
        if (eventSource.readyState === EventSource.CLOSED) {
            realTimeEnabled = false;
            $('#realTimeStatus').text('Enable');
            $('#realTimeAlert').hide();
            showNotification('Real-time connection lost', 'warning');
        }
    };
}

function disableRealTime() {
    if (eventSource) {
        eventSource.close();
        eventSource = null;
    }
    
    realTimeEnabled = false;
    $('#realTimeStatus').text('Enable');
    $('#realTimeAlert').hide();
    showNotification('Real-time notifications disabled', 'info');
}

function addNotificationToList(notification) {
    const notificationHtml = `
        <div class="notification-item unread" data-id="${notification.id}" data-type="${notification.type}">
            <div class="d-flex align-items-start p-3 border-bottom">
                <div class="notification-icon me-3">
                    <i class="${notification.icon} fa-lg text-${getNotificationColorJS(notification.type)}"></i>
                </div>
                <div class="notification-content flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="notification-title mb-1 fw-bold">
                            ${notification.title}
                        </h6>
                        <small class="text-muted">
                            ${notification.timestamp}
                        </small>
                    </div>
                    <p class="notification-message mb-2">
                        ${notification.message}
                    </p>
                    <div class="notification-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="markAsRead(${notification.id})">
                            <i class="fas fa-check me-1"></i>Mark Read
                        </button>
                        ${notification.url ? `<a href="${notification.url}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-external-link-alt me-1"></i>View
                        </a>` : ''}
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification(${notification.id})">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#notificationsList').prepend(notificationHtml);
    
    // Add animation
    $('#notificationsList .notification-item:first').hide().fadeIn(500);
}

function showToast(notification) {
    // Use global OptimaPro toast for consistency across app
    const type = notification.type || 'info';
    const title = notification.title || (type.charAt(0).toUpperCase()+type.slice(1));
    if (window.OptimaPro && typeof OptimaPro.showNotification==='function') {
        // Combine title + message into one toast
        OptimaPro.showNotification(`${title}: ${notification.message || ''}`, type);
    } else {
        // Fallback to inline alert at top of page
        showNotification(`${title}: ${notification.message || ''}`, type);
    }
}

function getNotificationColorJS(type) {
    switch (type) {
        case 'success': return 'success';
        case 'warning': return 'warning';
        case 'error': return 'danger';
        case 'info':
        default: return 'info';
    }
}

function refreshNotifications() {
    location.reload();
}

function markAsRead(notificationId) {
    $.ajax({
        url: '<?= base_url('notifications/mark-read/') ?>' + notificationId,
        method: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $(`.notification-item[data-id="${notificationId}"]`)
                    .removeClass('unread').addClass('read')
                    .find('.notification-title').removeClass('fw-bold').addClass('text-muted')
                    .parent().find('.notification-message').addClass('text-muted')
                    .parent().find('.btn-outline-primary').remove();
                
                updateNotificationStats();
                showNotification('Notification marked as read', 'success');
            }
        },
        error: function() {
            showNotification('Failed to mark notification as read', 'error');
        }
    });
}

function markAllAsRead() {
    $.ajax({
        url: '<?= base_url('notifications/mark-all-read') ?>',
        method: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('.notification-item.unread')
                    .removeClass('unread').addClass('read')
                    .find('.notification-title').removeClass('fw-bold').addClass('text-muted')
                    .parent().find('.notification-message').addClass('text-muted')
                    .parent().find('.btn-outline-primary').remove();
                
                updateNotificationStats();
                showNotification('All notifications marked as read', 'success');
            }
        },
        error: function() {
            showNotification('Failed to mark notifications as read', 'error');
        }
    });
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        $.ajax({
            url: '<?= base_url('notifications/delete/') ?>' + notificationId,
            method: 'DELETE',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $(`.notification-item[data-id="${notificationId}"]`).fadeOut(500, function() {
                        $(this).remove();
                        updateNotificationStats();
                    });
                    showNotification('Notification deleted', 'success');
                }
            },
            error: function() {
                showNotification('Failed to delete notification', 'error');
            }
        });
    }
}

function updateNotificationStats() {
    // Update stats from current DOM
    const total = $('.notification-item').length;
    const unread = $('.notification-item.unread').length;
    
    $('#totalNotifications').text(total);
    $('#unreadNotifications').text(unread);
}

function filterNotifications(filter) {
    $('.notification-item').show();
    
    switch (filter) {
        case 'unread':
            $('.notification-item.read').hide();
            break;
        case 'today':
            // This would need server-side implementation for accurate filtering
            break;
    }
}

function clearAllNotifications() {
    if (confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
        showNotification('Clear all notifications feature coming soon!', 'info');
    }
}

function showTestModal() {
    $('#testNotificationModal').modal('show');
}

function sendTestNotification() {
    const formData = {
        title: $('#testTitle').val(),
        message: $('#testMessage').val(),
        type: $('#testType').val()
    };

    $.ajax({
        url: '<?= base_url('notifications/create') ?>',
        method: 'POST',
        data: formData,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#testNotificationModal').modal('hide');
                showNotification('Test notification sent!', 'success');
            } else {
                showNotification('Failed to send test notification', 'error');
            }
        },
        error: function() {
            showNotification('Error sending test notification', 'error');
        }
    });
}

function showNotification(message, type) {
    if (window.OptimaPro && typeof OptimaPro.showNotification==='function') {
        return OptimaPro.showNotification(message, type);
    }
    // Fallback simple alert if global not available
    alert(`${type ? type.toUpperCase() : 'INFO'}: ${message}`);
}

// Cleanup on page unload
$(window).on('beforeunload', function() {
    if (eventSource) {
        eventSource.close();
    }
});
</script>

<?php
// Helper functions
function getNotificationIcon($type) {
    switch ($type) {
        case 'success': return 'fas fa-check-circle';
        case 'warning': return 'fas fa-exclamation-triangle';
        case 'error': return 'fas fa-times-circle';
        case 'info':
        default: return 'fas fa-info-circle';
    }
}

function getNotificationColor($type) {
    switch ($type) {
        case 'success': return 'success';
        case 'warning': return 'warning';
        case 'error': return 'danger';
        case 'info':
        default: return 'info';
    }
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    
    return date('M j, Y', strtotime($datetime));
}
?>
<?= $this->endSection() ?> 