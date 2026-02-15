<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-3">
        <h4 class="fw-bold mb-1">
            <i class="bi bi-graph-up me-2 text-primary"></i>
            Reports Dashboard
        </h4>
        <p class="text-muted mb-0">Access, generate, and export various reports for business insights and analysis</p>
    </div>
    <!-- Old header section removed for consistency -->
    <div class="row mt-3">
        <div class="col-md-12 text-end mb-3">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshReports()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="scheduleReport()">
                    <i class="fas fa-clock me-1"></i>Schedule Report
                </button>
                <button class="btn btn-primary btn-sm" onclick="generateCustomReport()">
                    <i class="fas fa-plus me-1"></i>Custom Report
                </button>
            </div>
        </div>
    </div>

    <!-- Report Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Reports</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $report_stats['total_reports'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                                Completed Reports</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $report_stats['completed_reports'] ?? 0 ?>
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
                                Pending Reports</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $report_stats['pending_reports'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
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
                                This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $report_stats['this_month_reports'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reports -->
    <div class="row">
        <!-- Rental Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-handshake me-2"></i>Rental Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Monthly Rental Summary</h6>
                                <p class="mb-1 text-muted">Overview of all rental activities this month</p>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="generateQuickReport('rental_monthly')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Contract Performance</h6>
                                <p class="mb-1 text-muted">Analysis of contract completion rates</p>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="generateQuickReport('contract_performance')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Unit Utilization</h6>
                                <p class="mb-1 text-muted">Equipment usage and availability metrics</p>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="generateQuickReport('unit_utilization')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-dollar-sign me-2"></i>Financial Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Revenue Report</h6>
                                <p class="mb-1 text-muted">Monthly and yearly revenue analysis</p>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="generateQuickReport('revenue')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Expense Tracking</h6>
                                <p class="mb-1 text-muted">Operational and maintenance expenses</p>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="generateQuickReport('expenses')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Profit & Loss</h6>
                                <p class="mb-1 text-muted">Comprehensive P&L statement</p>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="generateQuickReport('profit_loss')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-wrench me-2"></i>Maintenance Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Maintenance Schedule</h6>
                                <p class="mb-1 text-muted">Upcoming and overdue maintenance</p>
                            </div>
                            <button class="btn btn-sm btn-outline-warning" onclick="generateQuickReport('maintenance_schedule')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Work Order Summary</h6>
                                <p class="mb-1 text-muted">Completed and pending work orders</p>
                            </div>
                            <button class="btn btn-sm btn-outline-warning" onclick="generateQuickReport('work_orders')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Downtime Analysis</h6>
                                <p class="mb-1 text-muted">Equipment downtime and causes</p>
                            </div>
                            <button class="btn btn-sm btn-outline-warning" onclick="generateQuickReport('downtime')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-boxes me-2"></i>Inventory Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Stock Level Report</h6>
                                <p class="mb-1 text-muted">Current inventory levels and alerts</p>
                            </div>
                            <button class="btn btn-sm btn-outline-info" onclick="generateQuickReport('stock_levels')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Sparepart Usage</h6>
                                <p class="mb-1 text-muted">Sparepart consumption analysis</p>
                            </div>
                            <button class="btn btn-sm btn-outline-info" onclick="generateQuickReport('sparepart_usage')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Asset Valuation</h6>
                                <p class="mb-1 text-muted">Current asset values and depreciation</p>
                            </div>
                            <button class="btn btn-sm btn-outline-info" onclick="generateQuickReport('asset_valuation')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Recent Reports</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow">
                    <a class="dropdown-item" href="#" onclick="exportReportsList()">
                        <i class="fas fa-download me-2"></i>Export List
                    </a>
                    <a class="dropdown-item" href="#" onclick="clearOldReports()">
                        <i class="fas fa-trash me-2"></i>Clear Old Reports
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="reportsTable">
                    <thead>
                        <tr>
                            <th>Report Name</th>
                            <th>Type</th>
                            <th>Generated By</th>
                            <th>Date Created</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($recent_reports) && is_array($recent_reports) && !empty($recent_reports)): ?>
                            <?php foreach ($recent_reports as $report): ?>
                                <tr>
                                    <td><?= esc($report['name']) ?></td>
                                    <td>
                                        <span class="badge badge-secondary"><?= esc($report['type']) ?></span>
                                    </td>
                                    <td><?= esc($report['first_name'] . ' ' . $report['last_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = $report['status'] == 'completed' ? 'success' : 
                                                      ($report['status'] == 'generating' ? 'warning' : 'secondary');
                                        ?>
                                        <span class="badge badge-<?= $statusClass ?>">
                                            <?= ucfirst($report['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($report['status'] == 'completed'): ?>
                                            <button class="btn btn-sm btn-primary" onclick="downloadReport(<?= $report['id'] ?>)" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" onclick="viewReport(<?= $report['id'] ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-danger" onclick="deleteReport(<?= $report['id'] ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No reports found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Custom Report Modal -->
<div class="modal fade" id="customReportModal" tabindex="-1" aria-labelledby="customReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customReportModalLabel">
                    <i class="fas fa-plus me-2"></i>Generate Custom Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customReportForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reportType" class="form-label">Report Type *</label>
                                <select class="form-select" id="reportType" required>
                                    <option value="">Select Report Type</option>
                                    <option value="rental_monthly">Monthly Rental Summary</option>
                                    <option value="contract_performance">Contract Performance</option>
                                    <option value="unit_utilization">Unit Utilization</option>
                                    <option value="revenue">Revenue Report</option>
                                    <option value="expenses">Expense Report</option>
                                    <option value="profit_loss">Profit & Loss</option>
                                    <option value="maintenance_schedule">Maintenance Schedule</option>
                                    <option value="work_orders">Work Order Summary</option>
                                    <option value="downtime">Downtime Analysis</option>
                                    <option value="stock_levels">Stock Level Report</option>
                                    <option value="sparepart_usage">Sparepart Usage</option>
                                    <option value="asset_valuation">Asset Valuation</option>
                                </select>
                            </div>
                        </div>
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
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dateFrom" class="form-label">Date From *</label>
                                <input type="date" class="form-control" id="dateFrom" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dateTo" class="form-label">Date To *</label>
                                <input type="date" class="form-control" id="dateTo" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reportName" class="form-label">Report Name *</label>
                        <input type="text" class="form-control" id="reportName" required 
                               placeholder="Enter a descriptive name for this report">
                    </div>
                    <div class="mb-3">
                        <label for="reportDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="reportDescription" rows="3" 
                                  placeholder="Optional description for this report"></textarea>
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

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 id="loadingMessage">Generating report...</h5>
                <p class="text-muted mb-0">Please wait while we process your request</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#reportsTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[3, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    // Set default dates
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    
    $('#dateFrom').val(firstDay.toISOString().split('T')[0]);
    $('#dateTo').val(today.toISOString().split('T')[0]);

    // Auto-generate report name based on type
    $('#reportType').change(function() {
        const type = $(this).val();
        const typeName = $(this).find('option:selected').text();
        const date = new Date().toLocaleDateString('id-ID');
        
        if (type) {
            $('#reportName').val(typeName + ' - ' + date);
        }
    });
});

// Report Generation Functions
function generateQuickReport(type) {
    showLoading('Generating quick report...');
    
    $.ajax({
        url: '<?= base_url('reports/quick/') ?>' + type,
        method: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            hideLoading();
            
            if (response.success) {
                showNotification('Report generated successfully!', 'success');
                
                // Auto-download the report
                if (response.download_url) {
                    window.open(response.download_url, '_blank');
                }
                
                // Refresh the reports table
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showNotification('Failed to generate report: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            showNotification('Error generating report: ' + error, 'error');
        }
    });
}

function generateCustomReport() {
    $('#customReportModal').modal('show');
}

function generateCustomReportNow() {
    // Validate form
    if (!$('#customReportForm')[0].checkValidity()) {
        $('#customReportForm')[0].reportValidity();
        return;
    }

    const formData = {
        type: $('#reportType').val(),
        format: $('#reportFormat').val(),
        date_from: $('#dateFrom').val(),
        date_to: $('#dateTo').val(),
        report_name: $('#reportName').val(),
        description: $('#reportDescription').val()
    };

    $('#customReportModal').modal('hide');
    showLoading('Generating custom report...');

    $.ajax({
        url: '<?= base_url('reports/generate') ?>',
        method: 'POST',
        data: formData,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            hideLoading();
            
            if (response.success) {
                showNotification('Custom report generated successfully!', 'success');
                
                // Auto-download the report
                if (response.download_url) {
                    window.open(response.download_url, '_blank');
                }
                
                // Refresh the reports table
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showNotification('Failed to generate custom report: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            showNotification('Error generating custom report: ' + error, 'error');
        }
    });
}

// Report Management Functions
function downloadReport(id) {
    window.open('<?= base_url('reports/download/') ?>' + id, '_blank');
}

function viewReport(id) {
    window.open('<?= base_url('reports/view/') ?>' + id, '_blank');
}

function deleteReport(id) {
    if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        $.ajax({
            url: '<?= base_url('reports/delete/') ?>' + id,
            method: 'DELETE',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Report deleted successfully!', 'success');
                    location.reload();
                } else {
                    showNotification('Failed to delete report: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotification('Error deleting report: ' + error, 'error');
            }
        });
    }
}

// Utility Functions
function refreshReports() {
    showLoading('Refreshing reports...');
    location.reload();
}

function scheduleReport() {
    showNotification('Report scheduling feature coming soon!', 'info');
}

function exportReportsList() {
    $('#reportsTable').DataTable().button('.buttons-excel').trigger();
}

function clearOldReports() {
    if (confirm('Are you sure you want to clear old reports? This will remove reports older than 30 days.')) {
        showNotification('Clear old reports feature coming soon!', 'info');
    }
}

function showLoading(message) {
    $('#loadingMessage').text(message);
    $('#loadingModal').modal('show');
}

function hideLoading() {
    $('#loadingModal').modal('hide');
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
    
    // Insert notification at the top of the container
    $('.container-fluid').prepend(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
<?= $this->endSection() ?> 