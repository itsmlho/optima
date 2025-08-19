<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<!-- Style from invent_unit.php for consistency -->
<style>
    .table-card { border: none; border-radius: 15px; box-shadow: 0 4px 25px rgba(0,0,0,0.1); }
    .card-stats { 
        border: none; 
        border-radius: 15px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.15); }
    .card-stats.active { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.2); border: 2px solid #0d6efd; }
</style>

<style>
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .stat-card .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .stat-card.total .stat-icon { background: rgba(0, 97, 242, 0.1); color: #0061f2; }
    .stat-card.available .stat-icon { background: rgba(0, 172, 105, 0.1); color: #00ac69; }
    .stat-card.rented .stat-icon { background: rgba(255, 182, 7, 0.1); color: #ffb607; }
    .stat-card.maintenance .stat-icon { background: rgba(232, 21, 0, 0.1); color: #e81500; }
    
    .stat-number { font-size: 2.5rem; font-weight: 700; color: #343a40; }
    .stat-label { font-size: 0.875rem; color: #6c757d; text-transform: uppercase; }

    .filter-card .card-header {
        background: rgba(0, 97, 242, 0.05);
        border-bottom: 1px solid rgba(0, 97, 242, 0.1);
    }

    .btn-add-asset {
        background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
        border: none;
        color: white;
    }
    .btn-add-asset:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 97, 242, 0.25);
        color: white;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Statistics Cards (Filters) -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-primary text-white h-100 active" onclick="applyCardFilter('')">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1" id="stat-total"><?= $stats['total'] ?? 0 ?></h2>
                    <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Semua Unit</h6>
                </div>
                <i class="fas fa-truck-monster fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-success text-white h-100" onclick="applyCardFilter('7')">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1" id="stat-available"><?= $stats['available'] ?? 0 ?></h2>
                    <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Stock Aset</h6>
                </div>
                <i class="fas fa-check-circle fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-info text-white h-100" onclick="applyCardFilter('3')">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1" id="stat-rented"><?= $stats['rented'] ?? 0 ?></h2>
                    <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Disewakan</h6>
                </div>
                <i class="fas fa-people-carry fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-warning text-white h-100" onclick="applyCardFilter('2')">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1" id="stat-maintenance"><?= $stats['maintenance'] ?? 0 ?></h2>
                    <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Workshop/Rusak</h6>
                </div>
                <i class="fas fa-tools fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Unit Assets Table -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Unit Asset List</h5>
        <div>
            <a href="<?= base_url('warehouse/unit-assets/export') ?>" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> Export
            </a>
            <button class="btn btn-add-asset btn-sm" onclick="addUnitAsset()" title="Creation disabled after migration" type="button">
                <i class="fas fa-info-circle me-1"></i> Creation Disabled
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="unitAssetsTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No. Unit</th>
                        <th>Serial Number</th>
                        <th>Model</th>
                        <th>Department</th>
                        <th>Location</th>
                        <th>Unit Status</th>
                        <!-- <th>Asset Status</th> -->
                        <th>Aksi</th>
                        <th>Konfirmasi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- View Modal -->
<div class="modal fade" id="viewUnitModal" tabindex="-1" aria-labelledby="viewUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUnitModalLabel">Unit Asset Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewUnitModalBody">
                <!-- Details will be loaded here via AJAX -->
                <div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal (Placeholder - a full implementation would be more complex) -->
<div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUnitModalLabel">Edit Unit Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editUnitModalBody">
                <p>The "Add" and "Edit" functionality is typically handled on a separate page for complex forms. Clicking "Add" or "Edit" will redirect to the appropriate page.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    // Placeholder for OptimaPro notifications if not defined
    if (typeof OptimaPro === 'undefined') {
        window.OptimaPro = {
            showNotification: (message, type) => {
                console.warn('OptimaPro not loaded, falling back to alert:', message);
                // Create a temporary notification element
                const notification = document.createElement('div');
                notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
                notification.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                document.body.appendChild(notification);
                setTimeout(() => notification.remove(), 5000);
            },
            showConfirmDialog: ({ title, message }) => {
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

    let unitAssetsTable;
    let currentStatusFilter = '';

    $(document).ready(function() {
        unitAssetsTable = $('#unitAssetsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('warehouse/unit-assets/datatable') ?>',
                type: 'POST',
                data: function (d) {
                    console.log('1. DataTables Mengirim Request:', d); // Log data yang dikirim
                    d.<?= csrf_token() ?> = '<?= csrf_hash() ?>'; // CSRF Token
                    d.status_unit_filter = currentStatusFilter; // Filter from cards
                    return d;
                },
                dataSrc: function (json) {
                    console.log('2. DataTables Menerima Response:', json); // Log response lengkap dari server
                    if (!json || !json.data) {
                        console.error('Format JSON dari server tidak valid:', json);
                        return [];
                    }
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('3. Terjadi AJAX Error:', { xhr, error, thrown });
                    $('#unitAssetsTable_processing').hide(); // Sembunyikan indikator "processing"
                    OptimaPro.showNotification('Gagal memuat data dari server. Silakan periksa browser console untuk detailnya.', 'error');
                }
            },
            columns: [
                { data: 'no_unit' },       // Show actual unit number instead of row index
                { data: 'serial_number' },
                { data: 'model_unit' },     // <-- Diperbaiki
                { data: 'departemen' },     // <-- Diperbaiki
                { data: 'lokasi_unit' },    // <-- Diperbaiki
                { data: 'status_unit' },
                { data: 'actions', orderable: false, searchable: false },
                { data: 'no_unit', orderable:false, searchable:false, render: function(data, type, row){
                        // Tampilkan tombol konfirmasi jika status bukan RENTAL
                        const rawStatus = (row.status_unit || '').toString().toUpperCase();
                        if (rawStatus.includes('RENTAL')) return '<span class="text-muted small">-</span>';
                        return `<button class=\"btn btn-sm btn-success\" title=\"Konfirmasi Jadi Asset\" onclick=\"confirmToAsset(${data})\"><i class=\"fas fa-check\"></i></button>`;
                    }
                }
            ],
            responsive: true,
            pageLength: 25,
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
            }
        });
    });

    function applyCardFilter(status) {
        $('.card-stats').removeClass('active');
        $(`.card-stats[onclick="applyCardFilter('${status}')"]`).addClass('active');
        currentStatusFilter = status;
        unitAssetsTable.ajax.reload();
    }

    function addUnitAsset() {
        OptimaPro.showNotification('Penambahan unit baru saat ini dinonaktifkan (inventory_unit menjadi sumber tunggal).', 'info');
    }

    function viewUnitAsset(no_unit) {
        const modalBody = $('#viewUnitModalBody');
        modalBody.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
        
        const viewModal = new bootstrap.Modal(document.getElementById('viewUnitModal'));
        viewModal.show();

        $.ajax({
            url: `<?= base_url('warehouse/unit-assets/show/') ?>${no_unit}`,
            type: 'GET',
            success: function(response) {
                // Assuming the controller returns a rendered HTML view for the modal body
                modalBody.html(response);
            },
            error: function() {
                modalBody.html('<div class="alert alert-danger">Failed to load unit details.</div>');
            }
        });
    }

    function editUnitAsset(no_unit) {
        window.location.href = `<?= base_url('warehouse/unit-assets/edit/') ?>${no_unit}`;
    }

    function confirmToAsset(no_unit){
        OptimaPro.showConfirmDialog({
            title: 'Konfirmasi Unit',
            message: 'Jadikan unit ini sebagai Asset (status RENTAL)? Tindakan tidak dapat dibatalkan.'
        }).then(res => {
            if(!res.isConfirmed) return;
            $.ajax({
                url: `<?= base_url('warehouse/unit-assets/confirm-to-asset/') ?>${no_unit}`,
                type: 'POST',
                data: { '<?= csrf_token() ?>':'<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(r){
                    if(r.success){
                        OptimaPro.showNotification(r.message,'success');
                        unitAssetsTable.ajax.reload(null,false);
                    } else {
                        OptimaPro.showNotification(r.message||'Gagal konfirmasi','error');
                    }
                },
                error: function(){
                    OptimaPro.showNotification('Server error saat konfirmasi','error');
                }
            });
        });
    }

    function deleteUnitAsset(no_unit) {
        OptimaPro.showConfirmDialog({
            title: 'Delete Unit Asset',
            message: `Are you sure you want to permanently delete unit asset ${no_unit}? This action cannot be undone.`,
            type: 'danger',
            confirmText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('warehouse/unit-assets/delete/') ?>${no_unit}`,
                    type: 'POST',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            OptimaPro.showNotification(response.message, 'success');
                            unitAssetsTable.ajax.reload(null, false); // Reload table without resetting pagination
                        } else {
                            OptimaPro.showNotification(response.message || 'Failed to delete unit asset.', 'error');
                        }
                    },
                    error: function(xhr) {
                        // Gunakan tanda '+' untuk menggabungkan string di JavaScript
                        const errorMsg = 'An error occurred: ' + (xhr.responseJSON?.message || 'Please try again.');
                        OptimaPro.showNotification(errorMsg, 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>