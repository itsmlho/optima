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
                    <li class="breadcrumb-item active">Create User</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <!-- Create User Form -->
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Create New User</h4>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/advanced-users/store') ?>" method="post" id="createUserForm">
                <?= csrf_field() ?>
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?= old('first_name') ?>" minlength="2" required>
                                    <div class="form-text">Minimum 2 characters</div>
                                </div>

                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?= old('last_name') ?>" minlength="2" required>
                                    <div class="form-text">Minimum 2 characters</div>
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= old('username') ?>" minlength="3" required>
                                    <div class="form-text">Minimum 3 characters</div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= old('email') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?= old('phone') ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                                    <div class="form-text">Minimum 6 characters</div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                                    <div class="form-text">Must match password above</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles and Permissions -->
                    <div class="col-md-6">
                        <!-- Roles -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Roles</h5>
                            </div>
                            <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                                <?php foreach ($allRoles as $role): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $role['id'] ?>"
                                        <?= in_array($role['id'], old('roles', [])) ? 'checked' : '' ?> >
                                    <label class="form-check-label"><?= esc($role['name']) ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Divisions -->
                        <div class="card mb-3 mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Divisions</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($allDivisions as $division): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="divisions[]" value="<?= $division['id'] ?>" id="division<?= $division['id'] ?>"
                                        <?= in_array($division['id'], old('divisions', [])) ? 'checked' : '' ?> >
                                    <label class="form-check-label" for="division<?= $division['id'] ?>"><?= esc($division['name']) ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12 text-end">
                        <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
.form-check {
    margin-bottom: 0.5rem;
}

.form-check .form-check-label {
    margin-left: 0.25rem;
}

.card .card-header h5 {
    color: #495057;
}

.form-text {
    font-size: 0.875em;
    color: #6c757d;
}

.is-invalid {
    border-color: #dc3545;
}

.is-valid {
    border-color: #28a745;
}

.form-control:focus.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control:focus.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Show division head option when division is selected
    $('input[name="divisions[]"]').change(function() {
        var divisionId = $(this).val();
        var headOption = $('#head-option-' + divisionId);
        
        if ($(this).is(':checked')) {
            headOption.show();
        } else {
            headOption.hide();
            $('#head' + divisionId).prop('checked', false);
        }
    });

    // Initialize division head options for already selected divisions
    $('input[name="divisions[]"]:checked').each(function() {
        var divisionId = $(this).val();
        $('#head-option-' + divisionId).show();
    });

    // Form validation
    $('#createUserForm').on('submit', function(e) {
        var isValid = true;
        var errors = [];
        
        // Check required fields with minimum length
        var firstName = $('#first_name').val().trim();
        var lastName = $('#last_name').val().trim();
        var username = $('#username').val().trim();
        var email = $('#email').val().trim();
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        
        // Validate first name
        if (firstName.length < 2) {
            errors.push('First name must be at least 2 characters long.');
            $('#first_name').addClass('is-invalid');
            isValid = false;
        } else {
            $('#first_name').removeClass('is-invalid');
        }
        
        // Validate last name
        if (lastName.length < 2) {
            errors.push('Last name must be at least 2 characters long.');
            $('#last_name').addClass('is-invalid');
            isValid = false;
        } else {
            $('#last_name').removeClass('is-invalid');
        }
        
        // Validate username
        if (username.length < 3) {
            errors.push('Username must be at least 3 characters long.');
            $('#username').addClass('is-invalid');
            isValid = false;
        } else {
            $('#username').removeClass('is-invalid');
        }
        
        // Validate email
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            errors.push('Please enter a valid email address.');
            $('#email').addClass('is-invalid');
            isValid = false;
        } else {
            $('#email').removeClass('is-invalid');
        }
        
        // Validate password
        if (password.length < 6) {
            errors.push('Password must be at least 6 characters long.');
            $('#password').addClass('is-invalid');
            isValid = false;
        } else {
            $('#password').removeClass('is-invalid');
        }
        
        // Validate password confirmation
        if (password !== confirmPassword) {
            errors.push('Password and Confirm Password must match.');
            $('#confirm_password').addClass('is-invalid');
            isValid = false;
        } else {
            $('#confirm_password').removeClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n• ' + errors.join('\n• '));
            return false;
        }
    });
    
    // Real-time validation feedback
    $('#first_name, #last_name, #username, #password, #confirm_password').on('blur', function() {
        var field = $(this);
        var value = field.val().trim();
        var minLength = field.attr('minlength') || 0;
        
        if (value.length < minLength) {
            field.addClass('is-invalid');
        } else {
            field.removeClass('is-invalid');
        }
    });
    
    // Real-time password match validation
    $('#confirm_password').on('keyup', function() {
        var password = $('#password').val();
        var confirmPassword = $(this).val();
        
        if (confirmPassword.length > 0) {
            if (password === confirmPassword) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });
});
</script>
<?= $this->endSection() ?>
