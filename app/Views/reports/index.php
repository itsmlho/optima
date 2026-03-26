<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<?php
$activeSection = $reports_active_section ?? '';
$sections = $reports_center_sections ?? [];
$customOpts = $custom_report_type_options ?? [];
?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-3">
        <h4 class="fw-bold mb-1">
            <i class="bi bi-graph-up me-2 text-primary"></i>
            <?= esc(lang('Reports.page_title_hub')) ?>
        </h4>
        <p class="text-muted mb-1"><?= esc(lang('Reports.hub_intro_body')) ?></p>
        <div class="btn-group btn-group-sm flex-wrap gap-1 mt-2" role="group" aria-label="Report sections">
            <a href="<?= base_url('reports') ?>" class="btn btn-sm <?= $activeSection === '' ? 'btn-primary' : 'btn-outline-primary' ?>"><?= esc(lang('Reports.hub_nav_all')) ?></a>
            <?php foreach ($sections as $secNav): ?>
                <?php
                $sk = $secNav['key'];
                $active = ($activeSection === $sk);
                $outline = 'btn-outline-' . ($secNav['header'] ?? 'secondary');
                $solid = 'btn-' . ($secNav['header'] ?? 'secondary');
                ?>
                <a href="<?= base_url('reports?section=' . esc($sk, 'url')) ?>" class="btn btn-sm <?= $active ? $solid : $outline ?>"><?= esc($secNav['title']) ?></a>
            <?php endforeach; ?>
            <?php if ($customOpts !== []): ?>
                <a href="<?= base_url('reports?section=custom') ?>" class="btn btn-sm <?= $activeSection === 'custom' ? 'btn-secondary' : 'btn-outline-secondary' ?>"><?= esc(lang('Reports.hub_nav_custom')) ?></a>
            <?php endif; ?>
        </div>
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
                <?php if ($customOpts !== []): ?>
                <button class="btn btn-primary btn-sm" onclick="generateCustomReport()">
                    <i class="fas fa-plus me-1"></i>Custom Report
                </button>
                <?php endif; ?>
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
                                <?= esc(lang('Reports.stats_total')) ?></div>
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
                                <?= esc(lang('Reports.stats_completed')) ?></div>
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
                                <?= esc(lang('Reports.stats_pending')) ?></div>
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
                                <?= esc(lang('Reports.stats_this_month')) ?></div>
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

    <?php if ($sections === [] && $activeSection !== 'custom'): ?>
    <div class="alert alert-info"><?= esc(lang('Reports.no_segments_for_role')) ?></div>
    <?php elseif ($activeSection === 'custom' && $customOpts !== []): ?>
    <div class="card shadow mb-4" id="reports-section-custom">
        <div class="card-body">
            <h5 class="fw-bold mb-2"><?= esc(lang('Reports.custom_page_title')) ?></h5>
            <p class="text-muted mb-3"><?= esc(lang('Reports.custom_intro')) ?></p>
            <button type="button" class="btn btn-primary" onclick="generateCustomReport()">
                <i class="fas fa-plus me-1"></i><?= esc(lang('Reports.hub_nav_custom')) ?>
            </button>
        </div>
    </div>
    <?php else: ?>
    <p class="text-muted small mb-3"><?= esc(lang('Reports.summary_matches_export')) ?></p>
    <div class="row">
        <?php foreach ($sections as $sec): ?>
            <?php if ($activeSection !== '' && $activeSection !== $sec['key']) {
                continue;
            } ?>
            <div class="col-12 mb-4" id="reports-section-<?= esc($sec['key'], 'attr') ?>">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light">
                        <h6 class="m-0 font-weight-bold text-<?= esc($sec['header'] ?? 'secondary', 'attr') ?>">
                            <i class="<?= esc($sec['icon'] ?? '', 'attr') ?>"></i><?= esc($sec['title']) ?>
                        </h6>
                        <span class="small text-muted"><?= esc($sec['date_from']) ?> — <?= esc($sec['date_to']) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($sec['summary']['segments'])): ?>
                        <div class="row g-2 mb-3">
                            <?php foreach ($sec['summary']['segments'] as $segId => $segInfo): ?>
                                <div class="col-md-4">
                                    <div class="border rounded p-2 h-100 bg-light">
                                        <div class="small text-muted text-uppercase"><?= esc($segInfo['title'] ?? $segId) ?></div>
                                        <div class="fw-semibold"><?= esc(lang('Reports.rows_in_export')) ?>: <?= (int) ($segInfo['row_count'] ?? 0) ?></div>
                                        <?php
                                        $sum = $segInfo['summary'] ?? [];
                                        $shown = 0;
                                        foreach ($sum as $k => $v) {
                                            if ($shown >= 4) {
                                                break;
                                            }
                                            if ($k === 'error') {
                                                echo '<div class="small text-danger">' . esc((string) $v) . '</div>';
                                                $shown++;

                                                continue;
                                            }
                                            if (!is_scalar($v) && $v !== null) {
                                                $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                                            }
                                            echo '<div class="small"><span class="text-muted">' . esc(str_replace('_', ' ', (string) $k)) . ':</span> ' . esc((string) $v) . '</div>';
                                            $shown++;
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($sec['items'] as $item): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="mb-1"><?= esc($item['title']) ?></h6>
                                        <p class="mb-0 text-muted small"><?= esc($item['description']) ?></p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-<?= esc($sec['header'] ?? 'primary', 'attr') ?>" data-report-type="<?= esc($item['id'], 'attr') ?>" title="<?= esc(lang('Reports.btn_generate_excel')) ?>">
                                        <i class="fas fa-file-excel me-1"></i><?= esc(lang('Reports.btn_generate_excel')) ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

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
                                        $st = $report['status'] ?? '';
                                        $statusClass = match ($st) {
                                            'completed' => 'success',
                                            'failed' => 'danger',
                                            'pending', 'processing' => 'warning',
                                            default => 'secondary',
                                        };
                                        ?>
                                        <span class="badge badge-<?= esc($statusClass, 'attr') ?>">
                                            <?= esc(ucfirst((string) $st)) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($st === 'completed'): ?>
                                            <button class="btn btn-sm btn-primary" onclick="downloadReport(<?= $report['id'] ?>)" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary btn-icon-only" onclick="viewReport(<?= $report['id'] ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-danger" onclick="deleteReport(<?= $report['id'] ?>)" title="Hapus Report" aria-label="Hapus report">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="text-center text-muted">No reports found</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
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
                                    <option value=""><?= esc(lang('Reports.select_report_type_placeholder')) ?></option>
                                    <?php foreach ($customOpts as $optVal => $optLabel): ?>
                                        <option value="<?= esc($optVal, 'attr') ?>"><?= esc($optLabel) ?></option>
                                    <?php endforeach; ?>
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
    $(document).on('click', '[data-report-type]', function () {
        var t = $(this).data('report-type');
        if (t) {
            generateQuickReport(t);
        }
    });

    const sp = new URLSearchParams(window.location.search);
    const sec = sp.get('section');
    if (sec === 'custom') {
        document.getElementById('reports-section-custom')?.scrollIntoView({ block: 'start', behavior: 'smooth' });
    } else if (sec) {
        document.getElementById('reports-section-' + sec)?.scrollIntoView({ block: 'start', behavior: 'smooth' });
    }

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

    var postData = {};
    if (window.csrfTokenName && window.csrfToken) {
        postData[window.csrfTokenName] = window.csrfToken;
    }

    $.ajax({
        url: '<?= base_url('reports/quick/') ?>' + type,
        method: 'POST',
        data: postData,
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
    if (!$('#reportType option').length || $('#reportType option').length <= 1) {
        showNotification('No report types available for your role.', 'warning');
        return;
    }
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
    if (window.csrfTokenName && window.csrfToken) {
        formData[window.csrfTokenName] = window.csrfToken;
    }

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
    OptimaConfirm.danger({
        title: 'Hapus Report',
        text: 'Apakah Anda yakin ingin menghapus report ini? Tindakan ini tidak dapat dibatalkan.',
        onConfirm: function() {
            var delBody = {};
            if (window.csrfTokenName && window.csrfToken) {
                delBody[window.csrfTokenName] = window.csrfToken;
            }
            $.ajax({
        url: '<?= base_url('reports/delete/') ?>' + id,
        method: 'POST',
        data: delBody,
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                alertSwal('success', 'Report berhasil dihapus!');
                location.reload();
            } else {
                alertSwal('error', response.message, 'Gagal Hapus Report');
            }
        },
        error: function(xhr, status, error) {
            alertSwal('error', 'Error: ' + error);
        }
    });
        }
    });
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
    OptimaConfirm.danger({
        title: 'Hapus Report Lama',
        text: 'Ini akan menghapus semua report yang lebih dari 30 hari beserta file-nya. Tindakan ini tidak dapat dibatalkan.',
        confirmText: '<i class="fas fa-trash me-1"></i>Ya, Hapus Report Lama',
        onConfirm: function() {
            showLoading('Menghapus report lama...');
            var body = { days: 30 };
            if (window.csrfTokenName && window.csrfToken) {
                body[window.csrfTokenName] = window.csrfToken;
            }
            $.ajax({
                url: '<?= base_url('reports/clear-old') ?>',
                method: 'POST',
                data: body,
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        alertSwal('success', response.message, 'Selesai');
                        setTimeout(function() { location.reload(); }, 2000);
                    } else {
                        alertSwal('error', response.message, 'Gagal');
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                    alertSwal('error', msg, 'Gagal Hapus Report Lama');
                }
            });
        }
    });
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