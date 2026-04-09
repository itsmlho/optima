<?= $this->extend('layouts/base') ?>

<?php
/**
 * Customer Management Module
 * 
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 * 
 * Quick Reference:
 * - Status ACTIVE    → <span class="badge badge-soft-green">ACTIVE</span>
 * - Status INACTIVE  → <span class="badge badge-soft-red">INACTIVE</span>
 * - Counter/Info     → <span class="badge badge-soft-blue">247</span>
 * - Warning/Pending  → <span class="badge badge-soft-yellow">Pending</span>
 * - Danger/Expired   → <span class="badge badge-soft-red">Expired</span>
 * 
 * See optima-pro.css line ~2030 for complete badge standards
 */

// Load global permission helper
helper('global_permission');

// Get permissions for marketing module
$permissions = get_global_permission('marketing');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>


<?= $this->section('content') ?>

<!-- Date Range Filter -->
<div class="row mt-3">
    <div class="col-md-12 text-end">
        <div class="d-inline-block">
            <?= view('components/date_range_filter', ['id' => 'customerDateRangePicker']) ?>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-customers">0</div>
                        <div class="text-muted"><?= lang('Marketing.total_customers') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-success-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-person-check stat-icon text-success"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-active-customers">0</div>
                        <div class="text-muted"><?= lang('Marketing.active_customers') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-file-earmark-text stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-contracts">0</div>
                        <div class="text-muted"><?= lang('Marketing.total_contracts') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-info-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-boxes stat-icon text-info"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-units">0</div>
                        <div class="text-muted"><?= lang('Dashboard.total_units') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="card mb-3">
    <div class="card-body p-0">
        <ul class="nav nav-tabs customer-status-tabs" id="customerStatusTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active tab-all" id="tab-all" data-status="all" type="button" role="tab">
                    <i class="bi bi-list-ul me-2"></i><?= lang('Marketing.all_customers') ?? lang('Common.all') ?> <?= lang('Marketing.customers') ?>
                    <span class="badge badge-tab-all ms-2" id="count-all">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-active" id="tab-active" data-status="active" type="button" role="tab">
                    <i class="bi bi-check-circle me-2"></i><?= lang('Common.active') ?>
                    <span class="badge badge-tab-active ms-2" id="count-active">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-inactive" id="tab-inactive" data-status="inactive" type="button" role="tab">
                    <i class="bi bi-x-circle me-2"></i><?= lang('Common.inactive') ?>
                    <span class="badge badge-tab-inactive ms-2" id="count-inactive">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-no-contract" id="tab-no-contract" data-status="no_contract" type="button" role="tab">
                    <i class="bi bi-exclamation-triangle me-2"></i>No Active Contract
                    <span class="badge badge-tab-no-contract ms-2" id="count-no-contract">0</span>
                </button>
            </li>
        </ul>
        <small class="text-muted d-block px-3 py-2 border-top">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Tip:</strong> Use "No Active Contract" tab to find customers that can be set to INACTIVE
        </small>
    </div>
</div>

<!-- Customer Table Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-people me-2 text-primary"></i>
                Customer Management
            </h5>
            <p class="text-muted small mb-0">
                Manage customer profiles, contracts, and track unit deployments
                <span class="ms-2 text-info">
                    <i class="bi bi-keyboard me-1"></i>
                    <small>Tip: Click row or press Tab + Enter to view details</small>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <?= ui_button('refresh', lang('App.refresh'), [
                'onclick' => 'refreshData()',
                'size' => 'sm',
                'color' => 'outline-secondary'
            ]) ?>
            
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v me-1"></i>Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('marketing/kontrak') ?>" target="_blank">
                            <i class="fas fa-file-contract text-primary me-2"></i>View All Contracts
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('marketing/quotations') ?>" target="_blank">
                            <i class="fas fa-file-invoice text-info me-2"></i>Quotations
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <?php if ($can_export): ?>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('marketing/export_customer') ?>">
                            <i class="fas fa-file-excel text-success me-2"></i>Export Data
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            
            <?= ui_button('add', lang('Marketing.add_customer'), [
                'onclick' => 'openAddCustomerModal()',
                'size' => 'sm'
            ]) ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="customerTable" class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Customer Code</th>
                        <th>Customer Name</th>
                        <th>Locations</th>
                        <th>Total Contracts</th>
                        <th>Active Contracts</th>
                        <th>Total Units</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data loaded via DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Customer Detail Modal -->
<div class="modal fade modal-wide" id="customerDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="bi bi-building me-2"></i><span id="customerName">Customer Details</span>
                    </h5>
                    <small class="text-muted" id="customerCode"></small>
                </div>
                <div class="d-flex gap-2">
                    <?= ui_button('print', 'Print PDF', [
                        'id' => 'printCustomerPDF',
                        'size' => 'sm',
                        'title' => 'Print PDF Report'
                    ]) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-3" id="customerDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="company-tab" data-bs-toggle="tab" data-bs-target="#company-content" type="button">
                            <i class="fas fa-building me-1"></i>Company Info
                        </button>
                    </li>          
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations-content" type="button">
                            <i class="fas fa-map-marker-alt me-1"></i>Locations (<span id="locationCount">0</span>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contracts-tab" data-bs-toggle="tab" data-bs-target="#contracts-content" type="button">
                            <i class="fas fa-file-contract me-1"></i>Contracts & PO (<span id="contractCount">0</span>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity-content" type="button">
                            <i class="fas fa-history me-1"></i>Activity Log
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="customerDetailTabContent">
                    <!-- Company Info Tab - FLATTENED STRUCTURE -->
                    <div class="tab-pane fade show active" id="company-content" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-building me-2"></i>Company Information
                                </h6>
                                <dl class="row" id="companyInfo">
                                    <div class="text-center text-muted">Loading...</div>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-chart-bar me-2"></i>Statistics
                                </h6>
                                <dl class="row" id="customerStats">
                                    <div class="text-center text-muted">Loading...</div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Locations Tab -->
                    <div class="tab-pane fade" id="locations-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><strong>Customer Locations</strong></h6>
                            <?= ui_button('add', 'Add Location', [
                                'onclick' => 'openAddLocationModal()',
                                'size' => 'sm'
                            ]) ?>
                        </div>
                        <div class="row" id="locationsList">
                            <div class="text-center text-muted">Loading locations...</div>
                        </div>
                    </div>

                    <!-- Contracts & PO Tab - LIMIT 5 + VIEW ALL BUTTON -->
                    <div class="tab-pane fade contracts-tab-pane" id="contracts-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><strong>Recent Contracts & PO</strong> <small class="text-muted">(Latest 5)</small></h6>
                            <div>
                                <a href="#" class="btn btn-primary btn-sm" id="btnManageContracts" onclick="event.preventDefault(); viewCustomerContracts()">
                                    <i class="fas fa-cogs me-1"></i>Kelola Semua Kontrak
                                </a>
                            </div>
                        </div>
                        <div class="alert alert-info alert-sm mb-3">
                            <i class="fas fa-info-circle me-2"></i>Showing latest 5 contracts for quick access. <a href="#" onclick="event.preventDefault(); viewCustomerContracts()" class="alert-link">View all for this customer →</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="contractsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-type">Type</th>
                                        <th class="col-contract">Contract/PO Number</th>
                                        <th class="col-location">Location</th>
                                        <th class="col-period">Period</th>
                                        <th class="col-units text-center">Units</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-actions text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Loading contracts...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Activity Log Tab - NEW -->
                    <div class="tab-pane fade" id="activity-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><strong>Customer Activity History</strong></h6>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm form-select-auto-width" id="activityFilter">
                                    <option value="all">All Activities</option>
                                    <option value="contract">Contracts</option>
                                    <option value="quotation">Quotations</option>
                                    <option value="delivery">Deliveries</option>
                                    <option value="location">Locations</option>
                                </select>
                            </div>
                        </div>
                        <div id="activityTimeline">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-spinner fa-spin me-2"></i>Loading activity log...
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="me-auto d-flex gap-2">
                    <?= ui_button('edit', 'Edit Customer', [
                        'onclick' => 'openEditCustomerModal(currentCustomerId)',
                        'color' => 'primary',
                        'size' => 'sm'
                    ]) ?>
                    <?php if ($can_delete): ?>
                    <?= ui_button('delete', 'Delete Customer', [
                        'onclick' => 'deleteCustomer(currentCustomerId)',
                        'color' => 'danger',
                        'size' => 'sm'
                    ]) ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-white" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCustomerForm">
                <?= csrf_field() ?>
                <input type="hidden" id="edit_customer_id" name="customer_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_customer_code">Customer Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_customer_code" name="customer_code" required maxlength="20" readonly>
                        <small class="form-text text-muted">Customer code cannot be changed</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_customer_name">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_customer_name" name="customer_name" required maxlength="255">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_is_active">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_is_active" name="is_active" required>
                            <option value="1">ACTIVE</option>
                            <option value="0">INACTIVE</option>
                        </select>
                        <small class="form-text text-muted">Set to INACTIVE if no longer doing business with this customer</small>
                    </div>
                    
                    <div class="alert alert-info alert-sm">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Changing status to INACTIVE will not affect existing contracts or units. It only marks this customer as no longer active in the system.
                    </div>
                </div>
                <div class="modal-footer">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                    <?= ui_button('save', 'Update Customer', ['type' => 'submit']) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Contract Detail Modal -->
<div class="modal fade modal-wide" id="contractDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="bi bi-file-text me-2"></i><strong>Contract Details</strong>
                    </h5>
                    <small class="text-muted" id="contractSubtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Contract Information -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><strong>Contract Information</strong></h6>
                    </div>
                    <div class="card-body" id="contractInfo">
                        <div class="text-center text-muted">Loading...</div>
                    </div>
                </div>

                <!-- Locations & Units Section -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><strong>Locations & Units</strong></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="accordion" id="locationsAccordion">
                            <!-- Locations loaded dynamically -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="$('#contractDetailModal').modal('hide'); deleteContract(currentContractId);">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm" onclick="$('#contractDetailModal').modal('hide'); window.location.href='<?= base_url('marketing/kontrak/detail/') ?>' + currentContractId;">
                        <i class="fas fa-cog me-1"></i>Kelola Kontrak
                    </button>
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Unit Detail Modal -->
<div class="modal fade modal-wide" id="unitDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-truck me-2"></i><strong>Unit Details</strong>
                    </h5>
                    <small class="text-light" id="unitSubtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="unitDetailContent">
                <div class="text-center text-muted">Loading...</div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-white" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('Marketing.add_customer') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCustomerForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <!-- Customer Basic Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-building me-2 text-primary"></i>Customer Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_code">Customer Code <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="customer_code" name="customer_code" required maxlength="20" placeholder="e.g., CUST-0001">
                                        <button class="btn btn-outline-secondary" type="button" id="generateCustomerCode" title="Auto-generate unique code">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Unique identifier for this customer</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_name">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required maxlength="255" placeholder="e.g., PT Maju Jaya">
                                    <small class="form-text text-muted">Legal company name</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="default_billing_method">Default Billing Method</label>
                            <select class="form-control" id="default_billing_method" name="default_billing_method">
                                <option value="CYCLE" selected>30-Day Rolling Cycle</option>
                                <option value="PRORATE">Prorate to Month-End</option>
                                <option value="MONTHLY_FIXED">Fixed Monthly Date</option>
                            </select>
                            <small class="form-text text-muted">Default billing calculation for contracts with this customer</small>
                        </div>
                    </div>

                    <!-- Primary Location Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-geo-alt me-2 text-success"></i>Primary Location Details
                            <small class="text-muted ms-2">(Head Office)</small>
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location_name">Location Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="location_name" name="location_name" value="Head Office" required maxlength="100" placeholder="e.g., Head Office">
                                    <small class="form-text text-muted">Primary location name</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="primary_location_code">Location Code</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="primary_location_code" name="primary_location_code" maxlength="50" placeholder="Auto-generated">
                                        <button class="btn btn-outline-secondary" type="button" id="generateLocationCode" title="Auto-generate location code">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Auto-generated if left empty</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="2" maxlength="500" required placeholder="Complete address including street name, building number, etc."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" maxlength="100" required placeholder="e.g., Jakarta">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="province">Province <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="province" name="province" maxlength="100" required placeholder="e.g., DKI Jakarta">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="10" placeholder="e.g., 12345">
                                </div>
                            </div>
                        </div>
                        

                    </div>

                    <!-- Contact Person Information -->
                    <div class="mb-3">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-person me-2 text-info"></i>Contact Person
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_person">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="contact_person" name="contact_person" maxlength="255" required placeholder="e.g., Budi Santoso">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" maxlength="20" required placeholder="e.g., 021-1234567 or 08123456789">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" maxlength="100" placeholder="e.g., contact@company.com">
                                </div>
                            </div>
                        </div>
                    
                    <!-- Additional Notes -->
                    <div class="mb-3">
                        <label for="notes">
                            <i class="bi bi-journal-text me-1"></i>Additional Notes
                        </label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" maxlength="255" placeholder="Optional notes or special instructions about this customer"></textarea>
                        <small class="form-text text-muted">Any special instructions or remarks</small>
                    </div>

                    <!-- Defaults required by backend validation -->
                    <input type="hidden" name="is_active" value="1">
                    <input type="hidden" name="location_type" value="HEAD_OFFICE">
                    <input type="hidden" name="pic_position" value="">
                </div>
                <div class="modal-footer">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                    <?= ui_button('save', 'Save Customer', ['type' => 'submit']) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Contract Modal -->
<div class="modal fade modal-wide" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><?= lang('Marketing.add_contract') ?></h5>
                    <small class="text-muted">Langkah 1: Informasi Dasar Kontrak</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <?= csrf_field() ?>
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
                        <input type="hidden" name="customer_id" id="contractCustomerId">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= lang('Marketing.contract_number') ?>*</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="contract_number" required>
                                <button class="btn btn-outline-secondary" type="button" id="generateContractNumber" title="<?= lang('Marketing.generate_contract_number') ?>">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label"><?= lang('Marketing.client_po_number') ?></label><input type="text" class="form-control" name="po_number"></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= lang('Marketing.customer') ?>*</label>
                            <input type="text" class="form-control hidden-input" id="customerDisplay" readonly>
                            <select class="form-select" id="customerSelect" required>
                                <option value="">-- Pilih Customer --</option>
                            </select>
                            <small class="form-text text-muted" id="customerHelpText">Pilih customer terlebih dahulu</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= lang('Marketing.location') ?>*</label>
                            <select class="form-select" name="customer_location_id" id="locationSelect" required disabled>
                                <option value="">-- <?= lang('Marketing.select_customer_first') ?> --</option>
                            </select>
                            <small class="form-text text-muted"><?= lang('Marketing.location_after_customer') ?></small>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label"><?= lang('Marketing.start_date') ?>*</label><input type="date" class="form-control" name="start_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label"><?= lang('Marketing.end_date') ?>*</label><input type="date" class="form-control" name="end_date" required></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rental Classification</label>
                            <select class="form-select" name="rental_type">
                                <option value="CONTRACT" selected>Formal Contract</option>
                                <option value="PO_ONLY">PO-Based Only</option>
                                <option value="DAILY_SPOT">Daily/Spot Rental</option>
                            </select>
                            <small class="text-muted">How is this rental documented?</small>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label"><?= lang('Marketing.rental_type') ?></label>
                            <select class="form-select" name="jenis_sewa">
                                <option value="BULANAN" selected><?= lang('Marketing.monthly') ?></option>
                                <option value="HARIAN"><?= lang('Marketing.daily') ?></option>
                            </select>
                            <small class="text-muted">Billing period</small>
                        </div>
                        <div class="col-12 mb-3"><label class="form-label"><?= lang('Marketing.notes') ?></label><textarea class="form-control" name="catatan" rows="3" placeholder="<?= lang('Marketing.additional_notes_optional') ?>"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="submit_action" id="submitAction" value="save_and_spec">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                    <?= ui_button('save', 'Simpan Kontrak', ['id' => 'btnSaveOnly']) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLocationModalLabel"><?= lang('Marketing.add_location') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addLocationForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="locationCustomerId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loc_location_name">Location Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_location_name" name="location_name" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loc_location_code">Location Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="loc_location_code" name="location_code" maxlength="50">
                                    <button class="btn btn-outline-secondary" type="button" id="generateLocationCodeModal" title="Generate Location Code">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Optional - will be auto-generated if empty</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loc_contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="loc_contact_person" name="contact_person" maxlength="255">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loc_phone">Phone</label>
                                <input type="text" class="form-control" id="loc_phone" name="phone" maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loc_email">Email</label>
                                <input type="email" class="form-control" id="loc_email" name="email" maxlength="128">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="loc_address">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="loc_address" name="address" rows="3" maxlength="500" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_city">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_city" name="city" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_province">Province <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_province" name="province" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="loc_postal_code" name="postal_code" maxlength="10">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="loc_notes">Notes</label>
                        <textarea class="form-control" id="loc_notes" name="notes" rows="2" maxlength="255"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="loc_is_primary" name="is_primary" value="1">
                            <label class="form-check-label" for="loc_is_primary">Set as Primary Location</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                    <?= ui_button('save', 'Save Location', ['type' => 'submit']) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Contract Modal (Inline Edit) -->
<div class="modal fade modal-wide" id="editContractModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Contract
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editContractForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" id="editContractId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contract Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editContractNumber" name="no_kontrak" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rental Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="editRentalType" name="rental_type" required>
                                <option value="FORMAL_CONTRACT">Formal Contract</option>
                                <option value="PO_ONLY">PO Only</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editStartDate" name="tanggal_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editEndDate" name="tanggal_selesai" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rental Rate <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editRentalRate" name="harga_sewa" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="ACTIVE">Active</option>
                                <option value="PENDING">Pending</option>
                                <option value="EXPIRED">Expired</option>
                                <option value="CANCELLED">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <input type="hidden" id="editCustomerId" name="customer_id">
                    <input type="hidden" id="editLocationId" name="location_id">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal SPK dari Kontrak -->
<div class="modal fade modal-wide" id="spkFromKontrakModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= lang('Marketing.create_spk') ?></h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="spkFromKontrakForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="kontrak_id" id="spkKontrakId">
                    <input type="hidden" name="qoutation_specifications_id" id="spkSpesifikasiId">
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Marketing.spk_type') ?></label>
                        <select class="form-select" name="jenis_spk" id="spkJenisSpk" required>
                            <option value="UNIT" selected><?= lang('Marketing.spk_unit') ?></option>
                            <option value="ATTACHMENT"><?= lang('Marketing.spk_attachment') ?></option>
                        </select>
                    </div>
                    
                    <!-- Target Unit Section (hanya untuk ATTACHMENT) -->
                    <div id="attachmentTargetSection" class="section-hidden">
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Marketing.target_unit') ?> <span class="text-danger">*</span></label>
                            <select class="form-control" name="target_unit_id" id="spkTargetUnitId">
                                <option value="">- <?= lang('Marketing.select_target_unit') ?> -</option>
                            </select>
                            <div class="form-text"><?= lang('Marketing.select_unit_for_attachment') ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Marketing.replacement_reason') ?></label>
                            <textarea class="form-control" name="replacement_reason" id="spkReplacementReason" rows="2" 
                                      placeholder="<?= lang('Marketing.replacement_reason_placeholder') ?>"></textarea>
                            <div class="form-text"><?= lang('Marketing.explain_replacement') ?></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Marketing.customer') ?></label>
                        <input type="text" class="form-control" name="pelanggan" id="spkPelanggan" readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Marketing.pic') ?></label>
                        <input type="text" class="form-control" name="pic" id="spkPic" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Marketing.contact') ?></label>
                        <input type="text" class="form-control" name="kontak" id="spkKontak" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Marketing.location') ?></label>
                        <input type="text" class="form-control" name="lokasi" id="spkLokasi" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Marketing.delivery_plan') ?></label>
                        <input type="date" class="form-control" name="delivery_plan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Marketing.unit_quantity') ?> <small class="text-muted" id="jumlahUnitHint"></small></label>
                        <input type="number" class="form-control" name="jumlah_unit" id="spkJumlahUnit" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Marketing.notes') ?></label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="<?= lang('Marketing.additional_notes_for_spk') ?>"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                    <?= ui_button('submit', 'Buat SPK', ['type' => 'submit']) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<style>
/* Customer Table Keyboard Navigation Enhancement */
#customerTable tbody tr[tabindex]:focus {
    outline: 2px solid #0d6efd !important;
    outline-offset: -2px !important;
    background-color: rgba(13, 110, 253, 0.05) !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

#customerTable tbody tr[tabindex]:hover {
    background-color: rgba(13, 110, 253, 0.08) !important;
    transition: background-color 0.2s ease;
}

#customerTable tbody tr[tabindex]:focus:hover {
    background-color: rgba(13, 110, 253, 0.12) !important;
}

/* Customer Status Filter Tabs - Color Coded */
.customer-status-tabs {
    margin-bottom: 0;
}

.customer-status-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
    transition: all 0.2s ease;
}

.customer-status-tabs .nav-link:hover {
    color: #495057;
}

/* Tab Link Colors (text and underline) - Badge colors handled by optima-pro.css */
.customer-status-tabs .tab-all {
    color: #6c757d;
}

.customer-status-tabs .tab-all.active {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    font-weight: 600;
}

.customer-status-tabs .tab-active {
    color: #198754;
}

.customer-status-tabs .tab-active.active {
    color: #198754;
    border-bottom-color: #198754;
    font-weight: 600;
}

.customer-status-tabs .tab-inactive {
    color: #dc3545;
}

.customer-status-tabs .tab-inactive.active {
    color: #dc3545;
    border-bottom-color: #dc3545;
    font-weight: 600;
}

.customer-status-tabs .tab-no-contract {
    color: #fd7e14;
}

.customer-status-tabs .tab-no-contract.active {
    color: #fd7e14;
    border-bottom-color: #fd7e14;
    font-weight: 600;
}
</style>
<script>
let customerTable;
let currentCustomerId = null;
let currentCustomerName = null;
let currentContractId = null;

$(document).ready(function() {
    console.log('Customer Management ready');
    
    // Check if DataTables library loaded properly
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('❌ CRITICAL: DataTables library not loaded!');
        showNotification('DataTables library missing. Please refresh the page.', 'error');
        // Give user option to manually refresh instead of forcing it
        return;
    }
    
    // Initialize components with proper sequence
    setTimeout(function() {
        initializeCustomerTable();
    }, 100); // Small delay to ensure DOM is ready
    
    // Statistics will be auto-calculated by helper
    // No need to call loadStatistics() manually
    
    // Setup status filter tabs
    setupStatusFilterTabs();
    
    // Setup tab event handlers
    setupTabHandlers();
    
    // Reset form when Add Customer modal is closed
    $('#addCustomerModal').on('hidden.bs.modal', function () {
        console.log('🔄 Resetting Add Customer form on modal close');
        $('#addCustomerForm')[0].reset();
        clearFormErrors('#addCustomerForm');
    });
    
    // REMOVED: Manual modal event handlers (not needed with close-reopen pattern)
});

// Global status filter variable - restore from sessionStorage for back navigation UX
const CUSTOMER_STATE_KEY = 'customer_mgmt_state';
let currentStatusFilter = (function() {
    try {
        const saved = sessionStorage.getItem(CUSTOMER_STATE_KEY);
        if (saved) {
            const state = JSON.parse(saved);
            return state.statusFilter || 'all';
        }
    } catch (e) { /* ignore */ }
    return 'all';
})();

// Initialize DataTable using OptimaDataTable centralized system
function initializeCustomerTable() {
    console.log('🔄 Initializing Customer DataTable...');
    
    try {
        // Initialize using OptimaDataTable with minimal config
        customerTable = OptimaDataTable.init('#customerTable', {
            ajax: {
                url: '<?= base_url('marketing/customer-management/getCustomers') ?>',
                type: 'POST',
                timeout: 30000,  // 30 seconds timeout
                data: function(d) {
                    // Add status filter to AJAX request
                    d.status_filter = currentStatusFilter;
                },
                error: function(xhr, error, code) {
                    console.error('❌ Customer DataTable AJAX Error:', {
                        status: xhr.status,
                        error: error,
                        code: code,
                        responseText: xhr.responseText
                    });
                    
                    // Force hide processing indicator
                    $('.dataTables_processing').hide();
                    
                    // Show specific error message
                    let errorMsg = 'Gagal memuat data customer. ';
                    if (xhr.status === 0) {
                        errorMsg += 'Koneksi terputus atau timeout.';
                    } else if (xhr.status === 404) {
                        errorMsg += 'URL endpoint tidak ditemukan.';
                    } else if (xhr.status === 500) {
                        errorMsg += 'Server error.';
                    } else if (xhr.status === 403) {
                        errorMsg += 'Akses ditolak - cek permission.';
                    } else {
                        errorMsg += 'Silakan coba lagi atau klik tombol Refresh.';
                    }
                    
                    console.error('❌ DataTables AJAX Error:', errorMsg);
                    console.error('   HTTP Status:', xhr.status);
                    console.error('   Response:', xhr.responseText);
                    
                    showNotification(errorMsg, 'error');
                }
            },
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
            order: [[1, 'asc']],
            stateSave: true,
            stateDuration: 60 * 60, // 1 hour - preserves search, page, pagination on back navigation
            columns: [
                { 
                    data: 'customer_code',
                    render: (data, type, row, meta) => {
                        let t = data || '-';
                        if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                            t = OptimaSearch.highlightForMeta(meta, t);
                        }
                        return t;
                    }
                },
                { 
                    data: 'customer_name',
                    render: (data, type, row, meta) => {
                        let t = data || '';
                        if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                            t = OptimaSearch.highlightForMeta(meta, t);
                        }
                        return t;
                    }
                },
                { 
                    data: 'locations_count',
                    className: 'text-center',
                    orderable: false,
                    render: (data, type, row, meta) => {
                        let t = String(data || 0);
                        if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                            t = OptimaSearch.highlightForMeta(meta, t);
                        }
                        return t;
                    }
                },
                { 
                    data: 'contracts_count',
                    className: 'text-center',
                    orderable: false,
                    render: (data, type, row, meta) => {
                        let t = String(data || 0);
                        if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                            t = OptimaSearch.highlightForMeta(meta, t);
                        }
                        return t;
                    }
                },
                { 
                    data: 'active_contracts_count',
                    className: 'text-center',
                    orderable: false,
                    render: (data, type, row, meta) => {
                        let inner = String(data > 0 ? data : 0);
                        if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                            inner = OptimaSearch.highlightForMeta(meta, inner);
                        }
                        if (data > 0) {
                            return `<span class="badge badge-soft-green">${inner}</span>`;
                        }
                        return `<span class="badge badge-soft-gray">${inner}</span>`;
                    }
                },
                { 
                    data: 'total_units',
                    className: 'text-center',
                    orderable: false,
                    render: (data, type, row, meta) => {
                        let t = String(data || 0);
                        if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                            t = OptimaSearch.highlightForMeta(meta, t);
                        }
                        return t;
                    }
                },
                { 
                    data: 'is_active',
                    className: 'text-center',
                    render: (data) => {
                        if (data == 1 || data === 'ACTIVE') {
                            return '<span class="badge badge-soft-green"><i class="bi bi-check-circle me-1"></i>ACTIVE</span>';
                        } else {
                            return '<span class="badge badge-soft-red"><i class="bi bi-x-circle me-1"></i>INACTIVE</span>';
                        }
                    }
                },
                { 
                    data: 'created_at',
                    render: (data, type, row, meta) => {
                        let t = data ? new Date(data).toLocaleDateString('id-ID') : '-';
                        if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                            t = OptimaSearch.highlightForMeta(meta, t);
                        }
                        return t;
                    }
                }
            ],
            rowCallback: function(row, data) {
                // Make rows clickable AND keyboard accessible
                row.style.cursor = 'pointer';
                row.tabIndex = 0; // Make row focusable
                row.setAttribute('role', 'button');
                row.setAttribute('aria-label', 'Open details for ' + data.customer_name);
                
                // Click handler
                row.onclick = () => openCustomerDetail(data.id);
                
                // Keyboard handler (Enter or Space)
                row.onkeydown = (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openCustomerDetail(data.id);
                    }
                };
            },
            initComplete: function(settings, json) {
                console.log('✅ Customer DataTable initialized');
                console.log('📊 Total records:', json ? json.recordsTotal : 0);
                
                // Force hide any stuck processing indicator
                $('.dataTables_processing').hide();
                
                // Load real statistics from server (all data, not just current page)
                loadStatistics();
            },
            drawCallback: function() {
                // Re-initialize tooltips and other UI elements after table redraw
                if (typeof initializeTooltips === 'function') {
                    initializeTooltips();
                }
            }
        });
        
        // Initialize date range filter using OptimaDataTable filter
        const dateInputs = $('#customerDateRangePicker').find('input[type="date"]');
        if (dateInputs.length === 2) {
            OptimaDataTable.filters.initDateRangeFilter(
                '#customerTable',
                dateInputs.eq(0).attr('id') ? '#' + dateInputs.eq(0).attr('id') : dateInputs.eq(0),
                dateInputs.eq(1).attr('id') ? '#' + dateInputs.eq(1).attr('id') : dateInputs.eq(1),
                6 // created_at column index (was 7, area column removed)
            );
        }
        
        console.log('✅ Customer table ready with centralized config');
        
    } catch (error) {
        console.error('❌ DataTable initialization failed:', error);
        showNotification('Failed to initialize customer table. Please refresh the page.', 'error');
    }
}

// Update statistics from table data
function updateStatistics(data) {
    if (!Array.isArray(data)) return;
    
    const total = data.length;
    const active = data.filter(row => row.is_active == 1).length;
    const totalContracts = data.reduce((sum, row) => sum + (parseInt(row.contracts_count) || 0), 0);
    const totalUnits = data.reduce((sum, row) => sum + (parseInt(row.total_units) || 0), 0);
    
    $('#stat-total-customers').text(total.toLocaleString('id-ID'));
    $('#stat-active-customers').text(active.toLocaleString('id-ID'));
    $('#stat-total-contracts').text(totalContracts.toLocaleString('id-ID'));
    $('#stat-total-units').text(totalUnits.toLocaleString('id-ID'));
}

// Load statistics with optional date filter
function loadStatistics(startDate, endDate) {
    var data = {};
    if (startDate && endDate) {
        data = { start_date: startDate, end_date: endDate };
        console.log('📊 Loading customer statistics WITH filter:', data);
        showNotification('Filtering stats: ' + startDate + ' to ' + endDate, 'info');
    } else {
        console.log('📊 Loading customer statistics WITHOUT filter (all data)');
    }

    // Add CSRF token using getCsrfTokenData() for fresh dynamic token
    const csrfData = (typeof getCsrfTokenData === 'function') 
        ? getCsrfTokenData() 
        : { tokenName: window.csrfTokenName || 'csrf_test_name', tokenValue: window.csrfToken || '' };
    
    console.log('🔐 CSRF Data:', csrfData);
    
    if (csrfData.tokenName && csrfData.tokenValue) {
        data[csrfData.tokenName] = csrfData.tokenValue;
    }
    
    console.log('📤 Sending data:', data);

    $.ajax({
        url: '<?= base_url('marketing/customer-management/getCustomerStats') ?>',
        type: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': csrfData.tokenValue
        },
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                console.log('✅ Statistics loaded:', stats);
                $('#stat-total-customers').text(stats.total_customers || 0);
                $('#stat-active-customers').text(stats.active_customers || 0);
                $('#stat-total-contracts').text(stats.total_contracts || 0);
                $('#stat-total-units').text(stats.total_units || 0);
                
                // Update tab badges
                if (stats.tab_counts) {
                    $('#count-all').text(stats.tab_counts.all || 0);
                    $('#count-active').text(stats.tab_counts.active || 0);
                    $('#count-inactive').text(stats.tab_counts.inactive || 0);
                    $('#count-no-contract').text(stats.tab_counts.no_contract || 0);
                }
            } else {
                console.error('❌ Failed to load statistics:', response.message);
                // Fallback: Try to calculate from current table data
                calculateStatsFromTable();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ AJAX error loading statistics:', error);
            console.error('   Response:', xhr.responseText);
            // Fallback: Try to calculate from current table data
            calculateStatsFromTable();
        }
    });
}

/**
 * Client-Side Statistics Fallback Calculation
 * 
 * Calculates statistics from DataTable data when server-side stats fail.
 * This provides resilient UI that works even if API is down.
 * 
 * @since Priority 3 Enhancement - March 7, 2026
 */
function calculateStatsFromTable() {
    console.log('📊 Calculating statistics from table data (fallback mode)...');
    
    try {
        const table = $('#customerTable').DataTable();
        const allData = table.rows({ search: 'applied' }).data();
        
        if (!allData || allData.length === 0) {
            console.warn('⚠️ No table data available for fallback calculation');
            // Show zeros with warning indicator
            $('#stat-total-customers').text('0').addClass('text-warning');
            $('#stat-active-customers').text('0').addClass('text-warning');
            $('#stat-total-contracts').text('0').addClass('text-warning');
            $('#stat-total-units').text('0').addClass('text-warning');
            return;
        }
        
        // Calculate statistics from visible/filtered data
        let stats = {
            total_customers: allData.length,
            active_customers: 0,
            total_contracts: 0,
            total_units: 0
        };
        
        // Iterate through each customer row
        for (let i = 0; i < allData.length; i++) {
            const row = allData[i];
            
            // Count active customers (status column may vary - check for 'Aktif', 'Active', or status=1)
            if (row.status === 'Aktif' || row.status === 'Active' || row.status === 1 || row.status === '1') {
                stats.active_customers++;
            }
            
            // Sum contracts (if contract_count column exists)
            if (row.contract_count) {
                stats.total_contracts += parseInt(row.contract_count) || 0;
            }
            
            // Sum units (if unit_count column exists)
            if (row.unit_count) {
                stats.total_units += parseInt(row.unit_count) || 0;
            }
        }
        
        console.log('✅ Fallback statistics calculated:', stats);
        console.log('ℹ️ Note: Contract/unit counts may be inaccurate if columns not loaded');
        
        // Update UI with calculated stats
        $('#stat-total-customers').text(stats.total_customers).removeClass('text-warning');
        $('#stat-active-customers').text(stats.active_customers).removeClass('text-warning');
        $('#stat-total-contracts').text(stats.total_contracts).removeClass('text-warning');
        $('#stat-total-units').text(stats.total_units).removeClass('text-warning');
        
        // Show notification that fallback was used
        showNotification('Statistics calculated from table data (API unavailable)', 'warning');
        
    } catch (error) {
        console.error('❌ Failed to calculate fallback statistics:', error);
        // Final fallback - show zeros
        $('#stat-total-customers').text('0');
        $('#stat-active-customers').text('0');
        $('#stat-total-contracts').text('0');
        $('#stat-total-units').text('0');
        showNotification('Unable to load statistics', 'error');
    }
}

// Open customer detail with lazy loading optimization
function openCustomerDetail(customerId) {
    currentCustomerId = customerId;
    
    // Update Manage Contracts link to filter by this customer
    $('#btnManageContracts').attr('href', '<?= base_url('marketing/kontrak?customer=') ?>' + customerId);
    
    // Show modal immediately with loading state
    if (typeof OptimaPro !== 'undefined' && OptimaPro.showLoading) {
        OptimaPro.showLoading('Loading customer details...');
    }
    $('#customerDetailModal').modal('show');
    $('#customerDetailContent').html('<div class="text-center p-4"><i class="fas fa-spin fa-circle-notch text-primary fs-2"></i><p class="mt-3 text-muted">Fetching customer data...</p></div>');
    
    // Lazy load customer data
    setTimeout(function() {
        // Get fresh CSRF token
        const csrfData = (typeof getCsrfTokenData === 'function') 
            ? getCsrfTokenData() 
            : { tokenName: window.csrfTokenName || 'csrf_test_name', tokenValue: window.csrfToken || '' };
        
        console.log('🔍 Loading customer detail for ID:', customerId);
        console.log('🔐 CSRF Token:', csrfData.tokenValue ? csrfData.tokenValue.substring(0, 20) + '...' : 'MISSING');
        
        $.ajax({
            url: `<?= base_url('marketing/customer-management/getCustomerDetail') ?>/${customerId}`,
            type: 'GET',
            timeout: 15000, // Increased to 15s for complex queries
            headers: {
                'X-CSRF-TOKEN': csrfData.tokenValue,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('✅ Customer detail loaded successfully:', response);
                if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                    OptimaPro.hideLoading();
                }
                if (response.success) {
                    displayCustomerDetail(response.data);
                } else {
                    console.error('❌ Server returned error:', response.message);
                    $('#customerDetailContent').html(
                        '<div class="alert alert-danger">' +
                        '<i class="bi bi-exclamation-triangle me-2"></i>' +
                        (response.message || 'Failed to load customer details') +
                        '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error loading customer detail:');
                console.error('   Status:', status);
                console.error('   HTTP Code:', xhr.status);
                console.error('   Error:', error);
                console.error('   Response:', xhr.responseText);
                
                if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                    OptimaPro.hideLoading();
                }
                
                let errorMsg = 'Error loading customer details. ';
                if (xhr.status === 0) {
                    errorMsg += 'Network connection lost or timeout.';
                } else if (xhr.status === 403) {
                    errorMsg += 'Access denied. Please check your permissions.';
                } else if (xhr.status === 404) {
                    errorMsg += 'Customer data not found.';
                } else if (xhr.status === 500) {
                    errorMsg += 'Server error occurred.';
                } else if (status === 'timeout') {
                    errorMsg += 'Request timed out. The customer may have many contracts/units.';
                } else {
                    errorMsg += 'Please try again or contact support.';
                }
                
                $('#customerDetailContent').html(
                    '<div class="alert alert-danger">' +
                    '<i class="bi bi-exclamation-triangle me-2"></i>' +
                    errorMsg +
                    '<br><small class="text-muted">HTTP ' + xhr.status + ': ' + error + '</small>' +
                    '</div>'
                );
            }
        });
    }, 100); // Small delay to show loading state
}

// Display customer detail
function displayCustomerDetail(data) {
    // Extract data from response structure
    const customer = data.customer || data;
    const stats = data.stats || {};
    const locations = data.locations || [];
    const contracts = data.contracts || [];
    
    // Store customer name globally for contract modal
    currentCustomerName = customer.customer_name || '';
    
    // Update modal title
    $('#customerName').text(customer.customer_name || 'Customer Details');
    $('#customerCode').text(customer.customer_code || '');
    
    // Reset tab states - only affect modal tabs, not sidebar
    $('#customerDetailTabs .nav-link').removeClass('active');
    $('#customerDetailTabContent .tab-pane').removeClass('show active').hide();
    
    // Set company tab as active
    $('#company-tab').addClass('active');
    $('#company-content').addClass('show active').show();
    
    // Company Info - USING DESCRIPTION LIST FORMAT
    const companyHtml = `
        <dt class="col-sm-5 text-muted">Customer Code</dt>
        <dd class="col-sm-7 fw-bold">${customer.customer_code || 'N/A'}</dd>
        
        <dt class="col-sm-5 text-muted">Customer Name</dt>
        <dd class="col-sm-7">${customer.customer_name || 'N/A'}</dd>
        
        <dt class="col-sm-5 text-muted">Created</dt>
        <dd class="col-sm-7">${customer.created_at ? new Date(customer.created_at).toLocaleDateString('id-ID') : 'N/A'}</dd>
        
        <dt class="col-sm-5 text-muted">Last Updated</dt>
        <dd class="col-sm-7">${customer.updated_at ? new Date(customer.updated_at).toLocaleDateString('id-ID') : 'N/A'}</dd>
        
        <dt class="col-sm-5 text-muted">Status</dt>
        <dd class="col-sm-7">
            <span class="badge ${customer.is_active == 1 ? 'badge-soft-green' : 'badge-soft-red'}">
                ${customer.is_active == 1 ? 'ACTIVE' : 'INACTIVE'}
            </span>
        </dd>
    `;
    $('#companyInfo').html(companyHtml);
    
    // Customer Stats - USING DESCRIPTION LIST FORMAT
    const statsHtml = `
        <dt class="col-sm-6 text-muted">Total Locations</dt>
        <dd class="col-sm-6"><span class="badge badge-soft-blue">${stats.total_locations || 0}</span></dd>
        
        <dt class="col-sm-6 text-muted">Total Contracts</dt>
        <dd class="col-sm-6"><span class="badge badge-soft-green">${stats.total_contracts || 0}</span></dd>
        
        <dt class="col-sm-6 text-muted">Total PO Only</dt>
        <dd class="col-sm-6"><span class="badge badge-soft-yellow">${stats.total_po_only || 0}</span></dd>
        
        <dt class="col-sm-6 text-muted">Active Units</dt>
        <dd class="col-sm-6"><span class="badge badge-soft-blue">${stats.total_units || 0}</span></dd>
    `;
    $('#customerStats').html(statsHtml);
    
    // Update tab badges
    $('#locationCount').text(stats.total_locations || 0);
    $('#contractCount').text(stats.total_contracts || 0);
    
    // Load contracts when tab is clicked (will be loaded when tab is shown)
}

// Load customer contracts - LIMIT 5 RECENT
function loadCustomerContracts(customerId) {
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getCustomerContracts') ?>/${customerId}?limit=5`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayContracts(response.data);
                $('#contractCount').text(response.total || response.data.length);
            } else {
                $('#contractsTable tbody').html('<tr><td colspan="6" class="text-center text-muted">No contracts found</td></tr>');
            }
        },
        error: function() {
            $('#contractsTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading contracts</td></tr>');
        }
    });
}

// Display contracts with rental type badges
function displayContracts(contracts) {
    if (!contracts || contracts.length === 0) {
        $('#contractsTable tbody').html(
            '<tr><td colspan="7" class="text-center text-muted py-4">' +
            '<i class="fas fa-folder-open fa-2x mb-2 d-block"></i>' +
            'No contracts found for this customer' +
            '</td></tr>'
        );
        return;
    }
    
    let html = '';
    
    contracts.forEach(contract => {
        const statusBadge = getStatusBadge(contract.status);
        const rentalTypeBadge = getRentalTypeBadge(contract.rental_type);
        const contractNo = contract.rental_type === 'PO_ONLY' 
            ? contract.customer_po_number || contract.no_kontrak
            : contract.no_kontrak;
        
        // Check if contract is expiring soon (within 30 days)
        const endDate = new Date(contract.tanggal_selesai);
        const today = new Date();
        const daysLeft = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));
        const isExpiringSoon = daysLeft > 0 && daysLeft <= 30;
        const isExpired = daysLeft <= 0;
        
        // Expiry badge - following Optima badge standards
        let expiryBadge = '';
        if (isExpired) {
            expiryBadge = '<span class="badge badge-soft-red ms-2"><i class="bi bi-exclamation-circle me-1"></i>Expired</span>';
        } else if (isExpiringSoon) {
            expiryBadge = `<span class="badge badge-soft-orange ms-2"><i class="bi bi-clock me-1"></i>${daysLeft} days left</span>`;
        }
        
        // Show renewal option for ACTIVE, expiring, or expired contracts
        const showRenew = (contract.status === 'ACTIVE' || isExpiringSoon || isExpired);
        const showAmend = (contract.status === 'ACTIVE');
        
        html += `
            <tr class="contract-row">
                <td>${rentalTypeBadge}</td>
                <td>
                    <div class="fw-semibold text-dark">${contractNo}</div>
                    ${expiryBadge}
                </td>
                <td><small>${contract.lokasi || '-'}</small></td>
                <td><small>${contract.tanggal_mulai} ~ ${contract.tanggal_selesai}</small></td>
                <td class="text-center">
                    <span class="badge badge-soft-blue rounded-pill">${contract.total_units || 0}</span>
                </td>
                <td>${statusBadge}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" onclick="openContractDetail(${contract.id})" title="Lihat Ringkasan">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="<?= base_url('marketing/kontrak/detail/') ?>${contract.id}" class="btn btn-outline-secondary" title="Kelola Kontrak">
                            <i class="fas fa-cog"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#contractsTable tbody').html(html);
}

// Get rental type badge - following Optima badge standards
function getRentalTypeBadge(type) {
    const badges = {
        'CONTRACT': '<span class="badge badge-soft-blue"><i class="fas fa-file-contract me-1"></i>Contract</span>',
        'PO_ONLY': '<span class="badge badge-soft-cyan"><i class="fas fa-file-invoice me-1"></i>PO Only</span>',
        'DAILY_SPOT': '<span class="badge badge-soft-yellow"><i class="fas fa-calendar-day me-1"></i>Daily/Spot</span>'
    };
    return badges[type] || '<span class="badge badge-soft-gray">Unknown</span>';
}

// Load customer activity log
function loadCustomerActivity(customerId, filter = 'all') {
    $('#activityTimeline').html('<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin me-2"></i>Loading activity log...</div>');
    
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getCustomerActivity') ?>/${customerId}?filter=${filter}`,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                displayActivityTimeline(response.data);
            } else {
                $('#activityTimeline').html('<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No activity records found.</div>');
            }
        },
        error: function() {
            $('#activityTimeline').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading activity log.</div>');
        }
    });
}

// Display activity timeline
function displayActivityTimeline(activities) {
    let html = '<div class="timeline">';
    
    activities.forEach((activity, index) => {
        const iconMap = {
            'contract': 'fa-file-contract',
            'quotation': 'fa-file-invoice-dollar',
            'delivery': 'fa-truck',
            'location': 'fa-map-marker-alt',
            'customer': 'fa-building'
        };
        
        const colorMap = {
            'contract': 'primary',
            'quotation': 'info',
            'delivery': 'success',
            'location': 'warning',
            'customer': 'secondary'
        };
        
        const icon = iconMap[activity.type] || 'fa-circle';
        const color = colorMap[activity.type] || 'secondary';
        const date = new Date(activity.created_at).toLocaleString('id-ID');
        
        html += `
            <div class="timeline-item">
                <div class="timeline-marker bg-${color}">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <strong>${activity.title}</strong>
                        <small class="text-muted">${date}</small>
                    </div>
                    <p class="mb-0 text-muted">${activity.description}</p>
                    ${activity.user ? `<small class="text-muted"><i class="fas fa-user me-1"></i>${activity.user}</small>` : ''}
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    $('#activityTimeline').html(html);
}

// Custom Contract Actions Menu - Rendered at body level to avoid modal clipping
function showContractActions(event, contractId, contractNo, showRenew, showAmend) {
    event.stopPropagation();
    
    // Remove any existing menu
    $('.contract-actions-menu').remove();
    
    // Get button position
    const button = event.currentTarget;
    const rect = button.getBoundingClientRect();
    
    // Build action menu with full CRUD options
    let renewItem = showRenew ? `
                <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); $('.contract-actions-menu').remove(); window.location.href='<?= base_url('marketing/kontrak/detail/') ?>' + ${contractId} + '#renewal';">
                    <i class="fas fa-sync-alt text-success me-2"></i>Renewal
                </a>` : '';
    let amendItem = showAmend ? `
                <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); $('.contract-actions-menu').remove(); window.location.href='<?= base_url('marketing/kontrak/detail/') ?>' + ${contractId} + '#rate';">
                    <i class="fas fa-calculator text-warning me-2"></i>Change Rate
                </a>` : '';

    let menuHTML = `
        <div class="contract-actions-menu" style="position: fixed; top: ${rect.bottom + 5}px; left: ${rect.left - 180}px; z-index: 10500;">
            <div class="list-group shadow" style="min-width: 200px;">
                <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); openContractDetail(${contractId}); $('.contract-actions-menu').remove();">
                    <i class="fas fa-eye text-primary me-2"></i>Lihat Ringkasan
                </a>
                <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); $('.contract-actions-menu').remove(); window.location.href='<?= base_url('marketing/kontrak/detail/') ?>' + ${contractId};">
                    <i class="fas fa-file-alt text-info me-2"></i>Buka Detail Lengkap
                </a>
                <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); $('.contract-actions-menu').remove(); window.location.href='<?= base_url('marketing/kontrak/edit/') ?>' + ${contractId};">
                    <i class="fas fa-edit text-primary me-2"></i>Edit Kontrak
                </a>
                ${renewItem}
                ${amendItem}
                <div class="dropdown-divider m-0"></div>
                <a href="#" class="list-group-item list-group-item-action text-danger" onclick="event.preventDefault(); $('.contract-actions-menu').remove(); deleteContract(${contractId});">
                    <i class="fas fa-trash me-2"></i>Hapus Kontrak
                </a>
            </div>
        </div>`;
    
    // Append to body
    $('body').append(menuHTML);
    
    // Close menu when clicking outside
    setTimeout(() => {
        $(document).one('click', function() {
            $('.contract-actions-menu').remove();
        });
    }, 100);
}

// View all contracts for current customer (filtered)
function viewCustomerContracts() {
    if (currentCustomerId) {
        window.location.href = `<?= base_url('marketing/kontrak') ?>?customer_id=${currentCustomerId}`;
    } else {
        window.location.href = `<?= base_url('marketing/kontrak') ?>`;
    }
}

// Open contract detail
function openContractDetail(contractId) {
    currentContractId = contractId;
    
    $.ajax({
        url: `<?= base_url('marketing/kontrak/detail') ?>/${contractId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayContractDetail(response.data);
                $('#contractDetailModal').modal('show');
            }
        }
    });
}

// Display contract detail - CLEAN DESIGN
function displayContractDetail(contract) {
    $('#contractSubtitle').text(`${contract.no_kontrak} - ${contract.customer_name}`);
    
    const contractHtml = `
        <div class="row g-3">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><td><strong>No. Kontrak:</strong></td><td>${contract.no_kontrak || '-'}</td></tr>
                    <tr><td><strong>No. PO Customer:</strong></td><td>${contract.customer_po_number || '-'}</td></tr>
                    <tr><td><strong>Customer:</strong></td><td>${contract.customer_name || '-'}</td></tr>
                    <tr><td><strong>Lokasi:</strong></td><td>${contract.location_name || '-'}</td></tr>
                    <tr><td><strong>PIC:</strong></td><td>${contract.contact_person || '-'}</td></tr>
                    <tr><td><strong>Kontak:</strong></td><td>${contract.phone || '-'}</td></tr>
                    <tr><td><strong>Alamat:</strong></td><td>${contract.address || 'Alamat belum tersedia'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><td><strong>Classification:</strong></td><td>${getRentalTypeBadge(contract.rental_type)}</td></tr>
                    <tr><td><strong>Jenis Sewa:</strong></td><td><span class="badge badge-soft-blue">${contract.jenis_sewa || 'BULANAN'}</span></td></tr>
                    <tr><td><strong>Status:</strong></td><td>${getStatusBadge(contract.status)}</td></tr>
                    <tr><td><strong>Tanggal Mulai:</strong></td><td>${contract.tanggal_mulai || '-'}</td></tr>
                    <tr><td><strong>Tanggal Berakhir:</strong></td><td>${contract.tanggal_berakhir || '-'}</td></tr>
                    <tr><td><strong>Total Unit:</strong></td><td><span class="fw-bold text-primary" id="contractTotalUnits">${contract.total_units || 0}</span></td></tr>
                    <tr><td><strong>Nilai Total:</strong></td><td><span class="fw-bold text-success">Rp ${formatNumber(contract.nilai_total || 0)}</span></td></tr>
                    <tr><td><strong>Dibuat Oleh:</strong></td><td>${contract.dibuat_oleh_nama || '-'}</td></tr>
                    <tr><td><strong>Dibuat Pada:</strong></td><td>${contract.dibuat_pada || '-'}</td></tr>
                </table>
            </div>
        </div>
    `;
    $('#contractInfo').html(contractHtml);
    
    // Clear previous content when opening a new contract
    $('#locationsAccordion').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data unit...</div>');
    
    // Load units for this contract
    loadContractUnits(currentContractId);
}

// Load contract units grouped by location - using same endpoint as kontrak.php
function loadContractUnits(contractId) {
    console.log('🔍 Loading units for contract ID:', contractId);
    console.log('📡 Calling URL:', `<?= base_url('marketing/kontrak/units/') ?>${contractId}`);
    
    $.ajax({
        url: `<?= base_url('marketing/kontrak/units/') ?>${contractId}`,
        type: 'GET',
        success: function(response) {
            console.log('✅ Response received:', response);
            console.log('📦 Units data:', response.data);
            console.log('🔢 Units count:', response.count);
            
            if (response.success) {
                if (response.data && response.data.length > 0) {
                    displayUnitsAccordion(response.data);
                } else {
                    console.warn('⚠️ Response success but no units found');
                    $('#locationsAccordion').html('<div class="alert alert-warning">Belum ada unit yang terdaftar untuk kontrak ini.</div>');
                }
            } else {
                console.error('❌ Response success=false:', response.message);
                $('#locationsAccordion').html('<div class="alert alert-warning">Tidak ada unit ditemukan untuk kontrak ini.</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ AJAX Error:', {xhr, status, error});
            console.error('Response Text:', xhr.responseText);
            $('#locationsAccordion').html('<div class="alert alert-danger">Gagal memuat data unit.</div>');
        }
    });
}

// Display units in accordion by location - FIXED
function displayUnitsAccordion(units) {
    console.log('📦 Displaying units:', units);
    console.log('📦 First unit structure:', units[0]);
    
    if (!units || units.length === 0) {
        $('#locationsAccordion').html('<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Tidak ada unit ditemukan untuk kontrak ini.</div>');
        return;
    }
    
    // Group units by location
    const locationGroups = {};
    
    units.forEach(unit => {
        const locationKey = unit.lokasi || 'Lokasi Belum Ditentukan';
        
        if (!locationGroups[locationKey]) {
            locationGroups[locationKey] = {
                locationName: locationKey,
                units: []
            };
        }
        locationGroups[locationKey].units.push(unit);
    });
    
    let html = '';
    let index = 0;
    
    for (const [locationKey, locationData] of Object.entries(locationGroups)) {
        const locationUnits = locationData.units;
        
        html += `
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading${index}">
                    <button class="accordion-button ${index > 0 ? 'collapsed' : ''}" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#collapse${index}">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <div>
                            <strong>${locationData.locationName}</strong>
                        </div>
                        <span class="badge badge-soft-blue ms-auto">${locationUnits.length} Unit${locationUnits.length > 1 ? 's' : ''}</span>
                    </button>
                </h2>
                <div id="collapse${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                     data-bs-parent="#locationsAccordion">
                    <div class="accordion-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No Unit</th>
                                        <th>Serial Number</th>
                                        <th>Tipe</th>
                                        <th>Merk/Model</th>
                                        <th>Kapasitas</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
        `;
        
        locationUnits.forEach(unit => {
            // Use field names from Kontrak::getContractUnits response
            const unitId = unit.id || unit.id_inventory_unit;
            const tipe = unit.jenis_unit || '-';
            const merk = unit.merk || '-';
            const model = unit.model || '';
            const merkModel = (merk === '-' || merk === 'N/A') && (model === '' || model === 'N/A') ? '-' : `${merk} ${model}`.trim();
            const kapasitas = unit.kapasitas || '-';
            const status = unit.status || 'N/A';
            const statusId = unit.status_unit_id;
            const statusColor = getStatusColor(status, statusId);
            
            html += `
                <tr class="cursor-pointer" onclick="showUnitDetail(${unitId})" 
                    onmouseover="this.style.backgroundColor='#f8f9fa'" 
                    onmouseout="this.style.backgroundColor=''">
                    <td><strong>${unit.no_unit || '-'}</strong></td>
                    <td><code>${unit.serial_number || '-'}</code></td>
                    <td>${tipe}</td>
                    <td>${merkModel}</td>
                    <td>${kapasitas}</td>
                    <td><span class="text-${statusColor}"><i class="fas fa-circle me-1 icon-xs"></i>${status}</span></td>
                </tr>
            `;
        });
        
        html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;
        index++;
    }
    
    $('#locationsAccordion').html(html);
}

// Helper function to get status color (simplified)
function getStatusColor(status, statusId) {
    if (statusId) {
        if (statusId >= 1 && statusId <= 3) return 'info';
        if (statusId >= 4 && statusId <= 6) return 'success';
        if (statusId >= 7 && statusId <= 9) return 'primary';
        if (statusId >= 10) return 'warning';
    }
    
    if (!status) return 'secondary';
    
    const statusUpper = status.toUpperCase();
    if (statusUpper.includes('RENTAL') || statusUpper.includes('ACTIVE') || statusUpper.includes('IN USE')) {
        return 'primary';
    } else if (statusUpper.includes('DELIVERED') || statusUpper.includes('SOLD')) {
        return 'success';
    } else if (statusUpper.includes('MAINTENANCE') || statusUpper.includes('REPAIR') || statusUpper.includes('RETURN')) {
        return 'warning';
    } else if (statusUpper.includes('AVAILABLE') || statusUpper.includes('STOK') || statusUpper.includes('READY')) {
        return 'info';
    } else {
        return 'secondary';
    }
}

// Modal functions for customer, contract, and spesifikasi
function openAddCustomerModal() {
    // Reset form to clear all fields
    $('#addCustomerForm')[0].reset();
    
    // Clear previous validation states
    clearFormErrors('#addCustomerForm');
    
    // Generate customer code automatically
    generateCustomerCode();
    
    // Reset default values
    $('#location_name').val('Head Office');
    $('#default_billing_method').val('CYCLE');

    // Show modal
    $('#addCustomerModal').modal('show');
}

// Open Edit Customer Modal
function openEditCustomerModal(customerId) {
    if (!customerId) {
        showNotification('Customer ID is required', 'error');
        return;
    }
    
    console.log('🔧 Opening Edit Customer modal for ID:', customerId);
    
    // Show loading state
    if (typeof OptimaPro !== 'undefined' && OptimaPro.showLoading) {
        OptimaPro.showLoading('Loading customer data...');
    }
    
    // Fetch customer data
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getCustomerDetail') ?>/${customerId}`,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': (typeof getCsrfTokenData === 'function') ? getCsrfTokenData().tokenValue : window.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                OptimaPro.hideLoading();
            }
            
            if (response.success && response.data.customer) {
                const customer = response.data.customer;
                
                // Populate form fields
                $('#edit_customer_id').val(customer.id);
                $('#edit_customer_code').val(customer.customer_code);
                $('#edit_customer_name').val(customer.customer_name);
                $('#edit_is_active').val(customer.is_active || 1);
                
                // Clear previous errors
                clearFormErrors('#editCustomerForm');
                
                // Show edit modal di atas customerDetailModal (stacked)
                $('#editCustomerModal').css('z-index', 1065);
                $('#editCustomerModal').modal('show');
                
                // Fix backdrop z-index
                $('#editCustomerModal').one('shown.bs.modal', function() {
                    $('.modal-backdrop').last().css('z-index', 1064);
                });
                
                console.log('✅ Customer data loaded:', customer);
            } else {
                showNotification('Failed to load customer data: ' + (response.message || 'Unknown error'), 'error');
            }
        },
        error: function(xhr, status, error) {
            if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                OptimaPro.hideLoading();
            }
            console.error('❌ Error loading customer:', error);
            showNotification('Error loading customer data', 'error');
        }
    });
}

/**
 * Delete Customer with Validation
 * Checks for active contracts and units before allowing deletion
 */
function deleteCustomer(customerId) {
    if (!customerId) {
        showNotification('Customer ID is required', 'error');
        return;
    }
    
    console.log('🗑️ Attempting to delete customer ID:', customerId);
    
    if (typeof OptimaPro !== 'undefined' && OptimaPro.showLoading) {
        OptimaPro.showLoading('Checking customer data...');
    }
    
    // First, check if customer can be deleted (no active contracts/units)
    $.ajax({
        url: `<?= base_url('marketing/customer-management/checkCustomerDeletion') ?>/${customerId}`,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': (typeof getCsrfTokenData === 'function') ? getCsrfTokenData().tokenValue : window.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                OptimaPro.hideLoading();
            }
            
            if (response.can_delete) {
                // Customer can be deleted - show confirmation dialog
                showDeleteConfirmation(customerId, response.customer_name);
            } else {
                // Cannot delete - show blocking warning with details
                showDeletionBlockedWarning(response);
            }
        },
        error: function(xhr, status, error) {
            if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                OptimaPro.hideLoading();
            }
            console.error('❌ Error checking deletion:', error);
            showNotification('Error checking customer status', 'error');
        }
    });
}

/**
 * Show deletion confirmation dialog (when customer CAN be deleted)
 */
function showDeleteConfirmation(customerId, customerName) {
    OptimaConfirm.danger({
        title: 'Delete Customer?',
        html: `
            <div class="text-start">
                <p class="mb-3">Are you sure you want to delete this customer?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Customer:</strong> ${customerName || 'ID ' + customerId}
                </div>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    This action cannot be undone. All customer data including locations and contacts will be permanently deleted.
                </p>
            </div>
        `,
        confirmText: 'Yes, Delete',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            performCustomerDeletion(customerId);
        }
    });
}

/**
 * Show warning when deletion is blocked
 */
function showDeletionBlockedWarning(data) {
    let reasonsHtml = '<ul class="text-start mb-0">';
    
    if (data.active_contracts > 0) {
        reasonsHtml += `<li><strong>${data.active_contracts} active contract(s)</strong> - Must be terminated first</li>`;
    }
    
    if (data.units_at_location > 0) {
        reasonsHtml += `<li><strong>${data.units_at_location} unit(s) at customer location</strong> - Must be returned first</li>`;
    }
    
    if (data.total_contracts > 0 && data.active_contracts === 0) {
        reasonsHtml += `<li>${data.total_contracts} completed/terminated contract(s) (historical data)</li>`;
    }
    
    reasonsHtml += '</ul>';
    
    OptimaConfirm.generic({
        title: 'Cannot Delete Customer',
        html: `
            <div class="text-start">
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-x-circle me-2"></i>
                    <strong>This customer cannot be deleted</strong>
                </div>
                <p class="mb-2"><strong>Reasons:</strong></p>
                ${reasonsHtml}
                <hr>
                <p class="text-muted small mb-0">
                    <i class="bi bi-lightbulb me-1"></i>
                    <strong>What to do:</strong><br>
                    1. Terminate all active contracts<br>
                    2. Return all units to warehouse<br>
                    3. Then you can delete this customer
                </p>
            </div>
        `,
        icon: 'danger',
        confirmText: 'OK, I Understand',
        cancelText: null
    });
}

/**
 * Perform actual customer deletion
 */
function performCustomerDeletion(customerId) {
    if (typeof OptimaPro !== 'undefined' && OptimaPro.showLoading) {
        OptimaPro.showLoading('Deleting customer...');
    }
    
    const csrfData = (typeof getCsrfTokenData === 'function') 
        ? getCsrfTokenData() 
        : { tokenName: window.csrfTokenName || 'csrf_test_name', tokenValue: window.csrfToken || '' };
    
    $.ajax({
        url: `<?= base_url('marketing/customer-management/deleteCustomer') ?>/${customerId}`,
        type: 'POST',
        data: {
            [csrfData.tokenName]: csrfData.tokenValue
        },
        headers: {
            'X-CSRF-TOKEN': csrfData.tokenValue,
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                OptimaPro.hideLoading();
            }
            
            if (response.success) {
                OptimaNotify.success(response.message || 'Customer has been deleted successfully');
                
                // Close customer detail modal
                $('#customerDetailModal').modal('hide');
                
                // Reload table
                if (customerTable) {
                    customerTable.ajax.reload(null, false);
                }
                
                // Reload statistics
                loadStatistics();
            } else {
                showNotification(response.message || 'Failed to delete customer', 'error');
            }
        },
        error: function(xhr) {
            if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                OptimaPro.hideLoading();
            }
            
            let errorMsg = 'An error occurred while deleting customer';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showNotification(errorMsg, 'error');
        }
    });
}

// Generate customer code function
function generateCustomerCode() {
    // Generate format: CUST-YYYYMMDD-XXX
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const random = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    
    const customerCode = `CUST-${year}${month}${day}-${random}`;
    $('#customer_code').val(customerCode);
}

function generateLocationCode() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const random = String(Math.floor(Math.random() * 100)).padStart(2, '0');
    const locationCode = `LOC-${year}${month}${day}-${random}`;
    $('#primary_location_code').val(locationCode);
}

function generateLocationCodeModal() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const random = String(Math.floor(Math.random() * 100)).padStart(2, '0');
    const locationCode = `LOC-${year}${month}${day}-${random}`;
    $('#loc_location_code').val(locationCode);
}

function openAddContractModal() {
    // Reset form
    $('#addContractForm')[0].reset();
    
    // If opened from customer detail modal, show readonly customer name
    if (currentCustomerId && currentCustomerName) {
        // Set customer ID in hidden field
        $('#contractCustomerId').val(currentCustomerId);
        
        // Show readonly input with customer name, hide dropdown
        $('#customerDisplay').val(currentCustomerName).show();
        $('#customerSelect').hide().prop('required', false);
        $('#customerHelpText').hide();
        
        // Load locations for current customer
        $.ajax({
            url: `<?= base_url('marketing/customer-management/getCustomerLocations/') ?>${currentCustomerId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const locationSelect = $('#locationSelect');
                    locationSelect.empty().append('<option value="">-- Pilih Lokasi --</option>');
                    response.data.forEach(location => {
                        locationSelect.append(`<option value="${location.id}">${location.location_name}</option>`);
                    });
                    locationSelect.prop('disabled', false);
                }
            },
            error: function() {
                console.error('Error loading customer locations');
            }
        });
    } else {
        // Clear hidden field and show dropdown for manual selection
        $('#contractCustomerId').val('');
        $('#customerDisplay').hide();
        $('#customerSelect').show().prop('required', true);
        $('#customerHelpText').show();
        
        // Load customers for dropdown
        loadCustomers();
        
        $('#locationSelect').prop('disabled', true).empty().append('<option value="">-- <?= lang('Marketing.select_customer_first') ?> --</option>');
    }
    
    // Show modal
    $('#addContractModal').modal('show');
}

// Fix: Pindahkan modal ke body agar tidak terjebak di stacking context main-content
$(document).ready(function() {
    $('#addLocationModal').appendTo('body');
    $('#editCustomerModal').appendTo('body');
    
    // Saat modal child ditutup, pastikan body tetap modal-open karena customerDetailModal masih terbuka
    $('#addLocationModal, #editCustomerModal').on('hidden.bs.modal', function() {
        if ($('#customerDetailModal').hasClass('show')) {
            $('body').addClass('modal-open');
        }
    });
});

function openAddLocationModal() {
    if (!currentCustomerId) {
        showNotification('Silakan pilih customer terlebih dahulu', 'warning');
        return;
    }
    
    // Reset form
    clearFormErrors('#addLocationForm');
    $('#addLocationForm')[0].reset();
    $('#addLocationForm').removeData('location-id');
    $('#addLocationModal .modal-title').text('<?= lang('Marketing.add_location') ?>');
    $('#locationCustomerId').val(currentCustomerId);
    $('#loc_location_code').val('');
    
    // Show di atas customerDetailModal (stacked modal)
    $('#addLocationModal').css('z-index', 1065);
    $('#addLocationModal').modal('show');
    
    // Fix backdrop z-index agar di antara kedua modal
    $('#addLocationModal').on('shown.bs.modal', function() {
        // Backdrop terakhir = backdrop untuk addLocationModal
        $('.modal-backdrop').last().css('z-index', 1064);
    });
}

function deleteLocation(locationId, locationName, isPrimary) {
    // Build confirmation modal
    const primaryWarning = isPrimary 
        ? `<div class="alert alert-info mb-3"><i class="ri-information-line me-1"></i> Lokasi ini ditandai sebagai <strong>Primary</strong>. Jika dihapus, pastikan ada lokasi lain yang dijadikan primary.</div>` 
        : '';
    
    const confirmModalHtml = `
        <div class="modal fade" id="deleteLocationConfirmModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger-subtle">
                        <h5 class="modal-title"><i class="ri-delete-bin-line me-2"></i>Hapus Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus lokasi <strong>"${locationName}"</strong>?</p>
                        ${primaryWarning}
                        <div class="text-muted small"><i class="ri-error-warning-line me-1"></i>Tindakan ini tidak dapat dibatalkan.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="btnConfirmDeleteLocation">
                            <i class="ri-delete-bin-line me-1"></i>Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    
    // Remove old instance if exists
    $('#deleteLocationConfirmModal').remove();
    $('body').append(confirmModalHtml);
    
    const confirmModal = new bootstrap.Modal(document.getElementById('deleteLocationConfirmModal'));
    $('#deleteLocationConfirmModal').css('z-index', 1070);
    confirmModal.show();
    $('#deleteLocationConfirmModal').on('shown.bs.modal', function() {
        $('.modal-backdrop').last().css('z-index', 1069);
    });
    $('#deleteLocationConfirmModal').on('hidden.bs.modal', function() {
        $(this).remove();
        if ($('#customerDetailModal').hasClass('show')) {
            document.body.classList.add('modal-open');
        }
    });
    
    // Handle confirm click
    $('#btnConfirmDeleteLocation').off('click').on('click', function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i>Menghapus...');
        
        const csrfData = (typeof getCsrfTokenData === 'function') ? getCsrfTokenData() : {};
        
        $.ajax({
            url: `<?= base_url('marketing/customer-management/deleteLocation') ?>/${locationId}`,
            method: 'POST',
            data: {
                [csrfData.tokenName || '<?= csrf_token() ?>']: csrfData.tokenValue || '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    confirmModal.hide();
                    showNotification(response.message, 'success');
                    if (currentCustomerId) {
                        loadCustomerLocations(currentCustomerId);
                    }
                } else if (response.has_units) {
                    // Hide confirm modal, show unit warning modal
                    confirmModal.hide();
                    
                    let tableRows = '';
                    response.units.forEach(function(unit) {
                        const noUnit = unit.no_unit || '-';
                        const merk = (unit.merk_unit || '') + ' ' + (unit.model_unit || '');
                        const sn = unit.serial_number || '-';
                        const kapasitas = unit.kapasitas_unit || '-';
                        const kontrak = unit.no_kontrak || '-';
                        tableRows += `<tr>
                            <td>${noUnit}</td>
                            <td>${merk.trim() || '-'}</td>
                            <td>${sn}</td>
                            <td>${kapasitas}</td>
                            <td>${kontrak}</td>
                        </tr>`;
                    });
                    
                    const warningHtml = `
                        <div class="modal fade" id="locationUnitWarningModal" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning-subtle">
                                        <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Tidak Dapat Menghapus Lokasi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        ${response.is_only_location ? '<div class="alert alert-danger mb-3"><i class="ri-error-warning-fill me-1"></i> Lokasi ini adalah <strong>satu-satunya lokasi</strong> customer. Tidak dapat dihapus karena customer harus memiliki minimal satu lokasi.</div>' : ''}
                                        <div class="alert alert-warning mb-3">
                                            <strong>Lokasi "${locationName}"</strong> masih memiliki <span class="badge badge-soft-red">${response.units.length} unit aktif</span> yang terikat pada kontrak.
                                            <br>Silakan hapus atau pindahkan unit terlebih dahulu dari halaman <strong>Kontrak</strong>.
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No Unit</th>
                                                        <th>Merk / Model</th>
                                                        <th>Serial Number</th>
                                                        <th>Kapasitas</th>
                                                        <th>No Kontrak</th>
                                                    </tr>
                                                </thead>
                                                <tbody>${tableRows}</tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    
                    setTimeout(function() {
                        $('#locationUnitWarningModal').remove();
                        $('body').append(warningHtml);
                        
                        const warningModal = new bootstrap.Modal(document.getElementById('locationUnitWarningModal'));
                        $('#locationUnitWarningModal').css('z-index', 1070);
                        warningModal.show();
                        $('#locationUnitWarningModal').on('shown.bs.modal', function() {
                            $('.modal-backdrop').last().css('z-index', 1069);
                        });
                        $('#locationUnitWarningModal').on('hidden.bs.modal', function() {
                            $(this).remove();
                            if ($('#customerDetailModal').hasClass('show')) {
                                document.body.classList.add('modal-open');
                            }
                        });
                    }, 300);
                } else {
                    confirmModal.hide();
                    showNotification(response.message || 'Gagal menghapus lokasi', 'error');
                }
            },
            error: function() {
                confirmModal.hide();
                showNotification('Terjadi kesalahan pada sistem', 'error');
            }
        });
    });
}

function openEditLocationModal(locationId) {
    clearFormErrors('#addLocationForm');
    
    $.ajax({
        url: `<?= base_url('marketing/customer-management/showCustomerLocation') ?>/${locationId}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const loc = response.data;
                $('#locationCustomerId').val(loc.customer_id);
                $('#addLocationModal .modal-title').text('Edit Location');
                $('#addLocationForm').data('location-id', locationId);
                $('#loc_location_name').val(loc.location_name);
                $('#loc_location_code').val(loc.location_code || '');
                $('#loc_contact_person').val(loc.contact_person || '');
                $('#loc_phone').val(loc.phone || '');
                $('#loc_address').val(loc.address || '');
                $('#loc_city').val(loc.city || '');
                $('#loc_province').val(loc.province || '');
                $('#loc_postal_code').val(loc.postal_code || '');
                $('#loc_email').val(loc.email || '');
                $('#loc_notes').val(loc.notes || '');
                $('#loc_is_primary').prop('checked', loc.is_primary == 1);
                
                // Show di atas customerDetailModal (stacked modal)
                $('#addLocationModal').css('z-index', 1065);
                $('#addLocationModal').modal('show');
                
                $('#addLocationModal').on('shown.bs.modal', function() {
                    $('.modal-backdrop').last().css('z-index', 1064);
                });
            } else {
                showNotification(response.message || 'Failed to load location', 'error');
            }
        },
        error: function() {
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
}


// Show unit detail
function showUnitDetail(unitId) {
    $.ajax({
        url: `<?= base_url('marketing/unit-detail') ?>/${unitId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayUnitDetail(response.data);
                $('#unitDetailModal').modal('show');
            }
        }
    });
}

// Display unit detail - COMPREHENSIVE (from kontrak.php)
function displayUnitDetail(unit) {
    // Helper function untuk baris tabel agar code lebih rapi
    const row = (label, val) => `
        <tr>
            <td class="text-muted pe-3 table-label-cell">${label}</td>
            <td class="fw-medium text-dark table-value-cell">${val || '-'}</td>
        </tr>`;

    // Helper untuk section header kecil
    const sectionHeader = (title, icon) => `
        <tr>
            <td colspan="2" class="pt-3 pb-1">
                <h6 class="text-primary border-bottom pb-1 mb-0 text-sm-custom">
                    <i class="${icon} me-2"></i>${title.toUpperCase()}
                </h6>
            </td>
        </tr>`;

    // Set subtitle modal
    $('#unitSubtitle').html(`
        <span class="badge badge-soft-gray me-2">${unit.merk_unit || 'N/A'}</span>
        <span class="text-muted">${unit.model_unit || ''}</span>
    `);

    let detailHtml = `
        <div class="container-fluid px-0">
            <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded mb-3 border">
                <div>
                    <div class="text-muted small text-uppercase text-xs">Nomor Unit</div>
                    <h4 class="mb-0 fw-bold text-dark">${unit.no_unit || '-'}</h4>
                </div>
                <div class="text-end">
                    <div class="text-muted small text-uppercase text-xs">Status</div>
                    <div class="d-flex align-items-center justify-content-end text-${getStatusColor(unit.status_unit_name)}">
                        <i class="fas fa-circle me-2 icon-xs"></i>
                        <span class="fw-bold small">${unit.status_unit_name || '-'}</span>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6 border-end-lg">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            ${sectionHeader('Informasi Umum', 'fas fa-info-circle')}
                            ${row('Serial Number', unit.serial_number_po)}
                            ${row('Tahun Pembuatan', unit.tahun_po)}
                            ${row('Tipe Unit', unit.nama_tipe_unit)}
                            ${row('Kapasitas', `<span class="fw-bold">${unit.kapasitas_unit || '-'}</span>`)}
                            
                            ${sectionHeader('Lokasi & Dept', 'fas fa-map-marker-alt')}
                            ${row('Departemen', unit.nama_departemen)}
                            ${row('Lokasi Saat Ini', unit.lokasi_unit)}
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            ${sectionHeader('Komponen Utama', 'fas fa-cogs')}
    `;

    // Logic Spesifikasi Teknis
    if (unit.model_mast && unit.model_mast !== 'Unknown') {
        detailHtml += row('Mast Model', unit.model_mast);
        detailHtml += row('Mast SN', unit.sn_mast_po);
    }
    
    if (unit.model_mesin && unit.model_mesin !== 'Unknown') {
        detailHtml += row('Engine Model', unit.model_mesin);
        detailHtml += row('Engine SN', unit.sn_mesin_po);
    }

    // Logic Ban/Roda
    if (unit.jenis_ban && unit.jenis_ban !== 'Unknown') detailHtml += row('Jenis Ban', unit.jenis_ban);
    if (unit.jenis_roda && unit.jenis_roda !== 'Unknown') detailHtml += row('Jenis Roda', unit.jenis_roda);

    // Logic Baterai (Simplified list)
    if (unit.batteries && unit.batteries.length > 0) {
        detailHtml += sectionHeader('Power & Battery', 'fas fa-car-battery');
        unit.batteries.forEach((bat, i) => {
            detailHtml += row(`Baterai #${i+1}`, `${bat.name} <span class="text-muted small">(${bat.serial_number || '-'})</span>`);
        });
    }

    // Logic Charger
    if (unit.chargers && unit.chargers.length > 0) {
        unit.chargers.forEach((chr, i) => {
             detailHtml += row(`Charger #${i+1}`, `${chr.name} <span class="text-muted small">(${chr.serial_number || '-'})</span>`);
        });
    }

    detailHtml += `
                        </tbody>
                    </table>
                </div>
            </div>
            
            <hr class="my-4 text-muted opacity-25">

            <div class="row g-3">
    `;

    // Logic Attachment
    if (unit.attachments && unit.attachments.length > 0) {
        detailHtml += `
            <div class="col-md-6">
                <h6 class="small text-muted fw-bold text-uppercase mb-2"><i class="fas fa-puzzle-piece me-1"></i> Attachments</h6>
                <div class="list-group list-group-flush border rounded-2">`;
        
        unit.attachments.forEach(att => {
            detailHtml += `
                <div class="list-group-item bg-light py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-medium small">${att.name || 'Attachment'}</span>
                        <span class="badge badge-soft-gray text-light fw-normal" style="font-size:0.7em">${att.serial_number || 'No SN'}</span>
                    </div>
                </div>`;
        });
        detailHtml += `</div></div>`;
    }

    // Logic Aksesoris
    let aksesoris = [];
    try {
        aksesoris = typeof unit.aksesoris === 'string' ? JSON.parse(unit.aksesoris) : unit.aksesoris;
    } catch (e) {
        aksesoris = unit.aksesoris ? unit.aksesoris.split(',').map(i => i.trim()).filter(i => i) : [];
    }

    if (aksesoris && aksesoris.length > 0) {
        detailHtml += `
            <div class="col-md-6">
                <h6 class="small text-muted fw-bold text-uppercase mb-2"><i class="fas fa-tools me-1"></i> Accessories</h6>
                <div class="d-flex flex-wrap gap-1">`;
        
        if (Array.isArray(aksesoris)) {
            aksesoris.forEach(item => {
                detailHtml += `<span class="badge border text-dark bg-white fw-normal py-2 px-3"><i class="fas fa-check text-success me-1"></i>${item}</span>`;
            });
        }
        detailHtml += `</div></div>`;
    }
    
    // Keterangan Full Width
    if (unit.keterangan) {
        detailHtml += `
            <div class="col-12 mt-3">
                <div class="alert alert-secondary border-0 mb-0 py-2 d-flex align-items-start">
                    <i class="fas fa-sticky-note me-2 mt-1"></i>
                    <div>
                        <strong class="small d-block text-uppercase">Catatan:</strong>
                        <span class="small text-muted">${unit.keterangan}</span>
                    </div>
                </div>
            </div>`;
    }

    detailHtml += `</div></div>`; // End row & container

    $('#unitDetailContent').html(detailHtml);
}

// Contract Action Functions
function editContract(contractId) {
    // Load contract data and show inline edit modal
    $.ajax({
        url: `<?= base_url('marketing/kontrak/detail') ?>/${contractId}`,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                const contract = response.data;
                
                // Populate edit form
                $('#editContractId').val(contract.id);
                $('#editContractNumber').val(contract.no_kontrak);
                $('#editCustomerId').val(contract.customer_id);
                $('#editLocationId').val(contract.location_id);
                $('#editRentalType').val(contract.rental_type);
                $('#editStartDate').val(contract.tanggal_mulai);
                $('#editEndDate').val(contract.tanggal_selesai);
                $('#editRentalRate').val(contract.harga_sewa);
                $('#editStatus').val(contract.status);
                
                // Show edit modal
                $('#editContractModal').modal('show');
            } else {
                showNotification('Error loading contract data', 'error');
            }
        },
        error: function() {
            showNotification('Error loading contract data', 'error');
        }
    });
}

function deleteContract(contractId) {
    OptimaConfirm.danger({
        title: 'Hapus Kontrak?',
        html: 'Apakah Anda yakin ingin menghapus kontrak ini?<br><small class="text-danger">Tindakan ini tidak dapat dibatalkan!</small>',
        confirmText: 'Ya, Hapus!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            OptimaPro.showLoading('Menghapus kontrak...');
            
            $.ajax({
                url: `<?= base_url('marketing/kontrak/delete') ?>/${contractId}`,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    OptimaPro.hideLoading();
                    if (response.success) {
                        OptimaNotify.success(response.message || 'Kontrak berhasil dihapus');
                        loadCustomerContracts(currentCustomerId);
                    } else {
                        OptimaNotify.error(response.message || 'Gagal menghapus kontrak');
                    }
                },
                error: function(xhr) {
                    OptimaPro.hideLoading();
                    console.error('Delete error:', xhr);
                    OptimaNotify.error('Terjadi kesalahan saat menghapus kontrak');
                }
            });
        }
    });
}

function renewContract(contractId) {
    // Load contract data
    $.ajax({
        url: '<?= base_url('marketing/kontrak/detail') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                const contract = response.data;
                
                // Populate renewal wizard with contract data
                $('#renewalParentContractId').val(contractId);
                $('#renewalOldContractNumber').text(contract.no_kontrak);
                $('#renewalCustomerName').text(contract.customer_name || 'N/A');
                $('#renewalOldStartDate').text(contract.tanggal_mulai || 'N/A');
                $('#renewalOldEndDate').text(contract.tanggal_berakhir || 'N/A');
                
                // Calculate suggested dates (gap-free)
                if (contract.tanggal_berakhir) {
                    const oldEndDate = new Date(contract.tanggal_berakhir);
                    const newStartDate = new Date(oldEndDate);
                    newStartDate.setDate(newStartDate.getDate() + 1);
                    
                    const newEndDate = new Date(newStartDate);
                    newEndDate.setFullYear(newEndDate.getFullYear() + 1);
                    newEndDate.setDate(newEndDate.getDate() - 1);
                    
                    $('#renewalStartDate').val(newStartDate.toISOString().split('T')[0]);
                    $('#renewalEndDate').val(newEndDate.toISOString().split('T')[0]);
                }
                
                // Show renewal wizard modal
                $('#renewalWizardModal').modal('show');
            } else {
                showNotification('Failed to load contract data', 'error');
            }
        },
        error: function() {
            showNotification('Error loading contract data', 'error');
        }
    });
}

// Helper functions
function formatNumber(num) {
    try {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    } catch (error) {
        console.warn('formatNumber error:', error);
        return (num || 0).toString();
    }
}

function getStatusBadge(status) {
    // Following Optima badge standards
    const statusMap = {
        'ACTIVE': 'badge-soft-green',
        'Aktif': 'badge-soft-green',
        'PENDING': 'badge-soft-yellow',
        'Pending': 'badge-soft-yellow',
        'EXPIRED': 'badge-soft-red',
        'Berakhir': 'badge-soft-red',
        'CANCELLED': 'badge-soft-gray',
        'Dibatalkan': 'badge-soft-gray',
        'Draft': 'badge-soft-gray',
        'INACTIVE': 'badge-soft-red'
    };
    
    const badgeClass = statusMap[status] || 'badge-soft-gray';
    return `<span class="badge ${badgeClass}">${status || 'Unknown'}</span>`;
}

function getStatusBadgeClass(status) {
    // Return class name following Optima badge standards
    if (!status) return 'badge-soft-gray';
    const statusLower = status.toLowerCase();
    
    if (statusLower.includes('tersedia') || statusLower.includes('available')) return 'badge-soft-cyan';
    if (statusLower.includes('rental') || statusLower.includes('disewa') || statusLower.includes('active')) return 'badge-soft-green';
    if (statusLower.includes('maintenance')) return 'badge-soft-orange';
    if (statusLower.includes('rusak') || statusLower.includes('broken')) return 'badge-soft-red';
    if (statusLower.includes('hilang') || statusLower.includes('lost')) return 'badge-soft-red';
    
    return 'badge-soft-gray';
}

function showNotification(message, type) {
    if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
        OptimaPro.showNotification(message, type);
        
        // Ensure notification z-index is above modals
        setTimeout(() => {
            $('.optima-notification, .notification-container, .toast-container').css('z-index', '9999');
        }, 100);
    } else if (window.OptimaNotify && typeof OptimaNotify[type] === 'function') {
        OptimaNotify[type](message);
    } else if (window.OptimaNotify) {
        OptimaNotify.info(message);
    } else {
        alert(message);
    }
}

// Auto-check DataTable visibility - DISABLED to prevent infinite loops
// This was causing reinitialize loops and stuck loading
function checkDataTableVisibility() {
    // Disabled - table will show "no data" message if empty
    // No need to reinitialize automatically
    return;
}

// DO NOT call visibility check - it causes infinite loops
// $(document).ready(function() {
//     checkDataTableVisibility();
// });

function refreshData() {
    console.log('🔄 Refreshing customer data...');
    
    try {
        // Use OptimaDataTable.reload() with centralized API
        OptimaDataTable.reload('#customerTable', false); // false = don't reset paging
        showNotification('Data refreshed successfully', 'success');
    } catch (error) {
        console.error('❌ Error refreshing data:', error);
        showNotification('Failed to refresh data. Please try again.', 'error');
        // User can manually click refresh button if needed - no forced page reload
    }
}

// ===== Generic form error helpers =====
function showFormErrors(formSelector, errors) {
    Object.keys(errors).forEach(function(field) {
        const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
        const input = $(`${formSelector} [name="${field}"]`);
        if (input.length) {
            input.addClass('is-invalid');
            let feedback = input.siblings('.invalid-feedback');
            if (!feedback.length) {
                // For input-group, add after group; else after input
                const group = input.closest('.input-group');
                if (group.length) {
                    group.after('<div class="invalid-feedback"></div>');
                    feedback = group.next('.invalid-feedback');
                } else {
                    input.after('<div class="invalid-feedback"></div>');
                    feedback = input.next('.invalid-feedback');
                }
            }
            feedback.text(messages[0]).show();
        }
    });
}

function clearFormErrors(formSelector) {
    const form = $(formSelector);
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').remove();
}

// Load customer locations
function loadCustomerLocations(customerId) {
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getCustomerLocations') ?>/${customerId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayLocations(response.data);
                $('#locationCount').text(response.data.length);
            } else {
                $('#locationsList').html('<div class="col-12 text-center text-muted">No locations found</div>');
            }
        },
        error: function() {
            $('#locationsList').html('<div class="col-12 text-center text-danger">Error loading locations</div>');
        }
    });
}

// Display locations
function displayLocations(locations) {
    let html = '';
    
    if (locations.length === 0) {
        html = '<div class="col-12 text-center text-muted">No locations found</div>';
    } else {
        locations.forEach(location => {
            const isPrimary = location.is_primary ? '<span class="badge badge-soft-blue ms-2">Primary</span>' : '';
            html += `
                <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">
                                            <i class="fas fa-map-marker-alt me-2"></i><strong>${location.location_name}</strong>${isPrimary}
                                        </h6>
                                        ${location.location_code ? `<small class="text-muted">Code: ${location.location_code}</small>` : ''}
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary" onclick="openEditLocationModal(${location.id})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteLocation(${location.id}, '${location.location_name.replace(/'/g, "\\\'")}', ${location.is_primary ? 1 : 0})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-12">
                                    <strong><i class="fas fa-map-marked-alt me-1"></i> Area:</strong><br>
                                    <span class="badge badge-soft-blue">${location.area_name || 'N/A'}</span>
                                </div>
                                <div class="col-12">
                                    <strong><i class="fas fa-map-marker me-1"></i> Alamat:</strong><br>
                                    <span class="text-muted">${location.address || 'Tidak ada alamat'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-city me-1"></i> City:</strong><br>
                                    <span class="text-muted">${location.city || '-'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-flag me-1"></i> Province:</strong><br>
                                    <span class="text-muted">${location.province || '-'}</span>
                                </div>
                                <div class="col-12"><hr class="my-2"></div>
                                <div class="col-6">
                                    <strong><i class="fas fa-user me-1"></i> Contact Person:</strong><br>
                                    <span class="text-muted">${location.contact_person || '-'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-phone me-1"></i> Phone:</strong><br>
                                    <span class="text-muted">${location.phone || '-'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-envelope me-1"></i> Email:</strong><br>
                                    <span class="text-muted">${location.email || '-'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-id-badge me-1"></i> PIC Position:</strong><br>
                                    <span class="text-muted">${location.pic_position || '-'}</span>
                                </div>
                                ${location.notes ? `
                                <div class="col-12">
                                    <strong><i class="fas fa-sticky-note me-1"></i> Notes:</strong><br>
                                    <span class="text-muted">${location.notes}</span>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#locationsList').html(html);
}

// Setup tab handlers
/**
 * Setup status filter tab handlers
 */
function setupStatusFilterTabs() {
    // Restore active tab based on saved state (for back navigation UX)
    $('.customer-status-tabs .nav-link').removeClass('active');
    const $activeTab = $(`.customer-status-tabs .nav-link[data-status="${currentStatusFilter}"]`);
    if ($activeTab.length) {
        $activeTab.addClass('active');
    } else {
        $('.customer-status-tabs .nav-link[data-status="all"]').addClass('active');
    }

    $('.customer-status-tabs .nav-link').on('click', function() {
        const status = $(this).data('status');
        
        // Update active tab
        $('.customer-status-tabs .nav-link').removeClass('active');
        $(this).addClass('active');
        
        // Update global filter and persist for back navigation
        currentStatusFilter = status;
        try {
            const state = JSON.parse(sessionStorage.getItem(CUSTOMER_STATE_KEY) || '{}');
            state.statusFilter = status;
            sessionStorage.setItem(CUSTOMER_STATE_KEY, JSON.stringify(state));
        } catch (e) { /* ignore */ }
        
        console.log('🔄 Status filter changed to:', status);
        
        // Reload DataTable with new filter
        if (customerTable) {
            customerTable.ajax.reload(null, false); // false = stay on current page
        }
    });
}

function setupTabHandlers() {
    // Handle Locations tab
    $('#locations-tab').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Remove active class from modal tabs only and hide all modal content
        $('#customerDetailTabs .nav-link').removeClass('active');
        $('#customerDetailTabContent .tab-pane').removeClass('show active').hide();
        
        // Add active class to locations tab and show content
        $(this).addClass('active');
        $('#locations-content').addClass('show active').show();
        
        // Load locations data
        if (currentCustomerId) {
            loadCustomerLocations(currentCustomerId);
        }
    });
    
    // Handle Contracts tab
    $('#contracts-tab').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Remove active class from modal tabs only and hide all modal content
        $('#customerDetailTabs .nav-link').removeClass('active');
        $('#customerDetailTabContent .tab-pane').removeClass('show active').hide();
        
        // Add active class to contracts tab and show content
        $(this).addClass('active');
        $('#contracts-content').addClass('show active').show();
        
        // Load contracts data
        if (currentCustomerId) {
            loadCustomerContracts(currentCustomerId);
        }
    });
    
    // Handle Activity Log tab - NEW
    $('#activity-tab').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Remove active class from modal tabs only and hide all modal content
        $('#customerDetailTabs .nav-link').removeClass('active');
        $('#customerDetailTabContent .tab-pane').removeClass('show active').hide();
        
        // Add active class to activity tab and show content
        $(this).addClass('active');
        $('#activity-content').addClass('show active').show();
        
        // Load activity data
        if (currentCustomerId) {
            loadCustomerActivity(currentCustomerId);
        }
    });
    
    // Activity filter change handler
    $('#activityFilter').on('change', function() {
        if (currentCustomerId) {
            loadCustomerActivity(currentCustomerId, $(this).val());
        }
    });
    
    // Handle Company Info tab
    $('#company-tab').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Remove active class from modal tabs only and hide all modal content
        $('#customerDetailTabs .nav-link').removeClass('active');
        $('#customerDetailTabContent .tab-pane').removeClass('show active').hide();
        
        // Add active class to company tab and show content
        $(this).addClass('active');
        $('#company-content').addClass('show active').show();
    });
    
    
    // Handle Locations & Units tab in Contract Details modal
    $('#locations-tab-contract').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Check if data already loaded (to prevent unnecessary reload)
        const locationsContainer = document.getElementById('locationsAccordion');
        const isAlreadyLoaded = locationsContainer && 
                                locationsContainer.innerHTML.trim() !== '' && 
                                !locationsContainer.innerHTML.includes('Memuat locations');
        
        // Remove active class from contract modal tabs only
        $('#contractDetailTabs .nav-link').removeClass('active');
        $('#contractDetailTabContent .tab-pane').removeClass('show active');
        
        // Add active class to locations tab and show content
        $(this).addClass('active');
        $('#locations-content-contract').addClass('show active');
        
        // Only load data if NOT already loaded
        console.log('Locations & Units tab clicked, currentContractId:', currentContractId, 'Already loaded:', isAlreadyLoaded);
        
        if (!isAlreadyLoaded) {
            // Show loading state
            $('#locationsAccordion').html(`
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="text-muted mt-3">Memuat locations & units...</p>
                </div>
            `);
            
            if (currentContractId) {
                // Small delay to ensure smooth transition
                setTimeout(() => {
                    loadContractUnits(currentContractId);
                }, 100);
            } else {
                console.warn('No currentContractId available for locations & units');
                $('#locationsAccordion').html('<div class="alert alert-warning"><i class="fas fa-info-circle me-2"></i>Pilih kontrak terlebih dahulu untuk melihat locations & units</div>');
            }
        }
        // If already loaded, just show it (no reload, scroll position maintained)
    });
    
    // Spesifikasi functionality removed - now handled in Quotations page
}





function loadCustomers(callback) {
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getCustomers') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const customerSelect = $('#customerSelect');
                customerSelect.empty().append('<option value="">-- Pilih Customer --</option>');
                response.data.forEach(customer => {
                    customerSelect.append(`<option value="${customer.id}">${customer.customer_name}</option>`);
                });
                
                // Call callback if provided
                if (typeof callback === 'function') {
                    callback();
                }
            }
        },
        error: function() {
            console.error('Error loading customers');
        }
    });
}

// Handle generate customer code button
$(document).on('click', '#generateCustomerCode', function() {
    generateCustomerCode();
});

$(document).on('click', '#generateLocationCode', function() {
    generateLocationCode();
});

$(document).on('click', '#generateLocationCodeModal', function() {
    generateLocationCodeModal();
});

// Handle customer selection in contract modal
$(document).on('change', '#customerSelect', function() {
    const customerId = $(this).val();
    const locationSelect = $('#locationSelect');
    
    if (customerId) {
        // Load locations for selected customer
        $.ajax({
            url: `<?= base_url('marketing/customer-management/getCustomerLocations/') ?>${customerId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    locationSelect.empty().append('<option value="">-- Pilih Lokasi --</option>');
                    response.data.forEach(location => {
                        locationSelect.append(`<option value="${location.id}">${location.location_name}</option>`);
                    });
                    locationSelect.prop('disabled', false);
                }
            },
            error: function() {
                console.error('Error loading locations');
            }
        });
    } else {
        locationSelect.empty().append('<option value="">-- Pilih Customer Dulu --</option>').prop('disabled', true);
    }
});

// Handle form submissions
$(document).on('submit', '#addCustomerForm', function(e) {
    e.preventDefault();
    // Clear previous errors before submit
    clearFormErrors('#addCustomerForm');

    $.ajax({
        url: '<?= base_url('marketing/customer-management/storeCustomer') ?>',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#addCustomerModal').modal('hide');
                $('#addCustomerForm')[0].reset();
                customerTable.ajax.reload();
            } else {
                if (response.errors) {
                    showFormErrors('#addCustomerForm', response.errors);
                    showNotification('Periksa kembali input Anda.', 'error');
                } else {
                    showNotification(response.message || 'Validation failed', 'error');
                }
            }
        },
        error: function() {
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
});

// Handle edit customer form submission
$(document).on('submit', '#editCustomerForm', function(e) {
    e.preventDefault();
    clearFormErrors('#editCustomerForm');
    
    const customerId = $('#edit_customer_id').val();
    const formData = $(this).serialize();
    
    // Debug logging for form submission
    console.log('📤 Submitting customer update for ID:', customerId);
    console.log('📋 Form data (serialized):', formData);
    console.log('📋 Form fields:', {
        customer_code: $('#edit_customer_code').val(),
        customer_name: $('#edit_customer_name').val(),
        is_active: $('#edit_is_active').val()
    });
    
    if (typeof OptimaPro !== 'undefined' && OptimaPro.showLoading) {
        OptimaPro.showLoading('Updating customer...');
    }
    
    $.ajax({
        url: `<?= base_url('marketing/customer-management/updateCustomer') ?>/${customerId}`,
        method: 'POST',
        data: formData,
        success: function(response) {
            if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                OptimaPro.hideLoading();
            }
            
            if (response.success) {
                showNotification(response.message || 'Customer updated successfully', 'success');
                $('#editCustomerModal').modal('hide');
                
                // Reload customer table
                if (customerTable) {
                    customerTable.ajax.reload(null, false);
                }
                
                // Refresh customer detail yang masih terbuka di belakang
                if (currentCustomerId && $('#customerDetailModal').hasClass('show')) {
                    openCustomerDetail(currentCustomerId);
                }
            } else {
                if (response.errors) {
                    showFormErrors('#editCustomerForm', response.errors);
                }
                showNotification(response.message || 'Failed to update customer', 'error');
            }
        },
        error: function(xhr) {
            if (typeof OptimaPro !== 'undefined' && OptimaPro.hideLoading) {
                OptimaPro.hideLoading();
            }
            
            console.error('❌ Error updating customer:');
            console.error('   HTTP Status:', xhr.status);
            console.error('   Status Text:', xhr.statusText);
            console.error('   Response:', xhr.responseText);
            console.error('   Response JSON:', xhr.responseJSON);
            
            let errorMsg = 'An error occurred while updating customer';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 0) {
                errorMsg = 'Network error - cannot reach server';
            } else if (xhr.status === 403) {
                errorMsg = 'Permission denied - you are not authorized to update customers';
            } else if (xhr.status === 404) {
                errorMsg = 'Customer not found or endpoint missing';
            } else if (xhr.status === 500) {
                errorMsg = 'Server error occurred. Please contact administrator.';
            }
            
            // Show validation errors if present
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                showFormErrors('#editCustomerForm', xhr.responseJSON.errors);
                errorMsg += ' - Please check form fields.';
            }
            
            showNotification(errorMsg, 'error');
        }
    });
});

// Handle edit contract form submission
$(document).on('submit', '#editContractForm', function(e) {
    e.preventDefault();
    
    const contractId = $('#editContractId').val();
    const formData = $(this).serialize();
   
    // Show loading
    OptimaPro.showLoading('Updating contract...');
    
    $.ajax({
        url: `<?= base_url('marketing/kontrak/update') ?>/${contractId}`,
        method: 'POST',
        data: formData,
        success: function(response) {
            OptimaPro.hideLoading();
            if (response.success) {
                OptimaNotify.success(response.message || 'Contract updated successfully');
                
                // Close edit modal
                $('#editContractModal').modal('hide');
                
                // Refresh contracts list if in customer detail
                if (currentCustomerId) {
                    loadContractsForCustomer(currentCustomerId);
                }
                
                // Reload customer table
                if (customerTable) {
                    customerTable.ajax.reload(null, false);
                }
            } else {
                OptimaPro.hideLoading();
                OptimaNotify.error(response.message || 'Failed to update contract');
            }
        },
        error: function(xhr) {
            OptimaPro.hideLoading();
            let errorMsg = 'An error occurred while updating contract';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            OptimaNotify.error(errorMsg);
        }
    });
});

$(document).on('submit', '#addContractForm', function(e) {
    e.preventDefault();
    clearFormErrors('#addContractForm');

    // Get form data
    let formData = $(this).serializeArray();
    
    // Ensure customer_id is included even if select is disabled
    if (currentCustomerId && !formData.find(item => item.name === 'customer_id')) {
        formData.push({name: 'customer_id', value: currentCustomerId});
    }

    $.ajax({
        url: '<?= base_url('marketing/kontrak/store') ?>',
        method: 'POST',
        data: $.param(formData),
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#addContractModal').modal('hide');
                $('#addContractForm')[0].reset();
                
                // Reload customer table
                if (customerTable) {
                    customerTable.ajax.reload();
                }
                
                // Reload contracts if we're in customer detail modal
                if (currentCustomerId) {
                    loadCustomerContracts(currentCustomerId);
                }
            } else {
                if (response.errors) {
                    showFormErrors('#addContractForm', response.errors);
                }
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
});

// Handle save contract buttons
$(document).on('click', '#btnSaveOnly, #btnSaveAndSpec', function() {
    const action = $(this).attr('id') === 'btnSaveAndSpec' ? 'save_and_spec' : 'save_only';
    $('#submitAction').val(action);
    
    // Trigger form submit
    $('#addContractForm').trigger('submit');
});

// Location form submit handler
$(document).on('submit', '#addLocationForm', function(e) {
    e.preventDefault();
    clearFormErrors('#addLocationForm');

    const locId = $('#addLocationForm').data('location-id');
    const url = locId 
        ? `<?= base_url('marketing/customer-management/updateCustomerLocation') ?>/${locId}` 
        : '<?= base_url('marketing/customer-management/storeCustomerLocation') ?>';

    $.ajax({
        url: url,
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#addLocationForm')[0].reset();
                $('#addLocationForm').removeData('location-id');
                $('#addLocationModal').modal('hide');
                
                // Refresh locations di customerDetailModal yang masih terbuka
                if (currentCustomerId) {
                    loadCustomerLocations(currentCustomerId);
                }
            } else {
                if (response.errors) {
                    displayFormErrors('#addLocationForm', response.errors);
                    showNotification('Periksa kembali input lokasi.', 'error');
                } else {
                    showNotification(response.message, 'error');
                }
            }
        },
        error: function() {
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
});

// SPK Modal Functions
function openSpkModalFromKontrak(spekId) {
    console.log('openSpkModalFromKontrak called with spekId:', spekId);
    
    if (!currentContractId) {
        showNotification('Kontrak belum dipilih. Buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    
    // Set form values
    document.getElementById('spkKontrakId').value = currentContractId;
    document.getElementById('spkSpesifikasiId').value = spekId;
    
    // Populate fields from contract data
    console.log('Current contract data:', window.currentContractData);
    
    if (window.currentContractData) {
        const pelangganField = document.getElementById('spkPelanggan');
        const picField = document.getElementById('spkPic');
        const kontakField = document.getElementById('spkKontak');
        const lokasiField = document.getElementById('spkLokasi');
        
        if (pelangganField) pelangganField.value = window.currentContractData.customer_name || window.currentContractData.pelanggan || '';
        if (picField) picField.value = window.currentContractData.contact_person || window.currentContractData.pic || '';
        if (kontakField) kontakField.value = window.currentContractData.phone || window.currentContractData.kontak || '';
        if (lokasiField) lokasiField.value = window.currentContractData.location_name || window.currentContractData.lokasi || '';
        
        console.log('SPK fields populated:', {
            customer: pelangganField?.value,
            pic: picField?.value,
            phone: kontakField?.value,
            location: lokasiField?.value
        });
    } else {
        console.log('No contract data available, loading contract data...');
        // Load contract data if not available
        loadContractDataForSpk(currentContractId);
    }
    
    // Determine SPK type based on specification type
    let spkType = 'UNIT'; // default
    try {
        const card = document.querySelector(`[data-spek-id="${spekId}"]`);
        if (card && card.getAttribute('data-is-attachment') === 'true') {
            spkType = 'ATTACHMENT';
        }
    } catch(e) { 
        console.warn('Failed to determine SPK type from specification', e); 
    }
    
    // Reset form and set SPK type
    document.querySelector('#spkFromKontrakForm [name="jenis_spk"]').value = spkType;
    document.querySelector('#spkFromKontrakForm [name="delivery_plan"]').value = '';
    document.querySelector('#spkFromKontrakForm [name="jumlah_unit"]').value = '';
    document.querySelector('#spkFromKontrakForm [name="catatan"]').value = '';
    
    // Set maximum units based on specification
    setMaxUnitsForSpk(spekId);
    
    // Load units for ATTACHMENT target if needed
    if (spkType === 'ATTACHMENT') {
        console.log('SPK type is ATTACHMENT, loading units...');
        loadContractUnitsForAttachment(currentContractId);
        
        // Show attachment target section
        const attachmentSection = document.getElementById('attachmentTargetSection');
        const targetUnitSelect = document.getElementById('spkTargetUnitId');
        const jumlahUnitDiv = document.querySelector('[for="spkJumlahUnit"]')?.closest('.mb-3');
        
        if (attachmentSection) {
            attachmentSection.style.display = 'block';
        }
        if (targetUnitSelect) {
            targetUnitSelect.setAttribute('required', 'required');
        }
        if (jumlahUnitDiv) {
            jumlahUnitDiv.style.display = 'none';
        }
    } else {
        console.log('SPK type is UNIT, hiding attachment section...');
        // Hide attachment target section for UNIT type
        const attachmentSection = document.getElementById('attachmentTargetSection');
        const targetUnitSelect = document.getElementById('spkTargetUnitId');
        const jumlahUnitDiv = document.querySelector('[for="spkJumlahUnit"]')?.closest('.mb-3');
        
        if (attachmentSection) {
            attachmentSection.style.display = 'none';
        }
        if (targetUnitSelect) {
            targetUnitSelect.removeAttribute('required');
            targetUnitSelect.value = '';
        }
        if (jumlahUnitDiv) {
            jumlahUnitDiv.style.display = 'block';
        }
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('spkFromKontrakModal'));
    modal.show();
}

// Function to set maximum units based on specification
function setMaxUnitsForSpk(spekId) {
    console.log('Setting max units for SPK, spekId:', spekId);
    
    try {
        // Find the specification card in the DOM
        const card = document.querySelector(`[data-spek-id="${spekId}"]`);
        if (card) {
            const jumlahDibutuhkan = Number(card.getAttribute('data-jumlah-dibutuhkan') || '0');
            const jumlahTersedia = Number(card.getAttribute('data-jumlah-tersedia') || '0');
            const available = Math.max(0, jumlahDibutuhkan - jumlahTersedia);
            
            console.log('Specification data:', {
                jumlahDibutuhkan,
                jumlahTersedia,
                available
            });
            
            const jumlahInput = document.getElementById('spkJumlahUnit');
            const hint = document.getElementById('jumlahUnitHint');
            const formEl = document.getElementById('spkFromKontrakForm');
            
            if (jumlahInput) {
                if (available > 0) {
                    jumlahInput.setAttribute('max', String(available));
                    jumlahInput.setAttribute('placeholder', `Maks ${available} unit`);
                    jumlahInput.setAttribute('min', '1');
                } else {
                    jumlahInput.removeAttribute('max');
                    jumlahInput.removeAttribute('placeholder');
                    jumlahInput.setAttribute('min', '1');
                }
            }
            
            if (hint) {
                hint.textContent = available > 0 ? `(maks ${available} unit)` : '(tidak ada unit tersedia)';
                hint.className = available > 0 ? 'text-muted' : 'text-danger';
            }
            
            if (formEl) {
                formEl.dataset.availableUnits = String(available);
            }
            
            console.log(`Max units set to: ${available}`);
        } else {
            console.warn('Specification card not found for spekId:', spekId);
            // Fallback: set default max
            const jumlahInput = document.getElementById('spkJumlahUnit');
            const hint = document.getElementById('jumlahUnitHint');
            
            if (jumlahInput) {
                jumlahInput.setAttribute('min', '1');
                jumlahInput.removeAttribute('max');
                jumlahInput.removeAttribute('placeholder');
            }
            
            if (hint) {
                hint.textContent = '';
                hint.className = 'text-muted';
            }
        }
    } catch(e) {
        console.error('Failed to set max units for SPK:', e);
    }
}

// Function to load contract data for SPK
function loadContractDataForSpk(kontrakId) {
    if (!kontrakId) return;
    
    console.log('Loading contract data for SPK, kontrakId:', kontrakId);
    
    fetch(`<?= base_url('marketing/kontrak/detail/') ?>${kontrakId}`)
        .then(r => r.json())
        .then(data => {
            console.log('Contract data loaded for SPK:', data);
            if (data && data.success) {
                window.currentContractData = data.data;
                
                // Populate SPK fields
                const pelangganField = document.getElementById('spkPelanggan');
                const picField = document.getElementById('spkPic');
                const kontakField = document.getElementById('spkKontak');
                const lokasiField = document.getElementById('spkLokasi');
                
                if (pelangganField) pelangganField.value = data.data.customer_name || data.data.pelanggan || '';
                if (picField) picField.value = data.data.contact_person || data.data.pic || '';
                if (kontakField) kontakField.value = data.data.phone || data.data.kontak || '';
                if (lokasiField) lokasiField.value = data.data.location_name || data.data.lokasi || '';
                
                console.log('SPK fields populated from loaded data:', {
                    customer: pelangganField?.value,
                    pic: picField?.value,
                    phone: kontakField?.value,
                    location: lokasiField?.value
                });
            }
        })
        .catch(err => {
            console.error('Failed to load contract data for SPK:', err);
        });
}

// Function to load units from contract for ATTACHMENT target
function loadContractUnitsForAttachment(kontrakId) {
    if (!kontrakId) return;
    
    console.log('Loading contract units for ATTACHMENT, kontrakId:', kontrakId);
    
    fetch(`<?= base_url('marketing/kontrak/units/') ?>${kontrakId}`)
        .then(r => r.json())
        .then(response => {
            console.log('Contract units loaded for ATTACHMENT:', response);
            const select = document.getElementById('spkTargetUnitId');
            if (!select) return;
            
            select.innerHTML = '<option value="">- Pilih Unit Tujuan -</option>';
            
            // Check if response has success and data array
            if (response && response.success && response.data && response.data.length > 0) {
                response.data.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = `${unit.no_unit} - ${unit.serial_number} - ${unit.jenis_unit || 'N/A'}`;
                    option.dataset.sn = unit.serial_number;
                    select.appendChild(option);
                });
                console.log(`Loaded ${response.data.length} units for ATTACHMENT target`);
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = '-- Tidak ada unit terdaftar di kontrak ini --';
                option.disabled = true;
                select.appendChild(option);
                console.log('No units found for this contract');
            }
        })
        .catch(err => {
            console.error('Failed to load contract units:', err);
        });
}

// Toggle attachment target section based on jenis_spk
$(document).on('change', '#spkJenisSpk', function() {
    const attachmentSection = document.getElementById('attachmentTargetSection');
    const targetUnitSelect = document.getElementById('spkTargetUnitId');
    const jumlahUnitDiv = document.querySelector('[for="spkJumlahUnit"]')?.closest('.mb-3');
    
    if (this.value === 'ATTACHMENT') {
        // Show attachment target section
        if (attachmentSection) attachmentSection.style.display = 'block';
        if (targetUnitSelect) targetUnitSelect.setAttribute('required', 'required');
        
        // Hide jumlah unit (always 1 for attachment)
        if (jumlahUnitDiv) jumlahUnitDiv.style.display = 'none';
        document.getElementById('spkJumlahUnit').value = '1';
        
        // Load units for this contract
        const kontrakId = document.getElementById('spkKontrakId').value;
        if (kontrakId) {
            loadContractUnitsForAttachment(kontrakId);
        }
    } else {
        // Hide attachment target section
        if (attachmentSection) attachmentSection.style.display = 'none';
        if (targetUnitSelect) {
            targetUnitSelect.removeAttribute('required');
            targetUnitSelect.value = '';
        }
        
        // Show jumlah unit
        if (jumlahUnitDiv) jumlahUnitDiv.style.display = 'block';
    }
});

// SPK Form submission
$(document).on('submit', '#spkFromKontrakForm', function(e) {
    e.preventDefault();
    clearFormErrors('#spkFromKontrakForm');
    
    const formData = new FormData(this);
    
    // Validate required fields
    const kontrakId = formData.get('kontrak_id');
    const kontrakSpesifikasiId = formData.get('kontrak_spesifikasi_id');
    
    if (!kontrakId) {
        showNotification('Data kontrak tidak tersedia. Pastikan halaman sudah dimuat dengan benar.', 'error');
        return;
    }
    
    if (!formData.get('pelanggan')) {
        showNotification('Data pelanggan tidak tersedia. Pastikan detail kontrak sudah dimuat.', 'error');
        return;
    }
    
    if (!formData.get('delivery_plan') || !formData.get('jumlah_unit')) {
        showNotification('Lengkapi semua field wajib.', 'error');
        return;
    }
    
    // Validate ATTACHMENT specific fields
    const jenisSpk = formData.get('jenis_spk');
    if (jenisSpk === 'ATTACHMENT') {
        const targetUnitId = formData.get('target_unit_id');
        if (!targetUnitId) {
            showNotification('Unit Tujuan wajib dipilih untuk SPK ATTACHMENT.', 'error');
            return;
        }
        // Force jumlah_unit to 1 for attachment
        formData.set('jumlah_unit', '1');
    }
    
    // Validate maximum units based on specification
    const formEl = document.getElementById('spkFromKontrakForm');
    const availableUnits = Number(formEl?.dataset.availableUnits || '0');
    const requestedUnits = Number(formData.get('jumlah_unit'));
    
    if (availableUnits > 0 && requestedUnits > availableUnits) {
        showNotification(`Jumlah unit melebihi yang tersedia. Maksimal: ${availableUnits} unit`, 'error');
        return;
    }
    
    if (availableUnits === 0) {
        showNotification('Tidak ada unit tersedia untuk spesifikasi ini.', 'error');
        return;
    }
    
    // Debug form data before submission
    console.log('SPK Form Data being submitted:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    // Submit form using fetch API (same as kontrak.php)
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
            showNotification('SPK berhasil dibuat!', 'success');
            const modalEl = document.getElementById('spkFromKontrakModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            // Refresh contract details if needed
            if (currentContractId) {
                loadContractDetail(currentContractId);
            }
        } else {
            showNotification(res.message || 'Gagal membuat SPK.', 'error');
        }
    })
    .catch(err => {
        console.error('SPK Create Error:', err);
        showNotification('Gagal membuat SPK: ' + err, 'error');
    });
});

// Print PDF functionality
$(document).on('click', '#printCustomerPDF', function() {
    if (!currentCustomerId) {
        showNotification('No customer selected', 'error');
        return;
    }
    
    // Show loading
    const originalText = $(this).html();
    $(this).html('<i class="fas fa-spinner fa-spin me-1"></i>Generating PDF...');
    $(this).prop('disabled', true);
    
    // Generate PDF URL
    const pdfUrl = `<?= base_url('marketing/customer-management/generatePDF/') ?>${currentCustomerId}`;
    
    // Open PDF in new tab
    const newWindow = window.open(pdfUrl, '_blank');
    
    // Reset button after a delay
    setTimeout(() => {
        $(this).html(originalText);
        $(this).prop('disabled', false);
    }, 2000);
    
    // Check if PDF opened successfully
    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
        showNotification('PDF generation failed. Please try again.', 'error');
    } else {
        showNotification('PDF report is being generated...', 'success');
    }
});

// ============================================================================
// SPRINT 1-3: BILLING ENHANCEMENT MODAL OPENERS
// ============================================================================

/**
 * Sprint 3: Open Amendment/Prorate Modal
 * Shows rate change calculator with prorate split
 */
function openAmendmentModal(contractId) {
    // Load contract data first
    $.ajax({
        url: '<?= base_url('marketing/kontrak/detail') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                const contract = response.data;
                
                // Populate amendment modal with contract data
                $('#prorateContractId').val(contractId);
                $('#prorateContractNumber').text(contract.no_kontrak || 'N/A');
                $('#prorateCustomerName').text(contract.customer_name || 'N/A');
                $('#prorateCurrentRate').val(contract.harga_sewa || 0);
                $('#prorateStartDate').val(contract.tanggal_mulai || '');
                $('#prorateEndDate').val(contract.tanggal_selesai || '');
                
                // Show modal
                $('#addendumProrateModal').modal('show');
            } else {
                showNotification('Error loading contract data', 'error');
            }
        },
        error: function() {
            showNotification('Error loading contract data', 'error');
        }
    });
}

/**
 * Sprint 3: Open Asset History Modal
 * Shows complete contract timeline with amendments and renewals
 */
function openHistoryModal(contractId) {
    // Show modal immediately
    $('#assetHistoryModal').modal('show');
    
    // Show loading state
    $('#contractTimelineContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br><small class="text-muted mt-2 d-block">Loading contract history...</small></div>');
    $('#rateHistoryContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>');
    
    // Load contract history
    $.ajax({
        url: '<?= base_url('marketing/billing/contract-history') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                renderContractTimeline(response.data);
            } else {
                $('#contractTimelineContent').html(
                    '<div class="alert alert-warning">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>' +
                    'No history data available for this contract.' +
                    '</div>'
                );
            }
        },
        error: function() {
            $('#contractTimelineContent').html(
                '<div class="alert alert-danger">' +
                '<i class="fas fa-times me-2"></i>' +
                'Error loading contract history.' +
                '</div>'
            );
        }
    });
    
    // Load rate history
    $.ajax({
        url: '<?= base_url('kontrak/getRateHistory') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                renderRateHistory(response.data);
            } else {
                $('#rateHistoryContent').html(
                    '<p class="text-muted text-center py-3">No rate changes recorded</p>'
                );
            }
        },
        error: function() {
            $('#rateHistoryContent').html(
                '<p class="text-danger text-center py-3">Error loading rate history</p>'
            );
        }
    });
}

/**
 * Render contract timeline in history modal
 */
function renderContractTimeline(events) {
    if (!events || events.length === 0) {
        $('#contractTimelineContent').html('<p class="text-muted text-center py-3">No events found</p>');
        return;
    }
    
    let html = '<div class="timeline">';
    
    events.forEach(event => {
        const iconMap = {
            'contract': 'fa-file-contract text-primary',
            'amendment': 'fa-edit text-warning',
            'renewal': 'fa-sync-alt text-success'
        };
        
        const icon = iconMap[event.type] || 'fa-circle text-secondary';
        
        html += '<div class="timeline-item">' +
                '<div class="timeline-marker"><i class="fas ' + icon + '"></i></div>' +
                '<div class="timeline-content">' +
                '<div class="timeline-time">' + event.date + '</div>' +
                '<h6>' + event.description + '</h6>';
        
        if (event.reason) {
            html += '<p class="text-muted mb-0">' + event.reason + '</p>';
        }
        
        if (event.total_value) {
            html += '<p class="mb-0"><strong>Value:</strong> Rp ' + 
                    parseFloat(event.total_value).toLocaleString('id-ID') + '</p>';
        }
        
        html += '</div></div>';
    });
    
    html += '</div>';
    
    $('#contractTimelineContent').html(html);
}

/**
 * Render rate history chart
 */
function renderRateHistory(rates) {
    if (!rates || rates.length === 0) {
        $('#rateHistoryContent').html('<p class="text-muted text-center py-3">No rate changes found</p>');
        return;
    }
    
    let html = '<div class="table-responsive">' +
               '<table class="table table-sm table-hover">' +
               '<thead class="table-light"><tr>' +
               '<th>Date</th><th>Unit</th><th>Old Rate</th><th>New Rate</th><th>Change</th>' +
               '</tr></thead><tbody>';
    
    rates.forEach(rate => {
        const change = rate.new_rate - rate.old_rate;
        const changeClass = change > 0 ? 'text-success' : 'text-danger';
        const changeIcon = change > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
        
        html += '<tr>' +
                '<td><small>' + rate.date + '</small></td>' +
                '<td><strong>' + rate.unit_no + '</strong></td>' +
                '<td>Rp ' + parseFloat(rate.old_rate).toLocaleString('id-ID') + '</td>' +
                '<td>Rp ' + parseFloat(rate.new_rate).toLocaleString('id-ID') + '</td>' +
                '<td class="' + changeClass + '">' +
                '<i class="fas ' + changeIcon + ' me-1"></i>' +
                'Rp ' + Math.abs(change).toLocaleString('id-ID') +
                '</td></tr>';
    });
    
    html += '</tbody></table></div>';
    
    $('#rateHistoryContent').html(html);
}

</script>

<!-- Sprint 1-3: Billing Enhancement Components -->
<?= $this->include('components/renewal_wizard') ?>
<?= $this->include('components/addendum_prorate') ?>
<?= $this->include('components/asset_history') ?>

<?= $this->endSection() ?>
