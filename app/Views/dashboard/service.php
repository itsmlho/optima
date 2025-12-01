<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid" aria-label="Dashboard Service" role="region">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" id="pageTitle">
            <i class="fas fa-tools me-2" aria-hidden="true"></i>Dashboard Service
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="me-3">
                <small class="text-muted">Terakhir diperbarui: </small>
                <span class="fw-bold" aria-live="polite"><?= date('d M Y, H:i') ?></span>
            </div>
            <button class="btn btn-primary btn-sm" onclick="location.reload()" aria-label="Refresh Dashboard"><i class="fas fa-sync-alt me-1" aria-hidden="true"></i>Refresh</button>
        </div>
    </div>

    <!-- Statistics Cards - Professional Standard -->
    
    <div class="row mt-3 mb-4" aria-label="Statistik Service" role="list">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-file-text stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-work-orders" data-count="<?= $service_stats['total_work_orders'] ?>">0</div>
                        <div class="text-muted">Total Work Orders</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-clock stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-pending-pmps"><?= $service_stats['pending_pmps'] ?></div>
                        <div class="text-muted">Pending PMPs</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-success-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-circle stat-icon text-success"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-completed-services"><?= $service_stats['completed_services'] ?></div>
                        <div class="text-muted">Completed Services</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-danger-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-exclamation-triangle stat-icon text-danger"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-maintenance-alerts"><?= $service_stats['maintenance_alerts'] ?></div>
                        <div class="text-muted">Maintenance Alerts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Work Orders Trend
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="workOrdersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Service Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="serviceStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Recent Work Orders
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-wrench text-info me-2"></i>
                                <strong>WO-2024-001</strong> - Engine maintenance FL-003
                            </div>
                            <small class="text-muted">2 hours ago</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-cogs text-warning me-2"></i>
                                <strong>WO-2024-002</strong> - Hydraulic system repair FL-007
                            </div>
                            <small class="text-muted">4 hours ago</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>WO-2024-003</strong> - Brake inspection completed FL-012
                            </div>
                            <small class="text-muted">6 hours ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Upcoming PMPS
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-calendar-times me-2"></i>
                        <strong>FL-001</strong> - Monthly service due tomorrow
                    </div>
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-calendar-check me-2"></i>
                        <strong>FL-005</strong> - 3-month inspection next week
                    </div>
                    <div class="alert alert-secondary" role="alert">
                        <i class="fas fa-calendar me-2"></i>
                        <strong>FL-009</strong> - Annual service next month
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Work Orders Chart
const workOrdersCtx = document.getElementById('workOrdersChart').getContext('2d');
const workOrdersChart = new Chart(workOrdersCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Work Orders',
            data: [12, 19, 3, 5, 2, 3],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Service Status Chart
const serviceStatusCtx = document.getElementById('serviceStatusChart').getContext('2d');
const serviceStatusChart = new Chart(serviceStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'In Progress', 'Pending'],
        datasets: [{
            data: [65, 25, 10],
            backgroundColor: [
                '#1cc88a',
                '#36b9cc',
                '#f6c23e'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
<?= $this->endSection() ?> 