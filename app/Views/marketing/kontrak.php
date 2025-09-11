<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
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
    /* Modal z-index hierarchy */
    #contractDetailModal { z-index: 1055 !important; }
    #contractDetailModal .modal-backdrop { z-index: 1054 !important; }
    #editContractModal { z-index: 1065 !important; }
    #editContractModal .modal-backdrop { z-index: 1064 !important; }
    #addSpesifikasiModal { z-index: 1070 !important; }
    #addSpesifikasiModal .modal-backdrop { z-index: 1069 !important; }
    #unitDetailModal { z-index: 1075 !important; }
    #unitDetailModal .modal-backdrop { z-index: 1074 !important; }
    
    /* Pastikan modal dialog berada di atas backdrop */
    .modal-dialog { z-index: 1056 !important; }
    #editContractModal .modal-dialog { z-index: 1066 !important; }
    #addSpesifikasiModal .modal-dialog { z-index: 1071 !important; }
    #unitDetailModal .modal-dialog { z-index: 1076 !important; }
    #unitDetailModal .modal-dialog { z-index: 1080 !important; }
    
    /* Unit table row hover effect */
    .unit-row:hover {
        background-color: #f8f9fa !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    
    /* Aksesori card styling */
    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }
    
    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }
    
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    
    /* Aksesori detail layout */
    .aksesori-item {
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .aksesori-item:hover {
        background: #e9ecef;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span>Show</span>
                    <select class="form-select form-select-sm" id="entriesPerPage" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span>Search:</span>
                    <input type="text" class="form-control form-control-sm" id="kontrakSearch" placeholder="" style="width: 200px;">
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="contractsTable" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. Kontrak</th>
                            <th>No. PO</th>
                            <th>Nama Perusahaan</th>
                            <th>Periode Kontrak</th>
                            <th>Total Unit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat melalui JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination and Info -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div id="kontrakTableInfo">Showing 0 to 0 of 0 entries</div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="kontrakPagination"></ul>
                </nav>
            </div>
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Kontrak*</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="contract_number" required>
                                <button class="btn btn-outline-secondary" type="button" id="generateContractNumber" title="Generate Nomor Kontrak">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                        </div>
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

<!-- Modal Edit Kontrak -->
<div class="modal fade" id="editContractModal" tabindex="-1" style="z-index: 1060;" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Edit Kontrak</h5>
                    <small class="text-muted">Ubah informasi kontrak</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editContractForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Kontrak*</label>
                            <input type="text" class="form-control" name="contract_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. PO Klien</label>
                            <input type="text" class="form-control" name="po_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Perusahaan*</label>
                            <input type="text" class="form-control" name="client_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama PIC</label>
                            <input type="text" class="form-control" name="pic">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kontak PIC</label>
                            <input type="text" class="form-control" name="kontak">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi/Alamat</label>
                            <input type="text" class="form-control" name="lokasi">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai*</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Berakhir*</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Sewa</label>
                            <select class="form-select" name="jenis_sewa">
                                <option value="BULANAN">Bulanan</option>
                                <option value="HARIAN">Harian</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="Draft">Draft</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Expired">Expired</option>
                                <option value="Expiring">Expiring</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Unit</label>
                            <input type="number" class="form-control" name="total_units" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nilai Kontrak</label>
                            <input type="number" class="form-control" name="contract_value" readonly>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="contract_id" id="editContractId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnUpdateContract" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Kontrak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Unit -->
<div class="modal fade" id="unitDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Detail Unit</h5>
                    <small class="text-muted" id="unitDetailSubtitle">Informasi lengkap unit</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="unitDetailContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat detail unit...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Fallback untuk OptimaPro jika belum dimuat
if (typeof OptimaPro === 'undefined') {
    console.log('OptimaPro not found, using fallback');
    window.OptimaPro = {
        showNotification: function(message, type, duration = 5000) {
            console.log(`OptimaPro.showNotification: [${type}] ${message}`);
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
            console.log(`OptimaPro.showConfirmDialog: ${title} - ${message}`);
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
    console.log('OptimaPro fallback loaded successfully');
} else {
    console.log('OptimaPro already loaded');
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

// Safe OptimaPro wrapper functions
function safeShowNotification(message, type = 'info', duration = 5000) {
    if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
        OptimaPro.showNotification(message, type, duration);
    } else {
        // Fallback to alert or console
        console.log(`[${type.toUpperCase()}] ${message}`);
        if (type === 'error' || type === 'danger') {
            alert(`Error: ${message}`);
        }
    }
}

function safeShowConfirmDialog({title, message}) {
    if (window.OptimaPro && typeof OptimaPro.showConfirmDialog === 'function') {
        return OptimaPro.showConfirmDialog({title, message});
    } else {
        // Fallback to native confirm
        return new Promise(resolve => {
            if (confirm(`${title}\n\n${message}`)) {
                resolve({ isConfirmed: true });
            } else {
                resolve({ isConfirmed: false });
            }
        });
    }
}
$(document).ready(function() {
    console.log('Document ready - initializing kontrak management...');
    
    // Global variables for filtering and pagination
    let currentKontrakFilter = 'all';
    let currentSearchQuery = '';
    let currentPage = 1;
    const itemsPerPage = 10;
    let allKontrakData = [];
    let filteredKontrakData = [];
    
    // OPTIMIZED: Check if data is already loaded to avoid duplicates
    if (typeof window.kontrakDataLoaded === 'undefined' || !window.kontrakDataLoaded) {
        // Initialize the page
        loadKontrakData();
        window.kontrakDataLoaded = true;
    } else {
        console.log('⏭️ Kontrak data already loaded, skipping initialization');
    }
    
    // OPTIMIZED: Function to load all kontrak data from server (with duplicate prevention)
    function loadKontrakData() {
        // Check if data is already being loaded
        if (window.kontrakDataLoading) {
            console.log('⏭️ Kontrak data already loading, skipping duplicate call');
            return;
        }
        
        window.kontrakDataLoading = true;
        console.log('🚀 Loading kontrak data...');
        
        const tbody = $('#contractsTable tbody');
        tbody.html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');
        
        $.ajax({
            url: '<?= base_url('marketing/kontrak/getDataTable') ?>',
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                draw: 1,
                start: 0,
                length: 100
            },
            success: function(response) {
                console.log('✅ Success Response:', response);
                
                if (response && response.data && response.data.length > 0) {
                    // Convert to expected format
                    allKontrakData = response.data.map(item => ({
                        no_kontrak: (item.contract_number || '').replace(/<[^>]*>/g, ''),
                        no_po_marketing: item.po || '',
                        pelanggan: item.client_name || '',
                        periode: item.period || '',
                        total_unit: item.total_unit || 0,
                        status: (item.status || '').replace(/<[^>]*>/g, '')
                    }));
                    
                    console.log('📊 Converted data:', allKontrakData.length, 'records');
                    applyKontrakFilter(currentKontrakFilter);
                    safeShowNotification('Data kontrak berhasil dimuat (' + allKontrakData.length + ' records)', 'success');
                } else {
                    console.warn('❌ No data in response');
                    allKontrakData = [];
                    applyKontrakFilter(currentKontrakFilter);
                    safeShowNotification('Tidak ada data kontrak ditemukan', 'warning');
                }
                
                // Reset loading flag
                window.kontrakDataLoading = false;
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', error);
                console.log('Status:', status, 'XHR:', xhr.status);
                tbody.html('<tr><td colspan="7" class="text-center text-danger">Error: ' + error + 
                          '<br><button class="btn btn-sm btn-primary mt-2" onclick="loadKontrakData()">Retry</button></td></tr>');
                safeShowNotification('Error loading data: ' + error, 'danger');
                
                // Reset loading flag
                window.kontrakDataLoading = false;
            }
        });
    }
    
    // NEW: Efficient data reload function (content-only, no script reload)
    function reloadKontrakDataOnly() {
        console.log('🔄 Reloading kontrak data only...');
        
        // Check if data is already being loaded
        if (window.kontrakDataLoading) {
            console.log('⏭️ Kontrak data already loading, skipping duplicate call');
            return;
        }
        
        window.kontrakDataLoading = true;
        
        $.ajax({
            url: '<?= base_url('marketing/kontrak/data') ?>',
            method: 'GET',
            data: {
                draw: 1,
                start: 0,
                length: 100
            },
            success: function(response) {
                console.log('✅ Data reload response:', response);
                
                if (response && response.data && response.data.length > 0) {
                    // Update data without reinitializing everything
                    allKontrakData = response.data.map(item => ({
                        no_kontrak: (item.contract_number || '').replace(/<[^>]*>/g, ''),
                        no_po_marketing: item.po || '',
                        pelanggan: item.client_name || '',
                        periode: item.period || '',
                        total_unit: item.total_unit || 0,
                        status: item.status || 'Unknown'
                    }));
                    
                    // Just update the display without reinitializing
                    applyKontrakFilter(currentKontrakFilter);
                    console.log('✅ Kontrak data reloaded: ' + allKontrakData.length + ' records');
                }
                
                // Reset loading flag
                window.kontrakDataLoading = false;
            },
            error: function(xhr, status, error) {
                console.error('❌ Data reload error:', error);
                // Reset loading flag
                window.kontrakDataLoading = false;
            }
        });
    }
    
    // Function to apply filter and update display
    function applyKontrakFilter(filter) {
        currentKontrakFilter = filter;
        currentPage = 1; // Reset to first page when filter changes
        
        // Update active card styling
        $('.filter-card').removeClass('active');
        $(`[data-filter="${filter}"]`).addClass('active');
        
        // Filter data based on status
        filteredKontrakData = allKontrakData.filter(kontrak => {
            const matchesFilter = filter === 'all' || kontrak.status.toLowerCase() === filter.toLowerCase();
            const matchesSearch = !currentSearchQuery || 
                kontrak.no_kontrak.toLowerCase().includes(currentSearchQuery.toLowerCase()) ||
                kontrak.no_po_marketing.toLowerCase().includes(currentSearchQuery.toLowerCase()) ||
                kontrak.pelanggan.toLowerCase().includes(currentSearchQuery.toLowerCase());
            
            return matchesFilter && matchesSearch;
        });
        
        updateKontrakDisplay();
        updateKontrakStatistics();
    }
    
    // Function to update statistics cards
    function updateKontrakStatistics() {
        const stats = {
            total: allKontrakData.length,
            aktif: allKontrakData.filter(k => k.status.toLowerCase() === 'aktif').length,
            expired: allKontrakData.filter(k => k.status.toLowerCase() === 'expired').length,
            expiring: allKontrakData.filter(k => k.status.toLowerCase() === 'expiring').length
        };
        
        $('#stat-total').text(formatNumber(stats.total));
        $('#stat-active').text(formatNumber(stats.aktif));
        $('#stat-expired').text(formatNumber(stats.expired));
        $('#stat-expiring').text(formatNumber(stats.expiring));
    }
    
    // Function to update kontrak table display
    function updateKontrakDisplay() {
        const tbody = $('#contractsTable tbody');
        
        if (filteredKontrakData.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                        Tidak ada data kontrak yang ditemukan
                    </td>
                </tr>
            `);
            updatePaginationInfo(0, 0, 0);
            updatePaginationControls(0);
            return;
        }
        
        // Calculate pagination
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, filteredKontrakData.length);
        const pageData = filteredKontrakData.slice(startIndex, endIndex);
        
        // Generate table rows
        let tableHTML = '';
        pageData.forEach(kontrak => {
            const statusBadge = getStatusBadge(kontrak.status);
            const period = kontrak.tanggal_mulai && kontrak.tanggal_selesai ? 
                `${kontrak.tanggal_mulai} s/d ${kontrak.tanggal_selesai}` : '-';
            
            tableHTML += `
                <tr>
                    <td>
                        <a href="#" class="text-decoration-none fw-bold" onclick="openContractDetail(${kontrak.id}); return false;">
                            ${kontrak.no_kontrak}
                        </a>
                    </td>
                    <td>${kontrak.no_po_marketing || '-'}</td>
                    <td>${kontrak.pelanggan || '-'}</td>
                    <td>${period}</td>
                    <td><span class="fw-bold text-primary">${kontrak.total_units || 0}</span></td>
                    <td>${statusBadge}</td>
                </tr>
            `;
        });
        
        tbody.html(tableHTML);
        
        // Update pagination info and controls
        updatePaginationInfo(startIndex + 1, endIndex, filteredKontrakData.length);
        updatePaginationControls(Math.ceil(filteredKontrakData.length / itemsPerPage));
    }
    
    // Function to get status badge HTML
    function getStatusBadge(status) {
        const statusLower = status.toLowerCase();
        let badgeClass = 'secondary';
        
        switch(statusLower) {
            case 'aktif':
                badgeClass = 'success';
                break;
            case 'expired':
                badgeClass = 'danger';
                break;
            case 'expiring':
                badgeClass = 'warning';
                break;
            case 'draft':
                badgeClass = 'info';
                break;
        }
        
        return `<span class="badge bg-${badgeClass}">${status}</span>`;
    }
    
    // Function to update pagination info
    function updatePaginationInfo(start, end, total) {
        $('#kontrakTableInfo').text(`Showing ${start} to ${end} of ${total} entries`);
    }
    
    // Function to update pagination controls
    function updatePaginationControls(totalPages) {
        const pagination = $('#kontrakPagination');
        
        if (totalPages <= 1) {
            pagination.hide();
            return;
        }
        
        pagination.show();
        let paginationHTML = '';
        
        // Previous button
        paginationHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changeKontrakPage(${currentPage - 1}); return false;">Previous</a>
            </li>
        `;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHTML += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changeKontrakPage(${i}); return false;">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Next button
        paginationHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changeKontrakPage(${currentPage + 1}); return false;">Next</a>
            </li>
        `;
        
        pagination.html(paginationHTML);
    }
    
    // Function to change page
    window.changeKontrakPage = function(page) {
        const totalPages = Math.ceil(filteredKontrakData.length / itemsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            updateKontrakDisplay();
        }
    };
    
    // Search functionality
    $('#searchKontrak').on('input', function() {
        currentSearchQuery = $(this).val().trim();
        currentPage = 1; // Reset to first page when searching
        applyKontrakFilter(currentKontrakFilter);
    });
    
    // Filter card click listeners
    $('.filter-card').on('click', function() {
        const filter = $(this).data('filter');
        applyKontrakFilter(filter);
    });
    
    // Set default active filter
    $('[data-filter="all"]').addClass('active');

    // Add contract number generation
    function generateContractNumber() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        return `KTR/${year}/${month}/`;
    }

    // Auto-fill contract number when modal opens
    $('#addContractModal').on('show.bs.modal', function() {
        const contractNumberField = $('input[name="contract_number"]');
        if (!contractNumberField.val()) {
            // Generate base contract number
            const baseNumber = generateContractNumber();
            contractNumberField.val(baseNumber);
            
            // Fetch next available number
            fetch('<?= base_url('marketing/kontrak/generate-number') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        contractNumberField.val(data.contract_number);
                    }
                })
                .catch(err => console.log('Could not auto-generate contract number:', err));
        }
    });

    // Check for duplicate contract number before submitting
    function checkContractNumberDuplicate(contractNumber) {
        return fetch('<?= base_url('marketing/kontrak/check-duplicate') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                contract_number: contractNumber,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            return data.duplicate || false;
        })
        .catch(err => {
            console.error('Error checking duplicate:', err);
            return false;
        });
    }

    // Validate contract number on blur
    $('input[name="contract_number"]').on('blur', function() {
        const contractNumber = $(this).val().trim();
        if (contractNumber) {
            checkContractNumberDuplicate(contractNumber).then(isDuplicate => {
                if (isDuplicate) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Nomor kontrak sudah digunakan. Gunakan tombol generate untuk nomor baru.</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });
        }
    });

    // Handle generate contract number button
    $('#generateContractNumber').on('click', function() {
        const button = $(this);
        const icon = button.find('i');
        const originalIcon = icon.attr('class');
        
        // Show loading state
        icon.attr('class', 'fas fa-spinner fa-spin');
        button.prop('disabled', true);
        
        fetch('<?= base_url('marketing/kontrak/generate-number') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('input[name="contract_number"]').val(data.contract_number);
                    safeShowNotification('Nomor kontrak berhasil di-generate', 'success');
                } else {
                    safeShowNotification('Gagal generate nomor kontrak: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(err => {
                console.error('Error generating contract number:', err);
                safeShowNotification('Gagal generate nomor kontrak', 'error');
            })
            .finally(() => {
                // Restore original state
                icon.attr('class', originalIcon);
                button.prop('disabled', false);
            });
    });

    // Primary action buttons
    $('#btnSaveAndSpec').on('click', function() {
        $('#submitAction').val('save_and_spec');
        $('#addContractForm').trigger('submit');
    });
    $('#btnSaveOnly').on('click', function() {
        $('#submitAction').val('save_only');
        $('#addContractForm').trigger('submit');
    });

    $('#addContractForm').off('submit').on('submit', function(e) {
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
            console.log('AJAX Success Response:', response);
            if (response.success) {
                $('#addContractModal').modal('hide');
                
                // Show success notification once
                safeShowNotification(response.message, 'success');
                
                // Safely reload DataTable with proper error handling
                try {
                    if (typeof window.contractsTable !== 'undefined' && window.contractsTable && window.contractsTable.ajax) {
                        window.contractsTable.ajax.reload();
                    } else if ($.fn.DataTable && $.fn.DataTable.isDataTable('#contractsTable')) {
                        $('#contractsTable').DataTable().ajax.reload();
                    } else {
                        console.warn('DataTable not found, reloading page instead');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Error reloading DataTable:', error);
                    // Fallback: reload page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
                
                if (isEdit) {
                    // Reset form after success
                    $('#addContractForm')[0].reset();
                    $('#addContractForm').removeData('contract-id');
                    $('#addContractForm').attr('action', 'store');
                    $('#addContractModal .modal-title').text('Tambah Kontrak Baru');
                } else {
                    // Create baru: arahkan sesuai submit_action
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
                        const contractNumber = $('input[name="contract_number"]').val();
                        safeShowConfirmDialog({
                            title: 'Nomor Kontrak Sudah Ada',
                            message: `Nomor kontrak "${contractNumber}" sudah digunakan. Klik "Generate Baru" untuk nomor baru, atau "Lihat Kontrak" untuk membuka kontrak yang sudah ada.`
                        }).then(res => {
                            if (res.isConfirmed) {
                                openContractDetail(response.existing_id);
                                setTimeout(() => {
                                    const spekTab = document.querySelector('#contractDetailTabs button[data-bs-target="#spesifikasi-content"]');
                                    if (spekTab) spekTab.click();
                                }, 400);
                            }
                        });
                        
                        // Also show a secondary option to generate new number
                        setTimeout(() => {
                            if (confirm('Apakah Anda ingin generate nomor kontrak baru?')) {
                                $('#generateContractNumber').click();
                            }
                        }, 1000);
                        
                        return;
                    }

                    let msg = response.message || 'Terjadi kesalahan.';
                    if (response.errors && typeof response.errors === 'object') {
                        msg = 'Validasi gagal: ' + Object.values(response.errors).join(', ');
                    }
                    safeShowNotification(msg, 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error Response:', xhr, status, error);
                let msg = 'Tidak dapat terhubung ke server.';
                if (xhr && xhr.responseText) {
                    try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch(e) {}
                }
                safeShowNotification(msg, 'danger');
            },
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
            safeShowNotification('Kontrak ID tidak ditemukan. Silakan buka detail kontrak terlebih dahulu.', 'error');
            return;
        }
        
        if (!jumlahDibutuhkan || jumlahDibutuhkan <= 0) {
            safeShowNotification('Jumlah unit dibutuhkan harus diisi dengan nilai lebih dari 0.', 'error');
            return;
        }
        
        if (!departemenId) {
            safeShowNotification('Departemen harus dipilih.', 'error');
            return;
        }
        
        if (!tipeUnitId) {
            safeShowNotification('Tipe Unit harus dipilih.', 'error');
            return;
        }
        
        if (!jenisUnit) {
            safeShowNotification('Jenis Unit harus dipilih.', 'error');
            return;
        }
        
        // Get form data with proper serialization
        const formData = $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>';
        
        // Debug: Log form data to check if kapasitas_id is included
        console.log('Form data being sent:', formData);
        console.log('Kapasitas value:', $('#spekKapasitas').val());
        
        // Check if this is edit mode
        const spekId = $('#spekEditId').val();
        const isEdit = spekId && spekId !== '';
        const url = isEdit ? 
            `<?= base_url('marketing/kontrak/update-spesifikasi/') ?>${spekId}` : 
            '<?= base_url('marketing/kontrak/add-spesifikasi') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            dataType: 'json',

            success: function(response) {
                if (response.success) {
                    safeShowNotification(response.message, 'success');
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
                    
                    safeShowNotification(errorMsg, 'error');
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
                
                safeShowNotification(errorMessage, 'error');
            }
        });
    }); // End spesifikasi form handler

    // Note: Only one submit handler for addSpesifikasiForm to prevent duplicates

});

function viewContractUnits(contractId) {
    // Validate contract ID
    if (!contractId || contractId == '0' || contractId == 0) {
        safeShowNotification('ID kontrak tidak valid.', 'error');
        return;
    }
    
    // Arahkan ke halaman list_unit.php dengan membawa ID kontrak
    window.location.href = `<?= base_url('marketing/list-unit/') ?>${contractId}`;
}

// Open contract detail modal with multi-specification support
function openContractDetail(id){
    console.log('Opening contract detail for ID:', id);
    
    // Validate contract ID
    if (!id || id == '0' || id == 0) {
        safeShowNotification('ID kontrak tidak valid.', 'error');
        return;
    }
    
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
    
    // Show modal with proper configuration
    const modalElement = document.getElementById('contractDetailModal');
    const modal = new bootstrap.Modal(modalElement, {
        backdrop: true,
        keyboard: true,
        focus: true
    });
    
    console.log('Showing modal...');
    
    // Force proper z-index before showing
    modalElement.style.zIndex = '1055';
    
    modal.show();
    
    // Fix backdrop z-index after modal is shown
    modalElement.addEventListener('shown.bs.modal', function () {
        console.log('Modal shown, setting up tab event handlers...');
        
        // Find and fix backdrop z-index
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.style.zIndex = '1054';
            console.log('Backdrop z-index set to 1054');
        }
        
        // Ensure modal dialog is above backdrop
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.zIndex = '1056';
        }
        
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
            
            // Store contract data globally for SPK creation
            window.currentContractData = d;
            
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
            
            let html = ``;
            
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
                        <label class="form-label">Pelanggan</label>
                        <input type="text" class="form-control" name="pelanggan" id="spkPelanggan" readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PIC</label>
                        <input type="text" class="form-control" name="pic" id="spkPic" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" class="form-control" name="kontak" id="spkKontak" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" class="form-control" name="lokasi" id="spkLokasi" readonly>
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
            console.log('openSpkModalFromKontrak called with spekId:', spekId);
            const kontrakId = window.currentKontrakId || document.getElementById('currentKontrakId')?.value;
            console.log('Current kontrakId:', kontrakId);
            if (!kontrakId) {
                safeShowNotification('Kontrak belum dipilih. Buka detail kontrak terlebih dahulu.', 'error');
                return;
            }
            document.getElementById('spkKontrakId').value = kontrakId;
            document.getElementById('spkSpesifikasiId').value = spekId;
            console.log('Set form values - kontrakId:', kontrakId, 'spekId:', spekId);
            
            // Populate fields from contract data
            if (window.currentContractData) {
                const pelangganField = document.getElementById('spkPelanggan');
                const picField = document.getElementById('spkPic');
                const kontakField = document.getElementById('spkKontak');
                const lokasiField = document.getElementById('spkLokasi');
                
                if (pelangganField) pelangganField.value = window.currentContractData.pelanggan || '';
                if (picField) picField.value = window.currentContractData.pic || '';
                if (kontakField) kontakField.value = window.currentContractData.kontak || '';
                if (lokasiField) lokasiField.value = window.currentContractData.lokasi || '';
            }
            
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
                
                // Validate required fields
                const kontrakId = formData.get('kontrak_id');
                const kontrakSpesifikasiId = formData.get('kontrak_spesifikasi_id');
                console.log('Validation - kontrak_id:', kontrakId, 'kontrak_spesifikasi_id:', kontrakSpesifikasiId);
                
                if (!kontrakId) {
                    safeShowNotification('Data kontrak tidak tersedia. Pastikan halaman sudah dimuat dengan benar.', 'error');
                    return;
                }
                
                // kontrak_spesifikasi_id can be 0 or null when creating SPK from contract without specific spec
                if (!kontrakSpesifikasiId && kontrakSpesifikasiId !== '0' && kontrakSpesifikasiId !== 0) {
                    console.log('kontrak_spesifikasi_id is empty, but kontrak_id is provided - this is OK');
                }
                if (!formData.get('pelanggan')) {
                    safeShowNotification('Data pelanggan tidak tersedia. Pastikan detail kontrak sudah dimuat.', 'error');
                    return;
                }
                if (!formData.get('delivery_plan') || !formData.get('jumlah_unit')) {
                    safeShowNotification('Lengkapi semua field wajib.', 'error');
                    return;
                }
                
                // Client-side limit based on available units
                const available = Number(spkForm.dataset.availableUnits || '0');
                const jumlah = Number(formData.get('jumlah_unit'));
                if (available > 0 && jumlah > available) {
                    safeShowNotification('Jumlah unit melebihi yang dibutuhkan. Maksimal: ' + available, 'error');
                    return;
                }
                
                // Log form data for debugging
                console.log('SPK Form Data:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }
                
                fetch('<?= base_url('marketing/spk/create') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => {
                    console.log('SPK Create Response Status:', response.status);
                    return response.json();
                })
                .then(res => {
                    console.log('SPK Create Response:', res);
                    if (res.success) {
                        safeShowNotification('SPK berhasil dibuat!', 'success');
                        const modalEl = document.getElementById('spkFromKontrakModal');
                        bootstrap.Modal.getInstance(modalEl)?.hide();
                        if (window.currentKontrakId) loadContractSpesifikasi(window.currentKontrakId);
                    } else {
                        safeShowNotification(res.message || 'Gagal membuat SPK.', 'error');
                    }
                })
                .catch(err => {
                    safeShowNotification('Gagal membuat SPK: ' + err, 'error');
                });
            });
        }
    }
});

function editContract(contractId) {
    // Validate contract ID
    if (!contractId || contractId == '0' || contractId == 0) {
        safeShowNotification('ID kontrak tidak valid.', 'error');
        return;
    }

    // Load data kontrak untuk edit
    $.ajax({
        url: `<?= base_url('marketing/kontrak/detail/') ?>${contractId}`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const contract = response.data;
                // Populate form with existing data
                $('#editContractForm input[name="contract_number"]').val(contract.no_kontrak);
                $('#editContractForm input[name="po_number"]').val(contract.no_po_marketing);
                $('#editContractForm input[name="client_name"]').val(contract.pelanggan);
                $('#editContractForm input[name="lokasi"]').val(contract.lokasi);
                $('#editContractForm input[name="start_date"]').val(contract.tanggal_mulai);
                $('#editContractForm input[name="end_date"]').val(contract.tanggal_berakhir);
                $('#editContractForm input[name="contract_value"]').val(contract.nilai_total);
                $('#editContractForm input[name="total_units"]').val(contract.total_units || 0);
                $('#editContractForm input[name="pic"]').val(contract.pic);
                $('#editContractForm input[name="kontak"]').val(contract.kontak);
                $('#editContractForm select[name="status"]').val(contract.status);
                $('#editContractForm select[name="jenis_sewa"]').val(contract.jenis_sewa || 'BULANAN');
                $('#editContractForm textarea[name="catatan"]').val(contract.catatan || '');
                $('#editContractForm input[name="contract_id"]').val(contractId);

                // Show edit modal
                $('#editContractModal').modal('show');
            } else {
                safeShowNotification(response.message, 'error');
            }
        },
        error: function() {
            safeShowNotification('Gagal memuat data kontrak.', 'error');
        }
    });
}

function deleteContract(contractId) {
    // Validate contract ID
    if (!contractId || contractId == '0' || contractId == 0) {
        safeShowNotification('ID kontrak tidak valid.', 'error');
        return;
    }
    
    // Use simple confirm dialog for testing
    if (confirm('Konfirmasi Hapus\n\nApakah Anda yakin ingin menghapus kontrak ini?')) {
        performDelete(contractId);
    }
}

function performDelete(contractId) {
    console.log('Attempting to delete contract with ID:', contractId);
    console.log('CSRF Token:', '<?= csrf_token() ?>');
    console.log('CSRF Hash:', '<?= csrf_hash() ?>');
    
    $.ajax({
        url: `<?= base_url('marketing/kontrak/delete/') ?>${contractId}`,
        method: 'POST',
        data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
        dataType: 'json',
        success: function(response) {
            console.log('Delete response:', response);
            if (response.success) {
                safeShowNotification(response.message, 'success');
                // Safely reload DataTable with proper error handling
                try {
                    if (typeof window.contractsTable !== 'undefined' && window.contractsTable && window.contractsTable.ajax) {
                        window.contractsTable.ajax.reload();
                    } else if ($.fn.DataTable && $.fn.DataTable.isDataTable('#contractsTable')) {
                        console.warn('window.contractsTable is undefined, trying direct DataTable access');
                        $('#contractsTable').DataTable().ajax.reload();
                    } else {
                        console.warn('DataTable not found, reloading page instead');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Error reloading DataTable:', error);
                    // Fallback: reload page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                safeShowNotification(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete AJAX error:', xhr, status, error);
            console.log('Status code:', xhr.status);
            console.log('Response headers:', xhr.getAllResponseHeaders());
            console.log('Response text:', xhr.responseText);
            
            // Try to parse error response
            try {
                const errorData = JSON.parse(xhr.responseText);
                console.log('Parsed error response:', errorData);
                if (errorData.message) {
                    safeShowNotification('Error: ' + errorData.message, 'error');
                } else {
                    safeShowNotification('Gagal menghapus kontrak.', 'error');
                }
            } catch (e) {
                console.log('Could not parse error response');
                safeShowNotification('Gagal menghapus kontrak.', 'error');
            }
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
                const summary = response.summary || {};
                
                // Add summary cards
                let unitsHtml = `
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold text-success">${summary.total_unit_dibutuhkan || 0}</div>
                                    <small>Total Unit</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold text-warning">Rp ${formatNumber(summary.total_nilai_bulanan || 0)}</div>
                                    <small>Total Nilai Bulanan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold text-info">Rp ${formatNumber(summary.total_nilai_harian || 0)}</div>
                                    <small>Total Nilai Harian</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                if (response.data.length > 0) {
                    unitsHtml += '<div class="table-responsive">';
                    unitsHtml += '<table class="table table-sm table-striped">';
                    unitsHtml += '<thead><tr><th>No Unit</th><th>Merk/Model</th><th>Kapasitas</th><th>Jenis Unit</th><th>Departemen</th><th>Harga Bulanan</th><th>Harga Harian</th><th>Status</th></tr></thead>';
                    unitsHtml += '<tbody>';
                    
                    response.data.forEach(unit => {
                        unitsHtml += `<tr style="cursor: pointer;" onclick="showUnitDetail(${unit.id})" class="unit-row">
                            <td>${unit.no_unit || '-'}</td>
                            <td>${unit.merk || '-'} ${unit.model || ''}</td>
                            <td>${unit.kapasitas || '-'}</td>
                            <td>${unit.jenis_unit || '-'}</td>
                            <td>${unit.departemen || '-'}</td>
                            <td class="text-success fw-bold">Rp ${formatNumber(unit.harga_per_unit_bulanan || unit.harga_bulanan || 0)}</td>
                            <td class="text-info fw-bold">Rp ${formatNumber(unit.harga_per_unit_harian || unit.harga_harian || 0)}</td>
                            <td><span class="badge bg-success">${unit.status || 'TERSEDIA'}</span></td>
                        </tr>`;
                    });
                    
                    unitsHtml += '</tbody></table></div>';
                } else {
                    unitsHtml += '<div class="alert alert-info">Belum ada unit yang di-assign ke kontrak ini.</div>';
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

// Function to show unit detail modal
function showUnitDetail(unitId) {
    console.log('Showing unit detail for ID:', unitId);
    
    // Reset modal content
    $('#unitDetailContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat detail unit...</p>
        </div>
    `);
    
    // Ensure proper z-index before showing modal
    const unitModal = document.getElementById('unitDetailModal');
    unitModal.style.zIndex = '1075';
    
    // Show modal with proper configuration
    const modal = new bootstrap.Modal(unitModal, {
        backdrop: true,
        keyboard: true,
        focus: true
    });
    
    modal.show();
    
    // Fix backdrop z-index after modal is shown
    unitModal.addEventListener('shown.bs.modal', function () {
        const backdrop = document.querySelector('.modal-backdrop:last-child');
        if (backdrop) {
            backdrop.style.zIndex = '1074';
        }
        
        const modalDialog = unitModal.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.zIndex = '1076';
        }
    }, { once: true });
    
    // Load unit detail
    $.ajax({
        url: `<?= base_url('marketing/unit-detail/') ?>${unitId}`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const unit = response.data;
                
                // Update modal subtitle
                $('#unitDetailSubtitle').text(`Unit ${unit.no_unit || 'N/A'} - ${unit.merk_unit || 'N/A'} ${unit.model_unit || ''}`);
                
                let detailHtml = `
                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header bg-primary">
                                    <h6 class="mb-0 text-black"><i class="fas fa-info-circle me-2"></i>Informasi Dasar</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6"><strong>No Unit:</strong></div>
                                        <div class="col-6">${unit.no_unit || '-'}</div>
                                        
                                        <div class="col-6"><strong>Serial Number:</strong></div>
                                        <div class="col-6">${unit.serial_number_po || '-'}</div>
                                        
                                        <div class="col-6"><strong>Merk:</strong></div>
                                        <div class="col-6">${unit.merk_unit || '-'}</div>
                                        
                                        <div class="col-6"><strong>Model:</strong></div>
                                        <div class="col-6">${unit.model_unit || '-'}</div>
                                        
                                        <div class="col-6"><strong>Tahun:</strong></div>
                                        <div class="col-6">${unit.tahun_po || '-'}</div>
                                        
                                        <div class="col-6"><strong>Tipe Unit:</strong></div>
                                        <div class="col-6">${unit.nama_tipe_unit || '-'}</div>
                                        
                                        <div class="col-6"><strong>Kapasitas:</strong></div>
                                        <div class="col-6">${unit.kapasitas_unit || '-'}</div>
                                        
                                        <div class="col-6"><strong>Departemen:</strong></div>
                                        <div class="col-6">${unit.nama_departemen || '-'}</div>
                                        
                                        <div class="col-6"><strong>Status:</strong></div>
                                        <div class="col-6"><span class="badge bg-${getStatusBadgeClass(unit.status_unit_name)}">${unit.status_unit_name || '-'}</span></div>
                                        
                                        <div class="col-6"><strong>Lokasi:</strong></div>
                                        <div class="col-6">${unit.lokasi_unit || '-'}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Technical Specifications -->
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header bg-success">
                                    <h6 class="mb-0 text-black"><i class="fas fa-cogs me-2"></i>Spesifikasi Teknis</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">`;
                
                // Mast Information
                if (unit.mast_name || unit.sn_mast_po) {
                    detailHtml += `
                        <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2"><i class="fas fa-arrows-alt-v me-1"></i>Mast</h6></div>
                        <div class="col-6"><strong>Model Mast:</strong></div>
                        <div class="col-6">${unit.mast_name || '-'}</div>
                        <div class="col-6"><strong>SN Mast:</strong></div>
                        <div class="col-6">${unit.sn_mast_po || '-'}</div>`;
                }
                
                // Engine Information
                if (unit.mesin_name || unit.sn_mesin_po) {
                    detailHtml += `
                        <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-engine me-1"></i>Mesin</h6></div>
                        <div class="col-6"><strong>Model Mesin:</strong></div>
                        <div class="col-6">${unit.mesin_name || '-'}</div>
                        <div class="col-6"><strong>SN Mesin:</strong></div>
                        <div class="col-6">${unit.sn_mesin_po || '-'}</div>`;
                }
                
                // Battery Information (for electric units)
                if (unit.baterai_name || unit.sn_baterai_po) {
                    detailHtml += `
                        <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-battery-full me-1"></i>Baterai</h6></div>
                        <div class="col-6"><strong>Model Baterai:</strong></div>
                        <div class="col-6">${unit.baterai_name || '-'}</div>
                        <div class="col-6"><strong>SN Baterai:</strong></div>
                        <div class="col-6">${unit.sn_baterai_po || '-'}</div>`;
                }
                
                // Attachments Information
                if (unit.attachments && unit.attachments.length > 0) {
                    detailHtml += `
                        <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-puzzle-piece me-1"></i>Attachment</h6></div>`;
                    unit.attachments.forEach((att, index) => {
                        detailHtml += `
                            <div class="col-6"><strong>${att.name || 'Attachment ' + (index + 1)}:</strong></div>
                            <div class="col-6">${att.merk || '-'}</div>
                            <div class="col-6"><strong>SN ${att.name || 'Att'}:</strong></div>
                            <div class="col-6">${att.serial_number || '-'}</div>`;
                    });
                }
                
                // Batteries Information (for Electric Units)
                if (unit.batteries && unit.batteries.length > 0) {
                    detailHtml += `
                        <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-battery-full me-1"></i>Baterai Tambahan</h6></div>`;
                    unit.batteries.forEach((bat, index) => {
                        detailHtml += `
                            <div class="col-6"><strong>${bat.name || 'Baterai ' + (index + 1)}:</strong></div>
                            <div class="col-6">${bat.merk || '-'}</div>
                            <div class="col-6"><strong>SN ${bat.name || 'Bat'}:</strong></div>
                            <div class="col-6">${bat.serial_number || '-'}</div>`;
                    });
                }
                
                // Chargers Information (for Electric Units)
                if (unit.chargers && unit.chargers.length > 0) {
                    detailHtml += `
                        <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-plug me-1"></i>Charger</h6></div>`;
                    unit.chargers.forEach((chr, index) => {
                        detailHtml += `
                            <div class="col-6"><strong>${chr.name || 'Charger ' + (index + 1)}:</strong></div>
                            <div class="col-6">${chr.merk || '-'}</div>
                            <div class="col-6"><strong>SN ${chr.name || 'Chr'}:</strong></div>
                            <div class="col-6">${chr.serial_number || '-'}</div>`;
                    });
                }
                
                // Wheels and Parts
                if (unit.ban_name || unit.roda_name || unit.valve_name) {
                    detailHtml += `
                        <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-circle me-1"></i>Ban & Roda</h6></div>`;
                    if (unit.ban_name) {
                        detailHtml += `
                            <div class="col-6"><strong>Ban:</strong></div>
                            <div class="col-6">${unit.ban_name}</div>`;
                    }
                    if (unit.roda_name) {
                        detailHtml += `
                            <div class="col-6"><strong>Roda:</strong></div>
                            <div class="col-6">${unit.roda_name}</div>`;
                    }
                    if (unit.valve_name) {
                        detailHtml += `
                            <div class="col-6"><strong>Valve:</strong></div>
                            <div class="col-6">${unit.valve_name}</div>`;
                    }
                }
                
                detailHtml += `
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                
                // Aksesori Terpasang dari kolom aksesoris
                if (unit.aksesoris) {
                    let aksesoris = [];
                    try {
                        // Try to parse as JSON if it's a string
                        aksesoris = typeof unit.aksesoris === 'string' ? JSON.parse(unit.aksesoris) : unit.aksesoris;
                    } catch (e) {
                        // If not JSON, treat as comma-separated string
                        aksesoris = unit.aksesoris.split(',').map(item => item.trim()).filter(item => item);
                    }
                    
                    if (aksesoris && aksesoris.length > 0) {
                        detailHtml += `
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header bg-info text-black">
                                            <h6 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i>Aksesori Terpasang</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">`;
                        
                        if (Array.isArray(aksesoris)) {
                            aksesoris.forEach((item, index) => {
                                detailHtml += `
                                    <div class="col-md-6 mb-2">
                                        <span>-</span>
                                        ${item}
                                    </div>`;
                            });
                        } else {
                            detailHtml += `
                                <div class="col-12">
                                    <span class="badge bg-primary me-1">1</span>
                                    ${aksesoris}
                                </div>`;
                        }
                        
                        detailHtml += `
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }
                }
                
                // Additional Notes
                if (unit.keterangan) {
                    detailHtml += `
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Keterangan</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">${unit.keterangan}</p>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                }
                
                $('#unitDetailContent').html(detailHtml);
                
            } else {
                $('#unitDetailContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Gagal memuat detail unit: ${response.message || 'Unknown error'}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading unit detail:', error);
            $('#unitDetailContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Gagal memuat detail unit. Silakan coba lagi.
                </div>
            `);
        }
    });
}

// Helper function for status badge class
function getStatusBadgeClass(status) {
    if (!status) return 'secondary';
    const statusLower = status.toLowerCase();
    switch(statusLower) {
        case 'tersedia':
        case 'available':
            return 'success';
        case 'rental':
        case 'disewa':
            return 'primary';
        case 'maintenance':
        case 'rusak':
            return 'warning';
        case 'hilang':
        case 'lost':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Open add spesifikasi modal
function openAddSpesifikasiModal() {
    const contractId = window.currentKontrakId;
    if (!contractId) {
        safeShowNotification('ID kontrak tidak ditemukan. Silakan buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    
    console.log('Opening add spesifikasi modal for contract ID:', contractId);
    
    // Reset form
    $('#addSpesifikasiForm')[0].reset();
    $('#spekKontrakId').val(contractId);
    
    // Clear specific fields that might not be cleared by reset()
    $('#spekModelUnit').val('');
    $('#spekAttachmentMerk').val('');
    
    // Uncheck all accessories
    $('input[name="aksesoris[]"]').prop('checked', false);
    
    // Remove edit mode hidden field if exists
    $('#spekEditId').remove();
    
    // Reset modal title and button text
    $('#addSpesifikasiModal .modal-title').text('Tambah Spesifikasi Unit');
    $('#submitSpesifikasiBtn').text('Simpan Spesifikasi');
    
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
        const selectedOption = this.options[this.selectedIndex];
        const departemenText = selectedOption ? selectedOption.text : '';
        
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
        const selectedOption = this.options[this.selectedIndex];
        const tipeUnitName = selectedOption ? selectedOption.text : '';
        
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

// Edit spesifikasi
function editSpesifikasi(spekId) {
    const contractId = window.currentKontrakId;
    if (!contractId) {
        safeShowNotification('ID kontrak tidak ditemukan. Silakan buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    
    // Fetch spesifikasi data for editing
    fetch(`<?= base_url('marketing/kontrak/spesifikasi-detail/') ?>${spekId}`)
        .then(response => response.json())
        .then(j => {
            if (!j.success) {
                safeShowNotification('Gagal memuat data spesifikasi: ' + j.message, 'error');
                return;
            }
            
            const spek = j.data || {};
            
            // Populate form with existing data
            $('#spekKontrakId').val(spek.kontrak_id);
            $('input[name="jumlah_dibutuhkan"]').val(spek.jumlah_dibutuhkan);
            $('input[name="catatan_spek"]').val(spek.catatan_spek);
            $('input[name="harga_per_unit_bulanan"]').val(spek.harga_per_unit_bulanan);
            $('input[name="harga_per_unit_harian"]').val(spek.harga_per_unit_harian);
            
            // Set hidden field for edit mode
            if (!$('#spekEditId').length) {
                $('#addSpesifikasiForm').prepend('<input type="hidden" name="spek_id" id="spekEditId">');
            }
            $('#spekEditId').val(spekId);
            
            // Load dropdowns first, then set values
            loadSpesifikasiDropdowns();
            
            // Set dropdown values after a short delay to ensure options are loaded
            setTimeout(() => {
                // Set departemen first without triggering change to avoid clearing other dropdowns
                if (spek.departemen_id) $('#spekDepartemen').val(spek.departemen_id);
                
                // Load all required dropdown data for this spesifikasi
                const loadPromises = [];
                
                // Load tipe_unit options
                if (spek.departemen_id) {
                    loadPromises.push(
                        fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=tipe_unit`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    fillSelectOptions('#spekTipeUnit', data.data || []);
                                    if (spek.tipe_unit_id) $('#spekTipeUnit').val(spek.tipe_unit_id);
                                }
                            })
                    );
                }
                
                // Load jenis_unit options
                if (spek.tipe_unit_id) {
                    // Get tipe unit name from current data
                    const tipeUnitText = spek.tipe_unit_nama || spek.tipe_unit || '';
                    if (tipeUnitText) {
                        loadPromises.push(
                            fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_unit&parent_tipe=${encodeURIComponent(tipeUnitText)}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        fillSelectOptions('#spekJenisUnit', data.data || []);
                                        if (spek.tipe_jenis) $('#spekJenisUnit').val(spek.tipe_jenis);
                                    }
                                })
                        );
                    }
                }
                
                // Wait for all dropdowns to load, then set values
                Promise.all(loadPromises).then(() => {
                    // Set other dropdown values
                    if (spek.kapasitas_id) $('#spekKapasitas').val(spek.kapasitas_id);
                    if (spek.merk_unit) $('#spekMerkUnit').val(spek.merk_unit);
                    if (spek.jenis_baterai) $('#spekJenisBaterai').val(spek.jenis_baterai);
                    if (spek.attachment_tipe) $('#spekAttachmentTipe').val(spek.attachment_tipe);
                    if (spek.charger_id) $('#spekCharger').val(spek.charger_id);
                    if (spek.mast_id) $('#spekMast').val(spek.mast_id);
                    if (spek.ban_id) $('#spekBan').val(spek.ban_id);
                    if (spek.roda_id) $('#spekRoda').val(spek.roda_id);
                    if (spek.valve_id) $('#spekValve').val(spek.valve_id);
                    if (spek.model_unit) $('#spekModelUnit').val(spek.model_unit);
                    if (spek.attachment_merk) $('#spekAttachmentMerk').val(spek.attachment_merk);
                });
                
                // Handle accessories checkboxes
                // First uncheck all checkboxes
                $('input[name="aksesoris[]"]').prop('checked', false);
                
                // Then check the selected ones
                if (spek.aksesoris) {
                    let aksesoris;
                    try {
                        aksesoris = typeof spek.aksesoris === 'string' ? JSON.parse(spek.aksesoris) : spek.aksesoris;
                    } catch (e) {
                        console.warn('Failed to parse aksesoris JSON:', spek.aksesoris);
                        aksesoris = [];
                    }
                    
                    if (Array.isArray(aksesoris)) {
                        aksesoris.forEach(function(acc) {
                            $(`input[name="aksesoris[]"][value="${acc}"]`).prop('checked', true);
                        });
                    }
                }
            }, 500);
            
            // Change modal title and button text
            $('#addSpesifikasiModal .modal-title').text('Edit Spesifikasi Unit');
            $('#submitSpesifikasiBtn').text('Update Spesifikasi');
            
            // Show modal
            $('#addSpesifikasiModal').modal('show');
        })
        .catch(error => {
            console.error('Error loading spesifikasi detail:', error);
            safeShowNotification('Gagal memuat data spesifikasi: ' + error.message, 'error');
        });
}

// Delete spesifikasi
function deleteSpesifikasi(spekId) {
    console.log('deleteSpesifikasi called with spekId:', spekId);
    
    if (!spekId) {
        safeShowNotification('ID spesifikasi tidak valid', 'error');
        return;
    }
    
    // Use native confirm for now
    if (!confirm('Apakah Anda yakin ingin menghapus spesifikasi ini?')) {
        return;
    }
    
    const url = `<?= base_url('marketing/kontrak/delete-spesifikasi/') ?>${spekId}`;
    console.log('Delete URL:', url);
    
    // Get fresh CSRF token
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    
    const formData = {};
    formData[csrfName] = csrfHash;
    
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        beforeSend: function() {
            console.log('Sending delete request with data:', formData);
        },
        success: function(response) {
            console.log('Delete response:', response);
            
            // Update CSRF token if provided
            if (response.csrf_hash) {
                $('meta[name="csrf-token"]').attr('content', response.csrf_hash);
            }
            
            if (response.success) {
                safeShowNotification(response.message, 'success');
                
                // Reload spesifikasi tab
                const contractId = window.currentKontrakId;
                if (contractId) {
                    loadContractSpesifikasi(contractId);
                } else {
                    console.warn('No contract ID found for reload');
                }
            } else {
                safeShowNotification(response.message || 'Gagal menghapus spesifikasi', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete AJAX error:', {xhr, status, error});
            console.error('Response text:', xhr.responseText);
            console.error('Status code:', xhr.status);
            
            let errorMessage = 'Terjadi kesalahan pada sistem';
            
            if (xhr.status === 403) {
                errorMessage = 'CSRF token tidak valid. Silakan refresh halaman dan coba lagi.';
            } else if (xhr.status === 404) {
                errorMessage = 'Endpoint tidak ditemukan.';
            } else if (xhr.status === 500) {
                errorMessage = 'Terjadi kesalahan server internal.';
            } else {
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    console.warn('Could not parse error response as JSON');
                }
            }
            
            safeShowNotification(errorMessage, 'error');
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
        safeShowNotification('Kontrak belum dipilih. Buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    const base = '<?= rtrim(base_url(), "\/") ?>';
    window.location.href = `${base}/marketing/spk?kontrak_id=${encodeURIComponent(kontrakId)}&spesifikasi_id=${encodeURIComponent(spekId)}`;
}
</script>
<!-- Contract Detail Modal with Multi-Specification Support -->
<div class="modal fade" id="contractDetailModal" tabindex="-1" style="z-index: 1055 !important;" data-bs-backdrop="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="z-index: 1056 !important;">
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
                        <button class="nav-link" id="units-tab" data-bs-toggle="tab" data-bs-target="#units-content" type="button" role="tab">
                            <i class="fas fa-truck me-1"></i>Data Unit (<span id="unitsCount">0</span>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="spesifikasi-tab" data-bs-toggle="tab" data-bs-target="#spesifikasi-content" type="button" role="tab">
                            <i class="fas fa-cogs me-1"></i>Spesifikasi (<span id="spekCount">0</span>)
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
                            <h6 class="mb-0">Request Spesifikasi Unit untuk dasar pembuatan SPK</h6>
                            <button class="btn btn-primary btn-sm" onclick="openAddSpesifikasiModal()">
                                <i class="fas fa-plus me-1"></i>Tambah Spesifikasi
                            </button>
                        </div>
                        <br>

                        <div id="spesifikasiList">
                            <p class="text-muted">Memuat spesifikasi...</p>
                        </div>
                    </div>

                    <!-- Units Tab -->
                    <div class="tab-pane fade" id="units-content" role="tabpanel">
                        
                        <div id="unitsList">
                            <p class="text-muted">Memuat unit...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" onclick="editContract(window.currentKontrakId)">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger" onclick="deleteContract(window.currentKontrakId)">
                    <i class="fas fa-trash"></i> Delete
                </button>
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
                            <select class="form-select" name="kapasitas_id" id="spekKapasitas">
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

<script>
// Event handler untuk tombol update kontrak
$('#btnUpdateContract').on('click', function() {
    const form = $('#editContractForm');
    const contractId = form.find('input[name="contract_id"]').val();
    
    // Debug logging
    console.log('Contract ID for update:', contractId);
    console.log('Form data:', Object.fromEntries(new FormData(form[0])));
    
    if (!contractId) {
        safeShowNotification('ID kontrak tidak ditemukan. Silakan tutup modal dan coba lagi.', 'error');
        return;
    }
    
    const formData = new FormData(form[0]);

    // Add CSRF token
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    // Validate required fields
    const contractNumber = formData.get('contract_number');
    const clientName = formData.get('client_name');
    const startDate = formData.get('start_date');
    const endDate = formData.get('end_date');

    if (!contractNumber || !clientName || !startDate || !endDate) {
        safeShowNotification('Mohon lengkapi semua field yang wajib diisi.', 'error');
        return;
    }

    // Show loading state
    const btn = $(this);
    const originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

    // Submit form
    $.ajax({
        url: `<?= base_url('marketing/kontrak/update/') ?>${contractId}`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('Update response:', response); // Debug log
            
            if (response.success) {
                safeShowNotification('Kontrak berhasil diperbarui!', 'success');
                $('#editContractModal').modal('hide');
                
                // OPTIMIZED: Use efficient data reload instead of full reload
                if (typeof reloadKontrakDataOnly === 'function') {
                    reloadKontrakDataOnly();
                } else if (typeof loadKontrakData === 'function') {
                    loadKontrakData();
                } else {
                    // Fallback: reload page
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                let errorMessage = response.message || 'Gagal memperbarui kontrak.';
                
                // Handle validation errors
                if (response.errors) {
                    const errorList = Object.values(response.errors).join('<br>');
                    errorMessage = `Validasi gagal:<br>${errorList}`;
                }
                
                safeShowNotification(errorMessage, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating contract:', {xhr, status, error});
            console.error('Response text:', xhr.responseText);
            
            let errorMessage = 'Terjadi kesalahan saat memperbarui kontrak.';
            
            // Try to parse error response
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.message) {
                    errorMessage = errorResponse.message;
                }
            } catch (e) {
                console.warn('Could not parse error response');
            }
            
            safeShowNotification(errorMessage, 'error');
        },
        complete: function() {
            // Reset button state
            btn.prop('disabled', false).html(originalText);
        }
    });
});
</script>

<?= $this->endSection() ?>

