<?= $this->extend('layouts/base') ?>

<?php
/**
 * Finance Invoices Module
 *
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 *
 * Quick Reference:
 * - Status DRAFT     → <span class="badge badge-soft-gray">DRAFT</span>
 * - Status APPROVED  → <span class="badge badge-soft-blue">APPROVED</span>
 * - Status SENT      → <span class="badge badge-soft-cyan">SENT</span>
 * - Status PAID      → <span class="badge badge-soft-green">PAID</span>
 * - Status OVERDUE   → <span class="badge badge-soft-red">OVERDUE</span>
 * - Status CANCELLED → <span class="badge badge-soft-gray">CANCELLED</span>
 * - Type ONE_TIME    → <span class="badge badge-soft-blue">One-Time</span>
 * - Type RECURRING   → <span class="badge badge-soft-cyan">Recurring</span>
 *
 * See optima-pro.css line ~2030 for complete badge standards
 */
?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<!-- Badge styles are centralized in optima-datatable.css -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Back-Billing Alert Widget -->
    <div class="card border-warning border-start border-4 shadow-sm mb-4" id="backBillingAlert" style="display:none;">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="card-title text-warning mb-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="backBillingCount">0</span> Missing Invoices Detected
                    </h5>
                    <p class="card-text mb-0">
                        <strong id="backBillingContracts">0</strong> contracts have overdue billing periods. 
                        Total estimated amount: <strong class="text-success">Rp <span id="backBillingAmount">0</span></strong>
                        <br>
                        <small class="text-muted">Oldest overdue: <span id="backBillingOldest">0</span> days</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-warning" onclick="showBackBillingModal()">
                        <i class="fas fa-eye me-2"></i>View Details
                    </button>
                    <button class="btn btn-primary" onclick="autoGenerateBackBilling()">
                        <i class="fas fa-magic me-2"></i>Auto-Generate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small"><i class="fas fa-filter text-primary me-1"></i>Status</label>
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
                    <label class="form-label fw-semibold small"><i class="fas fa-tag text-info me-1"></i>Type</label>
                    <select class="form-select form-select-sm" id="filter_type">
                        <option value="">All Types</option>
                        <option value="ONE_TIME">One-Time</option>
                        <option value="RECURRING">Recurring</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small"><i class="fas fa-calendar text-success me-1"></i>Date Range</label>
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
    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2 text-primary"></i>Invoice Management
                </h5>
                <p class="text-muted small mb-0">
                    Manage invoices, approvals, dan payment tracking
                    <span class="ms-2 text-info">
                        <i class="bi bi-info-circle me-1"></i>
                        <small>Tip: Gunakan filter Status dan Date Range untuk mempersempit daftar</small>
                    </span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('finance') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Dashboard
                </a>
                <button class="btn btn-success btn-sm" onclick="showGenerateModal()">
                    <i class="fas fa-plus me-1"></i>Generate Invoice
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="invoicesTable" class="table table-hover table-striped mb-0">
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
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Generate Invoice Modal -->
<div class="modal fade modal-wide" id="generateInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
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

<?= $this->section('javascript') ?>
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
                    return data === 'ONE_TIME' ? '<span class="badge badge-soft-blue">One-Time</span>' : '<span class="badge badge-soft-cyan">Recurring</span>';
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
                            <a href="<?= base_url('finance/invoices/view/') ?>${row.id}" class="btn btn-sm btn-outline-primary btn-icon-only" title="View">
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
    
    // Load back-billing stats on page load
    loadBackBillingStats();
});

function reloadTable() {
    invoicesTable.ajax.reload();
}

// Back-Billing Functions
function loadBackBillingStats() {
    fetch('<?= base_url('finance/getBackBillingStats') ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.data) {
            const stats = data.data;
            
            // Show alert only if there are missing invoices
            if (stats.total_missing > 0) {
                $('#backBillingAlert').show();
                $('#backBillingCount').text(stats.total_missing);
                $('#backBillingContracts').text(stats.total_contracts_affected);
                $('#backBillingAmount').text(new Intl.NumberFormat('id-ID').format(stats.total_estimated_amount));
                $('#backBillingOldest').text(stats.oldest_overdue_days);
            } else {
                $('#backBillingAlert').hide();
            }
        }
    })
    .catch(error => {
        console.error('Failed to load back-billing stats:', error);
    });
}

function showBackBillingModal() {
    // Load detailed missing invoices
    fetch('<?= base_url('finance/detectBackBilling') ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.data) {
            let html = '<div class="table-responsive"><table class="table table-sm table-striped">';
            html += '<thead><tr><th>Contract</th><th>Unit</th><th>Period</th><th>Days Overdue</th><th>Amount</th><th>Action</th></tr></thead><tbody>';
            
            data.data.forEach(invoice => {
                html += `<tr>
                    <td>${invoice.contract_number}<br><small class="text-muted">${invoice.customer_name}</small></td>
                    <td>${invoice.unit_number}</td>
                    <td>${invoice.period_start} to ${invoice.period_end}</td>
                    <td><span class="badge badge-soft-red">${invoice.days_overdue} days</span></td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(invoice.estimated_amount)}</td>
                    <td><button class="btn btn-sm btn-primary" onclick="generateSingleBackBilling(${invoice.contract_id})">Generate</button></td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            
            // Create modal dynamically
            const modalHtml = `
                <div class="modal fade modal-wide" id="backBillingModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-light">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Missing Invoices Details
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                ${html}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                               <button type="button" class="btn btn-primary" onclick="autoGenerateBackBilling()">
                                    <i class="fas fa-magic me-2"></i>Generate All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            $('#backBillingModal').remove();
            
            // Append and show
            $('body').append(modalHtml);
            new bootstrap.Modal(document.getElementById('backBillingModal')).show();
        }
    });
}

function autoGenerateBackBilling() {
    OptimaConfirm.generic({
        title: 'Generate Semua Back-Billing',
        text: 'Ini akan men-generate SEMUA invoice yang belum terbuat. Lanjutkan?',
        icon: 'info',
        confirmText: '<i class="fas fa-magic me-1"></i>Ya, Generate Semua',
        confirmButtonColor: '#0d6efd',
        onConfirm: function() {
            // Get all unique contract IDs from missing invoices
            fetch('<?= base_url('finance/detectBackBilling') ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.data) {
            const contractIds = [...new Set(data.data.map(inv => inv.contract_id))];
            
            // Show loading
            const loadingAlert = `
                <div class="alert alert-info" id="backBillingProgress">
                    <i class="fas fa-spinner fa-spin me-2"></i>Generating back-billing invoices... <span id="progressText">0/${contractIds.length}</span>
                </div>
            `;
            $('body').append(loadingAlert);
            
            // Generate for each contract
            let completed = 0;
            const promises = contractIds.map(contractId => {
                return generateSingleBackBilling(contractId, false)
                    .then(() => {
                        completed++;
                        $('#progressText').text(`${completed}/${contractIds.length}`);
                    });
            });
            
            Promise.all(promises).then(() => {
                $('#backBillingProgress').remove();
                alertSwal('success', 'Back-billing generation selesai!');
                loadBackBillingStats();
                invoicesTable.ajax.reload();
                $('#backBillingModal').modal('hide');
            });
        }
    });
        }
    });
}

function generateSingleBackBilling(contractId, showAlert = true) {
    const formData = new FormData();
    formData.append('contract_id', contractId);
    formData.append('auto_approve', 'false');
    
    return fetch('<?= base_url('finance/generateBackBilling') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (showAlert) {
            if (data.success) {
                OptimaNotify.success(`Generated ${data.count} invoice(s) for total Rp ${new Intl.NumberFormat('id-ID').format(data.total_amount)}`);
                loadBackBillingStats();
                invoicesTable.ajax.reload();
            } else {
                OptimaNotify.error('Error: ' + (data.message || 'Failed to generate back-billing'));
}
        }
        return data;
    });
}

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
            alertSwal('success', `Invoice berhasil dibuat: ${data.invoice_number}`);
            bootstrap.Modal.getInstance(document.getElementById('generateInvoiceModal')).hide();
            invoicesTable.ajax.reload();
        } else {
            alertSwal('error', data.message, 'Gagal Generate Invoice');
        }
    });
}

function approveInvoice(id) {
    OptimaConfirm.approve({
        title: 'Setujui Invoice',
        text: 'Apakah Anda yakin ingin menyetujui invoice ini?',
        confirmText: '<i class="fas fa-check me-1"></i>Ya, Setujui',
        onConfirm: function() {
            fetch(`<?= base_url('finance/invoices/approve/') ?>${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alertSwal('success', 'Invoice berhasil disetujui');
            invoicesTable.ajax.reload();
        } else {
            alertSwal('error', data.message, 'Gagal Menyetujui');
        }
    });
        }
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
        if (data.success) {
            OptimaNotify.success('Invoice marked as paid');
            invoicesTable.ajax.reload();
        } else {
            OptimaNotify.error('Error: ' + data.message);
        }
    });
}

function cancelInvoice(id) {
    OptimaConfirm.generic({
        title: 'Batalkan Invoice?',
        html: `
            <div class="text-start">
                <label class="form-label" for="optimaCancelInvoiceReason">Alasan pembatalan</label>
                <input id="optimaCancelInvoiceReason" type="text" class="form-control" placeholder="Masukkan alasan..." autofocus>
            </div>
        `,
        icon: 'warning',
        confirmText: 'Ya, Batalkan!',
        cancelText: (typeof window.lang === 'function' ? window.lang('back') : 'Back'),
        confirmButtonColor: '#dc3545',
        onConfirm: function() {
            var modal = document.getElementById('optimaConfirmModal');
            var input = modal ? modal.querySelector('#optimaCancelInvoiceReason') : null;
            if (!input) input = document.getElementById('optimaCancelInvoiceReason');
            
            var reason = input && input.value ? input.value.trim() : '';
            if (!reason) {
                OptimaNotify.warning('Alasan harus diisi!', 'Validasi');
                return;
            }
            
            var formData = new FormData();
            formData.append('reason', reason);
            
            fetch(\`<?= base_url('finance/invoices/cancel/') ?>\${id}\`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    OptimaNotify.success('Invoice berhasil dibatalkan');
                    invoicesTable.ajax.reload();
                } else {
                    OptimaNotify.error('Error: ' + data.message);
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
