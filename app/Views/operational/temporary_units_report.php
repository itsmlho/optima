<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-left-right me-2"></i>
                        Temporary Units Tracking Report
                    </h5>
                    <p class="mb-0 small">Unit pinjaman sementara yang harus dikembalikan (TUKAR_MAINTENANCE)</p>
                </div>
                
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h3 class="text-warning" id="totalTemporaryUnits">0</h3>
                                    <p class="mb-0 small">Total Temp                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            orary Units</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <h3 class="text-danger" id="overdueUnits">0</h3>
                                    <p class="mb-0 small">Overdue (>30 days)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <h3 class="text-info" id="avgDaysBorrowed">0</h3>
                                    <p class="mb-0 small">Avg Days Borrowed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h3 class="text-success" id="readyToReturn">0</h3>
                                    <p class="mb-0 small">Original Ready to Return</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Customer</label>
                                    <select id="filterCustomer" class="form-select">
                                        <option value="">All Customers</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Duration</label>
                                    <select id="filterDuration" class="form-select">
                                        <option value="">All Durations</option>
                                        <option value="7">Less than 7 days</option>
                                        <option value="30">7-30 days</option>
                                        <option value="60">30-60 days</option>
                                        <option value="90">More than 60 days</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <button class="btn btn-primary w-100" id="btnApplyFilter">
                                        <i class="bi bi-funnel me-2"></i>Apply Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table id="temporaryUnitsTable" class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Contract No.</th>
                                    <th>Temporary Unit</th>
                                    <th>Original Unit</th>
                                    <th>Start Date</th>
                                    <th>Days Borrowed</th>
                                    <th>Original Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Return Unit Modal -->
<div class="modal fade" id="returnUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Return Original Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    This will disconnect the temporary unit and reconnect the original unit to the contract.
                </div>
                <dl class="row">
                    <dt class="col-sm-5">Customer:</dt>
                    <dd class="col-sm-7" id="returnCustomer"></dd>
                    
                    <dt class="col-sm-5">Contract:</dt>
                    <dd class="col-sm-7" id="returnContract"></dd>
                    
                    <dt class="col-sm-5">Temporary Unit:</dt>
                    <dd class="col-sm-7" id="returnTempUnit"></dd>
                    
                    <dt class="col-sm-5">Original Unit:</dt>
                    <dd class="col-sm-7" id="returnOrigUnit"></dd>
                    
                    <dt class="col-sm-5">Days Borrowed:</dt>
                    <dd class="col-sm-7" id="returnDays"></dd>
                </dl>
                <input type="hidden" id="returnKontrakUnitId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnConfirmReturn">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Process Return
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let temporaryUnitsTable;

$(document).ready(function() {
    initializeDataTable();
    loadCustomerFilter();
    loadSummaryStats();
    
    $('#btnApplyFilter').on('click', function() {
        temporaryUnitsTable.ajax.reload();
        loadSummaryStats();
    });
    
    $('#btnConfirmReturn').on('click', processReturn);
});

function initializeDataTable() {
    temporaryUnitsTable = $('#temporaryUnitsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('operational/get-temporary-units') ?>',
            type: 'POST',
            data: function(d) {
                d.customer_filter = $('#filterCustomer').val();
                d.duration_filter = $('#filterDuration').val();
            }
        },
        columns: [
            { data: 'customer_name' },
            { data: 'no_kontrak' },
            { 
                data: 'temporary_unit',
                render: function(data, type, row) {
                    return `<strong>${data}</strong><br><small class="text-muted">${row.temp_serial}</small>`;
                }
            },
            { 
                data: 'original_unit',
                render: function(data, type, row) {
                    return `${data}<br><small class="text-muted">${row.orig_serial}</small>`;
                }
            },
            { 
                data: 'temporary_start_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            },
            { 
                data: 'days_borrowed',
                render: function(data) {
                    let badgeClass = 'bg-success';
                    if (data > 60) badgeClass = 'bg-danger';
                    else if (data > 30) badgeClass = 'bg-warning';
                    return `<span class="badge ${badgeClass}">${data} days</span>`;
                }
            },
            { 
                data: 'original_workflow_status',
                render: function(data) {
                    if (data === 'MAINTENANCE_COMPLETED') {
                        return '<span class="badge bg-success">Ready to Return</span>';
                    }
                    return `<span class="badge bg-info">${data || 'In Progress'}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    let returnBtn = '';
                    if (row.original_workflow_status === 'MAINTENANCE_COMPLETED') {
                        returnBtn = `<button class="btn btn-sm btn-success" onclick="showReturnModal(${row.kontrak_unit_id}, '${row.customer_name}', '${row.no_kontrak}', '${row.temporary_unit}', '${row.original_unit}', ${row.days_borrowed})" title="Process Return">
                            <i class="bi bi-arrow-counterclockwise"></i> Return
                        </button>`;
                    } else {
                        returnBtn = `<button class="btn btn-sm btn-secondary" disabled title="Original unit still in maintenance">
                            <i class="bi bi-wrench"></i> In Service
                        </button>`;
                    }
                    return returnBtn;
                }
            }
        ],
        order: [[5, 'desc']], // Sort by days borrowed descending
        pageLength: 25
    });
}

function loadCustomerFilter() {
    $.ajax({
        url: '<?= base_url('operational/get-customers-with-temporary-units') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#filterCustomer');
                response.data.forEach(customer => {
                    select.append(`<option value="${customer.id}">${customer.customer_name}</option>`);
                });
            }
        }
    });
}

function loadSummaryStats() {
    $.ajax({
        url: '<?= base_url('operational/get-temporary-units-stats') ?>',
        method: 'GET',
        data: {
            customer_filter: $('#filterCustomer').val(),
            duration_filter: $('#filterDuration').val()
        },
        success: function(response) {
            if (response.success) {
                $('#totalTemporaryUnits').text(response.stats.total_temporary);
                $('#overdueUnits').text(response.stats.overdue);
                $('#avgDaysBorrowed').text(Math.round(response.stats.avg_days));
                $('#readyToReturn').text(response.stats.ready_to_return);
            }
        }
    });
}

function showReturnModal(kontrakUnitId, customer, contract, tempUnit, origUnit, days) {
    $('#returnKontrakUnitId').val(kontrakUnitId);
    $('#returnCustomer').text(customer);
    $('#returnContract').text(contract);
    $('#returnTempUnit').html(`<strong>${tempUnit}</strong>`);
    $('#returnOrigUnit').text(origUnit);
    $('#returnDays').html(`<span class="badge bg-warning">${days} days</span>`);
    
    new bootstrap.Modal(document.getElementById('returnUnitModal')).show();
}

function processReturn() {
    const kontrakUnitId = $('#returnKontrakUnitId').val();
    
    if (!kontrakUnitId) {
        OptimaNotify.error('Data tidak valid');
        return;
    }
    
    $('#btnConfirmReturn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>Processing...');
    
    $.ajax({
        url: '<?= base_url('operational/process-temporary-unit-return') ?>',
        method: 'POST',
        data: {
            kontrak_unit_id: kontrakUnitId
        },
        success: function(response) {
            if (response.success) {
                OptimaNotify.success('Unit berhasil dikembalikan!');
                bootstrap.Modal.getInstance(document.getElementById('returnUnitModal')).hide();
                temporaryUnitsTable.ajax.reload();
                loadSummaryStats();
            } else {
                if (window.OptimaNotify) OptimaNotify.error('Error: ' + response.message);
                else alert('Error: ' + response.message);
            }
        },
        error: function() {
            OptimaNotify.error('Gagal memproses return. Silakan coba lagi.');
        },
        complete: function() {
            $('#btnConfirmReturn').prop('disabled', false).html('<i class="bi bi-arrow-counterclockwise me-2"></i>Process Return');
        }
    });
}
</script>

<?= $this->endSection() ?>
