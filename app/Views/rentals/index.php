<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 text-gray-800 mb-0">Rental Management</h1>
            <p class="text-muted">Manage forklift rentals and bookings</p>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('rentals/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Rental
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Rentals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRentals">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Rentals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeRentals">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingPayments">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Monthly Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthlyRevenue">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts for Expiring Rentals -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle"></i> Expiring Rentals (Next 7 Days)
                    </h6>
                </div>
                <div class="card-body">
                    <div id="expiringRentals" class="alert alert-info" role="alert">
                        <i class="fas fa-spinner fa-spin"></i> Loading expiring rentals...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rental Management Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">Rental List</h6>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="exportData('csv')">
                            <i class="fas fa-file-csv"></i> CSV
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="exportData('pdf')">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filter Controls -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Contract Status</label>
                    <select class="form-select" id="contractFilter">
                        <option value="">All Contract Status</option>
                        <option value="pending">Pending</option>
                        <option value="signed">Signed</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment Status</label>
                    <select class="form-select" id="paymentFilter">
                        <option value="">All Payment Status</option>
                        <option value="pending">Pending</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rental Type</label>
                    <select class="form-select" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
            </div>

            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="rentalsTable" role="table" aria-label="Daftar rental forklift">
                    <thead class="table-light">
                        <tr role="row">
                            <th scope="col" role="columnheader">Rental Number</th>
                            <th scope="col" role="columnheader">Customer</th>
                            <th scope="col" role="columnheader">Forklift</th>
                            <th scope="col" role="columnheader">Rental Period</th>
                            <th scope="col" role="columnheader">Amount</th>
                            <th scope="col" role="columnheader">Status</th>
                            <th scope="col" role="columnheader">Contract</th>
                            <th scope="col" role="columnheader">Payment</th>
                            <th scope="col" role="columnheader">Created</th>
                            <th scope="col" role="columnheader">Actions</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        <tr role="row">
                            <td colspan="10" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Rental Detail Modal -->
<div class="modal fade" id="rentalDetailModal" tabindex="-1" role="dialog" aria-labelledby="rentalDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rentalDetailModalLabel">
                    <i class="fas fa-handshake me-2" aria-hidden="true"></i> Rental Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="rentalDetailContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" role="dialog" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusUpdateModalLabel">
                    <i class="fas fa-edit me-2" aria-hidden="true"></i> Update Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <input type="hidden" id="statusRentalId" name="rental_id">
                    <div class="mb-3">
                        <label class="form-label" for="statusSelect">Status</label>
                        <select class="form-select" id="statusSelect" name="status" required aria-describedby="statusHelp">
                            <option value="">Select Status</option>
                            <option value="draft">Draft</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <div id="statusHelp" class="form-text">Pilih status untuk rental ini</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="statusNotes">Notes</label>
                        <textarea class="form-control" id="statusNotes" name="notes" rows="3" placeholder="Add notes about this status change..." aria-describedby="notesHelp"></textarea>
                        <div id="notesHelp" class="form-text">Catatan opsional untuk perubahan status</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cancel">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateStatus()" aria-label="Update Status">Update Status</button>
            </div>
        </div>
    </div>
</div>

<script>
let rentalTable;

$(document).ready(function() {
    // Initialize DataTable
    initializeTable();
    
    // Load statistics
    loadStatistics();
    
    // Load expiring rentals
    loadExpiringRentals();
    
    // Filter change handlers
    $('#statusFilter, #contractFilter, #paymentFilter, #typeFilter').on('change', function() {
        rentalTable.draw();
    });
});

function initializeTable() {
    rentalTable = $('#rentalsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('rentals/list') ?>',
            type: 'POST',
            data: function(d) {
                d.status_filter = $('#statusFilter').val();
                d.contract_filter = $('#contractFilter').val();
                d.payment_filter = $('#paymentFilter').val();
                d.type_filter = $('#typeFilter').val();
            }
        },
        columns: [
            { data: 'rental_number', name: 'rental_number' },
            { data: 'customer_info', name: 'customer_name' },
            { data: 'forklift_info', name: 'unit_code' },
            { data: 'rental_period', name: 'start_date' },
            { data: 'amount', name: 'final_amount' },
            { data: 'status', name: 'status' },
            { data: 'contract_status', name: 'contract_status' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[8, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: '<div class="text-center"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><br>No rentals found</div>',
            zeroRecords: '<div class="text-center"><i class="fas fa-search fa-3x text-muted mb-3"></i><br>No matching rentals found</div>'
        }
    });
}

function loadStatistics() {
    $.ajax({
        url: '<?= base_url('rentals/stats') ?>',
        type: 'POST',
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                $('#totalRentals').text(stats.total_rentals);
                $('#activeRentals').text(stats.active_rentals);
                $('#pendingPayments').text(stats.pending_payments);
                $('#monthlyRevenue').text('Rp ' + formatNumber(stats.monthly_revenue));
            }
        },
        error: function() {
            console.error('Failed to load statistics');
        }
    });
}

function loadExpiringRentals() {
    $.ajax({
        url: '<?= base_url('rentals/expiring') ?>',
        type: 'POST',
        success: function(response) {
            if (response.success) {
                const rentals = response.data;
                if (rentals.length > 0) {
                    let html = '<h6 class="text-warning mb-2">Rentals expiring soon:</h6>';
                    rentals.forEach(rental => {
                        html += `<div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>${rental.rental_number}</strong> - ${rental.customer_name}
                                <br><small class="text-muted">${rental.unit_code} expires on ${rental.end_date}</small>
                            </div>
                            <a href="<?= base_url('rentals/edit/') ?>${rental.rental_id}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-edit"></i> Review
                            </a>
                        </div>`;
                    });
                    $('#expiringRentals').html(html);
                } else {
                    $('#expiringRentals').html('<div class="text-center text-muted"><i class="fas fa-check-circle"></i> No rentals expiring soon</div>');
                }
            }
        },
        error: function() {
            $('#expiringRentals').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Failed to load expiring rentals</div>');
        }
    });
}

function refreshTable() {
    rentalTable.ajax.reload(null, false);
    loadStatistics();
    loadExpiringRentals();
}

function viewRental(id) {
    $('#rentalDetailModal').modal('show');
    $('#rentalDetailContent').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
    
    $.ajax({
        url: '<?= base_url('rentals/') ?>' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const rental = response.data;
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Rental Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Rental Number:</strong></td><td>${rental.rental_number}</td></tr>
                                <tr><td><strong>Start Date:</strong></td><td>${rental.start_date}</td></tr>
                                <tr><td><strong>End Date:</strong></td><td>${rental.end_date}</td></tr>
                                <tr><td><strong>Duration:</strong></td><td>${rental.rental_duration} ${rental.rental_type}</td></tr>
                                <tr><td><strong>Rate:</strong></td><td>Rp ${formatNumber(rental.rental_rate)} / ${rental.rental_rate_type}</td></tr>
                                <tr><td><strong>Final Amount:</strong></td><td><strong>Rp ${formatNumber(rental.final_amount)}</strong></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Customer Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Name:</strong></td><td>${rental.customer_name}</td></tr>
                                <tr><td><strong>Company:</strong></td><td>${rental.customer_company}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${rental.customer_email}</td></tr>
                                <tr><td><strong>Phone:</strong></td><td>${rental.customer_phone}</td></tr>
                                <tr><td><strong>Contact Person:</strong></td><td>${rental.contact_person || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Forklift Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Unit Code:</strong></td><td>${rental.unit_code}</td></tr>
                                <tr><td><strong>Unit Name:</strong></td><td>${rental.unit_name}</td></tr>
                                <tr><td><strong>Brand:</strong></td><td>${rental.brand}</td></tr>
                                <tr><td><strong>Model:</strong></td><td>${rental.model}</td></tr>
                                <tr><td><strong>Capacity:</strong></td><td>${rental.capacity}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Status Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Status:</strong></td><td>${getStatusBadge(rental.status)}</td></tr>
                                <tr><td><strong>Contract:</strong></td><td>${getContractBadge(rental.contract_status)}</td></tr>
                                <tr><td><strong>Payment:</strong></td><td>${getPaymentBadge(rental.payment_status)}</td></tr>
                                <tr><td><strong>Created:</strong></td><td>${rental.created_at}</td></tr>
                                <tr><td><strong>Created By:</strong></td><td>${rental.created_by_name || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                `;
                
                if (rental.notes) {
                    html += `
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Notes</h6>
                                <p class="text-muted">${rental.notes}</p>
                            </div>
                        </div>
                    `;
                }
                
                $('#rentalDetailContent').html(html);
            }
        },
        error: function() {
            $('#rentalDetailContent').html('<div class="alert alert-danger">Failed to load rental details</div>');
        }
    });
}

function editRental(id) {
    window.location.href = '<?= base_url('rentals/edit/') ?>' + id;
}

function deleteRental(id) {
    if (confirm('Are you sure you want to delete this rental?')) {
        $.ajax({
            url: '<?= base_url('rentals/delete/') ?>' + id,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    rentalTable.ajax.reload(null, false);
                    loadStatistics();
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                showNotification('error', 'An error occurred while deleting the rental');
            }
        });
    }
}

function updateStatusModal(id) {
    $('#statusRentalId').val(id);
    $('#statusUpdateModal').modal('show');
}

function updateStatus() {
    const rentalId = $('#statusRentalId').val();
    const status = $('#statusSelect').val();
    
    if (!status) {
        showNotification('Please select a status', 'warning');
        return;
    }
    
    $.ajax({
        url: '<?= base_url('rentals/update-status/') ?>' + rentalId,
        type: 'POST',
        data: {
            status: status,
            notes: $('#statusNotes').val()
        },
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);
                $('#statusUpdateModal').modal('hide');
                rentalTable.ajax.reload(null, false);
                loadStatistics();
            } else {
                showNotification('error', response.message);
            }
        },
        error: function() {
            showNotification('error', 'An error occurred while updating the status');
        }
    });
}

function exportData(format) {
    const filters = {
        status: $('#statusFilter').val(),
        contract: $('#contractFilter').val(),
        payment: $('#paymentFilter').val(),
        type: $('#typeFilter').val()
    };
    
    const queryString = new URLSearchParams(filters).toString();
    window.open('<?= base_url('rentals/export/') ?>' + format + '?' + queryString, '_blank');
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function getStatusBadge(status) {
    const badges = {
        'draft': '<span class="badge bg-secondary">Draft</span>',
        'confirmed': '<span class="badge bg-primary">Confirmed</span>',
        'active': '<span class="badge bg-success">Active</span>',
        'completed': '<span class="badge bg-info">Completed</span>',
        'cancelled': '<span class="badge bg-danger">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function getContractBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'signed': '<span class="badge bg-success">Signed</span>',
        'expired': '<span class="badge bg-danger">Expired</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function getPaymentBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'partial': '<span class="badge bg-info">Partial</span>',
        'paid': '<span class="badge bg-success">Paid</span>',
        'overdue': '<span class="badge bg-danger">Overdue</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').prepend(alertHtml);
    
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
<?= $this->endSection() ?> 