<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <?= isset($user) ? 'Edit User - ' . $user['first_name'] . ' ' . $user['last_name'] : 'Create New User' ?>
                    </h1>
                    <p class="text-muted mb-0">
                        <?= isset($user) ? 'Update user information and permissions' : 'Add a new user to the system' ?>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if (isset($user)): ?>
                        <div class="btn-group" role="group" id="actionButtons">
                            <button class="btn btn-primary btn-sm" id="btnEdit" onclick="enableEdit()">
                                <i class="fas fa-edit me-1"></i>Update Data
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="openChangePasswordModal()">
                                <i class="fas fa-key me-1"></i>Change Password
                            </button>
                        </div>
                        <div class="btn-group d-none" role="group" id="editButtons">
                            <button class="btn btn-outline-secondary btn-sm" onclick="cancelEdit()">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button class="btn btn-success btn-sm" onclick="saveUser()">
                                <i class="fas fa-save me-1"></i>Save Changes
                            </button>
                        </div>
                    <?php endif; ?>
                    <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form id="<?= isset($user) ? 'editUserForm' : 'createUserForm' ?>" 
                          action="<?= isset($user) ? base_url('admin/advanced-users/update/' . $user['id']) : base_url('admin/advanced-users/store') ?>" 
                          method="POST">
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label fw-bold">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?= isset($user) ? esc($user['first_name']) : '' ?>" 
                                           <?= isset($user) ? 'readonly' : '' ?> required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label fw-bold">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?= isset($user) ? esc($user['last_name']) : '' ?>" 
                                           <?= isset($user) ? 'readonly' : '' ?> required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label fw-bold">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= isset($user) ? esc($user['username']) : '' ?>" 
                                           <?= isset($user) ? 'readonly' : '' ?> required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= isset($user) ? esc($user['email']) : '' ?>" 
                                           <?= isset($user) ? 'readonly' : '' ?> required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-bold">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?= isset($user) ? esc($user['phone']) : '' ?>"
                                           <?= isset($user) ? 'readonly' : '' ?>>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label fw-bold">Status</label>
                                    <select class="form-select" id="status" name="status" 
                                            <?= isset($user) ? 'disabled' : '' ?> required>
                                        <option value="active" <?= (isset($user) && ($user['status'] == 'active' || (isset($user['is_active']) && $user['is_active'] == 1))) ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= (isset($user) && ($user['status'] == 'inactive' || (isset($user['is_active']) && $user['is_active'] == 0))) ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                    <input type="hidden" id="is_active" name="is_active" 
                                           value="<?= isset($user) ? ((isset($user['is_active']) && $user['is_active'] == 1) ? '1' : '0') : '1' ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Password Section (only for create) -->
                        <?php if (!isset($user)): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-bold">Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        <div id="passwordStrength" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirm" class="form-label fw-bold">Confirm Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        <div id="passwordMatch" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Division & Role -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-building me-2"></i>Division & Role
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Division *</label>
                                    <select class="form-select" id="division_select" name="division_select" 
                                            <?= isset($user) ? 'disabled' : '' ?> required>
                                        <option value="">Select Division</option>
                                        <?php foreach ($allDivisions as $division): ?>
                                            <option value="<?= $division['id'] ?>" 
                                                    <?= (isset($userDivisions) && !empty($userDivisions) && $userDivisions[0]['id'] == $division['id']) ? 'selected' : '' ?>>
                                                <?= esc($division['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" id="division" name="division" 
                                           value="<?= (isset($userDivisions) && !empty($userDivisions)) ? $userDivisions[0]['id'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Role *</label>
                                    <select class="form-select" id="role_select" name="role_select" 
                                            <?= isset($user) ? 'disabled' : '' ?> required>
                                        <option value="">Select Role</option>
                                        <!-- Roles will be populated dynamically -->
                                    </select>
                                    <input type="hidden" id="role" name="role" 
                                           value="<?= (isset($userRoles) && !empty($userRoles)) ? $userRoles[0]['id'] : '' ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons (only for create) -->
                        <?php if (!isset($user)): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Create User
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($user)): ?>
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
            <form id="changePasswordForm" action="<?= base_url('admin/advanced-users/change-password/' . $user['id']) ?>" method="POST">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>User:</strong> <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['username']) ?>)
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password')">
                                <i class="fas fa-eye" id="new_password_icon"></i>
                            </button>
                        </div>
                        <div class="form-text">Password harus minimal 6 karakter dan mengandung minimal 1 huruf dan 1 angka</div>
                        <div class="mt-2" id="passwordStrength"></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('confirm_password')">
                                <i class="fas fa-eye" id="confirm_password_icon"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-2"></i>Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
<?php if (isset($user)): ?>
let isEditMode = false;
let originalData = {};

// Enable Edit Mode
function enableEdit() {
    isEditMode = true;
    
    // Store original data
    originalData = {
        first_name: $('#first_name').val(),
        last_name: $('#last_name').val(),
        username: $('#username').val(),
        email: $('#email').val(),
        phone: $('#phone').val(),
        status: $('#status').val(),
        division: $('#division').val() || $('#division_select').val(),
        role: $('#role').val() || $('#role_select').val()
    };
    
    // Enable editable fields
    $('#first_name, #last_name, #username, #email, #phone').prop('readonly', false).addClass('form-control-focus');
    $('#status, #division_select, #role_select').prop('disabled', false);
    
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
            $('#username').val(originalData.username);
            $('#email').val(originalData.email);
            $('#phone').val(originalData.phone);
            $('#status').val(originalData.status);
            $('#division_select').val(originalData.division);
            $('#division').val(originalData.division);
            
            // Disable fields
            $('#first_name, #last_name, #username, #email, #phone').prop('readonly', true).removeClass('form-control-focus');
            $('#status, #division_select, #role_select').prop('disabled', true);
            
            // Reload roles if division changed
            if (originalData.division) {
                updateRoles(originalData.division);
                setTimeout(function() {
                    $('#role_select').val(originalData.role);
                    $('#role').val(originalData.role);
                }, 100);
            }
            
            // Toggle buttons
            $('#editButtons').addClass('d-none');
            $('#actionButtons').removeClass('d-none');
        }
    });
}

// Save User
function saveUser() {
    if (!isEditMode) {
        Swal.fire({
            icon: 'warning',
            title: 'Not in Edit Mode',
            text: 'Please click "Update Data" first to edit user',
            confirmButtonColor: '#4e73df'
        });
        return;
    }
    
    // Validate
    var isValid = true;
    var errors = [];
    
    const divisionValue = $('#division').val() || $('#division_select').val();
    if (!divisionValue || divisionValue === '') {
        errors.push('Please select a division.');
        isValid = false;
    }
    
    const roleValue = $('#role').val() || $('#role_select').val();
    if (!roleValue || roleValue === '') {
        errors.push('Please select a role.');
        isValid = false;
    }
    
    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: errors.join('\n'),
            confirmButtonColor: '#e74a3b'
        });
        return;
    }
    
    // Prepare form data
    var formData = {
        first_name: $('#first_name').val(),
        last_name: $('#last_name').val(),
        username: $('#username').val(),
        email: $('#email').val(),
        phone: $('#phone').val(),
        status: $('#status').val(),
        is_active: $('#is_active').val(),
        division: $('#division').val(),
        role: $('#role').val()
    };
    
    // Show loading
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait while we update user',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit via AJAX
    $.ajax({
        url: '<?= base_url('admin/advanced-users/update/' . $user['id']) ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'User berhasil diperbarui',
                    confirmButtonColor: '#4e73df',
                    timer: 2000,
                    showConfirmButton: true
                }).then(() => {
                    isEditMode = false;
                    
                    // Disable fields
                    $('#first_name, #last_name, #username, #email, #phone').prop('readonly', true).removeClass('form-control-focus');
                    $('#status, #division_select, #role_select').prop('disabled', true);
                    
                    // Toggle buttons
                    $('#editButtons').addClass('d-none');
                    $('#actionButtons').removeClass('d-none');
                    
                    // Reload to show updated data
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    text: response.message || 'Failed to update user',
                    confirmButtonColor: '#e74a3b'
                });
            }
        },
        error: function(xhr, status, error) {
            var errorMessage = 'Failed to update user';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
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
<?php endif; ?>

$(document).ready(function() {
    console.log('jQuery loaded, document ready');
    
    // Division-Role mapping based on actual database IDs
    const allRoles = [
        // Marketing Division (ID: 0)
        { id: '2', name: 'Head Marketing', division: '0' },
        { id: '3', name: 'Staff Marketing', division: '0' },
        
        // Service Diesel Division (ID: 1)
        { id: '6', name: 'Head Service Diesel', division: '1' },
        { id: '7', name: 'Staff Service Diesel', division: '1' },
        
        // Service Electric Division (ID: 2)
        { id: '8', name: 'Head Service Electric', division: '2' },
        { id: '9', name: 'Staff Service Electric', division: '2' },
        
        // Warehouse Division (ID: 3)
        { id: '16', name: 'Head Warehouse', division: '3' },
        { id: '32', name: 'Staff Warehouse', division: '3' },
        
        // HRD Division (ID: 4)
        { id: '14', name: 'Head HRD', division: '4' },
        { id: '15', name: 'Staff HRD', division: '4' },
        
        // Administrator Division (ID: 5)
        { id: '30', name: 'Administrator', division: '5' },
        
        // Purchasing Division (ID: 6)
        { id: '10', name: 'Head Purchasing', division: '6' },
        { id: '11', name: 'Staff Purchasing', division: '6' },
        
        // IT Division (ID: 7)
        { id: '33', name: 'Head IT', division: '7' },
        { id: '34', name: 'Staff IT', division: '7' }
    ];

    // Get current user data for edit
    const userRoles = <?= isset($userRoles) ? json_encode(array_column($userRoles ?? [], 'id')) : '[]' ?>;
    const userDivision = <?= isset($userDivisions) && !empty($userDivisions) ? json_encode($userDivisions[0]['id']) : 'null' ?>;
    
    console.log('=== DEBUG USER DATA ===');
    console.log('userRoles:', userRoles);
    console.log('userDivision:', userDivision);
    console.log('userRoles type:', typeof userRoles);
    console.log('userRoles length:', userRoles.length);
    console.log('Raw userRoles from PHP:', <?= json_encode($userRoles ?? []) ?>);
    console.log('Raw userDivisions from PHP:', <?= json_encode($userDivisions ?? []) ?>);
    console.log('========================');

    // Function to update roles based on division
    function updateRoles(selectedDivision) {
        console.log('updateRoles called with division:', selectedDivision);
        console.log('userRoles for selection:', userRoles);
        const roleSelect = $('#role_select');
        roleSelect.empty();
        roleSelect.append('<option value="">Select Role</option>');
        
        if (selectedDivision) {
            const filteredRoles = allRoles.filter(role => role.division === selectedDivision);
            console.log('Filtered roles:', filteredRoles);
            
            filteredRoles.forEach(role => {
                // Convert both to string for comparison to avoid type issues
                const roleIdStr = role.id.toString();
                const userRolesStr = userRoles.map(id => id.toString());
                const isSelected = userRolesStr.includes(roleIdStr);
                console.log(`Role ${role.id} (${role.name}) - isSelected: ${isSelected}`);
                roleSelect.append(`<option value="${role.id}" ${isSelected ? 'selected' : ''}>${role.name}</option>`);
                if (isSelected) {
                    $('#role').val(role.id);
                }
            });
            
            // Force trigger change event to ensure selection is visible
            roleSelect.trigger('change');
        }
    }

    // Handle division selection
    $('#division_select').on('change', function() {
        console.log('Division changed to:', $(this).val());
        const selectedDivision = $(this).val();
        $('#division').val(selectedDivision);
        updateRoles(selectedDivision);
        
        // Update hidden is_active field based on status
        <?php if (isset($user)): ?>
        updateStatusField();
        <?php endif; ?>
    });
    
    // Handle role selection
    $('#role_select').on('change', function() {
        console.log('Role changed to:', $(this).val());
        const selectedRole = $(this).val();
        $('#role').val(selectedRole);
    });
    
    // Update status field
    <?php if (isset($user)): ?>
    function updateStatusField() {
        const statusValue = $('#status').val();
        $('#is_active').val(statusValue === 'active' ? '1' : '0');
    }
    
    $('#status').on('change', function() {
        updateStatusField();
    });
    
    // Initialize status field on load
    updateStatusField();
    <?php endif; ?>
    
    // Also trigger on input event
    $('#division').on('input', function() {
        console.log('Division input changed to:', $(this).val());
        const selectedDivision = $(this).val();
        updateRoles(selectedDivision);
    });

    // Initial load for edit form
    console.log('Initial load - userDivision:', userDivision);
    console.log('Initial load - userRoles:', userRoles);
    
    if (userDivision !== null) {
        console.log('Edit mode - loading roles for division:', userDivision);
        $('#division').val(userDivision);
        updateRoles(userDivision);
    } else {
        // For create form, check if division is already selected
        const currentDivision = $('#division_select').val();
        if (currentDivision) {
            console.log('Create mode - Division already selected:', currentDivision);
            $('#division').val(currentDivision);
            updateRoles(currentDivision);
        }
    }
    
    // Additional trigger for initial load
    setTimeout(function() {
        const currentDivision = $('#division_select').val();
        if (currentDivision && $('#role_select option').length <= 1) {
            console.log('Timeout trigger - Division selected:', currentDivision);
            $('#division').val(currentDivision);
            updateRoles(currentDivision);
        }
    }, 100);
    
    // Trigger on window load as well
    $(window).on('load', function() {
        const currentDivision = $('#division_select').val();
        if (currentDivision && $('#role_select option').length <= 1) {
            console.log('Window load trigger - Division selected:', currentDivision);
            $('#division').val(currentDivision);
            updateRoles(currentDivision);
        }
    });
    
    // Manual fallback - force load roles after 1 second
    setTimeout(function() {
        const currentDivision = $('#division_select').val();
        if (currentDivision && userRoles.length > 0) {
            console.log('Manual fallback - forcing role load');
            $('#division').val(currentDivision);
            updateRoles(currentDivision);
        }
    }, 1000);

    // Password toggle functionality (only for create)
    <?php if (!isset($user)): ?>
    $('#togglePassword').on('click', function() {
        const passwordField = $('#password');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    $('#togglePasswordConfirm').on('click', function() {
        const passwordField = $('#password_confirm');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    // Password strength indicator
    $('#password').on('input', function() {
        const password = $(this).val();
        const strength = checkPasswordStrength(password);
        const strengthHtml = getPasswordStrengthHtml(strength);
        $('#passwordStrength').html(strengthHtml);
    });

    // Password match indicator
    $('#password_confirm').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        const matchHtml = getPasswordMatchHtml(password, confirmPassword);
        $('#passwordMatch').html(matchHtml);
    });

    function checkPasswordStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return score;
    }

    function getPasswordStrengthHtml(strength) {
        const levels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        const colors = ['danger', 'warning', 'info', 'success', 'success'];
        const level = Math.min(strength, 4);
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
    <?php endif; ?>

    <?php if (isset($user)): ?>
    // Change Password Form Submission
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();
        
        // Validation
        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'New password and confirm password do not match',
                confirmButtonColor: '#e74a3b'
            });
            return;
        }
        
        if (newPassword.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Password must be at least 6 characters long',
                confirmButtonColor: '#e74a3b'
            });
            return;
        }
        
        // Check password strength
        if (!/[a-zA-Z]/.test(newPassword) || !/[0-9]/.test(newPassword)) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Password must contain at least one letter and one number',
                confirmButtonColor: '#e74a3b'
            });
            return;
        }
        
        // Submit via AJAX
        const form = this;
        
        // Show loading
        Swal.fire({
            title: 'Changing Password...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                new_password: newPassword,
                confirm_password: confirmPassword
            },
            dataType: 'json',
            timeout: 30000, // 30 seconds timeout
            success: function(response) {
                // Close loading Swal first
                Swal.close();
                
                if (response && response.success) {
                    // Close modal first
                    $('#changePasswordModal').modal('hide');
                    
                    // Reset form
                    $('#changePasswordForm')[0].reset();
                    $('#passwordStrength').html('');
                    $('#passwordMatch').html('');
                    
                    // Show success notification (using existing notification system)
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification(response.message || 'Password berhasil diubah', 'success', 3000);
                    } else {
                        // Fallback to Swal if notification system not available
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Password berhasil diubah',
                            confirmButtonColor: '#4e73df',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    // Show error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: (response && response.message) ? response.message : 'Failed to change password',
                        confirmButtonColor: '#e74a3b'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Close loading Swal first
                Swal.close();
                
                console.error('Change password error:', {
                    status: status,
                    error: error,
                    statusCode: xhr.status,
                    responseText: xhr.responseText,
                    responseJSON: xhr.responseJSON
                });
                
                var errorMessage = 'Failed to change password';
                
                // Try to get error message from response
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        } else if (response.errors) {
                            // Handle validation errors
                            var errors = response.errors;
                            if (typeof errors === 'object') {
                                errorMessage = Object.values(errors).join(', ');
                            }
                        }
                    } catch (e) {
                        // If not JSON, check status code
                        if (xhr.status === 0) {
                            errorMessage = 'Network error. Please check your connection.';
                        } else if (xhr.status === 403) {
                            errorMessage = 'Access denied. You do not have permission to change password.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'User not found.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error occurred. Please try again.';
                        } else if (xhr.status === 400) {
                            errorMessage = 'Invalid request. Please check your input.';
                        } else {
                            errorMessage = 'Error: ' + (error || 'Unknown error');
                        }
                    }
                }
                
                // Show error notification
                if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification(errorMessage, 'danger', 5000);
                } else {
                    // Fallback to Swal
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                        confirmButtonColor: '#e74a3b'
                    });
                }
            },
            complete: function() {
                // Ensure loading is closed in case of any issues
                Swal.close();
            }
        });
    });
    
    // Password strength indicator for modal
    $('#new_password').on('input', function() {
        const password = $(this).val();
        let strength = 0;
        let strengthText = '';
        let strengthClass = '';
        
        if (password.length >= 6) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
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
            case 5:
                strengthText = 'Strong';
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
    
    // Password match indicator
    $('#confirm_password').on('input', function() {
        const password = $('#new_password').val();
        const confirmPassword = $(this).val();
        
        if (confirmPassword === '') {
            $('#passwordMatch').html('');
            return;
        }
        
        if (password === confirmPassword) {
            $('#passwordMatch').html('<span class="text-success"><i class="fas fa-check me-1"></i>Passwords match</span>');
        } else {
            $('#passwordMatch').html('<span class="text-danger"><i class="fas fa-times me-1"></i>Passwords do not match</span>');
        }
    });
    
    // Reset password form when modal is closed
    $('#changePasswordModal').on('hidden.bs.modal', function () {
        $('#changePasswordForm')[0].reset();
        $('#passwordStrength').html('');
        $('#passwordMatch').html('');
        $('#new_password, #confirm_password').attr('type', 'password');
        $('#new_password_icon, #confirm_password_icon').removeClass('fa-eye-slash').addClass('fa-eye');
    });
    <?php endif; ?>
    
    // Form validation and AJAX submission (only for create)
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        var isValid = true;
        var errors = [];
        
        // Check if division is selected
        const divisionValue = $('#division').val() || $('#division_select').val();
        if (!divisionValue || divisionValue === '') {
            errors.push('Please select a division.');
            isValid = false;
        }
        
        // Check if role is selected
        const roleValue = $('#role').val() || $('#role_select').val();
        if (!roleValue || roleValue === '') {
            errors.push('Please select a role.');
            isValid = false;
        }
        
        // Password validation for create form
        <?php if (!isset($user)): ?>
        if ($('#password').val() !== $('#password_confirm').val()) {
            errors.push('Passwords do not match.');
            isValid = false;
        }
        <?php endif; ?>
        
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
                        OptimaPro.showNotification(response.message || 'User berhasil disimpan', 'success', 3000);
                    }
                    setTimeout(function() {
                        window.location.href = '<?= base_url('admin/advanced-users') ?>';
                    }, 1500);
                } else {
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification(response.message || 'Failed to save user', 'danger', 5000);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to save user'));
                    }
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = 'Failed to save user';
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
    
    // Add focus styling for editable fields
    $('<style>')
        .prop('type', 'text/css')
        .html('.form-control-focus { border-color: #4e73df !important; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important; }')
        .appendTo('head');
});
</script>
<?= $this->endSection() ?>
