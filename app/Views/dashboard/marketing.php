<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid" aria-label="Dashboard Marketing" role="region">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" id="pageTitle">
            <i class="fas fa-chart-line me-2" aria-hidden="true"></i>Dashboard Marketing
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="me-3">
                <small class="text-muted">Terakhir diperbarui: </small>
                <span class="fw-bold" aria-live="polite"><?= date('d M Y, H:i') ?></span>
            </div>
            <div class="btn-group" role="group" aria-label="Aksi Marketing">
                <button class="btn btn-outline-primary btn-sm" onclick="exportMarketingDashboard()" aria-label="Export Dashboard"><i class="fas fa-download me-1" aria-hidden="true"></i>Export</button>
                <button class="btn btn-outline-success btn-sm" onclick="generateMarketingReport()" aria-label="Generate Report"><i class="fas fa-file-alt me-1" aria-hidden="true"></i>Report</button>
                <button class="btn btn-primary btn-sm" onclick="location.reload()" aria-label="Refresh Dashboard"><i class="fas fa-sync-alt me-1" aria-hidden="true"></i>Refresh</button>
            </div>
        </div>
    </div>

    <!-- Sales Performance KPIs - Professional Standard -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-primary text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-1" data-currency="<?= $marketing_stats['monthly_revenue'] ?? 0 ?>">Rp 0</h2>
                        <h6 class="card-title text-uppercase small mb-0">REVENUE BULAN INI</h6>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-arrow-up me-1"></i>+15.3% dari bulan lalu
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-success text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-1"><?= $marketing_stats['active_contracts'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small mb-0">KONTRAK AKTIF</h6>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-handshake me-1"></i><?= round(($marketing_stats['active_contracts'] ?? 0) / max(($marketing_stats['total_contracts'] ?? 1), 1) * 100, 1) ?>% dari total
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-handshake fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-warning text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-1"><?= $marketing_stats['pending_quotations'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small mb-0">PENAWARAN PENDING</h6>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-clock me-1"></i>Butuh follow-up
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-file-invoice fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-info text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-1">68.5%</h2>
                        <h6 class="card-title text-uppercase small mb-0">CONVERSION RATE</h6>
                        <div class="small mt-1 opacity-75">
                            <i class="fas fa-percentage me-1"></i>Target: 70%
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-chart-pie fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Performance Charts -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>Sales Performance Trend
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exportChart('salesChart')">
                                <i class="fas fa-download me-2"></i>Export Chart
                            </a>
                            <a class="dropdown-item" href="#" onclick="viewSalesDetails()">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart" style="height: 300px;"></canvas>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">Target bulanan: <strong>Rp 2.5M</strong> | Pencapaian: <strong>Rp 2.1M (84%)</strong></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Lead Sources
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="leadSourceChart" style="height: 200px;"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <div class="row">
                            <div class="col-6">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-primary"></i> Website
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-success"></i> Referral
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-warning"></i> Cold Call
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-info"></i> Social Media
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Funnel & Pipeline -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-funnel-dollar me-2"></i>Sales Funnel
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="salesFunnelChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Monthly Target vs Achievement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="targetChart" style="height: 250px;"></canvas>
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
                        <i class="fas fa-list me-2"></i>Recent Sales Activities
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="salesActivitiesTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Activity</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Sales Rep</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2024-01-15</td>
                                    <td>PT ABC Construction</td>
                                    <td>Contract Signed</td>
                                    <td>Rp 250,000,000</td>
                                    <td><span class="badge badge-success" role="status" aria-label="Closed Won">Closed Won</span></td>
                                    <td>John Doe</td>
                                </tr>
                                <tr>
                                    <td>2024-01-14</td>
                                    <td>CV XYZ Logistics</td>
                                    <td>Quotation Sent</td>
                                    <td>Rp 150,000,000</td>
                                    <td><span class="badge badge-warning" role="status" aria-label="Pending">Pending</span></td>
                                    <td>Jane Smith</td>
                                </tr>
                                <tr>
                                    <td>2024-01-13</td>
                                    <td>PT DEF Manufacturing</td>
                                    <td>Follow-up Call</td>
                                    <td>Rp 300,000,000</td>
                                    <td><span class="badge badge-info" role="status" aria-label="In Progress">In Progress</span></td>
                                    <td>Mike Johnson</td>
                                </tr>
                                <tr>
                                    <td>2024-01-12</td>
                                    <td>UD GHI Trading</td>
                                    <td>Initial Meeting</td>
                                    <td>Rp 80,000,000</td>
                                    <td><span class="badge badge-primary" role="status" aria-label="Qualified">Qualified</span></td>
                                    <td>Sarah Wilson</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Top Performers -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-trophy me-2"></i>Top Performers
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">John Doe</div>
                                <small class="text-muted">15 deals closed</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">Rp 750M</div>
                                <small class="text-muted">120% target</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">Jane Smith</div>
                                <small class="text-muted">12 deals closed</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">Rp 680M</div>
                                <small class="text-muted">108% target</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">Mike Johnson</div>
                                <small class="text-muted">10 deals closed</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">Rp 620M</div>
                                <small class="text-muted">98% target</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pipeline Alerts -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Pipeline Alerts
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">PT ABC Construction</div>
                                <small class="text-danger">Follow-up overdue</small>
                            </div>
                            <span class="badge bg-danger rounded-pill">High</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">CV XYZ Logistics</div>
                                <small class="text-warning">Proposal expires in 2 days</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">Medium</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">PT DEF Manufacturing</div>
                                <small class="text-info">Meeting scheduled</small>
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
                        <button class="btn btn-primary" onclick="createQuotationModal()">
                            <i class="fas fa-file-invoice me-2"></i>Create Quotation
                        </button>
                        <button class="btn btn-success" onclick="addLeadModal()">
                            <i class="fas fa-user-plus me-2"></i>Add Lead
                        </button>
                        <button class="btn btn-info" onclick="viewPipelineModal()">
                            <i class="fas fa-funnel-dollar me-2"></i>View Pipeline
                        </button>
                        <button class="btn btn-warning" onclick="followUpReminders()">
                            <i class="fas fa-bell me-2"></i>Follow-up Reminders
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
    $('#salesActivitiesTable').DataTable({
        responsive: true,
        pageLength: 5,
        order: [[0, 'desc']],
        dom: 'rtip'
    });

    // Initialize Charts
    initializeMarketingCharts();
});

function initializeMarketingCharts() {
    // Sales Performance Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue (Millions)',
                data: [1.8, 2.1, 1.9, 2.3, 2.7, 2.4, 2.8, 3.1, 2.6, 2.9, 3.2, 2.1],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Target',
                data: [2.5, 2.5, 2.5, 2.5, 2.5, 2.5, 2.5, 2.5, 2.5, 2.5, 2.5, 2.5],
                borderColor: '#e74a3b',
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [5, 5],
                fill: false
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
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value + 'M';
                        }
                    }
                }
            }
        }
    });

    // Lead Source Chart
    const leadCtx = document.getElementById('leadSourceChart').getContext('2d');
    new Chart(leadCtx, {
        type: 'doughnut',
        data: {
            labels: ['Website', 'Referral', 'Cold Call', 'Social Media'],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#36b9cc'],
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

    // Sales Funnel Chart
    const funnelCtx = document.getElementById('salesFunnelChart').getContext('2d');
    new Chart(funnelCtx, {
        type: 'bar',
        data: {
            labels: ['Leads', 'Qualified', 'Proposal', 'Negotiation', 'Closed Won'],
            datasets: [{
                label: 'Count',
                data: [150, 95, 65, 45, 32],
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a', 
                    '#f6c23e',
                    '#36b9cc',
                    '#5a5c69'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Target vs Achievement Chart
    const targetCtx = document.getElementById('targetChart').getContext('2d');
    new Chart(targetCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Target',
                data: [2.5, 2.5, 2.5, 2.5, 2.5, 2.5],
                backgroundColor: 'rgba(231, 74, 59, 0.3)',
                borderColor: '#e74a3b',
                borderWidth: 1
            }, {
                label: 'Achievement',
                data: [1.8, 2.1, 1.9, 2.3, 2.7, 2.1],
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value + 'M';
                        }
                    }
                }
            }
        }
    });
}

function exportMarketingDashboard() {
    alert('Export marketing dashboard functionality will be implemented');
}

function generateMarketingReport() {
    alert('Generate marketing report functionality will be implemented');
}

function exportChart(chartId) {
    alert('Export chart functionality will be implemented');
}

function viewSalesDetails() {
    alert('View sales details functionality will be implemented');
}

function createQuotationModal() {
    alert('Create quotation modal will be implemented');
}

function addLeadModal() {
    alert('Add lead modal will be implemented');
}

function viewPipelineModal() {
    alert('View pipeline modal will be implemented');
}

function followUpReminders() {
    alert('Follow-up reminders functionality will be implemented');
}
</script>

<?= $this->endSection() ?> 