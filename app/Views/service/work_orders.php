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
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> <?= lang('Service.service_list') ?></h5>
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
                            <span><?= lang('App.closed') ?></span>
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
                            <label for="filter-status-progress"><?= lang('Common.status') ?></label>
                            <select id="filter-status-progress" class="form-select form-select-sm">
                                <option value=""><?= lang('App.all_status') ?></option>
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
                            <label for="filter-priority-progress"><?= lang('App.priority') ?></label>
                            <select id="filter-priority-progress" class="form-select form-select-sm">
                                <option value=""><?= lang('App.all_priority') ?></option>
                                <?php foreach ($priorities as $priority): ?>
                                <option value="<?= $priority['priority_name'] ?>"><?= $priority['priority_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-start-date-progress">Start Date</label>
                            <input type="date" id="filter-start-date-progress" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-end-date-progress">End Date</label>
                            <input type="date" id="filter-end-date-progress" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                
                <!-- Progress Table -->
                <?php if (!can_view('service')): ?>
                <div class="alert alert-warning m-3">
                    <i class="fas fa-lock me-2"></i>
                    <strong><?= lang('App.access_restricted') ?>:</strong> <?= lang('App.no_permission_view') ?> <?= strtolower(lang('Service.work_orders')) ?>. 
                    <?= lang('App.contact_administrator') ?>.
                </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="progressWorkOrdersTable" class="table table-striped table-hover <?= !$can_view ? 'table-disabled' : '' ?>">
                        <thead>
                            <tr>
                                <th width="5%"><?= lang('Common.no') ?></th>
                                <th><?= lang('Service.work_order') ?></th>
                                <th><?= lang('Common.date') ?></th>
                                <th><?= lang('App.unit') ?></th>
                                <th><?= lang('Common.type') ?></th>
                                <th><?= lang('App.priority') ?></th>
                                <th><?= lang('Common.category') ?></th>
                                <th><?= lang('Common.status') ?></th>
                                <th width="10%"><?= lang('App.action') ?></th>
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
                            <label for="filter-priority-closed"><?= lang('App.priority') ?></label>
                            <select id="filter-priority-closed" class="form-select form-select-sm">
                                <option value=""><?= lang('App.all_priority') ?></option>
                                <?php foreach ($priorities as $priority): ?>
                                <option value="<?= $priority['priority_name'] ?>"><?= $priority['priority_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-start-date-closed">Start Date</label>
                            <input type="date" id="filter-start-date-closed" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-end-date-closed">End Date</label>
                            <input type="date" id="filter-end-date-closed" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-month-closed">Filter Month</label>
                            <select id="filter-month-closed" class="form-select form-select-sm">
                                <option value="">All Months</option>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
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
                                <th>WO Number</th>
                                <th>Date</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Category</th>
                                <th>Closed Date</th>
                                <th width="10%">Actions</th>
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
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workOrderFormTitle"><i class="fas fa-plus-circle me-2"></i>Add New Work Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="workOrderForm" action="<?= base_url('service/work-orders/store') ?>" method="post" novalidate>
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
                                        <option value="FABRIKASI"><?= lang('Service.fabrication') ?></option>
                                        <option value="PERSIAPAN"><?= lang('Service.preparation') ?></option>
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
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-users-cog me-2"></i>Staff Assignment</h6>
                        </div>
                        <div class="card-body">
                            <!-- Admin & Foreman - Dropdown -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="admin_id" class="form-label">Admin</label>
                                    <select class="form-select" id="admin_id" name="admin_id">
                                        <option value="" selected>-- Select Admin --</option>
                                    </select>
                                    <small class="form-text text-muted">Select admin based on area</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foreman_id" class="form-label">Foreman</label>
                                    <select class="form-select" id="foreman_id" name="foreman_id">
                                        <option value="" selected>-- Select Foreman --</option>
                                    </select>
                                    <small class="form-text text-muted">Select foreman based on area</small>
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
                                                    <option value="" selected disabled>-- Select Mechanic 1 --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="mechanic_2" name="mechanic_id[]">
                                                    <option value="" selected disabled>-- Select Mechanic 2 (Optional) --</option>
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
                                                    <option value="" selected disabled>-- Select Helper 1 --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select" id="helper_2" name="helper_id[]">
                                                    <option value="" selected disabled>-- Select Helper 2 (Optional) --</option>
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
                                <table class="table table-striped table-hover table-sm" id="sparepartTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="12%">Type*</th>
                                            <th width="28%">Item Name*</th>
                                            <th width="10%">Qty*</th>
                                            <th width="10%">Unit*</th>
                                            <th width="15%">Source*</th>
                                            <th width="20%">Notes</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sparepartTableBody">
                                        <!-- Dynamic rows will be added here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-sm" id="addSparepartRow">
                                    <i class="fas fa-plus"></i> Add Item
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
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" form="workOrderForm" id="btnSubmitWo" onclick="event.stopPropagation();">
                        <i class="fas fa-save me-1"></i> Save Work Order
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
                                    <dd class="col-sm-8"><span class="badge bg-success-subtle text-success-emphasis border border-success-subtle" id="viewUnitStatus">-</span></dd>
                                    
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
                                    <dd class="col-sm-7" id="viewWoCompletionDate">Not completed</dd>
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
                                        <td colspan="8" class="text-center text-muted">No items brought</td>
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
                                <label class="form-label text-muted">Additional Notes</label>
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
                    <i class="fas fa-trash me-1"></i>Delete Work Order
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        $('#workOrderFormTitle').text('New Work Order');
        $('#workOrderForm').attr('action', '<?= base_url('service/work-orders/store') ?>');
        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> Save Work Order');
        
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
        $('#unitSelectedText').text('-- Select Unit --');
        $('#unit_id').val('');
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
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Complaint Description wajib diisi minimal 3 karakter',
                confirmButtonText: 'OK'
            });
            $('#complaint_description').addClass('is-invalid').focus();
            return false;
        }
        $('#complaint_description').removeClass('is-invalid');
        
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
                $('#btnSubmitWo').prop('disabled', true).text('Saving...');
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
                        showAlert('error', response.message || 'An error occurred while saving data');
                    }
                } catch (e) {
                    showAlert('error', 'An error occurred while saving data');
                }
            },
            complete: function() {
                $('#btnSubmitWo').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Work Order');
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
                console.log('📦 Work Order Detail Data:', response.data);
                console.log('🔧 Spareparts Data:', response.data.spareparts);
                
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
        
        console.log('🚀 Start Work clicked (btn-assign) for ID:', id, 'WO Number:', woNumber);
        
        // Simple SweetAlert with print button
        Swal.fire({
            title: 'Start Work?',
            html: `
                <p>Make sure the Work Order document and Unit Verification Form have been printed before starting the work.</p>
                <p><strong>NOTE:</strong> Unit Verification is mandatory and must be documented to complete the Work Order.</p>
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
            title: 'Select Pause Type',
            text: woNumber ? `Work Order ${woNumber}` : 'Select pause type for this work order',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Pending',
            cancelButtonText: 'Cancel',
            showDenyButton: true,
            denyButtonText: 'Waiting for Sparepart',
            confirmButtonColor: '#ffc107',
            denyButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // User chose "Pending" - status ON_HOLD
                showStatusUpdateModal(id, 'ON_HOLD', 'Pending Work Order', 'Provide reason for pending');
            } else if (result.isDenied) {
                // User chose "Menunggu Sparepart" - status WAITING_PARTS
                showStatusUpdateModal(id, 'WAITING_PARTS', 'Waiting for Sparepart', 'Provide details of the required spare parts');
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
            console.log('🔄 Fallback WO number from row:', woNumber);
        }
        
        console.log('🟢 Complete button clicked - Opening Complete Modal first');
        
        // Open Complete Work Order Modal (NOT Unit Verification directly)
        if (typeof window.openCompleteModal === 'function') {
            window.openCompleteModal(id, woNumber);
        } else {
            console.error('❌ openCompleteModal function not found');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to open Complete modal. Please refresh the page.',
                confirmButtonColor: '#d33'
            });
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
        console.log('🚨 updateWorkOrderStatus called with:', { id, status, message, stack: new Error().stack });
        
        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to change the work order status?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
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
                showAlert('error', 'Failed to update work order status');
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
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value && (status === 'CANCELLED' || status === 'ON_HOLD' || status === 'WAITING_PARTS')) {
                    return 'Notes are required for this status'
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
                    Swal.fire('Error', response.message || 'Failed to load work order data', 'error');
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
                
                Swal.fire('Error', errorMessage, 'error');
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
                    Swal.fire('Error', response.message || 'Failed to load work order data', 'error');
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
            title: 'Delete Confirmation',
            text: `Are you sure you want to delete Work Order ${woNumber}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('🗑️ Confirmed deletion, sending request...');
                $.ajax({
                    url: '<?= base_url('service/work-orders/delete') ?>/' + id,
                    type: 'DELETE',
                    beforeSend: function() {
                        console.log('🗑️ Sending delete request to:', '<?= base_url('service/work-orders/delete') ?>/' + id);
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
                        showAlert('error', 'Failed to delete work order');
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
            title: 'Delete Confirmation',
            text: `Are you sure you want to delete Work Order ${woNumber}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('service/work-orders/delete') ?>/' + id,
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
                            showAlert('error', response.message || 'Failed to delete work order');
                        } catch (e) {
                            showAlert('error', 'Failed to delete work order');
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
            
            // Work details fields
            if (workOrder.repair_description) $('#repair_description').val(workOrder.repair_description);
            if (workOrder.sparepart_used) $('#sparepart_used').val(workOrder.sparepart_used);
            if (workOrder.notes) $('#notes').val(workOrder.notes);
            
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
                        subcategorySelect.empty().append('<option value="">-- Select Subcategory (if any) --</option>');
                        
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
            
            // Handle Admin and Foreman
            if (workOrder.admin_id) {
                console.log('👔 Setting admin ID:', workOrder.admin_id);
                // Wait for dropdown to be loaded first
                setTimeout(function() {
                    $('#admin_id').val(workOrder.admin_id).trigger('change');
                }, 1500);
            }
            
            if (workOrder.foreman_id) {
                console.log('👷 Setting foreman ID:', workOrder.foreman_id);
                setTimeout(function() {
                    $('#foreman_id').val(workOrder.foreman_id).trigger('change');
                }, 1500);
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
            Swal.fire('Error', 'An error occurred while populating the edit form: ' + error.message, 'error');
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
        
        // Fix Priority Badge - ensure it displays properly with correct class
        let priorityBadge = data.priority_badge || '<span class="badge bg-secondary">-</span>';
        // Make sure badge has proper size
        if (!priorityBadge.includes('badge')) {
            priorityBadge = `<span class="badge bg-secondary">${priorityBadge}</span>`;
        }
        $('#viewWoPriority').html(priorityBadge);
        
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
            container.text('-');
            $('#unitAccessoriesInline').hide();
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
        
        console.log('🔧 Raw spareparts data:', spareparts, typeof spareparts);
        
        if (spareparts && spareparts.length > 0) {
            let html = '';
            spareparts.forEach(function(sparepart, index) {
                console.log(`  📦 Item ${index + 1}:`, {
                    name: sparepart.name,
                    item_type: sparepart.item_type,
                    is_from_warehouse: sparepart.is_from_warehouse,
                    is_used: sparepart.is_used,
                    used_quantity: sparepart.used_quantity,
                    notes: sparepart.notes
                });
                
                const qtyBrought = (sparepart.qty || sparepart.quantity_brought || 0) + ' ' + (sparepart.satuan || 'pcs');
                const qtyUsed = sparepart.used_quantity || '-';
                
                // Item Type Badge
                const itemType = sparepart.item_type || 'sparepart';
                let typeBadge = '';
                if (itemType === 'tool') {
                    typeBadge = '<span class="badge bg-secondary">🔧 Tool</span>';
                } else {
                    typeBadge = '<span class="badge bg-primary">⚙ Sparepart</span>';
                }
                
                // Item name with source indicator
                let itemName = sparepart.name || sparepart.desc_sparepart || sparepart.sparepart_name || '-';
                const isFromWarehouse = sparepart.is_from_warehouse !== undefined ? parseInt(sparepart.is_from_warehouse) : 1;
                if (isFromWarehouse === 0) {
                    itemName += ' <span class="badge bg-warning text-dark">♻ Non-WH</span>';
                }
                
                // Determine usage status badge
                let statusBadge = '<span class="badge bg-secondary">Pending</span>';
                if (sparepart.is_used !== undefined && sparepart.is_used !== null) {
                    if (sparepart.is_used == 1 || sparepart.is_used === true) {
                        statusBadge = '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Used</span>';
                    } else if (sparepart.is_used == 0 || sparepart.is_used === false) {
                        statusBadge = '<span class="badge bg-warning"><i class="fas fa-undo me-1"></i>Returned</span>';
                    }
                }
                
                console.log(`    ➡️ Type: ${itemType}, Status: ${statusBadge}`);
                
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
        $('#workOrderFormTitle').html('<i class="fas fa-plus-circle me-2"></i>New Work Order');
        $('#workOrderForm').attr('action', '<?= base_url('service/work-orders/store') ?>');
        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> Save Work Order');
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
                $('#unit_search_results').html('<div class="list-group-item"><i class="fas fa-spinner fa-spin"></i> Searching...</div>').show();
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
                    $('#unit_search_results').html('<div class="list-group-item text-muted">No units found</div>').show();
                }
            },
            error: function() {
                $('#unit_search_results').html('<div class="list-group-item text-danger">Error searching units</div>').show();
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
            $('#workOrderFormTitle').html('<i class="fas fa-plus-circle me-2"></i>New Work Order');
            
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
    
    // Prevent duplicate mechanic selection
    $(document).on('change', '#mechanic_1, #mechanic_2', function() {
        const mechanic1 = $('#mechanic_1').val();
        const mechanic2 = $('#mechanic_2').val();
        
        if (mechanic1 && mechanic2 && mechanic1 === mechanic2) {
            Swal.fire({
                icon: 'warning',
                title: 'Duplikasi Mekanik',
                text: 'Tidak dapat memilih mekanik yang sama untuk Mechanic 1 dan Mechanic 2!',
                confirmButtonText: 'OK'
            });
            // Clear the dropdown that was just changed
            $(this).val('').trigger('change');
        }
    });
    
    // Prevent duplicate helper selection
    $(document).on('change', '#helper_1, #helper_2', function() {
        const helper1 = $('#helper_1').val();
        const helper2 = $('#helper_2').val();
        
        if (helper1 && helper2 && helper1 === helper2) {
            Swal.fire({
                icon: 'warning',
                title: 'Duplikasi Helper',
                text: 'Tidak dapat memilih helper yang sama untuk Helper 1 dan Helper 2!',
                confirmButtonText: 'OK'
            });
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

    // Units Dropdown Management
    function loadUnitsDropdown() {
        console.log('🔄 Loading units dropdown...');
        const unitSelect = $('#unit_id');
        
        
        $.ajax({
            url: '<?= base_url('service/work-orders/units-dropdown') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('📦 Units response:', response);
                
                if (response.success && response.data) {
                    unitSelect.empty().append('<option value="">-- Select Unit --</option>');
                    
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
                        unitSelect.append('<option value="">No units available</option>');
                        console.warn('⚠️ No units found in response');
                    }
                    
                    // Initialize Select2 with search - SIMPLE & DIRECT
                    setTimeout(function() {
                        try {
                            // Always initialize (we already destroyed above if needed)
                            unitSelect.select2({
                                placeholder: '-- Select Unit --',
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#workOrderModal'),
                                minimumInputLength: 0, // Enable search immediately
                                minimumResultsForSearch: 0, // Always show search box
                                language: {
                                    noResults: function() { return "No results found"; },
                                    searching: function() { return "Searching..."; }
                                }
                            });
                            console.log('✅ Select2 initialized for unit dropdown with search,', response.data.length, 'options');
                        } catch (e) {
                            console.error('❌ Error initializing Select2:', e);
                        }
                    }, 150);
                } else {
                    unitSelect.empty().append('<option value="">Error: ' + (response.message || 'Failed to load data') + '</option>');
                    console.error('❌ Error loading units:', response.message || 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error loading units:', error);
                console.error('❌ Status:', status);
                console.error('❌ Response:', xhr.responseText);
                unitSelect.empty().append('<option value="">Error loading unit data</option>');
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
    function loadStaffDropdownByArea(staffRole, targetId, areaId) {
        console.log(`🔄 Loading ${staffRole} for ${targetId}, area: ${areaId}`);
        
        $.ajax({
            url: '<?= base_url('service/work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: { 
                staff_role: staffRole,
                area_id: areaId // Filter by area
            },
            success: function(response) {
                console.log(`📦 ${staffRole} response for ${targetId}:`, response);
                
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
                    
                    console.log(`✅ ${staffRole} loaded: ${response.data.length} items for ${targetId}`);
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
        
        $.ajax({
            url: '<?= base_url('service/work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: { staff_role: staffRole },
            success: function(response) {
                
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
                    
                    // Re-initialize Select2 if not already initialized
                    if (!staffSelect.hasClass('select2-hidden-accessible')) {
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
                        (targetId === 'mechanic_1' ? '-- Select Mechanic 1 --' : '-- Select Mechanic 2 (Optional) --') :
                        (targetId === 'helper_1' ? '-- Select Helper 1 --' : '-- Select Helper 2 (Optional) --');
                    
                    staffSelect.empty().append(`<option value="">${placeholderText}</option>`);
                }
            },
            error: function(xhr, status, error) {
                console.error(`❌ AJAX Error loading ${staffRole} staff:`, error);
                
                // Add placeholder even on error
                const staffSelect = $('#' + targetId);
                let placeholderText = staffRole === 'MECHANIC' ? 
                    (targetId === 'mechanic_1' ? '-- Select Mechanic 1 --' : '-- Select Mechanic 2 (Optional) --') :
                    (targetId === 'helper_1' ? '-- Select Helper 1 --' : '-- Select Helper 2 (Optional) --');
                
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

    // Load admin and foreman dropdowns based on area
    function loadAreaStaff(areaId) {
        console.log('🔄 Loading area staff for area ID:', areaId);
        
        // Clear dropdowns
        $('#admin_id').html('<option value="">-- Select Admin --</option>');
        $('#foreman_id').html('<option value="">-- Select Foreman --</option>');
        $('#pic_name').val('');
        
        if (!areaId) {
            return;
        }
        
        $.ajax({
            url: '<?= base_url('service/work-orders/get-area-staff') ?>',
            type: 'POST',
            data: { area_id: areaId },
            success: function(response) {
                console.log('📦 Area staff response:', response);
                
                if (response.success) {
                    // Populate admin dropdown
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
                    
                    // Populate foreman dropdown
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
                    
                    // Load mechanic and helper dropdowns for this area
                    loadStaffDropdownByArea('MECHANIC', 'mechanic_1', areaId);
                    loadStaffDropdownByArea('MECHANIC', 'mechanic_2', areaId);
                    loadStaffDropdownByArea('HELPER', 'helper_1', areaId);
                    loadStaffDropdownByArea('HELPER', 'helper_2', areaId);
                    
                    console.log('✅ Area staff loaded successfully');
                } else {
                    console.error('❌ Error loading area staff:', response.message);
                    $('#admin_id').html('<option value="">Error loading staff</option>');
                    $('#foreman_id').html('<option value="">Error loading staff</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading area staff:', error);
                console.error('❌ Response:', xhr.responseText);
                $('#admin_id').html('<option value="">Error loading staff</option>');
                $('#foreman_id').html('<option value="">Error loading staff</option>');
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
     * Function addSparepartRow - Dynamic input: Dropdown untuk Sparepart, Text Input untuk Tool
     * Tool items manual input (tidak dari database)
     */
    addSparepartRow = function(sparepartData = null) {
        sparepartRowCount++;
        console.log(`🔧 Adding item row ${sparepartRowCount}`);
        
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
                        <!-- Sparepart Dropdown (Default) -->
                        <select class="form-select form-select-sm" 
                                name="sparepart_name[]" 
                                id="sparepart_${sparepartRowCount}" 
                                required>
                            <option value="">-- Select Sparepart --</option>
                        </select>
                        <!-- Tool Text Input (Hidden by default) -->
                        <input type="text" 
                               class="form-control form-control-sm d-none" 
                               name="sparepart_name[]" 
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
                    <!-- Source Toggle -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="is_from_warehouse[]" 
                               id="warehouse_${sparepartRowCount}" 
                               value="1" 
                               checked 
                               onchange="toggleSourceLabel(this)">
                        <label class="form-check-label small" for="warehouse_${sparepartRowCount}">
                            <span class="badge bg-success warehouse-badge">
                                <i class="fas fa-warehouse"></i> WH
                            </span>
                            <span class="badge bg-warning text-dark non-warehouse-badge d-none">
                                <i class="fas fa-recycle"></i> Bekas
                            </span>
                        </label>
                    </div>
                </td>
                <td>
                    <!-- Notes/Keterangan -->
                    <input type="text" 
                           class="form-control form-control-sm" 
                           name="sparepart_notes[]" 
                           placeholder="Optional notes..." 
                           maxlength="255">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm removeSparepartRow">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#sparepartTableBody').append(row);
        
        // Initialize Select2 for sparepart dropdown
        const sparepartSelect = $(`#sparepart_${sparepartRowCount}`);
        
        // Populate sparepart dropdown data
        if (window.sparepartsData && Array.isArray(window.sparepartsData) && window.sparepartsData.length > 0) {
            window.sparepartsData.forEach(function(sparepart) {
                const sparepartValue = sparepart.text || sparepart.nama_sparepart || sparepart.desc_sparepart || '';
                const sparepartLabel = sparepart.text || sparepart.nama_sparepart || sparepart.desc_sparepart || '';
                if (sparepartValue) {
                    sparepartSelect.append(`<option value="${sparepartValue}">${sparepartLabel}</option>`);
                }
            });
            console.log(`✅ Added ${window.sparepartsData.length} spareparts to dropdown #sparepart_${sparepartRowCount}`);
        }
        
        // Initialize Select2 with delay
        setTimeout(function() {
            try {
                if (!sparepartSelect.hasClass('select2-hidden-accessible')) {
                    sparepartSelect.select2({
                        placeholder: '-- Select Sparepart --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#workOrderModal'),
                        minimumInputLength: 0,
                        minimumResultsForSearch: 0
                    });
                    console.log(`✅ Select2 initialized for sparepart_${sparepartRowCount}`);
                }
            } catch (error) {
                console.error(`❌ Error initializing Select2:`, error);
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
                
                // Set item name
                const itemName = sparepartData.sparepart_name || sparepartData.name;
                if (itemName) {
                    if (sparepartData.item_type === 'tool') {
                        $(`#tool_input_${sparepartRowCount}`).val(itemName);
                    } else {
                        if (sparepartSelect.find(`option[value="${itemName}"]`).length === 0) {
                            sparepartSelect.append(`<option value="${itemName}">${itemName}</option>`);
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
                
                console.log(`✅ Populated item row ${sparepartRowCount}`);
            } catch (error) {
                console.error('❌ Error populating row:', error);
            }
        }
        
        return sparepartSelect;
    };
    
    /**
     * Switch Item Input - Toggle between Dropdown (Sparepart) and Text Input (Tool)
     */
    window.switchItemInput = function(rowId, itemType = null) {
        const typeSelect = $(`#item_type_${rowId}`);
        const type = itemType || typeSelect.val();
        const sparepartDropdown = $(`#sparepart_${rowId}`);
        const toolInput = $(`#tool_input_${rowId}`);
        
        console.log(`🔄 Switching item input for row ${rowId} to type: ${type}`);
        
        if (type === 'tool') {
            // Show text input, hide dropdown
            sparepartDropdown.addClass('d-none').removeAttr('required');
            toolInput.removeClass('d-none').attr('required', 'required');
            
            // Destroy Select2 if exists
            if (sparepartDropdown.hasClass('select2-hidden-accessible')) {
                sparepartDropdown.select2('destroy');
            }
            
            console.log(`✅ Switched to TOOL input (text) for row ${rowId}`);
        } else {
            // Show dropdown, hide text input
            toolInput.addClass('d-none').removeAttr('required');
            sparepartDropdown.removeClass('d-none').attr('required', 'required');
            
            // Re-initialize Select2
            if (!sparepartDropdown.hasClass('select2-hidden-accessible')) {
                sparepartDropdown.select2({
                    placeholder: '-- Select Sparepart --',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#workOrderModal'),
                    minimumInputLength: 0,
                    minimumResultsForSearch: 0
                });
            }
            
            console.log(`✅ Switched to SPAREPART dropdown for row ${rowId}`);
        }
    };
    
    /**
     * Toggle Source Label Function - Switch between Warehouse and Bekas badge
     */
    window.toggleSourceLabel = function(checkbox) {
        const row = $(checkbox).closest('tr');
        const warehouseBadge = row.find('.warehouse-badge');
        const nonWarehouseBadge = row.find('.non-warehouse-badge');
        
        if (checkbox.checked) {
            warehouseBadge.removeClass('d-none');
            nonWarehouseBadge.addClass('d-none');
        } else {
            warehouseBadge.addClass('d-none');
            nonWarehouseBadge.removeClass('d-none');
        }
    };
    
    console.log('✅ Item management system loaded - Spareparts (dropdown) & Tools (manual input)');
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
<?php include 'complete_work_order_modal.php'; ?>

<?= $this->endSection() ?>


