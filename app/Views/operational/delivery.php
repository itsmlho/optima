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

  <!-- Tabel Delivery -->
  <div class="card table-card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
      <h5 class="h5 mb-0 text-gray-800">Daftar Delivery Instructions</h5>
    </div>
    <div class="card-body">
      <table id="diTable" class="table table-striped table-hover" style="width:100%">
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
  </div>
</div>

<script>
let currentStatusFilter = 'all';

// Global variables for approval workflow
let currentApprovalStage = '';
let currentDiId = null;

document.addEventListener('DOMContentLoaded', ()=>{
  // Store filtered data globally for filter functionality
  let allData = [];
  let filteredData = [];

  // Initialize DataTable
  const table = $('#diTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
      url: '<?= base_url('operational/delivery/list') ?>',
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
      { data: 'po_kontrak_nomor', defaultContent: '-' },
      { data: 'pelanggan', defaultContent: '-' },
      { data: 'lokasi', defaultContent: '-' },
      { data: 'items_label', defaultContent: '-' },
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
      }},
      { data: null, render: function(data, type, row) {
        // Conditional action button based on status - approval workflow style
        let aksiBtn = '';
        if (!row.status || row.status === 'DIAJUKAN') {
          aksiBtn = '<span class="text-muted">Menunggu diproses</span>';
        } else if (row.status === 'DIPROSES') {
          // Show approval stage buttons directly in table
          const perencanaanDone = row.perencanaan_tanggal_approve ? true : false;
          const berangkatDone = row.berangkat_tanggal_approve ? true : false;
          const sampaiDone = row.sampai_tanggal_approve ? true : false;
          
          let approvalButtons = [];
          
          // Add active button for current stage
          if (!perencanaanDone) {
            approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('perencanaan', 'Perencanaan Pengiriman', ${row.id})">Perencanaan</button>`);
          } else if (!berangkatDone) {
            approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('berangkat', 'Berangkat', ${row.id})">Berangkat</button>`);
          } else if (!sampaiDone) {
            approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('sampai', 'Sampai', ${row.id})">Sampai</button>`);
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
        } else if (row.status === 'SAMPAI') {
          aksiBtn = '<span class="text-success">Completed</span>';
        } else {
          aksiBtn = '<span class="text-muted">-</span>';
        }
        
        return aksiBtn;
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
              <strong><i class="fas fa-info-circle"></i> Konfirmasi Data Pengiriman</strong><br>
              Pastikan data di bawah ini sudah benar sebelum melanjutkan ke tahap berikutnya.
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
          <strong><i class="fas fa-truck"></i> Data Operasional Pengiriman</strong><br>
          Lengkapi data supir dan kendaraan yang akan mengirim barang.
        </div>
        <div class="row g-3">
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
            <label class="form-label">Catatan Keberangkatan</label>
            <textarea class="form-control" name="catatan_berangkat" rows="3" 
                      placeholder="Masukkan catatan keberangkatan, kondisi barang, dll..."></textarea>
            <div class="form-text">Catatan ini akan menjadi dokumentasi saat barang diberangkatkan.</div>
          </div>
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
      if (j && j.success) {
        bootstrap.Modal.getInstance(document.getElementById('approvalStageModal')).hide();
        // Reload table to update buttons
        table.ajax.reload(null, false);
        notify(j.message || 'Approval berhasil disimpan', 'success');
      } else {
        notify(j.message || 'Gagal menyimpan approval', 'error');
      }
    }).catch(error=>{
      console.error('Error:', error);
      notify('Terjadi kesalahan pada sistem: ' + error.message, 'error');
    });
  });

  window.openDiDetail = (id) => {
    currentDiId = id;
    const modal = new bootstrap.Modal(document.getElementById('diDetailModal'));
    const body = document.getElementById('diDetailBody');
    body.innerHTML = '<p class="text-muted">Memuat...</p>';
    fetch('<?= base_url('operational/delivery/detail/') ?>'+id).then(r=>r.json()).then(j=>{
      if (!j.success) { body.innerHTML = '<div class="text-danger">Gagal memuat detail</div>'; modal.show(); return; }
      const d = j.data||{}; const spk = j.spk||{}; const items = j.items||[];
      const status = d.status || 'DIAJUKAN';
      const itemsHtml = items.length ? '<ul>'+items.map(i=>`<li>${i.item_type}: ${i.label}</li>`).join('')+'</ul>' : '<div class="text-muted">-</div>';
      
      // Update action buttons based on status
      const actionDiv = document.getElementById('modalActionButtons');
      let actionButtons = '';
      
      if (status === 'DIAJUKAN') {
        actionButtons = '<button class="btn btn-success btn-sm" id="btnProsesDI">Proses DI</button>';
      } else if (status === 'DIPROSES') {
        // Show approval stage buttons based on completion status
        let approvalButtons = [];
        
        // Check which stages are completed
        const perencanaanDone = d.perencanaan_tanggal_approve ? true : false;
        const berangkatDone = d.berangkat_tanggal_approve ? true : false;
        const sampaiDone = d.sampai_tanggal_approve ? true : false;
        
        // Add buttons for incomplete stages
        if (!perencanaanDone) {
          approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'perencanaan\', \'Perencanaan Pengiriman\', '+d.id+')">Perencanaan</button>');
        } else if (!berangkatDone) {
          approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'berangkat\', \'Berangkat\', '+d.id+')">Berangkat</button>');
        } else if (!sampaiDone) {
          approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'sampai\', \'Sampai\', '+d.id+')">Sampai</button>');
        }
        
        // Show completed stages with checkmarks
        if (perencanaanDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Perencanaan</span>');
        if (berangkatDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Berangkat</span>');
        if (sampaiDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Sampai</span>');
        
        actionButtons = approvalButtons.join(' ');
      } else if (status === 'SAMPAI') {
        actionButtons = `<span class="badge bg-success">Completed</span>`;
      }
      
      // Add PDF SPK button if SPK exists (for all statuses except DIAJUKAN)
      if (status !== 'DIAJUKAN' && spk && spk.id) {
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
          
          ${status === 'DIPROSES' || status === 'SAMPAI' ? `
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
              Catatan: ${d.catatan||'-'}
            </li>
            <li class="list-group-item">
              <strong>Berangkat</strong><br>
              <div class="row g-2">
                <div class="col-md-6">Tanggal Kirim: ${d.tanggal_kirim || d.berangkat_tanggal_approve || '-'}</div>
                <div class="col-md-6">Estimasi Sampai: ${d.estimasi_sampai||'-'}</div>
                <div class="col-md-6">Nama Supir: ${d.nama_supir||'-'}</div>
                <div class="col-md-6">No HP Supir: ${d.no_hp_supir||'-'}</div>
                <div class="col-md-6">No SIM: ${d.no_sim_supir||'-'}</div>
                <div class="col-md-6">Kendaraan: ${d.kendaraan||'-'}</div>
                <div class="col-md-6">No Polisi: ${d.no_polisi_kendaraan||'-'}</div>
              </div>
              Catatan: ${d.catatan_berangkat||'-'}
            </li>
            <li class="list-group-item">
              <strong>Sampai</strong><br>
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
            formData.append('status', 'DIPROSES');
            
            fetch(`<?= base_url('operational/delivery/update-status/') ?>${id}`, {
              method: 'POST',
              headers: {'X-Requested-With': 'XMLHttpRequest'},
              body: formData
            }).then(r=>r.json()).then(result=>{
              if (result && result.success) {
                notify('DI berhasil diproses. Status menjadi DIPROSES.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('diDetailModal')).hide();
                table.ajax.reload(null, false); // Reload table
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

  // Unified notifier (fallbacks)
  window.notify = function(msg, type='success'){
    if (window.OptimaPro && typeof OptimaPro.showNotification==='function') return OptimaPro.showNotification(msg, type);
    if (typeof showNotification==='function') return showNotification(msg, type);
    alert(msg);
  }
  
  window.upd = (id, st) => {
    const fd = new FormData(); fd.append('status', st);
    fetch('<?= base_url('operational/delivery/update-status/') ?>'+id, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd})
      .then(r=>r.json()).then(()=>table.ajax.reload(null, false));
  }
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
