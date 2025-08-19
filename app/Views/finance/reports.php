<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar me-2"></i>Financial Reports
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshReports()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="exportAllReports()">
                    <i class="fas fa-file-excel me-1"></i>Export All
                </button>
                <button class="btn btn-primary btn-sm" onclick="generateCustomReport()">
                    <i class="fas fa-plus me-1"></i>Custom Report
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Report Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-gradient-primary text-white shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                Profit & Loss</div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                Generate P&L Report
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-light btn-sm" onclick="generateReport('profit_loss')">
                            <i class="fas fa-download me-1"></i>Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-gradient-success text-white shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                Cash Flow</div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                Cash Flow Statement
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-light btn-sm" onclick="generateReport('cash_flow')">
                            <i class="fas fa-download me-1"></i>Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-gradient-info text-white shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                Balance Sheet</div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                Assets & Liabilities
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-light btn-sm" onclick="generateReport('balance_sheet')">
                            <i class="fas fa-download me-1"></i>Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-gradient-warning text-white shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                Budget Analysis</div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                Budget vs Actual
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-light btn-sm" onclick="generateReport('budget_analysis')">
                            <i class="fas fa-download me-1"></i>Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Performance Charts -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue vs Expenses Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueExpenseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Expense Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="expenseBreakdownChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Operational
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Maintenance
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Administrative
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="row">
        <!-- Income Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-arrow-up me-2"></i>Income Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Monthly Revenue Report</h6>
                                <p class="mb-1 text-muted">Detailed breakdown of monthly income</p>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="generateReport('monthly_revenue')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Customer Revenue Analysis</h6>
                                <p class="mb-1 text-muted">Revenue by customer and contract type</p>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="generateReport('customer_revenue')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Service Revenue Report</h6>
                                <p class="mb-1 text-muted">Revenue from rental and service operations</p>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="generateReport('service_revenue')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-arrow-down me-2"></i>Expense Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Operational Expenses</h6>
                                <p class="mb-1 text-muted">Daily operational costs and expenses</p>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="generateReport('operational_expenses')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Maintenance Costs</h6>
                                <p class="mb-1 text-muted">Equipment maintenance and repair costs</p>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="generateReport('maintenance_costs')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Administrative Expenses</h6>
                                <p class="mb-1 text-muted">Office and administrative costs</p>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="generateReport('admin_expenses')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-chart-line me-2"></i>Analysis Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Profitability Analysis</h6>
                                <p class="mb-1 text-muted">Profit margins and ROI analysis</p>
                            </div>
                            <button class="btn btn-sm btn-outline-info" onclick="generateReport('profitability')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Cost Center Analysis</h6>
                                <p class="mb-1 text-muted">Cost allocation by department</p>
                            </div>
                            <button class="btn btn-sm btn-outline-info" onclick="generateReport('cost_center')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Trend Analysis</h6>
                                <p class="mb-1 text-muted">Financial trends and forecasting</p>
                            </div>
                            <button class="btn btn-sm btn-outline-info" onclick="generateReport('trend_analysis')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax & Compliance Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-file-alt me-2"></i>Tax & Compliance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">VAT Report</h6>
                                <p class="mb-1 text-muted">Value Added Tax calculations</p>
                            </div>
                            <button class="btn btn-sm btn-outline-warning" onclick="generateReport('vat_report')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Tax Summary</h6>
                                <p class="mb-1 text-muted">Annual tax summary and obligations</p>
                            </div>
                            <button class="btn btn-sm btn-outline-warning" onclick="generateReport('tax_summary')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Audit Trail</h6>
                                <p class="mb-1 text-muted">Financial transaction audit log</p>
                            </div>
                            <button class="btn btn-sm btn-outline-warning" onclick="generateReport('audit_trail')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Report Modal -->
<div class="modal fade" id="customReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Generate Custom Financial Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customReportForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reportType" class="form-label">Report Type *</label>
                                <select class="form-select" id="reportType" required>
                                    <option value="">Select Report Type</option>
                                    <option value="profit_loss">Profit & Loss</option>
                                    <option value="cash_flow">Cash Flow</option>
                                    <option value="balance_sheet">Balance Sheet</option>
                                    <option value="budget_analysis">Budget Analysis</option>
                                    <option value="revenue_analysis">Revenue Analysis</option>
                                    <option value="expense_analysis">Expense Analysis</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reportPeriod" class="form-label">Period *</label>
                                <select class="form-select" id="reportPeriod" required>
                                    <option value="">Select Period</option>
                                    <option value="current_month">Current Month</option>
                                    <option value="last_month">Last Month</option>
                                    <option value="current_quarter">Current Quarter</option>
                                    <option value="last_quarter">Last Quarter</option>
                                    <option value="current_year">Current Year</option>
                                    <option value="last_year">Last Year</option>
                                    <option value="custom">Custom Date Range</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="customDateRange" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reportFormat" class="form-label">Format *</label>
                                <select class="form-select" id="reportFormat" required>
                                    <option value="">Select Format</option>
                                    <option value="pdf">PDF Document</option>
                                    <option value="excel">Excel Spreadsheet</option>
                                    <option value="csv">CSV File</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reportDetail" class="form-label">Detail Level</label>
                                <select class="form-select" id="reportDetail">
                                    <option value="summary">Summary</option>
                                    <option value="detailed">Detailed</option>
                                    <option value="comprehensive">Comprehensive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="generateCustomReportNow()">
                    <i class="fas fa-cog me-2"></i>Generate Report
                </button>
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
    
    $('#reportPeriod').change(function() {
        if ($(this).val() === 'custom') {
            $('#customDateRange').show();
        } else {
            $('#customDateRange').hide();
        }
    });
});

function initializeCharts() {
    // Revenue vs Expense Chart
    const revenueExpenseCtx = document.getElementById('revenueExpenseChart').getContext('2d');
    
    new Chart(revenueExpenseCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: [250, 280, 320, 290, 310, 340, 360, 380, 350, 390, 410, 450],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 2,
                fill: true
            }, {
                label: 'Expenses',
                data: [120, 140, 160, 130, 150, 170, 180, 190, 175, 195, 205, 225],
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
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value + 'M';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.parsed.y + 'M';
                        }
                    }
                }
            }
        }
    });

    // Expense Breakdown Chart
    const expenseBreakdownCtx = document.getElementById('expenseBreakdownChart').getContext('2d');
    
    new Chart(expenseBreakdownCtx, {
        type: 'doughnut',
        data: {
            labels: ['Operational', 'Maintenance', 'Administrative'],
            datasets: [{
                data: [60, 25, 15],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
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

function generateReport(reportType) {
    showNotification('Generating ' + reportType.replace('_', ' ') + ' report...', 'info');
    
    // Simulate report generation
    setTimeout(() => {
        showNotification('Report generated successfully!', 'success');
    }, 2000);
}

function generateCustomReport() {
    $('#customReportModal').modal('show');
}

function generateCustomReportNow() {
    if (!$('#customReportForm')[0].checkValidity()) {
        $('#customReportForm')[0].reportValidity();
        return;
    }

    const formData = {
        type: $('#reportType').val(),
        period: $('#reportPeriod').val(),
        start_date: $('#startDate').val(),
        end_date: $('#endDate').val(),
        format: $('#reportFormat').val(),
        detail: $('#reportDetail').val()
    };

    $('#customReportModal').modal('hide');
    showNotification('Generating custom financial report...', 'info');
    
    // Simulate custom report generation
    setTimeout(() => {
        showNotification('Custom report generated successfully!', 'success');
    }, 3000);
}

function refreshReports() {
    showNotification('Refreshing financial reports...', 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function exportAllReports() {
    showNotification('Exporting all financial reports...', 'info');
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