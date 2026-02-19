<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-3">
    <h4 class="fw-bold mb-1">
        <i class="bi bi-arrow-left-right me-2 text-primary"></i>
        Sparepart Usage & Returns Management
    </h4>
    <p class="text-muted mb-0">Track sparepart usage from warehouse to service and manage returns</p>
</div>

<style>
    /* Custom styling for sparepart usage table */
    #usageTable {
        width: 100% !important;
    }
    
    #usageTable th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.85rem;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
        vertical-align: middle;
    }
    
    #usageTable td {
        vertical-align: middle;
        font-size: 0.875rem;
    }
    
    #usageTable tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    /* Modal simple styling */
    .modal-header {
        border-bottom: 1px solid #dee2e6;
    }
    
    .bg-info-soft { background-color: #d1ecf1; }
    .bg-primary-soft { background-color: #cfe2ff; }
    .bg-warning-soft { background-color: #fff3cd; }
    .bg-success-soft { background-color: #d1e7dd; }
    
    /* Better table responsiveness */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>



    <!-- Statistics Cards -->
    <div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-info-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-list stat-icon text-info"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-usage-total">
                            <?= $stats['usage_total'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Total Usage (All)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW: Warehouse Usage -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-warehouse stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-usage-warehouse">
                            <?= $stats['usage_warehouse'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Warehouse Stock</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW: Non-Warehouse Usage -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card" style="background-color: #fff3cd;">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-recycle stat-icon" style="color: #856404;"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-usage-non-warehouse">
                            <?= $stats['usage_non_warehouse'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Bekas/Reuse</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-clock stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-return-pending">
                            <?= $stats['return_pending'] ?? 0 ?>
                        </div>
                        <div class="text-muted"><?= lang('Warehouse.pending_returns') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card with Tabs -->
    <div class="card table-card shadow mb-4">
        <div class="card-header">
            <div class="row align-items-center mb-3">
                <div class="col">
                    <h5 class="card-title fw-bold m-0">
                        <i class="fas fa-tools text-primary me-2"></i>
                        <?= lang('App.sparepart_usage_returns') ?>
                    </h5>
                </div>
            </div>
            
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-3" id="sparepartTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="usage-tab" data-bs-toggle="tab" data-bs-target="#usage" type="button" role="tab" aria-controls="usage" aria-selected="true">
                        <i class="fas fa-list-check me-1"></i>
                        <strong><?= lang('Warehouse.usage') ?></strong>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="returns-tab" data-bs-toggle="tab" data-bs-target="#returns" type="button" role="tab" aria-controls="returns" aria-selected="false">
                        <i class="fas fa-undo me-1"></i>
                        <strong><?= lang('Warehouse.returns') ?></strong>
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            <!-- Tab Content -->
            <div class="tab-content" id="sparepartTabContent">
                <!-- Tab Pemakaian -->
                <div class="tab-pane fade show active" id="usage" role="tabpanel">
                    <?php if (isset($usage_table_exists) && !$usage_table_exists): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Table <code>work_order_sparepart_usage</code> is not available yet.
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="usageTable" style="font-size: 0.875rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th style="width: 120px;">Work Order</th>
                                    <th style="width: 100px;">Date</th>
                                    <th>Customer</th>
                                    <th>Unit</th>
                                    <th style="width: 100px; text-align: center;">Items</th>
                                    <th style="width: 70px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTable will populate here -->
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Pengembalian -->
                <div class="tab-pane fade" id="returns" role="tabpanel">
                    <?php if (isset($return_table_exists) && !$return_table_exists): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Table <code>work_order_sparepart_returns</code> is not available yet. 
                        <a href="<?= base_url('warehouse/sparepart-returns') ?>" class="alert-link">See setup instructions</a>
                    </div>
                    <?php else: ?>
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Filter Status</label>
                            <select class="form-select" id="filter-status">
                                <option value="PENDING" selected>Pending</option>
                                <option value="CONFIRMED">Confirmed</option>
                                <option value="ALL">All</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100" onclick="applyReturnFilters()">
                                <i class="fas fa-search me-2"></i>Apply Filter
                            </button>
                        </div>
                    </div>

                    <!-- Returns Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm" id="returnsTable" style="font-size: 0.875rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 140px;">Work Order</th>
                                    <th style="min-width: 200px;">Item Details</th>
                                    <th style="width: 180px;">Customer / Unit</th>
                                    <th style="width: 120px;">Mechanic</th>
                                    <th style="width: 150px; text-align: center;">Quantity</th>
                                    <th style="width: 70px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTable will populate here -->
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Usage Modal -->
<div class="modal fade modal-wide" id="usageDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title text-dark fw-semibold" id="usageModalTitle">
                    <i class="fas fa-info-circle text-primary me-2"></i>Detail Usage
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="usageDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail/Confirm Return Modal -->
<div class="modal fade modal-wide" id="returnDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i><?= lang('App.detail') ?> <?= lang('Warehouse.returns') ?> <?= lang('Warehouse.sparepart') ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="returnDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let usageTable, returnsTable;
let usageTableInitialized = false;
let returnsTableInitialized = false;

$(document).ready(function() {
    // Handle tab switching
    $('#sparepartTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const targetTab = $(e.target).data('bs-target');
        
        // Small delay to ensure tab is fully visible
        setTimeout(function() {
            // Initialize Usage Table when usage tab is shown
            if (targetTab === '#usage' && !usageTableInitialized) {
                <?php if (isset($usage_table_exists) && $usage_table_exists): ?>
                initializeUsageTable();
                <?php endif; ?>
            }
            
            // Initialize Returns Table when returns tab is shown
            if (targetTab === '#returns' && !returnsTableInitialized) {
                <?php if (isset($return_table_exists) && $return_table_exists): ?>
                initializeReturnsTable();
                <?php endif; ?>
            }
        }, 150);
    });
    
    // Force activate Pemakaian tab on page load (default tab)
    $('#usage-tab').tab('show');
    
    // Initialize first tab (Pemakaian) on page load (with delay to ensure DOM is ready)
    setTimeout(function() {
        // Always initialize Pemakaian tab first as default
        <?php if (isset($usage_table_exists) && $usage_table_exists): ?>
        if (!usageTableInitialized) {
            console.log('Initializing default tab: Pemakaian');
            initializeUsageTable();
        }
        <?php endif; ?>
    }, 300);
    
    <?php if (isset($usage_table_exists) && $usage_table_exists): ?>
    // Format spareparts detail table
    function formatSparepartsTable(spareparts) {
        // Check if response has error
        if (spareparts && spareparts.error) {
            console.error('API Error:', spareparts.message);
            return '<div class="p-3 text-danger">Error: ' + (spareparts.message || 'Unknown error') + '</div>';
        }
        
        // Check if is array
        if (!Array.isArray(spareparts) || spareparts.length === 0) {
            return '<div class="p-3 text-muted">No spareparts found</div>';
        }
        
        var html = '<div class="p-3 bg-light"><table class="table table-sm table-bordered mb-0">';
        html += '<thead class="table-light">';
        html += '<tr>';
        html += '<th width="80px">Type</th>';
        html += '<th width="80px">Source</th>';
        html += '<th>Item</th>';
        html += '<th width="70px" class="text-center">Brought</th>';
        html += '<th width="70px" class="text-center">Used</th>';
        html += '<th width="70px" class="text-center">Returned</th>';
        html += '<th>Notes</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        
        spareparts.forEach(function(item) {
            // Type badge
            var typeBadge = item.item_type === 'tool' 
                ? '<span class="badge bg-secondary">🔧 Tool</span>'
                : '<span class="badge bg-primary">⚙ Part</span>';
            
            // Source badge  
            var sourceBadge = parseInt(item.is_from_warehouse) === 0
                ? '<span class="badge bg-warning text-dark">♻ Bekas</span>'
                : '<span class="badge bg-success">🏪 WH</span>';
            
            html += '<tr>';
            html += '<td>' + typeBadge + '</td>';
            html += '<td>' + sourceBadge + '</td>';
            html += '<td><strong>' + item.sparepart_name + '</strong><br><small class="text-muted">' + item.sparepart_code + '</small></td>';
            html += '<td class="text-center"><span class="badge bg-info">' + item.quantity_brought + '</span></td>';
            html += '<td class="text-center"><span class="badge bg-success">' + item.quantity_used + '</span></td>';
            html += '<td class="text-center">' + (item.quantity_return > 0 ? '<span class="badge bg-warning text-dark">' + item.quantity_return + '</span>' : '-') + '</td>';
            html += '<td><small>' + (item.usage_notes || '-') + '</small></td>';
            html += '</tr>';
        });
        
        html += '</tbody>';
        html += '</table></div>';
        
        return html;
    }
    
    function initializeUsageTable() {
        if (usageTableInitialized) {
            console.log('Usage table already initialized');
            return;
        }
        
        // Check if table element exists
        if ($('#usageTable').length === 0) {
            console.error('Usage table element not found');
            return;
        }
        
        // Ensure tab is active before initializing
        if (!$('#usage').hasClass('active')) {
            $('#usage-tab').tab('show');
        }
        
        console.log('Initializing usage table...');
        usageTable = $('#usageTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/sparepart-usage/get-usage-grouped') ?>',
            type: 'POST'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        columns: [
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '<i class="fas fa-plus-circle text-muted" style="cursor:pointer;"></i>'
            },
            { 
                data: 'work_order_number',
                name: 'work_order_number',
                render: function(data) {
                    return `<strong class="text-primary">${data}</strong>`;
                }
            },
            { 
                data: 'report_date',
                name: 'report_date'
            },
            { 
                data: 'customer_name',
                name: 'customer_name'
            },
            { 
                data: 'unit_number',
                name: 'unit_number',
                render: function(data, type, row) {
                    return `<strong>${data}</strong><br><small class="text-muted">${row.unit_info}</small>`;
                }
            },
            { 
                data: 'total_items',
                name: 'total_items',
                className: 'text-center',
                render: function(data, type, row) {
                    let html = `<strong>${data}</strong> items`;
                    if (row.nonwarehouse_items > 0) {
                        html += `<br><small class="text-muted">${row.warehouse_items} WH, ${row.nonwarehouse_items} Bekas</small>`;
                    }
                    return html;
                }
            },
            {
                data: 'work_order_id',
                orderable: false,
                className: 'text-center',
                render: function(data) {
                    return `<button class="btn btn-sm btn-info" onclick="viewWorkOrderDetail(${data})" title="View Detail">
                        <i class="fas fa-eye"></i>
                    </button>`;
                }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        language: {
            processing: "Processing...",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
        
        // Expand/collapse row functionality
        $('#usageTable tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = usageTable.row(tr);
            var icon = $(this).find('i');

            if (row.child.isShown()) {
                // Collapse
                row.child.hide();
                tr.removeClass('shown');
                icon.removeClass('fa-minus-circle text-primary').addClass('fa-plus-circle text-muted');
            } else {
                // Expand
                icon.removeClass('fa-plus-circle text-muted').addClass('fa-spinner fa-spin');
                
                // Fetch spareparts for this work order
                $.ajax({
                    url: '<?= base_url('warehouse/sparepart-usage/get-work-order-spareparts') ?>/' + row.data().work_order_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('Spareparts response:', response);
                        row.child(formatSparepartsTable(response)).show();
                        tr.addClass('shown');
                        icon.removeClass('fa-spinner fa-spin').addClass('fa-minus-circle text-primary');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        alert('Failed to load spareparts: ' + error);
                        icon.removeClass('fa-spinner fa-spin').addClass('fa-plus-circle text-muted');
                    }
                });
            }
        });
        
        usageTableInitialized = true;
        console.log('Usage table initialized successfully');
    }
    <?php endif; ?>

    <?php if (isset($return_table_exists) && $return_table_exists): ?>
    function initializeReturnsTable() {
        if (returnsTableInitialized) {
            console.log('Returns table already initialized');
            return;
        }
        
        // Check if table element exists
        if ($('#returnsTable').length === 0) {
            console.error('Returns table element not found');
            return;
        }
        
        // Check if tab is visible (either active or will be active)
        const returnsTab = $('#returns');
        if (!returnsTab.length) {
            console.error('Returns tab element not found');
            return;
        }
        
        console.log('Initializing returns table...');
        returnsTable = $('#returnsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/sparepart-usage/get-returns') ?>',
            type: 'POST',
            data: function(d) {
                d.status = $('#filter-status').val();
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        columns: [
            { 
                data: 'work_order_number',
                name: 'work_order_number',
                render: function(data, type, row) {
                    return `<strong class="text-primary">${data}</strong><br><small class="text-muted">${row.created_at}</small>`;
                }
            },
            { 
                data: 'sparepart_name',
                name: 'sparepart_name',
                render: function(data, type, row) {
                    // Type badge
                    let typeBadge = '';
                    if (row.item_type === 'tool') {
                        typeBadge = '<span class="badge bg-secondary me-1">🔧 Tool</span>';
                    } else {
                        typeBadge = '<span class="badge bg-primary me-1">⚙ Part</span>';
                    }
                    
                    // Source badge
                    let sourceBadge = '';
                    if (row.is_from_warehouse !== undefined && parseInt(row.is_from_warehouse) === 0) {
                        sourceBadge = '<span class="badge bg-warning text-dark">♻ Bekas</span>';
                    } else {
                        sourceBadge = '<span class="badge bg-success">🏪 WH</span>';
                    }
                    
                    return `${typeBadge} ${sourceBadge}<br><strong>${data}</strong><br><small class="text-muted">${row.sparepart_code}</small>`;
                }
            },
            { 
                data: 'customer_name',
                name: 'customer_name',
                render: function(data, type, row) {
                    return `<strong>${data || '-'}</strong><br><small class="text-muted"><i class="fas fa-truck"></i> ${row.unit_number || '-'}</small>`;
                }
            },
            { 
                data: 'mechanic_name',
                name: 'mechanic_name',
                render: function(data) {
                    return data && data !== '-' ? `<small>${data}</small>` : '<small class="text-muted">-</small>';
                }
            },
            { 
                data: 'quantity_brought',
                name: 'quantity_brought',
                className: 'text-center',
                render: function(data, type, row) {
                    let html = `<small class="text-muted d-block">Brought: <span class="badge bg-info">${data}</span></small>`;
                    html += `<small class="text-muted d-block">Used: <span class="badge bg-success">${row.quantity_used}</span></small>`;
                    if (row.quantity_return > 0) {
                        html += `<small class="text-muted d-block">Return: <span class="badge bg-warning text-dark">${row.quantity_return}</span></small>`;
                    }
                    // Add status badge
                    if (row.status === 'PENDING') {
                        html += `<small class="d-block mt-1"><span class="badge bg-warning text-dark">Pending</span></small>`;
                    } else if (row.status === 'CONFIRMED') {
                        html += `<small class="d-block mt-1"><span class="badge bg-success">Confirmed</span></small>`;
                    }
                    return html;
                }
            },
            {
                data: 'id',
                name: 'id',
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-info" onclick="viewReturnDetail(${data})" title="View Detail">
                        <i class="fas fa-eye"></i>
                    </button>`;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: "Processing...",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
        
        returnsTableInitialized = true;
        console.log('Returns table initialized successfully');
    }
    <?php endif; ?>

    // View usage detail
    window.viewUsageDetail = function(id) {
        $.ajax({
            url: '<?= base_url('warehouse/sparepart-usage/get-usage-detail') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
                $('#usageDetailBody').html('<div class="text-center py-5"><i class="fas fa-circle-notch fa-spin text-primary fs-2"></i><p class="mt-2 text-muted">Loading usage details...</p></div>');
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Type and Source badges
                    let typeBadge = '';
                    if (data.item_type === 'tool') {
                        typeBadge = '<span class="badge bg-secondary">🔧 Tool</span>';
                    } else {
                        typeBadge = '<span class="badge bg-primary">⚙ Sparepart</span>';
                    }
                    
                    let sourceBadge = '';
                    if (data.is_from_warehouse !== undefined && parseInt(data.is_from_warehouse) === 0) {
                        sourceBadge = '<span class="badge bg-warning text-dark ms-2">♻ Non-Warehouse</span>';
                    } else {
                        sourceBadge = '<span class="badge bg-success ms-2">🏪 Warehouse</span>';
                    }
                    
                    let html = `
                        <!-- Work Order Info -->
                        <div class="border-bottom pb-3 mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <label class="text-muted small d-block mb-1">Work Order</label>
                                    <h5 class="mb-0 text-primary fw-semibold">${data.work_order_number || '-'}</h5>
                                </div>
                                <div class="col-6 text-end">
                                    <label class="text-muted small d-block mb-1">Date</label>
                                    <div class="fw-semibold">${data.report_date_formatted || '-'}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Item Type & Source -->
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-2">Classification</label>
                            <div>
                                ${typeBadge}
                                ${sourceBadge}
                            </div>
                        </div>

                        <!-- Item Details -->
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="text-muted small d-block mb-1">Code</label>
                                    <div class="fw-semibold">${data.sparepart_code || '-'}</div>
                                </div>
                                <div class="col-md-8">
                                    <label class="text-muted small d-block mb-1">Name</label>
                                    <div class="fw-semibold">${data.sparepart_name || '-'}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-2">Quantity</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-muted small mb-1">Brought</div>
                                        <h5 class="mb-0 text-info">${data.quantity_brought || 0}</h5>
                                        <small class="text-muted">${data.satuan || 'PCS'}</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-muted small mb-1">Used</div>
                                        <h5 class="mb-0 text-success">${data.quantity_used || 0}</h5>
                                        <small class="text-muted">${data.satuan || 'PCS'}</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-muted small mb-1">Returned</div>
                                        <h5 class="mb-0 ${data.quantity_returned > 0 ? 'text-warning' : 'text-secondary'}">${data.quantity_returned > 0 ? data.quantity_returned : '0'}</h5>
                                        <small class="text-muted">${data.quantity_returned > 0 ? data.satuan || 'PCS' : '-'}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Work Order Details -->
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small d-block mb-1">Customer</label>
                                    <div class="fw-semibold">${data.customer_name || '-'}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block mb-1">Unit</label>
                                    <div class="fw-semibold">${data.unit_number || '-'}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block mb-1">Mechanic</label>
                                    <div class="fw-semibold">${data.mechanic_name || '-'}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block mb-1">Usage Date</label>
                                    <div class="fw-semibold">${data.used_at_formatted || '-'}</div>
                                </div>
                            </div>
                        </div>

                        ${data.usage_notes && data.usage_notes !== '-' ? `
                        <div class="border-start border-primary border-3 ps-3 py-2 mb-3">
                            <label class="text-muted small d-block mb-1"><i class="fas fa-sticky-note me-1"></i>Usage Notes</label>
                            <div>${data.usage_notes}</div>
                        </div>` : ''}
                        
                        ${data.return_notes && data.return_notes !== '-' ? `
                        <div class="border-start border-warning border-3 ps-3 py-2">
                            <label class="text-muted small d-block mb-1"><i class="fas fa-undo me-1"></i>Return Notes</label>
                            <div>${data.return_notes}</div>
                        </div>` : ''}
                    `;
                    $('#usageDetailBody').html(html);
                    $('#usageDetailModal').modal('show');
                } else {
                    alert('Error: ' + (response.message || 'Failed to load data'));
                }
            },
            error: function() {
                alert('An error occurred while loading data');
            }
        });
    };

    // View return detail
    window.viewReturnDetail = function(id) {
        $.ajax({
            url: '<?= base_url('warehouse/sparepart-usage/get-return-detail') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
                $('#returnDetailBody').html('<div class="text-center py-5"><i class="fas fa-circle-notch fa-spin text-primary fs-2"></i><p class="mt-2 text-muted">Loading return details...</p></div>');
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Type and Source badges
                    let typeBadge = '';
                    if (data.item_type === 'tool') {
                        typeBadge = '<span class="badge bg-secondary">🔧 Tool</span>';
                    } else {
                        typeBadge = '<span class="badge bg-primary">⚙ Sparepart</span>';
                    }
                    
                    let sourceBadge = '';
                    if (data.is_from_warehouse !== undefined && parseInt(data.is_from_warehouse) === 0) {
                        sourceBadge = '<span class="badge bg-warning text-dark ms-2">♻ Non-Warehouse</span>';
                    } else {
                        sourceBadge = '<span class="badge bg-success ms-2">🏪 Warehouse</span>';
                    }
                    
                    let html = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Work Order:</strong><br>
                                <span class="badge bg-primary">${data.work_order_number || '-'}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>WO Date:</strong><br>
                                ${data.report_date_formatted || '-'}
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-12 mb-2">
                                <strong>Item Type & Source:</strong><br>
                                ${typeBadge} ${sourceBadge}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Item Code:</strong><br>
                                ${data.sparepart_code}
                            </div>
                            <div class="col-md-6">
                                <strong>Item Name:</strong><br>
                                <strong>${data.sparepart_name}</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Quantity Brought:</strong><br>
                                <span class="badge bg-info">${data.quantity_brought} ${data.satuan}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Quantity Used:</strong><br>
                                <span class="badge bg-success">${data.quantity_used} ${data.satuan}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Quantity Return:</strong><br>
                                <span class="badge bg-warning">${data.quantity_return} ${data.satuan}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Customer:</strong><br>
                                ${data.customer_name || '-'}
                            </div>
                            <div class="col-md-6">
                                <strong>Unit:</strong><br>
                                ${data.unit_number || '-'}
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Mechanic:</strong><br>
                                ${data.mechanic_name || '-'}
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong><br>
                                <span class="badge ${data.status === 'PENDING' ? 'badge-pending' : 'badge-confirmed'}">${data.status}</span>
                            </div>
                        </div>
                        <hr>
                        ${data.return_notes ? `<div class="mb-3"><strong>Return Notes:</strong><br>${data.return_notes}</div>` : ''}
                        ${data.confirmed_at ? `<div class="mb-3"><strong>Confirmed At:</strong><br>${data.confirmed_at_formatted} by ${data.confirmed_by_name || '-'}</div>` : ''}
                    `;
                    
                    if (data.status === 'PENDING') {
                        html += `
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Confirmation Notes (Optional)</label>
                                <textarea class="form-control" id="confirm-notes" rows="3" placeholder="Add notes if necessary..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-success" onclick="confirmReturn(${data.id})">
                                    <i class="fas fa-check me-2"></i>Confirm Return
                                </button>
                            </div>
                        `;
                    }
                    
                    $('#returnDetailBody').html(html);
                    $('#returnDetailModal').modal('show');
                } else {
                    alert('Error: ' + (response.message || 'Failed to load data'));
                }
            },
            error: function() {
                alert('An error occurred while loading data');
            }
        });
    };

    // Apply return filters
    window.applyReturnFilters = function() {
        if (returnsTable && returnsTableInitialized) {
            returnsTable.ajax.reload();
        }
    };

    // Confirm return
    window.confirmReturn = function(id) {
        if (!confirm('Are you sure you want to confirm this sparepart return?')) {
            return;
        }

        const notes = $('#confirm-notes').val() || null;

        $.ajax({
            url: '<?= base_url('warehouse/sparepart-usage/confirm-return') ?>/' + id,
            type: 'POST',
            data: { notes: notes },
            success: function(response) {
                if (response.success) {
                    alert('Sparepart return has been successfully confirmed');
                    $('#returnDetailModal').modal('hide');
                    if (returnsTable && returnsTableInitialized) {
                        returnsTable.ajax.reload();
                    }
                } else {
                    alert('Error: ' + (response.message || 'Failed to confirm'));
                }
            },
            error: function() {
                alert('An error occurred while confirming');
            }
        });
    };
    
    // View work order detail (for Usage tab)
    window.viewWorkOrderDetail = function(workOrderId) {
        $.ajax({
            url: '<?= base_url('warehouse/sparepart-usage/get-work-order-spareparts') ?>/' + workOrderId,
            type: 'GET',
            dataType: 'json',
            success: function(spareparts) {
                console.log('Modal spareparts:', spareparts);
                
                // Check for error response
                if (spareparts && spareparts.error) {
                    alert('Error: ' + (spareparts.message || 'Unknown error'));
                    return;
                }
                
                if (Array.isArray(spareparts) && spareparts.length > 0) {
                    var html = '<div class="table-responsive">';
                    html += '<table class="table table-bordered table-sm">';
                    html += '<thead class="table-light">';
                    html += '<tr>';
                    html += '<th width="80px">Type</th>';
                    html += '<th width="80px">Source</th>';
                    html += '<th>Item</th>';
                    html += '<th width="80px" class="text-center">Brought</th>';
                    html += '<th width="80px" class="text-center">Used</th>';
                    html += '<th width="80px" class="text-center">Returned</th>';
                    html += '<th>Notes</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';
                    
                    spareparts.forEach(function(item) {
                        var typeBadge = item.item_type === 'tool' 
                            ? '<span class="badge bg-secondary">🔧 Tool</span>'
                            : '<span class="badge bg-primary">⚙ Part</span>';
                        
                        var sourceBadge = parseInt(item.is_from_warehouse) === 0
                            ? '<span class="badge bg-warning text-dark">♻ Bekas</span>'
                            : '<span class="badge bg-success">🏪 WH</span>';
                        
                        html += '<tr>';
                        html += '<td>' + typeBadge + '</td>';
                        html += '<td>' + sourceBadge + '</td>';
                        html += '<td><strong>' + item.sparepart_name + '</strong><br><small class="text-muted">' + item.sparepart_code + '</small></td>';
                        html += '<td class="text-center"><span class="badge bg-info">' + item.quantity_brought + '</span></td>';
                        html += '<td class="text-center"><span class="badge bg-success">' + item.quantity_used + '</span></td>';
                        html += '<td class="text-center">' + (item.quantity_return > 0 ? '<span class="badge bg-warning text-dark">' + item.quantity_return + '</span>' : '-') + '</td>';
                        html += '<td><small>' + (item.usage_notes || '-') + '</small></td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody>';
                    html += '</table>';
                    html += '</div>';
                    
                    // Get work order info from first item
                    var woInfo = spareparts[0];
                    $('#usageModalTitle').html('<i class="fas fa-info-circle text-primary me-2"></i>Work Order: ' + woInfo.work_order_number);
                    $('#usageDetailBody').html(`
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Date:</strong> ${woInfo.report_date || '-'}
                            </div>
                            <div class="col-md-6">
                                <strong>Mechanic:</strong> ${woInfo.mechanic_name || '-'}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Customer:</strong> ${woInfo.customer_name || '-'}
                            </div>
                            <div class="col-md-6">
                                <strong>Unit:</strong> ${woInfo.unit_number || '-'} - ${woInfo.unit_info || ''}
                            </div>
                        </div>
                        <hr>
                        <h6>Spareparts Used:</h6>
                        ${html}
                    `);
                    $('#usageDetailModal').modal('show');
                } else {
                    alert('No spareparts found for this work order');
                }
            },
            error: function() {
                alert('Failed to load work order details');
            }
        });
    };
});
</script>
<?= $this->endSection() ?>

