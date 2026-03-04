<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-cogs me-2 text-primary"></i><?= esc($page_title) ?>
            </h4>
            <p class="text-muted mb-0 small">Manage system settings and configurations</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm" onclick="saveAllSettings()">
                <i class="fas fa-save me-2"></i>Save All Changes
            </button>
        </div>
    </div>
</div>


    <!-- Settings Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i>General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cache-tab" data-bs-toggle="tab" data-bs-target="#cache" type="button" role="tab">
                                <i class="fas fa-database me-2"></i>Cache
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button" role="tab">
                                <i class="fas fa-tachometer-alt me-2"></i>Performance
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="queue-tab" data-bs-toggle="tab" data-bs-target="#queue" type="button" role="tab">
                                <i class="fas fa-tasks me-2"></i>Queue
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Security
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="settingsTabContent">
                        
                        <!-- General Settings Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <form id="generalSettingsForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Company Information</h5>
                                        
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" 
                                                   value="<?= esc($settings['company_name'] ?? '') ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="company_address" class="form-label">Company Address</label>
                                            <textarea class="form-control" id="company_address" name="company_address" rows="3"><?= esc($settings['company_address'] ?? '') ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="company_phone" class="form-label">Company Phone</label>
                                            <input type="text" class="form-control" id="company_phone" name="company_phone" 
                                                   value="<?= esc($settings['company_phone'] ?? '') ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="company_email" class="form-label">Company Email</label>
                                            <input type="email" class="form-control" id="company_email" name="company_email" 
                                                   value="<?= esc($settings['company_email'] ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-3">System Preferences</h5>
                                        
                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Timezone</label>
                                            <select class="form-select" id="timezone" name="timezone">
                                                <option value="Asia/Jakarta" <?= ($settings['timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta</option>
                                                <option value="Asia/Makassar" <?= ($settings['timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' ?>>Asia/Makassar</option>
                                                <option value="Asia/Jayapura" <?= ($settings['timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="date_format" class="form-label">Date Format</label>
                                            <select class="form-select" id="date_format" name="date_format">
                                                <option value="d/m/Y" <?= ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                                                <option value="m/d/Y" <?= ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                                                <option value="Y-m-d" <?= ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Currency</label>
                                            <select class="form-select" id="currency" name="currency">
                                                <option value="IDR" <?= ($settings['currency'] ?? '') === 'IDR' ? 'selected' : '' ?>>Indonesian Rupiah (IDR)</option>
                                                <option value="USD" <?= ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>US Dollar (USD)</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="language" class="form-label">Language</label>
                                            <select class="form-select" id="language" name="language">
                                                <option value="id" <?= ($settings['language'] ?? '') === 'id' ? 'selected' : '' ?>>Bahasa Indonesia</option>
                                                <option value="en" <?= ($settings['language'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                                       <?= !empty($settings['maintenance_mode']) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="maintenance_mode">
                                                    Maintenance Mode
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Cache Settings Tab -->
                        <div class="tab-pane fade" id="cache" role="tabpanel">
                            <form id="cacheSettingsForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Cache Configuration</h5>
                                        
                                        <div class="mb-3">
                                            <label for="cache_driver" class="form-label">Cache Driver</label>
                                            <select class="form-select" id="cache_driver" name="cache_driver">
                                                <option value="file" <?= ($cache_config['driver'] ?? '') === 'file' ? 'selected' : '' ?>>File</option>
                                                <option value="redis" <?= ($cache_config['driver'] ?? '') === 'redis' ? 'selected' : '' ?>>Redis</option>
                                                <option value="memcached" <?= ($cache_config['driver'] ?? '') === 'memcached' ? 'selected' : '' ?>>Memcached</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="cache_ttl" class="form-label">Default TTL (seconds)</label>
                                            <input type="number" class="form-control" id="cache_ttl" name="cache_ttl" 
                                                   value="<?= esc($cache_config['ttl'] ?? 3600) ?>" min="60">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="cache_prefix" class="form-label">Cache Prefix</label>
                                            <input type="text" class="form-control" id="cache_prefix" name="cache_prefix" 
                                                   value="<?= esc($cache_config['prefix'] ?? 'optima_') ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Redis Configuration</h5>
                                        
                                        <div class="mb-3">
                                            <label for="redis_host" class="form-label">Redis Host</label>
                                            <input type="text" class="form-control" id="redis_host" name="redis_host" 
                                                   value="<?= esc($cache_config['redis_host'] ?? 'localhost') ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="redis_port" class="form-label">Redis Port</label>
                                            <input type="number" class="form-control" id="redis_port" name="redis_port" 
                                                   value="<?= esc($cache_config['redis_port'] ?? 6379) ?>">
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="button" class="btn btn-outline-danger me-2" onclick="clearCache()">
                                                <i class="fas fa-trash me-2"></i>Clear All Cache
                                            </button>
                                            <button type="button" class="btn btn-outline-info" onclick="testCacheConnection()">
                                                <i class="fas fa-plug me-2"></i>Test Connection
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Performance Settings Tab -->
                        <div class="tab-pane fade" id="performance" role="tabpanel">
                            <form id="performanceSettingsForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Query Optimization</h5>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="query_logging" name="query_logging" 
                                                       <?= !empty($performance_config['query_logging']) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="query_logging">
                                                    Enable Query Logging
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="slow_query_threshold" class="form-label">Slow Query Threshold (seconds)</label>
                                            <input type="number" step="0.1" class="form-control" id="slow_query_threshold" name="slow_query_threshold" 
                                                   value="<?= esc($performance_config['slow_query_threshold'] ?? 1.0) ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="memory_monitoring" name="memory_monitoring" 
                                                       <?= !empty($performance_config['memory_monitoring']) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="memory_monitoring">
                                                    Memory Usage Monitoring
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Development</h5>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="profiling_enabled" name="profiling_enabled" 
                                                       <?= !empty($performance_config['profiling_enabled']) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="profiling_enabled">
                                                    Enable Profiling
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="button" class="btn btn-outline-info me-2" onclick="runPerformanceTest()">
                                                <i class="fas fa-play me-2"></i>Run Performance Test
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" onclick="clearLogs()">
                                                <i class="fas fa-broom me-2"></i>Clear Logs
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Queue Settings Tab -->
                        <div class="tab-pane fade" id="queue" role="tabpanel">
                            <form id="queueSettingsForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Queue Configuration</h5>
                                        
                                        <div class="mb-3">
                                            <label for="queue_driver" class="form-label">Queue Driver</label>
                                            <select class="form-select" id="queue_driver" name="queue_driver">
                                                <option value="database" <?= ($queue_config['driver'] ?? '') === 'database' ? 'selected' : '' ?>>Database</option>
                                                <option value="redis" <?= ($queue_config['driver'] ?? '') === 'redis' ? 'selected' : '' ?>>Redis</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="max_attempts" class="form-label">Max Retry Attempts</label>
                                            <input type="number" class="form-control" id="max_attempts" name="max_attempts" 
                                                   value="<?= esc($queue_config['max_attempts'] ?? 3) ?>" min="1">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="retry_delay" class="form-label">Retry Delay (seconds)</label>
                                            <input type="number" class="form-control" id="retry_delay" name="retry_delay" 
                                                   value="<?= esc($queue_config['retry_delay'] ?? 60) ?>" min="1">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Worker Settings</h5>
                                        
                                        <div class="mb-3">
                                            <label for="timeout" class="form-label">Job Timeout (seconds)</label>
                                            <input type="number" class="form-control" id="timeout" name="timeout" 
                                                   value="<?= esc($queue_config['timeout'] ?? 300) ?>" min="30">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="workers" class="form-label">Number of Workers</label>
                                            <input type="number" class="form-control" id="workers" name="workers" 
                                                   value="<?= esc($queue_config['workers'] ?? 2) ?>" min="1" max="10">
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="button" class="btn btn-outline-success me-2" onclick="startQueue()">
                                                <i class="fas fa-play me-2"></i>Start Queue
                                            </button>
                                            <button type="button" class="btn btn-outline-danger me-2" onclick="stopQueue()">
                                                <i class="fas fa-stop me-2"></i>Stop Queue
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" onclick="clearFailedJobs()">
                                                <i class="fas fa-trash me-2"></i>Clear Failed Jobs
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Security Settings Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <form id="securitySettingsForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Authentication</h5>
                                        
                                        <div class="mb-3">
                                            <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                                            <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                                   value="<?= esc($settings['session_timeout'] ?? 30) ?>" min="5">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                            <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                                   value="<?= esc($settings['max_login_attempts'] ?? 5) ?>" min="1">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="auto_backup" name="auto_backup" 
                                                       <?= !empty($settings['auto_backup']) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="auto_backup">
                                                    Automatic Backup
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="backup_frequency" class="form-label">Backup Frequency</label>
                                            <select class="form-select" id="backup_frequency" name="backup_frequency">
                                                <option value="daily" <?= ($settings['backup_frequency'] ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
                                                <option value="weekly" <?= ($settings['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                                <option value="monthly" <?= ($settings['backup_frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Security Actions</h5>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-primary" onclick="createBackup()">
                                                <i class="fas fa-download me-2"></i>Create Manual Backup
                                            </button>
                                            
                                            <button type="button" class="btn btn-outline-info" onclick="checkSystemHealth()">
                                                <i class="fas fa-heartbeat me-2"></i>System Health Check
                                            </button>
                                            
                                            <button type="button" class="btn btn-outline-warning" onclick="optimizeDatabase()">
                                                <i class="fas fa-database me-2"></i>Optimize Database
                                            </button>
                                            
                                            <button type="button" class="btn btn-outline-danger" onclick="clearSessions()">
                                                <i class="fas fa-sign-out-alt me-2"></i>Clear All Sessions
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Settings management JavaScript
function saveAllSettings() {
    const forms = document.querySelectorAll('#settingsTabContent form');
    let allData = {};
    
    forms.forEach(form => {
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            allData[key] = value;
        }
    });
    
    // Include checkbox values
    const checkboxes = document.querySelectorAll('#settingsTabContent input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        allData[checkbox.name] = checkbox.checked;
    });
    
    fetch('<?= base_url('admin/settings/update') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(allData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Settings saved successfully!');
        } else {
            showAlert('error', 'Error saving settings: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        showAlert('error', 'Error saving settings: ' + error.message);
    });
}

async function clearCache() {
    const confirmed = await confirmSwal({
        title: 'Hapus Cache',
        text: 'Apakah Anda yakin ingin menghapus semua cache?',
        type: 'delete',
        confirmText: '<i class="fas fa-trash me-1"></i>Ya, Hapus Cache'
    });
    if (!confirmed) return;
    fetch('<?= base_url('admin/cache/clear') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => { showAlert(data.success ? 'success' : 'error', data.message); });
}

function testCacheConnection() {
    fetch('<?= base_url('admin/cache/test') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        showAlert(data.success ? 'success' : 'error', data.message);
    });
}

function runPerformanceTest() {
    showAlert('info', 'Running performance test...');
    fetch('<?= base_url('admin/performance/test') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        showAlert(data.success ? 'success' : 'error', data.message);
    });
}

function startQueue() {
    fetch('<?= base_url('admin/queue/start') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        showAlert(data.success ? 'success' : 'error', data.message);
    });
}

async function stopQueue() {
    const confirmed = await confirmSwal({
        title: 'Hentikan Queue',
        text: 'Apakah Anda yakin ingin menghentikan queue?',
        type: 'delete',
        confirmText: '<i class="fas fa-stop me-1"></i>Ya, Hentikan'
    });
    if (!confirmed) return;
    fetch('<?= base_url('admin/queue/stop') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => { showAlert(data.success ? 'success' : 'error', data.message); });
}

function createBackup() {
    showAlert('info', 'Creating backup...');
    fetch('<?= base_url('admin/backup') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        showAlert(data.success ? 'success' : 'error', data.message);
    });
}

function checkSystemHealth() {
    fetch('<?= base_url('admin/health/check') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        showAlert(data.success ? 'success' : 'error', data.message);
    });
}

function showAlert(type, message) {
    // Gunakan global alertSwal jika tersedia, fallback ke Bootstrap alert
    if (typeof alertSwal !== 'undefined') {
        alertSwal(type, message);
        return;
    }
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    const container = document.querySelector('.mb-3') || document.body.firstChild;
    container.after(alertDiv);
    setTimeout(() => { if (alertDiv.parentNode) alertDiv.remove(); }, 5000);
}

// Additional functionality
async function clearLogs() {
    const confirmed = await confirmSwal({
        title: 'Hapus Semua Log',
        text: 'Apakah Anda yakin ingin menghapus semua log?',
        type: 'delete',
        confirmText: '<i class="fas fa-trash me-1"></i>Ya, Hapus Log'
    });
    if (!confirmed) return;
    fetch('<?= base_url('admin/logs/clear') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => { showAlert(data.success ? 'success' : 'error', data.message); });
}

async function clearFailedJobs() {
    const confirmed = await confirmSwal({
        title: 'Hapus Failed Jobs',
        text: 'Apakah Anda yakin ingin menghapus semua failed jobs?',
        type: 'delete',
        confirmText: '<i class="fas fa-trash me-1"></i>Ya, Hapus'
    });
    if (!confirmed) return;
    fetch('<?= base_url('admin/queue/clear-failed') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => { showAlert(data.success ? 'success' : 'error', data.message); });
}

async function optimizeDatabase() {
    const confirmed = await confirmSwal({
        title: 'Optimasi Database',
        text: 'Proses ini mungkin membutuhkan beberapa waktu. Apakah Anda yakin?',
        icon: 'info',
        confirmText: '<i class="fas fa-database me-1"></i>Ya, Optimasi'
    });
    if (!confirmed) return;
    showAlert('info', 'Mengoptimasi database...');
    fetch('<?= base_url('admin/database/optimize') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => { showAlert(data.success ? 'success' : 'error', data.message); });
}

function clearSessions() {
    if (confirm('Are you sure you want to clear all user sessions? This will log out all users.')) {
        fetch('<?= base_url('admin/sessions/clear') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            showAlert(data.success ? 'success' : 'error', data.message);
            if (data.success) {
                setTimeout(() => {
                    window.location.href = '<?= base_url('login') ?>';
                }, 2000);
            }
        });
    }
}
</script>

<?= $this->endSection() ?>