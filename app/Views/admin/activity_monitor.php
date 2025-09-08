<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<style>
.stat-card {
    border-left: 4px solid;
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-2px);
}
.activity-timeline {
    max-height: 400px;
    overflow-y: auto;
}
.timeline-item {
    padding: 10px;
    border-left: 3px solid #e9ecef;
    margin-bottom: 10px;
}
.timeline-item.critical {
    border-left-color: #dc3545;
    background-color: #fdf2f2;
}
.timeline-item.warning {
    border-left-color: #ffc107;
    background-color: #fffdf2;
}
.timeline-item.success {
    border-left-color: #198754;
    background-color: #f2fdf5;
}
.activity-filter {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">
    <div class="container-fluid">
        
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-chart-line text-primary"></i> Activity Monitoring Dashboard</h2>
                <p class="text-muted">Monitor semua aktivitas sistem secara real-time</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="btn btn-outline-info" onclick="exportReport()">
                    <i class="fas fa-file-export"></i> Export Report
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card" style="border-left-color: #0d6efd;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Total Activities</h6>
                                <h3 class="mb-0" id="totalActivities">-</h3>
                                <small class="text-muted">Last 24 hours</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-activity fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card" style="border-left-color: #dc3545;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Critical Activities</h6>
                                <h3 class="mb-0" id="criticalActivities">-</h3>
                                <small class="text-muted">Need attention</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card" style="border-left-color: #198754;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Active Users</h6>
                                <h3 class="mb-0" id="activeUsers">-</h3>
                                <small class="text-muted">Currently online</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card" style="border-left-color: #ffc107;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">System Health</h6>
                                <h3 class="mb-0" id="systemHealth">-</h3>
                                <small class="text-muted">Overall status</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-heartbeat fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="activity-filter">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Time Range</label>
                    <select class="form-select" id="timeRange" onchange="updateDashboard()">
                        <option value="1h">Last Hour</option>
                        <option value="24h" selected>Last 24 Hours</option>
                        <option value="7d">Last 7 Days</option>
                        <option value="30d">Last 30 Days</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Module</label>
                    <select class="form-select" id="moduleFilter" onchange="updateDashboard()">
                        <option value="">All Modules</option>
                        <option value="MARKETING">Marketing</option>
                        <option value="SERVICE">Service</option>
                        <option value="OPERATIONAL">Operational</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Action Type</label>
                    <select class="form-select" id="actionFilter" onchange="updateDashboard()">
                        <option value="">All Actions</option>
                        <option value="CREATE">Create</option>
                        <option value="UPDATE">Update</option>
                        <option value="DELETE">Delete</option>
                        <option value="PRINT">Print</option>
                        <option value="DOWNLOAD">Download</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <input type="text" class="form-control" id="userFilter" placeholder="Filter by user..." onkeyup="updateDashboard()">
                </div>
            </div>
        </div>

        <!-- Charts and Timeline -->
        <div class="row mb-4">
            <!-- Activity Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-area"></i> Activity Trends</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="activityChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Real-time Activity Feed -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-rss"></i> Live Activity Feed</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-timeline" id="activityFeed">
                            <!-- Live feed will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Module Activity Breakdown -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-pie"></i> Activity by Module</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="moduleChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-donut"></i> Action Types Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="actionChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Activity Table -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-table"></i> Detailed Activity Log</h5>
                <small class="text-muted">Read-only monitoring view</small>
            </div>
            <div class="card-body">
                <table id="activityMonitorTable" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Impact</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via DataTables -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Activity Detail Modal -->
<div class="modal fade" id="activityDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> Activity Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="activityDetailContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
let activityChart, moduleChart, actionChart;
let dataTable;

$(document).ready(function() {
    console.log('Initializing Activity Monitoring Dashboard...');
    
    // Initialize DataTable
    initializeDataTable();
    
    // Initialize Charts
    initializeCharts();
    
    // Load initial data
    updateDashboard();
    
    // Set up real-time updates
    setInterval(updateLiveFeed, 30000); // Update every 30 seconds
    
    console.log('Activity Monitoring Dashboard initialized');
});

function initializeDataTable() {
    dataTable = $('#activityMonitorTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '<?= base_url('/admin/activity-log/data') ?>',
            type: 'POST',
            data: function(d) {
                d.timeRange = $('#timeRange').val();
                d.module = $('#moduleFilter').val();
                d.action = $('#actionFilter').val();
                d.user = $('#userFilter').val();
            }
        },
        columns: [
            { 
                data: 'created_at',
                render: function(data) {
                    return '<small>' + data + '</small>';
                }
            },
            { 
                data: 'username',
                render: function(data) {
                    return '<span class="badge bg-light text-dark">' + data + '</span>';
                }
            },
            { 
                data: 'module_name',
                render: function(data) {
                    const colors = {
                        'MARKETING': 'primary',
                        'SERVICE': 'success', 
                        'OPERATIONAL': 'info',
                        'ADMIN': 'warning'
                    };
                    return '<span class="badge bg-' + (colors[data] || 'secondary') + '">' + data + '</span>';
                }
            },
            { data: 'action_type' },
            { 
                data: 'action_description',
                render: function(data) {
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
                }
            },
            { data: 'business_impact' },
            { 
                data: 'is_critical',
                render: function(data) {
                    return data ? '<i class="fas fa-exclamation-triangle text-danger"></i>' : '<i class="fas fa-check-circle text-success"></i>';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-outline-info" onclick="viewActivityDetails(' + row.id + ')"><i class="fas fa-eye"></i></button>';
                },
                orderable: false
            }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        language: {
            processing: "Loading activity data...",
            search: "Search activities:",
            lengthMenu: "Show _MENU_ activities per page",
            info: "Showing _START_ to _END_ of _TOTAL_ activities",
            emptyTable: "No activity data available",
            zeroRecords: "No matching activities found"
        }
    });
}

function initializeCharts() {
    // Activity Trend Chart
    const ctx1 = document.getElementById('activityChart').getContext('2d');
    activityChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Activities',
                data: [],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Module Chart
    const ctx2 = document.getElementById('moduleChart').getContext('2d');
    moduleChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ['#0d6efd', '#198754', '#17a2b8', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Action Chart
    const ctx3 = document.getElementById('actionChart').getContext('2d');
    actionChart = new Chart(ctx3, {
        type: 'polarArea',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ['#198754', '#0d6efd', '#dc3545', '#17a2b8', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function updateDashboard() {
    // Update statistics
    $.get('<?= base_url('/admin/activity-log/statistics') ?>', {
        period: $('#timeRange').val()
    }, function(data) {
        $('#totalActivities').text(data.total || 0);
        $('#criticalActivities').text(data.critical || 0);
        $('#activeUsers').text(data.active_users || 0);
        $('#systemHealth').text(getHealthStatus(data));
        
        // Update charts
        updateCharts(data);
    });
    
    // Refresh DataTable
    if (dataTable) {
        dataTable.ajax.reload();
    }
}

function updateCharts(data) {
    // Update activity trend chart
    if (data.trend) {
        activityChart.data.labels = data.trend.labels;
        activityChart.data.datasets[0].data = data.trend.data;
        activityChart.update();
    }
    
    // Update module chart
    if (data.by_module) {
        moduleChart.data.labels = data.by_module.map(item => item.module_name);
        moduleChart.data.datasets[0].data = data.by_module.map(item => item.count);
        moduleChart.update();
    }
    
    // Update action chart
    if (data.by_action) {
        actionChart.data.labels = data.by_action.map(item => item.action_type);
        actionChart.data.datasets[0].data = data.by_action.map(item => item.count);
        actionChart.update();
    }
}

function updateLiveFeed() {
    $.get('<?= base_url('/admin/activity-log/recent') ?>', function(data) {
        let feedHtml = '';
        data.forEach(function(activity) {
            let cssClass = activity.is_critical ? 'critical' : 
                          activity.business_impact === 'HIGH' ? 'warning' : 'success';
            
            feedHtml += `
                <div class="timeline-item ${cssClass}">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">${activity.created_at}</small>
                        <span class="badge bg-${activity.is_critical ? 'danger' : 'success'}">${activity.action_type}</span>
                    </div>
                    <div class="mt-1">
                        <strong>${activity.username}</strong> ${activity.action_description}
                    </div>
                    <small class="text-muted">${activity.module_name} • ${activity.table_name}</small>
                </div>
            `;
        });
        $('#activityFeed').html(feedHtml);
    });
}

function getHealthStatus(data) {
    const criticalRatio = data.critical / data.total;
    if (criticalRatio > 0.1) return '⚠️ Warning';
    if (criticalRatio > 0.05) return '⚡ Caution';
    return '✅ Good';
}

function refreshDashboard() {
    updateDashboard();
    updateLiveFeed();
    showNotification('Dashboard refreshed', 'success');
}

function exportReport() {
    const params = new URLSearchParams({
        format: 'csv',
        timeRange: $('#timeRange').val(),
        module: $('#moduleFilter').val(),
        action: $('#actionFilter').val(),
        user: $('#userFilter').val()
    });
    
    window.open('<?= base_url('/admin/activity-log/export') ?>?' + params.toString());
}

function viewActivityDetails(id) {
    $.get('<?= base_url('/admin/activity-log/details/') ?>' + id, function(data) {
        let content = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-user"></i> User Information</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Username:</strong> ${data.username || 'System'}</p>
                            <p><strong>Full Name:</strong> ${(data.first_name || '') + ' ' + (data.last_name || '')}</p>
                            <p><strong>IP Address:</strong> ${data.ip_address || 'N/A'}</p>
                            <p><strong>User Agent:</strong> <small>${data.user_agent || 'N/A'}</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-cog"></i> Activity Information</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Action:</strong> <span class="badge bg-primary">${data.action_type}</span></p>
                            <p><strong>Module:</strong> <span class="badge bg-secondary">${data.module_name}</span></p>
                            <p><strong>Table:</strong> ${data.table_name}</p>
                            <p><strong>Record ID:</strong> ${data.record_id}</p>
                            <p><strong>Time:</strong> ${data.created_at}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> Description & Impact</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Description:</strong></p>
                            <p class="text-muted">${data.action_description}</p>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Business Impact:</strong> 
                                        <span class="badge bg-${data.business_impact === 'HIGH' ? 'danger' : data.business_impact === 'MEDIUM' ? 'warning' : 'success'}">${data.business_impact}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Critical:</strong> 
                                        ${data.is_critical ? '<i class="fas fa-exclamation-triangle text-danger"></i> Yes' : '<i class="fas fa-check-circle text-success"></i> No'}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        if (data.old_values || data.new_values) {
            content += `
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-exchange-alt"></i> Data Changes</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Previous Values:</h6>
                                        <pre class="bg-light p-2 rounded">${data.old_values ? JSON.stringify(JSON.parse(data.old_values), null, 2) : 'No previous data'}</pre>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>New Values:</h6>
                                        <pre class="bg-light p-2 rounded">${data.new_values ? JSON.stringify(JSON.parse(data.new_values), null, 2) : 'No new data'}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        $('#activityDetailContent').html(content);
        $('#activityDetailModal').modal('show');
    }).fail(function() {
        showNotification('Error loading activity details', 'error');
    });
}

function showNotification(message, type) {
    // Simple notification function
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(notification);
    setTimeout(() => $('.alert').alert('close'), 3000);
}
</script>
<?= $this->endSection() ?>
