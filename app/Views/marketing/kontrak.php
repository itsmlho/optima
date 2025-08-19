<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    .table-card, .card-stats { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
    .modal-header { background: linear-gradient(135deg, #e9ecef 0%, #e9ecef 100%); color: white; border-radius: 15px 15px 0 0; }
    .filter-card.active { 
        transform: translateY(-3px); 
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); 
        border: 2px solid #fff; 
    }
    .filter-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25); 
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Statistics Cards (Nilai akan diisi oleh JavaScript) -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-primary text-white h-100 filter-card" data-filter="all" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-total">0</h2><h6 class="card-title text-uppercase small">Total Kontrak</h6></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-success text-white h-100 filter-card" data-filter="Aktif" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-active">0</h2><h6 class="card-title text-uppercase small">Kontrak Aktif</h6></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-warning text-white h-100 filter-card" data-filter="expiring" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-expiring">0</h2><h6 class="card-title text-uppercase small">Akan Berakhir</h6></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-secondary text-white h-100 filter-card" data-filter="Berakhir" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-expired">0</h2><h6 class="card-title text-uppercase small">Telah Berakhir</h6></div></div></div>
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
                        <th>No. PO</th>
                        <th>Nama Perusahaan</th>
                        <th>Periode Kontrak</th>
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
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Perusahaan*</label><input type="text" class="form-control" name="client_name" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama PIC</label><input type="text" class="form-control" name="pic"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Kontak PIC</label><input type="text" class="form-control" name="kontak"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Lokasi</label><input type="text" class="form-control" name="lokasi"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai Kontrak*</label><input type="date" class="form-control" name="start_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Berakhir Kontrak*</label><input type="date" class="form-control" name="end_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nilai Kontrak (Rp)*</label><input type="number" class="form-control" name="contract_value" required></div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Catatan*</label>
                            <textarea class="form-control" name="catatan" rows="3" required placeholder="Contoh: Catatan khusus kontrak, syarat tambahan, atau informasi penting lainnya."></textarea>
                        </div>
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
    
    // Global variable for current filter
    let currentKontrakFilter = 'all';
    
    // Function to apply filter and reload DataTable
    function applyKontrakFilter(filter) {
        currentKontrakFilter = filter;
        
        // Update active card styling
        $('.filter-card').removeClass('active');
        $(`[data-filter="${filter}"]`).addClass('active');
        
        // Reload DataTable with new filter
        contractsTable.ajax.reload();
    }
    
    const contractsTable = $('#contractsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('marketing/kontrak/getDataTable') ?>', // Updated endpoint
            type: 'POST',
            data: function(d) {
                // Add CSRF token to the data
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                
                // Add current filter to the data
                d.statusFilter = currentKontrakFilter;
                
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
            { data: 'contract_number', name: 'no_kontrak' },
            { data: 'po', name: 'no_po_marketing' },
            { data: 'client_name', name: 'pelanggan' },
            { data: 'period', name: 'tanggal_mulai' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']], // Changed to first column (contract_number)
        drawCallback: function(settings) {
            console.log('DataTable draw callback executed');
            const json = this.api().ajax.json();
            console.log('Draw callback - JSON data:', json);
            if(json && json.data && json.data.length===0){
                console.log('No rows rendered. If expected rows exist in DB, check controller Marketing::getDataTable and DB connection.');
            }
            // Make first column (No. Kontrak) clickable to open contract detail modal
            try {
                const api = this.api();
                api.rows({ page: 'current' }).every(function(rowIdx){
                    const rowData = this.data();
                    const node = this.node();
                    if (!rowData) return;
                    const $cell = $(node).find('td').eq(0);
                    if ($cell && $cell.length && !$cell.find('a.contract-link').length) {
                        // Extract text from the HTML content for display
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = rowData.contract_number || '';
                        const contractText = tempDiv.textContent || tempDiv.innerText || '';
                        
                        $cell.html(`<a href="#" class="contract-link text-decoration-none" onclick="openContractDetail(${rowData.id});return false;"><strong>${contractText}</strong></a>`);
                    }
                });
            } catch(e){ console.warn('Failed to attach contract links', e); }
        }
    });

    // Add filter card click listeners
    $('.filter-card').on('click', function() {
        const filter = $(this).data('filter');
        applyKontrakFilter(filter);
    });
    
    // Set default active filter (all)
    $('[data-filter="all"]').addClass('active');

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
            data: (function(form){
                let payload = $(form).serialize();
                // add CSRF
                payload += '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>';
                // on create, backend requires status; set default to PENDING without showing a field
                if (!isEdit) payload += '&status=' + encodeURIComponent('Pending');
                return payload;
            })(this),
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

// Open contract detail modal (AJAX) similar to SPK detail view
function openContractDetail(id){
    console.log('Opening contract detail for ID:', id);
    
    const pdfBtn = document.getElementById('btnPrintContract');
    if (pdfBtn) { pdfBtn.href = `<?= base_url('marketing/kontrak/print/') ?>${id}`; }
    const body = document.getElementById('contractDetailBody');
    if (!body) {
        console.error('contractDetailBody element not found');
        return;
    }
    
    body.innerHTML = '<p class="text-muted">Memuat...</p>';
    
    const detailUrl = `<?= base_url('marketing/kontrak/detail/') ?>${id}`;
    console.log('Fetching from URL:', detailUrl);
    
    fetch(detailUrl)
        .then(r => {
            console.log('Response status:', r.status);
            return r.json();
        })
        .then(j => {
            console.log('Response data:', j);
            if (!j || !j.success) { 
                body.innerHTML = '<div class="text-danger">Gagal memuat detail kontrak: ' + (j.message || 'Unknown error') + '</div>'; 
                return; 
            }
            const d = j.data || {};
            const esc = (s)=>{ if(s===null||s===undefined||s==='') return '-'; return String(s).replaceAll('<','&lt;').replaceAll('>','&gt;'); };
            
            // Basic contract information
            let contractInfo = `
                <div class="row g-2">
                    <div class="col-6"><strong>No. Kontrak:</strong> ${esc(d.no_kontrak||d.contract_number)}</div>
                    <div class="col-6"><strong>No. PO:</strong> ${esc(d.no_po_marketing || '-')}</div>
                    <div class="col-6"><strong>Nama Perusahaan:</strong> ${esc(d.pelanggan||d.client_name)}</div>
                    <div class="col-6"><strong>Alamat / Lokasi:</strong> ${esc(d.lokasi || d.lokasi || '-')}</div>
                    <div class="col-6"><strong>PIC:</strong> ${esc(d.pic || '-')}</div>
                    <div class="col-6"><strong>Kontak:</strong> ${esc(d.kontak || '-')}</div>
                    <div class="col-6"><strong>Periode:</strong> ${esc(d.periode|| (d.tanggal_mulai? (d.tanggal_mulai + ' - ' + (d.tanggal_berakhir||'-')) : '-'))}</div>
                    <div class="col-6"><strong>Nilai:</strong> ${d.nilai_total ? 'Rp ' + new Intl.NumberFormat('id-ID').format(d.nilai_total) : '-'}</div>
                    <div class="col-6"><strong>Total Unit:</strong> <span id="contractUnitsTotal">-</span></div>
                    <div class="col-6"><strong>Status:</strong> ${esc(d.status)}</div>
                    <div class="col-12"><hr></div>
                    <div class="col-12"><strong>Catatan:</strong> ${esc(d.catatan || '-')}</div>
                    <div class="col-12"><hr></div>
                    <div class="col-12"><h6><strong>Unit yang Terkait:</strong></h6></div>
                    <div class="col-12" id="contractUnitsContainer">
                        <div class="text-muted">Memuat daftar unit...</div>
                    </div>
                </div>
            `;
            
            body.innerHTML = contractInfo;
            
            // Load units for this contract
            fetch(`<?= base_url('marketing/kontrak/units/') ?>${id}`)
                .then(response => response.json())
                .then(unitsData => {
                    console.log('Units data:', unitsData);
                    const unitsContainer = document.getElementById('contractUnitsContainer');
                    
                    if (unitsData.success && unitsData.data && unitsData.data.length > 0) {
                        // Update the total units count using server-derived total
                        const totalSpan = document.getElementById('contractUnitsTotal');
                        if (totalSpan) totalSpan.textContent = unitsData.total ?? unitsData.data.length;
                        let unitsTable = `
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Unit</th>
                                            <th>Merk</th>
                                            <th>Model</th>
                                            <th>Kapasitas</th>
                                            <th>Jenis Unit</th>
                                            <th>Departemen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        
                        unitsData.data.forEach(unit => {
                            unitsTable += `
                                <tr>
                                    <td>${esc(unit.no_unit)}</td>
                                    <td>${esc(unit.merk)}</td>
                                    <td>${esc(unit.model)}</td>
                                    <td>${esc(unit.kapasitas)}</td>
                                    <td>${esc(unit.jenis_unit)}</td>
                                    <td>${esc(unit.departemen)}</td>
                                </tr>
                            `;
                        });
                        
                        unitsTable += `
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted">Total: ${unitsData.total ?? unitsData.data.length} unit(s). Untuk detail lengkap unit, kunjungi halaman <a href="<?= base_url('marketing/list-unit/') ?>${id}" target="_blank">List Unit</a>.</small>
                        `;
                        
                        unitsContainer.innerHTML = unitsTable;
                    } else {
                        const totalSpan = document.getElementById('contractUnitsTotal');
                        if (totalSpan) totalSpan.textContent = '0';
                        unitsContainer.innerHTML = '<div class="text-muted"><em>Tidak ada unit yang terkait dengan kontrak ini.</em></div>';
                    }
                })
                .catch(unitsError => {
                    console.warn('Error loading units:', unitsError);
                    const unitsContainer = document.getElementById('contractUnitsContainer');
                    if (unitsContainer) {
                        unitsContainer.innerHTML = '<div class="text-warning"><em>Gagal memuat daftar unit.</em></div>';
                    }
                    const totalSpan = document.getElementById('contractUnitsTotal');
                    if (totalSpan) totalSpan.textContent = '-';
                });
            // show modal
            console.log('Showing modal...');
            new bootstrap.Modal(document.getElementById('contractDetailModal')).show();
        })
        .catch((error) => { 
            console.error('Fetch error:', error);
            if (body) body.innerHTML = '<div class="text-danger">Gagal memuat detail kontrak: ' + error.message + '</div>'; 
        });
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
                $('input[name="lokasi"]').val(contract.lokasi);
                $('input[name="start_date"]').val(contract.tanggal_mulai);
                $('input[name="end_date"]').val(contract.tanggal_berakhir);
                $('input[name="contract_value"]').val(contract.nilai_total);
                $('input[name="total_units"]').val(contract.total_units || 0);
                $('input[name="pic"]').val(contract.pic);
                $('input[name="kontak"]').val(contract.kontak);
                
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
<!-- Contract Detail Modal -->
<div class="modal fade" id="contractDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header"><h6 class="modal-title">Detail Kontrak</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div id="contractDetailBody"><p class="text-muted">Memuat...</p></div></div>
            <div class="modal-footer">
                <a class="btn btn-outline-secondary" id="btnPrintContract" href="#" target="_blank" rel="noopener">Print PDF</a>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
