<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<?php
// Load permission helper for conditional form elements
helper('permission_helper');
?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-edit me-2"></i>Edit User
                    </h1>
                    <p class="text-muted mb-0">Modify user information and permissions</p>
                </div>
                <div>
                    <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Form -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form id="editUserForm" action="<?= base_url('admin/advanced-users/update/' . $user['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        
                        <!-- Personal Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                    <small class="text-muted">(User ID: <?= $user['id'] ?>)</small>
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?= esc($user['first_name']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?= esc($user['last_name']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= esc($user['username']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= esc($user['email']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?= esc($user['phone'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= ($user['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <?php if (hasPermission('service.user.edit_password') || hasPermission('admin.manage')): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-lock me-2"></i>Password
                                    <small class="text-muted">(Leave blank to keep current password)</small>
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="passwordStrength" class="mt-1"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirm" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="passwordMatch" class="mt-1"></div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-lock me-2"></i>
                                    You do not have permission to change passwords for this user.
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

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
                                                <option value="<?= $division['id'] ?>" 
                                                    <?= ($user['division'] ?? '') == $division['id'] ? 'selected' : '' ?>>
                                                    <?= esc($division['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <input type="hidden" id="division" name="division" value="<?= $user['division'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role_select" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role_select" name="role" required>
                                        <option value="">Select Role</option>
                                        <!-- Will be populated by JavaScript -->
                                    </select>
                                    <input type="hidden" id="role" name="role" value="<?= $user['role'] ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Service Area & Branch Management (only for Service division) -->
                        <div id="serviceAccessSection" 
                             class="d-none border border-success rounded p-4 bg-light">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-success mb-3 border-bottom border-success pb-2">
                                        <i class="fas fa-wrench me-2"></i>Service Area & Branch Access
                                        <small class="text-muted">(Khusus untuk divisi Service)</small>
                                    </h5>
                                    <?php if (!hasPermission('service.user.assign_area')): ?>
                                    <div class="alert alert-warning alert-sm">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        You do not have permission to modify service area assignments
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Area Type</label>
                                        <select class="form-select" id="area_type" name="area_type"
                                                <?= !hasPermission('service.user.assign_area') ? 'disabled' : '' ?>>
                                            <option value="">Select Area Type</option>
                                            <option value="CENTRAL" 
                                                <?= ($user['area_type'] ?? '') === 'CENTRAL' ? 'selected' : '' ?>>
                                                CENTRAL (Admin Service Pusat)
                                            </option>
                                            <option value="BRANCH" 
                                                <?= ($user['area_type'] ?? '') === 'BRANCH' ? 'selected' : '' ?>>
                                                BRANCH (Admin Service Area)
                                            </option>
                                        </select>
                                        <?php if (!hasPermission('service.user.assign_area')): ?>
                                        <small class="text-muted">Area type assignment is read-only for your role</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Central Access Section -->
                            <div id="centralAccessSection" class="card border-info"
                                 style="display: <?= ($user['area_type'] ?? '') === 'CENTRAL' ? 'block' : 'none' ?>;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="department_scope" class="form-label">Department Scope</label>
                                                <select class="form-select" id="department_scope" name="department_scope"
                                                        <?= !hasPermission('service.user.assign_area') ? 'disabled' : '' ?>>
                                                    <option value="">Select Department</option>
                                                    <option value="ELECTRIC" 
                                                        <?= ($user['department_scope'] ?? '') === 'ELECTRIC' ? 'selected' : '' ?>>
                                                        ELECTRIC
                                                    </option>
                                                    <option value="DIESEL_GASOLINE" 
                                                        <?= ($user['department_scope'] ?? '') === 'DIESEL_GASOLINE' ? 'selected' : '' ?>>
                                                        DIESEL + GASOLINE
                                                    </option>
                                                    <option value="ALL" 
                                                        <?= ($user['department_scope'] ?? '') === 'ALL' ? 'selected' : '' ?>>
                                                        ALL DEPARTMENTS
                                                    </option>
                                                </select>
                                                <?php if (!hasPermission('service.user.assign_area')): ?>
                                                <small class="text-muted">Department scope is read-only for your role</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Branch Access Section -->
                            <div id="branchAccessSection" class="card border-warning"
                                 style="display: <?= ($user['area_type'] ?? '') === 'BRANCH' ? 'block' : 'none' ?>;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Service Areas</label>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">Select specific service areas for branch access</small>
                                                    <?php if (hasPermission('service.user.assign_area')): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectServiceAreas">
                                                        <i class="fas fa-map-marked-alt me-1"></i>Manage Areas
                                                    </button>
                                                    <?php else: ?>
                                                    <small class="text-muted">(Read-only)</small>
                                                    <?php endif; ?>
                                                </div>
                                                <div id="selectedServiceAreasDisplay" class="border rounded p-2 bg-light">
                                                    <span class="text-muted">Loading current areas...</span>
                                                </div>
                                                <input type="hidden" id="selectedServiceAreas" name="service_areas" 
                                                       value="<?= esc($user['service_areas'] ?? '') ?>">
                                                <?php if (!hasPermission('service.user.assign_area') && !hasPermission('admin.manage')): ?>
                                                <small class="text-warning mt-1 d-block">
                                                    <i class="fas fa-lock me-1"></i>
                                                    You do not have permission to modify service area assignments
                                                </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </br>
                        <!-- Custom Permissions Section -->
                        <?php if (hasPermission('service.user.assign_permissions') || hasPermission('admin.manage')): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="text-warning mb-0 border-bottom border-warning pb-2">
                                        <i class="fas fa-key me-2"></i>Custom Permissions
                                        <small class="text-muted">(Optional overrides)</small>
                                    </h5>
                                    <button type="button" class="btn btn-sm btn-outline-warning" id="btnManagePermissions">
                                        <i class="fas fa-cogs me-1"></i>Manage Permissions
                                    </button>
                                </div>
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <div id="customPermissionsDisplay">
                                            <span class="text-muted">Loading current permissions...</span>
                                        </div>
                                        <input type="hidden" id="customPermissions" name="custom_permissions" 
                                               value="<?= esc(json_encode($userPermissions ?? [])) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-secondary">
                                    <div class="card-body text-center">
                                        <i class="fas fa-lock text-muted mb-2" style="font-size: 2rem;"></i>
                                        <h6 class="text-muted">Custom Permissions</h6>
                                        <p class="text-muted mb-0">
                                            You do not have permission to manage custom permissions for this user.
                                            User will inherit permissions from their assigned role only.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <?php if (hasPermission('service.user.edit')): ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update User
                                    </button>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-secondary" disabled>
                                        <i class="fas fa-lock me-2"></i>No Edit Permission
                                    </button>
                                    <?php endif; ?>
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
                    <i class="fas fa-map-marked-alt me-2"></i>Manage Service Areas
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

<!-- Custom Permissions Modal -->
<div class="modal fade modal-wide" id="customPermissionsModal" tabindex="-1" aria-labelledby="customPermissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="customPermissionsModalLabel">
                    <i class="fas fa-key me-2"></i>Manage Custom Permissions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Search Permissions</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="permissionSearch" placeholder="Type to search permissions...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Filter by Module</label>
                        <select class="form-select" id="moduleFilter">
                            <option value="">All Modules</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Filter by Page</label>
                        <select class="form-select" id="pageFilter">
                            <option value="">All Pages</option>
                        </select>
                    </div>
                </div>
                
                <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                    <div id="permissionsList">
                        <!-- Permissions will be loaded here -->
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                            <span class="ms-2">Loading permissions...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <button type="button" class="btn btn-sm btn-outline-success" id="btnSelectAllPermissions">
                        <i class="fas fa-check-double me-1"></i>Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnClearAllPermissions">
                        <i class="fas fa-times me-1"></i>Clear All
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="btnSavePermissions">
                    <i class="fas fa-save me-2"></i>Save Permissions
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
// Base configuration (BASE_URL already defined in layouts/base.php)
const USERS_LIST_URL = '<?= base_url('admin/advanced-users') ?>';
const CSRF_TOKEN = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

// Form mode detection
const IS_EDIT_MODE = true;

// Roles data for dynamic filtering
const ROLES_DATA = <?= json_encode($roles ?? []) ?>;

// User data for edit mode
const USER_ID = <?= $user['id'] ?>;
const USER_DATA = {
    id: <?= $user['id'] ?>,
    first_name: "<?= esc($user['first_name']) ?>",
    last_name: "<?= esc($user['last_name']) ?>",
    username: "<?= esc($user['username']) ?>",
    email: "<?= esc($user['email']) ?>",
    phone: "<?= esc($user['phone'] ?? '') ?>",
    status: "<?= esc($user['status'] ?? 'active') ?>",
    division: "<?= $user['division'] ?? '' ?>",
    division_name: "<?= esc($user['division_name'] ?? '') ?>",
    role: "<?= $user['role'] ?? '' ?>",
    role_name: "<?= esc($user['role_name'] ?? '') ?>"
};

// Service access data for existing user
const SERVICE_ACCESS_DATA = <?= json_encode($userServiceAccess ?? []) ?>;

// Permissions data for existing user
const USER_PERMISSIONS_DATA = <?= json_encode($userPermissions ?? []) ?>;
const ALL_PERMISSIONS_DATA = <?= json_encode($allPermissions ?? []) ?>;
const USER_ROLES_DATA = <?= json_encode($userRoles ?? []) ?>;

// Current user permissions for UI control
const CURRENT_USER_PERMISSIONS = {
    canEditUser: <?= hasPermission('service.user.edit') ? 'true' : 'false' ?>,
    canEditPassword: <?= hasPermission('service.user.edit_password') ? 'true' : 'false' ?>,
    canAssignArea: <?= hasPermission('service.user.assign_area') ? 'true' : 'false' ?>,
    canAssignBranch: <?= hasPermission('service.user.assign_branch') ? 'true' : 'false' ?>,
    canAssignPermissions: <?= hasPermission('service.user.assign_permissions') ? 'true' : 'false' ?>
};

// Debug service access data
console.log('🔍 SERVICE_ACCESS_DATA Debug:', {
    raw_data: SERVICE_ACCESS_DATA,
    type: typeof SERVICE_ACCESS_DATA,
    is_object: typeof SERVICE_ACCESS_DATA === 'object',
    has_area_type: SERVICE_ACCESS_DATA?.area_type,
    has_department_scope: SERVICE_ACCESS_DATA?.department_scope,
    has_service_area_ids: SERVICE_ACCESS_DATA?.service_area_ids
});

console.log('📊 Edit User Form initialized:', {
    mode: 'EDIT',
    user_id: USER_ID,
    user_data: USER_DATA,
    service_access: SERVICE_ACCESS_DATA,
    roles_count: ROLES_DATA?.length || 0,
    divisions_available: <?= json_encode(isset($divisions) ? count($divisions) : 0) ?>
});
</script>

<!-- ========================================
     🚀 COMPLETE INLINE JAVASCRIPT FOR EDIT USER
     ======================================== -->
<script>
// ========================================
// 🌐 GLOBAL VARIABLES
// ========================================
let selectedServiceAreasIds = [];
let serviceAreasData = [];
let selectedCustomPermissions = [];
let allPermissionsData = [];
let roleBasedPermissions = [];

// ========================================
// 🔧 DIVISION & ROLE MANAGEMENT
// ========================================
function initializeDivisionHandlers() {
    const divisionSelect = $('#division_select');
    const roleSelect = $('#role_select');
    const serviceSection = $('#serviceAccessSection');
    
    // Load divisions first
    loadDivisionOptions();
    
    // Load existing roles for current division
    if (USER_DATA.division) {
        loadRolesForDivision(USER_DATA.division, USER_DATA.role);
        
        // Check if current division is Service and show service section
        const currentDivisionText = $('#division_select option:selected').text().toLowerCase();
        if (currentDivisionText.includes('service') || currentDivisionText === 'service') {
            serviceSection.removeClass('d-none');
            console.log('🔧 Service section shown for existing Service user');
            // Load service areas for existing user
            loadServiceAreas();
        }
    }
    
    // Division change handler
    divisionSelect.on('change', function() {
        const selectedDivision = $(this).val();
        const selectedDivisionName = $(this).find('option:selected').text().toLowerCase();
        
        console.log('🏢 Division changed:', selectedDivision, selectedDivisionName);
        
        // Update hidden field
        $('#division').val(selectedDivision);
        
        // Load roles for new division
        loadRolesForDivision(selectedDivision);
        
        // Show/hide service section - check for 'service' in division name
        if (selectedDivisionName.includes('service') || selectedDivisionName === 'service') {
            serviceSection.removeClass('d-none');
            console.log('🔧 Service section shown for Service division');
            // Load service areas when service section is shown
            loadServiceAreas();
        } else {
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

function loadDivisionOptions() {
    console.log('🏢 Loading division options...');
    const divisionSelect = $('#division_select');
    
    // Add divisions from PHP data
    <?php if (isset($divisions) && is_array($divisions)): ?>
    const divisions = <?= json_encode($divisions) ?>;
    divisionSelect.html('<option value="">Select Division</option>');
    
    divisions.forEach(division => {
        const selected = USER_DATA.division && USER_DATA.division == division.id ? 'selected' : '';
        divisionSelect.append(`<option value="${division.id}" ${selected}>${division.name}</option>`);
    });
    <?php endif; ?>
    
    console.log('✅ Division options loaded, selected:', USER_DATA.division);
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
        roleSelect.prop('disabled', true);
        console.log('🔍 No division selected or no roles data available');
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
        
        // Apply permission-based UI restrictions
        applyPermissionRestrictions();
    }
}

// ========================================
// 🔒 PERMISSION-BASED UI CONTROL
// ========================================
function applyPermissionRestrictions() {
    console.log('🔒 Applying permission restrictions:', CURRENT_USER_PERMISSIONS);
    
    // Disable service area management if no permission
    if (!CURRENT_USER_PERMISSIONS.canAssignArea) {
        // Disable all service area controls
        $('#area_type, #department_scope').prop('disabled', true);
        $('#btnSelectServiceAreas').prop('disabled', true).addClass('d-none');
        
        // Add visual indicators
        $('.service-area-readonly').removeClass('d-none');
        
        console.log('🚫 Service area controls disabled - no assign_area permission');
    }
    
    // Disable custom permissions management if no permission
    if (!CURRENT_USER_PERMISSIONS.canAssignPermissions) {
        $('#btnManagePermissions').prop('disabled', true).addClass('d-none');
        console.log('🚫 Permissions management disabled - no assign_permissions permission');
    }
    
    // Apply read-only mode if no edit permission
    if (!CURRENT_USER_PERMISSIONS.canEditUser) {
        // Disable all form controls except cancel button
        $('#editUserForm input, #editUserForm select, #editUserForm textarea').prop('disabled', true);
        $('button[type="submit"]').prop('disabled', true);
        
        // Add read-only notification
        showReadOnlyNotification();
        
        console.log('🚫 Form in read-only mode - no edit permission');
    }
}

function showReadOnlyNotification() {
    const notification = `
        <div class="alert alert-info alert-dismissible fade show" role="alert" id="readOnlyAlert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Read-Only Mode:</strong> You are viewing this user's information in read-only mode. 
            You do not have permission to make changes.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insert notification at the top of the form
    $('#editUserForm').prepend(notification);
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
    // Parse existing service areas
    if (USER_DATA.service_areas && USER_DATA.service_areas.trim() !== '') {
        try {
            selectedServiceAreasIds = USER_DATA.service_areas.split(',')
                .map(id => parseInt(id.trim()))
                .filter(id => !isNaN(id));
            
            console.log('🗺️ Existing service areas loaded:', selectedServiceAreasIds);
            updateSelectedServiceAreasDisplay();
        } catch (error) {
            console.error('❌ Error parsing service areas:', error);
            selectedServiceAreasIds = [];
        }
    }
    
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
    
    console.log('🔍 Rendering service areas:', {
        areas_count: areas.length,
        selectedServiceAreasIds: selectedServiceAreasIds,
        selectedServiceAreasIds_types: selectedServiceAreasIds.map(id => typeof id)
    });
    
    areas.forEach(area => {
        // Ensure consistent type comparison (both as integers)
        const areaId = parseInt(area.id);
        const isSelected = selectedServiceAreasIds.some(selectedId => parseInt(selectedId) === areaId);
        const isChecked = isSelected ? 'checked' : '';
        const selectedClass = isSelected ? 'border-primary bg-light' : '';
        
        console.log('🔍 Area check:', {
            area_id: area.id,
            area_id_int: areaId,
            is_selected: isSelected,
            area_name: area.name
        });
        
        html += `
            <div class="col-md-6 mb-2">
                <div class="card ${selectedClass} service-area-item" style="cursor: pointer; transition: all 0.2s;">
                    <div class="card-body p-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   value="${areaId}" id="area_${areaId}" ${isChecked}>
                            <label class="form-check-label" for="area_${areaId}">
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
    
    console.log('✅ Service areas rendered with', selectedServiceAreasIds.length, 'selected areas');
    
    // Add click handlers for cards
    $('.service-area-item').on('click', function() {
        const checkbox = $(this).find('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked'));
        $(this).toggleClass('border-primary bg-light', checkbox.prop('checked'));
    });
    
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
    
    console.log('🗺️ Updated service areas:', selectedIds);
}

function updateSelectedServiceAreasDisplay() {
    const count = selectedServiceAreasIds?.length || 0;
    const display = $('#selectedServiceAreasDisplay');
    
    console.log('🔄 Updating service areas display:', {
        count: count,
        ids: selectedServiceAreasIds,
        display_element_exists: display.length > 0
    });
    
    if (count > 0) {
        display.html(`<span class="badge bg-success">${count} area(s) selected</span>`);
        $('#selectedServiceAreas').val(selectedServiceAreasIds.join(','));
    } else {
        display.html('<span class="text-muted">No areas selected</span>');
        $('#selectedServiceAreas').val('');
    }
}

// ========================================
// 🗺️ SERVICE ACCESS DATA MANAGEMENT
// ========================================
function loadExistingServiceAccess() {
    console.log('🗺️ Loading existing service access data:', SERVICE_ACCESS_DATA);
    
    try {
        if (!SERVICE_ACCESS_DATA || typeof SERVICE_ACCESS_DATA !== 'object') {
            console.log('⚠️ No service access data found for user');
            return;
        }
        
        // SERVICE_ACCESS_DATA is an object, not array
        // Populate area type
        if (SERVICE_ACCESS_DATA.area_type) {
            console.log('🔧 Setting area type:', SERVICE_ACCESS_DATA.area_type);
            $('#area_type').val(SERVICE_ACCESS_DATA.area_type);
            console.log('📍 Set area type:', SERVICE_ACCESS_DATA.area_type);
            
            // Trigger change event to show/hide dependent fields
            $('#area_type').trigger('change');
            
            // Debug: Check visibility after trigger
            setTimeout(() => {
                console.log('🔍 Section visibility check:', {
                    centralSection: $('#centralAccessSection').is(':visible'),
                    branchSection: $('#branchAccessSection').is(':visible'),
                    selectedDisplay: $('#selectedServiceAreasDisplay').length > 0,
                    area_type_value: $('#area_type').val()
                });
                
                // Force show branch section if area type is BRANCH
                if (SERVICE_ACCESS_DATA.area_type === 'BRANCH') {
                    $('#branchAccessSection').show();
                    $('#centralAccessSection').hide();
                    console.log('🔧 Forced branch section to show');
                } else if (SERVICE_ACCESS_DATA.area_type === 'CENTRAL') {
                    $('#centralAccessSection').show();
                    $('#branchAccessSection').hide();
                    console.log('🔧 Forced central section to show');
                }
            }, 100);
        }
        
        // Populate department scope for CENTRAL
        if (SERVICE_ACCESS_DATA.department_scope) {
            console.log('🔧 Setting department scope:', SERVICE_ACCESS_DATA.department_scope);
            $('#department_scope').val(SERVICE_ACCESS_DATA.department_scope);
            console.log('🏢 Set department scope:', SERVICE_ACCESS_DATA.department_scope);
        }
        
        // Populate selected service areas from service_area_ids
        if (SERVICE_ACCESS_DATA.service_area_ids && Array.isArray(SERVICE_ACCESS_DATA.service_area_ids)) {
            // Ensure all IDs are integers for consistent comparison
            selectedServiceAreasIds = SERVICE_ACCESS_DATA.service_area_ids.map(id => parseInt(id));
            updateSelectedServiceAreasDisplay();
            
            console.log('🌿 Loaded service area IDs:', {
                original: SERVICE_ACCESS_DATA.service_area_ids,
                converted: selectedServiceAreasIds,
                types: selectedServiceAreasIds.map(id => typeof id)
            });
            
            // If branch type, load specific service areas for modal
            if (SERVICE_ACCESS_DATA.area_type === 'BRANCH') {
                loadSpecificServiceAreas();
            }
        }
        
        console.log('✅ Service access data loaded successfully');
        
    } catch (error) {
        console.error('❌ Error loading service access data:', error);
    }
}


function loadSpecificServiceAreas() {
    console.log('🔍 Loading specific service areas for branch access...');
    
    // Trigger service area loading if not already loaded
    if (!serviceAreasData || serviceAreasData.length === 0) {
        loadServiceAreas();
    }
}

// ========================================
// 🔐 CUSTOM PERMISSIONS MANAGEMENT
// ========================================
function initializeCustomPermissionsHandlers() {
    console.log('🔐 Initializing custom permissions handlers...');
    
    // Load existing permissions data
    loadExistingPermissions();
    
    // Permissions button handler with debug
    console.log('🔍 Checking if btnManagePermissions exists:', $('#btnManagePermissions').length);
    
    $('#btnManagePermissions').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('🔐 =================================');
        console.log('🔐 Manage Permissions button clicked!');
        console.log('🔐 Button element:', this);
        console.log('🔐 Button jQuery object:', $(this));
        console.log('🔐 Modal element exists:', $('#customPermissionsModal').length > 0);
        console.log('🔐 Permissions data available:', typeof allPermissionsData, allPermissionsData?.length || 0);
        console.log('🔐 =================================');
        
        try {
            // Load permissions and existing data
            loadPermissions();
            loadExistingPermissions();
            
            console.log('🔐 About to show modal...');
            $('#customPermissionsModal').modal('show');
            
            setTimeout(() => {
                console.log('🔐 Modal visible check:', $('#customPermissionsModal').is(':visible'));
            }, 500);
            
        } catch (error) {
            console.error('❌ Error in permissions button handler:', error);
        }
    });
    
    // Modal save button
    $('#btnSavePermissions').on('click', confirmPermissionSelection);
    
    // Search functionality
    $('#permissionSearch').on('input', filterPermissions);
    $('#moduleFilter').on('change', filterPermissions);
    $('#pageFilter').on('change', filterPermissions);
    
    // Handle module change to update page filter
    $('#moduleFilter').on('change', function() {
        const selectedModule = $(this).val();
        populatePageFilter(selectedModule);
        filterPermissions();
    });
    
    // Bulk actions
    $('#btnSelectAllPermissions').on('click', selectAllPermissions);
    $('#btnClearAllPermissions').on('click', clearAllPermissions);
}

function loadExistingPermissions() {
    console.log('🔐 Loading existing permissions data...');
    console.log('👤 User permissions:', USER_PERMISSIONS_DATA);
    console.log('👔 User roles:', USER_ROLES_DATA);
    
    // Start with empty arrays
    selectedCustomPermissions = [];
    roleBasedPermissions = [];
    
    // Load custom user permissions first
    if (USER_PERMISSIONS_DATA && USER_PERMISSIONS_DATA.length > 0) {
        selectedCustomPermissions = USER_PERMISSIONS_DATA.map(perm => parseInt(perm.id));
        console.log('🎯 Loaded custom permissions:', selectedCustomPermissions);
    } else {
        console.log('🎯 No custom permissions found');
        selectedCustomPermissions = [];
    }
    
    // Load permissions from roles
    if (USER_ROLES_DATA && USER_ROLES_DATA.length > 0) {
        console.log('🔄 Loading role-based permissions...');
        loadRoleBasedPermissions();
    } else {
        console.log('⚠️ No roles found for user');
        // If no roles, just render with custom permissions
        if (allPermissionsData && allPermissionsData.length > 0) {
            renderPermissionsByModuleAndPage(allPermissionsData);
        }
    }
    
    updateCustomPermissionsDisplay();
}

function loadRoleBasedPermissions() {
    console.log('🔄 Loading role-based permissions...');
    
    if (!USER_ROLES_DATA || USER_ROLES_DATA.length === 0) {
        console.log('⚠️ No roles found for user');
        return;
    }
    
    const roleIds = USER_ROLES_DATA.map(role => role.id);
    
    $.ajax({
        url: '<?= base_url("admin/advanced-users/get-role-permissions") ?>',
        method: 'POST',
        data: {
            role_ids: roleIds,
            '<?= csrf_token() ?>': CSRF_HASH
        },
        dataType: 'json',
        success: function(response) {
            console.log('🎯 Role permissions response:', response);
            if (response && response.success && response.data) {
                roleBasedPermissions = response.data.map(perm => parseInt(perm.id));
                console.log('✅ Loaded role-based permissions:', roleBasedPermissions);
                updateCustomPermissionsDisplay();
                // Re-render permissions to update the display with role badges
                if (allPermissionsData && allPermissionsData.length > 0) {
                    renderPermissionsByModuleAndPage(allPermissionsData);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error loading role permissions:', error);
            roleBasedPermissions = [];
        }
    });
}


function loadPermissions() {
    console.log('🔐 ===== loadPermissions() called =====');
    
    const loadingHtml = `
        <div class="text-center p-3">
            <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
            <span class="ms-2">Loading permissions...</span>
        </div>
    `;
    
    const permissionsList = $('#permissionsList');
    console.log('🔐 permissionsList element exists:', permissionsList.length > 0);
    
    if (permissionsList.length > 0) {
        permissionsList.html(loadingHtml);
        console.log('🔐 Loading HTML set successfully');
    }
    
    // Use the permissions data from controller (same as create_user.php)
    const permissionsData = <?= json_encode($allPermissions ?? []) ?>;
    
    console.log('🔐 Permissions data from controller:');
    console.log('- Type:', typeof permissionsData);
    console.log('- Is Array:', Array.isArray(permissionsData));
    console.log('- Length:', permissionsData?.length || 0);
    console.log('- Sample:', permissionsData?.slice(0, 3) || 'No data');
    
    if (permissionsData && Array.isArray(permissionsData) && permissionsData.length > 0) {
        allPermissionsData = permissionsData;
        console.log('🔐 Setting allPermissionsData:', allPermissionsData.length, 'items');
        
        populateModuleFilter();
        populatePageFilter();
        renderPermissionsByModuleAndPage(allPermissionsData);
        
        console.log('🔐 Permissions rendered successfully');
    } else {
        console.warn('🔐 No permissions data available or invalid data structure');
        if (permissionsList.length > 0) {
            showPermissionsError('No permissions available');
        }
    }
}

function populateModuleFilter() {
    const modules = [...new Set(allPermissionsData.map(p => p.module || 'general'))];
    const moduleFilter = $('#moduleFilter');
    
    moduleFilter.empty().append('<option value="">All Modules</option>');
    modules.forEach(module => {
        moduleFilter.append(`<option value="${module}">${module.charAt(0).toUpperCase() + module.slice(1)}</option>`);
    });
}

function populatePageFilter(selectedModule = '') {
    const pageFilter = $('#pageFilter');
    let pages;
    
    if (selectedModule) {
        // Filter pages by selected module
        pages = [...new Set(allPermissionsData
            .filter(p => (p.module || 'general') === selectedModule)
            .map(p => p.page || 'general'))];
    } else {
        // All pages
        pages = [...new Set(allPermissionsData.map(p => p.page || 'general'))];
    }
    
    pageFilter.empty().append('<option value="">All Pages</option>');
    pages.forEach(page => {
        pageFilter.append(`<option value="${page}">${page.charAt(0).toUpperCase() + page.slice(1)}</option>`);
    });
}

function renderPermissionsByModuleAndPage(permissions) {
    console.log('🔐 Rendering permissions by module and page...');
    
    let html = '';
    
    // Group permissions by module and page
    const grouped = {};
    permissions.forEach(permission => {
        const module = permission.module || 'general';
        const page = permission.page || 'general';
        
        if (!grouped[module]) {
            grouped[module] = {};
        }
        if (!grouped[module][page]) {
            grouped[module][page] = [];
        }
        grouped[module][page].push(permission);
    });
    
    // Render grouped permissions
    Object.keys(grouped).sort().forEach(module => {
        html += `
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        ${module.charAt(0).toUpperCase() + module.slice(1)} Module
                    </h6>
                </div>
                <div class="card-body p-0">
        `;
        
        Object.keys(grouped[module]).sort().forEach(page => {
            const pagePermissions = grouped[module][page];
            
            html += `
                <div class="border-bottom">
                    <div class="p-3 bg-light">
                        <h6 class="mb-2 text-primary">
                            <i class="fas fa-file-alt me-2"></i>
                            ${page.charAt(0).toUpperCase() + page.slice(1)} Page
                        </h6>
                        <div class="row">
            `;
            
            pagePermissions.forEach(permission => {
                const permId = parseInt(permission.id);
                const isRoleBased = roleBasedPermissions.includes(permId);
                const isCustom = selectedCustomPermissions.includes(permId);
                
                let statusBadge = '';
                let cardClass = '';
                let checkboxState = '';
                
                if (isRoleBased && isCustom) {
                    statusBadge = '<span class="badge bg-info">Role + Custom</span>';
                    cardClass = 'border-info bg-light';
                    checkboxState = 'checked';
                } else if (isRoleBased) {
                    statusBadge = '<span class="badge bg-success">Role</span>';
                    cardClass = 'border-success bg-light';
                    checkboxState = 'checked disabled';
                } else if (isCustom) {
                    statusBadge = '<span class="badge bg-warning">Custom</span>';
                    cardClass = 'border-warning bg-light';
                    checkboxState = 'checked';
                } else {
                    cardClass = '';
                }
                
                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card ${cardClass} h-100">
                            <div class="card-body p-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           value="${permId}" id="perm_${permId}" ${checkboxState}
                                           ${isRoleBased && !isCustom ? 'data-role-based="true"' : ''}>
                                    <label class="form-check-label small" for="perm_${permId}">
                                        <strong>${permission.display_name}</strong>
                                        <div class="text-muted small">${permission.key_name}</div>
                                        ${permission.description ? `<div class="text-muted small">${permission.description}</div>` : ''}
                                        <div class="mt-1">${statusBadge}</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    if (html === '') {
        html = '<div class="alert alert-info">No permissions found matching your criteria.</div>';
    }
    
    $('#permissionsList').html(html);
}

function renderEnhancedPermissions(tree) {
    let html = '';
    
    Object.keys(tree).forEach(module => {
        const moduleData = tree[module];
        
        html += `
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-folder me-2"></i>
                        ${moduleData.name} Module
                        <span class="badge bg-light text-primary ms-2">${Object.keys(moduleData.pages).length} pages</span>
                    </h6>
                </div>
                <div class="card-body p-0">
        `;
        
        Object.keys(moduleData.pages).forEach(page => {
            const pageData = moduleData.pages[page];
            
            html += `
                <div class="border-bottom">
                    <div class="p-3 bg-light">
                        <h6 class="mb-2">
                            <i class="fas fa-file-alt me-2 text-secondary"></i>
                            ${pageData.name}
                            <span class="badge bg-secondary ms-2">${pageData.permissions.length} permissions</span>
                        </h6>
                    </div>
                    <div class="p-3">
                        <div class="row">
            `;
            
            // Group permissions by category
            const categorizedPerms = {};
            pageData.permissions.forEach(perm => {
                const category = perm.category || 'other';
                if (!categorizedPerms[category]) {
                    categorizedPerms[category] = [];
                }
                categorizedPerms[category].push(perm);
            });
            
            Object.keys(categorizedPerms).forEach(category => {
                html += `
                    <div class="col-12 mb-3">
                        <div class="badge bg-info mb-2">${category.charAt(0).toUpperCase() + category.slice(1)}</div>
                        <div class="row">
                `;
                
                categorizedPerms[category].forEach(permission => {
                    const permId = parseInt(permission.id);
                    const isRoleBased = roleBasedPermissions.includes(permId);
                    const isCustom = selectedCustomPermissions.includes(permId);
                    const hasAnyAccess = isRoleBased || isCustom;
                    
                    let statusBadge = '';
                    let cardClass = '';
                    let checkboxState = '';
                    let iconClass = 'fas fa-circle';
                    
                    if (isRoleBased && isCustom) {
                        statusBadge = '<span class="badge bg-info">Role + Custom</span>';
                        cardClass = 'border-info bg-light';
                        checkboxState = 'checked';
                        iconClass = 'fas fa-check-circle text-info';
                    } else if (isRoleBased) {
                        statusBadge = '<span class="badge bg-success">Role</span>';
                        cardClass = 'border-success bg-light';
                        checkboxState = 'checked disabled';
                        iconClass = 'fas fa-shield-alt text-success';
                    } else if (isCustom) {
                        statusBadge = '<span class="badge bg-warning">Custom</span>';
                        cardClass = 'border-warning bg-light';
                        checkboxState = 'checked';
                        iconClass = 'fas fa-user-cog text-warning';
                    } else {
                        iconClass = 'far fa-circle text-muted';
                    }
                    
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="card ${cardClass} permission-item h-100" 
                                 style="cursor: ${isRoleBased && !isCustom ? 'default' : 'pointer'}; transition: all 0.2s;">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-start">
                                        <div class="me-2 mt-1">
                                            <i class="${iconClass}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       value="${permId}" id="perm_${permId}" ${checkboxState}
                                                       ${isRoleBased && !isCustom ? 'data-role-based="true"' : ''}>
                                                <label class="form-check-label small" for="perm_${permId}">
                                                    <strong>${permission.display_name}</strong>
                                                    <div class="text-muted small">${permission.key_name}</div>
                                                    ${permission.description ? `<div class="text-muted small">${permission.description}</div>` : ''}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            ${statusBadge}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            });
            
            html += `
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    if (html === '') {
        html = '<div class="text-center p-3"><span class="text-muted">No permissions found</span></div>';
    }
    
    $('#permissionsList').html(html);
    
    // Add click handlers for cards
    $('.permission-item').on('click', function() {
        const checkbox = $(this).find('input[type="checkbox"]');
        
        // Don't allow clicking on role-based only permissions
        if (checkbox.data('role-based') && !checkbox.prop('checked')) {
            return;
        }
        
        // Only toggle if it's not disabled
        if (!checkbox.prop('disabled')) {
            checkbox.prop('checked', !checkbox.prop('checked'));
            updatePermissionCardStyle($(this), checkbox.prop('checked'), checkbox.data('role-based'));
        }
    });
}

function updatePermissionCardStyle(card, isChecked, isRoleBased) {
    if (isRoleBased) {
        return; // Don't change role-based styles
    }
    
    if (isChecked) {
        card.addClass('border-warning bg-light');
        card.find('.fa-circle').removeClass('far text-muted').addClass('fas fa-user-cog text-warning');
    } else {
        card.removeClass('border-warning bg-light');
        card.find('.fas.fa-user-cog').removeClass('fas fa-user-cog text-warning').addClass('far fa-circle text-muted');
    }
}

function filterPermissions() {
    const searchTerm = $('#permissionSearch').val().toLowerCase();
    const selectedModule = $('#moduleFilter').val();
    const selectedPage = $('#pageFilter').val();
    
    let filteredPermissions = allPermissionsData;
    
    // Filter by search term
    if (searchTerm) {
        filteredPermissions = filteredPermissions.filter(permission => 
            permission.display_name.toLowerCase().includes(searchTerm) ||
            (permission.description && permission.description.toLowerCase().includes(searchTerm)) ||
            permission.key_name.toLowerCase().includes(searchTerm)
        );
    }
    
    // Filter by module
    if (selectedModule) {
        filteredPermissions = filteredPermissions.filter(permission => 
            (permission.module || 'general') === selectedModule
        );
    }
    
    // Filter by page
    if (selectedPage) {
        filteredPermissions = filteredPermissions.filter(permission => 
            (permission.page || 'general') === selectedPage
        );
    }
    
    renderPermissionsByModuleAndPage(filteredPermissions);
}

function showPermissionsError(message) {
    $('#permissionsList').html(`
        <div class="col-12 text-center p-3">
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>${message}
            </div>
        </div>
    `);
}

// Permission modal functions
function selectAllPermissions() {
    $('#permissionsList input[type="checkbox"]').prop('checked', true);
    $('.permission-item').addClass('border-warning bg-light');
}

function clearAllPermissions() {
    $('#permissionsList input[type="checkbox"]').prop('checked', false);
    $('.permission-item').removeClass('border-warning bg-light');
}

function confirmPermissionSelection() {
    const selectedIds = [];
    const customOnlyIds = [];
    
    $('#permissionsList input[type="checkbox"]:checked').each(function() {
        const permId = parseInt($(this).val());
        selectedIds.push(permId);
        
        // Only include in custom permissions if it's not a role-based permission
        if (!roleBasedPermissions.includes(permId)) {
            customOnlyIds.push(permId);
        }
    });
    
    selectedCustomPermissions = customOnlyIds;
    updateCustomPermissionsDisplay();
    $('#customPermissions').val(JSON.stringify(customOnlyIds));
    $('#customPermissionsModal').modal('hide');
    
    console.log('🔐 All selected permissions:', selectedIds);
    console.log('🔐 Custom-only permissions:', customOnlyIds);
    console.log('🔐 Role-based permissions:', roleBasedPermissions);
}

function updateCustomPermissionsDisplay() {
    const customCount = selectedCustomPermissions?.length || 0;
    const roleCount = roleBasedPermissions?.length || 0;
    const totalUniquePermissions = new Set([...selectedCustomPermissions, ...roleBasedPermissions]).size;
    
    const display = $('#customPermissionsDisplay');
    
    let html = '';
    
    if (roleCount > 0 || customCount > 0) {
        html = '<div class="d-flex flex-wrap align-items-center gap-2">';
        
        if (roleCount > 0) {
            html += `<span class="badge bg-success">${roleCount} from role</span>`;
        }
        
        if (customCount > 0) {
            html += `<span class="badge bg-warning text-dark">${customCount} custom</span>`;
        }
        
        html += `<span class="badge bg-primary">${totalUniquePermissions} total</span>`;
        html += '</div>';
        
        if (customCount > 0) {
            html += '<small class="text-muted d-block mt-1">Custom permissions override role permissions</small>';
        }
    } else {
        html = '<span class="text-muted">No permissions set</span>';
    }
    
    display.html(html);
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
    
    // Password strength checker (only for new passwords)
    $('#password').on('input', function() {
        if ($(this).val()) {
            checkPasswordStrength();
        } else {
            $('#passwordStrength').empty();
        }
    });
    
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
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
        
        // Prepare form data including service access
        const formData = new FormData(this);
        
        // Add service access data with correct field names for controller
        const areaType = $('#area_type').val();
        const departmentScope = $('#department_scope').val();
        const selectedAreas = $('#selectedServiceAreas').val();
        
        if (areaType) {
            formData.append('area_type', areaType);
        }
        if (departmentScope) {
            formData.append('department_scope', departmentScope);
        }
        if (selectedAreas && selectedAreas.trim() !== '') {
            // Convert comma-separated string to array format expected by controller
            const areaIds = selectedAreas.split(',').map(id => id.trim()).filter(id => id);
            
            // Send as JSON string (Method 2 in controller)
            formData.append('service_area_ids_json', JSON.stringify(areaIds));
            
            // Also send as individual array elements (Method 3 in controller)
            areaIds.forEach((id, index) => {
                formData.append(`service_area_ids[${index}]`, id);
            });
        }
        
        console.log('📤 Submitting form with service access:', {
            area_type: areaType,
            department_scope: departmentScope,
            selected_areas: selectedAreas,
            area_ids_array: selectedAreas ? selectedAreas.split(',') : []
        });
        
        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showSuccessMessage('User updated successfully!');
                    setTimeout(() => {
                        window.location.href = USERS_LIST_URL;
                    }, 1500);
                } else {
                    showErrorMessage(response.message || 'Failed to update user');
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
    const requiredFields = ['first_name', 'last_name', 'username', 'email', 'division', 'role'];
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
    
    // Check password match (only if password is provided)
    const password = $('#password').val();
    const confirmPassword = $('#password_confirm').val();
    
    if (password && password !== confirmPassword) {
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
    console.log('🎯 ===============================');
    console.log('🎯 Initializing Edit User Form...');
    console.log('🎯 ===============================');
    
    // Debug: Check if form elements exist
    console.log('🔍 Form Elements Check:', {
        area_type_exists: $('#area_type').length > 0,
        department_scope_exists: $('#department_scope').length > 0,
        selectedServiceAreas_exists: $('#selectedServiceAreas').length > 0,
        editUserForm_exists: $('#editUserForm').length > 0,
        btnManagePermissions_exists: $('#btnManagePermissions').length > 0,
        customPermissionsModal_exists: $('#customPermissionsModal').length > 0,
        permissionsList_exists: $('#permissionsList').length > 0,
        division_select_exists: $('#division_select').length > 0,
        role_select_exists: $('#role_select').length > 0,
        serviceAccessSection_exists: $('#serviceAccessSection').length > 0
    });
    
    // Debug: Check data availability
    console.log('🔍 Data Availability Check:', {
        USER_DATA_defined: typeof USER_DATA !== 'undefined',
        ROLES_DATA_defined: typeof ROLES_DATA !== 'undefined',
        ROLES_DATA_length: ROLES_DATA ? ROLES_DATA.length : 0,
        permissions_data_available: <?= json_encode(!empty($allPermissions)) ?>,
        permissions_count: <?= json_encode(count($allPermissions ?? [])) ?>
    });
    
    // Initialize all handlers
    try {
        console.log('🔧 Initializing division handlers...');
        initializeDivisionHandlers();
        
        console.log('🔧 Initializing service area handlers...');
        initializeServiceAreaHandlers();
        
        console.log('🔧 Initializing custom permissions handlers...');
        initializeCustomPermissionsHandlers();
        
        console.log('🔧 Initializing password handlers...');
        initializePasswordHandlers();
        
        console.log('🔧 Initializing form submission...');
        initializeFormSubmission();
        
        console.log('🔧 Loading existing service access...');
        loadExistingServiceAccess();
        
        console.log('🔒 Applying permission-based restrictions...');
        applyPermissionRestrictions();
        
    } catch (error) {
        console.error('❌ Error during initialization:', error);
    }
    
    // Add focus styling using global CSS classes
    $('.form-control, .form-select').on('focus', function() {
        $(this).addClass('shadow-sm');
    }).on('blur', function() {
        $(this).removeClass('shadow-sm');
    });
    
    console.log('✅ Edit User Form initialized successfully!');
});
</script>
<?= $this->endSection() ?>