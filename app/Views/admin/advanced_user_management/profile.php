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
            <div class="btn-group" role="group" id="actionButtons">
                <button class="btn btn-primary btn-sm" id="btnEdit" onclick="enableEdit()">
                    <i class="fas fa-edit me-1"></i>Update Data
                </button>
            </div>
            <div class="btn-group d-none" role="group" id="editButtons">
                <button class="btn btn-outline-secondary btn-sm" onclick="cancelEdit()">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button class="btn btn-success btn-sm" onclick="saveProfile()">
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
                                       value="<?= esc($user_data['first_name'] ?? '') ?>" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= esc($user_data['last_name'] ?? '') ?>" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= esc($user_data['email'] ?? '') ?>" readonly>
                                <div class="form-text">Email cannot be changed</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= esc($user_data['phone'] ?? '') ?>" placeholder="+62 812-3456-7890" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="division_name" class="form-label">Division</label>
                                <input type="text" class="form-control" id="division_name" 
                                       value="<?= esc($user_data['division_name'] ?? 'No Division') ?>" readonly>
                                <div class="form-text">Division is managed by administrator</div>
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" id="position" 
                                       value="<?= esc($user_data['position'] ?? '') ?>" readonly>
                                <div class="form-text">Position is managed by administrator</div>
                            </div>
                            <div class="col-12">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" 
                                          placeholder="Tell us about yourself..." maxlength="500" readonly><?= esc($user_data['bio'] ?? '') ?></textarea>
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
                                            <td><?= esc($log['ip_address'] ?? '-') ?></td>
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

        <!-- Sidebar -->
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
                            <?php 
                            // Handle both relative and absolute avatar URLs
                            $avatarUrl = $user_data['avatar'];
                            if (!filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                                $avatarUrl = base_url($avatarUrl);
                            }
                            ?>
                            <img src="<?= $avatarUrl ?>" alt="Profile Picture" class="rounded-circle" 
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
                        <?= esc($user_data['position'] ?? 'Position') ?> • <?= esc($user_data['division_name'] ?? 'No Division') ?>
                    </div>
                    <form id="avatarForm" enctype="multipart/form-data">
                        <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" style="display: none;" disabled>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnChangePhoto" onclick="document.getElementById('avatarInput').click()" disabled>
                            <i class="fas fa-camera me-2"></i>Change Photo
                        </button>
                    </form>
                </div>
            </div>

            <!-- Security -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-shield-alt me-2"></i>Security
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Keep your account secure by using a strong password.
                    </p>
                    <button type="button" class="btn btn-warning w-100" onclick="openChangePasswordModal()">
                        <i class="fas fa-key me-2"></i>Change Password
                    </button>
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
                        <div class="col-6 mb-3">
                            <div class="text-muted mb-1">Member Since</div>
                            <div class="fw-bold"><?= date('M Y', strtotime($user_data['created_at'] ?? 'now')) ?></div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-muted mb-1">Profile Updates</div>
                            <div class="fw-bold"><?= count($profile_logs) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted mb-1">Last Login</div>
                            <div class="fw-bold"><?= date('d M Y', strtotime($user_data['last_login'] ?? 'now')) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted mb-1">Status</div>
                            <div>
                                <span class="badge badge-success">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="fas fa-key me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open('/profile/change-password', ['id' => 'passwordForm']) ?>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('current_password')">
                            <i class="fas fa-eye" id="current_password_icon"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password')">
                            <i class="fas fa-eye" id="new_password_icon"></i>
                        </button>
                    </div>
                    <div class="form-text">Minimum 8 characters, include uppercase, lowercase, numbers, and special characters</div>
                    <div class="mt-2" id="passwordStrength"></div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('confirm_password')">
                            <i class="fas fa-eye" id="confirm_password_icon"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-key me-2"></i>Change Password
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Changes Modal -->
<div class="modal fade" id="changesModal" tabindex="-1" aria-labelledby="changesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changesModalLabel">Change Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="changesModalBody">
                <!-- Changes will be displayed here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let isEditMode = false;
let originalData = {};

// Enable Edit Mode
function enableEdit() {
    isEditMode = true;
    
    // Store original data
    originalData = {
        first_name: $('#first_name').val(),
        last_name: $('#last_name').val(),
        phone: $('#phone').val(),
        bio: $('#bio').val()
    };
    
    // Enable editable fields
    $('#first_name, #last_name, #phone, #bio').prop('readonly', false).addClass('form-control-focus');
    
    // Enable avatar upload
    $('#avatarInput').prop('disabled', false);
    $('#btnChangePhoto').prop('disabled', false);
    
    // Toggle buttons
    $('#actionButtons').addClass('d-none');
    $('#editButtons').removeClass('d-none');
}

// Cancel Edit Mode
function cancelEdit() {
    Swal.fire({
        title: 'Cancel Changes?',
        text: "All unsaved changes will be lost!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, cancel it!'
    }).then((result) => {
        if (result.isConfirmed) {
            isEditMode = false;
            
            // Restore original data
            $('#first_name').val(originalData.first_name);
            $('#last_name').val(originalData.last_name);
            $('#phone').val(originalData.phone);
            $('#bio').val(originalData.bio);
            
            // Disable fields
            $('#first_name, #last_name, #phone, #bio').prop('readonly', true).removeClass('form-control-focus');
            
            // Disable avatar upload
            $('#avatarInput').prop('disabled', true);
            $('#btnChangePhoto').prop('disabled', true);
            
            // Toggle buttons
            $('#editButtons').addClass('d-none');
            $('#actionButtons').removeClass('d-none');
        }
    });
}

// Open Change Password Modal
function openChangePasswordModal() {
    $('#changePasswordModal').modal('show');
}

// Toggle Password Visibility
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password Strength Indicator
$('#new_password').on('input', function() {
    const password = $(this).val();
    let strength = 0;
    let strengthText = '';
    let strengthClass = '';
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    
    switch(strength) {
        case 0:
        case 1:
            strengthText = 'Very Weak';
            strengthClass = 'danger';
            break;
        case 2:
            strengthText = 'Weak';
            strengthClass = 'warning';
            break;
        case 3:
            strengthText = 'Medium';
            strengthClass = 'info';
            break;
        case 4:
            strengthText = 'Strong';
            strengthClass = 'success';
            break;
        case 5:
            strengthText = 'Very Strong';
            strengthClass = 'success';
            break;
    }
    
    $('#passwordStrength').html(
        '<div class="progress" style="height: 5px;">' +
        '<div class="progress-bar bg-' + strengthClass + '" style="width: ' + (strength * 20) + '%"></div>' +
        '</div>' +
        '<small class="text-' + strengthClass + '">Password Strength: ' + strengthText + '</small>'
    );
});

// Save Profile
function saveProfile() {
    if (!isEditMode) {
        Swal.fire({
            icon: 'warning',
            title: 'Not in Edit Mode',
            text: 'Please click "Update Data" first to edit your profile',
            confirmButtonColor: '#4e73df'
        });
        return;
    }
    
    const form = $('#profileForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait while we update your profile',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData(form);
    
    $.ajax({
        url: '<?= base_url('/profile/update') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Profile update response:', response);
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    confirmButtonColor: '#4e73df',
                    timer: 2000,
                    showConfirmButton: true
                }).then(() => {
                    isEditMode = false;
                    
                    // Disable fields
                    $('#first_name, #last_name, #phone, #bio').prop('readonly', true).removeClass('form-control-focus');
                    
                    // Disable avatar upload
                    $('#avatarInput').prop('disabled', true);
                    $('#btnChangePhoto').prop('disabled', true);
                    
                    // Toggle buttons
                    $('#editButtons').addClass('d-none');
                    $('#actionButtons').removeClass('d-none');
                    
                    // Reload to show updated data
                    location.reload();
                });
            } else {
                let errorMessage = response.message || 'Failed to update profile';
                
                // Show detailed validation errors if available
                if (response.errors) {
                    let errorList = '';
                    for (let field in response.errors) {
                        errorList += '<li>' + response.errors[field] + '</li>';
                    }
                    errorMessage = '<ul style="text-align: left;">' + errorList + '</ul>';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    html: errorMessage,
                    confirmButtonColor: '#e74a3b'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Profile update error:', xhr.responseText);
            
            let errorMessage = 'An error occurred while updating profile';
            
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMessage = response.message;
                }
            } catch (e) {
                // Use default error message
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage,
                confirmButtonColor: '#e74a3b'
            });
        }
    });
}

// Change Password Form
$('#passwordForm').on('submit', function(e) {
    e.preventDefault();
    
    const newPassword = $('#new_password').val();
    const confirmPassword = $('#confirm_password').val();
    
    if (newPassword !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'New password and confirm password do not match',
            confirmButtonColor: '#e74a3b'
        });
        return;
    }
    
    const formData = new FormData(this);
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#changePasswordModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    confirmButtonColor: '#4e73df'
                }).then(() => {
                    $('#passwordForm')[0].reset();
                    $('#passwordStrength').html('');
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message,
                    confirmButtonColor: '#e74a3b'
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while changing password',
                confirmButtonColor: '#e74a3b'
            });
        }
    });
});

// Show Changes Modal
function showChanges(changesJson) {
    const changes = JSON.parse(changesJson);
    let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Field</th><th>Old Value</th><th>New Value</th></tr></thead><tbody>';
    
    for (let field in changes) {
        html += '<tr><td>' + field + '</td><td>' + (changes[field].old || '-') + '</td><td>' + (changes[field].new || '-') + '</td></tr>';
    }
    
    html += '</tbody></table></div>';
    $('#changesModalBody').html(html);
    $('#changesModal').modal('show');
}

// Avatar Upload Preview
$('#avatarInput').on('change', function(e) {
    if (!isEditMode) {
        Swal.fire({
            icon: 'warning',
            title: 'Not in Edit Mode',
            text: 'Please click "Update Data" first to change your profile picture',
            confirmButtonColor: '#4e73df'
        });
        $(this).val(''); // Clear the file input
        return;
    }
    
    const file = e.target.files[0];
    if (file) {
        // Validate file type and extension
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!allowedTypes.includes(file.type) || !allowedExtensions.includes(fileExtension)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Format',
                text: 'Please select a valid image file (JPG, PNG, GIF, or WebP). ICO files are not supported.',
                confirmButtonColor: '#e74a3b'
            });
            $(this).val(''); // Clear the file input
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Image size should not exceed 2MB',
                confirmButtonColor: '#e74a3b'
            });
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = '<img src="' + e.target.result + '" class="rounded-circle" width="120" height="120" style="object-fit: cover;" id="profileImage">';
            $('#profileImage').replaceWith(img);
        };
        reader.readAsDataURL(file);
        
        // Upload image
        const formData = new FormData();
        formData.append('avatar', file);
        
        $.ajax({
            url: '<?= base_url('/profile/upload-avatar') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Avatar upload response:', response);
                if (response.success) {
                    // Update header avatar
                    const headerAvatar = document.querySelector('.btn-outline-secondary img');
                    if (headerAvatar && response.avatar_url) {
                        // Handle both relative and absolute URLs
                        let avatarUrl = response.avatar_url;
                        if (!avatarUrl.startsWith('http')) {
                            avatarUrl = window.location.origin + '/optima1/public/' + avatarUrl;
                        }
                        headerAvatar.src = avatarUrl;
                        headerAvatar.width = 40;
                        headerAvatar.height = 40;
                        headerAvatar.style.border = '2px solid #e3e6f0';
                        console.log('Header avatar updated to:', avatarUrl);
                    } else {
                        console.log('Header avatar element not found or no avatar_url in response');
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Profile picture updated successfully',
                        confirmButtonColor: '#4e73df',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#e74a3b'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Avatar upload error:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while uploading image: ' + error,
                    confirmButtonColor: '#e74a3b'
                });
            }
        });
    }
});

// Reset password form when modal is closed
$('#changePasswordModal').on('hidden.bs.modal', function () {
    $('#passwordForm')[0].reset();
    $('#passwordStrength').html('');
    
    // Reset all password fields to type password
    $('#current_password, #new_password, #confirm_password').attr('type', 'password');
    $('#current_password_icon, #new_password_icon, #confirm_password_icon').removeClass('fa-eye-slash').addClass('fa-eye');
});

// Add focus styling for editable fields
$(document).ready(function() {
    $('<style>')
        .prop('type', 'text/css')
        .html('.form-control-focus { border-color: #4e73df !important; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important; }')
        .appendTo('head');
});
</script>
<?= $this->endSection() ?>
