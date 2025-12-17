<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
  
<style>
/* Modern Tab-Based Tracking System - Clean Design */
.tracking-header {
  background: white;
  border: 1px solid #dee2e6;
  padding: 25px;
  border-radius: 8px;
  margin-bottom: 25px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.tracking-header h4 {
  margin: 0 0 8px 0;
  font-weight: 600;
  color: #495057;
}

.tracking-header .tracking-id {
  font-size: 1.3rem;
  font-weight: 700;
  color: #007bff;
}

/* Progress Bar */
.progress-overview {
  background: white; 
  padding: 25px;
  border-radius: 12px; 
  margin-bottom: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.progress-bar-container {
  height: 12px;
  background: #e9ecef;
  border-radius: 10px;
  overflow: hidden;
  position: relative;
}

.progress-bar-fill {
  height: 100%;
  background: #28a745;
  border-radius: 10px;
  transition: width 0.6s ease;
}

.progress-text {
  display: flex; 
  justify-content: space-between; 
  margin-top: 10px;
  font-size: 0.9rem;
  color: #6c757d;
}

/* Compact Timeline */
.compact-timeline {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px 0;
  overflow-x: visible;
  position: relative;
}

.timeline-node {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  border: 3px solid #e9ecef;
  background: white;
  color: #adb5bd;
  position: relative;
  flex-shrink: 0;
  transition: all 0.3s ease;
  cursor: pointer;
}

.timeline-node:hover {
  transform: scale(1.08);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  z-index: 10;
}

.timeline-node.completed {
  background: #28a745; 
  border-color: #28a745;
  color: white;
}

.timeline-node.current {
  background: #007bff; 
  border-color: #007bff;
  color: white;
  animation: pulse 2s infinite;
}

/* Tooltip for timeline nodes */
.timeline-node-tooltip {
  position: absolute;
  top: calc(100% + 15px);
  left: 50%;
  transform: translateX(-50%);
  padding: 14px 18px;
  background: #2c3e50;
  color: white;
  border-radius: 8px;
  font-size: 0.85rem;
  line-height: 1.6;
  white-space: normal;
  min-width: 280px;
  max-width: 320px;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease, transform 0.3s ease;
  z-index: 9999;
  box-shadow: 0 6px 20px rgba(0,0,0,0.4);
  text-align: left;
}

.timeline-node-tooltip::before {
  content: '';
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  border: 8px solid transparent;
  border-bottom-color: #2c3e50;
}

/* Fix untuk tooltip di ujung kiri (Marketing) */
.timeline-node:first-child .timeline-node-tooltip {
  left: 0;
  transform: none;
}

.timeline-node:first-child .timeline-node-tooltip::before {
  left: 30px;
  transform: translateX(0);
}

/* Fix untuk tooltip di ujung kanan (Sampai/Berangkat - 2 node terakhir) */
.timeline-node:nth-last-child(-n+2) .timeline-node-tooltip {
  left: auto;
  right: 0;
  transform: none;
}

.timeline-node:nth-last-child(-n+2) .timeline-node-tooltip::before {
  left: auto;
  right: 30px;
  transform: translateX(0);
}

.timeline-node:hover .timeline-node-tooltip {
  opacity: 1;
}

.timeline-node:first-child:hover .timeline-node-tooltip {
  opacity: 1;
  transform: translateY(2px);
}

.timeline-node:nth-last-child(-n+2):hover .timeline-node-tooltip {
  opacity: 1;
  transform: translateY(2px);
}

/* Tooltip tengah tetap centered */
.timeline-node:not(:first-child):not(:nth-last-child(-n+2)):hover .timeline-node-tooltip {
  transform: translateX(-50%) translateY(2px);
}

.timeline-connector {
  flex: 1;
  height: 3px;
  background: #e9ecef;
  margin: 0;
}

.timeline-connector.completed {
  background: #28a745;
}

@media (max-width: 1200px) {
  .timeline-node {
    width: 55px;
    height: 55px;
    font-size: 22px;
  }
}

@media (max-width: 992px) {
  .timeline-node {
    width: 50px;
    height: 50px;
    font-size: 20px;
  }
  
  .timeline-node-tooltip {
    min-width: 240px;
    max-width: 280px;
    font-size: 0.8rem;
  }
}

@keyframes pulse {
  0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4); }
  50% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
}

/* Tab Navigation */
.nav-tabs-custom {
  border-bottom: 2px solid #e9ecef;
  margin-bottom: 25px;
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.nav-tabs-custom .nav-link {
  border: none;
  border-bottom: 3px solid transparent;
  color: #6c757d; 
  font-weight: 600;
  padding: 12px 20px;
  transition: all 0.3s ease;
  background: transparent;
  border-radius: 8px 8px 0 0;
}

.nav-tabs-custom .nav-link:hover {
  color: #007bff; 
  background: #f8f9fa; 
}

.nav-tabs-custom .nav-link.active {
  color: #007bff;
  border-bottom-color: #007bff;
  background: white;
}

.nav-tabs-custom .nav-link i {
  margin-right: 8px;
}

/* Tab Content */
.tab-content-card {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  min-height: 400px;
}

/* Info Cards */
.info-card {
  background: white; 
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 20px;
  height: 100%;
  transition: all 0.3s ease;
}

.info-card:hover {
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.info-card-title {
  font-size: 0.85rem; 
  color: #6c757d;
  text-transform: uppercase; 
  letter-spacing: 0.5px; 
  margin-bottom: 15px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}

.info-card-title i {
  color: #007bff;
}

.info-item {
  margin-bottom: 12px;
}

.info-label {
  font-size: 0.85rem;
  color: #6c757d;
  margin-bottom: 4px;
}

.info-value {
  font-size: 1rem;
  color: #212529;
  font-weight: 500; 
}

.info-value.large {
  font-size: 1.25rem;
  font-weight: 700;
  color: #007bff;
}

/* Simple Table */
.detail-table {
  width: 100%;
  margin-top: 15px;
}

.detail-table tr:not(:last-child) td {
  border-bottom: 1px solid #f0f0f0;
}

.detail-table td {
  padding: 12px;
}

.detail-table td:first-child {
  width: 200px;
  color: #6c757d; 
  font-weight: 500; 
}

.detail-table td:last-child {
  color: #212529;
  font-weight: 500; 
}

/* Status Badge */
.status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 600;
}

.status-badge.success { background: #d4edda; color: #155724; }
.status-badge.primary { background: #cce7ff; color: #004085; }
.status-badge.warning { background: #fff3cd; color: #856404; }
.status-badge.danger { background: #f8d7da; color: #721c24; }
.status-badge.secondary { background: #e9ecef; color: #6c757d; }

/* Search Form */
.search-card {
    background: white; 
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  margin-bottom: 25px;
}

.search-card h6 {
  margin-bottom: 20px;
  color: #495057;
  font-weight: 600;
}

/* Loading State */
.loading-spinner {
  display: inline-block; 
  width: 20px; 
  height: 20px;
  border: 3px solid rgba(0,123,255,.3);
  border-radius: 50%; 
  border-top-color: #007bff;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
  .tracking-header {
    padding: 20px;
  }
  
  .nav-tabs-custom .nav-link {
    font-size: 0.85rem;
    padding: 10px 15px;
  }
  
  .compact-timeline {
    justify-content: flex-start;
  }
  
  .timeline-node {
    width: 35px;
    height: 35px;
  font-size: 14px;
}
}
</style>

<!-- Search Form -->
<div class="search-card">
  <h6><i class="fas fa-search me-2"></i>Shipment Tracking</h6>
  <form id="trackingSearchForm">
    <!-- Step 1: Initial Search -->
    <div id="searchStep1" class="search-step">
      <div class="row g-3">
        <div class="col-md-10">
          <div class="input-group input-group-lg">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" id="searchValue" name="search_value" 
                   placeholder="Enter Contract No., SPK, or DI..." autocomplete="off">
          </div>
          <small class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            The system will automatically detect the document type based on your input.
          </small>
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-primary btn-lg w-100" onclick="performSearch()">
            <i class="fas fa-search me-2"></i> Search
          </button>
        </div>
      </div>
    </div>

    <!-- Step 2: SPK Selection (if multiple) -->
    <div id="searchStep2" class="search-step" style="display: none;">
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label fw-bold">Select SPK:</label>
          <select class="form-select form-select-lg" id="spkSelect">
            <option value="">-- Select SPK --</option>
          </select>
          <small class="text-muted">This contract has multiple SPKs, please select one</small>
        </div>
        <div class="col-md-2">
          <label class="form-label">&nbsp;</label>
          <button type="button" class="btn btn-secondary btn-lg w-100" onclick="backToStep1()">
            <i class="fas fa-arrow-left me-2"></i> Back
          </button>
        </div>
        <div class="col-md-2">
          <label class="form-label">&nbsp;</label>
          <button type="button" class="btn btn-primary btn-lg w-100" onclick="proceedWithSPK()">
            Next <i class="fas fa-arrow-right ms-2"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Step 3: DI Selection (if multiple) -->
    <div id="searchStep3" class="search-step" style="display: none;">
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label fw-bold">Select DI:</label>
          <select class="form-select form-select-lg" id="diSelect">
            <option value="">-- Select DI --</option>
          </select>
          <small class="text-muted">This SPK has multiple DIs, please select one</small>
        </div>
        <div class="col-md-2">
          <label class="form-label">&nbsp;</label>
          <button type="button" class="btn btn-secondary btn-lg w-100" onclick="backToStep2()">
            <i class="fas fa-arrow-left me-2"></i> Back
          </button>
        </div>
        <div class="col-md-2">
          <label class="form-label">&nbsp;</label>
          <button type="button" class="btn btn-primary btn-lg w-100" onclick="proceedWithDI()">
            Track <i class="fas fa-search ms-2"></i>
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- Tracking Results (Hidden by default) -->
<div id="trackingResults" style="display: none;">
  <!-- Header with Progress -->
  <div class="tracking-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4><i class="fas fa-route me-2"></i>Shipment Tracking</h4>
        <div class="tracking-id" id="trackingId">-</div>
      </div>
      <button class="btn btn-light btn-sm" onclick="resetSearch()">
        <i class="fas fa-times me-1"></i> Close
      </button>
    </div>
    
    <!-- Compact Timeline -->
    <div class="compact-timeline" id="compactTimeline"></div>
  </div>

  <!-- Progress Overview -->
  <div class="progress-overview">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <strong>Overall Progress</strong>
      <span id="progressPercent" class="text-primary fw-bold">0%</span>
    </div>
    <div class="progress-bar-container">
      <div class="progress-bar-fill" id="progressBar" style="width: 0%"></div>
    </div>
    <div class="progress-text">
      <span id="progressSteps">0 out of 9 steps completed</span>
      <span id="progressStatus">In Progress</span>
    </div>
  </div>

  <!-- Tab Navigation -->
  <ul class="nav nav-tabs-custom" id="trackingTabs" role="tablist">
    <li class="nav-item">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ringkasan">
        <i class="fas fa-chart-pie"></i> Summary
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kontrak">
        <i class="fas fa-file-contract"></i> Contract
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-spk">
        <i class="fas fa-clipboard-list"></i> SPK
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-unit">
        <i class="fas fa-box"></i> Unit
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-delivery">
        <i class="fas fa-truck"></i> Delivery
      </button>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content">
    <!-- Tab: Ringkasan -->
    <div class="tab-pane fade show active" id="tab-ringkasan">
      <div class="tab-content-card">
        <div class="row" id="ringkasanContent">
          <div class="col-md-12 text-center text-muted">
            <div class="loading-spinner me-2"></div> Loading data...
          </div>
        </div>
      </div>
    </div>

    <!-- Tab: Kontrak -->
    <div class="tab-pane fade" id="tab-kontrak">
      <div class="tab-content-card">
        <h5 class="mb-4"><i class="fas fa-file-contract me-2 text-primary"></i>Contract Details</h5>
        <div id="kontrakContent">
          <p class="text-muted">Loading contract data...</p>
        </div>
      </div>
    </div>

    <!-- Tab: SPK -->
    <div class="tab-pane fade" id="tab-spk">
      <div class="tab-content-card">
        <h5 class="mb-4"><i class="fas fa-clipboard-list me-2 text-primary"></i>SPK Details</h5>
        <div id="spkContent">
          <p class="text-muted">Loading SPK data...</p>
        </div>
      </div>
    </div>

    <!-- Tab: Unit -->
    <div class="tab-pane fade" id="tab-unit">
      <div class="tab-content-card">
        <h5 class="mb-4"><i class="fas fa-box me-2 text-primary"></i>Unit Details</h5>
        <div id="unitContent">
          <p class="text-muted">Loading unit data...</p>
        </div>
      </div>
    </div>

    <!-- Tab: Pengiriman -->
    <div class="tab-pane fade" id="tab-delivery">
      <div class="tab-content-card">
        <h5 class="mb-4"><i class="fas fa-truck me-2 text-primary"></i>Delivery Details</h5>
        <div id="deliveryContent">
          <p class="text-muted">Loading delivery data...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Global variables
let currentTrackingData = null;

// Search function with multi-step
function performSearch() {
  const searchValue = document.getElementById('searchValue').value.trim();
  
  if (!searchValue) {
    alert('Please enter contract number, SPK, or DI');
    return;
  }
  
  // Fetch data
  fetch('<?= base_url('operational/tracking-search') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({ search_value: searchValue })
  })
  .then(response => response.json())
  .then(result => {
    if (result.success && result.data) {
      handleSearchResponse(result.data);
    } else {
      alert(result.message || 'Data not found');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while fetching data');
  });
}

function handleSearchResponse(data) {
  console.log('Handling search response:', data);
  
  // Check if multiple SPKs
  if (data.multiple_spks && data.spks && data.spks.length > 1) {
    console.log('Multiple SPKs found, showing selection');
    showSPKSelection(data.spks);
    return;
  }
  
  // Check if multiple DIs
  if (data.multiple_dis && data.dis && data.dis.length > 1) {
    console.log('Multiple DIs found, showing selection');
    showDISelection(data.dis);
    return;
  }
  
  // Single result - show directly
  console.log('Single result, rendering tracking data');
  currentTrackingData = data;
  renderTrackingData(data);
}

function showSPKSelection(spks) {
  const select = document.getElementById('spkSelect');
  select.innerHTML = '<option value="">-- Select SPK --</option>';
  
  spks.forEach(spk => {
    const option = document.createElement('option');
    option.value = spk.id;
    option.textContent = `${spk.nomor_spk} - ${spk.jenis_spk}`;
    select.appendChild(option);
  });
  
  document.getElementById('searchStep1').style.display = 'none';
  document.getElementById('searchStep2').style.display = 'block';
}

function showDISelection(dis) {
  const select = document.getElementById('diSelect');
  select.innerHTML = '<option value="">-- Select DI --</option>';
  
  dis.forEach(di => {
    const option = document.createElement('option');
    option.value = di.id;
    option.textContent = `${di.nomor_di} - ${di.status || 'N/A'}`;
    select.appendChild(option);
  });
  
  document.getElementById('searchStep2').style.display = 'none';
  document.getElementById('searchStep3').style.display = 'block';
}

function proceedWithSPK() {
  const spkId = document.getElementById('spkSelect').value;
  
  if (!spkId) {
    alert('Please select SPK');
    return;
  }
  
  console.log('Proceeding with SPK ID:', spkId);
  
  // Search by SPK ID
  fetch('<?= base_url('operational/tracking-search') ?>', {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json', 
        'X-Requested-With': 'XMLHttpRequest'
      },
    body: JSON.stringify({ search_value: spkId, search_type: 'spk' })
  })
  .then(response => response.json())
  .then(result => {
    console.log('SPK search result:', result);
    if (result.success && result.data) {
      handleSearchResponse(result.data);
      } else {
      alert('SPK not found: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
    console.error('Error in proceedWithSPK:', error);
    alert('An error occurred: ' + error.message);
  });
}

function proceedWithDI() {
  const diId = document.getElementById('diSelect').value;
  
  if (!diId) {
    alert('Please select DI');
    return;
  }
  
  // Search by DI ID
  fetch('<?= base_url('operational/tracking-search') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({ search_value: diId, search_type: 'di' })
  })
  .then(response => response.json())
  .then(result => {
    if (result.success && result.data) {
      currentTrackingData = result.data;
      renderTrackingData(result.data);
    } else {
      alert('DI not found');
    }
  });
}

function backToStep1() {
  document.getElementById('searchStep2').style.display = 'none';
  document.getElementById('searchStep1').style.display = 'block';
}

function backToStep2() {
  document.getElementById('searchStep3').style.display = 'none';
  document.getElementById('searchStep2').style.display = 'block';
}

function resetSearch() {
  document.getElementById('trackingResults').style.display = 'none';
  document.getElementById('searchStep1').style.display = 'block';
  document.getElementById('searchStep2').style.display = 'none';
  document.getElementById('searchStep3').style.display = 'none';
  document.getElementById('searchValue').value = '';
  currentTrackingData = null;
}

function renderTrackingData(data) {
  console.log('renderTrackingData called with:', data);
  
  // Hide search steps, show results
  document.getElementById('searchStep1').style.display = 'none';
  document.getElementById('searchStep2').style.display = 'none';
  document.getElementById('searchStep3').style.display = 'none';
  document.getElementById('trackingResults').style.display = 'block';
  
  // Set tracking ID
  document.getElementById('trackingId').textContent = 
    data.di?.nomor_di || data.spk?.nomor_spk || '-';
  
  console.log('Rendering compact timeline...');
  renderCompactTimeline(data);
  
  console.log('Rendering progress...');
  renderProgress(data);
  
  console.log('Rendering summary...');
  renderRingkasan(data);
  
  console.log('Tracking data rendered successfully');
}

function renderCompactTimeline(data) {
  console.log('renderCompactTimeline - data:', data);
  const container = document.getElementById('compactTimeline');
  if (!container) {
    console.error('compactTimeline container not found!');
    return;
  }
  
    const steps = getStepsConfig(data);
  console.log('Steps config:', steps);
    
  let html = '';
  steps.forEach((step, index) => {
    const isCompleted = step.actualDate && step.actualDate !== '-';
    const isCurrent = !isCompleted && (index === 0 || (steps[index-1].actualDate && steps[index-1].actualDate !== '-'));
    
    let className = 'timeline-node';
    if (isCompleted) className += ' completed';
    else if (isCurrent) className += ' current';
    
    // Create comprehensive tooltip with stage-specific details
    const tooltipText = getTooltipContent(step, index, data, isCompleted);
    
    html += `
      <div class="${className}">
        <i class="${step.icon}"></i>
        <div class="timeline-node-tooltip">${tooltipText}</div>
      </div>
    `;
    
    if (index < steps.length - 1) {
      html += `<div class="timeline-connector ${isCompleted ? 'completed' : ''}"></div>`;
    }
  });
    
  container.innerHTML = html;
  console.log('Compact timeline rendered');
}

function getTooltipContent(step, stepIndex, data, isCompleted) {
  const stageStatus = data.spk?.stage_status || {};
  const unitStages = stageStatus.unit_stages || {};
  const firstUnitKey = Object.keys(unitStages)[0];
  const stages = firstUnitKey ? unitStages[firstUnitKey] : {};
  const preparedUnits = data.spk?.prepared_units_detail || [];
  const firstUnit = preparedUnits[0] || {};
  
  let tooltip = `<strong>${step.step}</strong><br>`;
  tooltip += `<small>${step.event}</small><br>`;
  tooltip += `<hr style="margin: 8px 0; border-color: rgba(255,255,255,0.2);">`;
  
  if (!isCompleted) {
    tooltip += `<span style="color: #ffc107;">○ Belum selesai</span>`;
    if (step.estimatedCompletion && step.estimatedCompletion !== '-') {
      tooltip += `<br><small>Est: ${step.estimatedCompletion}</small>`;
    }
    return tooltip;
  }
  
  // Completed - show details based on step
  tooltip += `<span style="color: #20c997;">✓ ${formatDateTime(step.actualDate)}</span><br>`;
  
  switch(stepIndex) {
    case 0: // SPK Dibuat
      tooltip += `<strong>Created at:</strong> ${data.spk?.created_by_name || 'Marketing'}<br>`;
      tooltip += `<strong>Type:</strong> ${data.spk?.jenis_spk || '-'}<br>`;
      tooltip += `<strong>Customer:</strong> ${data.spk?.pelanggan || '-'}`;
      break;
      
    case 1: // Persiapan Unit
      const persiapan = stages.persiapan_unit || {};
      tooltip += `<strong>Mechanic:</strong> ${persiapan.mekanik || step.pic || '-'}<br>`;
      const noUnit = persiapan.no_unit || firstUnit.no_unit || '-';
      tooltip += `<strong>Unit Number:</strong> ${noUnit !== '-' ? noUnit.split('(SN:')[0].trim() : '-'}<br>`;
      if (persiapan.aksesoris_tersedia) {
        tooltip += `<strong>Accessories:</strong> ${persiapan.aksesoris_tersedia}`;
      }
      break;
      
    case 2: // Fabrikasi
      const fabrikasi = stages.fabrikasi || {};
      tooltip += `<strong>Mechanic:</strong> ${fabrikasi.mekanik || step.pic || '-'}<br>`;
      if (firstUnit.attachment_sn && firstUnit.attachment_sn !== '-') {
        tooltip += `<strong>Attachment:</strong> ${firstUnit.attachment_sn.split('(SN:')[0].trim()}<br>`;
      }
      if (fabrikasi.catatan) {
        tooltip += `<strong>Notes:</strong> ${fabrikasi.catatan}`;
      }
      break;
      
    case 3: // Painting
      const painting = stages.painting || {};
      tooltip += `<strong>Mechanic:</strong> ${painting.mekanik || step.pic || '-'}<br>`;
      if (painting.catatan) {
        tooltip += `<strong>Notes:</strong> ${painting.catatan}`;
      }
      break;
      
    case 4: // PDI Check
      const pdi = stages.pdi || {};
      tooltip += `<strong>Inspector:</strong> ${pdi.mekanik || step.pic || '-'}<br>`;
      if (pdi.catatan) {
        tooltip += `<strong>Result:</strong> ${pdi.catatan}<br>`;
      }
      tooltip += `<strong>Status:</strong> <span style="color: #20c997;">PASS ✓</span>`;
      break;
      
    case 5: // DI Dibuat
      tooltip += `<strong>Created at:</strong> ${data.di?.dibuat_oleh_name || 'Operational'}<br>`;
      tooltip += `<strong>DI Number:</strong> ${data.di?.nomor_di || '-'}<br>`;
      tooltip += `<strong>Destination:</strong> ${data.di?.lokasi || '-'}`;
      break;
      
    case 6: // Persiapan Kirim
      tooltip += `<strong>Driver:</strong> ${data.di?.nama_supir || '-'}<br>`;
      tooltip += `<strong>HP:</strong> ${data.di?.no_hp_supir || '-'}<br>`;
      tooltip += `<strong>Vehicle:</strong> ${data.di?.kendaraan || '-'} ${data.di?.no_polisi_kendaraan ? '(' + data.di.no_polisi_kendaraan + ')' : ''}<br>`;
      tooltip += `<strong>Schedule:</strong> ${formatDateTime(data.di?.tanggal_kirim)}`;
      break;
      
    case 7: // Berangkat
      tooltip += `<strong>Driver:</strong> ${data.di?.nama_supir || '-'}<br>`;
      tooltip += `<strong>Vehicle:</strong> ${data.di?.kendaraan || '-'}<br>`;
      tooltip += `<strong>Est. Arrival:</strong> ${formatDateTime(data.di?.estimasi_sampai)}`;
      if (data.di?.catatan_berangkat) {
        tooltip += `<br><strong>Notes:</strong> ${data.di.catatan_berangkat}`;
      }
      break;
      
    case 8: // Sampai
      tooltip += `<strong>Received:</strong> ${formatDateTime(data.di?.sampai_tanggal_approve)}<br>`;
      tooltip += `<strong>Location:</strong> ${data.di?.lokasi || '-'}<br>`;
      tooltip += `<strong>Receiver PIC:</strong> ${data.di?.pic || '-'}`;
      if (data.di?.catatan_sampai) {
        tooltip += `<br><strong>Notes:</strong> ${data.di.catatan_sampai}`;
      }
      break;
      
    default:
      if (step.pic) tooltip += `<strong>PIC:</strong> ${step.pic}`;
  }
  
  return tooltip;
}

function renderProgress(data) {
  console.log('renderProgress - data:', data);
    const steps = getStepsConfig(data);
  const completedSteps = steps.filter(s => s.actualDate && s.actualDate !== '-').length;
  const progress = Math.round((completedSteps / steps.length) * 100);
  
  console.log('Progress calculated:', progress + '%', completedSteps, 'of', steps.length);
  
  const progressBar = document.getElementById('progressBar');
  const progressPercent = document.getElementById('progressPercent');
  const progressSteps = document.getElementById('progressSteps');
  const progressStatus = document.getElementById('progressStatus');
  
  if (progressBar) progressBar.style.width = progress + '%';
  if (progressPercent) progressPercent.textContent = progress + '%';
  if (progressSteps) progressSteps.textContent = `${completedSteps} of ${steps.length} steps completed`;
  if (progressStatus) progressStatus.textContent = progress === 100 ? 'Completed' : 'In Progress';
  
  console.log('Progress rendered');
}

function renderRingkasan(data) {
  console.log('renderRingkasan - data:', data);
  const container = document.getElementById('ringkasanContent');
  
  if (!container) {
    console.error('ringkasanContent container not found!');
    return;
  }
  
  const html = `
    <div class="col-md-4 mb-3">
      <div class="info-card">
        <div class="info-card-title">
          <i class="fas fa-building"></i>
          <span>Customer Information</span>
                    </div>
        <div class="info-item">
          <div class="info-label">Company Name</div>
          <div class="info-value large">${data.spk?.pelanggan || '-'}</div>
                        </div>
        <div class="info-item">
          <div class="info-label">PIC</div>
          <div class="info-value">${data.spk?.pic || '-'}</div>
                        </div>
        <div class="info-item">
          <div class="info-label">Contact</div>
          <div class="info-value">${data.spk?.kontak || '-'}</div>
                        </div>
        <div class="info-item">
          <div class="info-label">Location</div>
          <div class="info-value">${data.spk?.lokasi || '-'}</div>
                        </div>
                    </div>
                        </div>
    
    <div class="col-md-4 mb-3">
      <div class="info-card">
        <div class="info-card-title">
          <i class="fas fa-box"></i>
          <span>Unit Information</span>
                            </div>
        <div class="info-item">
          <div class="info-label">SPK Type</div>
          <div class="info-value">${data.spk?.jenis_spk || '-'}</div>
                        </div>
        <div class="info-item">
          <div class="info-label">SPK Number</div>
          <div class="info-value">${data.spk?.nomor_spk || '-'}</div>
                    </div>
        <div class="info-item">
          <div class="info-label">Specification</div>
          <div class="info-value">${getUnitSummary(data)}</div>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-3">
      <div class="info-card">
        <div class="info-card-title">
          <i class="fas fa-truck"></i>
          <span>Delivery Status</span>
        </div>
        <div class="info-item">
          <div class="info-label">DI Number</div>
          <div class="info-value">${data.di?.nomor_di || '-'}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Current Status</div>
          <div class="info-value">${getCurrentStatus(data)}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Delivery Date</div>
          <div class="info-value">${formatDateTime(data.di?.tanggal_kirim)}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Destination Location</div>
          <div class="info-value">${data.di?.lokasi || '-'}</div>
                </div>
            </div>
        </div>
        `;
  
  container.innerHTML = html;
  console.log('Ringkasan rendered successfully');
}

function getUnitSummary(data) {
  const units = data.spk?.prepared_units_detail || [];
  if (units.length === 0) return '-';
  
  const unit = units[0];
  const parts = [];
  
  if (unit.jenis_unit) parts.push(unit.jenis_unit);
  if (unit.kapasitas_name) parts.push(unit.kapasitas_name);
  if (unit.departemen_name) parts.push(unit.departemen_name);
  
  return parts.join(' | ') || '-';
}

function getCurrentStatus(data) {
  if (data.di?.sampai_tanggal_approve) return 'Selesai Diterima';
  if (data.di?.berangkat_tanggal_approve) return 'Dalam Perjalanan';
  if (data.di?.perencanaan_tanggal_approve) return 'Siap Kirim';
  if (data.di?.dibuat_pada) return 'DI Dibuat';
  return 'Dalam Persiapan';
  }

  function getStepsConfig(data) {
  const stageStatus = data.spk?.stage_status || {};
  const unitStages = stageStatus.unit_stages || {};
  const firstUnitKey = Object.keys(unitStages)[0];
  const stages = firstUnitKey ? unitStages[firstUnitKey] : {};
  
  const persiapanUnit = stages.persiapan_unit || {};
  const fabrikasi = stages.fabrikasi || {};
  const painting = stages.painting || {};
  const pdi = stages.pdi || {};
    
    return [
    {step: 'SPK Created', icon: 'fas fa-file-signature', actualDate: data.spk?.dibuat_pada},
    {step: 'Unit Preparation', icon: 'fas fa-tools', actualDate: persiapanUnit.tanggal_approve},
    {step: 'Fabrication', icon: 'fas fa-hammer', actualDate: fabrikasi.tanggal_approve},
    {step: 'Painting', icon: 'fas fa-paint-brush', actualDate: painting.tanggal_approve},
    {step: 'PDI Check', icon: 'fas fa-check-circle', actualDate: pdi.tanggal_approve},
    {step: 'DI Created', icon: 'fas fa-file-invoice', actualDate: data.di?.dibuat_pada},
    {step: 'Preparation for Delivery', icon: 'fas fa-calendar-alt', actualDate: data.di?.perencanaan_tanggal_approve},
    {step: 'Departure', icon: 'fas fa-truck', actualDate: data.di?.berangkat_tanggal_approve},
    {step: 'Arrival', icon: 'fas fa-flag-checkered', actualDate: data.di?.sampai_tanggal_approve}
    ];
  }

  function formatDateTime(dateStr) {
    if (!dateStr || dateStr === '-') return '-';
      const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'});
}

// Lazy load tabs
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
  tab.addEventListener('shown.bs.tab', function(e) {
    const targetId = e.target.getAttribute('data-bs-target');
    
    if (!currentTrackingData) return;
    
    switch(targetId) {
      case '#tab-kontrak':
        if (!document.getElementById('kontrakContent').hasAttribute('data-loaded')) {
          renderKontrak(currentTrackingData);
          document.getElementById('kontrakContent').setAttribute('data-loaded', 'true');
        }
        break;
      case '#tab-spk':
        if (!document.getElementById('spkContent').hasAttribute('data-loaded')) {
          renderSPK(currentTrackingData);
          document.getElementById('spkContent').setAttribute('data-loaded', 'true');
        }
        break;
      case '#tab-unit':
        if (!document.getElementById('unitContent').hasAttribute('data-loaded')) {
          renderUnit(currentTrackingData);
          document.getElementById('unitContent').setAttribute('data-loaded', 'true');
        }
        break;
      case '#tab-delivery':
        if (!document.getElementById('deliveryContent').hasAttribute('data-loaded')) {
          renderDelivery(currentTrackingData);
          document.getElementById('deliveryContent').setAttribute('data-loaded', 'true');
        }
        break;
    }
  });
});

function renderKontrak(data) {
  const container = document.getElementById('kontrakContent');
  
  // Use kontrak data if available, otherwise fallback to SPK
  const kontrakDate = data.kontrak?.created_at || data.kontrak?.tanggal_kontrak || data.spk?.created_at;
  
  container.innerHTML = `
    <table class="detail-table">
      <tr>
        <td>Contract/PO Number</td>
        <td><strong>${data.spk?.po_kontrak_nomor || data.kontrak?.no_kontrak || '-'}</strong></td>
      </tr>
      <tr>
        <td>Contract Date</td>
        <td><strong>${formatDateTime(kontrakDate)}</strong></td>
      </tr>
      <tr>
        <td>Customer</td>
        <td>${data.spk?.pelanggan || data.kontrak?.nama_pelanggan || '-'}</td>
      </tr>
      <tr>
        <td>PIC</td>
        <td>${data.spk?.pic || '-'}</td>
      </tr>
      <tr>
        <td>Contact</td>
        <td>${data.spk?.kontak || '-'}</td>
      </tr>
      <tr>
        <td>Location</td>
        <td>${data.spk?.lokasi || '-'}</td>
      </tr>
    </table>
  `;
}

function renderSPK(data) {
  const container = document.getElementById('spkContent');
  
  // Get creator name like in print_spk.php
  const createdBy = data.spk?.created_by_name || data.spk?.created_by || data.spk?.marketing_name || 'Marketing';
  
  container.innerHTML = `
    <table class="detail-table">
      <tr>
        <td>SPK Number</td>
        <td><strong>${data.spk?.nomor_spk || '-'}</strong></td>
      </tr>
      <tr>
        <td>SPK Type</td>
        <td>${data.spk?.jenis_spk || '-'}</td>
      </tr>
      <tr>
        <td>Created Date</td>
        <td>${formatDateTime(data.spk?.dibuat_pada || data.spk?.created_at)}</td>
      </tr>
      <tr>
        <td>Created By</td>
        <td><strong>${createdBy}</strong></td>
      </tr>
      <tr>
        <td>Contract/PO Number</td>
        <td>${data.spk?.po_kontrak_nomor || '-'}</td>
      </tr>
      <tr>
        <td>SPK Status</td>
        <td><span class="status-badge primary">Active</span></td>
      </tr>
    </table>
  `;
}

function renderUnit(data) {
  const container = document.getElementById('unitContent');
  const units = data.spk?.prepared_units_detail || [];
  
  if (units.length === 0) {
    container.innerHTML = '<p class="text-muted">Unit data not available</p>';
      return;
    }
    
  const unit = units[0];
  container.innerHTML = `
    <table class="detail-table">
      <tr>
        <td>Unit Number</td>
        <td><strong>${unit.no_unit ? unit.no_unit.split('(SN:')[0].trim() : '-'}</strong></td>
      </tr>
      <tr>
        <td>Unit Type</td>
        <td>${unit.jenis_unit || '-'}</td>
      </tr>
      <tr>
        <td>Department</td>
        <td>${unit.departemen_name || '-'}</td>
      </tr>
      <tr>
        <td>Capacity</td>
        <td>${unit.kapasitas_name || '-'}</td>
      </tr>
      <tr>
        <td>Attachment</td>
        <td>${unit.attachment_sn ? unit.attachment_sn.split('(SN:')[0].trim() : '-'}</td>
      </tr>
      <tr>
        <td>Mast</td>
        <td>${unit.mast_name || '-'}</td>
      </tr>
      <tr>
        <td>Wheel</td>
        <td>${unit.roda_name || '-'}</td>
      </tr>
    </table>
  `;
}

function renderDelivery(data) {
  const container = document.getElementById('deliveryContent');
  container.innerHTML = `
    <table class="detail-table">
      <tr>
        <td>DI Number</td>
        <td><strong>${data.di?.nomor_di || '-'}</strong></td>
          </tr>
      <tr>
        <td>Delivery Date</td>
        <td>${formatDateTime(data.di?.tanggal_kirim)}</td>
      </tr>
      <tr>
        <td>Driver</td>
        <td>${data.di?.nama_supir || '-'}</td>
      </tr>
      <tr>
        <td>Driver Phone</td>
        <td>${data.di?.no_hp_supir || '-'}</td>
      </tr>
      <tr>
        <td>Vehicle</td>
        <td>${data.di?.kendaraan || '-'} ${data.di?.no_polisi_kendaraan ? '(' + data.di.no_polisi_kendaraan + ')' : ''}</td>
      </tr>
      <tr>
        <td>Destination Location</td>
        <td>${data.di?.lokasi || '-'}</td>
      </tr>
      <tr>
        <td>Status</td>
        <td>${getCurrentStatus(data)}</td>
      </tr>
    </table>
  `;
}

// Timeline tab removed - info available in compact timeline tooltips
</script>

<?= $this->endSection() ?>

