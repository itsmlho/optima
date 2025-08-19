<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Delivery Instructions (Marketing)</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#diCreateModal">Buat DI</button>
  </div>

  <div class="card">
    <div class="card-body p-0">
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
document.addEventListener('DOMContentLoaded', ()=>{
  const tb = document.querySelector('#diTable tbody');
  function loadDI(){
    fetch('<?= base_url('marketing/di/list') ?>').then(r=>r.json()).then(j=>{
      tb.innerHTML = '';
      (j.data||[]).forEach(r=>{
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
    });
  }
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
