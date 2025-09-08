<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
  /* Match marketing/spk.php */
  .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
  .table-card, .card-stats { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
  .modal-header { background: linear-gradient(135deg, #e9ecef 0%, #e9ecef 100%); color: white; border-radius: 15px 15px 0 0; }
  .filter-card.active { 
    transform: translateY(-3px); 
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); 
    border: 2px solid #fff; 
  }
  .filter-card:hover { 
    transform: translateY(-5px); 
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25); 
  }
  /* Unit selection list */
  .unit-list { max-height: 260px; overflow: auto; border: 1px solid #e5e7eb; border-radius: .5rem; padding:.5rem; }
  .unit-item { display:flex; align-items:flex-start; gap:.5rem; padding:.25rem .25rem; border-bottom:1px dashed #e5e7eb; }
  .unit-item:last-child{ border-bottom: none; }
  .unit-note { font-size:.8rem; color:#6b7280; }
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
    <div class="card card-stats bg-warning text-white h-100 filter-card" data-filter="DIRENCANAKAN" style="cursor:pointer;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <h2 class="fw-bold mb-1" id="submittedDI">0</h2>
          <h6 class="card-title text-uppercase small mb-0">DIRENCANAKAN</h6>
          <small class="opacity-75">Belum Dikerjakan</small>
        </div>
        <div class="ms-3">
          <i class="fas fa-clock fa-2x opacity-75"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card card-stats bg-info text-white h-100 filter-card" data-filter="DALAM_PERJALANAN" style="cursor:pointer;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <h2 class="fw-bold mb-1" id="inprogressDI">0</h2>
          <h6 class="card-title text-uppercase small mb-0">DALAM PERJALANAN</h6>
          <small class="opacity-75">Tim di Lapangan</small>
        </div>
        <div class="ms-3">
          <i class="fas fa-shipping-fast fa-2x opacity-75"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card card-stats bg-success text-white h-100 filter-card" data-filter="SELESAI" style="cursor:pointer;">
      <div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <h2 class="fw-bold mb-1" id="deliveredDI">0</h2>
          <h6 class="card-title text-uppercase small mb-0">SELESAI</h6>
          <small class="opacity-75">Tugas Completed</small>
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
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#diCreateModal">
          <i class="fas fa-plus"></i> Buat DI
        </button>
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
              <th>No. SPK</th>
              <th>PO/Kontrak</th>
              <th>Pelanggan</th>
              <th>Lokasi</th>
              <th>Total Unit</th>
              <th>Jenis Perintah</th>
              <th>Tujuan Perintah</th>
              <th>Req. Tanggal Kirim</th>
              <th>Status</th>
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

  <!-- Create DI Modal (from READY SPK) -->
  <div class="modal fade" id="diCreateModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h6 class="modal-title">Buat DI dari SPK READY</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="diCreateForm">
          <div class="modal-body">
            <div class="mb-2">
              <label class="form-label">Pilih SPK (READY)</label>
              <input type="text" class="form-control" id="modalSpkSearch" placeholder="Cari No. SPK / PO / Pelanggan">
              <select class="form-select mt-2" id="spkPick" name="spk_id" required></select>
              <div class="form-text">Hanya SPK dengan status READY yang bisa dibuat DI.</div>
            </div>
            
            <!-- FIELD WORKFLOW: Jenis Perintah, Tujuan Perintah -->
            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label class="form-label">Jenis Perintah Kerja <span class="text-danger">*</span></label>
                <select class="form-select" name="jenis_perintah_kerja_id" id="jenisPerintahSelect" required>
                  <option value="">-- Pilih Jenis Perintah --</option>
                  <!-- Options will be loaded dynamically -->
                </select>
                <div class="form-text">Tentukan aksi utama yang akan dilakukan</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tujuan Perintah <span class="text-danger">*</span></label>
                <select class="form-select" name="tujuan_perintah_kerja_id" id="tujuanPerintahSelect" required disabled>
                  <option value="">-- Pilih Jenis Perintah dulu --</option>
                </select>
                <div class="form-text">Alasan/konteks dari perintah kerja ini</div>
              </div>
            </div>
            
            <div id="diUnitsSection" style="display:none;" class="mb-3">
              <label class="form-label">Pilih Unit yang akan dikirim</label>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="small text-muted">Terpilih: <span id="selCount">0</span> <span class="unit-label">unit</span></div>
                <div>
                  <button class="btn btn-sm btn-outline-secondary" type="button" id="btnSelectAll">Pilih Semua</button>
                  <button class="btn btn-sm btn-outline-secondary" type="button" id="btnClearAll">Bersihkan</button>
                </div>
              </div>
              <div id="diUnitList" class="unit-list"><div class="text-muted small">Memuat unit dari SPK...</div></div>
              <div class="form-text">Jika SPK memiliki 3 unit, Anda dapat memilih berapa unit yang akan dikirim pada DI ini.</div>
            </div>
            <div class="row g-2">
              <div class="col-6"><label class="form-label">Tanggal Kirim</label><input type="date" class="form-control" name="tanggal_kirim"></div>
              <div class="col-6"><label class="form-label">Catatan</label><input type="text" class="form-control" name="catatan" placeholder="Opsional"></div>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Buat DI</button></div>
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
      jenisSelect.innerHTML = '<option value="">-- Pilih Jenis Perintah Kerja --</option>';
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
      editJenisSelect.innerHTML = '<option value="">-- Pilih Jenis Perintah Kerja --</option>';
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
          tujuanSelect.innerHTML = '<option value="">-- Pilih Tujuan --</option>';
          tujuanSelect.disabled = false;
          
          result.data.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.id;
            optionElement.textContent = `${option.kode} - ${option.nama}`;
            optionElement.title = option.deskripsi;
            tujuanSelect.appendChild(optionElement);
          });
        }
      } else {
        console.error('Failed to load tujuan perintah options:', result.message);
      }
    } catch (error) {
      console.error('Error loading tujuan perintah options:', error);
    }
  }
  
  // Setup dynamic dropdown untuk form create
  function setupWorkflowDropdowns() {
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const tujuanSelect = document.getElementById('tujuanPerintahSelect');
    
    if (!jenisSelect || !tujuanSelect) return;
    
    jenisSelect.addEventListener('change', function() {
      const jenisValue = this.value;
      
      // Reset tujuan dropdown
      tujuanSelect.innerHTML = '<option value="">-- Pilih Tujuan --</option>';
      tujuanSelect.disabled = true;
      
      if (jenisValue) {
        // Load tujuan options from API
        loadTujuanPerintahOptions(jenisValue, 'tujuanPerintahSelect');
      }
      
      // Trigger validation
      validateWorkflowForm();
    });
    
    tujuanSelect.addEventListener('change', validateWorkflowForm);
  }
  
  // Setup dropdown untuk edit form
  function setupEditWorkflowDropdowns() {
    const editJenisSelect = document.getElementById('editJenisPerintah');
    const editTujuanSelect = document.getElementById('editTujuanPerintah');
    
    if (!editJenisSelect || !editTujuanSelect) return;
    
    editJenisSelect.addEventListener('change', function() {
      const jenisValue = this.value;
      
      // Reset tujuan dropdown
      editTujuanSelect.innerHTML = '<option value="">-- Pilih Tujuan --</option>';
      editTujuanSelect.disabled = true;
      
      if (jenisValue) {
        // Load tujuan options from API
        loadTujuanPerintahOptions(jenisValue, 'editTujuanPerintah');
      }
    });
  }
  
  // Validasi form workflow
  function validateWorkflowForm() {
    const jenisSelect = document.getElementById('jenisPerintahSelect');
    const tujuanSelect = document.getElementById('tujuanPerintahSelect');
    const submitBtn = document.querySelector('#diCreateForm [type="submit"]');
    
    if (!jenisSelect || !tujuanSelect || !submitBtn) return;
    
    const jenisValid = jenisSelect.value !== '';
    const tujuanValid = tujuanSelect.value !== '';
    const isValid = jenisValid && tujuanValid;
    
    // Visual feedback
    jenisSelect.classList.toggle('is-invalid', !jenisValid && jenisSelect.value !== '');
    jenisSelect.classList.toggle('is-valid', jenisValid);
    
    tujuanSelect.classList.toggle('is-invalid', !tujuanValid && tujuanSelect.value !== '');
    tujuanSelect.classList.toggle('is-valid', tujuanValid);
    
    // Enable/disable submit button
    submitBtn.disabled = !isValid;
  }
  
  // Initialize workflow dropdowns and load data
  setupWorkflowDropdowns();
  setupEditWorkflowDropdowns();
  
  // Load dropdown data when modal is shown
  document.getElementById('diCreateModal').addEventListener('shown.bs.modal', function() {
    console.log('DI Create Modal shown, loading dropdown data...');
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
        tujuanSelect.innerHTML = '<option value="">-- Pilih Jenis Perintah dulu --</option>';
        tujuanSelect.disabled = true;
      }
      
      // Reset submit button
      const submitBtn = form.querySelector('[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;
    }
  });
  
  // =====================================================
  // END WORKFLOW BARU
  // =====================================================
  
  function loadDI(){
    fetch('<?= base_url('marketing/di/list') ?>').then(r=>r.json()).then(j=>{
      allDIData = j.data || [];
      updateStatistics();
      applyFilters();
    });
  }
  
  function updateStatistics() {
    const total = allDIData.length;
    
    // Update statistik menggunakan status utama (setelah optimasi)
    const direncanakan = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return status === 'DIAJUKAN' || status === 'SUBMITTED';
    }).length;
    
    const persiapanUnit = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return status === 'DISETUJUI' || status === 'PERSIAPAN_UNIT' || status === 'SIAP_KIRIM';
    }).length;
    
    const dalamPerjalanan = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return status === 'DALAM_PERJALANAN' || status === 'SHIPPED';
    }).length;
    
    const selesai = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return status === 'SELESAI' || status === 'SAMPAI_LOKASI' || status === 'DELIVERED';
    }).length;
    
    document.getElementById('totalDI').textContent = total;
    document.getElementById('submittedDI').textContent = direncanakan;
    document.getElementById('inprogressDI').textContent = dalamPerjalanan;
    document.getElementById('deliveredDI').textContent = selesai;
  }
  
  function applyFilters() {
    const searchTerm = document.getElementById('diSearch').value.toLowerCase();
    
    // Filter berdasarkan status utama (setelah optimasi)
    let filtered;
    if (currentFilter === 'all') {
      filtered = [...allDIData];
    } else if (currentFilter === 'DIRENCANAKAN') {
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return status === 'DIAJUKAN' || status === 'SUBMITTED';
      });
    } else if (currentFilter === 'DALAM_PERJALANAN') {
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return status === 'DALAM_PERJALANAN' || status === 'SHIPPED';
      });
    } else if (currentFilter === 'SELESAI') {
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return status === 'SELESAI' || status === 'SAMPAI_LOKASI' || status === 'DELIVERED';
      });
    } else if (currentFilter === 'DIBATALKAN') {
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return status === 'DIBATALKAN' || status === 'CANCELLED';
      });
    } else {
      // Exact match untuk filter status spesifik
      filtered = allDIData.filter(item => (item.status || '').toUpperCase() === currentFilter);
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
      // Display Indonesian status values with appropriate badge colors
      const getStatusDisplay = (status) => {
        const statusUpper = (status || '').toUpperCase();
        const statusMap = {
          'DIAJUKAN': { text: 'Diajukan', color: 'secondary' },
          'DIPROSES': { text: 'Diproses', color: 'info' },
          'DIKIRIM': { text: 'Dikirim', color: 'warning' },
          'SAMPAI': { text: 'Sampai', color: 'success' },
          'DIBATALKAN': { text: 'Dibatalkan', color: 'danger' }
        };
        const mapped = statusMap[statusUpper] || { text: status || 'Diajukan', color: 'secondary' };
        return `<span class="badge bg-${mapped.color}">${mapped.text}</span>`;
      };
      
      // Function to format total units display
      const formatTotalUnits = (r) => {
        // Prioritas: tampilkan unit jika ada, jika tidak ada unit maka tampilkan attachment
        if (r.total_units && r.total_units > 0) {
          return `<span class="badge bg-primary">${r.total_units} Unit</span>`;
        } else if (r.total_attachments && r.total_attachments > 0) {
          return `<span class="badge bg-warning">${r.total_attachments} Attachment</span>`;
        } else {
          return '<span class="badge bg-secondary">0</span>';
        }
      };
      
      tr.innerHTML = `
        <td><a href="#" onclick="openDiDetail(${r.id});return false;">${r.nomor_di}</a></td>
        <td>${r.spk_id || '-'}</td>
        <td>${r.po_kontrak_nomor||'-'}</td>
        <td>${r.pelanggan||'-'}</td>
        <td>${r.lokasi||'-'}</td>
        <td><span class="text-muted small">${formatTotalUnits(r)}</span></td>
        <td>${r.jenis_perintah || '-'}</td>
        <td>${r.tujuan_perintah || '-'}</td>
        <td>${r.tanggal_kirim||'-'}</td>
        <td>${getStatusDisplay(r.status)}</td>`;
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
  
  loadDI();

  window.openDiDetail = (id) => {
    currentDiId = id; // Store current DI ID
    const modal = new bootstrap.Modal(document.getElementById('diDetailModal'));
    const body = document.getElementById('diDetailBody');
    body.innerHTML = '<p class="text-muted">Memuat...</p>';
    fetch('<?= base_url('marketing/di/detail/') ?>'+id).then(r=>r.json()).then(j=>{
      if (!j.success) { body.innerHTML = '<div class="text-danger">Gagal memuat detail</div>'; modal.show(); return; }
      const d = j.data||{}; const spk = j.spk||{}; const items = j.items||[];
      const itemsHtml = items.length ? '<ul>'+items.map(i=>`<li>${i.item_type}: ${i.label}</li>`).join('')+'</ul>' : '<div class="text-muted">-</div>';
      body.innerHTML = `
        <div class="row g-2">
          <div class="col-6"><strong>No. DI:</strong> ${d.nomor_di}</div>
          <div class="col-6"><strong>Status:</strong> ${d.status}</div>
          <div class="col-6"><strong>PO/Kontrak:</strong> ${d.po_kontrak_nomor||'-'}</div>
          <div class="col-6"><strong>Tanggal Kirim:</strong> ${d.tanggal_kirim||'-'}</div>
          <div class="col-6"><strong>Nama Perusahaan:</strong> ${d.pelanggan||'-'}</div>
          <div class="col-6"><strong>PIC:</strong> ${d.pic||spk.pic||'-'}</div>
          <div class="col-6"><strong>Kontak:</strong> ${d.kontak||spk.kontak||'-'}</div>
          <div class="col-6"><strong>Lokasi:</strong> ${d.lokasi||'-'}</div>
          <div class="col-6"><strong>Jenis Perintah:</strong> ${d.jenis_perintah||'-'}</div>
          <div class="col-6"><strong>Tujuan Perintah:</strong> ${d.tujuan_perintah||'-'}</div>
          <div class="col-12"><hr></div>
          <div class="col-12"><strong>SPK Terkait:</strong> ${spk && spk.nomor_spk ? spk.nomor_spk : '-'}</div>
          <div class="col-12"><strong>Items:</strong><br>${itemsHtml}</div>
          <div class="col-12"><strong>Catatan:</strong><br>${d.catatan||'-'}</div>
        </div>`;
      modal.show();
    });
  }

  const spkPick = document.getElementById('spkPick');
  function loadReadySpk(q){
    const url = new URL('<?= base_url('marketing/spk/ready-options') ?>', window.location.origin);
    if (q) url.searchParams.set('q', q);
    fetch(url).then(r=>r.json()).then(j=>{
      spkPick.innerHTML = '<option value="">- Pilih SPK -</option>' + (j.data||[]).map(x=>`<option value="${x.id}">${x.label}</option>`).join('');
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
    list.innerHTML = '<div class="text-muted small">Memuat unit dari SPK...</div>';
    fetch(`<?= base_url('marketing/spk/detail/') ?>${id}`).then(r=>r.json()).then(j=>{
      const details = (j && j.spesifikasi && Array.isArray(j.spesifikasi.prepared_units_detail)) ? j.spesifikasi.prepared_units_detail : [];
      if (!details.length){ list.innerHTML = '<div class="text-danger small">Belum ada unit yang disiapkan pada SPK ini.</div>'; selCount.textContent = '0'; return; }
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
        
        wrap.innerHTML = `
          <input class="form-check-input unit-check" type="checkbox" id="${idSafe}" name="unit_ids[]" value="${unitId}" checked>
          <label for="${idSafe}" class="form-check-label">
            <div><strong>${it.unit_label || ('Unit #' + (idx+1))}</strong></div>
            <div class="unit-note">SN: ${it.serial_number || '-'}${attachmentText}</div>
          </label>`;
        list.appendChild(wrap);
      });
      const updateSel = ()=>{ const n = list.querySelectorAll('.unit-check:checked').length; selCount.textContent = String(n); };
      list.querySelectorAll('.unit-check').forEach(cb=> cb.addEventListener('change', updateSel));
      updateSel();
      document.getElementById('btnSelectAll').onclick = ()=>{ list.querySelectorAll('.unit-check').forEach(cb=> cb.checked=true); selCount.textContent = String(list.querySelectorAll('.unit-check:checked').length); };
      document.getElementById('btnClearAll').onclick = ()=>{ list.querySelectorAll('.unit-check').forEach(cb=> cb.checked=false); selCount.textContent = '0'; };
    });
  });

  document.getElementById('diCreateForm').addEventListener('submit', (e)=>{
    e.preventDefault();
    const fd = new FormData(e.target);
    // Ensure at least one unit selected if section is visible
    const list = document.getElementById('diUnitList');
    if (document.getElementById('diUnitsSection').style.display !== 'none'){
      const selected = list ? Array.from(list.querySelectorAll('.unit-check:checked')) : [];
      if (!selected.length){
        alert('Pilih minimal satu unit untuk DI ini.');
        return;
      }
    }
    
    // Debug: Log form data before sending
    console.log('DI Create Form Data:');
    for (let [key, value] of fd.entries()) {
      console.log(`  ${key}: ${value}`);
    }
    
    fetch('<?= base_url('marketing/di/create') ?>',{method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd})
      .then(r=>r.json()).then(j=>{
        console.log('DI Create Response:', j);  // Debug log
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
          const msg = (j.message || 'Gagal membuat DI') + debugInfo;
          
          console.error('DI Create Error:', j);  // Debug log
          
          // Show more detailed error in alert for debugging
          alert(msg);
          
          // Also show in UI notification if available
          if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification(j.message || 'Gagal membuat DI', 'error');
          else if (typeof showNotification==='function') showNotification(j.message || 'Gagal membuat DI', 'error');
        }
      }).catch(error => {
        console.error('DI Create Fetch Error:', error);  // Debug log
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
        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('DI berhasil diperbarui', 'success');
        else alert('DI berhasil diperbarui');
      } else {
        alert(j.message || 'Gagal memperbarui DI');
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
          alert('Gagal memuat data DI untuk diedit');
        }
      }).catch(error => {
        console.error('Edit DI Load Error:', error);
        alert('Error loading DI data: ' + error.message);
      });
  };
  
  window.deleteDI = function(diId) {
    if (!confirm('Apakah Anda yakin ingin menghapus DI ini?')) return;
    
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
        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('DI berhasil dihapus', 'success');
        else alert('DI berhasil dihapus');
      } else {
        alert(j.message || 'Gagal menghapus DI');
      }
    }).catch(error => {
      console.error('Delete DI Error:', error);
      alert('Network error: ' + error.message);
    });
  };
  
  // Edit DI from detail modal
  window.editDiFromDetail = function() {
    if (!currentDiId) {
      alert('DI ID tidak ditemukan');
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
      alert('DI ID tidak ditemukan');
      return;
    }
    
    // First confirmation
    if (!confirm('Apakah Anda yakin ingin menghapus DI ini?')) {
      return;
    }
    
    // Second confirmation
    if (!confirm('PERINGATAN: Tindakan ini tidak dapat dibatalkan!\n\nApakah Anda benar-benar yakin ingin menghapus DI ini?')) {
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
      alert('DI ID tidak ditemukan');
      return;
    }
    
    // Open print DI in new tab (not popup window)
    const printUrl = `<?= base_url('operational/delivery/print/') ?>${currentDiId}`;
    window.open(printUrl, '_blank');
  };
});
</script>
<!-- DI Detail Modal -->
<div class="modal fade" id="diDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Detail Delivery Instruction</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><div id="diDetailBody"><p class="text-muted">Memuat...</p></div></div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="btnPrintDi" onclick="printDiFromDetail()">
          <i class="fas fa-print"></i> Print PDF
        </button>
        <button class="btn btn-warning" id="btnEditDi" onclick="editDiFromDetail()">
          <i class="fas fa-edit"></i> Edit
        </button>
        <button class="btn btn-danger" id="btnDeleteDi" onclick="deleteDiFromDetail()">
          <i class="fas fa-trash"></i> Delete
        </button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit DI Modal -->
<div class="modal fade" id="diEditModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Edit Delivery Instruction</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form id="diEditForm">
        <input type="hidden" id="editDiId" name="id">
        <div class="modal-body">
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label">Jenis Perintah <span class="text-danger">*</span></label>
              <select class="form-select" id="editJenisPerintah" name="jenis_perintah" required>
                <option value="">- Pilih Jenis Perintah -</option>
                <!-- Options will be loaded from API -->
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tujuan Perintah <span class="text-danger">*</span></label>
              <select class="form-select" id="editTujuanPerintah" name="tujuan_perintah" required disabled>
                <option value="">- Pilih Jenis Perintah dulu -</option>
                <!-- Options will be loaded from API based on jenis -->
              </select>
            </div>
          </div>
          
          <!-- PERBAIKAN: Status Eksekusi sebagai display only, bukan input -->
          <div class="mb-3">
            <label class="form-label">Status Eksekusi</label>
            <div class="card bg-light">
              <div class="card-body py-2">
                <span id="editStatusEksekusiDisplay" class="badge bg-primary">READY</span>
                <small class="text-muted ms-2">Status diatur oleh sistem berdasarkan workflow</small>
              </div>
            </div>
          </div>
          
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Tanggal Kirim</label>
              <input type="date" class="form-control" id="editTanggalKirim" name="tanggal_kirim">
            </div>
            <div class="col-6">
              <label class="form-label">Catatan</label>
              <input type="text" class="form-control" id="editCatatan" name="catatan" placeholder="Opsional">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary" type="submit">Update DI</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
