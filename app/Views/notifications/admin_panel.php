<?php $this->extend('layouts/base'); ?>

<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-bell me-2"></i>Notification Management</h2>
            <p class="text-muted mb-0">Manage notification rules and settings</p>
        </div>
        <button class="btn btn-primary" onclick="showCreateRuleModal()">
            <i class="fas fa-plus me-2"></i>Create Notification Rule
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary bg-soft text-primary">
                                <i class="fas fa-rules fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Total Rules</h6>
                            <h3 class="mb-0 fw-bold"><?= $stats['total_rules'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-soft text-success">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Active Rules</h6>
                            <h3 class="mb-0 fw-bold"><?= $stats['active_rules'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info bg-soft text-info">
                                <i class="fas fa-bell fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Total Notifications</h6>
                            <h3 class="mb-0 fw-bold"><?= number_format($stats['total_notifications']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning bg-soft text-warning">
                                <i class="fas fa-calendar-day fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Today</h6>
                            <h3 class="mb-0 fw-bold"><?= number_format($stats['today_notifications']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .checkbox-group-title {
            letter-spacing: 0.08em;
        }
    </style>

    <!-- Notification Rules Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Notification Rules</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="rulesTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rule Name</th>
                            <th>Event Type</th>
                            <th>Target</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rules as $rule): ?>
                        <tr>
                            <td><?= $rule['id'] ?? 'N/A' ?></td>
                            <td>
                                <i class="fas fa-bell me-2 text-primary"></i>
                                <strong><?= esc($rule['name'] ?? 'N/A') ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= esc($rule['trigger_event'] ?? 'N/A') ?></span>
                            </td>
                            <td>
                                <?php if (!empty($rule['target_divisions'] ?? null)): ?>
                                    <span class="badge bg-info"><?= esc($rule['target_divisions']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($rule['target_department'] ?? null)): ?>
                                    <span class="badge bg-warning"><?= esc($rule['target_department']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($rule['target_role'] ?? null)): ?>
                                    <span class="badge bg-primary"><?= esc($rule['target_role']) ?></span>
                                <?php endif; ?>
                                <?php if (empty($rule['target_divisions'] ?? null) && empty($rule['target_department'] ?? null) && empty($rule['target_role'] ?? null)): ?>
                                    <span class="text-muted">All Users</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $typeColors = [
                                    'info' => 'primary',
                                    'success' => 'success',
                                    'warning' => 'warning',
                                    'error' => 'danger'
                                ];
                                $color = $typeColors[$rule['type'] ?? 'info'] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= esc($rule['type'] ?? 'N/A') ?></span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="status_<?= $rule['id'] ?? 0 ?>"
                                           <?= ($rule['is_active'] ?? false) ? 'checked' : '' ?>
                                           onchange="toggleRuleStatus(<?= $rule['id'] ?? 0 ?>)">
                                </div>
                            </td>
                            <td><?= date('d M Y', strtotime($rule['created_at'] ?? 'now')) ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info" onclick="viewRuleDetail(<?= $rule['id'] ?? 0 ?>)" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editRule(<?= $rule['id'] ?? 0 ?>)" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteRule(<?= $rule['id'] ?? 0 ?>)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Rule Modal -->
<div class="modal fade" id="ruleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ruleModalTitle">Create Notification Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="ruleForm">
                <div class="modal-body">
                    <input type="hidden" id="rule_id" name="rule_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Rule Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Event Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="event_type" name="event_type" required>
                                <option value="">Select event type</option>
                            </select>
                            <small class="text-muted">Options are loaded automatically from existing notification rules.</small>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">Target Divisions</label>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Toggle target divisions">
                                    <button type="button" class="btn btn-light" onclick="toggleCheckboxGroup('divisions', true)">
                                        <i class="fas fa-check-double me-1"></i>All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleCheckboxGroup('divisions', false)">
                                        <i class="fas fa-eraser me-1"></i>Clear
                                    </button>
                                </div>
                            </div>
                            <div class="checkbox-group mt-2" id="target_divisions_container" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; padding: 10px;">
                                <div class="text-muted text-center py-3">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading divisions...
                                </div>
                            </div>
                            <small class="text-muted">Select one or more divisions. Leave empty to target all divisions.</small>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">Target Roles</label>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Toggle target roles">
                                    <button type="button" class="btn btn-light" onclick="toggleCheckboxGroup('roles', true)">
                                        <i class="fas fa-check-double me-1"></i>All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleCheckboxGroup('roles', false)">
                                        <i class="fas fa-eraser me-1"></i>Clear
                                    </button>
                                </div>
                            </div>
                            <div class="checkbox-group mt-2" id="target_roles_container" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; padding: 10px;">
                                <div class="text-muted text-center py-3">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading roles...
                                </div>
                            </div>
                            <small class="text-muted">Select one or more roles. Leave empty to target all roles.</small>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">Target Users (Optional)</label>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Toggle target users">
                                    <button type="button" class="btn btn-light" onclick="toggleCheckboxGroup('users', true)">
                                        <i class="fas fa-check-double me-1"></i>All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleCheckboxGroup('users', false)">
                                        <i class="fas fa-eraser me-1"></i>Clear
                                    </button>
                                </div>
                            </div>
                            <div class="checkbox-group mt-2" id="target_users_container" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; padding: 10px;">
                                <div class="text-muted text-center py-3">
                                    <i class="fas fa-users"></i> Loading users...
                                </div>
                            </div>
                            <small class="text-muted">Leave empty to target all users in selected divisions/roles</small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Title Template <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title_template" name="title_template" required placeholder="Use {{variable}} for dynamic values">
                            <small class="text-muted">Available variables: {{nomor_spk}}, {{nomor_po}}, {{pelanggan}}, {{departemen}}, etc.</small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Message Template</label>
                            <textarea class="form-control" id="message_template" name="message_template" rows="3" placeholder="Notification message with {{variables}}"></textarea>
                        </div>
                        
                        
                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="info">Info</option>
                                <option value="success">Success</option>
                                <option value="warning">Warning</option>
                                <option value="error">Error</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Rule Detail Modal -->
<div class="modal fade" id="viewRuleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Rule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="ruleDetailContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = '<?= csrf_hash() ?>';
const baseUrl = '<?= base_url() ?>';

let notificationMetadataLoaded = false;
let notificationMetadataPromise = null;
let notificationMetadata = null;
let cascadingListenersInitialized = false;

window.notificationRuleSelectionCache = window.notificationRuleSelectionCache || {
    eventType: '',
    divisions: [],
    roles: [],
    users: []
};

// Utility: Convert event_type string to readable label
function formatEventTypeLabel(value) {
    if (!value) return '';
    return value
        .toString()
        .trim()
        .toLowerCase()
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

function slugifyValue(value, fallback = 'item') {
    const slug = value
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '_')
        .replace(/^_+|_+$/g, '');
    return slug || fallback;
}

function getSelectedValues(selector) {
    return Array.from(document.querySelectorAll(selector))
        .filter(cb => cb.checked)
        .map(cb => cb.value?.toString().trim())
        .filter(Boolean);
}

async function loadUsersWithFilters() {
    if (!notificationMetadataLoaded) {
        await ensureNotificationMetadata();
    }

    const selectedDivisions = getSelectedValues('input[name="target_divisions[]"]');
    const selectedRoles = getSelectedValues('input[name="target_roles[]"]');

    window.notificationRuleSelectionCache.divisions = selectedDivisions;
    window.notificationRuleSelectionCache.roles = selectedRoles;

    if (selectedDivisions.length === 0 && selectedRoles.length === 0) {
        await loadAllUsers();
        return;
    }

    try {
        const response = await fetch(`${baseUrl}/admin/advanced-users/get-users-by-divisions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                divisions: selectedDivisions,
                roles: selectedRoles
            })
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success && Array.isArray(data.users)) {
                populateUserCheckboxes(data.users);
            } else {
                populateUserCheckboxes([]);
            }
        }
    } catch (error) {
        console.error('Error loading users with filters:', error);
    }
}

async function ensureNotificationMetadata(forceReload = false) {
    if (notificationMetadataLoaded && !forceReload && notificationMetadata) {
        return notificationMetadata;
    }

    if (notificationMetadataPromise && !forceReload) {
        return notificationMetadataPromise;
    }

    notificationMetadataPromise = fetch(`${baseUrl}/notifications/options/metadata`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(result => {
            if (!result?.success || !result?.data) {
                throw new Error(result?.message || 'Invalid metadata response');
            }
            notificationMetadata = result.data;
            renderNotificationMetadata(notificationMetadata);
            notificationMetadataLoaded = true;
            return notificationMetadata;
        })
        .catch(error => {
            notificationMetadataLoaded = false;
            console.error('❌ Failed to load notification metadata:', error);
            throw error;
        });

    return notificationMetadataPromise;
}

function renderNotificationMetadata(metadata) {
    if (!metadata) return;

    renderEventTypeOptions(metadata.event_types ?? []);
    renderTargetCheckboxGroup('target_divisions_container', metadata.divisions ?? {}, 'divisions');
    renderTargetCheckboxGroup('target_roles_container', metadata.roles ?? {}, 'roles');
}

function renderEventTypeOptions(options) {
    const select = document.getElementById('event_type');
    if (!select) return;

    const previous = window.notificationRuleSelectionCache.eventType || select.value;

    Array.from(select.querySelectorAll('option[data-generated="true"]')).forEach(opt => opt.remove());

    options.forEach(optionData => {
        if (!optionData || !optionData.value) {
            return;
        }

        const value = optionData.value.toString().trim();
        if (value === '') return;

        let option = Array.from(select.options).find(opt => opt.value === value);
        if (!option) {
            option = document.createElement('option');
            option.value = value;
            option.dataset.generated = 'true';
            select.appendChild(option);
        }

        const count = optionData.rule_count ? ` (${optionData.rule_count})` : '';
        option.textContent = optionData.label ? `${optionData.label}${count}` : `${formatEventTypeLabel(value)}${count}`;
    });

    if (previous) {
        select.value = previous;
    } else {
        select.selectedIndex = 0;
        window.notificationRuleSelectionCache.eventType = select.value;
    }
}

function renderTargetCheckboxGroup(containerId, groupData, cacheKey) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const cacheValues = window.notificationRuleSelectionCache[cacheKey] ?? [];

    container.innerHTML = '';

    const renderSection = (title, items, source) => {
        if (!Array.isArray(items) || !items.length) {
            return;
        }

        const header = document.createElement('div');
        header.className = 'checkbox-group-title text-muted small text-uppercase fw-semibold mb-1 mt-2';
        header.textContent = title;
        container.appendChild(header);

        items.forEach(item => {
            if (!item?.value) return;

            const rawValue = item.value.toString().trim();
            if (rawValue === '') return;

            const checkboxId = `${cacheKey}_${slugifyValue(rawValue)}_${source}`;
            const wrapper = document.createElement('div');
            wrapper.className = 'form-check';

            const checkbox = document.createElement('input');
            checkbox.className = 'form-check-input';
            checkbox.type = 'checkbox';
            checkbox.name = cacheKey === 'divisions' ? 'target_divisions[]' : 'target_roles[]';
            checkbox.value = rawValue;
            checkbox.id = checkboxId;
            checkbox.dataset.source = source;

            const label = document.createElement('label');
            label.className = 'form-check-label';
            label.htmlFor = checkboxId;

            const displayLabel = item.label ?? formatEventTypeLabel(rawValue);
            label.textContent = source === 'legacy'
                ? `${displayLabel} (Legacy)`
                : displayLabel;

            if (item.count) {
                const badge = document.createElement('span');
                badge.className = 'badge bg-light text-muted ms-2';
                badge.textContent = item.count;
                label.appendChild(badge);
            }

            wrapper.appendChild(checkbox);
            wrapper.appendChild(label);
            container.appendChild(wrapper);
        });
    };

    renderSection('Organization', groupData.official ?? [], 'official');
    renderSection('Legacy Targets', groupData.legacy ?? [], 'legacy');

    applySelectionToCheckboxes(
        container.querySelectorAll('input[type="checkbox"]'),
        cacheValues
    );
}

function applySelectionToCheckboxes(nodeList, selectedValues = []) {
    if (!nodeList) return;
    const normalized = new Set(
        (selectedValues || []).map(value => value?.toString().trim().toLowerCase()).filter(Boolean)
    );

    nodeList.forEach(node => {
        if (!node || typeof node.value === 'undefined') return;
        const compare = node.value.toString().trim().toLowerCase();
        node.checked = normalized.has(compare);
    });
}

function applySelections() {
    applySelectionToCheckboxes(
        document.querySelectorAll('input[name="target_divisions[]"]'),
        window.notificationRuleSelectionCache.divisions
    );

    applySelectionToCheckboxes(
        document.querySelectorAll('input[name="target_roles[]"]'),
        window.notificationRuleSelectionCache.roles
    );

    applySelectionToCheckboxes(
        document.querySelectorAll('#target_users_container input[name="target_users[]"]'),
        window.notificationRuleSelectionCache.users
    );

    const eventTypeSelect = document.getElementById('event_type');
    if (eventTypeSelect && window.notificationRuleSelectionCache.eventType) {
        eventTypeSelect.value = window.notificationRuleSelectionCache.eventType;
    }
}

// Initialize DataTable - Simplified version
function initializeDataTable() {
    // Simple table initialization without DataTables for now
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async function() {
    initializeDataTable();
    try {
        await ensureNotificationMetadata();
    } catch (error) {
        console.error('Failed to initialize dropdown options on load:', error);
    }

    const eventTypeSelect = document.getElementById('event_type');
    if (eventTypeSelect) {
        eventTypeSelect.addEventListener('change', function () {
            window.notificationRuleSelectionCache.eventType = this.value;
        });
    }

    initializeDropdownCascading();
});

// Initialize dropdown cascading (Division → Role → User) - NOW WITH CHECKBOXES
async function initializeDropdownCascading() {
    if (!cascadingListenersInitialized) {
        document.addEventListener('change', event => {
            if (event.target && event.target.matches('input[name="target_divisions[]"]')) {
                window.notificationRuleSelectionCache.divisions = getSelectedValues('input[name="target_divisions[]"]');
                loadUsersForDivisionsCheckbox();
            } else if (event.target && event.target.matches('input[name="target_roles[]"]')) {
                window.notificationRuleSelectionCache.roles = getSelectedValues('input[name="target_roles[]"]');
                loadUsersForRolesCheckbox();
            } else if (event.target && event.target.matches('#target_users_container input[name="target_users[]"]')) {
                window.notificationRuleSelectionCache.users = getSelectedValues('#target_users_container input[name="target_users[]"]');
            }
        });
        cascadingListenersInitialized = true;
    }
    
    await loadAllUsers();
}

// Toggle helper for checkbox groups
function toggleCheckboxGroup(group, checked) {
    let selector = '';
    switch (group) {
        case 'divisions':
            selector = 'input[name="target_divisions[]"]';
            break;
        case 'roles':
            selector = 'input[name="target_roles[]"]';
            break;
        case 'users':
            selector = '#target_users_container input[name="target_users[]"]';
            break;
        default:
            return;
    }
    
    const checkboxes = document.querySelectorAll(selector);
    checkboxes.forEach(cb => {
        cb.checked = Boolean(checked);
    });
    
    window.notificationRuleSelectionCache.divisions = getSelectedValues('input[name="target_divisions[]"]');
    window.notificationRuleSelectionCache.roles = getSelectedValues('input[name="target_roles[]"]');
    window.notificationRuleSelectionCache.users = getSelectedValues('#target_users_container input[name="target_users[]"]');

    loadUsersWithFilters();
}

// Load all users for selection
async function loadAllUsers() {
    try {
        const response = await fetch(`${baseUrl}/admin/advanced-users/get-users`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.users) {
                populateUserCheckboxes(data.users);
            }
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Load users based on selected divisions (CHECKBOX VERSION)
async function loadUsersForDivisionsCheckbox() {
    await loadUsersWithFilters();
}

// Load users based on selected roles (CHECKBOX VERSION)
async function loadUsersForRolesCheckbox() {
    await loadUsersWithFilters();
}

// Populate user checkboxes (NEW VERSION)
function populateUserCheckboxes(users) {
    const container = document.getElementById('target_users_container');
    if (!container) return;
    
    const previousSelection = window.notificationRuleSelectionCache.users
        ?? Array.from(container.querySelectorAll('input[name="target_users[]"]:checked'))
            .map(cb => cb.value?.toString().trim())
            .filter(Boolean);
    const selectedSet = new Set(previousSelection);

    // Clear existing checkboxes
    container.innerHTML = '';
    
    if (users.length === 0) {
        container.innerHTML = '<div class="text-muted text-center py-3"><i class="fas fa-users-slash"></i> No users found</div>';
        return;
    }
    
    // Add users as checkboxes
    users.forEach(user => {
        const checkDiv = document.createElement('div');
        checkDiv.className = 'form-check';
        
        const checkbox = document.createElement('input');
        checkbox.className = 'form-check-input';
        checkbox.type = 'checkbox';
        checkbox.name = 'target_users[]';
        checkbox.value = user.id;
        checkbox.id = `user_${user.id}`;
        if (selectedSet.has(String(user.id))) {
            checkbox.checked = true;
        }
        
        const label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = `user_${user.id}`;
        label.textContent = `${user.username} (${user.division_name || 'No Division'})`;
        
        checkDiv.appendChild(checkbox);
        checkDiv.appendChild(label);
        container.appendChild(checkDiv);
    });
}

// Show Create Rule Modal
async function showCreateRuleModal() {
    await ensureNotificationMetadata();
    
    document.getElementById('ruleForm').reset();
    document.getElementById('rule_id').value = '';
    document.getElementById('ruleModalTitle').textContent = 'Create Notification Rule';
    
    // Reset all checkboxes
    document.querySelectorAll('input[name="target_divisions[]"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('input[name="target_roles[]"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('#target_users_container input[name="target_users[]"]').forEach(cb => cb.checked = false);
    
    window.notificationRuleSelectionCache = {
        eventType: '',
        divisions: [],
        roles: [],
        users: []
    };

    applySelections();
    
    // Reload all users for fresh selection
    await loadAllUsers();
    
    new bootstrap.Modal(document.getElementById('ruleModal')).show();
}

// Submit Rule Form
document.getElementById('ruleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const ruleId = document.getElementById('rule_id').value;
    const url = ruleId 
        ? `${baseUrl}/notifications/admin/update-rule/${ruleId}`
        : `${baseUrl}/notifications/admin/create-rule`;
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Failed to save rule', 'error');
    }
});

// Edit Rule
async function editRule(ruleId) {
    try {
        const response = await fetch(`${baseUrl}/notifications/get-rule/${ruleId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        if (result.success && result.rule) {
            const rule = result.rule;
            
            await ensureNotificationMetadata();
            
            // Reset form and all checkboxes first
            document.getElementById('ruleForm').reset();
            document.querySelectorAll('input[name="target_divisions[]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name="target_roles[]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('#target_users_container input[name="target_users[]"]').forEach(cb => cb.checked = false);
            
            // Set basic fields
            const eventTypeSelect = document.getElementById('event_type');
            document.getElementById('rule_id').value = rule.id;
            document.getElementById('name').value = rule.name;
            eventTypeSelect.value = rule.trigger_event;
            
            // Ensure event type option exists (handle legacy or custom events)
            if (eventTypeSelect.value !== rule.trigger_event) {
                const dynamicOption = document.createElement('option');
                dynamicOption.value = rule.trigger_event;
                dynamicOption.textContent = formatEventTypeLabel(rule.trigger_event);
                dynamicOption.dataset.dynamic = 'true';
                dynamicOption.dataset.generated = 'true';
                eventTypeSelect.appendChild(dynamicOption);
                eventTypeSelect.value = rule.trigger_event;
            }
            
            document.getElementById('title_template').value = rule.title_template;
            document.getElementById('message_template').value = rule.message_template || '';
            document.getElementById('type').value = rule.type;
            document.getElementById('is_active').value = rule.is_active;
            
            const targetDivisions = rule.target_divisions
                ? rule.target_divisions.split(',').map(d => d.trim()).filter(Boolean)
                : [];
            const targetRoles = rule.target_roles
                ? rule.target_roles.split(',').map(r => r.trim()).filter(Boolean)
                : [];
            const targetUsers = rule.target_users
                ? rule.target_users.split(',').map(u => u.trim()).filter(Boolean)
                : [];

            window.notificationRuleSelectionCache = {
                eventType: rule.trigger_event || '',
                divisions: targetDivisions,
                roles: targetRoles,
                users: targetUsers
            };

            applySelections();
            
            const hasTargetFilters = targetDivisions.length > 0 || targetRoles.length > 0;
            
            // Show modal title
            document.getElementById('ruleModalTitle').textContent = 'Edit Notification Rule';
            
            // Show modal first (so user can see it's loading)
            const modalElement = document.getElementById('ruleModal');
            const ruleModal = bootstrap.Modal.getOrCreateInstance(modalElement);
            ruleModal.show();
            
            // Load appropriate user list based on targets
            if (hasTargetFilters) {
                await loadUsersWithFilters();
            } else {
                await loadAllUsers();
            }

            applySelectionToCheckboxes(
                document.querySelectorAll('#target_users_container input[name="target_users[]"]'),
                window.notificationRuleSelectionCache.users
            );
        } else {
            console.error('❌ Failed to load rule:', result.message);
            Swal.fire('Error', result.message || 'Failed to load rule data', 'error');
        }
    } catch (error) {
        console.error('❌ Error in editRule:', error);
        Swal.fire('Error', 'Failed to load rule data: ' + error.message, 'error');
    }
}

// View Rule Detail
async function viewRuleDetail(ruleId) {
    try {
        const response = await fetch(`${baseUrl}/notifications/get-rule/${ruleId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (result.success && result.rule) {
            const rule = result.rule;
            const typeColors = {
                'info': 'primary',
                'success': 'success',
                'warning': 'warning',
                'error': 'danger'
            };
            
            const content = `
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Rule Name</h6>
                        <p class="mb-0"><i class="fas fa-bell me-2"></i><strong>${rule.name}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Event Type</h6>
                        <span class="badge bg-secondary">${rule.trigger_event}</span>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Type</h6>
                        <span class="badge bg-${typeColors[rule.type]}">${rule.type}</span>
                    </div>
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Target</h6>
                        <p class="mb-0">
                            ${rule.target_divisions ? `<span class="badge bg-info me-1">${rule.target_divisions}</span>` : ''}
                            ${rule.target_department ? `<span class="badge bg-warning me-1">${rule.target_department}</span>` : ''}
                            ${rule.target_role ? `<span class="badge bg-primary me-1">${rule.target_role}</span>` : ''}
                            ${!rule.target_division && !rule.target_department && !rule.target_role ? '<span class="text-muted">All Users</span>' : ''}
                        </p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Title Template</h6>
                        <p class="mb-0 font-monospace">${rule.title_template}</p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Message Template</h6>
                        <p class="mb-0 font-monospace">${rule.message_template || '-'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Status</h6>
                        <span class="badge bg-${rule.is_active ? 'success' : 'secondary'}">${rule.is_active ? 'Active' : 'Inactive'}</span>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Created</h6>
                        <p class="mb-0">${new Date(rule.created_at).toLocaleString()}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('ruleDetailContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('viewRuleModal')).show();
        }
    } catch (error) {
        Swal.fire('Error', 'Failed to load rule details', 'error');
    }
}

// Toggle Rule Status
async function toggleRuleStatus(ruleId) {
    try {
        const response = await fetch(`${baseUrl}/notifications/admin/toggle-status/${ruleId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Rule status updated successfully', 'success');
        } else {
            Swal.fire('Error', result.message, 'error');
            // Revert checkbox
            const checkbox = document.getElementById(`status_${ruleId}`);
            checkbox.checked = !checkbox.checked;
        }
    } catch (error) {
        Swal.fire('Error', 'Failed to update rule status', 'error');
    }
}

// Delete Rule
function deleteRule(ruleId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This notification rule will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`${baseUrl}/notifications/admin/delete-rule/${ruleId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: result.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Failed to delete rule', 'error');
            }
        }
    });
}
</script>

<?php $this->endSection(); ?>

