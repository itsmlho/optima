<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-key me-2 text-primary"></i>Permission Management
            </h1>
            <p class="mb-0 text-muted">Create and manage system permissions. Permissions are assigned to roles.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                <i class="fas fa-plus me-2"></i>Create Permission
            </button>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i> Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= base_url('admin/advanced-users') ?>"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/roles') ?>"><i class="fas fa-user-tag"></i> Manage Roles</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-key stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-permissions">
                            <?= $stats['total'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Total Permissions</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-success-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-stack stat-icon text-success"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-module-count">
                            <?= $stats['modules'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Modules</div>
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
                        <div class="stat-value" id="stat-active-permissions">
                            <?= $stats['active'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Active Permissions</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-file-earmark-text stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-pages-count">
                            <?= $stats['pages'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Total Pages</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card shadow">
        <div class="card-body">
            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4" id="permissionFilterTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-permissions-tab" data-filter="all" type="button">
                        <i class="fas fa-list me-2"></i>All Permissions
                        <span class="badge bg-light text-dark ms-2" id="all-permissions-count">
                            <?= $stats['total'] ?? 0 ?>
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="active-permissions-tab" data-filter="active" type="button">
                        <i class="fas fa-check-circle me-2"></i>Active
                        <span class="badge bg-success ms-2" id="active-permissions-count">
                            <?= $stats['active'] ?? 0 ?>
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="inactive-permissions-tab" data-filter="inactive" type="button">
                        <i class="fas fa-times-circle me-2"></i>Inactive
                        <span class="badge bg-secondary ms-2" id="inactive-permissions-count">
                            <?= $stats['inactive'] ?? 0 ?>
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="module-permissions-tab" data-filter="module" type="button">
                        <i class="fas fa-cubes me-2"></i>By Module
                        <span class="badge bg-info ms-2" id="module-permissions-count">
                            <?= $stats['modules'] ?? 0 ?>
                        </span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="permissionFilterTabsContent">
                <div class="tab-pane fade show active" id="all-permissions" role="tabpanel">
                    <!-- Search and Controls -->
                    <!-- <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 me-3">All Permissions</h5>
                            <div class="input-group" style="width: 300px;">
                                <span class="input-group-text">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control" id="permissionGlobalSearch" 
                                       placeholder="Search permissions...">
                            </div>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshPermissionTable()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div> -->
                    
                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="permissionsTable">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-tag me-1"></i>Display Name</th>
                                    <th><i class="fas fa-key me-1"></i>Key</th>
                                    <th><i class="fas fa-cube me-1"></i>Module</th>
                                    <th><i class="fas fa-file me-1"></i>Page</th>
                                    <th><i class="fas fa-cog me-1"></i>Action</th>
                                    <th><i class="fas fa-info-circle me-1"></i>Description</th>
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
    </div>
</div>

<!-- Create Permission Modal -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createPermissionForm">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createPermissionModalLabel">
                        <i class="fas fa-plus me-2"></i>Create New Permission
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Permission Management:</strong> Create and manage system permissions. 
                        Permissions will be assigned to roles, not directly to users.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tag me-1"></i>Display Name*
                                </label>
                                <input type="text" class="form-control" name="display_name" required
                                       placeholder="e.g., Create Users">
                                <div class="form-text">Human-readable name for this permission</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-key me-1"></i>Permission Key*
                                </label>
                                <input type="text" class="form-control" name="key_name" required 
                                       placeholder="e.g., users.create">
                                <div class="form-text">
                                    Use format: <code>module.action</code> (example: users.view, reports.export, dashboard.access)
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control" name="description" rows="3" 
                                  placeholder="Describe what this permission allows..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-cube me-1"></i>Module*
                                </label>
                                <select class="form-select" name="module" required>
                                    <option value="">Select Module</option>
                                    <option value="admin">Admin</option>
                                    <option value="dashboard">Dashboard</option>
                                    <option value="users">User Management</option>
                                    <option value="roles">Role Management</option>
                                    <option value="permissions">Permission Management</option>
                                    <option value="service">Service Management</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="warehouse">Warehouse</option>
                                    <option value="purchasing">Purchasing</option>
                                    <option value="finance">Finance</option>
                                    <option value="accounting">Accounting</option>
                                    <option value="reports">Reports</option>
                                    <option value="system">System</option>
                                    <option value="perizinan">Perizinan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-file me-1"></i>Page*
                                </label>
                                <input type="text" class="form-control" name="page" required
                                       placeholder="e.g., users, dashboard, reports">
                                <div class="form-text">Page or section name</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-cog me-1"></i>Action*
                                </label>
                                <select class="form-select" name="action" required>
                                    <option value="">Select Action</option>
                                    <option value="view">View</option>
                                    <option value="create">Create</option>
                                    <option value="edit">Edit</option>
                                    <option value="delete">Delete</option>
                                    <option value="manage">Manage</option>
                                    <option value="export">Export</option>
                                    <option value="import">Import</option>
                                    <option value="approve">Approve</option>
                                    <option value="reject">Reject</option>
                                    <option value="access">Access</option>
                                    <option value="navigation">Navigation</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Create Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Permission Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editPermissionForm">
                <input type="hidden" id="editPermissionId" name="id">
                <div class="modal-header bg-primary text-secondary">
                    <h5 class="modal-title" id="editPermissionModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Permission
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tag me-1"></i>Display Name*
                                </label>
                                <input type="text" class="form-control" id="editPermissionDisplayName" name="display_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-key me-1"></i>Permission Key*
                                </label>
                                <input type="text" class="form-control" id="editPermissionKeyName" name="key_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control" id="editPermissionDescription" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-cube me-1"></i>Module*
                                </label>
                                <select class="form-select" id="editPermissionModule" name="module" required>
                                    <option value="">Select Module</option>
                                    <option value="admin">Admin</option>
                                    <option value="dashboard">Dashboard</option>
                                    <option value="users">User Management</option>
                                    <option value="roles">Role Management</option>
                                    <option value="permissions">Permission Management</option>
                                    <option value="service">Service Management</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="warehouse">Warehouse</option>
                                    <option value="purchasing">Purchasing</option>
                                    <option value="finance">Finance</option>
                                    <option value="accounting">Accounting</option>
                                    <option value="reports">Reports</option>
                                    <option value="system">System</option>
                                    <option value="perizinan">Perizinan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-file me-1"></i>Page*
                                </label>
                                <input type="text" class="form-control" id="editPermissionPage" name="page" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-cog me-1"></i>Action*
                                </label>
                                <select class="form-select" id="editPermissionAction" name="action" required>
                                    <option value="">Select Action</option>
                                    <option value="view">View</option>
                                    <option value="create">Create</option>
                                    <option value="edit">Edit</option>
                                    <option value="delete">Delete</option>
                                    <option value="manage">Manage</option>
                                    <option value="export">Export</option>
                                    <option value="import">Import</option>
                                    <option value="approve">Approve</option>
                                    <option value="reject">Reject</option>
                                    <option value="access">Access</option>
                                    <option value="navigation">Navigation</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
const columnsDefault = [
    { data: 0, name: 'display_name', width: '18%' }, // Display Name
    { data: 1, name: 'key_name', width: '18%' },     // Key Name
    { data: 2, name: 'module', width: '12%', className: 'text-center' }, // Module
    { data: 3, name: 'page', width: '12%' },         // Page
    { data: 4, name: 'action', width: '10%', className: 'text-center' }, // Action
    { data: 5, name: 'description', width: '20%' },  // Description
    { data: 6, name: 'actions', orderable: false, searchable: false, width: '10%', className: 'text-center' } // Actions
];
const columnsByModule = [
    { data: 'module', name: 'module', width: '80%' },
    { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '20%', className: 'text-center' }
];

let permissionsTable;
let currentPermissionFilter = 'all';

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();
    
    // Tab click handlers
    $('.nav-link[data-filter]').on('click', function(e) {
        e.preventDefault();
        const filterType = $(this).data('filter');
        if (filterType) {
            filterPermissions(filterType);
        }
    });

    // Global search
    $('#permissionGlobalSearch').on('keyup', function() {
        permissionsTable.search(this.value).draw();
    });

    // Create Permission Form
    $('#createPermissionForm').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Creating...');
        
        $.ajax({
            url: '<?= base_url('admin/permissions/store') ?>',
            method: 'POST',
            data: $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#createPermissionModal').modal('hide');
                    OptimaNotify.success(response.message);
                    permissionsTable.ajax.reload();
                    $('#createPermissionForm')[0].reset();
                } else {
                    OptimaNotify.error(response.message || 'Failed to create permission.');
                }
            },
            error: function(xhr) {
                let message = 'Failed to create permission.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                OptimaNotify.error(message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Create Permission');
            }
        });
    });

    // Edit Permission Form
    $('#editPermissionForm').on('submit', function(e) {
        e.preventDefault();
        const permissionId = $('#editPermissionId').val();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');
        
        $.ajax({
            url: `<?= base_url('admin/permissions/update') ?>/${permissionId}`,
            method: 'POST',
            data: $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editPermissionModal').modal('hide');
                    OptimaNotify.success(response.message);
                    permissionsTable.ajax.reload();
                } else {
                    let msg = response.message || 'Failed to update permission.';
                    if (response.errors) {
                        msg += ' ' + Object.values(response.errors).join(' ');
                    }
                    OptimaNotify.error(msg);
                }
            },
            error: function(xhr) {
                let message = 'Failed to update permission.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                OptimaNotify.error(message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Permission');
            }
        });
    });
});

function initializeDataTable() {
    permissionsTable = $('#permissionsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        ajax: {
            url: '<?= base_url('admin/permissions/getDataTable') ?>',
            type: 'POST',
            data: function(d) {
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                d.filter = currentPermissionFilter;
                return d;
            },
                error: function(xhr, error, thrown) {
                    console.error('DataTable Ajax Error:', error, thrown);
                    OptimaNotify.error('Failed to load permissions data.');
                }
            },
            columns: columnsDefault, // default
        order: [[2, 'asc'], [3, 'asc'], [0, 'asc']], // Order by: Module, Page, Display Name
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
            emptyTable: 'No permissions found',
            zeroRecords: 'No matching permissions found',
            info: 'Showing _START_ to _END_ of _TOTAL_ permissions',
            infoEmpty: 'Showing 0 to 0 of 0 permissions',
            infoFiltered: '(filtered from _MAX_ total permissions)',
            search: '',
            searchPlaceholder: 'Search permissions...',
            lengthMenu: 'Show _MENU_ permissions per page',
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
                updatePermissionStats(json.stats);
            }
        }
    });
}

function toggleModulePermissions(module, btn) {
    const tr = $(btn).closest('tr');
    // Sudah terbuka? Tutup
    if (tr.next().hasClass('module-permissions-row')) {
        tr.next().remove();
        $(btn).find('i').removeClass('fa-minus').addClass('fa-plus');
        return;
    }
    // Tutup yang lain
    $('.module-permissions-row').remove();
    $('.btn-module-toggle i').removeClass('fa-minus').addClass('fa-plus');

    // AJAX ambil permission di module ini
    $.get('<?= base_url('admin/permissions/byModule') ?>/' + module, function(response) {
        let html = '<tr class="module-permissions-row"><td colspan="2">';
        if (response.permissions && response.permissions.length > 0) {
            html += '<ul class="list-group">';
            response.permissions.forEach(function(perm) {
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-key text-primary me-2"></i>${perm.key} <span class="text-muted ms-2">${perm.name || ''}</span></span>
                    <span>${perm.description || ''}</span>
                </li>`;
            });
            html += '</ul>';
        } else {
            html += '<span class="text-muted">No permissions in this module.</span>';
        }
        html += '</td></tr>';
        tr.after(html);
        $(btn).find('i').removeClass('fa-plus').addClass('fa-minus');
    });
}

function filterPermissions(filterType) {
    // Update active tab
    $('.nav-link[data-filter]').removeClass('active');
    $(`#${filterType}-permissions-tab`).addClass('active');
    
    // Update filter
    currentPermissionFilter = filterType;

    // Update kolom DataTables sesuai filter
    if (filterType === 'module') {
        permissionsTable.clear().destroy();
        permissionsTable = $('#permissionsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ajax: {
                url: '<?= base_url('admin/permissions/getDataTable') ?>',
                type: 'POST',
                data: function(d) {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                    d.filter = currentPermissionFilter;
                    return d;
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable Ajax Error:', error, thrown);
                    OptimaNotify.error('Failed to load permissions data.');
                }
            },
            columns: columnsByModule,
            order: [[0, 'asc']],
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
                emptyTable: 'No modules found',
                zeroRecords: 'No matching modules found',
                info: 'Showing _START_ to _END_ of _TOTAL_ modules',
                infoEmpty: 'Showing 0 to 0 of 0 modules',
                infoFiltered: '(filtered from _MAX_ total modules)',
                search: '',
                searchPlaceholder: 'Search modules...',
                lengthMenu: 'Show _MENU_ modules per page',
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>'
                }
            }
        });

        // Ganti header kolom
        $('#permissionsTable thead').html(`
            <tr>
                <th>Module</th>
                <th class="text-center">Actions</th>
            </tr>
        `);
    } else {
        permissionsTable.clear().destroy();
        initializeDataTable();
        // Kembalikan header default
        $('#permissionsTable thead').html(`
            <tr>
                <th><i class="fas fa-key me-1"></i>Permission Key</th>
                <th><i class="fas fa-tag me-1"></i>Display Name</th>
                <th><i class="fas fa-info-circle me-1"></i>Description</th>
                <th class="text-center"><i class="fas fa-cube me-1"></i>Module</th>
                <th class="text-center"><i class="fas fa-cogs me-1"></i>Actions</th>
            </tr>
        `);
    }

    // Update header title based on filter
    let headerTitle = '';
    switch(filterType) {
        case 'all':
            headerTitle = 'All Permissions';
            break;
        case 'system':
            headerTitle = 'System Permissions';
            break;
        case 'custom':
            headerTitle = 'Custom Permissions';
            break;
        case 'module':
            headerTitle = 'Permissions by Module';
            break;
    }
    $('.d-flex h5').text(headerTitle);

    console.log('Filter changed to:', filterType);
}

function refreshPermissionTable() {
    permissionsTable.ajax.reload();
    OptimaNotify.info('Loading latest permissions data');
}

function updatePermissionStats(stats) {
    // Update main stats cards
    $('#stat-total-permissions').text(stats.total || 0);
    $('#stat-module-count').text(stats.modules || 0);
    $('#stat-system-permissions').text(stats.system || 0);
    $('#stat-custom-permissions').text(stats.custom || 0);

    // Update tab badges
    $('#all-permissions-count').text(stats.total || 0);
    $('#system-permissions-count').text(stats.system || 0);
    $('#custom-permissions-count').text(stats.custom || 0);
    $('#module-permissions-count').text(stats.modules || 0);
}

function editPermission(id) {
    const loadingBtn = $(`button[onclick="editPermission(${id})"]`);
    const originalHtml = loadingBtn.html();
    loadingBtn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
    
    $.get(`<?= base_url('admin/permissions/getDetail') ?>/${id}`, function(response) {
        if (response.success) {
            const permission = response.permission;
            $('#editPermissionId').val(permission.id);
            $('#editPermissionDisplayName').val(permission.display_name);
            $('#editPermissionKeyName').val(permission.key_name);
            $('#editPermissionDescription').val(permission.description);
            
            // Fix: Check if module exists in dropdown, if not add it
            if (permission.module && $('#editPermissionModule option[value="'+permission.module+'"]').length === 0) {
                $('#editPermissionModule').append(new Option(permission.module, permission.module));
            }
            $('#editPermissionModule').val(permission.module);
            
            $('#editPermissionPage').val(permission.page);
            
            // Fix: Check if action exists in dropdown, if not add it
            if (permission.action && $('#editPermissionAction option[value="'+permission.action+'"]').length === 0) {
                $('#editPermissionAction').append(new Option(permission.action, permission.action));
            }
            $('#editPermissionAction').val(permission.action);
            
            $('#editPermissionModal').modal('show');
        } else {
            OptimaNotify.error(response.message || 'Failed to load permission.');
        }
    }).fail(function() {
        OptimaNotify.error('Failed to load permission details.');
    }).always(function() {
        loadingBtn.html(originalHtml).prop('disabled', false);
    });
}

function deletePermission(id) {
    OptimaConfirm.danger({
        title: 'Are you sure?',
        text: 'This permission will be permanently deleted and removed from all roles!',
        confirmText: 'Yes, delete it!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            OptimaPro.showLoading('Deleting permission...');
            
            $.ajax({
                url: `<?= base_url('admin/permissions/delete') ?>/${id}`,
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    OptimaPro.hideLoading();
                    if (response.success) {
                        OptimaNotify.success(response.message);
                        permissionsTable.ajax.reload();
                    } else {
                        OptimaNotify.error(response.message);
                    }
                },
                error: function(xhr) {
                    OptimaPro.hideLoading();
                    let message = 'Failed to delete permission.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    OptimaNotify.error(message);
                }
            });
        }
    });
}

// Auto-generate display name from key
$(document).on('input', 'input[name="key_name"]', function() {
    const key = $(this).val();
    const nameField = $(this).closest('form').find('input[name="display_name"]');
    
    if (key && !nameField.val()) {
        // Convert key to readable name
        let name = key.replace(/\./g, ' ').replace(/_/g, ' ');
        name = name.replace(/\b\w/g, l => l.toUpperCase());
        nameField.val(name);
    }
});
</script>
<?= $this->endSection() ?>