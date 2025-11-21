<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-key me-2"></i>Change Password
                    </h1>
                    <p class="text-muted mb-0">
                        Change password for <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['username']) ?>)
                    </p>
                </div>
                <div>
                    <a href="<?= base_url('admin/advanced-users/edit/' . $user['id']) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Edit User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lock me-2"></i>Password Information
                    </h5>
                </div>
                <div class="card-body">
                    <form id="changePasswordForm" action="<?= base_url('admin/advanced-users/change-password/' . $user['id']) ?>" method="POST">
                        
                        <!-- User Info -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>User:</strong> <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['username']) ?>)
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-bold">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                    <i class="fas fa-eye" id="newPasswordIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Password harus minimal 6 karakter dan mengandung minimal 1 huruf dan 1 angka
                            </div>
                            <div id="passwordStrength" class="mt-2"></div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                </button>
                            </div>
                            <div id="passwordMatch" class="mt-2"></div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('admin/advanced-users/edit/' . $user['id']) ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#toggleNewPassword').on('click', function() {
        const passwordField = $('#new_password');
        const icon = $('#newPasswordIcon');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        icon.toggleClass('fa-eye fa-eye-slash');
    });

    $('#toggleConfirmPassword').on('click', function() {
        const passwordField = $('#confirm_password');
        const icon = $('#confirmPasswordIcon');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        icon.toggleClass('fa-eye fa-eye-slash');
    });

    // Password strength indicator
    $('#new_password').on('input', function() {
        const password = $(this).val();
        const strength = checkPasswordStrength(password);
        const strengthHtml = getPasswordStrengthHtml(strength);
        $('#passwordStrength').html(strengthHtml);
    });

    // Password match indicator
    $('#confirm_password').on('input', function() {
        const password = $('#new_password').val();
        const confirmPassword = $(this).val();
        const matchHtml = getPasswordMatchHtml(password, confirmPassword);
        $('#passwordMatch').html(matchHtml);
    });

    function checkPasswordStrength(password) {
        let score = 0;
        if (password.length >= 6) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return score;
    }

    function getPasswordStrengthHtml(strength) {
        if (strength === 0) return '';
        const levels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        const colors = ['danger', 'warning', 'info', 'success', 'success'];
        const level = Math.min(strength - 1, 4);
        return `<span class="badge bg-${colors[level]}">${levels[level]}</span>`;
    }

    function getPasswordMatchHtml(password, confirmPassword) {
        if (confirmPassword === '') return '';
        if (password === confirmPassword) {
            return '<span class="text-success"><i class="fas fa-check me-1"></i>Passwords match</span>';
        } else {
            return '<span class="text-danger"><i class="fas fa-times me-1"></i>Passwords do not match</span>';
        }
    }

    // Form submission
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        var isValid = true;
        var errors = [];
        
        // Check password match
        if ($('#new_password').val() !== $('#confirm_password').val()) {
            errors.push('Passwords do not match.');
            isValid = false;
        }
        
        // Check password length
        if ($('#new_password').val().length < 6) {
            errors.push('Password must be at least 6 characters long.');
            isValid = false;
        }
        
        // Check password strength (at least one letter and one number)
        const password = $('#new_password').val();
        if (!/[a-zA-Z]/.test(password)) {
            errors.push('Password must contain at least one letter.');
            isValid = false;
        }
        if (!/[0-9]/.test(password)) {
            errors.push('Password must contain at least one number.');
            isValid = false;
        }
        
        if (!isValid) {
            if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                OptimaPro.showNotification(errors.join('<br>'), 'danger', 5000);
            } else {
                alert('Validation Error:\n' + errors.join('\n'));
            }
            return;
        }
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification(response.message || 'Password berhasil diubah', 'success', 3000);
                    } else {
                        alert('Success: ' + (response.message || 'Password berhasil diubah'));
                    }
                    setTimeout(function() {
                        window.location.href = '<?= base_url('admin/advanced-users/edit/' . $user['id']) ?>';
                    }, 1500);
                } else {
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification(response.message || 'Failed to change password', 'danger', 5000);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to change password'));
                    }
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = 'Failed to change password';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                }
                if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification(errorMessage, 'danger', 5000);
                } else {
                    alert('Error: ' + errorMessage);
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>


