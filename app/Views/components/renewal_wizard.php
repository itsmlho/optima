<!-- ========================================================================
     RENEWAL WIZARD MODAL - 5-Step Contract Renewal Process
     Sprint 3: Advanced Features
     ======================================================================== -->

<!-- Renewal Wizard Modal -->
<div class="modal fade" id="renewalWizardModal" tabindex="-1" aria-labelledby="renewalWizardLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title" id="renewalWizardLabel">
                        <i class="fas fa-sync-alt me-2"></i>Contract Renewal Wizard
                    </h5>
                    <small class="text-white-50">Step-by-step renewal process with gap-free transition</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <!-- Progress Stepper -->
                <div class="renewal-stepper mb-4">
                    <div class="stepper-progress-bar">
                        <div class="stepper-progress" id="stepperProgress" style="width: 20%;"></div>
                    </div>
                    <div class="stepper-steps">
                        <div class="stepper-step active" data-step="1">
                            <div class="stepper-circle">1</div>
                            <div class="stepper-label">Select Contract</div>
                        </div>
                        <div class="stepper-step" data-step="2">
                            <div class="stepper-circle">2</div>
                            <div class="stepper-label">Review Terms</div>
                        </div>
                        <div class="stepper-step" data-step="3">
                            <div class="stepper-circle">3</div>
                            <div class="stepper-label">Unit Changes</div>
                        </div>
                        <div class="stepper-step" data-step="4">
                            <div class="stepper-circle">4</div>
                            <div class="stepper-label">Rate Adjustment</div>
                        </div>
                        <div class="stepper-step" data-step="5">
                            <div class="stepper-circle">5</div>
                            <div class="stepper-label">Confirmation</div>
                        </div>
                    </div>
                </div>

                <form id="renewalWizardForm">
                    <!-- Step 1: Select Contract -->
                    <div class="wizard-step" id="step1" style="display: block;">
                        <h5 class="mb-3"><i class="fas fa-search text-primary me-2"></i>Step 1: Select Contract to Renew</h5>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Gap-Free Transition:</strong> The new contract will start automatically on the next day after the old contract ends.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Expiring Contract <span class="text-danger">*</span></label>
                                <select class="form-select" id="renewalSourceContract" name="source_contract_id" required>
                                    <option value="">-- Select contract to renew --</option>
                                </select>
                                <small class="form-text text-muted">Only contracts expiring within 90 days are shown</small>
                            </div>
                        </div>
                        
                        <div id="contractPreview" class="card border-primary" style="display:none;">
                            <div class="card-header bg-primary bg-opacity-10">
                                <h6 class="mb-0 text-primary"><i class="fas fa-file-contract me-2"></i>Current Contract Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Contract Number:</dt>
                                            <dd class="col-sm-7" id="preview_contract_number">-</dd>
                                            
                                            <dt class="col-sm-5">Customer:</dt>
                                            <dd class="col-sm-7" id="preview_customer">-</dd>
                                            
                                            <dt class="col-sm-5">Start Date:</dt>
                                            <dd class="col-sm-7" id="preview_start_date">-</dd>
                                            
                                            <dt class="col-sm-5">End Date:</dt>
                                            <dd class="col-sm-7" id="preview_end_date">-</dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-6">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Total Units:</dt>
                                            <dd class="col-sm-7" id="preview_total_units">-</dd>
                                            
                                            <dt class="col-sm-5">Contract Value:</dt>
                                            <dd class="col-sm-7" id="preview_contract_value">-</dd>
                                            
                                            <dt class="col-sm-5">Billing Method:</dt>
                                            <dd class="col-sm-7" id="preview_billing_method">-</dd>
                                            
                                            <dt class="col-sm-5">Days Until Expiry:</dt>
                                            <dd class="col-sm-7">
                                                <span class="badge bg-warning" id="preview_days_remaining">-</span>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Review Terms -->
                    <div class="wizard-step" id="step2" style="display: none;">
                        <h5 class="mb-3"><i class="fas fa-file-contract text-primary me-2"></i>Step 2: Review & Set New Terms</h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">New Contract Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="renewal_contract_number" name="contract_number" required>
                                    <button class="btn btn-outline-secondary" type="button" id="generateRenewalContractNumber" title="Auto-generate">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Start Date (Auto-calculated) <span class="text-danger">*</span></label>
                                <input type="date" class="form-control bg-light" id="renewal_start_date" name="start_date" required readonly>
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>Gap-free: Next day after old contract ends
                                </small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="renewal_end_date" name="end_date" required>
                                <small class="text-muted">Duration: <span id="renewal_duration">0</span> days</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Billing Method</label>
                                <select class="form-select" id="renewal_billing_method" name="billing_method">
                                    <option value="CYCLE">30-Day Rolling Cycle</option>
                                    <option value="PRORATE">Prorate to Month-End</option>
                                    <option value="MONTHLY_FIXED">Fixed Monthly Date</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Rental Type</label>
                                <select class="form-select" id="renewal_rental_type" name="rental_type">
                                    <option value="CONTRACT">Formal Contract</option>
                                    <option value="PO_ONLY">PO-Based Only</option>
                                    <option value="DAILY_SPOT">Daily/Spot Rental</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Customer PO Number</label>
                                <input type="text" class="form-control" id="renewal_po_number" name="po_number" placeholder="Optional">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" id="renewal_notes" name="notes" rows="3" placeholder="Renewal notes or special instructions"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Unit Changes -->
                    <div class="wizard-step" id="step3" style="display: none;">
                        <h5 class="mb-3"><i class="fas fa-exchange-alt text-primary me-2"></i>Step 3: Unit Changes (Optional)</h5>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            By default, all units from the old contract will be carried over. You can add, remove, or replace units below.
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Current Units in Old Contract</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover" id="currentUnitsTable">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAllUnits" checked></th>
                                                <th>Unit Number</th>
                                                <th>Type</th>
                                                <th>Current Rate</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-success">
                            <div class="card-header bg-success bg-opacity-10">
                                <h6 class="mb-0 text-success">
                                    <i class="fas fa-plus-circle me-2"></i>Add New Units (Expansion)
                                </h6>
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-success btn-sm" id="addNewUnitBtn">
                                    <i class="fas fa-plus me-2"></i>Add Unit
                                </button>
                                <div id="newUnitsContainer" class="mt-3">
                                    <!-- New units will be added here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Rate Adjustment -->
                    <div class="wizard-step" id="step4" style="display: none;">
                        <h5 class="mb-3"><i class="fas fa-calculator text-primary me-2"></i>Step 4: Rate Adjustment</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="applyRateIncrease">
                                    <label class="form-check-label" for="applyRateIncrease">
                                        Apply rate increase to all units
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div id="rateIncreaseSection" style="display:none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Increase Type</label>
                                    <select class="form-select" id="rateIncreaseType">
                                        <option value="percentage">Percentage (%)</option>
                                        <option value="fixed">Fixed Amount (Rp)</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Increase Value</label>
                                    <input type="number" class="form-control" id="rateIncreaseValue" step="0.01" placeholder="e.g., 5 for 5%">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-primary w-100" id="applyRateBtn">
                                        <i class="fas fa-check me-2"></i>Apply to All Units
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Unit Rates Preview</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover" id="rateAdjustmentTable">
                                        <thead>
                                            <tr>
                                                <th>Unit</th>
                                                <th>Old Rate</th>
                                                <th>New Rate</th>
                                                <th>Change</th>
                                                <th>Custom Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Confirmation -->
                    <div class="wizard-step" id="step5" style="display: none;">
                        <h5 class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Step 5: Confirmation & Submit</h5>
                        
                        <div class="alert alert-success">
                            <h6 class="alert-heading">
                                <i class="fas fa-check-circle me-2"></i>Renewal Summary
                            </h6>
                            <p class="mb-0">Please review all details before confirming the renewal.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary mb-3">
                                    <div class="card-header bg-primary bg-opacity-10">
                                        <h6 class="mb-0 text-primary">Old Contract</h6>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Contract No:</dt>
                                            <dd class="col-sm-7" id="confirm_old_contract">-</dd>
                                            
                                            <dt class="col-sm-5">Period:</dt>
                                            <dd class="col-sm-7" id="confirm_old_period">-</dd>
                                            
                                            <dt class="col-sm-5">Units:</dt>
                                            <dd class="col-sm-7" id="confirm_old_units">-</dd>
                                            
                                            <dt class="col-sm-5">Value:</dt>
                                            <dd class="col-sm-7" id="confirm_old_value">-</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-success mb-3">
                                    <div class="card-header bg-success bg-opacity-10">
                                        <h6 class="mb-0 text-success">New Contract</h6>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Contract No:</dt>
                                            <dd class="col-sm-7" id="confirm_new_contract">-</dd>
                                            
                                            <dt class="col-sm-5">Period:</dt>
                                            <dd class="col-sm-7" id="confirm_new_period">-</dd>
                                            
                                            <dt class="col-sm-5">Units:</dt>
                                            <dd class="col-sm-7" id="confirm_new_units">-</dd>
                                            
                                            <dt class="col-sm-5">Value:</dt>
                                            <dd class="col-sm-7" id="confirm_new_value">-</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-info">
                            <div class="card-header bg-info bg-opacity-10">
                                <h6 class="mb-0 text-info"><i class="fas fa-exchange-alt me-2"></i>Changes Summary</h6>
                            </div>
                            <div class="card-body">
                                <div id="changesSummary">
                                    <!-- Populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="confirmRenewal" required>
                            <label class="form-check-label" for="confirmRenewal">
                                I confirm that all information is correct and I want to proceed with the renewal
                            </label>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="parent_contract_id" id="parent_contract_id">
                    <input type="hidden" name="customer_id" id="renewal_customer_id">
                    <input type="hidden" name="location_id" id="renewal_location_id">
                    <input type="hidden" name="is_renewal" value="1">
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="wizardPrevBtn" style="display:none;">
                    <i class="fas fa-arrow-left me-2"></i>Previous
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="wizardNextBtn">
                    Next<i class="fas fa-arrow-right ms-2"></i>
                </button>
                <button type="button" class="btn btn-success" id="wizardSubmitBtn" style="display:none;">
                    <i class="fas fa-check me-2"></i>Confirm Renewal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Renewal Wizard Styles -->
<style>
.renewal-stepper {
    position: relative;
    margin-bottom: 2rem;
}

.stepper-progress-bar {
    position: absolute;
    top: 20px;
    left: 10%;
    right: 10%;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
}

.stepper-progress {
    height: 100%;
    background: linear-gradient(90deg, #0d6efd 0%, #0dcaf0 100%);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.stepper-steps {
    display: flex;
    justify-content: space-between;
    position: relative;
    z-index: 1;
}

.stepper-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
}

.stepper-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #fff;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #6c757d;
    transition: all 0.3s ease;
}

.stepper-step.active .stepper-circle {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
}

.stepper-step.completed .stepper-circle {
    background: #198754;
    border-color: #198754;
    color: #fff;
}

.stepper-step.completed .stepper-circle::before {
    content: '\f00c';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

.stepper-label {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    font-weight: 500;
    color: #6c757d;
    text-align: center;
}

.stepper-step.active .stepper-label {
    color: #0d6efd;
    font-weight: 600;
}

.stepper-step.completed .stepper-label {
    color: #198754;
}

.wizard-step {
    min-height: 400px;
}
</style>
