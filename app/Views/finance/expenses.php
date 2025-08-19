<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-receipt me-2"></i>Expense Management
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshExpenses()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="exportExpenses()">
                    <i class="fas fa-file-excel me-1"></i>Export
                </button>
                <button class="btn btn-primary btn-sm" onclick="addExpense()">
                    <i class="fas fa-plus me-1"></i>Add Expense
                </button>
            </div>
        </div>
    </div>

    <!-- Expense Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format(array_sum(array_column($expenses, 'amount')), 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
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
                                This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format(array_sum(array_column(array_filter($expenses, fn($exp) => date('Y-m', strtotime($exp['date'])) == date('Y-m')), 'amount')), 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                                Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_unique(array_column($expenses, 'category'))) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                                Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($expenses, fn($exp) => $exp['status'] == 'Approved')) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Categories Chart -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Expenses by Category</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="expenseCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Expense Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="expenseStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Approved
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Pending
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Rejected
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Expense Records</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow">
                    <a class="dropdown-item" href="#" onclick="filterExpenses('all')">
                        <i class="fas fa-list me-2"></i>Show All
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterExpenses('approved')">
                        <i class="fas fa-check me-2"></i>Approved Only
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterExpenses('pending')">
                        <i class="fas fa-clock me-2"></i>Pending Only
                    </a>
                    <a class="dropdown-item" href="#" onclick="filterExpenses('rejected')">
                        <i class="fas fa-times me-2"></i>Rejected Only
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="expensesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Submitted By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td class="font-weight-bold"><?= esc($expense['id']) ?></td>
                                <td><?= esc($expense['description']) ?></td>
                                <td>
                                    <span class="badge badge-secondary"><?= esc($expense['category']) ?></span>
                                </td>
                                <td class="text-right">
                                    Rp <?= number_format($expense['amount'], 0, ',', '.') ?>
                                </td>
                                <td><?= date('d M Y', strtotime($expense['date'])) ?></td>
                                <td>
                                    <?php
                                    $statusClass = $expense['status'] == 'Approved' ? 'success' : 
                                                  ($expense['status'] == 'Pending' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge badge-<?= $statusClass ?>">
                                        <?= $expense['status'] ?>
                                    </span>
                                </td>
                                <td><?= esc($expense['submitted_by']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewExpense('<?= $expense['id'] ?>')" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="editExpense('<?= $expense['id'] ?>')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($expense['status'] == 'Pending'): ?>
                                        <button class="btn btn-sm btn-success" onclick="approveExpense('<?= $expense['id'] ?>')" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="rejectExpense('<?= $expense['id'] ?>')" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-danger" onclick="deleteExpense('<?= $expense['id'] ?>')" title="Delete">
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

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Expense
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addExpenseForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseDescription" class="form-label">Description *</label>
                                <input type="text" class="form-control" id="expenseDescription" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseCategory" class="form-label">Category *</label>
                                <select class="form-select" id="expenseCategory" required>
                                    <option value="">Select Category</option>
                                    <option value="Fuel">Fuel</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Office Supplies">Office Supplies</option>
                                    <option value="Equipment">Equipment</option>
                                    <option value="Transportation">Transportation</option>
                                    <option value="Utilities">Utilities</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseAmount" class="form-label">Amount *</label>
                                <input type="number" class="form-control" id="expenseAmount" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseDate" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="expenseDate" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseVendor" class="form-label">Vendor/Supplier</label>
                                <input type="text" class="form-control" id="expenseVendor">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseReceiptNumber" class="form-label">Receipt Number</label>
                                <input type="text" class="form-control" id="expenseReceiptNumber">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="expenseNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="expenseNotes" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="expenseReceipt" class="form-label">Receipt Attachment</label>
                        <input type="file" class="form-control" id="expenseReceipt" accept="image/*,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveExpense()">
                    <i class="fas fa-save me-2"></i>Add Expense
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    $('#expensesTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[4, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    
    // Set default date to today
    $('#expenseDate').val(new Date().toISOString().split('T')[0]);
    
    initializeCharts();
});

function initializeCharts() {
    // Category Chart
    const categoryData = <?= json_encode(array_count_values(array_column($expenses, 'category'))) ?>;
    const categoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
    
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(categoryData),
            datasets: [{
                label: 'Expenses',
                data: Object.values(categoryData),
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Status Chart
    const statusData = <?= json_encode(array_count_values(array_column($expenses, 'status'))) ?>;
    const statusCtx = document.getElementById('expenseStatusChart').getContext('2d');
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#f4b619', '#e02d1b'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function addExpense() {
    $('#addExpenseModal').modal('show');
}

function saveExpense() {
    if (!$('#addExpenseForm')[0].checkValidity()) {
        $('#addExpenseForm')[0].reportValidity();
        return;
    }

    const formData = new FormData();
    formData.append('description', $('#expenseDescription').val());
    formData.append('category', $('#expenseCategory').val());
    formData.append('amount', $('#expenseAmount').val());
    formData.append('date', $('#expenseDate').val());
    formData.append('vendor', $('#expenseVendor').val());
    formData.append('receipt_number', $('#expenseReceiptNumber').val());
    formData.append('notes', $('#expenseNotes').val());
    
    const receiptFile = $('#expenseReceipt')[0].files[0];
    if (receiptFile) {
        formData.append('receipt', receiptFile);
    }

    $.ajax({
        url: '<?= base_url('finance/expenses/create') ?>',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#addExpenseModal').modal('hide');
                showNotification('Expense added successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Failed to add expense: ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('Error adding expense', 'error');
        }
    });
}

function viewExpense(expenseId) {
    showNotification('Opening expense details for ' + expenseId, 'info');
}

function editExpense(expenseId) {
    showNotification('Editing expense ' + expenseId, 'info');
}

function approveExpense(expenseId) {
    if (confirm('Approve this expense?')) {
        showNotification('Expense ' + expenseId + ' approved', 'success');
        setTimeout(() => location.reload(), 1500);
    }
}

function rejectExpense(expenseId) {
    if (confirm('Reject this expense?')) {
        showNotification('Expense ' + expenseId + ' rejected', 'warning');
        setTimeout(() => location.reload(), 1500);
    }
}

function deleteExpense(expenseId) {
    if (confirm('Are you sure you want to delete this expense?')) {
        showNotification('Expense ' + expenseId + ' deleted', 'success');
        setTimeout(() => location.reload(), 1500);
    }
}

function refreshExpenses() {
    location.reload();
}

function exportExpenses() {
    $('#expensesTable').DataTable().button('.buttons-excel').trigger();
}

function filterExpenses(filter) {
    const table = $('#expensesTable').DataTable();
    
    switch(filter) {
        case 'approved':
            table.column(5).search('Approved').draw();
            break;
        case 'pending':
            table.column(5).search('Pending').draw();
            break;
        case 'rejected':
            table.column(5).search('Rejected').draw();
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