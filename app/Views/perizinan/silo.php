<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
    <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-success-soft" onclick="filterByStatus('SILO_TERBIT')" style="cursor:pointer;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-check-circle stat-icon text-success"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-sudah-ada">
                        <?= $stats['sudah_ada'] ?? 0 ?>
                    </div>
                    <div class="text-muted">Sudah Ada SILO</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft" onclick="filterByStatus('progres')" style="cursor:pointer;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-clock stat-icon text-warning"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-progres">
                        <?= $stats['progres'] ?? 0 ?>
                    </div>
                    <div class="text-muted">Progres</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-danger-soft" onclick="filterByStatus('BELUM_ADA')" style="cursor:pointer;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-exclamation-triangle stat-icon text-danger"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-belum-ada">
                        <?= $stats['belum_ada'] ?? 0 ?>
                    </div>
                    <div class="text-muted">Belum Ada SILO</div>
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
<div class="card table-card mb-4">
    
        <!-- Tab Navigation -->
        <div class="card table-card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <ul class="nav nav-tabs flex-grow-1" id="statusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                            <i class="fas fa-list"></i>
                            <span>Semua</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sudah-ada-tab" data-bs-toggle="tab" data-bs-target="#sudah-ada" type="button" role="tab" aria-controls="sudah-ada" aria-selected="false">
                            <i class="fas fa-check-circle"></i>
                            <span>Sudah Ada SILO</span>
                            <span class="badge bg-success ms-2" id="badge-sudah-ada"><?= $stats['sudah_ada'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="progres-tab" data-bs-toggle="tab" data-bs-target="#progres" type="button" role="tab" aria-controls="progres" aria-selected="false">
                            <i class="fas fa-clock"></i>
                            <span>Progres</span>
                            <span class="badge bg-warning ms-2" id="badge-progres"><?= $stats['progres'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="belum-ada-tab" data-bs-toggle="tab" data-bs-target="#belum-ada" type="button" role="tab" aria-controls="belum-ada" aria-selected="false">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Belum Ada SILO</span>
                            <span class="badge bg-danger ms-2" id="badge-belum-ada"><?= $stats['belum_ada'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="akan-expired-tab" data-bs-toggle="tab" data-bs-target="#akan-expired" type="button" role="tab" aria-controls="akan-expired" aria-selected="false">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Akan Expired (30d)</span>
                            <span class="badge bg-warning ms-2" id="badge-akan-expired"><?= $stats['expiring_soon'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sudah-expired-tab" data-bs-toggle="tab" data-bs-target="#sudah-expired" type="button" role="tab" aria-controls="sudah-expired" aria-selected="false">
                            <i class="fas fa-times-circle"></i>
                            <span>Sudah Expired</span>
                            <span class="badge bg-danger ms-2" id="badge-sudah-expired"><?= $stats['expired'] ?? 0 ?></span>
                        </button>
                    </li>
                </ul>
            </div>

        <!-- Tab Content -->
        <div class="card-body p-3">
            <div class="tab-content" id="statusTabContent">
                <!-- Tab: Semua -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <!-- Filter Controls -->
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-status-all">Status</label>
                                <select id="filter-status-all" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="PENGAJUAN_PJK3">Pengajuan ke PJK3</option>
                                    <option value="SURAT_KETERANGAN_PJK3">Surat Keterangan PJK3</option>
                                    <option value="PENGAJUAN_UPTD">Pengajuan DISNAKER</option>
                                    <option value="SILO_TERBIT">SILO Terbit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="filter-departemen-all" class="form-label small fw-semibold text-muted">Departemen</label>
                                <select id="filter-departemen-all" class="form-select form-select-sm">
                                    <option value="">Semua Departemen</option>
                                    <?php if (isset($departments) && is_array($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= esc($dept['id_departemen']) ?>"><?= esc($dept['nama_departemen']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-expired-all">Filter Expired</label>
                                <select id="filter-expired-all" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    <option value="7">Akan Expired < 7 Hari</option>
                                    <option value="30">Akan Expired < 1 Bulan</option>
                                    <option value="90">Akan Expired < 3 Bulan</option>
                                    <option value="180">Akan Expired < 6 Bulan</option>
                                    <option value="expired">Sudah Expired</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="siloTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No Unit</th>
                                    <th>Serial Number</th>
                                    <th>Tipe Unit</th>
                                    <th>Departemen</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Nomor SILO</th>
                                    <th>Tanggal Terbit</th>
                                    <th>Tanggal Expired</th>
                                    <th width="10%">Aksi</th>
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
                    <!-- Filter Controls -->
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="filter-departemen-sudah-ada" class="form-label small fw-semibold text-muted">Departemen</label>
                                <select id="filter-departemen-sudah-ada" class="form-select form-select-sm">
                                    <option value="">Semua Departemen</option>
                                    <?php if (isset($departments) && is_array($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= esc($dept['id_departemen']) ?>"><?= esc($dept['nama_departemen']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-expired-sudah-ada">Filter Expired</label>
                                <select id="filter-expired-sudah-ada" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    <option value="7">Akan Expired < 7 Hari</option>
                                    <option value="30">Akan Expired < 1 Bulan</option>
                                    <option value="90">Akan Expired < 3 Bulan</option>
                                    <option value="180">Akan Expired < 6 Bulan</option>
                                    <option value="expired">Sudah Expired</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="siloTable2" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No Unit</th>
                                    <th>Serial Number</th>
                                    <th>Tipe Unit</th>
                                    <th>Departemen</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Nomor SILO</th>
                                    <th>Tanggal Terbit</th>
                                    <th>Tanggal Expired</th>
                                    <th width="10%">Aksi</th>
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
                    <!-- Filter Controls -->
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="filter-status-progres" class="form-label small fw-semibold text-muted">Status</label>
                                <select id="filter-status-progres" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="PENGAJUAN_PJK3">Pengajuan ke PJK3</option>
                                    <option value="SURAT_KETERANGAN_PJK3">Surat Keterangan PJK3</option>
                                    <option value="PENGAJUAN_UPTD">Pengajuan DISNAKER</option>
                                    <option value="SILO_TERBIT">SILO Terbit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="filter-departemen-progres" class="form-label small fw-semibold text-muted">Departemen</label>
                                <select id="filter-departemen-progres" class="form-select form-select-sm">
                                    <option value="">Semua Departemen</option>
                                    <?php if (isset($departments) && is_array($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= esc($dept['id_departemen']) ?>"><?= esc($dept['nama_departemen']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="siloTable3" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No Unit</th>
                                    <th>Serial Number</th>
                                    <th>Tipe Unit</th>
                                    <th>Departemen</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Nomor SILO</th>
                                    <th>Tanggal Terbit</th>
                                    <th>Tanggal Expired</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Akan Expired (30d) -->
                <div class="tab-pane fade" id="akan-expired" role="tabpanel">
                    <!-- Filter Controls -->
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="filter-departemen-akan-expired" class="form-label small fw-semibold text-muted">Departemen</label>
                                <select id="filter-departemen-akan-expired" class="form-select form-select-sm">
                                    <option value="">Semua Departemen</option>
                                    <?php if (isset($departments) && is_array($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= esc($dept['id_departemen']) ?>"><?= esc($dept['nama_departemen']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="siloTable5" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No Unit</th>
                                    <th>Serial Number</th>
                                    <th>Tipe Unit</th>
                                    <th>Departemen</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Nomor SILO</th>
                                    <th>Tanggal Terbit</th>
                                    <th>Tanggal Expired</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Sudah Expired -->
                <div class="tab-pane fade" id="sudah-expired" role="tabpanel">
                    <!-- Filter Controls -->
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="filter-departemen-sudah-expired" class="form-label small fw-semibold text-muted">Departemen</label>
                                <select id="filter-departemen-sudah-expired" class="form-select form-select-sm">
                                    <option value="">Semua Departemen</option>
                                    <?php if (isset($departments) && is_array($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= esc($dept['id_departemen']) ?>"><?= esc($dept['nama_departemen']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="siloTable6" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No Unit</th>
                                    <th>Serial Number</th>
                                    <th>Tipe Unit</th>
                                    <th>Departemen</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Nomor SILO</th>
                                    <th>Tanggal Terbit</th>
                                    <th>Tanggal Expired</th>
                                    <th width="10%">Aksi</th>
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
                <!-- Filter Controls -->
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="filter-departemen-belum-ada" class="form-label small fw-semibold text-muted">Departemen</label>
                            <select id="filter-departemen-belum-ada" class="form-select form-select-sm">
                                <option value="">Semua Departemen</option>
                                <?php if (isset($departments) && is_array($departments)): ?>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= esc($dept['id_departemen']) ?>"><?= esc($dept['nama_departemen']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="siloTable4" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No Unit</th>
                                <th>Serial Number</th>
                                <th>Tipe Unit</th>
                                <th>Departemen</th>
                                <th>Nama Perusahaan</th>
                                <th>Alamat</th>
                                <th width="10%">Aksi</th>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Buat Pengajuan SILO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unit_ids" class="form-label">Pilih Unit <span class="text-danger">*</span></label>
                        <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                            <input type="text" id="unit_search" class="form-control form-control-sm mb-2" placeholder="Cari unit...">
                            <div id="unit_checkboxes">
                                <!-- Units will be loaded here -->
                            </div>
                        </div>
                        <small class="text-muted">Hanya unit yang belum ada SILO aktif yang ditampilkan. Bisa pilih multiple unit.</small>
                    </div>
                    <div class="mb-3">
                        <label for="nama_pt_pjk3" class="form-label">Nama PT PJK3 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_pt_pjk3" name="nama_pt_pjk3" placeholder="Contoh: PT. GAHARU SAKTI PRATAMA" required>
                        <small class="text-muted">Nama perusahaan PJK3 yang melakukan pemeriksaan dan testing</small>
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
let siloTable5;     // Tab Akan Expired (30d)
let siloTable6;     // Tab Sudah Expired
let currentStatus = 'all';

// Function to get column definitions
function getColumnDefinitions(tableId) {
    // For "Belum Ada SILO" table, use different columns (no SILO data)
    if (tableId === '#siloTable4') {
        return [
            { 
                data: 'no_unit',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'serial_number',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'tipe_unit',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'departemen',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'nama_perusahaan',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'alamat',
                render: function(data) {
                    if (!data) return '-';
                    return data.length > 40 ? data.substring(0, 40) + '...' : data;
                }
            },
            { 
                data: 'id_silo',
                orderable: false,
                render: function(data, type, row) {
                    return '<div class="btn-group-vertical btn-group-sm" role="group">' +
                        '<button type="button" class="btn btn-sm btn-success" onclick="createSiloForUnit(' + row.id_silo + ')" title="Buat Pengajuan SILO"><i class="fas fa-plus me-1"></i> Buat Pengajuan</button>' +
                        '</div>';
                }
            }
        ];
    }
    
    // For other tables (with SILO data)
    return [
        { 
            data: 'no_unit',
            render: function(data) {
                return data ?  + data : '-';
            }
        },
            { 
                data: 'serial_number',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'tipe_unit',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'departemen',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'nama_perusahaan',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'alamat',
                render: function(data) {
                    if (!data) return '-';
                    // Truncate long addresses
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
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
                    'SURAT_KETERANGAN_PJK3': 'Surat Keterangan PJK3',
                    'PENGAJUAN_UPTD': 'Pengajuan DISNAKER',
                    'SILO_TERBIT': 'SILO Terbit',
                    'SILO_EXPIRED': 'SILO Expired'
                };
                const statusColors = {
                    'BELUM_ADA': 'danger',
                    'PENGAJUAN_PJK3': 'warning',
                    'SURAT_KETERANGAN_PJK3': 'info',
                    'PENGAJUAN_UPTD': 'warning',
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
                    let buttons = [];
                    
                    // For units without SILO, show create button
                    if (!row.status || row.status === null) {
                        buttons.push('<button type="button" class="btn btn-sm btn-success" onclick="createSiloForUnit(' + row.id_silo + ')" title="Buat Pengajuan SILO"><i class="fas fa-plus me-1"></i>Buat Pengajuan</button>');
                    } else {
                        // Always show detail button
                        buttons.push('<button type="button" class="btn btn-sm btn-info" onclick="showDetail(' + data + ')" title="Detail"><i class="fas fa-eye me-1"></i>Detail</button>');
                        
                        // Show update button based on status (stage by stage)
                        if (row.status !== 'SILO_TERBIT' && row.status !== 'SILO_EXPIRED') {
                            const actionLabel = getActionButtonLabel(row.status);
                            if (actionLabel) {
                                buttons.push('<button type="button" class="btn btn-sm btn-primary" onclick="showUpdateModal(' + data + ')" title="' + actionLabel + '"><i class="fas fa-arrow-right me-1"></i>' + actionLabel + '</button>');
                            }
                        }
                        
                    }
                    
                    return '<div class="btn-group-vertical btn-group-sm" role="group">' + buttons.join('') + '</div>';
                }
            }
        ];
}

// Function to initialize DataTable
function initDataTable(tableId, searchInputId, status, filterStatusId = null, filterDepartemenId = null) {
    // Check if table exists
    if ($(tableId).length === 0) {
        console.warn('Table ' + tableId + ' not found');
        return null;
    }
    
    // Determine which tab this is for filter IDs
    let statusFilterId = filterStatusId;
    let deptFilterId = filterDepartemenId;
    let expiredFilterId = null;
    
    if (tableId === '#siloTable') {
        statusFilterId = 'filter-status-all';
        deptFilterId = 'filter-departemen-all';
        expiredFilterId = 'filter-expired-all';
    } else if (tableId === '#siloTable2') {
        deptFilterId = 'filter-departemen-sudah-ada';
        expiredFilterId = 'filter-expired-sudah-ada';
    } else if (tableId === '#siloTable3') {
        statusFilterId = 'filter-status-progres';
        deptFilterId = 'filter-departemen-progres';
    } else if (tableId === '#siloTable4') {
        deptFilterId = 'filter-departemen-belum-ada';
    } else if (tableId === '#siloTable5') {
        deptFilterId = 'filter-departemen-akan-expired';
    } else if (tableId === '#siloTable6') {
        deptFilterId = 'filter-departemen-sudah-expired';
    }
    
    return $(tableId).DataTable({
        processing: true,
        serverSide: false,
        deferRender: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>', // l = length, f = filter/search, r = processing, t = table, i = info, p = pagination
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            emptyTable: "Tidak ada data SILO",
            zeroRecords: "Tidak ada data yang cocok",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Selanjutnya",
                last: "Terakhir"
            }
        },
        ajax: {
            url: '<?= base_url('perizinan/get-silo-list') ?>',
            type: 'GET',
            data: function(d) {
                const requestData = {
                    status: status,
                    search: ''
                };
                
                // Since serverSide: false, we don't use d.search.value
                // Search is handled client-side by DataTable itself
                // We only send filters to backend
                
                if (statusFilterId && $('#' + statusFilterId).length) {
                    const statusValue = $('#' + statusFilterId).val();
                    if (statusValue) requestData.filter_status = statusValue;
                }
                if (deptFilterId && $('#' + deptFilterId).length) {
                    const deptValue = $('#' + deptFilterId).val();
                    if (deptValue) requestData.filter_departemen = deptValue;
                }
                if (expiredFilterId && $('#' + expiredFilterId).length) {
                    const expiredValue = $('#' + expiredFilterId).val();
                    if (expiredValue) {
                        if (expiredValue === 'expired') {
                            requestData.expired = true;
                        } else {
                            requestData.expiring_soon = expiredValue;
                        }
                    }
                }
                return requestData;
            },
            dataSrc: function(json) {
                if (json && json.success && json.data) {
                    return json.data;
                }
                return [];
            },
            error: function(xhr, error, thrown) {
                // Ignore abort errors (happens when table is destroyed/reinitialized)
                if (error !== 'abort' && thrown !== 'abort') {
                    console.error('DataTable AJAX Error for status ' + status + ':', error, thrown);
                }
            }
        },
        columns: getColumnDefinitions(tableId),
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        responsive: true,
        autoWidth: false,
        deferRender: true,  // Only render rows when they are needed (prevents stacking)
        deferRender: true  // Only render rows when they are needed (prevents stacking)
    });
}

$(document).ready(function() {
    // Initialize only the active tab (Semua) immediately
    // Tab "Semua" is active by default
    siloTable = initDataTable('#siloTable', 'searchInput', 'all', 'filterStatus');

    // Initialize other tabs only when they are shown (lazy loading)
    // This prevents tables from stacking/overlapping

    // Filter handlers
    $('#filter-status-all').on('change', function() {
        if (siloTable) siloTable.ajax.reload();
    });
    
    $('#filter-departemen-all').on('change', function() {
        if (siloTable) siloTable.ajax.reload();
    });
    
    $('#filter-departemen-sudah-ada').on('change', function() {
        if (siloTable2) siloTable2.ajax.reload();
    });
    
    $('#filter-expired-sudah-ada').on('change', function() {
        if (siloTable2) siloTable2.ajax.reload();
    });
    
    $('#filter-status-progres').on('change', function() {
        if (siloTable3) siloTable3.ajax.reload();
    });
    
    $('#filter-departemen-progres').on('change', function() {
        if (siloTable3) siloTable3.ajax.reload();
    });
    
    $('#filter-departemen-belum-ada').on('change', function() {
        if (siloTable4) siloTable4.ajax.reload();
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
        } else if (target === '#akan-expired') {
            currentStatus = 'akan-expired';
            if (!siloTable5) {
                siloTable5 = initDataTable('#siloTable5', 'searchInput5', 'akan-expired');
            } else {
                siloTable5.ajax.reload();
            }
        } else if (target === '#sudah-expired') {
            currentStatus = 'sudah-expired';
            if (!siloTable6) {
                siloTable6 = initDataTable('#siloTable6', 'searchInput6', 'sudah-expired');
            } else {
                siloTable6.ajax.reload();
            }
        }
    });
    
    // Filter handlers for expired tabs
    $('#filter-departemen-akan-expired').on('change', function() {
        if (siloTable5) siloTable5.ajax.reload();
    });
    
    $('#filter-departemen-sudah-expired').on('change', function() {
        if (siloTable6) siloTable6.ajax.reload();
    });
    
    // Ensure "Semua" tab is active on page load (in case of refresh)
    // This prevents other tabs from showing their tables stacked
    $('#all-tab').tab('show');

    // Create form handler
    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        createSilo();
    });
    
    // Load units when modal is shown (only once)
    $('#createModal').on('show.bs.modal', function() {
        // Only load if checkboxes are empty
        if ($('#unit_checkboxes').children().length === 0) {
            loadAvailableUnits();
        }
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

let allAvailableUnits = [];

function loadAvailableUnits() {
    // Show loading
    $('#unit_checkboxes').html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div> <span class="ms-2">Memuat unit...</span></div>');
    
    $.ajax({
        url: '<?= base_url('perizinan/get-available-units') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                allAvailableUnits = response.data;
                renderUnitCheckboxes(allAvailableUnits);
                
                // Search functionality
                $('#unit_search').off('keyup').on('keyup', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    const filtered = allAvailableUnits.filter(function(unit) {
                        return unit.label.toLowerCase().includes(searchTerm);
                    });
                    renderUnitCheckboxes(filtered);
                });
            } else {
                $('#unit_checkboxes').html('<div class="text-danger text-center py-2">Gagal memuat unit: ' + (response.message || 'Unknown error') + '</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading units:', error, xhr.responseText);
            $('#unit_checkboxes').html('<div class="text-danger text-center py-2">Error memuat unit. Silakan refresh halaman.</div>');
        }
    });
}

function renderUnitCheckboxes(units) {
    const container = $('#unit_checkboxes');
    
    // Save currently checked unit IDs before clearing
    const checkedUnits = [];
    $('.unit-checkbox:checked').each(function() {
        checkedUnits.push($(this).val());
    });
    
    container.empty();
    
    if (units.length === 0) {
        container.html('<div class="text-muted text-center py-2">Tidak ada unit tersedia</div>');
        return;
    }
    
    units.forEach(function(unit) {
        const isChecked = checkedUnits.includes(String(unit.id));
        const checkbox = $('<div class="form-check mb-2">')
            .append($('<input>', {
                type: 'checkbox',
                class: 'form-check-input unit-checkbox',
                id: 'unit_' + unit.id,
                value: unit.id,
                name: 'unit_ids[]',
                checked: isChecked
            }))
            .append($('<label>', {
                class: 'form-check-label',
                for: 'unit_' + unit.id,
                text: unit.label
            }));
        container.append(checkbox);
    });
}

function showCreateModal() {
    $('#createForm')[0].reset();
    $('#unit_search').val('');
    $('#unit_checkboxes').empty();
    new bootstrap.Modal(document.getElementById('createModal')).show();
    // Load units after modal is shown
    setTimeout(function() {
        loadAvailableUnits();
    }, 300);
}

function createSiloForUnit(unitId) {
    // Pre-fill unit_id in create modal
    // unitId is actually id_inventory_unit for units without SILO
    $('#createForm')[0].reset();
    $('#unit_search').val('');
    $('#unit_checkboxes').empty();
    new bootstrap.Modal(document.getElementById('createModal')).show();
    
    // Load units after modal is shown, then check the checkbox
    setTimeout(function() {
        loadAvailableUnits();
        // Wait for units to load, then check the checkbox
        setTimeout(function() {
            $('#unit_' + unitId).prop('checked', true);
            $('#tanggal_pengajuan_pjk3').val(new Date().toISOString().split('T')[0]);
        }, 500);
    }, 300);
}

function createSilo() {
    // Get selected unit IDs
    const selectedUnits = [];
    $('.unit-checkbox:checked').each(function() {
        selectedUnits.push($(this).val());
    });
    
    if (selectedUnits.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih minimal 1 unit'
        });
        return;
    }
    
    const namaPtPjk3 = $('#nama_pt_pjk3').val();
    if (!namaPtPjk3 || namaPtPjk3.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Nama PT PJK3 harus diisi'
        });
        return;
    }
    
    const formData = {
        unit_ids: selectedUnits,
        nama_pt_pjk3: namaPtPjk3.trim(),
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
                if (siloTable5) siloTable5.ajax.reload();
                if (siloTable6) siloTable6.ajax.reload();
                // Update badge counts
                updateTabBadges();
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
                html += '<label class="form-label">Unit: <strong>' + (silo.no_unit || 'N/A') + '</strong></label><br>';
                html += '<label class="form-label">Status Saat Ini: <span class="badge bg-secondary">' + getStatusLabel(silo.status) + '</span></label><br>';
                html += '<label class="form-label">Status Berikutnya: <span class="badge bg-primary">' + getStatusLabel(nextStatus) + '</span></label>';
                html += '</div>';

                // Add fields based on next status
                if (nextStatus === 'SURAT_KETERANGAN_PJK3') {
                    html += '<div class="mb-3"><label class="form-label">Nomor Surat Keterangan <span class="text-danger">*</span></label>';
                    html += '<input type="text" class="form-control" name="nomor_surat_keterangan_pjk3" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Tanggal Terbit <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_surat_keterangan_pjk3" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Upload File PJK3 (PDF/Image)</label>';
                    html += '<input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFile(this, \'pjk3\')"></div>';
                    html += '<div id="pjk3Preview"></div>';
                } else if (nextStatus === 'PENGAJUAN_UPTD') {
                    html += '<div class="mb-3"><label class="form-label">Lokasi DISNAKER <span class="text-danger">*</span></label>';
                    html += '<input type="text" class="form-control" name="lokasi_disnaker" placeholder="Masukan alamat / lokasi DISNAKER." required></div>';
                    html += '<div class="mb-3"><label class="form-label">Tanggal Pengajuan DISNAKER <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_pengajuan_uptd" required></div>';
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
                    if (siloTable4) siloTable4.ajax.reload();
                    if (siloTable5) siloTable5.ajax.reload();
                    if (siloTable6) siloTable6.ajax.reload();
                    // Update badge counts
                    updateTabBadges();
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
    // Check file size (15MB = 15728640 bytes)
    const maxSize = 15 * 1024 * 1024;
    if (file.size > maxSize) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 15MB. File Anda: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB'
        });
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('file_type', fileType);

    // Show loading
    Swal.fire({
        title: 'Uploading...',
        text: 'Mohon tunggu, file sedang diupload',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '<?= base_url('perizinan/upload-file/') ?>' + siloId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'File berhasil diupload'
                });
                $('#updateModal').modal('hide');
                // Reload all tables
                if (siloTable) siloTable.ajax.reload();
                if (siloTable2) siloTable2.ajax.reload();
                if (siloTable3) siloTable3.ajax.reload();
                if (siloTable4) siloTable4.ajax.reload();
                if (siloTable5) siloTable5.ajax.reload();
                if (siloTable6) siloTable6.ajax.reload();
                // Update badge counts
                updateTabBadges();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Gagal upload file'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Upload error:', error, xhr.responseText);
            let errorMessage = 'Gagal upload file';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    errorMessage = 'Error: ' + xhr.status + ' - ' + error;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
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
                html += '<tr><th>No Unit:</th><td><strong>' + (silo.no_unit || 'N/A') + '</strong></td></tr>';
                html += '<tr><th>Serial Number:</th><td>' + (silo.serial_number || '-') + '</td></tr>';
                html += '<tr><th>Tahun Unit:</th><td>' + (silo.tahun_unit || '-') + '</td></tr>';
                html += '<tr><th>Tipe Unit:</th><td>' + (silo.tipe_unit || '-') + '</td></tr>';
                html += '<tr><th>Jenis Unit:</th><td>' + (silo.jenis_unit || '-') + '</td></tr>';
                html += '<tr><th>Model Unit:</th><td>' + (silo.model_unit || '-') + '</td></tr>';
                html += '<tr><th>Kapasitas Unit:</th><td>' + (silo.kapasitas_unit || '-') + '</td></tr>';
                html += '<tr><th>Departemen:</th><td>' + (silo.departemen || '-') + '</td></tr>';
                html += '<tr><th>Lokasi Unit:</th><td>' + (silo.lokasi_unit || '-') + '</td></tr>';
                html += '<tr><th>Nama Perusahaan:</th><td>' + (silo.nama_perusahaan || '-') + '</td></tr>';
                if (silo.alamat) {
                    html += '<tr><th>Alamat Customer:</th><td>' + silo.alamat;
                    if (silo.kota) {
                        html += ', ' + silo.kota;
                    }
                    if (silo.provinsi) {
                        html += ', ' + silo.provinsi;
                    }
                    html += '</td></tr>';
                }
                html += '</table></div>';
                
                html += '<div class="col-md-6"><h6>Informasi SILO</h6>';
                html += '<table class="table table-sm">';
                html += '<tr><th>Status:</th><td><span class="badge bg-' + getStatusColor(silo.status) + '">' + getStatusLabel(silo.status) + '</span></td></tr>';
                if (silo.nomor_silo) {
                    html += '<tr><th>Nomor SILO:</th><td>' + silo.nomor_silo + '</td></tr>';
                    html += '<tr><th>Tanggal Terbit:</th><td>' + formatDate(silo.tanggal_terbit_silo) + '</td></tr>';
                    html += '<tr><th>Tanggal Expired:</th><td>' + formatDate(silo.tanggal_expired_silo) + '</td></tr>';
                }
                if (silo.nama_pt_pjk3) {
                    html += '<tr><th>Nama PT PJK3:</th><td>' + silo.nama_pt_pjk3 + '</td></tr>';
                }
                if (silo.nomor_surat_keterangan_pjk3) {
                    html += '<tr><th>Nomor Surat PJK3:</th><td>' + silo.nomor_surat_keterangan_pjk3 + '</td></tr>';
                    html += '<tr><th>Tanggal Surat PJK3:</th><td>' + formatDate(silo.tanggal_surat_keterangan_pjk3) + '</td></tr>';
                }
                if (silo.tanggal_pengajuan_uptd) {
                    html += '<tr><th>Tanggal Pengajuan DISNAKER:</th><td>' + formatDate(silo.tanggal_pengajuan_uptd) + '</td></tr>';
                    if (silo.lokasi_disnaker) {
                        html += '<tr><th>Lokasi DISNAKER:</th><td><strong>' + silo.lokasi_disnaker + '</strong></td></tr>';
                    }
                }
                html += '</table></div></div>';
                
                // Horizontal Timeline dengan Tanggal
                html += '<hr><h6 class="mb-3">Timeline Proses</h6>';
                html += '<div class="d-flex justify-content-between align-items-start mb-4" style="position: relative; padding: 1.5rem 0;">';
                const statuses = ['PENGAJUAN_PJK3', 'SURAT_KETERANGAN_PJK3', 'PENGAJUAN_UPTD', 'SILO_TERBIT'];
                const currentIndex = getStatusIndex(silo.status);
                
                // Function to get date for each status
                function getStatusDate(status, silo) {
                    switch(status) {
                        case 'PENGAJUAN_PJK3': return silo.tanggal_pengajuan_pjk3 || null;
                        case 'SURAT_KETERANGAN_PJK3': return silo.tanggal_surat_keterangan_pjk3 || null;
                        case 'PENGAJUAN_UPTD': return silo.tanggal_pengajuan_uptd || null;
                        case 'SILO_TERBIT': return silo.tanggal_terbit_silo || null;
                        default: return null;
                    }
                }
                
                statuses.forEach(function(status, index) {
                    const isCompleted = currentIndex > index;
                    const isActive = currentIndex === index;
                    const statusDate = getStatusDate(status, silo);
                    
                    html += '<div class="text-center" style="flex: 1; position: relative; min-width: 120px;">';
                    
                    // Circle with number
                    html += '<div class="mb-2" style="width: 50px; height: 50px; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; border: 3px solid ';
                    if (isCompleted) {
                        html += '#28a745; background-color: #28a745; color: white;';
                    } else if (isActive) {
                        html += '#0d6efd; background-color: #0d6efd; color: white;';
                    } else {
                        html += '#e9ecef; background-color: #ffffff; color: #6c757d;';
                    }
                    html += '">' + (index + 1) + '</div>';
                    
                    // Status label
                    html += '<div style="font-size: 0.75rem; font-weight: ' + (isActive ? '600' : (isCompleted ? '600' : '500')) + '; color: ' + (isActive ? '#0d6efd' : (isCompleted ? '#28a745' : '#6c757d')) + '; margin-bottom: 0.25rem;">';
                    html += getStatusLabel(status);
                    html += '</div>';
                    
                    // Date
                    if (statusDate) {
                        html += '<div style="font-size: 0.7rem; color: #6c757d; font-weight: 500;">';
                        html += formatDate(statusDate);
                        html += '</div>';
                    } else {
                        html += '<div style="font-size: 0.7rem; color: #adb5bd; font-style: italic;">-</div>';
                    }
                    
                    html += '</div>';
                    
                    // Connector line
                    if (index < statuses.length - 1) {
                        html += '<div style="flex: 1; height: 3px; background-color: ' + (isCompleted ? '#28a745' : '#e9ecef') + '; margin: 0 0.5rem; position: relative; top: -30px; z-index: -1;"></div>';
                    }
                });
                html += '</div>';
                
                // File Preview Tabs
                const hasPjk3File = silo.file_surat_keterangan_pjk3 && silo.file_surat_keterangan_pjk3.trim() !== '';
                const hasSiloFile = silo.file_silo && silo.file_silo.trim() !== '';
                
                if (hasPjk3File || hasSiloFile) {
                    html += '<hr><h6 class="mb-3">Dokumen</h6>';
                    html += '<ul class="nav nav-tabs mb-3" role="tablist">';
                    if (hasPjk3File) {
                        html += '<li class="nav-item"><button class="nav-link active" id="tab-pjk3-btn" data-bs-toggle="tab" data-bs-target="#tab-pjk3" type="button" role="tab">File PJK3</button></li>';
                    }
                    if (hasSiloFile) {
                        html += '<li class="nav-item"><button class="nav-link' + (hasPjk3File ? '' : ' active') + '" id="tab-silo-btn" data-bs-toggle="tab" data-bs-target="#tab-silo" type="button" role="tab">File SILO</button></li>';
                    }
                    html += '</ul>';
                    html += '<div class="tab-content" id="fileTabContent">';
                    if (hasPjk3File) {
                        const pjk3PreviewUrl = '<?= base_url('perizinan/preview-file/') ?>' + siloId + '/pjk3';
                        const pjk3DownloadUrl = '<?= base_url('perizinan/download-file/') ?>' + siloId + '/pjk3';
                        const pjk3FileName = silo.file_surat_keterangan_pjk3.split('/').pop();
                        const pjk3FileExt = pjk3FileName.split('.').pop().toLowerCase();
                        html += '<div class="tab-pane fade show active" id="tab-pjk3" role="tabpanel">';
                        // Download button
                        html += '<div class="mb-3 text-end"><a href="' + pjk3DownloadUrl + '" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-download me-1"></i>Download File PJK3</a></div>';
                        // Preview content
                        if (['jpg', 'jpeg', 'png', 'gif'].includes(pjk3FileExt)) {
                            html += '<div class="text-center"><img src="' + pjk3PreviewUrl + '" class="img-fluid" style="max-height: 600px; width: auto; border: 1px solid #dee2e6; border-radius: 0.375rem;" onerror="this.parentElement.innerHTML=\'<div class=\\\'alert alert-warning\\\'>Gagal memuat gambar. <a href=\\\'' + pjk3DownloadUrl + '\\\' target=\\\'_blank\\\'>Download File</a></div>\'"></div>';
                        } else if (pjk3FileExt === 'pdf') {
                            html += '<iframe src="' + pjk3PreviewUrl + '" style="width: 100%; height: 700px; border: 1px solid #dee2e6; border-radius: 0.375rem;" onerror="this.parentElement.innerHTML=\'<div class=\\\'alert alert-warning\\\'>Gagal memuat PDF. <a href=\\\'' + pjk3DownloadUrl + '\\\' target=\\\'_blank\\\'>Download File</a></div>\'"></iframe>';
                        } else {
                            html += '<div class="text-center p-4"><a href="' + pjk3DownloadUrl + '" target="_blank" class="btn btn-primary btn-lg"><i class="fas fa-download me-2"></i>Download File PJK3</a></div>';
                        }
                        html += '</div>';
                    }
                    if (hasSiloFile) {
                        const siloPreviewUrl = '<?= base_url('perizinan/preview-file/') ?>' + siloId + '/silo';
                        const siloDownloadUrl = '<?= base_url('perizinan/download-file/') ?>' + siloId + '/silo';
                        const siloFileName = silo.file_silo.split('/').pop();
                        const siloFileExt = siloFileName.split('.').pop().toLowerCase();
                        html += '<div class="tab-pane fade' + (hasPjk3File ? '' : ' show active') + '" id="tab-silo" role="tabpanel">';
                        // Download button
                        html += '<div class="mb-3 text-end"><a href="' + siloDownloadUrl + '" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-download me-1"></i>Download File SILO</a></div>';
                        // Preview content
                        if (['jpg', 'jpeg', 'png', 'gif'].includes(siloFileExt)) {
                            html += '<div class="text-center"><img src="' + siloPreviewUrl + '" class="img-fluid" style="max-height: 600px; width: auto; border: 1px solid #dee2e6; border-radius: 0.375rem;" onerror="this.parentElement.innerHTML=\'<div class=\\\'alert alert-warning\\\'>Gagal memuat gambar. <a href=\\\'' + siloDownloadUrl + '\\\' target=\\\'_blank\\\'>Download File</a></div>\'"></div>';
                        } else if (siloFileExt === 'pdf') {
                            html += '<iframe src="' + siloPreviewUrl + '" style="width: 100%; height: 700px; border: 1px solid #dee2e6; border-radius: 0.375rem;" onerror="this.parentElement.innerHTML=\'<div class=\\\'alert alert-warning\\\'>Gagal memuat PDF. <a href=\\\'' + siloDownloadUrl + '\\\' target=\\\'_blank\\\'>Download File</a></div>\'"></iframe>';
                        } else {
                            html += '<div class="text-center p-4"><a href="' + siloDownloadUrl + '" target="_blank" class="btn btn-primary btn-lg"><i class="fas fa-download me-2"></i>Download File SILO</a></div>';
                        }
                        html += '</div>';
                    }
                    html += '</div>';
                } else {
                    html += '<hr><div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Belum ada dokumen yang diupload</div>';
                }
                
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
        'PENGAJUAN_PJK3': 'SURAT_KETERANGAN_PJK3',
        'SURAT_KETERANGAN_PJK3': 'PENGAJUAN_UPTD',
        'PENGAJUAN_UPTD': 'SILO_TERBIT'
    };
    return workflow[currentStatus] || null;
}

function getActionButtonLabel(currentStatus) {
    const nextStatus = getNextStatus(currentStatus);
    if (!nextStatus) return null;
    
    // Label berdasarkan next status (status yang akan dituju)
    const labels = {
        'SURAT_KETERANGAN_PJK3': 'Proses PJK3',  // Dari PENGAJUAN_PJK3 ke SURAT_KETERANGAN_PJK3
        'PENGAJUAN_UPTD': 'Proses DISNAKER',       // Dari SURAT_KETERANGAN_PJK3 ke PENGAJUAN_UPTD
        'SILO_TERBIT': 'Terbitkan SILO'           // Dari PENGAJUAN_UPTD ke SILO_TERBIT
    };
    return labels[nextStatus] || 'Next Stage';
}

function getStatusLabel(status) {
    const labels = {
        'BELUM_ADA': 'Belum Ada SILO',
        'PENGAJUAN_PJK3': 'Pengajuan ke PJK3',
        'SURAT_KETERANGAN_PJK3': 'Surat Keterangan PJK3',
        'PENGAJUAN_UPTD': 'Pengajuan DISNAKER',
        'SILO_TERBIT': 'SILO Terbit',
        'SILO_EXPIRED': 'SILO Expired'
    };
    return labels[status] || status;
}

function getStatusColor(status) {
    const colors = {
        'BELUM_ADA': 'danger',
        'PENGAJUAN_PJK3': 'warning',
        'SURAT_KETERANGAN_PJK3': 'info',
        'PENGAJUAN_UPTD': 'warning',
        'SILO_TERBIT': 'success',
        'SILO_EXPIRED': 'danger'
    };
    return colors[status] || 'secondary';
}

function getStatusIndex(status) {
    const statuses = ['PENGAJUAN_PJK3', 'SURAT_KETERANGAN_PJK3', 'PENGAJUAN_UPTD', 'SILO_TERBIT'];
    return statuses.indexOf(status);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
}

// Function to update tab badge counts
function updateTabBadges() {
    $.ajax({
        url: '<?= base_url('perizinan/get-silo-stats') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const stats = response.data;
                // Update badge counts
                $('#badge-sudah-ada').text(stats.sudah_ada || 0);
                $('#badge-progres').text(stats.progres || 0);
                $('#badge-belum-ada').text(stats.belum_ada || 0);
                $('#badge-akan-expired').text(stats.expiring_soon || 0);
                $('#badge-sudah-expired').text(stats.expired || 0);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating badge counts:', error);
        }
    });
}
</script>
<?= $this->endSection() ?>

