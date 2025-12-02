<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-bullhorn me-2"></i>Marketing Division Dashboard
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshMarketingData()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <a href="<?= base_url('marketing/quotations') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-contract me-1"></i>Quotations
                </a>
                <a href="<?= base_url('marketing/kontrak') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-handshake me-1"></i>Contracts
                </a>
            </div>
        </div>
    </div>

    <!-- Marketing Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Quotations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $marketing_stats['total_quotations'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
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
                                Pending Quotations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $marketing_stats['pending_quotations'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Active Contracts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $marketing_stats['active_contracts'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
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
                                Monthly Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($marketing_stats['monthly_revenue'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Performance -->
    <div class="row">
        <!-- Revenue Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marketing Performance -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Marketing Performance</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="performanceChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Conversion Rate
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Customer Satisfaction
                        </span>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="font-weight-bold mb-3">Key Metrics</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Conversion Rate</span>
                            <span class="font-weight-bold text-success"><?= $marketing_stats['conversion_rate'] ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?= $marketing_stats['conversion_rate'] ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Customer Satisfaction</span>
                            <span class="font-weight-bold text-info"><?= $marketing_stats['customer_satisfaction'] ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: <?= $marketing_stats['customer_satisfaction'] ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access and Recent Activities -->
    <div class="row">
        <!-- Quick Access -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Access</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="<?= base_url('marketing/quotations') ?>" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-file-contract me-2"></i>
                                Create Quotation
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="<?= base_url('marketing/kontrak') ?>" class="btn btn-outline-success btn-block">
                                <i class="fas fa-handshake me-2"></i>
                                Manage Contracts
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="<?= base_url('marketing/list-unit') ?>" class="btn btn-outline-info btn-block">
                                <i class="fas fa-list me-2"></i>
                                Unit Catalog
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="<?= base_url('marketing/unit-tersedia') ?>" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-check-circle me-2"></i>
                                Available Units
                            </a>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="font-weight-bold mb-3">Marketing Tools</h6>
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action" onclick="showPriceCalculator()">
                                    <i class="fas fa-calculator me-2"></i>
                                    Price Calculator
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="showCustomerDatabase()">
                                    <i class="fas fa-users me-2"></i>
                                    Customer Database
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="showMarketAnalysis()">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Market Analysis
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Quotations -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Quotations</h6>
                    <a href="<?= base_url('marketing/quotations') ?>" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <?php foreach ($recent_quotations as $quotation): ?>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="mb-1"><?= esc($quotation['id']) ?> - <?= esc($quotation['client']) ?></h6>
                                <p class="mb-1 text-muted"><?= esc($quotation['project']) ?></p>
                                <small class="text-muted">Value: Rp <?= number_format($quotation['value'], 0, ',', '.') ?></small>
                            </div>
                            <div class="text-right">
                                <?php
                                $statusClass = $quotation['status'] == 'Approved' ? 'success' : 
                                              ($quotation['status'] == 'Pending' ? 'warning' : 'secondary');
                                ?>
                                <span class="badge badge-<?= $statusClass ?>">
                                    <?= $quotation['status'] ?>
                                </span>
                                <br>
                                <small class="text-muted"><?= date('d M Y', strtotime($quotation['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Contracts -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Active Contracts</h6>
            <a href="<?= base_url('marketing/kontrak') ?>" class="btn btn-sm btn-primary">
                View All Contracts
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Contract Number</th>
                            <th>Client</th>
                            <th>Project</th>
                            <th>Value</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($active_contracts as $contract): ?>
                            <tr>
                                <td class="font-weight-bold"><?= esc($contract['contract_number']) ?></td>
                                <td><?= esc($contract['client']) ?></td>
                                <td><?= esc($contract['project']) ?></td>
                                <td>Rp <?= number_format($contract['value'], 0, ',', '.') ?></td>
                                <td><?= date('d M Y', strtotime($contract['start_date'])) ?></td>
                                <td><?= date('d M Y', strtotime($contract['end_date'])) ?></td>
                                <td>
                                    <span class="badge badge-success"><?= $contract['status'] ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewContract('<?= $contract['contract_number'] ?>')" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="editContract('<?= $contract['contract_number'] ?>')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?= json_encode(array_values($revenue_data)) ?>;
    const months = <?= json_encode(array_keys($revenue_data)) ?>;
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue (Rp)',
                data: revenueData,
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
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000) + 'M';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: Rp ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    
    new Chart(performanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Conversion Rate', 'Customer Satisfaction'],
            datasets: [{
                data: [<?= $marketing_stats['conversion_rate'] ?>, <?= $marketing_stats['customer_satisfaction'] ?>],
                backgroundColor: ['#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#17a673', '#2c9faf'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}

function refreshMarketingData() {
    showNotification('Refreshing marketing data...', 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function showPriceCalculator() {
    showNotification('Price calculator feature coming soon!', 'info');
}

function showCustomerDatabase() {
    window.location.href = '<?= base_url('customers') ?>';
}

function showMarketAnalysis() {
    showNotification('Market analysis feature coming soon!', 'info');
}

function viewContract(contractNumber) {
    showNotification('Opening contract ' + contractNumber, 'info');
}

// Ensure viewContract exists for compatibility with other views/scripts
if (typeof viewContract === 'undefined') {
    function viewContract(contractNumber) {
        window.location.href = '<?= base_url('marketing/kontrak') ?>?no_kontrak=' + encodeURIComponent(contractNumber);
    }
}

function editContract(contractNumber) {
    showNotification('Editing contract ' + contractNumber, 'info');
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