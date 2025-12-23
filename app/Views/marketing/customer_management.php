<?= $this->extend('layouts/base') ?>

<?php
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

<!-- Customer Table Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?= lang('Marketing.customer_management') ?></h5>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-primary" onclick="openAddCustomerModal()">
                <i class="fas fa-plus"></i> <?= lang('Marketing.add_customer') ?>
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> <?= lang('App.refresh') ?>
            </button>
            <?php if ($can_export): ?>
            <a href="<?= base_url('marketing/export_customer') ?>" class="btn btn-sm btn-outline-success">
                <i class="fas fa-file-excel"></i> <?= lang('App.export') ?>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="customerTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Customer Code</th>
                        <th>Customer Name</th>
                        <th>Area</th>
                        <th>Locations</th>
                        <th>Contracts</th>
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
<div class="modal fade" id="customerDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="bi bi-building me-2"></i><span id="customerName">Customer Details</span>
                    </h5>
                    <small class="text-muted" id="customerCode"></small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm text-white" id="printCustomerPDF" title="Print PDF Report">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Print PDF
                    </button>
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
                            <i class="fas fa-file-contract me-1"></i>Contracts (<span id="contractCount">0</span>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="customerDetailTabContent">
                    <!-- Company Info Tab -->
                    <div class="tab-pane fade show active" id="company-content" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><strong>Company Information</strong></h6>
                                    </div>
                                    <div class="card-body" id="companyInfo">
                                        <div class="text-center text-muted">Loading...</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><strong>Statistics</strong></h6>
                                    </div>
                                    <div class="card-body" id="customerStats">
                                        <div class="text-center text-muted">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Locations Tab -->
                    <div class="tab-pane fade" id="locations-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><strong>Customer Locations</strong></h6>
                            <button class="btn btn-sm btn-primary" onclick="openAddLocationModal()">
                                <i class="fas fa-plus me-1"></i>Add Location
                            </button>
                        </div>
                        <div class="row" id="locationsList">
                            <div class="text-center text-muted">Loading locations...</div>
                        </div>
                    </div>

                    <!-- Contracts Tab -->
                    <div class="tab-pane fade" id="contracts-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><strong>Customer Contracts</strong></h6>
                            <button class="btn btn-sm btn-primary" onclick="openAddContractModal()">
                                <i class="fas fa-plus me-1"></i>Add Contract
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="contractsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No. Kontrak</th>
                                        <th>No. PO</th>
                                        <th>Location</th>
                                        <th>Periode</th>
                                        <th>Total Units</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Contracts loaded dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-white" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Contract Detail Modal -->
<div class="modal fade" id="contractDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
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
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-white" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Unit Detail Modal -->
<div class="modal fade" id="unitDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('Marketing.add_customer') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCustomerForm">
                <div class="modal-body">
                    <!-- Company Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_code">Customer Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer_code" name="customer_code" required maxlength="20">
                                    <button class="btn btn-outline-secondary" type="button" id="generateCustomerCode" title="Generate Customer Code">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Unique customer identifier - bisa diisi manual atau generate otomatis</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required maxlength="255">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Primary Location & Contact -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location_name">Primary Location Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location_name" name="location_name" value="Head Office" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="primary_location_code">Primary Location Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="primary_location_code" name="primary_location_code" maxlength="50">
                                    <button class="btn btn-outline-secondary" type="button" id="generateLocationCode" title="Generate Location Code">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Optional - will be auto-generated if empty</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="area_id">Area <span class="text-danger">*</span></label>
                                <select class="form-control" id="area_id" name="area_id" required>
                                    <option value="">Select Area</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" maxlength="100" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" maxlength="20" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="10">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="province">Province <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="province" name="province" maxlength="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Primary Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" maxlength="500" required></textarea>
                        <small class="form-text text-muted">This will be created as the primary location</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Description</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>

                    <!-- Defaults required by backend validation -->
                    <input type="hidden" name="is_active" value="1">
                    <input type="hidden" name="location_type" value="HEAD_OFFICE">
                    <input type="hidden" name="pic_position" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Contract Modal -->
<div class="modal fade" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><?= lang('Marketing.add_contract') ?></h5>
                    <small class="text-muted">Langkah 1: Informasi Dasar Kontrak</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <input type="text" class="form-control" id="customerDisplay" readonly style="display:none;">
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
                        <div class="col-md-6 mb-3"><label class="form-label"><?= lang('Marketing.rental_type') ?></label>
                            <select class="form-select" name="jenis_sewa">
                                <option value="BULANAN" selected><?= lang('Marketing.monthly') ?></option>
                                <option value="HARIAN"><?= lang('Marketing.daily') ?></option>
                            </select>
                        </div>
                        <div class="col-md-6"></div> <!-- Empty space for alignment -->
                        <div class="col-12 mb-3"><label class="form-label"><?= lang('Marketing.notes') ?></label><textarea class="form-control" name="catatan" rows="3" placeholder="<?= lang('Marketing.additional_notes_optional') ?>"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="submit_action" id="submitAction" value="save_and_spec">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnSaveOnly" class="btn btn-primary">Simpan Kontrak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('Marketing.add_location') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addLocationForm">
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="locationCustomerId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_location_name">Location Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_location_name" name="location_name" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
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
                            <div class="form-group">
                                <label for="loc_area_id">Area <span class="text-danger">*</span></label>
                                <select class="form-control" id="loc_area_id" name="area_id" required>
                                    <option value="">Select Area</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="loc_contact_person" name="contact_person" maxlength="255">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_phone">Phone</label>
                                <input type="text" class="form-control" id="loc_phone" name="phone" maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_email">Email</label>
                                <input type="email" class="form-control" id="loc_email" name="email" maxlength="128">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="loc_address">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="loc_address" name="address" rows="3" maxlength="500" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="loc_city">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_city" name="city" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="loc_province">Province <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_province" name="province" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="loc_postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="loc_postal_code" name="postal_code" maxlength="10">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="loc_notes">Notes</label>
                        <textarea class="form-control" id="loc_notes" name="notes" rows="2" maxlength="255"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="loc_is_primary" name="is_primary" value="1">
                            <label class="form-check-label" for="loc_is_primary">
                                Set as Primary Location
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Location</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal SPK dari Kontrak -->
<div class="modal fade" id="spkFromKontrakModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= lang('Marketing.create_spk') ?></h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="spkFromKontrakForm">
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
                    <div id="attachmentTargetSection" style="display: none;">
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
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary" type="submit">Buat SPK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let customerTable;
let currentCustomerId = null;
let currentCustomerName = null;
let currentContractId = null;

$(document).ready(function() {
    console.log('🚀 Initializing Customer Management...');
    
    // Check if DataTables library loaded properly
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables library not loaded!');
        setTimeout(function() {
            location.reload();
        }, 2000);
        return;
    }
    
    // Initialize components with proper sequence
    setTimeout(function() {
        initializeCustomerTable();
    }, 100); // Small delay to ensure DOM is ready
    
    // Statistics will be auto-calculated by helper
    // No need to call loadStatistics() manually
    
    // Setup tab event handlers
    setupTabHandlers();
});

// Initialize DataTable with MAXIMUM PERFORMANCE optimization
function initializeCustomerTable() {
    // Destroy existing table if exists
    if ($.fn.DataTable.isDataTable('#customerTable')) {
        $('#customerTable').DataTable().destroy();
    }
    
    console.log('🔄 Initializing Customer DataTable with date filter helper...');
    
    try {
        // DataTable configuration
        var customerConfig = {
            processing: true,
            serverSide: true,
            deferRender: false,
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
            stateSave: false,
            search: {
                smart: false,
                caseInsensitive: true
            },
            searchDelay: 600,
            ajax: {
                url: '<?= base_url('marketing/customer-management/getCustomers') ?>',
                type: 'POST',
                timeout: 15000,
                error: function(xhr, error, code) {
                    console.error('DataTable AJAX Error:', error);
                    $('#customerTable_processing').hide();
                    showNotification('Failed to load customer data. Please refresh the page.', 'error');
                }
            },
            columns: [
            { 
                data: 'customer_code', 
                name: 'customer_code',
                render: function(data, type, row) {
                    return data || '-';
                }
            },
            { 
                data: 'customer_name', 
                name: 'customer_name'
            },
            { 
                data: 'area_name', 
                name: 'area_name',
                render: function(data, type, row) {
                    if (!data) return '-';
                    const areas = data.split(', ');
                    return areas.length > 2 ? areas[0] + ' +' + (areas.length - 1) : data;
                }
            },
            { 
                data: 'locations_count', 
                name: 'locations_count',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return data || 0;
                }
            },
            { 
                data: 'contracts_count', 
                name: 'contracts_count',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return data || 0;
                }
            },
            { 
                data: 'total_units', 
                name: 'total_units',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return data || 0;
                }
            },
            { 
                data: 'is_active', 
                name: 'is_active',
                className: 'text-center',
                render: function(data, type, row) {
                    return data == 1 ? 'ACTIVE' : 'INACTIVE';
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            }
        ],
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>t<"row"<"col-sm-6"i><"col-sm-6"p>>', // Simplified DOM
        language: {
            processing: "Loading customer data...",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: { "first": "First", "last": "Last", "next": "Next", "previous": "Previous" }
        },
        order: [[1, 'asc']],
        rowCallback: function(row, data) {
            // Minimal row callback for better performance
            row.style.cursor = 'pointer';
            row.onclick = function() { openCustomerDetail(data.id); };
        },
        initComplete: function(settings, json) {
            console.log('✅ DataTable initialized successfully');
            console.log('📊 Loaded', json.recordsTotal, 'total records');
        },
        drawCallback: function(settings) {
            // Use DataTable API to get record count
            const api = new $.fn.dataTable.Api(settings);
            const recordsDisplay = api.page.info().recordsDisplay;
            console.log('🎨 DataTable drawn with', recordsDisplay, 'visible records');
        }
    };
    
    // Initialize DataTable with date filter using new helper
    customerTable = initDataTableWithDateFilter({
        pickerId: 'customerDateRangePicker',
        tableId: 'customerTable',
        tableConfig: customerConfig,
        autoCalculateStats: true, // Enable auto-calculate dari data table
        statsConfig: {
            total: '#stat-total-customers', // Count semua rows
            active: {
                selector: '#stat-active-customers',
                filter: row => row.is_active == 1
            },
            contracts: {
                selector: '#stat-total-contracts',
                calculate: function(data) {
                    return data.reduce((sum, row) => sum + (parseInt(row.contracts_count) || 0), 0);
                }
            },
            units: {
                selector: '#stat-total-units',
                calculate: function(data) {
                    return data.reduce((sum, row) => sum + (parseInt(row.total_units) || 0), 0);
                }
            }
        },
        onTableReady: function(table) {
            console.log('✅ Customer table ready with auto-calculated stats');
        },
        debug: true
    });
    
    } catch (error) {
        console.error('❌ DataTable initialization failed:', error);
        showNotification('Failed to initialize customer table. Please refresh the page.', 'error');
    }
}

// Load statistics with optional date filter
function loadStatistics(startDate, endDate) {
    var data = {};
    if (startDate && endDate) {
        data = { start_date: startDate, end_date: endDate };
        console.log('📊 Loading customer statistics WITH filter:', data);
    } else {
        console.log('📊 Loading customer statistics WITHOUT filter (all data)');
    }
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getCustomerStats') ?>',
        type: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                console.log('✅ Statistics loaded:', stats);
                $('#stat-total-customers').text(stats.total_customers || 0);
                $('#stat-active-customers').text(stats.active_customers || 0);
                $('#stat-total-contracts').text(stats.total_contracts || 0);
                $('#stat-total-units').text(stats.total_units || 0);
            } else {
                console.error('❌ Failed to load statistics:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ AJAX error loading statistics:', error);
            console.error('   Response:', xhr.responseText);
        }
    });
}

// Open customer detail with lazy loading optimization
function openCustomerDetail(customerId) {
    currentCustomerId = customerId;
    
    // Show modal immediately with loading state
    $('#customerDetailModal').modal('show');
    $('#customerDetailContent').html('<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading customer details...</p></div>');
    
    // Lazy load customer data
    setTimeout(function() {
        $.ajax({
            url: `<?= base_url('marketing/customer-management/getCustomerDetail') ?>/${customerId}`,
            type: 'GET',
            timeout: 5000,
            success: function(response) {
                if (response.success) {
                    displayCustomerDetail(response.data);
                } else {
                    $('#customerDetailContent').html('<div class="alert alert-danger">Failed to load customer details</div>');
                }
            },
            error: function() {
                $('#customerDetailContent').html('<div class="alert alert-danger">Error loading customer details</div>');
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
    
    // Company Info (Area dihapus karena tidak relevan - setiap lokasi punya area sendiri)
    const companyHtml = `
        <table class="table table-sm table-borderless">
            <tr><td><strong>Customer Code:</strong></td><td>${customer.customer_code || 'N/A'}</td></tr>
            <tr><td><strong>Customer Name:</strong></td><td>${customer.customer_name || 'N/A'}</td></tr>
            <tr><td><strong>Created:</strong></td><td>${customer.created_at ? new Date(customer.created_at).toLocaleDateString('id-ID') : 'N/A'}</td></tr>
            <tr><td><strong>Last Updated:</strong></td><td>${customer.updated_at ? new Date(customer.updated_at).toLocaleDateString('id-ID') : 'N/A'}</td></tr>
            <tr><td><strong>Status:</strong></td><td>
                <span class="badge bg-${customer.is_active == 1 ? 'success' : 'secondary'}">
                    ${customer.is_active == 1 ? 'ACTIVE' : 'INACTIVE'}
                </span>
            </td></tr>
        </table>
    `;
    $('#companyInfo').html(companyHtml);
    
    // Customer Stats
    const statsHtml = `
        <table class="table table-sm table-borderless">
            <tr><td><strong>Total Locations:</strong></td><td><span class="badge bg-info">${stats.total_locations || 0}</span></td></tr>
            <tr><td><strong>Total Contracts:</strong></td><td><span class="badge bg-success">${stats.total_contracts || 0}</span></td></tr>
            <tr><td><strong>Total POs:</strong></td><td><span class="badge bg-warning text-dark">${contracts.filter(c => c.no_po_marketing).length || 0}</span></td></tr>
        </table>
    `;
    $('#customerStats').html(statsHtml);
    
    // Update tab badges
    $('#locationCount').text(stats.total_locations || 0);
    $('#contractCount').text(stats.total_contracts || 0);
    
    // Load contracts when tab is clicked (will be loaded when tab is shown)
}

// Load customer contracts
function loadCustomerContracts(customerId) {
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getCustomerContracts') ?>/${customerId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayContracts(response.data);
                $('#contractCount').text(response.data.length);
            } else {
                $('#contractsTable tbody').html('<tr><td colspan="6" class="text-center text-muted">No contracts found</td></tr>');
            }
        },
        error: function() {
            $('#contractsTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading contracts</td></tr>');
        }
    });
}

// Display contracts
function displayContracts(contracts) {
    let html = '';
    
    contracts.forEach(contract => {
        const statusBadge = getStatusBadge(contract.status);
        html += `
            <tr style="cursor: pointer;" onclick="openContractDetail(${contract.id})">
                <td><strong>${contract.no_kontrak}</strong></td>
                <td>${contract.no_po_marketing || '-'}</td>
                <td>${contract.lokasi || '-'}</td>
                <td>${contract.tanggal_mulai} - ${contract.tanggal_selesai}</td>
                <td class="text-center"><span class="badge bg-primary">${contract.total_units || 0}</span></td>
                <td>${statusBadge}</td>
            </tr>
        `;
    });
    
    $('#contractsTable tbody').html(html || '<tr><td colspan="6" class="text-center text-muted">No contracts found</td></tr>');
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
                    <tr><td><strong>No. PO Marketing:</strong></td><td>${contract.no_po_marketing || '-'}</td></tr>
                    <tr><td><strong>Customer:</strong></td><td>${contract.customer_name || '-'}</td></tr>
                    <tr><td><strong>Lokasi:</strong></td><td>${contract.location_name || '-'}</td></tr>
                    <tr><td><strong>PIC:</strong></td><td>${contract.contact_person || '-'}</td></tr>
                    <tr><td><strong>Kontak:</strong></td><td>${contract.phone || '-'}</td></tr>
                    <tr><td><strong>Alamat:</strong></td><td>${contract.address || 'Alamat belum tersedia'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    
                    <tr><td><strong>Jenis Sewa:</strong></td><td><span class="badge bg-info">${contract.jenis_sewa || 'BULANAN'}</span></td></tr>
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
                        <span class="badge bg-primary ms-auto">${locationUnits.length} Unit${locationUnits.length > 1 ? 's' : ''}</span>
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
                <tr style="cursor: pointer;" onclick="showUnitDetail(${unitId})" 
                    onmouseover="this.style.backgroundColor='#f8f9fa'" 
                    onmouseout="this.style.backgroundColor=''">
                    <td><strong>${unit.no_unit || '-'}</strong></td>
                    <td><code>${unit.serial_number || '-'}</code></td>
                    <td>${tipe}</td>
                    <td>${merkModel}</td>
                    <td>${kapasitas}</td>
                    <td><span class="text-${statusColor}"><i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>${status}</span></td>
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
    // Load areas for primary location dropdown
    loadAreas();
    
    // Generate customer code automatically
    generateCustomerCode();
    
    // Clear previous validation states
    clearFormErrors('#addCustomerForm');

    // Show modal
    $('#addCustomerModal').modal('show');
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

function openAddLocationModal() {
    clearFormErrors('#addLocationForm'); // Clear errors on open
    $('#addLocationModal .modal-title').text('Add New Location');
    $('#addLocationForm').removeData('location-id');
    // Clear location code field for new location
    $('#loc_location_code').val('');
    // Set customer ID if we have one
    if (currentCustomerId) {
        $('#locationCustomerId').val(currentCustomerId);
    }
    
    // Load areas for location dropdown
    loadLocationAreas();
    
    // Show modal
    $('#addLocationModal').modal('show');
}

function openEditLocationModal(locationId) {
    clearFormErrors('#addLocationForm');
    
    // Load areas first
    loadLocationAreas();
    
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
                
                // Set area_id after areas are loaded
                if (loc.area_id) {
                    setTimeout(() => {
                        $('#loc_area_id').val(loc.area_id);
                    }, 100);
                }
                
                $('#addLocationModal').modal('show');
            } else {
                console.error('Failed to load location:', response.message);
                showNotification(response.message || 'Failed to load location', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading location:', {xhr, status, error});
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
            <td class="text-muted pe-3" style="width: 140px; font-size: 0.9em;">${label}</td>
            <td class="fw-medium text-dark" style="font-size: 0.95em;">${val || '-'}</td>
        </tr>`;

    // Helper untuk section header kecil
    const sectionHeader = (title, icon) => `
        <tr>
            <td colspan="2" class="pt-3 pb-1">
                <h6 class="text-primary border-bottom pb-1 mb-0" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                    <i class="${icon} me-2"></i>${title.toUpperCase()}
                </h6>
            </td>
        </tr>`;

    // Set subtitle modal
    $('#unitSubtitle').html(`
        <span class="badge bg-secondary me-2">${unit.merk_unit || 'N/A'}</span>
        <span class="text-muted">${unit.model_unit || ''}</span>
    `);

    let detailHtml = `
        <div class="container-fluid px-0">
            <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded mb-3 border">
                <div>
                    <div class="text-muted small text-uppercase" style="font-size: 0.75rem;">Nomor Unit</div>
                    <h4 class="mb-0 fw-bold text-dark">${unit.no_unit || '-'}</h4>
                </div>
                <div class="text-end">
                    <div class="text-muted small text-uppercase" style="font-size: 0.75rem;">Status</div>
                    <div class="d-flex align-items-center justify-content-end text-${getStatusColor(unit.status_unit_name)}">
                        <i class="fas fa-circle me-2" style="font-size: 0.6rem;"></i>
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
                        <span class="badge bg-secondary text-light fw-normal" style="font-size:0.7em">${att.serial_number || 'No SN'}</span>
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
    const statusMap = {
        'Aktif': 'success',
        'Active': 'success',
        'Expired': 'danger',
        'Berakhir': 'danger',
        'Expiring': 'warning',
        'Draft': 'secondary'
    };
    
    const badgeClass = statusMap[status] || 'secondary';
    return `<span class="badge bg-${badgeClass}">${status || 'Unknown'}</span>`;
}

function getStatusBadgeClass(status) {
    if (!status) return 'secondary';
    const statusLower = status.toLowerCase();
    
    if (statusLower.includes('tersedia') || statusLower.includes('available')) return 'success';
    if (statusLower.includes('rental') || statusLower.includes('disewa')) return 'primary';
    if (statusLower.includes('maintenance') || statusLower.includes('rusak')) return 'warning';
    if (statusLower.includes('hilang') || statusLower.includes('lost')) return 'danger';
    
    return 'secondary';
}

function showNotification(message, type) {
    if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
        OptimaPro.showNotification(message, type);
        
        // Ensure notification z-index is above modals
        setTimeout(() => {
            $('.optima-notification, .notification-container, .toast-container').css('z-index', '9999');
        }, 100);
    } else {
        alert(message);
    }
}

// Auto-check DataTable visibility and reinitialize if needed
function checkDataTableVisibility() {
    setTimeout(function() {
        // Check if table wrapper is visible but no rows showing
        const tableWrapper = $('.dataTables_wrapper');
        const tableRows = $('#customerTable tbody tr');
        const isProcessing = $('.dataTables_processing').is(':visible');
        
        if (tableWrapper.length > 0 && tableRows.length === 0 && !isProcessing) {
            console.warn('⚠️ DataTable appears empty, checking status...');
            
            // Check if it's just "No data" or actually broken
            const noDataMessage = $('#customerTable tbody tr td').text();
            if (noDataMessage.includes('No data') || noDataMessage.includes('No matches')) {
                console.log('📄 Table is empty (no data)');
            } else {
                console.warn('🔄 Table seems broken, reinitializing...');
                refreshData();
            }
        }
    }, 2000); // Check after 2 seconds
}

// Call visibility check after initialization
$(document).ready(function() {
    checkDataTableVisibility();
});

function refreshData() {
    console.log('🔄 Refreshing customer data...');
    
    try {
        // Check if DataTable exists and is initialized
        if (customerTable && $.fn.DataTable.isDataTable('#customerTable')) {
            customerTable.ajax.reload(function(json) {
                console.log('✅ DataTable reloaded successfully');
                // Statistics auto-calculated by helper on draw
                showNotification('Data refreshed successfully', 'success');
            }, false); // false = don't reset paging
        } else {
            console.warn('⚠️ DataTable not initialized, reinitializing...');
            // Reinitialize table if it doesn't exist
            initializeCustomerTable();
            // Statistics auto-calculated by helper
            showNotification('Table reinitialized', 'info');
        }
    } catch (error) {
        console.error('❌ Error refreshing data:', error);
        // Fallback: reload the page
        showNotification('Reloading page to fix display issue...', 'warning');
        setTimeout(function() {
            location.reload();
        }, 1500);
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
            const isPrimary = location.is_primary ? '<span class="badge bg-primary ms-2">Primary</span>' : '';
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
                                    <button class="btn btn-sm btn-outline-primary" onclick="openEditLocationModal(${location.id})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-12">
                                    <strong><i class="fas fa-map-marked-alt me-1"></i> Area:</strong><br>
                                    <span class="badge bg-info">${location.area_name || 'N/A'}</span>
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


function loadAreas() {
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getAreas') ?>',
        method: 'GET',
        success: function(response) {
            console.log('Areas response:', response);
            if (response.success) {
                const areaSelect = $('#area_id');
                areaSelect.empty().append('<option value="">Select Area</option>');
                response.data.forEach(area => {
                    areaSelect.append(`<option value="${area.id}">${area.area_code} - ${area.area_name}</option>`);
                });
            } else {
                console.error('Error loading areas:', response.message);
                notify('Error loading areas: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading areas:', {xhr, status, error});
            notify('Error loading areas: ' + error, 'error');
        }
    });
}

function loadLocationAreas() {
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getAreas') ?>',
        method: 'GET',
        success: function(response) {
            console.log('Location Areas response:', response);
            if (response.success) {
                const areaSelect = $('#loc_area_id');
                areaSelect.empty().append('<option value="">Select Area</option>');
                response.data.forEach(area => {
                    areaSelect.append(`<option value="${area.id}">${area.area_code} - ${area.area_name}</option>`);
                });
            } else {
                console.error('Error loading areas for location:', response.message);
                notify('Error loading areas: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading areas for location:', {xhr, status, error});
            notify('Error loading areas: ' + error, 'error');
        }
    });
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

$(document).on('submit', '#addLocationForm', function(e) {
    e.preventDefault();
    clearFormErrors('#addLocationForm');

    const locId = $('#addLocationForm').data('location-id');
    const url = locId ? `<?= base_url('marketing/customer-management/updateCustomerLocation') ?>/${locId}` : '<?= base_url('marketing/customer-management/storeCustomerLocation') ?>';

    $.ajax({
        url: url,
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#addLocationModal').modal('hide');
                $('#addLocationForm')[0].reset();
                $('#addLocationForm').removeData('location-id');
                
                // Reload locations if we're in customer detail modal
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

// Auto-refresh mechanism when user returns to tab
$(window).on('focus', function() {
    console.log('👀 Window focused, checking table status...');
    
    // Only auto-refresh if table has been idle for more than 10 seconds
    if (customerTable && $.fn.DataTable.isDataTable('#customerTable')) {
        const now = new Date().getTime();
        if (!window.lastTableRefresh || (now - window.lastTableRefresh) > 10000) {
            console.log('🔄 Auto-refreshing data on window focus...');
            customerTable.ajax.reload(null, false); // Don't reset paging
            window.lastTableRefresh = now;
        }
    }
});

// Track when table was last refreshed
$(document).ready(function() {
    window.lastTableRefresh = new Date().getTime();
});
</script>

<?= $this->endSection() ?>


