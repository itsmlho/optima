<?= $this->extend('layouts/base') ?>

<?php
/**
 * Work Orders (Service) Module
 *
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 *
 * Quick Reference:
 * - Status Open / In Progress → <span class="badge badge-soft-cyan">Open</span>
 * - Status Completed / Closed  → <span class="badge badge-soft-green">Completed</span>
 * - Priority / Category        → <span class="badge badge-soft-blue">High</span>
 * - Department / Area          → <span class="badge badge-soft-cyan">Area</span>
 * - Tool                       → <span class="badge badge-soft-gray">Tool</span>
 * - Sparepart                  → <span class="badge badge-soft-blue">Sparepart</span>
 * - Used / Returned            → <span class="badge badge-soft-green">Used</span>, <span class="badge badge-soft-yellow">Returned</span>
 *
 * See optima-pro.css line ~2030 for complete badge standards
 */

// Load global permission helper
helper('global_permission');

// Get permissions for service module
$permissions = get_global_permission('service');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];

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
                    <div class="text-muted"><?= lang('Service.work_orders') ?> <?= lang('App.total') ?></div>
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
                    <div class="text-muted"><?= lang('Service.open') ?></div>
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
                    <div class="text-muted"><?= lang('Service.in_progress') ?></div>
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
                    <div class="text-muted"><?= lang('Service.completed') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Tab System for Work Orders -->
<div class="card table-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-wrench me-2 text-primary"></i>
                <?= lang('Service.service_list') ?>
            </h5>
            <p class="text-muted small mb-0">
                Monitor and manage service work orders from open to completion
                <span class="ms-2 text-info">
                    <i class="bi bi-info-circle me-1"></i>
                    <small>Tip: Use Progress / Closed tabs and filters to find work orders</small>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($can_export): ?>
            <a href="<?= base_url('service/export_workorder') ?>" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-excel"></i> <?= lang('Common.export') ?> Excel
            </a>
            <?php else: ?>
            <a href="#" class="btn btn-outline-success btn-sm disabled" onclick="return false;" title="<?= lang('App.access_denied') ?>">
                <i class="fas fa-file-excel"></i> <?= lang('Common.export') ?> Excel
            </a>
            <?php endif; ?>
            <?php if ($can_create): ?>
            <button id="btn-add-wo" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> <?= lang('Common.add') ?> <?= lang('Service.work_order') ?></button>
            <?php else: ?>
            <button id="btn-add-wo" class="btn btn-primary btn-sm disabled" onclick="return false;" title="<?= lang('App.access_restricted') ?>"><i class="fas fa-plus me-1"></i> <?= lang('Common.add') ?> <?= lang('Service.work_order') ?></button>
            <?php endif; ?>
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
                            <span><?= lang('Common.progress') ?></span>
                            <span class="badge" id="progress-count">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="closed-tab" data-bs-toggle="tab" data-bs-target="#closed-pane" type="button" role="tab" aria-controls="closed-pane" aria-selected="false">
                            <i class="fas fa-check-circle"></i>
                            <span><?= lang('Common.closed') ?></span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="workOrderTabContent">
            <!-- Progress Tab -->
            <div class="tab-pane fade show active" id="progress-pane" role="tabpanel" aria-labelledby="progress-tab">
                <!-- Filter Controls for Progress -->
                <div class="row mb-3 g-2">
                    <div class="col-md-3">
                        <label for="filter-status-progress" class="form-label fw-semibold mb-1"><i class="fas fa-filter text-primary me-1"></i><?= lang('Common.status') ?></label>
                        <select id="filter-status-progress" class="form-select form-select-sm">
                            <option value=""><?= lang('App.all_status') ?></option>
                            <?php foreach ($statuses as $status): ?>
                                <?php if (strtolower($status['status_name']) !== 'closed'): ?>
                                <option value="<?= $status['status_name'] ?>"><?= $status['status_name'] ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-priority-progress" class="form-label fw-semibold mb-1"><i class="fas fa-flag text-primary me-1"></i><?= lang('App.priority') ?></label>
                        <select id="filter-priority-progress" class="form-select form-select-sm">
                            <option value=""><?= lang('App.all_priority') ?></option>
                            <?php foreach ($priorities as $priority): ?>
                            <option value="<?= $priority['priority_name'] ?>"><?= $priority['priority_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-departemen-progress" class="form-label fw-semibold mb-1"><i class="fas fa-layer-group text-primary me-1"></i>Departemen</label>
                        <select id="filter-departemen-progress" class="form-select form-select-sm">
                            <option value="">Semua Departemen</option>
                            <?php foreach ($departemens as $dept): ?>
                            <option value="<?= $dept['id_departemen'] ?>"><?= esc($dept['nama_departemen']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-branch-progress" class="form-label fw-semibold mb-1"><i class="fas fa-map-marker-alt text-primary me-1"></i>Branch</label>
                        <select id="filter-branch-progress" class="form-select form-select-sm">
                            <option value="">Semua Branch</option>
                            <?php foreach ($areas as $area): ?>
                            <option value="<?= $area['id'] ?>"><?= esc($area['area_code'] . ' - ' . $area['area_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-start-date-progress" class="form-label fw-semibold mb-1">Dari Tanggal</label>
                        <input type="date" id="filter-start-date-progress" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label for="filter-end-date-progress" class="form-label fw-semibold mb-1">Sampai Tanggal</label>
                        <input type="date" id="filter-end-date-progress" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="reset-filter-progress" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-times me-1"></i>Reset Filter
                        </button>
                    </div>
                </div>
                
                <!-- Progress Table -->
                <?php if (!$can_view): ?>
                <div class="alert alert-warning m-3">
                    <i class="fas fa-lock me-2"></i>
                    <strong><?= lang('App.access_restricted') ?>:</strong> <?= lang('App.no_permission_view') ?> <?= strtolower(lang('Service.work_orders')) ?>. 
                    <?= lang('App.contact_administrator') ?>.
                </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="progressWorkOrdersTable" class="table table-striped table-hover mb-0 <?= !$can_view ? 'table-disabled' : '' ?>">
                        <thead class="table-light">
                            <tr>
                                <th><?= lang('Service.work_order') ?></th>
                                <th><?= lang('Common.date') ?></th>
                                <th><?= lang('App.unit') ?></th>
                                <th><?= lang('Common.type') ?></th>
                                <th><?= lang('App.priority') ?></th>
                                <th><?= lang('Common.category') ?></th>
                                <th><?= lang('Common.status') ?></th>
                                <th width="10%" class="text-center"><?= lang('App.action') ?></th>
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
                <div class="row mb-3 g-2">
                    <div class="col-md-3">
                        <label for="filter-priority-closed" class="form-label fw-semibold mb-1"><i class="fas fa-flag text-primary me-1"></i><?= lang('App.priority') ?></label>
                        <select id="filter-priority-closed" class="form-select form-select-sm">
                            <option value=""><?= lang('App.all_priority') ?></option>
                            <?php foreach ($priorities as $priority): ?>
                            <option value="<?= $priority['priority_name'] ?>"><?= $priority['priority_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-departemen-closed" class="form-label fw-semibold mb-1"><i class="fas fa-layer-group text-primary me-1"></i>Departemen</label>
                        <select id="filter-departemen-closed" class="form-select form-select-sm">
                            <option value="">Semua Departemen</option>
                            <?php foreach ($departemens as $dept): ?>
                            <option value="<?= $dept['id_departemen'] ?>"><?= esc($dept['nama_departemen']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-branch-closed" class="form-label fw-semibold mb-1"><i class="fas fa-map-marker-alt text-primary me-1"></i>Branch</label>
                        <select id="filter-branch-closed" class="form-select form-select-sm">
                            <option value="">Semua Branch</option>
                            <?php foreach ($areas as $area): ?>
                            <option value="<?= $area['id'] ?>"><?= esc($area['area_code'] . ' - ' . $area['area_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-month-closed" class="form-label fw-semibold mb-1">Bulan</label>
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
                    <div class="col-md-3">
                        <label for="filter-start-date-closed" class="form-label fw-semibold mb-1">Dari Tanggal</label>
                        <input type="date" id="filter-start-date-closed" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label for="filter-end-date-closed" class="form-label fw-semibold mb-1">Sampai Tanggal</label>
                        <input type="date" id="filter-end-date-closed" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="reset-filter-closed" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-times me-1"></i>Reset Filter
                        </button>
                    </div>
                </div>
                
                <!-- Closed Table -->
                <?php if (!$can_view): ?>
                <div class="alert alert-warning m-3">
                    <i class="fas fa-lock me-2"></i>
                    <strong>Access Denied:</strong> You do not have permission to view closed work orders. 
                    Please contact your administrator to request access.
                </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="closedWorkOrdersTable" class="table table-striped table-hover mb-0 <?= !$can_view ? 'table-disabled' : '' ?>">
                        <thead class="table-light">
                            <tr>
                                <th><?= lang('Service.work_order') ?></th>
                                <th><?= lang('Common.date') ?></th>
                                <th><?= lang('App.unit') ?></th>
                                <th><?= lang('Common.type') ?></th>
                                <th><?= lang('App.priority') ?></th>
                                <th><?= lang('Common.category') ?></th>
                                <th><?= lang('Service.closed_date') ?></th>
                                <th width="10%" class="text-center"><?= lang('App.action') ?></th>
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
    </div>
</div>

<!-- Modals Section -->

<!-- Modal Add/Edit Work Order -->
<div class="modal fade modal-wide" id="workOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workOrderFormTitle"><i class="fas fa-plus-circle me-2"></i><?= lang('Common.add') ?> <?= lang('Service.work_order') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="workOrderForm" action="<?= base_url('service/work-orders/store') ?>" method="post" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" id="work_order_id" name="work_order_id">
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i><?= lang('Service.main_info_work_order') ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="work_order_number" class="form-label"><?= lang('Service.wo_number') ?></label>
                                    <input type="text" class="form-control" id="work_order_number" name="work_order_number" readonly>
                                    <small class="form-text text-muted"><?= lang('Service.wo_number_auto') ?></small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="order_type" class="form-label"><?= lang('Service.order_type') ?> <span class="text-danger">*</span></label>
                                    <select class="form-select" id="order_type" name="order_type" required>
                                        <option value="" selected disabled>-- <?= lang('Service.select_order_type') ?> --</option>
                                        <option value="COMPLAINT"><?= lang('Service.complaint') ?></option>
                                        <option value="PMPS"><?= lang('Service.pmps') ?></option>
                                        <option value="REKONDISI">Rekondisi</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="unit_id" class="form-label"><?= lang('App.unit') ?> <span class="text-danger">*</span></label>
                                    <select class="form-select" id="unit_id" name="unit_id" required>
                                        <option value="" selected disabled>-- <?= lang('App.select') ?> <?= lang('App.unit') ?> --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label"><?= lang('Service.category') ?> <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="" selected disabled>-- <?= lang('App.select') ?> <?= lang('Service.category') ?> --</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" data-priority="<?= $category['default_priority_id'] ?? '' ?>"><?= $category['category_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subcategory_id" class="form-label"><?= lang('Service.sub_category') ?></label>
                                    <select class="form-select" id="subcategory_id" name="subcategory_id">
                                        <option value="">-- <?= lang('Service.select_sub_category') ?> --</option>
                                    </select>
                                    <small class="form-text text-muted"><?= lang('Service.sub_category_after_category') ?></small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="priority_display" class="form-label"><?= lang('Service.priority') ?></label>
                                    <input type="text" class="form-control" id="priority_display" readonly placeholder="<?= lang('Service.priority_auto') ?>">
                                    <input type="hidden" id="priority_id" name="priority_id">
                                    <small class="form-text text-muted"><?= lang('Service.priority_based_category') ?></small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="area" class="form-label"><?= lang('App.area') ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="area" name="area" readonly placeholder="<?= lang('Service.area_auto') ?>">
                                    <input type="hidden" id="area_id" name="area_id">
                                    <small class="form-text text-muted"><?= lang('Service.area_based_unit') ?></small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pic_name" class="form-label"><?= lang('App.pic') ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pic_name" name="pic_name" readonly placeholder="<?= lang('Service.pic_example') ?>">
                                    <small class="form-text text-muted">Automatically based on area</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="complaint_description" class="form-label"><?= lang('Service.complaint_description') ?> <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="complaint_description" name="complaint_description" rows="3" placeholder="<?= lang('Service.explain_complaint_detail') ?>" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Area Indicator Alert -->
                    <div id="areaIndicator" class="alert d-none mb-3" role="alert">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <strong id="areaIndicatorText"><?= lang('Service.select_unit_see_area') ?></strong>
                    </div>
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-users-cog me-2"></i>Staff Assignment</h6>
                        </div>
                        <div class="card-body">
                            <!-- Admin & Foreman - Dropdown -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="admin_id" class="form-label">Admin <small class="text-muted">(Optional)</small></label>
                                    <select class="form-select" id="admin_id" name="admin_id">
                                        <option value="" selected>-- <?= lang('Common.choose') ?> <?= lang('App.admin') ?> --</option>
                                    </select>
                                    <small class="form-text text-muted">Auto-selected if area assigned</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foreman_id" class="form-label">Foreman <small class="text-muted">(Optional)</small></label>
                                    <select class="form-select" id="foreman_id" name="foreman_id">
                                        <option value="" selected>-- <?= lang('Common.choose') ?> <?= lang('App.foreman') ?> --</option>
                                    </select>
                                    <small class="form-text text-muted">Auto-selected if area assigned</small>
                                </div>
                            </div>
                            
                            <!-- Mekanik - Pilihan 1-2 orang -->
                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Mechanic <span class="text-danger">*</span> <small class="text-muted">(Min 1, Max 2)</small></label>
                                    <div id="mechanic-container">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="mechanic_1" name="mechanic_id[]">
                                                    <option value="" selected disabled>-- <?= lang('Service.mechanic') ?> 1 --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="mechanic_2" name="mechanic_id[]">
                                                    <option value="" selected disabled>-- <?= lang('Service.mechanic') ?> 2 (<?= lang('Common.optional') ?>) --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>     
                            <!-- Helper - Pilihan 1-2 orang -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Helper <small class="text-muted">(Optional, Max 2)</small></label>
                                    <div id="helper-container">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="helper_1" name="helper_id[]">
                                                    <option value="" selected>-- <?= lang('App.helper') ?> 1 (<?= lang('Common.optional') ?>) --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="helper_2" name="helper_id[]">
                                                    <option value="" selected disabled>-- <?= lang('App.helper') ?> 2 (<?= lang('Common.optional') ?>) --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                            
                    <!-- Items Brought (Spareparts & Tools) -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-toolbox me-2"></i>Items Brought (Spareparts & Tools)</h6>
                            </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm" id="sparepartTable" style="table-layout: fixed; width: 100%;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 100px;">Type*</th>
                                            <th style="width: 280px;">Item Name*</th>
                                            <th style="width: 80px;">Qty*</th>
                                            <th style="width: 90px;">Unit*</th>
                                            <th style="width: 110px;">Source*</th>
                                            <th style="width: auto;">Notes</th>
                                            <th style="width: 60px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sparepartTableBody">
                                        <!-- Dynamic rows will be added here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-sm" id="addSparepartRow">
                                    <i class="fas fa-plus"></i> <?= lang('Common.add') ?> <?= lang('Service.item') ?>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Fields with <span class="text-danger">*</span> are required</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="event.stopPropagation();">
                        <i class="fas fa-times me-1"></i> <?= lang('Common.cancel') ?>
                    </button>
                    <button type="submit" class="btn btn-primary" form="workOrderForm" id="btnSubmitWo" onclick="event.stopPropagation();">
                        <i class="fas fa-save me-1"></i> <?= lang('Common.save') ?> <?= lang('Service.work_order') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Work Order -->
<div class="modal fade" id="viewWorkOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="margin-top: 2rem;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Work Order Details: <span id="viewWoNumberHeader" class="fw-bold">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-body-tertiary p-4">
                <div class="row g-3">
                    <!-- Left Column: Unit Details -->
                    <div class="col-lg-7">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="mb-3 text-dark border-bottom pb-2">
                                    <i class="fas fa-forklift me-2 text-primary"></i>Unit & Component Details
                                </h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-sm-4 text-muted">Unit Number</dt>
                                    <dd class="col-sm-8 fw-bold text-primary" id="viewUnitNumber">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Brand & Model</dt>
                                    <dd class="col-sm-8" id="viewUnitModel">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Unit Type</dt>
                                    <dd class="col-sm-8" id="viewUnitType">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Serial Number</dt>
                                    <dd class="col-sm-8 font-monospace" id="viewUnitSerial">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Unit Status</dt>
                                    <dd class="col-sm-8"><span class="badge badge-soft-green" id="viewUnitStatus">-</span></dd>
                                    
                                    <dt class="col-sm-4 text-muted">Capacity</dt>
                                    <dd class="col-sm-8" id="viewUnitCapacity">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Unit Year</dt>
                                    <dd class="col-sm-8" id="viewUnitYear">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Hour Meter (HM)</dt>
                                    <dd class="col-sm-8 fw-bold text-success" id="viewUnitHourMeter">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted pt-2 border-top">Engine Model</dt>
                                    <dd class="col-sm-8 pt-2 border-top" id="viewUnitEngine">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Engine SN</dt>
                                    <dd class="col-sm-8 font-monospace" id="viewUnitEngineSN">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Mast Model</dt>
                                    <dd class="col-sm-8" id="viewUnitMast">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Mast SN</dt>
                                    <dd class="col-sm-8 font-monospace" id="viewUnitMastSN">-</dd>
                                    
                                    <dt class="col-sm-4 text-muted">Mast Height</dt>
                                    <dd class="col-sm-8" id="viewUnitMastHeight">-</dd>

                                    <div id="unitComponentsSection" class="contents" style="display: none;">
                                        <dt class="col-sm-4 text-muted pt-2 border-top">Attachment</dt>
                                        <dd class="col-sm-8 pt-2 border-top" id="viewUnitAttachmentList">-</dd>

                                        <dt id="batteryLabel" class="col-sm-4 text-muted" style="display: none;">Battery</dt>
                                        <dd id="batteryValue" class="col-sm-8" style="display: none;">
                                            <span id="viewUnitBatteryList">-</span>
                                        </dd>
                                        
                                        <dt id="chargerLabel" class="col-sm-4 text-muted" style="display: none;">Charger</dt>
                                        <dd id="chargerValue" class="col-sm-8" style="display: none;">
                                            <span id="viewUnitChargerList">-</span>
                                        </dd>
                                    </div>
                                    
                                    <div id="unitAccessoriesInline" class="contents" style="display: none;">
                                        <dt class="col-sm-4 text-muted pt-2 border-top">Accessories</dt>
                                        <dd class="col-sm-8 pt-2 border-top" id="viewUnitAccessoriesList"></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Work Order Info -->
                    <div class="col-lg-5">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="mb-3 text-dark border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>Work Information
                                </h6>
                                <dl class="row mb-3 small">
                                    <dt class="col-sm-5 text-muted">Report Date</dt>
                                    <dd class="col-sm-7" id="viewWoReportDate">-</dd>
                                    
                                    <dt class="col-sm-5 text-muted">Order Type</dt>
                                    <dd class="col-sm-7" id="viewWoType">-</dd>
                                    
                                    <dt class="col-sm-5 text-muted">Category</dt>
                                    <dd class="col-sm-7" id="viewWoCategory">-</dd>
                                    
                                    <dt class="col-sm-5 text-muted">Department</dt>
                                    <dd class="col-sm-7" id="viewWoDepartemen">-</dd>
                                </dl>
                                
                                <div class="text-center p-3 rounded bg-body-secondary">
                                    <div class="row">
                                        <div class="col-6 border-end">
                                            <label class="small text-muted mb-1 d-block">Status</label>
                                            <span class="badge" id="viewWoStatus">-</span>
                                        </div>
                                        <div class="col-6">
                                            <label class="small text-muted mb-1 d-block">Priority</label>
                                            <span class="badge" id="viewWoPriority">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="mb-3 text-dark border-bottom pb-2">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>Customer & Location
                                </h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-sm-5 text-muted">Customer</dt>
                                    <dd class="col-sm-7 fw-bold" id="viewUnitCustomer">-</dd>
                                    
                                    <dt class="col-sm-5 text-muted">Location</dt>
                                    <dd class="col-sm-7" id="viewUnitLocation">-</dd>
                                    
                                    <dt class="col-sm-5 text-muted">Area</dt>
                                    <dd class="col-sm-7" id="viewWoArea">-</dd>
                                </dl>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="mb-3 text-dark border-bottom pb-2">
                                    <i class="fas fa-users me-2 text-primary"></i>Staff Assignment
                                </h6>
                                <ul class="list-unstyled mb-0 small">
                                    <li class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-shield fa-fw me-2 text-muted"></i>
                                        <strong class="me-2">Admin:</strong>
                                        <span class="ms-auto text-end" id="viewWoAdmin">-</span>
                                    </li>
                                    <li class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-tie fa-fw me-2 text-muted"></i>
                                        <strong class="me-2">Foreman:</strong>
                                        <span class="ms-auto text-end" id="viewWoForeman">-</span>
                                    </li>
                                    <li class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-cog fa-fw me-2 text-muted"></i>
                                        <strong class="me-2">Mechanic:</strong>
                                        <span class="ms-auto text-end" id="viewWoMechanic">-</span>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <i class="fas fa-user-friends fa-fw me-2 text-muted"></i>
                                        <strong class="me-2">Helper:</strong>
                                        <span class="ms-auto text-end" id="viewWoHelper">-</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3 text-dark border-bottom pb-2">
                                    <i class="fas fa-clock me-2 text-primary"></i>Time & Date
                                </h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-sm-5 text-muted">TTR</dt>
                                    <dd class="col-sm-7 fw-bold text-primary" id="viewWoTTR">-</dd>
                                    
                                    <dt class="col-sm-5 text-muted">Completion Date</dt>
                                    <dd class="col-sm-7" id="viewWoCompletionDate"><?= lang('Service.not_completed') ?? 'Not completed' ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>



                <div id="sparepartBroughtSection" class="card mt-4" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-toolbox me-2"></i>Items Brought (Spareparts & Tools)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 4%;">No</th>
                                        <th style="width: 10%;">Type</th>
                                        <th style="width: 28%;">Item Name</th>
                                        <th style="width: 12%;">Code</th>
                                        <th style="width: 8%;">Qty Brought</th>
                                        <th style="width: 8%;">Qty Used</th>
                                        <th style="width: 12%;">Status</th>
                                        <th style="width: 18%;">Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="viewSparepartBroughtList">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted"><?= lang('Service.no_items_brought') ?? 'No items brought' ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i>Work Details & Notes</h6>
                    </div>
                    <div class="card-body">
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Customer Complaint</label>
                                <div class="p-3 rounded bg-light" style="min-height: 120px;" id="viewWoComplaint"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Analysis & Repair</label>
                                <div class="p-3 rounded bg-light" style="min-height: 120px;" id="viewWoRepair"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted"><?= lang('Service.additional_notes') ?? 'Additional Notes' ?></label>
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
                    <i class="fas fa-edit me-1"></i><?= lang('Common.edit') ?> <?= lang('Service.work_order') ?>
                </button>
                <button type="button" class="btn btn-danger btn-delete-from-view" data-id="" data-wo-number="">
                    <i class="fas fa-trash me-1"></i><?= lang('Common.delete') ?> <?= lang('Service.work_order') ?>
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- Select2 JS sudah dimuat di base layout -->

<script>
// Global permission variables (accessible from all functions)
const canViewService = <?= $can_view ? 'true' : 'false' ?>;
const canCreateService = <?= $can_create ? 'true' : 'false' ?>;
const canExportService = <?= $can_export ? 'true' : 'false' ?>;

// Safe language helper function (fallback if window.lang not loaded yet)
if (typeof window.lang !== 'function') {
    window.lang = function(key) {
        const translations = {
            'cancel': 'Batal',
            'save': 'Simpan',
            'delete': 'Hapus',
            'close': 'Tutup',
            'submit': 'Submit'
        };
        return translations[key] || key;
    };
}

$(document).ready(function() {
    // Hide global page loading overlay as soon as this page JS is ready
    try {
        if (window.OptimaPro && typeof window.OptimaPro.hideLoading === 'function') {
            window.OptimaPro.hideLoading();
        } else {
            const loadingEl = document.getElementById('pageLoading');
            if (loadingEl) {
                loadingEl.style.opacity = '0';
                loadingEl.style.display = 'none';
                loadingEl.classList.add('fade-out');
            }
        }
    } catch (e) {
        console.warn('Failed to hide pageLoading overlay early:', e);
    }
    
    // REMOVED: Pre-loading 14k+ spareparts (performance optimization)
    // Now using AJAX Select2 for on-demand search via /service/work-orders/search-spareparts
    // <?php if (!empty($spareparts)): ?>
    //     window.sparepartsData = <?= json_encode($spareparts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
    // <?php else: ?>
    //     window.sparepartsData = [];
    // <?php endif; ?>
    
    // unitsData: REMOVED - KANIBAL dropdown now uses Select2 AJAX (see toggleKanibalFields)
    
    // Initialize user department scope for filtering
    <?php if (!empty($user_departemen_ids)): ?>
        window.currentUserDepartment = {
            ids: <?= json_encode(array_map('intval', $user_departemen_ids)) ?>,
            name: '<?= esc($user_departemen_name ?? '', 'js') ?>',
            scopeType: '<?= esc($scope_type ?? '', 'js') ?>'
        };
        console.log('🔐 User Department Scope:', window.currentUserDepartment.name, '| IDs:', window.currentUserDepartment.ids, '| Type:', window.currentUserDepartment.scopeType);
    <?php else: ?>
        window.currentUserDepartment = null;
        console.log('ℹ️ No department filtering - full access');
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
                d.start_date = $('#filter-start-date-progress').val();
                d.end_date = $('#filter-end-date-progress').val();
                d.departemen_id = $('#filter-departemen-progress').val();
                d.area_id = $('#filter-branch-progress').val();
                // Add CSRF token
                if (typeof window.getCsrfToken === 'function') {
                    d[window.csrfTokenName] = window.getCsrfToken();
                } else if (window.csrfToken) {
                    d[window.csrfTokenName] = window.csrfToken;
                }
            }
        },
        columns: [
            { data: 1 }, // work_order_number
            { data: 2 }, // report_date
            { data: 3 }, // unit_info
            { data: 4 }, // order_type
            { data: 5 }, // priority_badge
            { data: 6 }, // category
            { data: 7 }, // status_badge
            { data: 8, orderable: false, searchable: false } // action
        ],
        order: [[1, 'desc']], // Order by report_date descending
        language: {
            "sLengthMenu": "Show _MENU_ entries",
            "sZeroRecords": "No matching records found",
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
            "sInfoEmpty": "Showing 0 to 0 of 0 entries",
            "sInfoFiltered": "(filtered from _MAX_ total entries)",
            "sInfoPostFix": "",
            "sSearch": "Search:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "First",
                "sPrevious": "Previous",
                "sNext": "Next",
                "sLast": "Last"
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
    
    // Assign to window for access from modals
    window.progressWorkOrdersTable = progressTable;

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
                d.start_date = $('#filter-start-date-closed').val();
                d.end_date = $('#filter-end-date-closed').val();
                d.departemen_id = $('#filter-departemen-closed').val();
                d.area_id = $('#filter-branch-closed').val();
                // Add CSRF token
                if (typeof window.getCsrfToken === 'function') {
                    d[window.csrfTokenName] = window.getCsrfToken();
                } else if (window.csrfToken) {
                    d[window.csrfTokenName] = window.csrfToken;
                }
            }
        },
        columns: [
            { data: 1 }, // work_order_number
            { data: 2 }, // report_date
            { data: 3 }, // unit_info
            { data: 4 }, // order_type
            { data: 5 }, // priority_badge
            { data: 6 }, // category
            { data: 9 }, // closed_date
            { data: 8, orderable: false, searchable: false } // action
        ],
        order: [[6, 'desc']], // Order by closed_date descending
        language: {
            "sProcessing": "Processing...",
            "sLengthMenu": "Show _MENU_ entries",
            "sZeroRecords": "No matching records found",
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
            "sInfoEmpty": "Showing 0 to 0 of 0 entries",
            "sInfoFiltered": "(filtered from _MAX_ total entries)",
            "sInfoPostFix": "",
            "sSearch": "Search:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "First",
                "sPrevious": "Previous",
                "sNext": "Next",
                "sLast": "Last"
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
    
    // Assign to window for access from modals
    window.closedWorkOrdersTable = closedTable;
    
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
        // console.log('Closed tab activated - reloading data');
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

    // Filter handlers for Progress tab
    $('#filter-status-progress, #filter-priority-progress, #filter-departemen-progress, #filter-branch-progress').on('change', function() {
        reloadProgressTable();
    });
    $('#filter-start-date-progress, #filter-end-date-progress').on('change', function() {
        reloadProgressTable();
    });
    $('#reset-filter-progress').on('click', function() {
        $('#filter-status-progress, #filter-priority-progress, #filter-departemen-progress, #filter-branch-progress').val('');
        $('#filter-start-date-progress, #filter-end-date-progress').val('');
        reloadProgressTable();
    });

    // Filter handlers for Closed tab
    $('#filter-priority-closed, #filter-month-closed, #filter-departemen-closed, #filter-branch-closed').on('change', function() {
        reloadClosedTable();
    });
    $('#filter-start-date-closed, #filter-end-date-closed').on('change', function() {
        reloadClosedTable();
    });
    $('#reset-filter-closed').on('click', function() {
        $('#filter-priority-closed, #filter-month-closed, #filter-departemen-closed, #filter-branch-closed').val('');
        $('#filter-start-date-closed, #filter-end-date-closed').val('');
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
            // console.log('🔒 View Only mode activated for Service - blocking all table interactions');
            
            // Override showWorkOrderDetail function
            window.showWorkOrderDetail = function(id, woNumber) {
                // console.log('🚫 Access Denied: showWorkOrderDetail blocked for View Only user');
                safeShowNotification('Access Denied: You do not have permission to view work order details.', 'error');
                return false;
            };
            
            // Prevent all table interactions
            $('#progressWorkOrdersTable, #closedWorkOrdersTable').off('click').on('click', function(e) {
                // console.log('🚫 Table click blocked');
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            });
            
            // Prevent clicks on table rows
            $('#progressWorkOrdersTable tbody, #closedWorkOrdersTable tbody').off('click').on('click', 'tr', function(e) {
                // console.log('🚫 Row click blocked');
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
        $('#workOrderFormTitle').text(typeof window.lang === 'function' ? window.lang('new_work_order') : 'New Work Order');
        $('#workOrderForm').attr('action', '<?= base_url('service/work-orders/store') ?>');
        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> ' + (typeof window.lang === 'function' ? window.lang('save') : 'Save') + ' ' + (typeof window.lang === 'function' ? window.lang('work_order') : 'Work Order'));
        
        // Reset custom dropdowns
        resetCustomDropdowns();
        
        // Clear form errors
        clearFormErrors();
        
        // Clear hidden work order ID
        $('#work_order_id').val('');
    });
    
    // Function to reset custom dropdowns
    function resetCustomDropdowns() {
        // Reset Unit (Select2 + hidden native select)
        $('#unitSelectedText').text('-- Select Unit --');
        const $uid = $('#unit_id');
        if ($uid.hasClass('select2-hidden-accessible')) {
            $uid.val(null).trigger('change');
        } else {
            $uid.val('');
        }
        $('#unitDropdownList').empty();
        
        // Reset Staff dropdowns
        const staffTypes = ['admin', 'foreman', 'mechanic', 'helper'];
        staffTypes.forEach(function(type) {
            $(`#${type}SelectedText`).text(`-- Select ${type.charAt(0).toUpperCase() + type.slice(1)} --`);
            $(`#${type}_staff_id`).val('');
            $(`#${type}DropdownList`).empty();
            $(`#${type}Search`).val('');
        });
        
        // Reset subcategory dropdown
        $('#subcategory_id').empty().append('<option value="">-- Select Subcategory (if any) --</option>');
        
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
        
        // Frontend validation - Complaint Description
        const complaintDesc = $('#complaint_description').val();
        if (!complaintDesc || complaintDesc.trim().length < 3) {
            OptimaNotify.error(typeof window.lang === 'function' ? window.lang('complaint_required_min') : 'Complaint Description must be at least 3 characters');
            $('#complaint_description').addClass('is-invalid').focus();
            return false;
        }
        $('#complaint_description').removeClass('is-invalid');
        
        let formData = new FormData(this);
        let url = $(this).attr('action');
        
        // CRITICAL: Ensure CSRF token is included (FormData should get it from form automatically via csrf_field())
        // Double-check and add if missing
        const csrfData = getCsrfTokenData();
        if (csrfData && !formData.has(csrfData.tokenName)) {
            formData.append(csrfData.tokenName, csrfData.tokenValue);
            // console.log('✅ CSRF token manually added to FormData:', csrfData.tokenName);
        }
        
        // DEBUG: Log all form data being sent
        // console.log('📤 Form data being submitted:');
        for (let pair of formData.entries()) {
            // console.log('  -', pair[0] + ':', pair[1]);
        }
        
        // Clear previous errors
        clearFormErrors();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#btnSubmitWo').prop('disabled', true).text('Saving...');
            },
            success: function(response) {
                // console.log('✅ Success response:', response);
                if (response.success) {
                    showAlert('success', response.message);
                    $('#workOrderModal').modal('hide');
                    reloadProgressTable();
                    updateStatistics();
                } else {
                    console.error('❌ Server returned success=false:', response);
                    showAlert('error', response.message);
                    if (response.errors) {
                        displayFormErrors(response.errors);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', error, 'Status:', status);
                console.error('❌ XHR Status Code:', xhr.status);
                console.error('❌ Response Text:', xhr.responseText);
                
                try {
                    let response = JSON.parse(xhr.responseText);
                    console.error('❌ Parsed Response:', response);
                    if (response.errors) {
                        console.error('❌ Validation Errors:', response.errors);
                        displayFormErrors(response.errors);
                    } else {
                        showAlert('error', response.message || 'An error occurred while saving data');
                    }
                } catch (e) {
                    console.error('❌ Could not parse response:', e);
                    showAlert('error', 'An error occurred while saving data: ' + error);
                }
            },
            complete: function() {
                $('#btnSubmitWo').prop('disabled', false).html('<i class="fas fa-save me-1"></i> ' + (typeof window.lang === 'function' ? window.lang('save') : 'Save') + ' ' + (typeof window.lang === 'function' ? window.lang('work_order') : 'Work Order'));
            }
        });
    });

    // Show Work Order Detail function
    function showWorkOrderDetail(id, woNumber) {
        if (!canViewService) {
            if (window.OptimaNotify) OptimaNotify.error('Access Denied: You do not have permission to view work order details.');
            else alert('Access Denied: You do not have permission to view work order details.');
            return;
        }
        
        $.ajax({
            url: '<?= base_url('service/work-orders/view') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
            },
            success: function(response) {
                // console.log('📦 Work Order Detail Data:', response.data);
                // console.log('🔧 Spareparts Data:', response.data.spareparts);
                
                if (response.success) {
                    hideAlert();
                    populateViewModal(response.data);
                    $('#viewWorkOrderModal').modal('show');
                } else {
                    showAlert('error', response.message || 'Failed to load data');
                }
            },
            error: function(xhr, status, error) {
                hideAlert();
                showAlert('error', 'An error occurred while loading data: ' + error);
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
        
        // console.log('🚀 Start Work clicked (btn-assign) for ID:', id, 'WO Number:', woNumber);
        
        // OptimaConfirm with print button in HTML
        OptimaConfirm.approve({
            title: 'Start Work?',
            html: `
                <p>Make sure the Work Order document and Unit Verification Form have been printed before starting the work.</p>
                <p><strong>NOTE:</strong> Unit Verification is mandatory and must be documented to complete the Work Order.</p>
                <div class="mt-3">
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.open('<?= base_url('service/work-orders/print') ?>/' + ${id}, '_blank')">
                        <i class="fas fa-print me-2"></i>Print Work Order
                    </button>
                </div>
            `,
            confirmText: 'Ya, Mulai',
            cancelText: (typeof window.lang === 'function' ? window.lang('cancel') : 'Batal'),
            onConfirm: function() {
                updateWorkOrderStatusDirect(id, 'IN_PROGRESS', 'Work order dimulai');
            }
        });
    });
    
    // Pause Work - Show single modal dengan dropdown + textarea keterangan
    $(document).on('click', '.btn-pause', function() {
        let id = $(this).data('id');
        let woNumber = $(this).data('wo-number');

        OptimaConfirm.generic({
            title: woNumber ? `Pause WO ${woNumber}` : 'Pause Work Order',
            icon: 'question',
            confirmText: 'Simpan',
            cancelText: (typeof window.lang === 'function' ? window.lang('cancel') : 'Batal'),
            confirmButtonColor: '#ffc107',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1">Alasan Pause</label>
                        <select id="optimaPauseTypeSelect" class="form-select">
                            <option value="ON_HOLD">Menunggu Konfirmasi Customer</option>
                            <option value="WAITING_PARTS">Menunggu Sparepart</option>
                            <option value="WAITING_SCHEDULE">Menunggu Jadwal / Schedule</option>
                            <option value="WAITING_PERMIT">Menunggu Izin / Permit Kerja</option>
                            <option value="WAITING_TOOLS">Menunggu Alat / Tools Khusus</option>
                            <option value="OTHER_HOLD">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold mb-1">Keterangan <span class="text-danger">*</span></label>
                        <textarea id="optimaPauseNotes" class="form-control" rows="3" placeholder="Jelaskan detail alasan pause..."></textarea>
                    </div>
                </div>
            `,
            onConfirm: function() {
                var typeEl  = document.getElementById('optimaPauseTypeSelect');
                var notesEl = document.getElementById('optimaPauseNotes');
                var val   = typeEl  ? typeEl.value             : 'ON_HOLD';
                var notes = notesEl ? notesEl.value.trim()     : '';

                if (!notes) {
                    OptimaNotify.warning('Keterangan wajib diisi', 'Validasi');
                    return false; // keep modal open
                }

                const csrfData = window.getCsrfTokenData();
                $.ajax({
                    url: '<?= base_url('service/work-orders/update-status') ?>',
                    type: 'POST',
                    data: {
                        [csrfData.tokenName]: csrfData.tokenValue,
                        id: id,
                        status: val,
                        notes: notes
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
                    error: function(xhr) {
                        showAlert('error', 'Gagal memperbarui status work order');
                    }
                });
            }
        });
    });
    
    // Resume Work
    $(document).on('click', '.btn-resume', function() {
        let id = $(this).data('id');
        updateWorkOrderStatus(id, 'IN_PROGRESS', 'Work order resumed');
    });
    
    // Complete Work - Open Complete Modal First
    $(document).on('click', '.btn-complete', function() {
        let id = $(this).data('id');
        let woNumber = $(this).data('wo-number');
        
        // Fallback: get WO number from the row if not in button
        if (!woNumber) {
            let row = $(this).closest('tr');
            woNumber = row.find('td:nth-child(2)').text().trim(); // Work order number is in 2nd column
            // console.log('🔄 Fallback WO number from row:', woNumber);
        }
        
        // console.log('🟢 Complete button clicked - Opening Complete Modal first');
        
        // Open Complete Work Order Modal (NOT Unit Verification directly)
        if (typeof window.openCompleteModal === 'function') {
            window.openCompleteModal(id, woNumber);
        } else {
            console.error('❌ openCompleteModal function not found');
            OptimaNotify.error('Failed to open Complete modal. Please refresh the page.');
        }
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
            showAlert('error', 'Error: Unable to open sparepart validation modal');
        }
    });
    
    // Reopen Work Order
    $(document).on('click', '.btn-reopen', function() {
        let id = $(this).data('id');
        updateWorkOrderStatus(id, 'PENDING', 'Work order reopened');
    });
    
    // Cancel Work Order
    $(document).on('click', '.btn-cancel', function() {
        let id = $(this).data('id');
        showStatusUpdateModal(id, 'CANCELLED', 'Cancel Work Order', 'Provide reason for cancellation');
    });
    
    // Reassign Work Order
    $(document).on('click', '.btn-reassign', function() {
        let id = $(this).data('id');
        showStatusUpdateModal(id, 'ASSIGNED', 'Reassign Work Order', 'Select new technician');
    });

    // Function to update work order status with confirmation
    function updateWorkOrderStatus(id, status, message) {
        // console.log('🚨 updateWorkOrderStatus called with:', { id, status, message, stack: new Error().stack });
        
        OptimaConfirm.generic({
            title: 'Confirmation',
            text: 'Are you sure you want to change the work order status?',
            icon: 'question',
            confirmText: 'Yes',
            cancelText: window.lang('cancel'),
            confirmButtonColor: 'primary',
            onConfirm: function() {
                updateWorkOrderStatusDirect(id, status, message);
            }
        });
    }
    
    // Function to update work order status directly without confirmation
    function updateWorkOrderStatusDirect(id, status, message) {
        // console.log('🚨 updateWorkOrderStatusDirect called with:', { id, status, message });
        const csrfData = window.getCsrfTokenData();
        $.ajax({
            url: '<?= base_url('service/work-orders/update-status') ?>',
            type: 'POST',
            data: {
                [csrfData.tokenName]: csrfData.tokenValue,
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
            error: function(xhr) {
                showAlert('error', 'Failed to update work order status');
            }
        });
    }
    
    // Function to show status update modal with notes
    function showStatusUpdateModal(id, status, title, placeholder) {
        OptimaConfirm.generic({
            title: title,
            icon: status === 'CANCELLED' ? 'warning' : 'question',
            confirmText: 'Update',
            cancelText: window.lang('cancel'),
            confirmButtonColor: 'primary',
            html: `
                <div class="text-start">
                    <label class="form-label">Notes</label>
                    <textarea id="optimaWorkOrderStatusNotes" class="form-control" rows="4" placeholder="${placeholder}"></textarea>
                </div>
            `,
            onConfirm: function() {
                var el = document.getElementById('optimaWorkOrderStatusNotes');
                var notes = el ? (el.value || '').trim() : '';
                var notesRequired = (status === 'CANCELLED' || status === 'ON_HOLD' || status === 'WAITING_PARTS'
                    || status === 'WAITING_SCHEDULE' || status === 'WAITING_PERMIT' || status === 'WAITING_TOOLS' || status === 'OTHER_HOLD');
                if (notesRequired && !notes) {
                    OptimaNotify.warning('Notes are required for this status', 'Validasi');
                    showStatusUpdateModal(id, status, title, placeholder);
                    return;
                }

                const csrfData2 = window.getCsrfTokenData();
                $.ajax({
                    url: '<?= base_url('service/work-orders/update-status') ?>',
                    type: 'POST',
                    data: {
                        [csrfData2.tokenName]: csrfData2.tokenValue,
                        id: id,
                        status: status,
                        notes: notes || ''
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
                    error: function(xhr) {
                        showAlert('error', 'Failed to update work order status');
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
            showAlert('error', 'Error: Unable to find work order ID for printing');
        }
    });

    // Edit from view modal
    $(document).on('click', '.btn-edit-from-view', function() {
        let id = $(this).data('id');
        
        if (!id) {
            console.error('❌ No work order ID found for editing');
            showAlert('error', 'Error: Unable to find work order ID for editing');
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
                    OptimaNotify.error(response.message || 'Failed to load work order data');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error loading work order for edit:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMessage = 'Failed to load work order data';
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
                
                OptimaNotify.error(errorMessage);
            }
        });
    });
    
    // Edit from DataTable action buttons
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        
        if (!id) {
            console.error('❌ No work order ID found for editing');
            showAlert('error', 'Error: Unable to find work order ID for editing');
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
                    OptimaNotify.error(response.message || 'Failed to load work order data');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error loading work order for edit from table:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMessage = 'Failed to load work order data';
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
                
                OptimaNotify.error(errorMessage);
            }
        });
    });
    
    // Delete from view modal
    $(document).on('click', '.btn-delete-from-view', function(e) {
        e.preventDefault();
        
        let id = $(this).data('id');
        let woNumber = $(this).data('wo-number');
        
        $('#viewWorkOrderModal').modal('hide');
        
        OptimaConfirm.danger({
            title: 'Delete Confirmation',
            text: `Are you sure you want to delete Work Order ${woNumber}?`,
            confirmText: 'Yes, Delete',
            cancelText: window.lang('cancel'),
            onConfirm: function() {
                // console.log('🗑️ Confirmed deletion, sending request...');
                $.ajax({
                    url: '<?= base_url('service/work-orders/delete') ?>/' + id,
                    type: 'DELETE',
                    data: {[window.csrfTokenName]: window.getCsrfToken()},
                    success: function(response) {
                        // console.log('✅ Delete response:', response);
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
                        showAlert('error', 'Failed to delete work order');
                    }
                });
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
        
        OptimaConfirm.danger({
            title: 'Delete Confirmation',
            text: `Are you sure you want to delete Work Order ${woNumber}?`,
            confirmText: 'Yes, Delete',
            cancelText: window.lang('cancel'),
            onConfirm: function() {
                $.ajax({
                    url: '<?= base_url('service/work-orders/delete') ?>/' + id,
                    type: 'DELETE',
                    data: {[window.csrfTokenName]: window.getCsrfToken()},
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
                            showAlert('error', response.message || 'Failed to delete work order');
                        } catch (e) {
                            showAlert('error', 'Failed to delete work order');
                        }
                    }
                });
            }
        });
    });

    // Category change handler for subcategory
    $('#category_id').on('change', function() {
        let categoryId = $(this).val();
        let subcategorySelect = $('#subcategory_id');
        
        // Clear and reset subcategory dropdown
        subcategorySelect.empty().append('<option value="">-- Select Subcategory (if any) --</option>');
        
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
                        // console.log('✅ Subcategories loaded:', response.data.length, 'items');
                        
                        // Trigger Select2 update
                        if (subcategorySelect.hasClass('select2-hidden-accessible')) {
                            subcategorySelect.trigger('change');
                        }
                    } else {
                        // console.log('ℹ️ No subcategories found for category:', categoryId);
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
        // console.log('🔄 Populating edit form with data:', data);
        
        try {
            // Extract work order data from nested structure
            let workOrder = data.workOrder || data;
            // console.log('📋 Work Order data:', workOrder);
            
            // Basic form fields
            if (workOrder.id) $('#work_order_id').val(workOrder.id);
            if (workOrder.work_order_number) $('#work_order_number').val(workOrder.work_order_number);
            if (workOrder.order_type) $('#order_type').val(workOrder.order_type).trigger('change');
            if (workOrder.category_id) $('#category_id').val(workOrder.category_id).trigger('change');
            if (workOrder.area) $('#area').val(workOrder.area);
            if (workOrder.complaint_description) $('#complaint_description').val(workOrder.complaint_description);
            
            // Work details fields
            if (workOrder.repair_description) $('#repair_description').val(workOrder.repair_description);
            if (workOrder.sparepart_used) $('#sparepart_used').val(workOrder.sparepart_used);
            if (workOrder.notes) $('#notes').val(workOrder.notes);
            
            // console.log('✅ Basic fields populated');
            
            // Handle Unit selection with Select2
            if (workOrder.unit_id) {
                // console.log('🏢 Setting unit ID:', workOrder.unit_id);
                
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
                    // console.log('✅ Unit option added:', unitText);
                } else if (unitExists) {
                    unitSelect.val(workOrder.unit_id);
                    // console.log('✅ Unit selected from existing options');
                }
                
                // Trigger Select2 update
                unitSelect.trigger('change');
            }
            
            // Handle Category and Subcategory with Select2
            if (workOrder.category_id) {
                // console.log('📂 Setting category ID:', workOrder.category_id);
                $('#category_id').val(workOrder.category_id).trigger('change');
                
                // Load subcategories if category is selected
                if (workOrder.subcategory_id && data.subcategories) {
                    setTimeout(function() {
                        let subcategorySelect = $('#subcategory_id');
                        subcategorySelect.empty().append('<option value="">-- Select Subcategory (if any) --</option>');
                        
                        if (data.subcategories && data.subcategories.length > 0) {
                            data.subcategories.forEach(function(subcategory) {
                                let selected = subcategory.id == workOrder.subcategory_id ? 'selected' : '';
                                subcategorySelect.append(`<option value="${subcategory.id}" ${selected}>${subcategory.subcategory_name}</option>`);
                            });
                            subcategorySelect.trigger('change');
                            // console.log('✅ Subcategories populated, selected:', workOrder.subcategory_id);
                        }
                    }, 500); // Allow time for category change to trigger subcategory load
                }
            }
            
            // Handle Mechanic selections with Select2
            if (workOrder.mechanic_1 || workOrder.mechanic_id) {
                let mechanicId = workOrder.mechanic_1 || workOrder.mechanic_id;
                // console.log('🔧 Setting mechanic 1 ID:', mechanicId);
                
                let mechanicSelect = $('#mechanic_1');
                let mechanicExists = mechanicSelect.find(`option[value="${mechanicId}"]`).length > 0;
                
                if (!mechanicExists && data.mechanics) {
                    let mechanic = data.mechanics.find(m => m.id == mechanicId);
                    if (mechanic) {
                        let mechanicText = mechanic.staff_name || mechanic.name || `Mechanic ${mechanicId}`;
                        mechanicSelect.append(`<option value="${mechanicId}" selected>${mechanicText}</option>`);
                        // console.log('✅ Mechanic 1 option added:', mechanicText);
                    }
                } else if (mechanicExists) {
                    mechanicSelect.val(mechanicId);
                }
                mechanicSelect.trigger('change');
            }
            
            if (workOrder.mechanic_2) {
                // console.log('🔧 Setting mechanic 2 ID:', workOrder.mechanic_2);
                
                let mechanicSelect = $('#mechanic_2');
                let mechanicExists = mechanicSelect.find(`option[value="${workOrder.mechanic_2}"]`).length > 0;
                
                if (!mechanicExists && data.mechanics) {
                    let mechanic = data.mechanics.find(m => m.id == workOrder.mechanic_2);
                    if (mechanic) {
                        let mechanicText = mechanic.staff_name || mechanic.name || `Mechanic ${workOrder.mechanic_2}`;
                        mechanicSelect.append(`<option value="${workOrder.mechanic_2}" selected>${mechanicText}</option>`);
                        // console.log('✅ Mechanic 2 option added:', mechanicText);
                    }
                } else if (mechanicExists) {
                    mechanicSelect.val(workOrder.mechanic_2);
                }
                mechanicSelect.trigger('change');
            }
            
            // Handle Helper selections with Select2
            if (workOrder.helper_1 || workOrder.helper_id) {
                let helperId = workOrder.helper_1 || workOrder.helper_id;
                // console.log('🛠️ Setting helper 1 ID:', helperId);
                
                let helperSelect = $('#helper_1');
                let helperExists = helperSelect.find(`option[value="${helperId}"]`).length > 0;
                
                if (!helperExists && data.helpers) {
                    let helper = data.helpers.find(h => h.id == helperId);
                    if (helper) {
                        let helperText = helper.staff_name || helper.name || `Helper ${helperId}`;
                        helperSelect.append(`<option value="${helperId}" selected>${helperText}</option>`);
                        // console.log('✅ Helper 1 option added:', helperText);
                    }
                } else if (helperExists) {
                    helperSelect.val(helperId);
                }
                helperSelect.trigger('change');
            }
            
            if (workOrder.helper_2) {
                // console.log('🛠️ Setting helper 2 ID:', workOrder.helper_2);
                
                let helperSelect = $('#helper_2');
                let helperExists = helperSelect.find(`option[value="${workOrder.helper_2}"]`).length > 0;
                
                if (!helperExists && data.helpers) {
                    let helper = data.helpers.find(h => h.id == workOrder.helper_2);
                    if (helper) {
                        let helperText = helper.staff_name || helper.name || `Helper ${workOrder.helper_2}`;
                        helperSelect.append(`<option value="${workOrder.helper_2}" selected>${helperText}</option>`);
                        // console.log('✅ Helper 2 option added:', helperText);
                    }
                } else if (helperExists) {
                    helperSelect.val(workOrder.helper_2);
                }
                helperSelect.trigger('change');
            }
            
            // Handle Priority
            if (workOrder.priority_id) {
                // console.log('⚠️ Setting priority ID:', workOrder.priority_id);
                $('#priority_id').val(workOrder.priority_id);
            }
            
            // Handle Admin and Foreman
            if (workOrder.admin_id) {
                // console.log('👔 Setting admin ID:', workOrder.admin_id);
                // Wait for dropdown to be loaded first
                setTimeout(function() {
                    $('#admin_id').val(workOrder.admin_id).trigger('change');
                }, 1500);
            }
            
            if (workOrder.foreman_id) {
                // console.log('👷 Setting foreman ID:', workOrder.foreman_id);
                setTimeout(function() {
                    $('#foreman_id').val(workOrder.foreman_id).trigger('change');
                }, 1500);
            }
            
            // Handle PIC
            if (workOrder.pic) {
                // console.log('👤 Setting PIC:', workOrder.pic);
                $('#pic').val(workOrder.pic);
            }
            
            // Handle spareparts if they exist
            // console.log('🔧 Checking spareparts data:', data.spareparts);
            if (data.spareparts && data.spareparts.length > 0) {
                // console.log('🔧 Populating spareparts:', data.spareparts);
                // Clear existing sparepart rows
                $('#sparepartTableBody').empty();
                sparepartRowCount = 0; // Reset counter
                
                // Add sparepart rows with proper timing
                setTimeout(function() {
                    data.spareparts.forEach(function(sparepart, index) {
                        // console.log(`🔧 Adding sparepart row ${index + 1}:`, sparepart);
                        addSparepartRow(sparepart);
                    });
                    
                    // console.log('✅ All sparepart rows added, total:', data.spareparts.length);
                }, 200);
            } else {
                // console.log('📝 No spareparts data, adding empty row');
                // Clear existing sparepart rows
                $('#sparepartTableBody').empty();
                sparepartRowCount = 0; // Reset counter
                
                // Add one empty row
                setTimeout(function() {
                    addSparepartRow();
                }, 200);
            }
            
            // console.log('✅ Edit form populated successfully');
            
        } catch (error) {
            console.error('❌ Error populating edit form:', error);
            OptimaNotify.error('An error occurred while populating the edit form: ' + error.message);
        }
    }

    function populateViewModal(data) {
        // Debug: Log the data structure to understand what we're receiving
        // console.log('Work Order Detail Data:', data);
        // console.log('Accessories Data:', data.unit_accessories || data.accessories);
        
        // Update modal header with work order number
        $('#viewWoNumberHeader').text(data.work_order_number || '-');
        
        // Work Order Information
        $('#viewWoNumber').text(data.work_order_number || '-');
        $('#viewWoReportDate').text(data.report_date || '-');
        const orderTypeLabels = { COMPLAINT: 'Complaint', PMPS: 'PM/PS', REKONDISI: 'Rekondisi', PERSIAPAN: 'Persiapan', FABRIKASI: 'Rekondisi' };
        $('#viewWoType').text(orderTypeLabels[data.order_type] || data.order_type || '-');
        
        // Fix Priority Badge - Optima badge-soft-* system
        let priorityBadge = data.priority_badge || '<span class="badge badge-soft-gray">-</span>';
        if (!priorityBadge.includes('badge')) {
            priorityBadge = `<span class="badge badge-soft-blue">${priorityBadge}</span>`;
        }
        $('#viewWoPriority').html(priorityBadge);
        
        $('#viewWoCategory').text(data.category_name || '-');
        $('#viewWoDepartemen').html(data.unit_departemen ? `<span class="badge badge-soft-cyan">${data.unit_departemen}</span>` : '<span class="badge badge-soft-gray">-</span>');
        $('#viewWoStatus').html(data.status_badge || '<span class="badge badge-soft-gray">-</span>');
        $('#viewWoArea').text(data.area || '-');
        $('#viewWoTTR').text(data.time_to_repair ? data.time_to_repair + ' jam' : '-');
        $('#viewWoCompletionDate').text(data.completion_date || 'Belum selesai');
        
        // Unit Details  
        $('#viewUnitNumber').text(data.unit_number || '-');
        $('#viewUnitModel').text((data.unit_brand && data.model_unit) ? data.unit_brand + ' ' + data.model_unit : '-');
        $('#viewUnitType').text(data.unit_type || '-');
        $('#viewUnitDepartemen').html(data.unit_departemen ? `<span class="badge badge-soft-cyan">${data.unit_departemen}</span>` : '<span class="badge badge-soft-gray">-</span>');
        $('#viewUnitSerial').text(data.unit_serial || '-');
        $('#viewUnitLocation').text(data.unit_location || '-');
        $('#viewUnitCustomer').text(data.unit_customer || '-');
        $('#viewUnitStatus').html(data.unit_status ? `<span class="badge badge-soft-green">${data.unit_status}</span>` : '<span class="badge badge-soft-gray">-</span>');
        
        // Additional Unit Details
        $('#viewUnitCapacity').text(data.unit_capacity || '-');
        $('#viewUnitYear').text(data.unit_year || '-');
        
        // Hour Meter with formatting
        if (data.hour_meter) {
            $('#viewUnitHourMeter').text(parseFloat(data.hour_meter).toLocaleString() + ' hours');
        } else {
            $('#viewUnitHourMeter').text('-');
        }
        
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
            '<div class="text-muted fst-italic">Not available</div>');
            
        $('#viewWoRepair').html(data.repair_description ? 
            `<div class="text-dark">${data.repair_description}</div>` : 
            '<div class="text-muted fst-italic">Not available</div>');
            
        $('#viewWoSparepart').html(data.sparepart_used ? 
            `<div class="text-dark">${data.sparepart_used}</div>` : 
            '<div class="text-muted fst-italic">Not available</div>');
            
        $('#viewWoNotes').html(data.notes ? 
            `<div class="text-dark">${data.notes}</div>` : 
            '<div class="text-muted fst-italic">Not available</div>');
        
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
            container.html('<em class="text-muted">Not available</em>');
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
            container.html('<em class="text-muted">Not available</em>');
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
            container.html('<em class="text-muted">Not available</em>');
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
                    subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
                    
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
        
        // console.log('Raw accessories data:', accessories, typeof accessories);
        
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
            // console.log('Accessories data is neither array nor string:', accessories);
            container.text('-');
            $('#unitAccessoriesInline').hide();
            return;
        }
        
        // console.log('Processed accessories array:', accessoriesArray);
        
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
            
            // Display as simple text
            if (accessoryNames.length > 0) {
                container.text(accessoryNames.join(', '));
                $('#unitAccessoriesInline').show();
            } else {
                container.text('-');
                $('#unitAccessoriesInline').hide();
            }
        } else {
            container.text('-');
            $('#unitAccessoriesInline').hide();
        }
    }

    function populateSparepartBrought(spareparts) {
        const container = $('#viewSparepartBroughtList');
        const section = $('#sparepartBroughtSection');
        
        // console.log('🔧 Raw spareparts data:', spareparts, typeof spareparts);
        
        if (spareparts && spareparts.length > 0) {
            let html = '';
            spareparts.forEach(function(sparepart, index) {
                /* DISABLED DEBUG LOG
                console.log('  📦 Item ' + (index + 1) + ':', {
                    name: sparepart.name,
                    item_type: sparepart.item_type,
                    is_from_warehouse: sparepart.is_from_warehouse,
                    is_used: sparepart.is_used,
                    used_quantity: sparepart.used_quantity,
                    notes: sparepart.notes
                });
                */
                
                const qtyBrought = (sparepart.qty || sparepart.quantity_brought || 0) + ' ' + (sparepart.satuan || 'pcs');
                const qtyUsed = sparepart.used_quantity || '-';
                
                // Item Type Badge (Optima badge-soft-*)
                const itemType = sparepart.item_type || 'sparepart';
                let typeBadge = '';
                if (itemType === 'tool') {
                    typeBadge = '<span class="badge badge-soft-gray">Tool</span>';
                } else {
                    typeBadge = '<span class="badge badge-soft-blue">Sparepart</span>';
                }
                
                // Item name with source indicator
                let itemName = sparepart.name || sparepart.desc_sparepart || sparepart.sparepart_name || '-';
                const isFromWarehouse = sparepart.is_from_warehouse !== undefined ? parseInt(sparepart.is_from_warehouse) : 1;
                if (isFromWarehouse === 0) {
                    itemName += ' <span class="badge badge-soft-yellow">Non-WH</span>';
                }
                
                // Determine usage status badge
                let statusBadge = '<span class="badge badge-soft-gray">Pending</span>';
                if (sparepart.is_used !== undefined && sparepart.is_used !== null) {
                    if (sparepart.is_used == 1 || sparepart.is_used === true) {
                        statusBadge = '<span class="badge badge-soft-green"><i class="fas fa-check me-1"></i>Used</span>';
                    } else if (sparepart.is_used == 0 || sparepart.is_used === false) {
                        statusBadge = '<span class="badge badge-soft-yellow"><i class="fas fa-undo me-1"></i>Returned</span>';
                    }
                }
                
                // console.log(`    ➡️ Type: ${itemType}, Status: ${statusBadge}`);
                
                html += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center">${typeBadge}</td>
                        <td>${itemName}</td>
                        <td class="font-monospace">${sparepart.code || sparepart.kode || sparepart.sparepart_code || '-'}</td>
                        <td class="text-center">${qtyBrought}</td>
                        <td class="text-center">${qtyUsed}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td><small>${sparepart.usage_notes || sparepart.notes || '-'}</small></td>
                    </tr>
                `;
            });
            container.html(html);
            section.show();
        } else {
            container.html('<tr><td colspan="8" class="text-center text-muted">Not available</td></tr>');
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
        // console.log('📊 Updating statistics...');
        $.ajax({
            url: '<?= base_url('service/work-orders/stats') ?>',
            type: 'GET',
            success: function(response) {
                // console.log('📊 Statistics response:', response);
                if (response.status) {  // Backend menggunakan 'status' bukan 'success'
                    $('#stat-total-work-orders').text(response.data.total_work_orders || 0);
                    $('#stat-open').text(response.data.open_work_orders || 0);
                    $('#stat-in-progress').text(response.data.in_progress_work_orders || 0);
                    $('#stat-completed').text(response.data.completed_work_orders || 0);
                    // console.log('📊 Statistics updated successfully');
                } else {
                    // console.log('❌ Failed to update statistics:', response.message);
                }
            },
            error: function(xhr, status, error) {
                // console.log('❌ Error updating statistics:', error);
                // console.log('❌ XHR:', xhr.responseText);
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
        // console.log('🆕 Opening new work order modal');
        // Auto generate WO number when opening modal
        generateWorkOrderNumber();
        
        // Ensure form is set for create mode
        var saveText = (typeof window.lang === 'function' ? window.lang('save') : 'Save') + ' ' + (typeof window.lang === 'function' ? window.lang('work_order') : 'Work Order');
        $('#workOrderFormTitle').html('<i class="fas fa-plus-circle me-2"></i>' + (typeof window.lang === 'function' ? window.lang('new_work_order') : 'New Work Order'));
        $('#workOrderForm').attr('action', '<?= base_url('service/work-orders/store') ?>');
        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> ' + saveText);
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
                // console.log('🔢 WO number generated:', response);
                if (response.success) {
                    $('#work_order_number').val(response.work_order_number);
                } else {
                    // console.log('❌ Failed to generate WO number:', response.message);
                }
            },
            error: function(xhr, status, error) {
                // console.log('❌ Error generating work order number:', error);
            }
        });
    }

    // Load Unit Verification Data - Defined in unit_verification.php (included at bottom)
    // No wrapper needed - window.loadUnitVerificationData is globally available
    
    // Unit picker: hanya #unit_id + Select2 (loadUnitsDropdown). Legacy #unit_search dihapus.
    $(document).on('click', function() {
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
        // console.log('📌 Subcategory changed:', subcategoryId);
        if (subcategoryId) {
            const csrfData = getCsrfTokenData();
            // Get priority for subcategory
            $.ajax({
                url: '<?= base_url('service/work-orders/get-subcategory-priority') ?>',
                type: 'POST',
                data: { 
                    subcategory_id: subcategoryId,
                    [csrfData.tokenName]: csrfData.tokenValue
                },
                success: function(response) {
                    // console.log('✅ Priority response:', response);
                    if (response.success && response.priority_id) {
                        setPriority(response.priority_id);
                    } else {
                        console.warn('⚠️ No priority found for subcategory');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error getting priority:', error);
                }
            });
        } else {
            // console.log('⚠️ No subcategory selected, clearing priority');
            $('#priority_id').val('');
            $('#priority_display').val('');
        }
    });

    function setPriority(priorityId) {
        // console.log('🎯 Setting priority:', priorityId);
        const csrfData = getCsrfTokenData();
        // Find priority name and set display
        $.ajax({
            url: '<?= base_url('service/work-orders/get-priority') ?>',
            type: 'POST',
            data: { 
                priority_id: priorityId,
                [csrfData.tokenName]: csrfData.tokenValue
            },
            success: function(response) {
                // console.log('✅ Priority details:', response);
                if (response.success) {
                    $('#priority_id').val(priorityId);
                    $('#priority_display').val(response.priority_name);
                    // console.log('✅ Priority set:', priorityId, '-', response.priority_name);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error getting priority details:', error);
                // Set anyway
                $('#priority_id').val(priorityId);
                $('#priority_display').val('Priority ID: ' + priorityId);
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
                resultDiv.html('<div class="list-group-item"><i class="fas fa-spinner fa-spin"></i> Searching...</div>').show();
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
                    resultDiv.html('<div class="list-group-item text-muted">No staff found</div>').show();
                }
            },
            error: function() {
                resultDiv.html('<div class="list-group-item text-danger">Error searching staff</div>').show();
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
        // console.log('📋 Modal shown, loading data...');
        
        // Load unit dropdown first - it will handle its own Select2 initialization
        // DO NOT destroy unit_id here - let loadUnitsDropdown() handle it completely
        loadUnitsDropdown();
        
        // Initialize staff dropdowns with department filtering
        if (window.currentUserDepartment && window.currentUserDepartment.ids && window.currentUserDepartment.ids.length > 0) {
            console.log(`🔐 Filtering staff by department: ${window.currentUserDepartment.name} (IDs: ${window.currentUserDepartment.ids})`);
            
            // Load all staff filtered by department scope (backend auto-applies scope)
            loadStaffByDepartmentScope('ADMIN', 'admin_id');
            loadStaffByDepartmentScope('FOREMAN', 'foreman_id');
            loadStaffByDepartmentScope('MECHANIC', 'mechanic_1');
            loadStaffByDepartmentScope('MECHANIC', 'mechanic_2');
            loadStaffByDepartmentScope('HELPER', 'helper_1');
            loadStaffByDepartmentScope('HELPER', 'helper_2');
        } else {
            console.log('ℹ️ No department filtering - loading ALL staff with Select2');
            // Load ALL staff (Admin, Foreman, Mechanic, Helper) with Select2
            loadAllStaffFallback();
        }
        
        // Initialize Select2 for other dropdowns (NOT unit_id and NOT sparepart - they handle themselves)
        // Use longer delay to ensure unit and sparepart dropdowns are initialized first
        setTimeout(function() {
            // console.log('🔄 Initializing Select2 for other dropdowns (excluding unit_id and sparepart)');
            initializeSelect2();
        }, 800); // Increased delay to ensure unit and sparepart dropdowns are initialized first
        
        // Add initial sparepart row if not exists - with proper timing
        // Wait for sparepartsData to be available and ensure it's loaded
        setTimeout(function() {
            if ($('#sparepartTableBody tr').length === 0) {
                // console.log('🔧 Adding initial sparepart row');
                // console.log('📦 SparepartsData available:', window.sparepartsData ? window.sparepartsData.length : 0, 'items');
                
                // Ensure sparepartsData is available
                if (!window.sparepartsData || !Array.isArray(window.sparepartsData) || window.sparepartsData.length === 0) {
                    console.warn('⚠️ SparepartsData not available yet, waiting...');
                    // Retry after a bit more delay
                    setTimeout(function() {
                        if ($('#sparepartTableBody tr').length === 0) {
                            // console.log('🔧 Retrying to add initial sparepart row');
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
            $('#workOrderFormTitle').html('<i class="fas fa-plus-circle me-2"></i>' + (typeof window.lang === 'function' ? window.lang('new_work_order') : 'New Work Order'));
            
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
        
        // console.log('✅ Select2 library is available');
        
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
                return $(this).data('placeholder') || '-- Select --';
            },
            language: {
                noResults: function() {
                    return "No results found";
                },
                searching: function() {
                    return "Searching...";
                }
            }
        };

        // Initialize searchable dropdowns (Unit and Sparepart only)
        const searchableSelectors = [
            { id: '#unit_id', placeholder: '-- Select Unit --', searchable: true }
        ];

        // Initialize regular dropdowns (non-searchable) - Clean appearance
        const regularSelectors = [
            { id: '#category_id', placeholder: '-- Select Category --' },
            { id: '#subcategory_id', placeholder: '-- Select Subcategory --' },
            { id: '#order_type', placeholder: '-- Select Order Type --' },
            { id: '#mechanic_1', placeholder: '-- Select Mechanic 1 --' },
            { id: '#mechanic_2', placeholder: '-- Select Mechanic 2 (Optional) --' },
            { id: '#helper_1', placeholder: '-- Select Helper 1 --' },
            { id: '#helper_2', placeholder: '-- Select Helper 2 (Optional) --' }
        ];

        // Initialize searchable dropdowns - Only for fields that really need search
        searchableSelectors.forEach(function(config) {
            // CRITICAL: unit_id is handled separately in loadUnitsDropdown() - NEVER touch it here
            if (config.id === '#unit_id') {
                // console.log('⏭️ Skipping unit_id completely - managed by loadUnitsDropdown()');
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
            // console.log('🔍 Checking element:', config.id, 'Found:', $element.length, 'Has Select2:', $element.hasClass('select2-hidden-accessible'));
            if ($element.length && !$element.hasClass('select2-hidden-accessible')) {
                // console.log('✅ Initializing Select2 for:', config.id);
                try {
                    $element.select2({
                ...modalConfig,
                        placeholder: config.placeholder,
                        minimumResultsForSearch: Infinity, // Disable search for clean appearance
                        allowClear: false,
                        width: '100%'
                    });
                    // console.log('✅ Successfully initialized Select2 for:', config.id);
                } catch (e) {
                    console.error('❌ Error initializing Select2 for:', config.id, e);
                }
            }
        });

        // Handle spareparts separately for multiple selection with search
        if ($('#spareparts').length && !$('#spareparts').hasClass('select2-hidden-accessible')) {
            $('#spareparts').select2({
                ...searchableConfig,
                placeholder: '-- Select Sparepart --',
                multiple: true
            });
        }
        
        // We now initialize Select2 for sparepart dropdowns with proper styling
        
    }

    // Handle unit change to auto-fill area
    $(document).on('change', '#unit_id', function() {
        const unitId = $(this).val();
        if (unitId) {
            // Try Select2 AJAX data first (has full unit object), fallback to allUnits cache
            const s2data = $(this).select2('data');
            const unit = (s2data && s2data[0] && s2data[0].unit)
                ? s2data[0].unit
                : (window.allUnits || []).find(function(u) { return u.id == unitId; });
            if (unit && unit.area_name) {
                // CASE 1: Unit has area - filter staff by area
                $('#area').val(unit.area_name);
                $('#area_id').val(unit.area_id);
                
                // Show area info indicator
                showAreaIndicator(unit.area_name, true);
                
                // Load staff based on user login scope (not unit area)
                loadAreaStaff();
            } else {
                // CASE 2: Unit has NO area
                $('#area').val('N/A');
                $('#area_id').val('');
                
                // Show fallback indicator
                showAreaIndicator(null, false);
                
                // Load staff based on user login scope
                loadAreaStaff();
            }
        } else {
            // Clear area and staff fields if no unit selected
            $('#area').val('');
            $('#area_id').val('');
            hideAreaIndicator();
            $('#admin_id').val('').trigger('change');
            $('#foreman_id').val('').trigger('change');
            $('#mechanic_1, #mechanic_2').val('').trigger('change');
            $('#helper_1, #helper_2').val('').trigger('change');
        }
    });
    
    // Prevent duplicate mechanic selection
    $(document).on('change', '#mechanic_1, #mechanic_2', function() {
        const mechanic1 = $('#mechanic_1').val();
        const mechanic2 = $('#mechanic_2').val();
        
        if (mechanic1 && mechanic2 && mechanic1 === mechanic2) {
            OptimaNotify.warning('Tidak dapat memilih mekanik yang sama untuk Mechanic 1 dan Mechanic 2!');
            // Clear the dropdown that was just changed
            $(this).val('').trigger('change');
        }
    });

    /**
     * Sync selected Foreman as an option in mechanic dropdowns.
     * When a foreman is selected, their name appears in mechanic_1/mechanic_2
     * so the foreman can also act as a mechanic without needing a separate mechanic.
     * The foreman option is marked with data-foreman="1" for easy removal on deselect.
     */
    function syncForemanToMechanicDropdowns() {
        const foremanId   = $('#foreman_id').val();
        const foremanName = $('#foreman_id').find('option:selected').text().trim();
        const FOREMAN_ATTR = 'data-foreman';

        // Remove any previously-injected foreman option from both mechanic dropdowns
        $('#mechanic_1, #mechanic_2').each(function() {
            const $sel = $(this);
            const currentVal = $sel.val();
            $sel.find('option[' + FOREMAN_ATTR + ']').remove();

            // If the formerly-selected value was the foreman option, clear it
            if (currentVal && currentVal === $sel.data('foreman-id-prev')) {
                $sel.val('').trigger('change.select2');
            }

            // Refresh Select2 display
            if ($sel.hasClass('select2-hidden-accessible')) {
                $sel.trigger('change.select2');
            }
        });

        if (!foremanId || foremanId === '0') {
            $('#mechanic_1, #mechanic_2').removeData('foreman-id-prev');
            return;
        }

        // Store current foreman id for cleanup on next change
        $('#mechanic_1, #mechanic_2').data('foreman-id-prev', foremanId);

        const optionHtml = `<option value="${foremanId}" ${FOREMAN_ATTR}="1">${foremanName} (Foreman)</option>`;

        ['mechanic_1', 'mechanic_2'].forEach(function(id) {
            const $sel = $('#' + id);
            if (!$sel.length) return;

            // Insert right after the placeholder option (index 0)
            $sel.find('option:first').after(optionHtml);

            // Refresh Select2
            if ($sel.hasClass('select2-hidden-accessible')) {
                $sel.trigger('change.select2');
            }
        });
    }

    // Wire up: call syncForemanToMechanicDropdowns whenever foreman_id changes
    $(document).on('change', '#foreman_id', function() {
        syncForemanToMechanicDropdowns();
    });
    
    // Prevent duplicate helper selection
    $(document).on('change', '#helper_1, #helper_2', function() {
        const helper1 = $('#helper_1').val();
        const helper2 = $('#helper_2').val();
        
        if (helper1 && helper2 && helper1 === helper2) {
            OptimaNotify.warning('Tidak dapat memilih helper yang sama untuk Helper 1 dan Helper 2!');
            // Clear the dropdown that was just changed
            $(this).val('').trigger('change');
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
    // XHR tracker: abort stale requests before populating the same dropdown again
    window._staffXhr = {};

    // Units Dropdown Management
    function loadUnitsDropdown() {
        const unitSelect = $('#unit_id');

        // Destroy existing Select2 instance if present
        if (unitSelect.hasClass('select2-hidden-accessible')) {
            unitSelect.select2('destroy');
        }
        unitSelect.empty().append('<option value="">-- Select Unit --</option>');

        const O2 = window.OptimaUnitSelect2;
        const s2cfg = {
            placeholder: '-- Select Unit --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#workOrderModal'),
            minimumInputLength: 1,
            language: {
                noResults:     function() { return "Unit tidak ditemukan"; },
                searching:     function() { return "Mencari..."; },
                inputTooShort: function() { return "Ketik minimal 1 karakter untuk mencari unit"; }
            },
            ajax: {
                url: '<?= base_url('service/work-orders/units-dropdown') ?>',
                dataType: 'json',
                delay: 350,
                data: function(params) {
                    return { search: params.term };
                },
                processResults: function(data) {
                    if (!data.success || !data.data) return { results: [] };

                    return {
                        results: data.data.map(function(unit) {
                            const label = (O2 && typeof O2.line1FromRow === 'function')
                                ? O2.line1FromRow(O2.normalizeRow(unit))
                                : [unit.no_unit, unit.jenis, unit.kapasitas, unit.status ? '[' + unit.status + ']' : ''].filter(Boolean).join(' - ');

                            // Cache for area auto-fill when unit is selected
                            if (!window.allUnits) window.allUnits = [];
                            const idx = window.allUnits.findIndex(function(u) { return u.id == unit.id; });
                            if (idx >= 0) window.allUnits[idx] = unit; else window.allUnits.push(unit);

                            return { id: unit.id, text: label, unit: unit };
                        })
                    };
                },
                cache: true
            }
        };

        if (O2 && typeof O2.templateResult === 'function') {
            s2cfg.templateResult = function(item) {
                if (!item.unit) return item.text;
                return O2.templateResult(item.unit, {});
            };
            s2cfg.templateSelection = function(item) {
                if (!item.unit) return item.text;
                return O2.templateSelection(item.unit, {});
            };
        }

        unitSelect.select2(s2cfg);
    }
    function displayUnits(units) {
        let unitList = $('#unitDropdownList');
        unitList.empty();
        
        units.forEach(function(unit) {
            // Format: "1 - Counter Balance - 3.5 TON - [STATUS] (Kontrak / Lokasi)"
            const jenis = unit.jenis || unit.unit_type || 'N/A';
            const kapasitas = unit.kapasitas || 'N/A';
            const status = unit.status || 'N/A';
            const pelanggan = unit.pelanggan || 'Belum Ada Kontrak';
            const lokasi = unit.lokasi || 'N/A';
            
            let displayName = `${unit.no_unit} - ${jenis} - ${kapasitas} - [${status}] (${pelanggan} / ${lokasi})`;
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
            // Search across all relevant fields
            const searchableText = [
                unit.no_unit,
                unit.pelanggan,
                unit.lokasi,
                unit.jenis,
                unit.kapasitas,
                unit.status,
                unit.merk_unit,
                unit.model_unit
            ].filter(Boolean).join(' ').toLowerCase();
            
            return searchableText.includes(searchTerm);
        });
        displayUnits(filteredUnits);
    }

    // Staff Dropdown Management
    function loadStaffDropdownByArea(staffRole, targetId, areaId) {
        // console.log(`🔄 Loading ${staffRole} for ${targetId}, area: ${areaId}`);
        
        $.ajax({
            url: '<?= base_url('service/work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: { 
                staff_role: staffRole,
                area_id: areaId // Filter by area
            },
            success: function(response) {
                // console.log(`📦 ${staffRole} response for ${targetId}:`, response);
                
                if (response.success && response.data) {
                    const staffSelect = $('#' + targetId);
                    
                    // Clear existing options and add placeholder
                    let placeholderText = staffRole === 'MECHANIC' ? 
                        (targetId === 'mechanic_1' ? '-- Select Mechanic 1 --' : '-- Select Mechanic 2 (Optional) --') :
                        (targetId === 'helper_1' ? '-- Select Helper 1 --' : '-- Select Helper 2 (Optional) --');
                    
                    staffSelect.empty().append(`<option value="" selected ${targetId.endsWith('_2') ? '' : 'disabled'}>${placeholderText}</option>`);
                    
                    // Add staff options
                    response.data.forEach(function(staff) {
                        let staffName = staff.staff_name || staff.name || 'Unknown Staff';
                        let staffCode = staff.staff_code || staff.employee_code || '';
                        let optionText = staffCode ? `${staffName} (${staffCode})` : staffName;
                        
                        staffSelect.append(`<option value="${staff.id}">${optionText}</option>`);
                    });
                    
                    // console.log(`✅ ${staffRole} loaded: ${response.data.length} items for ${targetId}`);
                } else {
                    console.error(`❌ No ${staffRole} staff found for area ${areaId}`);
                    const staffSelect = $('#' + targetId);
                    let placeholderText = `No ${staffRole.toLowerCase()} assigned to this area`;
                    staffSelect.empty().append(`<option value="" selected disabled>${placeholderText}</option>`);
                }
            },
            error: function(xhr, status, error) {
                console.error(`❌ AJAX Error loading ${staffRole}:`, error);
                console.error('❌ Response:', xhr.responseText);
            }
        });
    }
    
    function loadStaffDropdown(staffRole, targetId) {
        // Abort any in-flight request for this dropdown to prevent race-condition duplicates
        if (window._staffXhr[targetId]) {
            window._staffXhr[targetId].abort();
            window._staffXhr[targetId] = null;
        }
        
        // Build data object with CSRF token
        const ajaxData = { 
            staff_role: staffRole
        };
        ajaxData[window.csrfTokenName] = window.csrfTokenValue;
        
        window._staffXhr[targetId] = $.ajax({
            url: '<?= base_url('service/work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: ajaxData,
            success: function(response) {
                console.log(`📦 ${staffRole} response for ${targetId}:`, response);
                
                if (response.success && response.data) {
                    const staffSelect = $('#' + targetId);
                    
                    // Clear existing options and add placeholder
                    let placeholderText = staffRole === 'MECHANIC' ? 
                        (targetId === 'mechanic_1' ? '-- Select Mechanic 1 --' : '-- Select Mechanic 2 (Optional) --') :
                        (targetId === 'helper_1' ? '-- Select Helper 1 --' : '-- Select Helper 2 (Optional) --');
                    
                    staffSelect.empty().append(`<option value="">${placeholderText}</option>`);
                    
                    // Add staff options
                    response.data.forEach(function(staff) {
                        let staffName = staff.staff_name || staff.name || 'Unknown Staff';
                        let staffCode = staff.staff_code || staff.employee_code || '';
                        let optionText = staffCode ? `${staffName} (${staffCode})` : staffName;
                        
                        staffSelect.append(`<option value="${staff.id}">${optionText}</option>`);
                    });
                    
                    console.log(`🔧 Initializing Select2 for ${targetId}...`);
                    
                    // ALWAYS destroy first, then re-initialize
                    if (staffSelect.hasClass('select2-hidden-accessible')) {
                        staffSelect.select2('destroy');
                    }
                    
                    // Initialize Select2
                    staffSelect.select2({
                        placeholder: placeholderText,
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#workOrderModal'),
                        minimumInputLength: 0,
                        language: {
                            noResults: function() { return "No results found"; },
                            searching: function() { return "Searching..."; }
                        }
                    });
                    
                    console.log(`✅ ${staffRole} loaded with Select2 for ${targetId}: ${response.data.length} items`);

                    // Re-inject foreman as option after mechanic dropdown reloads
                    if (staffRole === 'MECHANIC' && typeof syncForemanToMechanicDropdowns === 'function') {
                        syncForemanToMechanicDropdowns();
                    }
                } else {
                    console.error(`❌ Error loading ${staffRole} staff:`, response.message || 'No data received');
                    
                    // Still add placeholder even if no data
                    const staffSelect = $('#' + targetId);
                    let placeholderText = staffRole === 'MECHANIC' ? 
                        (targetId === 'mechanic_1' ? '-- Select Mechanic 1 --' : '-- Select Mechanic 2 (Optional) --') :
                        (targetId === 'helper_1' ? '-- Select Helper 1 --' : '-- Select Helper 2 (Optional) --');
                    
                    staffSelect.empty().append(`<option value="">${placeholderText}</option>`);
                }
            },
            error: function(xhr, status, error) {
                if (status === 'abort') return; // Intentionally aborted — ignore
                console.error(`❌ AJAX Error loading ${staffRole} for ${targetId}:`, error);
                console.error('Response:', xhr.responseText);
                
                // Add placeholder even on error
                const staffSelect = $('#' + targetId);
                let placeholderText = staffRole === 'MECHANIC' ? 
                    (targetId === 'mechanic_1' ? '-- Select Mechanic 1 --' : '-- Select Mechanic 2 (Optional) --') :
                    (targetId === 'helper_1' ? '-- Select Helper 1 --' : '-- Select Helper 2 (Optional) --');
                
                staffSelect.empty().append(`<option value="">${placeholderText}</option>`);
            },
            complete: function() {
                window._staffXhr[targetId] = null;
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

    // Load admin and foreman dropdowns based on user login scope
    function loadAreaStaff() {
        // Clear dropdowns
        $('#admin_id').html('<option value="">-- Select Admin --</option>');
        $('#foreman_id').html('<option value="">-- Select Foreman --</option>');
        $('#pic_name').val('');
        
        $.ajax({
            url: '<?= base_url('service/work-orders/get-area-staff') ?>',
            type: 'POST',
            data: {},
            success: function(response) {
                // console.log('📦 Area staff response:', response);
                
                if (response.success) {
                    // Populate admin dropdown — clear first to prevent race-condition duplicates
                    $('#admin_id').html('<option value="">-- Select Admin --</option>');
                    if (response.data.admins && response.data.admins.length > 0) {
                        response.data.admins.forEach(function(admin, index) {
                            $('#admin_id').append(`<option value="${admin.id}">${admin.staff_name}</option>`);
                            
                            // Auto-select first admin and set as PIC
                            if (index === 0) {
                                $('#admin_id').val(admin.id);
                                $('#pic_name').val(admin.staff_name);
                            }
                        });
                    } else {
                        $('#admin_id').html('<option value="">No admin assigned to this area</option>');
                    }
                    
                    // Initialize Select2 for Admin (NEW)
                    if (!$('#admin_id').hasClass('select2-hidden-accessible')) {
                        $('#admin_id').select2({
                            placeholder: '-- Select Admin --',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#workOrderModal')
                        });
                    } else {
                        $('#admin_id').trigger('change.select2');
                    }
                    
                    // Populate foreman dropdown — clear first to prevent race-condition duplicates
                    $('#foreman_id').html('<option value="">-- Select Foreman --</option>');
                    if (response.data.foremans && response.data.foremans.length > 0) {
                        response.data.foremans.forEach(function(foreman, index) {
                            $('#foreman_id').append(`<option value="${foreman.id}">${foreman.staff_name}</option>`);
                            
                            // Auto-select first foreman
                            if (index === 0) {
                                $('#foreman_id').val(foreman.id);
                            }
                        });
                    } else {
                        $('#foreman_id').html('<option value="">No foreman assigned to this area</option>');
                    }
                    
                    // Initialize Select2 for Foreman (NEW)
                    if (!$('#foreman_id').hasClass('select2-hidden-accessible')) {
                        $('#foreman_id').select2({
                            placeholder: '-- Select Foreman --',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#workOrderModal')
                        });
                    } else {
                        $('#foreman_id').trigger('change.select2');
                    }
                    
                    // Load mechanic and helper dropdowns via user scope
                    loadStaffDropdown('MECHANIC', 'mechanic_1');
                    loadStaffDropdown('MECHANIC', 'mechanic_2');
                    loadStaffDropdown('HELPER', 'helper_1');
                    loadStaffDropdown('HELPER', 'helper_2');
                    
                    // console.log('✅ Area staff loaded successfully');
                } else {
                    console.error('❌ Error loading area staff:', response.message);
                    // Fallback to all staff if area staff load fails
                    loadAllStaffFallback();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading area staff:', error);
                console.error('❌ Response:', xhr.responseText);
                // Fallback to all staff on error
                loadAllStaffFallback();
            }
        });
        
        // Update PIC when admin changes
        $('#admin_id').off('change').on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            if ($(this).val()) {
                $('#pic_name').val(selectedText);
            } else {
                $('#pic_name').val('');
            }
        });
    }
    
    // NEW: Fallback function to load ALL staff when no area specified
    function loadAllStaffFallback() {
        console.log('🔄 FALLBACK: Loading all available staff (no area filter, WITH Select2)');
        
        // Load ALL admins with Select2
        const adminData = { staff_role: 'ADMIN' };
        adminData[window.csrfTokenName] = window.csrfTokenValue;

        if (window._staffXhr['admin_id']) {
            window._staffXhr['admin_id'].abort();
            window._staffXhr['admin_id'] = null;
        }
        window._staffXhr['admin_id'] = $.ajax({
            url: '<?= base_url('service/work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: adminData,
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    $('#admin_id').empty().append('<option value="">-- Select Admin --</option>');
                    response.data.forEach(function(admin, index) {
                        $('#admin_id').append('<option value="' + admin.id + '">' + admin.staff_name + '</option>');
                        if (index === 0) {
                            $('#admin_id').val(admin.id);
                            $('#pic_name').val(admin.staff_name);
                        }
                    });
                    
                    // Initialize Select2 for Admin
                    if ($('#admin_id').hasClass('select2-hidden-accessible')) {
                        $('#admin_id').select2('destroy');
                    }
                    $('#admin_id').select2({
                        placeholder: '-- Select Admin --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#workOrderModal')
                    });
                    console.log('✅ Admin dropdown loaded with Select2:', response.data.length, 'items');
                }
            },
            error: function(xhr, status) { if (status === 'abort') return; },
            complete: function() { window._staffXhr['admin_id'] = null; }
        });
        
        // Load ALL foremans with Select2
        const foremanData = { staff_role: 'FOREMAN' };
        foremanData[window.csrfTokenName] = window.csrfTokenValue;

        if (window._staffXhr['foreman_id']) {
            window._staffXhr['foreman_id'].abort();
            window._staffXhr['foreman_id'] = null;
        }
        window._staffXhr['foreman_id'] = $.ajax({
            url: '<?= base_url('service/work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: foremanData,
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    $('#foreman_id').empty().append('<option value="">-- Select Foreman --</option>');
                    response.data.forEach(function(foreman, index) {
                        $('#foreman_id').append('<option value="' + foreman.id + '">' + foreman.staff_name + '</option>');
                        if (index === 0) {
                            $('#foreman_id').val(foreman.id);
                        }
                    });
                    
                    // Initialize Select2 for Foreman
                    if ($('#foreman_id').hasClass('select2-hidden-accessible')) {
                        $('#foreman_id').select2('destroy');
                    }
                    $('#foreman_id').select2({
                        placeholder: '-- Select Foreman --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#workOrderModal')
                    });
                    console.log('✅ Foreman dropdown loaded with Select2:', response.data.length, 'items');
                }
            },
            error: function(xhr, status) { if (status === 'abort') return; },
            complete: function() { window._staffXhr['foreman_id'] = null; }
        });
        
        // Load ALL mechanics and helpers (using existing loadStaffDropdown function with Select2)
        loadStaffDropdown('MECHANIC', 'mechanic_1');
        loadStaffDropdown('MECHANIC', 'mechanic_2');
        loadStaffDropdown('HELPER', 'helper_1');
        loadStaffDropdown('HELPER', 'helper_2');
        
        // console.log('✅ All staff loaded (fallback mode)');
    }
    
    /**
     * Filter staff by unit's department (DIESEL/ELECTRIC)
     * Called when unit is selected in the form
     */
    function filterStaffByUnitDepartment(unitId) {
        if (!unitId) {
            // No unit selected, load all staff
            loadAllStaffFallback();
            return;
        }
        
        // Fetch unit area via AJAX (no pre-loaded data needed)
        const unitAreaData = { unit_id: unitId };
        unitAreaData[window.csrfTokenName] = window.csrfTokenValue;
        
        $.ajax({
            url: '<?= base_url('service/work-orders/get-unit-area') ?>',
            type: 'POST',
            data: unitAreaData,
            success: function(response) {
                if (response.success && response.data && response.data.departemen_id) {
                    const departemenId = response.data.departemen_id;
                    const departemenName = response.data.departemen_name || '';
                    
                    console.log(`🔍 Unit department: ${departemenName} (ID: ${departemenId})`);
                    
                    // Filter staff by department
                    loadStaffByDepartment(departemenId, 'MECHANIC', 'mechanic_1');
                    loadStaffByDepartment(departemenId, 'MECHANIC', 'mechanic_2');
                    loadStaffByDepartment(departemenId, 'HELPER', 'helper_1');
                    loadStaffByDepartment(departemenId, 'HELPER', 'helper_2');
                } else {
                    console.warn('⚠️ No department info, loading all staff');
                    loadAllStaffFallback();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error getting unit department:', error);
                loadAllStaffFallback();
            }
        });
    }
    
    /**
     * Load staff filtered by department
     */
    function loadStaffByDepartment(departemenId, staffRole, targetId) {
        // Build data object with CSRF token
        const ajaxData = { 
            staff_role: staffRole,
            departemen_id: departemenId
        };
        ajaxData[window.csrfTokenName] = window.csrfTokenValue;
        
        $.ajax({
            url: '<?= base_url('service/work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: ajaxData,
            success: function(response) {
                if (response.success && response.data) {
                    const staffSelect = $('#' + targetId);
                    
                    // Determine placeholder text based on role
                    let placeholderText;
                    if (staffRole === 'ADMIN') {
                        placeholderText = '-- Select Admin --';
                    } else if (staffRole === 'FOREMAN') {
                        placeholderText = '-- Select Foreman --';
                    } else if (staffRole === 'MECHANIC') {
                        placeholderText = targetId === 'mechanic_1' ? '-- Select Mechanic 1 --' : '-- Select Mechanic 2 (Optional) --';
                    } else if (staffRole === 'HELPER') {
                        placeholderText = targetId === 'helper_1' ? '-- Select Helper 1 --' : '-- Select Helper 2 (Optional) --';
                    } else {
                        placeholderText = `-- Select ${staffRole} --`;
                    }
                    
                    // Clear and add placeholder
                    staffSelect.empty().append(`<option value="">${placeholderText}</option>`);
                    
                    // Add staff options
                    response.data.forEach(function(staff, index) {
                        let staffName = staff.staff_name || staff.name || 'Unknown';
                        let staffCode = staff.staff_code || staff.employee_code || '';
                        let optionText = staffCode ? `${staffName} (${staffCode})` : staffName;
                        
                        staffSelect.append(`<option value="${staff.id}">${optionText}</option>`);
                        
                        // Auto-select first admin (for PIC)
                        if (staffRole === 'ADMIN' && targetId === 'admin_id' && index === 0) {
                            staffSelect.val(staff.id);
                            $('#pic_name').val(staffName);
                        }
                        
                        // Auto-select first foreman
                        if (staffRole === 'FOREMAN' && targetId === 'foreman_id' && index === 0) {
                            staffSelect.val(staff.id);
                        }
                    });
                    
                    // Initialize/Re-initialize Select2
                    if (staffSelect.hasClass('select2-hidden-accessible')) {
                        staffSelect.select2('destroy');
                    }
                    
                    staffSelect.select2({
                        placeholder: placeholderText,
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#workOrderModal'),
                        minimumInputLength: 0
                    });
                    
                    console.log(`✅ Loaded ${response.data.length} ${staffRole} for department ${departemenId}`);
                } else {
                    console.error(`❌ No ${staffRole} found for department`);
                }
            },
            error: function(xhr, status, error) {
                console.error(`❌ Error loading ${staffRole} by department:`, error);
            }
        });
    }
    
    /**
     * Load staff filtered by user's department scope (sends departemen_ids[])
     * Backend auto-applies scope when no explicit filter is sent
     */
    function loadStaffByDepartmentScope(staffRole, targetId) {
        // Abort any in-flight request for this dropdown to prevent race-condition duplicates
        if (window._staffXhr[targetId]) {
            window._staffXhr[targetId].abort();
            window._staffXhr[targetId] = null;
        }

        const ajaxData = { 
            staff_role: staffRole
        };
        
        // Send department IDs if available
        if (window.currentUserDepartment && window.currentUserDepartment.ids) {
            window.currentUserDepartment.ids.forEach(function(id, index) {
                ajaxData['departemen_ids[' + index + ']'] = id;
            });
        }
        
        ajaxData[window.csrfTokenName] = window.csrfTokenValue;
        
        window._staffXhr[targetId] = $.ajax({
            url: '<?= base_url('service/work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: ajaxData,
            success: function(response) {
                if (response.success && response.data) {
                    const staffSelect = $('#' + targetId);
                    
                    let placeholderText;
                    if (staffRole === 'ADMIN') {
                        placeholderText = '-- Select Admin --';
                    } else if (staffRole === 'FOREMAN') {
                        placeholderText = '-- Select Foreman --';
                    } else if (staffRole === 'MECHANIC') {
                        placeholderText = targetId === 'mechanic_1' ? '-- Select Mechanic 1 --' : '-- Select Mechanic 2 (Optional) --';
                    } else if (staffRole === 'HELPER') {
                        placeholderText = targetId === 'helper_1' ? '-- Select Helper 1 --' : '-- Select Helper 2 (Optional) --';
                    } else {
                        placeholderText = `-- Select ${staffRole} --`;
                    }
                    
                    staffSelect.empty().append(`<option value="">${placeholderText}</option>`);
                    
                    response.data.forEach(function(staff, index) {
                        let staffName = staff.staff_name || staff.name || 'Unknown';
                        let staffCode = staff.staff_code || staff.employee_code || '';
                        let optionText = staffCode ? `${staffName} (${staffCode})` : staffName;
                        
                        staffSelect.append(`<option value="${staff.id}">${optionText}</option>`);
                        
                        if (staffRole === 'ADMIN' && targetId === 'admin_id' && index === 0) {
                            staffSelect.val(staff.id);
                            $('#pic_name').val(staffName);
                        }
                        if (staffRole === 'FOREMAN' && targetId === 'foreman_id' && index === 0) {
                            staffSelect.val(staff.id);
                        }
                    });
                    
                    if (staffSelect.hasClass('select2-hidden-accessible')) {
                        staffSelect.select2('destroy');
                    }
                    
                    staffSelect.select2({
                        placeholder: placeholderText,
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#workOrderModal'),
                        minimumInputLength: 0
                    });

                    // Re-inject foreman as option after mechanic dropdown reloads
                    if (staffRole === 'MECHANIC' && typeof syncForemanToMechanicDropdowns === 'function') {
                        syncForemanToMechanicDropdowns();
                    }
                    
                    console.log(`✅ Loaded ${response.data.length} ${staffRole} (dept scope)`);
                } else {
                    console.error(`❌ No ${staffRole} found for department scope`);
                }
            },
            error: function(xhr, status, error) {
                if (status === 'abort') return; // Intentionally aborted — ignore
                console.error(`❌ Error loading ${staffRole} by dept scope:`, error);
            },
            complete: function() {
                window._staffXhr[targetId] = null;
            }
        });
    }
    
    /**
     * Format sparepart option with badge for code
     */
    function formatSparepartOption(sparepart) {
        if (!sparepart.id) return sparepart.text;
        
        const text = sparepart.text || '';
        // Format: "[CODE] NAME" → show with badge
        const match = text.match(/^\[([^\]]+)\]\s+(.+)$/);
        
        if (match) {
            const code = match[1];
            const name = match[2];
            return $(`<span><span class="badge bg-primary me-2">${code}</span>${name}</span>`);
        }
        
        return $(`<span>${text}</span>`);
    }
    
    /**
     * Format selected sparepart (show full text)
     */
    function formatSparepartSelection(sparepart) {
        return sparepart.text || sparepart.id;
    }
    
    /**
     * Trigger manual input for sparepart (called from dropdown button)
     */
    window.triggerManualInput = function(rowId) {
        const sparepartDropdown = $(`#sparepart_${rowId}`);
        const manualInput = $(`#sparepart_manual_${rowId}`);
        
        // Close dropdown
        sparepartDropdown.select2('close');
        
        // Hide dropdown
        sparepartDropdown.addClass('d-none').removeAttr('name').removeAttr('required');
        
        // Destroy Select2
        if (sparepartDropdown.hasClass('select2-hidden-accessible')) {
            sparepartDropdown.select2('destroy');
        }
        
        // Show manual input
        manualInput.removeClass('d-none')
                   .attr('name', 'sparepart_name[]')
                   .attr('required', 'required')
                   .focus();
        
        console.log(`📝 Switched to manual input for row ${rowId}`);
    };
    
    // Unit change handler - Filter staff by unit department
    $('#unit_id').on('change', function() {
        const unitId = $(this).val();
        if (unitId) {
            filterStaffByUnitDepartment(unitId);
        }
    });
    
    // NEW: Visual indicator functions for area-based staff loading
    function showAreaIndicator(areaName, hasArea) {
        var indicator = $('#areaIndicator');
        var indicatorText = $('#areaIndicatorText');
        
        // Remove all alert classes
        indicator.removeClass('alert-info alert-warning alert-success d-none');
        
        if (hasArea && areaName) {
            // Case 1: Unit HAS area - show info alert with area name
            indicator.addClass('alert-info');
            indicatorText.html('<i class="fas fa-filter me-1"></i> Area: <strong>' + areaName + '</strong> — Staff filtered by this area');
        } else {
            // Case 2: Unit has NO area - show warning alert
            indicator.addClass('alert-warning');
            indicatorText.html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>No Area Assigned</strong> — Showing all available staff (not filtered)');
        }
        
        indicator.removeClass('d-none').fadeIn();
    }
    
    function hideAreaIndicator() {
        $('#areaIndicator').addClass('d-none').fadeOut();
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
        
        // Helper is optional - always clear any invalid state
        $('#helper_1, #helper_2').removeClass('is-invalid');
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
     * Function addSparepartRow - Dynamic input: Dropdown untuk Sparepart, Text Input untuk Tool
     * Tool items manual input (tidak dari database)
     */
    addSparepartRow = function(sparepartData = null) {
        sparepartRowCount++;
        // console.log(`🔧 Adding item row ${sparepartRowCount}`);
        
        const row = `
            <tr>
                <td>
                    <!-- Item Type: Sparepart or Tool -->
                    <select class="form-select form-select-sm" name="item_type[]" 
                            id="item_type_${sparepartRowCount}" 
                            onchange="switchItemInput(${sparepartRowCount})" required>
                        <option value="sparepart" selected>
                            <i class="fas fa-cog"></i> Sparepart
                        </option>
                        <option value="tool">
                            <i class="fas fa-tools"></i> Tool
                        </option>
                    </select>
                </td>
                <td>
                    <!-- Dynamic Input Container -->
                    <div id="item_input_container_${sparepartRowCount}">
                        <!-- Sparepart Dropdown (Default - active) -->
                        <select class="form-select form-select-sm" 
                                name="sparepart_name[]" 
                                id="sparepart_${sparepartRowCount}" 
                                required>
                            <option value="">-- Select Sparepart --</option>
                        </select>
                        <!-- Manual Sparepart Input (Hidden by default - NO name attr) -->
                        <input type="text" 
                               class="form-control form-control-sm d-none" 
                               id="sparepart_manual_${sparepartRowCount}"
                               placeholder="Ketik nama sparepart manual" 
                               maxlength="255">
                        <!-- Tool Text Input (Hidden by default - NO name attr until activated) -->
                        <input type="text" 
                               class="form-control form-control-sm d-none" 
                               id="tool_input_${sparepartRowCount}"
                               placeholder="e.g., Kunci Inggris 12mm" 
                               maxlength="255">
                    </div>
                </td>
                <td>
                    <input type="number" 
                           class="form-control form-control-sm" 
                           name="sparepart_quantity[]" 
                           value="1" 
                           min="1" 
                           required>
                </td>
                <td>
                    <select class="form-select form-select-sm" name="sparepart_unit[]" required>
                        <optgroup label="📦 Barang / Unit">
                            <option value="PCS" selected>PCS</option>
                            <option value="UNIT">UNIT</option>
                            <option value="SET">SET</option>
                            <option value="PASANG">PASANG</option>
                        </optgroup>
                        <optgroup label="⚖️ Berat">
                            <option value="KG">KG</option>
                            <option value="GRAM">GRAM</option>
                        </optgroup>
                        <optgroup label="📏 Panjang">
                            <option value="METER">METER</option>
                            <option value="CM">CM</option>
                        </optgroup>
                        <optgroup label="🧴 Volume">
                            <option value="LITER">LITER</option>
                            <option value="ML">ML</option>
                        </optgroup>
                    </select>
                </td>
                <td>
                    <!-- Source Type Dropdown -->
                    <select class="form-select form-select-sm" 
                            name="source_type[]" 
                            id="source_type_${sparepartRowCount}" 
                            onchange="toggleKanibalFields(${sparepartRowCount})" 
                            required>
                        <option value="WAREHOUSE" selected>
                            <i class="fas fa-warehouse"></i> Warehouse
                        </option>
                        <option value="BEKAS">
                            <i class="fas fa-recycle"></i> Bekas
                        </option>
                        <option value="KANIBAL">
                            <i class="fas fa-exchange-alt"></i> Kanibal
                        </option>
                    </select>
                </td>
                <td>
                    <!-- KANIBAL Fields (Hidden by default) -->
                    <div class="kanibal-fields d-none" id="kanibal_fields_${sparepartRowCount}">
                        <div class="mb-2">
                            <label class="form-label form-label-sm mb-1">Dari Unit *</label>
                            <select class="form-select form-select-sm" 
                                    name="source_unit_id[]" 
                                    id="source_unit_${sparepartRowCount}">
                                <option value="">-- Pilih Unit Sumber --</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label form-label-sm mb-1">Alasan *</label>
                            <textarea class="form-control form-control-sm" 
                                      name="source_notes[]" 
                                      id="source_notes_${sparepartRowCount}"
                                      rows="2" 
                                      placeholder="Contoh: Unit rusak total"
                                      maxlength="500"></textarea>
                        </div>
                    </div>
                    
                    <!-- Notes for non-KANIBAL -->
                    <div class="non-kanibal-notes" id="non_kanibal_notes_${sparepartRowCount}">
                        <input type="text" 
                               class="form-control form-control-sm" 
                               name="sparepart_notes[]" 
                               placeholder="Optional notes..." 
                               maxlength="255">
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm removeSparepartRow">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#sparepartTableBody').append(row);
        
        // Initialize Select2 for sparepart dropdown WITH AJAX (optimized for 14k+ items)
        const sparepartSelect = $(`#sparepart_${sparepartRowCount}`);
        
        // REMOVED: Pre-population of 14,000+ items (performance killer)
        // NOW: Use AJAX Select2 for on-demand loading
        
        // Initialize Select2 with AJAX configuration
        setTimeout(function() {
            try {
                if (!sparepartSelect.hasClass('select2-hidden-accessible')) {
                    sparepartSelect.select2({
                        placeholder: '-- Ketik untuk cari sparepart --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#workOrderModal'),
                        minimumInputLength: 2, // Require 2 characters before searching
                        ajax: {
                            url: '<?= base_url('service/work-orders/search-spareparts') ?>',
                            dataType: 'json',
                            delay: 250, // Debounce requests
                            data: function (params) {
                                return {
                                    q: params.term, // Search term
                                    page: params.page || 1
                                };
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                    results: data.results,
                                    pagination: {
                                        more: data.pagination.more
                                    }
                                };
                            },
                            cache: true
                        },
                        language: {
                            inputTooShort: function() {
                                return 'Ketik minimal 2 karakter untuk mencari...';
                            },
                            searching: function() {
                                return 'Mencari sparepart...';
                            },
                            noResults: function() {
                                return 'Tidak ada sparepart ditemukan';
                            },
                            loadingMore: function() {
                                return 'Memuat lebih banyak...';
                            }
                        }
                    });
                    console.log(`✅ AJAX Select2 initialized for sparepart_${sparepartRowCount} (on-demand loading)`);
                }
            } catch (error) {
                console.error(`❌ Error initializing AJAX Select2:`, error);
            }
        }, 150);
        
        // If editing existing data, populate fields
        if (sparepartData) {
            try {
                // Set item type
                if (sparepartData.item_type) {
                    $(`#item_type_${sparepartRowCount}`).val(sparepartData.item_type).trigger('change');
                    switchItemInput(sparepartRowCount, sparepartData.item_type);
                }
                
                // Set item name (for edit mode - add option manually)
                const itemName = sparepartData.sparepart_name || sparepartData.name;
                if (itemName) {
                    if (sparepartData.item_type === 'tool') {
                        $(`#tool_input_${sparepartRowCount}`).val(itemName);
                    } else {
                        // Add the existing sparepart as an option (for edit mode)
                        if (sparepartSelect.find(`option[value="${itemName}"]`).length === 0) {
                            sparepartSelect.append(`<option value="${itemName}" selected>${itemName}</option>`);
                        }
                        sparepartSelect.val(itemName).trigger('change');
                    }
                }
                
                // Set quantity, unit, notes
                if (sparepartData.quantity || sparepartData.qty) {
                    sparepartSelect.closest('tr').find('input[name="sparepart_quantity[]"]')
                        .val(sparepartData.quantity || sparepartData.qty);
                }
                if (sparepartData.unit || sparepartData.satuan) {
                    sparepartSelect.closest('tr').find('select[name="sparepart_unit[]"]')
                        .val(sparepartData.unit || sparepartData.satuan);
                }
                if (sparepartData.notes) {
                    sparepartSelect.closest('tr').find('input[name="sparepart_notes[]"]')
                        .val(sparepartData.notes);
                }
                if (sparepartData.is_from_warehouse !== undefined) {
                    const checkbox = $(`#warehouse_${sparepartRowCount}`);
                    checkbox.prop('checked', sparepartData.is_from_warehouse == 1);
                    toggleSourceLabel(checkbox[0]);
                }
                
                console.log(`✅ Populated item row ${sparepartRowCount} (edit mode)`);
            } catch (error) {
                console.error('❌ Error populating row:', error);
            }
        }
        
        return sparepartSelect;
    };
    
    /**
     * Add Manual Entry Button to Sparepart Dropdown (Below Search Box)
     * Triggered when sparepart Select2 dropdown opens
     */
    $(document).on('select2:open', '[id^="sparepart_"]:not([id$="_manual"])', function() {
        const $select = $(this);
        const rowId = $select.attr('id').replace('sparepart_', '');
        const $dropdown = $('.select2-dropdown:last');
        
        // Remove existing manual entry button (if any)
        $dropdown.find('.sparepart-manual-entry-btn').remove();
        
        // Create manual entry button
        const manualButton = $(`
            <div class="sparepart-manual-entry-btn" 
                 style="padding: 12px 15px; 
                        cursor: pointer; 
                        border: 2px solid #007bff; 
                        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                        text-align: center;
                        font-weight: 600;
                        color: #0d47a1;
                        margin: 0;
                        border-radius: 0;
                        border-left: 0;
                        border-right: 0;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        transition: all 0.2s ease;">
                <i class="fas fa-pencil-alt me-2"></i>
                <span>📝 Input Manual Sparepart</span>
            </div>
        `);
        
        // Hover effects
        manualButton.hover(
            function() {
                $(this).css({
                    'background': 'linear-gradient(135deg, #bbdefb 0%, #90caf9 100%)',
                    'transform': 'scale(1.02)',
                    'box-shadow': '0 4px 8px rgba(0,0,0,0.15)'
                });
            },
            function() {
                $(this).css({
                    'background': 'linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%)',
                    'transform': 'scale(1)',
                    'box-shadow': '0 2px 4px rgba(0,0,0,0.1)'
                });
            }
        );
        
        // Click event
        manualButton.on('click', function() {
            console.log(`📝 Manual entry button clicked for row ${rowId}`);
            
            // Close Select2
            $select.select2('close');
            
            // Switch to manual input
            const manualInput = $(`#sparepart_manual_${rowId}`);
            
            // Hide dropdown
            $select.addClass('d-none').removeAttr('name').removeAttr('required');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            
            // Show manual input
            manualInput.removeClass('d-none')
                       .attr('name', 'sparepart_name[]')
                       .attr('required', 'required')
                       .focus();
            
            console.log(`✅ Switched to manual input for row ${rowId}`);
        });
        
        // Insert button IMMEDIATELY AFTER search container (CRITICAL POSITION)
        const $searchContainer = $dropdown.find('.select2-search');
        if ($searchContainer.length > 0) {
            $searchContainer.after(manualButton);
            console.log(`✅ Manual entry button added below search for row ${rowId}`);
        } else {
            // Fallback: prepend to results
            $dropdown.find('.select2-results').prepend(manualButton);
            console.log(`⚠️ Manual entry button added at top of results for row ${rowId}`);
        }
    });
    
    /**
     * Switch Item Input - Toggle between Dropdown (Sparepart) and Text Input (Tool)
     */
    window.switchItemInput = function(rowId, itemType = null) {
        const typeSelect = $(`#item_type_${rowId}`);
        const type = itemType || typeSelect.val();
        const sparepartDropdown = $(`#sparepart_${rowId}`);
        const manualInput = $(`#sparepart_manual_${rowId}`);
        const toolInput = $(`#tool_input_${rowId}`);
        
        if (type === 'tool') {
            // TOOL mode: show text input, hide dropdown & manual
            sparepartDropdown.addClass('d-none').removeAttr('required').removeAttr('name');
            manualInput.addClass('d-none').removeAttr('required').removeAttr('name');
            toolInput.removeClass('d-none').attr('required', 'required').attr('name', 'sparepart_name[]');
            
            // Destroy Select2 if exists
            if (sparepartDropdown.hasClass('select2-hidden-accessible')) {
                sparepartDropdown.select2('destroy');
            }
        } else {
            // SPAREPART mode: show dropdown, hide text inputs
            toolInput.addClass('d-none').removeAttr('required').removeAttr('name');
            manualInput.addClass('d-none').removeAttr('required').removeAttr('name');
            sparepartDropdown.removeClass('d-none').attr('required', 'required').attr('name', 'sparepart_name[]');
            
            // Re-initialize Select2 with AJAX (optimized)
            if (!sparepartDropdown.hasClass('select2-hidden-accessible')) {
                sparepartDropdown.select2({
                    placeholder: '-- Ketik untuk cari sparepart --',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#workOrderModal'),
                    minimumInputLength: 2,
                    ajax: {
                        url: '<?= base_url('service/work-orders/search-spareparts') ?>',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    },
                    language: {
                        inputTooShort: function() {
                            return 'Ketik minimal 2 karakter untuk mencari...';
                        },
                        searching: function() {
                            return 'Mencari sparepart...';
                        },
                        noResults: function() {
                            return 'Tidak ada sparepart ditemukan';
                        },
                        loadingMore: function() {
                            return 'Memuat lebih banyak...';
                        }
                    }
                });
            }
            
            // console.log(`✅ Switched to SPAREPART dropdown for row ${rowId}`);
        }
    };
    
    /**
     * Toggle KANIBAL Fields - Show/hide unit selector and notes when KANIBAL selected
     */
    window.toggleKanibalFields = function(rowId) {
        const sourceTypeSelect = $(`#source_type_${rowId}`);
        const sourceType = sourceTypeSelect.val();
        const kanibalFields = $(`#kanibal_fields_${rowId}`);
        const nonKanibalNotes = $(`#non_kanibal_notes_${rowId}`);
        const sourceUnitSelect = $(`#source_unit_${rowId}`);
        const sourceNotesTextarea = $(`#source_notes_${rowId}`);
        
        if (sourceType === 'KANIBAL') {
            // Show KANIBAL-specific fields
            kanibalFields.removeClass('d-none');
            nonKanibalNotes.addClass('d-none');
            
            // Make fields required
            sourceUnitSelect.attr('required', 'required');
            sourceNotesTextarea.attr('required', 'required');
            
            // Initialize Select2 AJAX for KANIBAL source unit (lazy-load, no pre-load)
            if (!sourceUnitSelect.hasClass('select2-hidden-accessible')) {
                sourceUnitSelect.select2({
                    placeholder: '-- Ketik no unit / pelanggan --',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#workOrderModal'),
                    minimumInputLength: 2,
                    language: {
                        noResults: function() { return 'Unit tidak ditemukan'; },
                        searching: function() { return 'Mencari...'; },
                        inputTooShort: function() { return 'Ketik minimal 2 karakter...'; }
                    },
                    ajax: {
                        url: base_url + 'service/work-orders/units-dropdown',
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return { search: params.term || '', kanibal: '1' };
                        },
                        processResults: function(resp) {
                            const units = resp.data || [];
                            return {
                                results: units.map(function(u) {
                                    const label = u.no_unit
                                        + (u.pelanggan && u.pelanggan !== 'Belum Ada Kontrak' ? ' – ' + u.pelanggan : '')
                                        + (u.merk ? ' [' + u.merk + ']' : '');
                                    return { id: u.id, text: label };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }
            
            console.log(`✅ KANIBAL fields shown for row ${rowId}`);
        } else {
            // Hide KANIBAL-specific fields
            kanibalFields.addClass('d-none');
            nonKanibalNotes.removeClass('d-none');
            
            // Remove required attribute
            sourceUnitSelect.removeAttr('required').val('');
            sourceNotesTextarea.removeAttr('required').val('');
            
            console.log(`✅ KANIBAL fields hidden for row ${rowId}`);
        }
    };
    
    /**
     * Handle Sparepart Dropdown - Toggle between dropdown and manual input
     */
    $(document).on('change', '[id^="sparepart_"]:not([id$="_manual"])', function() {
        const rowId = $(this).attr('id').replace('sparepart_', '');
        const selectedValue = $(this).val();
        
        if (selectedValue === 'INPUT_MANUAL') {
            // Switch to manual input mode
            const manualInput = $(`#sparepart_manual_${rowId}`);
            
            // Hide dropdown, remove name attribute
            $(this).addClass('d-none').removeAttr('name').removeAttr('required');
            
            // Destroy Select2 if active
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            // Show manual input, add name attribute
            manualInput.removeClass('d-none')
                       .attr('name', 'sparepart_name[]')
                       .attr('required', 'required')
                       .focus();
            
            console.log(`📝 Switched to manual sparepart input for row ${rowId}`);
        }
    });
    
    /**
     * Allow user to switch back from manual input to dropdown (double-click)
     */
    $(document).on('dblclick', '[id^="sparepart_manual_"]', function() {
        if (!confirm('Kembali ke pilihan dropdown?')) return;
        
        const rowId = $(this).attr('id').replace('sparepart_manual_', '');
        const dropdown = $(`#sparepart_${rowId}`);
        
        // Hide manual input, remove name attribute
        $(this).addClass('d-none')
               .removeAttr('name')
               .removeAttr('required')
               .val('');
        
        // Show dropdown, restore name attribute
        dropdown.removeClass('d-none')
                .attr('name', 'sparepart_name[]')
                .attr('required', 'required')
                .val('');
        
        // Re-initialize Select2
        if (!dropdown.hasClass('select2-hidden-accessible')) {
            dropdown.select2({
                placeholder: '-- Select Sparepart --',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#workOrderModal'),
                minimumInputLength: 0,
                minimumResultsForSearch: 0
            });
        }
        
        console.log(`📝 Switched back to dropdown for row ${rowId}`);
    });
    
    // console.log('✅ Item management system loaded - Spareparts (dropdown/manual) & Tools (manual input)');
});

</script>

<?php include 'sparepart_validation.php'; ?>
<?php include 'complete_work_order_modal.php'; ?>
<?php include 'unit_verification.php'; ?>

<?= $this->endSection() ?>


