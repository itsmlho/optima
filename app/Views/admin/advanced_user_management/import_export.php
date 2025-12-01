<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Import/Export Users</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/advanced-users') ?>">User Management</a></li>
                    <li class="breadcrumb-item active">Import/Export</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('admin/advanced-users') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Export Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-download me-2"></i>Export Users</h5>
                </div>
                <div class="card-body">
                    <form id="exportForm">
                        <div class="mb-3">
                            <label class="form-label">Export Format</label>
                            <select class="form-select" name="format" required>
                                <option value="csv">CSV (Comma Separated Values)</option>
                                <option value="xlsx">Excel (XLSX)</option>
                                <option value="json">JSON (JavaScript Object Notation)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Filter by Division</label>
                            <select class="form-select" name="division_id">
                                <option value="">All Divisions</option>
                                <?php foreach ($divisions as $division): ?>
                                <option value="<?= $division['id'] ?>"><?= esc($division['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Filter by Role</label>
                            <select class="form-select" name="role_id">
                                <option value="">All Roles</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Filter by Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Include Data</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_roles" checked>
                                <label class="form-check-label">User Roles</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_divisions" checked>
                                <label class="form-check-label">Division Assignments</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_permissions">
                                <label class="form-check-label">Custom Permissions</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_positions">
                                <label class="form-check-label">Position Assignments</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-download"></i> Export Users
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Import Users</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Import Requirements:</strong>
                        <ul class="mb-0 mt-2">
                            <li>File must be CSV or Excel format</li>
                            <li>Required columns: first_name, last_name, email, username</li>
                            <li>Optional columns: phone, status, roles, divisions</li>
                            <li>Maximum file size: 10MB</li>
                        </ul>
                    </div>

                    <form id="importForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Select File</label>
                            <input type="file" class="form-control" name="import_file" accept=".csv,.xlsx,.xls" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Import Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="update_existing" id="updateExisting">
                                <label class="form-check-label" for="updateExisting">
                                    Update existing users (match by email)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="send_welcome_email" id="sendWelcome">
                                <label class="form-check-label" for="sendWelcome">
                                    Send welcome email to new users
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="auto_generate_password" id="autoPassword" checked>
                                <label class="form-check-label" for="autoPassword">
                                    Auto-generate passwords for new users
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default Role for New Users</label>
                            <select class="form-select" name="default_role_id">
                                <option value="">No default role</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-upload"></i> Import Users
                        </button>
                    </form>

                    <div id="importProgress" class="mt-3" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="mt-2" id="importStatus">Processing...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Download -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-download me-2"></i>Download Templates</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Download pre-formatted templates to ensure your import file has the correct structure.</p>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-csv fa-2x text-success mb-2"></i>
                                    <h6>Basic User Template</h6>
                                    <p class="small text-muted">Basic user information only</p>
                                    <a href="<?= base_url('admin/advanced-users/template/basic') ?>" class="btn btn-sm btn-outline-success">
                                        Download CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-excel fa-2x text-primary mb-2"></i>
                                    <h6>Complete Template</h6>
                                    <p class="small text-muted">Includes roles, divisions, permissions</p>
                                    <a href="<?= base_url('admin/advanced-users/template/complete') ?>" class="btn btn-sm btn-outline-primary">
                                        Download Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-code fa-2x text-info mb-2"></i>
                                    <h6>JSON Template</h6>
                                    <p class="small text-muted">For API integration</p>
                                    <a href="<?= base_url('admin/advanced-users/template/json') ?>" class="btn btn-sm btn-outline-info">
                                        Download JSON
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Import/Export History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>File</th>
                                    <th>Records</th>
                                    <th>Status</th>
                                    <th>User</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($importHistory)): ?>
                                    <?php foreach ($importHistory as $history): ?>
                                    <tr>
                                        <td><?= date('M d, Y H:i', strtotime($history['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $history['type'] == 'import' ? 'success' : 'primary' ?>">
                                                <?= ucfirst($history['type']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($history['filename']) ?></td>
                                        <td><?= $history['total_records'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $history['status'] == 'completed' ? 'success' : ($history['status'] == 'failed' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($history['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($history['user_name']) ?></td>
                                        <td>
                                            <?php if ($history['log_file']): ?>
                                            <a href="<?= base_url('admin/advanced-users/download-log/' . $history['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-download"></i> Log
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            No import/export history found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Export form handler
    $('#exportForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        // Add selected checkboxes
        $('input[type="checkbox"]:checked', this).each(function() {
            params.append(this.name, '1');
        });
        
        window.location.href = '<?= base_url('admin/advanced-users/export') ?>?' + params.toString();
    });

    // Import form handler
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        $('#importProgress').show();
        $('.progress-bar').css('width', '10%');
        $('#importStatus').text('Uploading file...');
        
        $.ajax({
            url: '<?= base_url('admin/advanced-users/import') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = (evt.loaded / evt.total) * 50; // 50% for upload
                        $('.progress-bar').css('width', percentComplete + '%');
                    }
                });
                return xhr;
            },
            success: function(response) {
                $('.progress-bar').css('width', '100%');
                $('#importStatus').text('Import completed!');
                
                if (response.success) {
                    alert('Import completed successfully!\n\n' +
                          'Created: ' + (response.created || 0) + ' users\n' +
                          'Updated: ' + (response.updated || 0) + ' users\n' +
                          'Errors: ' + (response.errors || 0) + ' records');
                    
                    setTimeout(function() {
                        window.location.href = '<?= base_url('admin/advanced-users') ?>';
                    }, 2000);
                } else {
                    alert('Import failed: ' + response.message);
                    $('#importProgress').hide();
                }
            },
            error: function(xhr) {
                $('#importProgress').hide();
                try {
                    var response = JSON.parse(xhr.responseText);
                    alert('Error: ' + response.message);
                } catch (e) {
                    alert('An error occurred during import.');
                }
            }
        });
    });

    // File input validation
    $('input[name="import_file"]').on('change', function() {
        var file = this.files[0];
        if (file) {
            var fileSize = file.size / 1024 / 1024; // MB
            if (fileSize > 10) {
                alert('File size must be less than 10MB');
                $(this).val('');
                return;
            }
            
            var fileName = file.name.toLowerCase();
            var allowedExtensions = ['.csv', '.xlsx', '.xls'];
            var isValidFile = allowedExtensions.some(ext => fileName.endsWith(ext));
            
            if (!isValidFile) {
                alert('Please select a CSV or Excel file');
                $(this).val('');
                return;
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
