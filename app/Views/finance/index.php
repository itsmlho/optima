<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line me-2"></i>Financial Management
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshFinancialData()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <a href="<?= base_url('finance/invoices') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-invoice me-1"></i>Invoices
                </a>
                <a href="<?= base_url('finance/reports') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-chart-bar me-1"></i>Financial Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($financial_summary['total_revenue'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Total Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($financial_summary['total_expenses'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
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
                                Net Profit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($financial_summary['net_profit'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                Outstanding Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($financial_summary['outstanding_invoices'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="row">
        <!-- Monthly Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue Trend</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exportChart('revenue')">
                                <i class="fas fa-download me-2"></i>Export Chart
                            </a>
                            <a class="dropdown-item" href="#" onclick="printChart('revenue')">
                                <i class="fas fa-print me-2"></i>Print Chart
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Overview -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Financial Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="financialPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Revenue
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Expenses
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Profit
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions and Quick Actions -->
    <div class="row">
        <!-- Recent Transactions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Transactions</h6>
                    <a href="<?= base_url('finance/payments') ?>" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <?php foreach ($recent_transactions as $transaction): ?>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle <?= $transaction['type'] == 'Income' ? 'bg-success' : 'bg-danger' ?> me-3">
                                    <i class="fas <?= $transaction['type'] == 'Income' ? 'fa-arrow-up' : 'fa-arrow-down' ?> text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?= esc($transaction['description']) ?></h6>
                                    <small class="text-muted"><?= date('d M Y', strtotime($transaction['date'])) ?></small>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold <?= $transaction['type'] == 'Income' ? 'text-success' : 'text-danger' ?>">
                                    <?= $transaction['type'] == 'Income' ? '+' : '-' ?>Rp <?= number_format($transaction['amount'], 0, ',', '.') ?>
                                </div>
                                <span class="badge badge-<?= $transaction['status'] == 'Completed' ? 'success' : 'warning' ?>">
                                    <?= $transaction['status'] ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="<?= base_url('finance/invoices') ?>" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-file-invoice me-2"></i>
                                Create Invoice
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="<?= base_url('finance/payments') ?>" class="btn btn-outline-success btn-block">
                                <i class="fas fa-credit-card me-2"></i>
                                Record Payment
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="<?= base_url('finance/expenses') ?>" class="btn btn-outline-danger btn-block">
                                <i class="fas fa-receipt me-2"></i>
                                Add Expense
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="<?= base_url('finance/reports') ?>" class="btn btn-outline-info btn-block">
                                <i class="fas fa-chart-bar me-2"></i>
                                View Reports
                            </a>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="font-weight-bold mb-3">Financial Health Indicators</h6>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Cash Flow</span>
                                    <span class="font-weight-bold text-success">
                                        Rp <?= number_format($financial_summary['cash_flow'], 0, ',', '.') ?>
                                    </span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: 85%"></div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Pending Payments</span>
                                    <span class="font-weight-bold text-warning">
                                        Rp <?= number_format($financial_summary['pending_payments'], 0, ',', '.') ?>
                                    </span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: 35%"></div>
                                </div>
                            </div>
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
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const monthlyRevenue = <?= json_encode(array_values($monthly_revenue)) ?>;
    const months = <?= json_encode(array_keys($monthly_revenue)) ?>;
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue (Rp)',
                data: monthlyRevenue,
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
                            return 'Rp ' + value.toLocaleString();
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

    // Financial Pie Chart
    const pieCtx = document.getElementById('financialPieChart').getContext('2d');
    const financialData = [
        <?= $financial_summary['total_revenue'] ?>,
        <?= $financial_summary['total_expenses'] ?>,
        <?= $financial_summary['net_profit'] ?>
    ];
    
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Revenue', 'Expenses', 'Profit'],
            datasets: [{
                data: financialData,
                backgroundColor: ['#1cc88a', '#e74a3b', '#36b9cc'],
                hoverBackgroundColor: ['#17a673', '#e02d1b', '#2c9faf'],
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
                            return context.label + ': Rp ' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function refreshFinancialData() {
    showNotification('Refreshing financial data...', 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function exportChart(type) {
    showNotification('Exporting ' + type + ' chart...', 'info');
}

function printChart(type) {
    showNotification('Printing ' + type + ' chart...', 'info');
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