<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2 text-gradient fw-bold">
            <?= isset($mode) && $mode === 'history' ? 'Work Order History' : 'Work Orders Management' ?>
        </h1>
        <p class="text-muted mb-0">
            <?= isset($mode) && $mode === 'history' ? 'View completed and closed work orders' : 'Manage maintenance and service requests efficiently' ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-pro" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="fas fa-download me-2"></i>Export
        </button>
        <?php if (!isset($mode) || $mode !== 'history'): ?>
        <button class="btn btn-primary btn-pro" data-bs-toggle="modal" data-bs-target="#addWorkOrderModal">
            <i class="fas fa-plus me-2"></i>New Work Order
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Stats Cards Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="pro-stats-label">Total Work Orders</div>
                        <div class="pro-stats-value" id="totalWorkOrders">0</div>
                        <div class="pro-stats-change text-success">
                            <i class="fas fa-arrow-up me-1"></i>+12% from last month
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="pro-stats-icon bg-primary">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="pro-stats-label">Open</div>
                        <div class="pro-stats-value text-warning" id="openWorkOrders">0</div>
                        <div class="pro-stats-change text-info">
                            <i class="fas fa-clock me-1"></i>Pending action
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="pro-stats-icon bg-warning">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="pro-stats-label">In Progress</div>
                        <div class="pro-stats-value text-info" id="progressWorkOrders">0</div>
                        <div class="pro-stats-change text-success">
                            <i class="fas fa-tools me-1"></i>Being worked on
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="pro-stats-icon bg-info">
                            <i class="fas fa-cog fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="pro-stats-label">Completed</div>
                        <div class="pro-stats-value text-success" id="completedWorkOrders">0</div>
                        <div class="pro-stats-change text-success">
                            <i class="fas fa-check me-1"></i>This month
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="pro-stats-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Data Table Card -->
<div class="pro-card shadow-sm">
    <div class="pro-card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="pro-card-title mb-1">Work Orders List</h5>
                <p class="pro-card-subtitle mb-0">All maintenance and service requests</p>
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
                            <?php if (isset($mode) && $mode === 'history'): ?>
                                <option value="CLOSED">Closed</option>
                            <?php else: ?>
                                <option value="OPEN">Open</option>
                                <option value="KENDALA">Kendala</option>
                                <option value="PENDING">Pending</option>
                                <option value="IN_PROGRESS">In Progress</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-medium">From Date</label>
                        <input type="date" class="form-control form-control-sm" id="dateFrom">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-medium">To Date</label>
                        <input type="date" class="form-control form-control-sm" id="dateTo">
                    </div>
                    <div class="col-md-2">
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
    
    <div class="pro-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="workOrdersTable" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold">WO Number</th>
                        <th class="fw-semibold">Unit</th>
                        <th class="fw-semibold">Description</th>
                        <th class="fw-semibold">Status</th>
                        <th class="fw-semibold">Priority</th>
                        <th class="fw-semibold">Assigned To</th>
                        <th class="fw-semibold">Created</th>
                        <th class="fw-semibold">Due Date</th>
                        <th class="fw-semibold text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Work Order Modal -->
<div class="modal fade" id="addWorkOrderModal" tabindex="-1" role="dialog" aria-labelledby="addWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWorkOrderModalLabel">Add New Work Order</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addWorkOrderForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="woNumber">WO Number</label>
                                <input type="text" class="form-control" id="woNumber" value="Auto-generated" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unitSelect">Unit</label>
                                <select class="form-control" id="unitSelect" name="unit" required>
                                    <option value="" selected disabled>Select Unit</option>
                                    <option value="Forklift-001">Forklift-001</option>
                                    <option value="Forklift-002">Forklift-002</option>
                                    <option value="Forklift-003">Forklift-003</option>
                                    <option value="Forklift-004">Forklift-004</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <select class="form-control" id="priority" name="priority" required>
                                    <option value="" selected disabled>Select Priority</option>
                                    <option value="LOW">Low</option>
                                    <option value="MEDIUM">Medium</option>
                                    <option value="HIGH">High</option>
                                    <option value="CRITICAL">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="assignedTo">Assigned To</label>
                                <select class="form-control" id="assignedTo" name="assigned_to" required>
                                    <option value="" selected disabled>Select Mechanic</option>
                                    <option value="Mekanik A">Mekanik A</option>
                                    <option value="Mekanik B">Mekanik B</option>
                                    <option value="Mekanik C">Mekanik C</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dueDate">Due Date</label>
                                <input type="date" class="form-control" id="dueDate" name="due_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estimatedHours">Estimated Hours</label>
                                <input type="number" class="form-control" id="estimatedHours" name="estimated_hours" min="1" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Work Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Work Order Modal -->
<div class="modal fade" id="editWorkOrderModal" tabindex="-1" role="dialog" aria-labelledby="editWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWorkOrderModalLabel">Edit Work Order</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editWorkOrderForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="editWorkOrderId" name="id">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="woNumberEdit">WO Number</label>
                                <input type="text" class="form-control" id="woNumberEdit" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unitSelectEdit">Unit</label>
                                <select class="form-control" id="unitSelectEdit" name="unit" required>
                                    <option value="Forklift-001">Forklift-001</option>
                                    <option value="Forklift-002">Forklift-002</option>
                                    <option value="Forklift-003">Forklift-003</option>
                                    <option value="Forklift-004">Forklift-004</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priorityEdit">Priority</label>
                                <select class="form-control" id="priorityEdit" name="priority" required>
                                    <option value="LOW">Low</option>
                                    <option value="MEDIUM">Medium</option>
                                    <option value="HIGH">High</option>
                                    <option value="CRITICAL">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="assignedToEdit">Assigned To</label>
                                <select class="form-control" id="assignedToEdit" name="assigned_to" required>
                                    <option value="Mekanik A">Mekanik A</option>
                                    <option value="Mekanik B">Mekanik B</option>
                                    <option value="Mekanik C">Mekanik C</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descriptionEdit">Description</label>
                        <textarea class="form-control" id="descriptionEdit" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dueDateEdit">Due Date</label>
                                <input type="date" class="form-control" id="dueDateEdit" name="due_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estimatedHoursEdit">Estimated Hours</label>
                                <input type="number" class="form-control" id="estimatedHoursEdit" name="estimated_hours" min="1" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Work Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Work Order Modal -->
<div class="modal fade" id="viewWorkOrderModal" tabindex="-1" role="dialog" aria-labelledby="viewWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewWorkOrderModalLabel">Work Order Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewWorkOrderBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Work Orders</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Export Format</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="excel" value="excel" checked>
                        <label class="form-check-label" for="excel">
                            Excel (.xlsx)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="pdf" value="pdf">
                        <label class="form-check-label" for="pdf">
                            PDF (.pdf)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="csv" value="csv">
                        <label class="form-check-label" for="csv">
                            CSV (.csv)
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

<?php
$this->endSection();
?>

<?php $this->section('scripts'); ?>
    <!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Additional Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    // CSRF Token Handling for all AJAX requests
    let csrf_token = '<?= csrf_hash() ?>';
    
    // Current mode (active or history)
    const currentMode = '<?= isset($mode) ? $mode : 'active' ?>';
    
    // Status lists based on mode
    const activeStatuses = ['OPEN', 'KENDALA', 'PENDING'];
    const historyStatuses = ['CLOSED'];
    
    $.ajaxSetup({
        data: {
            '<?= csrf_token() ?>': function() { return csrf_token; }
        },
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success: function(response) {
            if (response && response.token) {
                csrf_token = response.token; 
            }
        }
    });

    var workOrdersTable;

    $(document).ready(function() {
        // Initialize DataTable with Bootstrap Pro styling
        workOrdersTable = $('#workOrdersTable').DataTable({
            "processing": true,
            "serverSide": false, // Change to client-side for now
            "responsive": true,
            "language": {
                "processing": '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                "emptyTable": '<div class="text-center py-4"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No work orders found</h5><p class="text-muted">Create your first work order to get started.</p></div>',
                "zeroRecords": '<div class="text-center py-4"><i class="fas fa-search fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No matching records found</h5><p class="text-muted">Try adjusting your search or filter criteria.</p></div>'
            },
            "ajax": {
                "url": "<?= base_url('api/workorders/list') ?>",
                "type": "POST",
                "data": function(d) {
                    d.status = $('#statusFilter').val();
                    d.dateFrom = $('#dateFrom').val();
                    d.dateTo = $('#dateTo').val();
                    d.mode = currentMode;
                    d['<?= csrf_token() ?>'] = csrf_token;
                },
                "error": function(xhr, status, error) {
                    console.error('DataTable AJAX Error:', xhr.responseText);
                    Swal.fire('Error!', 'Failed to load work orders data.', 'error');
                },
                "dataSrc": function(json) {
                    if(json.token) {
                        csrf_token = json.token;
                    }
                    // Update stats
                    updateStats(json.stats || {});
                    return json.data || [];
                }
            },
            "columns": [
                { 
                    "data": "wo_number",
                    "render": function(data, type, row) {
                        return `<span class="fw-medium text-primary">${data}</span>`;
                    }
                },
                { "data": "unit" },
                { 
                    "data": "description", 
                    "orderable": false,
                    "render": function(data, type, row) {
                        return data.length > 50 ? data.substr(0, 50) + '...' : data;
                    }
                },
                { 
                    "data": "status",
                    "render": function(data, type, row) {
                        const statusClasses = {
                            'OPEN': 'bg-warning text-dark',
                            'KENDALA': 'bg-danger',
                            'PENDING': 'bg-secondary',
                            'IN_PROGRESS': 'bg-info',
                            'COMPLETED': 'bg-success',
                            'CLOSED': 'bg-dark',
                            'CANCELLED': 'bg-danger'
                        };
                        const badgeClass = statusClasses[data] || 'bg-secondary';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    "data": "priority",
                    "render": function(data, type, row) {
                        const priorityClasses = {
                            'LOW': 'bg-light text-dark',
                            'MEDIUM': 'bg-primary',
                            'HIGH': 'bg-warning text-dark',
                            'CRITICAL': 'bg-danger'
                        };
                        const badgeClass = priorityClasses[data] || 'bg-secondary';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { "data": "assigned_to" },
                { 
                    "data": "created_at",
                    "render": function(data, type, row) {
                        return new Date(data).toLocaleDateString();
                    }
                },
                { 
                    "data": "due_date",
                    "render": function(data, type, row) {
                        const dueDate = new Date(data);
                        const today = new Date();
                        const isOverdue = dueDate < today;
                        const dateStr = dueDate.toLocaleDateString();
                        return isOverdue ? `<span class="text-danger fw-medium">${dateStr}</span>` : dateStr;
                    }
                },
                { 
                    "data": null, 
                    "orderable": false, 
                    "searchable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-info" onclick="viewWorkOrder(${row.id})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="editWorkOrder(${row.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteWorkOrder(${row.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "order": [[6, 'desc']],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                   '<"row"<"col-sm-12"tr>>' +
                   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "buttons": [
                {
                    extend: 'excel',
                    className: 'btn btn-success btn-sm',
                    text: '<i class="fas fa-file-excel"></i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    text: '<i class="fas fa-file-pdf"></i> PDF'
                }
            ]
        });

        // Filter handlers
        $('#applyFilter').click(function(){
          workOrdersTable.ajax.reload();
        });
        $('#resetFilter').on('click', function() {
            $('#statusFilter, #dateFrom, #dateTo').val('');
            workOrdersTable.ajax.reload();
        });

        // Add Work Order form submission
        $('#addWorkOrderForm').on('submit', function(e) {
            e.preventDefault();
            if (!this.checkValidity()) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }
            const formData = $(this).serialize();
            $.ajax({
                url: "<?= site_url('api/workorders/create') ?>",
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    handleResponse(response, 'Work order created successfully!');
                    if(response.success) $('#addWorkOrderModal').modal('hide');
                },
                error: () => Swal.fire('Error!', 'Could not connect to the server.', 'error')
            });
        });

        // Edit Work Order form submission
        $('#editWorkOrderForm').on('submit', function(e) {
            e.preventDefault();
            if (!this.checkValidity()) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }
            const formData = $(this).serialize();
            $.ajax({
                url: "<?= base_url('api/workorders/update') ?>",
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    handleResponse(response, 'Work order update successfully!');
                    if(response.success) $('#editWorkOrderModal').modal('hide');
                },
                error: function(a){
                  Swal.fire('Error!', 'Could not connect to the server.', 'error');
                  console.error(a.responseText);
                }
            });
        });

        // Reset forms when modals are hidden
        $('#addWorkOrderModal, #editWorkOrderModal').on('hidden.bs.modal', function() {
            $(this).find('form').removeClass('was-validated')[0].reset();
        });
    });

    // --- Action Functions ---

    function viewWorkOrder(id) {
        $.ajax({
            url: `<?= site_url('api/workorders/get/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const wo = response.data;
                    let detailsHtml = `
                        <p><strong>WO Number:</strong> ${wo.wo_number}</p>
                        <p><strong>Unit:</strong> ${wo.unit}</p>
                        <p><strong>Status:</strong> ${wo.status.replace(/<[^>]*>?/gm, '')}</p>
                        <p><strong>Priority:</strong> ${wo.priority.replace(/<[^>]*>?/gm, '')}</p>
                        <p><strong>Assigned To:</strong> ${wo.assigned_to}</p>
                        <p><strong>Due Date:</strong> ${wo.due_date}</p>
                        <hr>
                        <p><strong>Description:</strong></p>
                        <p>${wo.description}</p>
                    `;
                    $('#viewWorkOrderBody').html(detailsHtml);
                    $('#viewWorkOrderModal').modal('show');
                } else {
                    Swal.fire('Not Found!', response.message, 'error');
                }
            },
            error: () => Swal.fire('Error!', 'Could not retrieve work order details.', 'error')
        });
    }

    function editWorkOrder(id) {
        $.ajax({
            url: `<?= site_url('api/workorders/get/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const wo = response.data;
                    $('#editWorkOrderId').val(wo.wo_id);
                    $('#woNumberEdit').val(wo.wo_number);
                    $('#unitSelectEdit').val(wo.unit);
                    $('#priorityEdit').val(wo.priority);
                    $('#assignedToEdit').val(wo.assigned_to);
                    $('#descriptionEdit').val(wo.description);
                    $('#dueDateEdit').val(wo.due_date);
                    $('#estimatedHoursEdit').val(wo.estimated_hours);
                    $('#editWorkOrderModal').modal('show');
                } else {
                    Swal.fire('Not Found!', response.message, 'error');
                }
            },
            error: () => Swal.fire('Error!', 'Could not retrieve work order data.', 'error')
        });
    }

    function deleteWorkOrder(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= site_url('api/workorders/delete') ?>",
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: (response) => handleResponse(response, 'Work order has been deleted.'),
                    error: () => Swal.fire('Error!', 'Could not connect to the server.', 'error')
                });
            }
        });
    }
    
    // Update stats function
    function updateStats(stats) {
        if (currentMode === 'history') {
            // For history mode, show closed items
            $('#totalWorkOrders').text(stats.total || 0);
            $('#openWorkOrders').text(stats.closed || 0);
            $('#progressWorkOrders').text(stats.last_month || 0);
            $('#completedWorkOrders').text(stats.this_month || 0);
            
            // Update labels for history mode
            $('.pro-stats-label').eq(1).text('Closed');
            $('.pro-stats-label').eq(2).text('Last Month');
            $('.pro-stats-label').eq(3).text('This Month');
        } else {
            // For active mode, show active items
            $('#totalWorkOrders').text(stats.total || 0);
            $('#openWorkOrders').text(stats.open || 0);
            $('#progressWorkOrders').text(stats.kendala || 0);
            $('#completedWorkOrders').text(stats.pending || 0);
            
            // Update labels for active mode
            $('.pro-stats-label').eq(1).text('Open');
            $('.pro-stats-label').eq(2).text('Kendala');
            $('.pro-stats-label').eq(3).text('Pending');
        }
    }
    
    // Enhanced response handler
    function handleResponse(response, successMessage = null) {
        if (response.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: successMessage || response.message,
                timer: 2000,
                showConfirmButton: false
            });
            workOrdersTable.ajax.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: response.message || 'An error occurred'
            });
        }
    }
    
    // Export data function
    function exportData() {
        const format = $('input[name="exportFormat"]:checked').val();
        if (format === 'excel') {
            workOrdersTable.button('.buttons-excel').trigger();
        } else if (format === 'pdf') {
            workOrdersTable.button('.buttons-pdf').trigger();
        } else if (format === 'csv') {
            workOrdersTable.button('.buttons-csv').trigger();
        }
        $('#exportModal').modal('hide');
    }
    
    // Add mock data for testing (remove when backend is ready)
    function loadMockData() {
        const mockData = [
            {
                id: 1,
                wo_number: 'WO-2024-001',
                unit: 'Forklift-001',
                description: 'Routine maintenance and oil change',
                status: 'OPEN',
                priority: 'MEDIUM',
                assigned_to: 'Mekanik A',
                created_at: '2024-01-15',
                due_date: '2024-01-20'
            },
            {
                id: 2,
                wo_number: 'WO-2024-002',
                unit: 'Forklift-002',
                description: 'Brake system repair and inspection',
                status: 'IN_PROGRESS',
                priority: 'HIGH',
                assigned_to: 'Mekanik B',
                created_at: '2024-01-14',
                due_date: '2024-01-18'
            },
            {
                id: 3,
                wo_number: 'WO-2024-003',
                unit: 'Forklift-003',
                description: 'Engine overhaul and parts replacement',
                status: 'COMPLETED',
                priority: 'CRITICAL',
                assigned_to: 'Mekanik C',
                created_at: '2024-01-10',
                due_date: '2024-01-15'
            }
        ];
        
        // Clear and add mock data
        workOrdersTable.clear();
        workOrdersTable.rows.add(mockData);
        workOrdersTable.draw();
        
        // Update stats
        updateStats({
            total: mockData.length,
            open: mockData.filter(item => item.status === 'OPEN').length,
            in_progress: mockData.filter(item => item.status === 'IN_PROGRESS').length,
            completed: mockData.filter(item => item.status === 'COMPLETED').length
        });
    }
    
    // Load mock data if AJAX fails
    setTimeout(function() {
        if (workOrdersTable.data().count() === 0) {
            console.log('Loading mock data for demonstration...');
            loadMockData();
        }
    }, 2000);
</script>
<?php
$this->endSection();
?>

