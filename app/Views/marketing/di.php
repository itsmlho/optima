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

<!-- Statistics Cards -->
  <div class="row mt-3 mb-4">
      <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
          <div class="stat-card bg-primary-soft filter-card" data-filter="all" style="cursor:pointer;">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-file-text stat-icon text-primary"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="totalDI">0</div>
                      <div class="text-muted"><?= lang('Marketing.total') ?> DI</div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
          <div class="stat-card bg-warning-soft filter-card" data-filter="DIRENCANAKAN" style="cursor:pointer;">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-calendar-check stat-icon text-warning"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="submittedDI">0</div>
                      <div class="text-muted"><?= lang('Marketing.planned') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
          <div class="stat-card bg-info-soft filter-card" data-filter="DALAM_PERJALANAN" style="cursor:pointer;">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-arrow-repeat stat-icon text-info"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="inprogressDI">0</div>
                      <div class="text-muted"><?= lang('Marketing.in_progress') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
          <div class="stat-card bg-success-soft filter-card" data-filter="SELESAI" style="cursor:pointer;">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-check-circle stat-icon text-success"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="deliveredDI">0</div>
                      <div class="text-muted"><?= lang('Marketing.completed') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
          <div class="stat-card bg-danger-soft filter-card" data-filter="AWAITING_CONTRACT" style="cursor:pointer;">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-file-earmark-lock stat-icon text-danger"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="awaitingContractDI">0</div>
                      <div class="text-muted">Awaiting Contract</div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="card table-card mb-3">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
      <h5 class="h5 mb-0 text-gray-800"><?= lang('App.delivery_instructions_di') ?> <?= lang('App.show') ?></h5>
      <div class="d-flex gap-2 align-items-center">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#diCreateModal">
          <i class="fas fa-plus"></i> <?= lang('Marketing.create') ?> DI
        </button>
      </div>
    </div>
    
    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-3" id="filterTabs">
      <li class="nav-item">
        <a class="nav-link active filter-tab" href="#" data-filter="all"><?= lang('Marketing.all') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link filter-tab" href="#" data-filter="SUBMITTED"><?= lang('Marketing.submitted') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link filter-tab" href="#" data-filter="INPROGRESS"><?= lang('Marketing.in_progress') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link filter-tab" href="#" data-filter="DELIVERED"><?= lang('Marketing.delivered') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link filter-tab text-warning" href="#" data-filter="AWAITING_CONTRACT">
          <i class="fas fa-exclamation-triangle me-1"></i>Awaiting Contract
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link filter-tab" href="#" data-filter="CANCELLED"><?= lang('Marketing.cancelled') ?></a>
      </li>
    </ul>
    
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
          <span>Show</span>
          <select class="form-select form-select-sm" id="entriesPerPage" style="width: auto;">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>entries</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span>Search:</span>
          <input type="text" class="form-control form-control-sm" id="diSearch" placeholder="" style="width: 200px;">
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-hover table-manual-sort" id="diTable">
          <thead>
            <tr>
              <th>No. DI</th>
              <th>No. SPK</th>
              <th>PO/Contract</th>
              <th>Customer</th>
              <th>Location</th>
              <th>Total Items</th>
              <th>Command Type</th>
              <th>Command Purpose</th>
              <th>Req. Delivery Date</th>
              <th data-no-sort>Status</th>
              <th data-no-sort>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Pagination and Info -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div id="diTableInfo">Showing 0 to 0 of 0 entries</div>
        <nav>
          <ul class="pagination pagination-sm mb-0" id="diPagination"></ul>
        </nav>
      </div>
    </div>
  </div>

  <!-- Enhanced DI Modal with correct TUKAR workflow support -->
  <div class="modal fade" id="diCreateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title"><?= lang('Marketing.create') ?> DI</h6>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="diCreateForm">
          <div class="modal-body">
            <!-- SPK Selection -->
            <div class="mb-3" id="spkSelectionSection">
              <label class="form-label"><?= lang('Marketing.select_spk_ready') ?></label>
              <input type="text" class="form-control" id="modalSpkSearch" placeholder="<?= lang('Marketing.search_spk_po_customer') ?>">
              <select class="form-select mt-2" id="spkPick" name="spk_id"></select>
              <div class="form-text"><?= lang('Marketing.only_ready_spk_for_di') ?></div>
            </div>
            
            <!-- Standard Item Selection (for non-TUKAR workflows) - Dynamic for Units/Attachments -->
            <div id="diUnitsSection" style="display:none;" class="mb-3">
              <label class="form-label" id="itemSelectionLabel"><?= lang('Marketing.select_items_to_ship') ?></label>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="small text-muted"><?= lang('App.selected') ?>: <span id="selCount">0</span> <span id="itemTypeLabel"><?= lang('App.items') ?></span></div>
                <div>
                  <button class="btn btn-sm btn-outline-secondary" type="button" id="btnSelectAll"><?= lang('App.select_all') ?></button>
                  <button class="btn btn-sm btn-outline-secondary" type="button" id="btnClearAll"><?= lang('App.clear') ?></button>
                </div>
              </div>
              <div id="diUnitList" class="unit-list"><div class="text-muted small"><?= lang('Marketing.loading_items_from_spk') ?></div></div>
              <div class="form-text" id="itemSelectionHelp"><?= lang('Marketing.items_to_ship_in_di') ?></div>
            </div>
            
            <!-- Enhanced Workflow Section - Step 1: Jenis & Tujuan Perintah -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label"><?= lang('Marketing.command_type') ?> <span class="text-danger">*</span></label>
                <select class="form-select" name="jenis_perintah_kerja_id" id="jenisPerintahSelect" required>
                  <option value="">-- <?= lang('Marketing.select_command_type') ?> --</option>
                </select> 
                <div class="form-text"><?= lang('Marketing.specify_main_action') ?></div>
              </div>
              <div class="col-md-6">
                <label class="form-label"><?= lang('Marketing.command_purpose') ?> <span class="text-danger">*</span></label>
                <select class="form-select" name="tujuan_perintah_kerja_id" id="tujuanPerintahSelect" required disabled>
                  <option value="">-- <?= lang('Marketing.select_command_type_first') ?> --</option>
                </select>
                <div class="form-text">
                  <?= lang('Marketing.reason_context_command') ?><br>
                  <small>
                    🔴 <?= lang('Marketing.permanent') ?> | 🔵 <?= lang('Marketing.temporary_returns') ?> | 🟡 <?= lang('Marketing.temp_replacement') ?> | 🟢 <?= lang('Marketing.relocation') ?>
                  </small>
                </div>
              </div>
            </div>

            <!-- Step 2: Kontrak Selection (for TARIK and TUKAR workflows) -->
            <div id="diKontrakSelection" style="display:none;" class="mb-3">
              <label class="form-label"><?= lang('Marketing.select_contract') ?> <span class="text-danger">*</span></label>
              <select class="form-select" name="kontrak_id" id="kontrakSelect">
                <option value="">-- <?= lang('Marketing.select_contract') ?> --</option>
              </select>
              <div class="form-text"><?= lang('Marketing.contract_for_pull_exchange') ?></div>
            </div>

            <!-- Step 3: TARIK Unit Selection (Simple selection from kontrak) -->
            <div id="diTarikOnlySection" style="display:none;" class="mb-3">
              <label class="form-label"><?= lang('Marketing.select_units_to_pull') ?></label>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="small text-muted"><?= lang('App.selected') ?>: <span id="tarikOnlyCount">0</span> <?= lang('App.unit') ?></div>
                <div>
                  <button class="btn btn-sm btn-outline-warning" type="button" id="btnSelectAllTarikOnly">Select All</button>
                  <button class="btn btn-sm btn-outline-secondary" type="button" id="btnClearTarikOnly">Clear</button>
                </div>
              </div>
              <div id="diTarikOnlyList" class="unit-list">
                <div class="text-muted small">Select a contract first...</div>
              </div>
              <div class="form-text">Selected units will be removed from the contract (FK relationship removed)</div>
            </div>

            <!-- Step 3: TUKAR Unit Selection (Complex TARIK from kontrak + KIRIM from SPK) -->
            <div id="diTukarWorkflow" style="display:none;" class="mb-4">
              <div class="alert alert-info">
                <i class="fas fa-exchange-alt"></i> 
                <strong>Workflow EXCHANGE:</strong> Select units from the contract to be pulled as replacements
              </div>
              
              <!-- Unit PULL Section for EXCHANGE -->
              <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                  <h6 class="mb-0"><i class="fas fa-minus-circle"></i> Pull Units (from selected contract)</h6>
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="small text-muted">Selected: <span id="tarikCount">0</span> unit</div>
                    <div>
                      <button class="btn btn-sm btn-outline-warning" type="button" id="btnSelectAllTarik">Select All</button>
                      <button class="btn btn-sm btn-outline-secondary" type="button" id="btnClearTarik">Clear</button>
                    </div>
                  </div>
                  <div id="diTarikUnitList" class="unit-list">
                    <div class="text-muted small">Select a contract first...</div>
                  </div>
                  <div class="form-text">Selected units will be removed from the contract (FK relationship removed)</div>
                </div>
              </div>
            </div>

            <!-- Hidden fields for backend validation -->
            <input type="hidden" name="po_kontrak_nomor" id="po_kontrak_nomor">
            <input type="hidden" name="pelanggan" id="pelanggan">

            <!-- Common Fields -->
            <div class="row g-2">
              <div class="col-6"><label class="form-label"><?= lang('Marketing.delivery_date') ?></label><input type="date" class="form-control" name="tanggal_kirim"></div>
              <div class="col-6"><label class="form-label"><?= lang('Marketing.notes') ?></label><input type="text" class="form-control" name="catatan" placeholder="<?= lang('App.optional') ?>"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('App.cancel') ?></button>
            <button class="btn btn-primary" type="submit">Create DI</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Global variables
let allDIData = [];
let filteredDIData = [];
let currentFilter = 'all';
let currentPage = 1;
let entriesPerPage = 10;
let currentDiId = null; // Store current DI ID for edit/delete operations

// Global function for TARIK workflow unit count (must be global for onchange access)
function updateTarikOnlyCount() {
  const checked = document.querySelectorAll('input[name="tarik_units[]"]:checked');
  document.getElementById('tarikOnlyCount').textContent = checked.length;
}

document.addEventListener('DOMContentLoaded', ()=>{
  const tb = document.querySelector('#diTable tbody');
  
  // =====================================================
  // WORKFLOW BARU: DYNAMIC DROPDOWN SYSTEM FROM DATABASE
  // =====================================================
  
  // Load workflow options from database
  let jenisPerintahOptions = [];
  let workflowMapping = {};
  
  // Load jenis perintah from API
  async function loadJenisPerintahOptions() {
    try {
      const response = await fetch('<?= base_url('marketing/get-jenis-perintah-kerja') ?>', {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      });
      const result = await response.json();
      
      if (result.success) {
        jenisPerintahOptions = result.data;
        populateJenisPerintahDropdown();
        console.log('Loaded', result.data.length, 'jenis perintah options');
      } else {
        console.error('Failed to load jenis perintah options:', result.message);
      }
    } catch (error) {
      console.error('Error loading jenis perintah options:', error);
    }
  }
  
  // Populate jenis perintah dropdown
  function populateJenisPerintahDropdown() {
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const editJenisSelect = document.getElementById('editJenisPerintah');
    
    if (jenisSelect) {
      jenisSelect.innerHTML = '<option value="">-- Select Work Order Type --</option>';
      jenisPerintahOptions.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.id;
        optionElement.textContent = `${option.kode} - ${option.nama}`;
        optionElement.title = option.deskripsi;
        jenisSelect.appendChild(optionElement);
      });
      console.log('Populated jenisPerintahSelect with', jenisPerintahOptions.length, 'options');
    }
    
    if (editJenisSelect) {
      editJenisSelect.innerHTML = '<option value="">-- Select Work Order Type --</option>';
      jenisPerintahOptions.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.id;
        optionElement.textContent = `${option.kode} - ${option.nama}`;
        optionElement.title = option.deskripsi;
        editJenisSelect.appendChild(optionElement);
      });
    }
  }
  
  // Load tujuan perintah based on jenis
  async function loadTujuanPerintahOptions(jenisId, targetSelectId) {
    try {
      const response = await fetch(`<?= base_url('marketing/get-tujuan-perintah-kerja') ?>?jenis_id=${jenisId}`, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      });
      const result = await response.json();
      
      if (result.success) {
        const tujuanSelect = document.getElementById(targetSelectId);
        if (tujuanSelect) {
          tujuanSelect.innerHTML = '<option value="">-- Select Destination --</option>';
          tujuanSelect.disabled = false;
          
          result.data.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.id;
            
            // Add visual indicators for workflow type
            let displayText = `${option.kode} - ${option.nama}`;
            let title = option.deskripsi;
            
            // Add workflow type indicators
            if (option.kode === 'TARIK_HABIS_KONTRAK') {
              displayText += ' 🔴';
              title += ' | PERMANENT: Unit disconnected from customer';
            } else if (option.kode === 'TARIK_MAINTENANCE' || option.kode === 'TARIK_RUSAK') {
              displayText += ' 🔵';
              title += ' | TEMPORARY: Unit returns to customer after service';
            } else if (option.kode === 'TARIK_PINDAH_LOKASI') {
              displayText += ' 🟢';
              title += ' | RELOCATION: Same customer, different location';
            } else if (option.kode === 'TUKAR_MAINTENANCE') {
              displayText += ' 🟡';
              title += ' | TEMPORARY REPLACEMENT: Original unit returns after maintenance';
            } else if (option.kode === 'TUKAR_UPGRADE' || option.kode === 'TUKAR_DOWNGRADE' || option.kode === 'TUKAR_RUSAK') {
              displayText += ' 🔴';
              title += ' | PERMANENT: Old unit replaced, new unit takes over';
            }
            
            optionElement.textContent = displayText;
            optionElement.title = title;
            tujuanSelect.appendChild(optionElement);
          });
        }
      } else {
        console.error('Failed to load destination options:', result.message);
      }
    } catch (error) {
      console.error('Error loading destination options:', error);
    }
  }
  
  // Setup dynamic dropdown for form create
  function setupWorkflowDropdowns() {
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const tujuanSelect = document.getElementById('tujuanPerintahSelect');
    
    if (!jenisSelect || !tujuanSelect) return;
    
    jenisSelect.addEventListener('change', function() {
      const jenisValue = this.value;
      
      // Reset tujuan dropdown
      tujuanSelect.innerHTML = '<option value="">-- Select Destination --</option>';
      tujuanSelect.disabled = true;
      
      if (jenisValue) {
        // Load tujuan options from API
        loadTujuanPerintahOptions(jenisValue, 'tujuanPerintahSelect');
      }
      
      // Handle workflow type change with enhanced logic
      handleWorkflowJenisChange();
      
      // Trigger validation
      validateWorkflowForm();
    });
    
    tujuanSelect.addEventListener('change', function() {
      handleWorkflowJenisChange();
      validateWorkflowForm();
    });
  }

  // Enhanced workflow change handler with TARIK and TUKAR support
  function handleWorkflowJenisChange() {
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const jenisText = jenisSelect.selectedOptions[0]?.textContent || '';
    
    // Use text-based detection for workflow types
    const isTukarWorkflow = jenisText.toUpperCase().includes('TUKAR');
    const isTarikWorkflow = jenisText.toUpperCase().includes('TARIK') && !isTukarWorkflow;
    
    console.log('Workflow changed:', { jenisText, isTukarWorkflow, isTarikWorkflow });
    
    // Get all relevant sections
    const spkSection = document.getElementById('spkSelectionSection');
    const kontrakSelection = document.getElementById('diKontrakSelection');
    const tukarWorkflow = document.getElementById('diTukarWorkflow');
    const tarikOnlySection = document.getElementById('diTarikOnlySection');
    const standardUnits = document.getElementById('diUnitsSection');
    const spkPick = document.getElementById('spkPick');
    
    if (isTarikWorkflow) {
      // TARIK workflow: No SPK needed, only kontrak and units
      spkSection.style.display = 'none';
      spkPick.removeAttribute('required');
      spkPick.value = '';
      
      kontrakSelection.style.display = 'block';
      tarikOnlySection.style.display = 'block';
      tukarWorkflow.style.display = 'none';
      standardUnits.style.display = 'none';
      
      // Load kontrak options for TARIK
      loadKontrakOptionsForTarik();
      
    } else if (isTukarWorkflow) {
      // TUKAR workflow: SPK → Jenis/Tujuan → Kontrak → Unit TARIK
      spkSection.style.display = 'block';
      spkPick.setAttribute('required', 'required');
      
      kontrakSelection.style.display = 'block';
      tukarWorkflow.style.display = 'block';
      tarikOnlySection.style.display = 'none';
      standardUnits.style.display = 'none';
      
      // Load kontrak options for TUKAR
      loadKontrakOptionsForTukar();
      
    } else {
      // Standard workflow: SPK → Units
      spkSection.style.display = 'block';
      spkPick.setAttribute('required', 'required');
      
      kontrakSelection.style.display = 'none';
      tukarWorkflow.style.display = 'none';
      tarikOnlySection.style.display = 'none';
      standardUnits.style.display = 'block';
    }
  }

  // Load available contracts for TARIK workflow
  async function loadKontrakOptionsForTarik() {
    try {
      const response = await fetch('<?= base_url('marketing/kontrak/get-active-contracts') ?>', {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      });
      const result = await response.json();
      
      if (result.success) {
        const kontrakSelect = document.getElementById('kontrakSelect');
        
        const optionsHtml = '<option value="">-- Select Contract --</option>' + 
          result.data.map(k => `<option value="${k.id}">${k.no_kontrak} - ${k.pelanggan}</option>`).join('');
        
        kontrakSelect.innerHTML = optionsHtml;
        
        // Setup kontrak change handler for TARIK
        setupKontrakChangeForTarik();
        
        console.log('Loaded', result.data.length, 'contract options for TARIK workflow');
      } else {
        console.error('Failed to load contract options:', result.message);
      }
    } catch (error) {
      console.error('Error loading contract options:', error);
    }
  }

  // Load available contracts for TUKAR workflow
  async function loadKontrakOptionsForTukar() {
    try {
      const response = await fetch('<?= base_url('marketing/kontrak/get-active-contracts') ?>', {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      });
      const result = await response.json();
      
      if (result.success) {
        const kontrakSelect = document.getElementById('kontrakSelect');
        
        const optionsHtml = '<option value="">-- Select Contract --</option>' + 
          result.data.map(k => `<option value="${k.id}">${k.no_kontrak} - ${k.pelanggan}</option>`).join('');
        
        kontrakSelect.innerHTML = optionsHtml;
        
        // Setup kontrak change handler for TUKAR
        setupKontrakChangeHandler();
        
        console.log('Loaded', result.data.length, 'contract options for TUKAR workflow');
      } else {
        console.error('Failed to load contract options:', result.message);
      }
    } catch (error) {
      console.error('Error loading contract options:', error);
    }
  }

  // Setup handler for kontrak selection change (TARIK workflow)
  function setupKontrakChangeForTarik() {
    const kontrakSelect = document.getElementById('kontrakSelect');
    
    kontrakSelect.addEventListener('change', function() {
      if (this.value) {
        // Get selected option text which contains "no_kontrak - pelanggan"
        const selectedOption = this.selectedOptions[0];
        const optionText = selectedOption.textContent;
        
        // Parse no_kontrak and pelanggan from option text
        const parts = optionText.split(' - ');
        const noKontrak = parts[0] || '';
        const pelanggan = parts[1] || '';
        
        // Auto-populate hidden fields for backend validation
        document.getElementById('po_kontrak_nomor').value = noKontrak;
        document.getElementById('pelanggan').value = pelanggan;
        
        console.log(`TARIK Kontrak selected: ${noKontrak} - ${pelanggan}`);
        
        // Load TARIK units only for TARIK workflow
        loadTarikOnlyUnits(this.value);
      } else {
        // Reset hidden fields and list
        document.getElementById('po_kontrak_nomor').value = '';
        document.getElementById('pelanggan').value = '';
        document.getElementById('diTarikOnlyList').innerHTML = '<div class="text-muted small">Select a contract first...</div>';
        document.getElementById('tarikOnlyCount').textContent = '0';
      }
    });
  }

  // Load units for TARIK-only workflow
  async function loadTarikOnlyUnits(kontrakId) {
    try {
      const response = await fetch(`<?= base_url('marketing/kontrak/units/') ?>${kontrakId}`, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      });
      const result = await response.json();
      
      if (result.success && result.data.length > 0) {
        const unitListHtml = result.data.map(unit => `
          <div class="unit-item">
            <input type="checkbox" id="tarikOnly_${unit.id}" name="tarik_units[]" value="${unit.id}" onchange="updateTarikOnlyCount()">
            <label for="tarikOnly_${unit.id}" class="flex-grow-1">
              <strong>${unit.no_unit || 'N/A'}</strong> - ${unit.jenis_unit || unit.merk + ' ' + unit.model || 'N/A'}
              <div class="unit-note">Capacity: ${unit.kapasitas || 'N/A'} | Status: ${unit.status || 'N/A'}</div>
            </label>
          </div>
        `).join('');
        
        document.getElementById('diTarikOnlyList').innerHTML = unitListHtml;
        console.log('Loaded', result.data.length, 'units for TARIK from contract', kontrakId);
      } else {
        document.getElementById('diTarikOnlyList').innerHTML = '<div class="text-muted small">No units in this contract</div>';
      }
    } catch (error) {
      console.error('Error loading TARIK units:', error);
      document.getElementById('diTarikOnlyList').innerHTML = '<div class="text-danger small">Error loading units</div>';
    }
  }

  // Setup handler for kontrak selection change (TUKAR workflow)
  function setupKontrakChangeHandler() {
    const kontrakSelect = document.getElementById('kontrakSelect');
    
    kontrakSelect.addEventListener('change', async function() {
      if (this.value) {
        // Get selected option text which contains "no_kontrak - pelanggan"
        const selectedOption = this.selectedOptions[0];
        const optionText = selectedOption.textContent;
        
        // Parse no_kontrak and pelanggan from option text
        const parts = optionText.split(' - ');
        const noKontrak = parts[0] || '';
        const pelanggan = parts[1] || '';
        
        // Auto-populate hidden fields for backend validation
        document.getElementById('po_kontrak_nomor').value = noKontrak;
        document.getElementById('pelanggan').value = pelanggan;
        
        console.log(`Kontrak selected: ${noKontrak} - ${pelanggan}`);
        
        // Load TARIK units (current units in kontrak) for TUKAR workflow
        loadTukarUnits(this.value);
      } else {
        // Reset hidden fields and unit list
        document.getElementById('po_kontrak_nomor').value = '';
        document.getElementById('pelanggan').value = '';
        document.getElementById('diTarikUnitList').innerHTML = '<div class="text-muted small">Select a contract first...</div>';
        document.getElementById('tarikCount').textContent = '0';
      }
    });
  }

  // Load units for TUKAR workflow: hanya units dari kontrak yang akan ditarik
  async function loadTukarUnits(kontrakId) {
    try {
      // Load units currently in this kontrak (for TARIK in TUKAR workflow)
      const tarikResponse = await fetch(`<?= base_url('marketing/kontrak/units/') ?>${kontrakId}`, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      });
      const tarikResult = await tarikResponse.json();
      
      if (tarikResult.success) {
        // Populate TARIK units (units currently in kontrak) for TUKAR workflow
        populateUnitList(tarikResult.data || [], 'tarik', 'diTarikUnitList', 'tarikCount');
        
        console.log(`Loaded TUKAR units for kontrak ${kontrakId}: ${tarikResult.data?.length || 0} TARIK units`);
      } else {
        console.error('Failed to load TARIK units:', tarikResult);
        document.getElementById('diTarikUnitList').innerHTML = '<div class="text-danger small">Failed to load units from contract.</div>';
        document.getElementById('tarikCount').textContent = '0';
      }
    } catch (error) {
      console.error('Error loading TUKAR units:', error);
      document.getElementById('diTarikUnitList').innerHTML = '<div class="text-danger small">Error loading units</div>';
      document.getElementById('tarikCount').textContent = '0';
    }
  }


  // Generic function to populate unit list for TUKAR workflow
  function populateUnitList(units, type, listId, countId) {
    const list = document.getElementById(listId);
    const count = document.getElementById(countId);
    
    list.innerHTML = '';
    
    if (!units.length) {
      const message = type === 'tarik' ? 
        'No units in this contract to be WITHDRAWN.' : 
        'No units available to be SENT to this contract.';
      list.innerHTML = `<div class="text-warning small">${message}</div>`;
      count.textContent = '0';
      return;
    }
    
    units.forEach((unit, idx) => {
      const wrap = document.createElement('div');
      wrap.className = 'unit-item';
      const unitId = unit.unit_id || unit.id || ('idx'+idx);
      const idSafe = `${type}_unit_${unitId}`;
      
      wrap.innerHTML = `
        <input class="form-check-input unit-check-${type}" type="checkbox" id="${idSafe}" name="${type}_unit_ids[]" value="${unitId}">
        <label for="${idSafe}" class="form-check-label">
          <div><strong>${unit.no_unit || unit.unit_label || unit.label || ('Unit #' + (idx+1))}</strong> - ${unit.jenis_unit || unit.merk + ' ' + unit.model || 'N/A'}</div>
          <div class="unit-note">Capacity: ${unit.kapasitas || 'N/A'} • Status: ${unit.status || 'Available'}</div>
        </label>`;
      list.appendChild(wrap);
    });
    
    // Setup count updates and buttons
    const updateCount = () => {
      const checked = list.querySelectorAll(`.unit-check-${type}:checked`).length;
      count.textContent = String(checked);
      validateWorkflowForm(); // Trigger validation when count changes
    };
    
    list.querySelectorAll(`.unit-check-${type}`).forEach(cb => cb.addEventListener('change', updateCount));
    updateCount();
    
    // Setup select all/clear buttons
    document.getElementById(`btnSelectAll${type.charAt(0).toUpperCase() + type.slice(1)}`).onclick = () => {
      list.querySelectorAll(`.unit-check-${type}`).forEach(cb => cb.checked = true);
      updateCount();
    };
    
    document.getElementById(`btnClear${type.charAt(0).toUpperCase() + type.slice(1)}`).onclick = () => {
      list.querySelectorAll(`.unit-check-${type}`).forEach(cb => cb.checked = false);
      updateCount();
    };
  }

  // Enhanced form validation with correct TUKAR support
  function validateWorkflowForm() {
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const tujuanSelect = document.getElementById('tujuanPerintahSelect');
    const submitBtn = document.querySelector('#diCreateForm [type="submit"]');
    
    if (!jenisSelect || !tujuanSelect || !submitBtn) return;
    
    const jenisText = jenisSelect.selectedOptions[0]?.textContent || '';
    const isTukarWorkflow = jenisText.toUpperCase().includes('TUKAR');
    
    const jenisValid = jenisSelect.value !== '';
    const tujuanValid = tujuanSelect.value !== '';
    let additionalValid = true;
    
    // Additional validation for TUKAR workflow
    if (isTukarWorkflow && jenisValid && tujuanValid) {
      const kontrakSelect = document.getElementById('kontrakSelect');
      const tarikCount = parseInt(document.getElementById('tarikCount')?.textContent || '0');
      
      additionalValid = kontrakSelect.value !== '' && tarikCount > 0;
      
      // Visual feedback for TUKAR-specific fields
      kontrakSelect.classList.toggle('is-invalid', kontrakSelect.value === '');
      kontrakSelect.classList.toggle('is-valid', kontrakSelect.value !== '');
    }
    
    const isValid = jenisValid && tujuanValid && additionalValid;
    
    // Visual feedback
    jenisSelect.classList.toggle('is-invalid', !jenisValid && jenisSelect.value !== '');
    jenisSelect.classList.toggle('is-valid', jenisValid);
    
    tujuanSelect.classList.toggle('is-invalid', !tujuanValid && tujuanSelect.value !== '');
    tujuanSelect.classList.toggle('is-valid', tujuanValid);
    
    // Enable/disable submit button
    submitBtn.disabled = !isValid;
  }
  
  // Setup dropdown untuk edit form
  function setupEditWorkflowDropdowns() {
    const editJenisSelect = document.getElementById('editJenisPerintah');
    const editTujuanSelect = document.getElementById('editTujuanPerintah');
    
    if (!editJenisSelect || !editTujuanSelect) return;
    
    editJenisSelect.addEventListener('change', function() {
      const jenisValue = this.value;
      
      // Reset tujuan dropdown
      editTujuanSelect.innerHTML = '<option value="">-- Select Purpose --</option>';
      editTujuanSelect.disabled = true;
      
      if (jenisValue) {
        // Load tujuan options from API
        loadTujuanPerintahOptions(jenisValue, 'editTujuanPerintah');
      }
    });
  }

  
  // Initialize workflow dropdowns and load data
  setupWorkflowDropdowns();
  setupEditWorkflowDropdowns();
  
  // Load dropdown data when modal is shown
  document.getElementById('diCreateModal').addEventListener('shown.bs.modal', function() {
    console.log('Enhanced DI Create Modal shown, loading dropdown data...');
    loadJenisPerintahOptions();
  });
  
  // Also load on page load as backup
  loadJenisPerintahOptions();
  
  // Reset form saat modal ditutup
  document.getElementById('diCreateModal').addEventListener('hidden.bs.modal', function() {
    const form = document.getElementById('diCreateForm');
    if (form) {
      form.reset();
      form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
      });
      
      // Reset tujuan dropdown
      const tujuanSelect = document.getElementById('tujuanPerintahSelect');
      if (tujuanSelect) {
        tujuanSelect.innerHTML = '<option value="">-- Select Command Type first --</option>';
        tujuanSelect.disabled = true;
      }
      
      // Reset workflow sections with correct IDs
      document.getElementById('diKontrakSelection').style.display = 'none';
      document.getElementById('diTukarWorkflow').style.display = 'none';
      document.getElementById('diTarikOnlySection').style.display = 'none';
      document.getElementById('diUnitsSection').style.display = 'none';
      
      // Reset unit lists and counts
      document.getElementById('diTarikUnitList').innerHTML = '<div class="text-muted small">Select a contract first...</div>';
      document.getElementById('diTarikOnlyList').innerHTML = '<div class="text-muted small">Select a contract first...</div>';
      document.getElementById('tarikCount').textContent = '0';
      document.getElementById('tarikOnlyCount').textContent = '0';
      
      // Reset submit button
      const submitBtn = form.querySelector('[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;
    }
  });
  
  // =====================================================
  // END WORKFLOW BARU
  // =====================================================
  
  function loadDI(startDate = null, endDate = null){
    let url = '<?= base_url('marketing/di/list') ?>';
    if (startDate && endDate) {
      url += `?start_date=${startDate}&end_date=${endDate}`;
    }
    fetch(url).then(r=>r.json()).then(j=>{
      allDIData = j.data || [];
      updateStatistics();
      applyFilters();
    }).catch(error => {
      console.error('Error loading DI data:', error);
    });
  }
  
  // Load initial data
  loadDI();
  
  function updateStatistics() {
    const total = allDIData.length;
    
    // Update statistik menggunakan status_di
    const direncanakan = allDIData.filter(item => {
      const status = (item.status_di || '').toUpperCase();
      return status === 'DIAJUKAN';
    }).length;
    
    const persiapanUnit = allDIData.filter(item => {
      const status = (item.status_di || '').toUpperCase();
      return status === 'DISETUJUI' || status === 'PERSIAPAN_UNIT' || status === 'SIAP_KIRIM';
    }).length;
    
    const dalamPerjalanan = allDIData.filter(item => {
      const status = (item.status_di || '').toUpperCase();
      return status === 'DALAM_PERJALANAN';
    }).length;
    
    const selesai = allDIData.filter(item => {
      const status = (item.status_di || '').toUpperCase();
      return status === 'SELESAI' || status === 'SAMPAI_LOKASI';
    }).length;
    
    const awaitingContract = allDIData.filter(item => {
      const status = (item.status_di || '').toUpperCase();
      return status === 'AWAITING_CONTRACT';
    }).length;
    
    document.getElementById('totalDI').textContent = total;
    document.getElementById('submittedDI').textContent = direncanakan;
    document.getElementById('inprogressDI').textContent = dalamPerjalanan;
    document.getElementById('deliveredDI').textContent = selesai;
    document.getElementById('awaitingContractDI').textContent = awaitingContract;
  }
  
  function applyFilters() {
    const searchTerm = document.getElementById('diSearch').value.toLowerCase();
    
    // Filter berdasarkan status_di - map between English and Indonesian status terms
    let filtered;
    if (currentFilter === 'all') {
      filtered = [...allDIData];
    } else if (currentFilter === 'SUBMITTED') {
      filtered = allDIData.filter(item => {
        const status = (item.status_di || '').toUpperCase();
        return status === 'DIAJUKAN';
      });
    } else if (currentFilter === 'INPROGRESS') {
      filtered = allDIData.filter(item => {
        const status = (item.status_di || '').toUpperCase();
        return status === 'DISETUJUI' || 
               status === 'PERSIAPAN_UNIT' ||
               status === 'SIAP_KIRIM' ||
               status === 'DALAM_PERJALANAN';
      });
    } else if (currentFilter === 'DELIVERED') {
      filtered = allDIData.filter(item => {
        const status = (item.status_di || '').toUpperCase();
        return status === 'SAMPAI_LOKASI' || status === 'SELESAI';
      });
    } else if (currentFilter === 'AWAITING_CONTRACT') {
      filtered = allDIData.filter(item => {
        const status = (item.status_di || '').toUpperCase();
        return status === 'AWAITING_CONTRACT';
      });
    } else if (currentFilter === 'CANCELLED') {
      filtered = allDIData.filter(item => {
        const status = (item.status_di || '').toUpperCase();
        return status === 'DIBATALKAN';
      });
    } else {
      // Exact match untuk filter status spesifik
      filtered = allDIData.filter(item => (item.status_di || '').toUpperCase() === currentFilter);
    }
    
    // Filter by search term
    if (searchTerm) {
      filtered = filtered.filter(item => {
        return (item.nomor_di || '').toLowerCase().includes(searchTerm) ||
               (item.spk_id || '').toLowerCase().includes(searchTerm) ||
               (item.po_kontrak_nomor || '').toLowerCase().includes(searchTerm) ||
               (item.pelanggan || '').toLowerCase().includes(searchTerm) ||
               (item.jenis_perintah || '').toLowerCase().includes(searchTerm) ||
               (item.tujuan_perintah || '').toLowerCase().includes(searchTerm) ||
               (item.spk_pic || '').toLowerCase().includes(searchTerm) ||
               (item.lokasi || '').toLowerCase().includes(searchTerm);
      });
    }
    
    filteredDIData = filtered;
    currentPage = 1; // Reset to first page
    renderDITable();
    updatePagination();
  }
  
  function renderDITable() {
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = startIndex + entriesPerPage;
    const dataToShow = filteredDIData.slice(startIndex, endIndex);
    
    tb.innerHTML = '';
    dataToShow.forEach(r=>{
      const tr = document.createElement('tr');
      // Display status values with appropriate badge colors
      const getStatusDisplay = (status, createdDate) => {
        const statusUpper = (status || '').toUpperCase();
        const statusMap = {
          'DIAJUKAN': { text: 'DIAJUKAN', color: 'secondary' },
          'DISETUJUI': { text: 'DISETUJUI', color: 'info' },
          'PERSIAPAN_UNIT': { text: 'PERSIAPAN_UNIT', color: 'warning' },
          'SIAP_KIRIM': { text: 'SIAP_KIRIM', color: 'primary' },
          'DALAM_PERJALANAN': { text: 'DALAM_PERJALANAN', color: 'warning' },
          'SAMPAI_LOKASI': { text: 'SAMPAI_LOKASI', color: 'success' },
          'SELESAI': { text: 'SELESAI', color: 'success' },
          'DIBATALKAN': { text: 'DIBATALKAN', color: 'danger' },
          'AWAITING_CONTRACT': { text: 'AWAITING CONTRACT', color: 'warning' }
        };
        const mapped = statusMap[statusUpper] || { text: status || 'DIAJUKAN', color: 'secondary' };
        
        // Calculate days pending for AWAITING_CONTRACT status
        if (statusUpper === 'AWAITING_CONTRACT' && createdDate) {
          const created = new Date(createdDate);
          const now = new Date();
          const daysPending = Math.floor((now - created) / (1000 * 60 * 60 * 24));
          const urgencyColor = daysPending > 14 ? 'danger' : (daysPending > 7 ? 'warning' : 'info');
          return `<span class="badge bg-${mapped.color}">${mapped.text}</span> <span class="badge bg-${urgencyColor}" title="Days waiting for contract">${daysPending}d</span>`;
        }
        
        return `<span class="badge bg-${mapped.color}">${mapped.text}</span>`;
      };
      
      // Function to format total units display
      const formatTotalUnits = (r) => {
        const totalUnits = r.total_units || 0;
        const totalAttachments = r.total_attachments || 0;
        const jenisSpk = r.jenis_spk || 'UNIT'; // Use jenis_spk from delivery_instructions
        const hasTemporary = r.has_temporary_units || false;
        
        let unitsDisplay = '';
        
        if (jenisSpk === 'ATTACHMENT') {
          // For ATTACHMENT SPK, prioritize attachments count
          if (totalAttachments > 0) {
            unitsDisplay = `<span class="badge bg-warning">${totalAttachments} Attachment</span>`;
          } else {
            unitsDisplay = '<span class="badge bg-secondary">No attachments</span>';
          }
        } else {
          // For UNIT SPK, prioritize units count
          if (totalUnits > 0) {
            unitsDisplay = `<span class="badge bg-primary">${totalUnits} Unit</span>`;
          } else if (totalAttachments > 0) {
            // Fallback to attachments if no units but has attachments
            unitsDisplay = `<span class="badge bg-warning">${totalAttachments} Attachment</span>`;
          } else {
            unitsDisplay = '<span class="badge bg-secondary">0</span>';
          }
        }
        
        // Add temporary indicator badge if has temporary units
        if (hasTemporary) {
          unitsDisplay += ' <span class="badge bg-warning-subtle text-warning border border-warning" title="Contains temporary units (TUKAR_MAINTENANCE)">🔄 TEMP</span>';
        }
        
        return unitsDisplay;
      };
      
      // Function to add workflow indicators to tujuan
      const formatTujuanWithIndicator = (tujuan) => {
        if (!tujuan) return '-';
        
        let indicator = '';
        let tooltip = '';
        
        if (tujuan.includes('HABIS_KONTRAK') || tujuan.includes('Habis Kontrak')) {
          indicator = '🔴';
          tooltip = 'PERMANENT: Unit disconnected from customer';
        } else if (tujuan.includes('MAINTENANCE') || tujuan.includes('Maintenance')) {
          if (tujuan.includes('TUKAR') || tujuan.includes('Ganti')) {
            indicator = '🟡';
            tooltip = 'TEMPORARY REPLACEMENT: Original unit returns after maintenance';
          } else {
            indicator = '🔵';
            tooltip = 'TEMPORARY: Unit returns after service';
          }
        } else if (tujuan.includes('RUSAK') || tujuan.includes('Rusak')) {
          if (tujuan.includes('TUKAR') || tujuan.includes('Ganti')) {
            indicator = '🔴';
            tooltip = 'PERMANENT: Unit replaced';
          } else {
            indicator = '🔵';
            tooltip = 'TEMPORARY: Unit returns after repair';
          }
        } else if (tujuan.includes('PINDAH_LOKASI') || tujuan.includes('Pindah')) {
          indicator = '🟢';
          tooltip = 'RELOCATION: Same customer, different location';
        } else if (tujuan.includes('UPGRADE') || tujuan.includes('DOWNGRADE')) {
          indicator = '🔴';
          tooltip = 'PERMANENT: Unit replaced';
        }
        
        return indicator ? `<span title="${tooltip}">${indicator}</span> ${tujuan}` : tujuan;
      };
      
      // Check if DI has contract - show Link button if not
      const hasContract = r.contract_id !== null && r.contract_id !== '';
      let actionsHtml = '-';
      
      if (!hasContract && r.status_di !== 'DIBATALKAN') {
        // Show Link Contract button for DI without contract
        actionsHtml = `<button class="btn btn-sm btn-outline-warning link-di-contract" 
          data-di-id="${r.id}" 
          data-di-number="${r.nomor_di}" 
          title="Link to Contract">
          <i class="fas fa-link"></i> Link
        </button>`;
      } else if (hasContract) {
        actionsHtml = '<span class="badge bg-success"><i class="fas fa-check"></i> Linked</span>';
      }
      
      // Enhanced PO/Contract display with contract status indicator
      const contractDisplay = hasContract 
        ? `${r.po_kontrak_nomor || '-'} <span class="badge bg-success-subtle text-success" title="Contract linked"><i class="fas fa-link"></i></span>`
        : `${r.po_kontrak_nomor ||  '-'} <span class="badge bg-warning text-dark" title="No contract linked - invoice generation disabled">NO CONTRACT</span>`;
      
      tr.innerHTML = `
        <td><a href="#" onclick="openDiDetail(${r.id});return false;">${r.nomor_di}</a></td>
        <td>${r.spk_id || '-'}</td>
        <td>${contractDisplay}</td>
        <td>${r.pelanggan||'-'}</td>
        <td>${r.lokasi||'-'}</td>
        <td><span class="text-muted small">${formatTotalUnits(r)}</span></td>
        <td>${r.jenis_perintah || '-'}</td>
        <td>${formatTujuanWithIndicator(r.tujuan_perintah)}</td>
        <td>${r.tanggal_kirim||'-'}</td>
        <td>${getStatusDisplay(r.status_di, r.dibuat_pada)}</td>
        <td>${actionsHtml}</td>`;
      tb.appendChild(tr);
    });
    
    // Update table info
    const totalEntries = filteredDIData.length;
    const start = totalEntries === 0 ? 0 : startIndex + 1;
    const end = Math.min(endIndex, totalEntries);
    document.getElementById('diTableInfo').textContent = 
      `Showing ${start} to ${end} of ${totalEntries} entries`;
  }
  
  function updatePagination() {
    const totalPages = Math.ceil(filteredDIData.length / entriesPerPage);
    const pagination = document.getElementById('diPagination');
    pagination.innerHTML = '';
    
    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = '<a class="page-link" href="#" onclick="changePage(' + (currentPage - 1) + ')">Previous</a>';
    pagination.appendChild(prevLi);
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
      const li = document.createElement('li');
      li.className = `page-item ${currentPage === i ? 'active' : ''}`;
      li.innerHTML = '<a class="page-link" href="#" onclick="changePage(' + i + ')">' + i + '</a>';
      pagination.appendChild(li);
    }
    
    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}`;
    nextLi.innerHTML = '<a class="page-link" href="#" onclick="changePage(' + (currentPage + 1) + ')">Next</a>';
    pagination.appendChild(nextLi);
  }
  
  window.changePage = function(page) {
    const totalPages = Math.ceil(filteredDIData.length / entriesPerPage);
    if (page >= 1 && page <= totalPages) {
      currentPage = page;
      renderDITable();
      updatePagination();
    }
  }
  
  // Event listeners
  document.getElementById('entriesPerPage').addEventListener('change', function() {
    entriesPerPage = parseInt(this.value);
    currentPage = 1;
    renderDITable();
    updatePagination();
  });
  
  document.getElementById('diSearch').addEventListener('input', function() {
    applyFilters();
  });
  
  // Filter card click listeners
  document.querySelectorAll('.filter-card').forEach(card => {
    card.addEventListener('click', function() {
      const filter = this.dataset.filter;
      currentFilter = filter;
      
      // Update active card
      document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      
      applyFilters();
    });
  });
  
  // Add filter tab click listeners
  document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      const filter = this.dataset.filter;
      currentFilter = filter;
      
      // Update active tab
      document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      
      // Update active card
      document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
      const correspondingCard = document.querySelector(`[data-filter="${filter}"]`);
      if (correspondingCard) {
        correspondingCard.classList.add('active');
      }
      
      applyFilters();
    });
  });
  
  // Event delegation untuk tombol Link DI to Contract (dynamically created)
  document.getElementById('diTable').addEventListener('click', function(e) {
    const btn = e.target.closest('.link-di-contract');
    if (btn) {
      e.preventDefault();
      const diId = btn.dataset.diId;
      const diNumber = btn.dataset.diNumber;
      openLinkDIContractModal(diId, diNumber);
    }
  });
  
  loadDI();

  let currentDiJenis = ''; // Store jenis_perintah for SPPU
  
  window.openDiDetail = (id) => {
    currentDiId = id; // Store current DI ID
    const modal = new bootstrap.Modal(document.getElementById('diDetailModal'));
    const body = document.getElementById('diDetailBody');
    const btnSppu = document.getElementById('btnPrintSppu');
    body.innerHTML = '<p class="text-muted">Loading...</p>';
    btnSppu.style.display = 'none'; // Hide SPPU button by default
    
    fetch('<?= base_url('marketing/di/detail/') ?>'+id).then(r=>r.json()).then(j=>{
      if (!j.success) { body.innerHTML = '<div class="text-danger">Failed to load details</div>'; modal.show(); return; }
      const d = j.data||{}; const spk = j.spk||{}; const items = j.items||[];
      
      // Store jenis_perintah and show SPPU button for TARIK/TUKAR
      currentDiJenis = (d.jenis_perintah || '').toUpperCase();
      if (currentDiJenis === 'TARIK' || currentDiJenis === 'TUKAR') {
        btnSppu.style.display = 'inline-block';
      } 
      
      // Parse spesifikasi JSON if exists
      let spesifikasi = {};
      if (spk.spesifikasi) {
        try {
          spesifikasi = JSON.parse(spk.spesifikasi);
        } catch (e) {
          console.log('Failed to parse spesifikasi:', e);
        }
      }
      
      // Enhanced: Detect SPK type for proper detail display
      const spkType = spk.jenis_spk || 'UNIT';
      const isAttachmentSpk = (spkType === 'ATTACHMENT');
      
      // Build detailed items display with professional design
      let itemsDetailHtml = '';
      if (items.length > 0) {
        const unitItems = items.filter(i => i.item_type === 'UNIT');
        const attachmentItems = items.filter(i => i.item_type === 'ATTACHMENT');
        
        itemsDetailHtml = '<div class="mt-3">';
        
        if (isAttachmentSpk) {
          // For ATTACHMENT SPK, focus on attachments
          itemsDetailHtml += '<h6 class="text-muted mb-3">Attachments Sent:</h6>';
          if (attachmentItems.length > 0) {
            itemsDetailHtml += '<div class="list-group list-group-flush">';
            attachmentItems.forEach(item => {
              // Use item data first, fallback to spesifikasi if null
              const attachName = item.att_tipe || spesifikasi.attachment_tipe || 'Attachment';
              const attachMerk = item.att_merk || spesifikasi.attachment_merk || '-';
              const attachModel = item.att_model || '';
              const fullAttachmentName = attachModel ? `${attachName} ${attachModel}` : attachName;
              
              itemsDetailHtml += `
                <div class="list-group-item border-0 px-0">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-paperclip text-primary me-3"></i>
                    <div>
                      <div class="fw-semibold">${fullAttachmentName}</div>
                      <small class="text-muted">Merk: ${attachMerk}</small>
                    </div>
                  </div>
                </div>`;
            });
            itemsDetailHtml += '</div>';
          } else {
            // Fallback to spesifikasi data for ATTACHMENT SPK when no items
            const attachType = spesifikasi.attachment_tipe || 'Attachment';
            const attachMerk = spesifikasi.attachment_merk || '-';
            itemsDetailHtml += `
              <div class="card border-info">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-paperclip text-info me-3"></i>
                    <div>
                      <div class="fw-semibold">${attachType}</div>
                      <small class="text-muted">Merk: ${attachMerk}</small>
                    </div>
                  </div>
                </div>
              </div>`;
          }
        } else {
          // For UNIT SPK, group units with their attachments
          if (unitItems.length > 0) {
            itemsDetailHtml += '<h6 class="text-muted mb-3">Units Sent:</h6>';
            itemsDetailHtml += '<div class="list-group list-group-flush">';
            
            unitItems.forEach(unit => {
              const unitLabel = unit.no_unit || unit.unit_label || 'Unit';
              const unitMerk = unit.merk_unit || '-';
              const unitModel = unit.model_unit || '';
              const unitSN = unit.serial_number ? ` (SN: ${unit.serial_number})` : '';
              
              // Find attachments for this unit (if parent_unit_id matches)
              const unitAttachments = attachmentItems.filter(att => 
                att.parent_unit_id == unit.unit_id || att.parent_unit_id == unit.id
              );
              
              itemsDetailHtml += `
                <div class="list-group-item border-0 px-0">
                  <div class="d-flex align-items-start">
                    <i class="bi bi-truck text-success me-3 mt-1"></i>
                    <div class="flex-grow-1">
                      <div class="fw-semibold">${unitLabel} - ${unitMerk} ${unitModel}${unitSN}</div>`;
              
              // Show attached items for this unit
              if (unitAttachments.length > 0) {
                itemsDetailHtml += '<div class="mt-2 ms-3">';
                unitAttachments.forEach(att => {
                  const attachName = att.att_tipe || 'Attachment';
                  const attachMerk = att.att_merk || '-';
                  itemsDetailHtml += `
                    <div class="d-flex align-items-center mb-1">
                      <i class="bi bi-plus-circle text-primary me-2"></i>
                      <small><strong>${attachName}</strong> (${attachMerk})</small>
                    </div>`;
                });
                itemsDetailHtml += '</div>';
              }
              
              itemsDetailHtml += `
                    </div>
                  </div>
                </div>`;
            });
            itemsDetailHtml += '</div>';
          }
          
          // Show standalone attachments (not linked to any unit)
          const standaloneAttachments = attachmentItems.filter(att => 
            !att.parent_unit_id || !unitItems.some(unit => unit.unit_id == att.parent_unit_id || unit.id == att.parent_unit_id)
          );
          
          if (standaloneAttachments.length > 0) {
            itemsDetailHtml += '<h6 class="text-muted mb-3 mt-4">Additional Attachments:</h6>';
            itemsDetailHtml += '<div class="list-group list-group-flush">';
            standaloneAttachments.forEach(item => {
              const attachName = item.att_tipe || 'Attachment';
              const attachMerk = item.att_merk || '-';
              itemsDetailHtml += `
                <div class="list-group-item border-0 px-0">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-paperclip text-warning me-3"></i>
                    <div>
                      <div class="fw-semibold">${attachName}</div>
                      <small class="text-muted">Merk: ${attachMerk}</small>
                    </div>
                  </div>
                </div>`;
            });
            itemsDetailHtml += '</div>';
          }
        }
        
        itemsDetailHtml += '</div>';
      } else {
        itemsDetailHtml = '<div class="alert alert-warning border-warning"><small><i class="bi bi-exclamation-triangle me-2"></i>No items prepared for this shipment yet.</small></div>';
      }
      
      // Enhanced detail display with professional design
      body.innerHTML = `
        <div class="row g-3">
          <!-- Basic Information -->
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-primary">
              <i class="bi bi-clipboard-data me-2"></i>Document & Customer Information
            </h6>
            <div class="row g-2">
              <div class="col-6"><strong>DI Number:</strong> ${d.nomor_di}</div>
              <div class="col-6"><strong>Status:</strong> <span class="badge bg-primary">${d.status}</span></div>
              <div class="col-6"><strong>SPK Number:</strong> ${spk.nomor_spk||'-'}</div>
              <div class="col-6"><strong>SPK Type:</strong> <span class="badge ${isAttachmentSpk ? 'bg-warning' : 'bg-info'}">${spkType}</span></div>
              <div class="col-6"><strong>PO/Contract:</strong> ${d.po_kontrak_nomor||'-'}</div>
              <div class="col-6"><strong>Shipping Date:</strong> ${d.tanggal_kirim||'-'}</div>
              <div class="col-6"><strong>Company Name:</strong> ${d.pelanggan||'-'}</div>
              <div class="col-6"><strong>PIC:</strong> ${spk.pic||'-'}</div>
              <div class="col-12"><strong>Shipping Location:</strong><br><small class="text-muted">${d.lokasi||'-'}</small></div>
            </div>
          </div>
          
          <!-- Workflow Information -->
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-info">
              <i class="bi bi-gear me-2"></i>Workflow Information
            </h6>
            <div class="row g-2">
              <div class="col-6"><strong>Order Type:</strong> ${d.jenis_perintah||'-'}</div>
              <div class="col-6"><strong>Order Destination:</strong> ${d.tujuan_perintah||'-'}</div>
              <div class="col-6"><strong>Contact:</strong> ${spk.kontak||'-'}</div>
              <div class="col-6"><strong>Execution Status:</strong> ${d.status_eksekusi||'Pending'}</div>
            </div>
          </div>
          
          <!-- Items Detail -->
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-success">
              <i class="bi ${isAttachmentSpk ? 'bi-paperclip' : 'bi-truck'} me-2"></i>Detail Items Sent
            </h6>
            ${itemsDetailHtml}
          </div>
          
          <!-- Transportation Information (if available) -->
          ${d.nama_supir || d.kendaraan || d.no_polisi_kendaraan ? `
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-warning">
              <i class="bi bi-truck me-2"></i>Transportation Information
            </h6>
            <div class="row g-2">
              <div class="col-6"><strong>Driver Name:</strong> ${d.nama_supir||'[Not provided]'}</div>
              <div class="col-6"><strong>Driver Phone:</strong> ${d.no_hp_supir||'[Not provided]'}</div>
              <div class="col-6"><strong>Vehicle:</strong> ${d.kendaraan||'[Not provided]'}</div>
              <div class="col-6"><strong>License Plate:</strong> ${d.no_polisi_kendaraan||'[Not provided]'}</div>
            </div>
          </div>
          ` : ''}
          
          <!-- Notes -->
          ${d.catatan ? `
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-secondary">
              <i class="bi bi-sticky me-2"></i>Special Notes
            </h6>
            <div class="alert alert-light border"><small>${d.catatan}</small></div>
          </div>
          ` : ''}
        </div>`;
      modal.show();
    });
  }

  const spkPick = document.getElementById('spkPick');
  function loadReadySpk(q){
    const url = new URL('<?= base_url('marketing/spk/ready-options') ?>', window.location.origin);
    if (q) url.searchParams.set('q', q);
    fetch(url).then(r=>r.json()).then(j=>{
      spkPick.innerHTML = '<option value="">- Select SPK -</option>' + (j.data||[]).map(x=>`<option value="${x.id}">${x.label}</option>`).join('');
    });
  }
  loadReadySpk('');
  document.getElementById('modalSpkSearch').addEventListener('input', e=> loadReadySpk(e.target.value.trim()));
  // When a SPK is picked, load prepared units for selection
  document.getElementById('spkPick').addEventListener('change', (e)=>{
    const id = e.target.value;
    const section = document.getElementById('diUnitsSection');
    const list = document.getElementById('diUnitList');
    const selCount = document.getElementById('selCount');
    if (!id) { section.style.display='none'; list.innerHTML=''; return; }
    section.style.display='block';
    list.innerHTML = '<div class="text-muted small">Loading items from SPK...</div>';
    fetch(`<?= base_url('marketing/spk/detail/') ?>${id}`).then(r=>r.json()).then(j=>{
      // ENHANCEMENT: Detect SPK type for proper item display
      const spkType = j && j.jenis_spk ? j.jenis_spk.toUpperCase() : 'UNIT';
      const isAttachmentSpk = (spkType === 'ATTACHMENT');
      
      console.log('SPK Type detected:', spkType, 'isAttachment:', isAttachmentSpk);
      
      // Handle different SPK types differently
      if (isAttachmentSpk) {
        // Update UI labels for ATTACHMENT SPK
        document.getElementById('itemSelectionLabel').textContent = 'Select Attachments to Send';
        document.getElementById('itemTypeLabel').textContent = 'attachment';
        document.getElementById('itemSelectionHelp').textContent = 'Attachments to be sent with this DI.';
        
        // DEBUG: Log the full spesifikasi structure
        console.log('🔍 DEBUG SPK ATTACHMENT spesifikasi:', j.spesifikasi);
        console.log('🔍 DEBUG selected:', j.spesifikasi?.selected);
        
        // For ATTACHMENT SPK, check selected attachment from spesifikasi
        const selected = j && j.spesifikasi && j.spesifikasi.selected ? j.spesifikasi.selected : {};
        
        // Check multiple possible attachment data locations
        let attachmentData = null;
        if (selected.attachment) {
          attachmentData = selected.attachment;
          console.log('✅ Found attachment in selected.attachment:', attachmentData);
        } else if (selected.inventory_attachment_id && j.spesifikasi.selected) {
          // Try to use inventory_attachment_id if available
          attachmentData = {
            id: selected.inventory_attachment_id,
            label: 'Attachment Item',
            tipe: 'Attachment',
            merk: '-'
          };
          console.log('✅ Found attachment via inventory_attachment_id:', selected.inventory_attachment_id);
        } else if (j.spesifikasi.attachment_merk || j.spesifikasi.attachment_tipe) {
          // Fallback to basic attachment info from spesifikasi
          attachmentData = {
            id: 'att_' + (j.data?.id || '1'),
            label: j.spesifikasi.attachment_merk || 'Attachment Item',
            tipe: j.spesifikasi.attachment_tipe || 'Attachment',
            merk: j.spesifikasi.attachment_merk || '-'
          };
          console.log('✅ Created attachment from spesifikasi fields:', attachmentData);
        }
        
        if (attachmentData) {
          // Show single attachment item for ATTACHMENT SPK
          list.innerHTML = '';
          const wrap = document.createElement('div');
          wrap.className = 'unit-item';
          const attachId = attachmentData.id || 'att1';
          const idSafe = `attachment_${attachId}`;
          
          wrap.innerHTML = `
            <input class="form-check-input unit-check" type="checkbox" id="${idSafe}" name="attachment_ids[]" value="${attachId}" checked>
            <label for="${idSafe}" class="form-check-label">
              <div><strong>📎 ${attachmentData.label || 'Attachment'}</strong></div>
              <div class="unit-note">Type: ${attachmentData.tipe || '-'} | Merk: ${attachmentData.merk || '-'}</div>
            </label>`;
          list.appendChild(wrap);
          
          selCount.textContent = '1';
          document.getElementById('btnSelectAll').onclick = ()=>{ document.getElementById(idSafe).checked=true; selCount.textContent = '1'; };
          document.getElementById('btnClearAll').onclick = ()=>{ document.getElementById(idSafe).checked=false; selCount.textContent = '0'; };
          document.getElementById(idSafe).addEventListener('change', ()=>{ selCount.textContent = document.getElementById(idSafe).checked ? '1' : '0'; });
          
          console.log('✅ ATTACHMENT SPK items loaded:', attachmentData.label);
        } else {
          list.innerHTML = '<div class="text-danger small">No attachments prepared for this ATTACHMENT SPK.</div>';
          selCount.textContent = '0';
        }
      } else {
        // Update UI labels for UNIT SPK
        document.getElementById('itemSelectionLabel').textContent = 'Select Units to Send';
        document.getElementById('itemTypeLabel').textContent = 'unit';
        document.getElementById('itemSelectionHelp').textContent = 'If the SPK has 3 units, you can select how many units will be sent with this DI.';
        
        // Standard UNIT SPK handling
        const details = (j && j.spesifikasi && Array.isArray(j.spesifikasi.prepared_units_detail)) ? j.spesifikasi.prepared_units_detail : [];
        if (!details.length){ list.innerHTML = '<div class="text-danger small">No units prepared for this SPK.</div>'; selCount.textContent = '0'; return; }
        list.innerHTML = '';
        details.forEach((it, idx)=>{
          // Skip null/undefined items
          if (!it || typeof it !== 'object') {
            console.warn('Skipping invalid unit item:', it);
            return;
          }
          
          const wrap = document.createElement('div');
          wrap.className = 'unit-item';
          const unitId = it.unit_id || ('idx'+idx);
          const idSafe = `unit_${unitId}`;
          
          // Debug log the unit ID value
          console.log(`Unit in form: ID=${it.unit_id}, Label=${it.unit_label||('Unit #' + (idx+1))}`);
          
          // Build attachment label safely
          let attachmentText = '';
          if (it.attachment_label && typeof it.attachment_label === 'string') {
            attachmentText = ` &nbsp; • &nbsp; ${it.attachment_label}`;
          }
          
          // Check if unit is in active DI
          const isInActiveDI = it.is_in_active_di || false;
          const activeDI = it.active_di_info || null;
          const disabled = isInActiveDI ? 'disabled' : '';
          const checked = isInActiveDI ? '' : 'checked';
          const warningBadge = isInActiveDI && activeDI ? ` <span class="badge bg-warning text-dark">Already in ${activeDI.nomor_di}</span>` : '';
          
          wrap.innerHTML = `
            <input class="form-check-input unit-check" type="checkbox" id="${idSafe}" name="unit_ids[]" value="${unitId}" ${checked} ${disabled}>
            <label for="${idSafe}" class="form-check-label">
              <div><strong>${it.unit_label || ('Unit #' + (idx+1))}</strong>${warningBadge}</div>
              <div class="unit-note">SN: ${it.serial_number || '-'}${attachmentText}</div>
            </label>`;
          list.appendChild(wrap);
        });
        const updateSel = ()=>{ const n = list.querySelectorAll('.unit-check:checked').length; selCount.textContent = String(n); };
        list.querySelectorAll('.unit-check').forEach(cb=> cb.addEventListener('change', updateSel));
        updateSel();
        document.getElementById('btnSelectAll').onclick = ()=>{ list.querySelectorAll('.unit-check:not(:disabled)').forEach(cb=> cb.checked=true); selCount.textContent = String(list.querySelectorAll('.unit-check:checked').length); };
        document.getElementById('btnClearAll').onclick = ()=>{ list.querySelectorAll('.unit-check:not(:disabled)').forEach(cb=> cb.checked=false); selCount.textContent = '0'; };
      }
    });
  });

  // Setup button handlers for TARIK-only workflow
  function setupTarikOnlyButtons() {
    document.getElementById('btnSelectAllTarikOnly').onclick = () => {
      document.querySelectorAll('input[name="tarik_units[]"]').forEach(cb => cb.checked = true);
      updateTarikOnlyCount();
    };
    
    document.getElementById('btnClearTarikOnly').onclick = () => {
      document.querySelectorAll('input[name="tarik_units[]"]').forEach(cb => cb.checked = false);
      updateTarikOnlyCount();
    };
  }

  // Setup button handlers for TUKAR workflow
  function setupTukarButtons() {
    document.getElementById('btnSelectAllTarik').onclick = () => {
      document.querySelectorAll('.unit-check-tarik').forEach(cb => cb.checked = true);
      // Update count manually
      const checked = document.querySelectorAll('.unit-check-tarik:checked').length;
      document.getElementById('tarikCount').textContent = String(checked);
    };
    
    document.getElementById('btnClearTarik').onclick = () => {
      document.querySelectorAll('.unit-check-tarik').forEach(cb => cb.checked = false);
      document.getElementById('tarikCount').textContent = '0';
    };
  }

  // Call setup functions when DOM is ready
  setupTarikOnlyButtons();
  setupTukarButtons();

  document.getElementById('diCreateForm').addEventListener('submit', (e)=>{
    e.preventDefault();
    const fd = new FormData(e.target);
    
    // Check workflow type
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const jenisText = jenisSelect.selectedOptions[0]?.textContent || '';
    const isTukarWorkflow = jenisText.toUpperCase().includes('TUKAR');
    const isTarikWorkflow = jenisText.toUpperCase().includes('TARIK') && !isTukarWorkflow;
    
    // Enhanced validation for different workflows
    if (isTarikWorkflow) {
      // TARIK workflow: Only needs kontrak and units to tarik
      const tarikUnits = Array.from(document.querySelectorAll('input[name="tarik_units[]"]:checked'));
      
      if (!tarikUnits.length) {
        alert('Select at least one unit to be PULLED from the contract.');
        return;
      }
      
      // TARIK doesn't need SPK, remove it from form data
      fd.delete('spk_id');
      
      console.log('TARIK Workflow - Tarik:', tarikUnits.length, 'units');
      
    } else if (isTukarWorkflow) {
      // TUKAR workflow: Needs SPK, kontrak, and tarik units
      const tarikUnits = Array.from(document.querySelectorAll('.unit-check-tarik:checked'));
      
      if (!tarikUnits.length) {
        alert('Select at least one unit to be PULLED from the contract.');
        return;
      }
      
      const spkId = document.getElementById('spkPick').value;
      if (!spkId) {
        alert('Select SPK for TUKAR workflow.');
        return;
      }
      
      const kontrakId = document.getElementById('kontrakSelect').value;
      if (!kontrakId) {
        alert('Select contract for TUKAR workflow.');
        return;
      }
      
      console.log('Pulled Workflow - Pull:', tarikUnits.length, 'units from contract', kontrakId, 'using SPK', spkId);
      
    } else {
      // Standard workflow validation
      const list = document.getElementById('diUnitList');
      if (document.getElementById('diUnitsSection').style.display !== 'none'){
        const selected = list ? Array.from(list.querySelectorAll('.unit-check:checked')) : [];
        if (!selected.length){
          alert('Select at least one unit for this DI.');
          return;
        }
      }
    }
    
    // Debug: Log form data before sending
    console.log('Enhanced DI Create Form Data:');
    for (let [key, value] of fd.entries()) {
      console.log(`  ${key}: ${value}`);
    }
    
    fetch('<?= base_url('marketing/di/create') ?>',{method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd})
      .then(r=>r.json()).then(j=>{
        console.log('Enhanced DI Create Response:', j);  // Debug log
        if (j && j.success){
          bootstrap.Modal.getInstance(document.getElementById('diCreateModal')).hide();
          e.target.reset();
          loadDI();
          if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('DI dibuat: ' + (j.nomor||''), 'success');
          else if (typeof showNotification==='function') showNotification('DI dibuat: ' + (j.nomor||''), 'success');
          else alert('DI dibuat: ' + (j.nomor||''));
        } else {
          // Format full debug error info if available
          const debugInfo = j.debug ? `\nDebug Info:\n${JSON.stringify(j.debug, null, 2)}` : '';
          const msg = (j.message || 'Failed to create DI') + debugInfo;
          
          console.error('Enhanced DI Create Error:', j);  // Debug log
          
          // Show more detailed error in alert for debugging
          alert(msg);
          
          // Also show in UI notification if available
          if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification(j.message || 'Failed to create DI', 'error');
          else if (typeof showNotification==='function') showNotification(j.message || 'Failed to create DI', 'error');
        }
      }).catch(error => {
        console.error('Enhanced DI Create Fetch Error:', error);  // Debug log
        alert('Network error: ' + error.message);
      });
  });
  
  // PERBAIKAN: Event listener untuk form edit DI
  document.getElementById('diEditForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const diId = document.getElementById('editDiId').value;
    
    console.log('DI Edit Form Data:');
    for (let [key, value] of fd.entries()) {
      console.log(`  ${key}: ${value}`);
    }
    
    fetch(`<?= base_url('marketing/di/update/') ?>${diId}`, {
      method: 'PUT',
      headers: {'X-Requested-With': 'XMLHttpRequest'},
      body: fd
    }).then(r => r.json()).then(j => {
      console.log('DI Edit Response:', j);
      if (j && j.success) {
        bootstrap.Modal.getInstance(document.getElementById('diEditModal')).hide();
        loadDI(); // Reload data
        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('DI successfully updated', 'success');
        else alert('DI successfully updated');
      } else {
        alert(j.message || 'Failed to update DI');
      }
    }).catch(error => {
      console.error('DI Edit Error:', error);
      alert('Network error: ' + error.message);
    });
  });
  
  // PERBAIKAN: Tambah function untuk edit dan delete DI
  window.editDI = function(diId) {
    // Load data DI yang akan diedit
    fetch(`<?= base_url('marketing/di/detail/') ?>${diId}`)
      .then(r => r.json()).then(j => {
        if (j && j.success) {
          const data = j.data || {};
          
          // Populate form edit
          document.getElementById('editDiId').value = diId;
          document.getElementById('editJenisPerintah').value = data.jenis_perintah || '';
          document.getElementById('editTujuanPerintah').value = data.tujuan_perintah || '';
          document.getElementById('editTanggalKirim').value = data.tanggal_kirim || '';
          document.getElementById('editCatatan').value = data.catatan || '';
          
          // PERBAIKAN: Display status eksekusi dengan badge, bukan input
          const statusEksekusi = data.status_eksekusi || 'READY';
          const statusDisplay = document.getElementById('editStatusEksekusiDisplay');
          const statusMap = {
            'READY': { text: 'Ready', color: 'primary' },
            'DISPATCHED': { text: 'Dispatched', color: 'warning' },
            'DELIVERED': { text: 'Delivered', color: 'success' },
            'CANCELLED': { text: 'Cancelled', color: 'danger' }
          };
          const mapped = statusMap[statusEksekusi] || { text: statusEksekusi, color: 'secondary' };
          statusDisplay.className = `badge bg-${mapped.color}`;
          statusDisplay.textContent = mapped.text;
          
          // Show modal
          new bootstrap.Modal(document.getElementById('diEditModal')).show();
        } else {
          alert('Failed to load DI data for editing');
        }
      }).catch(error => {
        console.error('Edit DI Load Error:', error);
        alert('Error loading DI data: ' + error.message);
      });
  };
  
  window.deleteDI = function(diId) {
    if (!confirm('Are you sure you want to delete this DI?')) return;
    
    fetch(`<?= base_url('marketing/di/delete/') ?>${diId}`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
      }
    }).then(r => r.json()).then(j => {
      if (j && j.success) {
        loadDI(); // Reload data
        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('DI successfully deleted', 'success');
        else alert('DI successfully deleted');
      } else {
        alert(j.message || 'Failed to delete DI');
      }
    }).catch(error => {
      console.error('Delete DI Error:', error);
      alert('Network error: ' + error.message);
    });
  };
  
  // Edit DI from detail modal
  window.editDiFromDetail = function() {
    if (!currentDiId) {
      alert('DI ID not found');
      return;
    }
    
    // Close detail modal first
    const detailModal = bootstrap.Modal.getInstance(document.getElementById('diDetailModal'));
    if (detailModal) detailModal.hide();
    
    // Call existing edit function
    editDI(currentDiId);
  };
  
  // Delete DI from detail modal with double confirmation
  window.deleteDiFromDetail = function() {
    if (!currentDiId) {
      alert('DI ID not found');
      return;
    }
    
    // First confirmation
    if (!confirm('Are you sure you want to delete this DI?')) {
      return;
    }
    
    // Second confirmation
    if (!confirm('WARNING: This action cannot be undone!\n\nAre you absolutely sure you want to delete this DI?')) {
      return;
    }
    
    // Close detail modal first
    const detailModal = bootstrap.Modal.getInstance(document.getElementById('diDetailModal'));
    if (detailModal) detailModal.hide();
    
    // Call existing delete function
    deleteDI(currentDiId);
  };
  
  // Print DI from detail modal
  window.printDiFromDetail = function() {
    if (!currentDiId) {
      alert('DI ID not found');
      return;
    }
    
    // Open print DI in new tab (not popup window)
    const printUrl = `<?= base_url('operational/delivery/print/') ?>${currentDiId}`;
    window.open(printUrl, '_blank');
  };
  
  // Print Withdrawal Letter (SPPU) from detail modal
  window.printWithdrawalLetter = function() {
    if (!currentDiId) {
      alert('DI ID not found');
      return;
    }
    
    // Open print SPPU in new tab
    const sppu = `<?= base_url('marketing/di/print-withdrawal/') ?>${currentDiId}`;
    window.open(sppu, '_blank');
  };
  
  // ============================================================
  // LINK DI TO CONTRACT FUNCTIONS
  // ============================================================
  
  /**
   * Open "Link DI to Contract" modal
   * Loads available contracts from same customer
   */
  window.openLinkDIContractModal = async function(diId, diNumber) {
    const modal = new bootstrap.Modal(document.getElementById('linkDIContractModal'));
    
    // Set DI info
    document.getElementById('linkDiId').value = diId;
    document.getElementById('linkDiNumber').textContent = diNumber;
    
    // Reset form
    document.getElementById('linkDIContractForm').reset();
    document.getElementById('linkDiId').value = diId; // Restore after reset
    
    // Load available contracts for this DI's customer
    const contractSelect = document.getElementById('linkContractId');
    contractSelect.innerHTML = '<option value="">Loading contracts...</option>';
    
    try {
      // Get DI details to find customer
      const diRes = await fetch(`<?= base_url('marketing/di/detail/') ?>${diId}`);
      const diData = await diRes.json();
      
      if (!diData.success || !diData.data) {
        throw new Error('Failed to load DI details');
      }
      
      const customerId = diData.data.pelanggan_id;
      
      // Load contracts for this customer
      const contractRes = await fetch(`<?= base_url('marketing/contracts/by-customer/') ?>${customerId}`);
      const contractData = await contractRes.json();
      
      if (!contractData.success) {
        throw new Error('Failed to load contracts');
      }
      
      const contracts = contractData.data || [];
      
      // Populate dropdown with DEAL contracts only
      contractSelect.innerHTML = '<option value="">- Select Contract -</option>';
      
      const dealContracts = contracts.filter(c => c.status_kontrak === 'DEAL');
      
      if (dealContracts.length === 0) {
        contractSelect.innerHTML = '<option value="">No DEAL contracts available for this customer</option>';
      } else {
        dealContracts.forEach(contract => {
          const option = document.createElement('option');
          option.value = contract.id;
          option.textContent = `${contract.nomor_kontrak} - ${contract.customer_name || ''} (${contract.tanggal_kontrak || ''})`;
          contractSelect.appendChild(option);
        });
      }
      
    } catch (error) {
      console.error('Error loading contracts:', error);
      contractSelect.innerHTML = '<option value="">Error loading contracts</option>';
      alert('Failed to load contracts: ' + error.message);
    }
    
    modal.show();
  };
  
  /**
   * Handle Link DI to Contract form submission
   */
  document.getElementById('linkDIContractForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Linking...';
    
    try {
      const response = await fetch('<?= base_url('marketing/di/link-to-contract') ?>', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('linkDIContractModal'));
        modal.hide();
        
        // Show success message
        Swal.fire({
          icon: 'success',
          title: 'Contract Linked!',
          html: result.message || 'DI has been successfully linked to contract.<br>Invoice generation is now enabled.',
          confirmButtonColor: '#28a745'
        });
        
        // Reload DI table
        loadDI();
      } else {
        throw new Error(result.message || 'Failed to link contract');
      }
    } catch (error) {
      console.error('Error linking contract:', error);
      Swal.fire({
        icon: 'error',
        title: 'Link Failed',
        text: error.message || 'An error occurred while linking contract',
        confirmButtonColor: '#dc3545'
      });
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
  
});
</script>

<!-- DI Detail Modal -->
<div class="modal fade" id="diDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title"><?= lang('App.detail') ?> Delivery Instruction</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><div id="diDetailBody"><p class="text-muted">Loading...</p></div></div>
      <div class="modal-footer">
        <button class="btn btn-success" id="btnPrintSppu" onclick="printWithdrawalLetter()" style="display:none;">
          <i class="fas fa-file-contract"></i> Print SPPU
        </button>
        <button class="btn btn-primary" id="btnPrintDi" onclick="printDiFromDetail()">
          <i class="fas fa-print"></i> Print DI
        </button>
        <button class="btn btn-warning" id="btnEditDi" onclick="editDiFromDetail()">
          <i class="fas fa-edit"></i> Edit
        </button>
        <button class="btn btn-danger" id="btnDeleteDi" onclick="deleteDiFromDetail()">
          <i class="fas fa-trash"></i> Delete
        </button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit DI Modal -->
<div class="modal fade" id="diEditModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title"><?= lang('App.edit') ?> Delivery Instruction</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form id="diEditForm">
        <input type="hidden" id="editDiId" name="id">
        <div class="modal-body">
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label"><?= lang('Marketing.command_type') ?> <span class="text-danger">*</span></label>
              <select class="form-select" id="editJenisPerintah" name="jenis_perintah" required>
                <option value="">- <?= lang('Marketing.select_command') ?> -</option>
                <!-- Options will be loaded from API -->
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= lang('Marketing.command_purpose') ?> <span class="text-danger">*</span></label>
              <select class="form-select" id="editTujuanPerintah" name="tujuan_perintah" required disabled>
                <option value="">- <?= lang('Marketing.select_command_first') ?> -</option>
                <!-- Options will be loaded from API based on jenis -->
              </select>
            </div>
          </div>
          
          <!-- PERBAIKAN: Status Eksekusi sebagai display only, bukan input -->
          <div class="mb-3">
            <label class="form-label"><?= lang('Marketing.execution_status') ?></label>
            <div class="card bg-light">
              <div class="card-body py-2">
                <span id="editStatusEksekusiDisplay" class="badge bg-primary">READY</span>
                <small class="text-muted ms-2"><?= lang('Marketing.status_managed_by_system') ?></small>
              </div>
            </div>
          </div>
          
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label"><?= lang('Marketing.delivery_date') ?></label>
              <input type="date" class="form-control" id="editTanggalKirim" name="tanggal_kirim">
            </div>
            <div class="col-6">
              <label class="form-label"><?= lang('Marketing.notes') ?></label>
              <input type="text" class="form-control" id="editCatatan" name="catatan" placeholder="<?= lang('App.optional') ?>">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Update DI</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Link DI to Contract -->
<div class="modal fade" id="linkDIContractModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Link DI to Contract</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="linkDIContractForm">
        <input type="hidden" id="linkDiId" name="di_id">
        <div class="modal-body">
          <div class="alert alert-info small mb-3">
            <i class="fas fa-info-circle"></i> 
            Link <strong id="linkDiNumber"></strong> to a Contract. 
            This will enable invoice generation for this DI.
          </div>

          <div class="mb-3">
            <label class="form-label">Select Contract <span class="text-danger">*</span></label>
            <select class="form-select" id="linkContractId" name="contract_id" required>
              <option value="">- Select Contract -</option>
              <!-- Dynamic options loaded via JS -->
            </select>
            <small class="text-muted">Only DEAL contracts from the same customer will be shown.</small>
          </div>

          <div class="mb-3">
            <label class="form-label">BAST Date (Optional)</label>
            <input type="date" class="form-control" id="linkBastDate" name="bast_date" 
                   max="<?= date('Y-m-d') ?>">
            <small class="text-muted">Set handover date if already executed.</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes (Optional)</label>
            <textarea class="form-control" id="linkNotes" name="notes" rows="2" 
                      placeholder="Optional notes about contract linkage"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-link"></i> Link Contract
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
