<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .card .card-header h5 { color: #495057; }
    .permission-grid { max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 1rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?? 'Edit User' ?></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/advanced-users') ?>">User Management</a></li>
                    <li class="breadcrumb-item active">Edit User</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <!-- Edit User Form -->
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Edit User: <?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h4>
        </div>
        <div class="card-body">
            
            <!-- Menampilkan Pesan Error Validasi -->
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/advanced-users/update/' . $user['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <!-- Kolom Kiri: Informasi Dasar -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h5 class="mb-0">Basic Information</h5></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" name="first_name" value="<?= old('first_name', $user['first_name']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" name="last_name" value="<?= old('last_name', $user['last_name']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username *</label>
                                    <input type="text" class="form-control" name="username" value="<?= old('username', $user['username']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" value="<?= old('email', $user['email']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" name="phone" value="<?= old('phone', $user['phone'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="password">
                                    <small class="form-text text-muted">Leave blank to keep current password</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password">
                                </div>
                            </div>
                        </div>
                         <!-- RBAC Divisions Section -->
                        <div class="card mb-3 mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Divisions</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($allDivisions as $division): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="divisions[]" value="<?= $division['id'] ?>" id="division<?= $division['id'] ?>"
                                        <?= in_array($division['id'], old('divisions', array_column($userDivisions ?? [], 'id'))) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="division<?= $division['id'] ?>"><?= esc($division['name']) ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    

                    <!-- Kolom Kanan: Peran & Izin -->
                    <div class="col-md-6">
                        <!-- RBAC Roles Section -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Roles</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($allRoles as $role): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $role['id'] ?>" id="role<?= $role['id'] ?>"
                                        <?= in_array($role['id'], old('roles', array_column($userRoles ?? [], 'id'))) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="role<?= $role['id'] ?>"> <?= esc($role['name']) ?>
                                        
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- RBAC Permissions Override Section -->
                        <!-- Permissions -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Custom Permissions</h5>
                                <small class="text-muted">Additional permissions beyond roles</small>
                            </div>
                            <div class="card-body" style="max-height: 410px; overflow-y: auto;">
                                <?php 
                                // Buat array ID permissions user yang sudah ada
                                $existingPermissionIds = [];
                                if (isset($userPermissions) && !empty($userPermissions)) {
                                    if (is_array($userPermissions) && !empty($userPermissions) && isset($userPermissions[0]) && is_array($userPermissions[0])) {
                                        if (isset($userPermissions[0]['id'])) {
                                            $existingPermissionIds = array_column($userPermissions, 'id');
                                        } elseif (isset($userPermissions[0]['permission_id'])) {
                                            $existingPermissionIds = array_column($userPermissions, 'permission_id');
                                        }
                                    } else {
                                        $existingPermissionIds = $userPermissions;
                                    }
                                }
                                ?>
                                
                                <?php if (!empty($groupedPermissions)): ?>
                                    <?php foreach ($groupedPermissions as $module => $modulePermissions): ?>
                                    <div class="mb-3">
                                        <h6 class="text-primary border-bottom pb-1"><?= ucfirst($module) ?></h6>
                                        <?php foreach ($modulePermissions as $permission): ?>
                                        <div class="form-check ms-2">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $permission['id'] ?>" id="perm<?= $permission['id'] ?>"
                                                <?= in_array($permission['id'], old('permissions', $existingPermissionIds)) ? 'checked' : '' ?> >
                                            <label class="form-check-label" for="perm<?= $permission['id'] ?>">
                                                <?= esc($permission['description'] ?? $permission['name']) ?>
                                                <small class="text-muted d-block"><?= esc($permission['key']) ?></small>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php if (!empty($allPermissions)): ?>
                                        <?php if (is_array($allPermissions) && isset($allPermissions[0]) && is_array($allPermissions[0])): ?>
                                            <!-- Handle flat array of permissions -->
                                            <?php foreach ($allPermissions as $permission): ?>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                        value="<?= $permission['id'] ?>" id="perm_<?= $permission['id'] ?>"
                                                        <?= in_array($permission['id'], old('permissions', $existingPermissionIds)) ? 'checked' : '' ?> >
                                                    <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                                                        <?= esc($permission['key'] ?? $permission['name']) ?>
                                                        <small class="text-muted d-block"><?= esc($permission['description'] ?? '') ?></small>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <!-- Handle grouped permissions -->
                                            <?php foreach ($allPermissions as $module => $permissionList): ?>
                                                <?php if (is_array($permissionList)): ?>
                                                    <div class="mb-3">
                                                        <h6 class="text-capitalize fw-bold text-primary"><?= esc($module) ?></h6>
                                                        <?php foreach ($permissionList as $permission): ?>
                                                            <div class="form-check ms-3">
                                                                <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                                    value="<?= $permission['id'] ?>" id="perm_<?= $permission['id'] ?>"
                                                                    <?= in_array($permission['id'], old('permissions', $existingPermissionIds)) ? 'checked' : '' ?> >
                                                                <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                                                                    <?= esc($permission['key'] ?? $permission['name']) ?>
                                                                    <small class="text-muted d-block"><?= esc($permission['description'] ?? '') ?></small>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted">Tidak ada izin yang tersedia.</p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12 text-end">
                        <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    // Show division head option when division is selected
    $('input[name="divisions[]"]').change(function() {
        var divisionId = $(this).val();
        var headOption = $('#head-option-' + divisionId);
        
        if ($(this).is(':checked')) {
            headOption.show();
        } else {
            headOption.hide();
            $('#head' + divisionId).prop('checked', false);
        }
    });

    // Initialize division head options for already selected divisions
    $('input[name="divisions[]"]:checked').each(function() {
        var divisionId = $(this).val();
        $('#head-option-' + divisionId).show();
    });

    // Also initialize based on current user divisions (for edit form)
    <?php if (!empty($userDivisions)): ?>
        <?php foreach ($userDivisions as $userDiv): ?>
            $('#head-option-<?= $userDiv['id'] ?>').show();
        <?php endforeach; ?>
    <?php endif; ?>

    // Form validation
    $('#editUserForm').on('submit', function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        
        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('Password and Confirm Password must match.');
            return false;
        }
        
        if (password && password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long.');
            return false;
        }
    });
});

function confirmDelete(userId) {
    $('#deleteModal').modal('show');
    
    $('#confirmDeleteBtn').off('click').on('click', function() {
        $.ajax({
            url: '<?= base_url('admin/advanced-users/delete') ?>/' + userId,
            method: 'DELETE',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    alert('User deleted successfully');
                    window.location.href = '<?= base_url('admin/advanced-users') ?>';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    alert('Error: ' + response.message);
                } catch(e) {
                    alert('Error: ' + xhr.statusText);
                }
            }
        });
        
        $('#deleteModal').modal('hide');
    });
}
</script>
<?= $this->endSection() ?>
