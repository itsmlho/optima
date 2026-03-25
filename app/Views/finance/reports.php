<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar me-2"></i><?= lang('Finance.financial_reports') ?>
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshReports()">
                    <i class="fas fa-sync-alt me-1"></i><?= lang('Finance.refresh') ?>
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="exportAllReports()">
                    <i class="fas fa-file-excel me-1"></i><?= lang('Finance.export_all') ?>
                </button>
                <button class="btn btn-primary btn-sm" onclick="generateCustomReport()">
                    <i class="fas fa-plus me-1"></i><?= lang('Finance.custom_report') ?>
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
                                <?= lang('Finance.report_profit_loss_short') ?></div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                <?= lang('Finance.report_generate_pl') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-light btn-sm" onclick="generateReport('profit_loss')">
                            <i class="fas fa-download me-1"></i><?= lang('Finance.generate') ?>
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
                                <?= lang('Finance.report_cash_flow') ?></div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                <?= lang('Finance.report_cash_flow_statement') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-light btn-sm" onclick="generateReport('cash_flow')">
                            <i class="fas fa-download me-1"></i><?= lang('Finance.generate') ?>
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
                                <?= lang('Finance.report_balance_sheet') ?></div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                <?= lang('Finance.report_assets_liabilities') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-light btn-sm" onclick="generateReport('balance_sheet')">
                            <i class="fas fa-download me-1"></i><?= lang('Finance.generate') ?>
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
                                <?= lang('Finance.report_budget_analysis') ?></div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                <?= lang('Finance.report_budget_vs_actual') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-light btn-sm" onclick="generateReport('budget_analysis')">
                            <i class="fas fa-download me-1"></i><?= lang('Finance.generate') ?>
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
                    <h6 class="m-0 font-weight-bold text-primary"><?= lang('Finance.chart_revenue_vs_expenses') ?></h6>
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
                    <h6 class="m-0 font-weight-bold text-primary"><?= lang('Finance.chart_expense_breakdown') ?></h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="expenseBreakdownChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> <?= lang('Finance.expense_cat_operational') ?>
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> <?= lang('Finance.expense_cat_maintenance') ?>
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> <?= lang('Finance.expense_cat_administrative') ?>
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
                        <i class="fas fa-arrow-up me-2"></i><?= lang('Finance.reports_income_section') ?>
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
                                <h6 class="mb-1"><?= lang('Finance.report_customer_revenue') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_customer_revenue_desc') ?></p>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="generateReport('customer_revenue')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_service_revenue') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_service_revenue_desc') ?></p>
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
                        <i class="fas fa-arrow-down me-2"></i><?= lang('Finance.reports_expense_section') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_operational_expenses') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_operational_expenses_desc') ?></p>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="generateReport('operational_expenses')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_maintenance_costs') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_maintenance_costs_desc') ?></p>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="generateReport('maintenance_costs')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_admin_expenses') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_admin_expenses_desc') ?></p>
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
                        <i class="fas fa-chart-line me-2"></i><?= lang('Finance.reports_analysis_section') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_profitability') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_profitability_desc') ?></p>
                            </div>
                            <button class="btn btn-sm btn-outline-info" onclick="generateReport('profitability')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_cost_center') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_cost_center_desc') ?></p>
                            </div>
                            <button class="btn btn-sm btn-outline-info" onclick="generateReport('cost_center')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_trend_analysis') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_trend_analysis_desc') ?></p>
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
                        <i class="fas fa-file-alt me-2"></i><?= lang('Finance.reports_tax_section') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_vat') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_vat_desc') ?></p>
                            </div>
                            <button class="btn btn-sm btn-outline-warning" onclick="generateReport('vat_report')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_tax_summary') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_tax_summary_desc') ?></p>
                            </div>
                            <button class="btn btn-sm btn-outline-warning" onclick="generateReport('tax_summary')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= lang('Finance.report_audit_trail') ?></h6>
                                <p class="mb-1 text-muted"><?= lang('Finance.report_audit_trail_desc') ?></p>
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i><?= lang('Finance.generate_custom_financial_report') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customReportForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reportType" class="form-label"><?= lang('Finance.report_type_label') ?></label>
                                <select class="form-select" id="reportType" required>
                                    <option value=""><?= lang('Finance.select_report_type') ?></option>
                                    <option value="profit_loss"><?= lang('Finance.report_profit_loss_short') ?></option>
                                    <option value="cash_flow"><?= lang('Finance.report_cash_flow') ?></option>
                                    <option value="balance_sheet"><?= lang('Finance.report_balance_sheet') ?></option>
                                    <option value="budget_analysis"><?= lang('Finance.report_budget_analysis') ?></option>
                                    <option value="revenue_analysis"><?= lang('Finance.report_revenue_analysis') ?></option>
                                    <option value="expense_analysis"><?= lang('Finance.report_expense_analysis') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reportPeriod" class="form-label"><?= lang('Finance.period_label') ?></label>
                                <select class="form-select" id="reportPeriod" required>
                                    <option value=""><?= lang('Finance.select_period') ?></option>
                                    <option value="current_month"><?= lang('Finance.period_current_month') ?></option>
                                    <option value="last_month"><?= lang('Finance.period_last_month') ?></option>
                                    <option value="current_quarter"><?= lang('Finance.period_current_quarter') ?></option>
                                    <option value="last_quarter"><?= lang('Finance.period_last_quarter') ?></option>
                                    <option value="current_year"><?= lang('Finance.period_current_year') ?></option>
                                    <option value="last_year"><?= lang('Finance.period_last_year') ?></option>
                                    <option value="custom"><?= lang('Finance.period_custom_range') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="customDateRange" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="startDate" class="form-label"><?= lang('Common.start_date') ?></label>
                                <input type="date" class="form-control" id="startDate">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endDate" class="form-label"><?= lang('Common.end_date') ?></label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reportFormat" class="form-label"><?= lang('Finance.format_label') ?></label>
                                <select class="form-select" id="reportFormat" required>
                                    <option value=""><?= lang('Finance.select_format') ?></option>
                                    <option value="pdf"><?= lang('Finance.format_pdf') ?></option>
                                    <option value="excel"><?= lang('Finance.format_excel') ?></option>
                                    <option value="csv"><?= lang('Finance.format_csv') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reportDetail" class="form-label"><?= lang('Finance.detail_level') ?></label>
                                <select class="form-select" id="reportDetail">
                                    <option value="summary"><?= lang('Finance.detail_summary') ?></option>
                                    <option value="detailed"><?= lang('Finance.detail_detailed') ?></option>
                                    <option value="comprehensive"><?= lang('Finance.detail_comprehensive') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-primary" onclick="generateCustomReportNow()">
                    <i class="fas fa-cog me-2"></i><?= lang('Finance.generate_report') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?php
$reportsJsI18n = [
    'revenue' => lang('Finance.chart_label_revenue'),
    'expenses' => lang('Finance.chart_label_expenses'),
    'op' => lang('Finance.expense_cat_operational'),
    'maint' => lang('Finance.expense_cat_maintenance'),
    'admin' => lang('Finance.expense_cat_administrative'),
    'chartMonths' => explode(',', lang('Finance.report_demo_month_labels')),
    'notifGenerating' => lang('Finance.notif_generating_report'),
    'notifReady' => lang('Finance.notif_report_ready'),
    'notifCustomGen' => lang('Finance.notif_custom_report_generating'),
    'notifCustomReady' => lang('Finance.notif_custom_report_ready'),
    'notifRefresh' => lang('Finance.notif_refreshing_reports'),
    'notifExport' => lang('Finance.notif_exporting_reports'),
    'typeInfo' => lang('Finance.notif_type_info'),
    'typeSuccess' => lang('Finance.notif_type_success'),
    'typeWarning' => lang('Finance.notif_type_warning'),
    'typeError' => lang('Finance.notif_type_error'),
];
?>
<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const reportsI18n = <?= json_encode($reportsJsI18n, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

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
            labels: reportsI18n.chartMonths,
            datasets: [{
                label: reportsI18n.revenue,
                data: [250, 280, 320, 290, 310, 340, 360, 380, 350, 390, 410, 450],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 2,
                fill: true
            }, {
                label: reportsI18n.expenses,
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
            labels: [reportsI18n.op, reportsI18n.maint, reportsI18n.admin],
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
    showNotification(reportsI18n.notifGenerating, 'info');
    
    // Simulate report generation
    setTimeout(() => {
        showNotification(reportsI18n.notifReady, 'success');
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
    showNotification(reportsI18n.notifCustomGen, 'info');
    
    // Simulate custom report generation
    setTimeout(() => {
        showNotification(reportsI18n.notifCustomReady, 'success');
    }, 3000);
}

function refreshReports() {
    showNotification(reportsI18n.notifRefresh, 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function exportAllReports() {
    showNotification(reportsI18n.notifExport, 'info');
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    const typeLabel = type === 'success' ? reportsI18n.typeSuccess :
        type === 'error' ? reportsI18n.typeError :
        type === 'warning' ? reportsI18n.typeWarning : reportsI18n.typeInfo;
    
    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <strong>${typeLabel}!</strong> ${message}
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