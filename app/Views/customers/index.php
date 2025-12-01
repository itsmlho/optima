<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Action Buttons -->
<div class="d-flex justify-content-end gap-2 mb-4">
    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
        <i class="fas fa-download me-2"></i>Export
    </button>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
        <i class="fas fa-plus me-2"></i>Add Customer
    </button>
</div>

<!-- Stats Cards Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-muted small">Total Customers</div>
                        <div class="h4 fw-bold"><?= $stats['total_customers'] ?? 0 ?></div>
                        <div class="small text-success">
                            <i class="fas fa-arrow-up me-1"></i>+8% this month
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-muted small">Active Customers</div>
                        <div class="h4 fw-bold text-success"><?= $stats['active_customers'] ?? 0 ?></div>
                        <div class="small text-info">
                            <i class="fas fa-check-circle me-1"></i>Currently active
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-muted small">New This Month</div>
                        <div class="h4 fw-bold text-info"><?= $stats['new_this_month'] ?? 0 ?></div>
                        <div class="small text-success">
                            <i class="fas fa-user-plus me-1"></i>Recent additions
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-muted small">Total Revenue</div>
                        <div class="h4 fw-bold text-warning">Rp 2.5B</div>
                        <div class="small text-success">
                            <i class="fas fa-chart-line me-1"></i>From customers
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Data Table Card -->
<div class="card shadow-sm">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1">Customers List</h5>
                <p class="text-muted mb-0">All registered customers and their information</p>
            </div>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-filter me-1"></i> Filters
            </button>
        </div>

        <div class="collapse" id="filterCollapse">
            <div class="border-top mt-3 pt-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-medium">Status</label>
                        <select class="form-select form-select-sm" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-medium">Registration Period</label>
                        <select class="form-select form-select-sm" id="periodFilter">
                            <option value="">All Time</option>
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_year">This Year</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" id="applyFilter">
                                <i class="fas fa-search"></i> Apply
                            </button>
                            <button class="btn btn-outline-secondary btn-sm flex-fill" id="resetFilter">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="customersTable">
                <thead class="table-header">
                    <tr>
                        <th class="fw-semibold">Customer</th>
                        <th class="fw-semibold">Company</th>
                        <th class="fw-semibold">Contact</th>
                        <th class="fw-semibold">Total Rentals</th>
                        <th class="fw-semibold">Last Rental</th>
                        <th class="fw-semibold">Status</th>
                        <th class="fw-semibold text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($customers) && is_array($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white me-3">
                                            <?= strtoupper(substr($customer['name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?= htmlspecialchars($customer['name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($customer['email']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($customer['company']) ?></td>
                                <td><?= htmlspecialchars($customer['phone']) ?></td>
                                <td>
                                    <span class="badge bg-info"><?= $customer['total_rentals'] ?> rentals</span>
                                </td>
                                <td><?= date('d M Y', strtotime($customer['last_rental'])) ?></td>
                                <td>
                                    <?php if ($customer['status'] === 'Active'): ?>
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><i class="fas fa-pause-circle me-1"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-info" onclick="viewCustomer(<?= $customer['id'] ?>)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="editCustomer(<?= $customer['id'] ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteCustomer(<?= $customer['id'] ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCustomerForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customerName" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customerName" name="name" required>
                            <div class="invalid-feedback">Please provide a customer name.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="customerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customerEmail" name="email" required>
                            <div class="invalid-feedback">Please provide a valid email.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="customerPhone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="customerPhone" name="phone" required>
                            <div class="invalid-feedback">Please provide a phone number.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="customerCompany" class="form-label">Company</label>
                            <input type="text" class="form-control" id="customerCompany" name="company" required>
                            <div class="invalid-feedback">Please provide a company name.</div>
                        </div>
                        <div class="col-12">
                            <label for="customerAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="customerAddress" name="address" rows="3" required></textarea>
                            <div class="invalid-feedback">Please provide an address.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="taxNumber" class="form-label">Tax Number (Optional)</label>
                            <input type="text" class="form-control" id="taxNumber" name="tax_number">
                        </div>
                        <div class="col-md-6">
                            <label for="customerStatus" class="form-label">Status</label>
                            <select class="form-select" id="customerStatus" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback">Please select a status.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Customer Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Export Format</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="excel" value="excel" checked>
                        <label class="form-check-label" for="excel">
                            <i class="fas fa-file-excel text-success"></i> Excel (.xlsx)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="pdf" value="pdf">
                        <label class="form-check-label" for="pdf">
                            <i class="fas fa-file-pdf text-danger"></i> PDF (.pdf)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="csv" value="csv">
                        <label class="form-check-label" for="csv">
                            <i class="fas fa-file-csv text-info"></i> CSV (.csv)
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="exportData()">Export</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let customersTable;

$(document).ready(function() {
    // Initialize DataTable with Bootstrap Pro styling
    customersTable = $('#customersTable').DataTable({
        "responsive": true,
        "language": {
            "processing": '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
            "emptyTable": '<div class="text-center py-4"><i class="fas fa-users fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No customers found</h5><p class="text-muted">Add your first customer to get started.</p></div>',
            "zeroRecords": '<div class="text-center py-4"><i class="fas fa-search fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No matching customers found</h5><p class="text-muted">Try adjusting your search or filter criteria.</p></div>',
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries"
        },
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, 'asc']],
        "columnDefs": [
            { "targets": [6], "orderable": false, "searchable": false }
        ]
    });

    // Custom filters
    $('#statusFilter').on('change', function() {
        let val = $.fn.dataTable.util.escapeRegex($(this).val());
        customersTable.column(5).search(val ? val : '', true, false).draw();
    });

    $('#applyFilter').on('click', function() {
        customersTable.draw();
    });

    $('#resetFilter').on('click', function() {
        $('#statusFilter, #periodFilter').val('');
        customersTable.search('').columns().search('').draw();
    });

    // Form validation and submission
    $('#addCustomerForm').on('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        $.ajax({
            url: "<?= base_url('customers/store') ?>",
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#addCustomerModal').modal('hide');
                        $('#addCustomerForm').removeClass('was-validated')[0].reset();
                        location.reload(); // Reload to show new customer
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: () => Swal.fire('Error!', 'Could not connect to the server.', 'error')
        });
    });

    // Reset form when modal is hidden
    $('#addCustomerModal').on('hidden.bs.modal', function() {
        $('#addCustomerForm').removeClass('was-validated')[0].reset();
    });
});

// CRUD Functions
function viewCustomer(id) {
    Swal.fire({
        title: 'Customer Details',
        html: `
            <div class="text-start">
                <p><strong>Name:</strong> PT. Customer ${id}</p>
                <p><strong>Email:</strong> customer${id}@example.com</p>
                <p><strong>Phone:</strong> 021-1234567${id}</p>
                <p><strong>Company:</strong> PT. Customer Company ${id}</p>
                <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                <p><strong>Total Rentals:</strong> 15 rentals</p>
                <p><strong>Last Rental:</strong> 2024-01-15</p>
                <p><strong>Registration Date:</strong> 2023-06-15</p>
            </div>
        `,
        confirmButtonText: 'Close',
        showCancelButton: false,
        width: '500px'
    });
}

function editCustomer(id) {
    window.location.href = `<?= base_url('customers/edit/') ?>${id}`;
}

function deleteCustomer(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the customer and all related data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?= base_url('customers/delete/') ?>${id}`,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: () => Swal.fire('Error!', 'Could not connect to the server.', 'error')
            });
        }
    });
}

function exportData() {
    const format = $('input[name="exportFormat"]:checked').val();
    $('#exportModal').modal('hide');
    
    Swal.fire({
        icon: 'success',
        title: 'Export Started',
        text: `Exporting customer data as ${format.toUpperCase()}...`,
        timer: 2000,
        showConfirmButton: false
    });
}
</script>
<?= $this->endSection(); ?> 
