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
                    <div class="text-muted"><?= lang('App.already_have_silo') ?></div>
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
                    <div class="text-muted"><?= lang('Common.progress') ?></div>
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
                    <div class="text-muted"><?= lang('App.no_silo_yet') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert for Expiring Soon -->
<?php if (($stats['expiring_soon'] ?? 0) > 0): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong><?= lang('App.warning') ?>!</strong> <?= lang('App.silo_expiring_message', [$stats['expiring_soon']]) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= lang('Common.close') ?>"></button>
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
                            <span><?= lang('Common.all') ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sudah-ada-tab" data-bs-toggle="tab" data-bs-target="#sudah-ada" type="button" role="tab" aria-controls="sudah-ada" aria-selected="false">
                            <i class="fas fa-check-circle"></i>
                            <span><?= lang('App.already_have_silo') ?></span>
                            <span class="badge bg-success ms-2" id="badge-sudah-ada"><?= $stats['sudah_ada'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="progres-tab" data-bs-toggle="tab" data-bs-target="#progres" type="button" role="tab" aria-controls="progres" aria-selected="false">
                            <i class="fas fa-clock"></i>
                            <span><?= lang('Common.progress') ?></span>
                            <span class="badge bg-warning ms-2" id="badge-progres"><?= $stats['progres'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="belum-ada-tab" data-bs-toggle="tab" data-bs-target="#belum-ada" type="button" role="tab" aria-controls="belum-ada" aria-selected="false">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?= lang('App.no_silo_yet') ?></span>
                            <span class="badge bg-danger ms-2" id="badge-belum-ada"><?= $stats['belum_ada'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="akan-expired-tab" data-bs-toggle="tab" data-bs-target="#akan-expired" type="button" role="tab" aria-controls="akan-expired" aria-selected="false">
                            <i class="fas fa-exclamation-circle"></i>
                            <span><?= lang('App.expiring_soon_30d') ?></span>
                            <span class="badge bg-warning ms-2" id="badge-akan-expired"><?= $stats['expiring_soon'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sudah-expired-tab" data-bs-toggle="tab" data-bs-target="#sudah-expired" type="button" role="tab" aria-controls="sudah-expired" aria-selected="false">
                            <i class="fas fa-times-circle"></i>
                            <span><?= lang('App.expired') ?></span>
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
                                <label for="filter-status-all"><?= lang('Common.status') ?></label>
                                <select id="filter-status-all" class="form-select form-select-sm">
                                    <option value=""><?= lang('App.all_statuses') ?></option>
                                    <option value="PENGAJUAN_PJK3"><?= lang('App.submission_to_pjk3') ?></option>
                                    <option value="SURAT_KETERANGAN_PJK3"><?= lang('App.pjk3_certificate') ?></option>
                                    <option value="PENGAJUAN_UPTD"><?= lang('App.submission_to_disnaker') ?></option>
                                    <option value="SILO_TERBIT"><?= lang('App.silo_issued') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="filter-departemen-all" class="form-label small fw-semibold text-muted"><?= lang('App.department') ?></label>
                                <select id="filter-departemen-all" class="form-select form-select-sm">
                                    <option value=""><?= lang('App.all_departments') ?></option>
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
                                <label for="filter-expired-all"><?= lang('App.filter_expired') ?></label>
                                <select id="filter-expired-all" class="form-select form-select-sm">
                                    <option value=""><?= lang('Common.all') ?></option>
                                    <option value="7"><?= lang('App.expiring_7days') ?></option>
                                    <option value="30"><?= lang('App.expiring_1month') ?></option>
                                    <option value="90"><?= lang('App.expiring_3months') ?></option>
                                    <option value="180"><?= lang('App.expiring_6months') ?></option>
                                    <option value="expired"><?= lang('App.expired') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="siloTable" class="table table-sm table-striped table-hover" style="font-size: 0.875rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 150px;">Unit Info</th>
                                    <th style="min-width: 100px;">Department</th>
                                    <th style="min-width: 150px;">Customer</th>
                                    <th style="min-width: 120px;">Status</th>
                                    <th style="min-width: 100px;">SILO No</th>
                                    <th style="min-width: 90px;">Issue</th>
                                    <th style="min-width: 90px;">Expiry</th>
                                    <th style="width: 80px;">Actions</th>
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
                                <label for="filter-departemen-sudah-ada" class="form-label small fw-semibold text-muted">Department</label>
                                <select id="filter-departemen-sudah-ada" class="form-select form-select-sm">
                                    <option value="">All Departments</option>
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
                                    <option value="">All</option>
                                    <option value="7">Expiring Soon < 7 Days</option>
                                    <option value="30">Expiring Soon < 1 Month</option>
                                    <option value="90">Expiring Soon < 3 Months</option>
                                    <option value="180">Expiring Soon < 6 Months</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="siloTable2" class="table table-sm table-striped table-hover" style="font-size: 0.875rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 150px;">Unit Info</th>
                                    <th style="min-width: 100px;">Department</th>
                                    <th style="min-width: 150px;">Customer</th>
                                    <th style="min-width: 120px;">Status</th>
                                    <th style="min-width: 100px;">SILO No</th>
                                    <th style="min-width: 90px;">Issue</th>
                                    <th style="min-width: 90px;">Expiry</th>
                                    <th style="width: 80px;">Actions</th>
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
                                    <option value="">All Statuses</option>
                                    <option value="PENGAJUAN_PJK3">Submission to PJK3</option>
                                    <option value="SURAT_KETERANGAN_PJK3">PJK3 Certificate</option>
                                    <option value="PENGAJUAN_UPTD">Submission to DISNAKER</option>
                                    <option value="SILO_TERBIT">SILO Issued</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="filter-departemen-progres" class="form-label small fw-semibold text-muted">Department</label>
                                <select id="filter-departemen-progres" class="form-select form-select-sm">
                                    <option value="">All Departments</option>
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
                        <table id="siloTable3" class="table table-sm table-striped table-hover" style="font-size: 0.875rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 150px;">Unit Info</th>
                                    <th style="min-width: 100px;">Department</th>
                                    <th style="min-width: 150px;">Customer</th>
                                    <th style="min-width: 120px;">Status</th>
                                    <th style="min-width: 100px;">SILO No</th>
                                    <th style="min-width: 90px;">Issue</th>
                                    <th style="min-width: 90px;">Expiry</th>
                                    <th style="width: 80px;">Actions</th>
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
                                <label for="filter-departemen-akan-expired" class="form-label small fw-semibold text-muted">Department</label>
                                <select id="filter-departemen-akan-expired" class="form-select form-select-sm">
                                    <option value="">All Departments</option>
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
                        <table id="siloTable5" class="table table-sm table-striped table-hover" style="font-size: 0.875rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 150px;">Unit Info</th>
                                    <th style="min-width: 100px;">Department</th>
                                    <th style="min-width: 150px;">Customer</th>
                                    <th style="min-width: 120px;">Status</th>
                                    <th style="min-width: 100px;">SILO No</th>
                                    <th style="min-width: 90px;">Issue</th>
                                    <th style="min-width: 90px;">Expiry</th>
                                    <th style="width: 80px;">Actions</th>
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
                                <label for="filter-departemen-sudah-expired" class="form-label small fw-semibold text-muted">Department</label>
                                <select id="filter-departemen-sudah-expired" class="form-select form-select-sm">
                                    <option value="">All Departments</option>
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
                        <table id="siloTable6" class="table table-sm table-striped table-hover" style="font-size: 0.875rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 150px;">Unit Info</th>
                                    <th style="min-width: 100px;">Department</th>
                                    <th style="min-width: 150px;">Customer</th>
                                    <th style="min-width: 120px;">Status</th>
                                    <th style="min-width: 100px;">SILO No</th>
                                    <th style="min-width: 90px;">Issue</th>
                                    <th style="min-width: 90px;">Expiry</th>
                                    <th style="width: 80px;">Actions</th>
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
                            <label for="filter-departemen-belum-ada" class="form-label small fw-semibold text-muted">Department</label>
                            <select id="filter-departemen-belum-ada" class="form-select form-select-sm">
                                <option value="">All Departments</option>
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
                    <table id="siloTable4" class="table table-sm table-striped table-hover" style="font-size: 0.875rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 180px;">Unit Info</th>
                                <th style="min-width: 100px;">Department</th>
                                <th style="min-width: 180px;">Customer</th>
                                <th style="width: 100px;">Actions</th>
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
                <h5 class="modal-title" id="createModalLabel">Create SILO Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unit_ids" class="form-label">Select Unit <span class="text-danger">*</span></label>
                        <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                            <input type="text" id="unit_search" class="form-control form-control-sm mb-2" placeholder="Search units...">
                            <div id="unit_checkboxes">
                                <!-- Units will be loaded here -->
                            </div>
                        </div>
                        <small class="text-muted">Only units without an active SILO are displayed. Multiple units can be selected.</small>
                    </div>
                    <div class="mb-3">
                        <label for="nama_pt_pjk3" class="form-label">Company<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_pt_pjk3" name="nama_pt_pjk3" placeholder="Example: PT. GAHARU SAKTI PRATAMA" required>
                        <small class="text-muted">Name of the PJK3 company conducting the inspection and testing</small>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pengajuan_pjk3" class="form-label">Submission Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_pengajuan_pjk3" name="tanggal_pengajuan_pjk3" required>
                    </div>
                    <div class="mb-3">
                        <label for="catatan_pengajuan_pjk3" class="form-label">Notes</label>
                        <textarea class="form-control" id="catatan_pengajuan_pjk3" name="catatan_pengajuan_pjk3" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Submission</button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Detail SILO -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail SILO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Content will be dynamically loaded -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                data: null,
                render: function(data, type, row) {
                    return '<div class="unit-info-compact">' +
                        '<div class="fw-bold text-primary" style="font-size: 0.9rem;">' + (row.no_unit || '-') + '</div>' +
                        '<small class="text-muted d-block">SN: ' + (row.serial_number || '-') + '</small>' +
                        '<small class="text-muted d-block">' + (row.tipe_unit || '-') + '</small>' +
                        '</div>';
                }
            },
            { 
                data: 'departemen',
                render: function(data) {
                    return '<span class="badge bg-secondary">' + (data || '-') + '</span>';
                }
            },
            { 
                data: 'nama_perusahaan',
                render: function(data, type, row) {
                    if (!data) return '-';
                    return '<div class="customer-info-compact">' +
                        '<div class="fw-bold" style="font-size: 0.85rem;">' + data + '</div>' +
                        '</div>';
                }
            },
            { 
                data: 'id_silo',
                orderable: false,
                render: function(data, type, row) {
                    return '<button type="button" class="btn btn-sm btn-success w-100" onclick="createSiloForUnit(' + row.id_silo + ')" title="Create SILO Submission">' +
                        '<i class="fas fa-plus me-1"></i>Create</button>';
                }
            }
        ];
    }
    
    // For other tables (with SILO data)
    return [
        { 
            data: null,
            render: function(data, type, row) {
                return '<div class="unit-info-compact">' +
                    '<div class="fw-bold text-primary" style="font-size: 0.9rem;">' + (row.no_unit || '-') + '</div>' +
                    '<small class="text-muted d-block">SN: ' + (row.serial_number || '-') + '</small>' +
                    '<small class="text-muted d-block">' + (row.tipe_unit || '-') + '</small>' +
                    '</div>';
            }
        },
            { 
                data: 'departemen',
                render: function(data) {
                    return '<span class="badge bg-secondary">' + (data || '-') + '</span>';
                }
            },
            { 
                data: 'nama_perusahaan',
                render: function(data, type, row) {
                    if (!data) return '-';
                    return '<div class="customer-info-compact">' +
                        '<div class="fw-bold" style="font-size: 0.85rem;">' + data + '</div>' +
                        '</div>';
                }
            },
            { 
                data: 'status',
                render: function(data) {
                if (!data || data === null) {
                    return '<span class="badge bg-danger">No SILO</span>';
                }
                const statusLabels = {
                    'BELUM_ADA': 'No SILO',
                    'PENGAJUAN_PJK3': 'PJK3 Submit',
                    'SURAT_KETERANGAN_PJK3': 'PJK3 Letter',
                    'PENGAJUAN_UPTD': 'UPTD Submit',
                    'SILO_TERBIT': 'SILO Issued',
                    'SILO_EXPIRED': 'Expired'
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
                return data ? '<small>' + data + '</small>' : '-';
            }
        },
        { 
            data: 'tanggal_terbit_silo',
            render: function(data) {
                return data ? '<small>' + formatDate(data) + '</small>' : '-';
            }
        },
            { 
                data: 'tanggal_expired_silo',
                render: function(data) {
                    if (!data) return '-';
                    const expired = new Date(data) < new Date();
                    const expiringSoon = new Date(data) <= new Date(Date.now() + 30 * 24 * 60 * 60 * 1000);
                    let badge = '';
                    if (expired) badge = '<br><span class="badge bg-danger mt-1">Expired</span>';
                    else if (expiringSoon) badge = '<br><span class="badge bg-warning mt-1">Soon</span>';
                    return '<small>' + formatDate(data) + '</small>' + badge;
                }
            },
            { 
                data: 'id_silo',
                orderable: false,
                render: function(data, type, row) {
                    let buttons = [];
                    
                    // For units without SILO, show create button
                    if (!row.status || row.status === null) {
                        buttons.push('<button type="button" class="btn btn-sm btn-success w-100 mb-1" onclick="createSiloForUnit(' + row.id_silo + ')" title="Create SILO"><i class="fas fa-plus me-1"></i>Create</button>');
                    } else {
                        // Always show detail button
                        buttons.push('<button type="button" class="btn btn-sm btn-info w-100 mb-1" onclick="showDetail(' + data + ')" title="Detail"><i class="fas fa-eye me-1"></i>Detail</button>');
                        
                        // Show update button based on status (stage by stage)
                        if (row.status !== 'SILO_TERBIT' && row.status !== 'SILO_EXPIRED') {
                            const actionLabel = getActionButtonLabel(row.status);
                            if (actionLabel) {
                                buttons.push('<button type="button" class="btn btn-sm btn-primary w-100" onclick="showUpdateModal(' + data + ')" title="' + actionLabel + '">' + actionLabel + '</button>');
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
            processing: "Loading...",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            emptyTable: "No SILO data available",
            zeroRecords: "No matching data found",
            paginate: {
                first: "First",
                previous: "Previous",
                next: "Next",
                last: "Last"
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
        
        // Adjust columns after tab is shown to prevent overlap
        setTimeout(function() {
            if (target === '#all' && siloTable) {
                siloTable.columns.adjust().draw();
            } else if (target === '#sudah-ada' && siloTable2) {
                siloTable2.columns.adjust().draw();
            } else if (target === '#progres' && siloTable3) {
                siloTable3.columns.adjust().draw();
            } else if (target === '#belum-ada' && siloTable4) {
                siloTable4.columns.adjust().draw();
            } else if (target === '#akan-expired' && siloTable5) {
                siloTable5.columns.adjust().draw();
            } else if (target === '#sudah-expired' && siloTable6) {
                siloTable6.columns.adjust().draw();
            }
        }, 100);
        
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
    
    // Ensure "All" tab is active and initialized on page load
    // This prevents overlap when clicking other tabs after refresh
    $('#all-tab').addClass('active');
    $('#all').addClass('show active');
    
    // Wait for DOM to be ready, then ensure table columns are adjusted
    setTimeout(function() {
        if (siloTable) {
            siloTable.columns.adjust().draw();
        }
    }, 200);

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
    $('#unit_checkboxes').html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div> <span class="ms-2">Loading units...</span></div>');
    
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
                $('#unit_checkboxes').html('<div class="text-danger text-center py-2">Failed to load units: ' + (response.message || 'Unknown error') + '</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading units:', error, xhr.responseText);
            $('#unit_checkboxes').html('<div class="text-danger text-center py-2">Error loading units. Please refresh the page.</div>');
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
        container.html('<div class="text-muted text-center py-2">No units available</div>');
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
            title: 'Warning',
            text: 'Select at least 1 unit'
        });
        return;
    }
    
    const namaPtPjk3 = $('#nama_pt_pjk3').val();
    if (!namaPtPjk3 || namaPtPjk3.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Nama PT PJK3 must be filled'
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
                text: response?.message || 'An error occurred while creating the submission'
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
                        title: 'Cannot Update',
                        text: 'Status has reached the final stage and cannot be updated further.'
                    });
                    return;
                }

                $('#update_silo_id').val(siloId);
                $('#update_current_status').val(silo.status);
                
                let html = '<div class="mb-3">';
                html += '<label class="form-label">Unit: <strong>' + (silo.no_unit || 'N/A') + '</strong></label><br>';
                html += '<label class="form-label">Current Status: <span class="badge bg-secondary">' + getStatusLabel(silo.status) + '</span></label><br>';
                html += '<label class="form-label">Next Status: <span class="badge bg-primary">' + getStatusLabel(nextStatus) + '</span></label>';
                html += '</div>';

                // Add fields based on next status
                if (nextStatus === 'SURAT_KETERANGAN_PJK3') {
                    html += '<div class="mb-3"><label class="form-label">Letter Number <span class="text-danger">*</span></label>';
                    html += '<input type="text" class="form-control" name="nomor_surat_keterangan_pjk3" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Issue Date <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_surat_keterangan_pjk3" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Upload PJK3 File (PDF/Image)</label>';
                    html += '<input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFile(this, \'pjk3\')"></div>';
                    html += '<div id="pjk3Preview"></div>';
                } else if (nextStatus === 'PENGAJUAN_UPTD') {
                    html += '<div class="mb-3"><label class="form-label">DISNAKER Location <span class="text-danger">*</span></label>';
                    html += '<input type="text" class="form-control" name="lokasi_disnaker" placeholder="Enter DISNAKER address/location." required></div>';
                    html += '<div class="mb-3"><label class="form-label">DISNAKER Submission Date <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_pengajuan_uptd" required></div>';
                } else if (nextStatus === 'SILO_TERBIT') {
                    html += '<div class="mb-3"><label class="form-label">SILO Number <span class="text-danger">*</span></label>';
                    html += '<input type="text" class="form-control" name="nomor_silo" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Issue Date <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_terbit_silo" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Expiration Date <span class="text-danger">*</span></label>';
                    html += '<input type="date" class="form-control" name="tanggal_expired_silo" required></div>';
                    html += '<div class="mb-3"><label class="form-label">Upload SILO File (PDF/Image)</label>';
                    html += '<input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFile(this, \'silo\')"></div>';
                    html += '<div id="siloPreview"></div>';
                }

                html += '<div class="mb-3"><label class="form-label">Description</label>';
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
                text: response?.message || 'An error occurred while updating the status'
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
            title: 'File Too Large',
            text: 'Maximum file size is 15MB. Your file: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB'
        });
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('file_type', fileType);

    // Show loading
    Swal.fire({
        title: 'Uploading...',
        text: 'Please wait, the file is being uploaded',
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
                    title: 'Success',
                    text: response.message || 'File uploaded successfully'
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
                    text: response.message || 'Failed to upload file'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Upload error:', error, xhr.responseText);
            let errorMessage = 'Failed to upload file';
            
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
                html += '<h6>Unit Information</h6>';
                html += '<table class="table table-sm">';
                html += '<tr><th>Unit Number:</th><td><strong>' + (silo.no_unit || 'N/A') + '</strong></td></tr>';
                html += '<tr><th>Serial Number:</th><td>' + (silo.serial_number || '-') + '</td></tr>';
                html += '<tr><th>Unit Year:</th><td>' + (silo.tahun_unit || '-') + '</td></tr>';
                html += '<tr><th>Unit Type:</th><td>' + (silo.tipe_unit || '-') + '</td></tr>';
                html += '<tr><th>Unit Category:</th><td>' + (silo.jenis_unit || '-') + '</td></tr>';
                html += '<tr><th>Unit Model:</th><td>' + (silo.model_unit || '-') + '</td></tr>';
                html += '<tr><th>Unit Capacity:</th><td>' + (silo.kapasitas_unit || '-') + '</td></tr>';
                html += '<tr><th>Department:</th><td>' + (silo.departemen || '-') + '</td></tr>';
                html += '<tr><th>Unit Location:</th><td>' + (silo.lokasi_unit || '-') + '</td></tr>';
                html += '<tr><th>Company Name:</th><td>' + (silo.nama_perusahaan || '-') + '</td></tr>';
                if (silo.alamat) {
                    html += '<tr><th>Customer Address:</th><td>' + silo.alamat;
                    if (silo.kota) {
                        html += ', ' + silo.kota;
                    }
                    if (silo.provinsi) {
                        html += ', ' + silo.provinsi;
                    }
                    html += '</td></tr>';
                }
                html += '</table></div>';
                
                html += '<div class="col-md-6"><h6>SILO Information</h6>';
                html += '<table class="table table-sm">';
                html += '<tr><th>Status:</th><td><span class="badge bg-' + getStatusColor(silo.status) + '">' + getStatusLabel(silo.status) + '</span></td></tr>';
                if (silo.nomor_silo) {
                    html += '<tr><th>SILO Number:</th><td>' + silo.nomor_silo + '</td></tr>';
                    html += '<tr><th>Issue Date:</th><td>' + formatDate(silo.tanggal_terbit_silo) + '</td></tr>';
                    html += '<tr><th>Expiration Date:</th><td>' + formatDate(silo.tanggal_expired_silo) + '</td></tr>';
                }
                if (silo.nama_pt_pjk3) {
                    html += '<tr><th>PT PJK3 Name:</th><td>' + silo.nama_pt_pjk3 + '</td></tr>';
                }
                if (silo.nomor_surat_keterangan_pjk3) {
                    html += '<tr><th>PJK3 Letter Number:</th><td>' + silo.nomor_surat_keterangan_pjk3 + '</td></tr>';
                    html += '<tr><th>PJK3 Letter Date:</th><td>' + formatDate(silo.tanggal_surat_keterangan_pjk3) + '</td></tr>';
                }
                if (silo.tanggal_pengajuan_uptd) {
                    html += '<tr><th>DISNAKER Submission Date:</th><td>' + formatDate(silo.tanggal_pengajuan_uptd) + '</td></tr>';
                    if (silo.lokasi_disnaker) {
                        html += '<tr><th>DISNAKER Location:</th><td><strong>' + silo.lokasi_disnaker + '</strong></td></tr>';
                    }
                }
                html += '</table></div></div>';
                
                // Horizontal Timeline with Dates
                html += '<hr><h6 class="mb-3">Process Timeline</h6>';
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
                    html += '<hr><h6 class="mb-3">Documents</h6>';
                    html += '<ul class="nav nav-tabs mb-3" role="tablist">';
                    if (hasPjk3File) {
                        html += '<li class="nav-item"><button class="nav-link active" id="tab-pjk3-btn" data-bs-toggle="tab" data-bs-target="#tab-pjk3" type="button" role="tab">PJK3 File</button></li>';
                    }
                    if (hasSiloFile) {
                        html += '<li class="nav-item"><button class="nav-link' + (hasPjk3File ? '' : ' active') + '" id="tab-silo-btn" data-bs-toggle="tab" data-bs-target="#tab-silo" type="button" role="tab">SILO File</button></li>';
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
                            html += '<div class="text-center"><img src="' + pjk3PreviewUrl + '" class="img-fluid" style="max-height: 600px; width: auto; border: 1px solid #dee2e6; border-radius: 0.375rem;" onerror="this.parentElement.innerHTML=\'<div class=\\\'alert alert-warning\\\'>Error Loading Image. <a href=\\\'' + pjk3DownloadUrl + '\\\' target=\\\'_blank\\\'>Download File</a></div>\'"></div>';
                        } else if (pjk3FileExt === 'pdf') {
                            html += '<iframe src="' + pjk3PreviewUrl + '" style="width: 100%; height: 700px; border: 1px solid #dee2e6; border-radius: 0.375rem;" onerror="this.parentElement.innerHTML=\'<div class=\\\'alert alert-warning\\\'>Error Loading PDF. <a href=\\\'' + pjk3DownloadUrl + '\\\' target=\\\'_blank\\\'>Download File</a></div>\'"></iframe>';
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

