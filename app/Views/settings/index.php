<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog me-2"></i>System Settings
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="getSystemInfo()">
                    <i class="fas fa-info-circle me-1"></i>System Info
                </button>
                <button class="btn btn-outline-warning btn-sm" onclick="clearCache()">
                    <i class="fas fa-broom me-1"></i>Clear Cache
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="createBackup()">
                    <i class="fas fa-download me-1"></i>Backup
                </button>
                <button class="btn btn-primary btn-sm" onclick="saveSettings()">
                    <i class="fas fa-save me-1"></i>Save Settings
                </button>
            </div>
        </div>
    </div>

    <?= form_open('/settings/update', ['id' => 'settingsForm']) ?>
    
    <!-- Settings Tabs -->
    <div class="card shadow">
        <div class="card-header p-0">
            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                        <i class="fas fa-cog me-2"></i>General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab">
                        <i class="fas fa-building me-2"></i>Company
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="localization-tab" data-bs-toggle="tab" data-bs-target="#localization" type="button" role="tab">
                        <i class="fas fa-globe me-2"></i>Localization
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                        <i class="fas fa-shield-alt me-2"></i>Security
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="backup-tab" data-bs-toggle="tab" data-bs-target="#backup" type="button" role="tab">
                        <i class="fas fa-database me-2"></i>Backup
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="settingsTabContent">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <h5 class="mb-4"><?= lang('App.general_settings') ?></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_name" class="form-label"><?= lang('App.application_name') ?></label>
                                <input type="text" class="form-control" id="app_name" name="app_name" 
                                       value="<?= esc($settings['app_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="items_per_page" class="form-label"><?= lang('App.items_per_page') ?></label>
                                <select class="form-select" id="items_per_page" name="items_per_page">
                                    <option value="10" <?= $settings['items_per_page'] == '10' ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $settings['items_per_page'] == '25' ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $settings['items_per_page'] == '50' ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $settings['items_per_page'] == '100' ? 'selected' : '' ?>>100</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="app_description" class="form-label"><?= lang('App.application_description') ?></label>
                                <textarea class="form-control" id="app_description" name="app_description" rows="3"><?= esc($settings['app_description']) ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="session_timeout" class="form-label"><?= lang('App.session_timeout_minutes') ?></label>
                                <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                       value="<?= esc($settings['session_timeout']) ?>" min="15" max="480">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?= lang('App.system_modes') ?></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                           <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="maintenance_mode">
                                        <?= lang('App.maintenance_mode') ?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="debug_mode" name="debug_mode" 
                                           <?= $settings['debug_mode'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="debug_mode">
                                        <?= lang('App.debug_mode') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Settings -->
                <div class="tab-pane fade" id="company" role="tabpanel">
                    <h5 class="mb-4"><?= lang('App.company_information') ?></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_name" class="form-label"><?= lang('App.company_name') ?></label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?= esc($settings['company_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_email" class="form-label"><?= lang('App.company_email') ?></label>
                                <input type="email" class="form-control" id="company_email" name="company_email" 
                                       value="<?= esc($settings['company_email']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_phone" class="form-label"><?= lang('App.company_phone') ?></label>
                                <input type="tel" class="form-control" id="company_phone" name="company_phone" 
                                       value="<?= esc($settings['company_phone']) ?>">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="company_address" class="form-label"><?= lang('App.company_address') ?></label>
                                <textarea class="form-control" id="company_address" name="company_address" rows="3"><?= esc($settings['company_address']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Localization Settings -->
                <div class="tab-pane fade" id="localization" role="tabpanel">
                    <h5 class="mb-4"><?= lang('App.localization_settings') ?></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="timezone" class="form-label"><?= lang('App.timezone') ?></label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="Asia/Jakarta" <?= $settings['timezone'] == 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar" <?= $settings['timezone'] == 'Asia/Makassar' ? 'selected' : '' ?>>Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura" <?= $settings['timezone'] == 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura (WIT)</option>
                                    <option value="UTC" <?= $settings['timezone'] == 'UTC' ? 'selected' : '' ?>>UTC</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_format" class="form-label"><?= lang('App.date_format') ?></label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="d/m/Y" <?= $settings['date_format'] == 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                                    <option value="m/d/Y" <?= $settings['date_format'] == 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                                    <option value="Y-m-d" <?= $settings['date_format'] == 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                                    <option value="d-m-Y" <?= $settings['date_format'] == 'd-m-Y' ? 'selected' : '' ?>>DD-MM-YYYY</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label"><?= lang('App.currency') ?></label>
                                <select class="form-select" id="currency" name="currency">
                                    <option value="IDR" <?= $settings['currency'] == 'IDR' ? 'selected' : '' ?>>Indonesian Rupiah (IDR)</option>
                                    <option value="USD" <?= $settings['currency'] == 'USD' ? 'selected' : '' ?>>US Dollar (USD)</option>
                                    <option value="EUR" <?= $settings['currency'] == 'EUR' ? 'selected' : '' ?>>Euro (EUR)</option>
                                    <option value="SGD" <?= $settings['currency'] == 'SGD' ? 'selected' : '' ?>>Singapore Dollar (SGD)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="language" class="form-label"><?= lang('App.language') ?></label>
                                <select class="form-select" id="language" name="language">
                                    <option value="id" <?= $settings['language'] == 'id' ? 'selected' : '' ?>>Bahasa Indonesia</option>
                                    <option value="en" <?= $settings['language'] == 'en' ? 'selected' : '' ?>>English</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <h5 class="mb-4"><?= lang('App.security_settings') ?></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_login_attempts" class="form-label"><?= lang('App.max_login_attempts') ?></label>
                                <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                       value="<?= esc($settings['max_login_attempts']) ?>" min="3" max="10">
                                <div class="form-text"><?= lang('App.failed_login_before_lockout') ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_expiry_days" class="form-label"><?= lang('App.password_expiry_days') ?></label>
                                <input type="number" class="form-control" id="password_expiry_days" name="password_expiry_days" 
                                       value="<?= esc($settings['password_expiry_days']) ?>" min="30" max="365">
                                <div class="form-text"><?= lang('App.days_before_password_expires') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <h5 class="mb-4"><?= lang('App.notification_settings') ?></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?= lang('App.email_notifications') ?></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                                           <?= $settings['email_notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_notifications">
                                        <?= lang('App.enable_email_notifications') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?= lang('App.sms_notifications') ?></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" 
                                           <?= $settings['sms_notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="sms_notifications">
                                        <?= lang('App.enable_sms_notifications') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Test Email Configuration</h6>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="test_email" placeholder="Enter email address">
                                        <button class="btn btn-outline-primary" type="button" onclick="testEmail()">
                                            <i class="fas fa-paper-plane me-1"></i>Send Test Email
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Backup Settings -->
                <div class="tab-pane fade" id="backup" role="tabpanel">
                    <h5 class="mb-4">Backup Settings</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Auto Backup</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="auto_backup" name="auto_backup" 
                                           <?= $settings['auto_backup'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="auto_backup">
                                        Enable Automatic Backup
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="backup_frequency" class="form-label">Backup Frequency</label>
                                <select class="form-select" id="backup_frequency" name="backup_frequency">
                                    <option value="daily" <?= $settings['backup_frequency'] == 'daily' ? 'selected' : '' ?>>Daily</option>
                                    <option value="weekly" <?= $settings['backup_frequency'] == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                    <option value="monthly" <?= $settings['backup_frequency'] == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Manual Backup</h6>
                                    <p class="card-text">Create a manual backup of the database now.</p>
                                    <button type="button" class="btn btn-success" onclick="createBackup()">
                                        <i class="fas fa-download me-2"></i>Create Backup Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?= form_close() ?>
</div>

<!-- System Info Modal -->
<div class="modal fade" id="systemInfoModal" tabindex="-1" aria-labelledby="systemInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="systemInfoModalLabel"><?= lang('App.system_information') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="systemInfoBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function saveSettings() {
    $('#settingsForm').submit();
}

function testEmail() {
    const email = $('#test_email').val();
    if (!email) {
        showNotification('Please enter an email address', 'error');
        return;
    }

    showNotification('Sending test email...', 'info');

    fetch('/settings/test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'test_email=' + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');
    })
    .catch(error => {
        showNotification('Failed to send test email', 'error');
    });
}

function createBackup() {
    showNotification('Creating backup...', 'info');

    fetch('/settings/backup', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');
    })
    .catch(error => {
        showNotification('Failed to create backup', 'error');
    });
}

function clearCache() {
    if (!confirm('Are you sure you want to clear the cache?')) {
        return;
    }

    showNotification('Clearing cache...', 'info');

    fetch('/settings/clear-cache', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');
    })
    .catch(error => {
        showNotification('Failed to clear cache', 'error');
    });
}

function getSystemInfo() {
    const modal = new bootstrap.Modal(document.getElementById('systemInfoModal'));
    modal.show();

    fetch('/settings/system-info', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const info = data.info;
            const html = `
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tr><th>PHP Version</th><td>${info.php_version}</td></tr>
                        <tr><th>CodeIgniter Version</th><td>${info.codeigniter_version}</td></tr>
                        <tr><th>Server Software</th><td>${info.server_software}</td></tr>
                        <tr><th>Database Version</th><td>${info.database_version}</td></tr>
                        <tr><th>Memory Limit</th><td>${info.memory_limit}</td></tr>
                        <tr><th>Max Execution Time</th><td>${info.max_execution_time} seconds</td></tr>
                        <tr><th>Upload Max Filesize</th><td>${info.upload_max_filesize}</td></tr>
                        <tr><th>Post Max Size</th><td>${info.post_max_size}</td></tr>
                        <tr><th>Available Disk Space</th><td>${info.disk_space}</td></tr>
                        <tr><th>Last Backup</th><td>${info.last_backup}</td></tr>
                    </table>
                </div>
            `;
            document.getElementById('systemInfoBody').innerHTML = html;
        } else {
            document.getElementById('systemInfoBody').innerHTML = '<div class="alert alert-danger">Failed to load system information</div>';
        }
    })
    .catch(error => {
        document.getElementById('systemInfoBody').innerHTML = '<div class="alert alert-danger">Failed to load system information</div>';
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 1060; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}
</script>

<?= $this->endSection() ?> 