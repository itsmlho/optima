<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    .table-card, .card-stats { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
    .modal-header { background: linear-gradient(135deg, #e9ecef 0%, #e9ecef 100%); color: white; border-radius: 15px 15px 0 0; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Statistics Cards (Nilai akan diisi oleh JavaScript) -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-primary text-white h-100"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-total">0</h2><h6 class="card-title text-uppercase small">Total Kontrak</h6></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-success text-white h-100"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-active">0</h2><h6 class="card-title text-uppercase small">Kontrak Aktif</h6></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-warning text-white h-100"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-expiring">0</h2><h6 class="card-title text-uppercase small">Akan Berakhir</h6></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-secondary text-white h-100"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-expired">0</h2><h6 class="card-title text-uppercase small">Telah Berakhir</h6></div></div></div>
    </div>

    <!-- Tabel Daftar Kontrak -->
    <div class="card table-card">
        <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <h5 class="h5 mb-0 text-gray-800">Daftar Kontrak Rental</h5>
            <button class="btn btn-sm btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addContractModal">
                <span class="fw-semibold">+ Kontrak</span>
            </button>
        </div>
        <div class="card-body">
            <table id="contractsTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>No. Kontrak</th>
                        <th>Klien</th>
                        <th>Periode Kontrak</th>
                        <th>Nilai</th>
                        <th>Total Unit</th> <!-- UPDATE: Kolom baru ditambahkan -->
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan dimuat oleh DataTables melalui AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kontrak -->
<div class="modal fade" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kontrak Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">No. Kontrak*</label><input type="text" class="form-control" name="contract_number" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">No. PO Klien</label><input type="text" class="form-control" name="po_number"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Klien*</label><input type="text" class="form-control" name="client_name" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Proyek</label><input type="text" class="form-control" name="project_name"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai*</label><input type="date" class="form-control" name="start_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Berakhir*</label><input type="date" class="form-control" name="end_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nilai Kontrak (Rp)*</label><input type="number" class="form-control" name="contract_value" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Status Awal*</label><select class="form-select" name="status" required><option value="Aktif">Aktif</option><option value="Pending">Pending</option><option value="Berakhir">Berakhir</option><option value="Dibatalkan">Dibatalkan</option></select></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kontrak</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Fallback untuk OptimaPro jika belum dimuat
if (typeof OptimaPro === 'undefined') {
    window.OptimaPro = {
        showNotification: function(message, type, duration = 5000) {
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' || type === 'danger' ? 'alert-danger' : 
                              type === 'warning' ? 'alert-warning' : 'alert-info';
            
            const notification = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('body').append(notification);
            setTimeout(() => $('.alert').fadeOut(), duration);
        },
        showConfirmDialog: function({title, message}) {
            return new Promise(resolve => {
                if (confirm(`${title}\n${message}`)) {
                    resolve({ isConfirmed: true });
                } else {
                    resolve({ isConfirmed: false });
                }
            });
        }
    };
}
$(document).ready(function() {
    console.log('Initializing DataTable...');
    console.log('AJAX URL:', '<?= base_url('marketing/contracts/getDataTable') ?>');
    
    const contractsTable = $('#contractsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('marketing/kontrak/getDataTable') ?>', // Updated endpoint
            type: 'POST',
            data: function(d) {
                // Add CSRF token to the data
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                console.log('Sending data:', d);
                return d;
            },
            dataSrc: function(json) {
                console.log('DataTable AJAX Response:', json);
                if(!json){ console.warn('No JSON returned'); return []; }
                if(json.error){ console.error('Server returned error:', json.error); }
                
                // Check for errors
                if (json.error) {
                    console.error('Server Error:', json.error);
                    OptimaPro.showNotification('Server Error: ' + json.error, 'danger');
                    return [];
                }
                
                // Update statistics if available
                if (json && json.stats) {
                    $('#stat-total').text(json.stats.total ?? 0);
                    $('#stat-active').text(json.stats.active ?? 0);
                    $('#stat-expiring').text(json.stats.expiring ?? 0);
                    $('#stat-expired').text(json.stats.expired ?? 0);
                }
                
                return json.data || [];
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', xhr, error, thrown);
                console.log('Response Text:', xhr.responseText);
                OptimaPro.showNotification('Error loading data: ' + error, 'danger');
            }
        },
        columns: [
            { data: 'contract_number' },
            { data: 'client_name' },
            { data: 'period' },
            { data: 'value' },
            { data: 'total_units' }, // UPDATE: Kolom baru ditambahkan
            { data: 'status' },
            { data: 'actions', orderable: false }
        ],
        order: [[0, 'desc']], // Changed to first column (contract_number)
        drawCallback: function(settings) {
            console.log('DataTable draw callback executed');
            const json = this.api().ajax.json();
            console.log('Draw callback - JSON data:', json);
            if(json && json.data && json.data.length===0){
                console.log('No rows rendered. If expected rows exist in DB, check controller Marketing::getDataTable and DB connection.');
            }
        }
    });

    $('#addContractForm').on('submit', function(e) {
        e.preventDefault();
        
        const contractId = $(this).data('contract-id');
        const isEdit = contractId ? true : false;
        const url = isEdit ? 
            `<?= base_url('marketing/kontrak/update/') ?>${contractId}` : 
            '<?= base_url('marketing/kontrak/store') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>',
            dataType: 'json',
            success: function(response) {
                $('#addContractModal').modal('hide');
                if (response.success) {
                    OptimaPro.showNotification(response.message, 'success');
                    contractsTable.ajax.reload();
                    
                    // Reset form after success
                    $('#addContractForm')[0].reset();
                    $('#addContractForm').removeData('contract-id');
                    $('#addContractForm').attr('action', 'store');
                    $('#addContractModal .modal-title').text('Tambah Kontrak Baru');
                } else {
                    OptimaPro.showNotification(response.message || 'Terjadi kesalahan.', 'danger');
                }
            },
            error: function() {
                OptimaPro.showNotification('Tidak dapat terhubung ke server.', 'danger');
            }
        });
    });
    
    // Reset form when modal is closed
    $('#addContractModal').on('hidden.bs.modal', function() {
        $('#addContractForm')[0].reset();
        $('#addContractForm').removeData('contract-id');
        $('#addContractForm').attr('action', 'store');
        $('#addContractModal .modal-title').text('Tambah Kontrak Baru');
    });
});

function viewContractUnits(contractId) {
    // Arahkan ke halaman list_unit.php dengan membawa ID kontrak
    window.location.href = `<?= base_url('marketing/list-unit/') ?>${contractId}`;
}

function editContract(contractId) {
    // Load data kontrak untuk edit
    $.ajax({
        url: `<?= base_url('marketing/kontrak/detail/') ?>${contractId}`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const contract = response.data;
                // Populate form with existing data
                $('input[name="contract_number"]').val(contract.no_kontrak);
                $('input[name="po_number"]').val(contract.no_po_marketing);
                $('input[name="client_name"]').val(contract.pelanggan);
                $('input[name="project_name"]').val(contract.lokasi);
                $('input[name="start_date"]').val(contract.tanggal_mulai);
                $('input[name="end_date"]').val(contract.tanggal_berakhir);
                $('select[name="status"]').val(contract.status);
                
                // Change form action to update
                $('#addContractForm').attr('action', 'update');
                $('#addContractForm').data('contract-id', contractId);
                $('#addContractModal .modal-title').text('Edit Kontrak');
                $('#addContractModal').modal('show');
            } else {
                OptimaPro.showNotification(response.message, 'error');
            }
        },
        error: function() {
            OptimaPro.showNotification('Gagal memuat data kontrak.', 'error');
        }
    });
}

function deleteContract(contractId) {
    OptimaPro.showConfirmDialog({
        title: 'Konfirmasi Hapus',
        message: 'Apakah Anda yakin ingin menghapus kontrak ini?'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?= base_url('marketing/kontrak/delete/') ?>${contractId}`,
                method: 'POST',
                data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        OptimaPro.showNotification(response.message, 'success');
                        contractsTable.ajax.reload();
                    } else {
                        OptimaPro.showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    OptimaPro.showNotification('Gagal menghapus kontrak.', 'error');
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
