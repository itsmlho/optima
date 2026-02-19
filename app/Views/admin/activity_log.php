<?= $this->extend('layouts/base') ?>


<?= $this->section('content') ?>
<div class="content-body" id="activityLogContent">

        
        <div class="row mb-4">
            <div class="col">
                <p class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Semua aktivitas user dicatat di sini dengan deskripsi yang detail. 
                    <strong>Klik pada baris tabel</strong> untuk melihat informasi lengkap perubahan data.
                </p>
            </div>

            <div class="col-auto">
                <button class="btn btn-success" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <a href="<?= base_url('/admin/activity-log/export') ?>" class="btn btn-info">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-filter"></i> Advanced Filters
                    <button class="btn btn-sm btn-link float-end" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </h6>
            </div>
            <div class="card-body collapse" id="advancedFilters">
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" id="filterDateFrom" name="date_from">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" id="filterDateTo" name="date_to">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Module</label>
                            <select class="form-select" id="filterModule" name="module_filter">
                                <option value="">All Modules</option>
                                <option value="QUOTATION">Quotation</option>
                                <option value="INVOICE">Invoice</option>
                                <option value="PURCHASE_ORDER">Purchase Order</option>
                                <option value="ASSET">Asset Management</option>
                                <option value="INVENTORY">Inventory</option>
                                <option value="USER">User Management</option>
                                <option value="SETTINGS">Settings</option>
                                <option value="PROFILE">Profile</option>
                                <option value="EXPORT">Export</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Action Type</label>
                            <select class="form-select" id="filterAction" name="action_filter">
                                <option value="">All Actions</option>
                                <option value="CREATE">CREATE</option>
                                <option value="UPDATE">UPDATE</option>
                                <option value="DELETE">DELETE</option>
                                <option value="EXPORT">EXPORT</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Business Impact</label>
                            <select class="form-select" id="filterImpact" name="impact_filter">
                                <option value="">All Impact Levels</option>
                                <option value="LOW">LOW</option>
                                <option value="MEDIUM">MEDIUM</option>
                                <option value="HIGH">HIGH</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="filterCritical" name="critical_only" value="1">
                                <label class="form-check-label" for="filterCritical">
                                    Critical Activities Only
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                    <i class="fas fa-times"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <table id="activityLogTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Tabel</th>
                            <th>Deskripsi</th>
                            <th>Impact</th>
                            <th>Critical</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat melalui DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
</div>

<!-- OPTIMIZED Modal untuk Detail Activity -->
<div class="modal-optimized modal-wide" id="activityDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-muted">
                <h5 class="modal-title">
                    <i class="fas fa-list-alt me-2"></i>Activity Log Detail
                </h5>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="activityDetailContent" data-dynamic="true">
                <!-- Content loaded dynamically for performance -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?php // Using 'script' section instead of 'js' to match base layout ?>
<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    console.log('🚀 Initializing OPTIMIZED Activity Log DataTable...');
    
    // Destroy existing table if present for clean initialization
    if ($.fn.DataTable.isDataTable('#activityLogTable')) {
        $('#activityLogTable').DataTable().destroy();
    }
    
    var table = $('#activityLogTable').DataTable({
        processing: true,
        serverSide: true,
        deferRender: false, // Render immediately for instant display
        pageLength: 10, // REDUCED: Smaller pages for faster rendering
        lengthMenu: [[5, 10, 15, 25], [5, 10, 15, 25]], // Optimized options
        stateSave: false, // Disable for performance
        autoWidth: false, // Disable auto width calculation
        searchDelay: 800, // INCREASED: Aggressive debouncing
        ajax: {
            url: '<?= base_url('/admin/activity-log/data') ?>',
            type: 'POST',
            timeout: 15000, // Increased timeout
            data: function(d) {
                // Add filter parameters to request
                d.date_from = $('#filterDateFrom').val();
                d.date_to = $('#filterDateTo').val();
                d.module_filter = $('#filterModule').val();
                d.impact_filter = $('#filterImpact').val();
                d.critical_only = $('#filterCritical').is(':checked') ? 1 : 0;
                d.action_filter = $('#filterAction').val();
            },
            error: function(xhr, error, thrown) {
                console.error('❌ DataTables AJAX Error:', error, thrown);
                $('#activityLogTable_processing').hide();
                showNotification('Failed to load activity log data. Please refresh.', 'error');
            },
            dataSrc: function(json) {
                console.log('📄 DataTables received', json.recordsTotal, 'total records');
                return json.data || [];
            }
        },
        columns: [
            { 
                data: 'created_at', 
                title: 'Waktu',
                render: function(data, type, row) {
                    // Server sends formatted string "27/01/2026 15:30:45" - display directly
                    return data || '-';
                }
            },
            { data: 'username', title: '<?= lang("App.user") ?>' },
            { data: 'module_name', title: '<?= lang("App.module") ?>' },
            { data: 'action_type', title: '<?= lang("App.action") ?>' },
            { data: 'table_name', title: '<?= lang("App.table") ?>' },
            { data: 'action_description', title: '<?= lang("App.description") ?>' },
            { data: 'business_impact', title: '<?= lang("App.impact") ?>' },
            { data: 'is_critical', title: '<?= lang("App.critical") ?>' }
        ],
        dom: 'lfrtip', // Minimal DOM for maximum speed
        order: [[0, 'desc']],
        pageLength: 10, // REDUCED for faster rendering
        lengthMenu: [[5, 10, 15, 25], [5, 10, 15, 25]], // Optimized options
        language: {
            processing: '<div class="d-flex align-items-center justify-content-center p-2"><div class="spinner-border spinner-border-sm me-2"></div><?= lang("App.loading") ?>...</div>',
            search: "<?= lang('App.search') ?>:",
            lengthMenu: "<?= lang('App.show') ?> _MENU_",
            info: "_START_ <?= lang('App.to') ?> _END_ <?= lang('App.of') ?> _TOTAL_",
            emptyTable: "<?= lang('App.no_activity_logs') ?>",
            zeroRecords: "No matching records"
        },
        rowCallback: function(row, data) {
            // PERFORMANCE: Use vanilla JS for better speed
            row.style.cursor = 'pointer';
            row.title = 'Click to view details';
            row.onclick = function() {
                const activityId = data.activity_id;
                console.log('🖱️ Activity clicked:', activityId);
                if (activityId) {
                    showActivityDetailOptimized(activityId);
                } else {
                    console.error('❌ No activity_id found:', data);
                }
            };
        },
        initComplete: function() {
            console.log('✅ Activity Log DataTable optimized and ready');
        }
    });
    
    console.log('DataTable initialized');
});

// OPTIMIZED: Ultra-fast activity detail modal  
function showActivityDetailOptimized(activityId) {
    console.log('🚀 Loading activity detail (optimized):', activityId);
    
    // Show modal immediately for instant feedback
    const modal = $('#activityDetailModal');
    modal.addClass('show').css('display', 'block');
    $('body').addClass('modal-open-optimized');
    
    // Show loading state immediately
    $('#activityDetailContent').html(`
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 mb-0 text-muted">Loading activity details...</p>
        </div>
    `);
    
    // Lazy load content with minimal delay
    setTimeout(function() {
        $.ajax({
            url: '<?= base_url('/admin/activity-log/details/') ?>' + activityId,
            type: 'GET',
            timeout: 8000,
            success: function(response) {
                console.log('✅ Activity detail loaded');
                if (response.success) {
                    // Build simplified content for better performance
                    const data = response.data;
                    const content = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6><strong>User:</strong></h6>
                                <p>${data.username || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Action:</strong></h6>
                                <p><span class="badge bg-primary">${data.action_type || 'N/A'}</span></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6><strong>Description:</strong></h6>
                                <p>${data.action_description || 'No description'}</p>
                            </div>
                        </div>
                        ${data.old_values || data.new_values ? `
                        <div class="row">
                            ${data.old_values ? `
                            <div class="col-md-6">
                                <h6 class="text-danger">Old Values:</h6>
                                <div style="max-height: 200px; overflow-y: auto;">
                                    <pre class="bg-light p-2 small">${JSON.stringify(data.old_values, null, 2)}</pre>
                                </div>
                            </div>` : ''}
                            ${data.new_values ? `
                            <div class="col-md-6">
                                <h6 class="text-success">New Values:</h6>
                                <div style="max-height: 200px; overflow-y: auto;">
                                    <pre class="bg-light p-2 small">${JSON.stringify(data.new_values, null, 2)}</pre>
                                </div>
                            </div>` : ''}
                        </div>` : ''}
                    `;
                    $('#activityDetailContent').html(content);
                } else {
                    $('#activityDetailContent').html(`
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            ${response.message || 'Failed to load activity details'}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading activity detail:', error);
                $('#activityDetailContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load activity details. Please try again.
                    </div>
                `);
            }
        });
    }, 50); // Minimal delay for smooth UX
}

// Function untuk show detail
function viewDetails(id) {
    $.ajax({
        url: '<?= base_url('/admin/activity-log/details/') ?>' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong>Informasi User:</strong></h6>
                            <p><strong>Username:</strong> ${data.username}</p>
                            <p><strong>Nama:</strong> ${data.full_name || '-'}</p>
                            <p><strong>Waktu:</strong> ${data.created_at}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Informasi Aktivitas:</strong></h6>
                            <p><strong>Tipe:</strong> <span class="badge bg-primary">${data.action_type}</span></p>
                            <p><strong>Module:</strong> ${data.module_name}</p>
                            <p><strong>Tabel:</strong> ${data.table_name}</p>
                            <p><strong>Record ID:</strong> ${data.record_id}</p>
                        </div>
                    </div>
                    <hr>
                    <h6><strong>Deskripsi:</strong></h6>
                    <p>${data.action_description}</p>
                `;
                
                if (data.workflow_stage) {
                    content += `
                        <hr>
                        <h6><strong>Workflow Stage:</strong></h6>
                        <p><span class="badge bg-info">${data.workflow_stage}</span></p>
                    `;
                }
                
                content += `
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong>Impact:</strong></h6>
                            <p><strong>Level:</strong> <span class="badge bg-warning">${data.business_impact}</span></p>
                            <p><strong>Critical:</strong> ${data.is_critical ? 'Ya' : 'Tidak'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Field Yang Terpengaruh:</strong></h6>
                            <p>${data.affected_fields ? JSON.stringify(data.affected_fields).replace(/[{}"\[\]]/g, '').replace(/,/g, ', ') : 'Tidak ada data'}</p>
                        </div>
                    </div>
                `;
                
                if (data.old_values || data.new_values) {
                    content += `
                        <hr>
                        <h6><strong>Perubahan Data:</strong></h6>
                        <div class="row">
                    `;
                    
                    if (data.old_values) {
                        content += `
                            <div class="col-md-6">
                                <h6 class="text-danger">Nilai Lama:</h6>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    <pre class="bg-light p-2 small">${JSON.stringify(data.old_values, null, 2)}</pre>
                                </div>
                            </div>
                        `;
                    }
                    
                    if (data.new_values) {
                        content += `
                            <div class="col-md-6">
                                <h6 class="text-success">Nilai Baru:</h6>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    <pre class="bg-light p-2 small">${JSON.stringify(data.new_values, null, 2)}</pre>
                                </div>
                            </div>
                        `;
                    }
                    
                    content += `</div>`;
                }
                
                $('#activityDetailContent').html(content);
                $('#activityDetailModal').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('Error loading activity details: ' + error);
        }
    });
}

// Filter functions
function applyFilters() {
    table.ajax.reload();
}

function resetFilters() {
    $('#filterForm')[0].reset();
    table.ajax.reload();
}
</script>
<?= $this->endSection() ?>
