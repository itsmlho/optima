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
    // Map Indonesian status to English and count accordingly
    const submitted = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return !item.status || status === 'DIAJUKAN' || status === 'SUBMITTED';
    }).length;
    const inprogress = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return status === 'DIPROSES' || status === 'PROCESSED' || 
             status === 'DIKIRIM' || status === 'DISPATCHED' || status === 'SHIPPED';
    }).length;
    const delivered = allDIData.filter(item => {
      const status = (item.status || '').toUpperCase();
      return status === 'SAMPAI' || status === 'ARRIVED' || status === 'DELIVERED';
    }).length;
    
    document.getElementById('totalDI').textContent = total;
    document.getElementById('submittedDI').textContent = submitted;
    document.getElementById('inprogressDI').textContent = inprogress;
    document.getElementById('deliveredDI').textContent = delivered;
  }
  
  function applyFilters() {
    const searchTerm = document.getElementById('diSearch').value.toLowerCase();
    
    // Filter by status - map between Indonesian and English status terms with grouping
    let filtered;
    if (currentFilter === 'all') {
      filtered = [...allDIData];
    } else if (currentFilter === 'SUBMITTED') {
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return !item.status || status === 'DIAJUKAN' || status === 'SUBMITTED';
      });
    } else if (currentFilter === 'INPROGRESS') {
      // Group Processed + Shipped as "In Progress"
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return status === 'DIPROSES' || status === 'PROCESSED' ||
               status === 'DIKIRIM' || status === 'DISPATCHED' || status === 'SHIPPED';
      });
    } else if (currentFilter === 'DELIVERED') {
      filtered = allDIData.filter(item => {
        const status = (item.status || '').toUpperCase();
        return status === 'SAMPAI' || status === 'ARRIVED' || status === 'DELIVERED';
      });
    } else {
      // Legacy filter - exact match for backward compatibility
      filtered = allDIData.filter(item => (item.status || '').toUpperCase() === currentFilter);
    }
    
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
      // Convert Indonesian status to English for display and set badge colors
      const getStatusDisplay = (status) => {
        const statusUpper = (status || '').toUpperCase();
        const statusMap = {
          'DIAJUKAN': { text: 'Submitted', color: 'secondary' },
          'SUBMITTED': { text: 'Submitted', color: 'secondary' },
          'DIPROSES': { text: 'Processed', color: 'info' },
          'PROCESSED': { text: 'Processed', color: 'info' },
          'DIKIRIM': { text: 'Shipped', color: 'warning' },
          'DISPATCHED': { text: 'Shipped', color: 'warning' },
          'SHIPPED': { text: 'Shipped', color: 'warning' },
          'SAMPAI': { text: 'Delivered', color: 'success' },
          'ARRIVED': { text: 'Delivered', color: 'success' },
          'DELIVERED': { text: 'Delivered', color: 'success' },
          'DIBATALKAN': { text: 'Canceled', color: 'danger' },
          'CANCELED': { text: 'Canceled', color: 'danger' }
        };
        const mapped = statusMap[statusUpper] || { text: status || 'Submitted', color: 'secondary' };
        return `<span class="badge bg-${mapped.color}">${mapped.text}</span>`;
      };
      
      tr.innerHTML = `
        <td><a href="#" onclick="openDiDetail(${r.id});return false;">${r.nomor_di}</a></td>
        <td>${r.spk_id || '-'}</td>
        <td>${r.po_kontrak_nomor||'-'}</td>
        <td>${r.pelanggan||'-'}</td>
        <td>${r.spk_pic||'-'}</td>
        <td>${r.spk_kontak||'-'}</td>
        <td>${r.lokasi||'-'}</td>
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
        const wrap = document.createElement('div');
        wrap.className = 'unit-item';
        const idSafe = `unit_${it.unit_id||('idx'+idx)}`;
        wrap.innerHTML = `
          <input class="form-check-input unit-check" type="checkbox" id="${idSafe}" name="unit_ids[]" value="${it.unit_id}" checked>
          <label for="${idSafe}" class="form-check-label">
            <div><strong>${it.unit_label||('Unit #' + (idx+1))}</strong></div>
            <div class="unit-note">SN: ${it.serial_number||'-'}${it.attachment_label?` &nbsp; • &nbsp; ${it.attachment_label}`:''}</div>
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
