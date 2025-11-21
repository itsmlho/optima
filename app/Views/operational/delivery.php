<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- CSS umum sudah ada di optima-pro.css -->
<style>
/* Custom delivery page - Smart address column */
.lokasi-cell {
  max-width: 200px;
  position: relative;
  cursor: pointer;
}

.lokasi-preview {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.4;
  font-size: 13px;
  color: #495057;
  margin: 0;
}

.lokasi-tooltip {
  position: absolute;
  top: -10px;
  left: 0;
  right: -20px;
  background: #fff;
  border: 2px solid #007bff;
  border-radius: 8px;
  padding: 12px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.25);
  z-index: 1050;
  max-width: 320px;
  font-size: 14px;
  line-height: 1.4;
  color: #495057;
  display: none;
}

.lokasi-cell:hover .lokasi-tooltip {
  display: block;
}

.lokasi-badge {
  display: inline-block;
  background: #e3f2fd;
  color: #1976d2;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 500;
  margin-top: 3px;
}

/* Compact Action Buttons */
.btn-action {
  padding: 5px 8px;
  margin: 2px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  border: none;
  transition: all 0.2s;
}

.btn-action:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Status Badge Improvements */
.badge {
  font-size: 12px;
  padding: 6px 10px;
  border-radius: 12px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Statistics Cards - Modern Dashboard Style -->
<div class="row g-4 mb-4">
  <div class="col-xl-3 col-md-6">
    <div class="card card-stats bg-primary text-white h-100 filter-card" data-filter="all" style="cursor:pointer;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <h2 class="fw-bold mb-1" id="totalDI">0</h2>
          <h6 class="card-title text-uppercase small mb-0">TOTAL DI</h6>
        </div>
        <div class="ms-3">
          <i class="fas fa-clipboard-list fa-2x opacity-75"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card card-stats bg-warning text-white h-100 filter-card" data-filter="SUBMITTED" style="cursor:pointer;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <h2 class="fw-bold mb-1" id="submittedDI">0</h2>
          <h6 class="card-title text-uppercase small mb-0">PENDING</h6>
          <small class="opacity-75">Submitted</small>
        </div>
        <div class="ms-3">
          <i class="fas fa-clock fa-2x opacity-75"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card card-stats bg-info text-white h-100 filter-card" data-filter="INPROGRESS" style="cursor:pointer;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <h2 class="fw-bold mb-1" id="inprogressDI">0</h2>
          <h6 class="card-title text-uppercase small mb-0">IN PROGRESS</h6>
          <small class="opacity-75">Processed + Shipped</small>
        </div>
        <div class="ms-3">
          <i class="fas fa-shipping-fast fa-2x opacity-75"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card card-stats bg-success text-white h-100 filter-card" data-filter="DELIVERED" style="cursor:pointer;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <h2 class="fw-bold mb-1" id="deliveredDI">0</h2>
          <h6 class="card-title text-uppercase small mb-0">COMPLETED</h6>
          <small class="opacity-75">Delivered</small>
        </div>
        <div class="ms-3">
          <i class="fas fa-check-circle fa-2x opacity-75"></i>
        </div>
      </div>
    </div>
  </div>
</div>

  <div class="card table-card mb-3">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
      <h5 class="h5 mb-0 text-gray-800">Daftar Delivery Instruction (DI)</h5>
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
        <table class="table table-sm mb-0" id="diTable">
          <thead>
            <tr>
              <th>No. DI</th>
              <th>PO/Kontrak</th>
              <th>Pelanggan</th>
              <th>Lokasi</th>
              <th>Total Items</th>
              <th>Jenis Perintah</th>
              <th>Tujuan Perintah</th>
              <th>Req. Tanggal Kirim</th>
              <th>Status Eksekusi</th>
              <th>Supir/Kendaraan</th>
              <th>Aksi Operasional</th>
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
  
  function load(){
    fetch('<?= base_url('operational/delivery/list') ?>').then(r=>r.json()).then(j=>{
      allDIData = j.data || [];
      updateStatistics();
      applyFilters();
    });
  }
  
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
          approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('perencanaan', 'Perencanaan Pengiriman', ${r.id})">Perencanaan</button>`);
        } else if (!berangkatDone) {
          approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('berangkat', 'Berangkat', ${r.id})">Berangkat</button>`);
        } else if (!sampaiDone) {
          approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('sampai', 'Sampai', ${r.id})">Sampai</button>`);
        } else {
          // All approvals done - should be ARRIVED status already
          approvalButtons.push('<span class="text-info">Menunggu update status ke ARRIVED</span>');
        }
        
        // Add small completed badges
        const completedBadges = [];
        if (perencanaanDone) completedBadges.push('<small class="badge bg-success me-1">✓ Perencanaan</small>');
        if (berangkatDone) completedBadges.push('<small class="badge bg-success me-1">✓ Berangkat</small>');
        if (sampaiDone) completedBadges.push('<small class="badge bg-success me-1">✓ Sampai</small>');
        
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
        return `<div class="small"><strong>Supir:</strong> ${driver}<br><strong>Kendaraan:</strong> ${vehicle}</div>`;
      };
      
      // Operational status display
      const getOperationalStatusDisplay = (r) => {
        const status = r.status_di;
        const statusUpper = (status || '').toUpperCase();
        const statusMap = {
          'DIAJUKAN': { text: 'DIAJUKAN', color: 'secondary' },
          'DISETUJUI': { text: 'DISETUJUI', color: 'info' },
          'PERSIAPAN_UNIT': { text: 'PERSIAPAN_UNIT', color: 'warning' },
          'SIAP_KIRIM': { text: 'SIAP_KIRIM', color: 'primary' },
          'DALAM_PERJALANAN': { text: 'DALAM_PERJALANAN', color: 'warning' },
          'SAMPAI_LOKASI': { text: 'SAMPAI_LOKASI', color: 'success' },
          'SELESAI': { text: 'SELESAI', color: 'success' },
          'DIBATALKAN': { text: 'DIBATALKAN', color: 'danger' }
        };
        const mapped = statusMap[statusUpper] || { text: status || 'DIAJUKAN', color: 'secondary' };
        return `<span class="badge bg-${mapped.color}">${mapped.text}</span>`;
      };
      
      tr.innerHTML = `
        <td><a href="#" onclick="openDiDetail(${r.id});return false;">${r.nomor_di}</a></td>
        <td>${r.po_kontrak_nomor||'-'}</td>
        <td>${r.pelanggan||'-'}</td>
        <td class="lokasi-cell">${formatLokasiColumn(r.lokasi)}</td>
        <td class="small">${formatTotalUnits(r)}</td>
        <td>${r.jenis_perintah || '-'}</td>
        <td>${r.tujuan_perintah || '-'}</td>
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
              <strong><i class="fas fa-info-circle"></i> Perencanaan Data Pengiriman</strong><br>
              Lengkapi semua data pengiriman untuk tahap perencanaan.
            </div>
            <div class="row g-3">
              <div class="col-6">
                <label class="form-label">No. PO/Kontrak</label>
                <input type="text" class="form-control-plaintext" readonly value="${di.po_kontrak_nomor || '-'}">
              </div>
              <div class="col-6">
                <label class="form-label">No. SPK</label>
                <input type="text" class="form-control-plaintext" readonly value="${spk.nomor_spk || '-'}">
              </div>
              <div class="col-6">
                <label class="form-label">Pelanggan</label>
                <input type="text" class="form-control-plaintext" readonly value="${di.pelanggan || '-'}">
              </div>
              <div class="col-6">
                <label class="form-label">Lokasi Pengiriman</label>
                <textarea class="form-control-plaintext" readonly rows="3" style="resize: none; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.375rem 0.75rem; background-color: #f8f9fa;">${di.lokasi || '-'}</textarea>
              </div>
              <div class="col-12"><hr></div>
              <div class="col-12"><h6 class="text-primary">Data Operasional Pengiriman</h6></div>
              <div class="col-6 mb-3">
                <label class="form-label">Tanggal Kirim <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal_kirim" required>
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">Estimasi Sampai <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="estimasi_sampai" required>
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">Nama Supir <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_supir" required placeholder="Nama lengkap supir">
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">No HP Supir <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="no_hp_supir" required placeholder="08xxxxxxxxxx">
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">No SIM <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="no_sim_supir" required placeholder="Nomor SIM supir">
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">Kendaraan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="kendaraan" required placeholder="Jenis/merk kendaraan">
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">No Polisi Kendaraan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="no_polisi_kendaraan" required placeholder="Nomor polisi kendaraan">
              </div>
              <div class="col-12">
                <label class="form-label">Catatan Perencanaan</label>
                <textarea class="form-control" name="catatan_perencanaan" rows="3" 
                          placeholder="Masukkan catatan untuk perencanaan pengiriman (opsional)..."></textarea>
                <div class="form-text">Catatan ini akan membantu tim operasional dalam proses berikutnya.</div>
              </div>
            </div>
          `;
        }
      });
      
      
    } else if (stage === 'berangkat') {
      container.innerHTML = `
        <hr>
        <div class="alert alert-warning">
          <strong><i class="fas fa-truck"></i> Konfirmasi Keberangkatan</strong><br>
          Konfirmasi bahwa pengiriman telah dimulai dan tambahkan catatan jika diperlukan.
        </div>
        <div class="alert alert-info">
          <strong>Info:</strong> Data operasional pengiriman sudah ditetapkan pada tahap perencanaan.
        </div>
        <div class="mb-3">
          <label class="form-label">Catatan Keberangkatan</label>
          <textarea class="form-control" name="catatan_berangkat" rows="4" 
                    placeholder="Masukkan catatan keberangkatan, kondisi barang saat berangkat, atau informasi tambahan lainnya..."></textarea>
          <div class="form-text">Catatan ini akan menjadi dokumentasi saat barang diberangkatkan dari lokasi asal.</div>
        </div>
      `;
      
    } else if (stage === 'sampai') {
      container.innerHTML = `
        <hr>
        <div class="alert alert-success">
          <strong><i class="fas fa-map-marker-alt"></i> Konfirmasi Kedatangan</strong><br>
          Konfirmasi bahwa barang telah sampai di lokasi tujuan dengan selamat.
        </div>
        <div class="mb-3">
          <label class="form-label">Catatan Kedatangan <span class="text-danger">*</span></label>
          <textarea class="form-control" name="catatan_sampai" rows="4" required 
                    placeholder="Contoh: Barang telah sampai dengan selamat. Diserahkan kepada Bapak John (Manager Operasional). Kondisi unit baik, tidak ada kerusakan. BAST sudah ditandatangani pukul 14:30 WIB."></textarea>
          <div class="form-text">
            <strong>Pastikan mencantumkan:</strong><br>
            • Nama penerima dan jabatan<br>
            • Kondisi barang saat sampai<br>
            • Waktu penyerahan<br>
            • Status BAST/dokumen
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
        notify(j.message || 'Approval berhasil disimpan', 'success');
      } else {
        console.error('Server error:', j); // Debug log
        notify(j.message || 'Gagal menyimpan approval', 'error');
      }
    }).catch(error=>{
      console.error('Error:', error);
      notify('Terjadi kesalahan pada sistem: ' + error.message, 'error');
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
    body.innerHTML = '<p class="text-muted">Memuat...</p>';
    fetch('<?= base_url('operational/delivery/detail/') ?>'+id).then(r=>r.json()).then(j=>{
      if (!j.success) { body.innerHTML = '<div class="text-danger">Gagal memuat detail</div>'; modal.show(); return; }
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
          itemsHtml += '<h6 class="text-muted mb-3">Attachment yang Dikirim:</h6>';
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
        itemsHtml += '<h6 class="text-muted mb-3">Attachment yang Dikirim:</h6>';
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
        itemsHtml += '<h6 class="text-muted mb-3 mt-4">Attachment Tambahan:</h6>';
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
        itemsHtml = '<div class="alert alert-warning border-warning"><small><i class="bi bi-exclamation-triangle me-2"></i>Belum ada items yang disiapkan untuk pengiriman ini.</small></div>';
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
        actionButtons = '<button class="btn btn-success btn-sm" id="btnProsesDI">Proses DI</button>';
        console.log('✅ Showing Proses DI button for status:', status);
      } else if (status === 'PROCESSED' || status === 'SIAP KIRIM') {
        // Show approval stage buttons for processed statuses
        let approvalButtons = [];
        
        // Check which stages are completed
        const perencanaanDone = d.perencanaan_tanggal_approve ? true : false;
        const berangkatDone = d.berangkat_tanggal_approve ? true : false;
        const sampaiDone = d.sampai_tanggal_approve ? true : false;
        
        // Add buttons for incomplete stages
        if (!perencanaanDone) {
          approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'perencanaan\', \'Perencanaan Pengiriman\')">Perencanaan</button>');
        } else if (!berangkatDone) {
          approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'berangkat\', \'Berangkat\')">Berangkat</button>');
        } else if (!sampaiDone) {
          approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'sampai\', \'Sampai\')">Sampai</button>');
        }
        
        // Show completed stages with checkmarks
        if (perencanaanDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Perencanaan</span>');
        if (berangkatDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Berangkat</span>');
        if (sampaiDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Sampai</span>');
        
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
              <i class="bi bi-clipboard-data me-2"></i>Informasi Dokumen & Pelanggan
            </h6>
            <div class="row g-2">
              <div class="col-6"><strong>No. DI:</strong> ${d.nomor_di}</div>
              <div class="col-6"><strong>Status:</strong> <span class="badge bg-secondary">${status}</span></div>
              <div class="col-6"><strong>No. SPK:</strong> ${spk && spk.nomor_spk ? spk.nomor_spk : '-'}</div>
              <div class="col-6"><strong>Jenis SPK:</strong> <span class="badge ${isAttachmentSpk ? 'bg-warning' : 'bg-info'}">${spkType}</span></div>
              <div class="col-6"><strong>PO/Kontrak:</strong> ${d.po_kontrak_nomor||'-'}</div>
              <div class="col-6"><strong>Tanggal Kirim:</strong> ${d.tanggal_kirim||'-'}</div>
              <div class="col-6"><strong>Nama Perusahaan:</strong> ${d.pelanggan||'-'}</div>
              <div class="col-6"><strong>PIC:</strong> ${spk.pic||'-'}</div>
              <div class="col-6"><strong>Kontak:</strong> ${spk.kontak||'-'}</div>
              <div class="col-12"><strong>Lokasi Pengiriman:</strong><br>
                <div class="bg-light p-2 rounded border mt-1">${d.lokasi||'-'}</div>
              </div>
            </div>
          </div>
          
          <!-- Workflow Information -->
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-info">
              <i class="bi bi-gear me-2"></i>Informasi Workflow
            </h6>
            <div class="row g-2">
              <div class="col-6"><strong>Jenis Perintah:</strong> ${d.jenis_perintah||'-'}</div>
              <div class="col-6"><strong>Tujuan Perintah:</strong> ${d.tujuan_perintah||'-'}</div>
              <div class="col-6"><strong>Status Eksekusi:</strong> ${d.status_eksekusi||'Pending'}</div>
              <div class="col-6"><strong>Dibuat Oleh:</strong> ${d.dibuat_oleh_name || d.dibuat_oleh || '-'}</div>
            </div>
          </div>
          
          <!-- Items Detail -->
          <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 text-success">
              <i class="bi bi-truck me-2"></i>Detail Items yang Dikirim
            </h6>
            ${itemsHtml}
          </div>
          
          ${status === 'PROCESSED' || status === 'DELIVERED' ? `
          <div class="col-12"><hr></div>
          <div class="col-12"><h6 class="mb-2">📋 Status Delivery Workflow</h6></div>
          
          <div class="col-12">
            <div class="row g-2">
              <div class="col-4">
                <strong>1. Perencanaan:</strong> 
                ${d.perencanaan_tanggal_approve ? 
                  `<span class="badge bg-success">✓ Selesai</span><br>
                  Tanggal: ${d.perencanaan_tanggal_approve||'-'}</small>` 
                  : '<span class="badge bg-warning">Menunggu</span>'}
              </div>
              <div class="col-4">
                <strong>2. Berangkat:</strong> 
                ${d.berangkat_tanggal_approve ? 
                  `<span class="badge bg-success">✓ Selesai</span><br>
                  Tanggal: ${d.berangkat_tanggal_approve||'-'}</small>` 
                  : '<span class="badge bg-warning">Menunggu</span>'}
              </div>
              <div class="col-4">
                <strong>3. Sampai:</strong> 
                ${d.sampai_tanggal_approve ? 
                  `<span class="badge bg-success">✓ Selesai</span><br>
                  Tanggal: ${d.sampai_tanggal_approve||'-'}</small>` 
                  : '<span class="badge bg-warning">Menunggu</span>'}
              </div>
            </div>
          </div>
          ` : ''}
        </div>
        <div class="mt-3">
          <h6>Detail Delivery Data</h6>
          <ol class="list-group list-group-numbered">
            <li class="list-group-item">
              <strong>Perencanaan Pengiriman</strong><br>
              <div class="row g-2">
                <div class="col-md-6">Tanggal Kirim: ${d.tanggal_kirim||'-'}</div>
                <div class="col-md-6">Estimasi Sampai: ${d.estimasi_sampai||'-'}</div>
                <div class="col-md-6">Nama Supir: ${d.nama_supir||'-'}</div>
                <div class="col-md-6">No HP Supir: ${d.no_hp_supir||'-'}</div>
                <div class="col-md-6">No SIM: ${d.no_sim_supir||'-'}</div>
                <div class="col-md-6">Kendaraan: ${d.kendaraan||'-'}</div>
                <div class="col-md-6">No Polisi: ${d.no_polisi_kendaraan||'-'}</div>
              </div>
              Catatan: ${d.catatan_perencanaan||d.catatan||'-'}
            </li>
            <li class="list-group-item">
              <strong>Berangkat</strong><br>
              Tanggal Approval: ${d.berangkat_tanggal_approve||'-'}<br>
              Catatan: ${d.catatan_berangkat||'-'}
            </li>
            <li class="list-group-item">
              <strong>Sampai</strong><br>
              Tanggal Approval: ${d.sampai_tanggal_approve||'-'}<br>
              Catatan: ${d.catatan_sampai||'-'}
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
            if (confirm('Apakah Anda yakin ingin memproses DI ini? Status akan berubah menjadi PROCESSED dan DI akan masuk ke tahap workflow operasional.')) {
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
                  notify('DI berhasil diproses. Silakan lanjutkan ke tahap Perencanaan Pengiriman untuk mengisi detail operasional.', 'success');
                  bootstrap.Modal.getInstance(document.getElementById('diDetailModal')).hide();
                  load(); // Reload table
                } else {
                  notify(result.message || 'Gagal memproses DI', 'error');
                }
              }).catch(err => {
                notify('Terjadi kesalahan saat memproses DI', 'error');
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
        <h6 class="modal-title">Konfirmasi Approval - <span id="approvalStageTitle"></span></h6>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="approvalStageForm">
        <div class="modal-body">
          <!-- Stage-specific content -->
          <div id="stageSpecificContent"></div>

          <div class="form-text mt-2">
            <small>Data ini akan menjadi dokumentasi proses delivery dan tanda tangan approval.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-success" type="submit">Approve & Simpan</button>
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
      <div class="modal-body"><div id="diDetailBody"><p class="text-muted">Memuat...</p></div></div>
      <div class="modal-footer">
        <div id="modalActionButtons">
          <!-- Buttons will be populated based on status -->
        </div>
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
