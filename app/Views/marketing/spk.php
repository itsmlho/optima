<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
    <div class="d-flex justify-content-end align-items-center mb-3">
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('operational/tracking') ?>">Tracking</a>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#spkModal">Buat SPK</button>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Daftar SPK</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0" id="spkList">
                    <thead><tr><th>No. SPK</th><th>Jenis</th><th>Kontrak/PO</th><th>Nama Perusahaan</th><th>PIC</th><th>Kontak</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Monitoring Kontrak → SPK</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" id="monitoringTable">
                    <thead>
                        <tr>
                            <th>Kontrak</th>
                            <th>PO Marketing</th>
                            <th>Nama Perusahaan</th>
                            <th>Lokasi</th>
                            <th>Total SPK</th>
                            <th>SUB</th>
                            <th>PROG</th>
                            <th>READY</th>
                            <th>DELIV</th>
                            <th>CANCEL</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="spkModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header"><h6 class="modal-title">Buat SPK</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="spkForm">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Jenis SPK</label>
                            <select class="form-select form-select-sm w-auto" name="jenis_spk" required>
                                <option value="UNIT" selected>SPK Unit</option>
                                <option value="ATTACHMENT">SPK Attachment</option>
                                <option value="TUKAR">SPK Tukar</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" id="kontrakLabel">Kontrak/PO (<span id="kontrakStatusTxt">Pending</span>)</label>
                            <input class="form-control" list="kontrakOptions" name="po_kontrak_nomor" placeholder="Cari no kontrak / no PO / pelanggan" autocomplete="off">
                            <datalist id="kontrakOptions"></datalist>
                            <div class="form-text" id="kontrakHelp">Ketik untuk mencari dari kontrak status Pending.</div>
                        </div>
                        <div class="mb-2"><label class="form-label">Pelanggan</label><input class="form-control" name="pelanggan" id="inpPelanggan" required></div>
                        <div class="mb-2"><label class="form-label">PIC (Person In Charge)</label><input class="form-control" name="pic" id="inpPic" placeholder="Nama PIC dari perusahaan"></div>
                        <div class="mb-2"><label class="form-label">Kontak PIC</label><input class="form-control" name="kontak" id="inpKontak" placeholder="Nomor telepon/HP PIC"></div>
                        <div class="mt-2"><label class="form-label">Lokasi</label><input class="form-control" name="lokasi" id="inpLokasi" placeholder="Otomatis mengikuti Pelanggan"></div>
                        <div class="mb-2">
                            <label class="form-label">Delivery Plan</label>
                            <input type="date" class="form-control" name="delivery_plan" placeholder="Tanggal rencana pengiriman">
                        </div>
                        
                        <hr>
                        <div class="row g-2" id="specGrid">
                            <label class="form-label">Spesifikasi (permintaan garis besar)</label>
                            <!-- Departemen, Tipe, Jenis (cascading) -->
                            <div class="col-4" data-spec="departemen_id"><label class="form-label">Departemen</label><select class="form-select" name="spesifikasi[departemen_id]" id="optDepartemen"></select></div>
                            <div class="col-4" data-spec="tipe_unit"><label class="form-label">Tipe Unit</label><select class="form-select" id="optTipeUnit"></select></div>
                            <div class="col-4" data-spec="tipe_jenis"><label class="form-label">Jenis</label><select class="form-select" name="spesifikasi[tipe_jenis]" id="optTipeJenis"></select></div>
                            <div class="col-6" data-spec="merk_unit"><label class="form-label">Merk Unit</label><select class="form-select" name="spesifikasi[merk_unit]" id="optMerkUnit"></select></div>
                            <div class="col-6" data-spec="valve_id"><label class="form-label">Valve</label><select class="form-select" name="spesifikasi[valve_id]" id="optValve"></select></div>
                            <div class="col-6" data-spec="jenis_baterai"><label class="form-label">Baterai (Jenis)</label><select class="form-select" name="spesifikasi[jenis_baterai]" id="optJenisBaterai"></select></div>
                            <div class="col-6" data-spec="attachment_tipe"><label class="form-label">Attachment (Tipe)</label><select class="form-select" name="spesifikasi[attachment_tipe]" id="optAttachmentTipe"></select></div>
                            <div class="col-6" data-spec="attachment_merk"><label class="form-label">Merk Attachment</label><select class="form-select" name="spesifikasi[attachment_merk]" id="optAttachmentMerk"></select></div>
                            <div class="col-6" data-spec="roda_id"><label class="form-label">Roda</label><select class="form-select" name="spesifikasi[roda_id]" id="optRoda"></select></div>
                            <div class="col-6" data-spec="kapasitas_id"><label class="form-label">Kapasitas</label><select class="form-select" name="spesifikasi[kapasitas_id]" id="optKapasitas"></select></div>
                            <div class="col-6" data-spec="mast_id"><label class="form-label">Mast</label><select class="form-select" name="spesifikasi[mast_id]" id="optMast"></select></div>
                            <div class="col-6" data-spec="ban_id"><label class="form-label">Ban</label><select class="form-select" name="spesifikasi[ban_id]" id="optBan"></select></div>
                        </div>
                        <div class="mt-2" id="accBlock">
                            <label class="form-label">Aksesoris</label>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Lampu-lampu" id="acc1"> <label class="form-check-label" for="acc1">Lampu-lampu</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Rotary Lamp" id="acc2"> <label class="form-check-label" for="acc2">Rotary Lamp</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Blue Light Spot" id="acc3"> <label class="form-check-label" for="acc3">Blue Light Spot</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Red Line" id="acc4"> <label class="form-check-label" for="acc4">Red Line</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Work Light" id="acc6"> <label class="form-check-label" for="acc6">Work Light</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Back Buzzer" id="acc7"> <label class="form-check-label" for="acc7">Back Buzzer</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Camera" id="acc8"> <label class="form-check-label" for="acc8">Camera</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Camera AI" id="acc9"> <label class="form-check-label" for="acc9">Camera AI</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Speed Limitter" id="acc10"> <label class="form-check-label" for="acc10">Speed Limitter</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Apar 1kg" id="acc17"> <label class="form-check-label" for="acc17">Apar 1kg</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Apar 3kg" id="acc18"> <label class="form-check-label" for="acc18">Apar 3kg</label></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Laser Fork" id="acc11"> <label class="form-check-label" for="acc11">Laser Fork</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Voice Announcer" id="acc12"> <label class="form-check-label" for="acc12">Voice Announcer</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Horn Speaker" id="acc13"> <label class="form-check-label" for="acc13">Horn Speaker</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Sensor Parking" id="acc14"> <label class="form-check-label" for="acc14">Sensor Parking</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Bio Metric" id="acc15"> <label class="form-check-label" for="acc15">Bio Metric</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Horn (klakson)" id="acc16"> <label class="form-check-label" for="acc16">Horn (klakson)</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="P3K" id="acc19"> <label class="form-check-label" for="acc19">P3K</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Safety Belt Interloc" id="acc20"> <label class="form-check-label" for="acc20">Safety Belt Interloc</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Beacon" id="acc21"> <label class="form-check-label" for="acc21">Beacon</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Telematic" id="acc22"> <label class="form-check-label" for="acc22">Telematic</label></div>
                                    <div class="form-check"><input class="form-check-input acc" type="checkbox" value="Spark Arrestor" id="acc23"> <label class="form-check-label" for="acc23">Spark Arrestor</label></div>
                                </div>
                            </div>
                            <input type="hidden" name="spesifikasi[aksesoris]" id="accHidden">
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="catatan" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Tutup</button><button class="btn btn-primary" type="submit">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Buat DI -->
    <div class="modal fade" id="diModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h6 class="modal-title">Buat Delivery Instruction</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="diForm">
                    <div class="modal-body">
                        <input type="hidden" name="spk_id" id="diSpkId">
                        <div class="mb-2"><label class="form-label">No. SPK</label><input class="form-control" id="diNoSpk" readonly></div>
                        <div class="mb-2"><label class="form-label">Kontrak/PO</label><input class="form-control" id="diPoNo" readonly></div>
                        <div class="mb-2"><label class="form-label">Pelanggan</label><input class="form-control" id="diPelanggan" readonly></div>
                        <div class="mb-2"><label class="form-label">Lokasi</label><input class="form-control" id="diLokasi" readonly></div>
                        <div class="mb-2">
                            <label class="form-label">Item Terpilih (dari Service)</label>
                            <div class="alert alert-light border" id="diSelectedSummary">
                                <span class="text-muted">Belum ada ringkasan.</span>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><label class="form-label">Tanggal Kirim</label><input type="date" class="form-control" name="tanggal_kirim"></div>
                            <div class="col-6 d-flex align-items-end"><span class="text-muted small">Opsional</span></div>
                        </div>
                        <div class="mt-2"><label class="form-label">Catatan</label><textarea class="form-control" name="catatan" rows="3" placeholder="Instruksi pengiriman (opsional)"></textarea></div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Buat DI</button></div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Map status to Bootstrap badge classes per entity
    function statusBadge(entity, status){
        const s = (status||'').toUpperCase();
        const mapSPK = { SUBMITTED:'secondary', IN_PROGRESS:'info', READY:'success', DELIVERED:'primary', COMPLETED:'primary', CANCELLED:'danger' };
        const mapDI  = { SUBMITTED:'secondary', DISPATCHED:'info', ARRIVED:'success', CANCELLED:'danger' };
        const cls = (entity==='DI'?mapDI[s]:mapSPK[s]) || 'secondary';
        return `<span class="badge bg-${cls}">${status}</span>`;
    }
    function loadSpk(){
        fetch('<?= base_url('marketing/spk/list') ?>').then(r=>r.json()).then(j=>{
            const tb = document.querySelector('#spkList tbody');
            tb.innerHTML = '';
            (j.data||[]).forEach(r=>{
                const tr = document.createElement('tr');
                const diBtn = (r.status === 'READY')
                  ? `<button class="btn btn-sm btn-primary buat-di" data-id="${r.id}" data-spk='${JSON.stringify({id:r.id, nomor_spk:r.nomor_spk, po:r.po_kontrak_nomor, pelanggan:r.pelanggan, lokasi:r.lokasi}).replace(/'/g,"&apos;")}' title="Buat DI">Buat DI</button>`
                  : '';
                const aksiBtn = diBtn || '<span class="text-muted">-</span>';
                tr.innerHTML = `<td><a href="#" onclick=\"openDetail(${r.id});return false;\">${r.nomor_spk}</a></td>`+
                  `<td><span class=\"badge bg-dark\">${r.jenis_spk||'UNIT'}</span></td>`+
                  `<td>${r.po_kontrak_nomor||'-'}</td>`+
                  `<td>${r.pelanggan||'-'}</td>`+
                  `<td>${r.pic||'-'}</td>`+
                  `<td>${r.kontak||'-'}</td>`+
                  `<td>${statusBadge('SPK', r.status)}</td>`+
                  `<td>${aksiBtn}</td>`;
                tb.appendChild(tr);
            });
            // Wire up Buat DI buttons
            tb.querySelectorAll('.buat-di').forEach(btn=>{
                btn.addEventListener('click', (e)=>{
                    const data = JSON.parse(e.currentTarget.getAttribute('data-spk').replace(/&apos;/g, "'"));
                    document.getElementById('diSpkId').value = data.id || '';
                    document.getElementById('diNoSpk').value = data.nomor_spk || '';
                    document.getElementById('diPoNo').value = data.po || '';
                    document.getElementById('diPelanggan').value = data.pelanggan || '';
                    document.getElementById('diLokasi').value = data.lokasi || '';
                    document.getElementById('diPic').value = data.pic || '';
                    document.getElementById('diKontak').value = data.kontak || '';
                    // Load selected items summary
                    const sum = document.getElementById('diSelectedSummary');
                    if (sum) { sum.innerHTML = '<span class="text-muted">Memuat item terpilih...</span>'; }
                    fetch(`<?= base_url('marketing/spk/detail/') ?>${data.id}`).then(r=>r.json()).then(j=>{
                        if (sum) {
                            if (j && j.success) {
                                const s = j.spesifikasi || {};
                                const u = s.selected && s.selected.unit ? s.selected.unit : null;
                                const a = s.selected && s.selected.attachment ? s.selected.attachment : null;
                                const unit = u ? `${u.no_unit||'-'} - ${u.merk_unit||'-'} ${u.model_unit||''} @ ${u.lokasi_unit||'-'}${u.serial_number?` [SN: ${u.serial_number}]`:''}` : null;
                                const att  = a ? `${a.tipe||'-'} ${a.merk||''} ${a.model||''}${a.sn_attachment?` [SN: ${a.sn_attachment}]`:''}${a.lokasi_penyimpanan?` @ ${a.lokasi_penyimpanan}`:''}` : null;
                                const html = `<ul class=\"mb-0\">${unit?`<li>Unit: ${unit}</li>`:''}${att?`<li>Attachment: ${att}</li>`:''}</ul>`;
                                sum.innerHTML = (unit || att) ? html : '<span class="text-muted">Belum ada item yang ditetapkan Service.</span>';
                            } else {
                                sum.innerHTML = '<span class="text-danger">Gagal memuat ringkasan item.</span>';
                            }
                        }
                    });
                    const modal = new bootstrap.Modal(document.getElementById('diModal'));
                    modal.show();
                });
            });
            // Detail now opens by clicking the No. SPK link
        });
    }
    function loadKontrakOptions(q){
        const url = new URL('<?= base_url('marketing/spk/kontrak-options') ?>', window.location.origin);
        if(q) url.searchParams.set('q', q);
        const jenisSel = document.querySelector('select[name="jenis_spk"]');
        const jenis = jenisSel ? jenisSel.value : 'UNIT';
        const kontrakStatus = (jenis === 'TUKAR') ? 'Aktif' : 'Pending';
        url.searchParams.set('status', kontrakStatus);
        fetch(url).then(r=>r.json()).then(j=>{
            const dl = document.getElementById('kontrakOptions');
            dl.innerHTML = '';
            (j.data||[]).forEach(opt=>{
                const o = document.createElement('option');
                o.value = opt.no_po_marketing || opt.no_kontrak || '';
                o.label = opt.label;
                dl.appendChild(o);
            });
        });
    }
    function loadMonitoring(){
        fetch('<?= base_url('marketing/spk/monitoring') ?>').then(r=>r.json()).then(j=>{
            const tb = document.querySelector('#monitoringTable tbody');
            tb.innerHTML = '';
            (j.data||[]).forEach(r=>{
                const tr = document.createElement('tr');
                const fmt = (v)=> v==null?0:v;
                tr.innerHTML = `
                    <td>${r.no_kontrak||'-'}</td>
                    <td>${r.no_po_marketing||'-'}</td>
                    <td>${r.pelanggan||'-'}</td>
                    <td>${r.lokasi||'-'}</td>
                    <td><span class="badge bg-dark">${fmt(r.total_spk)}</span></td>
                    <td><span class="badge bg-secondary">${fmt(r.submitted)}</span></td>
                    <td><span class="badge bg-info">${fmt(r.in_progress)}</span></td>
                    <td><span class="badge bg-success">${fmt(r.ready)}</span></td>
                    <td><span class="badge bg-primary">${fmt(r.delivered)}</span></td>
                    <td><span class="badge bg-danger">${fmt(r.cancelled)}</span></td>
                    <td>${r.last_update||'-'}</td>`;
                tb.appendChild(tr);
            });
        });
    }
    document.addEventListener('DOMContentLoaded',()=>{
    loadSpk();
    loadKontrakOptions('');
    loadMonitoring();
    const kontrakInput = document.querySelector('input[name="po_kontrak_nomor"]');
    const pelangganInput = document.getElementById('inpPelanggan');
    const lokasiInput = document.getElementById('inpLokasi');
    kontrakInput.addEventListener('input', (e) => {
            const v = e.target.value.trim();
            // fetch as user types (debounce-lite)
            loadKontrakOptions(v);
            // try to find matching option and autofill pelanggan & lokasi from dataset
            const dl = document.getElementById('kontrakOptions');
            const match = Array.from(dl.options).find(o => o.value === v);
            if (match) {
                // We can't store custom data in datalist options cross-browser reliably; parse from label first
                // Label format: "<no kontrak> (<no po>) - <pelanggan>"
                if (match.label) {
                    const parts = match.label.split(' - ');
                    if (parts[1]) {
                        pelangganInput.value = parts[1];
                    }
                }
            }
        });
        // Lokasi mengikuti perubahan Pelanggan secara langsung
        pelangganInput.addEventListener('input', ()=>{ /* do not mirror lokasi automatically anymore */ });

        // Override lokasi based on kontrak lookup when focus leaves kontrak field (fetch selected option’s lokasi via API)
        kontrakInput.addEventListener('change', () => {
            const v = kontrakInput.value.trim();
            const url = new URL('<?= base_url('marketing/spk/kontrak-options') ?>', window.location.origin);
            if (v) url.searchParams.set('q', v);
            const jenisSel = document.querySelector('select[name="jenis_spk"]');
            const jenis = jenisSel ? jenisSel.value : 'UNIT';
            url.searchParams.set('status', (jenis === 'TUKAR') ? 'Aktif' : 'Pending');
            fetch(url).then(r=>r.json()).then(j=>{
                const rows = j.data||[];
                // Try exact match by no_po_marketing or no_kontrak
                const exact = rows.find(x => x.no_po_marketing === v || x.no_kontrak === v);
                if (exact) {
                    if (exact.pelanggan) pelangganInput.value = exact.pelanggan;
                    if (exact.lokasi) lokasiInput.value = exact.lokasi;
                }
            });
        });

        // Toggle fields by jenis SPK and update kontrak labels/help
        const jenisSel = document.querySelector('select[name="jenis_spk"]');
        function applyJenisRules(){
            const jenis = jenisSel.value;
            const showForAttachment = new Set(['attachment_tipe','attachment_merk','kapasitas_id']);
            const allSpecCols = Array.from(document.querySelectorAll('#specGrid [data-spec]'));
            if (jenis === 'ATTACHMENT') {
                allSpecCols.forEach(el => {
                    const key = el.getAttribute('data-spec');
                    if (showForAttachment.has(key)) { el.classList.remove('d-none'); }
                    else { el.classList.add('d-none'); }
                });
                document.getElementById('accBlock')?.classList.add('d-none');
                document.getElementById('kontrakStatusTxt').textContent = 'Pending';
                document.getElementById('kontrakHelp').textContent = 'Ketik untuk mencari dari kontrak status Pending.';
            } else if (jenis === 'TUKAR') {
                allSpecCols.forEach(el => el.classList.remove('d-none'));
                document.getElementById('accBlock')?.classList.remove('d-none');
                document.getElementById('kontrakStatusTxt').textContent = 'Aktif';
                document.getElementById('kontrakHelp').textContent = 'Ketik untuk mencari dari kontrak status Aktif (untuk SPK Tukar).';
            } else {
                // UNIT
                allSpecCols.forEach(el => el.classList.remove('d-none'));
                document.getElementById('accBlock')?.classList.remove('d-none');
                document.getElementById('kontrakStatusTxt').textContent = 'Pending';
                document.getElementById('kontrakHelp').textContent = 'Ketik untuk mencari dari kontrak status Pending.';
            }
            // Refresh kontrak options based on jenis
            loadKontrakOptions('');
        }
        jenisSel.addEventListener('change', applyJenisRules);
        applyJenisRules();

        // Accessories sync - gather all .acc checkboxes
        const accBoxes = Array.from(document.querySelectorAll('.acc'));
        const accHidden = document.getElementById('accHidden');
        function syncAccessories(){
            const vals = accBoxes.filter(cb=>cb.checked).map(cb=>cb.value);
            accHidden.value = JSON.stringify(vals);
        }
        accBoxes.forEach(cb=>cb.addEventListener('change', syncAccessories));
        syncAccessories();
        document.getElementById('spkForm').addEventListener('submit', (e)=>{
            e.preventDefault();
            const fd = new FormData(e.target);
            fetch('<?= base_url('marketing/spk/create') ?>',{method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd})
                .then(r=>r.json()).then(j=>{ 
                    if(j.success){ 
                        e.target.reset(); loadSpk(); loadMonitoring();
                        bootstrap.Modal.getInstance(document.getElementById('spkModal')).hide();
                        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('SPK dibuat: ' + (j.nomor||''), 'success');
                        else if (typeof showNotification==='function') showNotification('SPK dibuat: ' + (j.nomor||''), 'success');
                    } else {
                        const msg = j.message || 'Gagal membuat SPK';
                        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification(msg, 'error');
                        else if (typeof showNotification==='function') showNotification(msg, 'error');
                    }
                });
        });
        // DI form submit
        document.getElementById('diForm').addEventListener('submit', (e)=>{
            e.preventDefault();
            const fd = new FormData(e.target);
            // spk_id already set; backend enforces COMPLETED status
            fetch('<?= base_url('marketing/di/create') ?>',{method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd})
                .then(r=>r.json()).then(j=>{
                    if (j && j.success) {
                        bootstrap.Modal.getInstance(document.getElementById('diModal')).hide();
                        e.target.reset();
                        loadSpk();
                        loadMonitoring();
                        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('DI dibuat: '+ (j.nomor||''), 'success');
                        else if (typeof showNotification==='function') showNotification('DI dibuat: '+ (j.nomor||''), 'success');
                        else alert('DI dibuat: '+ (j.nomor||''));
                    } else {
                        const msg = (j && j.message) ? j.message : 'Gagal membuat DI';
                        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification(msg, 'error');
                        else if (typeof showNotification==='function') showNotification(msg, 'error');
                        else alert(msg);
                    }
                });
        });
        // Load dropdown options for specs
            function fillSelect(sel, items) { sel.innerHTML = '<option value="">- Pilih -</option>' + items.map(i => `<option value="${i.id}">${i.name}</option>`).join(''); }
            const specTypes = [
                { type: 'merk_unit', sel: '#optMerkUnit' },
                { type: 'valve', sel: '#optValve' },
                { type: 'jenis_baterai', sel: '#optJenisBaterai' },
                { type: 'attachment_tipe', sel: '#optAttachmentTipe' },
                { type: 'attachment_merk', sel: '#optAttachmentMerk' },
                { type: 'roda', sel: '#optRoda' },
                { type: 'departemen', sel: '#optDepartemen' },
                { type: 'kapasitas', sel: '#optKapasitas' },
                { type: 'mast', sel: '#optMast' },
                { type: 'ban', sel: '#optBan' },
            ];
            specTypes.forEach(s => {
                fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=${s.type}`)
                    .then(r => r.json()).then(j => { if (j.success) { const el = document.querySelector(s.sel); if (el) fillSelect(el, j.data || []); } });
            });

            // Cascading dropdowns (Departemen -> Tipe -> Jenis) ala Purchasing
            const $dept = document.querySelector('#optDepartemen');
            const $tipe = document.querySelector('#optTipeUnit');
            const $jenis = document.querySelector('#optTipeJenis');
            const $merk = document.querySelector('#optMerkUnit');
            const $kap = document.querySelector('#optKapasitas');
            // departemen -> tipe list uses Purchasing api/get-tipe-units to fetch tipe distinct
            function reloadTipeFromDept(){
                if(!$dept) return;
                const deptVal = $dept.value;
                // reset tipe & jenis
                if ($tipe) $tipe.innerHTML = '<option value="">- Pilih -</option>';
                if ($jenis) $jenis.innerHTML = '<option value="">- Pilih -</option>';
                if(!deptVal){ return; }
                const url = new URL('<?= base_url('/purchasing/api/get-tipe-units') ?>', window.location.origin);
                url.searchParams.set('departemen', deptVal);
                fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(j=>{
                    if(!j.success) return;
                    const types = [...new Set((j.data||[]).map(r=>r.tipe).filter(Boolean))];
                    if ($tipe) $tipe.innerHTML = '<option value="">- Pilih -</option>' + types.map(t=>`<option value="${t}">${t}</option>`).join('');
                });
            }
            if($dept){ $dept.addEventListener('change', reloadTipeFromDept); }

            // tipe -> jenis list using same API filtered by departemen & tipe
            function reloadJenisFromTipe(){
                if(!$dept || !$tipe) return;
                const deptVal = $dept.value; const tipeVal = $tipe.value;
                if ($jenis) $jenis.innerHTML = '<option value="">- Pilih -</option>';
                if(!deptVal || !tipeVal){ return; }
                const url = new URL('<?= base_url('/purchasing/api/get-tipe-units') ?>', window.location.origin);
                url.searchParams.set('departemen', deptVal);
                url.searchParams.set('tipe', tipeVal);
                fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(j=>{
                    if(!j.success) return;
                    const jenisList = [...new Set((j.data||[]).map(r=>r.jenis).filter(Boolean))];
                    if ($jenis) $jenis.innerHTML = '<option value="">- Pilih -</option>' + jenisList.map(x=>`<option value="${x}">${x}</option>`).join('');
                });
            }
            if($tipe){ $tipe.addEventListener('change', reloadJenisFromTipe); }

            // tipe change could be used to constrain jenis further via same API (optional here as we store text directly)
            // merk -> model cascade using Purchasing get_model_unit_merk
            const modelSelect = document.createElement('select');
            modelSelect.className = 'form-select';
            modelSelect.name = 'spesifikasi[model_unit_id]';
            modelSelect.id = 'optModelUnit';
            const merkContainer = document.querySelector('[data-spec="merk_unit"]').parentElement || document;
            // Insert model select after merk
            const merkRow = document.querySelector('#optMerkUnit');
            if (merkRow) {
                const wrapper = document.createElement('div');
                wrapper.className = 'col-6';
                wrapper.setAttribute('data-spec','model_unit_id');
                wrapper.innerHTML = '<label class="form-label">Model Unit</label>';
                wrapper.appendChild(modelSelect);
                document.getElementById('specGrid').appendChild(wrapper);
            }
            function reloadModelByMerk(){
                const sel = document.getElementById('optMerkUnit');
                if(!sel) return;
                const merk = sel.value;
                const mdl = document.getElementById('optModelUnit');
                if(!merk){ mdl.innerHTML = '<option value="">- Pilih Merk dulu -</option>'; return; }
                const url = new URL('<?= base_url('purchasing/api/get_model_unit_merk') ?>', window.location.origin);
                url.searchParams.set('merk', merk);
                fetch(url).then(r=>r.json()).then(j=>{
                    const rows = (j.data||[]);
                    mdl.innerHTML = '<option value="">- Pilih Model -</option>' + rows.map(it=>`<option value="${it.id_model_unit}">${it.model_unit}</option>`).join('');
                }).catch(()=>{ mdl.innerHTML = '<option value="">Gagal memuat model</option>'; });
            }
            if ($merk) { $merk.addEventListener('change', reloadModelByMerk); }
    });
    </script>
    <!-- Detail SPK Modal -->
    <div class="modal fade" id="spkDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header"><h6 class="modal-title">Detail SPK</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div id="spkDetailBody"><p class="text-muted">Memuat...</p></div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-outline-secondary" id="btnPrintPdf" href="#" target="_blank" rel="noopener">Print PDF</a>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    function openDetail(id){
        const pdfBtn = document.getElementById('btnPrintPdf');
        if (pdfBtn) { pdfBtn.href = `<?= base_url('marketing/spk/print/') ?>${id}`; }
         const body = document.getElementById('spkDetailBody');
         body.innerHTML = '<p class="text-muted">Memuat...</p>';
        fetch(`<?= base_url('marketing/spk/detail/') ?>${id}`).then(r=>r.json()).then(j=>{
            if (!j.success) { body.innerHTML = '<div class="text-danger">Gagal memuat detail</div>'; return; }
            const d = j.data||{}; const s = j.spesifikasi||{};
            const aks = Array.isArray(s.aksesoris)?s.aksesoris:[];
            const u = s.selected && s.selected.unit ? s.selected.unit : null;
            const a = s.selected && s.selected.attachment ? s.selected.attachment : null;
            const unit = u ? `${u.no_unit||'-'} - ${u.merk_unit||'-'} ${u.model_unit||''} @ ${u.lokasi_unit||'-'}${u.serial_number?` [SN: ${u.serial_number}]`:''}` : null;
            const snList = u ? [
            u.serial_number ? `Unit: ${u.serial_number}` : null,
            u.sn_mast ? `Mast: ${u.sn_mast}` : null,
            u.sn_mesin ? `Mesin: ${u.sn_mesin}` : null,
            u.sn_baterai ? `Baterai: ${u.sn_baterai}` : null,
            u.sn_charger ? `Charger: ${u.sn_charger}` : null,
        ].filter(Boolean) : [];
        const att  = a ? `${a.tipe||'-'} ${a.merk||''} ${a.model||''}${a.sn_attachment?` [SN: ${a.sn_attachment}]`:''}${a.lokasi_penyimpanan?` @ ${a.lokasi_penyimpanan}`:''}` : null;
            body.innerHTML = `
                <div class="row g-2">
                    <div class="col-6"><strong>Jenis SPK:</strong> ${d.jenis_spk||'-'}</div>
                    <div class="col-6"><strong>No SPK:</strong> ${d.nomor_spk}</div>
                    <div class="col-6"><strong>Kontrak/PO:</strong> ${d.po_kontrak_nomor||'-'}</div>
                    <div class="col-6"><strong>Pelanggan:</strong> ${d.pelanggan||'-'}</div>
                    <div class="col-6"><strong>Lokasi:</strong> ${d.lokasi||'-'}</div>
                    <div class="col-6"><strong>Delivery Plan:</strong> ${d.delivery_plan||'-'}</div>
                    <div class="col-6"><strong>Pic:</strong> ${d.pic||'-'}</div>
                    <div class="col-6"><strong>Kontak:</strong> ${d.kontak||'-'}</div>
                    <div class="col-12"><hr></div>
                    <div class="col-6"><strong>Tipe (Jenis):</strong> ${s.tipe_jenis||'-'}</div>
                    <div class="col-6"><strong>Merk Unit:</strong> ${s.merk_unit||'-'}</div>
                    <div class="col-6"><strong>Valve:</strong> ${s.valve_id_name||s.valve_id||'-'}</div>
                    <div class="col-6"><strong>Baterai (Jenis):</strong> ${s.jenis_baterai||'-'}</div>
                    <div class="col-6"><strong>Attachment (Tipe):</strong> ${s.attachment_tipe||'-'}</div>
                    <div class="col-6"><strong>Roda:</strong> ${s.roda_id_name||s.roda_id||'-'}</div>
                    <div class="col-6"><strong>Departemen:</strong> ${s.departemen_id_name||s.departemen_id||'-'}</div>
                    <div class="col-6"><strong>Kapasitas:</strong> ${s.kapasitas_id_name||s.kapasitas_id||'-'}</div>
                    <div class="col-6"><strong>Mast:</strong> ${s.mast_id_name||s.mast_id||'-'}</div>
                    <div class="col-6"><strong>Ban:</strong> ${s.ban_id_name||s.ban_id||'-'}</div>
                    <div class="col-12"><strong>Aksesoris:</strong> ${aks.join(', ')||'-'}</div>
                    <div class="col-12"><hr></div>
            <div class="col-12"><strong>Item Terpilih:</strong></div>
					<div class="col-12" id="svcUnitDetailBlock">${(s.selected && s.selected.unit)?'<div class="text-muted">Memuat detail unit...</div>':'<div class="text-muted">Unit: -</div>'}</div>
					${(s.selected && s.selected.attachment) ? (()=>{ const a=s.selected.attachment; return `<div class=\"col-12\"><div><strong>Attachment:</strong> ${a.tipe||'-'} ${a.merk||''} ${a.model||''}${a.sn_attachment?` [SN: ${a.sn_attachment}]`:''}${a.lokasi_penyimpanan?` @ ${a.lokasi_penyimpanan}`:''}</div></div>`; })() : ''}
				</div>`;
			// Load full unit detail if selected
			if (s.selected && s.selected.unit && s.selected.unit.id) {
				const esc = (str)=>{ if(str===null||str===undefined||str==='') return '-'; return String(str).replaceAll('<','&lt;').replaceAll('>','&gt;'); };
				fetch(`<?= base_url('warehouse/inventory/get-unit-full-detail/') ?>${s.selected.unit.id}`).then(r=>r.json()).then(resp=>{
					const host = document.getElementById('svcUnitDetailBlock');
					if(!host) return;
					if(!(resp && resp.success && resp.data)){ host.innerHTML = '<div class="text-danger">Gagal memuat detail unit</div>'; return; }
					const data = resp.data;
					host.innerHTML = `
					<div class="row g-2">
                        <div class="col-6"><strong>ID Unit</strong>: ${esc(data.id_inventory_unit)}</div>
                        <div class="col-6"><strong>Serial Number</strong>: ${esc(data.serial_number_po)}</div>
                        <div class="col-6"><strong>Merk</strong>: ${esc(data.merk_unit)}</div>
                        <div class="col-6"><strong>Model</strong>: ${esc(data.model_unit)}</div>
                        <div class="col-6"><strong>Jenis Unit</strong>: ${esc(data.nama_departemen)}</div>
                        <div class="col-6"><strong>Tipe Unit</strong>: ${esc(data.nama_tipe_unit)}</div>
                        <div class="col-6"><strong>Tahun</strong>: ${esc(data.tahun_po)}</div>
                        <div class="col-6"><strong>Kapasitas</strong>: ${esc(data.kapasitas_unit)}</div>
                        <div class="col-6"><strong>Tanggal Masuk</strong>: ${esc(data.tanggal_masuk)}</div>
                        <div class="col-12"><hr></div>
                        <div class="col-6"><strong>Attachment</strong>: ${esc(data.attachment_tipe || '-')}</div>
                        <div class="col-6"><strong>SN Attachment</strong>: ${esc(data.sn_attachment_po)}</div>
                        <div class="col-6"><strong>Mast</strong>: ${esc(data.tipe_mast)}</div>
                        <div class="col-6"><strong>SN Mast</strong>: ${esc(data.sn_mast_po)}</div>
                        <div class="col-6"><strong>Mesin</strong>: ${esc((data.merk_mesin||'-') + ' ' + (data.model_mesin||''))}</div>
                        <div class="col-6"><strong>SN Mesin</strong>: ${esc(data.sn_mesin_po)}</div>
                        <div class="col-6"><strong>Baterai</strong>: ${esc(data.tipe_baterai)}</div>
                        <div class="col-6"><strong>SN Baterai</strong>: ${esc(data.sn_baterai_po)}</div>
                        <div class="col-6"><strong>Charger</strong>: ${esc(data.tipe_charger)}</div>
                        <div class="col-6"><strong>SN Charger</strong>: ${esc(data.sn_charger_po)}</div>
                        <div class="col-6"><strong>Ban</strong>: ${esc(data.tipe_ban)}</div>
                        <div class="col-6"><strong>Roda</strong>: ${esc(data.tipe_roda)}</div>
                        <div class="col-6"><strong>Valve</strong>: ${esc(data.jumlah_valve)}</div>
                        <div class="col-6"><strong>Aksesoris</strong>: ${esc(data.aksesoris_unit)}</div>
                    </div>
                    <div class="col-12"><hr></div>
                    <div class="col-12"><strong>Catatan:</strong> ${esc(data.catatan_unit)}</div>
                    <div class="col-12"><hr></div>
                    </div>`;
				});
			}
            new bootstrap.Modal(document.getElementById('spkDetailModal')).show();
        });
    }
    </script>
    <style>
    /* Ensure the SPK modal body scrolls when content is long */
    #spkModal .modal-body { max-height: 70vh; overflow-y: auto; }
    </style>
</div>
<?= $this->endSection() ?>
