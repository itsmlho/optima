<?= $this->extend('layouts/base') ?>

<?php
/**
 * Sparepart Usage & Returns - Warehouse
 * BADGE/CARD: Optima badge-soft-* in JS; card-header bg-light; styles in optima-pro.css (SPAREPART USAGE PAGE).
 */
?>
<?= $this->section('content') ?>

<div class="sparepart-usage-page">
<!-- Page Header -->
<div class="mb-3">
    <h4 class="fw-bold mb-1">
        <i class="bi bi-arrow-left-right me-2 text-primary"></i>
        Sparepart Usage & Returns Management
    </h4>
    <p class="text-muted mb-0">Track sparepart usage from warehouse to service and manage returns</p>
</div>

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
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-recycle stat-icon text-warning"></i>
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

    <!-- Main Content Card with Source Tabs -->
    <div class="card table-card shadow mb-4">
        <div class="card-header bg-light">
            <div class="row align-items-center mb-2">
                <div class="col">
                    <h5 class="card-title fw-bold m-0">
                        <i class="fas fa-tools text-primary me-2"></i>
                        <?= lang('App.sparepart_usage_returns') ?>
                    </h5>
                </div>
            </div>
            <!-- Top-level: Source Tabs (Work Orders / SPK) -->
            <ul class="nav nav-tabs border-0" id="sourceTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="wo-source-tab" data-bs-toggle="tab" data-bs-target="#source-wo" type="button" role="tab" aria-selected="true">
                        <i class="fas fa-wrench me-1"></i><strong>Work Orders</strong>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="spk-source-tab" data-bs-toggle="tab" data-bs-target="#source-spk" type="button" role="tab" aria-selected="false">
                        <i class="fas fa-clipboard-list me-1"></i><strong>SPK</strong>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="sourceTabContent">

                <!-- ===== WORK ORDERS TAB ===== -->
                <div class="tab-pane fade show active" id="source-wo" role="tabpanel">
                    <ul class="nav nav-pills mb-3 mt-1" id="woSubTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="wo-usage-tab" data-bs-toggle="tab" data-bs-target="#wo-usage" type="button" role="tab">
                                <i class="fas fa-list-check me-1"></i><?= lang('Warehouse.usage') ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="wo-returns-tab" data-bs-toggle="tab" data-bs-target="#wo-returns" type="button" role="tab">
                                <i class="fas fa-undo me-1"></i><?= lang('Warehouse.returns') ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="wo-nonwh-tab" data-bs-toggle="tab" data-bs-target="#wo-nonwh" type="button" role="tab">
                                <i class="fas fa-recycle me-1"></i>Non-Warehouse
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- WO: Usage -->
                        <div class="tab-pane fade show active" id="wo-usage" role="tabpanel">
                            <?php if (isset($usage_table_exists) && !$usage_table_exists): ?>
                            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Table <code>work_order_sparepart_usage</code> is not available yet.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0" id="woUsageTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30px;"></th>
                                            <th style="width: 120px;">Reference</th>
                                            <th style="width: 100px;">Date</th>
                                            <th>Customer</th>
                                            <th>Unit</th>
                                            <th style="width: 100px; text-align: center;">Items</th>
                                            <th style="width: 70px; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                        <!-- WO: Returns -->
                        <div class="tab-pane fade" id="wo-returns" role="tabpanel">
                            <?php if (isset($return_table_exists) && !$return_table_exists): ?>
                            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Table <code>work_order_sparepart_returns</code> is not available yet.</div>
                            <?php else: ?>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Filter Status</label>
                                    <select class="form-select" id="wo-filter-status">
                                        <option value="PENDING" selected>Pending</option>
                                        <option value="CONFIRMED">Confirmed</option>
                                        <option value="ALL">All</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" onclick="applyReturnFilters('WO')">
                                        <i class="fas fa-search me-2"></i>Apply Filter
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm mb-0" id="woReturnsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 130px;">Reference</th>
                                            <th style="min-width: 200px;">Item Details</th>
                                            <th style="width: 180px;">Customer / Unit</th>
                                            <th style="width: 120px;">Mechanic</th>
                                            <th style="width: 150px; text-align: center;">Quantity</th>
                                            <th style="width: 70px; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                        <!-- WO: Non-Warehouse -->
                        <div class="tab-pane fade" id="wo-nonwh" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0" id="woNonwhTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 120px;">Reference</th>
                                            <th>Item Name</th>
                                            <th style="width: 80px;">Type</th>
                                            <th style="width: 90px;">Item Source</th>
                                            <th>Notes</th>
                                            <th style="width: 80px; text-align: center;">Qty</th>
                                            <th style="width: 180px;">Customer / Unit</th>
                                            <th style="width: 110px;">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- /#source-wo -->

                <!-- ===== SPK TAB ===== -->
                <div class="tab-pane fade" id="source-spk" role="tabpanel">
                    <ul class="nav nav-pills mb-3 mt-1" id="spkSubTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="spk-usage-tab" data-bs-toggle="tab" data-bs-target="#spk-usage" type="button" role="tab">
                                <i class="fas fa-list-check me-1"></i><?= lang('Warehouse.usage') ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="spk-returns-tab" data-bs-toggle="tab" data-bs-target="#spk-returns" type="button" role="tab">
                                <i class="fas fa-undo me-1"></i><?= lang('Warehouse.returns') ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="spk-nonwh-tab" data-bs-toggle="tab" data-bs-target="#spk-nonwh" type="button" role="tab">
                                <i class="fas fa-recycle me-1"></i>Non-Warehouse
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- SPK: Usage -->
                        <div class="tab-pane fade show active" id="spk-usage" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0" id="spkUsageTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30px;"></th>
                                            <th style="width: 120px;">Reference</th>
                                            <th style="width: 100px;">Date</th>
                                            <th>Customer</th>
                                            <th>Unit</th>
                                            <th style="width: 100px; text-align: center;">Items</th>
                                            <th style="width: 70px; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <!-- SPK: Returns -->
                        <div class="tab-pane fade" id="spk-returns" role="tabpanel">
                            <?php if (isset($return_table_exists) && !$return_table_exists): ?>
                            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>SPK returns table is not available yet.</div>
                            <?php else: ?>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Filter Status</label>
                                    <select class="form-select" id="spk-filter-status">
                                        <option value="PENDING" selected>Pending</option>
                                        <option value="CONFIRMED">Confirmed</option>
                                        <option value="ALL">All</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" onclick="applyReturnFilters('SPK')">
                                        <i class="fas fa-search me-2"></i>Apply Filter
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm mb-0" id="spkReturnsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 130px;">Reference</th>
                                            <th style="min-width: 200px;">Item Details</th>
                                            <th style="width: 180px;">Customer / Unit</th>
                                            <th style="width: 120px;">Mechanic</th>
                                            <th style="width: 150px; text-align: center;">Quantity</th>
                                            <th style="width: 70px; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                        <!-- SPK: Non-Warehouse -->
                        <div class="tab-pane fade" id="spk-nonwh" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0" id="spkNonwhTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 120px;">Reference</th>
                                            <th>Item Name</th>
                                            <th style="width: 80px;">Type</th>
                                            <th style="width: 90px;">Item Source</th>
                                            <th>Notes</th>
                                            <th style="width: 80px; text-align: center;">Qty</th>
                                            <th style="width: 180px;">Customer / Unit</th>
                                            <th style="width: 110px;">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- /#source-spk -->

            </div><!-- /.tab-content sourceTabContent -->
        </div><!-- /.card-body -->
    </div><!-- /.card -->
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
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i><?= lang('App.detail') ?> <?= lang('Warehouse.returns') ?> <?= lang('Warehouse.sparepart') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
</div><!-- .sparepart-usage-page -->

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Table instances
let woUsageDT, woReturnsDT, woNonwhDT;
let spkUsageDT, spkReturnsDT, spkNonwhDT;

const tableState = {
    woUsage: false, woReturns: false, woNonwh: false,
    spkUsage: false, spkReturns: false, spkNonwh: false
};

// Setup CSRF token for all AJAX requests
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
    beforeSend: function(xhr, settings) {
        if (settings.type !== 'GET' && settings.type !== 'HEAD') {
            if (!settings.data) settings.data = {};
            if (typeof settings.data === 'string') {
                settings.data += '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>';
            } else {
                settings.data['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            }
        }
    }
});

// ----------------------------------------------------------------
// Shared helper: format expand-row sparepart table
// ----------------------------------------------------------------
function formatSparepartsTable(spareparts) {
    if (spareparts && spareparts.error) {
        return '<div class="p-3 text-danger">Error: ' + (spareparts.message || 'Unknown error') + '</div>';
    }
    if (!Array.isArray(spareparts) || spareparts.length === 0) {
        return '<div class="p-3 text-muted">No spareparts found</div>';
    }
    var html = '<div class="p-3 bg-light"><table class="table table-sm table-bordered mb-0">';
    html += '<thead class="table-light"><tr><th width="80px">Type</th><th width="80px">Source</th><th>Item</th><th width="70px" class="text-center">Brought</th><th width="70px" class="text-center">Used</th><th width="70px" class="text-center">Returned</th><th>Notes</th></tr></thead><tbody>';
    spareparts.forEach(function(item) {
        var typeBadge = item.item_type === 'tool'
            ? '<span class="badge badge-soft-gray">🔧 Tool</span>'
            : '<span class="badge badge-soft-blue">⚙ Part</span>';
        var srcType = (item.source_type || '').toUpperCase();
        var sourceBadge;
        if (srcType === 'KANIBAL') {
            var kanibalUnit = item.source_unit_number ? ' (' + item.source_unit_number + ')' : '';
            sourceBadge = '<span class="badge badge-soft-orange">♻ Kanibal' + kanibalUnit + '</span>';
        } else if (srcType === 'BEKAS' || parseInt(item.is_from_warehouse) === 0) {
            sourceBadge = '<span class="badge badge-soft-yellow">♻ Bekas</span>';
        } else {
            sourceBadge = '<span class="badge badge-soft-green">🏪 WH</span>';
        }
        html += '<tr><td>' + typeBadge + '</td><td>' + sourceBadge + '</td>'
            + '<td><strong>' + item.sparepart_name + '</strong><br><small class="text-muted">' + item.sparepart_code + '</small></td>'
            + '<td class="text-center"><span class="badge badge-soft-cyan">' + item.quantity_brought + '</span></td>'
            + '<td class="text-center"><span class="badge badge-soft-green">' + item.quantity_used + '</span></td>'
            + '<td class="text-center">' + (item.quantity_return > 0 ? '<span class="badge badge-soft-yellow">' + item.quantity_return + '</span>' : '-') + '</td>'
            + '<td><small>' + (item.usage_notes || '-') + '</small></td></tr>';
    });
    html += '</tbody></table></div>';
    return html;
}

// ----------------------------------------------------------------
// Usage table factory (shared for WO and SPK)
// ----------------------------------------------------------------
function initUsageTable(source) {
    const stateKey = (source === 'SPK' ? 'spk' : 'wo') + 'Usage';
    if (tableState[stateKey]) return;
    const tableId  = source === 'SPK' ? '#spkUsageTable' : '#woUsageTable';
    if ($(tableId).length === 0) return;
    tableState[stateKey] = true;

    const dt = $(tableId).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/sparepart-usage/get-usage-grouped') ?>',
            type: 'POST',
            data: function(d) {
                d.source = source;
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                return d;
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        columns: [
            { className: 'details-control', orderable: false, data: null, defaultContent: '<i class="fas fa-plus-circle text-muted cursor-pointer"></i>' },
            { data: 'reference_number', name: 'reference_number', render: function(data) { return `<strong class="text-primary">${data}</strong>`; } },
            { data: 'report_date', name: 'report_date' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'unit_number', name: 'unit_number', render: function(data, type, row) { return `<strong>${data}</strong><br><small class="text-muted">${row.unit_info}</small>`; } },
            { data: 'total_items', name: 'total_items', className: 'text-center', render: function(data, type, row) {
                let html = `<strong>${data}</strong> items`;
                if (row.nonwarehouse_items > 0) {
                    html += `<br><small class="text-muted">${row.warehouse_items} WH, <span class="text-warning fw-semibold">${row.nonwarehouse_items} Non-WH</span></small>`;
                } else {
                    html += `<br><small class="text-muted">${row.warehouse_items} WH</small>`;
                }
                return html;
            }},
            { data: 'record_id', orderable: false, className: 'text-center', render: function(data, type, row) {
                return `<button class="btn btn-sm btn-outline-primary btn-icon-only" onclick="viewWorkOrderDetail(${data})" title="View Detail"><i class="fas fa-eye"></i></button>`;
            }}
        ],
        order: [[2, 'desc']],
        pageLength: 25,
        language: { search: 'Search:', lengthMenu: 'Show _MENU_ entries', info: 'Showing _START_ to _END_ of _TOTAL_ entries', infoEmpty: 'No entries found', infoFiltered: '(filtered from _MAX_ total)', paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' } }
    });

    // Expand/collapse row
    $(tableId + ' tbody').on('click', 'td.details-control', function() {
        const tr   = $(this).closest('tr');
        const row  = dt.row(tr);
        const icon = $(this).find('i');
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            icon.removeClass('fa-minus-circle text-primary').addClass('fa-plus-circle text-muted');
        } else {
            icon.removeClass('fa-plus-circle text-muted').addClass('fa-spinner fa-spin');
            const rowData  = row.data();
            const recordId = rowData.record_id;
            const url = source === 'SPK'
                ? '<?= base_url('warehouse/sparepart-usage/get-spk-spareparts') ?>/' + recordId
                : '<?= base_url('warehouse/sparepart-usage/get-work-order-spareparts') ?>/' + recordId;
            $.ajax({
                url: url, type: 'GET', dataType: 'json',
                success: function(response) {
                    row.child(formatSparepartsTable(response)).show();
                    tr.addClass('shown');
                    icon.removeClass('fa-spinner fa-spin').addClass('fa-minus-circle text-primary');
                },
                error: function(xhr, status, error) {
                    if (window.alertSwal) alertSwal('error', 'Failed to load spareparts: ' + error);
                    icon.removeClass('fa-spinner fa-spin').addClass('fa-plus-circle text-muted');
                }
            });
        }
    });

    if (source === 'WO') woUsageDT = dt; else spkUsageDT = dt;
}

// ----------------------------------------------------------------
// Returns table factory (shared for WO and SPK)
// ----------------------------------------------------------------
function initReturnsTable(source) {
    const stateKey = (source === 'SPK' ? 'spk' : 'wo') + 'Returns';
    if (tableState[stateKey]) return;
    const tableId  = source === 'SPK' ? '#spkReturnsTable' : '#woReturnsTable';
    const filterId = source === 'SPK' ? '#spk-filter-status' : '#wo-filter-status';
    if ($(tableId).length === 0) return;
    tableState[stateKey] = true;

    const dt = $(tableId).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/sparepart-usage/get-returns') ?>',
            type: 'POST',
            data: function(d) {
                d.source = source;
                d.status = $(filterId).val();
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                return d;
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        columns: [
            { data: 'reference_number', render: function(data, type, row) { return `<strong class="text-primary">${data}</strong><br><small class="text-muted">${row.created_at}</small>`; } },
            { data: 'sparepart_name', name: 'sparepart_name', render: function(data, type, row) {
                let typeBadge = row.item_type === 'tool' ? '<span class="badge badge-soft-gray me-1">🔧 Tool</span>' : '<span class="badge badge-soft-blue me-1">⚙ Part</span>';
                let sourceBadge = parseInt(row.is_from_warehouse) === 0 ? '<span class="badge badge-soft-yellow">♻ Bekas</span>' : '<span class="badge badge-soft-green">🏪 WH</span>';
                return `${typeBadge} ${sourceBadge}<br><strong>${data}</strong><br><small class="text-muted">${row.sparepart_code}</small>`;
            }},
            { data: 'customer_name', render: function(data, type, row) { return `<strong>${data || '-'}</strong><br><small class="text-muted"><i class="fas fa-truck"></i> ${row.unit_number || '-'}</small>`; } },
            { data: 'mechanic_name', render: function(data) { return data && data !== '-' ? `<small>${data}</small>` : '<small class="text-muted">-</small>'; } },
            { data: 'quantity_brought', className: 'text-center', render: function(data, type, row) {
                let html = `<small class="text-muted d-block">Brought: <span class="badge badge-soft-cyan">${data}</span></small>`;
                html += `<small class="text-muted d-block">Used: <span class="badge badge-soft-green">${row.quantity_used}</span></small>`;
                if (row.quantity_return > 0) html += `<small class="text-muted d-block">Return: <span class="badge badge-soft-yellow">${row.quantity_return}</span></small>`;
                if (row.status === 'PENDING') html += `<small class="d-block mt-1"><span class="badge badge-soft-yellow">Pending</span></small>`;
                else if (row.status === 'CONFIRMED') html += `<small class="d-block mt-1"><span class="badge badge-soft-green">Confirmed</span></small>`;
                return html;
            }},
            { data: 'id', orderable: false, className: 'text-center', render: function(data, type, row) {
                return `<button class="btn btn-sm btn-outline-primary btn-icon-only" onclick="viewReturnDetail(${data},'${source}')" title="View Detail"><i class="fas fa-eye"></i></button>`;
            }}
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: { search: 'Search:', lengthMenu: 'Show _MENU_ entries', info: 'Showing _START_ to _END_ of _TOTAL_ entries', infoEmpty: 'No entries found', infoFiltered: '(filtered from _MAX_ total)', paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' } }
    });

    if (source === 'WO') woReturnsDT = dt; else spkReturnsDT = dt;
}

// ----------------------------------------------------------------
// Non-Warehouse table factory (shared for WO and SPK)
// ----------------------------------------------------------------
function initNonwhTable(source) {
    const stateKey = (source === 'SPK' ? 'spk' : 'wo') + 'Nonwh';
    if (tableState[stateKey]) return;
    const tableId = source === 'SPK' ? '#spkNonwhTable' : '#woNonwhTable';
    if ($(tableId).length === 0) return;
    tableState[stateKey] = true;

    const dt = $(tableId).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/sparepart-usage/get-manual-entries') ?>',
            type: 'POST',
            data: function(d) {
                d.source = source;
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                return d;
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        columns: [
            { data: 'reference_number', render: function(data) { return `<strong class="text-primary">${data}</strong>`; } },
            { data: 'item_name' },
            { data: 'item_type', orderable: false, render: function(data) { return data === 'tool' ? '<span class="badge badge-soft-gray">🔧 Tool</span>' : '<span class="badge badge-soft-blue">⚙ Part</span>'; } },
            { data: 'item_source', orderable: false, render: function(data) {
                const src = (data || '').toUpperCase();
                if (src === 'KANIBAL') return '<span class="badge badge-soft-orange">♻ Kanibal</span>';
                return '<span class="badge badge-soft-yellow">♻ Bekas</span>';
            }},
            { data: 'source_notes', render: function(data) { return data && data !== '-' ? `<small>${data}</small>` : '<small class="text-muted">-</small>'; } },
            { data: 'quantity_brought', className: 'text-center', render: function(data, type, row) { return `<span class="badge badge-soft-cyan">${data}</span> <small>${row.satuan}</small>`; } },
            { data: 'customer_name', render: function(data, type, row) { return `<strong>${data || '-'}</strong><br><small class="text-muted">${row.unit_number || '-'}</small>`; } },
            { data: 'created_at' }
        ],
        order: [[7, 'desc']],
        pageLength: 25,
        language: { search: 'Search:', lengthMenu: 'Show _MENU_ entries', info: 'Showing _START_ to _END_ of _TOTAL_ entries', infoEmpty: 'No entries found', infoFiltered: '(filtered from _MAX_ total)', paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' } }
    });

    if (source === 'WO') woNonwhDT = dt; else spkNonwhDT = dt;
}

// ----------------------------------------------------------------
// Tab event handling
// ----------------------------------------------------------------
$(document).ready(function() {
    // Source tab switches
    $('#sourceTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).data('bs-target');
        setTimeout(function() {
            if (target === '#source-wo') {
                if ($('#wo-usage').hasClass('active'))   { <?php if (isset($usage_table_exists) && $usage_table_exists): ?> initUsageTable('WO'); <?php endif; ?> }
                else if ($('#wo-returns').hasClass('active')) { <?php if (isset($return_table_exists) && $return_table_exists): ?> initReturnsTable('WO'); <?php endif; ?> }
                else if ($('#wo-nonwh').hasClass('active'))   initNonwhTable('WO');
            } else if (target === '#source-spk') {
                if ($('#spk-usage').hasClass('active'))   initUsageTable('SPK');
                else if ($('#spk-returns').hasClass('active')) { <?php if (isset($return_table_exists) && $return_table_exists): ?> initReturnsTable('SPK'); <?php endif; ?> }
                else if ($('#spk-nonwh').hasClass('active'))   initNonwhTable('SPK');
            }
        }, 150);
    });

    // WO sub-tab switches
    $('#woSubTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).data('bs-target');
        setTimeout(function() {
            if (target === '#wo-usage')   { <?php if (isset($usage_table_exists) && $usage_table_exists): ?> initUsageTable('WO'); <?php endif; ?> }
            else if (target === '#wo-returns') { <?php if (isset($return_table_exists) && $return_table_exists): ?> initReturnsTable('WO'); <?php endif; ?> }
            else if (target === '#wo-nonwh')   initNonwhTable('WO');
        }, 150);
    });

    // SPK sub-tab switches
    $('#spkSubTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).data('bs-target');
        setTimeout(function() {
            if (target === '#spk-usage')   initUsageTable('SPK');
            else if (target === '#spk-returns') { <?php if (isset($return_table_exists) && $return_table_exists): ?> initReturnsTable('SPK'); <?php endif; ?> }
            else if (target === '#spk-nonwh')   initNonwhTable('SPK');
        }, 150);
    });

    // Initialize default: WO Usage on page load
    setTimeout(function() {
        <?php if (isset($usage_table_exists) && $usage_table_exists): ?>
        initUsageTable('WO');
        <?php endif; ?>
    }, 300);
});

// ----------------------------------------------------------------
// Apply return filters
// ----------------------------------------------------------------
window.applyReturnFilters = function(source) {
    if (source === 'WO' && woReturnsDT) woReturnsDT.ajax.reload();
    else if (source === 'SPK' && spkReturnsDT) spkReturnsDT.ajax.reload();
};

// ----------------------------------------------------------------
// View usage detail (Work Order sparepart list in modal)
// ----------------------------------------------------------------
window.viewUsageDetail = function(id) {
    $.ajax({
        url: '<?= base_url('warehouse/sparepart-usage/get-usage-detail') ?>/' + id,
        type: 'GET',
        beforeSend: function() {
            $('#usageDetailBody').html('<div class="text-center py-5"><i class="fas fa-circle-notch fa-spin text-primary fs-2"></i><p class="mt-2 text-muted">Loading...</p></div>');
        },
        success: function(response) {
            if (response.success) {
                const data = response.data;
                let typeBadge   = data.item_type === 'tool' ? '<span class="badge badge-soft-gray">🔧 Tool</span>' : '<span class="badge badge-soft-blue">⚙ Sparepart</span>';
                let sourceBadge = data.is_from_warehouse !== undefined && parseInt(data.is_from_warehouse) === 0
                    ? '<span class="badge badge-soft-yellow ms-2">♻ Non-Warehouse</span>'
                    : '<span class="badge badge-soft-green ms-2">🏪 Warehouse</span>';
                let html = `
                    <div class="border-bottom pb-3 mb-3"><div class="row">
                        <div class="col-6"><label class="text-muted small d-block mb-1">Work Order</label><h5 class="mb-0 text-primary fw-semibold">${data.work_order_number || '-'}</h5></div>
                        <div class="col-6 text-end"><label class="text-muted small d-block mb-1">Date</label><div class="fw-semibold">${data.report_date_formatted || '-'}</div></div>
                    </div></div>
                    <div class="mb-3"><label class="text-muted small d-block mb-2">Classification</label><div>${typeBadge}${sourceBadge}</div></div>
                    <div class="bg-light p-3 rounded mb-3"><div class="row">
                        <div class="col-md-4"><label class="text-muted small d-block mb-1">Code</label><div class="fw-semibold">${data.sparepart_code || '-'}</div></div>
                        <div class="col-md-8"><label class="text-muted small d-block mb-1">Name</label><div class="fw-semibold">${data.sparepart_name || '-'}</div></div>
                    </div></div>
                    <div class="mb-3"><label class="text-muted small d-block mb-2">Quantity</label>
                        <div class="row g-2">
                            <div class="col-4"><div class="border rounded p-3 text-center"><div class="text-muted small mb-1">Brought</div><h5 class="mb-0 text-info">${data.quantity_brought || 0}</h5></div></div>
                            <div class="col-4"><div class="border rounded p-3 text-center"><div class="text-muted small mb-1">Used</div><h5 class="mb-0 text-success">${data.quantity_used || 0}</h5></div></div>
                            <div class="col-4"><div class="border rounded p-3 text-center"><div class="text-muted small mb-1">Returned</div><h5 class="mb-0 ${data.quantity_returned > 0 ? 'text-warning' : 'text-secondary'}">${data.quantity_returned > 0 ? data.quantity_returned : '0'}</h5></div></div>
                        </div>
                    </div>
                    <div class="bg-light p-3 rounded mb-3"><div class="row g-3">
                        <div class="col-md-6"><label class="text-muted small d-block mb-1">Customer</label><div class="fw-semibold">${data.customer_name || '-'}</div></div>
                        <div class="col-md-6"><label class="text-muted small d-block mb-1">Unit</label><div class="fw-semibold">${data.unit_number || '-'}</div></div>
                        <div class="col-md-6"><label class="text-muted small d-block mb-1">Mechanic</label><div class="fw-semibold">${data.mechanic_name || '-'}</div></div>
                        <div class="col-md-6"><label class="text-muted small d-block mb-1">Usage Date</label><div class="fw-semibold">${data.used_at_formatted || '-'}</div></div>
                    </div></div>
                    ${data.usage_notes && data.usage_notes !== '-' ? `<div class="border-start border-primary border-3 ps-3 py-2 mb-3"><label class="text-muted small d-block mb-1"><i class="fas fa-sticky-note me-1"></i>Usage Notes</label><div>${data.usage_notes}</div></div>` : ''}
                `;
                $('#usageModalTitle').html('<i class="fas fa-info-circle text-primary me-2"></i>Detail Usage');
                $('#usageDetailBody').html(html);
                $('#usageDetailModal').modal('show');
            } else {
                if (window.OptimaNotify) OptimaNotify.error('Error: ' + (response.message || 'Failed to load data'));
            }
        },
        error: function() {
            if (window.OptimaNotify) OptimaNotify.error('An error occurred while loading data');
        }
    });
};

// ----------------------------------------------------------------
// View WO sparepart list (for usage expand/action)
// ----------------------------------------------------------------
window.viewWorkOrderDetail = function(workOrderId) {
    $.ajax({
        url: '<?= base_url('warehouse/sparepart-usage/get-work-order-spareparts') ?>/' + workOrderId,
        type: 'GET', dataType: 'json',
        success: function(spareparts) {
            if (spareparts && spareparts.error) {
                if (window.OptimaNotify) OptimaNotify.error('Error: ' + (spareparts.message || 'Unknown error'));
                return;
            }
            if (Array.isArray(spareparts) && spareparts.length > 0) {
                var woInfo = spareparts[0];
                var html = '<div class="table-responsive">'
                    + '<table class="table table-bordered table-sm"><thead class="table-light"><tr>'
                    + '<th width="80px">Type</th><th width="80px">Source</th><th>Item</th>'
                    + '<th width="80px" class="text-center">Brought</th><th width="80px" class="text-center">Used</th>'
                    + '<th width="80px" class="text-center">Returned</th><th>Notes</th>'
                    + '</tr></thead><tbody>';
                spareparts.forEach(function(item) {
                    var typeBadge   = item.item_type === 'tool' ? '<span class="badge badge-soft-gray">🔧 Tool</span>' : '<span class="badge badge-soft-blue">⚙ Part</span>';
                    var sourceBadge = parseInt(item.is_from_warehouse) === 0 ? '<span class="badge badge-soft-yellow">♻ Bekas</span>' : '<span class="badge badge-soft-green">🏪 WH</span>';
                    html += '<tr><td>' + typeBadge + '</td><td>' + sourceBadge + '</td>'
                        + '<td><strong>' + item.sparepart_name + '</strong><br><small class="text-muted">' + item.sparepart_code + '</small></td>'
                        + '<td class="text-center"><span class="badge badge-soft-cyan">' + item.quantity_brought + '</span></td>'
                        + '<td class="text-center"><span class="badge badge-soft-green">' + item.quantity_used + '</span></td>'
                        + '<td class="text-center">' + (item.quantity_return > 0 ? '<span class="badge badge-soft-yellow">' + item.quantity_return + '</span>' : '-') + '</td>'
                        + '<td><small>' + (item.usage_notes || '-') + '</small></td></tr>';
                });
                html += '</tbody></table></div>';
                $('#usageModalTitle').html('<i class="fas fa-info-circle text-primary me-2"></i>Work Order: ' + woInfo.work_order_number);
                $('#usageDetailBody').html(`
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Date:</strong> ${woInfo.report_date || '-'}</div>
                        <div class="col-md-6"><strong>Mechanic:</strong> ${woInfo.mechanic_name || '-'}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Customer:</strong> ${woInfo.customer_name || '-'}</div>
                        <div class="col-md-6"><strong>Unit:</strong> ${woInfo.unit_number || '-'} ${woInfo.unit_info || ''}</div>
                    </div><hr>
                    <h6>Spareparts Used:</h6>${html}
                `);
                $('#usageDetailModal').modal('show');
            } else {
                if (window.OptimaNotify) OptimaNotify.warning('No spareparts found for this work order');
            }
        },
        error: function() {
            if (window.OptimaNotify) OptimaNotify.error('Failed to load work order details');
        }
    });
};

// ----------------------------------------------------------------
// View return detail
// ----------------------------------------------------------------
window.viewReturnDetail = function(id, sourceType) {
    sourceType = sourceType || 'WO';
    $.ajax({
        url: sourceType === 'SPK'
            ? '<?= base_url('warehouse/sparepart-usage/get-return-detail') ?>/' + id + '?source=SPK'
            : '<?= base_url('warehouse/sparepart-usage/get-return-detail') ?>/' + id,
        type: 'GET',
        beforeSend: function() {
            $('#returnDetailBody').html('<div class="text-center py-5"><i class="fas fa-circle-notch fa-spin text-primary fs-2"></i><p class="mt-2 text-muted">Loading...</p></div>');
        },
        success: function(response) {
            if (response.success) {
                const data = response.data;
                let typeBadge   = data.item_type === 'tool' ? '<span class="badge badge-soft-gray">🔧 Tool</span>' : '<span class="badge badge-soft-blue">⚙ Sparepart</span>';
                let sourceBadge = parseInt(data.is_from_warehouse) === 0 ? '<span class="badge badge-soft-yellow ms-2">♻ Non-Warehouse</span>' : '<span class="badge badge-soft-green ms-2">🏪 Warehouse</span>';
                let srcBadge    = sourceType === 'SPK' ? '<span class="badge badge-soft-purple me-1">SPK</span>' : '<span class="badge badge-soft-blue me-1">WO</span>';
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Source:</strong><br>${srcBadge}</div>
                        <div class="col-md-6"><strong>${sourceType === 'SPK' ? 'SPK Number' : 'Work Order'}:</strong><br><span class="badge badge-soft-blue">${data.work_order_number || data.reference_number || '-'}</span></div>
                    </div>
                    <div class="row mb-3"><div class="col-md-6"><strong>Date:</strong><br>${data.report_date_formatted || '-'}</div></div>
                    <hr>
                    <div class="row mb-3"><div class="col-md-12 mb-2"><strong>Item:</strong><br>${typeBadge}${sourceBadge}</div></div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Code:</strong><br>${data.sparepart_code || '-'}</div>
                        <div class="col-md-6"><strong>Name:</strong><br><strong>${data.sparepart_name || '-'}</strong></div>
                    </div><hr>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Brought:</strong><br><span class="badge badge-soft-cyan">${data.quantity_brought} ${data.satuan}</span></div>
                        <div class="col-md-4"><strong>Used:</strong><br><span class="badge badge-soft-green">${data.quantity_used} ${data.satuan}</span></div>
                        <div class="col-md-4"><strong>Return:</strong><br><span class="badge badge-soft-yellow">${data.quantity_return} ${data.satuan}</span></div>
                    </div><hr>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Customer:</strong><br>${data.customer_name || '-'}</div>
                        <div class="col-md-6"><strong>Unit:</strong><br>${data.unit_number || '-'}</div>
                    </div><hr>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Mechanic:</strong><br>${data.mechanic_name || '-'}</div>
                        <div class="col-md-6"><strong>Status:</strong><br><span class="badge ${data.status === 'PENDING' ? 'badge-soft-yellow' : 'badge-soft-green'}">${data.status}</span></div>
                    </div><hr>
                    ${data.return_notes ? `<div class="mb-3"><strong>Return Notes:</strong><br>${data.return_notes}</div>` : ''}
                    ${data.confirmed_at ? `<div class="mb-3"><strong>Confirmed:</strong><br>${data.confirmed_at_formatted || data.confirmed_at} by ${data.confirmed_by_name || '-'}</div>` : ''}
                `;
                if (data.status === 'PENDING') {
                    const confirmUrl = sourceType === 'SPK'
                        ? '<?= base_url('warehouse/sparepart-usage/confirm-spk-return') ?>/' + id
                        : '<?= base_url('warehouse/sparepart-usage/confirm-return') ?>/' + id;
                    html += `<hr><div class="mb-3"><label class="form-label">Confirmation Notes (Optional)</label>
                        <textarea class="form-control" id="confirm-notes" rows="3" placeholder="Add notes if necessary..."></textarea></div>
                        <div class="d-grid"><button class="btn btn-success" onclick="doConfirmReturn('${confirmUrl}')"><i class="fas fa-check me-2"></i>Confirm Return</button></div>`;
                }
                $('#returnDetailBody').html(html);
                $('#returnDetailModal').modal('show');
            } else {
                if (window.OptimaNotify) OptimaNotify.error('Error: ' + (response.message || 'Failed to load data'));
            }
        },
        error: function() {
            if (window.OptimaNotify) OptimaNotify.error('An error occurred while loading data');
        }
    });
};

// ----------------------------------------------------------------
// Confirm return
// ----------------------------------------------------------------
window.doConfirmReturn = function(url) {
    OptimaConfirm.approve({
        title: 'Konfirmasi Return?',
        text: 'Sparepart return ini akan dikonfirmasi.',
        confirmText: 'Ya, Konfirmasi!',
        cancelText: window.lang ? window.lang('cancel') : 'Batal',
        onConfirm: function() {
            const notes = $('#confirm-notes').val() || null;
            $.ajax({
                url: url, type: 'POST',
                data: { notes: notes, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                success: function(response) {
                    if (response.success) {
                        OptimaNotify.success('Sparepart return berhasil dikonfirmasi');
                        $('#returnDetailModal').modal('hide');
                        if (woReturnsDT) woReturnsDT.ajax.reload();
                        if (spkReturnsDT) spkReturnsDT.ajax.reload();
                    } else {
                        OptimaNotify.error('Error: ' + (response.message || 'Gagal mengonfirmasi'));
                    }
                },
                error: function() { OptimaNotify.error('Terjadi kesalahan saat konfirmasi'); }
            });
        }
    });
};

window.confirmReturn = function(id) {
    doConfirmReturn('<?= base_url('warehouse/sparepart-usage/confirm-return') ?>/' + id);
};
</script>
<?= $this->endSection() ?>
