<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    .table-card { border: none; border-radius: 15px; box-shadow: 0 4px 25px rgba(0,0,0,0.1); }
    .card-stats { 
        border: none; 
        border-radius: 15px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    .card-stats.active {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        border: 2px solid #0d6efd;
    }
    /* Konsistensi kolom aksi seperti halaman lain */
    #inventory-unit-table th:last-child, #inventory-unit-table td:last-child { width: 55px; text-align: center; }
    #inventory-unit-table .dropdown-toggle { padding: 2px 6px; }
    #inventory-unit-table .dropdown-menu { font-size: 0.8rem; }
    /* Sticky header & hover */
    #inventory-unit-table thead th { position: sticky; top: 0; background: #f8f9fa; z-index: 5; }
    #inventory-unit-table tbody tr:hover { background: #eef6ff !important; }
    .badge { font-weight:500; }
    /* Responsive hide columns */
    @media (max-width: 992px){
        #inventory-unit-table td:nth-child(5), #inventory-unit-table th:nth-child(5),
        #inventory-unit-table td:nth-child(6), #inventory-unit-table th:nth-child(6),
        #inventory-unit-table td:nth-child(7), #inventory-unit-table th:nth-child(7){ display:none; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Interactive Filter Tabs (mirip permissions) & Toggle Advanced Filters -->
    <div class="card table-card">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <ul class="nav nav-tabs flex-grow-1" id="unitStatusTabs">
            <li class="nav-item"><button class="nav-link active" data-status="" type="button"><i class="fas fa-list me-1"></i>Semua <span class="badge bg-secondary ms-1" id="count-all"><?= $stats['total'] ?? 0 ?></span></button></li>
            <li class="nav-item"><button class="nav-link" data-status="7" type="button"><i class="fas fa-box me-1"></i>Stock Aset <span class="badge bg-success ms-1" id="count-stock"><?= $stats['in_stock'] ?? 0 ?></span></button></li>
            <li class="nav-item"><button class="nav-link" data-status="8" type="button"><i class="fas fa-archive me-1"></i>Stok Non Aset <span class="badge bg-secondary ms-1" id="count-nonasset"><?= $stats['non_asset'] ?? 0 ?></span></button></li>
            <li class="nav-item"><button class="nav-link" data-status="3" type="button"><i class="fas fa-handshake me-1"></i>Rental <span class="badge bg-warning text-dark ms-1" id="count-rental"><?= $stats['rented'] ?? 0 ?></span></button></li>
            <li class="nav-item"><button class="nav-link" data-status="9" type="button"><i class="fas fa-shopping-cart me-1"></i>Jual <span class="badge bg-info text-dark ms-1" id="count-sold"><?= $stats['sold'] ?? 0 ?></span></button></li>
            <!-- <li class="nav-item"><button class="nav-link" data-status="2" type="button"><i class="fas fa-tools me-1"></i>Workshop <span class="badge bg-danger ms-1" id="count-workshop"><?= $stats['workshop'] ?? 0 ?></span></button></li> -->
        </ul>
    </div>

    <!-- Inventory Table -->
    <div class="card table-card">
        <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <h5 class="card-title fw-bold m-0">Daftar Stok Unit</h5>
            <div class="d-flex gap-2 ms-auto">
                <a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="false" aria-controls="filterCollapse" title="Tampilkan / Sembunyikan Filter">
                    <i class="fas fa-filter me-1"></i>Filter
                </a>
                <a href="<?= base_url('warehouse/inventory/export-invent-unit') ?>" class="btn btn-sm btn-success" id="btnExport" title="Export CSV">
                    <i class="fas fa-file-export me-1"></i>Export
                </a>
            </div>
        </div>

        <div class="collapse" id="filterCollapse">
            <div class="card-body bg-light-subtle border-top">
                <form id="filterForm" class="row gx-3 gy-2 align-items-end">
                    <div class="col-md-4 col-sm-12">
                        <label for="filter_departemen" class="form-label">Departemen</label>
                        <select id="filter_departemen" class="form-select">
                            <option value="" selected>Semua Departemen</option>
                            <?php if(!empty($departemen_options)): foreach($departemen_options as $d): ?>
                                <option value="<?= esc($d['id_departemen']) ?>"><?= esc($d['nama_departemen']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <label for="filter_lokasi" class="form-label">Lokasi</label>
                        <select id="filter_lokasi" class="form-select">
                            <option value="" selected>Semua Lokasi</option>
                            <?php if(!empty($lokasi_options)): foreach($lokasi_options as $l): ?>
                                <option value="<?= esc($l) ?>"><?= esc($l) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="col-md-4 col-sm-12 d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="fas fa-check me-1"></i> Terapkan
                        </button>
                        <button type="button" id="btnResetFilter" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body pt-2">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <div class="input-group input-group-sm" style="max-width:340px;">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-secondary"></i></span>
                    <input type="text" id="unitSearch" class="form-control" placeholder="Cari Serial / Lokasi / Merk / Model..." autocomplete="off">
                    <button class="btn btn-outline-secondary" type="button" id="btnClearSearch" title="Bersihkan pencarian"><i class="fas fa-times"></i></button>
                </div>
                <div class="small text-muted" id="activeFilterInfo"></div>
            </div>
            <table id="inventory-unit-table" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>No. Unit</th>
                        <th>Serial Number</th>
                        <th>Merk</th>
                        <th>Model</th>
                        <th>Tipe</th>
                        <th>Departemen</th>
                        <th>Status</th>
                        <th>Lokasi</th>
                        <th>Tanggal Masuk</th>
                        <th>Aksi</th>
                        <th>Konfirmasi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal View Unit Detail -->
<div class="modal fade" id="viewUnitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="unitDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Stok Unit -->
<div class="modal fade" id="editUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Stok Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUnitForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id_inventory_unit">
                    <div class="mb-3">
                        <label class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="edit_serial_number" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Merk</label>
                        <input type="text" class="form-control" id="edit_merk" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status_unit" class="form-label">Status Unit</label>
                        <select class="form-select" id="edit_status_unit" name="status_unit" required>
                            <option value="7">STOCK ASET</option>
                            <option value="3">RENTAL</option>
                            <option value="9">JUAL</option>
                            <option value="2">WORKSHOP-RUSAK</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lokasi" class="form-label">Lokasi Unit</label>
                        <select class="form-select" id="edit_lokasi" name="lokasi_unit">
                            <option value="POS 1">POS 1</option>
                            <option value="POS 2">POS 2</option>
                            <option value="POS 3">POS 3</option>
                            <option value="POS 4">POS 4</option>
                            <option value="POS 5">POS 5</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentStatusFilter = '';
    let unitTable;
    let departemenFilter = '';
    let lokasiFilter = '';

    function updateDynamicBadges(stats){
        if(!stats) return;
        $('#count-all').text(stats.total ?? 0);
        $('#count-stock').text(stats.in_stock ?? 0);
        $('#count-rental').text(stats.rented ?? 0);
        $('#count-sold').text(stats.sold ?? 0);
        $('#count-workshop').text(stats.workshop ?? 0);
    }

    $(document).ready(function(){
        unitTable = $('#inventory-unit-table').DataTable({
            processing:true,
            serverSide:true,
            pageLength:25,
            order:[[8,'desc']], // index shifted after removing internal id column
            scrollX:true,
            deferRender:true,
            ajax:{
                url:'<?= base_url('warehouse/inventory/invent_unit') ?>',
                type:'POST',
                data:function(d){
                    d.status_unit = currentStatusFilter;
                    d.departemen_id = departemenFilter;
                    d.lokasi_unit = lokasiFilter;
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                },
                dataSrc:function(json){
                    if(json.csrf_hash){ $('meta[name="<?= csrf_token() ?>"]').attr('content', json.csrf_hash); }
                    if(json.stats){ updateDynamicBadges(json.stats); }
                    return json.data;
                },
                error:function(xhr){
                    Swal.fire('Error','Gagal memuat data (lihat console)','error');
                    console.error('DataTables Error', xhr.responseText);
                }
            },
            columns:[
                { data:'no_unit', render:function(data){ return data || '-'; } },
                { data:'serial_number_po', render:d=> d||'-' },
                { data:'merk_unit', render:d=> d||'-' },
                { data:'model_unit', render:d=> d||'-' },
                { data:'nama_tipe_unit', render:d=> d||'-' },
                { data:'nama_departemen', render:d=> d||'-' },
                { data:'status_unit_name', render:function(d){
                        if(!d) return '-';
                        const s=d.toUpperCase();
                        let cls='bg-secondary';
                        if(s.includes('STOCK')) cls='bg-success';
                        else if(s.includes('RENTAL')) cls='bg-warning';
                        else if(s.includes('JUAL')) cls='bg-info';
                        else if(s.includes('WORKSHOP')||s.includes('RUSAK')) cls='bg-danger';
                        return `<span class="badge ${cls}">${d}</span>`;
                    }
                },
                { data:'lokasi_unit', render:d=> d||'-' },
                { data:'tanggal_masuk', render:d=> d||'-' },
                { data:'id_inventory_unit', orderable:false, render:function(data,type,row){
                        const items=[];
                        items.push(`<li><a class=\"dropdown-item\" href=\"#\" onclick=\"viewUnit(${data})\"><i class=\"fas fa-eye me-2\"></i>Lihat Detail</a></li>`);
                        items.push(`<li><a class=\"dropdown-item\" href=\"#\" onclick=\"editUnit(${data})\"><i class=\"fas fa-edit me-2\"></i>Edit</a></li>`);
                        items.push('<li><hr class=\"dropdown-divider\"></li>');
                        items.push(`<li><a class=\"dropdown-item text-danger\" href=\"#\" onclick=\"deleteUnit(${data})\"><i class=\"fas fa-trash me-2\"></i>Hapus</a></li>`);
                        return `<div class=\"dropdown\"><button class=\"btn btn-sm btn-outline-secondary dropdown-toggle\" data-bs-toggle=\"dropdown\"><i class=\"fas fa-ellipsis-h\"></i></button><ul class=\"dropdown-menu dropdown-menu-end\">${items.join('')}<\/ul></div>`;
                    }
                },
                { data:'id_inventory_unit', orderable:false, render:function(data,type,row){
                        // Normalisasi status id bisa berupa string/number atau tidak dikirim
                        const rawId = row.status_unit_id !== undefined ? row.status_unit_id : row.status_unit;
                        const stId = rawId !== undefined && rawId !== null && rawId !== '' ? parseInt(rawId,10) : NaN;
                        const name = (row.status_unit_name||'').toUpperCase();
                        const isNonAsset = stId === 8 || name.includes('NON ASET');
                        const hasNoUnit = !!(row.no_unit && String(row.no_unit).trim() !== '');
                        if(isNonAsset && !hasNoUnit){
                            return `<button class=\"btn btn-sm btn-success\" onclick=\"confirmToAsset(${data})\" title=\"Konfirmasi Jadi Aset (beri No Unit)\"><i class=\"fas fa-check\"></i></button>`;
                        }
                        return '<span class="text-muted small">-</span>';
                    }
                }
            ],
            language:{
                emptyTable:'Tidak ada data tersedia',
                processing:'Memproses...',
                info:'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                infoEmpty:'Menampilkan 0 data',
                paginate:{previous:'Sebelumnya', next:'Berikutnya'}
            },
            dom: 'rtip'
        });

        // Tab status click
        $('#unitStatusTabs .nav-link').on('click', function(){
            $('#unitStatusTabs .nav-link').removeClass('active');
            $(this).addClass('active');
            currentStatusFilter = $(this).data('status');
            updateDynamicTitle();
            unitTable.ajax.reload();
        });
        // Dropdown filters
        $('#filter_departemen').on('change', function(){
            departemenFilter = this.value; updateDynamicTitle(); unitTable.ajax.reload();
        });
        $('#filter_lokasi').on('change', function(){
            lokasiFilter = this.value; updateDynamicTitle(); unitTable.ajax.reload();
        });
        $('#btnResetFilter').on('click', function(){
            currentStatusFilter=''; departemenFilter=''; lokasiFilter='';
            $('#filter_departemen').val(''); $('#filter_lokasi').val('');
            $('#unitStatusTabs .nav-link').removeClass('active');
            $('#unitStatusTabs .nav-link[data-status=""]').addClass('active');
            updateDynamicTitle(); unitTable.ajax.reload();
        });
        $('#btnToggleAdvanced').on('click', function(){ $('#advancedFilters').slideToggle(150); });
        // Custom search debounce
        let searchTimer=null;
        $('#unitSearch').on('keyup paste', function(){
            const val=this.value; clearTimeout(searchTimer); searchTimer=setTimeout(()=>{ unitTable.search(val).draw(); },350);
        });
        $('#btnClearSearch').on('click', function(){ $('#unitSearch').val(''); unitTable.search('').draw(); });
        $('#editUnitForm').on('submit', function(e){
            e.preventDefault();
            const id=$('#edit_id').val();
            $.ajax({
                url:`<?= base_url('warehouse/inventory/update-unit/') ?>${id}`,
                type:'POST',
                data:$(this).serialize()+ '&<?= csrf_token() ?>=<?= csrf_hash() ?>',
                dataType:'json',
                success:function(r){
                    if(r.success){ $('#editUnitModal').modal('hide'); unitTable.ajax.reload(null,false); Swal.fire('Berhasil!', r.message,'success'); }
                    else { Swal.fire('Gagal!', r.message,'error'); }
                },
                error:function(){ Swal.fire('Error!','Tidak dapat terhubung ke server.','error'); }
            });
        });
    });

        // Fungsi konfirmasi unit jadi aset
    function confirmToAsset(id){
        Swal.fire({
            title:'Konfirmasi Jadi Aset',
            html:`<div class='mb-2'>Masukkan No Unit unik untuk aset ini:</div>
                  <input id='swalNoUnit' class='swal2-input' placeholder='Contoh: FL-2025-001' style='width:90%;'>`,
            focusConfirm:false,
            showCancelButton:true,
            confirmButtonText:'Simpan & Konfirmasi',
            preConfirm:()=>{
                const val = document.getElementById('swalNoUnit').value.trim();
                if(!val){ Swal.showValidationMessage('No Unit wajib diisi'); return false; }
                return val;
            }
        }).then(res=>{
            if(!res.isConfirmed) return;
            $.ajax({
                url:`<?= base_url('warehouse/inventory/confirm-to-asset/') ?>${id}`,
                type:'POST',
                data:{'<?= csrf_token() ?>':'<?= csrf_hash() ?>', no_unit: res.value},
                dataType:'json',
                success:function(r){
                    if(r.success){ Swal.fire('Berhasil', r.message,'success'); unitTable.ajax.reload(null,false); }
                    else { Swal.fire('Gagal', r.message,'error'); }
                },
                error:function(xhr){
                    let msg='Tidak dapat terhubung ke server';
                    if(xhr.responseJSON && xhr.responseJSON.message) msg=xhr.responseJSON.message;
                    Swal.fire('Error', msg,'error');
                }
            });
        });
    }

    function updateDynamicTitle(){
        let parts = ['Daftar Stok Unit'];
        if(currentStatusFilter){
            const mapStatus = { '7':'STOCK ASET','3':'RENTAL','9':'JUAL','2':'WORKSHOP-RUSAK' };
            parts.push(mapStatus[currentStatusFilter]||'Status '+currentStatusFilter);
        }
        if(departemenFilter){
            const txt = $('#filter_departemen option:selected').text();
            if(txt) parts.push(txt);
        }
        if(lokasiFilter){
            const txt = $('#filter_lokasi option:selected').text();
            if(txt) parts.push('Lokasi '+txt);
        }
        $('#unitTableTitle').text(parts.join(' - '));
    }

    function viewUnit(id) {
        console.log('viewUnit called for ID:', id);
        
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-unit-full-detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#unitDetailContent').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><br>Memuat detail unit...</div>');
                $('#viewUnitModal').modal('show');
            },
            success: function(response) {
                console.log('AJAX Success Response:', response);
                
                if (response.success) {
                    const data = response.data;
                    const detailHtml = createUnitDetailHtml(data);
                    $('#unitDetailContent').html(detailHtml);
                } else {
                    const errorHtml = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Gagal Memuat Detail</h5>
                            <p>${response.message || 'Terjadi kesalahan tidak diketahui'}</p>
                        </div>
                    `;
                    $('#unitDetailContent').html(errorHtml);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', {xhr, status, error});
                console.log('Response Text:', xhr.responseText);
                
                let errorMessage = 'Terjadi kesalahan saat memuat detail unit.';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    errorMessage += ' (Server Error ' + xhr.status + ')';
                }
                
                const errorHtml = `
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error ${xhr.status}</h5>
                        <p>${errorMessage}</p>
                        <details class="mt-2">
                            <summary>Technical Details</summary>
                            <pre class="mt-2 text-muted small">${xhr.responseText}</pre>
                        </details>
                    </div>
                `;
                $('#unitDetailContent').html(errorHtml);
            }
        });
    }

    function createUnitDetailHtml(data) {
        const h = (str) => {
            if (str === null || str === undefined || str === '') {
                return '-';
            }
            return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;');
        };
        
        console.log('Creating detail HTML for data:', data);
        
        return `
            <div class="row">
                <!-- Basic Unit Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-black">
                            <h6 class="mb-0"><i class="fas fa-truck me-2"></i>Informasi Unit</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>ID Unit</strong></td><td>: ${h(data.id_inventory_unit)}</td></tr>
                                <tr><td><strong>Serial Number</strong></td><td>: ${h(data.serial_number_po)}</td></tr>
                                <tr><td><strong>Merk</strong></td><td>: ${h(data.merk_unit)}</td></tr>
                                <tr><td><strong>Model</strong></td><td>: ${h(data.model_unit)}</td></tr>
                                <tr><td><strong>Jenis Unit</strong></td><td>: ${h(data.nama_departemen)}</td></tr>
                                <tr><td><strong>Tipe Unit</strong></td><td>: ${h(data.nama_tipe_unit)}</td></tr>
                                <tr><td><strong>Tahun</strong></td><td>: ${h(data.tahun_po)}</td></tr>
                                <tr><td><strong>Kapasitas</strong></td><td>: ${h(data.kapasitas_unit)}</td></tr>
                                <tr><td><strong>Status</strong></td><td>: <span class="badge bg-info">${h(data.status_unit_name)}</span></td></tr>
                                <tr><td><strong>Lokasi</strong></td><td>: ${h(data.lokasi_unit)}</td></tr>
                                <tr><td><strong>Tanggal Masuk</strong></td><td>: ${h(data.tanggal_masuk)}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Component Details -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-black">
                            <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Detail Komponen</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>Mast</strong></td><td>: ${h(data.tipe_mast)}</td></tr>
                                <tr><td><strong>SN Mast</strong></td><td>: ${h(data.sn_mast_po)}</td></tr>
                                <tr><td><strong>Mesin</strong></td><td>: ${h(data.merk_mesin + ' ' + data.model_mesin)}</td></tr>
                                <tr><td><strong>SN Mesin</strong></td><td>: ${h(data.sn_mesin_po)}</td></tr>
                                <tr><td><strong>Baterai</strong></td><td>: ${h(data.tipe_baterai)}</td></tr>
                                <tr><td><strong>SN Baterai</strong></td><td>: ${h(data.sn_baterai_po)}</td></tr>
                                <tr><td><strong>Ban</strong></td><td>: ${h(data.tipe_ban)}</td></tr>
                                <tr><td><strong>Roda</strong></td><td>: ${h(data.tipe_roda)}</td></tr>
                                <tr><td><strong>Valve</strong></td><td>: ${h(data.jumlah_valve)}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Purchase Order Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Informasi PO</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>No. PO</strong></td><td>: ${h(data.no_po)}</td></tr>
                                <tr><td><strong>Tanggal PO</strong></td><td>: ${h(data.tanggal_po)}</td></tr>
                                <tr><td><strong>Supplier</strong></td><td>: ${h(data.nama_supplier)}</td></tr>
                                <tr><td><strong>Status PO</strong></td><td>: <span class="badge bg-secondary">${h(data.status_po)}</span></td></tr>
                                <tr><td><strong>Kondisi Jual</strong></td><td>: ${h(data.status_penjualan)}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Verification Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-black">
                            <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Informasi Verifikasi</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>Status Verifikasi</strong></td><td>: <span class="badge ${data.status_verifikasi === 'Sesuai' ? 'bg-success' : data.status_verifikasi === 'Tidak Sesuai' ? 'bg-danger' : 'bg-secondary'}">${h(data.status_verifikasi)}</span></td></tr>
                                <tr><td><strong>Catatan Verifikasi</strong></td><td>: ${h(data.catatan_verifikasi) || '-'}</td></tr>
                            </table>
                            ${data.keterangan ? `
                            <hr>
                            <h6><strong>Keterangan:</strong></h6>
                            <p class="text-muted">${h(data.keterangan)}</p>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function editUnit(id) {
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-unit-detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#edit_id').val(data.id_inventory_unit);
                    $('#edit_serial_number').val(data.serial_number_po);
                    $('#edit_merk').val(data.merk_unit);
                    $('#edit_status_unit').val(data.status_unit);
                    $('#edit_lokasi').val(data.lokasi_unit);
                    $('#editUnitModal').modal('show');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            }
        });
    }

    function deleteUnit(id) {
        Swal.fire({
            title: 'Hapus Unit?',
            html: 'Tindakan ini <b>tidak dapat dibatalkan</b>.<br>Pastikan unit tidak memiliki transaksi aktif.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((res) => {
            if (!res.isConfirmed) return;
            $.ajax({
                url: `<?= base_url('warehouse/inventory/delete-unit/') ?>${id}`,
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(r){
                    if(r.success){
                        Swal.fire('Berhasil', r.message || 'Unit dihapus', 'success');
                        unitTable.ajax.reload(null,false);
                    } else {
                        Swal.fire('Gagal', r.message || 'Unit tidak dapat dihapus', 'error');
                    }
                },
                error: function(xhr){
                    let msg = 'Server tidak merespon';
                    if(xhr.responseJSON && xhr.responseJSON.message){
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    }
</script>
<?= $this->endSection() ?>
