<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-shield"></i> <?= esc($title) ?>
                    </h3>
                </div>
                <div class="card-body">
                    <!-- User Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="userSelect" class="form-label fw-bold">Select User:</label>
                            <select id="userSelect" class="form-select">
                                <option value="">-- Select User --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" 
                                            data-role="<?= esc($user['role_name'] ?? 'No Role') ?>"
                                            <?= ($selected_user_id == $user['id'] ? 'selected' : '') ?>>
                                        <?= esc($user['username']) ?> 
                                        (<?= esc($user['email']) ?>) 
                                        - <?= esc($user['role_name'] ?? 'No Role') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" id="quickGrantBtn" class="btn btn-success me-2" disabled>
                                <i class="fas fa-plus-circle"></i> Quick Grant
                            </button>
                            <button type="button" id="clearAllOverridesBtn" class="btn btn-outline-danger" disabled>
                                <i class="fas fa-eraser"></i> Clear All Overrides
                            </button>
                        </div>
                    </div>

                    <!-- User Info Banner -->
                    <div id="userInfoBanner" style="display: none;" class="alert alert-info">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="mb-2">
                                    <i class="fas fa-user"></i> <span id="selectedUserName"></span>
                                    <span class="badge bg-primary ms-2" id="selectedUserRole"></span>
                                </h5>
                                <p class="mb-0">
                                    <strong>Permission Priority:</strong> 
                                    <span class="badge bg-danger">1. User DENY</span> → 
                                    <span class="badge bg-success">2. User GRANT</span> → 
                                    <span class="badge bg-info">3. Role Permission</span> → 
                                    <span class="badge bg-secondary">4. Default DENY</span>
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="stats-box">
                                    <div><strong>Role Permissions:</strong> <span id="rolePermCount" class="badge bg-info">0</span></div>
                                    <div><strong>User Overrides:</strong> <span id="userOverrideCount" class="badge bg-warning">0</span></div>
                                    <div><strong>Effective Total:</strong> <span id="effectiveCount" class="badge bg-success">0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Module Filter -->
                    <div id="filterSection" style="display: none;" class="row mb-3">
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
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="granted">✅ Granted (User Override)</option>
                                <option value="denied">❌ Denied (User Override)</option>
                                <option value="role">📋 From Role</option>
                                <option value="none">⚪ No Access</option>
                            </select>
                        </div>
                    </div>

                    <!-- Permissions List -->
                    <div id="permissionsList" style="display: none; max-height: 600px; overflow-y: auto;">
                        <!-- Will be populated via AJAX -->
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" style="display: none;" class="text-center py-5">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading user permissions...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Grant Modal -->
<div class="modal fade" id="quickGrantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Quick Grant Permissions</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Permissions to Grant:</label>
                    <select id="quickGrantSelect" class="form-select" multiple size="10">
                        <!-- Populated dynamically -->
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reason (Optional):</label>
                    <textarea id="quickGrantReason" class="form-control" rows="2" placeholder="Why granting these permissions?"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Expiration Date (Optional - for temporary access):</label>
                    <input type="datetime-local" id="quickGrantExpires" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmQuickGrant">
                    <i class="fas fa-check"></i> Grant Permissions
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
.permission-row {
    border-bottom: 1px solid #dee2e6;
    padding: 12px;
    transition: background-color 0.2s;
}

.permission-row:hover {
    background-color: #f8f9fa;
}

.permission-status-icon {
    width: 30px;
    text-align: center;
    font-size: 1.2rem;
}

.permission-controls {
    display: flex;
    gap: 5px;
}

.override-indicator {
    font-size: 0.75rem;
    padding: 2px 6px;
}

.module-section {
    background: #f8f9fa;
    border-left: 4px solid #28a745;
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
</style>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const base_url = '<?= base_url() ?>';
let currentUserId = null;
let allUserPermissions = {};

$(document).ready(function() {
    // User selection change
    $('#userSelect').change(function() {
        const userId = $(this).val();
        if (userId) {
            currentUserId = userId;
            const selectedOption = $(this).find('option:selected');
            const userName = selectedOption.text().split('(')[0].trim();
            const userRole = selectedOption.data('role');

            $('#selectedUserName').text(userName);
            $('#selectedUserRole').text(userRole);

            loadUserPermissions(userId);
            $('#quickGrantBtn, #clearAllOverridesBtn').prop('disabled', false);
        } else {
            $('#userInfoBanner, #filterSection, #permissionsList').hide();
            $('#quickGrantBtn, #clearAllOverridesBtn').prop('disabled', true);
        }
    });

    // Quick Grant button
    $('#quickGrantBtn').click(function() {
        showQuickGrantModal();
    });

    // Clear all overrides
    $('#clearAllOverridesBtn').click(function() {
        clearAllOverrides();
    });

    // Search/filter
    $('#searchPermission, #moduleFilter, #statusFilter').on('change keyup', function() {
        filterPermissions();
    });

    // Confirm quick grant
    $('#confirmQuickGrant').click(function() {
        executeQuickGrant();
    });

    // Auto-load if user is selected
    <?php if ($selected_user_id): ?>
        $('#userSelect').trigger('change');
    <?php endif; ?>
});

function loadUserPermissions(userId) {
    $('#loadingIndicator').show();
    $('#userInfoBanner, #filterSection, #permissionsList').hide();

    $.ajax({
        url: `${base_url}/permission-management/get-user-permissions/${userId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allUserPermissions = response.data;
                renderUserPermissions(response.data);
                updateStats(response.data);
                $('#loadingIndicator').hide();
                $('#userInfoBanner, #filterSection, #permissionsList').show();
            } else {
                OptimaNotify.error(response.message || 'Failed to load permissions');
                $('#loadingIndicator').hide();
            }
        },
        error: function(xhr) {
            OptimaNotify.error('Failed to load user permissions');
            $('#loadingIndicator').hide();
        }
    });
}

function renderUserPermissions(data) {
    let html = '';

    for (const [module, pages] of Object.entries(data)) {
        html += `<div class="module-section" data-module="${module}">
            <h4 class="mb-3"><i class="fas fa-folder-open"></i> ${ucfirst(module)}</h4>`;

        for (const [page, permissions] of Object.entries(pages)) {
            html += `<div class="page-section" data-page="${page}">
                <h5 class="mb-2"><i class="fas fa-file-alt"></i> ${ucfirst(page.replace(/_/g, ' '))}</h5>`;

            for (const perm of permissions) {
                const statusInfo = getPermissionStatusInfo(perm);
                html += `
                    <div class="permission-row" data-status="${statusInfo.status}" data-permission-id="${perm.id}">
                        <div class="row align-items-center">
                            <div class="col-md-1 permission-status-icon">
                                ${statusInfo.icon}
                            </div>
                            <div class="col-md-5">
                                <strong>${perm.action}</strong>
                                <span class="badge bg-secondary ms-2">${perm.key_name}</span>
                                ${statusInfo.badge}
                                <br><small class="text-muted">${perm.description}</small>
                            </div>
                            <div class="col-md-3">
                                ${getSourceInfo(perm)}
                            </div>
                            <div class="col-md-3 text-end permission-controls">
                                ${getPermissionControls(perm)}
                            </div>
                        </div>
                    </div>
                `;
            }

            html += `</div>`;
        }

        html += `</div>`;
    }

    $('#permissionsList').html(html);
}

function getPermissionStatusInfo(perm) {
    if (perm.user_override) {
        if (perm.user_override.granted == 1) {
            return {
                status: 'granted',
                icon: '<i class="fas fa-check-circle text-success"></i>',
                badge: '<span class="override-indicator badge bg-success">USER GRANT</span>'
            };
        } else {
            return {
                status: 'denied',
                icon: '<i class="fas fa-times-circle text-danger"></i>',
                badge: '<span class="override-indicator badge bg-danger">USER DENY</span>'
            };
        }
    } else if (perm.has_role_permission) {
        return {
            status: 'role',
            icon: '<i class="fas fa-shield-alt text-info"></i>',
            badge: '<span class="override-indicator badge bg-info">FROM ROLE</span>'
        };
    } else {
        return {
            status: 'none',
            icon: '<i class="fas fa-ban text-secondary"></i>',
            badge: '<span class="override-indicator badge bg-secondary">NO ACCESS</span>'
        };
    }
}

function getSourceInfo(perm) {
    if (perm.user_override) {
        const expires = perm.user_override.expires_at ? 
            `<br><small class="text-warning">⏰ Expires: ${perm.user_override.expires_at}</small>` : '';
        const reason = perm.user_override.reason ? 
            `<br><small class="text-muted">📝 ${perm.user_override.reason}</small>` : '';
        return `<small>User Override${expires}${reason}</small>`;
    } else if (perm.has_role_permission) {
        return '<small class="text-info">Inherited from role</small>';
    } else {
        return '<small class="text-muted">No permission</small>';
    }
}

function getPermissionControls(perm) {
    let html = '';

    if (perm.user_override) {
        // Has override - show remove button
        html += `<button class="btn btn-sm btn-outline-secondary" onclick="removeOverride(${perm.id})" title="Remove Override">
            <i class="fas fa-eraser"></i> Clear
        </button>`;
    } else {
        // No override - show grant/deny buttons
        html += `<button class="btn btn-sm btn-outline-success" onclick="grantPermission(${perm.id})" title="Grant Permission">
            <i class="fas fa-check"></i> Grant
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="denyPermission(${perm.id})" title="Deny Permission">
            <i class="fas fa-times"></i> Deny
        </button>`;
    }

    return html;
}

function grantPermission(permissionId, reason = '', expiresAt = null) {
    OptimaConfirm.generic({
        title: 'Grant Permission?',
        icon: 'question',
        confirmText: 'Grant',
        cancelText: (typeof window.lang === 'function' ? window.lang('cancel') : 'Cancel'),
        confirmButtonColor: '#28a745',
        html: `
            <div class="text-start">
                <label class="form-label">Reason (optional)</label>
                <textarea id="optimaGrantPermissionReason" class="form-control" rows="3" placeholder="Reason for granting (optional)">${reason ? String(reason).replace(/</g,'&lt;') : ''}</textarea>
            </div>
        `,
        onConfirm: function() {
            var el = document.getElementById('optimaGrantPermissionReason');
            var val = el ? (el.value || '').trim() : '';
            updateUserPermission(permissionId, 1, val, expiresAt);
        }
    });
}

function denyPermission(permissionId) {
    OptimaConfirm.danger({
        title: 'Deny Permission?',
        icon: 'warning',
        text: 'This will explicitly deny access even if role has permission',
        confirmText: 'Deny',
        cancelText: (typeof window.lang === 'function' ? window.lang('cancel') : 'Cancel'),
        confirmButtonColor: '#dc3545',
        html: `
            <div class="text-start">
                <label class="form-label">Reason (required)</label>
                <textarea id="optimaDenyPermissionReason" class="form-control" rows="3" placeholder="Reason for denial (required)"></textarea>
            </div>
        `,
        onConfirm: function() {
            var el = document.getElementById('optimaDenyPermissionReason');
            var val = el ? (el.value || '').trim() : '';
            if (!val) {
                OptimaNotify.warning('Reason is required for denial', 'Validasi');
                return;
            }
            updateUserPermission(permissionId, 0, val);
        }
    });
}

function removeOverride(permissionId) {
    OptimaConfirm.generic({
        title: 'Remove Override?',
        text: 'User will fallback to role permissions',
        icon: 'question',
        confirmText: 'Remove',
        cancelText: window.lang('cancel'),
        confirmButtonColor: 'secondary',
        onConfirm: function() {
            $.ajax({
                url: `${base_url}/permission-management/revoke-user-permission`,
                type: 'POST',
                dataType: 'json',
                data: {
                    [window.csrfTokenName]: window.csrfTokenValue,
                    user_id: currentUserId,
                    permission_id: permissionId
                },
                success: function(response) {
                    if (response.success) {
                        OptimaNotify.success(response.message);
                        loadUserPermissions(currentUserId);
                    } else {
                        OptimaNotify.error(response.message);
                    }
                }
            });
        }
    });
}

function updateUserPermission(permissionId, granted, reason, expiresAt = null) {
    $.ajax({
        url: `${base_url}/permission-management/grant-user-permission`,
        type: 'POST',
        dataType: 'json',
        data: {
            [window.csrfTokenName]: window.csrfTokenValue,
            user_id: currentUserId,
            permission_id: permissionId,
            granted: granted,
            reason: reason,
            expires_at: expiresAt
        },
        success: function(response) {
            if (response.success) {
                OptimaNotify.success(response.message);
                loadUserPermissions(currentUserId);
            } else {
                OptimaNotify.error(response.message);
            }
        },
        error: function(xhr) {
            OptimaNotify.error('Failed to update permission');
        }
    });
}

function showQuickGrantModal() {
    // Populate select with permissions that user doesn't have
    let options = '';
    for (const [module, pages] of Object.entries(allUserPermissions)) {
        for (const [page, permissions] of Object.entries(pages)) {
            for (const perm of permissions) {
                if (!perm.effective_permission) {
                    options += `<option value="${perm.id}">${perm.key_name} - ${perm.description}</option>`;
                }
            }
        }
    }
    $('#quickGrantSelect').html(options);
    $('#quickGrantModal').modal('show');
}

function executeQuickGrant() {
    const selectedPerms = $('#quickGrantSelect').val();
    const reason = $('#quickGrantReason').val();
    const expires = $('#quickGrantExpires').val();

    if (!selectedPerms || selectedPerms.length === 0) {
        OptimaNotify.error('Please select at least one permission');
        return;
    }

    const permissions = selectedPerms.map(id => ({
        permission_id: id,
        granted: 1,
        reason: reason
    }));

    $.ajax({
        url: `${base_url}/permission-management/bulk-update-user-permissions`,
        type: 'POST',
        dataType: 'json',
        data: {
            [window.csrfTokenName]: window.csrfTokenValue,
            user_id: currentUserId,
            permissions: permissions
        },
        success: function(response) {
            if (response.success) {
                $('#quickGrantModal').modal('hide');
                OptimaNotify.success(response.message);
                loadUserPermissions(currentUserId);
            } else {
                OptimaNotify.error(response.message);
            }
        }
    });
}

function clearAllOverrides() {
    OptimaConfirm.danger({
        title: 'Clear All Overrides?',
        text: 'This will remove all user-specific permissions. User will only have role permissions.',
        confirmText: 'Yes, Clear All',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            // Get all permission IDs with overrides
            const overrideIds = [];
            for (const [module, pages] of Object.entries(allUserPermissions)) {
                for (const [page, permissions] of Object.entries(pages)) {
                    for (const perm of permissions) {
                        if (perm.user_override) {
                            overrideIds.push(perm.id);
                        }
                    }
                }
            }

            // Remove each override
            let promises = overrideIds.map(id => {
                return $.ajax({
                    url: `${base_url}/permission-management/revoke-user-permission`,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        [window.csrfTokenName]: window.csrfTokenValue,
                        user_id: currentUserId,
                        permission_id: id
                    }
                });
            });

            Promise.all(promises).then(() => {
                OptimaNotify.success(`${overrideIds.length} overrides cleared`);
                loadUserPermissions(currentUserId);
            });
        }
    });
}

function filterPermissions() {
    const searchTerm = $('#searchPermission').val().toLowerCase();
    const moduleFilter = $('#moduleFilter').val();
    const statusFilter = $('#statusFilter').val();

    $('.module-section').each(function() {
        const $module = $(this);
        const moduleName = $module.data('module');
        let hasVisiblePages = false;

        if (moduleFilter && moduleName !== moduleFilter) {
            $module.hide();
            return;
        }

        $module.find('.page-section').each(function() {
            const $page = $(this);
            let hasVisiblePerms = false;

            $page.find('.permission-row').each(function() {
                const $row = $(this);
                const text = $row.text().toLowerCase();
                const status = $row.data('status');

                let show = true;

                // Search filter
                if (searchTerm && !text.includes(searchTerm)) {
                    show = false;
                }

                // Status filter
                if (statusFilter && status !== statusFilter) {
                    show = false;
                }

                if (show) {
                    $row.show();
                    hasVisiblePerms = true;
                } else {
                    $row.hide();
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

function updateStats(data) {
    let rolePermCount = 0;
    let userOverrideCount = 0;
    let effectiveCount = 0;

    for (const [module, pages] of Object.entries(data)) {
        for (const [page, permissions] of Object.entries(pages)) {
            for (const perm of permissions) {
                if (perm.has_role_permission) rolePermCount++;
                if (perm.user_override) userOverrideCount++;
                if (perm.effective_permission) effectiveCount++;
            }
        }
    }

    $('#rolePermCount').text(rolePermCount);
    $('#userOverrideCount').text(userOverrideCount);
    $('#effectiveCount').text(effectiveCount);
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>

<?= $this->endSection() ?>
