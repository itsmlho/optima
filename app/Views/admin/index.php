<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-cogs me-2"></i>System Administration
                    </h1>
                    <p class="text-muted mb-0">Manage system settings and configurations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                System Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge bg-success">Online</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $system_status['active_users'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Database Size
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $system_status['database_size'] ?? '0 MB' ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                System Load
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $system_status['system_load'] ?? 'Low' ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Administration Modules -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>User Management
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Manage users, roles, and permissions</p>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('/admin/advanced-users') ?>" class="btn btn-primary">
                            <i class="fas fa-users me-2"></i>User Management
                        </a>
                        <a href="<?= base_url('/admin/roles') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-user-tag me-2"></i>Role Management
                        </a>
                        <a href="<?= base_url('/admin/permissions') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-key me-2"></i>Permission Management
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-bar me-2"></i>System Monitoring
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Monitor system activities and performance</p>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('/admin/activity-log') ?>" class="btn btn-success">
                            <i class="fas fa-history me-2"></i>Activity Log
                        </a>
                        <a href="<?= base_url('/notifications') ?>" class="btn btn-outline-success">
                            <i class="fas fa-heartbeat me-2"></i>Notification Center
                        </a>
                        <a href="<?= base_url('notifications/admin') ?>" class="btn btn-outline-success">
                            <i class="fas fa-bell me-2"></i>Notification Rules
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <?php if (!empty($recent_activities)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-clock me-2"></i>Recent Activities
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activities as $activity): ?>
                                <tr>
                                    <td><?= isset($activity['created_at']) ? date('Y-m-d H:i:s', strtotime($activity['created_at'])) : date('Y-m-d H:i:s') ?></td>
                                    <td><?= esc($activity['user_name'] ?? 'Unknown User') ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($activity['action_type'] ?? 'UNKNOWN') === 'CREATE' ? 'success' : (($activity['action_type'] ?? 'UNKNOWN') === 'UPDATE' ? 'warning' : 'danger') ?>">
                                            <?= esc($activity['action_type'] ?? 'UNKNOWN') ?>
                                        </span>
                                    </td>
                                    <td><?= esc($activity['action_description'] ?? 'No description') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
