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

    <!-- Notification Rules Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>Notification Rules
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchRules" class="form-control border-0 bg-light" 
                               placeholder="Search rules...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Division Tabs -->
            <div class="border-bottom">
                <ul class="nav nav-tabs nav-tabs-clean" id="divisionTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" 
                                type="button" role="tab" data-division="all">
                            <i class="fas fa-globe me-2"></i>All Divisions
                        </button>
                    </li>
                    <?php if (!empty($divisions)): ?>
                        <?php foreach ($divisions as $division): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="div-<?= $division['id'] ?>-tab" 
                                        data-bs-toggle="tab" data-bs-target="#div-<?= $division['id'] ?>" 
                                        type="button" role="tab" data-division="<?= esc($division['name']) ?>">
                                    <i class="fas fa-building me-2"></i><?= esc($division['name']) ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Table -->
            <div class="table-responsive">
                <table id="rulesTable" class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Rule Name</th>
                            <th>Event Type</th>
                            <th>Target</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Status</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated by DataTables -->
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
                            <div class="checkbox-group mt-2 border rounded p-2" id="target_divisions_container">
                                <div class="text-muted text-center py-3">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading divisions...
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Select divisions to auto-filter users. Leave empty to target all divisions.
                            </small>
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
                            <div class="checkbox-group mt-2 border rounded p-2" id="target_roles_container">
                                <div class="text-muted text-center py-3">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading roles...
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Select roles to further filter users. Leave empty to target all roles.
                            </small>
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
                            <div class="checkbox-group mt-2 border rounded p-2" id="target_users_container">
                                <div class="text-muted text-center py-3">
                                    <i class="fas fa-users"></i> Loading users...
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-magic me-1 text-primary"></i>
                                <strong>Auto-filtered:</strong> Users list will update based on selected divisions/roles. 
                                Leave empty to target all users in selected divisions/roles.
                            </small>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Title Template <span class="text-danger">*</span></label>
                                <button type="button" id="variablesInfoBtn" class="btn btn-sm btn-outline-info" onclick="showVariablesInfo()" title="View all available variables">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control" id="title_template" name="title_template" required placeholder="Use {{variable}} for dynamic values">
                            <small class="text-muted" id="title_variables_hint"><i class="fas fa-search me-1"></i>Click "Available Variables" to browse all 118 events with search</small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Message Template</label>
                            <textarea class="form-control" id="message_template" name="message_template" rows="3" placeholder="Notification message with {{variables}}"></textarea>
                            <small class="text-muted" id="message_variables_hint">Use {{variable_name}} format to insert dynamic values</small>
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

<!-- Variables Info Modal -->
<div class="modal fade" id="variablesModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-l">
        <div class="modal-content">
            <div class="modal-header bg-primary text-muted">
                <h5 class="modal-title"><i class="fas fa-code me-2"></i>Available Variables</h5>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong id="variablesCount">118 events</strong> found. Click any variable to copy.
                </div>
                <div class="mb-3">
                    <input type="text" id="variableSearchInput" class="form-control form-control-lg" placeholder="🔍 Type to search events or variables..." autocomplete="off">
                </div>
                <div id="variablesContainer" style="max-height: 500px; overflow-y: auto;">
                    <!-- Variables will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
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

// Add custom styles for variables modal to ensure it's on top
const style = document.createElement('style');
style.textContent = `
    .variables-modal-container {
        z-index: 10000 !important;
    }
    .variables-modal-popup {
        z-index: 10001 !important;
    }
    .swal2-container.variables-modal-container .swal2-popup {
        z-index: 10001 !important;
    }
    .swal2-container.variables-modal-container {
        z-index: 10000 !important;
    }
`;
document.head.appendChild(style);

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
            label.textContent = displayLabel;

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
    
    // Debug: Log form data
    console.log('📤 Submitting notification rule...');
    console.log('Rule ID:', ruleId ? ruleId : 'NEW');
    console.log('URL:', url);
    console.log('Form data entries:');
    for (let pair of formData.entries()) {
        console.log(pair[0], '=', pair[1]);
    }
    
    // Auto-cascade logic: If only divisions selected, auto-add all roles in those divisions
    const selectedDivisions = formData.getAll('target_divisions[]');
    const selectedRoles = formData.getAll('target_roles[]');
    const selectedUsers = formData.getAll('target_users[]');
    
    console.log('Selected Divisions:', selectedDivisions);
    console.log('Selected Roles:', selectedRoles);
    console.log('Selected Users:', selectedUsers);
    
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
        console.log('Response:', result);
        
        if (result.success) {
            // Close modal
            const modalElement = document.getElementById('ruleModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });
            
            // Reload DataTable instead of full page
            if (ruleId && result.rule) {
                // Update: refresh DataTable row
                const table = $('#rulesTable').DataTable();
                
                // Find and update the row
                table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                    const data = this.data();
                    if (data.id == ruleId) {
                        // Update row data with new data
                        this.data(result.rule).draw(false);
                        console.log('✅ Updated row in DataTable');
                        return false; // Break loop
                    }
                });
            } else {
                // Create: reload full page to get new data
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        } else {
            Swal.fire('Error', result.message || 'Failed to save rule', 'error');
        }
    } catch (error) {
        console.error('Submit error:', error);
        Swal.fire('Error', 'Failed to save rule: ' + error.message, 'error');
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

// ========================================================================
// SEARCH AND FILTER FUNCTIONALITY
// ========================================================================

let allRules = <?= json_encode($rules) ?>;
let rulesTable;
let currentDivisionFilter = 'all';

// Debug: Log data untuk melihat strukturnya
console.log('📊 All Rules Data:', allRules);
console.log('📊 Total Rules:', allRules.length);

// Initialize DataTable
document.addEventListener('DOMContentLoaded', function() {
    rulesTable = $('#rulesTable').DataTable({
        data: allRules,
        columns: [
            { 
                data: 'id',
                className: 'text-center',
                width: '50px'
            },
            { 
                data: 'name',
                render: function(data, type, row) {
                    return `<i class="fas fa-bell me-2 text-primary"></i><strong class="text-dark">${data || 'N/A'}</strong>`;
                }
            },
            { 
                data: 'trigger_event',
                render: function(data) {
                    return `<span class="badge bg-secondary">${data || 'N/A'}</span>`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    let badges = [];
                    
                    // Parse divisions
                    if (row.target_divisions) {
                        const divs = row.target_divisions.split(',').map(d => d.trim());
                        divs.forEach(div => {
                            badges.push(`<span class="badge bg-info me-1">${div}</span>`);
                        });
                    }
                    
                    // Department
                    if (row.target_department) {
                        badges.push(`<span class="badge bg-warning me-1">${row.target_department}</span>`);
                    }
                    
                    // Role
                    if (row.target_role) {
                        badges.push(`<span class="badge bg-primary me-1">${row.target_role}</span>`);
                    }
                    
                    if (badges.length === 0) {
                        return '<span class="text-muted small">All Users</span>';
                    }
                    
                    return badges.join('');
                }
            },
            {
                data: 'type',
                className: 'text-center',
                width: '100px',
                render: function(data) {
                    const typeColors = {
                        'info': 'primary',
                        'success': 'success',
                        'warning': 'warning',
                        'error': 'danger'
                    };
                    const color = typeColors[data] || 'secondary';
                    return `<span class="badge bg-${color}">${data || 'info'}</span>`;
                }
            },
            {
                data: 'is_active',
                className: 'text-center',
                width: '80px',
                render: function(data, type, row) {
                    const checked = data == 1 ? 'checked' : '';
                    return `<div class="form-check form-switch d-inline-block">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="status_${row.id}"
                               ${checked}
                               onchange="toggleRuleStatus(${row.id})">
                    </div>`;
                }
            },
            {
                data: 'created_at',
                width: '100px',
                render: function(data) {
                    if (!data) return '-';
                    const date = new Date(data);
                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric', 
                        month: 'short', 
                        year: 'numeric'
                    });
                }
            },
            {
                data: 'id',
                className: 'text-center',
                width: '120px',
                orderable: false,
                render: function(data) {
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-info" onclick="viewRuleDetail(${data})" 
                                    title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning" onclick="editRule(${data})" 
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteRule(${data})" 
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        pageLength: 15,
        order: [[0, 'desc']],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        language: {
            lengthMenu: "Show _MENU_",
            search: "",
            searchPlaceholder: "Search...",
            info: "Showing _START_ to _END_ of _TOTAL_ rules",
            infoEmpty: "No rules found",
            infoFiltered: "(filtered from _MAX_ total)",
            zeroRecords: "No matching rules found",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                previous: '<i class="fas fa-angle-left"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                last: '<i class="fas fa-angle-double-right"></i>'
            }
        },
    });
    
    // Custom search box
    $('#searchRules').on('keyup', function() {
        rulesTable.search(this.value).draw();
    });
    
    // Division tab filter
    $('[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const division = $(e.target).data('division');
        currentDivisionFilter = division;
        filterByDivision(division);
    });
    
    // Initialize variables modal on page load
    loadNotificationVariables();
    
    // Update hint when event type changes
    $('#event_type').on('change', function() {
        const selectedEvent = $(this).val();
        const eventData = availableVariables[selectedEvent];
        
        if (eventData && eventData.variables && eventData.variables.length > 0) {
            const varCount = eventData.variables.length;
            const firstThree = eventData.variables.slice(0, 3).map(v => `{{${v}}}`).join(', ');
            const hintText = varCount > 3 
                ? `${varCount} variables available: ${firstThree}...` 
                : `${varCount} variable(s): ${firstThree}`;
            
            $('#title_variables_hint').html(`<i class="fas fa-check-circle text-success me-1"></i>${hintText}`);
            $('#message_variables_hint').html(`<i class="fas fa-lightbulb text-warning me-1"></i>Click "Available Variables" button to see all ${varCount} options`);
        } else {
            $('#title_variables_hint').text('No variables defined for this event');
            $('#message_variables_hint').text('Use {{variable_name}} format to insert dynamic values');
        }
    });
});

// Filter by division
function filterByDivision(division) {
    if (division === 'all') {
        // Show all
        rulesTable.column(3).search('').draw();
    } else {
        // Filter by specific division - search in target column
        rulesTable.column(3).search(division, false, false).draw();
    }
}

// Variables info modal functionality
let availableVariables = {};

async function loadNotificationVariables() {
    try {
        const response = await fetch(baseUrl + '/assets/data/notification_variables.json');
        if (!response.ok) {
            throw new Error('Failed to load notification variables');
        }
        availableVariables = await response.json();
        console.log('Notification variables loaded:', Object.keys(availableVariables).length, 'events');
    } catch (error) {
        console.error('Error loading notification variables:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load notification variables data'
        });
    }
}

function showVariablesInfo() {
    // Sort events alphabetically
    const sortedEvents = Object.keys(availableVariables).sort();
    
    // Build simple list of all variables
    let variablesHtml = '';
    
    sortedEvents.forEach(eventName => {
        const eventData = availableVariables[eventName];
        if (!eventData || !eventData.variables || eventData.variables.length === 0) return;
        
        const varCount = eventData.variables.length;
        const varsText = eventData.variables.join(' ');
        
        variablesHtml += `
            <div class="event-group mb-3 p-3 border rounded" data-event="${eventName.toLowerCase()}" data-vars="${varsText.toLowerCase()}">
                <div class="mb-2">
                    <h6 class="mb-1 text-primary">${eventName} <span class="badge bg-secondary">${varCount}</span></h6>
                </div>
                <div class="row g-2">
        `;
        
        eventData.variables.forEach(variable => {
            const varName = `{{${variable}}}`;
            variablesHtml += `
                <div class="col-md-6">
                    <div class="p-2 bg-light rounded" onclick="copyVariable('${varName}')" style="cursor: pointer;" title="Click to copy">
                        <code class="text-dark">${varName}</code>
                    </div>
                </div>
            `;
        });
        
        variablesHtml += `
                </div>
            </div>
        `;
    });
    
    // Update modal content
    document.getElementById('variablesContainer').innerHTML = variablesHtml;
    document.getElementById('variablesCount').textContent = `${sortedEvents.length} events`;
    
    // Show modal
    const variablesModal = new bootstrap.Modal(document.getElementById('variablesModal'));
    variablesModal.show();
    
    // Setup search after modal is shown
    setTimeout(() => {
        const searchInput = document.getElementById('variableSearchInput');
        const eventGroups = document.querySelectorAll('.event-group');
        
        searchInput.value = '';
        searchInput.focus();
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            eventGroups.forEach(group => {
                const eventName = group.getAttribute('data-event');
                const vars = group.getAttribute('data-vars');
                
                if (searchTerm === '' || eventName.includes(searchTerm) || vars.includes(searchTerm)) {
                    group.style.display = 'block';
                } else {
                    group.style.display = 'none';
                }
            });
        });
    }, 300);
}


// Simple copy function
window.copyVariable = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: `Copied: ${text}`
        });
    });
};

function copyToClipboard(text, element) {
    navigator.clipboard.writeText(text).then(() => {
        // Visual feedback
        const originalBg = element.style.backgroundColor;
        element.style.backgroundColor = '#d4edda';
        element.style.transition = 'background-color 0.3s';
        
        // Show toast notification
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        
        Toast.fire({
            icon: 'success',
            title: `Copied: ${text}`
        });
        
        // Reset background after animation
        setTimeout(() => {
            element.style.backgroundColor = originalBg;
        }, 500);
    }).catch(err => {
        console.error('Failed to copy:', err);
        Swal.fire({
            icon: 'error',
            title: 'Copy Failed',
            text: 'Failed to copy to clipboard',
            timer: 2000
        });
    });
}



</script>

<?php $this->endSection(); ?>

