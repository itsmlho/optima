<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .card-stats {
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .nav-tabs .nav-item {
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-link {
        padding: 1.25rem 2.5rem;
        border: 1px solid transparent;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        color: #6c757d;
        transition: all 0.15s ease-in-out;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: transparent;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
        color: #4e73df;
        background-color: #f8f9fc;
    }
    
    .nav-tabs .nav-link.active {
        color: white !important;
        background-color: #4e73df !important;
        border-color: #4e73df !important;
        box-shadow: 0 2px 4px rgba(78, 115, 223, 0.2);
    }
    
    #siloTable tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    #siloTable tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .file-preview {
        max-width: 100%;
        max-height: 300px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin-top: 10px;
    }
    
    .timeline-item {
        padding: 1rem;
        border-left: 3px solid #e9ecef;
        margin-bottom: 1rem;
        position: relative;
    }
    
    .timeline-item.completed {
        border-left-color: #28a745;
    }
    
    .timeline-item.active {
        border-left-color: #007bff;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 1rem;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #e9ecef;
    }
    
    .timeline-item.completed::before {
        background: #28a745;
    }
    
    .timeline-item.active::before {
        background: #007bff;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>


<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-stats border-left-success shadow h-100 py-2" onclick="filterByStatus('SILO_TERBIT')">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sudah Ada SILO</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-sudah-ada">
                            <?= $stats['sudah_ada'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-stats border-left-warning shadow h-100 py-2" onclick="filterByStatus('progres')">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Progres</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-progres">
                            <?= $stats['progres'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-stats border-left-danger shadow h-100 py-2" onclick="filterByStatus('BELUM_ADA')">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Belum Ada SILO</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-belum-ada">
                            <?= $stats['belum_ada'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert for Expiring Soon -->
<?php if (($stats['expiring_soon'] ?? 0) > 0): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Peringatan!</strong> Ada <?= $stats['expiring_soon'] ?> SILO yang akan expired dalam 30 hari ke depan.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Main Content Card -->
<div class="card table-card shadow mb-4">
    <div class="card-header py-3 ">
        <div class="d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar SILO</h6>
        <button class="btn btn-primary" onclick="showCreateModal()">
            <i class="fas fa-plus me-2"></i>Pengajuan SILO
        </button>
    </div>
    <div class="card-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="statusTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                    <i class="fas fa-list me-2"></i>Semua
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sudah-ada-tab" data-bs-toggle="tab" data-bs-target="#sudah-ada" type="button" role="tab">
                    <i class="fas fa-check-circle me-2"></i>Sudah Ada SILO
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="progres-tab" data-bs-toggle="tab" data-bs-target="#progres" type="button" role="tab">
                    <i class="fas fa-clock me-2"></i>Progres
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="belum-ada-tab" data-bs-toggle="tab" data-bs-target="#belum-ada" type="button" role="tab">
                    <i class="fas fa-exclamation-triangle me-2"></i>Belum Ada SILO
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="statusTabContent">
            <!-- Tab: Semua -->
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari unit, serial number, atau nomor SILO...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="PENGAJUAN_PJK3">Pengajuan ke PJK3</option>
                            <option value="TESTING_PJK3">Testing PJK3</option>
                            <option value="SURAT_KETERANGAN_PJK3">Surat Keterangan PJK3</option>
                            <option value="PENGAJUAN_UPTD">Pengajuan ke UPTD</option>
                            <option value="PROSES_UPTD">Proses UPTD</option>
                            <option value="SILO_TERBIT">SILO Terbit</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-secondary w-100" onclick="clearFilters()">
                            <i class="fas fa-redo me-2"></i>Reset Filter
                        </button>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="siloTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No Unit</th>
                                <th>Serial Number</th>
                                <th>Status</th>
                                <th>Nomor SILO</th>
                                <th>Tanggal Terbit</th>
                                <th>Tanggal Expired</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Sudah Ada SILO -->
            <div class="tab-pane fade" id="sudah-ada" role="tabpanel">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchInput2" placeholder="Cari unit, serial number, atau nomor SILO...">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary w-100" onclick="clearFiltersTab('sudah-ada')">
                            <i class="fas fa-redo me-2"></i>Reset Filter
                        </button>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="siloTable2" class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No Unit</th>
                                <th>Serial Number</th>
                                <th>Status</th>
                                <th>Nomor SILO</th>
                                <th>Tanggal Terbit</th>
                                <th>Tanggal Expired</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Progres -->
            <div class="tab-pane fade" id="progres" role="tabpanel">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchInput3" placeholder="Cari unit, serial number, atau nomor SILO...">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary w-100" onclick="clearFiltersTab('progres')">
                            <i class="fas fa-redo me-2"></i>Reset Filter
                        </button>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="siloTable3" class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No Unit</th>
                                <th>Serial Number</th>
                                <th>Status</th>
                                <th>Nomor SILO</th>
                                <th>Tanggal Terbit</th>
                                <th>Tanggal Expired</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Belum Ada SILO -->
            <div class="tab-pane fade" id="belum-ada" role="tabpanel">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchInput4" placeholder="Cari unit, serial number...">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary w-100" onclick="clearFiltersTab('belum-ada')">
                            <i class="fas fa-redo me-2"></i>Reset Filter
                        </button>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="siloTable4" class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No Unit</th>
                                <th>Serial Number</th>
                                <th>Status</th>
                                <th>Nomor SILO</th>
                                <th>Tanggal Terbit</th>
                                <th>Tanggal Expired</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Create SILO -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Buat Pengajuan SILO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unit_id" class="form-label">Pilih Unit <span class="text-danger">*</span></label>
                        <select class="form-select" id="unit_id" name="unit_id" required>
                            <option value="">-- Pilih Unit --</option>
                        </select>
                        <small class="text-muted">Hanya unit yang belum ada SILO aktif yang ditampilkan</small>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pengajuan_pjk3" class="form-label">Tanggal Pengajuan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_pengajuan_pjk3" name="tanggal_pengajuan_pjk3" required>
                    </div>
                    <div class="mb-3">
                        <label for="catatan_pengajuan_pjk3" class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan_pengajuan_pjk3" name="catatan_pengajuan_pjk3" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Update Status -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Status SILO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateForm">
                <input type="hidden" id="update_silo_id" name="silo_id">
                <input type="hidden" id="update_current_status" name="current_status">
                <div class="modal-body" id="updateModalBody">
                    <!-- Content will be dynamically loaded -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Detail SILO -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail SILO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Content will be dynamically loaded -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let siloTable;      // Tab Semua
let siloTable2;     // Tab Sudah Ada SILO
let siloTable3;     // Tab Progres
let siloTable4;     // Tab Belum Ada SILO
let currentStatus = 'all';

// Function to get column definitions
function getColumnDefinitions() {
    return [
        { 
            data: 'no_unit',
            render: function(data) {
                return data ? 'FL-' + data : '-';
            }
        },
        { 
            data: 'serial_number',
            render: function(data) {
                return data || '-';
            }
        },
        { 
            data: 'status',
            render: function(data) {
                if (!data || data === null) {
                    return '<span class="badge bg-danger">Belum Ada SILO</span>';
                }
                const statusLabels = {
                    'BELUM_ADA': 'Belum Ada SILO',
                    'PENGAJUAN_PJK3': 'Pengajuan ke PJK3',
                    'TESTING_PJK3': 'Testing PJK3',
                    'SURAT_KETERANGAN_PJK3': 'Surat Keterangan PJK3',
                    'PENGAJUAN_UPTD': 'Pengajuan ke UPTD',
                    'PROSES_UPTD': 'Proses UPTD',
                    'SILO_TERBIT': 'SILO Terbit',
                    'SILO_EXPIRED': 'SILO Expired'
                };
                const statusColors = {
                    'BELUM_ADA': 'danger',
                    'PENGAJUAN_PJK3': 'warning',
                    'TESTING_PJK3': 'warning',
                    'SURAT_KETERANGAN_PJK3': 'info',
                    'PENGAJUAN_UPTD': 'warning',
                    'PROSES_UPTD': 'warning',
                    'SILO_TERBIT': 'success',
                    'SILO_EXPIRED': 'danger'
                };
                const label = statusLabels[data] || data;
                const color = statusColors[data] || 'secondary';
                return '<span class="badge bg-' + color + '">' + label + '</span>';
            }
        },
        { 
            data: 'nomor_silo',
            render: function(data) {
                return data || '-';
            }
        },
        { 
            data: 'tanggal_terbit_silo',
            render: function(data) {
                return data ? formatDate(data) : '-';
            }
        },
        { 
            data: 'tanggal_expired_silo',
            render: function(data) {
                if (!data) return '-';
                const expired = new Date(data) < new Date();
                const expiringSoon = new Date(data) <= new Date(Date.now() + 30 * 24 * 60 * 60 * 1000);
                let badge = '';
                if (expired) badge = ' <span class="badge bg-danger">Expired</span>';
                else if (expiringSoon) badge = ' <span class="badge bg-warning">Expiring Soon</span>';
                return formatDate(data) + badge;
            }
        },
        { 
            data: 'id_silo',
            orderable: false,
            render: function(data, type, row) {
                let actions = '<div class="btn-group" role="group">';
                // For units without SILO, show create button
                if (!row.status || row.status === null) {
                    actions += '<button class="btn btn-sm btn-success" onclick="createSiloForUnit(' + row.id_silo + ')" title="Buat Pengajuan SILO"><i class="fas fa-plus"></i> Buat Pengajuan</button>';
                } else {
                    actions += '<button class="btn btn-sm btn-info" onclick="showDetail(' + data + ')" title="Detail"><i class="fas fa-eye"></i></button>';
                    if (row.status !== 'SILO_TERBIT' && row.status !== 'SILO_EXPIRED') {
                        actions += '<button class="btn btn-sm btn-primary" onclick="showUpdateModal(' + data + ')" title="Update Status"><i class="fas fa-edit"></i></button>';
                    }
                }
                actions += '</div>';
                return actions;
            }
        }
    ];
}

// Function to initialize DataTable
function initDataTable(tableId, searchInputId, status, filterStatusId = null) {
    // Check if table exists
    if ($(tableId).length === 0) {
        console.warn('Table ' + tableId + ' not found');
        return null;
    }
    
    return $(tableId).DataTable({
        processing: true,
        serverSide: false,
        deferRender: true, // Defer rendering for hidden tables
        ajax: {
            url: '<?= base_url('perizinan/get-silo-list') ?>',
            type: 'GET',
            data: function(d) {
                const requestData = {
                    status: status,
                    search: $('#' + searchInputId).val()
                };
                if (filterStatusId) {
                    requestData.filter_status = $('#' + filterStatusId).val();
                }
                console.log('DataTable AJAX Request for status ' + status + ':', requestData);
                return requestData;
            },
            dataSrc: function(json) {
                console.log('DataTable AJAX Response for status ' + status + ':', json);
                if (json && json.success && json.data) {
                    console.log('Returning ' + json.data.length + ' rows for status ' + status);
                    if (json.data.length > 0) {
                        console.log('First row sample:', json.data[0]);
                    }
                    return json.data;
                }
                console.warn('No data returned or invalid response for status ' + status);
                return [];
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error for status ' + status + ':', error, thrown);
                console.error('Response:', xhr.responseText);
                console.error('Status Code:', xhr.status);
            }
        },
        columns: getColumnDefinitions(),
        order: [[0, 'asc']],
        pageLength: 25,
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            emptyTable: "Tidak ada data SILO",
            zeroRecords: "Tidak ada data yang cocok"
        }
    });
}

$(document).ready(function() {
    // Initialize all DataTables immediately
    // Tab "Semua"
    siloTable = initDataTable('#siloTable', 'searchInput', 'all', 'filterStatus');

    // Tab "Sudah Ada SILO"
    siloTable2 = initDataTable('#siloTable2', 'searchInput2', 'SILO_TERBIT');

    // Tab "Progres"
    siloTable3 = initDataTable('#siloTable3', 'searchInput3', 'progres');

    // Tab "Belum Ada SILO" - Initialize with delay to ensure table exists
    setTimeout(function() {
        if ($('#siloTable4').length > 0) {
            siloTable4 = initDataTable('#siloTable4', 'searchInput4', 'BELUM_ADA');
        } else {
            console.warn('Table #siloTable4 not found, will initialize when tab is shown');
        }
    }, 100);

    // Search input handlers
    $('#searchInput').on('keyup', function() {
        if (siloTable) siloTable.ajax.reload();
    });
    
    $('#searchInput2').on('keyup', function() {
        if (siloTable2) siloTable2.ajax.reload();
    });
    
    $('#searchInput3').on('keyup', function() {
        if (siloTable3) siloTable3.ajax.reload();
    });
    
    $('#searchInput4').on('keyup', function() {
        if (siloTable4) siloTable4.ajax.reload();
    });

    // Filter status handler
    $('#filterStatus').on('change', function() {
        if (siloTable) siloTable.ajax.reload();
    });

    // Tab switching handler - initialize and reload table when tab is shown
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('data-bs-target');
        if (target === '#all') {
            currentStatus = 'all';
            if (siloTable) siloTable.ajax.reload();
        } else if (target === '#sudah-ada') {
            currentStatus = 'SILO_TERBIT';
            if (!siloTable2) {
                siloTable2 = initDataTable('#siloTable2', 'searchInput2', 'SILO_TERBIT');
            } else {
                siloTable2.ajax.reload();
            }
        } else if (target === '#progres') {
            currentStatus = 'progres';
            if (!siloTable3) {
                siloTable3 = initDataTable('#siloTable3', 'searchInput3', 'progres');
            } else {
                siloTable3.ajax.reload();
            }
        } else if (target === '#belum-ada') {
            currentStatus = 'BELUM_ADA';
            // Ensure table exists before initializing
            if ($('#siloTable4').length > 0) {
                if (!siloTable4) {
                    siloTable4 = initDataTable('#siloTable4', 'searchInput4', 'BELUM_ADA');
                } else {
                    siloTable4.ajax.reload();
                }
            } else {
                console.error('Table #siloTable4 not found in DOM');
            }
        }
    });

    // Load available units for create form
    loadAvailableUnits();

    // Create form handler
    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        createSilo();
    });
});

function filterByStatus(status) {
    currentStatus = status;
    if (status === 'SILO_TERBIT') {
        $('#sudah-ada-tab').tab('show');
        if (siloTable2) siloTable2.ajax.reload();
    } else if (status === 'progres') {
        $('#progres-tab').tab('show');
        if (siloTable3) siloTable3.ajax.reload();
    } else if (status === 'BELUM_ADA') {
        $('#belum-ada-tab').tab('show');
    } else {
        $('#all-tab').tab('show');
        if (siloTable) siloTable.ajax.reload();
    }
}

function clearFilters() {
    $('#searchInput').val('');
    $('#filterStatus').val('');
    currentStatus = 'all';
    $('#all-tab').tab('show');
    if (siloTable) siloTable.ajax.reload();
}

function clearFiltersTab(tabName) {
    if (tabName === 'sudah-ada') {
        $('#searchInput2').val('');
        if (siloTable2) siloTable2.ajax.reload();
    } else if (tabName === 'progres') {
        $('#searchInput3').val('');
        if (siloTable3) siloTable3.ajax.reload();
    } else if (tabName === 'belum-ada') {
        $('#searchInput4').val('');
        if (siloTable4) siloTable4.ajax.reload();
    }
}

function loadAvailableUnits() {
    $.ajax({
        url: '<?= base_url('perizinan/get-available-units') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const select = $('#unit_id');
                select.empty().append('<option value="">-- Pilih Unit --</option>');
                response.data.forEach(function(unit) {
                    select.append('<option value="' + unit.id + '">' + unit.label + '</option>');
                });
            }
        }
    });
}

function showCreateModal() {
    loadAvailableUnits();
    $('#createForm')[0].reset();
    new bootstrap.Modal(document.getElementById('createModal')).show();
}

function createSiloForUnit(unitId) {
    // Pre-fill unit_id in create modal
    // unitId is actually id_inventory_unit for units without SILO
    loadAvailableUnits();
    $('#createForm')[0].reset();
    // Wait a bit for units to load, then set the value
    setTimeout(function() {
        $('#unit_id').val(unitId);
        $('#tanggal_pengajuan_pjk3').val(new Date().toISOString().split('T')[0]);
    }, 500);
    new bootstrap.Modal(document.getElementById('createModal')).show();
}

function createSilo() {
    const formData = {
        unit_id: $('#unit_id').val(),
        tanggal_pengajuan_pjk3: $('#tanggal_pengajuan_pjk3').val(),
        catatan_pengajuan_pjk3: $('#catatan_pengajuan_pjk3').val()
    };

    $.ajax({
        url: '<?= base_url('perizinan/create-silo') ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                });
                $('#createModal').modal('hide');
                // Reload all tables
                if (siloTable) siloTable.ajax.reload();
                if (siloTable2) siloTable2.ajax.reload();
                if (siloTable3) siloTable3.ajax.reload();
                if (siloTable4) siloTable4.ajax.reload();
                // Reload page to update stats
                setTimeout(() => location.reload(), 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response?.message || 'Terjadi kesalahan saat membuat pengajuan'
            });
        }
    });
}

function showUpdateModal(siloId) {
    $.ajax({
        url: '<?= base_url('perizinan/get-silo-detail/') ?>' + siloId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const silo = response.data;
                const nextStatus = getNextStatus(silo.status);
                
                if (!nextStatus) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Dapat Update',
                        text: 'Status sudah mencapai tahap akhir'
                    });
                    return;
                }

                $('#update_silo_id').val(siloId);
                $('#update_current_status').val(silo.status);
                
                let html = '<div class="mb-3">';
                html += '<label class="form-label">Unit: <strong>FL-' + (silo.no_unit || 'N/A') + '</strong></label><br>';
                html += '<label class="form-label">Status Saat Ini: <span class="badge bg-secondary">' + getStatusLabel(silo.status) + '</span></label><br>';
                html += '<label class="form-label">Status Berikutnya: <span class="badge bg-primary">' + getStatusLabel(nextStatus) + '</span></label>';
                html += '</div>';

                // Add fields based on next status
                if (nextStatus === 'TESTING_PJK3') {
                    html += '<div class="mb-3"><label class="form-label">Tanggal Testing <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_testing_pjk3" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Hasil Testing</label>';
                    html += '<textarea class="form-control" name="hasil_testing_pjk3" rows="3"></textarea></div>';
                } else if (nextStatus === 'SURAT_KETERANGAN_PJK3') {
                    html += '<div class="mb-3"><label class="form-label">Nomor Surat Keterangan <span class="text-danger">*</span></label>';
                    html += '<input type="text" class="form-control" name="nomor_surat_keterangan_pjk3" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Tanggal Terbit <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_surat_keterangan_pjk3" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Upload File PJK3 (PDF/Image)</label>';
                    html += '<input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFile(this, \'pjk3\')"></div>';
                    html += '<div id="pjk3Preview"></div>';
                } else if (nextStatus === 'PENGAJUAN_UPTD') {
                    html += '<div class="mb-3"><label class="form-label">Tanggal Pengajuan <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_pengajuan_uptd" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Catatan</label>';
                    html += '<textarea class="form-control" name="catatan_pengajuan_uptd" rows="3"></textarea></div>';
                } else if (nextStatus === 'PROSES_UPTD') {
                    html += '<div class="mb-3"><label class="form-label">Tanggal Proses <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_proses_uptd" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Catatan</label>';
                    html += '<textarea class="form-control" name="catatan_proses_uptd" rows="3"></textarea></div>';
                } else if (nextStatus === 'SILO_TERBIT') {
                    html += '<div class="mb-3"><label class="form-label">Nomor SILO <span class="text-danger">*</span></label>';
                    html += '<input type="text" class="form-control" name="nomor_silo" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Tanggal Terbit <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_terbit_silo" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Tanggal Expired <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_expired_silo" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Upload File SILO (PDF/Image)</label>';
                    html += '<input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFile(this, \'silo\')"></div>';
                    html += '<div id="siloPreview"></div>';
                }

                html += '<div class="mb-3"><label class="form-label">Keterangan</label>';
                html += '<textarea class="form-control" name="keterangan" rows="2"></textarea></div>';

                $('#updateModalBody').html(html);
                $('#updateForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    updateSiloStatus(siloId);
                });
                
                new bootstrap.Modal(document.getElementById('updateModal')).show();
            }
        }
    });
}

function updateSiloStatus(siloId) {
    const form = $('#updateForm')[0];
    const formData = new FormData(form);
    formData.append('status', getNextStatus($('#update_current_status').val()));

    $.ajax({
        url: '<?= base_url('perizinan/update-silo-status/') ?>' + siloId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Upload file if exists
                const fileInput = form.querySelector('input[type="file"]');
                if (fileInput && fileInput.files.length > 0) {
                    uploadFile(siloId, fileInput.files[0], formData.get('status') === 'SURAT_KETERANGAN_PJK3' ? 'pjk3' : 'silo');
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                    $('#updateModal').modal('hide');
                    // Reload all tables
                    if (siloTable) siloTable.ajax.reload();
                    if (siloTable2) siloTable2.ajax.reload();
                    if (siloTable3) siloTable3.ajax.reload();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response?.message || 'Terjadi kesalahan saat update status'
            });
        }
    });
}

function uploadFile(siloId, file, fileType) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('file_type', fileType);

    $.ajax({
        url: '<?= base_url('perizinan/upload-file/') ?>' + siloId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Status dan file berhasil diupdate'
                });
                $('#updateModal').modal('hide');
                // Reload all tables
                if (siloTable) siloTable.ajax.reload();
                if (siloTable2) siloTable2.ajax.reload();
                if (siloTable3) siloTable3.ajax.reload();
                if (siloTable4) siloTable4.ajax.reload();
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal upload file'
            });
        }
    });
}

function showDetail(siloId) {
    $.ajax({
        url: '<?= base_url('perizinan/get-silo-detail/') ?>' + siloId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const silo = response.data;
                const history = response.history || [];
                
                let html = '<div class="row"><div class="col-md-6">';
                html += '<h6>Informasi Unit</h6>';
                html += '<table class="table table-sm">';
                html += '<tr><th>No Unit:</th><td>FL-' + (silo.no_unit || 'N/A') + '</td></tr>';
                html += '<tr><th>Serial Number:</th><td>' + (silo.serial_number || '-') + '</td></tr>';
                html += '<tr><th>Tipe Unit:</th><td>' + (silo.tipe_unit || '-') + '</td></tr>';
                html += '<tr><th>Model Unit:</th><td>' + (silo.model_unit || '-') + '</td></tr>';
                html += '</table></div>';
                
                html += '<div class="col-md-6"><h6>Informasi SILO</h6>';
                html += '<table class="table table-sm">';
                html += '<tr><th>Status:</th><td><span class="badge bg-' + getStatusColor(silo.status) + '">' + getStatusLabel(silo.status) + '</span></td></tr>';
                if (silo.nomor_silo) {
                    html += '<tr><th>Nomor SILO:</th><td>' + silo.nomor_silo + '</td></tr>';
                    html += '<tr><th>Tanggal Terbit:</th><td>' + formatDate(silo.tanggal_terbit_silo) + '</td></tr>';
                    html += '<tr><th>Tanggal Expired:</th><td>' + formatDate(silo.tanggal_expired_silo) + '</td></tr>';
                    if (silo.file_silo) {
                        html += '<tr><th>File SILO:</th><td><a href="<?= base_url('perizinan/download-file/') ?>' + siloId + '/silo" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-download"></i> Download</a></td></tr>';
                    }
                }
                if (silo.nomor_surat_keterangan_pjk3) {
                    html += '<tr><th>Nomor Surat PJK3:</th><td>' + silo.nomor_surat_keterangan_pjk3 + '</td></tr>';
                    html += '<tr><th>Tanggal Surat PJK3:</th><td>' + formatDate(silo.tanggal_surat_keterangan_pjk3) + '</td></tr>';
                    if (silo.file_surat_keterangan_pjk3) {
                        html += '<tr><th>File PJK3:</th><td><a href="<?= base_url('perizinan/download-file/') ?>' + siloId + '/pjk3" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-download"></i> Download</a></td></tr>';
                    }
                }
                html += '</table></div></div>';
                
                html += '<hr><h6>Timeline Proses</h6><div class="timeline">';
                // Add timeline items based on status
                const statuses = ['PENGAJUAN_PJK3', 'TESTING_PJK3', 'SURAT_KETERANGAN_PJK3', 'PENGAJUAN_UPTD', 'PROSES_UPTD', 'SILO_TERBIT'];
                statuses.forEach(function(status, index) {
                    const isCompleted = getStatusIndex(silo.status) > index;
                    const isActive = getStatusIndex(silo.status) === index;
                    html += '<div class="timeline-item ' + (isCompleted ? 'completed' : (isActive ? 'active' : '')) + '">';
                    html += '<strong>' + getStatusLabel(status) + '</strong><br>';
                    if (status === 'PENGAJUAN_PJK3' && silo.tanggal_pengajuan_pjk3) {
                        html += formatDate(silo.tanggal_pengajuan_pjk3) + '<br>';
                        if (silo.catatan_pengajuan_pjk3) html += '<small>' + silo.catatan_pengajuan_pjk3 + '</small>';
                    }
                    // Add other status details...
                    html += '</div>';
                });
                html += '</div>';
                
                $('#detailModalBody').html(html);
                new bootstrap.Modal(document.getElementById('detailModal')).show();
            }
        }
    });
}

function previewFile(input, type) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = '';
            if (file.type.startsWith('image/')) {
                preview = '<img src="' + e.target.result + '" class="file-preview">';
            } else if (file.type === 'application/pdf') {
                preview = '<iframe src="' + e.target.result + '" class="file-preview" style="width:100%;height:300px;"></iframe>';
            }
            $('#' + type + 'Preview').html(preview);
        };
        reader.readAsDataURL(file);
    }
}

// Helper functions
function getNextStatus(currentStatus) {
    const workflow = {
        'BELUM_ADA': 'PENGAJUAN_PJK3',
        'PENGAJUAN_PJK3': 'TESTING_PJK3',
        'TESTING_PJK3': 'SURAT_KETERANGAN_PJK3',
        'SURAT_KETERANGAN_PJK3': 'PENGAJUAN_UPTD',
        'PENGAJUAN_UPTD': 'PROSES_UPTD',
        'PROSES_UPTD': 'SILO_TERBIT'
    };
    return workflow[currentStatus] || null;
}

function getStatusLabel(status) {
    const labels = {
        'BELUM_ADA': 'Belum Ada SILO',
        'PENGAJUAN_PJK3': 'Pengajuan ke PJK3',
        'TESTING_PJK3': 'Testing PJK3',
        'SURAT_KETERANGAN_PJK3': 'Surat Keterangan PJK3',
        'PENGAJUAN_UPTD': 'Pengajuan ke UPTD',
        'PROSES_UPTD': 'Proses UPTD',
        'SILO_TERBIT': 'SILO Terbit',
        'SILO_EXPIRED': 'SILO Expired'
    };
    return labels[status] || status;
}

function getStatusColor(status) {
    const colors = {
        'BELUM_ADA': 'danger',
        'PENGAJUAN_PJK3': 'warning',
        'TESTING_PJK3': 'warning',
        'SURAT_KETERANGAN_PJK3': 'info',
        'PENGAJUAN_UPTD': 'warning',
        'PROSES_UPTD': 'warning',
        'SILO_TERBIT': 'success',
        'SILO_EXPIRED': 'danger'
    };
    return colors[status] || 'secondary';
}

function getStatusIndex(status) {
    const statuses = ['PENGAJUAN_PJK3', 'TESTING_PJK3', 'SURAT_KETERANGAN_PJK3', 'PENGAJUAN_UPTD', 'PROSES_UPTD', 'SILO_TERBIT'];
    return statuses.indexOf(status);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>
<?= $this->endSection() ?>
