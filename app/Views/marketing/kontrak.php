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
                <div>
                    <h5 class="modal-title">Tambah Kontrak Baru</h5>
                    <small class="text-muted">Langkah 1: Informasi Dasar Kontrak</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Alur Pembuatan Kontrak Baru
                        </h6>
                        <ol class="mb-0 ps-3">
                            <li><strong>Langkah 1:</strong> Isi informasi dasar kontrak (form ini)</li>
                            <li><strong>Langkah 2:</strong> Tambahkan spesifikasi unit yang dibutuhkan</li>
                            <li><strong>Langkah 3:</strong> Buat SPK untuk mengalokasikan unit dari inventory</li>
                        </ol>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Tips:</strong> Nilai kontrak dan total unit akan dihitung otomatis berdasarkan spesifikasi yang ditambahkan.
                        </small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">No. Kontrak*</label><input type="text" class="form-control" name="contract_number" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">No. PO Klien</label><input type="text" class="form-control" name="po_number"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Perusahaan*</label><input type="text" class="form-control" name="client_name" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama PIC</label><input type="text" class="form-control" name="pic"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Kontak PIC</label><input type="text" class="form-control" name="kontak"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Lokasi/Alamat</label><input type="text" class="form-control" name="lokasi"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai*</label><input type="date" class="form-control" name="start_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Berakhir*</label><input type="date" class="form-control" name="end_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Jenis Sewa</label>
                            <select class="form-select" name="jenis_sewa">
                                <option value="BULANAN" selected>Bulanan</option>
                                <option value="HARIAN">Harian</option>
                            </select>
                        </div>
                        <div class="col-md-6"></div> <!-- Empty space for alignment -->
                        <div class="col-12 mb-3"><label class="form-label">Catatan</label><textarea class="form-control" name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="submit_action" id="submitAction" value="save_and_spec">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnSaveAndSpec" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan & Lanjut ke Spesifikasi
                    </button>
                    <button type="button" id="btnSaveOnly" class="btn btn-outline-primary">Simpan Kontrak</button>
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
        },
        formatCurrency: function(amount) {
            return new Intl.NumberFormat('id-ID').format(amount || 0);
        }
    };
}

// Helper function for number formatting
function formatNumber(num) {
    try {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    } catch (error) {
        console.warn('formatNumber error:', error);
        return (num || 0).toString();
    }
}
$(document).ready(function() {
    console.log('Initializing DataTable...');
    console.log('AJAX URL:', '<?= base_url('marketing/kontrak/getDataTable') ?>');
    
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

    // Primary action buttons
    $('#btnSaveAndSpec').on('click', function() {
        $('#submitAction').val('save_and_spec');
        $('#addContractForm').trigger('submit');
    });
    $('#btnSaveOnly').on('click', function() {
        $('#submitAction').val('save_only');
        $('#addContractForm').trigger('submit');
    });

    $('#addContractForm').on('submit', function(e) {
        e.preventDefault();
        
        const contractId = $(this).data('contract-id');
        const isEdit = contractId ? true : false;
        const action = $('#submitAction').val();
        const url = isEdit ? 
            `<?= base_url('marketing/kontrak/update/') ?>${contractId}` : 
            '<?= base_url('marketing/kontrak/store') ?>';
        
        // Disable buttons to prevent double submit
        const $btnSaveAndSpec = $('#btnSaveAndSpec');
        const $btnSaveOnly = $('#btnSaveOnly');
        if (!$btnSaveAndSpec.data('orig-html')) $btnSaveAndSpec.data('orig-html', $btnSaveAndSpec.html());
        if (!$btnSaveOnly.data('orig-html')) $btnSaveOnly.data('orig-html', $btnSaveOnly.html());
        if (action === 'save_only') {
            $btnSaveOnly.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
            $btnSaveAndSpec.prop('disabled', true);
        } else {
            $btnSaveAndSpec.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
            $btnSaveOnly.prop('disabled', true);
        }

    $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>',
            dataType: 'json',
        success: function(response) {
                if (response.success) {
            $('#addContractModal').modal('hide');
            if (isEdit) {
                        // Jika edit, reload table dan show success message
                        OptimaPro.showNotification(response.message, 'success');
                        contractsTable.ajax.reload();
                        
                        // Reset form after success
                        $('#addContractForm')[0].reset();
                        $('#addContractForm').removeData('contract-id');
                        $('#addContractForm').attr('action', 'store');
                        $('#addContractModal .modal-title').text('Tambah Kontrak Baru');
                    } else {
                        // Create baru: arahkan sesuai submit_action
                        OptimaPro.showNotification(response.message, 'success');
                        contractsTable.ajax.reload();
                        const goToSpec = ($('#submitAction').val() === 'save_and_spec');
                        const newId = response.data && response.data.id ? response.data.id : null;
                        // Reset form
                        $('#addContractForm')[0].reset();
                        if (goToSpec && newId) {
                            // Buka modal detail kontrak dan tab spesifikasi
                            openContractDetail(newId);
                            setTimeout(() => {
                                const spekTab = document.querySelector('#contractDetailTabs button[data-bs-target="#spesifikasi-content"]');
                                if (spekTab) spekTab.click();
                            }, 400);
                        }
                    }
                } else {
                    // Duplicate contract handling
                    if (response.duplicate && response.existing_id) {
                        OptimaPro.showConfirmDialog({
                            title: 'No. Kontrak sudah ada',
                            message: 'Buka kontrak yang sudah ada dan lanjutkan dari sana?'
                        }).then(res => {
                            if (res.isConfirmed) {
                                openContractDetail(response.existing_id);
                                setTimeout(() => {
                                    const spekTab = document.querySelector('#contractDetailTabs button[data-bs-target="#spesifikasi-content"]');
                                    if (spekTab) spekTab.click();
                                }, 400);
                            }
                        });
                        return;
                    }

                    let msg = response.message || 'Terjadi kesalahan.';
                    if (response.errors && typeof response.errors === 'object') {
                        msg = 'Validasi gagal: ' + Object.values(response.errors).join(', ');
                    }
                    OptimaPro.showNotification(msg, 'danger');
                }
            },
            error: function(xhr, status, error) {
                let msg = 'Tidak dapat terhubung ke server.';
                if (xhr && xhr.responseText) {
                    try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch(e) {}
                }
                OptimaPro.showNotification(msg, 'danger');
            }
            ,
            complete: function() {
                // Re-enable buttons and restore labels
                $btnSaveAndSpec.prop('disabled', false).html($btnSaveAndSpec.data('orig-html'));
                $btnSaveOnly.prop('disabled', false).html($btnSaveOnly.data('orig-html'));
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

    // ===== SPESIFIKASI FORM HANDLER =====
    
    // Handle add spesifikasi form submission
    $('#addSpesifikasiForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Validate required fields on frontend
        const kontrakId = $('#spekKontrakId').val();
        const jumlahDibutuhkan = $('input[name="jumlah_dibutuhkan"]').val();
        const departemenId = $('select[name="departemen_id"]').val();
        const tipeUnitId = $('select[name="tipe_unit_id"]').val();
        const jenisUnit = $('select[name="tipe_jenis"]').val();
        
        // Frontend validation check
    
        if (!kontrakId) {
            OptimaPro.showNotification('Kontrak ID tidak ditemukan. Silakan buka detail kontrak terlebih dahulu.', 'error');
            return;
        }
        
        if (!jumlahDibutuhkan || jumlahDibutuhkan <= 0) {
            OptimaPro.showNotification('Jumlah unit dibutuhkan harus diisi dengan nilai lebih dari 0.', 'error');
            return;
        }
        
        if (!departemenId) {
            OptimaPro.showNotification('Departemen harus dipilih.', 'error');
            return;
        }
        
        if (!tipeUnitId) {
            OptimaPro.showNotification('Tipe Unit harus dipilih.', 'error');
            return;
        }
        
        if (!jenisUnit) {
            OptimaPro.showNotification('Jenis Unit harus dipilih.', 'error');
            return;
        }
        
        // Get form data with proper serialization
        const formData = $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>';
        

        
        $.ajax({
            url: '<?= base_url('marketing/kontrak/add-spesifikasi') ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',

            success: function(response) {
                if (response.success) {
                    OptimaPro.showNotification(response.message, 'success');
                    $('#addSpesifikasiModal').modal('hide');
                    
                    // Reset form
                    $('#addSpesifikasiForm')[0].reset();
                    
                    // Reload spesifikasi tab
                    const contractId = $('#spekKontrakId').val();
                    if (contractId) {
                        loadContractSpesifikasi(contractId);
                    }
                    

                } else {
                    // Show detailed error information
                    
                    let errorMsg = response.message || 'Terjadi kesalahan saat menyimpan spesifikasi';
                    
                    // Show validation errors if any
                    if (response.validation_errors && Object.keys(response.validation_errors).length > 0) {
                        errorMsg += '\nValidation errors: ' + JSON.stringify(response.validation_errors);
                    }
                    
                    // Show database errors if any
                    if (response.db_error && response.db_error.message) {
                        errorMsg += '\nDatabase error: ' + response.db_error.message;
                    }
                    
                    OptimaPro.showNotification(errorMsg, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                
                let errorMessage = 'Gagal menambah spesifikasi: ' + error;
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.warn('Could not parse error response');
                    }
                }
                
                OptimaPro.showNotification(errorMessage, 'error');
            }
        });
    }); // End spesifikasi form handler

    // Note: Only one submit handler for addSpesifikasiForm to prevent duplicates

});

function viewContractUnits(contractId) {
    // Arahkan ke halaman list_unit.php dengan membawa ID kontrak
    window.location.href = `<?= base_url('marketing/list-unit/') ?>${contractId}`;
}

// Open contract detail modal with multi-specification support
function openContractDetail(id){
    console.log('Opening contract detail for ID:', id);
    
    const pdfBtn = document.getElementById('btnPrintContract');
    if (pdfBtn) { pdfBtn.href = `<?= base_url('marketing/kontrak/print/') ?>${id}`; }
    
    // Store contract ID globally for other functions
    window.currentKontrakId = id;
    console.log('Set window.currentKontrakId to:', window.currentKontrakId);
    
    // Reset tabs to info tab
    const infoTab = document.querySelector('#info-tab');
    if (infoTab) {
        console.log('Clicking info tab to reset');
        infoTab.click();
    } else {
        console.error('Info tab not found!');
    }
    
    // Load basic contract info
    loadContractInfo(id);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('contractDetailModal'));
    console.log('Showing modal...');
    modal.show();
    
    // Setup tab event handler after modal is shown
    modal._element.addEventListener('shown.bs.modal', function () {
        console.log('Modal shown, setting up tab event handlers...');
        setupTabEventHandlers();
        
        // Check if tab event handler is working after modal is shown
        setTimeout(() => {
            const tabButtons = document.querySelectorAll('#contractDetailTabs button[data-bs-toggle="tab"]');
            console.log('Found tab buttons:', tabButtons.length);
            tabButtons.forEach((btn, index) => {
                console.log(`Tab ${index}:`, btn.getAttribute('data-bs-target'));
            });
        }, 100);
    });
}

// Setup tab event handlers
function setupTabEventHandlers() {
    console.log('Setting up tab event handler...');
    
    // Remove existing event handlers first
    $('#contractDetailTabs button[data-bs-toggle="tab"]').off('shown.bs.tab');
    
    // Add new event handler
    $('#contractDetailTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        console.log('Tab event triggered!');
        const target = e.target.getAttribute('data-bs-target');
        const contractId = window.currentKontrakId;
        
        console.log('Tab changed to:', target, 'Contract ID:', contractId);
        
        if (target === '#spesifikasi-content' && contractId) {
            console.log('Loading spesifikasi for contract:', contractId);
            loadContractSpesifikasi(contractId);
        } else if (target === '#units-content' && contractId) {
            console.log('Loading units for contract:', contractId);
            loadContractUnits(contractId);
        }
    });
    
    console.log('Tab event handlers setup complete');
}

function loadContractInfo(id) {
    const body = document.getElementById('contractDetailBody');
    if (!body) return;
    
    body.innerHTML = '<p class="text-muted">Memuat...</p>';
    
    fetch(`<?= base_url('marketing/kontrak/detail/') ?>${id}`)
        .then(response => response.json())
        .then(j => {
            if (!j.success) {
                body.innerHTML = '<div class="text-danger">Gagal memuat detail kontrak</div>';
                return;
            }
            
            const d = j.data || {};
            body.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6"><strong>No. Kontrak:</strong> ${d.no_kontrak || '-'}</div>
                    <div class="col-md-6"><strong>No. PO Marketing:</strong> ${d.no_po_marketing || '-'}</div>
                    <div class="col-md-6"><strong>Pelanggan:</strong> ${d.pelanggan || '-'}</div>
                    <div class="col-md-6"><strong>Lokasi:</strong> ${d.lokasi || '-'}</div>
                    <div class="col-md-6"><strong>PIC:</strong> ${d.pic || '-'}</div>
                    <div class="col-md-6"><strong>Kontak:</strong> ${d.kontak || '-'}</div>
                    <div class="col-md-6"><strong>Jenis Sewa:</strong> <span class="badge bg-info">${d.jenis_sewa || 'BULANAN'}</span></div>
                    <div class="col-md-6"><strong>Status:</strong> <span class="badge bg-${d.status === 'Aktif' ? 'success' : d.status === 'Pending' ? 'warning' : 'secondary'}">${d.status || '-'}</span></div>
                    <div class="col-md-6"><strong>Tanggal Mulai:</strong> ${d.tanggal_mulai || '-'}</div>
                    <div class="col-md-6"><strong>Tanggal Berakhir:</strong> ${d.tanggal_berakhir || '-'}</div>
                    <div class="col-md-6"><strong>Total Unit:</strong> <span class="fw-bold text-primary">${d.total_units || 0}</span></div>
                    <div class="col-md-6"><strong>Nilai Total:</strong> <span class="fw-bold text-success">Rp ${formatNumber(d.nilai_total || 0)}</span></div>
                    <div class="col-md-6"><strong>Dibuat Oleh:</strong> ${d.dibuat_oleh || '-'}</div>
                    <div class="col-md-6"><strong>Dibuat Pada:</strong> ${d.dibuat_pada || '-'}</div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading contract info:', error);
            body.innerHTML = '<div class="text-danger">Gagal memuat detail kontrak</div>';
        });
}

function loadContractSpesifikasi(kontrakId) {
    console.log('loadContractSpesifikasi called with kontrakId:', kontrakId);
    const container = document.getElementById('spesifikasiList');
    if (!container) {
        console.error('spesifikasiList container not found!');
        return;
    }
    
    console.log('Container found, setting loading message...');
    container.innerHTML = '<p class="text-muted">Memuat spesifikasi...</p>';
    
    const url = `<?= base_url('marketing/kontrak/spesifikasi/') ?>${kontrakId}`;
    console.log('Fetching URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status, response.statusText);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(j => {
            console.log('Spesifikasi response:', j); // Debug log
            if (!j.success) {
                console.error('API returned error:', j.message);
                container.innerHTML = '<div class="text-danger">Gagal memuat spesifikasi: ' + (j.message || 'Unknown error') + '</div>';
                return;
            }
            
            const spesifikasi = j.data || [];
            const summary = j.summary || {};
            
            console.log('Processing spesifikasi data, count:', spesifikasi.length);
            console.log('Summary data:', summary);
            
            // Update tab counter
            const spekCountElement = document.getElementById('spekCount');
            if (spekCountElement) {
                spekCountElement.textContent = spesifikasi.length;
                console.log('Updated tab counter to:', spesifikasi.length);
            } else {
                console.warn('spekCount element not found');
            }
            
            if (spesifikasi.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Belum Ada Spesifikasi</h5>
                        <p class="text-muted mb-4">
                            Kontrak ini belum memiliki spesifikasi unit yang dibutuhkan.<br>
                            Tambahkan spesifikasi untuk menentukan jenis unit, jumlah, dan harga yang diperlukan.
                        </p>
                        <div class="d-flex flex-column align-items-center gap-2">
                            <button class="btn btn-primary btn-lg" onclick="openAddSpesifikasiModal()">
                                <i class="fas fa-plus me-2"></i>Tambah Spesifikasi Pertama
                            </button>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Setelah menambah spesifikasi, nilai kontrak akan dihitung otomatis
                            </small>
                        </div>
                    </div>
                `;
                return;
            }
            
            let html = `
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center py-2">
                                <div class="fw-bold text-primary">${summary.total_spesifikasi || 0}</div>
                                <small>Spesifikasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center py-2">
                                <div class="fw-bold text-success">${summary.total_unit_dibutuhkan || 0}</div>
                                <small>Total Unit</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center py-2">
                                <div class="fw-bold text-warning">${formatNumber((summary.total_nilai_bulanan || 0) + (summary.total_nilai_harian || 0))}</div>
                                <small>Total Nilai</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            spesifikasi.forEach((spek, index) => {
                console.log(`Processing spek ${index + 1}:`, spek.spek_kode);
                
                try {
                    const progress = spek.jumlah_dibutuhkan > 0 ? 
                        Math.round((spek.jumlah_tersedia / spek.jumlah_dibutuhkan) * 100) : 0;
                    const progressClass = progress === 100 ? 'success' : progress > 0 ? 'warning' : 'secondary';
                
                html += `
                    <div class="card mb-3" data-spek-id="${spek.id}" data-jumlah-dibutuhkan="${spek.jumlah_dibutuhkan}" data-jumlah-tersedia="${spek.jumlah_tersedia}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <span class="badge bg-primary me-2">${spek.spek_kode}</span>
                                ${spek.catatan_spek || 'Spesifikasi ' + spek.spek_kode}
                            </h6>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editSpesifikasi(${spek.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSpesifikasi(${spek.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Total Unit</small>
                                    <div class="fw-bold">${spek.jumlah_dibutuhkan}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Harga Bulanan</small>
                                    <div class="fw-bold text-success">Rp ${formatNumber(spek.harga_per_unit_bulanan || 0)}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Harga Harian</small>
                                    <div class="fw-bold text-info">Rp ${formatNumber(spek.harga_per_unit_harian || 0)}</div>
                                </div>
                            </div>
                            
                            <div class="row g-2 mt-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Departemen</small>
                                    <div>${spek.nama_departemen || '-'}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Tipe/Jenis</small>
                                    <div>${spek.tipe_unit_name || spek.tipe_jenis || '-'}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Kapasitas</small>
                                    <div>${spek.kapasitas_name || '-'}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Merk/Model</small>
                                    <div>${spek.merk_unit || '-'} ${spek.model_unit || ''}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Attachment</small>
                                    <div>${spek.attachment_tipe || '-'} ${spek.attachment_merk || ''}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Baterai/Charger</small>
                                    <div>${spek.jenis_baterai || '-'} / ${spek.charger_name || '-'}</div>
                                </div>
                            </div>
                            
                            ${spek.aksesoris && spek.aksesoris.length > 0 ? `
                                <div class="row g-2 mt-2">
                                    <div class="col-12">
                                        <small class="text-muted">Aksesoris</small>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            ${spek.aksesoris.map(acc => `<span class="badge bg-secondary text-white">${acc}</span>`).join('')}
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${spek.jumlah_tersedia < spek.jumlah_dibutuhkan ? `
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-primary" onclick="openSpkModalFromKontrak(${spek.id})">
                                        <i class="fas fa-file-alt me-1"></i>Buat SPK
                                    </button>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>SPK yang dibuat: ${spek.jumlah_spk || 0}
                                    </small>
                                </div>
                            ` : `
                                <div class="mt-2">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>SPK Lengkap (${spek.jumlah_spk || 0} SPK dibuat)
                                    </span>
                                </div>
                            `}
                        </div>
                    </div>
                `;
                } catch (error) {
                    console.error(`Error processing spek ${index + 1}:`, error);
                }
            });
            
            console.log('Generated HTML length:', html.length);
            console.log('Setting container HTML...');
            
            try {
                container.innerHTML = html;
                console.log('Container HTML set successfully');
                console.log('Container children count:', container.children.length);
            } catch (error) {
                console.error('Error setting container HTML:', error);
                container.innerHTML = '<div class="text-danger">Error displaying data: ' + error.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Error loading specifications:', error);
            container.innerHTML = '<div class="text-danger">Gagal memuat spesifikasi: ' + error.message + '</div>';
        });
}

// Single modal for creating SPK from kontrak detail
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('spkFromKontrakModal')) {
        const modalHtml = `
<div class="modal fade" id="spkFromKontrakModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Buat SPK</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="spkFromKontrakForm">
                <div class="modal-body">
                    <input type="hidden" name="kontrak_id" id="spkKontrakId">
                    <input type="hidden" name="kontrak_spesifikasi_id" id="spkSpesifikasiId">
                    <div class="mb-3">
                        <label class="form-label">Jenis SPK</label>
                        <select class="form-select" name="jenis_spk" required>
                            <option value="UNIT" selected>SPK Unit</option>
                            <option value="ATTACHMENT">SPK Attachment</option>
                            <option value="TUKAR">SPK Tukar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Plan</label>
                        <input type="date" class="form-control" name="delivery_plan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Unit <small class="text-muted" id="jumlahUnitHint"></small></label>
                        <input type="number" class="form-control" name="jumlah_unit" id="spkJumlahUnit" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Keterangan tambahan untuk SPK ini (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary" type="submit">Buat SPK</button>
                </div>
            </form>
        </div>
    </div>
</div>`;

        const wrapper = document.createElement('div');
        wrapper.innerHTML = modalHtml;
        document.body.appendChild(wrapper);

        // Expose global opener so per-spec buttons can call it
        window.openSpkModalFromKontrak = function(spekId) {
            const kontrakId = window.currentKontrakId || document.getElementById('currentKontrakId')?.value;
            if (!kontrakId) {
                OptimaPro.showNotification('Kontrak belum dipilih. Buka detail kontrak terlebih dahulu.', 'error');
                return;
            }
            document.getElementById('spkKontrakId').value = kontrakId;
            document.getElementById('spkSpesifikasiId').value = spekId;
            // Reset other fields
            document.querySelector('#spkFromKontrakForm [name="jenis_spk"]').value = 'UNIT';
            document.querySelector('#spkFromKontrakForm [name="delivery_plan"]').value = '';
            document.querySelector('#spkFromKontrakForm [name="jumlah_unit"]').value = '';
            document.querySelector('#spkFromKontrakForm [name="catatan"]').value = '';
            // Compute available units from current spesifikasi list in DOM if present
            try {
                let available = 0;
                const card = document.querySelector(`[data-spek-id="${spekId}"]`);
                if (card) {
                    const need = Number(card.getAttribute('data-jumlah-dibutuhkan') || '0');
                    const have = Number(card.getAttribute('data-jumlah-tersedia') || '0');
                    available = Math.max(0, need - have);
                }
                const jumlahInput = document.getElementById('spkJumlahUnit');
                const hint = document.getElementById('jumlahUnitHint');
                const formEl = document.getElementById('spkFromKontrakForm');
                if (jumlahInput) {
                    if (available > 0) {
                        jumlahInput.setAttribute('max', String(available));
                        jumlahInput.setAttribute('placeholder', 'Maks ' + available);
                    } else {
                        jumlahInput.removeAttribute('max');
                        jumlahInput.removeAttribute('placeholder');
                    }
                }
                if (hint) {
                    hint.textContent = available > 0 ? `(maks ${available})` : '';
                }
                if (formEl) {
                    formEl.dataset.availableUnits = String(available);
                }
            } catch(e) { console.warn('Failed to set available units hint', e); }
            const modal = new bootstrap.Modal(document.getElementById('spkFromKontrakModal'));
            modal.show();
        };

        // Attach submit handler
        const spkForm = document.getElementById('spkFromKontrakForm');
        if (spkForm) {
            spkForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(spkForm);
                // Add CSRF
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                if (!formData.get('delivery_plan') || !formData.get('jumlah_unit')) {
                    OptimaPro.showNotification('Lengkapi semua field wajib.', 'error');
                    return;
                }
                // Client-side limit based on available units
                const available = Number(spkForm.dataset.availableUnits || '0');
                const jumlah = Number(formData.get('jumlah_unit'));
                if (available > 0 && jumlah > available) {
                    OptimaPro.showNotification('Jumlah unit melebihi yang dibutuhkan. Maksimal: ' + available, 'error');
                    return;
                }
                fetch('<?= base_url('marketing/spk/create') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        OptimaPro.showNotification('SPK berhasil dibuat!', 'success');
                        const modalEl = document.getElementById('spkFromKontrakModal');
                        bootstrap.Modal.getInstance(modalEl)?.hide();
                        if (window.currentKontrakId) loadContractSpesifikasi(window.currentKontrakId);
                    } else {
                        OptimaPro.showNotification(res.message || 'Gagal membuat SPK.', 'error');
                    }
                })
                .catch(err => {
                    OptimaPro.showNotification('Gagal membuat SPK: ' + err, 'error');
                });
            });
        }
    }
});

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

// Tab event handlers for lazy loading
// Note: Tab event handlers are now setup in setupTabEventHandlers() function
// which is called when modal is shown to ensure DOM elements are available

// Load contract units for units tab
function loadContractUnits(contractId) {
    $.ajax({
        url: `<?= base_url('marketing/kontrak/units/') ?>${contractId}`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let unitsHtml = '';
                if (response.data.length > 0) {
                    unitsHtml = '<div class="table-responsive">';
                    unitsHtml += '<table class="table table-sm table-striped">';
                    unitsHtml += '<thead><tr><th>No Unit</th><th>Spesifikasi</th><th>Merk/Model</th><th>Departemen</th><th>Status Sewa</th><th>Harga</th></tr></thead>';
                    unitsHtml += '<tbody>';
                    
                    response.data.forEach(unit => {
                        const harga = unit.jenis_sewa === 'harian' ? 
                            `Rp ${OptimaPro.formatCurrency(unit.harga_sewa_harian)}/hari` : 
                            `Rp ${OptimaPro.formatCurrency(unit.harga_sewa_bulanan)}/bulan`;
                        
                        unitsHtml += `<tr>
                            <td>${unit.nomor_unit}</td>
                            <td>${unit.spek_kode || '-'}</td>
                            <td>${unit.merk_unit || ''} ${unit.model_unit || ''}</td>
                            <td>${unit.departemen_nama || '-'}</td>
                            <td><span class="badge bg-success">Disewa</span></td>
                            <td>${harga}</td>
                        </tr>`;
                    });
                    
                    unitsHtml += '</tbody></table></div>';
                } else {
                    unitsHtml = '<div class="alert alert-info">Belum ada unit yang di-assign ke kontrak ini.</div>';
                }
                
                $('#unitsList').html(unitsHtml);
                $('#unitsCount').text(response.data.length);
            } else {
                $('#unitsList').html('<div class="alert alert-danger">Gagal memuat unit: ' + response.message + '</div>');
            }
        },
        error: function() {
            $('#unitsList').html('<div class="alert alert-danger">Gagal memuat unit.</div>');
        }
    });
}

// Open add spesifikasi modal
function openAddSpesifikasiModal() {
    const contractId = window.currentKontrakId;
    if (!contractId) {
        OptimaPro.showNotification('ID kontrak tidak ditemukan. Silakan buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    
    console.log('Opening add spesifikasi modal for contract ID:', contractId);
    
    // Reset form
    $('#addSpesifikasiForm')[0].reset();
    $('#spekKontrakId').val(contractId);
    
    console.log('Kontrak ID set to form:', $('#spekKontrakId').val()); // Debug
    
    // Reset dropdown states
    $('#spekCharger').prop('disabled', false);
    $('#spekJenisBaterai').prop('disabled', false);
    
    // Load dropdowns
    loadSpesifikasiDropdowns();
    
    // Show modal
    $('#addSpesifikasiModal').modal('show');
}

// Load dropdown options for spesifikasi form
function loadSpesifikasiDropdowns() {
    // Helper function to fill select options
    function fillSelect(selector, items, nameField = 'name') {
        const selectElement = document.querySelector(selector);
        if (!selectElement) return;
        
        const placeholder = selector.includes('Departemen') ? '-- Pilih Departemen --' :
                           selector.includes('TipeUnit') ? '-- Pilih Tipe Unit --' :
                           selector.includes('JenisUnit') ? '-- Pilih Jenis Unit --' :
                           selector.includes('Kapasitas') ? '-- Pilih Kapasitas --' :
                           selector.includes('Charger') ? '-- Pilih Charger --' :
                           selector.includes('Mast') ? '-- Pilih Mast --' :
                           selector.includes('Ban') ? '-- Pilih Ban --' :
                           selector.includes('Roda') ? '-- Pilih Roda --' :
                           selector.includes('Valve') ? '-- Pilih Valve --' : '-- Pilih --';
        
        let options = `<option value="">${placeholder}</option>`;
        if (items && items.length > 0) {
            items.forEach(item => {
                const displayName = item[nameField] || item.nama || item.kapasitas || item.jenis || item.tipe || item.id;
                options += `<option value="${item.id}">${displayName}</option>`;
            });
        }
        selectElement.innerHTML = options;
    }
    
    // Load initial dropdowns
    const initialTypes = [
        { type: 'departemen', selector: '#spekDepartemen' },
        { type: 'kapasitas', selector: '#spekKapasitas' },
        { type: 'merk_unit', selector: '#spekMerkUnit' },
        { type: 'jenis_baterai', selector: '#spekJenisBaterai' },
        { type: 'attachment_tipe', selector: '#spekAttachmentTipe' },
        { type: 'mast', selector: '#spekMast' },
        { type: 'ban', selector: '#spekBan' },
        { type: 'roda', selector: '#spekRoda' },
        { type: 'valve', selector: '#spekValve' }
    ];
    
    initialTypes.forEach(spec => {
        fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=${spec.type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fillSelect(spec.selector, data.data || []);
                } else {
                    console.warn(`Failed to load ${spec.type}:`, data.message);
                }
            })
            .catch(error => {
                console.error(`Error loading ${spec.type}:`, error);
            });
    });
    
    // Load departemen first, then set up cascading dropdowns
    fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=departemen`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fillSelect('#spekDepartemen', data.data || []);
                setupCascadingDropdowns();
            }
        })
        .catch(error => {
            console.error('Error loading departemen:', error);
        });
}

// Setup cascading dropdown behavior
function setupCascadingDropdowns() {
    // Departemen change - affects tipe_unit, baterai, charger
    $('#spekDepartemen').on('change', function() {
        const departemenId = this.value;
        const departemenText = this.options[this.selectedIndex].text;
        
        // Clear dependent dropdowns
        $('#spekTipeUnit').html('<option value="">-- Pilih Tipe Unit --</option>');
        $('#spekJenisUnit').html('<option value="">-- Pilih Jenis Unit --</option>');
        $('#spekCharger').html('<option value="">-- Pilih Charger --</option>');
        $('#spekJenisBaterai').html('<option value="">-- Pilih Jenis Baterai --</option>');
        
        if (departemenId) {
            // Load tipe_unit (DISTINCT tipe only)
            fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=tipe_unit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fillSelectOptions('#spekTipeUnit', data.data || []);
                    }
                });
            
            // Check if electric department for baterai & charger
            const isElectric = departemenText.toLowerCase().includes('electric') || 
                              departemenText.toLowerCase().includes('listrik');
            
            if (isElectric) {
                // Load charger for electric units
                fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=charger&departemen_id=${departemenId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fillSelectOptions('#spekCharger', data.data || []);
                        }
                    });
                
                // Load jenis baterai for electric units
                fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_baterai`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fillSelectOptions('#spekJenisBaterai', data.data || []);
                        }
                    });
                    
                // Enable baterai and charger fields
                $('#spekCharger').prop('disabled', false);
                $('#spekJenisBaterai').prop('disabled', false);
            } else {
                // Disable and clear baterai and charger for non-electric
                $('#spekCharger').prop('disabled', true);
                $('#spekJenisBaterai').prop('disabled', true);
                $('#spekCharger').html('<option value="">-- Tidak tersedia untuk unit non-electric --</option>');
                $('#spekJenisBaterai').html('<option value="">-- Tidak tersedia untuk unit non-electric --</option>');
            }
        } else {
            // Reset all fields if no departemen selected
            $('#spekCharger').prop('disabled', false);
            $('#spekJenisBaterai').prop('disabled', false);
        }
    });
    
    // Tipe Unit change - affects jenis unit
    $('#spekTipeUnit').on('change', function() {
        const tipeUnitId = this.value;
        const tipeUnitName = this.options[this.selectedIndex].text;
        
        // Clear jenis unit
        $('#spekJenisUnit').html('<option value="">-- Pilih Jenis Unit --</option>');
        
        if (tipeUnitId && tipeUnitName !== '-- Pilih Tipe Unit --') {
            // Send tipe name instead of ID for backend filtering
            fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_unit&parent_tipe=${encodeURIComponent(tipeUnitName)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fillSelectOptions('#spekJenisUnit', data.data || []);
                    }
                })
                .catch(error => {
                    console.error('Error loading jenis unit:', error);
                });
        }
    });
}

// Helper function to fill select options
function fillSelectOptions(selector, items, nameField = 'name') {
    const selectElement = document.querySelector(selector);
    if (!selectElement) return;
    
    const currentValue = selectElement.value;
    const placeholder = selectElement.querySelector('option[value=""]')?.textContent || '-- Pilih --';
    
    let options = `<option value="">${placeholder}</option>`;
    if (items && items.length > 0) {
        items.forEach(item => {
            const displayName = item[nameField] || item.nama || item.kapasitas || item.jenis || item.tipe || item.id;
            const selected = item.id == currentValue ? 'selected' : '';
            options += `<option value="${item.id}" ${selected}>${displayName}</option>`;
        });
    }
    selectElement.innerHTML = options;
}

// ===== DUPLICATE BLOCK REMOVED - MOVED TO MAIN DOCUMENT.READY =====

// Debug functions removed - no longer needed

// Delete spesifikasi
function deleteSpesifikasi(spekId) {
    OptimaPro.showConfirmDialog({
        title: 'Konfirmasi Hapus',
        message: 'Apakah Anda yakin ingin menghapus spesifikasi ini?'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?= base_url('marketing/kontrak/delete-spesifikasi/') ?>${spekId}`,
                method: 'POST',
                data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        OptimaPro.showNotification(response.message, 'success');
                        
                        // Reload spesifikasi tab
                        const contractId = window.currentKontrakId;
                        loadContractSpesifikasi(contractId);
                    } else {
                        OptimaPro.showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    OptimaPro.showNotification('Gagal menghapus spesifikasi.', 'error');
                }
            });
        }
    });
}

// Create SPK for specific specification
function createSPKForSpec(spekId) {
    // Redirect to SPK creation page with pre-selected spesifikasi
    // Use runtime JS values: window.currentKontrakId should be set when viewing a contract
    const kontrakId = window.currentKontrakId || document.getElementById('currentKontrakId')?.value;
    if (!kontrakId) {
        console.error('createSPKForSpec: kontrak id not available');
        OptimaPro.showNotification('Kontrak belum dipilih. Buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    const base = '<?= rtrim(base_url(), "\/") ?>';
    window.location.href = `${base}/marketing/spk?kontrak_id=${encodeURIComponent(kontrakId)}&spesifikasi_id=${encodeURIComponent(spekId)}`;
}
</script>
<!-- Contract Detail Modal with Multi-Specification Support -->
<div class="modal fade" id="contractDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Detail Kontrak</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="contractDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-content" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Info Kontrak
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="spesifikasi-tab" data-bs-toggle="tab" data-bs-target="#spesifikasi-content" type="button" role="tab">
                            <i class="fas fa-cogs me-1"></i>Spesifikasi (<span id="spekCount">0</span>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="units-tab" data-bs-toggle="tab" data-bs-target="#units-content" type="button" role="tab">
                            <i class="fas fa-truck me-1"></i>Unit Terkait (<span id="unitsCount">0</span>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="contractDetailTabContent">
                    <!-- Info Kontrak Tab -->
                    <div class="tab-pane fade show active" id="info-content" role="tabpanel">
                        <div id="contractDetailBody">
                            <p class="text-muted">Memuat...</p>
                        </div>
                    </div>

                    <!-- Spesifikasi Tab -->
                    <div class="tab-pane fade" id="spesifikasi-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Spesifikasi Unit</h6>
                            <button class="btn btn-primary btn-sm" onclick="openAddSpesifikasiModal()">
                                <i class="fas fa-plus me-1"></i>Tambah Spesifikasi
                            </button>
                        </div>
                        
                        <div id="spesifikasiList">
                            <p class="text-muted">Memuat spesifikasi...</p>
                        </div>
                    </div>

                    <!-- Units Tab -->
                    <div class="tab-pane fade" id="units-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Unit yang Terkait (via SPK)</h6>
                            <a href="/marketing/spk" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-alt me-1"></i>Kelola SPK
                            </a>
                        </div>
                        
                        <div id="unitsList">
                            <p class="text-muted">Memuat unit...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-outline-secondary" id="btnPrintContract" href="#" target="_blank" rel="noopener">Print PDF</a>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Spesifikasi Modal -->
<div class="modal fade" id="addSpesifikasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Tambah Spesifikasi Unit</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSpesifikasiForm" method="post" action="javascript:void(0)">
                <div class="modal-body">
                    <input type="hidden" name="kontrak_id" id="spekKontrakId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Unit Dibutuhkan</label>
                            <input type="number" class="form-control" name="jumlah_dibutuhkan" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Catatan Spesifikasi</label>
                            <input type="text" class="form-control" name="catatan_spek" placeholder="Opsional">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Harga Sewa Bulanan</label>
                            <input type="number" class="form-control" name="harga_per_unit_bulanan" step="0.01" placeholder="Rp per unit per bulan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Sewa Harian</label>
                            <input type="number" class="form-control" name="harga_per_unit_harian" step="0.01" placeholder="Rp per unit per hari">
                        </div>
                        
                        <!-- Debug Test Button -->


                        <div class="col-12"><hr><h6>Spesifikasi Teknis</h6></div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Departemen <span class="text-danger">*</span></label>
                            <select class="form-select" name="departemen_id" id="spekDepartemen" required></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipe Unit <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_unit_id" id="spekTipeUnit" required>
                                <option value="">-- Pilih Tipe Unit --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Unit <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_jenis" id="spekJenisUnit" required>
                                <option value="">-- Pilih Jenis Unit --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Kapasitas</label>
                            <select class="form-select" name="kapasitas" id="spekKapasitas">
                                <option value="">-- Pilih Kapasitas --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Merk Unit</label>
                            <select class="form-select" name="merk_unit" id="spekMerkUnit">
                                <option value="">-- Pilih Merk --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Baterai</label>
                            <select class="form-select" name="jenis_baterai" id="spekJenisBaterai">
                                <option value="">-- Pilih Jenis Baterai --</option>
                            </select>
                            <small class="text-muted">Hanya tersedia untuk unit Electric</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Attachment Tipe</label>
                            <select class="form-select" name="attachment_tipe" id="spekAttachmentTipe"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Charger</label>
                            <select class="form-select" name="charger_id" id="spekCharger"></select>
                            <small class="text-muted">Hanya tersedia untuk unit Electric</small>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Mast</label>
                            <select class="form-select" name="mast_id" id="spekMast"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ban</label>
                            <select class="form-select" name="ban_id" id="spekBan"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Roda</label>
                            <select class="form-select" name="roda_id" id="spekRoda"></select>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Valve</label>
                            <select class="form-select" name="valve_id" id="spekValve"></select>
                        </div>
                        
                        <!-- Accessories Section -->
                        <div class="col-12"><hr><h6>Aksesoris Unit</h6></div>
                        <div class="col-12">
                            <div class="row g-2">
                                <!-- Row 1 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LAMPU UTAMA" id="acc_lampu_utama">
                                        <label class="form-check-label" for="acc_lampu_utama">Lampu</label>
                                        <small class="text-muted">(Utama, Mundur, Sign, Stop)</small>
                                    </div>
                                </div>
                                
                                <!-- Row 2 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BLUE SPOT" id="acc_blue_spot">
                                        <label class="form-check-label" for="acc_blue_spot">Blue Spot</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="RED LINE" id="acc_red_line">
                                        <label class="form-check-label" for="acc_red_line">Red Line</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="WORK LIGHT" id="acc_work_light">
                                        <label class="form-check-label" for="acc_work_light">Work Light</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ROTARY LAMP" id="acc_rotary_lamp">
                                        <label class="form-check-label" for="acc_rotary_lamp">Rotary Lamp</label>
                                    </div>
                                </div>
                                
                                <!-- Row 3 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BACK BUZZER" id="acc_back_buzzer">
                                        <label class="form-check-label" for="acc_back_buzzer">Back Buzzer</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA AI" id="acc_camera_ai">
                                        <label class="form-check-label" for="acc_camera_ai">Camera AI</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA" id="acc_camera">
                                        <label class="form-check-label" for="acc_camera">Camera</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SENSOR PARKING" id="acc_sensor_parking">
                                        <label class="form-check-label" for="acc_sensor_parking">Sensor Parking</label>
                                    </div>
                                </div>
                                
                                <!-- Row 4 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPEED LIMITER" id="acc_speed_limiter">
                                        <label class="form-check-label" for="acc_speed_limiter">Speed Limiter</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LASER FORK" id="acc_laser_fork">
                                        <label class="form-check-label" for="acc_laser_fork">Laser Fork</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="VOICE ANNOUNCER" id="acc_voice_announcer">
                                        <label class="form-check-label" for="acc_voice_announcer">Voice Announcer</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN SPEAKER" id="acc_horn_speaker">
                                        <label class="form-check-label" for="acc_horn_speaker">Horn Speaker</label>
                                    </div>
                                </div>
                                
                                <!-- Row 5 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN KLASON" id="acc_horn_klason">
                                        <label class="form-check-label" for="acc_horn_klason">Horn Klason</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BIO METRIC" id="acc_bio_metric">
                                        <label class="form-check-label" for="acc_bio_metric">Bio Metric</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ACRYLIC" id="acc_acrylic">
                                        <label class="form-check-label" for="acc_acrylic">Acrylic</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="APAR 1 KG" id="acc_apar_1kg">
                                        <label class="form-check-label" for="acc_apar_1kg">APAR 1 KG</label>
                                    </div>
                                </div>
                                
                                <!-- Row 6 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="APAR 3 KG" id="acc_apar_3kg">
                                        <label class="form-check-label" for="acc_apar_3kg">APAR 3 KG</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="P3K" id="acc_p3k">
                                        <label class="form-check-label" for="acc_p3k">P3K</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SAFETY BELT INTERLOC" id="acc_safety_belt">
                                        <label class="form-check-label" for="acc_safety_belt">Safety Belt Interloc</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BEACON" id="acc_beacon">
                                        <label class="form-check-label" for="acc_beacon">Beacon</label>
                                    </div>
                                </div>
                                
                                <!-- Row 7 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="TELEMATIC" id="acc_telematic">
                                        <label class="form-check-label" for="acc_telematic">Telematic</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPARS ARRESTOR" id="acc_spars_arrestor">
                                        <label class="form-check-label" for="acc_spars_arrestor">Spars Arrestor</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitSpesifikasiBtn">Simpan Spesifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
