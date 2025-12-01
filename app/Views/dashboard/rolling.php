<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid" aria-label="Dashboard Rolling Unit" role="region">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" id="pageTitle">
            <i class="fas fa-truck-moving me-2" aria-hidden="true"></i>Dashboard Rolling Unit
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="me-3">
                <small class="text-muted">Terakhir diperbarui: </small>
                <span class="fw-bold" aria-live="polite"><?= date('d M Y, H:i') ?></span>
            </div>
            <div class="btn-group" role="group" aria-label="Aksi Ekspor dan Refresh">
                <button class="btn btn-outline-primary btn-sm" onclick="exportDashboard()" aria-label="Export Dashboard"><i class="fas fa-download me-1" aria-hidden="true"></i>Export</button>
                <button class="btn btn-primary btn-sm" onclick="location.reload()" aria-label="Refresh Dashboard"><i class="fas fa-sync-alt me-1" aria-hidden="true"></i>Refresh</button>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4" aria-label="Statistik Rolling Unit" role="list">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" role="listitem">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Unit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" data-count="<?= $rolling_stats['total_units'] ?? 0 ?>">0</div>
                            <div class="text-xs text-success"><i class="fas fa-arrow-up" aria-hidden="true"></i> +2 dari bulan lalu</div>
                        </div>
                        <div class="col-auto" aria-hidden="true">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                                Unit Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $rolling_stats['active_units'] ?? 0 ?>
                            </div>
                            <div class="text-xs text-success">
                                <i class="fas fa-check-circle"></i> <?= round(($rolling_stats['active_units'] ?? 0) / max(($rolling_stats['total_units'] ?? 1), 1) * 100, 1) ?>% dari total
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Maintenance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $rolling_stats['in_maintenance'] ?? 0 ?>
                            </div>
                            <div class="text-xs text-warning">
                                <i class="fas fa-tools"></i> <?= round(($rolling_stats['in_maintenance'] ?? 0) / max(($rolling_stats['total_units'] ?? 1), 1) * 100, 1) ?>% dari total
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
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
                                Unit Disewa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $rolling_stats['rented_units'] ?? 0 ?>
                            </div>
                            <div class="text-xs text-info">
                                <i class="fas fa-handshake"></i> <?= round(($rolling_stats['rented_units'] ?? 0) / max(($rolling_stats['total_units'] ?? 1), 1) * 100, 1) ?>% utilization
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Utilization Rate Trend
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exportChart('utilizationChart')">
                                <i class="fas fa-download me-2"></i>Export Chart
                            </a>
                            <a class="dropdown-item" href="#" onclick="viewChartDetails('utilization')">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="utilizationChart" style="height: 300px;"></canvas>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">Rata-rata utilization rate: <strong>78.5%</strong></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Status Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusChart" style="height: 200px;"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Aktif (<?= $rolling_stats['active_units'] ?? 0 ?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Maintenance (<?= $rolling_stats['in_maintenance'] ?? 0 ?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Disewa (<?= $rolling_stats['rented_units'] ?? 0 ?>)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Dashboard -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Unit Performance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="performanceChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>Downtime Analysis
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="downtimeChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Recent Activities
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="activitiesTable">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Unit</th>
                                    <th>Activity</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>10:30</td>
                                    <td>FL-001</td>
                                    <td>Maintenance completed</td>
                                    <td><span class="badge badge-success">Completed</span></td>
                                    <td>Workshop A</td>
                                </tr>
                                <tr>
                                    <td>09:15</td>
                                    <td>FL-015</td>
                                    <td>Deployed to site</td>
                                    <td><span class="badge badge-info">Active</span></td>
                                    <td>Site Jakarta</td>
                                </tr>
                                <tr>
                                    <td>08:45</td>
                                    <td>FL-008</td>
                                    <td>Scheduled maintenance</td>
                                    <td><span class="badge badge-warning">Pending</span></td>
                                    <td>Workshop B</td>
                                </tr>
                                <tr>
                                    <td>07:20</td>
                                    <td>FL-023</td>
                                    <td>Return from rental</td>
                                    <td><span class="badge badge-primary">Returned</span></td>
                                    <td>Depot</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Maintenance Alerts
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">FL-012</div>
                                <small class="text-danger">Overdue maintenance</small>
                            </div>
                            <span class="badge bg-danger rounded-pill">High</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">FL-007</div>
                                <small class="text-warning">Due in 2 days</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">Medium</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">FL-019</div>
                                <small class="text-info">Scheduled next week</small>
                            </div>
                            <span class="badge bg-info rounded-pill">Low</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="scheduleMaintenanceModal()">
                            <i class="fas fa-calendar-plus me-2"></i>Schedule Maintenance
                        </button>
                        <button class="btn btn-success" onclick="deployUnitModal()">
                            <i class="fas fa-truck-moving me-2"></i>Deploy Unit
                        </button>
                        <button class="btn btn-info" onclick="generateReport()">
                            <i class="fas fa-file-alt me-2"></i>Generate Report
                        </button>
                        <button class="btn btn-warning" onclick="viewMaintenanceSchedule()">
                            <i class="fas fa-calendar-check me-2"></i>View Schedule
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#activitiesTable').DataTable({
        responsive: true,
        pageLength: 5,
        order: [[0, 'desc']],
        dom: 'rtip'
    });

    // Initialize Charts
    initializeCharts();
});

function initializeCharts() {
    // Utilization Chart
    const utilizationCtx = document.getElementById('utilizationChart').getContext('2d');
    new Chart(utilizationCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Utilization Rate (%)',
                data: [65, 72, 68, 75, 82, 78, 85, 88, 76, 79, 83, 81],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Maintenance', 'Disewa', 'Idle'],
            datasets: [{
                data: [<?= $rolling_stats['active_units'] ?? 0 ?>, <?= $rolling_stats['in_maintenance'] ?? 0 ?>, <?= $rolling_stats['rented_units'] ?? 0 ?>, 5],
                backgroundColor: ['#1cc88a', '#f6c23e', '#36b9cc', '#e74a3b'],
                borderWidth: 0
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

    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: ['FL-001', 'FL-002', 'FL-003', 'FL-004', 'FL-005', 'FL-006'],
            datasets: [{
                label: 'Hours Worked',
                data: [240, 180, 220, 160, 200, 190],
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Downtime Chart
    const downtimeCtx = document.getElementById('downtimeChart').getContext('2d');
    new Chart(downtimeCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Downtime Hours',
                data: [12, 8, 15, 6],
                borderColor: '#e74a3b',
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function exportDashboard() {
    alert('Export dashboard functionality will be implemented');
}

function exportChart(chartId) {
    alert('Export chart functionality will be implemented');
}

function viewChartDetails(type) {
    alert('View chart details functionality will be implemented');
}

function scheduleMaintenanceModal() {
    alert('Schedule maintenance modal will be implemented');
}

function deployUnitModal() {
    alert('Deploy unit modal will be implemented');
}

function generateReport() {
    alert('Generate report functionality will be implemented');
}

function viewMaintenanceSchedule() {
    alert('View maintenance schedule functionality will be implemented');
}
</script>

<?= $this->endSection() ?> 