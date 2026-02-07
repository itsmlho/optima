<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
.status-badge {
    padding: 0.35rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 6px;
    text-transform: uppercase;
}
.status-draft { background: #6c757d; color: white; }
.status-approved { background: #0dcaf0; color: white; }
.status-sent { background: #0d6efd; color: white; }
.status-paid { background: #198754; color: white; }
.status-overdue { background: #dc3545; color: white; }
.status-cancelled { background: #adb5bd; color: white; }

.action-buttons .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-file-invoice text-primary me-2"></i>Invoice Management
            </h1>
            <p class="text-muted mb-0">Manage invoices, approvals, dan payment tracking</p>
        </div>
        <div>
            <a href="<?= base_url('finance') ?>" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Dashboard
            </a>
            <button class="btn btn-success" onclick="showGenerateModal()">
                <i class="fas fa-plus me-2"></i>Generate Invoice
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" id="filter_status">
                        <option value="">All Status</option>
                        <option value="DRAFT">Draft</option>
                        <option value="APPROVED">Approved</option>
                        <option value="SENT">Sent</option>
                        <option value="PAID">Paid</option>
                        <option value="OVERDUE">Overdue</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Type</label>
                    <select class="form-select form-select-sm" id="filter_type">
                        <option value="">All Types</option>
                        <option value="ONE_TIME">One-Time</option>
                        <option value="RECURRING">Recurring</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Date Range</label>
                    <input type="text" class="form-control form-control-sm" id="filter_daterange" placeholder="Select date range">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary btn-sm w-100" onclick="reloadTable()">
                        <i class="fas fa-filter me-1"></i>Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table id="invoicesTable" class="table table-hover table-striped" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice No</th>
                            <th>Customer</th>
                            <th>Contract</th>
                            <th>Amount</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Generate Invoice Modal -->
<div class="modal fade" id="generateInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice me-2"></i>Generate Invoice
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="invoiceTypeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="one-time-tab" data-bs-toggle="tab" data-bs-target="#one-time" type="button">
                            <i class="fas fa-file me-2"></i>One-Time (from DI)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="recurring-tab" data-bs-toggle="tab" data-bs-target="#recurring" type="button">
                            <i class="fas fa-repeat me-2"></i>Recurring (from Schedule)
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <!-- One-Time Invoice -->
                    <div class="tab-pane fade show active" id="one-time">
                        <form id="generateOneTimeForm">
                            <div class="mb-3">
                                <label class="form-label">Select DI <span class="text-danger">*</span></label>
                                <select class="form-select" id="di_id" name="di_id" required>
                                    <option value="">-- Select Delivery Instruction --</option>
                                </select>
                                <small class="text-muted">Only completed DIs with contracts</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Due Days <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="due_days" value="30" min="1" max="90" required>
                                <small class="text-muted">Days until payment due</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                            
                            <div id="lockedWarning" class="alert alert-warning d-none">
                                <i class="fas fa-lock me-2"></i><strong>Invoice Locked:</strong>
                                <div id="lockReasons" class="mt-2"></div>
                            </div>
                            
                            <div id="diPreview" class="alert alert-info d-none">
                                <h6 class="mb-2">DI Preview:</h6>
                                <div id="diPreviewContent"></div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Recurring Invoice -->
                    <div class="tab-pane fade" id="recurring">
                        <form id="generateRecurringForm">
                            <div class="mb-3">
                                <label class="form-label">Select Schedule <span class="text-danger">*</span></label>
                                <select class="form-select" id="schedule_id" name="schedule_id" required>
                                    <option value="">-- Select Billing Schedule --</option>
                                </select>
                                <small class="text-muted">Active recurring billing schedules</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                            
                            <div id="schedulePreview" class="alert alert-info d-none">
                                <h6 class="mb-2">Schedule Preview:</h6>
                                <div id="schedulePreviewContent"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitGenerateInvoice()">
                    <i class="fas fa-check me-2"></i>Generate Invoice
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
let invoicesTable;

$(document).ready(function() {
    // Initialize DataTable
    invoicesTable = $('#invoicesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('finance/invoices/datatable') ?>',
            type: 'POST',
            data: function(d) {
                d.status = $('#filter_status').val();
                d.type = $('#filter_type').val();
                d.daterange = $('#filter_daterange').val();
            }
        },
        columns: [
            { data: 'invoice_number' },
            { data: 'customer_name' },
            { data: 'contract_number' },
            { 
                data: 'total_amount',
                render: function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            { 
                data: 'issue_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('id-ID');
                }
            },
            { 
                data: 'due_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('id-ID');
                }
            },
            { 
                data: 'invoice_type',
                render: function(data) {
                    return data === 'ONE_TIME' ? '<span class="badge bg-primary">One-Time</span>' : '<span class="badge bg-info">Recurring</span>';
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    return `<span class="status-badge status-${data.toLowerCase()}">${data}</span>`;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    let actions = `
                        <div class="action-buttons btn-group btn-group-sm">
                            <a href="<?= base_url('finance/invoices/view/') ?>${row.id}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                    `;
                    
                    if (row.status === 'DRAFT') {
                        actions += `
                            <button onclick="approveInvoice(${row.id})" class="btn btn-sm btn-success" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="cancelInvoice(${row.id})" class="btn btn-sm btn-danger" title="Cancel">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                    }
                    
                    if (row.status === 'APPROVED' || row.status === 'SENT' || row.status === 'OVERDUE') {
                        actions += `
                            <button onclick="markAsPaid(${row.id})" class="btn btn-sm btn-primary" title="Mark as Paid">
                                <i class="fas fa-money-bill"></i>
                            </button>
                        `;
                    }
                    
                    actions += '</div>';
                    return actions;
                }
            }
        ],
        order: [[4, 'desc']],
        pageLength: 25,
        responsive: true
    });
    
    // Date range picker
    $('#filter_daterange').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear', format: 'DD/MM/YYYY' }
    });
    
    $('#filter_daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
    
    $('#filter_daterange').on('cancel.daterangepicker', function() {
        $(this).val('');
    });
    
    // DI selection change
    $('#di_id').on('change', function() {
        const diId = $(this).val();
        if (!diId) {
            $('#diPreview, #lockedWarning').addClass('d-none');
            return;
        }
        
        // Check if DI is ready for billing
        fetch(`<?= base_url('finance/invoices/check-di-readiness/') ?>${diId}`)
            .then(response => response.json())
            .then(data => {
                if (data.locked) {
                    $('#lockedWarning').removeClass('d-none');
                    $('#lockReasons').html('<ul>' + data.errors.map(e => '<li>' + e + '</li>').join('') + '</ul>');
                    $('#diPreview').addClass('d-none');
                } else {
                    $('#lockedWarning').addClass('d-none');
                    $('#diPreview').removeClass('d-none');
                    $('#diPreviewContent').html(`
                        <p><strong>DI:</strong> ${data.di.nomor_di}</p>
                        <p><strong>Customer:</strong> ${data.di.customer_name}</p>
                        <p><strong>Contract:</strong> ${data.di.contract_number}</p>
                        <p><strong>Estimated Amount:</strong> Rp ${new Intl.NumberFormat('id-ID').format(data.di.estimated_amount || 0)}</p>
                    `);
                }
            });
    });
});

function reloadTable() {
    invoicesTable.ajax.reload();
}

function showGenerateModal() {
    // Load DIs
    fetch('<?= base_url('finance/invoices/get-ready-dis') ?>')
        .then(r => r.json())
        .then(data => {
            const select = $('#di_id');
            select.html('<option value="">-- Select Delivery Instruction --</option>');
            if (data.success && data.data) {
                data.data.forEach(di => {
                    select.append(`<option value="${di.id}">${di.nomor_di} - ${di.customer_name} (${di.contract_number})</option>`);
                });
            }
        });
    
    // Load schedules
    fetch('<?= base_url('finance/invoices/get-active-schedules') ?>')
        .then(r => r.json())
        .then(data => {
            const select = $('#schedule_id');
            select.html('<option value="">-- Select Billing Schedule --</option>');
            if (data.success && data.data) {
                data.data.forEach(s => {
                    select.append(`<option value="${s.id}">${s.contract_number} - ${s.frequency} (Next: ${s.next_billing_date})</option>`);
                });
            }
        });
    
    new bootstrap.Modal(document.getElementById('generateInvoiceModal')).show();
}

function submitGenerateInvoice() {
    const activeTab = document.querySelector('#invoiceTypeTabs .nav-link.active').id;
    const isOneTime = activeTab === 'one-time-tab';
    
    const formData = new FormData(document.getElementById(isOneTime ? 'generateOneTimeForm' : 'generateRecurringForm'));
    const url = isOneTime ? '<?= base_url('finance/invoices/generate-from-di') ?>' : '<?= base_url('finance/invoices/generate-recurring') ?>';
    
    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(`Invoice generated: ${data.invoice_number}`);
            bootstrap.Modal.getInstance(document.getElementById('generateInvoiceModal')).hide();
            invoicesTable.ajax.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function approveInvoice(id) {
    if (!confirm('Approve this invoice?')) return;
    
    fetch(`<?= base_url('finance/invoices/approve/') ?>${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? 'Invoice approved' : 'Error: ' + data.message);
        if (data.success) invoicesTable.ajax.reload();
    });
}

function markAsPaid(id) {
    const paymentDate = prompt('Enter payment date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    if (!paymentDate) return;
    
    const formData = new FormData();
    formData.append('payment_date', paymentDate);
    
    fetch(`<?= base_url('finance/invoices/mark-paid/') ?>${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? 'Invoice marked as paid' : 'Error: ' + data.message);
        if (data.success) invoicesTable.ajax.reload();
    });
}

function cancelInvoice(id) {
    const reason = prompt('Enter cancellation reason:');
    if (!reason) return;
    
    const formData = new FormData();
    formData.append('reason', reason);
    
    fetch(`<?= base_url('finance/invoices/cancel/') ?>${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? 'Invoice cancelled' : 'Error: ' + data.message);
        if (data.success) invoicesTable.ajax.reload();
    });
}
</script>
<?= $this->endSection() ?>
