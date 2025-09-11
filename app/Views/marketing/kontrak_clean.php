<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .card-stats:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); 
    }
    .table-card, .card-stats { 
        border: none; 
        border-radius: 15px; 
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
    }
    .modal-header { 
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); 
        color: white; 
        border-radius: 15px 15px 0 0; 
    }
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
    
    .modal-dialog { z-index: 1056 !important; }
    #editContractModal .modal-dialog { z-index: 1066 !important; }
    #addSpesifikasiModal .modal-dialog { z-index: 1071 !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-primary text-white h-100 filter-card" data-filter="all" style="cursor: pointer;">
                <div class="card-body">
                    <h2 class="fw-bold mb-1" id="stat-total">0</h2>
                    <h6 class="card-title text-uppercase small">Total Kontrak</h6>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-success text-white h-100 filter-card" data-filter="Aktif" style="cursor: pointer;">
                <div class="card-body">
                    <h2 class="fw-bold mb-1" id="stat-active">0</h2>
                    <h6 class="card-title text-uppercase small">Kontrak Aktif</h6>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-warning text-white h-100 filter-card" data-filter="expiring" style="cursor: pointer;">
                <div class="card-body">
                    <h2 class="fw-bold mb-1" id="stat-expiring">0</h2>
                    <h6 class="card-title text-uppercase small">Akan Berakhir</h6>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-secondary text-white h-100 filter-card" data-filter="Berakhir" style="cursor: pointer;">
                <div class="card-body">
                    <h2 class="fw-bold mb-1" id="stat-expired">0</h2>
                    <h6 class="card-title text-uppercase small">Telah Berakhir</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Kontrak -->
    <div class="card table-card">
        <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <h5 class="h5 mb-0 text-gray-800">Daftar Kontrak Rental</h5>
            <button class="btn btn-sm btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addContractModal">
                <i class="fas fa-plus"></i>
                <span class="fw-semibold">Kontrak</span>
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
                    <input type="text" class="form-control form-control-sm" id="kontrakSearch" placeholder="Cari kontrak..." style="width: 200px;">
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
                    <small class="text-muted">Informasi Dasar Kontrak</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No. Kontrak <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="no_kontrak" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No. PO Marketing</label>
                                <input type="text" class="form-control" name="no_po_marketing">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pelanggan" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">PIC</label>
                                <input type="text" class="form-control" name="pic">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kontak</label>
                                <input type="text" class="form-control" name="kontak">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Lokasi</label>
                                <textarea class="form-control" name="lokasi" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_berakhir" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Sewa</label>
                                <select class="form-select" name="jenis_sewa">
                                    <option value="BULANAN">Bulanan</option>
                                    <option value="HARIAN">Harian</option>
                                    <option value="MINGGUAN">Mingguan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="Pending">Pending</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Berakhir">Berakhir</option>
                                    <option value="Dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
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

<!-- Modal Detail Kontrak -->
<div class="modal fade" id="contractDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kontrak</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="contractDetailBody">
                    <!-- Content will be loaded here -->
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
<script>
// Global variables
let currentKontrakFilter = 'all';
let currentSearchQuery = '';
let currentPage = 1;
let itemsPerPage = 10;
let allKontrakData = [];
let filteredKontrakData = [];

// Helper function for number formatting
function formatNumber(num) {
    try {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    } catch (error) {
        return (num || 0).toString();
    }
}

// Safe notification function
function safeShowNotification(message, type = 'info', duration = 5000) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' || type === 'danger' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' || type === 'danger' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(notification);
    setTimeout(() => $('.alert:last').fadeOut(), duration);
}

$(document).ready(function() {
    // Initialize the page
    loadKontrakData();

    // Handle entries per page change
    $('#entriesPerPage').on('change', function() {
        const val = parseInt($(this).val(), 10) || 10;
        itemsPerPage = val;
        currentPage = 1;
        applyKontrakFilter(currentKontrakFilter);
    });
    
    // Search functionality
    $('#kontrakSearch').on('input', function() {
        currentSearchQuery = $(this).val().trim();
        currentPage = 1;
        applyKontrakFilter(currentKontrakFilter);
    });
    
    // Filter card click listeners
    $('.filter-card').on('click', function() {
        const filter = $(this).data('filter');
        applyKontrakFilter(filter);
    });
    
    // Set default active filter
    $('[data-filter="all"]').addClass('active');

    // Add contract form submission
    $('#addContractForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        $.ajax({
            url: '<?= base_url('marketing/kontrak/store') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    safeShowNotification('Kontrak berhasil disimpan', 'success');
                    $('#addContractModal').modal('hide');
                    $('#addContractForm')[0].reset();
                    loadKontrakData();
                } else {
                    safeShowNotification(response.message || 'Gagal menyimpan kontrak', 'error');
                }
            },
            error: function(xhr, status, error) {
                safeShowNotification('Terjadi kesalahan: ' + error, 'error');
            }
        });
    });
});

// Function to load all kontrak data from server  
function loadKontrakData() {
    const tbody = $('#contractsTable tbody');
    
    tbody.html('<tr><td colspan="6" class="text-center">⏳ Loading...</td></tr>');
    
    $.ajax({
        url: '<?= base_url('marketing/kontrak/getDataTable') ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            draw: 1,
            start: 0,
            length: -1,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.data) {
                allKontrakData = response.data.map(item => ({
                    id: item.id,
                    no_kontrak: item.contract_number?.replace(/<[^>]*>/g, '') || '',
                    no_po_marketing: item.po || '',
                    pelanggan: item.client_name || '',
                    status: item.status?.replace(/<[^>]*>/g, '') || '',
                    tanggal_mulai: item.period?.split(' - ')[0] || '',
                    tanggal_selesai: item.period?.split(' - ')[1] || '',
                    total_units: 0
                }));
                
                if (response.stats) {
                    $('#stat-total').text(formatNumber(response.stats.total || 0));
                    $('#stat-active').text(formatNumber(response.stats.active || 0));
                    $('#stat-expired').text(formatNumber(response.stats.expired || 0));
                    $('#stat-expiring').text(formatNumber(response.stats.expiring || 0));
                } else {
                    $('#stat-total').text(formatNumber(allKontrakData.length));
                }
                
                applyKontrakFilter(currentKontrakFilter);
            } else {
                safeShowNotification('Error loading data: No data received', 'danger');
            }
        },
        error: function(xhr, status, error) {
            safeShowNotification('Error loading data: ' + error, 'danger');
        }
    });
}

function applyKontrakFilter(filter) {
    currentKontrakFilter = filter;
    currentPage = 1;
    
    $('.filter-card').removeClass('active');
    $(`[data-filter="${filter}"]`).addClass('active');
    
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

function updateKontrakStatistics() {
    const stats = {
        total: allKontrakData.length,
        aktif: allKontrakData.filter(k => k.status.toLowerCase() === 'aktif').length,
        expired: allKontrakData.filter(k => k.status.toLowerCase() === 'expired' || k.status.toLowerCase() === 'berakhir').length,
        expiring: allKontrakData.filter(k => k.status.toLowerCase() === 'expiring').length
    };
    
    $('#stat-total').text(formatNumber(stats.total));
    $('#stat-active').text(formatNumber(stats.aktif));
    $('#stat-expired').text(formatNumber(stats.expired));
    $('#stat-expiring').text(formatNumber(stats.expiring));
}

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
    
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, filteredKontrakData.length);
    const pageData = filteredKontrakData.slice(startIndex, endIndex);
    
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
                <td>
                    <a href="#" class="text-decoration-none" onclick="openContractDetail(${kontrak.id}); return false;">
                        ${kontrak.no_po_marketing || '-'}
                    </a>
                </td>
                <td>${kontrak.pelanggan || '-'}</td>
                <td>${period}</td>
                <td><span class="fw-bold text-primary">${kontrak.total_units || 0}</span></td>
                <td>${statusBadge}</td>
            </tr>
        `;
    });
    
    tbody.html(tableHTML);
    
    updatePaginationInfo(startIndex + 1, endIndex, filteredKontrakData.length);
    updatePaginationControls(Math.ceil(filteredKontrakData.length / itemsPerPage));
}

function getStatusBadge(status) {
    const statusLower = status.toLowerCase();
    let badgeClass = 'secondary';
    
    switch(statusLower) {
        case 'aktif':
            badgeClass = 'success';
            break;
        case 'expired':
        case 'berakhir':
            badgeClass = 'danger';
            break;
        case 'pending':
            badgeClass = 'warning';
            break;
        case 'dibatalkan':
            badgeClass = 'secondary';
            break;
    }
    
    return `<span class="badge bg-${badgeClass}">${status}</span>`;
}

function updatePaginationInfo(start, end, total) {
    $('#kontrakTableInfo').text(`Showing ${start} to ${end} of ${total} entries`);
}

function updatePaginationControls(totalPages) {
    const pagination = $('#kontrakPagination');
    
    if (totalPages <= 1) {
        pagination.hide();
        return;
    }
    
    pagination.show();
    let paginationHTML = '';
    
    paginationHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changeKontrakPage(${currentPage - 1}); return false;">Previous</a>
        </li>
    `;
    
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
    
    paginationHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changeKontrakPage(${currentPage + 1}); return false;">Next</a>
        </li>
    `;
    
    pagination.html(paginationHTML);
}

window.changeKontrakPage = function(page) {
    const totalPages = Math.ceil(filteredKontrakData.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        updateKontrakDisplay();
    }
};

function openContractDetail(id) {
    if (!id || id == '0' || id == 0) {
        safeShowNotification('ID kontrak tidak valid.', 'error');
        return;
    }
    
    const body = $('#contractDetailBody');
    body.html('<p class="text-muted">Memuat detail kontrak...</p>');
    
    $.ajax({
        url: `<?= base_url('marketing/kontrak/detail/') ?>${id}`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                body.html(`
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td><strong>No. Kontrak:</strong></td><td>${data.no_kontrak}</td></tr>
                                <tr><td><strong>No. PO:</strong></td><td>${data.no_po_marketing || '-'}</td></tr>
                                <tr><td><strong>Pelanggan:</strong></td><td>${data.pelanggan}</td></tr>
                                <tr><td><strong>PIC:</strong></td><td>${data.pic || '-'}</td></tr>
                                <tr><td><strong>Kontak:</strong></td><td>${data.kontak || '-'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td><strong>Lokasi:</strong></td><td>${data.lokasi || '-'}</td></tr>
                                <tr><td><strong>Tanggal Mulai:</strong></td><td>${data.tanggal_mulai}</td></tr>
                                <tr><td><strong>Tanggal Berakhir:</strong></td><td>${data.tanggal_berakhir}</td></tr>
                                <tr><td><strong>Status:</strong></td><td>${getStatusBadge(data.status)}</td></tr>
                                <tr><td><strong>Total Unit:</strong></td><td>${data.total_units || 0}</td></tr>
                            </table>
                        </div>
                    </div>
                `);
                $('#contractDetailModal').modal('show');
            } else {
                safeShowNotification('Gagal memuat detail kontrak', 'error');
            }
        },
        error: function() {
            safeShowNotification('Terjadi kesalahan saat memuat detail', 'error');
        }
    });
}
</script>
<?= $this->endSection() ?>
