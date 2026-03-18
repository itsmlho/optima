<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks me-2"></i>Queue Management Dashboard
                    </h6>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="processQueue()">
                            <i class="fas fa-play me-1"></i>Process Queue
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-1" onclick="refreshStats()">
                            <i class="fas fa-sync me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats Row -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="card-title h6">Pending Jobs</div>
                                            <div class="h4" id="pendingCount"><?= $queue_stats['pending'] ?? 0 ?></div>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="card-title h6">Completed</div>
                                            <div class="h4" id="completedCount"><?= $queue_stats['completed'] ?? 0 ?></div>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="card-title h6">Failed Jobs</div>
                                            <div class="h4" id="failedCount"><?= $queue_stats['failed'] ?? 0 ?></div>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="card-title h6">Cache Files</div>
                                            <div class="h4" id="cacheFiles"><?= $cache_stats['cache_files'] ?? 0 ?></div>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-hdd fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <button type="button" class="btn btn-outline-primary w-100" onclick="testEmail()">
                                                <i class="fas fa-envelope me-2"></i>Test Email Queue
                                            </button>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <button type="button" class="btn btn-outline-success w-100" onclick="testNotification()">
                                                <i class="fas fa-bell me-2"></i>Test Notification Queue
                                            </button>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <button type="button" class="btn btn-outline-warning w-100" onclick="clearCache()">
                                                <i class="fas fa-broom me-2"></i>Clear Cache
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <button type="button" class="btn btn-outline-danger w-100" onclick="cleanFailedJobs()">
                                                <i class="fas fa-trash me-2"></i>Clean Failed Jobs
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <button type="button" class="btn btn-outline-info w-100" onclick="showLogs()">
                                                <i class="fas fa-list me-2"></i>View Logs
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Queue Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Queue Directory:</strong></td>
                                            <td><code><?= WRITEPATH ?>queue/</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Max Jobs per Run:</strong></td>
                                            <td>5 jobs</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Job Timeout:</strong></td>
                                            <td>30 seconds</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Processed:</strong></td>
                                            <td id="lastProcessed"><?= $queue_stats['last_processed'] ?? 'Never' ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-server me-2"></i>Cache Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Cache Handler:</strong></td>
                                            <td>File-based</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cache Directory:</strong></td>
                                            <td><code><?= WRITEPATH ?>cache/</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cache Size:</strong></td>
                                            <td id="cacheSize"><?= $cache_stats['cache_size'] ?? '0 bytes' ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Default TTL:</strong></td>
                                            <td>5 minutes</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Action Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function processQueue() {
    fetch('<?= base_url('queue/process') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        showResult(data);
        if (data.success) {
            refreshStats();
        }
    })
    .catch(error => {
        showResult({success: false, message: 'Error: ' + error.message});
    });
}

function refreshStats() {
    fetch('<?= base_url('queue/stats') ?>')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStatsUI(data);
        }
    });
}

function testEmail() {
    const email = prompt('Enter email address to test:', '<?= session()->get('email') ?>');
    if (email) {
        fetch('<?= base_url('queue/test-email') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => showResult(data));
    }
}

function testNotification() {
    fetch('<?= base_url('queue/test-notification') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => showResult(data));
}

function clearCache() {
    OptimaConfirm.danger({
        title: 'Clear Cache?',
        text: 'Semua cache akan dihapus.',
        confirmText: 'Ya, Clear!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            fetch('<?= base_url('queue/clear-cache') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                showResult(data);
                refreshStats();
            });
        }
    });
}

function cleanFailedJobs() {
    OptimaConfirm.danger({
        title: 'Clean Failed Jobs?',
        text: 'Semua failed jobs akan dibersihkan.',
        confirmText: 'Ya, Clean!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            fetch('<?= base_url('queue/clean-failed') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                showResult(data);
                refreshStats();
            });
        }
    });
}

function showLogs() {
    showResult({
        success: true,
        message: 'Check application logs in: writable/logs/ for detailed queue processing information.'
    });
}

function showResult(data) {
    const modalBody = document.getElementById('modalBody');
    const alertClass = data.success ? 'alert-success' : 'alert-danger';
    const icon = data.success ? 'check-circle' : 'exclamation-triangle';
    
    modalBody.innerHTML = `
        <div class="alert ${alertClass}">
            <i class="fas fa-${icon} me-2"></i>
            ${data.message}
            ${data.job_id ? `<br><small>Job ID: ${data.job_id}</small>` : ''}
            ${data.processed_count ? `<br><small>Processed: ${data.processed_count} jobs</small>` : ''}
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('resultModal')).show();
}

function updateStatsUI(data) {
    if (data.queue_stats) {
        document.getElementById('pendingCount').textContent = data.queue_stats.pending || 0;
        document.getElementById('completedCount').textContent = data.queue_stats.completed || 0;
        document.getElementById('failedCount').textContent = data.queue_stats.failed || 0;
        document.getElementById('lastProcessed').textContent = data.queue_stats.last_processed || 'Never';
    }
    
    if (data.cache_stats) {
        document.getElementById('cacheFiles').textContent = data.cache_stats.cache_files || 0;
        document.getElementById('cacheSize').textContent = data.cache_stats.cache_size || '0 bytes';
    }
}

// Auto-refresh stats every 30 seconds
setInterval(refreshStats, 30000);
</script>

<?= $this->endSection() ?>