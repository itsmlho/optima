<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
<style>
.filter-card { 
    cursor: pointer; 
    transition: all 0.3s ease; 
}
.filter-card.active { 
    transform: translateY(-3px); 
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); 
    border: 2px solid #fff; 
}
.filter-card:hover { 
    transform: translateY(-5px); 
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25); 
}
</style>

  <!-- Statistics Cards -->
  <div class="row g-4 mb-4">
    <div class="col-xl-2 col-md-4"><div class="card card-stats bg-primary text-white h-100 filter-card" data-filter="all" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="totalDI">0</h2><h6 class="card-title text-uppercase small">Total DI</h6></div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card card-stats bg-secondary text-white h-100 filter-card" data-filter="DIAJUKAN" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="diajukanDI">0</h2><h6 class="card-title text-uppercase small">Diajukan</h6></div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card card-stats bg-warning text-white h-100 filter-card" data-filter="DIPROSES" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="diprosesDI">0</h2><h6 class="card-title text-uppercase small">Diproses</h6></div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card card-stats bg-info text-white h-100 filter-card" data-filter="DIKIRIM" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="dikirimDI">0</h2><h6 class="card-title text-uppercase small">Dikirim</h6></div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card card-stats bg-success text-white h-100 filter-card" data-filter="SAMPAI" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="sampaiDI">0</h2><h6 class="card-title text-uppercase small">Sampai</h6></div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card card-stats bg-danger text-white h-100 filter-card" data-filter="DIBATALKAN" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="dibatalkanDI">0</h2><h6 class="card-title text-uppercase small">Dibatalkan</h6></div></div></div>
  </div>

  <!-- Tabel Daftar DI -->
  <div class="card table-card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
      <h5 class="h5 mb-0 text-gray-800">Daftar Delivery Instructions (DI)</h5>
      <button class="btn btn-sm btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#diCreateModal">
        <span class="fw-semibold">+ Buat DI</span>
      </button>
    </div>
    <div class="card-body">
      <table id="diTable" class="table table-striped table-hover" style="width:100%">
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
        <tbody>
          <!-- Data akan dimuat oleh DataTables melalui AJAX -->
        </tbody>
      </table>
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
let currentStatusFilter = 'all';

document.addEventListener('DOMContentLoaded', ()=>{
  // Store filtered data globally for filter functionality
  let allData = [];
  let filteredData = [];

  // Initialize DataTable
  const table = $('#diTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
      url: '<?= base_url('marketing/di/list') ?>',
      type: 'GET',
      dataSrc: function(json) {
        allData = json.data || [];
        updateStatistics(allData);
        applyCurrentFilter();
        return filteredData;
      }
    },
    columns: [
      { data: 'nomor_di', render: function(data, type, row) {
        return `<a href="#" onclick="openDiDetail(${row.id});return false;">${data}</a>`;
      }},
      { data: 'spk_id', defaultContent: '-' },
      { data: 'po_kontrak_nomor', defaultContent: '-' },
      { data: 'pelanggan', defaultContent: '-' },
      { data: 'spk_pic', defaultContent: '-' },
      { data: 'spk_kontak', defaultContent: '-' },
      { data: 'lokasi', defaultContent: '-' },
      { data: 'tanggal_kirim', defaultContent: '-' },
      { data: 'status', render: function(data, type, row) {
        const statusMap = {
          'DIAJUKAN': { class: 'secondary', text: 'Diajukan' },
          'DIPROSES': { class: 'warning', text: 'Diproses' },
          'DIKIRIM': { class: 'info', text: 'Dikirim' },
          'SAMPAI': { class: 'success', text: 'Sampai' },
          'DIBATALKAN': { class: 'danger', text: 'Dibatalkan' }
        };
        const status = statusMap[data?.toUpperCase()] || { class: 'secondary', text: 'Diajukan' };
        return `<span class="badge bg-${status.class}">${status.text}</span>`;
      }}
    ],
    order: [[0, 'desc']],
    language: {
      processing: "Memuat...",
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ entri",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
      infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
      infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
      paginate: {
        first: "Pertama",
        last: "Terakhir", 
        next: "Selanjutnya",
        previous: "Sebelumnya"
      }
    }
  });

  function updateStatistics(data) {
    const total = data.length;
    const diajukan = data.filter(item => !item.status || item.status.toUpperCase() === 'DIAJUKAN').length;
    const diproses = data.filter(item => item.status?.toUpperCase() === 'DIPROSES').length;
    const dikirim = data.filter(item => item.status?.toUpperCase() === 'DIKIRIM').length;
    const sampai = data.filter(item => item.status?.toUpperCase() === 'SAMPAI').length;
    const dibatalkan = data.filter(item => item.status?.toUpperCase() === 'DIBATALKAN').length;
    
    document.getElementById('totalDI').textContent = total;
    document.getElementById('diajukanDI').textContent = diajukan;
    document.getElementById('diprosesDI').textContent = diproses;
    document.getElementById('dikirimDI').textContent = dikirim;
    document.getElementById('sampaiDI').textContent = sampai;
    document.getElementById('dibatalkanDI').textContent = dibatalkan;
  }

  function applyCurrentFilter() {
    if (currentStatusFilter === 'all') {
      filteredData = [...allData];
    } else {
      filteredData = allData.filter(item => {
        const status = item.status?.toUpperCase() || 'DIAJUKAN';
        return status === currentStatusFilter;
      });
    }
  }

  // Filter cards click handlers
  document.querySelectorAll('.filter-card[data-filter]').forEach(card => {
    card.addEventListener('click', function() {
      const filter = this.dataset.filter;
      currentStatusFilter = filter;
      
      // Update active card
      document.querySelectorAll('.filter-card[data-filter]').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      
      // Apply filter and refresh table
      applyCurrentFilter();
      table.clear().rows.add(filteredData).draw();
    });
  });

  // Set default active filter
  document.querySelector('[data-filter="all"]').classList.add('active');

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
          table.ajax.reload(); // Reload DataTable instead of custom loadDI
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
