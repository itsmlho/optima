<?= $this->extend('layouts/base') ?>


<!-- CSS umum sudah ada di optima-pro.css -->

<?= $this->section('content') ?>

<!-- Statistics Cards -->
  <div class="row mt-3 mb-4">
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-primary-soft" data-filter="all" style="cursor:pointer;">
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
          <div class="stat-card bg-warning-soft" data-filter="SUBMITTED" style="cursor:pointer;">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-clock stat-icon text-warning"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="submittedDI">0</div>
                      <div class="text-muted">Pending</div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-info-soft" data-filter="INPROGRESS" style="cursor:pointer;">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-arrow-repeat stat-icon text-info"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="inprogressDI">0</div>
                      <div class="text-muted">In Progress</div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-success-soft" data-filter="DELIVERED" style="cursor:pointer;">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-check-circle stat-icon text-success"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="deliveredDI">0</div>
                      <div class="text-muted">Completed</div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="card table-card mb-3">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
      <h5 class="h5 mb-0 text-gray-800">List Delivery Instruction (DI)</h5>
      <div class="d-flex gap-2 align-items-center">
        <!-- No create button for operational - they process existing DIs -->
      </div>
    </div>
    
    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-3" id="filterTabs">
      <li class="nav-item">
        <a class="nav-link active filter-tab" href="#" data-filter="all">All</a>
      </li>
      <li class="nav-item">
        <a class="nav-link filter-tab" href="#" data-filter="SUBMITTED">Submitted</a>
      </li>
      <li class="nav-item">
        <a class="nav-link filter-tab" href="#" data-filter="INPROGRESS">In Progress</a>
      </li>
      <li class="nav-item">
        <a class="nav-link filter-tab" href="#" data-filter="DELIVERED">Delivered</a>
      </li>
      <li class="nav-item">
        <a class="nav-link filter-tab" href="#" data-filter="CANCELLED">Cancelled</a>
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
              <th>DI Number</th>
              <th>PO/Contract</th>
              <th>Customer</th>
              <th>Location</th>
              <th>Total Items</th>
              <th>Command Type</th>
              <th>Command Destination</th>
              <th>Req. Delivery Date</th>
              <th>Execution Status</th>
              <th>Driver/Vehicle</th>
              <th data-no-sort>Operational Actions</th>
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
</div>
<script>
// Global variables for approval workflow
let currentApprovalStage = '';
let currentDiId = null;

// Global variables for filtering and pagination
let allDIData = [];
let filteredDIData = [];
let currentFilter = 'all';
let currentPage = 1;
let entriesPerPage = 10;

document.addEventListener('DOMContentLoaded', ()=>{
  const tb = document.querySelector('#diTable tbody');
  
  function load(startDate = null, endDate = null){
    let url = '<?= base_url('operational/delivery/list') ?>';
    if (startDate && endDate) {
      url += `?start_date=${startDate}&end_date=${endDate}`;
    }
    fetch(url).then(r=>r.json()).then(j=>{
      allDIData = j.data || [];
      updateStatistics();
      applyFilters();
    });
  }
  
  // Load initial data
  load();
  
  function updateStatistics() {
    const total = allDIData.length;
    // Count by status_di
    const submitted = allDIData.filter(item => {
      const status = (item.status_di || '').toUpperCase();
      return !item.status_di || status === 'DIAJUKAN';
    }).length;
    const inprogress = allDIData.filter(item => {
      const status = (item.status_di || '').toUpperCase();
      return status === 'DISETUJUI' || 
             status === 'PERSIAPAN_UNIT' ||
             status === 'SIAP_KIRIM' ||
             status === 'DALAM_PERJALANAN';
    }).length;
    const delivered = allDIData.filter(item => {
      const status = (item.status_di || '').toUpperCase();
      return status === 'SAMPAI_LOKASI' || status === 'SELESAI';
    }).length;
    
    document.getElementById('totalDI').textContent = total;
    document.getElementById('submittedDI').textContent = submitted;
    document.getElementById('inprogressDI').textContent = inprogress;
    document.getElementById('deliveredDI').textContent = delivered;
  }
  
  function applyFilters() {
    const searchTerm = document.getElementById('diSearch').value.toLowerCase();
    
    // Filter by status_di - map between Indonesian and English status terms
    let filtered;
    if (currentFilter === 'all') {
      filtered = [...allDIData];
    } else if (currentFilter === 'SUBMITTED') {
      filtered = allDIData.filter(item => {
        const status = (item.status_di || '').toUpperCase();
        return !item.status_di || status === 'DIAJUKAN';
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
    } else if (currentFilter === 'CANCELLED') {
      filtered = allDIData.filter(item => {
        const status = (item.status_di || '').toUpperCase();
        return status === 'DIBATALKAN';
      });
    } else {
      // Legacy filter - exact match
      filtered = allDIData.filter(item => (item.status_di || '').toUpperCase() === currentFilter);
    }
    
    // Filter by search term
    if (searchTerm) {
      filtered = filtered.filter(item => {
        return (item.nomor_di || '').toLowerCase().includes(searchTerm) ||
               (item.po_kontrak_nomor || '').toLowerCase().includes(searchTerm) ||
               (item.pelanggan || '').toLowerCase().includes(searchTerm) ||
               (item.lokasi || '').toLowerCase().includes(searchTerm) ||
               (item.items_label || '').toLowerCase().includes(searchTerm);
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
      // Convert Indonesian status to English for display and set badge colors
      const getStatusDisplay = (status) => {
        const statusUpper = (status || '').toUpperCase();
        const statusMap = {
          'SUBMITTED': { text: 'Submitted', color: 'secondary' },
          'PROCESSED': { text: 'Processed', color: 'info' },
          'SHIPPED': { text: 'Shipped', color: 'warning' },
          'DELIVERED': { text: 'Delivered', color: 'success' },
          'CANCELLED': { text: 'Cancelled', color: 'danger' }
        };
        const mapped = statusMap[statusUpper] || { text: status || 'Submitted', color: 'secondary' };
        return `<span class="badge bg-${mapped.color}">${mapped.text}</span>`;
      };
      
      // Conditional action button based on status - approval workflow style
      let aksiBtn = '';
      const statusDi = (r.status_di || '').toUpperCase();
      if (!r.status_di || statusDi === 'DIAJUKAN') {
        aksiBtn = '<span class="text-muted">Menunggu diproses</span>';
      } else if (statusDi === 'SIAP_KIRIM' || statusDi === 'DISETUJUI' || statusDi === 'PERSIAPAN_UNIT' || statusDi === 'DALAM_PERJALANAN') {
        // Show approval stage buttons directly in table
        const perencanaanDone = r.perencanaan_tanggal_approve ? true : false;
        const berangkatDone = r.berangkat_tanggal_approve ? true : false;
        const sampaiDone = r.sampai_tanggal_approve ? true : false;
        
        let approvalButtons = [];
        
        // Add active button for current stage
        if (!perencanaanDone) {
          approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('perencanaan', 'Plan Shipping', ${r.id})">Plan</button>`);
        } else if (!berangkatDone) {
          approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('berangkat', 'Depart', ${r.id})">Depart</button>`);
        } else if (!sampaiDone) {
          approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('sampai', 'Arrive', ${r.id})">Arrive</button>`);
        } else {
          // All approvals done - should be ARRIVED status already
          approvalButtons.push('<span class="text-info">Waiting to update status to ARRIVED</span>');
        }
        
        // Add small completed badges
        const completedBadges = [];
        if (perencanaanDone) completedBadges.push('<small class="badge bg-success me-1">✓ Plan</small>');
        if (berangkatDone) completedBadges.push('<small class="badge bg-success me-1">✓ Depart</small>');
        if (sampaiDone) completedBadges.push('<small class="badge bg-success me-1">✓ Arrive</small>');
        
        aksiBtn = approvalButtons.join(' ') + (completedBadges.length > 0 ? '<br>' + completedBadges.join('') : '');
      } else if (statusDi === 'SAMPAI_LOKASI' || statusDi === 'SELESAI') {
        aksiBtn = '<span class="text-success">Completed</span>';
      } else {
        aksiBtn = '<span class="text-muted">-</span>';
      }
      
      // Function to format total units display for operational
      const formatTotalUnits = (r) => {
        const totalUnits = r.total_units || 0;
        const totalAttachments = r.total_attachments || 0;
        const jenisSpk = r.jenis_spk || 'UNIT'; // Use jenis_spk from delivery_instructions
        
        if (jenisSpk === 'ATTACHMENT') {
          // For ATTACHMENT SPK, prioritize attachments count
          if (totalAttachments > 0) {
            const attachmentText = totalAttachments === 1 ? 'attachment' : 'attachments';
            return `<span class="badge bg-warning">${totalAttachments} ${attachmentText}</span>`;
          } else {
            return '<span class="text-muted">No attachments</span>';
          }
        } else {
          // For UNIT SPK, prioritize units count
          if (totalUnits > 0) {
            const unitText = totalUnits === 1 ? 'unit' : 'units';
            return `<span class="badge bg-info">${totalUnits} ${unitText}</span>`;
          } else if (totalAttachments > 0) {
            // Fallback to attachments if no units but has attachments
            const attachmentText = totalAttachments === 1 ? 'attachment' : 'attachments';
            return `<span class="badge bg-warning">${totalAttachments} ${attachmentText}</span>`;
          } else {
            return '<span class="text-muted">-</span>';
          }
        }
      };

      // Function to format location column with smart truncation
      const formatLokasiColumn = (lokasi) => {
        if (!lokasi || lokasi === '-') {
          return '<span class="text-muted">-</span>';
        }
        
        // Limit preview to reasonable length
        const maxPreviewLength = 50;
        const isLong = lokasi.length > maxPreviewLength;
        
        if (isLong) {
          const preview = lokasi.substring(0, maxPreviewLength) + '...';
          return `
            <div class="lokasi-preview">${preview}</div>
            <div class="lokasi-tooltip">
              <strong>Alamat Lengkap:</strong><br>
              ${lokasi}
            </div>
          `;
        } else {
          return `<div class="lokasi-preview">${lokasi}</div>`;
        }
      };
      
      // Format driver/vehicle info
      const formatDriverVehicle = (r) => {
        const driver = r.nama_supir || '-';
        const vehicle = r.kendaraan && r.no_polisi_kendaraan ? 
          `${r.kendaraan} (${r.no_polisi_kendaraan})` : 
          (r.kendaraan || r.no_polisi_kendaraan || '-');
        
        if (driver === '-' && vehicle === '-') return '-';
        return `<div class="small"><strong>Driver:</strong> ${driver}<br><strong>Vehicle:</strong> ${vehicle}</div>`;
      };
      
      // Operational status display
      const getOperationalStatusDisplay = (r) => {
        const status = r.status_di;
        const statusUpper = (status || '').toUpperCase();
        const statusMap = {
          'DIAJUKAN': { text: 'SUBMITTED', color: 'secondary' },
          'DISETUJUI': { text: 'APPROVED', color: 'info' },
          'PERSIAPAN_UNIT': { text: 'UNIT_PREPARATION', color: 'warning' },
          'SIAP_KIRIM': { text: 'READY_TO_SHIP', color: 'primary' },
          'DALAM_PERJALANAN': { text: 'IN_TRANSIT', color: 'warning' },
          'SAMPAI_LOKASI': { text: 'ARRIVED_AT_LOCATION', color: 'success' },
          'SELESAI': { text: 'COMPLETED', color: 'success' },
          'DIBATALKAN': { text: 'CANCELLED', color: 'danger' }
        };
        const mapped = statusMap[statusUpper] || { text: status || 'SUBMITTED', color: 'secondary' };
        return `<span class="badge bg-${mapped.color}">${mapped.text}</span>`;
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
      
      tr.innerHTML = `
        <td><a href="#" onclick="openDiDetail(${r.id});return false;">${r.nomor_di}</a></td>
        <td>${r.po_kontrak_nomor||'-'}</td>
        <td>${r.pelanggan||'-'}</td>
        <td class="lokasi-cell">${formatLokasiColumn(r.lokasi)}</td>
        <td class="small">${formatTotalUnits(r)}</td>
        <td>${r.jenis_perintah || '-'}</td>
        <td>${formatTujuanWithIndicator(r.tujuan_perintah)}</td>
        <td>${r.tanggal_kirim||'-'}</td>
        <td>${getOperationalStatusDisplay(r)}</td>
        <td class="small">${formatDriverVehicle(r)}</td>
        <td>${aksiBtn}</td>`;

      tb.appendChild(tr);
    });
    
    // Update table info
    const totalEntries = filteredDIData.length;
    const start = totalEntries === 0 ? 0 : ((currentPage - 1) * entriesPerPage) + 1;
    const end = Math.min(currentPage * entriesPerPage, totalEntries);
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
  
  load();
  // Approval Stage Modal Functions (similar to SPK workflow)
  
  window.openApprovalModal = (stage, stageTitle, diId) => {
    currentApprovalStage = stage;
    currentDiId = diId;
    
    // Debug log
    console.log('Opening approval modal:', { stage, stageTitle, diId });
    console.log('Current variables:', { currentApprovalStage, currentDiId });
    
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
                <label class="form-label">PO/Contract No.</label>
                <input type="text" class="form-control-plaintext" readonly value="${di.po_kontrak_nomor || '-'}">
              </div>
              <div class="col-6">
                <label class="form-label">SPK No.</label>
                <input type="text" class="form-control-plaintext" readonly value="${spk.nomor_spk || '-'}">
              </div>
              <div class="col-6">
                <label class="form-label">Customer</label>
                <input type="text" class="form-control-plaintext" readonly value="${di.pelanggan || '-'}">
              </div>
              <div class="col-6">
                <label class="form-label">Shipping Location</label>
                <textarea class="form-control-plaintext readonly-textarea" readonly rows="3" style="resize: none;">${di.lokasi || '-'}</textarea>
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
  
  // Add form submission handler for approval stage
  document.getElementById('approvalStageForm').addEventListener('submit', function(e){
    e.preventDefault();
    
    // Debug log
    console.log('Form submitted. Current variables:', { currentApprovalStage, currentDiId });
    
    // Check if currentApprovalStage is defined
    if (!currentApprovalStage) {
      console.error('currentApprovalStage is not defined!');
      notify('Error: Stage approval tidak terdefinisi', 'error');
      return;
    }
    
    const fd = new FormData(this);
    fd.append('stage', currentApprovalStage);
    
    // Debug: Log all form data
    console.log('Form data being sent:');
    for (let [key, value] of fd.entries()) {
      console.log(key, value);
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
      console.log('Server response:', j); // Debug log
      if (j && j.success) {
        bootstrap.Modal.getInstance(document.getElementById('approvalStageModal')).hide();
        // Reload table to update buttons
        load();
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
  
  // Unified notifier (fallbacks)
  window.notify = function(msg, type='success'){
    if (window.OptimaPro && typeof OptimaPro.showNotification==='function') return OptimaPro.showNotification(msg, type);
    if (typeof showNotification==='function') return showNotification(msg, type);
    alert(msg);
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
          console.log('Failed to parse spesifikasi:', e);
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
      console.log('🔍 DI Modal Status Debug:', { 
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
        console.log('✅ Showing Process DI button for status:', status);
      } else if (status === 'PROCESSED' || status === 'SIAP KIRIM') {
        // Show approval stage buttons for processed statuses
        let approvalButtons = [];
        
        // Check which stages are completed
        const perencanaanDone = d.perencanaan_tanggal_approve ? true : false;
        const berangkatDone = d.berangkat_tanggal_approve ? true : false;
        const sampaiDone = d.sampai_tanggal_approve ? true : false;
        
        // Add buttons for incomplete stages
        if (!perencanaanDone) {
          approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'perencanaan\', \'Perencanaan Pengiriman\')">Plan</button>');
        } else if (!berangkatDone) {
          approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'berangkat\', \'Berangkat\')">Depart</button>');
        } else if (!sampaiDone) {
          approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'sampai\', \'Sampai\')">Arrive</button>');
        }
        
        // Show completed stages with checkmarks
        if (perencanaanDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Plan</span>');
        if (berangkatDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Depart</span>');
        if (sampaiDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Arrive</span>');
        
        actionButtons = approvalButtons.join(' ');
        console.log('✅ Showing workflow buttons for status:', status);
      } else if (status === 'DELIVERED' || status === 'SELESAI' || status === 'COMPLETED') {
        // Show completed status
        actionButtons = `<span class="badge bg-success">Completed</span>`;
        console.log('✅ Showing completed status for:', status);
      } else {
        // For any other status, show no action buttons
        actionButtons = '';
        console.log('⚠️ No action buttons for status:', status);
      }
      
      // Add Print SPK button if SPK exists (for all statuses except SUBMITTED and DIAJUKAN)
      if (status !== 'SUBMITTED' && status !== 'DIAJUKAN' && spk && spk.id) {
        const printSPKButton = `<a class="btn btn-outline-success btn-sm" href="<?= base_url('marketing/spk/print/') ?>${spk.id}" target="_blank" rel="noopener"><i class="fas fa-print"></i> Print SPK</a>`;
        actionButtons = actionButtons ? `${actionButtons} ${printSPKButton}` : printSPKButton;
      }
      
      // Add Print DI button right after PDF SPK button - open in new tab like PDF SPK
      const printDIButton = `<a class="btn btn-outline-primary btn-sm" href="<?= base_url('operational/delivery/print/') ?>${id}" target="_blank" rel="noopener"><i class="fas fa-print"></i> Print DI</a>`;
      actionButtons = actionButtons ? `${actionButtons} ${printDIButton}` : printDIButton;
      
      body.innerHTML = `
        <div class="row g-3">
          <!-- Basic Information -->
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-primary">
              <i class="bi bi-clipboard-data me-2"></i>Document Information
            </h6>
            <div class="row g-2">
              <div class="col-6"><strong>DI Number:</strong> ${d.nomor_di}</div>
              <div class="col-6"><strong>Status:</strong> <span class="badge bg-secondary">${status}</span></div>
              <div class="col-6"><strong>SPK Number:</strong> ${spk && spk.nomor_spk ? spk.nomor_spk : '-'}</div>
              <div class="col-6"><strong>SPK Type:</strong> <span class="badge ${isAttachmentSpk ? 'bg-warning' : 'bg-info'}">${spkType}</span></div>
              <div class="col-6"><strong>PO/Contract:</strong> ${d.po_kontrak_nomor||'-'}</div>
              <div class="col-6"><strong>Delivery Date:</strong> ${d.tanggal_kirim||'-'}</div>
              <div class="col-6"><strong>Company Name:</strong> ${d.pelanggan||'-'}</div>
              <div class="col-6"><strong>PIC:</strong> ${spk.pic||'-'}</div>
              <div class="col-6"><strong>Contact:</strong> ${spk.kontak||'-'}</div>
              <div class="col-12"><strong>Delivery Location:</strong><br>
                <div class="bg-light p-2 rounded border mt-1">${d.lokasi||'-'}</div>
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
                  `<span class="badge bg-success">✓ Completed</span><br>
                  Date: ${d.perencanaan_tanggal_approve||'-'}</small>` 
                  : '<span class="badge bg-warning">Pending</span>'}
              </div>
              <div class="col-4">
                <strong>2. Departure:</strong> 
                ${d.berangkat_tanggal_approve ? 
                  `<span class="badge bg-success">✓ Completed</span><br>
                  Date: ${d.berangkat_tanggal_approve||'-'}</small>` 
                  : '<span class="badge bg-warning">Pending</span>'}
              </div>
              <div class="col-4">
                <strong>3. Arrival:</strong> 
                ${d.sampai_tanggal_approve ? 
                  `<span class="badge bg-success">✓ Completed</span><br>
                  Date: ${d.sampai_tanggal_approve||'-'}</small>` 
                  : '<span class="badge bg-warning">Pending</span>'}
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
      
      // Add event listener for Proses DI button
      setTimeout(() => {
        const prosesDIBtn = document.getElementById('btnProsesDI');
        if (prosesDIBtn) {
          prosesDIBtn.addEventListener('click', () => {
            // Simple confirmation dialog
            if (confirm('Are you sure you want to process this DI? The status will change to PROCESSED and the DI will enter the operational workflow stage.')) {
              const formData = new FormData();
              formData.append('action', 'assign_driver');
              // Send minimal data - actual driver info will be filled during Perencanaan stage
              formData.append('nama_supir', '');
              formData.append('no_hp_supir', '-');
              formData.append('no_sim_supir', '-');
              formData.append('kendaraan', '');
              formData.append('no_polisi_kendaraan', '-');
              
              fetch(`<?= base_url('operational/delivery/update-status/') ?>${id}`, {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                body: formData
              }).then(r=>r.json()).then(result=>{
                if (result && result.success) {
                  notify('DI has been processed. Please proceed to the Delivery Planning stage to fill in the operational details.', 'success');
                  bootstrap.Modal.getInstance(document.getElementById('diDetailModal')).hide();
                  load(); // Reload table
                } else {
                  notify(result.message || 'Failed to process DI', 'error');
                }
              }).catch(err => {
                notify('An error occurred while processing the DI', 'error');
                console.error(err);
              });
            }
          });
        }
      }, 100);
      modal.show();
    });
  }
  
  window.upd = (id, st) => {
    const fd = new FormData(); fd.append('status', st);
    fetch('<?= base_url('operational/delivery/update-status/') ?>'+id, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd})
      .then(r=>r.json()).then(()=>load());
  }
  
  load();
});
</script>
<!-- Approval Stage Modal -->
<div class="modal fade" id="approvalStageModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Approval Confirmation - <span id="approvalStageTitle"></span></h6>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="approvalStageForm">
        <div class="modal-body">
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
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Detail Delivery Instruction</h6>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body"><div id="diDetailBody"><p class="text-muted">Loading...</p></div></div>
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


