<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-3">
    <h4 class="fw-bold mb-1">
        <i class="bi bi-box-seam me-2 text-primary"></i>
        Service Units Database
    </h4>
    <p class="text-muted mb-0">Complete list of units available for service operations and maintenance</p>
</div>

<!-- Header & Status Tabs / Controls -->
<div class="card shadow-business mb-3">
        <div class="card-body p-lg">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between gap-3">
                        <div class="flex-grow-1">
                                <ul class="nav nav-tabs" id="svcStatusTabs">
                                        <li class="nav-item"><button class="nav-link active" data-status="" type="button">Semua <span class="badge badge-soft-gray ms-1" id="svc-count-all">0</span></button></li>
                                        <li class="nav-item"><button class="nav-link" data-status="7" type="button">Stock Aset <span class="badge badge-soft-green ms-1" id="svc-count-7">0</span></button></li>
                                        <li class="nav-item"><button class="nav-link" data-status="8" type="button">Non Aset <span class="badge badge-soft-green ms-1" id="svc-count-8">0</span></button></li>
                                        <li class="nav-item"><button class="nav-link" data-status="3" type="button">Rental <span class="badge badge-soft-yellow ms-1" id="svc-count-3">0</span></button></li>
                                        <li class="nav-item"><button class="nav-link" data-status="2" type="button">Workshop <span class="badge badge-soft-red ms-1" id="svc-count-2">0</span></button></li>
                                </ul>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#svcFilterCollapse" title="Tampilkan / Sembunyikan Filter"><i class="fas fa-filter me-1"></i>Filter</button>
                            <button class="btn btn-sm btn-success" id="svcExport" title="Export CSV"><i class="fas fa-file-export me-1"></i>Export</button>
                        </div>
                </div>
                <div class="mt-3">
                <div class="input-group input-group-sm shadow-sm" style="max-width:320px;">
                    <span class="input-group-text border-end-0"><i class="fas fa-search text-secondary"></i></span>
                    <input type="text" id="svcSearch" class="form-control border-start-0" placeholder="Cari Unit / Serial / Merk / Model / Lokasi" aria-label="Pencarian">
                    <button class="btn btn-outline-secondary" id="svcClear" title="Clear"><i class="fas fa-times"></i></button>
                </div>
        </div>
</div>

<div class="collapse mb-3" id="svcFilterCollapse">
        <div class="card border-0 bg-light p-3 shadow-sm">
                <form id="svcFilterForm" class="row gx-3 gy-2 align-items-end">
                        <div class="col-md-4 col-sm-6">
                                <label class="form-label small mb-1 text-uppercase fw-semibold">Departemen</label>
                                <select id="svcDept" class="form-select form-select-sm">
                                        <option value="" selected>Semua Departemen</option>
                                        <?php if(!empty($departemen_options)): foreach($departemen_options as $d): ?>
                                                <option value="<?= esc($d['id_departemen']) ?>"><?= esc($d['nama_departemen']) ?></option>
                                        <?php endforeach; endif; ?>
                                </select>
                        </div>
                        <div class="col-md-4 col-sm-6">
                                <label class="form-label small mb-1 text-uppercase fw-semibold">Lokasi</label>
                                <select id="svcLokasi" class="form-select form-select-sm">
                                        <option value="" selected>Semua Lokasi</option>
                                        <?php if(!empty($lokasi_options)): foreach($lokasi_options as $l): ?>
                                                <option value="<?= esc($l) ?>"><?= esc($l) ?></option>
                                        <?php endforeach; endif; ?>
                                </select>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Terapkan</button>
                                <button type="button" id="svcReset" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo me-1"></i>Reset</button>
                        </div>
                </form>
        </div>
</div>

<div class="card shadow-sm">
        <div class="card-body p-2">
                <div class="table-responsive">
                        <table id="service-units-table" class="table table-sm table-hover align-middle mb-0 w-100">
                                <thead>
                                        <tr class="small text-uppercase">
                                                <th>No Unit</th>
                                                <th>Serial</th>
                                                <th>Merk / Model</th>
                                                <th>Tipe</th>
                                                <th>Kapasitas</th>
                                                <th>Status</th>
                                                <th>Lokasi</th>
                                                <th>Jenis Unit</th>
                                                <th class="text-center">Aksi</th>
                                        </tr>
                                </thead>
                        </table>
                </div>
        </div>
</div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="svcDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Detail Unit</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="svcDetailBody"><div class="text-center text-muted py-5">Memuat...</div></div>
                        <div class="modal-footer justify-content-between">
                                <div class="small text-muted" id="svcDetailMeta"></div>
                                <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm" type="button" id="svcEditFromDetail"><i class="fas fa-edit me-1"></i>Edit</button>
                                        <a id="svcWorkOrderBtn" class="btn btn-sm btn-warning" href="#"><i class="fas fa-wrench me-1"></i>Work Order</a>
                                        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                </div>
                        </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="svcEditModal" tabindex="-1" aria-labelledby="svcEditModalLabel" aria-hidden="true"> 
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="svcEditModalLabel">Edit Detail Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="svcEditForm" aria-label="Form Edit Unit" class="needs-validation" novalidate>
                <div class="modal-body bg-light">
                    <input type="hidden" name="id" id="svcEditId">

                    <h6 class="text-muted">Informasi Dasar & Status</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="svcEditNoUnit" class="form-label">No. Unit</label>
                            <input type="text" class="form-control form-control-sm" id="svcEditNoUnit" disabled readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="svcEditStatus" class="form-label">Status Unit <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="status_unit_id" id="svcEditStatus" required>
                                <option value="" selected disabled>Pilih Status...</option>
                                <?php if(!empty($status_options)): foreach($status_options as $s): ?>
                                    <option value="<?= esc($s['id']) ?>"><?= esc(strtoupper($s['name'])) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="svcEditArea" class="form-label">Area <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="area_id" id="svcEditArea" required>
                                <option value="" selected disabled>Pilih Area...</option>
                            </select>
                            <div class="form-text">Area menentukan penugasan staff untuk work order</div>
                        </div>
                        <div class="col-md-4">
                            <label for="svcEditTahun" class="form-label">Tahun Pembuatan</label>
                            <input type="number" class="form-control form-control-sm" name="tahun_unit" id="svcEditTahun" placeholder="Contoh: 2023">
                        </div>
                        <div class="col-md-6">
                            <label for="svcEditDept" class="form-label">Departemen</label>
                            <select class="form-select form-select-sm" name="departemen_id" id="svcEditDept">
                                <option value="" selected disabled>Pilih Departemen...</option>
                                <?php if(!empty($departemen_options)): foreach($departemen_options as $d): ?>
                                    <option value="<?= esc($d['id_departemen']) ?>"><?= esc($d['nama_departemen']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="svcEditLokasi" class="form-label">Lokasi Unit</label>
                            <input type="text" class="form-control form-control-sm" name="lokasi_unit" id="svcEditLokasi" placeholder="Lokasi terkini unit">
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Spesifikasi Unit -->
                    <div class="card border-0 bg-light mb-3">
                        <!-- <div class="card-header bg-primary text-black py-2">
                            <h6 class="mb-0">Spesifikasi Unit</h6>
                        </div> -->
                        <div class="card-body py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="svcEditModel" class="form-label fw-semibold">Model Unit</label>
                                    <select class="form-select form-select-sm" name="model_unit_id" id="svcEditModel">
                                        <option value="" selected disabled>Pilih Model...</option>
                                        <?php if(!empty($model_unit_options)): foreach($model_unit_options as $m): ?>
                                            <option value="<?= esc($m['id']) ?>"><?= esc($m['name']) ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="svcEditSerial" class="form-label fw-semibold">Serial Number Unit</label>
                                    <input type="text" class="form-control form-control-sm" name="serial_number" id="svcEditSerial" placeholder="S/N Unit">
                                </div>
                                <div class="col-md-6">
                                    <label for="svcEditTipe" class="form-label fw-semibold">Tipe/Jenis</label>
                                    <select class="form-select form-select-sm" name="tipe_unit_id" id="svcEditTipe">
                                        <option value="" selected disabled>Pilih Tipe...</option>
                                        <?php if(!empty($tipe_unit_options)): foreach($tipe_unit_options as $t): ?>
                                            <option value="<?= esc($t['id']) ?>"><?= esc($t['name']) ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="svcEditKapasitas" class="form-label fw-semibold">Kapasitas</label>
                                    <select class="form-select form-select-sm" name="kapasitas_unit_id" id="svcEditKapasitas">
                                        <option value="" selected disabled>Pilih Kapasitas...</option>
                                        <?php if(!empty($kapasitas_options)): foreach($kapasitas_options as $k): ?>
                                            <option value="<?= esc($k['id']) ?>"><?= esc($k['name']) ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Komponen Utama -->
                    <div class="card border-0 bg-light mb-3">
                        <!-- <div class="card-header bg-success text-black py-2">
                            <h6 class="mb-0">Komponen Utama</h6>
                        </div> -->
                        <div class="card-body py-3">
                            <!-- Mast -->
                            <div class="border rounded p-3 mb-3 ">
                                <h6 class="text-primary mb-2">Mast</h6>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label for="svcEditMast" class="form-label small">Model Mast</label>
                                        <select class="form-select form-select-sm" name="model_mast_id" id="svcEditMast">
                                            <option value="" selected disabled>Pilih Mast...</option>
                                            <?php if(!empty($mast_options)): foreach($mast_options as $mm): ?>
                                                <option value="<?= esc($mm['id']) ?>"><?= esc($mm['name']) ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="svcEditSnMast" class="form-label small">Serial Number</label>
                                        <input type="text" class="form-control form-control-sm" name="sn_mast" id="svcEditSnMast" placeholder="S/N Mast">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="svcEditTinggiMast" class="form-label small">Tinggi Mast</label>
                                        <input type="text" class="form-control form-control-sm" name="tinggi_mast" id="svcEditTinggiMast" placeholder="ex: 4500mm">
                                    </div>
                                </div>
                            </div>

                            <!-- Mesin -->
                            <div class="border rounded p-3 mb-3 ">
                                <h6 class="text-primary mb-2">Mesin</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="svcEditMesin" class="form-label small">Model Mesin</label>
                                        <select class="form-select form-select-sm" name="model_mesin_id" id="svcEditMesin">
                                            <option value="" selected disabled>Pilih Mesin...</option>
                                            <?php if(!empty($mesin_options)): foreach($mesin_options as $ms): ?>
                                                <option value="<?= esc($ms['id']) ?>"><?= esc($ms['name']) ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="svcEditSnMesin" class="form-label small">Serial Number</label>
                                        <input type="text" class="form-control form-control-sm" name="sn_mesin" id="svcEditSnMesin" placeholder="S/N Mesin">
                                    </div>
                                </div>
                            </div>

                            <!-- Baterai -->
                            <div class="border rounded p-3 mb-3 ">
                                <h6 class="text-primary mb-2">Baterai</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="svcEditBaterai" class="form-label small">Model Baterai</label>
                                        <select class="form-select form-select-sm" name="model_baterai_id" id="svcEditBaterai">
                                            <option value="" selected disabled>Pilih Baterai...</option>
                                            <?php if(!empty($baterai_options)): foreach($baterai_options as $bt): ?>
                                                <option value="<?= esc($bt['id']) ?>"><?= esc($bt['name']) ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="svcEditSnBaterai" class="form-label small">Serial Number</label>
                                        <input type="text" class="form-control form-control-sm" name="sn_baterai" id="svcEditSnBaterai" placeholder="S/N Baterai">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Komponen Tambahan -->
                    <div class="card border-0 bg-light mb-3">
                        <!-- <div class="card-header bg-info text-black py-2">
                            <h6 class="mb-0">Komponen Tambahan</h6>
                        </div> -->
                        <div class="card-body py-3">
                            <!-- Attachment -->
                            <div class="border rounded p-3 mb-3 ">
                                <h6 class="text-primary mb-2">Attachment</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="svcEditAttachment" class="form-label small">Model Attachment</label>
                                        <select class="form-select form-select-sm" name="model_attachment_id" id="svcEditAttachment">
                                            <option value="" selected disabled>Pilih Attachment...</option>
                                            <?php if(!empty($attachment_options)): foreach($attachment_options as $at): ?>
                                                <option value="<?= esc($at['id']) ?>"><?= esc($at['name']) ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="svcEditSnAttachment" class="form-label small">Serial Number</label>
                                        <input type="text" class="form-control form-control-sm" name="sn_attachment" id="svcEditSnAttachment" placeholder="S/N Attachment">
                                    </div>
                                </div>
                            </div>

                            <!-- Charger -->
                            <div class="border rounded p-3 mb-3 ">
                                <h6 class="text-primary mb-2">Charger</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="svcEditCharger" class="form-label small">Model Charger</label>
                                        <select class="form-select form-select-sm" name="model_charger_id" id="svcEditCharger">
                                            <option value="" selected disabled>Pilih Charger...</option>
                                            <?php if(!empty($charger_options)): foreach($charger_options as $cg): ?>
                                                <option value="<?= esc($cg['id']) ?>"><?= esc($cg['name']) ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="svcEditSnCharger" class="form-label small">Serial Number</label>
                                        <input type="text" class="form-control form-control-sm" name="sn_charger" id="svcEditSnCharger" placeholder="S/N Charger">
                                    </div>
                                </div>
                            </div>

                            <!-- Valve -->
                            <div class="border rounded p-3 ">
                                <h6 class="text-primary mb-2">Valve</h6>
                                <div class="col-md-6">
                                    <label for="svcEditValve" class="form-label small">Tipe Valve</label>
                                    <select class="form-select form-select-sm" name="valve_id" id="svcEditValve">
                                        <option value="" selected disabled>Pilih Valve...</option>
                                        <?php if(!empty($valve_options)): foreach($valve_options as $vl): ?>
                                            <option value="<?= esc($vl['id']) ?>"><?= esc($vl['name']) ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="card border-0 bg-light">
                        <!-- <div class="card-header bg-secondary text-black py-2">
                            <h6 class="mb-0">Catatan</h6>
                        </div> -->
                        <div class="card-body py-3">
                            <label for="svcEditKet" class="form-label fw-semibold">Keterangan</label>
                            <textarea class="form-control form-control-sm" rows="3" name="keterangan" id="svcEditKet" placeholder="Tambahkan catatan atau keterangan lain jika perlu..."></textarea>
                        </div>
                    </div>

                    <div class="alert alert-danger d-none py-2 px-3 mt-3 small" id="svcEditErr"></div>
                    <div class="alert alert-success d-none py-2 px-3 mt-3 small" id="svcEditOk"></div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('javascript') ?>
<script>
let svcTable;
let currentTabStatus = '';
let lastSearchValue='';
function svcStatusBadge(id,name){
        id=parseInt(id||0); name=(name||'').toUpperCase();
        let cls='secondary';
        if(id===7||name.includes('STOCK ASET')) cls='success';
        else if(id===8) cls='success';
        else if(id===3) cls='warning';
        else if(id===2) cls='danger';
        const softMap = { success: 'badge-soft-green', warning: 'badge-soft-yellow', danger: 'badge-soft-red', info: 'badge-soft-cyan', primary: 'badge-soft-blue' };
        return `<span class="badge ${softMap[cls] || 'badge-soft-gray'}">${name||'-'}</span>`;
}
function svcActions(id){
        return `<div class="dropdown">`
                + `<button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class='fas fa-ellipsis-h'></i></button>`
                + `<ul class='dropdown-menu'>`
                + `<li><a class='dropdown-item' href='#' onclick='svcView(${id})'><i class="fas fa-eye me-2 text-info"></i>Lihat</a></li>`
                + `<li><a class='dropdown-item' href='#' onclick='svcEdit(${id})'><i class="fas fa-edit me-2 text-primary"></i>Edit</a></li>`
                + `<li><a class='dropdown-item' href='<?= base_url('service/work-orders') ?>?unit=${id}'><i class="fas fa-wrench me-2 text-warning"></i>Work Order</a></li>`
                + `</ul></div>`;
}
// Load area options for edit form
function loadAreaOptions() {
    fetch('<?= base_url('service/areas') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const areaSelect = document.getElementById('svcEditArea');
                if (areaSelect) {
                    areaSelect.innerHTML = '<option value="">Pilih Area...</option>';
                    data.data.forEach(area => {
                        const option = document.createElement('option');
                        option.value = area.id;
                        option.textContent = `${area.area_code} - ${area.area_name}`;
                        areaSelect.appendChild(option);
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error loading areas:', error);
        });
}

function initSvc(){
        svcTable = $('#service-units-table').DataTable({
                processing:true, serverSide:true, pageLength:25,
                ajax:{
                        url:'<?= base_url('service/data-unit/data') ?>', type:'POST',
                        data:function(d){
                                d.status = currentTabStatus; // '' means all
                                const deptVal = $('#svcDept').val(); if(deptVal) d.departemen_id = deptVal; // single-select now
                                const lokasiVal = $('#svcLokasi').val(); if(lokasiVal) d.lokasi_unit = lokasiVal;
                                d['<?= csrf_token() ?>'] = $('meta[name="csrf-token"]').attr('content');
                        },
                                        dataSrc:function(j){
                                                if(j?.csrf_hash){ $('meta[name="csrf-token"]').attr('content', j.csrf_hash);} 
                                                if(j?.stats?.status_counts){
                                                        const sc=j.stats.status_counts;
                                                        $('#svc-count-7').text(sc[7]||0);
                                                        $('#svc-count-8').text(sc[8]||0);
                                                        $('#svc-count-3').text(sc[3]||0);
                                                        $('#svc-count-2').text(sc[2]||0);
                                                        const total=(sc[7]||0)+(sc[8]||0)+(sc[3]||0)+(sc[2]||0);
                                                        $('#svc-count-all').text(total);
                                                }
                                                updateActiveFilterInfo();
                                                return j.data||[]; 
                                        },
                                error:function(xhr){
                                        let msg = 'Ajax error';
                                        try { msg = xhr.responseJSON?.error || xhr.status+' '+xhr.statusText; } catch(e){}
                                        $('#service-units-table').after('<div class="alert alert-danger mt-2 p-2 small">'+msg+' (lihat console)</div>');
                                        console.error('Service DataTable error:', xhr.responseText);
                                }
                },
                order:[[0,'asc']],
                columns:[
                        {data:'no_unit', render:d=> d?`<strong>${d}</strong>`:'-'},
                        {data:'serial_number'},
                        {data:null, render:r=>`<div class='small'><span class='fw-semibold'>${r.merk_unit||'-'}</span><br><span class='text-muted'>${r.model_unit||''}</span></div>`},
                        {data:'tipe_full'},
                        {data:'kapasitas_unit'},
                        {data:null, render:r=>svcStatusBadge(r.status_unit_id,r.status_unit_name)},
                        {data:'lokasi_unit'},
                        {data:'nama_departemen'},
                        {data:null, orderable:false, searchable:false, render:r=>svcActions(r.id)}
                ],
                dom:'rtip'
        });
        // Search
                let tmr; $('#svcSearch').on('input', function(){ clearTimeout(tmr); const v=this.value; tmr=setTimeout(()=>{ lastSearchValue=v; svcTable.search(v).draw(); },300); });
        $('#svcClear').on('click', function(){ $('#svcSearch').val(''); svcTable.search('').draw(); });
                $('#svcFilterForm').on('submit', e=>{ e.preventDefault(); svcTable.ajax.reload(null,false); });
                $('#svcReset').on('click', function(){
                        currentTabStatus='';
                        // no status dropdown now
                        $('#svcDept').val('');
                        $('#svcLokasi').val('');
                        $('#svcSearch').val(''); lastSearchValue='';
                        $('#svcStatusTabs .nav-link').removeClass('active'); $('#svcStatusTabs .nav-link[data-status=""]').addClass('active');
                        svcTable.search('').draw();
                        svcTable.ajax.reload(null,false);
                });
                // Auto reload when departemen or lokasi changed
                $('#svcDept, #svcLokasi').on('change', function(){ svcTable.ajax.reload(null,false); });
                $('#svcStatusTabs .nav-link').on('click', function(){
                        $('#svcStatusTabs .nav-link').removeClass('active');
                        $(this).addClass('active');
                        currentTabStatus = $(this).data('status');
                        // no status dropdown toggle
                        svcTable.ajax.reload();
                });
                // removed status dropdown listener
                updateActiveFilterInfo();
}
let svcLastDetail = null;
function svcView(id){
        $('#svcDetailBody').html('<div class="text-center text-muted py-5">Memuat...</div>');
        $('#svcDetailModal').modal('show');
        fetch('<?= base_url('service/data-unit/detail') ?>/'+id)
            .then(r=>r.json())
            .then(j=>{
                if(!j.success){ $('#svcDetailBody').html('<div class="text-danger">Gagal memuat</div>'); return; }
                const d=j.data; svcLastDetail=d;
                $('#svcWorkOrderBtn').attr('href','<?= base_url('service/work-orders') ?>?unit='+d.id_inventory_unit);
                                $('#svcDetailBody').html(`
                                        <div class='row g-3'>
                        <div class='col-md-6'>
                            <h6 class='fw-bold mb-2'>Informasi Unit</h6>
                            <table class='table table-sm mb-0'>
                                <tr><td class='small text-muted'>No Unit</td><td><strong>${d.no_unit||'-'}</strong></td></tr>
                                <tr><td class='small text-muted'>Serial Purchasing</td><td>${d.serial_number_po||'-'}</td></tr>
                                <tr><td class='small text-muted'>Merk / Model</td><td>${d.merk_unit||'-'} ${d.model_unit||''}</td></tr>
                                <tr><td class='small text-muted'>Tipe / Jenis</td><td>${d.nama_tipe_unit||'-'}</td></tr>
                                <tr><td class='small text-muted'>Kapasitas</td><td>${d.kapasitas_unit||'-'}</td></tr>
                                <tr><td class='small text-muted'>Tahun</td><td>${d.tahun_po||'-'}</td></tr>
                                <tr><td class='small text-muted'>Keterangan</td><td>${d.keterangan||'-'}</td></tr>
                            </table>
                        </div>
                        <div class='col-md-6'>
                            <h6 class='fw-bold mb-2'>Spesifikasi Teknis</h6>
                            <table class='table table-sm mb-0'>
                                <tr><td class='small text-muted'>Mast</td><td>${d.tipe_mast||'-'} (${d.sn_mast_po||'-'})</td></tr>
                                <tr><td class='small text-muted'>Mesin</td><td>${(d.merk_mesin||'-')} ${(d.model_mesin||'')} SN: ${(d.sn_mesin_po||'-')}</td></tr>
                                <tr><td class='small text-muted'>Baterai</td><td>${(d.merk_baterai||'-')} ${(d.tipe_baterai||'')} SN: ${(d.sn_baterai_po||'-')}</td></tr>
                                <tr><td class='small text-muted'>Ban</td><td>${d.tipe_ban||'-'}</td></tr>
                                <tr><td class='small text-muted'>Roda</td><td>${d.tipe_roda||'-'}</td></tr>
                                <tr><td class='small text-muted'>Valve</td><td>${d.jumlah_valve||'-'}</td></tr>
                            </table>
                        </div>
                        <div class='col-12'>
                            <h6 class='fw-bold mt-3 mb-2'>Status</h6>
                                                        <div class='row small'>
                                                                <div class='col-md-4'><strong>Status:</strong> ${d.status_unit_name||'-'}</div>
                                                                <div class='col-md-4'><strong>Departemen:</strong> ${d.nama_departemen||'-'}</div>
                                                                <div class='col-md-4'><strong>Lokasi:</strong> ${d.lokasi_unit||'-'}</div>
                                                        </div>
                                                        <div class='small text-muted mt-2'>Verifikasi: ${d.status_verifikasi||'-'} • ${d.catatan_verifikasi||''}</div>
                                                </div>
                                                <div class='col-12 mt-3'>
                                                        <h6 class='fw-bold mb-2'>Riwayat Maintenance (Ringkas)</h6>
                                                        <div id='svcMaintHistory' class='small text-muted'>Memuat riwayat...</div>
                                                </div>
                    </div>`);
                                $('#svcDetailMeta').text('ID#'+d.id_inventory_unit+' • Dept '+(d.nama_departemen||'-'));
                                $('#svcEditFromDetail').off('click').on('click', ()=>{ $('#svcDetailModal').modal('hide'); svcEdit(d.id_inventory_unit); });
                                // load maintenance history
                                fetch('<?= base_url('service/data-unit/maintenance-history') ?>/'+id)
                                   .then(r=>r.json())
                                   .then(h=>{
                                           if(!h.success){ $('#svcMaintHistory').html('<span class="text-danger">Gagal memuat riwayat</span>'); return; }
                                           if(!h.data || !h.data.length){ $('#svcMaintHistory').html('<em>Tidak ada data</em>'); return; }
                                           let html='<table class="table table-sm mb-0"><thead><tr><th>Tanggal</th><th>Jenis</th><th>Catatan</th><th>DT(h)</th></tr></thead><tbody>';
                                           h.data.forEach(r=>{ html+=`<tr><td>${r.date}</td><td>${r.type}</td><td>${r.notes}</td><td>${r.downtime_hours}</td></tr>`; });
                                           html+='</tbody></table>';
                                           $('#svcMaintHistory').html(html);
                                   })
                                   .catch(()=> $('#svcMaintHistory').html('<span class="text-danger">Error</span>'));
            })
            .catch(()=> $('#svcDetailBody').html('<div class="text-danger">Error server</div>'));
}
function svcEdit(id){
        // If we already loaded detail, reuse some fields when IDs match
        const load = ()=>{
                $('#svcEditErr').addClass('d-none'); $('#svcEditOk').addClass('d-none');
                $('#svcEditId').val(id);
                $('#svcEditNoUnit').val('');
                $('#svcEditStatus').val('');
                $('#svcEditArea').val('');
                $('#svcEditLokasi').val('');
                $('#svcEditKet').val('');
                $('#svcEditModal').modal('show');
                        fetch('<?= base_url('service/data-unit/detail') ?>/'+id)
                        .then(r=>r.json())
                        .then(j=>{
                                if(!j.success){ $('#svcEditErr').removeClass('d-none').text(j.message||'Gagal memuat'); return; }
                                const d=j.data;
                                $('#svcEditNoUnit').val(d.no_unit||'');
                                            $('#svcEditStatus').val(d.status_unit||'');
                                            $('#svcEditArea').val(d.area_id||'');
                                            $('#svcEditDept').val(d.departemen_id||'');
                                            $('#svcEditModel').val(d.model_unit_id||'');
                                            $('#svcEditTipe').val(d.tipe_unit_id||'');
                                            $('#svcEditKapasitas').val(d.kapasitas_unit_id||'');
                                            $('#svcEditMast').val(d.model_mast_id||'');
                                            $('#svcEditMesin').val(d.model_mesin_id||'');
                                            $('#svcEditBaterai').val(d.model_baterai_id||'');
                                            $('#svcEditBan').val(d.ban_id||'');
                                            $('#svcEditRoda').val(d.roda_id||'');
                                            $('#svcEditValve').val(d.valve_id||'');
                                            $('#svcEditAttachment').val(d.model_attachment_id||'');
                                            $('#svcEditCharger').val(d.model_charger_id||'');
                                        $('#svcEditTahun').val(d.tahun_po||'');
                                        $('#svcEditSerial').val(d.serial_number_po||'');
                                        $('#svcEditLokasi').val(d.lokasi_unit||'');
                                        $('#svcEditSnMast').val(d.sn_mast_po||'');
                                        $('#svcEditSnMesin').val(d.sn_mesin_po||'');
                                        $('#svcEditSnBaterai').val(d.sn_baterai_po||'');
                                            $('#svcEditSnAttachment').val(d.sn_attachment_po||'');
                                            $('#svcEditSnCharger').val(d.sn_charger_po||'');
                                            $('#svcEditTinggiMast').val(d.tinggi_mast||'');
                                        $('#svcEditKet').val(d.keterangan||'');
                        })
                        .catch(()=> $('#svcEditErr').removeClass('d-none').text('Error server'));
        };
        load();
}

$('#svcEditForm').on('submit', function(e){
        e.preventDefault();
        const id=$('#svcEditId').val();
        const fd=new FormData(this);
        $('#svcEditErr').addClass('d-none'); $('#svcEditOk').addClass('d-none');
        fetch('<?= base_url('service/data-unit/update') ?>/'+id, {
                method:'POST',
                body:fd
        }).then(r=>r.json())
        .then(j=>{
                if(!j.success){ $('#svcEditErr').removeClass('d-none').text(j.message||'Gagal menyimpan'); return; }
                $('#svcEditOk').removeClass('d-none').text(j.message||'Tersimpan');
                svcTable.ajax.reload(null,false);
                setTimeout(()=>{ $('#svcEditModal').modal('hide'); if(svcLastDetail && svcLastDetail.id_inventory_unit==id){ svcView(id); } },700);
        })
        .catch(()=> $('#svcEditErr').removeClass('d-none').text('Error server'));
});

$(function(){ 
    initSvc(); 
    loadAreaOptions(); // Load area options on page load
});
// Export
$('#svcExport').on('click', function(){
        const params = new URLSearchParams();
        if(currentTabStatus!=='') params.append('status', currentTabStatus);
        const dept=$('#svcDept').val(); if(dept) params.append('departemen_id', dept);
        const lokasi=$('#svcLokasi').val(); if(lokasi) params.append('lokasi_unit', lokasi);
        const q=$('#svcSearch').val(); if(q) params.append('q', q);
        window.location='<?= base_url('service/data-unit/export') ?>'+(params.toString()?'?'+params.toString():'');
});

function updateActiveFilterInfo(){
        const chips=[];
        if(currentTabStatus!==''){
                const map={7:'STOCK ASET',8:'NON ASET',3:'RENTAL',2:'WORKSHOP-RUSAK'};
                chips.push(`<span class="badge rounded-pill text-bg-primary">${map[currentTabStatus]||('Status '+currentTabStatus)}</span>`);
        }
        if($('#svcDept').val()){
                chips.push(`<span class="badge rounded-pill text-bg-info">${$('#svcDept option:selected').text()}</span>`);
        }
        if($('#svcLokasi').val()){
                chips.push(`<span class="badge rounded-pill text-bg-secondary">Lokasi ${$('#svcLokasi option:selected').text()}</span>`);
        }
        if(lastSearchValue){
                chips.push(`<span class="badge rounded-pill text-bg-dark"><i class="fas fa-search me-1"></i>${lastSearchValue}</span>`);
        }
        if(!chips.length){
                $('#svcActiveFilterInfo').html('Semua Unit');
                $('#svcQuickReset').hide();
        } else {
                $('#svcActiveFilterInfo').html(chips.join(' '));
                $('#svcQuickReset').show();
        }
}
// Quick reset from filter bar
$('#svcQuickReset').on('click', ()=>$('#svcReset').trigger('click'));
</script>
<?= $this->endSection() ?> 
