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
                                                <?= esc($permission['display_name'] ?? $permission['key_name'] ?? '') ?>
                                                <small class="text-muted d-block"><?= esc($permission['key_name'] ?? '') ?></small>
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
                    
                    <!-- Service Area & Branch Access (shown only for Service division) -->
                    <div id="approveServiceAccessSection" class="card mb-3 d-none">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-wrench me-2"></i>Service Area & Branch Access <small class="text-light">(Khusus Divisi Service)</small></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="approveAreaType" class="form-label"><strong>Area Type</strong></label>
                                    <select class="form-select" id="approveAreaType" name="area_type">
                                        <option value="">Select Area Type</option>
                                        <option value="CENTRAL">CENTRAL (Admin Service Pusat)</option>
                                        <option value="BRANCH">BRANCH (Admin Service Area)</option>
                                    </select>
                                </div>
                            </div>
                            <div id="approveCentralSection" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="approveDepartmentScope" class="form-label">Department Scope</label>
                                        <select class="form-select" id="approveDepartmentScope" name="department_scope">
                                            <option value="">Select Department</option>
                                            <option value="ELECTRIC">ELECTRIC</option>
                                            <option value="DIESEL_GASOLINE">DIESEL + GASOLINE</option>
                                            <option value="ALL">ALL DEPARTMENTS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="approveBranchSection" style="display:none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Pilih service area untuk branch access</small>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="approveBtnSelectAreas">
                                        <i class="fas fa-map-marked-alt me-1"></i>Select Areas
                                    </button>
                                </div>
                                <div id="approveSelectedAreasDisplay" class="border rounded p-2 bg-light">
                                    <span class="text-muted">No areas selected</span>
                                </div>
                                <input type="hidden" id="approveSelectedAreaIds" name="service_area_ids_json">
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

<!-- Service Areas Selection Modal for Approve (inside content section) -->
<div class="modal fade" id="approveServiceAreasModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-map-marked-alt me-2"></i>Select Service Areas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="approveAreaSearch" placeholder="Search areas...">
                    </div>
                </div>
                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                    <div id="approveAreasList" class="row">
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2">Loading areas...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="approveSaveServiceAreas">
                    <i class="fas fa-save me-2"></i>Save Selection
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// ============================================================
// CSRF Helper — always reads the latest token from cookie
// CI4 sets the token value in cookie: csrf_cookie_name
// Token field name is:  csrf_test_name
// ============================================================
function getCsrfToken() {
    if (window.csrfToken) return window.csrfToken;
    const name = 'csrf_cookie_name=';
    const cookies = decodeURIComponent(document.cookie).split(';');
    for (let i = 0; i < cookies.length; i++) {
        const c = cookies[i].trim();
        if (c.indexOf(name) === 0) return c.substring(name.length);
    }
    // Last resort: read from meta tag set by base layout
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

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
            data: function() {
                const d = { division_id: divisionId, role_id: roleId };
                // Service area fields (only when Service division)
                const areaType = $('#approveAreaType').val();
                if (areaType) {
                    d.area_type = areaType;
                    if (areaType === 'CENTRAL') {
                        d.department_scope = $('#approveDepartmentScope').val();
                    } else if (areaType === 'BRANCH') {
                        d.service_area_ids_json = JSON.stringify(approveSelectedServiceAreasIds);
                    }
                }
                d['<?= csrf_token() ?>'] = getCsrfToken();
                return d;
            }(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            success: function(response) {
                if (response.success) {
                    bootstrap.Modal.getInstance(document.getElementById('approveUserModal')).hide();
                    alertSwal('success', response.message, 'User Diaktifkan!');
                    $('#usersTable').DataTable().ajax.reload(null, false);
                } else {
                    alertSwal('error', response.message || 'Gagal mengaktifkan user', 'Error');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                alertSwal('error', response.message || 'Terjadi kesalahan saat mengaktifkan user', 'Error');
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
                d['<?= csrf_token() ?>'] = getCsrfToken();
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
                OptimaNotify.error(msg);
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
            OptimaNotify.warning('Please select at least one user.');
            return;
        }
        
        if (selectedPermissions.length === 0) {
            OptimaNotify.warning('Please select at least one permission.');
            return;
        }
        
        var csrfData = {};
        csrfData['<?= csrf_token() ?>'] = getCsrfToken();
        $.ajax({
            url: '<?= base_url('admin/advanced-users/bulk-assign-permissions') ?>',
            method: 'POST',
            data: $.extend({
                user_ids: selectedUsers,
                permission_ids: selectedPermissions,
                division_id: formData.get('division_id'),
                granted: formData.get('action') === 'grant' ? 1 : 0
            }, csrfData),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertSwal('success', 'Bulk permission berhasil diterapkan.');
                    $('#bulkPermissionModal').modal('hide');
                    location.reload();
                } else {
                    alertSwal('error', response.message, 'Error');
                }
            },
            error: function(xhr) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    alertSwal('error', response.message);
                } catch (e) {
                    alertSwal('error', 'Terjadi kesalahan saat memproses permintaan.');
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

// Division-Role mapping from actual DB data with division_id
const allRoles = <?= json_encode(array_values(array_map(function($r) {
    return ['id' => (string)$r['id'], 'name' => $r['name'], 'division_id' => (string)($r['division_id'] ?? '')];
}, $roles ?? []))) ?>;

// Approve modal: service area state
let approveSelectedServiceAreasIds = [];
let approveServiceAreasData = [];

function loadApproveServiceAreas() {
    $('#approveAreasList').html('<div class="text-center p-3"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2">Loading...</span></div>');
    $.ajax({
        url: '<?= base_url('admin/advanced-users/get-service-areas') ?>',
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                approveServiceAreasData = response.data;
                renderApproveServiceAreas(approveServiceAreasData);
            } else {
                $('#approveAreasList').html('<div class="col-12 text-center p-3"><span class="text-muted">No areas available</span></div>');
            }
        },
        error: function() {
            $('#approveAreasList').html('<div class="col-12 text-center p-3"><div class="alert alert-danger">Gagal memuat data areas</div></div>');
        }
    });
}

function renderApproveServiceAreas(areas) {
    let html = '';
    areas.forEach(area => {
        const isChecked = approveSelectedServiceAreasIds.includes(area.id) ? 'checked' : '';
        const selectedClass = isChecked ? 'border-primary bg-light' : '';
        html += `<div class="col-md-6 mb-2"><div class="card ${selectedClass} approve-area-item" style="cursor:pointer;">
            <div class="card-body p-3"><div class="form-check">
                <input class="form-check-input" type="checkbox" value="${area.id}" id="apprvArea_${area.id}" ${isChecked}>
                <label class="form-check-label" for="apprvArea_${area.id}"><strong>${area.area_name}</strong><br><small class="text-muted">${area.area_code} — ${area.area_type}</small></label>
            </div></div></div></div>`;
    });
    if (!html) html = '<div class="col-12 text-center p-3"><span class="text-muted">No areas found</span></div>';
    $('#approveAreasList').html(html);
    $('.approve-area-item').on('click', function() {
        const cb = $(this).find('input[type="checkbox"]');
        cb.prop('checked', !cb.prop('checked'));
        $(this).toggleClass('border-primary bg-light', cb.prop('checked'));
    });
}

function confirmApproveAreaSelection() {
    const ids = [];
    $('#approveAreasList input[type="checkbox"]:checked').each(function() {
        ids.push(parseInt($(this).val()));
    });
    approveSelectedServiceAreasIds = ids;
    const count = ids.length;
    $('#approveSelectedAreasDisplay').html(count > 0
        ? `<span class="badge bg-primary">${count} area(s) selected</span>`
        : '<span class="text-muted">No areas selected</span>');
    $('#approveSelectedAreaIds').val(JSON.stringify(ids));
    bootstrap.Modal.getInstance(document.getElementById('approveServiceAreasModal')).hide();
}

$(document).on('click', '#approveBtnSelectAreas', function() {
    loadApproveServiceAreas();
    new bootstrap.Modal(document.getElementById('approveServiceAreasModal')).show();
});

$(document).on('click', '#approveSaveServiceAreas', confirmApproveAreaSelection);

$(document).on('input', '#approveAreaSearch', function() {
    const q = $(this).val().toLowerCase();
    const filtered = approveServiceAreasData.filter(a =>
        a.area_name.toLowerCase().includes(q) || a.area_code.toLowerCase().includes(q));
    renderApproveServiceAreas(filtered);
});

$(document).on('change', '#approveAreaType', function() {
    const val = $(this).val();
    if (val === 'CENTRAL') {
        $('#approveCentralSection').show();
        $('#approveBranchSection').hide();
    } else if (val === 'BRANCH') {
        $('#approveCentralSection').hide();
        $('#approveBranchSection').show();
    } else {
        $('#approveCentralSection, #approveBranchSection').hide();
    }
});

// Function to update roles based on division (filter by division_id)
function updateApprovalRoles(selectedDivision) {
    console.log('updateApprovalRoles called with division:', selectedDivision);
    const roleSelect = $('#approveRoleId');
    roleSelect.empty();
    roleSelect.append('<option value="">Select Role</option>');
    
    if (!selectedDivision) {
        console.log('No division selected');
        return;
    }
    
    // Filter roles by division_id
    const divisionStr = selectedDivision.toString();
    const filteredRoles = allRoles.filter(role => role.division_id === divisionStr);
    
    console.log('Filtered roles for division', divisionStr, ':', filteredRoles);
    
    if (filteredRoles.length === 0) {
        console.warn('No roles found for division:', divisionStr);
        roleSelect.append('<option value="" disabled>No roles available for this division</option>');
    } else {
        filteredRoles.forEach(role => {
            roleSelect.append('<option value="' + role.id + '">' + role.name + '</option>');
        });
    }
    
    roleSelect.trigger('change');
}

function approveUser(userId, userName) {
    // Load user data and show modal
    $.ajax({
        url: '<?= base_url('admin/advanced-users/get-user-for-approval') ?>/' + userId,
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },

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

                // Reset service area section
                approveSelectedServiceAreasIds = [];
                $('#approveServiceAccessSection').addClass('d-none');
                $('#approveAreaType').val('');
                $('#approveDepartmentScope').val('');
                $('#approveCentralSection, #approveBranchSection').hide();
                $('#approveSelectedAreasDisplay').html('<span class="text-muted">No areas selected</span>');

                // Pre-populate service access if user already has it
                if (response.user_service_access) {
                    const sa = response.user_service_access;
                    $('#approveAreaType').val(sa.area_type || '');
                    if (sa.area_type === 'CENTRAL') {
                        $('#approveDepartmentScope').val(sa.department_scope || '');
                    } else if (sa.area_type === 'BRANCH' && response.user_branch_access && response.user_branch_access.branch_ids) {
                        approveSelectedServiceAreasIds = response.user_branch_access.branch_ids.map(Number);
                        const cnt = approveSelectedServiceAreasIds.length;
                        $('#approveSelectedAreasDisplay').html(cnt > 0
                            ? `<span class="badge bg-primary">${cnt} area(s) selected</span>`
                            : '<span class="text-muted">No areas selected</span>');
                    }
                }
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('approveUserModal'));
                modal.show();
            } else {
                OptimaNotify.error('Error: ' + (response.message || 'Gagal memuat data user'));
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            OptimaNotify.error('Error: ' + (response.message || 'Terjadi kesalahan saat memuat data user'));
        }
    });
}

// Handle division change for approval modal
$(document).on('change', '#approveDivisionId', function() {
    console.log('Approval division changed to:', $(this).val());
    const selectedDivision = $(this).val();
    updateApprovalRoles(selectedDivision);
    // Show/hide service section based on selected division name
    const divName = $(this).find('option:selected').text().toLowerCase();
    if (divName.includes('service')) {
        $('#approveServiceAccessSection').removeClass('d-none');
        // Show sub-section based on already-selected area_type
        const at = $('#approveAreaType').val();
        if (at === 'CENTRAL') { $('#approveCentralSection').show(); $('#approveBranchSection').hide(); }
        else if (at === 'BRANCH') { $('#approveCentralSection').hide(); $('#approveBranchSection').show(); }
    } else {
        $('#approveServiceAccessSection').addClass('d-none');
        $('#approveAreaType').val('');
        $('#approveCentralSection, #approveBranchSection').hide();
    }
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
    OptimaConfirm.generic({
        title: 'Nonaktifkan User',
        text: `Apakah Anda yakin ingin menonaktifkan user "${userName}"? User tidak akan dapat login ke sistem setelah dinonaktifkan.`,
        icon: 'warning',
        confirmText: '<i class="fas fa-user-slash me-1"></i>Ya, Nonaktifkan',
        confirmButtonColor: '#fd7e14',
        onConfirm: function() {
            $.ajax({
                url: '<?= base_url('admin/advanced-users/deactivate-user') ?>/' + userId,
                type: 'POST',
                dataType: 'json',
                data: (function() {
                    var d = {};
                    d['<?= csrf_token() ?>'] = getCsrfToken();
                    return d;
                })(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                success: function(response) {
                    if (response.success) {
                        alertSwal('success', response.message, 'User Dinonaktifkan');
                        $('#usersTable').DataTable().ajax.reload();
                    } else {
                        alertSwal('error', response.message || 'Gagal menonaktifkan user', 'Error');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    alertSwal('error', response.message || 'Terjadi kesalahan saat menonaktifkan user', 'Error');
                }
            });
        }
    });
}

function confirmDeleteUser(userId, userName) {
    OptimaConfirm.danger({
        title: 'Hapus User',
        text: `Apakah Anda yakin ingin menghapus user "${userName}"? Tindakan ini tidak dapat dibatalkan!`,
        onConfirm: function() {
            $.ajax({
                url: '<?= base_url('admin/advanced-users/delete') ?>/' + userId,
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alertSwal('success', 'User berhasil dihapus!');
                        location.reload();
                    } else {
                        alertSwal('error', response.message, 'Error');
                    }
                },
                error: function(xhr) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        alertSwal('error', response.message);
                    } catch (e) {
                        alertSwal('error', 'Terjadi kesalahan saat menghapus user.');
                    }
                }
            });
        }
    });
}

function exportUsers() {
    window.location.href = '<?= base_url('admin/advanced-users/export') ?>';
}

function cleanExpiredPermissions() {
    OptimaConfirm.generic({
        title: 'Bersihkan Permission Kadaluarsa',
        text: 'Ini akan menghapus semua permission yang sudah kadaluarsa atau tidak lagi valid. Lanjutkan?',
        icon: 'warning',
        confirmText: '<i class="fas fa-broom me-1"></i>Ya, Bersihkan',
        confirmButtonColor: '#fd7e14',
        onConfirm: function() {
            $.post('<?= base_url('admin/advanced-users/clean-expired') ?>', { '<?= csrf_token() ?>': getCsrfToken() }, function(response) {
                if (response.success) {
                    alertSwal('success', 'Permission kadaluarsa berhasil dibersihkan.\nTerhapus: ' + (response.removed_count || 0) + ' permissions');
                    location.reload();
                } else {
                    alertSwal('error', response.message, 'Error');
                }
            }).fail(function() {
                alertSwal('error', 'Terjadi kesalahan saat membersihkan permission kadaluarsa.');
            });
        }
    });
}
</script>
<?= $this->endSection() ?>