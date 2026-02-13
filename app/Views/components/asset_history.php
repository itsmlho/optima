<!-- 
    Asset History Unified View Component
    Sprint 3: Advanced Features
    
    Purpose: Visualize complete asset lifecycle including contracts, amendments, renewals
    Features: 
    - Contract timeline visualization
    - Unit journey tracking (which contracts unit served)
    - Rate change history per unit
    - Amendment history with prorate visualizations
    - Renewal chains (parent → child contracts)
    - Interactive timeline with clickable milestones
-->

<!-- Asset History Modal -->
<div class="modal fade" id="assetHistoryModal" tabindex="-1" aria-labelledby="assetHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="assetHistoryModalLabel">
                    <i class="fas fa-history"></i> Asset History & Timeline
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                
                <!-- View Type Selector -->
                <ul class="nav nav-tabs mb-3" id="historyViewTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="contractView-tab" data-bs-toggle="tab" data-bs-target="#contractView" type="button" role="tab">
                            <i class="fas fa-file-contract"></i> Contract Timeline
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="unitView-tab" data-bs-toggle="tab" data-bs-target="#unitView" type="button" role="tab">
                            <i class="fas fa-truck"></i> Unit Journey
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renewalView-tab" data-bs-toggle="tab" data-bs-target="#renewalView" type="button" role="tab">
                            <i class="fas fa-sync-alt"></i> Renewal Chains
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rateView-tab" data-bs-toggle="tab" data-bs-target="#rateView" type="button" role="tab">
                            <i class="fas fa-chart-line"></i> Rate History
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="historyViewContent">
                    
                    <!-- CONTRACT TIMELINE VIEW -->
                    <div class="tab-pane fade show active" id="contractView" role="tabpanel">
                        <!-- Contract Selector -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Contract</label>
                                <select class="form-select" id="historyContractId">
                                    <option value="">-- Select contract --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Filter By</label>
                                <select class="form-select" id="contractEventFilter">
                                    <option value="all">All Events</option>
                                    <option value="amendments">Amendments Only</option>
                                    <option value="renewals">Renewals Only</option>
                                    <option value="units">Unit Changes</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Timeline Visualization -->
                        <div id="contractTimeline">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-clock fa-3x mb-3"></i>
                                <p>Select a contract to view its timeline</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- UNIT JOURNEY VIEW -->
                    <div class="tab-pane fade" id="unitView" role="tabpanel">
                        <!-- Unit Selector -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Unit</label>
                                <select class="form-select" id="historyUnitId">
                                    <option value="">-- Select unit --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Filter</label>
                                <select class="form-select" id="unitStatusFilter">
                                    <option value="all">All Contracts</option>
                                    <option value="active">Active Only</option>
                                    <option value="completed">Completed Only</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Unit Journey Timeline -->
                        <div id="unitJourneyTimeline">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-route fa-3x mb-3"></i>
                                <p>Select a unit to view its journey across contracts</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- RENEWAL CHAINS VIEW -->
                    <div class="tab-pane fade" id="renewalView" role="tabpanel">
                        <!-- Renewal Chain Selector -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Contract Family</label>
                                <select class="form-select" id="renewalChainId">
                                    <option value="">-- Select contract --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Display Mode</label>
                                <select class="form-select" id="renewalDisplayMode">
                                    <option value="tree">Tree View</option>
                                    <option value="timeline">Timeline View</option>
                                    <option value="comparison">Comparison Table</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Renewal Chain Visualization -->
                        <div id="renewalChainVisualization">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-project-diagram fa-3x mb-3"></i>
                                <p>Select a contract to view its renewal chain</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- RATE HISTORY VIEW -->
                    <div class="tab-pane fade" id="rateView" role="tabpanel">
                        <!-- Rate History Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Contract</label>
                                <select class="form-select" id="rateHistoryContractId">
                                    <option value="">-- All contracts --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Unit</label>
                                <select class="form-select" id="rateHistoryUnitId">
                                    <option value="">-- All units --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date Range</label>
                                <select class="form-select" id="rateHistoryDateRange">
                                    <option value="all">All Time</option>
                                    <option value="30">Last 30 Days</option>
                                    <option value="90">Last 90 Days</option>
                                    <option value="180">Last 6 Months</option>
                                    <option value="365">Last Year</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Rate History Chart & Table -->
                        <div id="rateHistoryContent">
                            <!-- Chart -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-chart-line"></i> Rate Changes Over Time</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="rateHistoryChart" height="100"></canvas>
                                </div>
                            </div>
                            
                            <!-- Table -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-table"></i> Detailed Rate History</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm" id="rateHistoryTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Event</th>
                                                <th>Contract</th>
                                                <th>Unit</th>
                                                <th>Old Rate</th>
                                                <th>New Rate</th>
                                                <th>Change</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="exportHistoryBtn">
                    <i class="fas fa-download"></i> Export to PDF
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Timeline Styles */
.timeline-container {
    position: relative;
    padding: 20px 0;
}

.timeline-line {
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #007bff 0%, #6610f2 100%);
}

.timeline-event {
    position: relative;
    padding-left: 70px;
    margin-bottom: 30px;
    min-height: 60px;
}

.timeline-marker {
    position: absolute;
    left: 15px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: white;
    border: 3px solid #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    z-index: 2;
}

.timeline-marker.amendment {
    border-color: #ffc107;
    color: #ffc107;
}

.timeline-marker.renewal {
    border-color: #28a745;
    color: #28a745;
}

.timeline-marker.unit-change {
    border-color: #17a2b8;
    color: #17a2b8;
}

.timeline-marker.rate-change {
    border-color: #dc3545;
    color: #dc3545;
}

.timeline-content {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 15px;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.timeline-content:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transform: translateX(5px);
    cursor: pointer;
}

.timeline-content.amendment {
    border-left-color: #ffc107;
}

.timeline-content.renewal {
    border-left-color: #28a745;
}

.timeline-content.unit-change {
    border-left-color: #17a2b8;
}

.timeline-content.rate-change {
    border-left-color: #dc3545;
}

.timeline-date {
    font-size: 12px;
    color: #6c757d;
    font-weight: bold;
}

.timeline-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-description {
    font-size: 14px;
    color: #495057;
    margin-bottom: 10px;
}

.timeline-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    font-size: 13px;
}

.timeline-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Unit Journey Styles */
.unit-journey-card {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: white;
    transition: all 0.3s ease;
}

.unit-journey-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,123,255,0.2);
}

.unit-journey-card.active {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-color: #28a745;
}

.unit-journey-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.unit-journey-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

/* Renewal Chain Styles */
.renewal-tree {
    padding: 20px;
}

.renewal-node {
    position: relative;
    padding: 15px;
    margin-bottom: 20px;
    border: 2px solid #007bff;
    border-radius: 8px;
    background: white;
}

.renewal-node.generation-0 {
    border-color: #6610f2;
    background: #f3e8ff;
}

.renewal-node.generation-1 {
    margin-left: 40px;
    border-color: #007bff;
}

.renewal-node.generation-2 {
    margin-left: 80px;
    border-color: #17a2b8;
}

.renewal-node.generation-3 {
    margin-left: 120px;
    border-color: #28a745;
}

.renewal-connector {
    position: absolute;
    left: -40px;
    top: 50%;
    width: 40px;
    height: 2px;
    background: #dee2e6;
}

.renewal-connector::before {
    content: '';
    position: absolute;
    left: 0;
    top: -5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #007bff;
}

/* Rate History Chart */
#rateHistoryChart {
    max-height: 300px;
}

/* Responsive */
@media (max-width: 768px) {
    .timeline-event {
        padding-left: 50px;
    }
    
    .timeline-marker {
        left: 10px;
        width: 25px;
        height: 25px;
        font-size: 12px;
    }
    
    .timeline-line {
        left: 22px;
    }
    
    .renewal-node {
        margin-left: 0 !important;
    }
    
    .renewal-connector {
        display: none;
    }
}
</style>

<!-- Include Chart.js for rate history visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="<?= base_url('assets/js/asset-history.js') ?>"></script>
