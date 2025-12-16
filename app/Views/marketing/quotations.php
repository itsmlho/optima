<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Date Range Filter - Top Right -->
<div class="row mt-3">
    <div class="col-md-12 text-end">
        <div class="d-inline-block">
            <?= view('components/date_range_filter', [
                'id' => 'quotationDateRangePicker'
            ]) ?>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-file-text stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-quotations">0</div>
                    <div class="text-muted">Total Quotations</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-clock stat-icon text-warning"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-pending">0</div>
                    <div class="text-muted">Pending</div>
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
                    <div class="stat-value" id="stat-approved">0</div>
                    <div class="text-muted">Approved</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-danger-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-x-circle stat-icon text-danger"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-rejected">0</div>
                    <div class="text-muted">Rejected</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quotations Table Card -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-file-text me-2 text-primary"></i>
                Prospect & Quotations Management
            </h5>
            <p class="text-muted small mb-0">Kelola prospect dan penawaran harga untuk pelanggan</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openCreateProspectModal()">
            <i class="bi bi-plus-circle me-2"></i>Add Prospect
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="quotationsTable" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Quotation Number</th>
                        <th>Prospect Name</th>
                        <th>Quotation Title</th>
                        <th>Amount</th>
                        <th>Stage</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Prospect Modal -->
<div class="modal fade" id="createProspectModal" tabindex="-1" aria-labelledby="createProspectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-muted">
                <h5 class="modal-title fw-600" id="createProspectModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Create New Prospect
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createProspectForm" novalidate>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Step 1:</strong> Create prospect with basic company information. 
                        You can add quotation specifications in the next step.
                    </div>
                    
                    <h6 class="mb-3"><i class="fas fa-building me-2"></i>Company Information</h6>
                    
                    <!-- Smart Customer Search -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="linkExistingCustomer">
                            <label class="form-check-label" for="linkExistingCustomer">
                                <i class="fas fa-link me-1"></i>Existing Customer
                            </label>
                        </div>
                    </div>
                    
                    <!-- Customer Search Section -->
                    <div id="customerSearchSection" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-search me-2"></i>Search and link to existing customer to avoid data duplication.
                        </div>
                        <div class="mb-3">
                            <label for="customerSearchInput" class="form-label">Search Customer</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="customerSearchInput" placeholder="Type customer name...">
                                <button type="button" class="btn btn-outline-secondary" id="searchCustomerBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div id="customerSearchResults"></div>
                        <input type="hidden" id="selectedCustomerId" name="existing_customer_id">
                    </div>
                    
                    <!-- New Customer Section -->
                    <div id="newCustomerSection">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prospectCompanyName" class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="prospectCompanyName" name="prospect_name" required>
                                    <small class="form-text text-muted">Will be used to create new customer if prospect converts</small>
                                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectContactPerson" class="form-label">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prospectContactPerson" name="prospect_contact_person" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="prospectEmail" name="prospect_email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectPhone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="prospectPhone" name="prospect_phone">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="prospectAddress" class="form-label">Address</label>
                        <textarea class="form-control" id="prospectAddress" name="prospect_address" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectCity" class="form-label">City</label>
                                <input type="text" class="form-control" id="prospectCity" name="prospect_city">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectProvince" class="form-label">Province</label>
                                <input type="text" class="form-control" id="prospectProvince" name="prospect_province">
                            </div>
                        </div>
                    </div>
                </div>
                    
                    <!-- Quotation Details Section (Always visible) -->
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-file-contract me-2"></i>Quotation Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quotationTitle" class="form-label">Quotation Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="quotationTitle" name="quotation_title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="validUntil" class="form-label">Valid Until <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="validUntil" name="valid_until" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quotationDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="quotationDescription" name="quotation_description" rows="2" placeholder="Brief description of the quotation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Prospect
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="detailModalLabel">Quotation Details</h5>
                    <small class="text-muted" id="quotationSubtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-3" id="quotationDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="quotation-info-tab" data-bs-toggle="tab" data-bs-target="#quotation-info-content" type="button" role="tab" aria-controls="quotation-info-content" aria-selected="true">
                            <i class="fas fa-file-alt me-1"></i>Quotation Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications-content" type="button" role="tab" aria-controls="specifications-content" aria-selected="false">
                            <i class="fas fa-cogs me-1"></i>Specifications (<span id="specCountQuotation">0</span>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="quotationDetailTabContent">
                    <!-- Quotation Info Tab -->
                    <div class="tab-pane fade show active" id="quotation-info-content" role="tabpanel" aria-labelledby="quotation-info-tab">
                        <div id="detailContent">
                            <!-- Content will be loaded dynamically -->
                        </div>
                    </div>

                    <!-- Specifications Tab -->
                    <div class="tab-pane fade" id="specifications-content" role="tabpanel" aria-labelledby="specifications-tab">
                        <!-- Header with Add Buttons -->
                        <div class="mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-2 mb-md-0"><strong>Specifications for SPK</strong></h6>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <button class="btn btn-primary btn-sm me-2" onclick="openAddSpecificationModal()" type="button">
                                        <i class="fas fa-plus me-1"></i>Unit
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="openAddAttachmentModal()" type="button">
                                        <i class="fas fa-plus me-1"></i>Attachment Only
                                    </button>
                                </div>
                            </div>
                        </div>                        <!-- SPK Creation Info Alert - only visible when quotation is DEAL status -->
                        <div class="alert alert-info d-none" id="spkCreationInfo">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Quotation DEAL!</strong> You can create an SPK based on the specifications that have been made.
                        </div>

                        <div id="spesifikasiListContract">
                            <p class="text-muted">Loading specifications...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div id="quotationActions">
                    <!-- Action buttons will be populated dynamically -->
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Unified Specification Modal (Add & Edit) -->
<div class="modal fade" id="addSpecificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-muted" id="specModalHeader">
                <h6 class="modal-title fw-600" id="specModalTitle">
                    <i class="fas fa-cogs me-2"></i>Add Unit Specification
                </h6>
                <button class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSpecificationForm" method="post" action="javascript:void(0)">
                <div class="modal-body">
                    <input type="hidden" name="id_quotation" id="specQuotationId">
                    <input type="hidden" name="id_specification" id="specId">
                    <input type="hidden" name="specification_type" id="specType" value="UNIT">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Quantity Required</label>
                            <input type="number" class="form-control" name="quantity" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Specification Name</label>
                            <input type="text" class="form-control" name="specification_name" placeholder="Optional">
                            <small class="text-muted">Enter description, e.g. "Specification 1", "Spare Unit", "Additional Unit", etc.</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Monthly Rental Price <span class="text-danger" id="monthlyPriceRequired">*</span></label>
                            <input type="number" class="form-control" name="unit_price" id="monthlyPrice" step="0.01" placeholder="Rp per unit per month">
                            <small class="text-muted">Fill in at least one: Monthly or Daily price</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Daily Rental Price <span class="text-danger" id="dailyPriceRequired">*</span></label>
                            <input type="number" class="form-control" name="harga_per_unit_harian" id="dailyPrice" step="0.01" placeholder="Rp per unit per day">
                            <small class="text-muted">Fill in at least one: Monthly or Daily price</small>
                        </div>
                        
                        <div class="col-12"><hr><h6>Technical Specifications</h6></div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select" name="departemen_id" id="specDepartemen" required></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_unit_id" id="specTipeUnit" required>
                                <option value="">-- Select Unit Type --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Capacity</label>
                            <select class="form-select" name="kapasitas_id" id="specKapasitas">
                                <option value="">-- Select Capacity --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit Brand</label>
                            <select class="form-select" name="brand_id" id="specMerkUnit">
                                <option value="">-- Select Brand --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Battery Type</label>
                            <select class="form-select" name="battery_id" id="specJenisBaterai">
                                <option value="">-- Select Battery --</option>
                            </select>
                            <small class="text-muted">Available for Electric units only</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Charger</label>
                            <select class="form-select" name="charger_id" id="specCharger"></select>
                            <small class="text-muted">Available for Electric units only</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Attachment Type</label>
                            <select class="form-select" name="attachment_id" id="specAttachmentTipe"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Valve</label>
                            <select class="form-select" name="valve_id" id="specValve"></select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Mast</label>
                            <select class="form-select" name="mast_id" id="specMast"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tire</label>
                            <select class="form-select" name="ban_id" id="specBan"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Wheel</label>
                            <select class="form-select" name="roda_id" id="specRoda"></select>
                        </div>
                        
                        <!-- Accessories Section -->
                        <div class="col-12"><hr><h6>Unit Accessories</h6></div>
                        <div class="col-12">
                            <div class="row g-2">
                                <!-- Row 1 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LAMPU UTAMA" id="acc_lampu_utama">
                                        <label class="form-check-label" for="acc_lampu_utama">Main Light</label>
                                        <small class="text-muted">(Main, Reverse, Signal, Stop)</small>
                                    </div>
                                </div>
                                
                                <!-- Row 2 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BLUE SPOT" id="acc_blue_spot">
                                        <label class="form-check-label" for="acc_blue_spot">Blue Spot</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="RED LINE" id="acc_red_line">
                                        <label class="form-check-label" for="acc_red_line">Red Line</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="WORK LIGHT" id="acc_work_light">
                                        <label class="form-check-label" for="acc_work_light">Work Light</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ROTARY LAMP" id="acc_rotary_lamp">
                                        <label class="form-check-label" for="acc_rotary_lamp">Rotary Lamp</label>
                                    </div>
                                </div>
                                
                                <!-- Row 3 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BACK BUZZER" id="acc_back_buzzer">
                                        <label class="form-check-label" for="acc_back_buzzer">Back Buzzer</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA AI" id="acc_camera_ai">
                                        <label class="form-check-label" for="acc_camera_ai">Camera AI</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA" id="acc_camera">
                                        <label class="form-check-label" for="acc_camera">Camera</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SENSOR PARKING" id="acc_sensor_parking">
                                        <label class="form-check-label" for="acc_sensor_parking">Sensor Parking</label>
                                    </div>
                                </div>
                                
                                <!-- Row 4 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPEED LIMITER" id="acc_speed_limiter">
                                        <label class="form-check-label" for="acc_speed_limiter">Speed Limiter</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LASER FORK" id="acc_laser_fork">
                                        <label class="form-check-label" for="acc_laser_fork">Laser Fork</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="VOICE ANNOUNCER" id="acc_voice_announcer">
                                        <label class="form-check-label" for="acc_voice_announcer">Voice Announcer</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN SPEAKER" id="acc_horn_speaker">
                                        <label class="form-check-label" for="acc_horn_speaker">Horn Speaker</label>
                                    </div>
                                </div>
                                
                                <!-- Row 5 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN KLASON" id="acc_horn_klason">
                                        <label class="form-check-label" for="acc_horn_klason">Horn Klason</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BIO METRIC" id="acc_bio_metric">
                                        <label class="form-check-label" for="acc_bio_metric">Bio Metric</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ACRYLIC" id="acc_acrylic">
                                        <label class="form-check-label" for="acc_acrylic">Acrylic</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="P3K" id="acc_p3k">
                                        <label class="form-check-label" for="acc_p3k">First Aid Kit</label>
                                    </div>
                                </div>
                                
                                <!-- Row 6 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SAFETY BELT INTERLOC" id="acc_safety_belt">
                                        <label class="form-check-label" for="acc_safety_belt">Safety Belt Interlock</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPARS ARRESTOR" id="acc_spars_arrestor">
                                        <label class="form-check-label" for="acc_spars_arrestor">Spark Arrestor</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="submitSpecificationBtn">
                        <i class="fas fa-save me-1"></i><span id="submitBtnText">Save Specification</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Attachment Modal -->
<div class="modal fade" id="addAttachmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-muted">
                <h6 class="modal-title fw-600">
                    <i class="fas fa-paperclip me-2"></i>Add Attachment Specification
                </h6>
                <button class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAttachmentForm" method="post" action="javascript:void(0)">
                <div class="modal-body">
                    <input type="hidden" name="id_quotation" id="attachmentQuotationId">
                    <input type="hidden" name="specification_type" id="attachmentSpecType" value="ATTACHMENT">
                    <input type="hidden" name="category" value="ATTACHMENT">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Quantity Required <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Specification Name</label>
                            <input type="text" class="form-control" name="specification_name" placeholder="Optional">
                            <small class="text-muted">e.g., "Fork Attachment", "Side Shifter", etc.</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Monthly Rental Price <span class="text-muted">(Optional)</span></label>
                            <input type="number" class="form-control" name="unit_price" id="attachmentMonthlyPrice" step="0.01" placeholder="Rp per unit per month">
                            <small class="text-muted">Fill in at least one: Monthly or Daily</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Daily Rental Price <span class="text-muted">(Optional)</span></label>
                            <input type="number" class="form-control" name="harga_per_unit_harian" id="attachmentDailyPrice" step="0.01" placeholder="Rp per unit per day">
                            <small class="text-muted">Fill in at least one: Monthly or Daily</small>
                        </div>
                        
                        <div class="col-12"><hr><h6>Attachment Details</h6></div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Attachment Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="attachment_tipe" id="attachmentTipe" required></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Attachment Brand</label>
                            <input type="text" class="form-control" name="attachment_merk" placeholder="e.g., OEM, Cascade, etc.">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Description / Notes</label>
                            <textarea class="form-control" name="specification_description" rows="3" placeholder="Additional details about the attachment"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-info" id="submitAttachmentBtn">
                        <i class="fas fa-save me-1"></i>Save Attachment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unified Customer Location Modal (Select or Add) -->
<div class="modal fade" id="selectCustomerLocationModal" tabindex="-1" aria-labelledby="selectLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="selectLocationModalLabel">Customer Location</h5>
                    <small class="text-muted" id="locationModalCustomerName"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="locationModalAlert">
                    <i class="fas fa-map-marker-alt"></i> Please select a location or add new one.
                </div>
                
                <!-- Toggle Buttons -->
                <div class="btn-group w-100 mb-4" role="group" aria-label="Location mode">
                    <button type="button" class="btn btn-outline-primary active" id="btnSelectExisting">
                        <i class="fas fa-list"></i> Select Existing
                    </button>
                    <button type="button" class="btn btn-outline-success" id="btnAddNew">
                        <i class="fas fa-plus"></i> Add New Location
                    </button>
                </div>
                
                <!-- Select Existing Location Section -->
                <div id="existingLocationSection">
                    <div class="mb-3">
                        <label for="modalLocationSelect" class="form-label fw-bold">Existing Locations:</label>
                        <select class="form-control form-control-lg" id="modalLocationSelect">
                            <option value="">-- Select a location --</option>
                        </select>
                    </div>
                    
                    <!-- Location Details Preview -->
                    <div id="locationPreview" class="card d-none mt-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-map-marker-alt text-primary"></i> Location Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Location Name:</strong></p>
                                    <p class="text-muted" id="preview_location_name">-</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Contact Person:</strong></p>
                                    <p class="text-muted" id="preview_contact_person">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1"><strong>Address:</strong></p>
                                    <p class="text-muted" id="preview_address">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>City:</strong></p>
                                    <p class="text-muted" id="preview_city">-</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Province:</strong></p>
                                    <p class="text-muted" id="preview_province">-</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Contact Phone:</strong></p>
                                    <p class="text-muted" id="preview_contact_phone">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add New Location Section -->
                <div id="addNewLocationSection" style="display: none;">
                    <form id="customerLocationFormUnified">
                        <input type="hidden" name="customer_id" id="unified_customer_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unified_location_name" class="form-label">Location Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unified_location_name" name="location_name" required maxlength="255">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unified_location_code" class="form-label">Location Code</label>
                                <input type="text" class="form-control" id="unified_location_code" name="location_code" maxlength="50">
                                <small class="form-text text-muted">Optional - auto-generated if empty</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unified_area_id" class="form-label">Area <span class="text-danger">*</span></label>
                                <select class="form-control" id="unified_area_id" name="area_id" required>
                                    <option value="">Select Area</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unified_contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="unified_contact_person" name="contact_person" maxlength="255">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unified_phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="unified_phone" name="phone" maxlength="20">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unified_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="unified_email" name="email" maxlength="128">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="unified_address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="unified_address" name="address" rows="3" required maxlength="500"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="unified_city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unified_city" name="city" required maxlength="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unified_province" class="form-label">Province <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unified_province" name="province" required maxlength="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unified_postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="unified_postal_code" name="postal_code" maxlength="10">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="unified_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="unified_notes" name="notes" rows="2" maxlength="255"></textarea>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="unified_is_primary" name="is_primary" value="1" checked>
                            <label class="form-check-label" for="unified_is_primary">
                                Set as Primary Location
                            </label>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="continueWithLocationBtn">
                    <i class="fas fa-arrow-right"></i> Continue
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Contract Modal -->
<div class="modal fade" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Contract & PO</h5>
                    <small class="text-muted d-block" id="contractModalCustomerName"></small>
                    <small class="text-muted">Select existing contract or create new</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Information
                        </h6>
                        <p class="mb-0">
                            <strong>Select existing contract</strong> or <strong>create new</strong>. 
                            If you select existing, data will be loaded automatically. If you create new, fill out the form below.
                        </p>
                    </div>
                    
                    <!-- Single Dropdown for Contract/PO Selection -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Contract / PO <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="contractOrPOSelect" required style="background-color: #f8f9fa;">
                                <option value="">Select Contract / PO</option>
                                <option value="__ADD_NEW__" style="background-color: #e3f2fd; color: #1976d2; font-weight: 600;">
                                    &#43; Add New Contract
                                </option>
                            </select>
                            <small class="form-text text-muted">Search by Contract Number or PO Number, or select "+ Add New Contract"</small>
                        </div>
                    </div>
                    
                    <!-- Contract Form (shown when __ADD_NEW__ or existing selected) -->
                    <div id="contractFormSection" style="display:none;">
                        <hr class="my-4">
                        <h6 class="mb-3">Create New Contract</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contract Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="contract_number" id="contract_number_input" required>
                                    <button class="btn btn-outline-secondary" type="button" id="generateContractNumberBtn" title="Generate Contract Number">
                                        <i class="fas fa-magic"></i>
                                    </button>   
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Client PO Number</label>
                                <input type="text" class="form-control" name="po_number" id="po_number_input">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" id="customerNameDisplay" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" id="locationNameDisplay" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="contract_start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="end_date" id="contract_end_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rental Type</label>
                                <select class="form-select" name="jenis_sewa" id="contract_jenis_sewa">
                                    <option value="BULANAN" selected>Monthly</option>
                                    <option value="HARIAN">Daily</option>
                                </select>
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="catatan" id="contract_catatan" rows="3" placeholder="Additional notes (optional)"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden fields -->
                    <input type="hidden" name="quotation_id" id="contractQuotationId">
                    <input type="hidden" name="customer_id" id="customerIdContractNew">
                    <input type="hidden" name="location_id" id="locationIdContractNew">
                    <input type="hidden" name="workflow_completed" value="true">
                    <input type="hidden" id="selectedContractId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Contract
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create SPK Selection Modal -->
<div class="modal fade" id="createSPKModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-muted">
                <div>
                    <h5 class="modal-title">Create SPK from Quotation</h5>
                    <small class="d-block" id="spkModalQuotationInfo"></small>
                </div>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <form id="createSPKForm">
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong> Select specifications and quantities to create SPK. 
                        You can create multiple SPKs from different specifications.
                    </div>
                    
                    <!-- Customer & Contract Details -->
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-building me-2"></i>Customer & Contract Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2" id="spkCustomerDetails">
                                <div class="col-12 text-muted text-center py-2">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Loading customer details...
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delivery Date -->
                    <div class="row mb-12">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Estimated Delivery Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="spk_delivery_date" name="delivery_date" required>
                            <small class="text-muted">Target date for unit delivery, default set a 7-day lead time</small>
                        </div>
                    </div>
                    </br>

                    <!-- Specifications List -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Specifications to Create SPK:</label>
                    </div>
                    
                    <div id="spkSpecificationsList" class="border rounded p-3">
                        <!-- Will be populated dynamically -->
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <p>Loading specifications...</p>
                        </div>
                    </div>
                    
                    <!-- Hidden fields -->
                    <input type="hidden" id="spk_quotation_id" name="quotation_id">
                    <input type="hidden" id="spk_customer_id" name="customer_id">
                    <input type="hidden" id="spk_contract_id" name="contract_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitSPKBtn">
                        <i class="fas fa-check me-2"></i>Create Selected SPK(s)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Global variable for DataTable
var quotationsTable;

$(document).ready(function() {
    // DataTable configuration
    var quotationsConfig = {
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('marketing/quotations/data') ?>',
            type: 'POST',
            error: function(xhr, error, code) {
                console.error('DataTable AJAX error:', xhr.responseText);
                Swal.fire('Error', 'Failed to load data: ' + xhr.responseText, 'error');
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'quotation_number' },
            { data: 'prospect_name' },
            { data: 'quotation_title' },
            { 
                data: 'total_amount',
                render: function(data) {
                    return data || 'Rp 0';
                }
            },
            { 
                data: 'workflow_stage',
                orderable: false
            },
            { data: 'quotation_date' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[7, 'desc']],
        responsive: true,
        language: {
            processing: "Loading...",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No quotations found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries available",
            infoFiltered: "(filtered from _MAX_ total entries)"
        },
        rowCallback: function(row, data) {
            // Add pointer cursor and click functionality
            $(row).css('cursor', 'pointer');
            $(row).attr('title', 'Click to view details');
        }
    };
    
    // Initialize DataTable with date filter using new helper
    quotationsTable = initDataTableWithDateFilter({
        pickerId: 'quotationDateRangePicker',
        tableId: 'quotationsTable',
        tableConfig: quotationsConfig,
        autoCalculateStats: true, // Enable auto-calculate dari data table
        statsConfig: {
            total: '#stat-total-quotations', // Count semua rows
            pending: { 
                selector: '#stat-pending',
                filter: row => {
                    const stage = (row.workflow_stage || '').toUpperCase();
                    return stage.includes('PENDING') || stage.includes('DRAFT') || stage.includes('SENT');
                }
            },
            approved: {
                selector: '#stat-approved',
                filter: row => {
                    const stage = (row.workflow_stage || '').toUpperCase();
                    return stage.includes('ACCEPTED') || stage.includes('APPROVED') || stage.includes('WON');
                }
            },
            rejected: {
                selector: '#stat-rejected',
                filter: row => {
                    const stage = (row.workflow_stage || '').toUpperCase();
                    return stage.includes('REJECT') || stage.includes('LOST') || stage.includes('CANCEL');
                }
            }
        },
        onTableReady: function(table) {
            // Table initialization complete
            
            // Add row click functionality
            $('#quotationsTable tbody').on('click', 'tr', function(e) {
                // Don't trigger row click if user clicked on action buttons
                if (!$(e.target).closest('.btn, .dropdown').length) {
                    var data = table.row(this).data();
                    if (data && data.id_quotation) {
                        viewQuotation(data.id_quotation);
                    }
                }
            });
        },
        debug: true
    });

    // Form submission
    $('#quotationForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var url = '<?= base_url('marketing/quotations/store') ?>';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Success', response.message, 'success');
                    $('#quotationModal').modal('hide');
                    quotationsTable.ajax.reload();
                    loadStatistics();
                    $('#quotationForm')[0].reset();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                console.error('Form submission error:', xhr.responseText);
                Swal.fire('Error', 'Failed to save quotation', 'error');
            }
        });
    });

    // Reset form when modal is hidden
    $('#quotationModal').on('hidden.bs.modal', function() {
        $('#quotationForm')[0].reset();
        $('#quotationId').val('');
        $('#quotationModalLabel').text('Add New Quotation');
    });

    // === UNIFIED LOCATION MODAL EVENT HANDLERS ===
    // Cleanup: Remove any existing handlers first to prevent duplicates
    $('#continueWithLocationBtn').off('click');
    $('#btnSelectExisting').off('click');
    $('#btnAddNew').off('click');
    $('#modalLocationSelect').off('change');
    
    // Toggle between Select Existing and Add New modes
    $('#btnSelectExisting').on('click', function() {
        $(this).addClass('active');
        $('#btnAddNew').removeClass('active');
        $('#existingLocationSection').show();
        $('#addNewLocationSection').hide();
        $('#continueWithLocationBtn').html('<i class="fas fa-arrow-right"></i> Continue with Selected');
    });
    
    $('#btnAddNew').on('click', function() {
        $(this).addClass('active');
        $('#btnSelectExisting').removeClass('active');
        $('#existingLocationSection').hide();
        $('#addNewLocationSection').show();
        $('#continueWithLocationBtn').html('<i class="fas fa-save"></i> Save & Continue');
        
        // Load areas when switching to Add New mode
        loadAreasForLocation();
    });
    
    // Location select change handler - show preview
    $('#modalLocationSelect').on('change', function() {
        const selectedLocationId = $(this).val();
        const locations = $('#selectCustomerLocationModal').data('existingLocations') || [];
        
        if (selectedLocationId) {
            const selectedLocation = locations.find(loc => loc.id == selectedLocationId);
            if (selectedLocation) {
                $('#preview_location_name').text(selectedLocation.location_name || '-');
                $('#preview_contact_person').text(selectedLocation.contact_person || '-');
                $('#preview_address').text(selectedLocation.address || '-');
                $('#preview_city').text(selectedLocation.city || '-');
                $('#preview_province').text(selectedLocation.province || '-');
                $('#preview_contact_phone').text(selectedLocation.contact_phone || selectedLocation.phone || '-');
                $('#locationPreview').removeClass('d-none');
            }
        } else {
            $('#locationPreview').addClass('d-none');
        }
    });
    
    // Continue button handler for unified modal
    $('#continueWithLocationBtn').on('click', debounce(function() {
        const customerId = $('#selectCustomerLocationModal').data('customerId');
        const quotationId = $('#selectCustomerLocationModal').data('quotationId');
        const dealMessage = $('#selectCustomerLocationModal').data('dealMessage');
        const isAddNewMode = $('#btnAddNew').hasClass('active');
        
        if (isAddNewMode) {
            // Add New Location mode - validate and save
            const locationData = {
                customer_id: customerId,
                location_name: $('#unified_location_name').val().trim(),
                location_code: $('#unified_location_code').val().trim(),
                area_id: $('#unified_area_id').val(),
                contact_person: $('#unified_contact_person').val().trim(),
                phone: $('#unified_phone').val().trim(),
                email: $('#unified_email').val().trim(),
                address: $('#unified_address').val().trim(),
                city: $('#unified_city').val().trim(),
                province: $('#unified_province').val().trim(),
                postal_code: $('#unified_postal_code').val().trim(),
                notes: $('#unified_notes').val().trim(),
                is_primary: $('#unified_is_primary').is(':checked') ? 1 : 0
            };
            
            // Validate required fields
            if (!locationData.location_name || !locationData.address || !locationData.city || 
                !locationData.province || !locationData.area_id) {
                Swal.fire('Error', 'Please fill all required fields (marked with *)', 'error');
                return;
            }
            
            // Mark workflow completed and close modal
            $('#selectCustomerLocationModal').data('workflowCompleted', true);
            $('#selectCustomerLocationModal').modal('hide');
            
            // Save location
            saveCustomerLocation(customerId, locationData, quotationId);
            
        } else {
            // Select Existing mode - validate selection
            const selectedLocation = $('#modalLocationSelect').val();
            
            if (!selectedLocation) {
                Swal.fire('Error', 'Please select a location from the list', 'error');
                return;
            }
            
            // Store location ID globally for contract creation
            window.currentSelectedLocationId = selectedLocation;
            // Location ID stored for contract creation
            
            // Mark workflow completed and close modal
            $('#selectCustomerLocationModal').data('workflowCompleted', true);
            $('#selectCustomerLocationModal').modal('hide');
            
            // Update primary location and continue
            updateCustomerPrimaryLocation(customerId, selectedLocation, quotationId, dealMessage);
        }
    }, 300));
    
    // === CONTRACT MODAL EVENT HANDLERS ===
    
    // Dropdown change handler - show form and load data based on selection
    $('#contractOrPOSelect').on('change', function() {
        const selectedValue = $(this).val();
        const contractFormSection = $('#contractFormSection');
        
        if (selectedValue === '__ADD_NEW__') {
            // Show empty form for new contract
            resetContractForm();
            contractFormSection.slideDown();
            
            // Enable all fields for new entry
            $('#contract_number_input, #po_number_input').prop('disabled', false).prop('readonly', false).removeClass('bg-light');
            $('#contract_start_date, #contract_end_date, #contract_jenis_sewa').prop('disabled', false);
            $('#contract_catatan').prop('readonly', false).removeClass('bg-light');
            $('#generateContractNumberBtn').prop('disabled', false);
            
            // Clear customer and location (will use current quotation's customer)
            $('#customerNameDisplay').val('').prop('readonly', true);
            $('#locationNameDisplay').val('').prop('readonly', true);
            
            // Change button text for creating new contract
            $('#submitContractBtn').html('<i class="fas fa-save me-2"></i>Save New Contract');
            
        } else if (selectedValue) {
            // Load existing contract data - 100% READ-ONLY
            const selectedOption = $(this).find('option:selected');
            const contractData = selectedOption.data('contract');
            
            if (contractData) {
                populateContractForm(contractData);
                contractFormSection.slideDown();
                
                // Make ALL fields READ-ONLY for existing contract (Option 1: Pure Selection)
                $('#contract_number_input, #po_number_input').prop('readonly', true).addClass('bg-light');
                $('#customerNameDisplay, #locationNameDisplay').prop('readonly', true).addClass('bg-light');
                $('#contract_start_date, #contract_end_date, #contract_jenis_sewa').prop('disabled', true).addClass('bg-light');
                $('#contract_catatan').prop('readonly', true).addClass('bg-light');
                $('#generateContractNumberBtn').prop('disabled', true);
                
                // Change button text to indicate linking/using existing contract
                $('#submitContractBtn').html('<i class="fas fa-link me-2"></i>Use This Contract');
            }
            
        } else {
            // No selection - hide form
            contractFormSection.slideUp();
            resetContractForm();
        }
    });
    
    // Auto-load contract by number or PO (mutual search)
    let contractLoadTimeout;
    $('#contract_number_input, #po_number_input').on('input', function() {
        clearTimeout(contractLoadTimeout);
        const isContractNumber = $(this).attr('id') === 'contract_number_input';
        const field = isContractNumber ? 'no_kontrak' : 'no_po_marketing';
        const value = $(this).val().trim();
        
        if (value.length >= 3) {
            contractLoadTimeout = setTimeout(() => {
                searchAndLoadContract(field, value);
            }, 500);
        }
    });
    
    // Submit contract form - prevent default form submission
    $('#addContractForm').on('submit', function(e) {
        e.preventDefault(); // Prevent page reload
        
        const quotationId = window.currentContractQuotationId;
        const customerId = window.currentContractCustomerId;
        const selectedContractId = $('#contractOrPOSelect').val();
        
        if (selectedContractId && selectedContractId !== '__ADD_NEW__') {
            // OPTION 1: Link existing contract to quotation (READ-ONLY, no update)
            linkExistingContract(selectedContractId, quotationId);
            
        } else {
            // Create new contract
            const contractNumber = $('#contract_number_input').val().trim();
            const poNumber = $('#po_number_input').val().trim();
            const locationId = $('#locationIdContractNew').val();
            const startDate = $('#contract_start_date').val();
            const endDate = $('#contract_end_date').val();
            
            // Validate required fields for new contract
            if (!contractNumber && !poNumber) {
                Swal.fire('Error', 'Please fill Contract Number OR PO Number', 'error');
                return false;
            }
            
            if (!locationId || !startDate || !endDate) {
                Swal.fire('Error', 'Please fill all required fields', 'error');
                return false;
            }
            
            // Prepare form data for new contract
            const formData = {
                contract_number: contractNumber,
                po_number: poNumber,
                customer_location_id: locationId,
                start_date: startDate,
                end_date: endDate,
                jenis_sewa: $('#contract_jenis_sewa').val(),
                catatan: $('#contract_catatan').val(),
                quotation_id: quotationId,
                workflow_completed: true
            };
            
            saveNewContract(formData);
        }
        
        return false; // Prevent any default form behavior
    });
    
    // Clean up contract form when modal is closed
    // Clean up contract modal when closed
    $('#addContractModal').on('hidden.bs.modal', function() {
        // Reset dropdown to default
        $('#contractOrPOSelect').val('');
        
        // Hide and reset form
        $('#contractFormSection').hide();
        resetContractForm();
        
        // Reset hidden fields
        $('#customerIdContractNew').val('');
        $('#locationIdContractNew').val('');
        $('#contractQuotationId').val('');
        $('#customerNameDisplay').val('');
        $('#locationNameDisplay').val('');
        $('#selectedContractId').val('');
    });

    // === CUSTOMER SEARCH INITIALIZATION (moved from third ready block) ===
    initCustomerSearch();
    
    // Auto-run disabled - enable manually in console if needed: testWorkflowComplete()
});

// Debounce utility function to prevent multiple rapid clicks
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

let currentDateRange = { start: null, end: null };

function loadStatistics(startDate = null, endDate = null) {
    const params = {};
    if (startDate && endDate) {
        params.start_date = startDate;
        params.end_date = endDate;
    }
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/stats') ?>',
        type: 'POST',
        data: params,
        success: function(data) {
            $('#stat-total-quotations').text(data.total || 0);
            $('#stat-pending').text(data.pending || 0);
            $('#stat-approved').text(data.approved || 0);
            $('#stat-rejected').text(data.rejected || 0);
        },
        error: function(xhr, status, error) {
            console.error('❌ Failed to load quotation statistics:', error);
            console.error('   Response:', xhr.responseText);
        }
    });
}

function viewQuotation(id) {
    // Set current quotation ID for specifications
    currentQuotationId = id;
    
    $.get('<?= base_url('marketing/quotations/getQuotation/') ?>' + id, function(response) {
        // Handle different response formats
        if (response.status === 'error') {
            console.error('API returned error:', response.message);
            $('#detailContent').html('<div class="alert alert-danger">Error: ' + response.message + '</div>');
            $('#quotationActions').html('');
            $('#detailModal').modal('show');
            return;
        }
        
        // Use the data property for detailed information
        const data = response.data || response;
        
        // Check if data is valid
        if (!data.id_quotation) {
            console.error('Invalid quotation data:', data);
            $('#detailContent').html('<div class="alert alert-danger">Invalid quotation data received</div>');
            $('#quotationActions').html('');
            $('#detailModal').modal('show');
            return;
        }
        
        // Update modal subtitle
        $('#quotationSubtitle').text((data.quotation_number || 'undefined') + ' - ' + (data.prospect_name || 'No Customer'));
        
        var content = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Quotation Number:</strong><br>
                    ${data.quotation_number || 'undefined'}<br><br>
                    <strong>Customer:</strong><br>
                    ${data.customer_name || data.prospect_name || 'undefined'}<br><br>
                    <strong>Amount:</strong><br>
                    <strong class="text-success">Rp ${data.total_amount ? parseFloat(data.total_amount).toLocaleString('id-ID') : '0'}</strong><br><br>
                    ${data.location_name ? `
                    <strong>Customer Location:</strong><br>
                    <div class="ms-2 p-2 bg-light rounded border">
                        <div class="mb-1"><i class="fas fa-map-marker-alt text-primary me-2"></i><strong>${data.location_name}</strong></div>
                        ${data.location_address ? `<div class="mb-1"><small class="text-muted">${data.location_address}</small></div>` : ''}
                        ${data.pic_name ? `<div class="mb-1"><small><i class="fas fa-user text-info me-1"></i><strong>PIC:</strong> ${data.pic_name}</small></div>` : ''}
                        ${data.pic_phone ? `<div><small><i class="fas fa-phone text-success me-1"></i>${data.pic_phone}</small></div>` : ''}
                    </div><br>
                    ` : ''}
                    
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong><br>
                    <span class="badge bg-${data.stage === 'ACCEPTED' ? 'success' : data.stage === 'SENT' ? 'warning' : 'danger'}">${(data.stage || 'ERROR').toUpperCase()}</span><br><br>
                    <strong>Valid Until:</strong><br>
                    ${data.valid_until || 'undefined'}<br><br>
                    <strong>Created:</strong><br>
                    ${data.created_at || 'undefined'}
                </div>
            </div>
            <hr>
            <strong>Description:</strong><br>
            ${data.quotation_description || 'undefined'}<br><br>
            ${data.notes ? '<strong>Notes:</strong><br>' + data.notes : ''}
        `;
        $('#detailContent').html(content);
        
        // Populate action buttons based on workflow stage
        let actionButtons = '';
        const workflowStage = data.workflow_stage || 'PROSPECT';
        
        // Add edit button if allowed
        if (['PROSPECT', 'QUOTATION'].includes(workflowStage)) {
            actionButtons += `<button class="btn btn-warning me-2" onclick="editQuotation(${data.id_quotation})">
                <i class="fas fa-edit me-1"></i>Edit
            </button>`;
        }
        
        // Add delete button if not DEAL
        if (workflowStage !== 'DEAL') {
            actionButtons += `<button class="btn btn-danger" onclick="deleteQuotation(${data.id_quotation})">
                <i class="fas fa-trash me-1"></i>Delete
            </button>`;
        }
        
        $('#quotationActions').html(actionButtons);
        
        // Show/Hide SPK creation button and info based on workflow stage
        const spkButton = document.getElementById('createSpkFromQuotation');
        const spkInfo = document.getElementById('spkCreationInfo');
        
        if (workflowStage === 'DEAL') {
            if (spkButton) spkButton.style.display = 'inline-block';
            if (spkInfo) spkInfo.classList.remove('d-none');
        } else {
            if (spkButton) spkButton.style.display = 'none';
            if (spkInfo) spkInfo.classList.add('d-none');
        }
        
        // Store current quotation data for SPK creation
        window.currentQuotationForSPK = data;
        
        // Reset specifications tab to force reload
        $('#specifications-tab').removeClass('loaded');
        $('#spesifikasiListContract').html('<p class="text-muted">Click the Specifications tab to load data...</p>');
        
        $('#detailModal').modal('show');
        
        // Ensure tabs are properly initialized when modal is shown
        $('#detailModal').on('shown.bs.modal', function() {
            // Reset to first tab (Quotation Info) and ensure proper state
            $('#quotation-info-tab').tab('show');
            $('#quotation-info-tab').addClass('active').attr('aria-selected', 'true');
            $('#specifications-tab').removeClass('active').attr('aria-selected', 'false');
            $('#quotation-info-content').addClass('show active');
            $('#specifications-content').removeClass('show active');
        });
    }).fail(function(xhr) {
        console.error('AJAX Error details:', {
            status: xhr.status,
            statusText: xhr.statusText, 
            responseText: xhr.responseText,
            url: '<?= base_url('marketing/quotations/get-quotation/') ?>' + id
        });
        
        let errorMsg = 'Failed to load quotation details';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
        } else if (xhr.responseText) {
            try {
                const response = JSON.parse(xhr.responseText);
                errorMsg = response.message || errorMsg;
            } catch (e) {
                errorMsg += ' (Server error: ' + xhr.status + ')';
            }
        }
        
        // Show error in modal instead of SweetAlert
        $('#detailContent').html('<div class="alert alert-danger"><strong>Error:</strong> ' + errorMsg + '<br><small>Status: ' + xhr.status + ' - ' + xhr.statusText + '</small></div>');
        $('#quotationActions').html('');
        $('#detailModal').modal('show');
    });
}

function editQuotation(id) {
    $.get('<?= base_url('marketing/quotations/get-quotation/') ?>' + id, function(data) {
        $('#quotationId').val(data.id);
        $('#quotationNumber').val(data.quotation_number);
        $('#customerId').val(data.customer_id);
        $('#description').val(data.description);
        $('#amount').val(data.amount);
        $('#validUntil').val(data.valid_until);
        $('#notes').val(data.notes);
        $('#quotationModalLabel').text('Edit Quotation');
        $('#quotationModal').modal('show');
    }).fail(function() {
        Swal.fire('Error', 'Failed to load quotation data', 'error');
    });
}

function deleteQuotation(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('marketing/quotations/delete/') ?>' + id,
                type: 'DELETE',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Deleted!', response.message, 'success');
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // Close detail modal if deleted item is currently being viewed
                        if (currentQuotationId == id && $('#detailModal').hasClass('show')) {
                            $('#detailModal').modal('hide');
                        }
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete quotation', 'error');
                }
            });
        }
    });
}

// Global quotation variables
let currentQuotationId = null;

// Enhanced tab switching with proper cleanup
$(document).on('shown.bs.tab', '#quotationDetailTabs button[data-bs-toggle="tab"]', function (e) {
    const targetTab = $(e.target).attr('data-bs-target');
    
    // Clear any lingering active states
    $('#quotationDetailTabs .nav-link').removeClass('active').attr('aria-selected', 'false');
    $('.tab-pane').removeClass('show active');
    
    // Set correct active state for current tab
    $(e.target).addClass('active').attr('aria-selected', 'true');
    $(targetTab).addClass('show active');
});

// Event handler for specifications tab click
$(document).on('click', '#specifications-tab', function() {
    if (!currentQuotationId) {
        return;
    }
    
    // Check if tab is already loaded
    if ($(this).hasClass('loaded')) {
        return;
    }
    
    // Check quotation workflow stage before loading specifications
    $.get('<?= base_url('marketing/quotations/get-quotation/') ?>' + currentQuotationId, function(response) {
        
        // Handle error responses
        if (response.status === 'error') {
            const container = document.getElementById('spesifikasiListContract');
            container.innerHTML = `<div class="alert alert-danger">Error: ${response.message}</div>`;
            return;
        }
        
        // Use the data property for detailed information
        const quotation = response.data || response;
        
        if (quotation.id_quotation) {
            // Check workflow stage
            if (quotation.workflow_stage === 'PROSPECT') {
                // Show specifications tab but with convert message
                const container = document.getElementById('spesifikasiListContract');
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-arrow-circle-up fa-3x text-info"></i>
                        </div>
                        <h5 class="text-info">Convert to Quotation First</h5>
                        <p class="text-muted mb-4">
                            This is currently a prospect. Convert it to quotation to add specifications<br>
                            and define required units, quantities, and pricing.
                        </p>
                        <button class="btn btn-info btn-lg" onclick="convertToQuotation(${currentQuotationId})">
                            <i class="fas fa-arrow-right me-2"></i>Convert to Quotation
                        </button>
                    </div>
                `;
                $('#specifications-tab').addClass('loaded');
                return;
            }
            
            if (['DEAL', 'NOT_DEAL'].includes(quotation.workflow_stage)) {
                // Load specifications but in read-only mode
                loadQuotationSpecifications(currentQuotationId);
                $('#specifications-tab').addClass('loaded');
                
                // Hide add specification buttons
                $('.btn-primary:contains("Tambah Unit")').hide();
                $('.btn-success:contains("Tambah Attachment")').hide();
                return;
            }
            
            // For QUOTATION and SENT stages, load normally
            loadQuotationSpecifications(currentQuotationId);
            $('#specifications-tab').addClass('loaded');
        } else {
            console.error('Failed to load quotation details:', response);
            const container = document.getElementById('spesifikasiListContract');
            container.innerHTML = `<div class="alert alert-danger">Failed to load quotation details: ${response.message || 'Unknown error'}</div>`;
        }
    }).fail(function(xhr) {
        console.error('AJAX error loading quotation for specifications:', xhr);
        const container = document.getElementById('spesifikasiListContract');
        let errorMsg = 'Network error';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
        }
        container.innerHTML = `<div class="alert alert-danger">Failed to load quotation details: ${errorMsg}</div>`;
    });
});

// Load quotation specifications
function loadQuotationSpecifications(quotationId) {
    const container = document.getElementById('spesifikasiListContract');
    if (!container) {
        console.error('spesifikasiListContract container not found!');
        return;
    }
    
    container.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading specifications...</div>';
    
    fetch(`<?= base_url('marketing/quotations/get-specifications/') ?>${quotationId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(response => {
            if (!response.success) {
                throw new Error(response.message || 'Failed to load specifications');
            }
            
            const specifications = response.data || [];
            const summary = response.summary || {};
            
            // Update tab counter
            $('#specCountQuotation').text(specifications.length);
            
            if (specifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">No Specifications Yet</h5>
                        <p class="text-muted mb-4">
                            This quotation doesn't have any specifications yet.<br>
                            Add specifications to define the required units, quantities, and pricing.
                        </p>
                        <button class="btn btn-primary btn-lg" onclick="openAddSpecificationModal()">
                            <i class="fas fa-plus me-2"></i>Add First Specification
                        </button>
                    </div>
                `;
                return;
            }
            
            displayQuotationSpecifications(specifications);
        })
        .catch(error => {
            console.error('Error loading specifications:', error);
            container.innerHTML = `<div class="alert alert-danger">Error loading specifications: ${error.message}</div>`;
        });
}

// Display quotation specifications
function displayQuotationSpecifications(specifications) {
    const container = document.getElementById('spesifikasiListContract');
    
    let html = '';
    specifications.forEach((spec, index) => {
        // Determine specification type
        const specType = spec.specification_type || 'UNIT';
        const isAttachment = specType === 'ATTACHMENT';
        const cardClass = isAttachment ? 'border-success' : 'border-primary';
        const badgeClass = isAttachment ? 'bg-success' : 'bg-primary';
        
        // Build specification details
        const details = [];
        
        // Department and Unit Type
        if (spec.nama_departemen) {
            details.push(`<div class="col-md-3"><small class="text-muted">Department</small><div class="fw-bold">${spec.nama_departemen}</div></div>`);
        }
        
        if (spec.jenis_tipe_unit) {
            details.push(`<div class="col-md-3"><small class="text-muted">Unit Type</small><div class="fw-bold">${spec.jenis_tipe_unit}</div></div>`);
        } else if (spec.nama_tipe_unit) {
            details.push(`<div class="col-md-3"><small class="text-muted">Unit Type</small><div class="fw-bold">${spec.nama_tipe_unit}</div></div>`);
        }
        
        // Capacity
        if (spec.nama_kapasitas) {
            details.push(`<div class="col-md-3"><small class="text-muted">Capacity</small><div class="fw-bold">${spec.nama_kapasitas}</div></div>`);
        }
        
        // Quantity
        details.push(`<div class="col-md-3"><small class="text-muted">Quantity</small><div class="fw-bold text-primary">${spec.quantity || 0} unit(s)</div></div>`);
        
        // Pricing
        const monthlyPrice = spec.monthly_price || spec.unit_price || spec.harga_per_unit || 0;
        const dailyPrice = spec.daily_price || spec.harga_per_unit_harian || 0;
        const totalPrice = spec.total_price || (monthlyPrice * (spec.quantity || 0));
        
        if (monthlyPrice > 0) {
            details.push(`<div class="col-md-3"><small class="text-muted">Monthly Price/Unit</small><div class="fw-bold text-success">Rp ${formatNumber(monthlyPrice)}</div></div>`);
        }
        
        if (dailyPrice > 0) {
            details.push(`<div class="col-md-3"><small class="text-muted">Daily Price/Unit</small><div class="fw-bold text-info">Rp ${formatNumber(dailyPrice)}</div></div>`);
        }
        
        details.push(`<div class="col-md-3"><small class="text-muted">Total Price</small><div class="fw-bold text-primary">Rp ${formatNumber(totalPrice)}</div></div>`);
        
        // Brand and Model
        if (spec.merk_unit) {
            details.push(`<div class="col-md-3"><small class="text-muted">Brand</small><div>${spec.merk_unit}</div></div>`);
        }
        
        if (spec.model_unit) {
            details.push(`<div class="col-md-3"><small class="text-muted">Model</small><div>${spec.model_unit}</div></div>`);
        }
        
        // Electric Specific - Battery and Charger
        if (spec.jenis_baterai) {
            details.push(`<div class="col-md-3"><small class="text-muted">Battery Type</small><div><i class="fas fa-battery-full text-warning me-1"></i>${spec.jenis_baterai}</div></div>`);
        }
        
        // Charger - check for various field combinations
        let chargerInfo = '';
        if (spec.merk_charger && spec.tipe_charger) {
            chargerInfo = `${spec.merk_charger} - ${spec.tipe_charger}`;
        } else if (spec.charger_id && spec.charger_id > 0) {
            chargerInfo = `Charger ID: ${spec.charger_id}`;
        }
        
        if (chargerInfo) {
            details.push(`<div class="col-md-4"><small class="text-muted">Charger</small><div><i class="fas fa-charging-station text-success me-1"></i>${chargerInfo}</div></div>`);
        }
        
        // Technical Specifications
        const techSpecs = [];
        if (spec.valve_name) {
            techSpecs.push(`<span class="badge bg-light text-dark me-1"><i class="fas fa-cog me-1"></i>Valve: ${spec.valve_name}</span>`);
        }
        if (spec.mast_name) {
            techSpecs.push(`<span class="badge bg-light text-dark me-1"><i class="fas fa-arrows-alt-v me-1"></i>Mast: ${spec.mast_name}</span>`);
        }
        if (spec.tire_name) {
            techSpecs.push(`<span class="badge bg-light text-dark me-1"><i class="fas fa-circle me-1"></i>Tire: ${spec.tire_name}</span>`);
        }
        if (spec.wheel_name) {
            techSpecs.push(`<span class="badge bg-light text-dark me-1"><i class="fas fa-circle-notch me-1"></i>Wheel: ${spec.wheel_name}</span>`);
        }
        
        // Attachment Information
        if (spec.attachment_tipe) {
            details.push(`<div class="col-md-4"><small class="text-muted">Attachment Type</small><div><i class="fas fa-tools text-success me-1"></i>${spec.attachment_tipe}</div></div>`);
        }
        if (spec.attachment_merk) {
            details.push(`<div class="col-md-4"><small class="text-muted">Attachment Brand</small><div>${spec.attachment_merk}</div></div>`);
        }
        
        // Accessories
        let accessoriesBadges = '';
        if (spec.unit_accessories && spec.unit_accessories.trim() !== '') {
            const accessories = spec.unit_accessories.split(',').map(a => a.trim());
            accessoriesBadges = accessories.map(acc => 
                `<span class="badge bg-info me-1"><i class="fas fa-plus-circle me-1"></i>${acc}</span>`
            ).join('');
        }
        
        html += `
            <div class="card mb-3 ${cardClass}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge ${badgeClass} me-2">SPEC-${index + 1}</span>
                        <span class="badge bg-light text-dark me-2">${specType}</span>
                        <strong>${spec.specification_name || 'Specification ' + (index + 1)}</strong>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editSpecification(${spec.id_specification})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSpecification(${spec.id_specification})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        ${details.join('')}
                    </div>
                    
                    ${techSpecs.length > 0 ? `
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Technical Specifications:</small>
                        ${techSpecs.join('')}
                    </div>
                    ` : ''}
                    
                    ${accessoriesBadges ? `
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Unit Accessories:</small>
                        ${accessoriesBadges}
                    </div>
                    ` : ''}
                    
                    ${spec.specification_description && spec.specification_description.trim() !== '' ? `
                    <div class="mt-3">
                        <small class="text-muted d-block mb-1">Description:</small>
                        <div class="text-muted">${spec.specification_description}</div>
                    </div>
                    ` : ''}
                </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Open Add Specification Modal
function openAddSpecificationModal() {
    if (!currentQuotationId) {
        Swal.fire('Warning', 'Please select a quotation first', 'warning');
        return;
    }
    
    // Check quotation workflow stage before allowing specifications
    $.get('<?= base_url('marketing/quotations/get-quotation/') ?>' + currentQuotationId, function(response) {
        // Handle error responses
        if (response.status === 'error') {
            Swal.fire('Error', 'Failed to load quotation: ' + response.message, 'error');
            return;
        }
        
        // Use the data property for detailed information
        const quotation = response.data || response;
        
        if (quotation) {
            // Check workflow stage
            if (quotation.workflow_stage === 'PROSPECT') {
                Swal.fire({
                    title: 'Convert to Quotation First',
                    text: 'This prospect must be converted to quotation before adding specifications.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Convert Now',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        convertToQuotation(currentQuotationId);
                    }
                });
                return;
            }
            
            if (!['QUOTATION', 'SENT'].includes(quotation.workflow_stage)) {
                Swal.fire('Warning', 'Specifications can only be added to quotations in QUOTATION or SENT stage.', 'warning');
                return;
            }
            
            // If stage is valid, proceed to open specification modal
            proceedWithSpecificationModal();
        } else {
            Swal.fire('Error', 'Failed to load quotation details', 'error');
        }
    }).fail(function() {
        Swal.fire('Error', 'Failed to load quotation details', 'error');
    });
}

function proceedWithSpecificationModal() {
    // Reset form to Add mode
    $('#addSpecificationForm')[0].reset();
    $('#specQuotationId').val(currentQuotationId);
    $('#specId').val(''); // Clear spec ID for add mode
    $('#specType').val('UNIT'); // Set specification type to UNIT
    
    // Reset modal to Add mode
    $('#specModalHeader').removeClass('bg-primary').addClass('bg-success');
    $('#specModalTitle').html('<i class="fas fa-cogs me-2"></i>Add Unit Specification');
    $('#submitBtnText').text('Save Specification');
    $('#submitSpecificationBtn').removeClass('btn-primary').addClass('btn-success');
    
    // Load dropdown data
    loadDepartemenForSpecification();
    loadTipeUnitForSpecification(); // This will load data but not populate options until dept is selected
    loadKapasitasForSpecification();
    loadUnitBrandsForSpecification();
    loadAttachmentTypesForSpecification();
    loadValvesForSpecification();
    loadMastsForSpecification();
    loadTiresForSpecification();
    loadWheelsForSpecification();
    
    // Initialize battery and charger as disabled
    $('#specJenisBaterai, #specCharger').prop('disabled', true);
    $('#specJenisBaterai').html('<option value="">-- Select Battery --</option>');
    $('#specCharger').html('<option value="">-- Select Charger --</option>');
    
    $('#addSpecificationModal').modal('show');
}

// Open add attachment modal
function openAddAttachmentModal() {
    if (!currentQuotationId) {
        Swal.fire('Warning', 'Please select a quotation first', 'warning');
        return;
    }
    
    // Reset form
    $('#addAttachmentForm')[0].reset();
    $('#attachmentSpecType').val('ATTACHMENT'); // Set specification type to ATTACHMENT
    $('#attachmentQuotationId').val(currentQuotationId);
    
    // Load attachment types
    loadAttachmentTypesForAttachment();
    
    // Show modal
    $('#addAttachmentModal').modal('show');
    
    // Focus on quantity after modal is shown
    setTimeout(() => {
        $('#addAttachmentForm [name="quantity"]').focus();
    }, 300);
}

// Department change handler - handle electric/non-electric filtering
$(document).on('change', '#specDepartemen', function() {
    const selectedDept = $(this).val();
    const selectedDeptText = $(this).find('option:selected').text().toLowerCase();
    
    // Check if selected department is electric
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    // Handle battery and charger field visibility/state
    if (isElectric) {
        // Enable and load data for electric department
        $('#specJenisBaterai, #specCharger').prop('disabled', false);
        loadBatteriesForSpecification();
        loadChargersForSpecification();
    } else {
        // Disable and clear for non-electric departments
        $('#specJenisBaterai, #specCharger').prop('disabled', true);
        $('#specJenisBaterai').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        $('#specCharger').html('<option value="">Hanya tersedia untuk unit Electric</option>');
    }
    
    // Update Unit Type options based on selected department
    updateTipeUnitOptions();
});

// Unit Type change handler - handle cascading to other components
$(document).on('change', '#specTipeUnit', function() {
    const selectedTipeUnit = $(this).val();
    const selectedText = $(this).find('option:selected').text();
    
    if (selectedTipeUnit) {
        // Filter kapasitas based on unit type if needed
        // For now we load all kapasitas, but this can be enhanced
        loadKapasitasForSpecification();
        
        // Load other components that may depend on unit type
        loadUnitBrandsForSpecification();
        loadAttachmentTypesForSpecification();
    } else {
        // Clear dependent dropdowns
        $('#specKapasitas').html('<option value="">-- Select Capacity --</option>');
    }
});

// Functions to load dropdown data - consistent with kontrak spesifikasi pattern
function loadDepartemenForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=departemen', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Department --</option>';
            response.data.forEach(dept => {
                options += `<option value="${dept.id}">${dept.name}</option>`;
            });
            $('#specDepartemen').html(options);
        } else {
            console.error('Departemen API error:', response.message);
            $('#specDepartemen').html('<option value="">Error: ' + (response.message || 'Unknown error') + '</option>');
        }
    }).fail(function(xhr) {
        console.error('Failed to load departments:', {
            status: xhr.status,
            statusText: xhr.statusText,
            responseText: xhr.responseText
        });
        $('#specDepartemen').html('<option value="">Error loading departments</option>');
    });
}

function loadTipeUnitForSpecification() {
    return $.ajax({
        url: '<?= base_url('marketing/customer-management/getTipeUnit') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Store all unit type data globally for filtering
                window.allTipeUnitData = response.data;
                
                // Initially show placeholder only
                $('#specTipeUnit').html('<option value="">-- Pilih Tipe Unit --</option>');
            } else {
                console.error('API returned error:', response.message);
                $('#specTipeUnit').html('<option value="">Error: ' + (response.message || 'Unknown error') + '</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading tipe unit:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            $('#specTipeUnit').html('<option value="">Error loading unit types</option>');
        }
    });
}

// Function to update Unit Type options based on selected department
function updateTipeUnitOptions() {
    const selectedDept = $('#specDepartemen').val();
    const selectedDeptText = $('#specDepartemen option:selected').text();
    const select = $('#specTipeUnit');
    
    select.empty().append('<option value="">-- Pilih Tipe Unit --</option>');
    
    if (!selectedDept || !window.allTipeUnitData) {
        if (!window.allTipeUnitData) {
            // Try to load tipe unit data if it's not loaded
            loadTipeUnitForSpecification();
        }
        return;
    }
    
    // Filter and show only units for selected department
    const filteredUnits = window.allTipeUnitData.filter(unit => {
        return unit.id_departemen == selectedDept;
    });
    
    if (filteredUnits.length === 0) {
        select.append('<option value="">No unit types available for this department</option>');
        return;
    }
    
    // Group by jenis to avoid duplicates
    const uniqueJenis = [...new Set(filteredUnits.map(unit => unit.jenis))];
    
    uniqueJenis.sort().forEach(jenis => {
        // Find the first unit with this jenis to get the id
        const unitWithJenis = filteredUnits.find(unit => unit.jenis === jenis);
        if (unitWithJenis) {
            select.append(`<option value="${unitWithJenis.id_tipe_unit}" data-dept="${selectedDept}">${jenis}</option>`);
        }
    });
    
    // Also clear dependent dropdowns when unit type changes
    $('#specKapasitas').html('<option value="">-- Select Capacity --</option>');
}

function loadKapasitasForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=kapasitas', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Capacity --</option>';
            response.data.forEach(cap => {
                options += `<option value="${cap.id}">${cap.name}</option>`;
            });
            $('#specKapasitas').html(options);
        } else {
            console.error('Kapasitas API error:', response.message);
            $('#specKapasitas').html('<option value="">Error: ' + (response.message || 'Unknown error') + '</option>');
        }
    }).fail(function(xhr) {
        console.error('Failed to load capacities:', {
            status: xhr.status,
            statusText: xhr.statusText,
            responseText: xhr.responseText
        });
        $('#specKapasitas').html('<option value="">Error loading capacities</option>');
    });
}

function loadChargersForSpecification() {
    const selectedDeptId = $('#specDepartemen').val();
    const selectedDeptText = $('#specDepartemen option:selected').text().toLowerCase();
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    if (!isElectric || !selectedDeptId) {
        $('#specCharger').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        return Promise.resolve();
    }
    
    return $.get(`<?= base_url('marketing/spk/spec-options') ?>?type=charger&departemen_id=${selectedDeptId}`, function(response) {
        if (response.success) {
            let options = '<option value="">-- Pilih Charger --</option>';
            response.data.forEach(charger => {
                options += `<option value="${charger.id}">${charger.name}</option>`;
            });
            $('#specCharger').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load chargers:', xhr.responseText);
        $('#specCharger').html('<option value="">Error loading chargers</option>');
    });
}

function loadUnitBrandsForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=merk_unit', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Brand --</option>';
            response.data.forEach(brand => {
                // Backend returns {id: model_unit_id, name: "Brand - Model"}
                options += `<option value="${brand.id}">${brand.name}</option>`;
            });
            $('#specMerkUnit').html(options);
        }
    }).fail(function(xhr) {
        console.error('❌ Failed to load unit brands:', xhr.responseText);
        $('#specMerkUnit').html('<option value="">Error loading brands</option>');
    });
}

function loadBatteriesForSpecification() {
    const selectedDeptId = $('#specDepartemen').val();
    const selectedDeptText = $('#specDepartemen option:selected').text().toLowerCase();
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    if (!isElectric || !selectedDeptId) {
        $('#specJenisBaterai').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        return Promise.resolve();
    }
    
    return $.get(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_baterai&departemen_id=${selectedDeptId}`, function(response) {
        if (response.success) {
            let options = '<option value="">-- Pilih Baterai --</option>';
            response.data.forEach(battery => {
                // Backend returns {id: battery_id, name: "Brand - Type (Jenis)"}
                options += `<option value="${battery.id}">${battery.name}</option>`;
            });
            $('#specJenisBaterai').html(options);
        }
    }).fail(function(xhr) {
        console.error('❌ Failed to load batteries:', xhr.responseText);
        $('#specJenisBaterai').html('<option value="">Error loading batteries</option>');
    });
}

function loadAttachmentTypesForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=attachment_tipe', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Attachment Type --</option>';
            response.data.forEach(att => {
                // Backend returns {id: attachment_id, name: "Tipe - Brand Model"}
                options += `<option value="${att.id}">${att.name}</option>`;
            });
            $('#specAttachmentTipe').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load attachment types:', xhr.responseText);
        $('#specAttachmentTipe').html('<option value="">Error loading attachments</option>');
    });
}

function loadAttachmentTypesForAttachment() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=attachment_tipe', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Attachment Type --</option>';
            response.data.forEach(att => {
                // Backend returns DISTINCT values with {id: name, name: name}
                options += `<option value="${att.name}">${att.name}</option>`;
            });
            $('#attachmentTipe').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load attachment types:', xhr.responseText);
        $('#attachmentTipe').html('<option value="">Error loading attachments</option>');
    });
}

function loadValvesForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=valve', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Valve --</option>';
            response.data.forEach(valve => {
                options += `<option value="${valve.id}">${valve.name}</option>`;
            });
            $('#specValve').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load valves:', xhr.responseText);
        $('#specValve').html('<option value="">Error loading valves</option>');
    });
}

function loadMastsForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=mast', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Mast --</option>';
            response.data.forEach(mast => {
                options += `<option value="${mast.id}">${mast.name}</option>`;
            });
            $('#specMast').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load masts:', xhr.responseText);
        $('#specMast').html('<option value="">Error loading masts</option>');
    });
}

function loadTiresForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=ban', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Tire --</option>';
            response.data.forEach(tire => {
                options += `<option value="${tire.id}">${tire.name}</option>`;
            });
            $('#specBan').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load tires:', xhr.responseText);
        $('#specBan').html('<option value="">Error loading tires</option>');
    });
}

function loadWheelsForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=roda', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Wheel --</option>';
            response.data.forEach(wheel => {
                options += `<option value="${wheel.id}">${wheel.name}</option>`;
            });
            $('#specRoda').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load wheels:', xhr.responseText);
        $('#specRoda').html('<option value="">Error loading wheels</option>');
    });
}

// Handle specification form submission (unified for Add and Edit)
$('#addSpecificationForm').on('submit', function(e) {
    e.preventDefault();
    
    console.log('Specification form submitted');
    
    // Check if this is Add or Edit mode
    const specId = $('#specId').val();
    const isEditMode = specId && specId !== '';
    
    console.log('Form mode:', isEditMode ? 'EDIT' : 'ADD', 'Spec ID:', specId);
    
    // Enhanced validation
    const quantity = parseInt($('#addSpecificationForm [name="quantity"]').val());
    const monthlyPrice = parseFloat($('#monthlyPrice').val()) || 0;
    const dailyPrice = parseFloat($('#dailyPrice').val()) || 0;
    const departemen = $('#specDepartemen').val();
    const tipeUnit = $('#specTipeUnit').val();
    
    // Client-side validation
    if (!quantity || quantity < 1) {
        Swal.fire('Validation Error', 'Please enter a valid quantity (minimum 1)', 'warning');
        return;
    }
    
    // Validate: at least one price (monthly or daily) must be filled
    if (monthlyPrice === 0 && dailyPrice === 0) {
        Swal.fire('Validation Error', 'Please fill in at least one price field (Monthly Rental Price or Daily Rental Price)', 'warning');
        $('#monthlyPrice').focus();
        return;
    }
    
    if (!departemen) {
        Swal.fire('Validation Error', 'Please select a department', 'warning');
        $('#specDepartemen').focus();
        return;
    }
    
    if (!tipeUnit) {
        Swal.fire('Validation Error', 'Please select a unit type', 'warning');
        $('#specTipeUnit').focus();
        return;
    }
    
    const formData = new FormData(this);
    const submitBtn = $('#submitSpecificationBtn');
    
    // Determine endpoint based on mode
    const endpoint = isEditMode 
        ? '<?= base_url('marketing/quotations/update-specification') ?>' 
        : '<?= base_url('marketing/quotations/add-specification') ?>';
    
    const actionText = isEditMode ? 'Updating...' : 'Saving...';
    const successMsg = isEditMode ? 'Specification updated successfully' : 'Specification added successfully';
    
    submitBtn.prop('disabled', true).html(`<i class="fas fa-spinner fa-spin me-1"></i>${actionText}`);
    
    $.ajax({
        url: endpoint,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#addSpecificationModal').modal('hide');
                Swal.fire('Success', response.message || successMsg, 'success');
                
                // Reload specifications and refresh tab
                if (currentQuotationId) {
                    $('#specifications-tab').removeClass('loaded');
                    loadQuotationSpecifications(currentQuotationId);
                }
                
                // Reset form for next entry
                $('#addSpecificationForm')[0].reset();
            } else {
                Swal.fire('Error', response.message || 'Failed to save specification', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving specification:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            let errorMessage = 'Failed to save specification';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    if (errorData.message) errorMessage = errorData.message;
                } catch (e) {
                    console.error('Could not parse error response');
                }
            }
            
            Swal.fire('Error', errorMessage, 'error');
        },
        complete: function() {
            const btnText = $('#specId').val() ? 'Update Specification' : 'Save Specification';
            submitBtn.prop('disabled', false).html(`<i class="fas fa-save me-1"></i><span id="submitBtnText">${btnText}</span>`);
        }
    });
});

// Handle attachment form submission
$('#addAttachmentForm').on('submit', function(e) {
    e.preventDefault();
    
    console.log('Attachment form submitted');
    
    // Enhanced validation
    const quantity = parseInt($('#addAttachmentForm [name="quantity"]').val());
    const monthlyPrice = parseFloat($('#attachmentMonthlyPrice').val()) || 0;
    const dailyPrice = parseFloat($('#attachmentDailyPrice').val()) || 0;
    const attachmentType = $('#attachmentTipe').val();
    
    // Client-side validation
    if (!quantity || quantity < 1) {
        Swal.fire('Validation Error', 'Please enter a valid quantity (minimum 1)', 'warning');
        return;
    }
    
    // Validate: at least one price (monthly or daily) must be filled
    if (monthlyPrice === 0 && dailyPrice === 0) {
        Swal.fire('Validation Error', 'Please fill in at least one price field (Monthly Rental Price or Daily Rental Price)', 'warning');
        $('#attachmentMonthlyPrice').focus();
        return;
    }
    
    if (!attachmentType) {
        Swal.fire('Validation Error', 'Please select an attachment type', 'warning');
        $('#attachmentTipe').focus();
        return;
    }
    
    const formData = new FormData(this);
    const submitBtn = $('#submitAttachmentBtn');
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/add-specification') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#addAttachmentModal').modal('hide');
                Swal.fire('Success', response.message || 'Attachment added successfully', 'success');
                
                // Reload specifications and refresh tab
                if (currentQuotationId) {
                    $('#specifications-tab').removeClass('loaded');
                    loadQuotationSpecifications(currentQuotationId);
                }
                
                // Reset form for next entry
                $('#addAttachmentForm')[0].reset();
            } else {
                Swal.fire('Error', response.message || 'Failed to add attachment', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding attachment:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            let errorMessage = 'Failed to add attachment';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    if (errorData.message) errorMessage = errorData.message;
                } catch (e) {
                    console.error('Could not parse error response');
                }
            }
            
            Swal.fire('Error', errorMessage, 'error');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Attachment');
        }
    });
});

// Edit specification - reuse Add Specification modal
function editSpecification(specId) {
    if (!currentQuotationId) {
        console.error('No currentQuotationId set!');
        Swal.fire('Error', 'Quotation ID is missing', 'error');
        return;
    }
    
    // First, load specification data
    const apiUrl = `<?= base_url('marketing/quotations/getSpecifications/') ?>${currentQuotationId}`;
    
    $.ajax({
        url: apiUrl,
        method: 'GET',
        success: function(response) {
            if (!response || !response.success) {
                console.error('API returned error or no success flag');
                Swal.fire('Error', response?.message || 'Failed to load specification data', 'error');
                return;
            }
            
            if (!response.data || !Array.isArray(response.data)) {
                console.error('Invalid response data structure');
                Swal.fire('Error', 'Invalid data structure received', 'error');
                return;
            }
            
            // Find the specification with matching ID
            const spec = response.data.find(s => s.id_specification == specId);
            
            if (!spec) {
                console.error('Specification not found in data');
                Swal.fire('Error', 'Specification not found', 'error');
                return;
            }

            // Change modal title and button for edit mode
            $('#specModalHeader').removeClass('bg-success').addClass('bg-primary');
            $('#specModalTitle').html('<i class="fas fa-edit me-2"></i>Edit Unit Specification');
            $('#submitBtnText').text('Update Specification');
            $('#submitSpecificationBtn').removeClass('btn-success').addClass('btn-primary');
            
            // Set spec ID for update
            $('#specId').val(spec.id_specification);
            $('#specQuotationId').val(currentQuotationId);
            
            // Populate basic fields using name attribute
            $('[name="quantity"]').val(spec.quantity || 1);
            $('[name="specification_name"]').val(spec.specification_name || '');
            $('[name="unit_price"]').val(spec.harga_per_unit || spec.unit_price || 0);
            $('[name="harga_per_unit_harian"]').val(spec.harga_per_unit_harian || 0);
            
            console.log('📋 Starting to load all dropdown data...');
            
            // STEP 1: Load independent dropdowns first (parallel)
            Promise.all([
                loadDepartemenForSpecification(),
                loadKapasitasForSpecification(),
                loadUnitBrandsForSpecification(),
                loadAttachmentTypesForSpecification(),
                loadValvesForSpecification(),
                loadMastsForSpecification(),
                loadTiresForSpecification(),
                loadWheelsForSpecification(),
                loadTipeUnitForSpecification()
            ]).then(() => {
                console.log('✅ Independent dropdowns loaded');
                
                // Set independent dropdown values IMMEDIATELY after load
                console.log('📌 Setting Capacity:', spec.kapasitas_id);
                $('#specKapasitas').val(spec.kapasitas_id || '');
                
                console.log('📌 Setting Unit Brand:', spec.merk_unit);
                $('#specMerkUnit').val(spec.brand_id || '');
                
                console.log('📌 Setting Attachment Type:', spec.attachment_tipe);
                $('#specAttachmentTipe').val(spec.attachment_id || '');
                
                console.log('📌 Setting Valve:', spec.valve_id);
                $('#specValve').val(spec.valve_id || '');
                
                console.log('📌 Setting Mast:', spec.mast_id);
                $('#specMast').val(spec.mast_id || '');
                
                console.log('📌 Setting Tire:', spec.ban_id);
                $('#specBan').val(spec.ban_id || '');
                
                console.log('📌 Setting Wheel:', spec.roda_id);
                $('#specRoda').val(spec.roda_id || '');
                
                // STEP 2: Set department and handle cascading
                $('#specDepartemen').val(spec.departemen_id || '');
                console.log('✅ Department set to:', spec.departemen_id);
                
                // Check if electric department
                const deptText = $('#specDepartemen option:selected').text().toLowerCase();
                const isElectric = deptText.includes('electric') || deptText.includes('listrik');
                console.log('Is Electric Department:', isElectric);
                
                // STEP 3: Update Unit Type dropdown based on department
                if (spec.departemen_id && window.allTipeUnitData) {
                    console.log('📋 Filtering tipe unit for department:', spec.departemen_id);
                    updateEditTipeUnitOptions(spec.departemen_id).then(() => {
                        $('#specTipeUnit').val(spec.tipe_unit_id || '');
                        console.log('✅ Unit Type set to:', spec.tipe_unit_id);
                    });
                }
                
                // STEP 4: Handle electric-specific fields
                if (isElectric) {
                    $('#specJenisBaterai').prop('disabled', false).closest('.col-md-4').find('small').show();
                    $('#specCharger').prop('disabled', false).closest('.col-md-4').find('small').show();
                    
                    // DON'T trigger change - load battery/charger directly
                    console.log('📋 Loading Battery and Charger for Electric unit...');
                    
                    // Load battery and charger with proper timing
                    Promise.all([
                        loadBatteriesForSpecification(),
                        loadChargersForSpecification()
                    ]).then(() => {
                        console.log('📌 Setting Battery Type (battery_id):', spec.battery_id);
                        $('#specJenisBaterai').val(spec.battery_id || '');
                        
                        console.log('📌 Setting Charger:', spec.charger_id);
                        $('#specCharger').val(spec.charger_id || '');
                        
                        console.log('✅ Battery and Charger loaded and set');
                    });
                } else {
                    $('#specJenisBaterai').prop('disabled', true).val('').closest('.col-md-4').find('small').hide();
                    $('#specCharger').prop('disabled', true).val('').closest('.col-md-4').find('small').hide();
                }
                
                // Handle accessories
                $('[name="aksesoris[]"]').prop('checked', false);
                
                const accessoriesData = spec.aksesoris || spec.unit_accessories || '';
                if (accessoriesData && accessoriesData !== '') {
                    const accessories = typeof accessoriesData === 'string' 
                        ? accessoriesData.split(',').map(a => a.trim())
                        : (Array.isArray(accessoriesData) ? accessoriesData : []);
                    
                    accessories.forEach(accessory => {
                        $(`[name="aksesoris[]"][value="${accessory}"]`).prop('checked', true);
                    });
                    
                    console.log('✅ Accessories loaded:', accessories);
                }
                
                // Show modal after ALL data is loaded and set
                console.log('=== ALL DATA LOADED - SHOWING MODAL ===');
                setTimeout(() => {
                    $('#addSpecificationModal').modal('show');
                    console.log('✅ Modal displayed');
                }, 200);
                
            }).catch(error => {
                console.error('❌ Error loading dropdown data:', error);
                Swal.fire('Error', 'Failed to load dropdown data: ' + error.message, 'error');
            });
        },
        error: function(xhr, status, error) {
            console.error('=== AJAX ERROR ===');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('XHR Status:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            console.error('Full XHR:', xhr);
            
            let errorMsg = 'Failed to load specification data';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMsg = 'API endpoint not found (404). Check route configuration.';
            } else if (xhr.status === 500) {
                errorMsg = 'Server error (500). Check server logs.';
            }
            
            Swal.fire('Error', errorMsg, 'error');
        }
    });
}

// Load functions for edit modal
function loadDepartemenForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=departemen', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Department --</option>';
            response.data.forEach(dept => {
                options += `<option value="${dept.id}">${dept.name}</option>`;
            });
            $('#edit_departemen_id').html(options);
        }
    });
}

function loadTipeUnitForEdit() {
    return $.ajax({
        url: '<?= base_url('marketing/customer-management/getTipeUnit') ?>',
        method: 'GET',
        success: function(response) {
            console.log('Edit Tipe Unit API Response:', response);
            if (response.success) {
                // Store all unit type data globally for filtering (same as Add Specification)
                window.editAllTipeUnitData = response.data;
                console.log('Loaded', response.data.length, 'tipe unit records for edit');
                // Initially show placeholder only
                $('#edit_tipe_unit_id').html('<option value="">-- Select Unit Type --</option>');
            } else {
                console.error('API returned error:', response.message);
                $('#edit_tipe_unit_id').html('<option value="">Error loading unit types</option>');
            }
        },
        error: function(xhr) {
            console.error('Failed to load unit types for edit:', xhr.responseText);
            $('#edit_tipe_unit_id').html('<option value="">Error loading unit types</option>');
        }
    });
}

function updateEditTipeUnitOptions(departemenId) {
    return new Promise((resolve) => {
        const select = $('#specTipeUnit');
        
        select.empty().append('<option value="">-- Select Unit Type --</option>');
        
        if (!departemenId || !window.allTipeUnitData) {
            console.log('No department selected or no tipe unit data available');
            resolve();
            return;
        }
        
        console.log('Edit - Filtering units for department:', departemenId);
        
        // Filter by department (same logic as Add Specification)
        const filteredUnits = window.allTipeUnitData.filter(unit => {
            return unit.id_departemen == departemenId;
        });
        
        console.log('Edit - Filtered Units:', filteredUnits);
        
        if (filteredUnits.length === 0) {
            select.append('<option value="">No unit types available for this department</option>');
            resolve();
            return;
        }
        
        // Group by jenis to avoid duplicates (show JENIS not TIPE)
        const uniqueJenis = [...new Set(filteredUnits.map(unit => unit.jenis))];
        console.log('Edit - Unique jenis found:', uniqueJenis);
        
        uniqueJenis.sort().forEach(jenis => {
            // Find the first unit with this jenis to get the id
            const unitWithJenis = filteredUnits.find(unit => unit.jenis === jenis);
            if (unitWithJenis) {
                console.log('Edit - Adding option:', unitWithJenis.id_tipe_unit, jenis);
                select.append(`<option value="${unitWithJenis.id_tipe_unit}">${jenis}</option>`);
            }
        });
        
        resolve();
    });
}

function loadKapasitasForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=kapasitas', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Capacity --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_kapasitas_id').html(options);
        }
    });
}

function loadUnitBrandsForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=merk_unit', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Brand --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.name}">${item.name}</option>`;
            });
            $('#edit_brand').html(options);
        }
    });
}

function loadBatteriesForEdit(departemenId) {
    return $.get(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_baterai&departemen_id=${departemenId}`, function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Battery --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.name}">${item.name}</option>`;
            });
            $('#edit_jenis_baterai').html(options).prop('disabled', false);
        }
    });
}

function loadChargersForEdit(departemenId) {
    return $.get(`<?= base_url('marketing/spk/spec-options') ?>?type=charger&departemen_id=${departemenId}`, function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Charger --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_charger_id').html(options).prop('disabled', false);
        }
    }).fail(function(xhr) {
        console.error('Failed to load chargers for edit:', xhr.responseText);
        $('#edit_charger_id').html('<option value="">Error loading chargers</option>');
    });
}

function loadAttachmentTypesForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=attachment_tipe', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Attachment Type --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.name}">${item.name}</option>`;
            });
            $('#edit_attachment_tipe').html(options);
        }
    });
}

function loadValvesForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=valve', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Valve --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_valve_id').html(options);
        }
    });
}

function loadMastsForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=mast', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Mast --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_mast_id').html(options);
        }
    });
}

function loadTiresForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=ban', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Tire --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_ban_id').html(options);
        }
    });
}

function loadWheelsForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=roda', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Wheel --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_roda_id').html(options);
        }
    });
}

// Department change handler for edit modal
$(document).on('change', '#edit_departemen_id', function() {
    const departemenId = $(this).val();
    const deptText = $(this).find('option:selected').text().toLowerCase();
    const isElectric = deptText.includes('electric') || deptText.includes('listrik');
    
    console.log('Edit department changed:', departemenId, deptText, 'Electric:', isElectric);
    
    // Update unit types based on department
    updateEditTipeUnitOptions(departemenId);
    
    // Handle battery and charger fields visibility
    if (isElectric && departemenId) {
        // Enable and load battery/charger options
        $('#edit_jenis_baterai').prop('disabled', false).removeClass('bg-light');
        $('#edit_charger_id').prop('disabled', false).removeClass('bg-light');
        
        loadBatteriesForEdit(departemenId);
        loadChargersForEdit(departemenId);
    } else {
        // Disable and clear battery/charger fields
        $('#edit_jenis_baterai')
            .html('<option value="">Hanya tersedia untuk unit Electric</option>')
            .prop('disabled', true)
            .addClass('bg-light')
            .val('');
        $('#edit_charger_id')
            .html('<option value="">Hanya tersedia untuk unit Electric</option>')
            .prop('disabled', true)
            .addClass('bg-light')
            .val('');
    }
});

// Handle Edit Specification Form Submit
$(document).on('submit', '#editSpecificationForm', function(e) {
    e.preventDefault();
    
    const specId = $('#edit_spec_id').val();
    const formData = $(this).serialize();
    
    $.ajax({
        url: `<?= base_url('marketing/quotations/update-specification/') ?>${specId}`,
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#editSpecificationModal').modal('hide');
                Swal.fire('Success!', response.message, 'success');
                
                // Reload specifications
                if (currentQuotationId) {
                    loadQuotationSpecifications(currentQuotationId);
                }
            } else {
                Swal.fire('Error', response.message || 'Failed to update specification', 'error');
            }
        },
        error: function(xhr) {
            console.error('Error updating specification:', xhr);
            Swal.fire('Error', 'Failed to update specification: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
    });
});

// Auto-calculate total price in edit form
$(document).on('input', '#edit_quantity, #edit_unit_price', function() {
    const quantity = parseFloat($('#edit_quantity').val()) || 0;
    const unitPrice = parseFloat($('#edit_unit_price').val()) || 0;
    const totalPrice = quantity * unitPrice;
    
    $('#edit_total_price').val(totalPrice);
    $('#edit_total_price_display').val('Rp ' + totalPrice.toLocaleString('id-ID'));
});

// Delete specification
function deleteSpecification(specId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This specification will be deleted permanently',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?= base_url('marketing/quotations/delete-specification/') ?>${specId}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        
                        // Reload specifications
                        if (currentQuotationId) {
                            loadQuotationSpecifications(currentQuotationId);
                        }
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error deleting specification:', xhr);
                    Swal.fire('Error', 'Failed to delete specification: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                }
            });
        }
    });
}

// Helper function for number formatting
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num || 0);
}

// ===== PROSPECT CREATION FUNCTIONS =====

function openCreateProspectModal() {
    // Reset form
    $('#createProspectForm')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    
    // Reset customer search section
    $('#selectedCustomerId').val('');
    $('#customerSearchResults').html('');
    $('#linkExistingCustomer').prop('checked', false);
    $('#customerSearchSection').hide();
    $('#newCustomerSection').show();
    
    // Ensure proper required attributes
    $('#prospectCompanyName').attr('required', 'required');
    $('#prospectContactPerson').attr('required', 'required');
    
    // Set default valid until date (30 days from now)
    const defaultDate = new Date();
    defaultDate.setDate(defaultDate.getDate() + 30);
    $('#validUntil').val(defaultDate.toISOString().split('T')[0]);
    
    // Show modal and focus first field when ready
    $('#createProspectModal').modal('show');
    
    setTimeout(() => {
        $('#prospectCompanyName').focus();
    }, 300);
}

// Handle prospect creation form submission
$('#createProspectForm').on('submit', function(e) {
    e.preventDefault();
    
    // Custom validation for dynamic form
    let isValid = true;
    const isExistingCustomer = $('#linkExistingCustomer').is(':checked');
    
    // Clear previous validation states
    $('.is-invalid').removeClass('is-invalid');
    
    if (isExistingCustomer) {
        // Validate customer selection
        if (!$('#selectedCustomerId').val()) {
            $('#customerSearchInput').addClass('is-invalid');
            Swal.fire('Validation Error', 'Please select a customer or switch to new customer mode', 'error');
            return false;
        }
    } else {
        // Validate new customer fields
        if (!$('#prospectCompanyName').val().trim()) {
            $('#prospectCompanyName').addClass('is-invalid');
            isValid = false;
        }
        if (!$('#prospectContactPerson').val().trim()) {
            $('#prospectContactPerson').addClass('is-invalid');
            isValid = false;
        }
    }
    
    // Common validation for quotation fields
    if (!$('#quotationTitle').val().trim()) {
        $('#quotationTitle').addClass('is-invalid');
        isValid = false;
    }
    if (!$('#validUntil').val()) {
        $('#validUntil').addClass('is-invalid');
        isValid = false;
    }
    
    if (!isValid) {
        Swal.fire('Validation Error', 'Please fill in all required fields', 'error');
        return false;
    }
    
    const formData = new FormData(this);
    const submitBtn = $(this).find('button[type="submit"]');
    
    // Add workflow stage as prospect
    formData.append('workflow_stage', 'PROSPECT');
    formData.append('quotation_date', new Date().toISOString().split('T')[0]);
    formData.append('stage', 'DRAFT');
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...');
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/create-prospect') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#createProspectModal').modal('hide');
                Swal.fire({
                    title: 'Success!', 
                    text: 'Prospect created successfully. You can now add specifications.',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Add Specifications',
                    cancelButtonText: 'View Later'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Open the quotation to add specifications
                        viewQuotation(response.data.id_quotation);
                    }
                    
                    // Reload quotations table and statistics
                    quotationsTable.ajax.reload();
                    loadStatistics();
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error creating prospect:', error);
            console.error('XHR Response:', xhr);
            console.error('Response JSON:', xhr.responseJSON);
            
            let errorMessage = 'Failed to create prospect';
            
            // Check for validation errors
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Display detailed validation errors if available
                if (xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let errorList = '<ul class="text-start">';
                    Object.keys(errors).forEach(field => {
                        errorList += `<li><strong>${field}:</strong> ${errors[field]}</li>`;
                    });
                    errorList += '</ul>';
                    
                    Swal.fire({
                        title: 'Validation Error',
                        html: errorMessage + errorList,
                        icon: 'error',
                        width: '600px'
                    });
                    return;
                }
            }
            
            Swal.fire('Error', errorMessage, 'error');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Create Prospect');
        }
    });
});

// ===== WORKFLOW STAGE FUNCTIONS =====

function convertToQuotation(quotationId) {
    Swal.fire({
        title: 'Convert to Quotation?',
        text: 'Allow adding specifications and sending to customer.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Convert',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'swal2-small'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/convert-to-quotation') ?>/' + quotationId)
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Quotation Created!',
                            text: response.message,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Add Specifications Now',
                            cancelButtonText: 'Later'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open specifications modal
                                addSpecifications(quotationId);
                            }
                        });
                        
                        // Reload table and statistics
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // If modal is open and this is current quotation, refresh it
                        if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                            // Reset specifications tab loaded state
                            $('#specifications-tab').removeClass('loaded');
                            
                            // Reload quotation data
                            viewQuotation(quotationId);
                            
                            // If specifications tab is active, reload it
                            if ($('#specifications-tab').hasClass('active')) {
                                setTimeout(() => {
                                    $('#specifications-tab').click();
                                }, 500);
                            }
                        }
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Failed to convert prospect', 'error');
                });
        }
    });
}

function openSpecificationsModal(quotationId) {
    // First check if quotation is in correct stage for specifications
    $.get('<?= base_url('marketing/quotations/get/') ?>' + quotationId, function(response) {
        // Handle error responses
        if (response.status === 'error') {
            Swal.fire('Error', 'Failed to load quotation: ' + response.message, 'error');
            return;
        }
        
        // Use the data property for detailed information
        const quotation = response.data || response;
        
        if (quotation && quotation.id_quotation) {
            // Check workflow stage
            if (quotation.workflow_stage === 'PROSPECT') {
                Swal.fire({
                    title: 'Convert to Quotation First',
                    text: 'This prospect must be converted to quotation before adding specifications.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Convert Now',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        convertToQuotation(quotationId);
                    }
                });
                return;
            }
            
            if (!['QUOTATION', 'SENT'].includes(quotation.workflow_stage)) {
                Swal.fire('Warning', 'Specifications can only be added to quotations in QUOTATION or SENT stage.', 'warning');
                return;
            }
            
            // Open the quotation modal and switch to specifications tab
            viewQuotation(quotationId);
            // Switch to specifications tab after modal loads
            setTimeout(() => {
                $('#specifications-tab').click();
            }, 500);
        } else {
            Swal.fire('Error', 'Failed to load quotation details', 'error');
        }
    }).fail(function() {
        Swal.fire('Error', 'Failed to load quotation details', 'error');
    });
}

function printQuotation(quotationId) {
    // Open print quotation in new window
    const printUrl = '<?= base_url('marketing/quotations/print/') ?>' + quotationId;
    window.open(printUrl, '_blank', 'noopener,noreferrer');
}

function sendQuotation(quotationId) {
    Swal.fire({
        title: 'Send Quotation?',
        text: 'Mark quotation as sent to customer.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Send',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'swal2-small'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/send-quotation') ?>/' + quotationId)
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Success!', 'Quotation sent successfully', 'success');
                        
                        // Reload table and statistics
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // Refresh modal if open
                        if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                            viewQuotation(quotationId);
                        }
                    } else {
                        // Check if this is a specifications requirement error
                        if (response.require_specs) {
                            Swal.fire({
                                title: 'Specifications Required',
                                text: response.message,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Add Specifications',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    addSpecifications(quotationId);
                                }
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Failed to send quotation', 'error');
                });
        }
    });
}

function markAsDeal(quotationId) {
    // First check if customer profile validation is needed
    $.get('<?= base_url('marketing/quotations/customer-profile-status/') ?>' + quotationId, function(response) {
        if (response.success && response.has_customer && !response.is_complete) {
            // Customer profile is not complete - show validation modal
            Swal.fire({
                title: 'Customer Profile Validation Required',
                html: `
                    <div class="text-start">
                        <p><strong>🔒 Customer Profile Incomplete</strong></p>
                        <p>Before marking this quotation as DEAL, the customer profile must be completed with:</p>
                        <ul class="text-start" style="list-style-type: disc; padding-left: 20px;">
                            <li>Complete customer information</li>
                            <li>At least one valid customer location</li>
                            <li>Contact person details</li>
                            <li>Complete address information</li>
                        </ul>
                        <p><strong>Would you like to:</strong></p>
                    </div>
                `,
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Complete Profile First',
                denyButtonText: 'Mark as Deal Anyway',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'swal2-large'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Open customer profile in new tab
                    if (response.customer_id) {
                        const customerEditUrl = '<?= base_url('customer-management/showCustomer/') ?>' + response.customer_id;
                        window.open(customerEditUrl, '_blank');
                        Swal.fire({
                            title: 'Customer Profile Opened',
                            text: 'Please complete the customer profile in the new tab, then return here to mark as deal.',
                            icon: 'info'
                        });
                    } else {
                        Swal.fire('Error', 'Customer ID not found', 'error');
                    }
                } else if (result.isDenied) {
                    // Proceed with marking as deal without complete profile
                    proceedMarkAsDeal(quotationId, true);
                }
                // If cancelled, do nothing
            });
        } else {
            // Customer profile is complete or no customer exists yet, proceed normally
            proceedMarkAsDeal(quotationId, false);
        }
    }).fail(function() {
        // If profile check fails, proceed with normal flow
        Swal.fire('Warning', 'Could not verify customer profile status. Proceeding with normal flow.', 'warning');
        proceedMarkAsDeal(quotationId, false);
    });
}

function proceedMarkAsDeal(quotationId, skipValidation = false) {
    const confirmText = skipValidation ? 
        'Will mark as deal and automatically create customer record (profile can be completed later).' :
        'Will mark as deal and automatically create customer record.';
        
    Swal.fire({
        title: 'Mark as Deal?',
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Deal',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'swal2-small'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/markAsDeal') ?>/' + quotationId)
                .done(function(response) {
                    if (response.success) {
                        // Show location modal and DO NOT update table yet
                        // Table will be updated only after location workflow completes
                        showCustomerLocationModal(response.customer_id, quotationId, response.message);
                        
                        // Do NOT reload table here - wait for location selection completion
                        // quotationsTable.ajax.reload(); // Removed
                        // loadStatistics(); // Removed
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Failed to mark as deal', 'error');
                });
        }
    });
}

// Function to handle complete customer profile button
function completeCustomerProfile(quotationId) {
    // Get quotation details to extract customer ID
    $.get('<?= base_url('marketing/quotations/getQuotation/') ?>' + quotationId)
        .done(function(response) {
            if (response.success && response.data && response.data.created_customer_id) {
                // Show customer location modal directly
                showCustomerLocationModal(response.data.created_customer_id, quotationId, 'Please complete customer profile and location to proceed with contract creation.');
            } else {
                Swal.fire('Error', 'Customer not found for this quotation', 'error');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'Failed to load quotation details', 'error');
        });
}

// Function to handle complete customer contract button
function completeCustomerContract(quotationId) {
    // Get quotation details to extract customer ID and location ID
    $.get('<?= base_url('marketing/quotations/getQuotation/') ?>' + quotationId)
        .done(function(response) {
            if (response.success && response.data) {
                if (!response.data.created_customer_id) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No Customer Linked',
                        text: 'This quotation is not linked to any customer. Please create customer first.',
                        confirmButtonText: 'Create Customer'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            completeCustomerProfile(quotationId);
                        }
                    });
                    return;
                }
                
                // Extract customer location ID from quotation data
                const customerLocationId = response.data.customer_location_id || response.data.location_id;
                console.log('📍 Extracted customer location ID from quotation:', customerLocationId);
                
                // Verify customer exists
                $.get('<?= base_url('customers/get/') ?>' + response.data.created_customer_id)
                    .done(function(customerResponse) {
                        if (!customerResponse.success) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Customer Not Found',
                                text: 'The linked customer (ID: ' + response.data.created_customer_id + ') no longer exists. Please update quotation.',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                        
                        // Customer exists, proceed with contract check, passing location ID
                        checkCustomerContracts(response.data.created_customer_id, quotationId, customerLocationId, 'Please select or create a contract to proceed with SPK creation.');
                    })
                    .fail(function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Verifying Customer',
                            text: 'Could not verify customer existence. Error: ' + (xhr.responseJSON?.message || xhr.statusText),
                            confirmButtonText: 'OK'
                        });
                    });
            } else {
                Swal.fire('Error', 'Failed to load quotation data', 'error');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'Failed to load quotation details', 'error');
        });
}

function markAsNotDeal(quotationId) {
    Swal.fire({
        title: 'Mark as No Deal?',
        text: 'This will close the quotation permanently.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, No Deal',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'swal2-small'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/markAsNotDeal') ?>/' + quotationId)
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Success!', response.message, 'success');
                        
                        // Reload table and statistics
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // Refresh modal if open
                        if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                            viewQuotation(quotationId);
                        }
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Failed to mark as not deal', 'error');
                });
        }
    });
}

// Function to show customer location modal after marking as deal
function showCustomerLocationModal(customerId, quotationId, dealMessage) {
    console.log('=== showCustomerLocationModal ===');
    console.log('Customer ID:', customerId);
    console.log('Quotation ID:', quotationId);
    console.log('Fetching locations from:', '<?= base_url('customers/getLocations') ?>/' + customerId);
    
    // Get customer's existing locations
    $.get('<?= base_url('customers/getLocations') ?>/' + customerId)
        .done(function(locationResponse) {
            console.log('Location response received:', locationResponse);
            
            let existingLocations = locationResponse.data || locationResponse.locations || [];
            console.log('Customer locations found:', existingLocations.length, 'locations');
            
            if (existingLocations.length > 0) {
                console.log('Location details:', existingLocations);
            }
            
            // Show location selection modal (customerData will be empty for now)
            showLocationSelectionModal(customerId, quotationId, dealMessage, existingLocations, {});
        })
        .fail(function(xhr, status, error) {
            console.error('=== FAILED to get customer locations ===');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            
            Swal.fire({
                title: 'Error Loading Locations',
                text: 'Failed to load customer locations. Error: ' + (xhr.responseText || error),
                icon: 'error',
                confirmButtonText: 'Continue Anyway'
            }).then(() => {
                // Fallback - show unified modal with no existing locations (will show Add New by default)
                showLocationSelectionModal(customerId, quotationId, dealMessage, [], {});
            });
        });
}

// Function to show location selection modal (unified)
function showLocationSelectionModal(customerId, quotationId, dealMessage, locations, customerData) {
    console.log('=== showLocationSelectionModal ===');
    console.log('Received locations:', locations);
    console.log('Locations count:', locations ? locations.length : 0);
    
    customerData = customerData || {};
    
    let locationOptions = '<option value="">-- Select a location --</option>';
    
    // Add existing locations to dropdown if any
    if (locations && locations.length > 0) {
        console.log('Building location options for', locations.length, 'locations');
        locations.forEach(function(location) {
            console.log('Processing location:', location);
            let cityProvince = '';
            if (location.city) cityProvince += location.city;
            if (location.province) cityProvince += (cityProvince ? ', ' : '') + location.province;
            
            let displayText = location.location_name;
            if (cityProvince) displayText += ' (' + cityProvince + ')';
            if (location.contact_person) displayText += ' - ' + location.contact_person;
            
            locationOptions += `<option value="${location.id}">${displayText}</option>`;
        });
        console.log('Final location options HTML:', locationOptions);
    } else {
        console.log('No locations to display');
    }

    console.log('Setting dropdown HTML...');
    $('#modalLocationSelect').html(locationOptions);
    console.log('Dropdown updated. Current dropdown HTML:', $('#modalLocationSelect').html());
    
    // Update alert message and UI based on whether locations exist
    let alertMessage;
    if (locations && locations.length > 0) {
        alertMessage = `<i class="fas fa-map-marker-alt"></i> Customer has <strong>${locations.length}</strong> existing location(s). Select one or add new.`;
        // Show "Select Existing" tab by default
        $('#btnSelectExisting').addClass('active');
        $('#btnAddNew').removeClass('active');
        $('#existingLocationSection').show();
        $('#addNewLocationSection').hide();
    } else {
        alertMessage = '<i class="fas fa-info-circle"></i> No existing locations found. Please add new location.';
        // Show "Add New" tab by default
        $('#btnAddNew').addClass('active');
        $('#btnSelectExisting').removeClass('active');
        $('#existingLocationSection').hide();
        $('#addNewLocationSection').show();
        // Load areas when showing Add New by default
        loadAreasForLocation();
    }
    
    $('#locationModalAlert').html(alertMessage);
    
    // Reset form
    $('#customerLocationFormUnified')[0].reset();
    $('#locationPreview').addClass('d-none');
    
    // Populate form with customer data (read-only fields)
    $('#unified_customer_id').val(customerId);
    
    // Pre-fill contact person, phone, email from customer data if available
    if (customerData.contact_person) {
        $('#unified_contact_person').val(customerData.contact_person).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_contact_person').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.phone || customerData.contact_phone) {
        $('#unified_phone').val(customerData.phone || customerData.contact_phone).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_phone').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.email || customerData.contact_email) {
        $('#unified_email').val(customerData.email || customerData.contact_email).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_email').prop('readonly', false).removeClass('bg-light');
    }
    
    // Pre-fill address data from customer if available
    if (customerData.address) {
        $('#unified_address').val(customerData.address).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_address').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.city) {
        $('#unified_city').val(customerData.city).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_city').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.province) {
        $('#unified_province').val(customerData.province).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_province').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.postal_code) {
        $('#unified_postal_code').val(customerData.postal_code).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_postal_code').prop('readonly', false).removeClass('bg-light');
    }
    
    // Store data for later use
    $('#selectCustomerLocationModal').data('customerId', customerId);
    $('#selectCustomerLocationModal').data('quotationId', quotationId);
    $('#selectCustomerLocationModal').data('dealMessage', dealMessage);
    $('#selectCustomerLocationModal').data('existingLocations', locations);
    $('#selectCustomerLocationModal').data('customerData', customerData);
    
    // Set customer name in modal title
    if (customerData && customerData.customer_name) {
        $('#locationModalCustomerName').text('Customer: ' + customerData.customer_name);
    } else {
        $('#locationModalCustomerName').text('');
    }
    
    // CRITICAL: Always reset workflowCompleted to false on modal open
    $('#selectCustomerLocationModal').data('workflowCompleted', false);
    console.log('🔒 Workflow flag reset to FALSE - location selection required');
    
    // Add modal close prevention
    $('#selectCustomerLocationModal').off('hide.bs.modal');
    $('#selectCustomerLocationModal').on('hide.bs.modal', function(e) {
        // Only allow close if workflow completed
        if (!$(this).data('workflowCompleted')) {
            console.log('Modal close prevented - workflow not completed');
            e.preventDefault();
            
            Swal.fire({
                title: 'Location Required',
                text: 'Please select/save a location to continue, or cancel the deal process.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Continue Selection',
                cancelButtonText: 'Cancel Deal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isDismissed || result.dismiss === Swal.DismissReason.cancel) {
                    // User wants to cancel the deal process
                    console.log('❌ User cancelled location selection - workflow NOT completed');
                    
                    // Remove the hide prevention handler first
                    $('#selectCustomerLocationModal').off('hide.bs.modal');
                    
                    // Keep workflow flag as false
                    $('#selectCustomerLocationModal').data('workflowCompleted', false);
                    
                    // Now force close the modal
                    $('#selectCustomerLocationModal').modal('hide');
                    
                    Swal.fire({
                        title: 'Process Cancelled',
                        text: 'The location selection has been cancelled.',
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
    
    // Show modal
    $('#selectCustomerLocationModal').modal('show');
}

// Function to load areas for location dropdown
function loadAreasForLocation() {
    console.log('Loading areas for location dropdown...');
    $.get('<?= base_url('marketing/customer-management/getAreas') ?>')
        .done(function(response) {
            console.log('Areas response:', response);
            let options = '<option value="">Select Area</option>';
            
            if (response.success && response.data) {
                response.data.forEach(function(area) {
                    options += `<option value="${area.id}">${area.area_name}</option>`;
                });
                console.log('Areas loaded successfully:', response.data.length, 'areas');
            } else {
                console.error('Invalid areas response format:', response);
            }
            
            $('#unified_area_id').html(options);
        })
        .fail(function(xhr) {
            console.error('Failed to load areas:', xhr.responseText);
            $('#unified_area_id').html('<option value="">Failed to load areas</option>');
        });
}

// Event handlers for Bootstrap modals
// Second $(document).ready() block removed - merged into main block above

// Function to save customer location
function saveCustomerLocation(customerId, locationData, quotationId) {
    locationData.customer_id = customerId;
    locationData.is_primary = 1;
    locationData.is_active = 1;
    locationData.location_type = 'HEAD_OFFICE';
    locationData.quotation_id = quotationId; // CRITICAL: Pass quotation ID for workflow tracking
    locationData.workflow_completed = true; // Flag to indicate modal workflow is complete
    
    console.log('Saving customer location with quotation ID:', locationData);
    
    $.ajax({
        url: '<?= base_url('customers/saveLocation') ?>',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(locationData),
        dataType: 'json'
    })
    .done(function(response) {
            console.log('Location save response:', response);
            if (response.success) {
                // Store location ID globally for contract creation
                if (response.data && response.data.location_id) {
                    window.currentSelectedLocationId = response.data.location_id;
                    console.log('Stored new location ID:', window.currentSelectedLocationId);
                }
                
                // Mark workflow as completed in modal
                $('#selectCustomerLocationModal').data('workflowCompleted', true);
                $('#selectCustomerLocationModal').modal('hide');
                
                Swal.fire({
                    title: 'Location Saved!',
                    text: 'Customer location saved successfully. Click "Complete Contract" to continue.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Reload DataTable to reflect updated workflow stage
                if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                    quotationsTable.ajax.reload(null, false);
                }
                
                // Do NOT auto-proceed to contract - let user click button
            } else {
                console.error('❌ Location save failed:', response.message);
                // Keep modal open and show error
                Swal.fire({
                    title: 'Failed to Save Location',
                    text: response.message || 'Failed to save location. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
                // DO NOT close modal - user must fix and retry
            }
        })
        .fail(function(xhr) {
            console.error('❌ AJAX Failed to save location');
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseText);
            console.error('Full XHR:', xhr);
            
            let errorMessage = 'Failed to save customer location. ';
            
            if (xhr.status === 404) {
                errorMessage += 'API endpoint not found (404). Please check route configuration.';
            } else if (xhr.status === 500) {
                errorMessage += 'Server error (500). Check server logs for details.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage += xhr.responseJSON.message;
            } else {
                errorMessage += 'Unknown error occurred.';
            }
            
            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
            
            // DO NOT close modal or proceed - user must fix the issue
        });
}

// Function to update customer primary location
function updateCustomerPrimaryLocation(customerId, locationId, quotationId, dealMessage) {
    const updateData = {
        customer_id: customerId,
        location_id: locationId,
        quotation_id: quotationId, // CRITICAL: Pass quotation ID for workflow tracking
        workflow_completed: true // Flag to indicate modal workflow is complete
    };
    
    // Store location globally for contract creation
    window.currentSelectedLocationId = locationId;
    
    $.ajax({
        url: '<?= base_url('customers/setPrimaryLocation') ?>',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(updateData),
        dataType: 'json'
    })
    .done(function(response) {
        console.log('Primary location update response:', response);
        if (response.success) {
            // Mark workflow as completed in modal
            $('#selectCustomerLocationModal').data('workflowCompleted', true);
            $('#selectCustomerLocationModal').modal('hide');
            
            Swal.fire({
                title: 'Location Selected!', 
                text: 'Location selected successfully. Click "Complete Contract" to continue.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Reload DataTable to reflect updated workflow stage
            if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                quotationsTable.ajax.reload(null, false);
            }
            
            // Do NOT auto-proceed to contract - let user click button
        } else {
            console.error('❌ Primary location update failed:', response.message);
            Swal.fire({
                title: 'Failed to Update Location',
                text: response.message || 'Failed to update primary location. Please try again.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
            // DO NOT close modal
        }
    })
    .fail(function(xhr) {
        console.error('❌ AJAX Failed to update primary location');
        console.error('Status:', xhr.status);
        console.error('Response:', xhr.responseText);
        console.error('Full XHR:', xhr);
        
        let errorMessage = 'Failed to update customer primary location. ';
        
        if (xhr.status === 404) {
            errorMessage += 'API endpoint not found (404). Please check route configuration.';
        } else if (xhr.status === 500) {
            errorMessage += 'Server error (500). Check server logs for details.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage += xhr.responseJSON.message;
        } else {
            errorMessage += 'Unknown error occurred.';
        }
        
        Swal.fire({
            title: 'Error',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'Try Again'
        });
        
        // DO NOT close modal or proceed
    });
}

// Function to check customer contracts and proceed to next workflow
function checkCustomerContracts(customerId, quotationId, customerLocationId, message) {
    console.log('Checking customer contracts for customer:', customerId, 'location:', customerLocationId);
    
    // Directly show create/select contract modal (unified modal)
    showCreateContractModal(customerId, quotationId, customerLocationId, message);
}

// Function to show create contract modal
function showCreateContractModal(customerId, quotationId, customerLocationId, message) {
    console.log('showCreateContractModal called:', {customerId, quotationId, customerLocationId, message});
    
    // Store in global variables for contract form
    window.currentContractQuotationId = quotationId;
    window.currentContractCustomerId = customerId;
    window.currentSelectedLocationId = customerLocationId; // Store location ID
    
    console.log('✅ Stored location ID in window.currentSelectedLocationId:', customerLocationId);
    
    // Show modal immediately with loading state
    $('#addContractModal').modal('show');
    
    // Set dropdown to "Add New Contract" by default and show form
    $('#contractOrPOSelect').val('__ADD_NEW__');
    $('#contractFormSection').show();
    
    // Enable all fields for new entry
    $('#contract_number_input, #po_number_input').prop('disabled', false).prop('readonly', false).removeClass('bg-light');
    $('#contract_start_date, #contract_end_date, #contract_jenis_sewa').prop('disabled', false);
    $('#contract_catatan').prop('readonly', false).removeClass('bg-light');
    $('#generateContractNumberBtn').prop('disabled', false);
    
    // Change button text
    $('#addContractForm button[type="submit"]').html('<i class="fas fa-save me-2"></i>Save New Contract');
    
    // Load customer and location data
    loadCustomersForContract(customerId);
    
    // Update modal title
    $.ajax({
        url: `<?= base_url('customers/get/') ?>${customerId}`,
        method: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                const customerName = response.data.customer_name;
                $('#contractModalCustomerName').text('Customer: ' + customerName);
                console.log('Customer name set in contract modal:', customerName);
            }
        },
        error: function(xhr) {
            console.error('Error loading customer for contract modal:', xhr);
            $('#contractModalCustomerName').text('Customer: Unknown');
        }
    });
}

// Function to load customer and location data for contract modal (read-only)
function loadCustomersForContract(customerId) {
    console.log('Loading customer and location data for contract modal:', customerId);
    
    const quotationId = window.currentContractQuotationId;
    const locationId = window.currentSelectedLocationId;
    
    // Set quotation ID and customer ID
    $('#contractQuotationId').val(quotationId);
    $('#customerIdContractNew').val(customerId);
    
    // Load existing contracts for this customer
    loadExistingContracts(customerId);
    
    // Get quotation data to populate customer and location
    $.ajax({
        url: `<?= base_url('marketing/quotations/get-quotation/') ?>${quotationId}`,
        method: 'GET',
        success: function(quotationResponse) {
            console.log('📥 Quotation data loaded for contract:', quotationResponse);
            
            if (quotationResponse.status === 'success' || quotationResponse.success) {
                const quotation = quotationResponse.data || quotationResponse;
                
                // Set customer name from quotation (prospect_name or customer_name)
                const customerName = quotation.customer_name || quotation.prospect_name || 'N/A';
                
                // Use setTimeout to ensure field is rendered before setting value
                setTimeout(function() {
                    $('#customerNameDisplay').val(customerName);
                    console.log('✅ Customer name set:', customerName, '| Field value:', $('#customerNameDisplay').val());
                }, 100);
                
                // Set location name if available
                if (quotation.location_name) {
                    setTimeout(function() {
                        $('#locationNameDisplay').val(quotation.location_name);
                        console.log('✅ Location name set:', quotation.location_name, '| Field value:', $('#locationNameDisplay').val());
                    }, 100);
                    
                    // If we have location from quotation, also set the location ID
                    if (locationId) {
                        $('#locationIdContractNew').val(locationId);
                    }
                } else if (locationId) {
                    // If no location in quotation but we have locationId, fetch location details
                    $.ajax({
                        url: `<?= base_url('customers/getLocations/') ?>${customerId}`,
                        method: 'GET',
                        success: function(locResponse) {
                            console.log('📥 Locations loaded:', locResponse);
                            if (locResponse.success && locResponse.data) {
                                const selectedLocation = locResponse.data.find(loc => loc.id == locationId);
                                if (selectedLocation) {
                                    setTimeout(function() {
                                        $('#locationNameDisplay').val(selectedLocation.location_name);
                                        $('#locationIdContractNew').val(selectedLocation.id);
                                        console.log('✅ Location set from customer locations:', selectedLocation.location_name);
                                    }, 100);
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Error loading locations:', xhr);
                        }
                    });
                } else {
                    setTimeout(function() {
                        $('#locationNameDisplay').val('No location selected');
                        console.warn('⚠️ No location available');
                    }, 100);
                }
            } else {
                console.error('❌ Invalid quotation response');
            }
        },
        error: function(xhr) {
            console.error('❌ Error loading quotation for contract:', xhr);
            const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Unknown error';
            console.error('Quotation error details:', errorMsg);
            
            // Fallback: try to get customer data directly
            $.ajax({
                url: `<?= base_url('customers/get/') ?>${customerId}`,
                method: 'GET',
                success: function(custResponse) {
                    if (custResponse.success && custResponse.data) {
                        setTimeout(function() {
                            $('#customerNameDisplay').val(custResponse.data.customer_name);
                            console.log('✅ Customer name set from customer API:', custResponse.data.customer_name);
                        }, 100);
                    }
                },
                error: function(xhr) {
                    console.error('❌ Error loading customer:', xhr);
                }
            });
        }
    });
}

// Function to load existing contracts for customer
function loadExistingContracts(customerId) {
    console.log('=== loadExistingContracts START ===');
    console.log('Customer ID:', customerId);
    console.log('API URL:', `<?= base_url('customers/getContracts/') ?>${customerId}`);
    
    $.ajax({
        url: `<?= base_url('customers/getContracts/') ?>${customerId}`,
        method: 'GET',
        dataType: 'json',
        beforeSend: function(xhr) {
            console.log('Sending AJAX request for contracts...');
        },
        success: function(response) {
            console.log('✅ Contracts API Response:', response);
            console.log('Response type:', typeof response);
            console.log('Has contracts array?', response.hasOwnProperty('contracts'));
            
            const dropdown = $('#contractOrPOSelect');
            console.log('Dropdown element found:', dropdown.length > 0);
            
            // Keep the default option and "+ Tambah Kontrak Baru"
            dropdown.find('option:not([value=""]):not([value="__ADD_NEW__"])').remove();
            
            if (response.success && response.contracts && response.contracts.length > 0) {
                console.log(`✅ Found ${response.contracts.length} existing contracts:`, response.contracts);
                
                // Add existing contracts before the "+ Tambah Kontrak Baru" option
                const addNewOption = dropdown.find('option[value="__ADD_NEW__"]').detach();
                console.log('Removed "Add New" option temporarily');
                
                response.contracts.forEach((contract, index) => {
                    const displayText = `${contract.no_kontrak || contract.no_po_marketing || 'N/A'} - ${contract.tanggal_mulai || ''} s/d ${contract.tanggal_berakhir || ''}`;
                    console.log(`Adding contract ${index + 1}:`, displayText);
                    
                    const option = $('<option></option>')
                        .val(contract.id)
                        .text(displayText)
                        .data('contract', contract);
                    dropdown.append(option);
                });
                
                // Re-add the "+ Tambah Kontrak Baru" option at the end
                dropdown.append(addNewOption);
                console.log('Re-added "Add New" option');
                
                console.log('✅ Contracts populated in dropdown. Total options:', dropdown.find('option').length);
            } else {
                console.warn('⚠️ No existing contracts found or empty response');
                console.log('Response details - success:', response.success, 'contracts:', response.contracts);
            }
            console.log('=== loadExistingContracts END ===');
        },
        error: function(xhr, status, error) {
            console.error('❌ Error loading contracts');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('XHR:', xhr);
            console.error('Response Text:', xhr.responseText);
            
            const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Unknown error';
            console.error('Contract loading error details:', errorMsg);
            
            // Show user-friendly message
            Swal.fire({
                icon: 'info',
                title: 'No Existing Contracts',
                text: 'Could not load existing contracts. You can create a new contract.',
                timer: 2000,
                showConfirmButton: false
            });
            
            console.log('=== loadExistingContracts END (with error) ===');
        }
    });
}

// Function to reset contract form to empty state
function resetContractForm() {
    // Clear all field values
    $('#contract_number_input').val('');
    $('#po_number_input').val('');
    $('#contract_start_date').val('');
    $('#contract_end_date').val('');
    $('#contract_jenis_sewa').val('');
    $('#contract_catatan').val('');
    $('#selectedContractId').val('');
    $('#customerNameDisplay').val('');
    $('#locationNameDisplay').val('');
    
    // Remove readonly state and styling
    $('#contract_number_input, #po_number_input, #customerNameDisplay, #locationNameDisplay').prop('readonly', false).removeClass('bg-light');
    $('#generateContractNumberBtn').prop('disabled', false);
}

// Function to populate contract form with existing contract data
function populateContractForm(contractData) {
    console.log('Populating contract form with:', contractData);
    
    // Basic contract fields
    $('#contract_number_input').val(contractData.no_kontrak || '');
    $('#po_number_input').val(contractData.no_po_marketing || '');
    $('#contract_start_date').val(contractData.tanggal_mulai || '');
    $('#contract_end_date').val(contractData.tanggal_berakhir || '');
    $('#contract_jenis_sewa').val(contractData.jenis_sewa || '');
    $('#contract_catatan').val(contractData.catatan || '');
    $('#selectedContractId').val(contractData.id || '');
    
    // Customer and Location fields
    $('#customerIdContract').val(contractData.customer_id || '');
    $('#customerNameDisplay').val(contractData.customer_name || contractData.nama_customer || '');
    
    $('#locationIdContractNew').val(contractData.location_id || '');
    $('#locationNameDisplay').val(contractData.location_name || contractData.alamat || '');
    
    console.log('✅ Contract form populated with customer:', contractData.customer_name, 'location:', contractData.location_name);
}

// Function to search and load contract by contract number or PO number
function searchAndLoadContract(field, value) {
    const customerId = window.currentContractCustomerId;
    
    console.log(`Searching contract by ${field}:`, value);
    
    $.ajax({
        url: `<?= base_url('customers/searchContract') ?>`,
        method: 'POST',
        data: {
            customer_id: customerId,
            field: field,
            value: value,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success && response.data) {
                console.log('Contract found:', response.data);
                populateContractForm(response.data);
                
                // Update dropdown to show this contract is selected
                const contractId = response.data.id;
                if ($(`#contractOrPOSelect option[value="${contractId}"]`).length > 0) {
                    $('#contractOrPOSelect').val(contractId).trigger('change');
                }
                
                Swal.fire({
                    icon: 'info',
                    title: 'Contract Found',
                    text: 'Existing contract data loaded',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                console.log('No matching contract found');
            }
        },
        error: function(xhr) {
            console.error('Error searching contract:', xhr);
        }
    });
}

// Function to save new contract
function saveNewContract(formData) {
    console.log('Saving new contract:', formData);
    
    $.ajax({
        url: '<?= base_url('marketing/kontrak/store') ?>',
        method: 'POST',
        data: {
            ...formData,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            console.log('Contract save response:', response);
            
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Contract created successfully',
                    icon: 'success',
                    timer: 2000
                });
                
                // Update quotation contract complete flag
                updateQuotationContractComplete(formData.quotation_id);
                
                // Close modal and refresh
                $('#addContractModal').modal('hide');
                
                // Refresh quotations table
                if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                    quotationsTable.ajax.reload(null, false);
                }
            } else {
                Swal.fire('Error', response.message || 'Failed to save contract', 'error');
            }
        },
        error: function(xhr) {
            console.error('Error saving contract:', xhr);
            Swal.fire('Error', 'Failed to save contract: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
    });
}

// Function to link existing contract to quotation (Option 1: Pure Selection - No Update)
function linkExistingContract(contractId, quotationId) {
    console.log('Linking existing contract to quotation:', {contractId, quotationId});
    
    // Simply update quotation to mark contract as complete
    // No changes to the contract itself
    $.ajax({
        url: `<?= base_url('marketing/quotations/linkContract') ?>`,
        method: 'POST',
        data: {
            quotation_id: quotationId,
            contract_id: contractId,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            console.log('Contract link response:', response);
            
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Contract linked to quotation successfully',
                    icon: 'success',
                    timer: 2000
                });
                
                // Update quotation contract complete flag
                updateQuotationContractComplete(quotationId);
                
                // Close modal and refresh
                $('#addContractModal').modal('hide');
                
                // Refresh quotations table
                if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                    quotationsTable.ajax.reload(null, false);
                }
            } else {
                Swal.fire('Error', response.message || 'Failed to link contract', 'error');
            }
        },
        error: function(xhr) {
            console.error('Error linking contract:', xhr);
            Swal.fire('Error', 'Failed to link contract: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
    });
}

// Function to update quotation contract complete flag
function updateQuotationContractComplete(quotationId) {
    console.log('Updating quotation contract complete flag:', quotationId);
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/updateContractComplete') ?>',
        method: 'POST',
        data: {
            quotation_id: quotationId,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            console.log('Quotation contract flag updated:', response);
            if (response.success) {
                Swal.fire({
                    title: 'Workflow Updated!',
                    text: 'Contract stage completed. You can now create SPK.',
                    icon: 'success',
                    timer: 2000
                });
            }
        },
        error: function(xhr) {
            console.error('Error updating contract flag:', xhr);
        }
    });
}

// Function to complete deal workflow (with contract selected)
function completeDealWorkflow(quotationId, successMessage) {
    Swal.fire({
        title: 'Workflow Completed!',
        text: successMessage,
        icon: 'success',
        timer: 2000
    });
    
    // Refresh quotations table to update action buttons
    if (typeof quotationsTable !== 'undefined' && quotationsTable) {
        quotationsTable.ajax.reload(null, false); // Maintain current page
    }
    
    // Load statistics
    loadStatistics();
    
    // Refresh modal if open
    if (typeof currentQuotationId !== 'undefined' && currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
        setTimeout(() => {
            viewQuotation(quotationId);
        }, 500);
    }
    
    // Close any open modals
    $('.modal').modal('hide');
}

// Function to complete deal workflow with table update (when workflow done)
function completeDealWorkflowWithUpdate(quotationId, successMessage) {
    Swal.fire({
        title: 'Deal Completed!',
        text: successMessage,
        icon: 'success',
        timer: 3000
    });
    
    // Refresh quotations table to update action buttons
    if (typeof quotationsTable !== 'undefined' && quotationsTable) {
        quotationsTable.ajax.reload(null, false);
    }
    
    // Load statistics  
    loadStatistics();
    
    // Refresh modal if open
    if (typeof currentQuotationId !== 'undefined' && currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
        setTimeout(() => {
            viewQuotation(quotationId);
        }, 500);
    }
    
    // Close any open modals
    $('.modal').modal('hide');
}

function createCustomerFromDeal(quotationId) {
    Swal.fire({
        title: 'Create Customer?',
        text: 'This will create a customer record from this successful deal and save the prospect data permanently.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Create Customer',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/createCustomerFromDeal') ?>/' + quotationId)
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Customer Created!',
                            text: response.message,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'View Customer',
                            cancelButtonText: 'Continue Here'
                        }).then((result) => {
                            if (result.isConfirmed && response.customer_id) {
                                // Redirect to customer detail page
                                window.open('<?= base_url('customer-management/showCustomer/') ?>' + response.customer_id, '_blank');
                            }
                        });
                        
                        // Reload table and statistics
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // Refresh modal if open
                        if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                            viewQuotation(quotationId);
                        }
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Failed to create customer', 'error');
                });
        }
    });
}

function createContract(quotationId) {
    // Get quotation data first to get customer ID
    $.get('<?= base_url('marketing/quotations/detail') ?>/' + quotationId)
        .done(function(quotationResponse) {
            if (!quotationResponse.success || !quotationResponse.quotation.created_customer_id) {
                Swal.fire('Error', 'Customer must be created before contract. Please mark as deal first.', 'error');
                return;
            }
            
            const customerId = quotationResponse.quotation.created_customer_id;
            
            // Check customer profile completion
            $.get('<?= base_url('marketing/quotations/getCustomerProfileStatus') ?>/' + customerId)
                .done(function(profileResponse) {
                    if (profileResponse.success && !profileResponse.profile_status.complete) {
                        // Customer profile is not complete
                        Swal.fire({
                            title: 'Customer Profile Incomplete',
                            html: `
                                <div class="text-left">
                                    <p class="mb-2">Customer profile must be completed before creating contract.</p>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Customer: <strong>${profileResponse.profile_status.customer_name}</strong>
                                    </div>
                                    <p class="small text-muted">Please complete customer address, contact details, and location information.</p>
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Complete Profile',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open customer edit page in new tab
                                const customerEditUrl = '<?= base_url('customers/edit') ?>/' + customerId;
                                window.open(customerEditUrl, '_blank');
                            }
                        });
                        return;
                    }
                    
                    // Profile is complete, proceed with contract creation
                    Swal.fire({
                        title: 'Create Contract & PO?',
                        text: 'This will create contract and purchase order documents.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Create',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceedWithContractCreation(quotationId);
                        }
                    });
                })
                .fail(function() {
                    Swal.fire('Error', 'Failed to check customer profile status', 'error');
                });
        })
        .fail(function() {
            Swal.fire('Error', 'Failed to get quotation details', 'error');
        });
}

function proceedWithContractCreation(quotationId) {
    $.post('<?= base_url('marketing/quotations/createContract') ?>/' + quotationId)
        .done(function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Contract Created!',
                    text: response.message,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'View Contract',
                    cancelButtonText: 'Continue Here'
                }).then((result) => {
                    if (result.isConfirmed && response.contract_id) {
                        // Redirect to contract detail page
                        window.open('<?= base_url('contracts/detail') ?>/' + response.contract_id, '_blank');
                    }
                });
                
                // Reload table to show updated actions
                quotationsTable.ajax.reload();
                
                // Refresh modal if open
                if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                    viewQuotation(quotationId);
                }
            } else {
                if (response.require_profile_completion) {
                    Swal.fire({
                        title: 'Customer Profile Required',
                        text: response.message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Complete Profile',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const customerEditUrl = '<?= base_url('customers/edit') ?>/' + response.customer_id;
                            window.open(customerEditUrl, '_blank');
                        }
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        })
        .fail(function() {
            Swal.fire('Error', 'Failed to create contract', 'error');
        });
}

function createSPK(quotationId) {
    // Direct call to createSPKFromQuotation with validation
    $.get('<?= base_url('marketing/quotations/getQuotation') ?>/' + quotationId)
        .done(function(quotationResponse) {
            if (!quotationResponse.success || !quotationResponse.data || !quotationResponse.data.created_customer_id) {
                Swal.fire('Error', 'Customer must be created before SPK. Please mark as deal first.', 'error');
                return;
            }
            
            const quotation = quotationResponse.data;
            
            // Check if contract is linked
            if (!quotation.created_contract_id) {
                Swal.fire({
                    title: 'Contract Required',
                    html: 'Please create contract first using <strong>"Complete Customer Profile"</strong> button.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Proceed to SPK creation modal with specification selection
            createSPKFromQuotation(quotationId);
        })
        .fail(function() {
            Swal.fire('Error', 'Failed to get quotation details', 'error');
        });
}

function proceedWithSPKCreation(quotationId) {
    // Redirect to new SPK creation modal with specification selection
    createSPKFromQuotation(quotationId);
}

function addSpecifications(quotationId) {
    // Open the specifications modal directly
    $.post('<?= base_url('marketing/quotations/addSpecifications') ?>/' + quotationId)
        .done(function(response) {
            if (response.success && response.open_specifications) {
                // Open the quotation detail modal and switch to specifications tab
                viewQuotation(quotationId);
                
                setTimeout(() => {
                    $('#specifications-tab').click();
                    
                    // Show helpful message
                    Swal.fire({
                        title: 'Add Specifications',
                        text: 'Please add at least one specification before sending the quotation.',
                        icon: 'info',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }, 1000);
            } else {
                Swal.fire('Error', response.message || 'Failed to open specifications', 'error');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'Failed to open specifications', 'error');
        });
}

// Legacy function for backward compatibility
function createCustomer(quotationId) {
    return createCustomerFromDeal(quotationId);
}

// ===== TESTING & DEBUG FUNCTIONS =====

function testWorkflowComplete() {
    console.log('=== TESTING COMPLETE WORKFLOW ===');
    
    // Test 1: Check if quotations table is loaded
    console.log('Test 1: DataTable status');
    if (typeof quotationsTable !== 'undefined') {
        console.log('✅ DataTable initialized');
    } else {
        console.log('❌ DataTable not initialized');
        return;
    }
    
    // Test 2: Check if API endpoints are reachable
    console.log('Test 2: Testing API endpoints');
    
    // Test departemen endpoint
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=departemen')
        .done(function(response) {
            console.log('✅ Departemen API:', response.success ? 'OK' : 'FAILED');
            console.log('   - Data count:', response.data ? response.data.length : 0);
        })
        .fail(function() {
            console.log('❌ Departemen API: FAILED');
        });
    
    // Test tipe unit endpoint  
    $.get('<?= base_url('marketing/customer-management/getTipeUnit') ?>')
        .done(function(response) {
            console.log('✅ Tipe Unit API:', response.success ? 'OK' : 'FAILED');
            console.log('   - Data count:', response.data ? response.data.length : 0);
        })
        .fail(function() {
            console.log('❌ Tipe Unit API: FAILED');
        });
    
    // Test 3: Check workflow stage functions
    console.log('Test 3: Workflow functions');
    const workflowFunctions = ['convertToQuotation', 'openSpecificationsModal', 'sendQuotation', 'markAsDeal', 'markAsNotDeal', 'createCustomer'];
    workflowFunctions.forEach(func => {
        if (typeof window[func] === 'function') {
            console.log('✅', func, 'function available');
        } else {
            console.log('❌', func, 'function missing');
        }
    });
    
    // Test 4: Check modal elements
    console.log('Test 4: Modal elements');
    const modals = ['#createProspectModal', '#addSpecificationModal', '#detailModal'];
    modals.forEach(modal => {
        if ($(modal).length > 0) {
            console.log('✅', modal, 'exists');
        } else {
            console.log('❌', modal, 'missing');
        }
    });
    
    console.log('=== WORKFLOW TEST COMPLETED ===');
}

// Function to simulate complete workflow for testing
function simulateWorkflow(prospectName = 'Test Prospect ' + Date.now()) {
    console.log('=== SIMULATING COMPLETE WORKFLOW ===');
    
    Swal.fire({
        title: 'Start Workflow Simulation?',
        text: 'This will create a test prospect and walk through the complete quotation workflow.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Start Simulation',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Starting workflow simulation...');
            
            // Step 1: Open create prospect modal
            console.log('Step 1: Opening prospect creation modal');
            openCreateProspectModal();
            
            // Auto-fill form for testing (with delay to ensure modal is open)
            setTimeout(() => {
                console.log('Step 2: Auto-filling prospect form');
                $('#prospectName').val(prospectName);
                $('#prospectEmail').val('test@example.com');
                $('#prospectPhone').val('08123456789');
                $('#prospectCompany').val('Test Company');
                $('#prospectAddress').val('Test Address');
                $('#prospectNotes').val('Created via workflow simulation');
                
                console.log('✅ Prospect form filled. Please click "Create Prospect" to continue.');
                console.log('Next steps:');
                console.log('1. Click Create Prospect');
                console.log('2. Convert to Quotation');
                console.log('3. Add Specifications');
                console.log('4. Send Quotation');
                console.log('5. Mark as Deal/Not Deal');
            }, 500);
        }
    });
}

// Quick test functions for individual components
function testProspectCreation() {
    console.log('Testing prospect creation...');
    openCreateProspectModal();
}

function testSpecificationModal() {
    if (!currentQuotationId) {
        Swal.fire('Warning', 'Please select a quotation first', 'warning');
        return;
    }
    console.log('Testing specification modal...');
    openAddSpecificationModal();
}

function testCascadingDropdowns() {
    console.log('Testing cascading dropdowns...');
    if (!currentQuotationId) {
        Swal.fire('Info', 'Creating test quotation first...', 'info');
        testProspectCreation();
        return;
    }
    
    openAddSpecificationModal();
    
    setTimeout(() => {
        loadDepartemenForSpecification();
        loadTipeUnitForSpecification();
    }, 500);
}

// Smart Customer Search Functions
function initCustomerSearch() {
    $('#linkExistingCustomer').on('change', function() {
        if (this.checked) {
            $('#customerSearchSection').slideDown();
            $('#newCustomerSection').slideUp();
            // Remove required attributes from hidden fields
            $('#prospectCompanyName').removeAttr('required');
            $('#prospectContactPerson').removeAttr('required');
        } else {
            $('#customerSearchSection').slideUp();
            $('#newCustomerSection').slideDown();
            // Restore required attributes for visible fields
            $('#prospectCompanyName').attr('required', 'required');
            $('#prospectContactPerson').attr('required', 'required');
            $('#selectedCustomerId').val('');
        }
    });
    
    // Auto-search functionality with debouncing
    let searchTimer;
    $('#customerSearchInput').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        // Clear previous timer
        clearTimeout(searchTimer);
        
        // Clear results if search term is too short
        if (searchTerm.length === 0) {
            $('#customerSearchResults').html('');
            return;
        }
        
        if (searchTerm.length < 2) {
            $('#customerSearchResults').html('<div class="alert alert-info"><i class="fas fa-keyboard me-2"></i>Keep typing... (minimum 2 characters)</div>');
            return;
        }
        
        // Debounce search - wait 500ms after user stops typing
        searchTimer = setTimeout(function() {
            searchCustomers();
        }, 500);
    });
    
    // Keep the manual search button functional
    $('#searchCustomerBtn').on('click', function() {
        clearTimeout(searchTimer);
        searchCustomers();
    });
    
    // Handle Enter key
    $('#customerSearchInput').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimer);
            searchCustomers();
        }
    });
}

function searchCustomers() {
    const searchTerm = $('#customerSearchInput').val().trim();
    if (searchTerm.length < 2) {
        $('#customerSearchResults').html('<div class="alert alert-info"><i class="fas fa-keyboard me-2"></i>Please enter at least 2 characters</div>');
        return;
    }
    
    // Show loading indicator with smaller spinner for auto-search
    $('#customerSearchResults').html(`
        <div class="d-flex align-items-center py-2 px-3 bg-light rounded">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <small class="text-muted">Searching for "${searchTerm}"...</small>
        </div>
    `);
    
    $.ajax({
        url: '<?= base_url('marketing/customer-management/searchCustomers') ?>',
        method: 'POST',
        data: { search: searchTerm },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = '<div class="list-group mt-2">';
                response.data.forEach(function(customer) {
                    const locationDetails = customer.primary_location ? 
                        `<i class="fas fa-map-marker-alt me-1"></i>${customer.primary_location}` : 
                        `<i class="fas fa-building me-1"></i>${customer.location_count} location(s)`;
                    
                    const contactInfo = customer.primary_contact ? 
                        `<i class="fas fa-user me-1"></i>${customer.primary_contact}` + 
                        (customer.primary_phone ? ` <i class="fas fa-phone ms-2 me-1"></i>${customer.primary_phone}` : '') : '';
                    
                    const addressPreview = customer.primary_address ? 
                        `<small class="d-block text-muted mt-1"><i class="fas fa-home me-1"></i>${customer.primary_address.length > 80 ? customer.primary_address.substring(0, 80) + '...' : customer.primary_address}</small>` : '';
                    
                    const locationsPreview = customer.locations_summary ? 
                        `<small class="d-block text-primary mt-1"><i class="fas fa-list me-1"></i>Locations: ${customer.locations_summary}</small>` : '';
                    
                    html += `
                        <div class="list-group-item list-group-item-action customer-search-item" 
                             data-customer-id="${customer.id}" 
                             data-customer-name="${customer.customer_name}"
                             data-customer-code="${customer.customer_code}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${customer.customer_name}</h6>
                                <small class="text-muted">${customer.customer_code}</small>
                            </div>
                            <p class="mb-1">${locationDetails}</p>
                            ${contactInfo ? `<p class="mb-1 text-info">${contactInfo}</p>` : ''}
                            ${addressPreview}
                            ${locationsPreview}
                        </div>
                    `;
                });
                html += '</div>';
                $('#customerSearchResults').html(html);
                
                $('.customer-search-item').on('click', function() {
                    const customerId = $(this).data('customer-id');
                    const customerName = $(this).data('customer-name');
                    const customerCode = $(this).data('customer-code');
                    
                    $('#selectedCustomerId').val(customerId);
                    $('#customerSearchInput').val(`${customerName} (${customerCode})`);
                    $('#customerSearchResults').html(`
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>Selected: <strong>${customerName}</strong> 
                            <small class="text-muted">(${customerCode})</small>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="clearCustomerSelection()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                            <div class="mt-2">
                                <small class="text-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Customer data will be automatically populated in the quotation.
                                </small>
                            </div>
                        </div>
                    `);
                });
            } else {
                $('#customerSearchResults').html(`
                    <div class="alert alert-info">
                        <i class="fas fa-search me-2"></i>No customers found matching "<strong>${searchTerm}</strong>"
                        <div class="mt-2">
                            <small class="d-block">We searched in: Customer names, codes, location names, and cities</small>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="switchToNewCustomer()">
                                <i class="fas fa-plus me-1"></i>Create new customer instead
                            </button>
                        </div>
                    </div>
                `);
            }
        },
        error: function(xhr) {
            console.warn('Search error:', xhr);
            $('#customerSearchResults').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Unable to search right now. Please try again.
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="searchCustomers()">
                        <i class="fas fa-redo me-1"></i>Retry
                    </button>
                </div>
            `);
        }
    });
}

function clearCustomerSelection() {
    $('#selectedCustomerId').val('');
    $('#customerSearchInput').val('');
    $('#customerSearchResults').html('');
}

function switchToNewCustomer() {
    $('#linkExistingCustomer').prop('checked', false).trigger('change');
}

// Reset tabs when modal is hidden
$(document).on('hidden.bs.modal', '#detailModal', function () {
    // Reset to default state - quotation info tab active
    $('#quotation-info-tab').addClass('active').attr('aria-selected', 'true');
    $('#specifications-tab').removeClass('active').attr('aria-selected', 'false');
    $('#quotation-info-content').addClass('show active');
    $('#specifications-content').removeClass('show active');
    
    // Clear current quotation ID
    currentQuotationId = null;
    
    // Unbind the shown.bs.modal event to prevent multiple bindings
    $('#detailModal').off('shown.bs.modal');
});

// Function to create contract for quotation
function createContractForQuotation(quotationId) {
    // Show loading
    Swal.fire({
        title: 'Creating Contract',
        text: 'Please wait...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: `<?= base_url('marketing/quotations/createContract/') ?>${quotationId}`,
        method: 'POST',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    html: `Contract created successfully!<br><strong>${response.contract_number}</strong>`,
                    icon: 'success',
                    confirmButtonText: 'Continue to SPK'
                }).then(() => {
                    // Retry opening SPK modal
                    createSPKFromQuotation(quotationId);
                });
            } else {
                Swal.fire('Error', response.message || 'Failed to create contract', 'error');
            }
        },
        error: function(xhr) {
            console.error('Error creating contract:', xhr);
            const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Failed to create contract';
            Swal.fire('Error', errorMsg, 'error');
        }
    });
}

// Function to create SPK from quotation specifications
function createSPKFromQuotation(quotationId) {
    console.log('Opening SPK creation modal for quotation:', quotationId);
    
    // Get quotation data with specifications
    $.ajax({
        url: `<?= base_url('marketing/quotations/getQuotation/') ?>${quotationId}`,
        method: 'GET',
        success: function(response) {
            if (!response.success || !response.data) {
                Swal.fire('Error', 'Failed to load quotation data', 'error');
                return;
            }
            
            const quotation = response.data;
            
            // Validate quotation has required data
            if (!quotation.created_customer_id) {
                Swal.fire('Error', 'Customer must be created first. Please mark as deal.', 'error');
                return;
            }
            
            if (!quotation.created_contract_id) {
                Swal.fire({
                    title: 'Contract Required',
                    html: 'Please create contract first using <strong>"Complete Customer Profile"</strong> button.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Load specifications for this quotation
            loadSpecificationsForSPK(quotation);
        },
        error: function(xhr) {
            console.error('Error loading quotation:', xhr);
            Swal.fire('Error', 'Failed to load quotation details', 'error');
        }
    });
}

// Function to load specifications for SPK creation
function loadSpecificationsForSPK(quotation) {
    $.ajax({
        url: `<?= base_url('marketing/quotations/getSpecifications/') ?>${quotation.id_quotation}`,
        method: 'GET',
        success: function(response) {
            if (!response.success) {
                Swal.fire('Error', 'Failed to load specifications', 'error');
                return;
            }
            
            const specifications = response.data || [];
            
            if (specifications.length === 0) {
                Swal.fire({
                    title: 'No Specifications',
                    text: 'This quotation has no specifications yet. Please add specifications first.',
                    icon: 'warning',
                    confirmButtonText: 'Add Specifications'
                }).then((result) => {
                    if (result.isConfirmed) {
                        addSpecifications(quotation.id_quotation);
                    }
                });
                return;
            }
            
            // Populate SPK modal with data
            showSPKCreationModal(quotation, specifications);
        },
        error: function(xhr) {
            console.error('Error loading specifications:', xhr);
            Swal.fire('Error', 'Failed to load specifications', 'error');
        }
    });
}

// Function to show SPK creation modal
function showSPKCreationModal(quotation, specifications) {
    console.log('Showing SPK modal with specifications:', specifications);
    
    // Set modal title info
    $('#spkModalQuotationInfo').html(`
        <strong>Quotation:</strong> ${quotation.quotation_number || 'N/A'} | 
        <strong>Customer:</strong> ${quotation.prospect_name || 'N/A'}
    `);
    
    // Populate customer details section
    $('#spkCustomerDetails').html(`
        <div class="col-md-6">
            <small class="text-muted">Company</small>
            <div class="fw-bold">${quotation.customer_name || quotation.prospect_name || '-'}</div>
        </div>
        <div class="col-md-6">
            <small class="text-muted">Location</small>
            <div class="fw-bold">${quotation.location_name || '-'}</div>
        </div>
        <div class="col-md-6">
            <small class="text-muted">PIC / Contact</small>
            <div>${quotation.pic_name || '-'} ${quotation.pic_phone ? '/ ' + quotation.pic_phone : ''}</div>
        </div>
        <div class="col-md-6">
            <small class="text-muted">Contract Number</small>
            <div class="fw-bold text-primary">${quotation.contract_number || '-'}</div>
        </div>
    `);
    
    // Determine the correct contract ID from available fields
    const contractId = quotation.created_contract_id || quotation.contract_id || quotation.id_kontrak;
    
    // Set hidden fields
    $('#spk_quotation_id').val(quotation.id_quotation);
    $('#spk_customer_id').val(quotation.created_customer_id);
    $('#spk_contract_id').val(contractId);
    
    // Debug log
    console.log('📋 Setting SPK form fields:');
    console.log('  - quotation_id:', quotation.id_quotation);
    console.log('  - customer_id:', quotation.created_customer_id);
    console.log('  - contract_id:', contractId);
    
    // Set default delivery date (today + 7 days)
    const deliveryDate = new Date();
    deliveryDate.setDate(deliveryDate.getDate() + 7);
    $('#spk_delivery_date').val(deliveryDate.toISOString().split('T')[0]);
    
    // Build specifications list with checkboxes
    let specsHTML = '';
    
    specifications.forEach((spec, index) => {
        const specId = spec.id_specification || spec.id;
        
        // Use database specification_name and specification_description
        let specTitle = spec.specification_name || `Specification #${index + 1}`;
        
        // Determine if this is UNIT or ATTACHMENT based on category
        const specType = spec.category && spec.category.toLowerCase().includes('attachment') ? 'ATTACHMENT' : 'UNIT';
        
        // Use specification_description if available, otherwise build from details
        const specDescription = spec.specification_description && spec.specification_description.trim() !== '' 
            ? spec.specification_description 
            : buildSpecificationDescription(spec);
        
        const maxQty = parseInt(spec.quantity) || 1;
        const existingUnits = parseInt(spec.existing_spk_units) || 0;
        // IMPORTANT: Use 0 if available_units is 0, don't fallback to maxQty!
        const availableUnits = spec.available_units !== undefined && spec.available_units !== null 
            ? parseInt(spec.available_units) 
            : parseInt(spec.quantity) || 1;
        const isFullyCreated = availableUnits <= 0;
        
        // Debug logging
        console.log(`Spec ${specId}:`, {
            maxQty,
            existingUnits,
            availableUnits,
            isFullyCreated,
            rawData: {
                quantity: spec.quantity,
                existing_spk_units: spec.existing_spk_units,
                available_units: spec.available_units
            }
        });
        
        specsHTML += `
            <div class="card mb-2 border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check">
                            <input class="form-check-input spec-checkbox" type="checkbox" 
                                   id="spec_${specId}" name="specifications[]" value="${specId}" 
                                   data-max-qty="${availableUnits}"
                                   ${isFullyCreated ? 'disabled' : ''}>
                            <label class="form-check-label fw-bold ${isFullyCreated ? 'text-muted' : ''}" for="spec_${specId}">
                                ${specTitle}
                            </label>
                        </div>
                        <span class="badge bg-${specType === 'UNIT' ? 'primary' : 'success'}">${specType}</span>
                    </div>
                    
                    <div class="row g-2 small">
                        <div class="col-md-3">
                            <strong>Department:</strong> ${spec.nama_departemen || '-'}
                        </div>
                        <div class="col-md-3">
                            <strong>Unit Type:</strong> ${spec.jenis_tipe_unit || spec.nama_tipe_unit || '-'}
                        </div>
                        <div class="col-md-3">
                            <strong>Brand & Model:</strong> ${spec.merk_unit || '-'} ${spec.model_unit ? '- ' + spec.model_unit : ''}
                        </div>
                        <div class="col-md-3">
                            <strong>Capacity:</strong> ${spec.nama_kapasitas || '-'}
                        </div>
                    </div>
                    
                    ${spec.jenis_baterai || spec.attachment_tipe || (spec.merk_charger && spec.tipe_charger) ? `
                    <div class="row g-2 small mt-2">
                        ${spec.jenis_baterai ? `
                        <div class="col-md-4">
                            <strong>Battery:</strong> ${spec.jenis_baterai}
                        </div>
                        ` : ''}
                        ${spec.merk_charger && spec.tipe_charger ? `
                        <div class="col-md-4">
                            <strong>Charger:</strong> ${spec.merk_charger} - ${spec.tipe_charger}
                        </div>
                        ` : ''}
                        ${spec.attachment_tipe || spec.attachment_merk ? `
                        <div class="col-md-4">
                            <strong>Attachment:</strong> ${spec.attachment_tipe || ''}${spec.attachment_tipe && spec.attachment_merk ? ' - ' : ''}${spec.attachment_merk || ''}
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}
                    
                    ${spec.valve_name || spec.mast_name || spec.tire_name || spec.wheel_name ? `
                    <div class="row g-2 small mt-2">
                        ${spec.valve_name ? `
                        <div class="col-md-3">
                            <strong>Valve:</strong> ${spec.valve_name}
                        </div>
                        ` : ''}
                        ${spec.mast_name ? `
                        <div class="col-md-3">
                            <strong>Mast:</strong> ${spec.mast_name}
                        </div>
                        ` : ''}
                        ${spec.tire_name ? `
                        <div class="col-md-3">
                            <strong>Tire:</strong> ${spec.tire_name}
                        </div>
                        ` : ''}
                        ${spec.wheel_name ? `
                        <div class="col-md-3">
                            <strong>Wheel:</strong> ${spec.wheel_name}
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}
                    
                    ${spec.unit_accessories && spec.unit_accessories.trim() !== '' ? `
                    <div class="mt-2 small">
                        <strong>Accessories:</strong> 
                        ${spec.unit_accessories.split(',').map(acc => `<span class="badge bg-info text-dark me-1">${acc.trim()}</span>`).join('')}
                    </div>
                    ` : ''}
                    
                    <div class="row g-2 small mt-2">
                        <div class="col-md-6">
                            <strong>Price:</strong> 
                            ${spec.monthly_price || spec.unit_price || spec.harga_per_unit ? 
                                `Rp ${parseFloat(spec.monthly_price || spec.unit_price || spec.harga_per_unit).toLocaleString('id-ID')}/month` : ''}
                            ${spec.daily_price || spec.harga_per_unit_harian ? 
                                ` | Rp ${parseFloat(spec.daily_price || spec.harga_per_unit_harian).toLocaleString('id-ID')}/day` : ''}
                        </div>
                        <div class="col-md-6">
                            <strong>Qty:</strong> ${spec.quantity} unit(s)
                            ${existingUnits > 0 ? `<span class="text-warning ms-2">(${existingUnits} have SPK)</span>` : ''}
                        </div>
                    </div>
                    
                    ${!isFullyCreated ? `
                    <div class="row g-2 mt-2 align-items-center">
                        <div class="col-auto">
                            <small class="text-muted">Select quantity:</small>
                        </div>
                        <div class="col-auto">
                            <input type="number" class="form-control form-control-sm spec-quantity" 
                                   id="qty_${specId}" name="quantities[${specId}]" 
                                   min="1" max="${availableUnits}" value="${availableUnits}" 
                                   style="width: 80px;" disabled>
                        </div>
                        <div class="col-auto">
                            <small class="text-muted">of ${availableUnits} available</small>
                        </div>
                    </div>
                    ` : `
                    <div class="alert alert-secondary py-2 mt-2 mb-0">
                        <i class="fas fa-check-circle me-2"></i>All ${maxQty} unit(s) already have SPK
                    </div>
                    `}
                </div>
            </div>
        `;
    });
    
    if (specsHTML === '') {
        specsHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>No specifications available</div>';
    }
    
    $('#spkSpecificationsList').html(specsHTML);
    
    // Add checkbox change handler
    $('.spec-checkbox').on('change', function() {
        const specId = $(this).val();
        const qtyInput = $(`#qty_${specId}`);
        
        if ($(this).is(':checked')) {
            qtyInput.prop('disabled', false).focus();
        } else {
            qtyInput.prop('disabled', true);
        }
    });
    
    // Show modal
    $('#createSPKModal').modal('show');
}

// Helper function to build specification description
function buildSpecificationDescription(spec) {
    let parts = [];
    
    // Basic Info - Department and Unit Type
    if (spec.nama_departemen) {
        parts.push(`<strong>Department:</strong> ${spec.nama_departemen}`);
    }
    
    if (spec.jenis_tipe_unit) {
        parts.push(`<strong>Unit Type:</strong> ${spec.jenis_tipe_unit}`);
    } else if (spec.nama_tipe_unit) {
        parts.push(`<strong>Unit Type:</strong> ${spec.nama_tipe_unit}`);
    }
    // Capacity
    if (spec.nama_kapasitas) {
        parts.push(`<strong>Capacity:</strong> ${spec.nama_kapasitas}`);
    }
    
    // Brand and Model
    if (spec.merk_unit) {
        parts.push(`<strong>Brand:</strong> ${spec.merk_unit}`);
    }
    if (spec.model_unit && spec.model_unit.trim() !== '') {
        parts.push(`<strong>Model:</strong> ${spec.model_unit}`);
    }
    
    // Electric Specific - Battery and Charger
    const isElectric = spec.nama_departemen && 
                      (spec.nama_departemen.toLowerCase().includes('electric') || 
                       spec.nama_departemen.toLowerCase().includes('listrik'));
    
    if (isElectric) {
        if (spec.jenis_baterai) {
            parts.push(`<strong>Battery:</strong> ${spec.jenis_baterai}`);
        }
        // Charger info could be added here if available in spec object
    }
    
    // Technical Specs
    const techSpecs = [];
    if (spec.valve_id) techSpecs.push(`Valve`);
    if (spec.mast_id) techSpecs.push(`Mast`);
    if (spec.ban_id) techSpecs.push(`Tire`);
    if (spec.roda_id) techSpecs.push(`Wheel`);
    
    if (techSpecs.length > 0) {
        parts.push(`<strong>Tech:</strong> ${techSpecs.join(', ')}`);
    }
    
    // Attachment details (if ATTACHMENT type)
    if (spec.attachment_tipe) {
        parts.push(`<strong>Attachment:</strong> ${spec.attachment_tipe}`);
    }
    if (spec.attachment_merk) {
        parts.push(`<strong>Att. Brand:</strong> ${spec.attachment_merk}`);
    }
    
    // Accessories
    if (spec.unit_accessories && spec.unit_accessories.trim() !== '') {
        const accessories = spec.unit_accessories.split(',').map(a => a.trim());
        if (accessories.length > 0) {
            parts.push(`<strong>Accessories:</strong> ${accessories.slice(0, 3).join(', ')}${accessories.length > 3 ? '...' : ''}`);
        }
    }
    
    // Pricing Info
    const prices = [];
    if (spec.monthly_price || spec.unit_price || spec.harga_per_unit) {
        const monthlyPrice = spec.monthly_price || spec.unit_price || spec.harga_per_unit;
        prices.push(`Monthly: Rp ${parseFloat(monthlyPrice).toLocaleString('id-ID')}`);
    }
    if (spec.daily_price || spec.harga_per_unit_harian) {
        const dailyPrice = spec.daily_price || spec.harga_per_unit_harian;
        prices.push(`Daily: Rp ${parseFloat(dailyPrice).toLocaleString('id-ID')}`);
    }
    
    if (prices.length > 0) {
        parts.push(`<strong>Price:</strong> ${prices.join(' | ')}`);
    }
    
    // Quantity in quotation
    if (spec.quantity) {
        parts.push(`<strong>Qty:</strong> ${spec.quantity} unit(s)`);
    }
    
    return parts.length > 0 ? parts.join(' • ') : 'No details available';
}

// Handle SPK creation form submission
$('#createSPKForm').on('submit', function(e) {
    e.preventDefault();
    
    // Get selected specifications
    const selectedSpecs = [];
    $('.spec-checkbox:checked').each(function() {
        const specId = $(this).val();
        const quantity = parseInt($(`#qty_${specId}`).val());
        const maxQty = parseInt($(this).data('max-qty'));
        
        if (quantity > maxQty) {
            Swal.fire('Error', `Quantity for specification #${specId} exceeds maximum (${maxQty})`, 'error');
            return false;
        }
        
        selectedSpecs.push({
            specification_id: specId,
            quantity: quantity
        });
    });
    
    if (selectedSpecs.length === 0) {
        Swal.fire('Warning', 'Please select at least one specification', 'warning');
        return false;
    }
    
    // Prepare form data
    const formData = {
        quotation_id: $('#spk_quotation_id').val(),
        customer_id: $('#spk_customer_id').val(),
        contract_id: $('#spk_contract_id').val(),
        delivery_date: $('#spk_delivery_date').val(),
        specifications: selectedSpecs
    };
    
    console.log('=== SPK Form Data Debug ===');
    console.log('Quotation ID:', formData.quotation_id);
    console.log('Customer ID:', formData.customer_id);
    console.log('Contract ID:', formData.contract_id);
    console.log('Delivery Date:', formData.delivery_date);
    console.log('Specifications:', formData.specifications);
    console.log('===========================');
    
    // Validate required fields
    if (!formData.quotation_id || !formData.customer_id || !formData.contract_id) {
        Swal.fire('Error', 'Missing quotation, customer, or contract ID. Please close and reopen the modal.', 'error');
        submitBtn.prop('disabled', false).html('Create Selected SPK(s)');
        return false;
    }
    
    // Disable submit button
    const submitBtn = $('#submitSPKBtn');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...');
    
    // Submit to backend
    $.ajax({
        url: '<?= base_url('marketing/spk/createFromQuotation') ?>',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(formData),
        success: function(response) {
            submitBtn.prop('disabled', false).html('<i class="fas fa-check me-2"></i>Create Selected SPK(s)');
            
            if (response.success) {
                $('#createSPKModal').modal('hide');
                
                const spkCount = response.spk_count || 1;
                const spkNumbers = response.spk_numbers || [];
                const allAllocated = response.all_specs_allocated || false;
                const statusUpdated = response.status_updated || false;
                
                let message = `${spkCount} SPK(s) created successfully!`;
                if (spkNumbers.length > 0) {
                    message += `\n\nSPK Numbers: ${spkNumbers.join(', ')}`;
                }
                
                // Add status update notification if all specs are allocated
                if (statusUpdated && allAllocated) {
                    message += '\n\n✅ All specifications completed!\nQuotation marked as CLOSED.';
                }
                
                Swal.fire({
                    title: 'Success!',
                    text: message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Refresh table if needed
                    if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                        quotationsTable.ajax.reload(null, false);
                    }
                });
            } else {
                Swal.fire('Error', response.message || 'Failed to create SPK', 'error');
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).html('<i class="fas fa-check me-2"></i>Create Selected SPK(s)');
            console.error('Error creating SPK:', xhr);
            Swal.fire('Error', 'Failed to create SPK: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
    });
});


// Global Date Range Picker Callbacks for Quotations
// Note: Callbacks are now handled by setupDataTableDateFilter() mixin
// No need for manual callback definitions anymore!


// Third $(document).ready() block removed - merged into main block above
</script>

<?= $this->endSection() ?>