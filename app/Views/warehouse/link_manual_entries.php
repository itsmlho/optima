<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1"><i class="fas fa-link me-2"></i><?= esc($page_title) ?></h4>
                    <p class="text-muted small mb-0">Link manual sparepart entries to official warehouse codes for data standardization</p>
                </div>
                <div>
                    <a href="<?= base_url('warehouse') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Warehouse
                    </a>
                </div>
            </div>

            <!-- Info Alert -->
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Instructions:</strong> 
                Select a manual entry below, enter the official sparepart code and name, then review affected work orders before linking.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- Manual Entries Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Manual Sparepart Entries</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="manualEntriesTable" class="table table-hover table-striped" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Manual Name</th>
                                    <th>Source Type</th>
                                    <th class="text-center">WO Count</th>
                                    <th class="text-center">Entry Count</th>
                                    <th>First Used</th>
                                    <th>Last Used</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Link Modal -->
<div class="modal fade" id="linkModal" tabindex="-1" aria-labelledby="linkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="linkModalLabel">
                    <i class="fas fa-link me-2"></i>Link Manual Entry to Official Code
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="linkForm">
                    <!-- Manual Entry Info -->
                    <div class="alert alert-secondary">
                        <h6 class="mb-2"><i class="fas fa-edit me-2"></i>Manual Entry:</h6>
                        <p class="mb-0" id="manualNameDisplay"></p>
                        <input type="hidden" name="manual_name" id="manualNameInput">
                    </div>

                    <!-- Official Sparepart Info -->
                    <div class="mb-3">
                        <label for="officialCode" class="form-label fw-bold">Official Sparepart Code *</label>
                        <input type="text" class="form-control" id="officialCode" name="official_code" 
                               placeholder="e.g., SPR-ALT-001" required>
                        <div class="form-text">Enter the official warehouse sparepart code</div>
                    </div>

                    <div class="mb-3">
                        <label for="officialName" class="form-label fw-bold">Official Sparepart Name *</label>
                        <input type="text" class="form-control" id="officialName" name="official_name" 
                               placeholder="e.g., Alternator Bosch 12V Original" required>
                        <div class="form-text">Enter the standardized sparepart name</div>
                    </div>

                    <!-- Affected Work Orders -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-clipboard-list me-2"></i>Affected Work Orders
                        </label>
                        <div id="affectedWOsContainer" class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                            <div class="text-center text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Loading affected work orders...
                            </div>
                        </div>
                        <div class="form-text">
                            Select which work orders should be updated. Exact match only (case-insensitive).
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="alert alert-warning d-none" id="summaryAlert">
                        <h6 class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Update Summary:</h6>
                        <p class="mb-0">
                            You are about to update <strong id="selectedCount">0</strong> work orders.
                            This action will replace manual entry with official code and name.
                        </p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnLinkEntries" disabled>
                    <i class="fas fa-link me-2"></i>Link Selected Work Orders
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let currentManualName = '';
    let affectedWOs = [];
    
    // Initialize DataTables
    const table = $('#manualEntriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/get-manual-entries-data') ?>',
            type: 'POST',
            data: function(d) {
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error, thrown);
                alert('Failed to load manual entries. Please refresh the page.');
            }
        },
        columns: [
            { 
                data: 'sparepart_name',
                render: function(data, type, row) {
                    return `<strong>${escapeHtml(data)}</strong>`;
                }
            },
            { 
                data: 'source_type',
                render: function(data) {
                    const badges = {
                        'WAREHOUSE': '<span class="badge bg-success">Warehouse</span>',
                        'BEKAS': '<span class="badge bg-warning">Bekas</span>',
                        'KANIBAL': '<span class="badge bg-danger">Kanibal</span>'
                    };
                    return badges[data] || data;
                }
            },
            { 
                data: 'wo_count', 
                className: 'text-center',
                render: function(data) {
                    return `<span class="badge bg-primary">${data}</span>`;
                }
            },
            { 
                data: 'entry_count', 
                className: 'text-center',
                render: function(data) {
                    return `<span class="badge bg-secondary">${data}</span>`;
                }
            },
            { 
                data: 'first_used',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            },
            { 
                data: 'last_used',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary btn-link-entry" 
                                data-name="${escapeHtml(row.sparepart_name)}"
                                data-wos="${escapeHtml(row.wo_numbers)}"
                                data-count="${row.wo_count}">
                            <i class="fas fa-link"></i> Link
                        </button>
                    `;
                }
            }
        ],
        order: [[5, 'desc']], // Sort by last_used descending
        pageLength: 25,
        language: {
            emptyTable: "No manual entries found. All spareparts have official codes!",
            zeroRecords: "No matching manual entries found"
        }
    });
    
    // Open Link Modal
    $(document).on('click', '.btn-link-entry', function() {
        const manualName = $(this).data('name');
        const woNumbers = $(this).data('wos');
        const woCount = $(this).data('count');
        
        currentManualName = manualName;
        $('#manualNameDisplay').text(manualName);
        $('#manualNameInput').val(manualName);
        $('#officialCode').val('');
        $('#officialName').val('');
        $('#summaryAlert').addClass('d-none');
        $('#btnLinkEntries').prop('disabled', true);
        
        // Load affected work orders
        loadAffectedWOs(manualName);
        
        $('#linkModal').modal('show');
    });
    
    // Load Affected Work Orders
    function loadAffectedWOs(manualName) {
        $('#affectedWOsContainer').html(`
            <div class="text-center text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading affected work orders...
            </div>
        `);
        
        $.ajax({
            url: '<?= base_url('warehouse/get-manual-entry-wos') ?>',
            type: 'POST',
            data: {
                manual_name: manualName,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success && response.data) {
                    affectedWOs = response.data;
                    renderWOCheckboxes(response.data);
                } else {
                    $('#affectedWOsContainer').html(`
                        <div class="text-center text-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Failed to load work orders: ${response.message || 'Unknown error'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                $('#affectedWOsContainer').html(`
                    <div class="text-center text-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        Error loading work orders. Please try again.
                    </div>
                `);
            }
        });
    }
    
    // Render WO Checkboxes
    function renderWOCheckboxes(wos) {
        if (wos.length === 0) {
            $('#affectedWOsContainer').html(`
                <div class="text-center text-muted">
                    No work orders found for this manual entry.
                </div>
            `);
            return;
        }
        
        let html = `
            <div class="mb-2">
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAll">
                    <i class="fas fa-check-square me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeselectAll">
                    <i class="fas fa-square me-1"></i>Deselect All
                </button>
            </div>
            <div class="wo-checkboxes">
        `;
        
        wos.forEach(function(wo) {
            html += `
                <div class="form-check mb-2">
                    <input class="form-check-input wo-checkbox" type="checkbox" 
                           value="${wo.work_order_id}" id="wo_${wo.work_order_id}" checked>
                    <label class="form-check-label" for="wo_${wo.work_order_id}">
                        <strong>${escapeHtml(wo.work_order_number)}</strong> - 
                        ${escapeHtml(wo.unit_number || 'No Unit')} - 
                        <span class="text-muted">${escapeHtml(wo.created_at_formatted)}</span>
                    </label>
                </div>
            `;
        });
        
        html += '</div>';
        $('#affectedWOsContainer').html(html);
        
        // Update summary immediately
        updateSummary();
    }
    
    // Select All / Deselect All
    $(document).on('click', '#btnSelectAll', function() {
        $('.wo-checkbox').prop('checked', true);
        updateSummary();
    });
    
    $(document).on('click', '#btnDeselectAll', function() {
        $('.wo-checkbox').prop('checked', false);
        updateSummary();
    });
    
    // Update Summary on checkbox change
    $(document).on('change', '.wo-checkbox', function() {
        updateSummary();
    });
    
    // Update Summary Function
    function updateSummary() {
        const selectedCount = $('.wo-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#summaryAlert').removeClass('d-none');
            $('#btnLinkEntries').prop('disabled', false);
        } else {
            $('#summaryAlert').addClass('d-none');
            $('#btnLinkEntries').prop('disabled', true);
        }
    }
    
    // Submit Link Form
    $('#btnLinkEntries').on('click', function() {
        const officialCode = $('#officialCode').val().trim();
        const officialName = $('#officialName').val().trim();
        const selectedWOs = $('.wo-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (!officialCode || !officialName) {
            alert('Please enter both official code and name');
            return;
        }
        
        if (selectedWOs.length === 0) {
            alert('Please select at least one work order');
            return;
        }
        
        if (!confirm(`Are you sure you want to link ${selectedWOs.length} work order(s) to official code ${officialCode}?\n\nThis will update:\n- Sparepart Code: ${officialCode}\n- Sparepart Name: ${officialName}\n\nThis action cannot be undone easily.`)) {
            return;
        }
        
        // Disable button and show loading
        $('#btnLinkEntries').prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Linking...
        `);
        
        $.ajax({
            url: '<?= base_url('warehouse/link-manual-entries') ?>',
            type: 'POST',
            data: {
                manual_name: currentManualName,
                official_code: officialCode,
                official_name: officialName,
                selected_wos: selectedWOs,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(`Success! ${response.updated_count} entries linked to ${officialCode}`);
                    $('#linkModal').modal('hide');
                    table.ajax.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to link entries'));
                }
            },
            error: function(xhr) {
                alert('Error: Failed to link entries. Please try again.');
            },
            complete: function() {
                $('#btnLinkEntries').prop('disabled', false).html(`
                    <i class="fas fa-link me-2"></i>Link Selected Work Orders
                `);
            }
        });
    });
    
    // Helper: Escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? String(text).replace(/[&<>"']/g, m => map[m]) : '';
    }
});
</script>
<?= $this->endSection() ?>
