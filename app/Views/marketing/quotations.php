<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
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

<!-- Add Specification Modal -->
<div class="modal fade" id="addSpecificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-muted">
                <h6 class="modal-title fw-600">
                    <i class="fas fa-cogs me-2"></i>Add Unit Specification
                </h6>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSpecificationForm" method="post" action="javascript:void(0)">
                <div class="modal-body">
                    <input type="hidden" name="id_quotation" id="specQuotationId">
                    
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
                            <label class="form-label">Monthly Rental Price <span class="text-danger" id="hargaRequired">*</span></label>
                            <input type="number" class="form-control" name="unit_price" step="0.01" placeholder="Rp per unit per month" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Daily Rental Price</label>
                            <input type="number" class="form-control" name="harga_per_unit_harian" step="0.01" placeholder="Rp per unit per day">
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
                            <select class="form-select" name="merk_unit" id="specMerkUnit">
                                <option value="">-- Select Brand --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Battery Type</label>
                            <select class="form-select" name="jenis_baterai" id="specJenisBaterai">
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
                            <select class="form-select" name="attachment_tipe" id="specAttachmentTipe"></select>
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
                        <i class="fas fa-save me-1"></i>Save Specification
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
                        <h6 class="mb-3">Contract Details</h6>
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
    // Initialize DataTable
    quotationsTable = $('#quotationsTable').DataTable({
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
    });
    
    // Add row click functionality
    $('#quotationsTable tbody').on('click', 'tr', function(e) {
        // Don't trigger row click if user clicked on action buttons
        if (!$(e.target).closest('.btn, .dropdown').length) {
            var data = quotationsTable.row(this).data();
            if (data && data.id_quotation) {
                viewQuotation(data.id_quotation);
            }
        }
    });

    // Load statistics
    loadStatistics();

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
            console.log('Stored selected location ID:', window.currentSelectedLocationId);
            
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
        
        console.log('Contract dropdown changed:', selectedValue);
        
        if (selectedValue === '__ADD_NEW__') {
            // Show empty form for new contract
            console.log('Add new contract selected');
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
            console.log('Existing contract selected, ID:', selectedValue);
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
                
                console.log('✅ All fields set to READ-ONLY for existing contract');
            }
            
        } else {
            // No selection - hide form
            console.log('No selection - hiding form');
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
            console.log('Linking existing contract:', selectedContractId);
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
    
    // Auto-run basic system test after page fully loads
    setTimeout(() => {
        console.log('Auto-running basic system test...');
        testWorkflowComplete();
    }, 2000);
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

function loadStatistics() {
    $.get('<?= base_url('marketing/quotations/stats') ?>', function(data) {
        $('#stat-total-quotations').text(data.total || 0);
        $('#stat-pending').text(data.pending || 0);
        $('#stat-approved').text(data.approved || 0);
        $('#stat-rejected').text(data.rejected || 0);
    }).fail(function() {
        console.error('Failed to load statistics');
    });
}

function viewQuotation(id) {
    // Set current quotation ID for specifications
    currentQuotationId = id;
    
    $.get('<?= base_url('marketing/quotations/get-quotation/') ?>' + id, function(response) {
        console.log('Response received:', response); // Debug log
        console.log('Response type:', typeof response); // Debug log
        console.log('Response status:', response.status); // Debug log
        
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
                    ${data.prospect_name || 'undefined'}<br><br>
                    <strong>Amount:</strong><br>
                    Rp ${data.total_amount ? parseFloat(data.total_amount).toLocaleString('id-ID') : 'NaN'}
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
    console.log('Tab switched to:', targetTab);
    
    // Clear any lingering active states
    $('#quotationDetailTabs .nav-link').removeClass('active').attr('aria-selected', 'false');
    $('.tab-pane').removeClass('show active');
    
    // Set correct active state for current tab
    $(e.target).addClass('active').attr('aria-selected', 'true');
    $(targetTab).addClass('show active');
});

// Event handler for specifications tab click
$(document).on('click', '#specifications-tab', function() {
    console.log('Specifications tab clicked, currentQuotationId:', currentQuotationId);
    
    if (!currentQuotationId) {
        console.warn('No currentQuotationId set');
        return;
    }
    
    // Check if tab is already loaded
    if ($(this).hasClass('loaded')) {
        console.log('Tab already loaded');
        return;
    }
    
    // Check quotation workflow stage before loading specifications
    $.get('<?= base_url('marketing/quotations/get-quotation/') ?>' + currentQuotationId, function(response) {
        console.log('Specifications API Response:', response);
        
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
    console.log('Loading specifications for quotation:', quotationId);
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
        const isAttachmentSpec = spec.attachment_tipe && (!spec.tipe_unit_id || spec.tipe_unit_id === '0');
        const cardClass = isAttachmentSpec ? 'border-success' : 'border-primary';
        const badgeClass = isAttachmentSpec ? 'bg-success' : 'bg-primary';
        const specType = isAttachmentSpec ? 'Attachment' : 'Unit';
        
        html += `
            <div class="card mb-3 ${cardClass}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <span class="badge ${badgeClass} me-2">${spec.spek_kode || 'QS-' + (index + 1)}</span>
                        <span class="badge bg-light text-dark me-2">${specType}</span>
                        ${spec.specification_name || 'Specification ' + (index + 1)}
                    </h6>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editSpecification(${spec.id_specification})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSpecification(${spec.id_specification})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <small class="text-muted">Quantity</small>
                            <div class="fw-bold">${spec.quantity || 0}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Unit Price</small>
                            <div class="fw-bold text-success">Rp ${formatNumber(spec.unit_price || 0)}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Total Price</small>
                            <div class="fw-bold text-primary">Rp ${formatNumber(spec.total_price || 0)}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Equipment Type</small>
                            <div>${spec.equipment_type || '-'}</div>
                        </div>
                        ${spec.brand ? `
                        <div class="col-md-4">
                            <small class="text-muted">Brand</small>
                            <div>${spec.brand}</div>
                        </div>
                        ` : ''}
                        ${spec.model ? `
                        <div class="col-md-4">
                            <small class="text-muted">Model</small>
                            <div>${spec.model}</div>
                        </div>
                        ` : ''}
                        ${spec.nama_departemen ? `
                        <div class="col-md-4">
                            <small class="text-muted">Department</small>
                            <div>${spec.nama_departemen}</div>
                        </div>
                        ` : ''}
                        ${spec.nama_tipe_unit ? `
                        <div class="col-md-4">
                            <small class="text-muted">Unit Type</small>
                            <div>${spec.nama_tipe_unit} ${spec.jenis ? `(${spec.jenis})` : ''}</div>
                        </div>
                        ` : ''}
                        ${spec.kapasitas ? `
                        <div class="col-md-4">
                            <small class="text-muted">Capacity</small>
                            <div>${spec.kapasitas}</div>
                        </div>
                        ` : ''}
                        ${spec.merk_charger && spec.tipe_charger ? `
                        <div class="col-md-4">
                            <small class="text-muted">Charger</small>
                            <div>${spec.merk_charger} - ${spec.tipe_charger}</div>
                        </div>
                        ` : ''}
                    </div>
                    ${spec.specification_description ? `
                    <div class="mt-2">
                        <small class="text-muted">Description:</small>
                        <div>${spec.specification_description}</div>
                    </div>
                    ` : ''}
                    ${spec.notes ? `
                    <div class="mt-2">
                        <small class="text-muted">Notes:</small>
                        <div>${spec.notes}</div>
                    </div>
                    ` : ''}
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
    // Reset form
    $('#addSpecificationForm')[0].reset();
    $('#specQuotationId').val(currentQuotationId);
    
    // Reset modal title and button text
    $('#addSpecificationModal .modal-title').text('Add Specification');
    $('#submitSpecificationBtn').text('Save Specification');
    
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
    
    // Test data loading after modal is shown
    setTimeout(() => {
        console.log('Testing data loading...');
        console.log('Window.allTipeUnitData:', window.allTipeUnitData);
        console.log('Departemen options count:', $('#specDepartemen option').length);
        console.log('TipeUnit options count:', $('#specTipeUnit option').length);
    }, 1000);
}

// Open add attachment modal
function openAddAttachmentModal() {
    if (!currentQuotationId) {
        Swal.fire('Warning', 'Please select a quotation first', 'warning');
        return;
    }
    
    // For now, show info message that this will be implemented
    Swal.fire('Info', 'Add Attachment functionality will be implemented soon', 'info');
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
    
    console.log('Unit Type selected:', selectedTipeUnit, selectedText);
    
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
    console.log('Loading departemen data...');
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=departemen', function(response) {
        console.log('Departemen API Response:', response);
        if (response.success) {
            let options = '<option value="">-- Select Department --</option>';
            response.data.forEach(dept => {
                options += `<option value="${dept.id}">${dept.name}</option>`;
            });
            $('#specDepartemen').html(options);
            console.log('Loaded', response.data.length, 'departments');
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
    console.log('Loading tipe unit data...');
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getTipeUnit') ?>',
        method: 'GET',
        success: function(response) {
            console.log('Tipe Unit API Response:', response);
            if (response.success) {
                // Store all unit type data globally for filtering
                window.allTipeUnitData = response.data;
                
                console.log('Loaded', response.data.length, 'tipe unit records');
                
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
    
    console.log('updateTipeUnitOptions called - Department:', selectedDept, selectedDeptText);
    
    select.empty().append('<option value="">-- Pilih Tipe Unit --</option>');
    
    if (!selectedDept || !window.allTipeUnitData) {
        console.log('No department selected or no tipe unit data available');
        if (!window.allTipeUnitData) {
            console.log('allTipeUnitData is not loaded yet, trying to load...');
            // Try to load tipe unit data if it's not loaded
            loadTipeUnitForSpecification();
        }
        return;
    }
    
    console.log('All Tipe Unit Data:', window.allTipeUnitData);
    
    // Filter and show only units for selected department
    const filteredUnits = window.allTipeUnitData.filter(unit => {
        console.log('Checking unit:', unit, 'department match:', unit.id_departemen == selectedDept);
        return unit.id_departemen == selectedDept;
    });
    
    console.log('Filtered Units for department', selectedDept + ':', filteredUnits);
    
    if (filteredUnits.length === 0) {
        select.append('<option value="">No unit types available for this department</option>');
        console.log('No units found for department:', selectedDept);
        return;
    }
    
    // Group by jenis to avoid duplicates
    const uniqueJenis = [...new Set(filteredUnits.map(unit => unit.jenis))];
    console.log('Unique jenis found:', uniqueJenis);
    
    uniqueJenis.sort().forEach(jenis => {
        // Find the first unit with this jenis to get the id
        const unitWithJenis = filteredUnits.find(unit => unit.jenis === jenis);
        if (unitWithJenis) {
            console.log('Adding option:', unitWithJenis.id_tipe_unit, jenis);
            select.append(`<option value="${unitWithJenis.id_tipe_unit}" data-dept="${selectedDept}">${jenis}</option>`);
        }
    });
    
    console.log('Updated Tipe Unit options count:', uniqueJenis.length);
    
    // Also clear dependent dropdowns when unit type changes
    $('#specKapasitas').html('<option value="">-- Select Capacity --</option>');
}

function loadKapasitasForSpecification() {
    console.log('Loading kapasitas data...');
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=kapasitas', function(response) {
        console.log('Kapasitas API Response:', response);
        if (response.success) {
            let options = '<option value="">-- Select Capacity --</option>';
            response.data.forEach(cap => {
                options += `<option value="${cap.id}">${cap.name}</option>`;
            });
            $('#specKapasitas').html(options);
            console.log('Loaded', response.data.length, 'capacities');
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
    const selectedDeptText = $('#specDepartemen option:selected').text().toLowerCase();
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    if (!isElectric) {
        $('#specCharger').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        return;
    }
    
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=charger', function(response) {
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
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=merk_unit', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Brand --</option>';
            response.data.forEach(brand => {
                options += `<option value="${brand.id}">${brand.name}</option>`;
            });
            $('#specMerkUnit').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load unit brands:', xhr.responseText);
        $('#specMerkUnit').html('<option value="">Error loading brands</option>');
    });
}

function loadBatteriesForSpecification() {
    const selectedDeptText = $('#specDepartemen option:selected').text().toLowerCase();
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    if (!isElectric) {
        $('#specJenisBaterai').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        return;
    }
    
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=jenis_baterai', function(response) {
        if (response.success) {
            let options = '<option value="">-- Pilih Baterai --</option>';
            response.data.forEach(battery => {
                options += `<option value="${battery.id}">${battery.name}</option>`;
            });
            $('#specJenisBaterai').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load batteries:', xhr.responseText);
        $('#specJenisBaterai').html('<option value="">Error loading batteries</option>');
    });
}

function loadAttachmentTypesForSpecification() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=attachment_tipe', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Attachment Type --</option>';
            response.data.forEach(att => {
                options += `<option value="${att.id}">${att.name}</option>`;
            });
            $('#specAttachmentTipe').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load attachment types:', xhr.responseText);
        $('#specAttachmentTipe').html('<option value="">Error loading attachments</option>');
    });
}

function loadValvesForSpecification() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=valve', function(response) {
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
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=mast', function(response) {
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
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=ban', function(response) {
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
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=roda', function(response) {
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

// Handle specification form submission
$('#addSpecificationForm').on('submit', function(e) {
    e.preventDefault();
    
    console.log('Specification form submitted');
    
    // Enhanced validation
    const quantity = parseInt($('#addSpecificationForm [name="quantity"]').val());
    const unitPrice = parseFloat($('#addSpecificationForm [name="unit_price"]').val());
    const departemen = $('#specDepartemen').val();
    const tipeUnit = $('#specTipeUnit').val();
    
    // Client-side validation
    if (!quantity || quantity < 1) {
        Swal.fire('Validation Error', 'Please enter a valid quantity (minimum 1)', 'warning');
        return;
    }
    
    if (!unitPrice || unitPrice < 0) {
        Swal.fire('Validation Error', 'Please enter a valid unit price', 'warning');
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
    
    // Log form data for debugging
    console.log('Form submission data:');
    for (let [key, value] of formData.entries()) {
        console.log(key, ':', value);
    }
    
    submitBtn.prop('disabled', true).text('Saving...');
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/add-specification') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Specification save response:', response);
            if (response.success) {
                $('#addSpecificationModal').modal('hide');
                Swal.fire('Success', response.message || 'Specification added successfully', 'success');
                
                // Reload specifications and refresh tab
                if (currentQuotationId) {
                    $('#specifications-tab').removeClass('loaded');
                    loadQuotationSpecifications(currentQuotationId);
                    
                    // Update quotation total if provided
                    if (response.quotation_total) {
                        console.log('Updated quotation total:', response.quotation_total);
                    }
                }
                
                // Reset form for next entry
                $('#addSpecificationForm')[0].reset();
            } else {
                Swal.fire('Error', response.message || 'Failed to add specification', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding specification:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            let errorMessage = 'Failed to add specification';
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
            submitBtn.prop('disabled', false).text('Save Specification');
        }
    });
});

// Edit specification
function editSpecification(specId) {
    // Implementation for editing specification
    Swal.fire('Info', 'Edit specification functionality will be implemented', 'info');
}

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
                error: function() {
                    Swal.fire('Error', 'Failed to delete specification', 'error');
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
            let errorMessage = 'Failed to create prospect';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
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
    // Get quotation details to extract customer ID
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
                        
                        // Customer exists, proceed with contract check
                        checkCustomerContracts(response.data.created_customer_id, quotationId, 'Please select or create a contract to proceed with SPK creation.');
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
function checkCustomerContracts(customerId, quotationId, message) {
    console.log('Checking customer contracts for customer:', customerId);
    
    // Directly show create/select contract modal (unified modal)
    showCreateContractModal(customerId, quotationId, message);
}

// Function to show create contract modal
function showCreateContractModal(customerId, quotationId, message) {
    console.log('showCreateContractModal called:', {customerId, quotationId, message});
    
    // Store in global variables for contract form
    window.currentContractQuotationId = quotationId;
    window.currentContractCustomerId = customerId;
    
    // Get customer name and set in modal title
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
    
    // Load customers and locations for the modal
    loadCustomersForContract(customerId);
    
    // Show the modal
    $('#addContractModal').modal('show');
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
    
    // Get customer details
    $.ajax({
        url: `<?= base_url('customers/get/') ?>${customerId}`,
        method: 'GET',
        success: function(custResponse) {
            console.log('Customer data loaded:', custResponse);
            if (custResponse.success && custResponse.data) {
                // Display customer name as read-only
                $('#customerNameDisplay').val(custResponse.data.customer_name);
                $('#customerIdContractNew').val(custResponse.data.id);
            }
        },
        error: function(xhr) {
            console.error('Error loading customer:', xhr);
            const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Unknown error';
            console.error('Customer error details:', errorMsg);
            Swal.fire({
                icon: 'error',
                title: 'Failed to Load Customer',
                text: 'Could not load customer data. Error: ' + errorMsg,
                timer: 3000
            });
        }
    });
    
    // Get selected location
    if (locationId) {
        $.ajax({
            url: `<?= base_url('customers/getLocations/') ?>${customerId}`,
            method: 'GET',
            success: function(locResponse) {
                console.log('Locations loaded:', locResponse);
                if (locResponse.success && locResponse.data) {
                    // Find the selected location
                    const selectedLocation = locResponse.data.find(loc => loc.id == locationId);
                    if (selectedLocation) {
                        $('#locationNameDisplay').val(selectedLocation.location_name);
                        $('#locationIdContractNew').val(selectedLocation.id);
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading locations:', xhr);
                const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Unknown error';
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Load Location',
                    text: 'Could not load location data. Error: ' + errorMsg,
                    timer: 3000
                });
            }
        });
    } else {
        console.warn('No location ID found in workflow');
        Swal.fire({
            icon: 'warning',
            title: 'No Location Selected',
            text: 'Please complete location selection first.',
            timer: 3000
        });
    }
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
                Swal.fire('Error', 'Contract must be linked before creating SPK. Please complete contract selection.', 'error');
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
        console.log('Loading test data for cascading...');
        loadDepartemenForSpecification();
        loadTipeUnitForSpecification();
    }, 500);
}

// Console commands for easy testing
console.log('=== OPTIMA QUOTATION WORKFLOW TESTING ===');
console.log('Available test commands:');
console.log('- testWorkflowComplete()     : Test all system components');
console.log('- simulateWorkflow()         : Simulate complete workflow with test data');
console.log('- testProspectCreation()     : Test prospect creation modal');
console.log('- testSpecificationModal()   : Test specification modal (need active quotation)');
console.log('- testCascadingDropdowns()   : Test dropdown cascading functionality');
console.log('=====================================');

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
                Swal.fire('Error', 'Contract must be linked first. Please complete contract selection.', 'error');
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
    
    // Set hidden fields
    $('#spk_quotation_id').val(quotation.id_quotation);
    $('#spk_customer_id').val(quotation.created_customer_id);
    $('#spk_contract_id').val(quotation.created_contract_id);
    
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
        
        const maxQty = spec.quantity || 1;
        const existingUnits = spec.existing_spk_units || 0;
        const availableUnits = spec.available_units || maxQty;
        const isFullyCreated = availableUnits <= 0;
        
        specsHTML += `
            <div class="specification-item border-bottom pb-3 mb-3">
                <div class="form-check">
                    <input class="form-check-input spec-checkbox" type="checkbox" 
                           id="spec_${specId}" name="specifications[]" value="${specId}" 
                           data-max-qty="${availableUnits}"
                           ${isFullyCreated ? 'disabled' : ''}>
                    <label class="form-check-label fw-bold ${isFullyCreated ? 'text-muted' : ''}" for="spec_${specId}">
                        ${specTitle} 
                        <span class="badge bg-${specType === 'UNIT' ? 'primary' : 'success'} ms-2">${specType}</span>
                        ${isFullyCreated ? '<span class="badge bg-secondary ms-2"><i class="fas fa-check-circle me-1"></i>All Units Have SPK</span>' : ''}
                    </label>
                </div>
                <div class="ms-4 mt-2">
                    <small class="text-muted d-block mb-2">${specDescription}</small>
                    
                    ${isFullyCreated ? `
                        <div class="alert alert-info py-2 mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>All ${maxQty} unit(s) already have SPK created.</strong> 
                            (Existing: ${existingUnits} unit(s))
                        </div>
                    ` : `
                        <!-- Quantity input -->
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <label class="col-form-label-sm">Quantity for SPK:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" class="form-control form-control-sm spec-quantity" 
                                       id="qty_${specId}" name="quantities[${specId}]" 
                                       min="1" max="${availableUnits}" value="${availableUnits}" 
                                       style="width: 100px;" disabled>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted">
                                    of ${availableUnits} available 
                                    ${existingUnits > 0 ? `<span class="text-warning">(${existingUnits} already have SPK)</span>` : ''}
                                </small>
                            </div>
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
    
    // Category info (shows department and capacity)
    if (spec.category) {
        parts.push(`<strong>Category:</strong> ${spec.category}`);
    }
    
    // Brand and Model
    if (spec.merk_unit) parts.push(`<strong>Brand:</strong> ${spec.merk_unit}`);
    if (spec.model_unit && spec.model_unit.trim() !== '') parts.push(`<strong>Model:</strong> ${spec.model_unit}`);
    
    // Attachment details
    if (spec.attachment_tipe) parts.push(`<strong>Attachment Type:</strong> ${spec.attachment_tipe}`);
    if (spec.attachment_merk) parts.push(`<strong>Attachment Brand:</strong> ${spec.attachment_merk}`);
    
    // Quantity in quotation
    if (spec.quantity) parts.push(`<strong>Quotation Qty:</strong> ${spec.quantity} unit(s)`);
    
    return parts.join(' | ') || 'No details available';
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
                
                let message = `${spkCount} SPK(s) created successfully!`;
                if (spkNumbers.length > 0) {
                    message += `\n\nSPK Numbers: ${spkNumbers.join(', ')}`;
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


// Third $(document).ready() block removed - merged into main block above
</script>

<?= $this->endSection() ?>