<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
.simple-permission-table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.simple-permission-table th,
.simple-permission-table td {
    border: 1px solid #dee2e6;
    padding: 15px;
    text-align: center;
}

.simple-permission-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.simple-permission-table .module-name {
    text-align: left;
    font-weight: 500;
    color: #333;
}

.permission-checkbox {
    transform: scale(1.3);
    cursor: pointer;
}

.permission-level {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.permission-level.active {
    color: #28a745;
    font-weight: 600;
}

.role-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.role-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.role-name {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.role-description {
    color: #6c757d;
    margin-top: 5px;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.btn-simple {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-simple:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.permission-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.permission-info h6 {
    margin-bottom: 10px;
    color: #495057;
}

.permission-info p {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
}

/* Fixed scrollable permission container - SIMPLIFIED */
.permission-container {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: #fff;
    padding: 10px;
}

.permission-container::-webkit-scrollbar {
    width: 8px;
}

.permission-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.permission-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.permission-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Roles container - more compact */
.roles-container {
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #fff;
    padding: 15px;
}

.roles-container::-webkit-scrollbar {
    width: 8px;
}

.roles-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.roles-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.roles-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Compact role cards */
.role-card {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s;
}

.role-card:hover {
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    transform: translateY(-1px);
}

.role-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.role-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.role-description {
    color: #6c757d;
    font-size: 13px;
    margin: 0;
    margin-top: 2px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.btn-simple {
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: 500;
    font-size: 12px;
    transition: all 0.2s;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Simple Role Management</h1>
                    <p class="text-muted">Manage user roles and permissions with a simple interface</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-simple" onclick="createNewRole()">
                        <i class="fas fa-plus me-2"></i>Create New Role
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle btn-simple" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i> Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('admin/advanced-users') ?>"><i class="fas fa-users"></i> Manage Users</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('admin/permissions') ?>"><i class="fas fa-key"></i> Manage Permissions</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Permission Info -->
            <div class="permission-info">
                <h6><i class="fas fa-info-circle me-2"></i>Permission Levels</h6>
                <p>
                    <strong>View:</strong> Can only view data (read-only) | 
                    <strong>Edit:</strong> Can view, edit, and create data (read + write + create) | 
                    <strong>Full:</strong> Can view, edit, create, delete, and export data (read + write + create + delete + export)
                </p>
            </div>

            <!-- Roles List -->
            <div class="roles-container">
                <div id="rolesList">
                    <!-- Roles will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="roleId" name="role_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="roleName" class="form-label">Role Name *</label>
                            <input type="text" class="form-control" id="roleName" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="roleStatus" class="form-label">Status</label>
                            <select class="form-select" id="roleStatus" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="roleDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="roleDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <h6>Permissions</h6>
                        <p class="text-muted small">Select permissions for each module and page</p>
                        
                        <div class="permission-container" style="max-height: 400px; overflow-y: auto;">
                            <div id="permissionsTable">
                                <!-- Permissions will be loaded here as cards -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-simple" id="saveRoleBtn" onclick="saveRole()">
                    <i class="fas fa-save me-2"></i>Save Role
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Load roles on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRoles();
});

// Load all roles
function loadRoles() {
    fetch('<?= base_url('admin/roles/get-roles') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRoles(data.roles);
            } else {
                console.error('Error loading roles:', data.message);
                alert('Error loading roles: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading roles');
        });
}

// Display roles in the list
function displayRoles(roles) {
    const rolesList = document.getElementById('rolesList');
    rolesList.innerHTML = '';

    if (roles.length === 0) {
        rolesList.innerHTML = '<div class="text-center py-4"><p class="text-muted mb-0">No roles found</p></div>';
        return;
    }

    roles.forEach(role => {
        const roleCard = document.createElement('div');
        roleCard.className = 'role-card';
        roleCard.innerHTML = `
            <div class="role-header">
                <div>
                    <h6 class="role-name">${role.name}</h6>
                    <p class="role-description">${role.description || 'No description'}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="status-badge ${role.is_active ? 'status-active' : 'status-inactive'}">
                        ${role.is_active ? 'Active' : 'Inactive'}
                    </span>
                    <button class="btn btn-sm btn-outline-primary btn-simple" onclick="editRole(${role.id})">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>
                </div>
            </div>
        `;
        rolesList.appendChild(roleCard);
    });
}

// Edit role
function editRole(roleId) {
    fetch(`<?= base_url('admin/roles/get-role') ?>/${roleId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateRoleForm(data.role, data.permissions);
                document.getElementById('roleModalTitle').textContent = 'Edit Role';
                new bootstrap.Modal(document.getElementById('roleModal')).show();
            } else {
                console.error('Error loading role:', data.message);
                alert('Error loading role: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading role');
        });
}

// Create new role
function createNewRole() {
    document.getElementById('roleForm').reset();
    document.getElementById('roleId').value = '';
    document.getElementById('roleModalTitle').textContent = 'Create New Role';
    loadPermissions();
    new bootstrap.Modal(document.getElementById('roleModal')).show();
}

// Load permissions for the form
function loadPermissions() {
    return fetch('<?= base_url('admin/roles/get-permissions') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPermissions(data.permissions);
                return data.permissions;
            } else {
                console.error('Error loading permissions:', data.message);
                alert('Error loading permissions: ' + data.message);
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading permissions');
            throw error;
        });
}

// Display permissions in the table
function displayPermissions(permissions) {
    const permissionsTable = document.getElementById('permissionsTable');
    permissionsTable.innerHTML = '';

    console.log('🔐 Displaying permissions:', permissions);

    // Render dengan struktur yang sama seperti edit_user.php
    let html = '';
    
    // Group permissions by module and page
    const grouped = {};
    
    // Flatten permissions dari format bertingkat ke array sederhana
    Object.keys(permissions).forEach(module => {
        if (typeof permissions[module] === 'object') {
            Object.keys(permissions[module]).forEach(page => {
                const pagePermissions = permissions[module][page];
                
                if (Array.isArray(pagePermissions) && pagePermissions.length > 0) {
                    if (!grouped[module]) {
                        grouped[module] = {};
                    }
                    if (!grouped[module][page]) {
                        grouped[module][page] = [];
                    }
                    grouped[module][page] = pagePermissions;
                }
            });
        }
    });
    
    // Render grouped permissions dengan card layout
    Object.keys(grouped).sort().forEach(module => {
        html += `
            <div class="col-12">
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
                
                html += `
                    <div class="col-md-6 col-lg-4 mb-2">
                        <div class="card border h-100" style="cursor: pointer; transition: all 0.2s;">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-start">
                                    <div class="me-2 mt-1">
                                        <i class="fas fa-key text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox" 
                                                   value="${permId}" id="perm_${permId}"
                                                   data-module="${module}" data-page="${page}" data-action="${permission.action}">
                                            <label class="form-check-label small" for="perm_${permId}">
                                                <strong>${permission.display_name}</strong>
                                                <div class="text-muted small">${permission.key_name}</div>
                                                ${permission.description ? `<div class="text-muted small">${permission.description}</div>` : ''}
                                            </label>
                                        </div>
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
                </div>
            `;
        });
        
        html += `
                    </div>
                </div>
            </div>
        `;
    });
    
    if (html === '') {
        html = '<div class="text-center p-3"><span class="text-muted">No permissions found</span></div>';
    }
    
    // Update table structure untuk card layout
    permissionsTable.innerHTML = `
        <div class="row">
            ${html}
        </div>
       
    `;
}

// Populate role form
function populateRoleForm(role, permissions) {
    document.getElementById('roleId').value = role.id;
    document.getElementById('roleName').value = role.name;
    document.getElementById('roleDescription').value = role.description || '';
    document.getElementById('roleStatus').value = role.is_active;

    // Load permissions first
    loadPermissions().then(() => {
        // Set checked states based on role permissions
        setTimeout(() => {
            console.log('🔐 Setting permissions for role:', role.name);
            console.log('🔐 Role permissions:', permissions);
            
            // Clear all checkboxes first
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
            
            // Set checked states for role permissions
            permissions.forEach(perm => {
                const checkbox = document.getElementById(`perm_${perm.id}`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('✅ Permission set:', perm.display_name);
                } else {
                    console.log('❌ Checkbox not found for permission ID:', perm.id);
                }
            });
        }, 500);
    });
}

// Save role
function saveRole() {
    const formData = new FormData(document.getElementById('roleForm'));
    const permissions = [];
    
    // Collect permission data from checked checkboxes
    document.querySelectorAll('.permission-checkbox:checked').forEach(checkbox => {
        permissions.push({
            permission_id: parseInt(checkbox.value),
            granted: 1
        });
    });
    
    const roleData = {
        role_id: formData.get('role_id') || null,
        name: formData.get('name'),
        description: formData.get('description'),
        is_active: parseInt(formData.get('is_active')),
        permissions: permissions
    };
    
    console.log('🔐 Saving role data:', roleData);
    
    fetch('<?= base_url('admin/roles/saveRole') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(roleData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
            loadRoles();
            alert('Role saved successfully!');
        } else {
            alert('Error saving role: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving role');
    });
}

</script>
<?= $this->endSection() ?>
