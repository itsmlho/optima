 <?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user me-2"></i>Profile Management
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="me-3">
                <small class="text-muted">Last updated: </small>
                <span class="fw-bold"><?= date('d M Y, H:i', strtotime($user_data['updated_at'] ?? 'now')) ?></span>
            </div>
            <div class="btn-group" role="group">
                <button class="btn btn-outline-secondary btn-sm" onclick="resetProfile()">
                    <i class="fas fa-undo me-1"></i>Reset
                </button>
                <button class="btn btn-primary btn-sm" onclick="saveProfile()">
                    <i class="fas fa-save me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-edit me-2"></i>Personal Information
                    </h6>
                </div>
                <div class="card-body">
                    <?= form_open('/profile/update', ['id' => 'profileForm']) ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= esc($user_data['first_name'] ?? '') ?>" required>
                                <div class="invalid-feedback">Please provide a valid first name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= esc($user_data['last_name'] ?? '') ?>" required>
                                <div class="invalid-feedback">Please provide a valid last name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= esc($user_data['email'] ?? '') ?>" required>
                                <div class="invalid-feedback">Please provide a valid email address.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= esc($user_data['phone'] ?? '') ?>" placeholder="+62 812-3456-7890">
                            </div>
                            <div class="col-md-6">
                                <label for="division" class="form-label">Division <span class="text-danger">*</span></label>
                                <select class="form-select" id="division" name="division" required>
                                    <option value="">Select Division</option>
                                    <?php foreach ($divisions as $key => $division): ?>
                                        <option value="<?= $key ?>" <?= ($user_data['division'] ?? '') == $key ? 'selected' : '' ?>>
                                            <?= $division ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a division.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="position" name="position" 
                                       value="<?= esc($user_data['position'] ?? '') ?>" required>
                                <div class="invalid-feedback">Please provide your position.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                <select class="form-select" id="location" name="location" required>
                                    <option value="">Select Location</option>
                                    <?php foreach ($locations as $key => $location): ?>
                                        <option value="<?= $key ?>" <?= ($user_data['location'] ?? '') == $key ? 'selected' : '' ?>>
                                            <?= $location ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a location.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="supervisor_id" class="form-label">Supervisor</label>
                                <select class="form-select" id="supervisor_id" name="supervisor_id">
                                    <option value="">No Supervisor</option>
                                    <?php foreach ($supervisors as $supervisor): ?>
                                        <option value="<?= $supervisor['id'] ?>" <?= ($user_data['supervisor_id'] ?? '') == $supervisor['id'] ? 'selected' : '' ?>>
                                            <?= esc($supervisor['first_name'] . ' ' . $supervisor['last_name']) ?> - <?= esc($supervisor['position'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" 
                                          placeholder="Tell us about yourself..." maxlength="500"><?= esc($user_data['bio'] ?? '') ?></textarea>
                                <div class="form-text">Maximum 500 characters</div>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Profile Activity Log
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="activityLogTable">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Action</th>
                                    <th>Changes</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($profile_logs)): ?>
                                    <?php foreach ($profile_logs as $log): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                            <td>
                                                <?php
                                                $actionLabels = [
                                                    'profile_update' => '<span class="badge badge-info">Profile Update</span>',
                                                    'password_change' => '<span class="badge badge-warning">Password Change</span>',
                                                    'avatar_change' => '<span class="badge badge-success">Avatar Change</span>'
                                                ];
                                                echo $actionLabels[$log['action']] ?? '<span class="badge badge-secondary">' . ucfirst($log['action']) . '</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($log['changes'])): ?>
                                                    <?php 
                                                    $changes = json_decode($log['changes'], true);
                                                    if (is_array($changes)):
                                                    ?>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="showChanges('<?= htmlspecialchars(json_encode($changes)) ?>')">
                                                            <i class="fas fa-eye"></i> View Changes
                                                        </button>
                                                    <?php else: ?>
                                                        <small class="text-muted">No details</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <small class="text-muted">No changes recorded</small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($log['ip_address']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No activity logs found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Picture & Security -->
        <div class="col-lg-4">
            <!-- Profile Picture -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-camera me-2"></i>Profile Picture
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if (!empty($user_data['avatar'])): ?>
                            <img src="<?= $user_data['avatar'] ?>" alt="Profile Picture" class="rounded-circle" 
                                 width="120" height="120" style="object-fit: cover;" id="profileImage">
                        <?php else: ?>
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 120px; height: 120px;" id="profileImage">
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-2">
                        <strong><?= esc($user_data['first_name'] ?? 'User') ?> <?= esc($user_data['last_name'] ?? '') ?></strong>
                    </div>
                    <div class="text-muted mb-3">
                        <?= esc($user_data['position'] ?? 'Position') ?> • <?= esc($divisions[$user_data['division']] ?? 'Division') ?>
                    </div>
                    <div class="text-muted mb-3">
                        <i class="fas fa-map-marker-alt me-1"></i><?= esc($locations[$user_data['location']] ?? 'Location') ?>
                    </div>
                    <form id="avatarForm" enctype="multipart/form-data">
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('avatarInput').click()">
                            <i class="fas fa-camera me-2"></i>Change Photo
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-key me-2"></i>Security
                    </h6>
                </div>
                <div class="card-body">
                    <?= form_open('/profile/change-password', ['id' => 'passwordForm']) ?>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                            <div class="form-text">Minimum 8 characters</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm w-100">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    <?= form_close() ?>
                </div>
            </div>

            <!-- Profile Statistics -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-chart-bar me-2"></i>Profile Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h5 mb-0 font-weight-bold text-primary"><?= count($profile_logs) ?></div>
                                <div class="text-xs text-muted">Profile Changes</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 font-weight-bold text-success">
                                <?= date('d M Y', strtotime($user_data['created_at'] ?? 'now')) ?>
                            </div>
                            <div class="text-xs text-muted">Member Since</div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="h6 mb-0 font-weight-bold text-info">Profile Completion</div>
                        <div class="progress mt-2">
                            <?php
                            $completion = 0;
                            $fields = ['first_name', 'last_name', 'email', 'phone', 'division', 'position', 'location', 'bio', 'avatar'];
                            foreach ($fields as $field) {
                                if (!empty($user_data[$field])) $completion++;
                            }
                            $completionPercent = round(($completion / count($fields)) * 100);
                            ?>
                            <div class="progress-bar bg-info" style="width: <?= $completionPercent ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $completionPercent ?>% Complete</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Changes Modal -->
<div class="modal fade" id="changesModal" tabindex="-1" aria-labelledby="changesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changesModalLabel">Profile Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="changesModalBody">
                <!-- Changes will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#activityLogTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']],
        dom: 'rtip'
    });

    // Avatar upload
    $('#avatarInput').on('change', function() {
        const file = this.files[0];
        if (file) {
            uploadAvatar(file);
        }
    });

    // Form validation
    $('#profileForm').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });

    $('#passwordForm').on('submit', function(e) {
        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            showNotification('Passwords do not match', 'error');
            return false;
        }
    });
});

function saveProfile() {
    $('#profileForm').submit();
}

function resetProfile() {
    $('#profileForm')[0].reset();
    $('#profileForm').removeClass('was-validated');
    showNotification('Form reset successfully', 'info');
}

function uploadAvatar(file) {
    const formData = new FormData();
    formData.append('avatar', file);
    
    // Show loading
    showNotification('Uploading avatar...', 'info');
    
    fetch('/profile/upload-avatar', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update profile image
            const profileImage = document.getElementById('profileImage');
            if (profileImage.tagName === 'IMG') {
                profileImage.src = data.avatar_url;
            } else {
                // Replace div with img
                const img = document.createElement('img');
                img.src = data.avatar_url;
                img.alt = 'Profile Picture';
                img.className = 'rounded-circle';
                img.style.width = '120px';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                img.id = 'profileImage';
                profileImage.parentNode.replaceChild(img, profileImage);
            }
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to upload avatar', 'error');
    });
}

function showChanges(changesJson) {
    try {
        const changes = JSON.parse(changesJson);
        let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Field</th><th>From</th><th>To</th></tr></thead><tbody>';
        
        for (const [field, change] of Object.entries(changes)) {
            html += `<tr>
                <td><strong>${field.replace('_', ' ').toUpperCase()}</strong></td>
                <td><span class="text-muted">${change.from || 'Empty'}</span></td>
                <td><span class="text-success">${change.to || 'Empty'}</span></td>
            </tr>`;
        }
        
        html += '</tbody></table></div>';
        
        document.getElementById('changesModalBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('changesModal')).show();
    } catch (error) {
        showNotification('Error displaying changes', 'error');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 1060; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}
</script>

<?= $this->endSection() ?> 