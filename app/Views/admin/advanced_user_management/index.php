<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<?php
// Load permission helper for UI conditionals
helper('permission_helper');
?>

<!-- Success/Error Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0"><?= $stats['total_users'] ?? 0 ?></h3>
                        <p class="mb-0">Total Users</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0"><?= $stats['active_users'] ?? 0 ?></h3>
                        <p class="mb-0">Active Users</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0"><?= $stats['users_with_multiple_divisions'] ?? 0 ?></h3>
                        <p class="mb-0">Multi-Division Users</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-sitemap fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>  
    <div class="col-md-3">
        <div class="card stats-card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0"><?= $stats['users_with_custom_permissions'] ?? 0 ?></h3>
                        <p class="mb-0">Custom Permissions</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-key fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">User Management</h4>
            <small class="text-muted">Assign roles, divisions, and manage user access</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/advanced-users/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create User
            </a>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i> Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportUsers()"><i class="fas fa-download"></i> Export Users</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/roles') ?>"><i class="fas fa-user-tag"></i> Manage Roles</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/permissions') ?>"><i class="fas fa-key"></i> Manage Permissions</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="cleanExpiredPermissions()"><i class="fas fa-trash"></i> Clean Expired</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        
        <!-- Tab Content -->
        <div class="tab-content" id="filterTabsContent">
            <!-- All Users Tab -->
            <div class="tab-pane fade show active" id="all-users" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-striped" id="usersTable">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Divisions</th>
                                <th>Roles</th>
                                <th>Custom Permissions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Division Tabs -->
            <?php if (!empty($divisions)): ?>
                <?php foreach ($divisions as $division): ?>
                <div class="tab-pane fade" id="div-<?= $division['id'] ?>" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5><?= esc($division['name']) ?> Division Users</h5>
                        <a href="<?= base_url('admin/advanced-users/division/' . $division['id']) ?>" class="btn btn-primary btn-sm">
                            Manage Division
                        </a>
                    </div>
                    <div id="division-<?= $division['id'] ?>-content">
                        Loading...
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bulk Permission Modal -->
<div class="modal fade" id="bulkPermissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Permission Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkPermissionForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Select Users</label>
                            <div class="user-select-list" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="selected_users[]" value="<?= $user['id'] ?>" id="user<?= $user['id'] ?>">
                                        <label class="form-check-label" for="user<?= $user['id'] ?>">
                                            <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                            <small class="text-muted d-block"><?= esc($user['email']) ?></small>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-muted">No users found.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select Permissions</label>
                            <div class="permission-select-list" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                <?php if (!empty($permissions)): ?>
                                    <?php foreach ($permissions as $module => $modulePermissions): ?>
                                    <div class="mb-3">
                                        <h6 class="text-primary"><?= ucfirst($module) ?></h6>
                                        <?php foreach ($modulePermissions as $permission): ?>
                                        <div class="form-check ms-3">
                                            <input class="form-check-input" type="checkbox" name="selected_permissions[]" value="<?= $permission['id'] ?>" id="perm<?= $permission['id'] ?>">
                                            <label class="form-check-label" for="perm<?= $permission['id'] ?>">
                                                <?= esc($permission['name']) ?>
                                                <small class="text-muted d-block"><?= esc($permission['key']) ?></small>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-muted">No permissions found.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Division (Optional)</label>
                            <select class="form-select" name="division_id">
                                <option value="">All Divisions</option>
                                <?php if (!empty($divisions)): ?>
                                    <?php foreach ($divisions as $division): ?>
                                    <option value="<?= $division['id'] ?>"><?= esc($division['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Action</label>
                            <select class="form-select" name="action" required>
                                <option value="grant">Grant Permission</option>
                                <option value="deny">Deny Permission</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Permission Matrix Modal -->
<div class="modal fade modal-wide" id="userMatrixModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Permission Matrix</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="userMatrixContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve User Modal -->
<div class="modal fade" id="approveUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-secondary">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Approve User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveUserForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Konfirmasi Data User</strong><br>
                        Silakan verifikasi data user dan tentukan Divisi serta Posisi sebelum mengaktifkan akun.
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi User</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Nama:</strong><br>
                                    <span id="approveUserName">-</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Email:</strong><br>
                                    <span id="approveUserEmail">-</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Username:</strong><br>
                                    <span id="approveUserUsername">-</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>No. Telepon:</strong><br>
                                    <span id="approveUserPhone">-</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Divisi (User Input):</strong><br>
                                    <span id="approveUserDivision" class="text-muted">-</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Posisi (User Input):</strong><br>
                                    <span id="approveUserPosition" class="text-muted">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-black">
                            <h6 class="mb-0">
                                <i class="fas fa-building me-2"></i>Division & Role
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="approveDivisionId" class="form-label">
                                        <strong>Division <span class="text-danger">*</span></strong>
                                    </label>
                                    <select class="form-select" id="approveDivisionId" name="division_id" required>
                                        <option value="">Select Division</option>
                                    </select>
                                    <div class="invalid-feedback">Silakan pilih division.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="approveRoleId" class="form-label">
                                        <strong>Role <span class="text-danger">*</span></strong>
                                    </label>
                                    <select class="form-select" id="approveRoleId" name="role_id" required>
                                        <option value="">Select Role</option>
                                    </select>
                                    <div class="invalid-feedback">Silakan pilih role.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="approveUserId" name="user_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-2"></i>Approve & Aktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Helper function to get CSRF token from cookie
function getCsrfToken() {
    const name = '<?= csrf_token() ?>=';
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookies = decodedCookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        let c = cookies[i].trim();
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
}

// Setup global AJAX config to include CSRF token
$.ajaxSetup({
    beforeSend: function(xhr) {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', getCsrfToken());
    }
});

$(document).ready(function() {
    // Handle approve form submission
    $('#approveUserForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const userId = $('#approveUserId').val();
        const divisionId = $('#approveDivisionId').val();
        const roleId = $('#approveRoleId').val();
        
        // Validate
        if (!divisionId || !roleId) {
            form.addClass('was-validated');
            return false;
        }
        
        // Disable submit button
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
        
        $.ajax({
            url: '<?= base_url('admin/advanced-users/approve-user') ?>/' + userId,
            type: 'POST',
            dataType: 'json',
            data: {
                division_id: divisionId,
                role_id: roleId
            },
            success: function(response) {
                if (response.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('approveUserModal')).hide();
                    
                    // Show success message
                    alert(response.message);
                    
                    // Reload table
                    $('#usersTable').DataTable().ajax.reload(null, false);
                } else {
                    alert('Error: ' + (response.message || 'Gagal mengaktifkan user'));
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                alert('Error: ' + (response.message || 'Terjadi kesalahan saat mengaktifkan user'));
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
        
        return false;
    });
    // Initialize DataTable with AJAX
    $('#usersTable').DataTable({
        responsive: true,
        pageLength: 25,
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/advanced-users/getDataTable') ?>',
            type: 'POST',
            data: function(d) {
                // Include CSRF token in request
                return d;
            },
            error: function(xhr, error, thrown) {
                let msg = 'Failed to load users data.';
                if (xhr.responseText) {
                    try {
                        let resp = JSON.parse(xhr.responseText);
                        if (resp.error) msg += ' ' + resp.error;
                    } catch (e) {}
                }
                alert(msg);
                console.error('DataTable error:', xhr.status, xhr.responseText);
            }
        },
        columns: [
            { data: 'user_info' },
            { data: 'email' },
            { data: 'divisions' },
            { data: 'roles' },
            { data: 'custom_permissions', defaultContent: '<span class="text-muted">-</span>' },
            { data: 'status' },
            { data: 'actions', orderable: false }
        ],
        order: [[0, 'asc']]
    });

    // Load division content when tab is activated
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr('data-bs-target');
        if (target && target.startsWith('#div-')) {
            var divisionId = target.replace('#div-', '');
            loadDivisionUsers(divisionId);
        }
    });

    // Bulk permission form
    $('#bulkPermissionForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var selectedUsers = formData.getAll('selected_users[]');
        var selectedPermissions = formData.getAll('selected_permissions[]');
        
        if (selectedUsers.length === 0) {
            alert('Please select at least one user.');
            return;
        }
        
        if (selectedPermissions.length === 0) {
            alert('Please select at least one permission.');
            return;
        }
        
        $.ajax({
            url: '<?= base_url('admin/advanced-users/bulk-assign-permissions') ?>',
            method: 'POST',
            data: {
                user_ids: selectedUsers,
                permission_ids: selectedPermissions,
                division_id: formData.get('division_id'),
                granted: formData.get('action') === 'grant' ? 1 : 0
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Bulk permission assignment completed successfully.');
                    $('#bulkPermissionModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    alert('Error: ' + response.message);
                } catch (e) {
                    alert('An error occurred while processing the request.');
                }
            }
        });
    });
});

function loadDivisionUsers(divisionId) {
    var contentDiv = $('#division-' + divisionId + '-content');
    
    if (contentDiv.data('loaded')) {
        return; // Already loaded
    }
    
    $.get('<?= base_url('admin/advanced-users/division-users') ?>/' + divisionId, function(data) {
        contentDiv.html(data);
        contentDiv.data('loaded', true);
    }).fail(function() {
        contentDiv.html('<div class="alert alert-danger">Error loading division users.</div>');
    });
}

function viewUserMatrix(userId) {
    $('#userMatrixContent').html('Loading...');
    $('#userMatrixModal').modal('show');
    
    $.get('<?= base_url('admin/advanced-users/user-matrix') ?>/' + userId, function(response) {
        if (response.success) {
            renderUserMatrix(response.data);
        } else {
            $('#userMatrixContent').html('<div class="alert alert-danger">Error loading user matrix.</div>');
        }
    });
}

function renderUserMatrix(data) {
    let html = `
        <div class="row">
            <div class="col-md-5">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="mb-2">User Information</h6>
                        <table class="table table-sm mb-0">
                            <tr><td><strong>Name</strong></td><td>${data.user.first_name} ${data.user.last_name}</td></tr>
                            <tr><td><strong>Email</strong></td><td>${data.user.email}</td></tr>
                            <tr><td><strong>Status</strong></td><td><span class="badge bg-${data.user.status === 'active' ? 'success' : 'warning'}">${data.user.status}</span></td></tr>
                        </table>
                        <h6 class="mt-3 mb-2">Roles</h6>
                        <div>`;
    if (data.roles && data.roles.length > 0) {
        data.roles.forEach(role => {
            html += `<span class="badge bg-primary me-1 mb-1">${role.name}</span>`;
        });
    } else {
        html += '<span class="text-muted">No roles assigned</span>';
    }
    html += `</div>
                        <h6 class="mt-3 mb-2">Divisions</h6>
                        <div>`;
    if (data.divisions && data.divisions.length > 0) {
        data.divisions.forEach(division => {
            html += `<span class="badge bg-info me-1 mb-1">${division.name}${division.is_head ? ' <i class="fas fa-crown"></i>' : ''}</span>`;
        });
    } else {
        html += '<span class="text-muted">No divisions assigned</span>';
    }
    html += `</div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card permission-matrix">
                    <div class="card-body">
                        <h6 class="mb-3">Effective Permissions</h6>
                        <div class="row">`;

    // Group permissions by module
    if (data.permissions && data.permissions.effective_permissions) {
        let permissions = data.permissions.effective_permissions;
        let groupedPerms = {};
        Object.keys(permissions).forEach(key => {
            let module = key.split('.')[0];
            if (!groupedPerms[module]) groupedPerms[module] = [];
            groupedPerms[module].push({key: key, granted: permissions[key]});
        });

        Object.keys(groupedPerms).forEach(module => {
            html += `<div class="col-md-6 mb-3">
                        <div class="fw-bold text-primary mb-2">${module.charAt(0).toUpperCase() + module.slice(1)}</div>
                        <ul class="list-unstyled mb-0">`;
            groupedPerms[module].forEach(perm => {
                html += `<li class="mb-2 d-flex align-items-center">
                            <span class="me-2">
                                <span class="badge rounded-circle bg-${perm.granted ? 'success' : 'danger'}" style="width:22px;height:22px;">
                                    ${perm.granted ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>'}
                                </span>
                            </span>
                            <span class="${perm.granted ? 'text-success' : 'text-danger'}">${perm.key}</span>
                        </li>`;
            });
            html += `</ul></div>`;
        });
    } else {
        html += '<div class="col-12"><span class="text-muted">No permissions found</span></div>';
    }

    html += `       </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#userMatrixContent').html(html);
}

function quickAssignMenu(userId) {
    window.location.href = '<?= base_url('admin/advanced-users/quick-assign') ?>?user=' + userId;
}

// Division-Role mapping (same as in form.php)
const allRoles = [
    // Marketing Division (ID: 0)
    { id: '2', name: 'Head Marketing', division: '0' },
    { id: '3', name: 'Staff Marketing', division: '0' },
    
    // Service Diesel Division (ID: 1)
    { id: '6', name: 'Head Service Diesel', division: '1' },
    { id: '7', name: 'Staff Service Diesel', division: '1' },
    
    // Service Electric Division (ID: 2)
    { id: '8', name: 'Head Service Electric', division: '2' },
    { id: '9', name: 'Staff Service Electric', division: '2' },
    
    // Warehouse Division (ID: 3)
    { id: '16', name: 'Head Warehouse', division: '3' },
    { id: '32', name: 'Staff Warehouse', division: '3' },
    
    // HRD Division (ID: 4)
    { id: '14', name: 'Head HRD', division: '4' },
    { id: '15', name: 'Staff HRD', division: '4' },
    
    // Administrator Division (ID: 5)
    { id: '30', name: 'Administrator', division: '5' },
    
    // Purchasing Division (ID: 6)
    { id: '10', name: 'Head Purchasing', division: '6' },
    { id: '11', name: 'Staff Purchasing', division: '6' },
    
    // IT Division (ID: 7)
    { id: '33', name: 'Head IT', division: '7' },
    { id: '34', name: 'Staff IT', division: '7' }
];

// Function to update roles based on division
function updateApprovalRoles(selectedDivision) {
    console.log('updateApprovalRoles called with division:', selectedDivision);
    const roleSelect = $('#approveRoleId');
    roleSelect.empty();
    roleSelect.append('<option value="">Select Role</option>');
    
    if (selectedDivision) {
        // Convert division ID to string for comparison
        const divisionStr = selectedDivision.toString();
        const filteredRoles = allRoles.filter(role => role.division === divisionStr);
        console.log('Filtered roles for division', divisionStr, ':', filteredRoles);
        
        if (filteredRoles.length === 0) {
            console.warn('No roles found for division:', divisionStr);
        }
        
        filteredRoles.forEach(role => {
            roleSelect.append('<option value="' + role.id + '">' + role.name + '</option>');
        });
        
        // Force trigger change event
        roleSelect.trigger('change');
    }
}

function approveUser(userId, userName) {
    // Load user data and show modal
    $.ajax({
        url: '<?= base_url('admin/advanced-users/get-user-for-approval') ?>/' + userId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Populate user info
                $('#approveUserId').val(response.user.id);
                $('#approveUserName').text(response.user.first_name + ' ' + response.user.last_name);
                $('#approveUserEmail').text(response.user.email);
                $('#approveUserUsername').text(response.user.username || '-');
                $('#approveUserPhone').text(response.user.phone || '-');
                
                // Show user input division and position
                $('#approveUserDivision').text(response.user.division_name || '-');
                $('#approveUserPosition').text(response.user.position || '-');
                
                // Populate divisions dropdown (for admin selection)
                const divisionSelect = $('#approveDivisionId');
                divisionSelect.empty().append('<option value="">Select Division</option>');
                if (response.divisions && response.divisions.length > 0) {
                    response.divisions.forEach(function(division) {
                        divisionSelect.append('<option value="' + division.id + '">' + division.name + '</option>');
                    });
                }
                
                // Reset role dropdown
                $('#approveRoleId').empty().append('<option value="">Select Role</option>');
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('approveUserModal'));
                modal.show();
            } else {
                alert('Error: ' + (response.message || 'Gagal memuat data user'));
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            alert('Error: ' + (response.message || 'Terjadi kesalahan saat memuat data user'));
        }
    });
}

// Handle approve form submission
$('#approveUserForm').on('submit', function(e) {
    e.preventDefault();
    
    const form = $(this);
    const userId = $('#approveUserId').val();
    const divisionId = $('#approveDivisionId').val();
    const roleId = $('#approveRoleId').val();
    
    // Validate
    if (!divisionId || !roleId) {
        form.addClass('was-validated');
        return false;
    }
    
    // Disable submit button
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
    
    $.ajax({
        url: '<?= base_url('admin/advanced-users/approve-user') ?>/' + userId,
        type: 'POST',
        dataType: 'json',
        data: {
            division_id: divisionId,
            role_id: roleId
        },
        success: function(response) {
            if (response.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('approveUserModal')).hide();
                
                // Show success message
                alert(response.message);
                
                // Reload table
                $('#usersTable').DataTable().ajax.reload(null, false);
            } else {
                alert('Error: ' + (response.message || 'Gagal mengaktifkan user'));
                submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            alert('Error: ' + (response.message || 'Terjadi kesalahan saat mengaktifkan user'));
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
    
    return false;
});

// Handle division change for approval modal
$(document).on('change', '#approveDivisionId', function() {
    console.log('Approval division changed to:', $(this).val());
    const selectedDivision = $(this).val();
    updateApprovalRoles(selectedDivision);
});

// Also handle when modal is shown (in case division is pre-selected)
$(document).on('shown.bs.modal', '#approveUserModal', function() {
    const selectedDivision = $('#approveDivisionId').val();
    if (selectedDivision) {
        console.log('Modal shown, division already selected:', selectedDivision);
        updateApprovalRoles(selectedDivision);
    }
});

function deactivateUser(userId, userName) {
    if (!confirm('Apakah Anda yakin ingin menonaktifkan user "' + userName + '"?\n\nUser tidak akan dapat login ke sistem setelah dinonaktifkan.')) {
        return;
    }
    
    $.ajax({
        url: '<?= base_url('admin/advanced-users/deactivate-user') ?>/' + userId,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                $('#usersTable').DataTable().ajax.reload();
            } else {
                alert('Error: ' + (response.message || 'Gagal menonaktifkan user'));
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            alert('Error: ' + (response.message || 'Terjadi kesalahan saat menonaktifkan user'));
        }
    });
}

function confirmDeleteUser(userId, userName) {
    if (confirm('Are you sure you want to delete user "' + userName + '"?\n\nThis action cannot be undone!')) {
        $.ajax({
            url: '<?= base_url('admin/advanced-users/delete') ?>/' + userId,
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('User deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    alert('Error: ' + response.message);
                } catch (e) {
                    alert('An error occurred while deleting the user.');
                }
            }
        });
    }
}

function exportUsers() {
    window.location.href = '<?= base_url('admin/advanced-users/export') ?>';
}

function cleanExpiredPermissions() {
    if (confirm('Are you sure you want to clean all expired permissions?\n\nThis will remove permissions that have expired or are no longer valid.')) {
        $.post('<?= base_url('admin/advanced-users/clean-expired') ?>', {}, function(response) {
            if (response.success) {
                alert('Expired permissions cleaned successfully.\n\nRemoved: ' + (response.removed_count || 0) + ' permissions');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }).fail(function() {
            alert('An error occurred while cleaning expired permissions.');
        });
    }
}
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>