<?= $this->extend('layouts/base') ?>

<?php
/**
 * Operators (Operator Management) - Marketing
 * BADGE/CARD: Optima badge-soft-* if used; card-header bg-light, table mb-0.
 */
// Load global permission helper
helper('global_permission');

// Get permissions for marketing module
$permissions = get_global_permission('marketing');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-person-badge stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-operators"><?= $totalOperators ?></div>
                    <div class="text-muted">Total Operators</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-check-circle stat-icon text-success"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-available"><?= $availableOperators ?></div>
                    <div class="text-muted">Available</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-person-check stat-icon text-warning"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-assigned"><?= $assignedOperators ?></div>
                    <div class="text-muted">Assigned</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-danger-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-exclamation-triangle stat-icon text-danger"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-expiring">0</div>
                    <div class="text-muted">Cert. Expiring Soon</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Operators Table Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-person-badge me-2 text-primary"></i>
                Operator Management
            </h5>
            <p class="text-muted small mb-0">Manage operator/driver master data and certifications</p>
        </div>
        <div class="d-flex gap-2">
            <?= ui_button('refresh', 'Refresh', [
                'onclick' => 'refreshTable()',
                'size' => 'sm',
                'color' => 'outline-secondary'
            ]) ?>
            
            <?php if ($can_create): ?>
            <?= ui_button('add', 'Add Operator', [
                'onclick' => 'openAddModal()',
                'size' => 'sm'
            ]) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="operatorsTable" class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Certification</th>
                        <th>Monthly Rate</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data loaded via DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Operator Modal -->
<div class="modal fade" id="operatorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i>
                    <span id="modalTitle">Add Operator</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="operatorForm">
                <input type="hidden" id="operatorId" name="operator_id">
                <div class="modal-body">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs mb-3" id="operatorFormTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button">
                                <i class="fas fa-user me-1"></i>Basic Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cert-tab" data-bs-toggle="tab" data-bs-target="#certification-info" type="button">
                                <i class="fas fa-certificate me-1"></i>Certification
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rate-tab" data-bs-toggle="tab" data-bs-target="#rate-info" type="button">
                                <i class="fas fa-dollar-sign me-1"></i>Rates
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-info" type="button">
                                <i class="fas fa-phone me-1"></i>Contact
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="operatorFormTabContent">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="operator_name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="operator_name" name="operator_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nik" class="form-label">NIK/ID Number</label>
                                        <input type="text" class="form-control" id="nik" name="nik">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                            </div>
                            <div class="mb-3" id="statusGroup" style="display:none;">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="AVAILABLE">Available</option>
                                    <option value="ASSIGNED">Assigned</option>
                                    <option value="ON_LEAVE">On Leave</option>
                                    <option value="INACTIVE">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Certification Tab -->
                        <div class="tab-pane fade" id="certification-info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="certification_level" class="form-label">Certification Level <span class="text-danger">*</span></label>
                                        <select class="form-select" id="certification_level" name="certification_level" required>
                                            <option value="">Select Level</option>
                                            <option value="BASIC">Basic</option>
                                            <option value="INTERMEDIATE">Intermediate</option>
                                            <option value="ADVANCED">Advanced</option>
                                            <option value="EXPERT">Expert</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="certification_number" class="form-label">Certificate Number</label>
                                        <input type="text" class="form-control" id="certification_number" name="certification_number">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="certification_issued_date" class="form-label">Issue Date</label>
                                        <input type="date" class="form-control" id="certification_issued_date" name="certification_issued_date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="certification_expiry" class="form-label">Expiry Date</label>
                                        <input type="date" class="form-control" id="certification_expiry" name="certification_expiry">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="certification_issuer" class="form-label">Issuing Organization</label>
                                <input type="text" class="form-control" id="certification_issuer" name="certification_issuer" placeholder="e.g., Kemnaker RI">
                            </div>
                        </div>

                        <!-- Rates Tab -->
                        <div class="tab-pane fade" id="rate-info" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Set operator service rates for contract quotations
                            </div>
                            <div class="mb-3">
                                <label for="monthly_rate" class="form-label">Monthly Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="monthly_rate" name="monthly_rate" 
                                           required min="0" step="1000" placeholder="8000000">
                                </div>
                                <small class="text-muted">Standard monthly package rate</small>
                            </div>
                            <div class="mb-3">
                                <label for="daily_rate" class="form-label">Daily Rate</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="daily_rate" name="daily_rate" 
                                           min="0" step="1000" placeholder="350000">
                                </div>
                                <small class="text-muted">Per-day rate for short-term assignments</small>
                            </div>
                            <div class="mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" 
                                           min="0" step="1000" placeholder="50000">
                                </div>
                                <small class="text-muted">Per-hour rate for overtime or spot work</small>
                            </div>
                        </div>

                        <!-- Contact Tab -->
                        <div class="tab-pane fade" id="contact-info" role="tabpanel">
                            <h6 class="mb-3">Emergency Contact</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emergency_contact" class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" id="emergency_contact" name="emergency_contact">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emergency_phone" class="form-label">Contact Phone</label>
                                        <input type="text" class="form-control" id="emergency_phone" name="emergency_phone">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Additional notes about operator skills, experience, etc."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Operator Detail Modal -->
<div class="modal fade" id="viewOperatorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i>
                    <span id="viewOperatorName">Operator Details</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Code</th>
                                <td id="view_operator_code"></td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td id="view_operator_name"></td>
                            </tr>
                            <tr>
                                <th>NIK</th>
                                <td id="view_nik"></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td id="view_phone"></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td id="view_email"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="view_status"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Certification</th>
                                <td id="view_certification_level"></td>
                            </tr>
                            <tr>
                                <th>Cert. Number</th>
                                <td id="view_certification_number"></td>
                            </tr>
                            <tr>
                                <th>Expiry Date</th>
                                <td id="view_certification_expiry"></td>
                            </tr>
                            <tr>
                                <th>Monthly Rate</th>
                                <td id="view_monthly_rate"></td>
                            </tr>
                            <tr>
                                <th>Daily Rate</th>
                                <td id="view_daily_rate"></td>
                            </tr>
                            <tr>
                                <th>Hourly Rate</th>
                                <td id="view_hourly_rate"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Emergency Contact</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="20%">Name</th>
                                <td id="view_emergency_contact"></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td id="view_emergency_phone"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row" id="notesSection" style="display:none;">
                    <div class="col-12">
                        <h6>Notes</h6>
                        <p id="view_notes" class="text-muted"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <?php if ($can_edit): ?>
                <button type="button" class="btn btn-primary" onclick="editFromView()">
                    <i class="fas fa-edit me-1"></i>Edit
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let operatorsTable;
let currentOperatorId = null;
let isEditMode = false;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();
    
    // Load statistics
    loadStatistics();
    
    // Form submit handler
    $('#operatorForm').on('submit', function(e) {
        e.preventDefault();
        saveOperator();
    });
});

function initializeDataTable() {
    operatorsTable = $('#operatorsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('marketing/operators/getOperators') ?>',
            type: 'POST',
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error, thrown);
                showNotification('Error loading data: ' + error, 'error');
            }
        },
        columns: [
            { data: 'operator_code' },
            { data: 'operator_name' },
            { 
                data: 'cert_badge',
                orderable: false,
                searchable: false
            },
            { data: 'monthly_rate_formatted' },
            { 
                data: 'status_badge',
                orderable: false,
                searchable: false
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            },
            { 
                data: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[0, 'asc']],
        pageLength: 10,
        language: {
            processing: "Loading...",
            emptyTable: "No operators found",
            zeroRecords: "No matching operators found"
        }
    });
    
    // Event delegation for action buttons
    $('#operatorsTable').on('click', '.btn-view', function() {
        const id = $(this).data('id');
        viewOperator(id);
    });
    
    $('#operatorsTable').on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        editOperator(id);
    });
    
    $('#operatorsTable').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        deleteOperator(id);
    });
}

function openAddModal() {
    isEditMode = false;
    currentOperatorId = null;
    
    $('#modalTitle').text('Add New Operator');
    $('#operatorForm')[0].reset();
    $('#operatorId').val('');
    $('#statusGroup').hide();
    
    // Switch to first tab
    $('#basic-tab').tab('show');
    
    $('#operatorModal').modal('show');
}

function viewOperator(id) {
    $.ajax({
        url: `<?= base_url('marketing/operators/getOperator') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const op = response.data;
                currentOperatorId = op.id;
                
                $('#viewOperatorName').text(op.operator_name);
                $('#view_operator_code').text(op.operator_code);
                $('#view_operator_name').text(op.operator_name);
                $('#view_nik').text(op.nik || '-');
                $('#view_phone').text(op.phone || '-');
                $('#view_email').text(op.email || '-');
                $('#view_status').html(getStatusBadge(op.status));
                
                $('#view_certification_level').html(getCertBadge(op.certification_level));
                $('#view_certification_number').text(op.certification_number || '-');
                $('#view_certification_expiry').text(op.certification_expiry || '-');
                
                $('#view_monthly_rate').text('Rp ' + formatNumber(op.monthly_rate));
                $('#view_daily_rate').text('Rp ' + formatNumber(op.daily_rate));
                $('#view_hourly_rate').text('Rp ' + formatNumber(op.hourly_rate));
                
                $('#view_emergency_contact').text(op.emergency_contact || '-');
                $('#view_emergency_phone').text(op.emergency_phone || '-');
                
                if (op.notes) {
                    $('#view_notes').text(op.notes);
                    $('#notesSection').show();
                } else {
                    $('#notesSection').hide();
                }
                
                $('#viewOperatorModal').modal('show');
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr) {
            showNotification('Error loading operator details', 'error');
        }
    });
}

function editOperator(id) {
    $.ajax({
        url: `<?= base_url('marketing/operators/getOperator') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const op = response.data;
                
                isEditMode = true;
                currentOperatorId = op.id;
                
                $('#modalTitle').text('Edit Operator');
                $('#operatorId').val(op.id);
                $('#operator_name').val(op.operator_name);
                $('#nik').val(op.nik);
                $('#phone').val(op.phone);
                $('#email').val(op.email);
                $('#address').val(op.address);
                $('#certification_level').val(op.certification_level);
                $('#certification_number').val(op.certification_number);
                $('#certification_issued_date').val(op.certification_issued_date);
                $('#certification_expiry').val(op.certification_expiry);
                $('#certification_issuer').val(op.certification_issuer);
                $('#monthly_rate').val(op.monthly_rate);
                $('#daily_rate').val(op.daily_rate);
                $('#hourly_rate').val(op.hourly_rate);
                $('#emergency_contact').val(op.emergency_contact);
                $('#emergency_phone').val(op.emergency_phone);
                $('#notes').val(op.notes);
                $('#status').val(op.status);
                
                $('#statusGroup').show();
                
                // Switch to first tab
                $('#basic-tab').tab('show');
                
                $('#operatorModal').modal('show');
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr) {
            showNotification('Error loading operator data', 'error');
        }
    });
}

function editFromView() {
    $('#viewOperatorModal').modal('hide');
    setTimeout(() => {
        editOperator(currentOperatorId);
    }, 300);
}

function saveOperator() {
    const formData = $('#operatorForm').serialize();
    const url = isEditMode 
        ? `<?= base_url('marketing/operators/update') ?>/${currentOperatorId}`
        : '<?= base_url('marketing/operators/create') ?>';
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#operatorModal').modal('hide');
                operatorsTable.ajax.reload();
                loadStatistics();
            } else {
                let errorMsg = response.message;
                if (response.errors) {
                    errorMsg += '<br>' + Object.values(response.errors).join('<br>');
                }
                showNotification(errorMsg, 'error');
            }
        },
        error: function(xhr) {
            showNotification('Error saving operator', 'error');
        }
    });
}

function deleteOperator(id) {
    Swal.fire({
        title: 'Delete Operator?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?= base_url('marketing/operators/delete') ?>/${id}`,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        operatorsTable.ajax.reload();
                        loadStatistics();
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function(xhr) {
                    showNotification('Error deleting operator', 'error');
                }
            });
        }
    });
}

function loadStatistics() {
    $.ajax({
        url: '<?= base_url('marketing/operators/getStats') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                $('#stat-total-operators').text(stats.total);
                $('#stat-available').text(stats.available);
                $('#stat-assigned').text(stats.assigned);
                $('#stat-expiring').text(stats.expiring_soon);
            }
        }
    });
}

function refreshTable() {
    operatorsTable.ajax.reload();
    loadStatistics();
    showNotification('Data refreshed', 'success');
}

function getStatusBadge(status) {
    const badges = {
        'AVAILABLE': '<span class="badge badge-success">Available</span>',
        'ASSIGNED': '<span class="badge badge-primary">Assigned</span>',
        'ON_LEAVE': '<span class="badge badge-warning">On Leave</span>',
        'INACTIVE': '<span class="badge badge-secondary">Inactive</span>'
    };
    return badges[status] || status;
}

function getCertBadge(level) {
    const badges = {
        'EXPERT': '<span class="badge badge-danger">Expert</span>',
        'ADVANCED': '<span class="badge badge-warning">Advanced</span>',
        'INTERMEDIATE': '<span class="badge badge-info">Intermediate</span>',
        'BASIC': '<span class="badge badge-secondary">Basic</span>'
    };
    return badges[level] || level;
}

function formatNumber(num) {
    return Number(num).toLocaleString('id-ID');
}

function showNotification(message, type) {
    if (window.OptimaNotify && typeof OptimaNotify[type] === 'function') {
        OptimaNotify[type](message);
    } else if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
        OptimaPro.showNotification(message, type);
    } else if (typeof Swal !== 'undefined') {
        Swal.fire({
            text: message,
            icon: type,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    } else {
        alert(message);
    }
}
</script>
<?= $this->endSection() ?>
