<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice me-2"></i>Invoice Management
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshInvoices()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="exportInvoices()">
                    <i class="fas fa-file-excel me-1"></i>Export
                </button>
                <button class="btn btn-primary btn-sm" onclick="createInvoice()">
                    <i class="fas fa-plus me-1"></i>Create Invoice
                </button>
            </div>
        </div>
    </div>

    <!-- Invoice Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($invoices) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Paid Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($invoices, fn($inv) => $inv['status'] == 'Paid')) ?>
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
                                Pending Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($invoices, fn($inv) => $inv['status'] == 'Pending')) ?>
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
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($invoices, fn($inv) => $inv['status'] == 'Overdue')) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Invoice List</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow">
                    <a class="dropdown-item" href="#" onclick="filterInvoices('all')">
                        <i class="fas fa-list me-2"></i>Show All
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterInvoices('paid')">
                        <i class="fas fa-check me-2"></i>Paid Only
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterInvoices('pending')">
                        <i class="fas fa-clock me-2"></i>Pending Only
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterInvoices('overdue')">
                        <i class="fas fa-exclamation-triangle me-2"></i>Overdue Only
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="invoicesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td class="font-weight-bold"><?= esc($invoice['id']) ?></td>
                                <td><?= esc($invoice['customer']) ?></td>
                                <td class="text-right">
                                    Rp <?= number_format($invoice['amount'], 0, ',', '.') ?>
                                </td>
                                <td><?= date('d M Y', strtotime($invoice['due_date'])) ?></td>
                                <td>
                                    <?php
                                    $statusClass = $invoice['status'] == 'Paid' ? 'success' : 
                                                  ($invoice['status'] == 'Pending' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge badge-<?= $statusClass ?>">
                                        <?= $invoice['status'] ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($invoice['created_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewInvoice('<?= $invoice['id'] ?>')" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="editInvoice('<?= $invoice['id'] ?>')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="downloadInvoice('<?= $invoice['id'] ?>')" title="Download">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <?php if ($invoice['status'] != 'Paid'): ?>
                                        <button class="btn btn-sm btn-warning" onclick="markAsPaid('<?= $invoice['id'] ?>')" title="Mark as Paid">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-danger" onclick="deleteInvoice('<?= $invoice['id'] ?>')" title="Delete">
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

<!-- Create Invoice Modal -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Create New Invoice
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createInvoiceForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="invoiceCustomer" class="form-label">Customer *</label>
                                <select class="form-select" id="invoiceCustomer" required>
                                    <option value="">Select Customer</option>
                                    <option value="PT. Maju Jaya">PT. Maju Jaya</option>
                                    <option value="CV. Sukses Mandiri">CV. Sukses Mandiri</option>
                                    <option value="PT. Berkah Selalu">PT. Berkah Selalu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="invoiceAmount" class="form-label">Amount *</label>
                                <input type="number" class="form-control" id="invoiceAmount" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="invoiceDueDate" class="form-label">Due Date *</label>
                                <input type="date" class="form-control" id="invoiceDueDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="invoiceStatus" class="form-label">Status</label>
                                <select class="form-select" id="invoiceStatus">
                                    <option value="Pending">Pending</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="invoiceDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="invoiceDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveInvoice()">
                    <i class="fas fa-save me-2"></i>Create Invoice
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    $('#invoicesTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[5, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    
    // Set default due date to 30 days from now
    const futureDate = new Date();
    futureDate.setDate(futureDate.getDate() + 30);
    $('#invoiceDueDate').val(futureDate.toISOString().split('T')[0]);
});

function createInvoice() {
    $('#createInvoiceModal').modal('show');
}

function saveInvoice() {
    if (!$('#createInvoiceForm')[0].checkValidity()) {
        $('#createInvoiceForm')[0].reportValidity();
        return;
    }

    const formData = {
        customer: $('#invoiceCustomer').val(),
        amount: $('#invoiceAmount').val(),
        due_date: $('#invoiceDueDate').val(),
        status: $('#invoiceStatus').val(),
        description: $('#invoiceDescription').val()
    };

    $.ajax({
        url: '<?= base_url('finance/invoices/create') ?>',
        method: 'POST',
        data: formData,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#createInvoiceModal').modal('hide');
                showNotification('Invoice created successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Failed to create invoice: ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('Error creating invoice', 'error');
        }
    });
}

function viewInvoice(invoiceId) {
    showNotification('Opening invoice ' + invoiceId, 'info');
}

function editInvoice(invoiceId) {
    showNotification('Editing invoice ' + invoiceId, 'info');
}

function downloadInvoice(invoiceId) {
    showNotification('Downloading invoice ' + invoiceId, 'info');
}

function markAsPaid(invoiceId) {
    if (confirm('Mark this invoice as paid?')) {
        $.ajax({
            url: '<?= base_url('finance/payments/update/') ?>' + invoiceId,
            method: 'POST',
            data: { status: 'Paid' },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Invoice marked as paid!', 'success');
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function() {
                showNotification('Error updating invoice status', 'error');
            }
        });
    }
}

function deleteInvoice(invoiceId) {
    if (confirm('Are you sure you want to delete this invoice?')) {
        showNotification('Invoice ' + invoiceId + ' deleted', 'success');
        setTimeout(() => location.reload(), 1500);
    }
}

function refreshInvoices() {
    location.reload();
}

function exportInvoices() {
    $('#invoicesTable').DataTable().button('.buttons-excel').trigger();
}

function filterInvoices(filter) {
    const table = $('#invoicesTable').DataTable();
    
    switch(filter) {
        case 'paid':
            table.column(4).search('Paid').draw();
            break;
        case 'pending':
            table.column(4).search('Pending').draw();
            break;
        case 'overdue':
            table.column(4).search('Overdue').draw();
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