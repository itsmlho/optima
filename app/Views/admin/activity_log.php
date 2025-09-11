<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .activity-detail-row {
        border-left: 4px solid #007bff;
        padding-left: 15px;
    }
    .status-badge {
        font-size: 0.875em;
    }
    .activity-timestamp {
        color: #6c757d;
        font-size: 0.9em;
    }
    .pre-scrollable {
        max-height: 200px;
        overflow-y: auto;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body" id="activityLogContent">

        
        <div class="row mb-4">
            <div class="col">
                <p class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Semua aktivitas user dicatat di sini dengan deskripsi yang detail. 
                    <strong>Klik pada baris tabel</strong> untuk melihat informasi lengkap perubahan data.
                </p>
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
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat melalui DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
</div>

<!-- Modal untuk Detail Activity -->
<div class="modal fade" id="activityDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Detail Activity Log
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="activityDetailContent">
                <!-- Content akan dimuat via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
            },
            dataSrc: function(json) {
                // Debug: log the data structure
                console.log('DataTables received data:', json);
                if (json.data && json.data.length > 0) {
                    console.log('First row data:', json.data[0]);
                    console.log('Available fields:', Object.keys(json.data[0]));
                }
                return json.data;
            }
        },
        columns: [
            { data: 'created_at', title: 'Waktu' },
            { data: 'username', title: 'User' },
            { data: 'module_name', title: 'Module' },
            { data: 'action_type', title: 'Action' },
            { data: 'table_name', title: 'Tabel' },
            { data: 'action_description', title: 'Deskripsi' },
            { data: 'business_impact', title: 'Impact' },
            { data: 'is_critical', title: 'Critical' }
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
        },
        rowCallback: function(row, data) {
            // Add click event to each row
            $(row).css('cursor', 'pointer');
            $(row).attr('title', 'Klik untuk melihat detail lengkap');
            $(row).on('click', function() {
                const activityId = data.activity_id;
                console.log('Row clicked, activity_id:', activityId);
                if (activityId) {
                    viewDetails(activityId);
                } else {
                    console.error('No activity_id found in row data:', data);
                }
            });
            
            // Add tooltip for action description
            $(row).find('td:eq(5)').attr('title', data.action_description);
        }
    });
    
    console.log('DataTable initialized');
});

// Function untuk show detail
function viewDetails(id) {
    $.ajax({
        url: '<?= base_url('/admin/activity-log/details/') ?>' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong>Informasi User:</strong></h6>
                            <p><strong>Username:</strong> ${data.username}</p>
                            <p><strong>Nama:</strong> ${data.full_name || '-'}</p>
                            <p><strong>Waktu:</strong> ${data.created_at}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Informasi Aktivitas:</strong></h6>
                            <p><strong>Tipe:</strong> <span class="badge bg-primary">${data.action_type}</span></p>
                            <p><strong>Module:</strong> ${data.module_name}</p>
                            <p><strong>Tabel:</strong> ${data.table_name}</p>
                            <p><strong>Record ID:</strong> ${data.record_id}</p>
                        </div>
                    </div>
                    <hr>
                    <h6><strong>Deskripsi:</strong></h6>
                    <p>${data.action_description}</p>
                `;
                
                if (data.workflow_stage) {
                    content += `
                        <hr>
                        <h6><strong>Workflow Stage:</strong></h6>
                        <p><span class="badge bg-info">${data.workflow_stage}</span></p>
                    `;
                }
                
                content += `
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong>Impact:</strong></h6>
                            <p><strong>Level:</strong> <span class="badge bg-warning">${data.business_impact}</span></p>
                            <p><strong>Critical:</strong> ${data.is_critical ? 'Ya' : 'Tidak'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Field Yang Terpengaruh:</strong></h6>
                            <p>${data.affected_fields ? JSON.stringify(data.affected_fields).replace(/[{}"\[\]]/g, '').replace(/,/g, ', ') : 'Tidak ada data'}</p>
                        </div>
                    </div>
                `;
                
                if (data.old_values || data.new_values) {
                    content += `
                        <hr>
                        <h6><strong>Perubahan Data:</strong></h6>
                        <div class="row">
                    `;
                    
                    if (data.old_values) {
                        content += `
                            <div class="col-md-6">
                                <h6 class="text-danger">Nilai Lama:</h6>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    <pre class="bg-light p-2 small">${JSON.stringify(data.old_values, null, 2)}</pre>
                                </div>
                            </div>
                        `;
                    }
                    
                    if (data.new_values) {
                        content += `
                            <div class="col-md-6">
                                <h6 class="text-success">Nilai Baru:</h6>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    <pre class="bg-light p-2 small">${JSON.stringify(data.new_values, null, 2)}</pre>
                                </div>
                            </div>
                        `;
                    }
                    
                    content += `</div>`;
                }
                
                $('#activityDetailContent').html(content);
                $('#activityDetailModal').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('Error loading activity details: ' + error);
        }
    });
}
</script>
<?= $this->endSection() ?>
