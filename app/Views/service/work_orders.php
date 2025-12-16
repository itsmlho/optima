<?= $this->extend('layouts/base') ?>

<?php
// Load global permission helper
helper('global_permission');

// Get permissions for service module
$permissions = get_global_permission('service');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];

// Initialize Phase 3 optimization services
$lazyService = new \App\Services\LazyLoadingService();
$assetService = new \App\Services\AssetMinificationService();
?>

<?= $this->section('content') ?>

<!-- Alert Container -->
<div id="alertContainer" class="mb-3"></div>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-file-text stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-work-orders">0</div>
                    <div class="text-muted">Total Work Orders</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-info-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-folder2-open stat-icon text-info"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-open">0</div>
                    <div class="text-muted">Open</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-gear stat-icon text-warning"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-in-progress">0</div>
                    <div class="text-muted">In Progress</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-check-circle stat-icon text-success"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-completed">0</div>
                    <div class="text-muted">Completed</div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Tab System for Work Orders -->
<div class="card table-card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Daftar Work Orders</h5>
            <div class="d-flex gap-2">
                <?php if ($can_export): ?>
                <a href="<?= base_url('service/export_workorder') ?>" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <?php else: ?>
                <a href="#" class="btn btn-outline-success btn-sm disabled" onclick="return false;" title="Access Denied">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <?php endif; ?>
                <?php if ($can_create): ?>
                <button id="btn-add-wo" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Tambah Work Order</button>
                <?php else: ?>
                <button id="btn-add-wo" class="btn btn-primary btn-sm disabled" onclick="return false;" title="Access Denied"><i class="fas fa-plus me-1"></i> Tambah Work Order</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Tab Navigation -->
        <div class="card table-card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <ul class="nav nav-tabs flex-grow-1" id="workOrderTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress-pane" type="button" role="tab" aria-controls="progress-pane" aria-selected="true">
                            <i class="fas fa-tasks"></i>
                            <span>Progress</span>
                            <span class="badge" id="progress-count">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="closed-tab" data-bs-toggle="tab" data-bs-target="#closed-pane" type="button" role="tab" aria-controls="closed-pane" aria-selected="false">
                            <i class="fas fa-check-circle"></i>
                            <span>Closed</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="workOrderTabContent">
            <!-- Progress Tab -->
            <div class="tab-pane fade show active" id="progress-pane" role="tabpanel" aria-labelledby="progress-tab">
                <!-- Filter Controls for Progress -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-status-progress">Status</label>
                            <select id="filter-status-progress" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <?php foreach ($statuses as $status): ?>
                                    <?php if (strtolower($status['status_name']) !== 'closed'): ?>
                                    <option value="<?= $status['status_name'] ?>"><?= $status['status_name'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-priority-progress">Prioritas</label>
                            <select id="filter-priority-progress" class="form-select form-select-sm">
                                <option value="">Semua Prioritas</option>
                                <?php foreach ($priorities as $priority): ?>
                                <option value="<?= $priority['priority_name'] ?>"><?= $priority['priority_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-start-date-progress">Tanggal Mulai</label>
                            <input type="date" id="filter-start-date-progress" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-end-date-progress">Tanggal Akhir</label>
                            <input type="date" id="filter-end-date-progress" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                
                <!-- Progress Table -->
                <?php if (!can_view('service')): ?>
                <div class="alert alert-warning m-3">
                    <i class="fas fa-lock me-2"></i>
                    <strong>Access Denied:</strong> You do not have permission to view work orders. 
                    Please contact your administrator to request access.
                </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="progressWorkOrdersTable" class="table table-striped table-hover <?= !$can_view ? 'table-disabled' : '' ?>">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nomor WO</th>
                                <th>Tanggal</th>
                                <th>Unit</th>
                                <th>Tipe</th>
                                <th>Prioritas</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded dynamically via DataTable -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Closed Tab -->
            <div class="tab-pane fade" id="closed-pane" role="tabpanel" aria-labelledby="closed-tab">
                <!-- Filter Controls for Closed -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-priority-closed">Prioritas</label>
                            <select id="filter-priority-closed" class="form-select form-select-sm">
                                <option value="">Semua Prioritas</option>
                                <?php foreach ($priorities as $priority): ?>
                                <option value="<?= $priority['priority_name'] ?>"><?= $priority['priority_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-start-date-closed">Tanggal Mulai</label>
                            <input type="date" id="filter-start-date-closed" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-end-date-closed">Tanggal Akhir</label>
                            <input type="date" id="filter-end-date-closed" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-month-closed">Filter Bulan</label>
                            <select id="filter-month-closed" class="form-select form-select-sm">
                                <option value="">Semua Bulan</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Closed Table -->
                <?php if (!can_view('service')): ?>
                <div class="alert alert-warning m-3">
                    <i class="fas fa-lock me-2"></i>
                    <strong>Access Denied:</strong> You do not have permission to view closed work orders. 
                    Please contact your administrator to request access.
                </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="closedWorkOrdersTable" class="table table-striped table-hover <?= !$can_view ? 'table-disabled' : '' ?>">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nomor WO</th>
                                <th>Tanggal</th>
                                <th>Unit</th>
                                <th>Tipe</th>
                                <th>Prioritas</th>
                                <th>Kategori</th>
                                <th>Tanggal Closed</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded dynamically via DataTable -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals Section -->

<!-- Modal Add/Edit Work Order -->
<div class="modal fade" id="workOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workOrderFormTitle"><i class="fas fa-plus-circle me-2"></i>Tambah Work Order Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="workOrderForm" action="<?= base_url('service/work-orders/store') ?>" method="post" novalidate>
                    <input type="hidden" id="work_order_id" name="work_order_id">
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Informasi Utama Work Order</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="work_order_number" class="form-label">Nomor Work Order</label>
                                    <input type="text" class="form-control" id="work_order_number" name="work_order_number" readonly>
                                    <small class="form-text text-muted">Nomor WO akan terisi otomatis (+1 dari WO terakhir)</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="order_type" class="form-label">Tipe Order <span class="text-danger">*</span></label>
                                    <select class="form-select" id="order_type" name="order_type" required>
                                        <option value="" selected disabled>-- Pilih Tipe Order --</option>
                                        <option value="COMPLAINT">Complaint</option>
                                        <option value="PMPS">PMPS</option>
                                        <option value="FABRIKASI">Fabrikasi</option>
                                        <option value="PERSIAPAN">Persiapan</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                                    <select class="form-select" id="unit_id" name="unit_id" required>
                                        <option value="" selected disabled>-- Pilih Unit --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="" selected disabled>-- Pilih Kategori --</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" data-priority="<?= $category['default_priority_id'] ?? '' ?>"><?= $category['category_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subcategory_id" class="form-label">Sub Kategori</label>
                                    <select class="form-select" id="subcategory_id" name="subcategory_id">
                                        <option value="">-- Pilih Sub Kategori (jika ada) --</option>
                                    </select>
                                    <small class="form-text text-muted">Sub kategori akan muncul setelah memilih kategori</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="priority_display" class="form-label">Prioritas</label>
                                    <input type="text" class="form-control" id="priority_display" readonly placeholder="Otomatis berdasarkan kategori">
                                    <input type="hidden" id="priority_id" name="priority_id">
                                    <small class="form-text text-muted">Prioritas otomatis berdasarkan kategori & sub kategori</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="area" class="form-label">Area <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="area" name="area" readonly placeholder="Area akan terisi otomatis berdasarkan unit">
                                    <input type="hidden" id="area_id" name="area_id">
                                    <small class="form-text text-muted">Area akan terisi otomatis berdasarkan unit yang dipilih</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pic_name" class="form-label">PIC <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pic_name" name="pic_name" placeholder="Masukkan nama PIC" required>
                                    <small class="form-text text-muted">contoh: Adit (082136033596)</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="complaint_description" class="form-label">Deskripsi Keluhan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="complaint_description" name="complaint_description" rows="3" placeholder="Jelaskan keluhan atau permintaan pekerjaan secara detail..." required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-users-cog me-2"></i>Penugasan Staff</h6>
                        </div>
                        <div class="card-body">
                            <!-- Admin & Foreman - Auto Fill -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="admin_id" class="form-label">Admin</label>
                                    <input type="text" class="form-control" id="admin_display" readonly>
                                    <input type="hidden" id="admin_id" name="admin_id">
                                    <small class="form-text text-muted">Otomatis berdasarkan area</small>
                                        </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foreman_id" class="form-label">Foreman</label>
                                    <input type="text" class="form-control" id="foreman_display" readonly>
                                    <input type="hidden" id="foreman_id" name="foreman_id">
                                    <small class="form-text text-muted">Otomatis berdasarkan area</small>
                                </div>
                            </div>
                            
                            <!-- Mekanik - Pilihan 1-2 orang -->
                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Mekanik <span class="text-danger">*</span> <small class="text-muted">(Min 1, Max 2)</small></label>
                                    <div id="mechanic-container">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="mechanic_1" name="mechanic_id[]">
                                                    <option value="" selected disabled>-- Pilih Mekanik 1 --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="mechanic_2" name="mechanic_id[]">
                                                    <option value="" selected disabled>-- Pilih Mekanik 2 (Opsional) --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>     
                            <!-- Helper - Pilihan 1-2 orang -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Helper <span class="text-danger">*</span> <small class="text-muted">(Min 1, Max 2)</small></label>
                                    <div id="helper-container">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="helper_1" name="helper_id[]">
                                                    <option value="" selected disabled>-- Pilih Helper 1 --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="helper_2" name="helper_id[]">
                                                    <option value="" selected disabled>-- Pilih Helper 2 (Opsional) --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                            
                    <!-- Sparepart yang Dibawa -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Sparepart yang Dibawa</h6>
                            </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="sparepartTable">
                                    <thead>
                                        <tr>
                                            <th width="50%">Nama Sparepart*</th>
                                            <th width="20%">Kuantiti*</th>
                                            <th width="20%">Satuan*</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sparepartTableBody">
                                        <!-- Dynamic rows will be added here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-sm" id="addSparepartRow">
                                    <i class="fas fa-plus"></i> Tambah Sparepart
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Fields dengan tanda <span class="text-danger">*</span> wajib diisi</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="event.stopPropagation();">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" form="workOrderForm" id="btnSubmitWo" onclick="event.stopPropagation();">
                        <i class="fas fa-save me-1"></i> Simpan Work Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Work Order -->
<div class="modal fade" id="viewWorkOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Detail Work Order: <span id="viewWoNumberHeader" class="fw-bold">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-body-tertiary p-4">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6 class="mb-3 text-dark">Informasi Pekerjaan</h6>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 text-muted">Tanggal Lapor</dt>
                                        <dd class="col-sm-8" id="viewWoReportDate">-</dd>
                                        <dt class="col-sm-4 text-muted">Tipe Order</dt>
                                        <dd class="col-sm-8" id="viewWoType">-</dd>
                                        <dt class="col-sm-4 text-muted">Kategori</dt>
                                        <dd class="col-sm-8" id="viewWoCategory">-</dd>
                                        <dt class="col-sm-4 text-muted">Departemen</dt>
                                        <dd class="col-sm-8" id="viewWoDepartemen">-</dd>
                                    </dl>
                                </div>
                                <hr>
                                <div>
                                    <h6 class="mb-3 text-dark">Detail Unit & Komponen</h6>
                                     <dl class="row mb-0">
                                        <dt class="col-sm-4 text-muted">Nomor Unit</dt>
                                        <dd class="col-sm-8 fw-bold text-primary" id="viewUnitNumber">-</dd>
                                        <dt class="col-sm-4 text-muted">Merk & Model</dt>
                                        <dd class="col-sm-8" id="viewUnitModel">-</dd>
                                        <dt class="col-sm-4 text-muted">Tipe Unit</dt>
                                        <dd class="col-sm-8" id="viewUnitType">-</dd>
                                        <dt class="col-sm-4 text-muted">Serial Number</dt>
                                        <dd class="col-sm-8 font-monospace" id="viewUnitSerial">-</dd>
                                        <dt class="col-sm-4 text-muted">Departemen</dt>
                                        <dd class="col-sm-8"><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" id="viewUnitDepartemen">-</span></dd>
                                        <dt class="col-sm-4 text-muted">Status Unit</dt>
                                        <dd class="col-sm-8"><span class="badge bg-success-subtle text-success-emphasis border border-success-subtle" id="viewUnitStatus">-</span></dd>
                                        <dt class="col-sm-4 text-muted">Kapasitas</dt>
                                        <dd class="col-sm-8" id="viewUnitCapacity">-</dd>
                                        <dt class="col-sm-4 text-muted">Tahun Unit</dt>
                                        <dd class="col-sm-8" id="viewUnitYear">-</dd>
                                        <dt class="col-sm-4 text-muted">Model Mesin</dt>
                                        <dd class="col-sm-8" id="viewUnitEngine">-</dd>
                                        <dt class="col-sm-4 text-muted">SN Mesin</dt>
                                        <dd class="col-sm-8 font-monospace" id="viewUnitEngineSN">-</dd>
                                        <dt class="col-sm-4 text-muted">Model Mast</dt>
                                        <dd class="col-sm-8" id="viewUnitMast">-</dd>
                                        <dt class="col-sm-4 text-muted">SN Mast</dt>
                                        <dd class="col-sm-8 font-monospace" id="viewUnitMastSN">-</dd>
                                        <dt class="col-sm-4 text-muted">Tinggi Mast</dt>
                                        <dd class="col-sm-8" id="viewUnitMastHeight">-</dd>

                                        <div id="unitComponentsSection" class="contents" style="display: none;">
                                            <dt class="col-sm-4 text-muted pt-2">Attachment</dt>
                                            <dd class="col-sm-8 pt-2" id="viewUnitAttachmentList">-</dd>

                                            <dt id="batteryLabel" class="col-sm-4 text-muted" style="display: none;">Battery</dt>
                                            <dd id="batteryValue" class="col-sm-8" style="display: none;">
                                                <span id="viewUnitBatteryList">-</span>
                                            </dd>
                                            
                                            <dt id="chargerLabel" class="col-sm-4 text-muted" style="display: none;">Charger</dt>
                                            <dd id="chargerValue" class="col-sm-8" style="display: none;">
                                                <span id="viewUnitChargerList">-</span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-5">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="text-center p-3 rounded bg-body-secondary mb-4">
                                    <div class="row">
                                        <div class="col-6 border-end">
                                            <label class="small text-muted mb-1">Status</label>
                                            <div><span class="badge fs-6" id="viewWoStatus">-</span></div>
                                        </div>
                                        <div class="col-6">
                                            <label class="small text-muted mb-1">Prioritas</label>
                                            <div><span class="badge fs-6" id="viewWoPriority">-</span></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <h6 class="mb-3 text-dark">Pelanggan & Lokasi</h6>
                                <dl class="row mb-4">
                                    <dt class="col-sm-4 text-muted">Pelanggan</dt>
                                    <dd class="col-sm-8 fw-bold" id="viewUnitCustomer">-</dd>
                                    <dt class="col-sm-4 text-muted">Lokasi</dt>
                                    <dd class="col-sm-8" id="viewUnitLocation">-</dd>
                                    <dt class="col-sm-4 text-muted">Area</dt>
                                    <dd class="col-sm-8" id="viewWoArea">-</dd>
                                </dl>
                                
                                <h6 class="mb-3 text-dark">Penugasan Staff</h6>
                                <ul class="list-unstyled mb-4">
                                    <li class="d-flex align-items-center mb-2"><i class="fas fa-user-shield fa-fw me-2 text-muted"></i> <strong>Admin:</strong> <span class="ms-auto" id="viewWoAdmin">-</span></li>
                                    <li class="d-flex align-items-center mb-2"><i class="fas fa-user-tie fa-fw me-2 text-muted"></i> <strong>Foreman:</strong> <span class="ms-auto" id="viewWoForeman">-</span></li>
                                    <li class="d-flex align-items-center mb-2"><i class="fas fa-user-cog fa-fw me-2 text-muted"></i> <strong>Mekanik:</strong> <span class="ms-auto" id="viewWoMechanic">-</span></li>
                                    <li class="d-flex align-items-center"><i class="fas fa-user-friends fa-fw me-2 text-muted"></i> <strong>Helper:</strong> <span class="ms-auto" id="viewWoHelper">-</span></li>
                                </ul>

                                <h6 class="mb-3 text-dark">Waktu & Tanggal</h6>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4 text-muted">TTR</dt>
                                    <dd class="col-sm-8 fw-bold text-primary" id="viewWoTTR">-</dd>
                                    <dt class="col-sm-4 text-muted">Tgl. Selesai</dt>
                                    <dd class="col-sm-8" id="viewWoCompletionDate">Belum selesai</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="unitAccessoriesSection" class="card mt-4" style="display: none;">
                    <div class="card-header">
                         <h6 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i>Aksesoris Unit</h6>
                    </div>
                    <div class="card-body">
                        <div id="viewUnitAccessoriesList" class="component-list">
                            </div>
                    </div>
                </div>

                <div id="sparepartBroughtSection" class="card mt-4" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Sparepart Dibawa</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 40%;">Nama Spare Part</th>
                                        <th style="width: 15%;">Code</th>
                                        <th style="width: 10%;">QTY</th>
                                        <th style="width: 30%;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="viewSparepartBroughtList">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada sparepart yang dibawa</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i>Detail Pekerjaan & Catatan</h6>
                    </div>
                    <div class="card-body">
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Keluhan Pelanggan</label>
                                <div class="p-3 rounded bg-light" style="min-height: 120px;" id="viewWoComplaint"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Analisa & Perbaikan</label>
                                <div class="p-3 rounded bg-light" style="min-height: 120px;" id="viewWoRepair"></div>
                            </div>
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-muted">Sparepart Digunakan</label>
                                <div class="p-3 rounded bg-light" style="min-height: 80px;" id="viewWoSparepart"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Catatan Tambahan</label>
                                <div class="p-3 rounded bg-light" style="min-height: 80px;" id="viewWoNotes"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-print-from-view" data-id="" id="btnPrintFromView">
                    <i class="fas fa-print me-1"></i>Print Work Order
                </button>
                <button type="button" class="btn btn-primary btn-edit-from-view" data-id="" id="btnEditFromView">
                    <i class="fas fa-edit me-1"></i>Edit Work Order
                </button>
                <button type="button" class="btn btn-danger btn-delete-from-view" data-id="" data-wo-number="">
                    <i class="fas fa-trash me-1"></i>Hapus
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Phase 3: Lazy loading JavaScript -->
<?= $lazyService->getLazyLoadingScript() ?>
<?= $lazyService->getLazyContentScript() ?>
<script>
// Global permission variables (accessible from all functions)
const canViewService = <?= $can_view ? 'true' : 'false' ?>;
const canCreateService = <?= $can_create ? 'true' : 'false' ?>;
const canExportService = <?= $can_export ? 'true' : 'false' ?>;

$(document).ready(function() {
    
    // Initialize global spareparts data for dropdowns
    <?php if (!empty($spareparts)): ?>
        window.sparepartsData = <?= json_encode($spareparts) ?>;
    <?php else: ?>
        window.sparepartsData = [];
    <?php endif; ?>
    
    // Force close all modals on page load with multiple methods (except work order modal)
    setTimeout(function() {
        // Method 1: jQuery - only close unit verification modal
        $('#unitVerificationModal').modal('hide');
        
        // Method 2: Bootstrap native - only for unit verification modal
        if (window.bootstrap) {
            const unitVerificationModal = document.getElementById('unitVerificationModal');
            if (unitVerificationModal) {
                const bsModal = bootstrap.Modal.getInstance(unitVerificationModal);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        }
        
        // Method 3: Force DOM cleanup - only for unit verification modal
        $('#unitVerificationModal').removeClass('show').hide();
        
        // Only remove modal-open class if no important modals are shown
        if (!$('#workOrderModal').hasClass('show') && !$('#viewWorkOrderModal').hasClass('show')) {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        }
        
        // Method 4: Reset modal attributes - only for unit verification modal
        $('#unitVerificationModal').attr('aria-hidden', 'true').css('display', 'none');
        
        // Remove force hide CSS after cleanup
        setTimeout(function() {
            const style = document.createElement('style');
            style.innerHTML = `
                .modal.show { display: block !important; }
                body.modal-open { overflow: hidden !important; }
                .modal-backdrop { display: block !important; }
            `;
            document.head.appendChild(style);
        }, 500);
        
    }, 100);
    
    // Initialize DataTables for both tabs
    let progressTable = null;
    let closedTable = null;
    
    // Use standard DataTable initialization for better compatibility
    progressTable = $('#progressWorkOrdersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('service/work-orders/data') ?>',
            type: 'POST',
            data: function(d) {
                d.tab = 'progress';
                d.useOptimized = true;
                d.status = $('#filter-status-progress').val();
                d.priority = $('#filter-priority-progress').val();
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false }, // Row number
            { data: 1 }, // work_order_number
            { data: 2 }, // report_date
            { data: 3 }, // unit_info
            { data: 4 }, // order_type
            { data: 5 }, // priority_badge
            { data: 6 }, // category
            { data: 7 }, // status_badge
            { data: 8, orderable: false, searchable: false } // action
        ],
        order: [[2, 'desc']], // Order by report_date descending
        language: {
            "sProcessing": "Sedang memproses...",
            "sLengthMenu": "Tampilkan _MENU_ data",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "sInfoFiltered": "(disaring dari _MAX_ total data)",
            "sInfoPostFix": "",
            "sSearch": "Cari:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            }
        },
        drawCallback: function(settings) {
            updateProgressCount(settings.json.recordsFiltered || 0);
        },
        createdRow: function(row, data, dataIndex) {
            // Add click event to row (except action column)
            $(row).addClass('clickable-row');
            // DataTable automatically applies DT_RowAttr to the row, so we can access them directly
            let woId = $(row).attr('data-wo-id');
            let woNumber = $(row).attr('data-wo-number');
            let statusCode = $(row).attr('data-status-code');
            
            // Set additional data for easy access
            $(row).data('wo-id', woId);
            $(row).data('wo-number', woNumber);
            $(row).data('status-code', statusCode);
        }
    });

    closedTable = $('#closedWorkOrdersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('service/work-orders/data') ?>',
            type: 'POST',
            data: function(d) {
                d.tab = 'closed';
                d.useOptimized = true;
                d.priority = $('#filter-priority-closed').val();
                d.month = $('#filter-month-closed').val();
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false }, // Row number
            { data: 1 }, // work_order_number
            { data: 2 }, // report_date
            { data: 3 }, // unit_info
            { data: 4 }, // order_type
            { data: 5 }, // priority_badge
            { data: 6 }, // category
            { data: 9 }, // closed_date
            { data: 8, orderable: false, searchable: false } // action
        ],
        order: [[7, 'desc']], // Order by closed_date descending
        language: {
            "sProcessing": "Sedang memproses...",
            "sLengthMenu": "Tampilkan _MENU_ data",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "sInfoFiltered": "(disaring dari _MAX_ total data)",
            "sInfoPostFix": "",
            "sSearch": "Cari:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            }
        },
        drawCallback: function(settings) {
            updateClosedCount(settings.json.recordsFiltered || 0);
        },
        createdRow: function(row, data, dataIndex) {
            // Add click event to row (except action column)
            $(row).addClass('clickable-row');
            // DataTable automatically applies DT_RowAttr to the row, so we can access them directly
            let woId = $(row).attr('data-wo-id');
            let woNumber = $(row).attr('data-wo-number');
            let statusCode = $(row).attr('data-status-code');
            
            // Set additional data for easy access
            $(row).data('wo-id', woId);
            $(row).data('wo-number', woNumber);
            $(row).data('status-code', statusCode);
        }
    });
    
    // Helper functions for safe table reload
    function reloadProgressTable() {
        if (progressTable && typeof progressTable.ajax !== 'undefined') {
            progressTable.ajax.reload();
        }
    }
    
    function reloadClosedTable() {
        if (closedTable && typeof closedTable.ajax !== 'undefined') {
            closedTable.ajax.reload();
        }
    }

    // Update count functions
    function updateProgressCount(count) {
        $('#progress-count').text(count);
    }

    function updateClosedCount(count) {
        // Count badge removed for closed tab
        // $('#closed-count').text(count);
    }

    // Initialize closed table when closed tab is first shown
    $('#closed-tab').on('shown.bs.tab', function (e) {
        // Force reload closed table
        reloadClosedTable();
        // Adjust column sizing
        setTimeout(function() {
            if (closedTable && typeof closedTable.columns !== 'undefined') {
                closedTable.columns.adjust();
                if (closedTable.responsive) {
                    closedTable.responsive.recalc();
                }
            }
        }, 100);
        console.log('Closed tab activated - reloading data');
    });
    
    // Also handle click event for closed tab
    $('#closed-tab').on('click', function(e) {
        // Small delay to ensure tab is fully shown
        setTimeout(function() {
            if ($('#closed-tab').hasClass('active')) {
                reloadClosedTable();
                // Adjust column sizing
                setTimeout(function() {
                    closedTable.columns.adjust();
                    closedTable.responsive.recalc();
                }, 100);
            }
        }, 150);
    });

    // Ensure Progress tab is active on page load and reload progress table
    $(document).ready(function() {
        // Force Progress tab to be active
        $('#progress-tab').addClass('active').attr('aria-selected', 'true');
        $('#closed-tab').removeClass('active').attr('aria-selected', 'false');
        
        // Show Progress pane and hide Closed pane
        $('#progress-pane').addClass('show active');
        $('#closed-pane').removeClass('show active');
        
        // Reload progress table to ensure data is loaded
        setTimeout(function() {
            reloadProgressTable();
            // Load initial statistics
            updateStatistics();
        }, 100);
    });

    // Filter handlers for Progress tab (status and priority only, date handled by helper)
    $('#filter-status-progress, #filter-priority-progress').on('change', function() {
        reloadProgressTable();
    });

    // Filter handlers for Closed tab (priority and month only, date handled by helper)
    $('#filter-priority-closed, #filter-month-closed').on('change', function() {
        reloadClosedTable();
    });

    // Update all table references to use progressTable as default
    window.workOrderTable = progressTable; // For backward compatibility
    window.workOrdersTable = progressTable; // For unit verification modal
    window.progressTable = progressTable; // Direct reference
    window.closedTable = closedTable; // Direct reference
    
    // Row click events for both tables
        // Enhanced click prevention for View Only users
        if (!canViewService) {
            console.log('🔒 View Only mode activated for Service - blocking all table interactions');
            
            // Override showWorkOrderDetail function
            window.showWorkOrderDetail = function(id, woNumber) {
                console.log('🚫 Access Denied: showWorkOrderDetail blocked for View Only user');
                safeShowNotification('Access Denied: You do not have permission to view work order details.', 'error');
                return false;
            };
            
            // Prevent all table interactions
            $('#progressWorkOrdersTable, #closedWorkOrdersTable').off('click').on('click', function(e) {
                console.log('🚫 Table click blocked');
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            });
            
            // Prevent clicks on table rows
            $('#progressWorkOrdersTable tbody, #closedWorkOrdersTable tbody').off('click').on('click', 'tr', function(e) {
                console.log('🚫 Row click blocked');
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                safeShowNotification('Access Denied: You do not have permission to view work order details.', 'error');
                return false;
            });
        }
        
        $('#progressWorkOrdersTable tbody, #closedWorkOrdersTable tbody').on('click', 'tr.clickable-row', function(e) {
            if (!canViewService) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                safeShowNotification('Access Denied: You do not have permission to view work order details.', 'error');
                return false;
            }
        
        // Don't trigger if clicking on action buttons
        if ($(e.target).closest('.btn-group-vertical').length > 0) {
            return;
        }
        
        let woId = $(this).data('wo-id');
        let woNumber = $(this).data('wo-number');
        
        if (woId) {
            showWorkOrderDetail(woId, woNumber);
        }
    });

    // Reset form when modal is hidden
    $('#workOrderModal').on('hidden.bs.modal', function() {
        // Reset form inputs
        $('#workOrderForm')[0].reset();
        
        // Reset modal title and action
        $('#workOrderFormTitle').text('Tambah Work Order Baru');
        $('#workOrderForm').attr('action', '<?= base_url('service/work-orders/store') ?>');
        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> Simpan Work Order');
        
        // Reset custom dropdowns
        resetCustomDropdowns();
        
        // Clear form errors
        clearFormErrors();
        
        // Clear hidden work order ID
        $('#work_order_id').val('');
    });
    
    // Function to reset custom dropdowns
    function resetCustomDropdowns() {
        // Reset Unit dropdown
        $('#unitSelectedText').text('-- Pilih Unit --');
        $('#unit_id').val('');
        $('#unitDropdownList').empty();
        
        // Reset Staff dropdowns
        const staffTypes = ['admin', 'foreman', 'mechanic', 'helper'];
        staffTypes.forEach(function(type) {
            $(`#${type}SelectedText`).text(`-- Pilih ${type.charAt(0).toUpperCase() + type.slice(1)} --`);
            $(`#${type}_staff_id`).val('');
            $(`#${type}DropdownList`).empty();
            $(`#${type}Search`).val('');
        });
        
        // Reset subcategory dropdown
        $('#subcategory_id').empty().append('<option value="">-- Pilih Sub Kategori (jika ada) --</option>');
        
        // Reset sparepart table
        $('#sparepartTableBody select[id^="sparepart_"]').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                try {
                    $(this).select2('destroy');
                } catch (e) {
                    // Silent fail
                }
            }
        });
        
        $('#sparepartTableBody').empty();
        sparepartRowCount = 0;
        
        // Add one empty row after a small delay
        setTimeout(function() {
            if ($('#workOrderModal').hasClass('show') && $('#sparepartTableBody tr').length === 0) {
                addSparepartRow();
            }
        }, 250);
        
        // Reset priority display
        $('#priority_display').val('');
        $('#priority_id').val('');
        
        // Clear work order number (will be auto-generated)
        $('#work_order_number').val('');
    }

    // Submit Work Order Form
    $('#workOrderForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let url = $(this).attr('action');
        
        // Clear previous errors
        clearFormErrors();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#btnSubmitWo').prop('disabled', true).text('Menyimpan...');
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#workOrderModal').modal('hide');
                    reloadProgressTable();
                    updateStatistics();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error, 'Status:', status);
                
                try {
                    let response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                        displayFormErrors(response.errors);
                    } else {
                        showAlert('error', response.message || 'Terjadi kesalahan saat menyimpan data');
                    }
                } catch (e) {
                    showAlert('error', 'Terjadi kesalahan saat menyimpan data');
                }
            },
            complete: function() {
                $('#btnSubmitWo').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Work Order');
            }
        });
    });

    // Show Work Order Detail function
    function showWorkOrderDetail(id, woNumber) {
        if (!canViewService) {
            alert('Access Denied: You do not have permission to view work order details.');
            return;
        }
        
        $.ajax({
            url: '<?= base_url('service/work-orders/view') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
            },
            success: function(response) {
                if (response.success) {
                    hideAlert();
                    populateViewModal(response.data);
                    
                    // Debug modal show
                    console.log('🔍 Showing viewWorkOrderModal...');
                    $('#viewWorkOrderModal').modal('show');
                    
                    // Force show modal if needed
                    setTimeout(function() {
                        if (!$('#viewWorkOrderModal').hasClass('show')) {
                            $('#viewWorkOrderModal').addClass('show').css('display', 'block');
                            $('body').addClass('modal-open');
                        }
                    }, 100);
                } else {
                    showAlert('error', response.message || 'Gagal memuat data');
                }
            },
            error: function(xhr, status, error) {
                hideAlert();
                showAlert('error', 'Terjadi kesalahan saat memuat data: ' + error);
            }
        });
    }

    // Status Action Buttons Event Handlers
    
    // Start Work (menggunakan class btn-assign) - Simple confirmation with print button
    $(document).on('click', '.btn-assign', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        let id = $(this).data('id');
        let woNumber = $(this).data('wo-number') || 'WO-' + id;
        
        console.log('🚀 Start Work clicked (btn-assign) for ID:', id, 'WO Number:', woNumber);
        
        // Simple SweetAlert with print button
        Swal.fire({
            title: 'Mulai Pekerjaan?',
            html: `
                <p>Pastikan dokumen Work Order dan Form Verifikasi Unit telah dicetak sebelum memulai pekerjaan.</p>
                <p><strong>CATATAN:</strong> Verifikasi Unit wajib dilakukan dan didokumentasikan untuk menyelesaikan Work Order.</p>
                <div class="mt-3">
                    <button type="button" class="btn btn-primary" onclick="window.open('<?= base_url('service/work-orders/print') ?>/' + ${id}, '_blank')">
                        <i class="fas fa-print me-2"></i>Print Work Order
                    </button>
                </div>
                
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Mulai',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                updateWorkOrderStatusDirect(id, 'IN_PROGRESS', 'Work order dimulai');
            }
        });
    });
    
    // Pause Work - Show dropdown with options
    $(document).on('click', '.btn-pause', function() {
        let id = $(this).data('id');
        let woNumber = $(this).data('wo-number');
        
        Swal.fire({
            title: 'Pilih Jenis Pause',
            text: woNumber ? `Work Order ${woNumber}` : 'Pilih jenis pause untuk work order ini',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Pending',
            cancelButtonText: 'Batal',
            showDenyButton: true,
            denyButtonText: 'Menunggu Sparepart',
            confirmButtonColor: '#ffc107',
            denyButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // User chose "Pending" - status ON_HOLD
                showStatusUpdateModal(id, 'ON_HOLD', 'Pending Work Order', 'Berikan alasan pending');
            } else if (result.isDenied) {
                // User chose "Menunggu Sparepart" - status WAITING_PARTS
                showStatusUpdateModal(id, 'WAITING_PARTS', 'Menunggu Sparepart', 'Berikan detail sparepart yang dibutuhkan');
            }
        });
    });
    
    // Resume Work
    $(document).on('click', '.btn-resume', function() {
        let id = $(this).data('id');
        updateWorkOrderStatus(id, 'IN_PROGRESS', 'Work order dilanjutkan');
    });
    
    // Complete Work - Open Unit Verification Modal
    $(document).on('click', '.btn-complete', function() {
        let id = $(this).data('id');
        let woNumber = $(this).data('wo-number');
        
        // Fallback: get WO number from the row if not in button
        if (!woNumber) {
            let row = $(this).closest('tr');
            woNumber = row.find('td:nth-child(2)').text().trim(); // Work order number is in 2nd column
            console.log('🔄 Fallback WO number from row:', woNumber);
        }
        
        // Open Unit Verification Modal
        $('#unitVerificationModal').modal('show');
        
        // Load unit verification data
        loadUnitVerificationData(id, woNumber);
    });
    
    // Close Work Order
    $(document).on('click', '.btn-close-wo', function() {
        let id = $(this).data('id');
        let woNumber = $(this).data('wo-number');
        
        // Always open sparepart validation modal for close action
        if (typeof window.openSparepartValidationModal === 'function') {
            window.openSparepartValidationModal(id, woNumber);
        } else {
            console.error('❌ Sparepart validation modal function not found');
            showAlert('error', 'Error: Tidak dapat membuka modal validasi sparepart');
        }
    });
    
    // Reopen Work Order
    $(document).on('click', '.btn-reopen', function() {
        let id = $(this).data('id');
        updateWorkOrderStatus(id, 'PENDING', 'Work order dibuka kembali');
    });
    
    // Cancel Work Order
    $(document).on('click', '.btn-cancel', function() {
        let id = $(this).data('id');
        showStatusUpdateModal(id, 'CANCELLED', 'Cancel Work Order', 'Berikan alasan pembatalan');
    });
    
    // Reassign Work Order
    $(document).on('click', '.btn-reassign', function() {
        let id = $(this).data('id');
        showStatusUpdateModal(id, 'ASSIGNED', 'Reassign Work Order', 'Pilih teknisi baru');
    });

    // Function to update work order status with confirmation
    function updateWorkOrderStatus(id, status, message) {
        console.log('🚨 updateWorkOrderStatus called with:', { id, status, message, stack: new Error().stack });
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengubah status work order?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                updateWorkOrderStatusDirect(id, status, message);
            }
        });
    }
    
    // Function to update work order status directly without confirmation
    function updateWorkOrderStatusDirect(id, status, message) {
        console.log('🚨 updateWorkOrderStatusDirect called with:', { id, status, message });
        
        $.ajax({
            url: '<?= base_url('service/work-orders/update-status') ?>',
            type: 'POST',
            data: {
                id: id,
                status: status,
                notes: message
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    reloadProgressTable();
                    updateStatistics();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Gagal mengubah status work order');
            }
        });
    }
    
    // Function to show status update modal with notes
    function showStatusUpdateModal(id, status, title, placeholder) {
        Swal.fire({
            title: title,
            input: 'textarea',
            inputPlaceholder: placeholder,
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value && (status === 'CANCELLED' || status === 'ON_HOLD' || status === 'WAITING_PARTS')) {
                    return 'Catatan wajib diisi untuk status ini'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('service/work-orders/update-status') ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        status: status,
                        notes: result.value || ''
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            reloadProgressTable();
                            updateStatistics();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'Gagal mengubah status work order');
                    }
                });
            }
        });
    }

    // Print from view modal
    $(document).on('click', '.btn-print-from-view', function() {
        let id = $(this).data('id');
        
        if (id) {
            // Open print work order in new window
            const printUrl = '<?= base_url('service/work-orders/print') ?>/' + id;
            window.open(printUrl, '_blank');
        } else {
            console.error('❌ No work order ID found for printing');
            showAlert('error', 'Error: Tidak dapat menemukan ID work order untuk dicetak');
        }
    });

    // Edit from view modal
    $(document).on('click', '.btn-edit-from-view', function() {
        let id = $(this).data('id');
        
        if (!id) {
            console.error('❌ No work order ID found for editing');
            showAlert('error', 'Error: Tidak dapat menemukan ID work order untuk diedit');
            return;
        }
        
        // Close view modal first
        $('#viewWorkOrderModal').modal('hide');
        
        // Load work order data for editing
        $.ajax({
            url: '<?= base_url('service/work-orders/edit') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
            },
            success: function(response) {
                
                if (response.success) {
                    // Wait for view modal to close then open edit modal
                    setTimeout(function() {
                        // Setup modal for editing
                        $('#workOrderFormTitle').html('<i class="fas fa-edit me-2"></i>Edit Work Order');
                        $('#workOrderForm').attr('action', '<?= base_url('work-orders/update') ?>/' + id);
                        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> Update Work Order');
                        $('#work_order_id').val(id);
                        
                        // Open modal first to trigger dropdown loading
                        $('#workOrderModal').modal('show');
                        
                        // Wait for modal to be shown and dropdowns loaded, then populate
                        setTimeout(function() {
                            populateEditForm(response.data);
                        }, 1000); // Give dropdowns time to load
                        
                    }, 300);
                } else {
                    console.error('❌ Edit failed:', response.message);
                    Swal.fire('Error', response.message || 'Gagal memuat data work order', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error loading work order for edit:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMessage = 'Gagal memuat data work order';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        let response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        console.error('Failed to parse error response:', e);
                    }
                }
                
                Swal.fire('Error', errorMessage, 'error');
            }
        });
    });
    
    // Edit from DataTable action buttons
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        
        if (!id) {
            console.error('❌ No work order ID found for editing');
            showAlert('error', 'Error: Tidak dapat menemukan ID work order untuk diedit');
            return;
        }
        
        // Load work order data for editing directly (no view modal to close)
        $.ajax({
            url: '<?= base_url('work-orders/edit') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
            },
            success: function(response) {
                
                if (response.success) {
                    // Setup modal for editing
                    $('#workOrderFormTitle').html('<i class="fas fa-edit me-2"></i>Edit Work Order');
                    $('#workOrderForm').attr('action', '<?= base_url('work-orders/update') ?>/' + id);
                    $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> Update Work Order');
                    $('#work_order_id').val(id);
                    
                    // Open modal first to trigger dropdown loading
                    $('#workOrderModal').modal('show');
                    
                    // Wait for modal to be shown and dropdowns loaded, then populate
                    setTimeout(function() {
                        populateEditForm(response.data);
                    }, 1000); // Give dropdowns time to load
                    
                } else {
                    console.error('❌ Edit failed:', response.message);
                    Swal.fire('Error', response.message || 'Gagal memuat data work order', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error loading work order for edit from table:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMessage = 'Gagal memuat data work order';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        let response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        console.error('Failed to parse error response:', e);
                    }
                }
                
                Swal.fire('Error', errorMessage, 'error');
            }
        });
    });
    
    // Delete from view modal
    $(document).on('click', '.btn-delete-from-view', function(e) {
        e.preventDefault();
        
        let id = $(this).data('id');
        let woNumber = $(this).data('wo-number');
        
        $('#viewWorkOrderModal').modal('hide');
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus Work Order ${woNumber}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('🗑️ Confirmed deletion, sending request...');
                $.ajax({
                    url: '<?= base_url('work-orders/delete') ?>/' + id,
                    type: 'DELETE',
                    beforeSend: function() {
                        console.log('🗑️ Sending delete request to:', '<?= base_url('work-orders/delete') ?>/' + id);
                    },
                    success: function(response) {
                        console.log('✅ Delete response:', response);
                        if (response.success) {
                            showAlert('success', response.message);
                            reloadProgressTable();
                            updateStatistics();
                        } else {
                            console.log('❌ Delete failed:', response.message);
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Delete error:', error);
                        console.error('❌ Delete response:', xhr.responseText);
                        showAlert('error', 'Gagal menghapus work order');
                    }
                });
            } else {
            }
        });
    });

    // Delete from DataTable action buttons
    $(document).on('click', '.btn-delete-wo', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent row click event
        
        let id = $(this).data('id');
        let $row = $(this).closest('tr');
        let woNumber = $row.find('td:nth-child(2)').text(); // Get WO number from table row
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus Work Order ${woNumber}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('work-orders/delete') ?>/' + id,
                    type: 'DELETE',
                    beforeSend: function() {
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            reloadProgressTable();
                            updateStatistics();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Delete error:', error);
                        console.error('❌ Delete response:', xhr.responseText);
                        try {
                            let response = JSON.parse(xhr.responseText);
                            showAlert('error', response.message || 'Gagal menghapus work order');
                        } catch (e) {
                            showAlert('error', 'Gagal menghapus work order');
                        }
                    }
                });
            } else {
            }
        });
    });

    // Category change handler for subcategory
    $('#category_id').on('change', function() {
        let categoryId = $(this).val();
        let subcategorySelect = $('#subcategory_id');
        
        // Clear and reset subcategory dropdown
        subcategorySelect.empty().append('<option value="">-- Pilih Sub Kategori (jika ada) --</option>');
        
        if (categoryId) {
            $.ajax({
                url: '<?= base_url('service/work-orders/get-subcategories') ?>',
                type: 'POST',
                data: { category_id: categoryId },
                success: function(response) {
                    if (response.success && response.data) {
                        $.each(response.data, function(index, subcategory) {
                            subcategorySelect.append(`<option value="${subcategory.id}">${subcategory.subcategory_name}</option>`);
                        });
                        console.log('✅ Subcategories loaded:', response.data.length, 'items');
                        
                        // Trigger Select2 update
                        if (subcategorySelect.hasClass('select2-hidden-accessible')) {
                            subcategorySelect.trigger('change');
                        }
                    } else {
                        console.log('ℹ️ No subcategories found for category:', categoryId);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error loading subcategories:', error);
                }
            });
        } else {
            // Trigger Select2 update for empty state
            if (subcategorySelect.hasClass('select2-hidden-accessible')) {
                subcategorySelect.trigger('change');
            }
        }
    });

    // Helper functions
    function populateEditForm(data) {
        console.log('🔄 Populating edit form with data:', data);
        
        try {
            // Extract work order data from nested structure
            let workOrder = data.workOrder || data;
            console.log('📋 Work Order data:', workOrder);
            
            // Basic form fields
            if (workOrder.id) $('#work_order_id').val(workOrder.id);
            if (workOrder.work_order_number) $('#work_order_number').val(workOrder.work_order_number);
            if (workOrder.order_type) $('#order_type').val(workOrder.order_type).trigger('change');
            if (workOrder.category_id) $('#category_id').val(workOrder.category_id).trigger('change');
            if (workOrder.area) $('#area').val(workOrder.area);
            if (workOrder.complaint_description) $('#complaint_description').val(workOrder.complaint_description);
            
            console.log('✅ Basic fields populated');
            
            // Handle Unit selection with Select2
            if (workOrder.unit_id) {
                console.log('🏢 Setting unit ID:', workOrder.unit_id);
                
                // For Select2, we need to add the option first if it doesn't exist
                let unitSelect = $('#unit_id');
                let unitExists = unitSelect.find(`option[value="${workOrder.unit_id}"]`).length > 0;
                
                if (!unitExists && data.unit) {
                    // Add the unit option from the response data
                    let unitText = data.unit.no_unit || `Unit ${workOrder.unit_id}`;
                    if (data.unit.pelanggan) unitText += ` - ${data.unit.pelanggan}`;
                    if (data.unit.merk_unit || data.unit.model_unit) {
                        unitText += ` (${data.unit.merk_unit || ''} ${data.unit.model_unit || ''}`.trim() + ')';
                    }
                    
                    unitSelect.append(`<option value="${workOrder.unit_id}" selected>${unitText}</option>`);
                    console.log('✅ Unit option added:', unitText);
                } else if (unitExists) {
                    unitSelect.val(workOrder.unit_id);
                    console.log('✅ Unit selected from existing options');
                }
                
                // Trigger Select2 update
                unitSelect.trigger('change');
            }
            
            // Handle Category and Subcategory with Select2
            if (workOrder.category_id) {
                console.log('📂 Setting category ID:', workOrder.category_id);
                $('#category_id').val(workOrder.category_id).trigger('change');
                
                // Load subcategories if category is selected
                if (workOrder.subcategory_id && data.subcategories) {
                    setTimeout(function() {
                        let subcategorySelect = $('#subcategory_id');
                        subcategorySelect.empty().append('<option value="">-- Pilih Sub Kategori (jika ada) --</option>');
                        
                        if (data.subcategories && data.subcategories.length > 0) {
                            data.subcategories.forEach(function(subcategory) {
                                let selected = subcategory.id == workOrder.subcategory_id ? 'selected' : '';
                                subcategorySelect.append(`<option value="${subcategory.id}" ${selected}>${subcategory.subcategory_name}</option>`);
                            });
                            subcategorySelect.trigger('change');
                            console.log('✅ Subcategories populated, selected:', workOrder.subcategory_id);
                        }
                    }, 500); // Allow time for category change to trigger subcategory load
                }
            }
            
            // Handle Mechanic selections with Select2
            if (workOrder.mechanic_1 || workOrder.mechanic_id) {
                let mechanicId = workOrder.mechanic_1 || workOrder.mechanic_id;
                console.log('🔧 Setting mechanic 1 ID:', mechanicId);
                
                let mechanicSelect = $('#mechanic_1');
                let mechanicExists = mechanicSelect.find(`option[value="${mechanicId}"]`).length > 0;
                
                if (!mechanicExists && data.mechanics) {
                    let mechanic = data.mechanics.find(m => m.id == mechanicId);
                    if (mechanic) {
                        let mechanicText = mechanic.staff_name || mechanic.name || `Mechanic ${mechanicId}`;
                        mechanicSelect.append(`<option value="${mechanicId}" selected>${mechanicText}</option>`);
                        console.log('✅ Mechanic 1 option added:', mechanicText);
                    }
                } else if (mechanicExists) {
                    mechanicSelect.val(mechanicId);
                }
                mechanicSelect.trigger('change');
            }
            
            if (workOrder.mechanic_2) {
                console.log('🔧 Setting mechanic 2 ID:', workOrder.mechanic_2);
                
                let mechanicSelect = $('#mechanic_2');
                let mechanicExists = mechanicSelect.find(`option[value="${workOrder.mechanic_2}"]`).length > 0;
                
                if (!mechanicExists && data.mechanics) {
                    let mechanic = data.mechanics.find(m => m.id == workOrder.mechanic_2);
                    if (mechanic) {
                        let mechanicText = mechanic.staff_name || mechanic.name || `Mechanic ${workOrder.mechanic_2}`;
                        mechanicSelect.append(`<option value="${workOrder.mechanic_2}" selected>${mechanicText}</option>`);
                        console.log('✅ Mechanic 2 option added:', mechanicText);
                    }
                } else if (mechanicExists) {
                    mechanicSelect.val(workOrder.mechanic_2);
                }
                mechanicSelect.trigger('change');
            }
            
            // Handle Helper selections with Select2
            if (workOrder.helper_1 || workOrder.helper_id) {
                let helperId = workOrder.helper_1 || workOrder.helper_id;
                console.log('🛠️ Setting helper 1 ID:', helperId);
                
                let helperSelect = $('#helper_1');
                let helperExists = helperSelect.find(`option[value="${helperId}"]`).length > 0;
                
                if (!helperExists && data.helpers) {
                    let helper = data.helpers.find(h => h.id == helperId);
                    if (helper) {
                        let helperText = helper.staff_name || helper.name || `Helper ${helperId}`;
                        helperSelect.append(`<option value="${helperId}" selected>${helperText}</option>`);
                        console.log('✅ Helper 1 option added:', helperText);
                    }
                } else if (helperExists) {
                    helperSelect.val(helperId);
                }
                helperSelect.trigger('change');
            }
            
            if (workOrder.helper_2) {
                console.log('🛠️ Setting helper 2 ID:', workOrder.helper_2);
                
                let helperSelect = $('#helper_2');
                let helperExists = helperSelect.find(`option[value="${workOrder.helper_2}"]`).length > 0;
                
                if (!helperExists && data.helpers) {
                    let helper = data.helpers.find(h => h.id == workOrder.helper_2);
                    if (helper) {
                        let helperText = helper.staff_name || helper.name || `Helper ${workOrder.helper_2}`;
                        helperSelect.append(`<option value="${workOrder.helper_2}" selected>${helperText}</option>`);
                        console.log('✅ Helper 2 option added:', helperText);
                    }
                } else if (helperExists) {
                    helperSelect.val(workOrder.helper_2);
                }
                helperSelect.trigger('change');
            }
            
            // Handle Priority
            if (workOrder.priority_id) {
                console.log('⚠️ Setting priority ID:', workOrder.priority_id);
                $('#priority_id').val(workOrder.priority_id);
            }
            
            // Handle PIC
            if (workOrder.pic) {
                console.log('👤 Setting PIC:', workOrder.pic);
                $('#pic').val(workOrder.pic);
            }
            
            // Handle spareparts if they exist
            console.log('🔧 Checking spareparts data:', data.spareparts);
            if (data.spareparts && data.spareparts.length > 0) {
                console.log('🔧 Populating spareparts:', data.spareparts);
                // Clear existing sparepart rows
                $('#sparepartTableBody').empty();
                sparepartRowCount = 0; // Reset counter
                
                // Add sparepart rows with proper timing
                setTimeout(function() {
                    data.spareparts.forEach(function(sparepart, index) {
                        console.log(`🔧 Adding sparepart row ${index + 1}:`, sparepart);
                        addSparepartRow(sparepart);
                    });
                    
                    console.log('✅ All sparepart rows added, total:', data.spareparts.length);
                }, 200);
            } else {
                console.log('📝 No spareparts data, adding empty row');
                // Clear existing sparepart rows
                $('#sparepartTableBody').empty();
                sparepartRowCount = 0; // Reset counter
                
                // Add one empty row
                setTimeout(function() {
                    addSparepartRow();
                }, 200);
            }
            
            console.log('✅ Edit form populated successfully');
            
        } catch (error) {
            console.error('❌ Error populating edit form:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat mengisi form edit: ' + error.message, 'error');
        }
    }

    function populateViewModal(data) {
        // Debug: Log the data structure to understand what we're receiving
        console.log('Work Order Detail Data:', data);
        console.log('Accessories Data:', data.unit_accessories || data.accessories);
        
        // Update modal header with work order number
        $('#viewWoNumberHeader').text(data.work_order_number || '-');
        
        // Work Order Information
        $('#viewWoNumber').text(data.work_order_number || '-');
        $('#viewWoReportDate').text(data.report_date || '-');
        $('#viewWoType').text(data.order_type || '-');
        $('#viewWoPriority').html(data.priority_badge || '<span class="badge bg-secondary">-</span>');
        $('#viewWoCategory').text(data.category_name || '-');
        $('#viewWoDepartemen').html(data.unit_departemen ? `<span class="badge bg-info">${data.unit_departemen}</span>` : '<span class="badge bg-secondary">-</span>');
        $('#viewWoStatus').html(data.status_badge || '<span class="badge bg-secondary">-</span>');
        $('#viewWoArea').text(data.area || '-');
        $('#viewWoTTR').text(data.time_to_repair ? data.time_to_repair + ' jam' : '-');
        $('#viewWoCompletionDate').text(data.completion_date || 'Belum selesai');
        
        // Unit Details  
        $('#viewUnitNumber').text(data.unit_number || '-');
        $('#viewUnitModel').text((data.unit_brand && data.model_unit) ? data.unit_brand + ' ' + data.model_unit : '-');
        $('#viewUnitType').text(data.unit_type || '-');
        $('#viewUnitDepartemen').html(data.unit_departemen ? `<span class="badge bg-info">${data.unit_departemen}</span>` : '<span class="badge bg-secondary">-</span>');
        $('#viewUnitSerial').text(data.unit_serial || '-');
        $('#viewUnitLocation').text(data.unit_location || '-');
        $('#viewUnitCustomer').text(data.unit_customer || '-');
        $('#viewUnitStatus').html(data.unit_status ? `<span class="badge bg-success">${data.unit_status}</span>` : '<span class="badge bg-secondary">-</span>');
        
        // Additional Unit Details
        $('#viewUnitCapacity').text(data.unit_capacity || '-');
        $('#viewUnitYear').text(data.unit_year || '-');
        $('#viewUnitEngine').text(data.unit_engine || '-');
        $('#viewUnitEngineSN').text(data.unit_engine_sn || '-');
        $('#viewUnitMast').text(data.unit_mast || '-');
        $('#viewUnitMastSN').text(data.unit_mast_sn || '-');
        $('#viewUnitMastHeight').text(data.unit_mast_height || '-');
        
        // Handle Unit Components
        populateUnitComponents(data);
        
        // Handle Unit Accessories
        populateUnitAccessories(data.unit_accessories || data.accessories || []);
        
        // Handle Sparepart Brought
        populateSparepartBrought(data.spareparts || []);
        
        // Staff Assignment
        $('#viewWoAdmin').text(data.admin_staff_name || '-');
        $('#viewWoForeman').text(data.foreman_staff_name || '-');
        $('#viewWoMechanic').text(data.mechanic_staff_name || '-');
        $('#viewWoHelper').text(data.helper_staff_name || '-');
        
        // Descriptions and Details  
        $('#viewWoComplaint').html(data.complaint_description ? 
            `<div class="text-dark">${data.complaint_description}</div>` : 
            '<div class="text-muted fst-italic">Tidak ada deskripsi keluhan</div>');
            
        $('#viewWoRepair').html(data.repair_description ? 
            `<div class="text-dark">${data.repair_description}</div>` : 
            '<div class="text-muted fst-italic">Belum ada perbaikan</div>');
            
        $('#viewWoSparepart').html(data.sparepart_used ? 
            `<div class="text-dark">${data.sparepart_used}</div>` : 
            '<div class="text-muted fst-italic">Tidak ada sparepart yang digunakan</div>');
            
        $('#viewWoNotes').html(data.notes ? 
            `<div class="text-dark">${data.notes}</div>` : 
            '<div class="text-muted fst-italic">Tidak ada catatan</div>');
        
        // Set data attributes for buttons
        $('.btn-print-from-view').data('id', data.id);
        $('.btn-edit-from-view').data('id', data.id);
        $('.btn-delete-from-view').data('id', data.id).data('wo-number', data.work_order_number);
    }

    function populateUnitComponents(data) {
        // Always populate attachments
        populateUnitAttachments(data.unit_attachments || []);
        
        // Show/hide unit components section
        let hasComponents = false;
        
        // Check if we have any attachments
        if (data.unit_attachments && data.unit_attachments.length > 0) {
            hasComponents = true;
        }
        
        // Handle ELECTRIC department components
        if (data.unit_departemen === 'ELECTRIC') {
            // Show battery section and populate
            $('#batteryLabel, #batteryValue').show();
            populateUnitBatteries(data.unit_batteries || []);
            
            // Show charger section and populate
            $('#chargerLabel, #chargerValue').show();
            populateUnitChargers(data.unit_chargers || []);
            
            // Check if we have electric components
            if ((data.unit_batteries && data.unit_batteries.length > 0) || 
                (data.unit_chargers && data.unit_chargers.length > 0)) {
                hasComponents = true;
            }
        } else {
            // Hide electric components for non-electric units
            $('#batteryLabel, #batteryValue').hide();
            $('#chargerLabel, #chargerValue').hide();
        }
        
        // Show/hide the entire components section
        if (hasComponents) {
            $('#unitComponentsSection').show();
        } else {
            $('#unitComponentsSection').hide();
        }
    }
    
    function populateUnitAttachments(attachments) {
        const container = $('#viewUnitAttachmentList');
        
        if (attachments && attachments.length > 0) {
            let textList = [];
            attachments.forEach(function(attachment, index) {
                let text = `${index + 1}. ${attachment.tipe || 'Attachment'} - ${attachment.merk || 'Unknown'}`;
                if (attachment.model) text += ` ${attachment.model}`;
                if (attachment.sn_attachment) text += ` (SN: ${attachment.sn_attachment})`;
                textList.push(text);
            });
            container.text(textList.join(', '));
        } else {
            container.html('<em class="text-muted">Tidak ada attachment</em>');
        }
    }

    function populateUnitBatteries(batteries) {
        const container = $('#viewUnitBatteryList');
        
        if (batteries && batteries.length > 0) {
            let textList = [];
            batteries.forEach(function(battery, index) {
                let text = `${index + 1}. ${battery.tipe_baterai || 'Battery'} - ${battery.merk_baterai || 'Unknown'}`;
                if (battery.jenis_baterai) text += ` ${battery.jenis_baterai}`;
                if (battery.sn_baterai) text += ` (SN: ${battery.sn_baterai})`;
                textList.push(text);
            });
            container.text(textList.join(', '));
        } else {
            container.html('<em class="text-muted">Tidak ada battery</em>');
        }
    }

    function populateUnitChargers(chargers) {
        const container = $('#viewUnitChargerList');
        
        if (chargers && chargers.length > 0) {
            let textList = [];
            chargers.forEach(function(charger, index) {
                let text = `${index + 1}. ${charger.tipe_charger || 'Charger'} - ${charger.merk_charger || 'Unknown'}`;
                if (charger.sn_charger) text += ` (SN: ${charger.sn_charger})`;
                textList.push(text);
            });
            container.text(textList.join(', '));
        } else {
            container.html('<em class="text-muted">Tidak ada charger</em>');
        }
    }

    function loadSubcategories(categoryId, selectedSubcategoryId = null) {
        $.ajax({
            url: '<?= base_url('service/work-orders/get-subcategories') ?>',
            type: 'POST',
            data: { category_id: categoryId },
            success: function(response) {
                if (response.success) {
                    let subcategorySelect = $('#subcategory_id');
                    subcategorySelect.empty().append('<option value="">Pilih Sub Kategori</option>');
                    
                    $.each(response.data, function(index, subcategory) {
                        let selected = selectedSubcategoryId == subcategory.id ? 'selected' : '';
                        subcategorySelect.append(`<option value="${subcategory.id}" ${selected}>${subcategory.subcategory_name}</option>`);
                    });
                }
            }
        });
    }
    
    function populateUnitAccessories(accessories) {
        const container = $('#viewUnitAccessoriesList');
        
        console.log('Raw accessories data:', accessories, typeof accessories);
        
        // Handle different data types
        let accessoriesArray = [];
        
        if (typeof accessories === 'string') {
            try {
                accessoriesArray = JSON.parse(accessories);
            } catch (e) {
                // If it's a comma-separated string, split it
                accessoriesArray = accessories.split(',').map(item => item.trim());
            }
        } else if (Array.isArray(accessories)) {
            accessoriesArray = accessories;
        } else {
            console.log('Accessories data is neither array nor string:', accessories);
            container.html('<div class="text-muted fst-italic">Tidak ada aksesoris</div>');
            $('#unitAccessoriesSection').hide();
            return;
        }
        
        console.log('Processed accessories array:', accessoriesArray);
        
        if (accessoriesArray && accessoriesArray.length > 0) {
            // Create simple comma-separated list like complaint format
            let accessoryNames = [];
            
            accessoriesArray.forEach(function(accessory) {
                // Handle if accessory is a string (just the name)
                let accessoryName = '';
                
                if (typeof accessory === 'string') {
                    accessoryName = accessory.trim();
                } else if (typeof accessory === 'object') {
                    accessoryName = accessory.accessory_name || accessory.name || 'Unknown Accessory';
                }
                
                if (accessoryName) {
                    accessoryNames.push(accessoryName);
                }
            });
            
            // Display as simple text like complaint format
            if (accessoryNames.length > 0) {
                container.html(`<div class="text-dark">${accessoryNames.join(', ')}</div>`);
                $('#unitAccessoriesSection').show();
            } else {
                container.html('<div class="text-muted fst-italic">Tidak ada aksesoris</div>');
                $('#unitAccessoriesSection').hide();
            }
        } else {
            container.html('<div class="text-muted fst-italic">Tidak ada aksesoris</div>');
            $('#unitAccessoriesSection').hide();
        }
    }

    function populateSparepartBrought(spareparts) {
        const container = $('#viewSparepartBroughtList');
        const section = $('#sparepartBroughtSection');
        
        console.log('Raw spareparts data:', spareparts, typeof spareparts);
        
        if (spareparts && spareparts.length > 0) {
            let html = '';
            spareparts.forEach(function(sparepart, index) {
                const qtyWithUnit = (sparepart.qty || '') + ' ' + (sparepart.satuan || 'pcs');
                html += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${sparepart.name || sparepart.desc_sparepart || '-'}</td>
                        <td class="font-monospace">${sparepart.code || sparepart.kode || '-'}</td>
                        <td class="text-center">${qtyWithUnit}</td>
                        <td>${sparepart.notes || '-'}</td>
                    </tr>
                `;
            });
            container.html(html);
            section.show();
        } else {
            container.html('<tr><td colspan="5" class="text-center text-muted">Tidak ada sparepart yang dibawa</td></tr>');
            section.hide();
        }
    }

    function clearFormErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }

    function displayFormErrors(errors) {
        $.each(errors, function(field, message) {
            let input = $(`#${field}`);
            input.addClass('is-invalid');
            input.after(`<div class="invalid-feedback">${message}</div>`);
        });
    }

    function showAlert(type, message) {
        // Use OptimaPro notification system if available
        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
            let toastType = type === 'error' ? 'danger' : type;
            OptimaPro.showNotification(message, toastType);
        } else if (typeof showNotification === 'function') {
            // Fallback to global notification system
            let toastType = type === 'success' ? 'success' : 
                           type === 'error' ? 'danger' : 'info';
            showNotification(message, toastType);
        } else {
            // Fallback to local alert system
            let alertClass = type === 'success' ? 'alert-success' : 
                            type === 'error' ? 'alert-danger' : 'alert-info';
            
            let alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert" id="mainAlert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            $('#alertContainer').html(alertHtml);
            
            if (type === 'success') {
                setTimeout(hideAlert, 3000);
            }
        }
    }

    function hideAlert() {
        $('#mainAlert').alert('close');
    }

    function updateStatistics() {
        console.log('📊 Updating statistics...');
        $.ajax({
            url: '<?= base_url('service/work-orders/stats') ?>',
            type: 'GET',
            success: function(response) {
                console.log('📊 Statistics response:', response);
                if (response.status) {  // Backend menggunakan 'status' bukan 'success'
                    $('#stat-total-work-orders').text(response.data.total_work_orders || 0);
                    $('#stat-open').text(response.data.open_work_orders || 0);
                    $('#stat-in-progress').text(response.data.in_progress_work_orders || 0);
                    $('#stat-completed').text(response.data.completed_work_orders || 0);
                    console.log('📊 Statistics updated successfully');
                } else {
                    console.log('❌ Failed to update statistics:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('❌ Error updating statistics:', error);
                console.log('❌ XHR:', xhr.responseText);
                // Don't retry - just skip statistics update
            }
        });
    }

    // Auto-refresh statistics every 2 minutes (120 seconds)
    // Store interval ID so we can clear it if needed
    let statisticsInterval = null;
    
    function startStatisticsInterval() {
        // Clear existing interval if any
        if (statisticsInterval) {
            clearInterval(statisticsInterval);
        }
        // Start new interval - update every 2 minutes
        statisticsInterval = setInterval(function() {
            // Only update if no modals are open
            if (!$('.modal.show').length) {
                updateStatistics();
            }
        }, 120000); // 2 minutes = 120000ms
    }
    
    // Start the interval
    startStatisticsInterval();
    
    // Pause statistics updates when modals are open
    $(document).on('shown.bs.modal', '.modal', function() {
        if (statisticsInterval) {
            clearInterval(statisticsInterval);
            statisticsInterval = null;
        }
    });
    
    // Resume statistics updates when modals are closed
    $(document).on('hidden.bs.modal', '.modal', function() {
        // Only resume if no other modals are open
        if (!$('.modal.show').length && !statisticsInterval) {
            startStatisticsInterval();
        }
    });

    // Print Work Order
    $(document).on('click', '.btn-print', function() {
        let id = $(this).data('id');
        window.open('<?= base_url('work-orders/print') ?>/' + id, '_blank');
    });

    // Export functionality
    $('#exportBtn').on('click', function() {
        window.location.href = '<?= base_url('work-orders/export') ?>';
    });

    // Add Work Order button
    $('#btn-add-wo').on('click', function() {
        console.log('🆕 Opening new work order modal');
        // Auto generate WO number when opening modal
        generateWorkOrderNumber();
        
        // Ensure form is set for create mode
        $('#workOrderFormTitle').html('<i class="fas fa-plus-circle me-2"></i>Tambah Work Order Baru');
        $('#workOrderForm').attr('action', '<?= base_url('service/work-orders/store') ?>');
        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> Simpan Work Order');
        $('#work_order_id').val('');
        
        // Initialize Select2 immediately before showing modal
        setTimeout(function() {
            initializeSelect2();
        }, 100);
        
        $('#workOrderModal').modal('show');
    });

    // Auto generate Work Order number
    function generateWorkOrderNumber() {
        $.ajax({
            url: '<?= base_url('service/work-orders/generate-number') ?>',
            type: 'GET',
            success: function(response) {
                console.log('🔢 WO number generated:', response);
                if (response.success) {
                    $('#work_order_number').val(response.work_order_number);
                } else {
                    console.log('❌ Failed to generate WO number:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('❌ Error generating work order number:', error);
            }
        });
    }

    // Load Unit Verification Data - Now handled by unit_verification.php
    function loadUnitVerificationData(workOrderId, woNumber) {
        // Call the function from unit_verification.php
        if (typeof window.loadUnitVerificationData === 'function') {
            window.loadUnitVerificationData(workOrderId, woNumber);
        } else {
            console.error('❌ loadUnitVerificationData function not found');
        }
    }

    // Unit search functionality
    let unitSearchTimeout;
    $('#unit_search').on('input', function() {
        let query = $(this).val().trim();
        clearTimeout(unitSearchTimeout);
        
        if (query.length >= 2) {
            unitSearchTimeout = setTimeout(function() {
                searchUnits(query);
            }, 300);
        } else {
            $('#unit_search_results').hide();
        }
    });

    function searchUnits(query) {
        $.ajax({
            url: '<?= base_url('service/work-orders/search-units') ?>',
            type: 'POST',
            data: { query: query },
            beforeSend: function() {
                $('#unit_search_results').html('<div class="list-group-item"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>').show();
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(unit) {
                        html += `<div class="list-group-item list-group-item-action unit-result" 
                                    data-unit-id="${unit.id_inventory_unit}" 
                                    data-unit-info="${unit.no_unit} - ${unit.pelanggan} (${unit.merk_unit} ${unit.model_unit})">
                                    <div class="fw-bold">${unit.no_unit}</div>
                                    <div class="text-muted small">${unit.pelanggan} | ${unit.merk_unit || 'N/A'} ${unit.model_unit || ''}</div>
                                    <div class="text-primary small">SN: ${unit.serial_number || 'N/A'} | Lokasi: ${unit.lokasi}</div>
                                </div>`;
                    });
                    $('#unit_search_results').html(html).show();
                } else {
                    $('#unit_search_results').html('<div class="list-group-item text-muted">Tidak ada unit yang ditemukan</div>').show();
                }
            },
            error: function() {
                $('#unit_search_results').html('<div class="list-group-item text-danger">Error mencari unit</div>').show();
            }
        });
    }

    // Unit selection
    $(document).on('click', '.unit-result', function() {
        let unitId = $(this).data('unit-id');
        let unitInfo = $(this).data('unit-info');
        
        $('#unit_id').val(unitId);
        $('#unit_search').val(unitInfo);
        $('#unit_search_results').hide();
    });

    // Clear unit search
    $('#btn_clear_unit').on('click', function() {
        $('#unit_search').val('');
        $('#unit_id').val('');
        $('#unit_search_results').hide();
    });

    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#unit_search, #unit_search_results').length) {
            $('#unit_search_results').hide();
        }
        // Also hide staff search results
        $('.list-group[id$="_search_results"]').hide();
    });

    // Category change handler for subcategory and auto priority
    $('#category_id').on('change', function() {
        let categoryId = $(this).val();
        let selectedOption = $(this).find('option:selected');
        let defaultPriority = selectedOption.data('priority');
        
        // Load subcategories
        loadSubcategories(categoryId);
        
        // Set auto priority based on category
        if (defaultPriority) {
            setPriority(defaultPriority);
        }
    });

    // Subcategory change handler for auto priority
    $('#subcategory_id').on('change', function() {
        let subcategoryId = $(this).val();
        if (subcategoryId) {
            // Get priority for subcategory
            $.ajax({
                url: '<?= base_url('service/work-orders/get-subcategory-priority') ?>',
                type: 'POST',
                data: { subcategory_id: subcategoryId },
                success: function(response) {
                    if (response.success && response.priority_id) {
                        setPriority(response.priority_id);
                    }
                }
            });
        }
    });

    function setPriority(priorityId) {
        // Find priority name and set display
        $.ajax({
            url: '<?= base_url('service/work-orders/get-priority') ?>',
            type: 'POST',
            data: { priority_id: priorityId },
            success: function(response) {
                if (response.success) {
                    $('#priority_id').val(priorityId);
                    $('#priority_display').val(response.priority_name);
                }
            }
        });
    }

    // Staff search functionality
    let staffSearchTimeouts = {};
    $('.staff-search').on('input', function() {
        let $this = $(this);
        let query = $this.val().trim();
        let staffType = $this.data('staff-type');
        let resultDiv = $this.closest('.input-group').next().next();
        
        clearTimeout(staffSearchTimeouts[staffType]);
        
        if (query.length >= 2) {
            staffSearchTimeouts[staffType] = setTimeout(function() {
                searchStaff(query, staffType, resultDiv);
            }, 300);
        } else {
            resultDiv.hide();
        }
    });

    function searchStaff(query, staffType, resultDiv) {
        $.ajax({
            url: '<?= base_url('work-orders/search-staff') ?>',
            type: 'POST',
            data: { query: query, staff_type: staffType },
            beforeSend: function() {
                resultDiv.html('<div class="list-group-item"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>').show();
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(staff) {
                        html += `<div class="list-group-item list-group-item-action staff-result" 
                                    data-staff-id="${staff.id}" 
                                    data-staff-name="${staff.staff_name}"
                                    data-staff-type="${staffType.toLowerCase()}">
                                    <div class="fw-bold">${staff.staff_name}</div>
                                    <div class="text-muted small">${staff.position || staffType} | ${staff.department || 'N/A'}</div>
                                </div>`;
                    });
                    resultDiv.html(html).show();
                } else {
                    resultDiv.html('<div class="list-group-item text-muted">Tidak ada staff yang ditemukan</div>').show();
                }
            },
            error: function() {
                resultDiv.html('<div class="list-group-item text-danger">Error mencari staff</div>').show();
            }
        });
    }

    // Staff selection
    $(document).on('click', '.staff-result', function() {
        let staffId = $(this).data('staff-id');
        let staffName = $(this).data('staff-name');
        let staffType = $(this).data('staff-type');
        
        $(`#${staffType}_staff_id`).val(staffId);
        $(`#${staffType}_staff_search`).val(staffName);
        $(this).parent().hide();
    });

    // Clear staff search
    $('.btn-clear-staff').on('click', function() {
        let target = $(this).data('target');
        $(`#${target}_staff_search`).val('');
        $(`#${target}_staff_id`).val('');
        $(`#${target}_search_results`).hide();
    });


    // Load initial data when modal opens
    $('#workOrderModal').on('shown.bs.modal', function() {
        console.log('📋 Modal shown, loading data...');
        
        // Load unit dropdown first - it will handle its own Select2 initialization
        // DO NOT destroy unit_id here - let loadUnitsDropdown() handle it completely
        loadUnitsDropdown();
        loadMechanicHelperDropdowns();
        
        // Initialize Select2 for other dropdowns (NOT unit_id and NOT sparepart - they handle themselves)
        // Use longer delay to ensure unit and sparepart dropdowns are initialized first
        setTimeout(function() {
            console.log('🔄 Initializing Select2 for other dropdowns (excluding unit_id and sparepart)');
            initializeSelect2();
        }, 800); // Increased delay to ensure unit and sparepart dropdowns are initialized first
        
        // Add initial sparepart row if not exists - with proper timing
        // Wait for sparepartsData to be available and ensure it's loaded
        setTimeout(function() {
            if ($('#sparepartTableBody tr').length === 0) {
                console.log('🔧 Adding initial sparepart row');
                console.log('📦 SparepartsData available:', window.sparepartsData ? window.sparepartsData.length : 0, 'items');
                
                // Ensure sparepartsData is available
                if (!window.sparepartsData || !Array.isArray(window.sparepartsData) || window.sparepartsData.length === 0) {
                    console.warn('⚠️ SparepartsData not available yet, waiting...');
                    // Retry after a bit more delay
                    setTimeout(function() {
                        if ($('#sparepartTableBody tr').length === 0) {
                            console.log('🔧 Retrying to add initial sparepart row');
                            addSparepartRow();
                        }
                    }, 200);
                } else {
                    addSparepartRow();
                }
            }
        }, 400);
    });

    // Fix Select2 modal issues
    $(document).ready(function() {
        // Prevent Select2 from interfering with modal scroll
        $(document).on('select2:open', function(e) {
            // Ensure modal remains scrollable
            $('#workOrderModal .modal-body').css('overflow-y', 'auto');
            
            // Prevent focus jumping to close button
            e.preventDefault();
            e.stopPropagation();
            
            // Set proper z-index for dropdown
            $('.select2-dropdown').css('z-index', 10060);
        });
        
        // Restore modal scroll when dropdown closes
        $(document).on('select2:close', function(e) {
            $('#workOrderModal .modal-body').css('overflow-y', 'auto');
        });
        
        // Prevent modal from losing scroll when Select2 is clicked
        $(document).on('click', '.select2-container', function(e) {
            e.stopPropagation();
        });
        
        // Ensure dropdown opens properly on click
        $(document).on('click', '.select2-selection', function(e) {
            e.stopPropagation();
            const $container = $(this).closest('.select2-container');
            const $select = $container.prev('select');
            
            if (!$container.hasClass('select2-container--open')) {
                $select.select2('open');
            }
        });
        
        // Clean up Select2 when modal is hidden
        $('#workOrderModal').on('hidden.bs.modal', function() {
            // Safely destroy Select2 instances
            const selectorsToDestroy = ['#unit_id', '#category_id', '#subcategory_id', '#order_type', '#mechanic_1', '#mechanic_2', '#helper_1', '#helper_2', '#spareparts'];
            
            selectorsToDestroy.forEach(function(selector) {
                const $element = $(selector);
                if ($element.length && $element.hasClass('select2-hidden-accessible')) {
                    try {
                        $element.select2('destroy');
                    } catch (e) {
                        console.warn('Error destroying Select2 for ' + selector + ':', e);
                    }
                }
            });
            
            // Remove any orphaned Select2 elements
            $('.select2-container').remove();
            $('.select2-dropdown').remove();
            
            // Reset form
            $('#workOrderForm')[0].reset();
            $('#work_order_id').val('');
            
            // Reset modal title
            $('#workOrderFormTitle').html('<i class="fas fa-plus-circle me-2"></i>Tambah Work Order Baru');
            
            // Ensure modal body scroll is restored
            $('#workOrderModal .modal-body').css('overflow-y', 'auto');
        });
    });

    // Initialize Select2 for searchable dropdowns - Clean OPTIMA Theme
    function initializeSelect2() {
        
        // Check if Select2 is available
        if (typeof $.fn.select2 === 'undefined') {
            console.error('❌ Select2 library not loaded!');
            return;
        }
        
        console.log('✅ Select2 library is available');
        
        // Safely destroy existing instances - EXCLUDE unit_id (handled separately in loadUnitsDropdown)
        // unit_id should NEVER be destroyed here to prevent duplicate initialization
        const selectorsToDestroy = ['#category_id', '#subcategory_id', '#order_type', '#mechanic_1', '#mechanic_2', '#helper_1', '#helper_2', '#spareparts'];
        
        selectorsToDestroy.forEach(function(selector) {
            const $element = $(selector);
            if ($element.length && $element.hasClass('select2-hidden-accessible')) {
                try {
                    $element.select2('destroy');
                } catch (e) {
                    console.warn('Error destroying Select2 for ' + selector + ':', e);
                }
            }
        });
        
        // NEVER destroy unit_id here - it's managed by loadUnitsDropdown() with proper search config
        
        // DO NOT remove Select2 containers - let them be managed by their own functions
        // Removing containers can break unit_id and sparepart dropdowns
        
        // Common configuration for modal compatibility
        const modalConfig = {
            allowClear: true,
            width: '100%',
            dropdownParent: $('#workOrderModal'),
            escapeMarkup: function(markup) { return markup; },
            theme: 'default'
        };

        // Searchable dropdowns configuration - Clean appearance
        const searchableConfig = {
                ...modalConfig,
            minimumInputLength: 0,
            allowClear: true,
            placeholder: function() {
                return $(this).data('placeholder') || '-- Pilih --';
            },
            language: {
                noResults: function() {
                    return "Tidak ada hasil ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        };

        // Initialize searchable dropdowns (Unit and Sparepart only)
        const searchableSelectors = [
            { id: '#unit_id', placeholder: '-- Pilih Unit --', searchable: true }
        ];

        // Initialize regular dropdowns (non-searchable) - Clean appearance
        const regularSelectors = [
            { id: '#category_id', placeholder: '-- Pilih Kategori --' },
            { id: '#subcategory_id', placeholder: '-- Pilih Sub Kategori --' },
            { id: '#order_type', placeholder: '-- Pilih Tipe Order --' },
            { id: '#mechanic_1', placeholder: '-- Pilih Mekanik 1 --' },
            { id: '#mechanic_2', placeholder: '-- Pilih Mekanik 2 (Opsional) --' },
            { id: '#helper_1', placeholder: '-- Pilih Helper 1 --' },
            { id: '#helper_2', placeholder: '-- Pilih Helper 2 (Opsional) --' }
        ];

        // Initialize searchable dropdowns - Only for fields that really need search
        searchableSelectors.forEach(function(config) {
            // CRITICAL: unit_id is handled separately in loadUnitsDropdown() - NEVER touch it here
            if (config.id === '#unit_id') {
                console.log('⏭️ Skipping unit_id completely - managed by loadUnitsDropdown()');
                return; // Skip immediately, don't even check the element
            }
            
            const $element = $(config.id);
            if ($element.length) {
                // Destroy existing Select2 if any (only for non-unit_id elements)
                if ($element.hasClass('select2-hidden-accessible')) {
                    try {
                        $element.select2('destroy');
                    } catch (e) {
                        // Ignore destroy errors
                    }
                }
                
                // Only initialize if element has options (data loaded)
                if ($element.find('option').length > 1) {
                    $element.select2({
                        ...searchableConfig,
                        minimumResultsForSearch: 5,
                        placeholder: config.placeholder,
                        dropdownParent: $('#workOrderModal')
                    });
                }
            }
        });

        // Initialize regular dropdowns - Clean Bootstrap-like appearance
        regularSelectors.forEach(function(config) {
            const $element = $(config.id);
            console.log('🔍 Checking element:', config.id, 'Found:', $element.length, 'Has Select2:', $element.hasClass('select2-hidden-accessible'));
            if ($element.length && !$element.hasClass('select2-hidden-accessible')) {
                console.log('✅ Initializing Select2 for:', config.id);
                try {
                    $element.select2({
                ...modalConfig,
                        placeholder: config.placeholder,
                        minimumResultsForSearch: Infinity, // Disable search for clean appearance
                        allowClear: false,
                        width: '100%'
                    });
                    console.log('✅ Successfully initialized Select2 for:', config.id);
                } catch (e) {
                    console.error('❌ Error initializing Select2 for:', config.id, e);
                }
            }
        });

        // Handle spareparts separately for multiple selection with search
        if ($('#spareparts').length && !$('#spareparts').hasClass('select2-hidden-accessible')) {
            $('#spareparts').select2({
                ...searchableConfig,
                placeholder: '-- Pilih Sparepart --',
                multiple: true
            });
        }
        
        // We now initialize Select2 for sparepart dropdowns with proper styling
        
    }

    // Handle unit change to auto-fill area
    $(document).on('change', '#unit_id', function() {
        const unitId = $(this).val();
        if (unitId) {
            // Find unit data from loaded units
            const unit = window.allUnits.find(u => u.id == unitId);
            if (unit && unit.area_name) {
                $('#area').val(unit.area_name);
                $('#area_id').val(unit.area_id);
                
                // Load area staff
                loadAreaStaff(unit.area_id);
            }
        } else {
            // Clear area and staff fields if no unit selected
            $('#area').val('');
            $('#area_id').val('');
            $('#admin').val('').trigger('change');
            $('#foreman').val('').trigger('change');
            $('#mechanic_1, #mechanic_2').val('').trigger('change');
            $('#helper_1, #helper_2').val('').trigger('change');
        }
    });

    // Global variables to store data - make them globally accessible
    window.allUnits = [];
    window.allStaff = {
        admin: [],
        foreman: [],
        mechanic: [],
        helper: []
    };

    // Units Dropdown Management
    function loadUnitsDropdown() {
        console.log('🔄 Loading units dropdown...');
        const unitSelect = $('#unit_id');
        
        // Show loading state
        unitSelect.empty().append('<option value="">Memuat data unit...</option>');
        
        $.ajax({
            url: '<?= base_url('service/work-orders/units-dropdown') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('📦 Units response:', response);
                
                if (response.success && response.data) {
                    unitSelect.empty().append('<option value="">-- Pilih Unit --</option>');
                    
                    if (response.data.length > 0) {
                        response.data.forEach(function(unit) {
                            const pelanggan = unit.pelanggan || 'N/A';
                            const jenis = unit.jenis || 'N/A';
                            const kapasitas = unit.kapasitas || 'N/A';
                            const displayText = `${unit.no_unit} - ${pelanggan} (${jenis} - ${kapasitas})`;
                            unitSelect.append(`<option value="${unit.id}">${displayText}</option>`);
                        });
                        
                        // Store units globally for area auto-fill
                        window.allUnits = response.data;
                        console.log('✅ Units loaded successfully:', response.data.length, 'units');
                    } else {
                        unitSelect.append('<option value="">Tidak ada unit tersedia</option>');
                        console.warn('⚠️ No units found in response');
                    }
                    
                    // Initialize Select2 with search - SIMPLE & DIRECT
                    setTimeout(function() {
                        try {
                            // Always initialize (we already destroyed above if needed)
                            unitSelect.select2({
                                placeholder: '-- Pilih Unit --',
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#workOrderModal'),
                                minimumInputLength: 0, // Enable search immediately
                                minimumResultsForSearch: 0, // Always show search box
                                language: {
                                    noResults: function() { return "Tidak ada hasil ditemukan"; },
                                    searching: function() { return "Mencari..."; }
                                }
                            });
                            console.log('✅ Select2 initialized for unit dropdown with search,', response.data.length, 'options');
                        } catch (e) {
                            console.error('❌ Error initializing Select2:', e);
                        }
                    }, 150);
                } else {
                    unitSelect.empty().append('<option value="">Error: ' + (response.message || 'Gagal memuat data') + '</option>');
                    console.error('❌ Error loading units:', response.message || 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error loading units:', error);
                console.error('❌ Status:', status);
                console.error('❌ Response:', xhr.responseText);
                unitSelect.empty().append('<option value="">Error memuat data unit</option>');
            }
        });
    }

    function displayUnits(units) {
        let unitList = $('#unitDropdownList');
        unitList.empty();
        
        units.forEach(function(unit) {
            let displayName = unit.no_unit + ' - ' + (unit.pelanggan || 'Unknown') + ' (' + (unit.merk_unit + ' ' + unit.model_unit || unit.unit_type || 'Unknown') + ')';
            unitList.append(`
                <a class="dropdown-item unit-item" href="#" data-unit-id="${unit.id}" data-unit='${JSON.stringify(unit)}'>
                    ${displayName}
                </a>
            `);
        });
    }

    // Unit selection handler
    $(document).on('click', '.unit-item', function(e) {
        e.preventDefault();
        let unitId = $(this).data('unit-id');
        let unitData = $(this).data('unit');
        let displayName = $(this).text();
        
        $('#unitSelectedText').text(displayName);
        $('#unit_id').val(unitId);
    });

    // Unit filter function - make it globally accessible
    window.filterUnits = function() {
        let searchTerm = $('#unitSearch').val().toLowerCase();
        let filteredUnits = window.allUnits.filter(unit => {
            let displayName = (unit.no_unit + ' ' + (unit.pelanggan || '') + ' ' + (unit.merk_unit || '') + ' ' + (unit.model_unit || '') + ' ' + (unit.unit_type || '')).toLowerCase();
            return displayName.includes(searchTerm);
        });
        displayUnits(filteredUnits);
    }

    // Staff Dropdown Management
    function loadStaffDropdown(staffRole, targetId) {
        
        $.ajax({
            url: '<?= base_url('service/work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: { staff_role: staffRole },
            success: function(response) {
                
                if (response.success && response.data) {
                    const staffSelect = $('#' + targetId);
                    
                    // Clear existing options and add placeholder
                    let placeholderText = staffRole === 'MECHANIC' ? 
                        (targetId === 'mechanic_1' ? '-- Pilih Mekanik 1 --' : '-- Pilih Mekanik 2 (Opsional) --') :
                        (targetId === 'helper_1' ? '-- Pilih Helper 1 --' : '-- Pilih Helper 2 (Opsional) --');
                    
                    staffSelect.empty().append(`<option value="">${placeholderText}</option>`);
                    
                    // Add staff options
                    response.data.forEach(function(staff) {
                        let staffName = staff.staff_name || staff.name || 'Unknown Staff';
                        let staffCode = staff.staff_code || staff.employee_code || '';
                        let optionText = staffCode ? `${staffName} (${staffCode})` : staffName;
                        
                        staffSelect.append(`<option value="${staff.id}">${optionText}</option>`);
                    });
                    
                    // Re-initialize Select2 if not already initialized
                    if (!staffSelect.hasClass('select2-hidden-accessible')) {
                        staffSelect.select2({
                            placeholder: placeholderText,
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#workOrderModal'),
                            minimumInputLength: 0,
                            language: {
                                noResults: function() { return "Tidak ada hasil ditemukan"; },
                                searching: function() { return "Mencari..."; }
                            }
                        });
                    } else {
                        // Just trigger change to update Select2
                        staffSelect.trigger('change');
                    }
                    
                    console.log(`✅ ${staffRole} staff loaded successfully:`, response.data.length, 'items');
                } else {
                    console.error(`❌ Error loading ${staffRole} staff:`, response.message || 'No data received');
                    
                    // Still add placeholder even if no data
                    const staffSelect = $('#' + targetId);
                    let placeholderText = staffRole === 'MECHANIC' ? 
                        (targetId === 'mechanic_1' ? '-- Pilih Mekanik 1 --' : '-- Pilih Mekanik 2 (Opsional) --') :
                        (targetId === 'helper_1' ? '-- Pilih Helper 1 --' : '-- Pilih Helper 2 (Opsional) --');
                    
                    staffSelect.empty().append(`<option value="">${placeholderText}</option>`);
                }
            },
            error: function(xhr, status, error) {
                console.error(`❌ AJAX Error loading ${staffRole} staff:`, error);
                
                // Add placeholder even on error
                const staffSelect = $('#' + targetId);
                let placeholderText = staffRole === 'MECHANIC' ? 
                    (targetId === 'mechanic_1' ? '-- Pilih Mekanik 1 --' : '-- Pilih Mekanik 2 (Opsional) --') :
                    (targetId === 'helper_1' ? '-- Pilih Helper 1 --' : '-- Pilih Helper 2 (Opsional) --');
                
                staffSelect.empty().append(`<option value="">${placeholderText}</option>`);
            }
        });
    }


    function displayStaff(staffType, staffList) {
        let dropdownList = $(`#${staffType}DropdownList`);
        dropdownList.empty();
        
        staffList.forEach(function(staff) {
            dropdownList.append(`
                <a class="dropdown-item staff-item" href="#" data-staff-type="${staffType}" data-staff-id="${staff.id}">
                    ${staff.staff_name}
                </a>
            `);
        });
    }

    // Staff selection handler
    $(document).on('click', '.staff-item', function(e) {
        e.preventDefault();
        let staffType = $(this).data('staff-type');
        let staffId = $(this).data('staff-id');
        let staffName = $(this).text();
        
        $(`#${staffType}SelectedText`).text(staffName);
        $(`#${staffType}_staff_id`).val(staffId);
    });

    // Staff filter function - make it globally accessible
    window.filterStaff = function(staffType) {
        let searchTerm = $(`#${staffType}Search`).val().toLowerCase();
        let filteredStaff = window.allStaff[staffType].filter(staff => {
            return staff.staff_name.toLowerCase().includes(searchTerm);
        });
        displayStaff(staffType, filteredStaff);
    }

    function loadMechanicHelperDropdowns() {
        loadStaffDropdown('MECHANIC', 'mechanic_1');
        loadStaffDropdown('MECHANIC', 'mechanic_2');
        loadStaffDropdown('HELPER', 'helper_1');
        loadStaffDropdown('HELPER', 'helper_2');
    }

    // Auto-fill admin and foreman based on area
    function loadAreaStaff(areaId) {
        if (!areaId) {
            $('#admin_display').val('');
            $('#admin_id').val('');
            $('#foreman_display').val('');
            $('#foreman_id').val('');
            return;
        }
        
        $.ajax({
            url: '<?= base_url('service/work-orders/get-area-staff') ?>',
            type: 'POST',
            data: { area_id: areaId },
                success: function(response) {
                    if (response.success) {
                    // Set admin
                    if (response.data.admin) {
                        $('#admin_display').val(response.data.admin.staff_name);
                        $('#admin_id').val(response.data.admin.id);
                    }
                    
                    // Set foreman
                    if (response.data.foreman) {
                        $('#foreman_display').val(response.data.foreman.staff_name);
                        $('#foreman_id').val(response.data.foreman.id);
                    }
                    
                    console.log('✅ Area staff loaded successfully');
                                } else {
                    console.error('❌ Error loading area staff:', response.message);
                }
            },
            error: function() {
                console.error('❌ Error loading area staff');
            }
        });
    }

    // Add validation for staff selection
    function validateStaffSelection() {
        const mechanic1 = $('#mechanic_1').val();
        const mechanic2 = $('#mechanic_2').val();
        const helper1 = $('#helper_1').val();
        const helper2 = $('#helper_2').val();
        
        // Check if at least one mechanic is selected
        if (!mechanic1 && !mechanic2) {
            $('#mechanic_1').addClass('is-invalid');
            $('#mechanic_2').addClass('is-invalid');
                } else {
            $('#mechanic_1, #mechanic_2').removeClass('is-invalid');
        }
        
        // Check if at least one helper is selected
        if (!helper1 && !helper2) {
            $('#helper_1').addClass('is-invalid');
            $('#helper_2').addClass('is-invalid');
                } else {
            $('#helper_1, #helper_2').removeClass('is-invalid');
        }
    }

    // Add event listeners for staff validation
    $(document).on('change', '#mechanic_1, #mechanic_2, #helper_1, #helper_2', function() {
        validateStaffSelection();
    });

    // Dynamic Sparepart Form
    let sparepartRowCount = 0;

    // Add sparepart row
    $('#addSparepartRow').on('click', function() {
        addSparepartRow();
    });

    // Remove sparepart row
    $(document).on('click', '.removeSparepartRow', function() {
        $(this).closest('tr').remove();
    });

    /**
     * Function addSparepartRow - searchable dropdown seperti unit dropdown
     * Menggunakan Select2 dengan search functionality
     */
    addSparepartRow = function(sparepartData = null) {
        sparepartRowCount++;
        console.log(`🔧 Adding sparepart row ${sparepartRowCount} [UNIT STYLE]`);
        
        // 1. Buat baris dengan dropdown standar (PERSIS seperti format unit_id)
        const row = `
            <tr>
                <td>
                    <select class="form-select" name="sparepart_name[]" id="sparepart_${sparepartRowCount}" required>
                        <option value="">-- Pilih Sparepart --</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control" name="sparepart_quantity[]" value="1" min="1" required>
                </td>
                <td>
                    <select class="form-select form-select-sm" name="sparepart_unit[]" required>
                        <option value="PCS">PCS</option>
                        <option value="SET">SET</option>
                        <option value="LITER">LITER</option>
                        <option value="KG">KG</option>
                        <option value="METER">METER</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm removeSparepartRow">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#sparepartTableBody').append(row);
        
        // 2. Ambil referensi elemen
        const sparepartSelect = $(`#sparepart_${sparepartRowCount}`);
        const quantityInput = sparepartSelect.closest('tr').find('input[name="sparepart_quantity[]"]');
        const unitSelect = sparepartSelect.closest('tr').find('select[name="sparepart_unit[]"]');
        
        // 3. Tambahkan semua data sparepart ke dropdown (PERSIS seperti unit_id)
        sparepartSelect.empty().append('<option value="">-- Pilih Sparepart --</option>');
        
        if (window.sparepartsData && Array.isArray(window.sparepartsData) && window.sparepartsData.length > 0) {
            window.sparepartsData.forEach(function(sparepart) {
                // Handle both formats: sparepart.text or other formats
                const sparepartValue = sparepart.text || sparepart.nama_sparepart || sparepart.desc_sparepart || '';
                const sparepartLabel = sparepart.text || sparepart.nama_sparepart || sparepart.desc_sparepart || '';
                if (sparepartValue) {
                    sparepartSelect.append(`<option value="${sparepartValue}">${sparepartLabel}</option>`);
                }
            });
            console.log(`✅ Added ${window.sparepartsData.length} spareparts to dropdown #sparepart_${sparepartRowCount}`);
        } else {
            console.warn(`⚠️ No spareparts data available for row ${sparepartRowCount}. SparepartsData:`, window.sparepartsData);
        }
        
        // 4. Inisialisasi Select2 untuk searchable dropdown (PERSIS seperti unit)
        // Use longer timeout to ensure DOM is ready and data is populated
        setTimeout(function() {
            try {
                const sparepartElement = $(`#sparepart_${sparepartRowCount}`);
                if (sparepartElement.length === 0) {
                    console.error(`❌ Sparepart element #sparepart_${sparepartRowCount} not found!`);
                    return;
                }
                
                if (!sparepartElement.hasClass('select2-hidden-accessible')) {
                    sparepartElement.select2({
                        placeholder: '-- Pilih Sparepart --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#workOrderModal'),
                        minimumInputLength: 0, // Enable search immediately
                        minimumResultsForSearch: 0, // Always show search box
                        language: {
                            noResults: function() { return "Tidak ada hasil ditemukan"; },
                            searching: function() { return "Mencari..."; }
                        }
                    });
                    console.log(`✅ Select2 initialized for sparepart_${sparepartRowCount} with search`);
                } else {
                    console.log(`ℹ️ Select2 already initialized for sparepart_${sparepartRowCount}`);
                }
            } catch (error) {
                console.error(`❌ Error initializing Select2 for sparepart_${sparepartRowCount}:`, error);
            }
        }, 150);
        
        // 5. Jika data disediakan, isi form
        if (sparepartData) {
            try {
                // Set sparepart name
                if (sparepartData.sparepart_name || sparepartData.name) {
                    let sparepartName = sparepartData.sparepart_name || sparepartData.name;
                    
                    // Jika option tidak ada, tambahkan
                    if (sparepartSelect.find(`option[value="${sparepartName}"]`).length === 0) {
                        sparepartSelect.append(`<option value="${sparepartName}">${sparepartName}</option>`);
                    }
                    
                    // Set value dan trigger change untuk Select2
                    sparepartSelect.val(sparepartName).trigger('change');
                }
                
                // Set quantity dan unit
                if (sparepartData.quantity || sparepartData.qty) {
                    quantityInput.val(sparepartData.quantity || sparepartData.qty);
                }
                
                if (sparepartData.unit || sparepartData.satuan) {
                    unitSelect.val(sparepartData.unit || sparepartData.satuan);
                }
                
                console.log(`✅ Populated sparepart row ${sparepartRowCount} with:`, sparepartName);
            } catch (error) {
                console.error('❌ Error: ', error);
            }
        }
        
        return sparepartSelect; // Return for chaining
    };
    
    console.log('✅ Sparepart dropdown fix applied - Standard dropdown mode active');
});

// Production asset optimization
<?php if (ENVIRONMENT === 'production'): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Load minified assets untuk production
    const optimizedCSS = document.querySelector('link[href*="optima-pro.css"]');
    if (optimizedCSS) {
        const minifiedCSS = '<?= $assetService->getAsset('css', 'optima-pro.css') ?>';
        if (minifiedCSS) {
            optimizedCSS.href = '<?= base_url() ?>' + minifiedCSS;
        }
    }
});
<?php endif; ?>
</script>

<?php include 'sparepart_validation.php'; ?>
<?php include 'unit_verification.php'; ?>

<?= $this->endSection() ?>


