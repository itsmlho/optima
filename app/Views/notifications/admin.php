<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-xl">
    <!-- Header Card -->
    <div class="card table-card mb-4">
        <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div>
                <h1 class="h4 mb-1 text-gray-800">
                    <i class="fas fa-cogs me-2"></i>
                    Notification Rules Management
                </h1>
                <p class="mb-0 text-muted">Configure automatic notifications for system events</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="refreshRules()">
                    <i class="fas fa-sync-alt me-1"></i>
                    Refresh
                </button>
                <button class="btn btn-primary" onclick="showCreateModal()">
                    <i class="fas fa-plus me-1"></i>
                    Create Rule
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card table-card">
        <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">Active Rules: <?= count($rules ?? []) ?></h5>
                <small class="text-muted">Manage notification rules for different system events</small>
            </div>
        </div>
        <div class="card-body">
            <!-- Rules Table -->
            <?php if (!empty($rules)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Rule Name</th>
                            <th>Trigger Event</th>
                            <th>Target</th>
                            <th>Priority</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rules as $rule): ?>
                        <tr style="cursor: pointer;" onclick="viewRule(<?= $rule['id'] ?>)">
                            <td>
                                <div class="rule-name"><?= esc($rule['name']) ?></div>
                                <div class="rule-activity"><?= esc($rule['title_template'] ?? '') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-code me-1"></i>
                                    <?= esc($rule['trigger_event'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <i class="fas fa-users me-1"></i>
                                    <?php 
                                    $targetType = 'Mixed';
                                    if (!empty($rule['target_roles'])) $targetType = 'Roles';
                                    elseif (!empty($rule['target_divisions'])) $targetType = 'Divisions'; 
                                    elseif (!empty($rule['target_departments'])) $targetType = 'Departments';
                                    elseif (!empty($rule['target_users'])) $targetType = 'Users';
                                    ?>
                                    <?= $targetType ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $priority = $rule['priority'] ?? 1;
                                $priorityText = ['Low', 'Medium', 'High'];
                                $priorityColors = [
                                    0 => 'success',  // low
                                    1 => 'warning',  // medium
                                    2 => 'danger'    // high
                                ];
                                $priorityName = $priorityText[$priority] ?? 'Medium';
                                $color = $priorityColors[$priority] ?? 'warning';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= $priorityName ?></span>
                            </td>
                            <td>
                                <span class="badge <?= $rule['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $rule['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No notification rules configured</h4>
                <p class="text-muted mb-4">Create your first notification rule to start automating system notifications</p>
                <button class="btn btn-primary" onclick="showCreateModal()">
                    <i class="fas fa-plus me-1"></i>
                    Create First Rule
                </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Rule Actions Modal -->
<div class="modal" id="ruleActionsModal" style="display: none;">
    <div class="modal-content modal-content-sm">
        <div class="modal-header">
            <h5 class="modal-title" id="actionsModalTitle">Rule Actions</h5>
            <button class="modal-close" onclick="closeActionsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="d-grid gap-2">
                <button class="btn btn-outline-primary" onclick="viewRuleFromActions()" id="viewRuleBtn">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
                <button class="btn btn-outline-warning" onclick="editRule()" id="editRuleBtn">
                    <i class="fas fa-edit"></i>
                    Edit Rule
                </button>
                <button class="btn btn-outline-info" onclick="testRule()" id="testRuleBtn">
                    <i class="fas fa-play"></i>
                    Test Rule
                </button>
                <button class="btn btn-outline-danger" onclick="deleteRule()" id="deleteRuleBtn">
                    <i class="fas fa-trash"></i>
                    Delete Rule
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Rule Details Modal -->
<div class="modal" id="viewRuleModal">
    <div class="modal-content modal-content-md">
        <div class="modal-header">
            <h5 class="modal-title">Rule Details</h5>
            <button class="modal-close" onclick="closeViewModal()">&times;</button>
        </div>
        <div class="modal-body" id="ruleDetailsContent">
            <!-- Rule details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline-warning" onclick="editRule()">
                <i class="fas fa-edit"></i>
                Edit
            </button>
            <button class="btn btn-outline-danger" onclick="deleteRuleFromDetail()">
                <i class="fas fa-trash"></i>
                Delete
            </button>
            <button class="btn btn-outline-secondary" onclick="closeViewModal()">Tutup</button>
        </div>
    </div>
</div>

<!-- Create/Edit Rule Modal -->
<div class="modal" id="ruleModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Create Notification Rule</h5>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="ruleForm">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="ruleName">Rule Name *</label>
                    <input type="text" class="form-control" id="ruleName" name="name" required 
                           placeholder="e.g., SPK Approval Notification">
                </div>

                <div class="form-group">
                    <label class="form-label" for="triggerEvent">Trigger Event *</label>
                    <select class="form-control form-select" id="triggerEvent" name="trigger_event" required>
                        <option value="">Select trigger event...</option>
                        <?php foreach ($activity_types ?? [] as $type): ?>
                            <option value="<?= esc($type) ?>"><?= ucfirst(esc(str_replace('_', ' ', $type))) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="titleTemplate">Title Template *</label>
                    <input type="text" class="form-control" id="titleTemplate" name="title_template" required
                           placeholder="e.g., SPK #{spk_id} requires your approval">
                </div>

                <div class="form-group">
                    <label class="form-label" for="messageTemplate">Message Template *</label>
                    <textarea class="form-control" id="messageTemplate" name="message_template" rows="3" required
                              placeholder="e.g., SPK #{spk_id} created by {created_by} is pending your approval."></textarea>
                </div>

                <!-- Auto Include Superadmin -->
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="autoIncludeSuperadmin" name="auto_include_superadmin" checked>
                        <label class="form-check-label" for="autoIncludeSuperadmin">
                            <strong>Auto-include Super Administrator</strong>
                            <br><small class="text-muted">Superadmin will automatically receive all notifications from this rule</small>
                        </label>
                    </div>
                </div>

                <!-- Targeting Mode -->
                <div class="form-group">
                    <label class="form-label">Targeting Mode</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="targeting_mode" id="legacyMode" value="legacy" checked>
                        <label class="form-check-label" for="legacyMode">
                            Single Target (Legacy)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="targeting_mode" id="multiMode" value="multi">
                        <label class="form-check-label" for="multiMode">
                            Multi-Target (Enhanced) - Recommended
                        </label>
                    </div>
                </div>

                <!-- Legacy Single Target -->
                <div id="legacyTargeting" class="targeting-section">
                    <div class="form-group">
                        <label class="form-label" for="targetType">Target Type *</label>
                        <select class="form-control form-select" id="targetType" name="target_type">
                            <option value="">Select target type...</option>
                            <option value="role">By Role</option>
                            <option value="division">By Division</option>
                            <option value="department">By Department</option>
                            <option value="user">Specific User</option>
                            <option value="all">All Users</option>
                        </select>
                    </div>

                    <div class="form-group" id="targetValuesGroup" style="display: none;">
                        <label class="form-label" for="targetValues">Target Values</label>
                        <select class="form-control form-select" id="targetValues" name="target_values[]" multiple>
                            <!-- Options will be populated based on target type -->
                        </select>
                    </div>
                </div>

                <!-- Enhanced Multi-Target -->
                <div id="multiTargeting" class="targeting-section" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Multi-Target Mode:</strong> You can select multiple divisions, roles, departments, and specific users. 
                        All selected targets will receive the notification.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="multiDivisions">Divisions</label>
                                <select class="form-control form-select" id="multiDivisions" name="multi_divisions[]" multiple>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Service">Service</option>
                                    <option value="Operational">Operational</option>
                                    <option value="Purchase">Purchase</option>
                                    <option value="Warehouse">Warehouse</option>
                                    <option value="Finance">Finance</option>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple divisions</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="multiRoles">Roles</label>
                                <select class="form-control form-select" id="multiRoles" name="multi_roles[]" multiple>
                                    <option value="manager">Manager</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="technician">Technician</option>
                                    <option value="staff">Staff</option>
                                    <option value="operator">Operator</option>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple roles</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="multiDepartments">Departments</label>
                                <select class="form-control form-select" id="multiDepartments" name="multi_departments[]" multiple>
                                    <option value="DIESEL">Diesel</option>
                                    <option value="ELEKTRIK">Elektrik</option>
                                    <option value="SPARE_PART">Spare Part</option>
                                    <option value="MAINTENANCE">Maintenance</option>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple departments</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="multiUsers">Specific Users</label>
                                <select class="form-control form-select" id="multiUsers" name="multi_users[]" multiple>
                                    <!-- Will be populated via AJAX -->
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple users</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="priority">Priority</label>
                    <select class="form-control form-select" id="priority" name="priority">
                        <option value="0">Low</option>
                        <option value="1" selected>Medium</option>
                        <option value="2">High</option>
                    </select>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                        <label class="form-check-label" for="isActive">
                            Rule is active
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Save Rule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Available data from PHP
const availableRoles = <?= json_encode($available_roles ?? []) ?>;
const availableDivisions = <?= json_encode($available_divisions ?? []) ?>;
const availableDepartments = <?= json_encode($available_departments ?? []) ?>;
const availableUsers = <?= json_encode($available_users ?? []) ?>;

let currentRuleId = null;
let currentRuleData = null;

function showRuleActions(ruleId, ruleName) {
    currentRuleId = ruleId;
    document.getElementById('actionsModalTitle').textContent = ruleName;
    document.getElementById('ruleActionsModal').classList.add('show');
}

function closeActionsModal() {
    document.getElementById('ruleActionsModal').classList.remove('show');
    currentRuleId = null;
}

function viewRuleFromActions() {
    if (!currentRuleId) return;
    closeActionsModal();
    viewRule(currentRuleId);
}

function viewRule(ruleId) {
    if (!ruleId && !currentRuleId) return;
    
    const targetRuleId = ruleId || currentRuleId;
    
    fetch(`<?= base_url('notifications/getRule') ?>/${targetRuleId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const rule = data.rule;
            currentRuleId = rule.id;
            currentRuleData = rule;
            
            // Format target type
            let targetType = 'All Users';
            let targetValues = '';
            if (rule.target_roles) {
                targetType = 'Roles';
                targetValues = rule.target_roles;
            } else if (rule.target_divisions) {
                targetType = 'Divisions';
                targetValues = rule.target_divisions;
            } else if (rule.target_departments) {
                targetType = 'Departments';
                targetValues = rule.target_departments;
            } else if (rule.target_users) {
                targetType = 'Users';
                targetValues = rule.target_users;
            }
            
            const priorityText = ['Low', 'Medium', 'High'];
            const priority = priorityText[rule.priority] || 'Medium';
            const priorityColors = ['success', 'warning', 'danger'];
            const priorityColor = priorityColors[rule.priority] || 'warning';
            
            const detailsHtml = `
                <div class="rule-details">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Rule Name:</strong></div>
                        <div class="col-sm-8">${rule.name}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Trigger Event:</strong></div>
                        <div class="col-sm-8"><span class="badge bg-secondary">${rule.trigger_event}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Title Template:</strong></div>
                        <div class="col-sm-8">${rule.title_template}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Message Template:</strong></div>
                        <div class="col-sm-8">${rule.message_template}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Target Type:</strong></div>
                        <div class="col-sm-8">
                            <span class="rule-target">
                                <i class="fas fa-users"></i>
                                ${targetType}${targetValues ? ' (' + targetValues + ')' : ''}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Priority:</strong></div>
                        <div class="col-sm-8"><span class="badge bg-${priorityColor}">${priority}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            <span class="rule-status ${rule.is_active ? 'status-active' : 'status-inactive'}">
                                ${rule.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Created:</strong></div>
                        <div class="col-sm-8">${rule.created_at || 'N/A'}</div>
                    </div>
                </div>
            `;
            
            document.getElementById('ruleDetailsContent').innerHTML = detailsHtml;
            document.getElementById('viewRuleModal').classList.add('show');
        } else {
            alert('Failed to load rule details: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load rule details');
    });
}

function editRuleFromDetail() {
    if (!currentRuleData) {
        console.error('currentRuleData is null - cannot edit rule');
        alert('Rule data not available. Please try again.');
        return;
    }
    
    closeViewModal();
    
    // Populate modal with rule data
    document.getElementById('modalTitle').textContent = 'Edit Notification Rule';
    document.getElementById('ruleName').value = currentRuleData.name || '';
    document.getElementById('triggerEvent').value = currentRuleData.trigger_event || '';
    document.getElementById('titleTemplate').value = currentRuleData.title_template || '';
    document.getElementById('messageTemplate').value = currentRuleData.message_template || '';
    document.getElementById('priority').value = currentRuleData.priority || 1;
    document.getElementById('isActive').checked = currentRuleData.is_active == 1;
    
    // Handle target type logic
    let targetType = 'all';
    if (currentRuleData.target_roles) targetType = 'role';
    else if (currentRuleData.target_divisions) targetType = 'division';
    else if (currentRuleData.target_departments) targetType = 'department';
    else if (currentRuleData.target_users) targetType = 'user';
    
    document.getElementById('targetType').value = targetType;
    document.getElementById('targetType').dispatchEvent(new Event('change'));
    
    // Set form action to update
    const form = document.getElementById('ruleForm');
    form.dataset.ruleId = currentRuleId;
    form.dataset.action = 'update';
    
    // Show modal
    document.getElementById('ruleModal').classList.add('show');
}

async function deleteRuleFromDetail() {
    if (!currentRuleId) return;
    
    const confirmed = await confirmSwal({
        title: 'Hapus Notification Rule',
        text: 'Apakah Anda yakin ingin menghapus rule notifikasi ini?',
        type: 'delete'
    });
    if (!confirmed) return;
    
    fetch(`<?= base_url('notifications/deleteRule') ?>/${currentRuleId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeViewModal();
            location.reload();
        } else {
            alertSwal('error', data.message, 'Gagal Hapus Rule');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertSwal('error', 'Gagal menghapus rule');
    });
}

function closeViewModal() {
    document.getElementById('viewRuleModal').classList.remove('show');
    currentRuleData = null;
}

function showCreateModal() {
    document.getElementById('modalTitle').textContent = 'Create Notification Rule';
    const form = document.getElementById('ruleForm');
    form.reset();
    form.dataset.action = 'create';
    delete form.dataset.ruleId;
    document.getElementById('ruleModal').classList.add('show');
    document.getElementById('isActive').checked = true;
    document.getElementById('targetValuesGroup').style.display = 'none';
}

function closeModal() {
    document.getElementById('ruleModal').classList.remove('show');
}

function editRule() {
    if (!currentRuleId) return;
    
    // Fetch rule data
    fetch(`<?= base_url('notifications/getRule') ?>/${currentRuleId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const rule = data.rule;
            
            // Populate modal with rule data
            document.getElementById('modalTitle').textContent = 'Edit Notification Rule';
            document.getElementById('ruleName').value = rule.name || '';
            document.getElementById('triggerEvent').value = rule.trigger_event || '';
            document.getElementById('titleTemplate').value = rule.title_template || '';
            document.getElementById('messageTemplate').value = rule.message_template || '';
            document.getElementById('priority').value = rule.priority || 1;
            document.getElementById('isActive').checked = rule.is_active == 1;
            
            // Handle target type logic
            let targetType = 'all';
            if (rule.target_roles) targetType = 'role';
            else if (rule.target_divisions) targetType = 'division';
            else if (rule.target_departments) targetType = 'department';
            else if (rule.target_users) targetType = 'user';
            
            document.getElementById('targetType').value = targetType;
            document.getElementById('targetType').dispatchEvent(new Event('change'));
            
            // Set form action to update
            const form = document.getElementById('ruleForm');
            form.dataset.ruleId = currentRuleId;
            form.dataset.action = 'update';
            
            // Show modal
            closeActionsModal();
            document.getElementById('ruleModal').classList.add('show');
        } else {
            alert('Failed to load rule: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load rule data');
    });
}

async function testRule() {
    if (!currentRuleId) return;
    
    const confirmed = await confirmSwal({
        title: 'Test Notification Rule',
        text: 'Kirim notifikasi test untuk rule ini?',
        icon: 'info',
        confirmText: '<i class="fas fa-play me-1"></i>Ya, Test'
    });
    if (!confirmed) return;
    
    fetch(`<?= base_url('notifications/testRule') ?>/${currentRuleId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({
            test_data: {
                spk_id: 'TEST_001',
                created_by: '<?= session()->get('name') ?>',
                timestamp: new Date().toISOString()
            }
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertSwal('success', 'Test rule berhasil!');
        } else {
            alertSwal('error', data.message, 'Test Gagal');
        }
        closeActionsModal();
    })
    .catch(error => {
        console.error('Error:', error);
        alertSwal('error', 'Gagal menjalankan test rule');
    });
}

async function deleteRule() {
    if (!currentRuleId) return;
    
    const confirmed = await confirmSwal({
        title: 'Hapus Notification Rule',
        text: 'Apakah Anda yakin ingin menghapus rule notifikasi ini?',
        type: 'delete'
    });
    if (!confirmed) return;
    
    fetch(`<?= base_url('notifications/deleteRule') ?>/${currentRuleId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeActionsModal();
            location.reload();
        } else {
            alertSwal('error', data.message, 'Gagal Hapus Rule');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertSwal('error', 'Gagal menghapus rule');
    });
}

function refreshRules() {
    location.reload();
}

function toggleTargetingMode(mode) {
    const legacySection = document.getElementById('legacyTargeting');
    const multiSection = document.getElementById('multiTargeting');
    
    if (mode === 'multi') {
        legacySection.style.display = 'none';
        multiSection.style.display = 'block';
    } else {
        legacySection.style.display = 'block';
        multiSection.style.display = 'none';
    }
}

function loadUsers() {
    const multiUsers = document.getElementById('multiUsers');
    
    // Clear existing options
    multiUsers.innerHTML = '';
    
    // Use available users data from PHP
    if (availableUsers && availableUsers.length > 0) {
        availableUsers.forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = `${user.first_name} ${user.last_name} (${user.division || 'No Division'})`;
            multiUsers.appendChild(option);
        });
    } else {
        multiUsers.innerHTML = '<option value="">No users available</option>';
    }
}

function loadUsersForTargetSelect() {
    const valuesSelect = document.getElementById('targetValues');
    
    // Clear existing options
    valuesSelect.innerHTML = '';
    
    // Use available users data from PHP
    if (availableUsers && availableUsers.length > 0) {
        availableUsers.forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = `${user.first_name} ${user.last_name} (${user.division || 'No Division'})`;
            valuesSelect.appendChild(option);
        });
    } else {
        valuesSelect.innerHTML = '<option value="">No users available</option>';
    }
}

// Handle targeting modes and target type change
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for targeting mode change
    document.querySelectorAll('input[name="targeting_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            toggleTargetingMode(this.value);
        });
    });

    // Load users for multi-target mode
    loadUsers();

    document.getElementById('targetType').addEventListener('change', function() {
        const targetType = this.value;
        const valuesGroup = document.getElementById('targetValuesGroup');
        const valuesSelect = document.getElementById('targetValues');
        
        if (targetType === 'all') {
            valuesGroup.style.display = 'none';
            return;
        }
        
        valuesGroup.style.display = 'block';
        valuesSelect.innerHTML = '';
        
        let options = [];
        switch (targetType) {
            case 'role':
                options = availableRoles;
                break;
            case 'division':
                options = availableDivisions;
                break;
            case 'department':
                options = availableDepartments;
                break;
            case 'user':
                // Load users dynamically via AJAX
                loadUsersForTargetSelect();
                return; // Exit early since we're loading async
        }
        
        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option;
            optionElement.textContent = option;
            valuesSelect.appendChild(optionElement);
        });
    });

    // Handle form submission
    document.getElementById('ruleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Check targeting mode
        const targetingMode = document.querySelector('input[name="targeting_mode"]:checked').value;
        
        if (targetingMode === 'multi') {
            // Handle multi-targeting
            data.use_multi_target = true;
            
            // Get selected values from multi-target selects
            data.multi_divisions = Array.from(document.getElementById('multiDivisions').selectedOptions)
                                        .map(option => option.value);
            data.multi_roles = Array.from(document.getElementById('multiRoles').selectedOptions)
                                    .map(option => option.value);
            data.multi_departments = Array.from(document.getElementById('multiDepartments').selectedOptions)
                                          .map(option => option.value);
            data.multi_users = Array.from(document.getElementById('multiUsers').selectedOptions)
                                    .map(option => option.value);
        } else {
            // Handle legacy single targeting
            data.use_multi_target = false;
            const targetValues = Array.from(document.getElementById('targetValues').selectedOptions)
                                      .map(option => option.value);
            data.target_values = targetValues;
        }
        
        // Check if this is an update or create
        const isUpdate = this.dataset.action === 'update';
        const ruleId = this.dataset.ruleId;
        
        const url = isUpdate 
            ? `<?= base_url('notifications/updateRule') ?>/${ruleId}`
            : '<?= base_url('notifications/createRule') ?>';
        
        const method = isUpdate ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                location.reload();
            } else {
                alert('Failed to save rule: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to save rule');
        });
    });

    // Close modal when clicking outside
    document.getElementById('ruleModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    document.getElementById('ruleActionsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeActionsModal();
        }
    });
    
    document.getElementById('viewRuleModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeViewModal();
        }
    });
});
</script>

<?= $this->endSection() ?>
