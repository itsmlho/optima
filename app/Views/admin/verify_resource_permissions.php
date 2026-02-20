<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Resource Permissions Verification</h6>
                </div>
                <div class="card-body">
                    <!-- Verification Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card <?= $verification['total_permissions_ok'] ? 'border-success' : 'border-danger' ?>">
                                <div class="card-body">
                                    <h5 class="card-title">Total Permissions</h5>
                                    <p class="card-text">
                                        <strong>Expected:</strong> <?= $verification['expected_total'] ?><br>
                                        <strong>Actual:</strong> <?= $verification['actual_total'] ?><br>
                                        <?php if ($verification['total_permissions_ok']): ?>
                                            <span class="badge bg-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">✗ Missing</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card <?= $verification['resource_permissions_ok'] ? 'border-success' : 'border-danger' ?>">
                                <div class="card-body">
                                    <h5 class="card-title">Resource Permissions</h5>
                                    <p class="card-text">
                                        <strong>Expected:</strong> <?= $verification['expected_resource'] ?><br>
                                        <strong>Actual:</strong> <?= $verification['actual_resource'] ?><br>
                                        <?php if ($verification['resource_permissions_ok']): ?>
                                            <span class="badge bg-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">✗ Missing</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resource Permissions List -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Resource Permissions (<?= count($resource_permissions) ?>)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Key</th>
                                            <th>Name</th>
                                            <th>Module</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resource_permissions as $perm): ?>
                                            <tr>
                                                <td><code><?= esc($perm['key']) ?></code></td>
                                                <td><?= esc($perm['name']) ?></td>
                                                <td><?= esc($perm['module']) ?></td>
                                                <td><?= esc($perm['description'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Role Permissions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Role Permissions Count</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Role Name</th>
                                            <th>Total Permissions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($role_permissions as $role): ?>
                                            <tr>
                                                <td><?= esc($role['name']) ?></td>
                                                <td><?= $role['permission_count'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Resource Permissions by Role -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Resource Permissions by Role</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Role Name</th>
                                            <th>Permission Key</th>
                                            <th>Permission Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resource_by_role as $item): ?>
                                            <tr>
                                                <td><?= esc($item['role_name']) ?></td>
                                                <td><code><?= esc($item['permission_key']) ?></code></td>
                                                <td><?= esc($item['permission_name']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

