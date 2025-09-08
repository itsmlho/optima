<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">
    <div class="container-fluid">
        
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-history text-primary"></i> System Activity Log</h2>
                <p class="text-muted">Semua aktivitas user dicatat di sini (CREATE, UPDATE, DELETE, PRINT, DOWNLOAD)</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-success" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <a href="<?= base_url('/admin/activity-log/export') ?>" class="btn btn-info">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <table id="activityLogTable" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Tabel</th>
                            <th>Deskripsi</th>
                            <th>Impact</th>
                            <th>Critical</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat melalui DataTables -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal untuk Detail Activity -->
<div class="modal fade" id="activityDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Activity Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="activityDetailContent">
                <!-- Content akan dimuat via AJAX -->
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?php // Using 'script' section instead of 'js' to match base layout ?>
<?= $this->section('script') ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    console.log('Initializing Activity Log DataTable...');
    
    $('#activityLogTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('/admin/activity-log/data') ?>',
            type: 'POST',
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX Error:', error, thrown);
                console.log('Response:', xhr.responseText);
            }
        },
        columns: [
            { data: 'created_at' },
            { data: 'username' },
            { data: 'module_name' },
            { data: 'action_type' },
            { data: 'table_name' },
            { data: 'action_description' },
            { data: 'business_impact' },
            { data: 'is_critical' },
            { data: 'actions' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            emptyTable: "Tidak ada data activity log",
            zeroRecords: "Tidak ada data yang cocok"
        }
    });
    
    console.log('DataTable initialized');
});

// Function untuk show detail
function viewDetails(id) {
    $.ajax({
        url: '<?= base_url('/admin/activity-log/details/') ?>' + id,
        type: 'GET',
        success: function(data) {
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>User Information:</strong></h6>
                        <p><strong>Username:</strong> ${data.user.username}</p>
                        <p><strong>Name:</strong> ${data.user.name}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Action Information:</strong></h6>
                        <p><strong>Type:</strong> <span class="badge bg-primary">${data.action.type}</span></p>
                        <p><strong>Module:</strong> ${data.action.module}</p>
                        <p><strong>Table:</strong> ${data.action.table}</p>
                        <p><strong>Record ID:</strong> ${data.action.record_id}</p>
                    </div>
                </div>
                <hr>
                <h6><strong>Description:</strong></h6>
                <p>${data.action.description}</p>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Impact:</strong></h6>
                        <p><strong>Level:</strong> <span class="badge bg-warning">${data.impact.level}</span></p>
                        <p><strong>Critical:</strong> ${data.impact.critical ? 'Ya' : 'Tidak'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Technical Info:</strong></h6>
                        <p><strong>Time:</strong> ${data.created_at}</p>
                        <p><strong>IP:</strong> ${data.technical.ip_address || '-'}</p>
                    </div>
                </div>
            `;
            
            if (data.changes.old_values || data.changes.new_values) {
                content += `
                    <hr>
                    <h6><strong>Data Changes:</strong></h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Old Values:</h6>
                            <pre>${JSON.stringify(data.changes.old_values, null, 2)}</pre>
                        </div>
                        <div class="col-md-6">
                            <h6>New Values:</h6>
                            <pre>${JSON.stringify(data.changes.new_values, null, 2)}</pre>
                        </div>
                    </div>
                `;
            }
            
            $('#activityDetailContent').html(content);
            $('#activityDetailModal').modal('show');
        },
        error: function() {
            alert('Error loading activity details');
        }
    });
}
</script>
<?= $this->endSection() ?>
