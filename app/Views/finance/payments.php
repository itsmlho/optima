<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-credit-card me-2"></i>Payment Management
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshPayments()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="exportPayments()">
                    <i class="fas fa-file-excel me-1"></i>Export
                </button>
                <button class="btn btn-primary btn-sm" onclick="recordPayment()">
                    <i class="fas fa-plus me-1"></i>Record Payment
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($payments) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Completed Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($payments, fn($pay) => $pay['status'] == 'Completed')) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($payments, fn($pay) => $pay['status'] == 'Pending')) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format(array_sum(array_column($payments, 'amount')), 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow">
                    <a class="dropdown-item" href="#" onclick="filterPayments('all')">
                        <i class="fas fa-list me-2"></i>Show All
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterPayments('completed')">
                        <i class="fas fa-check me-2"></i>Completed Only
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterPayments('pending')">
                        <i class="fas fa-clock me-2"></i>Pending Only
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterPayments('failed')">
                        <i class="fas fa-times me-2"></i>Failed Only
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="paymentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Invoice ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="font-weight-bold"><?= esc($payment['id']) ?></td>
                                <td><?= esc($payment['invoice_id']) ?></td>
                                <td><?= esc($payment['customer']) ?></td>
                                <td class="text-right">
                                    Rp <?= number_format($payment['amount'], 0, ',', '.') ?>
                                </td>
                                <td><?= esc($payment['method']) ?></td>
                                <td>
                                    <?php
                                    $statusClass = $payment['status'] == 'Completed' ? 'success' : 
                                                  ($payment['status'] == 'Pending' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge badge-<?= $statusClass ?>">
                                        <?= $payment['status'] ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y H:i', strtotime($payment['date'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewPayment('<?= $payment['id'] ?>')" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($payment['status'] == 'Pending'): ?>
                                        <button class="btn btn-sm btn-success" onclick="confirmPayment('<?= $payment['id'] ?>')" title="Confirm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-primary" onclick="printReceipt('<?= $payment['id'] ?>')" title="Print Receipt">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deletePayment('<?= $payment['id'] ?>')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Record New Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="recordPaymentForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paymentInvoice" class="form-label">Invoice ID *</label>
                                <select class="form-select" id="paymentInvoice" required>
                                    <option value="">Select Invoice</option>
                                    <option value="INV-2024-001">INV-2024-001 - PT. Maju Jaya</option>
                                    <option value="INV-2024-002">INV-2024-002 - CV. Sukses Mandiri</option>
                                    <option value="INV-2024-003">INV-2024-003 - PT. Berkah Selalu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paymentAmount" class="form-label">Amount *</label>
                                <input type="number" class="form-control" id="paymentAmount" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label">Payment Method *</label>
                                <select class="form-select" id="paymentMethod" required>
                                    <option value="">Select Method</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Check">Check</option>
                                    <option value="Credit Card">Credit Card</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paymentDate" class="form-label">Payment Date *</label>
                                <input type="datetime-local" class="form-control" id="paymentDate" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paymentStatus" class="form-label">Status</label>
                                <select class="form-select" id="paymentStatus">
                                    <option value="Completed">Completed</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paymentReference" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="paymentReference">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="paymentNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="paymentNotes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePayment()">
                    <i class="fas fa-save me-2"></i>Record Payment
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    $('#paymentsTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[6, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    
    // Set default payment date to now
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    $('#paymentDate').val(now.toISOString().slice(0, 16));
});

function recordPayment() {
    $('#recordPaymentModal').modal('show');
}

function savePayment() {
    if (!$('#recordPaymentForm')[0].checkValidity()) {
        $('#recordPaymentForm')[0].reportValidity();
        return;
    }

    const formData = {
        invoice_id: $('#paymentInvoice').val(),
        amount: $('#paymentAmount').val(),
        method: $('#paymentMethod').val(),
        date: $('#paymentDate').val(),
        status: $('#paymentStatus').val(),
        reference: $('#paymentReference').val(),
        notes: $('#paymentNotes').val()
    };

    $.ajax({
        url: '<?= base_url('finance/payments/create') ?>',
        method: 'POST',
        data: formData,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#recordPaymentModal').modal('hide');
                showNotification('Payment recorded successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Failed to record payment: ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('Error recording payment', 'error');
        }
    });
}

function viewPayment(paymentId) {
    showNotification('Opening payment details for ' + paymentId, 'info');
}

function confirmPayment(paymentId) {
    if (confirm('Confirm this payment?')) {
        $.ajax({
            url: '<?= base_url('finance/payments/update/') ?>' + paymentId,
            method: 'POST',
            data: { status: 'Completed' },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Payment confirmed!', 'success');
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function() {
                showNotification('Error confirming payment', 'error');
            }
        });
    }
}

function printReceipt(paymentId) {
    showNotification('Printing receipt for payment ' + paymentId, 'info');
}

function deletePayment(paymentId) {
    if (confirm('Are you sure you want to delete this payment record?')) {
        showNotification('Payment ' + paymentId + ' deleted', 'success');
        setTimeout(() => location.reload(), 1500);
    }
}

function refreshPayments() {
    location.reload();
}

function exportPayments() {
    $('#paymentsTable').DataTable().button('.buttons-excel').trigger();
}

function filterPayments(filter) {
    const table = $('#paymentsTable').DataTable();
    
    switch(filter) {
        case 'completed':
            table.column(5).search('Completed').draw();
            break;
        case 'pending':
            table.column(5).search('Pending').draw();
            break;
        case 'failed':
            table.column(5).search('Failed').draw();
            break;
        default:
            table.search('').columns().search('').draw();
    }
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container-fluid').prepend(notification);
    
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
<?= $this->endSection() ?> 