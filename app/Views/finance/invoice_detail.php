<?= $this->extend('layouts/base') ?>

<?php
$invoiceStatusLabels = [
    'DRAFT' => lang('Finance.draft'),
    'APPROVED' => lang('Common.approved'),
    'SENT' => lang('Finance.sent'),
    'PAID' => lang('Finance.paid'),
    'OVERDUE' => lang('Finance.overdue'),
    'CANCELLED' => lang('Finance.cancelled'),
];
$currentStatus = $invoice['status'] ?? 'DRAFT';
$statusDisplay = $invoiceStatusLabels[$currentStatus] ?? $currentStatus;
?>

<?= $this->section('css') ?>
<style>
.invoice-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px 10px 0 0;
}
.status-badge-lg {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    font-weight: 700;
    border-radius: 8px;
    text-transform: uppercase;
}
.timeline {
    position: relative;
    padding-left: 2rem;
}
.timeline-item {
    position: relative;
    padding-bottom: 2rem;
    border-left: 2px solid #e9ecef;
}
.timeline-item:last-child {
    border-left: 2px solid transparent;
}
.timeline-badge {
    position: absolute;
    left: -9px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: white;
    border: 2px solid;
}
.timeline-badge-draft { border-color: #6c757d; }
.timeline-badge-approved { border-color: #0dcaf0; }
.timeline-badge-sent { border-color: #0d6efd; }
.timeline-badge-paid { border-color: #198754; }
.timeline-badge-overdue { border-color: #fd7e14; }
.timeline-badge-cancelled { border-color: #dc3545; }
.invoice-total {
    font-size: 2rem;
    font-weight: 700;
    color: #667eea;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header with Breadcrumb -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('finance/invoices') ?>"><i class="fas fa-file-invoice me-1"></i><?= lang('Finance.invoices') ?></a></li>
                <li class="breadcrumb-item active"><?= esc($invoice['invoice_number'] ?? lang('Finance.invoice_detail_title')) ?></li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
            <?= esc($invoice['invoice_number'] ?? lang('Finance.invoice_detail_title')) ?>
        </h4>
        <p class="text-muted small mb-0">
            <?= lang('Finance.contract') ?>: <?= esc($invoice['contract_number'] ?? '-') ?>
            &bull; <span class="badge badge-soft-<?= in_array($invoice['status'] ?? '', ['PAID']) ? 'green' : (in_array($invoice['status'] ?? '', ['OVERDUE']) ? 'red' : (in_array($invoice['status'] ?? '', ['APPROVED','SENT']) ? 'blue' : 'gray')) ?>"><?= esc($statusDisplay) ?></span>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('finance/invoices') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
        </a>
    </div>
</div>
<div>

    <!-- Invoice Details Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3"><?= lang('Finance.customer_information') ?></h6>
                    <p class="mb-1">
                        <strong><i class="fas fa-building me-2 text-primary"></i><?= esc($invoice['customer_name'] ?? '-') ?></strong>
                    </p>
                    <p class="mb-1 small text-muted">
                        <i class="fas fa-map-marker-alt me-2"></i><?= esc($invoice['customer_address'] ?? '-') ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block"><?= lang('Finance.issue_date') ?></small>
                            <strong><?= date('d M Y', strtotime($invoice['issue_date'])) ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block"><?= lang('Finance.due_date') ?></small>
                            <strong class="<?= strtotime($invoice['due_date']) < time() && $invoice['status'] !== 'PAID' ? 'text-danger' : '' ?>">
                                <?= date('d M Y', strtotime($invoice['due_date'])) ?>
                            </strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block"><?= lang('Common.type') ?></small>
                            <span class="badge <?= $invoice['invoice_type'] === 'ONE_TIME' ? 'bg-primary' : 'bg-info' ?>">
                                <?= $invoice['invoice_type'] === 'ONE_TIME' ? lang('Finance.invoice_type_one_time') : lang('Finance.invoice_type_recurring') ?>
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block"><?= lang('Finance.total_amount') ?></small>
                            <div class="invoice-total">
                                Rp <?= number_format($invoice['total_amount'] ?? 0, 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Line Items -->
        <div class="col-lg-8">
            <!-- Invoice Items -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i><?= lang('Finance.invoice_items') ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40%"><?= lang('Common.description') ?></th>
                                    <th width="15%" class="text-center"><?= lang('Finance.qty_short') ?></th>
                                    <th width="15%" class="text-center"><?= lang('Finance.unit') ?></th>
                                    <th width="15%" class="text-end"><?= lang('Finance.unit_price') ?></th>
                                    <th width="15%" class="text-end"><?= lang('Finance.subtotal') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted"><?= lang('Finance.no_line_items') ?></td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($item['item_name']) ?></strong>
                                                <?php if (!empty($item['description'])): ?>
                                                    <br><small class="text-muted"><?= esc($item['description']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= number_format($item['quantity'], 2) ?></td>
                                            <td class="text-center"><?= esc($item['unit']) ?></td>
                                            <td class="text-end">Rp <?= number_format($item['unit_price'], 0, ',', '.') ?></td>
                                            <td class="text-end">
                                                <strong>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-light">
                                        <td colspan="4" class="text-end"><strong><?= lang('Common.total') ?>:</strong></td>
                                        <td class="text-end">
                                            <strong class="fs-5 text-primary">
                                                Rp <?= number_format($invoice['total_amount'], 0, ',', '.') ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <?php if (!empty($invoice['notes'])): ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i><?= lang('Common.notes') ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(esc($invoice['notes'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i><?= lang('Common.actions') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <?php if ($invoice['status'] === 'DRAFT'): ?>
                            <button class="btn btn-success" onclick="approveInvoice(<?= $invoice['id'] ?>)">
                                <i class="fas fa-check me-2"></i><?= lang('Finance.approve_invoice') ?>
                            </button>
                            <button class="btn btn-danger" onclick="cancelInvoice(<?= $invoice['id'] ?>)">
                                <i class="fas fa-times me-2"></i><?= lang('Common.cancel') ?>
                            </button>
                        <?php endif; ?>
                        
                        <?php if (in_array($invoice['status'], ['APPROVED', 'SENT', 'OVERDUE'])): ?>
                            <button class="btn btn-primary" onclick="markAsPaid(<?= $invoice['id'] ?>)">
                                <i class="fas fa-money-bill me-2"></i><?= lang('Finance.mark_as_paid') ?>
                            </button>
                        <?php endif; ?>
                        
                        <button class="btn btn-outline-secondary" onclick="printInvoice()">
                            <i class="fas fa-print me-2"></i><?= lang('Common.print') ?>
                        </button>
                        <button class="btn btn-outline-info" onclick="downloadPDF()">
                            <i class="fas fa-file-pdf me-2"></i><?= lang('Finance.download_pdf') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Status History -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-clock-rotate-left me-2"></i><?= lang('Finance.status_history') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($history)): ?>
                        <p class="text-muted text-center mb-0"><?= lang('Finance.no_status_history') ?></p>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($history as $h): ?>
                                <?php
                                $hStatus = $h['status'] ?? '';
                                $hStatusLabel = $invoiceStatusLabels[$hStatus] ?? $hStatus;
                                ?>
                                <div class="timeline-item">
                                    <div class="timeline-badge timeline-badge-<?= strtolower($hStatus) ?>"></div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">
                                            <?= esc($hStatusLabel) ?>
                                        </h6>
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('d M Y H:i', strtotime($h['changed_at'])) ?>
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-user me-1"></i>
                                            <?= esc($h['changed_by_name'] ?? lang('Finance.system_user')) ?>
                                        </small>
                                        <?php if (!empty($h['notes'])): ?>
                                            <p class="mb-0 mt-2 small">
                                                <i class="fas fa-comment me-1"></i>
                                                <?= esc($h['notes']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?php
$invDetailJs = [
    'approveTitle' => lang('Finance.approve_invoice_question'),
    'approveText' => lang('Finance.approve_invoice_will_send_customer'),
    'approvedOk' => lang('Finance.invoice_approved_reload'),
    'errorPrefix' => lang('Finance.error_prefix_plain'),
    'markPaidTitle' => lang('Finance.mark_paid_modal_title'),
    'paymentDateLabel' => lang('Finance.payment_date'),
    'confirm' => lang('Common.confirm'),
    'cancel' => lang('Common.cancel'),
    'paymentDateRequired' => lang('Finance.payment_date_required_msg'),
    'validation' => lang('Finance.validation'),
    'markedPaidOk' => lang('Finance.invoice_marked_paid_reload'),
    'cancelTitle' => lang('Finance.cancel_invoice_detail_title'),
    'cancelWarn' => lang('Finance.cancel_invoice_ensure_reason'),
    'yesCancel' => lang('Finance.yes_cancel_short'),
    'back' => lang('Common.back'),
    'cancelReasonLabel' => lang('Finance.cancel_reason_label_form'),
    'cancelReasonPh' => lang('Finance.cancel_reason_placeholder'),
    'reasonRequired' => lang('Finance.cancel_reason_required'),
    'cancelledOk' => lang('Finance.invoice_cancelled_success'),
    'pdfSoon' => lang('Finance.pdf_export_coming_soon'),
];
?>
<?= $this->section('javascript') ?>
<script>
const invDetailI18n = <?= json_encode($invDetailJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

function approveInvoice(id) {
    OptimaConfirm.approve({
        title: invDetailI18n.approveTitle,
        text: invDetailI18n.approveText,
        onConfirm: function() {
            fetch(`<?= base_url('finance/invoices/approve/') ?>${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alertSwal('success', invDetailI18n.approvedOk).then(() => location.reload());
        } else {
            alertSwal('error', invDetailI18n.errorPrefix + ' ' + (data.message || ''));
        }
    });
        }
    });
}

function markAsPaid(id) {
    OptimaConfirm.generic({
        title: invDetailI18n.markPaidTitle,
        icon: 'question',
        html: '<label class="form-label">' + invDetailI18n.paymentDateLabel + '</label><input id="optimaPaymentDate" type="date" class="form-control" value="' + new Date().toISOString().split('T')[0] + '">',
        confirmText: invDetailI18n.confirm,
        cancelText: invDetailI18n.cancel,
        confirmButtonColor: '#0d6efd',
        onConfirm: function() {
            var el = document.getElementById('optimaPaymentDate');
            var paymentDate = el ? (el.value || '').trim() : '';
            if (!paymentDate) {
                OptimaNotify.warning(invDetailI18n.paymentDateRequired, invDetailI18n.validation);
                return;
            }

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
                    alertSwal('success', invDetailI18n.markedPaidOk).then(() => location.reload());
                } else {
                    alertSwal('error', invDetailI18n.errorPrefix + ' ' + (data.message || ''));
                }
            });
        }
    });
}

function cancelInvoice(id) {
    OptimaConfirm.danger({
        title: invDetailI18n.cancelTitle,
        icon: 'warning',
        text: invDetailI18n.cancelWarn,
        confirmText: invDetailI18n.yesCancel,
        cancelText: invDetailI18n.back,
        confirmButtonColor: '#dc3545',
        html: '<div class="text-start"><label class="form-label">' + invDetailI18n.cancelReasonLabel + '</label><textarea id="optimaCancelInvoiceReason" class="form-control" rows="4" placeholder="' + invDetailI18n.cancelReasonPh.replace(/"/g, '&quot;') + '"></textarea></div>',
        onConfirm: function() {
            var el = document.getElementById('optimaCancelInvoiceReason');
            var reason = el ? (el.value || '').trim() : '';
            if (!reason) {
                OptimaNotify.warning(invDetailI18n.reasonRequired, invDetailI18n.validation);
                return;
            }

            const formData = new FormData();
            formData.append('reason', reason);

            fetch(`<?= base_url('finance/invoices/cancel/') ?>${id}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alertSwal('success', invDetailI18n.cancelledOk).then(() => location.reload());
                } else {
                    alertSwal('error', invDetailI18n.errorPrefix + ' ' + (data.message || ''));
                }
            });
        }
    });
}

function printInvoice() {
    window.print();
}

function downloadPDF() {
    alertSwal('info', invDetailI18n.pdfSoon);
}
</script>
<?= $this->endSection() ?>
