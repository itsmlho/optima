<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user me-2"></i>Profile
        </h1>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

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
                    <?= form_open('/auth/update-profile', ['id' => 'profileForm']) ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= esc($user['first_name'] ?? '') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('first_name')): ?>
                                    <div class="text-danger small"><?= $validation->getError('first_name') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= esc($user['last_name'] ?? '') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('last_name')): ?>
                                    <div class="text-danger small"><?= $validation->getError('last_name') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= esc($user['email'] ?? '') ?>" readonly>
                                <div class="form-text">Email cannot be changed</div>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= esc($user['username'] ?? '') ?>" readonly>
                                <div class="form-text">Username cannot be changed</div>
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" id="position" name="position" 
                                       value="<?= esc($user['position'] ?? '') ?>" readonly>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="col-lg-4">
            <!-- Security Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shield-alt me-2"></i>Security Settings
                    </h6>
                </div>
                <div class="card-body">
                    <!-- OTP Toggle -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <label class="form-label mb-1 fw-bold">
                                    <i class="fas fa-key me-2"></i>Two-Factor Authentication (OTP)
                                </label>
                                <p class="text-muted small mb-0">
                                    Aktifkan OTP untuk keamanan ekstra. Setelah aktif, Anda akan diminta verifikasi OTP via email setiap kali login.
                                </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span class="badge <?= $otp_enabled ? 'bg-success' : 'bg-secondary' ?> me-2">
                                    <?= $otp_enabled ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                                <?php if ($otp_enabled && !empty($user['otp_enabled_at'])): ?>
                                    <small class="text-muted">
                                        Diaktifkan: <?= date('d M Y, H:i', strtotime($user['otp_enabled_at'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-sm <?= $otp_enabled ? 'btn-outline-danger' : 'btn-outline-success' ?>" 
                                    id="toggleOtpBtn" data-otp-enabled="<?= $otp_enabled ? '1' : '0' ?>">
                                <i class="fas fa-<?= $otp_enabled ? 'toggle-on' : 'toggle-off' ?> me-1"></i>
                                <span id="otpToggleText"><?= $otp_enabled ? 'Nonaktifkan' : 'Aktifkan' ?></span>
                            </button>
                        </div>
                        <div id="otpToggleAlert" class="mt-2"></div>
                    </div>

                    <!-- Change Password -->
                    <div>
                        <h6 class="mb-3">Change Password</h6>
                        <?= form_open('/auth/change-password', ['id' => 'changePasswordForm']) ?>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <?php if (isset($validation) && $validation->hasError('current_password')): ?>
                                    <div class="text-danger small"><?= $validation->getError('current_password') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <?php if (isset($validation) && $validation->hasError('new_password')): ?>
                                    <div class="text-danger small"><?= $validation->getError('new_password') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <?php if (isset($validation) && $validation->hasError('confirm_password')): ?>
                                    <div class="text-danger small"><?= $validation->getError('confirm_password') ?></div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-lock me-2"></i>Change Password
                            </button>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Management -->
    <?php if ($track_devices && !empty($sessions)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-desktop me-2"></i>Active Sessions
                        <span class="badge bg-primary ms-2"><?= $active_session_count ?></span>
                    </h6>
                    <?php if (count($sessions) > 1): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="logoutAllBtn">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout All Other Sessions
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>Browser</th>
                                    <th>OS</th>
                                    <th>IP Address</th>
                                    <th>Last Activity</th>
                                    <th>Login Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sessionsTableBody">
                                <?php foreach ($sessions as $session): ?>
                                    <?php
                                    $isCurrentSession = ($session['session_id'] === $current_session_id);
                                    $lastActivity = $session['last_activity'] ? strtotime($session['last_activity']) : null;
                                    $loginTime = $session['login_at'] ? strtotime($session['login_at']) : null;
                                    
                                    // Calculate time ago
                                    $lastActivityAgo = $lastActivity ? timeAgo($lastActivity) : 'N/A';
                                    $loginTimeAgo = $loginTime ? timeAgo($loginTime) : 'N/A';
                                    ?>
                                    <tr data-session-id="<?= esc($session['session_id']) ?>" class="<?= $isCurrentSession ? 'table-info' : '' ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php
                                                $deviceIcon = 'desktop';
                                                if ($session['device_type'] === 'mobile') $deviceIcon = 'mobile-alt';
                                                elseif ($session['device_type'] === 'tablet') $deviceIcon = 'tablet-alt';
                                                ?>
                                                <i class="fas fa-<?= $deviceIcon ?> me-2 text-primary"></i>
                                                <div>
                                                    <strong><?= esc($session['device_name'] ?? 'Unknown Device') ?></strong>
                                                    <?php if ($isCurrentSession): ?>
                                                        <span class="badge bg-success ms-2">Current</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= esc($session['browser'] ?? 'Unknown') ?></td>
                                        <td><?= esc($session['os'] ?? 'Unknown') ?></td>
                                        <td><code><?= esc($session['ip_address'] ?? 'N/A') ?></code></td>
                                        <td>
                                            <span class="text-muted"><?= esc($lastActivityAgo) ?></span>
                                            <?php if ($lastActivity): ?>
                                                <br><small class="text-muted"><?= date('d M Y, H:i', $lastActivity) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-muted"><?= esc($loginTimeAgo) ?></span>
                                            <?php if ($loginTime): ?>
                                                <br><small class="text-muted"><?= date('d M Y, H:i', $loginTime) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($session['is_active']): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!$isCurrentSession): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger logout-session-btn" 
                                                        data-session-id="<?= esc($session['session_id']) ?>"
                                                        title="Logout this session">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small">Current session</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Info:</strong> Session management helps you keep track of where you're logged in. 
                        You can logout other sessions if you suspect unauthorized access.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
// Helper function for time ago
function timeAgo($time) {
    $diff = time() - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('d M Y', $time);
    }
}
?>

<script>
$(document).ready(function() {
    // Toggle OTP
    $('#toggleOtpBtn').on('click', function() {
        const btn = $(this);
        const currentStatus = btn.data('otp-enabled') == '1';
        const actionText = currentStatus ? 'menonaktifkan' : 'mengaktifkan';
        
        if (window.OptimaConfirm && typeof OptimaConfirm.generic === 'function') {
            OptimaConfirm.generic({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin ${actionText} OTP?`,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((confirmed) => {
                if (!confirmed) return;
                $(this).trigger('click.proceed');
            });
            return;
        }
        if (!window.confirm(`Apakah Anda yakin ingin ${actionText} OTP?`)) {
            return;
        }
        
        // Disable button
        btn.prop('disabled', true);
        const originalHtml = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin me-1"></i>Memproses...');
        
        $.ajax({
            url: '<?= base_url('auth/toggle-otp') ?>',
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const alertHtml = `
                        <div class="alert alert-${response.otp_enabled ? 'success' : 'info'} alert-dismissible fade show" role="alert">
                            <i class="fas fa-${response.otp_enabled ? 'check-circle' : 'info-circle'} me-2"></i>
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('#otpToggleAlert').html(alertHtml);
                    
                    // Update UI
                    const isEnabled = response.otp_enabled;
                    btn.data('otp-enabled', isEnabled ? '1' : '0');
                    btn.removeClass(isEnabled ? 'btn-outline-danger' : 'btn-outline-success')
                       .addClass(isEnabled ? 'btn-outline-danger' : 'btn-outline-success');
                    btn.html(`
                        <i class="fas fa-${isEnabled ? 'toggle-on' : 'toggle-off'} me-1"></i>
                        <span>${isEnabled ? 'Nonaktifkan' : 'Aktifkan'}</span>
                    `);
                    
                    // Update badge
                    const badge = btn.closest('.card-body').find('.badge');
                    badge.removeClass(isEnabled ? 'bg-secondary' : 'bg-success')
                          .addClass(isEnabled ? 'bg-success' : 'bg-secondary')
                          .text(isEnabled ? 'Aktif' : 'Tidak Aktif');
                    
                    // Auto-dismiss alert after 5 seconds
                    setTimeout(function() {
                        $('#otpToggleAlert .alert').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 5000);
                } else {
                    showAlert('danger', response.message || 'Gagal mengubah status OTP.');
                }
                btn.prop('disabled', false);
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showAlert('danger', response.message || 'Terjadi kesalahan. Silakan coba lagi.');
                btn.prop('disabled', false);
                btn.html(originalHtml);
            }
        });
    });
    
    // Show alert message
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#otpToggleAlert').html(alertHtml);
        
        setTimeout(function() {
            $('#otpToggleAlert .alert').fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Logout specific session
    $(document).on('click', '.logout-session-btn', function(e) {
        e.preventDefault();
        
        const sessionId = $(this).data('session-id');
        const btn = $(this);
        const row = btn.closest('tr');
        
        if (!window.confirm('Are you sure you want to logout this session?')) {
            return;
        }
        
        // Disable button
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '<?= base_url('auth/logout-session') ?>/' + sessionId,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', response.message);
                    
                    // Remove row with animation
                    row.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Update session count
                        updateSessionCount();
                    });
                } else {
                    showAlert('danger', response.message || 'Failed to logout session.');
                    btn.prop('disabled', false).html('<i class="fas fa-sign-out-alt"></i>');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showAlert('danger', response.message || 'An error occurred. Please try again.');
                btn.prop('disabled', false).html('<i class="fas fa-sign-out-alt"></i>');
            }
        });
    });
    
    // Logout all other sessions
    $('#logoutAllBtn').on('click', function(e) {
        e.preventDefault();
        
        if (!window.confirm('Are you sure you want to logout all other sessions? You will remain logged in on this device.')) {
            return;
        }
        
        const btn = $(this);
        const originalText = btn.html();
        
        // Disable button
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Processing...');
        
        $.ajax({
            url: '<?= base_url('auth/logout-all-sessions') ?>',
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', response.message);
                    
                    // Remove all rows except current session
                    $('#sessionsTableBody tr').not('.table-info').fadeOut(300, function() {
                        $(this).remove();
                        updateSessionCount();
                    });
                    
                    // Hide logout all button if no other sessions
                    setTimeout(function() {
                        if ($('#sessionsTableBody tr').length <= 1) {
                            $('#logoutAllBtn').fadeOut();
                        }
                    }, 400);
                } else {
                    showAlert('danger', response.message || 'Failed to logout sessions.');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showAlert('danger', response.message || 'An error occurred. Please try again.');
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Update session count
    function updateSessionCount() {
        const count = $('#sessionsTableBody tr').length;
        $('.card-header .badge').text(count);
    }
    
    // Show alert message
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insert at top of container
        $('.container-fluid').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
});
</script>

<?= $this->endSection() ?>

