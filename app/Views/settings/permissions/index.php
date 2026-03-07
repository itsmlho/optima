<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-key"></i> <?= esc($title) ?>
                        </h3>
                        <div class="btn-group">
                            <a href="<?= base_url('permission-management/role-permissions') ?>" class="btn btn-sm btn-light">
                                <i class="fas fa-users-cog"></i> Role Permissions
                            </a>
                            <a href="<?= base_url('permission-management/user-permissions') ?>" class="btn btn-sm btn-light">
                                <i class="fas fa-user-shield"></i> User Permissions
                            </a>
                            <a href="<?= base_url('permission-management/audit-trail') ?>" class="btn btn-sm btn-light">
                                <i class="fas fa-history"></i> Audit Trail
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-key fa-3x text-primary mb-2"></i>
                                    <h4 class="mb-0" id="totalPermissions">-</h4>
                                    <p class="text-muted mb-0">Total Permissions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-layer-group fa-3x text-success mb-2"></i>
                                    <h4 class="mb-0" id="totalModules">-</h4>
                                    <p class="text-muted mb-0">Modules</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-3x text-info mb-2"></i>
                                    <h4 class="mb-0" id="totalPages">-</h4>
                                    <p class="text-muted mb-0">Pages</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-bolt fa-3x text-warning mb-2"></i>
                                    <h4 class="mb-0" id="totalActions">-</h4>
                                    <p class="text-muted mb-0">Action Types</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" id="searchPermissions" class="form-control" placeholder="🔍 Search permissions...">
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
                            <select id="actionFilter" class="form-select">
                                <option value="">All Actions</option>
                                <option value="navigation">Navigation</option>
                                <option value="view">View</option>
                                <option value="create">Create</option>
                                <option value="edit">Edit</option>
                                <option value="delete">Delete</option>
                                <option value="approve">Approve</option>
                                <option value="export">Export</option>
                                <option value="print">Print</option>
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="permissionsTable" class="table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Module</th>
                                    <th>Page</th>
                                    <th>Action</th>
                                    <th>Permission Key</th>
                                    <th>Display Name</th>
                                    <th>Roles Using</th>
                                    <th>Users Using</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.action-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.action-navigation { background: #0d6efd; color: white; }
.action-view { background: #17a2b8; color: white; }
.action-create { background: #28a745; color: white; }
.action-edit { background: #ffc107; color: black; }
.action-delete { background: #dc3545; color: white; }
.action-approve { background: #6f42c1; color: white; }
.action-export { background: #6c757d; color: white; }
.action-print { background: #20c997; color: white; }
.action-other { background: #e9ecef; color: #495057; }

.module-badge {
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    background: #f8f9fa;
    color: #495057;
}
</style>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let permissionsTable;

$(document).ready(function() {
    // Initialize DataTable
    permissionsTable = $('#permissionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('permission-management/get-permissions') ?>',
            type: 'POST',
            data: function(d) {
                d[window.csrfTokenName] = window.csrfTokenValue;
                d.module = $('#moduleFilter').val();
                d.action = $('#actionFilter').val();
            }
        },
        columns: [
            { data: 'id', width: '50px' },
            { 
                data: 'module',
                render: function(data) {
                    return `<span class="module-badge">${data}</span>`;
                }
            },
            { data: 'page' },
            { 
                data: 'action',
                render: function(data) {
                    return `<span class="action-badge action-${data}">${data}</span>`;
                }
            },
            { 
                data: 'key_name',
                render: function(data) {
                    return `<code>${data}</code>`;
                }
            },
            { data: 'display_name' },
            { 
                data: 'role_count',
                className: 'text-center',
                render: function(data) {
                    if (data > 0) {
                        return `<span class="badge bg-success">${data} roles</span>`;
                    }
                    return `<span class="badge bg-secondary">0</span>`;
                }
            },
            { 
                data: 'user_count',
                className: 'text-center',
                render: function(data) {
                    if (data > 0) {
                        return `<span class="badge bg-info">${data} users</span>`;
                    }
                    return `<span class="badge bg-secondary">0</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    if (data) {
                        return new Date(data).toLocaleDateString('id-ID');
                    }
                    return '-';
                }
            }
        ],
        order: [[1, 'asc'], [2, 'asc'], [3, 'asc']],
        pageLength: 50,
        language: {
            processing: "Loading...",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ permissions",
            infoEmpty: "Showing 0 to 0 of 0 permissions",
            infoFiltered: "(filtered from _MAX_ total permissions)",
            zeroRecords: "No matching permissions found",
            emptyTable: "No permissions available",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        drawCallback: function(settings) {
            const api = this.api();
            const data = api.ajax.json();
            
            if (data) {
                $('#totalPermissions').text(data.recordsTotal || 0);
                
                // Calculate unique modules and pages
                const uniqueModules = new Set();
                const uniquePages = new Set();
                const uniqueActions = new Set();
                
                api.rows({ page: 'current' }).data().each(function(row) {
                    uniqueModules.add(row.module);
                    uniquePages.add(row.page);
                    uniqueActions.add(row.action);
                });
                
                $('#totalModules').text(uniqueModules.size);
                $('#totalPages').text(uniquePages.size);
                $('#totalActions').text(uniqueActions.size);
            }
        }
    });

    // Search input
    $('#searchPermissions').on('keyup', function() {
        permissionsTable.search(this.value).draw();
    });

    // Module filter
    $('#moduleFilter').on('change', function() {
        permissionsTable.ajax.reload();
    });

    $('#actionFilter').on('change', function() {
        permissionsTable.ajax.reload();
    });
});

function clearFilters() {
    $('#searchPermissions').val('');
    $('#moduleFilter').val('');
    $('#actionFilter').val('');
    permissionsTable.search('').ajax.reload();
}
</script>
<?= $this->endSection() ?>
