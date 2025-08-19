<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/advanced-users') ?>">User Management</a></li>
                    <li class="breadcrumb-item active">User Details</li>
                </ul>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <a href="<?= base_url('admin/advanced-users/edit/' . $user['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit User
                    </a>
                    <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body text-center">
                    <!-- User Avatar -->
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                    </div>
                    
                    <h4 class="mb-1"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                    <p class="text-muted mb-3">@<?= esc($user['username']) ?></p>
                    
                    <span class="badge bg-<?= $user['status'] == 'active' ? 'success' : ($user['status'] == 'inactive' ? 'warning' : 'danger') ?> fs-6">
                        <?= ucfirst($user['status']) ?>
                    </span>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <a href="mailto:<?= esc($user['email']) ?>"><?= esc($user['email']) ?></a>
                    </div>
                    
                    <?php if (!empty($user['phone'])): ?>
                    <div class="mb-3">
                        <strong>Phone:</strong><br>
                        <a href="tel:<?= esc($user['phone']) ?>"><?= esc($user['phone']) ?></a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <strong>User ID:</strong><br>
                        <?= $user['id'] ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        <?= $user['created_at'] ?? 'N/A' ?>
                    </div>
                    
                    <div class="mb-0">
                        <strong>Last Login:</strong><br>
                        <?= $user['last_login'] ?? 'Never' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles and Permissions -->
        <div class="col-md-8">
            <!-- Roles -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Assigned Roles</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($userRoles)): ?>
                        <div class="row">
                            <?php foreach ($userRoles as $role): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <h6 class="card-title mb-1"><?= esc($role['name']) ?></h6>
                                        <p class="card-text text-muted small mb-0"><?= esc($role['description']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No roles assigned to this user.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Divisions -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Assigned Divisions</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($userDivisions)): ?>
                        <div class="row">
                            <?php foreach ($userDivisions as $division): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <h6 class="card-title mb-1">
                                            <?= esc($division['name']) ?>
                                            <?php if ($division['is_head'] ?? false): ?>
                                                <i class="fas fa-crown text-warning ms-1" title="Division Head"></i>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="card-text text-muted small mb-0">
                                            Code: <?= esc($division['code']) ?>
                                            <?php if ($division['is_head'] ?? false): ?>
                                                <br><span class="text-warning">Division Head</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No divisions assigned to this user.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Permission Matrix -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Permission</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($permissionMatrix['effective_permissions'])): ?>
                        <div class="row">
                            <?php foreach ($permissionMatrix['effective_permissions'] as $permission => $granted): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-<?= $granted ? 'success' : 'danger' ?> me-2">
                                        <?= $granted ? '✓' : '✗' ?>
                                    </span>
                                    <span class="<?= $granted ? 'text-success' : 'text-danger' ?>">
                                        <?= esc($permission) ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No specific permissions configured for this user.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity History (Placeholder) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Activity tracking will be implemented in future updates.
                    </div>
                    
                    <!-- Placeholder for activity log -->
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">User Created</h6>
                                <p class="timeline-text text-muted">Account was created on <?= $user['created_at'] ?? 'N/A' ?></p>
                            </div>
                        </div>
                        
                        <?php if ($user['last_login']): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Last Login</h6>
                                <p class="timeline-text text-muted">Last logged in on <?= $user['last_login'] ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
.avatar-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.timeline-title {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 0.8rem;
    margin-bottom: 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -30px;
    top: 5px;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.card .card-body .badge {
    font-size: 0.75em;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Any additional JavaScript for the user details page
    console.log('User details page loaded');
});
</script>
<?= $this->endSection() ?>
