<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<style>
.filter-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #dee2e6;
}

.filter-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  border-color: #0d6efd;
}

.filter-card.active {
  background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
  color: white;
  border-color: #0d6efd;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(13,110,253,0.3);
}

.filter-card.active .text-muted {
  color: rgba(255,255,255,0.8) !important;
}
</style>

<div class="container-fluid py-3">
  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="all">
        <div class="card-body py-3">
          <h5 class="mb-1 text-primary" id="totalDI">0</h5>
          <small class="text-muted">Total DI</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="SUBMITTED">
        <div class="card-body py-3">
          <h5 class="mb-1 text-secondary" id="submittedDI">0</h5>
          <small class="text-muted">Submitted</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="DISPATCHED">
        <div class="card-body py-3">
          <h5 class="mb-1 text-info" id="dispatchedDI">0</h5>
          <small class="text-muted">Dispatched</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="ARRIVED">
        <div class="card-body py-3">
          <h5 class="mb-1 text-success" id="arrivedDI">0</h5>
          <small class="text-muted">Arrived</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="CANCELLED">
        <div class="card-body py-3">
          <h5 class="mb-1 text-danger" id="cancelledDI">0</h5>
          <small class="text-muted">Cancelled</small>
        </div>
      </div>
    </div>
    <div class="col-md-2 d-flex align-items-center">
      <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#diCreateModal">
        <i class="fas fa-plus"></i> Buat DI
      </button>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <!-- DataTable-style controls -->
      <div class="row mb-3">
        <div class="col-md-6 d-flex align-items-center">
          <label class="me-2">Show</label>
          <select class="form-select form-select-sm me-2" id="entriesPerPage" style="width: auto;">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>entries</span>
        </div>
        <div class="col-md-6">
          <div class="input-group input-group-sm">
            <span class="input-group-text">Search:</span>
            <input type="text" class="form-control" id="searchInput" placeholder="Cari No. DI, SPK, PO, Pelanggan...">
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-sm mb-0" id="diTable">
          <thead>
            <tr>
              <th>No. DI</th>
              <th>No. SPK</th>
              <th>PO/Kontrak</th>
              <th>Nama Perusahaan</th>
              <th>PIC</th>
              <th>Kontak</th>
              <th>Lokasi</th>
              <th>Tanggal Kirim</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="row mt-3">
        <div class="col-md-6">
          <div id="tableInfo" class="text-muted"></div>
        </div>
        <div class="col-md-6">
          <nav>
            <ul class="pagination pagination-sm justify-content-end mb-0" id="pagination"></ul>
          </nav>
        </div>
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
              <input type="text" class="form-control" id="spkSearch" placeholder="Cari No. SPK / PO / Pelanggan">
              <select class="form-select mt-2" id="spkPick" name="spk_id" required></select>
              <div class="form-text">Hanya SPK dengan status READY yang bisa dibuat DI.</div>
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

document.addEventListener('DOMContentLoaded', ()=>{
  const tb = document.querySelector('#diTable tbody');
  
  function loadDI(){
    fetch('<?= base_url('marketing/di/list') ?>').then(r=>r.json()).then(j=>{
      allDIData = j.data || [];
      updateStatistics();
      applyFilters();
    });
  }
  
  function updateStatistics() {
    const total = allDIData.length;
    const submitted = allDIData.filter(item => (item.status || '').toUpperCase() === 'SUBMITTED').length;
    const dispatched = allDIData.filter(item => (item.status || '').toUpperCase() === 'DISPATCHED').length;
    const arrived = allDIData.filter(item => (item.status || '').toUpperCase() === 'ARRIVED').length;
    const cancelled = allDIData.filter(item => (item.status || '').toUpperCase() === 'CANCELLED').length;
    
    document.getElementById('totalDI').textContent = total;
    document.getElementById('submittedDI').textContent = submitted;
    document.getElementById('dispatchedDI').textContent = dispatched;
    document.getElementById('arrivedDI').textContent = arrived;
    document.getElementById('cancelledDI').textContent = cancelled;
  }
  
  function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    // Filter by status
    let filtered = currentFilter === 'all' ? [...allDIData] : 
                   allDIData.filter(item => (item.status || '').toUpperCase() === currentFilter);
    
    // Filter by search term
    if (searchTerm) {
      filtered = filtered.filter(item => {
        return (item.nomor_di || '').toLowerCase().includes(searchTerm) ||
               (item.spk_id || '').toLowerCase().includes(searchTerm) ||
               (item.po_kontrak_nomor || '').toLowerCase().includes(searchTerm) ||
               (item.pelanggan || '').toLowerCase().includes(searchTerm) ||
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
      const badge = (s)=>{ const m={SUBMITTED:'secondary',DISPATCHED:'info',ARRIVED:'success',CANCELLED:'danger'}; const c=m[(s||'').toUpperCase()]||'secondary'; return `<span class="badge bg-${c}">${s}</span>`; };
      tr.innerHTML = `
        <td><a href="#" onclick="openDiDetail(${r.id});return false;">${r.nomor_di}</a></td>
        <td>${r.spk_id || '-'}</td>
        <td>${r.po_kontrak_nomor||'-'}</td>
        <td>${r.pelanggan||'-'}</td>
        <td>${r.spk_pic||'-'}</td>
        <td>${r.spk_kontak||'-'}</td>
        <td>${r.lokasi||'-'}</td>
        <td>${r.tanggal_kirim||'-'}</td>
        <td>${badge(r.status)}</td>`;
      tb.appendChild(tr);
    });
    
    // Update table info
    const totalEntries = filteredDIData.length;
    const start = totalEntries === 0 ? 0 : startIndex + 1;
    const end = Math.min(endIndex, totalEntries);
    document.getElementById('tableInfo').textContent = 
      `Showing ${start} to ${end} of ${totalEntries} entries`;
  }
  
  function updatePagination() {
    const totalPages = Math.ceil(filteredDIData.length / entriesPerPage);
    const pagination = document.getElementById('pagination');
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
  
  document.getElementById('searchInput').addEventListener('input', function() {
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
          <div class="col-6"><strong>PIC:</strong> ${spk.pic||'-'}</div>
          <div class="col-6"><strong>Kontak:</strong> ${spk.kontak||'-'}</div>
          <div class="col-6"><strong>Lokasi:</strong> ${d.lokasi||'-'}</div>
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
  document.getElementById('spkSearch').addEventListener('input', e=> loadReadySpk(e.target.value.trim()));

  document.getElementById('diCreateForm').addEventListener('submit', (e)=>{
    e.preventDefault();
    const fd = new FormData(e.target);
    fetch('<?= base_url('marketing/di/create') ?>',{method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd})
      .then(r=>r.json()).then(j=>{
        if (j && j.success){
          bootstrap.Modal.getInstance(document.getElementById('diCreateModal')).hide();
          e.target.reset();
          loadDI();
          if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('DI dibuat: ' + (j.nomor||''), 'success');
          else if (typeof showNotification==='function') showNotification('DI dibuat: ' + (j.nomor||''), 'success');
          else alert('DI dibuat: ' + (j.nomor||''));
        } else {
          const msg = j.message || 'Gagal membuat DI';
          if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification(msg, 'error');
          else if (typeof showNotification==='function') showNotification(msg, 'error');
          else alert(msg);
        }
      });
  });
});
</script>
<!-- DI Detail Modal -->
<div class="modal fade" id="diDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Detail Delivery Instruction</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><div id="diDetailBody"><p class="text-muted">Memuat...</p></div></div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
