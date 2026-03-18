<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-shield-alt"></i> <?= esc($title) ?>
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Role Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="roleSelect" class="form-label fw-bold">Select Role:</label>
                            <select id="roleSelect" class="form-select">
                                <option value="">-- Select Role --</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= ($selected_role_id == $role['id'] ? 'selected' : '') ?>>
                                        <?= esc($role['name']) ?>
                                        <?= $role['is_system_role'] ? '<span class="badge bg-secondary">System</span>' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" id="savePermissionsBtn" class="btn btn-success me-2" disabled>
                                <i class="fas fa-save"></i> Save Permissions
                            </button>
                            <button type="button" id="selectAllBtn" class="btn btn-outline-primary me-2" disabled>
                                <i class="fas fa-check-square"></i> Select All
                            </button>
                            <button type="button" id="deselectAllBtn" class="btn btn-outline-secondary" disabled>
                                <i class="fas fa-square"></i> Deselect All
                            </button>
                        </div>
                    </div>

                    <!-- Permission Grid -->
                    <div id="permissionGrid" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            Pilih permissions yang ingin diberikan ke role <strong id="selectedRoleName"></strong>
                        </div>

                        <!-- Module Filter -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" id="searchPermission" class="form-control" placeholder="🔍 Search permissions...">
                            </div>
                            <div class="col-md-3">
                                <select id="moduleFilter" class="form-select">
                                    <option value="">All Modules</option>
                                    <?php foreach ($modules as $module): ?>
                                        <option value="<?= esc($module) ?>"><?= ucfirst($module) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5 text-end">
                                <span id="permissionCount" class="badge bg-info fs-6">0 permissions selected</span>
                            </div>
                        </div>

                        <!-- Permissions List -->
                        <div id="permissionsList" style="max-height: 600px; overflow-y: auto;">
                            <!-- Will be populated via AJAX -->
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" style="display: none;" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading permissions...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.module-section {
    background: #f8f9fa;
    border-left: 4px solid #0d6efd;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.page-section {
    background: white;
    border: 1px solid #dee2e6;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 5px;
}

.permission-checkbox {
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.permission-checkbox:hover {
    background-color: #e9ecef;
}

.permission-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.permission-action-badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}

.stats-box {
    background: #e7f1ff;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
}
</style>

<script>
const base_url = '<?= base_url() ?>';
let currentRoleId = null;
let allPermissions = {};

$(document).ready(function() {
    // Role selection change
    $('#roleSelect').change(function() {
        const roleId = $(this).val();
        if (roleId) {
            currentRoleId = roleId;
            loadRolePermissions(roleId);
            $('#selectedRoleName').text($('#roleSelect option:selected').text());
            $('#savePermissionsBtn, #selectAllBtn, #deselectAllBtn').prop('disabled', false);
        } else {
            $('#permissionGrid').hide();
            $('#savePermissionsBtn, #selectAllBtn, #deselectAllBtn').prop('disabled', true);
        }
    });

    // Save permissions
    $('#savePermissionsBtn').click(function() {
        saveRolePermissions();
    });

    // Select all
    $('#selectAllBtn').click(function() {
        $('.permission-checkbox input[type="checkbox"]').prop('checked', true);
        updatePermissionCount();
    });

    // Deselect all
    $('#deselectAllBtn').click(function() {
        $('.permission-checkbox input[type="checkbox"]').prop('checked', false);
        updatePermissionCount();
    });

    // Search filter
    $('#searchPermission').on('keyup', function() {
        filterPermissions();
    });

    // Module filter
    $('#moduleFilter').change(function() {
        filterPermissions();
    });

    // Auto-load if role is selected
    <?php if ($selected_role_id): ?>
        $('#roleSelect').trigger('change');
    <?php endif; ?>
});

function loadRolePermissions(roleId) {
    $('#loadingIndicator').show();
    $('#permissionGrid').hide();

    $.ajax({
        url: `${base_url}/permission-management/get-role-permissions/${roleId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allPermissions = response.data;
                renderPermissions(response.data);
                $('#loadingIndicator').hide();
                $('#permissionGrid').show();
                updatePermissionCount();
            } else {
                OptimaNotify.error(response.message || 'Failed to load permissions');
            }
        },
        error: function(xhr) {
            OptimaNotify.error('Failed to load permissions');
            $('#loadingIndicator').hide();
        }
    });
}

function renderPermissions(data) {
    let html = '';

    for (const [module, pages] of Object.entries(data)) {
        const modulePerms = Object.values(pages).flat();
        const grantedCount = modulePerms.filter(p => p.granted).length;

        html += `
            <div class="module-section" data-module="${module}">
                <h4 class="mb-3">
                    <i class="fas fa-folder-open text-primary"></i> 
                    ${ucfirst(module)}
                    <span class="badge bg-info ms-2">${grantedCount}/${modulePerms.length} granted</span>
                </h4>
        `;

        for (const [page, permissions] of Object.entries(pages)) {
            const pageGrantedCount = permissions.filter(p => p.granted).length;

            html += `
                <div class="page-section" data-page="${page}">
                    <h5 class="mb-2">
                        <i class="fas fa-file-alt text-secondary"></i> 
                        ${ucfirst(page.replace(/_/g, ' '))}
                        <span class="badge bg-secondary ms-2">${pageGrantedCount}/${permissions.length}</span>
                    </h5>
                    <div class="row">
            `;

            for (const perm of permissions) {
                const actionBadgeClass = getActionBadgeClass(perm.action);
                html += `
                    <div class="col-md-4 col-lg-3 permission-checkbox">
                        <label class="d-flex align-items-center">
                            <input type="checkbox" 
                                   class="form-check-input me-2" 
                                   value="${perm.id}"
                                   data-permission-key="${perm.key_name}"
                                   ${perm.granted ? 'checked' : ''}
                                   onchange="updatePermissionCount()">
                            <span class="badge ${actionBadgeClass} permission-action-badge">
                                ${perm.action}
                            </span>
                        </label>
                        <small class="text-muted d-block ms-4" title="${perm.description}">
                            ${perm.description.substring(0, 40)}${perm.description.length > 40 ? '...' : ''}
                        </small>
                    </div>
                `;
            }

            html += `
                    </div>
                </div>
            `;
        }

        html += `</div>`;
    }

    $('#permissionsList').html(html);
}

function saveRolePermissions() {
    const permissionIds = [];
    $('.permission-checkbox input[type="checkbox"]:checked').each(function() {
        permissionIds.push($(this).val());
    });

    OptimaConfirm.generic({
        title: 'Save Permissions?',
        text: `Assign ${permissionIds.length} permissions to this role?`,
        icon: 'question',
        confirmText: 'Yes, Save',
        cancelText: window.lang('cancel'),
        confirmButtonColor: 'primary',
        onConfirm: function() {
            $.ajax({
                url: `${base_url}/permission-management/save-role-permissions`,
                type: 'POST',
                dataType: 'json',
                data: {
                    [window.csrfTokenName]: window.csrfTokenValue,
                    role_id: currentRoleId,
                    permission_ids: permissionIds
                },
                success: function(response) {
                    if (response.success) {
                        OptimaNotify.success(response.message, 'Success');
                        loadRolePermissions(currentRoleId); // Reload
                    } else {
                        OptimaNotify.error(response.message);
                    }
                },
                error: function(xhr) {
                    OptimaNotify.error('Failed to save permissions');
                }
            });
        }
    });
}

function filterPermissions() {
    const searchTerm = $('#searchPermission').val().toLowerCase();
    const moduleFilter = $('#moduleFilter').val();

    $('.module-section').each(function() {
        const $module = $(this);
        const moduleName = $module.data('module');
        let hasVisiblePages = false;

        // Module filter
        if (moduleFilter && moduleName !== moduleFilter) {
            $module.hide();
            return;
        }

        $module.find('.page-section').each(function() {
            const $page = $(this);
            let hasVisiblePerms = false;

            $page.find('.permission-checkbox').each(function() {
                const $perm = $(this);
                const permKey = $perm.find('input').data('permission-key');
                const description = $perm.find('small').text().toLowerCase();

                if (searchTerm === '' || permKey.includes(searchTerm) || description.includes(searchTerm)) {
                    $perm.show();
                    hasVisiblePerms = true;
                } else {
                    $perm.hide();
                }
            });

            if (hasVisiblePerms) {
                $page.show();
                hasVisiblePages = true;
            } else {
                $page.hide();
            }
        });

        if (hasVisiblePages) {
            $module.show();
        } else {
            $module.hide();
        }
    });
}

function updatePermissionCount() {
    const total = $('.permission-checkbox input[type="checkbox"]').length;
    const checked = $('.permission-checkbox input[type="checkbox"]:checked').length;
    $('#permissionCount').text(`${checked}/${total} permissions selected`);
}

function getActionBadgeClass(action) {
    const map = {
        'navigation': 'bg-primary',
        'view': 'bg-info',
        'create': 'bg-success',
        'edit': 'bg-warning',
        'delete': 'bg-danger',
        'approve': 'bg-success',
        'reject': 'bg-danger',
        'export': 'bg-secondary',
        'print': 'bg-info',
        'import': 'bg-primary'
    };
    return map[action] || 'bg-secondary';
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>

<?= $this->endSection() ?>
