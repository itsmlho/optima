<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Quick Permission Assignment</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/advanced-users') ?>">User Management</a></li>
                    <li class="breadcrumb-item active">Quick Assign</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Assignment Interface -->
    <div class="row">
        <!-- User Selection -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Select User</h5>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="userSearch" placeholder="Search users...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div id="usersList" style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($users as $user): ?>
                        <div class="user-item p-2 border-bottom cursor-pointer" data-user-id="<?= $user['id'] ?>" onclick="selectUser(<?= $user['id'] ?>)">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-primary text-white me-2">
                                    <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <strong><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                    <br><small class="text-muted"><?= esc($user['email']) ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permission Assignment -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Permission Assignment</h5>
                    <small class="text-muted">Select a user to assign permissions</small>
                </div>
                <div class="card-body" id="permissionAssignmentArea">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-user-plus fa-3x mb-3"></i>
                        <p>Please select a user from the left panel to start assigning permissions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Assign Templates -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Assignment Templates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card template-card" onclick="applyTemplate('admin')">
                            <div class="card-body text-center">
                                <i class="fas fa-user-shield fa-2x text-danger mb-2"></i>
                                <h6>Administrator</h6>
                                <small class="text-muted">Full system access</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card template-card" onclick="applyTemplate('manager')">
                            <div class="card-body text-center">
                                <i class="fas fa-user-tie fa-2x text-primary mb-2"></i>
                                <h6>Manager</h6>
                                <small class="text-muted">Management permissions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card template-card" onclick="applyTemplate('employee')">
                            <div class="card-body text-center">
                                <i class="fas fa-user fa-2x text-success mb-2"></i>
                                <h6>Employee</h6>
                                <small class="text-muted">Basic user permissions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card template-card" onclick="applyTemplate('readonly')">
                            <div class="card-body text-center">
                                <i class="fas fa-eye fa-2x text-info mb-2"></i>
                                <h6>Read Only</h6>
                                <small class="text-muted">View-only access</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
.user-item:hover {
    background-color: #f8f9fa;
}

.user-item.selected {
    background-color: #e3f2fd;
    border-color: #2196f3;
}

.template-card {
    cursor: pointer;
    transition: transform 0.2s;
    margin-bottom: 1rem;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.permission-toggle {
    margin-bottom: 0.5rem;
}

.quick-actions {
    position: sticky;
    top: 20px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let selectedUserId = null;

$(document).ready(function() {
    // User search functionality
    $('#userSearch').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('.user-item').each(function() {
            var userName = $(this).text().toLowerCase();
            if (userName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});

function selectUser(userId) {
    selectedUserId = userId;
    
    // Update UI to show selected user
    $('.user-item').removeClass('selected');
    $('[data-user-id="' + userId + '"]').addClass('selected');
    
    // Load user's current permissions
    loadUserPermissions(userId);
}

function loadUserPermissions(userId) {
    $('#permissionAssignmentArea').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    
    $.get('<?= base_url('admin/advanced-users/user-matrix') ?>/' + userId, function(response) {
        if (response.success) {
            renderQuickAssignInterface(response.data);
        } else {
            $('#permissionAssignmentArea').html('<div class="alert alert-danger">Error loading user permissions.</div>');
        }
    });
}

function renderQuickAssignInterface(userData) {
    var html = `
        <div class="row">
            <div class="col-md-8">
                <h6>Quick Permission Toggles</h6>
                <div class="permission-toggles">`;
    
    // Common permissions for quick assignment
    var quickPermissions = [
        { key: 'users.view', name: 'View Users', module: 'users' },
        { key: 'users.create', name: 'Create Users', module: 'users' },
        { key: 'users.edit', name: 'Edit Users', module: 'users' },
        { key: 'users.delete', name: 'Delete Users', module: 'users' },
        { key: 'reports.view', name: 'View Reports', module: 'reports' },
        { key: 'reports.export', name: 'Export Reports', module: 'reports' },
        { key: 'settings.view', name: 'View Settings', module: 'settings' },
        { key: 'settings.edit', name: 'Edit Settings', module: 'settings' }
    ];
    
    quickPermissions.forEach(function(perm) {
        var isGranted = userData.permissions && userData.permissions.effective_permissions && userData.permissions.effective_permissions[perm.key];
        
        html += `
            <div class="form-check form-switch permission-toggle">
                <input class="form-check-input" type="checkbox" id="quick_${perm.key}" 
                       ${isGranted ? 'checked' : ''} 
                       onchange="togglePermission('${perm.key}', this.checked)">
                <label class="form-check-label" for="quick_${perm.key}">
                    <strong>${perm.name}</strong>
                    <br><small class="text-muted">${perm.key}</small>
                </label>
            </div>`;
    });
    
    html += `
                </div>
            </div>
            <div class="col-md-4">
                <div class="quick-actions">
                    <h6>Quick Actions</h6>
                    <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="$('#templateModal').modal('show')">
                        <i class="fas fa-magic"></i> Use Template
                    </button>
                    <button class="btn btn-outline-success btn-sm w-100 mb-2" onclick="saveChanges()">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button class="btn btn-outline-warning btn-sm w-100 mb-2" onclick="resetPermissions()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <hr>
                    <h6>User Info</h6>
                    <p><strong>${userData.user.first_name} ${userData.user.last_name}</strong></p>
                    <p class="text-muted small">${userData.user.email}</p>
                </div>
            </div>
        </div>`;
    
    $('#permissionAssignmentArea').html(html);
}

function togglePermission(permissionKey, granted) {
    if (!selectedUserId) {
        OptimaNotify.warning('Please select a user first.');
        return;
    }
    
    $.post('<?= base_url('admin/advanced-users/quick-assign-permission') ?>', {
        user_id: selectedUserId,
        permission_key: permissionKey,
        granted: granted ? 1 : 0,
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    }, function(response) {
        if (response.success) {
            OptimaNotify.success('Permission updated successfully');
        } else {
            OptimaNotify.error('Error: ' + response.message);
            $('#quick_' + permissionKey).prop('checked', !granted);
        }
    });
}

function applyTemplate(template) {
    if (!selectedUserId) {
        OptimaNotify.warning('Please select a user first.');
        return;
    }
    
    // Template definitions
    var templates = {
        admin: ['users.view', 'users.create', 'users.edit', 'users.delete', 'reports.view', 'reports.export', 'settings.view', 'settings.edit'],
        manager: ['users.view', 'users.create', 'users.edit', 'reports.view', 'reports.export', 'settings.view'],
        employee: ['users.view', 'reports.view'],
        readonly: ['users.view', 'reports.view']
    };
    
    var templatePermissions = templates[template] || [];
    
    // Reset all toggles first
    $('.permission-toggle input[type="checkbox"]').prop('checked', false);
    
    // Apply template permissions
    templatePermissions.forEach(function(permKey) {
        $('#quick_' + permKey).prop('checked', true);
        togglePermission(permKey, true);
    });
    
    $('#templateModal').modal('hide');
    showNotification('Template applied successfully', 'success');
}

function saveChanges() {
    showNotification('All changes have been saved automatically', 'info');
}

function resetPermissions() {
    OptimaConfirm.danger({
        title: 'Reset Permissions?',
        text: 'Semua permission user ini akan direset.',
        icon: 'warning',
        confirmText: 'Ya, Reset!',
        cancelText: (typeof window.lang === 'function' ? window.lang('cancel') : 'Cancel'),
        confirmButtonColor: '#dc3545',
        onConfirm: function() {
            $('.permission-toggle input[type="checkbox"]').each(function() {
                if ($(this).is(':checked')) {
                    $(this).prop('checked', false);
                    var permKey = $(this).attr('id').replace('quick_', '');
                    togglePermission(permKey, false);
                }
            });
        }
    });
}

function showNotification(message, type) {
    if (window.OptimaNotify && typeof OptimaNotify[type] === 'function') {
        OptimaNotify[type](message);
    } else if (window.OptimaPro) {
        OptimaPro.showNotification(message, type);
    }
}
</script>
<?= $this->endSection() ?>
