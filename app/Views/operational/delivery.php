<?= $this->extend('layouts/base') ?>

<?php
/**
 * Delivery Instructions Processing (Unit Deployment) Module
 *
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 *
 * Quick Reference:
 * - SUBMITTED  → badge-soft-yellow
 * - INPROGRESS → badge-soft-cyan
 * - DELIVERED  → badge-soft-green
 * - CANCELLED  → badge-soft-red
 * - Completed  → badge-soft-green, Pending → badge-soft-yellow
 *
 * See optima-pro.css line ~2030 for complete badge standards
 */
?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
  <div class="row mt-3 mb-4">
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-primary-soft cursor-pointer" data-filter="all">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-truck stat-icon text-primary"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="totalDI">0</div>
                      <div class="text-muted">Total DI</div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-warning-soft cursor-pointer" data-filter="SUBMITTED">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-clock stat-icon text-warning"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="submittedDI">0</div>
                      <div class="text-muted"><?= lang('Common.pending') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-info-soft cursor-pointer" data-filter="INPROGRESS">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-arrow-repeat stat-icon text-info"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="inprogressDI">0</div>
                      <div class="text-muted"><?= lang('App.in_progress') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-success-soft cursor-pointer" data-filter="DELIVERED">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-check-circle stat-icon text-success"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="deliveredDI">0</div>
                      <div class="text-muted"><?= lang('Common.completed') ?></div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="card table-card mb-3">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
      <div>
        <h5 class="card-title mb-0">
          <i class="bi bi-truck me-2 text-primary"></i><?= lang('App.list_delivery_instruction') ?>
        </h5>
        <p class="text-muted small mb-0">
          Process and manage delivery instructions from marketing for unit distribution
          <span class="ms-2 text-info"><i class="bi bi-info-circle me-1"></i><small>Tip: Click stat cards or tabs to filter by status</small></span>
        </p>
      </div>
      <div class="d-flex gap-2 align-items-center">
        <!-- No create button for operational - they process existing DIs -->
      </div>
    </div>
    
    <div class="card-body">
      <!-- Filter Tabs -->
      <ul class="nav nav-tabs mb-3" id="filterTabs">
        <li class="nav-item">
          <a class="nav-link active filter-tab" href="#" data-filter="all"><?= lang('App.all') ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link filter-tab" href="#" data-filter="SUBMITTED"><?= lang('App.submitted') ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link filter-tab" href="#" data-filter="INPROGRESS"><?= lang('App.in_progress') ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link filter-tab" href="#" data-filter="DELIVERED"><?= lang('App.delivered') ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link filter-tab" href="#" data-filter="CANCELLED"><?= lang('App.cancelled') ?></a>
        </li>
      </ul>
    
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0 table-manual-sort" id="diTable">
          <thead class="table-light">
            <tr>
              <th>DI Number</th>
              <th>Customer</th>
              <th>Items</th>
              <th class="d-none d-lg-table-cell">Type</th>
              <th class="d-none d-xl-table-cell">Delivery</th>
              <th>Status</th>
              <th class="d-none d-lg-table-cell">Driver</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <!-- DataTables will add pagination and info here automatically -->
    </div>
  </div>
</div>
<script>
// Global variables for approval workflow
let currentApprovalStage = '';
let currentDiId = null;

// Global variable for filtering
let currentFilter = 'all';
let diTable; // DataTable instance

document.addEventListener('DOMContentLoaded', ()=>{
  
  // Helper render functions for DataTables columns
  const formatTotalItems = (data, type, row) => {
    const totalUnits = row.total_units || 0;
    const totalAttachments = row.total_attachments || 0;
    const jenisSpk = row.jenis_spk || 'UNIT';
    
    if (jenisSpk === 'ATTACHMENT') {
      if (totalAttachments > 0) {
        const text = totalAttachments === 1 ? 'attachment' : 'attachments';
        return `<span class="badge badge-soft-yellow">${totalAttachments} ${text}</span>`;
      }
      return '<span class="text-muted">No attachments</span>';
    } else {
      if (totalUnits > 0) {
        const text = totalUnits === 1 ? 'unit' : 'units';
        return `<span class="badge badge-soft-cyan">${totalUnits} ${text}</span>`;
      } else if (totalAttachments > 0) {
        const text = totalAttachments === 1 ? 'attachment' : 'attachments';
        return `<span class="badge badge-soft-yellow">${totalAttachments} ${text}</span>`;
      }
      return '<span class="text-muted">-</span>';
    }
  };
  
  const formatDriverCompact = (data, type, row) => {
    const driver = row.nama_supir || '-';
    const vehicle = row.no_polisi_kendaraan || '';
    if (driver === '-') return '-';
    return `<div class="small">${driver}${vehicle ? '<br><small class="text-muted">' + vehicle + '</small>' : ''}</div>`;
  };
  
  const getStatusDisplay = (data, type, row) => {
    const statusUpper = (row.status_di || '').toUpperCase();
    const statusMap = {
      'DIAJUKAN': { text: 'SUBMITTED', cls: 'badge-soft-gray' },
      'DISETUJUI': { text: 'APPROVED', cls: 'badge-soft-cyan' },
      'PERSIAPAN_UNIT': { text: 'UNIT_PREPARATION', cls: 'badge-soft-yellow' },
      'SIAP_KIRIM': { text: 'READY_TO_SHIP', cls: 'badge-soft-blue' },
      'DALAM_PERJALANAN': { text: 'IN_TRANSIT', cls: 'badge-soft-yellow' },
      'SAMPAI_LOKASI': { text: 'ARRIVED_AT_LOCATION', cls: 'badge-soft-green' },
      'SELESAI': { text: 'COMPLETED', cls: 'badge-soft-green' },
      'DIBATALKAN': { text: 'CANCELLED', cls: 'badge-soft-red' }
    };
    const mapped = statusMap[statusUpper] || { text: row.status_di || 'SUBMITTED', cls: 'badge-soft-gray' };
    return `<span class="badge ${mapped.cls}">${mapped.text}</span>`;
  };
  
  const formatActions = (data, type, row) => {
    const statusUpper = (row.status_di || '').toUpperCase();
    
    if (!row.status_di || statusUpper === 'DIAJUKAN') {
      return `<button class="btn btn-sm btn-success" onclick="prosesDI(${row.id})"><i class="fas fa-play"></i> Proses DI</button>`;
    }
    
    if (['SIAP_KIRIM', 'DISETUJUI', 'PERSIAPAN_UNIT', 'DALAM_PERJALANAN'].includes(statusUpper)) {
      const perencanaanDone = row.perencanaan_tanggal_approve ? true : false;
      const berangkatDone = row.berangkat_tanggal_approve ? true : false;
      const sampaiDone = row.sampai_tanggal_approve ? true : false;
      
      let buttons = [];
      if (!perencanaanDone) {
        buttons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('perencanaan', 'Plan Shipping', ${row.id})">Plan</button>`);
      } else if (!berangkatDone) {
        buttons.push(`<button class="btn btn-sm btn-primary" onclick="openApprovalModal('berangkat', 'Depart', ${row.id})">Depart</button>`);
      } else if (!sampaiDone) {
        buttons.push(`<button class="btn btn-sm btn-success" onclick="openApprovalModal('sampai', 'Arrive', ${row.id})">Arrive</button>`);
      } else {
        buttons.push('<span class="text-success small">Completed</span>');
      }
      
      const badges = [];
      if (perencanaanDone) badges.push('<span class="badge badge-soft-green">✓</span>');
      if (berangkatDone) badges.push('<span class="badge badge-soft-green">✓</span>');
      if (sampaiDone) badges.push('<span class="badge badge-soft-green">✓</span>');
      
      let html = `<div class="stage-buttons">${buttons.join(' ')}</div>`;
      if (badges.length > 0) {
        html += `<div class="stage-badges mt-1">${badges.join('')}</div>`;
      }
      return html;
    }
    
    if (['SAMPAI_LOKASI', 'SELESAI'].includes(statusUpper)) {
      return '<span class="text-success">Completed</span>';
    }
    
    return '<span class="text-muted">-</span>';
  };
  
  // Initialize DataTable
  try {
    diTable = OptimaDataTable.init('#diTable', {
      ajax: {
        url: '<?= base_url('operational/delivery/data') ?>',
        type: 'POST',
        data: function(d) {
          d.status_filter = currentFilter;
          return d;
        }
      },
      serverSide: true,
      pageLength: 25,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      order: [[0, 'desc']],
      columns: [
        { 
          data: 'nomor_di',
          responsivePriority: 1,
          render: function(data, type, row) {
            return `<a href="#" onclick="openDiDetail(${row.id});return false;" class="fw-medium">${data}</a>`;
          }
        },
        { 
          data: 'pelanggan',
          responsivePriority: 2,
          render: function(data, type, row) {
            const pelanggan = data || '-';
            return pelanggan.length > 15 ? 
              `<span class="small">${pelanggan.substring(0, 15)}...</span>` : 
              `<span class="small">${pelanggan}</span>`;
          }
        },
        { 
          data: null,
          responsivePriority: 3,
          className: 'text-center',
          render: formatTotalItems,
          orderable: false
        },
        { 
          data: 'jenis_perintah',
          className: 'd-none d-lg-table-cell small',
          responsivePriority: 6,
          defaultContent: '-'
        },
        { 
          data: 'requested_delivery_date',
          className: 'd-none d-xl-table-cell small',
          responsivePriority: 7,
          defaultContent: '-'
        },
        { 
          data: 'status_di',
          responsivePriority: 4,
          render: getStatusDisplay
        },
        { 
          data: null,
          className: 'd-none d-lg-table-cell small',
          responsivePriority: 8,
          render: formatDriverCompact,
          orderable: false
        },
        { 
          data: null,
          responsivePriority: 5,
          render: formatActions,
          orderable: false
        }
      ]
    });
    
    // console.log('✅ Operational Delivery DataTable initialized');
  } catch(error) {
    console.error('❌ Failed to initialize DataTable:', error);
  }
  
  // Load statistics - make it global so prosesDI can call it
  window.loadStatistics = function() {
    fetch('<?= base_url('operational/delivery/stats') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ status_filter: currentFilter })
    })
    .then(r => r.json())
    .then(stats => {
      document.getElementById('totalDI').textContent = stats.total || 0;
      document.getElementById('submittedDI').textContent = stats.submitted || 0;
      document.getElementById('inprogressDI').textContent = stats.inprogress || 0;
      document.getElementById('deliveredDI').textContent = stats.delivered || 0;
    })
    .catch(err => console.error('Failed to load statistics:', err));
  }
  
  // Initial stats load
  loadStatistics();
  
  // Filter handling
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
      const card = document.querySelector(`.filter-card[data-filter="${filter}"]`);
      if (card) card.classList.add('active');
      
      // Reload table with new filter
      if (diTable) {
        diTable.ajax.reload();
      }
    });
  });
  
  // Filter card click listeners
  document.querySelectorAll('.filter-card').forEach(card => {
    card.addEventListener('click', function() {
      const filter = this.dataset.filter;
      currentFilter = filter;
      
      // Update active card
      document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      
      // Update active tab
      document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
      const tab = document.querySelector(`.filter-tab[data-filter="${filter}"]`);
      if (tab) tab.classList.add('active');
      
      // Reload table with new filter
      if (diTable) {
        diTable.ajax.reload();
      }
    });
  });
  
  // Add form submission handler for approval stage (must be inside DOMContentLoaded)
  const approvalForm = document.getElementById('approvalStageForm');
  if (approvalForm) {
    approvalForm.addEventListener('submit', function(e){
      e.preventDefault();
      
      // Debug log
      // console.log('Form submitted. Current variables:', { currentApprovalStage, currentDiId });
      
      // Check if currentApprovalStage is defined
      if (!currentApprovalStage) {
        console.error('currentApprovalStage is not defined!');
        notify('Error: Stage approval tidak terdefinisi', 'error');
        return;
      }
      
      const fd = new FormData(this);
      fd.append('stage', currentApprovalStage);
      
      // Debug: Log all form data
      // console.log('Form data being sent:');
      for (let [key, value] of fd.entries()) {
        // console.log(key, value);
      }
      
      fetch(`<?= base_url('operational/delivery/approve-stage/') ?>${currentDiId}`, {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        body: fd
      }).then(r=>{
        if (!r.ok) {
          throw new Error(`HTTP Error: ${r.status} ${r.statusText}`);
        }
        return r.json();
      }).then(j=>{
        // console.log('Server response:', j); // Debug log
        if (j && j.success) {
          bootstrap.Modal.getInstance(document.getElementById('approvalStageModal')).hide();
          
          // Check if we need to switch filter based on stage completion
          // After 'sampai' approval, status becomes SAMPAI_LOKASI (DELIVERED filter)
          if (currentApprovalStage === 'sampai' && currentFilter === 'INPROGRESS') {
            // console.log('🔀 Stage sampai completed - switching to DELIVERED filter');
            currentFilter = 'DELIVERED';
            
            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            const deliveredTab = document.querySelector('.filter-tab[data-filter="DELIVERED"]');
            if (deliveredTab) deliveredTab.classList.add('active');
            
            // Update active card
            document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
            const deliveredCard = document.querySelector('.filter-card[data-filter="DELIVERED"]');
            if (deliveredCard) deliveredCard.classList.add('active');
          }
          
          // Reload table to update buttons
          if (diTable) diTable.ajax.reload(null, false);
          
          // Reload statistics
          loadStatistics();
          
          notify(j.message || 'Approval saved successfully', 'success');
        } else {
          console.error('Server error:', j); // Debug log
          notify(j.message || 'Failed to save approval', 'error');
        }
      }).catch(error=>{
        console.error('Error:', error);
        notify('System error occurred: ' + error.message, 'error');
      });
    });
  }
  
});
  
  window.openApprovalModal = (stage, stageTitle, diId) => {
    currentApprovalStage = stage;
    currentDiId = diId;
    
    // Debug log
    // console.log('Opening approval modal:', { stage, stageTitle, diId });
    // console.log('Current variables:', { currentApprovalStage, currentDiId });
    
    document.getElementById('approvalStageTitle').textContent = stageTitle;
    
    // Load stage-specific content
    loadStageSpecificContent(stage, diId);
    
    new bootstrap.Modal(document.getElementById('approvalStageModal')).show();
  }
  
  function loadStageSpecificContent(stage, diId) {
    const container = document.getElementById('stageSpecificContent');
    
    if (stage === 'perencanaan') {
      // Get DI details to show current data for confirmation
      fetch(`<?= base_url('operational/delivery/detail/') ?>${diId}`).then(r=>r.json()).then(j=>{
        if (j.success) {
          const di = j.data || {};
          const spk = j.spk || {};
          
          container.innerHTML = `
            <hr>
            <div class="alert alert-info">
              <strong><i class="fas fa-info-circle"></i>Shipping Plan</strong><br>
              Complete all delivery data for the planning stage.
            </div>
            <div class="row g-3">
              <div class="col-6">
                <label class="form-label">SPK No.</label>
                <input type="text" class="form-control-plaintext" readonly value="${spk.nomor_spk || '-'}">
              </div>
              <div class="col-6">
                <label class="form-label">Source</label>
                <input type="text" class="form-control-plaintext" readonly value="${di.contract_id ? 'Contract' : (di.quotation_number || 'Quotation')}" style="color: ${di.contract_id ? '#28a745' : '#ffc107'}; font-weight: 500;">
              </div>
              <div class="col-6">
                <label class="form-label">Customer</label>
                <input type="text" class="form-control-plaintext" readonly value="${di.pelanggan || '-'}">
              </div>
              <div class="col-6">
                <label class="form-label">Shipping Location</label>
                <textarea class="form-control-plaintext readonly-textarea resize-none" readonly rows="3">${di.lokasi || '-'}</textarea>
              </div>
              <div class="col-12"><hr></div>
              <div class="col-12"><h6 class="text-primary">Operational Delivery Data</h6></div>
              <div class="col-6 mb-3">
                <label class="form-label">Shipping Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal_kirim" required>
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">Estimated Arrival <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="estimasi_sampai" required>
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">Driver Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_supir" required placeholder="Full name of the driver">
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">Driver Phone Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="no_hp_supir" required placeholder="08xxxxxxxxxx">
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">Driver License Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="no_sim_supir" required placeholder="Driver's license number">
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">Vehicle <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="kendaraan" required placeholder="Type/brand of vehicle">
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Vehicle License Plate Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="no_polisi_kendaraan" required placeholder="Vehicle license plate number">
              </div>
              <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="catatan_perencanaan" rows="3" 
                          placeholder="Enter notes for delivery planning (optional)..."></textarea>
                <div class="form-text">These notes will assist the operational team in the next steps.</div>
              </div>
            </div>
          `;
        }
      });
      
      
    } else if (stage === 'berangkat') {
      container.innerHTML = `
        <hr>
        <div class="alert alert-warning">
          <strong><i class="fas fa-truck"></i> Departure Confirmation</strong><br>
          Confirm that the delivery has started and add notes if necessary.
        </div>
        <div class="alert alert-info">
          <strong>Info:</strong> Delivery operational data has been set in the planning stage.
        </div>
        <div class="mb-3">
          <label class="form-label">Departure Notes</label>
          <textarea class="form-control" name="catatan_berangkat" rows="4" 
                    placeholder="Enter departure notes, condition of goods at departure, or other additional information..."></textarea>
          <div class="form-text">These notes will serve as documentation when the goods depart from the origin location.</div>
        </div>
      `;
      
    } else if (stage === 'sampai') {
      container.innerHTML = `
        <hr>
        <div class="alert alert-success">
          <strong><i class="fas fa-map-marker-alt"></i> Arrival Confirmation</strong><br>
          Confirm that the goods have arrived safely at the destination.
        </div>
        <div class="mb-3">
          <label class="form-label">Arrival Notes <span class="text-danger">*</span></label>
          <textarea class="form-control" name="catatan_sampai" rows="4" required 
                    placeholder="Example: Goods have arrived safely. Delivered to Mr. John (Operations Manager). Unit condition is good, no damage. BAST was signed at 14:30 WIB."></textarea>
          <div class="form-text">
            <strong>Make sure to include:</strong><br>
            • Recipient's name and position<br>
            • Condition of goods upon arrival<br>
            • Time of handover<br>
            • BAST/document status
          </div>
        </div>
      `;
      
    } else {
      container.innerHTML = '';
    }
  }
  
  // Unified notifier (fallbacks)
  window.notify = function(msg, type='success'){
    if (window.OptimaNotify && typeof OptimaNotify[type] === 'function') return OptimaNotify[type](msg);
    if (window.OptimaPro && typeof OptimaPro.showNotification==='function') return OptimaPro.showNotification(msg, type);
  }
  
  // Workflow indicator formatter
  // Proses DI function - can be called from table or modal
  window.prosesDI = async function(id) {
    const { isConfirmed } = await Swal.fire({
      title: 'Proses DI?',
      text: 'Status akan berubah ke SIAP KIRIM dan masuk workflow operasional.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Proses!',
      cancelButtonText: 'Batal'
    });
    if (!isConfirmed) return;
    
    // console.log('🚀 prosesDI called for DI:', id);
    
    const formData = new FormData();
    formData.append('action', 'assign_driver');
    formData.append('nama_supir', '');
    formData.append('no_hp_supir', '-');
    formData.append('no_sim_supir', '-');
    formData.append('kendaraan', '');
    formData.append('no_polisi_kendaraan', '-');
    
    // console.log('📤 Sending request to:', `<?= base_url('operational/delivery/update-status/') ?>${id}`);
    
    fetch(`<?= base_url('operational/delivery/update-status/') ?>${id}`, {
      method: 'POST',
      headers: {'X-Requested-With': 'XMLHttpRequest'},
      body: formData
    })
    .then(r => {
      // console.log('📥 Response status:', r.status, r.statusText);
      if (!r.ok) {
        throw new Error(`HTTP ${r.status}: ${r.statusText}`);
      }
      return r.json();
    })
    .then(result => {
      // console.log('✅ Server response:', result);
      // console.log('🔍 Current filter value:', currentFilter);
      
      if (result && result.success) {
        notify('DI berhasil diproses. Silakan lanjutkan ke tahap Perencanaan untuk mengisi detail operasional.', 'success');
        // console.log('🔄 Reloading table...');
        
        // CRITICAL: Switch to INPROGRESS filter so user can see the updated DI
        // When status changes from DIAJUKAN → SIAP_KIRIM, it no longer matches SUBMITTED filter
        // console.log('🔍 Checking filter switch condition: currentFilter === "SUBMITTED"?', currentFilter === 'SUBMITTED');
        
        if (currentFilter === 'SUBMITTED') {
          // console.log('🔀 Switching from SUBMITTED to INPROGRESS filter');
          currentFilter = 'INPROGRESS';
          
          // Update active tab
          document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
          const inprogressTab = document.querySelector('.filter-tab[data-filter="INPROGRESS"]');
          if (inprogressTab) {
            inprogressTab.classList.add('active');
            // console.log('✅ INPROGRESS tab activated');
          } else {
            console.warn('⚠️ INPROGRESS tab not found');
          }
          
          // Update active card
          document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
          const inprogressCard = document.querySelector('.filter-card[data-filter="INPROGRESS"]');
          if (inprogressCard) {
            inprogressCard.classList.add('active');
            // console.log('✅ INPROGRESS card activated');
          } else {
            console.warn('⚠️ INPROGRESS card not found');
          }
        } else {
          // console.log('⚠️ Filter switch skipped - not on SUBMITTED filter');
        }
        
        if (typeof diTable !== 'undefined' && diTable) {
          diTable.ajax.reload(null, false); // false = stay on current page
          // console.log('✅ Table reloaded');
        } else {
          console.error('❌ diTable is not defined!');
        }
        
        // Reload statistics to update counts
        loadStatistics();
      } else {
        console.error('❌ Server returned failure:', result);
        notify(result.message || 'Gagal memproses DI', 'error');
      }
    })
    .catch(err => {
      console.error('❌ Error in prosesDI:', err);
      notify('Terjadi kesalahan saat memproses DI: ' + err.message, 'error');
    });
  };
  
  window.formatTujuanWithIndicator = function(tujuan) {
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
  }
  
  window.openDiDetail = (id) => {
    currentDiId = id;
    const modal = new bootstrap.Modal(document.getElementById('diDetailModal'));
    const body = document.getElementById('diDetailBody');
    body.innerHTML = '<p class="text-muted">Loading...</p>';
    fetch('<?= base_url('operational/delivery/detail/') ?>'+id).then(r=>r.json()).then(j=>{
      if (!j.success) { body.innerHTML = '<div class="text-danger">Failed to load details</div>'; modal.show(); return; }
      const d = j.data||{}; const spk = j.spk||{}; const items = j.items||[]; 
      const spesifikasi = j.spesifikasi||{}; const kontrak = j.kontrak||{};
      
      // Parse spesifikasi JSON if exists (backup method)
      let s = spesifikasi;
      if (spk.spesifikasi && Object.keys(s).length === 0) {
        try {
          s = JSON.parse(spk.spesifikasi);
        } catch (e) {
          // console.log('Failed to parse spesifikasi:', e);
        }
      }
      const k = kontrak;
      
      const status = d.status_di || d.status_eksekusi || d.status || 'SUBMITTED';
      // Enhanced: Detect SPK type for proper detail display
      const spkType = spk.jenis_spk || d.jenis_spk || 'UNIT';
      const isAttachmentSpk = (spkType === 'ATTACHMENT');
      // Display items in structured format with modern design
      let itemsHtml = '';
      if (j.items && j.items.length > 0) {
        const unitItems = j.items.filter(i => i.item_type === 'UNIT');
        const attachmentItems = j.items.filter(i => i.item_type === 'ATTACHMENT');
        
        if (isAttachmentSpk) {
          // For ATTACHMENT SPK, focus on attachments
          itemsHtml += '<h6 class="text-muted mb-3">Attachments Sent:</h6>';
          if (attachmentItems.length > 0) {
            itemsHtml += '<div class="list-group list-group-flush">';
            attachmentItems.forEach(item => {
              // Use item data first, then spesifikasi and kontrak as fallback (like print_di.php)
              const attachName = item.att_tipe || s.attachment_tipe || k.attachment_tipe || k.attachment_name || 'Attachment';
              const attachMerk = item.att_merk || s.attachment_merk || k.attachment_merk || '-';
              const attachModel = item.att_model || s.attachment_model || k.attachment_model || '';
              const attachSN = item.sn_attachment ? ` (SN: ${item.sn_attachment})` : '';
              const fullAttachmentName = attachModel ? `${attachName} ${attachModel}` : attachName;
              
              itemsHtml += `
                <div class="list-group-item border-0 px-0">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-paperclip text-primary me-3"></i>
                    <div>
                      <div class="fw-semibold">${fullAttachmentName}${attachSN}</div>
                      <small class="text-muted">Merk: ${attachMerk}</small>
                    </div>
                  </div>
                </div>`;
            });
            itemsHtml += '</div>';
          } else if (j.attachments && j.attachments.length > 0) {
            // Use standalone attachments from backend (fallback from spesifikasi)
            itemsHtml += '<div class="list-group list-group-flush">';
            j.attachments.forEach(attachment => {
              const attachName = attachment.tipe || 'Attachment';
              const attachMerk = attachment.merk || '-';
              const attachModel = attachment.model || '';
              const attachSN = attachment.sn_attachment && attachment.sn_attachment !== '-' ? ` (SN: ${attachment.sn_attachment})` : '';
              const fullAttachmentName = attachModel ? `${attachName} ${attachModel}` : attachName;
              
              itemsHtml += `
                <div class="list-group-item border-0 px-0">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-paperclip text-primary me-3"></i>
                    <div>
                      <div class="fw-semibold">${fullAttachmentName}${attachSN}</div>
                      <small class="text-muted">Merk: ${attachMerk}</small>
                    </div>
                  </div>
                </div>`;
            });
            itemsHtml += '</div>';
          } else {
            // Final fallback to spesifikasi data for ATTACHMENT SPK when no itemsnformasi Attachment (like print_di.php)
            const attachType = s.attachment_tipe || k.attachment_tipe || k.attachment_name || 'Attachment';
            const attachMerk = s.attachment_merk || k.attachment_merk || '-';
            const attachModel = s.attachment_model || k.attachment_model || '';
            const fullAttachmentName = attachModel ? `${attachType} ${attachModel}` : attachType;
            itemsHtml += `
              <div class="card border-info">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-paperclip text-info me-3"></i>
                    <div>
                      <div class="fw-semibold">${fullAttachmentName}</div>
                      <small class="text-muted">Merk: ${attachMerk}</small>
                    </div>
                  </div>
                </div>
              </div>`;
          }
        } else {
          // For UNIT SPK, display units with their details
          itemsHtml += '<div class="list-group list-group-flush">';
          j.items.forEach((unitData, index) => {
            const unitNum = index + 1;
            
            // Format unit info with detailed data: no_unit.model_unit.kapasitas_unit_id.departemen_id
            const unitInfo = unitData.unit_info || {};
            const unitLabel = `${unitInfo.no_unit || '-'} • ${unitInfo.model_unit || '-'} • ${unitInfo.kapasitas_unit_nama || unitInfo.kapasitas_unit_id || '-'} • ${unitInfo.departemen_nama || unitInfo.departemen_id || '-'}`;
            
            itemsHtml += `
              <div class="list-group-item border-0 px-0">
                <div class="d-flex align-items-start">
                  <i class="bi bi-truck text-success me-3 mt-1"></i>
                  <div class="flex-grow-1">
                    <div class="fw-semibold">Unit ${unitNum}: ${unitLabel}</div>`;
            
            // Display battery and charger if electric
            if (unitData.unit_info.jenis_power && unitData.unit_info.jenis_power.toLowerCase().includes('electric')) {
              itemsHtml += '<div class="mt-2 ms-3">';
              
              if (unitData.battery) {
                // Format battery with detailed info: merk_baterai.tipe_baterai.jenis_baterai
                const battery = unitData.battery;
                const batteryLabel = `${battery.merk_baterai || '-'} • ${battery.tipe_baterai || '-'} • ${battery.jenis_baterai || '-'}`;
                itemsHtml += `
                  <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-battery text-warning me-2"></i>
                    <small><strong>BATTERY</strong> - ${batteryLabel}</small>
                  </div>`;
              }
              
              if (unitData.charger) {
                // Format charger with detailed info: merk_charger.tipe_charger
                const charger = unitData.charger;
                const chargerLabel = `${charger.merk_charger || '-'} • ${charger.tipe_charger || '-'}`;
                itemsHtml += `
                  <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-plug text-info me-2"></i>
                    <small><strong>CHARGER</strong> - ${chargerLabel}</small>
                  </div>`;
              }
              
              itemsHtml += '</div>';
            }
            
            // Display unit-specific attachments with enhanced details
            if (unitData.attachments && unitData.attachments.length > 0) {
              itemsHtml += '<div class="mt-2 ms-3">';
              unitData.attachments.forEach(attachment => {
                // Use print_di.php fallback logic: attachment data first, then spesifikasi, then kontrak
                const attachmentName = attachment.tipe || s.attachment_tipe || k.attachment_tipe || k.attachment_name || 'Attachment';
                const attachmentMerk = attachment.merk || s.attachment_merk || k.attachment_merk || '-';
                const attachmentModel = attachment.model || s.attachment_model || k.attachment_model || '';
                const attachmentSN = attachment.sn_attachment && attachment.sn_attachment !== '-' ? ` (SN: ${attachment.sn_attachment})` : '';
                const fullAttachmentName = attachmentModel ? `${attachmentName} ${attachmentModel}` : attachmentName;
                
                itemsHtml += `
                  <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-plus-circle text-primary me-2"></i>
                    <small><strong>${fullAttachmentName}</strong>${attachmentSN}<br>
                      <span class="text-muted">Merk: ${attachmentMerk}</span>
                    </small>
                  </div>`;
              });
              itemsHtml += '</div>';
            }
            
            itemsHtml += `
                  </div>
                </div>
              </div>`;
          });
          itemsHtml += '</div>';
        }
      }
      
      // For ATTACHMENT SPK with no items, show attachment data from spesifikasi/kontrak
      if (isAttachmentSpk && !itemsHtml && (j.attachments && j.attachments.length > 0)) {
        itemsHtml += '<h6 class="text-muted mb-3">Shipping Attachments:</h6>';
        itemsHtml += '<div class="list-group list-group-flush">';
        j.attachments.forEach(attachment => {
          const attachName = attachment.tipe || 'Attachment';
          const attachMerk = attachment.merk || '-';
          const attachModel = attachment.model || '';
          const attachSN = attachment.sn_attachment && attachment.sn_attachment !== '-' ? ` (SN: ${attachment.sn_attachment})` : '';
          const fullAttachmentName = attachModel ? `${attachName} ${attachModel}` : attachName;
          
          itemsHtml += `
            <div class="list-group-item border-0 px-0">
              <div class="d-flex align-items-center">
                <i class="bi bi-paperclip text-primary me-3"></i>
                <div>
                  <div class="fw-semibold">${fullAttachmentName}${attachSN}</div>
                  <small class="text-muted">Merk: ${attachMerk}</small>
                </div>
              </div>
            </div>`;
        });
        itemsHtml += '</div>';
      }
      
      // Display general attachments (not unit-specific) with enhanced details
      if (j.attachments && j.attachments.length > 0 && !isAttachmentSpk) {
        itemsHtml += '<h6 class="text-muted mb-3 mt-4">Additional Attachments:</h6>';
        itemsHtml += '<div class="list-group list-group-flush">';
        j.attachments.forEach(attachment => {
          // Use print_di.php fallback logic: attachment data first, then spesifikasi, then kontrak
          const attachmentName = attachment.tipe || s.attachment_tipe || k.attachment_tipe || k.attachment_name || 'Attachment';
          const attachmentMerk = attachment.merk || s.attachment_merk || k.attachment_merk || '-';
          const attachmentModel = attachment.model || s.attachment_model || k.attachment_model || '';
          const attachmentSN = attachment.sn_attachment && attachment.sn_attachment !== '-' ? ` (SN: ${attachment.sn_attachment})` : '';
          const fullAttachmentName = attachmentModel ? `${attachmentName} ${attachmentModel}` : attachmentName;
          
          itemsHtml += `
            <div class="list-group-item border-0 px-0">
              <div class="d-flex align-items-center">
                <i class="bi bi-paperclip text-warning me-3"></i>
                <div>
                  <div class="fw-semibold">${fullAttachmentName}${attachmentSN}</div>
                  <small class="text-muted">Merk: ${attachmentMerk}</small>
                </div>
              </div>
            </div>`;
        });
        itemsHtml += '</div>';
      }
      
      if (!itemsHtml) {
        itemsHtml = '<div class="alert alert-warning border-warning"><small><i class="bi bi-exclamation-triangle me-2"></i>No items have been prepared for this delivery yet.</small></div>';
      }
      
      // Update action buttons based on status
      const actionDiv = document.getElementById('modalActionButtons');
      let actionButtons = '';
      
      // Debug: Log the actual status and raw data
      // console.log('🔍 DI Modal Status Debug:', { 
        status, 
        status_di: d.status_di, 
        status_eksekusi: d.status_eksekusi,
        status_field: d.status,
        spk: spk?.id,
        raw_d: d 
      });
      
      // Determine action buttons based on status
      if (status === 'SUBMITTED' || status === 'DIAJUKAN') {
        // Only show Proses DI button for initial statuses
        actionButtons = '<button class="btn btn-success btn-sm" id="btnProsesDI">Process DI</button>';
        // console.log('✅ Showing Process DI button for status:', status);
      } else if (status === 'SIAP_KIRIM' || status === 'DISETUJUI' || status === 'PERSIAPAN_UNIT' || status === 'DALAM_PERJALANAN') {
        // Show approval stage buttons for processed statuses
        let approvalButtons = [];
        
        // Check which stages are completed
        const perencanaanDone = d.perencanaan_tanggal_approve ? true : false;
        const berangkatDone = d.berangkat_tanggal_approve ? true : false;
        const sampaiDone = d.sampai_tanggal_approve ? true : false;
        
        // Add buttons for incomplete stages - MUST pass id parameter!
        if (!perencanaanDone) {
          approvalButtons.push(`<button class="btn btn-warning btn-sm" onclick="openApprovalModal('perencanaan', 'Perencanaan Pengiriman', ${id})">Plan</button>`);
        } else if (!berangkatDone) {
          approvalButtons.push(`<button class="btn btn-warning btn-sm" onclick="openApprovalModal('berangkat', 'Berangkat', ${id})">Depart</button>`);
        } else if (!sampaiDone) {
          approvalButtons.push(`<button class="btn btn-warning btn-sm" onclick="openApprovalModal('sampai', 'Sampai', ${id})">Arrive</button>`);
        }
        
        // Show completed stages with checkmarks
        if (perencanaanDone) approvalButtons.push('<span class="badge badge-soft-green me-1">✓ Plan</span>');
        if (berangkatDone) approvalButtons.push('<span class="badge badge-soft-green me-1">✓ Depart</span>');
        if (sampaiDone) approvalButtons.push('<span class="badge badge-soft-green me-1">✓ Arrive</span>');
        
        actionButtons = approvalButtons.join(' ');
        // console.log('✅ Showing workflow buttons for status:', status);
      } else if (status === 'DELIVERED' || status === 'SELESAI' || status === 'COMPLETED') {
        // Show completed status
        actionButtons = `<span class="badge badge-soft-green">Completed</span>`;
        // console.log('✅ Showing completed status for:', status);
      } else {
        // For any other status, show no action buttons
        actionButtons = '';
        // console.log('⚠️ No action buttons for status:', status);
      }
      
      // Add Print SPK button if SPK exists (for all statuses except SUBMITTED and DIAJUKAN)
      if (status !== 'SUBMITTED' && status !== 'DIAJUKAN' && spk && spk.id) {
        const printSPKButton = `<a class="btn btn-outline-success btn-sm" href="<?= base_url('marketing/spk/print/') ?>${spk.id}" target="_blank" rel="noopener"><i class="fas fa-print"></i> Print SPK</a>`;
        actionButtons = actionButtons ? `${actionButtons} ${printSPKButton}` : printSPKButton;
      }
      
      // Add Print DI button right after PDF SPK button - open in new tab like PDF SPK
      const printDIButton = `<a class="btn btn-outline-primary btn-sm" href="<?= base_url('operational/delivery/print/') ?>${id}" target="_blank" rel="noopener"><i class="fas fa-print"></i> Print DI</a>`;
      actionButtons = actionButtons ? `${actionButtons} ${printDIButton}` : printDIButton;
      
      // Add Print SPPU button for TARIK, TUKAR, and RELOKASI command types (use kode, not nama)
      const jenisPerintahKode = d.jenis_perintah_kode || '';
      if (jenisPerintahKode === 'TARIK' || jenisPerintahKode === 'TUKAR' || jenisPerintahKode === 'RELOKASI') {
        const printSPPUButton = `<a class="btn btn-outline-success btn-sm" href="<?= base_url('marketing/di/print-withdrawal/') ?>${id}" target="_blank" rel="noopener"><i class="fas fa-file-contract"></i> Print SPPU</a>`;
        actionButtons = actionButtons ? `${actionButtons} ${printSPPUButton}` : printSPPUButton;
        // console.log('✅ Added Print SPPU button for command type:', jenisPerintahKode);
      }
      
      body.innerHTML = `
        <div class="row g-3">
          <!-- Basic Information -->
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-primary">
              <i class="bi bi-clipboard-data me-2"></i>Document Information
            </h6>
            <div class="row g-2">
              <div class="col-6"><strong>DI Number:</strong> ${d.nomor_di}</div>
              <div class="col-6"><strong>Status:</strong> <span class="badge badge-soft-gray">${status}</span></div>
              <div class="col-6"><strong>SPK Number:</strong> ${spk && spk.nomor_spk ? spk.nomor_spk : '-'}</div>
              <div class="col-6"><strong>SPK Type:</strong> <span class="badge ${isAttachmentSpk ? 'badge-soft-yellow' : 'badge-soft-cyan'}">${spkType}</span></div>
              <div class="col-6"><strong>Source:</strong> <span style="color: ${d.contract_id ? '#28a745' : '#ffc107'}; font-weight: 500;">${d.contract_id ? 'Contract' : (d.quotation_number || 'Quotation')}</span></div>
              <div class="col-6"><strong>Delivery Date:</strong> ${d.tanggal_kirim||'-'}</div>
              <div class="col-6"><strong>Company Name:</strong> ${d.pelanggan||'-'}</div>
              <div class="col-6"><strong>PIC:</strong> ${spk.pic||'-'}</div>
              <div class="col-6"><strong>Contact:</strong> ${spk.kontak||'-'}</div>
              <div class="col-12"><strong>Delivery Location:</strong><br>
                <div class="bg-light p-2 rounded border mt-1 word-wrap-break max-h-100px-scroll">${d.lokasi||'-'}</div>
              </div>
            </div>
          </div>
          
          <!-- Workflow Information -->
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-info">
              <i class="bi bi-gear me-2"></i>Workflow Information
            </h6>
            <div class="row g-2">
              <div class="col-6"><strong>Command Type:</strong> ${d.jenis_perintah||'-'}</div>
              <div class="col-6"><strong>Command Destination:</strong> ${formatTujuanWithIndicator(d.tujuan_perintah)}</div>
              <div class="col-6"><strong>Execution Status:</strong> ${d.status_eksekusi||'Pending'}</div>
              <div class="col-6"><strong>Created By:</strong> ${d.dibuat_oleh_name || d.dibuat_oleh || '-'}</div>
            </div>
          </div>
          
          <!-- Items Detail -->
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-success">
              <i class="bi bi-truck me-2"></i>Detail Items Delivered
            </h6>
            ${itemsHtml}
          </div>
          
          ${status === 'PROCESSED' || status === 'DELIVERED' ? `
          <div class="col-12"><hr></div>
          <div class="col-12"><h6 class="mb-2">📋 Status Delivery Workflow</h6></div>
          
          <div class="col-12">
            <div class="row g-2">
              <div class="col-4">
                <strong>1. Plan:</strong> 
                ${d.perencanaan_tanggal_approve ? 
                  `<span class="badge badge-soft-green">✓ Completed</span><br>
                  Date: ${d.perencanaan_tanggal_approve||'-'}</small>` 
                  : '<span class="badge badge-soft-yellow">Pending</span>'}
              </div>
              <div class="col-4">
                <strong>2. Departure:</strong> 
                ${d.berangkat_tanggal_approve ? 
                  `<span class="badge badge-soft-green">✓ Completed</span><br>
                  Date: ${d.berangkat_tanggal_approve||'-'}</small>` 
                  : '<span class="badge badge-soft-yellow">Pending</span>'}
              </div>
              <div class="col-4">
                <strong>3. Arrival:</strong> 
                ${d.sampai_tanggal_approve ? 
                  `<span class="badge badge-soft-green">✓ Completed</span><br>
                  Date: ${d.sampai_tanggal_approve||'-'}</small>` 
                  : '<span class="badge badge-soft-yellow">Pending</span>'}
              </div>
            </div>
          </div>
          ` : ''}
        </div>
        <div class="mt-3">
          <h6>Detail Delivery Data</h6>
          <ol class="list-group list-group-numbered">
            <li class="list-group-item">
              <strong>Delivery Planning</strong><br>
              <div class="row g-2">
                <div class="col-md-6">Send Date: ${d.tanggal_kirim||'-'}</div>
                <div class="col-md-6">Estimated Arrival: ${d.estimasi_sampai||'-'}</div>
                <div class="col-md-6">Driver Name: ${d.nama_supir||'-'}</div>
                <div class="col-md-6">Driver Phone: ${d.no_hp_supir||'-'}</div>
                <div class="col-md-6">Driver License: ${d.no_sim_supir||'-'}</div>
                <div class="col-md-6">Vehicle: ${d.kendaraan||'-'}</div>
                <div class="col-md-6">License Plate: ${d.no_polisi_kendaraan||'-'}</div>
              </div>
              Catatan: ${d.catatan_perencanaan||d.catatan||'-'}
            </li>
            <li class="list-group-item">
              <strong>Departure</strong><br>
              Date Approval: ${d.berangkat_tanggal_approve||'-'}<br>
              Notes: ${d.catatan_berangkat||'-'}
            </li>
            <li class="list-group-item">
              <strong>Arrival</strong><br>
              Date Approval: ${d.sampai_tanggal_approve||'-'}<br>
              Notes: ${d.catatan_sampai||'-'}
            </li>
          </ol>
        </div>`;
      
      // Set action buttons
      actionDiv.innerHTML = actionButtons;
      
      // Add event listener for Proses DI button in modal
      setTimeout(() => {
        const prosesDIBtn = document.getElementById('btnProsesDI');
        if (prosesDIBtn) {
          prosesDIBtn.addEventListener('click', () => {
            // Close modal first
            bootstrap.Modal.getInstance(document.getElementById('diDetailModal')).hide();
            // Call global function
            prosesDI(id);
          });
        }
      }, 100);
      modal.show();
    });
  }
  
  window.upd = (id, st) => {
    const fd = new FormData(); fd.append('status', st);
    fetch('<?= base_url('operational/delivery/update-status/') ?>'+id, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd})
      .then(r=>r.json()).then(()=>{ if (diTable) diTable.ajax.reload(); });
  }
</script>
<!-- Approval Stage Modal -->
<div class="modal fade" id="approvalStageModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Approval Confirmation - <span id="approvalStageTitle"></span></h6>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="approvalStageForm">
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
          <!-- Stage-specific content -->
          <div id="stageSpecificContent"></div>

          <div class="form-text mt-2">
            <small>This data will serve as documentation for the delivery process and approval signatures.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-success" type="submit">Approve & Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- DI Detail Modal -->
<div class="modal fade" id="diDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Detail Delivery Instruction</h6>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <div id="diDetailBody"><p class="text-muted">Loading...</p></div>
      </div>
      <div class="modal-footer">
        <div id="modalActionButtons">
          <!-- Buttons will be populated based on status -->
        </div>
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>


