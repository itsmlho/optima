<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .stats-card { 
        transition: transform 0.2s; 
        cursor: pointer;
    }
    .stats-card:hover { 
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .role-icon { 
        width: 40px; 
        height: 40px; 
        border-radius: 50%; 
        background: rgba(0,0,0,0.05); 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }
    .permission-grid { 
        border: 1px solid #dee2e6; 
        border-radius: 0.375rem; 
        padding: 1rem; 
        max-height: 400px; 
        overflow-y: auto; 
        background-color: #f8f9fa;
    }
    .permission-module {
        background: white;
        border-radius: 0.25rem;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        border-left: 3px solid #007bff;
    }
    .permission-grid .form-check {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin-bottom: 0.5rem;
    }
    .permission-grid .form-check:hover { 
        background-color: rgba(0,123,255,0.05); 
    }
    .permission-badge { 
        font-size: 0.75rem; 
    }
    .role-card { 
        border-left: 4px solid #007bff; 
        transition: all 0.3s ease;
    }
    .role-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .role-card.system { 
        border-left-color: #28a745; 
    }
    .role-card.custom { 
        border-left-color: #ffc107; 
    }
    
    /* DataTable customization */
    #rolesTable th,
    #rolesTable td {
        vertical-align: middle;
        padding: 0.75rem 0.5rem;
    }
    
    #rolesTable th:first-child,
    #rolesTable td:first-child {
        width: 40px;
        text-align: center;
    }
    
    .badge-permission {
        font-size: 0.7rem;
        margin: 0.1rem;
    }
    
    .role-actions .btn {
        margin: 0.1rem;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .permission-grid {
            max-height: 300px;
        }
        
        .permission-module .row {
            margin: 0;
        }
        
        .permission-module .col-md-6 {
            padding: 0.25rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-tag me-2 text-primary"></i>Role Management
            </h1>
            <p class="mb-0 text-muted">Create and manage roles with permissions. Users inherit permissions from their assigned roles.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="fas fa-plus me-1"></i>Create Role
            </button>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog me-1"></i>Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportRoles()"><i class="fas fa-download me-2"></i>Export Roles</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/advanced-users') ?>"><i class="fas fa-users me-2"></i>Manage Users</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/permissions') ?>"><i class="fas fa-key me-2"></i>Manage Permissions</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="syncRolePermissions()"><i class="fas fa-sync me-2"></i>Sync Permissions</a></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="cleanUnusedRoles()"><i class="fas fa-trash me-2"></i>Clean Unused Roles</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-1" id="stat-total">
                                <?= $stats['total'] ?? 0 ?>
                            </h4>
                            <p class="card-text mb-0">Total Roles</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tag fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-1" id="stat-active">
                                <?= $stats['active'] ?? 0 ?>
                            </h4>
                            <p class="card-text mb-0">Active Roles</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-1" id="stat-permissions">
                                <?= $stats['permissions'] ?? 0 ?>
                            </h4>
                            <p class="card-text mb-0">Total Permissions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-key fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-1" id="stat-role-permissions">
                                <?= $stats['role_permissions'] ?? 0 ?>
                            </h4>
                            <p class="card-text mb-0">Role Permissions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-link fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card shadow">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>All Roles</h5>
                    <small class="text-muted">Manage all system roles and their permissions</small>
                </div>
                <div class="d-flex gap-2">
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control" id="roleGlobalSearch" placeholder="Search roles...">
                    </div>
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshRoleTable()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Bulk Actions -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button class="btn btn-sm btn-success" onclick="bulkActivateRoles()" disabled id="bulkActivateBtn">
                        <i class="fas fa-check me-1"></i>Activate Selected
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="bulkDeactivateRoles()" disabled id="bulkDeactivateBtn">
                        <i class="fas fa-pause me-1"></i>Deactivate Selected
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="bulkDeleteRoles()" disabled id="bulkDeleteBtn">
                        <i class="fas fa-trash me-1"></i>Delete Selected
                    </button>
                </div>
                <small class="text-muted">
                    <span id="selectedCount">0</span> role(s) selected
                </small>
            </div>
            
            <!-- DataTable -->
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="rolesTable">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="selectAllRoles" class="form-check-input"></th>
                            <th><i class="fas fa-user-tag me-1"></i>Role</th>
                            <th><i class="fas fa-info-circle me-1"></i>Description</th>
                            <th class="text-center"><i class="fas fa-key me-1"></i>Permissions</th>
                            <th class="text-center"><i class="fas fa-users me-1"></i>Users</th>
                            <th class="text-center"><i class="fas fa-toggle-on me-1"></i>Status</th>
                            <th class="text-center"><i class="fas fa-cogs me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat oleh DataTables melalui AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="createRoleForm">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createRoleModalLabel">
                        <i class="fas fa-plus me-2"></i>Create New Role
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Role Management:</strong> Create roles and assign permissions to them. 
                        Users will inherit permissions from their assigned roles.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user-tag me-1"></i>Role Name*
                                </label>
                                <input type="text" class="form-control" name="name" required 
                                       placeholder="e.g. Manager, Supervisor, Administrator">
                                <div class="form-text">Enter a descriptive name for this role</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-cog me-1"></i>Role Type
                                </label>
                                <select class="form-select" name="is_system_role">
                                    <option value="0">Custom Role</option>
                                    <option value="1">System Role</option>
                                </select>
                                <div class="form-text">System roles have special privileges</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control" name="description" rows="3" 
                                  placeholder="Describe what this role can do and its responsibilities..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-key me-1"></i>Assign Permissions to Role
                        </label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Select permissions this role should have</small>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions(true)">
                                    <i class="fas fa-check-square me-1"></i>Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllPermissions(false)">
                                    <i class="fas fa-square me-1"></i>Clear All
                                </button>
                            </div>
                        </div>
                        <div class="permission-grid">
                            <?php if (isset($permissions) && !empty($permissions)): ?>
                                <?php foreach ($permissions as $module => $permissionList): ?>
                                    <div class="permission-module">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="text-capitalize fw-bold mb-0 text-primary">
                                                <i class="fas fa-cube me-1"></i><?= esc($module) ?>
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="toggleModulePermissions('<?= $module ?>')">
                                                <i class="fas fa-toggle-on me-1"></i>
                                                <small>Toggle All</small>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <?php foreach ($permissionList as $permission): ?>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input module-<?= $module ?>" 
                                                               type="checkbox" name="permissions[]" 
                                                               value="<?= $permission['id'] ?>" 
                                                               id="create_perm_<?= $permission['id'] ?>">
                                                        <label class="form-check-label" for="create_perm_<?= $permission['id'] ?>">
                                                            <strong><?= esc($permission['name'] ?? $permission['key']) ?></strong>
                                                            <?php if (!empty($permission['description'])): ?>
                                                                <br><small class="text-muted"><?= esc($permission['description']) ?></small>
                                                            <?php endif; ?>
                                                            <br><code class="small"><?= esc($permission['key']) ?></code>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center p-4">
                                    <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No permissions available</p>
                                    <small class="text-muted">Please create permissions first before creating roles.</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="editRoleForm">
                <input type="hidden" id="editRoleId" name="id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editRoleModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Role
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user-tag me-1"></i>Role Name*
                                </label>
                                <input type="text" class="form-control" id="editRoleName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-toggle-on me-1"></i>Status
                                </label>
                                <select class="form-select" id="editRoleStatus" name="is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control" id="editRoleDescription" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-key me-1"></i>Permissions
                        </label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Select permissions for this role</small>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllEditPermissions(true)">
                                    <i class="fas fa-check-square me-1"></i>Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllEditPermissions(false)">
                                    <i class="fas fa-square me-1"></i>Clear All
                                </button>
                            </div>
                        </div>
                        <div class="permission-grid" id="editPermissionGrid">
                            <?php if (isset($permissions) && !empty($permissions)): ?>
                                <?php foreach ($permissions as $module => $permissionList): ?>
                                    <div class="permission-module">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="text-capitalize fw-bold mb-0 text-primary">
                                                <i class="fas fa-cube me-1"></i><?= esc($module) ?>
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="toggleEditModulePermissions('<?= $module ?>')">
                                                <i class="fas fa-toggle-on me-1"></i>
                                                <small>Toggle All</small>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <?php foreach ($permissionList as $permission): ?>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input edit-module-<?= $module ?>" 
                                                               type="checkbox" name="permissions[]" 
                                                               value="<?= $permission['id'] ?>" 
                                                               id="edit_perm_<?= $permission['id'] ?>">
                                                        <label class="form-check-label" for="edit_perm_<?= $permission['id'] ?>">
                                                            <strong><?= esc($permission['name'] ?? $permission['key']) ?></strong>
                                                            <?php if (!empty($permission['description'])): ?>
                                                                <br><small class="text-muted"><?= esc($permission['description']) ?></small>
                                                            <?php endif; ?>
                                                            <br><code class="small"><?= esc($permission['key']) ?></code>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Role Details Modal -->
<div class="modal fade" id="roleDetailsModal" tabindex="-1" aria-labelledby="roleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="roleDetailsModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Role Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="roleDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let rolesTable;
let currentRoleFilter = 'all';

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();
    
    // Global search
    $('#roleGlobalSearch').on('keyup', function() {
        rolesTable.search(this.value).draw();
    });

    // Select all checkbox functionality
    $('#selectAllRoles').on('change', function() {
        const isChecked = this.checked;
        $('input[name="role_ids[]"]').prop('checked', isChecked);
        updateBulkActionButtons();
    });

    // Individual checkbox change
    $(document).on('change', 'input[name="role_ids[]"]', function() {
        updateBulkActionButtons();
        updateSelectAllCheckbox();
    });

    // Handle Create Form
    $('#createRoleForm').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Creating...');
        
        $.ajax({
            url: '<?= base_url('admin/roles/store') ?>',
            method: 'POST',
            data: $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#createRoleModal').modal('hide');
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 3000
                    });
                    rolesTable.ajax.reload();
                    $('#createRoleForm')[0].reset();
                    updateRoleCounts();
                } else {
                    Swal.fire('Error!', response.message || 'Failed to create role.', 'error');
                }
            },
            error: function(xhr) {
                let message = 'Failed to create role.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire('Error!', message, 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Create Role');
            }
        });
    });

    // Handle Edit Form
    $('#editRoleForm').on('submit', function(e) {
        e.preventDefault();
        const roleId = $('#editRoleId').val();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');
        
        $.ajax({
            url: `<?= base_url('admin/roles/update') ?>/${roleId}`,
            method: 'POST',
            data: $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editRoleModal').modal('hide');
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 3000
                    });
                    rolesTable.ajax.reload();
                    updateRoleCounts();
                } else {
                    let msg = response.message || 'Failed to update role.';
                    if (response.errors) {
                        msg += '<br><small>' + Object.values(response.errors).join('<br>') + '</small>';
                    }
                    Swal.fire({
                        title: 'Error!',
                        html: msg,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Failed to update role.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire('Error!', message, 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Role');
            }
        });
    });

    // Load initial stats
    updateRoleCounts();
});

function initializeDataTable() {
    rolesTable = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        ajax: {
            url: '<?= base_url('admin/roles/getDataTable') ?>',
            type: 'POST',
            data: function(d) {
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                d.filter = currentRoleFilter;
                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable Ajax Error:', error, thrown);
                console.log('Response:', xhr.responseText); // Add this for debugging
                Swal.fire('Error!', 'Failed to load roles data.', 'error');
            }
        },
        columns: [
            { 
                data: 0,
                name: 'checkbox',
                orderable: false,
                searchable: false,
                width: '40px',
                className: 'text-center'
            },
            { data: 1, name: 'role', width: '20%' },
            { data: 2, name: 'description', width: '25%' },
            { data: 3, name: 'permissions', orderable: false, width: '15%', className: 'text-center' },
            { data: 4, name: 'users', orderable: false, width: '10%', className: 'text-center' },
            { data: 5, name: 'status', width: '10%', className: 'text-center' },
            { data: 6, name: 'actions', orderable: false, searchable: false, width: '15%', className: 'text-center' }
        ],
        order: [[1, 'asc']],
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
            emptyTable: 'No roles found',
            zeroRecords: 'No matching roles found',
            info: 'Showing _START_ to _END_ of _TOTAL_ roles',
            infoEmpty: 'Showing 0 to 0 of 0 roles',
            infoFiltered: '(filtered from _MAX_ total roles)',
            search: '',
            searchPlaceholder: 'Search roles...',
            lengthMenu: 'Show _MENU_ roles per page',
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                previous: '<i class="fas fa-angle-left"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                last: '<i class="fas fa-angle-double-right"></i>'
            }
        },
        drawCallback: function(settings) {
            const json = this.api().ajax.json();
            if (json && json.stats) {
                updateRoleStats(json.stats);
            }
            updateBulkActionButtons();
            updateSelectAllCheckbox();
        }
    });
}

function updateBulkActionButtons() {
    const selectedCount = $('input[name="role_ids[]"]:checked').length;
    const isDisabled = selectedCount === 0;
    
    $('#bulkActivateBtn, #bulkDeactivateBtn, #bulkDeleteBtn').prop('disabled', isDisabled);
    $('#selectedCount').text(selectedCount);
}

function updateSelectAllCheckbox() {
    const totalCheckboxes = $('input[name="role_ids[]"]').length;
    const checkedCheckboxes = $('input[name="role_ids[]"]:checked').length;
    
    if (totalCheckboxes === 0) {
        $('#selectAllRoles').prop('indeterminate', false).prop('checked', false);
    } else if (checkedCheckboxes === totalCheckboxes) {
        $('#selectAllRoles').prop('indeterminate', false).prop('checked', true);
    } else if (checkedCheckboxes > 0) {
        $('#selectAllRoles').prop('indeterminate', true);
    } else {
        $('#selectAllRoles').prop('indeterminate', false).prop('checked', false);
    }
}

function updateRoleStats(stats) {
    $('#stat-total').text(stats.total || 0);
    $('#stat-active').text(stats.active || 0);
    $('#stat-permissions').text(stats.permissions || 0);
    $('#stat-role-permissions').text(stats.role_permissions || 0);
}

function updateRoleCounts() {
    $.get('<?= base_url('admin/roles/getCounts') ?>', function(data) {
        updateRoleStats(data);
    }).fail(function() {
        console.warn('Failed to load role counts');
    });
}

function refreshRoleTable() {
    rolesTable.ajax.reload();
    updateRoleCounts();
    
    Swal.fire({
        title: 'Refreshing...',
        text: 'Loading latest roles data',
        icon: 'info',
        timer: 1500,
        showConfirmButton: false
    });
}

function editRole(id) {
    const loadingBtn = $(`button[onclick="editRole(${id})"]`);
    const originalHtml = loadingBtn.html();
    loadingBtn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
    
    $.get(`<?= base_url('admin/roles/getRoleDetail') ?>/${id}`, function(response) {
        if (response.success) {
            const role = response.role;
            const rolePermissions = response.rolePermissions || [];
            
            // Fill form fields
            $('#editRoleId').val(role.id);
            $('#editRoleName').val(role.name);
            $('#editRoleDescription').val(role.description || '');
            $('#editRoleStatus').val(role.is_active);

            // Clear all permission checkboxes first
            $('#editPermissionGrid .form-check-input').prop('checked', false);
            
            // Check assigned permissions
            if (rolePermissions.length > 0) {
                rolePermissions.forEach(function(permissionId) {
                    $(`#edit_perm_${permissionId}`).prop('checked', true);
                });
            }
            
            $('#editRoleModal').modal('show');
        } else {
            Swal.fire('Error!', response.message || 'Failed to load role details.', 'error');
        }
    }).fail(function() {
        Swal.fire('Error!', 'Failed to load role details.', 'error');
    }).always(function() {
        loadingBtn.html(originalHtml).prop('disabled', false);
    });
}

function deleteRole(id, roleName) {
    Swal.fire({
        title: 'Are you sure?',
        html: `Delete role <strong>"${roleName}"</strong>?<br><small class="text-muted">This action cannot be undone!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i>Yes, delete it!',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the role',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: `<?= base_url('admin/roles/delete') ?>/${id}`,
                method: 'POST',
                data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message,
                            icon: 'success',
                            timer: 3000
                        });
                        rolesTable.ajax.reload();
                        updateRoleCounts();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to delete role.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', message, 'error');
                }
            });
        }
    });
}

function viewRole(id) {
    const loadingBtn = $(`button[onclick="viewRole(${id})"]`);
    const originalHtml = loadingBtn.html();
    loadingBtn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
    
    $.get(`<?= base_url('admin/roles/show') ?>/${id}`, function(response) {
        if (response.success && response.data) {
            const role = response.data.role;
            const permissions = response.data.permissions || [];
            const users = response.data.users || [];

            let permList = permissions.length > 0
                ? permissions.map(p => `
                    <li class="d-flex justify-content-between align-items-center">
                        <span>
                            <strong>${p.name || p.key}</strong>
                            <br><small class="text-muted">${p.description || '-'}</small>
                            <br><code class="small">${p.key}</code>
                        </span>
                        <span class="badge bg-info">${p.module || 'General'}</span>
                    </li>`).join('')
                : '<li class="text-muted text-center py-3">No permissions assigned</li>';

            let userList = users.length > 0
                ? users.map(u => `
                    <li class="d-flex justify-content-between align-items-center">
                        <span>
                            <strong>${u.first_name} ${u.last_name}</strong>
                            <br><small class="text-muted">${u.email}</small>
                        </span>
                        <span class="badge ${u.is_active ? 'bg-success' : 'bg-secondary'}">${u.is_active ? 'Active' : 'Inactive'}</span>
                    </li>`).join('')
                : '<li class="text-muted text-center py-3">No users assigned</li>';

            const statusBadge = role.is_active ? 
                '<span class="badge bg-success">Active</span>' : 
                '<span class="badge bg-secondary">Inactive</span>';

            const typesBadge = role.is_system_role ? 
                '<span class="badge bg-warning">System Role</span>' : 
                '<span class="badge bg-info">Custom Role</span>';

            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Role Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>${role.name}</td>
                            </tr>
                            <tr>
                                <td><strong>Description:</strong></td>
                                <td>${role.description || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>${typesBadge}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>${statusBadge}</td>
                            </tr>
                            <tr>
                                <td><strong>Permissions:</strong></td>
                                <td><span class="badge bg-primary">${permissions.length}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Users:</strong></td>
                                <td><span class="badge bg-success">${users.length}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Assigned Users (${users.length})</h6>
                        <div style="max-height: 200px; overflow-y: auto;">
                            <ul class="list-unstyled">${userList}</ul>
                        </div>
                    </div>
                </div>
                <hr>
                <h6 class="fw-bold">Assigned Permissions (${permissions.length})</h6>
                <div style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-unstyled">${permList}</ul>
                </div>
            `;

            $('#roleDetailsContent').html(content);
            $('#roleDetailsModal').modal('show');
        } else {
            Swal.fire('Error!', response.message || 'Failed to load role details.', 'error');
        }
    }).fail(function() {
        Swal.fire('Error!', 'Failed to load role details.', 'error');
    }).always(function() {
        loadingBtn.html(originalHtml).prop('disabled', false);
    });
}

// Permission management functions
function selectAllPermissions(select) {
    $('#createRoleModal .permission-grid .form-check-input').prop('checked', select);
}

function selectAllEditPermissions(select) {
    $('#editRoleModal .permission-grid .form-check-input').prop('checked', select);
}

function toggleModulePermissions(module) {
    const checkboxes = $(`.module-${module}`);
    const allChecked = checkboxes.filter(':checked').length === checkboxes.length;
    checkboxes.prop('checked', !allChecked);
}

function toggleEditModulePermissions(module) {
    const checkboxes = $(`.edit-module-${module}`);
    const allChecked = checkboxes.filter(':checked').length === checkboxes.length;
    checkboxes.prop('checked', !allChecked);
}

// Bulk actions
function bulkActivateRoles() {
    const selectedRoles = getSelectedRoles();
    if (selectedRoles.length === 0) {
        Swal.fire('Warning!', 'Please select roles to activate.', 'warning');
        return;
    }
    performBulkAction('activate', selectedRoles);
}

function bulkDeactivateRoles() {
    const selectedRoles = getSelectedRoles();
    if (selectedRoles.length === 0) {
        Swal.fire('Warning!', 'Please select roles to deactivate.', 'warning');
        return;
    }
    performBulkAction('deactivate', selectedRoles);
}

function bulkDeleteRoles() {
    const selectedRoles = getSelectedRoles();
    if (selectedRoles.length === 0) {
        Swal.fire('Warning!', 'Please select roles to delete.', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Are you sure?',
        html: `Delete <strong>${selectedRoles.length}</strong> selected roles?<br><small class="text-muted">This action cannot be undone!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i>Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            performBulkAction('delete', selectedRoles);
        }
    });
}

function getSelectedRoles() {
    return $('input[name="role_ids[]"]:checked').map(function() {
        return this.value;
    }).get();
}

function performBulkAction(action, roleIds) {
    Swal.fire({
        title: 'Processing...',
        text: `Please wait while we ${action} the selected roles`,
        icon: 'info',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: `<?= base_url('admin/roles/bulkAction') ?>`,
        method: 'POST',
        data: {
            action: action,
            role_ids: roleIds,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    timer: 3000
                });
                rolesTable.ajax.reload();
                updateRoleCounts();
                $('#selectAllRoles').prop('checked', false);
                updateBulkActionButtons();
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function(xhr) {
            let message = `Failed to ${action} roles.`;
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire('Error!', message, 'error');
        }
    });
}

// Additional utility functions
function exportRoles() {
    window.location.href = '<?= base_url('admin/roles/export') ?>?filter=' + currentRoleFilter;
}

function syncRolePermissions() {
    Swal.fire({
        title: 'Sync Role Permissions?',
        text: 'This will synchronize all role permissions with the current permission structure.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-sync me-1"></i>Yes, sync now!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('admin/roles/syncPermissions') ?>', {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }, function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success');
                    rolesTable.ajax.reload();
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            });
        }
    });
}

function cleanUnusedRoles() {
    Swal.fire({
        title: 'Clean Unused Roles?',
        text: 'This will remove roles that have no users assigned to them.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: '<i class="fas fa-broom me-1"></i>Yes, clean now!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('admin/roles/cleanUnused') ?>', {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }, function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success');
                    rolesTable.ajax.reload();
                    updateRoleCounts();
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>