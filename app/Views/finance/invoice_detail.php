<?= $this->extend('layouts/base') ?>

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
                <li class="breadcrumb-item"><a href="<?= base_url('finance/invoices') ?>"><i class="fas fa-file-invoice me-1"></i>Invoices</a></li>
                <li class="breadcrumb-item active"><?= esc($invoice['invoice_number'] ?? 'Detail') ?></li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
            <?= esc($invoice['invoice_number'] ?? 'Invoice Detail') ?>
        </h4>
        <p class="text-muted small mb-0">
            Kontrak: <?= esc($invoice['contract_number'] ?? '-') ?>
            &bull; <span class="badge badge-soft-<?= in_array($invoice['status'] ?? '', ['PAID']) ? 'green' : (in_array($invoice['status'] ?? '', ['OVERDUE']) ? 'red' : (in_array($invoice['status'] ?? '', ['APPROVED','SENT']) ? 'blue' : 'gray')) ?>"><?= esc($invoice['status'] ?? 'DRAFT') ?></span>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('finance/invoices') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>
<div>

    <!-- Invoice Details Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Customer Information</h6>
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
                            <small class="text-muted d-block">Issue Date</small>
                            <strong><?= date('d M Y', strtotime($invoice['issue_date'])) ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Due Date</small>
                            <strong class="<?= strtotime($invoice['due_date']) < time() && $invoice['status'] !== 'PAID' ? 'text-danger' : '' ?>">
                                <?= date('d M Y', strtotime($invoice['due_date'])) ?>
                            </strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Type</small>
                            <span class="badge <?= $invoice['invoice_type'] === 'ONE_TIME' ? 'bg-primary' : 'bg-info' ?>">
                                <?= $invoice['invoice_type'] === 'ONE_TIME' ? 'One-Time' : 'Recurring' ?>
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Total Amount</small>
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
                        <i class="fas fa-list me-2"></i>Invoice Items
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40%">Description</th>
                                    <th width="15%" class="text-center">Qty</th>
                                    <th width="15%" class="text-center">Unit</th>
                                    <th width="15%" class="text-end">Unit Price</th>
                                    <th width="15%" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No items</td>
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
                                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
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
                            <i class="fas fa-sticky-note me-2"></i>Notes
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
                        <i class="fas fa-bolt me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <?php if ($invoice['status'] === 'DRAFT'): ?>
                            <button class="btn btn-success" onclick="approveInvoice(<?= $invoice['id'] ?>)">
                                <i class="fas fa-check me-2"></i>Approve Invoice
                            </button>
                            <button class="btn btn-danger" onclick="cancelInvoice(<?= $invoice['id'] ?>)">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                        <?php endif; ?>
                        
                        <?php if (in_array($invoice['status'], ['APPROVED', 'SENT', 'OVERDUE'])): ?>
                            <button class="btn btn-primary" onclick="markAsPaid(<?= $invoice['id'] ?>)">
                                <i class="fas fa-money-bill me-2"></i>Mark as Paid
                            </button>
                        <?php endif; ?>
                        
                        <button class="btn btn-outline-secondary" onclick="printInvoice()">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                        <button class="btn btn-outline-info" onclick="downloadPDF()">
                            <i class="fas fa-file-pdf me-2"></i>Download PDF
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
                        <i class="fas fa-clock-rotate-left me-2"></i>Status History
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($history)): ?>
                        <p class="text-muted text-center mb-0">No history available</p>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($history as $h): ?>
                                <div class="timeline-item">
                                    <div class="timeline-badge timeline-badge-<?= strtolower($h['status']) ?>"></div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">
                                            <?= esc($h['status']) ?>
                                        </h6>
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('d M Y H:i', strtotime($h['changed_at'])) ?>
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-user me-1"></i>
                                            <?= esc($h['changed_by_name'] ?? 'System') ?>
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

<?= $this->section('scripts') ?>
<script>
function approveInvoice(id) {
    OptimaConfirm.approve({
        title: 'Approve Invoice?',
        text: 'Invoice akan disetujui dan dikirim ke customer.',
        onConfirm: function() {
            fetch(`<?= base_url('finance/invoices/approve/') ?>${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alertSwal('success', 'Invoice berhasil disetujui.').then(() => location.reload());
        } else {
            alertSwal('error', 'Error: ' + data.message);
        }
    });
        }
    });
}

function markAsPaid(id) {
    OptimaConfirm.generic({
        title: 'Tandai Lunas',
        icon: 'question',
        html: '<label class="form-label">Tanggal Pembayaran</label><input id="optimaPaymentDate" type="date" class="form-control" value="' + new Date().toISOString().split('T')[0] + '">',
        confirmText: 'Konfirmasi',
        cancelText: window.lang('cancel'),
        confirmButtonColor: '#0d6efd',
        onConfirm: function() {
            var el = document.getElementById('optimaPaymentDate');
            var paymentDate = el ? (el.value || '').trim() : '';
            if (!paymentDate) {
                OptimaNotify.warning('Tanggal pembayaran harus diisi.', 'Validasi');
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
                    alertSwal('success', 'Invoice telah ditandai lunas.').then(() => location.reload());
                } else {
                    alertSwal('error', 'Error: ' + data.message);
                }
            });
        }
    });
}

function cancelInvoice(id) {
    OptimaConfirm.danger({
        title: 'Batalkan Invoice',
        icon: 'warning',
        text: 'Pastikan alasan pembatalan sudah ditulis.',
        confirmText: 'Ya, Batalkan',
        cancelText: window.lang('back'),
        confirmButtonColor: '#dc3545',
        html: '<div class="text-start"><label class="form-label">Alasan Pembatalan</label><textarea id="optimaCancelInvoiceReason" class="form-control" rows="4" placeholder="Masukkan alasan..."></textarea></div>',
        onConfirm: function() {
            var el = document.getElementById('optimaCancelInvoiceReason');
            var reason = el ? (el.value || '').trim() : '';
            if (!reason) {
                OptimaNotify.warning('Alasan harus diisi.', 'Validasi');
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
                    alertSwal('success', 'Invoice telah dibatalkan.').then(() => location.reload());
                } else {
                    alertSwal('error', 'Error: ' + data.message);
                }
            });
        }
    });
}

function printInvoice() {
    window.print();
}

function downloadPDF() {
    alertSwal('info', 'Fitur PDF generation akan segera hadir.');
}
</script>
<?= $this->endSection() ?>
