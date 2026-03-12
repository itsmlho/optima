<?php
// File ini adalah partial view yang akan dimuat oleh AJAX ke dalam tab divisi.
// Controller akan mengirimkan variabel $division_users ke view ini.
?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Pengguna</th>
                <th>Email</th>
                <th>Peran</th>
                <th>Status</th>
                <th>Permissions</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($division_users)): ?>
                <?php foreach ($division_users as $user): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary text-white me-2">
                                <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <strong><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                <br><small class="text-muted">@<?= esc($user['username']) ?></small>
                                <?php if ($user['is_head'] ?? false): ?>
                                    <br><span class="badge bg-warning"><i class="fas fa-crown"></i> Division Head</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><?= esc($user['email']) ?></td>
                    <td>
                        <?php if (!empty($user['roles'])): ?>
                            <?php foreach ($user['roles'] as $role): ?>
                                <span class="badge bg-primary me-1"><?= esc($role['name']) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">Tidak ada peran</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= $user['status'] == 'active' ? 'success' : ($user['status'] == 'inactive' ? 'warning' : 'danger') ?>">
                            <?= ucfirst($user['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($user['custom_permissions_count']) && $user['custom_permissions_count'] > 0): ?>
                            <span class="badge bg-info"><?= $user['custom_permissions_count'] ?> Custom</span>
                        <?php else: ?>
                            <span class="text-muted">Role-based</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="<?= base_url('admin/advanced-users/show/' . $user['id']) ?>" class="btn btn-outline-primary btn-sm" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-primary" onclick="viewUserMatrix(<?= $user['id'] ?>)" title="Permission Matrix">
                                <i class="fas fa-table"></i>
                            </button>
                            <a href="<?= base_url('admin/advanced-users/edit/' . $user['id']) ?>" class="btn btn-warning" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-success" onclick="quickAssignMenu(<?= $user['id'] ?>)" title="Quick Assign">
                                <i class="fas fa-bolt"></i>
                            </button>
                            <button class="btn btn-danger" onclick="confirmDeleteUser(<?= $user['id'] ?>, '<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>')" title="Delete User">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <br>Tidak ada pengguna di divisi ini.
                        <br><small>Klik "Edit User" pada tab "All Users" untuk menambahkan pengguna ke divisi ini.</small>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($division_users)): ?>
<div class="mt-3">
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <h6 class="mb-1">Division Statistics</h6>
                    <small class="text-muted">
                        Total Users: <?= count($division_users) ?> | 
                        Active: <?= count(array_filter($division_users, fn($u) => $u['is_active'] == 1)) ?> |
                        Heads: <?= count(array_filter($division_users, fn($u) => $u['is_head'] ?? false)) ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-2 justify-content-end">
                <button class="btn btn-sm btn-outline-primary" onclick="exportDivisionUsers()">
                    <i class="fas fa-download"></i> Export
                </button>
                <button class="btn btn-sm btn-outline-success" onclick="bulkAssignToDivision()">
                    <i class="fas fa-users-cog"></i> Bulk Assign
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function exportDivisionUsers() {
    // Export users in this division
    var divisionId = window.location.pathname.split('/').pop() || 'current';
    window.location.href = '<?= base_url('admin/advanced-users/export') ?>?division=' + divisionId;
}

function bulkAssignToDivision() {
    // Open bulk assign modal for this division
    if (window.OptimaNotify) OptimaNotify.info('Bulk assignment feature - will be implemented to show users not in this division for bulk addition');
    else alert('Bulk assignment feature - will be implemented to show users not in this division for bulk addition');
}

// Add functions that are called from the main index.php but need to work in this context
function viewUserMatrix(userId) {
    if (typeof parent !== 'undefined' && parent.viewUserMatrix) {
        parent.viewUserMatrix(userId);
    } else {
        window.open('<?= base_url('admin/advanced-users/show') ?>/' + userId, '_blank');
    }
}

function quickAssignMenu(userId) {
    if (typeof parent !== 'undefined' && parent.quickAssignMenu) {
        parent.quickAssignMenu(userId);
    } else {
        window.open('<?= base_url('admin/advanced-users/quick-assign') ?>?user=' + userId, '_blank');
    }
}

function confirmDeleteUser(userId, userName) {
    if (typeof parent !== 'undefined' && parent.confirmDeleteUser) {
        parent.confirmDeleteUser(userId, userName);
    } else {
        Swal.fire({
            title: 'Hapus User?',
            text: `User "${userName}" akan dihapus. Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '<?= base_url('admin/advanced-users/delete') ?>/' + userId,
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        OptimaNotify.success('User berhasil dihapus');
                        location.reload();
                    } else {
                        OptimaNotify.error('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    OptimaNotify.error('Terjadi kesalahan saat menghapus user.');
                }
            });
        });
    }
}
</script>
<?php endif; ?>
