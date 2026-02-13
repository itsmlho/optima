<!-- ========================================================================
     ADDENDUM PRORATE SPLIT COMPONENT
     Sprint 3: Advanced Features
     
     Visualizes mid-period rate changes with automatic prorate calculation
     Shows split billing: Part 1 (old rate) + Part 2 (new rate)
     ======================================================================== -->

<!-- Addendum Prorate Split Modal -->
<div class="modal fade" id="addendumProrateModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-calculator me-2"></i>Addendum Prorate Split Calculator
                    </h5>
                    <small class="text-white-50">Mid-period rate adjustment with automatic proration</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <form id="addendumProrateForm">
                    <!-- Contract Selection -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Select Contract <span class="text-danger">*</span></label>
                            <select class="form-select" id="prorateContractId" name="contract_id" required>
                                <option value="">-- Select active contract --</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Current Billing Period Display -->
                    <div class="card border-info mb-3" id="currentPeriodCard" style="display:none;">
                        <div class="card-header bg-info bg-opacity-10">
                            <h6 class="mb-0 text-info">
                                <i class="fas fa-calendar-alt me-2"></i>Current Billing Period
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Period Start</small>
                                    <strong id="display_period_start">-</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Period End</small>
                                    <strong id="display_period_end">-</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Total Days</small>
                                    <strong id="display_total_days">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Amendment Details -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Effective Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="prorateEffectiveDate" name="effective_date" required>
                            <small class="text-muted">Date when new rate takes effect</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <select class="form-select" id="prorateReason" name="reason" required>
                                <option value="">-- Select reason --</option>
                                <option value="RATE_INCREASE">Rate Increase</option>
                                <option value="RATE_DECREASE">Rate Decrease</option>
                                <option value="UNIT_CHANGE">Unit Change</option>
                                <option value="SPECIAL_DISCOUNT">Special Discount</option>
                                <option value="MARKET_ADJUSTMENT">Market Adjustment</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Unit Rate Changes -->
                    <div class="card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Unit Rate Changes</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="applyBulkRateChange">
                                <i class="fas fa-percentage me-1"></i>Apply Bulk Change
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="prorateUnitsTable">
                                    <thead>
                                        <tr>
                                            <th>Unit Number</th>
                                            <th>Current Rate</th>
                                            <th>New Rate <span class="text-danger">*</span></th>
                                            <th>Change</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Prorate Split Visualization -->
                    <div class="card border-success" id="prorateSplitVisualization" style="display:none;">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="mb-0 text-success">
                                <i class="fas fa-chart-pie me-2"></i>Prorate Split Calculation
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Timeline Visualization -->
                            <div class="prorate-timeline mb-4">
                                <div class="timeline-header">
                                    <span id="timeline_start_date">-</span>
                                    <span class="timeline-divider" id="timeline_effective_date">-</span>
                                    <span id="timeline_end_date">-</span>
                                </div>
                                <div class="timeline-bar">
                                    <div class="timeline-segment timeline-old" id="timeline_old_segment">
                                        <span class="segment-label">Part 1: Old Rate</span>
                                        <span class="segment-days" id="timeline_old_days">0 days</span>
                                    </div>
                                    <div class="timeline-segment timeline-new" id="timeline_new_segment">
                                        <span class="segment-label">Part 2: New Rate</span>
                                        <span class="segment-days" id="timeline_new_days">0 days</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Calculation Breakdown -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="calc-card calc-old">
                                        <h6 class="calc-title">
                                            <i class="fas fa-history me-2"></i>Part 1: Before Amendment
                                        </h6>
                                        <dl class="row mb-0">
                                            <dt class="col-sm-6">Days:</dt>
                                            <dd class="col-sm-6" id="calc_old_days">-</dd>
                                            
                                            <dt class="col-sm-6">Rate (Old):</dt>
                                            <dd class="col-sm-6" id="calc_old_rate">-</dd>
                                            
                                            <dt class="col-sm-6">Calculation:</dt>
                                            <dd class="col-sm-6 small" id="calc_old_formula">-</dd>
                                            
                                            <dt class="col-sm-6 fw-bold">Amount:</dt>
                                            <dd class="col-sm-6 fw-bold text-primary" id="calc_old_amount">-</dd>
                                        </dl>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="calc-card calc-new">
                                        <h6 class="calc-title">
                                            <i class="fas fa-star me-2"></i>Part 2: After Amendment
                                        </h6>
                                        <dl class="row mb-0">
                                            <dt class="col-sm-6">Days:</dt>
                                            <dd class="col-sm-6" id="calc_new_days">-</dd>
                                            
                                            <dt class="col-sm-6">Rate (New):</dt>
                                            <dd class="col-sm-6" id="calc_new_rate">-</dd>
                                            
                                            <dt class="col-sm-6">Calculation:</dt>
                                            <dd class="col-sm-6 small" id="calc_new_formula">-</dd>
                                            
                                            <dt class="col-sm-6 fw-bold">Amount:</dt>
                                            <dd class="col-sm-6 fw-bold text-success" id="calc_new_amount">-</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Total Summary -->
                            <div class="calc-total mt-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Total Days</small>
                                        <strong id="total_days">-</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Billing Amount</small>
                                        <strong class="text-primary" id="total_amount">-</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">vs. Full Month (Old Rate)</small>
                                        <strong id="comparison_amount">-</strong>
                                        <span class="badge ms-2" id="comparison_badge">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Amendment Notes</label>
                            <textarea class="form-control" id="prorateNotes" name="notes" rows="3" placeholder="Additional notes or justification for rate change"></textarea>
                        </div>
                    </div>
                    
                    <input type="hidden" name="period_start" id="hidden_period_start">
                    <input type="hidden" name="period_end" id="hidden_period_end">
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="calculateProrateBtn">
                    <i class="fas fa-calculator me-2"></i>Calculate
                </button>
                <button type="button" class="btn btn-success" id="submitAddendumBtn" style="display:none;">
                    <i class="fas fa-check me-2"></i>Create Amendment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Rate Change Modal -->
<div class="modal fade" id="bulkRateChangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply Bulk Rate Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Change Type</label>
                        <select class="form-select" id="bulkChangeType">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (Rp)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Value</label>
                        <input type="number" class="form-control" id="bulkChangeValue" step="0.01" placeholder="e.g., 10 for 10%">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyBulkBtn">Apply</button>
            </div>
        </div>
    </div>
</div>

<!-- Prorate Split Styles -->
<style>
.prorate-timeline {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #495057;
}

.timeline-divider {
    color: #dc3545;
    position: relative;
}

.timeline-divider::before {
    content: '▼';
    position: absolute;
    top: -8px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 1.2rem;
}

.timeline-bar {
    display: flex;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-segment {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 0.5rem;
    color: white;
    transition: all 0.3s ease;
}

.timeline-old {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.timeline-new {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.segment-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.segment-days {
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.calc-card {
    padding: 1rem;
    border-radius: 8px;
    border: 2px solid;
    margin-bottom: 1rem;
}

.calc-old {
    border-color: #6c757d;
    background: #f8f9fa;
}

.calc-new {
    border-color: #28a745;
    background: #d4edda;
}

.calc-title {
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid;
    font-weight: 600;
}

.calc-old .calc-title {
    border-color: #6c757d;
    color: #495057;
}

.calc-new .calc-title {
    border-color: #28a745;
    color: #155724;
}

.calc-total {
    padding: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    color: white;
}

.calc-total strong {
    font-size: 1.1rem;
}
</style>
