<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
.card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
.table-card, .card-stats { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
.modal-header { background: linear-gradient(135deg, #e9ecef 0%, #e9ecef 100%); color: white; border-radius: 15px 15px 0 0; }
.filter-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #dee2e6;
}

.filter-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25);
  border-color: #0d6efd;
}

.filter-card.active {
  background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
  color: white;
  border-color: #0d6efd;
  transform: translateY(-3px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  border: 2px solid #fff;
}

.filter-card.active .text-muted {
  color: rgba(255,255,255,0.8) !important;
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
              <th>Item</th>
              <th>Tanggal Kirim</th>
              <th>Status</th>
              <th>Aksi</th>
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
    // Map Indonesian status to English and count accordingly
    const submitted = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return !item.status || status === 'SUBMITTED';
    }).length;
    const inprogress = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return status === 'PROCESSED' || 
             status === 'SHIPPED';
    }).length;
    const delivered = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return status === 'DELIVERED';
    }).length;
    
    document.getElementById('totalDI').textContent = total;
    document.getElementById('submittedDI').textContent = submitted;
    document.getElementById('inprogressDI').textContent = inprogress;
    document.getElementById('deliveredDI').textContent = delivered;
  }
  
  function applyFilters() {
    const searchTerm = document.getElementById('diSearch').value.toLowerCase();
    
    // Filter by status - map between Indonesian and English status terms
    let filtered;
    if (currentFilter === 'all') {
      filtered = [...allDIData];
    } else if (currentFilter === 'SUBMITTED') {
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return !item.status || status === 'SUBMITTED';
      });
    } else if (currentFilter === 'INPROGRESS') {
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return status === 'PROCESSED' || 
               status === 'SHIPPED';
      });
    } else if (currentFilter === 'DELIVERED') {
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return status === 'DELIVERED';
      });
    } else {
      // Legacy filter - exact match
      filtered = allDIData.filter(item => (item.status || '').toUpperCase() === currentFilter);
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
      if (!r.status || r.status === 'SUBMITTED') {
        aksiBtn = '<span class="text-muted">Menunggu diproses</span>';
      } else if (r.status === 'PROCESSED') {
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
      } else if (r.status === 'DELIVERED') {
        aksiBtn = '<span class="text-success">Completed</span>';
      } else {
        aksiBtn = '<span class="text-muted">-</span>';
      }
      
      tr.innerHTML = `
        <td><a href="#" onclick="openDiDetail(${r.id});return false;">${r.nomor_di}</a></td>
        <td>${r.po_kontrak_nomor||'-'}</td>
        <td>${r.pelanggan||'-'}</td>
        <td>${r.lokasi||'-'}</td>
        <td>${r.items_label||'-'}</td>
        <td>${r.tanggal_kirim||'-'}</td>
        <td>${getStatusDisplay(r.status)}</td>
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
                <input type="text" class="form-control-plaintext" readonly value="${di.lokasi || '-'}">
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
      const status = d.status || 'SUBMITTED';
      // Display items in structured format
      let itemsHtml = '';
      if (j.items && j.items.length > 0) {
        j.items.forEach((unitData, index) => {
          const unitNum = index + 1;
          itemsHtml += `<div class="col-12"><hr></div>`;
          itemsHtml += `<div class="col-12"><strong>Unit ${unitNum}:</strong> ${unitData.unit_info.label}</div>`;
          
          // Display battery and charger if electric
          if (unitData.unit_info.jenis_power && unitData.unit_info.jenis_power.toLowerCase().includes('electric')) {
            itemsHtml += `<div class="col-12"><strong>Items:</strong><ul class="mb-2">`;
            
            if (unitData.battery) {
              itemsHtml += `<li><strong>BATTERY</strong> - ${unitData.battery.label}</li>`;
            }
            
            if (unitData.charger) {
              itemsHtml += `<li><strong>CHARGER</strong> - ${unitData.charger.label}</li>`;
            }
            
            itemsHtml += `</ul></div>`;
          }
          
          // Display unit-specific attachments
          if (unitData.attachments && unitData.attachments.length > 0) {
            itemsHtml += `<div class="col-12"><strong>Attachments:</strong><ul class="mb-2">`;
            unitData.attachments.forEach(attachment => {
              itemsHtml += `<li>${attachment.label}</li>`;
            });
            itemsHtml += `</ul></div>`;
          }
        });
      }
      
      // Display general attachments (not unit-specific)
      if (j.attachments && j.attachments.length > 0) {
        itemsHtml += `<div class="col-12"><hr></div>`;
        itemsHtml += `<div class="col-12"><strong>General Attachments:</strong><ul class="mb-0">`;
        j.attachments.forEach(attachment => {
          itemsHtml += `<li>${attachment.label}</li>`;
        });
        itemsHtml += `</ul></div>`;
      }
      
      if (!itemsHtml) {
        itemsHtml = '<div class="text-muted">No items found</div>';
      }
      
      // Update action buttons based on status
      const actionDiv = document.getElementById('modalActionButtons');
      let actionButtons = '';
      
      if (status === 'SUBMITTED') {
        actionButtons = '<button class="btn btn-success btn-sm" id="btnProsesDI">Proses DI</button>';
      } else if (status === 'PROCESSED') {
        // Show approval stage buttons based on completion status
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
      } else if (status === 'DELIVERED') {
        actionButtons = `<span class="badge bg-success">Completed</span>`;
      }
      
      // Add PDF SPK button if SPK exists (for all statuses except SUBMITTED)
      if (status !== 'SUBMITTED' && spk && spk.id) {
        const pdfButton = `<a class="btn btn-outline-info btn-sm" href="<?= base_url('service/spk/print/') ?>${spk.id}" target="_blank" rel="noopener"><i class="fas fa-file-pdf"></i> PDF SPK</a>`;
        actionButtons = actionButtons ? `${actionButtons} ${pdfButton}` : pdfButton;
      }
      
      // Add Print DI button right after PDF SPK button - open in new tab like PDF SPK
      const printDIButton = `<a class="btn btn-outline-primary btn-sm" href="<?= base_url('operational/delivery/print/') ?>${id}" target="_blank" rel="noopener"><i class="fas fa-print"></i> Print DI</a>`;
      actionButtons = actionButtons ? `${actionButtons} ${printDIButton}` : printDIButton;
      
      body.innerHTML = `
        <div class="row g-2">
          <div class="col-6"><strong>No. DI:</strong> ${d.nomor_di}</div>
          <div class="col-6"><strong>Status:</strong> <span class="badge bg-secondary">${status}</span></div>
          <div class="col-6"><strong>PO/Kontrak:</strong> ${d.po_kontrak_nomor||'-'}</div>
          <div class="col-6"><strong>Tanggal Kirim:</strong> ${d.tanggal_kirim||'-'}</div>
          <div class="col-6"><strong>Nama Perusahaan:</strong> ${d.pelanggan||'-'}</div>
          <div class="col-6"><strong>PIC:</strong> ${spk.pic||'-'}</div>
          <div class="col-6"><strong>Kontak:</strong> ${spk.kontak||'-'}</div>
          <div class="col-6"><strong>Lokasi:</strong> ${d.lokasi||'-'}</div>
          <div class="col-12"><hr></div>
          <div class="col-12"><strong>SPK Terkait:</strong> ${spk && spk.nomor_spk ? spk.nomor_spk : '-'}</div>
          <div class="col-12"><strong>Items:</strong><br>${itemsHtml}</div>
          
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
            const formData = new FormData();
            formData.append('status', 'PROCESSED');
            
            fetch(`<?= base_url('operational/delivery/update-status/') ?>${id}`, {
              method: 'POST',
              headers: {'X-Requested-With': 'XMLHttpRequest'},
              body: formData
            }).then(r=>r.json()).then(result=>{
              if (result && result.success) {
                notify('DI berhasil diproses. Status menjadi PROCESSED.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('diDetailModal')).hide();
                load(); // Reload table
              } else {
                notify(result.message || 'Gagal memproses DI', 'error');
              }
            });
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
