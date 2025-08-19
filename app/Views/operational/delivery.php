<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-3">
  <h5 class="mb-3">Delivery Instructions</h5>
  <div class="card">
    <div class="card-body p-0">
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
    </div>
  </div>
</div>
<script>
// Global variables for approval workflow
let currentApprovalStage = '';
let currentDiId = null;

document.addEventListener('DOMContentLoaded', ()=>{
  const tb = document.querySelector('#diTable tbody');
  function load(){
    fetch('<?= base_url('operational/delivery/list') ?>').then(r=>r.json()).then(j=>{
      tb.innerHTML = '';
      (j.data||[]).forEach(r=>{
        const tr = document.createElement('tr');
        const badge = (s)=>{ const m={DIAJUKAN:'secondary',DIPROSES:'info',DIKIRIM:'warning',SAMPAI:'success',DIBATALKAN:'danger'}; const c=m[(s||'').toUpperCase()]||'secondary'; return `<span class="badge bg-${c}">${s}</span>`; };
        // Conditional action button based on status - approval workflow style
        let aksiBtn = '';
        if (!r.status || r.status === 'DIAJUKAN') {
          aksiBtn = '<span class="text-muted">Menunggu diproses</span>';
        } else if (r.status === 'DIPROSES') {
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
        } else if (r.status === 'SAMPAI') {
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
          <td>${badge(r.status)}</td>
          <td>${aksiBtn}</td>`;

        tb.appendChild(tr);
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
      });
    });
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
        load();
        notify(j.message || 'Approval berhasil disimpan', 'success');
      } else {
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
                <div class="col-md-6">Tanggal Kirim: ${d.perencanaan_tanggal_approve||'-'}</div>
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
