<?= $this->extend('layouts/base') ?>

<?php
/**
 * Delivery Instructions (DI) Module
 * 
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 * 
 * Quick Reference:
 * - Status DIRENCANAKAN    → <span class="badge badge-soft-yellow">DIRENCANAKAN</span>
 * - Status DALAM_PERJALANAN → <span class="badge badge-soft-cyan">DALAM_PERJALANAN</span>
 * - Status SELESAI         → <span class="badge badge-soft-green">SELESAI</span>
 * - Status CANCELLED       → <span class="badge badge-soft-red">CANCELLED</span>
 * - Status AWAITING_CONTRACT → <span class="badge badge-soft-orange">AWAITING CONTRACT</span>
 * - Count / Total items    → <span class="badge badge-soft-blue">5</span>
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

<?= $this->section('css') ?>
<style>
.unit-list {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    max-height: 240px;
    overflow-y: auto;
    padding: 6px;
    background: #f8f9fa;
}
.unit-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 14px 10px 16px;
    border-radius: 6px;
    margin-bottom: 3px;
    background: #fff;
    border: 1px solid #e9ecef;
    transition: background 0.15s, border-color 0.15s;
    cursor: pointer;
}
.unit-item:hover {
    background: #eef3ff;
    border-color: #b8cef9;
}
.unit-item input[type="checkbox"] {
    margin-top: 4px;
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #0d6efd;
}
.unit-item label {
    cursor: pointer;
    flex: 1;
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.4;
}
.unit-item-title { font-size: 0.9rem; }
.unit-item input[type="checkbox"]:checked + label .unit-item-title {
    font-weight: 600;
    color: #0d47a1;
}
.unit-item:has(input:checked) {
    background: #e8f0fe;
    border-color: #90b4f7;
}
.unit-note {
    font-size: 0.775rem;
    color: #6c757d;
    margin-top: 4px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
}
.unit-detail-pill {
    display: inline-block;
    background: #e9ecef;
    border-radius: 10px;
    padding: 1px 8px;
    font-size: 0.72rem;
    color: #495057;
    white-space: nowrap;
}
</style>
<?= $this->endSection() ?>

<!-- Statistics Cards -->
  <div class="row mt-3 mb-4">
      <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
          <div class="stat-card bg-primary-soft filter-card cursor-pointer" data-filter="all">
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
          <div class="stat-card bg-warning-soft filter-card cursor-pointer" data-filter="DIRENCANAKAN">
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
          <div class="stat-card bg-info-soft filter-card cursor-pointer" data-filter="DALAM_PERJALANAN">
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
          <div class="stat-card bg-success-soft filter-card cursor-pointer" data-filter="SELESAI">
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
          <div class="stat-card bg-danger-soft filter-card cursor-pointer" data-filter="AWAITING_CONTRACT">
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
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div>
        <h5 class="card-title mb-0">
          <i class="bi bi-truck me-2 text-primary"></i>
          <?= lang('App.delivery_instructions_di') ?>
        </h5>
        <p class="text-muted small mb-0">
          Kelola instruksi pengiriman dan penyebaran unit ke lokasi pelanggan
          <span class="ms-2 text-info">
            <i class="bi bi-info-circle me-1"></i>
            <small>Tip: Click pada stat card untuk filter berdasarkan status</small>
          </span>
        </p>
      </div>
      <div class="d-flex gap-2">
        <?= ui_button('add', 'Create DI', [
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#diCreateModal',
            'size' => 'sm'
        ]) ?>
      </div>
    </div>
    
    <div class="card-body">
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
    
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0 table-manual-sort" id="diTable">
          <thead class="table-light">
            <tr>
              <th>No. DI</th>
              <th class="d-none d-xl-table-cell">PO/Contract</th>
              <th>Customer</th>
              <th class="d-none d-xxl-table-cell">Location</th>
              <th>Total Items</th>
              <th class="d-none d-lg-table-cell">Command</th>
              <th class="d-none d-xl-table-cell">Req. Delivery Date</th>
              <th data-no-sort>Status</th>
              <th data-no-sort>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <!-- DataTables will add pagination and info here -->
    </div>
  </div>

  <!-- Enhanced DI Modal with correct TUKAR workflow support -->
  <div class="modal fade modal-wide" id="diCreateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
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
                <div class="d-flex gap-2">
                  <?= ui_button('select-all', lang('App.select_all'), [
                      'color' => 'outline-secondary',
                      'size' => 'sm',
                      'type' => 'button',
                      'id' => 'btnSelectAll'
                  ]) ?>
                  <?= ui_button('clear', lang('App.clear'), [
                      'color' => 'outline-secondary',
                      'size' => 'sm',
                      'type' => 'button',
                      'id' => 'btnClearAll'
                  ]) ?>
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
                    <?= lang('Marketing.permanent') ?> | <?= lang('Marketing.temporary_returns') ?> | <?= lang('Marketing.temp_replacement') ?> | <?= lang('Marketing.relocation') ?>
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
                <div class="d-flex gap-2">
                  <?= ui_button('select-all', 'Select All', [
                      'color' => 'outline-warning',
                      'size' => 'sm',
                      'type' => 'button',
                      'id' => 'btnSelectAllTarikOnly'
                  ]) ?>
                  <?= ui_button('clear', 'Clear', [
                      'color' => 'outline-secondary',
                      'size' => 'sm',
                      'type' => 'button',
                      'id' => 'btnClearTarikOnly'
                  ]) ?>
                </div>
              </div>
              <div id="diTarikOnlyList" class="unit-list">
                <div class="text-muted small">Select a contract first...</div>
              </div>
              <div class="form-text">Selected units will be removed from the contract (FK relationship removed)</div>
            </div>

            <!-- Step 3: TUKAR / ANTAR+TARIK Unit Selection (Complex TARIK from kontrak + KIRIM from SPK) -->
            <div id="diTukarWorkflow" style="display:none;" class="mb-4">
              <div class="alert alert-info mb-2" id="diTukarWorkflowAlert">
                <i class="fas fa-exchange-alt"></i> 
                <strong id="diTukarWorkflowMode">Mode TUKAR:</strong> Pilih unit <strong>KIRIM</strong> dari SPK di atas, dan pilih unit <strong>TARIK</strong> dari kontrak di bawah. Jumlah tidak harus sama.
              </div>
              
              <!-- Unit PULL Section for EXCHANGE -->
              <div class="card border-warning">
                <div class="card-header bg-light">
                  <h6 class="mb-0 fw-semibold"><i class="fas fa-minus-circle text-warning me-1"></i> Unit yang Ditarik (dari kontrak lama)</h6>
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="small text-muted">Selected: <span id="tarikCount">0</span> unit</div>
                    <div class="d-flex gap-2">
                      <?= ui_button('select-all', 'Select All', [
                          'color' => 'outline-warning',
                          'size' => 'sm',
                          'type' => 'button',
                          'id' => 'btnSelectAllTarik'
                      ]) ?>
                      <?= ui_button('clear', 'Clear', [
                          'color' => 'outline-secondary',
                          'size' => 'sm',
                          'type' => 'button',
                          'id' => 'btnClearTarik'
                      ]) ?>
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
            <input type="hidden" name="pelanggan_id" id="pelanggan_id">
            <!-- TUKAR workflow: contract of old unit being pulled -->
            <input type="hidden" name="tarik_contract_id" id="createTarikContractId">

            <div class="row g-2 mb-2">
              <div class="col-12">
                <label class="form-label">Customer Location <span class="text-danger">*</span></label>
                <select class="form-select" name="customer_location_id" id="customerLocationSelect" required disabled>
                  <option value="">-- Pilih Customer Location --</option>
                </select>
                <small class="text-muted">Lokasi customer wajib dipilih pada tahap DI.</small>
              </div>
              <div class="col-12" id="operatorRatePreview" style="display:none;">
                <div class="alert alert-info py-2 mb-0">
                  <strong>Operator Rate Lokasi:</strong>
                  <span id="operatorRateMonthly">Bulanan: -</span>
                  <span class="mx-2">|</span>
                  <span id="operatorRateDaily">Harian: -</span>
                </div>
              </div>
            </div>

            <!-- Common Fields -->
            <div class="row g-2">
              <div class="col-6"><label class="form-label"><?= lang('Marketing.delivery_date') ?></label><input type="date" class="form-control" name="tanggal_kirim"></div>
              <div class="col-6"><label class="form-label"><?= lang('Marketing.notes') ?></label><input type="text" class="form-control" name="catatan" placeholder="<?= lang('App.optional') ?>"></div>
            </div>
          </div>
          <div class="modal-footer">
            <?= ui_button('cancel', lang('App.cancel'), ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
            <?= ui_button('submit', 'Create DI', ['type' => 'submit']) ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// UI Badge Helper - Generate consistent badge colors (Optima badge-soft-* system)
function uiBadge(type, text, options = {}) {
    const badgeMap = {
        'active': 'badge-soft-green', 'approved': 'badge-soft-green', 'completed': 'badge-soft-green', 'delivered': 'badge-soft-green', 'linked': 'badge-soft-green',
        'pending': 'badge-soft-yellow', 'ready': 'badge-soft-blue', 'in_progress': 'badge-soft-cyan', 'processing': 'badge-soft-cyan',
        'rejected': 'badge-soft-red', 'cancelled': 'badge-soft-red', 'failed': 'badge-soft-red', 'deleted': 'badge-soft-red',
        'draft': 'badge-soft-gray', 'new': 'badge-soft-blue', 'info': 'badge-soft-cyan', 'warning': 'badge-soft-yellow',
        'created': 'badge-soft-green', 'updated': 'badge-soft-cyan', 'submitted': 'badge-soft-gray', 'success': 'badge-soft-green',
        'primary': 'badge-soft-blue', 'secondary': 'badge-soft-gray', 'danger': 'badge-soft-red'
    };
    const cls = options.softClass || badgeMap[type.toLowerCase()] || 'badge-soft-gray';
    const extraClass = options.class || '';
    const icon = options.icon ? `<i class="${options.icon}"></i> ` : '';
    const title = options.title ? ` title="${options.title}"` : '';
    return `<span class="badge ${cls} ${extraClass}"${title}>${icon}${text}</span>`;
}

// Global variables
let diTable; // DataTable instance
let currentFilter = 'all';
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
  let currentEditDiData = {}; // stores DI data for use in change handlers
  let currentEditKirimItems = []; // stores kirim_items from diDetail API
  let currentEditTarikItems = []; // stores tarik_items from diDetail API
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

  // Helper: get kode of selected jenis perintah
  function getSelectedJenisKode() {
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const selectedId = parseInt(jenisSelect.value);
    const opt = jenisPerintahOptions.find(o => parseInt(o.id) === selectedId);
    return opt ? opt.kode.toUpperCase() : '';
  }

  // Enhanced workflow change handler with TARIK, TUKAR, and ANTAR+TARIK support
  function handleWorkflowJenisChange() {
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const jenisText = jenisSelect.selectedOptions[0]?.textContent || '';
    const jenisKode = getSelectedJenisKode();
    
    // Use kode-based detection (more reliable than text)
    const isAntarTarikWorkflow = jenisKode === 'ANTAR_TARIK';
    const isTukarWorkflow = jenisKode === 'TUKAR';
    const isTukarLikeWorkflow = isTukarWorkflow || isAntarTarikWorkflow; // both use same 2-panel UI
    const isTarikWorkflow = jenisKode === 'TARIK';
    
    console.log('Workflow changed:', { jenisText, jenisKode, isTukarLikeWorkflow, isTarikWorkflow });
    
    // Get all relevant sections
    const spkSection = document.getElementById('spkSelectionSection');
    const kontrakSelection = document.getElementById('diKontrakSelection');
    const tukarWorkflow = document.getElementById('diTukarWorkflow');
    const tarikOnlySection = document.getElementById('diTarikOnlySection');
    const standardUnits = document.getElementById('diUnitsSection');
    const spkPick = document.getElementById('spkPick');
    
    // Update alert text based on mode
    const modeLabel = document.getElementById('diTukarWorkflowMode');
    if (modeLabel) {
      modeLabel.textContent = isAntarTarikWorkflow ? 'Mode ANTAR+TARIK:' : 'Mode TUKAR:';
    }

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
      
    } else if (isTukarLikeWorkflow) {
      // TUKAR / ANTAR+TARIK workflow: SPK → Kontrak → Unit TARIK + Unit KIRIM
      spkSection.style.display = 'block';
      spkPick.setAttribute('required', 'required');
      
      kontrakSelection.style.display = 'block';
      tukarWorkflow.style.display = 'block';
      tarikOnlySection.style.display = 'none';
      standardUnits.style.display = 'block'; // Tampilkan KIRIM units dari SPK
      
      // Load kontrak only if SPK already selected (has customer_id)
      if (pelangganIdInput && pelangganIdInput.value) {
        loadKontrakOptionsForTukar();
      } else {
        // Prompt user to pick SPK first
        const kontrakSelect = document.getElementById('kontrakSelect');
        if (kontrakSelect) kontrakSelect.innerHTML = '<option value="">-- Pilih SPK terlebih dahulu --</option>';
        const tarikList = document.getElementById('diTarikUnitList');
        if (tarikList) tarikList.innerHTML = '<div class="text-muted small">Pilih SPK untuk memuat unit TARIK.</div>';
      }
      
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

  // Load available contracts for TARIK workflow (includes EXPIRED)
  async function loadKontrakOptionsForTarik() {
    try {
      const response = await fetch('<?= base_url('marketing/kontrak/get-contracts-for-tarik') ?>', {
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
          result.data.map(k => `<option value="${k.id}">${k.label} (${k.unit_count} units)</option>`).join('');
        
        kontrakSelect.innerHTML = optionsHtml;
        
        // Setup kontrak change handler for TARIK
        setupKontrakChangeForTarik();
        
        // Auto-select if kontrak_id is in URL params
        const urlParams = new URLSearchParams(window.location.search);
        const preselectedKontrak = urlParams.get('kontrak_id');
        if (preselectedKontrak) {
          kontrakSelect.value = preselectedKontrak;
          kontrakSelect.dispatchEvent(new Event('change'));
        }
        
        console.log('Loaded', result.data.length, 'contract options for TARIK workflow');
      } else {
        console.error('Failed to load contract options:', result.message);
      }
    } catch (error) {
      console.error('Error loading contract options:', error);
    }
  }

  // Load available contracts for TUKAR workflow — filtered by customer dari SPK yang dipilih
  async function loadKontrakOptionsForTukar() {
    try {
      const customerId = pelangganIdInput ? (pelangganIdInput.value || '') : '';
      const tarikUrl = '<?= base_url('marketing/kontrak/get-contracts-for-tarik') ?>' +
        (customerId ? '?customer_id=' + encodeURIComponent(customerId) : '');
      const response = await fetch(tarikUrl, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      });
      const result = await response.json();
      
      if (result.success) {
        const kontrakSelect = document.getElementById('kontrakSelect');
        
        const optionsHtml = '<option value="">-- Pilih Kontrak Unit Lama --</option>' + 
          result.data.map(k => `<option value="${k.id}">${k.label} (${k.unit_count} unit)</option>`).join('');
        
        kontrakSelect.innerHTML = optionsHtml;
        
        // Setup kontrak change handler for TUKAR
        setupKontrakChangeHandler();
        
        console.log('Loaded', result.data.length, 'contract options for TUKAR workflow (customer:', customerId, ')');
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
        fetch(`<?= base_url('marketing/rental/get-kontrak/') ?>${this.value}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
          .then(r => r.json())
          .then(res => {
            const customerId = res?.data?.customer_id || res?.customer_id || '';
            pelangganIdInput.value = customerId;
            loadCustomerLocationsByCustomer(customerId);
          })
          .catch(() => resetCustomerLocationSelection());
        
        console.log(`TARIK Kontrak selected: ${noKontrak} - ${pelanggan}`);
        
        // Load TARIK units only for TARIK workflow
        loadTarikOnlyUnits(this.value);
      } else {
        // Reset hidden fields and list
        document.getElementById('po_kontrak_nomor').value = '';
        document.getElementById('pelanggan').value = '';
        pelangganIdInput.value = '';
        resetCustomerLocationSelection();
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
        fetch(`<?= base_url('marketing/rental/get-kontrak/') ?>${this.value}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
          .then(r => r.json())
          .then(res => {
            const customerId = res?.data?.customer_id || res?.customer_id || '';
            pelangganIdInput.value = customerId;
            loadCustomerLocationsByCustomer(customerId);
          })
          .catch(() => resetCustomerLocationSelection());
        
        console.log(`Kontrak selected: ${noKontrak} - ${pelanggan}`);
        
        // Store tarik_contract_id for backend
        document.getElementById('createTarikContractId').value = this.value;

        // Load TARIK units (current units in kontrak) for TUKAR workflow
        loadTukarUnits(this.value);
      } else {
        // Reset hidden fields and unit list
        document.getElementById('po_kontrak_nomor').value = '';
        document.getElementById('pelanggan').value = '';
        pelangganIdInput.value = '';
        resetCustomerLocationSelection();
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

  // Enhanced form validation with correct TUKAR/ANTAR+TARIK support
  function validateWorkflowForm() {
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const tujuanSelect = document.getElementById('tujuanPerintahSelect');
    const submitBtn = document.querySelector('#diCreateForm [type="submit"]');
    
    if (!jenisSelect || !tujuanSelect || !submitBtn) return;
    
    const jenisKode = getSelectedJenisKode();
    const isTukarLikeWorkflow = jenisKode === 'TUKAR' || jenisKode === 'ANTAR_TARIK';
    
    const jenisValid = jenisSelect.value !== '';
    const tujuanValid = tujuanSelect.value !== '';
    let additionalValid = true;
    
    // Additional validation for TUKAR / ANTAR+TARIK workflow
    if (isTukarLikeWorkflow && jenisValid && tujuanValid) {
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
      const jenisText = this.selectedOptions[0]?.textContent || '';
      
      // Reset tujuan dropdown
      editTujuanSelect.innerHTML = '<option value="">-- Select Purpose --</option>';
      editTujuanSelect.disabled = true;
      
      if (jenisValue) {
        loadTujuanPerintahOptions(jenisValue, 'editTujuanPerintah');
      }

      // Show/hide TUKAR section
      const jenisOpt = jenisPerintahOptions.find(o => String(o.id) === String(jenisValue));
      const isTukar = jenisOpt ? jenisOpt.kode === 'TUKAR' : jenisText.toUpperCase().includes('TUKAR');
      const tukarSection = document.getElementById('editTukarSection');
      if (tukarSection) tukarSection.style.display = isTukar ? 'block' : 'none';
      // Populate TUKAR section content when user switches to TUKAR
      if (isTukar) {
        populateEditTukarSection(currentEditDiData, currentEditKirimItems, currentEditTarikItems);
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
      if (typeof resetCustomerLocationSelection === 'function') {
        resetCustomerLocationSelection();
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

  // Auto-open Create DI modal for TARIK when redirected from kontrak page
  (function autoOpenTarikModal() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('create_tarik') === '1') {
      const kontrakId = urlParams.get('kontrak_id');
      setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('diCreateModal'));
        modal.show();
        
        // Wait for dropdown data to load, then auto-select TARIK
        setTimeout(async () => {
          const jenisSelect = document.getElementById('jenisPerintahSelect');
          if (jenisSelect) {
            const tarikOption = Array.from(jenisSelect.options).find(o => o.textContent.toUpperCase().includes('TARIK') && !o.textContent.toUpperCase().includes('TUKAR') && !o.textContent.toUpperCase().includes('ANTAR'));
            if (tarikOption) {
              jenisSelect.value = tarikOption.value;
              jenisSelect.dispatchEvent(new Event('change'));
              
              // Wait for tujuan dropdown to populate
              await new Promise(r => setTimeout(r, 500));
              const tujuanSelect = document.getElementById('tujuanPerintahSelect');
              if (tujuanSelect) {
                const habisOption = Array.from(tujuanSelect.options).find(o => o.textContent.toUpperCase().includes('HABIS'));
                if (habisOption) {
                  tujuanSelect.value = habisOption.value;
                  tujuanSelect.dispatchEvent(new Event('change'));
                }
              }
            }
          }
        }, 800);
      }, 300);

      // Clean URL params
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  })();

  /**
   * Load DI statistics from server
   */
  function loadDIStatistics() {
    $.ajax({
      url: '<?= base_url('marketing/di/stats') ?>',
      type: 'POST',
      data: { 
        status_filter: currentFilter,
        [window.csrfTokenName]: window.csrfToken || ''
      },
      beforeSend: function(xhr) {
        if (window.csrfToken) {
          xhr.setRequestHeader('X-CSRF-TOKEN', window.csrfToken);
        }
      },
      success: function(data) {
        document.getElementById('totalDI').textContent = data.total || 0;
        document.getElementById('submittedDI').textContent = data.submitted || 0;
        document.getElementById('inprogressDI').textContent = data.inprogress || 0;
        document.getElementById('deliveredDI').textContent = data.delivered || 0;
        document.getElementById('awaitingContractDI').textContent = data.awaiting_contract || 0;
      },
      error: function(xhr) {
        console.error('Failed to load DI statistics:', xhr.responseText);
      }
    });
  }
  
  /**
   * Filter DI table by status
   */
  function filterDIData(filter) {
    currentFilter = filter;
    
    // Update active filter card/tab
    document.querySelectorAll('.filter-card, .filter-tab').forEach(el => el.classList.remove('active'));
    document.querySelector(`.filter-card[data-filter="${filter}"], .filter-tab[data-filter="${filter}"]`)?.classList.add('active');
    
    // Reload DataTable with new filter (server handles it)
    if (diTable && diTable.ajax) {
      diTable.ajax.reload();
    }
  }
  
  /**
   * Initialize DataTable for DI table
   */
  try {
    diTable = OptimaDataTable.init('#diTable', {
      ajax: {
        url: '<?= base_url('marketing/di/data') ?>',
        type: 'POST',
        data: function(d) {
          d.status_filter = currentFilter;
          return d;
        },
        error: function(xhr) {
          console.error('❌ DI DataTable error:', xhr.responseText);
        }
      },
      serverSide: true,
      pageLength: 25,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      order: [[0, 'desc']],
      columns: [
        { 
          data: 'nomor_di',
          render: function(data, type, row) {
            return `<a href="#" onclick="openDiDetail(${row.id});return false;">${data}</a>`;
          }
        },
        { 
          data: 'po_kontrak_nomor',
          className: 'd-none d-xl-table-cell',
          responsivePriority: 4,
          render: function(data, type, row) {
            const hasContract = row.contract_id !== null && row.contract_id !== '';
            if (hasContract) {
              return `${data || '-'} ${uiBadge('success', '', {icon: 'fas fa-link', title: 'Contract linked'})}`;
            } else {
              return `${data || '-'} ${uiBadge('warning', 'NO CONTRACT', {title: 'No contract linked'})}`;
            }
          }
        },
        { 
          data: 'pelanggan', 
          defaultContent: '-'
        },
        { 
          data: 'lokasi', 
          defaultContent: '-',
          className: 'd-none d-xxl-table-cell',
          responsivePriority: 6
        },
        { 
          data: null,
          render: function(data, type, row) {
            const totalUnits = row.total_units || 0;
            const totalAttachments = row.total_attachments || 0;
            const jenisSpk = row.jenis_spk || 'UNIT';
            
            if (jenisSpk === 'ATTACHMENT') {
              return totalAttachments > 0 
                ? uiBadge('warning', `${totalAttachments} Attachment`)
                : uiBadge('secondary', 'No attachments');
            } else {
              return totalUnits > 0
                ? uiBadge('primary', `${totalUnits} Unit`)
                : uiBadge('secondary', 'No units');
            }
          }
        },
        { 
          data: null,
          className: 'd-none d-lg-table-cell',
          responsivePriority: 6,
          render: function(data, type, row) {
            const jenis = row.jenis_perintah || '-';
            const tujuan = row.tujuan_perintah || '';
            
            if (!tujuan) return jenis;
            
            let indicator = '';
            if (tujuan.includes('HABIS_KONTRAK')) indicator = '🔴';
            else if (tujuan.includes('MAINTENANCE') && tujuan.includes('TUKAR')) indicator = '🟡';
            else if (tujuan.includes('MAINTENANCE')) indicator = '🔵';
            else if (tujuan.includes('RUSAK') && tujuan.includes('TUKAR')) indicator = '🔴';
            else if (tujuan.includes('RUSAK')) indicator = '🔵';
            else if (tujuan.includes('PINDAH_LOKASI')) indicator = '🟢';
            else if (tujuan.includes('UPGRADE') || tujuan.includes('DOWNGRADE')) indicator = '🔴';
            
            return indicator ? `${indicator} ${jenis} - ${tujuan}` : `${jenis} - ${tujuan}`;
          }
        },
        { 
          data: 'requested_delivery_date', 
          defaultContent: '-',
          className: 'd-none d-xl-table-cell',
          responsivePriority: 5
        },
        { 
          data: 'status_di',
          render: function(data, type, row) {
            const statusUpper = (data || '').toUpperCase();
            const statusMap = {
              'DIAJUKAN': { text: 'DIAJUKAN', cls: 'badge-soft-gray' },
              'DISETUJUI': { text: 'DISETUJUI', cls: 'badge-soft-cyan' },
              'PERSIAPAN_UNIT': { text: 'PERSIAPAN_UNIT', cls: 'badge-soft-yellow' },
              'SIAP_KIRIM': { text: 'SIAP_KIRIM', cls: 'badge-soft-blue' },
              'DALAM_PERJALANAN': { text: 'DALAM_PERJALANAN', cls: 'badge-soft-cyan' },
              'SAMPAI_LOKASI': { text: 'SAMPAI_LOKASI', cls: 'badge-soft-green' },
              'SELESAI': { text: 'SELESAI', cls: 'badge-soft-green' },
              'DIBATALKAN': { text: 'DIBATALKAN', cls: 'badge-soft-red' },
              'AWAITING_CONTRACT': { text: 'On-Hire (Pending PO)', cls: 'badge-soft-orange' }
            };
            const mapped = statusMap[statusUpper] || { text: data || 'DIAJUKAN', cls: 'badge-soft-gray' };
            
            if (statusUpper === 'AWAITING_CONTRACT' && row.dibuat_pada) {
              const created = new Date(row.dibuat_pada);
              const now = new Date();
              const daysPending = Math.floor((now - created) / (1000 * 60 * 60 * 24));
              const urgencyClass = daysPending > 14 ? 'badge-soft-red' : (daysPending > 7 ? 'badge-soft-orange' : 'badge-soft-cyan');
              return `<span class="badge ${mapped.cls}"><i class="fas fa-clock me-1"></i>${mapped.text}</span> <span class="badge ${urgencyClass}" title="Days waiting">${daysPending}d</span>`;
            }
            
            return `<span class="badge ${mapped.cls}">${mapped.text}</span>`;
          }
        },
        { 
          data: null,
          orderable: false,
         searchable: false,
          render: function(data, type, row) {
            const hasContract = row.contract_id !== null && row.contract_id !== '';
            
            if (!hasContract && row.status_di !== 'DIBATALKAN') {
              return `<button class="btn btn-sm btn-outline-warning link-di-contract" 
                data-di-id="${row.id}" 
                data-di-number="${row.nomor_di}" 
                title="Link to Contract">
                <i class="fas fa-link"></i> Link
              </button>`;
            } else if (hasContract) {
              const diTotal = parseInt(row.di_total_unit_count || 0, 10);
              const diLinked = parseInt(row.di_linked_unit_count || 0, 10);
              const showSync = diTotal > 0 && diLinked < diTotal;

              return uiBadge('linked', 'Linked', {icon: 'fas fa-check'}) + `
                ${showSync ? `
                  <button class="btn btn-sm btn-outline-warning sync-di-contract ms-2"
                    data-di-id="${row.id}"
                    data-contract-id="${row.contract_id}"
                    title="Re-sync units to contract (if Units Location is empty)">
                    <i class="fas fa-sync me-1"></i>Re-sync
                  </button>
                ` : ''}`;
            }
            return '-';
          }
        }
      ],
      drawCallback: function(settings, json) {
        console.log('✅ DI DataTable drawn');
        
        // Load server-side statistics
        loadDIStatistics();
        
        // Wire up Link Contract buttons
        $('#diTable tbody').off('click', '.link-di-contract').on('click', '.link-di-contract', function() {
          const diId = $(this).data('di-id');
          const diNumber = $(this).data('di-number');
          openLinkDIContractModal(diId, diNumber);
        });

        // Wire up Sync Units to Contract buttons
        $('#diTable tbody').off('click', '.sync-di-contract').on('click', '.sync-di-contract', async function() {
          const diId = $(this).data('di-id');
          const contractId = $(this).data('contract-id');
          const btn = this;
          
          if (!diId || !contractId) {
            OptimaNotify.error('DI atau Contract tidak valid.');
            return;
          }

          btn.disabled = true;
          btn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i>Sync...';
          OptimaPro.showLoading('Sync units to contract...');

          try {
            const fd = new FormData();
            fd.append('di_id', diId);
            fd.append('contract_id', contractId);

            const response = await (window.csrfFetch || window.fetch)('<?= base_url('marketing/di/sync-units-to-contract') ?>', {
              method: 'POST',
              body: fd
            });

            const result = await response.json();
            if (result.success) {
              OptimaNotify.success(result.message || 'Units synced successfully.');
              loadDI();
            } else {
              throw new Error(result.message || 'Failed to sync units.');
            }
          } catch (error) {
            console.error('Error syncing units:', error);
            OptimaNotify.error(error.message || 'An error occurred while syncing units.');
          } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync"></i>';
            OptimaPro.hideLoading();
          }
        });
      }
    });
    
    console.log('✅ Marketing DI DataTable initialized successfully');
    
  } catch(error) {
    console.error('❌ Failed to initialize DI DataTable:', error);
  }
  
  // Filter tab/card click listeners
  document.querySelectorAll('.filter-tab, .filter-card').forEach(el => {
    el.addEventListener('click', function(e) {
      e.preventDefault();
      filterDIData(this.dataset.filter);
    });
  });
  
  // OLD MANUAL RENDERING CODE REMOVED - Migrated to OptimaDataTable server-side pagination
  
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
      
      // Store jenis_perintah and show SPPU button for TARIK/TUKAR/ANTAR+TARIK
      currentDiJenis = (d.jenis_perintah || '').toUpperCase();
      if (currentDiJenis === 'TARIK' || currentDiJenis === 'TUKAR' || currentDiJenis === 'ANTAR+TARIK' || currentDiJenis === 'ANTAR_TARIK') {
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
              <div class="col-6"><strong>Status:</strong> ${uiBadge('primary', d.status)}</div>
              <div class="col-6"><strong>SPK Number:</strong> ${spk.nomor_spk||'-'}</div>
              <div class="col-6"><strong>SPK Type:</strong> <span class="badge ${isAttachmentSpk ? 'badge-soft-orange' : 'badge-soft-cyan'}">${spkType}</span></div>
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
  const customerLocationSelect = document.getElementById('customerLocationSelect');
  const pelangganIdInput = document.getElementById('pelanggan_id');
  let spkReadyMap = {};

  function formatIDR(value) {
    const n = Number(value);
    if (!Number.isFinite(n)) return '-';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
  }

  function resetCustomerLocationSelection() {
    customerLocationSelect.innerHTML = '<option value="">-- Pilih Customer Location --</option>';
    customerLocationSelect.value = '';
    customerLocationSelect.disabled = true;
    document.getElementById('operatorRatePreview').style.display = 'none';
    document.getElementById('operatorRateMonthly').textContent = 'Bulanan: -';
    document.getElementById('operatorRateDaily').textContent = 'Harian: -';
  }

  async function loadCustomerLocationsByCustomer(customerId) {
    if (!customerId) {
      resetCustomerLocationSelection();
      return;
    }
    try {
      const response = await fetch(`<?= base_url('marketing/kontrak/customer-locations/') ?>${customerId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const result = await response.json();
      if (!result.success) {
        resetCustomerLocationSelection();
        return;
      }
      const options = (result.data || []).map(loc => {
        const monthly = loc.operator_monthly_rate ? ` | Bulanan ${formatIDR(loc.operator_monthly_rate)}` : '';
        const daily = loc.operator_daily_rate ? ` | Harian ${formatIDR(loc.operator_daily_rate)}` : '';
        return `<option value="${loc.id}" data-op-monthly="${loc.operator_monthly_rate || ''}" data-op-daily="${loc.operator_daily_rate || ''}">${loc.location_name}${monthly}${daily}</option>`;
      }).join('');
      customerLocationSelect.innerHTML = '<option value="">-- Pilih Customer Location --</option>' + options;
      customerLocationSelect.disabled = false;
    } catch (error) {
      console.error('Failed to load customer locations:', error);
      resetCustomerLocationSelection();
    }
  }

  customerLocationSelect.addEventListener('change', function() {
    const selected = this.selectedOptions[0];
    const monthly = selected?.dataset?.opMonthly || '';
    const daily = selected?.dataset?.opDaily || '';
    const hasRate = monthly !== '' || daily !== '';
    document.getElementById('operatorRatePreview').style.display = hasRate ? 'block' : 'none';
    document.getElementById('operatorRateMonthly').textContent = `Bulanan: ${monthly !== '' ? formatIDR(monthly) : '-'}`;
    document.getElementById('operatorRateDaily').textContent = `Harian: ${daily !== '' ? formatIDR(daily) : '-'}`;
  });

  function loadReadySpk(q){
    const url = new URL('<?= base_url('marketing/spk/ready-options') ?>', window.location.origin);
    if (q) url.searchParams.set('q', q);
    fetch(url).then(r=>r.json()).then(j=>{
      spkReadyMap = {};
      (j.data || []).forEach(item => { spkReadyMap[String(item.id)] = item; });
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
    if (!id) { section.style.display='none'; list.innerHTML=''; resetCustomerLocationSelection(); return; }
    const selectedSpk = spkReadyMap[String(id)] || null;
    if (selectedSpk) {
      document.getElementById('po_kontrak_nomor').value = selectedSpk.po || '';
      document.getElementById('pelanggan').value = selectedSpk.pelanggan || '';
      pelangganIdInput.value = selectedSpk.customer_id || '';
      loadCustomerLocationsByCustomer(selectedSpk.customer_id || '');
      // If current workflow is TUKAR or ANTAR+TARIK, reload contracts filtered by this customer
      const _jenisKode = getSelectedJenisKode();
      if (_jenisKode === 'TUKAR' || _jenisKode === 'ANTAR_TARIK') {
        loadKontrakOptionsForTukar();
        // Update label to clarify this is KIRIM section
        const lbl = document.getElementById('itemSelectionLabel');
        if (lbl) lbl.innerHTML = '&#128228; Unit yang Dikirim <span class="badge badge-soft-blue ms-1">KIRIM</span> <small class="text-muted fw-normal">(opsional)</small>';
      }
    } else {
      resetCustomerLocationSelection();
    }
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
          const warningBadge = isInActiveDI && activeDI ? ` ${uiBadge('warning', `Already in ${activeDI.nomor_di}`)}` : '';
          
          // Build type label: "Forklift Counter Balance"
          const jenisLabel = [it.jenis_unit, it.tipe_jenis].filter(Boolean).join(' ');
          // Only show attachment if non-empty and not just a dash
          const hasAttachment = it.attachment_label && it.attachment_label.trim() !== '' && it.attachment_label.trim() !== '-';

          wrap.innerHTML = `
            <input class="form-check-input unit-check" type="checkbox" id="${idSafe}" name="unit_ids[]" value="${unitId}" ${checked} ${disabled}>
            <label for="${idSafe}" class="form-check-label">
              <div class="unit-item-title"><strong>${it.unit_label ? it.unit_label.split(' @ ')[0] : ('Unit #' + (idx+1))}</strong>${warningBadge}</div>
              <div class="unit-note">
                <span title="Serial Number"><i class="fas fa-barcode me-1"></i>SN: ${it.serial_number || '-'}</span>
                ${jenisLabel   ? `<span class="unit-detail-pill">${jenisLabel}</span>` : ''}
                ${it.kapasitas_name ? `<span class="unit-detail-pill">${it.kapasitas_name}</span>` : ''}
                ${it.model_unit  ? `<span class="unit-detail-pill">${[it.merk_unit, it.model_unit].filter(Boolean).join(' ')}</span>` : ''}
                ${hasAttachment ? `<span class="d-block mt-1 text-muted"><i class="fas fa-link me-1"></i>${it.attachment_label}</span>` : ''}
              </div>
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
    const customerLocationId = customerLocationSelect.value;
    if (!customerLocationId) {
      OptimaNotify.warning('Pilih Customer Location terlebih dahulu.');
      return;
    }
    
    // Check workflow type
    const _submitJenisKode = getSelectedJenisKode();
    const isTukarWorkflow = _submitJenisKode === 'TUKAR';
    const isAntarTarikWorkflow = _submitJenisKode === 'ANTAR_TARIK';
    const isTukarLikeWorkflow = isTukarWorkflow || isAntarTarikWorkflow;
    const isTarikWorkflow = _submitJenisKode === 'TARIK';
    
    // Enhanced validation for different workflows
    if (isTarikWorkflow) {
      // TARIK workflow: Only needs kontrak and units to tarik
      const tarikUnits = Array.from(document.querySelectorAll('input[name="tarik_units[]"]:checked'));
      
      if (!tarikUnits.length) {
        OptimaNotify.warning('Pilih minimal satu unit yang akan ditarik dari kontrak.');
        return;
      }
      
      // TARIK doesn't need SPK, remove it from form data
      fd.delete('spk_id');
      
      console.log('TARIK Workflow - Tarik:', tarikUnits.length, 'units');
      
    } else if (isTukarLikeWorkflow) {
      // TUKAR / ANTAR+TARIK workflow: SPK (KIRIM) + kontrak + TARIK units
      // KIRIM bisa 0 unit (boleh kosong), TARIK wajib minimal 1
      const tarikUnits = Array.from(document.querySelectorAll('.unit-check-tarik:checked'));
      
      if (!tarikUnits.length) {
        OptimaNotify.warning('Pilih minimal satu unit yang akan ditarik dari kontrak.');
        return;
      }
      
      const spkId = document.getElementById('spkPick').value;
      if (!spkId) {
        OptimaNotify.warning('Pilih SPK untuk workflow ' + (isAntarTarikWorkflow ? 'ANTAR+TARIK' : 'TUKAR') + '.');
        return;
      }
      
      const kontrakId = document.getElementById('kontrakSelect').value;
      if (!kontrakId) {
        OptimaNotify.warning('Pilih kontrak unit lama untuk workflow ' + (isAntarTarikWorkflow ? 'ANTAR+TARIK' : 'TUKAR') + '.');
        return;
      }
      
      const kirimUnits = Array.from(document.querySelectorAll('#diUnitList .unit-check:checked'));
      console.log((isAntarTarikWorkflow ? 'ANTAR+TARIK' : 'TUKAR') + ' Workflow - KIRIM:', kirimUnits.length, 'unit, TARIK:', tarikUnits.length, 'unit dari kontrak', kontrakId);
      
    } else {
      // Standard workflow validation
      const list = document.getElementById('diUnitList');
      if (document.getElementById('diUnitsSection').style.display !== 'none'){
        const selected = list ? Array.from(list.querySelectorAll('.unit-check:checked')) : [];
        if (!selected.length){
          OptimaNotify.warning('Pilih minimal satu unit untuk DI ini.');
          return;
        }
      }
    }
    
    // Debug: Log form data before sending
    console.log('Enhanced DI Create Form Data:');
    for (let [key, value] of fd.entries()) {
      console.log(`  ${key}: ${value}`);
    }
    
    (window.csrfFetch || window.fetch)('<?= base_url('marketing/di/create') ?>',{method:'POST', body: fd})
      .then(r=>r.json()).then(j=>{
        console.log('Enhanced DI Create Response:', j);  // Debug log
        if (j && j.success){
          bootstrap.Modal.getInstance(document.getElementById('diCreateModal')).hide();
          e.target.reset();
          loadDI();
          OptimaNotify.success('DI dibuat: ' + (j.nomor||''));
        } else {
          console.error('Enhanced DI Create Error:', j);
          OptimaNotify.error(j.message || 'Failed to create DI');
        }
      }).catch(error => {
        console.error('Enhanced DI Create Fetch Error:', error);
        OptimaNotify.error('Network error: ' + error.message);
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
    
    (window.csrfFetch || window.fetch)(`<?= base_url('marketing/di/update/') ?>${diId}`, {
      method: 'POST', // treat as POST for CSRF; backend will accept or route accordingly
      body: fd
    }).then(r => r.json()).then(j => {
      console.log('DI Edit Response:', j);
      if (j && j.success) {
        bootstrap.Modal.getInstance(document.getElementById('diEditModal')).hide();
        loadDI(); // Reload data
        OptimaNotify.success('DI berhasil diupdate');
      } else {
        OptimaNotify.error(j.message || 'Gagal mengupdate DI');
      }
    }).catch(error => {
      console.error('DI Edit Error:', error);
      OptimaNotify.error('Network error: ' + error.message);
    });
  });
  
  window.editDI = function(diId) {
    currentEditDiData = {}; // reset
    currentEditKirimItems = [];
    currentEditTarikItems = [];
    // Load data DI yang akan diedit
    fetch(`<?= base_url('marketing/di/detail/') ?>${diId}`)
      .then(r => r.json()).then(j => {
        if (j && j.success) {
          const data = j.data || {};
          currentEditDiData = data; // store for change handler
          currentEditKirimItems = j.kirim_items || [];
          currentEditTarikItems = j.tarik_items || [];
          console.log('[TUKAR] editDI loaded: kirimItems=', currentEditKirimItems.length, 'tarikItems=', currentEditTarikItems.length);
          
          // Populate form edit
          document.getElementById('editDiId').value = diId;
          
          // Set jenis_perintah_kerja_id
          const jenisId = data.jenis_perintah_kerja_id || '';
          document.getElementById('editJenisPerintah').value = jenisId;
          
          // Load tujuan options then set value
          const tujuanId = data.tujuan_perintah_kerja_id || '';
          const tujuanSelect = document.getElementById('editTujuanPerintah');
          if (jenisId) {
            loadTujuanPerintahOptions(jenisId, 'editTujuanPerintah').then(() => {
              tujuanSelect.value = tujuanId;
              tujuanSelect.disabled = false;
            }).catch(() => { tujuanSelect.value = tujuanId; });
          } else {
            tujuanSelect.value = tujuanId;
          }
          
          document.getElementById('editTanggalKirim').value = data.tanggal_kirim || '';
          document.getElementById('editCatatan').value = data.catatan || '';
          
          // PERBAIKAN: Display status eksekusi dengan badge, bukan input
          const statusEksekusi = data.status_eksekusi || 'READY';
          const statusDisplay = document.getElementById('editStatusEksekusiDisplay');
          const statusMap = {
            'READY': { text: 'Ready', cls: 'badge-soft-blue' },
            'DISPATCHED': { text: 'Dispatched', cls: 'badge-soft-yellow' },
            'DELIVERED': { text: 'Delivered', cls: 'badge-soft-green' },
            'CANCELLED': { text: 'Cancelled', cls: 'badge-soft-red' }
          };
          const mapped = statusMap[statusEksekusi] || { text: statusEksekusi, cls: 'badge-soft-gray' };
          statusDisplay.className = `badge ${mapped.cls}`;
          statusDisplay.textContent = mapped.text;

          // TUKAR section: detect via jenisPerintahOptions array (more reliable than text parsing)
          const tukarSection = document.getElementById('editTukarSection');
          const checkTukarAndPopulate = () => {
            const jenisOpt = jenisPerintahOptions.find(o => String(o.id) === String(jenisId));
            const isTukar = jenisOpt && jenisOpt.kode && jenisOpt.kode.toUpperCase() === 'TUKAR';
            console.log('[TUKAR] checkTukarAndPopulate: jenisId=', jenisId, 'kode=', jenisOpt?.kode, 'isTukar=', isTukar);
            if (tukarSection) tukarSection.style.display = isTukar ? 'block' : 'none';
            if (isTukar) populateEditTukarSection(data, currentEditKirimItems, currentEditTarikItems);
          };
          // If jenisPerintahOptions already loaded, run immediately; else wait
          if (jenisPerintahOptions.length > 0) {
            checkTukarAndPopulate();
          } else {
            setTimeout(checkTukarAndPopulate, 500);
          }
          
          // Show modal
          new bootstrap.Modal(document.getElementById('diEditModal')).show();
        } else {
          OptimaNotify.error('Gagal memuat data DI untuk diedit');
        }
      }).catch(error => {
        console.error('Edit DI Load Error:', error);
        OptimaNotify.error('Error loading DI data: ' + error.message);
      });
  };

  // Populate TUKAR section in edit modal
  function populateEditTukarSection(data, kirimItems, tarikItems) {
    console.log('[TUKAR] populateEditTukarSection called. kirimItems:', kirimItems.length, 'tarikItems:', tarikItems.length, 'contract_id:', data.contract_id);

    // Show KIRIM units (read-only)
    const kirimList = document.getElementById('editKirimItemsList');
    if (kirimList) {
      if (kirimItems.length) {
        kirimList.innerHTML = kirimItems.map(it =>
          `<div class="d-flex align-items-center gap-2 py-1">
            <span class="badge badge-soft-green">KIRIM</span>
            <span>${escHtml(it.label) || escHtml(it.no_unit) || 'Unit #' + it.unit_id}</span>
           </div>`
        ).join('');
      } else {
        kirimList.innerHTML = '<span class="text-muted">Tidak ada unit kirim tercatat (DI baru atau belum ada item)</span>';
      }
    } else {
      console.warn('[TUKAR] editKirimItemsList element not found');
    }

    // Load all active contracts for tarik_contract dropdown
    const editTarikKontrak = document.getElementById('editTarikKontrak');
    if (editTarikKontrak) {
      const customerId = data.pelanggan_id ? String(data.pelanggan_id) : '';
      const tarikUrl = '<?= base_url('marketing/kontrak/get-contracts-for-tarik') ?>' +
        (customerId ? '?customer_id=' + encodeURIComponent(customerId) : '');
      fetch(tarikUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      }).then(r => r.json()).then(res => {
        if (res.success && res.data) {
          editTarikKontrak.innerHTML = '<option value="">-- Sama dengan kontrak DI ini --</option>' +
            res.data.map(k => `<option value="${k.id}">${k.label || k.no_kontrak + ' - ' + k.pelanggan}</option>`).join('');
          // Set saved tarik_contract_id
          const tarikContractId = data.tarik_contract_id ? String(data.tarik_contract_id) : '';
          if (tarikContractId) editTarikKontrak.value = tarikContractId;

          // Load units: pakai tarik_contract_id jika ada, fallback ke contract_id DI ini
          const contractToLoad = tarikContractId || (data.contract_id ? String(data.contract_id) : '');
          console.log('[TUKAR] contractToLoad=', contractToLoad);
          if (contractToLoad) {
            loadEditTarikUnits(contractToLoad, tarikItems);
          } else {
            const unitList = document.getElementById('editTarikUnitList');
            if (unitList) unitList.innerHTML = '<span class="text-warning small"><i class="fas fa-exclamation-triangle me-1"></i>DI belum di-link ke kontrak. Link terlebih dahulu lalu edit kembali.</span>';
          }
        } else {
          console.error('[TUKAR] get-contracts-for-tarik failed:', res);
        }
      }).catch(err => {
        console.error('[TUKAR] Error fetching contracts for tarik:', err);
      });

      // When user changes tarik_contract, reload unit list
      editTarikKontrak.onchange = function() {
        const contractId = this.value || (data.contract_id ? String(data.contract_id) : '');
        if (contractId) loadEditTarikUnits(contractId, []);
        else {
          const unitList = document.getElementById('editTarikUnitList');
          if (unitList) unitList.innerHTML = '<span class="text-muted small">Pilih kontrak untuk memuat unit...</span>';
        }
      };
    }
  }

  // Helper: escape HTML for safe rendering
  function escHtml(str) { return str ? String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : ''; }

  // Load + render TARIK unit checkboxes in edit modal
  function loadEditTarikUnits(contractId, preSelected) {
    const listEl = document.getElementById('editTarikUnitList');
    if (!listEl) return;
    listEl.innerHTML = '<span class="text-muted small">Memuat unit...</span>';

    const selectedIds = preSelected.map(it => parseInt(it.unit_id));
    fetch(`<?= base_url('marketing/kontrak/units/') ?>${contractId}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(r => r.json()).then(res => {
      if (res.success && res.data.length) {
        listEl.innerHTML = res.data.map(unit => {
          const uid = unit.unit_id || unit.id;
          const checked = selectedIds.includes(parseInt(uid)) ? 'checked' : '';
          return `<div class="unit-item py-1">
            <input class="form-check-input me-2" type="checkbox" id="editTarik_${uid}"
              name="tarik_unit_ids[]" value="${uid}" ${checked}>
            <label for="editTarik_${uid}" class="form-check-label">
              <strong>${unit.no_unit || 'Unit #' + uid}</strong> — ${unit.merk || ''} ${unit.model || ''}
              <div class="unit-note text-muted small">${unit.kapasitas || ''} | ${unit.status || ''}</div>
            </label>
          </div>`;
        }).join('');
      } else {
        listEl.innerHTML = '<span class="text-muted small">Tidak ada unit dalam kontrak ini</span>';
      }
    }).catch(() => {
      listEl.innerHTML = '<span class="text-danger small">Gagal memuat unit</span>';
    });
  }
  
  window.deleteDI = function(diId) {
    OptimaConfirm.danger({
        title: 'Hapus Delivery Instruction',
        text: 'Apakah Anda yakin ingin menghapus DI ini? Tindakan ini tidak dapat dibatalkan.',
        onConfirm: function() {
            (window.csrfFetch || window.fetch)(`<?= base_url('marketing/di/delete/') ?>${diId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({})
    }).then(r => r.json()).then(j => {
      if (j && j.success) {
        loadDI();
        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('DI berhasil dihapus', 'success');
        else notify('DI berhasil dihapus', 'success');
      } else {
        alertSwal('error', j.message || 'Gagal menghapus DI', 'Error');
      }
    }).catch(error => {
      console.error('Delete DI Error:', error);
      alertSwal('error', 'Network error: ' + error.message);
    });
        }
    });
  };

  // Helper: reload DI DataTable (used in create/edit/delete callbacks)
  function loadDI() {
    if (diTable && diTable.ajax) diTable.ajax.reload();
  }

  // Edit DI from detail modal
  window.editDiFromDetail = function() {
    if (!currentDiId) {
      OptimaNotify.error('DI ID tidak ditemukan');
      return;
    }
    
    // Close detail modal first
    const detailModal = bootstrap.Modal.getInstance(document.getElementById('diDetailModal'));
    if (detailModal) detailModal.hide();
    
    // Call existing edit function
    editDI(currentDiId);
  };
  
  // Delete DI from detail modal with OptimaConfirm
  window.deleteDiFromDetail = function() {
    if (!currentDiId) {
      alertSwal('error', 'DI ID tidak ditemukan');
      return;
    }
    const idToDelete = currentDiId;
    OptimaConfirm.danger({
        title: 'Hapus Delivery Instruction',
        text: 'PERINGATAN: Tindakan ini tidak dapat dibatalkan! Apakah Anda benar-benar yakin ingin menghapus DI ini?',
        icon: 'warning',
        onConfirm: function() {
            // Close detail modal first, then wait for hide animation before fetching
            // to avoid Bootstrap modal reuse conflict (double-confirm race condition)
            const detailModalEl = document.getElementById('diDetailModal');
            const doDelete = function() {
                (window.csrfFetch || window.fetch)(`<?= base_url('marketing/di/delete/') ?>${idToDelete}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({})
                }).then(r => r.json()).then(j => {
                    if (j && j.success) {
                        loadDI();
                        if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') OptimaPro.showNotification('DI berhasil dihapus', 'success');
                        else notify('DI berhasil dihapus', 'success');
                    } else {
                        alertSwal('error', j.message || 'Gagal menghapus DI', 'Error');
                    }
                }).catch(function(error) {
                    console.error('Delete DI Error:', error);
                    alertSwal('error', 'Network error: ' + error.message);
                });
            };

            if (detailModalEl && typeof bootstrap !== 'undefined') {
                const instance = bootstrap.Modal.getInstance(detailModalEl);
                if (instance) {
                    detailModalEl.addEventListener('hidden.bs.modal', doDelete, { once: true });
                    instance.hide();
                } else {
                    doDelete();
                }
            } else {
                doDelete();
            }
        }
    });
  };
  
  // Print DI from detail modal
  window.printDiFromDetail = function() {
    if (!currentDiId) {
      OptimaNotify.error('DI ID tidak ditemukan');
      return;
    }
    
    // Open print DI in new tab (not popup window)
    const printUrl = `<?= base_url('operational/delivery/print/') ?>${currentDiId}`;
    window.open(printUrl, '_blank');
  };
  
  // Print Withdrawal Letter (SPPU) from detail modal
  window.printWithdrawalLetter = function() {
    if (!currentDiId) {
      OptimaNotify.error('DI ID tidak ditemukan');
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

    const contractSelect = document.getElementById('linkContractId');
    contractSelect.innerHTML = '<option value="">Memuat data...</option>';

    try {
      // Single endpoint resolves customer server-side (handles NULL pelanggan_id gracefully)
      const res = await fetch(`<?= base_url('marketing/di/linkable-contracts/') ?>${diId}`, {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
      });
      const data = await res.json();

      if (!data.success) {
        throw new Error(data.message || 'Gagal memuat data kontrak');
      }

      if (data.already_linked) {
        contractSelect.innerHTML = '<option value="">DI ini sudah terhubung ke kontrak</option>';
        modal.show();
        return;
      }

      const contracts = data.contracts || [];
      const poBulanan = data.po_bulanan || [];
      const customerLabel = data.customer_name ? ` (${data.customer_name})` : '';

      contractSelect.innerHTML = '';

      if (contracts.length === 0 && poBulanan.length === 0) {
        const emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = `Tidak ada kontrak/PO tersedia${customerLabel}`;
        contractSelect.appendChild(emptyOpt);
      } else {
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = `- Pilih Kontrak / PO${customerLabel} -`;
        contractSelect.appendChild(placeholder);

        // Group 1: Contracts (all types)
        if (contracts.length > 0) {
          const grp = document.createElement('optgroup');
          grp.label = '— Kontrak & PO —';
          contracts.forEach(c => {
            const typeMap = { CONTRACT: '[Kontrak]', PO_ONLY: '[PO Bulanan]', DAILY_SPOT: '[Spot]' };
            const typeLabel = typeMap[c.rental_type] || `[${c.rental_type}]`;
            const statusNote = c.status !== 'ACTIVE' ? ` ⚠ ${c.status}` : '';
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = `${typeLabel} ${c.no_kontrak}${statusNote} — mulai: ${c.tanggal_mulai || '-'}`;
            if (c.status !== 'ACTIVE') opt.style.color = '#999';
            grp.appendChild(opt);
          });
          contractSelect.appendChild(grp);
        }

        // Group 2: active PO Bulanan entries (contract_po_history)
        if (poBulanan.length > 0) {
          const grp2 = document.createElement('optgroup');
          grp2.label = '— PO Bulanan Aktif —';
          poBulanan.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.contract_id; // links to parent kontrak
            opt.dataset.poNumber = p.po_number;
            opt.textContent = `${p.po_number} (${p.contract_no}) ${p.effective_from || ''} s/d ${p.effective_to || ''}`;
            grp2.appendChild(opt);
          });
          contractSelect.appendChild(grp2);
        }
      }

    } catch (error) {
      console.error('Error loading contracts:', error);
      contractSelect.innerHTML = '<option value="">Gagal memuat data</option>';
      OptimaNotify.error('Gagal memuat kontrak: ' + error.message);
    }

    modal.show();
  };

  /**
   * Handle Link DI to Contract form submission
   */
  document.getElementById('linkDIContractForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    // If user selected a PO Bulanan option, attach the po_number as well
    const selectedOpt = document.getElementById('linkContractId').selectedOptions[0];
    if (selectedOpt && selectedOpt.dataset.poNumber) {
      formData.append('po_number', selectedOpt.dataset.poNumber);
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i> Linking...';
    OptimaPro.showLoading('Linking DI to contract...');
    
    try {
      const response = await (window.csrfFetch || window.fetch)('<?= base_url('marketing/di/link-to-contract') ?>', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('linkDIContractModal'));
        modal.hide();
        
        // Show success message
        OptimaNotify.success(result.message || 'DI has been successfully linked to contract. Invoice generation is now enabled.');
        
        // Reload DI table
        loadDI();
      } else {
        throw new Error(result.message || 'Failed to link contract');
      }
    } catch (error) {
      console.error('Error linking contract:', error);
      OptimaNotify.error(error.message || 'An error occurred while linking contract');
    } finally {
      OptimaPro.hideLoading();
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
        <?= ui_button('print', 'Print SPPU', [
            'id' => 'btnPrintSppu',
            'onclick' => 'printWithdrawalLetter()',
            'style' => 'display:none;',
            'color' => 'success',
            'icon' => 'fas fa-file-contract'
        ]) ?>
        <?= ui_button('print', 'Print DI', ['id' => 'btnPrintDi', 'onclick' => 'printDiFromDetail()']) ?>
        <?= ui_button('edit', 'Edit', ['id' => 'btnEditDi', 'onclick' => 'editDiFromDetail()']) ?>
        <?= ui_button('delete', 'Delete', ['id' => 'btnDeleteDi', 'onclick' => 'deleteDiFromDetail()']) ?>
        <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
      </div>
    </div>
  </div>
</div>

<!-- Edit DI Modal -->
<div class="modal fade" id="diEditModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title"><?= lang('App.edit') ?> Delivery Instruction</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form id="diEditForm">
        <input type="hidden" id="editDiId" name="id">
        <div class="modal-body">
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label"><?= lang('Marketing.command_type') ?> <span class="text-danger">*</span></label>
              <select class="form-select" id="editJenisPerintah" name="jenis_perintah_kerja_id" required>
                <option value="">- <?= lang('Marketing.select_command') ?> -</option>
                <!-- Options will be loaded from API -->
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= lang('Marketing.command_purpose') ?> <span class="text-danger">*</span></label>
              <select class="form-select" id="editTujuanPerintah" name="tujuan_perintah_kerja_id" required disabled>
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
                <span id="editStatusEksekusiDisplay" class="badge badge-soft-blue">READY</span>
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

          <!-- TUKAR Section: shown only when jenis = TUKAR -->
          <div id="editTukarSection" style="display:none;" class="mt-3">
            <hr>
            <h6 class="fw-semibold mb-2"><i class="fas fa-exchange-alt me-1"></i> Workflow TUKAR</h6>

            <!-- Unit baru (KIRIM) — read only info -->
            <div class="mb-3">
              <label class="form-label text-success fw-semibold"><i class="fas fa-arrow-right me-1"></i> Unit Baru (Dikirim dari SPK)</label>
              <div id="editKirimItemsList" class="border rounded p-2 bg-light small text-muted">
                <span class="text-muted">Loading...</span>
              </div>
            </div>

            <!-- Kontrak unit lama -->
            <div class="mb-2">
              <label class="form-label fw-semibold"><i class="fas fa-file-contract me-1"></i> Kontrak Unit Lama (yang ditarik)</label>
              <select class="form-select form-select-sm" id="editTarikKontrak" name="tarik_contract_id">
                <option value="">-- Sama dengan kontrak DI ini --</option>
                <!-- loaded via JS -->
              </select>
              <small class="text-muted">Default: kontrak yang sama. Pilih kontrak lain jika unit lama berasal dari kontrak berbeda (harga berbeda).</small>
            </div>

            <!-- Unit lama (TARIK) -->
            <div>
              <label class="form-label text-warning fw-semibold"><i class="fas fa-arrow-left me-1"></i> Unit Lama (Ditarik)</label>
              <div id="editTarikUnitList" class="unit-list border rounded p-2" style="max-height:200px;overflow-y:auto;">
                <span class="text-muted small">Pilih kontrak untuk memuat unit...</span>
              </div>
              <small class="text-muted">Pilih unit lama yang akan ditarik kembali.</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
          <?= ui_button('save', 'Update DI', ['type' => 'submit']) ?>
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
        <h6 class="modal-title">Link DI ke Kontrak / PO</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="linkDIContractForm">
        <input type="hidden" id="linkDiId" name="di_id">
        <div class="modal-body">
          <div class="alert alert-info small mb-3">
            <i class="fas fa-info-circle"></i>
            Link <strong id="linkDiNumber"></strong> ke Kontrak atau PO Bulanan.
            Ini mengaktifkan pembuatan invoice untuk DI ini.
          </div>

          <div class="mb-3">
            <label class="form-label">Pilih Kontrak / PO <span class="text-danger">*</span></label>
            <select class="form-select" id="linkContractId" name="contract_id" required>
              <option value="">- Pilih Kontrak / PO -</option>
            </select>
            <small class="text-muted">Menampilkan semua kontrak dan PO Bulanan aktif untuk customer terkait.</small>
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
          <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
          <?= ui_button('submit', 'Link Kontrak / PO', ['type' => 'submit', 'color' => 'warning', 'icon' => 'fas fa-link']) ?>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
