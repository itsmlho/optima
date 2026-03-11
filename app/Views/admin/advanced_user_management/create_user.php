<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-plus me-2"></i>Create New User
                    </h1>
                    <p class="text-muted mb-0">Add a new user to the system</p>
                </div>
                <div>
                    <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Create User Form -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form id="createUserForm" action="<?= base_url('admin/advanced-users/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <!-- Personal Information Section -->
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
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-lock me-2"></i>Password
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="passwordStrength" class="mt-1"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="passwordMatch" class="mt-1"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Division & Role Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-users-cog me-2"></i>Division & Role
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="division_select" class="form-label">Division <span class="text-danger">*</span></label>
                                    <select class="form-select" id="division_select" name="division" required>
                                        <option value="">Select Division</option>
                                        <?php if (isset($divisions) && !empty($divisions)): ?>
                                            <?php foreach ($divisions as $division): ?>
                                                <option value="<?= $division['id'] ?>"><?= esc($division['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <input type="hidden" id="division" name="division">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role_select" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role_select" name="role" required disabled>
                                        <option value="">Select Division First</option>
                                    </select>
                                    <input type="hidden" id="role" name="role">
                                </div>
                            </div>
                        </div>

                        <!-- Service Area & Branch Management (only for Service division) -->
                        <div id="serviceAccessSection" class="d-none border border-success rounded p-4 bg-light">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-success mb-3 border-bottom border-success pb-2">
                                        <i class="fas fa-wrench me-2"></i>Service Area & Branch Access
                                        <small class="text-muted">(Khusus untuk divisi Service)</small>
                                    </h5>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Area Type</label>
                                        <select class="form-select" id="area_type" name="area_type">
                                            <option value="">Select Area Type</option>
                                            <option value="CENTRAL">CENTRAL (Admin Service Pusat)</option>
                                            <option value="BRANCH">BRANCH (Admin Service Area)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Central Access Section -->
                            <div id="centralAccessSection" class="card border-info" style="display: none;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="department_scope" class="form-label">Department Scope</label>
                                                <select class="form-select" id="department_scope" name="department_scope">
                                                    <option value="">Select Department</option>
                                                    <option value="ELECTRIC">ELECTRIC</option>
                                                    <option value="DIESEL_GASOLINE">DIESEL + GASOLINE</option>
                                                    <option value="ALL">ALL DEPARTMENTS</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Branch Access Section -->
                            <div id="branchAccessSection" class="card border-warning" style="display: none;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Service Areas</label>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">Select specific service areas for branch access</small>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectServiceAreas">
                                                        <i class="fas fa-map-marked-alt me-1"></i>Select Areas
                                                    </button>
                                                </div>
                                                <div id="selectedServiceAreasDisplay" class="border rounded p-2 bg-light">
                                                    <span class="text-muted">No areas selected</span>
                                                </div>
                                                <input type="hidden" id="selectedServiceAreas" name="service_areas">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Create User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Area Selection Modal -->
<div class="modal fade" id="serviceAreasModal" tabindex="-1" aria-labelledby="serviceAreasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceAreasModalLabel">
                    <i class="fas fa-map-marked-alt me-2"></i>Select Service Areas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Search Areas</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="serviceAreaSearch" placeholder="Type to search areas...">
                    </div>
                </div>
                
                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                    <div id="serviceAreasList" class="row">
                        <!-- Service areas will be loaded here -->
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2">Loading areas...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveServiceAreas">
                    <i class="fas fa-save me-2"></i>Save Selection
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- ========================================
     📊 JAVASCRIPT DATA VARIABLES
     ======================================== -->
<script>
// NOTE: BASE_URL already defined in base layout - no need to redeclare
const USERS_LIST_URL = BASE_URL + 'admin/advanced-users';
const CSRF_TOKEN = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

// Form mode detection
const IS_EDIT_MODE = false;

// Roles data for dynamic filtering
console.log('🔍 [DEBUG] Roles data from PHP:', <?= json_encode($roles ?? []) ?>);
const ROLES_DATA = <?= json_encode($roles ?? []) ?>;

// No user data for create mode
const USER_ID = null;
const USER_DATA = null;

console.log('📊 Create User Form initialized:', {
    mode: 'CREATE',
    user_id: USER_ID,
    roles_count: ROLES_DATA?.length || 0,
    roles_data: ROLES_DATA,
    divisions_available: <?= json_encode(isset($divisions) ? count($divisions) : 0) ?>
});
</script>

<!-- ========================================
     🚀 COMPLETE INLINE JAVASCRIPT FOR CREATE USER
     ======================================== -->
<script>
// ========================================
// 🌐 GLOBAL VARIABLES
// ========================================
let selectedServiceAreasIds = [];
let serviceAreasData = [];

// ========================================
// 🔧 DIVISION & ROLE MANAGEMENT
// ========================================
function initializeDivisionHandlers() {
    const divisionSelect = $('#division_select');
    const roleSelect = $('#role_select');
    const serviceSection = $('#serviceAccessSection');
    
    // Division change handler
    divisionSelect.on('change', function() {
        const selectedDivision = $(this).val();
        const selectedDivisionName = $(this).find('option:selected').text().toLowerCase();
        
        console.log('🏢 Division changed:', selectedDivision, selectedDivisionName);
        
        // Update hidden field
        $('#division').val(selectedDivision);
        
        // Reset role selection
        roleSelect.empty().append('<option value="">Select Role</option>');
        $('#role').val('');
        
        if (selectedDivision) {
            // Load roles for the selected division
            loadRolesForDivision(selectedDivision);
            
            // Show/hide service section based on division
            if (selectedDivisionName.includes('service')) {
                serviceSection.removeClass('d-none');
                console.log('🔧 Service section shown for Service division');
            } else {
                serviceSection.addClass('d-none');
                resetServiceFields();
            }
        } else {
            roleSelect.prop('disabled', true);
            serviceSection.addClass('d-none');
            resetServiceFields();
        }
    });
    
    // Role change handler
    roleSelect.on('change', function() {
        const selectedRole = $(this).val();
        $('#role').val(selectedRole);
        handleRoleBasedUI();
    });
}

function loadRolesForDivision(divisionId, selectedRoleId = null) {
    const roleSelect = $('#role_select');
    
    // Clear current roles
    roleSelect.empty().append('<option value="">Select Role</option>');
    $('#role').val('');
    
    if (divisionId && ROLES_DATA && ROLES_DATA.length > 0) {
        // Filter roles for the division
        const filteredRoles = ROLES_DATA.filter(role => {
            // Handle both string and number comparison
            return role.division_id == divisionId || role.division == divisionId;
        });
        
        console.log('🔍 Filtered roles for division', divisionId, ':', filteredRoles);
        
        filteredRoles.forEach(role => {
            const selected = selectedRoleId && role.id == selectedRoleId ? 'selected' : '';
            roleSelect.append(`<option value="${role.id}" ${selected}>${role.name}</option>`);
        });
        
        roleSelect.prop('disabled', false);
        
        // Set hidden field if role was selected
        if (selectedRoleId) {
            $('#role').val(selectedRoleId);
        }
    } else {
        console.warn('⚠️ No division selected or no roles data available');
        roleSelect.prop('disabled', true);
    }
}

function handleRoleBasedUI() {
    const selectedDivision = $('#division_select option:selected').text().toLowerCase();
    const selectedRole = $('#role_select option:selected').text().toLowerCase();
    const serviceSection = $('#serviceAccessSection');
    
    // Show service section for service-related roles
    if (selectedDivision.includes('service') || selectedRole.includes('service')) {
        serviceSection.removeClass('d-none');
        console.log('🔧 Service section shown for service role');
    }
}

function resetServiceFields() {
    $('#area_type').val('');
    $('#department_scope').val('');
    $('#selectedServiceAreas').val('');
    selectedServiceAreasIds = [];
    updateSelectedServiceAreasDisplay();
    $('#centralAccessSection, #branchAccessSection').hide();
}

// ========================================
// 🗺️ SERVICE AREA MANAGEMENT
// ========================================
function initializeServiceAreaHandlers() {
    // Area type change handler
    $('#area_type').on('change', function() {
        const areaType = $(this).val();
        
        if (areaType === 'CENTRAL') {
            $('#centralAccessSection').show();
            $('#branchAccessSection').hide();
        } else if (areaType === 'BRANCH') {
            $('#centralAccessSection').hide();
            $('#branchAccessSection').show();
        } else {
            $('#centralAccessSection, #branchAccessSection').hide();
        }
    });
    
    // Service areas button handler
    $('#btnSelectServiceAreas').on('click', function() {
        loadServiceAreas();
        $('#serviceAreasModal').modal('show');
    });
    
    // Modal save button
    $('#btnSaveServiceAreas').on('click', confirmAreaSelection);
    
    // Search functionality
    $('#serviceAreaSearch').on('input', filterServiceAreas);
}

function loadServiceAreas() {
    const loadingHtml = `
        <div class="text-center p-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span class="ms-2">Loading areas...</span>
        </div>
    `;
    
    $('#serviceAreasList').html(loadingHtml);
    
    $.ajax({
        url: '<?= base_url("admin/advanced-users/get-service-areas") ?>',
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF_HASH
        },
        dataType: 'json',
        beforeSend: function(xhr) {
            console.log('🔗 Request URL:', '<?= base_url("admin/advanced-users/get-service-areas") ?>');
            console.log('🔑 CSRF Token:', CSRF_HASH);
        },
        success: function(response) {
            console.log('🗺️ Service areas response:', response);
            if (response && response.success && response.data) {
                serviceAreasData = response.data;
                renderServiceAreas(serviceAreasData);
            } else {
                showServiceAreasError('No service areas available');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Service areas AJAX error details:');
            console.error('📊 Status:', status);
            console.error('🚨 Error:', error);
            console.error('📄 Response Text:', xhr.responseText);
            console.error('🔢 Status Code:', xhr.status);
            console.error('📋 Ready State:', xhr.readyState);
            
            // Try to parse error response as JSON
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                console.error('📝 Parsed Error Response:', errorResponse);
            } catch (parseError) {
                console.error('❓ Could not parse error response as JSON');
            }
            console.error('❌ Service areas error:', xhr.responseText);
            
            // Show user-friendly error message
            showServiceAreasError('Failed to load service areas. Please check your connection and try again.');
        }
    });
}

function renderServiceAreas(areas) {
    let html = '';
    
    areas.forEach(area => {
        const isChecked = selectedServiceAreasIds.includes(area.id) ? 'checked' : '';
        const selectedClass = isChecked ? 'border-primary bg-light' : '';
        html += `
            <div class="col-md-6 mb-2">
                <div class="card ${selectedClass} service-area-item" style="cursor: pointer; transition: all 0.2s;">
                    <div class="card-body p-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   value="${area.id}" id="area_${area.id}" ${isChecked}>
                            <label class="form-check-label" for="area_${area.id}">
                                <strong>${area.name}</strong>
                                ${area.description ? `<br><small class="text-muted">${area.description}</small>` : ''}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    if (html === '') {
        html = '<div class="col-12 text-center p-3"><span class="text-muted">No service areas found</span></div>';
    }
    
    $('#serviceAreasList').html(html);
    
    // Add click handlers for cards
    $('.service-area-item').on('click', function() {
        const checkbox = $(this).find('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked'));
        $(this).toggleClass('border-primary bg-light', checkbox.prop('checked'));
    });
}

function filterServiceAreas() {
    const searchTerm = $('#serviceAreaSearch').val().toLowerCase();
    const filteredAreas = serviceAreasData.filter(area => 
        area.name.toLowerCase().includes(searchTerm) ||
        (area.description && area.description.toLowerCase().includes(searchTerm))
    );
    renderServiceAreas(filteredAreas);
}

function showServiceAreasError(message) {
    $('#serviceAreasList').html(`
        <div class="col-12 text-center p-3">
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>${message}
            </div>
        </div>
    `);
}

// Service area modal functions
function selectAllAreas() {
    $('#serviceAreasList input[type="checkbox"]').prop('checked', true);
    $('.service-area-item').addClass('border-primary bg-light');
}

function clearAllAreas() {
    $('#serviceAreasList input[type="checkbox"]').prop('checked', false);
    $('.service-area-item').removeClass('border-primary bg-light');
}

function confirmAreaSelection() {
    const selectedIds = [];
    $('#serviceAreasList input[type="checkbox"]:checked').each(function() {
        selectedIds.push(parseInt($(this).val()));
    });
    
    selectedServiceAreasIds = selectedIds;
    updateSelectedServiceAreasDisplay();
    $('#selectedServiceAreas').val(selectedIds.join(','));
    $('#serviceAreasModal').modal('hide');
    
    console.log('🗺️ Selected service areas:', selectedIds);
}

function updateSelectedServiceAreasDisplay() {
    const count = selectedServiceAreasIds?.length || 0;
    const display = $('#selectedServiceAreasDisplay');
    
    if (count > 0) {
        display.html(`<span class="badge bg-primary">${count} area(s) selected</span>`);
    } else {
        display.html('<span class="text-muted">No areas selected</span>');
    }
}

// ========================================
// 🔐 PASSWORD MANAGEMENT
// ========================================
function initializePasswordHandlers() {
    // Toggle password visibility
    $('#togglePassword').on('click', function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    $('#togglePasswordConfirm').on('click', function() {
        const passwordField = $('#password_confirm');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Password strength checker
    $('#password').on('input', checkPasswordStrength);
    $('#password_confirm').on('input', checkPasswordMatch);
}

function checkPasswordStrength() {
    const password = $('#password').val();
    const strengthDiv = $('#passwordStrength');
    
    if (!password) {
        strengthDiv.empty();
        return;
    }
    
    let score = 0;
    let feedback = [];
    
    // Length check
    if (password.length >= 8) score++;
    else feedback.push('At least 8 characters');
    
    // Uppercase check
    if (/[A-Z]/.test(password)) score++;
    else feedback.push('One uppercase letter');
    
    // Lowercase check
    if (/[a-z]/.test(password)) score++;
    else feedback.push('One lowercase letter');
    
    // Number check
    if (/\d/.test(password)) score++;
    else feedback.push('One number');
    
    // Special character check
    if (/[^A-Za-z0-9]/.test(password)) score++;
    else feedback.push('One special character');
    
    let strengthText, strengthClass;
    
    if (score >= 5) {
        strengthText = 'Very Strong';
        strengthClass = 'text-success';
    } else if (score >= 4) {
        strengthText = 'Strong';
        strengthClass = 'text-info';
    } else if (score >= 3) {
        strengthText = 'Medium';
        strengthClass = 'text-warning';
    } else {
        strengthText = 'Weak';
        strengthClass = 'text-danger';
    }
    
    let html = `<small class="${strengthClass}">Password strength: ${strengthText}</small>`;
    
    if (feedback.length > 0) {
        html += `<br><small class="text-muted">Missing: ${feedback.join(', ')}</small>`;
    }
    
    strengthDiv.html(html);
}

function checkPasswordMatch() {
    const password = $('#password').val();
    const confirmPassword = $('#password_confirm').val();
    const matchDiv = $('#passwordMatch');
    
    if (!confirmPassword) {
        matchDiv.empty();
        return;
    }
    
    if (password === confirmPassword) {
        matchDiv.html('<small class="text-success">Passwords match</small>');
    } else {
        matchDiv.html('<small class="text-danger">Passwords do not match</small>');
    }
}

// ========================================
// 📝 FORM SUBMISSION
// ========================================
function initializeFormSubmission() {
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...');
        
        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    showSuccessMessage('User created successfully!');
                    setTimeout(() => {
                        window.location.href = USERS_LIST_URL;
                    }, 1500);
                } else {
                    showErrorMessage(response.message || 'Failed to create user');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Network error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showErrorMessage(errorMessage);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
}

function validateForm() {
    const requiredFields = ['first_name', 'last_name', 'username', 'email', 'password', 'password_confirm', 'division', 'role'];
    let isValid = true;
    
    // Check required fields
    requiredFields.forEach(field => {
        const value = $(`#${field}`).val() || $(`#${field}_select`).val();
        if (!value || value.trim() === '') {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });
    
    // Check password match
    const password = $('#password').val();
    const confirmPassword = $('#password_confirm').val();
    
    if (password !== confirmPassword) {
        showFieldError('password_confirm', 'Passwords do not match');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(fieldName, message) {
    const field = $(`#${fieldName}, #${fieldName}_select`);
    field.addClass('is-invalid');
    
    // Remove existing error message
    field.siblings('.invalid-feedback').remove();
    
    // Add error message
    field.after(`<div class="invalid-feedback">${message}</div>`);
}

function clearFieldError(fieldName) {
    const field = $(`#${fieldName}, #${fieldName}_select`);
    field.removeClass('is-invalid');
    field.siblings('.invalid-feedback').remove();
}

// ========================================
// 🎨 UI HELPERS
// ========================================
function showSuccessMessage(message) {
    const alert = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container-fluid').prepend(alert);
    
    // Auto dismiss after 3 seconds
    setTimeout(() => {
        $('.alert-success').alert('close');
    }, 3000);
}

function showErrorMessage(message) {
    const alert = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container-fluid').prepend(alert);
    
    // Auto scroll to top
    $('html, body').animate({ scrollTop: 0 }, 300);
}

// ========================================
// 🚀 INITIALIZATION
// ========================================
$(document).ready(function() {
    console.log('🎯 Initializing Create User Form...');
    
    // Initialize all handlers
    initializeDivisionHandlers();
    initializeServiceAreaHandlers();
    initializePasswordHandlers();
    initializeFormSubmission();
    
    // Add focus styling using global CSS classes
    $('.form-control, .form-select').on('focus', function() {
        $(this).addClass('shadow-sm');
    }).on('blur', function() {
        $(this).removeClass('shadow-sm');
    });
    
    console.log('✅ Create User Form initialized successfully!');
});
</script>
<?= $this->endSection() ?>
