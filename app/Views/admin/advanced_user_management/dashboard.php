<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">User Management Dashboard</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">User Management Overview</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-primary">
                    <i class="fas fa-users"></i> Manage Users
                </a>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0"><?= $stats['total_users'] ?></h3>
                            <p class="mb-0">Total Users</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0"><?= $stats['active_users'] ?></h3>
                            <p class="mb-0">Active Users</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0"><?= $stats['total_roles'] ?></h3>
                            <p class="mb-0">Roles</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-user-tag fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0"><?= $stats['total_permissions'] ?></h3>
                            <p class="mb-0">Permissions</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-key fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Users -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2">
                                                <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <strong><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                                <br><small class="text-muted"><?= esc($user['email']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($user['roles'])): ?>
                                            <?php foreach (array_slice($user['roles'], 0, 2) as $role): ?>
                                                <span class="badge bg-primary me-1"><?= esc($role['name']) ?></span>
                                            <?php endforeach; ?>
                                            <?php if (count($user['roles']) > 2): ?>
                                                <span class="text-muted">+<?= count($user['roles']) - 2 ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No roles</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $user['status'] == 'active' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= date('M d', strtotime($user['created_at'])) ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Division Distribution -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Division Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="divisionChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Permission Usage -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Permission Usage Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Permission</th>
                                    <th>Module</th>
                                    <th>Users with Access</th>
                                    <th>Usage %</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($permissionUsage as $perm): ?>
                                <tr>
                                    <td><?= esc($perm['key']) ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc(explode('.', $perm['key'])[0]) ?></span>
                                    </td>
                                    <td><?= $perm['user_count'] ?></td>
                                    <td>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $perm['usage_percentage'] ?>%"></div>
                                        </div>
                                        <small><?= number_format($perm['usage_percentage'], 1) ?>%</small>
                                    </td>
                                    <td>
                                        <?php if ($perm['role_based'] > $perm['custom']): ?>
                                            <span class="badge bg-info">Role-based</span>
                                        <?php elseif ($perm['custom'] > 0): ?>
                                            <span class="badge bg-warning">Custom</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Mixed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('admin/advanced-users/create') ?>" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create New User
                        </a>
                        <a href="<?= base_url('admin/advanced-users/matrix') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-table"></i> Permission Matrix
                        </a>
                        <a href="<?= base_url('admin/advanced-users/quick-assign') ?>" class="btn btn-primary">
                            <i class="fas fa-bolt"></i> Quick Assign
                        </a>
                        <button class="btn btn-warning" onclick="bulkActions()">
                            <i class="fas fa-layer-group"></i> Bulk Actions
                        </button>
                        <a href="<?= base_url('admin/advanced-users/export') ?>" class="btn btn-secondary">
                            <i class="fas fa-download"></i> Export Data
                        </a>
                    </div>

                    <hr>

                    <h6>System Health</h6>
                    <div class="mb-2">
                        <small class="text-muted">Users without roles:</small>
                        <span class="float-end badge bg-warning"><?= $stats['users_without_roles'] ?></span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Inactive users:</small>
                        <span class="float-end badge bg-danger"><?= $stats['inactive_users'] ?></span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Custom permissions:</small>
                        <span class="float-end badge bg-info"><?= $stats['custom_permissions_count'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Division Distribution Chart
    const divisionData = <?= json_encode($divisionStats) ?>;
    
    const ctx = document.getElementById('divisionChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: divisionData.map(d => d.name),
            datasets: [{
                data: divisionData.map(d => d.user_count),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                    '#FF6384',
                    '#C9CBCF'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

function bulkActions() {
    // Open bulk actions modal or redirect to bulk page
    window.location.href = '<?= base_url('admin/advanced-users') ?>?tab=bulk';
}
</script>
<?= $this->endSection() ?>
