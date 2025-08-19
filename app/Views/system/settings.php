<?= $this->extend('layouts/base') ?>

<?= $this->section('title') ?>System Settings - OPTIMA<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cogs mr-2"></i>System Settings
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="saveSettings()">
                <i class="fas fa-save"></i> Save Settings
            </button>
            <button type="button" class="btn btn-secondary" onclick="resetSettings()">
                <i class="fas fa-undo"></i> Reset to Default
            </button>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sliders-h"></i> Application Settings
                    </h6>
                </div>
                <div class="card-body">
                    <form id="settingsForm">
                        <!-- General Settings -->
                        <div class="form-section">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle"></i> General Settings
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="app_name">Application Name</label>
                                        <input type="text" class="form-control" id="app_name" 
                                               value="OPTIMA - Asset Management System">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="app_version">Version</label>
                                        <input type="text" class="form-control" id="app_version" 
                                               value="1.0.0" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="app_description">Description</label>
                                <textarea class="form-control" id="app_description" rows="3">
Professional Asset Management System for PT Sarana Mitra Luas Tbk
                                </textarea>
                            </div>
                        </div>

                        <hr>

                        <!-- Company Settings -->
                        <div class="form-section">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-building"></i> Company Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">Company Name</label>
                                        <input type="text" class="form-control" id="company_name" 
                                               value="PT Sarana Mitra Luas Tbk">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_email">Company Email</label>
                                        <input type="email" class="form-control" id="company_email" 
                                               value="info@saranamitraluas.co.id">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="company_address">Company Address</label>
                                <textarea class="form-control" id="company_address" rows="3">
Jalan Raya Bekasi Km. 21, Jakarta Timur, DKI Jakarta 13920
                                </textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_phone">Phone</label>
                                        <input type="text" class="form-control" id="company_phone" 
                                               value="+62 21 461 0808">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_website">Website</label>
                                        <input type="url" class="form-control" id="company_website" 
                                               value="https://www.saranamitraluas.co.id">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- System Settings -->
                        <div class="form-section">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-cog"></i> System Configuration
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="timezone">Timezone</label>
                                        <select class="form-control" id="timezone">
                                            <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                                            <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                            <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_format">Date Format</label>
                                        <select class="form-control" id="date_format">
                                            <option value="d/m/Y" selected>DD/MM/YYYY</option>
                                            <option value="m/d/Y">MM/DD/YYYY</option>
                                            <option value="Y-m-d">YYYY-MM-DD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currency">Currency</label>
                                        <select class="form-control" id="currency">
                                            <option value="IDR" selected>Indonesian Rupiah (IDR)</option>
                                            <option value="USD">US Dollar (USD)</option>
                                            <option value="SGD">Singapore Dollar (SGD)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language">Language</label>
                                        <select class="form-control" id="language">
                                            <option value="id" selected>Bahasa Indonesia</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Performance Settings -->
                        <div class="form-section">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-tachometer-alt"></i> Performance & Limits
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="items_per_page">Items Per Page</label>
                                        <select class="form-control" id="items_per_page">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="session_timeout">Session Timeout (minutes)</label>
                                        <select class="form-control" id="session_timeout">
                                            <option value="30">30</option>
                                            <option value="60" selected>60</option>
                                            <option value="120">120</option>
                                            <option value="240">240</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Security Settings -->
                        <div class="form-section">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-shield-alt"></i> Security Settings
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="max_login_attempts">Max Login Attempts</label>
                                        <select class="form-control" id="max_login_attempts">
                                            <option value="3">3</option>
                                            <option value="5" selected>5</option>
                                            <option value="10">10</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_expiry">Password Expiry (days)</label>
                                        <select class="form-control" id="password_expiry">
                                            <option value="30">30</option>
                                            <option value="60">60</option>
                                            <option value="90" selected>90</option>
                                            <option value="180">180</option>
                                            <option value="0">Never</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- System Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-heartbeat"></i> System Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="status-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Application Status</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                    </div>
                    <div class="status-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Database Status</span>
                            <span class="badge bg-success">Connected</span>
                        </div>
                    </div>
                    <div class="status-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Server Load</span>
                            <span class="badge bg-warning">Moderate</span>
                        </div>
                    </div>
                    <div class="status-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Memory Usage</span>
                            <span class="text-muted">45.2 MB</span>
                        </div>
                    </div>
                    <div class="status-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Storage Available</span>
                            <span class="text-muted">2.4 GB</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="clearCache()">
                            <i class="fas fa-broom"></i> Clear Cache
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="backupDatabase()">
                            <i class="fas fa-database"></i> Backup Database
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="viewLogs()">
                            <i class="fas fa-file-alt"></i> View System Logs
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="systemInfo()">
                            <i class="fas fa-info"></i> System Information
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock"></i> Recent Settings Changes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="activity-item mb-2">
                        <div class="small text-muted">Today, 14:30</div>
                        <div>Session timeout updated to 60 minutes</div>
                    </div>
                    <div class="activity-item mb-2">
                        <div class="small text-muted">Yesterday, 09:15</div>
                        <div>Currency changed to IDR</div>
                    </div>
                    <div class="activity-item">
                        <div class="small text-muted">2 days ago, 16:45</div>
                        <div>Timezone set to Asia/Jakarta</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function saveSettings() {
    // Show loading
    OptimaPro.showLoading('Saving settings...');
    
    // Simulate save process
    setTimeout(() => {
        OptimaPro.hideLoading();
        OptimaPro.showNotification('Settings saved successfully!', 'success');
    }, 1000);
}

function resetSettings() {
    if (confirm('Are you sure you want to reset all settings to default values?')) {
        OptimaPro.showLoading('Resetting settings...');
        
        setTimeout(() => {
            OptimaPro.hideLoading();
            OptimaPro.showNotification('Settings reset to default values', 'info');
            location.reload();
        }, 1000);
    }
}

function clearCache() {
    OptimaPro.showLoading('Clearing cache...');
    
    setTimeout(() => {
        OptimaPro.hideLoading();
        OptimaPro.showNotification('Cache cleared successfully!', 'success');
    }, 800);
}

function backupDatabase() {
    OptimaPro.showLoading('Creating database backup...');
    
    setTimeout(() => {
        OptimaPro.hideLoading();
        OptimaPro.showNotification('Database backup created successfully!', 'success');
    }, 2000);
}

function viewLogs() {
    window.open('/logs', '_blank');
}

function systemInfo() {
    alert('System Information:\n\nPHP Version: 8.1.0\nCodeIgniter: 4.x\nDatabase: MySQL 8.0\nServer: Apache 2.4');
}
</script>
<?= $this->endSection() ?>
