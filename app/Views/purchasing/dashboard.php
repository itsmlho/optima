<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .card-stats {
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .card-stats::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
    }
    
    .filter-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .table-responsive {
        border-radius: 0;
    }
    
    .btn-action {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .btn-action:hover {
        transform: scale(1.05);
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin: -1.5rem -1.5rem 2rem -1.5rem;
        border-radius: 0 0 20px 20px;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .po-badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.001);
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white !important;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .alert {
        border: none;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1.5rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.6rem 1rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Enhanced Page Header -->
<div class="page-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-0 fw-bold">
                    <i class="fas fa-shopping-cart me-3"></i>Purchasing Division Dashboard
                </h1>
                <p class="mb-0 opacity-75">Comprehensive purchase order management and monitoring system</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light btn-action" onclick="exportPurchasingData()">
                    <i class="fas fa-download me-2"></i>Export Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- PO Unit Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="h2 mb-2 fw-bold"><?= isset($po_unit_stats['total']) ? $po_unit_stats['total'] : 0 ?></div>
                        <div class="text-uppercase small opacity-75 fw-semibold">PO Unit</div>
                        <div class="small opacity-50 mt-1">Total purchase orders</div>
                    </div>
                    <div class="opacity-75">
                        <i class="fas fa-truck fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- PO Attachment Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="h2 mb-2 fw-bold"><?= isset($po_attachment_stats['total']) ? $po_attachment_stats['total'] : 0 ?></div>
                        <div class="text-uppercase small opacity-75 fw-semibold">PO Attachment</div>
                        <div class="small opacity-50 mt-1">Battery & attachments</div>
                    </div>
                    <div class="opacity-75">
                        <i class="fas fa-battery-full fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- PO Sparepart Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="h2 mb-2 fw-bold"><?= isset($po_sparepart_stats['total']) ? $po_sparepart_stats['total'] : 0 ?></div>
                        <div class="text-uppercase small opacity-75 fw-semibold">PO Sparepart</div>
                        <div class="small opacity-50 mt-1">Parts & components</div>
                    </div>
                    <div class="opacity-75">
                        <i class="fas fa-cogs fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Notifications Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="h2 mb-2 fw-bold"><?= isset($notification_stats['unread']) ? $notification_stats['unread'] : 0 ?></div>
                        <div class="text-uppercase small opacity-75 fw-semibold">Notifications</div>
                        <div class="small opacity-50 mt-1">Unread messages</div>
                    </div>
                    <div class="opacity-75">
                        <i class="fas fa-bell fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="<?= base_url('/purchasing/po-unit') ?>" class="btn btn-primary w-100 btn-action">
                            <i class="fas fa-truck me-2"></i>PO Unit
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= base_url('/purchasing/po-attachment') ?>" class="btn btn-success w-100 btn-action">
                            <i class="fas fa-battery-full me-2"></i>PO Attachment
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= base_url('/purchasing/po-sparepart') ?>" class="btn btn-warning w-100 btn-action">
                            <i class="fas fa-cogs me-2"></i>PO Sparepart
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= base_url('/purchasing/po-unitForm') ?>" class="btn btn-info w-100 btn-action">
                            <i class="fas fa-plus me-2"></i>New PO
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Notifications -->
<div class="row g-4">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bell me-2"></i>Recent Notifications
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_notifications)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_notifications as $notification): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?></td>
                                        <td>
                                            <span class="po-badge bg-<?= $notification['type'] == 'warning' ? 'warning' : ($notification['type'] == 'error' ? 'danger' : 'info') ?>">
                                                <?= ucfirst($notification['type']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($notification['message']) ?></td>
                                        <td>
                                            <span class="status-badge bg-<?= $notification['is_read'] ? 'secondary' : 'primary' ?>">
                                                <?= $notification['is_read'] ? 'Read' : 'Unread' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent notifications</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mt-4">
    <div class="col-md-6">
        <div class="card table-card">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>PO Status Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="poStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card table-card">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>Monthly PO Trends
                </h5>
            </div>
            <div class="card-body">
                <canvas id="poTrendsChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // PO Status Distribution Chart
    const ctx1 = document.getElementById('poStatusChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    <?= isset($po_unit_stats['pending']) ? $po_unit_stats['pending'] : 0 ?>,
                    <?= isset($po_unit_stats['approved']) ? $po_unit_stats['approved'] : 0 ?>,
                    <?= isset($po_unit_stats['completed']) ? $po_unit_stats['completed'] : 0 ?>,
                    0
                ],
                backgroundColor: ['#ffc107', '#28a745', '#007bff', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Monthly PO Trends Chart
    const ctx2 = document.getElementById('poTrendsChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'PO Unit',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4
            }, {
                label: 'PO Attachment',
                data: [8, 12, 10, 18, 15, 22],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }, {
                label: 'PO Sparepart',
                data: [5, 8, 6, 12, 10, 15],
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

function exportPurchasingData() {
    // Add export functionality here
    OptimaPro.showNotification('Export functionality will be implemented', 'info');
}
</script>
<?= $this->endSection() ?> 