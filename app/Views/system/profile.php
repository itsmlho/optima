<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Action Buttons -->
<div class="d-flex justify-content-end gap-2 mb-4">
    <button class="btn btn-outline-secondary" onclick="resetProfile()">
        <i class="fas fa-undo me-2"></i>Reset Changes
    </button>
    <button class="btn btn-primary" onclick="saveProfile()">
        <i class="fas fa-save me-2"></i>Save Changes
    </button>
</div>

<div class="row g-4">
    <!-- Profile Information -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Profile Information</h5>
                <p class="text-muted mb-0">Update your personal details</p>
            </div>
            <div class="card-body">
                <form id="profileForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" value="Admin">
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" value="User">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" value="<?= esc($user_email) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" value="+62 812-3456-7890">
                        </div>
                        <div class="col-md-6">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select" id="department">
                                <option value="admin">Administration</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="operations">Operations</option>
                                <option value="management">Management</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" id="position" value="System Administrator">
                        </div>
                        <div class="col-12">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" rows="3" placeholder="Tell us about yourself...">Experienced system administrator responsible for maintaining and optimizing the asset management system.</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Profile Picture & Security -->
    <div class="col-lg-4">
        <!-- Profile Picture -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Profile Picture</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <?php if (isset($user_data['avatar']) && !empty($user_data['avatar'])): ?>
                        <img src="<?= $user_data['avatar'] ?>" alt="Profile Picture" class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-2">
                    <strong><?= $user_data['first_name'] ?? 'User' ?> <?= $user_data['last_name'] ?? '' ?></strong>
                </div>
                <div class="text-muted mb-3"><?= $user_data['role'] ?? 'User' ?> • <?= $user_data['department'] ?? 'Department' ?></div>
                <button class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-camera me-2"></i>Change Photo
                </button>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Security</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="currentPassword">
                </div>
                <div class="mb-3">
                    <label for="newPassword" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="newPassword">
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword">
                </div>
                <button class="btn btn-warning btn-sm w-100">
                    <i class="fas fa-key me-2"></i>Change Password
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function saveProfile() {
    // Simulate save
    showNotification('Profile updated successfully!', 'success');
}

function resetProfile() {
    // Reset form
    document.getElementById('profileForm').reset();
    showNotification('Changes reset', 'info');
}

function showNotification(message, type) {
    // Create notification
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 1060; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    // Auto remove
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}
</script>

<?= $this->endSection() ?> 