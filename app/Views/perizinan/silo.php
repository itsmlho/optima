<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">SILO Management</li>
    </ol>
</nav>

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
    <strong><?= lang('App.warning') ?>!</strong> <?= sprintf(lang('App.silo_expiring_message'), $stats['expiring_soon']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= lang('Common.close') ?>"></button>
</div>
<?php endif; ?>

<!-- Main Content Card -->
<div class="card table-card mb-4">

        <!-- Card Header: Title + Action Buttons -->
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-earmark-check me-2 text-primary"></i>
                    SILO Management
                </h5>
                <p class="text-muted small mb-0">Kelola izin operasional unit dan pantau masa berlaku</p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <button type="button" class="btn btn-primary btn-sm" onclick="showCreateModal()">
                    <i class="fas fa-plus me-1"></i>Buat Pengajuan
                </button>
                <a href="<?= base_url('perizinan/export-silo') ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Export
                </a>
                <button type="button" class="btn btn-secondary btn-sm" onclick="refreshAllTables()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="card-body p-0">
            <ul class="nav nav-tabs px-3 pt-2" id="statusTabs" role="tablist">
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
                            <span class="badge badge-soft-green ms-2" id="badge-sudah-ada"><?= $stats['sudah_ada'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="progres-tab" data-bs-toggle="tab" data-bs-target="#progres" type="button" role="tab" aria-controls="progres" aria-selected="false">
                            <i class="fas fa-clock"></i>
                            <span><?= lang('Common.progress') ?></span>
                            <span class="badge badge-soft-yellow ms-2" id="badge-progres"><?= $stats['progres'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="belum-ada-tab" data-bs-toggle="tab" data-bs-target="#belum-ada" type="button" role="tab" aria-controls="belum-ada" aria-selected="false">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?= lang('App.no_silo_yet') ?></span>
                            <span class="badge badge-soft-red ms-2" id="badge-belum-ada"><?= $stats['belum_ada'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="akan-expired-tab" data-bs-toggle="tab" data-bs-target="#akan-expired" type="button" role="tab" aria-controls="akan-expired" aria-selected="false">
                            <i class="fas fa-exclamation-circle"></i>
                            <span><?= lang('App.expiring_soon_30d') ?></span>
                            <span class="badge badge-soft-orange ms-2" id="badge-akan-expired"><?= $stats['expiring_soon'] ?? 0 ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sudah-expired-tab" data-bs-toggle="tab" data-bs-target="#sudah-expired" type="button" role="tab" aria-controls="sudah-expired" aria-selected="false">
                            <i class="fas fa-times-circle"></i>
                            <span><?= lang('App.expired') ?></span>
                            <span class="badge badge-soft-red ms-2" id="badge-sudah-expired"><?= $stats['expired'] ?? 0 ?></span>
                        </button>
                    </li>
                </ul>
            </div><!-- /card-body tabs row -->

        <!-- Tab Content -->
        <div class="card-body p-3">
            <div class="tab-content" id="statusTabContent">
                <!-- Tab: Semua -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <!-- Filter Controls -->
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <div class="mb-3">
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
                            <div class="mb-3">
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
                            <div class="mb-3">
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Create SILO Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unit_ids" class="form-label">Select Unit <span class="text-danger">*</span></label>
                        <div class="border rounded p-2">
                            <input type="text" id="unit_search" class="form-control form-control-sm mb-2" placeholder="Search units...">
                            <div id="unit_checkboxes" style="max-height: 260px; overflow-y: auto;">
                                <!-- Units will be loaded here (paged) -->
                            </div>
                        </div>
                        <small class="text-muted">
                            Only units without an active SILO are displayed. Search is performed on the server to keep this list fast.
                            Multiple units can be selected.
                        </small>
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

<!-- Modal: Edit SILO -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit me-2 text-primary"></i>Edit Data SILO
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_silo_id">
                <div class="modal-body">
                    <div id="editAlert" class="d-none mb-3"></div>

                    <!-- Section: PJK3 -->
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3 text-primary"><i class="fas fa-building me-2"></i>Data PJK3</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Nama PT PJK3</label>
                                    <input type="text" class="form-control form-control-sm" name="nama_pt_pjk3" id="edit_nama_pt_pjk3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Tanggal Pengajuan PJK3</label>
                                    <input type="date" class="form-control form-control-sm" name="tanggal_pengajuan_pjk3" id="edit_tanggal_pengajuan_pjk3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Tanggal Testing PJK3</label>
                                    <input type="date" class="form-control form-control-sm" name="tanggal_testing_pjk3" id="edit_tanggal_testing_pjk3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Hasil Testing PJK3</label>
                                    <input type="text" class="form-control form-control-sm" name="hasil_testing_pjk3" id="edit_hasil_testing_pjk3" placeholder="Lulus / Tidak Lulus / ...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Nomor Surat Keterangan PJK3</label>
                                    <input type="text" class="form-control form-control-sm" name="nomor_surat_keterangan_pjk3" id="edit_nomor_sk_pjk3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Tanggal Surat Keterangan PJK3</label>
                                    <input type="date" class="form-control form-control-sm" name="tanggal_surat_keterangan_pjk3" id="edit_tanggal_sk_pjk3">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Catatan Pengajuan PJK3</label>
                                    <textarea class="form-control form-control-sm" name="catatan_pengajuan_pjk3" id="edit_catatan_pjk3" rows="2"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Upload/Ganti File PJK3 <span class="text-muted">(PDF/JPG/PNG, maks 15MB)</span></label>
                                    <input type="file" class="form-control form-control-sm" name="file_pjk3" id="edit_file_pjk3" accept=".pdf,.jpg,.jpeg,.png">
                                    <div id="edit_pjk3_current" class="mt-1 small text-muted d-none">
                                        File saat ini: <a href="#" id="edit_pjk3_link" target="_blank"><i class="fas fa-file me-1"></i><span id="edit_pjk3_name"></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: UPTD -->
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3 text-warning"><i class="fas fa-landmark me-2"></i>Data UPTD / DISNAKER</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Tanggal Pengajuan UPTD</label>
                                    <input type="date" class="form-control form-control-sm" name="tanggal_pengajuan_uptd" id="edit_tanggal_pengajuan_uptd">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Lokasi DISNAKER</label>
                                    <input type="text" class="form-control form-control-sm" name="lokasi_disnaker" id="edit_lokasi_disnaker" placeholder="Alamat kantor DISNAKER">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: SILO -->
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3 text-success"><i class="fas fa-certificate me-2"></i>Data SILO</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Nomor SILO</label>
                                    <input type="text" class="form-control form-control-sm" name="nomor_silo" id="edit_nomor_silo">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Tanggal Terbit</label>
                                    <input type="date" class="form-control form-control-sm" name="tanggal_terbit_silo" id="edit_tanggal_terbit_silo">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Tanggal Kedaluwarsa</label>
                                    <input type="date" class="form-control form-control-sm" name="tanggal_expired_silo" id="edit_tanggal_expired_silo">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Upload/Ganti File SILO <span class="text-muted">(PDF/JPG/PNG, maks 15MB)</span></label>
                                    <input type="file" class="form-control form-control-sm" name="file_silo" id="edit_file_silo" accept=".pdf,.jpg,.jpeg,.png">
                                    <div id="edit_silo_current" class="mt-1 small text-muted d-none">
                                        File saat ini: <a href="#" id="edit_silo_link" target="_blank"><i class="fas fa-file me-1"></i><span id="edit_silo_name"></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnEditSubmit">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Update Status -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail SILO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Content will be dynamically loaded -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="detailEditBtn" onclick="openEditFromDetail()" style="display:none">
                    <i class="fas fa-edit me-1"></i>Edit Data
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">



<!-- SweetAlert2 -->

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
                    let html = '<div class="unit-info-compact">' +
                        '<div class="fw-bold text-primary" style="font-size: 0.9rem;">' + (row.no_unit || '-') + '</div>' +
                        '<small class="text-muted d-block">SN: ' + (row.serial_number || '-') + '</small>' +
                        '<small class="text-muted d-block">' + (row.tipe_unit || '-') + '</small>' +
                        '</div>';
                    if (window.OptimaSearch && typeof OptimaSearch.highlightForTable === 'function') {
                        html = OptimaSearch.highlightForTable($('#siloTable4').DataTable(), html);
                    }
                    return html;
                }
            },
            { 
                data: 'departemen',
                render: function(data) {
                    let label = data || '-';
                    if (window.OptimaSearch && typeof OptimaSearch.highlightForTable === 'function') {
                        label = OptimaSearch.highlightForTable($('#siloTable4').DataTable(), label);
                    }
                    return '<span class="badge badge-soft-blue">' + label + '</span>';
                }
            },
            { 
                data: 'nama_perusahaan',
                render: function(data, type, row) {
                    if (!data) return '-';
                    let label = data;
                    if (window.OptimaSearch && typeof OptimaSearch.highlightForTable === 'function') {
                        label = OptimaSearch.highlightForTable($('#siloTable4').DataTable(), label);
                    }
                    return '<div class="customer-info-compact">' +
                        '<div class="fw-bold" style="font-size: 0.85rem;">' + label + '</div>' +
                        '</div>';
                }
            },
            { 
                data: 'id_silo',
                orderable: false,
                render: function(data, type, row) {
                    return '<button type="button" class="btn btn-xs btn-soft-green" onclick="createSiloForUnit(' + row.id_silo + ')" title="Buat Pengajuan">' +
                        '<i class="fas fa-plus me-1"></i>Buat</button>';
                }
            }
        ];
    }
    
    // For other tables (with SILO data)
    return [
        { 
            data: null,
            render: function(data, type, row) {
                let html = '<div class="unit-info-compact">' +
                    '<div class="fw-bold text-primary" style="font-size: 0.9rem;">' + (row.no_unit || '-') + '</div>' +
                    '<small class="text-muted d-block">SN: ' + (row.serial_number || '-') + '</small>' +
                    '<small class="text-muted d-block">' + (row.tipe_unit || '-') + '</small>' +
                    '</div>';
                if (window.OptimaSearch && typeof OptimaSearch.highlightForTable === 'function') {
                    html = OptimaSearch.highlightForTable($(tableId).DataTable(), html);
                }
                return html;
            }
        },
            { 
                data: 'departemen',
                render: function(data) {
                    let label = data || '-';
                    if (window.OptimaSearch && typeof OptimaSearch.highlightForTable === 'function') {
                        label = OptimaSearch.highlightForTable($(tableId).DataTable(), label);
                    }
                    return '<span class="badge badge-soft-blue">' + label + '</span>';
                }
            },
            { 
                data: 'nama_perusahaan',
                render: function(data, type, row) {
                    if (!data) return '-';
                    let label = data;
                    if (window.OptimaSearch && typeof OptimaSearch.highlightForTable === 'function') {
                        label = OptimaSearch.highlightForTable($(tableId).DataTable(), label);
                    }
                    return '<div class="customer-info-compact">' +
                        '<div class="fw-bold" style="font-size: 0.85rem;">' + label + '</div>' +
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
                    'BELUM_ADA': 'Belum Ada',
                    'PENGAJUAN_PJK3': 'Pengajuan PJK3',
                    'SURAT_KETERANGAN_PJK3': 'Surat Ket. PJK3',
                    'PENGAJUAN_UPTD': 'Pengajuan UPTD',
                    'SILO_TERBIT': 'SILO Terbit',
                    'SILO_EXPIRED': 'Expired'
                };
                const statusClasses = {
                    'BELUM_ADA': 'badge-soft-red',
                    'PENGAJUAN_PJK3': 'badge-soft-yellow',
                    'SURAT_KETERANGAN_PJK3': 'badge-soft-blue',
                    'PENGAJUAN_UPTD': 'badge-soft-yellow',
                    'SILO_TERBIT': 'badge-soft-green',
                    'SILO_EXPIRED': 'badge-soft-red'
                };
                const label = statusLabels[data] || data;
                const cls = statusClasses[data] || 'badge-soft-gray';
                return '<span class="badge ' + cls + '">' + label + '</span>';;
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
                        buttons.push('<button type="button" class="btn btn-xs btn-success" onclick="createSiloForUnit(' + row.id_silo + ')" title="Buat Pengajuan"><i class="fas fa-plus"></i></button>');
                    } else {
                        // Always show detail button
                        buttons.push('<button type="button" class="btn btn-xs btn-primary" onclick="showDetail(' + data + ')" title="Detail"><i class="fas fa-eye"></i></button>');
                        // Show update button based on status (stage by stage)
                        if (row.status !== 'SILO_TERBIT' && row.status !== 'SILO_EXPIRED') {
                            const actionLabel = getActionButtonLabel(row.status);
                            if (actionLabel) {
                                buttons.push('<button type="button" class="btn btn-xs btn-warning" onclick="showUpdateModal(' + data + ')" title="' + actionLabel + '"><i class="fas fa-arrow-right"></i></button>');
                            }
                        }
                    }

                    return '<div class="d-flex justify-content-center gap-1">' + buttons.join('') + '</div>';
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
                    // Ikutkan nilai pencarian global DataTables (jika ada)
                    search: (d && d.search && typeof d.search.value === 'string') ? d.search.value : ''
                };
                
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

    // Live search untuk daftar unit di modal (server-side, dengan debounce)
    $('#unit_search').on('keyup', function() {
        if (unitSearchTimer) {
            clearTimeout(unitSearchTimer);
        }
        unitSearchTimer = setTimeout(function() {
            loadAvailableUnits();
        }, 350);
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
let unitSearchTimer = null;

function loadAvailableUnits() {
    const search = $('#unit_search').val() || '';

    // Show loading
    $('#unit_checkboxes').html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div> <span class="ms-2">Loading units...</span></div>');
    
    $.ajax({
        url: '<?= base_url('perizinan/get-available-units') ?>',
        type: 'GET',
        dataType: 'json',
        data: {
            search: search,
            limit: 50
        },
        success: function(response) {
            if (response.success && response.data) {
                allAvailableUnits = response.data;
                renderUnitCheckboxes(allAvailableUnits);
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
        let displayText = unit.label;
        if (typeof window.OptimaUnitSelect2 !== 'undefined') {
            const Ou = OptimaUnitSelect2;
            const r = Ou.normalizeRow({
                id: unit.id,
                no_unit: unit.no_unit,
                serial_number: unit.serial_number || '',
                merk: unit.merk_unit,
                model_unit: unit.model_unit,
                jenis: unit.jenis_display || '',
                kapasitas: (unit.kapasitas_unit && unit.kapasitas_unit !== 'N/A') ? unit.kapasitas_unit : '',
                status: unit.status_name || '',
                lokasi: unit.lokasi_display || '',
                pelanggan: unit.nama_perusahaan || ''
            });
            displayText = Ou.line1FromRow(r);
            if (r.serial_number) {
                displayText += ' · SN: ' + r.serial_number;
            }
            if (unit.nama_perusahaan && unit.nama_perusahaan !== 'N/A') {
                displayText += ' · ' + unit.nama_perusahaan;
            }
        }

        // Highlight hasil pencarian (baik di no_unit, model, maupun SN) menggunakan helper global
        const termRaw = ($('#unit_search').val() || '').trim();
        if (window.OptimaSearch && typeof OptimaSearch.highlightText === 'function' && termRaw) {
            displayText = OptimaSearch.highlightText(displayText, termRaw);
        }
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
                html: displayText
            }));
        container.append(checkbox);
    });
}

function showCreateModal() {
    $('#createForm')[0].reset();
    $('#unit_search').val('');
    $('#unit_checkboxes').empty();
    new bootstrap.Modal(document.getElementById('createModal')).show();
    // Load initial units after modal is shown
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
        $('#unit_search').val(''); // reset search so prefill unit tetap bisa ditemukan
        loadAvailableUnits();
        // Wait for units to load, then check the checkbox
        setTimeout(function() {
            $('#unit_' + unitId).prop('checked', true);
            $('#tanggal_pengajuan_pjk3').val(new Date().toISOString().split('T')[0]);
        }, 600);
    }, 300);
}

function createSilo() {
    // Get selected unit IDs
    const selectedUnits = [];
    $('.unit-checkbox:checked').each(function() {
        selectedUnits.push($(this).val());
    });
    
    if (selectedUnits.length === 0) {
        OptimaNotify.warning('Select at least 1 unit');
        return;
    }
    
    const namaPtPjk3 = $('#nama_pt_pjk3').val();
    if (!namaPtPjk3 || namaPtPjk3.trim() === '') {
        OptimaNotify.warning('Nama PT PJK3 must be filled');
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
                OptimaNotify.success(response.message);
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
                OptimaNotify.error(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            OptimaNotify.error(response?.message || 'An error occurred while creating the submission');
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
                    OptimaNotify.warning('Status has reached the final stage and cannot be updated further.');
                    return;
                }

                $('#update_silo_id').val(siloId);
                $('#update_current_status').val(silo.status);
                
                let html = '<div class="mb-3">';
                html += '<label class="form-label">Unit: <strong>' + (silo.no_unit || 'N/A') + '</strong></label><br>';
                html += '<label class="form-label">Current Status: <span class="badge badge-soft-blue">' + getStatusLabel(silo.status) + '</span></label><br>';
                html += '<label class="form-label">Next Status: ' + getStatusBadge(nextStatus) + '</label>';
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
                    OptimaNotify.success(response.message);
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
                OptimaNotify.error(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            OptimaNotify.error(response?.message || 'An error occurred while updating the status');
        }
    });
}

function uploadFile(siloId, file, fileType) {
    // Check file size (15MB = 15728640 bytes)
    const maxSize = 15 * 1024 * 1024;
    if (file.size > maxSize) {
        OptimaNotify.error('Maximum file size is 15MB. Your file: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('file_type', fileType);

    // Show loading (no SweetAlert fallback)
    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showLoading === 'function') {
        OptimaPro.showLoading('Uploading...');
    }

    $.ajax({
        url: '<?= base_url('perizinan/upload-file/') ?>' + siloId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.hideLoading === 'function') {
                OptimaPro.hideLoading();
            }
            if (response.success) {
                OptimaNotify.success(response.message || 'File uploaded successfully');
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
                OptimaNotify.error(response.message || 'Failed to upload file');
            }
        },
        error: function(xhr, status, error) {
            if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.hideLoading === 'function') {
                OptimaPro.hideLoading();
            }
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
            
            OptimaNotify.error(errorMessage);
        }
    });
}

let currentDetailSiloId = null;

function openEditFromDetail() {
    if (!currentDetailSiloId) return;
    $('#detailModal').modal('hide');
    showEditModal(currentDetailSiloId);
}

function showDetail(siloId) {
    currentDetailSiloId = siloId;
    // Show modal immediately with spinner
    $('#detailModal').find('#detailEditBtn').hide();
    $('#detailModalBody').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted small">Memuat data...</div></div>');
    new bootstrap.Modal(document.getElementById('detailModal')).show();

    $.ajax({
        url: '<?= base_url('perizinan/get-silo-detail/') ?>' + siloId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (!response.success) {
                $('#detailModalBody').html('<div class="alert alert-danger">Gagal memuat data.</div>');
                return;
            }
            // Show Edit button in footer
            $('#detailEditBtn').show();
            const silo = response.data;
            const history = response.history || [];

            // ── Info cards ──
            let html = '<div class="row g-3 mb-3">';

            // Unit info
            html += '<div class="col-md-6"><div class="card border-0 bg-light h-100"><div class="card-body">';
            html += '<h6 class="fw-semibold text-primary mb-3"><i class="fas fa-truck me-2"></i>Informasi Unit</h6>';
            html += '<table class="table table-sm table-borderless mb-0">';
            html += '<tr><td class="text-muted small w-45">No. Unit</td><td class="small"><strong>' + (silo.no_unit || '-') + '</strong></td></tr>';
            html += '<tr><td class="text-muted small">Serial Number</td><td class="small">' + (silo.serial_number || '-') + '</td></tr>';
            html += '<tr><td class="text-muted small">Tipe Unit</td><td class="small">' + (silo.tipe_unit || '-') + '</td></tr>';
            html += '<tr><td class="text-muted small">Model</td><td class="small">' + (silo.model_unit || '-') + '</td></tr>';
            html += '<tr><td class="text-muted small">Kapasitas</td><td class="small">' + (silo.kapasitas_unit || '-') + '</td></tr>';
            html += '<tr><td class="text-muted small">Departemen</td><td class="small"><span class="badge badge-soft-blue">' + (silo.departemen || '-') + '</span></td></tr>';
            html += '<tr><td class="text-muted small">Customer</td><td class="small">' + (silo.nama_perusahaan || '-') + '</td></tr>';
            html += '</table>';
            html += '</div></div></div>';

            // SILO info
            html += '<div class="col-md-6"><div class="card border-0 bg-light h-100"><div class="card-body">';
            html += '<h6 class="fw-semibold text-success mb-3"><i class="fas fa-certificate me-2"></i>Informasi SILO</h6>';
            html += '<table class="table table-sm table-borderless mb-0">';
            html += '<tr><td class="text-muted small w-45">Status</td><td class="small">' + getStatusBadge(silo.status) + '</td></tr>';
            if (silo.nomor_silo) {
                html += '<tr><td class="text-muted small">Nomor SILO</td><td class="small"><strong>' + silo.nomor_silo + '</strong></td></tr>';
                html += '<tr><td class="text-muted small">Tanggal Terbit</td><td class="small">' + formatDate(silo.tanggal_terbit_silo) + '</td></tr>';
                html += '<tr><td class="text-muted small">Kedaluwarsa</td><td class="small">' + formatDate(silo.tanggal_expired_silo) + '</td></tr>';
            }
            if (silo.nama_pt_pjk3) { html += '<tr><td class="text-muted small">PT PJK3</td><td class="small">' + silo.nama_pt_pjk3 + '</td></tr>'; }
            if (silo.nomor_surat_keterangan_pjk3) {
                html += '<tr><td class="text-muted small">No. SK PJK3</td><td class="small">' + silo.nomor_surat_keterangan_pjk3 + '</td></tr>';
                html += '<tr><td class="text-muted small">Tgl. SK PJK3</td><td class="small">' + formatDate(silo.tanggal_surat_keterangan_pjk3) + '</td></tr>';
            }
            if (silo.lokasi_disnaker) { html += '<tr><td class="text-muted small">DISNAKER</td><td class="small">' + silo.lokasi_disnaker + '</td></tr>'; }
            html += '</table>';
            html += '</div></div></div>';
            html += '</div>'; // row

            // ── Timeline ──
            html += '<div class="card border-0 bg-light mb-3"><div class="card-body">';
            html += '<h6 class="fw-semibold mb-3"><i class="fas fa-route me-2 text-primary"></i>Alur Proses</h6>';
            const tlStatuses = ['PENGAJUAN_PJK3', 'SURAT_KETERANGAN_PJK3', 'PENGAJUAN_UPTD', 'SILO_TERBIT'];
            const currentIndex = getStatusIndex(silo.status);
            html += '<div class="d-flex align-items-start" style="overflow-x:auto;">';
            tlStatuses.forEach(function(st, i) {
                const done   = currentIndex > i;
                const active = currentIndex === i;
                const dates  = {'PENGAJUAN_PJK3': silo.tanggal_pengajuan_pjk3, 'SURAT_KETERANGAN_PJK3': silo.tanggal_surat_keterangan_pjk3, 'PENGAJUAN_UPTD': silo.tanggal_pengajuan_uptd, 'SILO_TERBIT': silo.tanggal_terbit_silo};
                const cStyle = done   ? 'background:#198754;border-color:#198754;color:#fff;'
                             : active ? 'background:#0d6efd;border-color:#0d6efd;color:#fff;'
                                      : 'background:#fff;border-color:#dee2e6;color:#adb5bd;';
                const lColor = done ? '#198754' : active ? '#0d6efd' : '#adb5bd';
                html += '<div class="text-center flex-shrink-0" style="min-width:100px;">';
                html += '<div style="width:44px;height:44px;border-radius:50%;border:3px solid;margin:0 auto 6px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;' + cStyle + '">' + (done ? '<i class="fas fa-check"></i>' : (i+1)) + '</div>';
                html += '<div style="font-size:.72rem;font-weight:600;color:' + lColor + ';">' + getStatusLabel(st) + '</div>';
                html += '<div style="font-size:.67rem;color:#adb5bd;">' + (dates[st] ? formatDate(dates[st]) : '—') + '</div>';
                html += '</div>';
                if (i < tlStatuses.length - 1) {
                    html += '<div style="flex:1;height:3px;background:' + (done ? '#198754' : '#dee2e6') + ';margin-top:20px;min-width:20px;"></div>';
                }
            });
            html += '</div>';
            html += '</div></div>';

            // ── Documents (lazy load) ──
            const hasPjk3File = !!(silo.file_surat_keterangan_pjk3 && silo.file_surat_keterangan_pjk3.trim());
            const hasSiloFile = !!(silo.file_silo && silo.file_silo.trim());
            html += '<div class="card border-0 bg-light"><div class="card-body">';
            html += '<h6 class="fw-semibold mb-3"><i class="fas fa-paperclip me-2 text-primary"></i>Dokumen</h6>';
            if (!hasPjk3File && !hasSiloFile) {
                html += '<div class="text-muted small"><i class="fas fa-info-circle me-1"></i>Belum ada dokumen yang diupload.</div>';
            } else {
                html += '<div class="row g-2">';
                if (hasPjk3File) {
                    const pjk3DownloadUrl = '<?= base_url('perizinan/download-file/') ?>' + siloId + '/pjk3';
                    html += '<div class="col-md-6"><div class="border rounded p-3 text-center bg-white">';
                    html += '<div class="mb-2"><i class="fas fa-file-alt fa-2x text-primary"></i></div>';
                    html += '<div class="fw-semibold small mb-2">Surat Keterangan PJK3</div>';
                    html += '<button class="btn btn-sm btn-outline-primary me-1" onclick="loadDocPreview(' + siloId + ', \'pjk3\', this)" data-loaded="0"><i class="fas fa-eye me-1"></i>Lihat</button>';
                    html += '<a href="' + pjk3DownloadUrl + '" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-download me-1"></i>Unduh</a>';
                    html += '<div id="doc_preview_pjk3_' + siloId + '" class="mt-2"></div>';
                    html += '</div></div>';
                }
                if (hasSiloFile) {
                    const siloDownloadUrl = '<?= base_url('perizinan/download-file/') ?>' + siloId + '/silo';
                    html += '<div class="col-md-6"><div class="border rounded p-3 text-center bg-white">';
                    html += '<div class="mb-2"><i class="fas fa-certificate fa-2x text-success"></i></div>';
                    html += '<div class="fw-semibold small mb-2">File SILO</div>';
                    html += '<button class="btn btn-sm btn-outline-success me-1" onclick="loadDocPreview(' + siloId + ', \'silo\', this)" data-loaded="0"><i class="fas fa-eye me-1"></i>Lihat</button>';
                    html += '<a href="' + siloDownloadUrl + '" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-download me-1"></i>Unduh</a>';
                    html += '<div id="doc_preview_silo_' + siloId + '" class="mt-2"></div>';
                    html += '</div></div>';
                }
                html += '</div>';
            }
            html += '</div></div>';

            $('#detailModalBody').html(html);
        },
        error: function() {
            $('#detailModalBody').html('<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>');
        }
    });
}

// Lazy-load document preview in detail modal
function loadDocPreview(siloId, type, btn) {
    const $btn = $(btn);
    const $preview = $('#doc_preview_' + type + '_' + siloId);

    // If already loaded, toggle visibility
    if ($btn.data('loaded') == 1) {
        $preview.toggle();
        $btn.html($preview.is(':visible') ? '<i class="fas fa-eye-slash me-1"></i>Sembunyikan' : '<i class="fas fa-eye me-1"></i>Lihat');
        return;
    }

    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Memuat...');
    const previewUrl = '<?= base_url('perizinan/preview-file/') ?>' + siloId + '/' + type;

    // Try as image first; on error fall back to iframe (PDF)
    const $img = $('<img>').addClass('img-fluid rounded border mt-1').css('max-height', '500px')
        .on('load', function() {
            $btn.prop('disabled', false).html('<i class="fas fa-eye-slash me-1"></i>Sembunyikan').data('loaded', 1);
        })
        .on('error', function() {
            // Likely PDF — use iframe
            const $fr = $('<iframe>').attr('src', previewUrl).css({width: '100%', height: '600px', border: '1px solid #dee2e6', borderRadius: '0.375rem'});
            $preview.html($fr);
            $btn.prop('disabled', false).html('<i class="fas fa-eye-slash me-1"></i>Sembunyikan').data('loaded', 1);
        })
        .attr('src', previewUrl);
    $preview.html($img);
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
function getStatusBadge(status) {
    const classes = {
        'BELUM_ADA':             'badge-soft-red',
        'PENGAJUAN_PJK3':        'badge-soft-yellow',
        'SURAT_KETERANGAN_PJK3': 'badge-soft-blue',
        'PENGAJUAN_UPTD':        'badge-soft-yellow',
        'SILO_TERBIT':           'badge-soft-green',
        'SILO_EXPIRED':          'badge-soft-red',
    };
    return '<span class="badge ' + (classes[status] || 'badge-soft-gray') + '">' + getStatusLabel(status) + '</span>';
}

// Kept for backward compat (updateModal still uses getStatusColor indirectly via html construction)
function getStatusColor(status) {
    const colors = {
        'BELUM_ADA': 'danger', 'PENGAJUAN_PJK3': 'warning',
        'SURAT_KETERANGAN_PJK3': 'info', 'PENGAJUAN_UPTD': 'warning',
        'SILO_TERBIT': 'success', 'SILO_EXPIRED': 'danger'
    };
    return colors[status] || 'secondary';
}

function showEditModal(siloId) {
    // Reset form
    $('#editForm')[0].reset();
    $('#editAlert').addClass('d-none').html('');
    $('#edit_pjk3_current, #edit_silo_current').addClass('d-none');

    $.ajax({
        url: '<?= base_url('perizinan/get-silo-edit/') ?>' + siloId,
        type: 'GET',
        dataType: 'json',
        success: function(r) {
            if (!r.success) { OptimaNotify.error(r.message || 'Gagal memuat data'); return; }
            const d = r.data;
            $('#edit_silo_id').val(d.id_silo);
            $('#edit_nama_pt_pjk3').val(d.nama_pt_pjk3 || '');
            $('#edit_tanggal_pengajuan_pjk3').val(d.tanggal_pengajuan_pjk3 || '');
            $('#edit_tanggal_testing_pjk3').val(d.tanggal_testing_pjk3 || '');
            $('#edit_hasil_testing_pjk3').val(d.hasil_testing_pjk3 || '');
            $('#edit_nomor_sk_pjk3').val(d.nomor_surat_keterangan_pjk3 || '');
            $('#edit_tanggal_sk_pjk3').val(d.tanggal_surat_keterangan_pjk3 || '');
            $('#edit_catatan_pjk3').val(d.catatan_pengajuan_pjk3 || '');
            $('#edit_tanggal_pengajuan_uptd').val(d.tanggal_pengajuan_uptd || '');
            $('#edit_lokasi_disnaker').val(d.lokasi_disnaker || '');
            $('#edit_nomor_silo').val(d.nomor_silo || '');
            $('#edit_tanggal_terbit_silo').val(d.tanggal_terbit_silo || '');
            $('#edit_tanggal_expired_silo').val(d.tanggal_expired_silo || '');

            if (d.file_surat_keterangan_pjk3) {
                const n = d.file_surat_keterangan_pjk3.split('/').pop();
                $('#edit_pjk3_name').text(n);
                $('#edit_pjk3_link').attr('href', '<?= base_url() ?>' + d.file_surat_keterangan_pjk3);
                $('#edit_pjk3_current').removeClass('d-none');
            }
            if (d.file_silo) {
                const n2 = d.file_silo.split('/').pop();
                $('#edit_silo_name').text(n2);
                $('#edit_silo_link').attr('href', '<?= base_url() ?>' + d.file_silo);
                $('#edit_silo_current').removeClass('d-none');
            }

            new bootstrap.Modal(document.getElementById('editModal')).show();
        },
        error: function() { OptimaNotify.error('Gagal memuat data SILO'); }
    });
}

$('#editForm').on('submit', function(e) {
    e.preventDefault();
    const siloId = $('#edit_silo_id').val();
    const $btn = $('#btnEditSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');
    const fd = new FormData(this);

    $.ajax({
        url: '<?= base_url('perizinan/update-silo/') ?>' + siloId,
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(r) {
            if (r.success) {
                OptimaNotify.success(r.message || 'Data berhasil disimpan');
                $('#editModal').modal('hide');
                refreshAllTables();
            } else {
                $('#editAlert').removeClass('d-none').addClass('alert alert-danger').html(r.message || 'Gagal menyimpan');
            }
        },
        error: function(xhr) {
            $('#editAlert').removeClass('d-none').addClass('alert alert-danger').html((xhr.responseJSON && xhr.responseJSON.message) || 'Terjadi kesalahan');
        },
        complete: function() {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan Perubahan');
        }
    });
});

function refreshAllTables() {
    if (siloTable)  siloTable.ajax.reload();
    if (siloTable2) siloTable2.ajax.reload();
    if (siloTable3) siloTable3.ajax.reload();
    if (siloTable4) siloTable4.ajax.reload();
    if (siloTable5) siloTable5.ajax.reload();
    if (siloTable6) siloTable6.ajax.reload();
    updateTabBadges();
}

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

