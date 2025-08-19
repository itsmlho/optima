<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shopping-cart me-2"></i>Purchasing Division Dashboard
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="me-3">
                <small class="text-muted">Terakhir diperbarui: </small>
                <span class="fw-bold"><?= date('d M Y, H:i') ?></span>
            </div>
            <div class="btn-group" role="group">
                <button class="btn btn-outline-primary btn-sm" onclick="exportPurchasingDashboard()">
                    <i class="fas fa-download me-1"></i>Export
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="generatePOReport()">
                    <i class="fas fa-file-alt me-1"></i>Report
                </button>
                <button class="btn btn-primary btn-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- PO Unit Stats -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                PO Unit Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $po_unit_stats['total'] ?>
                            </div>
                            <div class="text-xs mt-1">
                                <span class="text-success">Sesuai: <?= $po_unit_stats['sesuai'] ?></span> |
                                <span class="text-warning">Belum: <?= $po_unit_stats['belum_dicek'] ?></span> |
                                <span class="text-danger">Tidak: <?= $po_unit_stats['tidak_sesuai'] ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PO Attachment Stats -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                PO Attachment & Battery</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $po_attachment_stats['total'] ?>
                            </div>
                            <div class="text-xs mt-1">
                                <span class="text-success">Sesuai: <?= $po_attachment_stats['sesuai'] ?></span> |
                                <span class="text-warning">Belum: <?= $po_attachment_stats['belum_dicek'] ?></span> |
                                <span class="text-danger">Tidak: <?= $po_attachment_stats['tidak_sesuai'] ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-battery-full fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PO Sparepart Stats -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                PO Sparepart Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $po_sparepart_stats['total'] ?>
                            </div>
                            <div class="text-xs mt-1">
                                <span class="text-success">Sesuai: <?= $po_sparepart_stats['sesuai'] ?></span> |
                                <span class="text-warning">Belum: <?= $po_sparepart_stats['belum_dicek'] ?></span> |
                                <span class="text-danger">Tidak: <?= $po_sparepart_stats['tidak_sesuai'] ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Notifications</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $notification_stats['pending'] ?>
                            </div>
                            <div class="text-xs mt-1">
                                <span class="text-info">Total: <?= $notification_stats['total'] ?></span> |
                                <span class="text-success">Read: <?= $notification_stats['read'] ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <!-- PO Management Cards -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Purchase Order Management</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- PO Unit Card -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-truck fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">PO Unit</h5>
                                    <p class="card-text text-muted">Kelola Purchase Order untuk Unit Forklift</p>
                                    <div class="mb-3">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="text-success font-weight-bold"><?= $po_unit_stats['sesuai'] ?></div>
                                                <small>Sesuai</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-warning font-weight-bold"><?= $po_unit_stats['belum_dicek'] ?></div>
                                                <small>Belum</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-danger font-weight-bold"><?= $po_unit_stats['tidak_sesuai'] ?></div>
                                                <small>Tidak</small>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('/purchasing/po-unit') ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-arrow-right me-1"></i>Kelola PO Unit
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- PO Attachment Card -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-battery-full fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">PO Attachment & Battery</h5>
                                    <p class="card-text text-muted">Kelola PO untuk Attachment & Battery</p>
                                    <div class="mb-3">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="text-success font-weight-bold"><?= $po_attachment_stats['sesuai'] ?></div>
                                                <small>Sesuai</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-warning font-weight-bold"><?= $po_attachment_stats['belum_dicek'] ?></div>
                                                <small>Belum</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-danger font-weight-bold"><?= $po_attachment_stats['tidak_sesuai'] ?></div>
                                                <small>Tidak</small>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('/purchasing/po-attachment') ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-arrow-right me-1"></i>Kelola PO Attachment
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- PO Sparepart Card -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-info h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-cogs fa-3x text-info"></i>
                                    </div>
                                    <h5 class="card-title">PO Sparepart</h5>
                                    <p class="card-text text-muted">Kelola Purchase Order untuk Sparepart</p>
                                    <div class="mb-3">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="text-success font-weight-bold"><?= $po_sparepart_stats['sesuai'] ?></div>
                                                <small>Sesuai</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-warning font-weight-bold"><?= $po_sparepart_stats['belum_dicek'] ?></div>
                                                <small>Belum</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-danger font-weight-bold"><?= $po_sparepart_stats['tidak_sesuai'] ?></div>
                                                <small>Tidak</small>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('/purchasing/po-sparepart') ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-arrow-right me-1"></i>Kelola PO Sparepart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Panel -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Notifications</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="markAllNotificationsRead()">Mark All as Read</a>
                            <a class="dropdown-item" href="#" onclick="viewAllNotifications()">View All</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="notifications-container" style="max-height: 400px; overflow-y: auto;">
                        <?php if (!empty($recent_notifications)): ?>
                            <?php foreach ($recent_notifications as $notification): ?>
                                <div class="notification-item mb-3 p-2 border rounded">
                                    <div class="d-flex align-items-start">
                                        <div class="me-2">
                                            <i class="<?= $notification['icon'] ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="notification-message">
                                                <?= $notification['message'] ?>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i><?= $notification['time_ago'] ?>
                                            </small>
                                            <span class="badge <?= $notification['badge_class'] ?> ms-2">
                                                <?= ucfirst($notification['status']) ?>
                                            </span>
                                        </div>
                                        <?php if ($notification['status'] === 'pending'): ?>
                                            <button class="btn btn-sm btn-outline-primary" onclick="markNotificationRead(<?= $notification['id_notification'] ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                <p>Tidak ada notifikasi terbaru</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Status Chart -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Verification Status Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="verificationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-bottom pb-2">
                                <div class="h4 text-primary"><?= $po_unit_stats['completion_rate'] ?>%</div>
                                <small class="text-muted">Unit Completion</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border-bottom pb-2">
                                <div class="h4 text-success"><?= $po_attachment_stats['completion_rate'] ?>%</div>
                                <small class="text-muted">Attachment Completion</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border-bottom pb-2">
                                <div class="h4 text-info"><?= $po_sparepart_stats['completion_rate'] ?>%</div>
                                <small class="text-muted">Sparepart Completion</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border-bottom pb-2">
                                <div class="h4 text-warning"><?= $notification_stats['today'] ?></div>
                                <small class="text-muted">Today's Notifications</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeVerificationChart();
});

function initializeVerificationChart() {
    const ctx = document.getElementById('verificationChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['PO Unit', 'PO Attachment', 'PO Sparepart'],
            datasets: [{
                label: 'Sesuai',
                data: [<?= $po_unit_stats['sesuai'] ?>, <?= $po_attachment_stats['sesuai'] ?>, <?= $po_sparepart_stats['sesuai'] ?>],
                backgroundColor: '#28a745'
            }, {
                label: 'Belum Dicek',
                data: [<?= $po_unit_stats['belum_dicek'] ?>, <?= $po_attachment_stats['belum_dicek'] ?>, <?= $po_sparepart_stats['belum_dicek'] ?>],
                backgroundColor: '#ffc107'
            }, {
                label: 'Tidak Sesuai',
                data: [<?= $po_unit_stats['tidak_sesuai'] ?>, <?= $po_attachment_stats['tidak_sesuai'] ?>, <?= $po_sparepart_stats['tidak_sesuai'] ?>],
                backgroundColor: '#dc3545'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
}

function exportPurchasingDashboard() {
    window.print();
}

function generatePOReport() {
    OptimaPro.showNotification('Generate PO report functionality will be implemented', 'info');
}

function markNotificationRead(id) {
    fetch(`<?= base_url('/purchasing/mark-notification-read/') ?>${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            OptimaPro.showNotification('Notification marked as read', 'success');
            location.reload();
        }
    });
}

function markAllNotificationsRead() {
    // Implementation for marking all notifications as read
    OptimaPro.showNotification('Mark all notifications functionality will be implemented', 'info');
}

function viewAllNotifications() {
    // Implementation for viewing all notifications
    OptimaPro.showNotification('View all notifications functionality will be implemented', 'info');
}
</script>

<?= $this->endSection() ?> 