<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-cogs me-2"></i><?= lang('Admin.system_administration') ?>
                    </h1>
                    <p class="text-muted mb-0"><?= lang('Admin.manage_system_settings') ?></p>
                </div>
                <div>
                    <button class="btn btn-outline-primary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Cards - Professional Standard -->

    <div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-success-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-server stat-icon text-success"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-system-status">
                            <span class="badge bg-<?= ($system_status['database_status'] ?? '') === 'Connected' ? 'success' : 'danger' ?>">
                                <?= ($system_status['database_status'] ?? '') === 'Connected' ? lang('Admin.online') : lang('Admin.offline') ?>
                            </span>
                        </div>
                        <div class="text-muted">System Status</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-users"><?= $total_users ?? 0 ?></div>
                        <div class="text-muted"><?= lang('Admin.total_users') ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-info-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-database stat-icon text-info"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-database-size"><?= $system_status['database_size'] ?? '0 MB' ?></div>
                        <div class="text-muted"><?= lang('Admin.database_size') ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-speedometer2 stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-system-load"><?= $system_status['system_load'] ?? 'Low' ?></div>
                        <div class="text-muted"><?= lang('Admin.system_load') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & System Metrics -->
    <div class="row mb-4">
        <!-- Performance Metrics -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= lang('Admin.performance_metrics') ?></h6>
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted"><?= lang('Admin.average_query_time') ?></small>
                                <div class="font-weight-bold"><?= number_format($performance_metrics['query_time'] ?? 0, 3) ?>s</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted"><?= lang('Admin.page_load_time') ?></small>
                                <div class="font-weight-bold"><?= number_format($performance_metrics['page_load_time'] ?? 0, 2) ?>s</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted"><?= lang('Admin.memory_peak') ?></small>
                                <div class="font-weight-bold"><?= $performance_metrics['memory_peak'] ?? '0 MB' ?></div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted"><?= lang('Admin.slow_queries') ?></small>
                                <div class="font-weight-bold text-<?= ($performance_metrics['slow_queries'] ?? 0) > 0 ? 'danger' : 'success' ?>">
                                    <?= $performance_metrics['slow_queries'] ?? 0 ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Statistics -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success"><?= lang('Admin.cache_statistics') ?></h6>
                    <i class="fas fa-database"></i>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted"><?= lang('Admin.hit_rate') ?></small>
                                <div class="font-weight-bold text-success"><?= number_format($cache_stats['hit_rate'] ?? 0, 1) ?>%</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted"><?= lang('Admin.total_keys') ?></small>
                                <div class="font-weight-bold"><?= number_format($cache_stats['total_keys'] ?? 0) ?></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted"><?= lang('Admin.miss_rate') ?></small>
                                <div class="font-weight-bold text-warning"><?= number_format($cache_stats['miss_rate'] ?? 0, 1) ?>%</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted"><?= lang('Admin.memory_usage') ?></small>
                                <div class="font-weight-bold"><?= $cache_stats['memory_usage'] ?? '0 MB' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Queue Status & System Monitoring -->
    <div class="row mb-4">
        <!-- Queue Status -->
        <div class="col-xl-4 col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-info"><?= lang('Admin.queue_status') ?></h6>
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-2">
                            <span class="badge bg-<?= ($queue_status['status'] ?? '') === 'Active' ? 'success' : 'secondary' ?> mb-2">
                                <?= $queue_status['status'] ?? 'Unknown' ?>
                            </span>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted"><?= lang('Common.pending') ?></small>
                                <div class="font-weight-bold"><?= $queue_status['pending'] ?? 0 ?></div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted"><?= lang('Admin.failed') ?></small>
                                <div class="font-weight-bold text-danger"><?= $queue_status['failed'] ?? 0 ?></div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Today</small>
                                <div class="font-weight-bold text-success"><?= $queue_status['completed_today'] ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Resources -->
        <div class="col-xl-8 col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning"><?= lang('Admin.system_resources') ?></h6>
                    <i class="fas fa-server"></i>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-6">
                            <div class="mb-3">
                                <small class="text-muted">Memory Usage</small>
                                <div class="progress mb-1" style="height: 10px;">
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: <?= $system_status['memory_usage'] ?? 0 ?>%"></div>
                                </div>
                                <small><?= number_format($system_status['memory_usage'] ?? 0, 1) ?>%</small>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="mb-3">
                                <small class="text-muted"><?= lang('Admin.storage_usage') ?></small>
                                <div class="progress mb-1" style="height: 10px;">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: <?= $system_status['storage_usage'] ?? 0 ?>%"></div>
                                </div>
                                <small><?= number_format($system_status['storage_usage'] ?? 0, 1) ?>%</small>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="mb-3">
                                <small class="text-muted">System Uptime</small>
                                <div class="font-weight-bold"><?= $system_status['uptime'] ?? 'Unknown' ?></div>
                            </div>
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
                        <i class="fas fa-users me-2"></i><?= lang('Admin.user_management') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted"><?= lang('Admin.manage_users_roles') ?></p>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('/admin/advanced-users') ?>" class="btn btn-primary">
                            <i class="fas fa-users me-2"></i><?= lang('Admin.user_management') ?>
                        </a>
                        <a href="<?= base_url('/admin/roles') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-user-tag me-2"></i><?= lang('Admin.role_management') ?>
                        </a>
                        <a href="<?= base_url('/admin/permissions') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-key me-2"></i><?= lang('Admin.permission_management') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-bar me-2"></i><?= lang('Admin.system_monitoring') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted"><?= lang('Admin.monitor_activities') ?></p>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('/admin/activity-log') ?>" class="btn btn-success">
                            <i class="fas fa-history me-2"></i><?= lang('Admin.activity_log') ?>
                        </a>
                        <a href="<?= base_url('/notifications') ?>" class="btn btn-outline-success">
                            <i class="fas fa-heartbeat me-2"></i><?= lang('Admin.notification_center') ?>
                        </a>
                        <a href="<?= base_url('notifications/admin') ?>" class="btn btn-outline-success">
                            <i class="fas fa-bell me-2"></i><?= lang('Admin.notification_rules') ?>
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
                        <i class="fas fa-clock me-2"></i><?= lang('Admin.recent_activities') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><?= lang('Admin.time') ?></th>
                                    <th><?= lang('App.user') ?></th>
                                    <th><?= lang('Common.action') ?></th>
                                    <th><?= lang('Admin.description') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activities as $activity): ?>
                                <tr>
                                    <td><?= isset($activity['created_at']) ? date('Y-m-d H:i:s', strtotime($activity['created_at'])) : date('Y-m-d H:i:s') ?></td>
                                    <td><?= esc($activity['user_name'] ?? lang('Admin.unknown_user')) ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($activity['action_type'] ?? 'UNKNOWN') === 'CREATE' ? 'success' : (($activity['action_type'] ?? 'UNKNOWN') === 'UPDATE' ? 'warning' : 'danger') ?>">
                                            <?= esc($activity['action_type'] ?? 'UNKNOWN') ?>
                                        </span>
                                    </td>
                                    <td><?= esc($activity['action_description'] ?? lang('Admin.no_description')) ?></td>
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
