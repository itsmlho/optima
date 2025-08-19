<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-wrench me-2"></i>Service Division Dashboard
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshServiceData()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <a href="<?= base_url('service/work-orders') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-clipboard-list me-1"></i>Work Orders
                </a>
                <a href="<?= base_url('service/pmps') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-calendar-check me-1"></i>PMPS
                </a>
            </div>
        </div>
    </div>

    <!-- Service Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Active Work Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                24
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Completed Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                8
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
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue PMPS</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                3
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Units in Service</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                12
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Quick Access -->
    <div class="row">
        <!-- Work Order Status Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Work Order Status Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="workOrderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('service/work-orders') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Manage Work Orders
                        </a>
                        <a href="<?= base_url('service/pmps') ?>" class="btn btn-outline-success">
                            <i class="fas fa-calendar-check me-2"></i>
                            PMPS Schedule
                        </a>
                        <a href="<?= base_url('service/data-unit') ?>" class="btn btn-outline-info">
                            <i class="fas fa-database me-2"></i>
                            Unit Data
                        </a>
                        <a href="<?= base_url('service/work-orders/history') ?>" class="btn btn-outline-warning">
                            <i class="fas fa-history me-2"></i>
                            Service History
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="font-weight-bold mb-3">Service Performance</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Completion Rate</span>
                            <span class="font-weight-bold text-success">92%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: 92%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>On-Time Delivery</span>
                            <span class="font-weight-bold text-info">85%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: 85%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Customer Satisfaction</span>
                            <span class="font-weight-bold text-warning">88%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: 88%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities and Alerts -->
    <div class="row">
        <!-- Recent Work Orders -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Work Orders</h6>
                    <a href="<?= base_url('service/work-orders') ?>" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">WO-2024-001 - Engine Maintenance</h6>
                                <p class="mb-1 text-muted">Unit: FL-003 | Assigned to: John Doe</p>
                                <small class="text-muted">Created: 2 hours ago</small>
                            </div>
                            <span class="badge badge-warning">In Progress</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">WO-2024-002 - Hydraulic System</h6>
                                <p class="mb-1 text-muted">Unit: FL-007 | Assigned to: Jane Smith</p>
                                <small class="text-muted">Created: 4 hours ago</small>
                            </div>
                            <span class="badge badge-info">Pending</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">WO-2024-003 - Brake Inspection</h6>
                                <p class="mb-1 text-muted">Unit: FL-012 | Assigned to: Mike Johnson</p>
                                <small class="text-muted">Completed: 1 day ago</small>
                            </div>
                            <span class="badge badge-success">Completed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Alerts -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Maintenance Alerts</h6>
                    <a href="<?= base_url('service/pmps') ?>" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 text-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    FL-001 - Monthly Service
                                </h6>
                                <p class="mb-1 text-muted">Due: Tomorrow</p>
                                <small class="text-muted">Last service: 2023-12-16</small>
                            </div>
                            <span class="badge badge-danger">Overdue</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 text-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    FL-005 - 3-Month Inspection
                                </h6>
                                <p class="mb-1 text-muted">Due: Next Week</p>
                                <small class="text-muted">Last service: 2023-10-22</small>
                            </div>
                            <span class="badge badge-warning">Due Soon</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 text-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    FL-008 - Oil Change
                                </h6>
                                <p class="mb-1 text-muted">Due: In 2 weeks</p>
                                <small class="text-muted">Last service: 2023-11-15</small>
                            </div>
                            <span class="badge badge-info">Scheduled</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Technicians -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Service Technicians Status</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/80x80" class="rounded-circle mb-2" alt="Technician">
                            <h6 class="card-title">John Doe</h6>
                            <p class="card-text">Senior Technician</p>
                            <span class="badge badge-success">Available</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/80x80" class="rounded-circle mb-2" alt="Technician">
                            <h6 class="card-title">Jane Smith</h6>
                            <p class="card-text">Hydraulic Specialist</p>
                            <span class="badge badge-warning">Busy</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/80x80" class="rounded-circle mb-2" alt="Technician">
                            <h6 class="card-title">Mike Johnson</h6>
                            <p class="card-text">Engine Specialist</p>
                            <span class="badge badge-success">Available</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/80x80" class="rounded-circle mb-2" alt="Technician">
                            <h6 class="card-title">Sarah Wilson</h6>
                            <p class="card-text">Electrical Specialist</p>
                            <span class="badge badge-danger">Off Duty</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    initializeCharts();
});

function initializeCharts() {
    // Work Order Status Chart
    const workOrderCtx = document.getElementById('workOrderChart').getContext('2d');
    
    new Chart(workOrderCtx, {
        type: 'doughnut',
        data: {
            labels: ['In Progress', 'Pending', 'Completed', 'On Hold'],
            datasets: [{
                data: [8, 6, 15, 3],
                backgroundColor: ['#f6c23e', '#e74a3b', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#f4b619', '#e02d1b', '#17a673', '#2c9faf'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' orders';
                        }
                    }
                }
            }
        }
    });
}

function refreshServiceData() {
    showNotification('Refreshing service data...', 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container-fluid').prepend(notification);
    
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
<?= $this->endSection() ?> 