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
                        <span id="backBillingCount">0</span> <?= lang('Finance.missing_invoices_label') ?>
                    </h5>
                    <p class="card-text mb-0">
                        <strong id="backBillingContracts">0</strong> <?= lang('Finance.contracts_overdue_suffix') ?>
                        <?= lang('Finance.total_estimated_amount') ?> <strong class="text-success">Rp <span id="backBillingAmount">0</span></strong>
                        <br>
                        <small class="text-muted"><?= lang('Finance.oldest_overdue_prefix') ?> <span id="backBillingOldest">0</span> <?= lang('Finance.days_suffix') ?></small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-warning" onclick="showBackBillingModal()">
                        <i class="fas fa-eye me-2"></i><?= lang('Finance.view_details') ?>
                    </button>
                    <button class="btn btn-primary" onclick="autoGenerateBackBilling()">
                        <i class="fas fa-magic me-2"></i><?= lang('Finance.auto_generate') ?>
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
                    <label class="form-label fw-semibold small"><i class="fas fa-filter text-primary me-1"></i><?= lang('Common.status') ?></label>
                    <select class="form-select form-select-sm" id="filter_status">
                        <option value=""><?= lang('App.all_status') ?></option>
                        <option value="DRAFT"><?= lang('Finance.draft') ?></option>
                        <option value="APPROVED"><?= lang('Common.approved') ?></option>
                        <option value="SENT"><?= lang('Finance.sent') ?></option>
                        <option value="PAID"><?= lang('Finance.paid') ?></option>
                        <option value="OVERDUE"><?= lang('Finance.overdue') ?></option>
                        <option value="CANCELLED"><?= lang('Finance.cancelled') ?></option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small"><i class="fas fa-tag text-info me-1"></i><?= lang('Common.type') ?></label>
                    <select class="form-select form-select-sm" id="filter_type">
                        <option value=""><?= lang('App.all_types') ?></option>
                        <option value="ONE_TIME"><?= lang('Finance.invoice_type_one_time') ?></option>
                        <option value="RECURRING"><?= lang('Finance.invoice_type_recurring') ?></option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small"><i class="fas fa-calendar text-success me-1"></i><?= lang('Finance.date_range') ?></label>
                    <input type="text" class="form-control form-control-sm" id="filter_daterange" placeholder="<?= lang('Finance.select_date_range') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary btn-sm w-100" onclick="reloadTable()">
                        <i class="fas fa-filter me-1"></i><?= lang('Common.apply') ?>
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
                    <i class="fas fa-file-invoice me-2 text-primary"></i><?= lang('Finance.invoice_management') ?>
                </h5>
                <p class="text-muted small mb-0">
                    <?= lang('Finance.invoice_management_subtitle') ?>
                    <span class="ms-2 text-info">
                        <i class="bi bi-info-circle me-1"></i>
                        <small><?= lang('Finance.invoices_tip_filter') ?></small>
                    </span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('finance') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i><?= lang('Finance.finance_dashboard_short') ?>
                </a>
                <button class="btn btn-success btn-sm" onclick="showGenerateModal()">
                    <i class="fas fa-plus me-1"></i><?= lang('Finance.generate_invoice') ?>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="invoicesTable" class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?= lang('Finance.invoice_no') ?></th>
                            <th><?= lang('Finance.customer_label') ?></th>
                            <th><?= lang('Finance.contract') ?></th>
                            <th><?= lang('Common.amount') ?></th>
                            <th><?= lang('Finance.issue_date') ?></th>
                            <th><?= lang('Finance.due_date') ?></th>
                            <th><?= lang('Common.type') ?></th>
                            <th><?= lang('Common.status') ?></th>
                            <th class="text-center"><?= lang('Common.actions') ?></th>
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
                    <i class="fas fa-file-invoice me-2"></i><?= lang('Finance.generate_invoice') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="invoiceTypeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="one-time-tab" data-bs-toggle="tab" data-bs-target="#one-time" type="button">
                            <i class="fas fa-file me-2"></i><?= lang('Finance.one_time_from_di') ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="recurring-tab" data-bs-toggle="tab" data-bs-target="#recurring" type="button">
                            <i class="fas fa-repeat me-2"></i><?= lang('Finance.recurring_from_schedule') ?>
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <!-- One-Time Invoice -->
                    <div class="tab-pane fade show active" id="one-time">
                        <form id="generateOneTimeForm">
                            <div class="mb-3">
                                <label class="form-label"><?= lang('Finance.select_di') ?> <span class="text-danger">*</span></label>
                                <select class="form-select" id="di_id" name="di_id" required>
                                    <option value=""><?= lang('Finance.select_delivery_instruction') ?></option>
                                </select>
                                <small class="text-muted"><?= lang('Finance.only_completed_di_contracts') ?></small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><?= lang('Finance.due_days') ?> <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="due_days" value="30" min="1" max="90" required>
                                <small class="text-muted"><?= lang('Finance.days_until_payment_due') ?></small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><?= lang('Finance.notes_optional') ?></label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                            
                            <div id="lockedWarning" class="alert alert-warning d-none">
                                <i class="fas fa-lock me-2"></i><strong><?= lang('Finance.invoice_locked') ?></strong>
                                <div id="lockReasons" class="mt-2"></div>
                            </div>
                            
                            <div id="diPreview" class="alert alert-info d-none">
                                <h6 class="mb-2"><?= lang('Finance.di_preview') ?></h6>
                                <div id="diPreviewContent"></div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Recurring Invoice -->
                    <div class="tab-pane fade" id="recurring">
                        <form id="generateRecurringForm">
                            <div class="mb-3">
                                <label class="form-label"><?= lang('Finance.select_schedule') ?> <span class="text-danger">*</span></label>
                                <select class="form-select" id="schedule_id" name="schedule_id" required>
                                    <option value=""><?= lang('Finance.select_billing_schedule') ?></option>
                                </select>
                                <small class="text-muted"><?= lang('Finance.active_recurring_schedules') ?></small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><?= lang('Finance.notes_optional') ?></label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                            
                            <div id="schedulePreview" class="alert alert-info d-none">
                                <h6 class="mb-2"><?= lang('Finance.schedule_preview') ?></h6>
                                <div id="schedulePreviewContent"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-primary" onclick="submitGenerateInvoice()">
                    <i class="fas fa-check me-2"></i><?= lang('Finance.generate_invoice') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>



<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<?php
$invoicesPageI18n = [
    'oneTime' => lang('Finance.invoice_type_one_time'),
    'recurring' => lang('Finance.invoice_type_recurring'),
    'statusLabels' => [
        'DRAFT' => lang('Finance.draft'),
        'APPROVED' => lang('Common.approved'),
        'SENT' => lang('Finance.sent'),
        'PAID' => lang('Finance.paid'),
        'OVERDUE' => lang('Finance.overdue'),
        'CANCELLED' => lang('Finance.cancelled'),
    ],
    'view' => lang('Common.view'),
    'approve' => lang('Common.approve'),
    'cancel' => lang('Common.cancel'),
    'markAsPaid' => lang('Finance.mark_as_paid'),
    'clear' => lang('Common.clear'),
    'close' => lang('Common.close'),
    'back' => lang('Common.back'),
    'contract' => lang('Finance.contract'),
    'unit' => lang('Finance.unit'),
    'period' => lang('Finance.period'),
    'daysOverdueTh' => lang('Finance.days_overdue_th'),
    'amount' => lang('Common.amount'),
    'action' => lang('Common.action'),
    'scheduleNextPrefix' => lang('Finance.schedule_next_prefix'),
    'selectDiPlaceholder' => lang('Finance.select_delivery_instruction'),
    'selectSchedulePlaceholder' => lang('Finance.select_billing_schedule'),
    'diColon' => lang('Finance.di_colon'),
    'customerColon' => lang('Finance.customer_colon'),
    'contractColon' => lang('Finance.contract_colon'),
    'estimatedAmountColon' => lang('Finance.estimated_amount_colon'),
    'periodRangeTo' => lang('Finance.period_range_to'),
    'daysCount' => lang('Finance.days_count'),
    'missingInvoicesModalTitle' => lang('Finance.missing_invoices_modal_title'),
    'generate' => lang('Finance.generate'),
    'generateAll' => lang('Finance.generate_all'),
    'generatingBackBilling' => lang('Finance.generating_back_billing'),
    'backBillingComplete' => lang('Finance.back_billing_complete'),
    'backBillingConfirmTitle' => lang('Finance.back_billing_confirm_title'),
    'backBillingConfirmText' => lang('Finance.back_billing_confirm_text'),
    'backBillingConfirmYes' => lang('Finance.back_billing_confirm_yes'),
    'approveInvoiceTitle' => lang('Finance.approve_invoice_title'),
    'approveInvoiceConfirm' => lang('Finance.approve_invoice_confirm'),
    'yesApprove' => lang('Finance.yes_approve'),
    'invoiceApprovedSuccess' => lang('Finance.invoice_approved_success'),
    'approveInvoiceFailed' => lang('Finance.approve_invoice_failed'),
    'paymentDatePrompt' => lang('Finance.payment_date_prompt'),
    'invoiceMarkedPaid' => lang('Finance.invoice_marked_paid'),
    'errorWithMessage' => lang('Finance.error_with_message'),
    'failedGenerateBackBilling' => lang('Finance.failed_generate_back_billing'),
    'cancelInvoiceTitle' => lang('Finance.cancel_invoice_title'),
    'cancelReasonLabel' => lang('Finance.cancel_reason_label'),
    'cancelReasonPlaceholder' => lang('Finance.cancel_reason_placeholder'),
    'yesCancelInvoice' => lang('Finance.yes_cancel_invoice'),
    'cancelReasonRequired' => lang('Finance.cancel_reason_required'),
    'validation' => lang('Finance.validation'),
    'invoiceCancelledSuccess' => lang('Finance.invoice_cancelled_success'),
    'invoiceCreatedWithNumber' => lang('Finance.invoice_created_with_number'),
    'generateInvoiceFailed' => lang('Finance.generate_invoice_failed'),
    'generatedInvoiceCount' => lang('Finance.generated_invoice_count'),
];
?>
<script>
const invI18n = <?= json_encode($invoicesPageI18n, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

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
                    return data === 'ONE_TIME' ? '<span class="badge badge-soft-blue">' + invI18n.oneTime + '</span>' : '<span class="badge badge-soft-cyan">' + invI18n.recurring + '</span>';
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const label = invI18n.statusLabels[data] || data;
                    return `<span class="status-badge status-${data.toLowerCase()}">${label}</span>`;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    let actions = `
                        <div class="action-buttons btn-group btn-group-sm">
                            <a href="<?= base_url('finance/invoices/view/') ?>${row.id}" class="btn btn-sm btn-outline-primary btn-icon-only" title="${invI18n.view}">
                                <i class="fas fa-eye"></i>
                            </a>
                    `;
                    
                    if (row.status === 'DRAFT') {
                        actions += `
                            <button onclick="approveInvoice(${row.id})" class="btn btn-sm btn-success" title="${invI18n.approve}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="cancelInvoice(${row.id})" class="btn btn-sm btn-danger" title="${invI18n.cancel}">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                    }
                    
                    if (row.status === 'APPROVED' || row.status === 'SENT' || row.status === 'OVERDUE') {
                        actions += `
                            <button onclick="markAsPaid(${row.id})" class="btn btn-sm btn-primary" title="${invI18n.markAsPaid}">
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
        locale: { cancelLabel: invI18n.clear, format: 'DD/MM/YYYY' }
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
                        <p><strong>${invI18n.diColon}</strong> ${data.di.nomor_di}</p>
                        <p><strong>${invI18n.customerColon}</strong> ${data.di.customer_name}</p>
                        <p><strong>${invI18n.contractColon}</strong> ${data.di.contract_number}</p>
                        <p><strong>${invI18n.estimatedAmountColon}</strong> Rp ${new Intl.NumberFormat('id-ID').format(data.di.estimated_amount || 0)}</p>
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
            html += '<thead><tr><th>' + invI18n.contract + '</th><th>' + invI18n.unit + '</th><th>' + invI18n.period + '</th><th>' + invI18n.daysOverdueTh + '</th><th>' + invI18n.amount + '</th><th>' + invI18n.action + '</th></tr></thead><tbody>';
            
            data.data.forEach(invoice => {
                const daysLabel = invI18n.daysCount.replace('{count}', String(invoice.days_overdue));
                html += `<tr>
                    <td>${invoice.contract_number}<br><small class="text-muted">${invoice.customer_name}</small></td>
                    <td>${invoice.unit_number}</td>
                    <td>${invoice.period_start} ${invI18n.periodRangeTo} ${invoice.period_end}</td>
                    <td><span class="badge badge-soft-red">${daysLabel}</span></td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(invoice.estimated_amount)}</td>
                    <td><button class="btn btn-sm btn-primary" onclick="generateSingleBackBilling(${invoice.contract_id})">${invI18n.generate}</button></td>
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
                                    <i class="fas fa-exclamation-triangle me-2"></i>${invI18n.missingInvoicesModalTitle}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                ${html}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${invI18n.close}</button>
                               <button type="button" class="btn btn-primary" onclick="autoGenerateBackBilling()">
                                    <i class="fas fa-magic me-2"></i>${invI18n.generateAll}
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
        title: invI18n.backBillingConfirmTitle,
        text: invI18n.backBillingConfirmText,
        icon: 'info',
        confirmText: '<i class="fas fa-magic me-1"></i>' + invI18n.backBillingConfirmYes,
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
                    <i class="fas fa-spinner fa-spin me-2"></i>${invI18n.generatingBackBilling} <span id="progressText">0/${contractIds.length}</span>
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
                alertSwal('success', invI18n.backBillingComplete);
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
                OptimaNotify.success(invI18n.generatedInvoiceCount.replace('{count}', String(data.count)).replace('{amount}', new Intl.NumberFormat('id-ID').format(data.total_amount)));
                loadBackBillingStats();
                invoicesTable.ajax.reload();
            } else {
                OptimaNotify.error(invI18n.errorWithMessage.replace('{message}', data.message || invI18n.failedGenerateBackBilling));
}
        }
        return data;
    });
}

function showGenerateModal() {
    // Load DIs
    fetch('<?= base_url('finance/invoices/get-ready-dis') ?>')
        .then(r => r.json())
        .then(data => {
            const select = $('#di_id');
            select.html('<option value="">' + invI18n.selectDiPlaceholder + '</option>');
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
            select.html('<option value="">' + invI18n.selectSchedulePlaceholder + '</option>');
            if (data.success && data.data) {
                data.data.forEach(s => {
                    select.append(`<option value="${s.id}">${s.contract_number} - ${s.frequency} (${invI18n.scheduleNextPrefix} ${s.next_billing_date})</option>`);
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
            alertSwal('success', invI18n.invoiceCreatedWithNumber.replace('{number}', data.invoice_number));
            bootstrap.Modal.getInstance(document.getElementById('generateInvoiceModal')).hide();
            invoicesTable.ajax.reload();
        } else {
            alertSwal('error', data.message, invI18n.generateInvoiceFailed);
        }
    });
}

function approveInvoice(id) {
    OptimaConfirm.approve({
        title: invI18n.approveInvoiceTitle,
        text: invI18n.approveInvoiceConfirm,
        confirmText: '<i class="fas fa-check me-1"></i>' + invI18n.yesApprove,
        onConfirm: function() {
            fetch(`<?= base_url('finance/invoices/approve/') ?>${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alertSwal('success', invI18n.invoiceApprovedSuccess);
            invoicesTable.ajax.reload();
        } else {
            alertSwal('error', data.message, invI18n.approveInvoiceFailed);
        }
    });
        }
    });
}

function markAsPaid(id) {
    const paymentDate = prompt(invI18n.paymentDatePrompt, new Date().toISOString().split('T')[0]);
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
            OptimaNotify.success(invI18n.invoiceMarkedPaid);
            invoicesTable.ajax.reload();
        } else {
            OptimaNotify.error(invI18n.errorWithMessage.replace('{message}', data.message || ''));
        }
    });
}

function cancelInvoice(id) {
    OptimaConfirm.generic({
        title: invI18n.cancelInvoiceTitle,
        html: `
            <div class="text-start">
                <label class="form-label" for="optimaCancelInvoiceReason">${invI18n.cancelReasonLabel}</label>
                <input id="optimaCancelInvoiceReason" type="text" class="form-control" placeholder="${invI18n.cancelReasonPlaceholder}" autofocus>
            </div>
        `,
        icon: 'warning',
        confirmText: invI18n.yesCancelInvoice,
        cancelText: invI18n.back,
        confirmButtonColor: '#dc3545',
        onConfirm: function() {
            var modal = document.getElementById('optimaConfirmModal');
            var input = modal ? modal.querySelector('#optimaCancelInvoiceReason') : null;
            if (!input) input = document.getElementById('optimaCancelInvoiceReason');
            
            var reason = input && input.value ? input.value.trim() : '';
            if (!reason) {
                OptimaNotify.warning(invI18n.cancelReasonRequired, invI18n.validation);
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
                    OptimaNotify.success(invI18n.invoiceCancelledSuccess);
                    invoicesTable.ajax.reload();
                } else {
                    OptimaNotify.error(invI18n.errorWithMessage.replace('{message}', data.message || ''));
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
